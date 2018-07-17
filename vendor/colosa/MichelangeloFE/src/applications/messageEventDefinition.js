var MessageEventDefinition = function (bpmnEvent) {
    var that = this,
        msgNameField,
        variableSelector;

    this.bpmnEvent = bpmnEvent;
    this.arrayMessageType = [];
    this.messageEventDefinitionOption = "";
    this.messageEventDefinitionUid = "";
    this.editRow = null;
    this.eventType = bpmnEvent.evn_behavior;
    this.gridCurrent = null;
    this.editMessageForm = null;
    this.dirtyGrid = false;
    this.myTitle = "";

    variableSelector = new CriteriaField({
        id: "txtMessageTypeVariableDefaultValue",
        name: "txtMessageTypeVariableDefaultValue",
        valueType: "string",
        label: that.bpmnEvent.evn_marker == "MESSAGECATCH" ? "Store value in".translate() : "Get value from".translate(),
        maxLength: 200,
        labelWidth: "50%",
        controlsWidth: 300,
        proportion: 3.4,
        required: false
    });

    msgNameField = {
        pmType: "text",
        id: "txtMessageTypeVariableName",
        name: "txtMessageTypeVariableName",
        label: (that.bpmnEvent.evn_marker === "MESSAGECATCH" ? "Value" : "Name").translate(),
        labelWidth: "40%",
        controlsWidth: 200,
        proportion: 2.5,
        valueType: "string",
        maxLength: 255,
        readOnly: true,
        required: false
    };

    this.cboMessageType = new PMUI.field.DropDownListField({
        id: "cboMessageType",
        name: "cboMessageType",
        label: "Message Type".translate(),
        options: [],
        required: true,
        controlsWidth: 150,
        onChange: function (newValue, prevValue) {
            var messageTypeData = that.getMessageTypeByIndex(that.cboMessageType.getValue());
            if (messageTypeData != null) {
                that.gridCurrent.setDataItems(messageTypeData.msgt_variables);

                that._resetEditMessageForm();
            }
        }
    });

    this.isDirtyFormMessageEvent = function () {
        if (that.frmMessageEventDefinition1.isDirty() || that.dirtyGrid) {
            var message_window = new PMUI.ui.MessageWindow({
                id: "cancelMessageType",
                width: 490,
                title: that.myTitle.translate(),
                windowMessageType: "warning",
                bodyHeight: 'auto',
                message: 'Are you sure you want to discard your changes?'.translate(),
                footerItems: [
                    {
                        text: "No".translate(),
                        handler: function () {
                            message_window.close();
                        },
                        buttonType: "error"
                    },
                    {
                        text: "Yes".translate(),
                        handler: function () {
                            message_window.close();
                            that.winMessageEventDefinition.close();
                        },
                        buttonType: "success"
                    }
                ]
            });
            message_window.open();
            message_window.showFooter();
        } else {
            that.winMessageEventDefinition.close();
        }
    };

    this.txtCorrelationValue = new CriteriaField({
        id: "txtCorrelationValue",
        name: "txtCorrelationValue",
        valueType: "string",
        label: "Correlation Value".translate(),
        maxLength: 200,
        value: "",
        controlsWidth: 380
    });

    this.btnSaveVariable = new PMUI.field.ButtonField({
        id: "btnSaveVariable",
        pmType: "buttonField",
        value: 'Save'.translate(),
        labelVisible: false,
        buttonAlign: 'center',
        controlsWidth: 50,
        proportion: 0.8,
        handler: function (field) {
            that.addVariableInGrdPnlVariable3();
            that.dirtyGrid = true;
        },
        buttonType: "success"
    });

    this.btnCancelVariable = new PMUI.field.ButtonField({
        id: "btnCancelVariable",
        pmType: "buttonField",
        value: "Cancel".translate(),
        labelVisible: false,
        buttonAlign: 'center',
        controlsWidth: 55,
        proportion: 0.6,

        handler: function (field) {
            that.cancelAcceptedValue();
        },
        buttonType: "error"
    });

    this.frmMessageEventDefinition1 = new PMUI.form.Form({
        id: "frmMessageEventDefinition1",
        title: "",
        width: DEFAULT_WINDOW_WIDTH - 70,
        visibleHeader: false,
        items: [
            that.cboMessageType,
            that.txtCorrelationValue,
            {
                id: "edit-panel",
                pmType: "panel",
                legend: "Message content".translate(),
                fieldset: true,
                layout: "hbox",
                items: [
                    that.bpmnEvent.evn_marker === "MESSAGECATCH" ? variableSelector : msgNameField,
                    that.bpmnEvent.evn_marker === "MESSAGECATCH" ? msgNameField : variableSelector,
                    that.btnCancelVariable,
                    that.btnSaveVariable
                ]
            }
        ],
        style: {
            cssProperties: {
                marginBottom: '15px'
            }
        }
    });

    this.editMessageForm = this.frmMessageEventDefinition1.getItem("edit-panel");

    this.grdPnlVariable3 = new PMUI.grid.GridPanel({
        id: "grdPnlVariable3",
        pageSize: 5,
        style: {cssClasses: ["mafe-gridPanel"]},
        emptyMessage: "No records found".translate(),
        filterable: false,

        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return "";
        },
        columns: [
            {
                id: "msgtv_name",
                title: "Name".translate(),
                columnData: "msgtv_name",
                dataType: "string",
                alignmentCell: "left",
                width: 180
            },
            {
                id: "msgtv_default_value",
                title: "Get value from".translate(),
                columnData: "msgtv_default_value",
                dataType: "string",
                alignmentCell: "left",
                width: 300
            },
            {
                id: "btnEdit",
                dataType: "button",
                title: "",
                buttonLabel: "Edit".translate(),
                width: 60,
                buttonStyle: {cssClasses: ["mafe-button-edit"]},

                onButtonClick: function (row, grid) {
                    that.setValueMessageEventDefinition(row);
                }
            }
        ],

        dataItems: null
    });

    this.grdPnlReceive = new PMUI.grid.GridPanel({
        id: "grdPnlVariable3",
        pageSize: 5,
        style: {cssClasses: ["mafe-gridPanel"]},
        emptyMessage: "No records found".translate(),
        filterable: false,
        tableContainerHeight: 204,
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return "";
        },

        columns: [
            {
                id: "msgtv_default_value",
                title: "Store value in".translate(),
                columnData: "msgtv_default_value",
                dataType: "string",
                alignmentCell: "left",
                width: 200
            },
            {
                id: "msgtv_name",
                title: "Name Value".translate(),
                columnData: "msgtv_name",
                dataType: "string",
                alignmentCell: "left",
                width: 280
            },
            {
                id: "btnEdit",
                dataType: "button",
                title: "",
                buttonLabel: "Edit".translate(),
                width: 60,
                buttonStyle: {cssClasses: ["mafe-button-edit"]},

                onButtonClick: function (row, grid) {
                    that.setValueMessageEventDefinition(row);
                }
            }
        ],
        dataItems: null
    });

    this.gridCurrent = this.grdPnlVariable3;

    MessageEventDefinition.prototype.init.call(this);
};

