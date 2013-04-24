/***************************************************************************
*				maborak.js
*                        ------------------------
*   Copyleft	: (c) 2007 maborak.com <maborak@maborak.com>
*   Version	: 0.6
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
var maborak = function(forceCssLoad){
	this.info={
     version: "0.3",
     name: "maborak",
     file: "maborak" + ((BROWSER_CACHE_FILES_UID != "")? "." + BROWSER_CACHE_FILES_UID : "") + ".js"
	},

	this.forceCssLoad = forceCssLoad ? true : false;
	/**
	* Make this Class
	* @param options = Object{Options.for.class} || {};
	* @access		 = Public;
	*/
	this.make=function(options)
	{
		this.protoCore();
		this.module={
			debug:function(flag){
				this.flag = flag || false;
				this.log=function(v)
				{
					if(typeof console!='undefined' && this.flag===true)
					{
						console.log(v || '');
					}
				};
				return this;
			}
		}.expand(this);
		this.options={
            thisIsNotPM:false
        }.concat(options || {});
		this.report	= new this.bitacora();
		this.loadMethods([this.checkBrowser],this);
		this.event	= this.factory(this.mantis,true);
		this.tools	= this.factory(this.extended.tools,true);
		this.file	= this.factory(this.fileCore,true);
		this.dom	= this.factory(this.extended.D0M,true);
		this.iphone	= this.factory(this.iphoneBrowser,true);
		this.cookie	= this.factory(this.extended.cookie,true);
		this.Package	= new this.PackageCore(this,this.file.db);

		this.report.add("Class loaded.");
		this.info.base=this.tools.baseJS(this.info.file);
		this.info.images=this.info.base+"images/";
		this.path_root=this.tools.path_root(this.info.base)+"/";

		if(this.options.modules){
			this.Package.Load(this.options.modules,{Instance:this,Type:"module"});
		}
		if(this.options.files){
			this.Package.Load(this.options.files,{Type:"file"});
		}
		this.exec(this.fix.memoryLeak);

		/* create Stylesheet BEGIN  */
		//erik: Now the core css is available just by demand
		if (this.forceCssLoad === true) {
			//console.log('cargo css');
			var st	=$dce('link');
			st.rel	='stylesheet';
			st.type	='text/css';
			st.href	=this.info.base+'stylesheet/default.css';
			this.dom.capture("tag.head 0").appendChild(st);
		}
		/* create Stylesheet END  */
		this.expand(this);
		return this;
	};
	this.factory=function(Class,create)
	{
		var cl = (typeof Class==="function")?Class:function(){};
		cl.prototype.parent = this;
		if(create===true)
		{
			//return new cl().expand();
			return new cl();
		}
		else
		{
			return cl;
		}
	},
	this.Class=function()
	{
		var Vc = function(){};
		return new Vc();
	},
	/**
	* @class Manage Patterns Design
	*/
	this.pattern={
		observer:function(event)
		{
			this.event = event;
			this.g="aaa";
			this.db = [];
			this.register=function(launch,Class)
			{
				this.event = event;
				this.Class = Class;
				this.launch = launch;
				if(this.verify())
				{
					return this.write();
				}
				return true;
			};
			this.verify=function()
			{
				return (typeof this.launch==="function")?true:false;
			};
			this.write=function()
			{
				var cap = {
					//update:this.parent.closure({instance:this,method:this.update}),
					//unregister:this.parent.closure({instance:this,method:this.unregister,args:this.db.length})
					update:this.update,
					unregister:this.unregister.args(this.db.length)
				};
				this.db.push(this.launch);
				if(this.Class)
				{
					this.Class.observer = cap;
				}
				delete this.event;
				delete this.Class;
				delete this.launch;
				return this.db.length-1;
			};
			this.update=function()
			{
				var ln = this.db.length;
				for(i=0;i<ln;i++)
				{
					if(typeof this.db[i]=="function")
					{
						this.db[i]();
					}
				}
			};
			this.unregister=function(uid)
			{
				//alert(this.db[uid])
				if(this.db[uid])
				{
					this.db[uid]=null;
				}
			};
			this.expand(this);
		}
	};
	/**
	* Private functions{
	*/
	var argumentsToArray=function(a){
		var args=[];
		for(var i=0;i<a.length;i++){args.push(a[i]);};
		return args;
	};
	var tagScript = '(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)';
	/**
	* }Private functions
	*/
	this.tmp= {
		rpc:{}
	};
	this.charset="utf-8";
	/**
	* Make Core Functions
	* @extends String || Array || Object
	*/
	this.protoCore=function()
	{
		/**
		 * document.getElementById
		 * @param {Object || String} el
		 */
		window['$'] = function(el){
			return (typeof el == "string") ? document.getElementById(el) : el;
		};
		/**
		 * document.createElement
		 * @param {String} el
		 */
		window['$dce'] = function(el){
			return document.createElement(el);
		};
		/**
		 * document.getElementsByName
		 * @param {Object || String} el
		 */
		window['$n'] = function(el){
			return (typeof el == "string")?document.getElementsByName(el):el;
		};
		Array.prototype.isArray		= true;
		Array.prototype.isObject	= false;
		/**
		* Only Int values in Array
		* @return Array
		*/
		Array.prototype.onlyInt		= function()
		{
			var valid=[];
			for(var i=0;i<this.length;i++)
			{
				if(!isNaN(this[i]))
				{
					valid.push(parseInt(this[i],10));
				}
			}
			return valid;
		};
		/**
		* Check if a value exists in an Array
		* @return Boolean
		*/
		Array.prototype.inArray	= function(search)
		{
			var valid=[];
			for(var i=0;i<this.length;i++)
			{
				if(this[i]===search)
				{
					return true;
				}
			}
			return false;
		};
		/**
		* Fill an array with values
		* @return Array
		*/
		Array.prototype.fill		= function(startIndex,cant,value)
		{
			for(var i=0;i<cant;i++)
			{
				this.splice(startIndex+i,0,value);
			}
			return this;
		};
		/**
		* Convert (Array || Object) to String
		* @param {Boolean} strict Optional: Exclude prototype (Methods && Properties)
		* @return String
		*/
		Array.prototype.toStr = Object.prototype.toStr	= function(strict)
		{
			var val, output = "";
			output += "{";
			for (var i in this) {
				val = this[i];
				if((!strict && this.propertyIsEnumerable(i)) || strict===true)
				{
					switch (typeof val) {
						case ("object"):
						if(typeof val.childNodes!="undefined")
						{
							output += i + ":[DOM.Object],\n";
						}
						else if (val.isArray || val.isObject) {
							output += i + ":" + val.toStr(strict) + ",\n";
						} else {
							output += i + ": Element||Event,\n\n";
						}
						break;
						case ("string"):
						output += i + ":'" + val + "',\n";
						break;
						case ("function"):
						output += i + ":FUNCTION,\n";
						break;
						default:
						output += i + ":" + val + ",\n";
					}
				}
			}
			output = output.substring(0, output.length-1) + "}";
			return output;
		};
		Array.prototype.indexOf=function(val)
		{
			for (var i = 0; i < this.length; i++)
			{
				if (this[i] == val){return i;}
			}
			return -1;
		};
		/**
		* Remove duplicate values
		* @return Array
		*/
		Array.prototype.unique = function()
		{
			if(this.length<2){return this;}
			var a = [], i, l = this.length;
			for( i=0; i<l; i++ ){
				if(a.indexOf(this[i])< 0 )
				{
					a.push( this[i]);
				}
			}
			return a;
		};
		/**
		* Fetch a key from an Array
		* @param {String|Boolean|Int|Object|Array} value Value to search
		* @return Int
		*/
		Array.prototype.key = function(value)
		{
			for(var i=0;i<this.length;i++) {
				if(this[i]===value){return i;}
			}
			return false;
		};
		/**
		* Return a random element
		* @param {Int} range Up to range
		* @return Value random
		*/
		Array.prototype.random = function(range)
		{
			var i = 0, l = this.length;
			if(!range) { range = this.length; }
			else if( range > 0 ) { range = range % l; }
			else { i = range; range = l + range % l; }
			return this[ Math.floor( range * Math.random() - i ) ];
		};
		/**
		* Map array elements
		* @param {Function} fun
		* @return Function
		*/
		Array.prototype.map = function(fun)
		{
			if(typeof fun!=="function"){return false;}
			var i = 0, l = this.length;
			for(i=0;i<l;i++)
			{
				fun(this[i]);
			}
			return true;
		};
		/**
		* Randomly interchange elements
		* @param {Boolean} recursive Shuffle recursive Array elements.
		* @return Array
		*/
		Array.prototype.shuffle = function(recursive)
		{
			var i = this.length, j, t;
			while( i ) {
				j = Math.floor( ( i-- ) * Math.random() );
				t = recursive && typeof this[i].shuffle!=='undefined' ? this[i].shuffle() : this[i];
				this[i] = this[j];
				this[j] = t;
			}
			return this;
		};
		/**
		* Eval scripts
		* @return String
		*/
		Array.prototype.evalScript = function(extracted)
		{
    		var s=this.map(function(sr){
				//window.setTimeout((sr.match(new RegExp(tagScript, 'im')) || ['', ''])[1],0);
				var sc=(sr.match(new RegExp(tagScript, 'im')) || ['', ''])[1];
				if(window.execScript){
					window.execScript(sc || " ");
				}
				else
				{
					//ndow.eval(code);
					window.setTimeout(sc,0);
				}
				//eval(sc);
			});
			return true;
		};
		/**
		* Clear Array
		* @return Array;
		*/
		Array.prototype.clear=function()
		{
			return this.splice(0,this.length);
		};
		/**
		* Copy Array
		* @return Array;
		*/
		Array.prototype.copy=function()
		{
			return this.slice(0,this.length);
		};
		/**
		* Insert data in Array key
		* @return Array
		*/
		Array.prototype.insert = function(key,value)
		{
			var na  = this.copy();
			value	= (!value || value.isArray===false)?[value]:value;
			this.clear();
			for(var i=0;i<na.length;i++)
			{
				if(i===key)
				{
						for(var j=0;j<value.length;j++)
						{
							this.push(value[j]);
						}
				}
				this.push(na[i]);
			}
			return this;
		};
		/**
		* Convert array to select data
		* @return Array
		*/
		Array.prototype.toSelect = function()
		{
            var s = [];
			for(var i=0;i<this.length;i++)
			{
			    s.push({value:i,text:this[i]});
			}
			return s;
		};

		Object.prototype.isObject	= true;
		Object.prototype.isArray	= false;

		/**
		* propertyIsEnumerable for Safari
		* @return Boolean
		**/
		Object.prototype.propertyIsEnumerable=function(i)
		{
			return (typeof Object.prototype[i]==="undefined")?true:false;
		};
		/**
		* Length of Object
		* @return Int
		*/
		/*Object.prototype.length	= function()
		{
			var j=0;
			for (var i in this) {
				if(this.propertyIsEnumerable(i))
				{
					j+=1;
				}
			}
			return j;
		};*/
		/**
		* Concat Object
		* @param {Object} obj Object
		* @return {Object} this
		*/
		Object.prototype.concat = function(obj)
		{
			for (var i in obj)
			{
				if(obj.propertyIsEnumerable(i))
				{
					this[i]=obj[i];
				}
			}
			return this;
		};
		/**
		* es| Obtener el valor de un Objeto a partir de su Key
		* @param {Int} id Key of object (1,2,3,4,5)
		* @return Key value
		*/
		Object.prototype.get_by_key= function(id,key)
		{
			var j=0;
			for (var i in this) {
				if(this.propertyIsEnumerable(i))
				{
					if(id===j){return (key)?i:this[i];}
					j+=1;
				}
			}
			return false;
		};
		/**
		* es| Verificar si existe un key
		* @param {String} key Key
		* @return Boolean
		*/
		Object.prototype.isset_key= function(key)
		{
			for (var i in this) {
				if(this.propertyIsEnumerable(i))
				{
					if(key===i){return true;}
				}
			}
			return false;
		};

		/**
		* es| Asignarle prototype.parent a todas las funciones
		* @param {Object} obj
		* @return {Object} this
		*/
		Object.prototype.setParent	= function(obj)
		{
			for (var i in this) {
				if(this.propertyIsEnumerable(i) && typeof this[i]==="function")
				{
					this[i].prototype.parent=obj || false;
				}
			}
			return this;
		};
		/**
		* es| Excluir objetos tipo DOM
		* @param {Boolean}
		*/
		Object.prototype.isObjectStrict	= function()
		{
			return (this.appendChild)?false:true;
		};
		/**
		* es| Expandir una Clase dentro de sus objetos literales
		* @param {Object}
		*/
		Object.prototype.expand=function(Class,recursive)
		{
			Class=Class || this;
			for(var i in this)
			{
				if(this.propertyIsEnumerable(i) && (typeof this[i]==="function" || (recursive===true && typeof this[i]==="object" && this[i].isObjectStrict())))
				{
					try{
						if(typeof this[i]==="function")
						{
							//kkk.push(this[i]);
							this[i]=this[i].extend(Class);
						}
						else
						{
							this[i]=this[i].expand(Class,recursive);
						}
					}
					catch(e){
						this[i]=this[i];
					}
				}
				else
				{
					//alert(i);
				}
			}
			return this;
		};
		Function.prototype.isObject	= false;
		Function.prototype.isArray	= false;
		/**
		* es| Expandir función en una Clase
		* @param {Funcion}
		*/
		Function.prototype.extend=function(Class)
		{
			try{
				//kkk.push(this);
				var oThis=this;
				var args=argumentsToArray(arguments);
				args.splice(0,1);
				return function()
				{
					return oThis.apply(Class,argumentsToArray(arguments).concat(args));
				};
			}
			catch(e){
				return this;
			}
		};
		/**
		* es| Añadir argumentos a una función
		* @param {Function}
		*/
		Function.prototype.args=function()
		{
			var oThis=this;
			var args=argumentsToArray(arguments);
			return function()
			{
				return oThis.apply(oThis,argumentsToArray(arguments).concat(args));
			};
		};
		String.prototype.isAlphaUS=function()
		{
			var a = this.split("");
			var b = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_".split("");
			//alert(b.length)
			for(var i=0;i<a.length;i++)
			{
				if(!b.inArray(a[i])){
					return false;
				}
			}
			return true;
		};
		/**
		* Strip whitespaces from the beginning and end of String
		* @return String with whitespaces stripped
		*/
		String.prototype.isString=true;
		/**
		* Strip whitespaces from the beginning and end of String
		* @return String with whitespaces stripped
		*/
		String.prototype.trim = function(){
			return( this.replace(new RegExp("^([\\s]+)|([\\s]+)$", "gm"), "") );
		};
		/**
		* Strip whitespaces from the beginning of String
		* @return String
		*/
		String.prototype.leftTrim = function(){
			return( this.replace(new RegExp("^[\\s]+", "gm"), "") );
		};
		/**
		* Strip whitespaces from the end of String
		* @return String
		*/
		String.prototype.rightTrim = function(){
			return( this.replace(new RegExp("[\\s]+$", "gm"), "") );
		};
		/**
		* Strip HTML tags from a string
		* @return String
		*/
		String.prototype.stripTags = function()
		{
			return this.replace(/<\/?[^>]+>/gi, '');
		};
		/**
		* Convert special characters to HTML entities
		* @return String
		*/
		String.prototype.escapeHTML = function()
		{
			var div = $dce('div');
			var text = document.createTextNode(this);
			div.appendChild(text);
			return div.innerHTML;
		};
		/**
		* Convert special HTML entities back to characters
		* @return String
		*/
		String.prototype.unescapeHTML = function()
		{
			var div = $dce('div');
			div.innerHTML = this.trim();
			return div.childNodes[0] ? div.childNodes[0].nodeValue : '';
		};
		/**
		* Search and Replace
		* @return String
		*/
		String.prototype.sReplace = function(search,replace)
		{
			search = search || "";
			replace= replace || "";
			var re = new RegExp(search,"g");
			return this.replace(re,replace);
		};
		/**
		* Camelize String (text-align -> textAlign)
		* @return String
		*/
		String.prototype.camelize = function ()
		{
			var oStringList = this.split("-");
			if (oStringList.length == 1) {
				return oStringList[0];
			}
			var camelizedString = this.indexOf("-")===0 ? oStringList[0].charAt(0).toUpperCase() + oStringList[0].substring(1) : oStringList[0];
			for (var i = 1, len = oStringList.length; i < len; i++)
			{
				var s = oStringList[i];
				camelizedString += s.charAt(0).toUpperCase() + s.substring(1);
			}
			return camelizedString;
		};
		/**
		* Convert String to Array
		* @return Array
		*/
		String.prototype.toArray = function()
		{
			return this.split("");
		};
		/**
		* extract script fragment
		* @return String
		*/
		String.prototype.extractScript = function()
		{
			var matchAll = new RegExp(tagScript, 'img');
    		return (this.match(matchAll) || []);
		};
		/**
		* Eval script fragment
		* @return String
		*/
		String.prototype.evalScript = function()
		{
    		return (this.match(new RegExp(tagScript, 'img')) || []).evalScript();
		};
		/**
		* strip script fragment
		* @return String
		*/
		String.prototype.stripScript = function()
		{
			return this.replace(new RegExp(tagScript, 'img'), '');
		};

		/**
		 * Return first letters as uppercase, rest lower.
		 */
		String.prototype.toInitCap = function(str)
		{
			return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
		        return $1.toUpperCase();
		    });
		};

		/**
		*	XMLSerializer Crossbrowser
		*/
		if((typeof XMLSerializer)==='undefined')
		{
			window.XMLSerializer = function() {
				this.toString=function()
				{
					return "[object XMLSerializer]";
				};
				this.serializeToString=function(xml){
					return xml.xml || xml.outerHTML || "Error XMLSerializer";
				};
			};
		}
	};
	/**
	* Load methods
	* @param methods = Array[Method || Array[Method,[Argument1,Argument2,...],return]];
	* @param instance= Class;
	* @example:
	* 		this.loadMethods([
	* 				this.proto,
	*				[this.checkBrowser,['argument1',More...,12]]
	*		],this);
	* @access		 = Public;
	*/
	this.loadMethods = function(methods,instance)
	{
		var _return_ = [];
		var tmp;
		for(var i=0;i<methods.length;i++)
		{
			if(methods[i])
			{
				if(methods[i].isArray)
				{
					if(typeof methods[i][0]=="function")
					{
						var method = (methods[i][1])?((methods[i][1].isArray)?methods[i][1]:[methods[i][1]]):false;

						if(method===false)
						{
							tmp = methods[i][0].apply(instance);
						}
						else
						{
							tmp = methods[i][0].apply(instance,method);
						}
						if(methods[i][2]===true){_return_.push(tmp);}
					}
				}
				else if(typeof methods[i]=="function")
				{
					methods[i].apply(instance);
				}
			}
		}
		return (_return_.length==1)?_return_[0]:_return_;
	};
	/**
	* Identify User-Agent of Browser
	* @result
	* 	isIE	= "Microsoft Internet Explorer"
	* 	isNS	= "Netscape"
	* 	isFF	= "Mozilla Firefox"
	* 	isSF	= "Safari"
	* 	isGK	= "Browsers based on Gecko"
	* 	isOP	= "Opera"
	* @access	= Private;
	*/
	this.checkBrowser = function()
	{
		var userAgent=navigator.userAgent;
		var u;
		this.browser={
			isIE:((userAgent.indexOf('MSIE')>=0)?true:false),
			isNS:((userAgent.indexOf('Netscape6/')>=0)?true:false),
			isFF:((userAgent.indexOf('Firefox')>=0)?true:false),
			isSF:((userAgent.indexOf('Safari')>=0)?true:false),
			isGK:((userAgent.indexOf('Gecko')>=0)?true:false),
			isIphone:((userAgent.indexOf('iPhone')>=0)?true:false),
			isOP:((userAgent.indexOf('Opera')>=0)?true:false)
		};
		this.browser.isIE=(this.browser.isOP)?false:this.browser.isIE;
		var checkFor=["MSIE","Netscape/6","Firefox","Safari","Gecko","Opera","iPhone"];
		for(var i=0;i<checkFor.length;i++)
		{
			var j = userAgent.indexOf(checkFor[i]);
			this.browser.version = userAgent+"::::"+userAgent.substr(j + checkFor[i].length);
		}
	};
	/**
	* @class		 = Event manager
	*/
	this.mantis = function()
	{
		this.db=[];
		this.flush=function()
		{
			var i=0;
			while (this.db.length > 0)
			{
				if(this.db[0] && this.db[0].isObject===true)
				{
					this.remove(this.db[0]._object_,this.db[0]._event_,this.db[0]._function_,this.db[0]._bumble_);
				}
				this.db.splice(0,1);
			}
		};
		/**
		* Add new Event;
		* @param _object_	= DOMelement;
		* @param _event_	= event [load,focus,etc];
		* @param _function_ = Function || Object{method,instance,[arguments[Array],event[Boolean],argument_is_array[Boolean]]} || Function[virtual];
		* @param _bumble_	= true || false;
		* @example:
		*
		*	1)	Callback simple:
		*		this.event.add(Input,"unload",FunctionX);
		*
		*	2)	Callback is Object
		*		this.event.add(Input,"click",{
		*			method	: this.other,
		*			instance: this
		*		});
		*	3)	Callback is Object & Advanced options
		*		this.event.add(Input,"click",{
		*			method	: this.other,
		*			instance: this
		*			arguments:[989898,767676], //Arguments to Function Callback
		*			event	:true // es| Expandir evento como argumento
		*		});
		*	4)	Callback to Virtual Instance
		*		this.event.add(Input,"click",leimnud.closure({
		*			method:this.changes,
		*			instance:this,
		*			arguments:98989898
		*		}));
		*	5)	Callback to Virtual Function
		*		this.event.add(Input,"click",leimnud.closure({
		*			Function:foo,
		*			arguments:[bla,99]
		*		}));
		*/
		this.add=function(_object_,_event_,_function_,_bumble_)
		{
			_function_=(_function_.isObject)?this.parent.closure(_function_):_function_;
			_object_ = this.parent.dom.element(_object_);
			if (_object_.addEventListener)
			{
				_object_.addEventListener(_event_,_function_,((_bumble_===true)?true:false));
			}
			else if(_object_.attachEvent)
			{
				_object_.attachEvent("on"+_event_,_function_);
			}
			else
			{
				this.report("Event registration not supported");
			}
			var event = {
				_object_	:_object_,
				_event_		:_event_,
				_function_	:_function_,
				_bumble_	:((_bumble_===true)?true:false)
			};
			this.db.push(event);
			return (this.db.length-1);
		};
		/**
		* Remove Event;
		* @param {DOM Object} _object_	= DOMelement;
		* @param {event} _event_	= event [load,focus,etc];
		* @param {Function} _function_ = Function || Object{method,instance,[arguments[Array],event[Boolean],argument_is_array[Boolean]]} || Function[virtual];
		* @param {Boolean} _bumble_	= true || false;
		* @example:
		*		Add new Event Examples.
		*/
		this.remove=function(_object_,_event_,_function_,_bumble_,uidInDB)
		{
			_function_=(_function_.isObject)?this.parent.closure(_function_):_function_;
			_object_ = this.parent.dom.element(_object_);
			if (_object_.removeEventListener)
			{
				_object_.removeEventListener(_event_,_function_,((_bumble_===true)?true:false));
			}
			else if(_object_.detachEvent)
			{
				_object_.detachEvent("on"+_event_,_function_);
			}
			if(uidInDB)
			{
				if(uidInDB==(this.db.length-1))
				{
					this.db.pop();
				}
				else
				{
					this.db[uidInDB]=null;
				}
			}
		};
		/**
		* es| Remover evento basado en Uid
		*/
		this.removeFromUid=function(uid)
		{
			if(this.db[uid])
			{
				var e = this.db[uid];
				this.remove(e._object_,e._event_,e._function_,e._bumble_,uid);
			}
		};
		/**
		* Flush Collection events from DB
		* @param	{Array} arrayEventsInDB Array of Events.
		*/
		this.flushCollection=function(arrayEventsInDB)
		{
			var l=arrayEventsInDB.length;
			for(i=0;i<l;i++)
			{
				this.remove(this.db[arrayEventsInDB[i]]._object_,this.db[arrayEventsInDB[i]]._event_,this.db[arrayEventsInDB[i]]._function_,this.db[arrayEventsInDB[i]]._bumble_,arrayEventsInDB[i]);
			}
		};
		/**
		* es| Reportar fallos en el registro de eventos
		* @param {String} text String;
		*/
		this.report=function(text)
		{
			if(this.parent && this.parent.report)
			{
				this.parent.report.add(text);
			}
		};
		/**
		* Captura DOM event
		* @param {Object} event
		* @return DOM
		*/
		this.dom=function(event)
		{
			return event.target || window.event.srcElement;
		};
		/**
		* es| Arreglar fallo IE (sobreposición de eventos)
		* @param {Object} event
		*/
		this.Null=function(event)
		{
			if(event.preventDefault)
			{
				event.preventDefault();
			}
			event.returnValue = false;
		};
		this.expand(this);
	};
	/**
	* @class	= System report
	* @access	= Public;
	*/
	this.bitacora=function()
	{
		this.db=[];
		/**
		* @param	text = String;
		* @access 	Public;
		*/
		this.add=function(text)
		{
			this.db.push(text);
		};
	};
	/**
	* es| Objeto con bugs Crossbrowser.
	* @access	= Public;
	*/
	this.fix={
		memoryLeak:function()
		{
			this.event.add(window,"unload",this.event.flush);
		}
	};
	/**
	* es |  Ejecuta un método de forma encapsulada
	*		especial para funciones en Objetos Literales
	* @param _function_ = method
	* @param _arguments_= arguments || false
	* @param _return_	= true || false
	* @param _instance_	= instance || this
	* @access	= Public;
	*/
	this.exec=function(_function_,_arguments_,_return_,_instance_)
	{
		/**return ((_instance_)?_instance_:this).loadMethods([[_function_,((_arguments_)?_arguments_:null),_return_ || false]],((_instance_)?_instance_:this));*/
		return this.loadMethods([[_function_,((_arguments_)?_arguments_:null),_return_ || false]],((_instance_)?_instance_:this));
	};
	/**
	* es|  Crear funciones virtuales
	* @param {Object} options = {
	*		method	:Method,
	*		instance:Instance,
	*		Function:Function,
	*		arguments:Array["sample",var,222]
	*		event	:true || false,   		#Expand event?
	*		argument_is_array:true || false		#Arguments is Array?
	*	} Options
	* @example:
	*	1)	Virtual Instance
	*		var virtualFunction = leimnud.closure({
	*			method:this.foo,
	*			instance:this,
	*			arguments:98989898
	*		});
	*	2)	Virtual Function
	*		var virtualFunction = leimnud.closure({
	*			Function:foo,
	*			arguments:[bla,99]
	*		});
	*/
	this.closure=function(options)
	{
		var method	=options.method;
		var instance=options.instance;
		var args	=(options.args || (typeof options.args=="number" && options.args===0))?options.args:false;
		var _function=options.Function || false;
		var isArr	=options.args_is_array || false;
		var _event	=options.event || false;
		var rf		=options.Return || false;
		return function(hEvent)
		{
			//window.status="EEE=> "+(h || window.event);
			var argss=(args===false)?false:((args.isArray && isArr===false)?args:[args]);
			//window.status = typeof _event+":"+hEvent+":"+_event;
			//window.status = args;

			var param=(_event)?[(hEvent || window.event)].concat(argss):argss;
			if(_function===false)
			{
				//window.status="EventHandler:=> "+param;
				method.apply(instance,param || [null]);
			}
			else
			{
				_function.apply(_function,param || [null]);
			}
			return rf;
		};
	};
	/**
	* es| Clase para cargar archivos,módulos,objetos
	*
	* @class			= Package Manager;
	* @param	parent	= Leimnud Class || Leimnud Instance;
	* @param	db		= Class File Manager;
	* @access			= Public;
	*/
	this.PackageCore=function(parent,db)
	{
		this.parent	= parent || false;
		this.db		= db || false;
		/**
		* Load new Package
		*/
		this.Load	= function(file,options)
		{
			this.options	=	{
				zip:false
			}.concat(options || {});
			if(arguments.length<2 || !this.check()){return false;}
			this.toLoad = ((this.options.Absolute===true)?this.options.Path:file).split(",");
			if(this.type === 'module' && (this.options.zip===true || this.parent.options.zip===true))
			{
				var tl = [];
				for (var i = this.toLoad.length; i > 0; i--)
				{
					this.name = this.toLoad[this.toLoad.length - i];
					if (!this.isset()) {
						tl.push(this.name);
						this.write(false);
					}
				}
				//alert(this.parent.options.thisIsNotPM);
    /*
				if (tl.length > 0) {
					var script = $dce("script");
					this.parent.dom.capture("tag.head 0").appendChild(script);
					script.src = (this.parent.options.inGulliver===true)?this.path+'maborak.loader.js':this.path + 'server/maborak.loader.php?load=' + tl.join(',');
//					script.src = this.path + 'maborak.loader.js';
//                    alert(script.src)
					script.type = "text/javascript";
					script.charset = this.parent.charset;
					if (this.type == "module") {
						this.write(script);
					}
				}
    */
			}
			else
			{
				for (var i = this.toLoad.length; i > 0; i--)
				{
					this.name = this.toLoad[this.toLoad.length - i];
					if (!this.isset()) {
						//if (this.options.noWrite === false && this.type!='module')
						//{
       this.src = stringReplace("maborak\\.loader\\.js", "maborak.loader"  + ((BROWSER_CACHE_FILES_UID != "")? "." + BROWSER_CACHE_FILES_UID : "") +  ".js", this.source());
							var script = $dce("script");
							this.parent.dom.capture("tag.head 0").appendChild(script);
							//script.src	=	this.src+"?d="+Math.random();
							script.src = this.src;
							script.type = "text/javascript";
							script.charset = this.parent.charset;
						//}
						if (this.type == "module") {
							this.write(script);
						}
					}
				}
			}
			delete this.Class;
			delete this.file;
			delete this.info;
			delete this.path;
			delete this.toLoad;
			delete this.type;
			delete this.src;
			return true;
		};
		/**
		* es| Obtener la ruta del archivo,modulo a cargar
		*
		* @access	= Private;
		*/
		this.source=function()
		{
			if(this.type=="module")
			{
				return this.path+"module."+this.name+".js";
			}
			else if(this.type=="file")
			{
				var nroute= (this.options.Absolute===true)?this.path:this.path+this.name+"/core/"+this.name+".js";
				return nroute;
			}
			return false;
		};
		/**
		* Probe conditions
		*
		* @access	= Private;
		*/
		this.check	= function()
		{
			if(!this.db || !this.options.Type){
				return false;
			}
			this.type	= this.options.Type.toLowerCase();
			if(this.type=="file")
			{
				this.path	= this.options.Path || this.parent.path_root;
				return true;
			}
			else if(this.type=="module")
			{
				this.Class=(this.options.Instance)?this.options.Instance:((this.options.Class)?this.options.Class.prototype:false);
				if(this.Class===false || !this.Class.info){return false;}
				if(!this.Class.module)
				{
					this.Class.module={};
				}
				this.path	= this.options.Path || this.Class.info.base || false;
				return (this.path===false)?false:true;
			}
			else
			{
				return false;
			}
		};
		/**
		* Prevent duplicate
		*
		* @access	= Private;
		*/
		this.isset	= function()
		{
			if(this.type=="module")
			{
				for(var i=this.db.length;i>0;i--)
				{
					if(this.db[this.db.length-i].name==this.Class.info.name)
					{
						this.file=this.db[this.db.length-i];
						break;
					}
				}
				if(!this.file)
				{
					this.db.push({
						name:this.Class.info.name,
						Class:this.Class,
						_Package_:[]
					});
					this.file=this.db[this.db.length-1];
				}
				for(i=this.file._Package_.length;i>0;i--)
				{
					var nm=this.file._Package_[this.file._Package_.length-i];
					if(nm.name==this.name && nm.type==this.type)
					{
						return true;
					}
				}
				this.Class.module[this.name]=true;
				return false;
			}
			else if(this.type=="file")
			{
				return false;
			}
			return false;
		};
		this.write	= function(script,option)
		{
			this.file._Package_.push({
				type	:this.type,
				loaded	:false,
				name	:this.name,
				script	:script,
				onLoad	:this.options.onLoad || false
			});
		};
		this.Public	= function(Package)
		{
			if(!Package || !Package.info || !Package.info.Class || !Package.info.Name || !Package.info.Type || !Package.content){return false;}
			for(var i=this.db.length;i>0;i--)
			{
				if(this.db[this.db.length-i].name==Package.info.Class)
				{
					this._file_=this.db[this.db.length-i];
					break;
				}
			}
			if(!this._file_)
			{
				return false;
			}
			else
			{
				this.tmpPgk=this._file_.Class.module[Package.info.Name];
				if(this.tmpPgk===true)
				{
					if(typeof Package.content=="function")
					{
						Package.content.prototype.parent=this._file_.Class;
					}
					else if(typeof Package.content=="object")
					{
						Package.content.setParent(this._file_.Class);
						//alert(Package.content+":"+this._file_.Class)
					}
					this._file_.Class.module[Package.info.Name]=Package.content;
					for(i=this._file_._Package_.length;i>0;i--)
					{
						var nm=this._file_._Package_[this._file_._Package_.length-i];
						if(nm.name==Package.info.Name && nm.type==Package.info.Type)
						{
							nm.loaded=true;
							if(!this.parent.browser.isIE)
							{
								this.parent.dom.remove(nm.script);
							}
							delete nm.script;
							if(nm.onLoad)
							{
								nm.onLoad();
							}
							break;
						}
					}
					delete this._file_;
				}
			}
			return true;
		};
	};
	this.fileCore	=function()
	{
		this.db		= [];
	};
	this.extended={
		cookie:function()
		{
			this.set = function(name, value, days, path, domain, secure)
			{
				var expires = -1;
				if(typeof days == "number" && days >= 0) {
					var d = new Date();
					d.setTime(d.getTime()+(days*24*60*60*1000));
					expires = d.toGMTString();
				}
				value = escape(value);
				document.cookie = name + "=" + value + ";"
				+ (expires != -1 ? " expires=" + expires + ";" : "")
				+ (path ? "path=" + path : "")
				+ (domain ? "; domain=" + domain : "")
				+ (secure ? "; secure" : "");
			};
			this.get = function(name)
			{
				var idx = document.cookie.lastIndexOf(name+'=');
				if(idx == -1) { return null; }
				var value = document.cookie.substring(idx+name.length+1);
				var end = value.indexOf(';');
				if(end == -1) { end = value.length; }
				value = value.substring(0, end);
				value = unescape(value);
				return value;
			};
			this.del = function(name)
			{
				this.set(name, "-",0);
			};
		},
		tools:function()
		{
			this.baseURL	=function()
			{
				return window.location;
			};
			this.path_root	=function(jsPath)
			{
				if(this.parent.browser.isIE)
				{
					//alert(jsPath)
					return jsPath+"../..";
				}
				else
				{
					var a = jsPath.split("/");
					a.pop();
					a.pop();
					a.pop();
					return a.join("/");
				}
			};
			this.baseJS	=function(js)
			{
				var Isrc="",script = document.getElementsByTagName('script');
				for (var i=script.length-1; i>=0; i--){
					if (script[i].src && (script[i].src.indexOf(js) != -1))
					{
						Isrc = script[i].src;
						Isrc = Isrc.substring(0, Isrc.lastIndexOf('/'));
						this.parent.info.domBaseJS=script[i];
						break;
					}
				}
				return Isrc+"/";
			};
			this.head=function()
			{
				return document.getElementsByTagName("HTML")[0].getElementsByTagName("HEAD")[0];
			};
			this.createUID=function()
			{
				return Math.random();
			};
			this.expand(this);
		},
		/**
		* @class Manage DOM elements
		* @param {Object} parent Leimnud instance
		*/
		D0M:function()
		{
			this.get_html=function()
			{
				return document.getElementsByTagName('html')[0];
			};
			this.get_doc=function(){
			    var doc = window.document;
			    return (!doc.compatMode || doc.compatMode == 'CSS1Compat')?this.get_html():doc.body;
			};
			/**
			* Capture DOM object from (String || DOM element)
			* @param {string || object} element String.id || DOM object
			* @return DOM object
			*/
			this.element=function(element)
			{//return document.getElementById(element);
//				return (!element)?false:((typeof element=="object")?element:(($(element))?$(element):false));
				return (!element)?false:((typeof element=="object")?element:((document.getElementById(element))?document.getElementById(element):false));
			};
			/**
			* Remove Elements
			* @param {DOM || Array.DOM} DOM Elements
			*/
			this.remove=function(DOM){
				DOM = (DOM.isArray || (DOM.isObject && !DOM.appendChild))?DOM:[DOM];
				for(var i in DOM)
				{
					if(DOM.propertyIsEnumerable(i))
					{
						if(DOM[i].isObject && !DOM[i].appendChild)
						{
							this.remove(DOM[i]);
						}
						else
						{
							var element=this.element(DOM[i]);
							if(element && element.parentNode)
							{
								element.parentNode.removeChild(element);
							}
						}
					}
				}
				return true;
			};
			/**
			* Automate DOM || HTMLCollection => ArrayDOMCollection
			* @param {string || DOM} DOM DOM || HTMLCollection
			* @param {Array} style ArrayDOMCollection
			*/
			this.automateDOMToCollection = function(DOM)
			{
				return ((!DOM.isArray && (DOM.isObject || (this.parent.browser.isIE && !DOM.isObject))) || DOM.isArray)?DOM:[DOM];
			};
			/**
			* Apply styles to DOM object
			* @param {string || DOM} DOM String.id || DOM object
			* @param {object} style es| Objeto con valores de estilo
			*/
			this.setStyle = function(DOM,styles)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				var sizeInPixel=["width","height","left","top","right","bottom",
						 "margin","marginLeft","marginRight","marginTop","marginBottom","marginLeftWidth","marginRightWidth","marginTopWidth","marginBottomWidth",
						 "padding","paddingLeft","paddingRight","paddingTop","paddingBottom","paddingLeftWidth","paddingRightWidth","paddingTopWidth","paddingBottomWidth",
						 "borderLeftWidth","borderRightWidth","borderTopWidth","borderBopttomWidth"
						 ];
				for(var j=0;j<DOM.length;j++)
				{
					var d0m=this.element(DOM[j]);
					if(d0m)
					{
						for (var value in styles)
						{
							if(styles.propertyIsEnumerable(value)){
								//console.info(value+":"+styles[value])
								var val = (typeof styles[value]=="function")?styles[value]():styles[value];
								try{
									var valu= (typeof val!="undefined")?val:" ";
									var prop=value.camelize();
									valu=(sizeInPixel.inArray(prop) && typeof valu==="number")?valu+"px":valu;
									d0m.style[prop] = valu;
								}
								catch(e){}
							}
						}
					}
				}
			};
			/**
			* Apply properties to DOM object
			* @param {string || DOM} DOM String.id || DOM object
			* @param {object} properties es| Objeto con propiedades
			*/
			this.setProperties = function(DOM,properties)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				for(var j=0;j<DOM.length;j++)
				{
					var d0m=this.element(DOM[j]);
					if(d0m)
					{
						for (var value in properties)
						{
							if(properties.propertyIsEnumerable(value)){
								//console.info(value+":"+styles[value])
								var val = properties[value];
								try{
								d0m[value] = (typeof val!="undefined")?val:" ";
								}
								catch(e){}
							}
						}
					}
				}
			};

			/**
			* Get styles from DOM object
			* @param {string || DOM} DOM String.id || DOM object
			* @param {object} style Propertie to get
			*/
			this.getStyle = function(DOM,style)
			{
				var d0m = this.element(DOM),rs;
				if(typeof style=="string")
				{
					var st	= style.split(",");
					rs	= [];
					//alert(style)
					for(var i=0;i<st.length;i++)
					{
						var stringStyle = st[i].camelize();
						//alert(d0m.style[stringStyle])
						var value = d0m.style[stringStyle];
						//console.info(st[i].camelize()+":"+value+"<-- A PEDIR")
						if (!value)
						{
							if(document.defaultView && document.defaultView.getComputedStyle)
							{
								var css = document.defaultView.getComputedStyle(d0m, null);
								value = css ? css.getPropertyValue(stringStyle) : null;
							}
							else if(d0m.currentStyle)
							{
								value = d0m.currentStyle[stringStyle];
							}
						}
						rs.push((value == 'auto')?null:value);
					}
					rs = (rs.length<2)?rs[0]:rs;
				}
				else if(style.isObject)
				{
					rs= {};
					for(i in style)
					{
						if(style.propertyIsEnumerable(i))
						{
							//alert(i+":"+this.getStyle(DOM,i))
							rs[i]=this.getStyle(DOM,i);
						}
					}
				}
				/*if (window.opera && ['left', 'top', 'right', 'bottom'].include(style))
				{
				if (this.getStyle(element, 'position') == 'static')
				{
				value = 'auto';
				};
				}*/
				//console.info(style+":"+rs)
				return rs;
			};
			/**
			* es| Capturar coordenadas X,Y de un elemento DOM
			* @param {String || DOM} DOM String.id || DOM object
			* @param {Boolean} Final Return coordinates x2,y2
			* @return {Object} position Coordinates x,y
			*/
			this.position=function(DOM,Final,StopOnAbsolute)
			{
				DOM = this.element(DOM);
				var position,initial = DOM;
				if(this.parent.dom.getStyle(DOM,"position")=="absolute")
				{
					position={
						x:parseInt(this.parent.dom.getStyle(DOM,"left"),10),
						y:parseInt(this.parent.dom.getStyle(DOM,"top"),10)
					};
				}
				else
				{
					position={
						x:0,
						y:0
					};
					if(!DOM){return position;}
					//var m = parseInt(this.parent.dom.getStyle(DOM,"margin"),10) || 0;

					position.x=parseInt(DOM.offsetLeft,10);
					position.y=parseInt(DOM.offsetTop,10);
					//alert(DOM.offsetParent);
					while (DOM.offsetParent){
						DOM = DOM.offsetParent;
						//alert(StopOnAbsolute)
						var sta = (typeof StopOnAbsolute=="string")?(StopOnAbsolute==DOM.id):StopOnAbsolute;
//						console.info(position.x+":"+position.y+":"+StopOnAbsolute+":"+DOM.id+":"+sta);
						if(sta && (this.parent.dom.getStyle(DOM,"position")=="absolute" || this.parent.dom.getStyle(DOM,"position")=="relative"))
						{
							break;
						}
						else
						{
							var gt = this.position(DOM,false,StopOnAbsolute);
							position.x += gt.x;
							position.y += gt.y;
						}
					}
				}
				//alert(position.x+":"+position.y)
				return (Final===true)?{x:(position.x+parseInt(initial.offsetWidth,10)),y:(position.y+parseInt(initial.offsetHeight,10))}:position;
			};
			/**
			* Transform HTMLCollection to ArrayCollection
			* @param {HTMLCOLLECTION} Collection Html Collection
			* @return {Array} Array Collection;
			*/
			this.CollectionToArray = function(Collection)
			{
				var r=[];
				for(var i=0;i<Collection.length;i++)
				{
					r.push(Collection[i]);
				}
				return r;
			};
			/**
			* Coordinates x,y Mouse
			* @param {Event} event Event
			* @return {Object} position Coordinates x,y
			*/
			this.mouse = function(event)
			{
				return {
					x:(this.parent.browser.isIE)?(window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft):(event.clientX + (window.scrollX || document.body.scrollLeft || 0)),
					y:(this.parent.browser.isIE)?(window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop):(event.clientY + (window.scrollY || document.body.scrollTop ||0))
				};
			};
			/**
			* Set Opacity
			* @param {DOM} DOM
			* @param {integer} integer Opacity
			*/
			this.opacity = function(DOM,opacity)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				for(var j=0;j<DOM.length;j++)
				{
					var d0m=this.element(DOM[j]);
					if(this.parent.browser.isIE)
					{
						this.setStyle(d0m,{
							filter:"alpha(opacity="+opacity+")"
						});
					}
					else
					{
						this.setStyle(d0m,{
							opacity:opacity/100
						});
					}
				}
				return true;
			};
			/**
			* Get Opacity
			* @param {DOM} DOM
			* @param {Float} Float Opacity
			*/
			this.getOpacity = function(DOM)
			{
				var opacity;
				var DOM = this.element(DOM);
				if(opacity = this.getStyle(DOM, 'opacity'))
				{
					return parseFloat(opacity);
				}
				if (opacity = (this.getStyle(DOM, 'filter') || '').match(/alpha\(opacity=(.*)\)/))
				{
					if(opacity[1])
					{
						return parseFloat(opacity[1]) / 100;
					}
				}
				return 1.0;
			};

			/**
			* Null right click
			* @param {DOM || Array[DOM]} DOM Elements
			* @return {Event} event Event false
			*/
			this.nullContextMenu = function(DOM)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				for(var i=0;i<DOM.length;i++)
				{
					DOM[i].oncontextmenu=function(){return false;};
				}
			};

			/**
			* DOM elements, range positions
			* @param {DOM || Array[DOM]} DOM Elements
			* @return {Object} position Coordinates x1:y1,x2:y2
			*/
			this.positionRange = function(DOM,StopOnAbsolute)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				var r={};
				for(var i=0;i<DOM.length;i++)
				{
					var p1 = this.position(DOM[i],false,StopOnAbsolute || false);
					r.x1=(!r.x1 || (p1.x<r.x1))?p1.x:r.x1;
					r.y1=(!r.y1 || (p1.y<r.y1))?p1.y:r.y1;
					var p2 = this.position(DOM[i],true,StopOnAbsolute || false);
					r.x2=(!r.x2 || (p2.x>r.x2))?p2.x:r.x2;
					r.y2=(!r.y2 || (p2.y>r.y2))?p2.y:r.y2;
				}
				return r;
			};
			/**
			* DOM elements, Fix positions if out of range
			* @param {DOM || Array[DOM]} DOM Elements
			* @param {Object} range Current range
			*/
			this.positionRangeFix = function(DOM,range)
			{
				DOM = (DOM.isArray)?DOM:[DOM];
				var r={};
				for(var i=0;i<DOM.length;i++)
				{
					var sL=parseInt(this.parent.dom.getStyle(DOM[i],"left"),10);
					var sT=parseInt(this.parent.dom.getStyle(DOM[i],"top"),10);
					this.parent.dom.setStyle(DOM[i],{
						left:sL+1
					});
				}
				return r;
			};

			/**
			* Capture DOM Element
			* @param {String} DOMstring Object to Search [(id|name|tag).(id|name|tag) (Index=0)]
			* @return Object HEAD
			* leimnud.dom.capture("id.html 0");
			*/
			this.capture=function(DOMstring)
			{
				var str = DOMstring.trim();
				var index = str.split(" ");
				var iDom  = index[0];
				iDom	  = iDom.split(".");
				if(iDom.length<2){return false;}
				index = (index.length<2)?"0":index[index.length-1];
				var all = (index==="*")?true:false;
				var pindex =index.split(",").onlyInt();
				index = pindex.unique();
				var by = iDom[0];
				iDom.splice(0,1);
				var el = iDom.join(".");
				var oDom;
				switch (by)
				{
					case "id":
					return $(el);
					case "name":
					oDom=document.getElementsByName(el);
					break;
					case "tag":
					oDom=document.getElementsByTagName(el);
					break;
					default:
					return false;
				}
				if(all)
				{
					return this.CollectionToArray(oDom);
				}
				else
				{
					if(index.length===0)
					{return false;}
					else if(index.length==1)
					{
						return oDom[0];
					}
					else
					{
						var nDom=[].fill(0,index.length,false);
						for(var i=0;i<oDom.length;i++)
						{
							if(index.inArray(i))
							{
								nDom[index.key(i)]=oDom[i];
							}
						}
						return nDom;
					}
				}
			};
			/**
			* Cancel Event Bubble
			* @param {Event} evt Event in !browser.isIE
			* @return {boolean} false
			*/
			this.bubble = function(allow,evt)
			{
				evt = evt || window.event || false;
				allow = (allow===true)?true:false;
				if(!evt){return false;}
				if(this.parent.browser.isIE)
				{
					evt.cancelBubble=!allow;
				}
				else
				{
					if(allow===false)
					{
						evt.stopPropagation();
					}
					else
					{

					}
				}
				return true;
			};
			/**
			* Load javascript file
			* @param {String} file
			* @return {boolean} result
			*/
			this.loadJs = function(file)
			{
				var jsS = document.getElementsByTagName("script");
				for(var i=0;i<jsS.length;i++)
				{
					if(jsS[i].src.indexOf(file)>-1){
						return false;
					}
				}
				var script = $dce("script");
				this.capture("tag.head 0").appendChild(script);
				script.src = file;
				script.type = "text/javascript";
				script.charset = this.parent.charset;
				return true;
			};
			this.getPageScroll=function()
			{
				return [window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop];
			};
			this.getPageSize = function()
				{
				    var xScroll, yScroll;
					if (window.innerHeight && window.scrollMaxY) {
						xScroll = window.innerWidth + window.scrollMaxX;
						yScroll = window.innerHeight + window.scrollMaxY;
					} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
						xScroll = document.body.scrollWidth;
						yScroll = document.body.scrollHeight;
					} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
						xScroll = document.body.offsetWidth;
						yScroll = document.body.offsetHeight;
					}

					var windowWidth, windowHeight;

					if (self.innerHeight) {	// all except Explorer
						if(document.documentElement.clientWidth){
							windowWidth = document.documentElement.clientWidth;
						} else {
							windowWidth = self.innerWidth;
						}
						windowHeight = self.innerHeight;
					} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
						windowWidth = document.documentElement.clientWidth;
						windowHeight = document.documentElement.clientHeight;
						//alert(windowHeight);
					} else if (document.body) { // other Explorers
						windowWidth = document.body.clientWidth;
						windowHeight = document.body.clientHeight;
					}

					// for small pages with total height less then height of the viewport
					if(yScroll < windowHeight){
						pageHeight = windowHeight;
					} else {
						pageHeight = yScroll;
					}

					// for small pages with total width less then width of the viewport
					if(xScroll < windowWidth){
						pageWidth = xScroll;
					} else {
						pageWidth = windowWidth;
					}
					return [pageWidth,pageHeight];
				};
			this.serializer = this.parent.factory(function(DOM,obj)
			{
				/**
				* Serialize form Element
				* @param {FormElement} form
				* @return {String} serialized
				*/
				this.DOM = DOM;
				this.inObject = (obj===true)?true:false;
				this.serialized = (this.inObject)?{}:"";
				this.parse=function()
				{

				};
				this.rake = function(val)
				{
					if(!val){return val;}
					if(typeof val==="object")
					{
						this.serialized.concat(val);
					}
					else
					{
						this.serialized+=val;
					}
					return true;
				};
				this.form = function()
				{
					var form = this.DOM;
					var serializeds = [];
					serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("input"),this.inObject).input());
					serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("select"),this.inObject).select());
					serializeds.push(new this.parent.dom.serializer(form.getElementsByTagName("textarea"),this.inObject).textarea());
					for (var i=0;i<serializeds.length;i++)
					{
						this.rake(serializeds[i]);
					}
					return this.serialized;
				};
				this.input = function()
				{
					for(var i=0;i<this.DOM.length;i++)
					{
						var inp = this.DOM[i];
						if(inp.name)
						{
							if(inp.type==="text")
							{
								var cn =(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");
								this.rake(cn);
							}
							else if(inp.type==="radio")
							{
								var cn =(inp.checked===true)?(inp.name+"="+escape(inp.value)+"&"):"";
								this.rake(cn);
							}
							else if(inp.type==="checkbox")
							{
								var cn =(inp.checked===true)?inp.name+"="+escape(inp.value)+"&":"";
								this.rake(cn);
							}
							else
							{
								var cn =(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");
								this.rake(cn);
							}
						}
					}
					return this.serialized;
				};
				this.select = function()
				{
					for(var i=0;i<this.DOM.length;i++)
					{
						var inp = this.DOM[i];
						if(inp.name)
						{
							if(inp.multiple===true)
							{
								for(var j=0;j<inp.options.length;j++)
								{
									if(inp.options[j].selected)
									{
										var cn =inp.name+"="+escape(inp.options[j].value)+"&";
										this.rake(cn);
									}
								}
							}
							else
							{
								try
								{
									var cn =inp.name+"="+escape(inp.options[inp.options.selectedIndex].value)+"&";
								}
								catch(e)
								{
									var cn =inp.name+"=&";
								}
								this.rake(cn);
							}
						}
					}
					return this.serialized;
				};
				this.textarea = function()
				{
					for(var i=0;i<this.DOM.length;i++)
					{
						var inp = this.DOM[i];
						if(inp.name)
						{
							var cn =(inp.name+"="+((inp.value)?escape(inp.value):"")+"&");
							this.rake(cn);
						}
					}
					return this.serialized;
				};
				this.expand(this);
				return this;
			});
		}
	};
	this.iphoneBrowser = function()
	{
		this.make=function()
		{
			this.parent.event.add(window,"load",function(){
				document.body.orient="landscape";
				//alert(window.innerWidth)
				window.scrollTo(0,1);
			});
		};
	};
	return this;
};
