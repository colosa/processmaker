(function () {
    $.imgUrl = "/lib/img/";
    $.icons = $("<div style='position:absolute;margin:0px;top:1px;right:1px;background:white;'>" +
        "<img src='" + $.imgUrl + "fd-move2.png' style='width:16px;height:16px;margin-right:2px;cursor:move;' class='formdesigner-move-row'>" +
        "<img src='" + $.imgUrl + "fd-close.png' style='width:16px;height:16px;margin-right:0px;cursor:pointer;' class='formdesigner-close-row'>" +
        "</div>");
    $.elicons = $("<div style='position:absolute;top:1px;margin:0px;right:1px;background:white;width:16px;height:16px;'>" +
        "<img src='" + $.imgUrl + "fd-close.png' style='width:16px;height:16px;margin-right:0px;cursor:pointer;' class='formdesigner-close-row'>" +
        "</div>");
    $.isOpenValidator = false;
    $.fmold = null;
    $.fmoldparent = null;
    $.fmoldborder = null;
    $.fmborder = "1px dashed black";
    $.designerSelectElement = function (el, fn, fn2) {
        $.icons.hide();
        $.elicons.hide();
        if ($.fmold !== null) {
            if ($.fmold.tagName === "TD") {
                var tr = $.fmoldparent;
                for (var i = 0; i < tr.childNodes.length; i++) {
                    td = tr.childNodes[i];
                    td.style.border = $.fmoldborder;
                }
            } else {
                $.fmold.style.border = $.fmoldborder;
            }
        }
        if (el.tagName === "TD") {
            $.fmoldborder = el.style.border;
            var tr = el.parentNode, td;
            for (var i = 0; i < tr.childNodes.length; i++) {
                td = tr.childNodes[i];
                if (i === 0) {
                    td.style.borderLeft = $.fmborder;
                }
                td.style.borderTop = $.fmborder;
                td.style.borderBottom = $.fmborder;
            }
            td = tr.childNodes[i - 1];
            td.style.borderRight = $.fmborder;
            $.fmold = el;
            $.fmoldparent = el.parentNode;
            //icons
            $(tr.childNodes[tr.childNodes.length - 1].childNodes[0]).append($.icons);
            $.icons.show();
            $.icons.find(".formdesigner-close-row")[0].onclick = function (e) {
                e.stopPropagation();
                if (fn2 && fn2() === false) {
                    return;
                }
                $(tr).remove();
                if (fn)
                    fn();
            };
        } else {
            $.fmoldborder = el.style.border;
            el.style.border = $.fmborder;
            $.fmold = el;
            //icons
            $(el).append($.elicons);
            $.elicons.show();
            $.elicons.find(".formdesigner-close-row")[0].onclick = function (e) {
                if ($.elicons.parent().parent()[0] && $.elicons.parent().parent()[0].tagName === "TD") {
                    $.elicons.parent().parent().addClass("itemVariables");
                    $.elicons.parent().parent().addClass("itemControls");
                    $.elicons.parent().parent().addClass("cellDragDrop");
                }
                e.stopPropagation();
                if (fn && fn() === false) {
                    return;
                }
                $(el).remove();
            };
        }
    };
    $.globalInvalidProperties = [];
    $.changeAllValue = function (string, prop, oldValue, newValue) {
        for (var i in string) {
            if (typeof string[i] === "object") {
                string[i] = $.changeAllValue(string[i], prop, oldValue, newValue);
            }
            if (typeof string[i] === "string" && i === prop && string[i] === oldValue) {
                string[i] = newValue;
            }
        }
        return string;
    };
    $.countValue = function (string, prop, value, withTrim) {
        var n = 0, a, b;
        for (var i in string) {
            if (typeof string[i] === "object")
                n = n + $.countValue(string[i], prop, value, withTrim);
            if (typeof string[i] === "string" && i === prop) {
                a = string[i];
                b = value;
                if (withTrim === true) {
                    a = a.trim();
                    b = b.trim();
                }
                if (a === b) {
                    n = n + 1;
                }
            }
        }
        return n;
    };
    $.getProp = function (string, prop) {
        var a = [], b;
        for (var i in string) {
            if (typeof string[i] === "object") {
                b = $.getProp(string[i], prop);
                for (var j = 0; j < b.length; j++) {
                    a.push(b[j]);
                }
            }
            if (typeof string[i] === "string" && i === prop) {
                a.push(string[i]);
            }
        }
        return a;
    };
    $.setAllPropForType = function (string, prop, value, types) {
        for (var i in string) {
            if (typeof string[i] === "object") {
                string[i] = $.setAllPropForType(string[i], prop, value, types);
            }
            if (typeof string[i] === "string" && i === prop && string["type"] && types.indexOf(string["type"]) >= 0) {
                string[i] = value;
            }
        }
        return string;
    };
    $.hoverToolbarButtons = function (a, img1, img2) {
        a.on("mouseout", function () {
            a.find("img")[0].src = "" + $.imgUrl + img1;
        });
        a.on("mouseover", function () {
            a.find("img")[0].src = "" + $.imgUrl + img2;
        });
    };
    $.rPadDT = function (str) {
        str = str.toString();
        return str.length < 2 ? $.rPadDT("0" + str) : str;
    };
    /**
     * Function: validkeys
     * Note: valid characteres for file name in http://support.microsoft.com/kb/177506/es
     *
     * (A-z)letter
     * (0-9)number
     *  ^   Accent circumflex (caret)
     *  &   Ampersand
     *  '   Apostrophe (single quotation mark)
     *  @   At sign
     *  {   Brace left
     *  }   Brace right
     *  [   Bracket opening
     *  ]   Bracket closing
     *  ,   Comma
     *  $   Dollar sign
     *  =   Equal sign
     *  !   Exclamation point
     *  -   Hyphen
     *  #   Number sign
     *  (   Parenthesis opening
     *  )   Parenthesis closing
     *  %   Percent
     *  .   Period
     *  +   Plus
     *  ~   Tilde
     *  _   Underscore
     *
     *  Example: only backspace, number and letter.
     *  $.validkeys(objectHtmlInput, ['isbackspace', 'isnumber', 'isletter']);
     *
     *  Aditional support:
     *  :   Colon
     *
     * @param {type} object
     * @param {type} validates
     * @returns {undefined}
     */
    $.validkeys = function (object, validates) {
        object.onkeypress = function (e) {
            var key = document.all ? e.keyCode : e.which;
            if (key === 0)
                return true;
            var isbackspace = key === 8;
            var isnumber = key > 47 && key < 58;
            var isletter = (key > 96 && key < 123) || (key > 64 && key < 91);
            var isaccentcircumflex = key === 94;
            var isampersand = key === 41;
            var isapostrophe = key === 145;
            var isatsign = key === 64;
            var isbraceleft = key === 123;
            var isbraceright = key === 125;
            var isbracketopening = key === 91;
            var isbracketclosing = key === 93;
            var iscomma = key === 130;
            var isdollarsign = key === 36;
            var isequalsign = key === 61;
            var isexclamationpoint = key === 33;
            var ishyphen = key === 45;
            var isnumbersign = key === 35;
            var isparenthesisopening = key === 40;
            var isparenthesisclosing = key === 41;
            var ispercent = key === 37;
            var isperiod = key === 46;
            var isplus = key === 43;
            var istilde = key === 126;
            var isunderscore = key === 95;
            var iscolon = key === 58;

            var sw = eval(validates[0]);
            for (var i = 1; i < validates.length; i++) {
                sw = sw || eval(validates[i]);
            }
            return sw;
        };
    };
    $.validDataTypeAndControlType = function (dataType, controlType, fn) {
        if (controlType === FormDesigner.main.TypesControl.text && (dataType === "string" || dataType === "integer" || dataType === "float")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.textarea && (dataType === "string" || dataType === "integer" || dataType === "float")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.dropdown && (dataType === "string" || dataType === "integer" || dataType === "float")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.checkbox && (dataType === "boolean")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.checkgroup && (dataType === "array")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.radio && (dataType === "string" || dataType === "integer" || dataType === "float" || dataType === "boolean")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.datetime && (dataType === "datetime")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.suggest && (dataType === "string" || dataType === "integer" || dataType === "float")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.hidden && (dataType === "string" || dataType === "integer" || dataType === "float" || dataType === "boolean" || dataType === "datetime")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.grid && (dataType === "grid")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.file && (dataType === "file")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.multipleFile && (dataType === "multiplefile")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.geomap && (dataType === "string")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.qrcode && (dataType === "string")) {
            fn();
        }
        if (controlType === FormDesigner.main.TypesControl.form && (dataType === "grid")) {
            fn();
        }
    };
    $.recovered = {data: [], date: ""};
    var Designer = function (dynaform) {
        this.onHide = new Function();
        this.onSave = new Function();
        this.dynaform = dynaform;
        this.form1 = null;
        this._auxForm = null;
        //this.form2 = null;
        Designer.prototype.init.call(this);
    };
    Designer.prototype.init = function () {
        this.loadDynaforms();
        var that = this;
        this.container = $("<div style='position:absolute;top:0;right:0;bottom:0;left:0;z-index:100;'></div>");
        this.center = $("<div class='ui-layout-center'></div>");
        this.north = $("<div class='ui-layout-north fd-toolbar-designer' style='overflow:hidden;background-color:#3397e1;padding:0px;'></div>");
        this.west = $("<div class='ui-layout-west' style='padding:0px;overflow-y:scroll;'></div>");
        this.container.append(this.center);
        this.container.append(this.north);
        this.container.append(this.west);
        $("body").append(this.container);
        var layout = this.container.layout({
            north: {
                enableCursorHotkey: false,
                resizable: false,
                spacing_open: 0,
                closable: false,
                size: 35
            },
            west: {
                enableCursorHotkey: false,
                resizable: true,
                spacing_open: 1,
                spacing_closed: 1,
                size: 233
            }
        });
        $(".ui-layout-toggler-west-open").css({"overflow": "visible"}).parent().css({"overflow": "visible"});
        $(".ui-layout-toggler-west-open").append("<img id='toggleWest' src='" + $.imgUrl + "fd-toggle.png' style='position:absolute;left:-6px;'/>");
        this.center[0].style.width = "";//todo

        //new objects
        var listControls = new FormDesigner.main.ListControls();
        var listMobileControls = new FormDesigner.main.ListMobileControls();
        var listProperties = new FormDesigner.main.ListProperties();
        var listHistory = new FormDesigner.main.ListHistory(this.dynaform);
        this.form1 = new FormDesigner.main.Form();
        //this.form2 = new FormDesigner.main.Form();
        //
        this.title = $("<div style='float:left;font-family:Montserrat,sans-serif;font-size:20px;color:white;margin:5px;white-space:nowrap;'>Titulo</div>");
        this.areaButtons = new FormDesigner.main.AreaButtons();
        this.areaToolBox = new FormDesigner.main.AreaToolBox();
        this.areaToolBox.addItem("Web controls".translate(), listControls);
        if (window.distribution === "1")
            this.areaToolBox.addItem("Mobile controls".translate(), listMobileControls);
        this.areaToolBox.addItem("Properties".translate(), listProperties);
        this.areaToolBox.addItem("History of use".translate(), listHistory);
        this.areaTabs = new FormDesigner.main.TabsForm();
        this.areaTabs.addItem("Master", this.form1);
        //this.areaTabs.addItem("Subform", form2);
        this.north.append(this.title);
        this.north.append(this.areaButtons.body);
        this.west.append(this.areaToolBox.body);
        this.center.append(this.areaTabs.body);
        //events
        var confirmRecovery = false;
        listHistory.onSelect = function (dynaform, history) {
            if (dynaform === null) {
                new FormDesigner.main.DialogMessage(null, "alert", "This content is empty.".translate());
                return;
            }
            var a = new FormDesigner.main.DialogConfirm(null, "warning", "Do you want to import? All your changes will be lost if you import it.".translate());
            a.onAccept = function () {
                listProperties.clear();
                $.recovered.data = [];
                that.form1.recovery = history.current === false ? true : false;
                that.form1.setData({
                    dyn_content: JSON.stringify(dynaform),
                    dyn_description: dynaform.items[0].description,
                    dyn_title: dynaform.items[0].name,
                    dyn_type: "xmlform",
                    dyn_uid: dynaform.items[0].id,
                    dyn_version: 2,
                    history_date: history.history_date
                });
                that.form1.recovery = false;
            };
        };
        this.areaButtons.save[0].onclick = function () {
            //save lost dynaforms
            if ($.recovered.data.length > 0) {
                if (confirmRecovery === false) {
                    var a = new FormDesigner.main.DialogConfirm(null, "warning", "The form was recovered from a previous version {0}. Possible missing controls are going to be recreated.".translate([$.recovered.date]));
                    a.onAccept = function () {
                        confirmRecovery = true;
                        that.areaButtons.save[0].onclick();
                    };
                    a.onCancel = function () {
                    };
                    return false;
                }
                confirmRecovery = false;
                var calls = [];
                for (var i = 0; i < $.recovered.data.length; i++) {
                    calls.push({url: "dynaform", method: "POST", data: $.recovered.data[i]});
                }
                calls.push({url: "dynaforms", method: "GET"});
                $.ajax({
                    async: false,
                    url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id + "/",
                    data: JSON.stringify({calls: calls}),
                    method: "POST",
                    contentType: "application/json",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'MULTIPART');
                        xhr.setRequestHeader("Authorization", "Bearer " + PMDesigner.project.keys.access_token);
                    },
                    success: function (responses) {
                        //synchronize id
                        var json = that.getData();
                        for (var i = 0; i < responses.length - 1; i++) {
                            if (responses[i].status === 201) {
                                json = $.changeAllValue(json, "id", $.recovered.data[i].dyn_uid_old, responses[i].response.dyn_uid)
                            }
                        }
                        $.recovered.data = [];
                        //update list dynaforms
                        if (responses[responses.length - 1].status === 200) {
                            $.remoteDynaforms = responses[responses.length - 1].response;
                        }
                        //update form
                        var form = that.form1.getData();
                        var dynaform = {
                            dyn_content: JSON.stringify(json),
                            dyn_description: form.description,
                            dyn_title: form.name,
                            dyn_type: "xmlform",
                            dyn_uid: form.id,
                            dyn_version: 2
                        };
                        that.form1.setData(dynaform);
                        listHistory.setDynaform(dynaform);
                        listHistory.reload();
                    },
                    error: function (responses) {
                    }
                });
            }
            //validations
            var data = that.getData();
            var a = $.getProp(data, "variable");
            for (var i = 0; i < a.length; i++) {
                if (a[i] !== "" && $.countValue(data, "variable", a[i], true) > 1) {
                    var b = new FormDesigner.main.DialogInvalid(null, a[i], "duplicated");
                    b.onAccept = function () {
                        b.dialog.dialog("close");
                    };
                    return false;
                }
            }
            a = $.getProp(data, "id");
            for (var i = 0; i < a.length; i++) {
                if (a[i] !== "" && $.countValue(data, "id", a[i], true) > 1) {
                    var b = new FormDesigner.main.DialogInvalid(null, a[i], "duplicated");
                    b.onAccept = function () {
                        b.dialog.dialog("close");
                    };
                    return false;
                }
            }
            if ($.globalInvalidProperties.length > 0) {
                new FormDesigner.main.DialogInvalidProperties();
                return false;
            }
            //save
            that.dynaform.dyn_title = data.name;
            that.dynaform.dyn_description = data.description;
            that.dynaform.dyn_content = JSON.stringify(data);
            var restClient = new PMRestClient({
                endpoint: 'dynaform/' + that.dynaform.dyn_uid,
                typeRequest: 'update',
                data: that.dynaform,
                functionSuccess: function (xhr, response) {
                    listHistory.setDynaform(that.dynaform);
                    listHistory.reload();
                    that.form1.setDirty();
                    that.onSave();
                },
                messageError: 'There are problems creating the DynaForm, please try again.'.translate(),
                messageSuccess: 'DynaForm saved successfully'.translate(),
                flashContainer: ''
            });
            restClient.executeRestClient();
            return false;
        };
        this.areaButtons.export_[0].onclick = function () {
            var jsondata = that.getData();
            var name = (that.form1.properties.name.value ? that.form1.properties.name.value : 'untitle') + '.json';
            if (window.navigator.msSaveBlob) {
                window.navigator.msSaveBlob(new Blob([JSON.stringify(jsondata)], {'type': 'application/octet-stream'}), name);
                return false;
            }
            var a = document.createElement('a');
            document.body.appendChild(a);
            a.href = window.URL.createObjectURL(new Blob([JSON.stringify(jsondata)], {'type': 'application/octet-stream'}));
            a.download = name;
            a.click();
            document.body.removeChild(a);
            delete a;
            return false;
        };
        this.areaButtons.import_[0].onclick = function () {
            var actionImport = function () {
                var form = that._getAuxForm();
                var input = document.createElement("input");
                input.type = "file";
                form.appendChild(input);
                form.style.display = "none";
                document.body.appendChild(form);
                input.onchange = function () {
                    var file = input.files[0];
                    if (file.name.indexOf(".json") < 0) {
                        new FormDesigner.main.DialogInvalidFile(null, file.name);
                        return;
                    }
                    var reader = new FileReader();
                    reader.readAsText(file, "UTF-8");
                    reader.onload = function (e) {
                        listProperties.clear();
                        var dynaform = that.form1.getData();
                        that.form1.setData({
                            dyn_content: e.target.result,
                            dyn_description: dynaform.description,
                            dyn_title: dynaform.name,
                            dyn_type: "xmlform",
                            dyn_uid: dynaform.id,
                            dyn_version: 2
                        });
                        that.form1.synchronizeVariables();
                    };
                    reader.onerror = function (evt) {
                    };
                    //IE8 implementation
                    //var filePath = f:\oo.txt;
                    //var fso = new ActiveXObject("Scripting.FileSystemObject");
                    //var textStream = fso.OpenTextFile(filePath);
                    //var fileData = file.ReadAll();
                };
                input.click();
            };
            if (that.form1.getData().items.length > 0) {
                var a = new FormDesigner.main.DialogConfirmImport();
                a.onAccept = actionImport;
            } else {
                actionImport();
            }
            return false;
        };
        this.areaButtons.preview[0].onclick = function () {
            that.onSave = function () {
                $(that.container).hide();
                var preview = new FormDesigner.main.Preview(this.dynaform.dyn_uid, this.dynaform.dyn_title, PMDesigner.project.id);
                preview.show();
                preview.setData();
                preview.onClose = function () {
                    that.show();
                };
                that.onSave = new Function();
            };
            that.areaButtons.save[0].onclick();
            return false;
        };
        this.areaButtons.clear[0].onclick = function () {
            if (that.form1.getData().items.length > 0) {
                var a = new FormDesigner.main.DialogConfirmClear();
                a.onAccept = function () {
                    that.form1.clear();
                    listProperties.clear();
                };
            }
            return false;
        };
        this.areaButtons.language[0].onclick = function () {
            that.onSave = function () {
                var a = new FormDesigner.main.DialogLanguage(null, that.dynaform.dyn_uid);
                a.onLoad = function (response) {
                    that.form1.setLanguages();
                };
                that.onSave = new Function();
            };
            that.areaButtons.save[0].onclick();
            return false;
        };
        this.areaButtons.close[0].onclick = function () {
            if (that.form1.isDirty()) {
                var a = new FormDesigner.main.DialogConfirmClose();
                a.onAccept = function () {
                    that.hide();
                };
            } else {
                that.hide();
            }
            return false;
        };
        this.form1.onSelect = function (properties) {
            listProperties.body.find('input').focus();
            listProperties.clear();
            listProperties.load(properties);
            that.areaToolBox.accordion.accordion("option", "active", (window.distribution === "1") ? 2 : 1);
            //initial form only
            if (that.form1 === properties.owner && properties.type.value === FormDesigner.main.TypesControl.form) {
                $.elicons.hide();
            }
        };
        this.form1.onRemove = function () {
            return false;
        };
        this.form1.onRemoveItem = function () {
            listProperties.clear();
        };
        this.form1.onRemoveCell = function () {
            listProperties.clear();
        };
        this.form1.onDrawControl = function (properties) {
            if (properties) {
                that.form1.setNextLabel(properties);
            }
            if (properties && properties.get().variable && properties.get().variable.type === "labelbutton") {
                that.form1.setNextVar(properties);
            }
        };
        this.form1.onSetProperty = function (prop, value, target) {
            if (prop === "name" && target.properties[prop].node && target instanceof FormDesigner.main.Form) {
                if (target === that.form1) {
                    that.title.text(value).attr("title", value);
                }

                if (value === "" || !value.replace(/\s/g, '').length) {
                    var a, b, old;
                    a = new FormDesigner.main.DialogInvalid(null, prop, "required");
                    a.onClose = function () {
                        old = target.properties[prop].oldValue;
                        b = target.properties.set(prop, old);
                        if (b.node) {
                            b.node.value = old;
                        }
                    };
                    a.onAccept = function () {
                        a.dialog.dialog("close");
                    };
                }
            }
            if (prop === "id" && target.properties[prop].node && $.isOpenValidator === false) {
                var sw1 = value === "";
                var sw2 = $.countValue(that.getData(), prop, value, true) > 1;
                var sw3 = target.properties[prop].regExp && target.properties[prop].regExp.test(target.properties[prop].value) === false;
                if (sw1 || sw2 || sw3) {
                    var a, b, old, type;
                    if (sw1)
                        type = "required";
                    if (sw2)
                        type = "duplicated";
                    if (sw3)
                        type = "invalid";
                    a = new FormDesigner.main.DialogInvalid(null, prop, type);
                    a.onClose = function () {
                        var existRegExp = target.properties[prop].regExp && target.properties[prop].regExpInv;
                        old = target.properties[prop].oldValue;
                        if (existRegExp && target.properties[prop].regExp.test(old) === false) {
                            old = old.replace(target.properties[prop].regExpInv, "");
                        }
                        b = target.properties.set(prop, old);
                        if (b.node) {
                            b.node.value = old;
                        }
                    };
                    a.onAccept = function () {
                        a.dialog.dialog("close");
                    };
                }
            }
            if (prop === "id" && target.properties[prop].node && target instanceof FormDesigner.main.GridItem) {
                target.properties["name"].value = value;
            }
            if (prop === "maxLength" && target.properties[prop].node) {
                if (value === "") {
                    var a, b, old;
                    a = new FormDesigner.main.DialogInvalid(null, prop, "required");
                    a.onClose = function () {
                        old = target.properties[prop].oldValue;
                        b = target.properties.set(prop, old);
                        if (b.node) {
                            b.node.value = old;
                        }
                    };
                    a.onAccept = function () {
                        a.dialog.dialog("close");
                    };
                }
            }
            if (prop === "variable" && target.properties[prop].node) {
                if (value !== "" && $.countValue(that.getData(), prop, value) > 1) {
                    $.isOpenValidator = true;
                    var a, b, old;
                    a = new FormDesigner.main.DialogInvalid(null, prop, "duplicated");
                    a.onClose = function () {
                        $.isOpenValidator = false;
                        old = target.properties[prop].oldValue;
                        b = target.properties.set(prop, old);
                        if (b.node) {
                            b.node.textContent = old === "" ? "..." : old;
                        }

                        old = target.properties["dataType"].oldValue;
                        b = target.properties.set("dataType", old);
                        if (b.node) {
                            b.node.textContent = old;
                        }

                        old = target.properties["name"].oldValue;
                        target.properties.set("name", old);

                        old = target.properties["dbConnectionLabel"].oldValue;
                        b = target.properties.set("dbConnectionLabel", old);
                        if (b.node) {
                            b.node.textContent = old;
                        }

                        old = target.properties["dbConnection"].oldValue;
                        target.properties.set("dbConnection", old);

                        old = target.properties["sql"].oldValue;
                        b = target.properties.set("sql", old);
                        if (b.node) {
                            b.node.textContent = old === "" ? "..." : old;
                        }

                        old = target.properties["options"].oldValue;
                        b = target.properties.set("options", old);
                        if (b.node) {
                            b.node.textContent = JSON.stringify(old);
                        }

                        old = target.properties["id"].oldValue;
                        b = target.properties.set("id", old);
                        if (b.node) {
                            b.node.value = old;
                        }
                    };
                    a.onAccept = function () {
                        a.dialog.dialog("close");
                    };
                }
            }
            if (prop === "gridStore" && target.properties.type.value === FormDesigner.main.TypesControl.form) {
                var sw = target.properties.gridStore.value;
                if (sw === false) {
                    target.properties.variable.value = "";
                    target.properties.dataType.value = "";
                    target.properties.protectedValue.value = false;
                }
                target.properties.variable.type = sw ? "labelbutton" : "hidden";
                target.properties.dataType.type = sw ? "label" : "hidden";
                target.properties.protectedValue.type = sw ? "checkbox" : "hidden";
                if (target.properties[prop].node) {
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "datasource") {
                if (value === "database") {
                    target.properties.dbConnectionLabel.type = "labelbutton";
                    target.properties.sql.type = "labelbutton";
                    target.properties.dataVariable.type = "hidden";
                }
                if (value === "dataVariable") {
                    target.properties.dbConnectionLabel.type = "hidden";
                    target.properties.sql.type = "hidden";
                    target.properties.dataVariable.type = "textbutton";
                }
                if (target.properties[prop].node) {
                    target.properties.dbConnectionLabel.value = "PM Database";
                    target.properties.dbConnection.value = "workflow";
                    target.properties.sql.value = "";
                    target.properties.dataVariable.value = "";
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "variable" && target.properties.type.value === FormDesigner.main.TypesControl.checkbox) {
                target.properties.options.type = value === "" ? "hidden" : "labelbutton";
                if (target.properties[prop].node) {
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "inp_doc_uid" && target.properties.type.value === FormDesigner.main.TypesControl.file) {
                if (target.properties.inp_doc_uid.value !== "") {
                    target.properties.size.disabled = true;
                    target.properties.sizeUnity.disabled = true;
                    target.properties.extensions.disabled = true;
                }
                if (target.properties.variable.node) {
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "useRelative" && target.properties.type.value === FormDesigner.main.TypesControl.datetime) {
                target.properties.minDate.disabled = value;
                target.properties.maxDate.disabled = value;
                target.properties.relativeMinDate.disabled = !value;
                target.properties.relativeMaxDate.disabled = !value;
                if (target.properties[prop].node) {
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "defaultDate" && target.properties.type.value === FormDesigner.main.TypesControl.datetime) {
                if (value === "today") {
                    target.properties.defaultDate.disabledTodayOption = true;
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
                if (value === "") {
                    target.properties.defaultDate.disabledTodayOption = false;
                    listProperties.clear();
                    listProperties.load(target.properties);
                }
            }
            if (prop === "required" && target.properties["requiredFieldErrorMessage"].node) {
                target.properties.requiredFieldErrorMessage.node.disabled = !value;
            }
        };
        this.form1.onSynchronizeVariables = function (variables) {
            var msg = "";
            var json = that.getData();
            for (var i = 0; i < variables.length; i++) {
                if (variables[i].create === undefined || variables[i].var_name !== variables[i].var_name_old) {
                    msg = msg + "+ '" + variables[i].var_name + "'<br>";
                }
                json = $.changeAllValue(json, "variable", variables[i].var_name_old, variables[i].var_name);
                json = $.changeAllValue(json, "id", variables[i].var_name_old, variables[i].var_name);
                json = $.changeAllValue(json, "name", variables[i].var_name_old, variables[i].var_name);
                json = $.changeAllValue(json, "var_name", variables[i].var_name_old, variables[i].var_name);
                json = $.changeAllValue(json, "var_uid", variables[i].var_uid_old, variables[i].var_uid);
                json = $.changeAllValue(json, "prj_uid", variables[i].prj_uid_old, variables[i].prj_uid);
            }
            listProperties.clear();
            var dynaform = that.form1.getData();
            that.form1.setData({
                dyn_content: JSON.stringify(json),
                dyn_description: dynaform.description,
                dyn_title: dynaform.name,
                dyn_type: "xmlform",
                dyn_uid: dynaform.id,
                dyn_version: 2
            });
            if (msg !== "") {
                new FormDesigner.main.DialogChangedVariables(null, msg);
            }
        };
        this.form1.properties.language.type = "select";
        this.form1.properties.mode.value = "edit";
        this.form1.properties.mode.items = [
            {value: "edit", label: "edit".translate()},
            {value: "view", label: "view".translate()},
            {value: "disabled", label: "disabled".translate()}
        ];
        this.form1.properties.mode.helpButton = "";
        this.form1.properties.gridStore.type = "hidden";
        this.form1.properties.printable.type = "checkbox";
        this.form1.properties.protectedValue.type = "hidden";
        this.form1.setData(this.dynaform);
        this.form1.setDirty();
        //this.form2.onSelect = function (properties) {
        //    listProperties.clear();
        //    listProperties.load(properties);
        //};
        //methods
        that.title.text(this.dynaform.dyn_title).attr("title", this.dynaform.dyn_title).tooltip({
            tooltipClass: "fd-tooltip",
            position: {my: "left top+1"}
        });
    };
    Designer.prototype._getAuxForm = function () {
        var form;
        if (!this._auxForm) {
            form = document.createElement('form');
            this._auxForm = form;
        }
        return this._auxForm;
    };
    Designer.prototype._disposeAuxForm = function () {
        var form = this._auxForm;
        if (form) {
            form.parentNode.removeChild(form);
        }
        this._auxForm = null;
    };
    Designer.prototype.show = function () {
        var a = document.body.childNodes;
        for (var i = 0; i < a.length; i++)
            $(a[i]).hide();
        $(this.container).show();
    };
    Designer.prototype.hide = function () {
        var a = document.body.childNodes;
        for (var i = 0; i < a.length; i++)
            $(a[i]).show();
        $(this.container).remove();
        this._disposeAuxForm();
        $(".loader").hide();
        this.onHide();
    };
    Designer.prototype.getForms = function () {
        return this.form1;
    };
    Designer.prototype.getData = function () {
        $.globalInvalidProperties = [];
        var data = {};
        data["name"] = this.form1.properties.name.value;
        data["description"] = this.form1.properties.description.value;
        data["items"] = [];
        data["items"].push(this.form1.getData());
        return data;
    };
    Designer.prototype.loadDynaforms = function () {
        $.ajax({
            async: false,
            url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id + "/dynaforms",
            method: "GET",
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + PMDesigner.project.keys.access_token);
            },
            success: function (data) {
                $.remoteDynaforms = data;
            }
        });
    };
    FormDesigner.extendNamespace('FormDesigner.main.Designer', Designer);
}());