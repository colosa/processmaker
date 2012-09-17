/*

1.- leer un template
- Crear array de posiciones.   Cada ID encontrado una posision. (WTF. como encontrar todos los elementos con ID)
2.- accordeonasdasd.... en DIV  panel...
3.- * <name campo='nombre' x='11' y='11'>

* <name campo='nombre' x='11' y='11'>

4.-  arrastrar soltar NO. right click Elementos.


i: .xml, .tpl

o: .xml, .tpl, .html





*/
var $a='0123456789ABCDEF';
var $b=function()
{
    $g = $a.split('');
    return $g.random()+$g.random()+$g.random()+$g.random()+$g.random()+$g.random();
};
leimnud.Package.Public({
    info	:{
        Class	:"maborak",
        File	:"module.dynaform.js",
        Name	:"dynaform",
        Type	:"module",
        Version	:"0.1"
    },
    content	:function()
    {
    	this.tmp = {};
        this.make=function(options)
        {
            this.options = {
                template :'default.tpl',
                sectionName:'section',
                target   :document.body,
                points   :{},
                dom      :{},
				drop	 :{},
				drag	 :{},
				menu	 :{},
				debug	 :false,
				editor	 :{showed:false},
				observers:{},
				panel	 :{},
				tmp		 :{},
				style:{
					section:"#B3B3BF",
					new_section:{border:'1px solid green',margin:4,minHeight:20},
					add_section:{position:'relative',border:'1px solid #B3B3BF',margin:4,minHeight:20}
				}
            }.concat(options || {});
			this.db=[];
			this.debug = new this.parent.module.debug(this.options.debug || false);
			this.options.observers['menu'] = new this.parent.pattern.observer();
            this.options.target.setStyle({
				textAlign:'center'
            });
			this.options.target.append(
				table =new DOM('table',{align:'center'},{width:'99%'})
			);
			var tr = table.insertRow(-1);
			$(tr).append(
				new DOM('td').append(
					new DOM('div',{className:'boxTop'}).append(
						new DOM('div',{className:'a'}),
						new DOM('div',{className:'b'}),
							new DOM('div',{className:'c'})
						),
						new DOM('div',{className:'boxContentNormal',id:"bcn"},{minHeight:this.options.target.clientHeight-70,paddingBottom:20}).append(
							this.options.dom.body = new DOM('div',{id:'root'},{border:'1px solid '+this.options.style.section,padding:5,minHeight:(this.options.target.clientHeight-80)})
						),
						new DOM('div',{className:'boxBottom'}).append(
							new DOM('div',{className:'a'}),
							new DOM('div',{className:'b'}),
							new DOM('div',{className:'c'})
					)
				)
			);
//			this.options.dom.body = this.options.target;
/*            this.options.target.append(
	            this.options.dom.body   = new DOM('div')
            );*/
//            this.options.dom.header = new DOM('div');
/*            this.setStyles();
            this.dynas=[];
            for(var i=0;i<12;i++)
            {
                var d;
                this.options.dom.header.append(
     	           d = new DOM('input',{type:'button',value:i},{backgroundColor:'#'+$b(),width:30,margin:2,border:'1px solid red'})
                );
                this.dynas.push(d);
            }*/
            this.load({
				template:this.options.template,
				xmlform:this.options.xmlform
			});
			this.buttons_set();
            return this;
        };
        this.buttons_set=function()
        {
			//alert(this.options.buttons.xml);
        	this.options.buttons.xml.onmouseup=function()
        	{
        		this.show_editor('xml');
        		//alert(this.options.window.editor)
        	}.extend(this);
        	this.options.buttons.template.onmouseup=function()
        	{
        		this.show_editor('template');
        	}.extend(this);
        };
        this.show_editor=function(b)
        {
        	//alert(this.options.editor.selected+":"+b);
        	this.options.window.textarea.value='';
        	if(this.options.editor.showed===true && this.options.editor.selected==b)
        	{
        		if(b=='xml')
        		{
        			//this.options.buttons.template.disabled=false;
        			//this.options.buttons.xml.disabled=true;
        			this.options.buttons.xml.className="dbo";
        			this.options.buttons.xml.dv='0';
        			this.options.buttons.template.dv='0';
        			this.options.buttons.template.className="dbs";
        		}
        		else
        		{
        			//this.options.buttons.template.disabled=true;
        			//this.options.buttons.xml.disabled=false;
        			this.options.buttons.xml.dv='0';
        			this.options.buttons.template.dv='0';
        			this.options.buttons.xml.className="dbs";
        			this.options.buttons.template.className="dbo";
        		}
        		this.options.window.editor.move({y:this.options.sizes.pi});
        		this.options.editor.showed=false;
        		this.options.editor.selected=b;
        	}
        	else
        	{
        		if(b=='xml')
        		{
        			//this.options.buttons.template.disabled=false;
        			//this.options.buttons.xml.disabled=true;
        			this.options.window.textarea.value=this.xmlform.serialize().sReplace('><','>\n<');

        			this.options.buttons.xml.dv='1';
        			this.options.buttons.template.dv='0';
        			this.options.buttons.xml.className="dbo";
        			this.options.buttons.template.className="dbs";
        		}
        		else
        		{
        			this.options.window.textarea.value='b';
        			//this.options.buttons.template.disabled=true;
        			//this.options.buttons.xml.disabled=false;
        			this.options.buttons.xml.dv='0';
        			this.options.buttons.template.dv='1';
        			this.options.buttons.xml.className="dbs";
        			this.options.buttons.template.className="dbo";
        		}
        		this.options.window.editor.move({y:this.options.sizes.pv,onFinish:function(){this.options.window.textarea.focus();}.extend(this)});
        		this.options.editor.showed=true;
        		this.options.editor.selected=b;
        	}
        };
        this.setStyles=function()
        {
/*            this.options.dom.actions.setStyle({
                border:'1px solid red',
                position:'absolute',
                width:200,
                height:400
            });
            this.options.dom.header.setStyle({
                border:'1px solid red',
                position:'relative',
                top:100,
                left:250,
                width:300,
                height:100
            });*/
            this.options.dom.body.setStyle({
                border:'1px solid red',
                position:'relative',
                top:0,
                left:250,
                width:600,
                height:400
            });
        };
        this.load=function(options)
        {
        	options = {
        		template:"empty.tpl",
				xmlform:"empty.xml"
        	}.concat(options || {});
            var r = new this.parent.module.rpc.xmlhttp({
                url:options.template
            });
            r.callback=function(rpc){
				this.xmlform = new this.parent.module.xmlform();
				this.xmlform.make({
					file	:options.xmlform,
					target	:this.options.target_info,
					debug	:this.options.debug,
					onload	:function(){
						this.editor();
						this.xmlform.tag_edit(this.xmlform.show_dyna(),'dyna_root');
						this.parse_elements();
					}.extend(this)
				});
                this.build({
                    template:rpc.xmlhttp.responseText
                });
            }.extend(this);
            r.make();
        };
        this.editor=function()
        {
        	var e =$dce("div",{innerHTML:"asd"},{height:100,width:1212,backgroundColor:"red",position:'absolute',zIndex:123123,top:0});
        	document.body.appendChild(e);
        };
        this.build=function(o)
        {
            this.options.dom.body.innerHTML=o.template;
            //this.options.dom.body.append(this.options.dom.header);
            var t = this.tplFirstChild();
            this.tplSetPoints({
                html:t
            });

            this.tplSetDropables();



            this.menu_root = new this.parent.module.app.menuRight();
			this.options.observers['menu'].register(this.menu_root.remove,this.menu_root);
            this.menu_root.make({
                target:this.options.dom.body,
                width:201,
                theme:'light',
                menu:[
	                {text:'New Dynaform',launch:function(){}},
	                {text:'New Section',launch:this.add_section.args(this.options.dom.body)},
   		            {text:'Save Dynaform',launch:function(){}},
   		            {separator:true},
   		            {text:'Edit XML',launch:function(){this.show_editor('xml');}.extend(this)},
   		            {text:'Edit Template',launch:function(){this.show_editor('template');}.extend(this)}
                ]
            });
        };
        this.tplFirstChild=function()
        {
            //return this.options.dom.body.firstChild;
            return this.options.dom.body;
        };
        this.tplSetPoints=function(o)
        {
            var t = o.html;
            var c = t.childNodes.length || 0;
			//alert(o.html.childNodes)
            for(var i=0;i<c;i++)
            {
                var e = $(t.childNodes[i]);
                var p = this.isPoint(e);
                if(p)
                {
                	if(e.parentNode.id!='root')
                	{
	                	/*e.parentNode.setStyle({
	                		border:"1px dashed #EEE"
	                	});*/
                	}
					e.setStyle({
						position:'relative',
						border:"1px solid "+this.options.style.section,
						margin:4,
						marginTop:((e.parentNode.id!='root')?4:0),
						minHeight:20
					});
                    this.options.points[p]=e;
                }
                if(e.childNodes && e.childNodes.length >0)
                {
                    this.tplSetPoints({
                        html:e
                    });
                }
            }
        };
        this.isPoint=function(e)
        {
        	if(e[this.options.sectionName]){
        		return e[this.options.sectionName];
        	}
        	if(e.attributes)
        	{
        		for(var i=0;i<e.attributes.length;i++)
        		{
        			if(e.attributes[i].nodeName==this.options.sectionName)
        			{
        				return e.attributes[i].nodeValue;
        			}
        		}
        		return false;
        	}
        	else
        	{
        		return false;
        	}
        };
		this.tpl_default=function()
		{
			return this.options.points.get_by_key(0,true);
		};
        this.tplSetDropables=function()
        {
        	this.options.drop['groups'] = new this.parent.module.drop().make();
            var obj = this.options.points;
            for (var i in obj)
            {
                if(obj.propertyIsEnumerable(i))
                {
					var dom = obj[i];
					//dom.style.border="1px solid transparent";
                    this.options.drop['groups'].register({
                        element:dom,
                        value:i,
                        events:{
                            over:function(dom)
                            {
								dom.style.border="1px dashed red";
                            }.extend(this,dom),
                            out:function(dom)
                            {
								dom.style.border="1px solid "+this.options.style.section;
                            }.extend(this,dom)
                        }});
                        //alert(dom.group);
					this.menu.group(dom);
                }
            }
            this.options.drop['groups'].setArrayPositions(true);
        };
        this.isset_tagName=function(n)
        {
        	return (this.xmlform.xml.getElementsByTagName(n).length>0)?true:false;
        	//return "Element_"+(new Date().getTime());
        };
        this.unique_name=function()
        {
        	return "Element_"+(new Date().getTime());
        };
		this.menu={
			/**
			*	Menu para Grupos (points)
			*/
			group:function(dom)
			{
				var menu = new this.parent.module.app.menuRight();
		        menu.make({
		        	target:dom,
		            width:150,
		            theme:'light',
		            //menu:this.group.elements.concat(
		            menu:[
		   		        {text:'Add element',launch:function(evt,g){
		   		        	//alert(evt+":"+g)
		   		        	this.options.panel.add=new this.parent.module.panel();
		   		        	this.options.panel.add.options={
		   		        		title:"Add element",
		   		        		size:{w:400,h:350},
		   		        		position:{center:true},
		   		        		statusBarButtons:[
		   		        			{value:'Create'},
		   		        			{value:'Cancel'}
		   		        		],
		   		        		fx:{modal:true}
		   		        	};
		   		        	this.options.panel.add.make();
		   		        	var a = {
		   		        		textAlign:'right',
		   		        		font:'normal 8pt Tahoma,sans-serif'
		   		        	};
		   		        	var b = {
		   		        		textAlign:'left',
		   		        		font:'normal 8pt Tahoma,sans-serif'
		   		        	};
		   		        	this.options.panel.add.addContent(
									new DOM('table',{align:'center',cellPadding:2},{width:'100%',margin:0}).append(
										new DOM('tbody').append(
											new DOM('tr').append(
												new DOM('td',{},{width:'30%'}).append(
													new DOM('div',{innerHTML:'Type:'},a)
												),
												new DOM('td',{},{width:'70%'}).append(
													new DOM('div',{},b).append(
														this.tmp.t = this.dynaform_dom_types()
													)
												)
											),
											new DOM('tr').append(
												new DOM('td').append(
													new DOM('div',{innerHTML:'Name:'},a)
												),
												new DOM('td').append(
													new DOM('div',{},b).append(
														this.tmp.n = new input({label:this.unique_name()},{},{style:{width:'70%'}})
													)
												)
											),
											new DOM('tr').append(
												new DOM('td').append(
													new DOM('div',{innerHTML:'NodeValue:'},a)
												),
												new DOM('td').append(
													new DOM('div',{},b).append(
														this.tmp.v = new input({},{},{style:{width:'70%'}})
													)
												)
											),
											new DOM('tr').append(
												new DOM('td',{colSpan:2}).append(
													new DOM('div',{},a).append(
														new DOM('fieldset',{},{border:'1px solid #B3B3BF'}).append(
															new DOM('legend',{innerHTML:'Properties'}),
															this.tmp.p = new DOM('div').append(this.dynaform_dom_properties())
														)
													)
												)
											)
										)
									)
							);
							this.tmp.g = window.event?evt:g;
							this.options.panel.add.elements.statusBarButtons[0].onmouseup=function()
							{
								if(this.tmp.n.value.trim()=='' || !this.tmp.n.value.isAlphaUS() || this.isset_tagName(this.tmp.n.value))
								{
									this.tmp.n.failed();
									return false;
								}
								else
								{
									this.tmp.n.passed();
									var a = {};
									for(var i=0;i<this.tmp.pr.length;i++)
									{
										a[this.tmp.pr[i].name]=this.tmp.pr[i].value;
									}
									//alert(this.tmp.p.length);
									this.add_element(this.tmp.n.value.trim(),true,{group:this.tmp.g,type:this.tmp.t.value}.concat(a));
									this.options.panel.add.remove();
								}
								//alert(this.tmp.t.value+":"+this.tmp.n.value+":"+this.tmp.v.value)
							}.extend(this);
							this.options.panel.add.elements.statusBarButtons[1].onmouseup=this.options.panel.add.remove;

							/*g = window.event?evt:g;
							this.add_element(this.unique_name(),true,{group:g,type:"other",ufo:7676});*/



						}.extend(this,this.isPoint(dom))},
						{text:'New section',launch:this.add_section.args(dom)},
		   		        {separator:true},
		   		        {text:'Delete element',launch:function(){}}
		            ]
		        });
				this.options.observers['menu'].register(menu.remove,menu);
			},
			/**
			*	Menu Principal
			*/
			principal:function(dom)
			{

			}
		}.expand(this);
		this.group={
			elements:[
				{text:'New Element',launch:function(){
					//console.log(this)
				}.extend(this)},
			]
		};
		this.parse_elements=function()
		{
			//alert(this.xmlform.db.length)
			for(var i=0;i<this.xmlform.db.length;i++)
			{
				this.add_element(this.get_xml_parsed_from_uid(i));
				//this.debug.log(e);
			}
		};
		this.get_xml_parsed_from_uid=function(db_uid)
		{
			var e = {
				type:'other',
				group:this.tpl_default()
			}.concat(this.xmlform.tag_attributes_to_object(this.xmlform.db[db_uid]));
			//console.log(e);
			return e;
		};
		this.add_section=function(e,d)
		{
			//d.setStyle({border:"1px solid "+this.options.style.section});
			this.tmp.sh = this.add_section_shadow(d);
			this.tmp.pn = new this.parent.module.panel();
			this.tmp.pn.options={
				title:"New section",
				size:{
					w:200,
					h:250
				},
				control:{close:false},
				statusBarButtons:[
		   			{value:'Create'},
		   		    {value:'Cancel'}
		   		],
				position:{center:true},
				fx:{/*modal:true*/}
			};
			this.tmp.pn.make();
			var a = {
		   		textAlign:'right',
		   		font:'normal 8pt Tahoma,sans-serif'
			};
		   	var b = {
		   		textAlign:'left',
		   		font:'normal 8pt Tahoma,sans-serif'
		   	};
			this.tmp.pn.addContent(
				new DOM('table',{align:'center',cellPadding:2},{width:'100%',margin:0}).append(
										new DOM('tbody').append(
											new DOM('tr').append(
												new DOM('td',{},{width:'50%'}).append(
													new DOM('div',{innerHTML:'Columns:'},a)
												),
												new DOM('td',{},{width:'50%'}).append(
													new DOM('div',{},b).append(
														this.tmp.c = new DOM('select',{onchange:function(){
																	this.tmp.p.innerHTML='';
																	this.tmp.p.append(this.dynaform_dom_section_names());
																}.extend(this)}
															).append(
															new DOM('option',{value:1,text:1}),
															new DOM('option',{value:2,text:2}),
															new DOM('option',{value:3,text:3}),
															new DOM('option',{value:4,text:4})
														)
													)
												)
											),
											new DOM('tr').append(
												new DOM('td',{colSpan:2}).append(
													new DOM('div',{},a).append(
														new DOM('fieldset',{},{border:'1px solid #B3B3BF'}).append(
															new DOM('legend',{innerHTML:'Labels'}),
															this.tmp.p = new DOM('div').append(this.dynaform_dom_section_names())
														)
													)
												)
											)
										)
									)
			);
			this.tmp.pn.elements.statusBarButtons[1].onmouseup=function(){
				this.tmp.sh.remove();
				if(this.tmp.sh_root)
				{
					this.tmp.sh_root.setStyle({border:'0px solid red'});
					this.tmp.sh_root=false;
				}
				this.tmp.pn.remove();
			}.extend(this);
			this.tmp.pn.elements.statusBarButtons[0].onmouseup=function()
							{
								var s = [];
								var r = true;
								for(var i=0;i<this.tmp.pr.length;i++)
								{
									var v = this.tmp.pr[i].value;
									if(v.trim()=='' || !v.isAlphaUS() || this.options.points[v] || s.inArray(v))
									{
										this.tmp.pr[i].failed();
										r = false;
									}
									else
									{
										this.tmp.pr[i].passed();
										s.push(v);
									}
								}
								if(r)
								{
									var w = 100/s.length;
									var __t;
									this.tmp.sh.replace(
										new DOM('table',{},{width:"100%"}).append(
											new DOM('tbody').append(
												__t = new DOM('tr')
											)
										)
									);
									var e = [];
									for(var i=0;i<s.length;i++)
									{
										var s_;
										__t.append(
											new DOM('td',{},{width:(w+"%")}).append(
												s_ = new DOM('div',{section:s[i]},this.options.style.add_section)
											)
										)
										this.options.points[s[i]]=s_;
									}
									this.tplSetDropables();
									this.tmp.pn.remove();
									if(this.tmp.sh_root)
									{
										this.tmp.sh_root.setStyle({border:'0px solid red'});
										this.tmp.sh_root=false;
									}
								}
								/*this.tmp.n.passed();
									var a = {};
									for(var i=0;i<this.tmp.pr.length;i++)
									{
										a[this.tmp.pr[i].name]=this.tmp.pr[i].value;
									}
									//alert(this.tmp.p.length);
									this.add_element(this.tmp.n.value.trim(),true,{group:this.tmp.g,type:this.tmp.t.value}.concat(a));
									this.options.panel.add.remove();*/

								//alert(this.tmp.t.value+":"+this.tmp.n.value+":"+this.tmp.v.value)
							}.extend(this);
		};
		this.add_section_shadow=function(t)
		{
			var s;
			if(t.id=='root')
			{
				t.append(
					s = new DOM('div',{},this.options.style.new_section)
				);
			}
			else
			{
				this.tmp.sh_root =t.parentNode;
				this.tmp.sh_root.setStyle({border:'1px solid red'});
				t.parentNode.append(
					s = new DOM('div',{},this.options.style.new_section)
				);
			}
			return s;
		};
		this.add_element=function(e,ne,at,o)
		{
				if(ne===true)
				{
					this.xmlform.add(e,at,o || {});
					e = at.concat({nodeName:e});
				}
				e.group=(this.options.points.isset_key(e.group))?e.group:this.tpl_default();
//console.info(e)
				var d = this.dynaform_dom[((this.dynaform_dom.isset_key(e.type))?e.type:'other')](e);
				pd = d.dom;
				pd.setStyle({
					margin:3,
					border:'1px solid #EEE',
					font:'normal 8pt Tahoma,MiscFixed',
					padding:5
				});
				pd.onmousedown=function(evt,db_uid)
				{
					var event = window.event || evt;
					var t = this.xmlform.db[db_uid];
					var d = this.db[db_uid];
					this.down_time = new Date().getTime();
					if(this.inDragProcess===true)
					{
						return false;
					}
					if(this.phantom_static)
					{
						this.phantom_static.remove();
					}
					if(event.ctrlKey)
					{
						this.reorder_element(event,db_uid);
					}
					else if(event.shiftKey)
					{
						this.options.drop.groups.setArrayPositions();
						this.reorder_element_group(event,db_uid);
					}
					else
					{
					}
					d.setStyle({
						border:'1px solid orange'
					});
					try{
						if(this.xmlform.current_edit!==db_uid){
							this.db[this.xmlform.current_edit].setStyle({
								border:'1px solid #EEE'
							});
						}
					}
					catch(e){

					}
					this.xmlform.tag_edit(t,db_uid,this.sync_xml_node.args(db_uid));
					return false;
				}.extend(this,d.db_uid,e.group);
				pd.onmouseup=function(event,db_uid){
					return false;
				}.extend(this,d.db_uid);
				pd.onmouseover=function(event,db_uid){
					var d = this.db[db_uid];
					if(this.xmlform.current_edit!==db_uid)
					{
						d.setStyle({border:'1px solid orange'});
						var event = window.event || event;
						if(event.ctrlKey)
						{
							d.setStyle({cursor:'move'});
						}
					}
				}.extend(this,d.db_uid);
				pd.onmouseout=function(event,db_uid){
					var d = this.db[db_uid];
					if(this.xmlform.current_edit!==db_uid)
					{
						d.setStyle({border:'1px solid #EEE',cursor:'default'});
					}
				}.extend(this,d.db_uid);

		};
		this.remove_element=function(db_uid)
		{
			this.db[db_uid].remove();
		};
		this.reorder_element_group=function(event,db_uid)
		{
				this.inDragProcess=true;
//				alert([arguments[0],arguments[1],arguments[2]]);
				var t = this.xmlform.db[db_uid];
				var d = this.db[db_uid];
				//var j = this.parent.dom.position(d,false,'bcn');
				var st = this.options.target.scrollTop;
				var sl = this.options.target.scrollLeft;
				var j	= this.parent.dom.mouse(event);
				this.options.dom.body.append(
					this.phantom_static = new DOM('div',{},{
						position:"absolute",
						width	:d.clientWidth,
						height	:d.clientHeight,
						border	:"1px solid orange",
						backgroundColor	:"orange",
						top		:(j.y-35)+st,
						left	:(j.x-40)+sl
					}).opacity(40)
				);
//				this.setImageAddRow(this.key_in_group(db_uid));
				this.options.drag.phantom = new this.parent.module.drag({
					elements:this.phantom_static,
					limitbox:this.options.dom.body
				});
				this.options.drag.phantom.events={
					move	:this.options.drop.groups.captureFromArray.args(this.options.drag.phantom,false,true),
					finish  :this.drag_elements_group_onfinish.args(db_uid)
				};
				this.options.drag.phantom.make();
				this.options.drag.phantom.onInit(event,0);
					//return false;
			return false;
		};
		this.reorder_element=function(event,db_uid)
		{
				var ctime = new Date().getTime();
				this.inDragProcess=true;
//				alert([arguments[0],arguments[1],arguments[2]]);
				var t = this.xmlform.db[db_uid];
				var d = this.db[db_uid];
				var j = this.parent.dom.position(d,false,true);
				this.options.points[d.group].append(
					this.phantom_static = new DOM('div',{},{
						position:"absolute",
						width	:d.clientWidth,
						height	:d.clientHeight,
						border	:"1px solid orange",
						backgroundColor	:"orange",
						top		:j.y,
						left	:j.x
					}).opacity(40),
					this.imageAddRow = new DOM('img',{
						src:this.parent.info.images+"nr.gif"
					})
				);
				this.options.drop.elements = new this.parent.module.drop();
				this.options.drop.elements.make();
				this.register_elements_drop(d.group);
				this.setImageAddRow(this.key_in_group(db_uid));
					this.options.drag.phantom = new this.parent.module.drag({
					elements:this.phantom_static,
					limit:"x",
					limitbox:this.options.points[d.group]
				});
				this.options.drag.phantom.events={
					move	:this.options.drop.elements.captureFromArray.args(this.options.drag.phantom,false,true),
					finish  :this.drag_elements_onfinish.args(db_uid)
				};
				this.options.drag.phantom.make();
				this.options.drag.phantom.onInit(event,0);
					//return false;

			return false;
		};
		this.sync_xml_node=function(data,db_uid)
		{
			var cd = this.xmlform.current_xml_edit.save('object');
			this.xmlform.sync_node(db_uid,cd);
			this.sync_dom(db_uid,cd);
		};
		this.sync_dom=function(db_uid,obj)
		{

		};
		this.register_groups_drop=function(group)
		{

		};
		this.register_elements_drop=function(group)
		{
			for(var i=0;i<this.db.length;i++)
			{
				if(this.db[i].group==group)
				{
					var c = this.db[i].db_uid;
					this.options.drop.elements.register({
						element	: this.db[i],
						value	: i,
						events	: {
							over:function(i)
							{
								//this.setImageAddRow(this.drop.lastSelected);
								if(this.options.drop.elements.selected!==false)
								{
									this.setImageAddRow(this.options.drop.elements.selected);
								}
							}.extend(this,c),
							out		:function(i)
							{
								/*if(this.drop.selected===false)
								{
									var uid=this.drop.arrayPositions.length-1;
									if(this.drop.position.y > this.drop.arrayPositions[uid].y2)
									{
										this.setImageAddRow(uid,true);
									}
								}*/
							}.extend(this,c)
						}
					});
				}
			}
			this.options.drop.elements.setArrayPositions(true);
		};
		this.setImageAddRow=function(drop_uid,last)
		{
			this.imageAddRow.setStyle({
				position:"absolute",
				zIndex	:100,
				top:((last)?this.options.drop.elements.arrayPositions[drop_uid].y2:this.options.drop.elements.arrayPositions[drop_uid].y1)-7,
				left:this.options.drop.elements.arrayPositions[drop_uid].x1-3
			});
		};
		this.drag_elements_group_onfinish=function(db_uid)
		{
			var drag = this.options.drag.phantom;
			var drop = this.options.drop.groups;
			if(drag.moved)
			{
				this.inDragProcess=false;
				this.phantom_static.remove();
				delete this.phantom_static;
				if(drop.selected!==false)
				{
					var t = drop.elements[drop.selected].value;
					//console.log(this.xmlform.db[db_uid])
					var c = this.xmlform.tag_attributes_to_object(this.xmlform.db[db_uid]).concat({
						group:t
					});
					var m = c['nodeName'];
					//console.log(c.group)
					////this.add_element(m,true,{group:c.group,type:"other"});
					//this.add_element(m,true,{group:t,type:"other"});
					this.add_element(m,true,c);
					this.remove_element(db_uid);
				}
				else
				{

				}

			}
			else
			{
				this.inDragProcess=false;
				this.phantom_static.remove();
				delete this.phantom_static;

			}
		};
		this.drag_elements_onfinish=function(db_uid)
		{
			this.imageAddRow.remove();
			delete this.imageAddRow;

			var insertRowIn,begin;

			var drag = this.options.drag.phantom;
			var drop = this.options.drop.elements;

			if(drag.moved)
			{
				if(drop.selected===false)
				{
					var uid=drop.arrayPositions.length-1;
					if((drop.position.y > drop.arrayPositions[uid].y2)/* && this.lastSelected===uid*/)
					{
						insertRowIn	= uid;
						begin		= false;
					}
					else
					{
						insertRowIn	= 0;
						begin		= true;
					}
				}
				else
				{
					insertRowIn	= drop.selected;
					begin		= true;
				}
				var t = this.db[drop.elements[insertRowIn].value];
//				t.parentNode.insertBefore(new DOM('input').replace(this.db[db_uid]));
				var n = t.before(new DOM('div')).replace(this.db[db_uid]);
				drag.flush();
				new this.parent.module.fx.move().make({
					duration:((drag.moved)?500:0),
					//end		:{x:this.drop.arrayPositions[insertRowIn].x1,y:this.drop.arrayPositions[insertRowIn].y1},
					end		:this.parent.dom.position(n,false,true),
					dom		:this.phantom_static,
					onFinish	:function()
					{
						new this.parent.module.fx.fade().make({
							duration:500,
							end		:0,
							dom		:this.phantom_static,
							onFinish	:function(){
								this.inDragProcess=false;
								this.phantom_static.remove();
								delete this.phantom_static;
							}.extend(this)
						});
					}.extend(this)
				});


				//var newRow=this.db[insertRowIn];
				//newRow.parentNode.replaceChild(domRow,newRow);
				//alert(movedUID+":"+this.options.data.rows[movedUID].info.rowIndex+":"+domRow.rowIndex)
				//alert(domRow.rowIndex)
			}
			else
			{
				/*insertRowIn	= drag.currentElementDrag.db_uid-1;
				begin		= true;*/
				this.inDragProcess=false;
				this.phantom_static.remove();
				delete this.phantom_static;
			}

		};
		/* Contar elementos en grupo */

		/*	Devuelve el Key actual de un objeto dentro del grupo	*/
		this.key_in_group=function(db_uid)
		{
			var a = this.db[db_uid];
			var j=0;
			for(var i=0;i<db_uid;i++)
			{
				if(this.db[i].group==a.group)
				{
					j+=1;
				}
			}
			return j;
		};
		this.dynaform_dom_section_names=function()
		{
			var v = this.tmp.c.options[this.tmp.c.options.selectedIndex].value;
			var a = {
		   		textAlign:'right',
		   		font:'normal 8pt Tahoma,sans-serif'
		   	};
		   	var b = {
		   		textAlign:'left',
		   		font:'normal 8pt Tahoma,sans-serif'
		   	};
			var at;
			var tb = new DOM('ol',{type:1});
			this.tmp.pr=[];
			for(var i=0;i<v;i++)
			{
				tb.append(
					new DOM('li').append(
						this.tmp.pr[i]=new input({style:{width:"100%",marginTop:2}})
					)
				);
			}
			return tb;
		};
		this.dynaform_dom_properties=function()
		{
			var v = this.tmp.t.options[this.tmp.t.options.selectedIndex].value;
			var a = {
		   		textAlign:'right',
		   		font:'normal 8pt Tahoma,sans-serif'
		   	};
		   	var b = {
		   		textAlign:'left',
		   		font:'normal 8pt Tahoma,sans-serif'
		   	};
			var at;
			var tb = new DOM('table',{cellPadding:2},{width:'100%'}).append(
				at = new DOM('tbody')
			);
			var as = this.dynaform_dom[v]({},true);
			this.tmp.pr=[];
			for(var i=0;i<as.length;i++)
			{
				at.append(
					new DOM('tr').append(
						new DOM('td',{},{width:'30%'}).append(
							new DOM('div',{innerHTML:as[i]+":"},a)
						),
						new DOM('td',{},{width:'70%'}).append(
							new DOM('div',{},b).append(
								this.tmp.pr[i]=new input({label:'',properties:{name:as[i]},style:{width:'100%'}})
							)
						)
					)
				);
			}
			return tb;
		};
		this.dynaform_dom_types=function()
		{
			var a = new DOM('select',{onchange:function(){
				this.tmp.p.innerHTML='';
				this.tmp.p.append(this.dynaform_dom_properties());
			}.extend(this)},{font:'normal 8pt Tahoma,sans-serif'});
			//alert(this.dynaform_dom)
			for (var i in this.dynaform_dom)
            {
                if(this.dynaform_dom.propertyIsEnumerable(i))
                {
					a.append(new DOM('option',{text:i,value:i}));
				}
            }
			return a;
		};
		this.dynaform_dom={
			text:function(options,get)
			{
				if(get){return ['size','maxlength','defaultvalue','required','dependentfields','linkfield','other_attribute'];}
				options={

				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:options.nodeName,db_uid:this.db.length,group:options.group})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			},
			other:function(options,get)
			{
				if(get){return ['other1','other2','other_attribute'];}
				options={

				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:'Other',group:options.group,db_uid:this.db.length})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			},
			title:function(options,get)
			{
				if(get){return ['other_attribute'];}
				options={

				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:options.nodeName,db_uid:this.db.length,group:options.group})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			}
		}.expand(this)
        this.expand(this);
        return this;
    }
});
