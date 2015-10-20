/**
* @author MaBoRaK
* @extends Class leimnud.module.panel
* @param options Panel options
*/
leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.panel.js",
		Name	:"panel",
		Type	:"module",
		Version	:"1.0.5"
	},
	content	:function(options)
	{
		this.uid	= this.parent.tools.createUID();
		this.zIndex	= 0;
		this.stepZindex	= 5;
		this.controlSize= {w:15,h:15};
		this.elements	= {};
		this.setStyle	= {};
		this.events	= {};
		this.tab	= {};
		this.showing	= {};
		this.make=function()
		{
			this.makeTmpDB();
			this.options 		= {
				statusBar	:false,
				titleBar	:true,
				cursorToDrag	:"default",
				elementToDrag	:"title",
				strict_position:true
			}.concatMaborak(this.options || {});
			this.options.fx		= {
				blinkToFront:true,
				shadow	:true,
				opacity	:true,
				modal	:false,
				fadeIn	:false,
				fadeOut	:false,
				drag	:true
			}.concatMaborak(this.options.fx || {});
			//this.options.fx		= this.options.fx || {};
			this.options.control	= {
				resize:false,
				close:true,
				drag:true
			}.concatMaborak(this.options.control || {});
			this.options.statusBar = (this.options.statusBarButtons || this.options.control.resize)?true:this.options.statusBar;

			this.options.size	= {
				w:200,h:200
			}.concatMaborak(this.options.size || {});
			this.options.position= {
				x:20,y:20
			}.concatMaborak(this.options.position || {});


			/**
			*
			* theme Begin
			*
			*/
			this.makeTheme();
			/**
			*
			* theme End
			*
			*/

			/***
			*
			* containerWindow Begin
			*
			*/
			this.elements.containerWindow = $dce("div");
            if (typeof this.options.id != 'undefined') {
                this.elements.containerWindow.id=this.options.id;
            }
			this.elements.containerWindow.className="panel_containerWindow___"+this.getTheme("containerWindow");
			this.styles.containerWindow();
			if(this.options.fx.fadeIn===true)
			{
				this.parent.dom.opacity(this.elements.containerWindow,0);
			}
			this.target().appendChild(this.elements.containerWindow);
			/**
			*
			* containerWindow End
			*
			*/

			/**
			*
			* mfrontend Begin
			*
			*/
			this.elements.frontend = $dce("div");
			this.elements.frontend.className="panel_frontend___"+this.getTheme("frontend");
			this.styles.frontend();
			this.elements.containerWindow.appendChild(this.elements.frontend);

			/**
			*
			* titlebar Begin
			*
			*/
			this.elements.titleBar = $dce("div");
			this.elements.titleBar.className="panel_titleBar___"+this.getTheme("titleBar");
			this.parent.exec(this.styles.titleBar,false,false,this);
			this.elements.frontend.appendChild(this.elements.titleBar);

			/** Title */

			this.elements.title = $dce("div");
			this.elements.title.className="panel_title___"+this.getTheme("title");
			this.parent.exec(this.styles.title,false,false,this);
			this.elements.title.innerHTML=this.options.title ||"";
			this.elements.titleBar.appendChild(this.elements.title);

			/**
			*
			* titlebar End
			*
			*/

			/** HeaderBar Begin */

			this.elements.headerBar = $dce("div");
			this.elements.headerBar.className="panel_headerBar___"+this.getTheme("headerBar");
			this.styles.headerBar();
			this.elements.frontend.appendChild(this.elements.headerBar);

			/**
			*
			*  HeaderBar End
			*
			*/




			/**
			*
			* tab Vertical Begin
			*
			*/
			this.elements.tab = $dce("div");
			this.elements.tab.className="panel_tab___"+this.getTheme("tab");

			/**
			*
			* tab Vertical End
			*
			*/

			/**
			*
			* content Begin
			*
			*/
			this.elements.content = $dce("div");
			this.elements.content.className="panel_content___"+this.getTheme("content");
			//this.elements.content.innerHTML="&nbsp;";

			/** Loader */

			this.elements.loader = $dce("div");
			this.elements.loader.className="panel_loader___"+this.getTheme("loader");
			this.elements.frontend.appendChild(this.elements.content);
			this.elements.frontend.appendChild(this.elements.tab);
			this.elements.frontend.appendChild(this.elements.loader);

			/**
			*
			* content End
			*
			*/

			/**
			*
			* statusBar Begin
			*
			*/
			this.elements.statusBar = $dce("div");
			this.elements.statusBar.className="panel_statusBar___"+this.getTheme("statusBar");
			this.styles.statusBar();
			this.elements.frontend.appendChild(this.elements.statusBar);
			/**
			*
			* statusBar End
			*
			*/
			/**
			*
			* statusButttons Begin
			*
			*/
			this.elements.statusButtons = $dce("div");
			this.elements.statusButtons.className="panel_statusButtons___"+this.getTheme("statusButtons");
			this.styles.statusButtons();
			this.elements.statusBar.appendChild(this.elements.statusButtons);
			/**
			*
			* statusButtons End
			*
			*/
			/**
			*
			* status Begin
			*
			*/
			this.elements.status = $dce("div");
			this.elements.status.innerHTML="&nbsp;";
			this.elements.status.className="panel_status___"+this.getTheme("status");
			this.parent.exec(this.styles.status,false,false,this);
			this.elements.statusBar.appendChild(this.elements.status);
			/**
			*
			* status End
			*
			*/

			/**
			*
			* resize Begin
			*
			*/
			this.elements.resize = $dce("div");
			this.elements.resize.className="panel_resize___"+this.getTheme("resize");
			this.styles.resize();
			this.elements.statusBar.appendChild(this.elements.resize);
			/**
			*
			* resize End
			*
			*/
			/* Height Content Fix*/
			/**
			*
			* controls Begin
			*
			*/
			this.makeStatusButtons();
			/**
			*
			* controls End
			*
			*/

			this.parent.exec(this.styles.loader,false,false,this);
			this.parent.exec(this.styles.tab,false,false,this);
			this.parent.exec(this.styles.content,false,false,this);

			/**
			*
			* frontend End
			*
			*/


			/**
			*
			* backend Begin
			*
			*/
			this.elements.backend = $dce("div");
			this.elements.backend.className="panel_backend___"+this.getTheme("backend");
			this.parent.exec(this.styles.backend,false,false,this);
			this.elements.containerWindow.appendChild(this.elements.backend);
			/* Iframe for IE */
			if(this.parent.browser.isIE)
			{
				this.elements.iframe=$dce("iframe");
				this.elements.iframe.className="panel_iframe___"+this.getTheme("iframe");
				this.elements.iframe.frameBorder="no";
				this.elements.iframe.scrolling="no";
				this.elements.iframe.src="about:blank";
				this.parent.exec(this.styles.iframe,false,false,this);
				this.elements.backend.appendChild(this.elements.iframe);
			}
			/**
			*
			* backend End
			*
			*/
			/**
			*
			* Events manager Begin
			*
			*/
			this.makeEvents();
			/**
			*
			* Events manager End
			*
			*/

			/**
			*
			* fx Begin
			*
			*/
			this.makeFx();
			/**
			*
			* fx End
			*
			*/


			/**
			*
			* controls Begin
			*
			*/
			this.makeControls();
			/**
			*
			* controls End
			*
			*/
			/**
			*
			* make Fade
			*
			*/
			if(this.options.fx.fadeIn===true)
			{
				new this.parent.module.fx.fade().make({
					duration:1000,
					end		:this.styles.fx.opacityPanel.Static/100,
					dom		:this.elements.containerWindow
				});
			}
			/**
			*
			* make Fade
			*
			*/

		};
		/**
		* Make Fx
		*/
		this.makeFx=function()
		{
			if(this.options.fx.shadow)
			{
				this.elements.shadow = $dce("div");
				this.elements.shadow.className="panel_shadow___"+this.getTheme("shadow");
				this.parent.exec(this.styles.shadow,false,false,this);
				if(this.options.fx.fadeIn===true)
				{
					this.parent.dom.opacity(this.elements.shadow,0);
					new this.parent.module.fx.fade().make({
						duration	:1000,
						end		:this.styles.fx.opacityShadow.Static/100,
						dom		:this.elements.shadow
					});
				}
				this.target().appendChild(this.elements.shadow);
			}
			if(this.options.fx.modal)
			{
				this.elements.modal = $dce("div");
				this.elements.modal.className="panel_modal___"+this.getTheme("modal");
				this.elements.modal.id="panel_modal___"+this.getTheme("modal");
				if(this.options.fx.fadeIn===true)
				{
					this.parent.dom.opacity(this.elements.modal,0);
				}
				this.styles.modal();
				this.target().appendChild(this.elements.modal);
			}
			if(this.options.fx.blinkToFront===true)
			{
				this.events.init.push(this.blink);
				this.elements.containerWindow.onmousedown =this.blink;
			}
			if(this.options.fx.opacity)
			{
				this.events.init.push(this.fx.setOpacity);
				this.events.finish.push(this.fx.unsetOpacity);
			}
			if(this.options.fx.rolled)
			{
				this.roll();
			}
		};
		this.makeStatusButtons=function()
		{
			if(this.options.statusBarButtons)
			{
				this.parent.dom.setStyle(this.elements.statusBar,{
					//height:25
				});
				var t = this.options.statusBarButtons;
				this.elements.statusBarButtons=[];
				for(var i=0;i<t.length;i++)
				{
/*					var b = $dce("input");
					b.type = t[i].type || "button";
					b.value = t[i].value || "Button";
					this.parent.dom.setStyle(b,{
						font:"normal 8pt Tahoma,MiscFixed"
					});*/
					var b = new button(t[i].value || "Button");
					this.elements.statusBarButtons.push(b);
					this.elements.statusButtons.appendChild(b);
				}
			}
		};
		this.blink=function(){
			if(this.zIndex<this.parent.tmp.panel.zIndex)
			{
				this.zIndex=this.makezIndex();
				this.parent.dom.setStyle(this.elements.containerWindow,{
					zIndex:this.zIndex
				});
				if(this.options.fx.shadow)
				{
					this.shadowReIndex();
				}
			}
		};
		this.move=function(opt)
		{
			opt = {
				fx:true,
				x:this.options.position.x,
				y:this.options.position.y
			}.concatMaborak(opt);
			//if(typeof opt.x!=="undefined" && typeof opt.y!=="undefined")
			//{
				this.options.position.x = opt.x;
				this.options.position.y = opt.y;
				if(opt.fx===true)
				{
					new this.parent.module.fx.move().make({
						duration:500,
						end		:opt,
						dom		:this.elements.containerWindow,
						onFinish:opt.onFinish || function(){}
					});
					if(this.options.fx.shadow)
					{
						new this.parent.module.fx.move().make({
							duration:500,
							end		:{x:opt.x+2,y:opt.y+2},
							dom		:this.elements.shadow
						});
					}
				}
				else
				{
					this.parent.dom.setStyle(this.elements.containerWindow,{
						left:opt.x,
						top:opt.y
					});
					if(this.options.fx.shadow)
					{
						this.parent.dom.setStyle(this.elements.shadow,{
							top	:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2),
							left:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"left"),10)+2)
						});
					}
				}
			//}
		};
		this.maximize=function()
		{
			this.move({
				x:0,
				y:0,
				fx:true,
				onFinish:function()
				{
					new this.parent.module.fx.algorithm().make({
						duration 	: 1000,
						begin		: this.options.size.w,
						transition	: "sineInOut",
						end	 		: this.target().offsetWidth,
						onTransition: function(fx){
							this.resize({w:fx.result});
						}.extend(this),
						onFinish:function(fx)
						{
							this.resize({w:fx.options.end});
							new this.parent.module.fx.algorithm().make({
								duration 	: 1000,
								begin		: this.options.size.h,
								transition	: "sineInOut",
								end	 		: this.target().clientHeight,
								onTransition: function(fx){
									this.resize({h:fx.result});
								}.extend(this),
								onFinish:function(fx)
								{
									this.resize({h:fx.options.end});
								}.extend(this)
							});
						}.extend(this)
					});
				}.extend(this)
			});
		};
		this.resize=function(opt)
		{
			opt = opt || {};
			this.options.size={
				w:opt.w || this.options.size.w,
				h:opt.h || this.options.size.h
			};
			this.parent.dom.setStyle(this.elements.containerWindow,{
				width:this.options.size.w,
				height:this.options.size.h
			});
			this.styles.content();
			if(this.options.fx.shadow)
			{
				this.styles.shadow();
			}
			if(this.tab.display==="horizontal")
			{
				this.parent.dom.setStyle(this.elements.tab,{
					width:this.elements.containerWindow.clientWidth-8
				});
			}
		};
		this.center=function(inR)
		{
			inR = inR || false;
			var center	= {
				x:(((this.target().clientWidth/2)+this.target().scrollLeft)-(this.options.size.w/2)),
				y:(((this.target().clientHeight/2)+this.target().scrollTop)-(this.options.size.h/2))
			};
			if(inR==="x" || inR==="y")
			{
				this.options.position.x = ((inR==="x")?center.x:(this.options.position.x || 0));
				this.options.position.y = ((inR==="y")?center.y:(this.options.position.y || 0));
			}
			else
			{
				this.options.position.x = center.x;
				this.options.position.y = center.y;
				this.options.position.x = this.options.position.x || 0;
				this.options.position.y = this.options.position.y || 0;

			}
			this.options.position.x = (this.options.position.x<0)?0:this.options.position.x;
			this.options.position.y = (this.options.position.y<0)?0:this.options.position.y;
			this.move({x:this.options.position.x,y:this.options.position.y});
		};
		this.fixContent=function(rcr)
		{
			//alert(this.elements.content.clientHeight+":"+this.elements.content.scrollHeight);
			//var diff = parseInt(this.elements.content.scrollHeight-this.originalContent.h,10);
			//alert(this.elements.content.scrollHeight+":"+(this.elements.content.offsetHeight+this.elements.content.scrollHeight))
			var v1 = this.elements.content.scrollHeight;
			//alert(v1)
			var v2 = this.elements.content.clientHeight;
			//alert(v2)
			//var v1 = 1745;
			//var v2 = 503;
			var diff = parseInt(v1-v2);
			//alert(this.elements.content.scrollHeight+"-"+this.elements.content.clientHeight+"="+(diff))
			var originalSize = this.options.size;
			var originalContentSize = this.originalContent;
			return;
			//alert(this.elements.content.scrollHeight+":"+this.elements.content.clientHeight+":"+originalContentSize.h+":"+this.options.size.h+":"+diff)
			if(diff>0)
			{
				this.resize({
					//w:this.options.size.w,
					h:this.options.size.h+diff
				});
				this.options.size 	= originalSize;
				this.originalContent= originalContentSize;
			}
			else if(this.elements.content.clientHeight>originalContentSize.h)
			{
				this.resize({
					//w:this.options.size.w,
					h:this.options.size.h
				});
				if(rcr!==true)
				{
					//					this.fixContent(true);
				}
			}
		};
		/**
		* Make Events
		*/
		this.makeEvents=function()
		{
			this.events.init	=(this.events.init)?((this.events.init.isArray)?this.events.init:[this.events.init]):[];
			this.events.move	=(this.events.move)?((this.events.move.isArray)?this.events.move:[this.events.move]):[];
			this.events.finish	=(this.events.finish)?((this.events.finish.isArray)?this.events.finish:[this.events.finish]):[];
		};
		this.makeControls=function()
		{
			this.controls=[];
			/** Close */
			if(this.options.control.close)
			{
				this.elements.close = $dce("div");
				this.elements.close.className="panel_close___"+this.getTheme("close");
				this.parent.exec(this.styles.close,false,false,this);
				this.controls.push(this.elements.close);
				this.elements.titleBar.appendChild(this.elements.close);
			}
			/** Rollup/Rolldown */
			if(this.options.control.roll)
			{
				this.elements.roll = $dce("div");
				this.elements.roll.className="panel_roll___"+this.getTheme("roll");
				this.styles.roll();
				this.controls.push(this.elements.roll);
				this.elements.titleBar.appendChild(this.elements.roll);
				this.elements.title.ondblclick=this.roll;
			}
			if(this.options.control.setup)
			{
				this.elements.setup = $dce("div");
				this.elements.setup.className="panel_roll___"+this.getTheme("roll");
				this.styles.setup();
				this.controls.push(this.elements.setup);
				this.elements.titleBar.appendChild(this.elements.setup);
			}

			/**
			* Drag window
			*/
			if(this.options.control.drag)
			{
				var etd = this.elements[this.options.elementToDrag];
				this.parent.dom.setStyle(this.elements.title,{cursor:this.options.cursorToDrag});
				this.drag=new this.parent.module.drag({
					link:{
						elements:[etd],
						ref:((this.options.fx.shadow===true)?[this.elements.containerWindow,this.elements.shadow]:[this.elements.containerWindow])
					},
					limit:this.options.limit || false
				});
				this.drag.events={
					init	:this.events.init,
					move	:this.events.move,
					finish	:this.events.finish.concat(function(pan){
						pan.options.position.x=parseInt(pan.elements.containerWindow.style.left,10);
						pan.options.position.y=parseInt(pan.elements.containerWindow.style.top,10);
					}.extend(this.drag,this))
				};
				this.drag.cursor=this.options.cursorToDrag;
				this.drag.make();
			}
			/**
			* Resize window
			*/
			if(this.options.control.resize)
			{
				this.parent.dom.setStyle(this.elements.resize,{cursor:"nw-resize"});
				this.resizeDrag=new this.parent.module.drag({
					link:{
						elements:[this.elements.resize],
						ref:[]
					},
					noCursorMove:true
				});
				this.resizeDrag.cursor="nw-resize";
				this.resizeDrag.events={
					init	:function(panel)
					{
						this.panelBeginSize=panel.options.size;
					}.extend(this.resizeDrag,this),
					move	:function(panel){
						var np={
							x:this.currentCursorPosition.x-this.cursorStart.x,
							y:this.currentCursorPosition.y-this.cursorStart.y
						};
						panel.resize({
							w:this.panelBeginSize.w+np.x,
							h:this.panelBeginSize.h+np.y
						});
					}.extend(this.resizeDrag,this)
				};
				this.resizeDrag.make();
			}
			else
			{
				this.parent.dom.setStyle(this.elements.resize,{background:"transparent"});
			}
		};
		this.makeTab=function(dynamic)
		{
			if(this.loading===true){return false;}

			var thm = this.tab.display==="vertical"?"":"H";
			var tb =this.elements.tabOptions[this.tabSelected];
			tb.className="panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected");
			//tb.onmouseover	= this.styles.tabCSS.sover.args(tb);
			//tb.onmouseout	= this.styles.tabCSS.sout.args(tb);
			tb.onmouseover	= function(o,j){
				o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
				o.a.className=o.b;
			}.args({a:tb,b:"panel_tabOptionSelectedOver"+thm+"___"+this.getTheme("tabOptionSelectedOver")});
			tb.onmouseout	= function(o,j){
				o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
				o.a.className=o.b;
			}.args({a:tb,b:"panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected")});
			var tc = (typeof this.tab.options[this.tabSelected].content);
			if(!dynamic)
			{
				/*this.parent.dom.setStyle(tb,this.styles.tabCSS.sel.concatMaborak({
				width		:parseInt(this.parent.dom.getStyle(tb,"width"),10)-((!this.parent.browser.isIE)?3:0),
				borderLeftWidth	:4
				}));*/
				if(this.tab.display==="vertical")
				{
					var hj = (parseInt(this.parent.dom.getStyle(tb,"width"),10)-((!this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?0:3):0));
					this.parent.dom.setStyle(tb,{
						width		:hj,
						borderLeftWidth	:4
					});
				}
				else
				{
					this.parent.dom.setStyle(tb,{
						//height		:parseInt(this.parent.dom.getStyle(tb,"height"),10)-((!this.parent.browser.isIE)?3:0),
						//borderTopWidth	:4,
						//paddingTop	:5
					});
				}
			}
			tb.onmouseup=function(){return false;};
			if(this.tabLastSelected!==false)
			{
				var tls =this.elements.tabOptions[this.tabLastSelected];
				tls.className="panel_tabOption"+thm+"___"+this.getTheme("tabOption");
				//tls.onmouseover	= this.styles.tabCSS.over.args(tls);
				tls.onmouseover	= function(o,j){
					o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
					o.a.className=o.b;
				}.args({a:tls,b:"panel_tabOptionOver"+thm+"___"+this.getTheme("tabOptionOver")});
				//tls.onmouseout	= this.styles.tabCSS.out.args(tls);
				tls.onmouseout	= function(o,j){
					o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
					o.a.className=o.b;
				}.args({a:tls,b:"panel_tabOption"+thm+"___"+this.getTheme("tabOption")});
				tls.onmouseup=function(event,tabID){
					if(this.tab.manualDisabled){return false;}
					this.tabSelected=(this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?tabID:event):tabID;
					this.makeTab();
					//this.resize();
					return false;
				}.extend(this,this.tabLastSelected);

				/*this.parent.dom.setStyle(tls,this.styles.tabCSS.def.concatMaborak({
				width		:parseInt(this.parent.dom.getStyle(tb,"width"),10)+((!this.parent.browser.isIE)?3:0),
				borderLeftWidth	:1
				}));*/

				if(this.tab.display==="vertical")
				{
					this.parent.dom.setStyle(tls,{
						width		:parseInt(this.parent.dom.getStyle(tb,"width"),10)+((!this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?0:3):0),
						borderLeftWidth	:1
					});
				}
				else
				{
					this.parent.dom.setStyle(tls,{
						//height		:parseInt(this.parent.dom.getStyle(tb,"height"),10)+((!this.parent.browser.isIE)?3:0),
						//borderTopWidth	:1,
						//paddingTop	:10
					});
				}
				this.parent.dom.setStyle(tls,this.setStyle.tabOption || {});
			}
			if (typeof(this.flag) != "undefined") {
				delete this.flag;
				return true;
			}

			this.parent.dom.setStyle(tb,this.setStyle.tabOptionSelected || {});
			if(!this.tab.options[this.tabSelected].noClear)
			{
				this.clearContent();
			}
			this.addContent(this.tab.options[this.tabSelected].content);

			this.tabLastSelected=this.tabSelected;
			return true;
		};
		this.selectTab=function(tab)
		{
			if(tab>=this.elements.tabOptions.length){return false;}
			if(this.tabSelected===tab){
				this.tabLastSelected=false;
			}
			this.tabSelected = tab;
			this.makeTab((this.tabLastSelected===false)?true:false);
			return true;
		};
		this.shadowReIndex=function()
		{
			this.parent.dom.setStyle(this.elements.shadow,{
				zIndex	:this.zIndex-2
			});
		};
		this.reIndexElements=function()
		{

		};
		this.controlPosition=function()
		{
			var cl=this.controls.length+1;
			return ((3*cl)+(this.controlSize.w*this.controls.length));
		};
		this.makeTmpDB=function()
		{
			if(!this.parent.tmp.panel)
			{
				this.parent.tmp.panel={};
				this.parent.tmp.panel.zIndex=100;
			}
		};
		this.makezIndex=function()
		{
			this.parent.tmp.panel.zIndex+=this.stepZindex;
			return this.parent.tmp.panel.zIndex;
		};
		this.target=function()
		{
			return (this.options.target)?this.options.target:this.parent.dom.capture("tag.body 0");
		};
		/**
		*
		* @return {Int} h Height border,padding of Top/Bottom Panel
		*
		*/
		this.spaceOutPanel=function()
		{
			var brdr={
				x:(parseInt(this.parent.dom.getStyle(this.elements.content,"marginLeft") || 0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"marginRight") || 0,10)),
				y:(parseInt(this.parent.dom.getStyle(this.elements.content,"marginTop") || 0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"marginBottom") || 0,10))
			};
			var pddn={
				x:(parseInt(this.parent.dom.getStyle(this.elements.content,"paddingLeft") || 0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"paddingRight") || 0,10)),
				y:(parseInt(this.parent.dom.getStyle(this.elements.content,"paddingTop") || 0,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"paddingBottom") || 0,10))
			};
			var bbb={
				x:(parseInt(this.parent.dom.getStyle(this.elements.content,"borderLeftWidth") || 1,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"borderRightWidth") || 1,10)),
				y:(parseInt(this.parent.dom.getStyle(this.elements.content,"borderTopWidth") || 1,10)+parseInt(this.parent.dom.getStyle(this.elements.content,"borderBottomWidth") || 1,10))
			};
			//alert(brdr.y+((this.parent.browser.isIE)?-1:pddn.y+1)+2)
			return {
				//x:brdr.x+((this.parent.browser.isIE)?0:pddn.x)+bbb.x,
				x:brdr.x+pddn.x+bbb.x,
				//y:brdr.y+((this.parent.browser.isIE)?-1:pddn.y+1)+bbb.y
				y:brdr.y+pddn.y+bbb.y
			};
		};
		this.roll=function()
		{
			if(this.rolling){return false;}
			if(this.inroll===true)
			{
				this.rolling=true;
				this.inroll=false;
				this.parent.dom.setStyle(this.elements.containerWindow,{
					overflow:"hidden",
					//height	:this.options.size.h,
					width	:this.lastWidth
				});
				this.parent.dom.setStyle([this.elements.content,this.elements.statusBar],{
					display:"block"
				});
				new this.parent.module.fx.algorithm().make({
					transition	:"sineOut",
					duration	:1000,
					begin		:this.elements.containerWindow.offsetHeight,
					end		:this.options.size.h,
					onTransition	:function(fx){
						this.parent.dom.setStyle([this.elements.containerWindow],{
							height:fx.result
						});
						if(this.options.fx.shadow)
						{
							this.styles.shadow();
						}

					}.extend(this),
					onFinish	:function(fx){
						this.parent.dom.setStyle([this.elements.containerWindow],{
							height:this.options.size.h
						});
						if(this.options.fx.shadow)
						{
							this.styles.shadow();
						}
						this.parent.dom.setStyle(this.elements.frontend,{
							width:"auto"
						});
						this.rolling=false;
						return (this.events.roll || function(){})();
					}.extend(this)
				});
			}
			else
			{
				this.rolling=true;
				this.inroll=true;
				//alert(this.parent.dom.getStyle(this.elements.titleBar,"height")+":"+this.elements.titleBar.clientHeight)
				this.lastWidth=this.options.size.w || this.elements.containerWindow.offsetWidth;
				this.parent.dom.setStyle(this.elements.containerWindow,{
					overflow:"hidden",
					//height	:this.elements.titleBar.clientHeight,
					width	:this.options.fx.rollWidth || this.lastWidth
				});
				new this.parent.module.fx.algorithm().make({
					transition	:"sineOut",
					duration	:1000,
					begin		:this.elements.containerWindow.offsetHeight,
					end		:this.elements.titleBar.offsetHeight,
					onTransition	:function(fx){
						this.parent.dom.setStyle([this.elements.containerWindow],{
							height:fx.result
						});
						if(this.options.fx.shadow)
						{
							this.styles.shadow();
						}

					}.extend(this),
					onFinish	:function(fx){
						this.parent.dom.setStyle([this.elements.containerWindow],{
							height:this.elements.titleBar.clientHeight
						});
						this.parent.dom.setStyle([this.elements.content,this.elements.statusBar],{
							display:"none"
						});
						if(this.options.fx.shadow)
						{
							this.styles.shadow();
						}
						this.parent.dom.setStyle(this.elements.frontend,{
							width:"100%"
						});
						this.rolling=false;
						return (this.events.roll || function(){})();
					}.extend(this)
				});
			}
		};
		this.remove=function()
		{
			//alert(this.parent.dom.getOpacity(this.elements.shadow))
            if(this.inRemove===true){return false;}else{this.inRemove=true;}
			var e	= [];
			if(this.options.fx.fadeOut===true)
			{
				e.push(this.elements.containerWindow);
				//e.push(this.elements.shadow);
				//e.push(this.elements.modal);
			}
			if(this.options.fx.shadow)
			{
				e.push(this.elements.shadow);
			}
			if(this.options.fx.modal)
			{
				e.push(this.elements.modal);
			}
			if(this.events.remove)
			{
				this.events.remove=(this.events.remove.isArray)?this.events.remove:[this.events.remove];
				for(var i=0;i<this.events.remove.length;i++)
				{
					if(typeof this.events.remove[i]=='function')
					{
						this.events.remove[i]();
					}
				}
			}
			if(this.cancelClose===true){return false;}
			if(this.options.fx.fadeOut===true)
			{
				new this.parent.module.fx.fade().make({
					duration:500,
					end		:0,
					dom		:e,
					onFinish	:function(){
						if(this.drag){this.drag.flush();}
						for(var i in this.elements)
						{
							if(this.elements.propertyIsEnumerable(i))
							{
								this.parent.dom.remove(this.elements[i]);
								delete this.elements[i];
							}
						}
						/*			if(this.events.remove && typeof this.events.remove=="function")
						{
						return this.events.remove();
						}*/

					}.extend(this)
				});
			}
			else
			{
				if(this.drag){this.drag.flush();}
				for(var i in this.elements)
				{
					if(this.elements.propertyIsEnumerable(i))
					{
						this.parent.dom.remove(this.elements[i]);
						delete this.elements[i];
					}
				}
			}
			return false;
		};
		this.addContent=function(content)
		{
			var Rt = false;
			if(typeof content=="string")
			{
				this.elements.content.innerHTML+=content;
				//alert(this.elements.content.clientHeight)
				var Rt = true;
			}
			else if(typeof content=="object")
			{
				this.elements.content.appendChild(content);
				//alert(this.elements.content.clientHeight)
				var Rt = true;
			}
			else if(typeof content=="function")
			{
				content();
			}
			return Rt;
		};
		this.open=function(options)
		{
			options = {
				proxy:false
			}.concatMaborak(options || {});
			this.clearContent();
			if(options.proxy===false)
			{
				if(options.url)
				{
					var rpc = new this.parent.module.rpc.xmlhttp({
						url	: options.url,
						method	: "GET"
//						args	:
					});
					this.loader.show();
					rpc.callback = function(rpc)
					{
						this.loader.hide();
						var content = rpc.xmlhttp.responseText;
						var scripts = content.extractScript();
						this.addContent(content);
						scripts.evalScript();
						var forms = this.elements.content.getElementsByTagName('form');
						for(var i=0;i<forms.length;i++)
						{
							var sub = new leimnud.module.app.submit({
									form	: forms[i]
							});
							sub.callback = function(){
								//alert(sub.rpc.xmlhttp.responseText)
							};
						}
					}.extend(this);
					rpc.make();
				}
				else if(options.html)
				{
					this.addContent(options.html);
				}
				else if(options.image)
				{
					this.addContent("<div style='text-align:center;'><img src=\""+options.image+"\" /></div>");
				}

			}
			else
			{
				this.addContent(new DOM('iframe',{
					src:options.url
				},{
					border:"0px solid red",
					height:"100%",
					width:"100%"
				}));
			}
			return true;
		};
		this.clearContent=function()
		{
			this.elements.content.innerHTML="";
			return true;
		};
		this.addContentTitle=function(content)
		{
			if(typeof content=="string")
			{
				this.elements.title.innerHTML=content;
				return true;
			}
			else if(typeof content=="object")
			{
				this.elements.title.appendChild(content);
				return true;
			}
			return false;
		},
		this.addContentStatus=function(content)
		{
			if(typeof content=="string")
			{
				this.elements.status.innerHTML+=content;
			}
			else if(typeof content=="object")
			{
				this.elements.status.appendChild(content);
			}
			if(!this.showing.status){
				this.status.show();
			}
			return true;
		};
		this.clearContentStatus=function()
		{
			this.elements.status.innerHTML="";
			return true;
		};
		this.fx={
			setOpacity:function()
			{
				//alert(this.styles.fx.opacityPanel.Static/100)
				this.parent.dom.setStyle(this.elements.containerWindow,{
					opacity:this.styles.fx.opacityPanel.Move/100,
					filter:"alpha(opacity="+this.styles.fx.opacityPanel.Move+")"
				});
				if(this.options.fx.shadow===true){
					this.parent.dom.setStyle(this.elements.shadow,{
						opacity:this.styles.fx.opacityShadow.Move/100,
						filter:"alpha(opacity="+this.styles.fx.opacityShadow.Move+")"
					});
				}
			},
			unsetOpacity:function()
			{
				this.parent.dom.setStyle(this.elements.containerWindow,{
					opacity:this.styles.fx.opacityPanel.Static/100,
					filter:"alpha(opacity="+this.styles.fx.opacityPanel.Static+")"
				});
				if(this.options.fx.shadow===true){
					this.parent.dom.setStyle(this.elements.shadow,{
						opacity:this.styles.fx.opacityShadow.Static/100,
						filter:"alpha(opacity="+this.styles.fx.opacityShadow.Static+")"
					});
				}
			}
		}.expand(this);
		this.styles={
			containerWindow:function()
			{
				this.options.size.w 	= this.options.size.w || 200;
				this.options.size.h 	= this.options.size.h || 200;
				if(this.options.intoDOM)
				{
					var center		= {
						x:(((this.target().offsetWidth/2)+this.target().scrollLeft)-(this.options.size.w/2)),
						y:(((this.target().offsetHeight/2)+this.target().scrollTop)-(this.options.size.h/2))
					};
				}
				else
				{
					var scroll = this.parent.dom.getPageScroll();
					this.parent.dom.get_doc()
					var center		= {
						x:((((this.parent.dom.get_doc().clientWidth)/2)+scroll[0])-(this.options.size.w/2)),
						y:((((this.parent.dom.get_doc().clientHeight)/2)+scroll[1])-(this.options.size.h/2))
					};
				}
				if(this.options.position.center===true)
				{
					this.options.position.x = center.x;
					this.options.position.y = center.y;
				}
				else if(this.options.position.centerX===true || this.options.position.centerY===true)
				{
					this.options.position.x = ((this.options.position.centerX===true)?center.x:(this.options.position.x || 0));
					this.options.position.y = ((this.options.position.centerY===true)?center.y:(this.options.position.y || 0));
				}
				else
				{
					this.options.position.x = this.options.position.x || 0;
					this.options.position.y = this.options.position.y || 0;
				}

				if(this.options.strict_position)
				{
					this.options.position.x = (this.options.position.x<0)?0:this.options.position.x;
					this.options.position.y = (this.options.position.y<0)?0:this.options.position.y;
				}


				this.zIndex		= this.options.zIndex || this.makezIndex();
				this.parent.dom.setStyle(this.elements.containerWindow,{
					width		: this.options.size.w,
					height		: this.options.size.h,
					//border:"1px solid #A3A2BC",
					position	:"absolute",
					left		: this.options.position.x,
					top			: this.options.position.y,
					opacity		: this.styles.fx.opacityPanel.Static/100,
					filter		: "alpha(opacity="+this.styles.fx.opacityPanel.Static+")",
					zIndex		: this.zIndex
				});
				this.parent.dom.setStyle(this.elements.containerWindow,this.setStyle.containerWindow || {});
			},
			frontend:function()
			{
				this.parent.dom.setStyle(this.elements.frontend,{
					width:(this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?"100%":"auto"):"100%"
					//height:"100%",
					//position:"absolute",
					//backgroundColor:"#FFFFFF",
					//zIndex:2,
					//overflow:"hidden",
					//top:0,
					//left:0
				});
				this.parent.dom.setStyle(this.elements.frontend,this.setStyle.frontend || {});

			},
			backend:function()
			{
				this.parent.dom.setStyle(this.elements.backend,{
					//width:"100%",
					//height:"100%",
					//position:"absolute",
					//overflow:"hidden",
					//zIndex:1,
					//top:0,
					//left:0
				});
				this.parent.dom.setStyle(this.elements.backend,this.setStyle.backend || {});

			},
			loader:function()
			{
				this.parent.dom.setStyle(this.elements.loader,{
					//display:"none",
					background:"url('/images/classic/loader_B.gif')",
					backgroundRepeat:"no-repeat",
					width:32,
					height:32,
					position:"absolute",
					display:"none"
				});
				this.parent.dom.setStyle(this.elements.loader,this.setStyle.loader || {});
			},
			iframe:function()
			{
				this.parent.dom.setStyle(this.elements.iframe,{
					//width:"100%",
					//height:"100%",
					//position:"absolute",
					//overflow:"hidden",
					//zIndex:1,
					//top:0,
					//left:0
				});
				this.parent.dom.setStyle(this.elements.iframe,this.setStyle.iframe || {});

			},
			titleBar:function()
			{
				this.parent.dom.setStyle(this.elements.titleBar,{
					//position:"relative",
					display:((!this.options.titleBar)?"none":"")
					//overflow:"hidden"
					//background:"url("+this.parent.info.base+"images/panel.bg.title.gif)",
					//backgroundColor:"white",
					//backgroundRepeat:"repeat-x"
					//borderBottom:"1px solid #DBE0E5"

				});
				this.parent.dom.setStyle(this.elements.titleBar,this.setStyle.titleBar || {});
			},
			title:function()
			{
				this.parent.dom.setStyle(this.elements.title,{
					//textAlign:"center",
					//width:"100%",
					//height:"100%",
					//color:"black",
					//font:"normal 8pt Tahoma,MiscFixed",
					//fontWeight:"bold",
					//paddingLeft:5,
					//paddingTop:3,
					//zIndex:1
				});
				this.parent.dom.setStyle(this.elements.title,this.setStyle.title || {});
			},
			roll:function()
			{
				this.parent.dom.setStyle(this.elements.roll,{
					//position:"absolute",
					//top:3,
					//font:"normal 0pt tahoma",
					//padding:0,
					right:this.controlPosition(),
					height:this.controlSize.h,
					width:this.controlSize.w
					//border:"1px solid #006699",
					//zIndex:2
				});
				this.parent.dom.setStyle(this.elements.roll,this.setStyle.roll || {});
				this.parent.event.add(this.elements.roll,"mouseup",this.roll,false);
			},
			setup:function()
			{
				this.parent.dom.setStyle(this.elements.setup,{
					//position:"absolute",
					//top:3,
					//font:"normal 0pt tahoma",
					//padding:0,
					right:this.controlPosition(),
					height:this.controlSize.h,
					width:this.controlSize.w
					//border:"1px solid #006699",
					//zIndex:2
				});
				this.parent.dom.setStyle(this.elements.setup,this.setStyle.setup || {});
				this.parent.event.add(this.elements.setup,"mouseup",(this.options.setup && typeof this.options.setup=='function')?this.options.setup:function(){return false;},false);
			},
			close:function()
			{
				this.parent.dom.setStyle(this.elements.close,{
					//font	:"normal 0pt tahoma",
					//padding	:0,
					//position:"absolute",
					height	:this.controlSize.h,
					//top		:3,
					right	:this.controlPosition(),
					width	:this.controlSize.w
					//background:"url("+this.parent.info.base+"images/panel.close.static.gif)"
					//cursor	:"hand",
					//zIndex	:2
				});
				this.parent.dom.setStyle(this.elements.close,this.setStyle.close || {});
				/*this.elements.close.onmouseover=this.parent.closure({
				method:function(){
				this.parent.dom.setStyle(this.elements.close,{
				background:"url("+this.parent.info.base+"images/panel.close.over.gif)",
				cursor:"pointer"
				});
				},
				instance:this
				});
				this.elements.close.onmouseout=this.parent.closure({
				method:function(){
				this.parent.dom.setStyle(this.elements.close,{
				background:"url("+this.parent.info.base+"images/panel.close.static.gif)",
				cursor	:"pointer"
				});
				},
				instance:this
				});*/
				/*this.elements.close.onmouseup=this.parent.closure({
				method:this.remove,
				instance:this
				});*/
				this.parent.event.add(this.elements.close,"mouseup",this.remove,false);
			},
			headerBar:function()
			{
				this.parent.dom.setStyle(this.elements.headerBar,{
					//position:"relative",
					display:((!this.options.headerBar)?"none":"block")
					//overflow:"hidden"
					//background:"url("+this.parent.info.base+"images/panel.bg.title.gif)",
					//backgroundColor:"white",
					//backgroundRepeat:"repeat-x"
					//borderBottom:"1px solid #DBE0E5"

				});
				this.parent.dom.setStyle(this.elements.headerBar,this.setStyle.headerBar || {});
			},
			shadow:function()
			{
				//alert((parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2)+":"+(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"left"),10)+2))
				//alert(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2)
				this.parent.dom.setStyle(this.elements.shadow,{
					width	:this.elements.containerWindow.offsetWidth,
					height	:this.elements.containerWindow.offsetHeight,
					//position:"absolute",
					top	:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"top"),10)+2),
					left	:(parseInt(this.parent.dom.getStyle(this.elements.containerWindow,"left"),10)+2),
					//backgroundColor:"#CCCCCC",
					opacity	:this.styles.fx.opacityShadow.Static/100,
					filter	:"alpha(opacity="+this.styles.fx.opacityShadow.Static+")",
					zIndex	:this.zIndex-2
				});
				this.parent.dom.setStyle(this.elements.shadow,this.setStyle.shadow || {});
			},
			modal:function()
			{
				//if(this.parent.browser.isIE)
				//{
				//var h = (this.parent.browser.isIE)?this.target().scrollHeight:this.target().scrollHeight;
				//var ps = this.parent.dom.getPageSize();
				//var ps = this.parent.dom.getPageSize();
				//alert(this.parent.dom.get_doc());
				//alert(window.document.compatMode+":"+window.document.html+":"+window.document.body+":"+this.parent.dom.get_doc()+":");
				//alert(this.parent.dom.getDoc()+":"+this.parent.dom.getDoc().scrollHeight)
				var ps = [this.parent.dom.get_doc().scrollWidth,this.parent.dom.get_doc().scrollHeight];
				//alert(ps[1]);
				//alert(this.target().scrollHeight)
				this.parent.dom.setStyle(this.elements.modal,{
					height	:ps[1],
					width	:ps[0],
					position:"absolute",
					zIndex	:this.zIndex-3
				});
				if(!!navigator.userAgent.match(/Trident.*rv[ :]*11\./)) {
				    this.parent.dom.setStyle(this.elements.modal,{
    					opacity	:this.styles.fx.opacityShadow.Static/100
    				});
				}
				/*}
				else
				{
				this.parent.dom.setStyle(this.elements.modal,{
				height	:"100%",
				width	:"100%",
				//position:"fixed",
				zIndex	:this.zIndex-3
				});
				}*/
				if(this.options.fx.fadeIn===true)
				{
					new this.parent.module.fx.fade().make({
						duration	:1000,
						end		:this.styles.fx.opacityModal.Static/100,
						dom		:this.elements.modal
					});
				}
				else
				{
					this.parent.dom.opacity(this.elements.modal,this.styles.fx.opacityModal.Static);
				}

				this.parent.dom.setStyle(this.elements.modal,this.setStyle.modal || {});
			},
			tab:function()
			{

				this.tab = {
					display	:"horizontal",
					initIn	:20,
					step	:5,
					optHeight:20,
					widthFixed:true,
					optWidth:this.tab.width-4
				}.concatMaborak(this.tab);
				var thm = this.tab.display==="vertical"?"":"H";
				var heightContent = this.options.size.h-(this.elements.titleBar.offsetHeight+this.elements.statusBar.offsetHeight);
				//alert(this.elements.headerBar.offsetHeight+":"+this.elements.headerBar.clientHeight)
				var beginTop = this.elements.titleBar.offsetHeight+this.elements.headerBar.offsetHeight;
				var beginLeft = 4;
				var space = this.spaceOutPanel();
				this.tab.width = (this.tab.display==="vertical")?((this.tab.options)?((this.tab.width)?this.tab.width:80):0):4;
				this.parent.dom.setStyle(this.elements.tab,this.setStyle.tab || {});
				if(this.tab.options)
				{
					this.parent.dom.setStyle(this.elements.tab,{
						height	:((this.tab.display==="vertical")?heightContent:this.tab.optHeight+4+(this.parent.browser.isIE ? ((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?0:14) : 0)),
						//border	:"1px solid red",
						width	:((this.tab.display==="vertical")?this.tab.width:this.options.size.w-8),
						top	:beginTop,
						left	:((this.tab.display==="vertical")?0:4)
					});

					this.tabSelected = false;
					this.tabLastSelected = false;
					/*this.tab.initIn = this.tab.initIn || 20;
					this.tab.step	= (typeof this.tab.step=="number")?this.tab.step:5;
					this.tab.optHeight	= this.tab.optHeight || 25;
					this.tab.optWidth	= (this.tab.optWidth || (this.tab.width -4));*/
					this.tab.diffWidthBugPadding = ((this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?20:0):20);
					this.elements.tabOptions=[];
					var lastBul = 0;
					for(var i=0;i<this.tab.options.length;i++)
					{
						var opH = this.tab.initIn+(this.tab.step*i)+(this.tab.optHeight*i);
						//var opW = beginLeft+this.tab.initIn+(this.tab.step*i)+((this.tab.optWidth-this.tab.diffWidthBugPadding)*i);
						var opW = (this.tab.initIn+((this.tab.widthFixed===true)?(this.tab.optWidth*i):lastBul))+(this.tab.step*i);
						var tb = $dce("div");
						this.parent.dom.setStyle(tb,{
							padding:5,
							paddingLeft:((this.tab.display==="vertical")?15:5),
							paddingTop:((this.tab.display==="vertical")?5:4),
							//width:this.tab.optWidth-((this.tab.display==="vertical")?this.tab.diffWidthBugPadding:10),
							width:((this.tab.widthFixed)?this.tab.optWidth-((this.tab.display==="vertical")?this.tab.diffWidthBugPadding:10):(typeof(mb_strlen) !== 'undefined' ? (mb_strlen(this.tab.options[i].title || '') * 0.60) + 'em' : 'auto')),
							//height:this.tab.optHeight-((this.tab.display==="vertical")?((this.parent.browser.isIE)?0:10):0),
							//height:this.tab.optHeight-((this.parent.browser.isIE)?0:10),
							position:"absolute",
							left:((this.tab.display==="vertical")?((this.tab.width-this.tab.optWidth)-((this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?1:-1):1)):opW),
							//left:0,
							top:((this.tab.display==="vertical")?opH:0),//"auto"),
							bottom:((this.tab.display==="vertical")?"auto":0)
						});
						tb.innerHTML=this.tab.options[i].title || "";
						if(this.tab.options[i].selected===true)
						{
							this.tabSelected = i;
							tb.className="panel_tabOptionSelected"+thm+"___"+this.getTheme("tabOptionSelected");
						}
						else
						{
							tb.className="panel_tabOption"+thm+"___"+this.getTheme("tabOption");
							tb.onmouseover	= function(o,j){
								o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
								o.a.className=o.b;
							}.args({a:tb,b:"panel_tabOptionOver"+thm+"___"+this.getTheme("tabOptionOver")});
							tb.onmouseout	= function(o,j){
								o = window.event?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?j:o):j;
								o.a.className=o.b;
							}.args({a:tb,b:"panel_tabOption"+thm+"___"+this.getTheme("tabOption")});

							this.parent.dom.setStyle(tb,this.setStyle.tabOption || {});
							tb.onmouseup=function(event,tabID){
								if(this.tab.manualDisabled){return false;}
								this.tabSelected=(this.parent.browser.isIE)?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?tabID:event):tabID;
								this.makeTab();
								return false;
							}.extend(this,i);
						}
						this.elements.tab.appendChild(tb);

						lastBul+=tb.clientWidth;
						this.elements.tabOptions.push(tb);
					}
					this.tabSelected=(this.tabSelected===false)?0:this.tabSelected;
					this.makeTab();
				}
			},
			content:function()
			{
				//if(this.tab.options){this.makeTab();}
				var mgLeft = ((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"width"),10):4);
				var mgTop = ((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"height"),10)-1:4);
				this.parent.dom.setStyle(this.elements.content,{
					//	background:"",
					//color:"black",
					//font:"normal 8pt Tahoma,MiscFixed",
					//textAlign:"justify",
					//border:"1px solid #A3A2BC",
					//overflow:"scroll",
					borderTopWidth:(!this.options.titleBar)?0:'auto',
					margin:4,
					marginLeft:((this.tab.display==="vertical")?mgLeft:4),
					marginTop:((this.tab.display==="vertical")?4:mgTop)
					//marginLeft:4
				});
				//alert(this.spaceOutPanel().y)
				var tamH = this.elements.titleBar.offsetHeight+this.elements.statusBar.offsetHeight+this.elements.headerBar.offsetHeight;
				var heightContent = this.options.size.h-tamH-(this.tab.options ? 20 : 0);
				//var heightContent = this.options.size.h-(this.elements.statusBar.clientHeight+this.elements.headerBar.clientHeight);
				//alert(this.elements.titleBar.clientHeight+":"+this.elements.statusBar.offsetHeight+":"+this.elements.headerBar.clientHeight)
				this.parent.dom.setStyle(this.elements.content,this.setStyle.content || {});
				var space = this.spaceOutPanel();
				//var hC = (heightContent-((this.options.statusBarButtons || this.tab.options)?space.y:2));
				//var hC = heightContent;
				var hC = (heightContent-space.y);
				//var wC = (this.options.size.w-space.x);
				var wC = (this.options.size.w-space.x);
				//alert(this.options.size.h+"::"+tamH+":"+(tamH+space.y)+"="+hC+"???"+space.y+"***"+this.elements.titleBar.clientHeight+":"+this.elements.statusBar.offsetHeight+":"+this.elements.headerBar.clientHeight);
				this.parent.dom.setStyle(this.elements.content,{
					height	:hC,
					width	:wC
					//width	:50
				});
				this.originalContent = {
					w:wC,
					h:hC
				};
			},
			statusBar:function()
			{
				//this.parent.dom.setStyle(this.elements.statusBar,{
				//	position:"relative",
				//	width	:"100%",
				//	font	:"normal 8pt tahoma,MiscFixed",
				//	padding	:0,
				//	margin	:0,
				//	borderTop:"1px solid #A3A2BC",
				//	border:"1px solid #A3A2BC",
				//	backgroundColor:"white",
				//color:"black",
				//overflow:"hidden",
				//	height	:10
				//});
				if(!this.options.statusBar)
				{
					this.showing.status=false;
					this.parent.dom.setStyle(this.elements.statusBar,{
						display:"none"
					});
				}
				this.parent.dom.setStyle(this.elements.statusBar,this.setStyle.statusBar || {});
			},
			status:function()
			{
				this.parent.dom.setStyle(this.elements.status,{
					display:((this.options.control.resize && !this.options.statusBarButtons)?"":"none")
					//display:"none"
				});
				this.parent.dom.setStyle(this.elements.status,this.setStyle.status || {});
			},
			statusButtons:function()
			{
				this.parent.dom.setStyle(this.elements.statusButtons,{
					position:"relative",
					textAlign:"center",
					display:((this.options.statusBarButtons)?"":"none")
				});
				this.parent.dom.setStyle(this.elements.statusButtons,this.setStyle.statusButtons || {});
			},
			resize:function()
			{
				this.parent.dom.setStyle(this.elements.resize,{
					//	position:"absolute",
					//	width	:17,
					//	bottom:0,
					//	right:0,
					//font	:"normal 8pt tahoma",
					//padding	:0,
					//margin	:0,
					//borderTop:"0px solid #A3A2BC",
					//backgroundColor:"red",
					//	height:17,
					//	overflow:"hidden"
				});
				this.parent.dom.setStyle(this.elements.resize,this.setStyle.resize || {});
			},
			fx:{
				opacityShadow:{
					Static	:20,
					Move	:5
				},
				opacityModal:{
					Static	:40,
					Move	:10
				},
				opacityPanel:{
					Static	:100,
					Move	:50
				}
			},
			tabCSS:{
				over:function(event,obj){
					obj = this.parent.browser.isIE?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?obj:event):obj;
					this.parent.dom.setStyle(obj,{
						//backgroundColor:"white"
					});
				},
				out:function(event,obj){
					obj = this.parent.browser.isIE?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?obj:event):obj;
					this.parent.dom.setStyle(obj,{
						//backgroundColor:"#EEE"
					});
				},
				sover:function(event,obj){
					obj = this.parent.browser.isIE?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?obj:event):obj;
					this.parent.dom.setStyle(obj,{
						cursor:"default"
					});
				},
				sout:function(event,obj){
					obj = this.parent.browser.isIE?((!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))?obj:event):obj;
					this.parent.dom.setStyle(obj,{
					});
				},
				sel:{
					font		:"normal 8pt Tahoma,MiscFixed",
					border		:"1px solid #A3A2BC",
					borderRight	:"1px solid #FFF",
					backgroundColor	:"white",
					fontWeight	:"bold",
					textAlign	:"left",
					color		:"#000000"
				},
				def:{
					font		:"normal 8pt Tahoma,Miscfixed",
					border		:"1px solid #A3A2BC",
					margin		:0,
					fontWeight	:"normal",
					color		:"#000000",
					backgroundColor	:"EEEEEE",
					textAlign	:"left",
					cursor		:"default"
				}
			}

		}.expand(this,true);
		this.makeTheme=function()
		{
			this.themesDefault = ["processmaker_fixed","panel","firefox"];
			this.theme = this.options.theme || "firefox";
			if(this.themesDefault.inArray(this.theme))
			{
				this.theme="processmaker";
			}
		};
		this.getTheme=function(obj)
		{
			return (this.customTheme && this.customTheme[obj])?this.customTheme[obj]:this.theme;
		};
		this.command=function(fn,args,ret)
		{
			if(typeof fn==="function")
			{
				this.parent.exec(fn,args || false,ret || false,this);
			}
		};
		this.loader={
			show:function()
			{
				this.loading = true;
				var mgTop = ((this.tab.options)?parseInt(this.parent.dom.getStyle(this.elements.tab,"height"),10)-1:0);
				this.parent.dom.setStyle(this.elements.loader,{
					top		:((this.options.size.h/2)-(32/2)+mgTop),
					left	:((this.options.size.w/2)-(32/2)),
					display	:"block"
				});
			},
			hide:function()
			{
				this.parent.dom.setStyle(this.elements.loader,{
					display:"none"
				});
				this.loading = false;
			}
		}.expand(this);
		this.status={
			show:function()
			{
				//var hS=parseInt(this.parent.dom.getStyle(this.elements.statusBar,"height"),10);

				//alert(hS)
				var hhS = this.elements.status.offsetHeight;
				var hC=parseInt(this.parent.dom.getStyle(this.elements.content,"height"),10);
				this.parent.dom.setStyle([this.elements['status'],this.elements.statusBar],{
					display:""
				});
				var hS=this.elements.statusBar.offsetHeight;
				this.parent.dom.setStyle(this.elements.content,{
					height:hC-(hS-hhS)
				});
				this.showing.status = true;
			},
			hide:function()
			{
				if(this.showing.status===true)
				{
					var hS=parseInt(this.parent.dom.getStyle(this.elements.statusBar,"height"),10);
					var hC=parseInt(this.parent.dom.getStyle(this.elements.content,"height"),10);
					this.parent.dom.setStyle(this.elements.statusBar,{
						display:"none"
					});
					this.parent.dom.setStyle(this.elements.content,{
						height:hC+hS
					});
					this.showing.status = false;
				}
			},
			write:function()
			{

			}
		}.expand(this);
		this.expand(this);
	}
});
