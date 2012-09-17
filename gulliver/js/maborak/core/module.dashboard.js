/***************************************************************************
*     			      module.dashboard.js
*                        ------------------------
*   Copyleft	: (c) 2007 maborak.com <maborak@maborak.com>
*   Version		: 0.2
*
***************************************************************************/

/***************************************************************************
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
***************************************************************************/
/**
* @class drag
*/
leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.dashboard.js",
		Name	:"dashboard",
		Type	:"module",
		Version	:"0.1"
	},
	content	:function(){
		this.elements	= {};
		this.make=function(options)
		{
			this.options	= {
				drag:true,
				panel:[],
				data:[]
			}.concat(options || {});
			this.drop = new this.parent.module.drop();
			this.drop.make();

			var width	= this.options.target.offsetWidth-50;
			this.columns	= this.options.data.length;
			this.widthColumn = (width/this.columns);
			this.elements.column=[];
			this.elements.table 	= $dce('table');
			$(this.elements.table).setStyle({
				width:width,
				borderCollapse:'collapse'
			})
			this.elements.tr	= this.elements.table.insertRow(-1);
			this.options.target.append(this.elements.table);
			this.matriz = [];
			for(var i=0;i<this.columns;i++)
			{
				this.elements.column[i]=this.elements.tr.insertCell(i);
				this.parent.dom.setStyle(this.elements.column[i],{
					width	:width/this.columns,
					border	:'0px solid red',
					//position:'relative',
					verticalAlign:'top'
				});
				this.matriz.push([]);
			}
			this.parseData();
			this.drop.setArrayPositions(true);
		};

        this.parseData=function()
		{
			for(var i=0;i<this.columns;i++)
			{
				var column = this.options.data[i];
				for(var j=0;j<column.length;j++)
				{
					var wd = column[j];
					this.panel({
						target:this.elements.column[i],
						column:i,
						index:j
					}.concat(wd));
					this.matriz[i].push(j);
				}
			}
		};
		this.panel=function(options)
		{
			options = {
				style:{},
				titleBar:true
			}.concat(options || {});
			var _panel = new this.parent.module.panel();
			_panel.options={
				target:options.target,
				title	:options.title || "",
				size:{w:(options.width || this.widthColumn),h:options.height || 300},
				position:{x:0,y:0},
				/*statusBarButtons:[
				  {value:G_STRINGS.ID_REMOVE}
				],
				statusbar:true,*/
				//setup:function(){alert('Setup under construction!')},
				titleBar:(options.titleBar || false),
				control:{resize:false,roll:false,setup:false,drag:this.options.drag,close:true},
				fx:{shadow:false,opacity:false}
			};
			_panel.setStyle={
				containerWindow:(options.style.containerWindow || {}).concat({
					position:'relative',
					border:"1px solid #afafaf",
					margin:3
				}),
				content:(options.style.content || {}).concat({
					overflow:"hidden",
					margin:0,
					borderLeftWidth:0,
					borderRightWidth:0,
					borderBottomWidth:0
				}),
				titleBar:(options.style.titleBar || {}).concat({
					backgroundImage:"url("+this.parent.info.images+"grid.title.gray.gif)",
					height:16,
					backgroundPosition:"0pt -5px"
				}),
				title:(options.style.title || {}).concat({
					padding:1,
					fontWeight:"normal"
				}),
				roll:(options.style.roll || {}).concat({
					top:1
				}),
				close:(options.style.close || {}).concat({
					top:1
				}),
				setup:(options.style.setup || {}).concat({
					top:1
				})
			};
			if(options.noBg)
			{
				_panel.setStyle.content.concat({
					backgroundColor	: "#DFDFDF",
					borderWidth	: 0
				});
				_panel.setStyle.containerWindow.concat({
					backgroundColor	: "#DFDFDF"
				});
				_panel.setStyle.frontend={
					backgroundColor	: "#DFDFDF"
				};
			}
			_panel.events={
				roll:function(){return this.drop.setArrayPositions(true);}.extend(this),
				init:[function(i){
					if(this.lock===true || this.moving==true){return false;}
					var e = this.options.panel[i].panel.elements.containerWindow;
					var p;
					this.currentPhantom = p = new DOM("div",false,{
						width	: e.clientWidth,
						height	: e.clientHeight,
						border	: "1px dashed red",
						position: "relative",
						margin	: 3
					});
					if(e.nextSibling)
					{
						e.parentNode.insertBefore(p,e.nextSibling);
					}
					else
					{
						e.parentNode.appendChild(p);
					}
					//console.info(e.nextSibling)
					//console.info(e.clientWidth+":"+e.clientHeight)
				}.extend(this,this.options.panel.length)],
				move:function(i){
					var e = this.options.panel[i].panel.elements.containerWindow;
					var h = this.drop.selected;
					this.drop.captureFromArray({currentElementDrag:e});
					//console.info(h+":"+this.drop.selected);
					if(this.drop.selected!==false && this.drop.selected!==h)
					{
						//var f = this.options.panel[this.drop.selected].panel.elements.containerWindow;
						var f = this.drop.elements[this.drop.selected].element;
						this.currentPhantom.remove();
						var p;
						this.currentPhantom = p = new DOM("div",false,{
							width	: e.clientWidth,
							height	: e.clientHeight,
							border	: "1px dashed red",
							position: "relative",
							margin	: 3
						});
						if(f.nextSibling)
						{
							f.parentNode.insertBefore(p,f.nextSibling);
						}
						else
						{
							f.parentNode.appendChild(p);
						}
						//console.info(this.currentPhantom);
					}
					this.de = this.drop.selected;
					//console.info(this.drop.selected)
				}.extend(this,this.options.panel.length),
				finish:function(i){
					if(this.lock===true && this.moving==true){return false;}
					this.lock=true;
					this.moving=true;
					var p = this.parent.dom.positionRange(this.currentPhantom,true);
					var e = this.options.panel[i].panel.elements.containerWindow;
					new this.parent.module.fx.algorithm().make({
						duration 	: 400,
						end		: [p.x1,p.y1],
						transition	: "sineOut",
						begin	 	: [parseInt(e.style.left),parseInt(e.style.top)],
						onTransition	: function(fx,dom)
						{
							dom.style.left	= fx.result[0];
							dom.style.top	= fx.result[1];
						}.extend(this,e),
						onFinish:function(fx,dom,finish)
						{
							var e = dom;
							dom.style.left	= fx.options.end[0];
							dom.style.top	= fx.options.end[1];
							try{
							this.currentPhantom.parentNode.replaceChild(e,this.currentPhantom);
							}catch(e){}
							this.parent.dom.setStyle(e,{
								left:"auto",
								top:"auto",
								position:"relative"
							});
							this.drop.setArrayPositions(true);
							this.lock=false;
							this.moving=false;
							if(this.drop.selected!==false)
							{
                                var inp = this.drop.elements[this.drop.selected].value;
                                //console.log(this.options.panel[inp]);
								//console.info("========================");
								//console.info(i+":"+this.drop.selected);
								//console.info(this.drop.elements[this.drop.selected].value);
							}
						}.extend(this,e)
					});
				}.extend(this,this.options.panel.length)
			};
			_panel.events.remove = function() {
			  _panel.cancelClose = true;
			  new leimnud.module.app.confirm().make({
          label : G_STRINGS.ID_CONFIRM_REMOVE_DASHBOARD,
          action: function() {
            removeDashboard(options['class'],options['type'],options['element']);
            return true;
          }.extend(this),
          cancel: function() {
            _panel.cancelClose = true;
      		  _panel.inRemove    = false;
            return false;
          }
        });
      };
	    _panel.make();
			if(options.url)
			{
				_panel.open({url:options.url,proxy:false});
			}
			if(options.image)
			{
				_panel.open({image:options.image,proxy:false});
			}
			this.options.panel.push({
				panel	:_panel,
				index	:this.options.panel.length-1,
				column	:options.column
			});
			this.drop.register({
				element:_panel.elements.containerWindow,
				value:this.options.panel.length-1
			});
			return _panel;
		};
		this.expand(this);
	}
});
