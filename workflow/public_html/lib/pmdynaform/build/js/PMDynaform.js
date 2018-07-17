
/**
 * @class PMDynaform
 * Base class PMDynaform
 * @singleton
 */

/**
 * @feature support for ie8
 * functions
 */

function getScrollTop() {
    if (typeof pageYOffset != 'undefined') {
        //most browsers except IE before #9
        return pageYOffset;
    }
    else {
        var B = document.body; //IE 'quirks'
        var D = document.documentElement; //IE with doctype
        D = (D.clientHeight) ? D : B;
        return D.scrollTop;
    }
};
//.trim to support ie8
if (!Array.prototype.filter) {
    Array.prototype.filter = function (fun/*, thisArg*/) {
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
    (function () {
        // Make sure we trim BOM and NBSP
        var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
        String.prototype.trim = function () {
            return this.replace(rtrim, '');
        };
    })();
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt /*, from*/) {
        var len = this.length >>> 0;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
            ? Math.ceil(from)
            : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++) {
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
    model: {},
    collection: {},
    Extension: {},
    restData: {},
    activeProject: null,
    FLashMessage: null,
    PATH_RTL_CSS: "css/PMDynaform-rtl.css"
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
        var F = function () {
        };
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
/**
 * Get the keys from active project
 * @returns {*}
 */
PMDynaform.getProjectKeys = function () {
    var resp = null,
        options;
    if (this.activeProject) {
        options = this.activeProject.webServiceManager.options;
        resp = _.extend(options.keys, options.token);
    }
    return resp;
};

/**
 * Set the instance of an active project pmdynaform
 * @param project
 * @returns { null | PMDynaform.core.ProjectMobile | PMDynaform.core.Project }
 */
PMDynaform.setActiveProject = function (project) {
    if ((PMDynaform.core.ProjectMobile && project instanceof PMDynaform.core.ProjectMobile) || project instanceof PMDynaform.core.Project) {
        this.activeProject = project;
        return project;
    }
    return null;
};

/**
 * Get the active project instance of pmdynaform
 * @returns { PMDynaform.core.ProjectMobile | PMDynaform.core.Project }
 */
PMDynaform.getActiveProject = function () {
    return this.activeProject;
};

/**
 * Get the workspace from the project
 * @returns {*}
 */
PMDynaform.getWorkspaceName = function () {
    var resp = null;
    if (this.activeProject) {
        resp = this.activeProject.webServiceManager.getKey("workspace");
    }
    return resp;
};

/**
 * Get the Accestoken from the project
 * @returns {*}
 */
PMDynaform.getAccessToken = function () {
    var resp = null;
    if (this.activeProject) {
        resp = this.activeProject.webServiceManager.getToken()["accessToken"];
    }
    return resp;
};

/**
 * Get the hostName from the project
 * @returns {*}
 */
PMDynaform.getHostName = function () {
    var resp = null;
    if (this.activeProject) {
        resp = this.activeProject.webServiceManager.getKey("server");
    }
    return resp;
};

/**
 * Get the enviroment (desktop, webkit android or iOS)
 * @returns {string}
 */
PMDynaform.getEnvironment = function () {
    var nav = navigator.userAgent,
        resp;
    resp = nav;
    if (nav === 'formslider-ios') {
        resp = "iOS";
    }
    if (nav === 'formslider-android') {
        resp = "android";
    }
    return resp;
};

String.prototype.capitalize = function () {
    return this.toLowerCase().replace(/(^|\s)([a-z])/g, function (m, p1, p2) {
        return p1 + p2.toUpperCase();
    });
};

jQuery.fn.extend({
    setLabel: function (newLabel, col) {
        var field = getFieldById(this.attr("id")) || null;
        if (typeof newLabel === "string" && field) {
            field.setLabel(newLabel, col);
        }
        return this;
    },
    getLabel: function (col) {
        var field = getFieldById(this.attr("id")) || null;
        if (field) {
            return field.getLabel(col);
        }
        return null;
    },
    /**
     * Sets a field's value into a form or grid
     * @param value
     * @param row
     * @param col
     * @returns {jQuery}
     */
    setValue: function (value, row, col) {
        var field = getFieldById(this.attr("id")) || null;
        if (field) {
            if (field.model.get("type") === "grid") {
                field.setValue(value, row, col);
            } else {
                field.setValue(value);
            }
        }
        return this;
    },
    setText: function (value, row, col) {
        var field = getFieldById(this.attr("id")) || null;
        if (field) {
            if (field.model.get("type") === "grid") {
                field.setText(value, row, col);
            } else {
                field.setText(value);
            }
        }
        return this;
    },
    /**
     * Helper for get the value of a Field or Grid
     * @param row
     * @param col
     * @returns {*}
     */
    getValue: function (row, col) {
        var field = getFieldById(this.attr("id")) || null,
            val = "",
            type;
        if (field) {
            type = field.model.get("type");
            if (type === "grid") {
                val = field.getValue(row, col);
            } else {
                val = field.getValue();
            }
        }
        return val;
    },
    /**
     * helper getAppDocUID function to get app document uid as reference to a document
     * @returns {array} val
     */
    getAppDocUID: function () {
        var item,
            val = null;
        if (getFieldById(this.attr("id"))) {
            item = getFieldById(this.attr("id"));
            if (typeof item.model.getAppDocUID === 'function'
                && item.model.getAppDocUID()) {
                val = item.model.getAppDocUID();
            }
        }
        return val;
    },
    /**
     * Helper setOnChange
     * @param handler
     * @returns {jQuery}
     */
    setOnchange: function (handler) {
        var item = this.getIntanceById(this.attr("id"));
        if (item && typeof item.setOnChange === "function") {
            item.setOnChange(handler);
        }
        return this;
    },
    getInfo: function () {
        var field = getFieldById(this.attr("id")) || null;
        if (field) {
            return field.getInfo();
        }
        return null;
    },
    setHref: function (value) {
        var field = getFieldById(this.attr("id")) || null;
        if (field.model.get("type") === "link") {
            field.setHref(value);
        }
        return this;
    },
    getHref: function () {
        var field = getFieldById(this.attr("id")) || null;
        if (field.model.get("type") === "link") {
            return field.getHref();
        }
        return this;
    },
    setRequired: function (field) {
    },
    required: function (field) {
    },
    getText: function (row, col) {
        var field = getFieldById(this.attr("id")) || null,
            typeField,
            val = null;

        if (field) {
            typeField = field.model.get("type");
            if (typeField === "grid") {
                val = field.getText(row, col);
            } else {
                val = field.getText();
            }
        }
        return val;
    },
    disableValidation: function (col) {
        var field = getFieldById(this.attr("id")) || null, val;
        if (field && field.disableValidation) {
            field.disableValidation(col);
        }
        return this;
    },
    enableValidation: function (col) {
        var field = getFieldById(this.attr("id")) || null, val;
        if (field && field.enableValidation) {
            field.enableValidation(col);
        }
        return this;
    },
    /**
     * Helper for get the control of a Field
     * @param row
     * @param col
     * @returns {Array}
     */
    getControl: function (row, col) {
        var field = getFieldById(this.attr("id")) || null,
            control = [],
            type;
        if (field) {
            type = field.model.get("type");
            if (type === "grid") {
                control = field.getControl(row, col);
            } else {
                control = field.getControl();
            }
        }
        return control;
    },
    getLabelControl: function () {
        var field = getFieldById(this.attr("id")) || null, val;
        if (field) {
            field.getLabelControl();
        }
        return this;
    },
    getHintHtml: function () {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field) {
            html = field.$el.find(".glyphicon-info-sign");
        }
        return $(html);
    },
    getSummary: function (col) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            return field.getSummary(col);
        }
        return this;
    },
    getNumberRows: function () {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") === "grid") {
            return field.getNumberRows();
        }
        return this;
    },
    addRow: function (data) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            field.addRow(data);
        }
        return this;
    },
    deleteRow: function (row) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            if (!row) {
                row = field.getNumberRows();
            }
            field.deleteRow(row);
        }
        return this;
    },
    onBeforeAdd: function () {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            if (typeof handler === "function") {
                field.setOnBeforeAddCallback(handler);
            }
        }
    },
    onAddRow: function (handler) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            if (typeof handler === "function") {
                field.setOnAddRowCallback(handler);
            }
        }
    },
    onShowRowDialog: function (handler) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid" && PMDynaform.core.ProjectMobile) {
            if (typeof handler === "function") {
                field.setOnShowRowDialog(handler);
            }
        }
    },
    onDeleteRow: function (handler) {
        var field = getFieldById(this.attr("id")) || null, html = [];
        if (field && field.model.get("type") == "grid") {
            if (typeof handler === "function") {
                field.setOnDeleteRowCallback(handler);
            }
        }
    },
    hideColumn: function (col) {
        var field = getFieldById(this.attr("id")) || null;
        if (field && field.model.get("type") === "grid") {
            field.hideColumn(parseInt(col, 10));
        }
    },
    showColumn: function (col) {
        var field = getFieldById(this.attr("id")) || null;
        if (field && field.model.get("type") === "grid") {
            field.showColumn(parseInt(col, 10));
        }
    },
    getData: function () {
        var field = getFieldById(this.attr("id")) || null, val;
        if (field && field.getData) {
            return field.getData();
        }
        return this;
    },
    getDataLabel: function () {
        var field = getFieldById(this.attr("id")) || null, val;
        if (field && field.getDataLabel) {
            return field.getDataLabel();
        }
        return this;
    },
    getForm: function () {
        var form;
        if (this.length) {
            form = getFormById(this.attr('id') || '') || null;
            return form;
        }
    },
    submitForm: function () {
        var project,
            form = this.getForm();
        if (form) {
            project = form.project;
            if (project && !project.isMobile()) {
                if (form.isValid()) {
                    form.submitNextStep();
                }
            } else {
                form.onSubmit();
            }
        }
    },

    saveForm: function () {
        var form;
        form = getFormById(this.attr("id"));
        if (form) {
            form.saveForm();
        }
        return this;
    },
    /**
     * Set a callback in submit action
     * @param callback
     * @returns {jQuery}
     */
    setOnSubmit: function (callback) {
        var form;
        form = getFormById(this.attr("id"));
        if (form) {
            form.setOnSubmit(callback);
        }
        return this;
    },
    _getJSONFormValues: function (elements) {
        var i;
        var data = {};
        if (elements.length > 0) {
            for (i = 0; i < elements.length; i += 1) {
                data[elements[i].name] = elements[i].value;
            }
        }
        return data;
    },
    /**
     * This is a help function to close the form, supported for mobile version
     * @returns {jQuery}
     */
    closeForm: function () {
        var form = getFormById(this.attr("id"));
        if (form && form instanceof PMDynaform.view.FormPanel) {
            form.close();
        }
        return this;
    },
    /**
     * Show Modal Loading
     * @returns {jQuery}
     */
    showFormModal: function () {
        var form = this.getForm(), modal;
        if (form) {
            modal = form.project.modalProgress;
            modal.render();
        }
        return this;
    },
    /**
     * Hide Modal Loading
     * @returns {jQuery}
     */
    hideFormModal: function () {
        var form = this.getForm(), modal;
        if (form) {
            modal = form.project.modalProgress;
            modal.hide();
        }
        return this;
    },
    /**
     * Hide New Button of the Grid
     * @returns {jQuery}
     */
    hideNewButton: function () {
        var field = getFieldById(this.attr("id")) || null,
            actionButton = "add";
        if (field && field.model.get("type") === "grid") {
            field.hideButton(actionButton);
        }
        return this;
    },
    /**
     * Show New Button of the Grid
     * @returns {jQuery}
     */
    showNewButton: function () {
        var field = getFieldById(this.attr("id")) || null,
            actionButton = "add";
        if (field && field.model.get("type") === "grid") {
            field.showButton(actionButton);
        }
        return this;
    },
    /**
     * Clear Content File
     * @param row
     * @param col
     * @returns {jQuery}
     */
    clear: function (row, col) {
        var field = getFieldById(this.attr("id")) || null,
            type;
        if (field) {
            type = field.model.get("type");
            if (type === "grid") {
                field.clearContent(row, col);
            } else {
                field.clearContent();
            }
        }
        return this;
    },
    /**
     * Get Instance by Id (Form or Field)
     * @returns {*}
     */
    getIntanceById: function (idItem) {
        var idInstance = idItem || null,
            instanceResult = getFormById(idInstance) || getFieldById(idInstance);
        return instanceResult || null;
    },
    /**
     * Returns all the forms fields, including the ones in any nested subform.
     * @param id The form's id.
     * @returns {Array<T>PMDynaform.view.Field}
     */
    getFields: function (id) {
        var form = getFormById(this.attr("id"))
            || getFieldById(this.attr("id"));

        return (form && form.getAllFields()) || [];
    }
});


(function () {
    var InputsValidation = function () {
        var config = {
            "data": {
                "name": "",
                "description": "",
                "items": [
                    {
                        "type": "form",
                        "variable": "",
                        "var_uid": "",
                        "dataType": "",
                        "id": "",
                        "name": "",
                        "description": "",
                        "mode": "edit",
                        "script": "",
                        "language": "en",
                        "externalLibs": "",
                        "printable": false,
                        "items": [],
                        "variables": []
                    }
                ]
            },
            "delIndex": 0,
            "dynaformUid": "",
            "keys": {
                "server": (location.protocol + "//" + window.location.host),
                "projectId": "",
                "workspace": "workflow"
            },
            "token": {
                "accessToken": "",
                "expiresIn": 0,
                "tokenType": "bearer",
                "scope": "",
                "refreshToken": "",
                "clientId": "",
                "clientSecret": ""
            },
            isPreview : false,
            isRTL: false,
            language: null,
            formAction: null,
            formAjax: null,
            submitRest: false,
            globalMode: null,
            externalLibs: "",
            renderTo: document.body,
            onLoad: new Function()
        };
        return {
            getDefaultData : function(){
                return config;
            }
        };
    };
    PMDynaform.extendNamespace("PMDynaform.util.InputsValidation", InputsValidation);
}());

/**
 * Singleton for implement the flow Case independent
 * @type {{VERSION: string, view: {}, model: {}, collection: {}, Extension: {}, restData: {}, activeProject: null, FLashMessage: null}}
 */
var xCase = {
    VERSION: "0.1.0",
    view: {},
    model: {},
    collection: {},
    Extension: {},
    restData: {},
    activeProject: null,
    FLashMessage: null
};

xCase.extendNamespace = function (path, newClass) {
    var current,
        pathArray,
        extension,
        i;

    if (arguments.length !== 2) {
        throw new Error("xCase.extendNamespace(): method needs 2 arguments");
    }

    pathArray = path.split('.');
    if (pathArray[0] === 'xCase') {
        pathArray = pathArray.slice(1);
    }
    current = xCase;

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
(function () {
    /*
     * @param {String}
     * The following key selectors are availables for the
     * getField and getGridField methods
     * - Using '#', is possible select a field with the identifier of the field
     * - Using ''. is possible select a field with the className of the field
     * - Putting 'attr[name="my-name"]' is possible select fields with the same name attribute
     **/
    var Selector = function (options) {
        this.onSupportSelectorFields = null;
        this.fields = {};
        this.queries = [];
        this.form = {};

        Selector.prototype.init.call(this, options);
    };
    /**
     * Initializes properties of the selector
     * @param options
     */
    Selector.prototype.init = function (options) {
        var defaults = {
            fields: {},
            queries: [],
            form: {},
            onSupportSelectorFields: {
                text: "onTextField",
                textarea: "onTextAreaField"
            }
        };

        $.extend(true, defaults, options);

        this.setOnSupportSelectorFields(defaults.onSupportSelectorFields)
            .setFields(defaults.fields)
            .setForms(defaults.form)
            .applyGlobalSelectors();
    };
    Selector.prototype.addQuery = function (query) {
        if (typeof query === "string") {
            this.queries.push(query);
        } else {
            throw new Error("The query selector must be a string");
        }

        return this;
    };
    Selector.prototype.setOnSupportSelectorFields = function (support) {
        if (typeof support === "object") {
            this.onSupportSelectorFields = support;
        } else {
            throw new Error("The parameter for the support fields is wrong");
        }

        return this;
    };
    /**
     * Sets fields
     * @param fields
     * @returns {Selector}
     */
    Selector.prototype.setFields = function (fields) {
        if (typeof fields === "object") {
            this.fields = fields;
        }
        return this;
    };
    /**
     * Sets form
     * @param form
     * @returns {Selector}
     */
    Selector.prototype.setForms = function (form) {
        if (typeof form === "object") {
            this.form = form;
        }
        return this;
    };
    Selector.prototype.onTextField = function () {
        return this;
    };
    Selector.prototype.onTextAreaField = function () {
        return this;
    };
    /**
     * Gets field instance searched
     * @param selectorId
     * @returns {object || null}
     */
    Selector.prototype.findFieldById = function (selectorId) {
        return selectorId && this.fields.hasOwnProperty(selectorId) ?
            this.fields[selectorId] : null;
    };
    /**
     * Gets form instance by id
     * @param selectorId
     * @returns {object || null}
     */
    Selector.prototype.findFormById = function (selectorId) {
        return selectorId && this.form.model.get("id") === selectorId ?
            this.form : null;
    };
    /**
     * Gets fields searched by name
     * @param selectorAttr
     * @returns {Array}
     */
    Selector.prototype.findFieldByName = function (selectorAttr) {
        var prop,
            fieldFinded = [];

        for (prop in this.fields) {
            if (this.fields[prop].model.get("name") === selectorAttr) {
                fieldFinded.push(this.fields[prop]);
            }
        }
        return fieldFinded;
    };
    /**
     * Gets fields by attribute
     * @param parameter
     * @param value
     * @returns {Array}
     */
    Selector.prototype.findFieldByAttribute = function (parameter, value) {
        var prop,
            fieldFinded = [],
            modelField;
        for (prop in this.fields) {
            modelField = this.fields[prop].model;
            if (value && modelField.attributes.hasOwnProperty(parameter) && modelField.get(parameter) === value) {
                fieldFinded.push(this.fields[prop]);
            }
        }
        return fieldFinded;
    };
    /**
     * findFieldByVariable: Gets a field considering the variable name as a parameter
     * @returns {object}
     */
    Selector.prototype.findFieldByVariable = function(selectorAttr) {
        var prop,
            fieldFinded;
        for (prop in this.fields) {
            if (this.fields[prop].model.get("variable") === selectorAttr) {
                fieldFinded = this.fields[prop];
                break;
            }
        }
        return fieldFinded;
    };
    Selector.prototype.applyGlobalSelectors = function () {
        var that = this;

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
        };
        /**
         * getFieldByVariable :Gets a field searched by the variable
         * @returns {object|undefined}
         */
        window.getFieldByVariable = function(query) {
            that.addQuery("name: " + query);
            return that.findFieldByVariable(query);
        };
        /**
         * get the subform with the supplied id
         * @param id
         * @returns {PMDynaform.view.SubForm|null}
         */
        window.getSubformById = function (id) {
            return that.findFieldById(id);
        };
        return this;
    };

    PMDynaform.extendNamespace("PMDynaform.core.Selector", Selector);
}());

(function () {

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

            return "PMD-" + sUID;
        },
        generateName: function (type) {
            return type + "[" + PMDynaform.core.Utils.generateID() + "]";
        },
        /**
         * validate JSON parse
         * @param str
         * @returns {boolean}
         */
        isJsonAndParse: function (str) {
            var result;
            try {
                result = JSON.parse(str);
            } catch (e) {
                result = str.split(',');
            }
            return result;
        },
        /**
         * check if it is a valid version of Internet Explorer for pmdynaform
         */
        checkValidIEVersion: function () {
            var version = false,
                appName = navigator.appName,
                appVersion = navigator.appVersion;
            if (appName == "Netscape") {
                if (appVersion.indexOf('Trident') !== -1){
                    version = 11;
                }
                if(appVersion.indexOf('Edge') !== -1){
                    version = 12;
                }
            }
            return version;
        }
    };
    PMDynaform.extendNamespace("PMDynaform.core.Utils", Utils);

}());
(function () {
    /**
     * @class PMDynaform.util.ExternalLibraries
     * Class that manages all external resources used and presented in json definition.
     *
     * The external libraries are used into a form as a externalLib property.
     *
     * The external library has the following types:
     *  - Regular js files
     *  - Regular css files
     *  - Images
     * Example to use:
     *      // e.g.
     *      // let's assume that there are an arroy of external resources
     *      // let's assume that callback is callback function to render after all resources has been load
     *
     *  this.externalLibraries  = new PMDynaform.util.ExternalLibraries({
     *           "libs": libs,
     *           "afterLoad": callback
     *   });
     * @param options
     * @constructor
     */
    var ExternalLibraries = function (options) {
        this.libs = [];
        this.cachedLibs = [];
        this.afterLoad = null;

        ExternalLibraries.prototype.init.call(this, options);
    };
    /**
     * This function init the class External libraries
     * * @param options
     * @returns {CaseManager}
     */
    ExternalLibraries.prototype.init = function (options) {
        var defaults = {
            "libs": [],
            "afterLoad": null
        };
        defaults = _.extend(defaults, options);
        this.setLibs(defaults.libs)
            .setAfterLoad(defaults.afterLoad);
        return this;
    };
    /**
     * Loads all scripts, an arroy of all libraries is used to do that
     * after has been load completly all libraries the collback afterLoad is executed
     * @returns {ExternalLibraries}
     */
    ExternalLibraries.prototype.setExternalLibreries = function (i) {
        var that = this, link;
        if (that.libs && i < that.libs.length) {
            link = that.onLoadScript(that.libs[i]);
            if (link) {
                link.onload = function () {
                    that.setExternalLibreries(i + 1);
                };
                link.onerror = function () {
                    console.error('invalid link :' + that.libs[i].url);
                    that.setExternalLibreries(i + 1);
                };
            } else {
                that.setExternalLibreries(i + 1);
            }
        } else {
            this.afterLoad();
        }
    };
    /**
     * Hook to execute all scripts and call loadjscssfile method
     * to load the external libraries and that is stored in cachedLibs property
     * @param lib
     * @returns {ExternalLibraries}
     */
    ExternalLibraries.prototype.onLoadScript = function (lib) {
        var type = lib.url.substring(lib.url.lastIndexOf(".") + 1);
        this.cachedLibs.push(lib);
        return this.loadjscssfile(lib.url, type);
    };
    /**
     * Append css or js external libraries to html head
     * if filetype is not js or css return null,
     * @param filename
     * @param filetype
     * @returns {*}
     */
    ExternalLibraries.prototype.loadjscssfile = function (filename, filetype) {
        var fileref = null;
        if (filetype === "js") { //if filename is a external JavaScript file
            fileref = document.createElement('script');
            fileref.setAttribute("type", "text/javascript");
            fileref.setAttribute("src", filename);
        } else if (filetype === "css") { //if filename is an external CSS file
            fileref = document.createElement("link");
            fileref.setAttribute("rel", "stylesheet");
            fileref.setAttribute("type", "text/css");
            fileref.setAttribute("href", filename);
        }
        if (fileref && fileref !== "undefined") {
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
        return fileref;
    };

    /**
     * Sets the array libs property
     * @param libs
     * @returns {ExternalLibraries}
     */
    ExternalLibraries.prototype.setLibs = function (libs) {
        if (_.isArray(libs)) {
            this.libs = libs;
        }
        return this;
    };
    /**
     * Sets the afterLoad callback as a external library property
     * @param callback
     * @returns {ExternalLibraries}
     */
    ExternalLibraries.prototype.setAfterLoad = function (callback) {
        if (_.isFunction(callback)) {
            this.afterLoad = callback;
        }
        return this;
    };
    /**
     * Gets the External library cachedLibs property
     * @returns {Array}
     */
    ExternalLibraries.prototype.getCachedLibs = function () {
        return this.cachedLibs;
    };
    /**
     * Clean the External library cachedLibs property
     */
    ExternalLibraries.prototype.clearCachedLibs = function () {
        this.cachedLibs = [];
    };

    PMDynaform.extendNamespace("PMDynaform.util.ExternalLibraries", ExternalLibraries);
}());
(function () {
    var Validators = {
        requiredText: {
            message: "This field is required",
            fn: function (val) {
                var value = val;
                if (_.isNumber(val)) {
                    value = val.toString();
                }
                value = value.trim();
                if (value === null || value.length === 0 || /^\s+$/.test(value)) {
                    return false;
                }
                return true;
            }
        },
        requiredDropDown: {
            message: "This field is required",
            fn: function (value) {
                value = typeof value === 'string' ? value.trim() : value;
                return !!value || typeof value === 'number';
            }
        },
        requiredCheckBox: {
            message: "This field is required",
            fn: function (value) {
                if (typeof value === "number") {
                    var bool = (value > 0) ? true : false;
                } else {
                    bool = false;
                }
                return bool;
            }
        },
        requiredCheckGroup: {
            message: "This field is required",
            fn: function (value) {
                if (typeof value === "number") {
                    var bool = (value > 0) ? true : false;
                } else {
                    bool = false;
                }
                return bool;
            }
        },
        requiredFile: {
            message: "This field is required",
            fn: function (value) {
                value = value.trim();
                if (value === null || value.length === 0 || /^\s+$/.test(value)) {
                    return false;
                }
                return true;
            }
        },
        requiredRadioGroup: {
            message: "This field is required",
            fn: function (value) {
                value = typeof value === 'string' ? value.trim() : value;
                return !!value || typeof value === 'number';
            }
        },
        integer: {
            message: "Invalid value for the integer field",
            mask: /[\d\.]/i,
            fn: function (n) {
                return (typeof n === 'string') ? /^-?\d+$/.test(n) : !isNaN(n = parseFloat(n, 10) && n % 1 === 0);
            }
        },
        float: {
            message: "Invalid value for the float field",
            fn: function (n) {
                return /^-?\d+\.?\d*$/.test(n);
            }
        },
        string: {
            fn: function (string) {
                return true;
            }
        },
        boolean: {
            fn: function (string) {
                return true;
            }
        },
        maxLength: {
            message: "The maximum length are ",
            fn: function (value, maxLength) {
                var maxLen;
                if (typeof maxLength !== "number") {
                    throw new Error("The parameter maxlength is not a number");
                }
                maxLen = (value.toString().length <= maxLength) ? true : false;
                return maxLen;
            }
        },
        /**
         * validate that there is at least one row on the grid
         * return [boolean]
         */
        requiredGrid: {
            message: "Information Required",
            fn: function (value) {
                if (value === null || value === 0) {
                    return false;
                }
                return true;
            }
        }
    };

    PMDynaform.extendNamespace("PMDynaform.core.Validators", Validators);
}());

(function () {
    var ModalProgressBar = Backbone.View.extend({
        timeHide: 1000,
        template: _.template($("#tpl-modal-global").html()),
        initialize: function () {
            //TODO: no need params.
        },
        render: function () {
            if ($('#modalProgressBar').length) {
                $('#modalProgressBar').remove();
            }
            $('body').append(this.template());
            this.show();
            return this;
        },
        show: function () {
            $('#modalProgressBar').modal({backdrop: 'static', keyboard: false}, 'show');
            return this;
        },
        hide: function () {
            if ($('#modalProgressBar').length) {
                setTimeout(function () {
                    $('#modalProgressBar').modal('hide');
                }, this.timeHide);
            }
            return this;
        },
        setTimeHide: function (timeHide) {
            this.timeHide = timeHide;
            return this;
        },
        getTimeHide: function () {
            return this.timeHide;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.ModalProgressBar", ModalProgressBar);
}());
(function () {
    var Project = function (options) {
        this.model = null;
        this.modalProgress = null;
        this.view = null;
        this.data = null;
        this.delIndex = null;
        this.fields = null;
        this.keys = null;
        this.token = null;
        this.renderTo = null;
        this.urlFormat = null;
        this.endPointsPath = null;
        this.forms = null;
        this.externalLibs = null;
        this.externalLibsArray = [];
        this.dependentLibraries = null;
        this.submitRest = null;
        this.formAjax = null;
        this.globalMode = null;
        this.onSubmitForm = new Function();
        this.language = "";
        this.onBeforePrintHandler = null;
        this.onAfterPrintHanlder = null;
        this.flashView = null;
        this.isRTL = false;
        this.isPreview = false;
        this.dynaformUid = null;
        Project.prototype.init.call(this, options);
    };

    Project.prototype.init = function (options) {
        var defaults = new PMDynaform.util.InputsValidation(),
            that = this;
        defaults = jQuery.extend(true, defaults.getDefaultData(), options);
        defaults.endPointsPath = {
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
            fileStreaming: "en/neoclassic/cases/casesStreamingFile?actionAjax=streaming&a={caseID}&d={fileId}",
            getAllDataCase: "case/{caseID}/variables",
            imageDownload: 'light/case/{caseID}/download64',
            fileDownload: "case/{caseID}/file/{fileID}",
            imageInfo: "light/case/{caseID}/download64",
            getImageGeo: "light/case/{caseID}/download64"
        };
        defaults.urlFormatMobile = "{server}/api/1.0/{workspace}/{endPointPath}";
        defaults.urlFormat = "{server}/{apiName}/{apiVersion}/{workspace}/{keyProject}/{projectId}/{endPointPath}";
        this.urlFormatStreaming = "{server}/sys{workspace}/{endPointPath}";
        that.setIsRTL(defaults.isRTL);
        that.setIsPreview(defaults.isPreview);
        $("body").append("<div class='pmDynaformLoading'></div>");
        this.loadExternalLibs(defaults.data, defaults.keys.server, function () {
            that.delIndex = defaults.delIndex;
            that.setDynaformUID(defaults.dynaformUid);
            that.setFormAjax(defaults.formAjax);
            that.setBeforePrintHandler(defaults.onBeforePrintHandler);
            that.setAfterPrintHandler(defaults.onAfterPrintHandler);
            that.setData(defaults.data);
            that.setLanguage();
            that.initModalProgress();
            that.setUrlFormat(defaults.urlFormat);
            that.setUrlFormatMobile(defaults.urlFormatMobile);
            that.setKeys(defaults.keys);
            that.setToken(defaults.token);
            that.setRenderTo(defaults.renderTo);
            that.setEndPointsPath(defaults.endPointsPath);
            that.createWebServiceManager();
            PMDynaform.setActiveProject(that);
            that.checkMobileData();
            that.submitRest = defaults.submitRest;
            if (!PMDynaform.core.ProjectMobile) {
                that.checkGeoMapsLibraries(defaults.onLoad);
            } else {
                that.checkGeoMapsLibraries();
            }
            //stop loading
            $("body").find(".pmDynaformLoading").remove();
        });
    };
    Project.prototype.setDynaformUID = function (dynUid) {
        var dyn_uid = null;
        if (dynUid || dynUid === null) {
            dyn_uid = dynUid;
        }
        this.dynaformUid = dyn_uid;
        return this;
    };
    /**
     * sets the value true if the project is a preview of processmaker
     * and false if it is running case
     * @param value, true or false
     * @returns {Project}
     */
    Project.prototype.setIsPreview = function (value) {
        if ((typeof value === "boolean") && value) {
            this.isPreview = true;
        } else {
            this.isPreview = false;
        }
        return this;
    };
    /**
     * setIsRTL
     * if the project is actually owned RTL this fixed property to adds
     * a stylesheet to support RTL
     */
    Project.prototype.setIsRTL = function (value) {
        if (typeof value === "boolean") {
            this.isRTL = value;
        } else {
            if (window.isRTL != undefined && window.isRTL != null) {
                this.isRTL = typeof window.isRTL === "boolean" ? window.isRTL : false;
            }
        }
        if (this.isRTL) {
            this.addCSSToRTL();
        } else {
            this.removeCSSToRTL();
        }
    };
    /**
     * getIsRTL
     * get the value of the property isRTL, this can be true or false
     */
    Project.prototype.getIsRTL = function () {
        return this.isRTL;
    };
    /**
     * addCSSToRTL
     * add a stylesheet with support of view (right to left)
     */
    Project.prototype.addCSSToRTL = function () {
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.id = "rtl-style";
        if (window.pathRTLCss !== undefined && window.pathRTLCss !== null) {
            link.href = window.pathRTLCss;
            PMDynaform.PATH_RTL_CSS = window.pathRTLCss;
        } else {
            link.href = PMDynaform.PATH_RTL_CSS || "";
        }
        document.head.appendChild(link);
        return this;
    };
    /**
     * Remove CSS RTL
     * @returns {Project}
     */
    Project.prototype.removeCSSToRTL = function () {
        var link = $("#rtl-style");
        if (link) {
            link.remove();
        }
        return this;
    };

    Project.prototype.setFormAjax = function (params) {
        if (params) {
            this.formAjax = params;
        }
        return this;
    };
    Project.prototype.getFormAjax = function () {
        return this.formAjax;
    };
    Project.prototype.initModalProgress = function () {
        this.modalProgress = new PMDynaform.view.ModalProgressBar();
        return this;
    };
    /**
     * @param globalMode
     * @returns {Project}
     */
    Project.prototype.setGlobalMode = function (globalMode) {
        if (globalMode) {
            this.globalMode = globalMode;
        }
        return this;
    };
    Project.prototype.checkMobileData = function () {
        if (!PMDynaform.core.ProjectMobile) {
            this.mobileDataControls = this.loadAllDataCase();
        }
        return this;
    };
    Project.prototype.setLanguage = function () {
        if (window.sysLang) {
            this.language = window.sysLang;
        }
        return this;
    };

    /**
     * Loads external libraries, here instance ExternalLibraries class.
     * @param jsonForm
     * @param callback
     */
    Project.prototype.loadExternalLibs = function (jsonForm, server, callback) {
        var libs = this.prepareLibsPath(jsonForm, server);
        this.externalLibraries = new PMDynaform.util.ExternalLibraries({
            "libs": libs,
            "afterLoad": callback
        });
        this.externalLibraries.setExternalLibreries(0);

    };
    /**
     * Prepare external libraries path
     * @returns {Array}
     */
    Project.prototype.prepareLibsPath = function (jsonForm, server) {
        var expression = /^(?:(http|https):)?(\/{2,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
        this.getAllExternalLibs(jsonForm, true);
        return _.map(this.externalLibsArray, function (lib) {
            lib = $.trim(lib);
            if (!expression.test(lib) && lib !== "") {
                lib = (lib.charAt(0) !== '/') ? '/' + lib : lib;
                lib = server + lib;

            }
            return {
                "url": lib,
                "skipCache": true
            };
        });
    };
    /**
     * Gets all external libraries considering json definition, that method is recursive.
     * @param jsonForm
     * @param externalLibs
     * @param root
     * @returns {*}
     */
    Project.prototype.getAllExternalLibs = function (jsonForm, root) {
        var i,
            form,
            result = "",
            max;
        if (root) {
            if (_.isArray(jsonForm.items)) {
                for (i = 0, max = jsonForm.items.length; i < max; i += 1) {
                    form = jsonForm.items[i];
                    if (form.type === 'form') {
                        result = (typeof form.externalLibs !== 'undefined' && form.externalLibs !== null) ?
                            form.externalLibs.replace(/\s+/g, '') : result;
                        if (result !== "") {
                            result = result.split(",");
                            this.externalLibsArray = _.union(this.externalLibsArray, result);
                        }
                        if (form.items && form.items.length > 0) {
                            this.getAllExternalLibs(form.items, false);
                        }

                    }
                }
            }
        } else {
            for (i = 0, max = jsonForm.length; i < max; i += 1) {
                form = jsonForm[i];
                if (form[0].items && form[0].items.length > 0) {
                    if (form[0].type === 'form') {
                        result = (typeof form[0].externalLibs !== 'undefined' && form[0].externalLibs !== null) ?
                            form[0].externalLibs.replace(/\s+/g, '') : result;
                        if (result !== "") {
                            result = result.split(",");
                            this.externalLibsArray = _.union(this.externalLibsArray, result);
                            this.getAllExternalLibs(form[0], true);
                        }

                    }
                }
            }
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
    /**
     * setAppData Sets the data to the form
     * @param {object} Set of valid data for the form
     */
    Project.prototype.setAppData = function(data) {
        var forms = this.getForms(),
            firstForm = 0;
        if (_.isArray(forms) && forms.length) {
            forms[firstForm].setAppData(data);
        }
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
        if (!PMDynaform.core.ProjectMobile) {
            if (keys.server.indexOf("http://") == -1 || keys.server.indexOf("https://") == -1) {
                keys.server = keys.server;
            }
        }
        if (typeof keys === "object") {
            for (key in keys) {
                leftBracket = (keys[key][0] === "/") ? keys[key].substring(1) : keys[key];
                keysFixed[key] = (leftBracket[leftBracket.length - 1] === "/") ? leftBracket.substring(0, leftBracket.length - 1) : leftBracket;
            }
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
            searchingMap(forms[i].items || []);
        }

        if (enableGeoMap) {
            this.loadGeoMapDependencies(onload);
        } else {
            this.loadProject(onload);
        }

        return this;
    };
    Project.prototype.checkScript = function () {
        var i, j, k, code, model, scriptCode = "", rows, item, type, row;
        for (i = 0; i < this.forms.length; i += 1) {
            rows = this.forms[i].model.get("items");
            if (!_.isEmpty(this.forms[i].model.get("script"))) {
                scriptCode = scriptCode.concat(this.forms[i].model.get("script").code);
            }
            for (j = 0; j < rows.length; j += 1) {
                row = rows[j];
                for (var k = 0; k < row.length; k += 1) {
                    item = row[k];
                    if (item.type === "form") {
                        if (!_.isEmpty(item.script)) {
                            scriptCode = scriptCode.concat(item.script.code);
                        }
                    }
                }
            }
        }
        if (scriptCode.trim().length) {
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
        }
        return this;
    };
    Project.prototype.getModelForm = function (index) {
        if (this.data.items[index] !== undefined) {
            return this.data.items[index];
        } else {
            return false;
        }
    };

    Project.prototype.loadProject = function (onload) {
        var that = this, firstForm;
        firstForm = this.getModelForm(0);
        if (firstForm) {
            if (typeof this.onBeforePrintHandler === "function") {
                firstForm.onBeforePrintHandler = this.onBeforePrintHandler;
            }
            if (typeof this.onAfterPrintHandler === "function") {
                firstForm.onAfterPrintHandler = this.onAfterPrintHandler;
            }
        }
        this.model = new PMDynaform.model.Panel(this.data);
        this.view = new PMDynaform.view.Panel({
            tagName: "div",
            renderTo: this.renderTo,
            model: this.model,
            project: this
        });
        this.flashMessage();
        if (onload && typeof onload === "function") {
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
        this.onScrollUpdate();
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
    /**
     * Create selector instance
     * @returns {Project}
     */
    Project.prototype.createSelectors = function () {
        var currentForm = this.getForm(),
            allFields = this.getAllItems(currentForm);

        this.fields = allFields;
        this.selector = new PMDynaform.core.Selector({
            fields: allFields,
            form: currentForm
        });
        return this;
    };
    /**
     * Creates a object with all children fields of the
     * form (included the fields into SubForms and SubForms objects) ordered by id.
     * Example:
     * {
     *    "textVar001": [Object View],
     *    "textareaVar001": [Object View],
     *    "suggestVar001": [Object View],
     *    "dropdownVar001": [Object View],
     *    "checkboxVar001": [Object View],
     *    "32123154646546545644565": [Object View], //SubFormId
     *    "textareaSubForm": [object View] // Children SubForm
     *    ...
     * }
     * @param form
     * @returns {{}}
     */
    Project.prototype.getAllItems = function (form) {
        var that = this,
            formItems = form ? form.getFields() : [],
            type,
            items = {},
            subItems = {};

        if (formItems.length) {
            $.each(formItems, function (index, item) {
                type = item.model.get("type");
                if (type !== "empty") {
                    items[item.model.get("id")] = item;
                    if (type === "form") {
                        subItems = that.getAllItems(item.getFormView());
                    }
                    $.extend(items, subItems);
                }
            });
        }
        return items;
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
    /**
     * Gets the current form
     * @returns {object || null}
     */
    Project.prototype.getForm = function () {
        var forms = this.getForms(),
            index = 0;
        return forms && forms.length ? forms[index] : null;
    };
    /**
     * Gets Array forms
     * @returns {array || null}
     */
    Project.prototype.getForms = function () {
        var forms;
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
    Project.prototype.loadAllDataCase = function () {
        var restClient, endpoint, url, that = this, resp = {};
        if (window.app_uid) {
            this.webServiceManager.getData(function (err, data) {
                if (!err) {
                    resp = data;
                }
            });
        }
        return resp;
    };

    Project.prototype.getFullEndPoint = function (urlEndpoint) {
        var k,
            keys = this.keys,
            urlFormat = urlEndpoint;
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{" + k + "}", "g"), keys[k]);
            }
        }
        return urlFormat;
    };
    Project.prototype.getFullURLMobile = function (endpoint) {
        var k,
            keys = this.keys,
            urlFormat = this.urlFormatMobile;
        urlFormat = urlFormat.replace(/{endPointPath}/, endpoint);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{" + k + "}", "g"), keys[k]);
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
        if (urlFormat.indexOf("file") > -1) {
            urlFormat = urlFormat.replace(/file/g, "http");
        }
        return urlFormat;
    };

    Project.prototype.getFullURLStreaming = function (endpoint) {
        var k,
            keys = this.keys,
            urlFormat = this.urlFormatStreaming;
        urlFormat = urlFormat.replace(/{endPointPath}/, endpoint);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{" + k + "}", "g"), keys[k]);
            }
        }
        urlFormat = window.location.protocol + "//" + urlFormat.replace(/{endPointPath}/, endpoint);
        return urlFormat;
    };
    Project.prototype.createWebServiceManager = function () {
        var keys1 = {
            server: this.keys.server,
            processUID: this.keys.projectId,
            taskUID: window.app_uid ? window.app_uid : null,
            caseUID: window.app_uid ? window.app_uid : null,
            workspace: this.keys.workspace,
            formUID: null,
            keyProject: "project",
            stepID: null,
            delIndex: this.delIndex,
            dyn_uid: this.dynaformUid
        };

        this.webServiceManager = new xCase.service.WebServiceManager({
            keys: keys1,
            token: this.token,
            language: this.language
        });
    };
    Project.prototype.setBeforePrintHandler = function (handler) {
        if (typeof handler === "function") {
            this.onBeforePrintHandler = handler;
        } else {
            handler = null;
        }
        return this;
    };
    Project.prototype.setAfterPrintHandler = function (handler) {
        if (typeof handler === "function") {
            this.onAfterPrintHandler = handler;
        } else {
            handler = null;
        }
        return this;
    };
    Project.prototype.flashMessage = function (config) {
        if (typeof config === "object") {
            if (!Project.flashMessage) {
                this.flashModel = new PMDynaform.ui.FlashMessageModel({
                    message: config.message || "",
                    emphasisMessage: config.emphasisMessage || "",
                    startAnimation: config.startAnimation || 1000,
                    type: config.type || "info",
                    appendTo: config.appendTo || document.body,
                    duration: config.duration,
                    absoluteTop: config.absoluteTop || false
                });
                this.flashView = new PMDynaform.ui.FlashMessageView({
                    model: this.flashModel
                });
            } else {
                this.configFlashMessage(config);
            }
            this.flashView.render();
            if (this.flashModel.get("absoluteTop")) {
                this.onScrollUpdate(this.flashView.el);
            }
        }
        return this;
    };
    Project.prototype.configFlashMessage = function (config) {
        if (this.flashModel && this.flashModel instanceof PMDynaform.ui.FlashMessageModel) {
            this.flashModel.set("message", config.message || "undefined message");
            this.flashModel.set("emphasisMessage", config.emphasisMessage || "undefined emphasisMessage");
            this.flashModel.set("startAnimation", config.startAnimation || 500);
            this.flashModel.set("type", config.type || "info");
            this.flashModel.set("appendTo", config.appendTo || document.body);
            this.flashModel.set("duration", config.duration || 1500);
            this.flashModel.set("absoluteTop", config.absoluteTop || false);
        }
        return this;
    };
    Project.prototype.hideCalendars = function (exclude) {
        var dateTimePicker,
            picker,
            i;

        dateTimePicker = $(document).find(".datetime-container").children();
        for (i = 0; i < dateTimePicker.length; i += 1) {
            if (dateTimePicker.get(i) === exclude) {
                continue;
            }
            if (dateTimePicker.eq(i).data) {
                picker = dateTimePicker.eq(i).data().DateTimePicker;
                if (picker) {
                    picker.hide();
                }
            }
        }
    };
    Project.prototype.onScrollUpdate = function (element) {
        var that = this;

        if (!this.isMobile()) {
            $(window).scroll(function () {
                if (element) {
                    element.style.top = $(document).scrollTop() + "px";
                }
                that.hideCalendars();
            });
        }
    };
    Project.prototype.isMobile = function () {
        return !!PMDynaform.core.ProjectMobile;
    };

    /**
     * getDynUID: Get the form id, to consume services
     * @returns {string}
     */
    Project.prototype.getDynUID = function() {
        var content = this.data,
            masterFormIndex = 0;
        if (content && _.isArray(content.items) && content.items[masterFormIndex]) {
            return content.items[masterFormIndex].id;
        }
        return null;
    };

    PMDynaform.extendNamespace("PMDynaform.core.Project", Project);

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

        expr = this.fields.toString().replace(/,/g, "|");
        expr = expr.replace(new RegExp("\\[", "g"), "\\[");
        expr = expr.replace(new RegExp("\\]", "g"), "\\]");
        this.tokens[name] = expr;
    };
    /**
     * Sets the value for the field selected
     * @param {String} name Corresponds to name of the field
     * @param {String||Number} value Value for the field
     * @private
     */
    Tokenizer.prototype.addTokenValue = function (name, value) {
        this.tokenFields[name] = parseFloat(value);
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
            tokenRegex.push('(' + this.tokens[tokenName] + ')');
        }

        this.regex = new RegExp(tokenRegex.join('|'), 'g');
    };
    /**
     * Find the tokens based of the data parameter and build an tokens array
     * @param {String} data
     * @private
     */
    Tokenizer.prototype.findTokens = function (data) {
        var tokens = [],
            match,
            group;

        while ((match = this.regex.exec(data)) !== null) {
            if (match === undefined) {
                continue;
            }

            for (group = 1; group < match.length; group += 1) {
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
    Formula.prototype.consumeConstant = function (token) {
        return 'Math.' + token.data.toUpperCase();
    };
    /**
     * Gets the valid value for the field passed as parameter
     * @param {String} token Token that represent the data of the field
     * @return {String}
     */
    Formula.prototype.consumeField = function (token) {
        return (this.tokenizer.tokenFields[token.data] === undefined) ? 0 : this.tokenizer.tokenFields[token.data];
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
            t,
            message,
            auxExpr;

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
        auxExpr = [];
        for (var i = 0; i < expr.length; i += 1) {
            if (typeof expr[i] === "number") {
                auxExpr.push("(" + expr[i] + ")");
            } else {
                auxExpr.push(expr[i]);
            }
        }
        expr = auxExpr;
        e = expr.join('');
        try {
            valueFixed = (new Function('return ' + e))();
        } catch (e) {
            valueFixed = 0;
            message = new PMDynaform.implements.Logger();
            message.showMessage("formula");
            throw new Error("Error in the formula property");
        }
        valueFixed = _.isNaN(valueFixed) ? "" : valueFixed;
        return valueFixed;
    };

    PMDynaform.extendNamespace("PMDynaform.core.Formula", Formula);
}());


(function () {
    var WebServiceManager = function (options) {
        /*
         options.keys
         options.endPoints
         options.urlBase
         options.token
         */
        this.options = options || {};
        this.options.endPoints = {
            startCase: "light/process/{processUID}/task/{taskUID}/start-case",
            trigger: "light/process/{processUID}/task/{taskUID}/case/{caseUID}/step/{stepUID}/execute-trigger/{triggerOption}",
            getData: "light/{caseUID}/variables?pro_uid={processUID}&act_uid={taskUID}&app_index={delIndex}&dyn_uid={dyn_uid}",
            conditionalSteps: "light/process/{processUID}/case/{caseUID}/{delIndex}/step/{stepPosition}",
            form: "light/project/{processUID}/dynaforms",
            saveData: "light/{caseUID}/variable?dyn_uid={formUID}&del_index={delIndex}",
            query: "project/{processUID}/process-variable/{var_name}/execute-query",
            querySuggest: "project/{processUID}/process-variable/{var_name}/execute-query-suggest",
            imageInfo: "light/case/{caseUID}/download64",
            nextStep: "light/get-next-step/{caseUID}",
            upload: "light/case/{caseUID}/upload",
            uploadMultipart: "light/case/{caseUID}/upload/{docUID}",
            fileStreaming: "en/neoclassic/cases/casesStreamingFile?actionAjax=streaming&a={caseUID}&d={fileId}",
            fileVersionsList: "cases/{caseUID}/input-document/{docUID}/versions",
            downloadFile: "cases/{caseUID}/input-document/{docUID}/file?v={version}"
        };
        this.options.links = {
            showDocument: "{server}/sys{workspace}/en/{skin}/cases/cases_ShowDocument?a={docUID}&v={version}"
        };
        this.options.urlBase = "{server}/api/1.0/{workspace}/{endPointPath}";
        this.options.urlBaseStreaming = "{server}/sys{workspace}/{endPointPath}";

    };

    WebServiceManager.prototype.getFullEndPoint = function (keys, urlBase, endPoint) {
        var k;
        urlBase = urlBase.replace(/{endPointPath}/, endPoint);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlBase = urlBase.replace(new RegExp("{" + k + "}", "g"), keys[k]);
            }
        }
        return urlBase;
    };

    WebServiceManager.prototype.setKey = function (name, value) {
        if (this.options.keys)
            this.options.keys[name] = value;
        return this;
    };

    WebServiceManager.prototype.getKey = function (name) {
        var resp = false;
        if (this.options.keys)
            resp = this.options.keys[name];
        return resp;
    };

    WebServiceManager.prototype.deleteKey = function (name, value) {
        if (this.options.keys)
            delete this.options.keys[name];
        return this;
    };

    WebServiceManager.prototype.getToken = function () {
        return this.options.token;
    };

    WebServiceManager.prototype.startCase = function (callback) {
        var that = this,
            resp,
            url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.startCase),
            method = "POST";

        $.ajax({
            url: url,
            type: method,
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                resp = {
                    "caseUID": data.caseId,
                    "caseTitle": data.caseNumber,
                    "caseNumber": data.caseNumber
                };
                callback(null, resp);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "state": "internetFail"
                };
                callback(resp, null);
            }
        });
        return resp;
    };

    WebServiceManager.prototype.getData = function (callback, options) {
        var that = this,
            method, url, resp;
        if (typeof options === 'object') {
            this.setKey('dyn_uid', options['dyn_uid']);
        }
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.getData);
        method = "GET";
        this.deleteKey('dyn_uid');

        $.ajax({
            url: url,
            type: method,
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                callback(null, data);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "status": "error"
                };
                callback(resp, null);
            }
        });
        return resp;
    };
    /**
     * This function that execute a endpoint VARIABLES of ProcessMaker
     * @param formID
     * @param data
     * @returns {*}
     */
    WebServiceManager.prototype.saveData = function (config, callback) {
        var that = this,
            url,
            method;
        this.setKey('formUID', config["formUID"]);
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.saveData);
        method = "PUT";
        this.deleteKey('formUID');

        config.data = (config.data && _.isObject(config.data)) ? config.data : {};
        $.ajax({
            url: url,
            type: method,
            async: false,
            data: JSON.stringify(config.data),
            contentType: "application/json",
            timeout: (1000),
            dataType: 'text',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language !== null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                callback(null, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error! Type: " + textStatus);
                callback(textStatus, null);
            }
        });
    };

    WebServiceManager.prototype.execAjax = function (ajaxParams) {
        var resp;
        var that = this;

        function beforeSendCallback(xhr) {
            if (ajaxParams.isJSON) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            }
        }

        var params = {
            url: ajaxParams.url,
            type: ajaxParams.method,
            async: false,
            data: ajaxParams.data || {},
            beforeSend: function (xhr) {
                beforeSendCallback(xhr);
            },
            success: function (data, textStatus) {
                resp = {
                    "state": "success"
                };
            },
            error: function (xhr, textStatus, errorThrown) {
                if (xhr.status == 200) {
                    resp = {
                        "state": "success"
                    };
                } else {
                    resp = {
                        "state": "internetFail"
                    };
                }
            }
        };

        $.ajax(params);
    };
    WebServiceManager.prototype.trigger = function (config, callback) {
        var that = this,
            method = "POST",
            url,
            resp = {};

        this.setKey('stepUID', config["stepUID"]);
        this.setKey('triggerOption', config["triggerOption"]);

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.trigger);

        this.deleteKey('stepUID');
        this.deleteKey('triggerOption');

        $.ajax({
            url: url,
            type: method,
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                resp = data ? data : true;
                callback(null, resp);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "status": "error"
                };
                callback(resp, null);
            }
        });
        return resp;
    };

    WebServiceManager.prototype.executeQuery = function (data, varName) {
        var that = this,
            method = "POST", url, resp = [];

        this.setKey('var_name', varName);

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.query);

        this.deleteKey('var_name');

        data = data || {};
        data.app_uid = (this.options && this.options.keys && this.options.keys.caseUID) || null;
        data.del_index = (this.options && this.options.keys && this.options.keys.delIndex) || null;

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                resp = data;
            }
        });
        return resp;
    };

    WebServiceManager.prototype.conditionalStep = function (config, callback) {
        var that = this,
            method = "GET", url, resp;

        this.setKey('stepUID', config["stepUID"]);
        this.setKey('stepPosition', config["stepPosition"]);

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.conditionalSteps);

        this.deleteKey('stepUID');
        this.deleteKey('stepPosition');

        $.ajax({
            url: url,
            type: method,
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            },
            success: function (data, textStatus) {
                callback(null, data);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = JSON.parse(xhr.responseText);
                callback(resp, null);
            }
        });
        return resp;
    };

    WebServiceManager.prototype.getForm = function (config, callback) {
        var that = this,
            method,
            url,
            sendData = [],
            resp = {};

        sendData.push(config["formUID"]);
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.form);

        method = "POST";
        $.ajax({
            url: url,
            type: method,
            async: false,
            data: JSON.stringify({
                formId: sendData
            }),
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                var respData = null;
                if (data.length != 0) {
                    respData = data[0].formContent;
                }
                callback(null, respData);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "state": "internetFail"
                };
                callback(resp, null);
            }
        });
        return resp;
    };

    WebServiceManager.prototype.getFormDefinition = function () {
        var that = this,
            method,
            url,
            resp = {};
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.dynaformDefinition);
        method = "GET",
            $.ajax({
                url: url,
                type: method,
                async: false,
                contentType: "application/json",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                    if (that.options.language != null) {
                        xhr.setRequestHeader("Accept-Language", that.options.language);
                    }
                },
                success: function (data, textStatus) {
                    resp = {
                        "data": data.data.formContent,
                        "state": "success"
                    };
                },
                error: function (xhr, textStatus, errorThrown) {
                    resp = {
                        "state": "internetFail"
                    };
                }
            });
        return resp;
    };

    WebServiceManager.prototype.imageInfo = function (id, width) {
        var that = this,
            method,
            url,
            resp = {};
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.imageInfo);
        method = "POST";
        $.ajax({
            url: url,
            type: method,
            async: false,
            data: JSON.stringify([{
                fileId: id,
                width: width,
                version: 1
            }]),
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language !== null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lan);
                }
            },
            success: function (data, textStatus) {
                resp = {
                    id: data[0].fileId,
                    base64: data[0].fileContent
                }
            },
            error: function (error) {
                resp = false;
            }
        });
        return resp;
    };

    WebServiceManager.prototype.imagesInfo = function (data) {
        var that = this,
            method,
            url,
            resp = [];
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.imageInfo);
        method = "POST";
        $.ajax({
            url: url,
            type: method,
            async: false,
            data: JSON.stringify(data),
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language !== null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            },
            success: function (data, textStatus) {
                resp = data;
            }
        });
        return resp;
    };

    WebServiceManager.prototype.restClient = function () {
        defaults = {
            url: "/rest/v10",
            method: "GET",
            contentType: "application/json",
            data: '',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + keys.access_token);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            },
            success: function () {
            },
            error: function () {
            }
        };
        _.extend(defaults, parems);

        defaults.type = _methodsMap[defaults.method];
        defaults.data = JSON.stringify(defaults.data);
        $.ajax(defaults);
    };

    WebServiceManager.prototype.getFullURLStreaming = function (id) {
        var k,
            keys = this.options.keys,
            urlFormat = this.options.urlBaseStreaming;
        this.setKey('fileId', id);
        urlFormat = urlFormat.replace(/{endPointPath}/, this.options.endPoints.fileStreaming);
        for (k in keys) {
            if (keys.hasOwnProperty(k)) {
                urlFormat = urlFormat.replace(new RegExp("{" + k + "}", "g"), keys[k]);
            }
        }
        this.deleteKey("fileId");
        return urlFormat;
    };
    /**
     * consumes suggest rest service
     * @param data
     * @param varName
     * @returns {Array}
     */
    WebServiceManager.prototype.executeQuerySuggest = function (data, varName, callback) {
        var that = this,
            method = "POST", url,
            appUID = this.options && this.options.keys ? this.getKey("caseUID") : null,
            delIndex = this.options && this.options.keys ? this.getKey("delIndex") : null;

        this.setKey('var_name', varName);

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.querySuggest);

        this.deleteKey('var_name');

        data = data ? data : {}
        data.app_uid = appUID;
        data.del_index = delIndex;

        return $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            async: true,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus, xhr) {
                callback(data, xhr);
            }
        });
    };

    WebServiceManager.prototype.nextStep = function (config, callback) {
        var that = this,
            data,
            method = "POST", url, resp = [];

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.nextStep);

        data = {
            "pro_uid": this.getKey("processUID"),
            "act_uid": this.getKey("taskUID"),
            "step_uid": config["stepUID"],
            "step_pos": config["stepPosition"],
            "app_index": this.getKey("delIndex"),
            "dyn_uid": null
        };

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            async: false,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.keys.lang);
                }
            },
            success: function (data, textStatus) {
                resp = data;
                callback(null, data);
            },
            error: function (xhr, textStatus, errorThrown) {
                callback(textStatus, null);
            }
        });
        return resp;
    };
    /**
     * Returns all the versions of a doc.
     * @param {String} appDocUid The doc uid from the versions must be retrieved.
     * @param {Function} callback A callback to invoke when the petition completes or fails.
     * @returns {WebServiceManager}
     */
    WebServiceManager.prototype.getFileVersions = function (appDocUid, callback) {
        var url = this.getFullEndPoint({
                caseUID: this.options.keys.caseUID,
                docUID: appDocUid, // or use the options one?
                server: this.options.keys.server,
                workspace: this.options.keys.workspace
            }, this.options.urlBase, this.options.endPoints.fileVersionsList),
            that = this;

        $.ajax({
            url: url,
            type: 'GET',
            async: true,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            },
            success: function (data, textStatus) {
                resp = {
                    status: "success",
                    data: data
                };
                callback(null, resp);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "status": "error"
                };
                callback(resp, null);
            }
        });
        return this;
    };

    WebServiceManager.prototype.upload = function (data, callback) {
        var that = this,
            method = "POST", url, resp = [];

        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.upload);
        return $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(data),
            async: true,
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
                if (that.options.language != null) {
                    xhr.setRequestHeader("Accept-Language", that.options.language);
                }
            },
            success: function (data, textStatus) {
                resp = {
                    status: "success",
                    data: data
                };
                callback(null, resp);
            },
            error: function (xhr, textStatus, errorThrown) {
                resp = {
                    "status": "error"
                };
                callback(resp, null);
            }
        });
    };

    WebServiceManager.prototype.uploadMultipart = function (docUID, data, callback, callbackupdate) {
        var that = this,
            method = "POST", url, resp = [];
        this.setKey('docUID', docUID);
        url = that.getFullEndPoint(that.options.keys, that.options.urlBase, that.options.endPoints.uploadMultipart);
        this.deleteKey('docUID');
        resp = $.ajax({
            url: url,
            type: 'POST',
            data: data,
            async: true,
            processData: false,
            contentType: false,
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', callbackupdate, false);
                }
                return myXhr;
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + that.options.token.accessToken);
            },
            success: function (data) {
                callback(null, data);
            },
            error: function (xhr, textStatus, errorThrown) {
                callback(textStatus);
            }
        });
        return resp;
    };
    /**
     * Make teh URL to download the file
     * @param uid
     * @returns {string}
     */
    WebServiceManager.prototype.showDocument = function (data) {
        var keys = {
            docUID: data.uid || "",
            type: data.type || "",
            version: data.version || 1
        };
        return this.getDocumentLink(keys);
    };

    WebServiceManager.prototype.getDocumentLink = function (options) {
        var k,
            keys,
            urlbase = this.options.links[options["type"]];
        if (urlbase) {
            keys = $.extend(true, this.options.keys, options);
            for (k in keys) {
                if (keys.hasOwnProperty(k)) {
                    urlbase = urlbase.replace(new RegExp("{" + k + "}", "g"), keys[k]);
                }
            }
        }
        return urlbase;
    };
    /**
     * Gets file type blob
     * @param data {object}
     * @param callback
     */
    WebServiceManager.prototype.downloadFile = function (data, callback) {
        var that = this,
            method = "GET",
            url;

        this.setKey('docUID', data.docUID);
        this.setKey('version', data.version);
        url = this.getFullEndPoint(this.options.keys, this.options.urlBase, this.options.endPoints.downloadFile);
        this.deleteKey('docUID');
        this.deleteKey('version');

        $.ajax({
            url: url,
            type: method,
            headers: {Authorization: "Bearer " + that.options.token.accessToken},
            dataType: 'binary',
            async: true,
            success: function (data) {
                callback(data);
            },
            error: function () {
                callback(null);
            }
        });
    };

    xCase.extendNamespace("xCase.service.WebServiceManager", WebServiceManager);
}());

(function () {
    var Logger = function (option) {
        this.template = _.template($("#tpl-messageWarning").html()),
            this.messages = {
                "formula": "There are references to missing objects in the form. The form configuration is not completed and it is not going to be displayed (rendered). Please contact the administrator.",
                "default": "Please contact the administrator."
            },
            this.$el;
        Logger.prototype.init.call(this, option);
    };

    Logger.prototype.init = function (options) {

    };

    Logger.prototype.showMessage = function (optionMessage) {
        var html,
            json = {
                message: this.messages[optionMessage] || this.messages["default"]
            };
        html = this.template(json);
        $(".pmDynaformLoading").css("background", "no-repeat #f9f9f9");
        $(".pmDynaformLoading").append(html);
    };

    PMDynaform.extendNamespace("PMDynaform.implements.Logger", Logger);
}());
(function () {
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
                grid: TransformJSON.prototype.grid,
                multipleFile: TransformJSON.prototype.file
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
    TransformJSON.prototype.text = function (field) {
        return {
            type: "label",
            colSpanControl: field.colSpanControl,
            colSpanLabel: field.colSpanLabel,
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: [
                field.defaultValue || field.value
            ],
            data: field.data
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
            data: field.data
        };
    };
    TransformJSON.prototype.checkgroup = function (field) {
        var validOpt = [],
            i;

        for (i = 0; i < field.options.length; i += 1) {
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
            data: field.data
        };
    };
    TransformJSON.prototype.checkbox = function (field) {
        var validOpt = [],
            i;
        for (i = 0; i < field.options.length; i += 1) {
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
            data: field.data
        };
    };
    TransformJSON.prototype.radio = function (field) {
        var validOpt = [],
            i;
        for (i = 0; i < field.options.length; i += 1) {
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
            data: field.data
        };
    };
    TransformJSON.prototype.dropdown = function (field) {
        var validOpt = [],
            i;

        for (i = 0; i < field.options.length; i += 1) {
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
            data: field.data
        };
    };
    TransformJSON.prototype.button = function (field) {
        return field;
    };
    TransformJSON.prototype.submit = function (field) {
        return field;
    };
    TransformJSON.prototype.datetime = function (field) {
        return {
            type: "label",
            colSpan: field.colSpan,
            label: field.label,
            fullOptions: [
                field.defaultValue || field.value
            ],
            data: field.data
        };
    };
    TransformJSON.prototype.suggest = function (field) {
        var validOpt = [],
            i;
        for (i = 0; i < field.options.length; i += 1) {
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
            data: field.data
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
        return field;
    };
    TransformJSON.prototype.grid = function (field) {
        return field;
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
        var jsonBuilt = field,
            total,
            sigleControl = ["text", "suggest", "textarea", "datetime"],
            data,
            i;

        if (this.json[field.type] && this.discardViewField(field.type)) {
            switch (field.mode) {
                case "disabled":
                    jsonBuilt = field;
                    jsonBuilt.disabled = true;
                    break;
                case "parent":
                    field.mode = this.parentMode;
                    jsonBuilt = this.reviewField(field);
                    break;
                case "view":
                    jsonBuilt = this.json[field.type](field);
                    break;
                default :
                    jsonBuilt = field;
            }
        }
        jsonBuilt.dataType = field.dataType || "";
        jsonBuilt.originalType = field.originalType || field.type;
        jsonBuilt.var_name = field.var_name || "";
        jsonBuilt.var_uid = field.var_uid || "";
        jsonBuilt.options || field.var_accepted_values;
        if (field.data) {
            jsonBuilt.fullOptions = [];
            if (sigleControl.indexOf(jsonBuilt.originalType) !== -1) {
                if (jsonBuilt.originalType === "suggest") {
                    jsonBuilt.fullOptions = [field.data["label"] || field.defaultValue];
                } else {
                    jsonBuilt.fullOptions = [field.data["value"] || field.defaultValue];
                }
            } else {
                if (jsonBuilt.originalType === "checkgroup") {
                    data = [];
                    if ($.isArray(field["optionsSql"])) {
                        total = field["options"].concat(field["optionsSql"]);
                    } else {
                        total = field["options"];
                    }
                    for (i = 0; i < total.length; i += 1) {
                        if (field.data["value"].indexOf(total[i]["value"]) !== -1) {
                            data.push(total[i]["label"]);
                        }
                    }
                    jsonBuilt.fullOptions = data || [field.defaultValue];
                } else {
                    jsonBuilt.fullOptions = [field.data["label"] || field.defaultValue];
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
(function () {
    var FileReader = window.FileReader,
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
                    'text/*': 'Text'
                },
                on: {
                    loadstart: function () {
                    },
                    progress: function () {
                    },
                    load: function () {
                    },
                    abort: function () {
                    },
                    error: function () {
                    },
                    loadend: function () {
                    },
                    skip: function () {
                    },
                    groupstart: function () {
                    },
                    groupend: function () {
                    },
                    beforestart: function () {
                    }
                }
            }
        };

    // Not all browsers support the FileReader interface.  Return with the enabled bit = false.
    if (!FileReader) {
        return;
    }

    // WorkerHelper is a little wrapper for generating web workers from strings
    var WorkerHelper = (function () {

        var URL = window.URL || window.webkitURL;
        var BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;

        // May need to get just the URL in case it is needed for things beyond just creating a worker.
        function getURL(script) {
            if (window.Worker && BlobBuilder && URL) {
                var bb = new BlobBuilder();
                bb.append(script);
                return URL.createObjectURL(bb.getBlob());
            }

            return null;
        }

        // If there is no need to revoke a URL later, or do anything fancy then just return the worker.
        function getWorker(script, onmessage) {
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
            if (e.dataTransfer.files && e.dataTransfer.files.length) {
                e.stopPropagation();
                e.preventDefault();
            }
        }

        function onlyWithFiles(fn) {
            return function () {
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

        var filesLeft = files.length,
            group = {
                groupID: getGroupID(),
                files: files,
                started: new Date()
            },
            sync,
            syncWorker;

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

        sync = FileStream.sync && FileReaderSyncSupport;
        syncWorker;

        // Only initialize the synchronous worker if the option is enabled - to prevent the overhead
        if (sync) {
            syncWorker = WorkerHelper.getWorker(workerScript, function (e) {
                var file = e.data.file;
                var result = e.data.result;

                // Workers seem to lose the custom property on the file object.
                if (!file.extra) {
                    file.extra = e.data.extra;
                }

                file.extra.ended = new Date();

                // Call error or load event depending on success of the read from the worker.
                opts.on[result === "error" ? "error" : "load"]({target: {result: result}}, file);
                groupFileDone();

            });
        }

        Array.prototype.forEach.call(files, function (file) {

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

                fileReaderEvents.forEach(function (eventName) {
                    reader['on' + eventName] = function (e) {
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
        var worker = WorkerHelper.getWorker(syncDetectionScript, function (e) {
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
        var s = ['bytes', 'kb', 'MB', 'GB', 'TB', 'PB'],
            e = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, Math.floor(e))).toFixed(2) + " " + s[e];
    }

    // getGroupID: generate a unique int ID for groups.
    var getGroupID = (function (id) {
        return function () {
            return id++;
        };
    })(0);

    // getUniqueID: generate a unique int ID for files
    var getUniqueID = (function (id) {
        return function () {
            return id++;
        };
    })(0);

    // The interface is supported, bind the FileStream callbacks
    FileStream.enabled = true;


    PMDynaform.extendNamespace("PMDynaform.core.FileStream", FileStream);

}());
(function () {
    /**
     * FullScreen class
     */
    var FullScreen = function (options) {
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
    FullScreen.prototype.init = function (options) {
        var defaults = {
            element: document.documentElement,
            onReadyScreen: function () {
            },
            onCancelScreen: function () {
            }
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

        this.supported = request ? true : null;

        return this;
    };
    FullScreen.prototype.cancel = function () {
        var requestMethod, fnCancelScreen, wscript, el;
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
        this.isInFullScreen = (el.fullScreenElement && el.fullScreenElement !== null) || (el.mozFullScreen || el.webkitIsFullScreen);
        if (this.isInFullScreen) {
            this.cancel();
        } else {
            this.applyZoom();
        }
        return false;
    };

    PMDynaform.extendNamespace("PMDynaform.core.FullScreen", FullScreen);
}());
(function () {
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
        this.type = type;
        return this;
    };
    Script.prototype.setScript = function (script) {
        this.script = script;
        return this;
    };
    Script.prototype.createHTML = function () {
        var html = document.createElement("script");

        html.type = this.type;
        html.text = this.script;
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
            get: function (index) {
                return elements[index];
            },
            /**
             * Inserts an element at the end of the list
             * @param {Object} item
             * @chainable
             */
            insert: function (item) {
                elements[size] = item;
                size += 1;
                return this;
            },
            /**
             * Inserts an element in a specific position
             * @param {Object} item
             * @chainable
             */
            insertAt: function (item, index) {
                elements.splice(index, 0, item);
                size = elements.length;
                return this;
            },
            /**
             * Removes an item from the list
             * @param {Object} item
             * @return {boolean}
             */
            remove: function (item) {
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
            getSize: function () {
                return size;
            },
            /**
             * Returns true if the list is empty
             * @returns {boolean}
             */
            isEmpty: function () {
                return size === 0;
            },
            /**
             * Returns the first occurrence of an element, if the element is not
             * contained in the list then returns -1
             * @param {Object} item
             * @return {number}
             */
            indexOf: function (item) {
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
            find: function (target) {
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
                function search(low, high) {
                    // If the low index is greater than the high index,
                    //  return null for not-found.
                    if (low > high) {
                        return undefined;
                    }

                    // If the value at the low index is our target, return
                    //  the low index.
                    if (sortedValues[low] === target) {
                        return low;
                    }

                    // If the value at the high index is our target, return
                    //  the high index.
                    if (sortedValues[high] === target) {
                        return high;
                    }

                    // Find the middle index and median element.
                    var middle = Math.floor(( low + high ) / 2);
                    var middleElement = sortedValues[middle];

                    // Recursive calls, depending on whether or not the
                    //  middleElement is less-than or greater-than the
                    //  target.
                    // Note: We can use high-1 and low+1 because we've
                    //  already checked for equality at the high and low
                    //  indexes above.
                    if (middleElement < target) {
                        return search(middle, high - 1);
                    } else if (middleElement > target) {
                        return search(low + 1, middle);
                    }

                    // If middleElement === target, we can return that value.
                    return middle;
                }

                // Start our search between the first and last elements of
                //  the array.
                return search(0, sortedValues.length - 1);
            },
            /**
             * Returns true if the list contains the item and false otherwise
             * @param {Object} item
             * @return {boolean}
             */
            contains: function (item) {
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
            sort: function (compFunction) {
                var compFunction = compFunction || function (a, b) {
                        if (a < b) {
                            return 1;
                        } else if (a > b) {
                            return -1;
                        } else {
                            return 0;
                        }
                    }, swap = function (items, firstIndex, secondIndex) {
                    var temp = items[firstIndex];
                    items[firstIndex] = items[secondIndex];
                    items[secondIndex] = temp;
                }, partition = function (items, left, right) {
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
            asArray: function () {
                return elements.slice(0);
            },
            /**
             * Swaps the position of two elements
             * @chainable
             */
            swap: function (index1, index2) {
                var aux;
                if (index1 < size && index1 >= 0 && index2 < size && index2 >= 0) {
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
            getFirst: function () {
                return elements[0];
            },
            /**
             * Returns the last element of the list
             * @return {Object}
             */
            getLast: function () {
                return elements[size - 1];
            },

            /**
             * Returns the last element of the list and deletes it from the list
             * @return {Object}
             */
            popLast: function () {
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
            getDimensionLimit: function () {
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
            clear: function () {
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
            set: function (items) {
                if (!(items === null || jQuery.isArray(items))) {
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

(function() {
    /**
     * @class PMDynaform.util.DependentsFieldManager
     * Class that handles dependent field events
     * @param options {Object} Instance configuration
     */
    var DependentsFieldManager = function(options) {
        /**
         * @param {object}: eventsManager, Dependent field event manager
         * extends Backbone.Events
         */
        this.eventsManager = {};
        /**
         * @param {object}: form, Related form
         */
        this.form = null;
        DependentsFieldManager.prototype.init.call(this, options);
    };
    /**
     * initialize the DependentsFieldManager
     * @chainable
     */
    DependentsFieldManager.prototype.init = function(options) {
        var defaults = {
            form: null
        };
        _.extend(defaults, options);
        this.setForm(defaults.form);
        _.extend(this.eventsManager, Backbone.Events);
        return this;
    };
    /**
     * setForm: Sets the corresponding Related form
     * @param form {object} instace PMDynaform.model.FormPanel
     * @chainable
     */
    DependentsFieldManager.prototype.setForm = function(form) {
        this.form = form;
        return this;
    };
    /**
     * addEvent, Records a new dependent field event
     * @param key {string} Is the identifier that is registered in the event manager
     * @param callback {function} Is the function that will be called after executing the event
     * @param target {object} Is the origin of the method call
     * @chainable
     */
    DependentsFieldManager.prototype.addEvent = function(key, callback, target) {
        this.eventsManager.on("dependency:" + key, callback, target);
        return this;
    };
    /**
     * removeEvent, Disable Events Logged on the Dependent Field Handler
     * @chainable
     */
    DependentsFieldManager.prototype.removeEvent = function(observer) {
        this.eventsManager.off("dependency:" + observer);
        return this;
    };
    /**
     * notify, executes the notification corresponding to the registered event
     * @param info {object} Information of the item that ejects the event
     * @chainable
     */
    DependentsFieldManager.prototype.notify = function(info) {
        this.eventsManager.trigger("dependency:" + info.registrationName, info);
        return this;
    };
    /**
     * executeFieldQuery, Run the query through the service
     * @param data {object}, necessary data for execute the query
     * @param variableName {string}, Name of the source field variable
     * @param target {object}, Instance PMDynform.model.Field, source field that executes the call
     * @returns {array}, set of options 
     */
    DependentsFieldManager.prototype.executeFieldQuery = function(data, variableName, target) {
        var project = this.getProject(),
            response = [];
        if (project) {
            response = project.webServiceManager.executeQuery(data, variableName, target);
        }
        return response;
    };
    /**
     * getForm, obtain the related form
     * @returns {PMDynaform.model.FormPanel}
     */
    DependentsFieldManager.prototype.getForm = function() {
        return this.form;
    };
    /**
     * getProject, obtain the project related
     * @returns {PMDynaform.core.Project}
     */
    DependentsFieldManager.prototype.getProject = function() {
        var form = this.getForm();
        if (form && form.get("project")) {
            return form.get("project");
        }
        return null;
    };

    PMDynaform.extendNamespace("PMDynaform.util.DependentsFieldManager", DependentsFieldManager);
}());
(function () {

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
            success: function () {
            },
            failure: function () {
            },
            complete: function () {
            }
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
    RestProxy.prototype.getData = function () {
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
        switch (type) {
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
                success: function (xhr, response) {
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
                success: function (xhr, response) {
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
                success: function (xhr, response) {
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
                success: function (xhr, response) {
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

    RestProxy.prototype.success = function (xhr, response) {
    };

    RestProxy.prototype.failure = function (xhr, response) {
    };

    RestProxy.prototype.complete = function (xhr, response) {
    };

    PMDynaform.extendNamespace('PMDynaform.proxy.RestProxy', RestProxy);

}());
(function () {

    var Validator = Backbone.View.extend({
        template: _.template($("#tpl-validator").html()),
        events: {
            "mouseover": "onMouseOver"
        },
        initialize: function () {
            this.render();
        },
        onMouseOver: function () {

        },
        render: function () {
            this.$el.addClass("pmdynaform-message-error");
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Validator", Validator);

}());
(function () {
    var PanelView = Backbone.View.extend({
        content: null,
        colsIndex: null,
        template: null,
        collection: null,
        items: null,
        views: [],
        renderTo: document.body,
        project: null,
        events: {
            "click a#print-button": "printForm"
        },
        initialize: function (options) {
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
            if (options.project) {
                this.project = options.project;
            }
            this.views = [];

            this.makePanels();
            this.render();
            for (i = 0; i < this.views.length; i += 1) {
                this.views[i].runningFormulator();
            }
        },
        printForm: function () {
            var result,
                flashModel,
                forms = this.views,
                i,
                form,
                formMode = "edit",
                project = this.project;

            formMode = forms && forms.length ? forms[0].model.get("mode") : formMode;

            if ((project && project.isPreview) || formMode === "disabled") {
                window.print();
            } else {
                if (typeof  this.views[0].model.get("onBeforePrintHandler") === "function") {
                    this.views[0].model.get("onBeforePrintHandler")();
                }
                for (i = 0; i < forms.length; i += 1) {
                    form = forms[i];
                    result = form.saveForm();
                }
                if (result) {
                    window.print();
                    if (typeof this.views[0].model.get("onAfterPrintHandler") === "function") {
                        this.views[0].model.get("onAfterPrintHandler")();
                    }
                } else {
                    flashModel = {
                        message: "error",
                        emphasisMessage: "Error:",
                        startAnimation: 1000,
                        type: "danger",
                        appendTo: this.el,
                        duration: 3000
                    };
                    this.project.flashMessage(flashModel);
                }
            }
            return this;
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
            for (i = 0; i < panels.length; i += 1) {
                fields = this.views[i].items.asArray();
                for (k = 0; k < fields.length; k += 1) {

                    if ((typeof fields[k].model.getData === "function") && (fields[k].model.attributes.type === "form")) {
                        subform = fields[k].getData();
                        $.extend(true, formData.variables, subform.variables);
                    } else if (typeof fields[k].model.getData === "function") {
                        field = fields[k].model.getData();
                        formData.variables[field.name] = field.value;
                    }
                }
            }
            return formData;
        },
        /**
         * Function to get Data to end point
         * @returns {{}}
         */
        getData2: function () {
            var i,
                k,
                fieldDt,
                fields,
                panels,
                grid,
                data = {},
                dataRecursive;

            panels = this.model.get("items");
            for (i = 0; i < panels.length; i += 1) {
                fields = this.views[i].items.asArray();
                for (k = 0; k < fields.length; k += 1) {
                    if (this.isValidFieldToSendData(fields[k])) {
                        if (typeof fields[k].getItems === "function") {
                            dataRecursive = this.getDataRecursive(fields[k]);
                            $.extend(true, data, dataRecursive);
                        } else if (typeof fields[k].model.getData === "function") {
                            if (fields[k].model.get("type") === "grid") {
                                grid = fields[k].model;
                                data[grid.get("name")] = fields[k].getData2();
                            } else {
                                fieldDt = fields[k].model.getAppData();
                                $.extend(true, data, fieldDt);
                            }
                        }
                    }
                }
            }
            return data;
        },
        /**
         * This function verify that field is valid to send data
         * @param field
         * @returns {boolean}
         */
        isValidFieldToSendData: function (field) {
            var flag = false,
                invalidTypes = ["empty", "title", "subtitle", "button", "submit", "panel", "link", "image"];

            if (field.model.get("mode") === "view") {
                if (invalidTypes.indexOf(field.model.get("originalType")) === -1) {
                    flag = true;
                }
            } else {
                if (invalidTypes.indexOf(field.model.get("type")) === -1) {
                    flag = true;
                }
            }
            return flag;
        },
        setAppData: function (data) {
            this.getPanels()[0].setAppData(data);
        },
        getDataRecursive: function (view) {
            var items = view.getItems(),
                viewField,
                fieldDt,
                dataRecursive = {},
                grid,
                data = {},
                index;

            for (index = 0; index < items.length; index += 1) {
                if (this.isValidFieldToSendData(items[index])) {
                    viewField = items[index];
                    if (this.isValidFieldToSendData(viewField)) {
                        if (typeof viewField.getItems === "function") {
                            dataRecursive = this.getDataRecursive(viewField);
                            $.extend(true, data, dataRecursive);
                        } else if (typeof viewField.model.getData === "function") {
                            if (viewField.model.get("type") === "grid") {
                                grid = viewField.model;
                                data[grid.get("name")] = viewField.getData2();
                            } else {
                                fieldDt = viewField.model.getAppData();
                                $.extend(true, data, fieldDt);
                            }
                        }
                    }
                }
            }
            return data;
        },
        setData2: function (data) {
            var index = 0;
            this.getPanels()[index].setData2(data);
            return this;
        },
        makePanels: function () {
            var i,
                items,
                panelModel,
                view;

            this.views = [];
            items = this.model.get("items");

            for (i = 0; i < items.length; i += 1) {
                if ($.inArray(items[i].type, ["panel", "form"]) >= 0) {
                    items[i].parent = this;
                    panelModel = new PMDynaform.model.FormPanel(items[i]);
                    panelModel.set("project", this.project);
                    view = new PMDynaform.view.FormPanel({
                        model: panelModel,
                        project: this.project
                    });

                    if (this.project) {
                        panelModel.set("project", this.project);
                    }

                    this.views.push(view);
                }
            }

            return this;
        },
        getPanels: function () {
            return (this.views.length > 0) ? this.views : [];
        },
        render: function () {
            var i,
                printed = true;

            this.$el = $(this.el);
            for (i = 0; i < this.views.length; i += 1) {
                printed = this.views[i].model.get("printable");
                this.$el.append(this.views[i].render().el);
                if (i === 0 && printed && typeof PMDynaform.core.ProjectMobile === "undefined") {
                    this.addPrinForm(this.views[i].el);
                    if (typeof this.views[i].model.get("onBeforePrintHandler") === "function") {
                        this.model.set("onBeforePrintHandlder", this.views[i].model.get("onBeforePrintHandlder"))
                    }
                    if (typeof this.views[i].model.get("onAfterPrintHandler") === "function") {
                        this.model.set("onAfterPrintHandlder", this.views[i].model.get("onAfterPrintHandlder"))
                    }
                }
            }
            this.$el.addClass("pmdynaform-container");
            if (PMDynaform.core.ProjectMobile) {
                this.$el.css({
                    height: "auto"
                });
            }
            $(this.renderTo).append(this.el);

            return this;
        },
        afterRender: function () {
            var i;

            for (i = 0; i < this.views.length; i += 1) {
                this.views[i].afterRender();
            }

            return this;
        },
        addPrinForm: function (container) {
            var printContainer,
                buttonPrint;
            printContainer = document.createElement("div");
            buttonPrint = document.createElement("a");
            buttonPrint.className = "print-button";
            buttonPrint.id = "print-button";
            printContainer.appendChild(buttonPrint);
            printContainer.className = "printContainer";
            if (container instanceof jQuery) {
                container.prepend(printContainer);
            } else {
                $(container).prepend(printContainer);
            }
            return this;
        },
        /**
         * Execute this function on after the submit button
         * @returns {PanelView}
         */
        afterSubmit: function () {
            var index;
            for (index = 0; index < this.views.length; index += 1) {
                if (_.isFunction(this.views[index].afterSubmit)) {
                    this.views[index].afterSubmit();
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Panel", PanelView);

}());

(function () {
    var FormPanel = Backbone.View.extend({
        tagName: "form",
        content: null,
        template: null,
        items: new PMDynaform.util.ArrayList(),
        views: [],
        templateRow: _.template($('#tpl-row').html()),
        colSpanLabel: 3,
        colSpanControl: 9,
        project: null,
        preTargetControl: null,
        sqlFields: [],
        changeFired: false,
        submit: [],
        events: {
            'submit': 'onSubmit'
        },
        onChange: function () {
        },
        onChangeCallback: function () {
        },
        requireVariableByField: [],
        /**
         * Executes form and subForm's onChangeCallBack
         * @param params
         */
        checkBinding: function (params) {
            var parent = this.project.getForm(),
                mobileSubForm = this.model.get("form"),
                idField = this.model.get("id") || (mobileSubForm && mobileSubForm.model.get("id"));
            params = this.getExtraData(params);
            //Executes onChangeCallBack of the SubForm
            this.onChangeCallback(params.idField, params.current, params.previous, params.row);
            if (parent && parent.model.isSubForm(idField)) {
                //Executes onChangeCallBack of the Form
                parent.onChangeCallback(params.idField, params.current, params.previous, params.row);
            }
        },
        /**
         * Sets OnChangeCallBack function.
         * @param handler
         * @returns {FormPanel}
         */
        setOnChange: function (handler) {
            if (typeof handler === "function") {
                this.onChangeCallback = handler;
            }
            return this;
        },
        /**
         * Modifies the current params.
         * @param params {object}
         * @returns {object}
         */
        getExtraData: function (params) {
            if (this.hasOwnProperty("extraData")) {
                params.idField = this.createGridId(this.extraData.gridId, this.extraData.nRow, params.idField) || params.idField;
                params.row = this.getFields() || null;
            }
            return params;
        },
        /**
         * Create newId for MobileGrid.
         * @param gridId
         * @param nRow
         * @param idColumn
         * @returns {string}
         */
        createGridId: function (gridId, nRow, idColumn) {
            return "[" + gridId + "][" + nRow + "][" + idColumn + "]";
        },
        onChangeHandler: function () {
            var that = this;
            return function (field, newValue, previousValue) {
                if (typeof that.onChange === 'function') {
                    that.onChange(field, newValue, previousValue);
                }
            };
        },
        initialize: function (options) {
            var fileConf,
                gridConf;

            if (PMDynaform.core.ProjectMobile) {
                gridConf = {
                    model: PMDynaform.model.GridMobile,
                    view: PMDynaform.view.GridMobile
                };
                fileConf = {
                    model: PMDynaform.model.FileUpload,
                    view: PMDynaform.view.FileUpload
                };
            } else {
                gridConf = {
                    model: PMDynaform.model.GridPanel,
                    view: PMDynaform.view.GridPanel
                };
                fileConf = {
                    model: PMDynaform.model.File,
                    view: PMDynaform.view.File
                };
            }
            var defaults = {
                factory: {
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
                        "file": fileConf,
                        "image": {
                            model: PMDynaform.model.Image,
                            view: PMDynaform.view.Image
                        },
                        "geomap": {
                            model: PMDynaform.model.GeoMap,
                            view: PMDynaform.view.GeoMap
                        },
                        "grid": gridConf,
                        "form": {
                            model: PMDynaform.model.SubForm,
                            view: PMDynaform.view.SubForm
                        },
                        "annotation": {
                            model: PMDynaform.model.Annotation,
                            view: PMDynaform.view.Annotation
                        },
                        "location": {
                            model: PMDynaform.model.GeoMobile,
                            view: PMDynaform.view.GeoMobile
                        },
                        "scannercode": {
                            model: PMDynaform.model.Qrcode_mobile,
                            view: PMDynaform.view.Qrcode_mobile
                        },
                        "signature": {
                            model: PMDynaform.model.Signature_mobile,
                            view: PMDynaform.view.Signature_mobile
                        },
                        "imagemobile": {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile
                        },
                        "audiomobile": {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile
                        },
                        "videomobile": {
                            model: PMDynaform.model.FileMobile,
                            view: PMDynaform.view.FileMobile
                        },
                        "panel": {
                            model: PMDynaform.model.PanelField,
                            view: PMDynaform.view.PanelField
                        },
                        "multiplefile": {
                            model: PMDynaform.file.MultipleFileModel,
                            view: PMDynaform.file.MultipleFileView
                        }
                    },
                    defaultProduct: "empty"
                }
            };
            this.items = new PMDynaform.util.ArrayList();
            this.onChangeCallback = options.onChangeCallback ? options.onChangeCallback : new Function();
            if (options.project) {
                this.project = options.project;
            }
            this.setFactory(defaults.factory);
            this.applyGlobalMode();
            this.makeItems();
        },
        setAction: function () {
            this.$el.attr("action", this.model.get("action"));
            return this;
        },
        setMethod: function () {
            this.$el.attr("method", this.model.get("method"));

            return this;
        },
        setFactory: function (factory) {
            this.factory = factory;
            return this;
        },
        getData: function () {
            var i,
                k,
                field,
                fields,
                panels,
                data = [],

                panels = this.viewsBuilt;

            for (i = 0; i < panels.length; i += 1) {
                fields = panels[i];
                for (k = 0; k < fields.length; k += 1) {
                    field = fields[k].model.get("data");
                    data.push(field);
                }
            }
            return data;
        },
        countElementsInJSON: function (obj) {
            var i = 0,
                item;
            for (item  in obj) {
                i += 1;
            }
            return i;
        },
        setData2: function (data) {
            var key, value, label, field, nameReplace, name;
            if (typeof data === "object") {
                for (key in data) {
                    name = key;
                    field = getFieldByName(name);
                    if (_.isArray(field) && field.length > 0) {
                        value = data[key];
                        if (data.hasOwnProperty(key + "_label")) {
                            label = data[key + "_label"];
                        } else {
                            label = data[key];
                        }
                        jQuery.each(field, function (index, element) {
                            element.setData({
                                value: value,
                                label: label
                            });
                        });
                    }
                }
            }
            return this;
        },
        /**
         * setAppData Sets the data to the form
         * @param appData {object} Set of valid data for the form
         * @chainable
         */
        setAppData: function (appData) {
            if (typeof appData === "object") {
                this.model.setAppData(appData);
            }
            return this;
        },
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
        makeItems: function () {
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
                items,
                configColSpan;
            this.sqlFields = [];
            fields = this.model.get("items");
            this.viewsBuilt = [];
            this.items.clear();

            for (i = 0; i < fields.length; i += 1) {
                rowView = [];
                for (j = 0; j < fields[i].length; j += 1) {
                    variableEnabled = this.validateVariableField(fields[i][j]);
                    if (fields[i][j] !== null && (variableEnabled === true || variableEnabled === "NOT")) {
                        fields[i][j] = this.applyGlobalModeField(fields[i][j]);
                        if (fields[i][j].type) {
                            if (!PMDynaform.core.ProjectMobile && fields[i][j].type === "location") {
                                fields[i][j].type = "geomap";
                            }
                            if (fields[i][j].type === "label") {
                                fields[i][j].type = "annotation";
                            }
                            if (fields[i][j].type === "checkbox" && fields[i][j].dataType !== "boolean" && fields[i][j].dataType !== "") {
                                fields[i][j].type = "checkgroup";
                            }
                            if (fields[i][j].type === "file" && fields[i][j].hasOwnProperty("inputDocuments")) {
                                if ($.isArray(fields[i][j]["inputDocuments"])) {
                                    $(fields[i][j]["inputDocuments"]).each(function () {
                                        that.model.attributes.inputDocuments[fields[i][j]["variable"]] = this;
                                    });
                                }
                            }
                            jsonFixed = new PMDynaform.core.TransformJSON({
                                parentMode: this.model.get("mode"),
                                field: fields[i][j]
                            });
                            product = factory.products[jsonFixed.getJSON().type.toLowerCase()] ?
                                factory.products[jsonFixed.getJSON().type.toLowerCase()] : factory.products[factory.defaultProduct];
                        } else {
                            jsonFixed = new PMDynaform.core.TransformJSON({
                                parentMode: this.model.get("mode"),
                                field: fields[i][j]
                            });
                            product = factory.products[factory.defaultProduct];
                        }

                        //The number 12 is related to 12 columns from Bootstrap framework
                        configColSpan = this.generateColSpan(fields[i][j].colSpan);
                        fieldModel = {
                            project: this.project,
                            parentMode: this.model.get("mode"),
                            namespace: this.model.get("namespace"),
                            variable: fields[i][j].variable ? fields[i][j].variable : null,
                            fieldsRelated: [],
                            name: fields[i][j].name,
                            id: fields[i][j].id || PMDynaform.core.Utils.generateID(),
                            options: fields[i][j].options,
                            optionsSql: fields[i][j].optionsSql,
                            required: fields[i][j].required || false,
                            hint: fields[i][j].hint || "",
                            format: fields[i][j].format || "",
                            formula: fields[i][j].formula || "",
                            sql: fields[i][j].sql || "",
                            defaultValue: fields[i][j].defaultValue || "",
                            defaultDate: fields[i][j].defaultDate || null,
                            _hidden: fields[i][j]._hidden || false,
                            colSpanLabel: configColSpan.label,
                            colSpanControl: configColSpan.control
                        };
                        if (fields[i][j].type === "form" || fields[i][j].type === "grid") {
                            fieldModel.variables = this.model.get("variables") || [];
                            fieldModel.data = this.model.get("data") || [];
                        }

                        $.extend(true, fieldModel, jsonFixed.getJSON());

                        if (fieldModel.type === "form" && fieldModel.mode === "parent") {
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
                        fieldModel.form = this.model;
                        fieldModel.parent = this.model;
                        productModel = new product.model(fieldModel);
                        productBuilt = new product.view({
                            model: productModel,
                            project: this.project,
                            parent: this,
                            form: this
                        });
                        if (productModel.get("type") === "form") {
                            this.model.get("subForms").insert(productBuilt);
                        }
                        productBuilt.parent = this;
                        productBuilt.project = this.project;
                        this.model.addField(productModel, productModel.get("variable") || productModel.get("name") || productModel.get("id"));
                        //add view in mobile project
                        if (this.project.addViewFields && productModel.get("type") !== "empty") {
                            this.project.addViewFields(productBuilt);
                        }
                        rowView.push(productBuilt);
                        this.items.insert(productBuilt);
                        productBuilt.model.attributes.view = productBuilt;
                    } else {
                        console.error("The field must have the variable property and must to be an object: ", fields[i][j]);
                    }
                }
                if (rowView.length) {
                    this.viewsBuilt.push(rowView);
                }
            }
            this.runningFormulator();
            this.model.loadSqlOptionsInFields();
            return this;
        },
        /**
         * Generate the colSpan to label and control
         * @param colSpan
         * @returns {{label: number, control: number}}
         */
        generateColSpan: function (colSpan) {
            var defaultColSpan = {
                label: 2,
                control: 10
            };
            switch (parseInt(colSpan)) {
                case 6:
                case 4:
                case 1:
                    defaultColSpan.label = 4;
                    defaultColSpan.control = 8;
                    break;
                case 5:
                case 3:
                case 2:
                    defaultColSpan.label = 5;
                    defaultColSpan.control = 7;
                    break;
            }
            return defaultColSpan;
        },
        runningFormulator: function () {
            var items,
                field,
                i,
                j,
                fieldsAsocied,
                rowCurrent,
                col;
            items = this.viewsBuilt;
            for (i = 0; i < items.length; i += 1) {
                for (j = 0; j < items[i].length; j += 1) {
                    field = items[i][j];
                    if (field.model.get("type") === "form") {
                        if (field.runningFormulator) {
                            field.runningFormulator();
                        }
                    }
                    if (field.model.get("type") === "grid") {
                        var rowsAll = field.gridtable;
                        for (var row = 0; row < rowsAll.length; row += 1) {
                            rowCurrent = rowsAll[row];
                            for (col = 0; col < rowCurrent.length; col += 1) {
                                if (rowCurrent[col].model.get("formula") && rowCurrent[col].model.get("formula").trim().length) {
                                    fieldsAsocied = rowCurrent.filter(function (element) {
                                        if (rowCurrent[col].fieldValid.indexOf(element.model.get("id")) > -1) {
                                            element.onFieldAssociatedHandler();
                                        }
                                    });
                                }
                            }
                        }
                    } else {
                        if (field.model.get("formula") && field.model.get("formula").trim().length) {
                            fieldsAsocied = items.filter(function (element) {
                                var k;
                                for (k = 0; k < element.length; k += 1) {
                                    if (field.fieldValid.indexOf(element[k].model.get("id")) > -1) {
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
                fields = this.items.asArray();

            for (i = 0; i < fields.length; i += 1) {
                fieldA = fields[i].model.get("variable");
                if (fieldA) {
                    for (j = 0; j < fields.length; j += 1) {
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
                    for (k = 0; k < fields.length; k += 1) {
                        fieldA = fields[k].model.get("variable");
                        if (fieldA) {
                            for (l = 0; l < fieldsSubForm.length; l += 1) {
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
                for (i = 0; i < variables.length; i += 1) {
                    if (variables[i] && variables[i].var_uid === var_uid) {
                        varSelected = variables[i];
                        break loop_variables;
                    }
                }
            return varSelected;
        },
        getFields: function () {
            return (this.items.getSize() > 0) ? this.items.asArray() : [];
        },
        /**
         * Returns all the fields in the form including the ones in any nested subform.
         * @returns {Array.<T>PMDynaform.view.Field}
         */
        getAllFields: function () {
            var formElements = this.getFields(),
                subformsFields = [],
                fields;

            fields = formElements.filter(function (i) {
                // The second expression is necessary since Grid for Mobile doesn't inherit from PMDynaform.view.Field.
                if (i instanceof PMDynaform.view.Field
                    || (PMDynaform.view.GridMobile && i instanceof PMDynaform.view.GridMobile)) {
                    return true;
                } else if (i instanceof PMDynaform.view.SubForm) {
                    subformsFields = subformsFields.concat(i.getAllFields());
                }
                return false;
            });

            return fields.concat(subformsFields);
        },
        beforeRender: function () {
            return this;
        },
        applySuccess: function () {
            var items = this.items.asArray(),
                i;

            for (i = 0; i < items.length; i += 1) {
                if (items[i].applyStyleSuccess) {
                    items[i].applyStyleSuccess();
                }
            }
        },
        onSubmit: function (event) {
            var booResponse;
            if (this.executeSubmitArray()) {
                if (!this.isValid(event)) {
                    booResponse = false;
                } else {
                    this.applySuccess();
                    booResponse = true;
                }
                //validate if has has submitRest enabled
                if (this.project.submitRest) {
                    if (event !== undefined) {
                        event.preventDefault();
                    }
                    if (booResponse) {
                        this.project.onSubmitForm();
                    }
                } else {
                    if (booResponse && event && event.type === 'submit') {
                        this.prepareFormToPost();
                    }
                }
            }
            else {
                if (event !== undefined) {
                    event.preventDefault();
                }
            }
            return booResponse;
        },
        /**
         * Submit to next step the current form panel
         */
        submitNextStep: function () {
            this.$el.submit();
            return this;
        },
        /**
         * prepare and enable fields to post the data
         * @param event
         * @returns {FormPanel}
         */
        prepareFormToPost: function () {
            //force to enable to post data
            if (this.project) {
                this.project.modalProgress.render();
            }
            this.$el.find(".form-control").prop('disabled', false);
            this.$el.find("input[type='hidden']").prop('disabled', false);
            this.$el.find(".pmdynaform-control-checkbox").prop('disabled', false);
            this.$el.find(".pmdynaform-control-checkgroup").prop('disabled', false);
            this.$el.find(".pmdynaform-control-radio").prop('disabled', false);
            return this;
        },
        executeSubmitArray: function () {
            var executeSubmit = true,
                responseCallback = true,
                indexSubmit;

            for (indexSubmit = 0; indexSubmit < this.submit.length; indexSubmit += 1) {
                if (typeof this.submit[indexSubmit] === "function") {
                    responseCallback = this.submit[indexSubmit]();
                    if (responseCallback !== undefined && typeof responseCallback === "boolean" && responseCallback === false) {
                        executeSubmit = false;
                        break;
                    }
                }
            }
            return executeSubmit;
        },
        isValid: function (event) {
            var i,
                formValid = true,
                itemField,
                itemsField = this.items.asArray();

            if (itemsField.length > 0) {
                for (i = 0; i < itemsField.length; i += 1) {
                    if (itemsField[i].validate) {
                        if (itemsField[i].firstLoad) {
                            itemsField[i].firstLoad = false;
                        }
                        itemsField[i].validate(event);
                        if (!itemsField[i].model.get("valid")) {
                            if (itemField === undefined) {
                                itemField = itemsField[i];
                            }
                            formValid = itemsField[i].model.get("valid");
                        }
                    }
                }
            }
            if (formValid) {
                for (i = 0; i < itemsField.length; i += 1) {
                    if (( itemsField[i].model.get("var_name") !== undefined) && (itemsField[i].model.get("var_name").trim().length === 0 )) {
                        if (itemsField[i].model.get("type") === "radio") {
                            itemsField[i].$el.find("input").attr("name", "");
                        }
                    }
                }
            } else {
                if (itemField && itemField.model.get("type") !== "grid" && itemField.model.get("type") !== "form") {
                    itemField.setFocus();
                }
            }
            return formValid;
        },
        render: function (subForm) {
            var i,
                j,
                $rowView;
            if (subForm) {
                this.el = document.createElement("div");
                this.$el = $(this.el);
            }
            for (i = 0; i < this.viewsBuilt.length; i += 1) {
                $rowView = $(this.templateRow());
                for (j = 0; j < this.viewsBuilt[i].length; j += 1) {
                    $rowView.append(this.viewsBuilt[i][j].render().el);
                }
                this.$el.append($rowView);
            }
            this.$el.attr("role", "form");
            this.$el.addClass("form-horizontal pmdynaform-form");
            this.el.style.height = "auto";
            this.setAction();
            this.setMethod();
            this.$el.attr("id", this.model.get("id"));
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
            return this;
        },
        afterRender: function () {
            var i,
                items = this.items.asArray();
            for (i = 0; i < items.length; i += 1) {
                if (items[i].afterRender) {
                    items[i].afterRender();
                }
            }
            return this;
        },
        setOnSubmit: function (callback) {
            if (callback && typeof callback === "function") {
                this.submit.push(callback);
            } else {
                return null;
            }
        },
        applyGlobalModeField: function (json) {
            if (this.project.globalMode && this.project.globalMode === 'view') {
                json.mode = this.project.globalMode;
            }
            return json;
        },
        applyGlobalMode: function () {
            if (this.project.globalMode === "view") {
                if (this.model.get("type") && this.model.get("type") === "form") {
                    this.model.set("mode", this.project.globalMode);
                }
            }
            return this;
        },
        /**
         * this method close this form, stand alone version for mobile
         */
        close: function () {
            this.model.close();
        },
        /**
         * This method looks fields from a valid criterion
         * @param  {String} criteria : es un criterio de filtro
         * @return {Array} result filter
         */
        searchFieldType: function (criteria) {
            var result = [],
                fields = this.getFields();
            if (criteria && criteria !== undefined) {
                result = _.filter(fields, function (item) {
                    if (item.model.get("type") === criteria) {
                        return item;
                    }
                });
            }
            return result;
        },
        saveForm: function () {
            var webServiceManager,
                formData,
                project = this.project,
                resp = true,
                panel = this.model.get("parent");
            if (project && panel) {
                webServiceManager = project.webServiceManager;
                formData = panel.getData2();
                webServiceManager.saveData({
                    formUID: this.model.get("id"),
                    data: formData
                }, function (err, data) {
                    if (!err) {
                        resp = true;
                    } else {
                        resp = false;
                    }
                });
                return resp;
            }
        },
        /**
         * Execute this function on after the submit button
         * @returns {FormPanel}
         */
        afterSubmit: function () {
            var items = this.items.asArray(),
                index;
            for (index = 0; index < items.length; index += 1) {
                if (items[index].afterSubmit) {
                    items[index].afterSubmit();
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.FormPanel", FormPanel);
}());

(function () {
    var FieldView = Backbone.View.extend({
        form: null,
        _hidden: false,
        tagName: "div",
        tagControl: "",
        tagHiddenToLabel: "",
        keyLabelControl: "",
        enableValidate: true,
        events: {
            "click .form-control": "onclickField"
        },
        initialize: function (options) {
            var defaults = {
                hidden: true
            };
            this.form = options.form ? options.form : null;
            jQuery.extend(true, defaults, options);

            if (defaults.hidden) {
                this.hide();
            } else {
                this.show();
            }

            if (options && options.project) {
                this.project = options.project;
            }
            this.setClassName()
                .render();
        },
        setColumnIndex: function (index) {
            this.columnIndex = index;
        },
        setClassName: function () {
            return this;
        },
        enableTooltip: function () {
            var tl = this.$el.find("[data-toggle=tooltip]");
            tl.tooltip({
                placement: "auto left",
                trigger: "click hover"
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

                if (!this.model.isValid()) {
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
                control.on(e, function (event) {
                    fn(event, that);

                    event.stopPropagation();
                });
            } else {
                throw new Error("Is not possible find the HTMLElement associated to field");
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
        render: function () {
            if (this._hidden || this.model.get("_hidden")) {
                this.hide();
            } else {
                this.show();
            }
            if (!this.model.get("enableValidate")) {
                this.hideRequire();
            }
            return this;
        },
        onclickField: function () {
            return this;
        },
        setLabel: function (label, col) {
            if (this.model.get("type") === "grid") {
                if (col !== undefined) {
                    if (col > 0 && col <= this.columnsModel.length) {
                        this.domTitleHeader[col - 1].find("span[class='title-column']").text(label);
                    } else {
                        return null;
                    }
                }
            } else {
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
        getLabel: function (col) {
            if (this.model.get("label") !== undefined) {
                if (col && this.model.get("type") === "grid") {
                    if (col <= this.columnsModel.length) {
                        return this.columnsModel[col - 1].label || "";
                    } else {
                        return null;
                    }
                } else {
                    return this.model.get("label");
                }
            }
            return null;
        },
        setText: function (value, row, col) {
            var options,
                data,
                existData,
                type,
                dataType,
                i,
                valuesfortrue,
                valuesforFalse;
            existData = false;
            type = this.model.get("type");
            dataType = this.model.get("dataType");
            data = {};
            if (type === "grid") {
                if (row !== undefined && col !== undefined) {
                    if ((row > 0 && col > 0) && row <= this.gridtable.length && col <= this.columnsModel.length) {
                        return this.gridtable[row - 1][col - 1].setText(value);
                    } else {
                        return null;
                    }
                }
            }
            if (value && value.toString().length || jQuery.isArray(value)) {
                options = this.model.get("options");
                if (options && jQuery.isArray(options)) {
                    if (dataType === "boolean" && options.length) {
                        valuesfortrue = [1, true, "1", "true"];
                        valuesforFalse = [0, false, "0", "false"];
                        if (options[0].label === value) {
                            value = options[0].value;
                        }
                        if (options[1].label === value) {
                            value = options[1].value;
                        }
                        if (valuesfortrue.indexOf(options[0].value) > -1 &&
                            valuesfortrue.indexOf(value) > -1) {
                            data = {
                                value: "1",
                                label: options[0].label
                            };
                            this.setValue(data["value"]);
                            return;
                        }
                        if (valuesforFalse.indexOf(options[1].value) > -1 &&
                            valuesforFalse.indexOf(value) > -1) {
                            data = {
                                value: "0",
                                label: options[1].label
                            };
                            this.setValue(data["value"]);
                            return;
                        }
                    } else {
                        if (type === "checkgroup") {
                            var arrayDataValue = [], arrayDataLabel = [];
                            for (i = 0; i < options.length; i += 1) {
                                options[i].selected = false;
                                if (value.indexOf(options[i].label) > -1) {
                                    options[i].selected = true;
                                    arrayDataLabel.push(options[i].label);
                                    arrayDataValue.push(options[i].value);
                                }
                            }
                            data = {
                                value: arrayDataValue,
                                label: arrayDataLabel
                            };
                            this.setValue(data["value"]);
                            return;
                        } else {
                            for (i = 0; i < options.length; i += 1) {
                                if (options[i].label === value) {
                                    data = {
                                        label: options[i].label,
                                        value: options[i].value
                                    };
                                    existData = true;
                                    this.setValue(data["value"]);
                                    return;
                                }
                            }
                        }
                    }
                }
                if (!existData) {
                    if (type === "text" || type === "textarea" || type === "hidden" || type === "link" || type === "image" ||
                        type == "title" || type == "annotation" || type == "subtitle" || type === "button" || type === "submit" || type === "suggest") {
                        data = {
                            value: value,
                            label: value
                        };
                        this.setValue(data["value"]);
                        return;
                        existData = true;
                    }
                    if (type === "datetime") {
                        value = value.replace(/-/g, "/");
                        if (new Date(value).toString() !== "Invalid Date") {
                            data = {
                                value: value,
                                label: value
                            }
                            this.setValue(data["value"]);
                            return;
                        }
                        existData = true;
                    }
                }
                if (existData) {
                    this.updateValueControlAndData(data, type, dataType);
                }
            } else {
                if (this.model.get("type") === "annotation") {
                    this.$el.find(".pmdynaform-control-annotation span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "button") {
                    this.$el.find("button[type='button'].btn-primary span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "submit") {
                    this.$el.find("button[type='submit'] span").text(value);
                    this.model.attributes.label = value;
                }
                if (this.model.get("type") === "image") {
                    this.model.attributes.src = value;
                    this.$el.find("img").attr("src", value);
                }
            }
            return this;
        },
        updateValueControlAndData: function (data, type, dataType) {
            var i;
            if (this.tagControl instanceof jQuery && this.tagHiddenToLabel instanceof jQuery) {
                if (type !== "datetime") {
                    this.model.attributes.data = data;
                    this.model.attributes.value = data["value"];
                    if (this.model.attributes.hasOwnProperty("keyLabel")) {
                        this.model.attributes.keyLabel = data["label"];
                    }
                    if (this.model.attributes.hasOwnProperty("keyValue")) {
                        this.model.attributes.keyValue = data["value"];
                    }
                    if (type === "radio") {
                        this.tagControl.find("input[id='form\[" + this.model.get("id") + "\]'][value='" + data["value"] + "']").prop("checked", true);
                    } else if (type === "checkgroup" || type === "checkbox") {
                        this.tagControl.find("input[type='checkbox']").attr("checked", false);
                        for (i = 0; i < data["value"].length; i += 1) {
                            this.tagControl.find("input[id='form\[" + this.model.get("id") + "\][" + data["value"][i] + "]']").prop("checked", true);
                        }
                        this.model.attributes.labelsSelected = data["label"];
                    } else {
                        this.tagControl.val(data["value"]);
                    }
                    if (type === "checkgroup") {
                        this.tagHiddenToLabel.val(JSON.stringify(data["label"]));
                    } else {
                        if (type === "link" || type === "annotation" || type === "title" || type === "subtitle" ||
                            type === "button" || type === "submit") {
                            this.tagHiddenToLabel.html(data["label"]);
                        } else if (type === "image") {
                            this.setSrc(data["value"]);
                        } else {
                            this.tagHiddenToLabel.val(data["label"]);
                        }
                    }
                    if (this.validate) {
                        this.validate();
                    }
                } else {
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
        setValue: function () {
        },
        getInfo: function () {
            return this.model.toJSON();
        },
        setHref: function (value) {
        },
        getDataType: function () {
            return this.model.get("dataType") || null;
        },
        getControlType: function () {

        },
        verifyData: function () {
        },
        getData: function () {
            return this.model.getData();
        },
        getDataLabel: function () {
            return this.model.getKeyLabel();
        },
        getText: function () {
        },
        getValue: function () {
            return this.model.getValue();
        },
        disableValidation: function (col) {
            var type, i, j;
            var col = col;
            type = this.model.get("type");
            if (type === "grid") {
                if (col !== undefined) {
                    if (typeof col == "string") {
                        this.columnsModel.find(function (column, index) {
                            if (column.columnId === col) {
                                col = index + 1;
                                return;
                            }
                        })
                    }
                    if ((col > 0 && col <= this.columnsModel.length)) {
                        this.domTitleHeader[col - 1].find(".pmdynaform-field-required").hide();
                        for (i = 0; i < this.gridtable.length; i += 1) {
                            this.columnsModel[col - 1].enableValidate = false;
                            this.gridtable[i][col - 1].disableValidation();
                        }
                    } else {
                        return null;
                    }
                } else {
                    var i, j, row, cell;
                    for (i = 0; i < this.gridtable.length; i += 1) {
                        row = this.gridtable[i];
                        for (j = 0; j < row.length; j += 1) {
                            cell = row[j];
                            if (cell.model.get("enableValidate") !== undefined) {
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
                if (this.model.get("required")) {
                    this.$el.find(".pmdynaform-field-required").hide();
                }
                this.model.attributes.enableValidate = false;
            }
            return this;
        },
        enableValidation: function (col) {
            var type, i, j, col = col;
            type = this.model.get("type");
            if (type === "grid") {
                if (col !== undefined) {
                    if (typeof col == "string") {
                        this.columnsModel.find(function (column, index) {
                            if (column.columnId === col) {
                                col = index + 1;
                                return;
                            }
                        })
                    }
                    if (col > 0 && col <= this.columnsModel.length) {
                        this.domTitleHeader[col - 1].find(".pmdynaform-field-required").show();
                        for (i = 0; i < this.gridtable.length; i += 1) {
                            this.columnsModel[col - 1].enableValidate = true;
                            this.gridtable[i][col - 1].enableValidation();
                        }
                    } else {
                        return null;
                    }
                } else {
                    var i, j, row, cell;
                    for (i = 0; i < this.gridtable.length; i += 1) {
                        row = this.gridtable[i];
                        for (j = 0; j < row.length; j += 1) {
                            cell = row[j];
                            if (cell.model.get("enableValidate") !== undefined) {
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
                if (this.model.get("group") === "form") {
                    this.$el.find(".pmdynaform-field-required").show();
                }
            }
            return this;
        },
        /**
         * Get Control HTML default
         * @returns {Array}
         */
        getControl: function () {
            return this.model.getControl();
        },
        getLabelControl: function () {

        },
        getHref: function () {
            return this.model.get("href");
        },
        setFocus: function () {
            if (this.getControl().length) {
                this.getControl().first().focus();
            }
        },
        /*
         This function change the values in the field formula associated, use with formulas
         Render a new values in the field with formula
         */
        onFieldAssociatedHandler: function () {
            var i,
                fieldsAssoc = this.formulaFieldsAssociated;
            if (fieldsAssoc.length > 0) {
                for (i = 0; i < fieldsAssoc.length; i += 1) {
                    if (fieldsAssoc[i].model.get("formulator") instanceof PMDynaform.core.Formula) {
                        this.model.addFormulaTokenAssociated(fieldsAssoc[i].model.get("formulator"));
                        this.model.updateFormulaValueAssociated(fieldsAssoc[i]);
                    }
                }
            }
            return this;
        },
        setData: function (data) {
            var value, label;
            if (this.model.get("type") !== "submit" && this.model.get("type") !== "button" && this.model.get("type") !== "panel") {
                if (this.model.get("type") === "label") {
                    this.setValue(data);
                } else {
                    value = data["value"];
                    this.setValue(value);
                }
            }
            return this;
        },
        setOnFieldFocusCallback: function (callback) {
            if (typeof callback === 'function' || callback === null) {
                this.onFieldFocusCallback = callback;
            }
            return this;
        },
        isDependent: function () {
            return this.model.isDependent();
        },
        updateValueHiddenControl: function (value) {
            var hidden;
            hidden = this.$el.find("input[type='hidden']");
            if (value !== null && value !== undefined) {
                if (hidden  instanceof jQuery && hidden.length) {
                    hidden.val(value);
                }
            }
            return this;
        },
        show: function () {
            if (this.el) {
                this.el.style.display = '';
            }
            this._hidden = false;
        },
        hide: function () {
            if (this.el) {
                this.el.style.display = 'none';
            }
            this._hidden = true;
        },
        isHidden: function () {
            return this._hidden;
        },
        hideRequire: function () {
            var tagRequired;
            tagRequired = this.$el.find(".pmdynaform-field-required");
            tagRequired.hide();
        },
        showRequire: function () {
            var tagRequired;
            tagRequired = this.$el.find(".pmdynaform-field-required");
            tagRequired.show();
        },
        /**
         * Default method clear File
         */
        clearContent: function () {
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Field", FieldView);
}());

(function () {
    var GridView = PMDynaform.view.Field.extend({
        block: true,
        template: _.template($("#tpl-grid").html()),
        templatePager: _.template($("#tpl-grid-pagination").html()),
        templateTotal: _.template($("#tpl-grid-totalcolumn").html()),
        templateEmptyGrid: _.template($("#tpl-grid-empty").html()),
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
            datetime: 6,
            dropdown: 7,
            text: 8,
            button: 9,
            link: 10,
            defect: 0
        },
        section: 1,
        titleHeader: [],
        indexResponsive: "3%",
        removeResponsive: "3%",
        thereArePriority: 0,
        columnsModel: [],
        domCarousel: null,
        tableBody: null,
        pageSize: null,
        paged: null,
        rowDataAdd: null,
        domTitleHeader: null,
        totalWidtRow: 0,
        colResponsiveTotalWidth: null,
        hiddenColumns: [],
        totalWidthStatic: 0,
        minCellWidth: 200,
        indexWidthStatic: 33,
        _$gridHeader: null,
        _gridHeader: null,
        deleteButtonVisibility: true,
        onDeleteRowCallback: function () {
        },
        onAddRowCallback: function () {
        },
        onBeforeAddRowCallback: function () {
        },
        onClickPageCallback: function () {
        },
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
        factory: {},
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
                        "multiplefile": {
                            model: PMDynaform.file.MultipleFileModel,
                            view: PMDynaform.file.MultipleFileView
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
            this.form = options.form ? options.form : null;
            this.pageSize = this.model.get("pageSize");
            this.paged = this.model.get("pager");
            this.colResponsiveTotalWidth = 0;
            this.items = [];
            this.row = [];
            this.dom = [];
            this.cols = [];
            this.showPage = 1;
            this.gridtable = this.model.get("gridtable");
            this.titleHeader = [];
            this.columnsModel = [];
            this.checkColSpanResponsive();
            this.setFactory(factory);
            this.rowDataAdd = [];
            this.dom = [];
            this.makeColumnModels();
            this.hiddenColumns = [];
            this.model.attributes.titleHeader = this.titleHeader;

        },
        onClickNew: function (e) {
            var newItem;
            newItem = this.addRow(e);
            return this;
        },
        addRow: function (data) {
            var j,
                row,
                rowData,
                currentRows,
                flagRow;
            currentRows = this.model.get("rows");
            this.rowDataAdd = [];
            if (this.model.get("layout") === "static") {
                flagRow = this.tableBody.find(".pmdynaform-static").last().children().length;
                this.domCarousel = this.tableBody.find(".pmdynaform-static").last();
            } else {
                flagRow = this.tableBody.children().last().children().length;
                this.domCarousel = this.tableBody.children().last();
            }
            if (flagRow === this.pageSize || flagRow === 0) {
                this.block = true;
                this.section = Math.ceil(this.dom.length / this.pageSize) + 1;
                flagRow = 0;
            } else {
                this.block = false;
                this.section = Math.ceil(this.dom.length / this.pageSize);
            }
            this.onBeforeAddRowCallback(this, this.model.attributes.rows, this.rowDataAdd);
            if (data && jQuery.isArray(data) && data.length) {
                this.rowDataAdd = data;
            }
            row = this.createHTMLRow(currentRows, this.rowDataAdd, flagRow);
            this.model.attributes.rows = parseInt(currentRows + 1, 10);
            this.model.attributes.rows = parseInt(currentRows + 1, 10);

            //new carousel container fix a in active mode
            if (this.model.get("rows") === 1) {
                this.domCarousel.addClass('active');
            }
            this.model.setPaginationItems();
            this.createHTMLPager("add");

            this.model.attributes.gridFunctions.push(row.data);

            this.runningRowFormulator(row.view);

            for (j = 0; j < row.model.length; j += 1) {
                if (row.model[j].get("type") !== "label" && row.model[j].get("operation") && row.model[j].get("operation").trim().length) {
                    row.view[j].onChangeCallbackOperation();
                }
                if (row.model[j].get("type") == "label" && row.model[j].get("operation")) {
                    this.createHTMLTotal();
                }
            }
            if (typeof this.onAddRowCallback === "function") {
                this.onAddRowCallback(this.gridtable[currentRows], this, this.gridtable.length);
            }
            this.validateGrid();
            return this.gridtable[currentRows];
        },
        runningRowFormulator: function (row) {
            var fieldsAsocied;
            for (var i = 0; i < row.length; i += 1) {
                if (row[i].model.get("formula") && row[i].model.get("formula").trim().length) {
                    fieldsAsocied = row.filter(function (element) {
                        if (row[i].fieldValid.indexOf(element.model.get("id")) > -1) {
                            element.onFieldAssociatedHandler();
                        }
                    });
                }
            }
        },
        removeRow: function (row) {
            var currentRows = this.model.get("rows"),
                itemRemoved;

            itemRemoved = this.gridtable.splice(row, 1);
            this.model.detachRegisteredEvents(itemRemoved);
            this.dom.splice(row, 1);
            this.model.attributes.rows = parseInt(currentRows - 1, 10);
            this.deleteFilesByRow(itemRemoved[0]);
            return itemRemoved;
        },
        makeColumnModels: function () {
            var columns = this.model.get("columns"),
                data = this.model.get("data"),
                columnModel,
                colSpanControl,
                factory = this.factory,
                product,
                newNameField,
                variableEnabled,
                jsonFixed,
                mergeModel,
                i;
            this.columnsModel = [];
            for (i = 0; i < columns.length; i += 1) {
                newNameField = "";
                mergeModel = columns[i];
                mergeModel.form = this.model.get("form") || null;
                if (mergeModel.mode && mergeModel.mode === "parent") {
                    mergeModel.mode = this.model.get("mode");
                }
                if ((mergeModel.originalType === "checkbox" || mergeModel.type === "checkbox" ) && mergeModel.mode === "view") {
                    mergeModel.mode = "disabled";
                    mergeModel.disabled = true;
                }
                if ((mergeModel.originalType === "checkbox" || mergeModel.type === "checkbox" ) && mergeModel.mode === "disabled") {
                    mergeModel.mode = "disabled";
                    mergeModel.disabled = true;
                }
                jsonFixed = new PMDynaform.core.TransformJSON({
                    parentMode: this.model.get("parentMode"),
                    field: mergeModel
                });
                if (jsonFixed.getJSON().type) {
                    product = factory.products[jsonFixed.getJSON().type.toLowerCase()] ?
                        factory.products[jsonFixed.getJSON().type.toLowerCase()] : factory.products[factory.defaultProduct];
                } else {
                    product = factory.products[factory.defaultProduct];
                }
                colSpanControl = this.colSpanControlField(jsonFixed.getJSON().type, i);
                columnModel = {
                    colSpanLabel: 4,
                    colSpanControl: (this.model.get("layout") === "form") ? 8 : colSpanControl,
                    colSpan: colSpanControl,
                    label: mergeModel.title,
                    title: mergeModel.title,
                    layout: this.model.get("layout"),
                    width: "200px",
                    project: this.model.get("project"),
                    namespace: this.model.get("namespace"),
                    mode: mergeModel.mode,
                    variable: (variableEnabled !== "NOT") ? this.getVariable(mergeModel.var_uid) : null,
                    _extended: {
                        name: mergeModel.name || PMDynaform.core.Utils.generateName("radio"),
                        id: mergeModel.id || PMDynaform.core.Utils.generateID(),
                        formula: mergeModel.formula || null
                    },
                    group: "grid",
                    columnName: mergeModel.name || PMDynaform.core.Utils.generateName("radio"),
                    columnId: mergeModel.id,
                    originalType: mergeModel.type,
                    product: product,
                    formula: mergeModel.formula || "",
                    operation: mergeModel.operation || "",
                    columnWidth: mergeModel.columnWidth || "",
                    defaultValue: mergeModel.defaultValue || "",
                    sql: mergeModel.sql || "",
                    required: mergeModel.required || false,
                    hint: mergeModel.hint || "",
                    format: mergeModel.format || null,
                    form: mergeModel.form || null,
                    options: mergeModel.options || [],
                    optionsSql: mergeModel.optionsSql || [],
                    defaultDate: mergeModel.defaultDate || null,
                    enableValidate: true,
                    parent: this.model
                };
                jQuery.extend(true, columnModel, jsonFixed.getJSON());
                columnModel.row = this.gridtable.length;
                columnModel.col = i;
                if (this.model.get("layout") == "static") {
                    if (columnModel.columnWidth && jQuery.isNumeric(columnModel.columnWidth)) {
                        var width = parseInt(columnModel.columnWidth);
                        this.totalWidtRow = this.totalWidtRow + width;
                    } else {
                        this.totalWidtRow = this.totalWidtRow + 200;
                    }
                }
                this.columnsModel.push(columnModel);
            }
            if (this.model.get("layout") == "responsive") {
                this.updateWidthResponsiveColumns();
            }

            return this;
        },
        updateWidthResponsiveColumns: function () {
            var i, totalWith = 0, width, undefinedWidth = [];
            if (this.columnsModel.length) {
                for (i = 0; i < this.columnsModel.length; i += 1) {
                    width = parseInt(this.columnsModel[i].columnWidth).toString();
                    if (width !== "NaN") {
                        if (totalWith + Number(width) < 94) {
                            totalWith = totalWith + Number(width);
                            this.columnsModel[i].columnWidth = Number(width) + "%";
                        } else {
                            if (94 - totalWith > 0) {
                                this.columnsModel[i].columnWidth = 94 - totalWith + "%";
                                totalWith = totalWith + 94 - totalWith;
                            } else {
                                this.columnsModel[i].columnWidth = 0 + "%";
                                undefinedWidth.push(this.columnsModel[i]);
                            }
                        }
                    } else {
                        this.columnsModel[i].columnWidth = 0 + "%";
                        undefinedWidth.push(this.columnsModel[i]);
                    }
                }
            }
            return this;
        },
        setValuesGridFunctions: function (field) {

            if (this.model.attributes.functions) {
                if (this.model.attributes.gridFunctions.length > 0) {
                    if (this.model.attributes.gridFunctions[field.row]) {
                        this.model.attributes.gridFunctions[field.row][field.col] = isNaN(parseFloat(field.data)) ? 0 : parseFloat(field.data);
                    }
                    this.model.applyFunction();
                }
            }
            return this;
        },
        getVariable: function (var_uid) {
            var i,
                varSelected,
                variables = this.model.get("variables");
            loop_variables:
                if (_.isArray(variables)) {
                    for (i = 0; i < variables.length; i += 1) {
                        if (variables[i] && variables[i].var_uid === var_uid) {
                            varSelected = variables[i];
                            break loop_variables;
                        }
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
                this.numberRest = 10 % columns.length;

                if (this.numberRest > 0) {
                    for (i = 0; i < columns.length; i += 1) {
                        if (this.priority[columns[i].type] <= 6) {
                            thereArePriority += 1;
                        }
                    }
                }
                this.thereArePriority = thereArePriority;
            }
            return this;
        },
        colSpanControlField: function (type, indexColumn) {
            var itemsLength = this.model.get("columns").length,
                layout = this.model.get("layout"),
                defaultColSpan = 8;
            if (this.numberRest > 0) {
                if (this.priority[type] <= 6 && this.thereArePriority > 0) {
                    defaultColSpan = parseInt(10 / itemsLength) + 1;
                    this.numberRest -= 1;
                    this.thereArePriority -= 1;
                } else {
                    if (this.numberRest >= parseInt(itemsLength - indexColumn)) {
                        defaultColSpan = parseInt(10 / itemsLength) + 1;
                        this.numberRest -= 1;
                    } else {
                        defaultColSpan = parseInt(10 / itemsLength);
                    }
                }
            } else {
                defaultColSpan = parseInt(10 / itemsLength);
            }

            return defaultColSpan;
        },
        colSpanControlFieldResponsive: function () {
            var columnWidth = 100, res;
            res = parseInt(this.indexResponsive) + parseInt(this.removeResponsive);
            columnWidth = parseInt((columnWidth - res) / (this.columnsModel.length - this.model.get("countHiddenControl")));
            return columnWidth - 1;
        },
        /*
         form[grid1][1][nombre]
         form[grid1][2][nombre]
         */
        changeIdField: function (nameform, row, column) {
            return "[" + nameform + "][" + row + "][" + column + "]";
        },
        changeNameField: function (nameform, row, column) {
            return "[" + nameform + "][" + row + "][" + column + "]";
        },
        updateNameFields: function (rowView) {
            var i,
                l,
                formulaFields = "";
            for (i = 0; i < rowView.length; i += 1) {
                formulaFields = rowView[i].model.get("_extended").formula;
                if (typeof formulaFields === "string") {
                    for (l = 0; l < rowView.length; l += 1) {
                        if (i !== l) {
                            formulaFields = formulaFields.replace(new RegExp(rowView[l].model.get("_extended").id, 'g'), rowView[l].model.get("id"));
                            rowView[i].model.attributes.formula = formulaFields;
                            rowView[i].model.attributes.formulator.data = formulaFields;
                        }
                    }
                }
            }
            return this;
        },
        setFactory: function (factory) {
            this.factory = factory;
            return this;
        },
        validate: function (event) {
            var i,
                k,
                row = [],
                validGrid = true,
                gridpanel = this.gridtable,
                itemCell;
            if (!this.validateGrid()) {
                return this;
            }
            for (i = 0; i < gridpanel.length; i += 1) {
                row = [];
                for (k = 0; k < gridpanel[i].length; k += 1) {
                    if (gridpanel[i][k].validate) {
                        if (gridpanel[i][k].firstLoad) {
                            gridpanel[i][k].firstLoad = false;
                        }
                        if (event) {
                            gridpanel[i][k].validate(event);
                            if (!gridpanel[i][k].model.get("valid")) {
                                if (itemCell === undefined) {
                                    itemCell = gridpanel[i][k];
                                }
                                validGrid = gridpanel[i][k].model.get("valid");
                                this.model.set("valid", validGrid);
                                validGrid = false;
                            }
                        } else {
                            gridpanel[i][k].validate();
                            validGrid = gridpanel[i][k].model.get("valid");
                            if (!validGrid) {
                                gridpanel[i][k].setFocus();
                                this.model.attributes.valid = false;
                            }
                        }
                    }
                }
            }
            if (itemCell) {
                itemCell.setFocus();
            }
            this.model.set("valid", validGrid);
            return validGrid;
        },
        onRemoveRow: function (event) {
            var rowNumber;
            if (event) {
                rowNumber = $(event.target).data("row");
                this.deleteRow(rowNumber, event);
            }

            return this;
        },
        updateGridFunctions: function (rows, index) {
            this.model.attributes.gridFunctions.splice(index - 1, 1);
            this.model.applyFunction();
            this.createHTMLTotal();
            return this;
        },
        deleteRow: function (index, event) {
            var itemRemoved,
                showPage,
                removedSection,
                initPage,
                i;
            showPage = Math.ceil(index / this.pageSize);
            jQuery(this.dom[index - 1]).remove();
            if (index > 0) {
                itemRemoved = this.removeRow(index - 1);
            } else {
                return this;
            }
            this.updateGridFunctions(itemRemoved, index);
            this.updatePropertiesCell(index - 1);
            if (this.model.attributes.pager) {
                this.block = true;
                this.flagRow = 0;
                this.section = 0;
                if (this.model.get("layout") === "static") {
                    for (var i = showPage; i < this.tableBody.find(".pmdynaform-static").length; i += 1) {
                        if (this.tableBody.find(".pmdynaform-static").eq(i).children().length) {
                            this.tableBody.find(".pmdynaform-static").eq(i - 1).append(this.tableBody.find(".pmdynaform-static").eq(i).children()[0])
                        }
                    }
                    if (this.tableBody.find(".pmdynaform-static").eq(i - 1).children().length === 0) {
                        removedSection = this.tableBody.find(".pmdynaform-static").eq(i - 1).remove();
                        if (i == 1) {
                            initPage = true;
                        }
                    }
                    if (!this.tableBody.find(".pmdynaform-static").eq(showPage - 1).children().length) {
                        if (this.tableBody.find(".pmdynaform-static").eq(showPage - 2).length) {
                            this.tableBody.find(".pmdynaform-static").eq(showPage - 2).addClass("active");
                        }
                    }
                } else {
                    for (i = showPage; i < this.tableBody.children().length; i += 1) {
                        if (this.tableBody.children().eq(i).children().length) {
                            this.tableBody.children().eq(i - 1).append(this.tableBody.children().eq(i).children()[0])
                        }
                    }
                    if (this.tableBody.children().eq(i - 1).children().length === 0) {
                        removedSection = this.tableBody.children().eq(i - 1).remove();
                    }
                    if (!this.tableBody.children().eq(showPage - 1).children().length) {
                        if (this.tableBody.children().eq(showPage - 2).length) {
                            this.tableBody.children().eq(showPage - 2).addClass("active");
                        }
                    }
                }
                this.model.setPaginationItems();
                this.createHTMLPager("remove");
                if (removedSection && removedSection.length) {
                    this.showPage = showPage - 1;
                    if (initPage) {
                        this.showPage = 1;
                    }
                } else {
                    this.showPage = showPage;
                }
            }
            if (typeof this.onDeleteRowCallback === "function") {
                this.onDeleteRowCallback(this, itemRemoved, index);
            }
            this.validateGrid();
            return this;
        },
        updatePropertiesCell: function (index) {
            var i,
                j,
                cell,
                cells,
                row,
                rows,
                element,
                name,
                control,
                container,
                idContainer,
                hiddenControls,
                type,
                nameHiddeControl = "",
                nameControl = "",
                idcontrol;
            rows = this.gridtable;
            for (i = index; i < rows.length; i += 1) {
                row = $(this.dom[i]);
                row.find(".index-row span").text(i + 1);
                row.find(".remove-row button").data("row", i + 1);
                cells = rows[i];
                if (cells) {
                    for (j = 0; j < cells.length; j += 1) {
                        cell = cells[j];
                        cell.model.attributes.row = i;
                        idContainer = this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
                        element = cell.$el;
                        container = element.find(".pmdynaform-" + cell.model.get("mode") + "-" + cell.model.get("type"));
                        container.attr({
                            "id": idContainer
                        });
                        type = cell.model.get("type");
                        switch (type) {
                            case "checkbox":
                                control = $(cell.$el.find("input[type='checkbox']"));
                                hiddenControls = element.find("input[type='hidden']");
                                if (this.model.get("variable") !== "") {
                                    nameControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                                    nameHiddeControl = nameControl.substring(0, nameControl.length - 1).concat("_label]");
                                }
                                idcontrol = "form" + this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
                                control.attr({
                                    name: nameControl,
                                    id: idcontrol
                                });
                                hiddenControls.attr({
                                    name: nameHiddeControl,
                                    id: idcontrol
                                });
                                break;
                            case "suggest":
                                control = $(cell.$el.find(".form-control"));
                                hiddenControls = element.find("input[type='hidden']");
                                if (this.model.get("variable")) {
                                    nameControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                                    nameControl = nameControl.substring(0, nameControl.length - 1).concat("_label]");
                                    nameHiddeControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                                } else {
                                    nameControl = "";
                                    nameHiddeControl = ""
                                }
                                idcontrol = "form" + this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
                                control.attr({
                                    name: nameControl,
                                    id: idcontrol
                                });
                                hiddenControls.attr({
                                    name: nameHiddeControl,
                                    id: idcontrol
                                });
                                break;
                            case "label":
                                hiddenControls = element.find("input[type='hidden']");
                                if (this.model.get("variable") !== "") {
                                    nameControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                                    nameHiddeControl = nameControl.substring(0, nameControl.length - 1).concat("_label]");
                                } else {
                                    nameControl = "";
                                    nameHiddeControl = "";
                                }
                                idcontrol = "form" + this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
                                hiddenControls.eq(0).attr({
                                    name: nameControl,
                                    id: idcontrol
                                });
                                hiddenControls.eq(1).attr({
                                    name: nameHiddeControl,
                                    id: idcontrol
                                });
                                break;
                            case "file":
                                this.updateFileCell(cell, i, j);
                                // TODO need refactor
                                break;
                            case "multipleFile":
                                this.updateMultipleFileCell(cell, i, j);
                                break;
                            default :
                                control = $(cell.$el.find(".form-control"));
                                hiddenControls = element.find("input[type='hidden']");
                                if (this.model.get("variable") !== "") {
                                    nameControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                                    nameHiddeControl = nameControl.substring(0, nameControl.length - 1).concat("_label]");
                                } else {
                                    nameControl = "";
                                    nameHiddeControl = "";
                                }
                                idcontrol = "form" + this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
                                control.attr({
                                    name: nameControl,
                                    id: idcontrol
                                });
                                hiddenControls.attr({
                                    name: nameHiddeControl,
                                    id: idcontrol
                                });
                                break;
                        }
                    }
                }
            }
            return this;
        },
        /**
         * force to update the hidden tags to send that by POST method to server
         * @param cell
         * @param i
         * @param j
         */
        updateMultipleFileCell: function (cell, i, j) {
            var files,
                k,
                index = i + 1,
                name = "[" + this.model.get('name') + "][" + index + "][" + cell.model.get('columnName') + "]";

            cell.model.set('name', name);
            cell.model.set('id', name);
            cell.model.set('nameToPostControl', this.createPostVariables(index, cell.model.get('columnName')));
            cell.model.set('nameToPostLabelControl', this.createPostVariables(index, cell.model.get('columnName'), '_label'));
            cell.model.get('view').removeHiddens();

            files = cell.model.get("fileCollection");
            cell.model.get('view').removeHiddens();
            if (_.isArray(files.models)) {
                for (k = 0; k < files.models.length; k += 1) {
                    cell.model.get('view').createHiddenByModel(files.models[k]);
                }
            }

        },
        /**
         * Updates File cell when a row has been removed
         *
         * @param cell
         */
        updateFileCell: function (cell, i, j) {
            var element = cell.$el,
                control = $(cell.$el.find(".form-control")),
                hiddenControls = element.find("input[type='hidden']"),
                fileControls = element.find("input[type='file']"),
                nameControl,
                nameHiddeControl,
                idcontrol;

            if (this.model.get("variable") !== "") {
                nameControl = "form" + this.changeIdField(this.model.get("name"), i + 1, cell.model.get("columnName"));
                nameHiddeControl = nameControl.substring(0, nameControl.length - 1).concat("_label]");

            } else {
                nameControl = "";
                nameHiddeControl = "";
            }
            idcontrol = "form" + this.changeIdField(this.model.get("id"), i + 1, this.columnsModel[j].id);
            control.attr({
                name: nameControl,
                id: idcontrol
            });
            hiddenControls.attr({
                name: nameHiddeControl,
                id: idcontrol
            });

            fileControls.attr({
                name: nameControl,
                id: idcontrol
            });
        },

        onClickPage: function (event) {
            var objData = $(event.currentTarget.children).data(),
                parentNode = $(event.currentTarget).parent(),
                i,
                index,
                nextItemElement,
                nextItem,
                prevItem;

            /************************** pagination rotate ************************************/
            nextItem = $('<li class="toNext"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">...</a></li>');
            prevItem = $('<li class="toPrev"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">...</a></li>');

            if (!$.isNumeric($(event.currentTarget).find("a").text())) {
                var $currentItem = parentNode.find('li.active');
                if ($(event.currentTarget).hasClass("toNextItem")) {
                    if ($currentItem.hasClass("toNext")) {
                        this.onClickNextSection($currentItem, parentNode, nextItem, prevItem);
                    } else {
                        if ($currentItem.next().attr("class") !== "toNextItem") {
                            parentNode.find('li').removeClass('active');
                            $currentItem.next().addClass("active");
                            $currentItem.next().find("a:eq(0)").trigger("click");
                        }
                    }
                }
                if ($(event.currentTarget).hasClass("toPrevItem")) {
                    if ($currentItem.hasClass("toPrev")) {
                        this.onClickPrevSection($currentItem, parentNode, nextItem, prevItem);
                    } else {
                        if ($currentItem.prev().attr("class") !== "toPrevItem") {
                            parentNode.find('li').removeClass('active');
                            $currentItem.prev().addClass("active");
                            $currentItem.prev().find("a:eq(0)").trigger("click");
                        }
                    }
                }
                if ($(event.currentTarget).hasClass("toLast")) {
                    var lastPosition = parseInt(parentNode.children().length) - 3;
                    if (!this.model.get("paginationRotate")) {
                        this.tableBody.find(".active").removeClass("active");
                        var e = parentNode.find('li:eq(' + lastPosition + ')');
                        if (parentNode.find("li.active a").text() != Math.ceil(this.gridtable.length / this.pageSize)) {
                            this.onClickNextSection(e, parentNode, nextItem, prevItem);
                        }
                        this.tableBody.children().last().addClass("active")
                    }
                    parentNode.find('.active').removeClass('active');
                    lastPosition = parseInt(parentNode.children().length) - 3;
                    parentNode.find('li:eq(' + lastPosition + ')').addClass("active");
                    return false;
                }
                if ($(event.currentTarget).hasClass("toFirst")) {
                    if (!this.model.get("paginationRotate")) {
                        var e = parentNode.find('li:eq(3)');
                        if (parentNode.find("li.active a").text() !== "1") {
                            this.onClickPrevSection(e, parentNode, nextItem, prevItem);
                        }
                    }
                    parentNode.find('li').removeClass('active');
                    parentNode.find('li:eq(2)').addClass("active");
                }
                if ($(event.currentTarget).hasClass("toNext")) {
                    index = Number($(event.currentTarget).prev().text().trim());
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
                    $(parentNode).find('.toPrev').remove();
                    $(parentNode).find('.toNext').remove();
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
                        display: "none"
                    });
                    if (((this.gridtable.length / this.pageSize) - index) > 5) {
                        for (i = index; i < index + 5; i += 1) {
                            if (!nextItemElement) {
                                nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                            }
                            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                                display: ""
                            }).addClass("showItem");
                        }
                        $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).before(nextItem);
                    } else {
                        for (i = index; i < (this.gridtable.length / this.pageSize); i += 1) {
                            if (!nextItemElement) {
                                nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                            }
                            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                                display: ""
                            }).addClass("showItem");
                        }
                    }
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index - 1).after(prevItem);
                    if (nextItemElement) {
                        nextItemElement.find("a").trigger("click");
                    }
                }
                if ($(event.currentTarget).hasClass("toPrev")) {
                    index = Number($(event.currentTarget).prev().text().trim());
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
                    $(parentNode).find('.toPrev').remove();
                    $(parentNode).find('.toNext').remove();
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
                        display: "none"
                    });

                    if (index - 5 != 0) {
                        if (index - 5 > -1) {
                            for (i = index - 1; i >= index - 5; i -= 1) {
                                if (!nextItemElement) {
                                    nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                                }
                                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                                    display: ""
                                }).addClass("showItem");
                            }
                            nextItemElement.after(nextItem);
                            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).after(prevItem);
                            if (nextItemElement) {
                                nextItemElement.find("a").trigger("click");
                            }
                        } else {

                            for (i = 5 - 1; i >= 0; i -= 1) {
                                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                                    display: ""
                                }).addClass("showItem");
                            }
                            if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index - 1).length) {
                                nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(index - 1);
                            }
                            $(parentNode).find(".showItem").last().after(prevItem);
                            if (nextItemElement) {
                                nextItemElement.find("a").trigger("click");
                            }
                        }
                    } else {
                        for (i = index - 1; i > -1; i -= 1) {
                            if (!nextItemElement) {
                                nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                            }
                            $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                                display: ""
                            }).addClass("showItem");
                        }
                        nextItemElement.after(nextItem);
                        if (nextItemElement) {
                            nextItemElement.find("a").trigger("click");
                        }
                    }
                }
            } else {
                parentNode.children().removeClass('active');
                $(event.currentTarget).addClass("active");
            }
            return this;
        },
        onClickNextSection: function (currentTarget, parentNode, nextItem, prevItem) {
            if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length - 1 > 5) {
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css("display", "none");
                $(parentNode).find('.toPrev').remove();
                $(parentNode).find('.toNext').remove();
                var i, nextItemElement, length = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length - 1;
                for (i = length; i > length - 5; i -= 1) {
                    if (!nextItemElement) {
                        nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                    }
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                        display: ""
                    }).addClass("showItem");
                }
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).after(prevItem);
                if (nextItemElement) {
                    nextItemElement.find("a").trigger("click");
                }
            }
        },
        onClickPrevSection: function (currentTarget, parentNode, nextItem, prevItem) {
            if ($(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').length - 1 > 5) {
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').removeClass("showItem");
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css("display", "none");
                $(parentNode).find('.toPrev').remove();
                $(parentNode).find('.toNext').remove();
                var i, nextItemElement;
                for (i = 0; i < 5; i += 1) {
                    if (!nextItemElement) {
                        nextItemElement = $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i);
                    }
                    $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).css({
                        display: ""
                    }).addClass("showItem");
                }
                $(parentNode).children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(i).before(nextItem);
                if (nextItemElement) {
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

            for (i = 0; i < trs.length; i += 1) {
                element = $(trs[i]).html();
                tdNumber = this.createRowNumber(i + 1);
                buttonRemove = this.createRemoveButton(i);
                $(trs[i].firstChild).replaceWith(tdNumber);
                $(trs[i].lastChild).replaceWith(buttonRemove);
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

                tdRemove = this.createRemoveButton(index - 1);
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
            if (layout === "form") {
                tdRemove.className = "pmdynaform-grid-removerow hidden-xs col-xs-1 col-sm-1 col-md-1 col-lg-1";
            }
            if (layout === "static") {
                tdRemove.className = "pmdynaform-grid-removerow-static";
            }
            if (layout === "responsive") {
                tdRemove.className = "pmdynaform-grid-removerow-responsive";
                tdRemove.style.display = "inline-block";
            }
            buttonRemove = document.createElement("button");

            buttonRemove.className = "glyphicon glyphicon-trash btn btn-danger btn-sm";
            buttonRemove.setAttribute("data-row", index);

            $(buttonRemove).data("row", index);
            $(buttonRemove).on("click", function (event) {
                that.onRemoveRow(event);
            });

            tdRemove.appendChild(buttonRemove);
            return tdRemove;
        },
        createHTMLTitle: function () {

            var k,
                dom,
                title,
                td,
                colSpan,
                label,
                layout = this.model.get("layout"),
                content,
                hint,
                spaceDelete;
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
            if (layout === "form") {
                td.className = "col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center wildcard";
            }

            if (layout === "responsive") {
                //For the case: responsive and form
                td.className = "text-center wildcard";
                td.style.display = "inline-block";
                td.style.width = this.indexResponsive;
            }
            td.appendChild(label);
            dom.append(td);
            for (k = 0; k < this.columnsModel.length; k += 1) {
                if (this.columnsModel[k].type !== "hidden") {
                    colSpan = this.columnsModel[k].colSpan;
                    title = this.columnsModel[k].title;
                    td = document.createElement("div");
                    label = document.createElement("span");
                    label.className = "title-column";
                    this.checkColSpanResponsive();

                    if (layout !== "responsive") {
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
                        "text-align": "center"
                    });
                    if (layout === "responsive") {
                        $(label).css({
                            width: "70%",
                            display: "inline-block"
                        });
                        $(td).css({
                            width: this.colSpanControlFieldResponsive(this.columnsModel) + "%",
                            display: "inline-block"
                        });
                    }
                    if (layout === "static") {
                        if (this.columnsModel[k]["columnWidth"] && Number(this.columnsModel[k]["columnWidth"]).toString() !== "NaN") {
                            $(td).css({
                                "min-width": parseInt(this.columnsModel[k]["columnWidth"])
                            });
                            $(label).css({
                                "width": parseInt(this.columnsModel[k]["columnWidth"]) - 40
                            });
                        } else {
                            $(td).css({
                                "min-width": "200px"
                            });
                            $(label).css({
                                "width": "160px"
                            });
                        }
                    }
                    if (layout === "responsive") {
                        $(td).css({
                            "width": this.columnsModel[k].columnWidth
                        });
                    }
                    label.title = title;

                    td.appendChild(label);
                    if (this.columnsModel[k].required) {
                        if (parseInt(this.columnsModel[k].columnWidth) === 0) {
                            td.appendChild($("<span class='pmdynaform-field-required'>*</span>")[0]);
                            label.style.display = "none";
                        } else {
                            td.appendChild($("<span class='pmdynaform-field-required'>*</span>")[0]);
                        }
                    }

                    hint = document.createElement("span");
                    if (this.columnsModel[k].hint && this.columnsModel[k].hint.trim().length) {
                        hint = document.createElement("span");
                        hint.className = "glyphicon glyphicon-info-sign";
                        hint.setAttribute("data-toggle", "tooltip");
                        hint.setAttribute("data-container", "body");
                        hint.setAttribute("data-placement", "bottom");
                        hint.setAttribute("data-original-title", this.columnsModel[k]["hint"]);
                        hint.style.float = "inherit";
                        $(hint).tooltip().click(function (e) {
                            $(this).tooltip('toggle');
                        });
                        if (this.model.get("columns").length < 6 && (layout == "responsive" || layout == "form")) {
                            td.appendChild(hint);
                        } else {
                            if (layout === "static") {
                                td.appendChild(hint);
                            } else {
                                label.setAttribute("data-toggle", "tooltip");
                                label.setAttribute("data-container", "body");
                                label.setAttribute("data-placement", "bottom");
                                label.setAttribute("data-original-title", this.columnsModel[k]["hint"]);
                            }
                        }
                    }
                    dom.append(td);
                    this.domTitleHeader.push($(td));
                } else {
                    this.domTitleHeader.push($("<span></span>"));
                }
            }
            if (layout === "static") {
                spaceDelete = document.createElement("div");
                spaceDelete.className = "pmdynaform-grid-removerow-static";
                $(spaceDelete).css({
                    "min-width": 38
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
            if (activeIndex.length) {
                htmlPager = pagerContainer.children();
                pagerItems = htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').not('.toPrev').not('.toNext');
                if (behavior == "add") {
                    if (Math.ceil(this.gridtable.length / this.pageSize) > pagerItems.length) {
                        elementList = jQuery("<li class = 'sec_" +
                            Math.ceil(this.gridtable.length / 5) + "'><a data-target='#" + this.model.get("id") + "-body' data-slide-to='" +
                            (Math.ceil(this.gridtable.length / this.pageSize) - 1) + "' href=''>" + Math.ceil(this.gridtable.length / this.pageSize) + "</a></li>");
                        elementList.css({display: "none"});
                        if (htmlPager.find(".toNext").length == 0 && (Number(elementList.text().trim()) > 5)) {
                            ellipsis = jQuery('<li class="toNext"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">...</a></li>');
                            htmlPager.find(".showItem").last().after(ellipsis);
                            ellipsis.after(elementList);
                        } else {
                            if (htmlPager.find(".toNext").length) {
                                htmlPager.find(".toNextItem").before(elementList);
                            } else {
                                elementList.css({
                                    display: ""
                                }).addClass("showItem");
                                htmlPager.find(".showItem").last().after(elementList);
                            }
                        }
                    }
                }
                if (behavior == "remove") {
                    var itemRemoved;
                    if (Math.ceil(this.gridtable.length / this.pageSize) > 0 && Math.ceil(this.gridtable.length / this.pageSize) < pagerItems.length) {
                        if (pagerItems.eq(pagerItems.length - 1).hasClass("active")) {
                            pagerItems.eq(pagerItems.length - 1).prev().addClass("active");
                        }
                        itemRemoved = pagerItems.eq(pagerItems.length - 1).remove();
                        if (htmlPager.find(".active").hasClass("toPrev")) {
                            htmlPager.find(".active").trigger("click");
                            htmlPager.find(".toNext").remove();
                        }
                        if (htmlPager.find(".active").text().trim() == 5) {
                            htmlPager.find(".toPrev").remove();
                            htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').css({
                                display: ""
                            });
                        }
                        if (Number(htmlPager.find(".showItem").last().text().trim()) <= (this.gridtable.length / 5)) {
                            htmlPager.find(".toNext").remove();
                        }
                    }
                }
            } else {
                pager = this.templatePager({
                    id: this.model.get("id") + "-body",
                    paginationItems: this.model.get("paginationItems"),
                    paginationRotate: this.model.get("paginationRotate"),
                    itemsSections: Math.ceil(this.dom.length / this.pageSize)
                });
                htmlPager = $(pager);
                pagerItems = htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem');
                htmlPager.children().not(":first").not(':last').not('.toPrevItem').not('.toNextItem').eq(0).addClass("active");
                if (Math.ceil(this.gridtable.length / 5) > 5) {
                    pagerItems.eq(5 - 1).nextAll().not(':last').not('.toNextItem').css({display: "none"});
                    pagerItems.eq(5).prevAll().not(".toFirst").addClass("showItem");
                    pagerItems.eq(5).before('<li class="toNext"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">...</a></li>');
                } else {
                    pagerItems.addClass("showItem");
                }
                htmlPager.children('li:first').after('<li class="toPrevItem"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">&lsaquo;</a></li>');
                htmlPager.children('li:last').before('<li class="toNextItem"><a data-target="#' + this.model.get("id") + '" data-rotate="' + this.model.get("paginationRotate") + '" href="javascript:void(0)">&rsaquo;</a></li>');
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
                containerStaticGrid,
                totalrow = this.model.get("totalrow"),
                layout = this.model.get("layout"),
                iconTotal = {
                    sum: "&#8721;",
                    avg: "&#935;",
                    other: "&#989;"
                },
                that = this;
            if (totalrow.length) {
                dom = this.$el.find(".pmdynaform-grid-functions");
                dom.children().remove();
                td = document.createElement("div");
                label = document.createElement("span");

                if (layout === "static") {
                    dom.addClass("pmdynaform-grid-thead-static");
                    if (this.$el.find(".pmdynaform-grid-static").length) {
                        dom.css({
                            width: this.totalWidtRow + 77
                        });
                    }
                } else {
                    //For the case: responsive and form
                    td.className = "col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center";
                }
                td.appendChild(label);
                dom.append(td);
                if (layout === "responsive") {
                    td.style.width = this.indexResponsive;
                } else {
                    $(td).css({
                        width: this.indexWidthStatic,
                        display: "inline-block"
                    });
                }
                if (this.gridtable[0]) {
                    for (k = 0; k < this.gridtable[0].length; k += 1) {
                        colSpan = this.gridtable[0][k].model.get("colSpan");
                        title = (totalrow[k] === null || totalrow[k] === undefined) ? '' : totalrow[k];
                        td = document.createElement("div");
                        if (this.hiddenColumns.indexOf(k + 1) > -1) {
                            td.style.display = "none";
                        }
                        label = document.createElement("span");
                        result = document.createElement("input");
                        result.style.width = "50%";
                        jQuery(result).attr('readonly', true);
                        if (layout === "form") {
                            this.checkColSpanResponsive();
                            colSpan = this.colSpanControlField(this.gridtable[0], this.gridtable[0][k].model.get("type"), k);
                            td.className = "col-xs-12 col-sm-" + colSpan + " col-md-" + colSpan + " col-lg-" + colSpan;
                        } else {
                            if (layout === "static") {
                                $(td).css({
                                    display: "inline-block"
                                });
                                if (this.gridtable[0][k].model.get("columnWidth") && Number(this.gridtable[0][k].model.get("columnWidth")).toString() !== "NaN") {
                                    $(td).css({
                                        "width": parseInt(this.gridtable[0][k].model.get("columnWidth"))
                                    });
                                    this.gridtable[0][k].$el.css({
                                        "width": parseInt(this.gridtable[0][k].model.get("columnWidth"))
                                    });
                                } else {
                                    if (this.gridtable[0][k].model.get("type") !== "hidden") {
                                        $(td).css({
                                            "min-width": this.minCellWidth
                                        });
                                        this.gridtable[0][k].$el.css({
                                            "width": this.minCellWidth
                                        });
                                    }
                                }
                                td.className = "pmdynaform-grid-field-static field-operation-result";
                            } else {
                                if (parseInt(this.gridtable[0][k].model.get("columnWidth")) !== 0) {
                                    $(td).css({
                                        "width": this.gridtable[0][k].model.get("columnWidth"),
                                        display: "inline-block"
                                    });
                                } else {
                                    $(td).css({
                                        "width": this.gridtable[0][k].model.get("columnWidth"),
                                        display: "none"
                                    });
                                }
                            }
                        }
                        operation = this.gridtable[0][k].model.attributes.operation;
                        if (operation) {
                            $(td).addClass("total");
                            icon = iconTotal[operation] ? iconTotal[operation] : iconTotal["other"];
                            label.innerHTML = icon + ": ";
                            result.value = title;
                            result.id = (operation + "-" + this.model.get("name") + "-" +
                            this.gridtable[0][k].model.get("columnName"));
                            $(td).addClass("function-result-" + this.gridtable[0][k].model.get("columnName"));
                            td.appendChild(label);
                            td.appendChild(result);
                        } else {
                            label.innerHTML = "";
                            result.value = "";
                        }
                        dom.append(td);
                    }
                    if (this.model.get("layout") == "static") {
                        this.tableBody.on("scroll", function (e) {
                            that.$el.find(".containerStaticGrid")[0].scrollLeft = e.target.scrollLeft;
                        });
                    }
                }
            }
            this.showToolTip();
            return this;
        },
        /**
         * Creates the html row
         * @param numberRow
         * @param dataRow
         * @param sectionAfected
         * @returns {{model: Array, view: Array, data: (*|Array)}}
         */
        createHTMLRow: function (numberRow, dataRow, sectionAfected) {
            var tr = this._createHtmlRow(),
                td,
                k,
                tdRemove,
                tdNumber,
                element,
                colSpan,
                product,
                cellModel,
                nameCell,
                cloneModel,
                idCell,
                cellView,
                row = [],
                rowModel = [],
                rowView = [],
                rowData,
                i,
                that = this,
                nameToPostControl,
                nameToPostLabelControl,
                keyEvent = PMDynaform.core.Utils.generateID();

            if (sectionAfected) {
                this.flagRow = sectionAfected;
            }
            tdNumber = this.createRowNumber(numberRow + 1);
            tr.appendChild(tdNumber);
            for (k = 0; k < this.columnsModel.length; k += 1) {
                cloneModel = jQuery.extend(true, {}, this.columnsModel[k]);
                cellModel = null;
                product = cloneModel.product;
                nameToPostControl = this.createPostVariables(numberRow + 1, cloneModel.name);
                nameToPostLabelControl = this.createPostVariables(numberRow + 1, cloneModel.name, '_label');
                cloneModel["nameToPostControl"] = nameToPostControl;
                cloneModel["nameToPostLabelControl"] = nameToPostLabelControl;
                cloneModel["row"] = numberRow;
                cloneModel["col"] = k;
                cloneModel["keyEvent"] = keyEvent;
                cloneModel["dataForDependent"] = {};
                cellModel = new product.model(cloneModel);
                if (this.model.get("variable").trim().length === 0) {
                    nameCell = "";
                    idCell = this.changeIdField(this.model.get("id"), numberRow + 1, cellModel.get("_extended").name);
                } else {
                    nameCell = this.changeNameField(this.model.get("name"), numberRow + 1, cellModel.get("_extended").name);
                    idCell = this.changeIdField(this.model.get("id"), numberRow + 1, cellModel.get("_extended").name);
                }
                cellModel.attributes.name = nameCell;
                cellModel.attributes.id = idCell;
                rowModel.push(cellModel);
            }
            for (i = 0; i < rowModel.length; i += 1) {
                product = this.columnsModel[i].product;
                cellView = null;
                cellView = new product.view({
                    model: rowModel[i],
                    form: this.form
                });
                cellView.setColumnIndex(i);
                cellView.setOnFieldFocusCallback(function () {
                    that.scrollToColumn(this.columnIndex);
                });
                rowModel[i].set("view", cellView);
                cellView.project = this.project;
                cellView.parent = this;
                colSpan = rowModel[i].attributes.colSpan;
                element = cellView.render().el;
                if (this.model.get("layout") === "responsive") {
                    if ($(element).find(".form-control")[0]) {
                        var elementParent = $(element).find(".form-control")[0].parentNode;
                        elementParent.style.padding = "0px";
                    }
                    td = document.createElement("div");
                    td.className = "grid-cell-responsive";
                    td.style.display = "inline-block";
                } else {
                    td = this._createHtmlCell(rowModel[i].attributes.type, colSpan, i);
                }
                if (this.hiddenColumns.indexOf(i + 1) > -1) {
                    $(td).hide();
                }
                if (cellView.model.get("type") !== "hidden" && this.model.get("layout") === "static") {
                    if (cellView.model.get("columnWidth") && Number(cellView.model.get("columnWidth")).toString() !== "NaN") {
                        $(td).css({
                            "min-width": parseInt(cellView.model.get("columnWidth")),
                            "max-width": parseInt(cellView.model.get("columnWidth"))
                        });
                        cellView.$el.css({
                            "width": parseInt(cellView.model.get("columnWidth"))
                        });
                    } else {
                        $(td).css({
                            "min-width": "200px",
                            "max-width": "200px"
                        });
                    }
                }

                if (this.model.get("layout") === "responsive") {
                    if (parseInt(cellView.model.get("columnWidth")) !== 0) {
                        $(td).css({
                            "width": cellView.model.get("columnWidth")
                        });
                    } else {
                        $(td).css({
                            "width": cellView.model.get("columnWidth"),
                            display: "none"
                        });
                    }
                }

                $(element).addClass("row form-group");
                td.appendChild(element);
                tr.appendChild(td);
                row.push(cellView);
                rowView.push(cellView);
            }
            rowData = this.prepareNewRow(row, dataRow);
            this.updateNameFields(row);
            for (var k = 0; k < row.length; k += 1) {
                if (row[k].model.get("formula")) {
                    row[k].model.attributes.formulaAssociatedObject = [];
                    row[k].onFormula(row);
                }
            }
            if (this.model.get("mode") === "edit") {
                if (this.model.get("deleteRow")) {
                    tdRemove = this.createRemoveButton(numberRow + 1);
                    $(tdRemove).addClass("remove-row");
                    tr.appendChild(tdRemove);
                }
            }
            if (this.model.get("layout") === "responsive") {
                jQuery(tdNumber).css({width: this.indexResponsive});
                jQuery(tdRemove).css({width: this.removeResponsive});
            }
            this.flagRow += 1;
            if (this.paged) {
                this._createHTLMCarucel();
                this.domCarousel.append(tr);
                this.tableBody.append(this.domCarousel);
            } else {
                this.tableBody.append(tr);
            }
            if (!this.deleteButtonVisibility) {
                this.hideButton("delete");
            }
            this.gridtable.push(row);
            this.dom.push(tr);
            return {
                model: rowModel,
                view: rowView,
                data: rowData
            };
        },
        /**
         * Prepare the data for the grid row
         * @param row
         * @param dataRow
         * @returns {Array}
         */
        prepareNewRow: function (row, dataRow) {
            var cellView,
                that = this,
                rowData = [];

            this.setRowData(row, dataRow);
            if (_.isArray(row)) {
                for (var i = 0; i < row.length; i += 1) {
                    cellView = row[i];
                    if (cellView.model.get("operation") !== "") {
                        cellView.on("changeValues", function () {
                            that.setValuesGridFunctions({
                                row: this.model.attributes.row,
                                col: this.model.attributes.col,
                                data: this.model.attributes.value
                            });
                            that.createHTMLTotal();
                        });
                    }
                    if (row[i].model.get("operation")) {
                        if (!isNaN(parseFloat(row[i].model.get("value")))) {
                            rowData.push(parseFloat(row[i].model.get("value")));
                        } else {
                            rowData.push(0);
                        }
                    }
                }
            }
            return rowData;
        },
        /**
         * Sets data in the cells identifying the mode (view, edit and disabled)
         * @param {array|number} row: is a index of array
         * @param {[type]} rowData : is a set of data
         */
        setRowData: function (row, rowData) {
            var i,
                cell,
                type,
                viewMode;
            if (typeof row === "number") {
                row = this.gridtable[row];
            }
            if (_.isArray(row) && _.isArray(rowData)) {
                for (i = 0; i < row.length; i += 1) {
                    cell = row[i];
                    type = cell.model.get("originalType");
                    viewMode = cell.model.get("mode");
                    if (rowData[i] && type !== "multipleFile") {
                        (viewMode === "edit" || viewMode === "disabled")  ?
                            this._setDataToEditMode(cell, rowData[i]) : this._setDataToViewMode(cell, rowData[i]);
                    }
                }
            }
            return this;
        },
        _setDataToViewMode: function (cell, data) {
            if (cell && data !== undefined && data !== null) {
                if (typeof cell.setData === "function") {
                    cell.setData(data);
                }
            }
            return this;
        },
        _setDataToEditMode: function (cell, data) {
            var type,
                dataType,
                fixedData;
            if (cell && data !== undefined && data !== null) {
                // Zanitizing the data because from helper arrives only the value and row works wit value and label
                fixedData = {
                    value: data.value || '',
                    label: data.label || data.value || ''
                };
                type = cell.model.get("type");
                dataType = cell.model.get("dataType") || "";
                if (type === "suggest" && typeof cell.setData === "function") {
                    cell.model.set({"data": fixedData}, {silent: true});
                    cell.model.set({"value": fixedData.value}, {silent: true});
                    cell.model.set("toDraw", true);
                } else if (dataType === "boolean" && typeof cell.setValue === "function") {
                    cell.setValue(fixedData['value'] || false);
                } else {
                    if (typeof cell.setValue === "function") {
                        cell.model.set({"data": fixedData}, {silent: true});
                        cell.model.set({"value": fixedData.value}, {silent: true});
                        cell.model.set("toDraw", true);
                        //TODO: This solution must be changed, because it's a patch.
                        if (type === "link") {
                            cell.setValue(data.value);
                        }
                    }
                }
            }
            return this;
        },
        _createHTLMCarucel: function () {
            if (this.block === true) {
                this.domCarousel = document.createElement("div");
                this.domCarousel.className = "pmdynaform-grid-section_" + this.section;
                if (this.model.get("layout") === "static") {
                    this.domCarousel.className += " pmdynaform-static";
                }
                this.domCarousel = $(this.domCarousel);
            }
            if (this.section === this.showPage) {
                this.domCarousel.addClass("item active");
            } else {
                this.domCarousel.addClass("item");
            }
            if (this.flagRow == this.pageSize) {
                this.block = true;
                this.section += 1;
                this.flagRow = 0;
            } else {
                this.block = false;
            }
            return this;
        },
        _createHtmlRow: function () {
            var tr;
            tr = document.createElement("div");
            tr.className = "pmdynaform-grid-row row form-group show-grid";
            if (this.model.get("layout") === "static") {
                tr.className += " pmdynaform-grid-static"
            }
            return tr;
        },
        _createHtmlCell: function (typeControl, colSpan, index) {
            var td, colSpan;
            td = document.createElement("div");
            if (this.model.attributes.layout === "form") {
                if (typeControl !== "hidden") {
                    this.checkColSpanResponsive();
                    colSpan = this.colSpanControlField(typeControl, index);
                    td.className = "col-xs-12 col-sm-" + colSpan + " col-md-" + colSpan + " col-lg-" + colSpan;
                } else {
                    jQuery(td).css({
                        width: 0 + "%",
                        display: "inline-block"
                    });
                }
            } else if (this.model.attributes.layout === "static") {
                if (typeControl !== "hidden") {
                    td.className = "pmdynaform-grid-field-static";
                }
            } else {
                if (typeControl !== "hidden") {
                    td.className = "col-xs-" + colSpan + " col-sm-" + colSpan + " col-md-" + colSpan + " col-lg-" + colSpan;
                    jQuery(td).css({
                        width: this.colSpanControlFieldResponsive() + "%",
                        display: "inline-block"
                    });
                } else {
                    jQuery(td).css({
                        width: 0 + "%",
                        display: "inline-block"
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
                if (cloneData.length) {
                    for (j in cloneData) {
                        if (cloneData.hasOwnProperty(j)) {
                            for (col = 0; col < grid[0].length; col += 1) {
                                if (!_.isEmpty(grid[0][col].model.attributes.variable)) {
                                    if (grid[0][col].model.attributes.variable.var_name === j) {
                                        if (cloneData[j] instanceof Array) {
                                            for (i = 0; i < grid.length; i += 1) {

                                                if (!this.gridtable[i][col].model.get("formulator")) {
                                                    grid[i][col].model.set("value", cloneData[j][i]);
                                                    if (this.gridtable[i][col].onFieldAssociatedHandler) {
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
            for (i = 0; i < gridpanel.length; i += 1) {
                rowData = [];
                for (k = 0; k < gridpanel[i].length; k += 1) {
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
            var i, j, k, rows, rowsData, rowData, row;
            this.tableBody = this.$el.find(".pmdynaform-grid-tbody");
            rows = this.model.get("rows");
            rowsData = this.model.get("data");
            if (this.model.get("layout") === "static") {
                this.tableBody.addClass("pmdynaform-static");
            }
            this.model.attributes.gridFunctions = [];
            if (!newItem) {
                this.dom = [];
                for (j = 0; j < rows; j += 1) {
                    rowData = rowsData[j + 1];
                    row = this.createHTMLRow(j, rowData);
                    if (row && row.data) {
                        this.model.attributes.gridFunctions.push(row.data);
                        for (i = 0; i < row.model.length; i += 1) {
                            if (row.model[i].get("operation") && row.model[i].get("operation").trim().length) {
                                row.view[i].onChangeCallbackOperation();
                            }
                            if (row.model[i].get("type") == "label" && row.model[i].get("operation")) {
                                this.createHTMLTotal();
                            }
                        }
                    }
                }
            } else {
                row = this.createHTMLRow(this.gridtable.length - 1);
                if (row && row.data) {
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
                beforeAdd: "setOnBeforeAddCallback"
            };

            if (allowEvents[e]) {
                this[allowEvents[e]](fn);
            } else {
                throw new Error("The event must be a valid event.\n The events available are remove, add and pager");
            }

            return this;
        },
        setOnDeleteRowCallback: function (fn) {
            if (typeof fn === "function") {
                this.onDeleteRowCallback = fn;
            } else {
                throw new Error("The callback must be a function");
            }
            return this;
        },
        setOnAddRowCallback: function (fn) {
            if (typeof fn === "function") {
                this.onAddRowCallback = fn;
            } else {
                throw new Error("The callback must be a function");
            }
            return this;
        },
        setOnBeforeAddCallback: function (fn) {
            if (typeof fn === "function") {
                this.onBeforeAddRowCallback = fn;
            } else {
                throw new Error("The callback must be a function");
            }
            return this;
        },
        setOnClickPageCallback: function (fn) {
            if (typeof fn === "function") {
                this.onClickPageCallback = fn;
            } else {
                throw new Error("The callback must be a function");
            }

            return this;
        },
        afterRender: function () {
            this.showToolTip();
        },
        /**
         * this method get data in json formated, of this field.
         * The suports controls are:
         * - text
         * - textarea
         * - dropdown
         * - hidden
         * - checkbox
         * - suggest
         * - datetime
         * @return {object} json
         */
        getData2: function () {
            var validControls,
                gridpanel,
                cellName,
                rowData,
                cell,
                data = {},
                key,
                k,
                i;
            validControls = ["text", "textarea", "dropdown", "hidden", "checkbox", "datetime", "suggest", "multipleFile"];
            gridpanel = this.gridtable;
            for (i = 0; i < gridpanel.length; i += 1) {
                data[i + 1] = {};
                rowData = {};
                for (k = 0; k < gridpanel[i].length; k += 1) {
                    cell = gridpanel[i][k].model;
                    if (validControls.indexOf(cell.get("originalType")) > -1) {
                        cellName = cell.get("columnName");
                        rowData[cellName] = cell.get("data")["value"];
                        rowData[cellName + "_label"] = cell.get("data")["label"];
                    }
                }
                data[i + 1] = rowData;
            }
            return data;
        },
        setData2: function (data) {
            var rowIndex, grid, dataRow,
                colIndexm, cols, colIndex,
                cellModelItem, cellViewItem,
                modeItem, dataItem, newItem, value, richi, option, options, i;
            grid = this.gridtable;
            for (rowIndex in data) {
                if (parseInt(rowIndex, 10) > this.gridtable.length) {
                    newItem = this.addRow();
                    this.renderGridTable();
                    this.onAddRowCallback(newItem, this);
                }
                cols = grid[parseInt(rowIndex, 10) - 1].length;
                for (colIndex = 0; colIndex < cols; colIndex += 1) {
                    cellViewItem = grid[parseInt(rowIndex, 10) - 1][colIndex];
                    cellModelItem = grid[parseInt(rowIndex, 10) - 1][colIndex].model;
                    modeItem = cellModelItem.get("mode");
                    for (dataItem in data[rowIndex]) {
                        if (cellModelItem.get("columnName") === dataItem) {
                            if (modeItem === "edit" || modeItem === "disabled") {
                                if (cellModelItem.get("type") === "suggest") {

                                    for (richi = 0; richi < cellModelItem.get("localOptions").length; richi += 1) {
                                        option = cellModelItem.get("localOptions")[richi].value;
                                        if (option === data[rowIndex][dataItem]) {
                                            value = cellModelItem.get("localOptions")[richi].label;
                                            break;
                                        }
                                    }
                                    if (value && !value.length) {
                                        for (richi = 0; richi < cellModelItem.get("options").length; richi += 1) {
                                            option = cellModelItem.get("options")[richi].value;
                                            if (option === data[rowIndex][dataItem]) {
                                                value = cellModelItem.get("options")[richi].label;
                                                break;
                                            }
                                        }
                                    }

                                    $(cellViewItem.el).find(":input").val(value);
                                    cellModelItem.attributes.value = data[rowIndex][dataItem];
                                } else if (cellModelItem.get("type") === "checkbox") {
                                    options = cellModelItem.get("options");
                                    if (cellModelItem.get("dataType") === "boolean") {
                                        if (data[cellModelItem.get("name")] === options[0].value) {
                                            options[1].selected = false;
                                            options[0].selected = true;
                                        } else {
                                            delete options[0].selected;
                                            options[1].selected = true;
                                            options[0].selected = false;
                                        }
                                    } else {
                                        for (i = 0; i < options.length; i += 1) {
                                            delete options[i].selected;
                                            if (data[rowIndex][dataItem].indexOf(options[i]) > -1) {
                                                options[i].selected = true;
                                            }
                                        }
                                    }
                                    cellModelItem.set("options", options);
                                    cellModelItem.initControl();
                                    cellModelItem.set("value", [data[rowIndex][dataItem]])
                                } else {
                                    cellModelItem.set("value", data[rowIndex][dataItem]);
                                }
                            }
                            if (modeItem === "view") {
                                if (cellModelItem.get("originalType") === "checkbox") {
                                    cellModelItem.set("fullOptions", data[rowIndex][dataItem]);
                                } else if (cellModelItem.get("originalType") === "dropdown") {
                                    value = [];
                                    for (richi = 0; richi < cellModelItem.get("localOptions").length; richi += 1) {
                                        option = cellModelItem.get("localOptions")[richi].value;
                                        if (option === data[rowIndex][dataItem]) {
                                            value.push(cellModelItem.get("localOptions")[richi].label);
                                            cellModelItem.set("fullOptions", value);
                                            break;
                                        }
                                    }
                                    if (!value.length) {
                                        for (richi = 0; richi < cellModelItem.get("options").length; richi += 1) {
                                            option = cellModelItem.get("options")[richi].value;
                                            if (option === data[rowIndex][dataItem]) {
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
        render: function () {
            var that = this,
                bodyGrid;

            this.$el.html(this.template(this.model.toJSON()));
            this.createHTMLTitle();
            this.renderGridTable(false);
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }

            this._$gridHeader = this.$el.find(".pmdynaform-grid-thead");
            this._gridHeader = this._$gridHeader.get(0);

            if (this.model.get("layout") === "static") {
                bodyGrid = this.$el.find(".pmdynaform-grid-tbody");
                bodyGrid.css("overflow", "auto");
                bodyGrid.scroll(function (event) {
                    that._$gridHeader.scrollLeft(bodyGrid.scrollLeft());
                    event.stopPropagation();
                });
            }
            if (!this.model.get("addRow")) {
                this.$el.find(".pmdynaform-grid-new").find("button").hide();
            }
            if (this.model.get("layout") === "responsive") {
                var size = {
                    "1200": 5,
                    "992": 4,
                    "768": 3,
                    "767": 2
                };

                $(window).resize(function () {
                    var j,
                        k,
                        width = $(window).width();

                    if (width >= 1200) {
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

            this.attachListeners();
            if (this._hidden) {
                this.hide();
            } else {
                this.show();
            }
            return this;
        },
        scrollToColumn: function (columnIndex) {
            // We add 1 because the columnIndezx is ignoring the column for row number.
            var gridBody = this.$el.find('.pmdynaform-grid-tbody').get(0),
                gridBodyBR = gridBody.getBoundingClientRect(),
                columnHeader,
                headerIndex = 0;
            if (this.model.get("layout") === "static") {
                columnHeader = this.$el.find('.pmdynaform-grid-thead')
                    .find('.pmdynaform-grid-field-static').eq(columnIndex + 1);
                if (columnHeader.get(headerIndex)) {
                    columnHeader = columnHeader.get(headerIndex).getBoundingClientRect();
                    if (columnHeader.left < gridBodyBR.left) {
                        gridBody.scrollLeft -= gridBodyBR.left - columnHeader.left;
                    }
                }
            }
        },
        setValue: function (value, row, col) {
            if (value !== undefined) {
                if (row !== undefined && col !== undefined) {
                    if ((row > 0 && col > 0) && row <= this.gridtable.length && col <= this.columnsModel.length) {
                        return this.gridtable[row - 1][col - 1].setValue(value);
                    } else {
                        return null;
                    }
                }
            }
            return this;
        },
        getValue: function (row, col) {
            return this.model.getValue(row, col);
        },
        /**
         * Create grid variables to send by AJAX to the server side
         * using the native form action to send via POST.
         * @param rowIndex index number
         * @param columnName column number
         * @param suffix when the field has _label suffix
         * @returns {string}
         */
        createPostVariables: function (rowIndex, columnName, suffix) {
            var index = this.model.get("variable") || this.model.get("id"),
                suffix =  suffix || '' ;
            return "form[" + index + "][" + rowIndex + "][" + columnName + suffix + "]";
        },

        getColumnHeader: function (index) {
            var cols;
            if (this._gridHeader) {
                cols = this.model.getHiddensBeforeColumn(index - 1);
                return this._$gridHeader.find('> *').eq(index - cols.length);
            }
            return null;
        },
        hideColumn: function (col) {
            var field = this,
                table,
                row,
                cell,
                i,
                label;
            table = field.gridtable;
            if (col > 0 && col <= field.columnsModel.length) {
                if (field.hiddenColumns.indexOf(col) === -1) {
                    field.hiddenColumns.push(col);
                }
                for (i = 0; i < table.length; i += 1) {
                    row = table[i];
                    cell = row[col - 1];
                    if (cell.model.get("type") !== "hidden") {
                        cell.$el.hide();
                        if (cell.$el.parent().length) {
                            cell.$el.parent().hide();
                        }
                        if (cell.model.get("operation") !== "") {
                            field.$el.find("." + "function-result-" + cell.model.get("columnName")).hide();
                        }
                    }
                }
                if (field.$el.find(".field-operation-result") && field.columnsModel[col - 1].type !== "hidden") {
                    field.$el.find(".field-operation-result").eq(col - 1).hide();
                    this.getColumnHeader(col).hide();
                }
            }
        },
        showColumn: function (col) {
            var field = this,
                table,
                row,
                cell,
                label,
                i,
                index;
            table = field.gridtable;
            if (col > 0 && col <= field.columnsModel.length) {
                index = field.hiddenColumns.indexOf(col);
                if (index > -1) {
                    field.hiddenColumns.splice(index, 1);
                }
                for (i = 0; i < table.length; i += 1) {
                    row = table[i];
                    cell = row[col - 1];
                    if (cell.model.get("type") !== "hidden") {
                        cell.$el.show();
                        if (cell.$el.parent().length) {
                            cell.$el.parent().show();
                        }
                    }
                }
                if (cell) {
                    if (cell.model.get("operation") !== "") {
                        field.$el.find("." + "function-result-" + cell.model.get("columnName")).show();
                    }
                    if (field.$el.find(".field-operation-result") && field.columnsModel[col - 1].type !== "hidden") {
                        field.$el.find(".field-operation-result").eq(col - 1).show();
                        this.getColumnHeader(col).show();
                    }
                }
            }
        },
        /**
         * Hide Buttons "NewRow" or "deleteRow"
         * @param buttonText
         * @returns {GridView}
         */
        hideButton: function (buttonText) {
            var itemNewButton = this.$el.find(".pmdynaform-grid-newitem"),
                itemDeleteButton = this.$el.find("button.glyphicon.glyphicon-trash");
            switch (buttonText) {
                case "add":
                    if (this.model.get("addRow") && itemNewButton.is(":visible")) {
                        itemNewButton.hide();
                    }
                    break;
                case "delete":
                    if (this.model.get("deleteRow") && itemDeleteButton.is(":visible")) {
                        this.deleteButtonVisibility = false;
                        itemDeleteButton.hide();
                    }
                    break;
            }
            return this;
        },
        /**
         * Show Buttons "NewRow" or "deleteRow"
         * @param buttonText
         * @returns {GridView}
         */
        showButton: function (buttonText) {
            var itemNewButton = this.$el.find(".pmdynaform-grid-newitem"),
                itemDeleteButton = this.$el.find("button.glyphicon.glyphicon-trash");
            switch (buttonText) {
                case "add":
                    if (this.model.get("addRow") && !itemNewButton.is(":visible")) {
                        itemNewButton.show();
                    }
                    break;
                case "delete":
                    if (this.model.get("deleteRow") && !itemDeleteButton.is(":visible")) {
                        this.deleteButtonVisibility = true;
                        itemDeleteButton.show();
                    }
                    break;
            }
            return this;
        },
        getNumberRows: function () {
            return this.gridtable.length;
        },
        /**
         * this method  execute, when the grid undergoes a change that requires validation check
         * @returns {boolean}
         */
        validateGrid: function () {
            var valid = true;
            if (this.validator) {
                this.validator.$el.remove();
            }
            valid = this.model.isValid();
            if (!valid) {
                this.validator = new PMDynaform.view.Validator({
                    model: this.model.get("validator")
                });
                this.$el.find(".pmdynaform-grid").parent().append(this.validator.el);
                this.$el.find(".pmdynaform-grid").addClass("has-error");
            } else {
                this.$el.find(".pmdynaform-grid").removeClass("has-error");
            }
            if (this.model.get("rows") === 0) {
                this.renderEmptyGrid();
            } else {
                this.removeEmptyGrid();
            }
            return valid;
        },
        /**
         * this template is append when the rows not exist in the grid
         * @returns {GridView}
         */
        renderEmptyGrid: function () {
            var emptyTag,
                container = this.$el.find("#" + this.model.get("id") + "-body");
            emptyTag = this.$el.find(".grid-empty");
            if (emptyTag instanceof jQuery && emptyTag.length === 0) {
                emptyTag = this.templateEmptyGrid({
                    message: this.model.get("emptyMessage")
                });
                container.prepend(emptyTag);
            }
            return this;
        },
        /**
         * this template is removed when the rows exist in the grid
         * @returns {GridView}
         */
        removeEmptyGrid: function () {
            this.$el.find(".grid-empty").remove();
            return this;
        },
        attachListeners: function () {
            var grid = this.$el.find('.pmdynaform-grid-tbody'),
                that = this;

            if (grid.length) {
                grid.on('scroll', function () {
                    if (that.project) {
                        that.project.hideCalendars();
                    }
                });
            }
        },
        getColumnToId: function (id) {
            var column, colIndex, cell = {};
            column = this.columnsModel.find(function (column, index) {
                if (column.columnId === id) {
                    colIndex = index + 1;
                    return column;
                }
            });
            if (colIndex) {
                cell = {
                    colIndex: colIndex,
                    column: column
                }
            }
            return cell;
        },
        getSummary: function (col) {
            var result = null, column, colIndex;
            column = parseInt(col);
            if (_.isNaN(column)) {
                column = this.getColumnToId(col);
                colIndex = column['colIndex'] ? column['colIndex'] : -1;
            } else {
                colIndex = column;
            }
            if (colIndex > 0 && colIndex <= this.columnsModel.length) {
                column = this.columnsModel[colIndex - 1];
                if (column && column.operation) {
                    result = this.model.get("totalrow")[colIndex - 1];
                }
            }
            return !!result ? result : 0;
        },
        validateRowCol: function (row, col) {
            var rowAux = Math.floor(row),
                colAux = Math.floor(col),
                sw = false;

            if (!_.isNaN(rowAux) && !_.isNaN(col)) {
                if (rowAux > 0 && colAux > 0 && rowAux <= this.gridtable.length && colAux <= this.columnsModel.length) {
                    sw = true;
                }
            }
            return sw;
        },
        getItemGrid: function (row, col) {
            var itemGrid = null;
            if (this.validateRowCol(row, col)) {
                itemGrid = this.gridtable[row - 1][col - 1];
            }
            return itemGrid;
        },
        getText: function (row, col) {
            var itemGrid = this.getItemGrid(row, col);
            return itemGrid ? itemGrid.getText() : null;
        },
        /**
         * Get Control HTML from Field
         * @param row
         * @param col
         * @returns {Array}
         */
        getControl: function (row, col) {
            var htmlControl = [];
            if (row && col) {
                if (this.model.isRowInRange(row) && this.model.isColumnInRange(col)) {
                    htmlControl = this.gridtable[row - 1][col - 1].getControl();
                }
            }
            return htmlControl;
        },
        /**
         * Create Tooltip and show in the event hover
         * and hide in the event blur or make scrolling in Body of the Datatable
         */
        showToolTip: function () {
            var dataTableResult = $(".pmdynaform-grid-functions").find("input"),
                toolTipVar = new PMDynaform.view.ToolTipView();

            if (dataTableResult) {
                dataTableResult.hover(function (e) {
                    toolTipVar.show($(this), $(this).val(), "bottom");
                    e.stopPropagation();
                    e.preventDefault();
                });

                dataTableResult.mouseout(function (e) {
                    toolTipVar.hide($(this));
                    e.stopPropagation();
                    e.preventDefault();
                });
            }
        },
        /**
         * Set the columnId to know the column delete file
         * @returns {number}
         */
        getColumnFileDelete: function (columnId, nameRow) {
            return this.model.getColumnFileDelete(columnId, nameRow);
        },
        /**
         * Clear Content File Grid
         * @param row
         * @param col
         * @returns {GridView}
         */
        clearContent: function (row, col) {
            var viewFile;
            if (row && col) {
                if (this.model.isRowInRange(row) && this.model.isColumnInRange(col)) {
                    viewFile = this.gridtable[row - 1][col - 1];
                    if (viewFile && viewFile.model.get("type") === "file") {
                        viewFile.clearContent();
                    }
                }
            }
            return this;
        },
        /**
         * Delete a files in a row of grid
         * @param row
         * @returns {GridView}
         */
        deleteFilesByRow: function (row) {
            var index;
            for (index = 0; index < row.length; index += 1) {
                if (row[index].model.get("originalType") === "multipleFile") {
                    row[index].model.deleteFiles();
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.GridPanel", GridView);
}());

(function () {
    var ButtonView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-button").html()),
        events: {
            "keydown": "preventEvents"
        },
        tagControl: null,
        tagLabel: null,
        initialize: function () {},
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
            if (this.tagControl.length) {
                this.tagControl.on(e, function (event) {
                    fn(event, that);
                    event.stopPropagation();
                });
            }
            return this;
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.tagControl = this.$el.find("button");
            this.tagLabel = this.$el.find("button span");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setValue: function (text) {
            if (text) {
                this.model.set("label", text);
                this.tagLabel.text(text);
            }
            return this;
        },
        getText: function () {
            var label = this.model.get("label");
            return label ? label : null;
        },
        getValue: function () {
            return this.model.getValue();
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Button", ButtonView);
}());
(function () {
    var DropDownView = PMDynaform.view.Field.extend({
        events: {
            "change select": "eventListener",
            "keydown select": "preventEvents",
            "focus select": "runDependetOptions"
        },
        clicked: false,
        jsonData: {},
        firstLoad: true,
        dirty: false,
        previousValue: "",
        triggerCallback: false,
        template: _.template($("#tpl-dropdown").html()),
        templateOptions: _.template($("#tpl-dropdown-options").html()),
        existHTML: false,
        /**
         * runDependetOptions(): when this component is dependent the other field, the options
         * are set for set full remote options
         * @return {Dropdown}
         */
        runDependetOptions: function () {
            var value,
                parent = this.parent,
                item,
                data = {},
                dependency,
                i,
                postRender = true;
            if (_.isArray(this.model.get("dependency")) && this.model.get("dependency").length) {
                if (this.firstLoad) {
                    if (!this.dirty && parent) {
                        this.tagControl.empty();
                        value = this.model.get("value") || "";
                        dependency = this.model.get("dependency");
                        for (i = 0; i < dependency.length; i += 1) {
                            if (parent.model.get("type") === "grid") {
                                item = parent.model.findCellInRow(this.model.get("row"), dependency[i]);
                            } else {
                                item = parent.model.get("fields")[dependency[i]];
                            }
                            if (item) {
                                data[dependency[i]] = item.get("value");
                            }
                        }
                        this.model.recoreryRemoteOptions(data, postRender);
                        this.model.trigger("change:options");
                        this.tagControl.val(value);
                        this.dirty = true;
                    }
                    this.firstLoad = false;
                }
            } else {
                this.firstLoad = false;
            }
            return this;
        },
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.previousValue = this.getValue();
            this.formulaFieldsAssociated = [];
            this.model.on("change:value", this.eventListener, this, {change: true});
            this.model.on("change:options", this.redrawOptions, this);
            this.model.on("change:toDraw", this.render, this);
            this.model.on("change:data", this.onChangeData, this);
        },
        /**
         * removePlaceholder(), this method remove placeholder option,
         * when is change option.
         * @return {object}
         */
        removePlaceholder: function () {
            this.tagControl.find("#placeholder-option").remove();
            return this;
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn {function}
         * @returns {DropDownView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Listens to change event
         * @param event
         * @param value
         */
        eventListener: function (event, value) {
            this.onChange(event, value);
            this.checkBinding();
        },
        /**
         * Executes onChangeCallback function
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
            // For execute formulas in the fields associated
            this.onFieldAssociatedHandler();
        },
        preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        setValueDefault: function () {
            var val = $(this.el).find(":selected").val();
            if (val != undefined && val != null) {
                this.model.set("value", val);
            } else {
                this.model.set("value", "");
            }
        },
        updateValues: function (event, value) {
            var hiddenInput,
                label,
                newValue,
                option;
            if (!event.type) {
                this.tagControl.val(value);
            }
            option = this.tagControl.find(":selected");
            if (this.tagControl) {
                this.tagControl.children().removeAttr("selected");
                option.prop("selected", true);
                option.attr("selected", "selected");
                label = option.text().trim();
                hiddenInput = this.$el.find("input[type='hidden']");
                hiddenInput.val(label);
                newValue = this.tagControl.val();
                this.model.set("keyLabel", label);
                this.model.set({"value": newValue}, {silent: true});
                this.updateDataModel(newValue, label);
            }
            return this;
        },
        updateDataModel: function (value, label) {
            var data;
            data = {
                value: value,
                label: label
            };
            this.model.set("data", data);
            this.$el.find(".content-print").text(data.label);
            return this;
        },
        /**
         * Updates data and value
         * @param event
         * @param value
         * @returns {DropDownView}
         */
        onChange: function (event, value) {
            if (this.model.get("therePlaceholder")) {
                this.removePlaceholder();
            }
            this.updateValues(event, value);
            this.validate();
            this.clicked = false;
            return this;
        },
        validate: function () {
            var drpValue;
            drpValue = this.$el.find("select").val() || "";
            this.model.set({value: drpValue}, {validate: true});
            if (this.model.get("enableValidate") && !this.firstLoad) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator"),
                        domain: false
                    });
                    this.$el.find("select").parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        updateValueControl: function () {
            var i,
                options = this.$el.find("select").find("option");
            loop:
                for (i = 0; i < options.length; i += 1) {
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
                control.on(e, function (event) {
                    fn(event, that);
                    event.stopPropagation();
                });
            }
            return this;
        },
        getHTMLControl: function () {
            return this.$el.find("select");
        },
        render: function () {
            var hidden,
                name;
            this.existHTML = true;
            this.$el.html(this.template(this.model.toJSON()));
            this._setDataOption();
            this.$el.find("input[type='hidden']").val(this.model.get("data")["label"]);
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            }
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            this.setValueToDomain();
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("select").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.tagControl = this.$el.find("select");
            this.tagControl.val(this.model.get("value") || "");
            this.tagControl.val(this.model.get("value").toString() || "");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        mergeOptions: function (remoteOptions, returnOptions) {
            var k, remoteOpt = [],
                localOpt,
                options;
            for (k = 0; k < remoteOptions.length; k += 1) {
                remoteOpt.push({
                    value: remoteOptions[k].value,
                    label: remoteOptions[k].text || remoteOptions[k].label || ""
                });
            }
            localOpt = this.model.get("localOptions");
            this.model.attributes.remoteOptions = remoteOpt;
            this.model.attributes.optionsSql = remoteOpt;
            options = localOpt.concat(remoteOpt);
            this.model.attributes.options = options;
            this._setOptions(this.model.get("options"));
            if (returnOptions) {
                return options;
            }
            if (options.length) {
                this.model.attributes.data = {
                    value: options[0].value,
                    label: options[0].label
                };
                if (this.model.get("validator")) {
                    this.model.get("validator").set("valid", true);
                }
                this.model.set("value", options[0].value);
            } else {
                this.model.set("value", "");
            }
            return options;
        },
        _setOptions: function (options) {
            var htmlOptions,
                placeholderOption = {},
                selectControl = this.$el.find("select"),
                therePlaceholder = this.model.get("therePlaceholder");
            if (therePlaceholder) {
                placeholderOption = this.model.get("placeholderOption");
            }
            selectControl.empty();
            htmlOptions = this.templateOptions({
                options: options,
                therePlaceholder: therePlaceholder,
                placeholderOption: placeholderOption
            });
            selectControl.append(htmlOptions);
            return this;
        },
        _setDataOption: function () {
            var data = this.model.get("data");
            if (this.model.get("dependency").length) {
                if (data && data["value"]) {
                    this._setOptions([data]);
                }
            }
            return this;
        },
        /**
         * Sets value and updates previousValue
         * @param value
         * @returns {DropDownView}
         */
        setValue: function (value) {
            var currentValue = this.model.get("value");
            if (value !== undefined) {
                this.previousValue = this.getValue();
                if (value === currentValue && this.firstLoad) {
                    this.model.trigger("change:value", this.model, value);
                } else {
                    this.model.set("value", value);
                }
                this.firstLoad = false;
            }
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        getControl: function () {
            var htmlControl = this.$el.find("select");
            return htmlControl;
        },
        /**
         * redrawOptions, Draw component options
         * @chainable
         */
        redrawOptions: function () {
            if (this.existHTML) {
                this.firstLoad = false;
                this._setOptions(this.model.get("options"));
                this.tagHiddenToLabel.val(this.model.get("data")["label"]);
            }
            return this;
        },
        /**
         * onChangeData, set label of the data in the hidden tag
         * @chainable
         */
        onChangeData: function () {
            var data = this.model.get("data"),
                hiddenInput = this.tagHiddenToLabel;
            if (data.hasOwnProperty("label")) {
                hiddenInput.val(data.label);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Dropdown", DropDownView);

}());

(function () {
    var RadioView = PMDynaform.view.Field.extend({
        clicked: false,
        previousValue: null,
        template: _.template($("#tpl-radio").html()),
        templateOptions: _.template($("#tpl-radio-options").html()),
        existHTML: false,
        events: {
            "change input": "eventListener",
            "keydown input": "preventEvents"
        },
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.previousValue = this.getValue();
            // this property have a control of formulas field
            this.formulaFieldsAssociated = [];
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:options", this.redrawOptions, this);
            this.model.on("change:toDraw", this.render, this);
        },
        preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn
         * @returns {RadioView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Listens to change event
         * @param event
         * @param value
         */
        eventListener: function (event, value) {
            this.onChange(event, value);
            this.checkBinding(event);
        },
        /**
         * Executes onChangeCallBack function
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            // For execute the formula field associated
            this.onFieldAssociatedHandler();
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        render: function () {
            this.existHTML = true;
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            this.setValueToDomain();
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='radio']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.getText());
            this.tagControl = this.$el.find(".pmdynaform-radio-items");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        validate: function () {
            this.model.set({}, {validate: true});
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.$el.find(".pmdynaform-control-radio-list").parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        /**
         * Updates data and value
         * @param event
         * @param value
         */
        onChange: function (event, value) {
            var option;
            this.clicked = true;
            if (value !== undefined && value !== null) {
                option = this.model.findOption(value, "value");
                this.updateDom(option);
            } else {
                option = $(event.target);
                this.updateModel(option);
            }
            if (option) {
                this.validate();
                //find and execute dependent fields
                this.clicked = false;
            }
        },
        updateDom: function (option) {
            var selectedHTMLOption;

            if (option !== null) {
                selectedHTMLOption = this.tagControl.find('input[type=radio][value="' + option.value + '"]');
            }
            if (selectedHTMLOption && selectedHTMLOption.length) {
                selectedHTMLOption[0].checked = true;
                this.updateValueHiddenControl(option.label);
            } else {
                this.tagControl.find('input[type=radio]').attr('checked', false);
            }
            return this;
        },
        /**
         * Updates data and value of the model
         * @param tagOption
         * @returns {RadioView}
         */
        updateModel: function (tagOption) {
            var option,
                value = tagOption.val();
            option = this.model.findOption(value, "value");
            if (option && option.value) {
                this.model.set("data", {
                    value: option.value,
                    label: option.label
                });
                this.$el.find(".content-print").text(option.label);
                this.model.attributes.value = option.value;
                this.model.attributes.keyLabel = option.label;
                this.updateValueHiddenControl(option.label);
            }
            return this;
        },
        getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-radio-list");
        },
        /**
         * Sets value and updates previous value
         * @param value
         * @returns {RadioView}
         */
        setValue: function (value) {
            var dataSelected = this.model.findOption(value, "value") || {value: "", label: ""};
            this.previousValue = this.getValue();
            this.model.set("data", {
                value: dataSelected.value,
                label: dataSelected.label
            });
            this.model.set("value", value);
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        renderOptions: function () {
            var htmlOptions,
                contendControl,
                config;
            contendControl = this.$el.find(".pmdynaform-radio-items");
            contendControl.empty();

            if (_.isArray(this.model.get("options"))) {
                config = {
                    name: this.model.get("name"),
                    id: this.model.get("id"),
                    type: this.model.get('type'),
                    namespace: this.model.get("namespace"),
                    disabled: this.model.get("disabled"),
                    options: this.model.get("options")
                };
                htmlOptions = this.templateOptions(config);
                contendControl.append(htmlOptions);
            }
            return this;
        },
        getControl: function () {
            return this.$el.find("input[type='radio']");
        },
        /**
         * redrawOptions, Draw component options
         * @chainable
         */
        redrawOptions: function () {
            var data = this.model.get("data");
            if (this.existHTML) {
                this.renderOptions();
                this.updateValueHiddenControl(data["label"] || "");
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Radio", RadioView);
}());

(function () {
    var SubmitView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-submit").html()),
        events: {
            "keydown": "preventEvents"
        },
        tagControl: null,
        tagLabel: null,
        initialize: function () {},
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
            if (this.tagControl.length) {
                this.tagControl.on(e, function (event) {
                    fn(event, that);
                    event.stopPropagation();
                });
            }
            return this;
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.tagControl = this.$el.find("button");
            this.tagLabel = this.$el.find("button span");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setValue: function (text) {
            if (text) {
                this.model.set("label", text);
                this.tagLabel.text(text);
            }
            return this;
        },
        getText: function () {
            var label = this.model.get("label");
            return label? label : null;
        },
        getValue: function () {
            return this.model.getValue();
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Submit", SubmitView);
}());
(function () {
    var TextareaView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-textarea").html()),
        validator: null,
        keyPressed: false,
        previousValue: "",
        events: {
            "change textarea": "eventListener",
            "keydown textarea": "refreshBinding"
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn {function}
         * @returns {TextareaView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Initializes Textarea properties
         * @param options
         */
        initialize: function (options) {
            // this property have a control of formulas field
            this.form = options.form ? options.form : null;
            this.formulaFieldsAssociated = [];
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:toDraw", this.render, this);
        },
        refreshBinding: function () {
            this.keyPressed = true;
            return this;
        },
        /**
         * Listens to event change
         * @param event
         * @returns {TextareaView}
         */
        eventListener: function (event) {
            this.onChange(event);
            this.checkBinding();
            return this;
        },
        /**
         * Executes onChangeCallback function
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            this.onFieldAssociatedHandler();
            if (this.model.get("operation")) {
                this.onChangeCallbackOperation(this);
            }
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        /**
         * Updates the values in the inputs controls nodes
         * @returns {TextareaView}
         */
        updateValueInput: function () {
            var textInput, hiddenInput;
            textInput = this.$el.find("textarea");
            hiddenInput = this.$el.find("input[type='hidden']");
            if (this.model.get("data")) {
                textInput.val(this.model.get("data")["label"]);
                hiddenInput.val(this.model.get("data")["label"]);
            }
            return this;
        },
        /**
         * Updates data and value
         * @param event
         * @returns {TextareaView}
         */
        onChange: function (event) {
            if (event.type !== "change") {
                this.updateValueInput();
            }
            if (event.type === "change") {
                this.updateDataModel();
                this.updateValueInput();
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.validate(event);
            this.clicked = false;
            return this;
        },
        /**
         * Updates model's data
         * @returns {TextareaView}
         */
        updateDataModel: function () {
            var data;
            data = {
                value: this.tagControl.val(),
                label: this.tagControl.val()
            };
            this.model.set("data", data);
            return this;
        },
        render: function () {
            var hidden, name;
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            }
            this.previousValue = this.model.get("value");

            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='textarea']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.tagControl = this.$el.find("textarea");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        validate: function (event) {
            var originalValue;
            originalValue = this.tagControl.val();
            this.model.set("validate", true);
            this.model.attributes.value = originalValue;
            this.model.validate();
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.tagControl.parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            this.changeValuesFieldsRelated();
            this.keyPressed = false;
            // For execute the formula field associated
            this.onFieldAssociatedHandler();
            return this;
        },
        on: function (e, fn) {
            var that = this,
                control = this.$el.find("textarea");

            if (control) {
                control.on(e, function (event) {
                    fn(event, that);

                    event.stopPropagation();
                });
            }

            return this;
        },
        /**
         * Gets HTML textarea of the control
         */
        getHTMLControl: function () {
            return this.$el.find("textarea");
        },
        mergeOptions: function (remoteOptions, click) {
            var k, item;
            if (_.isArray(remoteOptions) && remoteOptions.length) {
                item = remoteOptions[0];
                if (item.hasOwnProperty("value")) {
                    this.model.attributes.data = {
                        value: item["value"],
                        label: item["value"]
                    };
                    this.model.set("value", item["value"]);
                } else {
                    this.model.set("value", "");
                    this.model.attributes.data = {value: "", label: ""};
                }
            } else {
                this.model.attributes.data = {value: "", label: ""};
                this.model.set("value", "");
            }
            return this;
        },
        /**
         * Calls setValue of the TextareaModel
         * @param value
         * @returns {TextareaView}
         */
        setValue: function (value) {
            if (value !== undefined && value !== null) {
                this.previousValue = this.getValue();
                this.model.setValue(value);
            }
            return this;
        },
        /**
         * Gets label of data
         * @returns {null}
         */
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        /**
         * Gets value of data
         */
        getValue: function () {
            return this.model.getValue();
        },
        getControl: function () {
            return this.$el.find("textarea");
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.TextArea", TextareaView);
}());

(function () {
    var TextView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-text").html()),
        validator: null,
        keyPressed: false,
        fieldValid: [],
        previousValue: null,
        firstLoad: false,
        formulaFieldsAssociated: [],
        jsonData: {},
        events: {
            "change input": "eventListener",
            "keydown input": "refreshBinding"
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallBack function
         * @param fn {function}
         * @returns {TextView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Sets onChangeCallbackOperation
         * @param fn {function}
         * @returns {TextView}
         */
        setOnChangeCallbackOperation: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallbackOperation = fn;
            }
            return this;
        },
        /**
         * Initializes field properties
         * @param options {object}
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.previousValue = this.model.get("value");
            this.formulaFieldsAssociated = [];
            this.model.on("change:data", this.eventListener, this);
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:toDraw", this.render, this);
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
                    control.on(e, function (event) {
                        fn(event, that);
                        event.stopPropagation();
                    });
                } else {
                    throw new Error("Is not possible find the HTMLElement associated to field");
                }
            }
            return this;
        },
        /**
         * Listen to event change
         * @param event
         * @returns {TextView}
         */
        eventListener: function (event) {
            this.onChange(event);
            this.onFieldAssociatedHandler();
            this.checkBinding();
            return this;
        },
        /**
         * Executes onChangeCallBack function
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            if (this.model.get("group") === "grid" && this.onChangeCallbackOperation) {
                if (typeof this.onChangeCallbackOperation === "function") {
                    this.onChangeCallbackOperation();
                }
            }
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        /**
         * Updates data and value
         * @param event
         * @returns {TextView}
         */
        onChange: function (event) {
            if (event.type === "change") {
                this.updateDataModel(event);
            } else {
                this.updateValueInput();
            }
            this.onTextTransform(this.tagControl.val());
            this.validate(event);
            this.clicked = false;
            return this;
        },
        /**
         * Updates the values in the inputs controls nodes
         * @returns {TextView}
         */
        updateValueInput: function () {
            var currentLabel = this.getText(),
                textInput = this.getControl(),
                hiddenInput = this.$el.find("input[type='hidden']");
            textInput.val(currentLabel);
            hiddenInput.val(currentLabel);
            return this;
        },
        /**
         * Update model (data and value)
         * @returns {TextView}
         */
        updateDataModel: function (event) {
            var controlHtml = this.getControl(),
                valueControl = controlHtml.val() || "";
            this.setValue(valueControl);
            return this;
        },
        findKeyValue: function (value) {
            var i, options = this.model.get("options");
            for (i = 0; i < options.length; i += 1) {
                if (options[i]["label"] === value) {
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
        mergeOptions: function (remoteOptions, click) {
            var item;
            if (remoteOptions.length) {
                item = remoteOptions[0];
                if (item.hasOwnProperty("value")) {
                    this.model.setValue(item["value"]);
                } else {
                    this.model.setValue("");
                }
            } else {
                this.model.setValue("");
            }
        },
        validate: function (event, b, c) {
            this.keyPressed = true;
            this.model.set("validate", true);
            this.model.validate();
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.tagControl.parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            this.keyPressed = false;
            return this;
        },
        updateValueControl: function () {
            var inputVal = this.$el.find("input").val();
            this.model.set("value", inputVal);
            return this;
        },
        /**
         * Executes formula associated
         * @returns {TextView}
         */
        onFieldAssociatedHandler: function () {
            var i,
                fieldsAssoc = this.formulaFieldsAssociated;
            if (fieldsAssoc.length > 0) {
                for (i = 0; i < fieldsAssoc.length; i += 1) {
                    if (fieldsAssoc[i].model.get("formulator") instanceof PMDynaform.core.Formula) {
                        this.model.addFormulaTokenAssociated(fieldsAssoc[i].model.get("formulator"));
                        this.model.updateFormulaValueAssociated(fieldsAssoc[i]);
                    }
                }
            }
            return this;
        },
        /**
         * Turns text value to uppercase or lowercase
         * @param val
         * @returns {TextView}
         */
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
                    none: function () {
                        return val;
                    },
                    capitalizePhrase: function () {
                        return val.charAt(0).toUpperCase() + val.slice(1);
                    },
                    titleCase: function () {
                        return val.capitalize();
                    }
                };
            if (transform) {
                transformed = (availables[transform]) ? availables[transform]() : availables["none"]();
                this.$el.find("input[type='text']").val(transformed);
            }
            return this;
        },
        /**
         * Validates fields supported on formula
         * @param fields
         * @returns {{}}
         */
        checkFieldsValidForFormula: function (fields) {
            var validFields = ["text", "label", "dropdown", "suggest", "textarea", "radio"],
                responseObject = {},
                itemField,
                i;
            for (i = 0; i < fields.length; i += 1) {
                itemField = fields[i];
                if (validFields.indexOf(itemField.model.get("type")) > -1) {
                    responseObject[itemField.model.get("id")] = itemField;
                }
            }
            return responseObject;
        },
        /**
         * Event to process the formula
         * @param rows
         * @returns {TextView}
         */
        onFormula: function (rows) {
            var fieldsList,
                that = this,
                allFieldsView,
                formulaField = this.model.get("formula"),
                idFields = {},
                fieldFormula,
                fieldAdded = [],
                group = this.model.get("group");
            fieldsList = group === "grid" ? rows : this.parent.items;
            allFieldsView = (fieldsList instanceof Array) ? fieldsList : fieldsList.asArray();
            idFields = this.checkFieldsValidForFormula(allFieldsView);
            fieldFormula = formulaField.replace(/\s/g, '').split(/[\-(,|+*/\)]+/);
            if (this.model.get("group") === "grid") {
                for (var k = 0; k < rows.length; k += 1) {
                    if (fieldFormula.indexOf(rows[k].model.get("id")) > -1) {
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
            for (var obj = 0; obj < this.fieldValid.length; obj += 1) {
                this.model.addFormulaFieldName(this.fieldValid[obj]);
                idFields[this.fieldValid[obj]].formulaFieldsAssociated.push(that);
                if (this.model.get("group") === "grid") {
                    if (idFields.hasOwnProperty(this.fieldValid[obj])) {
                        this.formulaFieldsAssociated.push(idFields[this.fieldValid[obj]]);
                    }
                }
            }
            return this;
        },
        /**
         * Gets HTML input of the control
         */
        getHTMLControl: function () {
            return this.$el.find("input");
        },
        render: function () {
            var hidden,
                name;
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }

            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
                hidden.value = this.model.get("value");
            }
            if (this.model.get("group") === "form" && this.model.get("formula")) {
                this.onFormula();
            }
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='text']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.getText());
            this.tagControl = this.$el.find("input[type='text']");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            this.onTextTransform(this.tagControl.val());
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * Sets value and updates previousValue
         * @param value {string}
         * @returns {TextView}
         */
        setValue: function (value) {
            if (value !== undefined) {
                this.previousValue = this.getValue();
                this.model.setValue(value);
            }
            return this;
        },
        /**
         * Gets label of data
         * @returns {null}
         */
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        /**
         * Gets value of data
         */
        getValue: function () {
            return this.model.getValue();
        },
        /**
         * Gets HTML control
         */
        getControl: function () {
            return this.$el.find(":input:not([type=hidden])") //hidden is not considered
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Text", TextView);
}());

(function () {
    var File = PMDynaform.view.Field.extend({
        item: null,
        isIE: false,
        template: _.template($("#tpl-file").html()),
        filesLength: 0,
        events: {
            "click .pmdynaform-file-container .form-control": "onClickButton"
        },
        /**
         * Default function
         */
        onChange: function () {
        },
        initialize: function (options) {
            var auxiliarValue = this.getText();
            this.form = options.form ? options.form : null;
            this.model.addLabelToStack(auxiliarValue.length > 0 ? auxiliarValue[0] : "");
            if (_.isArray(this.model.get("value"))) {
                this.filesLength = this.model.get("value").length;
            }
            if (!this.model.get("cleaned")) {
                this.model.on("change:data", this.render, this);
            }
        },
        onClickButton: function (event) {
            if (!PMDynaform.core.ProjectMobile) {
                this.$el.find("input").trigger("click");
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        /**
         * Sets the callback onChange function
         * @param callback
         */
        setOnChange: function (callback) {
            if (_.isFunction(callback)) {
                this.onChange = callback;
            }
            return this;
        },
        render: function () {
            var that = this,
                hidden,
                nameDefault = this.model.get("name"),
                name,
                fileButton,
                title = 'Allowed file extensions: ' + this.model.get('extensions'),
                link,
                i,
                data = this.model.get("data"),
                label = data.label;

            this.$el.html(this.template(this.model.toJSON()));
            fileButton = that.$el.find("button[type='button']");

            if (PMDynaform.core.ProjectMobile) {
                fileButton.attr("disabled", "disabled");
            }
            link = this.$el.find("a.pmdynaform-control-file");
            fileButton.text(title);
            fileButton[0].title = title;
            hidden = this.$el.find("input[type='hidden']");

            if (this.model.get("hint")) {
                this.enableTooltip();
            }

            if (link.length > 0 && label.length > 0) {
                for (i = 0; i < label.length; i += 1) {
                    link.children()[i].title = label[i];
                }
            }
            $(hidden).val(JSON.stringify(label));
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = nameDefault.substring(0, nameDefault.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
                hidden.value = this.model.get("value");
            }
            if (nameDefault.trim().length === 0) {
                this.$el.find("input[type='file']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.eventChangeFile();
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            return this;
        },
        /**
         * Get File Type
         * @param file
         * @returns {{type: *, fileTarget: *}}
         */
        getFileType: function (file) {
            var type,
                fileTarget;
            if (file.files) {
                if (file.files[0]) {
                    type = file.files[0].name.substring(file.files[0].name.lastIndexOf(".") + 1);
                    fileTarget = file.files[0].name;
                }
            } else {
                if (file.value.trim().length) {
                    type = file.value.split("\\")[2].substring(file.value.split("\\")[2].lastIndexOf(".") + 1);
                    fileTarget = file.value;
                }
            }
            return {
                type: type,
                fileTarget: fileTarget
            }
        },
        /**
         * Check File
         * @param file
         * @returns {boolean}
         */
        isValid: function (file) {
            var validated = false,
                maxSize,
                type,
                that = this,
                errorType = {},
                extensions = this.model.get("extensions"),
                getType = that.getFileType(file),
                validatorModel = this.model.get('validator'),
                tagFile = this.$el.find("input[type='file']")[0],
                fileButton = that.$el.find("button[type='button']"),
                title = 'Allowed file extensions: ' + extensions,
                maxSizeInt = parseInt(this.model.get("size"), 10),
                sizeUnity = this.model.get("sizeUnity");

            extensions = _.isString(extensions) ? extensions.toLowerCase() : "";
            type = (_.isObject(getType) && _.isString(getType.type)) ? getType.type.toLowerCase() : "";

            if (this.model.get("sizeUnity").toLowerCase() !== "kb") {
                maxSize = maxSizeInt * 1024;
            } else {
                maxSize = maxSizeInt;
            }
            if (extensions === "*" || extensions === ".*") {
                validated = true;
            } else {
                if (extensions.indexOf(type) > -1) {
                    validated = true;
                } else {
                    errorType = {
                        type: 'support',
                        message: 'The file extension is not supported. Supported extension(s): '
                        + extensions
                    };
                    validatorModel.set('fileOnly', errorType);
                    fileButton.text(title);
                    fileButton[0].title = title;
                    validated = false;
                }
            }
            if (validated && file.files) {
                if (file.files[0] && (file.files[0].size / 1024 <= maxSize)) {
                    validated = true
                } else {
                    errorType = {
                        type: 'size',
                        message: "The file size exceeds the limit. Max allowed limit is: " + maxSizeInt + sizeUnity
                    };
                    validatorModel.set('fileOnly', errorType);
                    fileButton.text(title);
                    fileButton[0].title = title;
                    validated = false;
                }
            }
            if (validated) {
                validatorModel.set('fileOnly', null);
                this.updateValue(file.value);
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                    this.$el.removeClass('has-warning has-feedback');
                }
            } else {
                if (this.validator) {
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
                    this.model.set({value: []}, {silent: true});
                }
            }
            return validated;
        },
        /**
         * Validate Field File
         * @param e
         * @returns {boolean}
         */
        validate: function (e) {
            var tagFile = this.$el.find("input[type='file']")[0],
                validated = true;
            if (this.model.get("mode") == "view" || this.model.get("mode") == "disabled") {
                return true;
            }
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                    this.$el.removeClass('has-warning has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    $(tagFile).prev().append(this.validator.el);
                    this.applyStyleError();
                    this.model.get('validator').get('fileOnly', null);
                    validated = false;
                    this.model.attributes.valid = false;
                } else {
                    validated = true;
                    this.model.attributes.valid = true;
                }
            } else {
                this.model.attributes.valid = true;
            }
            return validated;
        },
        /**
         * Event change listener for update data file
         * @returns {File}
         */
        eventChangeFile: function () {
            var that = this,
                fileControl = this.$el.find("input[type='file']"),
                file;
            if (fileControl) {
                fileControl.on("change", function (event) {
                    file = event.target;
                    if (file.value && that.isValid(file)) {
                        that.changeFile(file.files);
                    }
                });
            }
            return this;
        },
        /**
         * Change File and update data
         * @param files
         */
        changeFile: function (files) {
            var nameFileLoad = "",
                index = 0;
            if (files && files.length > 0) {
                nameFileLoad = files[index] ? files[index].name : nameFileLoad;
            }
            this.updateValueButton(nameFileLoad);
            this.model.addLabelToStack(nameFileLoad);
            this.updateHidden(nameFileLoad);
            this.checkBinding();
        },
        /**
         * Update Text Button
         * @param nameFileLoad
         * @returns {File}
         */
        updateValueButton: function (nameFileLoad) {
            var index = 0,
                fileButton = this.$el.find("button[type='button']");
            if (_.isObject(fileButton) && !_.isEmpty(fileButton)) {
                fileButton.text(nameFileLoad);
                fileButton[index].title = nameFileLoad;
            }
            return this;
        },
        /**
         * Update Hidden Value
         * @returns {File}
         */
        updateHidden: function (nameFileLoad) {
            var hidden,
                nameFile = "[]",
                id;
            if (this.model.get("group") === "form") {
                id = "form[" + this.model.get("name") + "_label]";
                hidden = this.$el.find("input[type='hidden'][id='" + id + "']");
                if (hidden) {
                    nameFile = "[" + '"' + nameFileLoad + '"' + "]";
                    $(hidden).val(nameFile);
                }
            }
            return this;
        },
        /**
         * Updates the values of the value of the model
         * @param  {string} pathFile the new path to update
         * @return {this}
         */
        updateValue: function (pathFile) {
            var value = this.model.get("value");
            if (pathFile && _.isArray(value)) {
                value.push(pathFile);
                if (value.length === this.filesLength + 1) {
                    this.model.attributes.value = value;
                } else {
                    value.splice(this.filesLength, 1);
                    this.model.attributes.value = value;
                }
            }
            return this;
        },
        /**
         * Gets Text File
         * @returns {null}
         */
        getText: function () {
            return this.model.getText();
        },
        /**
         * Gets Value File
         * @returns {*}
         */
        getValue: function () {
            return this.model.getValue();
        },
        /**
         * Gets Html Control of the File
         * @returns {*}
         */
        getControl: function () {
            var htmlControl = this.$el.find("button").eq(0);
            return htmlControl;
        },
        /**
         * Clear Content File
         * @returns {File}
         */
        clearContent: function () {
            var controlFileHtml = this.$el.find(".pmdynaform-field-file"),
                valueControlFile = this.model.getValue(),
                appDocUID = this.model.getAppDocUID(),
                name;
            appDocUID = _.isArray(appDocUID) && !_.isEmpty(appDocUID) ? appDocUID[0] : null;
            if (valueControlFile && controlFileHtml && controlFileHtml.length > 0 && this.model.get("mode") !== "view") {
                name = this.createNameForHidden();
                this.model.clearContent();
                this.clearDomFile();
                if (appDocUID) {
                    this.appendHiddenInput(name, appDocUID);
                }
                this.model.clearStackLabels();
            }
            return this;
        },
        /**
         * Clear DOM File Control
         */
        clearDomFile: function () {
            this.$el.find("input[type=file]").val("");
            this.$el.find("input[type=hidden]").val("[]");
            this.$el.find("button").text("Allowed file extensions: .*");
            this.$el.find("button").prop("title", "Allowed file extensions: .*");
        },
        /**
         * Append Hidden Fields
         * @param name
         * @param appDocUid
         */
        appendHiddenInput: function (name, appDocUid) {
            var hiddenDocUid = [],
                hiddenVersion = [],
                inputType = "hidden",
                versionDefault = "1";
            hiddenDocUid = $("<input>", {
                name: name + "[appDocUid]",
                type: inputType,
                value: appDocUid
            });
            hiddenVersion = $("<input>", {
                name: name + "[version]",
                type: inputType,
                value: versionDefault
            });
            this.$el.append(hiddenDocUid);
            this.$el.append(hiddenVersion);
        },
        /**
         * Create Name for Files Deleted
         * @returns {string|*}
         */
        createNameForHidden: function () {
            var varName = "form[__VARIABLE_DOCUMENT_DELETE__]",
                idFile;
            idFile = this.model.get("id");
            varName = varName + "[" + idFile + "]";
            return varName;
        },
        /**
         * Executes onChange function
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.model.getCurrentNameFile(),
                    previous: this.model.getPreviousNameFile()
                };
            if (paramsValue.current !== paramsValue.previous) {
                this.onChange(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
            }
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.File", File);
}());

(function () {
    var CheckGroupView = PMDynaform.view.Field.extend({
        item: null,
        template: _.template($("#tpl-checkgroup").html()),
        templateOptions: _.template($("#tpl-checkgroup-options").html()),
        previousValue: null,
        firstLoad: false,
        /**
         * @param {boolean} existHTML: If html exists the value changes to true
         */
        existHTML: false,
        events: {
            "change input": "eventListener",
            "keydown input": "preventEvents"
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn {function}
         * @returns {CheckGroupView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:options", this.redrawOptions, this);
            this.model.on("change:toDraw", this.render, this);
        },
        /**
         * events triggered "change" and "validate when the field undergoes a change,
         * this method is llamdo by the set function or action
         */
        eventListener: function (event, value) {
            this.onChange(event, value);
            this.checkBinding(event, value);
        },
        /**
         * Executes onChangeCallback function
         * @returns {CheckGroupView}
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: JSON.stringify(this.getValue()),
                    previous: JSON.stringify(this.previousValue)
                };

            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }

            return this;
        },
        render: function () {
            var hidden,
                name;
            this.existHTML = true;
            this.$el.html(this.template(this.model.toJSON()));
            this.$el.find(".form-control").css({
                height: "auto"
            });
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.setValueToDomain();
            this.$el.find("input[type='hidden']")[0].name = "form[" + this.model.get("name") + "_label]";
            this.setValueHideControl();
            this.previousValue = this.model.get("value");
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='checkbox']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.tagControl = this.$el.find(".pmdynaform-checkbox-items");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        validate: function () {
            this.model.set({}, {validate: true});
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.$el.find(".pmdynaform-control-checkbox-list").parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        onChange: function (event, value) {
            this.updateValues(event, value);
            this.validate();
        },
        updateValues: function (event, values) {
            var i,
                dataChecked = {
                    values: [],
                    labels: []
                },
                item,
                elements,
                data,
                inputsTag;

            if (values && _.isArray(values)) {
                inputsTag = this.$el.find("input[type='checkbox']");
                inputsTag.attr("checked", false);
                elements = this.$el.find("input[type='checkbox']");
                for (i = 0; i < values.length; i += 1) {
                    item = values[i];
                    data = this.checkValueInOptions(item);
                    if (data) {
                        elements[data["index"]].checked = true;
                        dataChecked.values.push(data['value']);
                        dataChecked.labels.push(data['label']);
                    }
                }
            } else {
                dataChecked = this.getDataChecked();
            }
            this.model.set("labelsSelected", dataChecked.labels);
            this.model.attributes.value = dataChecked.values;
            this.updateDataModel(dataChecked.values, dataChecked.labels);
            this.$el.find("input[type='hidden']").val(JSON.stringify(dataChecked.labels));
            return this;
        },
        getDataChecked: function () {
            var dataChecked = {
                    values: [],
                    labels: []
                },
                checkedControls = this.$el.find("input[type='checkbox']:checked");
            checkedControls.each(function (index, element) {
                dataChecked.values.push(element.value);
                dataChecked.labels.push($(element).next().text());
            });
            return dataChecked;
        },
        checkValueInOptions: function (item) {
            var options,
                i;
            options = this.model.get("options");
            for (i = 0; i < options.length; i += 1) {
                if (options[i]["value"] === item) {
                    return {
                        value: options[i]["value"],
                        label: options[i]["label"],
                        index: i
                    }
                }
            }
            return false;
        },
        updateDataModel: function (value, label) {
            var data;
            if (jQuery.isArray(label)) {
                label = JSON.stringify(label);
            }
            data = {
                value: value,
                label: label
            };
            this.model.set("data", data);
            this.populateItemsPrintMode(data.label);
            return this;
        },
        getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-checkbox-list");
        },
        setValueHideControl: function () {
            var control;
            control = this.$el.find("input[type='hidden']");
            $(control).val(JSON.stringify(this.model.get("labelsSelected")));
            return this;
        },
        /**
         * Sets value and update previousValue
         * @param value
         * @returns {CheckGroupView}
         */
        setValue: function (value) {
            if (value && $.isArray(value)) {
                this.previousValue = this.getValue();
                this.model.set("value", value);
            }
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        renderOptions: function () {
            var htmlOptions,
                contendControl,
                config;
            contendControl = this.$el.find(".pmdynaform-checkbox-items");
            contendControl.empty();

            if (_.isArray(this.model.get("options"))) {
                config = {
                    name: this.model.get("name"),
                    id: this.model.get("id"),
                    type: "checkbox",
                    namespace: this.model.get("namespace"),
                    disabled: this.model.get("disabled"),
                    options: this.model.get("options")
                };
                htmlOptions = this.templateOptions(config);
                contendControl.append(htmlOptions);
            }
            return this;
        },
        getControl: function () {
            var htmlControl = this.$el.find("input[type='checkbox']");
            return htmlControl;
        },
        /**
         * redrawOptions, Draw component options
         * @chainable
         */
        redrawOptions: function () {
            if (this.existHTML) {
                this.renderOptions();
                this.updateValues();
            }
            return this;
        },
        /**
         * Populate Checkgroup in print mode
         * @param arrayItems
         */
        populateItemsPrintMode: function (arrayItems) {
            var i,
                max,
                containerPrint = this.$el.find(".content-print"),
                itemsCheckGroup = JSON.parse(arrayItems);
            containerPrint.empty();
            for (i = 0, max = itemsCheckGroup.length; i < max; i += 1) {
                containerPrint.append("<li>" + itemsCheckGroup[i] + "</li>");
            }

        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.CheckGroup", CheckGroupView);
}());

(function () {
    var CheckBoxView = PMDynaform.view.Field.extend({
        item: null,
        template: _.template($("#tpl-checkbox_yes_no").html()),
        previousValue: null,
        /**
         * This property is a handler the events
         * change input: when the change the control input type checkbox is fired the method handler event Listener
         */
        events: {
            "change input": "eventListener",
            "keydown input": "preventEvents"
        },
        firstLoad: false,
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn
         * @returns {CheckBoxView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.previousValue = this.getValue();
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:toDraw", this.render, this);
        },
        /**
         * Listens to change event
         * @param event
         * @param value
         */
        eventListener: function (event, value) {
            this.onChange(event, value);
            this.checkBinding(event, value);
        },
        /**
         * Executes onChangeCallback function
         * @returns {CheckBoxView}
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: JSON.stringify(this.getValue()),
                    previous: JSON.stringify(this.previousValue)
                };

            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        preventEvents: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return this;
        },
        render: function () {
            var hidden,
                name;
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.setValueToDomain();
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            } else {
                this.$el.find("input[type='hidden']")[0].name = "form[" + this.model.get("name") + "_label]";
            }
            if (this.model.get("options").length) {
                this.$el.find("input[type='checkbox']").eq(0).data({
                    value: this.model.get("options")[0]["value"],
                    label: this.model.get("options")[0]["label"]
                });
                this.$el.find("input[type='checkbox']").eq(1).data({
                    value: this.model.get("options")[1]["value"],
                    label: this.model.get("options")[1]["label"]
                });
            }
            this.setValueHideControl();
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='checkbox']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.tagControl = this.$el.find(".pmdynaform-checkbox-items");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        validate: function () {
            this.model.set({}, {validate: true});
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.$el.find(".pmdynaform-control-checkbox-list").parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        /**
         * This method is fired when the property value is changed
         * @param event: is the event target tag
         * @param value: is the change model when use set method
         */
        onChange: function (event, value) {
            this.updateValues(event, value);
            if (!this.firstLoad) {
                this.validate();
            }
            return this;
        },
        getHTMLControl: function () {
            return this.$el.find(".pmdynaform-control-checkbox-list");
        },
        setValueHideControl: function () {
            var control;
            control = this.$el.find("input[type='hidden']");
            if (this.model.get("dataType") === "boolean") {
                $(control).val(this.model.get("data")["label"].toString());
            } else {
                try {
                    $(control).val(JSON.stringify(this.model.get("data")["label"]));
                }
                catch (e) {
                    console.error(e);
                }
            }
            return this;
        },
        updateValues: function (event, value) {
            var controlHtml = this.$el.find("input[type='checkbox']"),
                firstCheckbox = controlHtml.eq(0),
                secondCheckbox = controlHtml.eq(1),
                newValue = "0",
                valuesForTrue = [1, true, "1", "true"],
                data;
            if (!value) {
                if (firstCheckbox.prop("checked")) {
                    secondCheckbox.prop("checked", false);
                    newValue = "1";
                } else {
                    secondCheckbox.prop("checked", true);
                    newValue = "0";
                }
                this.model.set({value: newValue}, {silent: true});
            } else {
                newValue = value;
                if (valuesForTrue.indexOf(newValue) !== -1) {
                    firstCheckbox.prop("checked", true);
                    secondCheckbox.prop("checked", false);
                } else {
                    firstCheckbox.prop("checked", false);
                    secondCheckbox.prop("checked", true);
                }
            }
            this.updateDataModel(newValue);
            data = this.model.get("data");
            this.$el.find(".content-print").text(data.label);
            this.$el.find("input[type='hidden']").val(data.label);
            return this;
        },
        /**
         * Update data of checkbox model
         * @param value
         * @returns {CheckBoxView}
         */
        updateDataModel: function (value) {
            this.model.setData(value);
            return this;
        },
        /**
         * Call to setValue of Model
         * @param value
         * @returns {CheckBoxView}
         */
        setValue: function (value) {
            this.previousValue = this.getValue();
            this.model.setValue(value);
            this.firstLoad = false;
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        getControl: function () {
            var htmlControl = this.$el.find("input[type='checkbox']");
            return htmlControl;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.CheckBox", CheckBoxView);
}());

(function () {

    var SuggestView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-text").html()),
        templateList: _.template($("#tpl-suggest-list").html()),
        templateOptions: _.template($("#tpl-suggest-options").html()),
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
        firstLoad: true,
        dirty: false,
        jsonData: {},
        previousValue: "",
        previousLabel: "",
        prevValueprevValue: "",
        prevValue: "",
        numberOfOptions: 10,
        containerOpened: false,
        events: {
            "keyup input": "generateOptionsHandler",
            "keydown input": "refreshBinding",
            "focusin input": "preventFocusIn",
            "change input": "eventListener"
        },
        stamp: null,
        /**
         * @param {Number} timeToWait - is the timer required to trigger an action data request
         * if the value is 0, does not perform cancellation requests, if the value is not 0,
         * this will be the timempo waiting before making a request
         */
        timeToWait: 0,
        timeoutHandler: null,
        xhr: null,
        remoteOptions: [],
        scrollTop: 0,
        controlSuggest: null,
        showVisibleElements: 5,
        heightItem: 40,
        /**
         *  @param {Array} - navigationKeys are valid navigation keys for selecting an item from the list
         */
        navigationKeys: [38, 40, 13],
        /**
         * @param {Array} invalidEnterKeys - is the list of keyboard events captured incorrect not to execute queries
         */
        invalidEnterKeys: [16, 17, 18, 20, 37, 39, 9],
        /**
         * This method is called for cancellation of writing board per share
         * when the value of "timeToWait" is 0 this method is not call
         * @private
         */
        selectedListItem: null,
        iconLoadingActive: true,
        keyNavigatorPress: false,

        _abortRequest: function () {
            if (this.xhr) {
                this.xhr.abort();
            }
            clearTimeout(this.timeoutHandler);
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets onChangeCallback function
         * @param fn {function}
         * @returns {SuggestView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.formulaFieldsAssociated = [];
            this.model.on("change:value", this.eventListener, this);
            this.setTimeToWait(parseInt(this.model.get("delay")));
            this.model.on("change:toDraw", this.render, this);
        },
        /**
         * set the time to wait
         * @param time
         * @returns {boolean}
         */
        setTimeToWait: function (time) {
            if (!isNaN(time)) {
                this.timeToWait = time;
            }
            return this;
        },
        preventFocusIn: function (event) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        },
        /**
         * run dependent fields when is  out focus, or selected option of the list
         */
        triggerDependency: function (event, data) {
            if (this.containerOpened) {
                this.eventListener(event, data);
                this.onFieldAssociatedHandler();
                this.destroySuggest();
            }
            return this;
        },
        clickedIsInSuggestList: function (event) {
            var target = null, item, clickInList = false;
            if (event && event.target) {
                target = event.target;
                if (this.containerOpened) {
                    item = this.containerList.find(target);
                }
                if (item && item.length) {
                    clickInList = true;
                    this.containerOpened = false;
                }
            }
            return clickInList;
        },
        /**
         * Listens to change event
         * @param event
         * @param value
         */
        eventListener: function (event, value) {
            if (event instanceof Backbone.Model) {
                this.onChange(event, value);
                this.onFieldAssociatedHandler();
            } else {
                if (event.type === "change") {
                    if (!this.keyNavigatorPress) {
                        this.updateValues(event, this.model.get("data").value);
                        this.model.trigger("change:data");
                        // if isn't listed it's not needed shot setOnChange event
                        if (!this.containerOpened) {
                            this.onChange(event, this.model.get("data").value);
                        }
                    }
                } else {
                    if (this.clickedIsInSuggestList(event) || this.$el.find(event.target).length) {
                        this.onChange(event, value);
                    } else {
                        this.onChange(event, this.model.get("data"));
                    }

                    this.onFieldAssociatedHandler();
                }
                this.keyNavigatorPress = false;
            }
        },
        generateOptionsHandler: function (event) {
            var that = this,
                newValue = this.controlSuggest ? this.controlSuggest.val() : "";

            if (newValue === null || newValue === undefined || newValue === "") {
                this.model.set({'data': {value: "", label: ""}}, {silent: true});
                this.destroySuggest();
            } else {
                if (this.navigationKeys.indexOf(event.which) === -1 &&
                    this.invalidEnterKeys.indexOf(event.which) === -1 && !event.ctrlKey) {
                    if (this.timeToWait) {
                        this._abortRequest();
                    }
                    if (newValue === "") {
                        this.hideSuggest();
                    } else {
                        this.prevValue = newValue;
                        if (event && event.type !== "submit") {
                            this.timeoutHandler = setTimeout(function () {
                                that.suggestPanelFactory(event);
                            }, this.timeToWait);
                        }
                    }
                }
            }
            return this;
        },
        suggestPanelFactory: function (event) {
            var value;
            value = this.controlSuggest.val();
            this.keyPressed = false;
            this.prevValue = value;
            this.model.attributes.value = value;
            this.model.set({'data': {value: value, label: value}}, {silent: true});
            this.generateOptions(this.numberOfOptions, value, event);
            return this;
        },
        refreshBinding: function (event) {
            var code;
            if (event) {
                code = event.which;
                if (code === 13) {
                    event.preventDefault();
                    event.stopPropagation();
                } else if (code === 9) {
                    this.pressedKeyTab(event);
                    this.keyNavigatorPress = false;
                    this.destroySuggest(event);
                }
                if (this.isListNavigationEvents(code)) {
                    this.keyNavigatorPress = true;
                    this.eventsOfNavigationList(code, event);
                }
                this.keyPressed = true;
            }
            return this;
        },
        isListNavigationEvents: function (code) {
            var resp = false;
            if (code && this.navigationKeys.indexOf(code) !== -1) {
                resp = true;
            }
            return resp;
        },
        eventsOfNavigationList: function (code, event) {
            if (typeof code === "number" && event) {
                switch (code) {
                    case 38:
                        this.pressedKeydownUp(event);
                        break;
                    case 40:
                        this.pressedKeydownDown(event);
                        break;
                    case 13:
                        this.pressedKeyIntro(event);
                        break;
                    case 9:
                        this.pressedKeyTab(event);
                        break;
                }
            }
            return this;
        },
        pressedKeydownUp: function (event) {
            var itemSelected;
            event.preventDefault();
            event.stopPropagation();
            if (this.containerOpened && ((this.stackRow - 1) >= 0)) {
                itemSelected = this.containerList.find('li').eq(this.stackRow - 1);
                if (itemSelected.length) {
                    itemSelected.find('a').focus();
                    this.stackRow = this.stackRow - 1;
                    this.toggleItemSelected(itemSelected);
                }
            }
            return this;
        },
        pressedKeydownDown: function (event) {
            var itemSelected;
            event.preventDefault();
            event.stopPropagation();
            if (this.containerOpened) {
                if ((this.stackRow + 1) <= this.containerList.find('li').length) {
                    itemSelected = this.containerList.find('li').eq(this.stackRow + 1);
                    if (itemSelected.length) {
                        itemSelected.find('a').focus();
                        this.stackRow = this.stackRow + 1;
                        this.toggleItemSelected(itemSelected);
                    }
                }
            }
            return this;
        },
        pressedKeyIntro: function (event) {
            var label,
                value,
                data;
            if (this.containerOpened) {
                label = this.selectedListItem.find("a").data().label;
                value = this.selectedListItem.find("a").data().value;
                data = {
                    value: value,
                    label: label
                };
                this.triggerDependency(event, data);
                this.hideSuggest();
                this.controlSuggest.focus();
            }
            return this;
        },
        pressedKeyTab: function (event) {
            this.model.trigger("change:data");
            return this;
        },
        /**
         * if the final event is a "click" returns the data of an item
         * element list html or input type text
         */
        clickedItemList: function (event, data) {
            var target = event.target,
                element,
                data;

            if (this.containerList.find(target).length) {
                element = $(event.currentTarget).find("a");
                data = this.getListData(element);
            } else {
                data = {
                    value: this.controlSuggest.val(),
                    label: this.controlSuggest.val()
                }
            }
            this.triggerDependency(event, data);
            this.controlSuggest.focus();
            return this;
        },
        /**
         * Executes onChangeCallBack function
         */
        checkBinding: function () {
            //If the key is not pressed, executes the render method
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
                this.previousLabel = this.getText();
            }
        },
        /**
         * Updates value dom
         * @returns {SuggestView}
         */
        updateValueInput: function () {
            var textInput,
                hiddenInput;
            textInput = this.$el.find("input[type='suggest']");
            hiddenInput = this.$el.find("input[type='hidden']");
            if (this.model.get("data")) {
                textInput.val(this.model.get("data")["label"]);
                hiddenInput.val(this.model.get("data")["value"]);
            }
            return this;
        },
        setValueDefault: function () {
            this.model.set("value", $(this.el).find(":input").val());
        },
        hideSuggest: function () {
            if (this.containerList instanceof jQuery && this.containerList.length) {
                this.containerList.hide();
                this.removeLoadingIcon();
            }
            this.containerOpened = false;
            this.stackRow = 0;
        },
        showSuggest: function () {
            this.containerOpened = true;
            this.containerList.show();
        },
        _calculatePosition: function () {
            var element,
                position,
                leftListElement,
                topListElement,
                listHeight;
            element = this.$el.find("input[type='suggest']");
            if (element[0].getBoundingClientRect) {
                position = element[0].getBoundingClientRect();
                if (position["top"] !== undefined) {
                    document.body.appendChild(this.containerList[0]);
                    leftListElement = position.left;
                }

                listHeight = this.containerList.outerHeight();

                if (listHeight <= position.top) {
                    topListElement = position.top + this._getScrollOffsets() - this.containerList.outerHeight();
                } else {
                    topListElement = position.bottom + this._getScrollOffsets();
                }
                this.containerList.css({
                    position: "absolute",
                    width: element.outerWidth(),
                    "min-width": 100,
                    left: leftListElement,
                    top: topListElement
                });
            }
        },
        _getScrollOffsets: function () {
            return document.body.scrollTop || document.documentElement.scrollTop || window.pageYOffset || getScrollTop();
        },
        _attachSuggestGlobalEvents: function () {
            if (this.containerList) {
                $(document).on("click." + this.cid, $.proxy(this.triggerDependency, this));
            }
        },
        /**
         * Updates data and value
         * @param event
         * @param value
         * @returns {SuggestView}
         */
        onChange: function (event, value) {
            this.removeLoadingIcon();
            this.updateValues(event, value);
            this.checkBinding();
            this.validate(event);
            this.clicked = false;
            return this;
        },
        /**
         * Updates model's data
         * @param event
         * @param value
         * @returns {SuggestView}
         */
        updateValues: function (event, value) {
            var hiddenInput, label, newValue, suggestControl;
            hiddenInput = this.$el.find("input[type='hidden']");
            suggestControl = this.$el.find("input[type='suggest']");
            if (event.type === "change") {
                hiddenInput.val(value);
            }

            if (event.type === "keydown") {
                hiddenInput.val(this.model.get("value"));
            }

            if (event.type === "click" || event.keyCode === 13) {
                hiddenInput.val(value.value);
                suggestControl.val(value.label);
            }
            if (!event.type && !event.keyCode) {
                hiddenInput.val(value);
                suggestControl.val(value);
            }
            label = suggestControl.val();
            newValue = hiddenInput.val();
            this.model.set("keyLabel", label);
            this.model.attributes.value = newValue;
            this.updateDataModel(newValue, label);
            return this;
        },
        updateDataModel: function (value, label) {
            var data,
                classPrint = ".content-print";
            data = {
                value: value,
                label: label
            };
            this.model.set("data", data);
            this.$el.find(".content-print").text(data.label);
            return this;
        },
        /**
         * builds suggest panel
         * @param maxItems
         * @param event
         */
        generateOptions: function (maxItems, val, event) {
            var localData = [],
                that = this;
            this.elements = [];
            this.elements = this.model.get("options");
            this.stackItems = [];
            if (this.containerList !== null) {
                this.containerList.empty();
            }
            if (val !== null && val !== undefined) {
                this.addLoadingIcon();
                localData = this.filterLocalOptions(val, this.numberOfOptions, event);
                this.makeNewListItems(localData, val, event);
            }
            return this;
        },
        addLoadingIcon: function () {
            var spinner;
            if (this.iconLoadingActive) {
                spinner = this.$el.find(".spinner-icon");
                spinner.addClass("active-spinner");
            }
            return this;
        },
        removeLoadingIcon: function () {
            var spinner;
            if (this.iconLoadingActive) {
                spinner = this.$el.find(".spinner-icon");
                spinner.removeClass("active-spinner");
            }
            return this;
        },
        makeNewListItems: function (localData, val, event) {
            var that = this, controlVal;
            if (this.canExecuteQuery()) {
                this.executeSuggestQuery(function (data, xhr) {
                    controlVal = that.controlSuggest.val();
                    if (controlVal && that.xhr === xhr) {
                        that.refreshSuggestList(localData.concat(data), val, that.numberOfOptions, event);
                        if (localData.length > 0 || data.length > 0) {
                            that._calculatePosition();
                        } else {
                            that.hideSuggest();

                        }

                    }
                });
            } else {
                this.refreshSuggestList(localData, val, this.numberOfOptions);
                if (_.isArray(localData) && localData.length > 0) {
                    this._calculatePosition();
                } else {
                    this.hideSuggest();
                }
            }
            return this;
        },
        canExecuteQuery: function () {
            return this.model.canExecuteQuery();
        },
        /**
         * filter local options pased in html
         */
        filterLocalOptions: function (value, maxItems, event) {
            var itemLabel,
                that = this,
                count = 0,
                data = [];
            //filter data
            $.grep(that.elements, function (options, index) {
                itemLabel = options.label.toString();
                if ((itemLabel.toLowerCase().indexOf(value.toLowerCase()) !== -1) && count < maxItems) {
                    data.push(options);
                    count += 1;
                }
            });
            return data;
        },
        _attachListSuggestEvents: function () {
            if (this.containerList instanceof jQuery) {
                this._attachMouseOverEvents(this.containerList);
                this._attachKeyDownEvents(this.containerList);
            }
            return this;
        },
        _attachMouseOverEvents: function (list) {
            var that = this;
            if (list && list.length) {
                $(this.containerList).on('mouseover', 'li', function (e) {
                    var selectedItem;
                    selectedItem = that.containerList.find(this);
                    if (selectedItem.length) {
                        that.stackRow = selectedItem.index();
                        that.toggleItemSelected(selectedItem);
                    } else {
                        that.stackRow = -1;
                    }
                });
            }
            return this;
        },
        _attachKeyDownEvents: function (list) {
            var that = this;
            if (list && list.length) {
                list.on('keydown', function (e) {
                    var code = e.which;
                    e.stopPropagation();
                    e.preventDefault();
                    if (that.isListNavigationEvents(code)) {
                        that.eventsOfNavigationList(code, e);
                    }
                });
            }
            return this;
        },
        _attachClickEventInList: function (listElements) {
            var that = this;
            if (listElements instanceof jQuery) {
                listElements.click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    that.clickedItemList(e);
                });
            }
            return this;
        },
        /**
         * refresh suggest panel
         * @param data
         */
        refreshSuggestList: function (data, value, maxItems, event) {
            var htmlOptions;
            this.stackItems = [];
            if (!this.containerList) {
                this.containerList = $(this.templateList());
                this._attachMouseOverEvents(this.containerList);
                this._attachKeyDownEvents(this.containerList);
            }
            this.stackRow = -1;
            this.containerList.empty();
            htmlOptions = this.templateOptions({
                options: data
            });
            this.containerList.append(htmlOptions);
            this.stackItems = this.containerList.find("li");
            this._attachClickEventInList(this.stackItems);
            this.showSuggest();
            this.updatingItemsList();
            return this;
        },
        updatingItemsList: function (html) {
            var index = 0,
                item;
            if (this.containerList) {
                item = this.containerList.find("li").eq(index).outerHeight();
                if (item instanceof jQuery && item.length) {
                    this.heightItem = item.outerHeight();
                }
                this.controlSuggest.after(this.containerList);
                if (this.stackItems.length > 4) {
                    this.containerList.css("height", this.heightItem * this.showVisibleElements);
                } else {
                    this.containerList.css("height", "auto");
                }
                this._attachSuggestGlobalEvents();
            }
            return this;
        },
        validate: function (event) {
            if (event && (event.which === 9) && (event.which !== 0)) { //tab key
                this.keyPressed = true;
            }
            this.model.attributes.validator.set("valid", true);
            this.model.validate(this.model.toJSON());
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error has-feedback');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.$el.find("input[type='suggest']").parent().append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        render: function () {
            var data,
                hidden,
                name,
                value = this.model.get("value");
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            data = this.model.get("data");
            if (this.firstLoad) {
                if (value !== undefined && value !== null && value !== "" && data) {
                    this.$el.find("input[type='suggest']").val(data["label"] === undefined || data["label"] === null ? "" : data["label"]);
                } else {
                    this.model.emptyValue();
                }
            } else {
                this.$el.find("input[type='suggest']").val(data["label"] === undefined || data["label"] === null ? "" : data["label"]);
            }
            this.setNameHiddenControl();
            this.$el.find("input[type='suggest']").focus();
            this.setValueToDomain();

            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='suggest']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.tagControl = this.$el.find("input[type='hidden']");
            this.tagHiddenToLabel = this.$el.find("input[type='suggest']");
            this.keyLabelControl = this.$el.find("input[type='hidden']");
            this.prevValue = this.$el.find("input[type='suggest']").val();
            this.previousValue = this.model.get("value");
            this.previousLabel = this.model.get("data").label;
            this.controlSuggest = this.$el.find("input[type='suggest']");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * Executes Suggest query to recovery all data
         * considering dependent fields
         * @returns {*}
         */
        executeSuggestQuery: function (callback) {
            var i,
                prj,
                postData,
                parentDependents,
                variable,
                data = {
                    "filter": this.model.get('value'),
                    "order_by": "ASC",
                    "limit": this.numberOfOptions,
                    "app_uid": PMDynaform.getProjectKeys().caseUID ? PMDynaform.getProjectKeys().caseUID : null
                };
            _.extend(data, this.model.buildDataForQuery({}));
            prj = this.model.get("project");
            if (this.model.get("group") === "grid") {
                variable = this.model.get("columnName");
            } else {
                if (this.model.get("variable") !== "") {
                    variable = this.model.get("variable");
                } else {
                    variable = this.model.get("id");
                }
            }
            this.xhr = prj.webServiceManager.executeQuerySuggest(data, variable, callback);
            return this;
        },
        mergeOptions: function (remoteOptions) {
            var k,
                remoteOpt = [],
                localOpt,
                options;
            for (k = 0; k < remoteOptions.length; k += 1) {
                remoteOpt.push({
                    value: remoteOptions[k].value,
                    label: remoteOptions[k].text
                });
            }
            localOpt = this.model.get("localOptions");
            this.model.attributes.remoteOptions = remoteOpt;
            options = localOpt.concat(remoteOpt);
            this.model.attributes.options = options;
            if (options.length) {
                this.model.attributes.data = {
                    value: options[0]["value"],
                    label: options[0]["label"]
                };
                this.updateValueInput();
                this.model.set("value", options[0]["value"]);
            }
            return options;
        },
        toggleItemSelected: function (selectedItem) {
            if (selectedItem instanceof jQuery) {
                if (selectedItem.length !== -1) {
                    this.selectedListItem = selectedItem;
                    this.deselectItems();
                    selectedItem.addClass("pmdynaform-suggest-list-keyboard");
                }
            }
            return this;
        },
        deselectItems: function () {
            if (this.containerOpened) {
                this.containerList.find("li").removeClass("pmdynaform-suggest-list-keyboard");
            }
            return this;
        },
        updateValueControl: function () {
            var inputVal = this.$el.find("input[type='suggest']").val();

            this.model.set("value", inputVal);

            return this;
        },
        getHTMLControl: function () {
            return this.$el.find("input[type='suggest']");
        },
        afterRender: function () {
            return this;
        },
        setNameHiddenControl: function () {
            var hidden,
                name;
            if (this.el) {
                if (this.model.get("group") === "grid") {
                    hidden = this.$el.find("input[type = 'hidden']")[0];
                    name = this.model.get("name");
                    name = name.substring(0, name.length - 1).concat("_label]");
                    this.$el.find("input[type='suggest']")[0].name = "form" + name;
                    this.$el.find("input[type='suggest']")[0].id = "form" + name;
                } else {
                    this.$el.find("input[type='suggest']")[0].name = "form[" + this.model.get("name") + "_label]";
                }
            }
            return this;
        },
        /**
         * Sets value and updates previousValue
         * @param value {string}
         * @returns {SuggestView}
         */
        setValue: function (value) {
            if (value !== undefined) {
                this.previousValue = this.getValue();
                this.model.setValue(value);
            }
            return this;
        },
        setData: function (data) {
            var dataObject;
            if (typeof data === "object") {
                dataObject = {
                    value: data['value'] !== undefined ? data['value'] : "",
                    label: data['label'] !== undefined ? data['label'] : ""
                };
                this.model.set("data", dataObject);
                this.model.attributes.value = dataObject["value"];
                this.render();
            }
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        getControl: function () {
            var htmlControl = this.$el.find("input[type='suggest']");
            return htmlControl;
        },
        /**
         * if the final event is a "keyUp" returns the data of an element html
         */
        _keyUpListenerData: function (data) {
            var data, element;
            element = this.containerList.find(".pmdynaform-suggest-list-keyboard").find("a");
            data = this.getListData(element);
            return data;
        },
        /**
         * if the final event is a "keydown" returns the data of an element html
         */
        _keyDownListenerData: function (event, data) {
            var element, data;
            if (event.which === 13) {
                element = this.containerList.find(".pmdynaform-suggest-list-keyboard").find("a");
                data = this.getListData(element);
            }
            if (event.which === 9) {
                data = {
                    value: this.controlSuggest.val(),
                    label: this.controlSuggest.val()
                }
            }
            return data;
        },
        /**
         * destroys the container list elements
         */
        destroySuggest: function (event) {
            this.removeLoadingIcon();
            if (this.containerList) {
                this.containerList.remove();
                this.containerList = null;
                this.containerOpened = false;
                this.stackRow = 0;
                this.clicked = false;
            }
            return this;
        },
        /**
         * get the data if there from an html element
         * return [object]
         */
        getListData: function (element) {
            var label,
                value,
                data = {
                    value: "",
                    label: ""
                };
            if (element) {
                data = {
                    label: $(element).data() ? $(element).data().label : "",
                    value: $(element).data() ? $(element).data().value : ""
                }
            }
            return data;
        },
        /**
         * preparePostData, Prepares the additional data to execute the service to execute the query
         * @returns data {object}
         */
        preparePostData: function () {
            var data;
            data = this.jsonData || {};
            if (this.model.get("group") === "grid") {
                data["field_id"] = this.model.get("columnName");
            } else {
                data["field_id"] = this.model.get("id");
            }
            if (this.model.get("form")) {
                if (this.model.get("form").get("form")) {
                    data["dyn_uid"] = this.model.get("form").get("form").get("id");
                } else {
                    data["dyn_uid"] = this.model.get("form").get("id");
                }
            }
            return data;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Suggest", SuggestView);
}());

(function () {
    var LinkView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-link").html()),
        validator: null,
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.model.on("change", this.render, this);
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            this.tagControl = this.tagHiddenToLabel = this.$el.find(".pmdynaform-control-link span");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setText: function (value) {
            if (value !== undefined) {
                this.model.set("text", value);
            }
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : "";
        },
        updateValues: function (value) {
            this.$el.find(".pmdynaform-control-link span").text(value);
            this.model.set("data", {
                value: value,
                label: value
            });
            return this;
        },
        validationURL: function (url) {
            return this.model.validationURL(url);
        },
        reformatURL: function (url) {
            return this.model.reformatURL(url);
        },
        setHref: function (href) {
            if (href !== undefined) {
                this.model.setHref(href);
            }
            return this;
        },
        setValue: function (value) {
            return this.setHref(value);
        },
        getValue: function () {
            return this.model.getValue();
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Link", LinkView);
}());

(function () {
    var Label = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-label").html()),
        template_showLabels: _.template($("#tpl-label-fullOptions").html()),
        validator: null,
        singleControl: [],
        fieldValid: [],
        formulaFieldsAssociated: [],
        initialize: function () {
            this.formulaFieldsAssociated = [];
            this.specialHiddens = ["dropdown", "radio", "suggest", "checkbox", "datetime"];
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:fullOptions", this.updateViewControls, this);
            this.model.on("change:toDraw", this.render, this);
        },
        /**
         * updateViewControls, redrawing the labels of the component, is call when the fulloptions is change
         * @chainable
         */
        updateViewControls: function () {
            var labelSpan,
                valueHidden = this.$el.find(".value-hidden"),
                labelHidden = this.$el.find(".label-hidden"),
                data = this.model.get("data"),
                originalType = this.model.get("originalType"),
                formControlContent = this.$el.find(".form-control");

            labelSpan = this.$el.find(".label-" + originalType);
            labelSpan.remove();
            labelSpan = this.template_showLabels({
                originalType: originalType,
                fullOptions: this.model.get("fullOptions")
            });
            formControlContent.prepend(labelSpan);
            this.model.get("fullOptions");
            valueHidden.val(data["value"]);
            labelHidden.val(data["label"]);
            return this;
        },
        eventListener: function (event) {
            this.onChange(event);
            this.onFieldAssociatedHandler();
            return this;
        },
        onChange: function (event) {
            var data;
            data = this.model.get("data");
            if (data["label"] !== undefined && data["label"] !== null) {
                this.model.setFullOptions(data["label"]);
            }
            return this;
        },
        /**
         * Set the default option or first option.
         * @returns {Label}
         */
        setFirstOption: function () {
            var data = this.model.get("data"),
                optionsArray = this.model.get("options"),
                defaultValue = this.model.get("defaultValue"),
                fullOptions = this.model.get("fullOptions"),
                optionDefault = data,
                auxiliarOption,
                index = 0;
            if (fullOptions.length && !fullOptions[index]) {
                if (_.isArray(optionsArray) && optionsArray.length) {
                    if (defaultValue) {
                        auxiliarOption = this.model.findOption(defaultValue, "value");
                        optionDefault = {
                            value: auxiliarOption.value,
                            label: auxiliarOption.label
                        };
                    } else {
                        optionDefault = {
                            value: optionsArray[index].value,
                            label: optionsArray[index].label
                        }
                    }
                }
                this.model.set({"data": optionDefault}, {silent: true});
                this.model.set({"value": optionDefault.value}, {silent: true});
                this.model.set("fullOptions", [optionDefault.label]);
            }
            return this;
        },
        render: function () {
            var hidden, name, newDateTime, $textAreaContent, msie;
            this.setFirstOption();
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            if (this.model.get("originalType") === "datetime") {
                newDateTime = this.renderDataTimeViewMode();
            }
            this.setDataInHiddenControls(this.model.get("originalType"));
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("]");
                hidden.name = hidden.id = "form" + name;

                hidden.name = hidden.id = "form" + name;
                hidden = this.$el.find("input[type = 'hidden']")[1];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
            } else {
                this.onFormula();
            }
            this.tagControl = this.$el.find("input[type='hidden']").eq(0);
            this.keyLabelControl = this.$el.find("input[type='hidden']").eq(1);
            if (newDateTime) {
                this.keyLabelControl.val(newDateTime);
                this.model.set('keyValue', newDateTime);
            }

            $textAreaContent = this.$el.find("span.label-textarea");
            if ($textAreaContent.length) {
                $textAreaContent.html(this.model.get('value').replace(/(?:\r\n|\r|\n)/g, "<br />"));
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);

            return this;
        },
        /**
         * render datetime field at view mode
         *
         **/
        renderDataTimeViewMode: function () {
            var formatedText = '', data;
            data = this.model.get('data');
            if (this.model.get("format") && data['value']) {
                formatedText = moment(data['value']).format(this.model.get('format'));
            } else {
                formatedText = this.model.get("data")['label'];
            }
            this.$el.find(".label-" + this.model.get("originalType")).text(formatedText);
            return formatedText;
        },
        setDataInHiddenControls: function (type) {
            type = (type && type.trim()) || null;
            if (type && this.specialHiddens.indexOf(type) !== -1) {
                switch (type) {
                    case 'suggest':
                    case 'dropdown':
                    case 'radio':
                    case 'datetime':
                        this.$el.find("input[type='hidden']")[1].value = this.model.get("data")["label"];
                        break;
                }
                this.$el.find("input[type='hidden']")[0].value = this.model.get("data")["value"];
            }
            return this;
        },
        /**
         * Executes formula associated
         * @returns {Label}
         */
        onFieldAssociatedHandler: function () {
            var i,
                fieldsAssoc = this.formulaFieldsAssociated;
            if (fieldsAssoc.length > 0) {
                for (i = 0; i < fieldsAssoc.length; i += 1) {
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
                    control.on(e, function (event) {
                        fn(event, that);
                        event.stopPropagation();
                    });
                } else {
                    throw new Error("Is not possible find the HTMLElement associated to field");
                }
            }
            return this;
        },
        /**
         * Validates fields supported on formula
         * @param fields
         * @returns {{}}
         */
        checkFieldsValidForFormula: function (fields) {
            var validFields = ["text", "label", "dropdown", "suggest", "textarea", "radio"],
                responseObject = {},
                itemField,
                i;
            for (i = 0; i < fields.length; i += 1) {
                itemField = fields[i];
                if (validFields.indexOf(itemField.model.get("type")) > -1) {
                    responseObject[itemField.model.get("id")] = itemField;
                }
            }
            return responseObject;
        },
        /**
         * Event to process formula
         * @param rows
         * @returns {Label}
         */
        onFormula: function (rows) {
            var fieldsList,
                that = this,
                allFieldsView,
                formulaField = this.model.get("formula"),
                idFields = {},
                fieldFormula,
                fieldAdded = [],
                group = this.model.get("group");
            fieldsList = group === "grid" ? rows : this.parent.items;
            allFieldsView = (fieldsList instanceof Array) ? fieldsList : fieldsList.asArray();
            idFields = this.checkFieldsValidForFormula(allFieldsView);
            fieldFormula = formulaField.replace(/\s/g, '').split(/[\-(,|+*/\)]+/);
            if (this.model.get("group") === "grid") {
                for (var k = 0; k < rows.length; k += 1) {
                    if (fieldFormula.indexOf(rows[k].model.get("id")) > -1) {
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
            for (var obj = 0; obj < this.fieldValid.length; obj += 1) {
                this.model.addFormulaFieldName(this.fieldValid[obj]);
                idFields[this.fieldValid[obj]].formulaFieldsAssociated.push(that);
                if (group === "grid") {
                    if (idFields.hasOwnProperty(this.fieldValid[obj])) {
                        this.model.attributes.formulaAssociatedObject.push(idFields[this.fieldValid[obj]]);
                    }
                }
            }
            return this;
        },
        getValue: function () {
            return this.model.getValue();
        },
        setValue: function (value) {
            this.model.setValue(value);
            return this;
        },
        setText: function (text) {
            var originalType = this.model.get("originalType"),
                newData;
            switch (originalType) {
                case "text":
                case "textarea":
                    newData = this.model.getTextBoxData(text);
                    break;
                case "suggest":
                    newData = this.model.getSuggestData(text);
                    break;
                case "dropdown":
                    newData = this.setDropdownText(text);
                    break;
                case "checkgroup":
                    newData = this.setChekgroupTexts(text);
                    break;
                case "checkbox":
                    newData = this.setCheckboxText(text);
                    break;
                case "radio":
                    newData = this.setRadioText(text);
                    break;
                case "datetime":
                    newData = this.model.getDateTimeData(text);
                    break;
            }
            if (newData) {
                this.model.setNewData(newData);
            }
            return this;
        },
        setRadioText: function (text) {
            var data;
            data = this.model.findOption(text, "label");
            return data ? data : {label: "", value: ""};
        },
        setDropdownText: function (text) {
            var data;
            data = this.model.findOption(text, "label");
            return data ? data : {label: "", value: ""};
        },
        setChekgroupTexts: function (texts) {
            var data,
                resultOptions;
            if (_.isArray(texts)) {
                resultOptions = this.model.findOptions(texts, "label");
                data = this.model.returnOptionsData(resultOptions);
            }
            return {
                value: data["values"],
                label: JSON.stringify(data["label"])
            };
        },
        setCheckboxText: function (text) {
            var data;
            data = this.model.findOption(text, "label");
            return data ? data : {label: "", value: ""};
        },
        updateTextData: function (data) {
            if (data && data.hasOwnProperty("value")) {
                this.model.set("data", {
                    value: data["value"],
                    label: data["value"]
                });
                this.model.set("value", data["value"]);
            } else {
                this.model.set("value", "");
                this.model.set("data", {value: "", label: ""});
            }
            return this;
        },
        updateDropdownData: function (data) {
            var options = this.model.get("options");
            if (data === null) {
                if (options.length > 0) {
                    data = {
                        value: options[0].value,
                        label: options[0].label
                    }
                } else {
                    data = {
                        value: "",
                        label: ""
                    }
                }
            }
            this.model.setData(data);
            return this;
        },
        updateCheckgroupData: function (data) {
            if (!_.isArray(data['value']) || data['value'].length === 0) {
                data = {
                    value: [],
                    label: JSON.stringify([])
                }
            }
            this.model.setData(data);
            return this;
        },
        updateRadioData: function (data) {
            var options = this.model.get("options");
            if (!data) {
                data = {
                    value: "",
                    label: ""
                }
            }
            this.model.setData(data);
            return this;
        },
        findDefValueInOptions: function (defVal) {
            var data = {
                    value: "",
                    label: ""
                },
                originalType,
                i,
                items,
                options,
                firstOption = 0,
                option;
            originalType = this.model.get("originalType");
            switch (originalType) {
                case 'checkgroup':
                    items = this.model.findOptions(defVal, "value");
                    if (_.isArray(items)) {
                        data = {
                            value: [],
                            label: []
                        };
                        for (i = 0; i < items.length; i += 1) {
                            data["value"].push(items[i].value);
                            data["label"].push(items[i].label);
                        }
                        data["label"] = JSON.stringify(data["label"]);
                    }
                    break;
                case 'radio':
                    option = this.model.findOption(defVal, "value");
                    if (option) {
                        data = option;
                    }
                    break;
                case 'text':
                case 'textarea':
                case 'dropdown':
                    option = this.model.findOption(defVal, "value");
                    if (option) {
                        data = option;
                    } else {
                        options = this.model.get("options");
                        if (_.isArray(options) && options.length) {
                            data = options[firstOption] || data;
                        }
                    }
                    break;
            }
            return data;
        },
        mergeOptions: function (remoteOptions) {
            var data,
                originalType = this.model.get("originalType") || "";
            if (_.isArray(remoteOptions) && remoteOptions.length) {
                this.model.mergeOptions(remoteOptions);
                data = this.findDefValueInOptions(this.model.get("defaultValue"));
                switch (originalType) {
                    case "text":
                    case "textarea":
                    case "suggest":
                        this.updateTextData(data);
                        break;
                    case "dropdown":
                        this.updateDropdownData(data)
                        break;
                    case "checkgroup":
                        this.updateCheckgroupData(data);
                        break;
                    case "radio":
                        this.updateRadioData(data);
                        break;
                }
            } else {
                this.model.set("data", {value: "", label: ""});
                this.model.set("value", "");
            }
            return this;
        },
        setData: function (data) {
            var value;
            this.model.set("data", data);
            if (this.model.get("originalType") === "suggest") {
                this.setValue(data);
            } else {
                this.setValue(data["value"]);
            }
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data && data["label"] ? data["label"] : null;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Label", Label);
}());

(function () {
    var Title = PMDynaform.view.Field.extend({
        template: null,
        validator: null,
        etiquette: {
            title: _.template($("#tpl-label-title").html()),
            subtitle: _.template($("#tpl-label-subtitle").html())
        },
        tagControl: null,
        tagLabel: null,
        initialize: function () {
            this.template = this.etiquette[this.model.get("type")];
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("type") === "title") {
                this.tagControl = this.$el.find("h4");
            } else {
                this.tagControl = this.$el.find("h5");
            }
            this.tagLabel = this.tagControl.find("p span");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setValue: function (text) {
            if (text !== undefined) {
                this.model.set("label", text);
                this.tagLabel.text(text);
            }
        },
        getText: function () {
            var label = this.model.get("label");
            return label ? label : null;
        },
        getValue: function () {
            return this.model.getValue();
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Title", Title);
}());

(function () {
    var Empty = Backbone.View.extend({
        item: null,
        template: _.template($("#tpl-empty").html()),
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Empty", Empty);

}());
(function () {
    var HiddenView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-hidden").html()),
        onChangeCallback: null,
        initialize: function (options) {
            this.model.on("change:value", this.eventListener, this);
        },
        /**
         *
         * @param fn
         * @returns {HiddenView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Listener change value
         */
        eventListener: function () {
            this.checkBinding();
        },
        /**
         * Execute onchangecallback
         * @returns {HiddenView}
         */
        checkBinding: function () {
            var form = this.parent;
            if (typeof this.onChangeCallback === "function") {
                this.onChangeCallback(this.getValue(), this.model.previous("value"));
            }
            if (form && typeof form.onChangeCallback === "function") {
                form.onChangeCallback(this.model.get("id"), this.getValue(), this.model.previous("value"));
            }
            return this;
        },
        render: function (isConsole) {
            var data = {},
                hidden;
            if (isConsole) {
                data["value"] = this.model.get("value");
                data["label"] = this.model.get("value");
                this.model.attributes.data = data;
            }
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("group") === "grid") {
                hidden = this.$el.find("input[type = 'hidden']")[1];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                hidden.name = hidden.id = "form" + name;
                hidden.value = this.model.get("value");
            }
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='hidden']").attr("name", "");
            }
            this.tagControl = this.$el.find("input[type='hidden']").eq(0);
            this.keyLabelControl = this.$el.find("input[type='hidden']").eq(1);
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']").eq(1);
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setValue: function (value) {
            if (value !== undefined) {
                this.model.set("value", value);
                this.updateValues(value);
            }
            return this;
        },
        updateValues: function (value) {
            this.tagControl.val(value);
            this.model.set("data", {value: value, label: value});
            this.$el.find("input[type='hidden']").eq(1).val(value);
            return this;
        },
        getText: function () {
            var data = this.model.get("data");
            return data? data["label"] : null;
        },
        getValue: function () {
            return this.model.getValue();
        },
        getControl: function () {
            var htmlControl = this.$el.find("input[type='hidden']").eq(0);
            return htmlControl;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Hidden", HiddenView);

}());

(function () {
    var ImageView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-image").html()),
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
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            this.tagControl = this.tagHiddenToLabel = this.$el.find("img");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        getSrc: function () {
            return this.model.get("src");
        },
        setSrc: function (value) {
            this.model.set("src", value);
            this.tagControl.attr("src", value);
            return this;
        },
        setValue: function (phat) {
            if (phat !== undefined) {
                this.setSrc(phat);
                this.model.attributes.value = phat;
                this.model.set("data", {
                    value: phat,
                    label: phat
                });
            }
            return this;
        },
        getText: function () {
            return this.getSrc();
        },
        getValue: function () {
            return this.model.getValue();
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.Image", ImageView);

}());

(function () {
    var SubFormView = Backbone.View.extend({
        template: _.template($('#tpl-form').html()),
        formView: null,
        defaultElement: "empty",
        availableElements: null,
        parent: null,
        /**
         * Sets Onchange function of the subForm.
         * @param callback
         * @returns {SubFormView}
         */
        setOnChange: function (callback) {
            this.formView.setOnChange(callback);
            return this;
        },
        /**
         * Initialize sproperties
         * @param options
         */
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
                "panel",
                "videomobile",
                "audiomobile",
                "imagemobile",
                "signature",
                "scannercode",
                "multiplefile",
                "location"
            ];
            this.availableElements = availableElements;
            if (options.project) {
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
                for (i = 0; i < json.items.length; i += 1) {
                    row = [];
                    for (j = 0; j < json.items[i].length; j += 1) {
                        if (json.items[i][j].type) {
                            if ($.inArray(json.items[i][j].type.toLowerCase(), this.availableElements) >= 0) {
                                row.push(json.items[i][j]);
                            }
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
        makeSubForm: function () {
            var panelmodel = new PMDynaform.model.FormPanel(this.model.get("modelForm"));
            this.model.set("formModel", panelmodel);
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
            return this.formView.items.asArray();
        },
        /**
         * Returns all the fields in the subformform including the ones in any nested subform.
         * @returns {Array.<T>PMDynaform.view.Field}
         */
        getAllFields: function () {
            var items = this.getItems(),
                subformFields = [],
                fields;

            fields = items.filter(function (i) {
                // The second expression is necessary since Grid for Mobile doesn't inherit from PMDynaform.view.Field.
                if (i instanceof PMDynaform.view.Field
                    || (PMDynaform.view.GridMobile && i instanceof PMDynaform.view.GridMobile)) {
                    return true;
                } else if (i instanceof PMDynaform.view.SubForm) {
                    subformFields = subformFields.concat(i.getAllFields());
                }
                return false;
            });

            return fields.concat(subformFields);
        },
        /**
         * Validate and set the subform valid property.
         * @param event
         * @returns {boolean}
         */
        isValid: function (event) {
            var i,
                formValid = true,
                formItems = this.formView.getFields(),
                item,
                itemField;

            if (_.isArray(formItems)) {
                for (i = 0; i < formItems.length; i += 1) {
                    item = formItems[i];
                    if (item.validate) {
                        if (item.firstLoad) {
                            item.firstLoad = false;
                        }
                        // Validate field
                        item.validate(event);
                        if (!item.model.get("valid")) {
                            formValid = item.model.get("valid");
                            // Save the first field to set the focus, that field must be distinct to grid
                            itemField = (itemField === undefined && item.model.get("type") !== "grid") ? item: itemField;
                        }
                    }
                }
                if (itemField) {
                    itemField.setFocus();
                }
            }
            // Finally set valid value to data model
            this.model.set("valid", formValid);
            return formValid;
        },
        setData: function (data) {
            //using the same method of PMDynaform.view.FormPanel
            this.formView.setData(data);

            return this;
        },
        /**
         * Gets data of the form.
         * @returns {{}}
         */
        getData: function () {
            var formView = this.getFormView(),
                fields = formView.getFields(),
                formData = {},
                item,
                dataField,
                i;
            for (i = 0; i < fields.length; i += 1) {
                item = fields[i];
                if (item.model.get("type") === "grid") {
                    dataField[item.model.get("name")] = item.getData2();
                } else {
                    dataField = item.model.getAppData();
                }
                $.extend(true, formData, dataField);
            }
            return formData;
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.$el.find(".pmdynaform-field-form").append(this.formView.render(true).el);

            return this;
        },
        afterRender: function () {
            this.formView.afterRender();
            return this;
        },
        /**
         * Calls to running formulator method
         * @returns {SubFormView}
         */
        runningFormulator: function () {
            this.formView.runningFormulator();
            return this;
        },
        /**
         * Gets the formView(FormPanel View) related to SubForm.
         * @returns {null}
         */
        getFormView: function () {
            return this.formView;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.SubForm", SubFormView);

}());

(function () {

    var GeoMapView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-map").html()),
        validator: null,
        events: {
            "click .pmdynaform-map-fullscreen button": "applyFullScreen"
        },
        $hiddens: [],
        initialize: function (options) {
            this.$hiddens = [];
            this.form = options.form ? options.form : null;
            var that = this;
        },
        onLoadGeoLocation: function () {
            var appData = this.project.mobileDataControls,
                that = this;
            if (appData && appData[this.model.get("name")]) {
                this.model.set("altitude", appData[this.model.get("name")]["altitude"]);
                this.model.set("latitude", appData[this.model.get("name")]["latitude"]);
                this.model.set("longitude", appData[this.model.get("name")]["longitude"]);
                this.createHiddens(appData[this.model.get("name")]);
            } else {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var pos = position.Geoposition ? position.Geoposition : position;
                        that.model.set("latitude", pos.coords.latitude || 0);
                        that.model.set("longitude", pos.coords.longitude || 0);
                        that.model.set("altitude", 0);
                        that.onLoadLocation();
                    });
                }
            }
            this.onLoadLocation();
            return this;
        },
        onLoadLocation: function () {
            var that = this,
                coords,
                mapOptions,
                map,
                marker,
                canvasHTML = that.$el.find(".pmdynaform-map-canvas")[0];
            coords = new google.maps.LatLng(this.model.get("latitude"), this.model.get("longitude"));
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
            google.maps.event.addListener(marker, 'dragend', function (event) {
                that.model.set("latitude", event.latLng.lat().toFixed(that.model.get("decimals")));
                that.model.set("longitude", event.latLng.lng().toFixed(that.model.get("decimals")));

            });
            this.model.set("marker", marker);

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
            var that = this;

            that.$el.html(that.template(that.model.toJSON()));
            this.loadAsyncData(1000);
            if (this.model.get("fullscreen")) {
                this.fullscreen = new PMDynaform.core.FullScreen({
                    element: this.$el.find(".pmdynaform-map-canvas")[0],
                    onReadyScreen: function () {
                        setTimeout(function () {
                            that.$el.find(".pmdynaform-map-canvas").css("height", $(window).height() + "px");
                        }, 500);
                    },
                    onCancelScreen: function () {
                        setTimeout(function () {
                            that.$el.find(".pmdynaform-map-canvas").css("height", "");
                        }, 500);
                    }
                });
            }
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * Load the map in async mode
         * @param time
         * @returns {GeoMapView}
         */
        loadAsyncData: function (time) {
            var that = this;
            if (_.isNumber(time)) {
                setTimeout(function () {
                    that.onLoadGeoLocation();
                }, time);
            }
            return this;
        },
        /**
         * Create the hiddens input for the field
         * @param obj
         * @returns {GeoMapView}
         */
        createHiddens: function (obj) {
            var prop,
                hidden,
                name;
            if (_.isObject(obj) && _.isArray(this.$hiddens)) {
                for (prop in obj) {
                    if (obj.hasOwnProperty(prop)) {
                        name = this.createNameforHidden(prop);
                        hidden = $("<input>", {name: name, type: "hidden", value: obj[prop]});
                        this.$el.append(hidden);
                        this.$hiddens.push(hidden);
                    }
                }
            }
            return this;
        }
        ,
        /**
         * Create names form the hidden input
         * @param prop
         * @returns {string}
         */
        createNameforHidden: function (prop) {
            var name = "";
            if (_.isString(prop)) {
                name = "form[" + this.model.get("variable") + "]" + "[" + prop + "]";
            }
            return name;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.GeoMap", GeoMapView);
}());

(function () {
    var Annotation = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-annotation").html()),
        tagControl: null,
        tagLabel: null,
        initialize: function () {
            this.model.set("type", "label");
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.tagControl = this.$el.find("span");
            this.tagLabel = this.tagControl.find("p span");
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setValue: function (text) {
            if (text !== undefined) {
                this.model.set("label", text);
                this.tagLabel.text(text);
            }
            return this;
        },
        setText: function (text) {
            this.setValue(text);
        },
        getText: function () {
            var label = this.model.get("label");
            return label ? label : null;
        },
        getValue: function () {
            return this.model.getValue();
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Annotation", Annotation);
}());

/**
 * The Datetime class was developed with the help of DateBootstrap plugin
 */
(function () {
    var DatetimeView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-datetime2").html()),
        validator: null,
        keyPressed: false,
        previousValue: null,
        changedValue: null,
        datepickerObject: null,
        firstLoad: false,
        hasValue: false,
        datetimeIsoFormat: "YYYY-MM-DD HH:mm:ss",
        dateIsoFormat: "YYYY-MM-DD",
        timeFormatRegEx: /[hHmsSaAZ]/,
        oldDate: null,
        navigatorKeys: [37, 38, 39, 40],
        events: {
            "blur input": "onBlurInput",
            "keydown input": "refreshBinding",
            'focus .form-control': 'onFieldFocus'
        },
        outFocus: false,
        /**
         * Initializes properties
         * @param options
         */
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.model.on("change:value", this.eventListener, this);
            this.model.on("change:toDraw", this.render, this);
            this.previousValue = this.getValue();
        },
        /**
         * onBlurInput helper to force to update the hidden with the real data
         * @param event
         */
        onBlurInput: function () {
            if (this.datepickerObject.date()) {
                this.tagHiddenToLabel.val(this.prepareFormatValue(this.datepickerObject.date()));
            }
        },
        /**
         * Listens to change event
         * @param event
         * @param value
         */
        eventListener: function (event, value) {
            this.onChange(event, value);
            this.checkBinding();
        },
        /**
         * Executes onChangeCallback function
         * @returns {DatetimeView}
         */
        checkBinding: function () {
            var form = this.form,
                paramsValue = {
                    idField: this.model.get("id"),
                    current: this.getValue(),
                    previous: this.previousValue
                };
            if (paramsValue.current !== paramsValue.previous) {
                this.onChangeCallback(paramsValue.current, paramsValue.previous);
                if (form) {
                    form.checkBinding(paramsValue);
                }
                this.previousValue = this.getValue();
            }
        },
        /**
         * Default function
         */
        onChangeCallback: function () {
        },
        /**
         * Sets the setOnchange helper function to a property
         * @param fn
         * @returns {DatetimeView}
         */
        setOnChange: function (fn) {
            if (typeof fn === "function") {
                this.onChangeCallback = fn;
            }
            return this;
        },
        /**
         * Updates data and value
         * @param event
         * @param value
         */
        onChange: function (event, value) {
            this.updateValue(value);
            this.validate(event);
        },
        /**
         * Update the value in DOM and update it's model
         * @param event
         * @param value
         * @returns {DatetimeView}
         */
        updateValue: function (value) {
            var newData = this.formatData(value);
            if (newData) {
                this.updateDataModel(newData);
                this.tagHiddenToLabel.val(newData.value);
            }
            return this;
        },
        /**
         * Update the data model with an formatted data whit contains a
         * value and label attributes.
         * @param data
         * @returns {DatetimeView}
         */
        updateDataModel: function (data) {
            if (data) {
                this.model.set({"data": data}, {silent: true});
                this.model.set({"value": data.value}, {silent: true});
                this.$el.find(".content-print").text(data.label);
            }
            return this;
        },
        /**
         * Validate if the field is required or has an RegEx
         * @param event
         * @returns {DatetimeView}
         */
        validate: function (event) {
            this.model.set({validate: true});
            this.model.validate();
            if (this.model.get("enableValidate")) {
                if (this.validator) {
                    this.validator.$el.remove();
                    this.$el.removeClass('has-error');
                }
                if (!this.model.isValid()) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    this.$el.find(".datetime-container").append(this.validator.el);
                    this.applyStyleError();
                }
            } else {
                this.model.attributes.valid = true;
            }
            return this;
        },
        /**
         * prevent default events
         * @param event
         * @returns {DatetimeView}
         */
        refreshBinding: function (event) {
            //Validation for the Submit event
            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.keyPressed = true;
            return this;
        },
        /**
         * Render main method to represent the component in the page
         * @returns {DatetimeView}
         */
        render: function () {
            var hidden,
                name,
                dateInput,
                control,
                defaultDate,
                that = this,
                windowResizeHandler;

            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get("hint") !== "") {
                this.enableTooltip();
            }
            control = this.$el.find('#datetime-container-control');

            windowResizeHandler = function () {
                if (control.data("DateTimePicker")) {
                    control.data("DateTimePicker").hide();
                }
                $(window).off('resize', windowResizeHandler);
            };

            control.datetimepicker({
                format: this.model.get("format"),
                stepping: this.model.get("stepping"),
                useCurrent: this.model.get("useCurrent"),
                collapse: this.model.get("collapse"),
                defaultDate: this.model.get("defaultDate") ? moment(this.model.get("defaultDate")) : false,
                disabledDates: this.model.get("disabledDates"),
                sideBySide: this.model.get("sideBySide"),
                daysOfWeekDisabled: this.model.get("daysOfWeekDisabled"),
                calendarWeeks: this.model.get("calendarWeeks"),
                viewMode: this.model.get("viewMode"),
                toolbarPlacement: this.model.get("toolbarPlacement"),
                showClear: this.model.get("showClear"),
                focusOnShow: true,
                showTodayButton: true,
                ignoreReadonly: true,
                "minDate": this.model.get("minDate").trim().length ? this.model.get("minDate") : false,
                "maxDate": this.model.get("maxDate").trim().length ? this.model.get("maxDate") : false
            }).on('dp.show', function () {
                if (that.project) {
                    that.project.hideCalendars(this);
                }
                that.recalculateWidgetPosition();
                $(window).on('resize', windowResizeHandler);
            }).on('dp.hide', function () {
                that.$el.find('input.form-control').eq(0).blur();
            }).on('dp.change', function (event) {
                that.dpOnChange(event.date);
            }).find('.form-control').attr('readonly', this.project.isMobile());
            this.datepickerObject = this.$el.find('#datetime-container-control').data()["DateTimePicker"];
            this.tagControl = this.$el.find("input[type='text']");
            this.tagHiddenToLabel = this.$el.find("input[type='hidden']");
            this.valueReader();
            if (this.model.get("group") === "grid") {
                dateInput = this.$el.find("input[type = 'text']")[0];
                name = this.model.get("name");
                name = name.substring(0, name.length - 1).concat("_label]");
                dateInput.name = dateInput.id = "form" + name;
            }
            if (this.model.get("name").trim().length === 0) {
                this.$el.find("input[type='text']").attr("name", "");
                this.$el.find("input[type='hidden']").attr("name", "");
            }

            this.keysNavigatorDefineEvents();
            this.$el.find(".content-print").text(this.model.get("data")["label"]);
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * Prepare the value because many formats is allowed in Datetime field
         * suported types: "08102017", "2017-10-23" or "2017-10-23 04:00:00"
         * @returns {DatetimeView}
         */
        valueReader: function () {
            var value = this.model.get("value"),
                data = this.model.get("data"),
                formatedDate;
            // I use the value instead of data because in an specific format
            // "dddd, Do-MMM-YYYY", the data property arrives empty from the server.
            // Data is not reliable.
            if (value) {
                // Need formated the value because from server arrives in the next way
                // "08102017" or
                // "2017-10-23" or
                // "2017-10-23 04:00:00"
                formatedDate = moment(value, [
                    this.datetimeIsoFormat,
                    this.dateIsoFormat,
                    this.model.get("format")
                ]);

                if (formatedDate.isValid()) {
                    this.setValue(this.prepareFormatValue(formatedDate));
                    this.tagHiddenToLabel.val(this.prepareFormatValue(formatedDate));
                }
            }
        },

        /**
         * Datetime picker onChange handler.
         * Set The changed value to datetime model.
         * @param date
         * @returns {DatetimeView}
         */
        dpOnChange: function (date) {
            this.model.set("value", date ? this.prepareFormatValue(date) : "");
            return this;
        },
        /**
         * Prepares the value in the required format.
         * if has time we will use "datetimeIsoFormat" format
         * if has not we will use "dateIsoFormat" format
         * @param userValue
         * @returns {*}
         */
        prepareFormatValue: function (userValue) {
            return !this.timeFormatRegEx.test(this.model.get('format')) ?
                userValue.format(this.dateIsoFormat) :
                userValue.format(this.datetimeIsoFormat);
        },
        /**
         * navigation event handler Time control
         * only keyboards navigation is controlled, up, down, right and left
         */
        keysNavigatorDefineEvents: function () {
            var that = this;
            if (this.tagControl instanceof jQuery) {
                this.tagControl.keyup(function (e) {
                    if (that.navigatorKeys.indexOf(e.keyCode) > -1) {
                        that.recalculateWidgetPosition();
                    }
                });
            }
            return this;
        },
        updateAttributeDatepicker: function (attribute, value) {
            if (this.datepickerObject && this.datepickerObject[attribute]) {
                this.datepickerObject[attribute](value);
            }
            return this;
        },
        /**
         * Parse the all formats to the user format
         * @param value
         * @returns {{value: string, label: string}}
         */
        formatData: function (value) {
            var originalFormat = this.model.get("format"),
                newData = {
                    value: "",
                    label: ""
                };
            if (value) {
                newData = {
                    value: value,
                    label: moment(value, [
                        this.datetimeIsoFormat,
                        this.dateIsoFormat,
                        originalFormat
                    ]).format(originalFormat)
                };
            }
            return newData;
        },
        /**
         * Sets a valid date, here we validate if is a correct dateTime value
         * @param value
         * @returns {DatetimeView}
         */
        setValue: function (value) {
            if (value !== undefined && value !== null) {
                this.previousValue = this.getValue();
                value === "" ? this.clear() :
                    this.datepickerObject.date(this.formatData(value).label);
            }
            return this;
        },

        /**
         * Clear the value and clear the datetimepicker control
         * @returns {DatetimeView}
         */
        clear: function () {
            this.datepickerObject.clear();
            this.tagHiddenToLabel.val("");
            return this;
        },
        /**
         * gets the value of a DatetimePicker
         */
        getValue: function () {
            return this.model.getValue();
        },
        getText: function () {
            var data = this.model.get("data");
            return data ? data["label"] : null;
        },

        /**
         * Recalculate The Wiget Postion
         * @returns {DatetimeView}
         */
        recalculateWidgetPosition: function () {
            var index = 0,
                fixedPosition = 'fixed',
                absolutePosition = 'absolute',
                controlDatePicker = this.$el.find(".datetime-container"),
                widgetCalendar = this.$el.find(".bootstrap-datetimepicker-widget"),
                picker = controlDatePicker.get(index),
                controlSpace = picker ? picker.getBoundingClientRect() : null,
                mainContainer = this.getParentElementForFloatingElements(),
                factorAlign = 2,
                isMobile = this.project.isMobile(),
                widgetRect,
                widgetSpace;

            //widgetRect is read only
            widgetRect = !_.isEmpty(widgetCalendar.get(index)) ? widgetCalendar.get(index).getBoundingClientRect() : null;
            if (widgetRect) {
                // widgetRect' clone
                widgetSpace = {
                    position: fixedPosition,
                    top: widgetRect.top,
                    left: widgetRect.left,
                    height: widgetRect.height
                };
                // Update the arrows's widget
                this.updateArrowsToRTL(widgetCalendar);
                // Check if the available space is short up and down
                if (this.spaceIsShort("up_down", controlSpace, widgetSpace)) {
                    widgetSpace = this.setWidgetPosition(widgetSpace, "top");
                } else {
                    //Check widget's initial position
                    if (this.positionWidgetIsUp(widgetSpace.top, controlSpace.top)) {
                        if (!this.existSpaceAvailable("up", controlSpace, widgetSpace)) {
                            widgetSpace = this.setWidgetPosition(widgetSpace, "under");
                        } else {
                            widgetSpace.top = widgetSpace.top - factorAlign;
                        }
                    } else {
                        if (!this.existSpaceAvailable("down", controlSpace, widgetSpace)) {
                            widgetSpace = this.setWidgetPosition(widgetSpace, "over");
                        } else {
                            widgetSpace.top = widgetSpace.top - factorAlign;
                        }
                    }
                }
                //If is mobile set properties and add scroll
                if (mainContainer && isMobile) {
                    widgetSpace.position = absolutePosition;
                    widgetSpace.top += mainContainer.scrollTop;
                    widgetSpace.left += mainContainer.scrollLeft;
                }
                //Append widget calendar to main container
                if (mainContainer && widgetCalendar.get(index)) {
                    mainContainer.appendChild(widgetCalendar.get(index));
                    this.applyPosition(widgetCalendar, widgetSpace);
                }
            }
            return this;
        },
        /**
         * Check the position widget
         * @param widgetTop
         * @param controlTop
         * @param controlHeight
         * @returns {boolean}
         */
        positionWidgetIsUp: function (widgetTop, controlTop) {
            var flag = false;

            if (controlTop > widgetTop) {
                flag = true;
            }
            return flag;
        },
        /**
         * Check if the available space is short
         * @param controlSpace
         * @param widgetSpace
         */
        spaceIsShort: function (position, controlSpace, widgetSpace) {
            var flag = false,
                defaultZoom = 1,
                factorZoom = window.devicePixelRatio || defaultZoom,
                availableSpace;

            availableSpace = {
                up: controlSpace.top * factorZoom,
                down: ($(window).height() - controlSpace.bottom) * factorZoom
            };

            if (availableSpace.up < widgetSpace.height * factorZoom &&
                availableSpace.down < widgetSpace.height * factorZoom) {
                flag = true;
            }

            return flag;
        },
        /**
         * Check the available space
         * @param position
         * @param controlSpace
         * @param widgetSpace
         * @returns {boolean}
         */
        existSpaceAvailable: function (position, controlSpace, widgetSpace) {
            var flag = false,
                defaultZoom = 1,
                factorZoom = window.devicePixelRatio || defaultZoom,
                availableSpace;

            availableSpace = {
                up: controlSpace.top * factorZoom,
                down: ($(window).height() - controlSpace.bottom) * factorZoom
            };

            switch (position) {
                case "up":
                    if (availableSpace.up >= widgetSpace.height * factorZoom) {
                        flag = true;
                    }
                    break;
                case "down":
                    if (availableSpace.down >= widgetSpace.height * factorZoom) {
                        flag = true;
                    }
                    break;
            }
            return flag;
        },
        /**
         * Apply widget's new positions
         * @param objPos
         */
        applyPosition: function (widgetCalendar, newPositions) {
            widgetCalendar.css({
                position: newPositions.position,
                left: newPositions.left,
                top: newPositions.top,
                height: newPositions.height
            });
            return this;
        },
        /**
         * Sets Widget Position
         * @param widgetSpace
         * @param position TOP, BOTTOM, OVER, UNDER and MIDDLE
         */
        setWidgetPosition: function (widgetSpace, position) {
            var factorPixelBottom = 2,
                factorPixeTop = 6,
                index = 0,
                controlDatePicker = this.$el.find(".datetime-container"),
                controlSpace = controlDatePicker.get(index).getBoundingClientRect(),
                factorAlign,
                factorInverse = -1;

            switch (position) {
                case "top":
                    widgetSpace.top = factorPixelBottom;
                    break;
                case "bottom":
                    widgetSpace.top = $(window).height() - widgetSpace.height - factorPixelBottom;
                    break;
                case "over":
                    widgetSpace.top = widgetSpace.top - (widgetSpace.height + controlSpace.height + factorPixeTop);
                    break;
                case "under":
                    widgetSpace.top = widgetSpace.top + (widgetSpace.height + controlSpace.height + factorPixelBottom);
                    break;
                case "middle":
                    factorAlign = factorPixelBottom + (controlSpace.height / 2 + widgetSpace.height / 2);
                    if (this.positionWidgetIsUp(widgetSpace.top, controlSpace.top)) {
                        widgetSpace.top = widgetSpace.top - (factorAlign * factorInverse);
                    } else {
                        widgetSpace.top = widgetSpace.top - factorAlign;
                    }
                    break;
            }
            return widgetSpace;
        },
        onFieldFocus: function () {
            if (typeof this.onFieldFocusCallback === 'function') {
                this.onFieldFocusCallback();
            }
        },
        isOpen: function () {
            var widget = this.$el.find(".bootstrap-datetimepicker-widget").get(0);
            if (widget) {
                return true;
            }
            return false;
        },
        /**
         * when the project has the RTL variable true value, date arrows component
         * is reversed, so that the slide functionality is reversed
         */
        updateArrowsToRTL: function (widget) {
            var arrowsPrev,
                arrowsNext,
                i = 0,
                classPrev,
                classNext;
            if (widget && widget instanceof jQuery) {
                if (this.project && this.project.isRTL) {
                    arrowsPrev = widget.find(".table-condensed .prev");
                    arrowsNext = widget.find(".table-condensed .next");
                    while (i < arrowsPrev.length && i < arrowsNext.length) {
                        classPrev = arrowsPrev.eq(i).find("span").attr("class");
                        classNext = arrowsNext.eq(i).find("span").attr("class");
                        if (classPrev !== undefined && classNext !== undefined) {
                            arrowsPrev.eq(i).find("span").attr("class", classNext);
                            arrowsNext.eq(i).find("span").attr("class", classPrev);
                            i += 1;
                        }
                    }
                }
            }
            return this;
        },
        /* Returns the element in which floating elements should be appended for this field.
         */
        getParentElementForFloatingElements: function () {
            var text = this.$el.find("input[type='text']"),
                parent = text.closest('.modal').get(0);

            return parent || document.body;
        },
        /**
         * Gets control user for helper function purposes.
         * the result must be an instance of jquery of the HTML control.
         */
        getControl: function () {
            var htmlControl = this.$el.find("input[type='text']");
            return htmlControl;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Datetime", DatetimeView);
}());
(function () {
    var PanelField = PMDynaform.view.Field.extend({
        validator: null,
        template: _.template($("#tpl-panelField").html()),
        initialize: function () {
        },
        render: function () {
            var content,
                footer;
            this.$el.html(this.template(this.model.toJSON()));
            this.$el.find(".panel-body").html(this.model.get("content"));
            footer = $(this.model.get("footerContent"));
            if (footer.length && footer instanceof jQuery) {
                this.$el.find(".panel-footer").append(footer);
            } else {
                this.$el.find(".panel-footer").text(this.model.get("footerContent"));
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.PanelField", PanelField);
}());

/**
 * Class Tooltip
 */
(function () {
    var ToolTipView = Backbone.View.extend({
        trigger: 'manual',
        position: 'bottom',
        title: 'Default Content',
        element: null,
        initialize: function () {
            //TODO: no need params.
        },
        /**
         * Creation of the Tooltip
         * @returns {ToolTipView}
         */
        render: function () {
            if (this.getElement()) {
                this.getElement().tooltip('destroy');
                this.getElement().tooltip({
                    'container': 'body',
                    'trigger': this.trigger,
                    'placement': this.getPosition(),
                    'title': this.getTitle()
                });
            }
            return this;
        },
        /**
         * Show Tooltip
         * @param element
         * @param title
         * @param position
         * @returns {ToolTipView}
         */
        show: function (element, title, position) {
            var tooltipWidget = null;
            this.setPosition(position);
            this.setTitle(title);
            this.setElement(element);
            this.render();
            this.getElement().tooltip('show');
            tooltipWidget = $(".tooltip");
            if (!this.validatePosition(tooltipWidget, position)) {
                this.reposition(tooltipWidget);
                this.getElement().tooltip('show');
            }
            return this;
        },
        /**
         * Hide Tooltip
         * @param element
         * @returns {ToolTipView}
         */
        hide: function (element) {
            var tooltipWidfget = $(".tooltip");
            element.tooltip('hide');
            element.tooltip('destroy');
            if (tooltipWidfget) {
                $(".tooltip").remove();
            }
            this.setElement(null);
            return this;
        },
        /**
         * Set Element of the Tooltip
         * @param element
         * @returns {ToolTipView}
         */
        setElement: function (element) {
            this.element = element ? element : this.element;
            return this;
        },
        /**
         * Set Position Tooltip
         * @param position
         * @returns {ToolTipView}
         */
        setPosition: function (position) {
            this.position = position ? position : this.position;
            return this;
        },
        /**
         * Set Text content of the Tooltip
         * @param title
         * @returns {ToolTipView}
         */
        setTitle: function (title) {
            this.title = title ? title : this.title;
            return this;
        },
        /**
         * Get Position Tooltip
         * @returns {string}
         */
        getPosition: function () {
            return this.position;
        },
        /**
         * Get Text Content Tooltip
         * @returns {string}
         */
        getTitle: function () {
            return this.title;
        },
        /**
         * Get Element of the Tooltip
         * @returns {null}
         */
        getElement: function () {
            return this.element;
        },
        /**
         * Validate Position Tooltip
         * @param element
         * @param position
         * @returns {boolean}
         */
        validatePosition: function (element, position) {
            var isAvailable = false,
                propsElement = [],
                margin = {top: 10, left: 20, bottom: 10, right: 20};

            propsElement = this.getPositionAvailable(element);
            if (propsElement && propsElement.length > 0) {
                switch (position) {
                    case "top":
                        isAvailable = (this.getPositionArray(propsElement, "top") > margin.top &&
                        this.getPositionArray(propsElement, "right") > margin.right &&
                        this.getPositionArray(propsElement, "left") > margin.left) ? true : isAvailable;
                        break;
                    case "bottom":
                        isAvailable = (this.getPositionArray(propsElement, "bottom") > margin.bottom &&
                        this.getPositionArray(propsElement, "right") > margin.right &&
                        this.getPositionArray(propsElement, "left") > margin.left) ? true : isAvailable;
                        break;
                    case "right":
                    case "left":
                        isAvailable = true;
                        break;
                    default:
                        isAvailable = false;
                }
            }
            return isAvailable;
        },
        /**
         * Get Array of Positions of the element
         * @param element
         * @returns {Array}
         */
        getPositionAvailable: function (element) {
            var top,
                right,
                bottom,
                left,
                propsElement,
                widthWindow = $(window).width(),
                heightWindow = $(window).height(),
                position = 'bottom',
                available,
                arrayProps = [],
                prop;

            propsElement = element ? element[0].getBoundingClientRect() : null;
            if (propsElement) {
                available = {
                    top: propsElement.top,
                    right: widthWindow - propsElement.right,
                    bottom: heightWindow - propsElement.bottom,
                    left: propsElement.left
                };
                for (prop in available) {
                    arrayProps.push({
                        'key': prop,
                        'value': available[prop]
                    });
                }
                arrayProps.sort(function (a, b) {
                    return b.value - a.value;
                });
                position = arrayProps[0];
            }

            return arrayProps;
        },
        /**
         * Reposition Tooltip
         * @param element
         * @returns {ToolTipView}
         */
        reposition: function (element) {
            var i,
                max,
                bestPosition = false,
                position,
                arrPositions = [];

            arrPositions = this.getPositionAvailable(element);
            for (i = 0 , max = arrPositions.length; i < max; i += 1) {
                position = arrPositions[i]["key"];
                if (this.validatePosition(element, position)) {
                    bestPosition = true;
                    break;
                }
            }
            this.setPosition(position);
            this.render();
            return this;
        },
        /**
         * Get Value Position of the Array Positions
         * @param arrayProps
         * @param position
         * @returns {*}
         */
        getPositionArray: function (arrayProps, position) {
            var valuePos,
                max,
                object,
                i;

            if (arrayProps && arrayProps.length > 0) {
                for (i = 0, max = arrayProps.length; i < max; i += 1) {
                    object = arrayProps[i];
                    if (object.key === position) {
                        valuePos = object.value;
                        break;
                    }
                }
            }
            return valuePos;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.ToolTipView", ToolTipView);
}());
(function () {
    /**
     * @class PMDynaform.util.ui.FlashMessageModel
     * A message to display for a while.
     *
     * Usage example:
     *
     *      @example
     *        flashModel = new PMDynaform.ui.FlashMessageModel({
	 *			message : "This is a flas message",
	 *			emphasisMessage: "Info",
	 *			startAnimation:5000,
	 *			closable:true,
	 *			type:"danger",
	 *			appendTo:document.body,
	 *			duration:5000
	 *		});
     *
     *
     * @constructor
     * Creates a new instance of the class.
     *
     * @cfg {String} [emphasisMessage=""] The object's emphasisMessage. It can be a single string
     * @cfg {String} [message=""] The object's message. It can be a single string
     * @cfg {Number} [duration=3000] The time in milliseconds the message will be displayed.
     * @cfg {String} [type="info"] The type for the message. Valid values: 'info', 'success', 'error', 'warning'.
     */
    var FlashMessageModel = Backbone.Model.extend({
        defaults: {
            /**
             * The message property sets a simple label that will be displayed in the component
             * @type {String}
             * @readonly
             */
            message: '',
            /**
             * The duration in milliseconds to show the message. Set by the config option
             * and the method.
             * @type {Number}
             * @readonly
             */
            duration: 3000,
            /**
             * The html element's object the message will be displayed in the DOM element
             * @type {HTMLElement}
             */
            appendTo: document.body,
            /**
             * The message's type. Set by the, config option with success, info, warning, danger
             * @type {String}
             * @readonly
             */
            type: 'info',
            /**
             * The duration in milliseconds to start the message. Set by the config option
             * and the method.
             * @type {Number}
             * @readonly
             */
            startAnimation: 1000,
            /**
             * The emphasisMessage property sets a emphasis label that will be displayed in the component
             * @type {String}
             * @readonly
             */
            emphasisMessage: '',
            /**
             * The valid Type 's set by config option with success, info, warning, danger
             * @type {Array}
             */
            validTypes: [],
            /**
             * add scroll of the scroll in the top, when show the flash message
             * @type {Boolean}
             */
            absoluteTop: false,
            closable: false
        },
        /**
         * When creating an instance of a model, you can pass in the initial values of the attributes
         * @param settings: properties with custom values
         * @returns {FlashMessageModel}
         */
        initialize: function (config) {
            this.set("validTypes", ["success", "info", "warning", "danger"]);
            this.on("change:type", this.setType);
            this.on("change:appendTo", this.setAppendTo);
        },
        /**
         * This method, set the type for the message. Valid values: 'info', 'success', 'error', 'warning'.
         * @param {[type]} type [description]
         */
        setType: function (model, type) {
            if (this.get("validTypes").indexOf(type) > -1) {
                this.set("type", type);
            } else {
                this.set("type", "info");
            }
            return this;
        },
        /**
         * The html element's object the message will be displayed in the DOM element
         * @param {[type]} model  : is a object model
         * @param {[type]} parentNode : this a html element container.
         */
        setAppendTo: function (model, parentNode) {
            if (_.isObject(parentNode)) {
                if (parentNode instanceof jQuery || parentNode.ELEMENT_NODE) {
                    this.set("appendTo", parentNode);
                }
            } else {
                this.set("appendTo", document.body);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.ui.FlashMessageModel", FlashMessageModel);
}());
(function () {
    var FileMobile = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-extfile").html()),
        templateAudio: _.template($("#tpl-extaudio").html()),
        templateVideo: _.template($("#tpl-extvideo").html()),
        templateMediaVideo: _.template($("#tpl-media-video").html()),
        templateMediaAudio: _.template($("#tpl-media-audio").html()),
        templateImage: _.template($("#tpl-extfile").html()),
        templatePlusImage: _.template($("#tpl-extfile-plus-image").html()),
        templatePlusAudio: _.template($("#tpl-extfile-plus-audio").html()),
        templatePlusVideo: _.template($("#tpl-extfile-plus-video").html()),
        templateRenderingWeb: _.template($("#tpl-multimedia-renderingWeb").html()),
        boxPlus: null,
        viewsFiles: [],
        mediaVideos: [],
        validator: null,
        messageRequired: "This field is required",
        events: {
            "click buttonImage": "onClickButtonMobile"
        },
        initialize: function () {
            return this;
        },
        /**
         * Listen OnclickEvent for Mobile Controls
         * @param event
         * @returns {FileMobile}
         */
        onClickButtonMobile: function (event) {
            var type = this.model.get("type");
            switch (type) {
                case "imageMobile":
                    this.onClickButtonImage(event);
                    break;
                case "audioMobile":
                    this.onClickButtonAudio(event);
                    break;
                case "videoMobile":
                    this.onClickButtonVideo(event);
                    break;
            }
            event.preventDefault();
            event.stopPropagation();
            return this;
        },
        /**
         * Listen OnclickEvent for Image Control
         * @param event
         * @returns {FileMobile}
         */
        onClickButtonImage: function (event) {
            var respData,
                project = this.model.get("project");
            respData = {
                idField: this.model.get("name"),
                docUid: this.model.get("inp_doc_uid"),
                type: "image",
                galleryEnabled: this.model.get("galleryEnabled"),
                cameraEnabled: this.model.get("cameraEnabled")
            };
            project.requestManager.getImage(respData);
            return this;
        },
        /**
         * Listen OnclickEvent for Audio Control
         * @param event
         * @returns {FileMobile}
         */
        onClickButtonAudio: function (event) {
            var respData;
            respData = {
                idField: this.model.get("name"),
                docUid: this.model.get("inp_doc_uid"),
                type: "audio"
            };
            this.model.get("project").requestManager.getAudio(respData);
            return this;
        },
        /**
         * Listen OnclickEvent for Video Control
         * @param event
         * @returns {FileMobile}
         */
        onClickButtonVideo: function (event) {
            var respData;
            respData = {
                idField: this.model.get("name"),
                docUid: this.model.get("inp_doc_uid"),
                type: "video"
            };
            this.model.get("project").requestManager.getVideo(respData);
            return this;
        },
        /**
         * Validate a File Mobile Controls
         * @returns {FileMobile}
         */
        validate: function () {
            if (this.validator) {
                this.validator.$el.remove();
                if (_.isFunction(this.removeStyleError)) {
                    this.removeStyleError();
                }
            }

            this.model.validate();
            if (!this.model.get("valid")) {
                this.validator = new PMDynaform.view.Validator({
                    model: new Backbone.Model({
                        message: {
                            required: this.model.get("requiredFieldErrorMessage") || this.messageRequired
                        }
                    })
                });
                this.$el.find(".pmdynaform-field-control").append(this.validator.$el);
                if (_.isFunction(this.applyStyleError)) {
                    this.applyStyleError();
                }
            }
            return this;
        },
        /**
         * This function apply style error in this field
         * @returns {FileUpload}
         */
        applyStyleError: function () {
            this.$el.addClass("has-error has-feedback");
            this.$el.find(".pmdynaform-file-droparea-ext").addClass("file-mobile-error");
            return this;
        },
        /**
         * THis function remove style error in this field
         * @returns {FileUpload}
         */
        removeStyleError: function () {
            this.$el.removeClass('has-error has-feedback');
            this.$el.find(".pmdynaform-file-droparea-ext").removeClass("file-mobile-error");
            return this;
        },
        render: function () {
            var dataFiles,
                nameField;

            if (PMDynaform.core.ProjectMobile) {
                this.createBoxPlus();
                this.$el.html(this.template(this.model.toJSON()));
                if (this.model.get("hint")) {
                    this.enableTooltip();
                }
                this.$el.find(".pmdynaform-file-droparea-ext").append(this.boxPlus);
            } else {
                this.$el.html(this.template(this.model.toJSON()));
                dataFiles = this.project.mobileDataControls;
                nameField = this.model.get("name");
                if (dataFiles.hasOwnProperty(nameField)) {
                    this.renderingWeb(dataFiles[nameField]);
                }
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * controls renders video, audio and image, according to the values
         * obtained from the mobile version.
         * @param items, is the list of the elements multimedia.
         * @returns {HTMLElement}
         */
        renderingWeb: function (items) {
            var ieVersion,
                type = this.model.get("type"),
                i,
                viewFiles,
                elements = [],
                container = this.$el.find(".pmdynaform-file-control"),
                downloadLink,
                data,
                linkService = "showDocument";
            if (_.isArray(items)) {
                container.empty();
                ieVersion = PMDynaform.core.Utils.checkValidIEVersion();
                if (type === "imageMobile") {
                    elements = this.model.remoteProxyData(items);
                } else {
                    for (i = 0; i < items.length; i += 1) {
                        data = {
                            uid: items[i],
                            type: linkService
                        };
                        downloadLink = this.project.webServiceManager.showDocument(data);
                        data = $.extend(true, this.model.urlFileStreaming(items[i]), {downloadLink: downloadLink});
                        elements.push(data);
                    }
                }
                viewFiles = this.templateRenderingWeb({
                    elements: elements,
                    type: type,
                    ieVersion: ieVersion
                });
                container.append(viewFiles);
            }
        },
        setFilesRFC: function (arrayFiles) {
            var type = this.model.get("type");
            switch (type) {
                case "imageMobile":
                    this.loadMixingSourceImages(arrayFiles);
                    break;
                case "audioMobile":
                case "videoMobile":
                    this.loadMixingSourceMedia(arrayFiles);
                    break;
            }
        },
        loadMixingSourceImages: function (arrayFiles) {
            var max = arrayFiles.length ? arrayFiles.length : 0,
                i,
                response;

            if (max) {
                response = this.model.remoteProxyData(arrayFiles);
            }
            if (response && response.length) {
                for (i = 0; i < response.length; i += 1) {
                    this.updateFiles(response[i]);
                }
            }
        },
        loadMixingSourceMedia: function (arrayFiles) {
            var itemMedia,
                item;
            for (var i = 0; i < arrayFiles.length; i++) {
                item = arrayFiles[i];
                if (typeof item === "string") {
                    itemMedia = this.model.urlFileStreaming(item);
                    this.createBoxFile(itemMedia);
                    this.model.addItemFile(itemMedia);
                }
                if (item.filePath) {
                    this.createBoxFile(item);
                    this.model.addItemFile(item);
                }
            }
        },
        /**
         * Create box and update array of files
         * @param item
         * @returns {FileMobile}
         */
        updateFiles: function (item) {
            if (_.isObject(item) && !_.isEmpty(item)) {
                this.model.addItemFile(item);
                this.createBoxFile(item);
            }
            return this;
        },
        /**
         * [setFiles Function for set files images, video and audio from a interface to mobile]
         * @param {[type]} arrayFiles [description]
         */
        setFiles: function (arrayFiles) {
            var i;
            for (i = 0; i < arrayFiles.length; i += 1) {
                this.updateFiles(arrayFiles[i]);
            }
            this.validate();
        },
        setData: function (data) {
            this.setFilesRFC(data["value"]);
            return this;
        },
        /**
         * Create Box of Mobile Controls
         * @param file
         */
        createBoxFile: function (file) {
            var type = this.model.get("type");
            switch (type) {
                case "imageMobile":
                    this.createBoxImage(file);
                    break;
                case "audioMobile":
                    this.createBoxAudio(file);
                    break;
                case "videoMobile":
                    this.createBoxVideo(file);
                    break;
            }
        },
        createBoxImage: function (file) {
            var newSrc,
                rand = Math.floor((Math.random() * 100000) + 3),
                template = document.createElement("div"),
                resizeImage = document.createElement("div"),
                preview = document.createElement("span"),
                progress = document.createElement("div");

            if (file["filePath"]) {
                newSrc = file["filePath"];
            }
            if (file["base64"]) {
                newSrc = this.model.makeBase64Image(file["base64"]);
            }

            if (newSrc) {
                template.id = rand;
                template.className = "pmdynaform-file-containerimage";

                resizeImage.className = "pmdynaform-file-resizeimage";
                resizeImage.innerHTML = '<img class="pmdynaform-image-ext" src="' + newSrc + '"><span class="pmdynaform-file-overlay"><span class="pmdynaform-file-updone"></span></span>';
                preview.id = rand;
                preview.className = "pmdynaform-file-preview";
                preview.appendChild(resizeImage);
                progress.id = rand;
                progress.className = "pmdynaform-file-progress";
                progress.innerHTML = "<span></span>";
                template.appendChild(preview);
                template.setAttribute("data-toggle", "modal");
                template.setAttribute("data-target", "#myModal");
                this.viewsFiles.push({
                    "id": file.id,
                    "data": template
                });
                this.$el.find(".pmdynaform-file-droparea-ext").prepend(template);
            }
            return this;
        },
        createBoxAudio: function (file) {
            var model,
                tplContainerAudio,
                tplMediaAudio,
                mediaElement;
            model = {
                src: file.filePath ? file.filePath : file,
                extension: file.extension ? file.extension : null,
                name: file.name
            };

            tplMediaAudio = this.templateMediaAudio(model);
            mediaElement = new PMDynaform.core.MediaElement({
                el: $(tplMediaAudio),
                type: "audio"
            });

            tplContainerAudio = $(this.templateAudio(model));
            tplContainerAudio.find(".pmdynaform-file-resizevideo").append(mediaElement.$el);
            this.$el.find(".pmdynaform-file-droparea-ext").prepend(tplContainerAudio);

            this.viewsFiles.push({
                "id": file.id,
                "data": tplContainerAudio
            });
            return this;
        },
        createBoxVideo: function (file) {
            var model,
                tplContainerVideo,
                tplMediaVideo,
                mediaElement,

                model = {
                    src: file.filePath ? file.filePath : file,
                    name: file.name
                };
            tplMediaVideo = this.templateMediaVideo(model);
            mediaElement = new PMDynaform.core.MediaElement({
                el: $(tplMediaVideo),
                type: "video",
                streaming: file.filePath ? false : true
            });

            tplContainerVideo = $(this.templateVideo(model));
            tplContainerVideo.find(".pmdynaform-file-resizevideo").append(mediaElement.$el);
            this.$el.find(".pmdynaform-file-droparea-ext").prepend(tplContainerVideo);

            this.viewsFiles.push({
                "id": file.id,
                "data": tplContainerVideo
            });
            return this;
        },
        createBoxPlus: function () {
            var type = this.model.get("type");
            switch (type) {
                case "imageMobile":
                    this.boxPlus = $(this.templatePlusImage());
                    break;
                case "audioMobile":
                    this.boxPlus = $(this.templatePlusAudio());
                    break;
                case "videoMobile":
                    this.boxPlus = $(this.templatePlusVideo());
                    break;
                default:
            }
            return this;
        },
        changeID: function (arrayNew) {
            var array = this.model.getFiles(),
                itemNew,
                itemOld;
            for (var i = 0; i < arrayNew.length; i++) {
                itemNew = arrayNew[i];
                for (var j = 0; j < array.length; j++) {
                    itemOld = array[j];
                    if (typeof itemOld === "string") {
                        if (itemNew["idOld"] === itemOld) {
                            itemOld = itemNew["idNew"];
                        }
                    }
                    if (typeof itemOld === "object") {
                        if (itemNew["idOld"] === itemOld["id"]) {
                            itemOld["id"] = itemNew["idNew"];
                        }
                    }
                }
            }
        },
        afterRender: function () {
            var data = this.model.get("data"),
                prj = this.model.get("project");
            if (data && data.value && prj && prj.loadDataField) {
                this.setFilesRFC(data.value);
            }
            return this;
        },
        /**
         * Enable the validation when only property required is true
         * @returns {FileMobile}
         */
        enableValidation: function () {
            if (this.model.get("required")) {
                this.model.set("enableValidate", true);
                this.showRequire();
            }
            return this;
        },
        /**
         * Disable the validation when only property required is true
         * @returns {FileMobile}
         */
        disableValidation: function () {
            if (this.model.get("required")) {
                this.model.set("enableValidate", false);
                if (_.isFunction(this.removeStyleError)) {
                    this.removeStyleError();
                }
                if (this.validator) {
                    this.validator.$el.remove();
                }
                this.hideRequire();
            }
            return this;
        },

        /**
         * Exchange the file ids.
         * @param oldId
         * @param newId
         * @returns {FileMobile}
         */
        exchangeMobileDataId: function (oldId, newId){
            this.model.exchangeMobileDataId(oldId, newId);
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.FileMobile", FileMobile);
}());

(function () {
    var GeoMobile = PMDynaform.view.Field.extend({
        item: null,
        template: _.template($("#tpl-extgeo").html()),
        templatePlus: _.template($("#tpl-extfile-plus").html()),
        templateGeoDesktop: _.template($("#tpl-map").html()),
        boxPlus: null,
        boxModal: null,
        boxBackground: null,
        viewsImages: [],
        imageOffLine: "geoMap.jpg",
        events: {
            "click button": "onClickButton"
        },
        initialize: function () {
        },
        onClickButton: function (event) {
            var respData;
            this.model.set("interactive", true);
            respData = {
                idField: this.model.get("name"),
                interactive: true
            };
            this.model.get("project").requestManager.getLocation(respData);
            event.preventDefault();
            event.stopPropagation();
            return this;
        },
        makeBase64Image: function (base64) {
            return "data:image/png;base64," + base64;
        },
        /**
         * Create Box Geo Map
         * @param data
         * @returns {GeoMobile}
         */
        createBox: function (data) {
            var rand,
                newsrc,
                template,
                resizeImage,
                preview;

            if (data.base64) {
                this.clearBox();
                newsrc = this.makeBase64Image(data.base64);
                rand = Math.floor((Math.random() * 100000) + 3);
                template = document.createElement("div");
                resizeImage = document.createElement("div");
                preview = document.createElement("span");

                template.id = rand;
                template.className = "pmdynaform-file-containergeo";
                resizeImage.className = "pmdynaform-file-resizeimage";
                resizeImage.innerHTML = '<img src="' + newsrc + '">';

                preview.id = rand;
                preview.className = "pmdynaform-file-preview";
                preview.appendChild(resizeImage);
                template.appendChild(preview);
                this.$el.find(".pmdynaform-ext-geo").prepend(template);
                this.hideButton();
            }
            return this;
        },
        /**
         * Clear Box Geo Map
         * @returns {GeoMobile}
         */
        clearBox: function () {
            var htmlBox = this.$el.find(".pmdynaform-ext-geo");
            if (htmlBox.length) {
                htmlBox.empty();
            }
            return this;
        },
        /**
         * Hide Button Map
         */
        hideButton: function () {
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
                that = this,
                data,
                canvasMap,
                coords,
                latitude,
                longitude,
                altitude;
            if (PMDynaform.core.ProjectMobile) {
                this.$el.html(this.template(this.model.toJSON()));
                fileContainer = this.$el.find(".pmdynaform-file-droparea-ext")[0];
                fileControl = this.$el.find("input")[0];
            } else {
                this.$el.html(this.templateGeoDesktop(this.model.toJSON()));
                auxClass = function (params) {
                    this.project = params.project;
                };
                auxClass.prototype.load = function () {
                    canvasMap = that.$el.find(".pmdynaform-map-canvas");
                    coords, mapOptions, map, marker;
                    if (that.project.mobileDataControls) {
                        if (that.project.mobileDataControls) {
                            data = that.project.mobileDataControls[that.model.get("name")];
                            if (data) {
                                latitude = data["latitude"] || 0;
                                longitude = data["longitude"] || 0;
                                altitude = data["altitude"] || 0;
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
                window.pmd = new auxClass({project: this});
                var script = document.createElement('script');
                script.type = 'text/javascript';
                $(script).data("script", "google");
                script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=pmd.load';
                document.body.appendChild(script);
            }
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        /**
         * Set Location
         * @param location
         */
        setLocation: function (location) {
            var newLocation = {},
                imageResponse,
                render;
            if (location && typeof location === "object" && !_.isEmpty(location)) {
                render = location.hasOwnProperty("render") && !location.render? location.render : true;
                newLocation = {
                    idField: location.idField,
                    id: location.id,
                    base64: null,
                    latitude: location.latitude,
                    longitude: location.longitude,
                    altitude: location.altitude
                };
                this.setGeoData(newLocation);
                if (render) {
                    imageResponse = this.model.remoteProxyData(newLocation.id);
                    if (imageResponse) {
                        newLocation.base64 = imageResponse.base64;
                        this.createBox(newLocation);
                    }
                }
            }
        },
        createImageOffLine: function (location) {
            location["filePath"] = this.imageOffLine;
            this.createBox({
                filePath: this.imageOffLine
            });
        },
        setData: function (data) {
            if (data["value"] && data["value"] !== "")
                this.setLocation(data["value"]);
            return this;
        },
        /**
         * Function for after render in dynaforms
         * @returns {GeoMobile}
         */
        afterRender: function () {
            var data = this.model.get("data"),
                prj = this.model.get("project");
            if (data && data.value && prj && prj.loadDataField) {
                this.setLocation(data.value);
            }
            return this;
        },
        /**
         * Get Geo Data from View
         * @returns {*}
         */
        getGeoData: function () {
            return this.model.getGeoData();
        },
        /**
         * Set Geo Data Model From View
         * @param data
         * @returns {GeoMobile}
         */
        setGeoData: function (data) {
            this.model.setGeoData(data);
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.view.GeoMobile", GeoMobile);
}());

(function () {
    var MediaElement = function (settings) {
        this.el = settings.el;
        this.$el = settings.el;
        this.streaming = settings.streaming ? settings.streaming : null;
        this.type = settings.type;
        if (this.type == "video") {
            MediaElement.prototype.initVideo.call(this, this.el);
        }
        if (this.type == "audio") {
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
        video.on('loadedmetadata', function () {

            //set video properties
            element.find('.current').text(timeFormat(0));
            element.find('.duration').text(timeFormat(video[0].duration));
            updateVolume(0, 0.7);

            //start to get video buffering data
            setTimeout(startBuffer, 150);
            //bind video events
        });

        //display video buffering bar
        var startBuffer = function () {
            var that = this;
            var currentBuffer = video[0].buffered.end(0);
            var maxduration = video[0].duration;
            var perc = 100 * currentBuffer / maxduration;
            element.find('.pmdynaform-media-bufferBar').css('width', perc + '%');

            if (currentBuffer < maxduration) {
                setTimeout(startBuffer, 500);
            }
        };

        //display current video play time
        video.on('timeupdate', function () {
            var currentPos = video[0].currentTime;
            var maxduration = video[0].duration;
            var perc = 100 * currentPos / maxduration;
            element.find('.pmdynaform-media-timeBar').css('width', perc + '%');
            element.find('.current').text(timeFormat(currentPos));
        });

        //CONTROLS EVENTS
        //video screen and play button clicked
        video.on('click', function () {
            playpause();
        });
        element.find('.btnPlay').on('click', function () {
            playpause();
        });
        var playpause = function () {
            if (kitKatMode != null) {
                JsInterface.startVideo(video[0].src, "video/mp4");
            } else {
                if (video[0].paused) {
                    element.find('.btnPlay').addClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon glyphicon-pause').removeClass('glyphicon-play');
                    video[0].play();
                }
                else {
                    element.find('.btnPlay').removeClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-pause').addClass('glyphicon glyphicon-play').removeClass('glyphicon-pause');
                    video[0].pause();
                }
            }
        };


        //fullscreen button clicked
        element.find('.btnFS').on('click', function () {
            if ($.isFunction(video[0].webkitEnterFullscreen)) {
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
        element.find('.sound').click(function () {
            video[0].muted = !video[0].muted;
            $(this).toggleClass('muted');
            if (video[0].muted) {
                element.find('.pmdynaform-media-volumeBar').css('width', 0);
            }
            else {
                element.find('.pmdynaform-media-volumeBar').css('width', video[0].volume * 100 + '%');
            }
        });

        //VIDEO EVENTS
        //video canplay event
        video.on('canplay', function () {
            element.find('.loading').fadeOut(100);
        });

        //video canplaythrough event
        //solve Chrome cache issue
        var completeloaded = false;
        video.on('canplaythrough', function () {
            completeloaded = true;
        });

        //video ended event
        video.on('ended', function () {
            element.find('.btnPlay').removeClass('paused');
            video[0].pause();
        });

        //video seeking event
        video.on('seeking', function () {
            //if video fully loaded, ignore loading screen
            if (!completeloaded) {
                element.find('.loading').fadeIn(200);
            }
        });

        //video seeked event
        video.on('seeked', function () {
        });

        //video waiting for more data event
        video.on('waiting', function () {
            element.find('.loading').fadeIn(200);
        });

        //VIDEO PROGRESS BAR
        //when video timebar clicked
        var timeDrag = false;
        /* check for drag event */
        element.find('.pmdynaform-media-progress').on('mousedown', function (e) {
            timeDrag = true;
            updatebar(e.pageX);
        });
        $(document).on('mouseup', function (e) {
            if (timeDrag) {
                timeDrag = false;
                updatebar(e.pageX);
            }
        });
        $(document).on('mousemove', function (e) {
            if (timeDrag) {
                updatebar(e.pageX);
            }
        });
        var updatebar = function (x) {
            var progress = element.find('.pmdynaform-media-progress');

            //calculate drag position
            //and update video currenttime
            //as well as progress bar
            var maxduration = video[0].duration;
            var position = x - progress.offset().left;
            var percentage = 100 * position / progress.width();
            if (percentage > 100) {
                percentage = 100;
            }
            if (percentage < 0) {
                percentage = 0;
            }
            element.find('.pmdynaform-media-timeBar').css('width', percentage + '%');
            video[0].currentTime = maxduration * percentage / 100;
        };

        //VOLUME BAR
        //volume bar event
        var volumeDrag = false;
        element.find('.pmdynaform-media-volume').on('mousedown', function (e) {
            volumeDrag = true;
            video[0].muted = false;
            element.find('.sound').removeClass('muted');
            updateVolume(e.pageX);
        });
        $(document).on('mouseup', function (e) {
            if (volumeDrag) {
                volumeDrag = false;
                updateVolume(e.pageX);
            }
        });
        $(document).on('mousemove', function (e) {
            if (volumeDrag) {
                updateVolume(e.pageX);
            }
        });
        var updateVolume = function (x, vol) {
            var volume = element.find('.pmdynaform-media-volume');
            var percentage;
            //if only volume have specificed
            //then direct update volume
            if (vol) {
                percentage = vol * 100;
            }
            else {
                var position = x - volume.offset().left;
                percentage = 100 * position / volume.width();
            }

            if (percentage > 100) {
                percentage = 100;
            }
            if (percentage < 0) {
                percentage = 0;
            }

            //update volume bar and video volume
            element.find('.pmdynaform-media-volumeBar').css('width', percentage + '%');
            video[0].volume = percentage / 100;

            //change sound icon based on volume
            if (video[0].volume == 0) {
                element.find('.sound').removeClass('sound2').addClass('muted');
            }
            else if (video[0].volume > 0.5) {
                element.find('.sound').removeClass('muted').addClass('sound2');
            }
            else {
                element.find('.sound').removeClass('muted').removeClass('sound2');
            }

        };

        //Time format converter - 00:00
        var timeFormat = function (seconds) {
            var m = Math.floor(seconds / 60) < 10 ? "0" + Math.floor(seconds / 60) : Math.floor(seconds / 60);
            var s = Math.floor(seconds - (m * 60)) < 10 ? "0" + Math.floor(seconds - (m * 60)) : Math.floor(seconds - (m * 60));
            return m + ":" + s;
        };
        this.$el = element;
    };


    MediaElement.prototype.initAudio = function (element) {
        var video = element.find("audio");
        var control = element.find(".pmdynaform-media-control");
        //remove default control when JS loaded
        video[0].removeAttribute("controls");
        element.find('.pmdynaform-media-control').fadeIn(500);
        element.find('.pmdynaform-media-caption').fadeIn(500);

        //before everything get started
        video.on('loadedmetadata', function () {

            //set video properties
            element.find('.current').text(timeFormat(0));
            element.find('.duration').text(timeFormat(video[0].duration));
            updateVolume(0, 0.7);

            //start to get video buffering data
            setTimeout(startBuffer, 150);

            //bind video events
        });

        //display video buffering bar
        var startBuffer = function () {
            var that = this;
            var currentBuffer = video[0].buffered.end(0);
            var maxduration = video[0].duration;
            var perc = 100 * currentBuffer / maxduration;
            element.find('.pmdynaform-media-bufferBar').css('width', perc + '%');

            if (currentBuffer < maxduration) {
                setTimeout(startBuffer, 500);
            }
        };

        //display current video play time
        video.on('timeupdate', function () {
            var currentPos = video[0].currentTime;
            var maxduration = video[0].duration;
            var perc = 100 * currentPos / maxduration;
            element.find('.pmdynaform-media-timeBar').css('width', perc + '%');
            element.find('.current').text(timeFormat(currentPos));
        });

        //CONTROLS EVENTS
        //video screen and play button clicked
        video.on('click', function () {
            playpause();
        });
        element.find('.btnPlay').on('click', function () {
            playpause();
        });
        var playpause = function () {
            if (kitKatMode != null) {
                JsInterface.startVideo(video[0].src, "audio/mp4");
            } else {
                if (video[0].paused || video[0].ended) {
                    element.find('.btnPlay').addClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-play').addClass('glyphicon-pause').removeClass('glyphicon-play');
                    video[0].play();
                }
                else {
                    element.find('.btnPlay').removeClass('paused');
                    element.find('.btnPlay').find('.glyphicon.glyphicon-pause').removeClass('glyphicon-pause').addClass('glyphicon-play');
                    video[0].pause();
                }
            }
        };


        //fullscreen button clicked
        element.find('.btnFS').on('click', function () {
            if ($.isFunction(video[0].webkitEnterFullscreen)) {
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
        element.find('.sound').click(function () {
            video[0].muted = !video[0].muted;
            $(this).toggleClass('muted');
            if (video[0].muted) {
                element.find('.pmdynaform-media-volumeBar').css('width', 0);
            }
            else {
                element.find('.pmdynaform-media-volumeBar').css('width', video[0].volume * 100 + '%');
            }
        });

        //VIDEO EVENTS
        //video canplay event
        video.on('canplay', function () {
            element.find('.loading').fadeOut(100);
        });

        //video canplaythrough event
        //solve Chrome cache issue
        var completeloaded = false;
        video.on('canplaythrough', function () {
            completeloaded = true;
        });

        //video ended event
        video.on('ended', function () {
            element.find('.btnPlay').removeClass('paused');
            video[0].pause();
        });

        //video seeking event
        video.on('seeking', function () {
            //if video fully loaded, ignore loading screen
            if (!completeloaded) {
                element.find('.loading').fadeIn(200);
            }
        });

        //video seeked event
        video.on('seeked', function () {
        });

        //video waiting for more data event
        video.on('waiting', function () {
            element.find('.loading').fadeIn(200);
        });

        //VIDEO PROGRESS BAR
        //when video timebar clicked
        var timeDrag = false;
        /* check for drag event */
        element.find('.pmdynaform-media-progress').on('mousedown', function (e) {
            timeDrag = true;
            updatebar(e.pageX);
        });
        $(document).on('mouseup', function (e) {
            if (timeDrag) {
                timeDrag = false;
                updatebar(e.pageX);
            }
        });
        $(document).on('mousemove', function (e) {
            if (timeDrag) {
                updatebar(e.pageX);
            }
        });
        var updatebar = function (x) {
            var progress = element.find('.pmdynaform-media-progress');

            //calculate drag position
            //and update video currenttime
            //as well as progress bar
            var maxduration = video[0].duration;
            var position = x - progress.offset().left;
            var percentage = 100 * position / progress.width();
            if (percentage > 100) {
                percentage = 100;
            }
            if (percentage < 0) {
                percentage = 0;
            }
            element.find('.pmdynaform-media-timeBar').css('width', percentage + '%');
            video[0].currentTime = maxduration * percentage / 100;
        };

        //VOLUME BAR
        //volume bar event
        var volumeDrag = false;
        element.find('.pmdynaform-media-volume').on('mousedown', function (e) {
            volumeDrag = true;
            video[0].muted = false;
            element.find('.sound').removeClass('muted');
            updateVolume(e.pageX);
        });
        $(document).on('mouseup', function (e) {
            if (volumeDrag) {
                volumeDrag = false;
                updateVolume(e.pageX);
            }
        });
        $(document).on('mousemove', function (e) {
            if (volumeDrag) {
                updateVolume(e.pageX);
            }
        });
        var updateVolume = function (x, vol) {
            var volume = element.find('.pmdynaform-media-volume');
            var percentage;
            //if only volume have specificed
            //then direct update volume
            if (vol) {
                percentage = vol * 100;
            }
            else {
                var position = x - volume.offset().left;
                percentage = 100 * position / volume.width();
            }

            if (percentage > 100) {
                percentage = 100;
            }
            if (percentage < 0) {
                percentage = 0;
            }

            //update volume bar and video volume
            element.find('.pmdynaform-media-volumeBar').css('width', percentage + '%');
            video[0].volume = percentage / 100;

            //change sound icon based on volume
            if (video[0].volume == 0) {
                element.find('.sound').removeClass('sound2').addClass('muted');
            }
            else if (video[0].volume > 0.5) {
                element.find('.sound').removeClass('muted').addClass('sound2');
            }
            else {
                element.find('.sound').removeClass('muted').removeClass('sound2');
            }

        };

        //Time format converter - 00:00
        var timeFormat = function (seconds) {
            var m = Math.floor(seconds / 60) < 10 ? "0" + Math.floor(seconds / 60) : Math.floor(seconds / 60);
            var s = Math.floor(seconds - (m * 60)) < 10 ? "0" + Math.floor(seconds - (m * 60)) : Math.floor(seconds - (m * 60));
            return m + ":" + s;
        };
        this.$el = element;
    };
    PMDynaform.extendNamespace("PMDynaform.core.MediaElement", MediaElement);

}());


(function () {
    var Qrcode_mobile = PMDynaform.view.Field.extend({
        item: null,
        template: _.template($("#tpl-ext-scannercode").html()),
        templatePlus: _.template($("#tpl-extfile-plus").html()),
        templateCode: _.template($("#tpl-ext-scanner-code").html()),
        boxPlus: null,
        boxModal: null,
        boxBackground: null,
        viewsImages: [],
        events: {
            "click button": "onClickButton"
        },
        initialize: function () {
        },
        onClickButton: function (event) {
            var respData = {
                idField: this.model.get("name")
            };

            this.model.get("project").requestManager.getScannerCode(respData);
            event.preventDefault();
            event.stopPropagation();
            return this;
        },
        hideButton: function () {
            var button;
            button = this.$el.find("button");
            button.hide();
        },
        showLabel: function (scannercode) {
            var container;
            container = this.$el.find("scanner").find(".pmdynaform-label-options");
            container.append(this.templateCode({label: scannercode}));
        },
        render: function () {
            var that = this,
                data;
            if (PMDynaform.core.ProjectMobile) {
                this.$el.html(this.template(this.model.toJSON()));
            } else {
                this.$el.html(this.template(this.model.toJSON()));
                if (that.project.mobileDataControls) {
                    if (that.project.mobileDataControls) {
                        data = that.project.mobileDataControls[that.model.get("name")];
                        if (data) {
                            this.setScannerCode(data);
                        }
                    }
                }
                this.$el.find("button").detach();
            }
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setScannerCode: function (scannercode) {
            var model, i;
            model = this.model;
            if (_.isArray(scannercode)) {
                for (i = 0; i < scannercode.length; i += 1) {
                    model.addCode(scannercode[i]);
                    this.showLabel(scannercode[i]);
                }
            } else {
                model.addCode(scannercode.data);
                this.showLabel(scannercode.data);
            }
            return this;
        },
        setData: function (data) {
            this.setScannerCode(data["value"]);
            return this;
        },
        readFileDeviceScanner: function (data) {
            var str;
            $.ajax({
                url: data.data,
                dataType: 'text',
                async: false,
                success: function (data, xhr) {
                    str = data;
                }
            });
            this.setScannerCode({data: str});
            return this;
        },
        /**
         * Function for after render in dynaforms
         * @returns {Qrcode_mobile}
         */
        afterRender: function () {
            var data = this.model.get("data"),
                prj = this.model.get("project");
            if (data && data.value && prj && prj.loadDataField) {
                this.setScannerCode(data.value);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Qrcode_mobile", Qrcode_mobile);
}());

(function () {
    var Signature_mobile = PMDynaform.view.Field.extend({
        item: null,
        template: _.template($("#tpl-ext-signature").html()),
        templatePlus: _.template($("#tpl-extfile-plus").html()),
        viewsImages: [],
        imageOffLine: "geoMap.jpg",
        events: {
            "click button": "onClickButton"
        },
        initialize: function () {

        },
        onClickButton: function (event) {
            var respData;
            this.model.set("interactive", true);
            respData = {
                idField: this.model.get("name")
            };

            this.model.get("project").requestManager.getSignature(respData);
            event.preventDefault();
            event.stopPropagation();
            return this;
        },
        makeBase64Image: function (base64) {
            return "data:image/png;base64," + base64;
        },
        createBox: function (data) {
            var rand,
                newsrc,
                template,
                resizeImage,
                preview,
                progress;

            if (data.filePath) {
                newsrc = data.filePath;
            } else {
                newsrc = this.makeBase64Image(data.base64);
            }
            rand = Math.floor((Math.random() * 100000) + 3);

            template = document.createElement("div"),
                resizeImage = document.createElement("div"),
                preview = document.createElement("span"),
                progress = document.createElement("div");

            template.id = rand;
            template.className = "pmdynaform-file-containergeo";

            resizeImage.className = "pmdynaform-file-resizeimage";
            resizeImage.innerHTML = '<img src="' + newsrc + '">';
            preview.id = rand;
            preview.className = "pmdynaform-file-preview";
            preview.appendChild(resizeImage);
            template.appendChild(preview);
            this.$el.find(".pmdynaform-ext-signature").prepend(template);
            this.hideButton();
            return this;
        },
        hideButton: function () {
            var button;
            button = this.$el.find("button");
            button.hide();
        },
        render: function () {
            var fileContainer,
                fileControl,
                signature,
                itemElement;
            if (PMDynaform.core.ProjectMobile) {
                this.$el.html(this.template(this.model.toJSON()));
                fileContainer = this.$el.find(".pmdynaform-file-droparea-ext")[0];
                fileControl = this.$el.find("input")[0];
            } else {
                this.$el.html(this.template(this.model.toJSON()));
                fileContainer = this.$el.find(".pmdynaform-ext-signature").empty();
                this.$el.find("button").hide();
                if (this.project.mobileDataControls) {
                    signature = this.project.mobileDataControls;
                    if (signature && signature[this.model.get("name")] && signature[this.model.get("name")].length > 0) {
                        signature = signature[this.model.get("name")];
                        signature = this.model.remoteProxyData(signature[0]);
                        itemElement = $("<img src=\"data:image/png;base64," + signature.base64 + "\"class='img-thumbnail' alt='Thumbnail Image'>");
                        fileContainer.append(itemElement);
                    }
                }
            }
            if (this.model.get("hint")) {
                this.enableTooltip();
            }
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        setFiles: function (arrayFiles) {
            for (var i = 0; i < arrayFiles.length; i++) {
                this.createBox(arrayFiles[i]);
                this.model.attributes.files.push(arrayFiles[i]);
            }
        },
        setSignature: function (arraySignature) {
            var i,
                response,
                files = [];
            for (i = 0; i < arraySignature.length; i++) {
                if (typeof arraySignature[i] == "string") {
                    response = this.model.remoteProxyData(arraySignature[i]);
                    this.createBox(response);
                    files.push(response);

                } else {
                    this.createBox(arraySignature[i]);
                    files.push(arraySignature[i]);
                }
            }
            this.model.set("files", files);
        },
        changeID: function (arrayNew) {
            var array = this.model.attributes.files,
                itemNew,
                itemOld;
            for (var i = 0; i < arrayNew.length; i++) {
                itemNew = arrayNew[i];
                for (var j = 0; j < array.length; j++) {
                    itemOld = array[j];
                    if (typeof itemOld === "string") {
                        if (itemNew["idOld"] === itemOld) {
                            itemOld = itemNew["idNew"];
                        }
                    }
                    if (typeof itemOld === "object") {
                        if (itemNew["idOld"] === itemOld["id"]) {
                            itemOld["id"] = itemNew["idNew"];
                        }
                    }
                }
            }
        },
        setData: function (data) {
            this.setSignature(data["value"]);
            return this;
        },
        /**
         * Function for after render in dynaforms
         * @returns {Signature_mobile}
         */
        afterRender: function () {
            var data = this.model.get("data"),
                prj = this.model.get("project");
            if (data && data.value && prj && prj.loadDataField) {
                this.setSignature(data.value);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.view.Signature_mobile", Signature_mobile);
}());

(function () {

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
            requiredFieldErrorMessage: "",
            domain: false,
            options: [],
            factory: {},
            valueDomain: null,
            regExp: null,
            requiredGrid: false,
            haveOptions: [
                "suggest",
                "checkbox",
                "radio",
                "dropdown"
            ]
        },
        initialize: function () {
            var factoryValidator = {
                "text": "requiredText",
                "checkbox": "requiredCheckBox",
                "checkgroup": "requiredCheckGroup",
                "radio": "requiredRadioGroup",
                "dropdown": "requiredDropDown",
                "textarea": "requiredText",
                "datetime": "requiredText",
                "suggest": "requiredText",
                "file": "requiredFile",
                "grid": "requiredGrid"
            };
            this.setFactory(factoryValidator);
            this.checkDomainProperty();
        },
        setFactory: function (obj) {
            this.set("factory", obj);
            return this;
        },
        checkDomainProperty: function () {
            this.attributes.domain = ($.inArray(this.get("type"), this.get("haveOptions")) >= 0) ? true : false;
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
                    if (this.get('fileOnly')['type'] == 'support') {
                        this.set('valid', false);
                        this.set('message', {
                            validator: this.get('fileOnly')['message']
                        });
                        return this;
                    }
                    if (this.get('fileOnly')['type'] == 'size') {
                        this.set('valid', false);
                        this.set('message', {
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
                        validator: this.get("requiredFieldErrorMessage") ||
                            PMDynaform.core.Validators[validator].message
                    });
                    return this;
                }
            }
            if (this.get("type") === "text" || this.get("type") === "textarea") {
                if (this.get("dataType") !== "" && value !== "") {
                    if (PMDynaform.core.Validators[this.get("dataType")] &&
                        PMDynaform.core.Validators[this.get("dataType")].fn(value) === false) {
                        this.set("valid", false);
                        this.set("message", {
                            "validator": PMDynaform.core.Validators[this.get("dataType")].message
                        });
                        return this;
                    }
                }

                if (this.get("maxLength")) {
                    if (PMDynaform.core.Validators.maxLength.fn(value, parseInt(this.get("maxLength"))) === false) {
                        this.set("valid", false);
                        this.set("message", {
                            validator: PMDynaform.core.Validators.maxLength.message + " " + this.get("maxLength") + " characters"
                        });
                        return this;
                    }
                }

                if (this.get("regExp") && this.get("regExp").validate !== "") {
                    regExp = new RegExp(this.get("regExp").validate);
                    if (value.length > 0 && !regExp.test(value)) {
                        this.set("valid", false);
                        this.set("message", {validator: this.get("regExp").message});
                    } else {
                        this.set('valid', true);
                    }
                    return this;
                }
            }
            return this;
        },
        /**
         * verifies that meets validation having at least one row
         * when the grid is required
         * @returns {Validator}
         */
        verifyGrid: function () {
            if (this.get("required")) {
                if (PMDynaform.core.Validators["requiredGrid"].fn(this.get("rowsNumber")) === false) {
                    this.set("valid", false);
                    this.set("message", {
                        validator: this.get("requiredFieldErrorMessage") ||
                            PMDynaform.core.Validators["requiredGrid"].message
                    });
                } else {
                    this.set('valid', true);
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Validator", Validator);

}());
(function () {
    var PanelModel = Backbone.Model.extend({
        defaults: {
            items: [],
            mode: "edit",
            namespace: "pmdynaform",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("form"),
            type: "form",
            onBeforePrintHandler: null,
            onAfterPrintHandler: null,
        },
        getData: function () {
            return {
                type: this.get("type"),
                name: this.get("name"),
                variables: {}
            }
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Panel", PanelModel);

}());
(function() {
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
            inputDocuments: {},
            printable: false,
            project: null,
            /**
             * @param {PMDynaform.util.DependentsFieldManager}, dependentsManager: Dependent field event handler
             */
            dependentsManager: null,
            /**
             * @param {object}, dependencyRelations: Dependent field relation
             */
            dependencyRelations: {},
            /**
             * @param {object}, fields: Fields that belong to this form
             */
            fields: {},
            /**
             * @param {PMDynaform.util.ArrayList}, subForms: Set of subforms
             */
            subForms: null
        },
        getData: function() {
            return {
                type: this.get("type"),
                action: this.get("action"),
                method: this.get("method")
            }
        },
        /**
         * initialize the model of the Form and Reset parameters
         * @chainable
         */
        initialize: function() {
            this.set("dependencyRelations", {});
            this.set("fields", {});
            this.set("subForms", new PMDynaform.util.ArrayList());
            this.set("dependentsManager", null);
            this.set("dependentsManager",
                new PMDynaform.util.DependentsFieldManager({
                    form: this
                }));
            return this;
        },
        /**
         * This method closes this form, stand alone version for mobile
         * @returns {FormPanel}
         */
        close: function() {
            var project = this.get("project"),
                windowMain;
            if (project) {
                if (project.isMobile() && project.requestManager) {
                    project.requestManager.closeForm();
                } else {
                    if (!project.isPreview) {
                        windowMain = this.getMainWindowFrame();
                        windowMain.parent.location.reload(true);
                    }
                }
            }
            return this;
        },
        /**
         * Get the main window
         * @returns {Window}
         */
        getMainWindowFrame: function() {
            var windowMain = window.parent;
            while (windowMain && windowMain.frameElement && windowMain.frameElement["name"] !== "casesFrame") {
                windowMain = windowMain.parent;
            }
            return windowMain;
        },
        /**
         * addField, Register a new field created in this form
         * @chainable
         */
        addField: function(field, key) {
            var fields;
            fields = this.get("fields");
            if (field && key) {
                fields[key] = field;
            }
            return this;
        },
        /**
         * dispachEvents, Executes logged events when a field that has dependents changes its value
         * @chainable
         */
        dispachEvents: function(nameToRegisterEvent, target, data) {
            this.get("dependentsManager").notify({
                registrationName: nameToRegisterEvent,
                target: target,
                data: data
            });
            return this;
        },
        /**
         * addEvent, Records a new dependent field event
         * @chainable
         */
        addEvent: function(field, callback, target) {
            this.get("dependentsManager").addEvent(field, callback, target);
            return this;
        },
        /**
         * detachRegisteredEvents, Disable Events Logged on the Dependent Field Handler
         * @chainable
         */
        detachRegisteredEvents: function(field, target) {
            this.get("dependentsManager").removeEvent(field, target);
            return this;
        },
        /**
         * Retrieves the options when a field is dependent and needs the options for drawing, 
         * this method is currently working on radio type and checkgroup
         */
        loadSqlOptionsInFields: function() {
            var item,
                relations = this.get("dependencyRelations"),
                i,
                dependents,
                dependent,
                optionsSql;
            for (item in relations) {
                if (relations.hasOwnProperty(item)) {
                    dependents = relations[item];
                    for (i = 0; i < dependents.length; i += 1) {
                        dependent = dependents[i];
                        optionsSql = dependent.get("optionsSql");
                        if (!_.isEmpty(dependent.get("sql")) && _.isArray(optionsSql)
                            && _.isEmpty(optionsSql) && typeof dependent.loadRemotesOptions === "function") {
                            dependent.loadRemotesOptions();
                        }
                    }
                }
            }
            return this;
        },
        /**
         * registerNewDependencyRelation, Prepares to Record Dependent Field for a Field That Has Dependents
         * @chainable
         */
        registerNewDependencyRelation: function(name) {
            var relations;
            relations = this.get("dependencyRelations");
            if (!relations.hasOwnProperty(name)) {
                relations[name] = [];
            }
            return this;
        },
        /**
         * registerNewDependent,Registers a new dependent field
         * @chainable
         */
        registerNewDependent: function(dependency, dependent) {
            var relations;
            relations = this.get("dependencyRelations");
            if (relations.hasOwnProperty(dependency)) {
                relations[dependency].push(dependent);
            }
            return this;
        },
        /**
         * setAppData: Sets the data to the form, set _label too
         * @param data {object} Set of valid data for the form
         */
        setAppData: function(data) {
            var dependency,
                key,
                field,
                dataForDependent;
            for (key in data) {
                if (data.hasOwnProperty(key)) {
                    field = this.getField(key);
                    if (field) {
                        if (this.needsValueAndLabelToSetData(field)) {
                            field.setAppData({
                                value: data[key],
                                label: data[key + "_label"]
                            });
                        } else {
                            if (field.isDependent && field.isDependent()) {
                                dataForDependent = this.dependentFilterData(field.get("dependency"), data);
                            }
                            field.setAppData(data[key], dataForDependent);
                        }
                    }
                }
            }
            return this;
        },
        /**
         * needsValueAndLabelToSetData, Evaluates if it is necessary to send data with value and label
         * @param field {object}, the efected field
         * @returns {boolean} result
         */
        needsValueAndLabelToSetData: function(field) {
            var needs = ["suggest", "file", "dropdown"],
                type,
                mode;
            if (field) {
                type = field.get("type");
                mode = field.get("mode");
                type = mode === "view" ? field.get("originalType") : type;
                if (needs.indexOf(type) > -1) {
                    return true;
                }
            }
            return false;
        },

        /**
         * dependentFilterData, If the field is dependent, construct the data by 
         * performing the search of the values in the appdata
         * @param dependency {array}, Values on which it depends
         * @param appData {object}, Set of valid data for the form
         * @returns newAppData {object}
         */
        dependentFilterData: function(dependency, appData) {
            var newAppData = {},
                i,
                item;
            if (_.isArray(dependency)) {
                for (i = 0; i < dependency.length; i += 1) {
                    item = dependency[i];
                    if (appData.hasOwnProperty(item)) {
                        newAppData[item] = appData[item];
                    }
                }
            }
            return newAppData;
        },
        /**
         * getField, Gets a form field from a key, this can be variable, name or id.
         * @param name {string}, Identifier, can be variable, name or id
         * @returns item {object}
         */
        getField: function(name) {
            var fields = this.get("fields"),
                subformModel,
                subForms,
                i,
                item = fields[name] || null;
            for (i = 0; i < this.get("subForms").getSize(); i += 1) {
                subForms = this.get("subForms").get(i);
                subformModel = subForms.model.get("formModel");
                fields = subformModel.get("fields");
                if (fields && fields[name]) {
                    item = fields[name];
                }
            }
            return item;
        },
        /**
         * Validates if a subForm exists into of the mainForm
         * @param id
         * @returns {boolean}
         */
        isSubForm: function (id) {
            var i,
                view,
                response = false,
                subForms = this.get("subForms"),
                size = subForms.getSize();
            if (id) {
                for (i = 0; i < size; i += 1) {
                    view = subForms.get(i);
                    if (view.model.get("id") === id) {
                        response = true;
                        break;
                    }
                }
            }
            return response;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.FormPanel", FormPanel);
}());

(function () {
    var FieldModel = Backbone.Model.extend({
        defaults: {
            colSpan: 12,
            id: PMDynaform.core.Utils.generateID(),
            label: "Untitled",
            name: PMDynaform.core.Utils.generateName(),
            value: "",
            nameGridColum: null,
            text: "",
            data: null,
            _hidden: false,
            /**
             * @param {object}: dataForDependent, Stores data to eject dependent field service
             */
            dataForDependent: {},
            /**
             * toDraw: When this property change, the view is redrawn
             * @member {boolean}
             */
            toDraw: false
        },
        initialize: function (options) {
            this.set("label", this.get("label"));
            this.set("defaultValue", this.get("defaultValue"));
        },
        /**
         * Generate default data object
         * @param defaultValue
         * @param value
         * @param data
         * @returns {{label: string, value: string}|*}
         */
        initData: function (defaultValue, value, data) {
            var auxData,
                auxValue = "0",
                auxLabel = "false",
                options = this.get("options"),
                i;

            if (typeof data === "object") {
                if (data.hasOwnProperty("value") && data["value"] !== "") {
                    if (this.get("optionsToFalse").indexOf(data["value"]) > -1) {
                        auxValue = "0";
                    } else {
                        if (this.get("optionsToTrue").indexOf(data["value"]) > -1) {
                            auxValue = "1";
                        }
                    }
                } else {
                    if (typeof defaultValue === "boolean") {
                        if (this.get("optionsToFalse").indexOf(defaultValue) > -1) {
                            auxValue = "0";
                        } else {
                            if (this.get("optionsToTrue").indexOf(defaultValue) > -1) {
                                auxValue = "1";
                            }
                        }
                    }
                }
            }
            for (i = 0; i < options.length; i += 1) {
                if (options[i].value === auxValue) {
                    auxLabel = options[i].label;
                    break;
                }
            }
            auxData = {
                label: auxLabel,
                value: auxValue
            };
            return auxData;
        },
        defineModelEvents: function () {
            this.on("change:text", this.onChangeText, this);
            this.on("change:options", this.onChangeOptions, this);
            this.on("change:data", this.onChangeData, this);
            return this;
        },
        /**
         * onChangeData: This event is executed by modifying the data property
         * @chainable
         */
        onChangeData: function () {
            this.setDataToSuggest();
            this.executeDependentsEvents();
            return this;
        },
        /**
         * onChangeData: Executes the dependency event if it is registered in the
         * "PMDynaform.util.DependentsFieldManager" in the form
         * @chainable
         */
        executeDependentsEvents: function () {
            var form = this.get("form"),
                name = this.evaluateName(),
                data = this.get("data");
            if (form) {
                form.dispachEvents(this.getNameToRegisterEvent(name), name, data);
            }
            return this;
        },
        /**
         * setDataToSuggest: PMDynaform.model.Suggest type fields are not executed in form-dependent
         * field events, but the dataForDependent property must be updated to be used in suggest
         * @chainable
         */
        setDataToSuggest: function () {
            var form = this.get("form"),
                dependent,
                dependentsRelation,
                dependents,
                i,
                data = {},
                name = this.evaluateName();
            if (this.get("group") === "grid") {
                name = name + ":" + this.attributes.keyEvent;
            }
            if (form) {
                dependentsRelation = this.get("form").get("dependencyRelations") || {};
                if (dependentsRelation.hasOwnProperty(name)) {
                    dependents = dependentsRelation[name];
                    for (i = 0; i < dependents.length; i += 1) {
                        dependent = dependents[i];
                        if (dependent.get("type") === "suggest") {
                            data[this.evaluateName()] = this.get("data")["value"] || "";
                            dependent.set("dataForDependent", data);
                            dependent.set("value", "");
                        }
                    }
                }
            }
            return this;
        },
        /**
         * evaluateName:  Evaluates the valid name either a cell or a regular field
         * @chainable
         */
        evaluateName: function () {
            var parent = this.get("group"),
                name = "";
            if (parent === "form") {
                name = this.get("variable") || this.get("id");
            } else if (parent === "grid") {
                name = this.get("columnName") || this.get("columnId");
            }
            return name;
        },
        /**
         * getNameToRegisterEvent: Creates an identifier to register in the event handler of form-dependent fields
         * @param variable {string}: Is the base name to create a new key
         * @returns {string}, the new key for registered event
         */
        getNameToRegisterEvent: function (variable) {
            var result = "",
                row = this.get("row"),
                parent = this.get("parent");
            if (parent.get("type") === "form") {
                result = variable;
            } else if (parent.get("type") === "grid") {
                result = variable + ":" + this.get("keyEvent");
            }
            return result;
        },
        onChangeValue: function (attrs, item) {
            var data;
            data = this.findOption(item, "value");
            if (data) {
                this.set("data", data);
            } else {
                this.set("data", {value: "", label: ""});
            }
            this.set("text", this.get("data")["label"]);
            return this;
        },
        onChangeText: function (attrs, item) {
            var data;
            data = this.findOption(item, "label");
            if (data) {
                this.set("data", data);
            } else {
                this.set("data", {value: "", label: ""});
            }
            this.set("value", this.get("data")["value"]);
            return this;
        },
        getData: function () {
            return {
                name: this.get("name") ? this.get("name") : "",
                value: this.get("value")
            }
        },
        /**
         * Get Control HTML default
         * @returns {Array}
         */
        getControl: function () {
            var controlHtml = [];
            return controlHtml;
        },
        parseLabel: function () {
            var currentLabel = this.get("label"),
                maxLength = this.get("maxLengthLabel"),
                itemsLabel,
                k,
                parsed = false;

            itemsLabel = currentLabel.split(/\s/g);
            for (k = 0; k < itemsLabel.length; k += 1) {
                if (itemsLabel[k].length > maxLength) {
                    parsed = true;
                }
            }
            if (parsed) {
                this.set("tooltipLabel", currentLabel);
                this.set("label", currentLabel.substr(0, maxLength - 4) + "...");
            }
            return this;
        },
        validate: function (attrs) {
            this.set("value", this.get("value"));
            this.set("label", this.get("label"));
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
                        endPointFixed = endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);
                    }
                }
            }

            return endPointFixed;
        },
        /**
         * The method check all the fields related to the current field based of the variable and
         * set the same value to others. After set the value, all the fields are rendered.
         */
        changeValuesFieldsRelated: function () {
            var i,
                currentValue = this.get("value"),
                fieldsRelated = this.get("fieldsRelated") || [];

            for (i = 0; i < fieldsRelated.length; i += 1) {
                fieldsRelated[i].model.attributes.value = currentValue;
                fieldsRelated[i].model.get("validator").set({
                    valueDomain: this.get("value"),
                    options: fieldsRelated[i].model.attributes.options || [],
                    domain: true
                });
                fieldsRelated[i].model.get("validator").verifyValue();
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
            if (this.get("group") === "grid") {
                data["field_id"] = this.get("columnName");
            } else {
                data["field_id"] = this.get("id");
            }
            if (this.get("form")) {
                if (this.get("form").model.get("form")) {
                    data["dyn_uid"] = this.get("form").get("form").get("id");
                } else {
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
            restClient = new PMDynaform.core.Proxy({
                url: url,
                method: 'POST',
                data: data,
                keys: prj.token,
                successCallback: function (xhr, response) {
                    that.mergeOptions(response);
                }
            });
            this.set("proxy", restClient);
            return this;
        },
        setLocalOptions: function () {
            if (this.get("options")) {
                this.set("localOptions", this.get("options"));
            }
            return this;
        },
        setRemoteOptions: function () {
            if (this.get("remoteOptions")) {
                this.set("remoteOptions", this.get("optionsSql"));
            }
            return this;
        },
        mergeOptionsSql: function () {
            var options = [];
            if (this.get("options") && this.get("optionsSql"))
                options = this.get("localOptions").concat(this.get("optionsSql"));
            this.set("options", options);
            return this;
        },
        /**
         * This function add value from a field in a field formula
         * @param formulator
         * @returns {FieldModel}
         */
        addFormulaTokenAssociated: function (formulator) {
            if (formulator instanceof PMDynaform.core.Formula) {
                formulator.addTokenValue(this.get("id"), this.get("value"));
            }
            return this;
        },
        /**
         * This function update the field with formula
         * @param field
         * @returns {FieldModel}
         */
        updateFormulaValueAssociated: function (field) {
            var resultField = field.model.get("formulator").evaluate();
            field.model.set("value", resultField);
            return this;
        },
        /**
         * findOption(): This method find and return a option in the array options if exist
         * @param value = the filter in the search  "value", "label" or "defaultValue"
         * @param criteria = is the criteria in the find the option should be a "value", "label"
         * @returns {boolean||object}
         */
        findOption: function (value, criteria) {
            var i,
                index = -1,
                options,
                option = null;
            if (_.isArray(this.get("options"))) {
                options = this.get("options").slice(0);
                if (_.isArray(options) && value !== undefined && typeof criteria === "string") {
                    for (i = 0; i < options.length; i += 1) {
                        if (options[i] && (value == options[i][criteria])) {
                            option = _.extend({}, options[i]);
                            index = i;
                            break;
                        }
                    }
                }
            }
            if (option !== null) {
                option['index'] = index;
            }
            return option;
        },
        /**
         * findOptions(): This method find and return multiple options in the array options if exist the values
         * @param values = the filter in the search "value" or  "label"
         * @param criteria = is the criteria in the find the option should be a "value" or "label"
         * @returns {Array}
         */
        findOptions: function (values, criteria) {
            var options = this.get("options"),
                filterOptions = [];
            if (typeof values === "string") {
                values = values.split('|');
            }
            if (_.isArray(values) && _.isArray(options) && typeof criteria === "string") {
                filterOptions = options.filter(function (item) {
                    index = _.find(values, function (num) {
                        return item[criteria] == num;
                    });
                    if (index) {
                        return item;
                    }
                });
            }
            return filterOptions;
        },
        /**
         * returnOptionsData(): This build the data for the multiple options
         * @param options: this options the field
         * @returns {{value: Array, label: Array}}
         */
        returnOptionsData: function (options) {
            var i,
                labels = [],
                values = [],
                options = options || this.get("options");
            if (_.isArray(options)) {
                for (i = 0; i < options.length; i += 1) {
                    values.push(options[i]["value"]);
                    labels.push(options[i]["label"]);
                }
            }
            return {
                value: values,
                label: labels
            }
        },
        /**
         * executeQuery, Run the query through the service
         * @param data {object}, necessary data for execute the query
         * @returns {array}, set of options
         */
        executeQuery: function (data) {
            var resp,
                prj;
            prj = this.get("project");
            resp = prj.webServiceManager.executeQuery(data, this.get("variable") || "", this);
            return resp;
        },
        /**
         * preparePostData, Prepares the additional data to execute the service to execute the query
         * @returns data {object}
         */
        preparePostData: function () {
            var data = {},
                parent = this.get("parent"),
                project = this.get("project");
            if (this.get("group") === "grid") {
                data["field_id"] = this.get("columnName");
            } else {
                data["field_id"] = this.get("id");
            }
            if (project) {
                data["dyn_uid"] = project.getDynUID() || "";
            } else {
                data["dyn_uid"] = "";
            }
            return data;
        },
        /**
         * isDependent, Verify if a field is dependent
         * @returns dependent {boolean}
         */
        isDependent: function () {
            var dependent = false,
                parentDependents = this.get("dependency");
            if (_.isArray(parentDependents) && parentDependents.length > 0) {
                dependent = true;
            }
            return dependent;
        },
        /**
         * formatResponse, Valid query execution service response
         * @param response {array}: a set of options
         * @returns response {array}
         */
        formatResponse: function (response) {
            var k,
                remoteOpt = [];
            if (_.isArray(remoteOpt)) {
                for (k = 0; k < response.length; k += 1) {
                    remoteOpt.push({
                        value: response[k].value,
                        label: response[k].text || response[k].label || ""
                    });
                }
            }
            return remoteOpt;
        },
        canExecuteQuery: function () {
            var sql = this.get('sql'),
                flag = false,
                executeQueryMap = ["database", "datavariable"];
            if (sql && sql.length) {
                flag = true;
            } else {
                //verify by datasource property
                flag = executeQueryMap.indexOf(this.get('datasource').toLowerCase()) > -1 ? true : false;
            }
            return flag;
        },
        getValue: function () {
            var value = "";
            return value;
        },
        /**
         * _dependentFieldEventRegister, if this component depends on another field, the dependence is recorded
         * @param sql {string}, Is the query, of which is made the search of the
         * variables on which this component depends
         */
        _dependentFieldEventRegister: function (sql) {
            var parse,
                result,
                variable,
                dep = [],
                managerDependents,
                that = this,
                form = this.get("form"),
                parent = this.get("parent");
            parse = /\@(?:([\@\%\#\=\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/g;
            while ((result = parse.exec(sql)) !== null) {
                if (_.isArray(result) && result.length) {
                    variable = result[0];
                    variable = variable.substring(2, variable.length);
                    if (parent.get("type") === "grid") {
                        form.registerNewDependencyRelation(variable + ":" + this.attributes.keyEvent);
                        form.registerNewDependent(variable + ":" + this.attributes.keyEvent, this);
                    } else {
                        form.registerNewDependencyRelation(variable);
                        form.registerNewDependent(variable, this);
                    }
                    // verify if variable  is into dep array
                    if (_.indexOf(dep, variable) === -1) {
                        dep.push(variable);
                        if (this.get("type") !== "suggest") {
                            form.addEvent(this.getNameToRegisterEvent(variable), this.dependentHandler, this);
                        }
                    }


                }
            }
            this.set("dependency", dep);
            return this;
        },

        /**
         * Dependent field handler, this callback is instanced for all register event,
         * and runs when the event logged in PMDynaform.util.DependentsFieldManager
         * param info {object}, Is the field information on which this component depends
         */
        dependentHandler: function (info) {
            var data,
                response,
                dependentsManager;
            data = this.buildDataForQuery(info);
            dependentsManager = this.getDependentsManager();
            response = dependentsManager.executeFieldQuery(data, this.get("variable") || "", this);
            this.afterExecuteQuery(response);
            return this;
        },
        /**
         * getDependentsManager, Retrieves form-dependent field handler
         * @returns {PMDynaform.util.DependentsFieldManager}
         */
        getDependentsManager: function () {
            var form;
            form = this.get("form");
            if (form) {
                return this.get("form").get("dependentsManager");
            }
            return null;
        },
        /*
         * buildDataForQuery, Builds the data needed to execute the query correctly
         * @param info {object}: the initial data of the fields on which this component depends
         * @returns dataForDependent {object}
         */
        buildDataForQuery: function (info) {
            var data = {},
                dependency = this.get("dependency"),
                i,
                target = info.target || "",
                dataTarget = info.data || "",
                dataForDependent = this.get("dataForDependent"),
                exist,
                dependencyItem,
                form = this.get("form"),
                parent = this.get("parent");
            if (!_.isEmpty(info)) {
                dataForDependent[target] = info.data.value;
            }
            for (i = 0; i < dependency.length; i += 1) {
                exist = dataForDependent.hasOwnProperty(dependency[i]);
                if (parent.get("type") === "grid") {
                    dependencyItem = parent.findCellInRow(this.get("row"), dependency[i]);
                } else {
                    dependencyItem = form.get("fields")[dependency[i]];
                }
                if (!exist && dependencyItem) {
                    dataForDependent[dependency[i]] = dependencyItem.get("value");
                }
            }
            _.extend(dataForDependent, this.preparePostData());
            return dataForDependent;
        },
        /*
         * Abstract method
         */
        afterExecuteQuery: function (response) {
        },
        /*
         * Abstract method,
         */
        onChangeOptions: function () {
        },
        /* mergeRemoteOptions, merge the options obtained from the query to the
         * service with the local options
         * @chainable
         */
        mergeRemoteOptions: function (remoteOptions) {
            var k,
                remoteOpt = [],
                localOpt = this.get("localOptions") || [],
                options;
            for (k = 0; k < remoteOptions.length; k += 1) {
                remoteOpt.push({
                    value: remoteOptions[k].value,
                    label: remoteOptions[k].text || remoteOptions[k].label || ""
                });
            }
            this.set("optionsSql", remoteOpt);
            options = localOpt.concat(remoteOpt);
            this.set("options", options);
            return this;
        },
        /**
         * Load the remote options by referring to the service
         * Anonymous function
         */
        loadRemotesOptions: function () {
            var data,
                dependentsManager,
                response;
            data = this.buildDataForQuery({});
            dependentsManager = this.getDependentsManager();
            response = dependentsManager.executeFieldQuery(data, this.get("variable") || "", this);
            this.mergeRemoteOptions(response);
            return this;
        },
        /**
         * Retrieves the domain of the component based on
         * the fields on which it depends
         * @param data {object}: Data of the fields on which it depends
         */
        recoreryRemoteOptions: function (data) {
            var dependentsManager = this.getDependentsManager(),
                response;
            if (typeof data === "object" && _.isArray(this.get("options"))) {
                _.extend(data, this.preparePostData());
                response = dependentsManager.executeFieldQuery(data, this.get("variable") || "", this);
                if (_.isArray(response)) {
                    this.mergeRemoteOptions(response);
                }
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (value) {
            var data;
            data = this.findOption(value, "value");
            if (!data) {
                data = {
                    value: value,
                    label: value
                }
            }
            this.set({"data": data}, {silent: true});
            this.set({"value": value}, {silent: true});
            this.set("toDraw", true);
            return this;
        },
        /**
         * Return the name and name_label data
         * @param value
         * @returns {object}
         */
        getAppData: function () {
            var dt = this.get("data") || {},
                data = {};
            data[this.get("name")] = dt.value;
            data[this.get("name") + "_label"] = dt.label;
            return data;
        },
        /**
         * selectedOptions, Select an option from the option set
         * @param criteria {string}, is the search criterion
         * @param values {array}, set of the values
         */
        selectedOptions: function (criteria, values) {
            var options = this.get("options"),
                i,
                validCriteria = ["index", "value", "label"];
            if (_.isArray(options) && validCriteria.indexOf(criteria) > -1) {
                for (i = 0; i < options.length; i += 1) {
                    if (criteria === "index" && values.indexOf(i) > -1) {
                        options[i].selected = true;
                    } else if (values.indexOf(options[i][criteria]) > -1) {
                        options[i].selected = true;
                    } else {
                        options[i].selected = false;
                    }
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Field", FieldModel);
}());

(function () {
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
                "static"
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
            valid: true,
            countHiddenControl: 0,
            addRow: true,
            deleteRow: true,
            variable: "",
            required: false,
            emptyMessage: "No records",
            addRowText: "New",
            hint: "",
            columnFileDelete: {}
        },
        /**
         * initialize the validator object in the grid
         * @returns {GridModel}
         */
        initValidators: function () {
            this.set("validator", new PMDynaform.model.Validator({
                type: this.get("type"),
                required: this.get("required"),
                rowsNumber: this.get("rows"),
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            return this;
        },
        /**
         * verify if the grid is valid
         * @returns {boolean}
         */
        isValid: function () {
            this.validate();
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        /**
         * execute the validation of the validator associated object
         * and evaluate the grid validation
         * @returns {GridModel}
         */
        validate: function () {
            this.get("validator").set("rowsNumber", this.get("rows"));
            this.get("validator").verifyGrid();
            return this;
        },
        initialize: function (options) {
            var pagesize;
            this.set("gridtable",[]);
            options = options || {};
            this.set("totalrow", []);
            if (options["addRow"] === undefined) {
                this.set("addRow", true);
            }
            if (options["deleteRow"] === undefined) {
                this.set("deleteRow", true);
            }
            if (jQuery.isNumeric(this.get("pageSize"))) {
                pagesize = parseInt(this.get("pageSize"), 10);
                if (pagesize < 1) {
                    pagesize = 1;
                    this.set("pager", false);
                }
            } else {
                this.set("pager", false);
            }
            if (!PMDynaform.core.ProjectMobile) {
                this.set("pageSize", pagesize);
            }
            this.set("label", this.get("label"));
            this.on("change:label", this.onChangeLabel, this);
            if (options.project) {
                this.project = options.project;
            }
            if (this.get("variable").trim().length === 0) {
                this.attributes.name = "";
            } else {
                this.attributes.name = this.get("variable");
            }
            this.fixCoutFieldsHidden();
            this.setLayoutGrid();
            this.setPaginationItems();
            this.checkTotalRow();
            this.initValidators();
        },
        /**
         * Changes undefined layout properties to static
         * @param initial
         * @param final
         * @returns {GridModel}
         */
        setLayoutGrid: function () {
            if ($.inArray(this.get("layout"), this.get("layoutOpt")) < 0) {
                this.set("layout", "static");
            }

            return this;
        },
        setPaginationItems: function () {
            var rows = this.get("rows"),
                size = this.get("pageSize"),
                rotate = this.get("paginationRotate"),
                pagerItems;

            pagerItems = Math.ceil(rows / size) ? Math.ceil(rows / size) : 1;

            this.set("paginationRotate", rotate);
            this.set("paginationItems", pagerItems);

            return this;
        },
        checkTotalRow: function () {
            var i;

            loop_total:
                for (i = 0; i < this.attributes.columns.length; i += 1) {
                    if (this.attributes.columns[i].operation) {
                        if (this.attributes.functionOptions[this.attributes.columns[i].operation.toLowerCase()]) {
                            this.attributes.functions = true;
                            break loop_total;
                        }

                    }
                }
            return this;
        },
        applyFunction: function () {
            var i;

            for (i = 0; i < this.attributes.columns.length; i += 1) {
                if (this.attributes.columns[i].operation) {
                    if (this.attributes.functionOptions[this.attributes.columns[i].operation.toLowerCase()]) {
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

            for (i = 0; i < grid.length; i += 1) {
                sum += grid[i][colIndex];
            }

            return sum;
        },
        avgValues: function (colIndex) {
            var i,
                sum = 0,
                grid = this.attributes.gridFunctions;

            for (i = 0; i < grid.length; i += 1) {
                sum += grid[i][colIndex];
            }

            return Math.round((sum / grid.length) * 100) / 100;
        },
        fixCoutFieldsHidden: function () {
            var i, countHiddenControl = 0;
            for (i = 0; i < this.get("columns").length; i += 1) {
                if (this.get("columns")[i].type === "hidden") {
                    countHiddenControl += 1;
                }
            }
            this.set("countHiddenControl", countHiddenControl);
            return this;
        },
        /**
         * Get Array of the Values of each field
         * @returns {{name: *, value: Array}}
         */
        getData: function () {
            var i,
                j,
                cell,
                row,
                dataGrid = [],
                gridTable = this.attributes.view.gridtable,
                max = gridTable.length,
                dataRow,
                data;
            if (this.get("view")) {
                for (i = 0; i < max; i += 1) {
                    dataRow = [];
                    row = gridTable[i];
                    for (j = 0; j < row.length; j += 1) {
                        cell = row[j];
                        dataRow.push(cell.getValue());
                    }
                    dataGrid.push(dataRow);
                }
            }
            data = {
                name: this.get("name"),
                value: dataGrid
            };
            return data;
        },
        /**
         * Get array of the values or an value cell
         * @param row
         * @param col
         * @returns {*}
         */
        getValue: function (row, col) {
            var valueGrid = "",
                gridData = this.getData(),
                values = "";
            if (row && col) {
                if (this.isColumnInRange(col) && this.isRowInRange(row)) {
                    values = gridData ? gridData.value : values;
                    valueGrid = values[row - 1][col - 1];
                }
            } else {
                valueGrid = gridData ? gridData.value : valueGrid;
            }
            return valueGrid;
        },
        /**
         * Validate index col
         * @param col
         * @returns {boolean}
         */
        isColumnInRange: function (col) {
            var valid = false,
                numCols = 0,
                columns = this.get("columns");
            numCols = columns ? columns.length : numCols;
            if (col >= 1 && col <= numCols) {
                valid = true;
            }
            return valid;
        },
        /**
         * Validate index row
         * @param row
         * @returns {boolean}
         */
        isRowInRange: function (row) {
            var valid = false,
                numRows = this.get("rows");
            if (row >= 1 && row <= numRows) {
                valid = true;
            }
            return valid;
        },
        /**
         * add a columnId to array columnFileDelete to know the columns to delete multiple files and update the variable to delete files; return the number of index in the array
         * @returns {number}
         */
        getColumnFileDelete: function (columnId, nameRow) {
            var prop,
                indexCol = -1,
                indexName = -1,
                arrFiles = this.get("columnFileDelete");

            if (!arrFiles.hasOwnProperty(columnId)) {
                arrFiles[columnId] = [];
            }
            for (prop in arrFiles) {
                if (arrFiles.hasOwnProperty(prop)) {
                    indexCol = indexCol + 1;
                    if (prop === columnId) {
                        break;
                    }
                }
            }

            indexName = arrFiles[columnId].indexOf(nameRow);
            if (indexName === -1) {
                if (_.isString(nameRow)) {
                    arrFiles[columnId].push(nameRow);
                    indexName = arrFiles[columnId].indexOf(nameRow);
                }
            }

            return {
                col: indexCol,
                row: indexName
            };
        },
        /**
         * Returns the array hiddens columns json before a number col
         * @param col
         * @returns {Array}
         */
        getHiddensBeforeColumn: function (col) {
            var arrCols = [], index,
                columns = this.get("columns");
            if (_.isNumber(col)) {
                for (index = 0; index < col; index += 1) {
                    if (columns[index].type === "hidden") {
                        arrCols.push(columns[index]);
                    }
                }
            }
            return arrCols;
        },
        /**
         * detachRegisteredEvents, Disable Events Logged on the Dependent Field Handler
         * @chainable
         */
        detachRegisteredEvents: function(row) {
            var i = 0,
                form = this.get("form"),
                cell,
                name;
            if (_.isArray(row) && row.length) {
                row = row[0];
                for (i = 0; i < row.length; i += 1) {
                    cell = row[i];
                    if (cell && form) {
                        name = cell.model.getNameToRegisterEvent(cell.model.get("columnName"));
                        form.detachRegisteredEvents(name, cell.model.get("columnName"));
                    }
                }
            }
            return this;
        },
        /**
         * findCellInRow: Obtains a cell that belongs to a row
         * @param rowIndex {number}: Row position
         * @param cellId {string}: cell original id
         * @returns {PMDynaform.model.Field}
         */
        findCellInRow: function(rowIndex, cellId) {
            var i,
                row = this.get("gridtable")[rowIndex];
            if (_.isArray(row)) {
                for (i = 0; i < row.length; i += 1) {
                    if (row[i].model.get("columnId") === cellId) {
                        return row[i].model;
                    }
                }
            }
            return null;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.GridPanel", GridModel);
}());

(function () {
    var ButtonModel = PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("button"),
            label: "untitled label",
            type: "button",
            namespace: "pmdynaform",
            disabled: false,
            colSpan: 12
        },
        getValue: function () {
            var label = this.get("label");
            return label ? label : "";
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Button", ButtonModel);
}());
(function () {
    var DropdownModel = PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "string",
            defaultValue: "",
            disabled: false,
            executeInit: true,
            group: "form",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("dropdown"),
            label: "untitled label",
            localOptions: [],
            mode: "edit",
            options: [],
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
            columnName: null,
            originalType: null,
            data: {
                value: "",
                label: ""
            },
            itemClicked: false,
            keyLabel: "",
            optionsSql: [],
            enableValidate: true,
            placeholder: "",
            text: "",
            /**
             * this parameter verify if exist placeholder in the component
             */
            therePlaceholder: false,
            /**
             * this property fix the custom placeholder options
             */
            placeholderOption: null,
             /**
             * @param {object}: dependency, Fields on which it depends
             */
            dependency: [],
             /**
             * @param {object}: dataForDependent, Stores data to eject dependent field service
             */
            dataForDependent:{}
        },
        initValidators: function () {
            this.set("validator", new PMDynaform.model.Validator({
                domain: true,
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            return this;
        },
        initialize: function (options) {
            var data;
            this.initValidators();
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            //verify the exist a placeholder an set the therePlaceholder parameter
            this.verifyExistPlaceholder();
            this.setDefaultValue();
            this.set("dataForDependent",{});
            data = this.get("data");
            if (data && data["value"] !== "") {
                this.attributes.value = data["value"];
                this.attributes.keyLabel = data["label"];
            } else {
                this.setDataOfOptions();
            }
            if (this.get("variable") && this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.set("text", data["label"]);
            //create a placeholder option if exist
            if (this.get("therePlaceholder")) {
                this.set("placeholderOption", this.createPlaceHolderOption());
            }
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
        },
        /**
         * Verify if exist placeholder, when exist set paremeter therePlacehodler = true
         * @returns {DropdownModel}
         */
        verifyExistPlaceholder: function () {
            var placeholder = this.get("placeholder"),
                therePlaceholder = false;
            if (typeof placeholder === "string") {
                if (placeholder.trim().length !== 0) {
                    therePlaceholder = true;
                }
            }
            this.set("therePlaceholder", therePlaceholder);
            return this;
        },
        /**
         * setDataOfOptions(): this method set data with the first option if no
         * exist placeholder option
         * @returns {DropdownModel}
         */
        setDataOfOptions: function () {
            var options = this.get("options");
            if (_.isArray(options)) {
                if (options.length && !this.get("therePlaceholder")) {
                    this.set("value", this.get("options")[0]["value"]);
                    this.set("data", {
                        value: this.get("options")[0]["value"],
                        label: this.get("options")[0]["label"]
                    });
                } else {
                    this.set("data", {value: "", label: ""});
                    this.set("value", "");
                }
            }
            return this;
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }
            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value")
                }
            }
        },
        setDefaultValue: function () {
            var options = this.get("options"),
                defaultValue = this.get("defaultValue"),
                arrayDefaults = ["", null, undefined];

            if (arrayDefaults.indexOf(defaultValue) > -1 && options.length > 0) {
                this.set("value", options[0].value);
            }

            return this;
        },
        isValid: function () {
            return this.get("valid");
        },
        validate: function () {
            var valueFixed = this.get("data")["value"];
            this.attributes.value = valueFixed;
            if (this.get("enableValidate")) {
                this.get("validator").set("type", this.get("type"));
                this.get("validator").set("required", this.get("required"));
                this.get("validator").set("value", valueFixed);
                this.get("validator").set("dataType", this.get("dataType"));
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        reviewRemotesOptions: function () {
            var sql;
            if ((this.get("variable") && this.get("variable").trim().length) || this.get("group") == "grid") {
                sql = this.get("sql");
                if (sql) {
                    this.reviewRemoteVariable();
                }
            }
            return this;
        },
        setLocalOptions: function () {
            var item = {};
            if (this.get("options")) {
                this.set("localOptions", this.get("options"));
            }
            return this;
        },
        /**
         * createPlaceHolderOption(), when the property 'therePlaceholder' is true
         * then the placeholder option is created
         * @returns {{}}
         */
        createPlaceHolderOption: function () {
            var option = {};
            option["label"] = this.get("placeholder");
            option["value"] = undefined;
            return option;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * Set value
         * @param value
         * @returns {DropdownModel}
         */
        setValue: function (value) {
            var dataOption,
                criteria = "value";
            if (value !== undefined && value !== null) {
                dataOption = this.findOption(value, criteria);
                if (dataOption && dataOption.hasOwnProperty("value") && dataOption.hasOwnProperty("label")) {
                    this.set("value", value);
                    this.set("data", {
                        value: dataOption.value || "",
                        label: dataOption.label || ""
                    });
                }
            }
            return this;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         */
        afterExecuteQuery: function(response) {
            this.mergeRemoteOptions(response);
            this.setFirstOptionInData();
            return this;
        },
        /**
         * setFirstOptionInData: Sets the first domain option if it exists
         * if there are not domain a default empty data has been setted
         */
        setFirstOptionInData: function() {
            var options = this.get("options"),
                defaultData = {"value": '', "label": ''},
                index = 0;
            if (options.length) {
                this.set('value', (options[index] && options[index].value) || '');
                this.set("data", options[index] || defaultData);
            }
            if (this.get('view')) {
                this.get('view').onChangeData();
                this.get('view').checkBinding();
            }
            return this;
        },
        /**
         * Load the remote options by referring to the service
         * Anonymous function
         */
        loadRemotesOptions: function(){

        },
        /**
         * Retrieves the domain of the component based on 
         * the fields on which it depends
         * @param data {object}: Data of the fields on which it depends
         * @param postRender {boolean}: Checks if the call is after the view exists
         */
        recoreryRemoteOptions: function(data, postRender) {
            var dependentsManager = this.getDependentsManager(),
                response;
            if (postRender) {
                if (typeof data === "object" && _.isArray(this.get("options"))) {
                    _.extend(data, this.preparePostData());
                    if (dependentsManager) {
                        response = dependentsManager.executeFieldQuery(data, this.get("variable") || "", this);
                    }
                    if (_.isArray(response)) {
                        this.mergeRemoteOptions(response);
                    }
                }
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the 
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (data) {
            var newData;
            if (typeof data === "object") {
                newData = data;
            } else {
                newData = this.findOption(data, "value");
                if (!newData) {
                    newData = {
                        value: "",
                        label: ""
                    }
                }
            }
            this.set({"data":newData},{silent: true});
            this.set({"value":newData.value},{silent: true});
            this.set("toDraw", true);
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Dropdown", DropdownModel);

}());

(function () {
    var RadioboxModel = PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("radio"),
            dataType: "string",
            disabled: false,
            defaultValue: "",
            label: "untitled label",
            localOptions: [],
            group: "form",
            hint: "",
            options: [],
            mode: "edit",
            type: "radio",
            readonly: false,
            remoteOptions: [],
            required: false,
            validator: null,
            valid: true,
            variable: "",
            var_uid: null,
            var_name: null,
            variableInfo: {},
            value: "",
            columnName: null,
            originalType: null,
            itemClicked: false,
            keyLabel: "",
            optionsSql: [],
            enableValidate: true,
            data: {
                value: "",
                label: ""
            }
        },
        initialize: function (attrs) {
            var data = this.get("data");
            this.set("label", this.get("label"));
            this.set("dataForDependent", {});
            this.set("validator", new PMDynaform.model.Validator({
                domain: true,
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            this.verifyControl();
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            this.initControl();

            if (data && data["value"].toString()) {
                this.attributes.value = data["value"];
                this.attributes.keyLabel = data["label"];
                this.set("data", data);
            } else {
                this.set("data", {value: "", label: ""});
                this.set("value", "");
            }
            if (this.get("group") === "form") {
                if (this.get("variable").trim().length === 0) {
                    this.attributes.name = this.get("id");
                }
            } else {
                this.attributes.name = this.get("id");
            }
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
        },
        initControl: function () {
            var opts = this.get("options"),
                i,
                newOpts = [],
                itemsSelected = [];

            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
            for (i = 0; i < opts.length; i += 1) {
                if (opts[i].selected) {
                    itemsSelected.push(opts[i].value.toString());
                }
                newOpts.push({
                    label: opts[i].label,
                    value: opts[i].value,
                    selected: opts[i].selected ? true : false
                });
            }

            this.set("options", newOpts);
            this.set("selected", itemsSelected);
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        isValid: function () {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        verifyControl: function () {
            var opts = this.get("options"), i;
            for (i = 0; i < opts.length; i += 1) {
                opts[i].value = opts[i].value.toString();
            }
            this.set("value", this.get("value").toString());
        },
        validate: function (attrs) {
            if (this.get("enableValidate")) {
                if (attrs) {
                    this.get("validator").set("type", attrs.type);
                    this.get("validator").set("value", attrs.value);
                    this.get("validator").set("valueDomain", attrs.value);
                    this.get("validator").set("required", attrs.required);
                    this.get("validator").set("dataType", attrs.dataType);
                }
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        setItemClicked: function (itemUpdated) {
            var opts = this.get("options"),
                selected = this.get("selected"),
                i;
            this.itemClicked = true;
            if (opts) {
                for (i = 0; i < opts.length; i += 1) {
                    if (opts[i].value.toString() === itemUpdated.value.toString()) {
                        this.set("value", itemUpdated.value.toString());
                    }
                }
            }
            return this;
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }

            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value")
                }
            }
        },
        onChangeValue: function (attrs, value) {
            var option;
            this.attributes.value = attrs.attributes.value;
            option = this.findOption(value, "value");
            if (option && typeof option === "object") {
                if (!this.itemClicked) {
                    this.set("data", {
                        value: option.value,
                        label: option.label
                    });
                }
                this.itemClicked = false;
                this.get("validator").set({
                    valueDomain: this.get("value"),
                    options: this.get("options") || []
                });
                this.get("validator").verifyValue();
            } else {
                this.attributes.value = this.previous("value");
            }
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         */
        afterExecuteQuery: function (response) {
            this.set("data", {value: "", label: ""});
            this.set({value: ""}, {silent: true});
            this.mergeRemoteOptions(response);
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param value {object} valid data for this component
         * @param dependencyData {object}, data to complete the component domain
         */
        setAppData: function (value, dependencyData) {
            var data;
            if (dependencyData) {
                this.recoreryRemoteOptions(dependencyData);
            }
            data = this.findOption(value, "value") || {value: "", label: ""};
            this.set({"data": data}, {silent: true});
            this.set({"value": value}, {silent: true});
            this.set({"keyLabel": data.label}, {silent: true});
            this.set("toDraw", true);
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Radio", RadioboxModel);
}());

(function () {
    var SubmitModel = PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("submit"),
            label: "untitled label",
            type: "submit",
            namespace: "pmdynaform",
            disabled: false,
            colSpan: 12
        },
        getValue: function () {
            var label = this.get("label");
            return label ? label : "";
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Submit", SubmitModel);
}()); 
(function () {
    var TextAreaModel = PMDynaform.model.Field.extend({
        defaults: {
            type: "textarea",
            placeholder: "",
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
            columnName: null,
            originalType: null,
            data: null,
            keyLabel: "",
            enableValidate: true,
            text: "",
            variable: ''
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }
            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value")
                }
            }
        },
        defineModelEvents: function () {
            this.on("change:text", this.onChange, this);
            this.on("change:value", this.onChange, this);
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:data", this.onChangeData, this);
            return this;
        },
        initialize: function (attrs) {
            var data, maxLength;
            this.set("optionsSql", null);
            this.set("label", this.get("label"));
            this.set("defaultValue", this.get("defaultValue"));
            this.set("dataForDependent",{});
            this.set("validator", new PMDynaform.model.Validator({
                "type": this.get("type"),
                "required": this.get("required"),
                "maxLength": this.get("maxLength"),
                "requiredFieldErrorMessage": this.get("requiredFieldErrorMessage"),
                "dataType": this.get("dataType") || "string",
                "regExp": {
                    validate: this.get("validate"),
                    message: this.get("validateMessage")
                }
            }));
             
            data = this.get("data");
            if (data && data["value"] !== "") {
                data = {
                    value: data["value"],
                    label: data["value"]
                };
                this.set("data", data);
                this.set("value", data["value"]);
                this.set("defaultValue", data["value"]);
            } else {
                this.set("data", {value: "", label: ""});
                this.set("value", "");
            }
            this.initControl();

            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.set("text", this.get("data")["label"]);
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
        },
        initControl: function () {
            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
        },
        isValid: function () {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        validate: function (attrs) {
            var valueFixed = this.get("data")["value"];
            if (this.get("enableValidate")) {
                this.get("validator").set("value", valueFixed);
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid",true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        onChange: function (attrs, item) {
            var data;
            data = {
                value: item || "",
                label: item || ""
            };
            this.set("data", data);
            this.set({text: item, value: item});
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * Set value
         * @param value
         */
        setValue: function (value) {
            if (value !== undefined) {
                this.set("value", value);
                this.set("text", value);
                this.set("data", {
                    value: value,
                    label: value
                });
            }
            return this;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         */
        afterExecuteQuery: function(response) {
            var indexValue = 0,
                responseDefault = [{
                value: "",
                text: ""
            }];
            response = response ? response : [];
            _.defaults(response, responseDefault);
            if (_.isArray(response)) {
                this.set("value", response[indexValue].value);
            }
            return this;
        },
        /**
         * Superclass override method
         */
        recoreryRemoteOptions: function(){
        }        
    });
    PMDynaform.extendNamespace("PMDynaform.model.TextArea", TextAreaModel);
}());

(function () {

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
            variable: '',
            var_uid: null,
            var_name: null,
            columnName: null,
            originalType: null,
            data: null,
            keyValue: null,
            keyLabel: "",
            formulaAssociatedObject: [],
            enableValidate: true,
            text: "",
            formula: ''
        },
        defineModelEvents: function () {
            this.on("change:text", this.onChange, this);
            this.on("change:value", this.onChange, this);
            this.on("change:data", this.onChangeData, this);
            return this;
        },
        initialize: function (attrs) {
            var data, maxLength;
            this.set("optionsSql", null);
            this.set("dataType", this.get("dataType").trim().length ? this.get("dataType") : "string");
            this.set("label", this.get("label"));
            this.set("dataForDependent", {});
            this.set("defaultValue", this.get("defaultValue"));
            this.set("validator", new PMDynaform.model.Validator({
                "type": this.get("type"),
                "required": this.get("required"),
                "requiredFieldErrorMessage": this.get("requiredFieldErrorMessage"),
                "maxLength": this.get("maxLength"),
                "dataType": this.get("dataType") || "string",
                "regExp": {
                    validate: this.get("validate"),
                    message: this.get("validateMessage")
                }
            }));

            if (this.get("formula").trim().length) {
                this.attributes.formula = this.get("formula").replace(/\s/g, '');
            }
            if (this.attributes._extended && this.attributes._extended.formula) {
                this.attributes._extended.formula = this.attributes._extended.formula.replace(/\s/g, '');
            }

            data = this.get("data");
            if (data && data["value"] !== "") {
                this.set("keyValue", data["value"]);
                if (data["label"] !== "") {
                    data = {
                        value: data["value"],
                        label: data["label"]
                    };
                } else {
                    data = {
                        value: data["value"],
                        label: data["value"]
                    };
                }
                this.set("data", data);
                this.set("value", data["value"]);
                this.set("defaultValue", data["value"]);
                this.set("keyLabel", data["value"]);
            } else {
                this.set("data", {value: "", label: ""});
                this.set("value", "");
                this.set("keyLabel", "");
            }
            this.initControl();
            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.set("text", this.get("data")["label"]);
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
        },
        initControl: function () {
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
        addFormulaTokenAssociated: function (formulator) {
            if (formulator instanceof PMDynaform.core.Formula) {
                formulator.addTokenValue(this.get("id"), this.get("data")["value"]);
            }
            return this;
        },
        addFormulaFieldName: function (otherField) {
            this.get("formulator").addField("field", otherField);
            return this;
        },
        updateFormulaValueAssociated: function (field) {
            var resultField = field.model.get("formulator").evaluate();
            field.model.setValue(resultField);
            return this;
        },
        isValid: function () {
            return this.get("valid");
        },
        validate: function (attrs) {
            var valueFixed = this.get("data")['value'];
            if (this.get("enableValidate")) {
                this.get("validator").set("value", valueFixed);
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }
            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value")
                }
            }
        },
        getData2: function () {
            var data = {}, name, value;
            name = this.get("variable") ? this.get("variable").var_name : this.get("name");
            value = this.get("value");
            data[name] = value;
            return data;
        },
        reviewRemotesOptions: function () {
            var sql;
            if (this.get("variable") && this.get("variable").trim().length) {
                sql = this.get("sql");
                if (sql) {
                    this.reviewRemoteVariable();
                }
            }
            return this;
        },
        onChange: function (attrs, item) {
            var data;
            item = (item === null || item === undefined) ? '' : item;
            data = {
                value: item,
                label: item
            };
            this.attributes.text = item;
            this.attributes.value = item;
            this.set("data", data);
            return this;
        },
        setValue: function (value) {
            this.set({"text": value}, {silent: true});
            this.set({"value": value}, {silent: true});
            this.set("data", {
                value: value,
                label: value
            });
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         */
        afterExecuteQuery: function (response) {
            var indexValue = 0,
                responseDefault = [{
                value: "",
                text: ""
            }];
            response = response ? response : [];
            _.defaults(response, responseDefault);
            if (_.isArray(response)) {
                this.set("value", response[indexValue].value);
            }
            return this;
        },
        /**
         * Superclass override method
         */
        recoreryRemoteOptions: function () {

        },
        /**
         * This function execute on change data
         * @param attrs
         * @param item
         */
        onChange: function (attrs, item) {
            var data;
            item = (item === null || item === undefined) ? '' : item;
            data = {
                value: item,
                label: item
            };
            this.attributes.text = item;
            this.attributes.value = item;
            this.attributes.keyLabel = item;
            this.set("data", data);
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Text", TextModel);
}());

(function () {
    var File = PMDynaform.model.Field.extend({
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
            columnName: null,
            originalType: null,
            data: {
                value: [],
                label: []
            },
            appDocUID: [],
            enableValidate: true,
            sizeUnity: "",
            stackLabels: [],
            cleaned: false
        },
        initialize: function (properties) {
            this.set("label", this.get("label"));
            this.set("defaultValue", this.get("defaultValue"));
            this.set("items", []);
            this.set("proxy", []);
            this.set("validator", new PMDynaform.model.Validator({
                "type": "file",
                "required": this.get("required"),
                "requiredFieldErrorMessage": this.get("requiredFieldErrorMessage")
            }));
            this.formatData(properties.data);
            this.setSizeAndUnity(properties.size, properties.sizeUnity);
            this.clearStackLabels();
            return this;
        },
        /**
         * Format Data File
         * @param data
         * @returns {File}
         */
        formatData: function (data) {
            var defaultValue = [],
                defaultLabel = [],
                valueAux,
                labelAux;
            if (data && _.isObject(data) && data.value && data.label) {
                labelAux = this.parseToArray(data.label);
                valueAux = this.parseToArray(data.value);
                if (_.isArray(labelAux) && _.isArray(valueAux)) {
                    defaultLabel = labelAux;
                    defaultValue = valueAux;
                }
                this.setAppDocUID(data.app_doc_uid ? data.app_doc_uid : null);
            }
            this.set("data", {
                value: defaultValue,
                label: defaultLabel
            });
            this.set("value", defaultValue);
            return this;
        },
        /**
         * Apply JSON PARSE
         * @param data
         * @returns {*}
         */
        parseToArray: function (data) {
            var arrData = data;
            if (data && _.isString(data)) {
                arrData = JSON.parse(data);
            }
            return arrData;
        },
        /**
         * Validate File
         * @returns {boolean}
         */
        isValid: function () {
            var valid = false;
            this.get("validator").set("value", this.get("value").toString());
            this.get("validator").verifyValue();
            if (this.get("validator").get("valid")) {
                valid = true;
            }
            return valid;
        },
        /**
         * Gets appDocUID to file field
         */
        getAppDocUID: function () {
            return this.get("appDocUID") || null;
        },
        /**
         * Set AppDocUid
         * @param appDocUid
         * @returns {File}
         */
        setAppDocUID: function (appDocUid) {
            if (_.isArray(appDocUid) && appDocUid.length) {
                this.set("appDocUID", appDocUid);
            }
            return this;
        },
        /**
         * Set Size and SizeUnity
         * @param size
         * @param unity
         * @returns {File}
         */
        setSizeAndUnity: function (size, unity) {
            var defaultSize = parseInt(size) || 999999,
                defaultUnity = parseInt(size) ? unity : "MB";
            this.set("size", defaultSize);
            this.set("sizeUnity", defaultUnity);
            return this;
        },
        /**
         * Get name file (text).
         * @returns {Array}
         */
        getText: function () {
            var data = this.get("data"),
                label = [];
            if (_.isArray(data.label) && data.label.length) {
                label = data.label;
            } else if (this.getCurrentNameFile()) {
                label = [this.getCurrentNameFile()];
            }
            return label;
        },
        /**
         * Get Doc UID (value).
         * @returns {Array}
         */
        getValue: function () {
            return this.getAppDocUID() || [];
        },
        /**
         * Add Label File To stackLabels
         * @param label
         * @returns {File}
         */
        addLabelToStack: function (label) {
            var auxiliarStack = this.get("stackLabels");
            if (typeof label === "string") {
                auxiliarStack.unshift(label);
            }
            this.set("stackLabels", auxiliarStack);
            return this;
        },
        /**
         * Clear stackLabels
         * @returns {File}
         */
        clearStackLabels: function () {
            this.set("stackLabels", []);
            return this;
        },
        /**
         * Clear Content File Data
         * @returns {File}
         */
        clearContent: function () {
            var data = this.get("data");
            if (data && !_.isEmpty(data) && data.value && data.label) {
                this.set("cleaned", true);
                this.set("data", {
                    value: [],
                    label: [],
                    app_doc_uid: []
                });
                this.set("value", []);
                this.set("appDocUID", []);
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param value {array} valid data for this component
         */
        setAppData: function (value) {
            this.set("value", value);
            return this;
        },
        /** Return the previous name of last file
         * @returns {string}
         */
        getPreviousNameFile: function () {
            var auxiliarStack = this.get("stackLabels"),
                index = 1;
            return (auxiliarStack && auxiliarStack.length > 1) ? auxiliarStack[index] : "";
        },
        /**
         * Return the name of the current file
         * @returns {string}
         */
        getCurrentNameFile: function () {
            var auxiliarStack = this.get("stackLabels"),
                index = 0;
            return (auxiliarStack && auxiliarStack.length > 0) ? auxiliarStack[index] : "";
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.File", File);
}());

(function () {
    var CheckGroupModel = PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "string",
            disabled: false,
            group: "form",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("checkgroup"),
            label: "untitled label",
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
            columnName: null,
            originalType: null,
            data: {},
            defaultValue: [],
            labelsSelected: [],
            variable: "",
            enableValidate: true
        },
        initialize: function (attrs) {
            var data;
            this.set("validator", new PMDynaform.model.Validator({
                type: this.get("type"),
                required: this.get("required"),
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            this.set("dataForDependent",{});
            if (this.get("optionsSql") || this.get("options")) {
                this.set("localOptions", this.get("options"));
                this.mergeRemoteOptions(this.get("optionsSql"));
            }
            if (this.get("data") || this.get("data")["value"] || this.get("data")["defaultValue"]) {
                this.set("data", this.initData(this.get("defaultValue"), this.get("value"), this.get("data"), this.get("variable")));
                this.attributes.value = this.get("data")["value"];
            }
            this.initControl();
            this.get("validator").set("value", this.get("value"));
             
            if (this.get("variable") === "") {
                this.attributes.name = "";
            }
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
            this.on("change:value", this.updateItemSelected, this);
            return this;
        },
        initData: function (defaultV, value, data) {
            var auxData = {}, existData = false;
            if (data) {
                if (typeof data === "object") {
                    if (data.hasOwnProperty("value") && $.isArray(data["value"])) {
                        if (data.hasOwnProperty("label") && data["label"].toString().trim() === "") {
                            auxData["value"] = data["value"];
                            auxData["label"] = [];
                            for (var i = 0; i < this.get("options").length; i += 1) {
                                if (data["value"].indexOf(this.get("options")[i]["value"]) > -1) {
                                    auxData["label"].push(this.get("options")[i]["label"]);
                                }
                            }
                            this.attributes.labelsSelected = auxData["label"];
                        } else {
                            if ($.isArray(data["label"])) {
                                this.attributes.labelsSelected = data["label"];
                                data["label"] = JSON.stringify(this.attributes.labelsSelected);
                                auxData = data;
                            } else {
                                if (data["label"].indexOf("[") === 0 && data["label"].lastIndexOf("]") === data["label"].length - 1) {
                                    this.attributes.labelsSelected = JSON.parse(data["label"]);
                                    data["label"] = JSON.stringify(this.attributes.labelsSelected);
                                    auxData = data;
                                }
                            }
                        }
                        existData = true;
                    } else {
                        if (typeof data["value"] === "string" && data["value"].length) {
                            data["value"] = data["value"].split(/,/g);
                            if (data["label"].indexOf("[") === 0 && data["label"].lastIndexOf("]") === data["label"].length - 1) {
                                this.attributes.labelsSelected = JSON.parse(data["label"]);
                                data["label"] = JSON.stringify(this.attributes.labelsSelected);
                            }
                            auxData = data;
                        }
                        if (!data.hasOwnProperty("value") || data["value"] === "") {
                            auxData["value"] = [];
                            auxData["label"] = [];
                            this.attributes.labelsSelected = [];
                        }
                        existData = true;
                    }
                } else {
                    auxData["value"] = [];
                    auxData["label"] = [];
                    this.attributes.labelsSelected = [];
                }
            } else {
                auxData["value"] = [];
                auxData["label"] = [];
                this.attributes.labelsSelected = [];
            }
            if (defaultV && !existData) {
                var defaultV = defaultV.split("|");
                for (var i = 0; i < this.get("options").length; i += 1) {
                    if (defaultV.indexOf(this.get("options")[i]["value"]) > -1) {
                        auxData["value"].push(this.get("options")[i]["value"]);
                        auxData["label"].push(this.get("options")[i]["label"]);
                    }
                }
            }
            if ($.isArray(auxData["label"])) {
                this.attributes.labelsSelected = auxData["label"];
                auxData["label"] = JSON.stringify(auxData["label"]);
            }
            return auxData;
        },
        initControl: function () {
            var opts = this.get("options"),
                i,
                newOpts = [],
                itemsSelected = [];

            for (i = 0; i < opts.length; i += 1) {
                if (this.get("data") && this.get("data").value) {
                    if (this.get("data").value.indexOf(opts[i].value) > -1) {
                        opts[i].selected = true;
                        itemsSelected.push(opts[i]);
                    }
                }
                newOpts.push({
                    label: opts[i].label,
                    value: opts[i].value,
                    selected: opts[i].selected ? true : false
                });
            }
            this.set("options", newOpts);
            this.set("selected", itemsSelected);
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        getData: function () {
            return {
                name: this.get("name") ? this.get("name") : "",
                value: this.get("value")
            };
        },
        validate: function (attrs) {
            if (this.get("enableValidate")) {
                this.get("validator").set("value", attrs.value.length);
                if (this.get("options").length) {
                    this.get("validator").set("options", this.attributes.options);
                }
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        isValid: function () {
            return this.get("valid");
        },
        setItemChecked: function (itemUpdated) {
            var opts = this.get("options"),
                selected = [],
                i;
            this.attributes.labelsSelected = [];
            if (opts) {
                for (i = 0; i < opts.length; i += 1) {
                    if (opts[i].value.toString() === itemUpdated.value.toString()) {
                        opts[i].selected = itemUpdated.checked;
                    }
                }
                this.set("options", opts);
                for (i = 0; i < opts.length; i += 1) {
                    if (i === opts.length - 1 && selected.length) {
                        opts[i].selected = false;
                    }
                    if (opts[i].selected) {
                        selected.push(opts[i].value);
                        this.attributes.labelsSelected.push(opts[i].label);
                    }
                }
                if (selected.length) {
                    this.attributes.value = selected;
                } else {
                    this.attributes.value = [];
                }
                this.set("selected", selected);
            }
            return this;
        },
        setItemsChecked: function (items) {
            for (var index = 0; index < items.length; index++) {
                this.setItemChecked({
                    value: items[index],
                    checked: true
                });
            }
            return this;
        },
        updateItemSelected: function () {
            var i,
                selected = this.get("selected"), auxValue, opts = this.get("options");
            if ($.isArray(this.get("value"))) {
                this.set("selected", []);
                selected = this.get("selected");
                for (i = 0; i < opts.length; i += 1) {
                    opts[i].selected = false;
                }
                this.set("options", opts);
                auxValue = this.get("value");
                for (i = 0; i < auxValue.length; i += 1) {
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
            for (i = 0; i < selected.length; i += 1) {
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
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         */
        afterExecuteQuery: function(response) {
            this.mergeRemoteOptions(response);
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the 
         * service to the component
         * @param data {object} valid data for this component
         * @param dependencyData {object}, data to complete the component domain
         * Is used in the method PMDynaform.model.CheckGroup.recoreryRemoteOptions
         */
        setAppData: function(values, dependencyData) {
            var data = {
                    value: [],
                    label: []
                },
                i,
                options;
            this.recoreryRemoteOptions(dependencyData);
            options = this.findOptions(values, "value");
            if (_.isArray(options)) {
                for (i = 0; i < options.length; i += 1) {
                    data.value.push(options[i].value);
                    data.label.push(options[i].label);
                }
            }
            this.set("labelsSelected", data.label);
            data.label = JSON.stringify(data.label);
            this.set({"data": data}, {silent: true});
            this.set({"value": data.value}, {silent: true});
            this.set("toDraw", true);
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.CheckGroup", CheckGroupModel);
}());

(function () {
    var CheckBoxModel = PMDynaform.model.Field.extend({
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
            label: "untitled label",
            localOptions: [],
            mode: "edit",
            options: [],
            required: false,
            type: "checkbox",
            validator: null,
            valid: true,
            value: null,
            columnName: null,
            originalType: null,
            data: {},
            defaultValue: null,
            labelsSelected: null,
            optionsToTrue: [true, 1, "true", "1"],
            optionsToFalse: [false, 0, "false", "0"],
            variable: "",
            enableValidate: true,
            /**
             * @member toDraw {boolean}: toDraw: When this property change, the view is redrawn
             */
            toDraw: false
        },
        /**
         * This initialize data
         * @param defData: json with valid options value and label
         * @returns {CheckBoxModel}
         */
        initialize: function (attrs) {
            var data;
            this.on("change:data", this.updateValue, this);
            this.on("change:value", this.updateItemSelected, this);
            this.set("validator", new PMDynaform.model.Validator({
                type: this.get("type"),
                required: this.get("required"),
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            if (_.isArray(this.get("options")) && !this.get("options").length) {
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
                this.attributes.dataType = "boolean";
            }
            if (this.get("data") || this.get("value") || this.get("defaultValue")) {
                data = this.initData(this.get("defaultValue"), this.get("value"), this.get("data"), this.get("variable"));
                this.set({data: data}, {silent: true});
                this.attributes.value = this.get("data")["value"];
            } else {
                this.attributes.data["value"] = "";
                this.attributes.data["label"] = "";
                this.attributes.value = this.get("data")["value"];
            }
            this.initControl();
            this.attributes.value = this.get("data").value;
            this.get("validator").set("value", this.get("value"));
             
            this.setLocalOptions();
            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.defineModelEvents();
            return this;
        },
        initControl: function () {
            var opts = this.get("options"),
                i,
                newOpts = [],
                itemsSelected = [];
            if (_.isArray(opts)) {
                for (i = 0; i < opts.length; i += 1) {
                    if (!opts[i].value && (typeof opts[i].value !== "number")) {
                        opts[i].value = opts[i].label;
                    }
                    if (this.get("data") && this.get("data").value) {
                        if (this.get("data").value.indexOf(opts[i].value) > -1) {
                            opts[i].selected = true;
                        } else {
                            opts[i].selected = false;
                        }
                    }
                    newOpts.push({
                        label: opts[i].label,
                        value: opts[i].value,
                        selected: opts[i].selected ? true : false
                    });
                }
            }
            this.set("options", newOpts);
            this.set("selected", itemsSelected);
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        getData: function () {
            if (this.get("group") === "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: [this.get("value")]
                }

            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: [this.get("value")]
                }
            }
            return this;
        },
        validate: function (attrs) {
            var value;
            value = parseInt(this.get("data")["value"]);
            if (this.get("enableValidate")) {
                this.get("validator").set("value", value);
                if (this.get("options").length) {
                    this.get("validator").set("options", this.attributes.options);
                }
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        isValid: function () {
            return this.get("valid");
        },
        /**
         * Update data with the current value
         * @returns {CheckBoxModel}
         */
        updateItemSelected: function () {
            var currValue = this.get("value"),
                currData = this.get("data");
            if (!this.attributes.disabled) {
                this.get("validator").set({
                    valueDomain: currValue,
                    options: this.get("options")
                });
                this.get("validator").set("value", this.get("selected").length);
                this.get("validator").verifyValue();
            }
            if (currValue && currData) {
                this.setData(currValue);
            }
            return this;
        },
        /**
         * Update value with the current data
         * @returns {CheckBoxModel}
         */
        updateValue: function () {
            var currentData = this.get("data");
            if (currentData && currentData.value) {
                this.set({value: currentData.value}, {silent: true});
            }
            return this;
        },
        /**
         * Get Value from data
         * @returns {null}
         */
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * Set Value
         * @param value
         * @returns {CheckBoxModel}
         */
        setValue: function (value) {
            var valuesForTrue = [1, true, "1", "true"],
                valuesForFalse = [0, false, "0", "false"];
            value = (_.isArray(value) && value.length > 0) ? value[0] : value;
            if (value !== undefined) {
                if (valuesForTrue.indexOf(value) > -1) {
                    this.set("value", "1");
                } else if (valuesForFalse.indexOf(value) > -1) {
                    this.set("value", "0");
                }
            }
            return this;
        },
        /**
         * Set Data
         * @param value
         * @returns {CheckBoxModel}
         */
        setData: function (value) {
            var data = this.findOption(value, "value");
            if (data) {
                this.set('data', {
                    value: data.value || '',
                    label: data.label || ''
                });
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the 
         * service to the component
         * @param data {boolean|string|number} valid data for this component
         */
        setAppData: function(value) {
            var data;
            data = this.findOption(value, "value");
            if (data) {
                this.selectedOptions("index", [data.index]);
                this.set({"data": data}, {silent: true});
                this.set({"value": data["value"]}, {silent: true});
                this.set("toDraw", true);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.CheckBox", CheckBoxModel);
}());

(function () {
    var DatetimeModel = PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            namespace: "pmdynaform",
            dataType: "date",
            group: "form",
            hint: "",
            id: "",
            name: "",
            placeholder: "",
            required: false,
            validator: null,
            originalType: null,
            disabled: false,
            format: "YYYY-MM-DD",
            mode: "edit",
            data: {
                value: "",
                label: ""
            },
            value: "",
            stepping: 1,
            minDate: false,
            maxDate: false,
            useCurrent: false,
            collapse: true,
            defaultDate: '',
            disabledDates: [],
            sideBySide: false,
            daysOfWeekDisabled: false,
            calendarWeeks: true,
            viewMode: "days",
            toolbarPlacement: "default",
            showTodayButton: true,
            showClear: true,
            widgetPositioning: {
                horizontal: "left",
                vertical: "bottom"
            },
            keepOpen: false,
            dayViewHeaderFormat: "MMMM YYYY",
            pickType: "datetime",
            keyLabel: "",
            enableValidate: true,
            text: "",
            variable: "",
            type: "datetime",
            label: "untitled label",
            datetimeIsoFormat: "YYYY-MM-DD HH:mm:ss",
            dateIsoFormat: "YYYY-MM-DD"
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }

            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value")
                }
            }
            return this;
        },
        initialize: function (options) {
            var useDefaults = {
                    showClear: false,
                    useCurrent: false
                },
                useCurrentOptions = [true, false, 'year', 'month', 'day', 'hour', 'minute'],
                viewMode = ['years', 'months', 'days'],
                data = {
                    value: "",
                    label: ""
                },
                defaultDate,
                maxOrMinDate,
                flag = true;
            this.redefinepropertiesV4(options);

            if (this.get("useCurrent") === "true") {
                this.attributes.useCurrent = JSON.parse(this.get("useCurrent"));
            }
            if (useCurrentOptions.indexOf(this.get("useCurrent")) === -1) {
                this.attributes.useCurrent = useDefaults["useCurrent"];
            }

            if (this.get("showClear") === "true") {
                this.attributes.showClear = JSON.parse(this.get("showClear"));
            }

            if (this.get("showClear") === "false") {
                this.attributes.showClear = JSON.parse(this.get("showClear"));
            }
            if (typeof this.get("showClear") !== "boolean") {
                this.attributes.showClear = useDefaults["showClear"];
            }

            if (this.get("format") === "false") {
                this.attributes.format = JSON.parse(this.get("format"));
            }

            this.customPickTimeIcon(this.get("pickType"));

            if (!_.isEmpty(this.get("data")) && (this.get("data")["value"] !== "" || this.get("data")["label"] !== "")) {
                this.set("defaultDate", false);
            } else {
                this.set("value", this.get("defaultDate"));
                this.set("data", data);
            }
            if (typeof this.get("maxDate") === "boolean") {
                this.set("maxDate", "");
            }

            if (!this.isDate(this.get("maxDate"))) {
                this.set("maxDate", "");
            }

            if (!this.isDate(this.get("minDate"))) {
                this.set("minDate", "");
            }

            if (!this.isDate(this.get("defaultDate"))) {
                this.set("defaultDate", "");
            }

            if (this.get("maxDate").trim().length && this.get("defaultDate") && this.get("defaultDate").trim().length) {
                defaultDate = this.get("defaultDate").split("-");
                maxOrMinDate = this.get("maxDate").split("-");
                if ((parseInt(defaultDate[0]) <= parseInt(maxOrMinDate[0]))) {
                    if ((parseInt(defaultDate[1]) <= parseInt(maxOrMinDate[1]))) {
                        if ((parseInt(defaultDate[2]) <= parseInt(maxOrMinDate[2]))) {
                            flag = true;
                        } else {
                            flag = false;
                        }
                    } else {
                        flag = false;
                    }
                } else {
                    flag = false;
                }
                if (!flag) {
                    this.set("defaultDate", false);
                }
            }
            if (flag) {
                if (typeof this.get("minDate") === "boolean") {
                    this.set("minDate", "");
                }
                if (this.get("minDate").trim().length && this.get("defaultDate") && this.get("defaultDate").trim().length) {
                    defaultDate = this.get("defaultDate").split("-");
                    maxOrMinDate = this.get("minDate").split("-");
                    if ((parseInt(defaultDate[0]) >= parseInt(maxOrMinDate[0]))) {
                        if ((parseInt(defaultDate[1]) >= parseInt(maxOrMinDate[1]))) {
                            if ((parseInt(defaultDate[2]) >= parseInt(maxOrMinDate[2]))) {
                                flag = true;
                            } else {
                                flag = false;
                            }
                        } else {
                            flag = false;
                        }
                    } else {
                        flag = false;
                    }
                    if (!flag) {
                        this.set("defaultDate", false);
                    }
                }
            }
            if (this.get("data") && this.get("data")["value"] !== "") {
                this.attributes.value = this.get("data")["value"];
                this.attributes.keyLabel = this.get("data")["label"];
            } else {
                if (this.get("defaultDate") !== "") {
                    this.attributes.data = {
                        value: this.get("defaultDate"),
                        label: this.get("defaultDate")
                    };
                } else {
                    this.attributes.data = {
                        value: "",
                        label: ""
                    };
                }
            }
            this.set("validator", new PMDynaform.model.Validator({
                required: this.get("required"),
                type: this.get("type"),
                dataType: this.get("dataType"),
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));

            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.defineModelEvents();
            this.set("text", this.get("data")["label"]);
            return this;
        },
        customPickTimeIcon: function (format) {

        },
        isValid: function () {
            return this.get("valid");
        },
        validate: function (attrs) {
            var valueFixed = this.get("data")["value"];
            if (this.get("enableValidate")) {
                this.get("validator").set("value", valueFixed);
                this.get("validator").verifyValue();
            } else {
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        isDate: function (dateValue) {
            var pattern = /@@|@\$|@=/;
            var d = new Date(dateValue);
            if (pattern.test(dateValue) || d == "Invalid Date" || typeof d == "undefined" || !d) {
                return false;
            }
            return true;
        },
        validateDate: function (date) {
            var valid, data, value;
            value = date.replace(/-/g, "/");
            if (new Date(value).toString() !== "Invalid Date") {
                valid = true;
            } else {
                valid = false;
            }
            return valid;
        },
        formatedData: function (value) {
            var newData,
                format = 'YYYY-MM-DD HH:mm:ss';
            if (value) {
                newData = {
                    value: moment(value).format(format),
                    label: moment(value).format(this.get('format'))
                };
            }
            return newData;
        },
        onChangeValue: function (attrs, item) {
            var data = {value: "", label: ""};
            if (item !== undefined) {
                if (this.validateDate(item)) {
                    data = this.formatedData(item);
                }
            }
            this.set("data", data);
            this.attributes.value = data["value"];
            this.attributes.text = data["label"];
            return this;
        },
        onChangeText: function (attrs, item) {
            // This method is not support to this control type 
            return this;
        },
        /**
         * Some property values that were modified in updating v3 to v4 is redefined necessary
         * @param  {Object} settings json configuration stored in definitiong
         * @return {Object} new json configuration
         */
        redefinepropertiesV4: function (settings) {
            var propConf;
            if (typeof settings === "object") {
                if (this.get("daysOfWeekDisabled")) {
                    propConf = this.get("daysOfWeekDisabled");
                    propConf = (_.isArray(propConf) && _.isEmpty(propConf)) ? false : propConf;
                    this.set("daysOfWeekDisabled", propConf);
                }
            }
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * Set value
         * @param value
         * @returns {DatetimeModel}
         */
        setValue: function (value) {
            if (value !== undefined && value !== null) {
                this.set("value", value);
                this.set("text", value);
                this.set("data",{
                    value: value,
                    label: value
                });
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {string} valid data for this component
         */
        setAppData: function (value) {
            if (value !== undefined) {
                value = value.replace(/-/g, "/");
                if (new Date(value).toString() !== "Invalid Date") {
                    this.set("value", value);
                } else if (!value) {
                    this.set("value", "");
                }
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.Datetime", DatetimeModel);
}());

(function () {

    var SuggestModel = PMDynaform.model.Field.extend({
        defaults: {
            autoComplete: "off",
            type: "suggest",
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
            variable: '',
            var_uid: null,
            var_name: null,
            options: [],
            localOptions: [],
            remoteOptions: [],
            columnName: null,
            originalType: null,
            mask: "",
            clickedControl: true,
            keyLabel: "",
            enableValidate: true,
            text: "",
            data: {
                value: '',
                label: ''
            },
            hint : ''
        },
        initialize: function (attrs) {
            var data;
            this.set("dataType", this.get("dataType").trim().length ? this.get("dataType") : "string");
            this.set("optionsSql", null);
            this.set("label", this.get("label"));
            this.set("dataForDependent",{});
            this.set("defaultValue", this.get("defaultValue"));
            this.set("validator", new PMDynaform.model.Validator({
                requiredFieldErrorMessage: this.get("requiredFieldErrorMessage")
            }));
            this.initControl();
            this.setLocalOptions();
            this.setRemoteOptions();
            data = this.get("data");
            if (data && data["value"] !== "" && data["label"] !== "") {
                this.set("value", data["value"]);
                this.set("keyLabel", data["label"]);
            } else {
                this.set("data", {value: "", label: ""});
                this.set("value", "");
            }
            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.set("text", data["label"]);
            this.defineModelEvents();
            this._dependentFieldEventRegister(this.get("sql"));
        },
        initControl: function () {
            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        isValid: function () {
            return this.get("valid");
        },
        emptyValue: function () {
            this.set("value", "");
        },
        validate: function (attrs) {
            var valueFixed = this.get("data")["value"];
            if (this.get("enableValidate")) {
                this.get("validator").set("type", this.get("type"));
                this.get("validator").set("required", this.get("required"));
                this.get("validator").set("value", valueFixed);
                this.get("validator").set("dataType", this.get("dataType"));
                this.get("validator").verifyValue();
            }else{
                this.get("validator").set("valid", true);
            }
            this.set("valid", this.get("validator").get("valid"));
            return this;
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value")
                }

            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("data")["value"]
                }
            }
        },
        reviewRemotesOptions: function () {
            var sql;
            if (this.get("variable") && this.get("variable").trim().length) {
                sql = this.get("sql");
                if (sql) {
                    this.reviewRemoteVariable();
                }
            }
            return this;
        },
        onChangeValue: function (attrs, item) {
            var data;
            data = this.findOption(item, "value");
            if (!data) {
                data = {
                    value: item,
                    label: item
                }
            }
            this.set("data", data);
            this.set("text", data["label"]);
            return this;
        },
        onChangeText: function (attrs, item) {
            var data;
            data = this.findOption(item, "label");
            if (!data) {
                data = {
                    value: item,
                    label: item
                }
            }
            this.set("data", data);
            this.set("text", data["label"]);
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return data ? data["value"] : null;
        },
        /**
         * Set Value
         * @param value
         * @returns {SuggestModel}
         */
        setValue: function (value) {
            if (value !== undefined) {
                this.set("value", value);
                this.set("data", {
                    value: value,
                    label: value
                });
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the 
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function(data) {
            if (data.hasOwnProperty("value") && data.hasOwnProperty("label")) {
                this.set({"data": data}, {silent: true});
                this.set({"value": data.value}, {silent: true});
                this.set("toDraw", true);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Suggest", SuggestModel);
}());

(function () {

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
            columnName: null,
            originalType: null,
            text: ""
        },
        initialize: function (options) {
            var data = this.getData(),
                text = data ? data.label : this.get("value");
            this.set("text", text || "");
            this.setHref(this.get("href"));
            this.on("change:text", this.onChangeText, this);
            this.on("change:value", this.onChangeValue, this);
            this.setTarget();
        },
        setTarget: function () {
            var opt = this.get("targetOptions"),
                target;

            target = opt[this.get("target")] ? opt[this.get("target")] : "_blank";
            this.set("target", target);
        },
        getData: function () {
            return this.get("data");
        },
        validationURL: function (url) {
            var reg = /^(?:(http|https):)?(\/{2,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
            return reg.test(url);
        },
        reformatURL: function (url) {
            var newHref = url;
            if (!(url.indexOf("http://") === 0 || url.indexOf("https://") === 0)) {
                newHref = "http://" + url;
            }
            return newHref;
        },
        setHref: function (href) {
            var newHref = href;
            if (!this.validationURL(href)) {
                newHref = this.reformatURL(href);
            }
            this.set("href", newHref);
            this.updateData(newHref, this.get("text"));
            return this;
        },
        onChangeValue: function (attrs, item) {
            if (item) {
                this.setHref(item);
            }
            return this;
        },
        onChangeText: function (attrs, item) {
            if (item) {
                this.set("text", item);
                this.updateData(this.get("href"), item);
            }
            return this;
        },
        updateData: function (href, dText) {
            this.set("data", {
                value: href,
                label: dText
            });
            return this;
        },
        getValue: function () {
            var href = this.get("href");
            return href ? href : "";
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Link", LinkModel);
}());
(function () {

    var Label = PMDynaform.model.Field.extend({
        defaults: {
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
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
            columnName: null,
            originalType: null,
            variable: "",
            var_uid: null,
            var_name: null,
            localOptions: [],
            remoteOptions: [],
            fullOptions: [""],
            data: null,
            value: null,
            dataType: null,
            keyValue: null,
            optionsSql: [],
            enableValidate: true,
            optionsToTrue: [true, 1, "true", "1"],
            optionsToFalse: [false, 0, "false", "0"],
            /**
             * @param {object}: dataForDependent, Stores data to eject dependent field service
             */
            dataForDependent: {},
            /**
             * @param {boolean}: supportedOptions
             */
            supportedOptions: false
        },
        initialize: function (options) {
            var originalType = this.get("originalType"),
                newData;
            if (options && _.isArray(options["options"])) {
                this.set("supportedOptions", true);
            }
            this.set("dataForDependent", {});
            this.set("label", this.get("label"));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:options", this.onChangeOptions, this);
            this.on("change:data", this.onChangeData, this);
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            this.setData(options.data);
            if (originalType === "checkbox") {
                newData = this.initData(this.get("defaultValue"), this.get("value"), this.get("data"));
                this.setData(newData);
            }
            if (_.isString(this.get("variable")) && this.get("variable") !== "") {
                this.set("name", this.get("variable") ? this.get("variable") : this.get("id"));
            }
            if (typeof this.get("formula") === "string" &&
                this.get('formula') !== "undefined" &&
                this.get('formula') !== "null" &&
                this.get('formula').length > 1) {
                this.set("formulator", new PMDynaform.core.Formula(this.get("formula")));
                this.set("disabled", true);
            }
            this._dependentFieldEventRegister(this.get("sql"));
            return this;
        },
        getData: function () {
            var value = "";
            if (this.get("group") == "grid") {
                if (this.get("originalType") !== "label") {
                    return {
                        name: this.get("columnName") ? this.get("columnName") : "",
                        value: this.get("keyValue")
                    }
                } else {
                    return {
                        name: this.get("columnName") ? this.get("columnName") : "",
                        value: this.get("value")
                    }
                }
            } else {
                if (this.get("originalType") !== "label") {
                    value = this.get("data")["value"];
                    if (this.get('originalType') === 'checkbox') {
                        value = this.get("value");
                    }
                    return {
                        name: this.get("name") ? this.get("name") : "",
                        value: value
                    }
                } else {
                    return {
                        name: this.get("name") ? this.get("name") : "",
                        value: this.get("value")
                    }
                }
            }
        },
        /**
         * setData, Set the data in the component
         * @chainable
         */
        setData: function (data) {
            var originalType = this.get("originalType"),
                value;
            if (data && !_.isEmpty(data)) {
                if (originalType === "checkgroup") {
                    value = data.hasOwnProperty("value") && data.value;
                    if (!value || _.isEmpty(value)) {
                        data = {
                            value: [],
                            label: JSON.stringify([])
                        };
                    }
                }
                this.set("data", data);
                this.set({"value": data["value"]}, {silent: true});
            } else {
                this.set("data", this.getDataWithDefaultValue());
            }
            this.set("fullOptions", this.obtainingLabelsToShow());
            return this;
        },
        setValue: function (value) {
            var originalType = this.get("originalType"),
                newData;
            switch (originalType) {
                case "checkbox":
                    newData = this.getCheckBoxData(value);
                    break;
                case "checkgroup":
                    newData = this.getCheckGroupData(value);
                    break;
                case "dropdown":
                    newData = this.getDropDownData(value);
                    break;
                case "datetime":
                    newData = this.getDateTimeData(value);
                    break;
                case "radio":
                    newData = this.getRadioData(value);
                    break;
                case "suggest":
                    newData = this.getSuggestData(value);
                    break;
                default:
                    newData = this.getTextBoxData(value);
                    break;
            }
            this.setNewData(newData);
            return this;
        },
        /**
         * Gets checkbox's data
         * @param value
         * @returns {{}}
         */
        getCheckBoxData: function (value) {
            var valuesForTrue = [1, true, "1", "true"],
                valuesForFalse = [0, false, "0", "false"],
                options = this.get("options"),
                valueDefault = (_.isArray(value) && value.length) ? value[0] : value,
                dataObject = {};
            if (valuesForTrue.indexOf(valueDefault) > -1) {
                dataObject = {
                    value: options[0]["value"],
                    label: options[0]["label"]
                };
            } else if (valuesForFalse.indexOf(valueDefault) > -1) {
                dataObject = {
                    value: options[1]["value"],
                    label: options[1]["label"]
                };
            }
            return dataObject;
        },
        /**
         * Gets checkgroup's data
         * @param values
         * @returns {{value: Array, label}}
         */
        getCheckGroupData: function (values) {
            var data,
                dataObject = {
                    value: [],
                    label: "[]"
                },
                resultOptions;
            if (_.isString(values)) {
                values = values.split(",");
            }
            if (_.isArray(values)) {
                resultOptions = this.findOptions(values, "value");
                data = this.returnOptionsData(resultOptions);
                dataObject = {
                    value: data["value"],
                    label: JSON.stringify(data["label"])
                };
            }
            return dataObject;
        },
        /**
         * Gets dropdown's data
         * @param value
         * @returns {*|boolean|Object|{value: string, label: string}}
         */
        getDropDownData: function (value) {
            var defaultData = {
                    value: "",
                    label: ""
                },
                dataObject = this.findOption(value, "value");
            if (!dataObject) {
                dataObject = this.get("data");
            }
            return dataObject || defaultData;
        },
        /**
         * Gets datetime's data
         * @param value
         * @returns {{value: string, label: string}}
         */
        getDateTimeData: function (value) {
            var format = 'YYYY-MM-DD HH:mm:ss',
                dataObject = {value: "", label: ""};
            value = value.replace(/-/g, "/");
            if (new Date(value).toString() !== "Invalid Date") {
                dataObject = {
                    value: moment(value).format(format),
                    label: moment(value).format(this.get('format'))
                }
            }
            return dataObject;
        },
        /**
         * Gets radio's data
         * @param value
         * @returns {*|boolean|Object|{value: string, label: string}}
         */
        getRadioData: function (value) {
            return this.findOption(value, "value") || {value: "", label: ""};
        },
        /**
         * Gets suggest's data
         * @param data
         * @returns {{value: *, label: *}}
         */
        getSuggestData: function (data) {
            var dataObject = {
                value: data,
                label: data
            };
            if (data && _.isObject(data) && data.hasOwnProperty("value") && data.hasOwnProperty("label")) {
                dataObject = {
                    value: data.value !== undefined ? data['value'] : "",
                    label: data.label !== undefined ? data['label'] : ""
                }
            }
            return dataObject;
        },
        /**
         * Gets textbox and textarea's data
         * @param value
         * @returns {{value: *, label: *}}
         */
        getTextBoxData: function (value) {
            return {value: value, label: value}
        },
        /**
         * Sets new data getted
         * @param dataObject
         * @returns {Label}
         */
        setNewData: function (dataObject) {
            this.set("data", dataObject);
            this.set("value", dataObject.value);
            this.setFullOptions(dataObject.label);
            return this;
        },
        /**
         * Gets full options
         * @param items
         * @returns {Label}
         */
        setFullOptions: function (items) {
            var options,
                element,
                showLabels = [];
            if (_.isArray(items)) {
                this.set("fullOptions", items);
            } else {
                try {
                    options = JSON.parse(items);
                    if (_.isArray(options)) {
                        this.set("fullOptions", options);
                    } else {
                        element = options;
                        showLabels.push(element);
                        this.set("fullOptions", showLabels);
                    }
                } catch (e) {
                    element = items;
                    showLabels.push(element);
                    this.set("fullOptions", showLabels);
                }
            }
            return this;
        },
        getValue: function () {
            var data = this.get("data");
            return (data && data.value) || "";
        },
        getDataWithDefaultValue: function () {
            var data,
                options,
                i;
            data = {
                label: this.get("defaultValue") || "",
                value: this.get("defaultValue") || ""
            };
            options = this.get("options") || [];
            for (i = 0; i < options.length; i += 1) {
                if (this.get("defaultValue") === options[i].value) {
                    data = options[i];
                    break;
                }
            }
            return data;
        },
        setLocalOptions: function () {
            this.set("localOptions", this.get("options"));
            return this;
        },
        addFormulaTokenAssociated: function (formulator) {
            if (formulator instanceof PMDynaform.core.Formula) {
                formulator.addTokenValue(this.get("id"), this.get("value"));
            }
            return this;
        },
        updateFormulaValueAssociated: function (field) {
            var resultField = field.model.get("formulator").evaluate();
            field.model.set("value", resultField);
            return this;
        },
        addFormulaFieldName: function (otherField) {
            this.get("formulator").addField("field", otherField);
            return this;
        },
        /**
         * Gets all labels to show
         * @returns {Array}
         */
        obtainingLabelsToShow: function () {
            var data,
                labels = [];
            data = this.get("data");
            if (data && !_.isEmpty(data)) {
                if (this.get("originalType") === "checkgroup") {
                    labels = PMDynaform.core.Utils.isJsonAndParse(data["label"]);
                } else {
                    labels.push(data["label"]);
                }
            }
            return labels;
        },
        /**
         * afterExecuteQuery: After executing the dependent field service,
         * it retrieves a data array, here it handles the new data
         * @param response {array}: response data set
         * @chainable
         */
        afterExecuteQuery: function (response) {
            var index = 0,
                supportedOptions = this.get("supportedOptions"),
                data,
                value,
                responseDefault = [{
                    value: "",
                    text: ""
                }];
            response = response ? response : [];
            _.defaults(response, responseDefault);
            if (_.isArray(response)) {
                if (supportedOptions) {
                    this.mergeRemoteOptions(response);
                    this.setDomainValue(index, 'index');
                } else {
                    value = response[index].value ? response[index].value : "";
                    data = {
                        value: value,
                        label: value
                    };
                    this.set("data", data);
                }
                this.set("value", this.get("data").value);
            }
            return this;
        },
        /**
         * setDomainValue: Set an element of the options as the data for the component
         * @param value {number|string|number}: The value you are looking for in the options
         * @param criteria {string}: Search criteria, The accepted values can be
         * - index
         * - value
         * - label
         * @chainable
         */
        setDomainValue: function (value, criteria) {
            var options = this.get("options"),
                option,
                invalidForAutocomplete = ["suggest", "radio", "checkgroup"],
                originalType = this.get("originalType"),
                data = {
                    value: "",
                    label: ""
                };
            if (criteria === "index") {
                option = options[value];
            } else if (criteria === "value" || criteria === "label") {
                option = this.findOption(value, criteria);
            }
            if (invalidForAutocomplete.indexOf(originalType) === -1) {
                this.set("data", option ? option : data);
            } else {
                this.setData(data);
            }
            return this;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         * @param dependencyData {object}, data to complete the component domain
         * @chainable
         */
        setAppData: function (value, dependencyData) {
            var data,
                originalType = this.get("originalType"),
                values;

            switch (originalType) {
                case "checkgroup":
                    this.recoreryRemoteOptions(dependencyData);
                    values = this.findOptions(value, "value");
                    this.setAppDataToCheckGroup(values);
                    break;
                case "radio":
                    this.recoreryRemoteOptions(dependencyData);
                    data = this.findOption(value, "value");
                    break;
                case "checkbox":
                    data = this.findOption(value[0], "value");
                    break;
                case "dropdown":
                case "suggest":
                    if (value.hasOwnProperty("value") && value.hasOwnProperty("label")) {
                        data = value;
                    } else {
                        data = this.findOption(value, value);
                    }
                    break;
                case "text":
                case "textarea":
                    data = {
                        value: value,
                        label: value
                    };
                    break;
                case "datetime":
                    data = this.setDatetimeValue(value);
                    break;
            }
            data = data || this.get("data");
            this.set({"data": data}, {silent: true});
            this.set({"value": value}, {silent: true});
            if (originalType === "checkgroup") {
                this.set("fullOptions", [JSON.parse(data.label)]);
            } else {
                this.set("fullOptions", [data.label]);
            }
            this.set("toDraw", true);
            return this;
        },
        /**
         * setDatetimeValue: Sets the corresponding data in the datetime
         * @param values {date} valid data for this component
         */
        setDatetimeValue: function (value) {
            var format = 'YYYY-MM-DD HH:mm:ss';
            value = value.replace(/-/g, "/");
            if (new Date(value).toString() !== "Invalid Date") {
                return {
                    value: moment(value).format(format),
                    label: moment(value).format(this.get('format'))
                };
            }
            return null;
        },
        /**
         * setAppDataToCheckGroup: Sets the corresponding data in the component
         * @param values {array} valid data for this component
         * @chainable
         */
        setAppDataToCheckGroup: function (values) {
            var data = {
                    value: [],
                    label: []
                },
                i;
            if (_.isArray(values)) {
                for (i = 0; i < values.length; i += 1) {
                    data.value.push(values[i].value);
                    data.label.push(values[i].label);
                }
            }
            data.label = JSON.stringify(data.label);
            this.set({"data": data}, {silent: true});
            this.set({"value": data.value}, {silent: true});
            return this;
        },
        /**
         * Abstract method to implement in the extended classes
         */
        setAppDataToCheckbox: function () {

        },
        /**
         * Retrieves the domain of the component based on
         * the fields on which it depends
         * @param data {object}: Data of the fields on which it depends
         */
        recoreryRemoteOptions: function (data) {
            var dependentsManager = this.getDependentsManager(),
                response;
            if (typeof data === "object" && this.get("supportedOptions")) {
                _.extend(data, this.preparePostData());
                response = dependentsManager.executeFieldQuery(data, this.get("variable") || "", this);
                if (_.isArray(response)) {
                    this.mergeRemoteOptions(response);
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Label", Label);
}());

(function () {
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
        initialize: function () {
            this.set("label", this.get("label"));
        },
        getValue: function () {
            var data = this.get("label");
            return data ? data : "";
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Title", Title);
}());
(function () {
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
(function () {
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
            group: "form",
            var_name: "",
            data: null,
            keyLabel: "",
            options: [],
            optionsSql: [],
            remoteOptions: [],
            text: "",
            variable: ""
        },
        defineModelEvents: function () {
            this.on("change:value", this.onChange, this);
            this.on("change:text", this.onChange, this);
            return this;
        },
        initialize: function (options) {
            var data = {};
            this.set("defaultValue", this.get("defaultValue"));
            if (!this.get("data")) {
                data = {
                    value: this.get("defaultValue"),
                    label: this.get("defaultValue")
                };
                this.attributes.data = data;
            }
            this.set("value", this.get("data")["value"]);
            this.set("keyLabel", this.get("data")["label"]);
            this.setLocalOptions();
            this.setRemoteOptions();
            this.mergeOptionsSql();
            this.initControl();
            if (this.get("variable").trim().length === 0) {
                if (this.get("group") === "form") {
                    this.attributes.name = "";
                } else {
                    this.attributes.name = this.get("id");
                }
            }
            this.defineModelEvents();
            this.set("text", this.get("data")["label"]);
            return this;
        },
        initControl: function () {
            if (this.get("defaultValue")) {
                this.set("value", this.get("defaultValue"));
            }
        },
        onChangeValue: function () {
        },
        getData: function () {
            if (this.get("group") == "grid") {
                return {
                    name: this.get("columnName") ? this.get("columnName") : "",
                    value: this.get("value") ? this.get("value") : ""
                }
            } else {
                return {
                    name: this.get("name") ? this.get("name") : "",
                    value: this.get("value") ? this.get("value") : ""
                }
            }
        },
        onChange: function (attrs, item) {
            var data;
            data = {
                value: item || "",
                label: item || ""
            };
            this.set("data", data);
            this.set({text: item, value: item});
            return this;
        },
        getValue: function () {
            var data = this.getData();
            return data ? data["value"] : null;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.Hidden", HiddenModel);
}());

(function () {
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
            columnName: null,
            originalType: null,
            group: "form",
            alternateText: "",
            comment: "",
            hint: ""
        },
        initialize: function (options) {
            var defaults;
            this.set("label", this.get("label"));
            this.set("defaultValue", this.get("defaultValue"));
            this.on("change:label", this.onChangeLabel, this);
            this.on("change:value", this.onChangeValue, this);
            if (options && options.project) {
                this.project = options.project;
            }
            this.setShapeType();
        },
        setShapeType: function () {
            var shape = this.get("shape"),
                types = this.get("shapeTypes"),
                selected;

            selected = types[shape] ? types[shape] : types["thumbnail"];
            this.set("shape", selected);
            return this;
        },
        getValue: function () {
            var value = this.get("src");
            return value ? value : "";
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.Image", ImageModel);
}());

(function () {
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
(function () {

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
            altitude: 0,
            latitude: 0,
            longitude: 0,
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
        initialize: function () {
            this.set("label", this.get("label"));
            this.checkSupportGeoLocation();
        },
        checkSupportGeoLocation: function () {
            var supportNavigator = navigator.geolocation ? true : false;

            this.set("supportNavigator", supportNavigator);

            return this;
        },
        rightToLeftLabels: function () {
            var marker = this.get("marker"),
                infowindow = new google.maps.InfoWindow();

            infowindow.setContent('<b>القاهرة</b>');
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(this.get("googlemap"), marker);
            });
        },
        getData: function () {
            return {
                name: this.get("variable") ? this.get("variable").var_name : this.get("name"),
                value: this.get("longitude") + "|" + this.get("latitude")
            };
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.GeoMap", GeoMapModel);
}());
(function () {
    var Annotation = PMDynaform.model.Field.extend({
        defaults: {
            type: "annotation",
            label: "untitled label",
            id: PMDynaform.core.Utils.generateID(),
            name: PMDynaform.core.Utils.generateName("title"),
            colSpan: 12,
            namespace: "pmdynaform"
        },
        initialize: function () {
            this.set("label", this.get("label"));
        },
        getValue: function () {
            var data = this.get("label");
            return data ? data : null;
        },
        getAppData: function () {
            return {};
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Annotation", Annotation);
}());
(function () {
    var PanelField = PMDynaform.model.Field.extend({
        defaults: {
            type: "panel",
            showHeader: false,
            showFooter: false,
            title: "untitled-panel",
            footerContent: "<div>footer pmdynaform!</div>",
            content: "<div>content Body in panel PMDynaform</div>",
            id: PMDynaform.core.Utils.generateID(),
            colSpan: 12,
            namespace: "pmdynaform",
            typePanel: "default",
            border: "1px"
        },
        initialize: function (options) {
            var length;
            if (this.get("border")) {
                length = this.verifyLenght(this.get("border"));
                this.set("border", length);
            }
        },
        verifyLenght: function (length) {
            if (typeof length === 'number') {
                length = length + "px";
            } else if (Number(length).toString() != "NaN") {
                length = length + "px";
            } else if (/^\d+(\.\d+)?px$/.test(length)) {
                length = length;
            } else if (/^\d+(\.\d+)?%$/.test(length)) {
                length = length;
            } else if (/^\d+(\.\d+)?em$/.test(length)) {
                length = length;
            } else if (length === 'auto' || length === 'inherit') {
                length = length;
            } else {
                length = "1px";
            }
            return length;
        }

    });
    PMDynaform.extendNamespace("PMDynaform.model.PanelField", PanelField);
}());
(function () {
    /**
     * @class PMDynaform.view.FlashMessageView
     * A message to display for a while.
     *
     * Usage example:
     *
     *      @example
     *      flashModel = new PMDynaform.ui.FlashMessageModel({
     *           message : "This is a flas message",
     *           emphasisMessage: "Info",
     *           startAnimation:5000,
     *           closable:true,
     *           type:"danger",
     *           appendTo:document.body,
     *           duration:5000
     *      });
     *      flashView = new PMDynaform.ui.FlashMessageView({model:flashModel})
     *      flashView.render();
     *
     */
    var FlashMessageView = Backbone.View.extend({
        template: _.template($('#tpl-flashMessage').html()),
        initialize: function () {
            this.model.on('change', this.render, this);
        },
        render: function () {
            var offsetTarget;
            this.$el.html(this.template(this.model.toJSON()));
            this.configurateAnimation();
            return this;
        },
        /**
         * This method sets the necessary parameters for the effect shown
         * the message using animation Jquery
         * @return {[type]} [description]
         */
        configurateAnimation: function () {
            var offsetTarget,
                target,
                animation,
                duration = this.model.get('duration');

            target = this.model.get('appendTo');
            if (!(target instanceof jQuery)) {
                target = jQuery(target);
            }
            offsetTarget = this.calculateContainerPosition(target);
            this.fixPosition(offsetTarget);

            animation = this.$el.finish().css({
                'top': offsetTarget.top - 50
            }).fadeTo(1, 0).animate({
                top: offsetTarget.top,
                opacity: 1
            }, this.model.get('startAnimation'), 'swing');

            if (duration) {
                animation.delay(duration)
                    .animate({
                        top: this.model.get("absoluteTop") ? 0 : offsetTarget.top,
                        opacity: 0,
                        zIndex: '0'
                    });
            }

            $(document.body).append(this.$el);
        },
        /**
         * This method calculates the position of parent container
         * to place the message at the head of it
         * @param  {[HTMLElement]} target : this a HTML element target
         * @return {[type]}  return a positions left, top, width
         */
        calculateContainerPosition: function (target) {
            var offset,
                width,
                target = target || this.model.get('appendTo');
            if (!(target instanceof jQuery)) {
                target = jQuery(target);
            }
            offset = target.offset();
            width = target.outerWidth();
            if (this.model.get("absoluteTop")) {
                offset.top = this.getAbsoluteTopScrollElement(target);
            }
            return {
                top: offset.top || 0,
                left: offset.left || 0,
                width: width || 0
            }
        },
        /**
         * This method recalculates and sets the position of the component flash message
         * @param  {[type]} offset this object with positions for to set in the component
         */
        fixPosition: function (offset) {
            var showWidth = offset.width / 2,
                showLeft = offset.width / 4 + offset.left;
            if (this.$el.length) {
                this.$el.css({
                    top: offset.top,
                    width: showWidth,
                    left: showLeft,
                    position: 'absolute'
                });
            }
            return this;
        },
        getAbsoluteTopScrollElement: function () {
            var scrollTop = 0;
            if (document.body) {
                scrollTop = $(document).scrollTop();
            }
            return scrollTop;
        }
    });
    PMDynaform.extendNamespace('PMDynaform.ui.FlashMessageView', FlashMessageView);
}());

(function () {
    var FileMobile = PMDynaform.model.Field.extend({
        defaults: {
            autoUpload: false,
            camera: true,
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
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
            files: [],
            data: {
                value: [],
                label: null
            },
            enableValidate: true
        },

        initialize: function () {
            this.attributes.files = [];
            this.set("items", []);
            this.set("proxy", []);
            if (this.get("id") && this.get("id").trim().length !== 0) {
                this.set("name", this.get("id"));
            }
        },
        getAppData: function () {
            var data,
                idFiles = [],
                respData = {};
            data = this.get("data");
            idFiles = _.isObject(data) && !_.isEmpty(data) && data.value ? data.value : idFiles;
            respData[this.get("id")] = idFiles;
            return respData;
        },
        /**
         * Validate a file mobile
         * @returns {FileMobile}
         */
        validate: function () {
            var isValid = false,
                value,
                data = this.getAppData();

            value = data[this.get("name")];
            if (PMDynaform.core.ProjectMobile && this.get("required") && this.get("enableValidate")) {
                if (value && _.isArray(value) && value.length > 0) {
                    isValid = true;
                }
            } else {
                isValid = true;
            }
            this.set("valid", isValid);
            return this;
        },
        getIDImage: function (index) {
            return this.attributes.images[index].id;
        },
        getBase64Image: function (index) {
            return this.attributes.images[index].value;
        },
        makeBase64Image: function (base64) {
            return "data:image/png;base64," + base64;
        },
        /**
         * Request Array Image Data
         * @param arrayImages
         * @returns [{id:"123456789...", base64: "sdhfg%4hd/f24g.."}] || []
         */
        remoteProxyData: function (arrayImages) {
            var project = this.get("project"),
                response,
                requestManager = project && PMDynaform.core.ProjectMobile ? project.getRequestManager() : null,
                respData = [],
                data;
            data = this.formatArrayImagesToSend(arrayImages);
            response = requestManager ? requestManager.imagesInfo(data) : project.webServiceManager.imagesInfo(data);
            respData = this.formatArrayImages(response);
            return respData;
        },
        /**
         * Format structure of the array of objects(files)
         * @param arrayImages
         * @returns {Array}
         */
        formatArrayImagesToSend: function (arrayImages) {
            var i,
                item,
                imageId,
                defaultSize = 100,
                dataToSend = [];
            for (i = 0; i < arrayImages.length; i += 1) {
                imageId = arrayImages[i];
                item = {};
                item.fileId = imageId;
                item.version =  1;
                if (PMDynaform.core.ProjectMobile) {
                    item.width = defaultSize;
                }
                dataToSend.push(item);
            }
            return dataToSend;
        },
        /**
         * Format response array
         * @param arrayImages
         * @returns {*}
         */
        formatArrayImages: function (arrayImages) {
            var i;
            for (i = 0; i < arrayImages.length; i += 1) {
                arrayImages[i].id = arrayImages[i]['fileId'];
                arrayImages[i].base64 = arrayImages[i]['fileContent'];
                delete arrayImages[i].fileId;
                delete arrayImages[i].fileContent;
            }
            return arrayImages;
        },
        remoteProxyDataMedia: function (id) {
            var prj = this.get("project"),
                url;
            url = prj.webServiceManager.getFullURLStreaming(id);
            return url;
        },
        urlFileStreaming: function (id) {
            var prj = this.get("project"),
                url,
                dataToSend;
            url = prj.webServiceManager.getFullURLStreaming(id);
            dataToSend = {
                id: id,
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
                        endPointFixed = endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);
                        endpoint = endPointFixed;
                    }
                }
            }
            return endPointFixed;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (data) {
            var view = this.get("view");
            if (data && view) {
                view.setFilesRFC(data);
            }
            return this;
        },
        /**
         * Get Array Files Image Control
         */
        getFiles: function () {
            return this.get("files");
        },
        /**
         * Set Array Files
         * @param arrayFiles
         * @returns {FileMobile}
         */
        setFiles: function (arrayFiles) {
            if (arrayFiles.length) {
                this.set("files", arrayFiles);
            }
            return this;
        },
        /**
         * Update data
         * @param arrayFiles
         * @returns {FileMobile}
         */
        updateData: function (arrayFiles) {
            var i,
                data,
                idFiles = [],
                max = _.isArray(arrayFiles) ? arrayFiles.length : 0;
            for (i = 0; i < max; i += 1) {
                idFiles.push(arrayFiles[i].id);
            }
            this.set("data", {
                value: idFiles,
                label: null
            });
            return this;
        },
        /**
         * Add Item to Array Files
         * @param item
         */
        addItemFile: function (item) {
            var arrayFiles = this.getFiles();
            if (item) {
                arrayFiles.push(item);
            }
            this.setFiles(arrayFiles);
            this.updateData(arrayFiles);
            return this;
        },
        /**
         * Change the file id with a "newID". Using the "oldId" to do that used only for offline purposes
         * @param oldId
         * @param newId
         */
        exchangeMobileDataId: function (oldId, newId){
            var dataArray = this.get('data').value,
                filesArray = this.get('files'),
                index = dataArray.indexOf(oldId);
            if (index >= 0) {
                dataArray[index] = newId;
                // force to update files id
                if (_.isArray(filesArray) && filesArray[index]) {
                   filesArray[index].id = newId;
                }
            }
            this.setFiles(filesArray);
            this.updateData(filesArray);
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.FileMobile", FileMobile);
}());

(function () {
    var GeoMobile = PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            type: "location",
            label: "Untitled label",
            mode: "edit",
            group: "form",
            labelButton: "Map",
            name: "name",
            colSpan: 12,
            colSpanLabel: 3,
            colSpanControl: 9,
            height: "auto",
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,
            geoData: null,
            interactive: true
        },
        initialize: function () {
            this.initControl();
            if (this.get("variable") && this.get("variable").trim().length !== 0) {
                this.set("name", this.get("variable"));
            }
        },
        initControl: function () {
            this.attributes.images = [];
            this.set("preview", true);
            return this;
        },
        isValid: function () {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getDataRFC: function () {
            var geoValue = this.attributes.geoData;
            if (geoValue == "null" || geoValue == null) {
                geoValue = null;
            } else {
                if (geoValue.imageId == "" || geoValue.imageId == null || typeof geoValue.imageId == "undefined") {
                    geoValue = geoValue;
                }
            }
            if (geoValue) {
                if (geoValue.data) {
                    delete geoValue.data;
                }
            }
            return {
                name: this.get("name"),
                value: geoValue
            };
        },
        getAppData: function () {
            var data = {},
                geoValue = this.attributes.geoData;
            if (geoValue) {
                if (geoValue.base64) {
                    delete geoValue.base64;
                }
            }
            data[this.get("name")] = geoValue;
            return data;
        },
        validate: function (attrs) {
        },
        /**
         * Get Image in Base64
         * @param id
         * @returns {*}
         */
        remoteProxyData: function (id) {
            var project = this.get("project");
            if (id) {
                return project.webServiceManager.imageInfo(id, 600);
            }
        },
        getImagesNetwork: function (location) {
            var prj = this.get("project"),
                url,
                restClient,
                endpoint,
                respData = {};
            endpoint = this.getEndpointVariables({
                type: "getImageGeo",
                keys: {
                    "{fileID}": location.imageId,
                    "{caseID}": prj.keys.caseID,
                }
            });
            url = prj.getFullURL(endpoint);
            restClient = new PMDynaform.core.Proxy({
                url: url,
                method: 'POST',
                data: {
                    fileId: location.imageId,
                    width: "600",
                    version: 1
                },
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
                        endPointFixed = endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);
                        endpoint = endPointFixed;
                    }
                }
            }
            return endPointFixed;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (data) {
            var view;
            if (data) {
                view = this.get("view");
                if (view) {
                    view.setLocation(data);
                }
            }
            return this;
        },
        /**
         * Get GeoData
         * @returns {*}
         */
        getGeoData: function () {
            return this.get("geoData");
        },
        /**
         * Set Geo Data
         * @param data
         * @returns {GeoMobile}
         */
        setGeoData: function (data) {
            if (data && typeof data === "object") {
                this.set("geoData", data);
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.GeoMobile", GeoMobile);
}());

(function () {
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
            colSpanLabel: 3,
            colSpanControl: 9,
            height: "auto",
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,
            codes: [],
            geoData: null,
            interactive: true
        },
        initialize: function () {
            this.initControl();
            if (this.get("variable") && this.get("variable").trim().length !== 0) {
                this.set("name", this.get("variable"));
            }
        },
        initControl: function () {
            this.attributes.codes = [];
            this.set("preview", true);
            return this;
        },
        isValid: function () {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getAppData: function () {
            var data = {};
            data[this.get("name")] = this.get("codes");
            return data;
        },
        addCode: function (newCode) {
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
                        endPointFixed = endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);
                        endpoint = endPointFixed;
                    }
                }
            }
            return endPointFixed;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (data) {
            var view;
            view = this.get("view");
            if (data && view) {
                view.setScannerCode(data);
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.model.Qrcode_mobile", Qrcode_mobile);
}());

(function () {
    var Signature_mobile = PMDynaform.model.Field.extend({
        defaults: {
            id: PMDynaform.core.Utils.generateID(),
            type: "signature",
            label: "Untitled label",
            mode: "edit",
            group: "form",
            labelButton: "Signature",
            name: "name",
            colSpanLabel: 3,
            colSpanControl: 9,
            colSpan: 12,
            height: "auto",
            value: "",
            required: false,
            hint: "",
            disabled: false,
            preview: false,
            valid: true,
            files: []
        },
        initialize: function () {
            this.initControl();
            if (this.get("id") && this.get("id").trim().length !== 0) {
                this.set("name", this.get("id"));
            }
        },
        initControl: function () {
            this.attributes.files = [];
            this.set("preview", true);
            return this;
        },
        isValid: function () {
            this.set("valid", this.get("validator").get("valid"));
            return this.get("valid");
        },
        getAppData: function () {
            var i,
                data = {},
                response = [],
                signatureValue = this.attributes.files;
            for (i = 0; i < signatureValue.length; i++) {
                if (typeof signatureValue[i].id != "undefined" && signatureValue[i].id != null) {
                    response.push(signatureValue[i].id);
                }
            }
            data[this.get("name")] = response;
            return data;
        },
        getDataCustom: function () {
            var signatureValue = this.attributes.files;
            return {
                name: this.get("name"),
                value: signatureValue
            };
        },
        validate: function (attrs) {

        },
        remoteProxyData: function (id) {
            return this.get("project").webServiceManager.imageInfo(id, 300);
        },
        remoteGenerateID: function (location) {
            var prj = this.get("project"),
                url,
                restClient,
                endpoint,
                respData = {};
            endpoint = this.getEndpointVariable({
                type: "generateImageGeo",
                keys: {
                    "{caseID}": prj.keys.caseID
                }
            });
            url = prj.getFullURL(endpoint);
            restClient = new PMDynaform.core.Proxy({
                url: url,
                method: 'POST',
                data: location,
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
                        endPointFixed = endpoint.replace(new RegExp(variable, "g"), urlObj.keys[variable]);
                        endpoint = endPointFixed;
                    }
                }
            }
            return endPointFixed;
        },
        /**
         * setAppData: Sets the corresponding data that is obtained from the
         * service to the component
         * @param data {object} valid data for this component
         */
        setAppData: function (data) {
            var view = this.get("view");

            if (data && view) {
                view.setSignature(data);
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.model.Signature_mobile", Signature_mobile);
}());

(function () {
    var MultipleFileModel = PMDynaform.model.Field.extend({
        defaults: {
            skin: "neoclassic",
            href: "#",
            colSpanLabel: 3,
            colSpanControl: 9,
            colSpan: 12,
            disabled: false,
            group: "form",
            height: "100%",
            hint: "",
            id: PMDynaform.core.Utils.generateID(),
            label: "Untitled label",
            labelButton: "Choose Files",
            mode: "edit",
            name: PMDynaform.core.Utils.generateName("file"),
            required: false,
            type: "file",
            valid: true,
            validator: null,
            value: "",
            gridDetail: "",
            gridDetailArray: [],
            toRemoveArray: [],
            multiFileCount: 0,
            data: {
                value: [],
                label: []
            },
            file: {
                title: "",
                extension: "",
                size: ""
            },
            types: {
                "264": "video",
                "bmp": "image",
                "dib": "image",
                "dng": "image",
                "dt2": "image",
                "emf": "image",
                "gif": "image",
                "ico": "image",
                "icon": "image",
                "jpeg": "image",
                "jpg": "image",
                "pcx": "image",
                "pic": "image",
                "png": "image",
                "psd": "image",
                "raw": "image",
                "tga": "image",
                "thm": "image",
                "tif": "image",
                "tiff": "image",
                "wbmp": "image",
                "wdp": "image",
                "webp": "image",
                "7z": "compress",
                "7zip": "compress",
                "arc": "compress",
                "arj": "compress",
                "bin": "compress",
                "cab": "compress",
                "cbr": "compress",
                "cbz": "compress",
                "cso": "compress",
                "dlc": "compress",
                "gz": "compress",
                "gzip": "compress",
                "jar": "compress",
                "rar": "compress",
                "tar": "compress",
                "tar.gz": "compress",
                "tgz": "compress",
                "zip": "compress",
                "3ga": "audio",
                "aac": "audio",
                "amr": "audio",
                "ape": "audio",
                "asf": "audio",
                "asx": "audio",
                "cda": "audio",
                "dvf": "audio",
                "flac": "audio",
                "gp4": "audio",
                "gp5": "audio",
                "gpx": "audio",
                "logic": "audio",
                "m4a": "audio",
                "m4b": "audio",
                "m4p": "audio",
                "midi": "audio",
                "mp3": "audio",
                "ogg": "audio",
                "pcm": "audio",
                "snd": "audio",
                "uax": "audio",
                "wav": "audio",
                "wma": "audio",
                "wpl": "audio",
                "numbers": "xls",
                "ods": "xls",
                "sdc": "xls",
                "sxc": "xls",
                "xls": "xls",
                "xlsm": "xls",
                "xlsx": "xls",
                "pdf": "pdf",
                "1st": "txt",
                "alx": "txt",
                "asp": "txt",
                "csv": "txt",
                "eng": "txt",
                "htm": "txt",
                "html": "txt",
                "log": "txt",
                "lrc": "txt",
                "lst": "txt",
                "nfo": "txt",
                "opml": "txt",
                "plist": "txt",
                "pts": "txt",
                "reg": "txt",
                "rep": "txt",
                "srt": "txt",
                "sub": "txt",
                "tbl": "txt",
                "text": "txt",
                "txt": "txt",
                "xml": "txt",
                "xsd": "txt",
                "xsl": "txt",
                "xslt": "txt",
                "odp": "ppt",
                "pot": "ppt",
                "potx": "ppt",
                "pps": "ppt",
                "ppsx": "ppt",
                "ppt": "ppt",
                "pptm": "ppt",
                "pptx": "ppt",
                "sdd": "ppt",
                "key": "ppt",
                "keynote": "ppt",
                "xps": "ppt",
                "3g2": "video",
                "3gp": "video",
                "avi": "video",
                "bik": "video",
                "dash": "video",
                "dat": "video",
                "dvr": "video",
                "flv": "video",
                "h264": "video",
                "m2t": "video",
                "m2ts": "video",
                "m4v": "video",
                "mkv": "video",
                "mod": "video",
                "mov": "video",
                "mp4": "video",
                "mpeg": "video",
                "mpg": "video",
                "mswmm": "video",
                "mts": "video",
                "ogv": "video",
                "prproj": "video",
                "rec": "video",
                "rmvb": "video",
                "swf": "video",
                "tod": "video",
                "tp": "video",
                "ts": "video",
                "vob": "video",
                "webm": "video",
                "wmv": "video",
                "abw": "doc",
                "aww": "doc",
                "cnt": "doc",
                "doc": "doc",
                "docm": "doc",
                "docx": "doc",
                "dot": "doc",
                "dotm": "doc",
                "dotx": "doc",
                "epub": "doc",
                "ind": "doc",
                "indd": "doc",
                "odf": "doc",
                "odt": "doc",
                "ott": "doc",
                "oxps": "doc",
                "pages": "doc",
                "pmd": "doc",
                "pub": "doc",
                "pwi": "doc",
                "rtf": "doc",
                "wpd": "doc",
                "wps": "doc",
                "wri": "doc"
            },
            files: [],
            fileCollection: null,
            formData: null,
            fileArray: [],
            messageValidations: "This field is required",
            enableValidate: true
        },
        initialize: function (options) {
            if (_.isString(this.get("variable")) && this.get("variable") !== "") {
                this.set("name", this.get("variable") ? this.get("variable") : this.get("id"));
            }
            this.set('fileArray', []);
            this.set('gridDetailArray', []);
            this.set('toRemoveArray', []);
            this.set('multiFileCount', 0);
            this.set('extensions', this.processPropertyExtension(this.get("extensions")));
            this.set("fileCollection", new PMDynaform.file.FileCollection());
            this.listenTo(this.get("fileCollection"), 'destroy', this.destroyModel);
            this.listenTo(this.get("fileCollection"), 'add', this.addModel);
            this.set("validator", new Backbone.Model({
                message: {
                    required: this.get("requiredFieldErrorMessage") || this.get("messageValidations")
                }
            }));
            return this;
        },
        /**
         * Function to pre-process the extension before input into template
         * @param ext{string}
         * @returns {string}
         */
        processPropertyExtension: function (ext) {
            var resp = "",
                val,
                arr = [];
            if (_.isString(ext)) {
                $.each(ext.split(","), function (index, value) {
                    val = value.replace(/\*./g, ".");
                    val = val.replace(/\.\*/g, "*");
                    arr.push(val);
                });
                resp = arr.join(",");
            }
            return resp;
        },
        isValid: function () {

        },
        /**
         * Function for set data RFC
         * @param data
         * @returns {FileUpload}
         */
        setData: function (data) {
            var value,
                index;
            try {
                value = _.isString(data.value) ? JSON.parse(data.value) : data.value;
                for (index = 0; index < data.value.length; index += 1) {
                    this.addFileModelByJSON(data.value[index]);
                }
            } catch (e) {
                console.error(e);
            }
        },
        /**
         * Function for get data for Rest RFC
         * @returns {{name: *, value}}
         */
        getData: function () {
            var data;
            data = this.get("fileCollection").getData();
            return {
                name: this.get("name"),
                value: data
            };
        },
        /**
         * RFC
         * @returns {{name: *, value: *}}
         */
        getKeyLabel: function () {
            var names;
            names = this.get("fileCollection").getNames();
            return {
                name: this.get("id") ? this.get("id").concat("_label") : "",
                value: names
            }
        },
        /**
         * Validate a file upload
         * @returns {FileUpload}
         */
        validate: function () {
            var isValid = true,
                files = this.get("fileCollection"),
                data = this.get("data");
            if (this.get("enableValidate")) {
                if (this.get("required") === true && !files.validate()) {
                    isValid = false;
                }
            } else {
                isValid = true;
            }
            this.set("valid", isValid);
            return this;
        },
        /**
         * Add file based from file wizard browser RFC
         * @param file
         * @returns {*}
         */
        addFileModel: function (file) {
            var files = this.get("fileCollection"),
                fileSize,
                fileExtension,
                fileModel;

            fileModel = files.add(new PMDynaform.file.FileModel({
                file: file,
                mode: "edit",
                parent: this,
                project: this.get("project"),
                form: this.get("form"),
                size: this.get("size"),
                sizeUnity: this.get("sizeUnity"),
                extensions: this.get("extensions")
            }));

            fileSize = fileModel._isFileSizeValid(file, this.get("size"), this.get("sizeUnity"));
            fileExtension = fileModel._isFileExtensionValid(file, this.get("extensions"));

            if (!fileSize) {
                fileModel.set("isValid", fileSize);
                fileModel.set("errorSize", true);
            }
            if (!fileExtension) {
                fileModel.set("isValid", fileExtension);
                fileModel.set("errorType", true);
            }
            files.updateIndex();
            return fileModel;
        },
        /**
         * Add file based in JSON from setData RFC
         * @param file
         * @returns {*}
         */
        addFileModelByJSON: function (file) {
            var files = this.get("fileCollection"),
                fileModel;

            fileModel = files.add(new PMDynaform.file.FileModel({
                appDocUid: file.appDocUid,
                file: {
                    name: file.name
                },
                version: file.version,
                mode: "view",
                parent: this,
                project: this.get("project"),
                form: this.get("form")
            }));
            fileModel.set("index", files.indexOf(fileModel));
            return fileModel;
        },
        /**
         * Function for get value
         * @returns {string}
         */
        getValue: function () {
            var value = "";
            return value;
        },
        destroyModel: function (model) {
            var i,
                files = this.get("fileCollection");
            files.updateIndex();
            this.get("toRemoveArray").push(model);
            this.createRemovedHiddens(model);
            this.get('view').removeHiddens();
            if (files.models && files.models.length) {
                for (i = 0; i < files.models.length; i += 1) {
                    this.get('view').createHiddenByModel(files.models[i]);
                }
            }
            this.updateData();
            return this;
        },
        updateGridDetail: function (fileModel) {
            var textDetail = '',
                gridDetail = this.get('gridDetailArray'),
                i;
            if (_.isArray(gridDetail)) {
                if (fileModel.get("isValid")) {
                    gridDetail.push(fileModel.get('file')['name']);
                    for (i = 0; i < gridDetail.length; i += 1) {
                        textDetail += (i > 0 ? ', ' : '') + gridDetail[i];
                    }
                    this.set('gridDetail', textDetail);
                }
            }
        },
        /**
         * Removes the removed file in the Grid Detail info considering colon
         * and if there is one file in multiple file field
         * @param fileModel
         */
        removeFromGridDetail: function (fileModel) {
            var textDetail = '',
                gridDetail = this.get('gridDetailArray'),
                i;
            if (_.isArray(gridDetail) && fileModel.get('index') !== null) {
                // remove from array
                gridDetail.splice(fileModel.get('index'), 1);
                if (gridDetail.length > 0) {
                    for (i = 0; i < gridDetail.length; i += 1) {
                        textDetail += (i > 0 ? ', ' : '') + gridDetail[i];
                    }
                } else {
                    textDetail = 'Choose File';
                }
                this.set('gridDetail', textDetail);
            }
        },
        /**
         * Create a inputs hidden from a files for delete in Back End
         * @param fileModel
         */
        createRemovedHiddens: function (fileModel) {
            this.get('view').createAllHiddens('add');
            if (fileModel.get("isValid")) {
                this.get('view').createDelHiddens(fileModel);
            }
        },
        /**
         * Reset the input Files in views for load a custom files
         * @param fileModel
         */
        resetInputFile: function () {
            if (this.get('group') !== 'grid') {
                this.get('view').resetInputFile();
            } else {
                this.get('view').resetInputFileModal();
            }
            return this;
        },
        /**
         * Update the data for getData from Form
         */
        updateData: function () {
            this.set("data", {
                value: this.get("fileCollection").getData(),
                label: this.get("fileCollection").getNames()
            });
        },
        /**
         * Delete files in a field
         * @returns {MultipleFileModel}
         */
        deleteFiles: function () {
            this.get("fileCollection").deleteFiles();
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.file.MultipleFileModel", MultipleFileModel);
}());

(function () {
    var MultipleFileView = PMDynaform.view.Field.extend({
        template: _.template($("#tpl-multiplefile").html()),
        templateInGrid: _.template($("#tpl-multiplefile-grid").html()),
        templateInGridField: _.template($("#tpl-multiplefile-grid-field").html()),
        validator: null,
        $hiddenFile: null,
        $hiddenValue: null,
        $hiddenLabel: null,
        $data: null,
        events: {
            "click .btn-uploadfile": "onClickButton",
            "click .pm-multiplefile-upload": "onClickUploadModal"
        },
        initialize: function (options) {
            this.form = options.form ? options.form : null;
            this.hiddenFile = null;
            this.$hiddenFile = null;
            this.$hiddenValue = null;
            this.$hiddenLabel = null;
            this.uploadModalModel = null;
            this.uploadModalView = null;
            this.model.on('change:gridDetail', this.gridDetailChange, this);
            return this;
        },
        /**
         * Click button for choose files
         * @param event
         * @returns {FileUpload}
         */
        onClickButton: function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (this.model.get("mode") === "edit") {
                this.openFileWizardWeb();
            }
            return this;
        },
        onClickUploadModal: function (event) {
            var that = this;
            event.preventDefault();
            event.stopPropagation();
            this.uploadModalModel = new PMDynaform.file.UploadModalModel({
                parent: that.model
            });
            this.uploadModalView = new PMDynaform.file.UploadModalView({
                model: this.uploadModalModel
            });
            this.uploadModalView.renderForm(that.model.get('fileCollection'));
            return this;
        },
        openFileWizardWeb: function (event) {
            this.$el.find(".pmdynaform-multiplefile-control input:file").trigger("click");
            return this;
        },
        /**
         * Function for render field
         * @returns {FileUpload}
         */
        render: function () {
            if (this.model.get('group') === 'grid') {
                this.$el.html(this.templateInGrid(this.model.toJSON()));
            } else {
                this.$el.html(this.template(this.model.toJSON()));
                this.$data = this.$el.find(".file-upload-box");
                this.$hiddenFile = this.$el.find("input:file");
                this.$data.hide();
                this.eventsBinding();
            }
            this.loadDataFromAppData();
            this.model.updateData();
            this.populateItemsPrintMode(this.getKeyLabel());
            PMDynaform.view.Field.prototype.render.apply(this, arguments);
            return this;
        },
        loadDataFromAppData: function () {
            var data,
                name;
            if (this.project.mobileDataControls) {
                data = this.project.mobileDataControls;
                // for purposes of web grids
                if (this.model.get('group') === 'grid'
                    && data[this.parent.model.get('id')]
                    && data[this.parent.model.get('id')][this.model.get('row') + 1]
                    && data[this.parent.model.get('id')][this.model.get('row') + 1][this.model.get('columnId')]) {
                    this.setData({
                        value: data[this.parent.model.get('id')][this.model.get('row') + 1][this.model.get('columnId')],
                        label: data[this.parent.model.get('id')][this.model.get('row') + 1][this.model.get('columnId')]
                    });
                }
                //end  for purposes of web grids
                if (data[this.model.get("name")]) {
                    name = this.model.get("name");
                    this.setData({
                        value: data[name],
                        label: data[name + "_label"] ? data[name + "_label"] : data[name]
                    });
                }
            }
            return this;
        },
        /**
         * Function for get data from model
         * @returns {*}
         */
        getData: function () {
            return this.model.getData();
        },
        getKeyLabel: function () {
            return this.model.getKeyLabel();
        },
        /**
         * Change ID, this function is used when the device use a async calls to Rest
         * @param arrayNew
         * @returns {FileUpload}
         */
        changeID: function (arrayNew) {
            if (_.isArray(arrayNew)) {
                this.model.changeID(arrayNew[0]);
            }
            return this;
        },
        /**
         * This function execute with setData2 from form Panel
         * @param data
         * @returns {FileUpload}
         */
        setData: function (data) {
            var value,
                index;
            try {
                value = _.isString(data.value) ? JSON.parse(data.value) : data.value;
                for (index = 0; index < value.length; index += 1) {
                    if (this.model.get('group') === 'grid') {
                        this.createGridMultiFile(value, index);
                    } else {
                        this.createFormMultiFile(value, index);
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },
        /**
         * Creates into a form a file model, creates all hiddens related to file
         * append the html tag to parent container
         * @param options
         * @param index
         * @returns {MultipleFileView}
         */

        createFormMultiFile: function (options, index) {
            var model,
                view,
                box = this.$el.find(".pmdynaform-multiplefile-box");
            model = this.model.addFileModelByJSON(options[index]);
            view = new PMDynaform.file.FileView({
                model: model,
                versionable: this.model.get("enableVersioning")
            });
            view.render();
            view.createHiddenForProperty();
            box.append(view.$el);
            return this;
        },
        /**
         * Call hiddens creator, an updates the grid info label.
         * @param options
         * @param index
         * @returns {MultipleFileView}
         */
        createGridMultiFile: function (options, index) {
            var model;
            model = this.model.addFileModelByJSON(options[index]);
            this.model.updateGridDetail(model);
            this.createHiddenByModel(model);
            return this;
        },
        /**
         * trigger to update Detail info in a grid row
         */
        gridDetailChange: function () {
            var htmlOutput = this.templateInGridField(this.model.toJSON()),
                box = this.$el.find(".pm-multiplefile-grid");
            box.html(htmlOutput);
        },
        /**
         * Validate a File Upload
         * @returns {FileUpload}
         */
        validate: function () {
            if (this.model.get("mode") !== "view") {
                if (this.validator) {
                    this.validator.$el.remove();
                    if (_.isFunction(this.removeStyleError)) {
                        this.removeStyleError();
                    }
                }
                if (_.isFunction(this.model.validate)) {
                    this.model.validate();
                }
                if (!this.model.get("valid")) {
                    this.validator = new PMDynaform.view.Validator({
                        model: this.model.get("validator")
                    });
                    if (this.model.get('group') === 'grid') {
                        this.$el.find(".pm-multiplefile-grid").append(this.validator.$el);
                    } else {
                        this.$el.find(".pmdynaform-field-control").append(this.validator.$el);
                    }

                    if (_.isFunction(this.applyStyleError)) {
                        this.applyStyleError();
                    }
                }
            }
            return this;
        },
        /**
         * This function apply style error in this field
         * @returns {FileUpload}
         */
        applyStyleError: function () {
            this.$el.addClass("has-error has-feedback");
            return this;
        },
        /**
         * THis function remove style error in this field
         * @returns {FileUpload}
         */
        removeStyleError: function () {
            this.$el.removeClass('has-error has-feedback');
            return this;
        },
        /**
         * Add events to view
         * @returns {MultipleFileView}
         */
        eventsBinding: function () {
            var that = this,
                dropBox = this.$el.find(".pmdynaform-file-dropbox");

            this.$hiddenFile.on('change', this.changeFileControl());
            dropBox.on("drop", this.drop());
            dropBox.on('dragover', this.dragOver());
            dropBox.on('dragleave', this.dragLeave());
            return this;
        },
        /**
         * This function is trigger when select a file in wizard File browser
         * @returns {Function}
         */
        changeFileControl: function () {
            var that = this;
            return function (event, ui) {
                var files;
                event.preventDefault();
                event.stopPropagation();
                files = event.target.files;
                that.processFiles(files);
                event.target.value = "";
                that.$el.find(that.hiddenFile).remove();
                return false;
            };
        },
        drop: function () {
            var that = this;
            return function (event) {
                var files;
                event.preventDefault();
                event.stopPropagation();
                files = event.originalEvent.dataTransfer.files;
                that.processFiles(files);
                return false;
            };
        },
        dragLeave: function () {
            var that = this;
            return function () {
                return false;
            };
        },
        dragOver: function () {
            var that = this;
            return function () {
                return false
            };
        },
        /**
         * Add the file to the model multipleFile
         * @param files
         * @returns {MultipleFileView}
         */
        processFiles: function (files) {
            var index = 0,
                fileView,
                fileModel,
                box = this.$el.find(".pmdynaform-multiplefile-box");
            if (files.length) {
                for (index = 0; index < files.length; index += 1) {
                    fileModel = this.model.addFileModel(files[index], "edit");
                    //create an instance of a file
                    fileView = new PMDynaform.file.FileView({
                        versionable: this.model.get("enableVersioning"),
                        model: fileModel,
                        loading: true
                    });
                    //fire wizard flag
                    fileModel.set('fromWizard', true);
                    fileView.render();
                    box.append(fileView.$el);
                    fileModel.fileUpload();
                    fileModel.set('fromWizard', false);
                }
            }
            this.validate();
            this.populateItemsPrintMode(this.model.getKeyLabel());
            return this;
        },
        /**
         * Function for get value from model
         * @returns {*}
         */
        getValue: function () {
            return this.model.getValue();
        },
        /**
         * update the files counter
         * @param action
         * @returns {MultipleFileView}
         */
        createAllHiddens: function (action) {
            var files = this.model.get("fileCollection"),
                i;
            this.removeHiddens();
            switch (action) {
                case 'add':
                    if (files.models && files.models.length) {
                        for (i = 0; i < files.models.length; i += 1) {
                            if (files.models[i].get('isValid')) {
                                this.model.get('view').createHiddenByModel(files.models[i]);
                            }
                        }
                    }
                    break;
                case 'remove':
                    break;
                default:
                    //TODO Actually have not the default behavior
                    break;
            }
            this.model.updateData();
            return this;
        },
        /**
         * Create a inputs hidden to make the object &b send the post HTML
         * @returns {FileView}
         */
        createHiddenByModel: function (model) {
            var prop,
                hidden,
                name,
                nameLabel,
                hiddenLabel,
                data = model.getData();
            if (model.get("isValid")) {
                for (prop in data) {
                    if (data.hasOwnProperty(prop)) {
                        if (this.model.get('group') === 'grid') {
                            name = this.model.get('nameToPostControl') + "[" + model.get("index") + "]" + "[" + prop + "]";
                        } else {
                            name = "form[" + model.getNameFileParent() + "]" + "[" + model.get("index") + "]" + "[" + prop + "]";
                        }
                        hidden = $("<input>", {name: name, type: "hidden", value: data[prop]});
                        this.$el.append(hidden);
                        this.model.get('fileArray').push(hidden);
                    }

                }
                if (this.model.get('group') === 'grid') {
                    nameLabel = this.model.get('nameToPostLabelControl') + "[" + model.get("index") + "]";
                    hiddenLabel = $("<input>", {name: nameLabel, type: "hidden", value: model.get('file')['name']});
                    this.$el.append(hiddenLabel);
                    this.model.get('fileArray').push(hiddenLabel);
                }

            }
            return this;
        },
        /**
         * Remove the inputs hidden fron a file
         * @returns {FileView}
         */
        removeHiddens: function () {
            var index;
            if (_.isArray(this.model.get('fileArray'))) {
                for (index = 0; index < this.model.get('fileArray').length; index += 1) {
                    this.model.get('fileArray')[index].remove();
                }
            }
            return this;
        },
        /**
         * Create hiddes to send the deleted files by POST
         * @param model
         * @returns {MultipleFileView}
         */
        createDelHiddens: function (model) {
            var data = model.getData(),
                prop,
                prj = this.model.get("project"),
                hidden,
                name;
            for (prop in data) {
                if (data.hasOwnProperty(prop)) {
                    name = this.createNameforHidden(prop, model);
                    hidden = $("<input>", {name: name, type: "hidden", value: data[prop]});
                    if (prj) {
                        prj.view.$el.find("form").append(hidden);
                    }
                }
            }
            return this;
        },
        /**
         * Reset the input file html tag for reset properties
         */
        resetInputFile: function () {
            var nInput = $("<input>", {
                type: "file",
                style: "display:none",
                multiple: "multiple",
                accept: this.model.get("extensions")
            });
            this.$hiddenFile.before(nInput).remove();
            this.$hiddenFile = nInput;
            this.eventsBinding();
            return this;
        },
        /**
         * Reset the input file in modal case Grid
         */
        resetInputFileModal: function () {
            this.uploadModalView.resetInputFile();
            return this;
        },
        /**
         * Create the name string for create hiddens to delete files in form & send to BackEnd
         * @param prop
         * @returns {string}
         */
        createNameforHidden: function (prop, model) {
            var index,
                name = "",
                indexRow = "",
                varName = 'form[__VARIABLE_DOCUMENT_DELETE__]',
                propName = "[" + prop + "]",
                colName = "[" + this.model.get("columnId") + "]",
                toRemove = this.model.get('toRemoveArray'),
                parent = this.parent || null,
                indexArrayRemove = this.model.get("toRemoveArray").length - 1,
                column;
            if (this.model.get("group") === "grid") {
                column = this.model.get("columnId");
                index = parent.getColumnFileDelete(column, model.getNameFileParent());
                indexRow = "[" + index.row + "]";
                name = varName + "[" + this.parent.model.get("name") + "]" + indexRow + colName + "[" + indexArrayRemove + "]" + propName;
            } else {
                name = varName + "[" + model.getNameFileParent() + "]" + "[" + toRemove.length + "]" + propName;
            }
            return name;
        },
        /**
         * Populate MultipleFile on print Mode
         * @param arrayItems
         */
        populateItemsPrintMode: function (arrayItems) {
            var i,
                max,
                containerPrint = this.$el.find(".content-print"),
                itemsMultipleFile = arrayItems.value;
            containerPrint.empty();
            for (i = 0, max = itemsMultipleFile.length; i < max; i += 1) {
                containerPrint.append("<li>" + itemsMultipleFile[i] + "</li>");
            }
        }
    });

    PMDynaform.extendNamespace("PMDynaform.file.MultipleFileView", MultipleFileView);
}());

"use strict";
(function () {
    var FileModel = Backbone.Model.extend({
        defaults: {
            xhr: null,
            file: null,
            percentage: null,
            appDocUid: null,
            index: null,
            updateIndex: false,
            version: 1,
            isValid: true,
            fromWizard: false,
            urlBase: "{server}/sys{ws}/en/{skin}/cases/cases_ShowDocument?a={docUID}&v=1",
            linkService: "showDocument",
            errorSize: false,
            errorType: false
        },
        initialize: function (options) {
            return this;
        },
        /**
         * Return a object with basic propeties to save data
         * @returns {{appDocUid: *, name: *, version: number}}
         */
        getData: function () {
            return {
                "appDocUid": this.get("appDocUid"),
                "name": this.get("file").name,
                "version": this.get("version")
            };
        },
        /**
         * Return the appDocUid from File
         * @returns {{appDocUid: *}}
         */
        getValue: function () {
            return {
                appDocUid: this.get("appDocUid")
            };
        },
        /**
         * Return the name from file
         * @returns {*}
         */
        getName: function () {
            return this.get("file").name;
        },
        /**
         * Prepare the data form multipart to save in Rest Endpoint
         * @param file
         * @returns {FormData|*}
         */
        prepareDataToUploadMultipart: function (file) {
            var formData;
            formData = new FormData();
            formData.append('form[]', file);
            return formData;
        },
        /**
         * Prepare the data for consume the Endpoint Upload
         * @param file
         * @returns {Array}
         */
        prepareDataToUpload: function (file) {
            var arrayResp = [],
                parent = this.get("parent"),
                type = parent && parent.get("inp_doc_uid") ? "INPUT" : null;

            arrayResp.push({
                "name": file.name,
                "fieldName": this.get("parent").get("id"),
                "docUid": parent.get("inp_doc_uid"),
                "appDocType": type
            });
            return arrayResp;
        },
        /**
         * Execute the upload Endpoint
         * @returns {FileModel}
         */
        fileUpload: function () {
            var project = this.get("project"),
                index = 0,
                that = this;

            if (project.webServiceManager && this.get("isValid")) {
                project.webServiceManager.upload(this.prepareDataToUpload(this.get("file")), function (err, data) {
                    if (!err) {
                        var appDoc;
                        appDoc = data.data[index].appDocUid;
                        that.set("appDocUid", appDoc);
                        that.fileUploadMultipart();
                    }
                });
            } else {
                this.set("updateIndex", true);
                this.get('parent').set('multiFileCount', this.get('parent').get('multiFileCount') + 1);
            }
            return this;
        },
        /**
         * Execute the uploadMultipart Endpoint
         * @returns {FileModel}
         */
        fileUploadMultipart: function () {
            var project = this.get("project"),
                xhr,
                formData,
                that = this;

            formData = this.prepareDataToUploadMultipart(this.get("file"));
            if (_.isFunction(project.webServiceManager.uploadMultipart)) {
                xhr = project.webServiceManager.uploadMultipart(this.get("appDocUid"), formData, function (err, data) {
                    that.trigger("upload_complete");
                }, this.progressValue());
                this.set("xhr", xhr);
            }
            return this;
        },
        /**
         * Parse the name to obtain the extension
         * @param path
         * @returns {*}
         */
        parseExtension: function (path) {
            var name,
                ext,
                indexExt = 1,
                parseExt = /(?:\.)([0-9a-z]+$)/i;
            name = this.parseName(path);
            ext = parseExt.exec(name);
            return (ext && _.isArray(ext)) ? ext[indexExt] : "";
        },
        /**
         * Parse name from a URL dir path
         * @param path
         * @returns {*}
         */
        parseName: function (path) {
            var name,
                parsePath = /^.*[\\\/]/;
            name = path.replace(parsePath, '');
            return name;
        },
        /**
         * Update the percentage upload
         * @returns {Function}
         */
        progressValue: function () {
            var that = this;
            return function (e) {
                var max, current, percentage;
                if (e.lengthComputable) {
                    max = e.total;
                    current = e.loaded;
                    percentage = (current * 100) / max;
                    that.set("percentage", percentage);
                }
            }
        },
        /**
         * Get Name from multipleFile Model
         * @returns {*}
         */
        getNameFileParent: function () {
            return this.get("parent").get("name");
        },
        /**
         * Perform a file extension validation.
         * @param file
         * @param {String} extensions A bunch of extensions separated by comma.
         * @returns {boolean}
         * @private
         */
        _isFileExtensionValid: function (file, extensions) {
            var allowedExtensions = extensions.trim().toLowerCase(),
                fileExtension;

            if (allowedExtensions === "" || allowedExtensions === "*") {
                return true;
            }

            allowedExtensions = allowedExtensions.split(",").map(function (i) {
                return i.replace(/\./, "").trim();
            });

            fileExtension =  file.name.split(".").pop().toLowerCase();

            return allowedExtensions.indexOf(fileExtension) >= 0;
        },
        /**
         * Returns the amount in bytes of a value in another unit.
         * @param {Number} amount
         * @param {String} units A string that specifies the input amount is in, it only supports 'MB' and 'KB'.
         * @returns {*}
         * @private
         */
        _getSizeInBytes: function (amount, units) {
            switch (units) {
                case 'MB':
                    amount *= 1024;
                case 'KB':
                    amount *= 1024;
                    break;
                default:
                    throw new Error('_getSizeInBytes(): Invalid \"units\" parameter');
            }

            return amount;
        },
        /**
         * Verifies if the file accomplish the size limit.
         * @param {File} file The file to be verified.
         * @param {Number} maxAllowedSize The amount size.
         * @param {String} unit The unit the amount is in, at the moment it only supports 'MB' and 'KB'.
         * @returns {boolean}
         * @private
         */
        _isFileSizeValid: function (file, maxAllowedSize, unit) {
            var maxAllowedSize = this._getSizeInBytes(maxAllowedSize, unit);

            return maxAllowedSize === 0 || file.size <= maxAllowedSize;
        },
        /**
         * Convert bytes to other unit rounded to two decimals
         * @param qBytes
         * @param unit
         * @returns {number}
         */
        convertTo: function (qBytes, unit) {
            var units = ["bytes", "KB", "MB", "GB"],
                constant = 1024,
                factor = 1,
                exponent = 0,
                decimals = 2,
                result = 0;
            if (qBytes && unit) {
                exponent = units.indexOf(unit.toUpperCase());
                factor = Math.pow(constant, exponent);
                result = Number((qBytes/factor).toFixed(decimals));
            }
            return result;
        },
        /**
         * Parse de Allow Extensions
         * @returns {*}
         */
        parseAllowExtensions: function () {
            var extensions = this.get("extensions").trim(),
                allowExtension;
            if (extensions) {
                extensions = extensions.replace(/\s*\./g, '');
                allowExtension = extensions.split(",");
            }
            return allowExtension;
        },
        /**
         * Return a object to render with a url
         * @returns {{ext: *, icon: *, name: *, href: *}|*}
         */
        getLinkDownload: function () {
            var resp = {},
                proj = this.get("project");
            if (proj && proj.webServiceManager && proj.webServiceManager.showDocument) {
                resp = proj.webServiceManager.showDocument({
                    uid: this.get("appDocUid"),
                    type: this.get("linkService"),
                    version: this.get("version")
                });
            }
            return resp;
        },
        /**
         * Calls to endpoint of download
         * @param version {string}
         * @param callback {function}
         * @returns {FileModel}
         */
        downloadFileVersion: function (version, callback) {
            var project = this.get("project"),
                webServiceManager = project.webServiceManager,
                dataFile = {
                    docUID: this.get("appDocUid"),
                    version: version
                };
            webServiceManager.downloadFile(dataFile, function (data) {
                callback(data);
            });
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.file.FileModel", FileModel);
}());

(function () {
    var FileView = Backbone.View.extend({
        template: _.template($("#tpl-multiplefile-file").html()),
        loaderTemplate: _.template($("#tpl-multiplefile-version-loader").html()),
        $hiddens: [],
        events: {
            "click .pmdynaform-mfile > .pmdynaform-mfile-actions li[data-action=delete] a": "onClickDelete",
            "click .pmdynaform-mfile > .pmdynaform-mfile-actions li[data-action=abort] a": "onClickCancelUpload",
            "click .pmdynaform-mfile > .pmdynaform-mfile-actions li[data-action=upload] a": "_fireUploadDialog",
            "click .pmdynaform-mfile > .pmdynaform-mfile-actions li[data-action=list] a": "_listVersions",
            "click .pmdynaform-mfile > .pmdynaform-mfile-actions li[data-action=unlist] a": "_unlistVersions",
            "click .pmdynaform-mfile-versions li[data-action=download] a": "_downloadVersion",
            "change .pmdynaform-mfile > .pmdynaform-mfile-input": "_startUpload"
        },
        iconsMap: {
            "bmp": "file-image-o",
            "dib": "file-image-o",
            "dng": "file-image-o",
            "dt2": "file-image-o",
            "emf": "file-image-o",
            "gif": "file-image-o",
            "ico": "file-image-o",
            "icon": "file-image-o",
            "jpeg": "file-image-o",
            "jpg": "file-image-o",
            "pcx": "file-image-o",
            "pic": "file-image-o",
            "png": "file-image-o",
            "psd": "file-image-o",
            "raw": "file-image-o",
            "tga": "file-image-o",
            "thm": "file-image-o",
            "tif": "file-image-o",
            "tiff": "file-image-o",
            "wbmp": "file-image-o",
            "wdp": "file-image-o",
            "webp": "file-image-o",
            "7z": "file-zip-o",
            "7zip": "file-zip-o",
            "arc": "file-zip-o",
            "arj": "file-zip-o",
            "bin": "file-zip-o",
            "cab": "file-zip-o",
            "cbr": "file-zip-o",
            "cbz": "file-zip-o",
            "cso": "file-zip-o",
            "dlc": "file-zip-o",
            "gz": "file-zip-o",
            "gzip": "file-zip-o",
            "jar": "file-zip-o",
            "rar": "file-zip-o",
            "tar": "file-zip-o",
            "tar.gz": "file-zip-o",
            "tgz": "file-zip-o",
            "zip": "file-zip-o",
            "3ga": "file-audio-o",
            "amr": "file-audio-o",
            "ape": "file-audio-o",
            "asf": "file-audio-o",
            "asx": "file-audio-o",
            "cda": "file-audio-o",
            "dvf": "file-audio-o",
            "flac": "file-audio-o",
            "gp4": "file-audio-o",
            "gp5": "file-audio-o",
            "gpx": "file-audio-o",
            "logic": "file-audio-o",
            "m4a": "file-audio-o",
            "m4b": "file-audio-o",
            "m4p": "file-audio-o",
            "midi": "file-audio-o",
            "mp3": "file-audio-o",
            "ogg": "file-audio-o",
            "aac": "file-audio-o",
            "pcm": "file-audio-o",
            "snd": "file-audio-o",
            "uax": "file-audio-o",
            "wav": "file-audio-o",
            "wma": "file-audio-o",
            "wpl": "file-audio-o",
            "numbers": "file-excel-o",
            "ods": "file-excel-o",
            "sdc": "file-excel-o",
            "sxc": "file-excel-o",
            "xls": "file-excel-o",
            "xlsm": "file-excel-o",
            "xlsx": "file-excel-o",
            "pdf": "file-pdf-o",
            "1st": "file-text-o",
            "alx": "file-text-o",
            "asp": "file-text-o",
            "csv": "file-text-o",
            "eng": "file-text-o",
            "htm": "file-text-o",
            "html": "file-text-o",
            "log": "file-text-o",
            "lrc": "file-text-o",
            "lst": "file-text-o",
            "nfo": "file-text-o",
            "opml": "file-text-o",
            "plist": "file-text-o",
            "pts": "file-text-o",
            "reg": "file-text-o",
            "rep": "file-text-o",
            "srt": "file-text-o",
            "sub": "file-text-o",
            "tbl": "file-text-o",
            "text": "file-text-o",
            "txt": "file-text-o",
            "xml": "file-text-o",
            "xsd": "file-text-o",
            "xsl": "file-text-o",
            "xslt": "file-text-o",
            "odp": "file-powerpoint-o",
            "pot": "file-powerpoint-o",
            "potx": "file-powerpoint-o",
            "pps": "file-powerpoint-o",
            "ppsx": "file-powerpoint-o",
            "ppt": "file-powerpoint-o",
            "pptm": "file-powerpoint-o",
            "pptx": "file-powerpoint-o",
            "sdd": "file-powerpoint-o",
            "key": "file-powerpoint-o",
            "keynote": "file-powerpoint-o",
            "xps": "file-powerpoint-o",
            "264": "file-video-o",
            "3g2": "file-video-o",
            "3gp": "file-video-o",
            "avi": "file-video-o",
            "bik": "file-video-o",
            "dash": "file-video-o",
            "dat": "file-video-o",
            "dvr": "file-video-o",
            "flv": "file-video-o",
            "h264": "file-video-o",
            "m2t": "file-video-o",
            "m2ts": "file-video-o",
            "m4v": "file-video-o",
            "mkv": "file-video-o",
            "mod": "file-video-o",
            "mov": "file-video-o",
            "mp4": "file-video-o",
            "mpeg": "file-video-o",
            "mpg": "file-video-o",
            "mswmm": "file-video-o",
            "mts": "file-video-o",
            "ogv": "file-video-o",
            "prproj": "file-video-o",
            "rec": "file-video-o",
            "rmvb": "file-video-o",
            "swf": "file-video-o",
            "tod": "file-video-o",
            "tp": "file-video-o",
            "ts": "file-video-o",
            "vob": "file-video-o",
            "webm": "file-video-o",
            "wmv": "file-video-o",
            "abw": "file-word-o",
            "aww": "file-word-o",
            "cnt": "file-word-o",
            "doc": "file-word-o",
            "docm": "file-word-o",
            "docx": "file-word-o",
            "dot": "file-word-o",
            "dotm": "file-word-o",
            "dotx": "file-word-o",
            "epub": "file-word-o",
            "ind": "file-word-o",
            "indd": "file-word-o",
            "odf": "file-word-o",
            "odt": "file-word-o",
            "ott": "file-word-o",
            "oxps": "file-word-o",
            "pages": "file-word-o",
            "pmd": "file-word-o",
            "pub": "dofile-word-oc",
            "pwi": "file-word-o",
            "rtf": "file-word-o",
            "wpd": "file-word-o",
            "wps": "file-word-o",
            "wri": "file-word-o"
        },
        /**
         * Initialize the view.
         * @param options
         * @returns {FileView}
         */
        initialize: function (options) {
            this.$hiddens = [];
            this._versionable = options.versionable || false;
            this._versions = [];
            this._initLoading = options.loading;
            this.listenTo(this.model, 'change:percentage', this._updatePercentage);
            this.listenTo(this.model, 'change:appDocUid', this.createHiddenForProperty);
            this._project = this.model.get('project');
            this._parent = this.model.get("parent");

            if (this._initLoading) {
                this.listenTo(this.model, 'upload_complete', this._onComplete);
            }

            this._dom = {};
            return this;
        },
        /**
         * Execute the button Cancel
         * @param e
         * @returns {FileView}
         */
        onClickCancelUpload: function (e) {
            var xhr;
            e.preventDefault();
            e.stopPropagation();

            if (this._initLoading) {
                xhr = this.model.get("xhr");
                if (xhr && xhr.abort) {
                    xhr.abort();
                    this.onClickDelete(e);
                }
            } else if (this._xhr) {
                this._xhr.abort();
                this._onComplete();
            }

            return this;
        },
        /**
         * Resets and displays the file uploading progress bar.
         * @private
         */
        _showProgressBar: function () {
            var progressBar,
                progressBarContainer;
            if (!this._dom.progressBar) {
                progressBarContainer = document.createElement('div');
                progressBarContainer.className = "progress";

                progressBar = document.createElement('div');
                progressBar.className = "progress-bar progress-bar-success";

                progressBarContainer.appendChild(progressBar);

                this._dom.progressBar = progressBar;
                this._dom.progressBarContainer = progressBarContainer;
            }

            this._dom.progressBar.textContent = "";
            this._dom.progressBar.style.width = "0%";
            this._dom.$fileInfo.append(this._dom.progressBarContainer);
            this._hideButtonsFromActionList('abort');
            this._dom.$fileActions.show();
        },
        /**
         * Render only file view
         * @returns {FileView}
         */
        render: function () {
            var mode = this._parent.get("mode"),
                filename = this.model.get("file").name,
                ext = this.model.parseExtension(filename),
                versionList;

            this.setElement(this.template({
                downloadLink: '#',
                viewid: this.cid,
                filename: filename,
                fileversion: '',
                versionable: this._versionable,
                mode: mode,
                iconClass: this._getIconClass(ext),
                details: '',
                extensions: this._parent.get("extensions")
            }));

            versionList = document.createElement('ul');
            versionList.className = "pmdynaform-mfile-versions";
            this.el.appendChild(versionList);

            this._dom.$fileTitle = this.$el.find('> .pmdynaform-mfile .pmdynaform-mfile-title');
            this._dom.$fileName = this.$el.find('> .pmdynaform-mfile .pmdynaform-mfile-name');
            this._dom.$fileVersion = this.$el.find('> .pmdynaform-mfile .pmdynaform-mfile-version');
            this._dom.$fileInfo = this.$el.find('> .pmdynaform-mfile .pmdynaform-mfile-details');
            this._dom.$fileActions = this.$el.find('> .pmdynaform-mfile .pmdynaform-mfile-actions');
            this._dom.$buttonList = this._dom.$fileActions.find('li[data-action=list]');
            this._dom.fileInput = this.$el.find('#pmdynaform-input-' + this.cid);
            this._dom.$versionList = $(versionList);
            this._dom.$listLoaderMessage = $(this.loaderTemplate());

            if (mode === 'edit') {
                if (this._initLoading) {
                    this._showProgressBar();
                } else {
                    this._onComplete();
                }

                if (!this.model.get("isValid")) {
                    this.showMessageError();
                }
            } else {
                this._onComplete();
            }
        },
        /**
         * Set Message Error
         */
        showMessageError: function () {
            $(this._dom.progressBar).removeClass('progress-bar-success')
                .addClass('progress-bar-danger')
                .css("width", '100%')
                .text(this.model.get("errorSize") ? this.constructMessageError("size")
                    : this.constructMessageError("type"));

            this._hideButtonsFromActionList('delete');
            this._dom.$fileActions.show();

            return this;
        },
        /**
         * Displays a Flash Message in error mode.
         * @param message
         * @returns {FileView}
         * @private
         */
        _showFlashErrorMessage: function (message) {
            this._project.flashMessage({
                message: message,
                duration: 0,
                type: 'danger',
                emphasisMessage: 'Error:'
            });

            return this;
        },
        destroy: function () {
            this.model.destroy();
        },
        /**
         * Construct the message error
         * @param value
         * @returns {string}
         */
        constructMessageError: function (value, file) {
            var sizeUnity = this._parent.get("sizeUnity"),
                sizeAllow = this._parent.get("size") + sizeUnity,
                typeAllow = this._parent.get("extensions"),
                message = "";

            switch (value) {
                case "size":
                    message = (file ? "\"" + file.name + "\"" : 'File') + " size exceeds the allowable limit of {" + sizeAllow + "}";
                    break;
                case "type":
                    message = "Invalid file format " + (file ? 'for "' + file.name + '"': '') + ", please upload a file with one of the following formats {" + typeAllow + "}";
                    break;
            }
            return message;
        },
        /**
         * update the percentage of progressbar
         * @returns {FileView}
         */
        _updatePercentage: function (model, value) {
            value = Math.floor(value);
            this._dom.progressBar.textContent = this._dom.progressBar.style.width = value + '%';

            return this;
        },
        /**
         * Sets the view in its final mode.
         * @returns {FileView}
         * @private
         */
        _onComplete: function () {
            var that = this,
                file = this.model.get("file");

            setTimeout(function () {
                var buttons = ['download'],
                    mode = that.model.get('parent').get("mode");

                that._dom.fileInput.val("");
                that._initLoading = false;
                that._dom.$fileName.text(file.name);
                that._dom.$fileTitle.attr("title", file.name);
                // TODO: Set the file details in the format "Created by John Doe on 2017-03-03"
                that._dom.$fileInfo.empty().text("");
                that._dom.$fileActions.find('[data-action=download] a').attr('href', that.model.getLinkDownload());

                if (mode === 'edit') {
                    buttons.push('delete');
                }

                if (that._versionable) {
                    buttons.push('list', 'unlist');

                    if (mode === 'edit') {
                        buttons.push('upload');
                    }
                }

                that._hideButtonsFromActionList(buttons);
                that._dom.$fileActions.show();
                that._renderVersions(that._versions);
            }, 300);

            return this;
        },

        createHiddenForProperty: function () {
            var parentView = this.model.get('parent').get('view');
            if (parentView) {
                parentView.createAllHiddens('add');
            }
        },
        /**
         * Returns the icon class for the file
         * @param ext
         * @returns {*|string}
         * @private
         */
        _getIconClass: function (ext) {
            return this.iconsMap[ext] || "file-o";
        },
        /**
         * Hide all action buttons, except by the especified ones.
         * @param [exceptions] An array of strings with the exceptions.
         * @returns {FileView}
         * @private
         */
        _hideButtonsFromActionList: function (exceptions) {
            var exceptionSelector = [],
                itemWidth = 29; // TODO: find a way to find this width dinamically

            if (typeof exceptions === 'string') {
                exceptions = [exceptions];
            }

            if (_.isArray(exceptions)) {
                exceptions.forEach(function (item) {
                    exceptionSelector.push('[data-action="' + item + '"]');
                });
                exceptionSelector = exceptionSelector.join(',');
            } else {
                exceptionSelector = null;
            }

            if (exceptionSelector) {
                this._dom.$fileActions.find('li').css("display", "").not(exceptionSelector).hide();
            } else {
                this._dom.$fileActions.find('li').hide();
            }

            this._dom.$fileTitle.css('width', 'calc(100% - '
                + (this._dom.$fileActions.find(exceptionSelector || "li").size() * itemWidth) + 'px)');

            return this;
        },
        /**
         * Render the supplied version in list.
         * @param version
         *
         * @returns {FileView}
         * @private
         */
        _renderVersionInList: function (version) {
            var versionItem = $(this.template({
                downloadLink: "#",
                viewid: this.cid + "_v" + version.app_doc_version,
                filename: version.app_doc_filename,
                fileversion: version.app_doc_version,
                versionable: false,
                mode: "view",
                details: "Uploaded by " + version.app_doc_create_user + " on " + version.app_doc_create_date,
                iconClass: this._getIconClass(this.model.parseExtension(version.app_doc_filename))
            }));

            this._dom.$listLoaderMessage.remove();

            versionItem.find('a').attr("data-version-number", version.app_doc_version);
            versionItem.find('a').attr("data-file-name", version.app_doc_filename);
            versionItem.find('.pmdynaform-mfile-action-item').not('[data-action=download]').remove()
                 .end().end().find('.pmdynaform-mfile-actions').show();

            this._dom.$versionList.append(versionItem);

            return this;
        },
        /**
         * Renders the supplied versions on the versions list.
         * @param {Array} versions
         * @private
         */
        _renderVersions: function (versions) {
            var that = this;

            if (versions.length) {
                this._dom.$versionList.empty();

                versions.forEach(function (version) {
                   that._renderVersionInList(version);
                });

            } else {
                this._dom.$listLoaderMessage.text("0 items");
            }
        },
        /**
         * Collapse the versions list.
         * @param {Event} e
         * @private
         */
        _unlistVersions: function (e) {
            e.preventDefault();
            this._dom.$versionList.stop().slideUp();
            this._dom.$buttonList.attr("data-action", "list").find('a').get(0).className = 'fa fa-chevron-circle-down';
        },
        /**
         * Displays the versions list.
         * @param {Event} e
         * @returns {FileView}
         * @private
         */
        _listVersions: function (e) {
            var appDocUID = this.model.get("appDocUid"),
                that = this;

            e.preventDefault();

            this._dom.$versionList.empty().append(this._dom.$listLoaderMessage.text("loading...")).stop().slideDown();
            this._dom.$buttonList.attr("data-action", "unlist").find('a').get(0).className = 'fa fa-chevron-circle-up';

            this._project.webServiceManager.getFileVersions(appDocUID, function (err, resp) {
                if (err) {
                    that._showFlashErrorMessage(err.status);
                } else {
                    that._versions = resp.data.sort(function (a, b) {
                        return parseInt(a.app_doc_version, 10) < parseInt(b.app_doc_version, 10) ? 1 : -1;
                    });

                    that._renderVersions(that._versions);
                }
            });

            return this;
        },
        /**
         * Uploads the the supplied file.
         * @param {Object} data The details of the file to be uploaded.
         * @param {File} file The file to be uploaded.
         * @private
         */
        _uploadFile: function (data, file) {
            var formData = new FormData(),
                that = this;

            formData.append("form[]", file);

            this._xhr = this._project.webServiceManager.uploadMultipart(data.appDocUid, formData, function (err) {
                var newVersion;

                if (err) {
                    that._showFlashErrorMessage(err.status);
                    return that._onComplete();
                }

                newVersion = {
                    app_doc_uid: data.appDocUid,
                    app_doc_filename: data.appDocFilename,
                    doc_uid: data.appDocUid,
                    app_doc_version: data.docVersion,
                    app_doc_create_date: data.appDocCreateDate,
                    app_doc_create_user: data.appDocCreateUser,
                    app_doc_type: data.appDocType,
                    app_doc_index: data.appDocIndex
                },
                oldAppDocUid = that.model.get("appDocUid");

                that._xhr = null;
                that._versions.unshift(newVersion);

                that.model.set({
                    file: {
                        name: newVersion.app_doc_filename
                    },
                    version: newVersion.app_doc_version
                }).set("appDocUid", newVersion.app_doc_uid);
                //The appDocUid update is not being performed in the first call due it is necessary to ensure
                // it is called after the name and version update, in order to updsate the hidden fields.

                if (oldAppDocUid === newVersion.app_doc_uid) {
                    that.createHiddenForProperty();
                }

                that._onComplete();
            }, function (e) {
                if (e.lengthComputable) {
                    that._updatePercentage(null, (e.loaded * 100) / e.total);
                }
            });
        },
        /**
         * Start the upload process.
         * @param {Event} e
         * @private
         */
        _startUpload: function (e) {
            var files = e.target.files,
                that = this,
                errorMessage,
                file,
                data;

            e.preventDefault();

            if (files.length === 1) {
                file = files.item(0);

                if (!this.model._isFileExtensionValid(file, this._parent.get("extensions"))) {
                    errorMessage = this.constructMessageError('type', file);
                } else if (!this.model._isFileSizeValid(file, this._parent.get("size"), this._parent.get("sizeUnity"))) {
                    errorMessage = this.constructMessageError('size', file);
                }

                if (errorMessage) {
                    return this._showFlashErrorMessage(errorMessage);
                }

                data = {
                    name: file.name,
                    fieldName: this.model.get("parent").get("var_name"),
                    docUid: this.model.get("parent").get("inp_doc_uid"),
                    appDocType: "INPUT",
                    appDocUid: this.model.get("appDocUid")
                };

                this._dom.$fileName.text(data.name);
                this._showProgressBar();

                this._xhr = this._project.webServiceManager.upload([data], function (err, data) {
                    if (err) {
                        that._showFlashErrorMessage(err.status);
                        return that._onComplete();
                    }
                    that._uploadFile(data.data[0], file);
                });
            } else if (files.length > 1) {
                this._showFlashErrorMessage("Only one version per file can be uploaded.");
            }

        },
        /**
         * Get data and create link of download
         * @param e
         * @private
         */
        _downloadVersion: function (e) {
            var that = this,
                item = e.target,
                version = item.getAttribute("data-version-number"),
                nameFile = item.getAttribute("data-file-name"),
                url;
            this.model.downloadFileVersion(version, function (data) {
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(data, nameFile);
                } else {
                    url = URL.createObjectURL(data);
                    that._executeFakeLink(url, nameFile);
                    window.URL.revokeObjectURL(url);
                }
            });
            e.preventDefault();
        },
        /**
         * Execute download
         * @param url {string}
         * @param nameFile {string}
         * @returns {FileView}
         * @private
         */
        _executeFakeLink: function (url, nameFile) {
            var fakeLink = $("<a/>", {
                    "id": "fakeDownloadLink",
                    "href": url,
                    "download": nameFile
                }).hide();
            fakeLink.appendTo("body");
            fakeLink[0].click();
            fakeLink.remove();
            return this;
        },
        _fireUploadDialog: function (e) {
            e.preventDefault();
            this._dom.fileInput.click();
        },
        /**
         * Delete a file handler
         * @returns {FileView}
         */
        onClickDelete: function (e) {
            var parentView = this.model.get('parent').get('view');
            if (this.model.get("parent").get("mode") === "edit" && parentView) {
                this.model.destroy();
                this.remove();
                if (this.model.get('parent').get('group') === 'grid') {
                    this.model.get('parent').removeFromGridDetail(this.model);
                }
                this.model.get('parent').resetInputFile();
            }

            e.preventDefault();

            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.file.FileView", FileView);
}());

(function () {
    var FileCollection = Backbone.Collection.extend({
        model: PMDynaform.file.FileModel,
        /**
         * Get the values from all Files
         * @returns {Array}
         */
        getValues: function () {
            var index,
                resp = [];
            for (index = 0; index < this.models.length; index += 1) {
                if (_.isFunction(this.models[index].getValue)) {
                    resp.push(this.models[index].getValue());
                }
            }
            return resp;
        },
        /**
         * Get the data{appDocuid, name, version} form all files
         * @returns {Array}
         */
        getData: function () {
            var index,
                resp = [];
            for (index = 0; index < this.models.length; index += 1) {
                if (_.isFunction(this.models[index].getData)) {
                    resp.push(this.models[index].getData());
                }
            }
            return resp;
        },
        /**
         * Get the names from files
         * @returns {Array}
         */
        getNames: function () {
            var index,
                resp = [];
            for (index = 0; index < this.models.length; index += 1) {
                if (_.isFunction(this.models[index].getName)) {
                    resp.push(this.models[index].getName());
                }
            }
            return resp;
        },
        /**
         * Validate the file
         * @returns {Array}
         */
        validate: function () {
            var index,
                resp = false;
            for (index = 0; index < this.models.length; index += 1) {
                if (this.models[index].get("isValid")) {
                    resp = true;
                    break;
                }
            }
            return resp;
        },
        /**
         * Update index valid in all files
         * @returns {Array}
         */
        updateIndex: function () {
            var index,
                indexNew = 0;
            for (index = 0; index < this.models.length; index += 1) {
                if (this.models[index].get("isValid")) {
                    this.models[index].set("index", indexNew);
                    indexNew += 1;
                }
            }
            return this;
        },
        /**
         * Delete files in a field
         * @returns {FileCollection}
         */
        deleteFiles: function () {
            var index = 0;
            while (this.models.length > 0) {
                this.models[index].destroy();
            }
            return this;
        }
    });

    PMDynaform.extendNamespace("PMDynaform.file.FileCollection", FileCollection);
}());

(function () {
    var ProgressBar = Backbone.View.extend({
        template: _.template($("#tpl-progressbar").html()),
        percentage: 0,
        message: "",
        title: "",
        striped: false,
        animate: false,
        type: "default",
        types: ["default", "info", "success", "warning", "danger", "file"],
        col: 12,
        initialize: function (options) {
            this.percentage = options.percentage ? options.percentage : 0;
            this.title = options.title ? options.title : "";
            if (options.type && _.indexOf(this.types, options.type) !== -1) {
                this.type = options.type;
            }
            this.striped = options.striped ? options.striped : false;
            this.animate = options.animate ? options.animate : false;
            this.message = options.message ? options.message : "";
            this.col = options.col ? options.col : 12;
            return this;
        },
        /**
         * Render the view of progressBar
         */
        render: function () {
            var obj = {
                value: 0,
                title: this.title,
                col: this.col,
                type: this.type,
                striped: this.striped,
                animate: this.animate
            };
            this.setElement(this.template(obj));
            this.setPercentage(this.percentage);
            this.setMessage(this.message);
            this.$el.find(".progressbar-title").tooltip({
                title: this.title
            });
            return this;
        },
        /**
         * Set the percentage to progressbar
         * @param percentage
         */
        setPercentage: function (percentage) {
            var bar = this.$el.find(".progress-bar");
            bar.width(percentage + "%");
            return this;
        },
        /**
         * Set the type[color] to progresBar
         * @param type
         */
        setType: function (type) {
            var bar = this.$el.find(".progress-bar");
            bar.removeClass("progress-bar-" + this.type);
            if (_.indexOf(this.types, type) !== -1) {
                bar.addClass("progress-bar-" + type);
                this.type = type;
            }
            return this;
        },
        /**
         * Set the message in the progress bar
         * @param message
         * @returns {ProgressBar}
         */
        setMessage: function (message) {
            var bar = this.$el.find(".progress-bar");
            bar.html(message);
            return this;
        },
        /**
         * Disable the animation striped in the progressbar
         * @param message
         * @returns {ProgressBar}
         */
        disableAnimation: function (message) {
            var bar = this.$el.find(".progress-bar");
            bar.removeClass("active");
            return this;
        },
        /**
         * Set the progress bar striped for animation
         * @param value
         * @returns {ProgressBar}
         */
        setStriped: function (value) {
            var bar = this.$el.find(".progress-bar");
            if (_.isBoolean(value)) {
                this.striped = value;
                if (value) {
                    bar.addClass("progress-bar-striped");
                } else {
                    bar.removeClass("progress-bar-striped");
                }
            }
            return this;
        }
    });
    PMDynaform.extendNamespace("PMDynaform.ui.ProgressBar", ProgressBar);
}());
(function () {
    /**
     * UploadModalModel  class Inherited from MultipleFileModel
     *
     */
    var UploadModalModel = PMDynaform.file.MultipleFileModel.extend({
        defaults: {
            parent: null
        },
        /**
         * Adds the file model to collection to drive all files in a collection
         * validates the size too
         * @param file
         * @param mode
         * @param parentView
         * @returns {*}
         */
        addFileModel: function (file) {
            var parentFile = this.get('parent'),
                files = parentFile.get('fileCollection'),
                fileSize,
                fileExtension,
                fileModel;

            fileModel = new PMDynaform.file.FileModel({
                file: file,
                mode: "edit",
                parent: parentFile,
                project: parentFile.get('project'),
                form: parentFile.get('form'),
                size: parentFile.get('size'),
                sizeUnity: parentFile.get('sizeUnity'),
                extensions: parentFile.get("extensions")
            });

            fileSize = fileModel._isFileSizeValid(file, parentFile.get("size"), parentFile.get("sizeUnity"));
            fileExtension = fileModel._isFileExtensionValid(file, parentFile.get("extensions"));

            if (!fileSize) {
                fileModel.set("isValid", fileSize);
                fileModel.set("errorSize", true);
            }
            if (!fileExtension) {
                fileModel.set("isValid", fileExtension);
                fileModel.set("errorType", true);
            }
            if (fileModel.get("isValid")) {
                fileModel = files.add(fileModel);
                files.updateIndex();
            }
            return fileModel;
        }

    });
    PMDynaform.extendNamespace("PMDynaform.file.UploadModalModel", UploadModalModel);
}());
(function () {
    var UploadModal = Backbone.View.extend({
        timeHide: 1000,
        template: _.template($("#tpl-upload-modal").html()),
        modal: null,
        $hiddenFile: null,
        labelButton: "Choose Files",
        labelClose: "Close",
        $hiddens: [],
        initialize: function () {
            //TODO: no need params.
        },
        /**
         * Render the modal from a multiple file in a grid field, receive a backbone collection
         * @param collection
         * @returns {UploadModal}
         */
        renderForm: function (collection) {
            var that = this,
                box,
                i,
                model,
                mode = "edit",
                ext = "",
                view,
                parent = this.model.get("parent");

            if ($('#modalUpload').length) {
                $('#modalUpload').remove();
            }
            mode = parent ? parent.get("mode") : mode;
            ext = parent ? parent.get("extensions") : ext;
            this.modal = $(this.template({
                mode: mode,
                labelButton: this.labelButton,
                labelClose: this.labelClose,
                extensions: ext
            }));
            $('body').append(this.modal);

            this.show();
            this.eventsBinding();
            this.modal.find('.btn-uploadfile').on('click', function (e) {
                that.onClickUploadButton(e);
            });
            box = this.modal.find(".pmdynaform-multiplefile-box");
            if (collection instanceof Backbone.Collection) {
                for (i = 0; i < collection.length; i += 1) {
                    model = collection.at(i);
                    view = new PMDynaform.file.FileView({
                        model: model,
                        versionable: this.model.get("parent").get("enableVersioning")
                    });
                    view.render();
                    box.append(view.$el);
                }
            }
            this.removeModal();
            return this;
        },

        /**
         * show modal form
         */
        show: function () {
            var modalUpload = $('#modalUpload');
            modalUpload.modal({backdrop: 'static', keyboard: false}, 'show');
            return this;
        },
        /**
         * Listen event hidden and remove modal if exist
         * @returns {UploadModal}
         */
        removeModal: function () {
            var modalUpload = $('#modalUpload');
            modalUpload.on('hidden.bs.modal', function () {
                if (modalUpload.length) {
                    modalUpload.remove();
                }
            });
            return this;
        },
        /**
         * upload button handler
         * @param event
         */
        onClickUploadButton: function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (this.model.get("parent") && this.model.get("parent").get('mode') === "edit") {
                this.openFileWizardWeb();
            }
        },
        /***
         * Open Wizard to select upload files
         * @param event
         */
        openFileWizardWeb: function () {
            $('.pm-modal-upload').find("input:file").trigger("click");
            return this;
        },

        /**
         * change files handler
         * @param event
         * @returns {boolean}
         */
        changeFileControl: function (event) {
            var files;
            event.preventDefault();
            event.stopPropagation();
            files = event.target.files;
            this.processFiles(files);
            event.target.value = "";
            return false;
        },
        /**
         * process al files to upload to create an instance of a file model an view
         * at last render the upload file
         * @param files
         * @returns {UploadModal}
         */
        processFiles: function (files) {
            var index = 0,
                fileView,
                fileModel,
                box = this.modal.find(".pmdynaform-multiplefile-box");
            if (files.length) {
                for (index = 0; index < files.length; index += 1) {
                    fileModel = this.model.addFileModel(files[index], "edit", this);

                    fileView = new PMDynaform.file.FileView({
                        model: fileModel,
                        loading: true,
                        versionable: this.model.get("parent").get("enableVersioning")
                    });
                    fileModel.set('fromWizard', true);
                    fileView.render();
                    box.append(fileView.$el);
                    fileModel.fileUpload();

                    fileModel.set('fromWizard', false);
                    this.model.get('parent').updateGridDetail(fileModel);
                }
            }
            return this;
        },
        /**
         * Reset the input file html tag for reset properties
         */
        resetInputFile: function () {
            var parent = this.model.get("parent"),
                nInput = $("<input>", {
                    type: "file",
                    style: "display:none",
                    multiple: "multiple",
                    accept: parent.get("extensions")
                });
            this.$hiddenFile.before(nInput).remove();
            this.$hiddenFile = nInput;
            this.eventsBinding();
            return this;
        },
        eventsBinding: function () {
            var that = this;
            this.$hiddenFile = this.modal.find("input:file");
            this.$hiddenFile.on('change', function (e) {
                that.changeFileControl(e);
            });
        }
    });
    PMDynaform.extendNamespace("PMDynaform.file.UploadModalView", UploadModal);
}());

