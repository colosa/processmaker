
var maborak=function(forceCssLoad){this.info={version:"0.3",name:"maborak",file:"maborak.js"},this.forceCssLoad=forceCssLoad?true:false;this.make=function(options)
{this.protoCore();this.module={debug:function(flag){this.flag=flag||false;this.log=function(v)
{if(typeof console!='undefined'&&this.flag===true)
{console.log(v||'');}};return this;}}.expand(this);this.options={thisIsNotPM:false}.concat(options||{});this.report=new this.bitacora();this.loadMethods([this.checkBrowser],this);this.event=this.factory(this.mantis,true);this.tools=this.factory(this.extended.tools,true);this.file=this.factory(this.fileCore,true);this.dom=this.factory(this.extended.D0M,true);this.iphone=this.factory(this.iphoneBrowser,true);this.cookie=this.factory(this.extended.cookie,true);this.Package=new this.PackageCore(this,this.file.db);this.report.add("Class loaded.");this.info.base=this.tools.baseJS(this.info.file);this.info.images=this.info.base+"images/";this.path_root=this.tools.path_root(this.info.base)+"/";if(this.options.modules){this.Package.Load(this.options.modules,{Instance:this,Type:"module"});}
if(this.options.files){this.Package.Load(this.options.files,{Type:"file"});}
this.exec(this.fix.memoryLeak);if(this.forceCssLoad===true){var st=$dce('link');st.rel='stylesheet';st.type='text/css';st.href=this.info.base+'stylesheet/default.css';this.dom.capture("tag.head 0").appendChild(st);}
this.expand(this);return this;};this.factory=function(Class,create)
{var cl=(typeof Class==="function")?Class:function(){};cl.prototype.parent=this;if(create===true)
{return new cl();}
else
{return cl;}},this.Class=function()
{var Vc=function(){};return new Vc();},this.pattern={observer:function(event)
{this.event=event;this.g="aaa";this.db=[];this.register=function(launch,Class)
{this.event=event;this.Class=Class;this.launch=launch;if(this.verify())
{return this.write();}
return true;};this.verify=function()
{return(typeof this.launch==="function")?true:false;};this.write=function()
{var cap={update:this.update,unregister:this.unregister.args(this.db.length)};this.db.push(this.launch);if(this.Class)
{this.Class.observer=cap;}
delete this.event;delete this.Class;delete this.launch;return this.db.length-1;};this.update=function()
{var ln=this.db.length;for(i=0;i<ln;i++)
{if(typeof this.db[i]=="function")
{this.db[i]();}}};this.unregister=function(uid)
{if(this.db[uid])
{this.db[uid]=null;}};this.expand(this);}};var argumentsToArray=function(a){var args=[];for(var i=0;i<a.length;i++){args.push(a[i]);};return args;};var tagScript='(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)';this.tmp={rpc:{}};this.charset="utf-8";this.protoCore=function()
{window['$']=function(el){return(typeof el=="string")?document.getElementById(el):el;};window['$dce']=function(el){return document.createElement(el);};window['$n']=function(el){return(typeof el=="string")?document.getElementsByName(el):el;};Array.prototype.isArray=true;Array.prototype.isObject=false;Array.prototype.onlyInt=function()
{var valid=[];for(var i=0;i<this.length;i++)
{if(!isNaN(this[i]))
{valid.push(parseInt(this[i],10));}}
return valid;};Array.prototype.inArray=function(search)
{var valid=[];for(var i=0;i<this.length;i++)
{if(this[i]===search)
{return true;}}
return false;};Array.prototype.fill=function(startIndex,cant,value)
{for(var i=0;i<cant;i++)
{this.splice(startIndex+i,0,value);}
return this;};Array.prototype.toStr=Object.prototype.toStr=function(strict)
{var val,output="";output+="{";for(var i in this){val=this[i];if((!strict&&this.propertyIsEnumerable(i))||strict===true)
{switch(typeof val){case("object"):if(typeof val.childNodes!="undefined")
{output+=i+":[DOM.Object],\n";}
else if(val.isArray||val.isObject){output+=i+":"+val.toStr(strict)+",\n";}else{output+=i+": Element||Event,\n\n";}
break;case("string"):output+=i+":'"+val+"',\n";break;case("function"):output+=i+":FUNCTION,\n";break;default:output+=i+":"+val+",\n";}}}
output=output.substring(0,output.length-1)+"}";return output;};Array.prototype.indexOf=function(val)
{for(var i=0;i<this.length;i++)
{if(this[i]==val){return i;}}
return-1;};Array.prototype.unique=function()
{if(this.length<2){return this;}
var a=[],i,l=this.length;for(i=0;i<l;i++){if(a.indexOf(this[i])<0)
{a.push(this[i]);}}
return a;};Array.prototype.key=function(value)
{for(var i=0;i<this.length;i++){if(this[i]===value){return i;}}
return false;};Array.prototype.random=function(range)
{var i=0,l=this.length;if(!range){range=this.length;}
else if(range>0){range=range%l;}
else{i=range;range=l+range%l;}
return this[Math.floor(range*Math.random()-i)];};Array.prototype.map=function(fun)
{if(typeof fun!=="function"){return false;}
var i=0,l=this.length;for(i=0;i<l;i++)
{fun(this[i]);}
return true;};Array.prototype.shuffle=function(recursive)
{var i=this.length,j,t;while(i){j=Math.floor((i--)*Math.random());t=recursive&&typeof this[i].shuffle!=='undefined'?this[i].shuffle():this[i];this[i]=this[j];this[j]=t;}
return this;};Array.prototype.evalScript=function(extracted)
{var s=this.map(function(sr){var sc=(sr.match(new RegExp(tagScript,'im'))||['',''])[1];if(window.execScript){window.execScript(sc||" ");}
else
{window.setTimeout(sc,0);}});return true;};Array.prototype.clear=function()
{return this.splice(0,this.length);};Array.prototype.copy=function()
{return this.slice(0,this.length);};Array.prototype.insert=function(key,value)
{var na=this.copy();value=(!value||value.isArray===false)?[value]:value;this.clear();for(var i=0;i<na.length;i++)
{if(i===key)
{for(var j=0;j<value.length;j++)
{this.push(value[j]);}}
this.push(na[i]);}
return this;};Array.prototype.toSelect=function()
{var s=[];for(var i=0;i<this.length;i++)
{s.push({value:i,text:this[i]});}
return s;};Object.prototype.isObject=true;Object.prototype.isArray=false;Object.prototype.propertyIsEnumerable=function(i)
{return(typeof Object.prototype[i]==="undefined")?true:false;};Object.prototype.concat=function(obj)
{for(var i in obj)
{if(obj.propertyIsEnumerable(i))
{this[i]=obj[i];}}
return this;};Object.prototype.get_by_key=function(id,key)
{var j=0;for(var i in this){if(this.propertyIsEnumerable(i))
{if(id===j){return(key)?i:this[i];}
j+=1;}}
return false;};Object.prototype.isset_key=function(key)
{for(var i in this){if(this.propertyIsEnumerable(i))
{if(key===i){return true;}}}
return false;};Object.prototype.setParent=function(obj)
{for(var i in this){if(this.propertyIsEnumerable(i)&&typeof this[i]==="function")
{this[i].prototype.parent=obj||false;}}
return this;};Object.prototype.isObjectStrict=function()
{return(this.appendChild)?false:true;};Object.prototype.expand=function(Class,recursive)
{Class=Class||this;for(var i in this)
{if(this.propertyIsEnumerable(i)&&(typeof this[i]==="function"||(recursive===true&&typeof this[i]==="object"&&this[i].isObjectStrict())))
{try{if(typeof this[i]==="function")
{this[i]=this[i].extend(Class);}
else
{this[i]=this[i].expand(Class,recursive);}}
catch(e){this[i]=this[i];}}
else
{}}
return this;};Function.prototype.isObject=false;Function.prototype.isArray=false;Function.prototype.extend=function(Class)
{try{var oThis=this;var args=argumentsToArray(arguments);args.splice(0,1);return function()
{return oThis.apply(Class,argumentsToArray(arguments).concat(args));};}
catch(e){return this;}};Function.prototype.args=function()
{var oThis=this;var args=argumentsToArray(arguments);return function()
{return oThis.apply(oThis,argumentsToArray(arguments).concat(args));};};String.prototype.isAlphaUS=function()
{var a=this.split("");var b="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_".split("");for(var i=0;i<a.length;i++)
{if(!b.inArray(a[i])){return false;}}
return true;};String.prototype.isString=true;String.prototype.trim=function(){return(this.replace(new RegExp("^([\\s]+)|([\\s]+)$","gm"),""));};String.prototype.leftTrim=function(){return(this.replace(new RegExp("^[\\s]+","gm"),""));};String.prototype.rightTrim=function(){return(this.replace(new RegExp("[\\s]+$","gm"),""));};String.prototype.stripTags=function()
{return this.replace(/<\/?[^>]+>/gi,'');};String.prototype.escapeHTML=function()
{var div=$dce('div');var text=document.createTextNode(this);div.appendChild(text);return div.innerHTML;};String.prototype.unescapeHTML=function()
{var div=$dce('div');div.innerHTML=this.trim();return div.childNodes[0]?div.childNodes[0].nodeValue:'';};String.prototype.sReplace=function(search,replace)
{search=search||"";replace=replace||"";var re=new RegExp(search,"g");return this.replace(re,replace);};String.prototype.camelize=function()
{var oStringList=this.split("-");if(oStringList.length==1){return oStringList[0];}
var camelizedString=this.indexOf("-")===0?oStringList[0].charAt(0).toUpperCase()+oStringList[0].substring(1):oStringList[0];for(var i=1,len=oStringList.length;i<len;i++)
{var s=oStringList[i];camelizedString+=s.charAt(0).toUpperCase()+s.substring(1);}
return camelizedString;};String.prototype.toArray=function()
{return this.split("");};String.prototype.extractScript=function()
{var matchAll=new RegExp(tagScript,'img');return(this.match(matchAll)||[]);};String.prototype.evalScript=function()
{return(this.match(new RegExp(tagScript,'img'))||[]).evalScript();};String.prototype.stripScript=function()
{return this.replace(new RegExp(tagScript,'img'),'');};if((typeof XMLSerializer)==='undefined')
{window.XMLSerializer=function(){this.toString=function()
{return"[object XMLSerializer]";};this.serializeToString=function(xml){return xml.xml||xml.outerHTML||"Error XMLSerializer";};};}};this.loadMethods=function(methods,instance)
{var _return_=[];var tmp;for(var i=0;i<methods.length;i++)
{if(methods[i])
{if(methods[i].isArray)
{if(typeof methods[i][0]=="function")
{var method=(methods[i][1])?((methods[i][1].isArray)?methods[i][1]:[methods[i][1]]):false;if(method===false)
{tmp=methods[i][0].apply(instance);}
else
{tmp=methods[i][0].apply(instance,method);}
if(methods[i][2]===true){_return_.push(tmp);}}}
else if(typeof methods[i]=="function")
{methods[i].apply(instance);}}}
return(_return_.length==1)?_return_[0]:_return_;};this.checkBrowser=function()
{var userAgent=navigator.userAgent;var u;this.browser={isIE:((userAgent.indexOf('MSIE')>=0)?true:false),isNS:((userAgent.indexOf('Netscape6/')>=0)?true:false),isFF:((userAgent.indexOf('Firefox')>=0)?true:false),isSF:((userAgent.indexOf('Safari')>=0)?true:false),isGK:((userAgent.indexOf('Gecko')>=0)?true:false),isIphone:((userAgent.indexOf('iPhone')>=0)?true:false),isOP:((userAgent.indexOf('Opera')>=0)?true:false)};this.browser.isIE=(this.browser.isOP)?false:this.browser.isIE;var checkFor=["MSIE","Netscape/6","Firefox","Safari","Gecko","Opera","iPhone"];for(var i=0;i<checkFor.length;i++)
{var j=userAgent.indexOf(checkFor[i]);this.browser.version=userAgent+"::::"+userAgent.substr(j+checkFor[i].length);}};this.mantis=function()
{this.db=[];this.flush=function()
{var i=0;while(this.db.length>0)
{if(this.db[0]&&this.db[0].isObject===true)
{this.remove(this.db[0]._object_,this.db[0]._event_,this.db[0]._function_,this.db[0]._bumble_);}
this.db.splice(0,1);}};this.add=function(_object_,_event_,_function_,_bumble_)
{_function_=(_function_.isObject)?this.parent.closure(_function_):_function_;_object_=this.parent.dom.element(_object_);if(_object_.addEventListener)
{_object_.addEventListener(_event_,_function_,((_bumble_===true)?true:false));}
else if(_object_.attachEvent)
{_object_.attachEvent("on"+_event_,_function_);}
else
{this.report("Event registration not supported");}
var event={_object_:_object_,_event_:_event_,_function_:_function_,_bumble_:((_bumble_===true)?true:false)};this.db.push(event);return(this.db.length-1);};this.remove=function(_object_,_event_,_function_,_bumble_,uidInDB)
{_function_=(_function_.isObject)?this.parent.closure(_function_):_function_;_object_=this.parent.dom.element(_object_);if(_object_.removeEventListener)
{_object_.removeEventListener(_event_,_function_,((_bumble_===true)?true:false));}
else if(_object_.detachEvent)
{_object_.detachEvent("on"+_event_,_function_);}
if(uidInDB)
{if(uidInDB==(this.db.length-1))
{this.db.pop();}
else
{this.db[uidInDB]=null;}}};this.removeFromUid=function(uid)
{if(this.db[uid])
{var e=this.db[uid];this.remove(e._object_,e._event_,e._function_,e._bumble_,uid);}};this.flushCollection=function(arrayEventsInDB)
{var l=arrayEventsInDB.length;for(i=0;i<l;i++)
{this.remove(this.db[arrayEventsInDB[i]]._object_,this.db[arrayEventsInDB[i]]._event_,this.db[arrayEventsInDB[i]]._function_,this.db[arrayEventsInDB[i]]._bumble_,arrayEventsInDB[i]);}};this.report=function(text)
{if(this.parent&&this.parent.report)
{this.parent.report.add(text);}};this.dom=function(event)
{return event.target||window.event.srcElement;};this.Null=function(event)
{if(event.preventDefault)
{event.preventDefault();}
event.returnValue=false;};this.expand(this);};this.bitacora=function()
{this.db=[];this.add=function(text)
{this.db.push(text);};};this.fix={memoryLeak:function()
{this.event.add(window,"unload",this.event.flush);}};this.exec=function(_function_,_arguments_,_return_,_instance_)
{return this.loadMethods([[_function_,((_arguments_)?_arguments_:null),_return_||false]],((_instance_)?_instance_:this));};this.closure=function(options)
{var method=options.method;var instance=options.instance;var args=(options.args||(typeof options.args=="number"&&options.args===0))?options.args:false;var _function=options.Function||false;var isArr=options.args_is_array||false;var _event=options.event||false;var rf=options.Return||false;return function(hEvent)
{var argss=(args===false)?false:((args.isArray&&isArr===false)?args:[args]);var param=(_event)?[(hEvent||window.event)].concat(argss):argss;if(_function===false)
{method.apply(instance,param||[null]);}
else
{_function.apply(_function,param||[null]);}
return rf;};};this.PackageCore=function(parent,db)
{this.parent=parent||false;this.db=db||false;this.Load=function(file,options)
{this.options={zip:false}.concat(options||{});if(arguments.length<2||!this.check()){return false;}
this.toLoad=((this.options.Absolute===true)?this.options.Path:file).split(",");if(this.type==='module'&&(this.options.zip===true||this.parent.options.zip===true))
{var tl=[];for(var i=this.toLoad.length;i>0;i--)
{this.name=this.toLoad[this.toLoad.length-i];if(!this.isset()){tl.push(this.name);this.write(false);}}
if(tl.length>0){var script=$dce("script");this.parent.dom.capture("tag.head 0").appendChild(script);script.src=(this.parent.options.inGulliver===true)?this.path+'maborak.loader.js':this.path+'server/maborak.loader.php?load='+tl.join(',');script.type="text/javascript";script.charset=this.parent.charset;if(this.type=="module"){this.write(script);}}}
else
{for(var i=this.toLoad.length;i>0;i--)
{this.name=this.toLoad[this.toLoad.length-i];if(!this.isset()){this.src=this.source();var script=$dce("script");this.parent.dom.capture("tag.head 0").appendChild(script);script.src=this.src;script.type="text/javascript";script.charset=this.parent.charset;if(this.type=="module"){this.write(script);}}}}
delete this.Class;delete this.file;delete this.info;delete this.path;delete this.toLoad;delete this.type;delete this.src;return true;};this.source=function()
{if(this.type=="module")
{return this.path+"module."+this.name+".js";}
else if(this.type=="file")
{var nroute=(this.options.Absolute===true)?this.path:this.path+this.name+"/core/"+this.name+".js";return nroute;}
return false;};this.check=function()
{if(!this.db||!this.options.Type){return false;}
this.type=this.options.Type.toLowerCase();if(this.type=="file")
{this.path=this.options.Path||this.parent.path_root;return true;}
else if(this.type=="module")
{this.Class=(this.options.Instance)?this.options.Instance:((this.options.Class)?this.options.Class.prototype:false);if(this.Class===false||!this.Class.info){return false;}
if(!this.Class.module)
{this.Class.module={};}
this.path=this.options.Path||this.Class.info.base||false;return(this.path===false)?false:true;}
else
{return false;}};this.isset=function()
{if(this.type=="module")
{for(var i=this.db.length;i>0;i--)
{if(this.db[this.db.length-i].name==this.Class.info.name)
{this.file=this.db[this.db.length-i];break;}}
if(!this.file)
{this.db.push({name:this.Class.info.name,Class:this.Class,_Package_:[]});this.file=this.db[this.db.length-1];}
for(i=this.file._Package_.length;i>0;i--)
{var nm=this.file._Package_[this.file._Package_.length-i];if(nm.name==this.name&&nm.type==this.type)
{return true;}}
this.Class.module[this.name]=true;return false;}
else if(this.type=="file")
{return false;}
return false;};this.write=function(script,option)
{this.file._Package_.push({type:this.type,loaded:false,name:this.name,script:script,onLoad:this.options.onLoad||false});};this.Public=function(Package)
{if(!Package||!Package.info||!Package.info.Class||!Package.info.Name||!Package.info.Type||!Package.content){return false;}
for(var i=this.db.length;i>0;i--)
{if(this.db[this.db.length-i].name==Package.info.Class)
{this._file_=this.db[this.db.length-i];break;}}
if(!this._file_)
{return false;}
else
{this.tmpPgk=this._file_.Class.module[Package.info.Name];if(this.tmpPgk===true)
{if(typeof Package.content=="function")
{Package.content.prototype.parent=this._file_.Class;}
else if(typeof Package.content=="object")
{Package.content.setParent(this._file_.Class);}
this._file_.Class.module[Package.info.Name]=Package.content;for(i=this._file_._Package_.length;i>0;i--)
{var nm=this._file_._Package_[this._file_._Package_.length-i];if(nm.name==Package.info.Name&&nm.type==Package.info.Type)
{nm.loaded=true;if(!this.parent.browser.isIE)
{this.parent.dom.remove(nm.script);}
delete nm.script;if(nm.onLoad)
{nm.onLoad();}
break;}}
delete this._file_;}}
return true;};};this.fileCore=function()
{this.db=[];};this.extended={cookie:function()
{this.set=function(name,value,days,path,domain,secure)
{var expires=-1;if(typeof days=="number"&&days>=0){var d=new Date();d.setTime(d.getTime()+(days*24*60*60*1000));expires=d.toGMTString();}
value=escape(value);document.cookie=name+"="+value+";"
+(expires!=-1?" expires="+expires+";":"")
+(path?"path="+path:"")
+(domain?"; domain="+domain:"")
+(secure?"; secure":"");};this.get=function(name)
{var idx=document.cookie.lastIndexOf(name+'=');if(idx==-1){return null;}
var value=document.cookie.substring(idx+name.length+1);var end=value.indexOf(';');if(end==-1){end=value.length;}
value=value.substring(0,end);value=unescape(value);return value;};this.del=function(name)
{this.set(name,"-",0);};},tools:function()
{this.baseURL=function()
{return window.location;};this.path_root=function(jsPath)
{if(this.parent.browser.isIE)
{return jsPath+"../..";}
else
{var a=jsPath.split("/");a.pop();a.pop();a.pop();return a.join("/");}};this.baseJS=function(js)
{var Isrc="",script=document.getElementsByTagName('script');for(var i=script.length-1;i>=0;i--){if(script[i].src&&(script[i].src.indexOf(js)!=-1))
{Isrc=script[i].src;Isrc=Isrc.substring(0,Isrc.lastIndexOf('/'));this.parent.info.domBaseJS=script[i];break;}}
return Isrc+"/";};this.head=function()
{return document.getElementsByTagName("HTML")[0].getElementsByTagName("HEAD")[0];};this.createUID=function()
{return Math.random();};this.expand(this);},D0M:function()
{this.get_html=function()
{return document.getElementsByTagName('html')[0];};this.get_doc=function(){var doc=window.document;return(!doc.compatMode||doc.compatMode=='CSS1Compat')?this.get_html():doc.body;};this.element=function(element)
{return(!element)?false:((typeof element=="object")?element:((document.getElementById(element))?document.getElementById(element):false));};this.remove=function(DOM){DOM=(DOM.isArray||(DOM.isObject&&!DOM.appendChild))?DOM:[DOM];for(var i in DOM)
{if(DOM.propertyIsEnumerable(i))
{if(DOM[i].isObject&&!DOM[i].appendChild)
{this.remove(DOM[i]);}
else
{var element=this.element(DOM[i]);if(element&&element.parentNode)
{element.parentNode.removeChild(element);}}}}
return true;};this.automateDOMToCollection=function(DOM)
{return((!DOM.isArray&&(DOM.isObject||(this.parent.browser.isIE&&!DOM.isObject)))||DOM.isArray)?DOM:[DOM];};this.setStyle=function(DOM,styles)
{DOM=(DOM.isArray)?DOM:[DOM];var sizeInPixel=["width","height","left","top","right","bottom","margin","marginLeft","marginRight","marginTop","marginBottom","marginLeftWidth","marginRightWidth","marginTopWidth","marginBottomWidth","padding","paddingLeft","paddingRight","paddingTop","paddingBottom","paddingLeftWidth","paddingRightWidth","paddingTopWidth","paddingBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth","borderBopttomWidth"];for(var j=0;j<DOM.length;j++)
{var d0m=this.element(DOM[j]);if(d0m)
{for(var value in styles)
{if(styles.propertyIsEnumerable(value)){var val=(typeof styles[value]=="function")?styles[value]():styles[value];try{var valu=(typeof val!="undefined")?val:" ";var prop=value.camelize();valu=(sizeInPixel.inArray(prop)&&typeof valu==="number")?valu+"px":valu;d0m.style[prop]=valu;}
catch(e){}}}}}};this.setProperties=function(DOM,properties)
{DOM=(DOM.isArray)?DOM:[DOM];for(var j=0;j<DOM.length;j++)
{var d0m=this.element(DOM[j]);if(d0m)
{for(var value in properties)
{if(properties.propertyIsEnumerable(value)){var val=properties[value];try{d0m[value]=(typeof val!="undefined")?val:" ";}
catch(e){}}}}}};this.getStyle=function(DOM,style)
{var d0m=this.element(DOM),rs;if(typeof style=="string")
{var st=style.split(",");rs=[];for(var i=0;i<st.length;i++)
{var stringStyle=st[i].camelize();var value=d0m.style[stringStyle];if(!value)
{if(document.defaultView&&document.defaultView.getComputedStyle)
{var css=document.defaultView.getComputedStyle(d0m,null);value=css?css.getPropertyValue(stringStyle):null;}
else if(d0m.currentStyle)
{value=d0m.currentStyle[stringStyle];}}
rs.push((value=='auto')?null:value);}
rs=(rs.length<2)?rs[0]:rs;}
else if(style.isObject)
{rs={};for(i in style)
{if(style.propertyIsEnumerable(i))
{rs[i]=this.getStyle(DOM,i);}}}
return rs;};this.position=function(DOM,Final,StopOnAbsolute)
{DOM=this.element(DOM);var position,initial=DOM;if(this.parent.dom.getStyle(DOM,"position")=="absolute")
{position={x:parseInt(this.parent.dom.getStyle(DOM,"left"),10),y:parseInt(this.parent.dom.getStyle(DOM,"top"),10)};}
else
{position={x:0,y:0};if(!DOM){return position;}
position.x=parseInt(DOM.offsetLeft,10);position.y=parseInt(DOM.offsetTop,10);while(DOM.offsetParent){DOM=DOM.offsetParent;var sta=(typeof StopOnAbsolute=="string")?(StopOnAbsolute==DOM.id):StopOnAbsolute;if(sta&&(this.parent.dom.getStyle(DOM,"position")=="absolute"||this.parent.dom.getStyle(DOM,"position")=="relative"))
{break;}
else
{var gt=this.position(DOM,false,StopOnAbsolute);position.x+=gt.x;position.y+=gt.y;}}}
return(Final===true)?{x:(position.x+parseInt(initial.offsetWidth,10)),y:(position.y+parseInt(initial.offsetHeight,10))}:position;};this.CollectionToArray=function(Collection)
{var r=[];for(var i=0;i<Collection.length;i++)
{r.push(Collection[i]);}
return r;};this.mouse=function(event)
{return{x:(this.parent.browser.isIE)?(window.event.clientX+document.documentElement.scrollLeft+document.body.scrollLeft):(event.clientX+(window.scrollX||document.body.scrollLeft||0)),y:(this.parent.browser.isIE)?(window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop):(event.clientY+(window.scrollY||document.body.scrollTop||0))};};this.opacity=function(DOM,opacity)
{DOM=(DOM.isArray)?DOM:[DOM];for(var j=0;j<DOM.length;j++)
{var d0m=this.element(DOM[j]);if(this.parent.browser.isIE)
{this.setStyle(d0m,{filter:"alpha(opacity="+opacity+")"});}
else
{this.setStyle(d0m,{opacity:opacity/100});}}
return true;};this.getOpacity=function(DOM)
{var opacity;var DOM=this.element(DOM);if(opacity=this.getStyle(DOM,'opacity'))
{return parseFloat(opacity);}
if(opacity=(this.getStyle(DOM,'filter')||'').match(/alpha\(opacity=(.*)\)/))
{if(opacity[1])
{return parseFloat(opacity[1])/100;}}
return 1.0;};this.nullContextMenu=function(DOM)
{DOM=(DOM.isArray)?DOM:[DOM];for(var i=0;i<DOM.length;i++)
{DOM[i].oncontextmenu=function(){return false;};}};this.positionRange=function(DOM,StopOnAbsolute)
{DOM=(DOM.isArray)?DOM:[DOM];var r={};for(var i=0;i<DOM.length;i++)
{var p1=this.position(DOM[i],false,StopOnAbsolute||false);r.x1=(!r.x1||(p1.x<r.x1))?p1.x:r.x1;r.y1=(!r.y1||(p1.y<r.y1))?p1.y:r.y1;var p2=this.position(DOM[i],true,StopOnAbsolute||false);r.x2=(!r.x2||(p2.x>r.x2))?p2.x:r.x2;r.y2=(!r.y2||(p2.y>r.y2))?p2.y:r.y2;}
return r;};this.positionRangeFix=function(DOM,range)
{DOM=(DOM.isArray)?DOM:[DOM];var r={};for(var i=0;i<DOM.length;i++)
{var sL=parseInt(this.parent.dom.getStyle(DOM[i],"left"),10);var sT=parseInt(this.parent.dom.getStyle(DOM[i],"top"),10);this.parent.dom.setStyle(DOM[i],{left:sL+1});}
return r;};this.capture=function(DOMstring)
{var str=DOMstring.trim();var index=str.split(" ");var iDom=index[0];iDom=iDom.split(".");if(iDom.length<2){return false;}
index=(index.length<2)?"0":index[index.length-1];var all=(index==="*")?true:false;var pindex=index.split(",").onlyInt();index=pindex.unique();var by=iDom[0];iDom.splice(0,1);var el=iDom.join(".");var oDom;switch(by)
{case"id":return $(el);case"name":oDom=document.getElementsByName(el);break;case"tag":oDom=document.getElementsByTagName(el);break;default:return false;}
if(all)
{return this.CollectionToArray(oDom);}
else
{if(index.length===0)
{return false;}
else if(index.length==1)
{return oDom[0];}
else
{var nDom=[].fill(0,index.length,false);for(var i=0;i<oDom.length;i++)
{if(index.inArray(i))
{nDom[index.key(i)]=oDom[i];}}
return nDom;}}};this.bubble=function(allow,evt)
{evt=evt||window.event||false;allow=(allow===true)?true:false;if(!evt){return false;}
if(this.parent.browser.isIE)
{evt.cancelBubble=!allow;}
else
{if(allow===false)
{evt.stopPropagation();}
else
{}}
return true;};this.loadJs=function(file)
{var jsS=document.getElementsByTagName("script");for(var i=0;i<jsS.length;i++)
{if(jsS[i].src.indexOf(file)>-1){return false;}}
var script=$dce("script");this.capture("tag.head 0").appendChild(script);script.src=file;script.type="text/javascript";script.charset=this.parent.charset;return true;};this.getPageScroll=function()
{return[window.pageXOffset||document.documentElement.scrollLeft||document.body.scrollLeft,window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop];};this.getPageSize=function()
{var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=window.innerWidth+window.scrollMaxX;yScroll=window.innerHeight+window.scrollMaxY;}else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight;}else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight;}
var windowWidth,windowHeight;if(self.innerHeight){if(document.documentElement.clientWidth){windowWidth=document.documentElement.clientWidth;}else{windowWidth=self.innerWidth;}
windowHeight=self.innerHeight;}else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight;}else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight;}
if(yScroll<windowHeight){pageHeight=windowHeight;}else{pageHeight=yScroll;}
if(xScroll<windowWidth){pageWidth=xScroll;}else{pageWidth=windowWidth;}
return[pageWidth,pageHeight];};this.serializer=this.parent.factory(function(DOM,obj)
{this.DOM=DOM;this.inObject=(obj===true)?true:false;this.serialized=(this.inObject)?{}:"";this.parse=function()
{};this.rake=function(val)
{if(!val){return val;}
if(typeof val==="object")
{this.serialized.concat(val);}
else
{this.serialized+=val;}
return true;};this.form=function()
{var form=this.DOM;var serializeds=[];serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("input"),this.inObject).input());serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("select"),this.inObject).select());serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("textarea"),this.inObject).textarea());for(var i=0;i<serializeds.length;i++)
{this.rake(serializeds[i]);}
return this.serialized;};this.input=function()
{for(var i=0;i<this.DOM.length;i++)
{var inp=this.DOM[i];if(inp.name)
{if(inp.type==="text")
{var cn=(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");this.rake(cn);}
else if(inp.type==="radio")
{var cn=(inp.checked===true)?(inp.name+"="+escape(inp.value)+"&"):"";this.rake(cn);}
else if(inp.type==="checkbox")
{var cn=(inp.checked===true)?inp.name+"="+escape(inp.value)+"&":"";this.rake(cn);}
else
{var cn=(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");this.rake(cn);}}}
return this.serialized;};this.select=function()
{for(var i=0;i<this.DOM.length;i++)
{var inp=this.DOM[i];if(inp.name)
{if(inp.multiple===true)
{for(var j=0;j<inp.options.length;j++)
{if(inp.options[j].selected)
{var cn=inp.name+"="+escape(inp.options[j].value)+"&";this.rake(cn);}}}
else
{try
{var cn=inp.name+"="+escape(inp.options[inp.options.selectedIndex].value)+"&";}
catch(e)
{var cn=inp.name+"=&";}
this.rake(cn);}}}
return this.serialized;};this.textarea=function()
{for(var i=0;i<this.DOM.length;i++)
{var inp=this.DOM[i];if(inp.name)
{var cn=(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");this.rake(cn);}}
return this.serialized;};this.expand(this);return this;});}};this.iphoneBrowser=function()
{this.make=function()
{this.parent.event.add(window,"load",function(){document.body.orient="landscape";window.scrollTo(0,1);});};};return this;};
function get_xmlhttp(){try{xmlhttp=new ActiveXObject("Msxml2.XMLHTTP1");}catch(e){try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}catch(E){xmlhttp=false;}}
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}
return xmlhttp;}
function ajax_function(ajax_server,funcion,parameters,method)
{var objetus;objetus=get_xmlhttp();var response;try
{if(parameters)parameters='&'+encodeURI(parameters);if(!method)method="POST";data="function="+funcion+parameters;questionMark=(ajax_server.split('?').length>1)?'&':'?';var callServer;callServer=new leimnud.module.rpc.xmlhttp({url:ajax_server,async:false,method:method,args:data});callServer.make();response=callServer.xmlhttp.responseText;var scs=callServer.xmlhttp.responseText.extractScript();scs.evalScript();delete callServer;}catch(ss){alert("Error: "+ss.message+var_dump(ss));}
return response;}
function ajax_message(ajax_server,funcion,parameters,method,callback)
{var objetus;objetus=get_xmlhttp();var response;try
{if(parameters)parameters='&'+encodeURI(parameters);if(!method)method="POST";data="function="+funcion+parameters;questionMark=(ajax_server.split('?').length>1)?'&':'?';objetus.open(method,ajax_server+((method==='GET')?questionMark+data:''),true);objetus.onreadystatechange=function(){if(objetus.readyState==4)
{if(objetus.status==200)
{if(callback)callback(objetus.responseText);}}}
if(method==='POST')objetus.setRequestHeader("Content-Type","application/x-www-form-urlencoded");objetus.send(((method==='GET')?null:data));}catch(ss)
{alert("error"+ss.message);}}
function ajax_post(ajax_server,parameters,method,callback,asynchronous)
{var objetus;objetus=get_xmlhttp();var response;try
{if(typeof(parameters)==='object')parameters=ajax_getForm(parameters);if(!method)method="POST";if(typeof(asynchronous)==='undefined')asynchronous=false;data=parameters;questionMark=(ajax_server.split('?').length>1)?'&':'?';if(method==='GET/POST'){objetus.open('POST',ajax_server+((data.length<1024)?(questionMark+data):''),asynchronous);}else{objetus.open(method,ajax_server+((method==='GET')?questionMark+data:''),asynchronous);}
objetus.onreadystatechange=function(){if(objetus.readyState==4)
{if(objetus.status==200)
{if(callback)callback(objetus.responseText);}}}
if((method==='POST')||(method==='GET/POST'))objetus.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");objetus.send(((method==='GET')?null:data));if(!asynchronous)
{if(callback)callback(objetus.responseText);return objetus.responseText;}}catch(ss)
{alert("Error: "+var_dump(ss));}}
function ajax_getForm(thisform){var formdata='';for(var i=0;i<thisform.length;i++){if(formdata!=='')formdata+='&';if(thisform.elements[i].type=="text"){formdata+=thisform.elements[i].name+"="+encodeURIComponent(thisform.elements[i].value);}else if(thisform.elements[i].type=="textarea"){formdata+=thisform.elements[i].name+"="+encodeURIComponent(thisform.elements[i].value);}else if(thisform.elements[i].type=="checkbox"){formdata+=thisform.elements[i].name+'='+((thisform.elements[i].checked)?'1':'0');}else if(thisform.elements[i].type=="radio"){if(thisform.elements[i].checked==true){formdata+=thisform.elements[i].name+"="+thisform.elements[i].value;}}else if(thisform.elements[i].type=="select-multiple"){for(var j=0;j<thisform.elements[i].options.length;j++){if(j!==0)formdata+='&';formdata+=((thisform.elements[i].options[j].selected)?thisform.elements[i].name.replace('[]','['+j+']')+"="+encodeURIComponent(thisform.elements[i].options[j].value):'');}}else{formdata+=thisform.elements[i].name+"="+encodeURIComponent(thisform.elements[i].value);}}
return formdata;}
function isNumber(sValue)
{var sValue=new String(sValue);var bDot=false;var i,sCharacter;if((sValue==null)||(sValue.length==0))
{if(isNumber.arguments.length==1)
{return false;}
else
{return(isNumber.arguments[1]==true);}}
for(i=0;i<sValue.length;i++)
{sCharacter=sValue.charAt(i);if(i!=0)
{if(sCharacter=='.')
{if(!bDot)
{bDot=true;}
else
{return false;}}
else
{if(!((sCharacter>='0')&&(sCharacter<='9')))
{return false;}}}
else
{if(sCharacter=='.')
{if(!bDot)
{bDot=true;}
else
{return false;}}
else
{if(!((sCharacter>='0')&&(sCharacter<='9')&&(sCharacter!='-')||(sCharacter=='+')))
{return false;}}}}
return true;}
function roundNumber(iNumber,iDecimals)
{if(typeof(iDecimals)==='undefined')
iDecimals=2;var iNumber=parseFloat(iNumber||0);var iDecimals=parseFloat(iDecimals||0);return Math.round(iNumber*Math.pow(10,iDecimals))/Math.pow(10,iDecimals);}
function toMaskNumber(iNumber,dec)
{iNumber=fix(iNumber.toString(),dec||2);var t=iNumber.split(".");var arrayResult=iNumber.replace(/\D/g,'').replace(/^0*/,'').split("").reverse();var result="";var aux=0;var sep=0;for(var i=0;i<arrayResult.length;i++)
{if(i==1)
{result="."+arrayResult[i]+result;}
else
{if(i>1&&aux>=3&&((aux%3)==0))
{result=arrayResult[i]+","+result;aux+=1;sep+=1;}
else
{result=arrayResult[i]+result;if(i>1)
{aux+=1;}}}}
return result;}
function fix(val,dec)
{var a=val.split(".");var r="";if(a.length==1)
{r=a[0]+"."+creaZero(dec);}
else
{if(a[1].length<=dec)
{r=a[0]+"."+a[1]+creaZero(dec-a[1].length);}
else
{r=a[0]+"."+a[1].substr(0,dec);}}
return r;}
function creaZero(cant)
{var a="";for(var i=0;i<cant;i++)
{a+="0";}
return a;}
function toUnmaskNumber(iNumber)
{var aux="";var num=new String(iNumber);var len=num.length;var i=0;for(i=0;i<len;i++){if(num.charAt(i)!=','&&num.charAt(i)!='$'&&num.charAt(i)!=' '&&num.charAt(i)!='%')aux=aux+num.charAt(i);}
return parseFloat(aux,2);}
function compareDates(datea,dateB,porDias)
{var a=datea.split('/');var b=dateB.split('/');x=new Date(a[2],a[1],(porDias)?1:a[0]);y=new Date(b[2],b[1],(porDias)?1:b[0]);return((x-y)<=0)?false:true;}
function diff_date(fecha1,fecha2)
{var f1=fecha1.split('-');fecha1=new Date();fecha1.setDate(f1[2]);fecha1.setMonth(f1[1]);fecha1.setYear(f1[0]);var f2=fecha2.split('-');fecha2=new Date();fecha2.setDate(f2[2]);fecha2.setMonth(f2[1]);fecha2.setYear(f2[0]);var dias=Math.floor((fecha1.getTime()-fecha2.getTime())/(3600000*24));return dias;}
function getField(fieldName,formId)
{if(formId)
{var form=document.getElementById(formId);if(!form){form=document.getElementsByName(formId);if(form){if(form.length>0){form=form[0];}}}
if(form.length>0){return form.elements['form['+fieldName+']'];}
else{return document.getElementById('form['+fieldName+']');}}
else
{return document.getElementById('form['+fieldName+']');}}
function getElementByName(fieldName)
{var elements=document.getElementsByName(fieldName);try{var x=0;if(elements.length===1)
return elements[0];else if(elements.length===0)
return elements[0];else
return elements;}catch(E)
{}}
var myDialog;function commonDialog(type,title,text,buttons,values,callbackFn){myDialog=new leimnud.module.panel();myDialog.options={size:{w:400,h:200},position:{center:true},title:title,control:{close:false,roll:false,drag:true,resize:false},fx:{blinkToFront:false,opacity:true,drag:false,modal:true},theme:"processmaker"};myDialog.make();switch(type){case'question':icon='question.gif';break
case'warning':icon='warning.gif';break
case'error':icon='error.gif';break
default:icon='information.gif';break}
var contentStr='';contentStr+="<div><table border='0' width='100%' > <tr height='70'><td width='60' align='center' >";contentStr+="<img src='/js/maborak/core/images/"+icon+"'></td><td >"+text+"</td></tr>";contentStr+="<tr height='35' valign='bottom'><td colspan='2' align='center'> ";if(buttons.custom&&buttons.customText)
contentStr+="<input type='button' value='"+buttons.customText+"' onclick='myDialog.dialogCallback(4); ';> &nbsp; ";if(buttons.cancel)
contentStr+="<input type='button' value='Cancel' onclick='myDialog.dialogCallback(0);'> &nbsp; ";if(buttons.yes)
contentStr+="<input type='button' value=' Yes ' onclick='myDialog.dialogCallback(1);'> &nbsp; ";if(buttons.no)
contentStr+="<input type='button' value=' No ' onclick='myDialog.dialogCallback(2);'> &nbsp; ";contentStr+="</td></tr>";contentStr+="</table>";myDialog.addContent(contentStr);myDialog.values=values;myDialog.dialogCallback=function(dialogResult){myDialog.remove();if(callbackFn)
callbackFn(dialogResult);}}
function var_dump(obj)
{var o,dump;dump='';if(typeof(obj)=='object'){for(o in obj)if(typeof(obj[o])!=='function')
{dump+=o+'('+typeof(obj[o])+'):'+obj[o]+"\n";}}
else
dump=obj;return dump;}
var currentPopupWindow;function popupWindow(title,url,width,height,callbackFn,autoSizeWidth,autoSizeHeight,modal,showModalColor){modal=(modal===false)?false:true;showModalColor=(showModalColor===false)?false:true;var myPanel=new leimnud.module.panel();currentPopupWindow=myPanel;myPanel.options={size:{w:width,h:height},position:{center:true},title:title,theme:"processmaker",control:{close:true,roll:false,drag:true,resize:false},fx:{blinkToFront:true,opacity:true,drag:true,modal:modal}};if(showModalColor===true)
{}
else
{myPanel.styles.fx.opacityModal.Static='0';}
myPanel.make();myPanel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:url});r.callback=leimnud.closure({Function:function(rpc,myPanel){myPanel.addContent(rpc.xmlhttp.responseText);var myScripts=myPanel.elements.content.getElementsByTagName('SCRIPT');for(var rr=0;rr<myScripts.length;rr++){try{if(myScripts[rr].innerHTML!=='')
if(window.execScript)
window.execScript(myScripts[rr].innerHTML,'javascript');else
window.setTimeout(myScripts[rr].innerHTML,0);}catch(e){alert(e.description);}}
var panelNonContentHeight=62;var panelNonContentWidth=28;myPanel.elements.content.style.padding="0px;";try{if(autoSizeWidth)
myPanel.resize({w:myPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth});if(autoSizeHeight)
myPanel.resize({h:myPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight});}catch(e){alert(_('ID_MSG_AJAX_FAILURE'));}
delete newdiv;delete myScripts;myPanel.command(myPanel.loader.hide);},args:[r,myPanel]});r.make();delete myPanel;}
var lastPopupWindow;function popupWindowObject(title,url,width,height,callbackFn,autoSizeWidth,autoSizeHeight,modal,showModalColor){modal=(modal===false)?false:true;showModalColor=(showModalColor===false)?false:true;var myPanel=new leimnud.module.panel();lastPopupWindow=myPanel;myPanel.options={size:{w:width,h:height},position:{center:true},title:title,theme:"processmaker",control:{close:true,roll:false,drag:true,resize:false},fx:{blinkToFront:true,opacity:true,drag:true,modal:modal}};if(showModalColor===true)
{}
else
{myPanel.styles.fx.opacityModal.Static='0';}
myPanel.make();myPanel.loader.show();var r=new leimnud.module.rpc.xmlhttp({url:url});r.callback=leimnud.closure({Function:function(rpc,myPanel){myPanel.addContent(rpc.xmlhttp.responseText);var myScripts=myPanel.elements.content.getElementsByTagName('SCRIPT');for(var rr=0;rr<myScripts.length;rr++){try{if(myScripts[rr].innerHTML!=='')
if(window.execScript)
window.execScript(myScripts[rr].innerHTML,'javascript');else
window.setTimeout(myScripts[rr].innerHTML,0);}catch(e){alert(e.description);}}
var panelNonContentHeight=62;var panelNonContentWidth=28;myPanel.elements.content.style.padding="0px;";try{if(autoSizeWidth)
myPanel.resize({w:myPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth});if(autoSizeHeight)
myPanel.resize({h:myPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight});}catch(e){alert(_('ID_MSG_AJAX_FAILURE'));}
delete newdiv;delete myScripts;myPanel.command(myPanel.loader.hide);},args:[r,myPanel]});r.make();return myPanel;}
function getAbsoluteLeft(o){oLeft=o.offsetLeft
while(o.offsetParent!=null){oParent=o.offsetParent
oLeft+=oParent.offsetLeft
o=oParent}
return oLeft}
function getAbsoluteTop(o){oTop=o.offsetTop
while(o.offsetParent!=null){oParent=o.offsetParent
oTop+=oParent.offsetTop
o=oParent}
return oTop}
function showHideElement(id)
{var element;if(typeof(id)=='object')element=id;else element=document.getElementById(id);if(element.style.display==='none'){switch(element.type){case'table':element.style.display='table';break;default:element.style.display='';}}else{element.style.display='none';}}
function showHideSearch(id,aElement,openText,closeText)
{var element=document.getElementById(id);if(element.style.display==='none'){if(!closeText)closeText=G_STRINGS.ID_CLOSE_SEARCH;if(aElement){aElement.innerHTML=closeText;var bullet=document.getElementById(aElement.id+'[bullet]');bullet.src='/images/bulletButtonDown.gif';}
switch(element.type){case'table':document.getElementById(id).style.display='table';break;default:document.getElementById(id).style.display='';}}else{if(!openText)openText=G_STRINGS.ID_OPEN_SEARCH;if(aElement){aElement.innerHTML=openText;var bullet=document.getElementById(aElement.id+'[bullet]');bullet.src='/images/bulletButton.gif';}
document.getElementById(id).style.display='none';}}
function loadPage(url,x,y,visibility,div){visibility=typeof(visibility)==='undefined'?'hidden':visibility;var r=new leimnud.module.rpc.xmlhttp({url:url});r.callback=leimnud.closure({Function:function(rpc,div){if(typeof(div)==='undefined')div=createDiv('');if(typeof(x)!=='undefined')div.style.left=x;if(typeof(y)!=='undefined')div.style.top=y;div.innerHTML=rpc.xmlhttp.responseText;var myScripts=div.getElementsByTagName('SCRIPT');for(var rr=0;rr<myScripts.length;rr++){try{if(myScripts[rr].innerHTML!=='')
if(window.execScript)
window.execScript(myScripts[rr].innerHTML,'javascript');else
window.setTimeout(myScripts[rr].innerHTML,0);}catch(e){alert(e.description);}}
delete div;delete myScripts;},args:[r,div]});r.make();}
function createDiv(id){var newdiv=document.createElement('div');newdiv.setAttribute('id',id);newdiv.style.position="absolute";newdiv.style.left=0;newdiv.style.top=0;newdiv.style.visibility="hidden";document.body.appendChild(newdiv);return newdiv;}
function refillText(fldName,ajax_server,values){var objetus;objetus=get_xmlhttp();objetus.open("GET",ajax_server+"?"+values,false);objetus.onreadystatechange=function(){if(objetus.readyState==1)
{var textfield=document.getElementById('form['+fldName+']');if(!isdefined(textfield))
var textfield=document.getElementById(fldName);textfield.value='';}
else if(objetus.readyState==4)
{if(objetus.status==200)
{var xmlDoc=objetus.responseXML;if(xmlDoc){var textfield=document.getElementById('form['+fldName+']');if(!isdefined(textfield))
var textfield=document.getElementById(fldName);var dataArray=xmlDoc.getElementsByTagName('value');if(dataArray[0].firstChild)
if((dataArray[0].firstChild.xml)!='_vacio'){textfield.value=dataArray[0].firstChild.xml;if(textfield.type!='hidden')
if(textfield.onchange)
textfield.onchange();}}}
else
{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function refillCaption(fldName,ajax_server,values){var objetus;objetus=get_xmlhttp();objetus.open("GET",ajax_server+"?"+values,false);objetus.onreadystatechange=function(){if(objetus.readyState==1)
{var textfield=document.getElementById('FLD_'+fldName);textfield.innerHTML='';}
else if(objetus.readyState==4)
{if(objetus.status==200)
{var xmlDoc=objetus.responseXML;if(xmlDoc){var textfield=document.getElementById('FLD_'+fldName);var dataArray=xmlDoc.getElementsByTagName('value');if(dataArray[0].firstChild)
if((dataArray[0].firstChild.xml)!='_vacio')
textfield.innerHTML=dataArray[0].firstChild.xml;}}
else
{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function refillDropdown(fldName,ajax_server,values,InitValue)
{var objetus;objetus=get_xmlhttp();objetus.open("GET",ajax_server+"?"+values,false);objetus.onreadystatechange=function(){if(objetus.readyState==1)
{var dropdown=document.getElementById('form['+fldName+']');while(dropdown.hasChildNodes())
dropdown.removeChild(dropdown.childNodes[0]);}
else if(objetus.readyState==4)
{if(objetus.status==200)
{var xmlDoc=objetus.responseXML;if(xmlDoc){var dropdown=document.getElementById('form['+fldName+']');var dataArray=xmlDoc.getElementsByTagName('item');itemsNumber=dataArray.length;if(InitValue==true)itemsNumber=dataArray.length-1;for(var i=0;i<itemsNumber;i++){dropdown.options[dropdown.length]=new Option(dataArray[i].firstChild.xml,dataArray[i].attributes[0].value);if(InitValue==true){if(dropdown.options[dropdown.length-1].value==dataArray[dataArray.length-1].firstChild.xml)
dropdown.options[i].selected=true;}}
dropdown.onchange();}}
else
{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function iframe_get_xmlhttp(){try{xmlhttp=new ActiveXObject('Msxml2.XMLHTTP2');}catch(e){try{xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){xmlhttp=false;}}
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}
return xmlhttp;}
function get_xmlhttp(){try{xmlhttp=false;if(window.ActiveXObject)
xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
catch(e){try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
catch(E){xmlhttp=false;}}
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}
return xmlhttp;}
function refillTextError(div_container,fldName,ajax_server,values)
{var objetus;objetus=get_xmlhttp();objetus.open("GET",ajax_server+"?"+values,false);objetus.onreadystatechange=function(){if(objetus.readyState==1)
{var textfield=document.getElementById('form['+fldName+']');textfield.value='';document.getElementById(div_container).innerHTML='';}
else if(objetus.readyState==4)
{if(objetus.status==200)
{var xmlDoc=objetus.responseXML;if(xmlDoc){var textfield=document.getElementById('form['+fldName+']');var dataArray=xmlDoc.getElementsByTagName('value');textfield.value=dataArray[0].firstChild.xml;var dataArray=xmlDoc.getElementsByTagName('message');if(dataArray[0].firstChild)
document.getElementById(div_container).innerHTML='<b>'+dataArray[0].firstChild.xml+'</b>';}}
else
{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function iframe_get_xmlhttp(){try{xmlhttp=new ActiveXObject('Msxml2.XMLHTTP5');}catch(e){try{xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){xmlhttp=false;}}
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}
return xmlhttp;}
function iframe_ajax_init(ajax_server,div_container,values,callback){var objetus;objetus=iframe_get_xmlhttp();objetus.open('GET',ajax_server+'?'+values,true);objetus.onreadystatechange=function(){if(objetus.readyState==1){document.getElementById(div_container).style.display='';document.getElementById(div_container).innerHTML='...';}
else if(objetus.readyState==4){if(objetus.status==200){document.getElementById(div_container).innerHTML=objetus.responseText;if(callback!='')
callback();}
else{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function iframe_ajax_init_2(ajax_server,div_container,values,callback){var objetus;objetus=iframe_get_xmlhttp();objetus.open('GET',ajax_server+'?'+values,true);objetus.onreadystatechange=function(){if(objetus.readyState==1){div_container.style.display='';div_container.innerHTML='...';}
else if(objetus.readyState==4){if(objetus.status==200){div_container.innerHTML=objetus.responseText;if(callback!='')
callback();}
else{window.alert('error-['+objetus.status+']-'+objetus.responseText);}}}
objetus.send(null);}
function myEmptyCallback(){}
function disable(obj){obj.disabled=true;return;}
function enable(obj){obj.disabled=false;return;}
function disableById(id){obj=getField(id);obj.disabled=true;return;}
function enableById(id){obj=getField(id);obj.disabled=false;return;}
function visible(obj){if(obj.style){obj.style.visibility='visible';}
return;}
function hidden(obj){if(obj.style){obj.style.visibility='hidden';}
return;}
function visibleById(id){obj=getField(id);obj.style.visibility='visible';return;}
function hiddenById(id){obj=getField(id);obj.style.visibility='hidden';return;}
function hiddenRowById(id){row='DIV_'+id+'.style.visibility = \'hidden\';';hiden='DIV_'+id+'.style.display = \'none\';';eval(row);eval(hiden);return;}
function visibleRowById(id){row='DIV_'+id+'.style.visibility = \'visible\';';block='DIV_'+id+'.style.display = \'block\';';eval(row);eval(block);return;}
function setFocus(obj){obj.focus();return;}
function setFocusById(id){obj=getField(id);setFocus(obj);return;}
function submitForm(){document.forms[0].submit();return;}
function changeValue(id,newValue){obj=getField(id);obj.value=newValue;return;}
function getValue(obj){return obj.value;}
function getValueById(id){obj=getField(id);return obj.value;}
function removeCurrencySign(snumber){var aux='';var num=new String(snumber);var len=num.length;var i=0;for(i=0;!(i>=len);i++)
if(num.charAt(i)!=','&&num.charAt(i)!='$'&&num.charAt(i)!=' ')aux=aux+num.charAt(i);return aux;}
function removePercentageSign(snumber){var aux='';var num=new String(snumber);var len=num.length;var i=0;for(i=0;!(i>=len);i++)
if(num.charAt(i)!=','&&num.charAt(i)!='%'&&num.charAt(i)!=' ')aux=aux+num.charAt(i);return aux;}
function toReadOnly(obj){if(obj){obj.readOnly='readOnly';obj.style.background='#CCCCCC';}
return;}
function toReadOnlyById(id){obj=getField(id);if(obj){obj.readOnly='readOnly';obj.style.background='#CCCCCC';}
return;}
function getGridField(Grid,Row,Field){obj=document.getElementById('form['+Grid+']'+'['+Row+']'+'['+Field+']');return obj;}
function getGridValueById(Grid,Row,Field){obj=getGridField(Grid,Row,Field);if(obj)
return obj.value;else
return'';}
function Number_Rows_Grid(Grid,Field){Number_Rows=1;if(getGridField(Grid,Number_Rows,Field)){Number_Rows=0;while(getGridField(Grid,(Number_Rows+1),Field))
Number_Rows++;return Number_Rows;}
else
return 0;}
function attachFunctionEventOnChange(Obj,TheFunction){Obj.oncustomize=TheFunction;}
function attachFunctionEventOnChangeById(Id,TheFunction){Obj=getField(Id);Obj.oncustomize=TheFunction;}
function attachFunctionEventOnKeypress(Obj,TheFunction){Obj.attachEvent('onkeypress',TheFunction);}
function attachFunctionEventOnKeypressById(Id,TheFunction){Obj=getField(Id);Obj.attachEvent('onkeypress',TheFunction);}
function unselectOptions(field){var radios=document.getElementById('form['+field+']');if(radios){var inputs=radios.getElementsByTagName('input');if(inputs){for(var i=0;i<inputs.length;++i){inputs[i].checked=false;}}}}
function validDate(TheField,Required){var date=TheField.split("-");var date1=date[0];var date2=date[1];var date3=date[2];var TheDay,TheMonth,TheYear;if((date1.length==4)&&(!TheYear))
TheYear=date1;if(date1.length==2)
if((date1>0)&&(date1<=12)&&(!TheMonth))
TheMonth=date1;else
if((date1>0)&&(date1<=31)&&(!TheDay))
TheDay=date1;else
TheYear=date1;if((date2.length==4)&&(!TheYear))
TheYear=date2;if(date2.length==2)
if((date2>0)&&(date2<=12)&&(!TheMonth))
TheMonth=date2;else
if((date2>0)&&(date2<=31)&&(!TheDay))
TheDay=date2;else
TheYear=date2;if((date3.length==4)&&(!TheYear))
TheYear=date3;if(date3.length==2)
if((date3>0)&&(date3<=12)&&(!TheMonth))
TheMonth=date3;else
if((date3>0)&&(date3<=31)&&(!TheDay))
TheDay=date3;else
TheYear=date3;if(!TheYear||!TheMonth||!TheDay)
return false;if((Required)||(Required=='true'))
if((TheYear==0)||(TheMonth==0)||(TheDay==0))
return false;if(TheMonth==02)
if(TheDay>29)
return false;if((TheMonth!=02)&&(TheMonth<13)&&(TheMonth>0))
if(TheDay>30)
return false;return true;}
function globalEval(scriptCode){if(scriptCode!=='')
if(window.execScript)
window.execScript(scriptCode,'javascript');else
window.setTimeout(scriptCode,0);}
function switchImage(oImg,url1,url2){if(oImg&&(url2!=='')){oImg.src=(oImg.src.substr(oImg.src.length-url1.length,url1.length)===url1)?url2:url1;}}
function MM_preloadImages(){var d=document;if(d.images){if(!d.MM_p)d.MM_p=new Array();var i,j=d.MM_p.length,a=MM_preloadImages.arguments;for(i=0;i<a.length;i++)
if(a[i].indexOf("#")!=0){d.MM_p[j]=new Image;d.MM_p[j++].src=a[i];}}}
function backImage(oImg,p){oImg.style.background=p;}
var lc=false;var sh=function(a,i)
{var c=(a.nextSibling.nextSibling)?a.nextSibling.nextSibling:a.nextSibling;if(lc&&lc.c!=i){lc.d.style.display='none';lc.a.style.color='#666';}
lc={d:c,c:i,a:a};a.style.color=(c.style.display==''||c.style.display=='none')?"black":"#666";c.style.display=(!c.style.display||c.style.display=='none')?"block":"none";}
function dynaformSetFocus(){var inputs=document.getElementsByTagName('input');if(inputs.length>0){for(i in inputs){type=inputs[i].type;if(type=="text"||type=="radio"||type=="checkbox"||type=="file"||type=="password"){try{inputs[i].focus();}catch(e){}
return false;}}}else{var ta=document.getElementsByTagName('textarea');if(ta.length>0){inputs[0].focus();return false;}}
return false;}
function idSet(name){var inputs=document.getElementsByTagName('input');if(inputs.length>0){for(i in inputs){id=inputs[i].id;if(id=="form["+name+"_label]"){if(inputs[i].value.trim())
var valueLabel=inputs[i].value;else
var valueLabel="Empty";}
if(id=="form["+name+"]"){try{if(valueLabel!="Empty"){if(!inputs[i].value)
inputs[i].value=valueLabel;}else
inputs[i].value="";}catch(e){}}}}
return false;}
function htmlentities(string,quote_style,charset,double_encode){var hash_map=get_html_translation_table('HTML_ENTITIES',quote_style),symbol='';string=string==null?'':string+'';if(!hash_map){return false;}
if(quote_style&&quote_style==='ENT_QUOTES'){hash_map["'"]='&#039;';}
if(!!double_encode||double_encode==null){for(symbol in hash_map){if(hash_map.hasOwnProperty(symbol)){string=string.split(symbol).join(hash_map[symbol]);}}}else{string=string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g,function(ignore,text,entity){for(symbol in hash_map){if(hash_map.hasOwnProperty(symbol)){text=text.split(symbol).join(hash_map[symbol]);}}
return text+entity;});}
return string.toString();}
function utf8_encode(argString){var utftext="",start,end,stringl=0;var string=argString;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++;}
else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128);}
else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128);}
if(enc!==null){if(end>start){utftext+=string.slice(start,end);}
utftext+=enc;start=end=n+1;}}
if(end>start){utftext+=string.slice(start,stringl);}
return utftext;}
function base64_encode(data){var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var o1,o2,o3,h1,h2,h3,h4,bits,i=0,ac=0,enc="",tmp_arr=[];if(!data){return data;}
data=utf8_encode(data+'');do{o1=data.charCodeAt(i++);o2=data.charCodeAt(i++);o3=data.charCodeAt(i++);bits=o1<<16|o2<<8|o3;h1=bits>>18&0x3f;h2=bits>>12&0x3f;h3=bits>>6&0x3f;h4=bits&0x3f;tmp_arr[ac++]=b64.charAt(h1)+b64.charAt(h2)+b64.charAt(h3)+b64.charAt(h4);}while(i<data.length);enc=tmp_arr.join('');switch(data.length%3){case 1:enc=enc.slice(0,-2)+'==';break;case 2:enc=enc.slice(0,-1)+'=';break;}
return enc;}
function get_html_translation_table(table,quote_style){var entities={},hash_map={},decimal;var constMappingTable={},constMappingQuoteStyle={};var useTable={},useQuoteStyle={};constMappingTable[0]='HTML_SPECIALCHARS';constMappingTable[1]='HTML_ENTITIES';constMappingQuoteStyle[0]='ENT_NOQUOTES';constMappingQuoteStyle[2]='ENT_COMPAT';constMappingQuoteStyle[3]='ENT_QUOTES';useTable=!isNaN(table)?constMappingTable[table]:table?table.toUpperCase():'HTML_SPECIALCHARS';useQuoteStyle=!isNaN(quote_style)?constMappingQuoteStyle[quote_style]:quote_style?quote_style.toUpperCase():'ENT_COMPAT';if(useTable!=='HTML_SPECIALCHARS'&&useTable!=='HTML_ENTITIES'){throw new Error("Table: "+useTable+' not supported');}
entities['38']='&amp;';if(useTable==='HTML_ENTITIES'){entities['160']='&nbsp;';entities['161']='&iexcl;';entities['162']='&cent;';entities['163']='&pound;';entities['164']='&curren;';entities['165']='&yen;';entities['166']='&brvbar;';entities['167']='&sect;';entities['168']='&uml;';entities['169']='&copy;';entities['170']='&ordf;';entities['171']='&laquo;';entities['172']='&not;';entities['173']='&shy;';entities['174']='&reg;';entities['175']='&macr;';entities['176']='&deg;';entities['177']='&plusmn;';entities['178']='&sup2;';entities['179']='&sup3;';entities['180']='&acute;';entities['181']='&micro;';entities['182']='&para;';entities['183']='&middot;';entities['184']='&cedil;';entities['185']='&sup1;';entities['186']='&ordm;';entities['187']='&raquo;';entities['188']='&frac14;';entities['189']='&frac12;';entities['190']='&frac34;';entities['191']='&iquest;';entities['192']='&Agrave;';entities['193']='&Aacute;';entities['194']='&Acirc;';entities['195']='&Atilde;';entities['196']='&Auml;';entities['197']='&Aring;';entities['198']='&AElig;';entities['199']='&Ccedil;';entities['200']='&Egrave;';entities['201']='&Eacute;';entities['202']='&Ecirc;';entities['203']='&Euml;';entities['204']='&Igrave;';entities['205']='&Iacute;';entities['206']='&Icirc;';entities['207']='&Iuml;';entities['208']='&ETH;';entities['209']='&Ntilde;';entities['210']='&Ograve;';entities['211']='&Oacute;';entities['212']='&Ocirc;';entities['213']='&Otilde;';entities['214']='&Ouml;';entities['215']='&times;';entities['216']='&Oslash;';entities['217']='&Ugrave;';entities['218']='&Uacute;';entities['219']='&Ucirc;';entities['220']='&Uuml;';entities['221']='&Yacute;';entities['222']='&THORN;';entities['223']='&szlig;';entities['224']='&agrave;';entities['225']='&aacute;';entities['226']='&acirc;';entities['227']='&atilde;';entities['228']='&auml;';entities['229']='&aring;';entities['230']='&aelig;';entities['231']='&ccedil;';entities['232']='&egrave;';entities['233']='&eacute;';entities['234']='&ecirc;';entities['235']='&euml;';entities['236']='&igrave;';entities['237']='&iacute;';entities['238']='&icirc;';entities['239']='&iuml;';entities['240']='&eth;';entities['241']='&ntilde;';entities['242']='&ograve;';entities['243']='&oacute;';entities['244']='&ocirc;';entities['245']='&otilde;';entities['246']='&ouml;';entities['247']='&divide;';entities['248']='&oslash;';entities['249']='&ugrave;';entities['250']='&uacute;';entities['251']='&ucirc;';entities['252']='&uuml;';entities['253']='&yacute;';entities['254']='&thorn;';entities['255']='&yuml;';}
if(useQuoteStyle!=='ENT_NOQUOTES'){entities['34']='&quot;';}
if(useQuoteStyle==='ENT_QUOTES'){entities['39']='&#39;';}
entities['60']='&lt;';entities['62']='&gt;';for(decimal in entities){if(entities.hasOwnProperty(decimal)){hash_map[String.fromCharCode(decimal)]=entities[decimal];}}
return hash_map;}
function stripslashes(str){return(str+'').replace(/\\(.?)/g,function(s,n1){switch(n1){case'\\':return'\\';case'0':return'\0';case'':return'';default:return n1;}});}
function addslashes(str){return(str+'').replace(/([\\"'])/g,"\\$1").replace(/\u0000/g,"\\0");}
function setNestedProperty(obj,propertyName,propertyValue){var oTarget=obj;for(var i=0;i<propertyName.length;i++){if(i==(propertyName.length-1)){oTarget[propertyName[i]]=propertyValue;return false;}
oTarget=oTarget[propertyName[i]];}}
function getBrowserClient(){var aBrowFull=new Array("opera","msie","firefox","opera","safari");var sInfo=navigator.userAgent.toLowerCase();sBrowser="";for(var i=0;i<aBrowFull.length;i++){if((sBrowser=="")&&(sInfo.indexOf(aBrowFull[i])!=-1)){sBrowser=aBrowFull[i];sVersion=String(parseFloat(sInfo.substr(sInfo.indexOf(aBrowFull[i])+aBrowFull[i].length+1)));return{name:sBrowser,browser:sBrowser,version:sVersion}}}
return false;};var _BROWSER=getBrowserClient();function createCookie(name,value,days){if(days){var date=new Date();date.setTime(date.getTime()+(days*24*60*60*1000));var expires="; expires="+date.toGMTString();}else var expires="";document.cookie=name+"="+value+expires+"; path=/";}
function readCookie(name){var ca=document.cookie.split(';');var nameEQ=name+"=";for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0)return c.substring(nameEQ.length,c.length);}
return null;}
function eraseCookie(name){createCookie(name,"",-1);}
function highlightRow(o,color){o.style.background=color}
String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g,"");}
function clearCalendar(id){document.getElementById(id).value='';document.getElementById(id+'[div]').innerHTML='';setTimeout('enableCalendar()',350);}
function lockCalendar(){G_CALENDAR_MEM_OFFSET='lock';}
function enableCalendar(){G_CALENDAR_MEM_OFFSET='enable';}
function parseDateFromMask(inputArray,mask){result=mask;result=result.replace("Y",inputArray.year);year=new String(inputArray.year);result=result.replace("y",year.substr(2,3));result=result.replace("m",inputArray.month);result=result.replace("d",inputArray.day);result=result.replace("h",inputArray.hour);result=result.replace("i",inputArray.minute);return result;}
Array.prototype.walk=function(funcionaplicada){for(var i=0,parar=false;i<this.length&&!parar;i++)
parar=funcionaplicada(this[i],i);return(this.length==i)?false:(i-1);}
Array.prototype.find=function(q){var dev=this.walk(function(elem){if(elem==q)
return true;});if(this[dev]==q)return dev;else return-1;}
Array.prototype.drop=function(x){this.splice(x,1);}
Array.prototype.deleteByValue=function(val){var eindex=this.find(val);this.drop(eindex);}
function Timer(functionName,time){setTimeout(functionName,time*1000);}
function PMOS_TemporalMessage(timeToHide){fade('temporalMessageTD','inOut');if(typeof(timeToHide)!='undefined'){Timer(function(){try{document.getElementById('temporalMessageTD').style.display='none';}catch(e){}},timeToHide);}}
function msgBox(msg,type,callbackAccept,callbackCancel){type=typeof(type)!='undefined'?type:'info';acceptEv=typeof(callbackAccept)!='undefined'?callbackAccept:false;cancelEv=typeof(callbackCancel)!='undefined'?callbackCancel:false;switch(type){case'alert':new leimnud.module.app.alert().make({label:msg,width:350,action:function(){if(acceptEv){setTimeout(acceptEv,1);}}.extend(this)});break;case'info':new leimnud.module.app.info().make({label:msg,width:350,action:function(){if(acceptEv){setTimeout(acceptEv,1);}}.extend(this)});break;case'confirm':if(cancelEv){new leimnud.module.app.confirm().make({label:msg,action:function(){if(acceptEv){setTimeout(acceptEv,0);}}.extend(this),cancel:function(){setTimeout(cancelEv,1);}.extend(this)});}else{new leimnud.module.app.confirm().make({label:msg,action:function(){if(acceptEv){setTimeout(acceptEv,1);}}.extend(this)});}
break;}}
function executeEvent(id,ev){switch(ev){case'click':document.getElementById(id).checked=true;if(document.getElementById(id).onclick){try{document.getElementById(id).onclick();}catch(e){}}
break;}}
function getClientWindowSize(){var wSize=[1024,768];if(typeof window.innerWidth!='undefined'){wSize=[window.innerWidth,window.innerHeight];}else if(typeof document.documentElement!='undefined'&&typeof document.documentElement.clientWidth!='undefined'&&document.documentElement.clientWidth!=0){wSize=[document.documentElement.clientWidth,document.documentElement.clientHeight];}else{var body=document.getElementsByTagName('body');if(typeof body!='undefined'&&typeof body[0]!='undefined'&&typeof body[0].clientWidth!='undefined'){wSize=[document.getElementsByTagName('body')[0].clientWidth,document.getElementsByTagName('body')[0].clientHeight];}}
return{width:wSize[0],height:wSize[1]};}
function popUp(URL,width,height,left,top,resizable){window.open(URL,'','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable='+resizable+',width='+width+',height='+height+',left = '+left+',top = '+top+'');}
function XHRequest(){return new leimnud.module.rpc.xmlhttp;}
function removeValue(id){if(document.getElementById('form['+id+']'))
document.getElementById('form['+id+']').value='';else if(document.getElementById(id))
document.getElementById(id).value='';fireEvent(document.getElementById(id),'change');}
function datePicker4(obj,id,mask,startDate,endDate,showTIme,idIsoDate)
{__lastMask__=mask;if(showTIme=='false'){showTIme=false;}
Calendar.setup({inputField:id,dateFormat:mask,trigger:id+"[btn]",bottomBar:true,min:startDate,max:endDate,animation:_BROWSER.name=='msie'?false:true,showTime:showTIme,opacity:1,onSelect:function(){this.hide();fireEvent(document.getElementById(id),'change');}});}
function fireEvent(element,event)
{if(document.createEventObject){var evt=document.createEventObject();return element.fireEvent('on'+event,evt)}
else{var evt=document.createEvent("HTMLEvents");evt.initEvent(event,true,true);return!element.dispatchEvent(evt);}}
function elementAttributesNS(e,ns)
{if(!this.__namespaceRegexps)
this.__namespaceRegexps={};var regexp=this.__namespaceRegexps[ns];if(!regexp){this.__namespaceRegexps[ns]=regexp=ns?eval("/^"+ns+":(.+)/"):/^([^:]*)$/;}
var result={};var atts=e.attributes;var l=atts.length;for(var i=0;i<l;i++){var m=atts[i].name.match(regexp);if(m)
result[m[1]]=atts[i].value;}
return result;}
function _()
{var argv=_.arguments;var argc=argv.length;if(typeof TRANSLATIONS!='undefined'&&TRANSLATIONS){if(typeof TRANSLATIONS[argv[0]]!='undefined'){if(argc>1){trn=TRANSLATIONS[argv[0]];for(i=1;i<argv.length;i++){trn=trn.replace('{'+(i-1)+'}',argv[i]);}}
else{trn=TRANSLATIONS[argv[0]];}}
else{trn='**'+argv[0]+'**';}}
else{PMExt.error('Processmaker JS Core Error','The TRANSLATIONS global object is not loaded!');trn='';}
return trn;}
function stringReplace(strSearch,stringReplace,str)
{var expression=eval("/"+strSearch+"/g");return str.replace(expression,stringReplace);}
var mb_strlen=function(str){str=str||'';var len=0;for(var i=0;i<str.length;i++){len+=str.charCodeAt(i)<0||str.charCodeAt(i)>255?2:1;}
return len;};var stripNonNumeric=function(str){str+='';var rgx=/^\d|\.|-$/;var out='';for(var i=0;i<str.length;i++){if(rgx.test(str.charAt(i))){if(!((str.charAt(i)=='.'&&out.indexOf('.')!=-1)||(str.charAt(i)=='-'&&out.length!=0))){out+=str.charAt(i);}}}
return out;};
var TimeToFade=1000.0;function fade(eid,inOut){inOut=(typeof(inOut)!='undefined')?true:false;var element=document.getElementById(eid);if(element==null)
return;if(element.FadeState==null)
{if(element.style.opacity==null||element.style.opacity==''||element.style.opacity=='1')
{element.FadeState=2;}
else
{element.FadeState=-2;}}
if(element.FadeState==1||element.FadeState==-1)
{element.FadeState=element.FadeState==1?-1:1;element.FadeTimeLeft=TimeToFade-element.FadeTimeLeft;}
else
{element.FadeState=element.FadeState==2?-1:1;element.FadeTimeLeft=TimeToFade;if(inOut){setTimeout("animateFadeInOut("+new Date().getTime()+",'"+eid+"')",33);}
else
setTimeout("animateFade("+new Date().getTime()+",'"+eid+"')",33);}}
function animateFade(lastTick,eid)
{var curTick=new Date().getTime();var elapsedTicks=curTick-lastTick;var element=document.getElementById(eid);if(element.FadeTimeLeft<=elapsedTicks)
{element.style.opacity=element.FadeState==1?'1':'0';element.style.filter='alpha(opacity = '+(element.FadeState==1?'100':'0')+')';element.FadeState=element.FadeState==1?2:-2;return;}
element.FadeTimeLeft-=elapsedTicks;var newOpVal=element.FadeTimeLeft/TimeToFade;if(element.FadeState==1)
newOpVal=1-newOpVal;element.style.opacity=newOpVal;element.style.filter='alpha(opacity = '+(newOpVal*100)+')';setTimeout("animateFade("+curTick+",'"+eid+"')",33);}
function animateFadeInOut(lastTick,eid)
{var curTick=new Date().getTime();var elapsedTicks=curTick-lastTick;var element=document.getElementById(eid);if(element.FadeTimeLeft<=elapsedTicks)
{element.style.opacity=element.FadeState==1?'1':'0';element.style.filter='alpha(opacity = '+(element.FadeState==1?'100':'0')+')';element.FadeState=element.FadeState==1?2:-2;fade(eid);return;}
element.FadeTimeLeft-=elapsedTicks;var newOpVal=element.FadeTimeLeft/TimeToFade;if(element.FadeState==1)
newOpVal=1-newOpVal;element.style.opacity=newOpVal;element.style.filter='alpha(opacity = '+(newOpVal*100)+')';setTimeout("animateFadeInOut("+curTick+",'"+eid+"')",33);}
var Effect=function(element){this.max=255;this.min=100;this.incrementor=1;this.element=(typeof(element)!='undefined')?element:"text";this.r=this.max;this.g=this.max;this.b=this.max;this.target='';this.iTarget='';switch(this.element){case"text":this.target="color";this.iTarget="this.max-";break;case"background":this.iTarget="";this.target="backgroundColor";break;}
this.setElement=function(e){this.element=e;switch(this.element){case"text":this.target="color";this.iTarget="this.max-";break;case"background":this.iTarget="";this.target="backgroundColor";break;}}
this.setMin=function(e){this.min=e;}
this.setMax=function(e){this.max=e;}
this.fadeIn=function(obj){variation=-1;increment=variation*this.incrementor;eval('obj.style.'+this.target+'="RGB("+('+this.iTarget+'this.r)+","+('+this.iTarget+'this.g)+","+('+this.iTarget+'this.b)+")"');this.r+=increment;this.b+=increment;this.g+=increment;eso=obj;elincrement=variation;if(this.r>this.min&&this.r<this.max){seguir=window.setTimeout("oEffect.fadeIn(eso,elincrement)",10);}
else{this.r-=increment;this.g-=increment;this.b-=increment;}}
this.fadeOut=function(obj){variation=1;increment=variation*this.incrementor;eval('obj.style.'+this.target+'="RGB("+('+this.iTarget+'this.r)+","+('+this.iTarget+'this.g)+","+('+this.iTarget+'this.b)+")"');this.r+=increment;this.b+=increment;this.g+=increment;eso=obj;elincrement=variation;if(this.r>this.min&&this.r<this.max){seguir=window.setTimeout("oEffect.fadeOut(eso,elincrement)",10);}
else{this.r-=increment;this.g-=increment;this.b-=increment;}}
this.fade=function(obj){variation=-1;increment=variation*this.incrementor;eval('obj.style.'+this.target+'="RGB("+('+this.iTarget+'this.r)+","+('+this.iTarget+'this.g)+","+('+this.iTarget+'this.b)+")"');this.r+=increment;this.b+=increment;this.g+=increment;eso=obj;elincrement=variation;if(this.r>this.min&&this.r<this.max){seguir=window.setTimeout("oEffect.fade(eso,elincrement)",10);}
else{this.r-=increment;this.g-=increment;this.b-=increment;if(typeof('cb')!='undefined'){setTimeout("oEffect.fadeOut(document.getElementById('"+obj.id+"'))",0);}else{setTimeout("oEffect.fadeOut(document.getElementById('"+obj.id+"'))",0);}}}}
var oEffect=new Effect();
function WebResource(uri,parameters,method)
{var request;request=get_xmlhttp();var response;try
{if(!method)method="POST";if(parameters!=''){parameters+='&rand='+Math.random();}
else{parameters='rand='+Math.random();}
data=parameters;request.open(method,uri+((method==='GET')?('?'+data):''),false);if(method==='POST')request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");request.send(((method==='GET')?null:data));var type=request.getResponseHeader('Content-Type');var reType=/\w+\/\w+/;var maType=reType.exec(type);type=maType?maType[0]:'';}catch(ss)
{alert("error"+ss.message);}
switch(type)
{case"text/json":try
{eval('response='+request.responseText+';');break;}
catch(err)
{}
G.alert('<textarea style="width:100%;" rows="9">'+request.responseText+'</textarea>');return;break;case"text/javascript":if(window.execScript)
window.execScript(request.responseText,'javascript');else
window.setTimeout(request.responseText,0);break;case"text/html":response=$dce('div');response.innerHTML=request.responseText;break;}
return response;}
function __wrCall(uri,func,parameters)
{var param=[];for(var a=0;a<parameters.length;a++)param.push(parameters[a]);return WebResource(uri,"function="+func+"&parameters="+encodeURIComponent(param.toJSONString()));}
if(!Object.prototype.toJSONString){Array.prototype.toJSONString=function(){var a=['['],b,i,l=this.length,v;function p(s){if(b){a.push(',');}
a.push(s);b=true;}
for(i=0;i<l;i+=1){v=this[i];switch(typeof v){case'undefined':case'function':case'unknown':break;case'object':if(v){if(typeof v.toJSONString==='function'){p(v.toJSONString());}}else{p("null");}
break;default:p(v.toJSONString());}}
a.push(']');return a.join('');};Boolean.prototype.toJSONString=function(){return String(this);};Date.prototype.toJSONString=function(){function f(n){return n<10?'0'+n:n;}
return'"'+this.getFullYear()+'-'+
f(this.getMonth()+1)+'-'+
f(this.getDate())+'T'+
f(this.getHours())+':'+
f(this.getMinutes())+':'+
f(this.getSeconds())+'"';};Number.prototype.toJSONString=function(){return isFinite(this)?String(this):"null";};Object.prototype.toJSONString=function(){var a=['{'],b,k,v;function p(s){if(b){a.push(',');}
a.push(k.toJSONString(),':',s);b=true;}
for(k in this){if(this.hasOwnProperty(k)){v=this[k];switch(typeof v){case'undefined':case'function':case'unknown':break;case'object':if(v){if(typeof v.toJSONString==='function'){p(v.toJSONString());}}else{p("null");}
break;default:p(v.toJSONString());}}}
a.push('}');return a.join('');};(function(s){var m={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'};s.parseJSON=function(filter){try{if(/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(this)){var j=eval('('+this+')');if(typeof filter==='function'){function walk(k,v){if(v&&typeof v==='object'){for(var i in v){if(v.hasOwnProperty(i)){v[i]=walk(i,v[i]);}}}
return filter(k,v);}
j=walk('',j);}
return j;}}catch(e){}
return this;};s.toJSONString=function(){if(/["\\\x00-\x1f]/.test(this)){return'"'+this.replace(/([\x00-\x1f\\"])/g,function(a,b){var c=m[b];if(c){return c;}
c=b.charCodeAt();return'\\u00'+
Math.floor(c/16).toString(16)+
(c%16).toString(16);})+'"';}
return'"'+this+'"';};})(String.prototype);}
if(!String.prototype.trim)
{String.prototype.trim=function()
{var cadena=this;for(i=0;i<cadena.length;)
{if(cadena.charAt(i)==" ")
cadena=cadena.substr(i+1,cadena.length);else
break;}
for(i=cadena.length-1;i>=0;i=cadena.length-1)
{if(cadena.charAt(i)==" ")
cadena=cadena.substr(0,i);else
break;}
return cadena.toString();}}
function DVEditor(where,body,oHiddenInput,height,mode)
{var me=this;var hiddenInput=oHiddenInput;var iframe=$dce("iframe");iframe.style.width="100%";iframe.style.height=height;iframe.style.margin="0px";iframe.style.padding="0px";iframe.style.border="none";where.appendChild(iframe);var head=document.childNodes[0].childNodes[0];var header='';if(iframe.contentWindow)
{var doc=iframe.contentWindow.document;}
else
{var doc=iframe.contentDocument;}
var _header=$dce("head");for(var i=0;i<head.childNodes.length;i++){try{if((head.childNodes[i].tagName==='LINK')&&(head.childNodes[i].type="text/css"))
{_header.appendChild(head.childNodes[i].cloneNode(true));}
else
{}}
catch(e)
{}}
header=_header.innerHTML;doc.open();doc.write('<html><head>'+header+'</head><body style="height:100%;padding:0px;margin:0px;border:none;background-color:ThreeDHighlight;cursor:text;">'+body+'</body></html>');doc.close();doc.designMode="on";if(mode=="edit"){doc.contentEditable=true;}else{doc.contentEditable=false;}
this.doc=doc;me.insertHTML=function(html)
{var cmd='inserthtml';var bool=false;var value=html;try
{doc.execCommand(cmd,bool,value);}catch(e){}
return false;};me.command=function()
{var cmd=this.getAttribute('name');var bool=false;var value=this.getAttribute('cmdValue')||null;if(value=='promptUser')
value=prompt((typeof(G_STRINGS[this.getAttribute('promptText')])!=='undefined')?G_STRINGS[this.getAttribute('promptText')]:this.getAttribute('promptText'));try
{doc.execCommand(cmd,bool,value);}catch(e){}
return false;}
me.loadToolBar=function(uri)
{var tb=WebResource(uri);iframe.parentNode.insertBefore(tb,iframe);me.setToolBar(tb);}
me.setToolBar=function(toolbar)
{var buttons=toolbar.getElementsByTagName('area');for(var b=0;b<buttons.length;b++)
{buttons[b].onclick=me.command;}}
me.getHTML=function()
{var body='';try{body=doc.getElementsByTagName('body')[0];body=body.innerHTML;}catch(e){}
return body;}
me.setHTML=function(html)
{try{body=doc.getElementsByTagName('body')[0];body.innerHTML=html;}catch(e){}
return body;}
me.refreshHidden=function()
{if(hiddenInput)
{var html=me.getHTML();var raiseOnChange=hiddenInput.value!==html;hiddenInput.value=html;if(raiseOnChange&&hiddenInput.onchange)hiddenInput.onchange();}}
me.syncHidden=function(name)
{me.refreshHidden();setTimeout(name+".syncHidden('"+name+"')",500);}}
function G_Tree(){this.lastSelected=false;this.lastSelectedClassName='treeNode';var me=this;this.changeSign=function(element,newSign){var spans=element.cells[0].childNodes;for(var r=0;r<spans.length;r++){if(spans[r].nodeName==='SPAN'){if(spans[r].getAttribute('name')===newSign){spans[r].style.display='';}else{spans[r].style.display='none';}}}};this.getRowOf=function(element){while(element.nodeName!='BODY'){if(element.getAttribute('name')){if(element.getAttribute('name').substr(0,9)==='treeNode['){var regexp=/^treeNode\[[^\]]+\]\[([^\]]+)\]$/;result=regexp.exec(element.getAttribute('name'));if(!(result&&result.length>=2))return false;return element.parentNode;}}
element=element.parentNode;}
return false;};this.contract=function(element){if(!(element=this.getRowOf(element)))return;var row=element.rowIndex;if((row+1)>=element.parentNode.rows.length)return;element.parentNode.rows[row+1].style.display='none';this.changeSign(element,'plus');};this.expand=function(element){if(!(element=this.getRowOf(element)))return;var row=element.rowIndex;if((row+1)>=element.parentNode.rows.length)return;element.parentNode.rows[row+1].style.display='';this.changeSign(element,'minus');};this.select=function(element){if(!(element=this.getRowOf(element)))return;if(me.lastSelected){if(me.lastSelected.cells[1])me.lastSelected.cells[1].className=me.lastSelectedClassName;}
me.lastSelected=element;me.lastSelectedClassName=me.lastSelected.cells[1].className;me.lastSelected.cells[1].className="treeNodeSelected";};this.refresh=function(div,server){div.innerHTML=ajax_function(server,'','');};};var tree=new G_Tree();
if(!Object.prototype.toJSONString){Array.prototype.toJSONString=function(){var a=['['],b,i,l=this.length,v;function p(s){if(b){a.push(',');}
a.push(s);b=true;}
for(i=0;i<l;i+=1){v=this[i];switch(typeof v){case'undefined':case'function':case'unknown':break;case'object':if(v){if(typeof v.toJSONString==='function'){p(v.toJSONString());}}else{p("null");}
break;default:p(v.toJSONString());}}
a.push(']');return a.join('');};Boolean.prototype.toJSONString=function(){return String(this);};Date.prototype.toJSONString=function(){function f(n){return n<10?'0'+n:n;}
return'"'+this.getFullYear()+'-'+
f(this.getMonth()+1)+'-'+
f(this.getDate())+'T'+
f(this.getHours())+':'+
f(this.getMinutes())+':'+
f(this.getSeconds())+'"';};Number.prototype.toJSONString=function(){return isFinite(this)?String(this):"null";};Object.prototype.toJSONString=function(){var a=['{'],b,k,v;function p(s){if(b){a.push(',');}
a.push(k.toJSONString(),':',s);b=true;}
for(k in this){if(this.hasOwnProperty(k)){v=this[k];switch(typeof v){case'undefined':case'function':case'unknown':break;case'object':if(v){if(typeof v.toJSONString==='function'){p(v.toJSONString());}}else{p("null");}
break;default:p(v.toJSONString());}}}
a.push('}');return a.join('');};(function(s){var m={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'};s.parseJSON=function(filter){try{if(/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(this)){var j=eval('('+this+')');if(typeof filter==='function'){function walk(k,v){if(v&&typeof v==='object'){for(var i in v){if(v.hasOwnProperty(i)){v[i]=walk(i,v[i]);}}}
return filter(k,v);}
j=walk('',j);}
return j;}}catch(e){}
throw new SyntaxError("parseJSON");};s.toJSONString=function(){if(/["\\\x00-\x1f]/.test(this)){return'"'+this.replace(/([\x00-\x1f\\"])/g,function(a,b){var c=m[b];if(c){return c;}
c=b.charCodeAt();return'\\u00'+
Math.floor(c/16).toString(16)+
(c%16).toString(16);})+'"';}
return'"'+this+'"';};})(String.prototype);}
function G_Form(element,id)
{var me=this;this.info={name:'G_Form',version:'1.0'};this.formula='';this.element=element;if(!element)return;this.id=id;this.aElements=[];this.ajaxServer='';this.getElementIdByName=function(name){if(name=='')return-1;var j;for(j=0;j<me.aElements.length;j++){if(me.aElements[j].name===name)return j;}
return-1;};this.getElementByName=function(name){var i=me.getElementIdByName(name);if(i>=0)return me.aElements[i];else return null;};this.hideGroup=function(group,parentLevel){if(typeof(parentLevel)==='undefined')parentLevel=1;for(var r=0;r<me.aElements.length;r++){if((typeof(me.aElements[r].group)!=='undefined')&&(me.aElements[r].group==group))
me.aElements[r].hide(parentLevel);}};this.showGroup=function(group,parentLevel){if(typeof(parentLevel)==='undefined')parentLevel=1;for(var r=0;r<me.aElements.length;r++){if((typeof(me.aElements[r].group)!=='undefined')&&(me.aElements[r].group==group))
me.aElements[r].show(parentLevel);}};this.verifyRequiredFields=function(){var valid=true;for(var i=0;i<me.aElements.length;i++){var verifiedField=((!me.aElements[i].required)||(me.aElements[i].required&&(me.aElements[i].value()!=='')));valid=valid&&verifiedField;if(!verifiedField){me.aElements[i].highLight();}}
return valid;};}
function G_Field(form,element,name)
{var me=this;this.form=form;this.element=element;this.name=name;this.dependentFields=[];this.dependentOf=[];this.hide=function(parentLevel){if(typeof(parentLevel)==='undefined')parentLevel=1;var parent=me.element;for(var r=0;r<parentLevel;r++)
parent=parent.parentNode;parent.style.display='none';};this.show=function(parentLevel){if(typeof(parentLevel)==='undefined')parentLevel=1;var parent=me.element;for(var r=0;r<parentLevel;r++)
parent=parent.parentNode;parent.style.display='';};this.setDependentFields=function(dependentFields){var i;if(dependentFields.indexOf(',')>-1){dependentFields=dependentFields.split(',');}
else{dependentFields=dependentFields.split('|');}
for(i=0;i<dependentFields.length;i++){if(me.form.getElementIdByName(dependentFields[i])>=0){me.dependentFields[i]=me.form.getElementByName(dependentFields[i]);me.dependentFields[i].addDependencie(me);}}};this.addDependencie=function(field){var exists=false;for(i=0;i<me.dependentOf.length;i++)
if(me.dependentOf[i]===field)exists=true;if(!exists)me.dependentOf[i]=field;};this.updateDepententFields=function(event){var tempValue;if(me.dependentFields.length===0)return true;var fields=[],Fields=[],i,grid='',row=0;for(i in me.dependentFields){if(me.dependentFields[i].dependentOf){for(var j=0;j<me.dependentFields[i].dependentOf.length;j++){var oAux=me.dependentFields[i].dependentOf[j];if(oAux.name.indexOf('][')>-1){var aAux=oAux.name.split('][');grid=aAux[0];row=aAux[1];fieldName=aAux[2];if(Fields.length>0){aux=Fields;aux.push('?');if(aux.join('*').indexOf(fieldName+'*')==-1){Fields.push(fieldName);eval("var oAux2 = {"+fieldName+":'"+oAux.value()+"'}");fields=fields.concat(oAux2);}}else{Fields.push(fieldName);eval("var oAux2 = {"+fieldName+":'"+oAux.value()+"'}");fields=fields.concat(oAux2);}}
else{aux=Fields;aux.push('?');oAux=me.dependentFields[i].dependentOf[0];if(Fields.length>0){if(aux.join('*').indexOf(oAux.name+'*')==-1){Fields.push(oAux.name);fields=fields.concat(me.dependentFields[i].dependentOf);}}else{Fields.push(oAux.name);fields=fields.concat(me.dependentFields[i].dependentOf);}}}}}
var callServer;callServer=new leimnud.module.rpc.xmlhttp({url:me.form.ajaxServer,async:false,method:"POST",args:"function=reloadField&"+'form='+encodeURIComponent(me.form.id)+'&fields='+encodeURIComponent(fields.toJSONString())+(grid!=''?'&grid='+grid:'')+(row>0?'&row='+row:'')});callServer.make();var response=callServer.xmlhttp.responseText;if(response.substr(0,1)==='['){var newcont;eval('newcont='+response+';');if(grid==''){for(var i=0;i<newcont.length;i++){var j=me.form.getElementIdByName(newcont[i].name);me.form.aElements[j].setValue(newcont[i].value);me.form.aElements[j].setContent(newcont[i].content);me.form.aElements[j].updateDepententFields();}}
else{for(var i=0;i<newcont.length;i++){var oAux=me.form.getElementByName(grid);if(oAux){var oAux2=oAux.getElementByName(row,newcont[i].name);if(oAux2){oAux2.setValue(newcont[i].value);oAux2.setContent(newcont[i].content);oAux2.updateDepententFields();}}}}}else{alert('Invalid response: '+response);}
return true;};this.setValue=function(newValue){me.element.value=newValue;};this.setContent=function(newContent){};this.setAttributes=function(attributes){for(var a in attributes){if(a=='formula'&&attributes[a]){sumaformu(this.element,attributes[a],attributes['mask']);}
switch(typeof(attributes[a])){case'string':case'int':case'boolean':if(a!='strTo'){switch(true){case typeof(me[a])==='undefined':case typeof(me[a])==='object':case typeof(me[a])==='function':case a==='isObject':case a==='isArray':break;default:me[a]=attributes[a];}}
else{me[a]=attributes[a];}}}};this.value=function(){return me.element.value;};this.toJSONString=function(){return'{'+me.name+':'+me.element.value.toJSONString()+'}';};this.highLight=function(){try{G.highLight(me.element);if(G.autoFirstField){me.element.focus();G.autoFirstField=false;setTimeout("G.autoFirstField=true;",1000);}}catch(e){}};}
function G_DropDown(form,element,name)
{var me=this;this.parent=G_Field;this.parent(form,element,name);this.setContent=function(content){var dd=me.element;var browser=getBrowserClient();if((browser.name=='msie')||((browser.name=='firefox')&&(browser.version<12))){while(dd.options.length>1)dd.remove(0);}else{for(var key in dd.options){dd.options[key]=null;}}
for(var o=0;o<content.options.length;o++){var optn=$dce("OPTION");optn.text=content.options[o].value;optn.value=content.options[o].key;dd.options[o]=optn;}};if(!element)return;leimnud.event.add(this.element,'change',this.updateDepententFields);}
G_DropDown.prototype=new G_Field();function G_Text(form,element,name)
{var me=this;this.mType="text";this.parent=G_Field;this.browser={};this.comma_separator=".";this.checkBrowser=function(){var nVer=navigator.appVersion;var nAgt=navigator.userAgent;var browserName=navigator.appName;var fullVersion=''+parseFloat(navigator.appVersion);var majorVersion=parseInt(navigator.appVersion,10);var nameOffset,verOffset,ix;if((verOffset=nAgt.indexOf("Opera"))!=-1){browserName="Opera";fullVersion=nAgt.substring(verOffset+6);if((verOffset=nAgt.indexOf("Version"))!=-1)
fullVersion=nAgt.substring(verOffset+8);}
else if((verOffset=nAgt.indexOf("MSIE"))!=-1){browserName="Microsoft Internet Explorer";fullVersion=nAgt.substring(verOffset+5);}
else if((verOffset=nAgt.indexOf("Chrome"))!=-1){browserName="Chrome";fullVersion=nAgt.substring(verOffset+7);}
else if((verOffset=nAgt.indexOf("Safari"))!=-1){browserName="Safari";fullVersion=nAgt.substring(verOffset+7);if((verOffset=nAgt.indexOf("Version"))!=-1)
fullVersion=nAgt.substring(verOffset+8);}
else if((verOffset=nAgt.indexOf("Firefox"))!=-1){browserName="Firefox";fullVersion=nAgt.substring(verOffset+8);}
else if((nameOffset=nAgt.lastIndexOf(' ')+1)<(verOffset=nAgt.lastIndexOf('/')))
{browserName=nAgt.substring(nameOffset,verOffset);fullVersion=nAgt.substring(verOffset+1);if(browserName.toLowerCase()==browserName.toUpperCase()){browserName=navigator.appName;}}
if((ix=fullVersion.indexOf(";"))!=-1)
fullVersion=fullVersion.substring(0,ix);if((ix=fullVersion.indexOf(" "))!=-1)
fullVersion=fullVersion.substring(0,ix);majorVersion=parseInt(''+fullVersion,10);if(isNaN(majorVersion)){fullVersion=''+parseFloat(navigator.appVersion);majorVersion=parseInt(navigator.appVersion,10);}
this.browser={name:browserName,fullVersion:fullVersion,majorVersion:majorVersion,userAgent:navigator.userAgent};};this.parent(form,element,name);if(element){this.prev=element.value;}
this.validate='Any';this.mask='';this.required=false;this.formula='';this.key_Change=false;var doubleChange=false;function IsUnsignedInteger(YourNumber){var Template=/^d+$/;return(Template.test(YourNumber))?1:0;}
function replaceAll(text,busca,reemplaza){while(text.toString().indexOf(busca)!=-1){text=text.toString().replace(busca,reemplaza);}
return text;}
function isNumberMask(mask){for(var key in mask){if(mask[key]!='#'&&mask[key]!=','&&mask[key]!='.'&&typeof(mask[key])=='string'){return false;}}
return true;}
this.setContent=function(content){me.element.value='';if(content.options){if(content.options[0]){me.element.value=content.options[0].value;}}};this.putFormatNumber=function(evt){};this.execFormula=function(event){if(me.formula!=''){leimnud.event.add(getField('faa'),'keypress',function(){alert(getField('faa').value);});}
return false;};this.value=function()
{return me.element.value;};this.getCursorPos=function(){var textElement=me.element;if(!document.selection)return textElement.selectionStart;var sOldText=textElement.value;var objRange=document.selection.createRange();var sOldRange=objRange.text;var sWeirdString='#%~';objRange.text=sOldRange+sWeirdString;objRange.moveStart('character',(0-sOldRange.length-sWeirdString.length));var sNewText=textElement.value;objRange.text=sOldRange;for(i=0;i<=sNewText.length;i++){var sTemp=sNewText.substring(i,i+sWeirdString.length);if(sTemp==sWeirdString){var cursorPos=(i-sOldRange.length);return cursorPos;}}};this.setSelectionRange=function(selectionStart,selectionEnd){var input=me.element;if(input.createTextRange){var range=input.createTextRange();range.collapse(true);range.moveEnd('character',selectionEnd);range.moveStart('character',selectionStart);range.select();}
else if(input.setSelectionRange){input.focus();input.setSelectionRange(selectionStart,selectionEnd);}};this.getCursorPosition=function(){if(navigator.appName=='Microsoft Internet Explorer'){var field=me.element;if(document.selection){field.focus();var oSel=document.selection.createRange();oSel.moveStart('character',-field.value.length);field.selectionEnd=oSel.text.length;oSel.setEndPoint('EndToStart',document.selection.createRange());field.selectionStart=oSel.text.length;}
return{selectionStart:field.selectionStart,selectionEnd:field.selectionEnd};}else{if(document.selection){var textElement=me.element;var sOldText=textElement.value;var objRange=document.selection.createRange();var sOldRange=objRange.text;var sWeirdString='#%~';objRange.text=sOldRange+sWeirdString;objRange.moveStart('character',(0-sOldRange.length-sWeirdString.length));var sNewText=textElement.value;objRange.text=sOldRange;for(i=0;i<=sNewText.length;i++){var sTemp=sNewText.substring(i,i+sWeirdString.length);if(sTemp==sWeirdString){var cursorPos=(i-sOldRange.length);return{selectionStart:cursorPos,selectionEnd:cursorPos+sOldRange.length};}}}else{var sel={selectionStart:0,selectionEnd:0};sel.selectionStart=me.element.selectionStart;sel.selectionEnd=me.element.selectionEnd;return sel;}}};this.removeMask=function(){value=me.element.value;cursor=me.getCursorPosition();chars=value.split('');newValue='';newCont=0;newCursor=0;for(c=0;c<chars.length;c++){switch(chars[c]){case'0':case'1':case'2':case'3':case'4':case'5':case'6':case'7':case'8':case'9':case me.comma_separator:newValue+=chars[c];newCont++;if(c+1==cursor.selectionStart){newCursor=newCont;}
break;case'-':if(me.validate=='Real'||me.validate=='Int'){newValue+=chars[c];newCont++;if(c+1==cursor.selectionStart){newCursor=newCont;}}
break;}}
if(cursor.selectionStart!=cursor.selectionEnd){return{result:newValue,cursor:cursor};}
else{return{result:newValue,cursor:{selectionStart:newCursor,selectionEnd:newCursor}};}};this.replaceMask=function(value,cursor,mask,type,comma){switch(type){case'currency':case'percentage':dir='reverse';break;default:if(me.mType=='text'&&me.validate=='Real'){dir='reverse';}else{dir='forward';}
break;}
return G.ApplyMask(value,mask,cursor,dir,comma);};this.replaceMasks=function(newValue,newCursor){masks=me.mask;aMasks=masks.split(';');aResults=[];for(m=0;m<aMasks.length;m++){mask=aMasks[m];type=me.mType;comma_sep=me.comma_separator;comma_sep=(comma_sep=='')?'.':comma_sep;aResults.push(me.replaceMask(newValue,newCursor,mask,type,comma_sep));break;}
minIndex=0;minValue=aResults[0].result;if(aResults.length>1){for(i=1;i<aResults.length;i++){if(aResults[i].result<minValue){minValue=aResults[i].result;minIndex=i;}}}
return aResults[minIndex];};this.getCleanMask=function(){aMask=me.mask.split('');maskOut='';for(i=0;i<aMask.length;i++){if(me.mType=='currency'||me.mType=='percentage'||(me.mType=='text'&&me.validate=='Real')){switch(aMask[i]){case'0':case'#':maskOut+=aMask[i];break;case me.comma_separator:maskOut+='_';break;}}
else{switch(aMask[i]){case'0':case'#':case'd':case'm':case'y':case'Y':maskOut+=aMask[i];break;}}}
return maskOut;}
this.applyMask=function(keyCode){if(me.mask!=''){dataWOMask=me.removeMask();currentValue=dataWOMask.result;currentSel=dataWOMask.cursor;cursorStart=currentSel.selectionStart;cursorEnd=currentSel.selectionEnd;action='mask';swPeriod=false;switch(keyCode){case 0:action='none';break;case 8:newValue=currentValue.substring(0,cursorStart-1);newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart-1;break;case 46:newValue=currentValue.substring(0,cursorStart);newValue+=currentValue.substring(cursorEnd+1,currentValue.length);newCursor=cursorStart;break;case 256:case 44:swPeriod=true;newValue=currentValue.substring(0,cursorStart);if(keyCode==256)
newValue+='.';else
newValue+=',';newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart+1;break;case 35:case 36:case 37:case 38:case 39:case 40:newValue=currentValue;switch(keyCode){case 36:newCursor=0;break;case 35:newCursor=currentValue.length;break;case 37:newCursor=cursorStart-1;break;case 39:newCursor=cursorStart+1;break;}
action='move';break;case 45:if(me.mType=='currency'){newValue=currentValue.substring(0,currentValue.length).split('');for(var numI=0;newValue.length>numI;numI++){var campVal=newValue[numI];if((typeof(campVal)==='number'||typeof(campVal)==='string')&&(campVal!=='')&&(!isNaN(campVal))){newValue=currentValue.substring(0,numI-1);newValue+='-'+currentValue.substring(numI);numI=newValue.length+1;newCursor=cursorStart+1;}else{if(campVal=='-'){newValue=currentValue.substring(0,numI-1);newValue+=currentValue.substring(numI+1);newCursor=cursorStart-1;numI=newValue.length+1;}}}
if(newValue.join){newValue=newValue.join('');}}
break;default:newKey=String.fromCharCode(keyCode);newValue=currentValue.substring(0,cursorStart);newValue+=newKey;newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart+1;break;}
if(newCursor<0)newCursor=0;if(keyCode!=8&&keyCode!=46&&keyCode!=35&&keyCode!=36&&keyCode!=37&&keyCode!=39){testData=dataWOMask.result;tamData=testData.length;cleanMask=me.getCleanMask();tamMask=cleanMask.length;sw=false;if(testData.indexOf(me.comma_separator)==-1){aux=cleanMask.split('_');tamMask=aux[0].length;sw=true;}
if(tamData>=tamMask){var minusExi;for(var numI=0;newValue.length>numI;numI++){var campVal=newValue[numI];if((typeof(campVal)==='number'||typeof(campVal)==='string')&&(campVal!=='')&&(!isNaN(campVal))){minusExi=false;}else{if(campVal=='-'){minusExi=true;numI=newValue.length+1;}}}
if(!(keyCode==45||(minusExi&&tamMask>=tamData))){if(sw&&!swPeriod&&testData.indexOf(me.comma_separator)==-1){action='none';}
if(!sw)action='none';}}}
switch(action){case'mask':case'move':dataNewMask=me.replaceMasks(newValue,newCursor);me.element.value=dataNewMask.result;me.setSelectionRange(dataNewMask.cursor,dataNewMask.cursor);break;}}
else{currentValue=me.element.value;currentSel=me.getCursorPosition();cursorStart=currentSel.selectionStart;cursorEnd=currentSel.selectionEnd;switch(keyCode){case 8:newValue=currentValue.substring(0,cursorStart-1);newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart-1;break;case 46:case 45:newValue=currentValue.substring(0,cursorStart);newValue+=currentValue.substring(cursorEnd+1,currentValue.length);newCursor=cursorStart;break;case 256:newValue=currentValue.substring(0,cursorStart);newValue+='.';newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart+1;break;case 35:case 36:case 37:case 38:case 39:case 40:newValue=currentValue;switch(keyCode){case 36:newCursor=0;break;case 35:newCursor=currentValue.length;break;case 37:newCursor=cursorStart-1;break;case 39:newCursor=cursorStart+1;break;}
break;default:newKey=String.fromCharCode(keyCode);newValue=currentValue.substring(0,cursorStart);newValue+=newKey;newValue+=currentValue.substring(cursorEnd,currentValue.length);newCursor=cursorStart+1;break;}
if(newCursor<0)newCursor=0;me.element.value=newValue;me.setSelectionRange(newCursor,newCursor);}};this.sendOnChange=function(){if(me.element.fireEvent){me.element.fireEvent("onchange");}else{var evObj=document.createEvent('HTMLEvents');evObj.initEvent('change',true,true);me.element.dispatchEvent(evObj);}};this.handleKeyDown=function(event){if(me.element.readOnly){return true;}
if(me.validate=='Any'&&me.mask=='')return true;var pressKey=(window.event)?window.event.keyCode:event.which;switch(pressKey){case 8:case 46:case 35:case 36:case 37:case 38:case 39:case 40:if(me.validate=='NodeName'&&((pressKey==8)||(pressKey==46))){return true;}
me.applyMask(pressKey);if((pressKey==8||pressKey==46)&&(me.validate!='Login'&&me.validate!='NodeName'))me.sendOnChange();me.checkBrowser();if(me.browser.name=='Chrome'||me.browser.name=='Safari'){event.returnValue=false;}
else{return false;}
break;case 9:return true;break;default:if(me.mType=='date'||me.mType=='currency'||me.mType=='percentage'||me.validate=='Real'||me.validate=='Int'){if((48<=pressKey&&pressKey<=57)||(pressKey==109||pressKey==190||pressKey==188||pressKey==189)||(96<=pressKey&&pressKey<=111)){return true;}
else{return false;}}
break;}
return true;};this.handleKeyPress=function(event){if(me.element.readOnly){return true;}
if((me.mType!='currency'&&me.mType!='percentage'&&me.mType!='date')&&(me.element.value.length>me.element.maxLength-1)){return true;}
if(me.validate=='Any'&&me.mask=='')return true;var keyCode=(window.event)?window.event.keyCode:event.which;if(navigator.userAgent.indexOf('MSIE')!=-1){if(keyCode==0)return true;}
switch(keyCode){case 9:case 13:return true;break;}
var swShiftKey=((me.mType=='currency')||(me.mType=='percentage')||(me.validate=='Real')||(me.validate=='Int'))?false:true;if(window.event){if(window.event.altKey){return true;}
if(window.event.ctrlKey){return true;}}else{if(event.altKey){return true;}
if(event.ctrlKey){return true;}}
me.checkBrowser();if((me.browser.name=='Firefox')&&(keyCode==8)&&(me.validate!='NodeName')){if(me.browser.name=='Chrome'||me.browser.name=='Safari'){event.returnValue=false;}
else{return false;}}
else{var pressKey=(window.event)?window.event.keyCode:event.which;if(me.mType=='date')me.validate='Int';keyValid=true;updateOnChange=true;switch(me.validate){case'Any':keyValid=true;break;case'Int':patron=/[0-9\-]/;key=String.fromCharCode(pressKey);keyValid=patron.test(key);break;case'Real':if(typeof me.comma_separator!='undefined'){patron=/[0-9\-]/;}
else{patron=/[0-9,\.]/;}
key=String.fromCharCode(pressKey);keyValid=patron.test(key);keyValid=keyValid||(pressKey==45);if(typeof me.comma_separator!='undefined'){if(me.comma_separator=='.'){if(me.element.value.indexOf('.')==-1){keyValid=keyValid||(pressKey==46);}}
else{if(me.element.value.indexOf(',')==-1){keyValid=keyValid||(pressKey==44);}}}
break;case'Alpha':patron=/[a-zA-Z]/;key=String.fromCharCode(pressKey);keyValid=patron.test(key);break;case'AlphaNum':patron=/[a-zA-Z0-9\sÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂºÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¤ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¶ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¼ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¹Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¹ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ]/;key=String.fromCharCode(pressKey);keyValid=patron.test(key);break;case'NodeName':case'Login':updateOnChange=false;if(me.getCursorPos()==0){if((pressKey>=48)&&(pressKey<=57)){keyValid=false;break;}}
if((keyCode==8)&&(me.validate=='NodeName')){keyValid=true;}else{var k=new leimnud.module.validator({valid:['Login'],key:(window.event)?window.event:event,lang:(typeof(me.language)!=='undefined')?me.language:"en"});keyValid=k.result();}
break;default:var k=new leimnud.module.validator({valid:[me.validate],key:(window.event)?window.event:event,lang:(typeof(me.language)!=='undefined')?me.language:"en"});keyValid=k.result();break;}
if(keyValid){if((me.validate=="Login"||me.validate=="NodeName")&&me.mask=="")return true;if(pressKey==46){me.applyMask(256);}
else{me.applyMask(pressKey);}
if(updateOnChange)me.sendOnChange();}
if(me.browser.name=='Firefox'){if(keyCode==0)return true;}
if(me.browser.name=='Chrome'||me.browser.name=='Safari'){event.returnValue=false;}
else{return false;}}};if(this.element){this.element.onblur=function(event)
{var evt=event||window.event;var keyPressed=evt.which||evt.keyCode;if((me.mask!='')&&((me.mType=='currency')||(me.mType=='percentage')||((me.validate=="Real")&&(me.mType=='text')))&&(me.mask.indexOf('-')==-1)&&(me.element.value!='')){masks=me.mask;aMasks=masks.split(';');for(m=0;m<aMasks.length;m++){var separatorField=",";if(typeof(me.comma_separator)!='undefined'){separatorField=me.comma_separator;}else{txtRealMask=aMasks[m].split('');p=txtRealMask.length-1;for(;p>=0;p--){if(txtRealMask[p]!='#'&&txtRealMask[p]!='%'&&txtRealMask[p]!=' '){separatorField=txtRealMask[p];break;}}}
var partsMaskSep=aMasks[m].split(separatorField);if(partsMaskSep.length==2){var countDecimal=0;txtRealMask=aMasks[m].split('');p=txtRealMask.length-1;for(;p>=0;p--){if(txtRealMask[p]=='#'){countDecimal++;}
if(txtRealMask[p]==separatorField){break;}}
var decimalString='';var pluginAfter='';var pluginDecimal='';var numberSet=me.element.value.split(separatorField);if(typeof(numberSet[1])=='undefined'){var decimalSet='';var newInt='';var flagAfter=true;var newPluginDecimal='';var decimalCade=numberSet[0].split('');for(p=0;p<decimalCade.length;p++){if((!isNaN(parseFloat(decimalCade[p]))&&isFinite(decimalCade[p]))||(decimalCade[p]==',')||(decimalCade[p]=='.')){newInt+=decimalCade[p];flagAfter=false;}else{if(flagAfter){pluginAfter+=decimalCade[p];}else{newPluginDecimal+=decimalCade[p];}}}
numberSet[0]=newInt;numberSet[1]=newPluginDecimal;}
var decimalSet=numberSet[1];var decimalCade=decimalSet.split('');var countDecimalNow=0;for(p=0;p<decimalCade.length;p++){if(!isNaN(parseFloat(decimalCade[p]))&&isFinite(decimalCade[p])){countDecimalNow++;decimalString+=decimalCade[p];}else{pluginDecimal+=decimalCade[p];}}
if(countDecimalNow<countDecimal){for(;countDecimalNow<countDecimal;countDecimalNow++){decimalString+='0';}
me.element.value=pluginAfter+numberSet[0]+separatorField+decimalString+pluginDecimal;}}
break;}}
if(this.validate=="Email")
{var pat=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;if(!pat.test(this.element.value))
{if(this.element.value==""){this.element.className="module_app_input___gray";return;}
else{this.element.className=this.element.className.split(" ")[0]+" FormFieldInvalid";}}
else
{this.element.className=this.element.className.split(" ")[0]+" FormFieldValid";}}
if(this.strTo){switch(this.strTo){case'UPPER':this.element.value=this.element.value.toUpperCase();break;case'LOWER':this.element.value=this.element.value.toLowerCase();break;}}}.extend(this);}
if(!element)return;if(!window.event){this.element.onkeydown=this.handleKeyDown;this.element.onkeypress=this.handleKeyPress;this.element.onchange=this.updateDepententFields;}else{leimnud.event.add(this.element,'keydown',this.handleKeyDown);leimnud.event.add(this.element,'keypress',this.handleKeyPress);leimnud.event.add(this.element,'change',this.updateDepententFields);}};G_Text.prototype=new G_Field();function G_Percentage(form,element,name)
{var me=this;this.parent=G_Text;this.parent(form,element,name);this.mType='percentage';this.mask='###.##';this.comma_separator=".";}
G_Percentage.prototype=new G_Field();function G_Currency(form,element,name)
{var me=this;this.parent=G_Text;this.parent(form,element,name);this.mType='currency';this.mask='_###,###,###,###,###;###,###,###,###,###.00';this.comma_separator=".";}
G_Currency.prototype=new G_Field();function G_TextArea(form,element,name)
{var me=this;this.parent=G_Text;this.parent(form,element,name);this.validate='Any';this.mask='';}
G_TextArea.prototype=new G_Field();function G_Date(form,element,name)
{var me=this;this.parent=G_Text;this.parent(form,element,name);this.mType='date';this.mask='dd-mm-yyyy';}
G_Date.prototype=new G_Field();function G()
{var reserved=['_',';','#','.','0','d','m','y','-'];function invertir(num)
{var num0='';num0=num;num="";for(r=num0.length-1;r>=0;r--)num+=num0.substr(r,1);return num;}
function __toMask(num,mask,cursor)
{var inv=false;if(mask.substr(0,1)==='_'){mask=mask.substr(1);inv=true;}
var re;if(inv){mask=invertir(mask);num=invertir(num);}
var minAdd=-1;var minLoss=-1;var newCursorPosition=cursor;var betterOut="";for(var r0=0;r0<mask.length;r0++){var out="";var j=0;var loss=0;var add=0;var cursorPosition=cursor;var i=-1;var dayPosition=0;var mounthPosition=0;var dayAnalized='';var mounthAnalized='';var blocks={};for(var r=0;r<r0;r++){var e=false;var m=mask.substr(r,1);__parseMask();}
i=0;for(r=r0;r<mask.length;r++){j++;if(j>200)break;e=num.substr(i,1);e=(e==='')?false:e;m=mask.substr(r,1);__parseMask();}
var io=num.length-i;io=(io<0)?0:io;loss+=io;loss=loss+add/1000;if(loss===0){betterOut=out;minLoss=0;newCursorPosition=cursorPosition;break;}
if((minLoss===-1)||(loss<minLoss)){minLoss=loss;betterOut=out;newCursorPosition=cursorPosition;}}
out=betterOut;if(inv){out=invertir(out);mask=invertir(mask);}
return{'result':out,'cursor':newCursorPosition,'value':minLoss,'mask':mask};function searchBlock(where,what)
{for(var r=0;r<where.length;r++){if(where[r].key===what)return where[r];}}
function __parseMask()
{var ok=true;switch(false){case m==='d':dayAnalized='';break;case m==='m':mounthAnalized='';break;default:}
if(e!==false){if(typeof(blocks[m])==='undefined')blocks[m]=e;else blocks[m]+=e;}
switch(m){case'0':if(e===false){out+='0';add++;break;}
case'y':case'#':if(e===false){out+='';break;}
if((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')||(e==='-')){out+=e;i++;}else{loss++;i++;r--;}
break;case'(':if(e===false){out+='';break;}
out+=m;if(i<cursor){cursorPosition++;}
break;case'd':if(e===false){out+='';break;}
if((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9'))ok=true;else ok=false;if(ok)dayAnalized=dayAnalized+e;if((ok)&&(parseInt(dayAnalized)>31))ok=false;if(ok){out+=e;i++;}else{loss++;i++;r--;}
break;case'm':if(e===false){out+='';break;}
if((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9'))ok=true;else ok=false;if(ok)mounthAnalized=mounthAnalized+e;if((ok)&&(parseInt(mounthAnalized)>12))ok=false;if(ok){out+=e;i++;}else{loss++;i++;r--;}
break;default:if(e===false){out+='';break;}
if(e===m){out+=e;i++;}else{out+=m;add++;if(i<cursor){cursorPosition++;};}}}}
function _getOnlyNumbers(num,_DEC){var _num='';_aNum=num.split('');for(var d=0;d<_aNum.length;d++){switch(_aNum[d]){case'-':case'0':case'1':case'2':case'3':case'4':case'5':case'6':case'7':case'8':case'9':case _DEC:_num=_num+_aNum[d];break;}}
return _num;}
function _getOnlyMask(mask,_DEC){var _mask='';_aMask=mask.split('');for(var d=0;d<_aMask.length;d++){switch(_aMask[d]){case'0':case'#':case'd':case'm':case'y':case'_':case _DEC:_mask+=_aMask[d];break;}}
return _mask;}
function _checkNumber(num,mask){var __DECIMAL_SEP='.';var aNum=_getOnlyNumbers(num,__DECIMAL_SEP);var _outM=aNum;var aMask=_getOnlyMask(mask,__DECIMAL_SEP);if(aMask.indexOf(__DECIMAL_SEP+'0')>0){eMask=aMask.replace(__DECIMAL_SEP,'');eNum=aNum.replace(__DECIMAL_SEP,'');if(eNum.length>eMask.length){_outM=aNum.substring(0,eMask.length+1);}}else{if(aMask.indexOf(__DECIMAL_SEP)>0){iMask=aMask.split(__DECIMAL_SEP);if(aNum.indexOf(__DECIMAL_SEP)>0){iNum=aNum.split(__DECIMAL_SEP);if(iNum[1].length>iMask[1].length){_outM=iNum[0]+__DECIMAL_SEP+iNum[1].substr(0,iMask[1].length);}else{if(iNum[0].length>iMask[0].length){_outM=iNum[0].substr(0,iMask[0].length)+__DECIMAL_SEP+iNum[1];}}}else{if(aNum.length>iMask[0].length){_outM=aNum.substr(0,iMask[0].length);}}}else{if(aNum.indexOf(__DECIMAL_SEP)>0){iNum=aNum.split(__DECIMAL_SEP);if(iNum[0].length>aMask.length){_outM=iNum[0].substr(0,aMask.length);}}else{if(aNum.length>aMask.length){_outM=aNum.substr(0,aMask.length);}}}}
return _outM;}
this.ApplyMask=function(num,mask,cursor,dir,comma_sep){myOut='';myCursor=cursor;if(num.length==0)return{result:'',cursor:0};switch(dir){case'forward':iMask=mask.split('');value=_getOnlyNumbers(num,'');iNum=value.split('');for(e=0;e<iMask.length&&iNum.length>0;e++){switch(iMask[e]){case'#':case'0':case'd':case'm':case'y':case'Y':if(iNum.length>0){key=iNum.shift();myOut+=key;}
break;default:myOut+=iMask[e];if(e<myCursor)myCursor++;break;}}
break;case'reverse':var __DECIMAL_SEP=comma_sep;var osize=num.length;num=_getOnlyNumbers(num,__DECIMAL_SEP);if(num.length==0)return{result:'',cursor:0};var iNum=invertir(num);var iMask=invertir(mask);if(iMask.indexOf('0'+__DECIMAL_SEP)>0){aMask=iMask;iNum=_getOnlyNumbers(iNum,'*');aNum=iNum;eMask=aMask.split('');eNum=aNum.split('');_cout='';for(e=0;e<eMask.length;e++){switch(eMask[e]){case'#':case'0':if(eNum.length>0){key=eNum.shift();_cout+=key;}
break;case'.':case',':if(eMask[e]!=__DECIMAL_SEP){if(eNum.length>0){_cout+=eMask[e];}}else{_cout+=eMask[e];}
break;default:_cout+=eMask[e];break;}}
myOut=_cout;}else{sw_d=false;aMask=iMask.split(__DECIMAL_SEP);aNum=iNum.split(__DECIMAL_SEP);if(aMask.length==1){dMask='';cMask=aMask[0];}else{dMask=aMask[0];cMask=aMask[1];}
if(aNum.length==1){dNum='';cNum=aNum[0];}else{sw_d=true;dNum=aNum[0];cNum=aNum[1];}
_dout='';pMask=dMask.split('');pNum=dNum.split('');for(p=0;p<pMask.length;p++){switch(pMask[p]){case'#':case'0':if(pNum.length>0){key=pNum.shift();_dout+=key;}
break;case',':case'.':if(pMask[p]!=__DECIMAL_SEP){if(pNum.length>0){_dout+=pMask[p];}}else{}
break;default:_dout+=pMask[p];break;}}
_cout='';sw_c=false;pMask=cMask.split('');pNum=cNum.split('');for(p=0;p<pMask.length;p++){switch(pMask[p]){case'#':case'0':case'd':case'm':case'y':if(pNum.length>0){key=pNum.shift();_cout+=key;sw_c=true;}
break;case',':case'.':if(pMask[p]!=__DECIMAL_SEP){if(pNum.length>0&&pNum[0]!='-'){_cout+=pMask[p];}}
break;default:if(pNum.length>0&&pNum[0]=='-'){key=pNum.shift();_cout+=key;}
_cout+=pMask[p];}}
if(pNum.length>0&&pNum[0]=='-'){key=pNum.shift();_cout+=key;}
if(sw_c&&sw_d){myOut=_dout+__DECIMAL_SEP+_cout;}else{myOut=_dout+_cout;}}
myOut=invertir(myOut);tmpCursor=0;aOut=myOut.split('');if(cursor==0){for(l=0;l<aOut.length;l++){switch(aOut[l]){case'0':case'1':case'2':case'3':case'4':case'5':case'6':case'7':case'8':case'9':case __DECIMAL_SEP:myCursor=l;l=aOut.length;break;}}}
else if(cursor==num.length){for(l=0;l<aOut.length;l++){switch(aOut[l]){case'0':case'1':case'2':case'3':case'4':case'5':case'6':case'7':case'8':case'9':case __DECIMAL_SEP:last=l;break;}}
myCursor=last+1;}
else{aNum=num.split('');offset=0;aNewNum=myOut.split('');for(a=0;a<cursor;a++){notFinded=false;while(aNum[a]!=aNewNum[a+offset]&&!notFinded){offset++;if(a+offset>aNewNum.length){offset=-1;notFinded=true;}}}
myCursor=cursor+offset;}
break;}
return{'result':myOut,'cursor':myCursor};};this.toMask=function(num,mask,cursor,direction){if(mask==='')return{'result':new String(num),'cursor':cursor};num=new String(num);var result=[];var subMasks=mask.split(';');for(var r=0;r<subMasks.length;r++){typedate=mask.indexOf("#");if((direction=='normal')&&(typedate=='0'))
result[r]=__toMask(num,subMasks[r],cursor);else
result[r]=_ApplyMask(num,subMasks[r],cursor,direction);}
var betterResult=0;for(r=1;r<subMasks.length;r++){if(result[r].value<result[betterResult].value)betterResult=r;}
return result[betterResult];};this.getValue=function(elem){return getNumericValue(elem.value(),((typeof elem.comma_separator!="undefined")?elem.comma_separator:""));};this.toMask2=function(num,mask,cursor)
{if(mask==='')return{'result':new String(num),'cursor':cursor};var subMasks=mask.split(';');var result=[];num=new String(num);for(var r=0;r<subMasks.length;r++){result[r]=__toMask(num,subMasks[r],cursor);}
var betterResult=0;for(r=1;r<subMasks.length;r++){if(result[r].value<result[betterResult].value)betterResult=r;}
return result[betterResult];};this.cleanMask=function(num,mask,cursor)
{mask=typeof(mask)==='undefined'?'':mask;if(mask==='')return{'result':new String(num),'cursor':cursor};var a,r,others=[];num=new String(num);if(typeof(cursor)==='undefined')cursor=0;a=num.substr(0,cursor);for(r=0;r<reserved.length;r++)mask=mask.split(reserved[r]).join('');while(mask.length>0){r=others.length;others[r]=mask.substr(0,1);mask=mask.split(others[r]).join('');num=num.split(others[r]).join('');cursor-=a.split(others[r]).length-1;}
return{'result':num,'cursor':cursor};};this.getId=function(element){var re=/(\[(\w+)\])+/;var res=re.exec(element.id);return res?res[2]:element.id;};this.getObject=function(element){var objId=G.getId(element);switch(element.tagName){case'FORM':return eval('form_'+objId);break;default:if(element.form){var formId=G.getId(element.form);return eval('form_'+objId+'.getElementByName("'+objId+'")');}}};this.blinked=[];this.blinkedt0=[];this.autoFirstField=true;this.pi=Math.atan(1)*4;this.highLight=function(element){var newdiv=$dce('div');newdiv.style.position="absolute";newdiv.style.display="inline";newdiv.style.height=element.clientHeight+2;newdiv.style.width=element.clientWidth+2;newdiv.style.background="#FF5555";element.style.backgroundColor='#FFCACA';element.parentNode.insertBefore(newdiv,element);G.doBlinkEfect(newdiv,1000);};this.setOpacity=function(e,o){e.style.filter='alpha';if(e.filters){e.filters['alpha'].opacity=o*100;}else{e.style.opacity=o;}};this.doBlinkEfect=function(div,T){var f=1/T;var j=G.blinked.length;G.blinked[j]=div;G.blinkedt0[j]=(new Date()).getTime();for(var i=1;i<=20;i++){setTimeout("G.setOpacity(G.blinked["+j+"],0.3-0.3*Math.cos(2*G.pi*((new Date()).getTime()-G.blinkedt0["+j+"])*"+f+"));",T/20*i);}
setTimeout("G.blinked["+j+"].parentNode.removeChild(G.blinked["+j+"]);G.blinked["+j+"]=null;",T/20*i);};var alertPanel;this.alert=function(html,title,width,height,autoSize,modal,showModalColor,runScripts)
{html='<div>'+html+'</div>';width=(width)?width:300;height=(height)?height:200;autoSize=(showModalColor===false)?false:true;modal=(modal===false)?false:true;showModalColor=(showModalColor===true)?true:false;var alertPanel=new leimnud.module.panel();alertPanel.options={size:{w:width,h:height},position:{center:true},title:title,theme:"processmaker",control:{close:true,roll:false,drag:true,resize:true},fx:{blinkToFront:true,opacity:true,drag:true,modal:modal}};if(showModalColor===false)
{alertPanel.styles.fx.opacityModal.Static='0';}
alertPanel.make();alertPanel.addContent(html);if(runScripts)
{var myScripts=alertPanel.elements.content.getElementsByTagName('SCRIPT');var sMyScripts=[];for(var rr=0;rr<myScripts.length;rr++)sMyScripts.push(myScripts[rr].innerHTML);for(var rr=0;rr<myScripts.length;rr++){try{if(sMyScripts[rr]!=='')
if(window.execScript)
window.execScript(sMyScripts[rr],'javascript');else
window.setTimeout(sMyScripts[rr],0);}catch(e){alert(e.description);}}}
var panelNonContentHeight=44;var panelNonContentWidth=28;try{if(autoSize)
{var newW=alertPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth;var newH=alertPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight;alertPanel.resize({w:((newW<width)?width:newW)});alertPanel.resize({h:((newH<height)?height:newH)});}}catch(e){alert(var_dump(e));}
delete newdiv;delete myScripts;alertPanel.command(alertPanel.loader.hide);};}
var G=new G();function G_Debugger()
{this.var_dump=function(obj)
{var o,dump;dump='';if(typeof(obj)=='object')
for(o in obj)
{dump+='<b>'+o+'</b>:'+obj[o]+"<br>\n";}
else
dump=obj;debugDiv=document.getElementById('debug');if(debugDiv)debugDiv.innerHTML=dump;return dump;};}
var oDebug=new G_Debugger();var datePickerPanel;function showDatePicker(ev,formId,idName,value,min,max){var coor=leimnud.dom.mouse(ev);var coorx=(coor.x-50);var coory=(coor.y-40);datePickerPanel=new leimnud.module.panel();datePickerPanel.options={size:{w:275,h:240},position:{x:coorx,y:coory},title:"Date Picker",theme:"panel",control:{close:true,drag:true},fx:{modal:true}};datePickerPanel.setStyle={containerWindow:{borderWidth:0}};datePickerPanel.make();datePickerPanel.idName=idName;datePickerPanel.formId=formId;var sUrl="/controls/calendar.php?v="+value+"&d="+value+"&min="+min+"&max="+max;var r=new leimnud.module.rpc.xmlhttp({url:sUrl});r.callback=leimnud.closure({Function:function(rpc){datePickerPanel.addContent(rpc.xmlhttp.responseText);},args:r});r.make();}
function moveDatePicker(n_datetime){var dtmin_value=document.getElementById('dtmin_value');var dtmax_value=document.getElementById('dtmax_value');var sUrl="/controls/calendar.php?d="+n_datetime+'&min='+dtmin_value.value+'&max='+dtmax_value.value;var r=new leimnud.module.rpc.xmlhttp({url:sUrl});r.callback=leimnud.closure({Function:function(rpc){datePickerPanel.clearContent();datePickerPanel.addContent(rpc.xmlhttp.responseText);},args:r});r.make();}
function selectDate(day){var obj=document.getElementById('span['+datePickerPanel.formId+']['+datePickerPanel.idName+']');getField(datePickerPanel.idName,datePickerPanel.formId).value=day;obj.innerHTML=day;datePickerPanel.remove();}
function set_datetime(n_datetime,b_close){moveDatePicker(n_datetime);}
function getRow(name){try{var element=null;if(typeof(name)==='string'){element=getField(name);if(element==null){aElements=document.getElementsByName('form['+name+'][]');if(aElements.length==0)
aElements=document.getElementsByName('form['+name+']');if(aElements.length){element=aElements[aElements.length-1];}else
element=null;}}
if(element!=null){while(element.tagName!=='TR'){element=element.parentNode;}
return element;}else{return null;}}catch(e){alert(e);}}
var getRowById=getRow;function hideRow(element){var row=getRow(element);if(row)row.style.display='none';removeRequiredById(element);delete row;}
var hideRowById=hideRow;function showRow(element){var row=getRow(element);requiredFields=[];sRequiredFields=document.getElementById('DynaformRequiredFields').value.replace(/%27/gi,'"');fields=new String(sRequiredFields);fields=stripslashes(fields);requiredFieldsList=eval(fields);for(i=0;i<requiredFieldsList.length;i++){requiredFields[i]=requiredFieldsList[i].name;}
if(requiredFields.inArray(element)){enableRequiredById(element);}
if(row)row.style.display='';delete row;}
var showRowById=showRow;function hideShowControl(element,name){var control;if(element){control=element.parentNode.getElementsByTagName("div")[0];control.style.display=control.style.display==='none'?'':'none';if(control.style.display==='none')getField(name).value='';delete control;}}
function contractSubtitle(subTitle){subTitle=getRow(subTitle);var c=subTitle.cells[0].className;var a=subTitle.rowIndex;var t=subTitle.parentNode;for(var i=a+1,m=t.rows.length;i<m;i++){if(t.rows[i].cells.length==1)break;t.rows[i].style.display='none';var aAux=getControlsInTheRow(t.rows[i]);for(var j=0;j<aAux.length;j++){removeRequiredById(aAux[j]);}}}
function expandSubtitle(subTitle){subTitle=getRow(subTitle);var c=subTitle.cells[0].className;var a=subTitle.rowIndex;var t=subTitle.parentNode;for(var i=a+1,m=t.rows.length;i<m;i++){if(t.rows[i].cells.length==1){break;}
t.rows[i].style.display='';var aAux=getControlsInTheRow(t.rows[i]);for(var j=0;j<aAux.length;j++){enableRequiredById(aAux[j]);}}}
function contractExpandSubtitle(subTitleName){subTitle=getRow(subTitleName);var c=subTitle.cells[0].className;var a=subTitle.rowIndex;var t=subTitle.parentNode;var contracted=false;for(var i=a+1,m=t.rows.length;i<m;i++){if(t.rows[i].cells.length==1)break;if(t.rows[i].style.display==='none'){contracted=true;}}
if(contracted)expandSubtitle(subTitleName);else contractSubtitle(subTitleName);}
var getControlsInTheRow=function(oRow){var aAux1=[];if(oRow.cells){var i;var j;var sFieldName;for(i=0;i<oRow.cells.length;i++){var aAux2=oRow.cells[i].getElementsByTagName('input');if(aAux2){for(j=0;j<aAux2.length;j++){sFieldName=aAux2[j].id.replace('form[','');sFieldName=sFieldName.replace(/]$/,'');aAux1.push(sFieldName);}}}}
return aAux1;};var notValidateThisFields=[];function getElementsByClassNameCrossBrowser(searchClass,node,tag){var classElements=new Array();if(node==null)
node=document;if(tag==null)
tag='*';var els=node.getElementsByTagName(tag);var elsLen=els.length;var pattern=new RegExp("(^|\\s)"+searchClass+"(\\s|$)");for(i=0,j=0;i<elsLen;i++){if(pattern.test(els[i].className)){classElements[j]=els[i];j++;}}
return classElements;}
var validateGridForms=function(invalidFields){grids=getElementsByClassNameCrossBrowser("grid",document,"div");Tlabels=getElementsByClassNameCrossBrowser("tableGrid",document,"table");nameGrid="";for(cnt=0;cnt<Tlabels.length;cnt++){if(Tlabels[cnt].getAttribute("name")){nameGrid=Tlabels[cnt].getAttribute("name");if(notValidateThisFields.inArray(nameGrid)){return invalidFields;}}}
for(j=0;j<grids.length;j++){fields=grids[j].getElementsByTagName('input');for(i=0;i<fields.length;i++){var vtext=new input(fields[i]);if(fields[i].getAttribute("pm:required")=="1"&&fields[i].value==''){$label=fields[i].name.split("[");$labelPM=fields[i].getAttribute("pm:label");if($labelPM==''||$labelPM==null){$fieldName=$label[3].split("]")[0]+" "+$label[2].split("]")[0];}else{$fieldName=$labelPM+" "+$label[2].split("]")[0];}
fieldGridName=$label[1]+"["+$label[2]+"["+$label[3].split("]")[0];if(!notValidateThisFields.inArray(fieldGridName)){invalidFields.push($fieldName);}
vtext.failed();}else{vtext.passed();}}
textAreas=grids[j].getElementsByTagName('textarea');for(i=0;i<textAreas.length;i++){var vtext=new input(textAreas[i]);if(textAreas[i].getAttribute("pm:required")=="1"&&textAreas[i].value==''){$label=textAreas[i].name.split("[");$labelPM=textAreas[i].getAttribute("pm:label");if($labelPM==''||$labelPM==null){$fieldName=$label[3].split("]")[0]+" "+$label[2].split("]")[0];}else{$fieldName=$labelPM+" "+$label[2].split("]")[0];}
fieldGridName=$label[1]+"["+$label[2]+"["+$label[3].split("]")[0];if(!notValidateThisFields.inArray(fieldGridName)){invalidFields.push($fieldName);}
vtext.failed();}else{vtext.passed();}}
dropdowns=grids[j].getElementsByTagName('select');for(i=0;i<dropdowns.length;i++){var vtext=new input(dropdowns[i]);if(dropdowns[i].getAttribute("pm:required")=="1"&&dropdowns[i].value==''){$label=dropdowns[i].name.split("[");$labelPM=dropdowns[i].getAttribute("pm:label");if($labelPM==''||$labelPM==null){$fieldName=$label[3].split("]")[0]+" "+$label[2].split("]")[0];}else{$fieldName=$labelPM+" "+$label[2].split("]")[0];}
fieldGridName=$label[1]+"["+$label[2]+"["+$label[3].split("]")[0];if(!notValidateThisFields.inArray(fieldGridName)){invalidFields.push($fieldName);}
vtext.failed();}else{vtext.passed();}}}
return(invalidFields);};var validateForm=function(sRequiredFields){if(typeof(sRequiredFields)!='object'||sRequiredFields.indexOf("%27")>0){sRequiredFields=sRequiredFields.replace(/%27/gi,'"');}
if(typeof(sRequiredFields)!='object'||sRequiredFields.indexOf("%39")>0){sRequiredFields=sRequiredFields.replace(/%39/gi,"'");}
aRequiredFields=eval(sRequiredFields);var sMessage='';var invalid_fields=Array();var fielEmailInvalid=Array();for(var i=0;i<aRequiredFields.length;i++){aRequiredFields[i].label=(aRequiredFields[i].label=='')?aRequiredFields[i].name:aRequiredFields[i].label;if(!notValidateThisFields.inArray(aRequiredFields[i].name)){if(typeof aRequiredFields[i].required!='undefined'){required=aRequiredFields[i].required;}
else{required=1;}
if(typeof aRequiredFields[i].validate!='undefined'){validate=aRequiredFields[i].validate;}
else{validate='';}
if(required==1)
{switch(aRequiredFields[i].type){case'suggest':var vtext1=new input(getField(aRequiredFields[i].name+'_label'));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext1.failed();}else{vtext1.passed();}
break;case'text':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}
else{vtext.passed();}
break;case'dropdown':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}else{vtext.passed();}
break;case'textarea':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}else{vtext.passed();}
break;case'password':var vpass=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vpass.failed();}else{vpass.passed();}
break;case'currency':var vcurr=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vcurr.failed();}else{vcurr.passed();}
break;case'percentage':var vper=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vper.failed();}else{vper.passed();}
break;case'yesno':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}else{vtext.passed();}
break;case'date':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}else{vtext.passed();}
break;case'file':var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value==''){invalid_fields.push(aRequiredFields[i].label);vtext.failed();}else{vtext.passed();}
break;case'listbox':var oAux=getField(aRequiredFields[i].name);var bOneSelected=false;for(var j=0;j<oAux.options.length;j++){if(oAux.options[j].selected){bOneSelected=true;j=oAux.options.length;}}
if(bOneSelected==false)
invalid_fields.push(aRequiredFields[i].label);break;case'radiogroup':var x=aRequiredFields[i].name;var oAux=document.getElementsByName('form['+x+']');var bOneChecked=false;for(var k=0;k<oAux.length;k++){var r=oAux[k];if(r.checked){bOneChecked=true;k=oAux.length;}}
if(bOneChecked==false)
invalid_fields.push(aRequiredFields[i].label);break;case'checkgroup':var bOneChecked=false;var aAux=document.getElementsByName('form['+aRequiredFields[i].name+'][]');for(var k=0;k<aAux.length;k++){if(aAux[k].checked){bOneChecked=true;k=aAux.length;}}
if(!bOneChecked){invalid_fields.push(aRequiredFields[i].label);}
break;}}
if(validate!=''){switch(aRequiredFields[i].type){case'suggest':break;case'text':if(validate=="Email"){var vtext=new input(getField(aRequiredFields[i].name));if(getField(aRequiredFields[i].name).value!=''){var email=getField(aRequiredFields[i].name);var filter=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;if(!filter.test(email.value)&&email.value!=""){fielEmailInvalid.push(aRequiredFields[i].label);vtext.failed();email.focus();}
else{vtext.passed();}}}
break;}}}}
invalid_fields=validateGridForms(invalid_fields);if(invalid_fields.length>0||fielEmailInvalid.length>0){for(j=0;j<invalid_fields.length;j++){sMessage+=(j>0)?', ':'';sMessage+=invalid_fields[j];}
var emailInvalidMessage="";for(j=0;j<fielEmailInvalid.length;j++){emailInvalidMessage+=(j>0)?', ':'';emailInvalidMessage+=fielEmailInvalid[j];}
var systemMessaggeInvalid="";if(invalid_fields.length>0){systemMessaggeInvalid+="\n \n"+G_STRINGS.ID_REQUIRED_FIELDS+": \n \n [ "+sMessage+" ]";}
if(fielEmailInvalid.length>0){systemMessaggeInvalid+="\n \n"+G_STRINGS.ID_VALIDATED_FIELDS+": \n \n [ "+emailInvalidMessage+" ]";}
alert(systemMessaggeInvalid);return false;}
else{var arrayForm=document.getElementsByTagName("form");var inputAux;var id="";var i1=0;var i2=0;for(i1=0;i1<=arrayForm.length-1;i1++){var frm=arrayForm[i1];for(i2=0;i2<=frm.elements.length-1;i2++){var elem=frm.elements[i2];if(elem.type=="checkbox"&&elem.disabled&&elem.checked){id=elem.id+"_";if(!document.getElementById(id)){inputAux=document.createElement("input");inputAux.type="hidden";inputAux.id=id;inputAux.name=elem.name;inputAux.value=elem.value;frm.appendChild(inputAux);}}}
var arrayLink=frm.getElementsByTagName("a");for(i2=0;i2<=arrayLink.length-1;i2++){var link=arrayLink[i2];if(typeof link.id!="undefined"&&link.id!=""&&link.id!="form[DYN_BACKWARD]"&&link.id!="form[DYN_FORWARD]"){var strHtml=link.parentNode.innerHTML;strHtml=stringReplace("\\x0A","",strHtml);strHtml=stringReplace("\\x0D","",strHtml);strHtml=stringReplace("\\x09","",strHtml);if(/^.*pm:field.*$/.test(strHtml)){id=link.id+"_";if(!document.getElementById(id)){var strAux=link.id.replace("form[","");strAux=strAux.substring(0,strAux.length-1);inputAux=document.createElement("input");inputAux.type="hidden";inputAux.id=id;inputAux.name=link.id;inputAux.value=link.href;frm.appendChild(inputAux);inputAux=document.createElement("input");inputAux.type="hidden";inputAux.id=id+"label";inputAux.name="form["+strAux+"_label]";inputAux.value=link.innerHTML;frm.appendChild(inputAux);}}}}}
return true;}};var getObject=function(sObject){var i;var oAux=null;var iLength=__aObjects__.length;for(i=0;i<iLength;i++){oAux=__aObjects__[i].getElementByName(sObject);if(oAux){return oAux;}}
return oAux;};var saveAndRefreshForm=function(oObject){if(oObject){oObject.form.action+='&_REFRESH_=1';oObject.form.submit();}
else{var oAux=window.document.getElementsByTagName('form');if(oAux.length>0){oAux[0].action+='&_REFRESH_=1';oAux[0].submit();}}};var saveForm=function(oObject){if(oObject){ajax_post(oObject.form.action,oObject.form,'POST');}
else{var oAux=window.document.getElementsByTagName('form');if(oAux.length>0){ajax_post(oAux[0].action,oAux[0],'POST');}}};var validateURL=function(url){var regexp=/http?s?:\/\/([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?/;if(regexp.test(url)){return true;}else{return false;}};var saveAndRedirectForm=function(oObject,oLocation){saveForm(oObject);if(validateURL(oLocation)){if(typeof(parent)!="undefined"){parent.location.href=oLocation;}else{document.location.href=oLocation;}}};var removeRequiredById=function(sFieldName){if(!notValidateThisFields.inArray(sFieldName)){notValidateThisFields.push(sFieldName);var oAux=document.getElementById('__notValidateThisFields__');if(oAux){oAux.value=notValidateThisFields.toJSONString();}}};var enableRequiredById=function(sFieldName){if(notValidateThisFields.inArray(sFieldName)){var i;var aAux=[];for(i=0;i<notValidateThisFields.length;i++){if(notValidateThisFields[i]!=sFieldName){aAux.push(notValidateThisFields[i]);}}
notValidateThisFields=aAux;var oAux=document.getElementById('__notValidateThisFields__');if(oAux){oAux.value=notValidateThisFields.toJSONString();}}};function dynaformVerifyFieldName(){pme_validating=true;setTimeout('verifyFieldName1();',0);return true;}
function verifyFieldName1(){var newFieldName=fieldName.value;var msj=_('DYNAFIELD_ALREADY_EXIST');var validatedFieldName=getField("PME_VALIDATE_NAME",fieldForm).value;var dField=new input(getField('PME_XMLNODE_NAME'));var valid=(newFieldName!=='')&&(((newFieldName!==savedFieldName)&&(validatedFieldName===''))||((newFieldName===savedFieldName)));if(newFieldName.length==0){valid=false;msj=_('DYNAFIELD_EMPTY');}
if(!(isNaN(parseInt(newFieldName.substr(0,1))))){valid=false;msj=_('DYNAFIELD_NODENAME_NUMBER');}
if(valid){dField.passed();getField("PME_ACCEPT",fieldForm).disabled=false;}else{getField("PME_ACCEPT",fieldForm).disabled=true;dField.failed();new leimnud.module.app.alert().make({label:msj});dField.focus();}
pme_validating=false;return valid;}
var objectsWithFormula=Array();function sumaformu(ee,fma,mask){afma=fma;var operators=['+','-','*','/','(','[','{','}',']',')',',','Math.pow','Math.PI','Math.sqrt'];var wos;for(var i=0;i<operators.length;i++){var j=0;while(j<fma.length){nfma=fma.replace(operators[i]," ");nfma=nfma.replace("  "," ");fma=nfma;j++;}}
wos=nfma.replace(/^\s+/g,'');nfma=wos.replace(/\s+$/g,'');theelemts=nfma.split(" ");objectsWithFormula[objectsWithFormula.length]={ee:ee,fma:afma,mask:mask,theElements:theelemts};for(var i=0;i<theelemts.length;i++){leimnud.event.add(getField(theelemts[i]),'keyup',function(key){var eventElement=key.srcElement?key.srcElement:key.target;if(typeof(this.id)=='undefined'){myId=eventElement.id.replace("form[","").replace("]","");}
else{myId=this.id.replace("form[","").replace("]","");}
for(i_elements=0;i_elements<objectsWithFormula.length;i_elements++){for(i_elements2=0;i_elements2<objectsWithFormula[i_elements].theElements.length;i_elements2++){if(objectsWithFormula[i_elements].theElements[i_elements2]==myId)
{var formula=objectsWithFormula[i_elements].fma;var ans=objectsWithFormula[i_elements].ee;var theelemts=objectsWithFormula[i_elements].theElements;for(var i=0;i<=theelemts.length-1;i++){var elem=getField(theelemts[i]);var elemAttribute=elementAttributesNS(elem,"pm");var elemValue=getNumericValue(elem.value,((typeof elemAttribute.decimal_separator!="undefined")?elemAttribute.decimal_separator:""));formula=formula.replace(theelemts[i],((elemValue=="")?0:parseFloat(elemValue)));}
var result=eval(formula);if(mask!=""){var elemAttribute=elementAttributesNS(ans,"pm");putFieldNumericValue(ans,result,mask,((typeof elemAttribute.decimal_separator!="undefined")?elemAttribute.decimal_separator:""));}else{ans.value=result;}}}}});}}
function showRowsById(aFields){for(i=0;i<aFields.length;i++){row=getRow(aFields[i]);if(row){row.style.display='';}}}
function hideRowsById(aFields){for(i=0;i<aFields.length;i++){row=getRow(aFields[i]);if(row){row.style.display='none';}}}
function dateSetMask(mask){if(mask!=''){mask=stringReplace("%y","yy",mask);mask=stringReplace("%Y","yyyy",mask);mask=stringReplace("%m","mm",mask);mask=stringReplace("%o","mm",mask);mask=stringReplace("%d","dd",mask);mask=stringReplace("%e","dd",mask);mask=stringReplace("%H","##",mask);mask=stringReplace("%I","##",mask);mask=stringReplace("%k","##",mask);mask=stringReplace("%l","##",mask);mask=stringReplace("%M","##",mask);mask=stringReplace("%S","##",mask);mask=stringReplace("%j","###",mask);}
return mask;}
function putFieldNumericValue(elem,num,mask,decimalSeparator)
{var strNum=num.toString();var arrayAux=[];var maskNumber="";var maskDecimal="";if(decimalSeparator!=""&&mask.indexOf(decimalSeparator)!=-1){arrayAux=mask.split(decimalSeparator);maskNumber=arrayAux[0];maskDecimal=arrayAux[1];}else{maskNumber=mask;maskDecimal="";}
var n="";var d="";if(strNum.indexOf(".")!=-1){arrayAux=strNum.split(".");n=arrayAux[0];d=arrayAux[1];}else{n=strNum;d="";}
var i=0;var cont=0;var pos=maskNumber.indexOf("#");if(pos!=-1){var mask1=maskNumber.substring(0,pos);var strAux=maskNumber.split("").reverse().join("");cont=0;pos=-1;for(i=0;i<=strAux.length-1;i++){if(strAux.charAt(i)=="#"){cont=cont+1;if(cont==n.length){pos=i;break;}}}
var mask2="";if(pos!=-1){mask2=strAux.substring(0,pos+1);mask2=mask2.split("").reverse().join("");}else{mask1=maskNumber;}
maskNumber=mask1+mask2;}
var newNumber=putStringMask(n,maskNumber,"reverse");var newDecimal=putStringMask(d,maskDecimal,"forward");elem.value=newNumber+decimalSeparator+newDecimal;}
function putStringMask(str,mask,dir)
{var newStr="";var i1=0;var i2=0;if(dir=="reverse"){str=str.split("").reverse().join("");mask=mask.split("").reverse().join("");}
for(i1=0;i1<=mask.length-1;i1++){switch(mask.charAt(i1)){case"#":if(i2<=str.length-1){newStr=newStr+str.charAt(i2);i2=i2+1;}else{newStr=newStr+"0";}
break;default:newStr=newStr+mask.charAt(i1);break;}}
if(dir=="reverse"){newStr=newStr.split("").reverse().join("");}
return newStr;}
function getNumericValue(val,decimalSeparator)
{var arrayNum=val.split("");var num="";for(var i=0;i<=arrayNum.length-1;i++){switch(arrayNum[i]){case"0":case"1":case"2":case"3":case"4":case"5":case"6":case"7":case"8":case"9":num=num+arrayNum[i];break;case decimalSeparator:num=num+".";break;}}
return num;}
function G_PagedTable()
{this.id='';this.name='';this.event='';this.element=null;this.field='';this.ajaxUri='';this.currentOrder='';this.currentFilter='';this.currentPage=1;this.totalRows=0;this.rowsPerPage=25;this.onInsertField='';this.onDeleteField='';this.afterDeleteField='';this.onUpdateField='';this.form;var me=this;function loadTable(func,uri){var div=document.getElementById('table['+me.id+']');var newContent=ajax_function(me.ajaxUri,func,uri);if(div.outerHTML){div.outerHTML=div.outerHTML.split(div.innerHTML).join(newContent);}else{div.innerHTML=newContent;}
var myScripts=div.getElementsByTagName('SCRIPT');for(var rr=0;rr<myScripts.length;rr++){try{if(myScripts[rr].innerHTML!=='')
if(window.execScript)
window.execScript(myScripts[rr].innerHTML,'javascript');else
window.setTimeout(myScripts[rr].innerHTML,0);}catch(e){alert(e.description);}}
eval("if (loadPopupMenu_"+me.id+")loadPopupMenu_"+me.id+"();");delete div;delete myScripts;}
this.showHideField=function(field)
{uri='field='+encodeURIComponent(field);var ns=[],showIt=true;for(var i=0,j=me.shownFields.length;i<j;i++){if(me.shownFields[i]!==field)ns.push(me.shownFields[i]);else showIt=false;}
if(showIt)ns.push(field);me.shownFields=ns;loadTable('showHideField',uri);}
this.updateField=function(field,title,width,height)
{width=width||500;height=height||200;popupWindow(title,this.popupPage+'&field='+encodeURIComponent(field),width,height);}
this.deleteField=function(field)
{}
this.doFilter=function(searchForm)
{var inputs,r,uri;inputs=searchForm.elements;me.currentFilter='';for(r=0;r<inputs.length;r++)
if(inputs[r].value!='')
{if(me.currentFilter!='')me.currentFilter+='&';me.currentFilter+=inputs[r].id+'='+encodeURIComponent(inputs[r].value);}
uri='order='+encodeURIComponent(me.currentOrder)
+'&page='+me.currentPage;if(me.currentFilter!='')
uri=uri+'&filter='+encodeURIComponent(me.currentFilter);loadTable('paint',uri);}
this.doFastSearch=function(criteria)
{uri='fastSearch='+encodeURIComponent(criteria);loadTable('paint',uri);}
this.doSort=function(fieldName,orderDirection)
{var inputs,r,uri;if(orderDirection)
me.currentOrder=fieldName+'='+orderDirection;else
me.currentOrder='';uri='order='+encodeURIComponent(me.currentOrder)
+'&page='+me.currentPage;if(me.currentFilter!='')
uri=uri+'&filter='+encodeURIComponent(me.currentFilter);loadTable('paint',uri);}
this.refresh=function()
{loadTable('paint','');}
this.doGoToPage=function(nextCurrentPage)
{var inputs,r,uri;me.currentPage=nextCurrentPage;uri='order='+encodeURIComponent(me.currentOrder)
+'&page='+me.currentPage;if(me.currentFilter!='')
uri=uri+'&filter='+encodeURIComponent(me.currentFilter);var ee=document.getElementById('table['+me.id+']');var newContent=ajax_function(me.ajaxUri,'paint',uri);if(ee.outerHTML)
ee.outerHTML=ee.outerHTML.split(ee.innerHTML).join(newContent);else
ee.innerHTML=newContent;delete ee;delete newContent;}
function encodeData(data)
{var enc;enc='';if(typeof(data)=='object')
for(u in data)
enc+='&'+u+'='+encodeURIComponent(data[u]);return encodeURIComponent(enc);}}
function popup(url)
{var h;lleft=((document.body.clientWidth/2)+document.body.scrollLeft);ltop=((document.body.clientHeight/2)+document.body.scrollTop);panelPopup=leimnud.panel.create({w:popupWidth,h:popupHeight},{x:lleft,y:ltop,center:true},"popup",9,false,{botones:{cerrar:true},style:{panel:{border:"1px solid #000000",color:"#000000",backgroundColor:"#FEFEFE"},html:{textAlign:"left",padding:"5px",paddingTop:"12px"}}});leimnud.panel.loader.begin(panelPopup);uyh=new leimnud.rpc.xmlhttp({method:"GET",url:url,callback:{_function:function($)
{leimnud.panel.loader.end($.arguments.obj);dc=$dce("div");leimnud.style.set(dc,{textAlign:"justify"});dc.innerHTML=$.request.responseText;leimnud.panel.html($.arguments.obj,dc);leimnud.panel.sombra($.arguments.obj,{sombra:{color:"#000000",opacity:30}});},arguments:{obj:panelPopup}}});}
function setRowClass(theRow,thePointerClass)
{if(thePointerClass==''||typeof(theRow.className)=='undefined'){return false;}
if(globalRowSelected==null||globalRowSelected.id!=theRow.id){globalRowSelectedClass=theRow.className;theRow.className=thePointerClass;}
return true;}
var globalRowSelected=null;var globalRowSelectedClass;function focusRow(o,className){if(className==''||typeof(o.className)=='undefined'){return false;}
if(globalRowSelected!=null){globalRowSelected.className=globalRowSelectedClass;}
globalRowSelected=o;o.className=className;return true;}
var G_Grid=function(oForm,sGridName){var oGrid=this;this.parent=G_Field;this.parent(oForm,'',sGridName);this.sGridName=sGridName;this.sAJAXPage=oForm.ajaxServer||'';this.oGrid=document.getElementById(this.sGridName);this.onaddrow=function(iRow){};this.ondeleterow=function(){};this.executeEvent=function(element,event){if(document.createEventObject){var evt=document.createEventObject();return element.fireEvent('on'+event,evt)}else{var evt=document.createEvent("HTMLEvents");evt.initEvent(event,true,true);return!element.dispatchEvent(evt);}};this.aFields=[];this.aElements=[];this.aFunctions=[];this.aFormulas=[];this.allDependentFields='';this.countRows=function(){return this.aElements.length/this.aFields.length;};this.getObjectName=function(Name){var arr=Name.split('][');var aux=arr.pop();aux=aux.replace(']','');return aux;};this.setFields=function(aFields,iRow)
{var tableGrid=document.getElementById(this.sGridName);var elem;var elemName="";var i=0;var j=0;this.aFields=aFields;for(i=iRow||1;i<=tableGrid.rows.length-2;i++){for(j=0;j<=this.aFields.length-1;j++){elemName=this.sGridName+"]["+i+"]["+this.aFields[j].sFieldName;if((elem=document.getElementById("form["+elemName+"]"))){switch(this.aFields[j].sType){case"text":this.aElements.push(new G_Text(oForm,elem,elemName));this.aElements[this.aElements.length-1].validate=this.aFields[j].oProperties.validate;if(this.aFields[j].oProperties.strTo){this.aElements[this.aElements.length-1].strTo=this.aFields[j].oProperties.strTo;}
if(this.aFields[j].oProperties){this.aElements[this.aElements.length-1].mask=this.aFields[j].oProperties.mask;}
break;case"currency":this.aElements.push(new G_Currency(oForm,elem,elemName));if(this.aFields[j].oProperties){if(this.aFields[j].oProperties.comma_separator){this.aElements[this.aElements.length-1].comma_separator=this.aFields[j].oProperties.comma_separator;}
this.aElements[this.aElements.length-1].validate=this.aFields[j].oProperties.validate;this.aElements[this.aElements.length-1].mask=this.aFields[j].oProperties.mask;}
break;case"percentage":this.aElements.push(new G_Percentage(oForm,elem,elemName));if(this.aFields[j].oProperties){if(this.aFields[j].oProperties.comma_separator){this.aElements[this.aElements.length-1].comma_separator=this.aFields[j].oProperties.comma_separator;}
this.aElements[this.aElements.length-1].validate=this.aFields[j].oProperties.validate;this.aElements[this.aElements.length-1].mask=this.aFields[j].oProperties.mask;}
break;case"dropdown":this.aElements.push(new G_DropDown(oForm,elem,elemName));if(this.aFields[j].oProperties){this.aElements[this.aElements.length-1].mask=this.aFields[j].oProperties.sMask;}
break;default:this.aElements.push(new G_Field(oForm,elem,elemName));if(this.aFields[j].oProperties){this.aElements[this.aElements.length-1].mask=this.aFields[j].oProperties.sMask;}
break;}}}}
var sw=false;if(this.allDependentFields==""){sw=true;}
for(j=0;j<=this.aFields.length-1;j++){i=iRow||1;while((elem=document.getElementById("form["+this.sGridName+"]["+i+"]["+this.aFields[j].sFieldName+"]"))){if(this.aFields[j].oProperties.dependentFields!=""){this.setDependents(i,this.getElementByName(i,this.aFields[j].sFieldName),aFields[j].oProperties.dependentFields,sw);}
i=i+1;}}};this.setDependents=function(iRow,me,theDependentFields,sw){var i;var dependentFields=theDependentFields||'';dependentFields=dependentFields.split(',');for(i=0;i<dependentFields.length;i++){var oField=this.getElementByName(iRow,dependentFields[i]);if(oField){me.dependentFields[i]=oField;me.dependentFields[i].addDependencie(me);if(sw){if(this.allDependentFields!='')this.allDependentFields+=',';this.allDependentFields+=dependentFields[i];}}}};this.unsetFields=function(){var i,j=0,k,l=0;k=this.aElements.length/this.aFields.length;for(i=0;i<this.aFields.length;i++){j+=k;l++;this.aElements.splice(j-l,1);}};this.getElementByName=function(iRow,sName){var i;for(i=0;i<this.aElements.length;i++){if(this.aElements[i].name===this.sGridName+']['+iRow+']['+sName){return this.aElements[i];}}
return null;};this.getElementValueByName=function(iRow,sName){var oAux=document.getElementById('form['+this.sGridName+']['+iRow+']['+sName+']');if(oAux){return oAux.value;}else{return'Object not found!';}};this.getFunctionResult=function(sName){var oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+this.sGridName+'_'+sName+']');if(oAux){return oAux.value;}else{return'Object not found!';}};this.cloneElement=function(elem)
{var strHtml=elem.parentNode.innerHTML;var tag=new String(elem.nodeName);var arrayMatch=[];var arrayAux1=[];var arrayAux2=[];var strHtmlAux="";var strAux="";var i=0;strHtml=stringReplace("\\x0A","",strHtml);strHtml=stringReplace("\\x0D","",strHtml);strHtml=stringReplace("\\x09","",strHtml);if((arrayMatch=eval("/^.*(<"+tag+".*id=\""+elem.id+"\".*>).*$/i").exec(strHtml))){strHtml=arrayMatch[1];}
strHtml=stringReplace("<"+tag,"",strHtml);strHtml=stringReplace("<"+tag.toLowerCase(),"",strHtml);strHtml=stringReplace("\\/>.*","",strHtml);strHtml=stringReplace(">.*","",strHtml);strHtmlAux=strHtml;strAux="";while((arrayMatch=/^(.*)(".*")(.*)$/g.exec(strHtmlAux))){strHtmlAux=arrayMatch[1];strAux=stringReplace(" ","__SPACE__",arrayMatch[2])+arrayMatch[3]+strAux;}
strAux=strHtmlAux+strAux;strHtml=strAux;if(/^.*read[oO]nly.*$/.test(strHtml)){if(!(/^.*read[oO]nly\=.*$/.test(strHtml))){strHtml=stringReplace("read[oO]nly","readonly=\"\"",strHtml);}}
if(/^.*disabled.*$/.test(strHtml)){if(!(/^.*disabled\=.*$/.test(strHtml))){strHtml=stringReplace("disabled","disabled=\"\"",strHtml);}}
if(/^.*checked.*$/i.test(strHtml)){strHtml=stringReplace("CHECKED","checked",strHtml);if(!(/^.*checked\=.*$/.test(strHtml))){strHtml=stringReplace("checked","checked=\"\"",strHtml);}}
var arrayAttribute=[];var a="";var v="";arrayAux1=strHtml.split(" ");for(i=0;i<=arrayAux1.length-1;i++){arrayAux2=arrayAux1[i].split("=");if(typeof arrayAux2[1]!="undefined"){a=arrayAux2[0].trim();v=stringReplace("\\\"","",arrayAux2[1]);v=stringReplace("__SPACE__"," ",v);arrayAttribute.push([a,v]);}}
var newElem=document.createElement(tag.toLowerCase());for(i=0;i<=arrayAttribute.length-1;i++){a=arrayAttribute[i][0];v=arrayAttribute[i][1];switch(a.toLowerCase()){case"id":case"name":newElem.setAttribute("id",elem.id);newElem.setAttribute("name",elem.id);break;case"class":newElem.className=v;break;case"style":newElem.style.cssText=((/^.*display\s*:\s*none.*$/i.test(strHtml))?"display: none;":"")+v;break;case"disabled":if(elem.disabled){newElem.disabled=true;}
break;case"readonly":if(elem.readOnly){newElem.readOnly=true;}
break;case"checked":if(elem.checked){newElem.checked=true;}
break;default:newElem.setAttribute(a,v);break;}}
switch(tag.toLowerCase()){case"input":case"textarea":newElem.value=elem.value;break;case"select":if(elem.options.length>0){var pos=0;for(i=0;i<=elem.options.length-1;i++){if(elem.options[i].selected){pos=i;}
newElem.options[i]=new Option(elem.options[i].text,elem.options[i].value,elem.options[i].defaultSelected);}
newElem.options[pos].selected=true;}
break;}
return newElem;};this.addGridRow=function(){this.oGrid=document.getElementById(this.sGridName);var i,aObjects;var defaultValue='';var n,a,x;var oRow=document.getElementById('firstRow_'+this.sGridName);var aCells=oRow.getElementsByTagName('td');var oNewRow=this.oGrid.insertRow(this.oGrid.rows.length-1);var currentRow=this.oGrid.rows.length-2;var newID,attributes,img2,gridType;oNewRow.onmouseover=function(){highlightRow(this,'#D9E8FF');};oNewRow.onmouseout=function(){highlightRow(this,'#fff');};for(i=0;i<aCells.length;i++){oNewRow.appendChild(aCells[i].cloneNode(true));switch(i){case 0:oNewRow.getElementsByTagName('td')[i].innerHTML=currentRow;break;case aCells.length-1:oNewRow.getElementsByTagName('td')[i].innerHTML=oNewRow.getElementsByTagName('td')[i].innerHTML.replace(/\[1\]/g,'\['+currentRow+'\]');break;default:var eNodeName=aCells[i].innerHTML.substring(aCells[i].innerHTML.indexOf('<')+1,aCells[i].innerHTML.indexOf(' '));eNodeName=eNodeName.toLowerCase();switch(eNodeName){case'input':aObjects=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('input');if(aObjects){newID=aObjects[0].id.replace(/\[1\]/g,'\['+currentRow+'\]');aObjects[0].id=newID;aObjects[0].name=newID;attributes=elementAttributesNS(aObjects[0],'pm');if(typeof attributes.defaultvalue!="undefined"&&attributes.defaultvalue!=""){defaultValue=attributes.defaultvalue;}else{defaultValue="";}
for(n=0;n<aObjects.length;n++){switch(aObjects[n].type){case'text':aObjects[n].className="module_app_input___gray";tags=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');if(tags.length==2){img2=tags[1].innerHTML;var datePickerTriggerId=tags[1].id.replace(/\[1\]/g,'\['+currentRow+'\]');oNewRow.getElementsByTagName('td')[i].removeChild(tags[1]);var scriptTags=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('script');oNewRow.getElementsByTagName('td')[i].removeChild(scriptTags[0]);if(tags[0].onclick){var onclickevn=new String(tags[0].onclick);eval('tags[0].onclick = '+onclickevn.replace(/\[1\]/g,'\['+currentRow+'\]')+';');}
var a2=document.createElement('a');if(a2.style.setAttribute){var styleText="position:relative;top:0px;left:-19px;";a2.style.setAttribute("cssText",styleText);}
else{var styleText="position:relative;top:0px;left:-22px;";a2.setAttribute("style",styleText);}
a2.id=datePickerTriggerId;a2.innerHTML=img2;oNewRow.getElementsByTagName('td')[i].appendChild(a2);datePicker4("",newID,attributes.mask,attributes.start,attributes.end,attributes.time);aObjects[n].value=defaultValue;}else{if(_BROWSER.name=='msie'&&aObjects.length==1){var oNewOBJ=this.cloneElement(aObjects[n]);oNewOBJ.value=defaultValue;var parentGG=aObjects[n].parentNode;parentGG.removeChild(aObjects[n]);parentGG.appendChild(oNewOBJ);}else{if((attributes.gridtype)&&attributes.gridtype=="currency"){var attributesCurrency=elementAttributesNS(aObjects[n],"");aObjects[n].value=attributesCurrency.value.replace(/[.,0-9\s]/g,"");}else{aObjects[n].value=defaultValue;}}}
break;case'checkbox':var attributeCheckBox=elementAttributesNS(aObjects[n],"");if(defaultValue==""||(typeof attributeCheckBox.falseValue!="undefined"&&defaultValue==attributeCheckBox.falseValue)||(typeof attributeCheckBox.falsevalue!="undefined"&&defaultValue==attributeCheckBox.falsevalue)){aObjects[n].checked=false;}else{aObjects[n].checked=true;}
break;case'hidden':if((attributes.gridtype!='yesno'&&attributes.gridtype!='dropdown')||typeof attributes.gridtype=='undefined')
aObjects[n].value=defaultValue;break;case'button':if(aObjects[n].onclick){var onclickevn=new String(aObjects[n].onclick);eval('aObjects[n].onclick = '+onclickevn.replace(/\[1\]/g,'\['+currentRow+'\]')+';');}
break;case"file":aObjects[n].value="";break;}}}
aObjects=null;break;case'textarea':aObjects=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('textarea');if(aObjects){aObjects[0].className="module_app_input___gray";newID=aObjects[0].id.replace(/\[1\]/g,'\['+currentRow+'\]');aObjects[0].id=newID;aObjects[0].name=newID;attributes=elementAttributesNS(aObjects[0],'pm');if(attributes.defaultvalue!=''&&typeof attributes.defaultvalue!='undefined'){defaultValue=attributes.defaultvalue;}else{defaultValue='';}
aObjects[0].innerHTML=defaultValue;}
aObjects=null;break;case'select':var oNewSelect;aObjects=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('select');if(aObjects){newID=aObjects[0].id.replace(/\[1\]/g,'\['+currentRow+'\]');aObjects[0].id=newID;aObjects[0].name=newID;oNewSelect=document.createElement(aObjects[0].tagName);oNewSelect.id=newID;oNewSelect.name=newID;oNewSelect.setAttribute('class','module_app_input___gray');aAttributes=aObjects[0].attributes;for(a=0;a<aAttributes.length;a++){if(aAttributes[a].name.indexOf('pm:')!=-1){oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);}
if(aAttributes[a].name=='disabled'){if(_BROWSER.name=='msie'){if(aAttributes[a].value=='true'){oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);}}
else{oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);}}}
attributes=elementAttributesNS(aObjects[0],'pm');if(attributes.defaultvalue!=''&&typeof attributes.defaultvalue!='undefined'){defaultValue=attributes.defaultvalue;}else{defaultValue='';}
if(attributes.gridtype!=''&&typeof attributes.gridtype!='undefined'){gridType=attributes.gridtype;}else{gridType='';}
var aDependents=this.allDependentFields.split(',');sObject=this.getObjectName(newID);var sw=false;for(x=0;x<aDependents.length;x++){if(aDependents[x]==sObject)sw=true;}
if(sw){var oAux=document.createElement(aObjects[0].tagName);for(var j=0;j<aObjects[0].options.length;j++){if(aObjects[0].options[j].value==''){var oOption=document.createElement('OPTION');oOption.value=aObjects[0].options[j].value;oOption.text=aObjects[0].options[j].text;oAux.options.add(oOption);}}
oNewSelect.innerHTML='';for(var r=0;r<oAux.options.length;r++){var xOption=document.createElement('OPTION');xOption.value=oAux.options[r].value;xOption.text=oAux.options[r].text;oNewSelect.options.add(xOption);}}else{if(defaultValue!=''){var oAux=document.createElement(aObjects[0].tagName);for(var j=0;j<aObjects[0].options.length;j++){var oOption=document.createElement('OPTION');oOption.value=aObjects[0].options[j].value;oOption.text=aObjects[0].options[j].text;if(aObjects[0].options[j].value===defaultValue){oOption.setAttribute('selected','selected');}
oAux.options.add(oOption);}
oNewSelect.innerHTML='';for(var r=0;r<oAux.options.length;r++){var xOption=document.createElement('OPTION');xOption.value=oAux.options[r].value;xOption.text=oAux.options[r].text;if(_BROWSER.name=='msie'){if(oAux.options[r].getAttribute('selected')!=''){xOption.setAttribute('selected','selected');}}else{if(oAux.options[r].getAttribute('selected')=='selected'){xOption.setAttribute('selected','selected');}}
oNewSelect.options.add(xOption);}}else{var oAux=document.createElement(aObjects[0].tagName);for(var j=0;j<aObjects[0].options.length;j++){var oOption=document.createElement('OPTION');oOption.value=aObjects[0].options[j].value;oOption.text=aObjects[0].options[j].text;oNewSelect.options.add(oOption);}}}
var parentSelect=aObjects[0].parentNode;parentSelect.removeChild(aObjects[0]);parentSelect.appendChild(oNewSelect);}
aObjects=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('input');if(aObjects.length>0){newID=aObjects[0].id.replace(/\[1\]/g,'\['+currentRow+'\]');aObjects[0].id=newID;aObjects[0].name=newID;}
aObjects=null;break;case'a':aObjects=oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');if(aObjects){newID=aObjects[0].id.replace(/\[1\]/g,'\['+currentRow+'\]');aObjects[0].id=newID;aObjects[0].name=newID;}
aObjects=null;break;}
break;}}
if(this.aFields.length>0){this.setFields(this.aFields,currentRow);}
if(this.aFunctions.length>0){this.assignFunctions(this.aFunctions,'change',currentRow);}
if(this.aFormulas.length>0){this.assignFormulas(this.aFormulas,'change',currentRow);}
var oAux;if(this.aFunctions.length>0){for(i=0;i<this.aFunctions.length;i++){oAux=document.getElementById('form['+this.sGridName+']['+currentRow+']['+this.aFunctions[i].sFieldName+']');if(oAux){switch(this.aFunctions[i].sFunction){case'sum':this.sum(false,oAux);break;case'avg':this.avg(false,oAux);break;}}}}
var xIsDependentOf=[];var exist=false;var m;for(i=0;i<this.aFields.length;i++){var oAux=this.getElementByName(currentRow,this.aFields[i].sFieldName);if(typeof oAux!=='undefined'&&oAux!=null)
if(typeof oAux.dependentFields!=='undefined'){if(oAux.dependentFields.length>0){exist=false;for(m=0;m<xIsDependentOf.length;m++)
if(xIsDependentOf[m]==oAux.name)exist=true;for(j=0;j<oAux.dependentFields.length;j++){xIsDependentOf.push(oAux.dependentFields[j].name);}
if(!exist){oAux.updateDepententFields();}}}}
for(var i=0;i<this.aFields.length;i++){var fieldName='form['+sGridName+']['+currentRow+']['+this.aFields[i].sFieldName+']';if(this.aFields[i].sType!='file'&&this.aFields[i].sType!='hidden'&&document.getElementById(fieldName).focus){document.getElementById(fieldName).focus();break;}}
if(this.onaddrow){this.onaddrow(currentRow);}};this.deleteGridRow=function(sRow,bWithoutConfirm)
{if(typeof bWithoutConfirm=="undefined"){bWithoutConfirm=false;}
if(this.oGrid.rows.length==3){new leimnud.module.app.alert().make({label:G_STRINGS.ID_MSG_NODELETE_GRID_ITEM});return false;}
if(bWithoutConfirm){this.deleteRowWC(this,sRow);}else{new leimnud.module.app.confirm().make({label:G_STRINGS.ID_MSG_DELETE_GRID_ITEM,action:function()
{this.deleteRowWC(this,sRow);}.extend(this)});}};this.deleteRowWC=function(oObj,aRow)
{var sRow=new String(aRow);sRow=sRow.replace("[","");sRow=sRow.replace("]","");var iRow=Number(sRow);var iRowAux=iRow+1;var lastItem=oObj.oGrid.rows.length-2;var elem2ParentNode;var elem2Id="";var elem2Name="";var elemAux;deleteRowOnDynaform(oObj,iRow);while(iRowAux<=(lastItem)){for(i=1;i<oObj.oGrid.rows[iRowAux-1].cells.length;i++){var oCell1=oObj.oGrid.rows[iRowAux-1].cells[i];var oCell2=oObj.oGrid.rows[iRowAux].cells[i];switch(oCell1.innerHTML.replace(/^\s+|\s+$/g,'').substr(0,6).toLowerCase()){case'<input':aObjects1=oCell1.getElementsByTagName('input');aObjects2=oCell2.getElementsByTagName('input');if(aObjects1&&aObjects2){switch(aObjects2[0].type){case"file":elem2ParentNode=aObjects2[0].parentNode;elem2Id=aObjects2[0].id;elem2Name=aObjects2[0].name;aObjects2[0].id=aObjects1[0].id;aObjects2[0].name=aObjects1[0].name;aObjects1[0].parentNode.replaceChild(aObjects2[0],aObjects1[0]);elemAux=document.createElement("input");elemAux.type="file";elemAux.setAttribute("id",elem2Id);elemAux.setAttribute("name",elem2Name);elem2ParentNode.insertBefore(elemAux,elem2ParentNode.firstChild);break;default:if(aObjects2[0].type=="checkbox"){aObjects1[0].checked=aObjects2[0].checked;}
aObjects1[0].value=aObjects2[0].value;aObjects1[0].className=aObjects2[0].className;break;}}
aObjects=oCell1.getElementsByTagName('div');if(aObjects.length>0){if(aObjects[0]){aObjects[0].id=aObjects[0].id.replace('/\['+(iRowAux-1)+'\]/g','\['+iRowAux+'\]');aObjects[0].name=aObjects[0].id.replace('/\['+(iRowAux-1)+'\]/g','\['+iRowAux+'\]');if(aObjects[0].onclick){sAux=new String(aObjects[0].onclick);eval('aObjects[0].onclick = '+sAux.replace('/\['+(iRowAux-1)+'\]/g','\['+iRowAux+'\]')+';');}}
aObjects=oCell1.getElementsByTagName('a');if(aObjects){if(aObjects[0]){if(aObjects[0].onclick){sAux=new String(aObjects[0].onclick);eval('aObjects[0].onclick = '+sAux.replace('/\['+(iRowAux-1)+'\]/g','\['+iRowAux+'\]')+';');}}}}
break;case'<selec':aObjects1=oCell1.getElementsByTagName('select');aObjects2=oCell2.getElementsByTagName('select');if(aObjects1&&aObjects2){var vValue=aObjects2[0].value;aObjects1[0].options.length=0;for(var j=0;j<aObjects2[0].options.length;j++){var optn=$dce("OPTION");optn.text=aObjects2[0].options[j].text;optn.value=aObjects2[0].options[j].value;aObjects1[0].options[j]=optn;}
aObjects1[0].value=vValue;aObjects1[0].className=aObjects2[0].className;}
break;case'<texta':aObjects1=oCell1.getElementsByTagName('textarea');aObjects2=oCell2.getElementsByTagName('textarea');if(aObjects1&&aObjects2){aObjects1[0].value=aObjects2[0].value;aObjects1[0].className=aObjects2[0].className;}
break;default:if((oCell2.innerHTML.indexOf('changeValues')==111||oCell2.innerHTML.indexOf('changeValues')==115)){break;}
if(oCell2.innerHTML.toLowerCase().indexOf('deletegridrow')==-1){oCell1.innerHTML=oCell2.innerHTML;}
break;}}
iRowAux++;}
this.oGrid.deleteRow(lastItem);var i=0;for(i=0;i<=this.aFields.length-1;i++){this.aElements.pop();}
var elem;if(oObj.aFunctions.length>0){for(i=0;i<=oObj.aFunctions.length-1;i++){elem=document.getElementById("form["+oObj.sGridName+"][1]["+oObj.aFunctions[i].sFieldName+"]");if(elem){switch(oObj.aFunctions[i].sFunction){case"sum":oObj.sum(false,elem);break;case"avg":oObj.avg(false,elem);break;}}}}
if(oObj.ondeleterow){oObj.ondeleterow(iRow);}};this.assignFunctions=function(aFields,sEvent,iRow)
{var elem;var i=0;var j=0;for(j=0;j<=aFields.length-1;j++){i=iRow||1;while((elem=document.getElementById("form["+this.sGridName+"]["+i+"]["+aFields[j].sFieldName+"]"))){switch(aFields[j].sFunction){case"sum":leimnud.event.add(elem,sEvent,{method:this.sum,instance:this,event:true});break;case"avg":leimnud.event.add(elem,sEvent,{method:this.avg,instance:this,event:true});break;default:leimnud.event.add(elem,sEvent,{method:aFields[j].sFunction,instance:this,event:true});break;}
i=i+1;}}};this.setFunctions=function(aFunctions){this.aFunctions=aFunctions;this.assignFunctions(this.aFunctions,'change');};this.determineBrowser=function()
{var nAgt=navigator.userAgent;var browserName="";if(nAgt.indexOf("Opera")!=-1){browserName="Opera";}else{if(nAgt.indexOf("MSIE")!=-1){browserName="MSIE";}else{if(nAgt.indexOf("Chrome")!=-1){browserName="Chrome";}else{if(nAgt.indexOf("Safari")!=-1){browserName="Safari";}else{if(nAgt.indexOf("Firefox")!=-1){browserName="Firefox";}}}}}
return browserName;};this.sum=function(oEvent,oDOM){oDOM=(oDOM?oDOM:oEvent.target||window.event.srcElement);var i,aAux,oAux,fTotal,sMask,nnName;aAux=oDOM.name.split('][');i=1;fTotal=0;aAux[2]=aAux[2].replace(']','');var j=1;for(var k=0;k<this.aElements.length;k++){nnName=this.aElements[k].name.split('][');if(aAux[2]==nnName[2]&&j<=(this.oGrid.rows.length-2)){oAux=this.getElementByName(j,nnName[2]);var oAux2=oAux.value().replace(/[$|a-zA-Z\s]/g,'');if((oAux!=null)&&(oAux.value().trim()!="")&&(oAux2)){fTotal=fTotal+parseFloat(G.getValue(oAux));}
j=j+1;}}
fTotal=fTotal.toFixed(2);oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'_'+aAux[2]+']');oAux.value=fTotal;oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'__'+aAux[2]+']');if(this.determineBrowser()=="MSIE"){oAux.innerText=fTotal;}else{oAux.innerHTML=fTotal;}};this.avg=function(oEvent,oDOM){oDOM=(oDOM?oDOM:oEvent.target||window.event.srcElement);var i,aAux,oAux,fTotal,sMask;aAux=oDOM.name.split('][');i=1;fTotal=0;aAux[2]=aAux[2].replace(']','');while((oAux=this.getElementByName(i,aAux[2]))){if(oAux.value().trim()!=""){fTotal=fTotal+parseFloat(G.getValue(oAux));}
sMask=oAux.mask;i=i+1;}
i--;if(fTotal>0){fTotal=(fTotal/i).toFixed(2);oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'_'+aAux[2]+']');oAux.value=fTotal;oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'__'+aAux[2]+']');if(this.determineBrowser()=="MSIE"){oAux.innerText=fTotal;}else{oAux.innerHTML=fTotal;}}else{oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'_'+aAux[2]+']');oAux.value=0;oAux=document.getElementById('form[SYS_GRID_AGGREGATE_'+oGrid.sGridName+'__'+aAux[2]+']');if(this.determineBrowser()=="MSIE"){oAux.innerText=0;}else{oAux.innerHTML=0;}}};this.assignFormulas=function(aFields,sEvent,iRow)
{var elem;var i=0
var j=0;for(j=0;j<=aFields.length-1;j++){i=iRow||1;while((elem=document.getElementById("form["+this.sGridName+"]["+i+"]["+aFields[j].sDependentOf+"]"))){leimnud.event.add(elem,sEvent,{method:this.evaluateFormula,instance:this,args:[elem,aFields[j]],event:true});i=i+1;}}};this.setFormulas=function(aFormulas){this.aFormulas=aFormulas;this.assignFormulas(this.aFormulas,'change');};this.evaluateFormula=function(oEvent,oDOM,oField){oDOM=(oDOM?oDOM:oEvent.target||window.event.srcElement);var aAux,sAux,i,oAux;var domId=oDOM.id;var oContinue=true;aAux=oDOM.name.split('][');sAux=oField.sFormula.replace(/\+|\-|\*|\/|\(|\)|\[|\]|\{|\}|\%|\$/g,' ');sAux=sAux.replace(/^\s+|\s+$/g,'');sAux=sAux.replace(/      /g,' ');sAux=sAux.replace(/     /g,' ');sAux=sAux.replace(/    /g,' ');sAux=sAux.replace(/   /g,' ');sAux=sAux.replace(/  /g,' ');aFields=sAux.split(' ');aFields=aFields.unique();sAux=oField.sFormula;for(i=0;i<aFields.length;i++){if(!isNumber(aFields[i])){oAux=this.getElementByName(aAux[1],aFields[i]);sAux=sAux.replace(new RegExp(aFields[i],"g"),"parseFloat(G.cleanMask(this.getElementByName("+aAux[1]+", '"+aFields[i]+"').value().replace(/[$|a-zA-Z\s]/g,'') || 0, '"+(oAux.sMask?oAux.sMask:'')
+"').result.replace(/,/g, ''))");eval("if (!document.getElementById('"+aAux[0]+']['+aAux[1]+']['+aFields[i]+"]')) { oContinue = false; }");}}
eval("if (!document.getElementById('"+aAux[0]+']['+aAux[1]+']['+oField.sFieldName+"]')) { oContinue = false; }");if(oContinue){for(i=0;i<this.aFields.length;i++){if(oField.sFieldName==this.aFields[i].sFieldName){maskformula=this.aFields[i].oProperties.mask;}}
if(maskformula!=''){maskDecimal=maskformula.split(";");if(maskDecimal.length>1){maskDecimal=maskDecimal[1].split(".");}else{maskDecimal=maskformula.split(".");}
if(typeof maskDecimal[1]!='undefined'){maskToPut=maskDecimal[1].length;}else{maskToPut=0;}}else{maskToPut=0;}
document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value='';this.executeEvent(document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']'),'keypress');eval("document.getElementById('"+aAux[0]+']['+aAux[1]+']['+oField.sFieldName+"]').value = ("+sAux+').toFixed('+maskToPut+');');document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value=document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value.replace(/^\s*|\s*$/g,"");if(document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value=='NaN')
document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value='';var symbol=document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value.replace(/[0-9.\s]/g,'');this.executeEvent(document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']'),'keypress');document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value=document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value.replace('-','');document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value=symbol+''+document.getElementById(aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']').value;if(typeof document.getElementById(domId)!='undefined'){document.getElementById(domId).focus();}
if(this.aFunctions.length>0){for(i=0;i<this.aFunctions.length;i++){oAux=document.getElementById('form['+this.sGridName+']['+aAux[1]+']['+this.aFunctions[i].sFieldName+']');if(oAux){if(oAux.name==aAux[0]+']['+aAux[1]+']['+oField.sFieldName+']'){switch(this.aFunctions[i].sFunction){case'sum':this.sum(false,oAux);break;case'avg':this.avg(false,oAux);break;}
if(oAux.fireEvent){oAux.fireEvent('onchange');}else{var evObj=document.createEvent('HTMLEvents');evObj.initEvent('change',true,true);oAux.dispatchEvent(evObj);}}}}}}else{new leimnud.module.app.alert().make({label:"Check your formula!\n\n"+oField.sFormula});}};this.deleteGridRownomsg=function(sRow){var i,iRow,iRowAux,oAux,ooAux;sRow=sRow.replace('[','');sRow=sRow.replace(']','');iRow=Number(sRow);deleteRowOnDynaform(this,iRow);iRowAux=iRow+1;while(iRowAux<=(this.oGrid.rows.length-2)){for(i=1;i<this.oGrid.rows[iRowAux-1].cells.length;i++){var oCell1=this.oGrid.rows[iRowAux-1].cells[i];var oCell2=this.oGrid.rows[iRowAux].cells[i];switch(oCell1.innerHTML.replace(/^\s+|\s+$/g,'').substr(0,6).toLowerCase()){case'<input':aObjects1=oCell1.getElementsByTagName('input');aObjects2=oCell2.getElementsByTagName('input');if(aObjects1&&aObjects2){if(aObjects1[0].type=='checkbox'){aObjects1[0].checked=aObjects2[0].checked;}
aObjects1[0].value=aObjects2[0].value;}
aObjects=oCell1.getElementsByTagName('div');if(aObjects.length>0){if(aObjects[0]){aObjects[0].id=aObjects[0].id.replace(/\[1\]/g,'\['+(this.oGrid.rows.length-2)+'\]');aObjects[0].name=aObjects[0].id.replace(/\[1\]/g,'\['+(this.oGrid.rows.length-2)+'\]');if(aObjects[0].onclick){sAux=new String(aObjects[0].onclick);eval('aObjects[0].onclick = '+sAux.replace(/\[1\]/g,'\['+(this.oGrid.rows.length-2)+'\]')+';');}}
aObjects=oCell1.getElementsByTagName('a');if(aObjects){if(aObjects[0]){if(aObjects[0].onclick){sAux=new String(aObjects[0].onclick);eval('aObjects[0].onclick = '+sAux.replace(/\[1\]/g,'\['+(this.oGrid.rows.length-2)+'\]')+';');}}}}
break;case'<selec':aObjects1=oCell1.getElementsByTagName('select');aObjects2=oCell2.getElementsByTagName('select');if(aObjects1&&aObjects2){var vValue=aObjects2[0].value;aObjects1[0].options.length=0;for(var j=0;j<aObjects2[0].options.length;j++){var optn=$dce("OPTION");optn.text=aObjects2[0].options[j].text;optn.value=aObjects2[0].options[j].value;aObjects1[0].options[j]=optn;}
aObjects1[0].value=vValue;}
break;case'<texta':aObjects1=oCell1.getElementsByTagName('textarea');aObjects2=oCell2.getElementsByTagName('textarea');if(aObjects1&&aObjects2){aObjects1[0].value=aObjects2[0].value;}
break;default:if((oCell2.innerHTML.indexOf('changeValues')==111||oCell2.innerHTML.indexOf('changeValues')==115)){break;}
if(oCell2.innerHTML.toLowerCase().indexOf('deletegridrow')==-1){oCell1.innerHTML=oCell2.innerHTML;}
break;}}
iRowAux++;}
this.oGrid.deleteRow(this.oGrid.rows.length-2);if(this.sAJAXPage!=''){}
this.aElements=[];for(var k=1;k<=this.oGrid.rows.length-2;k++){for(var i=0;i<this.aFields.length;i++){var j=k;switch(this.aFields[i].sType){case'text':this.aElements.push(new G_Text(oForm,document.getElementById('form['+this.sGridName+']['+j+']['+this.aFields[i].sFieldName+']'),this.sGridName+']['+j+']['+this.aFields[i].sFieldName));this.aElements[this.aElements.length-1].validate=this.aFields[i].oProperties.validate;if(this.aFields[i].oProperties.strTo){this.aElements[this.aElements.length-1].strTo=this.aFields[i].oProperties.strTo;}
break;case'currency':this.aElements.push(new G_Currency(oForm,document.getElementById('form['+this.sGridName+']['+j+']['+this.aFields[i].sFieldName+']'),this.sGridName+']['+j+']['+this.aFields[i].sFieldName));break;case'percentage':this.aElements.push(new G_Percentage(oForm,document.getElementById('form['+this.sGridName+']['+j+']['+this.aFields[i].sFieldName+']'),this.sGridName+']['+j+']['+this.aFields[i].sFieldName));break;case'dropdown':this.aElements.push(new G_DropDown(oForm,document.getElementById('form['+this.sGridName+']['+j+']['+this.aFields[i].sFieldName+']'),this.sGridName+']['+j+']['+this.aFields[i].sFieldName));break;}
j++;}}
if(this.aFunctions.length>0){for(i=0;i<this.aFunctions.length;i++){oAux=document.getElementById('form['+this.sGridName+'][1]['+this.aFunctions[i].sFieldName+']');if(oAux){switch(this.aFunctions[i].sFunction){case'sum':this.sum(false,oAux);break;case'avg':this.avg(false,oAux);break;}}}}
if(this.ondeleterow){this.ondeleterow();}};};function deleteRowOnDynaform(grid,sRow){var oRPC=new leimnud.module.rpc.xmlhttp({url:'../gulliver/genericAjax',args:'request=deleteGridRowOnDynaform&gridname='+grid.sGridName+'&rowpos='+sRow+'&formID='+grid.form.id});oRPC.callback=function(rpc){if(oPanel)
oPanel.loader.hide();scs=rpc.xmlhttp.responseText.extractScript();scs.evalScript();if(typeof(oDebuggerPanel)!='undefined'&&oDebuggerPanel!=null){oDebuggerPanel.clearContent();oDebuggerPanel.loader.show();var oRPC=new leimnud.module.rpc.xmlhttp({url:'cases_Ajax',args:'action=showdebug'});oRPC.callback=function(rpc){oDebuggerPanel.loader.hide();var scs=rpc.xmlhttp.responseText.extractScript();oDebuggerPanel.addContent(rpc.xmlhttp.responseText);scs.evalScript();}.extend(this);oRPC.make();}}.extend(this);oRPC.make();}
var __lastMask__;Calendar=function(){function bm(a){typeof a=="string"&&(a=document.getElementById(a));return a}function bk(a,b,c){for(c=0;c<a.length;++c)b(a[c])}function bj(){var a=document.documentElement,b=document.body;return{x:a.scrollLeft||b.scrollLeft,y:a.scrollTop||b.scrollTop,w:a.clientWidth||window.innerWidth||b.clientWidth,h:a.clientHeight||window.innerHeight||b.clientHeight}}function bi(a){var b=0,c=0,d=/^div$/i.test(a.tagName),e,f;d&&a.scrollLeft&&(b=a.scrollLeft),d&&a.scrollTop&&(c=a.scrollTop),e={x:a.offsetLeft-b,y:a.offsetTop-c},a.offsetParent&&(f=bi(a.offsetParent),e.x+=f.x,e.y+=f.y);return e}function bh(a,b){var c=e?a.clientX+document.body.scrollLeft:a.pageX,d=e?a.clientY+document.body.scrollTop:a.pageY;b&&(c-=b.x,d-=b.y);return{x:c,y:d}}function bg(a,b){var c=a.style;b!=null&&(c.display=b?"":"none");return c.display!="none"}function bf(a,b){b===""?e?a.style.filter="":a.style.opacity="":b!=null?e?a.style.filter="alpha(opacity="+b*100+")":a.style.opacity=b:e?/alpha\(opacity=([0-9.])+\)/.test(a.style.opacity)&&(b=parseFloat(RegExp.$1)/100):b=parseFloat(a.style.opacity);return b}function bd(a,b,c){function h(){var b=a.len;a.onUpdate(c/b,d),c==b&&g(),++c}function g(){b&&(clearInterval(b),b=null),a.onStop(c/a.len,d)}function f(){b&&g(),c=0,b=setInterval(h,1e3/a.fps)}function d(a,b,c,d){return d?c+a*(b-c):b+a*(c-b)}a=U(a,{fps:50,len:15,onUpdate:bl,onStop:bl}),e&&(a.len=Math.round(a.len/2)),f();return{start:f,stop:g,update:h,args:a,map:d}}function bc(a,b){if(!b(a))for(var c=a.firstChild;c;c=c.nextSibling)c.nodeType==1&&bc(c,b)}function bb(a,b){var c=ba(arguments,2);return b==undefined?function(){return a.apply(this,c.concat(ba(arguments)))}:function(){return a.apply(b,c.concat(ba(arguments)))}}function ba(a,b){b==null&&(b=0);var c,d,e;try{c=Array.prototype.slice.call(a,b)}catch(f){c=Array(a.length-b);for(d=b,e=0;d<a.length;++d,++e)c[e]=a[d]}return c}function _(a,b,c){var d=null;document.createElementNS?d=document.createElementNS("http://www.w3.org/1999/xhtml",a):d=document.createElement(a),b&&(d.className=b),c&&c.appendChild(d);return d}function $(a,b,c){if(b instanceof Array)for(var d=b.length;--d>=0;)$(a,b[d],c);else Y(b,c,a?c:null);return a}function Z(a,b){return Y(a,b,b)}function Y(a,b,c){if(a){var d=a.className.replace(/^\s+|\s+$/,"").split(/\x20/),e=[],f;for(f=d.length;f>0;)d[--f]!=b&&e.push(d[f]);c&&e.push(c),a.className=e.join(" ")}return c}function X(a){a=a||window.event,e?(a.cancelBubble=!0,a.returnValue=!1):(a.preventDefault(),a.stopPropagation());return!1}function W(a,b,c,d){if(a instanceof Array)for(var f=a.length;--f>=0;)W(a[f],b,c);else if(typeof b=="object")for(var f in b)b.hasOwnProperty(f)&&W(a,f,b[f],c);else a.removeEventListener?a.removeEventListener(b,c,e?!0:!!d):a.detachEvent?a.detachEvent("on"+b,c):a["on"+b]=null}function V(a,b,c,d){if(a instanceof Array)for(var f=a.length;--f>=0;)V(a[f],b,c,d);else if(typeof b=="object")for(var f in b)b.hasOwnProperty(f)&&V(a,f,b[f],c);else a.addEventListener?a.addEventListener(b,c,e?!0:!!d):a.attachEvent?a.attachEvent("on"+b,c):a["on"+b]=c}function U(a,b,c,d){d={};for(c in b)b.hasOwnProperty(c)&&(d[c]=b[c]);for(c in a)a.hasOwnProperty(c)&&(d[c]=a[c]);return d}function T(a){if(/\S/.test(a)){a=a.toLowerCase();function b(b){for(var c=b.length;--c>=0;)if(b[c].toLowerCase().indexOf(a)==0)return c+1}return b(L("smn"))||b(L("mn"))}}function S(a){if(a){if(typeof a=="number")return P(a);if(!(a instanceof Date)){var b=a.split(/-/);return new Date(parseInt(b[0],10),parseInt(b[1],10)-1,parseInt(b[2],10),12,0,0,0)}}return a}function R(a,b){var c=a.getMonth(),d=a.getDate(),e=a.getFullYear(),f=M(a),g=a.getDay(),h=a.getHours(),i=h>=12,j=i?h-12:h,k=N(a),l=a.getMinutes(),m=a.getSeconds(),n=/%./g,o;j===0&&(j=12),o={"%a":L("sdn")[g],"%A":L("dn")[g],"%b":L("smn")[c],"%B":L("mn")[c],"%C":1+Math.floor(e/100),"%d":d<10?"0"+d:d,"%e":d,"%H":h<10?"0"+h:h,"%I":j<10?"0"+j:j,"%j":k<10?"00"+k:k<100?"0"+k:k,"%k":h,"%l":j,"%m":c<9?"0"+(1+c):1+c,"%o":1+c,"%M":l<10?"0"+l:l,"%n":"\n","%p":i?"PM":"AM","%P":i?"pm":"am","%s":Math.floor(a.getTime()/1e3),"%S":m<10?"0"+m:m,"%t":"\t","%U":f<10?"0"+f:f,"%W":f<10?"0"+f:f,"%V":f<10?"0"+f:f,"%u":g+1,"%w":g,"%y":(""+e).substr(2,2),"%Y":e,"%%":"%"};return b.replace(n,function(a){return o.hasOwnProperty(a)?o[a]:a})}function Q(a,b,c){var d=a.getFullYear(),e=a.getMonth(),f=a.getDate(),g=b.getFullYear(),h=b.getMonth(),i=b.getDate();return d<g?-3:d>g?3:e<h?-2:e>h?2:c?0:f<i?-1:f>i?1:0}function P(a,b,c,d,e){if(!(a instanceof Date)){a=parseInt(a,10);var f=Math.floor(a/1e4);a=a%1e4;var g=Math.floor(a/100);a=a%100,a=new Date(f,g-1,a,b==null?12:b,c==null?0:c,d==null?0:d,e==null?0:e)}return a}function O(a){if(a instanceof Date)return 1e4*a.getFullYear()+100*(a.getMonth()+1)+a.getDate();if(typeof a=="string")return parseInt(a,10);return a}function N(a){a=new Date(a.getFullYear(),a.getMonth(),a.getDate(),12,0,0);var b=new Date(a.getFullYear(),0,1,12,0,0),c=a-b;return Math.floor(c/864e5)}function M(a){a=new Date(a.getFullYear(),a.getMonth(),a.getDate(),12,0,0);var b=a.getDay();a.setDate(a.getDate()-(b+6)%7+3);var c=a.valueOf();a.setMonth(0),a.setDate(4);return Math.round((c-a.valueOf())/6048e5)+1}function L(a,b){var c=i.__.data[a];b&&typeof c=="string"&&(c=K(c,b));return c}function K(a,b){return a.replace(/\$\{([^:\}]+)(:[^\}]+)?\}/g,function(a,c,d){var e=b[c],f;d&&(f=d.substr(1).split(/\s*\|\s*/),e=(e>=f.length?f[f.length-1]:f[e]).replace(/##?/g,function(a){return a.length==2?"#":e}));return e})}function J(b){if(!this._menuAnim){b=b||window.event;var c=b.target||b.srcElement,d=c.getAttribute("dyc-btn"),e=b.keyCode,f=b.charCode||e,g=H[e];if("year"==d&&e==13){var h=new Date(this.date);h.setDate(1),h.setFullYear(this._getInputYear()),this.moveTo(h,!0),z(this,!1);return X(b)}if(this._menuVisible){if(e==27){z(this,!1);return X(b)}}else{b.ctrlKey||(g=null),g==null&&!b.ctrlKey&&(g=I[e]),e==36&&(g=0);if(g!=null){y(this,g);return X(b)}f=String.fromCharCode(f).toLowerCase();var i=this.els.yearInput,j=this.selection;if(f==" "){z(this,!0),this.focus(),i.focus(),i.select();return X(b)}if(f>="0"&&f<="9"){z(this,!0),this.focus(),i.value=f,i.focus();return X(b)}var k=L("mn"),l=b.shiftKey?-1:this.date.getMonth(),m=0,n;while(++m<12){n=k[(l+m)%12].toLowerCase();if(n.indexOf(f)==0){var h=new Date(this.date);h.setDate(1),h.setMonth((l+m)%12),this.moveTo(h,!0);return X(b)}}if(e>=37&&e<=40){var h=this._lastHoverDate;if(!h&&!j.isEmpty()){h=e<39?j.getFirstDate():j.getLastDate();if(h<this._firstDateVisible||h>this._lastDateVisible)h=null}if(!h)h=e<39?this._lastDateVisible:this._firstDateVisible;else{var o=h;h=P(h);var l=100;while(l-->0){switch(e){case 37:h.setDate(h.getDate()-1);break;case 38:h.setDate(h.getDate()-7);break;case 39:h.setDate(h.getDate()+1);break;case 40:h.setDate(h.getDate()+7)}if(!this.isDisabled(h))break}h=O(h),(h<this._firstDateVisible||h>this._lastDateVisible)&&this.moveTo(h)}Y(this._getDateDiv(o),Z(this._getDateDiv(h),"DynarchCalendar-hover-date")),this._lastHoverDate=h;return X(b)}if(e==13&&this._lastHoverDate){j.type==a.SEL_MULTIPLE&&(b.shiftKey||b.ctrlKey)?(b.shiftKey&&this._selRangeStart&&(j.clear(!0),j.selectRange(this._selRangeStart,this._lastHoverDate)),b.ctrlKey&&j.set(this._selRangeStart=this._lastHoverDate,!0)):j.reset(this._selRangeStart=this._lastHoverDate);return X(b)}e==27&&!this.args.cont&&this.hide()}}}function G(){this.refresh();var a=this.inputField,b=this.selection;if(a){var c=b.print(__lastMask__||this.dateFormat);/input|textarea/i.test(a.tagName)?a.value=c:a.innerHTML=c}this.callHooks("onSelect",this,b)}function F(a){a=a||window.event;var b=C(a);if(b){var c=b.getAttribute("dyc-btn"),d=b.getAttribute("dyc-type"),e=a.wheelDelta?a.wheelDelta/120:-a.detail/3;e=e<0?-1:e>0?1:0,this.args.reverseWheel&&(e=-e);if(/^(time-(hour|min))/.test(d)){switch(RegExp.$1){case"time-hour":this.setHours(this.getHours()+e);break;case"time-min":this.setMinutes(this.getMinutes()+this.args.minuteStep*e)}X(a)}else/Y/i.test(c)&&(e*=2),y(this,-e),X(a)}}function E(a,b){b=b||window.event;var c=C(b);if(c){var d=c.getAttribute("dyc-type");if(d&&!c.getAttribute("disabled"))if(!a||!this._bodyAnim||d!="date"){var e=c.getAttribute("dyc-cls");e=e?D(e,0):"DynarchCalendar-hover-"+d,(d!="date"||this.selection.type)&&$(a,c,e),d=="date"&&($(a,c.parentNode.parentNode,"DynarchCalendar-hover-week"),this._showTooltip(c.getAttribute("dyc-date"))),/^time-hour/.test(d)&&$(a,this.els.timeHour,"DynarchCalendar-hover-time"),/^time-min/.test(d)&&$(a,this.els.timeMinute,"DynarchCalendar-hover-time"),Y(this._getDateDiv(this._lastHoverDate),"DynarchCalendar-hover-date"),this._lastHoverDate=null}}a||this._showTooltip()}function D(a,b){return"DynarchCalendar-"+a.split(/,/)[b]}function C(a){var b=a.target||a.srcElement,c=b;while(b&&b.getAttribute&&!b.getAttribute("dyc-type"))b=b.parentNode;return b.getAttribute&&b||c}function B(a){a=a||window.event;var b=this.els.topCont.style,c=bh(a,this._mouseDiff);b.left=c.x+"px",b.top=c.y+"px"}function A(b,c){c=c||window.event;var d=C(c);if(d&&!d.getAttribute("disabled")){var f=d.getAttribute("dyc-btn"),g=d.getAttribute("dyc-type"),h=d.getAttribute("dyc-date"),i=this.selection,j,k={mouseover:X,mousemove:X,mouseup:function(a){var b=d.getAttribute("dyc-cls");b&&Y(d,D(b,1)),clearTimeout(j),W(document,k,!0),k=null}};if(b){setTimeout(bb(this.focus,this),1);var l=d.getAttribute("dyc-cls");l&&Z(d,D(l,1));if("menu"==f)this.toggleMenu();else if(d&&/^[+-][MY]$/.test(f))if(y(this,f)){var m=bb(function(){y(this,f,!0)?j=setTimeout(m,40):(k.mouseup(),y(this,f))},this);j=setTimeout(m,350),V(document,k,!0)}else k.mouseup();else if("year"==f)this.els.yearInput.focus(),this.els.yearInput.select();else if(g=="time-am")V(document,k,!0);else if(/^time/.test(g)){var m=bb(function(a){w.call(this,a),j=setTimeout(m,100)},this,g);w.call(this,g),j=setTimeout(m,350),V(document,k,!0)}else h&&i.type&&(i.type==a.SEL_MULTIPLE?c.shiftKey&&this._selRangeStart?i.selectRange(this._selRangeStart,h):(!c.ctrlKey&&!i.isSelected(h)&&i.clear(!0),i.set(h,!0),this._selRangeStart=h):(i.set(h),this.moveTo(P(h),2)),d=this._getDateDiv(h),E.call(this,!0,{target:d})),V(document,k,!0);e&&k&&/dbl/i.test(c.type)&&k.mouseup(),!this.args.fixed&&/^(DynarchCalendar-(topBar|bottomBar|weekend|weekNumber|menu(-sep)?))?$/.test(d.className)&&!this.args.cont&&(k.mousemove=bb(B,this),this._mouseDiff=bh(c,bi(this.els.topCont)),V(document,k,!0))}else if("today"==f)!this._menuVisible&&i.type==a.SEL_SINGLE&&i.set(new Date),this.moveTo(new Date,!0),z(this,!1);else if(/^m([0-9]+)/.test(f)){var h=new Date(this.date);h.setDate(1),h.setMonth(RegExp.$1),h.setFullYear(this._getInputYear()),this.moveTo(h,!0),z(this,!1)}else g=="time-am"&&this.setHours(this.getHours()+12);e||X(c)}}function z(a,b){a._menuVisible=b,$(b,a.els.title,"DynarchCalendar-pressed-title");var c=a.els.menu;f&&(c.style.height=a.els.main.offsetHeight+"px");if(!a.args.animation)bg(c,b),a.focused&&a.focus();else{a._menuAnim&&a._menuAnim.stop();var d=a.els.main.offsetHeight;f&&(c.style.width=a.els.topBar.offsetWidth+"px"),b&&(c.firstChild.style.marginTop=-d+"px",a.args.opacity>0&&bf(c,0),bg(c,!0)),a._menuAnim=bd({onUpdate:function(e,f){c.firstChild.style.marginTop=f(be.accel_b(e),-d,0,!b)+"px",a.args.opacity>0&&bf(c,f(be.accel_b(e),0,.85,!b))},onStop:function(){a.args.opacity>0&&bf(c,.85),c.firstChild.style.marginTop="",a._menuAnim=null,b||(bg(c,!1),a.focused&&a.focus())}})}}function y(a,b,c){this._bodyAnim&&this._bodyAnim.stop();var d;if(b!=0){d=new Date(a.date),d.setDate(1);switch(b){case"-Y":case-2:d.setFullYear(d.getFullYear()-1);break;case"+Y":case 2:d.setFullYear(d.getFullYear()+1);break;case"-M":case-1:d.setMonth(d.getMonth()-1);break;case"+M":case 1:d.setMonth(d.getMonth()+1)}}else d=new Date;return a.moveTo(d,!c)}function w(a){switch(a){case"time-hour+":this.setHours(this.getHours()+1);break;case"time-hour-":this.setHours(this.getHours()-1);break;case"time-min+":this.setMinutes(this.getMinutes()+this.args.minuteStep);break;case"time-min-":this.setMinutes(this.getMinutes()-this.args.minuteStep);break;default:return}}function v(){this._bluringTimeout=setTimeout(bb(u,this),50)}function u(){this.focused=!1,Y(this.els.main,"DynarchCalendar-focused"),this._menuVisible&&z(this,!1),this.args.cont||this.hide(),this.callHooks("onBlur",this)}function t(){this._bluringTimeout&&clearTimeout(this._bluringTimeout),this.focused=!0,Z(this.els.main,"DynarchCalendar-focused"),this.callHooks("onFocus",this)}function s(a){var b=_("div"),c=a.els={},d={mousedown:bb(A,a,!0),mouseup:bb(A,a,!1),mouseover:bb(E,a,!0),mouseout:bb(E,a,!1),keypress:bb(J,a)};a.args.noScroll||(d[g?"DOMMouseScroll":"mousewheel"]=bb(F,a)),e&&(d.dblclick=d.mousedown,d.keydown=d.keypress),b.innerHTML=m(a),bc(b.firstChild,function(a){var b=r[a.className];b&&(c[b]=a),e&&a.setAttribute("unselectable","on")}),V(c.main,d),V([c.focusLink,c.yearInput],a._focusEvents={focus:bb(t,a),blur:bb(v,a)}),a.moveTo(a.date,!1),a.setTime(null,!0);return c.topCont}function q(a){function d(){c.showTime&&(b.push("<td>"),p(a,b),b.push("</td>"))}var b=[],c=a.args;b.push("<table",j," style='width:100%'><tr>"),c.timePos=="left"&&d(),c.bottomBar&&(b.push("<td>"),b.push("<table",j,"><tr><td>","<div dyc-btn='today' dyc-cls='hover-bottomBar-today,pressed-bottomBar-today' dyc-type='bottomBar-today' ","class='DynarchCalendar-bottomBar-today'>",L("today"),"</div>","</td></tr></table>"),b.push("</td>")),c.timePos=="right"&&d(),b.push("</tr></table>");return b.join("")}function p(a,b){b.push("<table class='DynarchCalendar-time'"+j+"><tr>","<td rowspan='2'><div dyc-type='time-hour' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-hour'></div></td>","<td dyc-type='time-hour+' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-up'></td>","<td rowspan='2' class='DynarchCalendar-time-sep'></td>","<td rowspan='2'><div dyc-type='time-min' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-minute'></div></td>","<td dyc-type='time-min+' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-up'></td>"),a.args.showTime==12&&b.push("<td rowspan='2' class='DynarchCalendar-time-sep'></td>","<td rowspan='2'><div class='DynarchCalendar-time-am' dyc-type='time-am' dyc-cls='hover-time,pressed-time'></div></td>"),b.push("</tr><tr>","<td dyc-type='time-hour-' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-down'></td>","<td dyc-type='time-min-' dyc-cls='hover-time,pressed-time' class='DynarchCalendar-time-down'></td>","</tr></table>")}function o(a){var b=["<table height='100%'",j,"><tr><td>","<table style='margin-top: 1.5em'",j,">","<tr><td colspan='3'><input dyc-btn='year' class='DynarchCalendar-menu-year' size='6' value='",a.date.getFullYear(),"' /></td></tr>","<tr><td><div dyc-type='menubtn' dyc-cls='hover-navBtn,pressed-navBtn' dyc-btn='today'>",L("goToday"),"</div></td></tr>","</table>","<p class='DynarchCalendar-menu-sep'>&nbsp;</p>","<table class='DynarchCalendar-menu-mtable'",j,">"],c=L("smn"),d=0,e=b.length,f;while(d<12){b[e++]="<tr>";for(f=4;--f>0;)b[e++]="<td><div dyc-type='menubtn' dyc-cls='hover-navBtn,pressed-navBtn' dyc-btn='m"+d+"' class='DynarchCalendar-menu-month'>"+c[d++]+"</div></td>";b[e++]="</tr>"}b[e++]="</table></td></tr></table>";return b.join("")}function n(a){return"<div unselectable='on'>"+R(a.date,a.args.titleFormat)+"</div>"}function m(a){var b=["<table class='DynarchCalendar-topCont'",j,"><tr><td>","<div class='DynarchCalendar'>",e?"<a class='DynarchCalendar-focusLink' href='#'></a>":"<button class='DynarchCalendar-focusLink'></button>","<div class='DynarchCalendar-topBar'>","<div dyc-type='nav' dyc-btn='-Y' dyc-cls='hover-navBtn,pressed-navBtn' ","class='DynarchCalendar-navBtn DynarchCalendar-prevYear'><div></div></div>","<div dyc-type='nav' dyc-btn='+Y' dyc-cls='hover-navBtn,pressed-navBtn' ","class='DynarchCalendar-navBtn DynarchCalendar-nextYear'><div></div></div>","<div dyc-type='nav' dyc-btn='-M' dyc-cls='hover-navBtn,pressed-navBtn' ","class='DynarchCalendar-navBtn DynarchCalendar-prevMonth'><div></div></div>","<div dyc-type='nav' dyc-btn='+M' dyc-cls='hover-navBtn,pressed-navBtn' ","class='DynarchCalendar-navBtn DynarchCalendar-nextMonth'><div></div></div>","<table class='DynarchCalendar-titleCont'",j,"><tr><td>","<div dyc-type='title' dyc-btn='menu' dyc-cls='hover-title,pressed-title' class='DynarchCalendar-title'>",n(a),"</div></td></tr></table>","<div class='DynarchCalendar-dayNames'>",k(a),"</div>","</div>","<div class='DynarchCalendar-body'></div>"];(a.args.bottomBar||a.args.showTime)&&b.push("<div class='DynarchCalendar-bottomBar'>",q(a),"</div>"),b.push("<div class='DynarchCalendar-menu' style='display: none'>",o(a),"</div>","<div class='DynarchCalendar-tooltip'></div>","</div>","</td></tr></table>");return b.join("")}function l(a,b,c){b=b||a.date,c=c||a.fdow,b=new Date(b.getFullYear(),b.getMonth(),b.getDate(),12,0,0,0);var d=b.getMonth(),e=[],f=0,g=a.args.weekNumbers;b.setDate(1);var h=(b.getDay()-c)%7;h<0&&(h+=7),b.setDate(0-h),b.setDate(b.getDate()+1);var i=new Date,k=i.getDate(),l=i.getMonth(),m=i.getFullYear();e[f++]="<table class='DynarchCalendar-bodyTable'"+j+">";for(var n=0;n<6;++n){e[f++]="<tr class='DynarchCalendar-week",n==0&&(e[f++]=" DynarchCalendar-first-row"),n==5&&(e[f++]=" DynarchCalendar-last-row"),e[f++]="'>",g&&(e[f++]="<td class='DynarchCalendar-first-col'><div class='DynarchCalendar-weekNumber'>"+M(b)+"</div></td>");for(var o=0;o<7;++o){var p=b.getDate(),q=b.getMonth(),r=b.getFullYear(),s=1e4*r+100*(q+1)+p,t=a.selection.isSelected(s),u=a.isDisabled(b);e[f++]="<td class='",o==0&&!g&&(e[f++]=" DynarchCalendar-first-col"),o==0&&n==0&&(a._firstDateVisible=s),o==6&&(e[f++]=" DynarchCalendar-last-col",n==5&&(a._lastDateVisible=s)),t&&(e[f++]=" DynarchCalendar-td-selected"),e[f++]="'><div dyc-type='date' unselectable='on' dyc-date='"+s+"' ",u&&(e[f++]="disabled='1' "),e[f++]="class='DynarchCalendar-day",L("weekend").indexOf(b.getDay())>=0&&(e[f++]=" DynarchCalendar-weekend"),q!=d&&(e[f++]=" DynarchCalendar-day-othermonth"),p==k&&q==l&&r==m&&(e[f++]=" DynarchCalendar-day-today"),u&&(e[f++]=" DynarchCalendar-day-disabled"),t&&(e[f++]=" DynarchCalendar-day-selected"),u=a.args.dateInfo(b),u&&u.klass&&(e[f++]=" "+u.klass),e[f++]="'>"+p+"</div></td>",b=new Date(r,q,p+1,12,0,0,0)}e[f++]="</tr>"}e[f++]="</table>";return e.join("")}function k(a){var b=["<table",j,"><tr>"],c=0;a.args.weekNumbers&&b.push("<td><div class='DynarchCalendar-weekNumber'>",L("wk"),"</div></td>");while(c<7){var d=(c+++a.fdow)%7;b.push("<td><div",L("weekend").indexOf(d)>=0?" class='DynarchCalendar-weekend'>":">",L("sdn")[d],"</div></td>")}b.push("</tr></table>");return b.join("")}function a(b){b=b||{},this.args=b=U(b,{animation:!f,cont:null,bottomBar:!0,date:!0,fdow:L("fdow"),min:null,max:null,reverseWheel:!1,selection:[],selectionType:a.SEL_SINGLE,weekNumbers:!1,align:"Bl/ / /T/r",inputField:null,trigger:null,dateFormat:"%Y-%m-%d",fixed:!1,opacity:e?1:3,titleFormat:"%b %Y",showTime:!1,timePos:"right",time:!0,minuteStep:5,noScroll:!1,disabled:bl,checkRange:!1,dateInfo:bl,onChange:bl,onSelect:bl,onTimeChange:bl,onFocus:bl,onBlur:bl}),this.handlers={};var c=this,d=new Date;b.min=S(b.min),b.max=S(b.max),b.date===!0&&(b.date=d),b.time===!0&&(b.time=d.getHours()*100+Math.floor(d.getMinutes()/b.minuteStep)*b.minuteStep),this.date=S(b.date),this.time=b.time,this.fdow=b.fdow,bk("onChange onSelect onTimeChange onFocus onBlur".split(/\s+/),function(a){var d=b[a];d instanceof Array||(d=[d]),c.handlers[a]=d}),this.selection=new a.Selection(b.selection,b.selectionType,G,this);var g=s(this);b.cont&&bm(b.cont).appendChild(g),b.trigger&&this.manageFields(b.trigger,b.inputField,b.dateFormat)}var b=navigator.userAgent,c=/opera/i.test(b),d=/Konqueror|Safari|KHTML/i.test(b),e=/msie/i.test(b)&&!c&&!/mac_powerpc/i.test(b),f=e&&/msie 6/i.test(b),g=/gecko/i.test(b)&&!d&&!c&&!e,h=a.prototype,i=a.I18N={};a.SEL_NONE=0,a.SEL_SINGLE=1,a.SEL_MULTIPLE=2,a.SEL_WEEK=3,a.dateToInt=O,a.intToDate=P,a.printDate=R,a.formatString=K,a.i18n=L,a.LANG=function(a,b,c){i.__=i[a]={name:b,data:c}},a.setup=function(b){return new a(b)},h.moveTo=function(a,b){var c=this;a=S(a);var d=Q(a,c.date,!0),e,f=c.args,g=f.min&&Q(a,f.min),h=f.max&&Q(a,f.max);f.animation||(b=!1),$(g!=null&&g<=1,[c.els.navPrevMonth,c.els.navPrevYear],"DynarchCalendar-navDisabled"),$(h!=null&&h>=-1,[c.els.navNextMonth,c.els.navNextYear],"DynarchCalendar-navDisabled"),g<-1&&(a=f.min,e=1,d=0),h>1&&(a=f.max,e=2,d=0),c.date=a,c.refresh(!!b),c.callHooks("onChange",c,a,b);if(b&&(d!=0||b!=2)){c._bodyAnim&&c._bodyAnim.stop();var i=c.els.body,j=_("div","DynarchCalendar-animBody-"+x[d],i),k=i.firstChild,m=bf(k)||.7,n=e?be.brakes:d==0?be.shake:be.accel_ab2,o=d*d>4,p=o?k.offsetTop:k.offsetLeft,q=j.style,r=o?i.offsetHeight:i.offsetWidth;d<0?r+=p:d>0?r=p-r:(r=Math.round(r/7),e==2&&(r=-r));if(!e&&d!=0){var s=j.cloneNode(!0),t=s.style,u=2*r;s.appendChild(k.cloneNode(!0)),t[o?"marginTop":"marginLeft"]=r+"px",i.appendChild(s)}k.style.visibility="hidden",j.innerHTML=l(c),c._bodyAnim=bd({onUpdate:function(a,b){var f=n(a);if(s)var g=b(f,r,u)+"px";if(e)q[o?"marginTop":"marginLeft"]=b(f,r,0)+"px";else{if(o||d==0)q.marginTop=b(d==0?n(a*a):f,0,r)+"px",d!=0&&(t.marginTop=g);if(!o||d==0)q.marginLeft=b(f,0,r)+"px",d!=0&&(t.marginLeft=g)}c.args.opacity>2&&s&&(bf(s,1-f),bf(j,f))},onStop:function(b){i.innerHTML=l(c,a),c._bodyAnim=null}})}c._lastHoverDate=null;return g>=-1&&h<=1},h.isDisabled=function(a){var b=this.args;return b.min&&Q(a,b.min)<0||b.max&&Q(a,b.max)>0||b.disabled(a)},h.toggleMenu=function(){z(this,!this._menuVisible)},h.refresh=function(a){var b=this.els;a||(b.body.innerHTML=l(this)),b.title.innerHTML=n(this),b.yearInput.value=this.date.getFullYear()},h.redraw=function(){var a=this,b=a.els;a.refresh(),b.dayNames.innerHTML=k(a),b.menu.innerHTML=o(a),b.bottomBar&&(b.bottomBar.innerHTML=q(a)),bc(b.topCont,function(c){var d=r[c.className];d&&(b[d]=c),c.className=="DynarchCalendar-menu-year"?(V(c,a._focusEvents),b.yearInput=c):e&&c.setAttribute("unselectable","on")}),a.setTime(null,!0)},h.setLanguage=function(b){var c=a.setLanguage(b);c&&(this.fdow=c.data.fdow,this.redraw())},a.setLanguage=function(a){var b=i[a];b&&(i.__=b);return b},h.focus=function(){try{this.els[this._menuVisible?"yearInput":"focusLink"].focus()}catch(a){}t.call(this)},h.blur=function(){this.els.focusLink.blur(),this.els.yearInput.blur(),u.call(this)},h.showAt=function(a,b,c){this._showAnim&&this._showAnim.stop(),c=c&&this.args.animation;var d=this.els.topCont,e=this,f=this.els.body.firstChild,g=f.offsetHeight,h=d.style;h.position="absolute",h.left=a+"px",h.top=b+"px",h.zIndex=1e4,h.display="",c&&(f.style.marginTop=-g+"px",this.args.opacity>1&&bf(d,0),this._showAnim=bd({onUpdate:function(a,b){f.style.marginTop=-b(be.accel_b(a),g,0)+"px",e.args.opacity>1&&bf(d,a)},onStop:function(){e.args.opacity>1&&bf(d,""),e._showAnim=null}}))},h.hide=function(){var a=this.els.topCont,b=this,c=this.els.body.firstChild,d=c.offsetHeight,e=bi(a).y;this.args.animation?(this._showAnim&&this._showAnim.stop(),this._showAnim=bd({onUpdate:function(f,g){b.args.opacity>1&&bf(a,1-f),c.style.marginTop=-g(be.accel_b(f),0,d)+"px",a.style.top=g(be.accel_ab(f),e,e-10)+"px"},onStop:function(){a.style.display="none",c.style.marginTop="",b.args.opacity>1&&bf(a,""),b._showAnim=null}})):a.style.display="none",this.inputField=null},h.popup=function(a,b){function h(b){var c={x:i.x,y:i.y};if(!b)return c;/B/.test(b)&&(c.y+=a.offsetHeight),/b/.test(b)&&(c.y+=a.offsetHeight-f.y),/T/.test(b)&&(c.y-=f.y),/l/.test(b)&&(c.x-=f.x-a.offsetWidth),/L/.test(b)&&(c.x-=f.x),/R/.test(b)&&(c.x+=a.offsetWidth),/c/i.test(b)&&(c.x+=(a.offsetWidth-f.x)/2),/m/i.test(b)&&(c.y+=(a.offsetHeight-f.y)/2);return c}a=bm(a),b||(b=this.args.align),b=b.split(/\x2f/);var c=bi(a),d=this.els.topCont,e=d.style,f,g=bj();e.visibility="hidden",e.display="",this.showAt(0,0),document.body.appendChild(d),f={x:d.offsetWidth,y:d.offsetHeight};var i=c;i=h(b[0]),i.y<g.y&&(i.y=c.y,i=h(b[1])),i.x+f.x>g.x+g.w&&(i.x=c.x,i=h(b[2])),i.y+f.y>g.y+g.h&&(i.y=c.y,i=h(b[3])),i.x<g.x&&(i.x=c.x,i=h(b[4])),this.showAt(i.x,i.y,!0),e.visibility="",this.focus()},h.manageFields=function(b,c,d){var e=this;c=bm(c),b=bm(b),/^button$/i.test(b.tagName)&&b.setAttribute("type","button"),V(b,"click",function(){e.inputField=c,e.dateFormat=d;if(e.selection.type==a.SEL_SINGLE){var f,g,h,i;f=/input|textarea/i.test(c.tagName)?c.value:c.innerText||c.textContent,f&&(g=/(^|[^%])%[bBmo]/.exec(d),h=/(^|[^%])%[de]/.exec(d),g&&h&&(i=g.index<h.index),f=Calendar.parseDate(f,i),f&&(e.selection.set(f,!1,!0),e.args.showTime&&(e.setHours(f.getHours()),e.setMinutes(f.getMinutes())),e.moveTo(f)))}e.popup(b)})},h.callHooks=function(a){var b=ba(arguments,1),c=this.handlers[a],d=0;for(;d<c.length;++d)c[d].apply(this,b)},h.addEventListener=function(a,b){this.handlers[a].push(b)},h.removeEventListener=function(a,b){var c=this.handlers[a],d=c.length;while(--d>=0)c[d]===b&&c.splice(d,1)},h.getTime=function(){return this.time},h.setTime=function(a,b){if(this.args.showTime){a=a!=null?a:this.time,this.time=a;var c=this.getHours(),d=this.getMinutes(),e=c<12;this.args.showTime==12&&(c==0&&(c=12),c>12&&(c-=12),this.els.timeAM.innerHTML=L(e?"AM":"PM")),c<10&&(c="0"+c),d<10&&(d="0"+d),this.els.timeHour.innerHTML=c,this.els.timeMinute.innerHTML=d,b||this.callHooks("onTimeChange",this,a)}},h.getHours=function(){return Math.floor(this.time/100)},h.getMinutes=function(){return this.time%100},h.setHours=function(a){a<0&&(a+=24),this.setTime(100*(a%24)+this.time%100)},h.setMinutes=function(a){a<0&&(a+=60),a=Math.floor(a/this.args.minuteStep)*this.args.minuteStep,this.setTime(100*this.getHours()+a%60)},h._getInputYear=function(){var a=parseInt(this.els.yearInput.value,10);isNaN(a)&&(a=this.date.getFullYear());return a},h._showTooltip=function(a){var b="",c,d=this.els.tooltip;a&&(a=P(a),c=this.args.dateInfo(a),c&&c.tooltip&&(b="<div class='DynarchCalendar-tooltipCont'>"+R(a,c.tooltip)+"</div>")),d.innerHTML=b};var j=" align='center' cellspacing='0' cellpadding='0'",r={"DynarchCalendar-topCont":"topCont","DynarchCalendar-focusLink":"focusLink",DynarchCalendar:"main","DynarchCalendar-topBar":"topBar","DynarchCalendar-title":"title","DynarchCalendar-dayNames":"dayNames","DynarchCalendar-body":"body","DynarchCalendar-menu":"menu","DynarchCalendar-menu-year":"yearInput","DynarchCalendar-bottomBar":"bottomBar","DynarchCalendar-tooltip":"tooltip","DynarchCalendar-time-hour":"timeHour","DynarchCalendar-time-minute":"timeMinute","DynarchCalendar-time-am":"timeAM","DynarchCalendar-navBtn DynarchCalendar-prevYear":"navPrevYear","DynarchCalendar-navBtn DynarchCalendar-nextYear":"navNextYear","DynarchCalendar-navBtn DynarchCalendar-prevMonth":"navPrevMonth","DynarchCalendar-navBtn DynarchCalendar-nextMonth":"navNextMonth"},x={"-3":"backYear","-2":"back",0:"now",2:"fwd",3:"fwdYear"},H={37:-1,38:-2,39:1,40:2},I={33:-1,34:1};h._getDateDiv=function(a){var b=null;if(a)try{bc(this.els.body,function(c){if(c.getAttribute("dyc-date")==a)throw b=c})}catch(c){}return b},(a.Selection=function(a,b,c,d){this.type=b,this.sel=a instanceof Array?a:[a],this.onChange=bb(c,d),this.cal=d}).prototype={get:function(){return this.type==a.SEL_SINGLE?this.sel[0]:this.sel},isEmpty:function(){return this.sel.length==0},set:function(b,c,d){var e=this.type==a.SEL_SINGLE;b instanceof Array?(this.sel=b,this.normalize(),d||this.onChange(this)):(b=O(b),e||!this.isSelected(b)?(e?this.sel=[b]:this.sel.splice(this.findInsertPos(b),0,b),this.normalize(),d||this.onChange(this)):c&&this.unselect(b,d))},reset:function(){this.sel=[],this.set.apply(this,arguments)},countDays:function(){var a=0,b=this.sel,c=b.length,d,e,f;while(--c>=0)d=b[c],d instanceof Array&&(e=P(d[0]),f=P(d[1]),a+=Math.round(Math.abs(f.getTime()-e.getTime())/864e5)),++a;return a},unselect:function(a,b){a=O(a);var c=!1;for(var d=this.sel,e=d.length,f;--e>=0;){f=d[e];if(f instanceof Array){if(a>=f[0]&&a<=f[1]){var g=P(a),h=g.getDate();if(a==f[0])g.setDate(h+1),f[0]=O(g),c=!0;else if(a==f[1])g.setDate(h-1),f[1]=O(g),c=!0;else{var i=new Date(g);i.setDate(h+1),g.setDate(h-1),d.splice(e+1,0,[O(i),f[1]]),f[1]=O(g),c=!0}}}else a==f&&(d.splice(e,1),c=!0)}c&&(this.normalize(),b||this.onChange(this))},normalize:function(){this.sel=this.sel.sort(function(a,b){a instanceof Array&&(a=a[0]),b instanceof Array&&(b=b[0]);return a-b});for(var a=this.sel,b=a.length,c,d;--b>=0;){c=a[b];if(c instanceof Array){if(c[0]>c[1]){a.splice(b,1);continue}c[0]==c[1]&&(c=a[b]=c[0])}if(d){var e=d,f=c instanceof Array?c[1]:c;f=P(f),f.setDate(f.getDate()+1),f=O(f);if(f>=e){var g=a[b+1];c instanceof Array&&g instanceof Array?(c[1]=g[1],a.splice(b+1,1)):c instanceof Array?(c[1]=d,a.splice(b+1,1)):g instanceof Array?(g[0]=c,a.splice(b,1)):(a[b]=[c,g],a.splice(b+1,1))}}d=c instanceof Array?c[0]:c}},findInsertPos:function(a){for(var b=this.sel,c=b.length,d;--c>=0;){d=b[c],d instanceof Array&&(d=d[0]);if(d<=a)break}return c+1},clear:function(a){this.sel=[],a||this.onChange(this)},selectRange:function(b,c){b=O(b),c=O(c);if(b>c){var d=b;b=c,c=d}var e=this.cal.args.checkRange;if(!e)return this._do_selectRange(b,c);try{bk((new a.Selection([[b,c]],a.SEL_MULTIPLE,bl)).getDates(),bb(function(a){if(this.isDisabled(a)){e instanceof Function&&e(a,this);throw"OUT"}},this.cal)),this._do_selectRange(b,c)}catch(f){}},_do_selectRange:function(a,b){this.sel.push([a,b]),this.normalize(),this.onChange(this)},isSelected:function(a){for(var b=this.sel.length,c;--b>=0;){c=this.sel[b];if(c instanceof Array&&a>=c[0]&&a<=c[1]||a==c)return!0}return!1},getFirstDate:function(){var a=this.sel[0];a&&a instanceof Array&&(a=a[0]);return a},getLastDate:function(){if(this.sel.length>0){var a=this.sel[this.sel.length-1];a&&a instanceof Array&&(a=a[1]);return a}},print:function(a,b){var c=[],d=0,e,f=this.cal.getHours(),g=this.cal.getMinutes();b||(b=" -> ");while(d<this.sel.length)e=this.sel[d++],e instanceof Array?c.push(R(P(e[0],f,g),a)+b+R(P(e[1],f,g),a)):c.push(R(P(e,f,g),a));return c},getDates:function(a){var b=[],c=0,d,e;while(c<this.sel.length){e=this.sel[c++];if(e instanceof Array){d=P(e[0]),e=e[1];while(O(d)<e)b.push(a?R(d,a):new Date(d)),d.setDate(d.getDate()+1)}else d=P(e);b.push(a?R(d,a):d)}return b}},a.isUnicodeLetter=function(a){return a.toUpperCase()!=a.toLowerCase()},a.parseDate=function(b,c,d){if(!/\S/.test(b))return"";b=b.replace(/^\s+/,"").replace(/\s+$/,""),d=d||new Date;var e=null,f=null,g=null,h=null,i=null,j=null,k=b.match(/([0-9]{1,2}):([0-9]{1,2})(:[0-9]{1,2})?\s*(am|pm)?/i);k&&(h=parseInt(k[1],10),i=parseInt(k[2],10),j=k[3]?parseInt(k[3].substr(1),10):0,b=b.substring(0,k.index)+b.substr(k.index+k[0].length),k[4]&&(k[4].toLowerCase()=="pm"&&h<12?h+=12:k[4].toLowerCase()=="am"&&h>=12&&(h-=12)));var l=function(){function k(a){d.push(a)}function j(){var a="";while(g()&&/[0-9]/.test(g()))a+=f();if(h(g()))return i(a);return parseInt(a,10)}function i(a){while(g()&&h(g()))a+=f();return a}function g(){return b.charAt(c)}function f(){return b.charAt(c++)}var c=0,d=[],e,h=a.isUnicodeLetter;while(c<b.length)e=g(),h(e)?k(i("")):/[0-9]/.test(e)?k(j()):f();return d}(),m=[];for(var n=0;n<l.length;++n){var o=l[n];/^[0-9]{4}$/.test(o)?(e=parseInt(o,10),f==null&&g==null&&c==null&&(c=!0)):/^[0-9]{1,2}$/.test(o)?(o=parseInt(o,10),o<60?o<0||o>12?o>=1&&o<=31&&(g=o):m.push(o):e=o):f==null&&(f=T(o))}m.length<2?m.length==1&&(g==null?g=m.shift():f==null&&(f=m.shift())):c?(f==null&&(f=m.shift()),g==null&&(g=m.shift())):(g==null&&(g=m.shift()),f==null&&(f=m.shift())),e==null&&(e=m.length>0?m.shift():d.getFullYear()),e<30?e+=2e3:e<99&&(e+=1900),f==null&&(f=d.getMonth()+1);return e!=null&&f!=null&&g!=null?new Date(e,f-1,g,h,i,j):null};var be={elastic_b:function(a){return 1-Math.cos(-a*5.5*Math.PI)/Math.pow(2,7*a)},magnetic:function(a){return 1-Math.cos(a*a*a*10.5*Math.PI)/Math.exp(4*a)},accel_b:function(a){a=1-a;return 1-a*a*a*a},accel_a:function(a){return a*a*a},accel_ab:function(a){a=1-a;return 1-Math.sin(a*a*Math.PI/2)},accel_ab2:function(a){return(a/=.5)<1?.5*a*a:-0.5*(--a*(a-2)-1)},brakes:function(a){a=1-a;return 1-Math.sin(a*a*Math.PI)},shake:function(a){return a<.5?-Math.cos(a*11*Math.PI)*a*a:(a=1-a,Math.cos(a*11*Math.PI)*a*a)}},bl=new Function;return a}()
var Static_AutosuggestResponseData;var swStoreEntry=1;if(typeof(bsn)=="undefined")
_b=bsn={};if(typeof(_b.Autosuggest)=="undefined")
_b.Autosuggest={};else
alert("Autosuggest is already set!");_b.AutoSuggest=function(id,param)
{if(!document.getElementById)
return 0;this.fld=_b.DOM.gE(id);if(!this.fld)
return 0;this.sInp="";this.nInpC=0;this.aSug=[];this.iHigh=0;this.oP=param?param:{};var k,def={minchars:1,meth:"get",varname:"input",className:"autosuggest",timeout:5000,delay:50,offsety:-5,shownoresults:true,noresults:"No results!",maxheight:250,cache:true,maxentries:25};for(k in def)
{if(typeof(this.oP[k])!=typeof(def[k]))
this.oP[k]=def[k];}
var p=this;this.fld.onkeypress=function(ev){return p.onKeyPress(ev);};this.fld.onkeyup=function(ev){return p.onKeyUp(ev);};this.fld.setAttribute("autocomplete","off");};_b.AutoSuggest.prototype.onKeyPress=function(ev)
{var key=(window.event)?window.event.keyCode:ev.keyCode;var RETURN=13;var TAB=9;var ESC=27;var bubble=1;switch(key){case RETURN:if(typeof this.oP.storeEntryData!="undefined"&&this.oP.storeEntryData[0]==1){var elem=document.getElementById(this.oP.storeEntryData[1]);if(elem.value!=""&&swStoreEntry==1&&typeof Static_AutosuggestResponseData!="undefined"&&Static_AutosuggestResponseData.results.length>0){for(var i=0;i<=Static_AutosuggestResponseData.results.length-1;i++){if(Static_AutosuggestResponseData.results[i].value==elem.value){swStoreEntry=0;}}}
if(elem.value==""){swStoreEntry=0;}
if(swStoreEntry==1){storeEntryProcessAjax(elem,this.oP.storeEntryData[2],this.oP.storeEntryData[3],this.oP.storeEntryData[4],this.oP.storeEntryData[5],this.oP.storeEntryData[6]);}}
this.setHighlightedValue();bubble=0;return false;break;case ESC:this.clearSuggestions();break;default:swStoreEntry=1;break;}
return bubble;};_b.AutoSuggest.prototype.onKeyUp=function(ev)
{var key=(window.event)?window.event.keyCode:ev.keyCode;var ARRUP=38;var ARRDN=40;var bubble=1;switch(key){case ARRUP:this.changeHighlight(key);this.setHighlightedValue2();bubble=0;swStoreEntry=0;break;case ARRDN:this.changeHighlight(key);this.setHighlightedValue2();bubble=0;swStoreEntry=0;break;default:this.getSuggestions(this.fld.value);break;}
return bubble;};_b.AutoSuggest.prototype.getSuggestions=function(val)
{if(val==this.sInp)
return 0;_b.DOM.remE(this.idAs);this.sInp=val;if(val.length<this.oP.minchars)
{this.aSug=[];this.nInpC=val.length;return 0;}
var ol=this.nInpC;this.nInpC=val.length?val.length:0;var l=this.aSug.length;if(this.nInpC>ol&&l&&l<this.oP.maxentries&&this.oP.cache)
{var arr=[];for(var i=0;i<l;i++)
{if(this.aSug[i].value.substr(0,val.length).toLowerCase()==val.toLowerCase()||this.aSug[i].value.toLowerCase().indexOf(val.toLowerCase())>0)
arr.push(this.aSug[i]);}
this.aSug=arr;this.createList(this.aSug);return false;}
else
{var pointer=this;var input=this.sInp;clearTimeout(this.ajID);this.ajID=setTimeout(function(){pointer.doAjaxRequest(input)},this.oP.delay);}
return false;};_b.AutoSuggest.prototype.doAjaxRequest=function(input)
{if(input!=this.fld.value)
return false;var pointer=this;if(typeof(this.oP.script)=="function")
var url=this.oP.script(encodeURIComponent(this.sInp));else
var url=this.oP.script+this.oP.varname+"="+encodeURIComponent(this.sInp);if(!url)
return false;var meth=this.oP.meth;var input=this.sInp;var onSuccessFunc=function(req){pointer.setSuggestions(req,input)};var onErrorFunc=function(status){alert("AJAX error: "+status);};var myAjax=new _b.Ajax();myAjax.makeRequest(url,meth,onSuccessFunc,onErrorFunc);};_b.AutoSuggest.prototype.setSuggestions=function(req,input)
{if(input!=this.fld.value)
return false;this.aSug=[];if(this.oP.json)
{var jsondata=eval('('+req.responseText+')');if(jsondata.status==0){Static_AutosuggestResponseData=jsondata;for(var i=0;i<jsondata.results.length;i++)
{this.aSug.push({'id':jsondata.results[i].id,'value':jsondata.results[i].value,'info':jsondata.results[i].info});}}else{return false;}}
else
{var xml=req.responseXML;var results=xml.getElementsByTagName('results')[0].childNodes;for(var i=0;i<results.length;i++)
{if(results[i].hasChildNodes())
this.aSug.push({'id':results[i].getAttribute('id'),'value':results[i].childNodes[0].nodeValue,'info':results[i].getAttribute('info')});}}
this.idAs="as_"+this.fld.id;this.createList(this.aSug);};_b.AutoSuggest.prototype.createList=function(arr)
{var pointer=this;_b.DOM.remE(this.idAs);this.killTimeout();if(arr.length==0&&!this.oP.shownoresults)
return false;var div=_b.DOM.cE("div",{id:this.idAs,className:this.oP.className});var ul=_b.DOM.cE("ul",{id:"as_ul"});for(var i=0;i<arr.length;i++)
{var val=arr[i].value;var st=val.toLowerCase().indexOf(this.sInp.toLowerCase());var output=val.substring(0,st)+"<em>"+val.substring(st,st+this.sInp.length)+"</em>"+val.substring(st+this.sInp.length);var span=_b.DOM.cE("span",{},output,true);if(arr[i].info!="")
{var br=_b.DOM.cE("br",{});span.appendChild(br);var small=_b.DOM.cE("small",{},arr[i].info);span.appendChild(small);}
var a=_b.DOM.cE("a",{href:"#"});var tl=_b.DOM.cE("span",{className:"tl"}," ");var tr=_b.DOM.cE("span",{className:"tr"}," ");a.appendChild(tl);a.appendChild(tr);a.appendChild(span);a.name=i+1;a.onclick=function(){pointer.setHighlightedValue();return false;};a.onmouseover=function(){pointer.setHighlight(this.name);};var li=_b.DOM.cE("li",{},a);ul.appendChild(li);}
if(arr.length==0&&this.oP.shownoresults)
{var li=_b.DOM.cE("li",{className:"as_warning"},this.oP.noresults);ul.appendChild(li);Static_AutosuggestResponseData.results.length=0;}
ul.style.cssText="zoom: 1; padding-top: 4px;";div.appendChild(ul);var pos=_b.DOM.getPos(this.fld);var divPosX=pos.x-3;var divPosY=pos.y-4;var divW=this.fld.offsetWidth;var divH=250;if(navigator.userAgent.toLowerCase().indexOf("msie")!=-1){var divPosX=pos.x-2;var divPosY=pos.y-4;var divW=this.fld.offsetWidth+5;}
div.style.left=divPosX+"px";div.style.top=(divPosY+this.fld.offsetHeight+this.oP.offsety)+"px";div.style.width=divW+"px";div.style.height=divH+"px";div.onmouseover=function(){pointer.killTimeout()};div.onmouseout=function(){pointer.resetTimeout()};document.getElementsByTagName("body")[0].appendChild(div);this.iHigh=0;var pointer=this;this.toID=setTimeout(function(){pointer.clearSuggestions()},this.oP.timeout);};_b.AutoSuggest.prototype.changeHighlight=function(key)
{var list=_b.DOM.gE("as_ul");if(!list)
return false;var n;if(key==40)
n=this.iHigh+1;else if(key==38)
n=this.iHigh-1;if(n>list.childNodes.length)
n=list.childNodes.length;if(n<1)
n=1;this.setHighlight(n);};_b.AutoSuggest.prototype.setHighlight=function(n)
{var list=_b.DOM.gE("as_ul");if(!list)
return false;if(this.iHigh>0)
this.clearHighlight();this.iHigh=Number(n);list.childNodes[this.iHigh-1].className="as_highlight";this.killTimeout();};_b.AutoSuggest.prototype.clearHighlight=function()
{var list=_b.DOM.gE("as_ul");if(!list)
return false;if(this.iHigh>0)
{list.childNodes[this.iHigh-1].className="";this.iHigh=0;}};_b.AutoSuggest.prototype.setHighlightedValue=function()
{if(this.iHigh)
{if(this.aSug[this.iHigh-1])
this.sInp=this.fld.value=html_entity_decode(this.aSug[this.iHigh-1].value);;this.fld.focus();if(this.fld.selectionStart)
this.fld.setSelectionRange(this.sInp.length,this.sInp.length);this.clearSuggestions();if(typeof(this.oP.callback)=="function")
this.oP.callback(this.aSug[this.iHigh-1]);}};_b.AutoSuggest.prototype.setHighlightedValue2=function()
{if(this.iHigh)
{if(this.aSug[this.iHigh-1])
this.sInp=this.fld.value=html_entity_decode(this.aSug[this.iHigh-1].value);}};_b.AutoSuggest.prototype.killTimeout=function()
{clearTimeout(this.toID);};_b.AutoSuggest.prototype.resetTimeout=function()
{clearTimeout(this.toID);var pointer=this;this.toID=setTimeout(function(){pointer.clearSuggestions()},500);};_b.AutoSuggest.prototype.clearSuggestions=function()
{this.killTimeout();var ele=_b.DOM.gE(this.idAs);var pointer=this;if(ele)
{var fade=new _b.Fader(ele,1,0,250,function(){_b.DOM.remE(pointer.idAs)});}};if(typeof(_b.Ajax)=="undefined")
_b.Ajax={};_b.Ajax=function()
{this.req={};this.isIE=false;};_b.Ajax.prototype.makeRequest=function(url,meth,onComp,onErr)
{if(meth!="POST")
meth="GET";this.onComplete=onComp;this.onError=onErr;var pointer=this;if(window.XMLHttpRequest)
{this.req=new XMLHttpRequest();this.req.onreadystatechange=function(){pointer.processReqChange()};this.req.open("GET",url,true);this.req.send(null);}
else if(window.ActiveXObject)
{this.req=new ActiveXObject("Microsoft.XMLHTTP");if(this.req)
{this.req.onreadystatechange=function(){pointer.processReqChange()};this.req.open(meth,url,true);this.req.send();}}};_b.Ajax.prototype.processReqChange=function()
{if(this.req.readyState==4){if(this.req.status==200)
{this.onComplete(this.req);}else{this.onError(this.req.status);}}};if(typeof(_b.DOM)=="undefined")
_b.DOM={};_b.DOM.cE=function(type,attr,cont,html)
{var ne=document.createElement(type);if(!ne)
return 0;for(var a in attr)
ne[a]=attr[a];var t=typeof(cont);if(t=="string"&&!html)
ne.appendChild(document.createTextNode(cont));else if(t=="string"&&html)
ne.innerHTML=cont;else if(t=="object")
ne.appendChild(cont);return ne;};_b.DOM.gE=function(e)
{var t=typeof(e);if(t=="undefined")
return 0;else if(t=="string")
{var re=document.getElementById(e);if(!re)
return 0;else if(typeof(re.appendChild)!="undefined")
return re;else
return 0;}
else if(typeof(e.appendChild)!="undefined")
return e;else
return 0;};_b.DOM.remE=function(ele)
{var e=this.gE(ele);if(!e)
return 0;else if(e.parentNode.removeChild(e))
return true;else
return 0;};_b.DOM.getPos=function(e)
{var e=this.gE(e);var obj=e;var curleft=0;if(obj.offsetParent)
{while(obj.offsetParent)
{curleft+=obj.offsetLeft;obj=obj.offsetParent;}}
else if(obj.x)
curleft+=obj.x;var obj=e;var curtop=0;if(obj.offsetParent)
{while(obj.offsetParent)
{curtop+=obj.offsetTop;obj=obj.offsetParent;}}
else if(obj.y)
curtop+=obj.y;return{x:curleft,y:curtop};};if(typeof(_b.Fader)=="undefined")
_b.Fader={};_b.Fader=function(ele,from,to,fadetime,callback)
{if(!ele)
return 0;this.e=ele;this.from=from;this.to=to;this.cb=callback;this.nDur=fadetime;this.nInt=50;this.nTime=0;var p=this;this.nID=setInterval(function(){p._fade()},this.nInt);};_b.Fader.prototype._fade=function()
{this.nTime+=this.nInt;var ieop=Math.round(this._tween(this.nTime,this.from,this.to,this.nDur)*100);var op=ieop/100;if(this.e.filters)
{try
{this.e.filters.item("DXImageTransform.Microsoft.Alpha").opacity=ieop;}catch(e){this.e.style.filter='progid:DXImageTransform.Microsoft.Alpha(opacity='+ieop+')';}}
else
{this.e.style.opacity=op;}
if(this.nTime==this.nDur)
{clearInterval(this.nID);if(this.cb!=undefined)
this.cb();}};_b.Fader.prototype._tween=function(t,b,c,d)
{return b+((c-b)*(t/d));};function storeEntryMessageHide(id)
{if(document.getElementById(id)){document.getElementById(id).parentNode.removeChild(document.getElementById(id));}}
function storeEntryProcessAjax(o,cnn,table,pk,pkt,fld)
{var myAjax=new _b.Ajax();myAjax.makeRequest("../gulliver/genericAjax?request=storeInTmp"+"&cnn="+cnn+"&table="+table+"&pk="+pk+"&pkt="+pkt+"&fld="+fld+"&value="+o.value,"POST",function(req)
{var response=eval("("+req.responseText+")");switch(response.status){case 1:var w1=document.documentElement.clientWidth;var sbX1=document.documentElement.scrollLeft;var sbY1=document.documentElement.scrollTop;var w2=document.body.clientWidth;var sbX2=document.body.scrollLeft;var sbY2=document.body.scrollTop;var bodyW=(w1>0)?w1:w2;var scrollbarX=(sbX1>0)?sbX1:sbX2;var scrollbarY=(sbY1>0)?sbY1:sbY2;storeEntryMessageHide("myIframe");var myIframe=document.createElement("iframe");myIframe.setAttribute("id","myIframe");myIframe.style.position="absolute";myIframe.style.left=(parseInt(bodyW/2)-200+scrollbarX)+"px";myIframe.style.top=(scrollbarY+5)+"px";myIframe.src="about:blank";myIframe.frameBorder=0;myIframe.scrolling="no";myIframe.style.width="400px";myIframe.style.height="45px";leimnud.event.add(myIframe,"load",function(evt)
{document.getElementById("myIframe").contentWindow.document.body.style.margin=0;document.getElementById("myIframe").contentWindow.document.body.style.padding=0;document.getElementById("myIframe").contentWindow.document.body.innerHTML="<div style=\"border: 1px solid #808080; width: 398px; height: 43px; background: #ADDCC7; font:0.9em arial, verdana, helvetica, sans-serif;\"><div style=\"margin: 0.5em 0 0 0.5em;\"><img src=\"/images/documents/_accept.png\" alt=\"\" style=\"margin-right: 0.8em; vertical-align: middle;\" />"+_("ID_FIELD_DYNAFORM_SUGGEST_MESSAGE_TEMPORAL")+"</div></div>";});document.body.appendChild(myIframe);setTimeout("storeEntryMessageHide(\"myIframe\")",1750);swStoreEntry=0;break;default:alert(response.message);break;}},function(req)
{});}
function html_entity_decode(string,quote_style){var histogram={},symbol='',tmp_str='',entity='';tmp_str=string.toString();if(false===(histogram=get_html_translation_table('HTML_ENTITIES',quote_style))){return false;}
delete(histogram['&']);histogram['&']='&amp;';for(symbol in histogram){entity=histogram[symbol];tmp_str=tmp_str.split(entity).join(symbol);}
return tmp_str;}
var pmtooltip=false;var pmtooltipShadow=false;var pmshadowSize=4;var pmtooltipMaxWidth=400;var pmtooltipMinWidth=100;var pmiframe=false;var tooltip_is_msie=(navigator.userAgent.indexOf('MSIE')>=0&&navigator.userAgent.indexOf('opera')==-1&&document.all)?true:false;function showTooltip(e,tooltipTxt){var bodyWidth=Math.max(document.body.clientWidth,document.documentElement.clientWidth)-20;if(!pmtooltip){pmtooltip=document.createElement('DIV');pmtooltip.id='pmtooltip';pmtooltipShadow=document.createElement('DIV');pmtooltipShadow.id='pmtooltipShadow';document.body.appendChild(pmtooltip);document.body.appendChild(pmtooltipShadow);if(tooltip_is_msie){pmiframe=document.createElement('IFRAME');pmiframe.frameborder='5';pmiframe.style.backgroundColor='#FFFFFF';pmiframe.src='#';pmiframe.style.zIndex=100;pmiframe.style.position='absolute';document.body.appendChild(pmiframe);}}
pmtooltip.style.display='block';pmtooltipShadow.style.display='block';if(tooltip_is_msie)pmiframe.style.display='block';var st=Math.max(document.body.scrollTop,document.documentElement.scrollTop);if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0;var leftPos=e.clientX+10;pmtooltip.style.width=null;pmtooltip.innerHTML=tooltipTxt;pmtooltip.style.left=leftPos+5+'px';pmtooltip.style.top=e.clientY+st+'px';pmtooltipShadow.style.left=leftPos+pmshadowSize+'px';pmtooltipShadow.style.top=e.clientY+st+pmshadowSize+'px';if(pmtooltip.offsetWidth>pmtooltipMaxWidth){pmtooltip.style.width=pmtooltipMaxWidth+'px';}
var tooltipWidth=pmtooltip.offsetWidth;if(tooltipWidth<pmtooltipMinWidth)tooltipWidth=pmtooltipMinWidth;pmtooltip.style.width=tooltipWidth+'px';pmtooltipShadow.style.width=pmtooltip.offsetWidth+'px';pmtooltipShadow.style.height=pmtooltip.offsetHeight+'px';if((leftPos+tooltipWidth)>bodyWidth){pmtooltip.style.left=(pmtooltipShadow.style.left.replace('px','')-((leftPos+tooltipWidth)-bodyWidth))+'px';pmtooltipShadow.style.left=(pmtooltipShadow.style.left.replace('px','')-((leftPos+tooltipWidth)-bodyWidth)+pmshadowSize)+'px';}
if(tooltip_is_msie){pmiframe.style.left=pmtooltip.style.left;pmiframe.style.top=pmtooltip.style.top;pmiframe.style.width=pmtooltip.offsetWidth+'px';pmiframe.style.height=pmtooltip.offsetHeight+'px';}}
function hideTooltip(){pmtooltip.style.display='none';pmtooltipShadow.style.display='none';if(tooltip_is_msie)pmiframe.style.display='none';}