MessageEventDefinition.prototype.init = function () {
    var that = this;

    that.createWindow();
    that.winMessageEventDefinition.addItem(that.frmMessageEventDefinition1);
    that.winMessageEventDefinition.addItem(that.gridCurrent);

    that.winMessageEventDefinition.open();
    this.editMessageForm.setVisible(false);
    this.applyStylesPost();

    that.load();
    document.getElementById("requiredMessage").style.marginTop = "15px";
};

MessageEventDefinition.prototype.applyStylesPost = function () {
    var that = this;
    that.btnSaveVariable.controls[0].addCSSClasses(["pmui pmui-button pmui-success"]);
    that.btnCancelVariable.controls[0].addCSSClasses(["pmui pmui-button pmui-error"]);
    that.btnSaveVariable.controls[0].html.style.padding = "5px";
    $(that.btnCancelVariable.controls[0].html).css({
        padding: "5px",
        width: "auto"
    });
};

MessageEventDefinition.prototype.createWindow = function () {
    var that = this;

    if (that.bpmnEvent.evn_marker === "MESSAGECATCH") {
        that.myTitle = (that.bpmnEvent.evn_type === "START" ? "Start Message Event" : "Intermediate Receive Message Event").translate();
        that.gridCurrent = this.grdPnlReceive;
    } else if (that.bpmnEvent.evn_marker === "MESSAGETHROW") {
        that.myTitle = (that.bpmnEvent.evn_type == "END" ? "End Message Event" : "Intermediate Send Message Event").translate();
        that.gridCurrent = this.grdPnlVariable3;
    }

    that.winMessageEventDefinition = new PMUI.ui.Window({
        id: "winMessageEventDefinition",
        title: that.myTitle.translate(),

        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH,
        modal: true,
        onBeforeClose: function () {
            that.isDirtyFormMessageEvent();
        },
        footerItems: [
            {
                pmType: "button",
                buttonType: "error",
                text: "Cancel".translate(),
                handler: function () {
                    that.isDirtyFormMessageEvent();
                }
            },
            {
                pmType: "button",
                buttonType: "success",
                text: "Save".translate(),

                handler: function () {
                    var correlationValueAux, data;
                    if (!that.frmMessageEventDefinition1.isValid()) {
                        return;
                    }
                    correlationValueAux = that.frmMessageEventDefinition1.getData();
                    data = {
                        evn_uid: that.bpmnEvent.evn_uid,
                        msgt_uid: that.cboMessageType.getValue(),
                        msged_variables: that.getVariablesByArray(that.grdPnlVariable3GetData()),
                        msged_correlation: correlationValueAux.txtCorrelationValue
                    };

                    switch (that.messageEventDefinitionOption) {
                        case "POST":
                            that.messageEventDefintionPostRestProxy(data);

                            that.gridCurrent.clearItems();
                            that.winMessageEventDefinition.close();
                            break;
                        case "PUT":
                            that.messageEventDefintionPutRestProxy(data, that.messageEventDefinitionUid);
                            that.gridCurrent.clearItems();
                            that.winMessageEventDefinition.close();
                            break;
                    }
                }
            }
        ],

        footerAlign: "right",
        visibleFooter: true,
        closable: true,
        buttonPanelPosition: "bottom"
    });
};

