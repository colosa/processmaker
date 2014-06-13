var menu_edit_items = [];
var stagesmap=function(){
	this.data={
		load:function(){
			var r = new leimnud.module.rpc.xmlhttp({
				url:this.options.dataServer,
				args:"action=load&data="+{uid:this.options.uid,mode:this.options.rw}.toJSONString()
			});
			r.callback=this.data.render.base;
			r.make();
		},
		render:{
			base:function(xml) {
				this.panels.editor.loader.hide();
				this.data.db=xml.xmlhttp.responseText.parseJSON().concat({});
				if (this.options.rw===true) {
				  this.menu = new this.parent.module.app.menuRight();
				  this.menu.make({
					  target:this.panels.editor.elements.content,//posiblemente hay que cambiar algo aqui para el menu contextual
					  width:150,
					  theme:this.options.theme,
					  menu:[
					    {image:"/images/add.png",text:G_STRINGS.ID_PROCESSMAP_ADD_STAGE,launch:this.addStage.extend(this)}
					  ]
				  });
				  menu_add = this.menu;
				  this.observers.menu.register(this.parent.closure({instance:this.menu,method:this.menu.remove}),this.menu);
				}
				this.data.render.title();
				this.data.render.stage();
				this.data.render.derivation();
			},
			stage:function() {
				var lngt = this.data.db.stages.length;
				for(var i =0;i<lngt;i++)
				{
					this.data.build.stage(i);
				}
			},
			title:function() {
				this.data.build.title();
			},
			derivation:function(uid,type) {
				for(var i=0;i<this.data.db.stages.length;i++)
				{
					this.data.render.lineDerivation(i);
				}
				return true;
			},
			lineDerivation:function(index) {
				var stage = this.data.db.stages[index];
				for(var j=0;j<stage.derivation.to.length;j++)
				{
					var derivation  = stage.derivation.to[j];
					if(derivation.stage==="-1" || derivation.stage==="-2")
					{
						var target=(stage.derivation.to.length>1)?'derivationBottom':'derivation';
						this.parent.dom.setStyle(stage.object.elements[target],{
							background:"url("+this.options.images_dir+derivation.stage+((target=="derivationBottom")?"bb.jpg":".gif")+"?aa="+Math.random()+")"
						});
					}
					else
					{
						var uid	 = this.tools.getIndexOfUid(derivation.stage);
						var stageF= stage.object.elements;
						var stageT= this.data.db.stages[uid].object.elements;
						var from = stage.object.elements.derivation;
						var toStage=this.data.db.stages[uid];
						var to	 = toStage.object.elements.stage;
						if(stage.derivation.type!==5)
						{
							this.parent.dom.setStyle(stage.object.elements.derivation,{
								background:"url("+this.options.images_dir+stage.derivation.type+"t.gif?aa="+Math.random()+")"
							});
						}
						else
						{
							var ij = toStage.object.inJoin;
							ij = (ij)?ij+1:1;
							toStage.object.inJoin = ij;
							this.parent.dom.setStyle(toStage.object.elements.init,{
								background:"url("+this.options.images_dir+stage.derivation.type+"t.gif?aa="+Math.random()+")",
								backgroundPosition:"2 0",
								backgroundRepeat:"no-repeat"
							});
						}
						var line = new this.parent.module.app.line({
							indexRootSize:30,
							indexRootLastSize:35,
							elements:[stageF.stage,stageT.stage],
							envolve:[
								[stageF.stage],
								[]
							],
							target:this.panels.editor.elements.content,
							color:"#228AB0",
							startA:50,
							zIndex:5
						});
						line.make();
						var cE=this.observers.lineas.register(line.update,line);
						derivation.object={
							line		: line,
							indexObserver	: cE
						};
					}
				}
			},
			deleteDerivation:function(uid,rec,spec) {
				var stage = this.data.db.stages[this.tools.getIndexOfUid(uid)];
				spec	 = (typeof spec!=="number")?false:spec;
				var deri = stage.derivation;
				for(var i=0;i<deri.to.length;i++)
				{
					if(spec===false || (spec!==false && spec===i))
					{
						deri.to[i].object.line.remove();
						this.observers.lineas.unregister(deri.to[i].object.indexObserver);
						if(deri.type===5)
						{
							var toStage = this.data.db.stages[this.tools.getIndexOfUid(deri.to[i].stage)];
							toStage.object.inJoin = toStage.object.inJoin-1;
							if(toStage.object.inJoin===0)
							{
								this.parent.dom.setStyle(toStage.object.elements.init,{
									backgroundPosition:"0 0",
									background:""
								});
							}
						}
					}
				}
				this.parent.dom.setStyle(stage.object.elements.derivation,{
					background:""
				});
				stage.derivation={to:[]};
				if(rec)
				{
					var tdb = this.data.db.stages;
					for(var i=0;i<tdb.length;i++)
					{
						var der = tdb[i].derivation.to || [];
						for(var j=0;j<der.length;j++)
						{
							if(der[j].stage===uid)
							{
								this.data.render.deleteDerivation(tdb[i].uid,false,j);
							}
						}
					}
				}
			}
		},
		build:{
		  title:function(index)
			{
				if(this.data.db.title)
				{
					var title=this.data.db.title;
					var t = document.createElement("div");
					t.className="processmap_title___"+this.options.theme;
					this.parent.dom.setStyle(t,{
						top:title.position.y,
						left:title.position.x,
						cursor:"default"
					});
					t.innerHTML=title.label;
					this.panels.editor.elements.content.appendChild(t);
					title.object={
						elements:{
							label:t
						}
					}
				}
			},
			stage:function(index)
			{
				var options 	= this.data.db.stages[index];
				var db		= this.data.db, stage=db.stages[index];
				var derivation 	= stage.derivation.to;
				var a = document.createElement("div");
				a.className="processmap_task___"+this.options.theme;
				this.parent.dom.setStyle(a,{
					top:options.position.y,
					left:options.position.x,
					cursor:((this.options.rw===true)?"move":"default"),
					backgroundColor:(options.color ? options.color : 'auto')
				});
				var b = document.createElement("div");
				b.className="processmap_task_label___"+this.options.theme;
				this.parent.dom.setStyle(b,{
					cursor:((this.options.rw===true)?"move":"default")
				});
				b.innerHTML = options.label;

				var c = document.createElement("div");
				this.parent.dom.setStyle(c,{
					position:"absolute",
					top: options.position.y+38,
					left:options.position.x+(81-12),
					height:25,
					width:25,
					border:"0px solid black",
					overflow:"hidden",
          cursor:'default',
					zIndex:9
				});
				var d = document.createElement("div");
				this.parent.dom.setStyle(d,{
					position:"absolute",
					top: options.position.y+49,
					left:options.position.x+(93),
					height:38,
					width:38,
					border:"0px solid black",
					overflow:"hidden",
					zIndex:9
				});
				var t = document.createElement("div");
				this.parent.dom.setStyle(t,{
					position:"absolute",
					top:options.position.y-30,
					left:options.position.x+(81-14),
					height:30,
					width:30,
					overflow:"hidden",
					zIndex:9
				});
				if(this.options.rw===true)
				{
				  this.menuEdit = new this.parent.module.app.menuRight();
				  this.menuEdit.make({
				  	target:a,
				  	width:201,
				  	theme:this.options.theme,
				  	menu:[
				  	{image:"/images/edit.gif",text:G_STRINGS.ID_PROCESSMAP_EDIT,launch:function(event,index){
					  	var panel;
					  	this.tmp.stagePanel = panel = new leimnud.module.panel();
					  	panel.options={
					  		limit	:true,
					  		size	:{w:450,h:160},
					  		position:{x:50,y:50,center:true},
					  		title	:G_STRINGS.ID_PROCESSMAP_EDIT+": "+stage.label,
					  		theme	:this.options.theme,
					  		control	:{close:true,resize:false},fx:{modal:true},
					  		statusBar:false,
					  		fx	:{modal:true}
					  	};
					  	panel.make();
					  	panel.loader.show();
					  	var r;
					  	panel.currentRPC = r = new leimnud.module.rpc.xmlhttp({
					  		url:this.options.dataServer,
					  		args:"action=editStage&data="+{
					  			stg_uid	:stage.uid,
					  			pro_uid	:this.options.uid,
					  			theindex:index
					  		}.toJSONString()
					  	});
					  	r.callback=function(rpc,panel)
					  	{
					  		panel.loader.hide();
					  		var scs = rpc.xmlhttp.responseText.extractScript();
					  		panel.addContent(rpc.xmlhttp.responseText);
					  		scs.evalScript();
					  	}.extend(this,panel);
					  	r.make();
					  }.extend(this,index)},
					  {image:"/images/task.gif",text:G_STRINGS.ID_PROCESSMAP_TASKS_ASSIGNED,launch:function(event,index){
					  	var panel;
					  	this.tmp.stagePanel = panel = new leimnud.module.panel();
					  	panel.options={
					  		limit	:true,
					  		size	:{w:450,h:400},
					  		position:{x:50,y:50,center:true},
					  		title	:G_STRINGS.ID_PROCESSMAP_TASKS_ASSIGNED_FOR+": "+stage.label,
					  		theme	:this.options.theme,
					  		control	:{close:true,resize:false},fx:{modal:true},
					  		statusBar:false,
					  		fx	:{modal:true}
					  	};
					  	panel.make();
					  	panel.loader.show();
					  	var r;
					  	panel.currentRPC = r = new leimnud.module.rpc.xmlhttp({
					  		url:this.options.dataServer,
					  		args:"action=tasksAssigned&data="+{
					  			stg_uid	:stage.uid,
					  			pro_uid	:this.options.uid,
					  			theindex:index
					  		}.toJSONString()
					  	});
					  	r.callback=function(rpc,panel)
					  	{
					  		panel.loader.hide();
					  		var scs = rpc.xmlhttp.responseText.extractScript();
					  		panel.addContent(rpc.xmlhttp.responseText);
					  		scs.evalScript();
					  	}.extend(this,panel);
					  	r.make();
					  }.extend(this,index)},
					  {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_STAGE,launch:this.parent.closure({instance:this,method:function(index){
				  		var data = this.data.db.stages[index];
				  		new this.parent.module.app.confirm().make({
				  			label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_STAGE+data.label,
				  			action:function()
				  			{
				  				var r = new leimnud.module.rpc.xmlhttp({
				  					url:this.options.dataServer,
				  					args:"action=deleteStage&data="+{pro_uid:this.options.uid,stg_uid:data.uid}.toJSONString()
				  				});
				  				r.callback = function() {
				  				  this.panels.editor.clearContent();
				  				  this.data.load();
				  			  }.extend(this);
				  				r.make();
				  			}.extend(this)
				  		});
				  	},args:index})}
				  	]
				  });
				  menu_edit_items.push(this.menuEdit);	  
				  this.observers.menu.register(this.menuEdit.remove,this.menuEdit);
				}
				this.panels.editor.elements.content.appendChild(a);
				a.appendChild(b);
				this.panels.editor.elements.content.appendChild(c);
				this.panels.editor.elements.content.appendChild(d);
				this.panels.editor.elements.content.appendChild(t);

				options.object={
					elements:{
						stage	: a,
						label	: b,
						derivation: c,
						derivationBottom: d,
						init	: t
					}
				};
				options.object.dropIndex=this.dropables.derivation.register({
					element	: a,
					value	: index,
					events	: {
						over	:this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){
							this.parent.dom.setStyle(e,{
								border:"1px solid #006699"
							});
						},args:[a,options,index]}),
						out		:this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){
							this.parent.dom.setStyle(e,{
								border:"0px solid #006699"
							});
						},args:[a,options,index]})
					}
				});
				if(this.options.rw===true)
				{
					options.object.drag = new this.parent.module.drag({
						link:{
							elements:a,
							ref:[a,c,d,t]
						},
						limit:true
					});
					this.observers.lineas.register(this.parent.closure({instance:options.object.drag,method:function(){}}),options.object.drag);
					options.object.drag.events={
						move	:this.parent.closure({instance:this,method:function(div,divC,uid,drag) {
						  options.object.drag.observer.update();
							var db = this.data.db;
						  },args:[a,c,index,options.object.drag]}),

						finish	:this.parent.closure({instance:this,method:function(div,divC,uid,drag){
							if(!drag.moved){return false;}
							var pos  = this.parent.dom.position(div);
							var h=pos;
							var data = this.data.db.stages[uid];
							var db = this.data.db;
							var r = new leimnud.module.rpc.xmlhttp({
									url:this.options.dataServer,
									args:"action=saveStagePosition&data="+{uid:data.uid,position:pos}.toJSONString()
								});
							r.make();
						},args:[a,c,index,options.object.drag]})
					};
					options.object.drag.make();
				}
			},
			derivation:function(options)
			{
				tt=options;
				var index=this.tools.getIndexOfUid(options.stg_uid);
				var from=this.data.db.stages[index];
				this.data.render.deleteDerivation(options.stg_uid);
				var affe=options.data;
				from.derivation.type=options.type;
				for(var i=0;i<affe.length;i++)
				{
					from.derivation.to[i]={
						stage:affe[i]
					};
				}
				this.data.render.lineDerivation(index);
			}
	  }
	}.expand(this,true);
	this.tools={
		getIndexOfUid:function(uid)
		{
			for(var i=0;i<this.data.db.stages.length;i++)
			{
				if(this.data.db.stages[i].uid===uid){return i;}
			}
		},
		getUidOfIndex:function(index)
		{
			return this.data.db.stages[index].uid || false;
		}
	}.expand(this);
	this.expand(this);
};

