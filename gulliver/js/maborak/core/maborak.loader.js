
leimnud.Package.Public({info:{Class:"maborak",File:"module.panel.js",Name:"panel",Type:"module",Version:"1.0.5"},content:function(options)
{this.uid=this.parent.tools.createUID();this.zIndex=0;this.stepZindex=5;this.controlSize={w:15,h:15};this.elements={};this.setStyle={};this.events={};this.tab={};this.showing={};this.make=function()
{this.makeTmpDB();this.options={statusBar:false,titleBar:true,cursorToDrag:"default",elementToDrag:"title",strict_position:true}.concat(this.options||{});this.options.fx={blinkToFront:true,shadow:true,opacity:true,modal:false,fadeIn:false,fadeOut:false,drag:true}.concat(this.options.fx||{});this.options.control={resize:false,close:true,drag:true}.concat(this.options.control||{});this.options.statusBar=(this.options.statusBarButtons||this.options.control.resize)?true:this.options.statusBar;this.options.size={w:200,h:200}.concat(this.options.size||{});this.options.position={x:20,y:20}.concat(this.options.position||{});this.makeTheme();this.elements.containerWindow=$dce("div");this.elements.containerWindow.className="panel_containerWindow___"+this.getTheme("containerWindow");this.styles.containerWindow();if(this.options.fx.fadeIn===true)
{this.parent.dom.opacity(this.elements.containerWindow,0);}
this.target().appendChild(this.elements.containerWindow);this.elements.frontend=$dce("div");this.elements.frontend.className="panel_frontend___"+this.getTheme("frontend");this.styles.frontend();this.elements.containerWindow.appendChild(this.elements.frontend);this.elements.titleBar=$dce("div");this.elements.titleBar.className="panel_titleBar___"+this.getTheme("titleBar");this.parent.exec(this.styles.titleBar,false,false,this);this.elements.frontend.appendChild(this.elements.titleBar);this.elements.title=$dce("div");this.elements.title.className="panel_title___"+this.getTheme("title");this.parent.exec(this.styles.title,false,false,this);this.elements.title.innerHTML=this.options.title||"";this.elements.titleBar.appendChild(this.elements.title);this.elements.headerBar=$dce("div");this.elements.headerBar.className="panel_headerBar___"+this.getTheme("headerBar");this.styles.headerBar();this.elements.frontend.appendChild(this.elements.headerBar);this.elements.tab=$dce("div");this.elements.tab.className="panel_tab___"+this.getTheme("tab");this.elements.content=$dce("div");this.elements.content.className="panel_content___"+this.getTheme("content");this.elements.loader=$dce("div");this.elements.loader.className="panel_loader___"+this.getTheme("loader");this.elements.frontend.appendChild(this.elements.content);this.elements.frontend.appendChild(this.elements.tab);this.elements.frontend.appendChild(this.elements.loader);this.elements.statusBar=$dce("div");this.elements.statusBar.className="panel_statusBar___"+this.getTheme("statusBar");this.styles.statusBar();this.elements.frontend.appendChild(this.elements.statusBar);this.elements.statusButtons=$dce("div");this.elements.statusButtons.className="panel_statusButtons___"+this.getTheme("statusButtons");this.styles.statusButtons();this.elements.statusBar.appendChild(this.elements.statusButtons);this.elements.status=$dce("div");this.elements.status.innerHTML="&nbsp;";this.elements.status.className="panel_status___"+this.getTheme("status");this.parent.exec(this.styles.status,false,false,this);this.elements.statusBar.appendChild(this.elements.status);this.elements.resize=$dce("div");this.elements.resize.className="panel_resize___"+this.getTheme("resize");this.styles.resize();this.elements.statusBar.appendChild(this.elements.resize);this.makeStatusButtons();this.parent.exec(this.styles.loader,false,false,this);this.parent.exec(this.styles.tab,false,false,this);this.parent.exec(this.styles.content,false,false,this);this.elements.backend=$dce("div");this.elements.backend.className="panel_backend___"+this.getTheme("backend");this.parent.exec(this.styles.backend,false,false,this);this.elements.containerWindow.appendChild(this.elements.backend);if(this.parent.browser.isIE)
{this.elements.iframe=$dce("iframe");this.elements.iframe.className="panel_iframe___"+this.getTheme("iframe");this.elements.iframe.frameBorder="no";this.elements.iframe.scrolling="no";this.elements.iframe.src="about:blank";this.parent.exec(this.styles.iframe,false,false,this);this.elements.backend.appendChild(this.elements.iframe);}
this.makeEvents();this.makeFx();this.makeControls();if(this.options.fx.fadeIn===true)
{new this.parent.module.fx.fade().make({duration:1000,end:this.styles.fx.opacityPanel.Static/100,dom:this.elements.containerWindow});}};this.makeFx=function()
{if(this.options.fx.shadow)
{this.elements.shadow=$dce("div");this.elements.shadow.className="panel_shadow___"+this.getTheme("shadow");this.parent.exec(this.styles.shadow,false,false,this);if(this.options.fx.fadeIn===true)
{this.parent.dom.opacity(this.elements.shadow,0);new this.parent.module.fx.fade().make({duration:1000,end:this.styles.fx.opacityShadow.Static/100,dom:this.elements.shadow});}
this.target().appendChild(this.elements.shadow);}
if(this.options.fx.modal)
{this.elements.modal=$dce("div");this.elements.modal.className="panel_modal___"+this.getTheme("modal");this.elements.modal.id="panel_modal___"+this.getTheme("modal");if(this.options.fx.fadeIn===true)
{this.parent.dom.opacity(this.elements.modal,0);}
this.styles.modal();this.target().appendChild(this.elements.modal);}
if(this.options.fx.blinkToFront===true)
{this.events.init.push(this.blink);this.elements.containerWindow.onmousedown=this.blink;}
if(this.options.fx.opacity)
{this.events.init.push(this.fx.setOpacity);this.events.finish.push(this.fx.unsetOpacity);}
if(this.options.fx.rolled)
{this.roll();}};this.makeStatusButtons=function()
{if(this.options.statusBarButtons)
{this.parent.dom.setStyle(this.elements.statusBar,{});var t=this.options.statusBarButtons;this.elements.statusBarButtons=[];for(var i=0;i<t.length;i++)
{var b=new button(t[i].value||"Button");this.elements.statusBarButtons.push(b);this.elements.statusButtons.appendChild(b);}}};this.blink=function(){if(this.zIndex<this.parent.tmp.panel.zIndex)
{this.zIndex=this.makezIndex();this.parent.dom.setStyle(this.elements.containerWindow,{zIndex:this.zIndex});if(this.options.fx.shadow)
{this.shadowReIndex();}}};this.move=function(opt)
{opt={fx:true,x:this.options.position.x,y:this.options.position.y}.concat(opt);this.options.position.x=opt.x;this.options.position.y=opt.y;if(opt.fx===true)
{new this.parent.module.fx.move().make({duration:500,end:opt,dom:this.elements.containerWindow,onFinish:opt.onFinish||function(){}});if(this.options.fx.shadow)
{new this.parent.module.fx.move().make({duration:500,end:{x:opt.x+2,y:opt.y+2},dom:this.elements.shadow});}}
else
{this.parent.dom.setStyle(this.elements.containerWindow,{left:opt.x,top:opt.y});if(this.options.fx.shadow)
{this.parent.dom.setStyle(this.elements.shadow,{top:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2),left:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"left"),10)+2)});}}};this.maximize=function()
{this.move({x:0,y:0,fx:true,onFinish:function()
{new this.parent.module.fx.algorithm().make({duration:1000,begin:this.options.size.w,transition:"sineInOut",end:this.target().offsetWidth,onTransition:function(fx){this.resize({w:fx.result});}.extend(this),onFinish:function(fx)
{this.resize({w:fx.options.end});new this.parent.module.fx.algorithm().make({duration:1000,begin:this.options.size.h,transition:"sineInOut",end:this.target().clientHeight,onTransition:function(fx){this.resize({h:fx.result});}.extend(this),onFinish:function(fx)
{this.resize({h:fx.options.end});}.extend(this)});}.extend(this)});}.extend(this)});};this.resize=function(opt)
{opt=opt||{};this.options.size={w:opt.w||this.options.size.w,h:opt.h||this.options.size.h};this.parent.dom.setStyle(this.elements.containerWindow,{width:this.options.size.w,height:this.options.size.h});this.styles.content();if(this.options.fx.shadow)
{this.styles.shadow();}
if(this.tab.display==="horizontal")
{this.parent.dom.setStyle(this.elements.tab,{width:this.elements.containerWindow.clientWidth-8});}};this.center=function(inR)
{inR=inR||false;var center={x:(((this.target().clientWidth/2)+this.target().scrollLeft)-(this.options.size.w/2)),y:(((this.target().clientHeight/2)+this.target().scrollTop)-(this.options.size.h/2))};if(inR==="x"||inR==="y")
{this.options.position.x=((inR==="x")?center.x:(this.options.position.x||0));this.options.position.y=((inR==="y")?center.y:(this.options.position.y||0));}
else
{this.options.position.x=center.x;this.options.position.y=center.y;this.options.position.x=this.options.position.x||0;this.options.position.y=this.options.position.y||0;}
this.options.position.x=(this.options.position.x<0)?0:this.options.position.x;this.options.position.y=(this.options.position.y<0)?0:this.options.position.y;this.move({x:this.options.position.x,y:this.options.position.y});};this.fixContent=function(rcr)
{var v1=this.elements.content.scrollHeight;var v2=this.elements.content.clientHeight;var diff=parseInt(v1-v2);var originalSize=this.options.size;var originalContentSize=this.originalContent;return;if(diff>0)
{this.resize({h:this.options.size.h+diff});this.options.size=originalSize;this.originalContent=originalContentSize;}
else if(this.elements.content.clientHeight>originalContentSize.h)
{this.resize({h:this.options.size.h});if(rcr!==true)
{}}};this.makeEvents=function()
{this.events.init=(this.events.init)?((this.events.init.isArray)?this.events.init:[this.events.init]):[];this.events.move=(this.events.move)?((this.events.move.isArray)?this.events.move:[this.events.move]):[];this.events.finish=(this.events.finish)?((this.events.finish.isArray)?this.events.finish:[this.events.finish]):[];};this.makeControls=function()
{this.controls=[];if(this.options.control.close)
{this.elements.close=$dce("div");this.elements.close.className="panel_close___"+this.getTheme("close");this.parent.exec(this.styles.close,false,false,this);this.controls.push(this.elements.close);this.elements.titleBar.appendChild(this.elements.close);}
if(this.options.control.roll)
{this.elements.roll=$dce("div");this.elements.roll.className="panel_roll___"+this.getTheme("roll");this.styles.roll();this.controls.push(this.elements.roll);this.elements.titleBar.appendChild(this.elements.roll);this.elements.title.ondblclick=this.roll;}
if(this.options.control.setup)
{this.elements.setup=$dce("div");this.elements.setup.className="panel_roll___"+this.getTheme("roll");this.styles.setup();this.controls.push(this.elements.setup);this.elements.titleBar.appendChild(this.elements.setup);}
if(this.options.control.drag)
{var etd=this.elements[this.options.elementToDrag];this.parent.dom.setStyle(this.elements.title,{cursor:this.options.cursorToDrag});this.drag=new this.parent.module.drag({link:{elements:[etd],ref:((this.options.fx.shadow===true)?[this.elements.containerWindow,this.elements.shadow]:[this.elements.containerWindow])},limit:this.options.limit||false});this.drag.events={init:this.events.init,move:this.events.move,finish:this.events.finish.concat(function(pan){pan.options.position.x=parseInt(pan.elements.containerWindow.style.left,10);pan.options.position.y=parseInt(pan.elements.containerWindow.style.top,10);}.extend(this.drag,this))};this.drag.cursor=this.options.cursorToDrag;this.drag.make();}
if(this.options.control.resize)
{this.parent.dom.setStyle(this.elements.resize,{cursor:"nw-resize"});this.resizeDrag=new this.parent.module.drag({link:{elements:[this.elements.resize],ref:[]},noCursorMove:true});this.resizeDrag.cursor="nw-resize";this.resizeDrag.events={init:function(panel)
{this.panelBeginSize=panel.options.size;}.extend(this.resizeDrag,this),move:function(panel){var np={x:this.currentCursorPosition.x-this.cursorStart.x,y:this.currentCursorPosition.y-this.cursorStart.y};panel.resize({w:this.panelBeginSize.w+np.x,h:this.panelBeginSize.h+np.y});}.extend(this.resizeDrag,this)};this.resizeDrag.make();}
else
{this.parent.dom.setStyle(this.elements.resize,{background:"transparent"});}};this.makeTab=function(dynamic)
{if(this.loading===true){return false;}
var thm=this.tab.display==="vertical"?"":"H";var tb=this.elements.tabOptions[this.tabSelected];tb.className="panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected");tb.onmouseover=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tb,b:"panel_tabOptionSelectedOver"+thm+"___"+this.getTheme("tabOptionSelectedOver")});tb.onmouseout=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tb,b:"panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected")});var tc=(typeof this.tab.options[this.tabSelected].content);if(!dynamic)
{if(this.tab.display==="vertical")
{var hj=(parseInt(this.parent.dom.getStyle(tb,"width"),10)-((!this.parent.browser.isIE)?3:0));this.parent.dom.setStyle(tb,{width:hj,borderLeftWidth:4});}
else
{this.parent.dom.setStyle(tb,{});}}
tb.onmouseup=function(){return false;};if(this.tabLastSelected!==false)
{var tls=this.elements.tabOptions[this.tabLastSelected];tls.className="panel_tabOption"+thm+"___"+this.getTheme("tabOption");tls.onmouseover=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tls,b:"panel_tabOptionOver"+thm+"___"+this.getTheme("tabOptionOver")});tls.onmouseout=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tls,b:"panel_tabOption"+thm+"___"+this.getTheme("tabOption")});tls.onmouseup=function(event,tabID){if(this.tab.manualDisabled){return false;}
this.tabSelected=(this.parent.browser.isIE)?event:tabID;this.makeTab();return false;}.extend(this,this.tabLastSelected);if(this.tab.display==="vertical")
{this.parent.dom.setStyle(tls,{width:parseInt(this.parent.dom.getStyle(tb,"width"),10)+((!this.parent.browser.isIE)?3:0),borderLeftWidth:1});}
else
{this.parent.dom.setStyle(tls,{});}
this.parent.dom.setStyle(tls,this.setStyle.tabOption||{});}
if(typeof(this.flag)!="undefined"){delete this.flag;return true;}
this.parent.dom.setStyle(tb,this.setStyle.tabOptionSelected||{});if(!this.tab.options[this.tabSelected].noClear)
{this.clearContent();}
this.addContent(this.tab.options[this.tabSelected].content);this.tabLastSelected=this.tabSelected;return true;};this.selectTab=function(tab)
{if(tab>=this.elements.tabOptions.length){return false;}
if(this.tabSelected===tab){this.tabLastSelected=false;}
this.tabSelected=tab;this.makeTab((this.tabLastSelected===false)?true:false);return true;};this.shadowReIndex=function()
{this.parent.dom.setStyle(this.elements.shadow,{zIndex:this.zIndex-2});};this.reIndexElements=function()
{};this.controlPosition=function()
{var cl=this.controls.length+1;return((3*cl)+(this.controlSize.w*this.controls.length));};this.makeTmpDB=function()
{if(!this.parent.tmp.panel)
{this.parent.tmp.panel={};this.parent.tmp.panel.zIndex=100;}};this.makezIndex=function()
{this.parent.tmp.panel.zIndex+=this.stepZindex;return this.parent.tmp.panel.zIndex;};this.target=function()
{return(this.options.target)?this.options.target:this.parent.dom.capture("tag.body 0");};this.spaceOutPanel=function()
{var brdr={x:(parseInt(this.parent.dom.getStyle(this.elements.content,"marginLeft")||0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"marginRight")||0,10)),y:(parseInt(this.parent.dom.getStyle(this.elements.content,"marginTop")||0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"marginBottom")||0,10))};var pddn={x:(parseInt(this.parent.dom.getStyle(this.elements.content,"paddingLeft")||0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"paddingRight")||0,10)),y:(parseInt(this.parent.dom.getStyle(this.elements.content,"paddingTop")||0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"paddingBottom")||0,10))};var bbb={x:(parseInt(this.parent.dom.getStyle(this.elements.content,"borderLeftWidth")||1,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"borderRightWidth")||1,10)),y:(parseInt(this.parent.dom.getStyle(this.elements.content,"borderTopWidth")||1,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"borderBottomWidth")||1,10))};return{x:brdr.x+pddn.x+bbb.x,y:brdr.y+pddn.y+bbb.y};};this.roll=function()
{if(this.rolling){return false;}
if(this.inroll===true)
{this.rolling=true;this.inroll=false;this.parent.dom.setStyle(this.elements.containerWindow,{overflow:"hidden",width:this.lastWidth});this.parent.dom.setStyle([this.elements.content,this.elements.statusBar],{display:"block"});new this.parent.module.fx.algorithm().make({transition:"sineOut",duration:1000,begin:this.elements.containerWindow.offsetHeight,end:this.options.size.h,onTransition:function(fx){this.parent.dom.setStyle([this.elements.containerWindow],{height:fx.result});if(this.options.fx.shadow)
{this.styles.shadow();}}.extend(this),onFinish:function(fx){this.parent.dom.setStyle([this.elements.containerWindow],{height:this.options.size.h});if(this.options.fx.shadow)
{this.styles.shadow();}
this.parent.dom.setStyle(this.elements.frontend,{width:"auto"});this.rolling=false;return(this.events.roll||function(){})();}.extend(this)});}
else
{this.rolling=true;this.inroll=true;this.lastWidth=this.options.size.w||this.elements.containerWindow.offsetWidth;this.parent.dom.setStyle(this.elements.containerWindow,{overflow:"hidden",width:this.options.fx.rollWidth||this.lastWidth});new this.parent.module.fx.algorithm().make({transition:"sineOut",duration:1000,begin:this.elements.containerWindow.offsetHeight,end:this.elements.titleBar.offsetHeight,onTransition:function(fx){this.parent.dom.setStyle([this.elements.containerWindow],{height:fx.result});if(this.options.fx.shadow)
{this.styles.shadow();}}.extend(this),onFinish:function(fx){this.parent.dom.setStyle([this.elements.containerWindow],{height:this.elements.titleBar.clientHeight});this.parent.dom.setStyle([this.elements.content,this.elements.statusBar],{display:"none"});if(this.options.fx.shadow)
{this.styles.shadow();}
this.parent.dom.setStyle(this.elements.frontend,{width:"100%"});this.rolling=false;return(this.events.roll||function(){})();}.extend(this)});}};this.remove=function()
{if(this.inRemove===true){return false;}else{this.inRemove=true;}
var e=[];if(this.options.fx.fadeOut===true)
{e.push(this.elements.containerWindow);}
if(this.options.fx.shadow)
{e.push(this.elements.shadow);}
if(this.options.fx.modal)
{e.push(this.elements.modal);}
if(this.events.remove)
{this.events.remove=(this.events.remove.isArray)?this.events.remove:[this.events.remove];for(var i=0;i<this.events.remove.length;i++)
{if(typeof this.events.remove[i]=='function')
{this.events.remove[i]();}}}
if(this.cancelClose===true){return false;}
if(this.options.fx.fadeOut===true)
{new this.parent.module.fx.fade().make({duration:500,end:0,dom:e,onFinish:function(){if(this.drag){this.drag.flush();}
for(var i in this.elements)
{if(this.elements.propertyIsEnumerable(i))
{this.parent.dom.remove(this.elements[i]);delete this.elements[i];}}}.extend(this)});}
else
{if(this.drag){this.drag.flush();}
for(var i in this.elements)
{if(this.elements.propertyIsEnumerable(i))
{this.parent.dom.remove(this.elements[i]);delete this.elements[i];}}}
return false;};this.addContent=function(content)
{var Rt=false;if(typeof content=="string")
{this.elements.content.innerHTML+=content;var Rt=true;}
else if(typeof content=="object")
{this.elements.content.appendChild(content);var Rt=true;}
else if(typeof content=="function")
{content();}
return Rt;};this.open=function(options)
{options={proxy:false}.concat(options||{});this.clearContent();if(options.proxy===false)
{if(options.url)
{var rpc=new this.parent.module.rpc.xmlhttp({url:options.url,method:"GET"});this.loader.show();rpc.callback=function(rpc)
{this.loader.hide();var content=rpc.xmlhttp.responseText;var scripts=content.extractScript();this.addContent(content);scripts.evalScript();var forms=this.elements.content.getElementsByTagName('form');for(var i=0;i<forms.length;i++)
{var sub=new leimnud.module.app.submit({form:forms[i]});sub.callback=function(){};}}.extend(this);rpc.make();}
else if(options.html)
{this.addContent(options.html);}
else if(options.image)
{this.addContent("<div style='text-align:center;'><img src=\""+options.image+"\" /></div>");}}
else
{this.addContent(new DOM('iframe',{src:options.url},{border:"0px solid red",height:"100%",width:"100%"}));}
return true;};this.clearContent=function()
{this.elements.content.innerHTML="";return true;};this.addContentTitle=function(content)
{if(typeof content=="string")
{this.elements.title.innerHTML=content;return true;}
else if(typeof content=="object")
{this.elements.title.appendChild(content);return true;}
return false;},this.addContentStatus=function(content)
{if(typeof content=="string")
{this.elements.status.innerHTML+=content;}
else if(typeof content=="object")
{this.elements.status.appendChild(content);}
if(!this.showing.status){this.status.show();}
return true;};this.clearContentStatus=function()
{this.elements.status.innerHTML="";return true;};this.fx={setOpacity:function()
{this.parent.dom.setStyle(this.elements.containerWindow,{opacity:this.styles.fx.opacityPanel.Move/100,filter:"alpha(opacity="+this.styles.fx.opacityPanel.Move+")"});if(this.options.fx.shadow===true){this.parent.dom.setStyle(this.elements.shadow,{opacity:this.styles.fx.opacityShadow.Move/100,filter:"alpha(opacity="+this.styles.fx.opacityShadow.Move+")"});}},unsetOpacity:function()
{this.parent.dom.setStyle(this.elements.containerWindow,{opacity:this.styles.fx.opacityPanel.Static/100,filter:"alpha(opacity="+this.styles.fx.opacityPanel.Static+")"});if(this.options.fx.shadow===true){this.parent.dom.setStyle(this.elements.shadow,{opacity:this.styles.fx.opacityShadow.Static/100,filter:"alpha(opacity="+this.styles.fx.opacityShadow.Static+")"});}}}.expand(this);this.styles={containerWindow:function()
{this.options.size.w=this.options.size.w||200;this.options.size.h=this.options.size.h||200;if(this.options.intoDOM)
{var center={x:(((this.target().offsetWidth/2)+this.target().scrollLeft)-(this.options.size.w/2)),y:(((this.target().offsetHeight/2)+this.target().scrollTop)-(this.options.size.h/2))};}
else
{var scroll=this.parent.dom.getPageScroll();this.parent.dom.get_doc()
var center={x:((((this.parent.dom.get_doc().clientWidth)/2)+scroll[0])-(this.options.size.w/2)),y:((((this.parent.dom.get_doc().clientHeight)/2)+scroll[1])-(this.options.size.h/2))};}
if(this.options.position.center===true)
{this.options.position.x=center.x;this.options.position.y=center.y;}
else if(this.options.position.centerX===true||this.options.position.centerY===true)
{this.options.position.x=((this.options.position.centerX===true)?center.x:(this.options.position.x||0));this.options.position.y=((this.options.position.centerY===true)?center.y:(this.options.position.y||0));}
else
{this.options.position.x=this.options.position.x||0;this.options.position.y=this.options.position.y||0;}
if(this.options.strict_position)
{this.options.position.x=(this.options.position.x<0)?0:this.options.position.x;this.options.position.y=(this.options.position.y<0)?0:this.options.position.y;}
this.zIndex=this.options.zIndex||this.makezIndex();this.parent.dom.setStyle(this.elements.containerWindow,{width:this.options.size.w,height:this.options.size.h,position:"absolute",left:this.options.position.x,top:this.options.position.y,opacity:this.styles.fx.opacityPanel.Static/100,filter:"alpha(opacity="+this.styles.fx.opacityPanel.Static+")",zIndex:this.zIndex});this.parent.dom.setStyle(this.elements.containerWindow,this.setStyle.containerWindow||{});},frontend:function()
{this.parent.dom.setStyle(this.elements.frontend,{width:(this.parent.browser.isIE)?"auto":"100%"});this.parent.dom.setStyle(this.elements.frontend,this.setStyle.frontend||{});},backend:function()
{this.parent.dom.setStyle(this.elements.backend,{});this.parent.dom.setStyle(this.elements.backend,this.setStyle.backend||{});},loader:function()
{this.parent.dom.setStyle(this.elements.loader,{background:"url('/images/classic/loader_B.gif')",backgroundRepeat:"no-repeat",width:32,height:32,position:"absolute",display:"none"});this.parent.dom.setStyle(this.elements.loader,this.setStyle.loader||{});},iframe:function()
{this.parent.dom.setStyle(this.elements.iframe,{});this.parent.dom.setStyle(this.elements.iframe,this.setStyle.iframe||{});},titleBar:function()
{this.parent.dom.setStyle(this.elements.titleBar,{display:((!this.options.titleBar)?"none":"")});this.parent.dom.setStyle(this.elements.titleBar,this.setStyle.titleBar||{});},title:function()
{this.parent.dom.setStyle(this.elements.title,{});this.parent.dom.setStyle(this.elements.title,this.setStyle.title||{});},roll:function()
{this.parent.dom.setStyle(this.elements.roll,{right:this.controlPosition(),height:this.controlSize.h,width:this.controlSize.w});this.parent.dom.setStyle(this.elements.roll,this.setStyle.roll||{});this.parent.event.add(this.elements.roll,"mouseup",this.roll,false);},setup:function()
{this.parent.dom.setStyle(this.elements.setup,{right:this.controlPosition(),height:this.controlSize.h,width:this.controlSize.w});this.parent.dom.setStyle(this.elements.setup,this.setStyle.setup||{});this.parent.event.add(this.elements.setup,"mouseup",(this.options.setup&&typeof this.options.setup=='function')?this.options.setup:function(){return false;},false);},close:function()
{this.parent.dom.setStyle(this.elements.close,{height:this.controlSize.h,right:this.controlPosition(),width:this.controlSize.w});this.parent.dom.setStyle(this.elements.close,this.setStyle.close||{});this.parent.event.add(this.elements.close,"mouseup",this.remove,false);},headerBar:function()
{this.parent.dom.setStyle(this.elements.headerBar,{display:((!this.options.headerBar)?"none":"block")});this.parent.dom.setStyle(this.elements.headerBar,this.setStyle.headerBar||{});},shadow:function()
{this.parent.dom.setStyle(this.elements.shadow,{width:this.elements.containerWindow.offsetWidth,height:this.elements.containerWindow.offsetHeight,top:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2),left:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"left"),10)+2),opacity:this.styles.fx.opacityShadow.Static/100,filter:"alpha(opacity="+this.styles.fx.opacityShadow.Static+")",zIndex:this.zIndex-2});this.parent.dom.setStyle(this.elements.shadow,this.setStyle.shadow||{});},modal:function()
{var ps=[this.parent.dom.get_doc().scrollWidth,this.parent.dom.get_doc().scrollHeight];this.parent.dom.setStyle(this.elements.modal,{height:ps[1],width:ps[0],position:"absolute",zIndex:this.zIndex-3});if(this.options.fx.fadeIn===true)
{new this.parent.module.fx.fade().make({duration:1000,end:this.styles.fx.opacityModal.Static/100,dom:this.elements.modal});}
else
{this.parent.dom.opacity(this.elements.modal,this.styles.fx.opacityModal.Static);}
this.parent.dom.setStyle(this.elements.modal,this.setStyle.modal||{});},tab:function()
{this.tab={display:"horizontal",initIn:20,step:5,optHeight:20,widthFixed:true,optWidth:this.tab.width-4}.concat(this.tab);var thm=this.tab.display==="vertical"?"":"H";var heightContent=this.options.size.h-(this.elements.titleBar.offsetHeight+this.elements.statusBar.offsetHeight);var beginTop=this.elements.titleBar.offsetHeight+this.elements.headerBar.offsetHeight;var beginLeft=4;var space=this.spaceOutPanel();this.tab.width=(this.tab.display==="vertical")?((this.tab.options)?((this.tab.width)?this.tab.width:80):0):4;this.parent.dom.setStyle(this.elements.tab,this.setStyle.tab||{});if(this.tab.options)
{this.parent.dom.setStyle(this.elements.tab,{height:((this.tab.display==="vertical")?heightContent:this.tab.optHeight+4+(this.parent.browser.isIE?14:0)),width:((this.tab.display==="vertical")?this.tab.width:this.options.size.w-8),top:beginTop,left:((this.tab.display==="vertical")?0:4)});this.tabSelected=false;this.tabLastSelected=false;this.tab.diffWidthBugPadding=((this.parent.browser.isIE)?0:20);this.elements.tabOptions=[];var lastBul=0;for(var i=0;i<this.tab.options.length;i++)
{var opH=this.tab.initIn+(this.tab.step*i)+(this.tab.optHeight*i);var opW=(this.tab.initIn+((this.tab.widthFixed===true)?(this.tab.optWidth*i):lastBul))+(this.tab.step*i);var tb=$dce("div");this.parent.dom.setStyle(tb,{padding:5,paddingLeft:((this.tab.display==="vertical")?15:5),paddingTop:((this.tab.display==="vertical")?5:4),width:((this.tab.widthFixed)?this.tab.optWidth-((this.tab.display==="vertical")?this.tab.diffWidthBugPadding:10):(typeof(mb_strlen)!=='undefined'?(mb_strlen(this.tab.options[i].title||'')*0.60)+'em':'auto')),position:"absolute",left:((this.tab.display==="vertical")?((this.tab.width-this.tab.optWidth)-((this.parent.browser.isIE)?-1:1)):opW),top:((this.tab.display==="vertical")?opH:0),bottom:((this.tab.display==="vertical")?"auto":0)});tb.innerHTML=this.tab.options[i].title||"";if(this.tab.options[i].selected===true)
{this.tabSelected=i;tb.className="panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected");}
else
{tb.className="panel_tabOption"+thm+"___"+this.getTheme("tabOption");tb.onmouseover=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tb,b:"panel_tabOptionOver"+thm+"___"+this.getTheme("tabOptionOver")});tb.onmouseout=function(o,j){o=window.event?o:j;o.a.className=o.b;}.args({a:tb,b:"panel_tabOption"+thm+"___"+this.getTheme("tabOption")});this.parent.dom.setStyle(tb,this.setStyle.tabOption||{});tb.onmouseup=function(event,tabID){if(this.tab.manualDisabled){return false;}
this.tabSelected=(this.parent.browser.isIE)?event:tabID;this.makeTab();return false;}.extend(this,i);}
this.elements.tab.appendChild(tb);lastBul+=tb.clientWidth;this.elements.tabOptions.push(tb);}
this.tabSelected=(this.tabSelected===false)?0:this.tabSelected;this.makeTab();}},content:function()
{var mgLeft=((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"width"),10):4);var mgTop=((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"height"),10)-1:4);this.parent.dom.setStyle(this.elements.content,{borderTopWidth:(!this.options.titleBar)?0:'auto',margin:4,marginLeft:((this.tab.display==="vertical")?mgLeft:4),marginTop:((this.tab.display==="vertical")?4:mgTop)});var tamH=this.elements.titleBar.offsetHeight+this.elements.statusBar.offsetHeight+this.elements.headerBar.offsetHeight;var heightContent=this.options.size.h-tamH-(this.tab.options?20:0);this.parent.dom.setStyle(this.elements.content,this.setStyle.content||{});var space=this.spaceOutPanel();var hC=(heightContent-space.y);var wC=(this.options.size.w-space.x);this.parent.dom.setStyle(this.elements.content,{height:hC,width:wC});this.originalContent={w:wC,h:hC};},statusBar:function()
{if(!this.options.statusBar)
{this.showing.status=false;this.parent.dom.setStyle(this.elements.statusBar,{display:"none"});}
this.parent.dom.setStyle(this.elements.statusBar,this.setStyle.statusBar||{});},status:function()
{this.parent.dom.setStyle(this.elements.status,{display:((this.options.control.resize&&!this.options.statusBarButtons)?"":"none")});this.parent.dom.setStyle(this.elements.status,this.setStyle.status||{});},statusButtons:function()
{this.parent.dom.setStyle(this.elements.statusButtons,{position:"relative",textAlign:"center",display:((this.options.statusBarButtons)?"":"none")});this.parent.dom.setStyle(this.elements.statusButtons,this.setStyle.statusButtons||{});},resize:function()
{this.parent.dom.setStyle(this.elements.resize,{});this.parent.dom.setStyle(this.elements.resize,this.setStyle.resize||{});},fx:{opacityShadow:{Static:20,Move:5},opacityModal:{Static:40,Move:10},opacityPanel:{Static:100,Move:50}},tabCSS:{over:function(event,obj){obj=this.parent.browser.isIE?event:obj;this.parent.dom.setStyle(obj,{});},out:function(event,obj){obj=this.parent.browser.isIE?event:obj;this.parent.dom.setStyle(obj,{});},sover:function(event,obj){obj=this.parent.browser.isIE?event:obj;this.parent.dom.setStyle(obj,{cursor:"default"});},sout:function(event,obj){obj=this.parent.browser.isIE?event:obj;this.parent.dom.setStyle(obj,{});},sel:{font:"normal 8pt Tahoma,MiscFixed",border:"1px solid #A3A2BC",borderRight:"1px solid #FFF",backgroundColor:"white",fontWeight:"bold",textAlign:"left",color:"#000000"},def:{font:"normal 8pt Tahoma,Miscfixed",border:"1px solid #A3A2BC",margin:0,fontWeight:"normal",color:"#000000",backgroundColor:"EEEEEE",textAlign:"left",cursor:"default"}}}.expand(this,true);this.makeTheme=function()
{this.themesDefault=["processmaker_fixed","panel","firefox"];this.theme=this.options.theme||"firefox";if(this.themesDefault.inArray(this.theme))
{this.theme="processmaker";}};this.getTheme=function(obj)
{return(this.customTheme&&this.customTheme[obj])?this.customTheme[obj]:this.theme;};this.command=function(fn,args,ret)
{if(typeof fn==="function")
{this.parent.exec(fn,args||false,ret||false,this);}};this.loader={show:function()
{this.loading=true;var mgTop=((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"height"),10)-1:0);this.parent.dom.setStyle(this.elements.loader,{top:((this.options.size.h/2)-(32/2)+mgTop),left:((this.options.size.w/2)-(32/2)),display:"block"});},hide:function()
{this.parent.dom.setStyle(this.elements.loader,{display:"none"});this.loading=false;}}.expand(this);this.status={show:function()
{var hhS=this.elements.status.offsetHeight;var hC=parseInt(this.parent.dom.getStyle(this.elements.content,"height"),10);this.parent.dom.setStyle([this.elements['status'],this.elements.statusBar],{display:""});var hS=this.elements.statusBar.offsetHeight;this.parent.dom.setStyle(this.elements.content,{height:hC-(hS-hhS)});this.showing.status=true;},hide:function()
{if(this.showing.status===true)
{var hS=parseInt(this.parent.dom.getStyle(this.elements.statusBar,"height"),10);var hC=parseInt(this.parent.dom.getStyle(this.elements.content,"height"),10);this.parent.dom.setStyle(this.elements.statusBar,{display:"none"});this.parent.dom.setStyle(this.elements.content,{height:hC+hS});this.showing.status=false;}},write:function()
{}}.expand(this);this.expand(this);}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.validator.js",Name:"validator",Type:"module",Version:"1.4"},content:function(param)
{this.valid=param.valid||false;this.invalid=param.invalid||false;this.validArray=(this.valid.isArray)?this.valid:[];this.invalidArray=(this.invalid.isArray)?this.invalid:[];this.add=param.add||false;this.generateKeys=function()
{this.keys=[];this.keys['es']=[];this.keys["es"]["Alpha"]=["abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ","áéíóúñÁÉÍÓÚÑüïÜÏ"," "];this.keys["es"]["Int"]=[[47,57]].concat("+-");this.keys["es"]["Real"]=[[48,57]].concat(".,-+");this.keys["es"]["Any"]=this.keys["es"]["Alpha"].concat("!#$%&/()=???+*{}[]-_.:,;'|\"\\@",[[48,57]]);this.keys["es"]["AlphaNum"]=this.keys['es']["Int"].concat(this.keys["es"]["Alpha"][0],this.keys["es"]["Alpha"][1]," ");this.keys["es"]["Field"]=["abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_"];this.keys["es"]["Email"]=[this.keys["es"]["Alpha"][0]].concat("._-@1234567890");this.keys["es"]["Login"]=[this.keys["es"]["Alpha"][0]].concat("._-@1234567890");this.keys["es"]["Path"]=this.keys['es']["Field"].concat("/"," ");this.keys["es"]["NodeName"]=this.keys['es']["Field"].concat("-");this.keys["en"]=[];this.keys["en"]["Alpha"]=[this.keys["es"]["Alpha"][0]];this.keys["en"]["Int"]=[[48,57]].concat("+-");this.keys["en"]["Real"]=[[48,57]].concat(".,-+");this.keys["en"]["Any"]=this.keys["en"]["Alpha"].concat("!#$%&/()=???+*{}[]-_.:,;'|\"\\@",[[48,57]]);this.keys["en"]["AlphaNum"]=this.keys['en']["Int"].concat(this.keys["en"]["Alpha"][0]," ");this.keys["en"]["Field"]=this.keys["es"]["Field"];this.keys["en"]["Email"]=[this.keys["es"]["Alpha"][0]].concat("._-@1234567890");this.keys["en"]["Login"]=[this.keys["es"]["Alpha"][0]].concat("._-@1234567890");this.keys["en"]["Path"]=this.keys['es']["Field"].concat("/"," ");this.keys["en"]["Tag"]=this.keys['es']["Field"].concat(","," ");this.keys["en"]["NodeName"]=this.keys['es']["Field"].concat("-");return(this.keys[this.lang][this.type])?this.keys[this.lang][this.type]:this.keys[this.lang]["Alpha"];};this.result=function()
{if(this.validArray[0].toLowerCase()=="any")
{return true;}
if(this.isNumber(param.key))
{this.key=param.key;}
else if(typeof param.key=="object")
{this.key=(param.key.which)?param.key.which:param.key.keyCode;}
else
{this.key=false;}
this.lang=param.lang||"en";var valid=true;for(var i=0;i<this.validArray.length;i++)
{this.type=this.validArray[i];valid=this.engine(this.generateKeys());if(valid===true){return true;}}
if(this.validArray.length===0)
{valid=this.engine([])}
return valid;};this.isNumber=function(a)
{return(a>=0)?true:false;};this.compareChar=function(_string,car){var i=0,a=false;while(i<_string.length&&!a){a=(_string.charCodeAt(i)==car);i++;}
return a;};this.isAlfaUS=function()
{patron=[];patron[0]=validator.keys.alfa[0];patron[1]=validator.keys.alfa[2];return patron;};this.isAlfa=function()
{patron=validator.keys.alfa;return patron;};this.checkAdd=function(p)
{if(this.add)
{return p.concat(this.add)}
else
{return p;}};this.engine=function(p)
{this.patron=this.checkAdd(p);var valid=false;for(var i=0;i<this.patron.length;i++)
{var b=this.patron[i];var type=typeof this.patron[i];if(type=="string")
{valid=this.compareChar(this.patron[i],this.key);}
else if(type=="object")
{valid=(this.key>=this.patron[i][0]&&this.key<=this.patron[i][1])?true:false;}
else if(type=="number")
{if(this.keys[this.lang]['validatorByLetter'])
{valid=(this.key==this.patron[i])?true:false;}
else
{valid=(this.key==this.patron[i])?true:false;}}
if(valid===true){return true;}}
return valid;};}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.app.js",Name:"app",Type:"module",Version:"0.1"},content:{line:function(options)
{this.options=options||{};this.make=function()
{this.options.concat(this.coords());this.direction=this.getDirection();this.lines=this.createLines();this.elements=[];for(var i=0;i<5;i++)
{var a=$dce("div");this.parent.dom.setStyle(a,{position:"absolute",width:this.options.size||1,height:this.options.size||1,border:"0px solid red",overflow:"hidden",backgroundColor:this.options.color||"black",zIndex:this.options.zIndex||4});this.options.target.appendChild(a);this.elements.push(a);}
var b=$dce("img");b.src=this.options.arrow||this.parent.info.base+"/images/arrowB.gif";this.parent.dom.setStyle(b,{position:"absolute",width:9,height:9,zIndex:this.options.zIndex||4});if(this.options.arrow!==false)
{this.options.target.appendChild(b);}
this.elements.push(b);this.paint();};this.changed=function()
{var probe=this.coords();return(this.options.x1!==probe.x1||this.options.y1!==probe.y1||this.options.x2!==probe.x2||this.options.y2!==probe.y2)?true:false;};this.remove=function()
{this.parent.dom.remove(this.elements);};this.coords=function()
{var el0=this.options.elements[0];var el1=this.options.elements[1];var fBug=(this.parent.browser.isGK)?1:0;var co={x1:((parseInt(el0.style.left,10)+(el0.offsetWidth/2))-fBug),y1:(parseInt(el0.style.top,10)+(el0.offsetHeight)),x2:((parseInt(el1.style.left||0,10)+(el1.offsetWidth/2))-fBug),y2:(parseInt(el1.style.top||0,10))};return co;};this.getDirection=function()
{var d={l:((this.options.x2<this.options.x1)?true:false),r:((this.options.x2>this.options.x1)?true:false),t:((this.options.y2<this.options.y1)?true:false),b:((this.options.y2>this.options.y1)?true:false)};return d;};this.createLines=function()
{return(this.direction.t)?5:3;};this.kase=function()
{var kase;if(this.options.y2>this.options.y1&&this.options.x2<this.options.x1)
{kase=1;}
else if(this.options.y1>this.options.y2&&this.options.x2<=this.options.x1)
{kase=2;}
else if(this.options.y2>this.options.y1&&this.options.x2>this.options.x1)
{kase=3;}
else if(this.options.y1>this.options.y2&&this.options.x2>this.options.x1)
{kase=4;}
else if(this.options.y2>this.options.y1&&this.options.x1===this.options.x2)
{kase=1;}
return kase;};this.paint=function()
{this.rootSize=this.options.indexRootSize||15;this.rootLastSize=this.options.indexRootLastSize||15;this.codo=[];var height0=((this.options.y2-this.options.y1)/2);this.codo[0]={x:this.options.x1,y:(this.options.y1+height0)};this.codo[1]={x:this.options.x2,y:this.codo[0].y};if(this.kase()==1)
{this.parent.dom.setStyle(this.elements[0],{height:height0,top:this.options.y1,left:this.codo[0].x});this.parent.dom.setStyle(this.elements[1],{width:this.codo[0].x-this.codo[1].x,top:this.codo[1].y,left:this.codo[1].x});this.parent.dom.setStyle(this.elements[2],{top:this.codo[1].y,left:this.codo[1].x,height:height0});}
else if(this.kase()==3)
{this.parent.dom.setStyle(this.elements[0],{height:height0,top:this.options.y1,left:this.codo[0].x});this.parent.dom.setStyle(this.elements[1],{width:this.codo[1].x-this.codo[0].x,top:this.codo[0].y,left:this.codo[0].x});this.parent.dom.setStyle(this.elements[2],{top:this.codo[1].y,left:this.codo[1].x,height:height0});}
else if(this.kase()==2)
{this.codo[0]={x:this.options.x1,y:(this.options.y1+this.rootSize+1)};this.codo[3]={x:this.options.x2,y:(this.options.y2-this.rootLastSize)};this.codo[2]={x:this.codo[0].x+((this.options.elements[0].offsetWidth/2)+this.rootSize),y:this.codo[3].y};this.codo[1]={x:this.codo[2].x,y:this.codo[0].y};this.parent.dom.setStyle(this.elements[0],{height:(this.codo[0].y-this.options.y1)+1,top:this.options.y1,left:this.options.x1});this.parent.dom.setStyle(this.elements[1],{width:this.codo[1].x-this.codo[0].x,top:this.codo[0].y,left:this.codo[0].x});this.parent.dom.setStyle(this.elements[2],{top:this.codo[2].y,left:this.codo[2].x,height:this.codo[1].y-this.codo[2].y});this.parent.dom.setStyle(this.elements[3],{top:this.codo[3].y,left:this.codo[3].x,width:this.codo[2].x-this.codo[3].x});this.parent.dom.setStyle(this.elements[4],{top:this.codo[3].y,left:this.codo[3].x,height:this.options.y2-this.codo[3].y});}
else if(this.kase()==4)
{this.codo[0]={x:this.options.x1,y:(this.options.y1+this.rootSize)};this.codo[3]={x:this.options.x2,y:(this.options.y2-this.rootLastSize)};this.codo[2]={x:this.codo[0].x-((this.options.elements[0].offsetWidth/2)+this.rootSize),y:this.codo[3].y};this.codo[1]={x:this.codo[2].x,y:this.codo[0].y};this.parent.dom.setStyle(this.elements[0],{height:this.codo[0].y-this.options.y1,top:this.options.y1,left:this.options.x1});this.parent.dom.setStyle(this.elements[1],{width:this.codo[0].x-this.codo[1].x,top:this.codo[1].y,left:this.codo[1].x});this.parent.dom.setStyle(this.elements[2],{top:this.codo[2].y,left:this.codo[2].x,height:this.codo[1].y-this.codo[2].y});this.parent.dom.setStyle(this.elements[3],{top:this.codo[2].y,left:this.codo[2].x,width:this.codo[3].x-this.codo[2].x});this.parent.dom.setStyle(this.elements[4],{top:this.codo[3].y,left:this.codo[3].x,height:this.options.y2-this.codo[3].y});}
var im=this.elements[5];this.parent.dom.setStyle(im,{top:this.options.y2-7,left:this.options.x2-4});};this.update=function()
{if(this.changed())
{this.options.concat(this.coords());if(this.kase()%2===0)
{this.elements[3].style.visibility="visible";this.elements[4].style.visibility="visible";}
else
{this.elements[3].style.visibility="hidden";this.elements[4].style.visibility="hidden";}
this.paint();}};this.expand(this);},menuRight:function(options){this.elements={};this.make=function(options)
{this.options={bubble:true,theme:"firefox"}.concat(options||{});if(!this.validate()){return false;}
if(this.options.auto_event){return false;}
this.parent.event.add(this.options.targetRemove||this.options.target,"click",this.updateObservers);this.options.target.oncontextmenu=this.menu;};this.menu=function(evt)
{this.evt=evt||this.options.auto_event||false;this.updateObservers();this.parent.dom.bubble(false,this.evt);this.maked=true;this.cursor=(this.options.auto_event)?this.options.auto_position:this.parent.dom.mouse(this.evt);this.positionTarget=this.parent.dom.position(this.options.target);this.elements.shadow=$dce("div");this.elements.shadow.className="app_menuRight_shadow___"+this.options.theme;this.elements.container=$dce("div");this.elements.container.className="app_menuRight_container___"+this.options.theme;this.parent.dom.setStyle(this.elements.container,{width:this.options.width||150,left:this.cursor.x-5,top:this.cursor.y-5});this.parent.dom.capture("tag.body 0").appendChild(this.elements.shadow);this.parseOptionsMenu();this.parent.dom.capture("tag.body 0").appendChild(this.elements.container);this.parent.dom.setStyle(this.elements.shadow,{width:this.elements.container.clientWidth,height:this.elements.container.clientHeight,left:this.cursor.x-((this.parent.browser.isIE)?1:3),top:this.cursor.y-((this.parent.browser.isIE)?1:3)});this.parent.dom.nullContextMenu([this.elements.container]);return false;};this.parseOptionsMenu=function()
{var ii=0;for(var i=0;i<this.options.menu.length;i++)
{if(typeof this.options.menu[i].separator=="undefined")
{var dv=$dce("div");dv.className="app_menuRight_option___"+this.options.theme;this.elements.container.appendChild(dv);var spI=$dce("div");spI.innerHTML="";spI.className="app_menuRight_option_image___"+this.options.theme;this.parent.dom.setStyle(spI,{});dv.appendChild(spI);var im;if(this.options.menu[i].image)
{im=$dce("img");im.className="app_menuRight_option_image_element___"+this.options.theme;im.src=this.options.menu[i].image;spI.appendChild(im);}
var spT=$dce("div");spT.className="app_menuRight_option_text___"+this.options.theme;spT.innerHTML=this.options.menu[i].text||"";dv.appendChild(spT);this.parent.dom.setStyle(dv,{});if(this.options.menu[i].submenu)
{}
else
{dv.onclick=this.launch.args(i);}
dv.onmouseover=function(evt,el){dv=(this.parent.browser.isIE?evt:el);var i=dv.i;dv.a.className="app_menuRight_option_over___"+this.options.theme;dv.b.className="app_menuRight_option_image_over___"+this.options.theme;if(this.submenu&&this.submenu_current_i!=i)
{this.submenu.remove();delete this.submenu;this.submenu_current_d.a.className="app_menuRight_option___"+this.options.theme;this.submenu_current_d.b.className="app_menuRight_option_image___"+this.options.theme;}
if(this.submenu&&this.submenu_current_i==i)
{return false;}
if(this.options.menu[i].submenu)
{var m=this.parent.dom.mouse(this.evt);var m=this.cursor;var n=this.parent.dom.position(dv.a,true);this.submenu_current_i=i;this.submenu_current_d=dv;this.submenu=new this.parent.module.app.menuRight();this.submenu.make({target:dv.a,width:201,auto_event:this.evt,auto_position:{x:n.x-5,y:n.y-15},parent_menu:this,theme:this.options.theme,menu:this.options.menu[i].submenu});this.submenu.menu();this.parent.event.add(this.options.targetRemove||this.options.target,"click",this.submenu.updateObservers);}}.extend(this,{a:dv,b:spI,i:i});dv.onmouseout=function(evt,el){var dv=(this.parent.browser.isIE?evt:el);if(this.submenu)
{return false;}
dv.a.className="app_menuRight_option___"+this.options.theme;dv.b.className="app_menuRight_option_image___"+this.options.theme;}.extend(this,{a:dv,b:spI});this.parent.dom.nullContextMenu([spI,spT,dv]);}
else
{var dv=$dce("div");dv.className="app_menuRight_optionNull___"+this.options.theme;this.elements.container.appendChild(dv);var spI=$dce("div");spI.innerHTML="";spI.className="app_menuRight_option_imageNull___"+this.options.theme;dv.appendChild(spI);var sep=$dce("div");sep.className="app_menuRight_separator___"+this.options.theme;dv.appendChild(sep);this.parent.dom.setStyle([dv,sep],{height:(this.parent.browser.isIE?2:0)});}
ii++;}};this.launch=function(evt,opt)
{if(this.options.parent_menu)
{this.options.parent_menu.updateObservers();}
this.remove();opt=this.parent.browser.isIE?evt:opt;var lch=this.options.menu[opt];if(lch&&typeof lch.launch=="function")
{lch.launch(evt);}};this.validate=function()
{this.options.target=this.parent.dom.element(this.options.target);return(!this.options.target||!this.options.menu)?false:true;};this.updateObservers=function()
{try{this.observer.update();}catch(e){this.remove();}};this.remove=function()
{if(this.maked===true)
{this.parent.dom.remove(this.elements.container);this.parent.dom.remove(this.elements.shadow);this.maked=false;}};this.expand(this);},submit:function(options)
{this.options={inProgress:function(){},callback:function(){}}.concat(options||{});if(!this.parent.dom.element(this.options.form)){return false;}
this.make=function(onSub)
{var Rt=true;onSub=arguments[1]||arguments[0];if(onSub&&typeof onSub==="function"){Rt=onSub();if(Rt===false){return false;}}
this.options.inProgress(this.options.form);var arg=new this.parent.dom.serializer(this.options.form,false);this.rpc=new this.parent.module.rpc.xmlhttp({url:this.options.form.action,method:this.options.form.method,args:arg.form()});this.rpc.callback=this.options.callback;this.rpc.make();return false;};this.expand(this);this.options.form.onsubmit=this.make.args(this.options.form.onsubmit);},iframe:function(element,post)
{this.element=this.parent.dom.element(element);this.post=(post===true)?true:false;if(!this.element){return false;}
var links=this.element.getElementsByTagName("a");this.href=function(event,opt)
{opt=arguments[1]||arguments[0];link=opt.l;onCl=opt.c;var loadIn=(link.target&&this.parent.dom.element(link.target))?this.parent.dom.element(link.target):this.element;var Rt=true;if(onCl&&typeof onCl==="function"){Rt=onCl();if(Rt===false){return false;}}
if(this.post)
{var a=link.href.split("?");this.url=a[0];}
else
{this.url=link.href;}
var rpc=new this.parent.module.rpc.xmlhttp({url:this.url,nocache:true,method:(this.post===true)?"POST":"GET",args:((this.post===true&&a[1])?a[1]:"")});rpc.callback=function(rpc,inn){inn.innerHTML=rpc.xmlhttp.responseText;new this.parent.module.app.iframe(inn,this.post)}.extend(this,loadIn);rpc.make();return false;};this.expand(this);for(var i=0;i<links.length;i++)
{var onC=links[i].onclick;links[i].onclick=this.href.args({l:links[i],c:onC});}},lightbox:function()
{this.inPlay=false;this.make=function(options)
{this.options={initIn:0,counter:true,target:document.body,resize:true,size:{w:400,h:310},position:{x:0,y:0,center:true},images:[]}.concat(options||{});this.windowImage();this.image=$dce("img");this.image.id=this.id;this.panel.elements.content.appendChild(this.image);this.load(this.options.initIn);};this.windowImage=function()
{this.panel=new this.parent.module.panel();this.panel.options={size:this.options.size,position:this.options.position,title:"",theme:"firefox",target:this.options.target,statusBar:true,limit:true,control:{close:true},fx:{shadow:false,modal:true,opacity:true,rolled:false,rollWidth:150}};this.panel.setStyle={content:{overflow:"hidden",textAlign:"center",verticalAlign:"center"},containerWindow:{border:"1px solid black"},modal:{backgroundColor:"black"},shadow:{backgroundColor:"black"},frontend:{backgroundColor:"white"}};this.panel.styles.fx.opacityShadow.Static=90;this.panel.make();this.panel.elements.modal.onmouseup=this.panel.remove;this.buttons();};this.load=function(evt,index)
{index=(typeof evt==="number")?evt:index;index=(index>this.options.images.length-1)?0:index;index=(index<0)?this.options.images.length-1:index;this.current=index;this.setLoad();var image=new Image();image.onload=this.show.args(image);image.src=this.options.images[index];if(this.options.counter)
{this.domCounter.innerHTML="<b>"+(index+1)+"</b> de <b>"+(this.options.images.length-1)+"</b>";}};this.show=function(evt,image)
{image=arguments[1]||arguments[0];if(this.options.resize)
{this.panel.resize({w:image.width+10,h:image.height+50});this.panel.center();}
this.image.src=image.src;this.unsetLoad();if(this.inPlay)
{setTimeout(this.control.next,5000);}};this.setLoad=function()
{this.image.style.display="none";this.panel.elements.content.style.borderWidth=1;this.panel.loader.show();};this.unsetLoad=function()
{this.panel.elements.content.style.borderWidth=0;this.panel.loader.hide();this.image.style.display="";};this.buttons=function()
{var target=this.panel.elements.statusBar;var end=$dce("div");this.parent.dom.setStyle(end,{right:5});var next=$dce("div");this.parent.dom.setStyle(next,{right:parseInt(end.style.right,10)+20});var play=$dce("div");this.parent.dom.setStyle(play,{right:parseInt(next.style.right,10)+20});var prev=$dce("div");this.parent.dom.setStyle(prev,{right:parseInt(play.style.right,10)+20});var begin=$dce("div");this.parent.dom.setStyle(begin,{right:parseInt(prev.style.right,10)+20});var title;this.domTitle=title=$dce("div");var counter;this.domCounter=counter=$dce("div");target.appendChild(end);target.appendChild(next);target.appendChild(play);target.appendChild(prev);target.appendChild(begin);target.appendChild(title);target.appendChild(counter);this.parent.dom.setStyle([end,next,play,prev,begin,title,counter],{position:"absolute",backgroundColor:"#006699",top:3,width:15,height:15,overflow:"hidden"});this.parent.dom.setStyle([title,counter],{left:5,backgroundColor:"white",color:"black",font:"normal 8pt Tahoma,MiscFixed"});this.parent.dom.setStyle(counter,{left:"auto",right:parseInt(begin.style.right,10)+30,width:60});begin.onmouseup=this.control.first;play.onmouseup=this.control.play;prev.onmouseup=this.control.previous;next.onmouseup=this.control.next;end.onmouseup=this.control.last;};this.control={play:function()
{if(!this.inPlay)
{this.control.next();}
this.inPlay=!this.inPlay;},next:function(){this.load(this.current+1);},previous:function(){this.load(this.current-1);},first:function()
{this.load(0);},last:function()
{this.load(this.options.images.length-1);}}.expand(this);this.expand(this);},slide:function()
{this.inPlay=false;this.last=false;this.elements={};this.stopped=false;this.inM=0;this.make=function(options)
{this.options={initIn:0,counter:true,playTimeOut:3,tactil:false,target:document.body,resize:true,size:{w:522,h:363},position:{x:0,y:30,centerX:true},skin_images:this.parent.info.base+"images/app.slide/",images:[]}.concat(options||{});this.options.thumbnail={show:4,size:{w:90,h:55},images:[]}.concat(options.thumbnail||{});this.windowImage();this.toolbarImage();this.image=$dce("img");this.panel.elements.content.appendChild(this.image);this.load(this.options.initIn);this.current=this.options.initIn;};this.windowImage=function()
{this.panel=new this.parent.module.panel();this.panel.options={size:this.options.size,position:this.options.position,target:this.options.target,statusBar:false,titleBar:false,limit:true,control:{drag:false,close:false},fx:{shadow:false,modal:true,opacity:true,rolled:false,rollWidth:150}};this.panel.events={remove:function(){var el=[this.domNext,this.domPlay,this.domPrev,this.buttonNext,this.domCounter,this.domTitle,this.domClose,this.toolbar,this.footer];if(this.options.banner)
{el.push(this.banner);}
new this.parent.module.fx.fade().make({duration:200,end:0,dom:el,onFinish:function(el){this.parent.dom.remove(el);}.extend(this,el)});}.extend(this)};this.panel.setStyle={content:{overflow:"hidden",textAlign:"center",verticalAlign:"center",margin:10,marginBottom:40,border:"1px solid #fff"},containerWindow:{border:"0px solid black",backgroundColor:"transparent"},modal:{backgroundColor:"black"},shadow:{backgroundColor:"black"},frontend:{background:"",backgroundColor:"#ECECEC"},backend:{backgroundColor:"transparent"},titleBar:{background:"transparent"},title:{textAlign:"left",color:"white"}};this.panel.styles.fx.opacityModal.Static=90;this.panel.make();if(this.options.tactil)
{this.panel.elements.modal.onmouseup=this.panel.remove;}};this.toolbarImage=function()
{this.toolbar=$dce("div");var div=$dce("div");var thu=this.options.thumbnail;var g=this.options.thumbnail.images.length;var h=4-(g%4);var j=(h===4)?0:h;var tw=((thu.show*thu.size.w)+(thu.show*(4)));this.toolbar.appendChild(div);this.toolbar.scrollLeft=0;this.options.target.appendChild(this.toolbar);this.parent.dom.setStyle(this.toolbar,{position:"absolute",border:"1px solid #666",width:tw+((this.parent.browser.isIE)?4:0),height:thu.size.h+((this.parent.browser.isIE)?8:4),padding:1,backgroundColor:"#000",overflow:"hidden",zIndex:this.panel.elements.containerWindow.style.zIndex+1});this.parent.dom.opacity(this.toolbar,80);var w=(thu.size.w*(thu.images.length+j))+(4*(thu.images.length+j));this.parent.dom.setStyle(div,{border:"0px solid blue",overflow:"hidden",width:w});this.elements.thumbs=[];for(var i=0;i<this.options.thumbnail.images.length;i++)
{var image=$dce("img");image.src=this.options.thumbnail.images[i].src;div.appendChild(image);this.parent.dom.setStyle(image,{margin:2,overflow:"hidden",width:thu.size.w,cursor:"pointer",height:thu.size.h});this.parent.dom.opacity(image,50);image.onmouseup=this.load.args(i);this.elements.thumbs.push(image);}
this.buttons();this.posComponents();};this.posComponents=function()
{var cn=this.panel.elements.containerWindow;var l=((parseInt(cn.style.left,10)+(cn.clientWidth/2))-(this.toolbar.clientWidth/2));this.parent.dom.setStyle(this.toolbar,{top:parseInt(cn.style.top,10)+290,left:l});this.parent.dom.setStyle(this.buttonPrevious,{top:parseInt(this.toolbar.style.top,10),left:parseInt(this.toolbar.style.left,10)-(this.buttonPrevious.clientWidth+5)});this.parent.dom.setStyle(this.buttonNext,{top:parseInt(this.toolbar.style.top,10),left:parseInt(this.toolbar.style.left,10)+this.toolbar.clientWidth+5});this.parent.dom.setStyle(this.domCounter,{top:parseInt(parseInt(this.toolbar.style.top,10)+65,10),left:parseInt(this.toolbar.style.left,10)+310});this.parent.dom.setStyle(this.domTitle,{top:parseInt(this.panel.options.position.y,10)-20,color:"#666",left:parseInt(this.panel.options.position.x,10)+5});this.parent.dom.setStyle(this.domClose,{top:parseInt(this.panel.options.position.y,10)+3,color:"#666",left:parseInt(this.panel.options.position.x+this.panel.options.size.w,10)-20});if(this.options.banner)
{this.parent.dom.setStyle(this.banner,{width:777,height:105,top:parseInt(this.domTitle.style.top)-110,left:(((this.options.target.clientWidth/2)+this.options.target.scrollLeft)-(777/2)),position:"absolute",zIndex:this.panel.elements.containerWindow.style.zIndex+1});}};this.load=function(evt,index)
{index=(typeof evt==="number")?evt:index;if(index>this.options.images.length-1||index<0)
{if(this.inPlay)
{this.control.play();}
return false;}
this.current=index;this.setLoad();this.domCounter.innerHTML="Foto <b>"+(index+1)+"</b> de <b>"+this.options.images.length+"</b>";this.domTitle.innerHTML=" <b>"+this.options.thumbnail.images[index].title||"Untitled"+"/b> ";if(this.last!==false)
{this.parent.dom.setStyle(this.elements.thumbs[this.last],{borderWidth:0,margin:2});this.parent.dom.opacity(this.elements.thumbs[this.last],50);}
this.parent.dom.setStyle(this.elements.thumbs[index],{border:"2px solid orange",margin:0});this.parent.dom.opacity(this.elements.thumbs[index],100);this.last=index;var image=new Image();image.onload=this.show.args(image);image.src=this.options.images[index];this.panel.addContentTitle(this.options.thumbnail.images[this.current].title||"");};this.show=function(evt,image)
{image=arguments[1]||arguments[0];if(this.options.resize)
{this.panel.resize({w:image.width+10,h:image.height+50});this.panel.center();}
this.image.src=image.src;this.unsetLoad();if(this.inPlay)
{setTimeout(this.control.next,this.options.playTimeOut*1000);}};this.setLoad=function()
{this.image.style.display="none";this.parent.dom.opacity(this.image,0);this.panel.loader.show();};this.unsetLoad=function()
{this.panel.loader.hide();this.image.style.display="";new this.parent.module.fx.fade().make({duration:500,end:1,dom:this.image,onFinish:function(){}.extend(this)});this.image.style.display="";};this.buttons=function()
{var target=this.options.target;this.footer=$dce("div");target.appendChild(this.footer);this.buttonNext=$dce("div");var rr=this.panel.elements.containerWindow;this.parent.dom.setStyle(this.footer,{position:"absolute",background:"url("+this.options.skin_images+"background_bottom_dark.png) no-repeat",width:524,height:56,top:this.panel.options.position.y+rr.offsetHeight,left:this.panel.options.position.x,zIndex:this.panel.elements.containerWindow.style.zIndex});this.buttonPrevious=$dce("div");this.parent.dom.setStyle([this.buttonNext,this.buttonPrevious],{position:"absolute",backgroundColor:"#006699",width:20,cursor:"pointer",height:this.toolbar.clientHeight,zIndex:this.panel.elements.containerWindow.style.zIndex});this.buttonNext.onmouseup=this.control.right;this.buttonPrevious.onmouseup=this.control.left;this.domNext=$dce("img");this.domNext.src=this.options.skin_images+"next_dark.png";this.domNext.onmouseover=function()
{this.domNext.src=this.options.skin_images+"next_on_dark.png";}.extend(this);this.domNext.onmouseout=function()
{this.domNext.src=this.options.skin_images+"next_dark.png";}.extend(this);this.parent.dom.setStyle(this.domNext,{position:"absolute",cursor:"pointer",width:30,height:56,top:this.panel.options.position.y+rr.offsetHeight+1,left:this.panel.options.position.x+295,zIndex:this.panel.elements.containerWindow.style.zIndex+1});this.domPlay=$dce("img");this.control.setPlay();this.parent.dom.setStyle(this.domPlay,{position:"absolute",cursor:"pointer",width:46,height:56,top:this.panel.options.position.y+rr.offsetHeight+1,left:this.panel.options.position.x+239,zIndex:this.panel.elements.containerWindow.style.zIndex+1});this.domPrev=$dce("img");this.domPrev.src=this.options.skin_images+"back_dark.png";this.domPrev.onmouseover=function()
{this.domPrev.src=this.options.skin_images+"back_on_dark.png";}.extend(this);this.domPrev.onmouseout=function()
{this.domPrev.src=this.options.skin_images+"back_dark.png";}.extend(this);this.parent.dom.setStyle(this.domPrev,{position:"absolute",cursor:"pointer",width:30,height:56,top:this.panel.options.position.y+rr.offsetHeight+1,left:this.panel.options.position.x+200,zIndex:this.panel.elements.containerWindow.style.zIndex+1});var counter;this.domCounter=counter=$dce("div");this.domTitle=$dce("div");this.domClose=$dce("img");this.domClose.src=this.options.skin_images+"close.gif";this.parent.dom.setStyle(this.domClose,{cursor:"pointer"});this.domClose.onmousedown=this.panel.remove;target.appendChild(this.domNext);target.appendChild(this.domPlay);target.appendChild(this.domPrev);target.appendChild(this.domCounter);target.appendChild(this.domTitle);target.appendChild(this.domClose);this.parent.dom.setStyle([counter,this.domTitle,this.domClose],{position:"absolute",font:"normal 8pt Tahoma,MiscFixed",color:"black",overflow:"hidden",zIndex:this.panel.elements.containerWindow.style.zIndex+1});this.domPlay.onmouseup=this.control.play;this.domPrev.onmouseup=this.control.previous;this.domNext.onmouseup=this.control.next;if(this.options.banner)
{this.banner=$dce("img");this.banner.src=this.options.banner;this.options.target.appendChild(this.banner);}};this.control={setPlay:function()
{this.domPlay.src=this.options.skin_images+"play_dark.png";this.domPlay.onmouseover=function()
{this.domPlay.src=this.options.skin_images+"play_on_dark.png";}.extend(this);this.domPlay.onmouseout=function()
{this.domPlay.src=this.options.skin_images+"play_dark.png";}.extend(this);},setPause:function()
{this.domPlay.src=this.options.skin_images+"pause_dark.png";this.domPlay.onmouseover=function()
{this.domPlay.src=this.options.skin_images+"pause_on_dark.png";}.extend(this);this.domPlay.onmouseout=function()
{this.domPlay.src=this.options.skin_images+"pause_dark.png";}.extend(this);},play:function()
{if(!this.inPlay)
{this.control.setPause();this.stopped=false;this.inPlay=!this.inPlay;this.control.next();}
else
{this.control.setPlay();this.stopped=true;this.inPlay=!this.inPlay;}},next:function(){if(this.Null===true){return false;}
if(this.stopped===false)
{var t=this.current+1;if(t%4===0)
{this.control.right();}
else
{this.load(t);}}
else
{this.stopped=false;}},previous:function(){if(this.Null===true){return false;}
var t=this.current;if(t%4===0)
{this.control.left();}
else
{this.load(this.current-1);}},left:function()
{this.Null=true;new this.parent.module.fx.algorithm().make({transition:"sineInOut",duration:1000,begin:this.toolbar.scrollLeft,end:this.toolbar.scrollLeft-((this.options.thumbnail.size.w*4)+(4*4)),onTransition:function(fx){this.toolbar.scrollLeft=fx.result;}.extend(this),onFinish:function(fx){this.toolbar.scrollLeft=fx.options.end;this.load(this.current-1);this.Null=false;}.extend(this)});},right:function()
{this.Null=true;new this.parent.module.fx.algorithm().make({transition:"sineInOut",duration:1000,begin:this.toolbar.scrollLeft,end:this.toolbar.scrollLeft+((this.options.thumbnail.size.w*4)+(4*4)),onTransition:function(fx){this.toolbar.scrollLeft=fx.result;}.extend(this),onFinish:function(fx){this.toolbar.scrollLeft=fx.options.end;var t=this.current+1;this.load(t);this.Null=false;}.extend(this)});}}.expand(this);this.expand(this);},box:function()
{this.panel=new this.parent.module.panel();this.panel.options={size:{w:300,h:200},title:"Prueba panel",headerBar:true,titleBar:false,elementToDrag:"backend",position:{x:5,y:5,center:true},fx:{shadow:false,modal:true,opacity:false}};this.panel.setStyle={containerWindow:{border:"0px solid red"},frontend:{backgroundColor:"transparent"},content:{margin:0,border:"0px solid red",borderLeft:"1px solid #DADADA",borderRight:"1px solid #DADADA",backgroundColor:"white"},headerBar:{display:''},statusBar:{}};this.panel.styles.fx.opacityModal.Static=0;this.panel.make();this.panel.elements.headerBar.className="boxTopPanel";this.panel.elements.headerBar.innerHTML="<div class='a'>&nbsp;</div><div class='b'>&nbsp;</div><div class='c'>&nbsp;</div>";this.panel.elements.statusBar.className="boxBottom";this.panel.elements.statusBar.innerHTML="<div class='a'>&nbsp;</div><div class='b'>&nbsp;</div><div class='c'>&nbsp;</div>";return this.panel;},confirm:function()
{this.make=function(options)
{var lb=(typeof G_STRINGS!=='undefined')?G_STRINGS:{};var label={accept:lb.ACCEPT||"Aceptar",cancel:lb.CANCEL||"Cancelar"};this.panel=new this.parent.module.panel();this.options={action:function(){}}.concat(options||{});this.panel.options={statusBarButtons:[{value:label.accept},{value:label.cancel}],position:{center:true},size:{w:(typeof(options.width)!='undefined')?options.width:350,h:(typeof(options.height)!='undefined')?options.height:100},control:{close:true,resize:false},fx:{modal:true}};this.panel.setStyle={content:{padding:10,paddingBottom:2,textAlign:"left",paddingLeft:50,background:"url("+this.parent.info.images+"question.png)",backgroundRepeat:"no-repeat",backgroundPosition:"10 50%",backgroundColor:"transparent",borderWidth:0}};this.panel.make();this.panel.addContent(this.options.label||"");this.panel.fixContent();this.panel.elements.statusBarButtons[0].onmouseup=function()
{this.options.action();this.panel.remove();return false;}.extend(this);this.panel.elements.statusBarButtons[1].onmouseup=function()
{if(this.options.cancel)
{this.options.cancel();}
this.panel.remove();return false;}.extend(this);this.panel.events={remove:function(){}.extend(this)};};},alert:function()
{this.make=function(options)
{var lb=(typeof G_STRINGS!=='undefined')?G_STRINGS:{};var label={accept:lb.ACCEPT||"Aceptar"};this.panel=new this.parent.module.panel();this.options={action:function(){},target:document.body}.concat(options||{});this.panel.options={statusBarButtons:[{value:label.accept}],target:this.options.target,position:{center:true},size:{w:(typeof(options.width)!='undefined')?options.width:300,h:(typeof(options.height)!='undefined')?options.height:110},control:{close:true,resize:false},fx:{modal:true}};this.panel.setStyle={content:{padding:10,paddingBottom:2,textAlign:"left",paddingLeft:65,background:"url("+this.parent.info.images+"warning.png)",backgroundRepeat:"no-repeat",backgroundPosition:"10 50%",backgroundColor:"transparent",borderWidth:0}};this.panel.make();this.panel.addContent(this.options.label||"");this.panel.fixContent();this.panel.elements.statusBarButtons[0].onmouseup=function()
{this.options.action();this.panel.remove();return false;}.extend(this);return this;};},info:function()
{this.make=function(options)
{var lb=(typeof G_STRINGS!=='undefined')?G_STRINGS:{};var label={accept:lb.ACCEPT||"Aceptar"};this.panel=new this.parent.module.panel();this.options={action:function(){},target:document.body}.concat(options||{});this.panel.options={statusBarButtons:[{value:label.accept}],target:this.options.target,position:{center:true},size:{w:(typeof(options.width)!='undefined')?options.width:300,h:(typeof(options.height)!='undefined')?options.height:110},control:{close:true,resize:false},fx:{modal:true}};this.panel.setStyle={content:{padding:10,paddingBottom:2,textAlign:"left",paddingLeft:65,background:"url("+this.parent.info.images+"info.png)",backgroundRepeat:"no-repeat",backgroundPosition:"10 50%",backgroundColor:"transparent",borderWidth:0}};this.panel.make();this.panel.addContent(this.options.label||"");this.panel.fixContent();this.panel.elements.statusBarButtons[0].onmouseup=function()
{this.options.action();this.panel.remove();return false;}.extend(this);return this;};},prompt:function()
{this.make=function(options)
{var lb=(typeof G_STRINGS!=='undefined')?G_STRINGS:{};var label={accept:lb.ACCEPT||"Aceptar",cancel:lb.CANCEL||"Cancelar"};this.panel=new this.parent.module.panel();this.options={action:function(){},value:""}.concat(options||{});this.panel.options={statusBarButtons:[{value:label.accept},{value:label.cancel}],position:{center:true},size:{w:300,h:110},control:{close:true,resize:false},fx:{modal:true}};this.panel.setStyle={content:{padding:10,paddingBottom:2,textAlign:"left",paddingLeft:50,background:"url("+this.parent.info.images+"question.png)",backgroundRepeat:"no-repeat",backgroundPosition:"10 50%",backgroundColor:"transparent",borderWidth:0}};this.panel.events={remove:this.functionOnFALSE};this.panel.make();this.panel.addContent(this.options.label||"");this.panel.addContent("<br>");this.input=$dce("input");this.input.type="text"
this.parent.dom.setStyle(this.input,{font:"normal 8pt Tahoma,MiscFixed",color:"#000",width:"100%",marginTop:3,backgroundColor:"white",border:"1px solid #919B9C"});this.panel.addContent(this.input);this.input.value=this.options.value;this.input.focus();this.input.onkeyup=function(evt)
{var evt=(window.event)?window.event:evt;var key=(evt.which)?evt.which:evt.keyCode;if(key==13)
{this.functionOnTRUE();}
else if(key==27)
{this.functionOnFALSE();}}.extend(this);this.panel.fixContent();this.panel.elements.statusBarButtons[0].onmouseup=this.functionOnTRUE;this.panel.elements.statusBarButtons[1].onmouseup=this.functionOnFALSE;return this;};this.functionOnTRUE=function()
{this.action=true;this.options.action(this.input.value);this.panel.remove();return false;};this.functionOnFALSE=function()
{if(this.options.cancel&&this.action!==true)
{this.options.cancel();}
this.panel.remove();return false;};this.expand(this);},radioEvents:function()
{this.makes=[];this.add=function(param)
{var radio=this.parent.module.dom.radioByValue({name:param.name,value:param.value});var idName=param.name+param.value;param.activated=false;radio.id=idName;radio.onclick=this.engine.args({options:param,radio:radio});if(param.make){this.makes[this.makes.length]=idName;}};this.engine=function(param)
{this.conditions(param.radio,param.options.event,true,false);if(param.options.revert)
{var rv=param.arguments.options.revert.split(",");for(var i=0;i<rv.length;i++)
{this.revert(rv[i],param);}}
else
{param.options.activated=true;}
this.engineChilds(param.options.childs,false);};this.engineChilds=function(childs,reverse)
{if(childs)
{var childs=childs.split(",");for(var i=0;i<childs.length;i++)
{var state=childs[i].split("|");var r_name=state[0],r_value=state[1],r_action=state[2];r_value=r_value.split("-");for(var j=0;j<r_value.length;j++)
{var r_adio=this.parent.module.dom.radioByValue({name:r_name,value:r_value[j]});this.conditions(r_adio,r_action,false,reverse);}}}};this.conditions=function(radio,action,probe,revert)
{var revert=revert||false;if(action=="c")
{if(revert)
{radio.checked=false;}else
{radio.checked=true;}
this.recursive(radio.id,action,probe);}
else if(action=="d")
{if(revert)
{radio.disabled=false;}else
{radio.disabled=true;}
this.recursive(radio.id,action,probe);}
else if(action=="cd")
{if(revert)
{radio.disabled=false;}else
{radio.disabled=true;radio.checked=false;}
this.recursive(radio.id,(revert)?"ce":"cd",probe);}
else if(action=="ce")
{if(revert)
{radio.disabled=true;}else
{radio.disabled=false;radio.checked=true;}
this.recursive(radio.id,action,probe);}
else if(action=="u")
{if(revert)
{radio.checked=true;}else
{radio.checked=false;}
this.recursive(radio.id,(revert)?"c":"u",probe);}
else if(action=="e")
{if(revert)
{radio.disabled=true;radio.checked=false;}else
{radio.disabled=false;}
this.recursive(radio.id,action,probe);}};this.recursive=function(r_id,action,probe)
{if(!probe)
{}};this.revert=function(r_id,ref)
{var idName=r_id.split("|");if(idName.length==2)
{idName=idName[0]+idName[1];if(leimnud.aplication.event.isset(idName)&&leimnud.aplication.event.query(idName).arguments.options.activated)
{var id=leimnud.aplication.event.query(idName).arguments;var child=id.options.childs;id.options.activated=false;ref.options.activated=true;this.conditions(id.radio,id.options.event,true,true);this.engineChilds(child,true);}
else
{ref.options.activated=true;}}};this.make=function()
{for(var i=0;i<this.makes.length;i++)
{leimnud.aplication.event.launch(false,this.makes[i]);}};this.expand(this);}}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.rpc.js",Name:"rpc",Type:"module",Version:"0.1"},content:{xmlhttp:function(options)
{this.options=options||{};this.headers=[];this.core=function()
{try{xmlhttp=false;if(window.ActiveXObject)
xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
catch(e)
{try
{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
catch(e)
{xmlhttp=false;}}
return(!xmlhttp&&typeof XMLHttpRequest!='undefined')?new XMLHttpRequest():xmlhttp||new function(){};};this.make=function()
{this.xmlhttp=this.core();this.url=this.options.url||false;if(!this.options.url||!this.xmlhttp){return false;}
this.method=(this.options.method||"POST").toUpperCase();this.args=this.options.args||"";this.async=(this.options.async===false)?false:true;if(this.method=="POST"){this.header("Content-Type","application/x-www-form-urlencoded");}
this.open();return true;};this.open=function()
{this.url=((this.url.substr(this.url.length-1,1)!=="?"&&this.method==="GET")?this.url+"?":this.url);this.url=((this.method=="GET")?(this.url+this.args):this.url);this.xmlhttp.open(this.method,this.url+((this.options.nocache)?"&rand_rpc="+Math.random():""),this.async);this.applyHeaders();this.xmlhttp.send((this.method=="GET")?null:this.args);this.xmlhttp.onreadystatechange=this.changes;};this.changes=function(g)
{if(this.xmlhttp.readyState==4)
{if(this.callback)
{this.callback.args(this)();}}};this.applyHeaders=function()
{for(var i=0;i<this.headers.length;i++)
{this.xmlhttp.setRequestHeader(this.headers[i].param,this.headers[i].value);}};this.header=function(param,value)
{this.headers.push({param:param,value:value});};this.expand(this);},json:function(options)
{this.interval=false;this.options={url:false,method:"GET",args:""}.concat(options||{});this.begin=new Date().getTime();this.tmp="rpcJson_"+this.begin;this.server=this.parent.info.base+"server/proxy.js.php";this.par=this.parent.info.domBaseJS.parentNode;this.make=function(options)
{if(!this.options.url||!this.par){return false;}
this.script=$dce("script");this.par.appendChild(this.script);this.script.src=this.server
+"?tmp="+this.tmp
+"&url="+this.options.url
+"&method="+this.options.method
+"&args="+encodeURIComponent(this.options.args);this.script.type="text/javascript";this.script.charset=this.parent.charset;this.interval=setInterval(this.probe,500);};this.probe=function()
{this.time=new Date().getTime()-this.begin;if(window[this.tmp]&&window[this.tmp].loaded===true||this.time>65000)
{this.interval=clearInterval(this.interval);var rt;try{rt=window[this.tmp].data.parseJSON();}
catch(e)
{rt="";}
if(this.options.debug===true&&console.info)
{console.info(rt)}
var myDocument;if(document.implementation.createDocument)
{var parser=new DOMParser();try{window.lk=myDocument=parser.parseFromString(rt||"<xml>empty</xml>","text/xml");}catch(e)
{myDocument=parser.parseFromString("<xml>empty</xml>","text/xml");}}else if(window.ActiveXObject){myDocument=new ActiveXObject("Microsoft.XMLDOM");myDocument.async="false";try{myDocument.loadXML(rt||"<xml>empty</xml>");}
catch(e)
{myDocument.loadXML("<xml>empty</xml>");}}
this.json={responseText:rt,responseXML:myDocument};if(this.parent.browser.isIE)
{window[this.tmp]=null;}
else
{delete window[this.tmp];}
this.script.parentNode.removeChild(this.script);if(this.callback)
{this.callback.args(this)();}}};this.expand(this);return this;}}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.fx.js",Name:"fx",Type:"module",Version:"0.1"},content:{algorithm:function()
{this.make=function(options)
{this.options={transition:"sineInOut",duration:2000,fps:70,onTransition:function(){},onFinish:function(){},begin:0,end:100,timeBegin:new Date().getTime()}.concat(options||{});this.timer=setInterval(function(){var time=new Date().getTime();if(time<this.options.timeBegin+this.options.duration)
{this.cTime=time-this.options.timeBegin;if(this.options.begin.isArray)
{this.result=[];for(var i=0;i<this.options.begin.length;i++)
{this.result.push(this.transitions[this.options.transition](this.cTime,this.options.begin[i],(this.options.end[i]-this.options.begin[i]),this.options.duration,0,0));}}
else
{this.result=this.transitions[this.options.transition](this.cTime,this.options.begin,(this.options.end-this.options.begin),this.options.duration,0,0);}
return this.options.onTransition(this);}
else
{this.timer=clearInterval(this.timer);return this.options.onFinish(this);}}.extend(this),Math.round(1000/this.options.fps));};this.transitions={linear:function(t,b,c,d){return c*t/d+b;},quadIn:function(t,b,c,d){return c*(t/=d)*t+b;},quadOut:function(t,b,c,d){return-c*(t/=d)*(t-2)+b;},quadInOut:function(t,b,c,d){if((t/=d/2)<1)return c/2*t*t+b;return-c/2*((--t)*(t-2)-1)+b;},cubicIn:function(t,b,c,d){return c*(t/=d)*t*t+b;},cubicOut:function(t,b,c,d){return c*((t=t/d-1)*t*t+1)+b;},cubicInOut:function(t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t+b;return c/2*((t-=2)*t*t+2)+b;},quartIn:function(t,b,c,d){return c*(t/=d)*t*t*t+b;},quartOut:function(t,b,c,d){return-c*((t=t/d-1)*t*t*t-1)+b;},quartInOut:function(t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t+b;return-c/2*((t-=2)*t*t*t-2)+b;},quintIn:function(t,b,c,d){return c*(t/=d)*t*t*t*t+b;},quintOut:function(t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b;},quintInOut:function(t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t*t+b;return c/2*((t-=2)*t*t*t*t+2)+b;},sineIn:function(t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+c+b;},sineOut:function(t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b;},sineInOut:function(t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b;},expoIn:function(t,b,c,d){return(t==0)?b:c*Math.pow(2,10*(t/d-1))+b;},expoOut:function(t,b,c,d){return(t==d)?b+c:c*(-Math.pow(2,-10*t/d)+1)+b;},expoInOut:function(t,b,c,d){if(t==0)return b;if(t==d)return b+c;if((t/=d/2)<1)return c/2*Math.pow(2,10*(t-1))+b;return c/2*(-Math.pow(2,-10*--t)+2)+b;},circIn:function(t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)-1)+b;},circOut:function(t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b;},circInOut:function(t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b;},elasticIn:function(t,b,c,d,a,p){if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(!a)a=1;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;},elasticOut:function(t,b,c,d,a,p){if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(!a)a=1;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b;},elasticInOut:function(t,b,c,d,a,p){if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(!a)a=1;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b;},backIn:function(t,b,c,d,s){if(!s)s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b;},backOut:function(t,b,c,d,s){if(!s)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b;},backInOut:function(t,b,c,d,s){if(!s)s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b;},bounceIn:function(t,b,c,d){return c-this.transitions.bounceOut(d-t,0,c,d)+b;},bounceOut:function(t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b;}else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b;}else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b;}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b;}},bounceInOut:function(t,b,c,d){if(t<d/2)return this.transitions.bounceIn(t*2,0,c,d)*.5+b;return this.transitions.bounceOut(t*2-d,0,c,d)*.5+c*.5+b;}}.expand(this);this.expand(this);},fade:function()
{this.make=function(options)
{this.options={duration:1000,transition:"sineInOut",end:0,onFinish:function(){}}.concat(options);this.dom=((this.options.dom||[]).isArray)?this.options.dom:[this.options.dom];for(var i=0;i<this.dom.length;i++)
{of=(i==(this.dom.length-1))?this.options.onFinish:function(){};new this.parent.module.fx.algorithm().make({duration:this.options.duration,end:this.options.end,transition:this.options.transition,begin:this.parent.dom.getOpacity(this.dom[i]),onTransition:function(fx,dom){this.parent.dom.opacity(dom,fx.result*100);}.extend(this,this.dom[i]),onFinish:function(fx,dom,finish)
{this.parent.dom.opacity(dom,this.options.end*100);return finish();}.extend(this,this.dom[i],of)});}
this.expand(this);};},move:function()
{this.make=function(options)
{this.options={duration:1000,transition:"sineInOut",end:0,onFinish:function(){}}.concat(options);this.dom=this.options.dom;new this.parent.module.fx.algorithm().make({duration:this.options.duration,end:[this.options.end.x,this.options.end.y],transition:this.options.transition,begin:[parseInt(this.dom.style.left),parseInt(this.dom.style.top)],onTransition:function(fx,dom){this.dom.style.left=fx.result[0];this.dom.style.top=fx.result[1];}.extend(this,this.dom),onFinish:function(fx,dom,finish)
{this.dom.style.left=this.options.end.x;this.dom.style.top=this.options.end.y;return this.options.onFinish();}.extend(this,this.dom)});this.expand(this);};}}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.drag.js",Name:"drag",Type:"module",Version:"0.1"},content:function(options){this.options={limitbox:false}.concat(options||{});this.loaded=false;this.eventHandlers=[];this.cursor="move";this.uid=this.parent.tools.createUID();this.make=function()
{this.options.elements=this.set();this.options.fx={type:"simple",target:document.body}.concat(this.options.fx||{});this.events=this.events||{};var elements=(this.options.elements||[]).length;this.flagEvents=[];var oThis=this;for(var i=0;i<elements;i++)
{var revent=this.parent.event.add(this.options.elements[i],"mousedown",this.parent.closure({method:this.onInit,instance:this,event:true,args:[i]}),false);this.flagEvents.push(revent);}};this.set=function()
{if(this.options.elements)
{this.type="simple";return(this.options.elements.isArray)?this.options.elements:[this.options.elements];}
else if(this.options.group)
{this.type="group";return(this.options.group.isArray)?this.options.group:[this.options.group];}
else if(this.options.link)
{this.type="link";this.linkRef=((!this.options.link.ref.isArray&&(this.options.link.ref.isObject||(this.parent.browser.isIE&&!this.options.link.ref.isObject)))||this.options.link.ref.isArray)?this.options.link.ref:[this.options.link.ref];return(this.options.link.elements.isArray)?this.options.link.elements:[this.options.link.elements];}
else
{return[];}};this.onInit=function(fEvent,elNum)
{if(this.noDrag===true)
{return false;}
if(this.lock===true){return false;}
this.lock=true;if(arguments.length<2&&this.parent.browser.isIE)
{elNum=fEvent;fEvent=window.event;}
this.currentElementInArray=elNum;var element=this.options.elements[elNum];this.currentElementDrag=element;var position;this.cursorStart=this.parent.dom.mouse(fEvent);if(this.type=="simple")
{if(this.options.fx.type=="simple")
{this.probeAbsolute(element);this.elementStart={x:parseInt(this.parent.dom.getStyle(element,"left"),10),y:parseInt(this.parent.dom.getStyle(element,"top"),10)};}
else if(this.options.fx.type=="clone")
{var m=this.parent.dom.mouse(fEvent);var ps=this.parent.dom.position(this.options.fx.target);var pos={x:(m.x-ps.x),y:(m.y-ps.y)};window.status=pos.x+":"+pos.y+"::::"+m.x+":"+m.y+":::"+ps.x+":"+ps.y;var clo=element.cloneNode(true);this.currentElementDrag=clo;var ev=this.parent.event.db[this.flagEvents[elNum]];this.parent.event.remove(clo,ev._event_,ev._function_);this.parent.dom.setStyle(clo,{visibility:"hidden"});this.options.fx.target.appendChild(clo);this.parent.dom.setStyle(clo,{position:"absolute",left:pos.x+(this.options.fx.target.scrollLeft||0)-(clo.clientWidth/2),top:pos.y+(this.options.fx.target.scrollTop||0)-(clo.clientHeight/2),zIndex:this.options.fx.zIndex||1000,visibility:"visible"});this.elementStart={x:parseInt(this.parent.dom.getStyle(clo,"left"),10),y:parseInt(this.parent.dom.getStyle(clo,"top"),10)};element=clo;this.parent.dom.opacity(clo,33);}}
else if(this.type=="group")
{this.elementStart=[];for(var i=0;i<this.options.elements.length;i++)
{position=this.parent.dom.position(this.options.elements[i],false,true);this.elementStart[i]={x:position.x,y:position.y};}
this.absolutizeGroup();}
else if(this.type=="link")
{this.elementStart=[];for(i=0;i<this.linkRef.length;i++)
{var position=this.parent.dom.position(this.linkRef[i],false,true);this.elementStart[i]={x:position.x,y:position.y};}
this.absolutizeLink();}
this.parent.event.add(document,"mousemove",this.parent.closure({method:this.onMove,instance:this,event:true,args:[elNum,element,this.parent.event.db.length]}),true);this.parent.event.add(document,"mouseup",this.parent.closure({method:this.onFinish,instance:this,event:true,args:[elNum,element,this.parent.event.db.length]}),true);if(window.event)
{window.event.cancelBubble=true;window.event.returnValue=false;}
else
{fEvent.preventDefault();}
this.parent.dom.bubble(false,fEvent);this.launchEvents(this.events.init);this.moved=false;};this.onMove=function(event,elNum,element)
{var cursor,rG,tL,tT;cursor=this.currentCursorPosition=this.parent.dom.mouse(event);if(this.type=="simple")
{rG={l:true,t:true};tL=parseInt(this.elementStart.x+(cursor.x-this.cursorStart.x),10);tT=parseInt(this.elementStart.y+(cursor.y-this.cursorStart.y),10);if((tL<0||this.options.limit==="x")||(this.options.limitbox&&(tL+element.clientWidth)>this.options.limitbox.clientWidth)){rG.l=false;}
if((tT<0||this.options.limit==="y")||(this.options.limitbox&&(tT+element.clientHeight)>this.options.limitbox.clientHeight)){rG.t=false;}
this.currentX=tL;this.currentY=tT;if(rG.l||!this.options.limit)
{this.parent.dom.setStyle(element,{left:tL});}
if(rG.t||!this.options.limit)
{this.parent.dom.setStyle(element,{top:tT});}}
else if(this.type=="group")
{for(var i=0;i<this.options.elements.length;i++)
{this.parent.dom.setStyle(this.options.elements[i],{left:this.elementStart[i].x+(cursor.x-this.cursorStart.x),top:this.elementStart[i].y+(cursor.y-this.cursorStart.y)});}}
else if(this.type=="link")
{if(this.options.limit===true)
{var rng=this.parent.dom.positionRange(this.linkRef,false,true);rG={l:true,t:true};for(i=0;i<this.linkRef.length;i++)
{tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);if(tL<0){rG.l=false;}
if(tT<0){rG.t=false;}}
for(i=0;i<this.linkRef.length;i++)
{tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);if(rG.l)
{this.parent.dom.setStyle(this.linkRef[i],{left:tL});}
if(rG.t)
{this.parent.dom.setStyle(this.linkRef[i],{top:tT});}}}
else
{for(i=0;i<this.linkRef.length;i++)
{tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);this.parent.dom.setStyle(this.linkRef[i],{left:tL,top:tT});}}}
if(window.event)
{window.event.cancelBubble=true;window.event.returnValue=false;}
else
{event.preventDefault();}
this.launchEvents(this.events.move);};this.onFinish=function(event,elNum,element,hand)
{if(arguments.length<4&&this.parent.browser.isIE)
{hand=element;element=elNum;elNum=event;event=window.event;}
this.cursorEnd=this.parent.dom.mouse(event);this.moved=((this.cursorStart.x!==this.cursorEnd.x)||(this.cursorStart.y!==this.cursorEnd.y))?true:false;this.launchEvents(this.events.finish);this.parent.event.remove(document,"mouseup",this.parent.event.db[hand]._function_,true,hand);this.parent.event.remove(document,"mousemove",this.parent.event.db[hand-1]._function_,true,hand-1);this.lock=false;};this.flush=function()
{this.parent.event.flushCollection(this.flagEvents);this.flagEvents=[];};this.probeAbsolute=function(d0m)
{if(this.parent.dom.getStyle(d0m,"position")!="absolute")
{var position=this.parent.dom.position(d0m,false,true);this.parent.dom.setStyle(d0m,{position:'absolute',left:position.x,top:position.y,cursor:this.cursor});}};this.absolutizeGroup=function()
{for(var i=0;i<this.options.elements.length;i++)
{if(this.parent.dom.getStyle(this.options.elements[i],"position")!="absolute")
{this.parent.dom.setStyle(this.options.elements[i],{position:'absolute',left:this.elementStart[i].x,top:this.elementStart[i].y,cursor:this.cursor});}}};this.absolutizeLink=function()
{for(var i=0;i<this.options.elements.length;i++)
{this.parent.dom.setStyle(this.options.elements[i],{cursor:this.cursor});}
for(i=0;i<this.linkRef.length;i++)
{if(this.parent.dom.getStyle(this.linkRef[i],"position")!="absolute")
{this.parent.dom.setStyle(this.linkRef[i],{position:'absolute',left:this.elementStart[i].x,top:this.elementStart[i].y});}}};this.launchEvents=function(event)
{if(event&&event.isArray===true)
{for(var i=0;i<event.length;i++)
{if(typeof event[i]=="function")
{event[i]();}}}
else
{return(event)?event():false;}};}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.drop.js",Name:"drop",Type:"module",Version:"0.1"},content:function(options){this.options=options||{};this.elements=[];this.selected=false;this.selID=false;this.lastSelected=false;this.make=function(options)
{return this;};this.register=function(data)
{var ev=data.events||{};data.events=ev;this.elements.push(data);return(this.elements.length-1);};this.unregister=function(index)
{this.elements[index]=null;};this.generateIndex=function()
{return this.elements.length;};this.capture=function(drag,StopOnAbsolute)
{this.drag=drag.currentElementDrag;var position=this.parent.dom.position(this.drag,false,StopOnAbsolute||false);position={x:position.x+(this.drag.clientWidth/2),y:position.y+(this.drag.clientHeight/2)};this.selected=false;for(var i=0;i<this.elements.length;i++)
{if(this.elements[i]!==null)
{var pt=this.parent.dom.positionRange(this.elements[i].element,StopOnAbsolute||false);if(position.x>pt.x1&&position.x<pt.x2&&position.y>pt.y1&&position.y<pt.y2)
{this.selected=i;break;}}}
if(this.selected===false)
{if(this.selID!==false)
{this.out(this.selID);}}
else
{if(this.selID!==false&&this.selID!==this.selected)
{this.out(this.selID);}
this.over(this.selected);}
this.lastSelected=(this.selected===false)?this.lastSelected:this.selected;};this.setArrayPositions=function(StopOnAbsolute)
{this.arrayPositions=[];for(var i=0;i<this.elements.length;i++)
{this.arrayPositions.push(this.parent.dom.positionRange(this.elements[i].element,StopOnAbsolute||false));}};this.captureFromArray=function(drag,Final,StopOnAbsolute)
{this.drag=drag.currentElementDrag;this.position={x:parseInt(this.drag.style.left),y:parseInt(this.drag.style.top)};this.selected=false;for(var i=0;i<this.arrayPositions.length;i++)
{var pt=this.arrayPositions[i];if(this.position.x>=pt.x1&&this.position.x<=pt.x2&&this.position.y>=pt.y1&&this.position.y<=pt.y2)
{this.selected=i;break;}}
this.lastSelected=(this.selected===false)?this.lastSelected:this.selected;if(this.selected===false)
{if(this.selID!==false)
{this.out(this.selID);}}
else
{if(this.selID!==false&&this.selID!==this.selected)
{this.out(this.selID);}
this.over(this.selected);}};this.over=function(uid)
{this.selID=uid;if(this.elements[uid]!==null)
{return this.launchEvents(this.elements[uid].events.over);}};this.out=function(uid)
{this.selID=false;if(this.elements[uid]!==null)
{return this.launchEvents(this.elements[uid].events.out);}};this.launchEvents=function(event)
{if(event&&event.isArray===true)
{for(var i=0;i<event.length;i++)
{if(typeof event[i]=="function")
{event[i]();}}}
else
{return(event)?event():false;}};this.expand(this);return this;}});
leimnud.Package.Public({info:{Class:"maborak",File:"module.dom.js",Name:"dom",Type:"module",Version:"0.1"},content:{button:function(label,go,style,prop)
{this.make=function(label,go,style,prop)
{this.button=(label&&label.tagName)?$(label):(new this.parent.module.dom.create("input",{className:"module_app_button___gray",type:"button",value:label||"Button"}.concat(prop||{}),style||{}));this.button.disable=function()
{this.button.disabled=true;this.button.className="module_app_buttonjs___gray module_app_buttonDisabled___gray";return this.button;}.extend(this);this.button.enable=function()
{this.button.disabled=false;this.button.className="module_app_buttonjs___gray";return this.button;}.extend(this);this.button.onmouseover=this.mouseover;this.button.onmouseout=this.mouseout;this.parent.dom.setStyle(this.button,style||{});if(typeof go==="function"){this.button.onmouseup=go.args(this.button);}
return this.button;};this.mouseover=function()
{if(this.button.disabled==true){return false;}
this.button.className="module_app_buttonjs___gray module_app_buttonHover___gray";return false;};this.mouseout=function()
{if(this.button.disabled==true){return false;}
this.button.className="module_app_buttonjs___gray";return false;};this.expand();return this.make(label,go,style,prop);},input:function(options)
{this.make=function(options)
{this.input=(options&&options.tagName)?$(options):(new this.parent.module.dom.create("input",{className:"module_app_input___gray",type:"text",value:options.label||"",maxLength:options.maxlength||"30"}.concat(options.properties||{}),(options.style||{})));this.input.disable=function()
{this.input.disabled=true;this.input.className=this.input.className+" module_app_inputDisabled___gray";return this.input;}.extend(this);this.input.enable=function()
{this.input.disabled=false;this.input.className=this.input.className.split(" ")[0];return this.input;}.extend(this);this.input.passed=function()
{if('\v'=='v')
this.input.className="module_app_inputPassed_ie___gray "+((this.input.className.split(' ')[1])||'');else
this.input.className="module_app_inputPassed___gray "+((this.input.className.split(' ')[1])||'');return this.input;}.extend(this);this.input.normal=function()
{this.input.className=this.input.className+" "+((this.input.className.split(' ')[1])||'');return this.input;}.extend(this);this.input.failed=function()
{if('\v'=='v')
this.input.className="module_app_inputFailed_ie___gray "+((this.input.className.split(' ')[1])||'');else
this.input.className="module_app_inputFailed___gray "+((this.input.className.split(' ')[1])||'');return this.input;}.extend(this);return this.input;};this.mouseover=function()
{this.input.className="module_app_input___gray module_app_inputHover___gray";return false;};this.mouseout=function()
{this.input.className="module_app_input___gray";return false;};this.expand();return this.make(options||{});},select:function(options)
{this.options={data:[],selected:0,properties:{},style:{}}.concat(options||{});this.make=function()
{this.select=new this.parent.module.dom.create("select",this.options.properties,this.options.style);this.select.className="module_app_select___gray";this.makeData();this.select.selected=function()
{return this.select.options[this.select.selectedIndex];}.extend(this);this.select.clear=function()
{var a=this.select.options;var b=a.length;for(var i=0;i<b;i++)
{a[0].parentNode.removeChild(a[0]);}}.extend(this);this.select.addOption=function(data)
{data={value:null,text:null,selected:false,key:false}.concat(data||{});var o=new Option(data.text,data.value,data.selected);if(data.key===false)
{this.select.append(o);}
else
{this.select.insertBefore(o,this.select.options[data.key]);this.select.selectedIndex=data.key;}}.extend(this);return this.select;};this.makeData=function(){var d=this.options.data;var j=d.length;for(var i=0;i<j;i++)
{this.select[i]=new Option(d[i].text,d[i].value,((this.options.selected===i)?true:false));}};this.expand();return this.make();},create:function(dom,properties,style)
{this.dom=$dce(dom);this.parent.dom.setProperties(this.dom,properties||{});this.parent.dom.setStyle(this.dom,style||{});return new this.parent.module.dom.methods(this.dom);},methods:function(dom)
{if(!dom){return false;}
if(dom.domed==true){return dom;}
this.dom=dom;this.dom.dom=this.dom;this.dom.domed=true;this.dom.replace=function(dom)
{if(!dom)
{return this.dom;}
else
{this.dom.parentNode.replaceChild(dom,this.dom);return dom;}}.extend(this);this.dom.before=function(dom)
{if(!dom)
{return this.dom;}
else
{this.dom.parentNode.insertBefore(dom,this.dom);return dom;}}.extend(this);this.dom.append=function()
{for(var i=0;i<arguments.length;i++)
{if(arguments[i])
{this.dom.appendChild(arguments[i]);}}
return this.dom;}.extend(this);this.dom.remove=function()
{this.dom.parentNode.removeChild(this.dom);}.extend(this);this.dom.opacity=function(o)
{this.parent.dom.opacity(this.dom,o);return this.dom;}.extend(this);this.dom.setStyle=function(style)
{this.parent.dom.setStyle(this.dom,style||{});return this.dom;}.extend(this);return this.dom;},radioByValue:function(param)
{var radio_name=$n(param.name)||false;var radio_value=param.value||false;for(var i=0;i<radio_name.length;i++)
{if(radio_name[i].value==radio_value)
{return radio_name[i];}}
return false;}}});
var $=function(el)
{var d=(typeof el=="string")?document.getElementById(el):el;return new leimnud.module.dom.methods(d);};var button=leimnud.module.dom.button;var input=leimnud.module.dom.input;var DOM=leimnud.module.dom.create;var panel=leimnud.module.panel;var select=leimnud.module.dom.select;
leimnud.Package.Public({info:{Class:"maborak",File:"module.dashboard.js",Name:"dashboard",Type:"module",Version:"0.1"},content:function(){this.elements={};this.make=function(options)
{this.options={drag:true,panel:[],data:[]}.concat(options||{});this.drop=new this.parent.module.drop();this.drop.make();var width=this.options.target.offsetWidth-50;this.columns=this.options.data.length;this.widthColumn=(width/this.columns);this.elements.column=[];this.elements.table=$dce('table');$(this.elements.table).setStyle({width:width,borderCollapse:'collapse'})
this.elements.tr=this.elements.table.insertRow(-1);this.options.target.append(this.elements.table);this.matriz=[];for(var i=0;i<this.columns;i++)
{this.elements.column[i]=this.elements.tr.insertCell(i);this.parent.dom.setStyle(this.elements.column[i],{width:width/this.columns,border:'0px solid red',verticalAlign:'top'});this.matriz.push([]);}
this.parseData();this.drop.setArrayPositions(true);};this.parseData=function()
{for(var i=0;i<this.columns;i++)
{var column=this.options.data[i];for(var j=0;j<column.length;j++)
{var wd=column[j];this.panel({target:this.elements.column[i],column:i,index:j}.concat(wd));this.matriz[i].push(j);}}};this.panel=function(options)
{options={style:{},titleBar:true}.concat(options||{});var _panel=new this.parent.module.panel();_panel.options={target:options.target,title:options.title||"",size:{w:(options.width||this.widthColumn),h:options.height||300},position:{x:0,y:0},titleBar:(options.titleBar||false),control:{resize:false,roll:false,setup:false,drag:this.options.drag,close:true},fx:{shadow:false,opacity:false}};_panel.setStyle={containerWindow:(options.style.containerWindow||{}).concat({position:'relative',border:"1px solid #afafaf",margin:3}),content:(options.style.content||{}).concat({overflow:"hidden",margin:0,borderLeftWidth:0,borderRightWidth:0,borderBottomWidth:0}),titleBar:(options.style.titleBar||{}).concat({backgroundImage:"url("+this.parent.info.images+"grid.title.gray.gif)",height:16,backgroundPosition:"0pt -5px"}),title:(options.style.title||{}).concat({padding:1,fontWeight:"normal"}),roll:(options.style.roll||{}).concat({top:1}),close:(options.style.close||{}).concat({top:1}),setup:(options.style.setup||{}).concat({top:1})};if(options.noBg)
{_panel.setStyle.content.concat({backgroundColor:"#DFDFDF",borderWidth:0});_panel.setStyle.containerWindow.concat({backgroundColor:"#DFDFDF"});_panel.setStyle.frontend={backgroundColor:"#DFDFDF"};}
_panel.events={roll:function(){return this.drop.setArrayPositions(true);}.extend(this),init:[function(i){if(this.lock===true||this.moving==true){return false;}
var e=this.options.panel[i].panel.elements.containerWindow;var p;this.currentPhantom=p=new DOM("div",false,{width:e.clientWidth,height:e.clientHeight,border:"1px dashed red",position:"relative",margin:3});if(e.nextSibling)
{e.parentNode.insertBefore(p,e.nextSibling);}
else
{e.parentNode.appendChild(p);}}.extend(this,this.options.panel.length)],move:function(i){var e=this.options.panel[i].panel.elements.containerWindow;var h=this.drop.selected;this.drop.captureFromArray({currentElementDrag:e});if(this.drop.selected!==false&&this.drop.selected!==h)
{var f=this.drop.elements[this.drop.selected].element;this.currentPhantom.remove();var p;this.currentPhantom=p=new DOM("div",false,{width:e.clientWidth,height:e.clientHeight,border:"1px dashed red",position:"relative",margin:3});if(f.nextSibling)
{f.parentNode.insertBefore(p,f.nextSibling);}
else
{f.parentNode.appendChild(p);}}
this.de=this.drop.selected;}.extend(this,this.options.panel.length),finish:function(i){if(this.lock===true&&this.moving==true){return false;}
this.lock=true;this.moving=true;var p=this.parent.dom.positionRange(this.currentPhantom,true);var e=this.options.panel[i].panel.elements.containerWindow;new this.parent.module.fx.algorithm().make({duration:400,end:[p.x1,p.y1],transition:"sineOut",begin:[parseInt(e.style.left),parseInt(e.style.top)],onTransition:function(fx,dom)
{dom.style.left=fx.result[0];dom.style.top=fx.result[1];}.extend(this,e),onFinish:function(fx,dom,finish)
{var e=dom;dom.style.left=fx.options.end[0];dom.style.top=fx.options.end[1];try{this.currentPhantom.parentNode.replaceChild(e,this.currentPhantom);}catch(e){}
this.parent.dom.setStyle(e,{left:"auto",top:"auto",position:"relative"});this.drop.setArrayPositions(true);this.lock=false;this.moving=false;if(this.drop.selected!==false)
{var inp=this.drop.elements[this.drop.selected].value;}}.extend(this,e)});}.extend(this,this.options.panel.length)};_panel.events.remove=function(){_panel.cancelClose=true;new leimnud.module.app.confirm().make({label:G_STRINGS.ID_CONFIRM_REMOVE_DASHBOARD,action:function(){removeDashboard(options['class'],options['type'],options['element']);return true;}.extend(this),cancel:function(){_panel.cancelClose=true;_panel.inRemove=false;return false;}});};_panel.make();if(options.url)
{_panel.open({url:options.url,proxy:false});}
if(options.image)
{_panel.open({image:options.image,proxy:false});}
this.options.panel.push({panel:_panel,index:this.options.panel.length-1,column:options.column});this.drop.register({element:_panel.elements.containerWindow,value:this.options.panel.length-1});return _panel;};this.expand(this);}});
var cases=function()
{this.parent=leimnud;this.panels={};this.make=function(options)
{this.options.target=this.parent.dom.element(this.options.target);var panel;this.panels.list=panel=new leimnud.module.panel();panel.options={size:{w:310,h:250},position:{x:50,y:50},title:"List",theme:"processmaker",target:this.options.target,statusBar:true,limit:true,control:{resize:false,close:true,roll:false},fx:{opacity:true,rollWidth:150,fadeIn:false}};this.panels.step=panel=new this.parent.module.panel();this.panels.step.options={size:{w:260,h:550},title:this.options.title,target:this.options.target,cursorToDrag:"move",position:{x:5,y:5},limit:true,fx:{shadow:false,modal:false,opacity:false}};this.panels.step.setStyle={statusBar:{}};this.panels.step.styles.fx.opacityModal.Static=0;this.panels.step.make();this.panels.step.events={remove:[function(){delete(this.panels.step);}.extend(this)]};panel.events.remove.push(function()
{var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"showWindow=false"});r.make();}.extend(this));panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action="+this.options.action+"&showWindow="+this.options.action});r.callback=function(rpc){this.panels.step.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();this.panels.step.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);r.make();}
this.expand(this);};
var oLeyendsPanel;var showInformation=function()
{if(Cse.panels.step)
Cse.panels.step.remove();Cse.panels.step=new leimnud.module.panel();Cse.panels.step.options={title:G_STRINGS.ID_INFORMATION,size:{w:260,h:450},position:{x:0,y:30,left:true},control:{close:true,resize:true},fx:{modal:false},statusBar:false,fx:{shadow:true,modal:false}}
Cse.panels.step.make();Cse.panels.step.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=information&showWindow=information"});oRPC.callback=function(rpc){Cse.panels.step.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();Cse.panels.step.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showActions=function()
{if(Cse.panels.step)
Cse.panels.step.remove();Cse.panels.step=new leimnud.module.panel();Cse.panels.step.options={title:G_STRINGS.ID_ACTIONS,size:{w:260,h:450},position:{x:0,y:30,left:true},control:{close:true,resize:true},fx:{modal:false},statusBar:false,fx:{shadow:true,modal:false}}
Cse.panels.step.make();Cse.panels.step.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=actions&showWindow=actions"});oRPC.callback=function(rpc){Cse.panels.step.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();Cse.panels.step.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showProcessMap=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:(document.body.clientWidth*95)/100,h:(document.body.clientHeight*95)/100},position:{x:0,y:0,center:true},title:G_STRINGS.ID_PROCESS_MAP,theme:"processmaker",statusBar:false,control:{resize:false,roll:false,drag:false},fx:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:false}};oPanel.events={remove:function(){oLeyendsPanel.remove();delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=showProcessMap"});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();oLeyendsPanel=new leimnud.module.panel();oLeyendsPanel.options={size:{w:160,h:155},position:{x:((document.body.clientWidth*95)/100)-((document.body.clientWidth*95)/100-(((document.body.clientWidth*95)/100)-160)),y:45,center:false},title:G_STRINGS.ID_COLOR_LEYENDS,theme:"processmaker",statusBar:false,control:{resize:false,roll:false,drag:true,close:false},fx:{modal:false,opacity:false,blinkToFront:true,fadeIn:false,drag:false}};oLeyendsPanel.setStyle={content:{overflow:'hidden'}};oLeyendsPanel.events={remove:function(){delete(oLeyendsPanel);}.extend(this)};oLeyendsPanel.make();oLeyendsPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=showLeyends"});oRPC.callback=function(rpc){oLeyendsPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oLeyendsPanel.addContent(rpc.xmlhttp.responseText);}.extend(this);oRPC.make();}.extend(this);oRPC.make();};var showProcessInformation=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:450,h:260},position:{x:0,y:0,center:true},title:G_STRINGS.ID_PROCESS_INFORMATION,theme:"processmaker",statusBar:false,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=showProcessInformation"});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showTransferHistory=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:900,h:520},position:{x:0,y:0,center:true},title:G_STRINGS.ID_CASE_HISTORY,theme:"processmaker",statusBar:false,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=showTransferHistory"});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};function dynaformHistory(PRO_UID,APP_UID,TAS_UID,DYN_UID)
{if(!DYN_UID)DYN_UID="";oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:900,h:520},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showDynaformListHistory&PRO_UID='+PRO_UID+'&APP_UID='+APP_UID+'&TAS_UID='+TAS_UID+'&DYN_UID='+DYN_UID});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
function toggleTable(tablename){table=getElementByName(tablename);if(table.style.display==''){table.style.display='none';}else{table.style.display='';}}
var showTaskInformation=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:450,h:322},position:{x:0,y:0,center:true},title:G_STRINGS.ID_TASK_INFORMATION,theme:"processmaker",statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=showTaskInformation"});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var cancelCase=function()
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_CONFIRM_CANCEL_CASE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=cancelCase'});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();}.extend(this)});};var reactivateCase=function()
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_REACTIVATE_CASES,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=reactivateCase'});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();}.extend(this)});};var pausecasePanel;var pauseCase=function(){var oPauseDiv=document.getElementById('pausediv');document.getElementById('spause').style.display='none';document.getElementById('scpause').style.display='block';oPauseDiv.style.display='block';var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showPauseCaseInput'});oRPC.callback=function(rpc){oPauseDiv.innerHTML=rpc.xmlhttp.responseText;}.extend(this);oRPC.make();};function cancelPauseCase(){document.getElementById('spause').style.display='block';document.getElementById('scpause').style.display='none';document.getElementById('pausediv').style.display='none';}
function toPause()
{unpausedate=document.getElementById('form[unpause_date]').value;if(unpausedate.trim()==''){msgBox(G_STRINGS.ID_CONFIRM_PAUSE_CASE_ALERT,'alert');return 0;}
new leimnud.module.app.confirm().make({label:G_STRINGS.ID_CONFIRM_PAUSE_CASE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=pauseCase&unpausedate='+unpausedate});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();}.extend(this)});}
var deleteCase=function(sApplicationUID)
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_CONFIRM_DELETE_CASE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=deleteCase&sApplicationUID='+sApplicationUID});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();}.extend(this)});};var unpauseCase=function()
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_CONFIRM_UNPAUSE_CASE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=unpauseCase'});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();}.extend(this)});};var view_reassignCase=function()
{var panel=new leimnud.module.panel();panel.options={size:{w:450,h:450},position:{x:50,y:50,center:true},title:'',control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:"cases_Ajax",args:"action=view_reassignCase"});r.callback=function(rpc)
{panel.loader.hide();panel.addContent(rpc.xmlhttp.responseText);var scs=rpc.xmlhttp.responseText.extractScript();scs.evalScript();};r.make();};var reassignCase=function(USR_UID,THETYPE)
{var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=reassignCase'+'&USR_UID='+USR_UID+'&THETYPE='+THETYPE});oRPC.callback=function(oRPC){window.location='casesListExtJs';}.extend(this);oRPC.make();};var adhocAssignmentUsers=function(){oPanel=new leimnud.module.panel();oPanel.options={size:{w:450,h:450},position:{x:0,y:0,center:true},title:'',theme:"processmaker",statusBar:false,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=adhocAssignmentUsers'});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showUploadedDocuments=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:700,h:350},position:{x:0,y:0,center:true},title:G_STRINGS.ID_UPLOADED_DOCUMENTS,theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showUploadedDocuments'});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showUploadedDocument=function(APP_DOC_UID){oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:300,h:300},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showUploadedDocument&APP_DOC_UID='+APP_DOC_UID});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showGeneratedDocuments=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:700,h:350},position:{x:0,y:0,center:true},title:G_STRINGS.ID_GENERATED_DOCUMENTS,theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showGeneratedDocuments'});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showGeneratedDocument=function(APP_DOC_UID){oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:300,h:250},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showGeneratedDocument&APP_DOC_UID='+APP_DOC_UID});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var showDynaforms=function(){oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:400,h:300},position:{x:0,y:0,center:true},title:G_STRINGS.ID_DYNAFORMS,theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showDynaformList'});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};function showDynaform(DYN_UID)
{oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:800,h:600},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:false,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();var iframe=document.createElement('iframe');iframe.setAttribute('id','dynaFormIframe');iframe.src='cases_Ajax?action=showDynaform&DYN_UID='+DYN_UID;iframe.style.border='0px';iframe.style.width='790';iframe.style.height=_client.height-20;oPanel2.addContent(iframe);}
function showDynaformHistory(DYN_UID,HISTORY_ID)
{oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:550,h:400},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showDynaformHistory&DYN_UID='+DYN_UID+'&HISTORY_ID='+HISTORY_ID});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
var messagesListPanel;var showHistoryMessages=function()
{oPanel=new leimnud.module.panel();oPanel.options={size:{w:800,h:420},position:{x:0,y:0,center:true},title:G_STRINGS.ID_HISTORY_MESSAGE_CASE,theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showHistoryMessages'});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();messagesListPanel=oPanel;};function showHistoryMessage(APP_UID,APP_MSG_UID)
{oPanel2=new leimnud.module.panel();oPanel2.options={size:{w:600,h:400},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:true,control:{resize:false,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel2.events={remove:function(){delete(oPanel2);}.extend(this)};oPanel2.make();oPanel2.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showHistoryMessage&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID});oRPC.callback=function(rpc){oPanel2.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel2.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
var deleteUploadedDocument=function(APP_DOC_UID){new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=deleteUploadedDocument&DOC='+APP_DOC_UID});oRPC.callback=function(oRPC){if((window.location.href.indexOf('/cases/cases_Step')>-1)&&(window.location.href.indexOf('?TYPE=INPUT_DOCUMENT&UID=')>-1)&&(window.location.href.indexOf('&ACTION=VIEW&')>-1)&&(window.location.href.indexOf('&DOC='+APP_DOC_UID)>-1)){window.location=getField('DYN_FORWARD');}
else{cases_AllInputdocsList.refresh();}}.extend(this);oRPC.make();}.extend(this)});};var deleteGeneratedDocument=function(APP_DOC_UID){new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=deleteGeneratedDocument&DOC='+APP_DOC_UID});oRPC.callback=function(oRPC){if((window.location.href.indexOf('/cases/cases_Step')>-1)&&(window.location.href.indexOf('?TYPE=OUTPUT_DOCUMENT&UID=')>-1)&&(window.location.href.indexOf('&ACTION=VIEW&')>-1)&&(window.location.href.indexOf('&DOC='+APP_DOC_UID)>-1)){window.location=getField('DYN_FORWARD');}
else{cases_AllOutputdocsList.refresh();}}.extend(this);oRPC.make();}.extend(this)});};var resendMessage=function(APP_UID,APP_MSG_UID)
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_RESENDMSG,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=resendMessage&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID});oRPC.callback=function(rpc){messagesListPanel.clearContent();messagesListPanel.loader.show();var oRPC2=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showHistoryMessages'});oRPC2.callback=function(rpc){messagesListPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();messagesListPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC2.make();}.extend(this);oRPC.make();}.extend(this)});};function showdebug()
{if(typeof parent!='undefined'&&typeof parent.parent!='undefined'){if(typeof parent.parent.Ext!='undefined'){if(!parent.parent.PANEL_EAST_OPEN){var debugPanel=parent.parent.Ext.getCmp('debugPanel');parent.parent.PANEL_EAST_OPEN=true;debugPanel.show();debugPanel.ownerCt.doLayout();debugPanel.expand();}
parent.parent.propStore.load();parent.parent.triggerStore.load();}}}
var uploadInputDocument=function(docID,appDocId,docVersion,actionType){if(actionType){if(actionType=="R"){windowTitle=G_STRINGS.ID_UPLOAD_REPLACE_INPUT;}
if(actionType=="NV"){windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT_VERSION;}}else{windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT;docVersion=1;actionType="";appDocId="";}
oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:300},position:{x:0,y:0,center:true},title:windowTitle,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:"action=uploadInputDocument&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var uploadToReviseInputDocument=function(docID,appDocId,docVersion,actionType){if(actionType){if(actionType=="R"){windowTitle=G_STRINGS.ID_UPLOAD_REPLACE_INPUT;}
if(actionType=="NV"){windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT_VERSION;}}else{windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT;docVersion=1;actionType="";}
oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:300},position:{x:0,y:0,center:true},title:windowTitle,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:"action=uploadToReviseInputDocument&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var inputDocumentVersionHistory=function(docID,appDocId){oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:400},position:{x:0,y:0,center:true},title:G_STRINGS.ID_INPUT_DOCUMENT_HISTORY,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:"action=inputDocumentVersionHistory&docID="+docID+"&appDocId="+appDocId});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};
var PROCESSMAP_STEP_EDIT=false;var PROCESSMAP_USER_EDIT=false;var processmapOutputsPanel;var _client=getClientWindowSize();var processmap=function(){this.data={load:function()
{var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=load&data="+{uid:this.options.uid,mode:this.options.rw,ct:this.options.ct}.toJSONString()});r.callback=this.data.render.base
r.make();},render:{buildingBlocks:{injector:function(lanzado)
{Wx=(lanzado=='dynaforms'||lanzado=='triggers'||lanzado=='outputs')?600:500;Hx=460;if(lanzado=="reportTables"){Wx=_client.width-30;Hx=_client.height-15;}
var bbk={dynaforms:1,messages:1,inputs:1,outputs:1,webbots:1};this.observers.menu.update();if(!this.panels.buildingBlocks)
{this.panels.buildingBlocks=new leimnud.module.panel();this.panels.buildingBlocks.options={limit:true,size:{w:Wx,h:Hx},position:{x:0,y:10,center:true},title:"",theme:"processmaker",statusBar:false,control:{drag:false,resize:false,close:true,drag:true},fx:{opacity:false,rolled:false,modal:true,drag:true}};this.panels.buildingBlocks.make();this.panels.buildingBlocks.events={remove:function()
{delete this.panels.buildingBlocks;}.extend(this)};}
else
{this.panels.buildingBlocks.clearContent();}
var bbk={outputs:function(){var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_OUTPUT_DOCUMENTS)
panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=outputs&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this);r.make();processmapOutputsPanel=panel;}.extend(this),inputs:function()
{var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_INPUT_DOCUMENTS);panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=inputs&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this);r.make();}.extend(this),triggers:function(){var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_TRIGGERS);panel.clearContent();panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=triggers&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this);r.make();}.extend(this),messages:function(){var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_MESSAGES);panel.clearContent();panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=messages&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this);r.make();}.extend(this),reportTables2:function(){var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_REPORT_TABLES);panel.clearContent();panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=reportTables&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this);r.make();}.extend(this),reportTables:function(){var url="../pmTables?PRO_UID="+this.options.uid;var isIE=(navigator.userAgent.toLowerCase().indexOf("msie")!=-1)?1:0;if(isIE==1){this.panels.buildingBlocks.remove();var w=screen.width-150;var h=screen.height-300;var windowAux=window.open(url,"reportTable","width="+w+", height="+h+", resizable=no, toolbar=no, menubar=no, scrollbars=yes, status=no, location=no, left="+((screen.width/2)-(w/2))+", top="+((screen.height/2)-(h/2)+50));}else{var panel=this.panels.buildingBlocks;panel.addContentTitle("");panel.clearContent();var iframe=document.createElement("iframe");iframe.setAttribute("id","reportTablesIframe");iframe.src=url;iframe.frameBorder=0;iframe.style.width=_client.width-40;iframe.style.height=_client.height-70;panel.addContent(iframe);}}.extend(this),dynaforms:function(){var panel=this.panels.buildingBlocks;panel.addContentTitle(G_STRINGS.ID_DYNAFORMS);panel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=dynaforms&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc){panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();this.buildLoading=false;}.extend(this)
r.make();}.extend(this)};bbk[lanzado]();},panel:function()
{var panel;panel=this.panels.toolbar=new leimnud.module.panel();this.panels.toolbar.options={limit:true,size:{w:220,h:31},position:{x:this.options.target.clientWidth-278,y:4},title:"",theme:"processmaker",target:this.options.target,titleBar:false,statusBar:false,elementToDrag:"content",cursorToDrag:"default",control:{drag:true,resize:false},fx:{opacity:true,shadow:false}};panel.setStyle={containerWindow:{border:"1px solid buttonshadow"},frontend:{backgroundColor:"buttonface"},content:{border:"1px solid transparent",backgroundColor:"transparent",margin:0,overflow:"hidden",padding:1}};this.panels.toolbar.make();var div=document.createElement("div");this.parent.dom.setStyle(div,{textAlign:"center"});var dr1=document.createElement("img");dr1.src=this.options.images_dir+"0.gif";dr1.title=_("ID_PROCESSMAP_SEQUENTIAL");div.appendChild(dr1);var dr2=document.createElement("img");dr2.src=this.options.images_dir+"1.gif";dr2.title=_("ID_PROCESSMAP_SELECTION");div.appendChild(dr2);var dr3=document.createElement("img");dr3.src=this.options.images_dir+"2.gif";dr3.title=_("ID_PROCESSMAP_EVALUATION");div.appendChild(dr3);var dr4=document.createElement("img");dr4.src=this.options.images_dir+"3.gif";dr4.title=_("ID_PROCESSMAP_PARALLEL_FORK");div.appendChild(dr4);var dr5=document.createElement("img");dr5.src=this.options.images_dir+"4.gif";dr5.title=_("ID_PROCESSMAP_PARALLEL_EVALUATION_FORK");div.appendChild(dr5);var dr6=document.createElement("img");dr6.src=this.options.images_dir+"5.gif";dr6.title=_("ID_PROCESSMAP_PARALLEL_JOIN");div.appendChild(dr6);var fin=document.createElement("img");fin.src=this.options.images_dir+"6.gif";fin.title=_("ID_END_OF_PROCESS");div.appendChild(fin);var ini=document.createElement("img");ini.src=this.options.images_dir+"7.gif";ini.title=_("ID_START_TASK");div.appendChild(ini);[dr1,dr2,dr3,dr4,dr5,dr6,fin,ini].map(function(el){el.className="processmap_toolbarItem___"+this.options.theme}.extend(this));this.dragables.derivation=new this.parent.module.drag({elements:[dr1,dr2,dr3,dr4,dr5,dr6,fin,ini],fx:{type:"clone",target:this.panels.editor.elements.content,zIndex:11}});this.dragables.derivation.typesDerivation=["simple","double","conditional","conditional1","conditional2","conditional3","final","initial"];this.dragables.derivation.events={init:[function(){this.dragables.derivation.noDrag=true;}.extend(this)],move:this.dropables.derivation.capture.args(this.dragables.derivation),finish:this.parent.closure({instance:this,method:function(){this.parent.dom.remove(this.dropables.derivation.drag||this.dragables.derivation.currentElementDrag);this.parent.dom.remove(this.dragables.derivation.currentElementDrag);if(this.dropables.derivation.selected!==false)
{this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);vAux=this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.click);this.dropables.derivation.selected=false;return vAux;}
else
{this.dragables.derivation.noDrag=false;}}})};this.dragables.derivation.make();this.parent.dom.setStyle([dr1,dr2,dr3,dr4,dr5,dr6,fin,ini],{cursor:"move"});panel.loader.hide();panel.addContent(div);leimnud._panel=['O'],leimnud.ipanel=0;},components:{}},base:function(xml)
{this.panels.editor.loader.hide();this.data.db=xml.xmlhttp.responseText.parseJSON().concat({});this.data.db.subprocess=[];this.panels.editor.addContentStatus(G_STRINGS.ID_PROCESSMAP_LOADING);if(this.options.rw===true)
{this.menu=new this.parent.module.app.menuRight();this.menu.make({target:this.panels.editor.elements.content,width:201,theme:this.options.theme,menu:[{image:"/images/edit.gif",text:G_STRINGS.ID_PROCESSMAP_EDIT_PROCESS,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:700,h:520},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_EDIT_PROCESS+": "+moldTitle(this.data.db.title.label,700),theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=process_Edit&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/edit.gif",text:G_STRINGS.ID_PROCESSMAP_EXPORT_PROCESS,launch:function(event){this.tmp.exportProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:600,h:230},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_EXPORT_PROCESS+": "+moldTitle(this.data.db.title.label,600),theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=process_Export&processMap=1&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{separator:true},{image:"/images/add.png",text:G_STRINGS.ID_PROCESSMAP_ADD_TASK,launch:this.addTask.extend(this,{tp:'task'})},{image:"/images/subProcess.png",text:G_STRINGS.ID_PROCESSMAP_ADD_SUBPROCESS,launch:this.addTask.extend(this,{tp:'subprocess'})},{image:"/images/addtext.png",text:G_STRINGS.ID_PROCESSMAP_ADD_TEXT,launch:this.addText.extend(this)},{image:"/images/linhori.png",text:G_STRINGS.ID_PROCESSMAP_HORIZONTAL_LINE,launch:this.addGuide.extend(this,"horizontal")},{image:"/images/linver.png",text:G_STRINGS.ID_PROCESSMAP_VERTICAL_LINE,launch:this.addGuide.extend(this,"vertical")},{image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_ALL_LINES,launch:function(event,index){index=this.parent.browser.isIE?event:index;new leimnud.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_ALL_LINES,action:function()
{for(var i=0;i<this.data.db.guide.length;i++)
{this.parent.dom.remove(this.data.db.guide[i].object.elements.guide);}
var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteGuides&data="+{pro_uid:this.options.uid}.toJSONString()});r.make();}.extend(this)});}.extend(this)},{separator:true},{image:"/images/object_permission.gif",text:G_STRINGS.ID_OBJECT_PERMISSIONS,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:850,h:480},position:{x:50,y:50,center:true},title:G_STRINGS.ID_OBJECT_PERMISSIONS,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=objectPermissions&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_PSUPERVISORS,submenu:[{image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_PROCESS_SUPERVISORS,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:300},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_PROCESS_SUPERVISORS,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=process_User&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/dynaforms.gif",text:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_DYNAFORMS,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_DYNAFORMS,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=supervisorDynaforms&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/inputdocument.gif",text:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_INPUTS,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_INPUTS,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=supervisorInputs&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)}]},{separator:true},{image:"/images/dynaforms.gif",text:G_STRINGS.ID_WEB_ENTRY,launch:function(event){this.tmp.editProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_WEB_ENTRY,theme:this.options.theme,control:{close:true,resize:true},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=webEntry&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER,submenu:[{image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER_PROPERTIES,launch:function(event){this.tmp.caseTrackerPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:300,h:180},position:{x:50,y:50,center:true},title:G_STRINGS.ID_CASE_TRACKER,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=caseTracker&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER_OBJECTS,launch:function(event){this.tmp.caseTrackerPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_CASE_TRACKER_OBJECTS,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=caseTrackerObjects&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)}]},{image:"/images/folder.gif",text:G_STRINGS.ID_PROCESS_FILES_MANAGER,launch:function(event){this.tmp.processFilesManagerPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:500,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESS_FILES_MANAGER,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=processFilesManager&data="+{pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/events.gif",text:G_STRINGS.ID_EVENTS,submenu:[{image:"/images/event_messageThrow.png",text:G_STRINGS.ID_INTERMEDIATE_MESSAGE_EVENT,launch:function(event){this.tmp.eventsPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:700,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_EVENT_MESSAGE,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=events&data="+{pro_uid:this.options.uid,type:"message"}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/event_conditional.png",text:G_STRINGS.ID_INTERMEDIATE_CONDITIONAL_EVENT,launch:function(event){this.tmp.eventsPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:700,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_EVENT_MESSAGE,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=events&data="+{pro_uid:this.options.uid,type:"conditional"}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)},{image:"/images/event_timer.png",text:G_STRINGS.ID_INTERMEDIATE_TIMER_EVENT,launch:function(event){this.tmp.eventsPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:700,h:380},position:{x:50,y:50,center:true},title:G_STRINGS.ID_EVENT_MULTIPLE,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{shadow:true,modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=events&data="+{pro_uid:this.options.uid,type:"multiple"}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this)}]}]});this.observers.menu.register(this.parent.closure({instance:this.menu,method:this.menu.remove}),this.menu);}
this.data.render.task();if(maximunX>this.options.size.w&&document.getElementById('pm_separator_div')){var pm_separator_div=document.getElementById('pm_separator_div');pm_separator_div.style.width=maximunX+200;}},task:function()
{var lngt=this.data.db.task.length;for(var i=0;i<lngt;i++)
{var tt=((this.data.db.task[i].task_type==='NORMAL')||(this.data.db.task[i].task_type==='ADHOC'))?'task':'subprocess';if(this.data.db.task[i].task_type==='HIDDEN'){tt='hidden';}
this.data.build.task(i,{tp:tt});}
this.data.render.taskINI();this.data.render.guide();},taskINI:function()
{var lngt=this.data.db.task.length;for(var i=0;i<lngt;i++)
{var task=this.data.db.task[i];if(task.taskINI===true)
{this.parent.dom.setStyle(task.object.elements.init,{background:"url("+this.options.images_dir+"inicio.gif)"});}}
return true;},setTaskINI:function(option)
{var task=this.data.db.task[this.tools.getIndexOfUid(option.task)];task.taskINI=option.value;this.parent.dom.setStyle(task.object.elements.init,{background:((task.taskINI===true)?"url("+this.options.images_dir+"inicio.gif)":"")});},guide:function()
{for(var i=0;i<this.data.db.guide.length;i++)
{this.data.build.guide(i);}
this.data.render.title();},title:function()
{this.data.build.title();this.data.render.text();},deleteDerivation:function(uid,rec,spec)
{var task=this.data.db.task[this.tools.getIndexOfUid(uid)];spec=(typeof spec!=="number")?false:spec;var deri=task.derivation;for(var i=0;i<deri.to.length;i++)
{if(spec===false||(spec!==false&&spec===i))
{if(deri.to[i].task==="-1"||deri.to[i].task==="-2")
{this.parent.dom.setStyle(task.object.elements[(deri.to.length>1)?'derivationBottom':'derivation'],{background:""});}
else
{deri.to[i].object.line.remove();this.observers.lineas.unregister(deri.to[i].object.indexObserver);}
if(deri.type===5||deri.type===8)
{var toTask=this.data.db.task[this.tools.getIndexOfUid(deri.to[i].task)];if(typeof(toTask)!='undefined'){toTask.object.inJoin=toTask.object.inJoin-1;if(toTask.object.inJoin===0)
{this.parent.dom.setStyle(toTask.object.elements.init,{backgroundPosition:"0 0",background:""});}}}}}
this.parent.dom.setStyle(task.object.elements.derivation,{background:""});task.derivation={to:[]};if(rec)
{var tdb=this.data.db.task;for(var i=0;i<tdb.length;i++)
{var der=tdb[i].derivation.to||[];for(var j=0;j<der.length;j++)
{if(der[j].task===uid)
{this.data.render.deleteDerivation(tdb[i].uid,false,j);}}}}},preDerivation:function(uid)
{var tmS;var typeDerivation=this.dragables.derivation.currentElementInArray;if(typeDerivation===6){var vars=this.data.db.task[uid];var vtd={type:0,tas_uid:vars.uid,pro_uid:this.options.uid,data:["-1"],next_task:'-1'}
this.data.build.derivation(vtd);vtd['delete']=true;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveNewPattern&data="+vtd.toJSONString()});r.make();this.inDerivationDrag=false;this.dragables.derivation.noDrag=false;return false;}
else if(typeDerivation===7){var vars=this.data.db.task[uid];if(vars.task_type!='SUBPROCESS'){this.data.render.setTaskINI({task:vars.uid,value:true});this.inDerivationDrag=false;this.dragables.derivation.noDrag=false;var r=new leimnud.module.rpc.xmlhttp({url:"../tasks/tasks_Ajax",args:"function=saveTaskData&oData="+{TAS_START:"TRUE",TAS_UID:vars.uid}.toJSONString()});r.make();}
else{this.inDerivationDrag=false;this.dragables.derivation.noDrag=false;}
return false;}
this.observers.menu.update();tmS=this.derivationArrowToDrop=document.createElement("div");this.parent.dom.setStyle(tmS,{position:"absolute",width:10,height:10,zIndex:12,overflow:"hidden",backgroundColor:"red"});this.panels.editor.elements.content.appendChild(tmS);var ln;ln=this.derivationLineToDrop=new this.parent.module.app.line({elements:[this.data.db.task[uid].object.elements.task,tmS],target:this.panels.editor.elements.content,color:"green",zIndex:15});ln.make();this.observers.lineas.register(this.parent.closure({instance:ln,method:ln.update}),ln);this.parent.event.add(this.data.db.task[uid].object.elements.task,"mouseover",this.parent.closure({instance:this,method:function(evt,arrow,lin,evi)
{var ec=this.parent.dom.position(this.panels.editor.elements.content);var mou=this.parent.dom.mouse(window.event||evt);this.parent.dom.setStyle(arrow,{left:mou.x-(ec.x+6),top:mou.y-(ec.y+6)});this.parent.exec(lin.update,false,false,lin);this.parent.event.flushCollection([evi]);},event:true,args:[tmS,ln,this.parent.event.db.length]}));if(this.parent.browser.isIE){this.data.db.task[uid].object.elements.task.fireEvent("onmouseover");}
var uidEventMMove=this.parent.event.db.length;this.parent.event.add(this.panels.editor.elements.content,"mousemove",function(evt,arrow,lin)
{var ec=this.parent.dom.position(this.panels.editor.elements.content);var mou=this.parent.dom.mouse(window.event||evt);this.parent.dom.setStyle(arrow,{left:(mou.x-(ec.x+6)+(this.panels.editor.elements.content.scrollLeft||0)),top:(mou.y-(ec.y+6)+(this.panels.editor.elements.content.scrollTop||0))});lin.update();this.parent.exec(this.dropables.derivation.capture,{currentElementDrag:arrow},false,this.dropables.derivation);}.extend(this,tmS,ln));this.parent.event.add(tmS,"click",function(evt,options)
{this.dropables.derivation.capture({currentElementDrag:options.arrow});this.dragables.derivation.noDrag=false;if(this.dropables.derivation.selected===false)
{options.line.remove();}
else
{options.line.remove();this.patternPanel(false,options.uid,{to:this.dropables.derivation.selected,type:this.dragables.derivation.currentElementInArray});if(this.dropables.derivation.elements[this.dropables.derivation.selected])
{this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);}}
this.inDerivationDrag=false;this.dropables.derivation.selected=false;this.parent.event.flushCollection([options.ue,options.ua]);this.parent.dom.remove(options.arrow);}.extend(this,{uid:uid,arrow:tmS,line:ln,ue:this.parent.event.db.length,ua:uidEventMMove}));},derivation:function(uid,type)
{for(var i=0;i<this.data.db.task.length;i++)
{this.data.render.lineDerivation(i);}
return true;},lineDerivation:function(index)
{var task=this.data.db.task[index];for(var j=0;j<task.derivation.to.length;j++)
{var derivation=task.derivation.to[j];if(derivation.task==="-1"||derivation.task==="-2")
{var target=(task.derivation.to.length>1)?'derivationBottom':'derivation';this.parent.dom.setStyle(task.object.elements[target],{background:"url("+this.options.images_dir+derivation.task+((target=="derivationBottom")?"bb.jpg":".gif")+"?aa="+Math.random()+")"});}
else
{var uid=this.tools.getIndexOfUid(derivation.task);var taskF=task.object.elements;var taskT=this.data.db.task[uid].object.elements;var from=task.object.elements.derivation;var toTask=this.data.db.task[uid];var to=toTask.object.elements.task;if(task.derivation.type===8||task.derivation.type===5)
{var ij=toTask.object.inJoin;ij=(ij)?ij+1:1;toTask.object.inJoin=ij;this.parent.dom.setStyle(toTask.object.elements.init,{background:"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")",backgroundPosition:"2 0",backgroundRepeat:"no-repeat"});}
else
{this.parent.dom.setStyle(task.object.elements.derivation,{background:"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")"});}
var line=new this.parent.module.app.line({indexRootSize:30,indexRootLastSize:35,elements:[taskF.task,taskT.task],envolve:[[taskF.task],[]],target:this.panels.editor.elements.content,color:"#228AB0",startA:50,zIndex:5});line.make();var cE=this.observers.lineas.register(line.update,line);derivation.object={line:line,indexObserver:cE};}}},text:function()
{var lngt=this.data.db.text.length;for(var i=0;i<lngt;i++)
{this.data.build.text(i);}
this.panels.editor.clearContentStatus();this.data.render.derivation();}},build:{task:function(index,options_task)
{options_task={tp:'task'}.concat(options_task||{});var options=this.data.db['task'][index];if(this.options.rw){options.color=((options_task.tp==='task')?"auto":"#9B88CA");if(options_task.tp==='hidden'){options.color="transparent";}}
var db=this.data.db,task=db.task[index];var derivation=task.derivation.to;var a=document.createElement("div");a.className="processmap_task___"+this.options.theme;this.parent.dom.setStyle(a,{top:options.position.y,left:options.position.x,cursor:((this.options.rw===true)?"move":"default"),backgroundColor:(options.color?options.color:'auto')});if(options.color=='#9B88CA'){var subp=((options_task.tp==='task')?"":"url(/images/subp.png)");}
if(options.color=='transparent'){var subp=((options_task.tp==='task')?"":"url(/images/0t.gif)");}
if(options_task.tp==='hidden'){options_task.tp='task';}
var b=document.createElement("div");b.className="processmap_task_label___"+this.options.theme;this.parent.dom.setStyle(b,{cursor:((this.options.rw===true)?"move":"default"),background:subp,backgroundRepeat:"no-repeat",backgroundPosition:"center",height:40});b.innerHTML=options.label;if(options.color=='transparent'){var b=document.createElement("div");b.className="processmap_task_label___"+this.options.theme;this.parent.dom.setStyle(b,{cursor:((this.options.rw===true)?"move":"default"),background:subp,backgroundRepeat:"repeat-y",backgroundPosition:"top",height:100});b.innerHTML=options.label;}
var b1=document.createElement("div");if(options.color!='transparent'){this.parent.dom.setStyle(b1,{top:'2',left:'5',border:"0px solid red",height:13,position:"absolute"});}
var c=document.createElement("div");this.parent.dom.setStyle(c,{position:"absolute",top:options.position.y+38,left:options.position.x+(81-12),height:25,width:25,border:"0px solid black",overflow:"hidden",cursor:(this.options.rw===true?"pointer":'default'),zIndex:9});if(this.options.rw===true){if(navigator.appName=="Microsoft Internet Explorer"){c.onclick=this.patternPanel.args(1,index,null);}else{c.onclick=this.patternPanel.args(index);}}
var d=document.createElement("div");this.parent.dom.setStyle(d,{position:"absolute",top:options.position.y+49,left:options.position.x+(93),height:38,width:38,border:"0px solid black",overflow:"hidden",zIndex:9});var t=document.createElement("div");this.parent.dom.setStyle(t,{position:"absolute",top:options.position.y-30,left:options.position.x+(81-14),height:30,width:30,overflow:"hidden",zIndex:9});if(this.options.rw===true)
{var menu=new this.parent.module.app.menuRight();var textMenu=G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC;var lengthText=mb_strlen(textMenu)*0.60;menu.make({target:a,width:(3+lengthText)+"em",theme:this.options.theme,menu:((options_task.tp=="task")?[{image:"/images/steps.png",text:G_STRINGS.ID_PROCESSMAP_STEPS,launch:function(event,index){this.tmp.stepsPanel=panel=new leimnud.module.panel();var data=this.data.db.task[index];var iForm=function(panel,index,ifo){panel.command(panel.loader.show);var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=steps&data="+{proUid:this.options.uid,tasUid:data.uid,option:ifo,index:index}.toJSONString()});r.callback=this.parent.closure({instance:this,method:function(index,rpc,panel){panel.command(panel.loader.hide);var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();},args:[index,r,panel]});r.make();}
panel.options={limit:true,size:{w:770,h:450},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_TASK_STEPS+" "+data.label.substr(0,82)+(data.label.length>=82?"...":""),theme:this.options.theme,statusBar:false,control:{close:true},fx:{modal:true}};panel.tab={width:300,optWidth:120,step:(this.parent.browser.isIE?3:4),options:[{title:G_STRINGS.ID_PROCESSMAP_STEPS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,1]}),selected:true},{title:G_STRINGS.ID_PROCESSMAP_CONDITIONS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,2]})},{title:G_STRINGS.ID_PROCESSMAP_TRIGGERS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,3]})}]};panel.events={remove:function(){}.extend(this)};panel.make();}.extend(this,index)},{image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS,launch:function(event,index){var panel;this.tmp.usersPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:450,h:300},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS+": "+task.label.substr(0,30)+(task.label.length>=30?"...":""),theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{modal:true}};panel.events={remove:function(){}.extend(this)};panel.make();panel.loader.show();var r;panel.currentRPC=r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=users&data="+{tas_uid:task.uid,pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this,index)},{image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC,launch:function(event,index){var panel;this.tmp.usersPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:450,h:300},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC+": "+task.label.substr(0,27)+(task.label.length>=27?"...":""),theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{modal:true}};panel.make();panel.loader.show();var r;panel.currentRPC=r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=users_adhoc&data="+{tas_uid:task.uid,pro_uid:this.options.uid}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this,index)},{image:"/images/rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS,launch:this.patternPanel.args(index)},{image:"/images/delete_rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_DELETE_PATTERNS,launch:this.parent.closure({instance:this,method:function(){var data=this.data.db.task[index];if(typeof(data.derivation.type)!='undefined'){new this.parent.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_WORKFLOW_CONFIRM_DELETE_PATTERNS+'"'+data.label+'"?',action:function(){var db=this.data.db,task=db.task[index];var vars={tas_uid:task.uid,pro_uid:this.options.uid};var aData={};aData.tas_uid=vars.tas_uid;aData.data=[];this.data.build.derivation(aData);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:'action=deleteAllRoutes&data='+vars.toJSONString()});r.make();}.extend(this)});}
else{new leimnud.module.app.alert().make({label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED});}},args:index})},{image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_TASK,launch:this.parent.closure({instance:this,method:function(index){var data=this.data.db.task[index];var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=taskCases&data="+{pro_uid:this.options.uid,task_uid:data.uid}.toJSONString()});r.callback=function(rpc){var rs=rpc.xmlhttp.responseText.parseJSON();var casesNumRec=rs.casesNumRec;if(casesNumRec==0){new this.parent.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK+" "+data.label,action:function(){data.object.drag.flush();this.dropables.derivation.unregister(data.object.dropIndex);this.data.render.deleteDerivation(data.uid,true);this.parent.dom.remove(data.object.elements);var r2=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteTask&data="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()});r2.make();}.extend(this)});}
else{var msg=_("ID_TASK_CANT_DELETE");msg=msg.replace("{0}",data.label);msg=msg.replace("{1}",casesNumRec);new this.parent.module.app.info().make({label:msg});}}.extend(this);r.make();return;if(confirm(G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK+" "+data.label))
{data.object.drag.flush();this.dropables.derivation.unregister(data.object.dropIndex);this.data.render.deleteDerivation(data.uid);this.parent.dom.remove(data.object.elements);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteTask&data="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()});r.make();}},args:index})},{simage:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_PROPERTIES,launch:this.parent.closure({instance:this,method:function(index){var panel;var iForm=function(panel,index,ifo){if(typeof(panel.flag)=='undefined'){if(!saveDataTaskTemporal(ifo)){var tabPass=panel.tabSelected;panel.tabSelected=panel.tabLastSelected;panel.tabLastSelected=tabPass;panel.flag=true;panel.makeTab();return false;}}
delete panel.flag;panel.command(panel.loader.show);var r=new this.parent.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=editTaskProperties&data="+{uid:data.uid,iForm:ifo,index:index}.toJSONString()});r.callback=this.parent.closure({instance:this,method:function(index,rpc,panel){panel.command(panel.loader.hide);panel.clearContent();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText.stripScript());scs.evalScript();},args:[index,r,panel]});r.make();}
this.tmp.propertiesPanel=panel=new leimnud.module.panel();var data=this.data.db.task[index];panel.options={limit:true,size:{w:600,h:430},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_TASK+": "+data.label.substr(0,75)+(data.label.length>=75?"...":""),theme:this.options.theme,statusBar:true,statusBarButtons:[{type:"button",value:G_STRINGS.ID_PROCESSMAP_SUBMIT},{type:"button",value:G_STRINGS.ID_PROCESSMAP_CANCEL}],control:{close:true,resize:false},fx:{modal:true}};panel.tab={width:170,optWidth:160,widthFixed:false,step:(this.parent.browser.isIE?3:4),options:[{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_DEFINITION,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,1]}),noClear:true,selected:true},{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_ASSIGNMENTS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,2]}),noClear:true},{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_TIMING,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,3]}),noClear:true},{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_PERMISSIONS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,5]}),noClear:true},{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_LABELS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,6]}),noClear:true},{title:G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_NOTIFICATIONS,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,7]}),noClear:true}]};var taskOptions=this.data.db.taskOptions;this.loadExtendedProperties=function(){for(i=0;i<taskOptions.length;i++){anElement={title:taskOptions[i].title,content:this.parent.closure({instance:this,method:iForm,args:[panel,index,taskOptions[i].id]}),noClear:true};panel.tab.options.push(anElement);}};this.loadExtendedProperties();panel.make();},args:index})}]:[{image:"/images/rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS,launch:this.patternPanel.args(index)},{image:"/images/delete_rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_DELETE_PATTERNS,launch:this.parent.closure({instance:this,method:function(){var data=this.data.db.task[index];if(typeof(data.derivation.type)!='undefined'){new this.parent.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_WORKFLOW_CONFIRM_DELETE_PATTERNS+'"'+data.label+'"?',action:function(){var db=this.data.db,task=db.task[index];var vars={tas_uid:task.uid,pro_uid:this.options.uid};var aData={};aData.tas_uid=vars.tas_uid;aData.data=[];this.data.build.derivation(aData);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:'action=deleteAllRoutes&data='+vars.toJSONString()});r.make();}.extend(this)});}
else{new leimnud.module.app.alert().make({label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED});}},args:index})},{image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_SUBPROCESS,launch:this.parent.closure({instance:this,method:function(index){var data=this.data.db.task[index];new this.parent.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_SUBPROCESS+data.label,action:function()
{data.object.drag.flush();this.dropables.derivation.unregister(data.object.dropIndex);this.data.render.deleteDerivation(data.uid,true);this.parent.dom.remove(data.object.elements);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteSubProcess&data="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()});r.make();}.extend(this)});return;if(confirm(G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK+data.label))
{data.object.drag.flush();this.dropables.derivation.unregister(data.object.dropIndex);this.data.render.deleteDerivation(data.uid);this.parent.dom.remove(data.object.elements);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteSubProcess="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()});r.make();}},args:index})},{simage:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_PROPERTIES,launch:function(event,index){var panel;this.tmp.subProcessPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:700,h:550},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_PROPERTIES,theme:this.options.theme,control:{close:true,resize:false},fx:{modal:true},statusBar:false,fx:{modal:true}};panel.make();panel.loader.show();var r;panel.currentRPC=r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=subProcess_Properties&data="+{tas_uid:task.uid,pro_uid:this.options.uid,index:index}.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}.extend(this,index)}])});this.observers.menu.register(menu.remove,menu);}
else{if(this.options.mi){this.parent.dom.setStyle(a,{cursor:('pointer')});a.title=G_STRINGS.ID_CLICK_VIEW_MORE_INFO;this.parent.dom.setStyle(b,{cursor:('pointer')});this.parent.event.add(a,'click',{instance:this,method:function(evt,index)
{var data=this.data.db.task[index];this.oTaskDetailsPanel=new leimnud.module.panel();this.oTaskDetailsPanel.options={limit:true,size:{w:300,h:227},position:{x:0,y:0,center:true},title:'',theme:'processmaker',statusBar:false,control:{drag:false,resize:false,close:true},fx:{opacity:true,rolled:false,modal:true}};this.oTaskDetailsPanel.make();this.oTaskDetailsPanel.events={remove:function(){delete this.oTaskDetailsPanel;}.extend(this)};this.oTaskDetailsPanel.loader.show();var r=new this.parent.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showTaskDetails&sTaskUID='+data.uid});r.callback=function(rpc){this.oTaskDetailsPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();this.oTaskDetailsPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);r.make();}.extend(this,index)});}}
this.panels.editor.elements.content.appendChild(a);a.appendChild(b);if(this.options.rw===true){a.appendChild(b1);}
this.panels.editor.elements.content.appendChild(c);this.panels.editor.elements.content.appendChild(d);this.panels.editor.elements.content.appendChild(t);options['object']={elements:{task:a,label:b,derivation:c,derivationBottom:d,init:t,statusIcons:b1}};options.object.dropIndex=this.dropables.derivation.register({element:a,value:index,events:{over:this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){this.parent.dom.setStyle(e,{border:"1px solid #006699"});},args:[a,options,index]}),out:this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){this.parent.dom.setStyle(e,{border:"0px solid #006699"});},args:[a,options,index]}),click:this.data.render.preDerivation.args(index)}});if(this.options.rw===true)
{options.object.drag=new this.parent.module.drag({link:{elements:a,ref:[a,c,d,t]},limit:true});this.observers.lineas.register(this.parent.closure({instance:options.object.drag,method:function(){}}),options.object.drag);options.object.drag.events={move:this.parent.closure({instance:this,method:function(div,divC,uid,drag){options.object.drag.observer.update();var db=this.data.db;},args:[a,c,index,options.object.drag]}),finish:this.parent.closure({instance:this,method:function(div,divC,uid,drag){if(!drag.moved){return false;}
var pos=this.parent.dom.position(div);var h=pos;var data=this.data.db.task[uid];var db=this.data.db;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveTaskPosition&data="+{uid:data.uid,position:pos}.toJSONString()});r.make();},args:[a,c,index,options.object.drag]})};options.object.drag.make();}},guide:function(index)
{var options=this.data.db.guide[index];var scl={x:this.panels.editor.elements.content.scrollLeft,y:this.panels.editor.elements.content.scrollTop};var a=document.createElement("div");var pos={top:((options.direction==="vertical")?0+scl.y:options.position),left:((options.direction==="horizontal")?0+scl.x:options.position)};this.parent.dom.setStyle(a,{position:"absolute",display:"",visibility:"visible",height:((options.direction==="vertical")?"100%":5),width:((options.direction==="horizontal")?"100%":5),backgroundColor:"transparent",borderLeft:((options.direction==="vertical")?"1":"0")+"px solid #FE9F0D",borderTop:((options.direction==="horizontal")?"1":"0")+"px solid #FE9F0D",overflow:'hidden',zIndex:1,cursor:((this.options.rw===true)?"move":"default"),left:pos.left,top:pos.top});var menu=new this.parent.module.app.menuRight();menu.make({target:a,width:201,theme:this.options.theme,menu:[{image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_LINE,launch:this.parent.closure({instance:this,method:function(index){var data=this.data.db.guide[index];this.parent.dom.remove(data.object.elements);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteGuide&data="+{uid:data.uid}.toJSONString()});r.make();},args:index})}],parent:this.parent});this.observers.menu.register(this.parent.closure({instance:menu,method:menu.remove}),menu);options.object={elements:{guide:a}};var Gdrag=new this.parent.module.drag({elements:a,limit:((options.direction==="horizontal")?"x":"y")});Gdrag.events={finish:this.parent.closure({instance:this,method:function(index,drag){if(!drag.moved){return false;}
var data=this.data.db.guide[index];var pos=this.parent.dom.position(data.object.elements.guide);data.position=(data.direction=="vertical")?pos.x:pos.y;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveGuidePosition&data="+{uid:data.uid,position:data.position,direction:data.direction}.toJSONString()});r.make();},args:[index,Gdrag]})};Gdrag.make();var guideObserver=this.observers.guideLines.register(this.parent.closure({instance:this,method:function(obj,direction){if(direction=="horizontal")
{obj.style.left=parseInt(this.panels.editor.elements.content.scrollLeft,10);}
else
{obj.style.top=parseInt(this.panels.editor.elements.content.scrollTop,10);}},args:[a,options.direction]}));this.panels.editor.elements.content.onscroll=this.observers.guideLines.update;this.panels.editor.elements.content.appendChild(a);},title:function(index)
{if(this.data.db.title)
{var title=this.data.db.title;var t=document.createElement("div");t.className="processmap_title___"+this.options.theme;this.parent.dom.setStyle(t,{top:title.position.y,left:title.position.x,cursor:((this.options.rw===true)?"move":"default")});t.innerHTML=title.label;if(this.options.rw===true)
{}
this.panels.editor.elements.content.appendChild(t);title.object={elements:{label:t}};if(this.options.rw===true)
{title.object.drag=new this.parent.module.drag({elements:t,limit:true});title.object.drag.events={finish:function(drag)
{if(!drag.moved){return false;}
var title=this.data.db.title;var pos=this.parent.dom.position(title.object.elements.label);title.position=pos;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveTitlePosition&data="+{pro_uid:this.options.uid,position:title.position}.toJSONString()});r.make();}.extend(this,title.object.drag)};title.object.drag.make();}}},text:function(index)
{var text=this.data.db.text[index];var a=document.createElement("div");a.className="processmap_text___"+this.options.theme;this.parent.dom.setStyle(a,{top:text.position.y,left:text.position.x,cursor:((this.options.rw===true)?"move":"default")});a.innerHTML=text.label;this.panels.editor.elements.content.appendChild(a);if(this.options.rw===true)
{var menu=new this.parent.module.app.menuRight();menu.make({target:a,width:201,theme:this.options.theme,menu:[{image:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_EDIT_TEXT,launch:function(evt,index){var text=this.data.db.text[index];new this.parent.module.app.prompt().make({label:G_STRINGS.ID_PROCESSMAP_EDIT_TEXT_CHANGE_TO,value:text.label.escapeHTML(),action:function(text,tObj){if(text.trim()!==""&&tObj.label!=text)
{tObj.label=tObj.object.elements.label.innerHTML=text.escapeHTML();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=updateText&data="+{uid:tObj.uid,label:tObj.label.unescapeHTML()}.toJSONString()});r.make();}}.extend(this,text)});}.extend(this,index)},{image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_TEXT,launch:function(evt,index){var text=this.data.db.text[index];var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=deleteText&data="+{uid:text.uid}.toJSONString()});r.make();this.parent.dom.remove(text.object.elements.label);this.data.db.text[index]=null;}.extend(this,index)}]});this.observers.menu.register(menu.remove,menu);text.object={elements:{label:a}};text.object.drag=new this.parent.module.drag({elements:a,limit:true});text.object.drag.events={finish:function(index,drag)
{if(!drag.moved){return false;}
var text=this.data.db.text[index];var pos=this.parent.dom.position(text.object.elements.label);text.position=pos;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveTextPosition&data="+{uid:text.uid,position:text.position}.toJSONString()});r.make();}.extend(this,index,text.object.drag)};text.object.drag.make();}},derivation:function(options)
{tt=options;var index=this.tools.getIndexOfUid(options.tas_uid);var from=this.data.db.task[index];this.data.render.deleteDerivation(options.tas_uid);var affe=options.data;from.derivation.type=options.type;for(var i=0;i<affe.length;i++)
{from.derivation.to[i]={task:affe[i]};}
this.data.render.lineDerivation(index);}},save:function()
{}}.expand(this,true);this.patternPanel=function(event,index,din){var options=this.data.db.task[index];var db=this.data.db,task=db.task[index];var derivation=task.derivation.to;var vars={tas_uid:task.uid,pro_uid:this.options.uid}.concat((din)?{type:din.type,next_task:this.data.db.task[din.to].uid}:{});if(event)
{if(typeof(this.data.db.task[index].derivation.type)=='undefined')
{new leimnud.module.app.alert().make({label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED});return false;}
var iWidth,iHeight;switch(this.data.db.task[index].derivation.type)
{case 0:iWidth=450;iHeight=205;break;case 1:iWidth=700;iHeight=350;break;case 2:iWidth=700;iHeight=350;break;case 3:iWidth=350;iHeight=350;break;case 4:iWidth=600;iHeight=350;break;case 5:iWidth=450;iHeight=205;break;case 8:iWidth=550;iHeight=300;break;}
this.tmp.derivationsPanel=panel=new leimnud.module.panel();panel.options={limit:true,size:{w:iWidth,h:iHeight},position:{x:50,y:50,center:true},title:G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS+": "+task.label,theme:this.options.theme,control:{close:true,resize:true},fx:{modal:true}};panel.make();panel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=derivations&data="+vars.toJSONString()});r.callback=function(rpc,panel)
{panel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();panel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this,panel);r.make();}
else
{if((this.data.db.task[index].derivation.type!=vars.type)&&(typeof(this.data.db.task[index].derivation.type)!='undefined'))
{if(typeof(this.data.db.task[index].derivation.type)!='undefined')
{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_PROCESSMAP_CONFIRM_WORKFLOW_PATTERN_CHANGE,action:function(){var aData={};aData.type=Number(vars.type);aData.tas_uid=vars.tas_uid;aData.data=[];aData.data.push(vars.next_task);this.data.build.derivation(aData);vars['delete']=true;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveNewPattern&data="+vars.toJSONString()});r.make();}.extend(this)});}}
else
{var aData={};aData.type=vars.type;aData.tas_uid=vars.tas_uid;aData.data=[];aData.data.push(vars.next_task);if((aData.type!=0)&&(aData.type!=5))
{for(var i=0;i<this.data.db.task[index].derivation.to.length;i++)
{if(!aData.data.inArray(this.data.db.task[index].derivation.to[i].task))
{aData.data.push(this.data.db.task[index].derivation.to[i].task);}}}
Pm.data.build.derivation(aData);aData.data.push(vars.next_task);vars['delete']=false;var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=saveNewPattern&data="+vars.toJSONString()});r.make();}}};this.tools={getIndexOfUid:function(uid)
{for(var i=0;i<this.data.db.task.length;i++)
{if(this.data.db.task[i].uid===uid){return i;}}},getUidOfIndex:function(index)
{return this.data.db.task[index].uid||false;}}.expand(this);this.expand(this);};processmap.prototype={parent:leimnud,tmp:{},info:{name:"processmap"},panels:{},dragables:{},dropables:{},make:function()
{this.options={theme:"firefox",rw:true,mi:true,ct:false,hideMenu:true}.concat(this.options||{});this.options.target=this.parent.dom.element(this.options.target);if(!this.validate()){return false;}
this.observers={menu:this.parent.factory(this.parent.pattern.observer,true),lineas:this.parent.factory(this.parent.pattern.observer,true),guideLines:this.parent.factory(this.parent.pattern.observer,true),buildingLineGuides:this.parent.factory(this.parent.pattern.observer,true)};this.dropables.derivation=new this.parent.module.drop();this.dropables.derivation.make();if(this.options.hideMenu===true)
{var h=this.parent.dom.element("pm_header");var m=this.parent.dom.element("pm_menu");var s=this.parent.dom.element("pm_submenu");var sep=this.parent.dom.element("pm_separator");sep.className="pm_separatorOff___"+this.options.theme;this.menuRolled=false;var dse=document.createElement("div");dse.id='pm_separator_div';dse.className="pm_separatorDOff___"+this.options.theme;sep.appendChild(dse);sep.onmouseup=function()
{if(this.menuRolled===true)
{sep.className="pm_separatorOff___"+this.options.theme;dse.className="pm_separatorDOff___"+this.options.theme;this.parent.dom.setStyle([h,m,s],{display:""});this.menuRolled=false;}
else
{sep.className="pm_separatorOn___"+this.options.theme;dse.className="pm_separatorDOn___"+this.options.theme;this.menuRolled=true;this.parent.dom.setStyle([h,m,s],{display:"none"});}}.extend(this,sep);dse.onmouseover=function()
{if(this.menuRolled===true)
{dse.className="pm_separatorDOn___"+this.options.theme+" pm_separatorOver___"+this.options.theme;}
else
{dse.className="pm_separatorDOff___"+this.options.theme+" pm_separatorOver___"+this.options.theme;}}.extend(this,dse);dse.onmouseout=function()
{if(this.menuRolled===true)
{dse.className="pm_separatorDOn___"+this.options.theme+" pm_separatorOut___"+this.options.theme;}
else
{dse.className="pm_separatorDOff___"+this.options.theme+" pm_separatorOut___"+this.options.theme;}}.extend(this,dse);}
if(this.options.rw===true){var bd=this.parent.dom.capture("tag.body 0");var sm=this.parent.dom.element("pm_submenu");this.parent.dom.setStyle(bd,{backgroundColor:"buttonface"});this.parent.dom.setStyle(sm,{height:25});}
this.panels.editor=new leimnud.module.panel();oClientWinSize=getClientWindowSize();var heightPanel=this.options.size.h;if(heightPanel<=oClientWinSize.height)heightPanel=heightPanel+800;this.panels.editor.options={limit:true,size:{w:(maximunX>this.options.size.w?maximunX+200:this.options.size.w),h:heightPanel},position:{x:200,y:0,centerX:true},title:"",titleBar:false,control:{resize:false},fx:{opacity:false,shadow:false,blinkToFront:false},theme:this.options.theme,target:this.options.target,modal:true,limit:true};this.panels.editor.setStyle={content:{background:"white url('"+this.options.images_dir+"bg_pm.gif') repeat fixed",backgroundPosition:"10 0"},containerWindow:{borderWidth:0,padding:0,backgroundColor:"buttonface"},titleBar:{background:"transparent",borderWidth:0,height:5},frontend:{backgroundColor:"buttonface"},backend:{backgroundColor:"buttonface"},status:{textAlign:"center"}};this.panels.editor.make();this.panels.editor.loader.show();this.panels.editor.addContentStatus(G_STRINGS.ID_PROCESSMAP_LOADING);this.data.load();if(this.options.rw===true)
{this.data.render.buildingBlocks.panel();}},validate:function()
{return(!this.options.target||!this.options.dataServer||!this.options.lang)?false:true;},addTask:function(evt,tp)
{var options=tp;var m=this.menu.cursor;var cpos=this.parent.dom.position(this.panels.editor.elements.content);var index=this.data.db['task'].length;var scl={x:this.panels.editor.elements.content.scrollLeft,y:this.panels.editor.elements.content.scrollTop};var pos={x:scl.x+(m.x-cpos.x),y:scl.y+(m.y-cpos.y)};this.data.db.task[index]={position:pos,label:G_STRINGS.ID_PROCESSMAP_NEW_TASK,uid:false,task_type:((options.tp=='task')?'NORMAL':'SUBPROCESS'),derivation:{to:[]}}
var data=this.data.db.task[index];if(options.tp=='task')
{var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=addTask&data="+{uid:this.options.uid,position:pos}.toJSONString()});r.callback=this.parent.closure({instance:this,method:function(index,rpc){var rs=rpc.xmlhttp.responseText.parseJSON();var data=this.data.db.task[index];data.uid=rs.uid||false;data.statusIcons=rs.statusIcons;this.data.build.task(index,{tp:'task'});data.label=data.object.elements.label.innerHTML=rs.label||"";},args:[index,r]});r.make();}
else
{if(options.tp=='subprocess'){this.data.build.task(index,{tp:'subprocess'});var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=addSubProcess&data="+{uid:this.options.uid,position:pos}.toJSONString()});r.callback=this.parent.closure({instance:this,method:function(index,rpc){var rs=rpc.xmlhttp.responseText.parseJSON();var data=this.data.db.task[index];data.label=data.object.elements.label.innerHTML=rs.label||"";data.uid=rs.uid||false;},args:[index,r]});r.make();}
else{this.data.build.task(index,{tp:'hidden'});var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=addTaskHidden&data="+{uid:this.options.uid,position:pos}.toJSONString()});r.callback=this.parent.closure({instance:this,method:function(index,rpc){var rs=rpc.xmlhttp.responseText.parseJSON();var data=this.data.db.task[index];data.label=data.object.elements.label.innerHTML=rs.label||"";data.uid=rs.uid||false;},args:[index,r]});r.make();}}},addText:function(evt)
{new this.parent.module.app.prompt().make({label:G_STRINGS.ID_PROCESSMAP_TEXT,action:function(text){if(text&&text.trim()!=="")
{var m=this.menu.cursor;var cpos=this.parent.dom.position(this.panels.editor.elements.content);var index=this.data.db.task.length;var scl={x:this.panels.editor.elements.content.scrollLeft,y:this.panels.editor.elements.content.scrollTop};var pos={x:scl.x+(m.x-cpos.x),y:scl.y+(m.y-cpos.y)};var index=this.data.db.text.length;this.data.db.text[index]={label:text,position:{x:pos.x,y:pos.y},uid:false};this.data.build.text(index);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=addText&data="+{uid:this.options.uid,label:text,position:{x:pos.x,y:pos.y}}.toJSONString()});r.callback=function(rpc,index){var rs=rpc.xmlhttp.responseText.parseJSON();this.data.db.text[index].uid=rs.uid;}.extend(this,index);r.make();}}.extend(this)});},addGuide:function(evt,dir)
{var m=this.menu.cursor;var cpos=this.parent.dom.position(this.panels.editor.elements.content);var index=this.data.db.guide.length;var scl={x:this.panels.editor.elements.content.scrollLeft,y:this.panels.editor.elements.content.scrollTop};var pos={x:(m.x-cpos.x),y:(m.y-cpos.y)};this.data.db.guide[index]={position:((dir==="horizontal")?pos.y+scl.y:pos.x+scl.x),uid:false,direction:dir}
var data=this.data.db.guide[index];this.data.build.guide(index);var r=new leimnud.module.rpc.xmlhttp({url:this.options.dataServer,args:"action=addGuide&data="+{uid:this.options.uid,position:data.position,direction:data.direction}.toJSONString()});r.callback=function(rpc,index)
{var rs=rpc.xmlhttp.responseText.parseJSON();var data=this.data.db.guide[index];data.uid=rs.uid||false;}.extend(this,index);r.make();}};var mainPanel;function showDbConnectionsList(PRO_UID)
{mainPanel=new leimnud.module.panel();mainPanel.options={size:{w:640,h:450},position:{x:0,y:0,center:true},title:G_STRINGS.ID_DBS_LIST,theme:"processmaker",statusBar:false,control:{resize:false,roll:false,drag:true},fx:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}};mainPanel.events={remove:function(){delete(mainPanel);}.extend(this)};mainPanel.make();mainPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'../dbConnections/dbConnectionsAjax',args:'action=showDbConnectionsList&PRO_UID='+PRO_UID});oRPC.callback=function(rpc){mainPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();mainPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
function showCaseSchedulerList(PRO_UID)
{mainPanel=new leimnud.module.panel();mainPanel.options={size:{w:850,h:570},position:{x:0,y:0,center:true},title:G_STRINGS.ID_PROCESSMAP_CASE_SCHEDULER_TITLE,theme:"processmaker",statusBar:false,control:{resize:false,roll:false,drag:true},fx:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}};mainPanel.events={remove:function(){delete(mainPanel);}.extend(this)};mainPanel.make();mainPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'processes_Ajax',args:'action=case_scheduler&PRO_UID='+PRO_UID});oRPC.callback=function(rpc){mainPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();mainPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
function showNewProcessMap(PRO_UID)
{window.location="../bpmnDesigner?id="+PRO_UID;}
function showLogCaseSchedulerList(PRO_UID)
{mainPanel=new leimnud.module.panel();mainPanel.options={size:{w:640,h:450},position:{x:0,y:0,center:true},title:"Case Scheduler Log List",theme:"processmaker",statusBar:false,control:{resize:false,roll:false,drag:true},fx:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}};mainPanel.events={remove:function(){delete(mainPanel);}.extend(this)};mainPanel.make();mainPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'processes_Ajax',args:'action=log_case_scheduler&PRO_UID='+PRO_UID});oRPC.callback=function(rpc){mainPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();mainPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}
function exitEditor()
{location.href='../processes/mainInit';}
function moldTitle(title,size)
{size=parseInt(size);chain=parseInt(title.length*6);if((size-chain)<0)
{chain=parseInt((size/6)-33);newTitle=title.substring(0,chain);title=newTitle+"...";}
return title;}
function openPMFolder(uid,rootfolder){currentFolder=uid;if((document.getElementById('child_'+uid).innerHTML!="")&&(uid!=rootfolder)){document.getElementById('child_'+uid).innerHTML="";getPMFolderContent(uid);return;}
document.getElementById('child_'+uid).innerHTML="<img src='/images/classic/loader_B.gif' >";var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:true,method:'POST',args:'action=openPMFolder&folderID='+uid+'&rootfolder='+rootfolder});oRPC.callback=function(rpc){document.getElementById('child_'+uid).innerHTML=rpc.xmlhttp.responseText;var scs=rpc.xmlhttp.responseText.extractScript();scs.evalScript();getPMFolderContent(uid);}.extend(this);oRPC.make();if(uid==rootfolder){getPMFolderTags(rootfolder);}}
function getPMFolderContent(uid){document.getElementById('spanFolderContent').innerHTML="<img src='/images/classic/loader_B.gif' >";var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:true,method:'POST',args:'action=getPMFolderContent&folderID='+uid});oRPC.callback=function(rpc){document.getElementById('spanFolderContent').innerHTML=oRPC.xmlhttp.responseText;var scs=oRPC.xmlhttp.responseText.extractScript();scs.evalScript();}.extend(this);oRPC.make();}
function getPMFolderSearchResult(searchKeyword,type){document.getElementById('spanFolderContent').innerHTML="<img src='/images/classic/loader_B.gif' >";var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:true,method:'POST',args:'action=getPMFolderContent&searchKeyword='+searchKeyword+'&type='+type});oRPC.callback=function(rpc){document.getElementById('spanFolderContent').innerHTML=oRPC.xmlhttp.responseText;var scs=oRPC.xmlhttp.responseText.extractScript();scs.evalScript();}.extend(this);oRPC.make();}
function getPMFolderTags(rootfolder){document.getElementById('tags_cloud').innerHTML="<img src='/images/classicloader_B.gif' >";var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:false,method:'POST',args:'action=getPMFolderTags&rootFolder='+rootfolder});oRPC.make();document.getElementById('tags_cloud').innerHTML=oRPC.xmlhttp.responseText;var scs=oRPC.xmlhttp.responseText.extractScript();scs.evalScript();}
var uploadDocument=function(docID,appDocId,docVersion,actionType,appId,docType){if(actionType){if(actionType=="R"){windowTitle=G_STRINGS.ID_UPLOAD_REPLACE_INPUT;}
if(actionType=="NV"){windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT_VERSION;}}else{windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT;docVersion=1;actionType="";appDocId="";}
oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:300},position:{x:0,y:0,center:true},title:windowTitle,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',args:"action=uploadDocument&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType+"&appId="+appId+"&docType="+docType});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var oPanel;var gUSER_UID;var uploadExternalDocument=function(folderID){gUSER_UID=folderID;oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:300},position:{x:0,y:0,center:true},title:G_STRINGS.ID_UPLOAD_EXTERNAL_DOCUMENT,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',args:"action=uploadExternalDocument&folderID="+folderID});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var oPanel;var gUSER_UID;var newFolder=function(folderID){gUSER_UID=folderID;oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:250},position:{x:0,y:0,center:true},title:G_STRINGS.ID_NEW_FOLDER,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',args:"action=newFolder&folderID="+folderID});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var documentVersionHistory=function(folderID,appDocId){oPanel=new leimnud.module.panel();oPanel.options={size:{w:550,h:300},position:{x:0,y:0,center:true},title:G_STRINGS.ID_INPUT_DOCUMENT_HISTORY,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',args:"action=documentVersionHistory&folderID="+folderID+"&appDocId="+appDocId});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var documentInfo=function(docID,appDocId,docVersion,actionType,appId,docType,usrUid){oPanel=new leimnud.module.panel();oPanel.options={size:{w:400,h:270},position:{x:0,y:0,center:true},title:G_STRINGS.ID_DOCUMENT_INFO,theme:"processmaker",statusBar:false,control:{resize:true,roll:false},fx:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}};oPanel.events={remove:function(){delete(oPanel);}.extend(this)};oPanel.make();oPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',args:"action=documentInfo&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType+"&appId="+appId+"&docType="+docType+"&usrUid="+usrUid});oRPC.callback=function(rpc){oPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();};var documentdelete=function(docID,appDocId,docVersion,actionType,appId,docType,usrUid){new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:true,method:'POST',args:'action=documentdelete&sFileUID='+appDocId+'&docVersion='+docVersion});oRPC.callback=function(oRPC){window.location='appFolderList';}.extend(this);oRPC.make();}.extend(this)});};function deletePMFolder(uid,rootfolder){new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,action:function(){var oRPC=new leimnud.module.rpc.xmlhttp({url:'appFolderAjax',async:true,method:'POST',args:'action=deletePMFolder&sFileUID='+uid+'&rootfolder='+rootfolder});oRPC.callback=function(oRPC){window.location='appFolderList';}.extend(this);oRPC.make();}.extend(this)});}
_editor_url="/htmlarea/";function editor_defaultConfig(objname){this.version="2.03"
this.width="auto";this.height="auto";this.bodyStyle='background-color: #FFFFFF; font-family: "Verdana"; font-size: x-small;';this.imgURL=_editor_url+'images/';this.debug=0;this.replaceNextlines=0;this.plaintextInput=0;this.toolbar=[['fontname'],['fontsize'],['bold','italic','underline','separator'],['justifyleft','justifycenter','justifyright','separator'],['OrderedList','UnOrderedList','Outdent','Indent','separator'],['forecolor','backcolor','separator'],['HorizontalRule','Createlink','InsertImage','InsertTable','htmlmode','separator'],['popupeditor','about']];this.fontnames={"Arial":"arial, helvetica, sans-serif","Courier New":"courier new, courier, mono","Georgia":"Georgia, Times New Roman, Times, Serif","Tahoma":"Tahoma, Arial, Helvetica, sans-serif","Times New Roman":"times new roman, times, serif","Verdana":"Verdana, Arial, Helvetica, sans-serif","impact":"impact","WingDings":"WingDings"};this.fontsizes={"1 (8 pt)":"1","2 (10 pt)":"2","3 (12 pt)":"3","4 (14 pt)":"4","5 (18 pt)":"5","6 (24 pt)":"6","7 (36 pt)":"7"};this.fontstyles=[];this.btnList={"bold":['Bold','Bold','editor_action(this.id)','ed_format_bold.gif'],"italic":['Italic','Italic','editor_action(this.id)','ed_format_italic.gif'],"underline":['Underline','Underline','editor_action(this.id)','ed_format_underline.gif'],"strikethrough":['StrikeThrough','Strikethrough','editor_action(this.id)','ed_format_strike.gif'],"subscript":['SubScript','Subscript','editor_action(this.id)','ed_format_sub.gif'],"superscript":['SuperScript','Superscript','editor_action(this.id)','ed_format_sup.gif'],"justifyleft":['JustifyLeft','Justify Left','editor_action(this.id)','ed_align_left.gif'],"justifycenter":['JustifyCenter','Justify Center','editor_action(this.id)','ed_align_center.gif'],"justifyright":['JustifyRight','Justify Right','editor_action(this.id)','ed_align_right.gif'],"orderedlist":['InsertOrderedList','Ordered List','editor_action(this.id)','ed_list_num.gif'],"unorderedlist":['InsertUnorderedList','Bulleted List','editor_action(this.id)','ed_list_bullet.gif'],"outdent":['Outdent','Decrease Indent','editor_action(this.id)','ed_indent_less.gif'],"indent":['Indent','Increase Indent','editor_action(this.id)','ed_indent_more.gif'],"forecolor":['ForeColor','Font Color','editor_action(this.id)','ed_color_fg.gif'],"backcolor":['BackColor','Background Color','editor_action(this.id)','ed_color_bg.gif'],"horizontalrule":['InsertHorizontalRule','Horizontal Rule','editor_action(this.id)','ed_hr.gif'],"createlink":['CreateLink','Insert Web Link','editor_action(this.id)','ed_link.gif'],"insertimage":['InsertImage','Insert Image','editor_action(this.id)','ed_image.gif'],"inserttable":['InsertTable','Insert Table','editor_action(this.id)','insert_table.gif'],"htmlmode":['HtmlMode','View HTML Source','editor_setmode(\''+objname+'\')','ed_html.gif'],"popupeditor":['popupeditor','Enlarge Editor','editor_action(this.id)','fullscreen_maximize.gif'],"about":['about','About this editor','editor_about(\''+objname+'\')','ed_about.gif'],"custom1":['custom1','Purpose of button 1','editor_action(this.id)','ed_custom.gif'],"custom2":['custom2','Purpose of button 2','editor_action(this.id)','ed_custom.gif'],"custom3":['custom3','Purpose of button 3','editor_action(this.id)','ed_custom.gif'],"help":['showhelp','Help using editor','editor_action(this.id)','ed_help.gif']};}
function editor_generate(objname,userConfig){var config=new editor_defaultConfig(objname);if(userConfig){for(var thisName=0;thisName<userConfig.length;thisName++){if(userConfig[thisName]){config[thisName]=userConfig[thisName];}}}
document.getElementById(objname).config=config;var obj=document.getElementById(objname);if(!config.width||config.width=="auto"){if(obj.style.width){config.width=obj.style.width;}
else if(obj.cols){config.width=(obj.cols*8)+22;}
else{config.width='100%';}}
if(!config.height||config.height=="auto"){if(obj.style.height){config.height=obj.style.height;}
else if(obj.rows){config.height=obj.rows*17}
else{config.height='200';}}
var tblOpen='<table border=0 cellspacing=0 cellpadding=0 style="float: left;"  unselectable="on"><tr><td style="border: none; padding: 1 0 0 0"><nobr>';var tblClose='</nobr></td></tr></table>\n';var toolbar='';var btnGroup,btnItem,aboutEditor;for(var btnGroup=0;btnGroup<config.toolbar.length;btnGroup++){if(config.toolbar[btnGroup].length==1&&config.toolbar[btnGroup][0].toLowerCase()=="linebreak"){toolbar+='<br clear="all">';continue;}
toolbar+=tblOpen;for(var btnItem=0;btnItem<config.toolbar[btnGroup].length;btnItem++){{var btnName=config.toolbar[btnGroup][btnItem].toLowerCase();if(btnName=="fontname"){toolbar+='<select id="_'+objname+'_FontName" onChange="editor_action(this.id)" unselectable="on" style="margin: 1 2 0 2; font-size: 12px;">';for(var fontname=0;fontname<config.fontnames.length;fontname++){toolbar+='<option value="'+config.fontnames[fontname]+'">'+fontname+'</option>'}
toolbar+='</select>';continue;}
if(btnName=="fontsize"){toolbar+='<select id="_'+objname+'_FontSize" onChange="editor_action(this.id)" unselectable="on" style="margin: 1 2 0 0; font-size: 12px;">';for(var fontsize=0;fontsize<config.fontsizes.length;fontsize++){toolbar+='<option value="'+config.fontsizes[fontsize]+'">'+fontsize+'</option>'}
toolbar+='</select>\n';continue;}
if(btnName=="fontstyle"){toolbar+='<select id="_'+objname+'_FontStyle" onChange="editor_action(this.id)" unselectable="on" style="margin: 1 2 0 0; font-size: 12px;">';+'<option value="">Font Style</option>';for(var i=0;i<config.fontstyles.length;i++){var fontstyle=config.fontstyles[i];toolbar+='<option value="'+fontstyle.className+'">'+fontstyle.name+'</option>'}
toolbar+='</select>';continue;}
if(btnName=="separator"){toolbar+='<span style="border: 1px inset; width: 1px; font-size: 16px; height: 16px; margin: 0 3 0 3"></span>';continue;}
var btnObj=config.btnList[btnName];if(btnName=='linebreak'){alert("htmlArea error: 'linebreak' must be in a subgroup by itself, not with other buttons.\n\nhtmlArea wysiwyg editor not created.");return;}
if(!btnObj){alert("htmlArea error: button '"+btnName+"' not found in button list when creating the wysiwyg editor for '"+objname+"'.\nPlease make sure you entered the button name correctly.\n\nhtmlArea wysiwyg editor not created.");return;}
var btnCmdID=btnObj[0];var btnTitle=btnObj[1];var btnOnClick=btnObj[2];var btnImage=btnObj[3];toolbar+='<button title="'+btnTitle+'" id="_'+objname+'_'+btnCmdID+'" class="btn" onClick="'+btnOnClick+'" onmouseover="if(this.className==\'btn\'){this.className=\'btnOver\'}" onmouseout="if(this.className==\'btnOver\'){this.className=\'btn\'}" unselectable="on"><img src="'+config.imgURL+btnImage+'" border=0 unselectable="on"></button>';}}
toolbar+=tblClose;}
var editor='<span id="_editor_toolbar"><table border=0 cellspacing=0 cellpadding=0 bgcolor="buttonface" style="padding: 1 0 0 2" width='+config.width+' unselectable="on"><tr><td>\n'
+toolbar
+'</td></tr></table>\n'
+'</td></tr></table></span>\n'
+'<textarea ID="_'+objname+'_editor" style="width:'+config.width+'; height:'+config.height+'; margin-top: -1px; margin-bottom: -1px;" wrap=soft></textarea>';editor+='<div id="_'+objname+'_cMenu" style="position: absolute; visibility: hidden;"></div>';if(!config.debug){document.getElementById(objname).style.display="none";}
if(config.plaintextInput){var contents=document.getElementById(objname).value;contents=contents.replace(/\r\n/g,'<br>');contents=contents.replace(/\n/g,'<br>');contents=contents.replace(/\r/g,'<br>');document.getElementById(objname).value=contents;}
insertHTMLAfterEnd(document.getElementById(objname),editor);editor_setmode(objname,'init');for(var idx=0;idx<document.forms.length;idx++){leimnud.event.add(document.forms[idx],'submit',function(){editor_filterOutput(objname);});}
return true;}
function editor_action(button_id){var BtnParts=Array();BtnParts=button_id.split("_");var objname=button_id.replace(/^_(.*)_[^_]*$/,'$1');var cmdID=BtnParts[BtnParts.length-1];var button_obj=document.getElementById(button_id);var editor_obj=document.getElementById("_"+objname+"_editor");var config=document.getElementById(objname).config;if(cmdID=='showhelp'){window.open(_editor_url+"popups/editor_help.html",'EditorHelp');return;}
if(cmdID=='popupeditor'){window.open(_editor_url+"popups/fullscreen.html?"+objname,'FullScreen','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=640,height=480');return;}
if(editor_obj.tagName.toLowerCase()=='textarea'){return;}
var editdoc=editor_obj.contentWindow.document;editor_focus(editor_obj);var idx=button_obj.selectedIndex;var val=(idx!=null)?button_obj[idx].value:null;if(0){}
else if(cmdID=='custom1'){alert("Hello, I am custom button 1!");}
else if(cmdID=='custom2'){var myTitle="This is a custom title";var myText=showModalDialog(_editor_url+"popups/custom2.html",myTitle,"resizable: yes; help: no; status: no; scroll: no; ");if(myText){editor_insertHTML(objname,myText);}}
else if(cmdID=='custom3'){editor_insertHTML(objname,"It's easy to add buttons that insert text!");}
else if(cmdID=='FontName'&&val){editdoc.execCommand(cmdID,0,val);}
else if(cmdID=='FontSize'&&val){editdoc.execCommand(cmdID,0,val);}
else if(cmdID=='FontStyle'&&val){editdoc.execCommand('RemoveFormat');editdoc.execCommand('FontName',0,'636c6173734e616d6520706c616365686f6c646572');var fontArray=editdoc.getElementsByTagName("FONT");for(i=0;i<fontArray.length;i++){if(fontArray[i].face=='636c6173734e616d6520706c616365686f6c646572'){fontArray[i].face="";fontArray[i].className=val;fontArray[i].outerHTML=fontArray[i].outerHTML.replace(/face=['"]+/,"");}}
button_obj.selectedIndex=0;}
else if(cmdID=='ForeColor'||cmdID=='BackColor'){var oldcolor=_dec_to_rgb(editdoc.queryCommandValue(cmdID));var newcolor=showModalDialog(_editor_url+"popups/select_color.html",oldcolor,"resizable: no; help: no; status: no; scroll: no;");if(newcolor!=null){editdoc.execCommand(cmdID,false,"#"+newcolor);}}
else{if(cmdID.toLowerCase()=='subscript'&&editdoc.queryCommandState('superscript')){editdoc.execCommand('superscript');}
if(cmdID.toLowerCase()=='superscript'&&editdoc.queryCommandState('subscript')){editdoc.execCommand('subscript');}
if(cmdID.toLowerCase()=='createlink'){editdoc.execCommand(cmdID,1);}
else if(cmdID.toLowerCase()=='insertimage'){showModalDialog(_editor_url+"popups/insert_image.html",editdoc,"resizable: no; help: no; status: no; scroll: no; ");}
else if(cmdID.toLowerCase()=='inserttable'){showModalDialog(_editor_url+"popups/insert_table.html?"+objname,window,"resizable: yes; help: no; status: no; scroll: no; ");}
else{editdoc.execCommand(cmdID);}}
editor_event(objname);}
function editor_event(objname,runDelay){var config=document.getElementById(objname).config;var editor_obj=document.getElementById("_"+objname+"_editor");if(runDelay==null){runDelay=0;}
var editdoc;var editEvent=editor_obj.contentWindow?editor_obj.contentWindow.event:event;if(editEvent&&editEvent.keyCode){var ord=editEvent.keyCode;var ctrlKey=editEvent.ctrlKey;var altKey=editEvent.altKey;var shiftKey=editEvent.shiftKey;if(ord==16){return;}
if(ord==17){return;}
if(ord==18){return;}
if(ctrlKey&&(ord==122||ord==90)){return;}
if((ctrlKey&&(ord==121||ord==89))||ctrlKey&&shiftKey&&(ord==122||ord==90)){return;}}
if(runDelay>0){return setTimeout(function(){editor_event(objname);},runDelay);}
if(this.tooSoon==1&&runDelay>=0){this.queue=1;return;}
this.tooSoon=1;setTimeout(function(){this.tooSoon=0;if(this.queue){editor_event(objname,-1);};this.queue=0;},333);editor_updateOutput(objname);editor_updateToolbar(objname);}
function editor_updateToolbar(objname,action){var config=document.getElementById(objname).config;var editor_obj=document.getElementById("_"+objname+"_editor");if(action=="enable"||action=="disable"){var tbItems=new Array('FontName','FontSize','FontStyle');for(var btnName=0;btnName<config.btnList.length;btnName++){tbItems.push(config.btnList[btnName][0]);}
for(var idxN=0;idxN<tbItems.length;idxN++){if(typeof(tbItems[idxN])=='undefined')alert(idxN);var cmdID=tbItems[idxN].toLowerCase();var tbObj=document.getElementById("_"+objname+"_"+tbItems[idxN]);if(cmdID=="htmlmode"||cmdID=="about"||cmdID=="showhelp"||cmdID=="popupeditor"){continue;}
if(tbObj==null){continue;}
var isBtn=(tbObj.tagName.toLowerCase()=="button")?true:false;if(action=="enable"){tbObj.disabled=false;if(isBtn){tbObj.className='btn'}}
if(action=="disable"){tbObj.disabled=true;if(isBtn){tbObj.className='btnNA'}}}
return;}
if(editor_obj.tagName.toLowerCase()=='textarea'){return;}
var editdoc=editor_obj.contentWindow.document;var fontname_obj=document.getElementById("_"+objname+"_FontName");if(fontname_obj){var fontname=editdoc.queryCommandValue('FontName');if(fontname==null){fontname_obj.value=null;}
else{var found=0;for(i=0;i<fontname_obj.length;i++){if(fontname.toLowerCase()==fontname_obj[i].text.toLowerCase()){fontname_obj.selectedIndex=i;found=1;}}
if(found!=1){fontname_obj.value=null;}}}
var fontsize_obj=document.getElementById("_"+objname+"_FontSize");if(fontsize_obj){var fontsize=editdoc.queryCommandValue('FontSize');if(fontsize==null){fontsize_obj.value=null;}
else{var found=0;for(i=0;i<fontsize_obj.length;i++){if(fontsize==fontsize_obj[i].value){fontsize_obj.selectedIndex=i;found=1;}}
if(found!=1){fontsize_obj.value=null;}}}
var classname_obj=document.getElementById("_"+objname+"_FontStyle");if(classname_obj){var curRange=editdoc.selection.createRange();var pElement;if(curRange.length){pElement=curRange[0];}
else{pElement=curRange.parentElement();}
while(pElement&&!pElement.className){pElement=pElement.parentElement;}
var thisClass=pElement?pElement.className.toLowerCase():"";if(!thisClass&&classname_obj.value){classname_obj.value=null;}
else{var found=0;for(i=0;i<classname_obj.length;i++){if(thisClass==classname_obj[i].value.toLowerCase()){classname_obj.selectedIndex=i;found=1;}}
if(found!=1){classname_obj.value=null;}}}
var IDList=Array('Bold','Italic','Underline','StrikeThrough','SubScript','SuperScript','JustifyLeft','JustifyCenter','JustifyRight','InsertOrderedList','InsertUnorderedList');for(i=0;i<IDList.length;i++){var btnObj=document.getElementById("_"+objname+"_"+IDList[i]);if(btnObj==null){continue;}
var cmdActive=editdoc.queryCommandState(IDList[i]);if(!cmdActive){if(btnObj.className!='btn'){btnObj.className='btn';}
if(btnObj.disabled!=false){btnObj.disabled=false;}}else if(cmdActive){if(btnObj.className!='btnDown'){btnObj.className='btnDown';}
if(btnObj.disabled!=false){btnObj.disabled=false;}}}}
function editor_updateOutput(objname){var config=document.getElementById(objname).config;var editor_obj=document.getElementById("_"+objname+"_editor");var isTextarea=(editor_obj.tagName.toLowerCase()=='textarea');var editdoc=isTextarea?null:editor_obj.contentWindow.document;var contents;if(isTextarea){contents=editor_obj.value;}
else{contents=editdoc.body.innerHTML;}
if(config.lastUpdateOutput&&config.lastUpdateOutput==contents){return;}
else{config.lastUpdateOutput=contents;}
document.getElementById(objname).value=contents;}
function editor_filterOutput(objname){editor_updateOutput(objname);var contents=document.getElementById(objname).value;var config=document.getElementById(objname).config;if(contents.toLowerCase()=='<p>&nbsp;</p>'){contents="";}
var filterTag=function(tagBody,tagName,tagAttr){tagName=tagName.toLowerCase();var closingTag=(tagBody.match(/^<\//))?true:false;if(tagName=='img'){tagBody=tagBody.replace(/(src\s*=\s*.)[^*]*(\*\*\*)/,"$1$2");}
if(tagName=='a'){tagBody=tagBody.replace(/(href\s*=\s*.)[^*]*(\*\*\*)/,"$1$2");}
return tagBody;};RegExp.lastIndex=0;var matchTag=/<\/?(\w+)((?:[^'">]*|'[^']*'|"[^"]*")*)>/g;contents=contents.replace(matchTag,filterTag);if(config.replaceNextlines){contents=contents.replace(/\r\n/g,' ');contents=contents.replace(/\n/g,' ');contents=contents.replace(/\r/g,' ');}
document.getElementById(objname).value=contents;}
function editor_setmode(objname,mode){var config=document.getElementById(objname).config;var editor_obj=document.getElementById("_"+objname+"_editor");if(document.readyState!='complete'){setTimeout(function(){editor_setmode(objname,mode)},25);return;}
var TextEdit='<textarea ID="_'+objname+'_editor" style="width:'+editor_obj.style.width+'; height:'+editor_obj.style.height+'; margin-top: -1px; margin-bottom: -1px;"></textarea>';var RichEdit='<iframe ID="_'+objname+'_editor"    style="width:'+editor_obj.style.width+'; height:'+editor_obj.style.height+';"></iframe>';if(mode=="textedit"||editor_obj.tagName.toLowerCase()=='iframe'){config.mode="textedit";var editdoc=editor_obj.contentWindow.document;var contents=editdoc.body.createTextRange().htmlText;editor_obj.outerHTML=TextEdit;editor_obj=document.getElementById("_"+objname+"_editor");editor_obj.value=contents;editor_event(objname);editor_updateToolbar(objname,"disable");editor_obj.onkeydown=function(){editor_event(objname);}
editor_obj.onkeypress=function(){editor_event(objname);}
editor_obj.onkeyup=function(){editor_event(objname);}
editor_obj.onmouseup=function(){editor_event(objname);}
editor_obj.ondrop=function(){editor_event(objname,100);}
editor_obj.oncut=function(){editor_event(objname,100);}
editor_obj.onpaste=function(){editor_event(objname,100);}
editor_obj.onblur=function(){editor_event(objname,-1);}
editor_updateOutput(objname);editor_focus(editor_obj);}
else{config.mode="wysiwyg";var contents=editor_obj.value;if(mode=='init'){contents=document.getElementById(objname).value;}
editor_obj.outerHTML=RichEdit;editor_obj=document.getElementById("_"+objname+"_editor");var html="";html+='<html><head>\n';if(config.stylesheet){html+='<link href="'+config.stylesheet+'" rel="stylesheet" type="text/css">\n';}
html+='<style>\n';html+='body {'+config.bodyStyle+'} \n';for(var i=0;i<config.fontstyles.length;i++){var fontstyle=config.fontstyles[i];if(fontstyle.classStyle){html+='.'+fontstyle.className+' {'+fontstyle.classStyle+'}\n';}}
html+='</style>\n'
+'</head>\n'
+'<body contenteditable="true" topmargin=1 leftmargin=1'
+'>'
+contents
+'</body>\n'
+'</html>\n';if(editor_obj.contentWindow)
var editdoc=editor_obj.contentWindow.document;else
var editdoc=editor_obj.contentDocument;editdoc.open();editdoc.write(html);editdoc.close();editor_updateToolbar(objname,"enable");editdoc.objname=objname;editdoc.onkeydown=function(){editor_event(objname);}
editdoc.onkeypress=function(){editor_event(objname);}
editdoc.onkeyup=function(){editor_event(objname);}
editdoc.onmouseup=function(){editor_event(objname);}
editdoc.body.ondrop=function(){editor_event(objname,100);}
editdoc.body.oncut=function(){editor_event(objname,100);}
editdoc.body.onpaste=function(){editor_event(objname,100);}
editdoc.body.onblur=function(){editor_event(objname,-1);}
if(mode!='init'){editor_focus(editor_obj);}}
if(mode!='init'){editor_event(objname);}}
function editor_focus(editor_obj){if(editor_obj.tagName.toLowerCase()=='textarea'){var myfunc=function(){editor_obj.focus();};setTimeout(myfunc,100);}
else{var editdoc=editor_obj.contentWindow.document;var editorRange=editdoc.body.createTextRange();var curRange=editdoc.selection.createRange();if(curRange.length==null&&!editorRange.inRange(curRange)){editorRange.collapse();editorRange.select();curRange=editorRange;}}}
function editor_about(objname){showModalDialog(_editor_url+"popups/about.html",window,"resizable: yes; help: no; status: no; scroll: no; ");}
function _dec_to_rgb(value){var hex_string="";for(var hexpair=0;hexpair<3;hexpair++){var myByte=value&0xFF;value>>=8;var nybble2=myByte&0x0F;var nybble1=(myByte>>4)&0x0F;hex_string+=nybble1.toString(16);hex_string+=nybble2.toString(16);}
return hex_string.toUpperCase();}
function editor_insertHTML(objname,str1,str2,reqSel){var config=document.getElementById(objname).config;var editor_obj=document.getElementById("_"+objname+"_editor");if(str1==null){str1='';}
if(str2==null){str2='';}
if(document.getElementById(objname)&&editor_obj==null){document.getElementById(objname).focus();document.getElementById(objname).value=document.getElementById(objname).value+str1+str2;return;}
if(editor_obj==null){return alert("Unable to insert HTML.  Invalid object name '"+objname+"'.");}
editor_focus(editor_obj);var tagname=editor_obj.tagName.toLowerCase();var sRange;if(tagname=='iframe'){var editdoc=editor_obj.contentWindow.document;sRange=editdoc.selection.createRange();var sHtml=sRange.htmlText;if(sRange.length){return alert("Unable to insert HTML.  Try highlighting content instead of selecting it.");}
var oldHandler=window.onerror;window.onerror=function(){alert("Unable to insert HTML for current selection.");return true;}
if(sHtml.length){if(str2){sRange.pasteHTML(str1+sHtml+str2)}
else{sRange.pasteHTML(str1);}}else{if(reqSel){return alert("Unable to insert HTML.  You must select something first.");}
sRange.pasteHTML(str1+str2);}
window.onerror=oldHandler;}
else if(tagname=='textarea'){editor_obj.focus();sRange=document.selection.createRange();var sText=sRange.text;if(sText.length){if(str2){sRange.text=str1+sText+str2;}
else{sRange.text=str1;}}else{if(reqSel){return alert("Unable to insert HTML.  You must select something first.");}
sRange.text=str1+str2;}}
else{alert("Unable to insert HTML.  Unknown object tag type '"+tagname+"'.");}
sRange.collapse(false);sRange.select();}
function editor_getHTML(objname){var editor_obj=document.getElementById("_"+objname+"_editor");var isTextarea=(editor_obj.tagName.toLowerCase()=='textarea');if(isTextarea){return editor_obj.value;}
else{return editor_obj.contentWindow.document.body.innerHTML;}}
function editor_setHTML(objname,html){var editor_obj=document.getElementById("_"+objname+"_editor");var isTextarea=(editor_obj.tagName.toLowerCase()=='textarea');if(isTextarea){editor_obj.value=html;}
else{editor_obj.contentWindow.document.body.innerHTML=html;}}
function editor_appendHTML(objname,html){var editor_obj=document.getElementById("_"+objname+"_editor");var isTextarea=(editor_obj.tagName.toLowerCase()=='textarea');if(isTextarea){editor_obj.value+=html;}
else{editor_obj.contentWindow.document.body.innerHTML+=html;}}
function _isMouseOver(obj,event){var mouseX=event.clientX;var mouseY=event.clientY;var objTop=obj.offsetTop;var objBottom=obj.offsetTop+obj.offsetHeight;var objLeft=obj.offsetLeft;var objRight=obj.offsetLeft+obj.offsetWidth;if(mouseX>=objLeft&&mouseX<=objRight&&mouseY>=objTop&&mouseY<=objBottom){return true;}
return false;}
function editor_cMenu_generate(editorWin,objname){var parentWin=window;editorWin.event.returnValue=false;var cMenuOptions=[['Cut','Ctrl-X',function(){}],['Copy','Ctrl-C',function(){}],['Paste','Ctrl-C',function(){}],['Delete','DEL',function(){}],['---',null,null],['Select All','Ctrl-A',function(){}],['Clear All','',function(){}],['---',null,null],['About this editor...','',function(){alert("about this editor");}]];editor_cMenu.options=cMenuOptions;var cMenuHeader=''
+'<div id="_'+objname+'_cMenu" onblur="editor_cMenu(this);" oncontextmenu="return false;" onselectstart="return false"'
+'  style="position: absolute; visibility: hidden; cursor: default; width: 167px; background-color: threedface;'
+'         border: solid 1px; border-color: threedlightshadow threeddarkshadow threeddarkshadow threedlightshadow;">'
+'<table border=0 cellspacing=0 cellpadding=0 width="100%" style="width: 167px; background-color: threedface; border: solid 1px; border-color: threedhighlight threedshadow threedshadow threedhighlight;">'
+' <tr><td colspan=2 height=1></td></tr>';var cMenuList='';var cMenuFooter=''
+' <tr><td colspan=2 height=1></td></tr>'
+'</table></div>';for(var menuIdx=0;menuIdx<editor_cMenu.options.length;menuIdx++){var menuName=editor_cMenu.options[menuIdx][0];var menuKey=editor_cMenu.options[menuIdx][1];var menuCode=editor_cMenu.options[menuIdx][2];if(menuName=="---"||menuName=="separator"){cMenuList+=' <tr><td colspan=2 class="cMenuDivOuter"><div class="cMenuDivInner"></div></td></tr>';}
else{cMenuList+='<tr class="cMenu" onMouseOver="editor_cMenu(this)" onMouseOut="editor_cMenu(this)" onClick="editor_cMenu(this, \''+menuIdx+'\',\''+objname+'\')">';if(menuKey){cMenuList+=' <td align=left class="cMenu">'+menuName+'</td><td align=right class="cMenu">'+menuKey+'</td>';}
else{cMenuList+=' <td colspan=2 class="cMenu">'+menuName+'</td>';}
cMenuList+='</tr>';}}
var cMenuHTML=cMenuHeader+cMenuList+cMenuFooter;document.getElementById('_'+objname+'_cMenu').outerHTML=cMenuHTML;editor_cMenu_setPosition(parentWin,editorWin,objname);parentWin['_'+objname+'_cMenu'].style.visibility='visible';parentWin['_'+objname+'_cMenu'].focus();}
function editor_cMenu_setPosition(parentWin,editorWin,objname){var event=editorWin.event;var cMenuObj=parentWin['_'+objname+'_cMenu'];var mouseX=event.clientX+parentWin.document.getElementById('_'+objname+'_editor').offsetLeft;var mouseY=event.clientY+parentWin.document.getElementById('_'+objname+'_editor').offsetTop;var cMenuH=cMenuObj.offsetHeight;var cMenuW=cMenuObj.offsetWidth;var pageH=document.body.clientHeight+document.body.scrollTop;var pageW=document.body.clientWidth+document.body.scrollLeft;if(mouseX+5+cMenuW>pageW){var left=mouseX-cMenuW-5;}
else{var left=mouseX+5;}
if(mouseY+5+cMenuH>pageH){var top=mouseY-cMenuH+5;}
else{var top=mouseY+5;}
cMenuObj.style.top=top;cMenuObj.style.left=left;}
function editor_cMenu(obj,menuIdx,objname){var action=event.type;if(action=="mouseover"&&!obj.disabled&&obj.tagName.toLowerCase()=='tr'){obj.className='cMenuOver';for(var i=0;i<obj.cells.length;i++){obj.cells[i].className='cMenuOver';}}
else if(action=="mouseout"&&!obj.disabled&&obj.tagName.toLowerCase()=='tr'){obj.className='cMenu';for(var i=0;i<obj.cells.length;i++){obj.cells[i].className='cMenu';}}
else if(action=="click"&&!obj.disabled){document.getElementById('_'+objname+'_cMenu').style.visibility="hidden";var menucode=editor_cMenu.options[menuIdx][2];menucode();}
else if(action=="blur"){if(!_isMouseOver(obj,event)){obj.style.visibility='hidden';}
else{if(obj.style.visibility!="hidden"){obj.focus();}}}
else{alert("editor_cMenu, unknown action: "+action);}}
function insertAfterEnd(oElement,oNewNode)
{oElement.parentNode.insertBefore(oNewNode,oElement.nextSibling);}
function insertHTMLAfterEnd(oElement,html)
{var auxDiv=$dce('div');auxDiv.innerHTML=html;for(var i=auxDiv.childNodes.length-1;i>=0;i--)
{insertAfterEnd(oElement,auxDiv.childNodes[i]);}}