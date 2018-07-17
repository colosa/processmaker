(
    function () {
        var processPermissionsSetForm;
        PMDesigner.processPermissions = function (event) {
            var PROCESS_PERMISSIONS_OPTION = "",
                PROCESS_PERMISSIONS_UID = "",
                arrayCboGroup = [],
                arrayCboUser = [],
                winGrdpnlProcessPermissions,
                grdpnlProcessPermissions,
                frmProcessPermissions,
                processPermissionsData,
                btnCreate, btnSave, btnCancel,
                loadDataFromServerToFields,
                disableAllItems,
                listProcessPermissions,
                isDirty2,
                isDirtyFormProcessPermission,
                refreshGridPanelInMainWindow,
                processPermissionsGetRestProxy,
                processPermissionsPostRestProxy,
                processPermissionGetRestProxy,
                processPermissionsPutRestProxy,
                cboTargetCboOriginTaskSetOptionsRestProxy,
                cboDynaFormSetOptionsRestProxy,
                cboInputDocumentSetOptionsRestProxy,
                cboOutputDocumentSetOptionsRestProxy,
                processPermissionsSetFormByType,
                processPermissionsDeleteRestProxy,
                cboStatusCase,
                cboTargetTask,
                cboGroupOrUser,
                cboOriginTask,
                optionsType,
                cboType,
                cboDynaForm,
                cboInputDocument,
                cboOutputDocument,
                cboPermission,
                cboParticipationRequired,
                processPermissionsDataIni = {};

            loadDataFromServerToFields = function () {
                var restClient = new PMRestClient({
                    typeRequest: 'post',
                    multipart: true,
                    data: {
                        calls: [
                            {
                                url: 'project/' + PMDesigner.project.id + '/',
                                method: 'GET'
                            }, {
                                url: 'project/' + PMDesigner.project.id + '/dynaforms',
                                method: 'GET'
                            }, {
                                url: 'project/' + PMDesigner.project.id + '/input-documents',
                                method: 'GET'
                            }, {
                                url: 'project/' + PMDesigner.project.id + '/output-documents',
                                method: 'GET'
                            }
                        ]
                    },
                    functionSuccess: function (xhr, response) {
                        var i;
                        data = response[0].response;
                        cboTargetTask.clearOptions();
                        cboOriginTask.clearOptions();
                        cboTargetTask.addOption({value: '', label: 'All Tasks'.translate()});
                        cboOriginTask.addOption({value: '', label: 'All Tasks'.translate()});
                        for (i = 0; i <= data.diagrams[0].activities.length - 1; i += 1) {
                            cboTargetTask.addOption({
                                value: data.diagrams[0].activities[i].act_uid,
                                label: data.diagrams[0].activities[i].act_name
                            });
                            cboOriginTask.addOption({
                                value: data.diagrams[0].activities[i].act_uid,
                                label: data.diagrams[0].activities[i].act_name
                            });
                        }
                        //project/dynaforms
                        data = response[1].response;
                        cboDynaForm.clearOptions();
                        cboDynaForm.addOption({value: '', label: 'All'.translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboDynaForm.addOption({value: data[i].dyn_uid, label: data[i].dyn_title});
                        }
                        //project/input-documents
                        data = response[2].response;
                        cboInputDocument.clearOptions();
                        cboInputDocument.addOption({value: '', label: 'All'.translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboInputDocument.addOption({value: data[i].inp_doc_uid, label: data[i].inp_doc_title});
                        }
                        //project/output-documents
                        data = response[3].response;
                        cboOutputDocument.clearOptions();
                        cboOutputDocument.addOption({value: '', label: 'All'.translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboOutputDocument.addOption({value: data[i].out_doc_uid, label: data[i].out_doc_title});
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restClient.setBaseEndPoint('');
                restClient.executeRestClient();
            };

            disableAllItems = function () {
                winGrdpnlProcessPermissions.getItems()[0].setVisible(false);
                winGrdpnlProcessPermissions.getItems()[1].setVisible(false);
                winGrdpnlProcessPermissions.hideFooter();
            };

            isDirty2 = function () {
                var user,
                    dynaForm,
                    inputDocument,
                    outputDocument,
                    flagInsert = (typeof(processPermissionsData.op_case_status) === "undefined") ? true : false;

                if (flagInsert) {
                    processPermissionsData = processPermissionsDataIni;
                }

                if (cboStatusCase.getValue() != processPermissionsData.op_case_status) {
                    return true;
                }

                if (cboTargetTask.getValue() != processPermissionsData.tas_uid) {
                    return true;
                }

                user = (cboGroupOrUser.get("value") !== null && cboGroupOrUser.get("value")) ? cboGroupOrUser.get("value") : "";

                if (user != processPermissionsData.usr_uid) {
                    return true;
                }

                if (cboOriginTask.getValue() != processPermissionsData.op_task_source) {
                    return true;
                }

                if (cboParticipationRequired.getValue() != processPermissionsData.op_participate) {
                    return true;
                }

                if (cboType.getValue() != processPermissionsData.op_obj_type) {
                    return true;
                }

                switch (cboType.getValue()) {
                    case "DYNAFORM":
                        dynaForm = (cboDynaForm.getValue() !== "") ? cboDynaForm.getValue() : "0";

                        if (dynaForm != processPermissionsData.op_obj_uid) {
                            return true;
                        }

                        if (cboPermission.getValue() != processPermissionsData.op_action) {
                            return true;
                        }
                        break;
                    case "ATTACHMENT":
                        if (cboPermission.getValue() !== processPermissionsData.op_action) {
                            return true;
                        }
                        break;
                    case "INPUT":
                        inputDocument = (cboInputDocument.getValue() !== "") ? cboInputDocument.getValue() : "0";

                        if (inputDocument != processPermissionsData.op_obj_uid) {
                            return true;
                        }

                        if (cboPermission.getValue() != processPermissionsData.op_action) {
                            return true;
                        }

                        break;
                    case "OUTPUT":
                        outputDocument = (cboOutputDocument.getValue() !== "") ? cboOutputDocument.getValue() : "0";

                        if (outputDocument != processPermissionsData.op_obj_uid) {
                            return true;
                        }
                        if (cboPermission.getValue() != processPermissionsData.op_action) {
                            return true;
                        }
                        break;
                    case "CASES_NOTES":
                    case "SUMMARY_FORM":
                        break;
                    case "ANY":
                    case "MSGS_HISTORY":
                        if (cboPermission.getValue() != processPermissionsData.op_action) {
                            return true;
                        }
                        break;
                }

                return false;
            };

            isDirtyFormProcessPermission = function () {
                $("input,select,textarea").blur();
                if (frmProcessPermissions.isVisible()) {
                    var result = frmProcessPermissions.isDirty(), message_window;

                    if (isDirty2()) {
                        message_window = new PMUI.ui.MessageWindow({
                            id: "cancelMessageTriggers",
                            windowMessageType: 'warning',
                            width: 490,
                            title: "Permissions".translate(),
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
                                        PROCESS_PERMISSIONS_OPTION = "";
                                        PROCESS_PERMISSIONS_UID = "";
                                        cboGroupOrUser.html.find("input").val("");
                                        cboGroupOrUser.value = "";
                                        if (clickedClose) {
                                            winGrdpnlProcessPermissions.close();
                                        } else {
                                            refreshGridPanelInMainWindow(false);
                                        }

                                    },
                                    buttonType: "success"
                                }
                            ]
                        });
                        message_window.open();
                        message_window.showFooter();
                    } else {
                        if (cboGroupOrUser.html.find("input").val()) {
                            cboGroupOrUser.html.find("input").val("");
                        }
                        cboGroupOrUser.containerList.hide();
                        PROCESS_PERMISSIONS_OPTION = "";
                        PROCESS_PERMISSIONS_UID = "";
                        refreshGridPanelInMainWindow(false);
                        if (clickedClose) {
                            winGrdpnlProcessPermissions.close();
                        }
                    }
                } else {
                    winGrdpnlProcessPermissions.close();
                }
            };
            refreshGridPanelInMainWindow = function (load) {
                disableAllItems();
                PROCESS_PERMISSIONS_OPTION = "";
                PROCESS_PERMISSIONS_UID = "";
                winGrdpnlProcessPermissions.getItems()[0].setVisible(true);
                winGrdpnlProcessPermissions.setTitle("Permissions".translate());
                load = load != null ? load : true;
                if (load) {
                    processPermissionsGetRestProxy(grdpnlProcessPermissions);
                }
            };

            processPermissionsGetRestProxy = function (grdpnl) {
                var restProxy = new PMRestClient({
                    endpoint: "process-permissions",
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        listProcessPermissions = response;
                        grdpnl.setDataItems(listProcessPermissions);
                        grdpnl.sort('group_user', 'asc');
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });

                restProxy.executeRestClient();
            };

            processPermissionsPostRestProxy = function (data) {
                var restProxy = new PMRestClient({
                    endpoint: "process-permission",
                    typeRequest: "post",
                    data: data,
                    functionSuccess: function (xhr, response) {
                        refreshGridPanelInMainWindow();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageSuccess: 'Permission saved successfully'.translate(),
                    flashContainer: grdpnlProcessPermissions
                });
                restProxy.executeRestClient();
            };

            processPermissionGetRestProxy = function (processPermissionsUid) {
                var restProxy = new PMRestClient({
                    endpoint: "process-permission/" + processPermissionsUid,
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        var data = response;
                        processPermissionsSetForm("PUT", data);
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restProxy.executeRestClient();
            };

            processPermissionsPutRestProxy = function (processPermissionsUid, data) {
                var restProxy = new PMRestClient({
                    endpoint: "process-permission/" + processPermissionsUid,
                    typeRequest: "update",
                    data: data,
                    functionSuccess: function (xhr, response) {
                        refreshGridPanelInMainWindow();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageSuccess: 'Permission edited successfully'.translate(),
                    flashContainer: grdpnlProcessPermissions
                });
                restProxy.executeRestClient();
            };

            cboTargetCboOriginTaskSetOptionsRestProxy = function (cboTargetTask, cboOriginTask) {
                cboTargetTask.clearOptions();
                cboOriginTask.clearOptions();
                var restProxy = new PMRestClient({
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        var data = response, i;
                        cboTargetTask.addOption({value: "", label: "All Tasks".translate()});
                        cboOriginTask.addOption({value: "", label: "All Tasks".translate()});
                        for (i = 0; i <= data.diagrams[0].activities.length - 1; i += 1) {
                            cboTargetTask.addOption({
                                value: data.diagrams[0].activities[i].act_uid,
                                label: data.diagrams[0].activities[i].act_name
                            });
                            cboOriginTask.addOption({
                                value: data.diagrams[0].activities[i].act_uid,
                                label: data.diagrams[0].activities[i].act_name
                            });
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });

                restProxy.executeRestClient();
            };

            cboDynaFormSetOptionsRestProxy = function (cboDynaForm) {
                cboDynaForm.clearOptions();
                var restProxy = new PMRestClient({
                    endpoint: "dynaforms",
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        var data = response, i;
                        cboDynaForm.addOption({value: "", label: "All".translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboDynaForm.addOption({value: data[i].dyn_uid, label: data[i].dyn_title});
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restProxy.executeRestClient();
            };

            cboInputDocumentSetOptionsRestProxy = function (cboInputDocument) {
                cboInputDocument.clearOptions();
                var restProxy = new PMRestClient({
                    endpoint: "input-documents",
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        var data = response, i;
                        cboInputDocument.addOption({value: "", label: "All".translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboInputDocument.addOption({value: data[i].inp_doc_uid, label: data[i].inp_doc_title});
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restProxy.executeRestClient();
            };

            cboOutputDocumentSetOptionsRestProxy = function (cboOutputDocument) {
                var restProxy;
                cboOutputDocument.clearOptions();
                restProxy = new PMRestClient({
                    endpoint: "output-documents",
                    typeRequest: "get",
                    functionSuccess: function (xhr, response) {
                        var data = response, i;
                        cboOutputDocument.addOption({value: "", label: "All".translate()});
                        for (i = 0; i <= data.length - 1; i += 1) {
                            cboOutputDocument.addOption({value: data[i].out_doc_uid, label: data[i].out_doc_title});
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restProxy.executeRestClient();
            };

            processPermissionsSetFormByType = function (type) {
                cboPermission.removeOption("DELETE");
                cboPermission.removeOption("RESEND");

                cboPermission.reset();
                cboDynaForm.setVisible(false);
                cboInputDocument.setVisible(false);
                cboOutputDocument.setVisible(false);
                cboPermission.setVisible(false);

                switch (type) {
                    case "DYNAFORM":
                        cboDynaForm.setVisible(true);
                        cboPermission.setVisible(true);
                        break;
                    case "ATTACHMENT":
                        cboPermission.setVisible(true);
                        break;
                    case "INPUT":
                        cboPermission.addOption({value: "DELETE", label: "Delete".translate()});

                        cboInputDocument.setVisible(true);
                        cboPermission.setVisible(true);
                        break;
                    case "OUTPUT":
                        cboPermission.addOption({value: "DELETE", label: "Delete".translate()});

                        cboOutputDocument.setVisible(true);
                        cboPermission.setVisible(true);
                        break;
                    case "CASES_NOTES":
                    case "SUMMARY_FORM":
                        break;
                    case "MSGS_HISTORY":
                        cboPermission.addOption({value: "RESEND", label: "Resend".translate()});

                        cboPermission.setVisible(true);
                        break;
                    case "ANY":
                        cboPermission.setVisible(true);
                        break;
                }
            };

            processPermissionsDeleteRestProxy = function (processPermissionsUid) {
                var restProxy = new PMRestClient({
                    endpoint: "process-permission/" + processPermissionsUid,
                    typeRequest: "remove",
                    functionSuccess: function (xhr, response) {
                        refreshGridPanelInMainWindow();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageSuccess: 'Permission deleted successfully'.translate(),
                    flashContainer: grdpnlProcessPermissions
                });
                restProxy.executeRestClient();
            };

            processPermissionsSetForm = function (option, data) {
                processPermissionsData = data
                PROCESS_PERMISSIONS_OPTION = option;
                PROCESS_PERMISSIONS_UID = (typeof(processPermissionsData.op_uid) != "undefined") ? processPermissionsData.op_uid : "";

                disableAllItems();
                winGrdpnlProcessPermissions.showFooter();
                winGrdpnlProcessPermissions.getItems()[1].setVisible(true);

                loadDataFromServerToFields();

                switch (option) {
                    case "POST":
                        winGrdpnlProcessPermissions.setTitle("Create permission".translate());
                        frmProcessPermissions.reset();
                        processPermissionsSetFormByType(cboType.getValue());

                        break;
                    case "PUT":
                        winGrdpnlProcessPermissions.setTitle("Edit permission".translate());
                        cboStatusCase.setValue(processPermissionsData.op_case_status);
                        cboTargetTask.setValue(processPermissionsData.tas_uid);

                        var endpoint;
                        if (processPermissionsData.op_user_relation == 1) {
                            endpoint = "users/" + processPermissionsData.usr_uid;
                        }
                        if (processPermissionsData.op_user_relation == 2) {
                            endpoint = "groups/" + processPermissionsData.usr_uid;
                        }

                        if (endpoint) {
                            var restClient = new PMRestClient({
                                typeRequest: 'get',
                                functionSuccess: function (xhr, response) {
                                    if (response.hasOwnProperty("usr_uid")) {
                                        cboGroupOrUser.set("value", response["usr_uid"]);
                                        cboGroupOrUser.data = response;
                                        cboGroupOrUser.html.find("input").val(response["usr_firstname"] + " " + response["usr_lastname"] + " " + "(" + response["usr_username"] + ")");
                                    }
                                    if (response.hasOwnProperty("grp_uid")) {
                                        cboGroupOrUser.set("value", response["grp_uid"]);
                                        cboGroupOrUser.data = response;
                                        cboGroupOrUser.html.find("input").val(response["grp_title"]);
                                    }
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: 'There are problems saving the assigned user, please try again.'.translate()
                            });
                            restClient.setBaseEndPoint(endpoint);
                            restClient.executeRestClient();
                        }

                        cboParticipationRequired.setValue(processPermissionsData.op_participate);
                        cboType.setValue(processPermissionsData.op_obj_type);
                        cboOriginTask.setValue(processPermissionsData.op_task_source);
                        processPermissionsSetFormByType(processPermissionsData.op_obj_type);
                        switch (processPermissionsData.op_obj_type) {
                            case "ANY":
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                            case "DYNAFORM":
                                cboDynaForm.setValue(processPermissionsData.op_obj_uid);
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                            case "ATTACHMENT":
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                            case "INPUT":
                                cboInputDocument.setValue(processPermissionsData.op_obj_uid);
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                            case "OUTPUT":
                                cboOutputDocument.setValue(processPermissionsData.op_obj_uid);
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                            case "CASES_NOTES":
                            case "SUMMARY_FORM":
                                break;
                            case "MSGS_HISTORY":
                                cboPermission.setValue(processPermissionsData.op_action);
                                break;
                        }
                        break;
                }
                frmProcessPermissions.setFocus();
            };

            cboStatusCase = new PMUI.field.DropDownListField({
                id: "cboStatusCase",
                name: "cboStatusCase",
                controlsWidth: "120px",
                label: "Case Status".translate(),
                value: "ALL",
                options: [
                    {
                        value: "ALL",
                        label: "All".translate()
                    },
                    {
                        value: "DRAFT",
                        label: "DRAFT".translate()
                    },
                    {
                        value: "TO_DO",
                        label: "TO DO".translate()
                    },
                    {
                        value: "PAUSED",
                        label: "PAUSED".translate()
                    },
                    {
                        value: "COMPLETED",
                        label: "COMPLETED".translate()
                    }
                ]
            });

            cboTargetTask = new PMUI.field.DropDownListField({
                id: "cboTargetTask",
                name: "cboTargetTask",
                controlsWidth: "300px",
                label: "Target Task".translate(),
                options: null
            });
            cboGroupOrUser = new SuggestField({
                label: "Group or User".translate(),
                id: "cboGroupOrUser",
                placeholder: "suggest users and groups",
                width: 500,
                required: true,
                separatingText: ["Groups", "Users"],
                dynamicLoad: {
                    data: [
                        {
                            key: "grp_uid",
                            label: ["grp_title"]
                        },
                        {
                            key: "usr_uid",
                            label: ["usr_firstname", "usr_lastname", "(", "usr_username", ")"]
                        }
                    ],
                    keys: {
                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE,
                        accessToken: PMDesigner.project.tokens.access_token,
                        endpoints: [
                            {
                                method: "GET",
                                url: 'groups'
                            }, {
                                method: "GET",
                                url: 'users'
                            }
                        ]
                    }
                }
            });

            cboOriginTask = new PMUI.field.DropDownListField({
                id: "cboOriginTask",
                name: "cboOriginTask",
                controlsWidth: "300px",
                label: "Origin Task".translate(),
                options: null
            });

            cboParticipationRequired = new PMUI.field.DropDownListField({
                id: "cboParticipationRequired",
                name: "cboParticipationRequired",
                controlsWidth: "70px",
                label: "Participation required?".translate(),
                value: "0",
                options: [
                    {
                        value: "0",
                        label: "No".translate()
                    },
                    {
                        value: "1",
                        label: "Yes".translate()
                    }
                ]
            });
            optionsType = [
                {
                    value: "ANY",
                    label: "All".translate()
                },
                {
                    value: "DYNAFORM",
                    label: "Dynaform".translate()
                },
                {
                    value: "ATTACHMENT",
                    label: "Attachment".translate()
                },
                {
                    value: "INPUT",
                    label: "Input Document".translate()
                },
                {
                    value: "OUTPUT",
                    label: "Output Document".translate()
                },
                {
                    value: "CASES_NOTES",
                    label: "Cases Notes".translate()
                },
                {
                    value: "MSGS_HISTORY",
                    label: "Messages History".translate()
                }
            ];

            if (enterprise == "1") {
                optionsType.push({value: "SUMMARY_FORM", label: "Summary Form".translate()});
            }

            cboType = new PMUI.field.DropDownListField({
                id: "cboType",
                name: "cboType",
                controlsWidth: "180px",
                label: "Type".translate(),
                value: "ANY",
                options: optionsType,
                onChange: function (newValue, prevValue) {
                    processPermissionsSetFormByType(cboType.getValue());
                }
            });

            cboDynaForm = new PMUI.field.DropDownListField({
                id: "cboDynaForm",
                name: "cboDynaForm",
                controlsWidth: "300px",
                label: "DynaForm".translate(),
                options: null,
                visible: false
            });

            cboInputDocument = new PMUI.field.DropDownListField({
                id: "cboInputDocument",
                name: "cboInputDocument",
                controlsWidth: "300px",
                label: "Input Document".translate(),
                options: null,
                visible: false
            });

            cboOutputDocument = new PMUI.field.DropDownListField({
                id: "cboOutputDocument",
                name: "cboOutputDocument",
                controlsWidth: "300px",
                label: "Output Document".translate(),
                options: null,
                visible: false
            });

            cboPermission = new PMUI.field.DropDownListField({
                id: "cboPermission",
                name: "cboPermission",
                controlsWidth: "100px",
                label: "Permission".translate(),
                value: "VIEW",
                options: [
                    {
                        value: "VIEW",
                        label: "View".translate()
                    },
                    {
                        value: "BLOCK",
                        label: "Block".translate()
                    }
                ],
                visible: true
            });

            frmProcessPermissions = new PMUI.form.Form({
                id: "frmProcessPermissions",

                title: "",
                width: "890px",
                items: [
                    cboStatusCase,
                    cboTargetTask,
                    cboOriginTask,
                    cboParticipationRequired,
                    cboType,
                    cboDynaForm,
                    cboInputDocument,
                    cboOutputDocument,
                    cboPermission
                ]
            });

            btnCreate = new PMUI.ui.Button({
                id: "btnCreate",
                text: "Create".translate(),
                height: "36px",
                width: 100,
                style: {
                    cssClasses: [
                        "mafe-button-create"
                    ]
                },

                handler: function () {
                    frmProcessPermissions.reset();
                    processPermissionsDataIni = {};

                    processPermissionsDataIni.op_case_status = "ALL";
                    processPermissionsDataIni.tas_uid = "";
                    processPermissionsDataIni.usr_uid = "";
                    processPermissionsDataIni.op_task_source = "";
                    processPermissionsDataIni.op_participate = "0";
                    processPermissionsDataIni.op_obj_type = "ANY";
                    processPermissionsDataIni.op_obj_uid = "";
                    processPermissionsDataIni.op_action = "VIEW";

                    processPermissionsSetForm("POST", {});
                }
            });

            grdpnlProcessPermissions = new PMUI.grid.GridPanel({
                id: "grdpnlProcessPermissions",
                filterPlaceholder: "Search ...".translate(),
                emptyMessage: 'No records found'.translate(),
                nextLabel: 'Next'.translate(),
                previousLabel: 'Previous'.translate(),
                customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                    return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
                },
                pageSize: 10,
                width: "96%",
                tableContainerHeight: 374,
                //height: DEFAULT_WINDOW_HEIGHT - 60,
                style: {
                    cssClasses: ["mafe-gridPanel"]
                },
                filterable: true,
                columns: [
                    {
                        columnData: "group_user",
                        title: "Group or User".translate(),
                        alignmentCell: 'left',
                        width: "190px",
                        sortable: true
                    },
                    {
                        columnData: "participated",
                        title: "Participation".translate(),
                        alignmentCell: 'left',
                        width: "115px",
                        sortable: true
                    },
                    {
                        columnData: "op_obj_type",
                        title: "Type".translate(),
                        alignmentCell: 'left',
                        width: "100px",
                        sortable: true
                    },
                    {
                        columnData: "object",
                        title: "Object".translate(),
                        alignmentCell: 'left',
                        width: "158px",
                        sortable: true
                    },
                    {
                        columnData: "op_action",
                        title: "Permission".translate(),
                        alignmentCell: 'left',
                        width: "100px",
                        sortable: true
                    },
                    {
                        columnData: "op_case_status",
                        title: "Status".translate(),
                        alignmentCell: 'left',
                        width: "70px",
                        sortable: true
                    },
                    {
                        id: 'grdpnlProcessPermissionsButtonEdit',
                        dataType: "button",
                        title: "",
                        buttonLabel: "Edit".translate(),
                        width: "70px",
                        buttonStyle: {
                            cssClasses: [
                                "mafe-button-edit"
                            ]
                        },

                        onButtonClick: function (row, grid) {
                            var data;
                            frmProcessPermissions.reset();
                            data = row.getData();
                            processPermissionGetRestProxy(data.op_uid);
                        }
                    },
                    {
                        id: 'grdpnlProcessPermissionsButtonDelete',
                        dataType: "button",
                        title: "",
                        buttonLabel: "Delete".translate(),
                        width: "80px",
                        buttonStyle: {
                            cssClasses: [
                                "mafe-button-delete"
                            ]
                        },

                        onButtonClick: function (row, grid) {
                            var data = row.getData(), msgWarning;
                            msgWarning = new PMUI.ui.MessageWindow({
                                id: "msgWarning",
                                windowMessageType: 'warning',
                                width: 490,
                                title: "Permissions".translate(),
                                message: "Do you want to delete this permission?".translate(),
                                footerItems: [
                                    {
                                        id: 'msgWarningButtonNo',
                                        text: "No".translate(),
                                        handler: function () {
                                            msgWarning.close();
                                        },
                                        buttonType: "error"
                                    },
                                    {
                                        id: 'msgWarningButtonYes',
                                        text: "Yes".translate(),
                                        handler: function () {
                                            processPermissionsDeleteRestProxy(data.op_uid);
                                            msgWarning.close();
                                        },
                                        buttonType: "success"
                                    }
                                ]
                            });

                            msgWarning.open();
                            msgWarning.dom.titleContainer.style.height = '17px';
                            msgWarning.showFooter();
                        }
                    }
                ],
                dataItems: null
            });

            winGrdpnlProcessPermissions = new PMUI.ui.Window({
                id: "winGrdpnlProcessPermissions",
                title: "Permissions".translate(),
                width: DEFAULT_WINDOW_WIDTH,
                height: DEFAULT_WINDOW_HEIGHT,
                buttonPanelPosition: "bottom",
                footerAlign: "right",
                onBeforeClose: function () {
                    clickedClose = true;
                    isDirtyFormProcessPermission();
                },
                footerItems: [
                    {
                        id: "btnCancel",
                        text: "Cancel".translate(),
                        buttonType: "error",
                        handler: function () {
                            clickedClose = false;
                            isDirtyFormProcessPermission();
                        }
                    },
                    {
                        id: "btnSave",
                        text: "Save".translate(),
                        buttonType: "success",
                        handler: function () {
                            var cboGroupOrUserValue = "", groupOrUser, data;

                            if (frmProcessPermissions.isValid() && cboGroupOrUser.isValid()) {
                                if (cboGroupOrUser.data) {
                                    if (cboGroupOrUser.data.hasOwnProperty("usr_uid")) {
                                        cboGroupOrUserValue = "1|" + cboGroupOrUser.get("value");
                                    }
                                    if (cboGroupOrUser.data.hasOwnProperty("grp_uid")) {
                                        cboGroupOrUserValue = "2|" + cboGroupOrUser.get("value");
                                    }
                                } else {
                                    cboGroupOrUserValue = "";
                                }
                                groupOrUser = cboGroupOrUserValue.split("|");

                                data = {
                                    op_case_status: cboStatusCase.getValue(),
                                    tas_uid: cboTargetTask.getValue() === '0' ? '' : cboTargetTask.getValue(),
                                    op_user_relation: groupOrUser[0],
                                    usr_uid: groupOrUser[1],
                                    op_task_source: cboOriginTask.getValue() === '0' ? '' : cboOriginTask.getValue(),
                                    op_participate: cboParticipationRequired.getValue()
                                };

                                switch (cboType.getValue()) {
                                    case "DYNAFORM":
                                        data["op_obj_type"] = cboType.getValue();
                                        data["dynaforms"] = cboDynaForm.getValue();
                                        data["op_action"] = cboPermission.getValue();
                                        break;
                                    case "INPUT":
                                        data["op_obj_type"] = cboType.getValue();
                                        data["inputs"] = cboInputDocument.getValue();
                                        data["op_action"] = cboPermission.getValue();
                                        break;
                                    case "OUTPUT":
                                        data["op_obj_type"] = cboType.getValue();
                                        data["outputs"] = cboOutputDocument.getValue();
                                        data["op_action"] = cboPermission.getValue();
                                        break;
                                    case "CASES_NOTES":
                                        data["op_obj_type"] = cboType.getValue();
                                        data["op_action"] = cboPermission.getValue();
                                        break;
                                    default:
                                        data["op_obj_type"] = cboType.getValue();
                                        data["op_action"] = cboPermission.getValue();
                                        break;
                                }
                                switch (PROCESS_PERMISSIONS_OPTION) {
                                    case "POST":
                                        processPermissionsPostRestProxy(data);
                                        break;
                                    case "PUT":
                                        processPermissionsPutRestProxy(PROCESS_PERMISSIONS_UID, data);
                                        break;
                                }
                            } else {
                                cboGroupOrUser.showMessageRequired();
                            }

                            cboGroupOrUser.html.find("input").val("");
                            cboGroupOrUser.value = "";
                        }
                    }
                ]
            });

            winGrdpnlProcessPermissions.addItem(grdpnlProcessPermissions);
            winGrdpnlProcessPermissions.addItem(frmProcessPermissions);

            refreshGridPanelInMainWindow();
            if (typeof listProcessPermissions !== "undefined") {
                winGrdpnlProcessPermissions.open();

                $(cboGroupOrUser.createHTML()).insertAfter(cboTargetTask.html);


                cboGroupOrUser.html.find("input").val("");
                jQuery(grdpnlProcessPermissions.html).css({
                    margin: "2px"
                });
                winGrdpnlProcessPermissions.body.style.height = "auto";
                $('#grdpnlProcessPermissions .pmui-textcontrol').css({'margin-top': '5px', width: '250px'});
                winGrdpnlProcessPermissions.defineEvents();
                applyStyleWindowForm(winGrdpnlProcessPermissions);

                grdpnlProcessPermissions.dom.toolbar.appendChild(btnCreate.getHTML());
                btnCreate.defineEvents();
                disableAllItems();
                refreshGridPanelInMainWindow();
            }
        };

        PMDesigner.processPermissions.create = function () {
            PMDesigner.processPermissions();

            frmProcessPermissions.reset();
            processPermissionsDataIni = {};

            processPermissionsDataIni.op_case_status = "ALL";
            processPermissionsDataIni.tas_uid = "";
            processPermissionsDataIni.usr_uid = "";
            processPermissionsDataIni.op_task_source = "";
            processPermissionsDataIni.op_participate = "0";
            processPermissionsDataIni.op_obj_type = "ANY";
            processPermissionsDataIni.op_obj_uid = "";
            processPermissionsDataIni.op_action = "VIEW";

            processPermissionsSetForm("POST", {});
        };
    }()
);