MessageEventDefinition.prototype.getMessageTypeByIndex = function (messageTypeUid) {
    var that = this,
        i,
        messageTypeData = null;

    if (that.arrayMessageType.length > 0) {
        for (i = 0; i <= that.arrayMessageType.length - 1; i += 1) {
            if (that.arrayMessageType[i].msgt_uid == messageTypeUid) {
                messageTypeData = that.arrayMessageType[i];
                break;
            }
        }
    }
    return messageTypeData;
};

MessageEventDefinition.prototype.getVariablesByArray = function (arrayVariables) {
    var variables = {}, i;

    for (i = 0; i <= arrayVariables.length - 1; i += 1) {
        variables[arrayVariables[i].msgtv_name] = arrayVariables[i].msgtv_default_value;
    }

    return variables;
};

MessageEventDefinition.prototype.getVariablesByObject = function (objectVariable) {
    var that = this,
        key, aux,
        arrayData = [];

    for (key in objectVariable) {
        aux = {};
        aux["msgtv_name"] = key;
        aux["msgtv_default_value"] = objectVariable[key];
        arrayData.push(aux);
    }
    return arrayData;
};

MessageEventDefinition.prototype.load = function () {
    var that = this, restProxy;

    that.messageEventDefinitionUid = "";
    that.messageEventDefinitionOption = "";

    restProxy = new PMRestClient({
        typeRequest: "get",
        endpoint: "message-event-definitions",

        functionSuccess: function (xhr, response) {
            var dataResponse = response,
                i,
                arrayMessageEventDefinition = dataResponse,
                arrayMessageEventDefinitionData = {};

            for (i = 0; i <= arrayMessageEventDefinition.length - 1; i += 1) {
                if (arrayMessageEventDefinition[i].evn_uid == that.bpmnEvent.evn_uid) {
                    that.messageEventDefinitionUid = arrayMessageEventDefinition[i].msged_uid
                    arrayMessageEventDefinitionData = arrayMessageEventDefinition[i];
                    break;
                }
            }

            that.cboMessageTypeSetOptionsRestProxy(that.cboMessageType, (typeof(arrayMessageEventDefinitionData.msgt_uid) != "undefined") ? arrayMessageEventDefinitionData.msgt_uid : "");

            if (that.messageEventDefinitionUid == "") {
                //POST
                that.messageEventDefinitionOption = "POST";

                that.editMessageForm.getField("txtMessageTypeVariableName").setValue("");
                that.editMessageForm.getField("txtMessageTypeVariableDefaultValue").setValue("");
            } else {
                //PUT
                that.messageEventDefinitionOption = "PUT";

                that.gridCurrent.setDataItems(that.getVariablesByObject(arrayMessageEventDefinitionData.msged_variables));
                that.frmMessageEventDefinition1.getField("txtCorrelationValue").setValue(arrayMessageEventDefinitionData.msged_correlation);
            }
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });

    restProxy.executeRestClient();
};

MessageEventDefinition.prototype.setValueMessageEventDefinition = function (row) {
    var that = this, data;

    that.editRow = row;
    data = row.getData();

    that.editMessageForm.getField("txtMessageTypeVariableName").setValue(data.msgtv_name);
    that.editMessageForm.getField("txtMessageTypeVariableDefaultValue").setValue(data.msgtv_default_value);

    that.editMessageForm.setVisible(true);
};

MessageEventDefinition.prototype.addVariableInGrdPnlVariable3 = function () {
    var that = this,
        data = {
            msgtv_name: that.editMessageForm.getField("txtMessageTypeVariableName").getValue(),
            msgtv_default_value: that.editMessageForm.getField("txtMessageTypeVariableDefaultValue").getValue()
        };

    if (that.editRow == null) {
        that.gridCurrent.addItem(new PMUI.grid.GridPanelRow({
            data: data
        }));
    } else {
        that.editRow.setData(data);
    }

    that.cancelAcceptedValue();
};

MessageEventDefinition.prototype.cancelAcceptedValue = function () {
    var that = this;
    that.editRow = null;
    that._resetEditMessageForm();
    that.editMessageForm.setVisible(false);
};

MessageEventDefinition.prototype.grdPnlVariable3GetData = function () {
    var that = this,
        i,
        data = [];

    if (that.gridCurrent.visible) {
        data = that.gridCurrent.getData();

        for (i = 0; i <= data.length - 1; i += 1) {
            delete data[i].key;
            delete data[i].type;
        }
    }
    return data;
};

MessageEventDefinition.prototype.cboMessageTypeSetOptionsRestProxy = function (cboMessageType, messageTypeUidSelected) {
    var that = this, restProxy, iAux;

    cboMessageType.clearOptions();
    iAux = 0;
    restProxy = new PMRestClient({
        typeRequest: "get",
        endpoint: "message-types",

        functionSuccess: function (xhr, response) {
            var dataResponse = response, messageTypeData, i, arrayOptions, iAux;
            that.arrayMessageType = dataResponse;
            arrayOptions = [];
            for (i = 0; i <= dataResponse.length - 1; i += 1) {
                if (dataResponse[i].msgt_uid == messageTypeUidSelected) {
                    iAux = i;
                }

                arrayOptions.push(
                    {
                        value: dataResponse[i].msgt_uid,
                        label: dataResponse[i].msgt_name
                    }
                );
            }

            if (arrayOptions.length > 0) {
                cboMessageType.setOptions(arrayOptions);

                if (messageTypeUidSelected == "") {
                    //POST
                    cboMessageType.setValue(arrayOptions[0].value);
                    messageTypeData = that.getMessageTypeByIndex(arrayOptions[0].value);

                    if (messageTypeData != null) {
                        that.gridCurrent.setDataItems(messageTypeData.msgt_variables);
                    }
                } else {
                    //PUT
                    cboMessageType.setValue(arrayOptions[iAux].value);
                }
            }
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });

    restProxy.executeRestClient();
};

MessageEventDefinition.prototype.messageEventDefintionPostRestProxy = function (data) {
    var restProxy = new PMRestClient({
        endpoint: "message-event-definition",
        typeRequest: "post",
        data: data,

        functionSuccess: function (xhr, response) {
            var dataResponse = response;
            PMDesigner.msgFlash('The property event was saved successfully.'.translate(), document.body, 'success', 3000, 5);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });

    restProxy.executeRestClient();
};

MessageEventDefinition.prototype.messageEventDefintionPutRestProxy = function (data, messageEventDefinitionUid) {
    var restProxy = new PMRestClient({
        endpoint: "message-event-definition/" + messageEventDefinitionUid,
        typeRequest: "update",
        data: data,

        functionSuccess: function (xhr, response) {
            var dataResponse = response;
            PMDesigner.msgFlash('The property event was saved successfully.'.translate(), document.body, 'success', 3000, 5);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });

    restProxy.executeRestClient();
};
/**
 * Reset the fields from the form's Edit panel.
 * @returns {MessageEventDefinition}
 * @private
 */
MessageEventDefinition.prototype._resetEditMessageForm = function () {
    if (this.editMessageForm) {
        this.editMessageForm.getItems().map(function (i) {
            i.setValue("");
        });
    }
    return this;
};
