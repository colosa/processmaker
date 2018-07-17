(function () {
    PMDesigner.scriptTaskProperties = function (activity) {
        var that = this,
            taskUid,
            taskType,
            triggerSelectedData,
            oldValue,
            uidProj,
            scrtas_uid,
            buttonCancel,
            restClientNewScriptTask,
            restClientUpdateScriptTask,
            restClientNewTrigger,
            restClientUpdateTrigger,
            $ctrlSpaceMessage,
            triggerEngine,
            getListTrigger,
            getScriptTask,
            formScriptTask,
            buttonSave,
            domSettings,
            scriptTaskPropertiesWindow,
            triggerUid;

        taskUid = activity.getID();
        taskType = activity.getTaskType();
        taskType = "TRIGGER";
        oldValue = "";
        uidProj = PMDesigner.project.id;
        scrtas_uid = "";
        triggerUid = "";
        $ctrlSpaceMessage = $("<p class='ctrlSpaceMessage'>" + "Press".translate() + " <strong>ctrl+space</strong> " + "to get the function list".translate() + ".</p>");

        /*window*/
        buttonCancel = new PMUI.ui.Button({
            id: 'cancelScriptButton',
            text: "Cancel".translate(),
            buttonType: 'error',
            handler: function (event) {
                clickedClose = false;
                scriptTaskPropertiesWindow.isDirtyFormScript();
            }
        });

        restClientNewScriptTask = function (triggerUid) {
            var restClient = new PMRestClient({
                endpoint: 'script-task',
                typeRequest: 'post',
                data: {
                    scrtas_obj_uid: triggerUid,
                    act_uid: taskUid,
                    scrtas_obj_type: taskType
                },
                functionSuccess: function () {
                    scriptTaskPropertiesWindow.close();
                    PMDesigner.msgFlash('Script Task saved correctly'.translate(), document.body, 'success', 3000, 5);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Script Task, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        restClientUpdateScriptTask = function (triggerUid) {
            var restClient = new PMRestClient({
                endpoint: 'script-task/' + formScriptTask.getItems()[0].items.get(4).getValue(),
                typeRequest: 'update',
                data: {
                    scrtas_obj_uid: triggerUid, /*trigger uid*/
                    act_uid: taskUid,
                    scrtas_obj_type: taskType
                },
                functionSuccess: function () {
                    scriptTaskPropertiesWindow.close();
                    PMDesigner.msgFlash('Script Task saved correctly'.translate(), document.body, 'success', 3000, 5);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Script Task, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        restClientNewTrigger = function (dataToSend, newScriptTask) {
            var restClient = new PMRestClient({
                endpoint: 'trigger',
                typeRequest: 'post',
                data: dataToSend,
                functionSuccess: function (xhr, response) {
                    triggerUid = response.tri_uid;
                    if (triggerUid != "" && typeof triggerUid != "undefinied") {
                        if (newScriptTask) {
                            restClientNewScriptTask(triggerUid);
                        } else {
                            restClientUpdateScriptTask(triggerUid);
                        }
                    }
                },
                functionFailure: function (xhr, response) {
                    var msg = response.error.message,
                        arrayMatch = [];

                    if ((arrayMatch = /^[\w\s]+\:\s*(.*)$/i.exec(msg))) {
                        msg = arrayMatch[1];
                    }

                    PMDesigner.msgWinError(msg);
                }
            });
            restClient.executeRestClient();
        };

        restClientUpdateTrigger = function (newScriptTask) {
            var restClient = new PMRestClient({
                endpoint: 'trigger/' + formScriptTask.getItems()[0].items.get(1).getValue(),
                typeRequest: 'update',
                data: {
                    scrtas_obj_uid: formScriptTask.getItems()[0].items.get(1).getValue(),
                    tri_webbot: formScriptTask.getItems()[1].controls[0].cm.getValue(),
                    act_uid: taskUid,
                    scrtas_obj_type: taskType
                },
                functionSuccess: function (xhr, response) {
                    triggerUid = response.tri_uid;
                    if (newScriptTask) {
                        restClientNewScriptTask(formScriptTask.getItems()[0].items.get(1).getValue());
                    } else {
                        restClientUpdateScriptTask(formScriptTask.getItems()[0].items.get(1).getValue());
                    }
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Script Task, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        buttonSave = new PMUI.ui.Button({
            id: 'saveScriptButton',
            text: "Save".translate(),
            handler: function (event) {
                var newScriptTask, dataToSend;
                if (formScriptTask.isValid()) {
                    if (formScriptTask.getItems()[0].items.get(4).getValue() === "") { /*new*/
                        newScriptTask = true;
                        if (formScriptTask.getItems()[0].items.get(1).getValue() != "") {
                            restClientUpdateTrigger(newScriptTask);
                        } else {
                            dataToSend = {
                                tri_title: formScriptTask.getItems()[0].items.get(2).getValue(),
                                tri_description: "",
                                tri_webbot: formScriptTask.getItems()[1].controls[0].cm.getValue(),
                                tri_param: ""
                            };
                            restClientNewTrigger(dataToSend, newScriptTask);
                        }
                    } else {
                        newScriptTask = false;
                        if (formScriptTask.getItems()[0].items.get(1).getValue() !== "") {
                            restClientUpdateTrigger(newScriptTask);
                        } else {
                            dataToSend = {
                                tri_title: formScriptTask.getItems()[0].items.get(2).getValue(),
                                tri_description: "",
                                tri_webbot: formScriptTask.getItems()[1].controls[0].cm.getValue(),
                                tri_param: ""
                            };
                            restClientNewTrigger(dataToSend, newScriptTask);
                        }
                    }
                }
            },
            buttonType: 'success'
        });

        scriptTaskPropertiesWindow = new PMUI.ui.Window({
            id: "scriptTaskPropertiesWindow",
            title: "Script Task Properties".translate(),
            width: DEFAULT_WINDOW_WIDTH,
            height: DEFAULT_WINDOW_HEIGHT,
            footerItems: [
                {
                    text: "@@",
                    id: "selectPickerButton",
                    handler: function () {
                        var picker = new VariablePicker();
                        picker.open({
                            success: function (variable) {
                                var codeMirror = formScriptTask.getItems()[1].controls[0].cm;
                                var cursorPos = codeMirror.getCursor();
                                codeMirror.replaceSelection(variable);
                                codeMirror.setCursor(cursorPos.line, cursorPos.ch);
                            }
                        });
                    },
                    style: {
                        cssProperties: {
                            "margin-left": '208px',
                            "background": "rgb(45, 62, 80)",
                            "border": "1px solid rgb(45, 62, 80)"
                        }
                    }
                },
                buttonCancel,
                buttonSave
            ],
            buttonPanelPosition: "bottom",
            footerAling: "right",
            onBeforeClose: function () {
                clickedClose = true;
                scriptTaskPropertiesWindow.isDirtyFormScript();
            }
        });

        scriptTaskPropertiesWindow.isDirtyFormScript = function () {
            var that = this,
                message_window,
                title = "Script Task".translate();
            if (oldValue !== formScriptTask.getItems()[1].controls[0].cm.getValue()) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    windowMessageType: 'warning',
                    width: 490,
                    title: title,
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
                                that.close();
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                that.close();
            }
        };
        /*end window*/

        /*form*/

        triggerEngine = new PMUI.field.DropDownListField({
            id: "triggerEngine",
            name: "triggerEngine",
            label: "Title".translate(),
            options: null,
            controlsWidth: 400,
            required: true,
            onChange: function (newValue, prevValue) {
                var uidTri = newValue, i;
                for (i = 0; i < triggerSelectedData.length; i += 1) {
                    if (triggerSelectedData[i].tri_uid == uidTri) {
                        formScriptTask.getItems()[1].controls[0].cm.setValue(triggerSelectedData[i].tri_webbot);
                    }
                }
            }
        });

        getListTrigger = function (triggerEngine) {
            var restClient = new PMRestClient({
                endpoint: 'triggers',
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    var arrayOptions = [], i;
                    triggerSelectedData = response;
                    arrayOptions[0] = {
                        label: "- Select a trigger -".translate(),
                        value: "",
                        disabled: true,
                        selected: true
                    };
                    for (i = 0; i <= triggerSelectedData.length - 1; i += 1) {
                        arrayOptions.push(
                            {
                                value: triggerSelectedData[i].tri_uid,
                                label: triggerSelectedData[i].tri_title
                            }
                        );
                    }
                    triggerEngine.setOptions(arrayOptions);
                    triggerEngine.setValue(arrayOptions[0].value);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems getting the Triggers list, please try again.".translate()
            });
            restClient.executeRestClient();
        };

        getScriptTask = function () {
            var restClient = new PMRestClient({
                endpoint: 'script-task/activity/' + taskUid,
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    var i;
                    if (typeof response == "object") {
                        triggerUid = response.scrtas_obj_uid;
                        if (triggerUid != "" && typeof triggerUid != "undefinied") {
                            for (i = 0; i < triggerSelectedData.length; i += 1) {
                                if (triggerSelectedData[i].tri_uid == triggerUid) {
                                    formScriptTask.getItems()[1].controls[0].setValue(triggerSelectedData[i].tri_webbot);
                                    oldValue = triggerSelectedData[i].tri_webbot;
                                    formScriptTask.getItems()[0].items.get(4).setValue(response.scrtas_uid);
                                    formScriptTask.getItems()[0].items.get(1).setValue(triggerUid);
                                }
                            }
                        }
                    }
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        };

        formScriptTask = new PMUI.form.Form({
            id: "formScriptTask",
            border: true,
            visibleHeader: false,
            width: '900px',
            height: "300px",
            name: "formScriptTask",
            title: '',
            items: [
                {
                    id: "panelDetailsCustom",
                    pmType: "panel",
                    layout: 'vbox',
                    fieldset: false,
                    height: '380px',
                    legend: "DETAILS".translate(),
                    items: [
                        {
                            id: "taskUid",
                            pmType: "text",
                            label: "ID".translate(),
                            value: taskUid,
                            name: "taskUid",
                            readOnly: true,
                            visible: false,
                            valueType: 'string'
                        },
                        triggerEngine,
                        {
                            id: "newScript",
                            pmType: "text",
                            label: "Title".translate(),
                            controlsWidth: 400,
                            value: "",
                            name: "newScript",
                            required: false,
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "taskType",
                            pmType: "text",
                            value: taskType,
                            name: "taskType",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "scrtas_uid",
                            pmType: "text",
                            value: scrtas_uid,
                            name: "scrtas_uid",
                            visible: false,
                            valueType: 'string'
                        }
                    ]
                }
            ]
        });

        that.cd = new PMCodeMirrorField({
            labelWidth: '23.8%'
        });
        that.cd.setLabel("Code".translate());
        formScriptTask.addItem(that.cd);
        formScriptTask.getItems()[1].setName('code');
        formScriptTask.getItems()[1].setID('code');
        formScriptTask.getItems()[1].setHeight(300);
        CodeMirror.commands.autocomplete = function (cm) {
            CodeMirror.showHint(cm, CodeMirror.phpHint);
        };

        getListTrigger(triggerEngine);
        getScriptTask();
        /*end form*/

        domSettings = function () {
            var codeMirrorControl, requiredMessage, titleAdd, titleBack;
            codeMirrorControl = formScriptTask.getItems()[1].controls[0].cm;
            formScriptTask.getItems()[1].html.style.padding = "10px";
            if (codeMirrorControl != undefined) {
                codeMirrorControl.setSize(650, 255); //CodeMirror Size
                $("#code").find(".CodeMirror-wrap").css({'border': '1px solid #c0c0c0'});
            }
            requiredMessage = $(document.getElementById("requiredMessage"));
            scriptTaskPropertiesWindow.body.appendChild(requiredMessage[0]);
            requiredMessage[0].style['marginTop'] = '65px';

            scriptTaskPropertiesWindow.footer.html.style.textAlign = 'right';
            $(".CodeMirror.cm-s-default.CodeMirror-wrap").after($ctrlSpaceMessage.css({
                "padding-left": "10px",
                "margin": "3px 0px 0px 0px"
            }));

            titleAdd = "Add new".translate();
            titleBack = "Back to list".translate();

            $("#triggerEngine").find("select").after('&nbsp;&nbsp;<a id="titleAdd" title="' + titleAdd + '" class="showHideScript pmui pmui-button mafe-button-edit-assign-no-hover" href="#" style="left: 0px; top: 0px; width: auto; line-height: normal; position: relative; height: auto; z-index: auto;"><span class="button-label"></span><span class="button-icon " style="display: inline-block;"></span></a>');
            $("#newScript").find("input").after('&nbsp;&nbsp;<a id="titleBack" title="' + titleBack + '" class="showHideScript pmui pmui-button mafe-button-delete-assign-no-hover" href="#" style="left: 0px; top: 0px; width: auto; line-height: normal; position: relative; height: auto; z-index: auto;"><span class="button-label"></span><span class="button-icon " style="display: inline-block;"></span></a>');
            $("#titleAdd, #titleBack").tooltip();
        };

        scriptTaskPropertiesWindow.addItem(formScriptTask);
        scriptTaskPropertiesWindow.open();
        scriptTaskPropertiesWindow.showFooter();
        domSettings();

        $(".showHideScript").on("click", function () {
            if (formScriptTask.getItems()[0].items.get(1).isVisible()) {
                formScriptTask.getItems()[0].items.get(1).setVisible(false);
            } else {
                formScriptTask.getItems()[0].items.get(1).setVisible(true);
                formScriptTask.getItems()[0].items.get(1).setValue("");
                formScriptTask.getItems()[0].items.get(2).setRequired(false);
                formScriptTask.getItems()[0].items.get(1).setRequired(true);
                $(formScriptTask.getItems()[0].items.get(1).html).find("select").focus();
            }

            if (!formScriptTask.getItems()[0].items.get(2).isVisible()) {
                formScriptTask.getItems()[0].items.get(2).setVisible(true);
                formScriptTask.getItems()[0].items.get(1).setRequired(false);
                formScriptTask.getItems()[0].items.get(1).setValue("");
                formScriptTask.getItems()[0].items.get(2).setRequired(true);
                formScriptTask.getItems()[0].items.get(2).setValue("");
                $(formScriptTask.getItems()[0].items.get(2).html).find("input").focus();
            } else {
                formScriptTask.getItems()[0].items.get(2).setVisible(false);
            }
            formScriptTask.getItems()[1].controls[0].cm.setValue("");
            oldValue = "";

        });

    };
}());