stagesmap.prototype={
	parent:leimnud,
	tmp:{},
	info:{
		name		: "stagesmap"
	},
	panels:{},dragables:{},dropables:{},
	make:function() {
		this.options = {
			theme	:"firefox",
			rw	:true,
			mi  :true,
			hideMenu:true
		}.concat(this.options || {});
		this.options.target = this.parent.dom.element(this.options.target);
		if(!this.validate()){return false;}
		this.observers = {
			menu 		: this.parent.factory(this.parent.pattern.observer,true),
			lineas 		: this.parent.factory(this.parent.pattern.observer,true)
		};
		this.dropables.derivation = new this.parent.module.drop();
		this.dropables.derivation.make();
		if (this.options.rw === true) {
		  var bd = this.parent.dom.capture("tag.body 0");
		  var sm = this.parent.dom.element("pm_submenu");
		  this.parent.dom.setStyle(bd,{
		  	backgroundColor:"buttonface"
		  });
		  this.parent.dom.setStyle(sm,{
		  	height:25
		  });
	  }
		this.panels.editor=new leimnud.module.panel();
		this.panels.editor.options={
			limit:true,
			size:{w:this.options.size.w,h:this.options.size.h - 10},
			position:{x:0,y:0},
			title:"",
			titleBar:false,
			control:{
				resize:false
			},
			fx:{
				opacity:false,
				shadow:false,
				blinkToFront:false
			},
			theme:this.options.theme,
			target:this.options.target,
			modal:true,
			limit:true
		};
		this.panels.editor.setStyle={
			content:{
					background:"white url('"+this.options.images_dir+"bg_pm.gif') repeat fixed",
					backgroundPosition:"10 0"
				},
			containerWindow	:{borderWidth:0,padding:0,backgroundColor:"buttonface"},
			titleBar	:{background:"transparent",borderWidth:0,height:5},
			frontend	:{backgroundColor:"buttonface"},
			backend		:{backgroundColor:"buttonface"},
			status		:{textAlign:"center"}
		};
		this.panels.editor.make();
		this.panels.editor.loader.show();
		this.data.load();
	},
	validate:function() {
		return (!this.options.target || !this.options.dataServer || !this.options.lang)?false:true;
	},
	addStage:function(evt) {
		var m = this.menu.cursor;
		var cpos = this.parent.dom.position($('sm_target'));
		var scl = {
			x:$('sm_target').scrollLeft,
			y:$('sm_target').scrollTop
		};
		var pos = {x:scl.x+(m.x-cpos.x)+100,y:scl.y+(m.y-cpos.y)};
		var r = new leimnud.module.rpc.xmlhttp({
			url:this.options.dataServer,
			args:"action=addStage&data="+{uid:this.options.uid,position:pos}.toJSONString()
		});
		r.callback = function() {
		  this.panels.editor.clearContent();
		  this.data.load();
		}.extend(this);
		r.make();
	}
};