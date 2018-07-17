if (typeof(consolidated) == 'undefined') {
    consolidated = '0';
}
PMDesigner.activityProperties = function (activity) {
    switch (activity.getActivityType()) {
        case "SUB_PROCESS":
            PMDesigner.propertiesSubProcess(activity);
            break;
        case "TASK":
            if (activity.getTaskType() === "SCRIPTTASK") {
                PMDesigner.scriptTaskProperties(activity);
            } else {
                PMDesigner.taskProperties(activity);
            }
            break;
    }
};

PMDesigner.taskProperties = function (activity) {
    var formDefinitions,
        featuresForms = [],
        propertiesTabs,
        formCaseLabels,
        dataProperties,
        formTimingControl,
        configurationForms,
        customGrid,
        formNotifications,
        buttonFieldCancel,
        buttonFieldAdd,
        abeForm,
        MobilePanel,
        enableTaskMobile,
        routeCaseMobile,
        abeMainPanel,
        abeAddOption,
        abeTemplates,
        abeDynaforms,
        abeEmailAcount,
        abeFields,
        warningChanges,
        windowProperties,
        processDataProperties,
        i,
        editRow = null,
        arrayTrue = '["TRUE"]',
        arrayFalse = '["FALSE"]',
        stringTrue = "TRUE",
        stringFalse = "FALSE";

    configurationForms = [
        {
            id: 'windowPropertiesTabPanelDefinitions',
            icon: '',
            title: 'Definitions'.translate(),
            panel: formDefinitions = new PMUI.form.Form({
                id: 'formDefinitions',
                visibleHeader: false,
                width: DEFAULT_WINDOW_WIDTH - 250,
                items: [{
                    id: 'formDefinitionsUID',
                    pmType: 'text',
                    name: 'UID',
                    valueType: 'string',
                    label: 'UID'.translate(),
                    controlsWidth: 300
                }, {
                    id: 'formDefinitionsTitle',
                    pmType: 'text',
                    name: 'tas_title',
                    valueType: 'string',
                    label: 'Title'.translate(),
                    placeholder: 'Insert a title'.translate(),
                    required: true,
                    controlsWidth: 300
                }, {
                    id: 'formDefinitionsDescription',
                    pmType: 'textarea',
                    name: 'tas_description',
                    valueType: 'string',
                    label: 'Description'.translate(),
                    placeholder: 'Insert a description'.translate(),
                    rows: 100,
                    controlsWidth: 300,
                    style: {cssClasses: ['mafe-textarea-resize']}
                }, new CriteriaField({
                    id: 'formDefinitionsVariable',
                    pmType: 'text',
                    name: 'tas_priority_variable',
                    valueType: 'string',
                    label: 'Variable for Case priority'.translate(),
                    controlsWidth: 300
                }), {
                    id: 'formDefinitionsRouting',
                    pmType: 'dropdown',
                    name: 'tas_derivation_screen_tpl',
                    valueType: 'string',
                    label: 'Routing Screen Template'.translate(),
                    controlsWidth: 150,
                    style: {
                        cssProperties: {
                            float: "left"
                        }
                    }
                }
                ]
            })
        },
        {
            id: 'windowPropertiesTabPanelCaseLabels',
            icon: '',
            title: 'Case Labels'.translate(),
            panel: formCaseLabels = new PMUI.form.Form({
                id: 'formCaseLabels',
                visibleHeader: false,
                width: DEFAULT_WINDOW_WIDTH - 250,
                items: [new CriteriaField({
                    id: 'formCaseLabelsTitle',
                    pmType: 'text',
                    name: 'tas_def_title',
                    valueType: 'string',
                    label: 'Title'.translate(),
                    placeholder: 'Insert a title'.translate(),
                    controlsWidth: DEFAULT_WINDOW_WIDTH - 527
                }), new CriteriaField({
                    id: 'formCaseLabelsDescription',
                    pmType: 'textarea',
                    name: 'tas_def_description',
                    valueType: 'string',
                    label: 'Description'.translate(),
                    placeholder: 'Insert a description'.translate(),
                    rows: 100,
                    controlsWidth: DEFAULT_WINDOW_WIDTH - 527,
                    renderType: 'textarea'
                })
                ]
            })
        },
        {
            id: 'windowPropertiesTabPanelTimingControl',
            icon: '',
            title: 'Timing Control'.translate(),
            panel: formTimingControl = new PMUI.form.Form({
                id: 'formTimingControl',
                visibleHeader: false,
                width: DEFAULT_WINDOW_WIDTH - 250,
                items: [{
                    id: 'formTimingControlFly',
                    pmType: 'checkbox',
                    name: 'tas_transfer_fly',
                    labelVisible: false,
                    options: [
                        {
                            id: 'formTimingControlOption',
                            label: 'Allow users to change the task duration in runtime'.translate(),
                            value: '1'
                        }
                    ],
                    onChange: function (val) {
                        changeTimingControl(this.controls[0].selected);
                    }
                }, {
                    id: 'formTimingMainPanel',
                    pmType: 'panel',
                    items: [
                        {
                            id: 'formTimingControlTask',
                            pmType: 'text',
                            name: 'tas_duration',
                            valueType: 'string',
                            label: 'Task duration'.translate(),
                            required: true,
                            maxLength: 3,
                            controlsWidth: 200,
                            validators: [
                                {
                                    pmType: "regexp",
                                    criteria: /^\d*$/,
                                    errorMessage: "Please enter a positive integer value".translate()
                                }
                            ]
                        },
                        {
                            id: 'formTimingControlAverage',
                            pmType: 'text',
                            name: 'tas_average',
                            valueType: 'string',
                            label: 'Average'.translate(),
                            maxLength: 3,
                            readOnly: true,
                            controlsWidth: 200
                        },
                        {
                            id: 'formTimingControlSdv',
                            pmType: 'text',
                            name: 'tas_sdv',
                            valueType: 'string',
                            label: 'SDV'.translate(),
                            maxLength: 3,
                            readOnly: true,
                            controlsWidth: 200
                        },
                        {
                            id: 'formTimingControlTime',
                            pmType: 'dropdown',
                            name: 'tas_timeunit',
                            label: 'Time unit'.translate(),
                            controlsWidth: 100,
                            options: [{
                                id: 'formTimingControlTime1',
                                label: 'Hours'.translate(),
                                value: 'HOURS'
                            }, {
                                id: 'formTimingControlTime2',
                                label: 'Days'.translate(),
                                value: 'DAYS'
                            }, {
                                id: 'formTimingControlTime3',
                                label: 'Minutes'.translate(),
                                value: 'MINUTES'
                            }
                            ]
                        }, {
                            id: 'formTimingControlCount',
                            pmType: 'dropdown',
                            name: 'tas_type_day',
                            label: 'Count days by'.translate(),
                            controlsWidth: 150,
                            options: [{
                                id: 'formTimingControlCount1',
                                label: 'Work Days'.translate(),
                                value: 1
                            }, {
                                id: 'formTimingControlCount2',
                                label: 'Calendar Days'.translate(),
                                value: 2
                            }
                            ]
                        }, {
                            id: 'formTimingControlCalendar',
                            pmType: 'dropdown',
                            name: 'tas_calendar',
                            label: 'Calendar'.translate(),
                            options: [],
                            controlsWidth: 150
                        }
                    ]
                }
                ]
            })
        },
        {
            id: 'windowPropertiesTabPanelNotifications',
            icon: '',
            title: 'Notifications'.translate(),
            panel: formNotifications = new PMUI.form.Form({
                id: 'formNotifications',
                visibleHeader: false,
                width: DEFAULT_WINDOW_WIDTH - 250,
                items: [
                    {
                        id: 'formNotificationsTasSend',
                        pmType: 'checkbox',
                        name: 'tas_send_last_email',
                        labelVisible: false,
                        options: [{
                            id: 'formNotificationsTasSend1',
                            label: 'After routing notify the next assigned user(s)'.translate(),
                            value: '1'
                        }
                        ],
                        onChange: function (val) {
                            changeFormNotifications(this.controls[0].selected, 'text');
                        }
                    }, {
                        id: 'formNotificationsMainPanel',
                        pmType: 'panel',
                        items: [
                            {
                                id: 'tas_email_server_uid',
                                name: 'tas_email_server_uid',
                                pmType: 'dropdown',
                                label: 'Email account'.translate(),
                                controlsWidth: 300,
                                labelWidth: "27%",
                                options: [
                                    {
                                        label: 'Default email account'.translate(),
                                        value: ''
                                    }
                                ]
                            },
                            {
                                id: 'tas_not_email_from_format',
                                name: 'tas_not_email_from_format',
                                pmType: 'dropdown',
                                label: 'Email From Format'.translate(),
                                controlsWidth: 300,
                                labelWidth: "27%",
                                options: [
                                    {
                                        id: 'assignedUser',
                                        label: 'Assigned User'.translate(),
                                        value: 0
                                    }, {
                                        id: 'emailAccountSettings',
                                        label: 'Email Account Settings'.translate(),
                                        value: 1
                                    }
                                ]
                            },
                            new CriteriaField({
                                id: 'formNotificationsSubject',
                                pmType: 'text',
                                name: 'tas_def_subject_message',
                                valueType: 'string',
                                label: 'Subject'.translate(),
                                placeholder: 'Insert a title'.translate(),
                                required: true,
                                controlsWidth: 300,
                                labelWidth: "27%"
                            }), {
                                id: 'formNotificationsContent',
                                pmType: 'dropdown',
                                name: 'tas_def_message_type',
                                label: 'Content Type'.translate(),
                                controlsWidth: 150,
                                labelWidth: "27%",
                                options: [{
                                    id: 'formNotificationsContent1',
                                    value: 'text',
                                    label: 'Plain Text'.translate()
                                }, {
                                    id: 'formNotificationsContent2',
                                    value: 'template',
                                    label: 'Html Template'.translate()
                                }
                                ],
                                onChange: function (value) {
                                    visibleContentType(value);
                                }
                            }, new CriteriaField({
                                id: 'formNotificationsMessage',
                                pmType: 'textarea',
                                name: 'tas_def_message',
                                valueType: 'string',
                                label: 'Message'.translate(),
                                placeholder: 'Insert a message'.translate(),
                                rows: 100,
                                width: 10,
                                required: true,
                                controlsWidth: 300,
                                renderType: 'textarea',
                                labelWidth: "27%"
                            }), {
                                id: 'formNotificationsTemplate',
                                pmType: 'dropdown',
                                name: 'tas_def_message_template',
                                label: 'Template'.translate(),
                                options: [{
                                    value: '',
                                    label: ''
                                }],
                                labelWidth: "27%"
                            }
                        ]
                    },
                    {
                        id: 'formNotificationsTasReceive',
                        pmType: 'checkbox',
                        name: 'tas_receive_last_email',
                        labelVisible: false,
                        options: [{
                            id: 'formNotificationsTasReceive',
                            label: 'Notify the assigned user to this task'.translate(),
                            value: '1'
                        }
                        ],
                        onChange: function (val) {
                            changeFormNotificationsReceive(this.controls[0].selected, 'text');
                        }
                    },
                    {
                        id: 'formNotificationsReceivePanel',
                        pmType: 'panel',
                        items: [
                            {
                                id: 'tas_receive_server_uid',
                                name: 'tas_receive_server_uid',
                                pmType: 'dropdown',
                                label: 'Email account'.translate(),
                                controlsWidth: 300,
                                labelWidth: "27%",
                                options: [
                                    {
                                        label: 'Default email account'.translate(),
                                        value: ''
                                    }
                                ]
                            },
                            {
                                id: 'tas_receive_email_from_format',
                                name: 'tas_receive_email_from_format',
                                pmType: 'dropdown',
                                label: 'Email From Format'.translate(),
                                controlsWidth: 300,
                                labelWidth: "27%",
                                options: [
                                    {
                                        id: 'assignedUser',
                                        label: 'Assigned User'.translate(),
                                        value: 0
                                    }, {
                                        id: 'emailAccountSettings',
                                        label: 'Email Account Settings'.translate(),
                                        value: 1
                                    }
                                ]
                            },
                            new CriteriaField({
                                id: 'tas_receive_subject_message',
                                pmType: 'text',
                                name: 'tas_receive_subject_message',
                                valueType: 'string',
                                label: 'Subject'.translate(),
                                placeholder: 'Insert a title'.translate(),
                                required: true,
                                controlsWidth: 300,
                                labelWidth: "27%"
                            }),
                            {
                                id: 'tas_receive_message_type',
                                pmType: 'dropdown',
                                name: 'tas_receive_message_type',
                                label: 'Content Type'.translate(),
                                controlsWidth: 150,
                                labelWidth: "27%",
                                options: [{
                                    id: 'formNotificationsReceive1',
                                    value: 'text',
                                    label: 'Plain Text'.translate()
                                }, {
                                    id: 'formNotificationsReceive2',
                                    value: 'template',
                                    label: 'Html Template'.translate()
                                }
                                ],
                                onChange: function (value) {
                                    visibleContentTypeReceive(value);
                                }
                            },
                            new CriteriaField({
                                id: 'tas_receive_message',
                                pmType: 'textarea',
                                name: 'tas_receive_message',
                                valueType: 'string',
                                label: 'Message'.translate(),
                                placeholder: 'Insert a message'.translate(),
                                rows: 100,
                                width: 10,
                                required: true,
                                controlsWidth: 300,
                                renderType: 'textarea',
                                labelWidth: "27%"
                            }),
                            {
                                id: 'tas_receive_message_template',
                                pmType: 'dropdown',
                                name: 'tas_receive_message_template',
                                label: 'Template'.translate(),
                                options: [{
                                    value: '',
                                    label: ''
                                }],
                                labelWidth: "27%"
                            }

                        ]
                    }
                ]
            })
        }
    ];
    if (consolidated == '1') {
        configurationForms.push({
            id: 'windowPropertiesTabPanelConsolidated',
            icon: '',
            title: 'Consolidated Case '.translate(),
            panel: formConsolidated = new PMUI.form.Form({
                id: 'formConsolidated',
                visibleHeader: false,
                width: DEFAULT_WINDOW_WIDTH - 250,
                items: [
                    {
                        id: 'formConsolidatedEnable',
                        pmType: 'checkbox',
                        name: 'consolidated_enable',
                        labelVisible: false,
                        options: [{
                            label: 'Enable consolidate for this task.'.translate(),
                            value: '1'
                        }
                        ],
                        onChange: function (val) {
                            changeConsolidated(this.controls[0].selected);
                        }
                    }, {
                        id: 'formConsolidatedMainPanel',
                        pmType: 'panel',
                        items: [
                            {
                                id: 'formConsolidatedReportTable',
                                pmType: 'text',
                                name: 'consolidated_report_table',
                                visible: false,
                                valueType: 'string',
                                controlsWidth: 300
                            }, {
                                id: 'formConsolidatedDynaform',
                                pmType: 'dropdown',
                                name: 'consolidated_dynaform',
                                label: 'Dynaform Template'.translate(),
                                options: [],
                                required: true,
                                controlsWidth: 300
                            }, {
                                id: 'formConsolidatedTable',
                                pmType: 'text',
                                name: 'consolidated_table',
                                valueType: 'string',
                                label: 'Table Name'.translate(),
                                placeholder: 'Insert a table name'.translate(),
                                required: true,
                                controlsWidth: 300,
                                style: {
                                    cssProperties: {
                                        float: "left"
                                    }
                                },
                                validators: [{
                                    pmType: "regexp",
                                    criteria: /^[a-zA-Z0-9_]+$/,
                                    errorMessage: "The table name can not contain spaces".translate()
                                }]
                            }, {
                                id: 'formConsolidatedTitle',
                                pmType: 'text',
                                name: 'consolidated_title',
                                valueType: 'string',
                                label: 'Title'.translate(),
                                placeholder: 'Insert a title'.translate(),
                                required: true,
                                controlsWidth: 300,
                                style: {
                                    cssProperties: {
                                        float: "left"
                                    }
                                }
                            }
                        ]
                    }
                ]
            })
        });
    }

    /*----------------------------------********---------------------------------*/

    warningChanges = new PMUI.ui.MessageWindow({
        id: 'warningChanges',
        windowMessageType: 'warning',
        width: 490,
        bodyHeight: 'auto',
        title: 'Activity Properties'.translate(),
        message: 'Are you sure you want to discard your changes?'.translate(),
        footerItems: [
            {
                id: 'warningChangesButtonNo',
                text: "No".translate(),
                handler: function () {
                    warningChanges.close();
                },
                buttonType: "error"
            },
            {
                id: 'warningChangesButtonYes',
                text: "Yes".translate(),
                handler: function () {
                    windowProperties.close();
                    warningChanges.close();
                },
                buttonType: "success"
            }
        ]
    });
    windowProperties = new PMUI.ui.Window({
        id: 'windowProperties',
        title: 'Activity Properties'.translate(),
        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH,
        onOpen: function () {
            loadServerData();
        },
        items: [
            propertiesTabs = new PMUI.panel.TabPanel({
                id: 'windowPropertiesTabPanel',
                height: 435,
                width: DEFAULT_WINDOW_WIDTH - 50,
                style: {
                    cssProperties: {
                        'margin-left': '10px'
                    }
                },
                items: configurationForms.concat(featuresForms),
                itemsPosition: {
                    position: 'left'
                },
                onTabClick: function (tab) {
                    setFocusTab(tab);
                }
            }),
            customGrid
        ],
        onBeforeClose: function () {
            if (isDirty()) {
                warningChanges.open();
                warningChanges.showFooter();
            } else {
                windowProperties.close();
            }
        },
        buttonPanelPosition: 'bottom',
        buttonsPosition: 'right',
        buttons: [
            {
                id: 'windowPropertiesButtonCancel',
                text: 'Cancel'.translate(),
                handler: function () {
                    if (isDirty()) {
                        warningChanges.open();
                        warningChanges.showFooter();
                    } else {
                        windowProperties.close();
                    }
                },
                buttonType: "error"
            },
            {
                id: 'windowPropertiesButtonSave',
                text: 'Save'.translate(),
                handler: function () {
                    saveData();
                },
                buttonType: 'success'
            }
        ]
    });

    function changeConsolidated(value) {
        var panel = formConsolidated.getItem('formConsolidatedMainPanel');
        formConsolidated.getField('consolidated_enable').setValue(value ? '["1"]' : '[]');

        if (panel) {
            panel.setVisible(value);
            if (value) {
                panel.enable();
            } else {
                panel.disable();
            }
        }

        formConsolidated.getField('consolidated_report_table').setVisible(false);
    }

    function changeTimingControl(value) {
        var mainPanel = formTimingControl.getItem('formTimingMainPanel');
        formTimingControl.getField('tas_transfer_fly').setValue(value ? '["1"]' : '[]');
        if (mainPanel) {
            if (value) {
                mainPanel.disable();

            } else {
                mainPanel.enable();
            }
            mainPanel.setVisible(!value);
        }
        if (window.enterprise !== "1") {
            formTimingControl.getField('tas_average').setVisible(false);
            formTimingControl.getField('tas_sdv').setVisible(false);
        }
    }

    function changeFormNotifications(value, valueTwo) {
        var panel = formNotifications.getItem('formNotificationsMainPanel');

        if (panel) {
            panel.setVisible(value);
            if (value) {
                panel.enable();
            } else {
                panel.disable();
            }
        }

        if (value) {
            formNotifications.getField('tas_def_message_type').setValue(valueTwo);
            visibleContentType(valueTwo);
        }
        formNotifications.getField('tas_send_last_email').setValue(value ? '["1"]' : '[]');
        formNotifications.getField('tas_def_subject_message').setFocus();
    }

    /**
     * Notification receive change handler, according to user selection
     * @param value
     * @param valueTwo
     */
    function changeFormNotificationsReceive(value, valueTwo) {
        var panel = formNotifications.getItem('formNotificationsReceivePanel');

        if (panel) {
            panel.setVisible(value);
            if (value) {
                panel.enable();
            } else {
                panel.disable();
            }
        }

        if (value) {
            formNotifications.getField('tas_receive_message_type').setValue(valueTwo);
            visibleContentTypeReceive(valueTwo);
        }
        formNotifications.getField('tas_receive_last_email').setValue(value ? '["1"]' : '[]');
        formNotifications.getField('tas_receive_subject_message').setFocus();
    }

    /**
     * Get value parsed
     * @param valueCheckBox '["TRUE"]'/'["FALSE"]'
     * @returns {string} "TRUE"/"FALSE"
     */
    function getValueCheckBox(valueCheckBox) {
        var optionSelected = JSON.parse(valueCheckBox),
            valChecked;
        valChecked = (Array.isArray(optionSelected) && optionSelected.length === 1) ? optionSelected[0] : stringFalse;
        return valChecked;
    }

    function loadFormData(response) {
        dataProperties = response.properties;
        formDefinitions.getField('UID').disable();
        formDefinitions.getField('UID').setValue(activity.id);
        formDefinitions.getField('tas_title').setValue(dataProperties.tas_title);
        formDefinitions.getField('tas_title').setFocus();
        formDefinitions.getField('tas_description').setValue(dataProperties.tas_description);
        formDefinitions.getField('tas_priority_variable').setValue(dataProperties.tas_priority_variable);
        formDefinitions.getField('tas_derivation_screen_tpl').setValue(dataProperties.tas_derivation_screen_tpl);

        formCaseLabels.getField('tas_def_title').setValue(dataProperties.tas_def_title);
        formCaseLabels.getField('tas_def_description').setValue(dataProperties.tas_def_description);

        if (dataProperties.tas_transfer_fly === 'FALSE') {
            changeTimingControl(false);
        } else {
            changeTimingControl(true);
        }
        formTimingControl.getField('tas_duration').setValue(dataProperties.tas_duration);
        formTimingControl.getField('tas_timeunit').setValue(dataProperties.tas_timeunit);
        formTimingControl.getField('tas_type_day').setValue(dataProperties.tas_type_day);
        formTimingControl.getField('tas_calendar').setValue(dataProperties.tas_calendar);

        if (window.enterprise === "1") {
            formTimingControl.getField('tas_average').setValue(dataProperties.tas_average);
            formTimingControl.getField('tas_sdv').setValue(dataProperties.tas_sdv);
        }

        changeFormNotifications(dataProperties.tas_send_last_email === 'TRUE', dataProperties.tas_def_message_type);
        changeFormNotificationsReceive(dataProperties.tas_receive_last_email === 'TRUE', dataProperties.tas_receive_message_type);
        formNotifications.getField('tas_def_subject_message').setValue(dataProperties.tas_def_subject_message);
        formNotifications.getField('tas_def_message_type').setValue(dataProperties.tas_def_message_type);
        formNotifications.getField('tas_def_message').setValue(dataProperties.tas_def_message);
        formNotifications.getField('tas_email_server_uid').setValue(dataProperties.tas_email_server_uid);
        formNotifications.getField('tas_def_message_template').setValue(dataProperties.tas_def_message_template);
        formNotifications.getField('tas_not_email_from_format').setValue(dataProperties.tas_not_email_from_format);
        // receive
        formNotifications.getField('tas_receive_subject_message').setValue(dataProperties.tas_receive_subject_message);
        formNotifications.getField('tas_receive_message_type').setValue(dataProperties.tas_receive_message_type);
        formNotifications.getField('tas_receive_message').setValue(dataProperties.tas_receive_message);
        formNotifications.getField('tas_receive_server_uid').setValue(dataProperties.tas_receive_server_uid);
        formNotifications.getField('tas_receive_message_template').setValue(dataProperties.tas_receive_message_template);
        formNotifications.getField('tas_receive_email_from_format').setValue(dataProperties.tas_receive_email_from_format);
    }
    /*----------------------------------********---------------------------------*/
    function loadCalendar(response) {
        var field = formTimingControl.getField('tas_calendar'), i;
        field.clearOptions();
        field.addOption({
            value: '',
            label: '- None -'.translate()
        });
        for (i = 0; i < response.length; i += 1) {
            field.addOption({
                value: response[i].cal_uid,
                label: response[i].cal_name
            });
        }
    }

    /**
     * Loads the template from rest service response
     * @param response
     * @param fieldName
     */
    function loadTemplate(response, fieldName) {
        var field = formNotifications.getField(fieldName), i, field2;
        field.clearOptions();
        field.addOption({
            value: 'alert_message.html',
            label: '-- Default --'.translate()
        });
        for (i = 0; i < response.length; i += 1) {
            field.addOption({
                value: response[i].prf_filename,
                label: response[i].prf_filename
            });
        }
        field2 = formDefinitions.getField('tas_derivation_screen_tpl');
        field2.clearOptions();
        field2.addOption({
            value: '',
            label: '-- Default --'.translate()
        });
        for (i = 0; i < response.length; i += 1) {
            if (response[i].prf_filename !== 'alert_message.html') {
                field2.addOption({
                    value: response[i].prf_filename,
                    label: response[i].prf_filename
                });
            }
        }
    };
    /**
     * Load Email account server dropdown options
     * @param response
     */
    function loadEmailAccount(response, fieldName) {
        var field = formNotifications.getField(fieldName) || null,
            i;
        if (response instanceof Array && field) {
            for (i = 0; i < response.length; i += 1) {
                field.addOption({
                    value: response[i].mess_uid,
                    label: response[i].mess_account
                });
            }
        }

    };

    function loadABETemplateField(templates) {
        var templateField = abeForm.getField('ABE_TEMPLATE'), i;
        for (i in templates) {
            if (templateField !== null) {
                templateField.addOption({
                    value: templates[i].FIELD,
                    label: templates[i].NAME
                });
            }
            abeTemplates.options.push({
                value: templates[i].FIELD,
                label: templates[i].NAME
            });
        }
    };
    /**
     * Loads the email accounts settings
     * @param response
     */
    function loadABEmailAccount(response) {
        var accountField = abeForm.getField('ABE_EMAIL_SERVER_UID') || null,
            i;

        if (response instanceof Array) {
            for (i = 0; i < response.length; i += 1) {
                if (accountField !== null) {
                    accountField.addOption({
                        value: response[i].mess_uid,
                        label: response[i].mess_account
                    });
                }
                abeEmailAcount.options.push({
                    value: response[i].mess_uid,
                    label: response[i].mess_account
                });
            }
        }

    };

    function loadABEDynaformField(dynaforms) {
        var dynaformField = abeForm.getField('DYN_UID'), i;
        for (i in dynaforms) {
            if (dynaformField !== null) {
                dynaformField.addOption({
                    value: dynaforms[i].DYN_UID,
                    label: dynaforms[i].DYN_NAME
                });
            }
            abeDynaforms.options.push({
                value: dynaforms[i].DYN_UID,
                label: dynaforms[i].DYN_NAME
            });
        }
    }

    function loadServerData() {
        var restClient, i;
        restClient = new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: {
                    "formconfig": {
                        "url": 'project/' + PMDesigner.project.id + '/activity/' + activity.id,
                        "method": 'GET'
                    },
                    "calendar": {
                        "url": 'calendar',
                        "method": 'GET'
                    },
                    "templates": {
                        "url": 'project/' + PMDesigner.project.id + '/file-manager?path=templates',
                        "method": 'GET'
                    },
                    "emailserver": {
                        "url": 'project/' + PMDesigner.project.id + '/email-event/accounts/emailServer',
                        "method": 'GET'
                    }
                }
            },
            functionSuccess: function (xhr, response) {
                loadTemplate(response["templates"].response, 'tas_def_message_template');
                loadTemplate(response["templates"].response, 'tas_receive_message_template');
                loadCalendar(response["calendar"].response);
                loadFormData(response["formconfig"].response);
                loadEmailAccount(response["emailserver"].response, 'tas_email_server_uid');
                loadEmailAccount(response["emailserver"].response, 'tas_receive_server_uid');

                /*----------------------------------********---------------------------------*/
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });
        if (consolidated == '1') {
            $.extend(restClient.data.calls, {
                "steps": {
                    'url': 'project/' + PMDesigner.project.id + '/activity/' + activity.id + '/steps',
                    'method': 'GET'
                },
                "consolidate": {
                    "url": 'consolidated/' + activity.id,
                    "method": 'GET'
                }
            });
        }
        /*----------------------------------********---------------------------------*/
        restClient.setBaseEndPoint('');
        restClient.executeRestClient();
    }

    function visibleContentType(value) {
        formNotifications.getField('tas_def_message_template').disable();
        formNotifications.getField('tas_def_message').disable();
        if (value === 'text') {
            formNotifications.getField('tas_def_message').enable();
            formNotifications.getField('tas_def_message').setVisible(true);
            formNotifications.getField('tas_def_message_template').setVisible(false);
        }
        if (value === 'template') {
            formNotifications.getField('tas_def_message_template').enable();
            formNotifications.getField('tas_def_message').setVisible(false);
            formNotifications.getField('tas_def_message_template').setVisible(true);
        }
    };
    /**
     * content type handler, enable or disable templates or message field
     * @param value
     */
    function visibleContentTypeReceive(value) {
        formNotifications.getField('tas_receive_message_template').disable();
        formNotifications.getField('tas_receive_message').disable();
        if (value === 'text') {
            formNotifications.getField('tas_receive_message').enable();
            formNotifications.getField('tas_receive_message').setVisible(true);
            formNotifications.getField('tas_receive_message_template').setVisible(false);
        }
        if (value === 'template') {
            formNotifications.getField('tas_receive_message_template').enable();
            formNotifications.getField('tas_receive_message').setVisible(false);
            formNotifications.getField('tas_receive_message_template').setVisible(true);
        }
    };
    function saveData() {
        var tabPanel = windowProperties.getItem('windowPropertiesTabPanel'),
            tabItems = tabPanel.getItems(),
            valid = true,
            panel,
            tas_transfer_fly,
            tas_send_last_email,
            tas_receive_last_email,
            dataCaseLabels,
            dataDefinitions,
            dataTimingControl,
            dataNotification,
            consolidated_enable,
            message,
            i;

        for (i = 0; i < tabItems.length; i += 1) {
            panel = tabItems[i].getPanel();
            if (panel instanceof PMUI.form.Form) {
                valid = valid && panel.isValid();
            } else {
                if (panel.getID() === "ActionsByEmailPanel") {
                    if (!abeForm.isValid()) {
                        valid = false;
                    }
                }
            }
            if (!valid) {
                tabItems[i].select();
                return;
            }
        }
        /*----------------------------------********---------------------------------*/

        tas_transfer_fly = formTimingControl.getField('tas_transfer_fly').getValue() === '["1"]';
        tas_send_last_email = formNotifications.getField('tas_send_last_email').getValue() === '["1"]';
        tas_receive_last_email = formNotifications.getField('tas_receive_last_email').getValue() === '["1"]';

        if (tas_transfer_fly !== false) {
            var a = formTimingControl.getField('tas_transfer_fly').getValue();
            formTimingControl.reset();
            formTimingControl.getField('tas_transfer_fly').setValue(a);
            formTimingControl.getField('tas_duration').setValue('1');
            formTimingControl.getField('tas_timeunit').setValue('DAYS');
            formTimingControl.getField('tas_type_day').setValue('');
            formTimingControl.getField('tas_calendar').setValue('');
        }

        if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
            dataDefinitions = getData2PMUI(formDefinitions.html);
            dataCaseLabels = getData2PMUI(formCaseLabels.html);
            dataTimingControl = getData2PMUI(formTimingControl.html);
            dataNotification = getData2PMUI(formNotifications.html);
            if (!dataCaseLabels) {
                dataCaseLabels = {};
            }
            if (!dataTimingControl) {
                dataTimingControl = {};
            }
            if (!dataNotification) {
                dataNotification = {};
            }
        } else {
            dataDefinitions = formDefinitions.getData();
            dataCaseLabels = formCaseLabels.getData();
            dataTimingControl = formTimingControl.getData();
            dataNotification = formNotifications.getData();
        }

        if (dataDefinitions['tas_title']) {
            dataProperties.tas_title = dataDefinitions['tas_title'];
        }
        if (dataDefinitions['tas_description']) {
            dataProperties.tas_description = dataDefinitions['tas_description'];
        }
        if (dataDefinitions['tas_priority_variable'] || typeof dataDefinitions['tas_priority_variable'] == "string") {
            dataProperties.tas_priority_variable = dataDefinitions['tas_priority_variable'];
        }
        if (dataDefinitions['tas_derivation_screen_tpl'] || dataDefinitions['tas_derivation_screen_tpl'] == "") {
            dataProperties.tas_derivation_screen_tpl = dataDefinitions['tas_derivation_screen_tpl'];
        }
        dataProperties.tas_def_title = dataCaseLabels['tas_def_title'];
        dataProperties.tas_def_description = dataCaseLabels['tas_def_description'];
        dataProperties.tas_transfer_fly = tas_transfer_fly ? 'TRUE' : 'FALSE';
        if (dataTimingControl['tas_duration']) {
            dataProperties.tas_duration = dataTimingControl['tas_duration'];
        }
        if (dataTimingControl['tas_timeunit']) {
            dataProperties.tas_timeunit = dataTimingControl['tas_timeunit'];
        }
        if (dataTimingControl['tas_type_day']) {
            dataProperties.tas_type_day = dataTimingControl['tas_type_day'];
        }
        if (dataTimingControl['tas_calendar']) {
            dataProperties.tas_calendar = dataTimingControl['tas_calendar'];
        }
        dataProperties.tas_send_last_email = tas_send_last_email ? 'TRUE' : 'FALSE';
        if (dataNotification['tas_not_email_from_format']) {
            dataProperties.tas_not_email_from_format = dataNotification['tas_not_email_from_format'];
        }
        if (dataNotification['tas_def_subject_message']) {
            dataProperties.tas_def_subject_message = dataNotification['tas_def_subject_message'];
        }
        if (dataNotification['tas_def_message_type']) {
            dataProperties.tas_def_message_type = dataNotification['tas_def_message_type'];
        }
        if (dataNotification['tas_def_message']) {
            dataProperties.tas_def_message = dataNotification['tas_def_message'];
        }
        if (dataNotification['tas_def_message_template']) {
            dataProperties.tas_def_message_template = dataNotification['tas_def_message_template'];
        }
        if (dataNotification['tas_email_server_uid'] !== 'undefined' && dataNotification['tas_email_server_uid'] !== null) {
            dataProperties.tas_email_server_uid = dataNotification['tas_email_server_uid'];
        }

        dataProperties.tas_receive_last_email = tas_receive_last_email ? 'TRUE' : 'FALSE';
        if (dataNotification['tas_receive_email_from_format']) {
            dataProperties.tas_receive_email_from_format = dataNotification['tas_receive_email_from_format'];
        }
        if (dataNotification['tas_receive_server_uid'] !== 'undefined' && dataNotification['tas_receive_server_uid'] !== null) {
            dataProperties.tas_receive_server_uid = dataNotification['tas_receive_server_uid'];
        }
        if (dataNotification['tas_receive_subject_message']) {
            dataProperties.tas_receive_subject_message = dataNotification['tas_receive_subject_message'];
        }
        if (dataNotification['tas_receive_message_type']) {
            dataProperties.tas_receive_message_type = dataNotification['tas_receive_message_type'];
        }
        if (dataNotification['tas_receive_message']) {
            dataProperties.tas_receive_message = dataNotification['tas_receive_message'];
        }
        if (dataNotification['tas_receive_message_template']) {
            dataProperties.tas_receive_message_template = dataNotification['tas_receive_message_template'];
        }
        /*----------------------------------********---------------------------------*/

        if (consolidated == '1') {
            consolidated_enable = false;
            if (formConsolidated.getField('consolidated_enable').getValue() == '["1"]') {
                consolidated_enable = true;
                if (!formConsolidated.isValid()) {
                    return;
                }
            }
            dataProperties.consolidate_data = {
                "consolidated_enable": consolidated_enable,
                "consolidated_dynaform": formConsolidated.getField('consolidated_dynaform').getValue(),
                "consolidated_table": formConsolidated.getField('consolidated_table').getValue(),
                "consolidated_title": formConsolidated.getField('consolidated_title').getValue(),
                "consolidated_report_table": formConsolidated.getField('consolidated_report_table').getValue()
            };
        }

        (new PMRestClient({
            endpoint: 'activity/' + activity.id,
            typeRequest: 'update',
            messageError: '',
            data: {
                definition: {},
                properties: dataProperties
            },
            functionSuccess: function (xhr, response) {
                setNameActivity(dataProperties.tas_title);
                windowProperties.close();
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            },
            messageSuccess: 'Task properties saved successfully'.translate(),
            flashContainer: document.body
        })).executeRestClient();
    }

    function setFocusTab(tab) {
        var style;
        $customGrid = $("#customGrid");
        $customGrid.hide().appendTo($("#windowProperties").find(".pmui-window-body:eq(0)"));

        if (tab.getTitle() === 'Definitions'.translate()) {
            formDefinitions.getField('tas_title').setFocus();
        }
        if (tab.getTitle() === 'Case Labels'.translate()) {
            formCaseLabels.getField('tas_def_title').setFocus();
            style = $('#formCaseLabelsDescription .pmui-field-label').attr("style");
            style = style + ' float: left;';
            $('#formCaseLabelsDescription .pmui-field-label').attr("style", style);
        }
        if (tab.getTitle() === 'Timing Control'.translate()) {
            formTimingControl.getField('tas_duration').setFocus();
        }
        if (tab.getTitle() === 'Notifications'.translate()) {
            formNotifications.getField('tas_def_subject_message').setFocus();
            style = $('#formNotificationsMessage .pmui-field-label').attr("style");
            style = style + ' float: left;';
            $('#formNotificationsMessage .pmui-field-label').attr("style", style);

            style = $('#tas_receive_message .pmui-field-label').attr("style");
            style = style + ' float: left;';
            $('#tas_receive_message .pmui-field-label').attr("style", style);
        }
        if (tab.getTitle() === 'Actions by Email'.translate()) {
            abeForm.getField("ABE_TYPE").onChange(abeForm.getField("ABE_TYPE").getValue());
        }
    }

    function isDirty() {
        return formDefinitions.isDirty() ||
            formCaseLabels.isDirty() ||
            formTimingControl.isDirty() ||
            formNotifications.isDirty();
    }

    function setNameActivity(name) {
        activity.setName(name);
        activity.setActName(name);
        PMDesigner.project.dirty = true;
    }

    windowProperties.open();
    windowProperties.showFooter();

    applyStyleWindowForm(windowProperties);
    if (consolidated == '1') {
        formConsolidated.getField('consolidated_report_table').setVisible(false);
    }
    /*----------------------------------********---------------------------------*/
    function customDOM() {
        $customGrid = $("#customGrid");
        $customGrid.show().appendTo($("#customGridPanel").find("fieldset:eq(0)"));
        document.getElementById("customGridPanel").style.width = "720px";
        document.getElementById("customGridPanel").style.padding = "";
        document.getElementById("customGridPanel").getElementsByTagName("fieldset")[0].style.width = "100%";
        $(".pmui-gridpanel-footer").css({"text-align": "center", "margin-top": "10px", "width": "120%"});
        $(".pmui-gridpanel-footer").removeClass("pmui-gridpanel-footer");
    }

    function clearAddOptionForm() {
        abeAddOption.getField("abe_custom_value_add").setValue("");
        abeAddOption.getField("abe_custom_label_add").setValue("");
        abeAddOption.getField("abe_custom_format_add").setValue("");
        abeAddOption.getField("buttonFieldCancel").setVisible(false);
        abeAddOption.getField("buttonFieldAdd").setValue("Add".translate());
        editRow = null;
    };
    function addAcceptedValue() {
        var abeValue = abeAddOption.getField("abe_custom_value_add").getValue(),
            abeLabel = abeAddOption.getField("abe_custom_label_add").getValue(),
            abeFormat = abeAddOption.getField("abe_custom_format_add").getValue();

        if (abeAddOption && !abeAddOption.isValid()) {
            return;
        }

        if (!evaluateTags(abeFormat, 'validate')) {
            return;
        }

        if (editRow === null) {
            customGrid.addItem(new PMUI.grid.GridPanelRow({
                data: {
                    abe_custom_value: abeValue,
                    abe_custom_label: abeLabel,
                    abe_custom_format: abeFormat
                }
            }));
        } else {
            editRow.setData({
                abe_custom_value: abeValue,
                abe_custom_label: abeLabel,
                abe_custom_format: abeFormat
            });
            editRow = null;
            abeAddOption.getField("buttonFieldCancel").setVisible(false);
            abeAddOption.getField("buttonFieldAdd").setValue("Add".translate());
        }
        abeAddOption.getField("abe_custom_value_add").setValue("");
        abeAddOption.getField("abe_custom_label_add").setValue("");
        abeAddOption.getField("abe_custom_format_add").setValue("");
    }

    function customGridRowEdit(row) {
        editRow = row;
        row = row.getData();
        if (abeAddOption) {
            abeAddOption.getField("abe_custom_value_add").setValue(row.abe_custom_value);
            abeAddOption.getField("abe_custom_label_add").setValue(row.abe_custom_label);
            abeAddOption.getField("abe_custom_format_add").setValue(row.abe_custom_format);
            abeAddOption.getField("buttonFieldCancel").setVisible(true);
            abeAddOption.getField("buttonFieldAdd").setValue("Save".translate());
        }
    }

    function evaluateTags(html, action) {
        var oldHtml;
        var tagBody = '(?:[^"\'>]|"[^"]*"|\'[^\']*\')*';
        var pattern = /^[a-zA-Z0-9\s\[\]\.\-_#%;,=:()']*$/;
        var tagOrComment = new RegExp(
            '<(?:'
                // Comment body.
            + '!--(?:(?:-*[^->])*--+|-?)'
                // Special "raw text" elements whose content should be elided.
            + '|script\\b' + tagBody + '>[\\s\\S]*?</script\\s*'
            + '|style\\b' + tagBody + '>[\\s\\S]*?</style\\s*'
                // Regular name
            + '|/?[a-z]'
            + tagBody
            + ')>',
            'gi');

        var action = (typeof action === 'undefined') ? 'delete' : 'validate';
        if (action == "validate") {
            return pattern.test(html);
        } else {
            do {
                oldHtml = html;
                html = html.replace(tagOrComment, '');
            } while (html !== oldHtml);
            return html.replace(/</g, '&lt;');
        }
    }
};
