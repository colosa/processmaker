(function () {
    PMDesigner.assigmentSupervisors = function (event) {
        var assigmentWindow = null,
            pageSizeAssignment = 8,
            flagEdit = 0,
            i,
            index = 0,
            quickMessageWindow = new QuickMessageWindow(),
            arrayObjectUserList = new Array(),
            arrayObjectUsers = new Array(),
            arrayObjectUserList2 = new Array(),
            arrayObjectUsers2 = new Array(),
            arrayDynaformInputDocumentID = new Array(),
            arrayDynaformInputDocumentObject = new Array(),
            arrayObjectRemovedSteps = new Array(),
            message_window,
            restClient,
            assigmentPanelUser,
            assigmentPanelUserList,
            assigmentPanelGlobal,
            panelTitleUser,
            titleUser,
            panelSearchUser,
            searchGridUser,
            panelGridUser,
            buttonsUsers,
            gridUsers,
            panelTitleUserList,
            titleUserList,
            panelSearchUserList,
            searchGridUserList,
            panelGridUserList,
            radioUsersList,
            buttonsUserList,
            gridUserList,
            radioUsers,
            applyStylesRadioButtonGroupField,
            applyStylesForToolbar,
            applyStyles,
            loadUsers,
            loadUsersList,
            groupRows,
            assignee,
            remove,
            loadGridCaseTacker,
            orderDataTree,
            titleTreeObjects,
            titleGridObjects,
            panelLabelObjects,
            panelObjects,
            treePanelObjects,
            updateItem,
            usersgrid,
            saveItemDyanformInputDocuments,
            getValuesAssignmentSteps,
            gridPanelObjects,
            panelContainerObjects,
            userslist,
            gridpanelobj,
            flashMessage = new PMUI.ui.FlashMessage({
                message: '',
                appendTo: document.body,
                duration: 1000,
                severity: "success"
            });
        assigmentWindow = new PMUI.ui.Window({
            id: 'assigmentUserWindow',
            title: 'Supervisors'.translate(),
            width: DEFAULT_WINDOW_WIDTH + 1,
            height: DEFAULT_WINDOW_HEIGHT,
            footerHeight: 'auto',
            bodyHeight: 'auto',
            modal: true,
            onBeforeClose: function () {
                if (flagEdit != 0) {
                    message_window.open();
                    message_window.showFooter();
                } else {
                    assigmentWindow.close();
                }
            },
            footerItems: [
                {
                    text: "Cancel",
                    handler: function () {
                        if (flagEdit != 0) {
                            message_window.open();
                            message_window.showFooter();
                        } else {
                            assigmentWindow.close();
                        }
                    },
                    buttonType: 'error'
                },
                {
                    text: 'Save',
                    handler: function () {
                        var i, j, idObject, objType, baseEndPointID;
                        if (flagEdit != 0) {
                            //save Configuration Supervisors and Steps(Dyanform - Inputs Documents)                            
                            if (gridUserList.getItems().length > 0) {
                                grid = gridUserList;
                                for (i = 0; i < arrayObjectUserList.length; i += 1) {
                                    b = arrayObjectUserList[i];
                                    grid.removeItem(b);
                                    grid.sort('sup_name', 'asc');
                                    gridUserList.addItem(b);
                                    radioUsersList.setValue(radioUsers.getValue());
                                    groupRows(gridUserList, radioUsers.getValue());
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

                            index = 0;
                            for (i = 0; i < gridPanelObjects.getItems().length; i += 1) {
                                idObject = gridPanelObjects.getItems()[i].getData().obj_uid;
                                index = arrayDynaformInputDocumentID.indexOf(idObject);
                                if (index <= -1) {
                                    saveItemDyanformInputDocuments(gridPanelObjects.getItems()[i]);
                                } else {
                                    updateItem(gridPanelObjects.getItems()[i], i);
                                }
                            }

                            for (i = 0; i < arrayObjectRemovedSteps.length; i += 1) {
                                for (j = 0; j < arrayDynaformInputDocumentObject.length; j += 1) {
                                    index = (arrayObjectRemovedSteps[i] == arrayDynaformInputDocumentObject[j].obj_uid) ? 0 : 1;
                                    if (index == 0) {
                                        objType = arrayDynaformInputDocumentObject[j].obj_type.toLowerCase();
                                        baseEndPointID = (objType === "dynaform") ? arrayDynaformInputDocumentObject[j].pud_uid : arrayDynaformInputDocumentObject[j].pui_uid;
                                        restClient = new PMRestClient({
                                            typeRequest: 'post',
                                            multipart: true,
                                            data: {
                                                "calls": [
                                                    {
                                                        "url": 'process-supervisor/' + objType + '/' + baseEndPointID,
                                                        "method": 'DELETE'
                                                    }
                                                ]
                                            },
                                            functionSuccess: function (xhr, response) {
                                            },
                                            functionFailure: function (xhr, response) {
                                            }
                                        });
                                        restClient.executeRestClient();
                                        break;
                                    }
                                }
                            }
                            assigmentWindow.close();
                        }
                        else {
                            assigmentWindow.close();
                        }
                    },
                    buttonType: "success"
                }
            ],
            visibleFooter: true,
            buttonPanelPosition: "bottom",
            footerAlign: "right"
        });

        message_window = new PMUI.ui.MessageWindow({
            id: "messageWindowCancel",
            width: 490,
            title: "Supervisors".translate(),
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
                        message_window.close();
                        assigmentWindow.close();
                    },
                    buttonType: "success"
                }
            ]
        });

        restClient = new PMRestClient({
            endpoint: 'projects',
            typeRequest: 'get',
            messageError: 'There are problems, please try again.'.translate(),
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });

        assigmentPanelUser = new PMUI.core.Panel({
            layout: "vbox",
            width: "49%",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-white'
                ]
            }
        });
        assigmentPanelUserList = new PMUI.core.Panel({
            layout: "vbox",
            width: "50%",
            style: {
                cssClasses: [
                    'mafe-panel-assignment-smooth'
                ]
            }
        });
        assigmentPanelGlobal = new PMUI.core.Panel({
            layout: 'hbox',
            width: DEFAULT_WINDOW_WIDTH,
            style: {
                cssClasses: [
                    'mafe-assigment-panel-global'
                ]
            }
        });

        panelTitleUser = new PMUI.core.Panel({
            layout: 'hbox'
        });
        titleUser = new PMUI.ui.TextLabel({
            id: 'titleUser',
            label: ' ',
            textMode: 'plain',
            text: 'Available Users List'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelSearchUser = new PMUI.core.Panel({
            layout: 'hbox'
        });
        searchGridUser = new PMUI.field.TextField({
            id: 'searchGridUser',
            label: ' ',
            placeholder: 'Search ...'.translate()
        });
        panelGridUser = new PMUI.core.Panel({layout: 'hbox'});
        buttonsUsers = new PMUI.field.OptionsSelectorField({
            id: 'buttonsUsers',
            orientation: 'horizontal',
            items: [{
                text: 'All'.translate(),
                selected: true,
                value: 'all',
                style: {
                    cssClasses: [
                        'pmui-switch-icon-all'
                    ]
                }
            }, {
                text: 'Users'.translate(),
                value: 'user',
                style: {
                    cssClasses: [
                        'pmui-switch-icon-user'
                    ]
                }
            }, {
                text: 'Groups'.translate(),
                value: 'group',
                style: {
                    cssClasses: [
                        'pmui-switch-icon-group'
                    ]
                }
            }
            ],
            listeners: {
                select: function (item, event) {
                    groupRows(gridUsers, item.value);
                }
            },
            style: {
                cssClasses: [
                    'mafe-assigment-buttons'
                ]
            }
        });
        gridUsers = new PMUI.grid.GridPanel({
            id: 'gridUsers',
            pageSize: pageSizeAssignment,
            behavior: 'dragdropsort',
            filterable: true,
            filterPlaceholder: 'Search ...'.translate(),
            emptyMessage: 'No records found'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/available-process-supervisors/paged'
                }
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].obj_type == "group") {
                        data[i].available = data[i].grp_name;
                    } else {
                        data[i].available = data[i]["usr_firstname"] + " " + data[i]["usr_lastname"] + " (" + data[i]["usr_username"] + ")";
                    }
                }
                return data;
            },
            columns: [{
                id: 'gridUsersButtonLabel',
                title: '',
                dataType: 'button',
                width: '10%',
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
                    option.id = "list-usersIngroup-iem"
                    if (row.getData()["obj_type"] === 'group') {
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
                        restClient.setBaseEndPoint('group/' + row.getData()["grp_uid"] + '/supervisor-users?start=0&limit=11');
                        restClient.executeRestClient();
                    }
                }
            }, {
                title: '',
                dataType: 'string',
                columnData: 'available',
                alignmentCell: 'left',
                width: '330px'
            }, {
                id: 'gridUsersButtonAssign',
                title: '',
                dataType: 'button',
                width: '19%',
                buttonStyle: {
                    cssClasses: [
                        'mafe-button-edit-assign'
                    ]
                },
                buttonLabel: function (row, data) {
                    row.getCells()[0].content.style.addClasses([row.getData()["obj_type"] === 'user' ? 'button-icon-user' : 'button-icon-group']);
                    return '';
                },
                onButtonClick: function (row, grid) {
                    grid = (grid != null) ? grid : gridUsers;
                    assignee(row);
                    gridUserList.goToPage(gridUserList.currentPage);
                    grid.goToPage(grid.currentPage);
                    flashMessage.setMessage("The user/group was successfully removed".translate());
                    flashMessage.setAppendTo(assigmentWindow.getHTML());
                    flashMessage.show();
                }
            }
            ],
            onDropOut: function (item, origin, destiny) {
            },
            onDrop: function (a, row) {
                grid = this;
                remove(row);
                gridUserList.goToPage(gridUserList.currentPage);
                grid.goToPage(grid.currentPage);
                flashMessage.setMessage("The user/group was successfully removed".translate());
                flashMessage.setAppendTo(assigmentWindow.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });

        panelTitleUserList = new PMUI.core.Panel({
            layout: 'hbox'
        });
        titleUserList = new PMUI.ui.TextLabel({
            id: 'titleUserList',
            textMode: 'plain',
            text: 'Assigned supervisors list'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });
        panelSearchUserList = new PMUI.core.Panel({
            layout: 'hbox'
        });
        searchGridUserList = new PMUI.field.TextField({
            id: 'searchGridUserList',
            label: ' ',
            placeholder: 'Search ...'.translate()
        });
        panelGridUserList = new PMUI.core.Panel({
            layout: 'hbox'
        });
        radioUsersList = new PMUI.field.RadioButtonGroupField({
            id: 'idRadioUsersList',
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [
                {
                    label: "View all".translate(),
                    value: "all"
                },
                {
                    label: "View users".translate(),
                    value: "user"
                },
                {
                    label: "View groups".translate(),
                    value: "group"
                }
            ],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "user" :
                        gridUserList.typeList = "user".translate();
                        break;
                    case "group":
                        gridUserList.typeList = "group".translate();
                        break;
                    default:
                        gridUserList.typeList = "";
                        break;
                }
                gridUserList.goToPage(0);
            },
            required: true,
            value: "all"
        });
        buttonsUserList = new PMUI.field.OptionsSelectorField({
            id: 'buttonsUserList',
            orientation: 'horizontal',
            items: [{
                text: 'All'.translate(),
                value: 'all',
                selected: true,
                style: {
                    cssClasses: [
                        'pmui-switch-icon-all'
                    ]
                }
            }, {
                text: 'Users'.translate(),
                value: 'user',
                style: {
                    cssClasses: [
                        'pmui-switch-icon-user'
                    ]
                }
            }, {
                text: 'Groups'.translate(),
                value: 'group',
                style: {
                    cssClasses: [
                        'pmui-switch-icon-group'
                    ]
                }
            }

            ],
            listeners: {
                select: function (item, event) {
                    groupRows(gridUserList, item.value);
                }
            },
            style: {
                cssClasses: [
                    'mafe-assigment-buttons'
                ]
            }
        });
        gridUserList = new PMUI.grid.GridPanel({
            id: 'gridUserList',
            pageSize: pageSizeAssignment,
            behavior: 'dragdropsort',
            filterPlaceholder: 'Search ...'.translate(),
            filterable: true,
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            emptyMessage: function () {
                var div = document.createElement('div'),
                    span = document.createElement('span');
                div.appendChild(span);
                div.className = 'mafe-grid-panel-empty';
                span.innerHTML = 'Drag & Drop a User or a Group here'.translate();
                return div;
            },
            onEmpty: function (grid, cell) {
                gridUserList.dom.tableContainer.style.overflow = 'hidden';
            },
            dynamicLoad: {
                keys: {
                    server: HTTP_SERVER_HOSTNAME,
                    projectID: PMDesigner.project.projectId,
                    workspace: WORKSPACE,
                    accessToken: PMDesigner.project.tokens.access_token,
                    endPoint: 'project/' + PMDesigner.project.id + '/process-supervisors/paged'
                }
            },
            customDataRest: function (data) {
                var i;
                for (i = 0; i < data.length; i += 1) {
                    if (data[i].pu_type == "GROUP_SUPERVISOR") {
                        data[i].assignee = data[i].grp_name;
                    } else {
                        data[i].assignee = data[i]["usr_firstname"] + " " + data[i]["usr_lastname"] + " (" + data[i]["usr_username"] + ")";
                    }
                }
                return data;
            },
            columns: [{
                id: 'gridUserListButtonLabel',
                title: '',
                width: '10%',
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
                    option.id = "list-usersIngroup-iem"
                    if (row.getData()["pu_type"] === "GROUP_SUPERVISOR") {
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
                        restClient.setBaseEndPoint('group/' + row.getData()["grp_uid"] + '/supervisor-users?start=0&limit=11');
                        restClient.executeRestClient();
                    }
                }
            }, {
                title: '',
                dataType: 'string',
                columnData: 'assignee',
                width: '330px',
                alignmentCell: 'left'
            }, {
                id: 'gridUserListButtonDelete',
                title: '',
                dataType: 'button',
                width: '10%',
                buttonStyle: {
                    cssClasses: [
                        'mafe-button-delete-assign'
                    ]
                },
                buttonLabel: function (row, data) {
                    row.getCells()[0].content.style.addClasses([row.getData()["pu_type"] === "SUPERVISOR" ? 'button-icon-user' : 'button-icon-group']);
                    return '';
                },
                onButtonClick: function (row, grid) {
                    grid = (grid != null) ? grid : gridUsers;
                    remove(row);
                    gridUsers.goToPage(gridUsers.currentPage);
                    grid.goToPage(grid.currentPage);
                    flashMessage.setMessage("Assignee saved successfully".translate());
                    flashMessage.setAppendTo(assigmentWindow.getHTML());
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
                flashMessage.setAppendTo(assigmentWindow.getHTML());
                flashMessage.show();
                return false;
            },
            style: {
                cssClasses: [
                    'mafe-designer-assigment-grid'
                ]
            }
        });
        radioUsers = new PMUI.field.RadioButtonGroupField({
            id: 'idRadioUsers',
            controlPositioning: 'horizontal',
            maxDirectionOptions: 3,
            options: [
                {
                    label: "View all".translate(),
                    value: "all"
                },
                {
                    label: "View users".translate(),
                    value: "user"
                },
                {
                    label: "View groups".translate(),
                    value: "group"
                }
            ],
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
            },
            required: true,
            value: "all",
            style: {
                cssProperties: {
                    'margin-top': '15px'
                }
            }
        });
        applyStylesRadioButtonGroupField = function () {
            $('#idRadioUsers .pmui-field-control-table').css("border", "none");
            $('#idRadioUsers').css("margin-top", "12px");
            radioUsers.defineEvents();

            $('#idRadioUsersList .pmui-field-control-table').css("border", "none");
            $('#idRadioUsersList').css("margin-top", "12px");
            radioUsersList.defineEvents();
        };

        applyStylesForToolbar = function () {
            $('.pmui-gridpanel-toolbar')[0].childNodes[0].style.width = "300px";
            $('.pmui-gridpanel-toolbar')[1].childNodes[0].style.width = "300px";
            $('.pmui-gridpanel-toolbar')[0].childNodes[0].style.backgroundColor = "#f5f5f5";
            $('.pmui-gridpanel-toolbar')[1].childNodes[0].style.backgroundColor = "#f5f5f5";
            $('#assigmentUserWindow')[0].childNodes[1].childNodes[0].childNodes[1].style.backgroundColor = "white";
            $('#gridUserList')[0].style.backgroundColor = "white";
            $('#gridUserList').css("width", "452px");
            $('#gridUsers').css({"width": "452px", "height": "175px"});
            $('.pmui-window-body .pmui-panel:eq(9)').css({
                'width': '1000px',
                'border-top': '1px solid #cdd2d5',
                'margin-top': '36px',
                'height': '80px'
            });
        };

        applyStyles = function () {
            applyStylesRadioButtonGroupField();
            gridUsers.dom.toolbar.appendChild(radioUsers.getHTML());
            gridUsers.dom.toolbar.style.height = "76px";
            gridUserList.dom.toolbar.appendChild(radioUsersList.getHTML());
            gridUserList.dom.toolbar.style.height = "76px";
            radioUsers.dom.labelTextContainer.style.display = "none";
            radioUsersList.dom.labelTextContainer.style.display = "none";
            gridUsers.hideHeaders();
            gridUserList.hideHeaders();
            assigmentPanelUserList.setHeight('100%');
            gridUsers.filterControl.html.style.width = "300px";
            gridUserList.filterControl.html.style.width = "300px";
            assigmentWindow.getItems()[0].getItems()[0].html.style.borderRight = "1px solid #cdd2d5";
            assigmentWindow.getItems()[0].getItems()[0].html.style.borderBottom = "1px solid #cdd2d5";
            assigmentWindow.getItems()[0].getItems()[1].html.style.borderBottom = "1px solid #cdd2d5";
            assigmentWindow.getItems()[0].getItems()[0].html.style.height = "415px";
            assigmentWindow.getItems()[0].getItems()[1].html.style.height = "415px";
            assigmentWindow.getItems()[0].getItems()[1].html.style.paddingLeft = "10px";
            $('#gridPanelObjects .pmui-gridpanel-tableContainer').css({'height': '245px'});
            $(".pmui-gridpanel-footer").addClass("pmui-gridpanel-footer-dinamic");
        };
        loadUsers = function (response) {
            var i;
            for (i = 0; i < response.length; i += 1) {
                if (response[i].obj_type === 'user') {
                    gridUsers.addDataItem({
                        sup_uid: response[i].usr_uid,
                        sup_name: response[i].usr_firstname + ' ' + response[i].usr_lastname,
                        obj_type: response[i].obj_type
                    });
                }
                if (response[i].obj_type === 'group') {
                    gridUsers.addDataItem({
                        sup_uid: response[i].grp_uid,
                        sup_name: response[i].grp_name,
                        obj_type: response[i].obj_type
                    });
                }
            }
            gridUsers.sort('sup_name', 'asc');
        };
        loadUsersList = function (response) {
            var i;
            for (i = 0; i < response.length; i += 1) {
                if (response[i].pu_type === 'SUPERVISOR') {
                    gridUserList.addDataItem({
                        sup_uid: response[i].usr_uid,
                        sup_name: response[i].usr_firstname + ' ' + response[i].usr_lastname,
                        obj_type: 'user',
                        pu_type: response[i].pu_type,
                        pu_uid: response[i].pu_uid
                    });
                }
                if (response[i].pu_type === 'GROUP_SUPERVISOR') {
                    gridUserList.addDataItem({
                        sup_uid: response[i].grp_uid,
                        sup_name: response[i].grp_name,
                        obj_type: 'group',
                        pu_type: response[i].pu_type,
                        pu_uid: response[i].pu_uid
                    });
                }
            }
            gridUserList.sort('sup_name', 'asc');
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
                    if (items[i].getData().obj_type !== value) {
                        grid.memorystack.push(items[i]);
                        grid.removeItem(items[i]);
                    }
                }
            }
            grid.sort('sup_name', 'asc');
        };
        assignee = function (row) {
            var restClient = new PMRestClient({
                endpoint: 'process-supervisor',
                typeRequest: 'post',
                data: {
                    usr_uid: row.getData()["obj_type"] == "group" ? row.getData()["grp_uid"] : row.getData()["usr_uid"],
                    pu_type: row.getData()["obj_type"] === 'group' ? 'GROUP_SUPERVISOR' : 'SUPERVISOR'
                },
                functionSuccess: function (xhr, response) {
                    row.data.customKeys.pu_type = response.pu_type;
                    row.data.customKeys.pu_uid = response.pu_uid;
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: 'There are problems saving the assigned user, please try again.'.translate()
            });
            restClient.executeRestClient();
        };
        remove = function (row) {
            var restClient = new PMRestClient({
                endpoint: 'process-supervisor/' + row.getData()["pu_uid"],
                typeRequest: 'remove',
                functionSuccess: function () {
                },
                functionComplete: function () {
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        };

        //steps Dynaforms and InputDocuments
        titleTreeObjects = new PMUI.ui.TextLabel({
            id: "titleTreeObjects",
            textMode: 'plain',
            text: 'Available Objects'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ]
            }
        });

        titleGridObjects = new PMUI.ui.TextLabel({
            id: "titleGridObjects",
            textMode: 'plain',
            text: 'Assigned objects'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-assigment-title'
                ],
                cssProperties: {
                    'left': 40
                }
            }
        });

        panelLabelObjects = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH * 0.94,
            fieldset: true,
            items: [
                titleTreeObjects,
                titleGridObjects
            ],
            style: {
                cssProperties: {
                    'margin-bottom': 2,
                    'margin-left': 50
                }
            },
            layout: "hbox"
        });

        panelObjects = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH * 0.94,
            height: 30,
            fieldset: true,
            items: [
                panelLabelObjects
            ],
            layout: "vbox"
        });

        //Objects
        orderDataTree = function (data) {
            var items = [], i,
                type = ['DYNAFORM', 'INPUT-DOCUMENT'],
                label = ['Dynaform', 'Input Document'];
            for (i = 0; i < type.length; i += 1) {
                items = [];
                for (var j = 0; j < data.length; j += 1) {
                    if (type[i] === data[j].obj_type) {
                        if (data[j].obj_type == "DYNAFORM") {
                            items.push({
                                step_type_obj: label[i].translate(),
                                obj_label: label[i].translate(),
                                obj_title: data[j]['dyn_title'],
                                obj_type: data[j]['obj_type'],
                                obj_uid: data[j]['obj_uid'],
                                dyn_uid: data[j]['dyn_uid']
                            });
                        } else {
                            if (data[j].obj_type == "INPUT-DOCUMENT") {
                                items.push({
                                    step_type_obj: label[i].translate(),
                                    obj_label: label[i].translate(),
                                    obj_title: data[j]['inp_doc_title'],
                                    obj_type: data[j]['obj_type'],
                                    obj_uid: data[j]['obj_uid'],
                                    obj_uid: data[j]['obj_uid'],
                                    inp_uid: data[j]['inp_doc_uid']
                                });
                            }
                        }
                    }
                }
                if (items.length === 0) {
                    dataTree.push({
                        obj_title: label[i].translate(),
                        items: [{obj_title: 'N/A'.translate(), obj_uid: ''}]
                    });
                } else {
                    dataTree.push({
                        obj_title: label[i].translate(),
                        items: items
                    });
                }
            }
        };

        loadGridCaseTacker = function (data) {
            var dataOrder = new Array(), i, j;
            for (i = 0; i < data.length; i += 1) {
                for (j = 0; j < data.length; j += 1) {
                    positionIndex = (data[j]['obj_type'] == 'DYNAFORM') ? data[j].pud_position : data[j].pui_position;
                    if (positionIndex == (i + 1)) {
                        dataOrder.push(data[j]);
                        switch (dataOrder[i]['obj_type']) {
                            case 'DYNAFORM':
                                label = dataOrder[i]['dyn_title'].translate();
                                break;
                            case 'INPUT-DOCUMENT':
                                label = dataOrder[i]['input_doc_title'].translate();
                                break;
                            default:
                                break;
                        }
                        dataOrder[i]['obj_title'] = label;
                        break;
                    }
                }
            }
            gridPanelObjects.setDataItems(dataOrder);
        };

        getValuesAssignmentSteps = function () {
            restClient = new PMRestClient({
                typeRequest: 'post',
                multipart: true,
                data: {
                    "calls": [
                        {
                            "url": "process-supervisor/available-assignmentsteps",
                            "method": 'GET'
                        },
                        {
                            "url": "process-supervisor/assignmentsteps",
                            "method": 'GET'
                        }
                    ]
                },
                functionSuccess: function (xhr, response) {
                    dataTree = [];
                    orderDataTree(response[0].response);
                    treePanelObjects.setDataItems(dataTree);
                    loadGridCaseTacker(response[1].response);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        };

        treePanelObjects = new PMUI.panel.TreePanel({
            id: 'treePanelObjects',
            proportion: 0.5,
            filterable: true,
            filterPlaceholder: 'Text to search'.translate(),
            emptyMessage: 'No records found'.translate(),
            style: {cssClasses: ['itemsSteps']},
            nodeDefaultSettings: {
                behavior: "drag",
                labelDataBind: 'obj_title',
                itemsDataBind: 'items',
                collapsed: false,
                childrenDefaultSettings: {
                    labelDataBind: 'obj_title',
                    autoBind: true
                },
                autoBind: true
            }
        });

        //Save Item (Drop)
        saveItemDyanformInputDocuments = function (rowStep) {
            rowStep = rowStep.getData();
            if (rowStep.obj_type === "DYNAFORM") {
                data = {
                    "dyn_uid": rowStep.obj_uid,
                    "pud_position": rowStep.obj_position
                };
            } else {
                data = {
                    "inp_doc_uid": rowStep.obj_uid,
                    "pui_position": rowStep.obj_position
                };
            }

            restClient = new PMRestClient({
                endpoint: 'process-supervisor/' + rowStep.obj_type.toLowerCase(),
                typeRequest: 'post',
                data: data,
                functionSuccess: function (xhr, response) {
                },
                functionFailure: function (xhr, response) {
                }
            });
            restClient.executeRestClient();
            return data;
        };

        //Update SORT tree
        updateItem = function (rowStep, i) {
            var objType,
                baseEndPointType,
                baseEndPointID;
            rowStep = rowStep.getData();
            rowStep.obj_position = i + 1;
            objType = rowStep.obj_type.toLowerCase(),
            baseEndPointType = (objType === "dynaform") ? 'dynaforms' : 'input-documents';
            baseEndPointID = (objType === "dynaform") ? rowStep.pud_uid : rowStep.pui_uid;
            if (objType === "dynaform") {
                rowStep.pud_position = rowStep.obj_position;
            } else {
                rowStep.pui_position = rowStep.obj_position;
            }

            restClient = new PMRestClient({
                typeRequest: 'post',
                multipart: true,
                data: {
                    "calls": [
                        {
                            "url": 'process-supervisor/' + objType + "/" + baseEndPointID,
                            "method": 'PUT',
                            "data": rowStep
                        }
                    ]
                },
                functionSuccess: function (xhr, response) {
                },
                functionFailure: function (xhr, response) {
                }
            });
            restClient.executeRestClient();
        };
        gridPanelObjects = new PMUI.grid.GridPanel({
            id: 'gridPanelObjects',
            proportion: 1.5,
            visibleFooter: false,
            filterable: false,
            style: {
                cssClasses: ['itemsSteps']
            },
            filterPlaceholder: 'Search ...'.translate(),
            emptyMessage: 'No records found'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            behavior: 'dragdropsort',
            columns: [
                {
                    title: 'Title'.translate(),
                    dataType: 'string',
                    width: 330,
                    alignment: "left",
                    columnData: "obj_title",
                    sortable: false,
                    alignmentCell: 'left'
                },
                {
                    title: 'Type'.translate(),
                    dataType: 'string',
                    width: 120,
                    alignment: "left",
                    columnData: "obj_type",
                    sortable: false,
                    alignmentCell: 'left'
                },
                {
                    id: 'gridPanelObjectsButtonDelete',
                    title: '',
                    dataType: 'button',
                    buttonLabel: '',
                    buttonStyle: {cssClasses: ['mafe-button-delete-assign']},
                    buttonTooltip: 'Remove Object'.translate(),
                    onButtonClick: function (row, grid) {
                        var rowStep, treePanelItems;
                        flagEdit = 1;
                        index = arrayDynaformInputDocumentID.indexOf(row.getData().obj_uid);
                        rowStep = row.getData();
                        treePanelItems = treePanelObjects.getItems();

                        if (index > -1) {
                            arrayObjectRemovedSteps.push(row.getData().obj_uid);
                            arrayDynaformInputDocumentID.splice(index, 1);
                        }
                        function removeRowClass(treeNode) {
                            var childNodeEl = $(treeNode.html).find('.pmui-gridpanelrow');
                            childNodeEl.removeClass('pmui-gridpanelrow');
                        }

                        function removeEmptyNode(indexNode) {
                            var parentNode = treePanelItems[indexNode];
                            if (parentNode.getItems().length == 1 &&
                                parentNode.getItems()[0].getData()['obj_uid'] == "") {
                                parentNode.removeItem(0);
                            }
                            parentNode.addItem(row);
                            removeRowClass(parentNode);
                        }

                        if (rowStep.obj_type === "DYNAFORM") {
                            removeEmptyNode(0);
                        } else {
                            removeEmptyNode(1);
                        }
                        grid.removeItem(row);
                    }
                }
            ],
            onDrop: function (grid, item, index) {
                var parentItems;
                if (item.getData()['obj_uid'] == "") {
                    return false;

                }
                parentItems = item.parent.getItems();
                if (parentItems.length == 1 && item.getData()['obj_uid'] != "") {
                    item.parent.addDataItem(
                        {obj_title: 'N/A'.translate(), obj_uid: ''}
                    );
                    item.parent.behaviorObject.draggedObject = item;
                }
                flagEdit = 1;
                rowStep = item.getData();
                rowStep.obj_position = index + 1;
                item.setData(rowStep);
                index = arrayObjectRemovedSteps.indexOf(item.getData().obj_uid);

                if (index > -1) {
                    arrayDynaformInputDocumentID.push(item.getData().obj_uid);
                    arrayObjectRemovedSteps.splice(index, 1);
                }
            },
            onSort: function (grid, item, index) {
                flagEdit = 1;
                rowStep = item.getData();
                rowStep.obj_position = index + 1;
            }
        });

        //principal Container Steps
        panelContainerObjects = new PMUI.core.Panel({
            id: "panelContainerObjects",
            width: DEFAULT_WINDOW_WIDTH,
            height: 250,
            fieldset: true,
            items: [treePanelObjects, gridPanelObjects],
            layout: "hbox",
            style: {
                cssProperties: {
                    'margin-top': '-40px'
                }
            }
        });

        assigmentWindow.open();
        panelTitleUser.addItem(titleUser);
        panelTitleUserList.addItem(titleUserList);
        assigmentWindow.body.style.overflowX = "hidden";

        panelGridUser.addItem(gridUsers);
        panelGridUserList.addItem(gridUserList);

        assigmentPanelGlobal.addItem(assigmentPanelUser);
        assigmentPanelGlobal.addItem(assigmentPanelUserList);
        assigmentWindow.addItem(assigmentPanelGlobal);

        assigmentWindow.addItem(panelObjects);
        assigmentWindow.addItem(panelContainerObjects);
        getValuesAssignmentSteps();

        gridPanelObjects.style.addProperties({overflow: 'auto'});
        gridPanelObjects.style.addProperties({float: 'right'});
        gridPanelObjects.setWidth(630);
        gridPanelObjects.setHeight(250);
        $('#gridPanelObjects').css("margin-right", "32px");
        $('#treePanelObjects').css("margin-left", "10px");

        assigmentPanelUser.addItem(panelTitleUser);
        assigmentPanelUser.addItem(panelSearchUser);
        assigmentPanelUser.addItem(radioUsers);
        assigmentPanelUser.addItem(panelGridUser);

        assigmentPanelUserList.addItem(panelTitleUserList);
        assigmentPanelUserList.addItem(panelSearchUserList);
        assigmentPanelUserList.addItem(radioUsersList);
        assigmentPanelUserList.addItem(panelGridUserList);
        gridUsers.dom.tableContainer.style.height = "245px";
        gridUserList.dom.tableContainer.style.height = "245px";
        gridUsers.goToPage(0);
        gridUserList.goToPage(0);
        assigmentWindow.defineEvents();
        applyStyles();
        treePanelObjects.style.addProperties({overflow: 'auto'});

        applyStylesForToolbar();
        usersgrid = gridUsers;
        for (i = 0; i < usersgrid.getItems().length; i += 1) {
            arrayObjectUsers2[i] = usersgrid.getItems()[i];
        }
        userslist = gridUserList;
        for (i = 0; i < userslist.getItems().length; i += 1) {
            arrayObjectUserList2[i] = userslist.getItems()[i];
        }
        gridpanelobj = gridPanelObjects;
        for (i = 0; i < gridpanelobj.getItems().length; i += 1) {
            arrayDynaformInputDocumentID[i] = gridpanelobj.getItems()[i].getData().obj_uid;
            arrayDynaformInputDocumentObject[i] = gridpanelobj.getItems()[i].getData();
        }
    };
}());