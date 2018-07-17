var PMMessageType = function (options) {
    var that = this;
    this.msgt_uid = null;
    this.edit = false;
    this.editRow = null;
    this.dirtyAcceptedValue = false;
    this.fieldInfo = null;
    this.onSave = new Function();
    this.onEdit = new Function();
    this.requiredMessage = null;
    this.clickedClose = true;
    this.previousMessageValue = null;
    this.buttonCreate = new PMUI.ui.Button({
        id: 'buttonCreate',
        text: "Create".translate(),
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

    this.buttonFieldAdd = new PMUI.field.ButtonField({
        id: 'buttonFieldAdd',
        pmType: 'buttonField',
        value: 'Create'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        controlsWidth: 50,
        proportion: 0.6,
        handler: function (field) {
            that.addAcceptedValue();
        },
        buttonType: "success",
        style: {
            cssProperties: {
                'vertical-align': 'top',
                'padding-top': '37px',
                'padding-right': '1px',
                'padding-bottom': '1px',
                'padding-left': '1px'
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
        controlsWidth: 50,
        proportion: 0.6,
        handler: function (field) {
            that.cancelAcceptedValue();
        },
        style: {
            cssProperties: {
                'vertical-align': 'top',
                'padding-top': '37px',
                'padding-right': '1px',
                'padding-bottom': '1px',
                'padding-left': '1px'
            }
        }
    });
    this.buttonFieldCancel.getControl().button.setButtonType("error");

    this.frmMessageType = new PMUI.form.Form({
        id: 'frmMessageType',
        width: 'auto',
        title: '',
        visibleHeader: false,
        items: [
            {
                id: 'txtMessageTypeName',
                name: 'txtMessageTypeName',
                pmType: 'text',
                label: "Name".translate(),
                placeholder: "Name".translate(),
                value: '',
                required: true,
                valueType: 'string',
                maxLength: 60,
                controlsWidth: 460
            },
            {
                id: "frmAcceptedValues",
                pmType: 'panel',
                legend: "Message Field".translate(),
                fieldset: true,
                layout: 'hbox',
                items: [
                    {
                        pmType: "text",
                        name: "txtMessageTypeVariableName",
                        label: "Message Field Name".translate(),
                        labelWidth: "100%",
                        controlsWidth: "400px",
                        proportion: 2.5,
                        valueType: "string",
                        maxLength: 255,
                        required: true,
                        labelPosition: "top",
                        validators: [
                            {
                                pmType: "regexp",
                                criteria: /^[a-zA-Z_]+[0-9a-zA-Z_]+$/,
                                errorMessage: "A valid variable starts with a letter or underscore, followed by any number of letters, numbers, or underscores.".translate()
                            }
                        ]
                    },
                    that.buttonFieldCancel,
                    that.buttonFieldAdd
                ]
            }
        ]
    });

    this.frmMessageType.getData = function () {
        var data = getData2PMUI(that.frmMessageType.html);
        return data;
    };

    this.gridMessages = new PMUI.grid.GridPanel({
        id: 'gridMessages',
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
            id: "msgtName",
            title: "Name".translate(),
            dataType: "string",
            columnData: "msgt_name",
            alignmentCell: "left",
            width: "330px",
            sortable: true
        }, {
            id: 'msgtEdit',
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
            id: 'msgtDelete',
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
        height: 240,
        style: {cssClasses: ['mafe-gridPanel']},
        filterPlaceholder: 'Search ...'.translate(),
        emptyMessage: 'No records found'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        filterable: false,
        tableContainerHeight: 200,
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return '';
        },
        columns: [
            {
                id: "msgtv_name",
                title: "Message Field Name".translate(),
                columnData: "msgtv_name",
                dataType: "string",
                alignmentCell: "left",
                width: 180,
                sortable: true
            },
            {
                id: "btnEdit",
                dataType: "button",
                title: "",
                buttonLabel: "Edit".translate(),
                width: 60,
                buttonStyle: {cssClasses: ["mafe-button-edit"]},

                onButtonClick: function (row, grid) {
                    that.editAcceptedValue(row);
                }
            },
            {
                id: "btnDelete",
                dataType: "button",
                title: "",
                buttonLabel: function (row, data) {
                    return "Delete".translate();
                },
                width: 75,
                buttonStyle: {cssClasses: ["mafe-button-delete"]},
                onButtonClick: function (row, grid) {
                    that.deleteAcceptedValue(row);
                }
            }
        ],
        dataItems: null
    });

    this.isDirtyFrmMessageType = function () {
        $("input,select,textarea").blur();
        if (this.frmMessageType.isVisible()) {
            if (this.frmMessageType.isDirty() || this.dirtyAcceptedValue) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: 'messageWindowCancel',
                    width: 490,
                    title: "Message Types".translate(),
                    windowMessageType: 'warning',
                    bodyHeight: 'auto',
                    message: 'Are you sure you want to discard your changes?'.translate(),
                    footerItems: [
                        {
                            id: 'messageWindowNo',
                            text: 'No'.translate(),
                            handler: function () {
                                message_window.close();
                            },
                            buttonType: "error"
                        },
                        {
                            id: 'messageWindowYes',
                            text: 'Yes'.translate(),
                            handler: function () {
                                that.requiredMessage.hide();
                                if (that.clickedClose) {
                                    that.winMessageType.close();
                                }
                                message_window.close();
                                that.showGrid();
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                if (this.clickedClose) {
                    this.winMessageType.close();
                } else {
                    this.showGrid();
                    this.requiredMessage.hide();
                }
            }
        } else {
            this.winMessageType.close();
        }
    };
    this.winMessageType = new PMUI.ui.Window({
        id: 'winMessageType',
        title: '',
        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH,
        buttonsPosition: 'right',
        onBeforeClose: function () {
            that.clickedClose = true;
            that.isDirtyFrmMessageType();
        },
        footerItems: [
            new PMUI.ui.Button({
                id: 'winMessageTypeCancel',
                text: 'Cancel'.translate(),
                handler: function () {
                    that.clickedClose = false;
                    that.isDirtyFrmMessageType();
                },
                buttonType: 'error'
            }),
            new PMUI.ui.Button({
                id: 'winMessageTypeSave',
                text: "Save".translate(),
                handler: function () {
                    that.frmAcceptedValues.getItems("fields").forEach(function (i) {
                        i.disable();
                    });
                    if (that.edit) {
                        that.updateMessageType();
                    } else {
                        that.createMessageType();
                    }
                    that.frmAcceptedValues.getItems("fields").forEach(function (i) {
                        i.enable();
                    });
                },
                buttonType: 'success'
            })
        ],
        buttonPanelPosition: 'bottom'
    });
    PMMessageType.prototype.init.call(this);
};

PMMessageType.prototype.init = function () {
    var that = this;

    that.buttonCreate.defineEvents();

    that.winMessageType.open();
    that.winMessageType.addItem(that.gridMessages);
    that.winMessageType.addItem(that.frmMessageType);

    that.winMessageType.addItem(that.gridAcceptedValues);
    that.winMessageType.hideFooter();
    that.requiredMessage = $(document.getElementById("requiredMessage"));

    this.buttonFieldAdd.controls[0].button.setStyle({cssProperties: {padding: "6px 15px"}});
    this.buttonFieldCancel.controls[0].button.setStyle({cssProperties: {padding: "6px 15px"}});
    that.requiredMessage.css({float: "none"});
    that.winMessageType.footer.html.style.textAlign = 'right';

    that.frmAcceptedValues = PMUI.getPMUIObject($('#frmAcceptedValues').css({'width': '690px'}).get(0));
    $('#gridMessages .pmui-textcontrol').css({'margin-top': '5px', width: '250px'});

    //$(that.frmAcceptedValues.getHTML()).find("#requiredMessage").empty();
    that.requiredMessage.hide();
    //that.winMessageType.body.appendChild(that.requiredMessage[0]);
    that.gridMessages.dom.toolbar.appendChild(that.buttonCreate.getHTML());
    that.showGrid();

    validateKeysField(that.frmMessageType.getField('txtMessageTypeName').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'isletter', 'isunderscore']);
    that.resetFrmAcceptedValues();
};

PMMessageType.prototype.createMessageType = function () {
    var that = this,
        data;

    if (!this.frmMessageType.isValid()) {
        return;
    }
    if (that.gridAcceptedValues.getData().length == 0) {
        PMDesigner.msgFlash('Add at least one variable.'.translate(), winMessageType, 'error', 3000, 5);
        return;
    }

    data = this.frmMessageType.getData();
    data.msgt_name = data.txtMessageTypeName;
    data.msgt_variables = that.getDataAcceptedValues();

    // This returned data is not necessary at sendind, so it is deleted
    delete data.txtMessageTypeVariableName;
    delete data.buttonFieldCancel;
    delete data.buttonFieldAdd;

    (new PMRestClient({
        endpoint: 'message-type',
        typeRequest: 'post',
        functionSuccess: function (xhr, response) {
            if (that.onSave(xhr, response) === false) {
                that.msgt_uid = null;
                return;
            }
            that.showGrid();
            that.load();
            that.msgt_uid = null;
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },

        messageError: ' ',
        data: data,
        messageSuccess: "Message Type Saved successfully.".translate(),
        flashContainer: that.panel

    })).executeRestClient();
};

PMMessageType.prototype.updateMessageType = function () {
    var that = this,
        data;
    if (!this.frmMessageType.isValid()) {
        return;
    }
    if (that.gridAcceptedValues.getData().length == 0) {
        if (!this.frmAcceptedValues.isValid()) {
            return;
        } else {
            PMDesigner.msgFlash('Add at least one variable.'.translate(), winMessageType, 'error', 3000, 5);
            return;
        }
    }

    data = this.frmMessageType.getData();
    data.msgt_name = data.txtMessageTypeName;
    data.msgt_variables = that.getDataAcceptedValues();

    (new PMRestClient({
        endpoint: "message-type/" + that.msgt_uid,
        typeRequest: "update",
        data: data,

        functionSuccess: function (xhr, response) {
            if (that.onEdit(xhr, response, data) === false) {
                that.msgt_uid = null;
                return;
            }

            that.showGrid();
            that.load();
            that.msgt_uid = null;
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },

        messageError: ' ',
        messageSuccess: "Message Type edited successfully.".translate(),
        flashContainer: that.panel
    })).executeRestClient();
};

PMMessageType.prototype.deleteMessage = function (msgt_uid) {
    var that = this;

    (new PMRestClient({
        endpoint: 'message-type/' + msgt_uid,
        typeRequest: 'remove',

        functionSuccess: function (xhr, response) {
            that.load();
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },

        messageError: 'Working...'.translate(),
        messageSuccess: 'Message Type Deleted successfully'.translate(),
        flashContainer: that.panel
    })).executeRestClient();
};

PMMessageType.prototype.load = function () {
    var that = this, restProxy;

    restProxy = new PMRestClient({
        endpoint: 'message-types',
        typeRequest: 'get',

        functionSuccess: function (xhr, response) {
            that.gridMessages.setDataItems(response);
            that.gridMessages.sort('msgt_name', 'asc');

            $(that.gridMessages.dom.toolbar).find("input").val("");
            that.gridMessages.clearFilter();
            document.getElementById("requiredMessage").style.marginTop = "16px";
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });

    restProxy.executeRestClient();
};

PMMessageType.prototype.del = function (data, row, grid) {
    var that = this,
        confirmWindow = new PMUI.ui.MessageWindow({
            id: 'confirmWindowDel',
            windowMessageType: 'warning',
            width: 490,
            bodyHeight: 'auto',
            title: "Message Types".translate(),
            message: "Do you want to delete this Message Type?".translate(),
            footerItems: [
                {
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
                        that.deleteMessage(data.msgt_uid, row);
                    },
                    buttonType: "success"
                }
            ]
        });

    confirmWindow.open();
    confirmWindow.dom.titleContainer.style.height = '17px';
    confirmWindow.showFooter();
};

PMMessageType.prototype.showGrid = function () {
    var that = this;
    that.frmMessageType.setVisible(false);
    that.frmAcceptedValues.setVisible(false);
    that.gridAcceptedValues.setVisible(false);
    $(that.gridMessages.dom.toolbar).find("input").val("");
    that.gridMessages.clearFilter();
    that.gridMessages.setVisible(true);
    that.winMessageType.setTitle("Message Types".translate());
    that.winMessageType.hideFooter();
};

PMMessageType.prototype.showForm = function () {
    var that = this;
    this.requiredMessage.show();
    that.edit = false;
    that.dirtyAcceptedValue = false;
    that.frmMessageType.setVisible(true);
    that.frmAcceptedValues.setVisible(true);
    that.gridAcceptedValues.setVisible(true);
    that.gridMessages.setVisible(false);
    that.winMessageType.setTitle("Create Message Type".translate());
    that.winMessageType.showFooter();
    that.frmMessageType.reset();
    that.frmMessageType.setFocus();
    that.changeViewFieldType();
    that.resetFrmAcceptedValues();
    that.gridAcceptedValues.clearItems();
    that.buttonFieldCancel.setVisible(false);
};

PMMessageType.prototype.showFormEdit = function (data) {
    var that = this;
    that.showForm();
    that.msgt_uid = data.msgt_uid;
    that.edit = true;

    that.winMessageType.setTitle('Edit Message Type'.translate());
    that.frmMessageType.getField('txtMessageTypeName').setValue(data.msgt_name);

    that.setDataAcceptedValues(data.msgt_variables);
    document.getElementById("requiredMessage").style.marginTop = "16px";
};

PMMessageType.prototype.changeViewFieldType = function () {
    var that = this, sw = true;

    that.frmAcceptedValues.setVisible(true);
    that.gridAcceptedValues.setVisible(true);

    validateKeysField(that.frmAcceptedValues.getField('txtMessageTypeVariableName').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'isletter', 'isunderscore']);

    if (this.fieldInfo === null) {
        this.fieldInfo = document.createTextNode('');
    }
};

PMMessageType.prototype.addAcceptedValue = function () {
    var that = this,
        value = $.trim(that.frmAcceptedValues.getField('txtMessageTypeVariableName').getValue()),
        message;
        
    // if the form (form field's RegEx) is invalid, add a Message Field will not be allowed.
    if (!that.frmAcceptedValues.isValid()) {
        return;
     }

    if (that.previousMessageValue !== value && that.isAcceptedValueAdded(value)) {
        message = new PMUI.ui.FlashMessage({
            message: "The variable Name already exists.".translate(),
            duration: 3000,
            severity: 'error',
            appendTo: that.winMessageType.footer
        });
        message.show();
        return;
    } else if (!value) {
        message = new PMUI.ui.FlashMessage({
            message: "Please, specify a name for the Message Field.".translate(),
            duration: 3000,
            severity: 'error',
            appendTo: that.winMessageType.footer
        });
        return message.show();
    }
    that.previousMessageValue = null;
    if (that.editRow === null) {
        that.gridAcceptedValues.addItem(new PMUI.grid.GridPanelRow({
            data: {
                msgtv_name: value
            }
        }));
    } else {
        that.editRow.setData({
            msgtv_name: value
        });
    }

    that.dirtyAcceptedValue = true;
    that.cancelAcceptedValue();
};

PMMessageType.prototype.editAcceptedValue = function (row) {
    var that = this, data;
    that.editRow = row;
    data = row.getData();

    that.previousMessageValue = data.msgtv_name;
    that.frmAcceptedValues.getField('txtMessageTypeVariableName').setValue(data.msgtv_name);
    that.buttonFieldAdd.setValue('Save'.translate());
    that.buttonFieldCancel.setVisible(true);
};

PMMessageType.prototype.deleteAcceptedValue = function (row) {
    var that = this,
        confirmWindow = new PMUI.ui.MessageWindow({
            id: 'confirmWindowDeleteAcceptedValue',
            windowMessageType: 'warning',
            width: 490,
            bodyHeight: 'auto',
            title: "Message Type".translate(),
            message: "Are you sure you want to delete the \"variable\"?".translate(),
            footerItems: [
                {
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

PMMessageType.prototype.getDataAcceptedValues = function () {
    var that = this, data = [], i;

    if (that.gridAcceptedValues.visible) {
        data = that.gridAcceptedValues.getData();

        for (i = 0; i < data.length; i += 1) {
            delete data[i].key;
            delete data[i].type;
        }
    }
    return data;
};

PMMessageType.prototype.setDataAcceptedValues = function (stringJsonData) {
    var that = this, i, data;
    if (!stringJsonData) {
        return;
    }

    data = stringJsonData;
    if (typeof stringJsonData === 'string') {
        data = JSON.parse(stringJsonData);
    }

    if (that.gridAcceptedValues.visible) {
        for (i = 0; i < data.length; i += 1) {
            that.gridAcceptedValues.addItem(new PMUI.grid.GridPanelRow({
                data: {
                    msgtv_name: data[i].msgtv_name,
                    msgtv_default_value: data[i].msgtv_default_value
                }
            }));
        }
    }
};

PMMessageType.prototype.cancelAcceptedValue = function () {
    var that = this;
    that.editRow = null;
    that.buttonFieldAdd.setValue("Create".translate());
    that.buttonFieldCancel.setVisible(false);
    that.resetFrmAcceptedValues();
};
/**
 * Validate if the value is present in the data collection.
 * @param value
 * @returns {boolean}
 */
PMMessageType.prototype.isAcceptedValueAdded = function (value) {
    var that = this, i,
        data = that.gridAcceptedValues.getData();

    for (i = 0; i < data.length; i += 1) {
        if (data[i].msgtv_name === value) {
            return true;
        }
    }
    return false;
};
/**
 * Resets the fields from the form's panel for accepted values.
 */
PMMessageType.prototype.resetFrmAcceptedValues = function () {
    this.frmAcceptedValues.getItems('fields').forEach(function (i) {
        i.setValue("");
    });
};

PMDesigner.messageType = function () {
    var pmvariables = new PMMessageType();
    pmvariables.load();
};

PMDesigner.messageType.create = function () {
    var pmvariables = new PMMessageType();
    pmvariables.showForm();
    pmvariables.load();
};
