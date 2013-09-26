/***************************************************************************
*     				  		  module.drag.js
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
		File	:"module.drag.js",
		Name	:"drag",
		Type	:"module",
		Version	:"0.1"
	},
	content	:function(options){
		this.options	= {
			limitbox:false
		}.concat(options || {});
		this.loaded		= false;
		this.eventHandlers=[];
		this.cursor 	= "move";
		this.uid		= this.parent.tools.createUID();
		this.make=function()
		{
			//alert(this.options.elements.isArray);
			//this.options.elements=(this.options.elements && !this.options.elements.length)?[this.options.elements]:this.options.elements
			this.options.elements=this.set();
			this.options.fx = {
				type:"simple",
				target:document.body
			}.concat(this.options.fx || {});
			this.events =this.events || {};
			//alert(this.options.elements.length);
			var elements=(this.options.elements || []).length;
			this.flagEvents=[];
			var oThis = this;
			for(var i=0;i<elements;i++)
			{

				var revent=this.parent.event.add(this.options.elements[i],"mousedown",this.parent.closure({
					method	:this.onInit,
					instance:this,
					event	:true,
					args	:[i]
				}),false);
				/*var oj=this.options.elements[i];
				var revent = this.parent.event.add(this.options.elements[i],"mousedown",function(rrr){
				oThis.onInit(rrr || window.event,i,oj);
				},false);*/

				this.flagEvents.push(revent);
				//this.parent.event.add(this.options.elements[i],"mousedown",x,false);
			}
			//alert(5);
		};
		this.set=function()
		{
			if(this.options.elements)
			{
				this.type="simple";
				/*return ((!this.options.elements.isArray && (this.options.elements.isObject || (this.parent.browser.isIE && !this.options.elements.isObject))) || this.options.elements.isArray)?this.options.elements:[this.options.elements];*/
				return (this.options.elements.isArray)?this.options.elements:[this.options.elements];
			}
			else if(this.options.group)
			{
				this.type="group";
				/*return ((!this.options.group.isArray && (this.options.group.isObject || (this.parent.browser.isIE && !this.options.group.isObject))) || this.options.group.isArray)?this.options.group:[this.options.group];*/
				return (this.options.group.isArray)?this.options.group:[this.options.group];
			}
			else if(this.options.link)
			{
				this.type="link";
				this.linkRef=((!this.options.link.ref.isArray && (this.options.link.ref.isObject || (this.parent.browser.isIE && !this.options.link.ref.isObject))) || this.options.link.ref.isArray)?this.options.link.ref:[this.options.link.ref];
				/*return ((!this.options.link.elements.isArray && (this.options.link.elements.isObject || (this.parent.browser.isIE && !this.options.link.elements.isObject))) || this.options.link.elements.isArray)?this.options.link.elements:[this.options.link.elements];*/
				return (this.options.link.elements.isArray)?this.options.link.elements:[this.options.link.elements];
			}
			else
			{
				return [];
			}
		};
		this.onInit=function(fEvent,elNum)
		{
			//window.status="onInit:=> "+arguments.length+":::"+event+":"+elNum+":"+elem;
			//window.status="onInit:=> "+arguments.length+":"+arguments[0]+":"+arguments[1]+":"+arguments[2];
			if(this.noDrag===true)
			{
				return false;
			}
			if(this.lock===true){return false;}
			this.lock=true;
			if(arguments.length<2 && this.parent.browser.isIE)
			{
				//element	= this.options.elements[elNum];
				elNum	= fEvent;
				fEvent	= window.event;
			}
			//window.status="onInit:=> "+fEvent+":"+elNum+":"+elem;
			//window.status=elNum+":"+element;
			this.currentElementInArray=elNum;
			var element=this.options.elements[elNum];
			this.currentElementDrag = element;
			var position;
			this.cursorStart	= this.parent.dom.mouse(fEvent);
			//	window.status=this.cursorStart.toStr()
			if(this.type=="simple")
			{
				if(this.options.fx.type=="simple")
				{
					this.probeAbsolute(element);
					this.elementStart	={
						x:parseInt(this.parent.dom.getStyle(element,"left"),10),
						y:parseInt(this.parent.dom.getStyle(element,"top"),10)
					};
				}
				else if(this.options.fx.type=="clone")
				{
					var m = this.parent.dom.mouse(fEvent);
					var ps = this.parent.dom.position(this.options.fx.target);

					var pos = {x:(m.x-ps.x),y:(m.y-ps.y)};
					window.status=pos.x+":"+pos.y+"::::"+m.x+":"+m.y+":::"+ps.x+":"+ps.y;


					var clo = element.cloneNode(true);
					this.currentElementDrag = clo;
					var ev = this.parent.event.db[this.flagEvents[elNum]];
					this.parent.event.remove(clo,ev._event_,ev._function_);
					this.parent.dom.setStyle(clo,{
						visibility:"hidden"
					});
					this.options.fx.target.appendChild(clo);
					this.parent.dom.setStyle(clo,{
						position:"absolute",
						left:pos.x+(this.options.fx.target.scrollLeft || 0)-(clo.clientWidth/2),
						top:pos.y+(this.options.fx.target.scrollTop || 0)-(clo.clientHeight/2),
						zIndex:this.options.fx.zIndex || 1000,
						visibility:"visible"
					});
					this.elementStart	={
						x:parseInt(this.parent.dom.getStyle(clo,"left"),10),
						y:parseInt(this.parent.dom.getStyle(clo,"top"),10)
					};
					element = clo;
					this.parent.dom.opacity(clo,33);
					//alert(element+":"+ps.x+":"+ps.y);
				}
				//alert(this.parent.dom.getStyle(element,"z-index"));
			}
			else if(this.type=="group")
			{
				//this.probeAbsoluteGroup();
				this.elementStart=[];
				for(var i=0;i<this.options.elements.length;i++)
				{
					position = this.parent.dom.position(this.options.elements[i],false,true);
					this.elementStart[i]={
						x:position.x,
						y:position.y
					};
				}
				this.absolutizeGroup();
			}
			else if(this.type=="link")
			{
				//this.probeAbsoGroup();
				this.elementStart=[];
				for(i=0;i<this.linkRef.length;i++)
				{
					var position = this.parent.dom.position(this.linkRef[i],false,true);
					//console.info(position)
					this.elementStart[i]={
						x:position.x,
						y:position.y
					};
				}
				this.absolutizeLink();
			}
			this.parent.event.add(document,"mousemove",this.parent.closure({
				method:this.onMove,
				instance:this,
				event:true,
				args:[elNum,element,this.parent.event.db.length]
			}),true);
			//window.status=this.parent.event.db.length;
			this.parent.event.add(document,"mouseup",this.parent.closure({
				method:this.onFinish,
				instance:this,
				event:true,
				args:[elNum,element,this.parent.event.db.length]
			}),true);
			if(window.event)
			{
				window.event.cancelBubble=true;
				window.event.returnValue=false;
			}
			else
			{
				fEvent.preventDefault();
			}
			this.parent.dom.bubble(false,fEvent);
			this.launchEvents(this.events.init);
			this.moved=false;
		};
		this.onMove=function(event,elNum,element)
		{
			//window.status=elNum+":"+element;
			//window.status="Mouse:"+this.parent.dom.mouse(event).toStr()+"__Element:"+this.parent.dom.position(element).toStr()+"__Range:"+this.parent.dom.positionRange(element).toStr();
			var cursor,rG,tL,tT;
			cursor = this.currentCursorPosition=this.parent.dom.mouse(event);
			if(this.type=="simple")
			{
				//window.status=this.parent.dom.mouse(event).toStr();//var element=this.parent.event.dom(event);
				//window.status=event+":"+element.style.left+":"+element.style.top+"::"+element;
				/*this.parent.dom.setStyle(element,{
				left:this.elementStart.x+(cursor.x-this.cursorStart.x),
				top:this.elementStart.y+(cursor.y-this.cursorStart.y)
				});*/
				rG={
					l:true,
					t:true
				};
				tL = parseInt(this.elementStart.x+(cursor.x-this.cursorStart.x),10);
				tT = parseInt(this.elementStart.y+(cursor.y-this.cursorStart.y),10);
				if((tL<0 || this.options.limit==="x") || (this.options.limitbox && (tL+element.clientWidth)>this.options.limitbox.clientWidth)){rG.l=false;}
				if((tT<0 || this.options.limit==="y") || (this.options.limitbox && (tT+element.clientHeight)>this.options.limitbox.clientHeight)){rG.t=false;}
				this.currentX = tL;
				this.currentY = tT;
				//var tL=parseInt(this.elementStart.x+(cursor.x-this.cursorStart.x),10);
				//var tT=parseInt(this.elementStart.y+(cursor.y-this.cursorStart.y),10);
				if(rG.l || !this.options.limit)
				{
					this.parent.dom.setStyle(element,{
						left:tL
					});
				}
				if(rG.t || !this.options.limit)
				{
					this.parent.dom.setStyle(element,{
						top:tT
					});
				}
			}
			else if(this.type=="group")
			{
				for(var i=0;i<this.options.elements.length;i++)
				{
					this.parent.dom.setStyle(this.options.elements[i],{
						left:this.elementStart[i].x+(cursor.x-this.cursorStart.x),
						top:this.elementStart[i].y+(cursor.y-this.cursorStart.y)
					});
				}
			}
			else if(this.type=="link")
			{
				if(this.options.limit===true)
				{
					rG={
						l:true,
						t:true
					};
					for(i=0;i<this.linkRef.length;i++)
					{
						tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);
						tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);
						if(tL<0){rG.l=false;}
						if(tT<0){rG.t=false;}
					}
					for(i=0;i<this.linkRef.length;i++)
					{
						tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);
						tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);
						if (tL > (screen.width - (element.clientWidth + 25))) {
			                rG.l = false;
			            }
			            if (tT > (screen.height - (element.clientHeight + 200))) {
			                rG.t = false;
			            } 
						if(rG.l)
						{
							this.parent.dom.setStyle(this.linkRef[i],{
								left:tL
							});
						}
						if(rG.t)
						{
							this.parent.dom.setStyle(this.linkRef[i],{
								top:tT
							});
						}

					}

				}
				else
				{
					for(i=0;i<this.linkRef.length;i++)
					{
						tL=parseInt(this.elementStart[i].x+(cursor.x-this.cursorStart.x),10);
						tT=parseInt(this.elementStart[i].y+(cursor.y-this.cursorStart.y),10);
						this.parent.dom.setStyle(this.linkRef[i],{
							left:tL,
							top:tT
						});
					}
				}
			}
			if(window.event)
			{
				window.event.cancelBubble=true;
				window.event.returnValue=false;
			}
			else
			{
				event.preventDefault();
			}
			this.launchEvents(this.events.move);
			//alert(4);
			//alert(this.parent.dom.mouse(event).toStr());
		};
		this.onFinish=function(event,elNum,element,hand)
		{
			//window.status=elNum+":"+element;
			//alert(this.elementStart[0].x)
			if(arguments.length<4 && this.parent.browser.isIE)
			{
				hand	= element;
				element	= elNum;
				elNum	= event;
				event	= window.event;
			}

			this.cursorEnd	= this.parent.dom.mouse(event);
			//window.status="Finish=> "+event+":"+elNum+":"+element+":"+hand;
			//alert(this.parent.event.db[hand].toStr());
			//var handler = this.parent.event.db[hand]._function_;
			//ert(this.cursorStart.x+":"+this.cursorStart.y+":"+this.cursor);
			this.moved = ((this.cursorStart.x !== this.cursorEnd.x) || (this.cursorStart.y !== this.cursorEnd.y))?true:false;
			this.launchEvents(this.events.finish);
			this.parent.event.remove(document,"mouseup",this.parent.event.db[hand]._function_,true,hand);
			this.parent.event.remove(document,"mousemove",this.parent.event.db[hand-1]._function_,true,hand-1);
			this.lock=false;
		};
		this.flush=function()
		{
			this.parent.event.flushCollection(this.flagEvents);
			this.flagEvents = [];
		};
		this.probeAbsolute=function(d0m)
		{
			if(this.parent.dom.getStyle(d0m,"position")!="absolute")
			{
				
				var position=this.parent.dom.position(d0m,false,true);
				//console.info(position);
				//alert(position.x+":"+position.y)
				this.parent.dom.setStyle(d0m,{
					position:'absolute',
					left	:position.x,
					top		:position.y,
					cursor	:this.cursor
				});
			}
		};
		this.absolutizeGroup=function()
		{
			for(var i=0;i<this.options.elements.length;i++)
			{
				if(this.parent.dom.getStyle(this.options.elements[i],"position")!="absolute")
				{
					this.parent.dom.setStyle(this.options.elements[i],{
						position:'absolute',
						left	:this.elementStart[i].x,
						top		:this.elementStart[i].y,
						cursor	:this.cursor
					});
				}
			}
		};
		this.absolutizeLink=function()
		{
			for(var i=0;i<this.options.elements.length;i++)
			{
				this.parent.dom.setStyle(this.options.elements[i],{cursor:this.cursor});
			}
			for(i=0;i<this.linkRef.length;i++)
			{
				if(this.parent.dom.getStyle(this.linkRef[i],"position")!="absolute")
				{
					this.parent.dom.setStyle(this.linkRef[i],{
						position:'absolute',
						left	:this.elementStart[i].x,
						top	:this.elementStart[i].y
					});
				}
			}
		};
		this.launchEvents=function(event)
		{
			if(event && event.isArray===true)
			{
				for(var i=0;i<event.length;i++)
				{
					if(typeof event[i]=="function")
					{
						event[i]();
					}
				}
			}
			else
			{
				return (event)?event():false;
			}
		};
	}
});

