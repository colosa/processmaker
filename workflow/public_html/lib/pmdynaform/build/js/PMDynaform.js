"use strict";
/**
 * @class PMDynaform
 * Base class PMDynaform
 * @singleton
 */

 /**
  * @feature support for ie8
  * functions 
  */
  
function getScrollTop(){
	if(typeof pageYOffset!= 'undefined'){
		//most browsers except IE before #9
		return pageYOffset;
	}
	else{
		var B= document.body; //IE 'quirks'
		var D= document.documentElement; //IE with doctype
		D= (D.clientHeight)? D: B;
		return D.scrollTop;
	}
};
  //.trim to support ie8
if (!Array.prototype.filter) {
  Array.prototype.filter = function(fun/*, thisArg*/) {
	'use strict';

	if (this === void 0 || this === null) {
	  throw new TypeError();
	}

	var t = Object(this);
	var len = t.length >>> 0;
	if (typeof fun !== 'function') {
	  throw new TypeError();
	}

	var res = [];
	var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
	for (var i = 0; i < len; i++) {
	  if (i in t) {
		var val = t[i];

		// NOTA: Tecnicamente este Object.defineProperty deben en 
		//        el indice siguiente, como push puede ser 
		//        afectado por la propiedad en object.prototype y 
		//        Array.prototype.
		//       Pero estos metodos nuevos, y colisiones deben ser
		//       raro, así que la alternativas mas compatible.       
		if (fun.call(thisArg, val, i, t)) {
		  res.push(val);
		}
	  }
	}

	return res;
  };
}

if (!String.prototype.trim) {
  (function() {
	// Make sure we trim BOM and NBSP
	var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
	String.prototype.trim = function() {
	  return this.replace(rtrim, '');
	};
  })();
}

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
	var len = this.length >>> 0;

	var from = Number(arguments[1]) || 0;
	from = (from < 0)
		 ? Math.ceil(from)
		 : Math.floor(from);
	if (from < 0)
	  from += len;

	for (; from < len; from++)
	{
	  if (from in this &&
		  this[from] === elt)
		return from;
	}
	return -1;
  };
}

if (!document.getElementsByClassName) {
    document.getElementsByClassName = function (classname) {
        var a = [];
        var re = new RegExp('(^| )' + classname + '( |$)');
        var els = this.getElementsByTagName("*");
        for (var i = 0, j = els.length; i < j; i++)
            if (re.test(els[i].className))
                a.push(els[i]);
        return a;
    };
}

if (!Array.prototype.find) {
    Array.prototype.find = function (callback, thisArg) {
        "use strict";
        var arr = this,
            arrLen = arr.length,
            i;
        for (i = 0; i < arrLen; i += 1) {
            if (callback.call(thisArg, arr[i], i, arr)) {
                return arr[i];
            }
        }
        return undefined;
    };
}

var PMDynaform = {
  VERSION: "0.1.0",
  view: {},
  model:{},
  collection:{},
  Extension: {}, 
  restData:{}
};
/**
 * Extends the PMDynaform namespace with the given `path` and making a pointer
 * from `path` to the given `class` (note that the `path`'s last token will be the pointer visible from outside
 * the definition of the class).
 *
 *      // e.g.
 *      // let's define a class inside an anonymous function
 *      // so that the global scope is not polluted
 *      (function () {
 *          var Class = function () {...};
 *
 *          // let's extend the namespace
 *          PMDynaform.extendNamespace('PMDynaform.package.Class', Class);
 *
 *      }());
 *
 *      // now PMDynaform.package.Class is a pointer to the class defined above
 *
 * @param {string} path
 * @param {Object} newClass
 * @return {Object} The argument `newClass`
 */
PMDynaform.extendNamespace = function (path, newClass) {
	var current,
		pathArray,
		extension,
		i;
	
	if (arguments.length !== 2) {
		throw new Error("Dynaform.extendNamespace(): method needs 2 arguments");
	}

	pathArray = path.split('.');
	if (pathArray[0] === 'PMDynaform') {
		pathArray = pathArray.slice(1);
	}
	current = PMDynaform;

	// create the 'path' namespace
	for (i = 0; i < pathArray.length - 1; i += 1) {
		extension = pathArray[i];
		if (typeof current[extension] === 'undefined') {
			current[extension] = {};
		}
		current = current[extension];
	}

	extension = pathArray[pathArray.length - 1];
	if (current[extension]) {
		
	}
	current[extension] = newClass;
	return newClass;
};

/**
 * Creates an object whose [[Prototype]] link points to an object's prototype (the object is gathered using the
 * argument `path` and it's the last token in the string), since `subClass` is given it will also mimic the
 * creation of the property `constructor` and a pointer to its parent called `superclass`:
 *
 *      // constructor pointer
 *      subClass.prototype.constructor === subClass       // true
 *
 *      // let's assume that superClass is the last token in the string 'path'
 *      subClass.superclass === superClass         // true
 *
 * An example of use:
 *
 *      (function () {
 *          var Class = function () {...};
 *
 *          // extending the namespace
 *          PMDynaform.extendNamespace('PMDynaform.package.Class', Class);
 *
 *      }());
 *
 *      (function () {
 *          var NewClass = function () {...};
 *
 *          // this class inherits from PMDynaform.package.Class
 *          PMDynaform.inheritFrom('PMDynaform.package.Class', NewClass);
 *
 *          // extending the namespace
 *          PMDynaform.extendNamespace('PMDynaform.package.NewClass', NewClass);
 *
 *      }());
 *
 * @param {string} path
 * @param {Object} subClass
 * @return {Object}
 */
PMDynaform.inheritFrom = function (path, subClass) {
	var current,
		extension,
		pathArray,
		i,
		prototype;

	if (arguments.length !== 2) {
		throw new Error("PMDynaform.inheritFrom(): method needs 2 arguments");
	}

	// function used to create an object whose [[Prototype]] link
	// points to `object`
	function clone(object) {
		var F = function () {};
		F.prototype = object;
		return new F();
	}

	pathArray = path.split('.');
	if (pathArray[0] === 'PMDynaform') {
		pathArray = pathArray.slice(1);
	}
	current = PMDynaform;

	// find that class the 'path' namespace
	for (i = 0; i < pathArray.length; i += 1) {
		extension = pathArray[i];
		if (typeof current[extension] === 'undefined') {
			throw new Error("PMDynaform.inheritFrom(): object " + extension + " not found, full path was " + path);
		}
		current = current[extension];
	}

	prototype = clone(current.prototype);

	prototype.constructor = subClass;
	subClass.prototype = prototype;
	subClass.superclass = current;
};

String.prototype.capitalize = function() {
	return this.toLowerCase().replace(/(^|\s)([a-z])/g, function(m, p1, p2) { return p1 + p2.toUpperCase(); });
};

jQuery.fn.extend({
	setLabel : function (newLabel, col) {
		var field = getFieldById(this.attr("id")) || null;
		if (typeof newLabel === "string" && field) {
			field.setLabel(newLabel, col);
		}
		return this;
	},
	getLabel : function (col) {
		var field = getFieldById(this.attr("id")) || null;
		if ( field ) {
			return field.getLabel(col);
		}
		return null;
  	},
	setValue : function (value, row, col) {
		var field = getFieldById(this.attr("id")) || null;
		if ( field ) {
			if (field.model.get("type") === "grid") {
				field.setValue(value, row, col);
			}else{
				field.setValue(value);
			}
		}
		return this;
	},
	setText : function (value, row, col) {
		var field = getFieldById(this.attr("id")) || null;
		if ( field ) {
			if (field.model.get("type") === "grid") {
				field.setText(value, row, col);
			}else{
				field.setText(value);
			}
		}
		return this;
	},
	getValue : function (row,col) {
		var field = getFieldById(this.attr("id")) || null, val = null, type;
		if (field) {
			type = field.model.get("type");
			if (val === null && field.model.get("type") === "grid" && row !== undefined && col !== undefined){
				val = field.getValue(row, col);
			}
			if (val === null && field.getData ){
				val = field.getValue();
			}
			if (val === null || val === undefined) {
				if (type === "image") {
					return field.getSrc();
				}
				if (field && field.getLabel) {
					val = field.getLabel();	
				}
			}
		}else{
			null;
		}
		return val;
  	},
	setOnchange : function ( handler ) {
		var item;
		if (getFieldById(this.attr("id"))){
			item = getFieldById(this.attr("id"));
		} else if ( getFormById(this.attr("id")) ) {
			item = getFormById(this.attr("id"));
		}
		if ( typeof handler === "function" && item) {
			if (item.model.get("type")!== "label"){
				item.setOnChange(handler);
			}
		}
		return this;
	},
	getInfo : function () {
		var field = getFieldById(this.attr("id")) || null;
		if (field){
			return field.getInfo();
		}
		return null;
	},
	setHref : function (value) {
		var field = getFieldById(this.attr("id")) || null;
		if (field.model.get("type") === "link") {
			field.setHref(value);
		}
		return this;
	},
	getHref : function () {
		var field = getFieldById(this.attr("id")) || null;
		if (field.model.get("type") === "link") {
			return field.getHref();
		}
		return this;
	},	
	setRequired : function (field) {
		//console.log("test method field");
	},
	required : function (field) {
		//console.log("test method field");
	},
	getText : function (row, col) {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field.model.get("type") !== "grid") {
			if ( field && field.getData ){
				val = field.getText();
			}
		} else {
			if (field.model.get("type") === "grid") {
				val = field.getText(row,col);
			}else{
				val = null;
			}
		}
		return val;
	},
	disableValidation : function (col) {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field && field.disableValidation) {
			field.disableValidation(col);
		}
		return this;
	},
	enableValidation : function (col) {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field && field.enableValidation) {
			field.enableValidation(col);
		}
		return this;
	},
	getControl : function (row, col) {
		var field = getFieldById(this.attr("id")) || null;
		if ( field ) {
			return field.getControl(row, col);
		}
		return [];
	},
	getLabelControl : function () {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field){
			field.getLabelControl();
		}
		return this;
	},
	getHintHtml : function () {
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field){
			html = field.$el.find(".glyphicon-info-sign");
		}
		return $(html);
	},
	getSummary : function (col) {
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			return field.getSummary(col);
		}
		return this;
	},
	getNumberRows : function (){
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			return field.gridtable.length;
		}
		return this;
	},
	addRow : function (data){
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			field.addRow(data);
		}
		return this;	
	},
	deleteRow : function (row){
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			if (!row){
				row = field.gridtable.length;
			}
			field.deleteRow(row);
		}
		return this;	
	},
	onBeforeAdd : function () {
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			if ( typeof handler === "function" ) {
				field.setOnBeforeAddCallback(handler);
			}
		}
	},
	onAddRow : function (handler) {
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			if ( typeof handler === "function" ) {
				field.setOnAddRowCallback(handler);
			}
		}
	},
	onDeleteRow : function (handler) {
		var field = getFieldById(this.attr("id")) || null, html = [];
		if (field && field.model.get("type")=="grid"){
			if ( typeof handler === "function" ) {
				field.setOnDeleteRowCallback(handler);
			}
		}
	},
	hideColumn : function (col) {
		var field = getFieldById(this.attr("id")) || null, html = [], table, row, cell, label;
		table = field.gridtable;
		if (col > 0 && col <= field.columnsModel.length) {
			if (field.hiddenColumns.indexOf(col) === -1) {
				field.hiddenColumns.push(col);
			}
			for (var i = 0 ; i < table.length ; i +=1) {
				row = table[i];
				cell = row[col-1];
				if (cell.model.get("type") != "hide"){
					cell.$el.hide();
					if ( cell.$el.parent().length ) {
						cell.$el.parent().hide();
					}
					if ( cell.model.get("operation") !== "" ) {
						field.$el.find("."+"function-result-"+cell.model.get("columnName")).hide();
					}
				}
			}
			if (field.$el.find(".field-operation-result")){
				field.$el.find(".field-operation-result").eq(col-1).hide();
			}
			for (var i= 0 ; i < field.domTitleHeader.length ; i +=1) {
				if (field.domTitleHeader[i].find(".title-column").text() == cell.model.get("label")){
					field.domTitleHeader[i].hide();
				}
			}
		}
	},
	showColumn : function (col) {
		var field = getFieldById(this.attr("id")) || null, html = [], table, row, cell, label, index;
		table = field.gridtable;
		if (col > 0 && col <= field.columnsModel.length) {
			index = field.hiddenColumns.indexOf(col); 
			if( index > -1){
				field.hiddenColumns.splice(index,1);
			}
			for (var i = 0 ; i < table.length ; i +=1) {
				row = table[i];
				cell = row[col-1];
				if (cell.model.get("type") != "hide"){
					cell.$el.show();
					if (cell.$el.parent().length){
						cell.$el.parent().show();
					}
				}
			}
			if (cell){
				if ( cell.model.get("operation") !== "" ) {
					field.$el.find("."+"function-result-"+cell.model.get("columnName")).show();
				}
				if (field.$el.find(".field-operation-result")){
					field.$el.find(".field-operation-result").eq(col-1).show();
				}
				for (var i= 0 ; i < field.domTitleHeader.length ; i +=1) {
					if (field.domTitleHeader[i].find(".title-column").text() == cell.model.get("label")){
						field.domTitleHeader[i].show();
					}
				}
			}
		}
	}, 
	getData : function () {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field && field.getData) {
			return field.getData();
		}
		return this;
	},
	getDataLabel : function () {
		var field = getFieldById(this.attr("id")) || null, val;
		if (field && field.getDataLabel) {
			return field.getDataLabel();
		}
		return this;
	}
});
(function(){
	/*
	 * @param {String}
	 * The following key selectors are availables for the
	 * getField and getGridField methods
	 * - Using '#', is possible select a field with the identifier of the field
	 * - Using ''. is possible select a field with the className of the field
	 * - Putting 'attr[name="my-name"]' is possible select fields with the same name attribute
	 *
	 **/
	var Selector = function (options) {
		this.onSupportSelectorFields = null;
		this.fieldType = null;
		this.fields = [];
		this.queries = [];
		this.forms = [];

		Selector.prototype.init.call(this, options);
	};
	Selector.prototype.init = function (options) {
		var defaults = {
			fields: [],
			queries: [],
			forms : [],
			onSupportSelectorFields: {
				text: "onTextField",
				textarea: "onTextAreaField"
			}
		};

		$.extend(true, defaults, options);
		
		this.setOnSupportSelectorFields(defaults.onSupportSelectorFields)
			.setFields(defaults.fields)
			.setForms(defaults.forms)
			.applyGlobalSelectors();
	};
	Selector.prototype.addQuery = function (query) {
		if (typeof query === "string") {
			this.queries.push(query);
		} else {
			throw new Error ("The query selector must be a string");
		}

		return this;
	};
	Selector.prototype.setOnSupportSelectorFields = function (support) {
		if (typeof support === "object") {
			this.onSupportSelectorFields = support;
		} else {
			throw new Error ("The parameter for the support fields is wrong");
		}

		return this;
	};
	Selector.prototype.setFields = function (fields) {
		if (typeof fields === "object") {
			this.fields = fields;
		}

		return this;
	};

	Selector.prototype.setForms = function (forms) {
		if (jQuery.isArray(forms)){
			this.forms = forms;
		}
		return this;
	};

	Selector.prototype.onTextField = function (selector) {
		//console.log("selector text field", selector);

		return this;
	};
	Selector.prototype.onTextAreaField = function (selector) {
		//console.log("selector textarea field", selector);
		
		return this;
	};

	Selector.prototype.findFieldById = function (selectorAttr) {
		var i,
		fieldFinded = null;

		searching:
		for (i=0; i<this.fields.length; i+=1) {
			if (this.fields[i].model.id === selectorAttr) {
				fieldFinded = this.fields[i];
				break searching;
			}
		}
		return fieldFinded;
	};
	Selector.prototype.findFormById = function (selectorId) {
		var i;
		for ( i = 0 ; i < this.forms.length ; i+=1 ) {
			if ( this.forms[i].model.id === selectorId ) {
				return this.forms[i];
			}
		}
		return null;
	},
	Selector.prototype.findFieldByName = function (selectorAttr) {
		var i,
		fieldFinded = [];

		for (i=0; i<this.fields.length; i+=1) {
			if (this.fields[i].model.get("name") === selectorAttr) {
				fieldFinded.push(this.fields[i]);
			}
		}
		return fieldFinded;
	};
	Selector.prototype.findFieldByAttribute = function (parameter, value) {
		var i,
		fieldFinded = [];

		for (i=0; i<this.fields.length; i+=1) {
			if (this.fields[i].model.attributes[parameter]) {
				if (this.fields[i].model.get(parameter) === value) {
					fieldFinded.push(this.fields[i]);
				}
			}
		}
		return fieldFinded;
	};
	Selector.prototype.applyGlobalSelectors = function () {
		var sel,
		i,
		that = this;
		
		window.getFieldByAttribute = function (attr, value) {
			that.addQuery(attr + ": " + value);
			return that.findFieldByAttribute(attr, value);
		};
		window.getFieldById = function (query) {
			that.addQuery("id: " + query);
			return that.findFieldById(query);
		};
		window.getFieldByName = function (query) {
			that.addQuery("name: " + query);
			return that.findFieldByName(query);
		};
		window.getFormById = function (query) {
			that.addQuery("id: " + query);
			return that.findFormById(query);
		}

		return this;
	};

	PMDynaform.extendNamespace("PMDynaform.core.Selector", Selector);
}());
(function(){
	
	var Utils = {
		generateID: function () {
			var rand = function (min, max) {
	            // Returns a random number
	            //
	            // version: 1109.2015
	            // discuss at: http://phpjs.org/functions/rand
	            // +   original by: Leslie Hoare
	            // +   bugfixed by: Onno Marsman
	            // %          note 1: See the commented out code below for a
	            // version which will work with our experimental
	            // (though probably unnecessary) srand() function)
	            // *     example 1: rand(1, 1);
	            // *     returns 1: 1

	            // fix for jsLint
	            // from: var argc = arguments.length;
	            if (typeof min === "undefined") {
	                min = 0;
	            }
	            if (typeof max === "undefined") {
	                max = 999999999;
	            }
	            return Math.floor(Math.random() * (max - min + 1)) + min;
	        },
	        uniqid = function (prefix, more_entropy) {
	        	var php_js = {},
	        	retId,
				formatSeed;
	            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	            // +    revised by: Kankrelune (http://www.webfaktory.info/)
	            // %        note 1: Uses an internal counter (in php_js global) to avoid collision
	            // *     example 1: uniqid();
	            // *     returns 1: 'a30285b160c14'
	            // *     example 2: uniqid('foo');
	            // *     returns 2: 'fooa30285b1cd361'
	            // *     example 3: uniqid('bar', true);
	            // *     returns 3: 'bara20285b23dfd1.31879087'
	            if (typeof prefix === 'undefined') {
	                prefix = "";
	            }
	            
                formatSeed = function (seed, reqWidth) {
                    var tempString = "",
                    i;

                    seed = parseInt(seed, 10).toString(16); // to hex str
                    if (reqWidth < seed.length) { // so long we split
                        return seed.slice(seed.length - reqWidth);
                    }
                    if (reqWidth > seed.length) { // so short we pad
                        // jsLint fix
                        tempString = "";
                        for (i = 0; i < 1 + (reqWidth - seed.length); i += 1) {
                            tempString += "0";
                        }
                        return tempString + seed;
                    }
                    return seed;
                };

	            // BEGIN REDUNDANT
	            if (!php_js) {
	                php_js = {};
	            }
	            // END REDUNDANT
	            if (!php_js.uniqidSeed) { // init seed with big random int
	                php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
	            }
	            php_js.uniqidSeed += 1;

	            retId = prefix; // start with prefix, add current milliseconds hex string
	            retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
	            retId += formatSeed(php_js.uniqidSeed, 5); // add seed hex string
	            if (more_entropy) {
	                // for more entropy we add a float lower to 10
	                retId += (Math.random() * 10).toFixed(8).toString();
	            }

	            return retId;
	        },
	        sUID;

		    do {
		        sUID = uniqid(rand(0, 999999999), true);
		        sUID = sUID.replace('.', '0');
		    } while (sUID.length !== 32);
		    
	    	return "pmd" + sUID;
		},
		generateName: function (type) {
			return type + "[" + PMDynaform.core.Utils.generateID() + "]";
		}
	};
	PMDynaform.extendNamespace("PMDynaform.core.Utils", Utils);
	
}());
(function() {
    var Validators = {
        /*
         * 
         * @type type
         */
        requiredText: {
            message: "This field is required",
            fn: function(value) {
                value = value.trim();
                if (value === null || value.length === 0 || /^\s+$/.test(value)) {
                    return false;
                }
                return true;
            }
        },
        /*
         * 
         * @type type
         */
        requiredDropDown: {
            message: "This field is required",
            fn: function(value) {
                value = value.trim();
                if (value === null || value.length === 0 || /^\s+$/.test(value)) {
                    return false;
                }
                return true;
            }
        },
        requiredCheckBox: {
            message: "This field is required",
            fn: function(value) {
                if (typeof value === "number") {
                    var bool = (value > 0)? true: false;    
                } else {
                    bool = false;
                }
                return bool;
            }
        },
		requiredCheckGroup: {
			message: "This field is required",
			fn: function(value) {
				if (typeof value === "number") {
					var bool = (value > 0)? true: false;    
                } else {
                    bool = false;
                }
                return bool;
            }
        },
        requiredFile: {
            message: "This field File is required",
            fn: function(value) {
                value = value.trim();
                if (value === null || value.length === 0 || /^\s+$/.test(value)) {
                    return false;
                }
                return true;
            }
        },
        requiredRadioGroup: {
            message: "This field is required",
            fn: function(value) {
                if (typeof value === "number") {
                    var bool = (value > 0)? true: false;    
                } else {
                    bool = false;
                }
                return bool;
            }
        },
        integer: {
            message: "Invalid value for the integer field",
            mask: /[\d\.]/i,
            fn: function(n) {
                return (n != "" && !isNaN(n) && Math.round(n) == n);
            }
        },
        float: {
            message: "Invalid value for the float field",
            fn: function(n) {
                return  /^-?\d+\.?\d*$/.test(n);
            }
        },
        string: {
            fn: function(string){
                return true;
            }
        },
        boolean: {
            fn: function(string) {
                return true;
            }
        },
        maxLength: {
            message: "The maximum length are ",
            fn: function(value, maxLength) {
                var maxLen;
                if (typeof maxLength !== "number") {
                    throw new Error ("The parameter maxlength is not a number");
                }
                maxLen = (value.toString().length <= maxLength)? true : false;                
                return maxLen;                                
            }
        },

        /*
         * 
         * @type type
         */
        /*
         * 
         * @type type
         */
        /*
        email: {
            message: "Invalid value for field email",
            fn: function(value) {
                if (!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value))) {
                    return false;
                }
                return true;
            }
        },*/

        /*
         * 
         * @type type
         */
        
        /*datetime: {
            message: "",
            format: "",
            fn: function(ano, mes, dia) {
                var date = new Date(ano, mes, dia);
                if (!isNaN(date)) {
                    return false;
                }
                return true;
            }
        },*/
        
        /*
         * 
         * @type type
         */
        /*password: {
            message: "",
            fn: function(field) {
                if (!/^(?=.*\d)(?=.*[a-z])\w{8,}/i.test(field.value)) {
                    return false;
                }
                return true;
            }
        },*/
        /*mask: {
            fn: function(value, mask) {
                
            }
        },*/
        /*domain: {
            message: "The value is not valid for the options domain",
            fn: function(value, options) {
                var i, 
                validated = true
                for (i=0; i<options.length; i+=1) {
                    if ( (value !== null) && (options[i].value.toString() === value.toString())){
                        validated = false;
                    }
                }

                return true;
            }
        }*/
    };
    
    PMDynaform.extendNamespace("PMDynaform.core.Validators", Validators);
}());

(function () {
    var Project = function (options) {
        this.model = null;
        this.view = null;
        this.data = null;
        this.fields = null;
        this.keys = null;
        this.token = null;
        this.renderTo = null;
        this.urlFormat = null;
        this.endPointsPath = null;
        this.forms = null;
        this.externalLibs = null;
        this.dependentLibraries = null;
        this.submitRest = null;
        this.onSubmitForm = new Function();
        this.language = options.language || null;
        Project.prototype.init.call(this, options);
    };

    Project.prototype.init = function (options) {
        var defaults = {
            submitRest: false,
            data: {},
            urlFormatMobile : "{server}/api/1.0/{workspace}/{endPointPath}",
            urlFormat: "{server}/{apiName}/{apiVersion}/{workspace}/{keyProject}/{projectId}/{endPointPath}",
            keys: {
                server: "",
                projectId: "",
                workspace: "",
                keyProject: "project",
                apiName: "api",
                apiVersion: "1.0"
            },
            token: {
                accessToken: "",
                clientId: "x-pm-local-client",
                clientSecret: "",
                expiresIn: "",
                refreshToken: "",
                scope: "",
                tokenType: "bearer"
            },
            endPointsPath: {
                project: "",
                createVariable: "process-variable",
                variableList: "process-variable",
                /**
                 * @key {var_uid} Defines the identifier of the variable
                 * The Endpoint is for get all information about Variable
                 **/
                variableInfo: "process-variable/{var_uid}",
                /**
                 * @key {var_name} Defines the variable name
                 * The Endpoint executes the query associated to variable
                 **/
                executeQuery: "process-variable/{var_name}/execute-query",
                /**
                 *
                 * @key {field_name} Defines the field name
                 * The Endpoint uploads a file
                 **/
                uploadFile: "uploadfile/{field_name}",
                executeQuerySuggest: "process-variable/{var_name}/execute-query-suggest",
                fileStreaming : "en/neoclassic/cases/casesStreamingFile?actionAjax=streaming&a={caseID}&d={fileId}",
                getAllDataCase :"case/{caseID}/variables",
                imageDownload: 'light/case/{caseID}/download64',
                fileDownload: "case/{caseID}/file/{fileID}",
                imageInfo: "light/case/{caseID}/download64",
                getImageGeo: "light/case/{caseID}/download64"
            },
            externalLibs: "",
            renderTo: document.body,
            onLoad: new Function()
        };
        this.urlFormatStreaming  = "{server}/sys{workspace}/{endPointPath}";
        if (!_.isEmpty(options.data) && options.data.items[0] && options.data.items[0]["externalLibs"]) {
            this.externalLibs = options.data.items[0]["externalLibs"].split(",");
        }
        var that = this;
        //start loading
        $("body").append("<div class='pmDynaformLoading' style='position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url(/lib/img/loading.gif) 50% 50% no-repeat #f9f9f9;'></div>");
        this.setExternalLibreries(this.externalLibs, 0, function () {
            jQuery.extend(true, defaults, options);
            that.setData(defaults.data);
            that.setLanguage();
            that.setUrlFormat(defaults.urlFormat);
            that.setUrlFormatMobile(defaults.urlFormatMobile);
            that.setKeys(defaults.keys);
            that.setToken(defaults.token);
            that.setRenderTo(defaults.renderTo);
            that.setEndPointsPath(defaults.endPointsPath);
            that.createWebServiceManager();
            that.checkMobileData();
            that.submitRest = defaults.submitRest;
            if (!PMDynaform.core.ProjectMobile){
                that.checkGeoMapsLibraries(defaults.onLoad);
            }else{
                that.checkGeoMapsLibraries();
            }
            //stop loading
            $("body").find(".pmDynaformLoading").remove();
        });
    };
    Project.prototype.checkMobileData = function () {
        if (!PMDynaform.core.ProjectMobile){
            this.mobileDataControls = this.loadAllDataCase();
        }
        return this;
    };
    Project.prototype.setLanguage = function () {       
        if(window.sysLang){
            this.language = window.sysLang;            
        }
        return this;
    };
    Project.prototype.setExternalLibreries = function (libs, i, fn) {
        var item, type, that = this;
        if (jQuery.isArray(libs) && i < libs.length) {
            item = libs[i].trim();
            type = item.substring(item.lastIndexOf(".") + 1);
            switch (type) {
                case "js" :
                    var script = document.createElement('script');
                    script.onload = function () {
                        that.setExternalLibreries(libs, i + 1, fn);
                    };
                    script.type = 'text/javascript';
                    script.src = item;
                    document.head.appendChild(script);
                    break;
                case "css" :
                    var link = document.createElement("link");
                    link.onload = function () {
                        that.setExternalLibreries(libs, i + 1, fn);
                    };
                    link.rel = "stylesheet";
                    link.href = item;
                    document.head.appendChild(link);
                    break;
            }
        } else {
            fn();
        }
    };
    Project.prototype.setData = function (data) {
        if (typeof data === "object") {
            this.data = data;
        }
        if (this.view) {
            this.destroy();
            this.loadProject();
        }

        return this;
    };
    Project.prototype.setData2 = function (data) {
        this.view.setData2(data);
        return this;
    };
    Project.prototype.setUrlFormat = function (url) {
        if (typeof url === "string") {
            this.urlFormat = url;
        }
        return this;
    };
    Project.prototype.setUrlFormatMobile = function (url) {
        if (typeof url === "string") {
            this.urlFormatMobile = url;
        }
        return this;
    };
    Project.prototype.setKeys = function (keys) {
        var keysFixed = {},
                key,
                leftBracket;
        if(!PMDynaform.core.ProjectMobile){
            if(keys.server.indexOf("http://") == -1 || keys.server.indexOf("https://") == -1){
                keys.server = location.protocol+"//"+keys.server;
            }           
        }
        if (typeof keys === "object") {
            for (key in keys) {
                leftBracket = (keys[key][0] === "/") ? keys[key].substring(1) : keys[key];
                keysFixed[key] = (leftBracket[leftBracket.length - 1] === "/") ? leftBracket.substring(0, leftBracket.length - 1) : leftBracket;
            }
            //keysFixed.server = keysFixed.server.replace(/\https:\/\//, "").replace(/\http:\/\//, "");
            this.keys = keysFixed;
        }

        return this;
    };
    Project.prototype.setToken = function (objToken) {
        if (typeof objToken === "object") {
            this.token = objToken;
        }

        return this;
    };
    Project.prototype.setRenderTo = function (to) {
        this.renderTo = to;

        return this;
    };
    Project.prototype.setEndPointsPath = function (endpoints) {
        var leftBracket,
                point,
                endpointsVerified = {};

        for (point in endpoints) {
            if (typeof endpoints[point] === "string") {
                leftBracket = (endpoints[point][0] === "/") ? endpoints[point].substring(1) : endpoints[point];
                endpointsVerified[point] = (endpoints[point][endpoints[point].length - 1] === "/") ?
                        endpoints[point].substring(0, endpoints[point].length - 1) :
                        endpoints[point];
            } else {
                throw new Error("The endpoint path is not correct, " + endpoints[point]);
            }
        }
        this.endPointsPath = endpointsVerified;

        return this;
    };
    Project.prototype.checkGeoMapsLibraries = function (onload) {
        var i,
                libs = [],
                enableGeoMap = false,
                searchingMap,
                forms = this.data.items;

        searchingMap = function (fields) {
            var j,
                    k,
                    l,
                    dependent = [
                        "geomap",
                        "other",
                        "location"
                    ];

            outer_loop:
            for (j = 0; j < fields.length; j += 1) {
                for (k = 0; k < fields[j].length; k += 1) {
                    if ($.inArray(fields[j][k].type, dependent) >= 0) {
                        enableGeoMap = true;
                        break outer_loop;
                    } else if (fields[j][k].type === "form") {
                        searchingMap(fields[j][k].items);
                    }
                }
            }
        };

        for (i = 0; i < forms.length; i += 1) {
            searchingMap(forms[i].items);
        }

        if (enableGeoMap) {
            this.loadGeoMapDependencies(onload);
        } else {
            this.loadProject(onload);
        }

        return this;
    };
    Project.prototype.checkScript = function () {
        var i, j, k, code, model, scriptCode = "", rows, item, type,row;
        for (i = 0; i < this.forms.length; i += 1) {
            rows = this.forms[i].model.get("items");
            if (!_.isEmpty(this.forms[i].model.get("script"))) {
                scriptCode = scriptCode.concat(this.forms[i].model.get("script").code);
            }
            for (j = 0 ; j < rows.length ; j +=1) {
                row = rows[j];
                for (var k = 0 ; k < row.length; k +=1) {
                    item = row[k];
                    if ( item.type === "form" ) {
                        if (!_.isEmpty(item.script)) {
                            scriptCode = scriptCode.concat(item.script.code);
                        }
                    }
                }
            }
        }
        if (scriptCode.trim().length){
            code = new PMDynaform.core.Script({
                script: scriptCode
            });
            code.render();
        }
    };
    Project.prototype.setAllFields = function (fields) {
        if (typeof fields === "object") {
            this.fields = fields;
            this.selector.setFields(fields);
            //console.log("set fields");
        }
        return this;
    };
    Project.prototype.loadProject = function (onload) {
        var that = this;
        
        this.model = new PMDynaform.model.Panel(this.data);
        this.view = new PMDynaform.view.Panel({
            tagName: "div",
            renderTo: this.renderTo,
            model: this.model,
            project: this
        });
        if (onload && typeof onload == "function"){
            onload();
        }
        this.forms = this.view.getPanels();
        this.createGlobalPmdynaformClass(this.view);
        this.createSelectors();
        this.checkScript();
        this.createMessageLoading();
        that.view.afterRender();
        that.view.$el.find(".pmdynaform-form-message-loading").remove();
        $("#shadow-form").remove();

        return this;
    };
    Project.prototype.createMessageLoading = function () {
        var msgTpl = _.template($('#tpl-loading').html());
        this.view.$el.prepend(msgTpl({
            title: "Loading",
            msg: "Please wait while the data is loading..."
        }));
        this.view.$el.find("#shadow-form").css("height", this.view.$el.height() + "px");
    };
    Project.prototype.createSelectors = function () {
        var i,
                eachForm,
                fields = [];

        eachForm = function (items) {
            var jFields;

            for (jFields = 0; jFields < items.length; jFields += 1) {
                if (items[jFields].model.get("type") === "form") {
                    eachForm(items[jFields].formView.getFields());
                } else {
                    fields.push(items[jFields]);
                }
            }
        };

        //Each Form
        for (i = 0; i < this.forms.length; i += 1) {
            eachForm(this.forms[i].getFields());
        }

        this.fields = fields;
        this.selector = new PMDynaform.core.Selector({
            fields: fields,
            forms: this.forms
        });

        return this;
    };
    Project.prototype.createGlobalPmdynaformClass = function (form) {

    };
    Project.prototype.loadGeoMapDependencies = function (onload) {
        var i,
                auxClass,
                instanceClass,
                that = this,
                loadScript = true,
                libs = "";

        libs = document.body.getElementsByTagName("script");
        outer_script:
                for (i = 0; i < libs.length; i += 1) {
            if ($(libs[i]).data) {
                if ($(libs[i]).data("script") === "google") {
                    loadScript = false;
                    break outer_script;
                }
            }
        }
        if (loadScript) {
            auxClass = function (params) {
                this.project = params.project;
            };
            auxClass.prototype.load = function () {
                this.project.loadProject(onload);
            };
            window.pmd = new auxClass({project: this});
            var script = document.createElement('script');
            script.type = 'text/javascript';
            $(script).data("script", "google");
            script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=pmd.load';
            document.body.appendChild(script);
        } else {
            this.loadProject(onload);
        }
        return this;
    };
    Project.prototype.registerKey = function (key, value) {
        if ((typeof key === "string") && (typeof value === "string")) {
            if (!this.keys[key]) {
                this.keys[key] = value;
            } else {
                throw new Error("The key already exists.");
            }
        } else {
            throw new Error("The parameters must be strings.");
        }

        return this;
    };
    Project.prototype.getEndPoint = function (type) {
        return this.endPointsPath[type];
    };
    Project.prototype.setModel = function (model) {
        if (model instanceof Backbone.Model) {
            this.model = model;
        }
        return this;
    };
    Project.prototype.setView = function (view) {
        if (view instanceof Backbone.View) {
            this.view = view;
        }
        return this;
    };
    Project.prototype.getForms = function () {
        var forms,
                panels = [];

        if (this.view instanceof PMDynaform.view.Panel) {
            forms = this.view.getPanels();
        }

        return forms;
    };
    Project.prototype.getData = function () {
        var formData = this.view.getData();

        return formData;
    };
    Project.prototype.destroy = function () {
        this.view.$el.remove();

        return this;
    };
    /*
    Mobile project methods
     */
    Project.prototype.loadAllDataCase = function() {
        var restClient, endpoint, url, that=this, resp = {};
        if(window.app_uid){
            return this.webServiceManager.loadAllDataCase();
        }
        return resp;       
    };

    Project.prototype.getFullEndPoint= function (urlEndpoint) {
        var k, 
        keys  = this.keys,
        urlFormat = urlEndpoint;
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{"+ k +"}" ,"g"), keys[k]);               
            }
        }
        return urlFormat;
    };
    Project.prototype.getFullURLMobile = function (endpoint) {
        var k, 
        keys  = this.keys,
        urlFormat = this.urlFormatMobile;
        urlFormat=urlFormat.replace(/{endPointPath}/, endpoint);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {                           
                    urlFormat = urlFormat.replace(new RegExp("{"+ k +"}" ,"g"), keys[k]);               
            }
        }
        urlFormat = window.location.protocol + "//" + urlFormat.replace(/{endPointPath}/, endpoint);
        return urlFormat;
    };
    Project.prototype.getFullURL = function (endpoint) {
        var k,
                keys = this.keys,
                urlFormat = this.urlFormat;

        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{" + k + "}", "g"), keys[k]);
                //endPointFixed =endpoint.replace(new RegExp(variable, "g"), keys[variable]);   
            }
        }
        urlFormat = window.location.protocol + "//" + urlFormat.replace(/{endPointPath}/, endpoint);        
        if (urlFormat.indexOf("file") > -1){
            urlFormat = urlFormat.replace(/file/g, "http");
        }
        return urlFormat;
    };

    Project.prototype.getFullURLStreaming = function (endpoint) {
        var k, 
        keys  = this.keys,
        urlFormat = this.urlFormatStreaming;
        urlFormat=urlFormat.replace(/{endPointPath}/, endpoint);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {                           
                    urlFormat = urlFormat.replace(new RegExp("{"+ k +"}" ,"g"), keys[k]);               
            }
        }
        urlFormat = window.location.protocol + "//" + urlFormat.replace(/{endPointPath}/, endpoint);
        return urlFormat;
    };
    Project.prototype.createWebServiceManager = function () {
        var keys1= {
            server: this.keys.server,
            processID: this.keys.projectId,
            taskID:window.app_uid? window.app_uid:null,
            caseID:window.app_uid? window.app_uid:null,
            workspace: this.keys.workspace,
            formID: null,
            keyProject : "project",            
            stepID : null,
            delIndex: null
        };

        this.webServiceManager = new PMDynaform.implements.WebServiceManager({
            keys : keys1,
            token : this.token,
            language : this.language        
        });    
    };

    PMDynaform.extendNamespace("PMDynaform.core.Project", Project);

}());
 (function(){
	var WebServiceManager = function(options) {
		/*
			options.keys
			options.endPoints
			options.urlBase
			options.token
		 */
		this.options = options || {};
		this.options.endPoints ={			
			dynaformDefinition : "light/project/{processID}/dynaform/{formID}",
			jsonDynaforms : "light/project/{processID}/dynaforms",			
			startCase :"light/process/{processID}/task/{taskID}/start-case",
			newTokens :"oauth/token",
			caseTypeList :"case/{caseID}/dynaform/{typeList}",
			loadDynaform :"case/{caseID}/dynaform/{formID}/data",
			getFormData :"case/{caseID}/dynaform/{formID}/data",
			getAllDataCase :"case/{caseID}/variables",									
			submitFormCase :"cases/{caseID}/variable", 
			routeCase :"light/cases/{caseID}/route-case",
			createVariable: "process-variable",
			imageInfo: "light/case/{caseID}/download64",
			fileDownload: "case/{caseID}/file/{fileID}",			
			variableList: "process-variable",			
			imageDownload: 'light/case/{caseID}/download64',			
			generateImageGeo:"/light/case/{caseID}/input-document/location",
			variableInfo: "process-variable/{var_uid}",
			uploadFile: "",
			refreshToken: "oauth/token",
			fileStreaming : "en/neoclassic/cases/casesStreamingFile?actionAjax=streaming&a={caseID}&d={fileId}",
			executeTrigger: "light/process/{processID}/task/{taskID}/case/{caseID}/step/{stepID}/execute-trigger/{triggerOption}",
            conditionalSteps: "light/process/{processID}/case/{caseID}/{delIndex}/step/{stepPosition}",
            executeQuery: "project/{processID}/process-variable/{var_name}/execute-query",  
		};
		this.options.urlBase = "{server}/api/1.0/{workspace}/{endPointPath}";
		this.options.urlBaseStreaming = "{server}/sys{workspace}/{endPointPath}";

	};

	WebServiceManager.prototype.getFullEndPoint= function (keys, urlBase, endPoint) {
        var k;
		urlBase=urlBase.replace(/{endPointPath}/, endPoint);		
		for (k in keys) {
			if (keys.hasOwnProperty(k)) {
				urlBase = urlBase.replace(new RegExp("{"+ k +"}" ,"g"), keys[k]);				
			}
		}
		return urlBase;
    };

    WebServiceManager.prototype.setKey = function(name, value) {
		if(this.options.keys)
			this.options.keys[name] = value;		
		return this;
	};

    WebServiceManager.prototype.getKey = function(name) {
        var resp = false;
        if(this.options.keys)
            resp = this.options.keys[name];        
        return resp;
    };

	WebServiceManager.prototype.deleteKey = function(name, value) {
		if(this.options.keys)
			delete this.options.keys[name];
		return this;
	};

    WebServiceManager.prototype.getToken = function() {        
        return this.options.token;
    };
    
	WebServiceManager.prototype.startCase = function() {		
    	var that =this,
            resp,
    		url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.startCase),
    		method = "POST";

		$.ajax({
            url: url,
            type: method,
            async : false,            
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
            		"state":"success",
            		"caseID":data.caseId,
            		"caseTitle":data.caseNumber,
            		"caseNumber":data.caseNumber	            		
            	};
            },
            error: function (xhr, textStatus, errorThrown) {
                resp= {	            		
            		"state":"internetFail"
            	};
            } 
        });
        return resp;       			
	};

	WebServiceManager.prototype.loadAllDataCase = function() {
    	var that = this,        
            method, url, resp;
    		
		url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.getAllDataCase);	
		method = "GET";
		
		$.ajax({
            url: url,
            type: method,
            async : false,                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
            		"state":"success",
            		"data": data
            	};
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
            		"state":"internetFail"
            	};
            } 
        });
        return resp;        
	};

	WebServiceManager.prototype.submitFormCase = function(data) {
	    var that = this,        
            url, method, resp;

		url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.submitFormCase);	   	    
		method = "PUT";

	    $.ajax({
            url: url,
            type: method,
            async : false,
            data: JSON.stringify(data),            
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
            		"state":"success"
            	};
            },
            error: function (xhr, textStatus, errorThrown) {
                if(xhr.status == 200){
                    resp = {
                        "state":"success"
                    };
                }else{
                    resp = {
                        "state":"internetFail"
                    };    
                }                
            } 
        });
        return resp;        
	};	

	WebServiceManager.prototype.executeTrigger = function(stepID , triggerOption) {
    	var that = this,        
    	    method = "POST", url, resp;
		
		this.setKey('stepID', stepID);
		this.setKey('triggerOption', triggerOption);
		
		url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.executeTrigger);	
		
		this.deleteKey('stepID');
		this.deleteKey('triggerOption');

		$.ajax({
            url: url,
            type: method,
            async : false,                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
            		"state":"success"
            	};
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
            		"state":"internetFail"
            	};
            } 
        });
        return resp;    
	};

    WebServiceManager.prototype.executeQuery = function(data,varName) {
        var that = this,        
            method = "POST", url, resp=[];
        
        this.setKey('var_name', varName);
        
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.executeQuery); 
        
        this.deleteKey('var_name');
        
        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            async : false,                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = data;
            }
        });
        return resp;    
    };

    WebServiceManager.prototype.conditionalStep = function(stepID , stepPosition) {
        var that = this,        
            method = "GET", url, resp;
        
        this.setKey('stepID', stepID);
        this.setKey('stepPosition', stepPosition);
        
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.conditionalSteps); 
        
        this.deleteKey('stepID');
        this.deleteKey('stepPosition');

        $.ajax({
            url: url,
            type: method,
            async : false,                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
                        "state":"success",
                        "data":data
                };
            },
            error: function (xhr, textStatus, errorThrown) {
             resp = {                    
                    "state":"internetFail"
                }
            } 
        });
        return resp;  
    };

	WebServiceManager.prototype.getJsonForm = function(formID) {
    	var that = this,
            method, url, sendData=[], resp;
    		
		sendData.push(formID);        
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.jsonDynaforms);	

		method = "POST";
		$.ajax({
            url: url,
            type: method,
            async : false,            
            data: JSON.stringify({
	            formId:sendData
	        }),
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                var respData = null;
            	if(data.length != 0){
            		respData = data[0].formContent;
            	}	            	
            	resp = {
            		"data":respData,
            		"state":"success"
            	};  
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
            		"state":"internetFail"
            	};
            } 
        });
        return resp;        
	};

	WebServiceManager.prototype.getFormDefinition = function() {
    	var that = this,        
            method, url, resp;
		url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.dynaformDefinition);	
		method =  "GET",
		$.ajax({
            url: url,
            type: method,
            async : false,                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = {
            		"data":data.data.formContent,
            		"state":"success"
            	}; 
            },
            error: function (xhr, textStatus, errorThrown) {               
                resp = {
            		"state":"internetFail"
            	};
            } 
        });
        return resp;        
	};

    WebServiceManager.prototype.imageInfo = function(id,width) {
        var that = this,
            method, url, resp;
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.imageInfo); 
        method = "POST",
        $.ajax({
            url: url,
            type: method,
            async : false,
            data: JSON.stringify([{
                fileId: id,
                width : width,
                version :1
            }]),                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = data; 
            } 
        });
        if (resp){
            return {
                id: resp[0].fileId,
                base64: resp[0].fileContent
            };
        }else{
            return {
                id: "",
                base64: ""
            };
        }        
    };

    WebServiceManager.prototype.imagesInfo = function(data) {
        var that = this,
            method, url, resp;
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.imageInfo); 
        method = "POST",
        $.ajax({
            url: url,
            type: method,
            async : false,
            data: JSON.stringify(data),                        
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (data, textStatus) {
                resp = data; 
            } 
        });
        return resp;                
    };  

    WebServiceManager.prototype.restClient = function() {
        defaults = {
            url: "/rest/v10",
            method: "GET",
            contentType: "application/json",
            data: '',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + keys.access_token);
                if(that.options.language!=null){
                    xhr.setRequestHeader("Accept-Language", that.options.language);                        
                }
            },
            success: function (){},
            error: function () {}
        };
        _.extend(defaults,parems);

        defaults.type = _methodsMap[defaults.method];
        defaults.data = JSON.stringify(defaults.data);
        $.ajax(defaults);
    };

    WebServiceManager.prototype.getFullURLStreaming = function (id) {
        var k, 
        keys  = this.options.keys,
        urlFormat = this.options.urlBaseStreaming;
        this.setKey('fileId', id);
        urlFormat=urlFormat.replace(/{endPointPath}/, this.options.endPoints.fileStreaming);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {                           
                    urlFormat = urlFormat.replace(new RegExp("{"+ k +"}" ,"g"), keys[k]);               
            }
        }
        this.deleteKey("fileId");
        return urlFormat;
     };             	

	PMDynaform.extendNamespace("PMDynaform.implements.WebServiceManager", WebServiceManager);
}());
(function () {
	/**
	 * @class PMDynaform.core.TokenStream
	 * Class to handle tokens or attributes for build de Formula 
	 * @param {Object} tokens
	 */
	var TokenStream = function (tokens) {
	    /**
         * @property {Number} [cursor=0] The property represents the current index 
         * of the tokens array.
         * @private
         */
	    this.cursor = 0;
	    /**
         * @property {Object} Encapsulate all tokens passed as parameter 
         * @private
         */
	    this.tokens = tokens;
	};
	 /**
     * Gets the next token element of the array
     * @return {String} element selected.
     * @private
     */
	TokenStream.prototype.next = function () {
		return this.tokens[this.cursor++];
	};
	/**
	 * The method helps in the cases when exist brackets inside of Formula
	 * @private
	 * @param {String} direction
	 * @return {String} token Element selected from token array
	 */
	TokenStream.prototype.peek = function (direction) {
		if (direction === undefined) {
            direction = 0;
        }
        return this.tokens[this.cursor + direction];
	};
	PMDynaform.extendNamespace("PMDynaform.core.TokenStream", TokenStream);


	/**
	 * @class PMDynaform.core.Tokenizer
	 * Class to manage all fields and their values, those values may be CONSTANTS, 
	 * MATH functions and FIELDS.
	 * @param {Object} tokens
	 */
	var Tokenizer = function () {
	    /**
         * @property {Number} [tokens={}] The property represents all the tokens stored
         * @private
         */
	    this.tokens = {};
	    /**
         * @property {String} [regex=null] Represents the property that encapsulate the execution
         * of the Regular Expression when the token is been finded or executed
         * @private
         */
	    this.regex = null;
	    /**
         * @property {Array} [fields=[]] Encapsulate all the fields associated or that are inside
         * of the tokens
         * @private
         */
	    this.fields = [];
	    /**
         * @property {Array} [tokenNames=[]] All the names of the tokens are stored in the array
         * @private
         */
	    this.tokenNames = [];
	    /**
         * @property {Object} [tokenFields={}] All the tokens fields are stored in this property
         * @private
         */
	    this.tokenFields = {};
	};
	/**
     * Adds new token to tokens array. If the element already exist this is replaced.
     * @param {String} name The name corresponde to name of the property inside of the tokens
     * @param {String} expression This is the value if the element is a field and is an expression
     * if the element is a bracket or some function.
     * @private
     */
	Tokenizer.prototype.addToken = function (name, expression) {
		this.tokens[name] = expression;
	};
	/**
     * Adds new expression to field
     * @param {String} name Parameter that describes to new element (Must of times is 'field')
     * @param {String} expression Value of the new element (Must of times is the name of the field)
     * @private
     */
	Tokenizer.prototype.addField = function (name, expression) {
		var expr;

		if ($.inArray(expression, this.fields) === -1) {
			this.fields.push(expression);
		}

		expr = this.fields.toString().replace(/,/g,"|");
		expr = expr.replace(new RegExp("\\[","g"),"\\[");
		expr = expr.replace(new RegExp("\\]","g"),"\\]");
		this.tokens[name] = expr;
	};
	/**
     * Sets the value for the field selected
     * @param {String} name Corresponds to name of the field
     * @param {String||Number} value Value for the field
     * @private
     */
	Tokenizer.prototype.addTokenValue = function (name, value) {
		var val;
		val = value.replace(/\s+/g, '')
		this.tokenFields[name] = (!parseFloat(val))? 0 : parseFloat(val);
	};
	/**
     * Executes and find tokens based on the formula expression
     * @param {Object} data
     * @private
     */
	Tokenizer.prototype.tokenize = function (data) {
		var tokens;

		this.buildExpression(data);
        tokens = this.findTokens(data);
        
        return new TokenStream(tokens);
	};
	/**
     * Builds the formula expression separating by tokens 
     * @param {object} data Represent the data of the formula
     * @private
     */
	Tokenizer.prototype.buildExpression = function (data) {
		var tokenRegex = [],
		tokenName;

	    for (tokenName in this.tokens) {
	        this.tokenNames.push(tokenName);
	        tokenRegex.push('('+this.tokens[tokenName]+')');
	    }

	    this.regex = new RegExp(tokenRegex.join('|'), 'g');
	};
	/**
     * Find the tokens based of the data parameter and build an tokens array
     * @param {String} data
     * @private
     */
	Tokenizer.prototype.findTokens = function(data) {
        var tokens = [],
        match,
        group;

        while ((match = this.regex.exec(data)) !== null) {
            if (match === undefined) {
                continue;
            }

            for (group = 1; group < match.length; group+=1) {
                if (!match[group]) continue;
                
                tokens.push({
                    name: this.tokenNames[group - 1],
                    data: match[group],
                    value: null
                });
            }
        }

        return tokens;
    };
    PMDynaform.extendNamespace("PMDynaform.core.Tokenizer", Tokenizer);
	
	/**
	 * @class PMDynaform.core.Formula
	 * Class to handle all the formula property. The class support brackets that encapsulates
	  to numbers, mathematical operations and functions.
	 * @param {Object} tokens
	 */
	var Formula = function (data) {
	    this.data = data.toString();
	    this.tokenizer = new Tokenizer();
	    
	};
	/**
	 * Initializes tokens by default, like division, multiplication, constant and function.
	 * Using the {@link PMDynaform.core.Tokenizer Tokenizer} class for sets the new tokens
	 */
	Formula.prototype.initializeTokens = function () {
		this.tokenizer.addToken('whitespace', '\\s+');
	    this.tokenizer.addToken('l_paren', '\\(');
	    this.tokenizer.addToken('r_paren', '\\)');
	    this.tokenizer.addToken('float', '[0-9]+\\.[0-9]+');
	    this.tokenizer.addToken('int', '[0-9]+');
	    this.tokenizer.addToken('div', '\\/');
	    this.tokenizer.addToken('mul', '\\*');
	    this.tokenizer.addToken('add', '\\+');
	    this.tokenizer.addToken('sub', '\\-');
	    this.tokenizer.addToken('constant', 'pi|PI');
	    this.tokenizer.addToken('function', '[a-zA-Z_][a-zA-Z0-9_]*');

		return this;
	};
	/**
	 * Adds new token using the {@link PMDynaform.core.Tokenizer Tokenizer} class for
	 * set the data
	 * @param {String} name Name of the token
	 * @param {String} value Value for the new token
	 */
	Formula.prototype.addToken = function (name, value) {
		this.tokenizer.addToken(name, value);
		return this;
	};
	/**
	 * Adds new token using the {@link PMDynaform.core.Tokenizer Tokenizer} class for
	 * set the data
	 * @param {String} name Name of the token
	 * @param {String} value Value for the new token
	 */
	Formula.prototype.addField = function (name, value) {
		this.tokenizer.addField(name, value);
		return this;
	};
	/**
	 * Adds value for the field using {@link PMDynaform.core.Tokenizer Tokenizer} class for set
	 * the data
	 * @param {String} name Name of the token
	 * @param {String} value Value for the new token
	 */
	Formula.prototype.addTokenValue = function (name, value) {
		this.tokenizer.addTokenValue(name, value);
		
		return this;
	};
	/**
	 * The current method add the prefix 'Math' to data from token
	 * @param {String} token Represents the token
	 * @return {String} Return the Mathematical valid data
	 */
	Formula.prototype.consumeConstant = function(token) {
		return 'Math.' + token.data.toUpperCase();
	};
	/**
	 * Gets the valid value for the field passed as parameter
	 * @param {String} token Token that represent the data of the field
	 * @return {String} 
	 */
	Formula.prototype.consumeField = function(token) {
		return (this.tokenizer.tokenFields[token.data] === undefined) ? 0: this.tokenizer.tokenFields[token.data];
	};
	/**
	 * Adds new token using the {@link PMDynaform.core.Tokenizer Tokenizer} class
	 * @param {String} name Name of the token
	 * @param {String} value Value for the new token
	 */
	Formula.prototype.consumeFunction = function (ts, token) {
        var a = [token.data],
        t;

        while (t = ts.next()) {
            a.push(t.data);
            if (t.name === 'r_paren') {
                break;
            }
        }

        return 'Math.' + a.join('');
    };
    /**
	 * Adds new token using the {@link PMDynaform.core.Tokenizer Tokenizer} class
	 * @param {String} name Name of the token
	 * @param {String} value Value for the new token
	 */
    Formula.prototype.evaluate = function () {
        var ts,
        valueFixed,
        expr = [],
        e,
        t;

        this.initializeTokens();
        ts = this.tokenizer.tokenize(this.data);
        
        while (t = ts.next()) {
            switch (t.name) {
                case 'int':
                case 'float':
                case 'mul':
                case 'div':
                case 'sub':
                case 'add':

                    expr.push(t.data);
                    break;
                case 'field':
            		expr.push(this.consumeField(t));
                	break;	
                case 'constant':
                    expr.push(this.consumeConstant(t));
                    break;
                case 'l_paren':
                	expr.push("(");
                	break;
                case 'r_paren':
                	expr.push(")");
                	break;
                case 'function':
                    var n = ts.peek();
                    if (n && n.name === 'l_paren') {
                        expr.push(this.consumeFunction(ts, t));
                        continue;
                    }
                default:
                    break;
            }
        }
		var auxExpr = [];
		for (var i=0; i < expr.length ; i+=1){
			if (typeof expr[i] === "number") {
				auxExpr.push("("+expr[i]+")");	
			} else {
				auxExpr.push(expr[i]);
			}
		}
		expr = auxExpr;
        e = expr.join('');
        try {
        	valueFixed = (new Function('return ' + e))();
        } catch(e) {
        	throw new Error("Error in the formula property")
        	valueFixed = 0;
        }
        return valueFixed;
    };


    PMDynaform.extendNamespace("PMDynaform.core.Formula", Formula);
}());


(function(){
    var Proxy = function (options) {
        //this.endpoint = null;
        this.method = null;
        this.data = null;
        this.successCallback = null;
        this.failureCallback = null;
        this.completeCallback = null;
        this.restProxy = null;
        this.keys = null;
        this.url = null;
        //this.server = null;
        this.multipart = null;

        this.dataType = 'json';
        this.authorizationType = "none";
        this.authorizationOAuth = false;
        Proxy.prototype.init.call(this, options);
    };

    Proxy.prototype.type = "proxy";

    Proxy.prototype.init = function (options) {
        var defaults = {
            url: "",
            //endpoint: '',
            method: 'GET',
            //server: '',
            data: {},
            keys: {
                accessToken: "",
                clientId: "x-pm-local-client",
                clientSecret: "",
                expiresIn: "",
                refreshToken: "",
                scope: "",
                tokenType: "bearer"
            },
            multipart: false,
            successCallback: function (resp, data) {},
            failureCallback: function (resp, data) {},
            completeCallback: function (resp, data) {}
        }

        jQuery.extend(true, defaults, options);

        this.setUrl(defaults.url)
            .setMethod(defaults.method)
            //.setServer(defaults.server)
            .setData(defaults.data)
            .setKeys(defaults.keys)
            .setMultipart(defaults.multipart)
            .setSuccessCallback(defaults.successCallback)
            .setFailureCallback(defaults.failureCallback)
            .setCompleteCallback(defaults.completeCallback)
            .setRestProxy();

        this.executeRestProxy();
    }
    Proxy.prototype.setUrl = function (url) {
        this.url = url;
        return this;
    };
    Proxy.prototype.setEndpoint = function (endpoint) {
        var leftBracket;
        if (typeof endpoint === "string") {
            leftBracket = (endpoint[0]==="/")? endpoint.substring(1) : endpoint;
            this.endpoint = (endpoint[endpoint.length-1]==="/")? endpoint.substring(0, endpoint.length-1) : endpoint;
        }
        
        return this;
    };
    Proxy.prototype.setMethod = function (method) {
        this.method = method;
        return this;
    };
    Proxy.prototype.setServer = function (server) {
        var leftBracket;
        if (typeof server === "string") {
            leftBracket = (server[0]==="/")? server.substring(1) : server;
            this.server = (server[server.length-1]==="/")? server.substring(0, server.length-1) : server;
        }
        return this;
    };
    Proxy.prototype.setData = function (data) {
        this.data = data;
        return this;
    };
    Proxy.prototype.setKeys = function (keys) {
        this.keys = keys;
        this.keys.token = {access_token: keys.token};
        return this;
    };
    Proxy.prototype.setMultipart = function (multipart ) {
        this.multipart = multipart ;
        return this;
    };
    Proxy.prototype.setSuccessCallback = function (fn) {
        if (typeof fn === 'function') {
            this.successCallback = fn;
        }
        return this;
    };
    Proxy.prototype.setFailureCallback = function (fn) {
        if (typeof fn === 'function') {
            this.failureCallback = fn;
        }
        return this;
    };
    Proxy.prototype.setCompleteCallback = function (fn) {
        if (typeof fn === 'function') {
            this.completeCallback = fn;
        }
        return this;
    };
    Proxy.prototype.getFullProxyPath = function () {
        return  this.server + "/" + 
                this.keys.apiName + "/" +
                this.keys.apiVersion + "/" + 
                this.keys.workspace + "/" + 
                "project" + "/" +
                this.keys.processId + "/" + 
                this.endpoint;
    };

    Proxy.prototype.setRestProxy = function () {
        var that = this;
        this.restProxy = new PMDynaform.proxy.RestProxy({
            url: this.url,
            method: that.method,
            data: that.data,
            authorizationOAuth: true,
            dataType: this.dataType,
            success: that.successCallback,
            failure: that.failureCallback,
            complete: that.completeCallback
        });
        this.restProxy.setAuthorizationType('oauth2', {
            access_token: this.keys.accessToken
        });
        return this;
    };

    Proxy.prototype.executeRestProxy = function () {
        var method = {
            "POST": "post",
            "UPDATE": "update",
            "GET": "get",
            "DELETE": "remove"
        };

        this.restProxy[method[this.method]]();  
        return this;
    };
    PMDynaform.extendNamespace("PMDynaform.core.Proxy", Proxy);

}());




(function(){
	var TransformJSON = function (settings) {
		this.parentMode = null;
		this.field = null;
		this.json = null;
		this.jsonBuilt = null;
		TransformJSON.prototype.init.call(this, settings);
	};

	TransformJSON.prototype.init = function (settings) {
		var defaults = {
			parentMode: "edit",
			field: {},
			json: {
				text: TransformJSON.prototype.text,
				textarea: TransformJSON.prototype.textArea,
				checkgroup: TransformJSON.prototype.checkgroup,
				checkbox: TransformJSON.prototype.checkbox,
				radio: TransformJSON.prototype.radio,
				dropdown: TransformJSON.prototype.dropdown, 
				button: TransformJSON.prototype.button,
				submit: TransformJSON.prototype.submit,
				datetime: TransformJSON.prototype.datetime,
				suggest: TransformJSON.prototype.suggest,
				link: TransformJSON.prototype.link, 
				file: TransformJSON.prototype.file,
				grid: TransformJSON.prototype.grid
			}		
		};

		jQuery.extend(true, defaults, settings);

		this.jsonBuilt = defaults.field;
		this.setParentMode(defaults.parentMode)
			.setField(defaults.field)
			.setJSONFactory(defaults.json)
			.buildJSON();

		return this;
	};
	TransformJSON.prototype.setParentMode = function (mode) {
		this.parentMode = mode;

		return this;
	};
	TransformJSON.prototype.text = function (field){
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: [
            	field.defaultValue || field.value
            ],
            data : field.data
		};
	};
	TransformJSON.prototype.textArea = function (field) {
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: [
            	field.defaultValue || field.value
            ],
            data : field.data
		};
	};
	TransformJSON.prototype.checkgroup = function (field) {
		var validOpt = [],
		i;

		for (i=0; i<field.options.length; i+=1) {
			if (field.options[i].selected) {
				if (field.options[i].selected === true) {
					validOpt.push(field.options[i].label);
				}
			}
			
		}
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: validOpt,
            data : field.data
		};
	};
	TransformJSON.prototype.checkbox = function (field) {
		var validOpt = [],
		i;
		for (i=0; i<field.options.length; i+=1) {
			if (field.options[i].selected) {
				if (field.options[i].selected === true) {
					validOpt.push(field.options[i].label);
				}
			}
			
		}
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: validOpt,
            data : field.data
		};
	};
	TransformJSON.prototype.radio = function (field) {
		var validOpt = [],
		i;

		for (i=0; i<field.options.length; i+=1) {
			if (field.defaultValue) {
				if (field.options[i].value.toString() === field.defaultValue.toString()) {
					validOpt.push(field.options[i].label);
				}
			}
			
		}
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: validOpt,
            data : field.data
		};
	};
	TransformJSON.prototype.dropdown = function (field) {
		var validOpt = [],
		i;

		for (i=0; i<field.options.length; i+=1) {
			if (field.defaultValue) {
				if (field.options[i].value.toString() === field.defaultValue.toString()) {
					validOpt.push(field.options[i].label);
				}
			}
		}
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: validOpt,
            data : field.data
		};
	};
	TransformJSON.prototype.button = function (field) {
		var fieldExtended = field;

		return fieldExtended;
	};
	TransformJSON.prototype.submit = function (field) {
		var fieldExtended = field;

		return fieldExtended;
	};
	TransformJSON.prototype.datetime = function (field) {
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: [
            	field.defaultValue || field.value
            ],
            data : field.data
		};
	};
	TransformJSON.prototype.suggest = function (field) {
		var validOpt = [],
		i, aux;

		for (i=0; i<field.options.length; i+=1) {
			if (field.defaultValue) {
				if (field.options[i].value.toString() === field.defaultValue.toString()) {
					validOpt.push(field.options[i].label);
				}
			}
		}
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: validOpt,
            data : field.data
		};
	};
	TransformJSON.prototype.link = function (field) {
		return {
			type: "label",
            colSpan: field.colSpan,
            label: field.label,
            options: [
            	field.value
            ]
		};
	};
	TransformJSON.prototype.file = function (field) {
		var fieldExtended = field;

		return fieldExtended;
	};
	TransformJSON.prototype.grid = function (field) {
		var fieldExtended = field;

		return fieldExtended;
	};
	TransformJSON.prototype.setField = function (field) {
		this.field = field;

		return this;
	};
	TransformJSON.prototype.setJSONFactory = function (factory) {
		this.json = factory;
		return this;
	};
	TransformJSON.prototype.discardViewField = function (type) {
        var disabled = [
            "button",
            "submit",
            "image",
            "label",
            "title",
            "subtitle"
        ];

        return ($.inArray(type, disabled) < 0) ? true : false;
    };
	TransformJSON.prototype.reviewField = function (field) {
		var jsonBuilt = field, total,
		sigleControl = ["text","suggest","textarea","datetime"], data, i;

		if (this.json[field.type] && this.discardViewField(field.type) ) {
			switch (field.mode) {
				case "disabled":
					jsonBuilt = field;
					jsonBuilt.disabled = true;
				break;
				case "parent":
					field.mode = this.parentMode;
					jsonBuilt = this.reviewField(field);
					//jsonBuilt = this.json[field.type](field);
				break;
				case "view":
					jsonBuilt = this.json[field.type](field);
				break;
				default :
					jsonBuilt = field;
			}
		}
		//if (jsonBuilt.fullOptions)
		jsonBuilt.dataType = field.dataType || "";
		jsonBuilt.originalType = field.originalType || field.type;
		jsonBuilt.var_name = field.var_name || "";
		jsonBuilt.var_uid = field.var_uid || "";
		jsonBuilt.options || field.var_accepted_values;
		if (field.data){
           	jsonBuilt.fullOptions = [];
           	if (sigleControl.indexOf(jsonBuilt.originalType) !== -1){
           		if (jsonBuilt.originalType === "suggest"){
					jsonBuilt.fullOptions = [ field.data["label"] || field.defaultValue];           			
           		}else{
					jsonBuilt.fullOptions = [ field.data["value"] || field.defaultValue];
           		}
			} else {
				if (jsonBuilt.originalType === "checkgroup" ){
	                data = [];
					if ($.isArray(field["optionsSql"])){
						total = field["options"].concat(field["optionsSql"]);					
					}else{
						total = field["options"];
					}
	                for ( i = 0 ; i < total.length ; i+=1 ) {
	                    if (field.data["value"].indexOf(total[i]["value"]) !==- 1){
	                    	data.push(total[i]["label"]);
	                    }
	                }
					jsonBuilt.fullOptions = data || [field.defaultValue];
				}else{
					jsonBuilt.fullOptions = [ field.data["label"] || field.defaultValue];
				}
			}
		}
		return jsonBuilt;
	};
	TransformJSON.prototype.buildJSON = function () {
		this.jsonBuilt = this.reviewField(this.field);
		
		return this;
	};
	
	TransformJSON.prototype.getJSON = function () {
		
		return this.jsonBuilt;
	};
	PMDynaform.extendNamespace("PMDynaform.core.TransformJSON", TransformJSON);

}());
(function(){
	
    var FileStream,
    FileReader = window.FileReader,
    FileReaderSyncSupport = false,
    workerScript = "self.addEventListener('message', function(e) { var data=e.data; try { var reader = new FileReaderSync; postMessage({ result: reader[data.readAs](data.file), extra: data.extra, file: data.file})} catch(e){ postMessage({ result:'error', extra:data.extra, file:data.file}); } }, false);",
    syncDetectionScript = "self.addEventListener('message', function(e) { postMessage(!!FileReaderSync); }, false);",
    fileReaderEvents = ['loadstart', 
                        'progress', 
                        'load', 
                        'abort', 
                        'error', 
                        'loadend'],

    FileStream = {
        enabled: false,
        setupInput: setupInput,
        setupDrop: setupDrop,
        setupClipboard: setupClipboard,
        sync: false,
        output: [],
        opts: {
            dragClass: "drag",
            accept: false,
            readAsDefault: 'BinaryString',
            readAsMap: {
                'image/*': 'DataURL',
                'text/*' : 'Text'
            },
            on: {
                loadstart: function() {},
                progress: function() {},
                load: function() {},
                abort: function() {},
                error: function() {},
                loadend: function() {},
                skip: function() {},
                groupstart: function() {},
                groupend: function() {},
                beforestart: function() {}
            }
        }
    };

    // Not all browsers support the FileReader interface.  Return with the enabled bit = false.
    if (!FileReader) {
        return;
    }

    // WorkerHelper is a little wrapper for generating web workers from strings
    var WorkerHelper = (function() {

        var URL = window.URL || window.webkitURL;
        var BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;

        // May need to get just the URL in case it is needed for things beyond just creating a worker.
        function getURL (script) {
            if (window.Worker && BlobBuilder && URL) {
                var bb = new BlobBuilder();
                bb.append(script);
                return URL.createObjectURL(bb.getBlob());
            }

            return null;
        }

        // If there is no need to revoke a URL later, or do anything fancy then just return the worker.
        function getWorker (script, onmessage) {
            var worker,
            url = getURL(script);

            if (url) {
                worker = new Worker(url);
                worker.onmessage = onmessage;
                return worker;
            }

            return null;
        }

        return {
            getURL: getURL,
            getWorker: getWorker
        };

    })();

    // setupClipboard: bind to clipboard events (intended for document.body)
    function setupClipboard(element, opts) {
        var instanceOptions = {};
        if (!FileStream.enabled) {
            return;
        }

        $.extend(true, instanceOptions, FileStream.opts);
        $.extend(true, instanceOptions, opts);
        //instanceOptions = extend(extend({}, FileStream.opts), opts);

        element.addEventListener("paste", onpaste, false);

        function onpaste(e) {
            var files = [];
            var clipboardData = e.clipboardData || {};
            var items = clipboardData.items || [];

            for (var i = 0; i < items.length; i++) {
                var file = items[i].getAsFile();

                if (file) {

                    // Create a fake file name for images from clipboard, since this data doesn't get sent
                    var matches = new RegExp("/\(.*\)").exec(file.type);
                    if (!file.name && matches) {
                        var extension = matches[1];
                        file.name = "clipboard" + i + "." + extension;
                    }

                    files.push(file);
                }
            }

            if (files.length) {
                processFileList(e, files, instanceOptions);
                e.preventDefault();
                e.stopPropagation();
            }
        }
    }

    // setupInput: bind the 'change' event to an input[type=file]
    function setupInput(input, opts) {
        var instanceOptions = {};

        if (!FileStream.enabled) {
            return;
        }
        //var instanceOptions = extend(extend({}, FileStream.opts), opts);
        $.extend(true, instanceOptions, FileStream.opts);
        $.extend(true, instanceOptions, opts);

        input.addEventListener("change", inputChange, false);
        input.addEventListener("drop", inputDrop, false);

        function inputChange(e) {
            processFileList(e, input.files, instanceOptions);
        }

        function inputDrop(e) {
            e.stopPropagation();
            e.preventDefault();
            processFileList(e, e.dataTransfer.files, instanceOptions);
        }
    }

    // setupDrop: bind the 'drop' event for a DOM element
    function setupDrop(dropbox, opts) {
        var dragClass, 
        initializedOnBody,
        instanceOptions = {};

        if (!FileStream.enabled) {
            return;
        }
        //var instanceOptions = extend(extend({}, FileStream.opts), opts);
        $.extend(true, instanceOptions, FileStream.opts);
        $.extend(true, instanceOptions, opts);

        if (!instanceOptions.dnd) {
            return;
        }
        dragClass = instanceOptions.dragClass;
        initializedOnBody = false;

        // Bind drag events to the dropbox to add the class while dragging, and accept the drop data transfer.
        dropbox.addEventListener("dragenter", onlyWithFiles(dragenter), false);
        dropbox.addEventListener("dragleave", onlyWithFiles(dragleave), false);
        dropbox.addEventListener("dragover", onlyWithFiles(dragover), false);
        dropbox.addEventListener("drop", onlyWithFiles(drop), false);

        // Bind to body to prevent the dropbox events from firing when it was initialized on the page.
        document.body.addEventListener("dragstart", bodydragstart, true);
        document.body.addEventListener("dragend", bodydragend, true);
        document.body.addEventListener("drop", bodydrop, false);

        function bodydragend(e) {
            initializedOnBody = false;
        }

        function bodydragstart(e) {
            initializedOnBody = true;
        }

        function bodydrop(e) {
            if (e.dataTransfer.files && e.dataTransfer.files.length ){
                e.stopPropagation();
                e.preventDefault();
            }
        }

        function onlyWithFiles(fn) {
            return function() {
                if (!initializedOnBody) {
                    fn.apply(this, arguments);
                }
            };
        }

        function drop(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                removeClass(dropbox, dragClass);
            }
            processFileList(e, e.dataTransfer.files, instanceOptions);
        }

        function dragenter(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                addClass(dropbox, dragClass);
            }
        }

        function dragleave(e) {
            if (dragClass) {
                removeClass(dropbox, dragClass);
            }
        }

        function dragover(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                addClass(dropbox, dragClass);
            }
        }
    }

    // setupCustomFileProperties: modify the file object with extra properties
    function setupCustomFileProperties(files, groupID) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            file.extra = {
                nameNoExtension: file.name.substring(0, file.name.lastIndexOf('.')),
                extension: file.name.substring(file.name.lastIndexOf('.') + 1),
                fileID: i,
                uniqueID: getUniqueID(),
                groupID: groupID,
                prettySize: prettySize(file.size)
            };
        }
    }

    // getReadAsMethod: return method name for 'readAs*' - http://www.w3.org/TR/FileAPI/#reading-a-file
    function getReadAsMethod(type, readAsMap, readAsDefault) {
        for (var r in readAsMap) {
            if (type.match(new RegExp(r))) {
                return 'readAs' + readAsMap[r];
            }
        }
        return 'readAs' + readAsDefault;
    }

    // processFileList: read the files with FileReader, send off custom events.
    function processFileList(e, files, opts) {

        var filesLeft = files.length;
        var group = {
            groupID: getGroupID(),
            files: files,
            started: new Date()
        };

        function groupEnd() {
            group.ended = new Date();
            opts.on.groupend(group);
        }

        function groupFileDone() {
            if (--filesLeft === 0) {
                groupEnd();
            }
        }

        FileStream.output.push(group);
        setupCustomFileProperties(files, group.groupID);

        opts.on.groupstart(group);

        // No files in group - end immediately
        if (!files.length) {
            groupEnd();
            return;
        }

        var sync = FileStream.sync && FileReaderSyncSupport;
        var syncWorker;

        // Only initialize the synchronous worker if the option is enabled - to prevent the overhead
        if (sync) {
            syncWorker = WorkerHelper.getWorker(workerScript, function(e) {
                var file = e.data.file;
                var result = e.data.result;

                // Workers seem to lose the custom property on the file object.
                if (!file.extra) {
                    file.extra = e.data.extra;
                }

                file.extra.ended = new Date();

                // Call error or load event depending on success of the read from the worker.
                opts.on[result === "error" ? "error" : "load"]({ target: { result: result } }, file);
                groupFileDone();

            });
        }

        Array.prototype.forEach.call(files, function(file) {

            file.extra.started = new Date();

            if (opts.accept && !file.type.match(new RegExp(opts.accept))) {
                opts.on.skip(file);
                groupFileDone();
                return;
            }

            if (opts.on.beforestart(file) === false) {
                opts.on.skip(file);
                groupFileDone();
                return;
            }

            var readAs = getReadAsMethod(file.type, opts.readAsMap, opts.readAsDefault);

            if (sync && syncWorker) {
                syncWorker.postMessage({
                    file: file,
                    extra: file.extra,
                    readAs: readAs
                });
            }
            else {

                var reader = new FileReader();
                reader.originalEvent = e;

                fileReaderEvents.forEach(function(eventName) {
                    reader['on' + eventName] = function(e) {
                        if (eventName == 'load' || eventName == 'error') {
                            file.extra.ended = new Date();
                        }
                        opts.on[eventName](e, file);
                        if (eventName == 'loadend') {
                            groupFileDone();
                        }
                    };
                });

                reader[readAs](file);
            }
        });
    }

    // checkFileReaderSyncSupport: Create a temporary worker and see if FileReaderSync exists
    function checkFileReaderSyncSupport() {
        var worker = WorkerHelper.getWorker(syncDetectionScript, function(e) {
            FileReaderSyncSupport = e.data;
        });

        if (worker) {
            worker.postMessage({});
        }
    }


    // hasClass: does an element have the css class?
    function hasClass(el, name) {
        return new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)").test(el.className);
    }

    // addClass: add the css class for the element.
    function addClass(el, name) {
        if (!hasClass(el, name)) {
          el.className = el.className ? [el.className, name].join(' ') : name;
        }
    }

    // removeClass: remove the css class from the element.
    function removeClass(el, name) {
        if (hasClass(el, name)) {
          var c = el.className;
          el.className = c.replace(new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)", "g"), " ").replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        }
    }

    // prettySize: convert bytes to a more readable string.
    function prettySize(bytes) {
        var s = ['bytes', 'kb', 'MB', 'GB', 'TB', 'PB'];
        var e = Math.floor(Math.log(bytes)/Math.log(1024));
        return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
    }

    // getGroupID: generate a unique int ID for groups.
    var getGroupID = (function(id) {
        return function() {
            return id++;
        };
    })(0);
    
    // getUniqueID: generate a unique int ID for files
    var getUniqueID = (function(id) {
        return function() {
            return id++;
        };
    })(0);

    // The interface is supported, bind the FileStream callbacks
    FileStream.enabled = true;


	PMDynaform.extendNamespace("PMDynaform.core.FileStream", FileStream);

}());
(function(){
    /**
     * FullScreen class
     */
    var FullScreen = function(options) {
        this.element = null;
        this.onReadyScreen = null;
        this.onCancelScreen = null;
        this.isInFullScreen = null;
        this.supported = null;
        FullScreen.prototype.init.call(this, options);
    };
    /**
     * [init description]
     * @param  {Object} options Config options
     */
    FullScreen.prototype.init = function(options) {
        var defaults = {
            element: document.documentElement,
            onReadyScreen: function(){},
            onCancelScreen: function(){}
        };
        jQuery.extend(true, defaults, options);
        this.element = defaults.element;
        this.onReadyScreen = defaults.onReadyScreen;
        this.onCancelScreen = defaults.onCancelScreen;
        this.checkFullScreen();
    };
    FullScreen.prototype.checkFullScreen = function () {
        var el = this.element,
        request = el.requestFullScreen || 
                        el.webkitRequestFullScreen || 
                        el.mozRequestFullScreen || 
                        el.msRequestFullScreen;

        this.supported = request ? true: null;

        return this;
    };
    FullScreen.prototype.cancel = function () {
        var requestMethod,fnCancelScreen, wscript, el;
        if (parent.document.documentElement === document.documentElement) {
            el = document;
        } else {
            el = parent.document;
        }
        requestMethod = el.cancelFullScreen || 
                        el.webkitCancelFullScreen || 
                        el.mozCancelFullScreen || 
                        el.exitFullscreen;
        if (requestMethod) {
            requestMethod.call(el);
            try {
                fnCancelScreen = this.onCancelScreen;
                fnCancelScreen(el);
            } catch (e) {
                throw new Error(e);
            }
        } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
            wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
    };

    FullScreen.prototype.applyZoom = function () {
        var requestMethod, wscript, fnReadyScreen, el = this.element;
        requestMethod = el.requestFullScreen || 
                        el.webkitRequestFullScreen || 
                        el.mozRequestFullScreen || 
                        el.msRequestFullScreen;

        if (requestMethod) {
            requestMethod.call(el); 
            try {
                fnReadyScreen = this.onReadyScreen;
                fnReadyScreen(el);
            } catch (e) {
                throw new Error(e);
            }
        } else if (typeof window.ActiveXObject !== "undefined") {
            wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
        return false
    };
    FullScreen.prototype.toggle = function () {
        var el;
        if (parent.document.documentElement === document.documentElement) {
            el = document;
        } else {
            el = parent.document;
        }
        this.isInFullScreen = (el.fullScreenElement && el.fullScreenElement !== null) ||  (el.mozFullScreen || el.webkitIsFullScreen);
        if (this.isInFullScreen) {
            this.cancel();
        } else {
            this.applyZoom();
        }
        return false;
    };

    PMDynaform.extendNamespace("PMDynaform.core.FullScreen", FullScreen);
}());
(function() {
	var Script = function (options) {
		this.name = null;
		this.type = null;
		this.html = null;
		this.script = "";
		this.renderTo = document.body;

		Script.prototype.init.call(this, options);
	};
	Script.prototype.init = function (options) {
		var defaults = {
			type: "text/javascript",
			script: ""
		};

		$.extend(true, defaults, options);
		this.setType(defaults.type)
			.setScript(defaults.script);
	};
	Script.prototype.setType = function (type) {
		this.type = type
		return this;
	};
	Script.prototype.setScript = function (script) {
		this.script = script;
		return this;
	};
	Script.prototype.createHTML = function () {
		var html = document.createElement("script");		

		html.type = this.type;
		html.text = this.script
		this.html = html;

		return html;
	};
	Script.prototype.getHTML = function () {
		if (!this.html) {
			this.createHTML();
		}

		return this.html;
	};
	Script.prototype.render = function () {
		var html = this.getHTML();
		//(new Function(this.script))();
		$(this.renderTo).append(html);

		return this;
	};
    PMDynaform.extendNamespace("PMDynaform.core.Script", Script);
}());

(function () {
    /**
     * @class PMUI.util.ArrayList
     * Construct a List similar to Java's ArrayList that encapsulates methods for
     * making a list that supports operations like get, insert and others.
     *
     *      some examples:
     *      var item,
     *          arrayList = new ArrayList();
     *      arrayList.getSize()                 // 0
     *      arrayList.insert({                  // insert an object
     *          id: 100,
     *          width: 100,
     *          height: 100
     *      });
     *      arrayList.getSize();                // 1
     *      arrayList.asArray();                // [{id : 100, ...}]
     *      item = arrayList.find('id', 100);   // finds the first element with an id that equals 100
     *      arrayList.remove(item);             // remove item from the arrayList
     *      arrayList.getSize();                // 0
     *      arrayList.isEmpty();                // true because the arrayList has no elements
     *
     * @constructor Returns an instance of the class ArrayList
     */
    var ArrayList = function () {
        /**
         * The elements of the arrayList
         * @property {Array}
         * @private
         */
        var elements = [],
            /**
             * The size of the array
             * @property {number} [size=0]
             * @private
             */
            size = 0,
            index,
            i;
        return {

            /**
             * The ID of this ArrayList is generated using the function Math.random
             * @property {number} id
             */
            id: Math.random(),
            /**
             * Gets an element in the specified index or undefined if the index
             * is not present in the array
             * @param {number} index
             * @returns {Object / undefined}
             */
            get : function (index) {
                return elements[index];
            },
            /**
             * Inserts an element at the end of the list
             * @param {Object} item
             * @chainable
             */
            insert : function (item) {
                elements[size] = item;
                size += 1;
                return this;
            },
            /**
             * Inserts an element in a specific position
             * @param {Object} item
             * @chainable
             */
            insertAt: function(item, index) {
                elements.splice(index, 0, item);
                size = elements.length;
                return this;
            },
            /**
             * Removes an item from the list
             * @param {Object} item
             * @return {boolean}
             */
            remove : function (item) {
                index = this.indexOf(item);
                if (index === -1) {
                    return false;
                }
                //swap(elements[index], elements[size-1]);
                size -= 1;
                elements.splice(index, 1);
                return true;
            },
            /**
             * Gets the length of the list
             * @return {number}
             */
            getSize : function () {
                return size;
            },
            /**
             * Returns true if the list is empty
             * @returns {boolean}
             */
            isEmpty : function () {
                return size === 0;
            },
            /**
             * Returns the first occurrence of an element, if the element is not
             * contained in the list then returns -1
             * @param {Object} item
             * @return {number}
             */
            indexOf : function (item) {
                for (i = 0; i < size; i += 1) {
                    if (item === elements[i]) {
                        return i;
                    }
                }
                return -1;
            },
            /**
             * Returns the the first object of the list that has the
             * specified attribute with the specified value
             * if the object is not found it returns undefined
             * @param {string} attribute
             * @param {string} value
             * @return {Object / undefined}
             */
            find : function (target) {
                var sortedValues = elements.sort();
                // summary:
				//    Performs a binary search on an array of sorted 
				//    values for a specified target. 
				// sortedValues: Array<String|Number>
				//    Array of values to search within.
				// target: String|Number
				//    Item to search for, within the sortedValues array.
				// returns:
				//    Number or null. The location of the target within
				//    the sortedValues array, if found. Otherwise returns 
				//    null.

				// define the recursive function.
				function search( low, high ) {
					// If the low index is greater than the high index, 
					//  return null for not-found. 
					if ( low > high ) {
					  return undefined;
					}

					// If the value at the low index is our target, return 
					//  the low index.
					if ( sortedValues[low] === target ){
					  return low;
					}

					// If the value at the high index is our target, return
					//  the high index.
					if ( sortedValues[high] === target ){
					  return high;
					}

					// Find the middle index and median element.
					var middle = Math.floor( ( low + high ) / 2 );
					var middleElement = sortedValues[middle];

					// Recursive calls, depending on whether or not the 
					//  middleElement is less-than or greater-than the 
					//  target.
					// Note: We can use high-1 and low+1 because we've 
					//  already checked for equality at the high and low 
					//  indexes above.
					if ( middleElement < target ) {
					  return search(middle, high-1);
					} else if ( middleElement > target ) {
					  return search(low+1, middle);
					}

					// If middleElement === target, we can return that value.
					return middle;
				}

				// Start our search between the first and last elements of 
				//  the array.
				return search(0, sortedValues.length-1);
            },
            /**
             * Returns true if the list contains the item and false otherwise
             * @param {Object} item
             * @return {boolean}
             */
            contains : function (item) {
                if (this.indexOf(item) !== -1) {
                    return true;
                }
                return false;
            },
            /**
             * Sorts the list using compFunction if possible, if no compFunction
             * is passed as an parameter then a default sorting method will be used. This default method will sort in 
             * ascending order.
             * @param {Function} [compFunction] The criteria function used to find out the position for the elements in
             * the array list. This function will receive two parameters, each one will be an element from the array 
             * list, the function will compare those elements and it must return:
             *
             * - 1, if the first element must be before the second element.
             * - -1, if the second element must be before the first element.
             * - 0, if the current situation doesn't met any of the two situations above. In this case both elements 
             * can be evaluated as they had the same value. For example, in an array list of numbers, when you are 
             * trying to apply a lineal sorting (ascending/descending) in a array list of numbers, if the array sorting
             * function finds two elements with the value 3 they should be evaluated returning 0, since both values are
             * the same.
             *
             * IMPORTANT NOTE: for a correct performance the sent parameter must return at least two of the values 
             * listed above, if it doesn't the function can produce an infinite loop and thus an error.
             * @return {boolean}
             */
            sort : function (compFunction) {
                var compFunction = compFunction || function(a, b) {
                        if(a < b) {
                            return 1;
                        } else if(a > b) {
                            return -1;
                        } else {
                            return 0;
                        }
                    }, swap = function (items, firstIndex, secondIndex){
                        var temp = items[firstIndex];
                        items[firstIndex] = items[secondIndex];
                        items[secondIndex] = temp;
                    }, partition = function(items, left, right) {
                        var pivot = items[Math.floor((right + left) / 2)],
                            i = left,
                            j = right;
                        while (i <= j) {
                            while (compFunction(items[i], pivot) > 0) {
                                i++;
                            }
                            while (compFunction(items[j], pivot) < 0) {
                                j--;
                            }
                            if (i <= j) {
                                swap(items, i, j);
                                i++;
                                j--;
                            }
                        }
                        return i;
                    }, quickSort = function (items, left, right) {
                        var index;
                        if (items.length > 1) {
                            index = partition(items, left, right);
                            if (left < index - 1) {
                                quickSort(items, left, index - 1);
                            }
                            if (index < right) {
                                quickSort(items, index, right);
                            }
                        }
                        return items;
                    };

                return quickSort(elements, 0, size - 1);
            },
            /**
             * Returns the list as an array
             * @return {Array}
             */
            asArray : function () {
                return elements.slice(0);
            },
            /**
             * Swaps the position of two elements
             * @chainable
             */
            swap: function(index1, index2) {
                var aux;
                if(index1 < size && index1 >=0 && index2 < size && index2 >= 0) {
                    aux = elements[index1];
                    elements[index1] = elements[index2];
                    elements[index2] = aux;
                }
                return this;
            },
            /**
             * Returns the first element of the list
             * @return {Object}
             */
            getFirst : function () {
                return elements[0];
            },
            /**
             * Returns the last element of the list
             * @return {Object}
             */
            getLast : function () {
                return elements[size - 1];
            },

            /**
             * Returns the last element of the list and deletes it from the list
             * @return {Object}
             */
            popLast : function () {
                var lastElement;
                size -= 1;
                lastElement = elements[size];
                elements.splice(size, 1);
                return lastElement;
            },
            /**
             * Returns an array with the objects that determine the minimum size
             * the container should have
             * The array values are in this order TOP, RIGHT, BOTTOM AND LEFT
             * @return {Array}
             */
            getDimensionLimit : function () {
                var result = [100000, -1, -1, 100000],
                    objects = [undefined, undefined, undefined, undefined];
                //number of pixels we want the inner shapes to be
                //apart from the border

                for (i = 0; i < size; i += 1) {
                    if (result[0] > elements[i].y) {
                        result[0] = elements[i].y;
                        objects[0] = elements[i];

                    }
                    if (result[1] < elements[i].x + elements[i].width) {
                        result[1] = elements[i].x + elements[i].width;
                        objects[1] = elements[i];
                    }
                    if (result[2] < elements[i].y + elements[i].height) {
                        result[2] = elements[i].y + elements[i].height;
                        objects[2] = elements[i];
                    }
                    if (result[3] > elements[i].x) {
                        result[3] = elements[i].x;
                        objects[3] = elements[i];
                    }
                }
                return result;
            },
            /**
             * Clears the content of the arrayList
             * @chainable
             */
            clear : function () {
                if (size !== 0) {
                    elements = [];
                    size = 0;
                }
                return this;
            },
            /**
             * Sets the elements for the object.
             * @param {Array|null} items Array with the items to set.
             * @chainable
             */
            set: function(items) {
                if(!(items === null || jQuery.isArray(items))) {
                    throw new Error("set(): The parameter must be an array or null.");
                }
                elements = (items && items.slice(0)) || [];
                size = elements.length;
                return this;
            }
        };
    };

    PMDynaform.extendNamespace("PMDynaform.util.ArrayList", ArrayList);

}());

(function (){

    var RestProxy = function (options) {
        this.url = null;
        this.method = null;
        this.rc = null;
        this.data = null;
        RestProxy.prototype.init.call(this, options);
    };
    
    RestProxy.prototype.type = "RestProxy";
    RestProxy.prototype.init = function (options) {
        var defaults = {
            url: null,
            method: 'GET',
            data: {},
            dataType: 'json',
            authorizationType: 'none',
            authorizationOAuth: false,
            success: function(){},
            failure: function(){},
            complete: function(){}
        };
        jQuery.extend(true, defaults, options);
        this.setRestClient()
            .setUrl(defaults.url)
            .setAuthorizationOAuth(defaults.authorizationOAuth)
            .setMethod(defaults.method)
            .setData(defaults.data)
            .setDataType(defaults.dataType)
            .setSuccessAction(defaults.success)
            .setFailureAction(defaults.failure)
            .setCompleteAction(defaults.complete);
    };

    RestProxy.prototype.setRestClient = function () {
        if (this.rc instanceof RestClient === false) {
            this.rc = new RestClient();
        }
        return this;
    };

    RestProxy.prototype.setUrl = function (url) {
        this.url = url;
        return this;
    };

    RestProxy.prototype.setAuthorizationOAuth = function (option) {
        if (typeof option === 'boolean') {
            this.rc.setSendBearerAuthorization(option);
        }
        return this;
    };

    RestProxy.prototype.setMethod = function (method) {
        this.method = method;
        return this;
    };
    RestProxy.prototype.setSuccessAction = function (action) {
        RestProxy.prototype.success = action;
        return this;
    };
    RestProxy.prototype.setFailureAction = function (action) {
        RestProxy.prototype.failure = action;
        return this;
    };
    RestProxy.prototype.setCompleteAction = function (action) {
        RestProxy.prototype.complete = action;
        return this;
    };

    RestProxy.prototype.setData = function (data) {
        this.data = data;
        return this;
    };
    RestProxy.prototype.getData = function() {
        return this.data;
    };
    RestProxy.prototype.setDataType = function (dataType) {
        this.rc.setDataType(dataType);
        return this;
    };
    RestProxy.prototype.setCredentials = function (usr, pass) {
        this.rc.setBasicCredentials(usr, pass);
        return this;
    };
    RestProxy.prototype.setContentType = function () {
        this.rc.setContentType();
        return this;
    };
    RestProxy.prototype.send = function () {
    };

    RestProxy.prototype.receive = function () {
    };
    RestProxy.prototype.setAuthorizationType = function (type, credentials) {
        this.rc.setAuthorizationType(type); 
        switch(type) {
            case 'none':
                break;
            case 'basic':
                    this.rc.setBasicCredentials(credentials.client, credentials.secret);
                break;
            case 'oauth2':
                    this.rc.setAccessToken(credentials);
                break;
        }
        
        return this;
    };

    RestProxy.prototype.post = function (settings) {
        var that = this;
        if (settings !== undefined) {
            that.init(settings);
        }
        if (this.rc) {
            that.rc.postCall({
                url: that.url,
                id: that.uid,
                data: that.data,
                success : function (xhr, response) {
                    that.success.call(that, xhr, response);
                },
                failure: function (xhr, response) {
                    that.failure.call(that, xhr, response);
                },
                complete: function (xhr, response) {
                    that.complete.call(that, xhr, response);
                }
            });
            that.rc.setSendBearerAuthorization(false);

        } else {
            throw new Error("the RestClient was not defined, please verify the property 'rc' for continue.");
        }
    };

    RestProxy.prototype.update = function (settings) {
        var that = this;
        if (settings !== undefined) {
            that.init(settings);
        }
        if (this.rc) {
            this.rc.putCall({
                url: this.url,
                id: this.uid,
                data: this.data,
                success : function (xhr, response) {
                    that.success.call(this, xhr, response);
                },
                failure: function (xhr, response) {
                    that.failure.call(this, xhr, response);
                },
                complete: function (xhr, response) {
                    that.complete.call(that, xhr, response);
                }
            });
        } else {
            throw new Error("the RestClient was not defined, please verify the property 'rc' for continue.");
        }
    };
    
    RestProxy.prototype.get = function (settings) {
        var that = this;
        if (settings !== undefined) {
            that.init(settings);
        }
        if (this.rc) {
            that.rc.getCall({
                url: that.url,
                id: that.uid,
                data: that.data,
                success : function (xhr, response) {
                    that.success.call(that, xhr, response);
                },
                failure: function (xhr, response) {
                    that.failure.call(that, xhr, response);
                },
                complete: function (xhr, response) {
                    that.complete.call(that, xhr, response);
                }
            });
            that.rc.setSendBearerAuthorization(false);

        } else {
            throw new Error("the RestClient was not defined, please verify the property 'rc' for continue.");
        }
    };
   
    RestProxy.prototype.remove = function (settings) {
        var that = this;
        if (settings !== undefined) {
            that.init(settings);
        }
        if (this.rc) {
            this.rc.deleteCall({
                url: this.url,
                id: this.uid,
                data: this.data,
                success : function (xhr, response) {
                    that.success.call(this, xhr, response);
                },
                failure: function (xhr, response) {
                    that.failure.call(this, xhr, response);
                },
                complete: function (xhr, response) {
                    that.complete.call(that, xhr, response);
                }
            });
        } else {
            throw new Error("the RestClient was not defined, please verify the property for continue.");
        }
    };
   
    RestProxy.prototype.success = function (xhr, response){        
    };
   
    RestProxy.prototype.failure = function (xhr, response){
    };
    
    RestProxy.prototype.complete = function (xhr, response){
    };
    
    PMDynaform.extendNamespace('PMDynaform.proxy.RestProxy', RestProxy);

}());
(function(){

	var Validator =  Backbone.View.extend({
        template: _.template($("#tpl-validator").html()),
        events:{
            "mouseover": "onMouseOver"
        },
        initialize: function() {
            this.render();
        },
        onMouseOver: function() {
            
        },
        render: function() {
            this.$el.addClass("pmdynaform-message-error");
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Validator", Validator);

}());
(function(){
    var PanelView = Backbone.View.extend({
        content : null,
        colsIndex: null,
        template: null,
        collection: null,
        items: null, 
        views: [],
        renderTo: document.body,
        project: null,
        initialize: function(options) {
            var i, defaults = {
                factory: {
                    products: {
                        "form": {
                            model: PMDynaform.model.FormPanel,
                            view: PMDynaform.view.FormPanel
                        },
                        "fieldset": {
                            model: PMDynaform.model.Fieldset,
                            view: PMDynaform.view.Fieldset
                        },
                        "panel": {
                            model: PMDynaform.model.FormPanel,
                            view: PMDynaform.view.FormPanel
                        }
                    },
                    defaultProduct: "form"
                }
            };
            if (options.renderTo) {
                this.renderTo = options.renderTo;
            }
            if(options.project) {
                this.project = options.project;
            }
            this.views = [];

            this.makePanels();
            this.render();
            for ( i = 0 ; i < this.views.length ; i+=1 ) {
                this.views[i].runningFormulator();
            }
        },
        getData: function () {
            var i, 
            k, 
            field,
            subform,
            fields, 
            panels,
            formData;

            panels = this.model.get("items");
            formData = this.model.getData();
            for (i = 0; i < panels.length; i+=1) {
                fields = this.views[i].items.asArray();
                for (k = 0; k < fields.length; k+=1) {

                    if ( (typeof fields[k].model.getData === "function") && (fields[k].model.attributes.type === "form") ){
                        subform = fields[k].getData();
                        $.extend(true, formData.variables, subform.variables);
                    } else if (typeof fields[k].model.getData === "function"){
                        field = fields[k].model.getData();
                        formData.variables[field.name] = field.value;
                    }

                    /*if ((typeof fields[k].getData === "function") && 
                        (fields[k] instanceof PMDynaform.view.Field)) {
                        field = fields[k].getData();
                        formData.variables[field.name] = field.value;
                    } else if ((typeof fields[k].getData === "function") && 
                        (fields[k] instanceof PMDynaform.view.SubForm)) {
                        subform = fields[k].getData();
                        $.extend(true, formData.variables, subform.variables);
                    }*/
                }
            }

            return formData;
        },
        getData2: function () {
            var i, 
            k, 
            field,
            subform,
            fields, 
            panels,
            formData,
            grid,
            data = {};

            panels = this.model.get("items");

            for (i = 0; i < panels.length; i+=1) {
                fields = this.views[i].items.asArray();
                for (k = 0; k < fields.length; k+=1) {
                    if ( (typeof fields[k].model.getData === "function") && (fields[k].model.attributes.type === "form") ){
                    } else if (typeof fields[k].model.getData === "function"){
                        if (fields[k].model.get("type") === "grid") {
                            grid = fields[k].model;
                            data[grid.get("name")] = fields[k].getData2();
                        } else {
                            field = fields[k].model.getData();
                            data[field.name] = field.value;
                            if (typeof fields[k].model.getKeyLabel === "function"){
                                field = fields[k].model.getKeyLabel();
                                data[field.name] = field.value;
                            }
                        }
                    }
                }
            }   
            return data;
        },
        setData2 : function (data) {
            this.getPanels()[0].setData2(data);
            return this;
        },
        makePanels: function() {  
            var i = 0,
            items,
            panelmodel,
            view;

            this.views = [];
            items = this.model.get("items");

            for(i=0; i<items.length; i+=1){
                if ($.inArray(items[i].type, ["panel","form"]) >= 0) {

                    panelmodel = new PMDynaform.model.FormPanel(items[i]);
                    
                    view = new PMDynaform.view.FormPanel({
                        model: panelmodel,
                        project: this.project
                    });
                    this.views.push(view);
                }
            }

            return this;
        },
        getPanels: function () {
            var items = (this.views.length > 0) ? this.views : [];
            
            return items;
        },
        render: function () {
            var i,
            j;

            this.$el = $(this.el);
            for(i=0; i<this.views.length; i+=1){
                this.$el.append(this.views[i].render().el);
            }
            this.$el.addClass("pmdynaform-container");
            if (PMDynaform.core.ProjectMobile){
                this.$el.css({
                    height : "auto"
                });
            }
            $(this.renderTo).append(this.el);

            return this;
        },
        afterRender: function () {
            var i;

            for(i=0; i<this.views.length; i+=1) {
                this.views[i].afterRender();
            }

            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Panel", PanelView);
    
}());

(function(){
    var FormPanel = Backbone.View.extend({
        tagName: "form",
        content : null,    
        template: null,
        items: new PMDynaform.util.ArrayList(),
        views:[],
        templateRow: _.template($('#tpl-row').html()),
        colSpanLabel: 3,
        colSpanControl: 9,
        project: null,
        preTargetControl : null,
        sqlFields : [],
        validDependentFields : [],
        events: {
            'submit': 'onSubmit'
        },
        onChange: function (){},
        /*
        requireVariableByField: [
            "text",
            "textarea",
            "checkbox",
            "radio",    
            "dropdown",
            "datetime",
            "suggest",
            "hidden",
            "label"
        ],*/
        requireVariableByField: [],
        checkBinding: function () {
            this.onChangeCallback(this.model.get("name"), this.previusValue, this.model.get("value"));
            //If the key is not pressed, executes the render method
            if (!this.keyPressed) {
                this.render();
            }
        },
        setOnChange : function (handler) {
            if (typeof handler === "function") {
                this.onChangeCallback = handler;
            }
            return this;
        },
        onChangeHandler : function() {
            var that = this;
            return function(field, newValue, previousValue) {
                if ( typeof that.onChange === 'function' ) {
                    that.onChange(field, newValue, previousValue);
                }
            };
        },        
        initialize: function(options) {
            var defaults = {
                factory : {
                    products: {
                        "text": {
                            model: PMDynaform.model.Text,
                            view: PMDynaform.view.Text
                        },
                        "textarea": {
                            model: PMDynaform.model.TextArea,
                            view: PMDynaform.view.TextArea
                        },
                        "checkgroup": {
                            model: PMDynaform.model.CheckGroup,
                            view: PMDynaform.view.CheckGroup
                        },
                        "checkbox": {
                            model: PMDynaform.model.CheckBox,
                            view: PMDynaform.view.CheckBox
                        },
                        "radio": {
                            model: PMDynaform.model.Radio,
                            view: PMDynaform.view.Radio
                        },
                        "dropdown": {
                            model: PMDynaform.model.Dropdown,
                            view: PMDynaform.view.Dropdown
                        },
                        "button": {
                            model: PMDynaform.model.Button,
                            view: PMDynaform.view.Button
                        },
                        "submit": {
                            model: PMDynaform.model.Submit,
                            view: PMDynaform.view.Submit
                        },
                        "datetime": {
                            model: PMDynaform.model.Datetime,
                            view: PMDynaform.view.Datetime
                        },
                        "fieldset": {
                            model: PMDynaform.model.Fieldset,
                            view: PMDynaform.view.Fieldset
                        },                    
                        "suggest": {
                            model: PMDynaform.model.Suggest,
                            view: PMDynaform.view.Suggest
                        },                                        
                        "link": {
                            model: PMDynaform.model.Link,
                            view: PMDynaform.view.Link
                        },                                        
                        "hidden": {
                            model: PMDynaform.model.Hidden,
                            view: PMDynaform.view.Hidden
                        },
                        "title": {
                            model: PMDynaform.model.Title,
                            view: PMDynaform.view.Title
                        },
                        "subtitle": {
                            model: PMDynaform.model.Title,
                            view: PMDynaform.view.Title
                        },
                        "label": {
                            model: PMDynaform.model.Label,
                            view: PMDynaform.view.Label
                        },
                        "empty": {
                            model: PMDynaform.model.Empty,
                            view: PMDynaform.view.Empty
                        },
                        "file": {
                            model: PMDynaform.model.File,
                            view: PMDynaform.view.File
                        },
                        "image": {
                            model: PMDynaform.model.Image,
                            view: PMDynaform.view.Image
                        },
                        "geomap": {
                            model: PMDynaform.model.GeoMap,
                            view: PMDynaform.view.GeoMap
                        },
                        "grid": {
                            model: PMDynaform.model.GridPanel,
                            view: PMDynaform.view.GridPanel
                        },
                        "form": {
                            model: PMDynaform.model.SubForm,
                            view: PMDynaform.view.SubForm
                        },
                        "annotation": {
                            model: PMDynaform.model.Annotation,
                            view: PMDynaform.view.Annotation  
                        },
                        "location" : {
                            model: PMDynaform.model.GeoMobile,
                            view: PMDynaform.view.GeoMobile  
                        },
                        "scannercode" : {
                            model: PMDynaform.model.Qrcode_mobile,
                            view: PMDynaform.view.Qrcode_mobile  
                        },
                        "signature": {
                            model: PMDynaform.model.Signature_mobile,
                            view: PMDynaform.view.Signature_mobile  
                        },
                        "imagemobile" : {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile  
                        },
                        "audiomobile" : {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile  
                        },
                        "videomobile" : {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile
                        },
                        "panel" : {
                            model: PMDynaform.model.PanelField,
                            view: PMDynaform.view.PanelField
                        }
                    },
                    defaultProduct: "empty"
                }       
            };
            this.validDependentFields = ["dropdown","suggest","text"];
            this.items = new PMDynaform.util.ArrayList();
            if(options.project) {
                this.project = options.project;
            }
            this.setFactory(defaults.factory);
            this.makeItems();
            //this.setFieldRelated();
        },
        setAction: function() {
            this.$el.attr("action", this.model.get("action"));

            return this;
        },
        setMethod: function() {
            this.$el.attr("method", this.model.get("method"));

            return this;
        },
        setFactory: function (factory) {
            this.factory = factory;
            return this;
        },
        getData: function() {
            return this.model.getData();
        },
        setData: function (data) {
            var i,
            j,
            cloneData = data,
            items = this.items.asArray();
            if (typeof data === "object") {
                for (i=0; i<items.length; i+=1) {
                    for (j in cloneData) {
                        if (items[i].model.attributes.variable) {
                            if (items[i].model.attributes.variable.var_name === j) {
                                items[i].model.set("value", cloneData[j]);
                            }
                        }
                    }
                    if (items[i] instanceof PMDynaform.view.SubForm) {
                        items[i].setData(data);
                    }

                    if (items[i] instanceof PMDynaform.view.GridPanel) {
                        items[i].setData(data);
                        //Nothing
                    }
                }
            } else {
                //console.log("Error, The 'data' parameter is not valid. Must be an array.");
            }
            
            return this;
        },
        countElementsInJSON : function (obj) {
            var i = 0, item;
            for ( item  in obj) {
                i+=1;
            }
            return i;
        },
        setData2 : function(data){
            var i, cloneData, items, j, k, type, options, valueViewMode, mode,value, 
            singleControl, valor, richi,option, label_items, size, item;
            singleControl = ["text","textarea","radio","link", "dropdown", "hidden"]
            if(this.project instanceof PMDynaform.core.ProjectMobile){
                items = this.project.viewfields;                                       
            }else{
                items = this.items.asArray(); 
            }
            label_items = {};
            if (data) {
                for ( item in data) {
                    if (item.indexOf("_label") > -1){
                        label_items[item] = data[item];
                    }
                }
            }
            for ( i = 0 ; i < items.length ; i+=1 ) {
                if (data[items[i].model.get("name")] !== undefined) {
                    mode = items[i].model.get("mode");
                    type = items[i].model.get("type");
                    if (mode === "edit" || mode === "disabled" || type === "hidden") {
                        if (singleControl.indexOf(type) !== -1 ) {
                            //format data: geotag to text and textarea
                            if (type === "text" || type === "textarea") {
                                if (!(typeof data[items[i].model.get("name")] === "string")) {
                                    var geo = data[items[i].model.get("name")];
                                    if (geo) {
                                        data[items[i].model.get("name")] = geo.latitude + " " + geo.longitude + " " + geo.altitude;
                                    }
                                }
                                if (!(typeof data[items[i].model.get("name") + "_label"] === "string")) {
                                    var geo = data[items[i].model.get("name") + "_label"];
                                    if (geo) {
                                        data[items[i].model.get("name") + "_label"] = geo.latitude + " " + geo.longitude + " " + geo.altitude;
                                    }
                                }
                            }
                            items[i].model.set("value", data[items[i].model.get("name")]);
                            if (label_items.hasOwnProperty(items[i].model.get("name").concat("_label"))){
                                items[i].model.attributes.data = {
                                        label : label_items[items[i].model.get("name").concat("_label")],
                                        value : data[items[i].model.get("name")]
                                    };
                                items[i].model.set("keyLabel",label_items[items[i].model.get("name").concat("_label")]);
                            }else{
                                items[i].model.set("keyLabel", data[items[i].model.get("name")]);
                            }
                            if ( items[i].clicked) {
                                items[i].render();
                            }
                        }
                            items[i].model.set("data",{
                                value : data[items[i].model.get("name")],
                                label : label_items[items[i].model.get("name").concat("_label")]
                            });
                            items[i].model.set("value",data[items[i].model.get("name")]);
                            if (label_items[items[i].model.get("name").concat("_label")]){
                                items[i].model.set("keyLabel",label_items[items[i].model.get("name").concat("_label")]);
                            }else{
                                if (type == "datetime") {
                                   items[i].model.set("keyLabel",data[items[i].model.get("name")]);
                                }else{
                                    var l;
                                    if(items[i].model.get("options") ){
                                        for (l = 0 ; l < items[i].model.get("options").length ; l+=1){
                                            if (items[i].model.get("options")[l]["value"] == data[items[i].model.get("name")]){
                                                items[i].model.set("keyLabel",items[i].model.get("options")[l]["label"]);
                                            }
                                        }
                                    }
                                }
                            }
                            items[i].render();
                        }
                        if(type=="imageMobile" || type=="audioMobile" || type=="videoMobile")
                            items[i].setFilesRFC(data[items[i].model.get('name')]);                              
                        
                        if(type=="signature"){
                            items[i].setSignature(data[items[i].model.get('name')]);
                        }
                        if(type=="location"){
                            items[i].setLocation(data[items[i].model.get('name')]);                        
                        }
                        if(type=="grid"){
                            items[i].setData2(data[items[i].model.get('name')]);                   
                        }
                        if (type === "checkbox") {
                            options = items[i].model.get("options");
                            if ( items[i].model.get("dataType") === "boolean" ) {
                                if ( data[items[i].model.get("name")] === options[0].value ){
                                    options[1].selected = false;
                                    options[0].selected = true;
                                } else {
                                    delete options[0].selected;
                                    options[1].selected = true;
                                    options[0].selected = false;
                                }
                            } else {
                                for ( k = 0 ; k < options.length; k+=1 ) {
                                    delete options[k].selected;
                                    if (data[items[i].model.get("name")].indexOf(options[k].value) !== -1){
                                        options[k].selected = true;
                                    }
                                }                                
                            }                            
                            items[i].model.set("data",{
                                value : data[items[i].model.get("name")],
                                label : label_items[items[i].model.get("name").concat("_label")]
                            });
                            items[i].model.set("value",items[i].model.get("data")["value"]);
                            items[i].model.set("keyLabel",items[i].model.get("data")["label"]);
                            items[i].render();                            
                        }
                        if (type === "checkgroup") {
                            items[i].model.setItemsChecked(JSON.parse(data[items[i].model.get("name")]));                            
                        }
                    }
                    if (mode === "view") {
                        if(items[i].model.get("originalType") !== "checkgroup"){
                            var value = [], jsondata = {};
                            if (label_items[items[i].model.get("name").concat("_label")] === undefined &&
                                items[i].model.get("type") !== "grid"){
                                if (items[i].model.attributes.options || items[i].model.attributes.optionsSql){
                                    for (var k=0 ; k < items[i].model.attributes.options.length ; k+=1){
                                        if (items[i].model.attributes.options[k].value === data[items[i].model.get("name")]){
                                            data[items[i].model.attributes.id + "_label"] = items[i].model.attributes.options[k].label;
                                            break;                            
                                        }
                                    }
                                    for (var k=0 ; k < items[i].model.attributes.optionsSql.length ; k+=1){
                                        if (items[i].model.attributes.optionsSql[k].value === data[items[i].model.get("name")]){
                                            data[items[i].model.attributes.id + "_label"] = items[i].model.attributes.optionsSql[k].label;
                                            break;                            
                                        }
                                    }
                                }
                                if (label_items[items[i].model.get("name").concat("_label")] === undefined){
                                    data[items[i].model.attributes.id + "_label"] = data[items[i].model.get("name")];
                                }
                                label_items[items[i].model.attributes.id + "_label"] = data[items[i].model.get("name")];
                            }
                            if (label_items[items[i].model.get("name").concat("_label")] && 
                            data[items[i].model.get("name")] ){
                                jsondata = {
                                    label : label_items[items[i].model.get("name").concat("_label")],
                                    value : data[items[i].model.get("name")]                                    
                                }
                                value.push(label_items[items[i].model.get("name").concat("_label")]);
                                if (items[i].model.get("originalType") === "checkbox"){
                                    if (items[i].model.get("dataType") === "boolean"){
                                        jsondata = {
                                            label : JSON.stringify([label_items[items[i].model.get("name").concat("_label")]]),
                                            value : data[items[i].model.get("name")]                                    
                                        }
                                    }else{
                                        value = JSON.parse(label_items[items[i].model.get("name").concat("_label")]);
                                        value.concat(JSON.parse(label_items[items[i].model.get("name").concat("_label")]));
                                        jsondata = {
                                            label : label_items[items[i].model.get("name").concat("_label")],
                                            value : data[items[i].model.get("name")]
                                        }
                                    }
                                }
                                items[i].model.attributes.data = jsondata;
                                items[i].model.attributes.value = data[items[i].model.get("name")];
                                items[i].model.attributes.keyLabel = label_items[items[i].model.get("name").concat("_label")];
                                items[i].model.attributes.keyValue = data[items[i].model.get("name")];
                                items[i].model.set("fullOptions", value);
                            }
                            if (items[i].model.get("type") === "grid"){
                                items[i].setData2(data[items[i].model.get("name")]);
                            }
                        }else{
                            items[i].model.attributes.keyLabel =data[items[i].model.get("name")+"_label"];
                            items[i].model.attributes.keyValue =data[items[i].model.get("name")];
                            items[i].model.set("fullOptions", JSON.parse(data[items[i].model.get("name")+"_label"]));
                        }    
                    }
                }
            }
        ,
        validateVariableField: function (field) {
            var isOk = false;

            if ($.inArray(field.type, this.requireVariableByField) >= 0) {
                if (field.var_uid) {
                    isOk = true;
                }
            } else {
                isOk = "NOT";
            }

            return isOk;
        },
        makeItems: function() {
            var i,
            j,
            factory = this.factory, 
			that = this,
            product, 
            variableEnabled,
            productBuilt, 
            rowView,
            productModel,
            jsonFixed,
            fieldModel,
            fields,
            items;
            this.sqlFields = [];
            fields =  this.model.get("items");
            this.viewsBuilt = [];
            this.items.clear();

            for(i=0; i<fields.length; i+=1) {
                rowView = [];
                for(j=0; j<fields[i].length; j+=1) {
                    variableEnabled = this.validateVariableField(fields[i][j]);
                    if (fields[i][j] !== null && (variableEnabled === true || variableEnabled === "NOT") ) {
                        if (fields[i][j].type) {
                            if(!PMDynaform.core.ProjectMobile && fields[i][j].type === "location"){
                                fields[i][j].type = "geomap";
                            }
                            if(fields[i][j].type === "label"){
                                fields[i][j].type = "annotation";
                            }
                            if (fields[i][j].type === "checkbox" && fields[i][j].dataType !== "boolean" && fields[i][j].dataType !== "") {
                                fields[i][j].type = "checkgroup";
                            }
                            if(fields[i][j].type === "file" && fields[i][j].hasOwnProperty("inputDocuments")){
                                if ($.isArray(fields[i][j]["inputDocuments"])){
                                    $(fields[i][j]["inputDocuments"]).each(function(){
                                        that.model.attributes.inputDocuments[fields[i][j]["variable"]] = this;
                                    });  
                                }
                            }
                            jsonFixed  = new PMDynaform.core.TransformJSON({
                                parentMode: this.model.get("mode"),
                                field: fields[i][j]
                            });
                            product =   factory.products[jsonFixed.getJSON().type.toLowerCase()] ? 
                                factory.products[jsonFixed.getJSON().type.toLowerCase()] : factory.products[factory.defaultProduct];
                        } else {
                            jsonFixed  = new PMDynaform.core.TransformJSON({
                                parentMode: this.model.get("mode"),
                                field: fields[i][j]
                            });
                            product = factory.products[factory.defaultProduct];
                        }
                        
                        //The number 12 is related to 12 columns from Bootstrap framework
                        fieldModel = {
                            colSpanLabel: this.createColspan(fields[i][j].colSpan, "label"),
                            colSpanControl: this.createColspan(fields[i][j].colSpan, "control"),
                            project: this.project,
                            parentMode: this.model.get("mode"),
                            namespace: this.model.get("namespace"),
                            variable: fields[i][j].variable ? fields[i][j].variable : null,
                            fieldsRelated: [],
                            name : fields[i][j].name,
                            id : fields[i][j].id || PMDynaform.core.Utils.generateID(),
                            options : fields[i][j].options,
                            form : this,
							optionsSql : fields[i][j].optionsSql,
							required : fields[i][j].required || false,
							hint : fields[i][j].hint || ""
                        };
                        if (fields[i][j].type === "form" || fields[i][j].type === "grid") {
                            fieldModel.variables = this.model.get("variables") || [];
                            fieldModel.data = this.model.get("data") || [];
                        }

                        $.extend(true, fieldModel, jsonFixed.getJSON());
                        
                        if ( fieldModel.type === "form" && fieldModel.mode === "parent") {
                            fieldModel.mode = this.model.get("mode");
                        }

                        //format data: geotag to text and textarea
                        if (fieldModel.data && (fieldModel.type === "text" || fieldModel.type === "textarea" )) {
                            var geo = fieldModel.data.value;
                            if (geo && geo.latitude && geo.longitude && geo.altitude) {
                                 fieldModel.data.value = geo.latitude + " " + geo.longitude + " " + geo.altitude;
                                 fieldModel.data.label = geo.latitude + " " + geo.longitude + " " + geo.altitude;
                            }
                        }
                        productModel = new product.model(fieldModel);

                        if (fieldModel.sql !== undefined && this.validDependentFields.indexOf( fieldModel.type ) >-1) {
                            productModel.set("parentDependents", []);
                            productModel.set("dependents", []);
                            this.sqlFields.push(productModel);
                        }

                        productBuilt = new product.view({
                            model: productModel,
                            project:this.project,
                            parent: this
                        });
                        productBuilt.parent = this;
                        productBuilt.project = this.project;
                        //add view in mobile project
                        if (this.project.addViewFields){
                            this.project.addViewFields(productBuilt);
                        }
                        rowView.push(productBuilt);
                        this.items.insert(productBuilt);
                        productBuilt.model.attributes.view = productBuilt;
                    } else {
                        console.error ("The field must have the variable property and must to be an object: ", fields[i][j]);
                    }
                }
                if (rowView.length) {
                    this.viewsBuilt.push(rowView);
                }
            }
            this.createDependencies();
            this.runningFormulator();
            return this;
        },
        createDependencies : function () {
            var i, j, nameField, viewItems, sql, fields, itemField, index, indexWhere, where, varName;
            fields  = this.sqlFields;
            for ( i = 0 ; i < fields.length ; i+=1) {
                fields[i].set("dependents", []);
                if ( fields[i].get("variable") && fields[i].get("variable").trim().length ) {
                    nameField = fields[i].get("variable");
                } else{
                    nameField = fields[i].get("id");
                }
                for ( j = 0  ; j < fields.length ; j+=1 ) {
                    if(i!==j){
                        indexWhere = fields[j].get("sql").toLowerCase().indexOf("where");
                        if (indexWhere !== -1) {
                            sql = fields[j].get("sql");
                            sql = sql.replace(/\n/g, " ");
                            where = sql.substring(indexWhere, sql.length);
                            where = where.split(" ");
                            if (this._existVariableInSql(where, nameField)){
                                fields[j].attributes.parentDependents.push(fields[i]);
                                fields[i].attributes.dependents.push(fields[j]);
                            }
                        }
                    }
                }
            }
        },
        _existVariableInSql : function (where, nameField) {
            if (where.indexOf("@#"+nameField)>-1 ||
            where.indexOf("@%"+nameField)>-1 ||
            where.indexOf("@@"+nameField)>-1 ||
            where.indexOf("@?"+nameField)>-1 ||
            where.indexOf("@$"+nameField)>-1 ||
            where.indexOf("@="+nameField)>-1 ||
            where.indexOf('\"'+"@#"+nameField+'\"')>-1 ||
            where.indexOf('\"'+"@%"+nameField+'\"')>-1 ||
            where.indexOf('\"'+"@@"+nameField+'\"')>-1 ||
            where.indexOf('\"'+"@?"+nameField+'\"')>-1 ||
            where.indexOf('\"'+"@$"+nameField+'\"')>-1 ||
            where.indexOf('\"'+"@="+nameField+'\"')>-1 ||
            where.indexOf('\''+"@#"+nameField+'\'')>-1 ||
            where.indexOf('\''+"@%"+nameField+'\'')>-1 ||
            where.indexOf('\''+"@@"+nameField+'\'')>-1 ||
            where.indexOf('\''+"@?"+nameField+'\'')>-1 ||
            where.indexOf('\''+"@$"+nameField+'\'')>-1 ||
            where.indexOf('\''+"@="+nameField+'\'')>-1){
                return true;
            }else{
                return false;
            }
        },
        createColspan : function  (colSpan, target) {
            var colspan;
            switch (parseInt(colSpan)) {
                case 12:
                    if (target === "label"){
                        colspan = 2;
                    } else { 
                        colspan = 10;
                    }
                break;
                case 11:
                    if (target === "label"){
                        colspan = 2;
                    } else {
                        colspan = 10;
                    }
                break;
                case 10:
                    if (target === "label"){
                        colspan = 2;
                    } else { 
                        colspan = 10;
                    }
                break;
                case 9:
                    if (target === "label"){
                        colspan = 2;
                    } else { 
                        colspan = 10;
                    }
                break;
                case 8:
                    if (target === "label"){
                        colspan = 2;
                    } else { 
                        colspan = 10;
                    }
                break;
                case 7:
                    if (target === "label"){
                        colspan = 2;
                    } else { 
                        colspan = 10;
                    }
                break;
                case 6:
                    if (target === "label"){
                        colspan = 4;
                    } else { 
                        colspan = 8;
                    }
                break;
                case 5:
                    if (target === "label"){
                        colspan = 5;
                    } else { 
                        colspan = 7;
                    }                
                break;
                case 4:
                    if (target === "label"){
                        colspan = 4;
                    } else { 
                        colspan = 8;
                    }                
                break;
                case 3:
                    if (target === "label"){
                        colspan = 5;
                    } else { 
                        colspan = 7;
                    }
                break;
                case 2:
                    if (target === "label"){
                        colspan = 5;
                    } else { 
                        colspan = 7;
                    }
                break;
                case 1:
                    if (target === "label"){
                        colspan = 4;
                    } else { 
                        colspan = 8;
                    }
                break;
            }
            return colspan;
        },
        runningFormulator: function () {
            var items, field, item, i,j,k, fieldsAsocied;
            items = this.viewsBuilt;
            for ( i = 0 ; i < items.length ; i+=1 ) {
                for ( j = 0 ; j < items[i].length ; j+=1 ) {
                    field = items[i][j];
                    if ( field.model.get("type") === "form" ) {
                        if (field.runningFormulator){
                            field.runningFormulator();
                        }
                    }
                    if ( field.model.get("type") === "grid" ) {
                        var rowsAll = field.gridtable;
                        for (var row = 0, rowCurrent ; row < rowsAll.length ; row+=1 ) {
                            rowCurrent = rowsAll[row];
                            for (var col = 0; col < rowCurrent.length ; col+=1 ) {
                                if (rowCurrent[col].model.get("formula") && rowCurrent[col].model.get("formula").trim().length){
                                    fieldsAsocied = rowCurrent.filter(function(element){
                                        if (rowCurrent[col].fieldValid.indexOf(element.model.get("id")) > -1) {
                                            element.onFieldAssociatedHandler();
                                        }                                        
                                    });
                                }                            
                            }                            
                        }
                    } else {
                        if (field.model.get("formula") && field.model.get("formula").trim().length){
                            fieldsAsocied = items.filter(function(element){
                                var k;
                                for (k = 0 ; k < element.length ;k+=1){
                                    if ( field.fieldValid.indexOf(element[k].model.get("id")) > -1) {
                                        element[k].onFieldAssociatedHandler();
                                    }
                                }
                            });
                        }
                    }
                }
            }
            return this;
        },
        setFieldRelated: function () {
            var i, 
            j,
            k,
            l,
            fieldA,
            fieldB,
            related,
            relatedA,
            relatedB,
            fieldsSubForm,
            relatingField,
            fields = this.items.asArray();

            for (i=0; i<fields.length; i+=1) {
                fieldA = fields[i].model.get("variable");
                if (fieldA) {
                    for (j=0; j<fields.length; j+=1) {
                        if (i !== j) {
                            fieldB = fields[j].model.get("variable");
                            if (fieldB) {
                                if (fieldA.var_uid === fieldB.var_uid) {
                                    related = fields[i].model.get("fieldsRelated");
                                    related.push(fields[j]);
                                    fields[i].model.set("fieldsRelated", related);
                                }
                            }
                        }
                    }
                }

                if (fields[i].model.get("type") === "form") {
                    fieldsSubForm = fields[i].getItems();
                    for (k=0; k<fields.length; k+=1) {
                        fieldA = fields[k].model.get("variable");
                        if (fieldA) {
                            for (l=0; l<fieldsSubForm.length; l+=1) {
                                fieldB = fieldsSubForm[l].model.get("variable");
                                if (fieldB) {
                                    if (fieldA.var_uid === fieldB.var_uid) {
                                        relatedA = fields[k].model.get("fieldsRelated");
                                        relatedA.push(fieldsSubForm[l]);
                                        fields[k].model.set("fieldsRelated", relatedA);

                                        relatedB = fieldsSubForm[l].model.get("fieldsRelated");
                                        relatedB.push(fields[k]);
                                        fieldsSubForm[l].model.set("fieldsRelated", relatedB);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return this;
        },
        getVariable: function (var_uid) {
            var i,
            varSelected,
            variables = this.model.attributes.variables;

            loop_variables:
            for (i=0; i<variables.length; i+=1) {
                if (variables[i] && variables[i].var_uid === var_uid) {
                    varSelected = variables[i];
                    break loop_variables;
                }
            }
            return varSelected;
        },
        getFields: function () {
            return (this.items.getSize() > 0)? this.items.asArray(): [];
        },
        beforeRender: function (){
            return this;
        },
        disableContextMenu: function() {
            this.$el.on("contextmenu", function(event) {
                event.preventDefault();
                event.stopPropagation();
            });
            
            return this;
        },
        onSubmit: function(event) {
            var booResponse, i, restData, restClient, items;

            if (!this.isValid(event)) {
                booResponse =  false;
            } else {
                items = this.items.asArray();
                for (i=0; i<items.length; i+=1) {
                    if(items[i].applyStyleSuccess) {
                        items[i].applyStyleSuccess();
                    }
                }
                booResponse =  true;
            }
            if(this.project.submitRest){
                event.preventDefault();
                if(booResponse){
                    this.project.onSubmitForm();
                }
            }
            if (booResponse) {
                this.$el.find(".form-control").prop('disabled', false);
                this.$el.find("input[type='hidden']").prop('disabled', false);
                this.$el.find(".pmdynaform-control-checkbox").prop('disabled', false);
                this.$el.find(".pmdynaform-control-radio").prop('disabled', false);
            }
            return booResponse;
        },
        isValid: function(event) {
            var i, formValid = true, itemField,
            itemsField = this.items.asArray();

            if (itemsField.length > 0) {
                for (i = 0; i < itemsField.length; i+=1) {
                    if(itemsField[i].validate) {
                        if (event){
                            itemsField[i].validate(event);
                            if (!itemsField[i].model.get("valid")) {
                                if(itemField === undefined){
                                    itemField = itemsField[i];
                                }
                                formValid = itemsField[i].model.get("valid");
                            }
                        }else{
                            itemsField[i].validate();
                            formValid = itemsField[i].model.get("valid");
                            if (!formValid){
                                if (itemsField[i].model.get("type")!=="grid" && itemsField[i].model.get("type")!=="form"){
                                    itemsField[i].setFocus();
                                }
                                return false;
                            }   
                        }
                    }
                }
            }
            if (formValid){
                for (i = 0; i < itemsField.length; i+=1) {
                    if( ( itemsField[i].model.get("var_name") !== undefined) && (itemsField[i].model.get("var_name").trim().length === 0 )) {
                        if (itemsField[i].model.get("type") === "radio") {
                            itemsField[i].$el.find("input").attr("name","");
                        }
                    }
                }
                if (!event) {               
                    this.$el.find(".form-control").prop('disabled', false);
                    this.$el.find("input[type='hidden']").prop('disabled', false);
                    this.$el.find(".pmdynaform-control-checkbox").prop('disabled', false);
                    this.$el.find(".pmdynaform-control-radio").prop('disabled', false);
                }
            }else{
                if (itemField && itemField.model.get("type")!=="grid" && itemField.model.get("type")!=="form"){
                    itemField.setFocus();
                }
            }
            return formValid;
        },
        render : function (subForm){
            var i,j, $rowView;
            if (subForm){
                this.el = document.createElement("div");
                this.$el = $(this.el);
            }
            for(i=0; i<this.viewsBuilt.length; i+=1){
                $rowView = $(this.templateRow());
                for(j=0; j<this.viewsBuilt[i].length; j+=1){
                    /*if (this.viewsBuilt[i][j].model.attributes.type === "form") {
                        this.viewsBuilt[i][j].model.attributes.type = "subform";
                    }*/
                    $rowView.append(this.viewsBuilt[i][j].render().el);
                }                
                this.$el.append($rowView);
            }
            this.$el.attr("role","form");
            this.$el.addClass("form-horizontal pmdynaform-form");
            this.el.style.height = "99%";
            this.setAction();
            this.setMethod();
            this.$el.attr("id",this.model.get("id"));
            if (this.model.get("target")) {
                this.$el.attr("target", this.model.get("target"));
            }
            
            var ids = this.model.get("inputDocuments");
            for (var id in ids) {
                var hidenInputs = document.createElement("input");
                hidenInputs.name = "INPUTS[" + id + "]";
                hidenInputs.type = "hidden";
                hidenInputs.value = ids[id];
                this.el.appendChild(hidenInputs);
            }
                
            this.disableContextMenu();
          return this;
        },
        afterRender: function () {
            var i,
            j,
            items = this.items.asArray();;

            for (i=0; i<items.length; i+=1) {
                if (items[i].afterRender) {
                    items[i].afterRender();
                }
            }
            /*for(i=0; i<this.viewsBuilt.length; i+=1){
                for(j=0; j<this.viewsBuilt[i].length; j+=1){
                    if (this.viewsBuilt[i][j].afterRender) {
                        this.viewsBuilt[i][j].afterRender();
                    }
                }
            }*/

            if (this.model.attributes.data) {
                this.setData(this.model.get("data"));
            }
            
            
            return this;
        },

    });

    PMDynaform.extendNamespace("PMDynaform.view.FormPanel", FormPanel);
    
}());

(function(){
	var FieldView = Backbone.View.extend({
		tagName: "div",
        tagControl : "",
        tagHiddenToLabel : "",
        keyLabelControl : "",
        enableValidate : true, 
        events : {
                "click .form-control": "onclickField",
            },
		initialize: function (options) {
			if(options.project) {
                this.project= options.project;
            }

			this.setClassName()
				.render();
		},
		setClassName: function() {
			//this.$el.addClass(this.model.get("container").style.cssClasses.toString().replace(/,/g," "));			
			return this;
		},
		/*getData: function() {
			if( this.updateValueControl) {
				this.updateValueControl();
			}

            return this.model.getData();
        },*/
        enableTooltip: function(){
        	this.$el.find("[data-toggle=tooltip]").tooltip().click(function(e) {
                $(this).tooltip('toggle');
            });
        	return this;
        },
        applyStyleError: function () {
            this.$el.addClass("has-error has-feedback");
            return this;
        },
        applyStyleWarning: function () {
            this.$el.addClass('has-warning');
            return this;
        },
        applyStyleSuccess: function () {
            this.$el.removeClass("has-error");
            if (!this.model.get("disabled")) {
            	this.$el.addClass("has-success");
            }
            
            return this;
        },
        changeValuesFieldsRelated: function () {
            this.model.changeValuesFieldsRelated();
            return this;
        },
        /**
         * The method is only supported if the field have options. 
         * Checks if the value to sets is inside of the options property.
         */
        setValueToDomain: function () {
            var htmlElement = this.getHTMLControl();
            
            if (htmlElement.length && !this.model.attributes.disabled) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error');
                }
                
                if(!this.model.isValid()){    
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    
                    htmlElement.parent().append(this.validator.el);
                    this.applyStyleError();
                }
            }

            return this;
        },
        /**
         * Apply Javascript events associated to control
         *
         */
        on: function (e, fn) {
        	var that = this, 
        	control = this.$el.find("input");

        	if (control) {
        		control.on(e, function(event){
	        		fn(event, that);

	        		event.stopPropagation();
	        	});
        	} else {
        		throw new Error ("Is not possible find the HTMLElement associated to field");
        	}
        	
        	return this;
        },
        /**
         * The method is just for return the Jquery HTML of the control 
         * @return {JQuery HTMLElement} Encapsulate the HTMLElement
         */
        getHTMLControl: function () {
            return this;
        },
		render: function() {
			this.$el.html( this.template(this.model.toJSON()) );
			if (this.model.get("hint")) {
				this.enableTooltip();
			}
            this.setValueToDomain();
			return this;
		},
        onclickField : function (){
            return this;
        },
        setLabel : function (label, col) {
            var tagLabel;
            if (this.model.get("type") === "grid"){
                if (col !== undefined){
                    if(col>0 && col <= this.columnsModel.length){
                        this.domTitleHeader[col-1].find("span[class='title-column']").text(label);
                    }else{
                        return null;
                    }
                }
            }else{
                if (this.model.attributes.label !== undefined) {
                    this.model.attributes.label = label;
                    if (this.el || this.$el.length) {
                        this.$el.find("label").find("span[class='textlabel']").text(label);
                        this.$el.find("h4").find("span[class='textlabel']").text(label);
                        this.$el.find("h5").find("span[class='textlabel']").text(label);
                        this.$el.find(".pmdynaform-grid-title span").text(label);
                        this.$el.find(".pmdynaform-control-annotation span").text(label);
                        this.$el.find("button[type='button'].btn-primary span").text(label);
                        this.$el.find("button[type='submit'] span").text(label);
                    }
                }
            }
            return this;
        },
        getLabel : function (col) {
            if (this.model.get("label") !== undefined){
                if (col && this.model.get("type") === "grid"){
                    if(col <= this.columnsModel.length){
                        return this.columnsModel[col-1].label || "";
                    }else{
                        return null;
                    }
                }else{
                    return this.model.get("label");
                }
            }
            return null;
        },
        setText : function (value) {
            var fieldWithOptions, label, options, keyValue, data, existData, type, dataType,  option;
            existData = false;
            type = this.model.get("type");
            dataType = this.model.get("dataType");
            data = {};
            if (type === "grid") {
                if (row !== undefined && col !== undefined){
                    if((row>0 && col>0) && row <= this.gridtable.length && col <= this.columnsModel.length){
                        return this.gridtable[row-1][col-1].setValue(value);
                    }else{
                        return null;
                    }
                }
            }
            if ( value && value.toString().length || jQuery.isArray(value)){
                options = this.model.get("options");
                if (options && jQuery.isArray(options)){
                    if(dataType === "boolean") {
                        var valuesfortrue = [1,true,"1","true"];
                        var valuesforFalse = [0,false,"0","false"];
                        if(options[0].label === value){
                            value = options[0].value;
                        }
                        if(options[1].label === value){
                            value = options[1].value;   
                        }
                        if(valuesfortrue.indexOf(options[0].value) > -1 && 
                            valuesfortrue.indexOf(value) > -1) {
                            data = {
                                    value : "1",
                                    label : options[0].label
                            }
                            existData = true;
                        }
                        if(valuesforFalse.indexOf(options[1].value) > -1 && 
                            valuesforFalse.indexOf(value) > -1) {
                            data = {
                                    value : "0",
                                    label : options[1].label
                            }
                            existData = true;                            
                        }
                    }else{
                        if (type === "checkgroup"){
                            var arrayDataValue = [], arrayDataLabel = [];
                            for (var i = 0 ; i < options.length ; i +=1){
                                options[i].selected = false;
                                if (value.indexOf(options[i].label)>-1){
                                    options[i].selected = true;
                                    arrayDataLabel.push(options[i].label);
                                    arrayDataValue .push(options[i].value);
                                }
                            }
                            data = {
                                value : arrayDataValue,
                                label : arrayDataLabel
                            }
                            existData = true;
                        }else{
                            for (var i = 0 ; i < options.length ; i +=1){
                                if (options[i].label === value){
                                    data = {
                                        label : options[i].label,
                                        value : options[i].value
                                    }
                                    existData = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                if ( !existData ) {
                    if ( type === "text" || type === "textarea" || type === "hidden" || type === "link" || type === "image" ||
                        type == "title" || type == "annotation" || type == "subtitle" || type === "button" || type === "submit") {
                        data = {
                            value : value,
                            label : value
                        }
                        existData = true;
                    }
                    if( type === "datetime") {
                        value = value.replace(/-/g,"/");
                        if (new Date(value).toString() !== "Invalid Date") {
                            data = {
                                value : value,
                                label : value
                            }
                        }
                        existData = true;
                    }
                }
                if (existData){
                    this.updateValueControlAndData(data, type, dataType);
                }
            }else{
                if (this.model.get("type") === "annotation"){
                    this.$el.find(".pmdynaform-control-annotation span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "button"){
                    this.$el.find("button[type='button'].btn-primary span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "submit"){
                    this.$el.find("button[type='submit'] span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "image" ) {
                    this.model.attributes.src = value;
                    this.$el.find("img").attr("src",value);
                }
            }
            return this;
        },
        updateValueControlAndData : function (data, type, dataType){
            var i;
            if ( this.tagControl instanceof jQuery && this.tagHiddenToLabel instanceof jQuery) {
                if (type !== "datetime"){
                    this.model.attributes.data = data;
                    this.model.attributes.value = data["value"];
                    if (this.model.attributes.hasOwnProperty("keyLabel")){
                        this.model.attributes.keyLabel = data["label"]; 
                    }
                    if (this.model.attributes.hasOwnProperty("keyValue")){
                        this.model.attributes.keyValue = data["value"];
                    }
                    if (type === "radio"){
                        this.tagControl.find("input[id='form\["+this.model.get("id")+"\]'][value='"+data["value"]+"']").prop("checked",true);
                    }else if( type === "checkgroup" || type === "checkbox"){
                        this.tagControl.find("input[type='checkbox']").attr("checked",false);
                        for (i=0; i < data["value"].length ; i+=1){
                            this.tagControl.find("input[id='form\["+this.model.get("id")+"\]["+data["value"][i]+"]']").prop("checked",true);
                        }
                        this.model.attributes.labelsSelected = data["label"];
                    } else{
                        this.tagControl.val(data["value"]);
                    }
                    if (type === "checkgroup"){
                        this.tagHiddenToLabel.val(JSON.stringify(data["label"]));
                    }else{
                        if (type === "link" || type === "annotation" || type === "title" || type === "subtitle" ||
                            type === "button" || type === "submit") {
                            this.tagHiddenToLabel.html(data["label"]);
                        }else if(type === "image"){
                            this.setSrc(data["value"]);
                        }else{
                            this.tagHiddenToLabel.val(data["label"]);
                        }
                    }
                    if (this.validate){
                        this.validate();
                    }
                }else{
                    this.$el.find("#datetime-container-control").data()["DateTimePicker"].date(new Date(data["value"]));
                    var label = this.$el.find("#datetime-container-control").data()["date"];
                    var value = this.formatData();
                    this.model.attributes.value = value;
                    this.model.attributes.data["value"] = value;
                    this.model.attributes.data["label"] = label;
                    this.model.attributes.keyLabel = label;
                    this.tagHiddenToLabel.val(this.model.get("data")["value"]);
                }
            }
            return this;
        },
        setValue : function (value, row, col) {
            var fieldWithOptions, label, options, keyValue, data, existData, type, dataType,  option;
            existData = false;
            type = this.model.get("type");
            dataType = this.model.get("dataType");
            data = {};
            if (type === "grid") {
                if (row !== undefined && col !== undefined){
                    if((row>0 && col>0) && row <= this.gridtable.length && col <= this.columnsModel.length){
                        return this.gridtable[row-1][col-1].setValue(value);
                    }else{
                        return null;
                    }
                }
            }
            if (typeof value === "boolean" && value === false) {
                value = value.toString();
            }
            if ( value !== undefined && value.toString().length || jQuery.isArray(value)){
                if (this.firstLoad){
                    this.firstLoad = false;
                }
                if (!this.dirty){
                    this.dirty = true;
                }
                if (this.model.attributes.parentDependents && this.model.attributes.parentDependents.length){
                    this.jsonData = this.generateDataDependenField();
                    var remoteOptions = this.executeQuery();
                    this.mergeOptions(remoteOptions, true);
                    this.model.set("value",value);
                }else{
                    this.model.set("value",value);
                }
                options = this.model.get("options");
                if (options && jQuery.isArray(options)){
                    if(dataType === "boolean") {
                        var valuesfortrue = [1,true,"1","true"];
                        var valuesforFalse = [0,false,"0","false"];
                        if(valuesfortrue.indexOf(options[0].value) > -1 && 
                            valuesfortrue.indexOf(value) > -1) {
                            data = {
                                    value : "1",
                                    label : options[0].label
                            }
                            existData = true;
                        }
                        if(valuesforFalse.indexOf(options[1].value) > -1 && 
                            valuesforFalse.indexOf(value) > -1) {
                            data = {
                                    value : "0",
                                    label : options[1].label
                            }
                            existData = true;                            
                        }
                    }else{
                        if (type === "checkgroup"){
                            var arrayDataValue = [], arrayDataLabel = [];
                            for (var i = 0 ; i < options.length ; i +=1){
                                options[i].selected = false;
                                if (value.indexOf(options[i].value)>-1){
                                    options[i].selected = true;
                                    arrayDataLabel.push(options[i].label);
                                    arrayDataValue .push(options[i].value);
                                }
                            }
                            data = {
                                value : arrayDataValue,
                                label : arrayDataLabel
                            }
                            existData = true;
                        }else{
                            for (var i = 0 ; i < options.length ; i +=1){
                                if (options[i].value === value){
                                    data = {
                                        label : options[i].label,
                                        value : options[i].value
                                    }
                                    existData = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                if ( !existData ) {
                    if ( type === "text" || type === "textarea" || type === "hidden" || type === "link" || type === "image" ||
                        type == "title" || type == "annotation" || type == "subtitle" || type === "button" || type === "submit") {
                        data = {
                            value : value,
                            label : value
                        }
                        existData = true;
                    }
                    if( type === "datetime") {
                        value = value.replace(/-/g,"/");
                        if (new Date(value).toString() !== "Invalid Date") {
                            data = {
                                value : value,
                                label : value
                            }
                        }
                        existData = true;
                    }
                }
                if (existData){
                    this.updateValueControlAndData(data, type, dataType);
                }
            }else{
                if (type === "link" ) {
                    this.model.attributes.value = value;
                    this.$el.find(".pmdynaform-control-link span").text(value);
                }
                if (type === "image" ) {
                    this.model.attributes.src = value;
                    this.$el.find("img").attr("src",value);
                }
            }
            return this;
        },
        getInfo : function () {
            return this.model.toJSON();
        },
        getValue : function (row, col) {
            var data, val = null;
            if (row !== undefined && col !== undefined){
                if((row>0 && col>0) && row <= this.gridtable.length && col <= this.columnsModel.length){
                    return this.gridtable[row-1][col-1].getValue();
                }else{
                    return null;
                }
            }
            if (this.model.getData !== undefined){
                return this.model.getData()["value"];
            }
            return null;
        },
        setHref : function (value) {
            this.model.attributes.href = value;
            this.$el.find("a").attr("href",value);
            return this;
        },        
        getDataType : function () {
            return this.model.get("dataType") || null;
        },
        getControlType : function () {

        },
        verifyData : function (){
            /*var data = {}, value;
            if ( this.model.get("value") && this.model.get("value").trim().length ) {
                data["value"] = this.model.get("value");
                data["label"] = this.model.get("label");
                this.model.set("data",data); 
            }
            return this;*/
        },
        getData: function() {
            return this.model.getData();
        },
        getDataLabel: function() {
            return this.model.getKeyLabel();
        },
        getText : function (row, col) {
            if (row !== undefined && col !== undefined){
                if(row <= this.gridtable.length && col <= this.columnsModel.length){
                    if (this.gridtable[row-1][col-1].model.getKeyLabel) {
                        return this.gridtable[row-1][col-1].model.get("data")["label"];
                    }
                    return null;
                }else{
                    return null;
                }
            }else{
                if (this.model.getKeyLabel){
                    if (this.model.get("type") === "link"){
                        return this.model.get("value");
                    }
                    if (this.model.get("type") === "image"){
                        return this.getSrc();
                    }
                    return this.model.getKeyLabel()["value"];
                }else{
                    return null;
                }
            }
        },
        disableValidation : function (col) {
			var type, i, j;
			var col = col;
			type = this.model.get("type");
			if (type === "grid") {
                if (col !== undefined){
					if (typeof col == "string") {
						this.columnsModel.find(function(column, index){
							if (column.columnId === col){
								col = index+1;
								return;
							}
						})
					}
					if ((col>0 && col <= this.columnsModel.length) ){
						this.domTitleHeader[col-1].find(".pmdynaform-field-required").hide();
						for (i=0 ; i < this.gridtable.length ;  i+=1){
							this.columnsModel[col-1].enableValidate = false;
							this.gridtable[i][col-1].disableValidation();
						}
                    }else{
                        return null;
                    }
                }else{
					var i, j, row, cell;
					for ( i = 0 ; i < this.gridtable.length ; i+=1 ) {
						row = this.gridtable[i];
						for ( j = 0 ; j < row.length ; j+=1 ) {
							cell = row[j];
							if ( cell.model.get("enableValidate") !== undefined ){
								cell.disableValidation();
								this.columnsModel[j].enableValidate = false;
								this.domTitleHeader[j].find(".pmdynaform-field-required").hide();
							}
						}
					}
				}
            }			
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
				if (this.model.get("required")){
					this.$el.find(".pmdynaform-field-required").hide();
				}
                this.model.attributes.enableValidate = false;
            }
            return this;
        },
		enableValidation : function (col) {
			var type, i, j, col = col;
			type = this.model.get("type");
			if (type === "grid") {
                if (col !== undefined){
					if (typeof col == "string") {
						this.columnsModel.find(function(column, index){
							if (column.columnId === col){
								col = index+1;
								return;
							}
						})
					}
                    if(col>0 && col <= this.columnsModel.length){
						this.domTitleHeader[col-1].find(".pmdynaform-field-required").show();
						for (i=0 ; i < this.gridtable.length ;  i+=1){
							this.columnsModel[col-1].enableValidate = true;
							this.gridtable[i][col-1].enableValidation();
						}
                    }else{
                        return null;
                    }
                }else{
					var i, j, row, cell;
					for ( i = 0 ; i < this.gridtable.length ; i+=1 ) {
						row = this.gridtable[i];
						for ( j = 0 ; j < row.length ; j+=1 ) {
							cell = row[j];
							if ( cell.model.get("enableValidate") !== undefined ){
								cell.enableValidation();
								this.columnsModel[j].enableValidate = true;
								this.domTitleHeader[j].find(".pmdynaform-field-required").show();
							}
						}
					}
				}
            }			
            if (this.model.get("enableValidate") !== undefined) {
				this.model.attributes.enableValidate = true;
				if (this.model.get("group") === "form"){
					this.$el.find(".pmdynaform-field-required").show();
				}
            }
            return this;
        },
        getControl : function (row, col) {
            var htmlControl = jQuery("");
            if (this.model.get("type") === "grid"){
                if (row !== undefined && col !== undefined){
                    if((row>0 && col>0) && row <= this.gridtable.length && col <= this.columnsModel.length){
                        htmlControl = this.gridtable[row-1][col-1].getControl();
                    }
                }
            }            
            if (this.model.get("type") === "text"){
                htmlControl = this.$el.find("input[type='text']");
            }
            if (this.model.get("type") === "textarea"){
                htmlControl = this.$el.find("textarea");
            }
            if (this.model.get("type") === "radio"){
                htmlControl = this.$el.find("input[type='radio']");
            }
            if (this.model.get("type") === "dropdown"){
                htmlControl = this.$el.find("select");
            }
            if (this.model.get("type") === "checkbox"){
                htmlControl = this.$el.find("input[type='checkbox']");
            }
			if (this.model.get("type") === "checkgroup"){
                htmlControl = this.$el.find("input[type='checkbox']");
            }
            if (this.model.get("type") === "datetime"){
                htmlControl = this.$el.find("input[type='text']");
            }
            if (this.model.get("type") === "suggest"){
                htmlControl = this.$el.find("input[type='suggest']");
            }
            if (this.model.get("type") === "hidden"){
                htmlControl = this.$el.find("input[type='hidden']").eq(0);
            }
			if (this.model.get("type") === "file"){
				htmlControl = this.$el.find("button").eq(0);
            }
            return htmlControl;
        },
        getLabelControl : function () {

        },
        getSummary : function (col){
            var tag;
			var col = col;
            if (col !== undefined){
					if (typeof col == "string") {
						this.columnsModel.find(function(column, index){
							if (column.columnId === col){
								col = index+1;
								return;
							}
						});
					}
                if(col>0 && col <= this.columnsModel.length){
                    if (this.columnsModel[col-1].operation){
                        tag =  this.$el.find("#"+this.columnsModel[col-1].operation+"-"+this.model.get("name")+"-"+this.columnsModel[col-1].name);
                    }
                    if (tag){
                        return tag.val();
                    }else{
                        return null;
                    }
                }else{
                    return null;
                }
            }
        },
        getHref : function () {
            return this.model.get("href");
        },
		setFocus : function(){
			this.getControl().first().focus();
		}
	});	
	PMDynaform.extendNamespace("PMDynaform.view.Field",FieldView);
}());

(function(){
	var GridView = PMDynaform.view.Field.extend({
		block: true,
		template: _.template( $("#tpl-grid").html()),
		templatePager: _.template( $("#tpl-grid-pagination").html() ),
		templateTotal: _.template( $("#tpl-grid-totalcolumn").html() ),
	    colSpanLabel: 3,
        colSpanControl: 9,
        gridtable: [],
        flagRow: 0,
        dom: [],
        row: [],
        cols: [],
        showPage: 1,
        items: [],
        numberRest: 0,
        rest: 0,
        priority: {
			file: 1,
			image: 2,
			radio: 3,
			checkbox: 4,
			textarea: 5,
			datetime:6,
			dropdown: 7,
			text: 8,
			button: 9,
			link: 10,
			defect: 0
		},
		section : 1,
        titleHeader: [],
		indexResponsive : "3%",
		removeResponsive : "3%",
        thereArePriority: 0,
        columnsModel : [],
        domCarousel : null,
        tableBody : null,
        pageSize : null,
        paged : null,
        rowDataAdd : null,
        domTitleHeader : null,
        totalWidtRow : 0,
        colResponsiveTotalWidth : null,
        hiddenColumns : [],
        totalWidthStatic : 0,
        onDeleteRowCallback: function(){},
        onAddRowCallback: function(){},
        onBeforeAddRowCallback : function (){},
        onClickPageCallback: function(){},
        events: {
                "click .pmdynaform-grid-newitem": "onClickNew",
                "click .pagination li": "onClickPage"
        },
        requireVariableByField: [
            "text",
            "textarea",
            "checkbox",
            "radio",    
            "dropdown",
            "datetime",
            "suggest",
            "link",
            "hidden",
            "label"
        ],
        factory : {},
        validDependentColumns : [],
        sqlColumns : [],
		initialize: function (options) {
			var factory = {
	            products: {
	                "text": {
	                    model: PMDynaform.model.Text,
	                    view: PMDynaform.view.Text
	                },
	                "textarea": {
	                    model: PMDynaform.model.TextArea,
	                    view: PMDynaform.view.TextArea
	                },
	                "checkbox": {
	                    model: PMDynaform.model.CheckBox,
	                    view: PMDynaform.view.CheckBox
	                },
	                "radio": {
	                    model: PMDynaform.model.Radio,
	                    view: PMDynaform.view.Radio
	                },
	                "dropdown": {
	                    model: PMDynaform.model.Dropdown,
	                    view: PMDynaform.view.Dropdown
	                },
	                "button": {
	                    model: PMDynaform.model.Button,
	                    view: PMDynaform.view.Button
	                }, 
	                "datetime": {
	                    model: PMDynaform.model.Datetime,
	                    view: PMDynaform.view.Datetime
	                },
	                "suggest": {
	                    model: PMDynaform.model.Suggest,
	                    view: PMDynaform.view.Suggest
	                },                                        
	                "link": {
	                    model: PMDynaform.model.Link,
	                    view: PMDynaform.view.Link
	                },                                        
	                "file": {
	                    model: PMDynaform.model.File,
	                    view: PMDynaform.view.File
	                },
	                "label": {
                        model: PMDynaform.model.Label,
                        view: PMDynaform.view.Label
                    },
	                "hidden": {
                    	model: PMDynaform.model.Hidden,
                        view: PMDynaform.view.Hidden
                    }
	            },
	            defaultProduct: "text"
	        },
	        k,
	        rows = parseInt(this.model.get("rows"), 10);
	        this.pageSize = this.model.get("pageSize");
            this.validDependentColumns = ["dropdown","suggest","text"];
            this.paged = this.model.get("pager");
            this.colResponsiveTotalWidth = 0;
	        this.items = [];
	        this.row = [];
	        this.dom = [];
	        this.cols = [];
	        this.showPage = 1;
			this.gridtable = [];
			this.titleHeader = [];
			this.checkColSpanResponsive();
			this.setFactory(factory);
			this.rowDataAdd = [];
			/*this.buildColumns({
				executeInit: true
			});*/
			this.dom = [];
			this.makeColumnModels();
			/*for (k=0; k<rows; k+=1) {
				this.addRow();
			}*/
			this.model.attributes.titleHeader = this.titleHeader;
			
		},
		/*buildColumns: function () {
			var row;
			row = this.makeColumns({
            	executeInit: true
            });
            this.model.attributes.dataColumns = row.model;
            this.model.attributes.totalRow = row.data;
            this.items = [];
            this.model.attributes.gridFunctions = [];
			return this;
		},*/
		onClickNew: function (e) {
			var newItem;
			newItem = this.addRow(e);
			return this;
		},
		addRow : function (data) {
			var i, j, row, product, rowData, currentRows, flagRow;
            rowData = this.model.get("data");
			currentRows = this.model.get("rows");
			this.rowDataAdd = [];
			if (this.model.get("layout") === "static") {
				flagRow = this.tableBody.find(".pmdynaform-static").last().children().length;
				this.domCarousel = this.tableBody.find(".pmdynaform-static").last();
			}else{
				flagRow = this.tableBody.children().last().children().length;
				this.domCarousel = this.tableBody.children().last();
			}
			if (flagRow === this.pageSize || flagRow === 0){
				this.block = true;
				this.section = Math.ceil(this.dom.length/this.pageSize)+1;
				flagRow = 0;
			}else{
				this.block = false;
				this.section = Math.ceil(this.dom.length/this.pageSize);
			}
			this.onBeforeAddRowCallback(this, this.model.attributes.rows, this.rowDataAdd);
			if (data && jQuery.isArray(data) && data.length){
				this.rowDataAdd = data;
			}
			row = this.createHTMLRow(currentRows, this.rowDataAdd, flagRow);
			this.model.attributes.rows = parseInt(currentRows + 1, 10);
			this.model.setPaginationItems();
			this.createHTMLPager("add");
            
            this.model.attributes.gridFunctions.push(row.data);
            
            for (j = 0 ; j < row.model.length ; j+=1){
            	if (row.model[j].get("type")!== "label"  && row.model[j].get("operation") && row.model[j].get("operation").trim().length){
					row.view[j].onChangeCallbackOperation();
            	}
            	if (row.model[j].get("type") == "label" && row.model[j].get("operation")){
            		this.createHTMLTotal();
            	}
            }
			if (typeof this.onAddRowCallback === "function") {
				this.onAddRowCallback(this.gridtable[currentRows], this, this.gridtable.length);
			}
			return this.gridtable[currentRows];
		},
		removeRow: function (row) {
			var currentRows = this.model.get("rows"),
			itemRemoved;

			itemRemoved = this.gridtable.splice(row, 1);
			this.dom.splice(row, 1);
			this.model.attributes.rows = parseInt(currentRows - 1, 10);
			
			return itemRemoved;
		},
        makeColumnModels : function (){
			var that = this,
			columns = this.model.get("columns"),
			data = this.model.get("data"),
			columnModel,
			suc,
			colSpanControl,
            factory = this.factory,
            size = this.gridtable.length,
            product,
            newNameField,
            newIdField,
            rowView = [],
            rowModel = [],
            productModel,
            variableEnabled,
            productBuilt,
            jsonFixed,
            mergeModel,
            newDependentFields,
            i;
            this.columnsModel = [];
			for (i = 0; i < columns.length; i+=1) {
            	newNameField = "";
            	mergeModel = columns[i] ;
            	mergeModel.form = this.model.get("form") || null;
				if ( mergeModel.mode && mergeModel.mode === "parent" ) {
					mergeModel.mode = this.model.get("mode");
				}
            	if ( (mergeModel.originalType === "checkbox"  || mergeModel.type === "checkbox" ) && mergeModel.mode === "view" ) {
            		mergeModel.mode = "disabled";
            		mergeModel.disabled = true;
            	}
				if ( (mergeModel.originalType === "checkbox"  || mergeModel.type === "checkbox" ) && mergeModel.mode === "disabled" ) {
					mergeModel.mode = "disabled";
					mergeModel.disabled = true;
             	}
            	jsonFixed  = new PMDynaform.core.TransformJSON({
                    parentMode: this.model.get("parentMode"),
                    field: mergeModel
                });
	            if (jsonFixed.getJSON().type) {
	                product =   factory.products[jsonFixed.getJSON().type.toLowerCase()] ? 
	                    factory.products[jsonFixed.getJSON().type.toLowerCase()] : factory.products[factory.defaultProduct];
	            } else {
	                product = factory.products[factory.defaultProduct];
	            }
				colSpanControl = this.colSpanControlField(jsonFixed.getJSON().type, i);
            	//variableEnabled = this.validateVariableField(mergeModel);
	            columnModel = {
            		colSpanLabel: 4,
                	colSpanControl: (this.model.get("layout") === "form") ? 8:colSpanControl,
                	colSpan: colSpanControl,
                	label: mergeModel.title,
                	title: mergeModel.title,
                	layout: this.model.get("layout"),
                	width: "200px",
	                project: this.model.get("project"),
	                namespace: this.model.get("namespace"),
	                mode: this.model.get("mode"),
	                variable: (variableEnabled !== "NOT")? this.getVariable(mergeModel.var_uid) : null,
	                _extended: {
	                	name: mergeModel.name || PMDynaform.core.Utils.generateName("radio"),
	                	id: mergeModel.id || PMDynaform.core.Utils.generateID(),
	                	dependentFields: mergeModel.dependentFields,
	                	formula: mergeModel.formula || null
	                },
	                group: "grid",
	                columnName : mergeModel.name || PMDynaform.core.Utils.generateName("radio"),
	                columnId : mergeModel.id,
					originalType : mergeModel.type,
	                product : product,
	                formula : mergeModel.formula || "",
	                operation : mergeModel.operation || "",
	                columnWidth : mergeModel.columnWidth || "",
	                defaultValue : mergeModel.defaultValue || "",
					required : mergeModel.required || false,
	                hint : mergeModel.hint || ""
	            };
            	jQuery.extend(true, columnModel, jsonFixed.getJSON());
            	columnModel.row = this.gridtable.length;
            	columnModel.col = i;
	        	if (this.model.get("layout") == "static"){
					if (columnModel.columnWidth && jQuery.isNumeric(columnModel.columnWidth)){
						var width = parseInt(columnModel.columnWidth);
						this.totalWidtRow = this.totalWidtRow + width;
					}else{
						this.totalWidtRow = this.totalWidtRow + 200;
					}
	        	}
				this.columnsModel.push(columnModel);
            }
        	if (this.model.get("layout") == "responsive"){
        		this.updateWidthResponsiveColumns();
        	}
            return this;
        },
        updateWidthResponsiveColumns : function () {
        	var i, totalWith=0, width, undefinedWidth = [];
        	if ( this.columnsModel.length ) {
        		for (i=0;i<this.columnsModel.length;i+=1){
        			width = parseInt(this.columnsModel[i].columnWidth).toString(); 
        			if(width !== "NaN"){
        				if (totalWith+Number(width) < 94){
        					totalWith = totalWith+Number(width);
							this.columnsModel[i].columnWidth = Number(width)+"%";
        				}else{
        					if (94 - totalWith > 0){
		        				this.columnsModel[i].columnWidth = 94 - totalWith+"%";
		        				totalWith = totalWith + 94 - totalWith;
        					}else{
		        				this.columnsModel[i].columnWidth = 0 + "%";
		        				undefinedWidth.push(this.columnsModel[i]);
        					}
        				}
        			}else{
        				this.columnsModel[i].columnWidth = 0 + "%";
        				undefinedWidth.push(this.columnsModel[i]);
        			}
        		}
        	}
        	return this;
        },
		setValuesGridFunctions: function (field) {

			if (this.model.attributes.functions) {
				this.model.attributes.gridFunctions[field.row][field.col] = isNaN(parseFloat(field.data))? 0: parseFloat(field.data);
				this.model.applyFunction();
			}
			return this;
		},
		getVariable: function (var_uid) {
            var i,
            varSelected,
            variables = this.model.attributes.variables;

            loop_variables:
            for (i=0; i<variables.length; i+=1) {
                if (variables[i] && variables[i].var_uid === var_uid) {
                    varSelected = variables[i];
                    break loop_variables;
                }
            }

            return varSelected;
        },
		checkColSpanResponsive: function () {
			var i,
			columns = this.model.get("columns"),
			thereArePriority = 0,
			layout = this.model.get("layout");

			if (layout === "responsive" || layout === "form") {
				this.numberRest = 10%columns.length;

				if (this.numberRest > 0) {
					for (i=0; i<columns.length; i+=1) {
						if (this.priority[columns[i].type] <= 6) {
							thereArePriority +=1;
						}
					}
				}
				this.thereArePriority = thereArePriority;
			}
			return this;
		},
		colSpanControlField: function (type, indexColumn) {
			var rest,
			itemsLength = this.model.get("columns").length,
			layout = this.model.get("layout"),
			defaultColSpan = 8;
			if (this.numberRest > 0) {
				if (this.priority[type] <= 6 && this.thereArePriority > 0) {
					defaultColSpan = parseInt(10/itemsLength) +1;
					this.numberRest -=1;
					this.thereArePriority -=1;
				} else {
					if (this.numberRest >= parseInt(itemsLength - indexColumn)) {
						defaultColSpan = parseInt(10/itemsLength) +1;
						this.numberRest -=1;
					} else {
						defaultColSpan = parseInt(10/itemsLength);
					}
				}
			} else {
				defaultColSpan = parseInt(10/itemsLength);
			}

			return defaultColSpan;
		},
		colSpanControlFieldResponsive : function  () {
			var columnWidth = 100, res;
			res = parseInt(this.indexResponsive) + parseInt(this.removeResponsive);
			columnWidth = parseInt((columnWidth - res)/(this.columnsModel.length-this.model.get("countHiddenControl")));
			return columnWidth - 1;
		},

		/*changeNameField: function (nameform, row, column) {
			return nameform+"]["+row+"]["+column;

		},*/
		/*
		form[grid1][1][nombre]
		form[grid1][2][nombre]
		*/
		changeIdField : function (nameform, row, column){
			return "["+nameform+"]["+row+"]["+column+"]";
		},
		changeNameField : function (nameform, row, column){
			return "["+nameform+"]["+row+"]["+column+"]";
		},
		/*changeNameField: function (name, row, column) {	 
			return name + "_" + row + "_" + column;	
		},*/	
		updateNameFields: function (rowView) {
			var i,
			j, 
			k,
			l,
			label,
			formulaFields = "",
			dependentFields,
			newDependentFields = [];
			for (i=0; i< rowView.length; i+=1) {
				formulaFields = rowView[i].model.get("_extended").formula;
				if (typeof formulaFields === "string") {
					for (l=0; l< rowView.length; l+=1) {
						if (i !== l) {
							formulaFields = formulaFields.replace(new RegExp(rowView[l].model.get("_extended").id, 'g'), rowView[l].model.get("id"));
							rowView[i].model.attributes.formula = formulaFields;
							rowView[i].model.attributes.formulator.data = formulaFields;
						}
					}
				}
			}
			return newDependentFields;
		},
		setFactory: function (factory) {
            this.factory = factory;
            return this;
        },
		validate: function(event) {
			var i, 
			k, 
			gridpanel,
			fields,
			row = [],
			validGrid = true,
			gridpanel = this.gridtable,
			itemCell;

			for (i=0; i<gridpanel.length; i+=1) {
				row = [];
				for (k=0; k<gridpanel[i].length; k+=1) {
					if(gridpanel[i][k].validate) {
						if (event){
							gridpanel[i][k].validate(event);
							if (!gridpanel[i][k].model.get("valid")) {
								if(itemCell === undefined){
									itemCell = gridpanel[i][k];
								}
								validGrid = gridpanel[i][k].model.get("valid");
								this.model.set("valid",validGrid);
								validGrid = false;
							}
						}else{
							gridpanel[i][k].validate();
							validGrid = gridpanel[i][k].model.get("valid");
							if (!validGrid){
								gridpanel[i][k].setFocus();
								this.model.attributes.valid = false;
                                return false;
                            }
						}
                    }
				}
			}
			if (itemCell){
				itemCell.setFocus();
			}
			this.model.set("valid",validGrid);
			return validGrid;
		},
		onRemoveRow: function (event) {
			var rowNumber, itemRemoved;
			if (event) {
				rowNumber = $(event.target).data("row");
				this.deleteRow(rowNumber,event);
			}

			return this;
		},
		updateGridFunctions : function (rows, index){
			var removed;
			removed = this.model.attributes.gridFunctions.splice(index-1,1);
			this.model.applyFunction()
			this.createHTMLTotal();
			return this;
		},
		deleteRow : function (index, event){
			var itemRemoved, table, partyfloat, showSection = 1, showPage, removedSection, initPage;
			if(this.gridtable.length === 1) {
				if (event){
					event.preventDefault();
					event.stopPropagation();
				}
				return false;
			}
			showPage  = Math.ceil(index/this.pageSize);
			jQuery(this.dom[index-1]).remove();
			if (index > 0) {
				itemRemoved = this.removeRow(index-1);
			}else{
				return this;
			}
			this.updateGridFunctions(itemRemoved, index);
			this.updatePropertiesCell(index-1);
			if (this.model.attributes.pager){
				this.block = true;
				this.flagRow = 0;
				this.section = 0;
				if (this.model.get("layout") === "static"){
					for (var i = showPage ; i < this.tableBody.find(".pmdynaform-static").length ; i+=1) {
						if (this.tableBody.find(".pmdynaform-static").eq(i).children().length){
							this.tableBody.find(".pmdynaform-static").eq(i-1).append(this.tableBody.find(".pmdynaform-static").eq(i).children()[0])
						}
					}
					if (this.tableBody.find(".pmdynaform-static").eq(i-1).children().length === 0){
						removedSection = this.tableBody.find(".pmdynaform-static").eq(i-1).remove();
						if (i==1){
							initPage = true;
						}
					}
					if (!this.tableBody.find(".pmdynaform-static").eq(showPage-1).children().length){
						if (this.tableBody.find(".pmdynaform-static").eq(showPage-2).length){
							this.tableBody.find(".pmdynaform-static").eq(showPage-2).addClass("active");
						}
					}
				}else{
					for (var i = showPage ; i < this.tableBody.children().length ; i+=1) {
						if (this.tableBody.children().eq(i).children().length){
							this.tableBody.children().eq(i-1).append(this.tableBody.children().eq(i).children()[0])
						}
					}
					if (this.tableBody.children().eq(i-1).children().length === 0){
						this.tableBody.children().eq(i-1).remove();
					}
					if (!this.tableBody.children().eq(showPage-1).children().length){
						if (this.tableBody.children().eq(showPage-2).length){
							this.tableBody.children().eq(showPage-2).addClass("active");
						}
					}
				}
				this.model.setPaginationItems();
				this.createHTMLPager("remove");
				if (removedSection && removedSection.length){
					this.showPage = showPage-1;
					if (initPage){
						this.showPage = 1;
					}
				}else{
					this.showPage = showPage;
				}
			}
			if (typeof this.onDeleteRowCallback==="function"){
				this.onDeleteRowCallback(this, itemRemoved, index);
			}
			return this;
		},		
		updatePropertiesCell : function (index) {
			var i, j, cell, cells, row, rows, element, name, control, container, idContainer,
			hiddenControls, type, nameHiddeControl, nameControl, idcontrol;
			rows = this.gridtable;
			for ( i = index ; i <  rows.length; i+=1) {
				row = $(this.dom[i]);
				row.find(".index-row span").text(i+1);
				row.find(".remove-row button").data("row",i+1);
				cells = rows[i];
				if (cells){
					for ( j = 0 ; j < cells.length ; j+=1 ) {
						cell = cells[j];
						cell.model.attributes.row = i;
						idContainer =  this.changeIdField(this.model.get("id"), i+1 , this.columnsModel[j].id);
						element = cell.$el;
						container = element.find(".pmdynaform-"+cell.model.get("mode")+"-"+cell.model.get("type"));
						container.attr({
							"id" : idContainer
						});
						type = cell.model.get("type");
						switch (type){
							case "suggest":
								control = $(cell.$el.find(".form-control"));
								hiddenControls = element.find("input[type='hidden']");
								if (this.model.get("variable")){
									nameControl = "form" + this.changeIdField(this.model.get("name"), i+1 , cell.model.get("columnName"));
									nameControl = nameControl.substring(0,nameControl.length-1).concat("_label]"); 
									nameHiddeControl = "form" + this.changeIdField(this.model.get("name"), i+1 , cell.model.get("columnName"));
								} else {
									nameControl = "";
									nameHiddeControl = ""
								}
								idcontrol = "form" + this.changeIdField(this.model.get("id"), i+1 , this.columnsModel[j].id);
								control.attr({
									name : nameControl,
									id : idcontrol
								});
								hiddenControls.attr({
									name : nameHiddeControl,
									id : idcontrol
								});
							break;
							case "label":
								hiddenControls = element.find("input[type='hidden']");
								if (this.model.get("variable") !== ""){
									nameControl = "form" + this.changeIdField(this.model.get("name"), i+1 , cell.model.get("columnName"));
									nameHiddeControl = nameControl.substring(0,nameControl.length-1).concat("_label]");
								}else{
									nameControl = "";
									nameHiddeControl = "";
								}
								idcontrol = "form" + this.changeIdField(this.model.get("id"), i+1 , this.columnsModel[j].id);
								hiddenControls.eq(0).attr({
									name : nameControl,
									id : idcontrol
								});
								hiddenControls.eq(1).attr({
									name : nameHiddeControl,
									id : idcontrol
								});
							break;
							default :
								control = $(cell.$el.find(".form-control"));
								hiddenControls = element.find("input[type='hidden']");
								if(this.model.get("variable") !== ""){
									nameControl = "form" + this.changeIdField(this.model.get("name"), i+1 , cell.model.get("columnName"));
									nameHiddeControl = nameControl.substring(0,nameControl.length-1).concat("_label]");
								}else{
									nameControl = "";
									nameHiddeControl = "";
								}
								idcontrol = "form" + this.changeIdField(this.model.get("id"), i+1 , this.columnsModel[j].id);
								control.attr({
									name : nameControl,
									id : idcontrol
								});
								hiddenControls.attr({
									name : nameHiddeControl,
									id : idcontrol
								});
							break;
						}
					}
				}
			}
			return this;
		},
		onClickPage: function (event) {
			var objData = $(event.currentTarget.children).data(),
			parentNode = $(event.currentTarget).parent();
			
			/************************** pagination rotate ************************************/
			var nextItem = $('<li class="toNext"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">...</a></li>');
			var prevItem = $('<li class="toPrev"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">...</a></li>');
			    
			if(!$.isNumeric($(event.currentTarget).find("a").text())) {
                var $currentItem = parentNode.find('li.active');
    			if($(event.currentTarget).hasClass("toNextItem")) {
    			    if($currentItem.hasClass("toNext")) {
        			    this.onClickNextSection($currentItem, parentNode, nextItem, prevItem);
        			} else {
        				if ( $currentItem.next().attr("class") !== "toNextItem") {
	        			    parentNode.find('li').removeClass('active');
	        			    $currentItem.next().addClass("active");
	        			    $currentItem.next().find("a:eq(0)").trigger("click");
        				}
        			}
    			}
    			if($(event.currentTarget).hasClass("toPrevItem")) {
    			    if($currentItem.hasClass("toPrev")) {
        			    this.onClickPrevSection($currentItem, parentNode, nextItem, prevItem);
        			} else {
        				if ( $currentItem.prev().attr("class") !== "toPrevItem" ) {
	        			    parentNode.find('li').removeClass('active');
	        			    $currentItem.prev().addClass("active");
	        			    $currentItem.prev().find("a:eq(0)").trigger("click");
        				}
        			}
    			}
    			if($(event.currentTarget).hasClass("toLast")) {
    			    var lastPosition = parseInt(parentNode.children().length) - 3;
    			    if(!this.model.get("paginationRotate")) {
    			        //var lastPosition2 = lastPosition - 1
    			        //var e = parentNode.find('li:eq('+ lastPosition2 +')');
    			        this.tableBody.find(".active").removeClass("active");
    			        var e = parentNode.find('li:eq('+ lastPosition +')');
    			        if (parentNode.find("li.active a").text() != Math.ceil(this.gridtable.length/this.pageSize)){
        			    	this.onClickNextSection(e, parentNode, nextItem, prevItem);
    			        }
						this.tableBody.children().last().addClass("active")
        			}
        			parentNode.find('.active').removeClass('active');
        			lastPosition = parseInt(parentNode.children().length) - 3;
    			    parentNode.find('li:eq('+ lastPosition +')').addClass("active");
    			    //this.tableBody.children().eq(Number(parentNode.find("li.active a").text())-1).removeClass("next left");
    			    //this.tableBody.children().removeClass("active");

    			    //event.stopPropagation();
    			    return false;
    			}
    			if($(event.currentTarget).hasClass("toFirst")) {
    			    if(!this.model.get("paginationRotate")) {
        			    var e = parentNode.find('li:eq(3)');
    			        if (parentNode.find("li.active a").text() !== "1"){
        			    	this.onClickPrevSection(e, parentNode, nextItem, prevItem);
    			        }
        			}
        			parentNode.find('li').removeClass('active');
    			    parentNode.find('li:eq(2)').addClass("active");
    			}
    			if ($(event.currentTarget).hasClass("toNext")){
    				var i, index = Number($(event.currentTarget).prev().text().trim());
    				var nextItemElement;
    				$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
					$(parentNode).find('.toPrev').remove();
            		$(parentNode).find('.toNext').remove();
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
						display : "none"
					});
    				if ( ((this.gridtable.length/this.pageSize) - index) > 5 ){
    					for (i = index ; i < index+5 ; i+=1) {
    						if  (!nextItemElement){
								nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
    						}
    						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
    							display : ""
    						}).addClass("showItem");
    					}
						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).before(nextItem);
    				}else{
    					for (i = index ; i < (this.gridtable.length/this.pageSize) ; i+=1) {
    						if  (!nextItemElement){
								nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
    						}
    						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
    							display : ""
    						}).addClass("showItem");
    					}
    				}
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index-1).after(prevItem);
					if (nextItemElement){
						nextItemElement.find("a").trigger("click");
					}
    			}
    			if ($(event.currentTarget).hasClass("toPrev")){
    				var i, index = Number($(event.currentTarget).prev().text().trim());
    				var nextItemElement;
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
					$(parentNode).find('.toPrev').remove();
            		$(parentNode).find('.toNext').remove();
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
						display : "none"
					});

    				if ( index - 5 != 0 ){
    					if (index -5 > -1){
	    					for (i = index-1 ; i >= index - 5 ; i-=1) {
	    						if  (!nextItemElement){
									nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
	    						}
	    						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
	    							display : ""
	    						}).addClass("showItem");
	    					}
							nextItemElement.after(nextItem);
							$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).after(prevItem);
							if (nextItemElement){
								nextItemElement.find("a").trigger("click");
							}
    					}else{

    						for (i = 5-1 ; i >= 0 ; i-=1) {
	    						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
	    							display : ""
	    						}).addClass("showItem");
	    					}
	    					if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index-1).length){
	    						nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index-1);
	    					}
							$(parentNode).find(".showItem").last().after(prevItem);
							if (nextItemElement){
								nextItemElement.find("a").trigger("click");
							}
    					}
    				}else{
    					for (i = index-1 ; i > -1 ; i-=1) {
    						if  (!nextItemElement){
								nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
    						}
    						$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
    							display : ""
    						}).addClass("showItem");
    					}
						nextItemElement.after(nextItem);
						if (nextItemElement){
							nextItemElement.find("a").trigger("click");
						}
    				}
    			}
			} else {
			    parentNode.children().removeClass('active');
			    $(event.currentTarget).addClass("active");
			}
			
			//this.onClickPageCallback(event, this);

			return this;
		},
		onClickNextSection: function (currentTarget, parentNode, nextItem, prevItem) {
			if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length-1 > 5){
				$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
			    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css("display", "none");
	            $(parentNode).find('.toPrev').remove();
	            $(parentNode).find('.toNext').remove();
	            var i, nextItemElement, length=$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length-1 ;
	            for (i = length ; i > length-5 ; i-=1){
					if  (!nextItemElement){
						nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
					}
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
						display : ""
					}).addClass("showItem");
	            }
	            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).after(prevItem);
				if (nextItemElement){
					nextItemElement.find("a").trigger("click");
				}
			}
		},
		onClickPrevSection: function (currentTarget, parentNode, nextItem, prevItem) {
			if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length-1 > 5){
				$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
			    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css("display", "none");
	            $(parentNode).find('.toPrev').remove();
	            $(parentNode).find('.toNext').remove();
	            var i, nextItemElement;
	            for (i = 0 ; i < 5 ; i+=1){
					if  (!nextItemElement){
						nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
					}
					$(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
						display : ""
					}).addClass("showItem");
	            }
	            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).before(nextItem);
				if (nextItemElement){
					nextItemElement.find("a").trigger("click");
				}
			}
		},

		refreshButtonsGrid: function () {
			var i,
			tdNumber,
			buttonRemove,
			trs = this.dom,
			element;

			for (i=0; i<trs.length; i+=1) {
				element = $(trs[i]).html();
				/*do {
					console.log("i-->"+i);
				}while(element.indexOf("form["+this.model.get("name")+"][") > -1);*/
				tdNumber = this.createRowNumber(i+1);
				buttonRemove = this.createRemoveButton(i);
				$(trs[i].firstChild).replaceWith( tdNumber );
				$(trs[i].lastChild).replaceWith( buttonRemove );
			}

			return this;
		},
		createRowNumber: function (index) {
			var tdNumber = document.createElement("div"),
			formgroup = document.createElement("div"),
			divNumber = document.createElement("div"),
			spanNumber = document.createElement("span"),
			label = document.createElement("label"),
			labelSpan = document.createElement("span"),
			containerField = document.createElement("div"),
			layout = this.model.get("layout"),
			tdRemove;
			if (layout === "form") {
				tdNumber.className = "col-xs-12 col-sm-1 col-md-1 col-lg-1"
			}
			if (layout === "static") {
				tdNumber.className = "pmdynaform-grid-field-static index";
				tdNumber.style.width = "33px";
			}			
			if (layout === "responsive") {
				tdNumber.width = this.indexResponsive;
				tdNumber.style.display = "inline-block";
			}
			label.className = "hidden-lg hidden-md hidden-sm visible-xs control-label col-xs-4";
			labelSpan.innerHTML = "Nro";
			label.appendChild(labelSpan);

			divNumber.className = "col-xs-4 col-sm-12 col-md-12 col-lg-12 pmdynaform-grid-label rowIndex";
			spanNumber.innerHTML = index;
			divNumber.appendChild(spanNumber);
			if (layout === "form") {
				containerField.appendChild(label);	
				
				tdRemove = this.createRemoveButton(index-1);
				tdRemove.className = "col-xs-1 visible-xs hidden-sm hidden-md hidden-lg remove-row-form";
				tdRemove.style.cssText = "float: right; margin-right: 15%";
				containerField.appendChild(tdRemove);
			}
			containerField.appendChild(divNumber);
			formgroup.className = "row form-group";
			formgroup.appendChild(containerField);
			tdNumber.appendChild(formgroup);
			$(tdNumber).addClass("index-row");
			return tdNumber;
		},
		createRemoveButton: function (index) {
			var that = this,
			tdRemove,
			buttonRemove,
			layout = this.model.get("layout");
			tdRemove = document.createElement("div");
			if (layout === "form" ) {
				tdRemove.className = "pmdynaform-grid-removerow hidden-xs col-xs-1 col-sm-1 col-md-1 col-lg-1";
			}
			if (layout === "static" ) {
				tdRemove.className = "pmdynaform-grid-removerow-static";
			}
			if (layout === "responsive" ) {
				tdRemove.className = "pmdynaform-grid-removerow-responsive";
				tdRemove.style.display = "inline-block";
			}			
			buttonRemove = document.createElement("button");
			
			buttonRemove.className = "glyphicon glyphicon-trash btn btn-danger btn-sm";
			buttonRemove.setAttribute("data-row", index);

			$(buttonRemove).data("row", index);
			$(buttonRemove).on("click", function(event) {
				that.onRemoveRow(event);
			});

			tdRemove.appendChild(buttonRemove);
			return tdRemove;
		},
		createHTMLTitle: function (	) {

			var k,
			dom,
			title,
			td,
			colSpan,
			label,
			layout = this.model.get("layout"),
			content,
			hint;
			this.domTitleHeader = [];

			dom = this.$el.find(".pmdynaform-grid-thead");
			td = document.createElement("div");
			content = document.createElement("div");
			label = document.createElement("span");
			
			if (layout === "static") {
				dom.addClass("pmdynaform-grid-thead-static");
				td.className = "pmdynaform-grid-field-static wildcard";
				td.style.minWidth = "33px";
			}
			if (layout === "form"){
				td.className = "col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center wildcard";
			}

			if (layout === "responsive") {
				//For the case: responsive and form
				td.className = "text-center wildcard";
				td.style.display = "inline-block";
				td.style.width = this.indexResponsive;		
			}
			//label.innerHTML = "Nro";
			td.appendChild(label);
			dom.append(td);
			for (k=0; k< this.columnsModel.length; k+=1) {
				if (this.columnsModel[k].type!=="hidden"){
					colSpan = this.columnsModel[k].colSpan;
					title = this.columnsModel[k].title;
					td = document.createElement("div");
					label = document.createElement("span");
					label.className = "title-column";
					this.checkColSpanResponsive();
					
					if (layout !== "responsive"){
						colSpan = this.colSpanControlField(this.columnsModel, this.columnsModel[k].type, k);
						td = this._createHtmlCell(this.columnsModel[k].type, colSpan, k);
					}
					label.innerHTML = title;
					label.style.fontWeight = "bold";
					label.style.maginLeft = "2px";
					$(label).css({
						"text-overflow": "ellipsis",
						"white-space": "nowrap",
						"overflow": "hidden",
						"display": "inline-block",
						"width": "80%",
						"text-align" : "center"
					});
					if (layout === "responsive") {
						$(label).css({
							width : "70%",
							display : "inline-block"
						});
						$(td).css({
							width : this.colSpanControlFieldResponsive(this.columnsModel)+"%",
							display : "inline-block"
						});
					}
					if (layout === "static") {
						if (this.columnsModel[k]["columnWidth"] && Number(this.columnsModel[k]["columnWidth"]).toString() !== "NaN"){
							$(td).css({
								"min-width" : parseInt(this.columnsModel[k]["columnWidth"])
							});
							$(label).css({
								"width" :  parseInt(this.columnsModel[k]["columnWidth"])-40
							});
						}else{
							$(td).css({
								"min-width" : "200px"
							});
							$(label).css({
								"width" : "160px"
							});
						}
					}
					if (layout === "responsive") {
						$(td).css({
							"width" : this.columnsModel[k].columnWidth
						});
						/*if (this.columnsModel[k]["columnWidth"] && Number(this.columnsModel[k]["columnWidth"]).toString() !== "NaN"){
							$(td).css({
								"min-width" : parseInt(this.columnsModel[k]["columnWidth"])
							});
						}*/
					}
					label.title = title;
					if(this.columnsModel[k].required){
						if(parseInt(this.columnsModel[k].columnWidth) === 0){
							label.appendChild($("<span class='pmdynaform-field-required'>*</span>")[0]);
							label.style.display = "none";					
						}else{
							label.appendChild($("<span class='pmdynaform-field-required'>*</span>")[0]);
						}
					}

					hint = document.createElement("span");
					
					td.appendChild(label);

					if(this.columnsModel[k].hint && this.columnsModel[k].hint.trim().length){
						hint = document.createElement("span");
						hint.className = "glyphicon glyphicon-info-sign";
						hint.setAttribute("data-toggle","tooltip");
						hint.setAttribute("data-container","body");
						hint.setAttribute("data-placement","bottom");
						hint.setAttribute("data-original-title",this.columnsModel[k]["hint"]);
						hint.style.float = "inherit";
						$(hint).tooltip().click(function(e) {
							$(this).tooltip('toggle');
						});
						if (this.model.get("columns").length < 6 && (layout== "responsive" || layout== "form")){
							td.appendChild(hint);
						}else{
							if( layout === "static" ) {
								td.appendChild(hint);
							}else{
								label.setAttribute("data-toggle","tooltip");
								label.setAttribute("data-container","body");
								label.setAttribute("data-placement","bottom");
								label.setAttribute("data-original-title",this.columnsModel[k]["hint"]);
							}
						}
					}
					dom.append(td);
					this.domTitleHeader.push($(td));
				}else{
					this.domTitleHeader.push($("<span></span>"));
				}
			}
			if (layout === "static") {
				var spaceDelete = document.createElement("div");
				spaceDelete.className = "pmdynaform-grid-removerow-static";
				$(spaceDelete).css({
					"min-width" : 38
				});
				dom.append(spaceDelete);
			}
			return this;
		},
		createHTMLPager: function (behavior) {
			var i,
			that = this,
			htmlPager,
			pagerContainer,
			activeIndex,
			pager,
			pagerItems,
			lastPager,
			elementList,
			ellipsis,
			newItem;
			pagerContainer = this.$el.find(".pmdynaform-grid-pagination");
			activeIndex = this.$el.find(".pagination").find("li.active");
			if (activeIndex.length){
				htmlPager = pagerContainer.children();
				pagerItems = htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').not('.toPrev').not('.toNext');
				if(behavior == "add"){
					if(Math.ceil(this.gridtable.length/this.pageSize)>pagerItems.length){
						elementList = jQuery("<li class = 'sec_"+
						Math.ceil(this.gridtable.length/5)+"'><a data-target='#"+this.model.get("id")+"-body' data-slide-to='"+
						(Math.ceil(this.gridtable.length/this.pageSize)-1) +"' href=''>"+Math.ceil(this.gridtable.length/this.pageSize)+"</a></li>");
						elementList.css({display:"none"});
						if(htmlPager.find(".toNext").length == 0 && (Number(elementList.text().trim())>5) ){
							ellipsis = jQuery('<li class="toNext"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">...</a></li>');
							htmlPager.find(".showItem").last().after(ellipsis);
							ellipsis.after(elementList);
						}else{
							if (htmlPager.find(".toNext").length){
								htmlPager.find(".toNextItem").before(elementList);
							}else{
								elementList.css({
									display : ""
								}).addClass("showItem");
								htmlPager.find(".showItem").last().after(elementList);								
							}
						}
					}
				}
				if (behavior == "remove"){
					var itemRemoved;
					if (Math.ceil(this.gridtable.length/this.pageSize) > 0 && Math.ceil(this.gridtable.length/this.pageSize)<pagerItems.length){
						if (pagerItems.eq(pagerItems.length-1).hasClass("active")){
							pagerItems.eq(pagerItems.length-1).prev().addClass("active");
						}
						itemRemoved = pagerItems.eq(pagerItems.length-1).remove();
						if (htmlPager.find(".active").hasClass("toPrev")){
							htmlPager.find(".active").trigger("click");
							htmlPager.find(".toNext").remove();
						}
						if(htmlPager.find(".active").text().trim() == 5){
							htmlPager.find(".toPrev").remove();
							htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
								display : ""
							});
						}
						if (Number(htmlPager.find(".showItem").last().text().trim()) <=(this.gridtable.length/5)){
							htmlPager.find(".toNext").remove();
						}
					}
				}
			}else{
				pager = this.templatePager({
					id: this.model.get("id")+"-body",
					paginationItems: this.model.get("paginationItems"),
					paginationRotate: this.model.get("paginationRotate"),
					itemsSections : Math.ceil(this.dom.length/this.pageSize)
				});
				htmlPager = $(pager);
				pagerItems = htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem');
				htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(0).addClass("active");
				if(Math.ceil(this.gridtable.length/5) > 5){
					pagerItems.eq(5-1).nextAll().not(':last').not('.toNextItem').css({display:"none"});
					pagerItems.eq(5).prevAll().not(".toFirst").addClass("showItem");
					pagerItems.eq(5).before('<li class="toNext"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">...</a></li>');
				}else{
					pagerItems.addClass("showItem");
				}
				htmlPager.children('li:first').after('<li class="toPrevItem"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">&lsaquo;</a></li>');
				htmlPager.children('li:last').before('<li class="toNextItem"><a data-target="#'+this.model.get("id")+'" data-rotate="'+this.model.get("paginationRotate")+'" href="javascript:void(0)">&rsaquo;</a></li>');
				pagerContainer.append(htmlPager);
			}

			return this;
		},
		createHTMLTotal: function () {
			var k,
			dom,
			title,
			td,
			operation,
			colSpan,
			label,
			result,
			icon,
			hint,
			containerStaticGrid,
			totalrow = this.model.get("totalrow"),
			layout = this.model.get("layout"),
			iconTotal = {
				sum: "&#8721;",
				avg: "&#935;",
				other: "&#989;"
			};
			if (totalrow.length) {
				dom = this.$el.find(".pmdynaform-grid-functions");
				dom.children().remove();
				td = document.createElement("div");
				label = document.createElement("span");
				
				if (layout === "static") {
					dom.addClass("pmdynaform-grid-thead-static");
					if (this.$el.find(".pmdynaform-grid-static").length){
						dom.css({
							width : this.totalWidtRow + 77
						});
					}
				} else {
					//For the case: responsive and form
					td.className = "col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center";
				}
				//label.innerHTML = "Nro";
				td.appendChild(label);
				dom.append(td);
				if (layout !== "form"){
					td.style.width = this.indexResponsive;
				}
				if(this.gridtable[0]){
					for (k=0; k< this.gridtable[0].length; k+=1) {
						colSpan = this.gridtable[0][k].model.get("colSpan");
						title = totalrow[k]? totalrow[k] : "";
						td = document.createElement("div");
						if (this.hiddenColumns.indexOf(k+1) > -1) {
							td.style.display = "none";
						}
						label = document.createElement("span");
						result = document.createElement("input");
						result.style.width = "50%";
						result.disabled = true;
						if (layout === "form"){
							this.checkColSpanResponsive();
							colSpan = this.colSpanControlField(this.gridtable[0], this.gridtable[0][k].model.get("type"), k);
							td.className = "col-xs-12 col-sm-"+colSpan+" col-md-"+colSpan+" col-lg-"+colSpan;
						}else{
							if (layout === "static"){
								if (this.gridtable[0][k].model.get("columnWidth") && Number(this.gridtable[0][k].model.get("columnWidth")).toString() !== "NaN"){
									$(td).css({
										"width" : parseInt(this.gridtable[0][k].model.get("columnWidth")),
										"display" : "inline-block"
									});
									this.gridtable[0][k].$el.css({
										"width" : parseInt(this.gridtable[0][k].model.get("columnWidth")),
										"display" : "inline-block"
									});
								}else{
									$(td).css({
										"min-width" : "200px"
									});
									this.gridtable[0][k].$el.css({
										"width" : "200px"
									});
								}
								td.className = "pmdynaform-grid-field-static field-operation-result";
							}else{
								if (parseInt(this.gridtable[0][k].model.get("columnWidth"))!==0){
									$(td).css({
										"width" : this.gridtable[0][k].model.get("columnWidth"),
										display : "inline-block"
									});
								}else{
									$(td).css({
										"width" : this.gridtable[0][k].model.get("columnWidth"),
										display : "none"
									});
								}
							}
						}
						operation = this.gridtable[0][k].model.attributes.operation;
						if (operation) {
							$(td).addClass("total");
							icon =  iconTotal[operation] ? iconTotal[operation] : iconTotal["other"];
							label.innerHTML = icon + ": ";
							result.value =  title;
							result.id = (operation +"-"+ this.model.get("name")+"-"+
										this.gridtable[0][k].model.get("columnName"));
							$(td).addClass("function-result-" + this.gridtable[0][k].model.get("columnName"));
							td.appendChild(label);
							td.appendChild(result);
						} else {
							label.innerHTML	= "";
							result.value =  "";
						}
						dom.append(td);
					}
					if (this.model.get("layout") == "static"){
						this.tableBody.on("scroll",function(e){
							$(".containerStaticGrid")[0].scrollLeft = e.target.scrollLeft;
						});
					}
				}
			}
			return this;
		},
		createHTMLRow: function (numberRow,dataRow, sectionAfected) {
			var tr, td, k, tdRemove, tdNumber, element, colSpan, product, cellModel, nameCell,
			idCell, cellView, row, that, rowModel, rowView, rowData;
			rowModel = [];
			rowView = [];
			rowData = [];
			tr = this._createHtmlRow(), that = this;
			this.sqlColumns = [];
			row = [];
			if (sectionAfected){
				this.flagRow = sectionAfected;
			}
			tdNumber = this.createRowNumber(numberRow+1);
			tr.appendChild(tdNumber);
			for ( k = 0; k < this.columnsModel.length ; k+=1 ) {
				cellModel = null;
				product = this.columnsModel[k].product;
				if ( dataRow && dataRow.length ) {
					var aux = this.columnsModel[k]["data"];
					this.columnsModel[k]["data"] = dataRow[k];
					cellModel = new product.model(this.columnsModel[k]);
					this.columnsModel[k]["data"] = aux;
				}else{
					cellModel = new product.model(this.columnsModel[k]);					
				}
				if ( cellModel.toJSON().originalType &&
					 cellModel.toJSON().originalType !== "label" ) {
					cellModel.set("fullOptions",this.transformJSON(cellModel.toJSON(), cellModel.get("originalType")));
				}
				cellModel.set("row",numberRow);
				cellModel.set("col",k);
				if (cellModel.get("sql") !== undefined && this.validDependentColumns.indexOf(cellModel.attributes.type) >-1) {
					cellModel.attributes.parentDependents = [];
					cellModel.attributes.dependents = [];
					this.sqlColumns.push(cellModel);
				}
				if (this.model.get("variable").trim().length === 0 ){
					nameCell = "";
					idCell = this.changeIdField(this.model.get("id"), numberRow+1, cellModel.get("_extended").name);
				}else{
					nameCell = this.changeNameField(this.model.get("name"), numberRow+1, cellModel.get("_extended").name);
					idCell = this.changeIdField(this.model.get("id"), numberRow+1, cellModel.get("_extended").name);
				}
				cellModel.attributes.name = nameCell;
				cellModel.attributes.id = idCell;
				rowModel.push(cellModel);
			}
			var i, idColumn, where, sql, indexWhere, j;
			for (var i = 0 ; i < this.sqlColumns.length ; i+=1) {
				this.sqlColumns[i].set("dependents", []);
				idColumn = this.sqlColumns[i].get("columnName");
				for ( j = 0  ; j < this.sqlColumns.length ; j+=1 ) {
					if(i!==j){
						indexWhere = this.sqlColumns[j].get("sql").toLowerCase().indexOf("where");
						if (indexWhere !== -1) {
							sql = this.sqlColumns[j].get("sql");
							sql = sql.replace(/\n/g, " ");
							where = sql.substring(indexWhere, sql.length);
							where = where.split(" ");
							if (this._existVariableInSql(where, idColumn)){
								this.sqlColumns[j].attributes.parentDependents.push(this.sqlColumns[i]);
								this.sqlColumns[i].attributes.dependents.push(this.sqlColumns[j]);
							}
						}
					}
				}
			}
			for (var i = 0 ; i < rowModel.length ; i+=1) {
				product = this.columnsModel[i].product;
				cellView = null;
				cellView = new product.view({
					model: rowModel[i]
				});
				rowModel[i].set("view", cellView);
				cellView.project = this.project;
				cellView.parent = this;
				colSpan = rowModel[i].attributes.colSpan;
				element = cellView.render().el;
				if (this.model.get("layout") === "responsive"){					
					var elementParent = $(element).find(".form-control")[0].parentNode;					
					elementParent.style.padding = "0px";
					td = document.createElement("div");
					td.style.display = "inline-block";
					$(td).css("float","left");
				}else{
					td = this._createHtmlCell(rowModel[i].attributes.type, colSpan, i);
				}
				if (this.hiddenColumns.indexOf(i+1) > -1){
					$(td).hide();
				}
				if ( cellView.model.get("type") !== "hidden" && this.model.get("layout") === "static") {
					if (cellView.model.get("columnWidth") && Number(cellView.model.get("columnWidth")).toString() !== "NaN"){
						$(td).css({
							"min-width" : parseInt(cellView.model.get("columnWidth")),
							"max-width" : parseInt(cellView.model.get("columnWidth"))							
						});
						cellView.$el.css({
							"width" : parseInt(cellView.model.get("columnWidth"))
						});
					}else{
						$(td).css({
							"min-width" : "200px",
							"max-width" : "200px"
						});				
					}
				}

				if (this.model.get("layout") === "responsive") {
					if (parseInt(cellView.model.get("columnWidth"))!==0){
						$(td).css({
							"width" : cellView.model.get("columnWidth")
						});
					}else{
						$(td).css({
							"width" : cellView.model.get("columnWidth"),
							display : "none"
						});
					}
				}

				$(element).addClass("row form-group");
				td.appendChild(element);
				tr.appendChild(td);
				row.push(cellView);
				if (cellView.model.get("operation")) {
					cellView.on("changeValues", function(){
						that.setValuesGridFunctions({
							row: this.model.attributes.row,
							col: this.model.attributes.col,
							data: this.model.attributes.value
						});
						that.createHTMLTotal();
					});
				}
				rowView.push(cellView);
				if (rowModel[i].get("operation")){
					if (!isNaN(parseFloat(rowModel[i].get("value")))){
						rowData.push(parseFloat(rowModel[i].get("value")));
					} else {
						rowData.push(0);	
					}
				}
			}
			this.updateNameFields(row);
			for (var k = 0; k < row.length ;  k+=1) {
				if (row[k].model.get("formula")) {
					row[k].model.attributes.formulaAssociatedObject = [];					
					row[k].onFormula(row);
					//row[k].onFieldAssociatedHandler();
				}
			}
			if (this.model.get("mode") === "edit") {
				if(this.model.get("deleteRow")){
					tdRemove = this.createRemoveButton(numberRow+1);
					$(tdRemove).addClass("remove-row");
					tr.appendChild(tdRemove);
				}
			}
			if ( this.model.get("layout") === "responsive") {
				jQuery(tdNumber).css({width:this.indexResponsive});
				jQuery(tdRemove).css({width:this.removeResponsive});
			}
			this.flagRow+=1;
			if (this.paged) {
				this._createHTLMCarucel();
				this.domCarousel.append(tr);
				this.tableBody.append(this.domCarousel);
			} else {
				this.tableBody.append(tr);	
			}
			this.gridtable.push(row);
			this.dom.push(tr);
			return {
				model: rowModel,
				view: rowView,
				data: rowData
			};
		},
		__createDependencyColumns : function () {
			var i, j, idColumn, where, indexWhere, sql;
			for ( i = 0 ; i < this.sqlColumns.length ; i+=1) {
				this.sqlColumns[i].set("dependents", []);
				idColumn = this.sqlColumns[i].get("columnName");
				for ( j = 0  ; j < this.sqlColumns.length ; j+=1 ) {
					if(i!==j){
						indexWhere = this.sqlColumns[j].get("sql").toLowerCase().indexOf("where");
						if (indexWhere !== -1) {
							sql = this.sqlColumns[j].get("sql");
							where = sql.substring(indexWhere, sql.length);
							where = where.split(" ");
							if (this._existVariableInSql(where, idColumn)){
								this.sqlColumns[j].attributes.parentDependents.push(this.sqlColumns[i]);
								this.sqlColumns[i].attributes.dependents.push(this.sqlColumns[j]);
							}
						}
					}
				}
			}
			return this;
		},
		_existVariableInSql : function (where, nameField) {
			if (where.indexOf("@#"+nameField)>-1 ||
			where.indexOf("@%"+nameField)>-1 ||
			where.indexOf("@@"+nameField)>-1 ||
			where.indexOf("@?"+nameField)>-1 ||
			where.indexOf("@$"+nameField)>-1 ||
			where.indexOf("@="+nameField)>-1 ||
			where.indexOf('\"'+"@#"+nameField+'\"')>-1 ||
			where.indexOf('\"'+"@%"+nameField+'\"')>-1 ||
			where.indexOf('\"'+"@@"+nameField+'\"')>-1 ||
			where.indexOf('\"'+"@?"+nameField+'\"')>-1 ||
			where.indexOf('\"'+"@$"+nameField+'\"')>-1 ||
			where.indexOf('\"'+"@="+nameField+'\"')>-1 ||
			where.indexOf('\''+"@#"+nameField+'\'')>-1 ||
			where.indexOf('\''+"@%"+nameField+'\'')>-1 ||
			where.indexOf('\''+"@@"+nameField+'\'')>-1 ||
			where.indexOf('\''+"@?"+nameField+'\'')>-1 ||
			where.indexOf('\''+"@$"+nameField+'\'')>-1 ||
			where.indexOf('\''+"@="+nameField+'\'')>-1){
				return true;
			}else{
				return false;
			}
        },
		_createHTLMCarucel : function () {
			if (this.block === true) {
				this.domCarousel = document.createElement("div");
				this.domCarousel.className = "pmdynaform-grid-section_"+this.section;
				if (this.model.get("layout") === "static") {
					this.domCarousel.className += " pmdynaform-static";
				}
				this.domCarousel = $(this.domCarousel);
				//this.showPage+=1;	
			}
			if (this.section === this.showPage) {
				this.domCarousel.addClass("item active");
			} else {
				this.domCarousel.addClass("item");
			}
			if (this.flagRow == this.pageSize) {
				this.block = true;
				this.section+=1;
				this.flagRow = 0;
			} else {
				this.block = false;
			}
			return this;
		},
		_createHtmlRow : function () {
			var tr;
			tr = document.createElement("div");
			tr.className = "pmdynaform-grid-row row form-group show-grid";
			if (this.model.get("layout") === "static") {
				tr.className += " pmdynaform-grid-static"
			}
			return tr;
		},
		_createHtmlCell : function (typeControl, colSpan, index) {
			var td, colSpan;
			td = document.createElement("div");
			if (this.model.attributes.layout  === "form") {
				if (typeControl !=="hidden") {
					this.checkColSpanResponsive();
					colSpan = this.colSpanControlField(typeControl,index);
					td.className = "col-xs-12 col-sm-"+colSpan+" col-md-"+colSpan+" col-lg-"+colSpan;
				} else {
					jQuery(td).css({
						width : 0+"%",
						display : "inline-block" 
					});
				}
			} else if(this.model.attributes.layout  === "static") {
				if ( typeControl !=="hidden" ) {
					td.className = "pmdynaform-grid-field-static";
				}
			} else {
				if ( typeControl  !== "hidden") {
					td.className = "col-xs-"+colSpan+" col-sm-"+colSpan+" col-md-"+colSpan+" col-lg-"+colSpan;
					jQuery(td).css({
						width : this.colSpanControlFieldResponsive()+"%",
						display : "inline-block" 
					});
				}else{
					jQuery(td).css({
						width : 0+"%",
						display : "inline-block" 
					});
				}
			}
			return td;
		},
		setData: function (data) {
            var col,
            i,
            j,
            cloneData = data,
            grid = this.gridtable;

            if (typeof data === "object") {
                if(cloneData.length){
	                for (j in cloneData) {
	                    if(cloneData.hasOwnProperty(j)) {
	                    	for (col=0; col<grid[0].length; col+=1) {
	                    		if (!_.isEmpty(grid[0][col].model.attributes.variable)) {
	                    			if (grid[0][col].model.attributes.variable.var_name === j) {
		                    			if (cloneData[j] instanceof Array) {
		                    				for (i=0; i<grid.length;i+=1) {

		                    					if (!this.gridtable[i][col].model.get("formulator")){
		                    						grid[i][col].model.set("value", cloneData[j][i]);
		                    						if (this.gridtable[i][col].onFieldAssociatedHandler){
		                    							this.gridtable[i][col].onFieldAssociatedHandler()
		                    						}
		                    					}
		                    				}
		                    			}	
		                    		}	
	                    		}	                    		
	                    	}
	                    }
	                }
                }                
            } else {
                //console.log("Error, The 'data' parameter is not valid. Must be an array.");
            }
            return this;
        },
		getData: function () {
			//console.log("getdata grid");
			var i, 
			k, 
			gridpanel,
			fields,
			rowData = [],
			gridData = [],
			gridFieldData = {
				name: this.model.get("name"),
				gridtable: []
			},
			data = this.model.getData();

			gridpanel = this.gridtable;
			for (i=0; i<gridpanel.length; i+=1) {
				rowData = [];
				for (k=0; k<gridpanel[i].length; k+=1) {
					if ((typeof gridpanel[i][k].getData === "function") && 
                        (gridpanel[i][k] instanceof PMDynaform.view.Field)) {
						rowData.push(gridpanel[i][k].getData());
					}
				}
				gridData.push(rowData);
			}
			gridFieldData.gridtable = gridData;

            return gridFieldData;
		},
		renderGridTable: function (newItem) {
			var i, j, k, rows, rowsData,rowData, row;
			this.tableBody = this.$el.find(".pmdynaform-grid-tbody");
			rows = this.model.get("rows");
			rowsData = this.model.get("data");
			if ( this.model.get("layout") === "static" ) {
				this.tableBody.addClass("pmdynaform-static");
			}
			this.model.attributes.gridFunctions = [];
			if ( !newItem ) {
				this.dom = [];
				for(j = 0; j < rows; j+=1){
					rowData = rowsData[j+1];
					row = this.createHTMLRow(j, rowData);
					if (row && row.data){
		            	this.model.attributes.gridFunctions.push(row.data);
			            for (i = 0 ; i < row.model.length ; i+=1){
			            	if (row.model[i].get("operation") && row.model[i].get("operation").trim().length){
								row.view[i].onChangeCallbackOperation();
			            	}
			            	if (row.model[i].get("type") == "label" && row.model[i].get("operation")){
			            		this.createHTMLTotal();
			            	}
			            }
					}
	            }
			} else {
				row = this.createHTMLRow(this.gridtable.length-1);
				if (row && row.data){
	            	this.model.attributes.gridFunctions.push(row.data);
				}
			}
			this.model.setPaginationItems();
			this.createHTMLPager();
			return this;
		},
		/**
		 * @Event
		 * @param Event  This must be an event valid
		 * @param Function Callback for the event
		 **/
		on: function (e, fn) {
			var allowEvents = {
				remove: "setOnDeleteRowCallback",
				add: "setOnAddRowCallback",
				pager: "setOnClickPageCallback",
				beforeAdd : "setOnBeforeAddCallback"
			};

			if (allowEvents[e]) {
				this[allowEvents[e]](fn);
			} else {
				throw new Error ("The event must be a valid event.\n The events available are remove, add and pager");
			}

			return this; 
		},
		setOnDeleteRowCallback: function (fn){
			if (typeof fn === "function") {
				this.onDeleteRowCallback = fn;
			} else {
				throw new Error ("The callback must be a function");
			}
			
			return this;
		},
		setOnAddRowCallback: function(fn){
			if (typeof fn === "function") {
				this.onAddRowCallback = fn;
			} else {
				throw new Error ("The callback must be a function");
			}
			
			return this;
		},
		setOnBeforeAddCallback : function (fn) {
			if (typeof fn === "function") {
				this.onBeforeAddRowCallback = fn;
			} else {
				throw new Error ("The callback must be a function");
			}
			return this;	
		},
		setOnClickPageCallback: function(fn){
			if (typeof fn === "function") {
				this.onClickPageCallback = fn;
			} else {
				throw new Error ("The callback must be a function");
			}
			
			return this;
		},
		afterRender: function () {
			
		},
		getData2: function () {
			var data, gridpanel, i, k, rowData, dataCell,key;
			data = {};

			gridpanel = this.gridtable;
			for (i=0; i<gridpanel.length; i+=1) {
				data[i+1] = {};
				rowData = {};
				for (k=0; k<gridpanel[i].length; k+=1) {
					if ((typeof gridpanel[i][k].getData === "function") && 
                        (gridpanel[i][k] instanceof PMDynaform.view.Field)) {
						dataCell = gridpanel[i][k].model.getData();
						rowData[dataCell.name] = dataCell.value; 
					}
				}
				data[i+1] = rowData;
			}
            return data;
		},
		setData2: function (data) {
           	var rowIndex, grid, dataRow, 
           		colIndexm, cols, colIndex,
           		cellModelItem, cellViewItem,
           		modeItem, dataItem, newItem, value, richi, option, options, i;
           	grid = this.gridtable;
           	for ( rowIndex in data) {
           		if ( parseInt(rowIndex,10) > this.gridtable.length ){
					newItem = this.addRow();
					this.renderGridTable();
					this.onAddRowCallback(newItem, this);
           		}
       			cols = grid[parseInt(rowIndex,10)-1].length;
       			for ( colIndex = 0 ; colIndex < cols ; colIndex +=1 ) {
       				cellViewItem = grid[parseInt(rowIndex,10)-1][colIndex];
       				cellModelItem = grid[parseInt(rowIndex,10)-1][colIndex].model;
       				modeItem = cellModelItem.get("mode");
   					for ( dataItem in data[rowIndex] ) {
   						if (cellModelItem.get("columnName") === dataItem) {
							if ( modeItem === "edit" || modeItem === "disabled" ) {
	       						if (cellModelItem.get("type") === "suggest"){

	                           		for ( richi = 0 ; richi < cellModelItem.get("localOptions").length ; richi +=1 ) {
		                                option  = cellModelItem.get("localOptions")[richi].value;
		                                if (option === data[rowIndex][dataItem]){
		                                    value = cellModelItem.get("localOptions")[richi].label;
		                                    break;
		                                }
		                            }
		                            if (value && !value.length){
		                                for ( richi = 0 ; richi < cellModelItem.get("options").length ; richi +=1 ) {
		                                    option  = cellModelItem.get("options")[richi].value;
		                                    if (option === data[rowIndex][dataItem]){
		                                        value = cellModelItem.get("options")[richi].label;
		                                        break;
		                                    }
		                                }
		                            }

	                                $(cellViewItem.el).find(":input").val(value);
	                                cellModelItem.attributes.value = data[rowIndex][dataItem];
	           					}else if (cellModelItem.get("type") === "checkbox"){
	           						options = cellModelItem.get("options");
	           						if (cellModelItem.get("dataType") === "boolean") {
		                                if ( data[cellModelItem.get("name")] === options[0].value ){
		                                    options[1].selected = false;
		                                    options[0].selected = true;
		                                } else {
		                                    delete options[0].selected;
		                                    options[1].selected = true;
		                                    options[0].selected = false;
		                                }	           							
	           						} else {
	           							for ( i = 0 ; i < options.length ; i +=1 ) {
	           								delete options[i].selected;
	           								if (data[rowIndex][dataItem].indexOf(options[i]) > -1 ) {
	           									options[i].selected = true;
	           								}
	           							}
	           						}
	           						cellModelItem.set("options",options);
	           						cellModelItem.initControl();
	           						cellModelItem.set("value", [data[rowIndex][dataItem]])
	           					} else {
									cellModelItem.set("value", data[rowIndex][dataItem]);
	           					}
							}
							if (modeItem === "view") {
								if (cellModelItem.get("originalType") === "checkbox"){
                            		cellModelItem.set("fullOptions", data[rowIndex][dataItem]);
                        		} else if (cellModelItem.get("originalType") === "dropdown" /*||
                                	cellModelItem.get("originalType") === "suggest"*/) {
                        			value = [];
		                            for ( richi = 0 ; richi < cellModelItem.get("localOptions").length ; richi +=1 ) {
		                                option  = cellModelItem.get("localOptions")[richi].value;
		                                if (option === data[rowIndex][dataItem]){
		                                    value.push(cellModelItem.get("localOptions")[richi].label);
		                                    cellModelItem.set("fullOptions", value);
		                                    break;
		                                }
		                            }
		                            if (!value.length){
		                                for ( richi = 0 ; richi < cellModelItem.get("options").length ; richi +=1 ) {
		                                    option  = cellModelItem.get("options")[richi].value;
		                                    if (option === data[rowIndex][dataItem]){
		                                        value.push(cellModelItem.get("options")[richi].label);
		                                        cellModelItem.set("fullOptions", value);
		                                        break;
		                                    }
		                                }
		                            }
                        		} else {
									value = [];
		                            value.push(data[rowIndex][dataItem]);
		                            cellModelItem.set("fullOptions", value);
								}
							}
       					}
					}
       			}
           	}
            return this;
        },
		render: function() {
                    if(PMDynaform.core.ProjectMobile){
                        var gridMobile = new PMDynaform.model.GridMobile(this.model.attributes, null, this.project);
                        this.$el.html(gridMobile.body);
                        this.setData2 = function(data){
                            gridMobile.setData(data);
                        };
                        this.getData2 = function(){
                            return gridMobile.getData();
                        };
                        return this;
                    }
			var j,
			headerGrid,
			bodyGrid;

			this.$el.html( this.template( this.model.toJSON()) );
			this.createHTMLTitle();
			this.renderGridTable(false);
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }

            if (this.model.get("layout") === "static") {
            	headerGrid = this.$el.find(".pmdynaform-grid-thead");
				bodyGrid = this.$el.find(".pmdynaform-grid-tbody");
				bodyGrid.css("overflow","auto");
				bodyGrid.scroll(function (event) { 
			        headerGrid.scrollLeft(bodyGrid.scrollLeft());
			        event.stopPropagation();
			    });
            }
            if(!this.model.get("addRow")) {
            	this.$el.find(".pmdynaform-grid-new").find("button").hide();
            }
            if (this.model.get("layout") === "responsive") {
            	var size = {
            		"1200": 5,
            		"992": 4,
            		"768": 3,
            		"767": 2
            	};
            
	            $( window ).resize(function() {
	            	var j, 
	            	k,
	            	width = $( window ).width();

	            	if ( width >= 1200) {
	            		//console.log("1200");
	            	}
	            	if (width >= 992 && width < 1200) {
	            		//console.log("992");
	            	}
	            	if (width >= 768 && width < 992) {
	            		//console.log(">768");
	            	}
	            	if (width < 768) {
	            		//console.log("<768");
	            	}

				});
			}
			
			return this;
		},
		transformJSON : function (field, type){
			var fullOptions = [], validOpt = [], i, aux;
			if(type == "text"){
			    return [field.defaultValue || field.value];
			}
			if(type == "textarea"){
				return [field.defaultValue || field.value];
			};
			if(type == "checkbox"){
				for (i=0; i<field.options.length; i+=1) {
					if (field.options[i].selected) {
						if (field.options[i].selected === true) {
							validOpt.push(field.options[i].label);
						}
					}
				}
				return validOpt;
			};
			if(type == "radio"){
				for (i=0; i<field.options.length; i+=1) {
					if (field.defaultValue) {
						if (field.options[i].value.toString() === field.defaultValue.toString()) {
							validOpt.push(field.options[i].label);
						}
					}
				}
				return validOpt;
			};
			if(type == "dropdown"){
				if (field.options.length){
					for (i=0; i<field.options.length; i+=1) {
						if (field.value) {
							if (field.options[i].label.toString() === field.value.toString()) {
								validOpt.push(field.options[i].label);
							}
						}
					}
				}else{

					validOpt = field.value? [field.value] : [];	
				}
				return validOpt;
			};
			if(type == "datetime"){
				return [field.defaultValue || field.value ];
			};
			if(type == "suggest"){
				if (field.options.length){
					for (i=0; i<field.options.length; i+=1) {
						if (field.value) {
							if (field.options[i].label.toString() === field.value.toString()) {
								validOpt.push(field.options[i].label);
							}
						}
					}
				}else{

					validOpt = field.value? [field.value] : [];	
				}
				return validOpt;
			};
			if(type === "link" ){
				return [field.value]
			};
			return null;
		}
	});
	PMDynaform.extendNamespace("PMDynaform.view.GridPanel",GridView);
}());

(function () {
    var GridMobile = function (property, renderTo, parent) {
        this.property = property;
        this.renderTo = renderTo;
        this.parent = parent;
        this.topClient = 0;
        this.pressTimer;
        GridMobile.prototype.init.call(this);
    };
    GridMobile.prototype.init = function () {
        var that = this;
        this.title = $("<span class='pm-form-grid-header-title'><span>");
        this.add = $("<img class='pm-form-grid-header-add' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wYRFA4ZqZ5P4gAAAP5JREFUOMul07EuRFEQBuCTuwmhU0sEES0dm3gC3kHiPbbcYmsq3YqodYJew2qIqNSylBJL4vo0c5N1c++y/MnJyfn/OXNmzsykVAJWsIs7POMJN+hgKdUBU+jiUz3e0UajfHkaF2H0ij2sYiZWM5znYXP8zQmOQnjA8ogoN9AP205BNiPsl3KO6OGsxK3jA29YTDgIj62KF0EFfxhSO+ExDnNjONgM6SpFOIbEyxFVOAmb2Tj3s4q/aqR6FFoeezacwvwYKWyF1MtSSufB76TfYzv204S1EWW8riljjgEWCnL/D43UKs9BVStPYPLHVv73MNWM83206wC3deP8BQS+B+NSJVuhAAAAAElFTkSuQmCC'>");
        this.add.on("click", function () {
            that.showRowEditor();
        });

        this.header = $("<div class='pm-form-grid-header'></div>");
        this.header.append(this.title);
        this.header.append(this.add);

        var a;
        this.carousel = $("<div class='pm-form-grid-body carousel slide' data-ride='carousel' data-interval=''><div class='carousel-inner' role='listbox'></div></div>");
        this.carousel.carousel();
        this.carousel.on("mousedown touchstart", function (e) {
            a = e.clientX || e.originalEvent.touches[0].clientX;
        });
        this.carousel.on("mouseup touchmove", function (e) {
            var sel = that.pagination.find(".active");
            var b = e.clientX || e.originalEvent.touches[0].clientX;
            if (b > a) {
                sel.removeClass("active");
                that.carousel.carousel("prev");
                if (sel.prev().length === 0)
                    that.pagination.find("li").last().addClass("active");
                else
                    sel.prev().addClass("active");
                that.setPageInfo(that.pagination.find("li").index(that.pagination.find(".active")));
            }
            if (b < a) {
                sel.removeClass("active");
                that.carousel.carousel("next");
                if (sel.next().length === 0)
                    that.pagination.find("li").first().addClass("active");
                else
                    sel.next().addClass("active");
                that.setPageInfo(that.pagination.find("li").index(that.pagination.find(".active")));
            }
        });

        this.plabel = $("<span class='pm-form-grid-footer-plabel'></span>");
        this.pagination = $("<ul class='pm-form-grid-footer-pagination carousel-indicators'></ul>");

        this.footer = $("<div class='pm-form-grid-footer'></div>");
        this.footer.append(this.plabel);
        this.footer.append(this.pagination);

        this.body = $("<div class='pm-form-grid'></div>");
        this.body.append(this.header);
        this.body.append(this.carousel);
        this.body.append(this.footer);

        //set property
        //property.addRow
        //property.colSpan
        //property.deleteRow
        //property.hint
        //property.id
        //property.label
        //property.layout
        //property.mode
        //property.name
        //property.pageSize
        this.pageSize = parseInt(this.property.pageSize);
        //property.title
        this.title.text(this.property.title);
        //property.type
        //property.variable
        //property.columns
        //property.data
        that.setData({});
    };
    GridMobile.prototype.addHeader = function () {
        var row = $("<div class='pm-form-grid-row-header row'></div>");
        return row;
    };
    GridMobile.prototype.addRow = function () {
        return $("<div class='pm-form-grid-row row'></div>");
    };
    GridMobile.prototype.delRow = function (index) {
        var that = this, i, dt, j = 1, dt2 = {};
        dt = that.getData();
        for (i in dt) {
            if (i !== index) {
                dt2[j] = dt[i];
                j++;
            }
        }
        that.setData(dt2);
    };
    GridMobile.prototype.addCell = function () {
        return $("<div class='pm-form-grid-row-cell'></div>");
    };
    GridMobile.prototype.setData = function (dt) {
        var that = this, i, j, table, columns, index, row, cell;
        var sw = parseInt(that.pageSize) === 0;
        var pn = 0;
        that.carousel.find(".carousel-inner").empty();
        that.pagination.empty();
        i = 0;
        for (index in dt) {
            if (i % that.pageSize === 0 || sw) {
                sw = false;
                table = that.buildHeader(i, pn);
                pn = pn + 1;
            }
            //setdata
            row = that.addRow();
            row.data("index", index);

            //long press
            row.mousedown(function () {
                var index = $(this).data("index");
                that.pressTimer = setTimeout(function () {
                    that.showOptions(index);
                    that.pressTimer = true;
                }, 1000);
                return false;
            }).mouseup(function () {
                if (that.pressTimer !== true) {
                    clearTimeout(that.pressTimer);
                    that.showRowEditor($(this).data("index"));
                }
                return false;
            });

            //long press mobile
            row.bind("touchstart", function (e) {
                var index = $(this).data("index");
                that.pressTimer = setTimeout(function () {
                    that.showOptions(index);
                    that.pressTimer = true;
                }, 1000);
                return false;
            }).bind("touchend", function (e) {
                if (that.pressTimer !== true) {
                    clearTimeout(that.pressTimer);
                    that.showRowEditor($(this).data("index"));
                }
                return false;
            });
            table.append(row);
            columns = that.property.columns;
            for (j = 0; j < columns.length; j++) {
                cell = that.addCell();
                if (dt[index][columns[j].id + "_label"] === undefined){
                    if (columns[j].options){
                        for (var k=0 ; k < columns[j].options.length ; k+=1){
                            if (columns[j].options[k].value === dt[index][columns[j].id]){
                                dt[index][columns[j].id + "_label"] = columns[j].options[k].label;
                                break;                            
                            }
                        }
                    }
                    if (dt[index][columns[j].id + "_label"] === undefined){
                        dt[index][columns[j].id + "_label"] = dt[index][columns[j].id];
                    }
                }
                cell.text(dt[index][columns[j].id + "_label"]);
                cell.data("index", columns[j].id);
                cell.data(columns[j].id, dt[index][columns[j].id]);
                cell.data(columns[j].id + "_label", dt[index][columns[j].id + "_label"]);
                row.append(cell);
            }
            i++;
        }
        that.size = i;
        if (i === 0) {
            table = that.buildHeader(i, pn);
            table.append("<div style='text-align:center;padding:5px;'>No rows</div>");
        }
        that.setPageInfo(0);
    };
    GridMobile.prototype.buildHeader = function (i, pn) {
        var that = this, row, cell, itemCarousel, itemPagination, columns, table, j;
        row = that.addHeader();
        columns = that.property.columns;
        for (j = 0; j < columns.length; j++) {
            cell = that.addCell();
            cell.text(columns[j].title);
            cell.removeClass("pm-form-grid-row-cell");
            cell.addClass("pm-form-grid-row-header-cell");
            row.append(cell);
            if (j === 0 || j === 1) {
                cell.css({"width": "50%"});
            }
        }
        table = $("<div class='pm-form-grid-body form-group'></div>");
        table.append(row);
        itemCarousel = $("<div class='item'>");
        itemCarousel.append(table);
        that.carousel.find(".carousel-inner").append(itemCarousel);
        //default
        if (i === 0)
            itemCarousel.addClass("active");

        //page
        itemPagination = $("<li class='item" + pn + "'></li>");
        itemPagination[0].pn = pn;
        itemPagination.on("click", function () {
            that.pagination.find(".active").removeClass("active");
            $(this).addClass("active");
            that.carousel.carousel(this.pn);
            that.setPageInfo(this.pn);
        });
        if (this.pageSize > 0) {
            that.pagination.append(itemPagination);
        }
        //default
        if (i === 0)
            itemPagination.addClass("active");
        return table;
    };
    GridMobile.prototype.getData = function () {
        var that = this, dt = {};
        var j = 1;
        that.carousel.find(".carousel-inner").find(".pm-form-grid-body").each(function (i, e) {
            $(e).find(".pm-form-grid-row").each(function (i, e) {
                dt[j] = {};
                $(e).find(".pm-form-grid-row-cell").each(function (i, e) {
                    dt[j][$(e).data("index")] = $(e).data($(e).data("index"));
                    dt[j][$(e).data("index") + "_label"] = $(e).data($(e).data("index") + "_label");
                });
                j++;
            });
        });
        return dt;
    };
    GridMobile.prototype.setPageInfo = function (start) {
        var that = this;
        var a = start * this.pageSize + 1;
        var b = start * this.pageSize + this.pageSize;
        if (this.size === 0) {
            a = b = 0;
        }
        var msg = "";
        if (this.pageSize > 0) {
            msg = a + "-" + b + " of ";
        }
        that.plabel.text("Showing " + msg + this.size);
    };
    GridMobile.prototype.showRowEditor = function (index) {
        this.modalShow();
        var that = this, keys;
        var i, items = [], field;
        for (i = 0; i < that.property.columns.length; i++) {
            field = that.property.columns[i];
            field.variable = field.id;//todo
            field.var_name = field.id;//todo
            field.colSpan = 12;
            items.push([field]);
        }
        items.push([{
                type: "button",
                id: "cancel",
                label: "Cancel",
                colSpan: 6
            }, {
                type: "button",
                id: "ok",
                label: "OK",
                colSpan: 6
            }
        ]);
        keys = that.parent.keys;
        keys.projectId = keys.processID;
        var rowEditor = new PMDynaform.core.Project({
            data: {
                items: [{
                        type: "form",
                        items: items
                    }
                ]
            },
            renderTo: document.body,
            submitRest: true,
            keys: keys,
            token: that.parent.token
        });
        if (index) {
            rowEditor.view.setData2(that.getData()[index]);
        }
        $(rowEditor.view.el).css({
            "position": "absolute",
            "height": "auto"
        });
        $("#shadow-form").remove();

        $(rowEditor.view.el).find("button").on("click", function () {
            if (this.id === "form[ok]") {
                $(rowEditor.view.el).remove();
                that.modalClose();
                var dt = that.getData();
                index = index ? index : that.size + 1;
                dt[index] = {};
                var form = rowEditor.getForms()[0];
                var fields = form.getFields();
                var field;
                for (i = 0; i < fields.length; i++) {
                    field = fields[i];
                    if (field.getInfo) {
                        dt[index][field.getInfo().id] = field.getData().value;
                        dt[index][field.getInfo().id + "_label"] = field.getDataLabel().value;
                        //todo
                        if (field.getInfo().type === "checkbox") {
                            dt[index][field.getInfo().id] = field.getData().value === 0 || field.getData().value === "0" ? "0" : "1";
                            dt[index][field.getInfo().id + "_label"] = field.model.getKeyLabel().value === "false" ? "false" : "true";
                        }
                    }
                }
                that.setData(dt);
            }
            if (this.id === "form[cancel]") {
                $(rowEditor.view.el).remove();
                that.modalClose();
            }
        });
        this.aux = rowEditor;
        return rowEditor;
    };
    GridMobile.prototype.showOptions = function (index) {
        this.modalShow();
        var that = this;
        var edit = $("<div class='pm-form-grid-modal-menu-button pm-form-grid-modal-menu-button-edit'>Edit</div>");
        edit.on("click", function () {
            $(".pm-form-grid-modal-menu").remove();
            that.modalClose();
            that.showRowEditor(index);
        });
        var del = $("<div class='pm-form-grid-modal-menu-button pm-form-grid-modal-menu-button-del'>Delete</div>");
        del.on("click", function () {
            $(".pm-form-grid-modal-menu").remove();
            that.modalClose();
            that.showWarning(function () {
                that.delRow(index);
            });
        });
        var cancel = $("<div class='pm-form-grid-modal-menu-button pm-form-grid-modal-menu-button-cancel'>Cancel</div>");
        cancel.on("click", function () {
            $(".pm-form-grid-modal-menu").remove();
            that.modalClose();
        });

        var options = $("<div class='pm-form-grid-modal-menu'></div>");
        options.css({"top": document.body.scrollTop + "px"});
        options.append(edit);
        options.append(del);
        options.append(cancel);
        $(document.body).append(options);
        return options;
    };
    GridMobile.prototype.showWarning = function (fn) {
        this.modalShow();
        var that = this, yes = $("<div class='pm-form-grid-modal-menu-button pm-form-grid-modal-menu-button-yes'>Yes</div>");
        yes.on("click", function () {
            fn();
            warning.remove();
            that.modalClose();
        });
        var no = $("<div class='pm-form-grid-modal-menu-button pm-form-grid-modal-menu-button-no'>No</div>");
        no.on("click", function () {
            warning.remove();
            that.modalClose();
        });
        var warning = $("<div class='pm-form-grid-modal-message'>" +
                "<div class='pm-form-grid-modal-message-title'>Warning</div>" +
                "<hr>" +
                "<div class='pm-form-grid-modal-message-label'>Are you sure you want to delete this row?</div>" +
                "</div>");
        warning.css({"top": document.body.scrollTop + "px"});
        warning.append(no);
        warning.append(yes);
        $(document.body).append(warning);
        return warning;
    };
    GridMobile.prototype.modalShow = function () {
        var that = this;
        this.parent.getForms()[0].$el.parent().parent().css({"display": "none"});
        this.sh = document.body.scrollTop;
        var modal = $("<div class='pm-form-grid-modal'></div>");
        modal.css({"height": "100%", "width": "100%"});
        $(window).on("resize", function () {
            $(that.aux.view.el).css({"height": "auto"});
            modal.css({"height": "100%", "width": "100%"});
        });
        $(document.body).append(modal);
    };
    GridMobile.prototype.modalClose = function () {
        this.parent.getForms()[0].$el.parent().parent().css({"display": "block"});
        document.body.scrollTop = this.sh;
        $(".pm-form-grid-modal").remove();
        $(document.body).css({"position": "static"});
    };
    PMDynaform.extendNamespace("PMDynaform.model.GridMobile", GridMobile);
}());
(function(){
	var ButtonView = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-button").html()),
		events: {
	        "keydown": "preventEvents"
	    },
		initialize: function () {
			this.model.on("change", this.render, this);
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        on: function (e, fn) {
        	var that = this;
        	$(this.$el.find("button")[0]).on(e, function(event){
        		fn(event, that);
        		
        		event.stopPropagation();
        	});
        	return this;
        },
		render: function() {
			this.$el.html( this.template(this.model.toJSON()) );
			this.tagControl = this.tagHiddenToLabel = this.$el.find("button span");
			return this;
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.Button",ButtonView);
	
}());

(function(){
	var DropDownView = PMDynaform.view.Field.extend({
		events: {
			"change select": "continueDependentFields",
            "blur select": "validate",
            "keydown select": "preventEvents"
		},
		clicked: false,
		jsonData : {}, 
		firstLoad : true,
		dirty : false,
		previousValue : null,
		triggerCallback : false,
		dependentFields : [],
		dependentFieldsData : [],
		template: _.template( $("#tpl-dropdown").html()),
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
		initialize: function () {
			this.model.on("change:value", this.checkBinding, this, {chage:true});
		},
		checkBinding: function (event) {
            var form = this.model.get("form"), 
            	data = {},
            	option, 
            	opts = this.model.get("options"), 
            	that = this,i;
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), this.previousValue);
            }

            if ( form && form.onChangeCallback /*&& this.triggerCallback*/) {
                form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
            	//this.triggerCallback = false;
            } /*else {
            	this.triggerCallback = true;
            }*/
			if (!this.firstLoad){
				this.firstLoad = false;
			}
			this.onChange(event);
			return this;
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
		setValueDefault: function(){
			var val = $(this.el).find(":selected").val();
			if(val!= undefined) {
				this.model.set("value",val);
			}
			else {
				this.model.set("value","");	
			}
		},
		onChange: function (event)  {
			var i, 
			j, 
			item, 
			dependents, 
			viewItems,
			label,
			control,
			hidden,
			sql, nameColumm, nameField, fieldDependentName;
			if ( !this.firstLoad ) {
				hidden = this.$el.find("input[type='hidden']");
				control = this.$el.find("select");
				if (hidden.length && control.length){
					if ($(control).find(":selected").length){
						label = $(control).find(":selected").text().trim();
						this.model.set("keyLabel",label);
							hidden[0].value = label || "";
							this.model.set("data",{
								value : this.model.get("value"),
								label : label
							});
					} else {
						hidden.val("");
						for ( i = 0 ; i < this.model.get("options").length ; i+=1 ) {
							if ( this.model.get("value") === this.model.get("options")[i].value){
								hidden.val(this.model.get("options")[i].label);
								break;
							}
						}
					}
				}
				//find dependent fields
				for ( i = 0 ; i < this.model.get("dependents").length ; i+=1 ) {
					this.model.get("dependents")[i].get("view").firstLoad = false;
					this.model.get("dependents")[i].get("view").onDependentHandler();
				}
				this.clicked = false;				
			}
			return this;
		},		
		continueDependentFields: function () {
			var newValue, 
			auxValue;
			this.previousValue = this.model.get("value");
			this.clicked = true;
			auxValue = $(this.el).find(":selected").val();
			newValue = (auxValue === undefined)? "" : auxValue;			
			this.model.set("value", newValue);
			//this.onChange(event);
			this.changeValuesFieldsRelated();
			
			return this;
		},
		onDependentHandler: function () {
			var execute = true, localOpt, remoteOptions, auxData,key;
			auxData = this.jsonData;
			this.jsonData = this.generateDataDependenField();
			remoteOptions = this.executeQuery();
			this.mergeOptions(remoteOptions);
			if(this.firstLoad){
				this.render();
			}
			return this;
		},							
        validate: function(){
        	var drpValue;
        	if(!this.model.get("disabled")) {
        		drpValue = this.$el.find("select").val() || "";
	            this.model.set({value: drpValue}, {validate: true});
				if (this.model.get("enableValidate")) {
		            if (this.validator) {
		                this.validator.$el.remove();
		                this.$el.removeClass('has-error');
		            }
		            if(!this.model.isValid()){
		                this.validator = new PMDynaform.view.Validator({
		                    model: this.model.get("validator"),
		                    domain: false
		                });  
		                this.$el.find("select").parent().append(this.validator.el);
		                this.applyStyleError();
		            }
				}else{
					this.model.attributes.valid = true;
				}
        	}        	
            return this;
        },
        updateValueControl: function () {
            var i,
            options = this.$el.find("select").find("option");
            loop:
            for (i=0; i<options.length; i+=1) {
            	if (options[i].selected) {
            		this.model.set("value", options[i].value);
            		break loop;
            	}
            }
            return this;    
        },
        on: function (e, fn) {
        	var that = this, 
        	control = this.$el.find("select");
        	if (control) {
        		control.on(e, function(event){
	        		fn(event, that);
	        		event.stopPropagation();
	        	});
        	}
        	return this;
        },
        getHTMLControl: function () {
            return this.$el.find("select");
        },
		render: function() {
			var that = this,
			hidden,
			name,
			containerOptions,
			fullOptions = [],
			option = "",
			thisValue,
			valueOption,
			fullOption;
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.find("option").remove();
			thisValue = this.model.get("value");
			thisValue = thisValue.toString();
			containerOptions = this.$el.find("select");
			fullOptions = this.model.get("options");
			
			for (var i = 0; i < fullOptions.length; i += 1) {
				fullOption = fullOptions[i];
				fullOption.value = fullOption.value || "";
				option = option +
				"<option value='" + fullOption.value + "' " + "  >" + fullOption.label + "</option>";
			}
			containerOptions.append(option);
			
			this._setDataOption();
			this.$el.find("input[type='hidden']").val(this.model.get("data")["label"])
			if (this.model.get("group") === "grid") {
				hidden = this.$el.find("input[type = 'hidden']")[0];
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("_label]");
				hidden.name = hidden.id = "form" + name;
			}
			if (this.model.get("hint")) {
				this.enableTooltip();
			}
			this.setValueToDomain();
			this.$el.find("select").mousedown(
				function (event) {
					var remoteOptions, value;
                	if (that.model.get("parentDependents") && that.model.get("parentDependents").length){
						if (that.firstLoad){
							if (!that.dirty){
								$(this).empty();
								value = that.model.get("value") || "";
								that.jsonData = that.generateDataDependenField();
								remoteOptions = that.executeQuery();
								that.mergeOptions(remoteOptions);
								this.value = value;
								that.dirty = true;
							}
							that.firstLoad = false;
						}
					} else {
						that.firstLoad = false;
					}
				}
			);
			if (this.model.get("name").trim().length === 0){
				this.$el.find("select").attr("name","");
				this.$el.find("input[type='hidden']").attr("name","");
			}
			this.tagControl = this.$el.find("select");
			this.tagControl.val(this.model.get("value")|| "");
			this.tagControl.val(this.model.get("value").toString()|| "");
			this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
			this.keyLabelControl = this.$el.find("input[type='hidden']");
			return this;
		},
		afterRender : function () {
			this.continueDependentFields();
			return this;
		},
		executeQuery : function (clicked) {
			var restClient, key, resp, prj, endpoint, url, data;
			data = this.jsonData || {};	
			if (this.model.get("group") === "grid"){
 				data["field_id"] = this.model.get("columnName");
			}else{
				data["field_id"] = this.model.get("id");
			}
			if ( this.model.get("form") ) {
				if (this.model.get("form").model.get("form")){
					data["dyn_uid"] = this.model.get("form").model.get("form").model.get("id");				
				}else{
					data["dyn_uid"] = this.model.get("form").model.get("id");
				} 
			}
			prj = this.model.get("project");			
			resp = prj.webServiceManager.executeQuery(data,this.model.get("var_name") || "");
			return resp;
		},
		mergeOptions : function (remoteOptions, returnOptions){
			var k, remoteOpt = [], localOpt = [], options = [];
			for ( k = 0; k < remoteOptions.length; k+=1) {
				remoteOpt.push({
					value : remoteOptions[k].value,
					label : remoteOptions[k].text
				});
			}
			localOpt = this.model.get("localOptions");
			this.model.attributes.remoteOptions = remoteOpt;
			this.model.attributes.optionsSql = remoteOpt;
			options = localOpt.concat(remoteOpt);
			this.model.attributes.options = options;
			this._setOptions(this.model.get("options"));
			if (returnOptions){
				return options;
			}
			if (options.length){
				this.model.attributes.data = {
					value : options[0].value,
					label : options[0].label 
				};
				if (this.model.get("validator")){
					this.model.get("validator").set("valid",true);
				}
				this.model.set("value",options[0].value);
			}else{
				this.model.set("value","");
			}
			return options;
		},
		getDependeciesField : function () {
			var i, items, nameField, j, sql;
			nameField = this.model.get("name");
			if (this.parent && this.parent.model.get("items").length) {
				items  = this.parent.model.get("items");
				for ( i = 0 ; i < items.length ; i+=1 ) {
					if (items[i]){
						for ( j = 0 ; j < items[i].length ; j+=1 ){
							if (items[i][j].name && nameField !== items[i][j].name) {
								sql = this.model.get("sql");
								if (sql &&
									(
										sql.indexOf("@#"+items[i][j].name) > -1 ||
										sql.indexOf("@@"+items[i][j].name) > -1 ||
										sql.indexOf("@%"+items[i][j].name) > -1 ||
										sql.indexOf("@="+items[i][j].name) > -1 ||
										sql.indexOf("@?"+items[i][j].name) > -1 ||
										sql.indexOf("@$"+items[i][j].name) > -1 
									)
								) {
									if(this.dependentFields.indexOf(items[i][j].name) === -1) {
										this.dependentFields.push(items[i][j].name);
										this.dependentFieldsData.push(items[i][j]);
									}
								}
							}
						}
					}
				}
			}
		},
		getDependeciesFieldGrid : function () {
			var i, items, nameField, j, sql;
			nameField = this.model.get("columnName");
			if (this.parent && this.parent.items.asArray().length) {
				items  = this.parent.items.asArray()
				for ( i = 0 ; i < items.length ; i+=1 ) {
					if (items[i]){
						if (items[i].model.get("name") && nameField !== items[i].model.get("name")) {
							sql = this.model.get("sql");
							if (sql &&
								(
									sql.indexOf("@#" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@@" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@%" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@=" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@?" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@$" + items[i].model.get("columnName")) > -1 
								)
							) {
								if(this.dependentFields.indexOf(items[i].model.get("columnName")) === -1) {
									this.dependentFields.push(items[i].model.get("columnName"));
									this.dependentFieldsData.push(items[i]);
								}
							}
						}
					}
				}
			}
		},
		generateDataDependenField : function () {
			var i, parentDependents, data = {}, name;
			parentDependents = this.model.get("parentDependents"); 
			for ( i = 0 ; i < parentDependents.length ; i+=1 ) {
				if ( parentDependents[i].get("group") === "grid" ) {
					name = parentDependents[i].get("columnName");
				}else{
					name = parentDependents[i].get("id");
				}
				if (parentDependents[i].get("type") === "text") {
					data[name] = parentDependents[i].get("view").fiendValueDependenField(parentDependents[i].get("value"));
				}else{
					data[name] = parentDependents[i].get("value");
				}
			}
			return data;
		},
		_setOptions : function (options) {
			var i, j, selectControl = this.$el.find("select");
			selectControl.empty();
			for ( i = 0 ; i < options.length ; i+=1 ) {
				selectControl.append($('<option value='+options[i]["value"]+'>'+options[i]["label"]+'</option>'));
			}
			return this;
		},
		_setDataOption : function () {
			var data = this.model.get("data");
			if (this.model.get("parentDependents").length){
				if (data && data["value"] && data["value"].trim().length) {
					this._setOptions([data]);
				}
			}
			return this;
		}
	});
	PMDynaform.extendNamespace("PMDynaform.view.Dropdown",DropDownView);
}());

(function(){
	var RadioView = PMDynaform.view.Field.extend({
		clicked: false,
		previousValue : null,
		template: _.template( $("#tpl-radio").html()),
		events: {
	        "click input": "onChange",
	        "blur input": "validate",
	        "keydown input": "preventEvents"
	    },
		initialize: function (){
			this.model.on("change:value", this.checkBinding, this);
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
		checkBinding: function () {
			var form = this.model.get("form");
            
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), this.previousValue);
            }

            if ( form && form.onChangeCallback) {
                form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
            }

			if (!this.clicked) {
				this.render();
				//this.validate();
			}
			return this;
		},
		render : function () {
			this.$el.html(this.template(this.model.toJSON()));
			if (this.model.get("hint")) {
				this.enableTooltip();
			}
			this.setValueToDomain();
			this.previousValue = this.model.get("value");
			if (this.model.get("name").trim().length === 0){
				this.$el.find("input[type='radio']").attr("name","");
				this.$el.find("input[type='hidden']").attr("name","");
			}
            this.tagControl = this.$el.find(".pmdynaform-radio-items");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
			return this;
		},
		validate: function() {
			if (!this.model.get("disabled")) {
				this.previousValue = this.model.get("value");
				this.model.set({},{validate: true});
				if (this.model.get("enableValidate")) {
					if (this.validator) {
		                this.validator.$el.remove();
		                this.$el.removeClass('has-error has-feedback');
		            }
					if(!this.model.isValid()){
		                this.validator = new PMDynaform.view.Validator({
		                    model: this.model.get("validator")
		                });
		                this.$el.find(".pmdynaform-control-radio-list").parent().append(this.validator.el);
		                this.applyStyleError();
		            }
				}else{
					this.model.attributes.valid = true;
				}
			}
			this.clicked = false;
			return this;
		},
		updateValueControl: function () {
            var i,
            inputs = this.$el.find("input");
            
            for (i=0; i<inputs.length; i+=1) {
            	if (inputs[i].checked) {
            		this.model.setItemClicked({
						value: inputs[i].value,
						checked: true
					});
            	}
            }
        	
            return this;
        },
		onChange: function (event) {
			var hidden, controls, label, i;
			this.clicked = true;
			this.model.setItemClicked({
				value: event.target.value,
				checked: event.target.checked
			});

			this.validate();
			this.changeValuesFieldsRelated();
			hidden = this.$el.find("input[type='hidden']");
			controls = this.$el.find("input[type='radio']");
			if (hidden.length && controls.length){
				for ( i = 0 ; i < this.model.get("options").length ; i +=1 ) {
					if (this.model.get("options")[i]["value"] === this.model.get("value")){
						hidden.val(this.model.get("options")[i]["label"]);
						//this.model.set("keyLabel",this.model.get("options")[i]["label"] || "");
						this.model.attributes.keyLabel = this.model.get("options")[i]["label"] || "";
						break;
					}
				}
			}
		},
		getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-radio-list");
        }
	});

	PMDynaform.extendNamespace("PMDynaform.view.Radio",RadioView);
}());

(function(){
	var SubmitView = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-submit").html()),
		events: {
	        "keydown": "preventEvents"
	    },
		initialize: function (options){
			this.model.on("change", this.render, this);	
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        on: function (e, fn) {
        	var that = this, 
        	control = this.$el.find("button");

        	if (control) {
        		control.on(e, function(event){
	        		fn(event, that);

	        		event.stopPropagation();
	        	});
        	}
        	
        	return this;
        },
		render: function() {
			this.$el.html( this.template(this.model.toJSON()) );
			this.tagControl = this.tagHiddenToLabel = this.$el.find("button span");
			return this;
		}
	});
	
	PMDynaform.extendNamespace("PMDynaform.view.Submit",SubmitView);
	
}());
(function(){
	var TextareaView = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-textarea").html()),
        validator: null,
        keyPressed: false,
        previousValue : "",
        events: {
                "blur textarea": "validate",
                //"keyup textarea": "validate",
                "keydown textarea": "refreshBinding"
        },
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        initialize: function (){
            var that = this;
            this.model.on("change:value", this.checkBinding, this);
        },
        refreshBinding: function () {
            this.keyPressed = true;
            return this;
        },
        checkBinding: function () {
            var form = this.model.get("form");
            if (this.model.get("operation")){
                this.onChangeCallbackOperation(this);
            }
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), this.previousValue);
            }

            if ( form && form.onChangeCallback ) {
                form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
            }

            //If the key is not pressed, executes the render method
            if (!this.keyPressed) {
                this.render();
            }
        },
        render : function () {
        	var hidden, name;
            this.$el.html( this.template(this.model.toJSON()) );
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0,name.length-1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            }
            this.previousValue = this.model.get("value");

            if (this.model.get("name").trim().length === 0){
                this.$el.find("input[type='textarea']").attr("name","");
                this.$el.find("input[type='hidden']").attr("name","");
            }
            this.tagControl = this.$el.find("textarea");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            return this;
        },
        validate: function(event){
            if (event) {
                if ((event.which === 9) && (event.which !==0)) { //tab key
                    this.keyPressed = true;
                }
            }
            if (!this.model.get("disabled")) {
                this.model.set({
                        value: this.$el.find("textarea").val()},{
                        validate: true});
                this.model.set("keyLabel",this.$el.find("textarea").val());
                this.$el.find("input").val(this.model.get("value"));
                if (this.model.get("enableValidate")) {
                    if (this.validator) {
                        this.validator.$el.remove();
                        this.$el.removeClass('has-error');
                    }
                    if(!this.model.isValid()){
                        this.validator = new PMDynaform.view.Validator({
                            model: this.model.get("validator")
                        });  
                        this.$el.find("textarea").parent().append(this.validator.el);
                        this.applyStyleError();
                    }
                }else{
					this.model.attributes.valid = true;
				}
            }
            this.changeValuesFieldsRelated();
            this.keyPressed = false;
            this.previousValue = this.model.get("value");
            return this;
        },
        updateValueControl: function () {
            var inputVal = this.$el.find("textarea").val();

            this.model.set("value", inputVal);

            return this;    
        },
        on: function (e, fn) {
            var that = this, 
            control = this.$el.find("textarea");

            if (control) {
                control.on(e, function(event){
                    fn(event, that);

                    event.stopPropagation();
                });
            }
            
            return this;
        },
        getHTMLControl: function () {
            return this.$el.find("textarea");
        }
	});

	PMDynaform.extendNamespace("PMDynaform.view.TextArea",TextareaView);
		
}());


(function () {
var TextView = PMDynaform.view.Field.extend({
		template: _.template($("#tpl-text").html()),
		validator: null,
		keyPressed: false,
		fieldValid:[],
		previousValue : null,
		firstLoad : true,
		formulaFieldsAssociated: [],
		jsonData : {},
        dependentFields : [],
        dependentFieldsData : [],		
		events: {
			"blur input": "validate",
			//"keyup input": "validate",
			"keydown input": "refreshBinding",
		},
		onChangeCallback: function (){},
		setOnChange : function (fn) {
			if (typeof fn === "function") {
				this.onChangeCallback = fn;
			}
			return this;
		},
		setOnChangeCallbackOperation: function (fn) {
			if (typeof fn === "function") {
				this.onChangeCallbackOperation = fn;
			}
			return this;
		},
		initialize: function () {
			var that = this;
			this.formulaFieldsAssociated = [];
			this.model.on("change:value", this.checkBinding, this);
		},
		on: function (e, fn) {
			var that = this, 
			control,
			localEvents = {
				"changeValues": "setOnChangeCallbackOperation"
			};
			if (localEvents[e]) {
				this[localEvents[e]](fn);
			} else {
				control = this.$el.find("input");
				if (control) {
					control.on(e, function(event){
						fn(event, that);
						event.stopPropagation();
					});
				} else {
					throw new Error ("Is not possible find the HTMLElement associated to field");
				} 
			}
			return this;
		},
		checkBinding: function (event) {
			var form = this.model.get("form"), keyValue;
			/*if(this.model.get("group") === "form"){
				if (this.model.get("operation")){
					this.onChangeCallbackOperation(this);
				}
			}*/
			if ( typeof this.onChangeCallback === 'function' ) {
				this.onChangeCallback(this.getValue(), this.previousValue);
			}
			if ( form && form.onChangeCallback ) {
				form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
			}
			//If the key is not pressed, executes the render method
			//this.onChangeHandler();
			if (!this.keyPressed) {
                this.updateValueInput();

			} else {
				keyValue = this.findKeyValue(this.model.get("value")) || "";
				this.model.set("keyValue",keyValue);
			}
			this.onChange(event);
		},
        updateValueInput : function () {
            var textInput, hiddenInput;
            textInput = this.$el.find("input[type='text']");
            hiddenInput = this.$el.find("input[type='hidden']");
            if (this.model.get("data")) {
                textInput.val(this.model.get("data")["label"]);
                hiddenInput.val(this.model.get("data")["label"]);
            }
            return this;
        },		
		findKeyValue : function (value){
			var i, options = this.model.get("options");
			for ( i = 0 ; i < options.length ; i+=1 ) {
				if ( options[i]["label"] === value ) {
					return options[i]["value"];
				}
			}
			return null;
		},
		refreshBinding: function (event) {
			//Validation for the Submit event
			if (event.which === 13) {
				event.preventDefault();
				event.stopPropagation();
			}
			this.keyPressed = true;
			return this;
		},
		onChange : function () {
            var i, 
            j, 
            item, 
            dependents, 
            viewItems, 
            valueSelected,
            hidden,
			nameField, fieldDependentName, sql;
            this.previousValue = this.model.get("value");
            if ( !this.firstLoad ) {
                hidden = this.$el.find("input[type='hidden']");
                if (hidden.length && this.model.get("value")){
                    valueSelected = this.model.get("value");
                    hidden.val(valueSelected||"");
                    this.model.set("keyLabel",valueSelected);
                }
                for ( i = 0 ; i < this.model.get("dependents").length ; i+=1 ) {
                        this.model.get("dependents")[i].get("view").firstLoad = false;
                        this.model.get("dependents")[i].get("view").onDependentHandler();
				}
                this.clicked = false;
            }
            if (this.model.get("group") == "grid" && this.onChangeCallbackOperation){
            	if (typeof this.onChangeCallbackOperation === "function") {
            		this.onChangeCallbackOperation();
            	}
            }
            return this;
		},
        createDependencies : function () {
            var i, 
            j, 
            item, 
            dependents, 
            viewItems;
            dependents = this.model.get("dependentFields") ? this.model.get("dependentFields"): [];
            viewItems = this.parent.items.asArray();            
            if (dependents.length > 0) {
                for (i = 0; i < viewItems.length; i+=1) {
                    for (j = 0; j < dependents.length; j+=1) {
                        item = viewItems[i].model.get("name");
                        if (dependents[j] === item) {
                            if (viewItems[i].model.setDependencies) {
                                viewItems[i].model.setDependencies(this);
                            }
                        }
                    }
                }
            }
            return this;
        },
        continueDependentFields: function (e) {
            var newValue,
            content;
            this.model.set("clickedControl",true);
            this.clicked = true;
            this.keyPressed = false;
            content = $(e.currentTarget).text();
            //newValue = $(this.el).find(":input").val();          
            $(this.el).find(":input[type='suggest']").val(content);
            this.model.set("value", $(e.currentTarget).find("span").data().value);
            this.containerList.remove();
            this.stackRow = 0;
            this.clicked = false; 
            return this;
        },
        onDependentHandler: function (target , datavalue) {
            var i, localOpt, remoteOptions;
            this.jsonData = this.generateDataDependenField();
			//this.jsonData[target] = datavalue;
            remoteOptions = this.executeQuery();
            this.mergeOptions(remoteOptions);
            this.firstLoad = false;
            return this;
        },
        executeQuery : function (data){
            var restClient, resp, prj, endpoint, url , data = this.jsonData;
			if (this.model.get("group") === "grid"){
 				data["field_id"] = this.model.get("columnName");
			}else{
				data["field_id"] = this.model.get("id");
			}
			if ( this.model.get("form") ) {
				if (this.model.get("form").model.get("form")){
					data["dyn_uid"] = this.model.get("form").model.get("form").model.get("id");				
				}else{
					data["dyn_uid"] = this.model.get("form").model.get("id");
				} 
			}
            prj = this.model.get("project");            
            resp = prj.webServiceManager.executeQuery(data,this.model.get("var_name") || "");            
            return resp;
        },
        mergeOptions : function (remoteOptions, click){
            var k, remoteOpt = [], localOpt = [], options;
            for ( k = 0; k < remoteOptions.length; k+=1) {
                remoteOpt.push({
                    value : remoteOptions[k].value,
                    label : remoteOptions[k].text
                });
            }
            localOpt = this.model.get("localOptions");
			this.model.attributes.optionsSql = remoteOpt;
            this.model.attributes.remoteOptions = remoteOpt;
            options = localOpt.concat(remoteOpt);
            this.model.attributes.options = options;
            if (!click){
	            if (options.length){
	 				this.model.attributes.data = {
	                    value : options[0]["label"],
	                    label : options[0]["label"]
	                };
	            	this.model.set({
	            		"keyValue" : options[0].value,
	            		"value" : options[0].label
	            	});
	            }else{
					this.model.set({	
	            		"keyValue" : "",
	            		"value" : ""
	            	});
				}
            }
            return options;
        },
        fiendValueDependenField : function (dataLabel) {
			var i;
			if (!this.model.get("options").length){
				this.onDependentHandler();
			}
            for ( i = 0 ; i  < this.model.get("options").length ; i+=1 ) {
				if (this.model.get("options")[i]["label"] === dataLabel) {
					return this.model.get("options")[i]["value"];
				}
            }
			return null;
        },
		validate: function (event,b,c) {
			var $inputField, 
			fieldsAssoc,
			originalValue,
			fieldsObj,
			maskLength,
			inputValue,
			i;
			this.keyPressed = true;
			if (event) {
					if ((event.which === 9) && (event.which !==0)) { //tab key
						//this.keyPressed = false;
					}
			}
			if (!this.model.get("disabled")) {
				$inputField = this.$el.find("input");
				if (this.model.get("mask")) {
					inputValue = $inputField.val();
					originalValue = $inputField.cleanVal();
					maskLength = this.model.get("mask").length;
					if (inputValue.length > maskLength ) {
						$inputField.val(inputValue.substring(0,maskLength));
					}
				} else {
					originalValue = $inputField.val();
				}

					//Before save an object
					this.onTextTransform(originalValue);
					this.model.set({value: originalValue}, {validate: true});
					if (this.model.get("enableValidate")) {
						if (this.validator) {
							this.validator.$el.remove();
							this.$el.removeClass('has-error has-feedback');
						}
						if(!this.model.isValid()){
							this.validator = new PMDynaform.view.Validator({
								model: this.model.get("validator")
							});
							$inputField.parent().append(this.validator.el);
							this.applyStyleError();
						}
					}else{
						this.model.attributes.valid = true;
					}
					this.changeValuesFieldsRelated();
					this.onFieldAssociatedHandler();
					this.keyPressed = false;
					this.previousValue = this.model.get("value");
				
			}
			return this;
		},
		updateValueControl: function () {
			var inputVal = this.$el.find("input").val();
			this.model.set("value", inputVal);
			return this; 
		},
		onFieldAssociatedHandler: function() {
			var i,
			fieldsAssoc = this.formulaFieldsAssociated;
			if (fieldsAssoc.length > 0) {
				for (i=0; i<fieldsAssoc.length; i+=1) {
					if (fieldsAssoc[i].model.get("formulator") instanceof PMDynaform.core.Formula) {
						this.model.addFormulaTokenAssociated(fieldsAssoc[i].model.get("formulator"));
						this.model.updateFormulaValueAssociated(fieldsAssoc[i]);
					}
				}
			}
			return this;
		},
		onTextTransform: function (val) {
			var transformed,
			transform = this.model.get("textTransform"),
			availables = {
				upper: function () {
					return val.toUpperCase();
				},
				lower: function () {
					return val.toLowerCase();
				},
				none: function() {
					return val;
				},
				capitalizePhrase : function (){
					return val.charAt(0).toUpperCase() + val.slice(1);
				},
				titleCase : function (){
					return val.capitalize();
				}
			};
			if (transform) {
				transformed = (availables[transform]) ? availables[transform]() : availables["none"]();
				this.$el.find("input").val(transformed);
			}
			return this;
		},
		onFormula: function(rows) {
			var fieldsList,
			that = this,
			allFields,
			allFieldsView,
			index,
			formulaField,
			idFields = {},
			fieldFormula,
			fieldValid,
			resultField,
			fieldAdded = [],
			fieldSelected,
			obj,
			i;
			if (this.model.get("group") == "grid") {
				fieldsList = rows;
			} else {
				fieldsList = this.parent.items;
			}
			//All Fields from the FORM
			allFieldsView = (fieldsList instanceof Array)? fieldsList: fieldsList.asArray();

			for (index = 0 ; index < allFieldsView.length ; index+=1 ) {
				if (allFieldsView[index] instanceof PMDynaform.view.Text) {
					idFields[allFieldsView[index].model.get("id")] = allFieldsView[index];
				}
				if (allFieldsView[index] instanceof PMDynaform.view.Label){
					idFields[allFieldsView[index].model.get("id")] = allFieldsView[index];	
				}
			}

			fieldSelected = {};
			//Fields from the Formula PROPERTY
			formulaField = this.model.get("formula");
			fieldFormula = formulaField.split(/[\-(,|+*/\)]+/);
			if (this.model.get("group") == "grid") {
				for (var k = 0 ; k < rows.length ; k+=1){
					if (fieldFormula.indexOf(rows[k].model.get("id")) > -1){
						rows[k].onFieldAssociatedHandler();
					}
				}
			}
			this.fieldValid = fieldFormula.filter(function existElement(element) {
				var result = false;
				if ((idFields[element] !== undefined) && ($.inArray(element, fieldAdded) === -1)) {
					fieldAdded.push(element);
					result = true
				}
				return result;
			});
			//Insert the Formula object to fields selected
			for (var obj = 0 ; obj < this.fieldValid.length ; obj+=1 ) {
				this.model.addFormulaFieldName(this.fieldValid[obj]);
				idFields[this.fieldValid[obj]].formulaFieldsAssociated.push(that);
				if (this.model.get("group") == "grid" ){
					if(idFields.hasOwnProperty(this.fieldValid[obj])){
						this.model.attributes.formulaAssociatedObject.push(idFields[this.fieldValid[obj]]);
					}
				}
			}
			return this;
		},
		getHTMLControl: function () {
			return this.$el.find("input");
		},
		render: function() {
			var hidden, name, that = this;
			this.$el.html( this.template(this.model.toJSON()));
			if (this.model.get("hint") !== "") {
				this.enableTooltip();
			}
			this.previousValue = this.model.get("value");
			if (this.model.get("group") === "grid") {
				hidden = this.$el.find("input[type = 'hidden']")[0];
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("_label]");
				hidden.name = hidden.id = "form" + name;
				hidden.value = this.model.get("value"); 
			}

			if (this.model.get("group") === "form" && this.model.get("formula")) {
				this.onFormula();
			}

			this.$el.find("input[type='text']").focusin(function(event){
                var remoteOptions, value;
				var fieldsAssociated;
				if (that.formulaFieldsAssociated.length){
					fieldsAssociated = that.formulaFieldsAssociated[0];
					for (var i = 0 ; i < fieldsAssociated.model.get("formulaAssociatedObject").length; i+=1){
						fieldsAssociated.model.get("formulaAssociatedObject")[i].onFieldAssociatedHandler();
					}
				}
                that.clicked = true;
                if (that.model.get("parentDependents") && that.model.get("parentDependents").length){
                    if (that.firstLoad){
                        if (!that.dirty){
                        	value = this.value; 
							that.jsonData = that.generateDataDependenField();
                            remoteOptions = that.executeQuery(data);
                            that.mergeOptions(remoteOptions);
                            this.value = value;
                            that.dirty = true;
                        }
                        that.firstLoad = false;
                    }
                } else {
                    that.firstLoad = false;
                }
            });
			/*this.$el.find("input[type='text']").click(function(event){
				console.log(that);
				var fieldsAssociated;
				if (that.formulaFieldsAssociated.length){
					fieldsAssociated = that.formulaFieldsAssociated[0];
					for (var i = 0 ; i < fieldsAssociated.model.get("formulaAssociatedObject").length; i+=1){
						fieldsAssociated.model.get("formulaAssociatedObject")[i].onFieldAssociatedHandler();
					}
				}
			});*/
			if (this.model.get("name").trim().length === 0){
				this.$el.find("input[type='text']").attr("name","");
				this.$el.find("input[type='hidden']").attr("name","");
			}
			this.tagControl = this.$el.find("input[type='text']");
			this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
			this.keyLabelControl = this.$el.find("input[type='hidden']");
			return this;
		},
        getDependeciesField : function () {
            var i, items, nameField, j, sql;
            nameField = this.model.get("name");
            this.dependenciesField = [];
            this.dependentFieldsData = [];
            if (this.parent && this.parent.model.get("items").length) {
                items  = this.parent.model.get("items");
                for ( i = 0 ; i < items.length ; i+=1 ) {
                    if (items[i]){
                        for ( j = 0 ; j < items[i].length ; j+=1 ){
                            if (items[i][j].name && nameField !== items[i][j].name) {
                                sql = this.model.get("sql");
                                if (sql && (
									sql.indexOf("@#"+items[i][j].name) > -1 ||
									sql.indexOf("@@"+items[i][j].name) > -1 ||
									sql.indexOf("@%"+items[i][j].name) > -1 ||
									sql.indexOf("@="+items[i][j].name) > -1 ||
									sql.indexOf("@?"+items[i][j].name) > -1 ||
									sql.indexOf("@$"+items[i][j].name) > -1 )
								) {
                                    if(this.dependentFields.indexOf(items[i][j].name) === -1) {
                                        this.dependentFields.push(items[i][j].name);
                                        this.dependentFieldsData.push(items[i][j]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        getDependeciesFieldGrid : function () {
            var i, items, nameField, j, sql;
            nameField = this.model.get("columnName");
            if (this.parent && this.parent.items.asArray().length) {
                items  = this.parent.items.asArray()
                for ( i = 0 ; i < items.length ; i+=1 ) {
                    if (items[i]){
                        if (items[i].model.get("name") && nameField !== items[i].model.get("name")) {
                            sql = this.model.get("sql");
                            if (sql && 
								(
									sql.indexOf("@#" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@@" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@%" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@=" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@?" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@$" + items[i].model.get("columnName")) > -1 
								)
							) {
                                if(this.dependentFields.indexOf(items[i].model.get("columnName")) === -1) {
                                    this.dependentFields.push(items[i].model.get("columnName"));
                                    this.dependentFieldsData.push(items[i]);
                                }
                            }
                        }
                    }
                }
            }
        },
        generateDataDependenField : function () {
			var i, parentDependents, data = {}, name;
			parentDependents = this.model.get("parentDependents"); 
			for ( i = 0 ; i < parentDependents.length ; i+=1 ) {
				if ( parentDependents[i].get("group") === "grid" ) {
					name = parentDependents[i].get("columnName");
				}else{
					name = parentDependents[i].get("id");
				}
				if (parentDependents[i].get("type") === "text") {
					data[name] = parentDependents[i].get("view").fiendValueDependenField(parentDependents[i].get("value"));
				}else{
					data[name] = parentDependents[i].get("value");
				}
			}
			return data;
        }
	});
	PMDynaform.extendNamespace("PMDynaform.view.Text",TextView);
}());
(function(){
	var File = PMDynaform.view.Field.extend({
		item: null,
		firstLoad : true,
		isIE : false,
		template: _.template( $("#tpl-file").html()),
		events: {
			"click .pmdynaform-file-container .form-control": "onClickButton",
			"click div[name='button-all'] .pmdynaform-file-buttonup": "onUploadAll",
			"click div[name='button-all'] .pmdynaform-file-buttoncancel": "onCancelAll",
			"click div[name='button-all'] .pmdynaform-file-buttonremove": "onRemoveAll"
		},
		initialize: function () {
			//this.setOnChangeFiles();
			this.model.on("change", this.render, this);
		},
		onClickButton: function (event) {
			if(!PMDynaform.core.ProjectMobile){
				this.$el.find("input").trigger( "click" );
				event.preventDefault();
				event.stopPropagation();
			}
			return this;
		},
		onUploadAll: function (event) {
			var i,
			length = this.model.get("items").length;

			this.$el.find("div[name='button-all'] .pmdynaform-file-buttonup").hide();
			this.$el.find("div[name='button-all'] .pmdynaform-file-buttonremove").hide();
			this.$el.find("div[name='button-all'] .pmdynaform-file-buttoncancel").show();
			for (i=0; i<length; i+=1) {
				this.model.uploadFile(i);	
			}

			event.preventDefault();
			event.stopPropagation();

			return this;
		},
		onCancelAll: function (event) {
			var i,
			length = this.model.get("items").length;

			this.$el.find("div[name='button-all'] .pmdynaform-file-buttonup").show();
			this.$el.find("div[name='button-all'] .pmdynaform-file-buttonremove").show();
			this.$el.find("div[name='button-all'] .pmdynaform-file-buttoncancel").hide();
			for (i=0; i<length; i+=1) {
				this.model.stopUploadFile(i);
			}
			event.preventDefault();
			event.stopPropagation();

			return this;
		},
		onRemoveAll: function (event) {

			this.model.set("items", []);
			this.render();
			event.preventDefault();
			event.stopPropagation();

			return this;
		},
		removeItem: function (event) {
			var items = this.model.get("items"),
			index = $(event.target).data("index");

			items.splice(index, 1);
			this.model.set("items", items);
			this.render();
			return this;
		},
		addNewItem: function (e, file) {
			if (this.validate(e, file)) {
				var items = this.model.get("items");
				items.push({
					event: e,
					file: file
				});
				this.model.set("items", items);
				/*
				 * The call to render method will not be necessary if the change event from initialize
				 * method would be working
				 **/
				this.render();
				if (items.length > 0) {
					this.$el.find(".pmdynaform-file-container div[name='button-all']").show();
				} else {
					this.$el.find(".pmdynaform-file-container div[name='button-all']").hide();
				}
			}
						
			return this;
		},
		onUploadItem: function (event) {
			var index = $(event.target).data("index");

			this.model.uploadFile(index);
			return this;
		},
		onCancelUploadItem: function () {
			var index = $(event.target).data("index");

			this.model.stopUploadFile(index);
			return this;
		},
		onToggleButtonUpload: function (event, action) {
			var i,
			j,
			buttons = $(event.target).parent().parent(),
			opt = {
				up: {
					show: [
						".pmdynaform-file-buttonstop",
						".pmdynaform-file-buttoncancel"
					],
					hide: [
						".pmdynaform-file-buttonup",
						".pmdynaform-file-buttonremove"
					]
				},
				cancel: {
					show: [
						".pmdynaform-file-buttonup",
						".pmdynaform-file-buttonremove"
					],
					hide: [
						".pmdynaform-file-buttonstop",
						".pmdynaform-file-buttoncancel"
					]
				}
			};

			for ( i=0; i<opt[action].hide.length; i+=1 ) {
				buttons.find(opt[action].hide[i]).hide();
			} 

			for ( j=0; j<opt[action].show.length; j+=1 ) {
				buttons.find(opt[action].show[j]).show();
			} 
			
			return this;
		},
		onClosePreview: function () {

			return this;
		},
		onPreviewItem: function (event) {
			var file,
			that = this,
			reader,
			previewFile,
			resource,
			previewFileName,
			heightContainer,
			shadow = document.createElement("div"),
			background = document.createElement("div"),
			preview = document.createElement("img"),
			index = $(event.target).data("index"),
			closeItem = document.createElement("span");
			closeItem.className = "glyphicon glyphicon-remove";

			closeItem.title = "Close";
			closeItem.setAttribute("data-placement", "bottom");
			
			$(closeItem).tooltip().click(function(e) {
				$(this).tooltip('toggle');
				event.preventDefault();
				event.stopPropagation();
			});

			heightContainer = $(document.documentElement).height();

			shadow.className = "pmdynaform-file-shadow";
			shadow.style.height = heightContainer + "px";
			background.className = "pmdynaform-file-preview-background"
			background.style.height = heightContainer + "px";
			background.setAttribute("contenteditable", "true");
			
			background.appendChild(closeItem);
			
			file = this.model.get("items")[index].file;
			
			if (file.type.match(/image.*/)) {
				reader  = new FileReader();
				reader.onloadend = function () {
					preview.src = reader.result;
				}
				reader.readAsDataURL(file);
				//preview.style.top = document.body.scrollTop + "px";	
			} else if(file.type.match(/audio.*/)) {
				resource = _.template( $("#tpl-audio").html());
				preview = resource({
					path: URL.createObjectURL(file),
					top: document.body.scrollTop + "px"
				});

			} else if(file.type.match(/video.*/)) {
				resource = _.template( $("#tpl-video").html());
				preview = resource({
					path: URL.createObjectURL(file),
					top: document.body.scrollTop + "px"
				});
			}/* else {

			}*/

			previewFile = document.createElement("div");
			previewFileName = document.createElement("p");
			previewFileName.name = "desc";
			previewFileName.appendChild(document.createTextNode(file.extra.nameNoExtension));
			previewFile.className = "pmdynaform-file-preview-image";
			
			$(previewFile).append(preview);
			$(previewFile).append(previewFileName);
			$(closeItem).on('click', function (event){
				document.body.removeChild(shadow);
				document.body.removeChild(background);
				that.$el.parents(".pmdynaform-container").css("position","");
				event.preventDefault();
				event.stopPropagation();
			});
			$(background).keyup(function(event){
				if (event.which === 27) {
					document.body.removeChild(shadow);
					document.body.removeChild(background);
					that.$el.parents(".pmdynaform-container").css("position","");
					event.preventDefault();
					event.stopPropagation();	
				}
			});
			
			$(background).append(previewFile);
			document.body.appendChild(shadow);
			document.body.appendChild(background);

			//Puts the parent node in Fixed mode
			this.$el.parents(".pmdynaform-container").css("position","fixed");

			return this;
		},
		renderFiles: function () {
			var i,
			that = this,
			items = this.model.get("items");

			for (i=0; i< items.length; i+=1) {
				if (that.model.get("preview")) {
					that.createBox(i, items[i].event,items[i].file);
				} else {
					that.createListBox(i, items[i].event, items[i].file);
				}
			}
			
			return this;
		},
		createButtonsHTML: function (index, opts) {
			var that = this,
			buttonGroups = document.createElement("div"),
			buttonGroupUp = document.createElement("div"),
			buttonGroupCancel = document.createElement("div"),
			buttonGroupRemove = document.createElement("div"),
			buttonGroupView = document.createElement("div"),
			buttonUp = document.createElement("button"),
			buttonCancel = document.createElement("button"),
			buttonRemove = document.createElement("button"),
			buttonView = document.createElement("button");

			/*
			 * Adding Options buttons to file
			 **/
			buttonGroups.className = "btn-group btn-group-justified";

			buttonGroupUp.className = "pmdynaform-file-buttonup btn-group";
			buttonGroupCancel.className = "pmdynaform-file-buttoncancel btn-group";
			buttonGroupCancel.style.display = "none";
			buttonGroupRemove.className = "pmdynaform-file-buttonremove btn-group";
			buttonGroupView.className = "pmdynaform-file-buttonview btn-group";

			buttonUp.className = "glyphicon glyphicon-upload btn btn-success btn-sm";
			buttonCancel.className = "glyphicon glyphicon-remove btn btn-danger btn-sm";
			buttonRemove.className = "glyphicon glyphicon-trash btn btn-danger btn-sm";
			buttonView.className = "glyphicon glyphicon-zoom-in btn btn-primary btn-sm";

			$(buttonUp).data("index", index);
			$(buttonCancel).data("index", index);
			$(buttonRemove).data("index", index);
			$(buttonView).data("index", index);
			
			$(buttonUp).on("click", function(event) {
				that.onToggleButtonUpload(event, "up");
				that.onUploadItem(event);
				event.stopPropagation();
				event.preventDefault();
			});
			$(buttonCancel).on("click", function(event) {
				that.onToggleButtonUpload(event, "cancel");
				that.onCancelUploadItem(event);
				event.stopPropagation();
				event.preventDefault();
			});
			$(buttonRemove).on("click", function(event) {
				that.removeItem(event);
				event.stopPropagation();
				event.preventDefault();
			});			
			$(buttonView).on("click", function(event) {
				that.onPreviewItem(event);
				event.stopPropagation();
				event.preventDefault();
			});

			buttonGroupUp.appendChild(buttonUp);
			buttonGroupCancel.appendChild(buttonCancel);
			buttonGroupRemove.appendChild(buttonRemove);
			buttonGroupView.appendChild(buttonView);
			if (opts.upload) {
				buttonGroups.appendChild(buttonGroupUp);
			}
			if (opts.cancel) {
				buttonGroups.appendChild(buttonGroupCancel);
			}
			if (opts.preview) {
				buttonGroups.appendChild(buttonGroupView);
			}
			if (opts.remove) {
				buttonGroups.appendChild(buttonGroupRemove);	
			}
			
			return buttonGroups;
		},
		createBox: function (index, e, file) {
			var buttonGroups,
			resource,
			enabledPreview = true,
			rand = Math.floor((Math.random()*100000)+3),
			imgName = file.name,
			src = e.target.result,
			template = document.createElement("div"),
			resizeImage = document.createElement("div"),
			preview = document.createElement("span"),
			progress = document.createElement("div"),
			imgPreview = document.createElement("img"),
			spanOverlay = document.createElement("span"),
			spanUpDone = document.createElement("span"),
			typeClasses = {
				video: {
					className: "pmdynaform-file-boxpreview-video",
					icon: "glyphicon glyphicon-facetime-video"
				},
				audio: {
					className: "pmdynaform-file-boxpreview-audio",
					icon: "glyphicon glyphicon-music"
				},
				file: {
					className: "pmdynaform-file-boxpreview-file",
					icon: "glyphicon glyphicon-book"
				}
			},
			fileName = file.extra.extension.toUpperCase();

			template.id = rand;
			template.className = "pmdynaform-file-containerimage";

			resizeImage.className = "pmdynaform-file-resizeimage";
			if (file.type.match(/image.*/)) {
				imgPreview.src = src;
				resizeImage.appendChild(imgPreview);
			} else if(file.type.match(/audio.*/)) {
				resizeImage.innerHTML = '<div class="'+ typeClasses['audio'].className +' thumbnail ' + typeClasses['audio'].icon+'"><div>'+ fileName +'</div></div>'; 
			} else if(file.type.match(/video.*/)) {
				resizeImage.innerHTML = '<div class="'+ typeClasses['video'].className +' thumbnail ' + typeClasses['video'].icon+'"><div>'+ fileName +'</div></div>'; 
			} else {
				enabledPreview = false,
				resizeImage.innerHTML = '<div class="'+ typeClasses['file'].className +' thumbnail ' + typeClasses['file'].icon+'"><div>'+ fileName +'</div></div>'; 
			}
			spanOverlay.className = "pmdynaform-file-overlay";
			spanUpDone.className = "pmdynaform-file-updone";
			spanOverlay.appendChild(spanUpDone);
			resizeImage.appendChild(spanOverlay);

			preview.id = rand;
			preview.className = "pmdynaform-file-preview";
			preview.appendChild(resizeImage);	    	

			progress.id = rand;
			progress.className = "pmdynaform-file-progress";
			progress.innerHTML = "<span></span>";

			template.appendChild(preview);
			buttonGroups = this.createButtonsHTML(index, {
				upload: true,
				preview: enabledPreview,
				cancel: true,
				remove: true
			});
			template.appendChild(buttonGroups);
			template.appendChild(progress);
			this.$el.find(".pmdynaform-file-droparea").append(template);
			
			return this;
			
		},
		createListBox: function (index, e, file) {
			var buttonGroups,
			enabledPreview = true,
			iconFile,
			rand = Math.floor((Math.random()*100000)+3),
			listItem = document.createElement("div"),
			label = document.createElement("div");

			listItem.className = "pmdynaform-file-listitem";
			if (file.type.match(/image.*/)) {
				//
			} else if(file.type.match(/audio.*/)) {
				//
			} else if(file.type.match(/video.*/)) {
				//
			} else {
				enabledPreview = false;				
			}
			buttonGroups = this.createButtonsHTML(index, {
				upload: true,
				preview: enabledPreview,
				cancel: true,
				remove: true
			});
			buttonGroups.style.width = "50%";
			label.className = "pmdynaform-label-nowrap";
			label.innerHTML = file.name;
			listItem.appendChild(label)
			listItem.appendChild(buttonGroups);
			

			this.$el.find(".pmdynaform-file-list").append(listItem);

			return this;
		},
		toggleButtonsAll: function () {
			//Select the name="button-all" for show the buttons

			return this;
		},
		render: function () {
			var that = this,
                fileContainer,
                fileControl,
                oprand,
                hidden,
                name,
                fileButton,
                fileControl;
            var title = 'Allowed file extensions: ' + this.model.get('extensions');
            var link;
            var i;
            var label = that.model.get("data")["label"];
			
			this.$el.html( this.template(this.model.toJSON()));
			
			fileControl = this.$el.find("input[type='file']");
			fileButton = that.$el.find("button[type='button']");
			if(PMDynaform.core.ProjectMobile) {
                fileButton.attr("disabled", "disabled");
            }
            link = this.$el.find("a.pmdynaform-control-file");
            fileButton.text(title);
            fileButton[0].title = title;
			hidden = this.$el.find("input[type='hidden']");
			fileContainer = this.$el.find(".pmdynaform-file-control")[0];
			
			if (this.model.get("hint")) {
				this.enableTooltip();
			}

            if (link.length > 0 && label.length > 0) {
                for (i = 0; i < label.length; i += 1) {
                    link.children()[i].title = label[i];
                }
            }

			if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
				fileControl.css({visibility:"inherit", width : "100%"});
				fileButton.css({display:"none"});
				this.isIE = true;
			}

			if (this.model.get("group") === "grid") {
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("_label]");
				hidden[0].name = hidden[0].id = "form" + name;
			} else {
				hidden[0].name = hidden[0].id = "form[" + this.model.get("name")+"_label]";
			}
			
            hidden.val(JSON.stringify(this.model.get("data")["label"]));
            			
			fileControl.change(function(e, ui){
				var file = e.target, nameFileLoad;
                var fileButton;
				if (file.value  && that.isValid(file)){
                    fileButton = that.$el.find("button[type='button']");
					if (file.files){
						nameFileLoad = file.files[0].name;
					} else {
						nameFileLoad = file.value.split("\\")[2]
					}
                    fileButton.text(nameFileLoad);
                    fileButton[0].title = nameFileLoad;
                    if (that.model.get("data")) {
                        if ( that.model.get("data")["label"].length) {
                            if ( that.model.get("data")["label"].indexOf(nameFileLoad) === -1  ) {
                                if (that.firstLoad){
                                    that.model.get("data")["label"].push(nameFileLoad);
                                }else{
                                    that.model.get("data")["label"].splice(that.model.get("data")["label"].length-1);
                                    that.model.get("data")["label"].push(nameFileLoad);
                                }
                            }
                        } else {
                            that.model.get("data")["label"].push(nameFileLoad);
                        }
                    }
					hidden.val(JSON.stringify(that.model.get("data")["label"]));
					that.firstLoad = false;
				} else {
                    file.value = "";
                    file.files = null;
                }
			});
			//PMDynaform.core.FileStream.setupInput(fileControl, oprand); 
			return this;
		},
        getFileType: function (file) {
            var type;
            var fileTarget;
            if (file.files){
                if (file.files[0]){
                    type = file.files[0].name.substring(file.files[0].name.lastIndexOf(".")+1);
                    fileTarget = file.files[0].name;
                }
            } else {
                if (file.value.trim().length){
                    type = file.value.split("\\")[2].substring(file.value.split("\\")[2].lastIndexOf(".")+1);
                    fileTarget = file.value;
                }
            }
            return {
                type: type,
                fileTarget: fileTarget
            }
        },
		isValid : function (file) {
			var validated = false, extensions, maxSize, type, fileTarget;
            var that = this;
            var errorType = {};
            extensions = this.model.get("extensions");
            var getType = that.getFileType(file);
            var validatorModel = this.model.get('validator');
            var tagFile = this.$el.find("input[type='file']")[0];
            var fileButton = that.$el.find("button[type='button']");
            var title = 'Allowed file extensions: ' + extensions;
            var maxSizeInt = parseInt(this.model.get("size"), 10);
            var sizeUnity = this.model.get("sizeUnity");

			if (this.model.get("sizeUnity").toLowerCase() !== "kb" ){
				maxSize = maxSizeInt*1024;
			} else {
				maxSize = maxSizeInt;
			}
			if (extensions === "*" || extensions === ".*"){
				validated = true;
			} else {
				if (this.model.get("extensions").indexOf(getType.type) > -1) {
					validated = true;
				}else{
                    errorType = {
                        type: 'support',
                        message: 'The file extension is not supported. Supported extension(s): '
                        + extensions
                    };
                    validatorModel.set('fileOnly',  errorType);
                    fileButton.text(title);
                    fileButton[0].title = title;
                    validated = false;
				}
			}
			if (validated && file.files){
				if ( file.files[0] && (file.files[0].size/1024 <= maxSize) ){
					validated = true
				}else{
                    errorType = {
                        type: 'size',
                        message:  "The file size exceeds the limit. Max allowed limit is: " + maxSizeInt + sizeUnity
                    };
                    validatorModel.set('fileOnly',  errorType);
                    fileButton.text(title);
                    fileButton[0].title = title;
                    validated = false;
				}				
			}
			if (validated){
                validatorModel.set('fileOnly', null);
				this.model.attributes.value = getType.fileTarget || "";
				if (this.validator){
					this.validator.$el.remove();
					this.$el.removeClass('has-error has-feedback');
                    this.$el.removeClass('has-warning has-feedback');
				}
			} else {
                if(this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                    this.$el.removeClass('has-warning has-feedback');
                }
                if (!this.model.isValid() && validatorModel.get('fileOnly') !== null) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    $(tagFile).prev().append(this.validator.el);
                    if (this.model.get('validator').get('fileOnly')) {
                        this.applyStyleWarning();
                        this.validator.$el.children().removeClass('alert');
                        this.validator.$el.children().addClass('warning');
                    }
                    validatorModel.set('fileOnly', null);
                    this.model.attributes.value = "";
                }
            }
			return validated;
		},
		validate: function(e) {
			var tagFile = this.$el.find("input[type='file']")[0], validated = true;
			if (this.model.get("mode")=="view" || this.model.get("mode")=="disabled"){
				return true;
			}
			if (this.model.get("enableValidate")) {
				if(this.validator){
					this.validator.$el.remove();
					this.$el.removeClass('has-error has-feedback');
                    this.$el.removeClass('has-warning has-feedback');
				}
				if (!this.model.isValid()){
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    $(tagFile).prev().append(this.validator.el);
                    this.applyStyleError();
                    this.model.get('validator').get('fileOnly', null);
                    validated = false;
                    this.model.attributes.valid = false;
				}else{
					validated = true;
					this.model.attributes.valid = true;
				}
			}else{
				this.model.attributes.valid = true;
			}
			return validated;	
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.File",File);
}());

(function(){
    var CheckGroupView = PMDynaform.view.Field.extend({
        item: null, 
        template: _.template( $("#tpl-checkgroup").html()),
        previousValue : null,
        events: {
            "click input": "onChange",
            "blur input": "validate",
            "keydown input": "preventEvents"
        },
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        initialize: function (){
            this.model.on("change", this.checkBinding, this);
        },
        checkBinding: function () {
            var form = this.model.get("form");
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), JSON.stringify(this.previousValue));
            }
            if ( form && form.onChangeCallback) {
                form.onChangeCallback(this.model.get("id"), JSON.stringify(this.model.get("value")), JSON.stringify(this.previousValue));
            }
            this.render();
            //this.validate();
            return this;
        },
        preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            return this;
        },
        render : function () {
            var hidden, name;
            this.$el.html( this.template(this.model.toJSON()));
			this.$el.find(".form-control").css({
				height:"auto"
			});
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.setValueToDomain();
            this.$el.find("input[type='hidden']")[0].name = "form[" + this.model.get("name")+"_label]";
            this.setValueHideControl();
            this.previousValue = this.model.get("value");
            if (this.model.get("name").trim().length === 0){
                this.$el.find("input[type='checkbox']").attr("name","");
                this.$el.find("input[type='hidden']").attr("name","");
            }
            this.tagControl = this.$el.find(".pmdynaform-checkbox-items");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            return this;
        },
        validate: function() {
            if (!this.model.get("disabled")) {
                this.model.set({},{validate: true});
                if (this.model.get("enableValidate")) {
                    if (this.validator) {
                        this.validator.$el.remove();
                        this.$el.removeClass('has-error has-feedback');
                    }
                    if(!this.model.isValid()) {
                        this.validator = new PMDynaform.view.Validator({
                            model: this.model.get("validator")
                        });  
                        this.$el.find(".pmdynaform-control-checkbox-list").parent().append(this.validator.el);
                        this.applyStyleError();
                    }
                }else{
					this.model.attributes.valid = true;
				}
            }           
            return this;
        },
        updateValueControl: function () {
            var i,
            inputs = this.$el.find("input");

            for (i=0; i<inputs.length; i+=1) {
                if (inputs[i].checked) {
                    this.model.setItemChecked({
                        value: inputs[i].value,
                        checked: true
                    });
                }
            }
            
            return this;    
        },
        onChange: function (event) {
            var checked, data = {};
            this.previousValue = this.model.get("value");
            $(event.target).val();
            checked = event.target.checked;
            this.$el.find("input[type='hidden']").val(JSON.stringify([]));
            this.model.setItemChecked({
                value: event.target.value,
                checked: checked
            });
            this.$el.find("input[type='hidden']").val(JSON.stringify(this.model.get("labelsSelected")));
			if (!this.model.get("labelsSelected").length){
				this.$el.find("input[type='checkbox']").last()[0].checked = true;
			}else{
				this.$el.find("input[type='checkbox']").last()[0].checked = false;			
			}
            this.validate();
        },
        getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-checkbox-list");
        },
        setValueHideControl : function () {
            var control;
            control = this.$el.find("input[type='hidden']");
            $(control).val(JSON.stringify(this.model.get("labelsSelected")));
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.CheckGroup",CheckGroupView);
}());
(function(){
	var CheckBoxView = PMDynaform.view.Field.extend({
		item: null,	
		template: _.template( $("#tpl-checkbox_yes_no").html()),
		previousValue : null,
		events: {
	        "click input": "onChange",
            "keydown input": "preventEvents"
	    },
		onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
		initialize: function (){
			this.model.on("change:value", this.checkBinding, this);
		},
		checkBinding: function () {
			var form = this.model.get("form");
			if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(JSON.stringify(this.getValue()), JSON.stringify(this.previousValue));
            }
            if ( form && form.onChangeCallback) {
                form.onChangeCallback(this.model.get("id"), JSON.stringify(this.model.get("value")), JSON.stringify(this.previousValue));
            }
			return this;
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            return this;
        },
		render : function () {
			var hidden, name;
            this.$el.html( this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.setValueToDomain();
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0,name.length-1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            }else{
                this.$el.find("input[type='hidden']")[0].name = "form[" + this.model.get("name")+"_label]";
            }
            if (this.model.get("options").length){
            	this.$el.find("input[type='checkbox']").eq(0).data({
            		value : this.model.get("options")[0]["value"],
            		label : this.model.get("options")[0]["label"]
            	});
            	this.$el.find("input[type='checkbox']").eq(1).data({
            		value : this.model.get("options")[1]["value"],
            		label : this.model.get("options")[1]["label"]
            	});
            }
            this.setValueHideControl();
            this.previousValue = this.model.get("value");

			if (this.model.get("name").trim().length === 0){
				this.$el.find("input[type='checkbox']").attr("name","");
				this.$el.find("input[type='hidden']").attr("name","");
			}
			this.tagControl = this.$el.find(".pmdynaform-checkbox-items");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
			this.keyLabelControl = this.$el.find("input[type='hidden']");
            return this;
		},
		validate: function() {
			if (!this.model.get("disabled")) {
				this.model.set({},{validate: true});
				if (this.model.get("enableValidate")) {
					if (this.validator) {
		                this.validator.$el.remove();
		                this.$el.removeClass('has-error has-feedback');
		            }
					if(!this.model.isValid()) {
		                this.validator = new PMDynaform.view.Validator({
		                    model: this.model.get("validator")
		                });  
		                this.$el.find(".pmdynaform-control-checkbox-list").parent().append(this.validator.el);
		                this.applyStyleError();
		            }
		       	}else{
					this.model.attributes.valid = true;
				}
			}			
			return this;
		},
		updateValueControl: function () {
            var i,
            inputs = this.$el.find("input");

            for (i=0; i<inputs.length; i+=1) {
            	if (inputs[i].checked) {
            		this.model.setItemChecked({
						value: inputs[i].value,
						checked: true
					});
            	}
            }
        	
            return this;    
        },
		onChange: function (event) {
			var data, options = this.model.get("options"), value;
			if ( event.target.checked ) {
				this.$el.find("input[type='checkbox']")[1].checked = false;
				data = this.$el.find("input[type='checkbox']").eq(0).data();
                options[0].selected = true;
                options[1].selected = false;
			} else {
				this.$el.find("input[type='checkbox']")[1].checked = true;
				data = this.$el.find("input[type='checkbox']").eq(1).data();
                options[0].selected = true;
                options[1].selected = false;
			}
			this.previousValue = this.model.get("value");
			this.model.attributes.options = options;
			this.model.attributes.data = data;
			this.model.attributes.value = data["value"];
			this.model.attributes.labelsSelected = data["label"];
			this.$el.find("input[type='hidden']")[0].value = data["label"];
			this.validate();
			this.checkBinding();
		},
		getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-checkbox-list");
        },
        setValueHideControl : function () {
        	var control;
        	control = this.$el.find("input[type='hidden']");
			if (this.model.get("dataType") === "boolean"){
				$(control).val(this.model.get("data")["label"].toString());
			} else {
				$(control).val(JSON.stringify(this.model.get("data")["label"]));
			}
        	return this;
        }
	});

	PMDynaform.extendNamespace("PMDynaform.view.CheckBox",CheckBoxView);
}());

(function(){

    var SuggestView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-text").html()),
        templateList: _.template($("#tpl-suggest-list").html()),
        //templateElement: _.template($("#tpl-suggest-element").html()),        
        validator: null,
        elements: [],
        input: null,
        containerList: null,
        makeFlag: false,
        keyPressed: false,
        pointerItem: 0,
        orientation: "under",
        stackItems: [],
        stackRow: 0,
        clicked: false,
        firstLoad : true,
        dirty : false,
        jsonData : {}, 
        previousValue : "",
        dependentFields : [],
        dependentFieldsData : [],
        events: {
                "click li": "continueDependentFields",
                "keyup input": "validate",
                "keydown input": "refreshBinding",
				"change input" : "onChange"
        },
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        initialize: function () {
            var that = this;
            //this.model.on("change:value", this.onChange, this);
            this.model.on("change:value", this.checkBinding, this);
            this.containerList = $(this.templateList());
            this.enableKeyUpEvent();
        },
        refreshBinding: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.keyPressed = true;
            return this;
        },
        checkBinding: function (event) {
            //If the key is not pressed, executes the render method
            var form = this.model.get("form");
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), this.previousValue);
            }

            if ( form && form.onChangeCallback ) {
                form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
            }
            if ((this.keyPressed === false) &&
                (this.clicked === false)) {
                this.updateValueInput();   
            }
            this.onChange(event);

        },
        updateValueInput : function () {
            var textInput, hiddenInput;
            textInput = this.$el.find("input[type='suggest']");
            hiddenInput = this.$el.find("input[type='hidden']");
            if (this.model.get("data")) {
                textInput.val(this.model.get("data")["label"]);
                hiddenInput.val(this.model.get("data")["value"]);
            }
            return this;
        },
        setValueDefault: function(){
            this.model.set("value",$(this.el).find(":input").val()); 
        },
        hideSuggest : function (){
            this.containerList.hide();
            this.stackRow = 0;
             this.containerList.empty();
        },
        showSuggest : function (){
             this.containerList.empty();
            this.containerList.show();
        },
        _calculatePosition : function () {
            var element, position, leftListElement, topListElement, fullHeight;
            element = this.$el.find("input[type='suggest']");
            if (element[0].getBoundingClientRect){
                position = element[0]? element[0].getBoundingClientRect() : {};
                if (position["top"] !== undefined) {
					document.body.appendChild(this.containerList[0]);
					leftListElement = position.left;
                }
                fullHeight =  position.top + $(element).outerHeight() + this.containerList.outerHeight();
                if (fullHeight > $(window).outerHeight() ) {
					topListElement = position.top +  this._getScrollOffsets() - this.containerList.outerHeight();
                }else{
					topListElement = position.top +  this._getScrollOffsets() + element.outerHeight();
                }
                this.containerList.css({
                    position : "absolute",
                    width : element.outerWidth(),
                    "min-width" : 100,
                    left : leftListElement,
                    top : topListElement 
                });
            }
        },
        _getScrollOffsets : function () {
            return document.body.scrollTop || document.documentElement.scrollTop || window.pageYOffset || getScrollTop();
        },
        _attachSuggestGlobalEvents: function() {
          if (this.containerList) {
             $(document).on("click."+this.$el, $.proxy(this.hideSuggest, this)); 
          }
        },
        _detachSuggestGlobalEvents: function() {
          if (!this.containerList) {
             $(document).off("click."+this.$el); 
          }
        },
        onChange: function (event)  {
            var i, 
            j, 
            item, 
            dependents, 
            viewItems, 
            valueSelected,
            hidden,
            nameField,
            fieldDependentName, sql;
            this.previousValue = this.model.get("value");
            if ( !this.firstLoad ) {
                hidden = this.$el.find("input[type='hidden']");
                if (hidden.length){
                    valueSelected = this.model.get("value");
                    hidden.val(valueSelected||"");
                    this.model.set("keyLabel",this.$el.find("input[type='suggest']").val());
                }
                for ( i = 0 ; i < this.model.get("dependents").length ; i+=1 ) {
                    this.model.get("dependents")[i].get("view").firstLoad = false;
                    this.model.get("dependents")[i].get("view").onDependentHandler();
                }
                this.clicked = false;
            }
            return this;
        },
        /*createDependencies : function () {
            var i, 
            j, 
            item, 
            dependents, 
            viewItems;
            dependents = this.model.get("dependentFields") ? this.model.get("dependentFields"): [];
            viewItems = this.parent.items.asArray();            
            if (dependents.length > 0) {
                for (i = 0; i < viewItems.length; i+=1) {
                    for (j = 0; j < dependents.length; j+=1) {
                        item = viewItems[i].model.get("name");
                        if (dependents[j] === item) {
                            if (viewItems[i].model.setDependencies) {
                                viewItems[i].model.setDependencies(this);
                            }
                        }
                    }
                }
            }
            return this;
        },*/
        makeElements: function (maxItems, event){
            var that = this,
            elementTpl,
            itemLabel,
            val,
            founded = false,
            count = 0;

            this.input = this.$el.find("input[type='suggest']");
            val = this.input.val();
            this.elements = [];
            this.elements = this.model.get("options");
            this._detachSuggestGlobalEvents();
            this.showSuggest();
            this.stackItems = [];

            if (this.containerList !== null) {
                this.containerList.empty();
            }
            
            if (val !== "") {
                $.grep(that.elements, function(data, index){
                    itemLabel = data.label.toString();
                    if ( (itemLabel.toLowerCase().indexOf(val.toLowerCase()) !== -1) && count < maxItems) {

                        that.updatingItemsList(data);
                        founded = true;

                        $(that.stackItems[that.stackRow]).addClass("pmdynaform-suggest-list-keyboard");
                        count += 1;
                    }
                });
                if (!founded) {
                    that.hideSuggest();
                }else{
                    this._calculatePosition();
                }
            } else {
                this.hideSuggest();
            }
        },
        updatingItemsList: function (data) {
            var li = document.createElement("li"),
            span = document.createElement("span"),
            that = this;

            span.innerHTML = data.label;
            span.setAttribute("data-value", data.value);
            span.setAttribute("selected", false);
            li.appendChild(span);
            li.className = "list-group-item";
            /*var elementTpl = this.templateElement({
                value: data.value, 
                label:data.label,
                selected: false
            });*/
            
			$(li).click(function(e){
				that.continueDependentFields(e);
			});
            
            this.stackItems.push(li);

            this.containerList.append(li); 

            this.input.after(this.containerList);
            this.containerList.css("position", "absolute");
            this.containerList.css("zIndex", 3);
            this.containerList.css("border-radius", "5px");
            
            if (this.stackItems.length > 4) {
                this.containerList.css("height","200px");
            } else {
                this.containerList.css("height","auto");
            }

            this._attachSuggestGlobalEvents();
            //this.onChange(event);
            return this;
        },
        continueDependentFields: function (e) {
            var newValue,
            content;
            this.model.set("clickedControl",true);
            this.clicked = true;
            this.keyPressed = false;
            content = $(e.currentTarget).text();
            //newValue = $(this.el).find(":input").val();          
            $(this.el).find(":input[type='suggest']").val(content);
            this.model.attributes.data = {
                label :  content,
                value : $(e.currentTarget).find("span").data().value
            }
            this.model.set("value", $(e.currentTarget).find("span").data().value);
            this.containerList.remove();
            this.stackRow = 0;
            this.clicked = false; 
            return this;
        },
        validate: function(event){
            //this.clicked = event.type === "submit" ? false : true;
            if (event && (event.which === 9) && (event.which !==0)) { //tab key
                this.keyPressed = true;
            }
            if (!this.model.get("disabled")) {
                    this.model.attributes.value = this.$el.find("input[type='suggest']").val();
                    this.model.attributes.validator.set("valid",true);
                    this.model.validate(this.model.toJSON());
                if (this.model.get("enableValidate")) {
                    if (this.validator) {
                        this.validator.$el.remove();
                        this.$el.removeClass('has-error has-feedback');
                    }
                    if(!this.model.isValid()){
                        this.validator = new PMDynaform.view.Validator({
                            model: this.model.get("validator")
                        });
                        this.$el.find("input[type='suggest']").parent().append(this.validator.el);
                        this.applyStyleError();
                    }
                }else{
					this.model.attributes.valid = true;
				}
            }
            this.keyPressed = false;
            if (event && event.type !== "submit") {
                this.makeElements(10);
            }
            return this;
        },
        render: function() {
            var data, 
            that = this,
            hidden,
            name;
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            data = this.model.get("data");
            if (this.firstLoad){
                if ( this.model.get("value") && data) {
                    this.$el.find("input[type='suggest']").val(data["label"]? data["label"] :"");
                } else {
                    this.model.emptyValue();
                }
            }else{
                this.$el.find("input[type='suggest']").val(data["label"]? data["label"] :"");
            }
            this.setNameHiddenControl();
            this.$el.find("input[type='suggest']").focusin(function(event){
                var remoteOptions;
                that.clicked = true;
                if (that.model.get("parentDependents") && that.model.get("parentDependents").length){
                    if(that.firstLoad){
                        if (!that.dirty){
                            that.jsonData = that.generateDataDependenField();
                            remoteOptions = that.executeQuery(data);
                            that.mergeOptions(remoteOptions);
                            that.dirty = true;
                            if (data && data["value"] && data["label"].trim().length) {
                                this.value = data["label"];
                            }
                        }
                        that.firstLoad = false;
                    }
                } else {
                    that.firstLoad = false;
                }
            });
            /*if (!that.firstLoad && this.dirty){
                this.$el.find("input[type='suggest']").val(data["label"]? data["label"] :"");
            }*/
            this.$el.find("input[type='suggest']").focus();
            this.setValueToDomain();

            if (this.model.get("name").trim().length === 0){
                this.$el.find("input[type='suggest']").attr("name","");
                this.$el.find("input[type='hidden']").attr("name","");
            }
            this.tagControl = this.$el.find("input[type='hidden']");
            this.tagHiddenToLabel = this.$el.find("input[type='suggest']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            return this;
        },
        onDependentHandler: function () {
            var i, localOpt, remoteOptions;
            this.jsonData = this.generateDataDependenField();
            remoteOptions = this.executeQuery();
            this.mergeOptions(remoteOptions);
            this.firstLoad = false;
            return this;
        },
        executeQuery : function (){
            var restClient, resp, prj, endpoint, url, data = this.jsonData;
            if (this.model.get("group") === "grid"){
                data["field_id"] = this.model.get("columnName");
            }else{
                data["field_id"] = this.model.get("id");
            }
            if ( this.model.get("form") ) {
                if (this.model.get("form").model.get("form")){
                    data["dyn_uid"] = this.model.get("form").model.get("form").model.get("id");             
                }else{
                    data["dyn_uid"] = this.model.get("form").model.get("id");
                } 
            }
            prj = this.model.get("project");            
            resp = prj.webServiceManager.executeQuery(data,this.model.get("var_name") || "");
            return resp;
        },
        mergeOptions : function (remoteOptions){
            var k, remoteOpt = [], localOpt = [], options;
            for ( k = 0; k < remoteOptions.length; k+=1) {
                remoteOpt.push({
                    value : remoteOptions[k].value,
                    label : remoteOptions[k].text
                });
            }
            localOpt = this.model.get("localOptions");
            this.model.attributes.remoteOptions = remoteOpt;
            this.model.attributes.optionsSql = remoteOpt;
            options = localOpt.concat(remoteOpt);
            this.model.attributes.options = options;
            if (options.length){
                this.model.attributes.data = {
                    value : options[0]["value"],
                    label : options[0]["label"]
                }
                this.updateValueInput();
                this.model.set("value",options[0]["value"]);
            }
            return options;
        },
        toggleItemSelected: function () {
            $(this.stackItems).removeClass("pmdynaform-suggest-list-keyboard");
            $(this.stackItems[this.stackRow]).addClass("pmdynaform-suggest-list-keyboard");

            return this;
        },
        enableKeyUpEvent: function () {
            var that = this, 
            code,
            containerScroll;
            
            this.$el.keyup(function (event) {
                if (that.stackItems.length >0) {
                    code = event.which;
                    if (code === 38) { // UP
                        if (that.stackRow > 0) {
                            that.stackRow-=1;
                            that.toggleItemSelected();
                        }
                        that.containerList.scrollTop(-10*parseInt(that.stackRow+1));
                    } 
                    if (code === 40) { // DOWN
                        if (that.stackRow < that.stackItems.length-1) {
                            that.stackRow+=1;
                            that.toggleItemSelected();
                        }
                        that.containerList.scrollTop(+10*parseInt(that.stackRow+1));
                    }
                    if ((code === 13)) { //ENTER
                        that.continueDependentFields({
                            currentTarget: $("body").find(".pmdynaform-suggest-list-keyboard")[0]
                        });
                    }
                }
            });
            
        },
        updateValueControl: function () {
            var inputVal = this.$el.find("input[type='suggest']").val();

            this.model.set("value", inputVal);

            return this;    
        },
        getHTMLControl: function () {
            return this.$el.find("input[type='suggest']");
        },
        afterRender : function () {
            //this.continueDependentFields();
            return this;
        },
        getDependeciesField : function () {
            var i, items, nameField, j, sql;
            nameField = this.model.get("name");
            if (this.parent && this.parent.model.get("items").length) {
                items  = this.parent.model.get("items");
                for ( i = 0 ; i < items.length ; i+=1 ) {
                    if (items[i]){
                        for ( j = 0 ; j < items[i].length ; j+=1 ){
                            if (items[i][j].name && nameField !== items[i][j].name) {
                                sql = this.model.get("sql");
                                if (sql && 
									(
										sql.indexOf("@#"+items[i][j].name) > -1 ||
										sql.indexOf("@@"+items[i][j].name) > -1 ||
										sql.indexOf("@%"+items[i][j].name) > -1 ||
										sql.indexOf("@="+items[i][j].name) > -1 ||
										sql.indexOf("@?"+items[i][j].name) > -1 ||
										sql.indexOf("@$"+items[i][j].name) > -1 
									)
								) {
                                    if(this.dependentFields.indexOf(items[i][j].name) === -1) {
                                        this.dependentFields.push(items[i][j].name);
                                        this.dependentFieldsData.push(items[i][j]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        getDependeciesFieldGrid : function () {
            var i, items, nameField, j, sql;
            nameField = this.model.get("columnName");
            if (this.parent && this.parent.items.asArray().length) {
                items  = this.parent.items.asArray()
                for ( i = 0 ; i < items.length ; i+=1 ) {
                    if (items[i]){
                        if (items[i].model.get("name") && nameField !== items[i].model.get("name")) {
                            sql = this.model.get("sql");
                            if (sql && (
									sql.indexOf("@#" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@@" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@%" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@=" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@?" + items[i].model.get("columnName")) > -1 ||
									sql.indexOf("@$" + items[i].model.get("columnName")) > -1 
								)
							) {
                                if(this.dependentFields.indexOf(items[i].model.get("columnName")) === -1) {
                                    this.dependentFields.push(items[i].model.get("columnName"));
                                    this.dependentFieldsData.push(items[i]);
                                }
                            }
                        }
                    }
                }
            }
        },
        generateDataDependenField : function () {
            var i, parentDependents, data = {}, name;
            parentDependents = this.model.get("parentDependents"); 
            for ( i = 0 ; i < parentDependents.length ; i+=1 ) {
                if ( parentDependents[i].get("group") === "grid" ) {
                    name = parentDependents[i].get("columnName");
                }else{
                    name = parentDependents[i].get("id");
                }
                if (parentDependents[i].get("type") === "text") {
                    data[name] = parentDependents[i].get("view").fiendValueDependenField(parentDependents[i].get("value"));
                }else{
                    data[name] = parentDependents[i].get("value");
                }
            }
            return data;
        },
        setNameHiddenControl : function (){
            var hidden;
            if(this.el){
                if (this.model.get("group") === "grid") {
                    hidden = this.$el.find("input[type = 'hidden']")[0];
                    name = this.model.get("name");
                    name = name.substring(0,name.length-1).concat("_label]");
                    this.$el.find("input[type='suggest']")[0].name = "form" + name;
                    this.$el.find("input[type='suggest']")[0].id = "form" + name;
                }else{
                    this.$el.find("input[type='suggest']")[0].name = "form[" + this.model.get("name")+"_label]";
                }
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Suggest",SuggestView);
}());
(function () {
    var LinkView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-link").html()),
        validator: null,
        initialize: function () {
            var that = this;
            this.model.on("change", this.render, this);
            if (this.model.get("href") === "") {
                this.model.set("href", this.model.get("defaultValue"));
                this.model.set("value", this.model.get("defaultValue"));
            }
        },
        render : function (){
            this.$el.html( this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.tagControl = this.tagHiddenToLabel = this.$el.find(".pmdynaform-control-link span");
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Link", LinkView);
}());

(function(){
    
    var Label = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-label").html()),
        validator: null,
        singleControl : [],
        fieldValid : [],
		formulaFieldsAssociated: [],
        initialize: function (){
            this.model.on("change", this.render, this);
            this.optionsControl = ["dropdown", "checkgroup","radio","suggest", "checkbox"]
        },
		render: function() {
			var hidden, name;
			this.$el.html( this.template(this.model.toJSON()));
			if (this.model.get("hint") !== "") {
				this.enableTooltip();
			}
            if (this.model.get("originalType") === "datetime"){
                if (this.model.get("group") !== "grid") {
                	if (this.model.get("keyLabel")){
                		if(this.model.get("required")){
                			this.$el.find("span:eq(2)").text(this.model.get("keyLabel"));                			
                		}else{
							this.$el.find("span:eq(1)").text(this.model.get("keyLabel"));
                		}
                	}else{
                		if(this.model.get("required")){
							this.$el.find("span:eq(2)").text(this.model.get("data")["label"]);                			
                		}else{
							this.$el.find("span:eq(1)").text(this.model.get("data")["label"]);
                		}
                	}
                }else{
                	if (this.model.get("keyLabel")){
                    	this.$el.find("span:eq(0)").text(this.model.get("keyLabel"));                		
                	} else {
                    	this.$el.find("span:eq(0)").text(this.model.get("data")["label"]);
                	}             	
                }
			}
			this.setDataInHiddenControls(this.model.get("originalType"));
			if (this.model.get("group") === "grid") {
				hidden = this.$el.find("input[type = 'hidden']")[0];
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("]");	
				hidden.name = hidden.id = "form" + name;
				
				hidden.name = hidden.id = "form" + name;
				hidden = this.$el.find("input[type = 'hidden']")[1];
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("_label]");
				hidden.name = hidden.id = "form" + name;
			}

			this.tagControl = this.$el.find("input[type='hidden']").eq(0);
			this.keyLabelControl = this.$el.find("input[type='hidden']").eq(1);
			return this;
		},
		setDataInHiddenControls : function (type) {
			if (type && type && type.trim().length) {
				if ( this.optionsControl.indexOf(type) !== -1 ) {
					if (type === "suggest") {
						this.$el.find("input[type='hidden']")[0].value = this.model.get("data")["value"];
						this.$el.find("input[type='hidden']")[1].value = this.model.get("data")["label"];
					}else{
						this.$el.find("input[type='hidden']")[0].value = this.model.get("data")["value"];
					}
					if (type === "checkgroup") {
						this.$el.find("input[type='hidden']")[1].value = JSON.stringify(JSON.parse(this.model.get("data")["label"]));
					}
					if (type === "dropdown" || type === "radio") {
						this.$el.find("input[type='hidden']")[1].value = this.model.get("data")["label"];
					}
				}else{
					if(type === "datetime") {
						this.$el.find("input[type='hidden']")[0].value = this.model.get("data")["value"];
						this.$el.find("input[type='hidden']")[1].value = this.model.get("data")["label"];
					}
				}
			}
			return this;
		},
		onFieldAssociatedHandler: function() {
			var i,
			fieldsAssoc = this.formulaFieldsAssociated;
			if (fieldsAssoc.length > 0) {
				for (i=0; i<fieldsAssoc.length; i+=1) {
					if (fieldsAssoc[i].model.get("formulator") instanceof PMDynaform.core.Formula) {
						this.model.addFormulaTokenAssociated(fieldsAssoc[i].model.get("formulator"));
						this.model.updateFormulaValueAssociated(fieldsAssoc[i]);
					}
				}
			}
			return this;
		},
		setOnChangeCallbackOperation: function (fn) {
			if (typeof fn === "function") {
				this.onChangeCallbackOperation = fn;
			}
			return this;
		},
		on: function (e, fn) {
			var that = this, 
			control,
			localEvents = {
				"changeValues": "setOnChangeCallbackOperation"
			};
			if (localEvents[e]) {
				this[localEvents[e]](fn);
			} else {
				control = this.$el.find("input");
				if (control) {
					control.on(e, function(event){
						fn(event, that);
						event.stopPropagation();
					});
				} else {
					throw new Error ("Is not possible find the HTMLElement associated to field");
				} 
			}
			return this;
		},
		onFormula: function(rows) {
			var fieldsList,
			that = this,
			allFields,
			allFieldsView,
			index,
			formulaField,
			idFields = {},
			fieldFormula,
			fieldValid,
			resultField,
			fieldAdded = [],
			fieldSelected,
			obj,
			i;
			if (this.model.get("group") == "grid") {
				fieldsList = rows;
			} else {
				fieldsList = this.parent.items;
			}
			//All Fields from the FORM
			allFieldsView = (fieldsList instanceof Array)? fieldsList: fieldsList.asArray();

			for (index = 0 ; index < allFieldsView.length ; index+=1 ) {
				if (allFieldsView[index] instanceof PMDynaform.view.Text) {
					idFields[allFieldsView[index].model.get("id")] = allFieldsView[index];
				}
				if (allFieldsView[index] instanceof PMDynaform.view.Label){
					idFields[allFieldsView[index].model.get("id")] = allFieldsView[index];	
				}
			}

			fieldSelected = {};
			//Fields from the Formula PROPERTY
			formulaField = this.model.get("formula");
			fieldFormula = formulaField.split(/[\-(,|+*/\)]+/);
			if (this.model.get("group") == "grid") {
				for (var k = 0 ; k < rows.length ; k+=1){
					if (fieldFormula.indexOf(rows[k].model.get("id")) > -1){
						rows[k].onFieldAssociatedHandler();
					}
				}
			}
			this.fieldValid = fieldFormula.filter(function existElement(element) {
				var result = false;
				if ((idFields[element] !== undefined) && ($.inArray(element, fieldAdded) === -1)) {
					fieldAdded.push(element);
					result = true
				}
				return result;
			});
			//Insert the Formula object to fields selected
			for (var obj = 0 ; obj < this.fieldValid.length ; obj+=1 ) {
				this.model.addFormulaFieldName(this.fieldValid[obj]);
				idFields[this.fieldValid[obj]].formulaFieldsAssociated.push(that);
				if (this.model.get("group") == "grid" ){
					if(idFields.hasOwnProperty(this.fieldValid[obj])){
						this.model.attributes.formulaAssociatedObject.push(idFields[this.fieldValid[obj]]);
					}
				}
			}
			return this;
		}
    });

    PMDynaform.extendNamespace("PMDynaform.view.Label", Label);
}());

(function(){
    
    var Title = PMDynaform.view.Field.extend({
        template: null,
        validator: null,
        etiquete: {
            title: _.template($("#tpl-label-title").html()),
            subtitle: _.template($("#tpl-label-subtitle").html())
        },
        initialize: function (){
            var type = this.model.get("type");
            this.template = this.etiquete[type];
            
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            if (this.model.get("type") === "title"){
                this.tagControl = this.tagHiddenToLabel = this.$el.find("h4").find("span[class='textlabel']");
            }else{
                this.tagControl = this.tagHiddenToLabel = this.$el.find("h5").find("span[class='textlabel']");
            }
            return this;
        }
    });
    
    PMDynaform.extendNamespace("PMDynaform.view.Title", Title);
}());

(function(){
	var Empty = Backbone.View.extend({
		item: null,
		template: _.template( $("#tpl-empty").html()),
		render: function() {
			this.$el.html( this.template(this.model.toJSON()) );
			return this;
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.Empty",Empty);
	
}());

(function(){
	var HiddenModel = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-hidden").html()),
		render: function(isConsole) {
			var data = {}, hidden;
			if ( isConsole ) {
				data["value"] = this.model.get("value");
				data["label"] = this.model.get("value");
				this.model.attributes.data = data;
			}
			this.$el.html( this.template(this.model.toJSON()) );
			if (this.model.get("group") === "grid") {
				hidden = this.$el.find("input[type = 'hidden']")[1];
				name = this.model.get("name");
				name = name.substring(0,name.length-1).concat("_label]");
				hidden.name = hidden.id = "form" + name;
				hidden.value = this.model.get("value"); 
			}
			if (this.model.get("name").trim().length === 0){
				this.$el.find("input[type='hidden']").attr("name","");
			}
			this.tagControl = this.$el.find("input[type='hidden']").eq(0);
			this.keyLabelControl = this.$el.find("input[type='hidden']").eq(1);
 			this.tagHiddenToLabel = this.$el.find("input[type='hidden']").eq(1);
			return this;
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.Hidden", HiddenModel);
	
}());

(function(){
	var ImageView = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-image").html()),
		events: {
	        "keydown": "preventEvents"
	    },
		initialize: function (){
			this.model.on("change", this.render, this);
		},
		preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
		render: function() {
			this.$el.html( this.template(this.model.toJSON()) );
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.tagControl = this.tagHiddenToLabel = this.$el.find("img");
			return this;
		},
		getSrc : function (){
			return this.model.get("src");
		},
		setSrc : function (value){
			this.model.set("src", value);
			this.tagControl.attr("src",value);
			return this;
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.Image", ImageView);
	
}());

(function(){
	var SubFormView = Backbone.View.extend({
        template:_.template($('#tpl-form').html()),
        formView: null,
        defaultElement: "empty",
        availableElements: null,
        parent: null,
		initialize: function (options) {
			var availableElements = [
                "text",
                "textarea",
                "checkbox",
				"checkgroup",
                "radio",
                "dropdown",
                "button",
                "datetime",
                "fieldset",
                "suggest",
                "link",
                "hidden",
                "title",
                "subtitle",
                "label",
                "empty",
                "file",
                "image",
                "grid",
                "panel"
            ];
            this.availableElements = availableElements;
            if(options.project) {
                this.project = options.project;
            }
            this.checkItems();
			this.makeSubForm();

		},
        checkItems: function () {
            var i,
            j,
            newItems = [],
            row = [],
            json = this.model.toJSON();

            if (json.items) {
                for (i=0; i<json.items.length; i+=1) {
                    row = [];
                    for(j=0; j<json.items[i].length; j+=1){
                        if ($.inArray(json.items[i][j].type, this.availableElements) >=0) {
                            row.push(json.items[i][j]);
                        } else {
                            json.items[i][j].type = this.defaultElement;
                            row.push(json.items[i][j]);
                        }
                    }
                    if (row.length > 0) {
                        newItems.push(row);
                    }
                }
            }

            json.items = newItems;
            this.model.set("modelForm", json);
            
            return this;
        },
		makeSubForm: function() {
            var panelmodel = new PMDynaform.model.FormPanel(this.model.get("modelForm"));

            this.formView = new PMDynaform.view.FormPanel({
                model: panelmodel, 
                project: this.project
            });

            return this;
        },
        validate: function (event) {
            this.isValid(event);
        },
        getItems: function () {
            return this.formView.items.asArray();;
        },
        isValid: function (event) {
            var i, formValid = true,
            itemsField = this.formView.items.asArray(), itemField;

            if (itemsField.length > 0) {
                for (i = 0; i < itemsField.length; i+=1) {
                    if(itemsField[i].validate) {
						if(event){
							itemsField[i].validate(event);
							if (!itemsField[i].model.get("valid")) {
								formValid = itemsField[i].model.get("valid");
								if(itemField === undefined){
									itemField = itemsField[i];
								}
							}
						}else{
							itemsField[i].validate();
							formValid = itemsField[i].model.get("valid");
							if (!formValid){
								if (itemsField[i].model.get("type")!=="grid"){
									itemsField[i].setFocus();
								}
								this.model.attributes.valid = false;
								return false;
							} 
						}
                    }
                }
            }
			if (itemField && itemField.model.get("type")!=="grid"){
				itemField.setFocus();
			}
            this.model.set("valid", formValid );

            return formValid;
        },
        setData: function (data) {
            //using the same method of PMDynaform.view.FormPanel
            this.formView.setData(data);

            return this;
        },
        getData: function () {
            var i,
            k,
            field,
            fields,
            panels,
            formData;
            
            formData = this.model.getData();

                fields = this.formView.items.asArray();
                for (k=0; k<fields.length; k+=1) {
                    if ((typeof fields[k].getData === "function") &&
                        (fields[k] instanceof PMDynaform.view.Field)) {
                        //formData.fields.push(fields[k].getData());
                        field = fields[k].getData();
                        formData.variables[field.name] = field.value;
                    }
                }
            
            return formData;
        },
        render: function () {
            this.$el.html( this.template(this.model.toJSON()) );
            this.$el.find(".pmdynaform-field-form").append(this.formView.render(true).el);

            return this;
        }
	});
	
	//.pmdynaform-formcontainer
	PMDynaform.extendNamespace("PMDynaform.view.SubForm", SubFormView);
	
}());

(function(){
    
    var GeoMapView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-map").html()),
        validator: null,
        events: {
            "click .pmdynaform-map-fullscreen button": "applyFullScreen"
        },
        initialize: function (attributes){
            var that = this;
            //this.model.on("change", this.render, this);
            
        },
        onLoadGeoLocation: function () {
            if (this.model.get("currentLocation") && this.model.get("supportNavigator")) {
                this.geoLocation();
            } else {
                this.onLoadLocation();
            }
            return this;
        },
        geoLocation: function () {
            var that = this;

            navigator.geolocation.getCurrentPosition(function(position){
                that.model.set("latitude", position.coords.latitude);
                that.model.set("longitude", position.coords.longitude);
                that.onLoadLocation();
            });

            return this;
        },
        onLoadLocation: function () {
            var that = this,
            coords, 
            mapOptions,
            map,
            marker,
            appData,
            remoteCoors = {},
            canvasHTML = that.$el.find(".pmdynaform-map-canvas")[0];
            appData = this.project.mobileDataControls.data; 
            if (appData && appData[this.model.get("name")]) {
                remoteCoors.altitude = appData[this.model.get("name")]["altitude"];
                remoteCoors.latitude = appData[this.model.get("name")]["latitude"];
                remoteCoors.longitude = appData[this.model.get("name")]["longitude"];
                coords = new google.maps.LatLng(remoteCoors.latitude,  remoteCoors.longitude);
            } else {
                coords = new google.maps.LatLng(this.model.get("latitude"), this.model.get("longitude"));
            }
            mapOptions = {
                zoom: this.model.get("zoom"),
                center: coords,
                panControl: this.model.get("panControl"),
                zoomControl: this.model.get("zoomControl"),
                scaleControl: this.model.get("scaleControl"),
                streetViewControl: this.model.get("streetViewControl"),
                overviewMapControl: this.model.get("overviewMapControl"),
                mapTypeControl: this.model.get("mapTypeControl"),
                navigationControlOptions: {
                    style: google.maps.NavigationControlStyle.SMALL
                },
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(canvasHTML, mapOptions);
            this.model.set("googlemap", map);
            
            marker = new google.maps.Marker({
              position: coords,
              map: map,
              draggable: this.model.get("dragMarker"),
              title: ""
            });
            google.maps.event.addListener(marker, 'dragend', function(event) {
                that.model.set("latitude", event.latLng.lat().toFixed(that.model.get("decimals")));
                that.model.set("longitude", event.latLng.lng().toFixed(that.model.get("decimals")));
                
            });
            this.model.set("marker", marker);
            //this.rightToLeftLabels();
            
            return this;
        },
        applyFullScreen: function () {
        	
            if (this.fullscreen.supported) {
            	this.fullscreen.toggle();
            } else {
            	this.$el(".pmdynaform-map-fullscreen").hide();
            }
            
            return this;
        },
        render: function () {
        	var that = this,
        	canvasMap;

        	that.$el.html( that.template( that.model.toJSON()) );
        	canvasMap = that.$el.find(".pmdynaform-map-canvas");
            this.onLoadGeoLocation();        		
            if (this.model.get("fullscreen")) {
            	this.fullscreen = new PMDynaform.core.FullScreen({
                element: this.$el.find(".pmdynaform-map-canvas")[0],
                onReadyScreen: function() {
		            setTimeout(function() {
		                that.$el.find(".pmdynaform-map-canvas").css("height", $(window).height() + "px");
		            }, 500);
		        },
		        onCancelScreen: function() {
		            setTimeout(function() {
		                that.$el.find(".pmdynaform-map-canvas").css("height", "");
		            }, 500);
		        }
            });
            }

			return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.GeoMap", GeoMapView);
}());

(function(){
    var Annotation = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-annotation").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            this.tagControl = this.tagHiddenToLabel = this.$el.find("span span");
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Annotation", Annotation);
}());

/**
 * The Datetime class was developed with the help of DateBootstrap plugin	
 */
(function(){
	var DatetimeView = PMDynaform.view.Field.extend({
		template : _.template($("#tpl-datetime2").html()),
		validator : null,
		keyPressed: false,
		previousValue : null,
		events: {
                "blur input": "validate",
                "keydown input": "refreshBinding"                
        },
        outFocus : false,
		initialize: function () {
            var that = this;
            this.model.on("change:value", this.checkBinding, this);
        },
        checkBinding: function () {
        	var form = this.model.get("form"), data = {};
            if ( typeof this.onChangeCallback === 'function' ) {
                this.onChangeCallback(this.getValue(), this.previousValue);
            }

            if ( form && form.onChangeCallback ) {
                form.onChangeCallback(this.model.get("id"), this.model.get("value"), this.previousValue);
            }

            //If the key is not pressed, executes the render method
            /*if (!this.keyPressed) {
                this.render();
            }*/
        },
        onChangeCallback: function (){},
        setOnChange : function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
	    validate: function(event){
	    	var data = {};
			if (event && event.type == "focusout") {
				this.outFocus = true;
			}else{
				this.outFocus = false;
			}
	    	this.previousValue = this.model.get("value");
	    	if (event) {
	    		if ((event.which === 9) && (event.which !==0)) { //tab key
	                this.keyPressed = true;
	            }
	    	}
	    	
	    	if(!this.model.get("disabled")) {
	    		this.model.set({value: this.formatData(this.$el.find("input"))}, {validate: true});
	    		this.model.attributes.data = {
	    			value : this.model.get("value"),
	    			label : this.$el.find("input").val()
	    		};
	    		this.model.set("keyLabel", this.$el.find("input").val());
				if (this.model.get("enableValidate")) {
		            if (this.validator) {
		                this.validator.$el.remove();
		                this.$el.removeClass('has-error');
		            }
		            if(!this.model.isValid()){
		                this.validator = new PMDynaform.view.Validator({
		                    model: this.model.get("validator")
		                });  
		                //this.$el.find(".input-group")[0].insertBefore( this.validator.el ,this.$el.find(".input-group-addon")[0]);
		                //this.$el.find(".input-group").parent().append(this.validator.el);
		                //this.$el.find(".pmdynaform-field-control").append(this.validator.el);
		                this.$el.find(".datetime-container").append(this.validator.el)
		                this.applyStyleError();
		            }
		        }else{
					this.model.attributes.valid = true;
				}
	    	}else{
				this.model.isValid();
	    	}
			if (this.model.get("name").trim().length){
				if (this.model.get("value").trim().length){
					this.$el.find("input[type='hidden']").val(this.model.get("value"));
				}
			}
            return this;
        },
        refreshBinding: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.keyPressed = true;
            return this;
        },
		render : function (isConsole){
			var data = {}, date, that = this, clickEvent, hidden, name, dateInput;
			if (!isConsole){
	            this.$el.html( this.template(this.model.toJSON()) );
	            if (this.model.get("hint") !== "") {
	                this.enableTooltip();
	            }
	            if (!this.outFocus){
					this.$el.find('#datetime-container-control').datetimepicker({
				            format  : this.model.get("format"),
				            stepping  : this.model.get("stepping"),
				            useCurrent  : this.model.get("useCurrent"),
				            collapse  : this.model.get("collapse"),
				            defaultDate  : this.model.get("defaultDate"),
				            disabledDates  : this.model.get("disabledDates"),
				            sideBySide  : this.model.get("sideBySide"),
				            daysOfWeekDisabled  : this.model.get("daysOfWeekDisabled"),
				            calendarWeeks  : this.model.get("calendarWeeks"),
				            viewMode  : this.model.get("viewMode"),
				            toolbarPlacement  : this.model.get("toolbarPlacement"),
				            showClear  : this.model.get("showClear"),
				            widgetPositioning  : this.model.get("widgetPositioning"),
				            date : this.model.get("value"),
				            showTodayButton  : true

					});
                                        try {
                                            this.$el.find('#datetime-container-control').data().DateTimePicker.minDate(this.model.get("minDate"));
                                        } catch (e) {
                                        }
                                        try {
                                            this.$el.find('#datetime-container-control').data().DateTimePicker.maxDate(this.model.get("maxDate"));
                                        } catch (e) {
                                        }
                                        
                        
	            } else { 
					this.$el.find('#datetime-container-control').datetimepicker({
				            format  : this.model.get("format"),
				            stepping  : this.model.get("stepping"),
				            useCurrent  : this.model.get("useCurrent"),
				            collapse  : this.model.get("collapse"),
				            defaultDate  : this.model.get("defaultDate"),
				            disabledDates  : this.model.get("disabledDates"),
				            sideBySide  : this.model.get("sideBySide"),
				            daysOfWeekDisabled  : this.model.get("daysOfWeekDisabled"),
				            calendarWeeks  : this.model.get("calendarWeeks"),
				            viewMode  : this.model.get("viewMode"),
				            toolbarPlacement  : this.model.get("toolbarPlacement"),
				            showClear  : this.model.get("showClear"),
				            widgetPositioning  : this.model.get("widgetPositioning"),
                                                        showTodayButton  : true
					});
                                        try {
                                            this.$el.find('#datetime-container-control').data().DateTimePicker.minDate(this.model.get("minDate"));
                                        } catch (e) {
                                        }
                                        try {
                                            this.$el.find('#datetime-container-control').data().DateTimePicker.maxDate(this.model.get("maxDate"));
                                        } catch (e) {
                                        }
	            }
	            
	            this.$el.find('#datetime-container-control').click(function () {
	                if($(this).find(".bootstrap-datetimepicker-widget").is(":visible")) {
    				    var width = $( window ).width();
    				    if (width > 550) {
    					    var w = $(this).width() - $(this).find(".bootstrap-datetimepicker-widget").width() - 8;
    					    $(this).find(".bootstrap-datetimepicker-widget").css({"left":parseInt(that.$el.find("#datetime-container-control")[0].getBoundingClientRect().left) + w});
    					}
    				}
                });

	            //this.model.attributes.value = this.model.get("data")["value"];
				if (this.model.get("group") === "grid") {
					dateInput = this.$el.find("input[type = 'text']")[0];
					name = this.model.get("name");
					name = name.substring(0,name.length-1).concat("_label]");
					dateInput.name = dateInput.id = "form" + name;
				}
				/*if ( this.model.get("value").trim().length ) {
					data["value"] = this.formatData( this.model.get("value"));
					data["label"] = this.formatData( this.model.get("value"));
					this.model.attributes.data = data;
				}*/
				if (this.model.get("value").trim().length){
					this.$el.find("input[type='hidden']").val(this.model.get("data").value);
				}
            }
			if (this.model.get("name").trim().length === 0){
				this.$el.find("input[type='text']").attr("name","");
				this.$el.find("input[type='hidden']").attr("name","");
			}
			this.tagControl = this.$el.find("input[type='text']");
			this.keyLabelControl = this.$el.find("input[type='hidden']");
			this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
			return this;
		},
		formatData : function(date){
			var valueDB, data, formatAux;
			data = this.$el.find('#datetime-container-control').data();
			data.DateTimePicker.format("YYYY-MM-DD HH:mm:ss");
			valueDB = data.date;
                        if (!valueDB) {
                            valueDB = "";
                        }
			data.DateTimePicker.format(this.model.get("format"));
			return valueDB;
		}
	})
	PMDynaform.extendNamespace("PMDynaform.view.Datetime",DatetimeView);
}());

(function(){
    var Audio_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Audio_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Audio_mobile", Audio_mobile);
}());

(function(){
    var Geomap_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Geomap_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Geomap_mobile", Geomap_mobile);
}());

(function(){
    var Image_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Image_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Image_mobile", Image_mobile);
}());

(function(){
    var Qrcode_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Qrcode_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Qrcode_mobile", Qrcode_mobile);
}());

(function(){
    var Signature_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Signature_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Signature_mobile", Signature_mobile);
}());

(function(){
    var Video_mobile = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-Video_mobile").html()),
        initialize: function (){
            this.model.on("change", this.render, this);
        },
        render: function() {
            this.$el.html( this.template(this.model.toJSON()) );
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Video_mobile", Video_mobile);
}());

(function(){
    var PanelField = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-panelField").html()),
        initialize: function (){
        },
        render: function() {
            var content, footer;
            this.$el.html( this.template(this.model.toJSON()) );
            this.$el.find(".panel-body").html(this.model.get("content"));
            footer = $(this.model.get("footerContent"));
            if ( footer.length && footer instanceof jQuery){
                this.$el.find(".panel-footer").append(footer);
            } else {
                this.$el.find(".panel-footer").text(this.model.get("footerContent"));
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.PanelField", PanelField);
}());

(function(){
	var FileMobile = PMDynaform.view.Field.extend({
		template: _.template( $("#tpl-extfile").html()),		
		templateAudio: _.template( $("#tpl-extaudio").html()),
		templateVideo: _.template( $("#tpl-extvideo").html()),
		templateMediaVideo: _.template( $("#tpl-media-video").html()),		
		templateMediaAudio: _.template( $("#tpl-media-audio").html()),		
		templateImage: _.template( $("#tpl-extfile").html()),
		templatePlusImage: _.template( $("#tpl-extfile-plus-image").html()),
		templatePlusAudio: _.template( $("#tpl-extfile-plus-audio").html()),
		templatePlusVideo: _.template( $("#tpl-extfile-plus-video").html()),
		boxPlus:null,
		viewsFiles:[],
		mediaVideos:[],
		events: {
			"click buttonImage": "onClickButtonMobile",
	        "click .pmdynaform-file-container .form-control": "onClickButton",
	        "click div[name='button-all'] .pmdynaform-file-buttonup": "onUploadAll",
	        "click div[name='button-all'] .pmdynaform-file-buttoncancel": "onCancelAll",
	        "click div[name='button-all'] .pmdynaform-file-buttonremove": "onRemoveAll"
	    },
		initialize: function () {
			//this.setOnChangeFiles();
			//this.attributes.files= [];
			this.model.on("change", this.render, this);
		},
		onClickButtonMobile: function (event) {			
			var model;
			model = this.model;			
			switch (model.get("type")) {
			    case "imageMobile":
			        this.onClickButtonImage(event);
			        break; 
			    case "audioMobile":
			        this.onClickButtonAudio(event);
			        break;
			    case "videoMobile":
			        this.onClickButtonVideo(event);
			        break;
			    default: 
			        this.$el.find("input").trigger( "click" );
			}
			event.preventDefault();
			event.stopPropagation();			
			return this;
		},
		
		onClickButtonImage: function (event) {		
			var respData; 
			respData = {
				idField: this.model.get("name"),
				type:"image"					
			};			
			if(navigator.userAgent == "formslider-android"){				
				JsInterface.getPicture(JSON.stringify(respData));				
			}			
			if(navigator.userAgent == "formslider-ios"){
				this.model.attributes.project.setMemoryStack({"data":respData});
				this.model.attributes.project.projectFlow.executeFakeIOS("upload-file");
			}					
			return this;
		},	
		onClickButtonAudio: function (event) {		
			var respData; 
			respData = {
				idField: this.model.get("name"),
				type:"audio"					
			};			
			if(navigator.userAgent == "formslider-android"){
				JsInterface.getAudio(JSON.stringify(respData));				
			}			
			if(navigator.userAgent == "formslider-ios"){
				this.model.attributes.project.setMemoryStack({"data":respData});
				this.model.attributes.project.projectFlow.executeFakeIOS("upload-file");
			}						
			return this;
		},
		onClickButtonVideo: function (event) {		
			var respData; 
			respData = {
				idField: this.model.get("name"),
				type:"video"					
			};			
			if(navigator.userAgent == "formslider-android"){
				JsInterface.getVideo(JSON.stringify(respData));				
			}			
			if(navigator.userAgent == "formslider-ios"){
				this.model.attributes.project.setMemoryStack({"data":respData});
				this.model.attributes.project.projectFlow.executeFakeIOS("upload-file");
			}						
			return this;
		},		
		validate: function(e, file) {
			var validated = true;
			//extensions = this.model.get("extensions"),
			//maxSize = this.model.get("size");
			
			//Check the extension of the file
			/*if(extensions.indexOf("*") < 0) {
				type = file.extra.extension.toLowerCase().trim();
				if (extensions.indexOf(type) < 0) {
					alert("The extension of the file is not supported for the field...");
					validated = false;
				}
			}*/
			
			// check file size
			/*if ((parseInt(file.size / 1024) > parseInt(maxSize * 1024)) && validated === true) {
				alert("File \""+file.name+"\" is too big. \n Max allowed size is "+ maxSize +" MB.");
				validated = false;
			}*/

			return validated;			
		},
		removeItem: function (event) {
			var items = this.model.get("items"),
			index = $(event.target).data("index");

			items.splice(index, 1);
			this.model.set("items", items);
			this.render();
			return this;
		},		
		onClosePreview: function () {

			return this;
		},
		onPreviewItem: function (event) {
			var file,
			reader,
			shadow = document.createElement("div"),
			background = document.createElement("div"),
			preview = document.createElement("img"),
			index = $(event.target).data("index"),
			closeItem = document.createElement("span");
			closeItem.className = "glyphicon glyphicon-remove";
			closeItem.title = "close";
			$(closeItem).tooltip().click(function(e) {
                $(this).tooltip('toggle');
            });
			heightContainer = document.documentElement.clientHeight;

			shadow.className = "pmdynaform-file-shadow";
			shadow.style.height = heightContainer + "px";
			background.className = "pmdynaform-file-preview-image"
			background.style.height = heightContainer + "px";
			background.appendChild(closeItem);
			$(background).on('click', function (event){
				document.body.removeChild(shadow);
				document.body.removeChild(background);
			});
			file = this.model.get("items")[index].file;
			reader  = new FileReader();
			reader.onloadend = function () {
				preview.src = reader.result;
			}
			
			if (file) {
				reader.readAsDataURL(file);
			} else {
				preview.src = "";
			}
			background.appendChild(preview);
			document.body.appendChild(shadow);
			document.body.appendChild(background);
			return this;
		},
		renderFiles: function () {
			var i,
			that = this,
			items = this.model.get("items");

			for (i=0; i< items.length; i+=1) {
				if (that.model.get("preview")) {
					that.createBox(i, items[i].event,items[i].file);
				} else {
					that.createListBox(i, items[i].event, items[i].file);
				}	
			}
			
			return this;
		},
		createButtonsHTML: function (index) {
			var that = this,
			buttonGroups = document.createElement("div"),
	    	buttonGroupUp = document.createElement("div"),
	    	buttonGroupCancel = document.createElement("div"),
	    	buttonGroupRemove = document.createElement("div"),
	    	buttonGroupView = document.createElement("div");
	    	buttonUp = document.createElement("button"),
	    	buttonCancel = document.createElement("button"),
	    	buttonRemove = document.createElement("button"),
	    	buttonView = document.createElement("button");

	    	/*
	    	 * Adding Options buttons to file
	    	 **/
	    	buttonGroups.className = "btn-group btn-group-justified";

	    	buttonGroupUp.className = "pmdynaform-file-buttonup btn-group";
	    	buttonGroupCancel.className = "pmdynaform-file-buttoncancel btn-group";
	    	buttonGroupCancel.style.display = "none";
	    	buttonGroupRemove.className = "pmdynaform-file-buttonremove btn-group";
	    	buttonGroupView.className = "pmdynaform-file-buttonview btn-group";

	    	buttonUp.className = "glyphicon glyphicon-upload btn btn-success btn-sm";
	    	buttonCancel.className = "glyphicon glyphicon-remove btn btn-danger btn-sm";
	    	buttonRemove.className = "glyphicon glyphicon-trash btn btn-danger btn-sm";
	    	buttonView.className = "glyphicon glyphicon-zoom-in btn btn-primary btn-sm";

	    	$(buttonUp).data("index", index);
	    	$(buttonCancel).data("index", index);
	    	$(buttonRemove).data("index", index);
	    	$(buttonView).data("index", index);
	    	
	    	$(buttonUp).on("click", function(event) {
				that.onToggleButtonUpload(event, "up");
				that.onUploadItem(event);
				event.stopPropagation();
				event.preventDefault();
			});
			$(buttonCancel).on("click", function(event) {
				that.onToggleButtonUpload(event, "cancel");
				that.onCancelUploadItem(event);
				event.stopPropagation();
				event.preventDefault();
			});
	    	$(buttonRemove).on("click", function(event) {
				that.removeItem(event);
				event.stopPropagation();
				event.preventDefault();
			});			
			$(buttonView).on("click", function(event) {
				that.onPreviewItem(event);
				event.stopPropagation();
				event.preventDefault();
			});

	    	buttonGroupUp.appendChild(buttonUp);
	    	buttonGroupCancel.appendChild(buttonCancel);
			buttonGroupRemove.appendChild(buttonRemove);
			buttonGroupView.appendChild(buttonView);

			buttonGroups.appendChild(buttonGroupUp);
			buttonGroups.appendChild(buttonGroupCancel);
			buttonGroups.appendChild(buttonGroupView);
			buttonGroups.appendChild(buttonGroupRemove);

			return buttonGroups;
		},
		createBox: function (index, e, file) {
			var buttonGroups,
			rand = Math.floor((Math.random()*100000)+3),
	    	imgName = file.name,
	    	src = e.target.result,
	    	template = document.createElement("div"),
	    	resizeImage = document.createElement("div"),
	    	preview = document.createElement("span"),
	    	progress = document.createElement("div"),
	    	imgPreview = document.createElement("img"),
	    	spanOverlay = document.createElement("span"),
	    	spanUpDone = document.createElement("span"),
	    	typeClasses = {
	    		video: {
	    			Classes: "pmdynaform-file-boxpreview-video",
	    			icon: "glyphicon glyphicon-facetime-video"
	    		},
	    		audio: {
	    			Classes: "pmdynaform-file-boxpreview-audio",
	    			icon: "glyphicon glyphicon-music"
	    		},
	    		file: {
	    			Classes: "pmdynaform-file-boxpreview-file",
	    			icon: "glyphicon glyphicon-book"
	    		}
	    	},
	    	fileName = file.name.split(/\./)[1].toUpperCase();

	    	template.id = rand;
	    	template.className = "pmdynaform-file-containerimage";

			resizeImage.className = "pmdynaform-file-resizeimage";
			if (file.type.match(/image.*/)) {
				imgPreview.src = src;
				resizeImage.appendChild(imgPreview);
			} else if(file.type.match(/audio.*/)) {
				resizeImage.innerHTML = '<div class="'+ typeClasses['audio'].Classes +' thumbnail ' + typeClasses['audio'].icon+'"><div>'+ fileName +'</div></div>'; 
			} else if(file.type.match(/video.*/)) {
				resizeImage.innerHTML = '<div class="'+ typeClasses['video'].Classes +' thumbnail ' + typeClasses['video'].icon+'"><div>'+ fileName +'</div></div>'; 
			} else {
				resizeImage.innerHTML = '<div class="'+ typeClasses['file'].Classes +' thumbnail ' + typeClasses['file'].icon+'"><div>'+ fileName +'</div></div>'; 
			}
			spanOverlay.className = "pmdynaform-file-overlay";
			spanUpDone.className = "pmdynaform-file-updone";
			spanOverlay.appendChild(spanUpDone);
			resizeImage.appendChild(spanOverlay);

	    	preview.id = rand;
	    	preview.className = "pmdynaform-file-preview";
			preview.appendChild(resizeImage);	    	

	    	progress.id = rand;
	    	progress.className = "pmdynaform-file-progress";
	    	progress.innerHTML = "<span></span>";

	    	template.appendChild(preview);
	    	buttonGroups = this.createButtonsHTML(index);
	    	template.appendChild(buttonGroups);
	    	template.appendChild(progress);
	    	this.$el.find(".pmdynaform-file-droparea").append(template);
	    	
	    	return this;
			
		},
		/*createListBox: function (index, e, file) {
			var buttonGroups,
			rand = Math.floor((Math.random()*100000)+3),
			listItem = document.createElement("div"),
			label = document.createElement("div");

			listItem.className = "pmdynaform-file-listitem";

			buttonGroups = this.createButtonsHTML(index);
			label.className = "pmdynaform-label-nowrap";
			label.innerHTML = file.name;
			listItem.appendChild(label)
			listItem.appendChild(buttonGroups);
			

			this.$el.find(".pmdynaform-file-list").append(listItem);

			return this;
		},*/
		toggleButtonsAll: function () {
			//Select the name="button-all" for show the buttons

			return this;
		},
		render: function () {
			var that = this,
			oprand, files,
			container,
			itemElement,
			filesTag,
			itemMedia,
			item;
			if (PMDynaform.core.ProjectMobile) {
				this.createBoxPlus();
				this.$el.html( this.template(this.model.toJSON()) );
				if (this.model.get("hint")) {
					this.enableTooltip();
				}
				this.renderFiles();
				this.toggleButtonsAll();
				oprand = {
					dragClass : "pmdynaform-file-active",
					dnd: this.model.get("dnd"),
				    on: {
				        load: function (e, file) {
				        	that.addNewItem(e, file);
				        }
				    }
				};

				var fileContainer = this.$el.find(".pmdynaform-file-droparea-ext")[0];
				this.$el.find(".pmdynaform-file-droparea-ext").append(this.boxPlus);
				var fileControl = this.$el.find("input")[0];
				if (this.model.get("dnd") || this.model.get("preview")) {
					//PMDynaform.core.FileStream.setupDrop(fileContainer, oprand);
				}
			} else {
				this.$el.html( this.template(this.model.toJSON()));
				container = this.$el.find(".pmdynaform-file-control").empty();
				files = this.project.mobileDataControls;
				if (files.data){
					files = files.data[this.model.get("name")];
	                var data;
	                if (this.project.mobileDataControls){
	                    if ( this.project.mobileDataControls["data"]){
	                        var files = this.project.mobileDataControls["data"][that.model.get("name")];
	                        if (files && files.length > 0 ) {
								switch (this.model.get("type")) {
								    case "imageMobile":
										files = this.model.remoteProxyData(files); 
										for (var i = 0 ; i < files.length ; i+=1){
											itemElement = $("<img src=\"data:image/png;base64,"+files[i].base64 + "\"class='img-thumbnail' alt='Thumbnail Image'>");
											container.append(itemElement);
										}
								        break; 
								    case "audioMobile":
										for (var i=0 ;i< files.length ;i++){
											item = files[i];
											itemMedia = this.model.urlFileStreaming(item);
											itemElement = $("<audio src=\'"+ itemMedia.filePath + "\'id="+itemMedia.id+" style='margin:50px 0px 20px 0px;width: 100%;height: auto; background-color: black; display: block;' controls>");
											container.append(itemElement);
										}
								        break;
								    case "videoMobile":
										for (var i=0 ;i< files.length ;i++){
											item = files[i];
											itemMedia = this.model.urlFileStreaming(item);
											itemElement = $("<video type='video/mp4' src=\'"+ itemMedia.filePath + "\'id="+itemMedia.id+" style='width:300px; height:300px;background-color: black;' controls>");
											container.append(itemElement);
										}
								        break;
								    default:
								        //this.renderDefault();
								}
	                        }
	                    }
	                }
				}
				
			}
			return this;
		},
		renderFile : function (){
			var model;
			model = this.model;			
			switch (model.get("type")) {
			    case "imageMobile":
			        this.renderImage();
			        break; 
			    case "audioMobile":
			        this.renderAudio();
			        break;
			    case "videoMobile":
			        this.renderVideo();
			        break;
			    default: 
			        this.renderDefault();
			}
		},
		renderImage : function (){

		},
		createBoxImage : function(file){
			var src,
				newsrc,
				rand = Math.floor((Math.random()*100000)+3);
	    	//imgName = file.name, 
	    	// not used, Irand just in case if user wanrand to print it.
	    	if(file.filePath){
	    		src = file.filePath;
	    		newsrc = src;
	    	}
	    	if(file["base64"]){
	    		src = file["base64"];
	    		newsrc = this.model.makeBase64Image(src); 
	    	}	    	
	    	//src = file["thumbnails"];	    	
	    	
	    	var template = document.createElement("div");
	    	var resizeImage = document.createElement("div");
	    	var preview = document.createElement("span");
	    	var progress = document.createElement("div");

	    	template.id = rand;
	    	template.className = "pmdynaform-file-containerimage";

			resizeImage.className = "pmdynaform-file-resizeimage";
			resizeImage.innerHTML = '<img class="pmdynaform-image-ext" src="'+newsrc+'"><span class="pmdynaform-file-overlay"><span class="pmdynaform-file-updone"></span></span>';	    		    	
	    	preview.id = rand;
	    	preview.className = "pmdynaform-file-preview";
			preview.appendChild(resizeImage);
	    	progress.id = rand;
	    	progress.className = "pmdynaform-file-progress";
	    	progress.innerHTML = "<span></span>";
	    	template.appendChild(preview);	    	
	    	template.setAttribute("data-toggle","modal");
	    	template.setAttribute("data-target","#myModal");	
	    	this.viewsFiles.push({
	    		"id":file.id,				
				"data":template	    		
	    	});
	    	this.$el.find(".pmdynaform-file-droparea-ext").prepend(template);	    	
	    	return this;
		},
		renderAudio : function (){

		},
		createBoxAudio : function(file){
			var model,
				tplContainerAudio,
				tplContainer,				
				tplMediaAudio,
				mediaElement;
			model= {
				id: Math.floor((Math.random()*100000)+3),
				src : file.filePath?file.filePath:file,
				extension:file.extension?file.extension:null,
				name: file.name
			};

			tplMediaAudio = this.templateMediaAudio(model);
			mediaElement = new PMDynaform.core.MediaElement({
				el: $(tplMediaAudio),
				type : "audio"
			});

			tplContainerAudio = $(this.templateAudio(model)); 			
			tplContainerAudio.find(".pmdynaform-file-resizevideo").append(mediaElement.$el);			
			this.$el.find(".pmdynaform-file-droparea-ext").prepend(tplContainerAudio);
			
			this.viewsFiles.push({
	    		"id":file.id,				
				"data":tplContainerAudio	    		
	    	});
	    	return this;
		},
		renderVideo : function (){

		},
		/**
		 * [createBoxVideo Create a html of a video]
		 * @param  {[type]} file [description]
		 * @return {[type]}      [description]
		 */
		createBoxVideo : function(file){
			var model,
				tplContainerVideo,
				tplContainer,				
				tplMediaVideo,
				mediaElement,
				urlVideo;
			// The url for streaming consume a endpoint of a project			
			model= {
				id: Math.floor((Math.random()*100000)+3),
				src : file.filePath?file.filePath:file,
				name: file.name
			};

			tplMediaVideo = this.templateMediaVideo(model);
			mediaElement = new PMDynaform.core.MediaElement({
				el:$(tplMediaVideo),
				type:"video",
				streaming:file.filePath? false: true
			});

			tplContainerVideo = $(this.templateVideo(model)); 			
			tplContainerVideo.find(".pmdynaform-file-resizevideo").append(mediaElement.$el);			
			this.$el.find(".pmdynaform-file-droparea-ext").prepend(tplContainerVideo);
			
			this.viewsFiles.push({
	    		"id":file.id,				
				"data":tplContainerVideo	    		
	    	});
	    	return this;
		},
		renderDefault : function (){

		},

		setFilesRFC : function (arrayFiles){
            var array,model,item;
			model = this.model;			
			switch (model.get("type")) {
			    case "imageMobile":
			    		this.loadMixingSourceImages(arrayFiles);			        
			        break; 
			    case "audioMobile":
			    		this.loadMixingSourceMedia(arrayFiles);			        
			        break;
			    case "videoMobile":
			        	this.loadMixingSourceMedia(arrayFiles);
			        break;
			    default:
			        //this.renderDefault();
			}
        },

        loadMixingSourceImages : function (arrayFiles){        	
            var arrayRemoteData=[],
            	arrayFilePath=[],
            	array,
            	sw=false,
            	item;            
            for (var i=0 ;i< arrayFiles.length ;i++){
            	item = arrayFiles[i];
            	if(item.filePath){            		
            		this.validateFiles(item);
            		this.model.attributes.files.push(item);
            	}else{
            		arrayRemoteData.push(item);
            	}            	
            }

            if(arrayRemoteData.length != 0){
	            for (var index = 0 ; index< arrayRemoteData.length;index++){
	            	var arrayI = [];
	            	arrayI.push(arrayRemoteData[index]);
	            	array = this.model.remoteProxyData(arrayI);
	            	//array = this.model.remoteProxyData(arrayRemoteData);			        			        
			        if(array){
			            for (var i=0 ;i< array.length ;i++){            	
			            	this.validateFiles(array[i]);
			            	this.model.attributes.files.push(array[i]);
			            }
		        	}	
	            }	            
        	}
        },

        loadMixingSourceMedia : function (arrayFiles){        	
            var arrayRemoteData=[],
            	arrayFilePath=[],
            	array,
            	itemMedia,
            	sw=false,
            	item;            
            for (var i=0 ;i< arrayFiles.length ;i++){
            	item = arrayFiles[i];
            	if(typeof item == "string"){
            		itemMedia = this.model.urlFileStreaming(item);
            		this.validateFiles(itemMedia);
            		this.model.attributes.files.push(itemMedia);			        	
            	}
            	if(item.filePath){
            		this.validateFiles(item);
            		this.model.attributes.files.push(item);			        	
            	}            	
            }
        },
        /**
         * [setFiles Function for set files images, video and audio from a interface to mobile]
         * @param {[type]} arrayFiles [description]
         */
        setFiles : function (arrayFiles){        	
            var array;            
            for (var i=0 ;i< arrayFiles.length ;i++){            	
            	this.validateFiles(arrayFiles[i]);
            	this.model.attributes.files.push(arrayFiles[i]);
            }
        },
        validateFiles: function(file) {
        	this.createBoxFile(file);						
			return this;
		},
		createBoxFile : function (file){
			var model,
				response;
			model = this.model;			
			switch (model.get("type")) {
			    case "imageMobile":
			    	this.createBoxImage(file);			    	
			        break; 
			    case "audioMobile":
			        this.createBoxAudio(file);
			        break;
			    case "videoMobile":
			        this.createBoxVideo(file);
			        break;
			    default: 
			        //this.renderDefault();
			}
		},
		createBoxPlus: function () {
			var model;
			model = this.model;			
			switch (model.get("type")) {
			    case "imageMobile":
			        this.boxPlus=$(this.templatePlusImage());
			        break; 
			    case "audioMobile":
			        this.boxPlus=$(this.templatePlusAudio());
			        break;
			    case "videoMobile":
			        this.boxPlus=$(this.templatePlusVideo());
			        break;
			    default: 
			        //this.renderDefault();
			}			
			return this;
		},
		changeID : function (arrayNew){        	
            var array = this.model.attributes.files,
            	itemNew,
            	itemOld;           
            for (var i=0 ;i< arrayNew.length ;i++){            	
            	itemNew = arrayNew[i];
            	for (var j=0 ;j< array.length ;j++){
            		itemOld = array[j];
            		if(typeof itemOld === "string"){
            			if(itemNew["idOld"] === itemOld){
            				itemOld = itemNew["idNew"];
            			}            				
            		}
            		if(typeof itemOld === "object"){
            			if(itemNew["idOld"] === itemOld["id"]){
            				itemOld["id"] = itemNew["idNew"];
            			}	
            		}
            	}
            }
        }            
	});

	PMDynaform.extendNamespace("PMDynaform.view.FileMobile",FileMobile);
}());

(function(){
	var GeoMobile = PMDynaform.view.Field.extend({
		item: null,	
		template: _.template( $("#tpl-extgeo").html()),
		templatePlus: _.template( $("#tpl-extfile-plus").html()),
		templateGeoDesktop: _.template($("#tpl-map").html()),
		boxPlus: null,
		boxModal:null,
		boxBackground:null,
		viewsImages: [],
		imageOffLine : "geoMap.jpg",		
		events: {
	        "click button": "onClickButton"	        
	    },
		initialize: function () {			
		},		
		onClickButton: function (event) {			
			var respData;
			this.model.set("interactive",true);			
			respData = {
					idField: this.model.get("name"),
					interactive:true
				};			
			if(navigator.userAgent == "formslider-android"){
				JsInterface.getGeoTag(JSON.stringify(respData));				
			}
			if(navigator.userAgent == "formslider-ios"){
				this.model.attributes.project.setMemoryStack({"data":respData,"source":"IOS"});
				this.model.attributes.project.projectFlow.executeFakeIOS("show-map");
			}
			event.preventDefault();
			event.stopPropagation();			
			return this;
		},
		makeBase64Image : function (base64){
            return "data:image/png;base64,"+base64;
        },		
		createBox: function (data) {
			var rand,
				newsrc,
				template,
				resizeImage,
				preview,
				progress;

			if(data.base64){
				newsrc = this.makeBase64Image(data.base64);				 				
			}			
			if(data.filePath){
				newsrc = data.filePath;
			}

			rand = Math.floor((Math.random()*100000)+3);	    	
	    	template = document.createElement("div"),
	    	resizeImage = document.createElement("div"),
	    	preview = document.createElement("span"),
	    	progress = document.createElement("div");

	    	template.id = rand;
	    	template.className = "pmdynaform-file-containergeo";

			resizeImage.className = "pmdynaform-file-resizeimage";
			resizeImage.innerHTML = '<img src="'+newsrc+'">';	    	
	    	preview.id = rand;
	    	preview.className = "pmdynaform-file-preview";
			preview.appendChild(resizeImage);
	    	template.appendChild(preview);	    	
	    	this.$el.find(".pmdynaform-ext-geo").prepend(template);	    	
	    	this.hideButton();
	    	return this;
		},		
		hideButton : function (){
			var button;
			button = this.$el.find("button");
			button.hide();	    	
		},	
		render: function () {
			var that = this,
				fileContainer,
				fileControl,
				auxClass,
				geomapDesktop,
				that = this;
				if (PMDynaform.core.ProjectMobile){
					this.$el.html( this.template(this.model.toJSON()));			
					if (this.model.get("hint")) {
						this.enableTooltip();
					}
					fileContainer = this.$el.find(".pmdynaform-file-droparea-ext")[0];			
					fileControl = this.$el.find("input")[0];
				}else{
					this.$el.html(this.templateGeoDesktop(this.model.toJSON()));
					auxClass = function (params) {
						this.project = params.project;
					};
					auxClass.prototype.load = function () {
						var canvasMap = that.$el.find(".pmdynaform-map-canvas");
						var coords, mapOptions, map, marker;
						if (that.project.mobileDataControls){
							if ( that.project.mobileDataControls["data"]){
								var data = that.project.mobileDataControls["data"][that.model.get("name")];
								if (data) {
									var latitude  = data["latitude"] || 0;
									var longitude = data["longitude"] || 0;
									var altitude = data["altitude"] || 0;
									coords = new google.maps.LatLng(latitude, longitude);
						 			mapOptions = {
						                zoom: 15,
						                center: coords,
						                panControl: false,
						                zoomControl: false,
						                scaleControl: true,
						                streetViewControl: false,
						                overviewMapControl: false,
						                mapTypeControl: true,
						                navigationControlOptions: {
						                    style: google.maps.NavigationControlStyle.SMALL
						                },
						                mapTypeId: google.maps.MapTypeId.ROADMAP
						            };
						            map = new google.maps.Map(canvasMap[0], mapOptions);
									marker = new google.maps.Marker({
							        	position: coords,
							        	map: map,
							        	draggable: false,
							        	title: ""
							        });
								}
							}
						}			            
					};
					window.pmd = new auxClass({project:this});
					var script = document.createElement('script');
					script.type = 'text/javascript';
					$(script).data("script", "google");;
					script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=pmd.load';
					document.body.appendChild(script);
				}
			return this;
		},
		setLocation: function (location){
			var model,obj={},
				response;
			model= this.model;
			model.set("geoData",location);
			if(this.model.get("interactive")&& location !=null){				
				if(location.id == "" || location.id == null){
					obj={
						location: {
							altitude : location.altitude,
					        latitude : location.latitude,
					        longitude : location.longitude
						}
					};
					response=model.remoteGenerateLocation(obj);
					if(response){
						if(response.success == true){
							obj["imageId"]=response.imageId;
							response=model.remoteProxyData(response);
							obj["data"]=response.data;							
							this.createBox(response);
							model.set("geoData",obj);
						}
					}
					else{
						this.createImageOffLine(location);
					}									
				}else{
					if(location.base64){
						this.createBox(location);		
					}else{
						response=model.remoteProxyData(location.id);								
						response.altitude = location.altitude;
						response.latitude = location.latitude;
						response.longitude = location.longitude;
						model.set("geoData",response);						
						this.createBox(response);
					}					
				}
			}
		},

		setLocationRFC: function (location){
			var model,
				obj={},
				response;
			model= this.model;
			model.set("geoData",location);

			//location.data is a string Base64 from device mobile
			if(location.data){
				this.createBox(response);	//ok	
			}else{
				response=model.getImagesNetwork(location);								
				this.createBox(response);		
			}
		},

		createImageOffLine: function (location){
			location["filePath"]=this.imageOffLine;
			this.createBox({
				filePath:this.imageOffLine
			});
		}
	});

	PMDynaform.extendNamespace("PMDynaform.view.GeoMobile",GeoMobile);
}());
(function(){
    var MediaElement = function (settings) {
        this.el = settings.el;
        this.$el = settings.el;
        this.streaming = settings.streaming? settings.streaming: null;
        this.type = settings.type;
        if(this.type == "video"){            
            MediaElement.prototype.initVideo.call(this, this.el);
        }
        if(this.type== "audio"){
            MediaElement.prototype.initAudio.call(this, this.el);
        }        
    };

    MediaElement.prototype.initVideo = function (element) {
            var video = element.find("video");
            var control = element.find(".pmdynaform-media-control");
            //remove default control when JS loaded
            video[0].removeAttribute("controls");
            element.find('.pmdynaform-media-control').fadeIn(500);
            element.find('.pmdynaform-media-caption').fadeIn(500);
         
            //before everything get started
            video.on('loadedmetadata', function() {
                    
                //set video properties
                element.find('.current').text(timeFormat(0));
                element.find('.duration').text(timeFormat(video[0].duration));
                updateVolume(0, 0.7);
                    
                //start to get video buffering data 
                setTimeout(startBuffer, 150);
                    
                //bind video events
                element
                /*.hover(function() {
                    $('.control').stop().fadeIn();
                    $('.caption').stop().fadeIn();
                }, function() {
                    if(!volumeDrag && !timeDrag){
                        $('.control').stop().fadeOut();
                        $('.caption').stop().fadeOut();
                    }
                })*/
                .on('click', function() {
                    element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon glyphicon-pause').removeClass('glyphicon glyphicon-play');
                    $(this).unbind('click');
                    video[0].play();
                });
            });
            
            //display video buffering bar
            var startBuffer = function() {
                var that= this;
                var currentBuffer = video[0].buffered.end(0);
                var maxduration = video[0].duration;
                var perc = 100 * currentBuffer / maxduration;
                element.find('.pmdynaform-media-bufferBar').css('width',perc+'%');
                    
                if(currentBuffer < maxduration) {
                    setTimeout(startBuffer, 500);
                }
            };  
            
            //display current video play time
            video.on('timeupdate', function() {
                var currentPos = video[0].currentTime;
                var maxduration = video[0].duration;
                var perc = 100 * currentPos / maxduration;
                element.find('.pmdynaform-media-timeBar').css('width',perc+'%');    
                element.find('.current').text(timeFormat(currentPos)); 
            });
            
            //CONTROLS EVENTS
            //video screen and play button clicked
            video.on('click', function() { playpause(); } );
            element.find('.btnPlay').on('click', function() { playpause(); } );
            var playpause = function() {
                if(kitKatMode != null){
                    JsInterface.startVideo(video[0].src,"video/mp4");                                    
                }else{
                    if(video[0].paused || video[0].ended) {
                        element.find('.btnPlay').addClass('paused');
                        element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon glyphicon-pause').removeClass('glyphicon glyphicon-play');
                        video[0].play();
                    }
                    else {
                        element.find('.btnPlay').removeClass('paused');
                        element.find('.btnPlay').find('.glyphicon.glyphicon-pause').removeClass('glyphicon glyphicon-pause').addClass('glyphicon glyphicon-play');
                        video[0].pause();
                    }
                }
            };

            
            //fullscreen button clicked
            element.find('.btnFS').on('click', function() {
                if($.isFunction(video[0].webkitEnterFullscreen)) {
                    video[0].webkitEnterFullscreen();
                }   
                else if ($.isFunction(video[0].mozRequestFullScreen)) {
                    video[0].mozRequestFullScreen();
                }
                else {
                    alert('Your browsers doesn\'t support fullscreen');
                }
            });
            
            //sound button clicked
            element.find('.sound').click(function() {
                video[0].muted = !video[0].muted;
                $(this).toggleClass('muted');
                if(video[0].muted) {
                    element.find('.pmdynaform-media-volumeBar').css('width',0);
                }
                else{
                    element.find('.pmdynaform-media-volumeBar').css('width', video[0].volume*100+'%');
                }
            });
            
            //VIDEO EVENTS
            //video canplay event
            video.on('canplay', function() {
                element.find('.loading').fadeOut(100);
            });
            
            //video canplaythrough event
            //solve Chrome cache issue
            var completeloaded = false;
            video.on('canplaythrough', function() {
                completeloaded = true;
            });
            
            //video ended event
            video.on('ended', function() {
                element.find('.btnPlay').removeClass('paused');
                video[0].pause();
            });

            //video seeking event
            video.on('seeking', function() {
                //if video fully loaded, ignore loading screen
                if(!completeloaded) { 
                    element.find('.loading').fadeIn(200);
                }   
            });
            
            //video seeked event
            video.on('seeked', function() { });
            
            //video waiting for more data event
            video.on('waiting', function() {
                element.find('.loading').fadeIn(200);
            });
            
            //VIDEO PROGRESS BAR
            //when video timebar clicked
            var timeDrag = false;   /* check for drag event */
            element.find('.pmdynaform-media-progress').on('mousedown', function(e) {
                timeDrag = true;
                updatebar(e.pageX);
            });
            $(document).on('mouseup', function(e) {
                if(timeDrag) {
                    timeDrag = false;
                    updatebar(e.pageX);
                }
            });
            $(document).on('mousemove', function(e) {
                if(timeDrag) {
                    updatebar(e.pageX);
                }
            });
            var updatebar = function(x) {
                var progress = element.find('.pmdynaform-media-progress');
                
                //calculate drag position
                //and update video currenttime
                //as well as progress bar
                var maxduration = video[0].duration;
                var position = x - progress.offset().left;
                var percentage = 100 * position / progress.width();
                if(percentage > 100) {
                    percentage = 100;
                }
                if(percentage < 0) {
                    percentage = 0;
                }
                element.find('.pmdynaform-media-timeBar').css('width',percentage+'%');  
                video[0].currentTime = maxduration * percentage / 100;
            };

            //VOLUME BAR
            //volume bar event
            var volumeDrag = false;
            element.find('.pmdynaform-media-volume').on('mousedown', function(e) {
                volumeDrag = true;
                video[0].muted = false;
                element.find('.sound').removeClass('muted');
                updateVolume(e.pageX);
            });
            $(document).on('mouseup', function(e) {
                if(volumeDrag) {
                    volumeDrag = false;
                    updateVolume(e.pageX);
                }
            });
            $(document).on('mousemove', function(e) {
                if(volumeDrag) {
                    updateVolume(e.pageX);
                }
            });
            var updateVolume = function(x, vol) {
                var volume = element.find('.pmdynaform-media-volume');
                var percentage;
                //if only volume have specificed
                //then direct update volume
                if(vol) {
                    percentage = vol * 100;
                }
                else {
                    var position = x - volume.offset().left;
                    percentage = 100 * position / volume.width();
                }
                
                if(percentage > 100) {
                    percentage = 100;
                }
                if(percentage < 0) {
                    percentage = 0;
                }
                
                //update volume bar and video volume
                element.find('.pmdynaform-media-volumeBar').css('width',percentage+'%');    
                video[0].volume = percentage / 100;
                
                //change sound icon based on volume
                if(video[0].volume == 0){
                    element.find('.sound').removeClass('sound2').addClass('muted');
                }
                else if(video[0].volume > 0.5){
                    element.find('.sound').removeClass('muted').addClass('sound2');
                }
                else{
                    element.find('.sound').removeClass('muted').removeClass('sound2');
                }
                
            };

            //Time format converter - 00:00
            var timeFormat = function(seconds){
                var m = Math.floor(seconds/60)<10 ? "0"+Math.floor(seconds/60) : Math.floor(seconds/60);
                var s = Math.floor(seconds-(m*60))<10 ? "0"+Math.floor(seconds-(m*60)) : Math.floor(seconds-(m*60));
                return m+":"+s;
            };
            this.$el=element;    
    };


    MediaElement.prototype.initAudio = function (element) {
            var video = element.find("audio");
            var control = element.find(".pmdynaform-media-control");
            //remove default control when JS loaded
            video[0].removeAttribute("controls");
            element.find('.pmdynaform-media-control').fadeIn(500);
            element.find('.pmdynaform-media-caption').fadeIn(500);
         
            //before everything get started
            video.on('loadedmetadata', function() {
                    
                //set video properties
                element.find('.current').text(timeFormat(0));
                element.find('.duration').text(timeFormat(video[0].duration));
                updateVolume(0, 0.7);
                    
                //start to get video buffering data 
                setTimeout(startBuffer, 150);
                    
                //bind video events
                element
                /*.hover(function() {
                    $('.control').stop().fadeIn();
                    $('.caption').stop().fadeIn();
                }, function() {
                    if(!volumeDrag && !timeDrag){
                        $('.control').stop().fadeOut();
                        $('.caption').stop().fadeOut();
                    }
                })*/
                .on('click', function() {
                    element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon glyphicon-pause').removeClass('glyphicon glyphicon-play');
                    $(this).unbind('click');
                    video[0].play();
                });
            });
            
            //display video buffering bar
            var startBuffer = function() {
                var that= this;
                var currentBuffer = video[0].buffered.end(0);
                var maxduration = video[0].duration;
                var perc = 100 * currentBuffer / maxduration;
                element.find('.pmdynaform-media-bufferBar').css('width',perc+'%');
                    
                if(currentBuffer < maxduration) {
                    setTimeout(startBuffer, 500);
                }
            };  
            
            //display current video play time
            video.on('timeupdate', function() {
                var currentPos = video[0].currentTime;
                var maxduration = video[0].duration;
                var perc = 100 * currentPos / maxduration;
                element.find('.pmdynaform-media-timeBar').css('width',perc+'%');    
                element.find('.current').text(timeFormat(currentPos)); 
            });
            
            //CONTROLS EVENTS
            //video screen and play button clicked
            video.on('click', function() { playpause(); } );
            element.find('.btnPlay').on('click', function() { playpause(); } );
            var playpause = function() {
                if(video[0].paused || video[0].ended) {
                    element.find('.btnPlay').addClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon-pause').removeClass('glyphicon-play');
                    video[0].play();
                }
                else {
                    element.find('.btnPlay').removeClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-pause').removeClass('glyphicon-pause').addClass('glyphicon-play');
                    video[0].pause();
                }
            };

            
            //fullscreen button clicked
            element.find('.btnFS').on('click', function() {
                if($.isFunction(video[0].webkitEnterFullscreen)) {
                    video[0].webkitEnterFullscreen();
                }   
                else if ($.isFunction(video[0].mozRequestFullScreen)) {
                    video[0].mozRequestFullScreen();
                }
                else {
                    alert('Your browsers doesn\'t support fullscreen');
                }
            });
            
            //sound button clicked
            element.find('.sound').click(function() {
                video[0].muted = !video[0].muted;
                $(this).toggleClass('muted');
                if(video[0].muted) {
                    element.find('.pmdynaform-media-volumeBar').css('width',0);
                }
                else{
                    element.find('.pmdynaform-media-volumeBar').css('width', video[0].volume*100+'%');
                }
            });
            
            //VIDEO EVENTS
            //video canplay event
            video.on('canplay', function() {
                element.find('.loading').fadeOut(100);
            });
            
            //video canplaythrough event
            //solve Chrome cache issue
            var completeloaded = false;
            video.on('canplaythrough', function() {
                completeloaded = true;
            });
            
            //video ended event
            video.on('ended', function() {
                element.find('.btnPlay').removeClass('paused');
                video[0].pause();
            });

            //video seeking event
            video.on('seeking', function() {
                //if video fully loaded, ignore loading screen
                if(!completeloaded) { 
                    element.find('.loading').fadeIn(200);
                }   
            });
            
            //video seeked event
            video.on('seeked', function() { });
            
            //video waiting for more data event
            video.on('waiting', function() {
                element.find('.loading').fadeIn(200);
            });
            
            //VIDEO PROGRESS BAR
            //when video timebar clicked
            var timeDrag = false;   /* check for drag event */
            element.find('.pmdynaform-media-progress').on('mousedown', function(e) {
                timeDrag = true;
                updatebar(e.pageX);
            });
            $(document).on('mouseup', function(e) {
                if(timeDrag) {
                    timeDrag = false;
                    updatebar(e.pageX);
                }
            });
            $(document).on('mousemove', function(e) {
                if(timeDrag) {
                    updatebar(e.pageX);
                }
            });
            var updatebar = function(x) {
                var progress = element.find('.pmdynaform-media-progress');
                
                //calculate drag position
                //and update video currenttime
                //as well as progress bar
                var maxduration = video[0].duration;
                var position = x - progress.offset().left;
                var percentage = 100 * position / progress.width();
                if(percentage > 100) {
                    percentage = 100;
                }
                if(percentage < 0) {
                    percentage = 0;
                }
                element.find('.pmdynaform-media-timeBar').css('width',percentage+'%');  
                video[0].currentTime = maxduration * percentage / 100;
            };

            //VOLUME BAR
            //volume bar event
            var volumeDrag = false;
            element.find('.pmdynaform-media-volume').on('mousedown', function(e) {
                volumeDrag = true;
                video[0].muted = false;
                element.find('.sound').removeClass('muted');
                updateVolume(e.pageX);
            });
            $(document).on('mouseup', function(e) {
                if(volumeDrag) {
                    volumeDrag = false;
                    updateVolume(e.pageX);
                }
            });
            $(document).on('mousemove', function(e) {
                if(volumeDrag) {
                    updateVolume(e.pageX);
                }
            });
            var updateVolume = function(x, vol) {
                var volume = element.find('.pmdynaform-media-volume');
                var percentage;
                //if only volume have specificed
                //then direct update volume
                if(vol) {
                    percentage = vol * 100;
                }
                else {
                    var position = x - volume.offset().left;
                    percentage = 100 * position / volume.width();
                }
                
                if(percentage > 100) {
                    percentage = 100;
                }
                if(percentage < 0) {
                    percentage = 0;
                }
                
                //update volume bar and video volume
                element.find('.pmdynaform-media-volumeBar').css('width',percentage+'%');    
                video[0].volume = percentage / 100;
                
                //change sound icon based on volume
                if(video[0].volume == 0){
                    element.find('.sound').removeClass('sound2').addClass('muted');
                }
                else if(video[0].volume > 0.5){
                    element.find('.sound').removeClass('muted').addClass('sound2');
                }
                else{
                    element.find('.sound').removeClass('muted').removeClass('sound2');
                }
                
            };

            //Time format converter - 00:00
            var timeFormat = function(seconds){
                var m = Math.floor(seconds/60)<10 ? "0"+Math.floor(seconds/60) : Math.floor(seconds/60);
                var s = Math.floor(seconds-(m*60))<10 ? "0"+Math.floor(seconds-(m*60)) : Math.floor(seconds-(m*60));
                return m+":"+s;
            };
            this.$el=element;    
    };
    PMDynaform.extendNamespace("PMDynaform.core.MediaElement", MediaElement);

}());


(function(){
    var Qrcode_mobile = PMDynaform.view.Field.extend({
        item: null, 
        template: _.template( $("#tpl-ext-scannercode").html()),
        templatePlus: _.template( $("#tpl-extfile-plus").html()),
        boxPlus: null,
        boxModal:null,
        boxBackground:null,
        viewsImages: [],
        imageOffLine : "geoMap.jpg",        
        events: {
            "click button": "onClickButton"         
        },
        initialize: function () {
            //this.setOnChangeFiles();
            //this.initDropArea();
        },              
        onClickButton: function (event) {           
            var respData;
            respData ={
                idField:this.model.get("name")
            };          
            if(navigator.userAgent == "formslider-android"){
                JsInterface.getScannerCode(JSON.stringify(respData));               
            }
            if(navigator.userAgent == "formslider-ios"){
                this.model.attributes.project.setMemoryStack({"data":respData});
                this.model.attributes.project.projectFlow.executeFakeIOS("scannercode");
            }
            event.preventDefault();
            event.stopPropagation();
            return this;
        },                      
        hideButton : function (){
            var button;
            button = this.$el.find("button");
            button.hide();          
        },
        showLabel : function (scannercode){
            var label,
                newValue,
                html,
                container;
            
            container = this.$el.find("scanner").find(".pmdynaform-label-options");         
            html = '<span>'+scannercode+'</span>';
            container.append(html);                     
        },
        render: function () {
            var that = this,
                fileContainer,
                fileControl;
            if (PMDynaform.core.ProjectMobile) {
                this.$el.html( this.template(this.model.toJSON()));
            }else{
                this.$el.html( this.template(this.model.toJSON()));
                var data;
                if (that.project.mobileDataControls){
                    if ( that.project.mobileDataControls["data"]){
                        var data = that.project.mobileDataControls["data"][that.model.get("name")];
                        if (data) {
                            this.setScannerCode(data);
                        }
                    }
                }
                this.$el.find("button").detach();
            }
            return this;
        },
        setScannerCode: function (scannercode) {
            var model, obj={}, response, aux, i;
            model= this.model;
            if ( jQuery.isArray(scannercode) ) {
                for ( i = 0 ; i < scannercode.length ; i+=1 ) {
                    model.addCode(scannercode[i]);
                    this.showLabel(scannercode[i]);
                }
            } else {
                model.addCode(scannercode.data);
                this.showLabel(scannercode.data);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Qrcode_mobile", Qrcode_mobile);
}());
(function(){
	var Signature_mobile = PMDynaform.view.Field.extend({
		item: null,	
		template: _.template( $("#tpl-ext-signature").html()),
		templatePlus: _.template( $("#tpl-extfile-plus").html()),						
		viewsImages: [],
		imageOffLine : "geoMap.jpg",		
		events: {
	        "click button": "onClickButton"	        
	    },
		initialize: function () {			
			
		},		
		onClickButton: function (event) {			
			var respData;
			this.model.set("interactive",true);			
			respData = {
					idField: this.model.get("name")					
				};			
			if(navigator.userAgent == "formslider-android"){
				JsInterface.getSignature(JSON.stringify(respData));				
			}
			if(navigator.userAgent == "formslider-ios"){
				this.model.attributes.project.setMemoryStack({"data":respData,"source":"IOS"});
				this.model.attributes.project.projectFlow.executeFakeIOS("signature");
			}
			event.preventDefault();
			event.stopPropagation();			
			return this;
		},
		makeBase64Image : function (base64){
            return "data:image/png;base64,"+base64;
        },		
		createBox: function (data) {
			var rand,
				newsrc,
				template,
				resizeImage,
				preview,
				progress;

			if(data.filePath){
				newsrc = data.filePath;
			}else{
				newsrc = this.makeBase64Image(data.base64); 	    		
			}	
			rand = Math.floor((Math.random()*100000)+3);
	    	
	    	template = document.createElement("div"),
	    	resizeImage = document.createElement("div"),
	    	preview = document.createElement("span"),
	    	progress = document.createElement("div");

	    	template.id = rand;
	    	template.className = "pmdynaform-file-containergeo";

			resizeImage.className = "pmdynaform-file-resizeimage";
			resizeImage.innerHTML = '<img src="'+newsrc+'">';	    	
	    	preview.id = rand;
	    	preview.className = "pmdynaform-file-preview";
			preview.appendChild(resizeImage);
	    	template.appendChild(preview);	    	
	    	this.$el.find(".pmdynaform-ext-geo").prepend(template);	    	
	    	this.hideButton();
	    	return this;
		},
		hideButton : function (){
			var button;
			button = this.$el.find("button");
			button.hide();	    	
		},	
		render: function () {
			var that = this,
				fileContainer,
				fileControl,
				signature,
				itemElement;
			if (PMDynaform.core.ProjectMobile) {
				this.$el.html( this.template(this.model.toJSON()));			
				if (this.model.get("hint")) {
					this.enableTooltip();
				}
				fileContainer = this.$el.find(".pmdynaform-file-droparea-ext")[0];			
				fileControl = this.$el.find("input")[0];
			}else{
				this.$el.html( this.template(this.model.toJSON()));
				fileContainer = this.$el.find(".pmdynaform-geo-container").empty();
				if (this.project.mobileDataControls){
					signature = this.project.mobileDataControls;
					if (signature.data && signature.data[this.model.get("name")] && signature.data[this.model.get("name")].length > 0) {
						signature = signature.data[this.model.get("name")];		
						signature = this.model.remoteProxyData(signature[0]); 
						itemElement = $("<img src=\"data:image/png;base64,"+signature.base64 + "\"class='img-thumbnail' alt='Thumbnail Image'>");
						fileContainer.append(itemElement);
					}
				}
			}
			return this;
		},
		setFiles : function (arrayFiles){        	
            var array;            
            for (var i=0 ;i< arrayFiles.length ;i++){            	
            	this.createBox(arrayFiles[i]);
            	this.model.attributes.files.push(arrayFiles[i]);
            }
        },
		setSignature: function (arraySignature){
			var i,
				response,
				obj=[],
				files=[];
			for (i=0;i< arraySignature.length;i++){
				if (typeof arraySignature[i] == "string"){					
					response=this.model.remoteProxyData(arraySignature[i]);
					this.createBox(response);					
					files.push(response);
											
				}else{
					this.createBox(arraySignature[i]);
					files.push(arraySignature[i]);					
				}
			}
			this.model.set("files",files);		
		},
		changeID : function (arrayNew){        	
            var array = this.model.attributes.files,
            	itemNew,
            	itemOld;           
            for (var i=0 ;i< arrayNew.length ;i++){            	
            	itemNew = arrayNew[i];
            	for (var j=0 ;j< array.length ;j++){
            		itemOld = array[j];
            		if(typeof itemOld === "string"){
            			if(itemNew["idOld"] === itemOld){
            				itemOld = itemNew["idNew"];
            			}            				
            		}
            		if(typeof itemOld === "object"){
            			if(itemNew["idOld"] === itemOld["id"]){
            				itemOld["id"] = itemNew["idNew"];
            			}	
            		}
            	}
            }
        }
	});


	PMDynaform.extendNamespace("PMDynaform.view.Signature_mobile",Signature_mobile);
}());
(function(){
	
	var Validator = Backbone.Model.extend({
        defaults: {
            message: {},
            title: "",
            type: "",
            dataType: "",
            value: "",
            valid: true,
            maxLength: null,
            required: false,
            domain: false,
            options: [],
            factory: {},
            valueDomain: null,
            regExp : null,
            haveOptions: [
                "suggest",
                "checkbox",
                "radio",
                "dropdown"
            ]
        },
        initialize: function() {
        	var factoryValidator = {
        		"text": "requiredText",
    			"checkbox": "requiredCheckBox",
				"checkgroup": "requiredCheckGroup",
    			"radio": "requiredRadioGroup",
    			"dropdown": "requiredDropDown",
    			"textarea": "requiredText",
    			"datetime": "requiredText",
                "suggest": "requiredText" ,
                "file" : "requiredFile"                
        	};
        	this.setFactory(factoryValidator);
            this.checkDomainProperty();
        },
        setFactory: function(obj) {
        	this.set("factory", obj);
        	return this;
        },
        checkDomainProperty: function () {
            this.attributes.domain = ($.inArray(this.get("type"), this.get("haveOptions")) >= 0)? true: false;
            return this;
        },
        verifyValue: function () {
            var value = this.get('value'),
            valueDomain = this.get('valueDomain'),
            options = this.get('options'),
            validator = this.attributes.factory[this.get("type").toLowerCase()],
            regExp;

            this.set("valid", true);
            delete this.get("message")[validator];
            if (this.get('type') == 'file') {
                if (this.get('fileOnly') && this.get('fileOnly') !== null) {
                    if(this.get('fileOnly')['type'] == 'support') {
                        this.set('valid', false);
                        this.set('message', {
                            validator: this.get('fileOnly')['message']
                        });
                        return this;
                    }
                    if(this.get('fileOnly')['type'] == 'size') {
                        this.set('valid', false);
                        this.set('message',  {
                            validator: this.get('fileOnly')['message']
                        });
                        return this;
                    }
                }
            }
            if (this.get("required")) {
            	if (PMDynaform.core.Validators[validator].fn(value) === false) {
	                this.set("valid", false);
	                this.set("message", {
	                    validator: PMDynaform.core.Validators[validator].message
	                });
                    return this;
	            }
            }
            if(this.get("type") === "text" || this.get("type") === "textarea"){
                if (this.get("dataType") !== "" && value !== "") {
                    if (PMDynaform.core.Validators[this.get("dataType")] && PMDynaform.core.Validators[this.get("dataType")].fn(value) === false) {
                        this.set("valid", false);
                        this.set("message", {
                            "validator": PMDynaform.core.Validators[this.get("dataType")].message
                        });
                        return this;
                    }
                }

                if (this.get("maxLength")) {
                     if (PMDynaform.core.Validators.maxLength.fn( value, parseInt(this.get("maxLength"))) === false) {
                        this.set("valid", false);
                        this.set("message", {
                            validator: PMDynaform.core.Validators.maxLength.message + " " +this.get("maxLength") + " characters"
                        });
                    }
                }

                if (this.get("regExp") && this.get("regExp").validate !== "any"){
                    regExp = new RegExp(this.get("regExp").validate);
                    if (value.length > 0 && !regExp.test(value)) {
                        this.set("valid", false);
                        this.set("message", {validator:this.get("regExp").message});
                    } else {
                        this.set('valid', true);
                    }
                    return this;
                }
            }
            return this;
            /*if (this.get("domain") === true && value !== "") {
                if (PMDynaform.core.Validators["domain"].fn(valueDomain, options) === false) {
                    this.set("valid", false);
                    this.set("message", {
                        validator: PMDynaform.core.Validators['domain'].message
                    });
                }
            }*/
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Validator", Validator);

}());
(function(){
	var PanelModel = Backbone.Model.extend({
		defaults: {
			items: [],
			mode: "edit",
			namespace: "pmdynaform",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("form"),
			type: "form"
		},
		getData: function() {
			return {
				type: this.get("type"),
				name: this.get("name"),
				variables: {}
			}
		}
	});
	PMDynaform.extendNamespace("PMDynaform.model.Panel", PanelModel);

}());
(function(){
	var FormPanel = Backbone.Model.extend({
		defaults: {
			action: "",
			autocomplete: "on",
			script: {},
			data: [],
			items: [],
			name: 'PMDynaform-form',
			method: "get",
			namespace: "pmdynaform",
			target: null,
			type: "panel",
			inputDocuments : {}
		},
		getData: function(){
			return {
				type: this.get("type"),
				action: this.get("action"),
				method: this.get("method")
			}
		}
	});
	PMDynaform.extendNamespace("PMDynaform.model.FormPanel", FormPanel);
}());
(function(){
	var FieldModel = Backbone.Model.extend({
		defaults: {
			colSpan: 12,
            id: PMDynaform.core.Utils.generateID(),
			label: "Untitled",
            name: PMDynaform.core.Utils.generateName(),
			value: "",
            nameGridColum : null
		},
        initialize: function (options) {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
        },
		getData: function() {
            /*return {
                name: this.get("variable") ? this.get("variable").var_name : this.get("name"),
                value: this.get("value")
            };*/
            return {
                name : this.get("name") ? this.get("name") : "",
                value :  this.get("value")
            }
        },
        parseLabel: function () {
            var currentLabel = this.get("label"),
                maxLength = this.get("maxLengthLabel"),
                currentSize,
                itemsLabel, 
                k, 
                parsed = false;

            itemsLabel = currentLabel.split(/\s/g);
            for (k=0; k<itemsLabel.length; k+=1) {
                if (itemsLabel[k].length > maxLength) {
                    parsed = true;
                }
            }
            if (parsed) {
                this.set("tooltipLabel", currentLabel);
                this.set("label", currentLabel.substr(0, maxLength-4)+"...");
            }
            return this;
        },
        checkHTMLtags: function (value) {
            var i,
            newValue = value;

            if (typeof value === "string") {
                if (value.match(/([\<])([^\>]{1,})*([\>])/i) !== null) {
                    value = value.replace(/</g, "&lt;");
                    newValue = value.replace(/>/g, "&gt;");
                }
                if (/\"|\'/g.test(newValue)) {
                    newValue = newValue.replace(/"/g, "&quot;");
                    newValue = newValue.replace(/'/g, "&#39;");
                }
            }
            return newValue;
        },
        validate: function (attrs) {
            this.set("value", this.checkHTMLtags(attrs.value));
            this.set("label", this.checkHTMLtags(attrs.label));

            return this;
        },
        getEndpointVariable: function (urlObj) {
        	var prj = this.get("project"),
        	endPointFixed,
        	variable,
        	endpoint;

        	if (prj.endPointsPath[urlObj.type]) {
        		endpoint = prj.endPointsPath[urlObj.type]
        		for (variable in urlObj.keys) {
        			if (urlObj.keys.hasOwnProperty(variable)) {
        				endPointFixed =endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);	
        			}
        		}
        	}

        	return endPointFixed;
        },
        onChangeLabel: function (attrs, options) {
            this.attributes.label = this.checkHTMLtags(attrs.attributes.label);
            
            return this;
        },
        onChangeValue: function (attrs, options) {
            var data = {};
            this.attributes.value = this.checkHTMLtags(attrs.attributes.value);
            
            if (this.attributes.options) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options") || []
                });
                this.get("validator").verifyValue();
            }
            if (this.get("data")){
                data["value"] = this.get("value");
                data["label"] = this.get("value");
                this.attributes.data = data;
            }
            return this;
        },
        onChangeOptions: function () {
            var i,
            newOptions = [],
            options = this.get("options");

            for (i=0; i<options.length; i+=1) {
                newOptions.push(this.checkHTMLtags(options[i]));
            }
            this.attributes.options = newOptions;
            
            return this;
        },
        /**
         * The method check all the fields related to the current field based of the variable and 
         * set the same value to others. After set the value, all the fields are rendered.
         */
        changeValuesFieldsRelated: function () {
            var i,
            currentValue = this.get("value"),
            fieldsRelated = this.get("fieldsRelated") || [];

            for (i=0; i<fieldsRelated.length; i+=1) {
                fieldsRelated[i].model.attributes.value = currentValue;
                fieldsRelated[i].model.get("validator").set({
                    valueDomain: this.get("value"),
                    options: fieldsRelated[i].model.attributes.options || [],
                    domain: true
                });
                fieldsRelated[i].model.get("validator").verifyValue();
                //fieldsRelated[i].model.set("value", currentValue);
                fieldsRelated[i].render();
            }

            return this;
        },
        reviewRemoteVariable: function () {
            var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            data = {};
            if (this.get("group") === "grid"){
                data["field_id"] = this.get("columnName");
            }else{
                data["field_id"] = this.get("id");
            }
            if ( this.get("form") ) {
                if ( this.get("form").model.get("form") ) { 
                    data["dyn_uid"] = this.get("form").get("form").get("id");             
                }else{
                    data["dyn_uid"] = this.get("form").model.get("id");
                } 
            }

            endpoint = this.getEndpointVariable({
                type: "executeQuery",
                keys: {
                    "{var_name}": this.get("var_name") || ""
                }
            });
            url = prj.getFullURL(endpoint);
            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data : data,
                keys : prj.token,
                successCallback: function (xhr, response) {
                    that.mergeOptions(response);
                }
            });
            this.set("proxy", restClient);
            return this;
        },
        mergeOptions : function (remoteOptions){
            var k, remoteOpt = [], localOpt = [], options = [];
            for ( k = 0; k < remoteOptions.length; k+=1) {
                remoteOpt.push({
                    value : remoteOptions[k].value,
                    label : remoteOptions[k].text
                });
            }
            localOpt = this.get("localOptions");
            options = localOpt.concat(remoteOpt);
            this.attributes.options = options;
            return this;
        },
        getKeyLabel : function (){
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }
            } else {
                return {
                    name : this.get("name") ? this.get("name").concat("_label") : "",
                    value :  this.get("keyLabel")
                }
            }
        },
        setLocalOptions: function () {
            if (this.get("options")){
                this.set("localOptions", this.get("options"));
            }
            return this;
        },
        setRemoteOptions :function (){
            if (this.get("remoteOptions")){
                this.set("remoteOptions", this.get("optionsSql"));
            }
            return this;
        },
        mergeOptionsSql : function (){
            var options = [];
            if (this.get("options") && this.get("optionsSql"))
                options = this.get("localOptions").concat(this.get("optionsSql"));
                this.set("options", options);
            return this;
        }
	});
	PMDynaform.extendNamespace("PMDynaform.model.Field", FieldModel);
}());
(function(){
	var GridModel = PMDynaform.model.Field.extend({
		defaults: {
			title: "Grid",
			colSpan: 12,
			colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
			columns: [],
			data: [],
			disabled: false,
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("grid"),
			gridtable: [],
			layoutOpt: [
				"responsive",
				"static",
				"form"
			],
			layout: "responsive",
			pager: true,
			paginationRotate: false,
			paginationItems: 1,
			pageSize: 5,
			mode: "edit",
			rows: 1,
			type: "grid",
			functions: false,
			totalrow: [],
			functionOptions: {
				"sum": "sumValues",
				"avg": "avgValues"
			},
			dataColumns: [],
			gridFunctions: [],
			titleHeader: [],
			valid : true,
			countHiddenControl : 0,
			newRow : true,
			deleteRow : true
		},
		initialize: function (options) {
			var pagesize;
			if (options["addRow"] === undefined){
				this.set("addRow",true);
			}
			if(options["deleteRow"] === undefined){
				this.set("deleteRow",true);	
			}
			if ( jQuery.isNumeric(this.get("pageSize") ) ){
				pagesize = parseInt(this.get("pageSize"),10);
				if ( pagesize < 1 ) {
					pagesize = 1;
					this.set("pager",false);
				}
			} else {
				this.set("pager",false);
			}
                        if (!PMDynaform.core.ProjectMobile) {
                            this.set("pageSize", pagesize);
                        }
			this.set("label", this.checkHTMLtags(this.get("label")));
			this.on("change:label", this.onChangeLabel, this);
			if(options.project) {
                this.project = options.project;
            }
			if ( this.get("variable").trim().length === 0) {
				this.attributes.name = "";
			}else{
            	this.attributes.name = this.get("variable");
			}
            this.fixCoutFieldsHidden();
            this.setLayoutGrid();
            this.setPaginationItems();
            this.checkTotalRow();
		},
		setLayoutGrid: function () {
			if ($.inArray(this.get("layout"), this.get("layoutOpt")) < 0) {
				this.set("layout", "responsive");
			}

			return this;
		},
		setPaginationItems: function () {
			var rows = this.get("rows"),
			size = this.get("pageSize"),
			rotate = this.get("paginationRotate"),
			pagerItems;

			pagerItems = Math.ceil(rows/size) ? Math.ceil(rows/size) : 1;
            
            this.set("paginationRotate", rotate);
			this.set("paginationItems", pagerItems);

			return this;
		},
		checkTotalRow: function () {
			var i;

			loop_total:
			for (i=0; i<this.attributes.columns.length; i+=1) {
				if(this.attributes.columns[i].operation) {
					if(this.attributes.functionOptions[this.attributes.columns[i].operation.toLowerCase()]) {
						this.attributes.functions = true;
						break loop_total;
					}

				}
			}
			return this;
		},
		applyFunction: function () {
			var i;

			for (i=0; i<this.attributes.columns.length; i+=1) {
				if(this.attributes.columns[i].operation) {
					if(this.attributes.functionOptions[this.attributes.columns[i].operation.toLowerCase()]) {
						this.attributes.totalrow[i] = this[this.attributes.functionOptions[this.attributes.columns[i].operation.toLowerCase()]](i);
					}
				}
			}

			return this;
		},
		sumValues: function (colIndex) {
			var i,
			sum = 0,
			grid = this.attributes.gridFunctions;

			for (i=0; i<grid.length; i+=1) {
				sum += grid[i][colIndex];
			}

			return sum;
		},
		avgValues: function (colIndex) {
			var i,
			sum = 0,
			grid = this.attributes.gridFunctions;

			for (i=0; i<grid.length; i+=1) {
				sum += grid[i][colIndex];
			}
			
			return Math.round ((sum/grid.length) * 100 ) /100 ;
		},
		getData: function () {
			var i, j, cell, row, dataGrid = [], dataRow = [];
			if (this.get("view")) {
				for (i = 0 ; i < this.attributes.view.gridtable.length ; i+=1){
					dataRow = [];
					row = this.attributes.view.gridtable[i];
					for ( j = 0 ; j < row.length ; j +=1){
						cell = row[j];
						dataRow.push(cell.getValue());
					}
					dataGrid.push(dataRow);
				}
			}
			return {
				name : this.get("name"),
				value : dataGrid
			}
		},
		fixCoutFieldsHidden : function(){
        	var i, countHiddenControl = 0;
        	for ( i = 0 ; i < this.get("columns").length ; i +=1) {
        		if (this.get("columns")[i].type === "hidden"){
        			countHiddenControl +=1;
        		}
        	}
        	this.set("countHiddenControl",countHiddenControl);
        	return this;
        }	
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.GridPanel", GridModel);
}());
(function(){
	var ButtonModel = PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
			disabled: false,
            namespace: "pmdynaform",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("button"),
			label: "untitled label",
			type: "button"
		}
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.Button", ButtonModel);
}());
(function(){
	var DropdownModel =  PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "string",
            defaultValue: "",
            dependenciesField: [],
            disabled: false,
            executeInit: true,
            group: "form",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("dropdown"),
			label: "untitled label",
			localOptions: [],
            mode: "edit",
            options: [
                {
                    label: "Empty",
                    value: "empty"
                }
            ],
            remoteOptions: [],
            required: false,
            type: "text",
            valid: true,
            validator: null,
            variable: null,
            var_uid: null,
            var_name: null,
            variableInfo: {},
            value: "",
            columnName : null,
            originalType : null,
            data : null,
            itemClicked : false,
            keyLabel : "",
            optionsSql : [],
			enableValidate : true,
			placeholder : ""
		},
		initialize: function(options) {
            var data;
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            this.on("change:options", this.onChangeOptions, this);
            this.set("validator", new PMDynaform.model.Validator({
                domain: true
            }));
            this.set("dependenciesField",[]);
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            this.setDefaultValue();
            data = this.get("data");
            if ( data && data["value"] !== "") {
                this.attributes.value = data["value"];
                this.attributes.keyLabel = data["label"];
            } else {
                if (this.get("options").length){
                    this.set("value",this.get("options")[0]["value"]);
                    this.set("data",{
                        value:this.get("options")[0]["value"],
                        label : this.get("options")[0]["label"]
                    });
                } else {
                    this.set("data",{value:"", label:""});
                    this.set("value","");
                }
            }
			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
        },
        getData : function (){
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }

            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value :  this.get("value")
                }
            }
        },
        getKeyLabel : function (){
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }
            } else {
                return {
                    name : this.get("name") ? this.get("name").concat("_label") : "",
                    value :  this.attributes.data["label"]
                }
            }
        },
        setDefaultValue: function () {
            var options = this.get("options"),
            defaultValue = this.get("defaultValue");
            
            if ($.inArray(defaultValue, ["", null, undefined]) > 0) {
                this.set("defaultValue", options[0].value);
                this.set("value", options[0].value);
            }

            return this;
        },
        setDependencies: function(newDependencie) {
            var arrayDep, i, result, newArray = [];
            arrayDep = this.get("dependenciesField");
            if(arrayDep.indexOf(newDependencie) === -1){
            	arrayDep.push(newDependencie);
            }
            //this.set("dependenciesField",[]);
            this.set("dependenciesField",arrayDep);
        },
        isValid: function(){
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        validate: function (attrs) {
        	
    		var valueFixed = attrs.value.trim();
            this.attributes.value = valueFixed; 
            //this.set("value", valueFixed);
            this.get("validator").set("type", attrs.type);
            this.get("validator").set("required", attrs.required);
            this.get("validator").set("value", valueFixed);
            
            this.get("validator").set("dataType", attrs.dataType);
            this.get("validator").verifyValue();
        	this.isValid();
            return this.get("valid");
        },
        onChangeValue: function (attrs, options) {
            var i, opts, data = {};
            this.attributes.value = this.checkHTMLtags(attrs.attributes.value);
            if (this.attributes.options) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options") || []
                });
                this.get("validator").verifyValue();
            }
            if (!this.itemClicked){
                opts = this.get("options");
                for ( i = 0 ; i < opts.length ; i+=1 ) {
                    if (opts[i]["value"] === this.get("value")){
                        data["value"] = opts[i]["value"];
                        data["label"] = opts[i]["label"];
                        break;
                    }
                }
                this.attributes.data = data;
            }
            this.itemClicked = false;
            return this;
        },
        reviewRemotesOptions : function () {
            var sql;
            if ( (this.get("variable") && this.get("variable").trim().length) || this.get("group") == "grid" ) {
                sql = this.get("sql");
                if (sql){
                    this.reviewRemoteVariable();
                }
            }
            return this;
        },
        setLocalOptions: function () {
            var item = {};
            if (this.get("placeholder")!==""){
                item["label"] = this.get("placeholder");
                item["value"] = "";
                this.attributes.options.splice(0,-1,item);
            }
            if (this.get("options")){
                this.set("localOptions", this.get("options"));
            }
            return this;
        }
	});
	PMDynaform.extendNamespace("PMDynaform.model.Dropdown", DropdownModel);

}());
(function(){
	var RadioboxModel =  PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
			colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("radio"),
			dataType: "string",
            dependenciesField: [],
            disabled: false,
            defaultValue: "",
            label: "",
            localOptions: [],
            group: "form",
            hint: "",
            options: [
            	{
                    label : "empty",
                    value: "empty"
                }
            ],
            mode: "edit",
            type: "radio",
            readonly: false,
            remoteOptions: [],
            required: false,
            validator: null,
            valid: true,
            variable: null,
            var_uid: null,
            var_name: null,
            variableInfo: {},
            value: "",
            columnName : null,
            originalType : null,
            data : null,
            itemClicked : false,
            keyLabel  : "",
            optionsSql : [],
			enableValidate : true
		},
		initialize: function(attrs) {
			var data;
			this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            this.on("change:options", this.onChangeOptions, this);
			this.set("validator", new PMDynaform.model.Validator({
				domain: true
			}));
			this.set("dependenciesField",[]);
			this.verifyControl();
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
			this.initControl();
            data = attrs["data"] || this.model.get("data");
            if ( data && data["value"].toString() ) {
                this.attributes.value = data["value"];
                this.attributes.keyLabel = data["label"];
				this.set("data",data);
            } else {
                    this.set("data",{value:"", label:""});
                    this.set("value","");
            }
            if (this.get("group") === "form"){
				if(this.get("variable").trim().length === 0){
					this.attributes.name = this.get("id");
				}
            }else{
                this.attributes.name = this.get("id");
            }
			//this.reviewRemoteVariable();
            
		},
		initControl: function() {
			var opts = this.get("options"), 
			i,
			newOpts = [],
			itemsSelected = [];

			if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
			for (i=0; i<opts.length; i+=1) {
				if(opts[i].selected) {
					itemsSelected.push(opts[i].value.toString());
				}
				newOpts.push({
                    label: this.checkHTMLtags(opts[i].label),
                    value: this.checkHTMLtags(opts[i].value),
                    selected: opts[i]? opts[i]: false
                });
			}
                
            this.set("options", newOpts);
			this.set("selected", itemsSelected);
		},
		setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
		isValid: function() {
            this.set("valid", this.get("validator").get("valid"));
        	return this.get("valid");
        },
        verifyControl: function() {
			var opts = this.get("options"), i;
			for (i=0; i<opts.length; i+=1) {
				opts[i].value = opts[i].value.toString();
			}
			this.set("value", this.get("value").toString());
		},
		validate: function(attrs) {
			
            this.get("validator").set("type", attrs.type);
            this.get("validator").set("value", attrs.value.length);
            this.get("validator").set("valueDomain", attrs.value);
            this.get("validator").set("required", attrs.required);
            this.get("validator").set("dataType", attrs.dataType);
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
		},
		setItemClicked: function(itemUpdated) {
			var opts = this.get("options"),
				selected = this.get("selected"),
				position,
				newSelected,
				i;
            this.itemClicked = true;
			if (opts) {
				for(i=0; i< opts.length; i+=1) {
					if(opts[i].value.toString() === itemUpdated.value.toString()) {
						this.set("value", itemUpdated.value.toString());
					}
				}
			}
			return this;
		},
		getData: function() {

            //console.log("getData text")
            /*return {
                name: this.get("variable") ? this.get("variable").var_name : this.get("name"),
                value: this.get("value")
            };*/
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }

            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value :  this.get("value")
                }
            }
		},
        onChangeValue: function (attrs, options) {
            var i, opts, data = {};
            this.attributes.value = this.checkHTMLtags(attrs.attributes.value);
            if (this.attributes.options) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options") || []
                });
                this.get("validator").verifyValue();
            }
            if (!this.itemClicked){
                opts = this.get("options");
                for ( i = 0 ; i < opts.length ; i+=1 ) {
                    if (opts[i]["value"] === this.get("value")){
                        data["value"] = opts[i]["value"];
                        data["label"] = opts[i]["label"];
                        break;
                    }
                }
                this.attributes.data = data;
            }
            this.itemClicked = false;
            return this;
        }        
	});
	PMDynaform.extendNamespace("PMDynaform.model.Radio",RadioboxModel);
}());
(function(){
	var SubmitModel =  PMDynaform.model.Field.extend({
		defaults: {
			type: "submit",
			namespace: "pmdynaform",
			placeholder: "untitled",
			label: "untitled label",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("submit"),
			disabled: false,
			colSpan: 12
		}
	});
	PMDynaform.extendNamespace("PMDynaform.model.Submit", SubmitModel);
}()); 
(function(){
	var TextAreaModel =  PMDynaform.model.Field.extend({
		defaults: {
			type: "text",
			placeholder: "untitled",
			label: "untitled label",
			id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("textarea"),
			colSpan: 12,
            value: "",
            defaultValue: "",
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            maxLengthLabel: 15,
            rows: 2,
            group: "form",
            dataType: "string",
            hint: "",
            disabled: false,
            maxLength: null,
            mode: "edit",
            required: false,
            validator: null,
            valid: true,
            columnName : null,
            originalType : null,
            options : [],
            data : null,
            localOptions: [],
            remoteOptions: [],
            keyLabel : "",
            optionsSql : [],
			enableValidate : true
        },
        getData: function() {
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }
            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value :  this.get("value")
                }
            }
        },
        initialize: function(attrs) {
            var data, maxLength;
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));

            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            
            this.set("validator", new PMDynaform.model.Validator({
                "type"  : this.get("type"),
                "required" : this.get("required"),
                "maxLength" : this.get("maxLength"),
                "dataType" : this.get("dataType") || "string",
                "regExp" : { 
                    validate : this.get("validate"), 
                    message : this.get("validateMessage")
                }
            }));
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            data = this.get("data");
            
            if ( data && data["value"] !== "") {
                this.set("keyValue", data["value"]);
                if (data["label"] !== ""){
                    data = {
                        value : data["value"],
                        label : data["label"]
                    };
                }else{
                    data = {
                        value : data["value"],
                        label : data["value"]
                    };
                }
                this.set("data",data)
                this.set("value", data["value"]);
                this.set("defaultValue",data["value"]);
                this.set("keyLabel",data["value"]);
            } else {
                this.set("data",{value:"", label:""});
                this.set("value","");
                this.set("keyLabel","");
            }

            this.initControl();

			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
        },
        initControl: function() {
            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
        },
        isValid: function(){
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        validate: function (attrs) {
            var valueFixed = attrs.value.trim();
            this.set("value", valueFixed);
            this.get("validator").set("value", valueFixed);
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.TextArea", TextAreaModel);
}());
(function(){

	var TextModel = PMDynaform.model.Field.extend({
		defaults: {
			type: "text",
			placeholder: "",
			label: "untitled label",
			id: PMDynaform.core.Utils.generateID(),
			name: PMDynaform.core.Utils.generateName("text"),
			colSpan: 12,
			colSpanLabel: 3,
			colSpanControl: 9,
			maxLengthLabel: 15,
			namespace: "pmdynaform",
			operation: null,
			tooltipLabel: "",
			value: "",
			group: "form",
			defaultValue: "",
			dataType: "string",
			hint: "",
			mask: "",
			disabled: false,
			maxLength: null,
			mode: "edit",
			autoComplete: "off",
			required: false,
			formulator: null,
			validator: null,
			textTransform: "",
			valid: true, 
			variable: null,
			var_uid: null,
			var_name: null,
			columnName : null,
			originalType : null,
			data : null,
			localOptions: [],
			options: [
			],
			keyValue : null,
            keyLabel : "",
            formulaAssociatedObject : [],
            optionsSql : [],
            remoteOptions : [],
			enableValidate : true
		},
		initialize: function(attrs) {
			var data, maxLength;
			this.set("dataType", this.get("dataType").trim().length ? this.get("dataType") : "string");
			this.on("change:label", this.onChangeLabel, this);
			this.on("change:options", this.onChangeOptions, this);
			this.on("change:value", this.onChangeValue,this);
			this.on("change:value", this.onChangeData,this);
			this.set("label", this.checkHTMLtags(this.get("label")));
			this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
			this.set("validator", new PMDynaform.model.Validator({
				"type"  : this.get("type"),
				"required" : this.get("required"),
				"maxLength" : this.get("maxLength"),
				"dataType" : this.get("dataType") || "string",
				"regExp" : { 
					validate : this.get("validate"), 
					message : this.get("validateMessage")
				}
			}));
			this.set("dependenciesField",[]);
            /*if( PMDynaform.core.ProjectMobile && this.get("project") && 
                this.get("project") instanceof PMDynaform.core.ProjectMobile){
                 this.reviewRemotesOptions();
            }*/
            if (this.get("formula").trim().length){
            	this.attributes.formula = this.get("formula").replace(/\s/g, '');
            }
            if (this.attributes._extended && this.attributes._extended.formula){
            	this.attributes._extended.formula = this.attributes._extended.formula.replace(/\s/g, '');	
            }
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();

			data = this.get("data");
			if ( data && data["value"] !== "") {
				this.set("keyValue", data["value"]);
				if (data["label"] !== ""){
					data = {
						value : data["value"],
						label : data["label"]
					};
				}else{
					data = {
						value : data["value"],
						label : data["value"]
					};
				}
				this.set("data",data)
				this.set("value", data["value"]);
				this.set("defaultValue",data["value"]);
				this.set("keyLabel",data["value"]);
			} else {
				this.set("data",{value:"", label:""});
				this.set("value","");
				this.set("keyLabel","");
			}
			this.initControl();
			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
		},
		onChangeData : function () {
			
		},
		initControl: function() {
			if (this.get("defaultValue")) {
				this.set("value", this.get("defaultValue"));
			}
			if (typeof this.get("formula") === "string" && 
				this.get('formula') !== "undefined" &&
				this.get('formula') !== "null" &&
				this.get('formula').length > 1) {
				this.set("formulator", new PMDynaform.core.Formula(this.get("formula")));
				this.set("disabled", true);
			}
		},
		addFormulaTokenAssociated: function(formulator) {
			if (formulator instanceof PMDynaform.core.Formula) {
				//formulator.addField("field", this.get("name"));
				formulator.addTokenValue(this.get("id"), this.get("value"));
			}
			return this;
		},
        setDependencies: function(newDependencie) {
            var arrayDep, i, result, newArray = [];
            arrayDep = this.get("dependenciesField");
            if(arrayDep.indexOf(newDependencie) === -1){
            	arrayDep.push(newDependencie);
            }
            this.set("dependenciesField",arrayDep);
        },
		addFormulaFieldName: function(otherField) {
			this.get("formulator").addField("field", otherField);
			return this;
		},
		updateFormulaValueAssociated: function(field) {
			var resultField = field.model.get("formulator").evaluate();

			field.model.set("value", resultField);
			return this;
		},
		isValid: function() {
			this.attributes.valid = this.get("validator").get("valid");
			//this.set("valid", this.get("validator").get("valid"));
			return this.get("valid");
		},
		validate: function (attrs) {
			var valueFixed = attrs.value.trim();
			this.set("value", valueFixed);
			this.get("validator").set("value", valueFixed);
			this.get("validator").verifyValue();
			this.isValid();
			return this.get("valid");
		},
		getData: function() {
			if (this.get("group") == "grid"){
				return {
					name : this.get("columnName") ? this.get("columnName"): "",
					value :  this.get("value")
				}

			} else {
				return {
					name : this.get("name") ? this.get("name") : "",
					value :  this.get("value")
				}
			}
		},		
		getData2: function() {
			var data = {}, name, value;
			name = this.get("variable") ? this.get("variable").var_name : this.get("name");
			value = this.get("value");
			data[name] = value;
			return data;
		},
        reviewRemotesOptions : function () {
            var sql;
            if ( this.get("variable") && this.get("variable").trim().length) {
                sql = this.get("sql");
                if (sql){
                    this.reviewRemoteVariable();
                }
            }
            return this;
        }
	});
	PMDynaform.extendNamespace("PMDynaform.model.Text", TextModel);
}());
(function(){
	var File =  PMDynaform.model.Field.extend({
		defaults: {
            autoUpload: false,
			camera: true,
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            defaultValue: "",
            disabled: false,
            dnd: false,
            dndMessage: "Drag or choose local files",
            extensions: "pdf, png, jpg, mp3, doc, txt",
            group: "form",
            height: "200px",
            hint: "",
			id: PMDynaform.core.Utils.generateID(),
            items: [],
            label: "Untitled label",
            labelButton: "Choose Files",
            mode: "edit",
            multiple: false,
            name: PMDynaform.core.Utils.generateName("file"),
            variable: null,
            inputDocuments: null,
            preview: false,
            required: false,
            size: 1, //1 MB
            type: "file",
            proxy: [],
            valid: true,
            validator: null,
            value: "",
            columnName : null,
            originalType : null,
            data : null,
			enableValidate : true
        },
        initialize: function() {
            var data;
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
            this.on("change:label", this.onChangeLabel, this);
            this.initControl();
            this.set("items", []);
            this.set("proxy", []);

            this.set("validator", new PMDynaform.model.Validator({
                "type"  : "file",
                "required" : this.get("required")
            }));

            data = this.get("data");
            if ( data && (typeof data === "object") && (this.get("group") !== "grid") ) {
                if ( !jQuery.isArray(data["value"]) ) {
                    if (data["value"].trim().length){
                        if (data["value"][0] === "[" && data["value"][data["value"].length-1] === "]"){
                            data["value"] = JSON.parse(data["value"]);
                        }else{
                            data["value"] = [];                            
                        }
                    }else{
                        data["value"] = [];
                    }
                }
                if ( !jQuery.isArray(data["label"])) {
                    if (data["label"].trim().length){
                        if (data["label"][0] === "[" && data["label"][data["label"].length-1] === "]"){
                            data["label"] = JSON.parse(data["label"]);
                        }else{
                            data["label"] = [];                            
                        }
                    }else{
                        data["label"] = [];
                    }
                }else{
                    data["label"] = [];
                }
                this.set( "data", data );
            } else {
                this.set("data",{
                    value : [],
                    label : []
                })
            }
            this.attributes.value = this.get("data")["value"];
            return this;
        },
        initControl: function() {
            if (this.get("dnd")) {
                //this.set("preview", true);
            }
            return this;
        },
        isValid: function() {
            this.get("validator").set("value",this.get("value").toString());
            this.get("validator").verifyValue();
            if (this.get("validator").get("valid")){
                return true;
            }else{
                return false;
            }
        },
        uploadSuccess: function (a, b, c) {
            //console.log("SUCCESS",a, b, c);

            return this;
        },
        uploadFailure: function (a, b, c) {
            //console.log("FAILURE",a, b, c);

            return this;
        },

        uploadFile: function (indexItem) {
            var file = this.get("items")[indexItem].file,
            rand = Math.floor((Math.random()*100000)+3),
            that = this,
            proxy = this.get("proxy"),
            proxyItem,
            formdata = new FormData();

            if (formdata) {
                formdata.append("images[]", file);

                proxyItem = $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    onprogress: function (progress) {
                        // calculate upload progress
                        var percentage = Math.floor((progress.total / progress.totalSize) * 100);
                        // log upload progress to console
                        //console.log('progress', percentage);
                        if (percentage === 100) {
                          //console.log('DONE!');
                        }
                    },
                    success: that.uploadSuccess,
                    failure: that.uploadFailure
                });
                proxy.push(proxyItem);
                this.set("proxy", proxy);
            }
            
            return this;
        },
        stopUploadFile: function (index) {
            var proxy = this.get("proxy");

            proxy[index].abort();
            return this;
        }
	});

	PMDynaform.extendNamespace("PMDynaform.model.File", File);
}());
(function(){
	var CheckGroupModel =  PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "string",
            dependenciesField: [],
            disabled: false,
            group: "form",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("checkgroup"),
			label: "",
            localOptions: [],
            maxLengthLabel: 15,
            mode: "edit",
            options: [],
            readonly: false,
            required: false,
            optionsSql: [],
            selected: [],
            type: "checkgroup",
            validator: null,
            valid: true,
            value: [],
            columnName : null,
            originalType : null,
            data : {},
            defaultValue : [],
			labelsSelected : [],
            variable : "",
			enableValidate : true
		},
		initialize: function (attrs) {
            var i,j,d,data, that = this, option;
            this.on("change:options", this.onChangeOptions, this);
            this.on("change:value", this.updateItemSelected, this);
			this.set("validator", new PMDynaform.model.Validator({
                type: attrs.type,
                required: attrs.required
            }));

            if (attrs.hasOwnProperty("optionsSql") || attrs.hasOwnProperty("options")){
                this.set("localOptions",attrs.options);
                this.mergeOptions();
            }
            if (attrs.hasOwnProperty("data") || attrs.hasOwnProperty("value") || attrs.hasOwnProperty("defaultValue")) {
                this.set("data",this.initData(attrs.defaultValue, attrs.value, attrs.data, attrs.variable));
                this.attributes.value = this.get("data")["value"];
            }
            this.initControl();
            this.get("validator").set("value",this.get("value"));
            this.set("dependenciesField",[]);
			if ( this.get("variable") === "") {
                this.attributes.name = "";
			}
            return this;
		},
        initData : function (defaultV, value, data) {
            var auxData = {}, existData = false;
            if ( data ) {
                if ( typeof data === "object" ) {
                    if ( data.hasOwnProperty("value") && $.isArray(data["value"]) ){
                        if ( data.hasOwnProperty("label") && data["label"].toString().trim() === "" ) {
                            auxData["value"] = data["value"];
                            auxData["label"] = [];
                            for ( var i= 0; i < this.get("options").length ; i+=1 ) {
                                if ( data["value"].indexOf(this.get("options")[i]["value"]) > -1 ) {
                                    auxData["label"].push(this.get("options")[i]["label"]);
                                }
                            }
                            this.attributes.labelsSelected = auxData["label"];
                        } else {
                            if (data["label"].indexOf("[") === 0 && data["label"].lastIndexOf("]") === data["label"].length-1){
                                this.attributes.labelsSelected = JSON.parse(data["label"]);
                                data["label"] = JSON.stringify(this.attributes.labelsSelected);
                                auxData = data;
                            }
                        }
                        existData = true;
                    } else {
						if (typeof data["value"] === "string" && data["value"].length){
							data["value"] = data["value"].split(/,/g);
							if (data["label"].indexOf("[") === 0 && data["label"].lastIndexOf("]") === data["label"].length-1){
                                this.attributes.labelsSelected = JSON.parse(data["label"]);
                                data["label"] = JSON.stringify(this.attributes.labelsSelected);
                            }
							auxData = data;
						}
                        if (!data.hasOwnProperty("value") || data["value"] === ""){
                            auxData["value"] = [];
                            auxData["label"] = [];
                            this.attributes.labelsSelected = [];
                        }
                    }
                } else {
                    auxData["value"] = [];
                    auxData["label"] = [];
                    this.attributes.labelsSelected = [];
                }
            }else{
                auxData["value"] = [];
                auxData["label"] = [];
                this.attributes.labelsSelected = [];
            }
            if (defaultV && !existData ){
                var defaultV = defaultV.split("|");
                for ( var i=0; i< this.get("options").length ; i+=1 ) {
                    if ( defaultV.indexOf(this.get("options")[i]["value"]) > -1) {
                        auxData["value"].push(this.get("options")[i]["value"]);
                        auxData["label"].push(this.get("label")[i]["label"]);
                    }
                }
            }
            if ($.isArray(auxData["label"])){
                this.attributes.labelsSelected = auxData["label"];
                auxData["label"] = JSON.stringify(auxData["label"]);
            }
            return auxData;
        },
		initControl: function() {
			var opts = this.get("options"), 
            i,
            newOpts = [],
			itemsSelected = [];

			for (i=0; i<opts.length; i+=1) {
                if (this.get("data") && this.get("data").value){
                    if (this.get("data").value.indexOf(opts[i].value) > -1 ) {
                        opts[i].selected = true;
						itemsSelected.push(opts[i]);
                    }
                }
                newOpts.push({
                    label: this.checkHTMLtags(opts[i].label),
                    value: this.checkHTMLtags(opts[i].value),
                    selected: opts[i].selected? true : false
                });
			}
            this.set("options", newOpts);
			this.set("selected", itemsSelected);
		},
        mergeOptions : function (){
            var options = [];
            if (this.get("options") && this.get("optionsSql"))
                options = this.get("localOptions").concat(this.get("optionsSql"));
				options.push({
					value : "",
					label : ""
				});
                this.set("options", options);
            return this;
        },
		setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
		getData: function() {
            return {
                name : this.get("name") ? this.get("name") : "",
                value : JSON.stringify(this.get("value"))
            }
            return this;
		},
        getKeyLabel : function (){
            return {
                name : this.get("name") ? this.get("name").concat("_label") : "",
                value :  JSON.stringify(this.get("labelsSelected"))
            }            
        },
		validate: function(attrs) {
            this.get("validator").set("value", attrs.selected.length);
            if(this.get("options").length){
                this.get("validator").set("options",this.attributes.options); 
            }
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
		},
		isValid: function(){
            this.attributes.valid = this.get("validator").get("valid");
        	return this.get("valid");
        },
		setItemChecked: function(itemUpdated) {
			var opts = this.get("options"),
				selected = [],
				i;
			this.attributes.labelsSelected = [];
			if (opts) {
                for(i=0; i<opts.length; i+=1) {
                	if(opts[i].value.toString() === itemUpdated.value.toString()) {
                		opts[i].selected = itemUpdated.checked;
                	}
                }
                this.set("options", opts);
                for ( i = 0; i < opts.length; i+=1 ) {
                    if ( i === opts.length-1 && selected.length) {
                        opts[i].selected = false;
                    }
                    if ( opts[i].selected) {
                        selected.push(opts[i].value);
						this.attributes.labelsSelected.push(opts[i].label);
                    }
                }
                if (selected.length) {
                    this.attributes.value = selected;
                }else{
                    this.attributes.value = [];
                }
                this.set("selected", selected);
			}
            return this;
		},
        setItemsChecked: function(items) {
            for (var index = 0; index < items.length; index ++){
                this.setItemChecked({
                    value: items[index],
                    checked: true
                });
            }
            return this;
        },
        updateItemSelected: function () {
            var i, data = {},
            selected = this.get("selected"), auxValue, opts = this.get("options");
            if ($.isArray(this.get("value"))) {
                this.set("selected",[]);
                selected = this.get("selected");
                for ( i = 0 ; i < opts.length ; i+=1 ){
                    opts[i].selected = false;                    
                }
                this.set("options",opts);
                auxValue = this.get("value");
                for ( i = 0 ; i < auxValue.length ; i+=1 ){
                    this.setItemChecked({
                        value: auxValue[i],
                        checked: true
                    });
                }
            } else {
                this.setItemChecked({
                    value: this.attributes.value,
                    checked: true
                });                
            }
            for (i=0; i<selected.length; i+=1) {
                this.setItemChecked({
                    value: selected[i].trim ? selected[i].trim() : selected[i],
                    checked: true
                });
            }
            if (!this.attributes.disabled) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options")
                });
                this.get("validator").set("value", this.get("selected").length);
                this.get("validator").verifyValue();
            }
            if (this.attributes.data) {
                this.attributes.data["value"] = this.get("value");
            } 
            return this;
        }
	});
	PMDynaform.extendNamespace("PMDynaform.model.CheckGroup",CheckGroupModel);
}());
(function(){
    var CheckBoxModel =  PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "boolean",
            disabled: false,
            group: "form",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("checkgroup"),
            label: "",
            localOptions: [],
            mode: "edit",
            options: [],
            required: false,
            type: "checkbox",
            validator: null,
            valid: true,
            value: null,
            columnName : null,
            originalType : null,
            data : {},
            defaultValue : null,
            labelsSelected : null,
            optionsToTrue : [true, 1, "true", "1"],
            optionsToFalse : [false, 0 , "false" , "0"],
            variable : "",
			enableValidate : true
        },
        initialize: function (attrs) {
            this.on("change:value", this.updateItemSelected, this);
            this.set("validator", new PMDynaform.model.Validator({
                type: attrs.type,
                required: attrs.required
            }));
            if (attrs.options && !attrs.options.length) {
                this.attributes.options = [
                    {
                        "value": "1",
                        "label": "true"
                    },
                    {
                        "value": "0",
                        "label": "false"
                    }
                ];
            }
            if (attrs.hasOwnProperty("data") || attrs.hasOwnProperty("value") || attrs.hasOwnProperty("defaultValue")) {
                this.set("data",this.initData(attrs.defaultValue, attrs.value, attrs.data, attrs.variable));
                this.attributes.value = this.get("data")["value"];
            }else{
                this.attributes.data["value"] = "";
                this.attributes.data["label"] = "";
                this.attributes.value = this.get("data")["value"];                
            }


            this.initControl();
            this.attributes.value = this.get("data").value; 
            this.get("validator").set("value",this.get("value"));
            this.set("dependenciesField",[]);
            this.setLocalOptions();
            if ( this.get("variable").trim().length === 0) {
                if ( this.get("group") === "form" ) {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            return this;
        },
        initData : function (defaultV, value, data) {
            var options = this.get("options"), index;
            var auxData = {}, existData = false;
            if (typeof data === "object" &&  data !== null ) {
                if ( data.hasOwnProperty("value") && data["value"].toString() !== "") {
					data["value"] = data["value"].toString();
                    index = this.get("optionsToFalse").indexOf(data["value"]);
                    if (index > -1) {
                        auxData["value"] = "0";
                    }
                    index = this.get("optionsToTrue").indexOf(data["value"]);
                    if (index > -1){
                        auxData["value"] = "1";
                    }
                    for ( var i = 0 ; i < options.length ; i+=1 ) {
                        if ( options[i].value === auxData["value"] ) {
                            auxData["label"] = options[i].label;
                        }
                    }
                } else {
                    if (!data.hasOwnProperty("value") || data["value"] === "" || jQuery.isArray(data["value"])){
                        if ( defaultV ){
                            if ( this.get("optionsToFalse").indexOf(defaultV) > -1){
                                 auxData["label"] = options[1].label;
                                 auxData["value"] = options[1].value
                            } 
                            if (this.get("optionsToTrue").indexOf(defaultV) > -1){
                                 auxData["label"] = options[0].label;
                                 auxData["value"] = options[0].value
                            }
                        }else{
                            for ( var i = 0 ; i < options.length ; i+=1 ) {
                                if ( this.get("optionsToFalse").indexOf(options[i].value) > -1 ) {
                                    auxData["value"] = options[i].value;
                                    auxData["label"] = options[i].label;
                                }
                            }
                        }
                    }
                }
                existData = true;
            } else {
                for ( var i = 0 ; i < options.length ; i+=1 ) {
                    if ( this.get("optionsToFalse").indexOf(options[i].value) > -1 ) {
                        auxData["value"] = options[i].value;
                        auxData["label"] = options[i].label;
                    }
                }
            }
            //defaultV 0, 1,"0", "1", true, false, "true", "false" 
            if ( defaultV && !existData ){
                if ( this.get("optionsToFalse").indexOf(defaultV) >-1){
                     auxData["label"] = options[1].label;
                     auxData["value"] = options[1].value
                } 
                if (this.get("optionsToTrue").indexOf(defaultV) >-1){
                     auxData["label"] = options[0].label;
                     auxData["value"] = options[0].value
                }
            }
            return auxData;
        },
        initControl: function() {
            var opts = this.get("options"), 
            i,
            newOpts = [],
            itemsSelected = [];

            for (i=0; i<opts.length; i+=1) {
                if (!opts[i].label) {
                    throw new Error ("The label parameter is necessary for the field");
                }
                if (!opts[i].value && (typeof opts[i].value !== "number") ) {
                    opts[i].value = opts[i].label;
                }
                if (this.get("data") && this.get("data").value){
                    if (this.get("data").value.indexOf(opts[i].value) > -1 ) {
                        opts[i].selected = true;
                    }else{
                        opts[i].selected = false;
                    }
                }
                newOpts.push({
                    label: this.checkHTMLtags(opts[i].label),
                    value: this.checkHTMLtags(opts[i].value),
                    selected: opts[i].selected? true : false
                });
            }
            this.set("options", newOpts);
            this.set("selected", itemsSelected);
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        getData: function() {
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName") : "",
                    value :  this.get("value")
                }

            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value : this.get("value")
                }
            }
            return this;
        },
        getKeyLabel : function (){
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }
            } else {
                return {
                    name : this.get("name") ? this.get("name").concat("_label") : "",
                    value :  this.get("data")["label"]
                }
            }
        },
        validate: function(attrs) {
		    var value;
            value = parseInt(attrs.value);
            this.get("validator").set("value", value);
            if(this.get("options").length){
                this.get("validator").set("options",this.attributes.options); 
            }
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
        },
        isValid: function(){
            this.attributes.valid = this.get("validator").get("valid"); 
            return this.get("valid");
        },
        updateItemSelected: function () {
            if (!this.attributes.disabled) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options")

                });
                this.get("validator").set("value", this.get("selected").length);
                this.get("validator").verifyValue();
            }
            if (this.attributes.data) {
                this.attributes.data["value"] = this.get("value");
            } 
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.CheckBox",CheckBoxModel);
}());
(function(){
	var DatetimeModel =  PMDynaform.model.Field.extend({
		defaults: {
			colSpan : 12,
			colSpanLabel : 3,
			colSpanControl : 9,
			namespace : "pmdynaform",
			dataType : "date",
			group : "form",
			hint : "",
			id : "",
			name : "",
			placeholder : "",
			required : false,
			validator : null,
			originalType : null,
			disabled : false,
			format : false,
			mode : "edit",
			data : null,
			value :"",
			stepping : 1,
			minDate : false,
			maxDate : false,
			useCurrent : false,
			collapse : true,
			defaultDate : false,
			disabledDates : [],
			sideBySide : false,
			daysOfWeekDisabled : [],
			calendarWeeks : true,
			viewMode : "days",
			toolbarPlacement : "default",
			showTodayButton : true,
			showClear : true,
			widgetPositioning : {
                horizontal : "left",
                vertical : "bottom"
			},
			keepOpen : false,
			dayViewHeaderFormat : "MMMM YYYY",
			pickType : "datetime",
			keyLabel : "",
			enableValidate : true
		},
		getData: function() {
			if (this.get("group") == "grid"){
				return {
					name : this.get("columnName") ? this.get("columnName"): "",
					value :  this.get("value")
				}

			} else {
				return {
					name : this.get("name") ? this.get("name") : "",
					value :  this.get("value")
				}
			}
			return this;
		},
		initialize: function(options) {
			var useDefaults = {
				showClear : false,
				useCurrent : true
			}, 
			useCurrentOptions = ['year', 'month', 'day', 'hour', 'minute'],
			viewMode = ['years', 'months', 'days'],
			data = {
				value : "",
				label : ""
			}, defaultDate, maxOrMinDate, flag = true;
			
			if (this.get("useCurrent") === "true") {
				this.attributes.useCurrent = JSON.parse(this.get("useCurrent"));
			}
			if (useCurrentOptions.indexOf(this.get("useCurrent")) === -1){
				this.attributes.useCurrent = useDefaults["useCurrent"];
			}
			
			if (this.get("showClear") === "true"){
				this.attributes.showClear = JSON.parse(this.get("showClear"));
			}

			if (this.get("showClear") === "false"){
				this.attributes.showClear = JSON.parse(this.get("showClear"));			
			}
			if ( typeof this.get("showClear") !== "boolean" ){
				this.attributes.showClear = useDefaults["showClear"];
			}
			
			if (this.get("format") === "false"){
				this.attributes.format = JSON.parse(this.get("format"));			
			}
			
			if ( viewMode.indexOf(options["viewMode"]) === -1){
				this.attributes.viewMode = "days";
			}

			this.customPickTimeIcon(this.get("pickType"));

			if ( !_.isEmpty(this.get("data")) && (this.get("data")["value"].trim().length || this.get("data")["label"].trim().length)   ) {
				this.set("defaultDate",false);
			}else{
				this.set("data",data);
			}

			if (typeof this.get("maxDate") === "boolean") {
				this.set("maxDate","");	
			}
			
			if (!this.isDate(this.get("maxDate"))) {
				this.set("maxDate","");	
			}
			
			if (!this.isDate(this.get("minDate"))) {
				this.set("minDate","");	
			}
			
			if (!this.isDate(this.get("defaultDate"))) {
				this.set("defaultDate",false);	
			}
			
			if ( this.get("maxDate").trim().length && this.get("defaultDate") && this.get("defaultDate").trim().length ) {
				defaultDate =  this.get("defaultDate").split("-");
				maxOrMinDate = this.get("maxDate").split("-");
				if ( (parseInt(defaultDate[0]) <= parseInt(maxOrMinDate[0])) ){
					if( (parseInt(defaultDate[1]) <= parseInt(maxOrMinDate[1])) ){
						if( (parseInt(defaultDate[2]) <= parseInt(maxOrMinDate[2])) ) {
							flag = true;
						}else{
							flag = false;
						}
					} else{
						flag = false;
					}
				}else{
					flag = false;
				}
				if (!flag){
					this.set("defaultDate",false);
				}
			}
			if (flag){
				if ( typeof this.get("minDate") === "boolean") {
					this.set("minDate","");
				}
				if ( this.get("minDate").trim().length && this.get("defaultDate") && this.get("defaultDate").trim().length ) {
					defaultDate =  this.get("defaultDate").split("-");
					maxOrMinDate = this.get("minDate").split("-");
					if ( (parseInt(defaultDate[0]) >= parseInt(maxOrMinDate[0])) ){
						if( (parseInt(defaultDate[1]) >= parseInt(maxOrMinDate[1])) ){
							if( (parseInt(defaultDate[2]) >= parseInt(maxOrMinDate[2])) ) {
								flag = true;
							}else{
								flag = false;
							}
						} else{
							flag = false;
						}
					}else{
						flag = false;
					}
					if (!flag){
						this.set("defaultDate",false);
					}
				}
			}			
			this.attributes.value = this.get("data")["value"];
			this.attributes.keyLabel = this.get("data")["label"];
            this.set("validator", new PMDynaform.model.Validator({
            	required : this.get("required"),
            	type : this.get("type"),
            	dataType :this.get("dataType")
            }));

			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
			return this;
		},
		customPickTimeIcon : function (format) {
			
		},
        isValid: function(){
            //this.set("valid", this.get("validator").get("valid"));
            this.attributes.valid = this.get("validator").get("valid");
            return this.get("valid");
        },
        validate: function (attrs) {
            var valueFixed = attrs.value.trim();
            this.set("value",valueFixed);
            //this.attributes.value = valueFixed;
            this.get("validator").set("value", valueFixed);
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
        },
        isDate:  function (dateValue) {
        	var pattern = /@@|@\$|@=/;
        	var d = new Date(dateValue);
        	if(pattern.test(dateValue) || d == "Invalid Date" || typeof d == "undefined" || !d) {
        		return false;
        	}
        	return true;
        }
	});

	PMDynaform.extendNamespace("PMDynaform.model.Datetime", DatetimeModel);
}());
(function(){

	var SuggestModel = PMDynaform.model.Field.extend({
		defaults: {
            autoComplete: "off",
            type: "text",
            placeholder: "untitled",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("suggest"),
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            value: "",
            group: "form",
            defaultValue: "",            
            maxLengthLabel: 15,
            mode: "edit",
            tooltipLabel: "",
            disabled: false,
            dataType: "string",
            executeInit: true,
            required: false,
            maxLength: null,
            validator: null,
            valid: true,
            proxy: null,
            variable: null,
            var_uid: null,
            var_name: null,
            options:[],
            localOptions: [],
            remoteOptions: [],
            dependenciesField: [],
            columnName : null,
            originalType : null,
            mask : "",
            clickedControl : true,
            keyLabel : "",
            optionsSql : [],
			enableValidate : true
        },
        initialize: function(attrs) {
            var data;
            this.set("dataType", this.get("dataType").trim().length?this.get("dataType"):"string");
            this.set("dependenciesField",[]);
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            this.set("validator", new PMDynaform.model.Validator());
            this.initControl();
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            /*if( PMDynaform.core.ProjectMobile && this.get("project") && 
                this.get("project") instanceof PMDynaform.core.ProjectMobile){
                 this.reviewRemotesOptions();
            }*/
            data = this.get("data");
            if ( data && data["value"] !== "" && data["label"] !== "" ) {
                this.set("value",data["value"]);
                this.set("keyLabel",data["label"]);
            } else {
                this.set("data",{value:"", label:""});
                this.set("value","");
            }
			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
        },
        initControl: function() {
            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        isValid: function(){
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        emptyValue: function (){
            this.set("value","");
        },
        setDependencies: function(newDependencie) {
            var arrayDep,i, result, newArray = [];
            arrayDep = this.get("dependenciesField");
            if(arrayDep.indexOf(newDependencie) == -1){
                arrayDep.push(newDependencie);
            }
            this.set("dependenciesField",[]);
            this.set("dependenciesField",arrayDep);
        },
        validate: function (attrs) {
            var valueFixed = attrs.value.trim();
            
            this.set("value", valueFixed);
            this.get("validator").set("type", attrs.type);
            this.get("validator").set("required", attrs.required);
            this.get("validator").set("value", valueFixed);
            this.get("validator").set("dataType", attrs.dataType);
            this.get("validator").verifyValue();
            this.isValid();
            return this.get("valid");
        },
        getData: function() {
            //console.log("getData text")
            /*return {
                name: this.get("variable") ? this.get("variable").var_name : this.get("name"),
                value: this.get("value")
            };*/
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }

            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value :  this.get("value")
                }
            }
        },
        onChangeValue: function (attrs, options) {
            var data = {}, opts,i, exist = false;
            this.attributes.value = this.checkHTMLtags(attrs.attributes.value);
            opts = this.get("options");
            /*if (this.attributes.options) {
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options") || []
                });
                this.get("validator").verifyValue();
            }*/
            if ( !this.get("clickedControl") ) {
                if (this.get("data")){
                    for ( i = 0 ; i < opts.length ; i+=1 ) {
                        if ( opts[i]["value"] === this.get("value") ) {
                            data["value"] = opts[i]["value"];
                            data["label"] = opts[i]["label"];
                            this.attributes.data = data;
                            exist = true;
                            //this.attributes.value = opts[i]["label"];
                            break;
                        }
                    }
                    if (!exist) {
                        data["value"] = "";
                        data["label"] = "";
                        //console.log("entra?");
                        this.attributes.data = data;
                    }
                }
            }
            return this;
        },
        reviewRemotesOptions : function () {
            var sql;
            if ( this.get("variable") && this.get("variable").trim().length) {
                sql = this.get("sql");
                if (sql){
                    this.reviewRemoteVariable();
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Suggest", SuggestModel);
}());
(function(){

	var LinkModel = PMDynaform.model.Field.extend({
		defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "string",
            defaultValue: "",
            disabled: false,
            group: "form",
            hint: "",
            href: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("link"),
            label: "untitled label",
            mode: "edit",
            required: false,
            target: "_blank",
            targetOptions: {
                blank: "_blank",
                parent: "_parent",
                self: "_self",
                top: "_top"
            },
            type: "link",
            valid: true,
            value: "",
            columnName : null,
            originalType : null
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            this.setTarget();
        },
        setTarget: function () {
            var opt = this.get("targetOptions"),
            target;

            target = opt[this.get("target")]? opt[this.get("target")] : "_blank";
            this.set("target", target);
        },
        getData: function() {

            //console.log("getData text")
            /*return {
                name: this.get("variable") ? this.get("variable").var_name : this.get("name"),
                value: this.get("value")
            };*/
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }

            } else {
                return {
                    name : this.get("name") ? this.get("name") : "",
                    value :  this.get("value")
                }
            }
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Link", LinkModel);
}());
(function(){

	var Label = PMDynaform.model.Field.extend({
		defaults: {
            colSpan: 12,
            group: "form",
            hint: "",
            namespace: "pmdynaform",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("label"),
            label: "untitled label",
            mode: "view",
            options: [],
            required: false,
            type: "label",
            columnName : null,
            originalType : null,
            variable: "",
            var_uid: null,
            var_name: null,
            localOptions : [],
            remoteOptions : [],
            fullOptions : [""],
            data : null,
            value : null,
			dataType : null,
            keyValue : null,
            optionsSql : [],
			enableValidation : true
        },
        getData: function() {
            if (this.get("group") == "grid"){
                if (this.get("originalType") !== "label"){
                    return {
                        name : this.get("columnName") ? this.get("columnName"): "",
                        value :  this.get("keyValue")
                    }
                }else{
                    return {
                        name : this.get("columnName") ? this.get("columnName"): "",
                        value :  this.get("value")
                    }
                }
            } else {
                if (this.get("originalType") !== "label"){ 
                    return {
                        name : this.get("name") ? this.get("name") : "",
                        value :  this.get("keyValue")
                    }
                }else{
                    return {
                        name : this.get("name") ? this.get("name") : "",
                        value :  this.get("value")
                    }
                }
            }
        },
        initialize: function(options) {
            var i, aux,
            newOptions = [],
			data;

            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:options", this.onChangeOptions, this);

            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
			data = this.get("data");
			if ( data ) {
                this.set("value", data["label"]);
                this.set("defaultValue",data["label"]);
                if (options.originalType == "text"){
                    if (jQuery.isArray(options.fullOptions) && options.fullOptions.length){
                        if(this.get("value") === ""){
							this.set("value",options.fullOptions[0]);
						}
                    }else{
                        this.set("fullOptions",[data["label"]]);
                    }
                }
			} else {
                if (options.originalType == "checkbox"){
                    this.set("data",{"value":[],"label":"[]"});
                }else{
                    if (this.get("defaultValue")){
                        this.set("data",{
                            value:this.get("defaultValue"), 
                            label:this.get("defaultValue")
                        });
                        this.set("value",this.get("defaultValue"));
                    }else{
        				this.set("data",{value:"", label:""});
        				this.set("value","");
                    }
                }
			}
            if (this.get("group") === "form"){
                if ( this.get("variable") && this.get("variable").trim().length === 0) {
                    if ( this.get("group") === "form" ) {
                        this.attributes.name = "";
                    } else {
                        this.attributes.name = this.get("id");
                    }
                }
            }
            if (typeof this.get("formula") === "string" && 
                this.get('formula') !== "undefined" &&
                this.get('formula') !== "null" &&
                this.get('formula').length > 1) {
                this.set("formulator", new PMDynaform.core.Formula(this.get("formula")));
                this.set("disabled", true);
            }
            return this;
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        addFormulaTokenAssociated: function(formulator) {
            if (formulator instanceof PMDynaform.core.Formula) {
                formulator.addTokenValue(this.get("id"), this.get("value"));
            }
            return this;
        },
        updateFormulaValueAssociated: function(field) {
            var resultField = field.model.get("formulator").evaluate();
            field.model.set("value", resultField);
            return this;
        },
        addFormulaFieldName: function(otherField) {
            this.get("formulator").addField("field", otherField);
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Label", Label);
}());
(function(){

	var Title = PMDynaform.model.Field.extend({
		defaults: {
            type: "title",
            label: "untitled label",
            mode: "view",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform",
            className: {
                title: "pmdynaform-label-title",
                subtitle: "pmdynaform-label-subtitle"
            }
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Title", Title);
}());
(function(){
	var Empty = Backbone.Model.extend({
		defaults: {
			colSpan: 12,
			namespace: "pmdynaform",
			id: PMDynaform.core.Utils.generateID(),
			type: "empty"
		}
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.Empty", Empty);
}());
(function(){
	var HiddenModel = PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
			dataType: "string",
			namespace: "pmdynaform",
			defaultValue: null,
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("hidden"),
			type: "hidden",
			valid: true,
			value: "",
			group : "form",
			var_name : "",
			data : null,
			keyLabel : "",
			options : [],
			optionsSql : [],
			remoteOptions : []
		},
		initialize: function (options) {
			var data = {};
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
            this.on("change:value", this.onChangeValue, this);
			
			if ( !this.get("data")){
				data = {
					value:this.get("defaultValue"),
					label:this.get("defaultValue")
				}
				this.attributes.data = data;
			}
			this.set("value",this.get("data")["value"]);
			this.set("keyLabel", this.get("data")["label"]);
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
			this.initControl();
			if ( this.get("variable").trim().length === 0) {
				if ( this.get("group") === "form" ) {
                	this.attributes.name = "";
				} else {
            		this.attributes.name = this.get("id");
				}
			}
			return this;
		},
		initControl: function () {
			if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
			}
		},
		checkHTMLtags: function (value) {
            var i,
            newValue = value;
            if (typeof value === "string") {
            	if (value.match(/([\<])([^\>]{1,})*([\>])/i) !== null) {
	                value = value.replace(/</g, "&lt;");
	                newValue = value.replace(/>/g, "&gt;");
	            }
	            if (/\"|\'/g.test(value)) {
	                newValue = newValue.replace(/"/g, "&quot;");
	                newValue = newValue.replace(/'/g, "&#39;");
	            }
            }

            return newValue;
        },
		onChangeValue: function () {
		},
		getData: function() {
			if (this.get("group") == "grid"){
				return {
					name : this.get("columnName") ? this.get("columnName"): "",
					value :  this.get("value")
				}
			} else {
				return {
					name : this.get("name") ? this.get("name") : "",
					value :  this.get("value")
				}
			}
        },
        getKeyLabel : function (){
            if (this.get("group") == "grid"){
                return {
                    name : this.get("columnName") ? this.get("columnName"): "",
                    value :  this.get("value")
                }
            } else {
                return {
                    name : this.get("name") ? this.get("name").concat("_label") : "",
                    value :  this.get("value")
                }
            }
        }
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.Hidden", HiddenModel);
}());
(function(){
	var ImageModel = PMDynaform.model.Field.extend({
		defaults: {
			colSpan: 12,
			colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
			disabled: false,
			defaultValue: "",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("image"),
			label: "untitled label",
			crossorigin: "anonymous",
			alt: "",
			src: "",
			height: "",
			width: "",
			mode: "view",
			shape: "thumbnail",
			shapeTypes: {
				thumbnail: "img-thumbnail",
				rounded: "img-rounded",
				circle: "img-circle"
			},
			type: "image",
            columnName : null,
            originalType : null,
            group : "form"
		},
		initialize: function (options) {
			var defaults;

			this.set("label", this.checkHTMLtags(this.get("label")));
            this.set("defaultValue", this.checkHTMLtags(this.get("defaultValue")));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
			if(options.project) {
                this.project = options.project;
            }
            this.setShapeType();
		},
		setShapeType: function () {
			var shape = this.get("shape"),
			types = this.get("shapeTypes"),
			selected;

			selected =types[shape] ? types[shape] : types["thumbnail"];
			this.set("shape", selected);
			return;
		}
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.Image", ImageModel);
}());

(function(){
	var SubFormModel = Backbone.Model.extend({
		defaults: {
			colSpan: 12,
			namespace: "pmdynaform",
			id: PMDynaform.core.Utils.generateID(),
 			name: PMDynaform.core.Utils.generateName("form"),
			type: "form",
			mode: "edit",
			valid: true,
			modelForm: null
		},
		initialize: function () {
			
		},
		getData: function () {
			return {
				name: this.get("name"),
				id: this.get("id"),
				variables: {}
			}
		}
	});
	
	PMDynaform.extendNamespace("PMDynaform.model.SubForm", SubFormModel);
}());
(function(){

	var GeoMapModel = PMDynaform.model.Field.extend({
		defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dragMarker: false,
            dataType: "string",
            disabled: false,
            decimals: 6,
            group: "form",
            hint: "",
            fullscreen: false,
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("link"),
            googlemap: null,
            label: "untitled label",
            mode: "edit",
            required: false,
            valid: true,
            value: "",
            navigator: true,
            currentLocation: false,
            supportNavigator: false,
            latitude : null,
            longitude: null,
            marker: null,
            zoom: 15,
            tooltipLabel: "",
            panControl: false,
            zoomControl: false,
            scaleControl: false,
            streetViewControl: false,
            overviewMapControl: false,
            mapTypeControl: false,
            title: ""
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.checkSupportGeoLocation();
        },
        checkSupportGeoLocation: function () {
            var supportNavigator = navigator.geolocation? true : false;

            this.set("supportNavigator", supportNavigator);

            return this;
        },
        rightToLeftLabels: function () {
            var marker = this.get("marker"),
            infowindow = new google.maps.InfoWindow();

            infowindow.setContent('<b>القاهرة</b>');
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(this.get("googlemap"), marker);
            });
        },
        getData: function() {
            return {
                name: this.get("variable")? this.get("variable").var_name : this.get("name"),
                value: this.get("longitude") + "|" + this.get("latitude")
            };
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.GeoMap", GeoMapModel);
}());
(function(){
	var Annotation = PMDynaform.model.Field.extend({
		defaults: {
            type: "annotation",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Annotation", Annotation);
}());
(function(){
	var Audio_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "audio_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Audio_mobile", Audio_mobile);
}());
(function(){
	var Geomap_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "geomap_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Geomap_mobile", Geomap_mobile);
}());
(function(){
	var Image_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "image_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Image_mobile", Image_mobile);
}());
(function(){
	var Qrcode_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "qrcode_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Qrcode_mobile", Qrcode_mobile);
}());
(function(){
	var Signature_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "signature_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Signature_mobile", Signature_mobile);
}());
(function(){
	var Video_mobile = PMDynaform.model.Field.extend({
		defaults: {
            type: "video_mobile",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function() {
            this.set("label", this.checkHTMLtags(this.get("label")));
            this.on("change:label", this.onChangeLabel, this);
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Video_mobile", Video_mobile);
}());
(function(){
	var PanelField = PMDynaform.model.Field.extend({
		defaults: {
            type: "panel",
            showHeader : false,
            showFooter : false,
            title : "untitled-panel",
            footerContent : "<div>footer pmdynaform!</div>",
            content : "<div>content Body in panel PMDynaform</div>",
            id: PMDynaform.core.Utils.generateID(),
            colSpan: 12,
            namespace: "pmdynaform",
            typePanel : "default",
            border : "1px"
        },
        initialize : function (options) {
            var length;
            if (options["border"]){
                length = this.verifyLenght(options["border"]);
                this.set("border",length);
            }

        },
        verifyLenght : function (length) {
            var length;
            if(typeof length === 'number') {
                length = length + "px";   
            } else if(Number(length).toString() != "NaN"){
                length = length + "px";
            }
            else if(/^\d+(\.\d+)?px$/.test(length)) {
                length = length;
            } else if(/^\d+(\.\d+)?%$/.test(length)) {
                length = length;
            } else if(/^\d+(\.\d+)?em$/.test(length)) {
                length = length;
            } else if(length === 'auto' || length === 'inherit') {
                length = length;
            } else {
                length = "1px";   
            }
            return length;
        }

    });
    PMDynaform.extendNamespace("PMDynaform.model.PanelField", PanelField);
}());
(function(){
	var FileMobile =  PMDynaform.model.Field.extend({
		defaults: {
            autoUpload: false,
			camera: true,
            colSpan: 12,
            disabled: false,
            dnd: false,
            dndMessage: "Drag or choose local files",
            extensions: "pdf, png, jpg, mp3, doc, txt",
            group: "form",
            height: "100%",
            hint: "",
			id: PMDynaform.core.Utils.generateID(),
            items: [],
            label: "Untitled label",
            labelButton: "Choose Files",
            mode: "edit",
            multiple: false,
            name: PMDynaform.core.Utils.generateName("file"),
            preview: false,
            required: false,
            size: 1, //1 MB
            type: "file",
            proxy: [],
            valid: true,
            validator: null,
            value: "",
            files:[]               
        },

        initialize: function() {
            this.attributes.files= [];
            this.initControl();
            this.set("items", []);
            this.set("proxy", []);
        },
        initControl: function() {
            if (this.get("dnd")) {
                this.set("preview", true);
            }
            return this;
        },
        getData: function() {
            var respData=[];
            for (var i = 0;i< this.attributes.files.length ;i++){
                if(this.attributes.files[i].id){
                    respData.push(this.attributes.files[i].id);
                }else{
                    respData.push(this.attributes.files[i]);
                }
            }
            return {
                name: this.get("name"),
                value: respData
            };
        },
        getDataComplete: function() {
            var respData=[];
            for (var i = 0;i< this.attributes.files.length ;i++){
                if(this.attributes.files[i]["base64"]){
                    respData.push(this.attributes.files[i].id);
                }else{
                    respData.push(this.attributes.files[i]);
                }                
            }
            return {
                name: this.get("name"),
                value: respData
            };
        },
        validate: function (attrs) {
            
        },
        getIDImage: function (index){
           return this.attributes.images[index].id;   
        },
        getBase64Image: function (index){
           return this.attributes.images[index].value;   
        },
        makeBase64Image : function (base64){
            return "data:image/png;base64,"+base64;
        },        
        remoteProxyData : function (arrayImages){           
                var prj = this.get("project"),
                url,
                restClient,
                that = this,
                endpoint,
                that= this,
                respData,
                data;
                data = this.formatArrayImagesToSend(arrayImages);                
                respData = this.formatArrayImages(prj.webServiceManager.imagesInfo(data));                                  
                return respData;            
        },
        formatArrayImagesToSend : function (arrayImages){
            var imageId,
                dataToSend = [],
                item = {};

            for (var i = 0; i< arrayImages.length ; i++){
                imageId = arrayImages[i];
                if(PMDynaform.core.ProjectMobile){
                    item = {
                        fileId: imageId,
                        width : "100",
                        version :1
                    };
                }else{
                    item = {
                        fileId: imageId,                        
                        version :1
                    };
                }                
                dataToSend.push(item);
            }
            return dataToSend;
        },
        formatArrayImages : function (arrayImages){
            var itemReceive, imageReceive,
                dataToSend = [],
                item = {};

            for (var i = 0; i< arrayImages.length ; i++){
                imageReceive = arrayImages[i];
                item = {
                    id: imageReceive.fileId,
                    base64: imageReceive.fileContent
                };
                dataToSend.push(item);
            }
            return dataToSend;
        },
        remoteProxyDataMedia : function (id){
                var prj = this.get("project"),
                url,
                restClient,
                that = this,
                endpoint,
                that= this,
                respData;                
                endpoint = this.getEndpointVariables({
                    type: "fileStreaming",
                    keys: {
                        "{fileId}": id,
                        "{caseID}": prj.webServiceManager.options.keys.caseID                           
                    }
                });                
                url = prj.webServiceManager.getFullURLStreaming(id);             
                return url;            
        },
        urlFileStreaming : function (id){
            var prj = this.get("project"),
                url,
                that = this,
                endpoint,                
                dataToSend;                                                
                url = prj.webServiceManager.getFullURLStreaming(id);
                dataToSend = {
                    id:id,
                    filePath: url
                };
                return dataToSend;
        },        
        getEndpointVariables: function (urlObj) {
            var prj = this.get("project"),
            endPointFixed,
            variable,
            endpoint;

            if (prj.endPointsPath[urlObj.type]) {
                endpoint = prj.endPointsPath[urlObj.type]
                for (variable in urlObj.keys) {
                    if (urlObj.keys.hasOwnProperty(variable)) {
                        endPointFixed =endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);  
                        endpoint= endPointFixed;
                    }
                }
            }

            return endPointFixed;
        }
	});

	PMDynaform.extendNamespace("PMDynaform.model.FileMobile", FileMobile);
}());
(function(){
    var GeoMobile =  PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            type: "location",
            label: "Untitled label",
            mode: "edit",
            group: "form",
            labelButton: "Map",
            name: "name",
            colSpan: 12,
            height: "auto",            
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,

            geoData:null,
            interactive: true            
        },
        initialize: function() {
            this.initControl();
        },
        initControl: function() {
            this.attributes.images = [];
            //if (this.get("dnd")) {
                this.set("preview", true);
            //}
            return this;
        },
        isValid: function() {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getDataRFC: function() {
            var geoValue = this.attributes.geoData;
            if(geoValue == "null" || geoValue == null){
                geoValue=null;                    
            }else{
                if(geoValue.imageId == "" || geoValue.imageId == null || typeof geoValue.imageId == "undefined"){
                    geoValue = geoValue;
                }
            }
            if(geoValue){
                if(geoValue.data){
                    delete geoValue.data;
                }
            }
            return {
                name: this.get("name"),
                value: geoValue
            };
        },
        getData: function() {
            var geoValue = this.attributes.geoData;            
            if(geoValue){
                if(geoValue.base64){
                    delete geoValue.base64;
                }
            }
            return {
                name: this.get("name"),
                value: geoValue
            };
        },
        
        validate: function (attrs) {
            
        },        
        remoteProxyData : function (id){           
            return this.get("project").webServiceManager.imageInfo(id,600);
            /*var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            that= this,
            respData;            
            endpoint = this.getEndpointVariables({
                        type: "getImageGeo",
                        keys: {                           
                            "{caseID}": prj.keys.caseID,                                                           
                        }
                    });
                    url = prj.getFullURL(endpoint);
            url = prj.getFullURL(endpoint);
            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data:[{
                    fileId: id,
                    width : "600",
                    version :1
                }],                
                keys: prj.token,
                successCallback: function (xhr, response) {
                    respData = response;
                }
            });
            return {
                id: respData[0].fileId,
                base64: respData[0].fileContent
            };*/
            
        },
        getImagesNetwork : function (location){           
            var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            that= this,
            respData;            
            endpoint = this.getEndpointVariables({
                        type: "getImageGeo",
                        keys: {
                            "{fileID}": location.imageId,
                            "{caseID}": prj.keys.caseID,                                                           
                        }
                    });
                    url = prj.getFullURL(endpoint);
            url = prj.getFullURL(endpoint);            
            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data: {
                    fileId: location.imageId,
                    width : "600",
                    version :1
                },                
                keys: prj.token,
                successCallback: function (xhr, response) {
                    respData = response;
                }
            });
            this.set("proxy", restClient);
            return respData;            
        },        
        remoteGenerateLocation : function (location){           
            var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            that= this,
            dataToSend = new FormData(),
            respData;           
                        
            dataToSend.append("tas_uid",location);                        
            dataToSend.append("tas_uid",prj.keys.taskID);
            dataToSend.append("app_doc_comment","");
            dataToSend.append("latitude",location.latitude);
            dataToSend.append("longitude",location.longitude);
     
            endpoint = this.getEndpointVariable({
                        type: "generateImageGeo",
                        keys: {
                            "{caseID}": prj.keys.caseID                          
                        }
                    });
                    url = prj.getFullURL(endpoint);
            url = prj.getFullURL(endpoint);

            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data:dataToSend,                
                keys: prj.token,
                successCallback: function (xhr, response) {
                    respData = response;
                }
            });
            this.set("proxy", restClient);
            return respData;
            
        },
        getEndpointVariables: function (urlObj) {
            var prj = this.get("project"),
            endPointFixed,
            variable,
            endpoint;

            if (prj.endPointsPath[urlObj.type]) {
                endpoint = prj.endPointsPath[urlObj.type]
                for (variable in urlObj.keys) {
                    if (urlObj.keys.hasOwnProperty(variable)) {
                        endPointFixed =endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);  
                        endpoint= endPointFixed;
                    }
                }
            }

            return endPointFixed;
        }   
    });

    PMDynaform.extendNamespace("PMDynaform.model.GeoMobile", GeoMobile);
}());
(function(){
    var Qrcode_mobile = PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            type: "scannercode",
            label: "Untitled label",
            mode: "edit",
            group: "form",
            labelButton: "Scanner Code",
            name: "name",
            colSpan: 12,
            height: "auto",            
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,
            codes: [],

            geoData:null,
            interactive: true            
        },
        initialize: function() {
            this.initControl();
        },
        initControl: function() {
            this.attributes.codes = [];
            this.set("preview", true);
            return this;
        },
        isValid: function() {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getData: function() {            
            return {
                name: this.get("name"),
                value: this.get("codes")
            };
        },
        addCode: function(newCode) {            
            var codes = this.get("codes");
            codes.push(newCode);
        },
        validate: function (attrs) {
            
        },                
        getEndpointVariables: function (urlObj) {
            var prj = this.get("project"),
            endPointFixed,
            variable,
            endpoint;

            if (prj.endPointsPath[urlObj.type]) {
                endpoint = prj.endPointsPath[urlObj.type]
                for (variable in urlObj.keys) {
                    if (urlObj.keys.hasOwnProperty(variable)) {
                        endPointFixed =endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);  
                        endpoint= endPointFixed;
                    }
                }
            }
            return endPointFixed;
        } 
    });
    PMDynaform.extendNamespace("PMDynaform.model.Qrcode_mobile", Qrcode_mobile);
}());
(function(){
    var Signature_mobile =  PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            type: "signature",
            label: "Untitled label",
            mode: "edit",
            group: "form",
            labelButton: "Signature",
            name: "name",
            colSpan: 12,
            height: "auto",            
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,
            files:[]            
        },
        initialize: function() {
            this.initControl();
        },
        initControl: function() {
            this.attributes.files= [];
            this.set("preview", true);            
            return this;
        },
        isValid: function() {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getData: function() {
            var i,
                response = [],
                signatureValue = this.attributes.files;
            for (i = 0;i< signatureValue.length;i++){
                if(typeof signatureValue[i].id  != "undefined" && signatureValue[i].id != null){
                    response.push(signatureValue[i].id);
                }
            }
            return {
                name: this.get("name"),
                value: response
            };             
        },
        getDataCustom: function() {
            var signatureValue = this.attributes.files;            
            return {
                name: this.get("name"),
                value: signatureValue
            };
        },
        validate: function (attrs) {
            
        },        
        remoteProxyData : function (id){           
            return this.get("project").webServiceManager.imageInfo(id,300);
            /*var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            that= this,
            respData,
            itemElement;
            endpoint = this.getEndpointVariables({
                type: "getImageGeo",
                keys: {                           
                    "{caseID}": prj.keys.caseID,                                                           
                }
            });
            if (PMDynaform.core.ProjectMobile){
                url = prj.getFullURL(endpoint);   
            }else{
                url = prj.getFullURLMobile(prj.endPointsPath.imageInfo);                    
            }
            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data:[{
                    fileId: id,
                    width : "300",
                    version :1
                }],                
                keys: prj.token,
                successCallback: function (xhr, response) {
                    respData = response;
                }
            });
            if (respData){
                return {
                    id: respData[0].fileId,
                    base64: respData[0].fileContent
                };
            }else{
                 return {
                    id: "",
                    base64: ""
                };
            }*/
        },
        remoteGenerateID : function (location){           
            var prj = this.get("project"),
            url,
            restClient,
            that = this,
            endpoint,
            that= this,
            respData;            
            endpoint = this.getEndpointVariable({
                        type: "generateImageGeo",
                        keys: {
                            "{caseID}": prj.keys.caseID                          
                        }
                    });
                    url = prj.getFullURL(endpoint);
            url = prj.getFullURL(endpoint);
            restClient = new PMDynaform.core.Proxy ({
                url: url,
                method: 'POST',
                data:location,                
                keys: prj.token,
                successCallback: function (xhr, response) {
                    respData = response;
                }
            });
            this.set("proxy", restClient);
            return respData;
            
        },
        getEndpointVariables: function (urlObj) {
            var prj = this.get("project"),
            endPointFixed,
            variable,
            endpoint;

            if (prj.endPointsPath[urlObj.type]) {
                endpoint = prj.endPointsPath[urlObj.type]
                for (variable in urlObj.keys) {
                    if (urlObj.keys.hasOwnProperty(variable)) {
                        endPointFixed =endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);  
                        endpoint= endPointFixed;
                    }
                }
            }

            return endPointFixed;
        }   
    });

    PMDynaform.extendNamespace("PMDynaform.model.Signature_mobile", Signature_mobile);
}());
