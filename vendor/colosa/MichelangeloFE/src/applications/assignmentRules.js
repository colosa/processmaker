(function () {
    PMDesigner.assigmentRules = function (event) {
        var formAssignmentRules,
            i,
            tabPanelAssignmentRules,
            windowAssignmentRules,
            dataProperties,
            activity = event,
            pageSizeAssignment = 9,
            pageSizeAssignmentAD = 9,
            quickMessageWindow = new QuickMessageWindow(),
            arrayObjectUserList = [],
            arrayObjectUserList2 = [],
            arrayObjectUsers = [],
            arrayObjectUsers2 = [],
            arrayObjectAdhocUser = [],
            arrayObjectAdhocUser2 = [],
            arrayObjectAdhocUserList = [],
            arrayObjectAdhocUserList2 = [],
            warningMessageWindowDirty,
            restClient,
            assigmentPanelGlobal,
            assigmentPanelUser,
            panelTitleUser,
            titleUser,
            panelGridUser,
            buttonsUsers,
            gridUsers,
            assigmentPanelUserList,
            panelTitleUserList,
            titleUserList,
            panelGridUserList,
            buttonsUserList,
            gridUserList,
            assigmentPanelGlobalAD,
            assigmentPanelUserAD,
            panelTitleUserAD,
            titleUserAD,
            panelSearchUserAD,
            searchGridUserAD,
            panelGridUserAD,
            buttonsUsersAD,
            gridUsersAD,
            assigmentPanelUserListAD,
            panelTitleUseListAD,
            titleUserListAD,
            panelGridUserListAD,
            buttonsUserListAD,
            gridUserListAD,
            panelContainerRules,
            panelContainerUsers,
            panelContainerUsersAdHoc,
            loadTrigger,
            loadFormData,
            loadServerData,
            loadFreeUsers,
            loadAssignmentUsers,
            loadAdHocFreeUsers,
            loadAdHocAssignmentUsers,
            updateRules,
            allHidden,
            changeRadioButtons,
            visibleService,
            hiddenTab,
            saveData,
            saveOrUpdateUserAndAdHocUsers,
            applyStyles,
            applyStylesAD,
            assignee,
            remove,
            assigneeAD,
            removeAD,
            groupRows,
            flashMessage = new PMUI.ui.FlashMessage({
                message: '',
                appendTo: document.body,
                duration: 1000,
                severity: "success"
            });

        warningMessageWindowDirty = new PMUI.ui.MessageWindow({
            id: 'warningMessageWindowDirty',
            windowMessageType: 'warning',
            width: 490,
            bodyHeight: 'auto',
            title: 'Routing Rule'.translate(),
            message: 'Are you sure you want to discard your changes?'.translate(),
            footerItems: [{
                id: 'warningMessageWindowDirtyButtonNo',
                text: 'No'.translate(),
                visible: true,
                handler: function () {
                    warningMessageWindowDirty.close();
                },
                buttonType: "error"
            }, {
                id: 'warningMessageWindowDirtyButtonYes',
                text: 'Yes'.translate(),
                visible: true,
                handler: function () {
                    warningMessageWindowDirty.close();
                    windowAssignmentRules.close();
                },
                buttonType: "success"
            }]
        });
        formAssignmentRules = new PMUI.form.Form({
            id: 'formAssignmentRules',
            visibleHeader: false,
            buttonPanelPosition: 'top',
            width: 910,
            items: [
                new PMUI.field.RadioButtonGroupField({
                    id: 'formTasAssignType',
                    name: 'tas_assign_type',
                    label: 'Case assignment method'.translate(),
                    required: false,
                    controlPositioning: 'horizontal',
                    maxDirectionOptions: 3,
                    options: [{
                        id: 'formTasAssignTypeCyclical',
                        label: 'Cyclical Assignment'.translate(),
                        value: 'BALANCED',
                        selected: true
                    }, {
                        id: 'formTasAssignTypeManual',
                        label: 'Manual Assignment'.translate(),
                        value: 'MANUAL'
                    }, {
                        id: 'formTasAssignTypeValue',
                        label: 'Value Based Assignment'.translate(),
                        value: 'EVALUATE'
                    }, {
                        id: 'formTasAssignTypeReports',
                        label: 'Reports to'.translate(),
                        value: 'REPORT_TO'
                    }, {
                        id: 'formTasAssignTypeSelf',
                        label: 'Self Service'.translate(),
                        value: 'SELF_SERVICE'
                    }, {
                        id: 'formTasAssignTypeSelfValue',
                        label: 'Self Service Value Based Assignment'.translate(),
                        value: 'SELF_SERVICE_EVALUATE'
                    }, {
                        id: 'formTasAssignTypeParallel',
                        label: 'Parallel Assignment'.translate(),
                        value: 'MULTIPLE_INSTANCE'
                    }, {
                        id: 'formTasAssignTypeParallel',
                        label: 'Value Based Assignment'.translate(),
                        value: 'MULTIPLE_INSTANCE_VALUE_BASED'
                    }
                    ],
                    onChange: function (newVal, oldVal) {
                        changeRadioButtons(newVal);
                    },
                }), new CriteriaField({
                    id: 'formAssignmentRulesVariable',
                    pmType: 'text',
                    name: 'tas_assign_variable',
                    valueType: 'string',
                    label: 'Variable for Value Based Assignment'.translate(),
                    controlsWidth: DEFAULT_WINDOW_WIDTH - 527
                }), new CriteriaField({
                    id: 'formAssignmentRulesVariableSelf',
                    pmType: 'text',
                    name: 'tas_group_variable',
                    valueType: 'string',
                    label: 'Variable for Self Service Value Based Assignment'.translate(),
                    controlsWidth: DEFAULT_WINDOW_WIDTH - 527
                }), {
                    id: 'formAssignmentRulesSetTimeout',
                    pmType: 'checkbox',
                    name: 'tas_selfservice_timeout',
                    label: 'Set a timeout'.translate(),
                    options: [{
                        id: 'formAssignmentRulesSetTimeoutOption',
                        label: '',
                        value: '1'
                    }
                    ],
                    onChange: function (val) {
                        visibleService(this.controls[0].selected);
                    }
                }, {
                    id: 'formAssignmentRulesTime',
                    pmType: 'text',
                    name: 'tas_selfservice_time',
                    valueType: 'string',
                    label: 'Time'.translate(),
                    required: true,
                    validators: [{
                        pmType: "regexp",
                        criteria: /^[0-9]*$/,
                        errorMessage: "Please enter a numeric value".translate()
                    }]
                }, {
                    id: 'formAssignmentRulesTimeUnit',
                    pmType: 'dropdown',
                    name: 'tas_selfservice_time_unit',
                    label: 'Time unit'.translate(),
                    options: [{
                        id: 'formAssignmentRulesTimeUnitOption1',
                        label: 'Hours'.translate(),
                        value: 'HOURS'
                    }, {
                        id: 'formAssignmentRulesTimeUnitOption2',
                        label: 'Days'.translate(),
                        value: 'DAYS'
                    }, {
                        id: 'formAssignmentRulesTimeUnitOption3',
                        label: 'Minutes'.translate(),
                        value: 'MINUTES'
                    }]
                }, {
                    id: 'formAssignmentRulesTrigger',
                    pmType: 'dropdown',
                    name: 'tas_selfservice_trigger_uid',
                    label: 'Trigger to execute'.translate(),
                    required: true,
                    options: [{
                        id: 'formAssignmentRulesTriggerOption1',
                        value: '',
                        label: ''
                    }]
                }, {
                    id: 'formAssignmentRulesTriggerExecute',
                    pmType: 'dropdown',
                    name: 'tas_selfservice_execution',
                    label: 'Execute Trigger'.translate(),
                    options: [{
                        id: 'formAssignmentRulesTriggerExecute1',
                        label: 'Every time scheduled by cron'.translate(),
                        value: 'EVERY_TIME'
                    }, {
                        id: 'formAssignmentRulesTriggerExecute2',
                        label: 'Once'.translate(),
                        value: 'ONCE'
                    }]
                }
            ]
        });
        restClient = new PMRestClient({
            endpoint: 'projects',
            typeRequest: 'get',
            messageError: "There are problems, please try again.".translate(),
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });

        assigmentPanelGlobal = new PMUI.core.Panel({
            id: "assigmentPanelGlobal",
            layout: "hbox",
            width: DEFAULT_WINDOW_WIDTH
        });
        assigmentPanelUser = new PMUI.core.Panel({
            id: "assigmentPanelUser",
            layout: "vbox",
            width: "60%",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-white'
                ]
            }
        });
        panelTitleUser = new PMUI.core.Panel({
            id: "panelTitleUser",
            layout: "hbox"
        });
        titleUser = new PMUI.ui.TextLabel({
            id: "titleUser",
            label: " ",
            textMode: 'plain',
            text: 'Available users list'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelGridUser = new PMUI.core.Panel({
            id: "panelGridUser",
            layout: "hbox",
            style: {
                cssClasses: ["mafe-panel"]
            }
        });
        buttonsUsers = new PMUI.field.RadioButtonGroupField({
            id: "buttonsUsers",
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [{
                id: 'buttonAllAv',
                label: 'View all'.translate(),
                value: 'all',
                selected: true
            }, {
                id: 'buttonUsersAv',
                label: 'View users'.translate(),
                value: 'user'
            }, {
                id: 'buttonGroupsAv',
                label: 'View groups'.translate(),
                value: 'group'
            }],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "user" :
                        gridUsers.typeList = "user";
                        break;
                    case "group":
                        gridUsers.typeList = "group";
                        break;
                    default:
                        gridUsers.typeList = "";
                        break;
                }
                gridUsers.goToPage(0);
            }
        });
        gridUsers = new PMUI.grid.GridPanel({
            id: "gridUsers",
            pageSize: pageSizeAssignment - 1,
            edges: 2,
            behavior: 'dragdropsort',
            displayedPages: 2,
            filterable: true,
            filterPlaceholder: 'Search ...'.translate(),
            emptyMessage: 'No records found'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            tableContainerHeight: 242,
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].aas_type == "group") {
                        data[i]["available"] = data[i].aas_name;
                    } else {
                        data[i]["available"] = data[i].aas_name + " " + data[i].aas_lastname + " (" + data[i].aas_username + ")";
                    }
                }
                return data;
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/activity/' + activity.id + '/available-assignee/paged'
                }
            },
            columns: [{
                id: 'gridUsersButtonLabel',
                title: '',
                dataType: 'button',
                width: "10%",
                buttonLabel: function (row, data) {
                    return data.fullName;
                },
                buttonStyle: {
                    cssClasses: [
                        'mafe-grid-button'
                    ]
                },
                onButtonClick: function (row, grid) {
                    var option, select;
                    select = document.createElement("span");
                    select.id = "list-usersIngroup";
                    option = document.createElement("span");
                    option.id = "list-usersIngroup-iem"

                    if (row.getData()["aas_type"] === 'group') {
                        var restClient = new PMRestClient({
                            typeRequest: 'get',
                            functionSuccess: function (xhr, response) {
                                var optionClone, i;
                                for (i = 0; i < response.length; i += 1) {
                                    if (i == 10) {
                                        optionClone = option.cloneNode(false);
                                        optionClone.innerHTML = "<b style='float: right'>. . .<b>";
                                        select.appendChild(optionClone);
                                    } else {
                                        optionClone = option.cloneNode(false);
                                        optionClone.textContent = "- " + response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                        optionClone.title = response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                        select.appendChild(optionClone);
                                    }
                                }
                                if (!optionClone) {
                                    optionClone = option.cloneNode(false);
                                    optionClone.textContent = "No users".translate();
                                    select.appendChild(optionClone);
                                }

                                quickMessageWindow.show($(row.html).find('a')[0], select);
                            },
                            functionFailure: function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            },
                            messageError: 'There are problems saving the assigned user, please try again.'.translate()
                        });
                        restClient.setBaseEndPoint('group/' + row.getData()["aas_uid"] + '/users?start=0&limit=11');
                        restClient.executeRestClient();
                    }
                }
            },
                {
                    title: "",
                    dataType: 'string',
                    columnData: "available",
                    alignmentCell: "left",
                    width: "330px"
                },
                {
                    id: 'gridUsersButtonAssign',
                    title: '',
                    dataType: 'button',
                    width: "10%",
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-edit-assign'
                        ]
                    },
                    buttonLabel: function (row, data) {
                        var sw = row.getData()["aas_type"] === 'user';
                        row.getCells()[0].content.style.addClasses([sw ? 'button-icon-user' : 'button-icon-group']);
                        return '';
                    },
                    onButtonClick: function (row, grid) {
                        var dataRow = {};
                        grid = (grid != null) ? grid : gridUsers;
                        assignee(row);
                        gridUserList.goToPage(gridUserList.currentPage);
                        grid.goToPage(0);
                        gridUserList.goToPage(0);
                        flashMessage.setMessage("Assignee saved successfully".translate());
                        flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                        flashMessage.show();
                    }
                }],
            onDropOut: function (item, origin, destiny) {
            },
            onDrop: function (a, row) {
                grid = this;
                remove(row);
                gridUserList.goToPage(gridUserList.currentPage);
                grid.goToPage(grid.currentPage);
                flashMessage.setMessage("The user/group was successfully removed".translate());
                flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });
        assigmentPanelUserList = new PMUI.core.Panel({
            id: "assigmentPanelUserList",
            layout: "vbox",
            width: "60%",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-white'
                ]
            }
        });
        panelTitleUserList = new PMUI.core.Panel({
            id: "panelTitleUserList",
            layout: "hbox"
        });
        titleUserList = new PMUI.ui.TextLabel({
            id: "titleUserList",
            textMode: 'plain',
            text: 'Assigned users list'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelGridUserList = new PMUI.core.Panel({
            id: "panelGridUserList",
            layout: "hbox",
            style: {
                cssClasses: ["mafe-panel"]
            }
        });
        buttonsUserList = new PMUI.field.RadioButtonGroupField({
            id: "buttonsUserList",
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [{
                id: 'buttonAllAs',
                label: 'View all'.translate(),
                value: 'all',
                selected: true
            }, {
                id: 'buttonUsersAs',
                label: 'View users'.translate(),
                value: 'user'
            }, {
                id: 'buttonGroupsAs',
                label: 'View groups'.translate(),
                value: 'group'
            }],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "user" :
                        gridUserList.typeList = "user";
                        break;
                    case "group":
                        gridUserList.typeList = "group";
                        break;
                    default:
                        gridUserList.typeList = "";
                        break;
                }
                gridUserList.goToPage(0);
            }
        });
        gridUserList = new PMUI.grid.GridPanel({
            id: "gridUserList",
            pageSize: pageSizeAssignment - 1,
            edges: 2,
            displayedPages: 2,
            behavior: 'dragdropsort',
            filterable: true,
            nextLabel: 'Next'.translate(),
            filterPlaceholder: 'Search ...'.translate(),
            previousLabel: 'Previous'.translate(),
            tableContainerHeight: 242,
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            emptyMessage: function () {
                var div = document.createElement('div'),
                    span = document.createElement('span');
                div.appendChild(span);
                div.className = "mafe-grid-panel-empty";
                div.style.height = gridUserList.dom.tableContainer.style.height;
                div.style.width = gridUserList.dom.tableContainer.style.width;
                span.innerHTML = 'Drag & Drop a User or a Group here'.translate();
                return div;
            },
            onEmpty: function () {
                gridUserList.dom.tableContainer.style.overflow = "hidden";
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/activity/' + activity.id + '/assignee/paged'
                }
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].aas_type == "group") {
                        data[i]["assignment"] = data[i].aas_name;
                    } else {
                        data[i]["assignment"] = data[i].aas_name + " " + data[i].aas_lastname + " (" + data[i].aas_username + ")";
                    }
                }
                return data;
            },
            columns: [
                {
                    id: 'gridUserListButtonLabel',
                    title: "",
                    width: "10%",
                    dataType: 'button',
                    buttonLabel: function (row, data) {
                        return data.lastName;
                    },
                    buttonStyle: {
                        cssClasses: [
                            'mafe-grid-button'
                        ]
                    },
                    onButtonClick: function (row, grid) {
                        var option, select;
                        select = document.createElement("span");
                        select.id = "list-usersIngroup";
                        option = document.createElement("span");
                        option.id = "list-usersIngroup-iem";

                        if (row.getData()["aas_type"] === "group") {
                            var restClient = new PMRestClient({
                                typeRequest: 'get',
                                functionSuccess: function (xhr, response) {
                                    var optionClone, i;
                                    for (i = 0; i < response.length; i += 1) {
                                        if (i == 10) {
                                            optionClone = option.cloneNode(false);
                                            optionClone.innerHTML = "<b style='float: right'>. . .<b>";
                                            select.appendChild(optionClone);
                                        } else {
                                            optionClone = option.cloneNode(false);
                                            optionClone.textContent = "- " + response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                            optionClone.title = response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                            select.appendChild(optionClone);
                                        }
                                    }
                                    if (!optionClone) {
                                        optionClone = option.cloneNode(false);
                                        optionClone.textContent = "No users".translate();
                                        select.appendChild(optionClone);
                                    }

                                    quickMessageWindow.show($(row.html).find('a')[0], select);
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: 'There are problems saving the assigned user, please try again.'.translate()
                            });
                            restClient.setBaseEndPoint("group/" + row.getData()["aas_uid"] + "/users?start=0&limit=11");
                            restClient.executeRestClient();
                        }
                    }
                },
                {
                    title: '',
                    dataType: 'string',
                    columnData: "assignment",
                    alignmentCell: "left",
                    width: "330px"
                },
                {
                    id: 'gridUserListButtonDelete',
                    title: '',
                    dataType: 'button',
                    width: "10%",
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-delete-assign'
                        ]
                    },
                    buttonLabel: function (row, data) {
                        var sw = row.getData()["aas_type"] === 'user';
                        row.getCells()[0].content.style.addClasses([sw ? 'button-icon-user' : 'button-icon-group']);
                        return '';
                    },
                    onButtonClick: function (row, grid) {
                        var dataRow = {};
                        grid = (grid != null) ? grid : gridUserList;
                        remove(row);
                        gridUserList.goToPage(gridUserList.currentPage);
                        grid.goToPage(0);
                        gridUsers.goToPage(0);
                        flashMessage.setMessage("The user/group was successfully removed".translate());
                        flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                        flashMessage.show();
                    }
                }
            ],
            onDropOut: function (item, origin, destiny) {
            },
            onDrop: function (grid, row) {
                var dataRow = {};
                grid = this;
                assignee(row);
                gridUsers.goToPage(gridUsers.currentPage);
                grid.goToPage(grid.currentPage);
                flashMessage.setMessage("Assignee saved successfully".translate());
                flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });

        assigmentPanelGlobalAD = new PMUI.core.Panel({
            id: "assigmentPanelGlobalAD",
            layout: "hbox",
            width: DEFAULT_WINDOW_WIDTH

        });
        assigmentPanelUserAD = new PMUI.core.Panel({
            id: "assigmentPanelUserAD",
            layout: "vbox",
            width: "60%",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-white'
                ]
            }
        });
        panelTitleUserAD = new PMUI.core.Panel({
            id: "panelTitleUserAD",
            layout: "hbox"
        });
        titleUserAD = new PMUI.ui.TextLabel({
            id: "titleUserAD",
            label: " ",
            textMode: 'plain',
            text: 'Available users list'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelSearchUserAD = new PMUI.core.Panel({
            id: "panelSearchUserAD",
            layout: "hbox"
        });
        searchGridUserAD = new PMUI.field.TextField({
            id: "searchGridUserAD",
            label: " ",
            placeholder: 'Search ...'.translate(),
            style: {
                cssClasses: [
                    'mafe-assigment-search'
                ]
            }
        });
        panelGridUserAD = new PMUI.core.Panel({
            id: "panelGridUserAD",
            layout: "hbox",
            style: {
                cssClasses: ["mafe-panel"]
            }
        });
        buttonsUsersAD = new PMUI.field.RadioButtonGroupField({
            id: "buttonsUsersAD",
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [{
                id: 'buttonAllAv',
                label: 'View all'.translate(),
                selected: true,
                value: 'all'
            }, {
                id: 'buttonUsersAv',
                label: 'View users'.translate(),
                value: 'user'
            }, {
                id: 'buttonGroupsAv',
                label: 'View groups'.translate(),
                value: 'group'
            }],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "user" :
                        gridUsersAD.typeList = "user";
                        break;
                    case "group":
                        gridUsersAD.typeList = "group";
                        break;
                    default:
                        gridUsersAD.typeList = "";
                        break;
                }
                gridUsersAD.goToPage(0);
            }
        });
        gridUsersAD = new PMUI.grid.GridPanel({
            id: "gridUsersAD",
            pageSize: pageSizeAssignmentAD - 1,
            filterable: true,
            behavior: 'dragdropsort',
            filterPlaceholder: 'Search ...'.translate(),
            emptyMessage: 'No records found'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            tableContainerHeight: 242,
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/activity/' + activity.id + '/adhoc-available-assignee/paged'
                }
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].ada_type == "group") {
                        data[i]["available"] = data[i].ada_name;
                    } else {
                        data[i]["available"] = data[i].ada_name + " " + data[i].ada_lastname + " (" + data[i].ada_username + ")";
                    }
                }
                return data;
            },
            columns: [
                {
                    id: 'gridUsersADButtonLabel',
                    title: '',
                    dataType: 'button',
                    width: "10%",
                    buttonLabel: function (row, data) {
                        return data.lastName;
                    },
                    buttonStyle: {
                        cssClasses: [
                            'mafe-grid-button'
                        ]
                    },
                    onButtonClick: function (row, grid) {
                        var option, select;
                        select = document.createElement("span");
                        select.id = "list-usersIngroup";
                        option = document.createElement("span");
                        option.id = "list-usersIngroup-iem";
                        if (row.getData()["ada_type"] === "group") {
                            var restClient = new PMRestClient({
                                typeRequest: 'get',
                                functionSuccess: function (xhr, response) {
                                    var optionClone, i;
                                    for (i = 0; i < response.length; i += 1) {
                                        if (i == 10) {
                                            optionClone = option.cloneNode(false);
                                            optionClone.innerHTML = "<b style='float: right'>. . .<b>";
                                            select.appendChild(optionClone);
                                        } else {
                                            optionClone = option.cloneNode(false);
                                            optionClone.textContent = "- " + response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                            optionClone.title = response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                            select.appendChild(optionClone);
                                        }
                                    }
                                    if (!optionClone) {
                                        optionClone = option.cloneNode(false);
                                        optionClone.textContent = "No users".translate();
                                        select.appendChild(optionClone);
                                    }

                                    quickMessageWindow.show($(row.html).find('a')[0], select);
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: 'There are problems saving the assigned user, please try again.'.translate()
                            });
                            restClient.setBaseEndPoint("group/" + row.getData()["ada_uid"] + "/users?start=0&limit=11");
                            restClient.executeRestClient();
                        }
                    }
                },
                {
                    title: "",
                    dataType: 'string',
                    columnData: "available",
                    alignmentCell: "left",
                    width: "330px"
                },
                {
                    id: 'gridUsersADButtonAssign',
                    title: '',
                    dataType: 'button',
                    width: "10%",
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-edit-assign'
                        ]
                    },
                    buttonLabel: function (row, data) {
                        var sw = row.getData()["ada_type"] === 'user';
                        row.getCells()[0].content.style.addClasses([sw ? 'button-icon-user' : 'button-icon-group']);
                        return '';
                    },
                    onButtonClick: function (row, grid) {
                        var dataRow = {};
                        grid = (grid != null) ? grid : gridUsersAD;
                        assigneeAD(row);
                        grid.goToPage(gridUsersAD.currentPage);
                        gridUserListAD.goToPage(0);
                        flashMessage.setMessage("Assignee saved successfully".translate());
                        flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                        flashMessage.show();
                    }
                }
            ],
            onDropOut: function (item, origin, destiny) {
                formAssignmentRules.dirty = true;
                grid = gridUsersAD;
                grid.sort('fullName', 'asc');
                buttonsUserListAD.setValue(buttonsUsersAD.getValue());
                groupRows(gridUsersAD, 'all');
                groupRows(gridUserListAD, 'all');
                arrayObjectAdhocUserList.push(item);
                index = arrayObjectAdhocUser.indexOf(item);
                if (index > -1) {
                    arrayObjectAdhocUser.splice(index, 1);
                }
            },
            onDrop: function (a, row) {
                grid = this;
                removeAD(row);
                gridUserListAD.goToPage(gridUserListAD.currentPage);
                grid.goToPage(grid.currentPage);
                flashMessage.setMessage("The user/group was successfully removed".translate());
                flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });
        assigmentPanelUserListAD = new PMUI.core.Panel({
            id: "assigmentPanelUserListAD",
            layout: "vbox",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-white'
                ]
            },
            width: "60%"
        });
        panelTitleUseListAD = new PMUI.core.Panel({
            id: "panelTitleUseListAD",
            layout: "hbox"
        });
        titleUserListAD = new PMUI.ui.TextLabel({
            id: "titleUserListAD",
            textMode: 'plain',
            text: 'Assigned users list'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelGridUserListAD = new PMUI.core.Panel({
            id: "panelGridUserListAD",
            layout: "hbox",
            style: {
                cssClasses: ["mafe-panel"]
            }
        });
        buttonsUserListAD = new PMUI.field.RadioButtonGroupField({
            id: "buttonsUserListAD",
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [{
                id: 'buttonAllAs',
                label: 'View all'.translate(),
                value: 'all',
                selected: true
            }, {
                id: 'buttonUsersAs',
                label: 'View users'.translate(),
                value: 'user'
            }, {
                id: 'buttonGroupsAs',
                label: 'View groups'.translate(),
                value: 'group'
            }],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "user" :
                        gridUserListAD.typeList = "user";
                        break;
                    case "group":
                        gridUserListAD.typeList = "group";
                        break;
                    default:
                        gridUserListAD.typeList = "";
                        break;
                }
                gridUserListAD.goToPage(0);
            }
        });
        gridUserListAD = new PMUI.grid.GridPanel({
            id: "gridUserListAD",
            pageSize: pageSizeAssignmentAD - 1,
            behavior: 'dragdropsort',
            filterable: true,
            filterPlaceholder: 'Search ...'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            tableContainerHeight: 242,
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            emptyMessage: function () {
                var div = document.createElement('div'),
                    span = document.createElement('span');
                div.appendChild(span);
                div.className = "mafe-grid-panel-empty";
                div.style.height = gridUserListAD.dom.tableContainer.style.height;
                div.style.width = gridUserListAD.dom.tableContainer.style.width;
                span.innerHTML = 'Drag & Drop a User or a Group here'.translate();
                return div;
            },
            onEmpty: function (grid, cell) {
                gridUserListAD.dom.tableContainer.style.overflow = "hidden";
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/activity/' + activity.id + '/adhoc-assignee/paged'
                }
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].ada_type == "group") {
                        data[i]["assignee"] = data[i].ada_name;
                    } else {
                        data[i]["assignee"] = data[i].ada_name + " " + data[i].ada_lastname + " (" + data[i].ada_username + ")";
                    }
                }
                return data;
            },
            columns: [
                {
                    id: 'gridUserListADButtonLabel',
                    title: "",
                    width: "13%",
                    dataType: 'button',
                    buttonLabel: function (row, data) {
                        return data.lastName;
                    },
                    buttonStyle: {
                        cssClasses: [
                            'mafe-grid-button'
                        ]
                    },
                    onButtonClick: function (row, grid) {
                        var option, select;
                        select = document.createElement("span");
                        select.id = "list-usersIngroup";
                        option = document.createElement("span");
                        option.id = "list-usersIngroup-iem";
                        if (row.getData()["ada_type"] === "group") {
                            var i,
                                restClient = new PMRestClient({
                                    typeRequest: 'get',
                                    functionSuccess: function (xhr, response) {
                                        var optionClone, i;
                                        for (i = 0; i < response.length; i += 1) {
                                            if (i == 10) {
                                                optionClone = option.cloneNode(false);
                                                optionClone.innerHTML = "<b style='float: right'>. . .<b>";
                                                select.appendChild(optionClone);
                                            } else {
                                                optionClone = option.cloneNode(false);
                                                optionClone.textContent = "- " + response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                                optionClone.title = response[i].usr_firstname + ' ' + response[i].usr_lastname;
                                                select.appendChild(optionClone);
                                            }
                                        }
                                        if (!optionClone) {
                                            optionClone = option.cloneNode(false);
                                            optionClone.textContent = "No users".translate();
                                            select.appendChild(optionClone);
                                        }

                                        quickMessageWindow.show($(row.html).find('a')[0], select);
                                    },
                                    functionFailure: function (xhr, response) {
                                        PMDesigner.msgWinError(response.error.message);
                                    },
                                    messageError: 'There are problems saving the assigned user, please try again.'.translate()
                                });
                            restClient.setBaseEndPoint("group/" + row.getData()["ada_uid"] + "/users?start=0&limit=11");
                            restClient.executeRestClient();
                        }
                    }
                },
                {
                    title: '',
                    dataType: 'string',
                    columnData: "assignee",
                    alignmentCell: "left",
                    width: "330px"
                },
                {
                    id: 'gridUserListADButtonDelete',
                    title: '',
                    dataType: 'button',
                    width: "10%",
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-delete-assign'
                        ]
                    },
                    buttonLabel: function (row, data) {
                        var sw = row.getData()["ada_type"] === 'user';
                        row.getCells()[0].content.style.addClasses([sw ? 'button-icon-user' : 'button-icon-group']);
                        return '';
                    },
                    onButtonClick: function (row, grid) {
                        var dataRow = {};
                        grid = (grid != null) ? grid : gridUserListAD;
                        removeAD(row);
                        grid.goToPage(grid.currentPage);
                        gridUsersAD.goToPage(gridUsersAD.currentPage);
                        flashMessage.setMessage("The user/group was successfully removed".translate());
                        flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                        flashMessage.show();
                    }
                }
            ],
            onDropOut: function (item, origin, destiny) {
            },
            onDrop: function (a, row) {
                grid = this;
                assigneeAD(row);
                gridUsersAD.goToPage(gridUsersAD.currentPage);
                grid.goToPage(grid.currentPage);
                flashMessage.setMessage("Assignee saved successfully".translate());
                flashMessage.setAppendTo(windowAssignmentRules.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });

        panelContainerRules = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH - 55,
            height: "auto",
            fieldset: true,
            items: [
                formAssignmentRules
            ]
        });
        panelContainerUsers = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH,
            height: "auto",
            fieldset: true,
            items: [
                assigmentPanelGlobal
            ]
        });
        panelContainerUsersAdHoc = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH,
            height: "auto",
            fieldset: true,
            items: [
                assigmentPanelGlobalAD
            ]
        });

        tabPanelAssignmentRules = new PMUI.panel.TabPanel({
            id: 'tabPanelAssignmentRules',
            width: "100%",
            height: "auto",
            items: [
                {
                    id: 'tabUsers',
                    title: 'Users'.translate(),
                    panel: panelContainerUsers
                },
                {
                    id: 'tabUsersAdHoc',
                    title: 'Ad Hoc Users'.translate(),
                    panel: panelContainerUsersAdHoc
                }
            ],
            onTabClick: function (item) {
                quickMessageWindow.close();
                switch (item.id) {
                    case 'tabRules':
                        break;
                    case 'tabUsers':
                        applyStyles();
                        gridUsers.goToPage(0);
                        gridUserList.goToPage(0);
                        break;
                    case 'tabUsersAdHoc':
                        applyStylesAD();
                        gridUsersAD.goToPage(0);
                        gridUserListAD.goToPage(0);
                        break;
                }
            }
        });
        windowAssignmentRules = new PMUI.ui.Window({
            id: 'windowAssignmentRules',
            title: 'Assignment Rules'.translate() + ': ' + activity.act_name,
            height: DEFAULT_WINDOW_HEIGHT,
            width: DEFAULT_WINDOW_WIDTH,
            footerItems: [
                {
                    id: 'windowConnectionsButtonCancel',
                    text: 'Close'.translate(),
                    handler: function () {
                        if (formAssignmentRules.isDirty()) {
                            warningMessageWindowDirty.open();
                            warningMessageWindowDirty.showFooter();
                        } else {
                            windowAssignmentRules.close();
                        }
                    },
                    buttonType: 'error'
                },
                {
                    buttonType: 'success',
                    id: 'windowPropertiesButtonSave',
                    text: "Save".translate(),
                    handler: function () {
                        saveData();
                    }
                }
            ],
            visibleFooter: true,
            buttonPanelPosition: 'bottom',
            footerAlign: "right",
            onBeforeClose: function () {
                if (formAssignmentRules.isDirty()) {
                    warningMessageWindowDirty.open();
                    warningMessageWindowDirty.showFooter();
                } else {
                    windowAssignmentRules.close();
                }
            }
        });

        loadTrigger = function (response) {
            var field = formAssignmentRules.getField('tas_selfservice_trigger_uid'), i;
            field.clearOptions();
            field.addOption({
                value: '',
                label: '- Select Trigger -'.translate()
            });
            for (i = 0; i < response.length; i += 1) {
                field.addOption({
                    value: response[i].tri_uid,
                    label: response[i].tri_title
                });
            }
        };
        loadFormData = function (response) {
            dataProperties = response.properties;
            formAssignmentRules.getField('tas_assign_type').setValue(dataProperties.tas_assign_type);
            formAssignmentRules.getField('tas_assign_variable').setValue(dataProperties.tas_assign_variable);
            formAssignmentRules.getField('tas_group_variable').setValue(dataProperties.tas_group_variable);
            changeRadioButtons(formAssignmentRules.getField('tas_assign_type').getValue());
            formAssignmentRules.getField('tas_selfservice_timeout').controls[0].deselect();
            if (dataProperties.tas_selfservice_timeout === 1) {
                formAssignmentRules.getField('tas_selfservice_timeout').controls[0].select();
            }
            formAssignmentRules.getField('tas_selfservice_time').setValue(dataProperties.tas_selfservice_time);
            formAssignmentRules.getField('tas_selfservice_time_unit').setValue(dataProperties.tas_selfservice_time_unit);
            formAssignmentRules.getField('tas_selfservice_trigger_uid').setValue(dataProperties.tas_selfservice_trigger_uid);
            formAssignmentRules.getField('tas_selfservice_execution').setValue(dataProperties.tas_selfservice_execution);
            visibleService(dataProperties.tas_selfservice_timeout === 1);
        };
        loadServerData = function () {
            var restClient = new PMRestClient({
                typeRequest: 'post',
                multipart: true,
                data: {
                    calls: [
                        {
                            url: 'project/' + PMDesigner.project.id + '/activity/' + activity.id,
                            method: 'GET'
                        }, {
                            url: 'project/' + PMDesigner.project.id + '/triggers',
                            method: 'GET'
                        }
                    ]
                },
                functionSuccess: function (xhr, response) {
                    loadTrigger(response[1].response);
                    loadFormData(response[0].response);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.setBaseEndPoint('');
            restClient.executeRestClient();
        };
        loadFreeUsers = function (response) {
            var i;
            gridUsers.clearItems();
            for (i = 0; i < response.length; i += 1) {
                gridUsers.addDataItem({
                    fullName: response[i].aas_name + " " + response[i].aas_lastname,
                    ass_uid: response[i].aas_uid,
                    ass_type: response[i].aas_type
                });
            }
        };
        loadAssignmentUsers = function (response) {
            var i;
            gridUserList.clearItems();
            for (i = 0; i < response.length; i += 1) {
                gridUserList.addDataItem({
                    fullName: response[i].aas_name + " " + response[i].aas_lastname,
                    ass_uid: response[i].aas_uid,
                    ass_type: response[i].aas_type
                });
            }
        };
        loadAdHocFreeUsers = function (response) {
            var i;
            gridUsersAD.clearItems();
            for (i = 0; i < response.length; i += 1) {
                gridUsersAD.addDataItem({
                    fullName: response[i].ada_name + " " + response[i].ada_lastname,
                    ada_uid: response[i].ada_uid,
                    ass_type: response[i].ada_type
                });
            }
        };
        loadAdHocAssignmentUsers = function (response) {
            var i;
            gridUserListAD.clearItems();
            for (i = 0; i < response.length; i += 1) {
                gridUserListAD.addDataItem({
                    fullName: response[i].ada_name + " " + response[i].ada_lastname,
                    ada_uid: response[i].ada_uid,
                    ass_type: response[i].ada_type
                });
            }
        };
        updateRules = function () {
            (new PMRestClient({
                endpoint: 'activity/' + activity.id,
                typeRequest: 'update',
                messageError: ' ',
                data: {
                    definition: {},
                    properties: dataProperties
                },
                messageSuccess: 'Assignment Rules saved successfully'.translate(),
                flashContainer: document.body,
                functionSuccess: function () {
                    formAssignmentRules.dirty = false;
                    windowAssignmentRules.close();
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            })).executeRestClient();
        };
        allHidden = function () {
            formAssignmentRules.getField('tas_assign_variable').setVisible(false);
            formAssignmentRules.getField('tas_group_variable').setVisible(false);
            formAssignmentRules.getField('tas_selfservice_timeout').setVisible(false);
            formAssignmentRules.getField('tas_selfservice_timeout').controls[0].deselect();
            formAssignmentRules.getField('tas_selfservice_time').setVisible(false);
            formAssignmentRules.getField('tas_selfservice_time_unit').setVisible(false);
            formAssignmentRules.getField('tas_selfservice_trigger_uid').setVisible(false);
            formAssignmentRules.getField('tas_selfservice_execution').setVisible(false);
        };
        changeRadioButtons = function (newVal) {
            allHidden();
            tabPanelAssignmentRules.setVisible(true);
            switch (newVal) {
                case 'EVALUATE':
                    formAssignmentRules.getField('tas_assign_variable').setVisible(true);
                    if (dataProperties.tas_assign_variable === "" || dataProperties.tas_assign_variable === null) {
                        formAssignmentRules.getField('tas_assign_variable').setValue('@@SYS_NEXT_USER_TO_BE_ASSIGNED');
                    } else {
                        formAssignmentRules.getField('tas_assign_variable').setValue(dataProperties.tas_assign_variable);
                    }
                    break;
                case 'SELF_SERVICE':
                    formAssignmentRules.getField('tas_selfservice_timeout').setVisible(true);
                    break;
                case 'SELF_SERVICE_EVALUATE':
                    formAssignmentRules.getField('tas_group_variable').setVisible(true);
                    if (dataProperties.tas_group_variable === "@@SYS_GROUP_TO_BE_ASSIGNED" || dataProperties.tas_group_variable === "" || dataProperties.tas_group_variable === null) {
                        formAssignmentRules.getField('tas_group_variable').setPlaceholder('@@ARRAY_OF_USERS or @@GROUP_UID');
                    } else {
                        formAssignmentRules.getField('tas_group_variable').setValue(dataProperties.tas_group_variable);
                    }
                    formAssignmentRules.getField('tas_selfservice_timeout').setVisible(true);
                    break;
                case 'REPORT_TO':
                    break;
                case 'MULTIPLE_INSTANCE_VALUE_BASED':
                    formAssignmentRules.getField('tas_assign_variable').setLabel("Array of users".translate());
                    if (formAssignmentRules.getField('tas_assign_variable').getValue() == "@@SYS_NEXT_USER_TO_BE_ASSIGNED" || formAssignmentRules.getField('tas_assign_variable').getValue() == "") {
                        formAssignmentRules.getField('tas_assign_variable').setValue('');
                        formAssignmentRules.getField('tas_assign_variable').setPlaceholder('@@ARRAY_OF_USERS');
                    }
                    formAssignmentRules.getField('tas_assign_variable').setVisible(true);
                    if (activity.act_loop_type == "PARALLEL") {
                        tabPanelAssignmentRules.setVisible(false);
                    }
                    break;
                default:
                    break;
            }
        };
        visibleService = function (value) {
            var a = formAssignmentRules.getField('tas_assign_type').getValue();
            if (a === 'SELF_SERVICE' || a === 'SELF_SERVICE_EVALUATE') {
                formAssignmentRules.getField('tas_selfservice_time').setVisible(value);
                formAssignmentRules.getField('tas_selfservice_time_unit').setVisible(value);
                formAssignmentRules.getField('tas_selfservice_trigger_uid').setVisible(value);
                formAssignmentRules.getField('tas_selfservice_execution').setVisible(value);
            }
        };
        hiddenTab = function (value) {
            tabPanelAssignmentRules.showTab(2);
        };
        saveData = function () {
            var a, b, c, d, tas_selfservice_timeout, data;
            tas_selfservice_timeout = formAssignmentRules.getField('tas_selfservice_timeout').getValue() === '["1"]';
            if (!tas_selfservice_timeout) {
                //validation because getData method do not work in IE
                if (navigator.userAgent.indexOf("MSIE") !== -1 || navigator.userAgent.indexOf("Trident") !== -1) {
                    data = getData2PMUI(formAssignmentRules.html);
                    b = data["tas_assign_variable"];
                } else {
                    b = formAssignmentRules.getField('tas_assign_variable').getValue();
                }
                a = formAssignmentRules.getField('tas_assign_type').getValue();
                c = formAssignmentRules.getField('tas_group_variable').getValue();
                d = formAssignmentRules.getField('tas_group_variable').getValue();
                formAssignmentRules.getField('tas_assign_type').setValue(a);
                formAssignmentRules.getField('tas_assign_variable').setValue(b);
                formAssignmentRules.getField('tas_group_variable').setValue(c);
                formAssignmentRules.getField('tas_selfservice_timeout').setValue(d);
                formAssignmentRules.getField('tas_selfservice_time').setValue('');
                formAssignmentRules.getField('tas_selfservice_time_unit').setValue('');
                formAssignmentRules.getField('tas_selfservice_trigger_uid').setValue('');
                formAssignmentRules.getField('tas_selfservice_execution').setValue('');
            } else {
                if (!formAssignmentRules.isValid()) {
                    return;
                }
            }
            dataProperties.tas_assign_type = formAssignmentRules.getField('tas_assign_type').getValue();
            dataProperties.tas_assign_variable = formAssignmentRules.getField('tas_assign_variable').getValue() === '' ? '@@SYS_NEXT_USER_TO_BE_ASSIGNED' : formAssignmentRules.getField('tas_assign_variable').getValue();
            dataProperties.tas_group_variable = formAssignmentRules.getField('tas_group_variable').getValue() === '' ? '@@SYS_GROUP_TO_BE_ASSIGNED' : formAssignmentRules.getField('tas_group_variable').getValue();
            dataProperties.tas_selfservice_timeout = tas_selfservice_timeout ? 1 : 0;
            dataProperties.tas_selfservice_time = formAssignmentRules.getField('tas_selfservice_time').getValue() !== "" ? parseInt(formAssignmentRules.getField('tas_selfservice_time').getValue(), 10) : 0;
            dataProperties.tas_selfservice_time_unit = formAssignmentRules.getField('tas_selfservice_time_unit').getValue();
            dataProperties.tas_selfservice_trigger_uid = formAssignmentRules.getField('tas_selfservice_trigger_uid').getValue();
            dataProperties.tas_selfservice_execution = formAssignmentRules.getField('tas_selfservice_execution').getValue();
            updateRules();
        };
        saveOrUpdateUserAndAdHocUsers = function () {
            //Assigne and Remove (users)
            var i, b;
            if (gridUserList.getItems().length > 0) {
                grid = gridUserList;
                for (i = 0; i < arrayObjectUserList.length; i += 1) {
                    b = arrayObjectUserList[i];
                    if (arrayObjectUserList2.indexOf(b) == -1) {
                        assignee(b);
                    }
                }
            }
            if (gridUsers.getItems().length > 0) {
                for (i = 0; i < arrayObjectUsers.length; i += 1) {
                    b = arrayObjectUsers[i];
                    if (arrayObjectUsers2.indexOf(b) == -1) {
                        remove(arrayObjectUsers[i]);
                    }
                }
            }
            //Assigne and Remove (AdHocUsers)
            if (gridUserListAD.getItems().length > 0) {
                grid = gridUserListAD;
                for (i = 0; i < arrayObjectAdhocUserList.length; i += 1) {
                    b = arrayObjectAdhocUserList[i];
                    if (arrayObjectAdhocUserList2.indexOf(b) == -1) {
                        assigneeAD(b);
                    }
                }
            }
            if (gridUsersAD.getItems().length > 0) {
                for (i = 0; i < arrayObjectAdhocUser.length; i += 1) {
                    b = arrayObjectAdhocUser[i];
                    if (arrayObjectAdhocUser2.indexOf(b) == -1) {
                        removeAD(arrayObjectAdhocUser[i]);
                    }
                }
            }
        };
        applyStyles = function () {
            gridUsers.dom.toolbar.appendChild(buttonsUsers.getHTML());
            buttonsUsers.defineEvents();
            gridUsers.dom.toolbar.style.height = "76px";
            gridUserList.dom.toolbar.appendChild(buttonsUserList.getHTML());
            buttonsUserList.defineEvents();
            gridUserList.dom.toolbar.style.height = "76px";
            buttonsUsers.dom.labelTextContainer.style.display = "none";
            buttonsUserList.dom.labelTextContainer.style.display = "none";
            gridUsers.hideHeaders();
            gridUserList.hideHeaders();
            assigmentPanelUserList.setHeight('100%');
            gridUsers.filterControl.html.style.width = "300px";
            gridUserList.filterControl.html.style.width = "300px";
        };
        applyStylesAD = function () {
            gridUsersAD.dom.toolbar.appendChild(buttonsUsersAD.getHTML());
            buttonsUsersAD.defineEvents();
            gridUsersAD.dom.toolbar.style.height = "76px";

            gridUserListAD.dom.toolbar.appendChild(buttonsUserListAD.getHTML());
            buttonsUserListAD.defineEvents();
            gridUserListAD.dom.toolbar.style.height = "76px";
            buttonsUsersAD.dom.labelTextContainer.style.display = "none";
            buttonsUserListAD.dom.labelTextContainer.style.display = "none";
            gridUsersAD.hideHeaders();
            gridUserListAD.hideHeaders();
            assigmentPanelUserListAD.setHeight('100%');
            gridUsersAD.filterControl.html.style.width = "300px";
            gridUserListAD.filterControl.html.style.width = "300px";
        };
        assignee = function (row) {
            restClient.setTypeRequest("post");
            restClient.setEndpoint("activity/" + activity.id + "/assignee");
            restClient.setData({aas_uid: row.getData()["aas_uid"], aas_type: row.getData()["aas_type"]});
            restClient.functionSuccess = function (xhr, response) {
            };
            restClient.executeRestClient();
        };
        remove = function (row) {
            restClient.setTypeRequest("remove");
            restClient.setEndpoint("activity/" + activity.id + "/assignee/" + row.getData().aas_uid);
            restClient.functionSuccess = function (xhr, response) {
            };
            restClient.executeRestClient();
        };
        assigneeAD = function (row) {
            restClient.setTypeRequest("post");
            restClient.setEndpoint("activity/" + activity.id + "/adhoc-assignee");
            restClient.setData({ada_uid: row.getData()["ada_uid"], ada_type: row.getData()["ada_type"]});
            restClient.functionSuccess = function (xhr, response) {
            };
            restClient.executeRestClient();
        };
        removeAD = function (row) {
            restClient.setTypeRequest("remove");
            restClient.setEndpoint("activity/" + activity.id + "/adhoc-assignee/" + row.getData()["ada_uid"]);
            restClient.functionSuccess = function (xhr, response) {
            };
            restClient.executeRestClient();
        };
        groupRows = function (grid, value) {
            var i, items;
            if (grid.memorystack === undefined) {
                grid.memorystack = [];
            }
            items = grid.getItems();
            while (grid.memorystack.length > 0) {
                grid.addItem(grid.memorystack.pop());
            }
            if (value !== 'all') {
                for (i = 0; i < items.length; i += 1) {
                    if (items[i].getData().ass_type !== value) {
                        grid.memorystack.push(items[i]);
                        grid.removeItem(items[i]);
                    }
                }
            }
            grid.sort('fullName', 'asc');
        };

        function onchangeRadio(grid, fieldName) {
            var radioButTrat = document.getElementsByName(fieldName), i;
            for (i = 0; i < radioButTrat.length; i += 1) {
                if (radioButTrat[i].checked == true) {
                    quickMessageWindow.close();
                    groupRows(grid, radioButTrat[i].value);
                }
            }
        }

        function domSettings() {
            if (activity.act_loop_type == "PARALLEL") {
                $(formAssignmentRules.getField("tas_assign_type").controls[0].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[1].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[2].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[3].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[4].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[5].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[6].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[7].html).parent().show();
                if (formAssignmentRules.getField('tas_assign_type').getValue() == "MULTIPLE_INSTANCE_VALUE_BASED") {
                    formAssignmentRules.getField('tas_assign_type').setValue("MULTIPLE_INSTANCE_VALUE_BASED");
                } else {
                    formAssignmentRules.getField('tas_assign_type').setValue("MULTIPLE_INSTANCE");
                }
            } else {
                $(formAssignmentRules.getField("tas_assign_type").controls[0].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[1].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[2].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[3].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[4].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[5].html).parent().show();
                $(formAssignmentRules.getField("tas_assign_type").controls[6].html).parent().hide();
                $(formAssignmentRules.getField("tas_assign_type").controls[7].html).parent().hide();
                if (formAssignmentRules.getField('tas_assign_type').getValue() == "MULTIPLE_INSTANCE_VALUE_BASED") {
                    formAssignmentRules.getField('tas_assign_type').setValue("BALANCED");
                    formAssignmentRules.getField('tas_assign_variable').setVisible(false);
                }
            }
        };

        panelTitleUser.addItem(titleUser);
        panelGridUser.addItem(gridUsers);
        assigmentPanelUser.addItem(panelTitleUser);
        assigmentPanelUser.addItem(panelGridUser);
        assigmentPanelGlobal.addItem(assigmentPanelUser);
        panelTitleUserList.addItem(titleUserList);
        panelGridUserList.addItem(gridUserList);
        assigmentPanelUserList.addItem(panelTitleUserList);
        assigmentPanelUserList.addItem(panelGridUserList);
        assigmentPanelGlobal.addItem(assigmentPanelUserList);

        panelTitleUserAD.addItem(titleUserAD);
        panelSearchUserAD.addItem(searchGridUserAD);
        panelGridUserAD.addItem(gridUsersAD);
        assigmentPanelUserAD.addItem(panelTitleUserAD);
        assigmentPanelUserAD.addItem(panelGridUserAD);
        assigmentPanelGlobalAD.addItem(assigmentPanelUserAD);
        panelTitleUseListAD.addItem(titleUserListAD);
        panelGridUserListAD.addItem(gridUserListAD);
        assigmentPanelUserListAD.addItem(panelTitleUseListAD);
        assigmentPanelUserListAD.addItem(panelGridUserListAD);
        assigmentPanelGlobalAD.addItem(assigmentPanelUserListAD);

        if (formAssignmentRules.dirty == null) {
            formAssignmentRules.dirty = false;
        }
        windowAssignmentRules.addItem(formAssignmentRules);
        windowAssignmentRules.addItem(tabPanelAssignmentRules);
        windowAssignmentRules.open();
        applyStyleWindowForm(windowAssignmentRules);
        windowAssignmentRules.body.style.overflowY = 'auto';
        windowAssignmentRules.body.style.overflowX = 'hidden';
        windowAssignmentRules.defineEvents();
        buttonsUsers.defineEvents();
        tabPanelAssignmentRules.itemClick(0);
        loadServerData();
        domSettings();
        //array for Users
        usersgrid = gridUsers;
        for (i = 0; i < usersgrid.getItems().length; i += 1) {
            arrayObjectUsers2[i] = usersgrid.getItems()[i];
        }
        userslist = gridUserList;
        for (i = 0; i < userslist.getItems().length; i += 1) {
            arrayObjectUserList2[i] = userslist.getItems()[i];
        }
        //array for AdHocUsers
        usersgrid = gridUsersAD;
        for (i = 0; i < usersgrid.getItems().length; i += 1) {
            arrayObjectAdhocUser2[i] = usersgrid.getItems()[i];
        }
        userslist = gridUserListAD;
        for (i = 0; i < userslist.getItems().length; i += 1) {
            arrayObjectAdhocUserList2[i] = userslist.getItems()[i];
        }
        formAssignmentRules.html.style.marginLeft = '30px';
        formAssignmentRulesSetTimeoutOption;
        document.getElementById("formAssignmentRulesSetTimeoutOption").childNodes[0].onchange = function () {
            visibleService(this.checked);
        };
        $(".pmui-field-control-table").css("border", "0px");
        $(".pmui-field-label").css("padding", "0px");
        $(".mafe-designer-assigment-title").css("margin-top", "25px");
        $("#formTasAssignType").append("<hr style=' border: 0; border-top: 1px solid #eee;'>");
        $(".pmui-gridpanel-footer").addClass("pmui-gridpanel-footer-dinamic");
    };
}());