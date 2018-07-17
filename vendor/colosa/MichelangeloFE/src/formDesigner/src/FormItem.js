(function () {
    var FormItem = function (options) {
        this.render = options.type;
        this.variable = options.variable;
        this.parent = options.parentObject;
        this.onSelect = new Function();
        this.onSetProperty = new Function();
        if (options.onSelect)
            this.onSelect = options.onSelect;
        this.onRemove = new Function();
        this.disabled = false;
        FormItem.prototype.init.call(this);
    };
    FormItem.prototype.init = function () {
        var that = this, html, label = "";
        switch (this.render) {
            case FormDesigner.main.TypesControl.text:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-text'><input type='text' style='width:100%;box-sizing:border-box;'/><div>";
                break;
            case FormDesigner.main.TypesControl.textarea:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-textarea'><textarea style='width:100%;box-sizing:border-box;height:18px;resize:vertical;padding:0px;'></textarea></div>";
                break;
            case FormDesigner.main.TypesControl.dropdown:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-dropdown'><select style='width:100%;box-sizing:border-box;'></select></div>";
                break;
            case FormDesigner.main.TypesControl.checkbox:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-checkbox'><input type='checkbox' value=''></div>";
                break;
            case FormDesigner.main.TypesControl.checkgroup:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-checkgroup'></div>";
                break;
            case FormDesigner.main.TypesControl.radio:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-radio'></div>";
                break;
            case FormDesigner.main.TypesControl.datetime:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-datetime'><span class='fd-gridForm-grid-suggest-placeholder'></span><img src='" + $.imgUrl + "fd-calendar.png' style='float:right;'></img></div>";
                break;
            case FormDesigner.main.TypesControl.suggest:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-suggest'><span class='fd-gridForm-grid-suggest-placeholder'></span><img src='" + $.imgUrl + "fd-ui-list-box.png' style='float:right;'></img></div>";
                break;
            case FormDesigner.main.TypesControl.hidden:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-hidden'><img src='" + $.imgUrl + "fd-ui-text-field-hidden.png' style='float:right;'></img></div>";
                break;
            case FormDesigner.main.TypesControl.title:
                html = "<div class='fd-gridForm-grid-title'><span style='white-space:nowrap;'></span></div>";
                break;
            case FormDesigner.main.TypesControl.subtitle:
                html = "<div class='fd-gridForm-grid-subtitle'><span style='white-space:nowrap;'></span></div>";
                break;
            case FormDesigner.main.TypesControl.annotation:
                html = "<div class='fd-gridForm-grid-annotation'><span style='white-space:nowrap;'></span></div>";
                break;
            case FormDesigner.main.TypesControl.link:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-link'><span style='color:blue;text-decoration:underline;'></span></div>";
                break;
            case FormDesigner.main.TypesControl.image:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-image'><img src='" + $.imgUrl + "fd-image.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.file:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-file'><button>Select file</button><span>No file was selected</span></div>";
                break;
            case FormDesigner.main.TypesControl.multipleFile:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-file'><button>Select file</button><span>No file was selected</span></div>";
                break;
            case FormDesigner.main.TypesControl.submit:
                html = "<button class='fd-gridForm-grid-submit'></button>";
                break;
            case FormDesigner.main.TypesControl.button:
                html = "<button class='fd-gridForm-grid-button'></button>";
                break;
            case FormDesigner.main.TypesControl.panel:
                html = "<div class='fd-gridForm-grid-panel'>" + "Panel: ".translate() + "<span class='fd-gridForm-grid-panel-placeholder'></span></div>";
                break;
            case FormDesigner.main.TypesControl.msgPanel:
                html = "<div class='fd-gridForm-grid-msgpanel'>" + "The specified subform could not be found in the process.".translate() + "<span class='fd-gridForm-grid-panel-placeholder'></span></div>";
                break;
            case FormDesigner.main.TypesControl.geomap:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-geomap'><img src='" + $.imgUrl + "fd-map.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.qrcode:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-qrcode'><img src='" + $.imgUrl + "fd-qr-code.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.signature:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-signature'><img src='" + $.imgUrl + "fd-text_signature.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.imagem:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-imagem'><img src='" + $.imgUrl + "fd-image-instagram.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.audiom:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-audiom'><img src='" + $.imgUrl + "fd-audio-card.png'></img></div>";
                break;
            case FormDesigner.main.TypesControl.videom:
                label = "<span class='fd-gridForm-field-label'></span>";
                html = "<div class='fd-gridForm-grid-videom'><img src='" + $.imgUrl + "fd-video.png'></img></div>";
                break;
        }
        if (label !== "")
            label = label + "<span class='fd-gridForm-grid-contentRequired'>*</span>";
        this.html = $("<div class='grid-item-field' tabindex='-1'>" + label + html + "</div>");
        this.html.data("objectInstance", this);
        this.html.on("click", function (e) {
            this.focus();
            e.stopPropagation();
            $.designerSelectElement(this, function () {
                if (that.disabled === true) {
                    return false;
                }
                that.onRemove();
            });
            that.onSelect(that.properties, that);
        });
        this.properties = new FormDesigner.main.Properties(this.render, this.html, that);
        this.properties.onClick = function (property) {
            var a, 
                b, 
                fields, 
                dialogCreateVariable,
                dialog;
            if (property === "formula") {
                fields = that.parent.getFieldObjects([
                    FormDesigner.main.TypesControl.text
                ]);
                a = new FormDesigner.main.DialogFormula(null, fields);
                a.onSave = function () {
                    that.properties.set(property, a.editor.getValue());
                };
                a.editor.setValue(that.properties.get()[property].value);
            }
            if (property === "variable") {
                dialogCreateVariable = new FormDesigner.main.DialogCreateVariable(null, that.render, [], that.properties.get()[property].value);
                dialogCreateVariable.onSave = function (variable) {
                    that.setVariable(variable);
                };
                dialogCreateVariable.onSelect = function (variable) {
                    dialogCreateVariable.dialog.dialog("close");
                    that.setVariable(variable);
                };
                FormDesigner.getNextNumberVar(that.getData(), that.properties, function (nextVar) {
                    dialogCreateVariable.setVarName(nextVar);
                });
            }
            if (property === "inputDocument") {
                dialog = new FormDesigner.main.DialogInputDocument(null);
                dialog.onClick = function (option) {
                    dialog.dialog.dialog("close").remove();
                    that.setInputDocument({
                        size: {
                            value: option.inp_doc_max_filesize,
                            disabled: true
                        },
                        sizeUnity: {
                            value: option.inp_doc_max_filesize_unit,
                            disabled: true
                        },
                        extensions: {
                            value: option.inp_doc_type_file,
                            disabled: true
                        },
                        enableVersioning: {
                            value: (option.inp_doc_versioning) ? true : false,
                            disabled: true
                        },
                        inp_doc_uid: {
                            value: option.inp_doc_uid,
                            disabled: true
                        },
                        inputDocument: {
                            value: option.inp_doc_title,
                            disabled: false
                        }
                    });
                };
                return false;
            }
            if (property === "dbConnectionLabel") {
                a = new FormDesigner.main.DialogDBConnection(null);
                a.onClick = function (option) {
                    a.dialog.dialog("close").remove();
                    b = that.properties.set(property, option.label);
                    b.node.textContent = option.label;

                    that.properties.set("dbConnection", option.value);
                };
            }
            if (property === "sql") {
                a = new FormDesigner.main.DialogSql(null);
                a.onSave = function () {
                    b = that.properties.set(property, a.editor.getValue());
                    b.node.textContent = a.editor.getValue();
                };
                a.editor.setValue(that.properties.get()[property].value);
            }
            if (property === "options") {
                a = new FormDesigner.main.DialogOptions(null, that.properties.get()[property].value);
                a.onApply = function () {
                    b = that.properties.set(property, a.getOptions());
                    b.node.textContent = JSON.stringify(a.getOptions());
                };
                if (that.properties.dataType.value === "boolean") {
                    a.setOptionsBoolean();
                }
            }
            if (property === "validate") {
                a = new FormDesigner.main.DialogRegularExpression(null, that.properties["dataType"].value);
                a.onClick = function (regex) {
                    a.dialog.dialog("close").remove();
                    b = that.properties.set("validate", regex.value);
                    if (b.node)
                        b.node.value = regex.value;
                };
            }
            if (property === "content") {
                var a = new FormDesigner.main.DialogContent();
                a.onSave = function () {
                    that.properties.set(property, a.editor.getValue());
                };
                a.editor.setValue(that.properties.get()[property].value);
            }
            if (property === "dataVariable") {
                a = new FormDesigner.main.DialogVariable(null, FormDesigner.main.TypesControl.checkgroup);
                a.onClick = function (variable) {
                    a.dialog.dialog("close").remove();
                    b = that.properties.set("dataVariable", "@@" + variable.var_name);
                    if (b.node)
                        b.node.value = variable.var_name;
                };
                a.load();
            }
        };
        this.properties.onSet = function (prop, value) {
            var oValue, oLabel;
            that.onSetProperty(prop, value, that);
            if (prop === "id") {
                that.html.find(".fd-gridForm-grid-panel-placeholder").text(value);
            }
            if (prop === "label") {
                that.html.find(".fd-gridForm-field-label").text(value);
                that.html.find(".fd-gridForm-grid-title").find("span").text(value);
                that.html.find(".fd-gridForm-grid-subtitle").find("span").text(value);
                that.html.find(".fd-gridForm-grid-annotation").find("span").text(value);
                that.html.find(".fd-gridForm-grid-submit").text(value);
                that.html.find(".fd-gridForm-grid-button").text(value);
            }
            if (prop === "value") {
                that.html.find(".fd-gridForm-grid-link").find("span").text(value);
            }
            if (prop === "required") {
                if (value)
                    that.html.find(".fd-gridForm-grid-contentRequired").show();
                else
                    that.html.find(".fd-gridForm-grid-contentRequired").hide();
            }
            if (prop === "options") {
                that.html.find(".fd-gridForm-grid-dropdown").find("select").empty();
                that.html.find(".fd-gridForm-grid-checkgroup").empty();
                that.html.find(".fd-gridForm-grid-radio").empty();
                for (var i = 0; i < value.length; i++) {
                    oValue = $('<div />').text(value[i].value).html();
                    oLabel = $('<div />').text(value[i].label).html();
                    that.html.find(".fd-gridForm-grid-dropdown").find("select").append("<option value='" + oValue + "'>" + oLabel + "</option>");
                    that.html.find(".fd-gridForm-grid-checkgroup").append("<label><input type='checkbox' value='" + oValue + "'><span>" + oLabel + "</span></label>");
                    that.html.find(".fd-gridForm-grid-radio").append("<label><input type='radio' value='" + oValue + "'><span>" + oLabel + "</span></label>");
                }
            }
            if (prop === "placeholder") {
                that.html.find(".fd-gridForm-grid-text").find("input").attr("placeholder", value);
                that.html.find(".fd-gridForm-grid-textarea").find("textarea").attr("placeholder", value);
                that.html.find(".fd-gridForm-grid-suggest-placeholder").text(value);
            }
        };
        this.properties.onClickClearButton = function (property) {
            var b, prop;
            if (property === "variable" && that.properties[property].value !== "") {
                var a = new FormDesigner.main.DialogConfirmClearVariable();
                a.onAccept = function () {
                    var label = that.properties.label.value;
                    that.parent.setNextLabel(that.properties);

                    that.properties.id.node.value = that.properties.id.value;
                    b = that.properties.set("variable", "");
                    b.node.textContent = "...";
                    b = that.properties.set("dataType", "");
                    b.node.textContent = "";
                    b = that.properties.set("label", label);
                    if (b.node)
                        b.node.value = label;

                    b = that.properties.set("dbConnectionLabel", "PM Database");
                    if (b.node)
                        b.node.textContent = "PM Database";
                    b = that.properties.set("dbConnection", "workflow");
                    b = that.properties.set("sql", "");
                    if (b.node)
                        b.node.textContent = "...";
                    b = that.properties.set("options", JSON.parse("[]"));
                    if (b.node)
                        b.node.textContent = "[]";

                    that.properties.size.disabled = false;
                    b = that.properties.set("size", "1024");
                    if (b.node) {
                        b.node.value = "1024";
                    }

                    that.properties.sizeUnity.disabled = false;
                    b = that.properties.set("sizeUnity", "KB");
                    if (b.node) {
                        b.node.value = "KB";
                    }

                    that.properties.extensions.disabled = false;
                    b = that.properties.set("extensions", "*");
                    if (b.node) {
                        b.node.value = "*";
                    }
                    that.properties.set("inp_doc_uid", "");
                };
            }
            if (property === "minDate" || property === "maxDate" || property === "defaultDate") {
                b = that.properties.set(property, "");
                if (b.node)
                    b.node.value = "";
            }
            if (property === "inputDocument") {
                that.setInputDocument({
                    size: {
                        value: 0,
                        disabled: false
                    },
                    sizeUnity: {
                        value: 'MB',
                        disabled: false
                    },
                    extensions: {
                        value: '*',
                        disabled: false
                    },
                    enableVersioning: {
                        value: false,
                        disabled: true
                    },
                    inp_doc_uid: {
                        value: null,
                        disabled: true
                    },
                    inputDocument: {
                        value: '...',
                        disabled: false
                    }
                });
            }
        };
    };
    FormItem.prototype.getData = function () {
        var data = {}, property, prop = this.properties.get();
        for (property in prop) {
            data[property] = prop[property].value;
        }
        if (this.variable) {
            data["var_name"] = this.variable["var_name"];
        }
        return data;
    };
    FormItem.prototype.setDisabled = function (disabled) {
        this.disabled = disabled;
        this.properties.setDisabled(disabled);
    };
    FormItem.prototype.setVariable = function (variable) {
        var that = this, b;
        that.variable = variable;
        that.properties.set("var_uid", variable.var_uid);
        b = that.properties.set("variable", variable.var_name);
        if (b.node)
            b.node.textContent = variable.var_name;
        b = that.properties.set("dataType", variable.var_field_type);
        if (b.node)
            b.node.textContent = variable.var_field_type;
        b = that.properties.set("id", variable.var_name);
        if (b.node)
            b.node.value = variable.var_name;
        b = that.properties.set("name", variable.var_name);
        if (b.node)
            b.node.value = variable.var_name;

        b = that.properties.set("dbConnectionLabel", variable.var_dbconnection_label);
        if (b.node)
            b.node.textContent = variable.var_dbconnection_label;
        that.properties.set("dbConnection", variable.var_dbconnection);

        b = that.properties.set("sql", variable.var_sql);
        if (b.node)
            b.node.textContent = variable.var_sql === "" ? "..." : variable.var_sql;
        b = that.properties.set("options", JSON.parse(variable.var_accepted_values));
        if (b.node)
            b.node.textContent = variable.var_accepted_values;

        if (typeof variable.inp_doc_uid === "string" && variable.inp_doc_uid.length > 0) {
            $.ajax({
                async: false,
                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id + "/input-document/" + variable.inp_doc_uid,
                method: "GET",
                contentType: "application/json",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + PMDesigner.project.keys.access_token);
                },
                success: function (data) {
                    that.properties.size.disabled = true;
                    b = that.properties.set("size", data.inp_doc_max_filesize);
                    if (b.node) {
                        b.node.value = data.inp_doc_max_filesize;
                    }

                    that.properties.sizeUnity.disabled = true;
                    b = that.properties.set("sizeUnity", data.inp_doc_max_filesize_unit);
                    if (b.node) {
                        b.node.value = data.inp_doc_max_filesize_unit;
                    }
                    b = that.properties.set("enableVersioning", data.inp_doc_versioning === 1);
                    if (b.node) {
                        b.node.enableVersioning = data.inp_doc_versioning === 1;
                    }
                    that.properties.extensions.disabled = true;
                    b = that.properties.set("extensions", data.inp_doc_type_file);
                    if (b.node) {
                        b.node.value = data.inp_doc_type_file;
                    }

                    that.properties.set("inp_doc_uid", data.inp_doc_uid);
                }
            });
        }
    };
    /**
     * Set inputDocument properties to a field
     * @param params
     */
    FormItem.prototype.setInputDocument = function (params) {
        var property,
            key;
        for (key in params) {
            property = this.properties.set(key, params[key].value);
            switch (key) {
                case 'inp_doc_uid':
                    break;
                case 'inputDocument':
                    if (property.node) {
                        property.node.textContent = params[key].value;
                    }
                    break;
                case 'enableVersioning':
                    if (property.node) {
                        property.node.textContent = (params[key].value) ? 'Yes' : 'No';
                    }
                default:
                    if (property.node) {
                        property.node.value = params[key].value;
                        property.node.disabled = params[key].disabled;
                    }
                    break;
            }
        }
    };
    FormDesigner.extendNamespace('FormDesigner.main.FormItem', FormItem);
}());
