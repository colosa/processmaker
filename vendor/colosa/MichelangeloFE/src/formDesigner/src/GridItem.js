(function () {
    var GridItem = function (render, variable, parent) {
        this.onSelect = new Function();
        this.onRemove = new Function();
        this.onSetProperty = new Function();
        this.render = render;
        this.variable = variable;
        this.parent = parent;
        this.disabled = false;
        GridItem.prototype.init.call(this);
    };
    GridItem.prototype.init = function () {
        var that = this, html;
        switch (this.render) {
            case FormDesigner.main.TypesControl.text:
                html = "<input type='text' value='' class='fd-gridForm-grid-text-column'>";
                break;
            case FormDesigner.main.TypesControl.textarea:
                html = "<textarea class='fd-gridForm-grid-textarea-column' style='resize:none;'></textarea>";
                break;
            case FormDesigner.main.TypesControl.dropdown:
                html = "<select class='fd-gridForm-grid-text-column' style='box-sizing:border-box;width:100%;'></select>";
                break;
            case FormDesigner.main.TypesControl.checkbox:
                html = "<input type='checkbox'>";
                break;
            case FormDesigner.main.TypesControl.checkgroup:
                html = "<input type='checkbox'>";
                break;
            case FormDesigner.main.TypesControl.radio:
                html = "<input type='radio'>";
                break;
            case FormDesigner.main.TypesControl.datetime:
                html = "<div style='height:18px;border:1px solid #c0c0c0;box-sizing:border-box;width:100%;background-color:white;'><span class='fd-gridForm-grid-suggest-placeholder'></span><img src='" + $.imgUrl + "fd-calendar.png' style='float:right;'/></div>";
                break;
            case FormDesigner.main.TypesControl.suggest:
                html = "<div style='height:18px;border:1px solid #c0c0c0;box-sizing:border-box;width:100%;background-color:white;'><span class='fd-gridForm-grid-suggest-placeholder'></span><img src='" + $.imgUrl + "fd-ui-list-box.png' style='float:right;'/></div>";
                break;
            case FormDesigner.main.TypesControl.hidden:
                html = "<div style='height:18px;border:1px dashed #c0c0c0;box-sizing:border-box;width:100%;background-color:white;'><img src='" + $.imgUrl + "fd-ui-text-field-hidden.png' style='float:right;'/></div>";
                break;
            case FormDesigner.main.TypesControl.title:
                html = "<span>...</span>";
                break;
            case FormDesigner.main.TypesControl.subtitle:
                html = "<span>...</span>";
                break;
            case FormDesigner.main.TypesControl.annotation:
                html = "<span>...</span>";
                break;
            case FormDesigner.main.TypesControl.link:
                html = "<span class='fd-gridForm-grid-link-column'></span>";
                break;
            case FormDesigner.main.TypesControl.image:
                html = "<img src='" + $.imgUrl + "fd-image.png'/>";
                break;
            case FormDesigner.main.TypesControl.file:
                html = "<button>Select file</button><span>No file was selected</span>";
            case FormDesigner.main.TypesControl.multipleFile:
                html = "<button>Select file</button><span>No file was selected</span>";
                break;
            case FormDesigner.main.TypesControl.submit:
                html = "<input type='button' value='button'>";
                break;
            case FormDesigner.main.TypesControl.button:
                html = "<input type='button' value='button'>";
                break;
        }
        this.html = $(
            "<div class=\"pm-mafe-grid-item\" pm-mafe-grid-item-type=\"" + this.render + "\" style='position:relative;background:white;'>" +
            "<div style='font-weight:bold;margin-bottom:2px;text-align:center;background:#2d3e50;color:white;padding:5px;height:12px;'>" +
            "   <span class='fd-gridForm-grid-griditem-columnLabel'></span><span class='fd-gridForm-grid-griditem-columnRequired'>*</span>" +
            "</div>" +
            "<div class='fd-gridForm-grid-griditem-columnType' style='padding:2px;background:#eaebed;height:31px;'></div>" +
            "</div>");
        this.html.find(".fd-gridForm-grid-griditem-columnType").append(html);
        this.html.data("objectInstance", this);
        this.html.on("click", function (e) {
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
            var a, b, fields, dialog;
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
            if (property === "dbConnectionLabel") {
                a = new FormDesigner.main.DialogDBConnection(null);
                a.onClick = function (option) {
                    a.dialog.dialog("close").remove();
                    b = that.properties.set(property, option.label);
                    b.node.textContent = option.label;

                    that.properties.set("dbConnection", option.value);
                };
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
                    b.node.value = regex.value;
                };
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
            that.onSetProperty(prop, value, that);
            if (prop === "label") {
                that.html.find(".fd-gridForm-grid-griditem-columnLabel").text(value);
            }
            if (prop === "value") {
                that.html.find(".fd-gridForm-grid-link-column").text(value);
            }
            if (prop === "required") {
                if (value)
                    that.html.find(".fd-gridForm-grid-griditem-columnRequired").show();
                else
                    that.html.find(".fd-gridForm-grid-griditem-columnRequired").hide();
            }
            if (prop === "options") {
                that.html.find(".fd-gridForm-grid-text-column").empty();
                for (var i = 0; i < value.length; i++) {
                    that.html.find(".fd-gridForm-grid-text-column").append("<option value='" + value[i].value + "'>" + value[i].label + "</option>");
                }
            }
            if (prop === "placeholder") {
                that.html.find(".fd-gridForm-grid-text-column").attr("placeholder", value);
                that.html.find(".fd-gridForm-grid-textarea-column").attr("placeholder", value);
                that.html.find(".fd-gridForm-grid-suggest-placeholder").text(value);
            }
        };
        this.properties.onGet = function (properties) {
            if (that.parent.properties.get().layout.value === "form") {
                that.properties.columnWidth.type = "hidden";
                that.properties.columnWidth.helpButton = "";
            }
            if (that.parent.properties.get().layout.value === "responsive") {
                that.properties.columnWidth.type = "text";
                that.properties.columnWidth.helpButton = "Percentage value.";
            }
            if (that.parent.properties.get().layout.value === "static") {
                that.properties.columnWidth.type = "text";
                that.properties.columnWidth.helpButton = "Pixel value.";
            }
        };
        // This section reset properties in clearbutton in grids
        this.properties.onClickClearButton = function (property) {
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
    GridItem.prototype.getData = function () {
        var prop = {};
        var a = this.properties.get();
        for (var b in a) {
            prop[b] = a[b].value;
        }
        prop["width"] = 100;//todo
        prop["title"] = prop.label;
        if (this.variable) {
            prop["var_name"] = this.variable.var_name;
        }
        return prop;
    };
    GridItem.prototype.setDisabled = function (disabled) {
        this.disabled = disabled;
        this.properties.setDisabled(disabled);
    };
    /**
     * Set properties for input document
     * @param params
     */
    GridItem.prototype.setInputDocument = function (params) {
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
    FormDesigner.extendNamespace('FormDesigner.main.GridItem', GridItem);
}());