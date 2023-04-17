<?php

namespace App\Http\Controllers\Api;

use Cache;
use Request;
use Carbon\Carbon;
use App\Models\User;
use App\Module\Base;
use App\Module\Ihttp;
use App\Models\WebSocketDialog;
use App\Models\WorkflowProcMsg;
use App\Exceptions\ApiException;
use App\Models\WebSocketDialogMsg;

/**
 * @apiDefine workflow
 *
 * 工作流
 */
class WorkflowController extends AbstractController
{
    private $flow_url = '';
    public function __construct()
    {
        $this->flow_url = env('FLOW_URL') ?: 'http://dootask-workflow-'.env('APP_ID');
    }

    /**
     * @api {get} api/workflow/verifyToken          01. 验证APi登录
     *
     * @apiVersion 1.0.0
     * @apiGroup users
     * @apiName verifyToken
     *
     * @apiSuccess {String} version
     * @apiSuccess {String} publish
     */
    public function verifyToken()
    {
        try {
            $user = User::auth();
            $user->checkAdmin();
            return Base::retSuccess('成功');
        } catch (\Throwable $th) {
            return response('身份无效', 400)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * @api {post} api/workflow/procdef/all          02. 查询流程定义
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName procdef__all
     *
     * @apiQuery {String} name               流程名称
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function procdef__all()
    {
        User::auth('admin');
        $data['name'] = Request::input('name');
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/procdef/findAll', json_encode($data));
        $procdef = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$procdef || $procdef['status'] != 200 || $ret['ret'] == 0) {
            return Base::retError($procdef['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($procdef['data']));
    }

    /**
     * @api {get} api/workflow/procdef/del          03. 删除流程定义
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName procdef__del
     *
     * @apiQuery {String} id               流程ID
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function procdef__del()
    {
        User::auth('admin');
        $data['id'] = Request::input('id');
        $ret = Ihttp::ihttp_get($this->flow_url.'/api/v1/workflow/procdef/delById?'.http_build_query($data));
        $procdef = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$procdef || $procdef['status'] != 200) {
            return Base::retError($procdef['message'] ?? '删除失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($procdef['data']));
    }

    /**
     * @api {post} api/workflow/process/start          04. 启动流程（审批中）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName process__start
     *
     * @apiQuery {String} proc_name            流程名称
     * @apiQuery {Number} department_id        部门ID
     * @apiQuery {Array} [var]                 启动流程类型信息（格式：[{type,startTime,endTime,description}]）
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function process__start()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['department_id'] = intval(Request::input('department_id'));
        $data['proc_name'] = Request::input('proc_name');
        //
        $var = json_decode(Request::input('var'), true);
        $data['var'] = $var;
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/process/start', json_encode(Base::arrayKeyToCamel($data)));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '启动失败');
        }
        //
        $process = Base::arrayKeyToUnderline($process['data']);
        $process = $this->getProcessById($process['id']); //获取最新的流程信息
        if ($process['candidate']) {
            $userid = explode(',', $process['candidate']);
            $toUser = User::whereIn('userid', $userid)->get()->toArray();
            $botUser = User::botGetOrCreate('approval-alert');
            if (empty($botUser)) {
                return Base::retError('审批机器人不存在');
            }
            foreach ($toUser as $val) {
                if ($val['bot']) {
                    continue;
                }
                $dialog = WebSocketDialog::checkUserDialog($botUser, $val['userid']);
                if (empty($dialog)) {
                    continue;
                }
                $this->workflowMsg('workflow_reviewer', $dialog, $botUser, $val, $process, 'start');
            }
            // 抄送人
            $notifier = $this->handleProcessNode($process);
            if ($notifier) {
                foreach ($notifier as $val) {
                    $dialog = WebSocketDialog::checkUserDialog($botUser, $val['target_id']);
                    $this->workflowMsg('workflow_notifier', $dialog, $botUser, $process, $process);
                }
            }
        }

        return Base::retSuccess('success', $process);
    }

    /**
     * @api {post} api/workflow/task/complete          05. 审批
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName task__complete
     *
     * @apiQuery {Number} task_id               流程ID
     * @apiQuery {String} pass                  标题 [true-通过，false-拒绝]
     * @apiQuery {String} comment               评论
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function task__complete()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['task_id'] = intval(Request::input('task_id'));
        $data['pass'] = Request::input('pass');
        $data['comment'] = Request::input('comment');
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/task/complete', json_encode(Base::arrayKeyToCamel($data)));
        $task = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$task || $task['status'] != 200) {
            return Base::retError($task['message'] ?? '审批失败');
        }
        //
        $task = Base::arrayKeyToUnderline($task['data']);
        $pass = $data['pass'] == 'true' ? 'pass' : 'refuse';
        $process = $this->getProcessById($task['proc_inst_id']);
        $botUser = User::botGetOrCreate('approval-alert');
        if (empty($botUser)) {
            return Base::retError('审批机器人不存在');
        }
        // 在流程信息关联的用户中查找
        $toUser = WorkflowProcMsg::where('proc_inst_id', $process['id'])->get()->toArray();
        foreach ($toUser as $val) {
            $dialog = WebSocketDialog::checkUserDialog($botUser, $val['userid']);
            if (empty($dialog)) {
                continue;
            }
            $this->workflowMsg('workflow_reviewer', $dialog, $botUser, $val, $process, $pass);
        }
        // 发起人
        if($process['is_finished'] == true) {
            $dialog = WebSocketDialog::checkUserDialog($botUser, $process['start_user_id']);
            $this->workflowMsg('workflow_submitter', $dialog, $botUser, ['userid' => $data['userid']], $process, $pass);
        }
        // 抄送人
        $notifier = $this->handleProcessNode($process, $task['step']);
        if ($notifier && $pass == 'pass') {
            foreach ($notifier as $val) {
                $dialog = WebSocketDialog::checkUserDialog($botUser, $val['target_id']);
                $this->workflowMsg('workflow_notifier', $dialog, $botUser, $process, $process);
            }
        }
        return Base::retSuccess('success', $task);
    }

    /**
     * @api {post} api/workflow/task/withdraw          06. 撤回
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName task__withdraw
     *
     * @apiQuery {Number} task_id               流程ID
     * @apiQuery {Number} proc_inst_id          流程实例ID
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function task__withdraw()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['task_id'] = intval(Request::input('task_id'));
        $data['proc_inst_id'] = intval(Request::input('proc_inst_id'));
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/task/withdraw', json_encode(Base::arrayKeyToCamel($data)));
        $task = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$task || $task['status'] != 200) {
            return Base::retError($task['message'] ?? '撤回失败');
        }
        //
        $process = $this->getProcessById($data['proc_inst_id']);
        $botUser = User::botGetOrCreate('approval-alert');
        if (empty($botUser)) {
            return Base::retError('审批机器人不存在');
        }
        // 在流程信息关联的用户中查找
        $toUser = WorkflowProcMsg::where('proc_inst_id', $process['id'])->get()->toArray();
        foreach ($toUser as $val) {
            $dialog = WebSocketDialog::checkUserDialog($botUser, $val['userid']);
            if (empty($dialog)) {
                continue;
            }
            //发送撤回提醒
            $this->workflowMsg('workflow_reviewer', $dialog, $botUser, $val, $process, 'withdraw');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($task['data']));
    }

    /**
     * @api {post} api/workflow/process/findTask          07. 查询需要我审批的流程（审批中）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName process__findTask
     *
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function process__findTask()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/process/findTask', json_encode(Base::arrayKeyToCamel($data)));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {post} api/workflow/process/startByMyself          08. 查询我启动的流程（审批中）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName process__startByMyself
     *
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function process__startByMyself()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));
        info($data);
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/process/startByMyself', json_encode($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {post} api/workflow/process/findProcNotify          09. 查询抄送我的流程（审批中）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName process__findProcNotify
     *
     * @apiQuery {Number} userid               用户ID
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function process__findProcNotify()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));

        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/process/findProcNotify', json_encode($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {get} api/workflow/identitylink/findParticipant          10. 查询流程实例的参与者（审批中）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName identitylink__findParticipant
     *
     * @apiQuery {Number} proc_inst_id             流程实例ID
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function identitylink__findParticipant()
    {
        User::auth();
        $proc_inst_id = Request::input('proc_inst_id');
        $ret = Ihttp::ihttp_get($this->flow_url.'/api/v1/workflow/identitylink/findParticipant?procInstId=' . $proc_inst_id);
        $identitylink = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$identitylink || $identitylink['status'] != 200) {
            return Base::retError($identitylink['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($identitylink['data']));
    }

    /**
     * @api {post} api/workflow/procHistory/findTask          11. 查询需要我审批的流程（已结束）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName procHistory__findTask
     *
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function procHistory__findTask()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/procHistory/findTask', json_encode(Base::arrayKeyToCamel($data)));
        info($ret);
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {post} api/workflow/procHistory/startByMyself          12. 查询我启动的流程（已结束）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName procHistory__startByMyself
     *
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function procHistory__startByMyself()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));
        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/procHistory/startByMyself', json_encode($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {post} api/workflow/procHistory/findProcNotify          13. 查询抄送我的流程（已结束）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName procHistory__findProcNotify
     *
     * @apiQuery {Number} page                  页码
     * @apiQuery {Number} page_size             每页条数
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function procHistory__findProcNotify()
    {
        $user = User::auth();
        $data['userid'] = (string)$user->userid;
        $data['pageIndex'] = intval(Request::input('page'));
        $data['pageSize'] = intval(Request::input('page_size'));

        $ret = Ihttp::ihttp_post($this->flow_url.'/api/v1/workflow/procHistory/findProcNotify', json_encode($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            return Base::retError($process['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($process['data']));
    }

    /**
     * @api {get} api/workflow/identitylinkHistory/findParticipant          14. 查询流程实例的参与者（已结束）
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName identitylinkHistory__findParticipant
     *
     * @apiQuery {Number} proc_inst_id             流程实例ID
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function identitylinkHistory__findParticipant()
    {
        User::auth();
        $proc_inst_id = Request::input('proc_inst_id');
        $ret = Ihttp::ihttp_get($this->flow_url.'/api/v1/workflow/identitylinkHistory/findParticipant?procInstId=' . $proc_inst_id);
        $identitylink = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$identitylink || $identitylink['status'] != 200) {
            return Base::retError($identitylink['message'] ?? '查询失败');
        }
        return Base::retSuccess('success', Base::arrayKeyToUnderline($identitylink['data']));
    }

    /**
     * @api {get} api/workflow/process/detail          15. 根据流程ID查询流程详情
     *
     * @apiDescription 需要token身份
     * @apiVersion 1.0.0
     * @apiGroup workflow
     * @apiName process__detail
     *
     * @apiQuery {Number} id               流程ID
     *
     * @apiSuccess {Number} ret     返回状态码（1正确、0错误）
     * @apiSuccess {String} msg     返回信息（错误描述）
     * @apiSuccess {Object} data    返回数据
     */
    public function process__detail()
    {
        User::auth();
        $data['id'] = intval(Request::input('id'));
        $workflow = $this->getProcessById($data['id']);
        return Base::retSuccess('success', $workflow);
    }

    // 审批机器人消息-审核人
    public function workflowMsg($type, $dialog, $botUser, $toUser, $process, $action = null)
    {
        $data = [
            'nickname' => User::userid2nickname($type == 'workflow_submitter' ? $toUser['userid'] : $process['start_user_id']),
            'proc_def_name' => $process['proc_def_name'],
            'department' => $process['department'],
            'type' => $process['var']['type'],
            'start_time' => $process['var']['start_time'],
            'end_time' => $process['var']['end_time'],
        ];
        $text = view('push.bot', ['type' => $type, 'action' => $action, 'data' => (object)$data])->render();
        $text = preg_replace("/^\x20+/", "", $text);
        $text = preg_replace("/\n\x20+/", "\n", $text);
        $msg_action = null;
        if ($action == 'withdraw' || $action == 'pass' || $action == 'refuse') {
            // 如果任务没有完成，则不需要更新消息
            if ($process['is_finished'] != true) {
                return true;
            }
            // 任务完成，给发起人发送消息
            if($type == 'workflow_submitter' && $action != 'withdraw'){
                return WebSocketDialogMsg::sendMsg($msg_action, $dialog->id, 'text', ['text' => $text], $botUser->userid, false, false, true);
            }
            // 查找最后一条消息msg_id
            $msg_action = 'update-'.$toUser['msg_id'];
        }
        $msg = WebSocketDialogMsg::sendMsg($msg_action, $dialog->id, 'text', ['text' => $text], $botUser->userid, false, false, true);
        // 关联信息
        if ($action == 'start') {
            $proc_msg = new WorkflowProcMsg();
            $proc_msg->proc_inst_id = $process['id'];
            $proc_msg->msg_id = $msg['data']->id;
            $proc_msg->userid = $toUser['userid'];
            $proc_msg->save();
        }
        return true;
    }

    // 根据ID获取流程
    public function getProcessById($id)
    {
        $data['id'] = intval($id);
        $ret = Ihttp::ihttp_get($this->flow_url."/api/v1/workflow/process/findById?".http_build_query($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            throw new ApiException($process['message'] ?? '查询失败');
        }
        return Base::arrayKeyToUnderline($process['data']);
    }

    // 处理流程节点返回是否有抄送人
    public function handleProcessNode($process, $step = 0)
    {
        // 获取流程节点
        $process_node = $process['node_infos'];
        //判断下一步是否有抄送人
        $step = $step + 1;
        $next_node = $process_node[$step] ?? [];
        if ($next_node) {
           if ($next_node['type'] == 'notifier'){
               return $next_node['node_user_list'] ?? [];
           }
        }
        return [];
    }

    // 根据ID查询流程实例的参与者（审批中）
    public function getUserProcessParticipantById($id)
    {
        $data['id'] = intval($id);
        $ret = Ihttp::ihttp_get($this->flow_url."/api/v1/workflow/identitylink/findParticipant?".http_build_query($data));
        $process = json_decode($ret['ret'] == 1 ? $ret['data'] : '{}', true);
        if (!$process || $process['status'] != 200) {
            throw new ApiException($process['message'] ?? '查询失败');
        }
        return $process;
    }

}
