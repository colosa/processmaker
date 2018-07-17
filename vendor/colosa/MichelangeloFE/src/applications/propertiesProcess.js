(
    function () {
        PMDesigner.propertiesProcess = function () {
            var responseProperties = null,
                getValuesProperties,
                isDirtyFormProcess,
                saveProperties,
                propertiesWindow,
                processUID,
                textTitle,
                textDescription,
                dropCalendar,
                dropProcessCat,
                dropDynaform,
                dropRouting,
                checkDebug,
                checkHideCase,
                checkSubProcess,
                dropCaseCreated,
                dropCaseDeleted,
                dropCaseCancelled,
                dropCasePaused,
                dropCaseUnpaused,
                dropCaseReassigned,
                dropCaseOpen,
                dropTypeProcess,
                proCost,
                loadProperties,
                proUnitCost,
                formEditProcess,
                loadDynaforms,
                loadCalendar,
                loadTemplate,
                loadTriggers,
                loadTypeProcess,
                loadCategory,
                clickedClose;

            getValuesProperties = function () {
                var restClient = new PMRestClient({
                    typeRequest: 'post',
                    multipart: true,
                    data: {
                        "calls": [
                            {
                                "url": "project/" + PMDesigner.project.id + "/dynaforms",
                                "method": 'GET'
                            },
                            {
                                "url": "calendars",
                                "method": 'GET'
                            },
                            {
                                "url": "project/categories",
                                "method": 'GET'
                            },
                            {
                                "url": "project/" + PMDesigner.project.id + "/file-manager?path=templates",
                                "method": 'GET'
                            },
                            {
                                "url": "project/" + PMDesigner.project.id + "/triggers",
                                "method": 'GET'
                            },
                            {
                                "url": "project/" + PMDesigner.project.id + "/process",
                                "method": 'GET'
                            }
                        ]
                    },
                    functionSuccess: function (xhr, response) {
                        loadDynaforms(response[0].response);
                        loadCalendar(response[1].response);
                        loadCategory(response[2].response);
                        loadTemplate(response[3].response);
                        loadTriggers(response[4].response);
                        loadTypeProcess(response[5].response);
                        loadProperties(response[5].response);
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restClient.setBaseEndPoint('');
                restClient.executeRestClient();
            };
            isDirtyFormProcess = function () {
                var message_window
                if (formEditProcess.isDirty()) {
                    message_window = new PMUI.ui.MessageWindow({
                        id: "cancelMessageTriggers",
                        windowMessageType: 'warning',
                        width: 490,
                        title: "Edit process".translate(),
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
                                    propertiesWindow.close();
                                },
                                buttonType: "success"
                            }
                        ]
                    });
                    message_window.open();
                    message_window.showFooter();
                } else {
                    propertiesWindow.close();
                }
            };
            saveProperties = function (data) {
                var restClient = new PMRestClient({
                    typeRequest: 'update',
                    data: data,
                    messageSuccess: "Properties saved successfully".translate(),
                    functionSuccess: function (xhr, response) {
                        propertiesWindow.close();

                        PMDesigner.project.setProjectName(data.pro_title);
                        PMDesigner.project.setDescription(data.pro_description);
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);

                        PMDesigner.project.dirty = false;
                    }
                });
                restClient.setEndpoint("process");
                restClient.executeRestClient();
            };

            propertiesWindow = new PMUI.ui.Window({
                id: "propertiesProcessWindow",
                title: "Edit process".translate(),
                width: DEFAULT_WINDOW_WIDTH,
                height: DEFAULT_WINDOW_HEIGHT,
                footerHeight: 'auto',
                bodyHeight: "auto",
                modal: true,
                buttonPanelPosition: "bottom",
                footerAlign: "right",
                onBeforeClose: function () {
                    clickClose = true;
                    isDirtyFormProcess();
                },
                buttons: [
                    {
                        id: 'outputWindowDocButtonCancel',
                        text: "Cancel".translate(),
                        buttonType: "error",
                        handler: function () {
                            clickedClose = false;
                            isDirtyFormProcess();
                        }
                    },
                    {
                        id: 'outputWindowDocButtonSave',
                        text: "Save".translate(),
                        buttonType: "success",
                        handler: function () {
                            var dataForm;
                            if (formEditProcess.isValid()) {
                                if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                                    dataForm = getData2PMUI(formEditProcess.html);
                                } else {
                                    dataForm = formEditProcess.getData();
                                }
                                dataForm.pro_debug = checkDebug.controls[0].selected ? 1 : 0;
                                dataForm.pro_show_message = checkHideCase.controls[0].selected ? 1 : 0;
                                dataForm.pro_subprocess = checkSubProcess.controls[0].selected ? 1 : 0;
                                functionAssignmentUsers = function (xhr, response) {
                                };
                                saveProperties(dataForm);
                            }
                        }
                    }
                ]
            });

            processUID = new PMUI.field.TextField({
                label: "UID".translate(),
                id: "process_uid",
                name: "pro_uid",
                labelWidth: "35%",
                controlsWidth: "300px",
                disabled: true
            });

            textTitle = new PMUI.field.TextField({
                label: "Title".translate(),
                id: 'textTitle',
                name: 'pro_title',
                labelWidth: "35%",
                placeholder: 'a text here'.translate(),
                controlsWidth: "300px",
                required: true
            });

            textDescription = new PMUI.field.TextAreaField({
                id: 'textDescription',
                name: 'pro_description',
                label: "Description".translate(),
                labelWidth: "35%",
                controlsWidth: "500px",
                rows: 150,
                style: {cssClasses: ['mafe-textarea-resize']}
            });

            dropCalendar = new PMUI.field.DropDownListField({
                id: "dropCalendar",
                name: "pro_calendar",
                labelWidth: "35%",
                label: "Calendar".translate(),
                controlsWidth: "300px",
                valueType: 'string',
                onChange: function (newValue, prevValue) {
                }
            });

            dropProcessCat = new PMUI.field.DropDownListField({
                id: "dropProcessCat",
                name: "pro_category",
                controlsWidth: "300px",
                labelWidth: "35%",
                label: "Process Category".translate(),
                valueType: 'string',
                onChange: function (newValue, prevValue) {
                }
            });

            dropDynaform = new PMUI.field.DropDownListField({
                id: "dropDynaform",
                name: "pro_summary_dynaform",
                controlsWidth: "300px",
                labelWidth: "35%",
                label: "Dynaform to show a case summary".translate(),
                valueType: 'string',
                onChange: function (newValue, prevValue) {
                }
            });

            dropRouting = new PMUI.field.DropDownListField({
                id: "dropRouting",
                name: "pro_derivation_screen_tpl",
                controlsWidth: "300px",
                labelWidth: "35%",
                label: "Routing Screen Template".translate(),
                valueType: 'string',
                onChange: function (newValue, prevValue) {
                }
            });

            checkDebug = new PMUI.field.CheckBoxGroupField({
                id: 'checkDebug',
                labelWidth: "35%",
                label: "Debug".translate(),
                name: "pro_debug",
                value: '1',
                controlPositioning: 'vertical',
                options: [
                    {
                        id: 'pro_debug',
                        disabled: false,
                        value: '1',
                        selected: false
                    }
                ],
                onChange: function (newVal, oldVal) {
                }
            });

            checkHideCase = new PMUI.field.CheckBoxGroupField({
                id: 'checkHideCase',
                labelWidth: "35%",
                label: "Hide the case number and the case title in the steps".translate(),
                value: '1',
                name: "pro_show_message",
                controlPositioning: 'vertical',
                options: [
                    {
                        disabled: false,
                        value: '1',
                        selected: false
                    }
                ],
                onChange: function (newVal, oldVal) {
                }
            });

            checkSubProcess = new PMUI.field.CheckBoxGroupField({
                id: 'checkSubProcess',
                labelWidth: "35%",
                label: "This a sub-process".translate(),
                value: '1',
                name: "pro_subprocess",
                controlPositioning: 'vertical',
                options: [
                    {
                        disabled: false,
                        value: '1',
                        selected: false
                    }
                ],
                onChange: function (newVal, oldVal) {
                }
            });
            dropCaseCreated = new PMUI.field.DropDownListField({
                id: "dropCaseCreated",
                name: "pro_tri_create",
                labelWidth: "35%",
                label: "Execute a trigger when a case is created".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            dropCaseDeleted = new PMUI.field.DropDownListField({
                id: "dropCaseDeleted",
                name: "pro_tri_deleted",
                labelWidth: "35%",
                label: "Execute a trigger when a case is deleted".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            dropCaseCancelled = new PMUI.field.DropDownListField({
                id: "dropCaseCancelled",
                name: "pro_tri_canceled",
                labelWidth: "35%",
                label: "Execute a trigger when a case is cancelled".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            dropCasePaused = new PMUI.field.DropDownListField({
                id: "dropCasePaused",
                name: "pro_tri_paused",
                labelWidth: "35%",
                label: "Execute a trigger when a case is paused".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            dropCaseUnpaused = new PMUI.field.DropDownListField({
                id: "dropCaseUnpaused",
                name: "pro_tri_unpaused",
                labelWidth: "35%",
                label: "Execute a trigger when a case is unpaused".translate(),
                valueType: "string",
                controlsWidth: "300px"
            });

            dropCaseReassigned = new PMUI.field.DropDownListField({
                id: "dropCaseReassigned",
                name: "pro_tri_reassigned",
                labelWidth: "35%",
                label: "Execute a trigger when a case is reassigned".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            dropCaseOpen = new PMUI.field.DropDownListField({
                id: "dropCaseOpen",
                name: "pro_tri_open",
                label: "Execute a trigger when a case is opened".translate(),
                labelWidth: "35%",
                valueType: "string",
                controlsWidth: "300px",

                onChange: function (newValue, prevValue) {
                }
            });

            dropTypeProcess = new PMUI.field.DropDownListField({
                id: "dropTypeProcess",
                name: "pro_type_process",
                labelWidth: "35%",
                label: "Process Design Access: Public / Private (Owner)".translate(),
                valueType: 'string',
                controlsWidth: "300px",
                onChange: function (newValue, prevValue) {
                }
            });

            proCost = new PMUI.field.TextField({
                id: 'proCost',
                name: 'pro_cost',
                labelWidth: "35%",
                value: 0,
                placeholder: 'a cost here'.translate(),
                label: "Cost".translate(),
                valueType: 'integer',
                controlsWidth: "300px",
                required: window.enterprise === "1" ? true : false,
                style: {
                    cssProperties: {
                        float: "left"
                    }
                }
            });

            proUnitCost = new PMUI.field.TextField({
                id: 'proUnitCost',
                name: 'pro_unit_cost',
                labelWidth: "35%",
                value: '$',
                placeholder: 'a unit cost here'.translate(),
                controlsWidth: "300px",
                label: "Units".translate(),
                valueType: 'string',
                required: window.enterprise === "1" ? true : false,
                style: {
                    cssProperties: {
                        float: "left"
                    }
                }
            });

            formEditProcess = new PMUI.form.Form({
                id: 'formEditProcess',
                fieldset: true,
                title: "Process Information".translate(),
                width: DEFAULT_WINDOW_WIDTH - DEFAULT_WINDOW_WIDTH * 0.10,
                items: [
                    processUID,
                    textTitle,
                    textDescription,
                    dropCalendar,
                    dropProcessCat,
                    dropDynaform,
                    dropRouting,
                    checkDebug,
                    checkHideCase,
                    checkSubProcess,
                    dropCaseCreated,
                    dropCaseDeleted,
                    dropCaseCancelled,
                    dropCasePaused,
                    dropCaseUnpaused,
                    dropCaseReassigned,
                    dropCaseOpen,
                    dropTypeProcess,
                    proCost,
                    proUnitCost
                ]
            });

            //Load Dynaforms
            loadDynaforms = function (response) {
                var i;
                dropDynaform.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                for (i = 0; i < response.length; i += 1) {
                    dropDynaform.addOption({
                        label: response[i].dyn_title,
                        value: response[i].dyn_uid,
                        select: false
                    });
                }
            };

            //Load calendar
            loadCalendar = function (response) {
                var i;
                dropCalendar.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                for (i = 0; i < response.length; i += 1) {
                    dropCalendar.addOption({
                        label: response[i].cal_name,
                        value: response[i].cal_uid,
                        select: false
                    });
                }
            };

            //Load category
            loadCategory = function (response) {
                var i;
                dropProcessCat.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                for (i = 0; i < response.length; i += 1) {
                    dropProcessCat.addOption({
                        label: response[i].cat_name,
                        value: response[i].cat_uid,
                        select: false
                    });
                }
            };

            //Load Templates
            loadTemplate = function (response) {
                var i;
                dropRouting.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                for (i = 0; i < response.length; i += 1) {
                    if (response[i].prf_filename != "alert_message.html") {
                        dropRouting.addOption({
                            label: response[i].prf_filename,
                            value: response[i].prf_filename,
                            select: false
                        });
                    }
                }
            };

            //Load triggers
            loadTriggers = function (response) {
                var i;
                dropCaseCreated.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                dropCaseDeleted.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                dropCaseCancelled.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                dropCasePaused.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                dropCaseUnpaused.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });
                dropCaseReassigned.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });

                dropCaseOpen.addOption({
                    label: "None".translate(),
                    value: "",
                    select: false
                });

                for (i = 0; i < response.length; i += 1) {
                    dropCaseCreated.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                    dropCaseDeleted.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                    dropCaseCancelled.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                    dropCasePaused.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                    dropCaseUnpaused.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                    dropCaseReassigned.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });

                    dropCaseOpen.addOption({
                        label: response[i].tri_title,
                        value: response[i].tri_uid,
                        select: false
                    });
                }
            };

            // Load type of process
            loadTypeProcess = function (response) {
                dropTypeProcess.addOption({
                    label: "Public".translate(),
                    value: "PUBLIC",
                    select: false
                });
                dropTypeProcess.addOption({
                    label: "Private".translate(),
                    value: "PRIVATE",
                    select: false
                });
            };

            // Load properties of process
            loadProperties = function (response) {
                propertiesWindow.addItem(formEditProcess);
                propertiesWindow.open();
                formEditProcess.getField("pro_type_process").hideColon();
                formEditProcess.reset();
                responseProperties = response;
                processUID.setValue(response.pro_uid);
                textTitle.setValue(response.pro_title);
                textDescription.setValue(response.pro_description);
                dropDynaform.setValue(response.pro_summary_dynaform);
                dropCaseCancelled.setValue(response.pro_tri_canceled);
                dropCaseCreated.setValue(response.pro_tri_create);
                dropCaseDeleted.setValue(response.pro_tri_deleted);
                dropCasePaused.setValue(response.pro_tri_paused);
                dropCaseUnpaused.setValue(response.pro_tri_unpaused);
                dropCaseReassigned.setValue(response.pro_tri_reassigned);
                dropCaseOpen.setValue(response.pro_tri_open);
                dropRouting.setValue(response.pro_derivation_screen_tpl);
                dropCalendar.setValue(response.pro_calendar);
                dropProcessCat.setValue(response.pro_category);
                dropTypeProcess.setValue(response.pro_type_process);
                checkHideCase.setHeight(57)
                if (response.pro_debug == 1) {
                    checkDebug.getControls()[0].select();
                }
                if (response.pro_show_message == 1) {
                    checkHideCase.getControls()[0].select();
                }
                if (response.pro_subprocess == 1) {
                    checkSubProcess.getControls()[0].select();
                }
                proCost.setValue(response.pro_cost);
                if (response.pro_unit_cost != null && response.pro_unit_cost != '') {
                    proUnitCost.setValue(response.pro_unit_cost);
                }
            };

            getValuesProperties();

            propertiesWindow.showFooter();
            propertiesWindow.defineEvents();
            applyStyleWindowForm(propertiesWindow);
            formEditProcess.getField('pro_title').setFocus();
            formEditProcess.getField("pro_type_process").html.style.float = "left";

            dropCaseCancelled.style.addProperties({"float": "left"});
            dropCasePaused.style.addProperties({"float": "left"});
            dropCaseUnpaused.style.addProperties({"float": "left"});
            dropCaseReassigned.style.addProperties({"float": "left"});
            dropCaseOpen.style.addProperties({"float": "left"});

            $("#dropCaseCancelled,#dropCasePaused,#dropCaseReassigned,#dropTypeProcess,#dropCaseOpen").find("select:eq(0)").css("z-index", 1);

            if (window.enterprise === "1") {
                proCost.setVisible(true);
                proUnitCost.setVisible(true);
            } else {
                proCost.setVisible(false);
                proUnitCost.setVisible(false);
            }
        };

    }()
);