import{m as d,n as f,a as l}from"./app.139edc4e.js";import{I as c}from"./IFrame.fe0c152e.js";var h=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"component-only-office"},[e.$A.isDesktop()?[e.loadError?i("Alert",{staticClass:"load-error",attrs:{type:"error","show-icon":""}},[e._v(e._s(e.$L("\u7EC4\u4EF6\u52A0\u8F7D\u5931\u8D25\uFF01")))]):e._e(),i("div",{staticClass:"placeholder",attrs:{id:e.id}})]:i("IFrame",{staticClass:"preview-iframe",attrs:{src:e.previewUrl},on:{"on-load":e.onFrameLoad}}),e.loading?i("div",{staticClass:"office-loading"},[i("Loading")],1):e._e()],2)},u=[];const m={name:"OnlyOffice",components:{IFrame:c},props:{id:{type:String,default:()=>"office_"+Math.round(Math.random()*1e4)},code:{type:String,default:""},historyId:{type:Number,default:0},value:{type:[Object,Array],default:function(){return{}}},readOnly:{type:Boolean,default:!1},documentKey:Function},data(){return{loading:!1,loadError:!1,docEditor:null}},beforeDestroy(){this.docEditor!==null&&(this.docEditor.destroyEditor(),this.docEditor=null)},computed:{...d(["userInfo","themeIsDark"]),fileType(){return this.getType(this.value.type)},fileName(){return this.value.name},fileUrl(){let e=this.code||this.value.id,t;return $A.leftExists(e,"msgFile_")?t=`dialog/msg/download/?msg_id=${$A.leftDelete(e,"msgFile_")}&token=${this.userToken}`:$A.leftExists(e,"taskFile_")?t=`project/task/filedown/?file_id=${$A.leftDelete(e,"taskFile_")}&token=${this.userToken}`:(t=`file/content/?id=${e}&token=${this.userToken}`,this.historyId>0&&(t+=`&history_id=${this.historyId}`)),t},previewUrl(){return $A.apiUrl(this.fileUrl)+"&down=preview"}},watch:{"value.id":{handler(e){!e||!$A.isDesktop()||(this.loading=!0,this.loadError=!1,$A.loadScript($A.apiUrl("../office/web-apps/apps/api/documents/api.js")).then(t=>{if(!this.documentKey){this.handleClose();return}const i=this.documentKey();i&&i.then?i.then(this.loadFile):this.loadFile()}).catch(t=>{this.loadError=!0}).finally(t=>{this.loading=!1}))},immediate:!0},previewUrl:{handler(){$A.isDesktop()||(this.loading=!0)},immediate:!0}},methods:{onFrameLoad(){this.loading=!1},getType(e){switch(e){case"word":return"docx";case"excel":return"xlsx";case"ppt":return"pptx"}return e},loadFile(e=""){this.docEditor!==null&&(this.docEditor.destroyEditor(),this.docEditor=null);let t=l;switch(l){case"zh-CHT":t="zh-TW";break}let i=this.code||this.value.id,a=$A.strExists(this.fileName,".")?this.fileName:this.fileName+"."+this.fileType,s=`${this.fileType}-${e||i}`;this.historyId>0&&(s+=`-${this.historyId}`);const r={document:{fileType:this.fileType,title:a,key:s,url:`http://nginx/api/${this.fileUrl}`},editorConfig:{mode:"edit",lang:t,user:{id:String(this.userInfo.userid),name:this.userInfo.nickname},customization:{uiTheme:this.themeIsDark?"theme-dark":"theme-classic-light",forcesave:!0,help:!1},callbackUrl:`http://nginx/api/file/content/office?id=${i}&token=${this.userToken}`},events:{onDocumentReady:this.onDocumentReady}};/\/hideenOfficeTitle\//.test(window.navigator.userAgent)&&(r.document.title=" "),(async y=>{if((this.readOnly||this.historyId>0)&&(r.editorConfig.mode="view",r.editorConfig.callbackUrl=null,!r.editorConfig.user.id)){let o=await $A.IDBInt("officeViewer");o||(o=$A.randNum(1e3,99999),await $A.IDBSet("officeViewer",o)),r.editorConfig.user.id="viewer_"+o,r.editorConfig.user.name="Viewer_"+o}this.$nextTick(()=>{this.docEditor=new DocsAPI.DocEditor(this.id,r)})})()},onDocumentReady(){this.$emit("on-document-ready",this.docEditor)}}},n={};var p=f(m,h,u,!1,_,"38b2d892",null,null);function _(e){for(let t in n)this[t]=n[t]}var $=function(){return p.exports}();export{$ as default};
