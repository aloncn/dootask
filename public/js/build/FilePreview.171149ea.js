import{_ as o}from"./openpgp_hi.15f91b1d.js";import{I as n}from"./IFrame.78489951.js";import{n as a}from"./app.befdac39.js";import"./@micro-zoe.c2e1472d.js";import"./jquery.5afcbe3d.js";import"./@traptitech.b6e72224.js";import"./katex.9792db63.js";import"./localforage.25a6b490.js";import"./markdown-it.28fd7f10.js";import"./entities.797c3e49.js";import"./uc.micro.39573202.js";import"./mdurl.2f66c031.js";import"./linkify-it.3ecfda1e.js";import"./punycode.b7e94e71.js";import"./highlight.js.24fdca15.js";import"./markdown-it-link-attributes.e1d5d151.js";import"./vue.ea2bb532.js";import"./vuex.cc7cb26e.js";import"./axios.6ec123f8.js";import"./le5le-store.b40f9152.js";import"./vue-router.2d566cd7.js";import"./vue-clipboard2.f62d09e0.js";import"./clipboard.13c9d2be.js";import"./view-design-hi.3da58854.js";import"./default-passive-events.a3d698c9.js";import"./vuedraggable.4bb621b8.js";import"./sortablejs.88918bd7.js";import"./vue-resize-observer.eb9dc3d4.js";import"./element-sea.faaf089e.js";import"./deepmerge.cecf392e.js";import"./resize-observer-polyfill.88ee0e7b.js";import"./throttle-debounce.7c3948b2.js";import"./babel-helper-vue-jsx-merge-props.5ed215c3.js";import"./normalize-wheel.2a034b9f.js";import"./async-validator.45b57f96.js";import"./babel-runtime.4773988a.js";import"./core-js.314b4a1d.js";var l=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"file-preview"},[t.isPreview?e("IFrame",{staticClass:"preview-iframe",attrs:{src:t.previewUrl},on:{"on-load":t.onFrameLoad}}):t.contentDetail?[e("div",{directives:[{name:"show",rawName:"v-show",value:t.headerShow&&!["word","excel","ppt"].includes(t.file.type),expression:"headerShow && !['word', 'excel', 'ppt'].includes(file.type)"}],staticClass:"edit-header"},[e("div",{staticClass:"header-title"},[e("div",{staticClass:"title-name"},[t._v(t._s(t.$A.getFileName(t.file)))]),e("Tag",{attrs:{color:"default"}},[t._v(t._s(t.$L("\u53EA\u8BFB")))]),e("div",{staticClass:"refresh"},[t.contentLoad?e("Loading"):e("Icon",{attrs:{type:"ios-refresh"},on:{click:t.getContent}})],1)],1)]),e("div",{staticClass:"content-body"},[t.file.type=="document"?[t.contentDetail.type=="md"?e("MDPreview",{attrs:{initialValue:t.contentDetail.content}}):e("TEditor",{attrs:{value:t.contentDetail.content,height:"100%",readOnly:""}})]:t.file.type=="drawio"?e("Drawio",{ref:"myFlow",attrs:{value:t.contentDetail,title:t.file.name,readOnly:""}}):t.file.type=="mind"?e("Minder",{ref:"myMind",attrs:{value:t.contentDetail,readOnly:""}}):["code","txt"].includes(t.file.type)?e("AceEditor",{attrs:{value:t.contentDetail.content,ext:t.file.ext,readOnly:""}}):["word","excel","ppt"].includes(t.file.type)?e("OnlyOffice",{attrs:{value:t.contentDetail,code:t.code,historyId:t.historyId,documentKey:t.documentKey,readOnly:""}}):t._e()],2)]:t._e(),t.contentLoad?e("div",{staticClass:"content-load"},[e("Loading")],1):t._e()],2)},s=[];const d=()=>o(()=>import("./preview.5a16ca29.js"),["js/build/preview.5a16ca29.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/vuex.cc7cb26e.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),c=()=>o(()=>import("./TEditor.c8054391.js"),["js/build/TEditor.c8054391.js","js/build/tinymce.1901e32c.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/ImgUpload.fc92d105.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/vuex.cc7cb26e.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),p=()=>o(()=>import("./AceEditor.b8f0d00b.js"),["js/build/AceEditor.b8f0d00b.js","js/build/vuex.cc7cb26e.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),m=()=>o(()=>import("./OnlyOffice.fe926521.js"),["js/build/OnlyOffice.fe926521.js","js/build/OnlyOffice.5570973b.css","js/build/vuex.cc7cb26e.js","js/build/IFrame.78489951.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),u=()=>o(()=>import("./Drawio.98b681f8.js"),["js/build/Drawio.98b681f8.js","js/build/Drawio.6a04e353.css","js/build/vuex.cc7cb26e.js","js/build/IFrame.78489951.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),_=()=>o(()=>import("./Minder.f5d16741.js"),["js/build/Minder.f5d16741.js","js/build/Minder.1839e1ef.css","js/build/IFrame.78489951.js","js/build/app.befdac39.js","js/build/app.0eb40c5c.css","js/build/@micro-zoe.c2e1472d.js","js/build/jquery.5afcbe3d.js","js/build/@traptitech.b6e72224.js","js/build/katex.9792db63.js","js/build/localforage.25a6b490.js","js/build/markdown-it.28fd7f10.js","js/build/entities.797c3e49.js","js/build/uc.micro.39573202.js","js/build/mdurl.2f66c031.js","js/build/linkify-it.3ecfda1e.js","js/build/punycode.b7e94e71.js","js/build/highlight.js.24fdca15.js","js/build/markdown-it-link-attributes.e1d5d151.js","js/build/vue.ea2bb532.js","js/build/vuex.cc7cb26e.js","js/build/axios.6ec123f8.js","js/build/le5le-store.b40f9152.js","js/build/openpgp_hi.15f91b1d.js","js/build/vue-router.2d566cd7.js","js/build/vue-clipboard2.f62d09e0.js","js/build/clipboard.13c9d2be.js","js/build/view-design-hi.3da58854.js","js/build/default-passive-events.a3d698c9.js","js/build/vuedraggable.4bb621b8.js","js/build/sortablejs.88918bd7.js","js/build/vue-resize-observer.eb9dc3d4.js","js/build/element-sea.faaf089e.js","js/build/deepmerge.cecf392e.js","js/build/resize-observer-polyfill.88ee0e7b.js","js/build/throttle-debounce.7c3948b2.js","js/build/babel-helper-vue-jsx-merge-props.5ed215c3.js","js/build/normalize-wheel.2a034b9f.js","js/build/async-validator.45b57f96.js","js/build/babel-runtime.4773988a.js","js/build/core-js.314b4a1d.js"]),h={name:"FilePreview",components:{IFrame:n,AceEditor:p,TEditor:c,MDPreview:d,OnlyOffice:m,Drawio:u,Minder:_},props:{code:{type:String,default:""},historyId:{type:Number,default:0},file:{type:Object,default:()=>({})},headerShow:{type:Boolean,default:!0}},data(){return{loadContent:0,contentDetail:null,loadPreview:!0}},watch:{"file.id":{handler(t){t&&(this.contentDetail=null,this.getContent())},immediate:!0,deep:!0}},computed:{contentLoad(){return this.loadContent>0||this.previewLoad},isPreview(){return this.contentDetail&&this.contentDetail.preview===!0},previewLoad(){return this.isPreview&&this.loadPreview===!0},previewUrl(){if(this.isPreview){const{name:t,key:i}=this.contentDetail;return $A.onlinePreviewUrl(t,i)}return""}},methods:{onFrameLoad(){this.loadPreview=!1},getContent(){if(["word","excel","ppt"].includes(this.file.type)){this.contentDetail=$A.cloneJSON(this.file);return}setTimeout(t=>{this.loadContent++},600),this.$store.dispatch("call",{url:"file/content",data:{id:this.code||this.file.id,history_id:this.historyId}}).then(({data:t})=>{this.contentDetail=t.content}).catch(({msg:t})=>{$A.modalError(t)}).finally(t=>{this.loadContent--})},documentKey(){return new Promise((t,i)=>{this.$store.dispatch("call",{url:"file/content",data:{id:this.code||this.file.id,only_update_at:"yes"}}).then(({data:e})=>{t(`${e.id}-${$A.Time(e.update_at)}`)}).catch(e=>{i(e)})})},exportMenu(t){switch(this.file.type){case"mind":this.$refs.myMind.exportHandle(t,this.file.name);break}}}},r={};var v=a(h,l,s,!1,f,null,null,null);function f(t){for(let i in r)this[i]=r[i]}var et=function(){return v.exports}();export{et as default};
