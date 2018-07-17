var PMVariables = function (options) {
    var that = this;
    this.initialFormAcceptedValuesKeyValue = null;
    this.initialFormAcceptedValuesValue = null;
    this.initialGridAcceptedValuesOrder = [];
    this.validateAcceptedValuesFlag = false;
    this.var_uid = null;
    this.var_name = null;
    this.edit = false;
    this.editRow = null;
    this.dirtyAcceptedValue = false;
    this.fieldInfo = null;
    this.onSave = new Function();
    this.onEdit = new Function();
    this.clickedClose = true;
    this.editingOptions = false;
    this.editRow = null;
    this.currentVariable = null;
    this.onWindowClose = new Function();
    this.buttonCreate = new PMUI.ui.Button({
        id: 'buttonCreate',
        text: 'Create'.translate(),
        height: '36px',
        width: 100,
        style: {
            cssClasses: [
                'mafe-button-create'
            ]

        },
        handler: function () {
            that.showForm();
        }
    });

    this.buttonCreateInputDocument = new PMUI.field.ButtonField({
        id: 'buttonCreateInputDocument',
        value: 'Create'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        proportion: 0.8,
        handler: function (field) {
            var inputDocument = new InputDocument();
            inputDocument.build();
            inputDocument.openFormInMainWindow();
            inputDocument.method = "POST";
        }
    });

    this.buttonEditInputDocument = new PMUI.field.ButtonField({
        id: 'buttonEditInputDocument',
        value: 'Edit'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        proportion: 0.8,
        handler: function (field) {
            var form = that.formVariables,
                fieldInpDoc = form.getField('inp_doc_uid'),
                inp_doc_uid = fieldInpDoc.getValue(),
                defaultText = "- Select an input document -".translate(),
                inputDocument;
            if (inp_doc_uid && inp_doc_uid !== defaultText) {
                inputDocument = new InputDocument();
                inputDocument.build();
                inputDocument.inputDocumentOriginDataForUpdate = {};
                inputDocument.openFormInMainWindow();
                inputDocument.inputDocumentFormGetProxy(inp_doc_uid);
            } else {
                fieldInpDoc.setValue("");
                form.isValid();
            }
        }
    });

    that.buttonCreateInputDocument.controls[0].button.setButtonType("success");
    that.buttonCreateInputDocument.controls[0].button.setStyle({
        cssClasses: ["mafe-button-create-variable", "pmui-success"],
        cssProperties: {padding: "8px 15px", border: "0px"}
    });
    that.buttonEditInputDocument.controls[0].button.setButtonType("success");
    that.buttonEditInputDocument.controls[0].button.setStyle({
        cssClasses: ["mafe-button-edit-variable", "pmui-success"],
        cssProperties: {padding: "8px 15px", border: "0px"}
    });

    var inp_doc_uid = new PMUI.field.DropDownListField({
        id: "inp_doc_uid",
        name: "inp_doc_uid",
        value: "",
        required: true,
        label: "Related Input Document".translate(),
        controlsWidth: 460,
        valueType: "string",
        labelPosition: "top",
        onChange: function () {
            that.validateInputDoc();
        }
    });

    this.buttonFieldAdd = new PMUI.field.ButtonField({
        id: 'buttonFieldAdd',
        pmType: 'buttonField',
        value: 'Create'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        controlsWidth: 50,
        proportion: 0.8,
        handler: function (field) {
            that.addAcceptedValue();
        },
        style: {
            cssProperties: {
                "margin-left": "10px"
            }
        }
    });
    this.buttonFieldAdd.getControl().button.setButtonType("success");
    this.buttonFieldCancel = new PMUI.field.ButtonField({
        id: 'buttonFieldCancel',
        pmType: 'buttonField',
        value: 'Cancel'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        controlsWidth: 55,
        proportion: 0.6,
        handler: function (field) {
            that.clickedClose = false;
            that.cancelAcceptedValue();
            that.editingOptions = false;
        }
    });
    this.buttonFieldCancel.getControl().button.setButtonType("error");

    this.formVariables = new PMUI.form.Form({
        id: 'formVariables',
        width: 'auto',
        title: '',
        visibleHeader: false,
        items: [
            {
                pmType: 'text',
                label: 'Variable Name'.translate(),
                placeholder: "Name".translate(),
                id: 'variableName',
                value: '',
                name: 'var_name',
                required: true,
                valueType: 'string',
                maxLength: 60,
                controlsWidth: 460,
                validators: [
                    {
                        pmType: "regexp",
                        criteria: /^[a-zA-Z\_]{1}\w+$/,
                        errorMessage: "A valid variable starts with a letter or underscore, followed by any number of letters, numbers, or underscores.".translate()
                    }
                ]
            }, {
                pmType: 'text',
                label: 'Label'.translate(),
                placeholder: 'Label'.translate(),
                id: 'variableLabel',
                value: 'label',
                name: 'var_label',
                valueType: 'string',
                maxLength: 60,
                controlsWidth: 460,
                visible: false
            }, {
                pmType: 'dropdown',
                label: 'Variable Type'.translate(),
                placeholder: 'Variable type'.translate(),
                id: 'varType',
                value: 'string',
                name: 'var_field_type',
                required: true,
                valueType: 'string',
                controlsWidth: 460,
                options: [
                    {
                        label: 'String',
                        value: 'string'
                    }, {
                        label: 'Integer',
                        value: 'integer'
                    }, {
                        label: 'Float',
                        value: 'float'
                    }, {
                        label: 'Boolean',
                        value: 'boolean'
                    }, {
                        label: 'Datetime',
                        value: 'datetime'
                    }, {
                        label: 'Grid',
                        value: 'grid'
                    }, {
                        label: 'Array',
                        value: 'array'
                    }, {
                        label: "File",
                        value: "file"
                    }, {
                        label: "Multiple File",
                        value: "multiplefile"
                    }, {
                        label: "Object",
                        value: "object"
                    }

                ],
                onChange: function (newValue, oldValue) {
                    var sw = that.gridAcceptedValues.visible === false ? true : (that.gridAcceptedValues.getData().length === 0);
                    var sw2 = that.formBooleanOptions.visible === false ? true : (that.formBooleanOptions.getField('trueOption').getValue() + that.formBooleanOptions.getField('falseOption').getValue()) === '';
                    if (sw && sw2) {
                        that.changeViewFieldType(newValue, oldValue);
                        that.resetAcceptedValuesPanel();
                        that.gridAcceptedValues.clearItems();
                        that.resetBooleanPanel();
                        return;
                    }
                    var message_window = new PMUI.ui.MessageWindow({
                        id: 'messageWindowCancel',
                        width: 490,
                        title: 'Variables'.translate(),
                        windowMessageType: 'warning',
                        bodyHeight: 'auto',
                        message: "This action will delete all options. Do you want to continue?".translate(),
                        footerItems: [
                            {
                                id: 'messageWindowNo',
                                text: 'No'.translate(),
                                handler: function () {
                                    message_window.close();
                                    that.formVariables.getField('var_field_type').setValue(oldValue);
                                },
                                buttonType: "error"
                            },
                            {
                                id: 'messageWindowYes',
                                text: 'Yes'.translate(),
                                handler: function () {
                                    message_window.close();
                                    that.changeViewFieldType(newValue, oldValue);
                                    that.resetAcceptedValuesPanel();
                                    that.gridAcceptedValues.clearItems();
                                    that.resetBooleanPanel();
                                },
                                buttonType: "success"
                            }
                        ],
                        onClose: function () {
                        }
                    });
                    message_window.open();
                    message_window.showFooter();
                }
            }, {
                pmType: "panel",
                id: "inp_doc_uidPanel",
                fieldset: false,
                layout: "hbox",
                items: [
                    inp_doc_uid,
                    that.buttonCreateInputDocument,
                    that.buttonEditInputDocument
                ]
            },
            {
                id: 'booleanPanel',
                pmType: 'panel',
                legend: 'Options'.translate(),
                fieldset: true,
                layout: 'vbox',
                items: [
                    {
                        pmType: 'panel',
                        layout: 'hbox',
                        items: [
                            new PMLabelField({
                                text: 'Key'.translate(),
                                textMode: 'plain',
                                style: {
                                    cssProperties: {
                                        color: '#AEAEAE',
                                        'font-weight': 'bold'
                                    }
                                },
                                proportion: 0.3
                            }),
                            new PMLabelField({
                                text: 'Label'.translate(),
                                textMode: 'plain',
                                style: {
                                    cssProperties: {
                                        color: '#AEAEAE',
                                        'font-weight': 'bold'
                                    }
                                }
                            })
                        ]
                    },
                    {
                        pmType: 'text',
                        name: 'trueOption',
                        label: 'True'.translate(),
                        controlsWidth: 460,
                        valueType: 'string',
                        maxLength: 100,
                        required: true
                    }, {
                        pmType: 'text',
                        name: 'falseOption',
                        label: 'False'.translate(),
                        controlsWidth: 460,
                        valueType: 'string',
                        maxLength: 100,
                        required: true
                    }
                ]
            },
            {
                pmType: 'dropdown',
                label: 'Database Connection'.translate(),
                placeholder: 'Database Connection'.translate(),
                id: 'varConnection',
                value: 'none',
                name: 'var_dbconnection',
                controlsWidth: 460,
                options: [{
                    label: 'PM Database',
                    value: 'workflow'
                }
                ],
                onChange: function (newValue, oldValue) {
                }
            }, {
                pmType: 'textarea',
                label: 'SQL'.translate(),
                placeholder: "Insert a SQL query like: SELECT [Key field], [Label field] FROM [Table name]".translate(),
                id: 'varSql',
                value: '',
                name: 'var_sql',
                valueType: 'string',
                controlsWidth: 460,
                style: {cssClasses: ['mafe-textarea-resize']}
            }, {
                pmType: 'checkbox',
                label: 'Define accepted variable values'.translate(),
                id: "chckboxOption",
                name: 'var_options_control',
                controlsWidth: 460,
                options: [{value: '1', label: ''}],
                onChange: function (newValue, oldValue) {
                    that.changeViewFieldType(that.formVariables.getField('var_field_type').getValue());
                }
            },
            {
                id: 'formAcceptedValues',
                pmType: 'panel',
                fieldset: false,
                layout: 'hbox',
                items: [
                    {
                        pmType: 'text',
                        name: 'keyValue',
                        id: "variable-keyvalue",
                        label: 'Key'.translate(),
                        labelWidth: '100%',
                        controlsWidth: 210,
                        proportion: 2.5,
                        valueType: 'string',
                        maxLength: 255,
                        labelPosition: "top"
                    },
                    {
                        pmType: 'text',
                        name: 'value',
                        id: "variable-value",
                        label: 'Label'.translate(),
                        labelWidth: '100%',
                        controlsWidth: 300,
                        valueType: 'string',
                        maxLength: 255,
                        proportion: 3.4,
                        labelPosition: "top"
                    },
                    that.buttonFieldCancel,
                    that.buttonFieldAdd
                ]
            }
        ]
    });
    this.formVariables.getData = function () {
        var data = getData2PMUI(that.formVariables.html);
        return data;
    };
    this.gridVariables = new PMUI.grid.GridPanel({
        id: 'gridVariables',
        pageSize: 10,
        width: '96%',
        style: {
            cssClasses: ['mafe-gridPanel']
        },
        filterPlaceholder: 'Search ...'.translate(),
        emptyMessage: 'No records found'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        tableContainerHeight: 374,
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return '';
        },
        columns: [{
            id: 'varName',
            title: 'Name'.translate(),
            dataType: 'string',
            columnData: 'var_name',
            alignmentCell: 'left',
            width: '300px',
            sortable: true
        }, {
            id: 'varType',
            title: 'Type'.translate(),
            dataType: 'string',
            alignmentCell: 'left',
            columnData: 'var_field_type',
            sortable: true
        }, {
            id: 'varEdit',
            dataType: 'button',
            title: '',
            buttonLabel: 'Edit'.translate(),
            width: '60px',
            buttonStyle: {
                cssClasses: [
                    'mafe-button-edit'
                ]
            },
            onButtonClick: function (row, grid) {
                that.showFormEdit(row.getData());
            }
        }, {
            id: 'varDelete',
            dataType: 'button',
            title: '',
            buttonLabel: function (row, data) {
                return 'Delete'.translate();
            },
            width: '70px',
            buttonStyle: {
                cssClasses: [
                    'mafe-button-delete'
                ]
            },
            onButtonClick: function (row, grid) {
                that.del(row.getData(), row, grid);
            }
        }
        ],
        dataItems: null
    });
    this.gridAcceptedValues = new PMUI.grid.GridPanel({
        id: 'gridAcceptedValues',
        pageSize: 5,
        style: {cssClasses: ['mafe-gridPanel']},
        filterPlaceholder: 'Text to Search'.translate(),
        emptyMessage: 'No records found'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        filterable: false,
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return '';
        },
        columns: [{
            id: 'keyvalue',
            title: 'Key'.translate(),
            columnData: 'keyValue',
            dataType: 'string',
            alignmentCell: 'left',
            width: 180
        }, {
            id: 'label',
            title: 'Label'.translate(),
            columnData: 'value',
            dataType: 'string',
            alignmentCell: 'left',
            width: 300
        }, {
            id: 'buttonEdit',
            dataType: 'button',
            title: '',
            buttonLabel: 'Edit'.translate(),
            width: 60,
            buttonStyle: {cssClasses: ['mafe-button-edit']},
            onButtonClick: function (row, grid) {
                that.editRow = row;
                that.editAcceptedValue(row);
            }
        }, {
            id: 'buttonDelete',
            dataType: 'button',
            title: '',
            buttonLabel: function (row, data) {
                return 'Delete'.translate();
            },
            width: 75,
            buttonStyle: {cssClasses: ['mafe-button-delete']},
            onButtonClick: function (row, grid) {
                if (row !== that.editRow) {
                    that.deleteAcceptedValue(row);
                } else {
                    PMDesigner.msgFlash('The row can not be removed, because is being edited.'.translate(), document.getElementById('windowVariables'), 'error', 1000, 5);
                }
            }
        }
        ],
        dataItems: null,
        behavior: 'dragdropsort'
    });
    this.isDirtyFormVariables = function () {
        $("input,select,textarea").blur();
        if (this.formVariables.isVisible()) {
            this.validateAcceptedValues();
            if (this.formVariables.isDirty() || this.dirtyAcceptedValue || this.validateAcceptedValuesFlag) {
                //if (this.formVariables.getField("var_options_control").controls[0].selected) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "messageWindowCancel",
                    width: 490,
                    title: "Variables".translate(),
                    windowMessageType: "warning",
                    bodyHeight: "auto",
                    message: 'Are you sure you want to discard your changes?'.translate(),
                    footerItems: [{
                        id: "messageWindowNo",
                        text: "No".translate(),
                        handler: function () {
                            message_window.close();
                        },
                        buttonType: "error"
                    },
                        {
                            id: "messageWindowYes",
                            text: "Yes".translate(),
                            handler: function () {
                                if (that.clickedClose) {
                                    that.windowVariables.close();
                                }
                                message_window.close();
                                that.showGrid();
                                that.windowVariables.hideFooter();
                            },
                            buttonType: "success"
                        },
                    ]
                });

                message_window.open();
                message_window.showFooter();
                /*} else {
                 that.showGrid();
                 }*/
            } else {
                if (that.clickedClose) {
                    that.windowVariables.close();
                } else {
                    that.showGrid();
                }
                this.initialFormAcceptedValuesKeyValue = null;
                this.initialFormAcceptedValuesValue = null;
                this.initialGridAcceptedValuesOrder = [];
                that.windowVariables.hideFooter();
            }
        } else {
            this.windowVariables.close();
        }
    };
    this.windowVariables = new PMUI.ui.Window({
        id: 'windowVariables',
        title: 'Variables'.translate(),
        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH,
        onBeforeClose: function () {
            that.clickedClose = true;
            that.isDirtyFormVariables();
        },
        footerItems: [
            new PMUI.ui.Button({
                id: "windowVariablesCancel",
                text: "Cancel".translate(),

                handler: function () {
                    that.clickedClose = false;
                    that.isDirtyFormVariables();
                },

                buttonType: "error"
            }),

            new PMUI.ui.Button({
                id: "windowVariablesSave",
                text: "Save".translate(),

                handler: function () {
                    if (that.edit) {
                        that.updateVariables();
                    } else {
                        that.saveVariables();
                    }
                },

                buttonType: "success"
            })
        ],
        onClose: function () {
            that.onWindowClose(that.currentVariable);
        },
        visibleFooter: true,
        buttonPanelPosition: "bottom"
    });
    PMVariables.prototype.init.call(this);
    that.setInputDocuments(inp_doc_uid);
};
PMVariables.prototype.init = function () {
    var that = this,
        acceptedValuesForm,
        label;

    that.buttonCreate.defineEvents();

    that.windowVariables.addItem(that.gridVariables);
    that.windowVariables.addItem(that.formVariables);
    that.windowVariables.addItem(that.gridAcceptedValues);
    that.windowVariables.hideFooter();
    that.windowVariables.open();
    label = $('#booleanPanel');
    acceptedValuesForm = $('#formAcceptedValues');
    that.customCss();
    acceptedValuesForm.find(".pmui-field-message").css("marginLeft", 10);
    $("#gridAcceptedValues").css({"height": "254px", "margin": "0 10px"});
    $("#requiredMessage").css({"margin-top": "10px"});
    $("#inp_doc_uid").find(".pmui-field-message:eq(0)").css("left", "226px");
    this.formAcceptedValues = PMUI.getPMUIObject(acceptedValuesForm.get(0));

    this.buttonFieldAdd.controls[0].button.setStyle({cssProperties: {padding: "6px 15px"}});
    this.buttonFieldCancel.controls[0].button.setStyle({cssProperties: {padding: "6px 15px"}});
    $('#gridVariables .pmui-textcontrol').css({'margin-top': '5px', width: '250px'});
    that.gridVariables.dom.toolbar.appendChild(that.buttonCreate.getHTML());

    this.formBooleanOptions = PMUI.getPMUIObject(label.get(0));
    that.showGrid();
    that.loadDataBaseConnections();

    validateKeysField(that.formVariables.getField('var_name').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'isletter', 'isunderscore']);

    that.resetAcceptedValuesPanel();
    label = $('#booleanPanel').css({'width': '675px', margin: '10px'}).find(".pmui-pmlabelfield");
    $(label[0]).replaceWith($(label[0]).find(".pmui-pmlabelcontrol").css({
        "font-size": "14px",
        "margin-right": "127px"
    }));
    $(label[1]).replaceWith($(label[1]).find(".pmui-pmlabelcontrol").css({
        "font-size": "14px",
        "margin-right": "127px"
    }));
    this.formVariables.panel.html.style.overflow = "scroll !important";
    $(this.formVariables.panel.html).removeClass("pmui-formpanel");
    $(this.formVariables.panel.html).append(that.formAcceptedValues.html);
    $(this.formVariables.panel.html).append(that.gridAcceptedValues.html);
    $(that.formAcceptedValues.html).find(".pmui-formpanel").css({"display": "inline-block"});
    that.windowVariables.footer.html.style.textAlign = "right";
};
PMVariables.prototype.saveVariables = function () {
    var that = this,
        data,
        inp_doc_uid_value = this.formVariables.getField("inp_doc_uid").controls[0].value;
    this.formVariables.getField("inp_doc_uid").setValue(inp_doc_uid_value);
    if (!this.formVariables.isValid()) {
        return;
    }
    if (this.formBooleanOptions.visible && !this.formBooleanOptions.isValid()) {
        return;
    }
    data = this.formVariables.getData();
    data.var_label = data.var_field_type;

    data.var_default = '';
    data.var_accepted_values = that.getDataAcceptedValues();
    data.var_field_size = 10;
    if (!this.formVariables.getField('var_sql').visible)
        data.var_sql = "";
    (new PMRestClient({
        endpoint: 'process-variable',
        typeRequest: 'post',
        functionSuccess: function (xhr, response) {
            if (that.onSave(xhr, response) === false) {
                that.var_uid = null;
                return;
            }
            that.showGrid();
            that.load();
            that.var_uid = null;
            that.windowVariables.hideFooter();
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
        messageError: ' ',
        data: data,
        messageSuccess: 'Variable saved successfully'.translate(),
        flashContainer: that.panel
    })).executeRestClient();
};
PMVariables.prototype.updateVariables = function () {
    var that = this,
        data,
        inp_doc_uid_value = this.formVariables.getField("inp_doc_uid").controls[0].value;
    this.formVariables.getField("inp_doc_uid").setValue(inp_doc_uid_value);
    if (!this.formVariables.isValid()) {
        return;
    }
    if (this.formBooleanOptions.visible && !this.formBooleanOptions.isValid()) {
        return;
    }
    data = this.formVariables.getData();
    data.var_label = data.var_field_type;
    data.var_field_size = 10;
    if (that.formVariables.getField('var_name').getValue().trim() === that.var_name.trim()) {
        delete data['var_name'];
    }
    data.var_default = '';
    data.var_accepted_values = that.getDataAcceptedValues();
    if (!this.formVariables.getField('var_sql').visible)
        data.var_sql = "";
    (new PMRestClient({
        endpoint: 'process-variable/' + that.var_uid,
        typeRequest: 'update',
        data: data,
        functionSuccess: function (xhr, response) {
            if (that.onEdit(xhr, response, data) === false) {
                that.var_uid = null;
                return;
            }
            var var_name_old = that.currentVariable.var_name;
            that.currentVariable = data;
            that.currentVariable.var_uid = that.var_uid;
            that.currentVariable.var_name_old = var_name_old;
            that.showGrid();
            that.load();
            that.var_uid = null;
            that.windowVariables.hideFooter();
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
        messageError: ' ',
        messageSuccess: 'Variable edited successfully'.translate(),
        flashContainer: that.panel
    })).executeRestClient();
};
PMVariables.prototype.deleteVariable = function (var_uid) {
    var that = this;
    (new PMRestClient({
        endpoint: 'process-variable/' + var_uid,
        typeRequest: 'remove',
        functionSuccess: function (xhr, response) {
            that.load();
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
        messageError: 'working...'.translate(),
        messageSuccess: 'Variable deleted successfully'.translate(),
        flashContainer: that.panel
    })).executeRestClient();
};
PMVariables.prototype.load = function () {
    var that = this;
    var restProxy = new PMRestClient({
        endpoint: 'process-variables',
        typeRequest: 'get',
        functionSuccess: function (xhr, response) {
            var listInputDocs = response;
            that.gridVariables.setDataItems(listInputDocs);
            that.gridVariables.sort('var_name', 'asc');
            $(that.gridVariables.dom.toolbar).find("input").val("");
            that.gridVariables.clearFilter();
            if (that.currentVariable !== null) {
                var var_name_old = that.currentVariable.var_name_old;
                for (var i = 0; i < response.length; i++) {
                    if (that.currentVariable.var_uid === response[i].var_uid) {
                        that.currentVariable = response[i];
                    }
                }
                that.currentVariable.var_name_old = var_name_old;
            }
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restProxy.executeRestClient();
};
PMVariables.prototype.del = function (data, row, grid) {
    var that = this;
    var confirmWindow = new PMUI.ui.MessageWindow({
        id: 'confirmWindowDel',
        windowMessageType: 'warning',
        width: 490,
        bodyHeight: 'auto',
        title: "Variables".translate(),
        message: 'Do you want to delete this variable?'.translate(),
        footerItems: [{
            text: 'No'.translate(),
            visible: true,
            handler: function () {
                confirmWindow.close();
            },
            buttonType: "error"
        }, {
            text: 'Yes'.translate(),
            visible: true,
            handler: function () {
                confirmWindow.close();
                that.deleteVariable(data.var_uid, row);
            },
            buttonType: "success"
        }
        ]
    });
    confirmWindow.open();
    confirmWindow.dom.titleContainer.style.height = '17px';
    confirmWindow.showFooter();
};

PMVariables.prototype.customCss = function () {
    $("#inp_doc_uidPanel").css({padding: ""});
    $("#inp_doc_uid").css({width: "75%"}).find("label:eq(0)").css({float: "left", width: "31.5%"});

    var td = $("#chckboxOption .pmui-field-control-table td")[0];

    if (typeof(td) != "undefined") {
        td.setAttribute("style", "padding:0px !important");
    }

    this.formVariables.panel.getHTML().setAttribute("style", "overflow: initial");
};

PMVariables.prototype.showGrid = function () {
    var that = this;
    that.formVariables.setVisible(false);
    that.disableAcceptedValuesPanel();
    that.gridAcceptedValues.setVisible(false);
    that.disableBooleanPanel();
    $(that.gridVariables.dom.toolbar).find("input").val("");
    that.gridVariables.clearFilter();
    that.gridVariables.setVisible(true);
    that.windowVariables.setTitle('Variables'.translate());
};
PMVariables.prototype.showForm = function () {
    var that = this;
    that.edit = false;
    that.dirtyAcceptedValue = false;
    that.formVariables.setVisible(true);
    that.enableAcceptedValuesPanel();
    that.gridAcceptedValues.setVisible(true);
    that.enableBooleanPanel();
    that.gridVariables.setVisible(false);
    that.windowVariables.setTitle('Create Variable'.translate());
    that.formVariables.reset();
    that.formVariables.setFocus();
    that.changeViewFieldType('string');
    that.resetAcceptedValuesPanel();
    that.gridAcceptedValues.clearItems();
    that.resetBooleanPanel();
    that.buttonFieldCancel.setVisible(false);
    that.windowVariables.showFooter();
    that.buttonCreateInputDocument.setVisible(false);
    that.buttonEditInputDocument.setVisible(false);
};
PMVariables.prototype.showFormEdit = function (data) {
    var that = this;
    that.showForm();
    that.var_uid = data.var_uid;
    that.edit = true;
    that.var_name = data.var_name;
    that.windowVariables.setTitle('Edit Variable'.translate());
    that.formVariables.getField('var_dbconnection').setValue(data.var_dbconnection);
    that.formVariables.getField('var_field_type').setValue(data.var_field_type);
    that.formVariables.getField('var_label').setValue(data.var_label);
    that.formVariables.getField('var_name').setValue(data.var_name);
    that.formVariables.getField('var_sql').setValue(data.var_sql);
    that.formVariables.getField('inp_doc_uid').setValue(data.inp_doc_uid);

    that.setVarOptionsControl(data.var_accepted_values);
    that.changeViewFieldType(data.var_field_type);
    that.setDataAcceptedValues(data.var_accepted_values);
    that.currentVariable = data;
    that.currentVariable.var_name_old = that.currentVariable.var_name;
};
PMVariables.prototype.loadDataBaseConnections = function () {
    var that = this;
    var restProxy = new PMRestClient({
        endpoint: 'database-connections',
        typeRequest: 'get',
        functionSuccess: function (xhr, response) {
            var dropdown = that.formVariables.getField('var_dbconnection');
            for (var i = 0; i < response.length; i++) {
                if (response[i].dbs_connection_type == "TNS") {
                    dropdown.addOption({
                        label: "[" + response[i].dbs_tns + "] " + response[i].dbs_type + " : " + response[i].dbs_database_description,
                        value: response[i].dbs_uid
                    });
                } else {
                    dropdown.addOption({
                        label: "[" + response[i].dbs_server + ":" + response[i].dbs_port + "] " + response[i].dbs_type + ": " + response[i].dbs_database_name + response[i].dbs_database_description,
                        value: response[i].dbs_uid
                    });
                }
            }
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restProxy.executeRestClient();
};
PMVariables.prototype.changeViewFieldType = function (newValue) {
    var that = this;
    var sw = that.formVariables.getField('var_options_control').controls[0].selected;

    that.formVariables.getField('var_dbconnection').setVisible(false);
    that.formVariables.getField('var_sql').setVisible(false);
    that.formVariables.getField('var_options_control').setVisible(false);
    that.formVariables.getField('inp_doc_uid').setVisible(false);
    that.formVariables.getField('inp_doc_uid').setRequired(false);
    that.disableAcceptedValuesPanel();
    that.gridAcceptedValues.setVisible(false);
    that.disableBooleanPanel();
    that.buttonCreateInputDocument.setVisible(false);
    that.buttonEditInputDocument.setVisible(false);

    validateKeysField(that.formAcceptedValues.getField('keyValue').getControls()[0].getHTML(), []);

    if (this.fieldInfo === null) {
        this.fieldInfo = document.createTextNode('');
        var a = that.formVariables.getField('var_field_type').getControls()[0];
        a.html.parentNode.appendChild(document.createElement('br'));
        a.html.parentNode.appendChild(this.fieldInfo);
    }

    switch (newValue) {
        case 'string':
            that.formVariables.getField('var_dbconnection').setVisible(true);
            that.formVariables.getField('var_sql').setVisible(true);
            that.formVariables.getField('var_options_control').setVisible(true);
            sw ? that.enableAcceptedValuesPanel() : that.disableAcceptedValuesPanel();
            that.gridAcceptedValues.setVisible(true && sw);
            this.fieldInfo.data = "Supported Controls: text, textarea, dropdown, radio, suggest, hidden.".translate();

            /*----------------------------------********---------------------------------*/

            break;
        case 'integer':
            that.formVariables.getField('var_dbconnection').setVisible(true);
            that.formVariables.getField('var_sql').setVisible(true);
            that.formVariables.getField('var_options_control').setVisible(true);
            sw ? that.enableAcceptedValuesPanel() : that.disableAcceptedValuesPanel();
            that.gridAcceptedValues.setVisible(true && sw);

            validateKeysField(that.formAcceptedValues.getField('keyValue').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'ishyphen']);
            this.fieldInfo.data = "Supported Controls: text, textarea, dropdown, radio, suggest, hidden.".translate();
            break;
        case 'float':
            that.formVariables.getField('var_dbconnection').setVisible(true);
            that.formVariables.getField('var_sql').setVisible(true);
            that.formVariables.getField('var_options_control').setVisible(true);
            sw ? that.enableAcceptedValuesPanel() : that.disableAcceptedValuesPanel();
            that.gridAcceptedValues.setVisible(true && sw);

            validateKeysField(that.formAcceptedValues.getField('keyValue').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'isperiod', 'ishyphen']);
            this.fieldInfo.data = "Supported Controls: text, textarea, dropdown, radio, suggest, hidden.".translate();
            break;
        case 'boolean':
            that.enableBooleanPanel();
            this.fieldInfo.data = "Supported Controls: checkbox, radio, hidden.".translate();
            break;
        case 'datetime':
            this.fieldInfo.data = "Supported Controls: datetime, hidden.".translate();
            break;
        case "grid":
            this.fieldInfo.data = "Supported Controls: grid.".translate();
            break;
        case "array":
            that.formVariables.getField('var_dbconnection').setVisible(true);
            that.formVariables.getField('var_sql').setVisible(true);
            that.formVariables.getField('var_options_control').setVisible(true);
            sw ? that.enableAcceptedValuesPanel() : that.disableAcceptedValuesPanel();
            that.gridAcceptedValues.setVisible(true && sw);
            this.fieldInfo.data = "Supported Controls: checkgroup.".translate();
            break;
        case 'file':
            that.formVariables.getField('inp_doc_uid').setVisible(true);
            that.buttonCreateInputDocument.setVisible(true);
            that.buttonEditInputDocument.setVisible(true);
            that.formVariables.getField('inp_doc_uid').setRequired(true);
            this.fieldInfo.data = "Supported Controls: file.".translate();
            that.validateInputDoc();
            break;
        case 'multiplefile':
            this.fieldInfo.data = "Supported Controls: Multiple File.".translate();
            break;
        case 'object':
            this.fieldInfo.data = "Supported Controls:".translate();
            break;
    }
};
/**
 * Enable or disable edit button of input document
 */
PMVariables.prototype.validateInputDoc = function () {
    var form = this.formVariables,
        fieldInpDoc = form.getField("inp_doc_uid"),
        defaultText = "- Select an input document -".translate();
    if (fieldInpDoc && fieldInpDoc.getValue() && fieldInpDoc.getValue() !== defaultText) {
        this.buttonEditInputDocument.enable();
    } else {
        this.buttonEditInputDocument.disable();
    }
};
PMVariables.prototype.addAcceptedValue = function () {
    var that = this,
        key = jQuery.trim(that.formAcceptedValues.getField('keyValue').getValue()),
        value = jQuery.trim(that.formAcceptedValues.getField('value').getValue());

    if (that.isAcceptedValueAdded()) {
        return PMDesigner.msgFlash('The key value already exists.'.translate(),
            document.getElementById('windowVariables'), 'error', 1000, 5);
    }
    if (!(key && value)) {
        return PMDesigner.msgFlash('The key and label must be supplied.'.translate(),
            document.getElementById('windowVariables'), 'error', 1000, 5);
    }
    if (that.editRow === null) {
        that.gridAcceptedValues.addItem(new PMUI.grid.GridPanelRow({
            data: {
                keyValue: key,
                value: value
            }
        }));
    } else {
        this.editingOptions = false;
        that.editRow.setData({
            keyValue: key,
            value: value
        });
    }
    that.dirtyAcceptedValue = true;
    that.cancelAcceptedValue();
};
PMVariables.prototype.editAcceptedValue = function (row) {
    var that = this;
    this.editingOptions = true;
    that.editRow = row;
    var data = row.getData();
    that.formAcceptedValues.getField('keyValue').setValue(data.keyValue);
    that.formAcceptedValues.getField('value').setValue(data.value);
    that.buttonFieldAdd.setValue('Save'.translate());
    that.buttonFieldCancel.setVisible(true);

    that.initialFormAcceptedValuesKeyValue = data.keyValue;
    that.initialFormAcceptedValuesValue = data.value;
};
PMVariables.prototype.deleteAcceptedValue = function (row) {
    var that = this;
    var confirmWindow = new PMUI.ui.MessageWindow({
        id: 'confirmWindowDeleteAcceptedValue',
        windowMessageType: 'warning',
        width: 490,
        bodyHeight: 'auto',
        title: "Variables".translate(),
        message: 'Do you want to delete this Key Value?'.translate(),
        footerItems: [{
            text: 'No'.translate(),
            visible: true,
            handler: function () {
                confirmWindow.close();
            },
            buttonType: "error"
        }, {
            text: 'Yes'.translate(),
            visible: true,
            handler: function () {
                confirmWindow.close();
                that.gridAcceptedValues.removeItem(row);
                that.dirtyAcceptedValue = true;
            },
            buttonType: "success"
        }
        ]
    });
    confirmWindow.open();
    confirmWindow.dom.titleContainer.style.height = '17px';
    confirmWindow.showFooter();
};
PMVariables.prototype.getDataAcceptedValues = function () {
    var that = this, data = [], i, dt = [];
    if (that.gridAcceptedValues.visible) {
        dt = that.gridAcceptedValues.getData();
        for (i = 0; i < dt.length; i++) {
            delete dt[i].key;
            delete dt[i].type;
            data.push({
                value: dt[i].keyValue,
                label: dt[i].value
            });
        }
    }
    if (that.formBooleanOptions.visible) {
        var a = that.formBooleanOptions.getItems("fields").reduce(function (prev, curr) {
                prev[curr.getName()] = curr.getValue();
                return prev;
            }, {});

        data = [
            {value: '1', label: a.trueOption},
            {value: '0', label: a.falseOption}
        ];
    }
    return data;
};
PMVariables.prototype.setDataAcceptedValues = function (stringJsonData) {
    if (!stringJsonData) {
        return;
    }
    var that = this, i, data = stringJsonData;
    if (typeof stringJsonData === 'string') {
        data = JSON.parse(stringJsonData);
    }
    if (that.gridAcceptedValues.visible) {
        for (i = 0; i < data.length; i++) {
            that.gridAcceptedValues.addItem(new PMUI.grid.GridPanelRow({
                data: {
                    keyValue: data[i].keyValue ? data[i].keyValue : data[i].value,
                    value: data[i].keyValue ? data[i].value : data[i].label
                }
            }));
            that.initialGridAcceptedValuesOrder.push(data[i].keyValue ? data[i].keyValue : data[i].value);
        }
    }
    if (that.formBooleanOptions.visible) {
        that.formBooleanOptions.getField('trueOption').setValue(data[0].label);
        that.formBooleanOptions.getField('falseOption').setValue(data[1].label);
    }
};
PMVariables.prototype.validateAcceptedValues = function () {
    var that = this,
        dirty = false,
        finalVal1,
        finalVal2,
        initVal1,
        initVal2,
        gridAcceptedValues,
        finalGridAcceptedValuesOrder = [],
        key;
    if (that.initialFormAcceptedValuesValue && that.formAcceptedValues.getField('value').getValue() != '') {
        finalVal1 = that.formAcceptedValues.getField('value').getValue();
        finalVal2 = that.formAcceptedValues.getField('keyValue').getValue();
        initVal1 = that.initialFormAcceptedValuesValue;
        initVal2 = that.initialFormAcceptedValuesKeyValue;
        if (finalVal1 !== initVal1 || finalVal2 !== initVal2) {
            dirty = true;
        }
    } else if (that.formAcceptedValues.getField('value').getValue() !== '' || that.formAcceptedValues.getField('keyValue').getValue()) {
        dirty = true;
    }

    if (that.initialGridAcceptedValuesOrder.length) {
        gridAcceptedValues = that.gridAcceptedValues.getData();
        for (key in gridAcceptedValues) {
            if (gridAcceptedValues.hasOwnProperty(key)) {
                finalGridAcceptedValuesOrder.push(gridAcceptedValues[key].keyValue);
            }
        }
        if (JSON.stringify(finalGridAcceptedValuesOrder) !== JSON.stringify(that.initialGridAcceptedValuesOrder)) {
            dirty = true;
        }
    }
    this.validateAcceptedValuesFlag = dirty;
};
PMVariables.prototype.setVarOptionsControl = function (stringJsonData) {
    if (!stringJsonData) {
        return;
    }
    var that = this, i, data = stringJsonData;
    if (typeof stringJsonData === 'string') {
        data = JSON.parse(stringJsonData);
    }
    if (data.length > 0) {
        that.formVariables.getField('var_options_control').setValue("['1']");
    }
};
PMVariables.prototype.cancelAcceptedValue = function () {
    var that = this;
    that.editRow = null;
    that.buttonFieldAdd.setValue('Add'.translate());
    that.buttonFieldCancel.setVisible(false);
    that.resetAcceptedValuesPanel();
};
PMVariables.prototype.isAcceptedValueAdded = function () {
    var that = this, i, keyValue, data, exist, i, index, rowEditValue;
    data = that.gridAcceptedValues.getData();
    keyValue = that.formAcceptedValues.getField('keyValue').getValue() || "";
    if (this.editingOptions) {
        rowEditValue = this.editRow.getData()["keyValue"];
        for (i = 0; i < data.length; i++) {
            if (rowEditValue === data[i].keyValue) {
                index = i;
                break;
            }
        }
        for (i = 0; i < data.length; i++) {
            if (i !== index) {
                if (keyValue === data[i].keyValue) {
                    exist = true;
                }
            }
        }
        if (exist) {
            return true;
        } else {
            return false;
        }
    } else {
        for (i = 0; i < data.length; i++) {
            if (data[i].keyValue === keyValue) {
                return true;
            }
        }
    }
    return false;
};
PMVariables.prototype.setInputDocuments = function (inp_doc_uid) {
    var restClient = new PMRestClient({
        endpoint: 'input-documents',
        typeRequest: 'get',
        functionSuccess: function (xhr, response) {
            inputDocumentsData = response;
            var arrayOptions = [];
            arrayOptions[0] = {
                label: "- Select an input document -".translate(),
                value: "",
                disabled: true,
                selected: true
            };
            for (var i = 0; i <= inputDocumentsData.length - 1; i++) {
                arrayOptions.push(
                    {
                        value: inputDocumentsData[i].inp_doc_uid,
                        label: inputDocumentsData[i].inp_doc_title
                    }
                );
            }
            inp_doc_uid.setOptions(arrayOptions);
            inp_doc_uid.setValue(arrayOptions[0].value);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
    });
    restClient.executeRestClient();
};
PMVariables.prototype.setInputDocumentsFromIDModule = function (inp_doc_uid, response) {
    var inp_doc_uid_val = $("#inp_doc_uid").find("select:eq(0) option:selected").val();
    var arrayOptions = [];
    inputDocumentsData = response;
    arrayOptions[0] = {
        label: "- Select an input document -".translate(),
        value: "",
        disabled: true,
        selected: true
    };
    for (var i = 0; i <= inputDocumentsData.length - 1; i++) {
        arrayOptions.push(
            {
                value: inputDocumentsData[i].inp_doc_uid,
                label: inputDocumentsData[i].inp_doc_title
            }
        );
    }
    inp_doc_uid.setOptions(arrayOptions);
    inp_doc_uid.setValue(arrayOptions[0].value);
    if (inp_doc_uid_val) {
        inp_doc_uid.setValue(inp_doc_uid_val);
    }
};
PMVariables.prototype.isWindowActive = function () {
    if ($("#formVariables").is(":visible")) {
        return true;
    }
    return false;
};
/**
 * Reset the fields from the form's boolean panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.resetBooleanPanel = function () {
    if (this.formBooleanOptions) {
        this.formBooleanOptions.getItems("fields").forEach(function (i) {
            i.setValue("");
        });
    }
    return this;
};
/**
 * Disable the fields from the form's boolean panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.disableBooleanPanel = function () {
    if (this.formBooleanOptions) {
        this.formBooleanOptions.setVisible(false)
            .getItems("fields").forEach(function (i) {
            i.disable();
        });
    }
    return this;
};
/**
 * Enable the fields from the form's boolean panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.enableBooleanPanel = function () {
    if (this.formBooleanOptions) {
        this.formBooleanOptions.setVisible(true)
            .getItems("fields").forEach(function (i) {
            i.enable();
        });
    }
    return this;
};
/**
 * Reset the fields from the form's Accepted Values panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.resetAcceptedValuesPanel = function () {
    if (this.formAcceptedValues) {
        this.formAcceptedValues.getItems().forEach(function (i) {
            i.setValue("");
        });
    }
    return this;
};
/**
 * Enable the fields from the form's Accepted Values panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.enableAcceptedValuesPanel = function () {
    if (this.formAcceptedValues) {
        this.formAcceptedValues.setVisible(true)
            .getItems('fields').forEach(function (i) {
            i.enable("");
        });
    }
    return this;
};
/**
 * Disable the fields from the form's Accepted Values panel.
 * @returns {PMVariables}
 */
PMVariables.prototype.disableAcceptedValuesPanel = function () {
    if (this.formAcceptedValues) {
        this.formAcceptedValues.setVisible(false)
            .getItems('fields').forEach(function (i) {
            i.disable("");
        });
    }
    return this;
};
PMDesigner.variables = function () {
    var pmvariables = new PMVariables();
    pmvariables.load();
};
PMDesigner.variables.create = function () {
    var pmvariables = new PMVariables();
    pmvariables.showForm();
    pmvariables.load();
};
