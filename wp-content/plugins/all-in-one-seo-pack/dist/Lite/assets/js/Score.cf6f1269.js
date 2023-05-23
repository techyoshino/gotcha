import{n as s}from"./_plugin-vue2_normalizer.d86aa1f3.js";const a={props:{header:String,description:String,isAnalyzing:Boolean,analyzeTime:{type:Number,default(){return 8}},placeholder:{type:String,default(){return""}},inputError:{type:Boolean,default(){return!1}}},data(){return{input:null,strings:{analyze:this.$t.__("Analyze",this.$td)}}},watch:{isAnalyzing(r){r||(this.input=null)}}};var i=function(){var n=this,e=n._self._c;return e("div",{staticClass:"analyze-main"},[e("div",{staticClass:"analyze-header",domProps:{innerHTML:n._s(n.header)}}),e("div",{staticClass:"analyze-description",domProps:{innerHTML:n._s(n.description)}}),e("div",{staticClass:"analyze-input"},[e("base-input",{class:{"aioseo-error":n.inputError},attrs:{placeholder:n.placeholder},on:{keyup:function(t){return!t.type.indexOf("key")&&n._k(t.keyCode,"enter",13,t.key,"Enter")?null:n.$emit("startAnalyzing",n.input)}},model:{value:n.input,callback:function(t){n.input=t},expression:"input"}}),e("base-button",{attrs:{type:"green",loading:n.isAnalyzing,disabled:!n.input},on:{click:function(t){return n.$emit("startAnalyzing",n.input)}}},[n._v(" "+n._s(n.strings.analyze)+" ")])],1),n._t("errors"),n.isAnalyzing?e("div",{staticClass:"analyze-progress"},[e("div",{staticClass:"analyze-progress-value",style:{animationDuration:`${n.analyzeTime}s`}})]):n._e()],2)},l=[],o=s(a,i,l,!1,null,null,null,null);const d=o.exports;const u={props:{score:{type:Number,required:!0}},computed:{getColorClass(){return 33>=this.score?"red":66>=this.score?"orange":"green"}}};var c=function(){var n=this,e=n._self._c;return e("div",{staticClass:"aioseo-analyze-score",class:n.getColorClass},[e("span",[n._v(n._s(n.score)+"/100")])])},_=[],p=s(u,c,_,!1,null,null,null,null);const f=p.exports;export{d as C,f as a};
