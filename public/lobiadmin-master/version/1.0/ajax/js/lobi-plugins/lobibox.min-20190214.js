var Lobibox=Lobibox||{};!function(){function o(o,t){this.$input=null,this.$type="prompt",this.$promptType=o,t=$.extend({},Lobibox.prompt.DEFAULT_OPTIONS,t),this.$options=this._processInput(t),this._init(),this.debug(this)}function t(o){this.$type="confirm",this.$options=this._processInput(o),this._init(),this.debug(this)}function s(o,t){this.$type=o,this.$options=this._processInput(t),this._init(),this.debug(this)}function n(o){this.$type="progress",this.$progressBarElement=null,this.$options=this._processInput(o),this.$progress=0,this._init(),this.debug(this)}function i(o,t){this.$type=o,this.$options=this._processInput(t),this._init(),this.debug(this)}Lobibox.prompt=function(t,s){return new o(t,s)},Lobibox.confirm=function(o){return new t(o)},Lobibox.progress=function(o){return new n(o)},Lobibox.error={},Lobibox.success={},Lobibox.warning={},Lobibox.info={},Lobibox.alert=function(o,t){return["success","error","warning","info"].indexOf(o)>-1?new s(o,t):void 0},Lobibox.window=function(o){return new i("window",o)};var e={$type:null,$el:null,$options:null,debug:function(){this.$options.debug&&window.console.debug.apply(window.console,arguments)},_processInput:function(o){if($.isArray(o.buttons)){for(var t={},s=0;s<o.buttons.length;s++){var n=Lobibox.base.OPTIONS.buttons[o.buttons[s]];t[o.buttons[s]]=n}o.buttons=t}o.customBtnClass=o.customBtnClass?o.customBtnClass:Lobibox.base.DEFAULTS.customBtnClass;for(var s in o.buttons){var n=o.buttons[s];o.buttons.hasOwnProperty(s)&&(n=$.extend({},Lobibox.base.OPTIONS.buttons[s],n),n["class"]||(n["class"]=o.customBtnClass)),o.buttons[s]=n}return o=$.extend({},Lobibox.base.DEFAULTS,o),void 0===o.showClass&&(o.showClass=Lobibox.base.OPTIONS.showClass),void 0===o.hideClass&&(o.hideClass=Lobibox.base.OPTIONS.hideClass),void 0===o.baseClass&&(o.baseClass=Lobibox.base.OPTIONS.baseClass),void 0===o.delayToRemove&&(o.delayToRemove=Lobibox.base.OPTIONS.delayToRemove),o},_init:function(){var o=this;o._createMarkup(),o.setTitle(o.$options.title),o.$options.draggable&&!o._isMobileScreen()&&(o.$el.addClass("draggable"),o._enableDrag()),o.$options.closeButton&&o._addCloseButton(),o.$options.closeOnEsc&&$(document).on("keyup.lobibox",function(t){27===t.which&&o.destroy()}),o.$options.baseClass&&o.$el.addClass(o.$options.baseClass),o.$options.showClass&&(o.$el.removeClass(o.$options.hideClass),o.$el.addClass(o.$options.showClass)),o.$el.data("lobibox",o)},_calculatePosition:function(o){var t,s=this;t="top"===o?30:"bottom"===o?$(window).outerHeight()-s.$el.outerHeight()-30:($(window).outerHeight()-s.$el.outerHeight())/2;var n=($(window).outerWidth()-s.$el.outerWidth())/2;return{left:n,top:t}},_createButton:function(o,t){var s=this,n=$("<button></button>").addClass(t["class"]).attr("data-type",o).html(t.text);return s.$options.callback&&"function"==typeof s.$options.callback&&n.on("click.lobibox",function(t){var n=$(this);s.$options.buttons[o]&&s.$options.buttons[o].closeOnClick&&s.destroy(),s.$options.callback(s,n.data("type"),t)}),n.click(function(){s.$options.buttons[o]&&s.$options.buttons[o].closeOnClick&&s.destroy()}),n},_generateButtons:function(){var o=this,t=[];for(var s in o.$options.buttons)if(o.$options.buttons.hasOwnProperty(s)){var n=o.$options.buttons[s],i=o._createButton(s,n);t.push(i)}return t},_createMarkup:function(){var o=this,t=$('<div class="lobibox"></div>');t.attr("data-is-modal",o.$options.modal);var s=$('<div class="lobibox-header"></div>').append('<span class="lobibox-title"></span>'),n=$('<div class="lobibox-body"></div>');if(t.append(s),t.append(n),o.$options.buttons&&!$.isEmptyObject(o.$options.buttons)){var i=$('<div class="lobibox-footer"></div>');i.append(o._generateButtons()),t.append(i),Lobibox.base.OPTIONS.buttonsAlign.indexOf(o.$options.buttonsAlign)>-1&&i.addClass("text-"+o.$options.buttonsAlign)}o.$el=t.addClass(Lobibox.base.OPTIONS.modalClasses[o.$type])},_setSize:function(){var o=this;o.setWidth(o.$options.width),o.setHeight("auto"===o.$options.height?o.$el.outerHeight():o.$options.height)},_calculateBodyHeight:function(o){var t=this,s=t.$el.find(".lobibox-header").outerHeight(),n=t.$el.find(".lobibox-footer").outerHeight();return o-(s?s:0)-(n?n:0)},_addBackdrop:function(){0===$(".lobibox-backdrop").length&&$("body").append('<div class="lobibox-backdrop"></div>')},_triggerEvent:function(o){var t=this;t.$options[o]&&"function"==typeof t.$options[o]&&t.$options[o](t)},_calculateWidth:function(o){var t=this;return o=Math.min($(window).outerWidth(),o),o===$(window).outerWidth()&&(o-=2*t.$options.horizontalOffset),o},_calculateHeight:function(o){return Math.min($(window).outerHeight(),o)},_addCloseButton:function(){var o=this,t=$('<span class="btn-close">&times;</span>');o.$el.find(".lobibox-header").append(t),t.on("mousedown",function(o){o.stopPropagation()}),t.on("click.lobibox",function(){o.destroy()})},_position:function(){var o=this;o._setSize();var t=o._calculatePosition();o.setPosition(t.left,t.top)},_isMobileScreen:function(){return $(window).outerWidth()<768?!0:!1},_enableDrag:function(){var o=this.$el,t=o.find(".lobibox-header");t.on("mousedown.lobibox",function(t){o.attr("offset-left",t.offsetX),o.attr("offset-top",t.offsetY),o.attr("allow-drag","true")}),$(document).on("mouseup.lobibox",function(){o.attr("allow-drag","false")}),$(document).on("mousemove.lobibox",function(t){if("true"===o.attr("allow-drag")){var s=t.clientX-parseInt(o.attr("offset-left"),10)-parseInt(o.css("border-left-width"),10),n=t.clientY-parseInt(o.attr("offset-top"),10)-parseInt(o.css("border-top-width"),10);o.css({left:s,top:n})}})},_setContent:function(o){var t=this;return t.$el.find(".lobibox-body").html(o),t},hide:function(){function o(){t.$el.addClass("lobibox-hidden"),0===$(".lobibox[data-is-modal=true]:not(.lobibox-hidden)").length&&($(".lobibox-backdrop").remove(),$("body").removeClass(Lobibox.base.OPTIONS.bodyClass))}var t=this;return t.$options.hideClass?(t.$el.removeClass(t.$options.showClass),t.$el.addClass(t.$options.hideClass),setTimeout(function(){o()},t.$options.delayToRemove)):o(),this},destroy:function(){function o(){t.$el.remove(),0===$(".lobibox[data-is-modal=true]").length&&($(".lobibox-backdrop").remove(),$("body").removeClass(Lobibox.base.OPTIONS.bodyClass)),t._triggerEvent("closed")}var t=this;return t._triggerEvent("beforeClose"),t.$options.hideClass?(t.$el.removeClass(t.$options.showClass),t.$el.addClass(t.$options.hideClass),setTimeout(function(){o()},t.$options.delayToRemove)):o(),this},setWidth:function(o){return o=this._calculateWidth(o),this.$el.css("width",o),this},setHeight:function(o){var t=this;o=t._calculateHeight(o),t.$el.css("height",o);var s=t._calculateBodyHeight(t.$el.innerHeight());return t.$el.find(".lobibox-body").css("height",s),t},setSize:function(o,t){var s=this;return s.setWidth(o),s.setHeight(t),s},setPosition:function(o,t){var s,n=this;return"number"==typeof o&&"number"==typeof t?s={left:o,top:t}:"string"==typeof o&&(s=n._calculatePosition(o)),n.$el.css(s),n},setTitle:function(o){var t=this;return t.$el.find(".lobibox-title").html(o),t},getTitle:function(){var o=this;return o.$el.find(".lobibox-title").html()},show:function(){var o=this;return o._triggerEvent("onShow"),o.$el.removeClass("lobibox-hidden"),$("body").append(o.$el),o.$options.modal&&($("body").addClass(Lobibox.base.OPTIONS.bodyClass),o._addBackdrop()),o._triggerEvent("shown"),o}};Lobibox.base={},Lobibox.base.OPTIONS={bodyClass:"lobibox-open",modalClasses:{error:"lobibox-error",success:"lobibox-success",info:"lobibox-info",warning:"lobibox-warning",confirm:"lobibox-confirm",progress:"lobibox-progress",prompt:"lobibox-prompt","default":"lobibox-default",window:"lobibox-window"},buttonsAlign:["left","center","right"],buttons:{ok:{"class":"lobibox-btn lobibox-btn-default",text:"OK",closeOnClick:!0},cancel:{"class":"lobibox-btn lobibox-btn-cancel",text:"Cancel",closeOnClick:!0},yes:{"class":"lobibox-btn lobibox-btn-yes",text:"Yes",closeOnClick:!0},no:{"class":"lobibox-btn lobibox-btn-no",text:"No",closeOnClick:!0}}},Lobibox.base.DEFAULTS={horizontalOffset:5,width:600,height:"auto",closeButton:!0,draggable:!1,customBtnClass:"lobibox-btn lobibox-btn-default",modal:!0,debug:!1,buttonsAlign:"center",closeOnEsc:!0,delayToRemove:200,baseClass:"animated-super-fast",showClass:"zoomIn",hideClass:"zoomOut",onShow:null,shown:null,beforeClose:null,closed:null},o.prototype=$.extend({},e,{constructor:o,_processInput:function(t){var s=this,n=e._processInput.call(s,t);return n.buttons={ok:Lobibox.base.OPTIONS.buttons.ok,cancel:Lobibox.base.OPTIONS.buttons.cancel},t=$.extend({},n,o.DEFAULT_OPTIONS,t)},_init:function(){var o=this;e._init.call(o),o.show(),o._setContent(o._createInput()),o._position(),o.$input.focus()},_createInput:function(){var o,t=this;t.$options.multiline?(t.$input=$("<textarea></textarea>"),t.$input.attr("rows",t.$options.lines)):t.$input=$('<input type="'+t.$promptType+'"/>'),t.$input.addClass("lobibox-input"),t.$input.attr(t.$options.attrs),t.$options.value&&t.setValue(t.$options.value),t.$options.label&&(o=$("<label>"+t.$options.label+"</label>"));var s=$("<div></div>").append(o,t.$input);return s},setValue:function(o){return this.$input.val(o),this},getValue:function(){return this.$input.val()}}),o.DEFAULT_OPTIONS={width:400,attrs:{},value:"",multiline:!1,lines:3,type:"text",label:""},t.prototype=$.extend({},e,{constructor:t,_processInput:function(o){var t=this,s=e._processInput.call(t,o);return s.buttons={yes:Lobibox.base.OPTIONS.buttons.yes,no:Lobibox.base.OPTIONS.buttons.no},o=$.extend({},s,Lobibox.confirm.DEFAULTS,o)},_init:function(){var o=this;e._init.call(o),o.show();var t=$("<div></div>");o.$options.iconClass&&t.append($('<div class="lobibox-icon-wrapper"></div>').append('<i class="lobibox-icon '+o.$options.iconClass+'"></i>')),t.append('<div class="lobibox-body-text-wrapper"><span class="lobibox-body-text">'+o.$options.msg+"</span></div>"),o._setContent(t.html()),o._position()}}),Lobibox.confirm.DEFAULTS={title:"Question",width:500,iconClass:"glyphicon glyphicon-question-sign"},s.prototype=$.extend({},e,{constructor:s,_processInput:function(o){var t=this,s=e._processInput.call(t,o);return s.buttons={ok:Lobibox.base.OPTIONS.buttons.ok},o=$.extend({},s,Lobibox.alert.OPTIONS[t.$type],Lobibox.alert.DEFAULTS,o)},_init:function(){var o=this;e._init.call(o),o.show();var t=$("<div></div>");o.$options.iconClass&&t.append($('<div class="lobibox-icon-wrapper"></div>').append('<i class="lobibox-icon '+o.$options.iconClass+'"></i>')),t.append('<div class="lobibox-body-text-wrapper"><span class="lobibox-body-text">'+o.$options.msg+"</span></div>"),o._setContent(t.html()),o._position()}}),Lobibox.alert.OPTIONS={warning:{title:"Warning",iconClass:"glyphicon glyphicon-question-sign"},info:{title:"Information",iconClass:"glyphicon glyphicon-info-sign"},success:{title:"Success",iconClass:"glyphicon glyphicon-ok-sign"},error:{title:"Error",iconClass:"glyphicon glyphicon-remove-sign"}},Lobibox.alert.DEFAULTS={},n.prototype=$.extend({},e,{constructor:n,_processInput:function(o){var t=this,s=e._processInput.call(t,o);return o=$.extend({},s,Lobibox.progress.DEFAULTS,o)},_init:function(){var o=this;e._init.call(o),o.show(),o.$progressBarElement=o.$options.progressTpl?$(o.$options.progressTpl):o._createProgressbar();var t;o.$options.label&&(t=$("<label>"+o.$options.label+"</label>"));var s=$("<div></div>").append(t,o.$progressBarElement);o._setContent(s),o._position()},_createProgressbar:function(){var o=this,t=$('<div class="lobibox-progress-bar-wrapper lobibox-progress-outer"></div>').append('<div class="lobibox-progress-bar lobibox-progress-element"></div>');return o.$options.showProgressLabel&&t.append('<span class="lobibox-progress-text" data-role="progress-text"></span>'),t},setProgress:function(o){var t=this;if(100!==t.$progress)return o=Math.min(100,Math.max(0,o)),t.$progress=o,t._triggerEvent("progressUpdated"),100===t.$progress&&t._triggerEvent("progressCompleted"),t.$el.find(".lobibox-progress-element").css("width",o.toFixed(1)+"%"),t.$el.find('[data-role="progress-text"]').html(o.toFixed(1)+"%"),t},getProgress:function(){return this.$progress}}),Lobibox.progress.DEFAULTS={width:500,showProgressLabel:!0,label:"",progressTpl:!1,progressUpdated:null,progressCompleted:null},i.prototype=$.extend({},e,{constructor:i,_processInput:function(o){var t=this,s=e._processInput.call(t,o);return o.content&&"function"==typeof o.content&&(o.content=o.content()),o.content instanceof jQuery&&(o.content=o.content.clone()),o=$.extend({},s,Lobibox.window.DEFAULTS,o)},_init:function(){var o=this;e._init.call(o),o.setContent(o.$options.content),o.$options.url&&o.$options.autoload?(o.$options.showAfterLoad||(o.show(),o._position()),o.load(function(){o.$options.showAfterLoad&&(o.show(),o._position())})):(o.show(),o._position())},setParams:function(o){var t=this;return t.$options.params=o,t},getParams:function(){var o=this;return o.$options.params},setLoadMethod:function(o){var t=this;return t.$options.loadMethod=o,t},getLoadMethod:function(){var o=this;return o.$options.loadMethod},setContent:function(o){var t=this;return t.$options.content=o,t.$el.find(".lobibox-body").html("").append(o),t},getContent:function(){var o=this;return o.$options.content},setUrl:function(o){return this.$options.url=o,this},getUrl:function(){return this.$options.url},load:function(o){var t=this;return t.$options.url?($.ajax(t.$options.url,{method:t.$options.loadMethod,data:t.$options.params}).done(function(s){t.setContent(s),o&&"function"==typeof o&&o(s)}),t):t}}),Lobibox.window.DEFAULTS={width:480,height:600,content:"",url:"",draggable:!0,autoload:!0,loadMethod:"GET",showAfterLoad:!0,params:{}}}();
Math.randomString=function(i){for(var o="",n="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",t=0;i>t;t++)o+=n.charAt(Math.floor(Math.random()*n.length));return o};var Lobibox=Lobibox||{};!function(){var i=function(i,o){this.$type,this.$options,this.$el,this.$sound;var n=this,t=function(i){return("mini"===i.size||"large"===i.size)&&(i.width=i.width||Lobibox.notify.OPTIONS[i.size].width),i=$.extend({},Lobibox.notify.OPTIONS[n.$type],Lobibox.notify.DEFAULTS,i),"mini"!==i.size&&i.title===!0?i.title=Lobibox.notify.OPTIONS[n.$type].title:"mini"===i.size&&i.title===!0&&(i.title=!1),i.icon===!0&&(i.icon=Lobibox.notify.OPTIONS[n.$type].icon),i.sound===!0&&(i.sound=Lobibox.notify.OPTIONS[n.$type].sound),i.sound&&(i.sound=Lobibox.notify.OPTIONS.soundPath+i.sound),i},s=function(){var i=r(),o=d();if(a(i,o),n.$el=i,n.$options.sound){var t=new Audio(n.$options.sound);t.play()}n.$el.data("lobibox",n)},a=function(i,o){if("normal"===n.$options.size)o.append(i);else if("mini"===n.$options.size)i.addClass("notify-mini"),o.append(i);else if("large"===n.$options.size){var t=l();t.append(i);var s=e(t.attr("id"));o.find(".tab-content").append(t),o.find(".nav-tabs").append(s),s.find(">a").tab("show")}},e=function(i){var o=$("<li></li>");return $('<a href="#'+i+'"></a>').attr("data-toggle","tab").attr("role","tab").append('<i class="tab-control-icon '+n.$options.icon+'"></i>').appendTo(o),o.addClass(Lobibox.notify.OPTIONS[n.$type]["class"]),o},l=function(){var i=$("<div></div>").addClass("tab-pane").attr("id",Math.randomString(10));return i},d=function(){var i;i="large"===n.$options.size?".lobibox-notify-wrapper-large":".lobibox-notify-wrapper";var o=n.$options.position.split(" ");i+="."+o.join(".");var t=$(i);return 0===t.length&&(t=$("<div></div>").addClass(i.replace(/\./g," ").trim()).appendTo($("body")),"large"===n.$options.size&&t.append($('<ul class="nav nav-tabs"></ul>')).append($('<div class="tab-content"></div>'))),t},r=function(){var i=$('<div class="lobibox-notify"></div>').addClass(Lobibox.notify.OPTIONS[n.$type]["class"]).addClass(Lobibox.notify.OPTIONS["class"]).addClass(n.$options.showClass),o=$('<div class="lobibox-notify-icon"></div>').appendTo(i);if(n.$options.img){var t=o.append('<img src="'+n.$options.img+'"/>');o.append(t)}else if(n.$options.icon){var s=o.append('<i class="'+n.$options.icon+'"></i>');o.append(s)}else i.addClass("without-icon");var a=$("<div></div>").addClass("lobibox-notify-body").append('<div class="lobibox-notify-msg">'+n.$options.msg+"</div>").appendTo(i);return n.$options.title&&a.prepend('<div class="lobibox-notify-title">'+n.$options.title+"<div>"),p(i),("normal"===n.$options.size||"mini"===n.$options.size)&&(c(i),b(i)),n.$options.width&&i.css("width",u(n.$options.width)),i},p=function(i){if(n.$options.closable){var o=$('<span class="lobibox-close">&times;</span>');i.append(o),o.click(function(){n.remove()})}},c=function(i){n.$options.closeOnClick&&i.click(function(){n.remove()})},b=function(i){if(n.$options.delay){if(n.$options.delayIndicator){var o=$('<div class="lobibox-delay-indicator"><div></div></div>');i.append(o)}var t=0,s=1e3/30,a=setInterval(function(){t+=s;var i=100*t/n.$options.delay;i>=100&&(i=100,n.remove(),a=clearInterval(a)),n.$options.delayIndicator&&o.find("div").css("width",i+"%")},s)}},f=function(i){var o=i.prev();return 0===o.length&&(o=i.next()),0===o.length?null:o.find(">a")},u=function(i){return i=Math.min($(window).outerWidth(),i)};this.remove=function(){n.$el.removeClass(n.$options.showClass).addClass(n.$options.hideClass);var i=n.$el.parent(),o=i.closest(".lobibox-notify-wrapper-large"),t="#"+i.attr("id"),s=o.find('>.nav-tabs>li:has(a[href="'+t+'"])');return s.addClass(Lobibox.notify.OPTIONS["class"]).addClass(n.$options.hideClass),setTimeout(function(){if("normal"===n.$options.size||"mini"===n.$options.size)n.$el.remove();else if("large"===n.$options.size){var o=f(s);o&&o.tab("show"),s.remove(),i.remove()}},500),n},this.$type=i,this.$options=t(o),s()};Lobibox.notify=function(o,n){return["info","warning","error","success"].indexOf(o)>-1?new i(o,n):void 0},Lobibox.notify.DEFAULTS={title:!0,size:"normal",showClass:"flipInX",hideClass:"zoomOutDown",icon:!0,msg:"",img:null,closable:!0,delay:5e3,delayIndicator:!0,closeOnClick:!0,width:400,sound:!0,position:"bottom right"},Lobibox.notify.OPTIONS={"class":"animated-fast",soundPath:"src/sounds/",large:{width:500},mini:{"class":"notify-mini"},success:{"class":"lobibox-notify-success",title:"Success",icon:"glyphicon glyphicon-ok-sign",sound:"sound2.mp3"},error:{"class":"lobibox-notify-error",title:"Error",icon:"glyphicon glyphicon-remove-sign",sound:"sound4.mp3"},warning:{"class":"lobibox-notify-warning",title:"Warning",icon:"glyphicon glyphicon-exclamation-sign",sound:"sound5.mp3"},info:{"class":"lobibox-notify-info",title:"Information",icon:"glyphicon glyphicon-info-sign",sound:"sound6.mp3"}}}();