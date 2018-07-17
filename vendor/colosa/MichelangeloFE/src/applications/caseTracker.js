(function () {
    PMDesigner.caseTracker = function () {
        var caseTrackerForm,
            index,
            flagEdit = 0,
            caseTrackerWindow,
            dataCaseTracker,
            dataTree,
            conditionform,
            conditionWindows,
            disableAllItems,
            formIsDirty,
            conditionformIsDirty,
            orderDataTree,
            saveItem,
            updateItem,
            treePanelObjects,
            loadGridCaseTacker,
            editCondition,
            gridPanelObjects,
            titleTreeObjects,
            getValuesCaseTrackerObjects,
            updateCaseTrackerPropertiesAndObjects,
            loadPropertiesCaseTracker,
            titleGridObjects,
            panelLabelObjects,
            panelContainerObjects,
            panelObjects,
            applyStylesWindow,
            showObjects,
            arrayObjectDropAssignedObjects = new Array(),
            arrayObjectAvailableObjects = new Array(),
            arrayObjectStepsCaseTracker = new Array();

        disableAllItems = function () {
            caseTrackerWindow.getItems()[0].setVisible(false);
            caseTrackerWindow.getItems()[1].setVisible(false);
            caseTrackerWindow.hideFooter();
        };
        formIsDirty = function () {
            if (caseTrackerForm.isDirty() || flagEdit == 1) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    windowMessageType: 'warning',
                    width: 490,
                    title: "Case Tracker".translate(),
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
                                caseTrackerWindow.close();
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                caseTrackerWindow.close();
            }
        };

        conditionformIsDirty = function () {
            if (conditionform.isDirty()) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    windowMessageType: 'warning',
                    width: 490,
                    title: "Case Tracker".translate(),
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
                                conditionWindows.close();
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                conditionWindows.close();
            }

        };
        orderDataTree = function (data) {
            var items = [];
            var type = ['DYNAFORM', 'INPUT_DOCUMENT', 'OUTPUT_DOCUMENT', 'EXTERNAL_STEP'];
            var label = ['Dynaform', 'Input Document', 'OutPut Document', 'External Step'];
            for (var i = 0; i < type.length; i += 1) {
                items = [];
                for (var j = 0; j < data.length; j += 1) {
                    if (type[i] === data[j].obj_type) {
                        items.push({
                            step_type_obj: label[i].translate(),
                            obj_label: label[i].translate(),
                            obj_title: data[j]['obj_title'],
                            obj_type: data[j]['obj_type'],
                            obj_uid: data[j]['obj_uid']
                        });
                    }
                }
                if (items.length === 0) {
                    dataTree.push({
                        obj_title: label[i].translate(),
                        items: []
                    });
                } else {
                    dataTree.push({
                        obj_title: label[i].translate(),
                        items: items
                    });
                }
            }
        };

        //Properties
        loadPropertiesCaseTracker = function () {
            dataCaseTracker = [];
            restClient = new PMRestClient({
                endpoint: 'case-tracker/property',
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    dataCaseTracker = response;
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        };

        updateCaseTrackerPropertiesAndObjects = function (data) {
            //save steps Objects Case Tracker
            var i, j;
            index = 0;
            for (i = 0; i < gridPanelObjects.getItems().length; i += 1) {
                idObject = (typeof gridPanelObjects.getItems()[i].getData().cto_uid_obj != 'undefined') ? gridPanelObjects.getItems()[i].getData().cto_uid_obj : gridPanelObjects.getItems()[i].getData().obj_uid;
                index = arrayObjectStepsCaseTracker.indexOf(idObject);
                if (index <= -1) {
                    saveItem(gridPanelObjects.getItems()[i]);
                } else {
                    updateItem(gridPanelObjects.getItems()[i], i);
                }
            }
            ;
            for (i = 0; i < arrayObjectAvailableObjects.length; i += 1) {
                for (j = 0; j < arrayObjectDropAssignedObjects.length; j += 1) {
                    index = (arrayObjectAvailableObjects[i] == arrayObjectDropAssignedObjects[j].cto_uid_obj) ? 0 : 1;
                    if (index == 0) {
                        restClient = new PMRestClient({
                            typeRequest: 'post',
                            multipart: true,
                            data: {
                                "calls": [
                                    {
                                        "url": 'case-tracker/object/' + arrayObjectDropAssignedObjects[j].cto_uid,
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

            //save Properties Case Tracker
            data ['map_type'] = data ['map_type'] == '["1"]' ? "PROCESSMAP" : "NONE";
            data ['routing_history'] = data ['routing_history'] == '["1"]' ? 1 : 0;
            data ['message_history'] = data ['message_history'] == '["1"]' ? 1 : 0;
            restClient = new PMRestClient({
                endpoint: 'case-tracker/property',
                typeRequest: 'update',
                data: data,
                functionSuccess: function (xhr, response) {
                    caseTrackerWindow.close();
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems updating the Case Tracker, please try again.".translate(),
                messageSuccess: 'Case Tracker updated successfully'.translate(),
                flashContainer: document.body
            });
            restClient.executeRestClient();
        };

        caseTrackerForm = new PMUI.form.Form({
            id: 'caseTrackerForm',
            fieldset: true,
            title: "",
            width: DEFAULT_WINDOW_WIDTH - 70,
            height: 30,
            layout: "hbox",
            items: [
                {
                    pmType: "annotation",
                    text: "Display :".translate(),
                    id: "DisplayMessage",
                    name: "DisplayMessage"
                },
                {
                    id: 'map_type',
                    pmType: 'checkbox',
                    labelVisible: false,
                    options: [
                        {
                            label: 'Processmap'.translate(),
                            value: '1'
                        }
                    ]
                },
                {
                    id: 'routing_history',
                    pmType: 'checkbox',
                    labelVisible: false,
                    options: [
                        {
                            label: 'Routing History'.translate(),
                            value: '1'
                        }
                    ]
                },
                {
                    id: 'message_history',
                    pmType: 'checkbox',
                    labelVisible: false,
                    options: [
                        {
                            id: 'message_history',
                            label: 'Messages'.translate(),
                            value: '1'
                        }
                    ]
                }
            ],
            style: {
                cssProperties: {
                    'margin-bottom': '70px'
                }
            }
        });

        //objects
        loadGridCaseTacker = function (data) {
            var i;
            for (i = 0; i < data.length; i += 1) {
                switch (data[i]['cto_type_obj']) {
                    case 'DYNAFORM':
                        label = 'Dynaform'.translate();
                        break;
                    case 'INPUT_DOCUMENT':
                        label = 'Input Document'.translate();
                        break;
                    case 'OUTPUT_DOCUMENT':
                        label = 'OutPut Document'.translate();
                        break;
                    case 'EXTERNAL_STEP':
                        label = 'External Step'.translate();
                        break;
                    default:
                        label = data[i]['tri_type'];
                        break;
                }
                data[i]['obj_label'] = label;
            }
            gridPanelObjects.setDataItems(data);
        };

        getValuesCaseTrackerObjects = function () {
            restClient = new PMRestClient({
                typeRequest: 'post',
                multipart: true,
                data: {
                    "calls": [
                        {
                            "url": "case-tracker/available-objects",
                            "method": 'GET'
                        },
                        {
                            "url": "case-tracker/objects",
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
        saveItem = function (rowStep) {
            rowStep = rowStep.getData();
            data = {
                "cto_type_obj": rowStep.obj_type,
                "cto_uid_obj": rowStep.obj_uid,
                "cto_condition": (typeof rowStep.cto_condition != 'undefined') ? rowStep.cto_condition : '',
                "cto_position": rowStep.cto_position
            };
            restClient = new PMRestClient({
                endpoint: 'case-tracker/object',
                typeRequest: 'post',
                data: data,
                functionSuccess: function (xhr, response) {
                    data = response;
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems saved, please try again.".translate()
            });
            restClient.executeRestClient();
            return data;
        };

        updateItem = function (rowStep, i) {
            rowStep = rowStep.getData();
            rowStep.cto_position = i + 1;
            restClient = new PMRestClient({
                typeRequest: 'post',
                multipart: true,
                data: {
                    "calls": [
                        {
                            "url": 'case-tracker/object/' + rowStep.cto_uid,
                            "method": 'PUT',
                            "data": rowStep
                        }
                    ]
                },
                functionSuccess: function (xhr, response) {
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
            filterPlaceholder: 'Search ...'.translate(),
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

        editCondition = function () {
            var visible, dataEdit;

            conditionform = new PMUI.form.Form({
                id: 'conditionform',
                title: "",
                fieldset: true,
                visibleHeader: false,
                width: 500,
                items: [
                    new CriteriaField({
                        id: 'cto_condition',
                        pmType: 'textarea',
                        name: 'cto_condition',
                        valueType: 'string',
                        label: 'Condition'.translate(),
                        placeholder: 'Insert a condition'.translate(),
                        rows: 200,
                        width: 250,
                        controlsWidth: 285,
                        renderType: 'textarea'
                    })
                ]
            });

            if (rowStep != '' && rowStep != undefined) {
                dataEdit = conditionform.getFields();
                dataEdit[0].setValue(rowStep['cto_condition']);
            }

            conditionWindows = new PMUI.ui.Window({
                id: 'conditionWindows',
                title: 'Condition'.translate(),
                width: 500,
                height: 'auto',
                footerHeight: 'auto',
                bodyHeight: 'auto',
                modal: true,
                buttonPanelPosition: 'bottom',
                footerAlign: "right",
                onBeforeClose: conditionformIsDirty,
                buttons: [
                    {
                        id: 'conditionObjectWindowButtonClose',
                        text: "Cancel".translate(),
                        handler: conditionformIsDirty,
                        buttonType: 'error'
                    },
                    {
                        id: 'conditionObjectWindowButtonSave',
                        text: "Save".translate(),
                        handler: function () {
                            var i, item;
                            if (conditionform.isValid()) {
                                idrowStep = (typeof rowStep.cto_uid_obj != 'undefined') ? rowStep.cto_uid_obj : rowStep.obj_uid;
                                for (i = 0; i < gridPanelObjects.getItems().length; i += 1) {
                                    item = gridPanelObjects.getItems()[i].getData();
                                    idObj = (typeof item.cto_uid_obj != 'undefined') ? item.cto_uid_obj : item.obj_uid;
                                    if (idObj == idrowStep) {
                                        rowStep.cto_condition = conditionform.getData()['cto_condition'];
                                        gridPanelObjects.getItems()[i].setData(rowStep);
                                        break;
                                    }
                                }
                                conditionWindows.close();
                            }
                        },
                        buttonType: 'success'
                    }
                ]
            });
            conditionWindows.addItem(conditionform);
            conditionWindows.open();
            conditionWindows.showFooter();
            applyStyleWindowForm(conditionWindows);
            conditionWindows.defineEvents();
            conditionWindows.footer.html.style.textAlign = 'right';
            conditionform.setFocus();
            style = $('#cto_condition .pmui-field-label').attr("style");
            style = style + ' float: left;';
            $('#cto_condition .pmui-field-label').attr("style", style);
        };

        gridPanelObjects = new PMUI.grid.GridPanel({
            id: 'gridPanelObjects',
            proportion: 1.5,
            visibleFooter: false,
            filterable: false,
            width: '640px',
            style: {cssClasses: ['itemsSteps']},
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
                    columnData: "obj_label",
                    sortable: false,
                    alignmentCell: 'left'
                },
                {
                    id: 'gridPanelObjectsButtonProperties',
                    title: '',
                    dataType: 'button',
                    buttonLabel: "Condition".translate(),
                    iconPosition: "center",
                    buttonStyle: {cssClasses: ['mafe-button-editstep']},
                    buttonTooltip: 'Edit Properties'.translate(),
                    onButtonClick: function (row, grid) {
                        rowStep = row.getData();
                        editCondition();
                    }
                },
                {
                    id: 'gridPanelObjectsButtonDelete',
                    title: '',
                    dataType: 'button',
                    buttonLabel: '',
                    buttonStyle: {cssClasses: ['mafe-button-delete-assign']},
                    buttonTooltip: 'Remove Object'.translate(),
                    onButtonClick: function (row, grid) {
                        flagEdit = 1;
                        rowStep = row.getData();
                        index = (row.getData().cto_uid_obj != 'undefined') ? arrayObjectStepsCaseTracker.indexOf(row.getData().cto_uid_obj) : arrayObjectStepsCaseTracker.indexOf(row.getData().obj_uid);
                        if (index > -1) {
                            arrayObjectAvailableObjects.push(row.getData().cto_uid_obj);
                            arrayObjectStepsCaseTracker.splice(index, 1);
                        }
                        objType = (typeof row.getData().cto_type_obj != 'undefined') ? row.getData().cto_type_obj : row.getData().obj_type;
                        grid.removeItem(row);
                        switch (objType) {
                            case 'DYNAFORM':
                                treePanelObjects.getItems()[0].addItem(row);
                                break;
                            case 'INPUT_DOCUMENT':
                                treePanelObjects.getItems()[1].addItem(row);
                                break;
                            case 'OUTPUT_DOCUMENT':
                                treePanelObjects.getItems()[2].addItem(row);
                                break;
                            case 'EXTERNAL_STEP':
                                treePanelObjects.getItems()[3].addItem(row);
                                break;
                            default:
                                break;
                        }
                    }
                }
            ],
            onDrop: function (grid, item, index) {
                flagEdit = 1;
                if (item.data.customKeys.obj_uid === '') {
                    return false;
                }
                rowStep = item.getData();
                rowStep.cto_position = index + 1;
                item.setData(rowStep);

                index = (typeof item.getData().cto_uid_obj != 'undefined') ? arrayObjectAvailableObjects.indexOf(item.getData().cto_uid_obj) : arrayObjectAvailableObjects.indexOf(item.getData().obj_uid);

                if (index > -1) {
                    itemPush = (typeof item.getData().cto_uid_obj != 'undefined') ? item.getData().cto_uid_obj : item.getData().obj_uid;
                    arrayObjectStepsCaseTracker.push(itemPush);
                    arrayObjectAvailableObjects.splice(index, 1);
                }
            },
            onSort: function (grid, item, index) {
                rowStep = item.getData();
            }
        });

        titleTreeObjects = new PMUI.ui.TextLabel({
            id: "titleTreeObjects",
            textMode: 'plain',
            text: 'Available Objects'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-steps-tree'
                ]
            }
        });

        titleGridObjects = new PMUI.ui.TextLabel({
            id: "titleGridObjects",
            textMode: 'plain',
            text: 'Assigned objects'.translate(),
            style: {
                cssClasses: [
                    'mafe-designer-stesp-grid'
                ]
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
                    'margin-bottom': 4,
                    'margin-top': 4,
                    'margin-left': 4
                }
            },
            layout: "hbox"
        });

        panelContainerObjects = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH * 0.94,
            height: 320,
            fieldset: true,
            items: [
                treePanelObjects,
                gridPanelObjects
            ],
            layout: "hbox"
        });

        panelObjects = new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH * 0.94,
            height: DEFAULT_WINDOW_HEIGHT * 0.70,
            fieldset: true,
            items: [
                panelLabelObjects,
                panelContainerObjects
            ],
            layout: "vbox"
        });

        caseTrackerWindow = new PMUI.ui.Window({
            id: 'caseTrackerWindow',
            title: "Case Tracker".translate(),
            width: DEFAULT_WINDOW_WIDTH,
            height: DEFAULT_WINDOW_HEIGHT,
            footerHeight: 'auto',
            bodyHeight: 'auto',
            modal: true,
            buttonPanelPosition: "bottom",
            onBeforeClose: formIsDirty,
            visibleFooter: false,
            footerAling: "right",
            footerItems: [{
                id: 'btnCloseCaseTracker',
                text: 'Cancel'.translate(),
                buttonType: "error",
                handler: formIsDirty
            },
                {
                    id: 'btnSaveCaseTracker',
                    text: "Save".translate(),
                    buttonType: "success",
                    handler: function () {
                        if (caseTrackerForm.isValid()) {
                            data = caseTrackerForm.getData();
                            updateCaseTrackerPropertiesAndObjects(data);
                        }
                    }
                }
            ],
            spaceButtons: 30
        });

        caseTrackerWindow.addItem(caseTrackerForm);
        caseTrackerWindow.addItem(panelObjects);
        caseTrackerWindow.open();
        caseTrackerWindow.showFooter();
        applyStyleWindowForm(caseTrackerWindow);
        caseTrackerWindow.defineEvents();
        caseTrackerWindow.footer.html.style.textAlign = 'right';
        caseTrackerForm.setFocus();

        applyStylesWindow = function () {
            $('#gridPanelObjects .pmui-gridpanel-tableContainer').css({'height': 'auto'});
            $('#caseTrackerForm :eq(2)').css({'padding': '0px 10px 0px 10px'});
            $('#caseTrackerForm :eq(0)').remove();
            items = caseTrackerWindow.getItems()[0].getItems();
            $(items[1].getHTML()).find('table').css('border', 'none');
            $(items[2].getHTML()).find('table').css('border', 'none');
            $(items[3].getHTML()).find('table').css('border', 'none');
        };

        showObjects = function () {
            disableAllItems();

            loadPropertiesCaseTracker();
            caseTrackerWindow.getItems()[0].setVisible(true);
            caseTrackerWindow.showFooter();
            caseTrackerWindow.setTitle("Case Tracker Properties".translate());

            if (dataCaseTracker != '') {
                var dataEdit = caseTrackerForm.getFields();
                dataEdit[1].setValue((dataCaseTracker['map_type'] == 'PROCESSMAP') ? '["1"]' : '[]');
                dataEdit[2].setValue((dataCaseTracker['routing_history'] == 1) ? '["1"]' : '[]');
                dataEdit[3].setValue((dataCaseTracker['message_history'] == 1) ? '["1"]' : '[]');
            }


            getValuesCaseTrackerObjects();
            caseTrackerWindow.getItems()[1].setVisible(true);
            caseTrackerWindow.setTitle("Case Tracker".translate());

            caseTrackerWindow.body.style.overflow = 'initial';

            gridPanelObjects.style.addProperties({overflow: 'auto'});
            gridPanelObjects.style.addProperties({float: 'right'});
            gridPanelObjects.setWidth(640);
            gridPanelObjects.setHeight(270);
            applyStylesWindow();
            treePanelObjects.style.addProperties({float: 'left'});
            treePanelObjects.style.addProperties({overflow: 'auto'});
            treePanelObjects.setWidth(200);
            treePanelObjects.setHeight(270);

            applyStyleTreePanel(treePanelObjects);

            panelLabelObjects.setHeight(15);
            caseTrackerWindow.defineEvents();

            caseTrackerWindow.setBodyPadding(5);

            panelObjects.style.addProperties({marginLeft: '15px'});
            for (var i = 0; i < gridPanelObjects.getItems().length; i += 1) {
                arrayObjectStepsCaseTracker[i] = gridPanelObjects.getItems()[i].getData().cto_uid_obj;
                arrayObjectDropAssignedObjects[i] = gridPanelObjects.getItems()[i].getData();
            }
        };
        showObjects();
    };

    PMDesigner.caseTracker.showObjects = function () {
        PMDesigner.caseTracker();
    };

}());