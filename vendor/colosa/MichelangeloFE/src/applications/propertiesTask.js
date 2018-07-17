/**
 * new stepTask module
 */
var stepsTask = function (activity) {
    this.mainWindow = null;
    this.secondaryWindow = null;
    this.stepsAssignTree = null;
    this.mainContainer = null;
    this.labelsPanel = null;
    this.stepsMainContainer = null;
    this.stepsAssignAccordion = null;
    this.confirmWindow = null;
    this.elementAccordionOpen = null;
    this.groupType = null;
    this.groupLabel = null;
    this.stepsType = null;
    this.stepsAssigned = null;
    stepsTask.prototype.initialize.call(this, activity);
};
/**
 * initialization method steps settings, constants are created.
 * components are created
 * the data is loaded
 * styles are customized
 * It extends behaviors and events
 */
stepsTask.prototype.initialize = function () {
    this.groupType = ['DYNAFORM', 'INPUT_DOCUMENT', 'OUTPUT_DOCUMENT', 'EXTERNAL'];
    this.groupLabel = ['Dynaform (s)'.translate(), 'Input Document (s)'.translate(), 'OutPut Document (s)'.translate(), 'External (s)'.translate()];
    this.stepsType = {
        "DYNAFORM": "Dynaform".translate(),
        "INPUT_DOCUMENT": "Input Document".translate(),
        "OUTPUT_DOCUMENT": "Output Document".translate(),
        "EXTERNAL": "External".translate()
    };
    this.stepsAssigned = new PMUI.util.ArrayList();
    this.elementAccordionOpen = new PMUI.util.ArrayList();
    this.createWidgets();
    this.mainWindow.addItem(this.mainContainer);
    this.mainWindow.open();
    this.loadInitialData();
    this.customStyles();
    this.elementsAccordionOpenFixed();
    this.addEventSortableInAccordionElements();
    this.addEventSortableInTreePanelElements();
};
/**
 * the components are created PMUI UI
 * - confirmWindow
 * - mainWindow
 * - labelsPanel
 * - stepsAssignTree
 * - stepsAssignAccordion
 * - stepsMainContainer
 * - mainContainer
 * - secondaryWindow
 */
stepsTask.prototype.createWidgets = function () {
    var that = this;
    this.confirmWindow = new PMUI.ui.MessageWindow({
        id: 'confirmWindowDeleteAcceptedValue',
        windowMessageType: 'warning',
        width: 490,
        bodyHeight: 'auto',
        title: '',
        message: '',
        footerItems: [
            {
                id: 'confirmWindow-footer-no',
                text: 'No'.translate(),
                visible: true,
                buttonType: "error"
            }, {
                id: 'confirmWindow-footer-yes',
                text: 'Yes'.translate(),
                visible: true,
                buttonType: "success"
            }
        ],
        visibleFooter: true
    });
    this.mainWindow = new PMUI.ui.Window({
        id: "stepsForTask",
        title: "Steps for task".translate(),
        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH
    });
    this.labelsPanel = new PMUI.core.Panel({
        width: DEFAULT_WINDOW_WIDTH,
        proportion: 0.08,
        layout: "hbox",
        items: [
            new PMUI.field.TextAnnotationField({
                text: 'Available Elements'.translate(),
                text_Align: 'center',
                proportion: 1.4
            }),
            new PMUI.field.TextAnnotationField({
                text: 'Assigned Elements (Drop here)'.translate(),
                text_Align: 'center',
                proportion: 1.3
            }),
            new PMUI.ui.Button({
                buttonType: "link",
                "text": "Expand all".translate(),
                id: "expand-button",
                proportion: 0.6,
                handler: function () {
                    var items, i, item, buttonAfected;
                    items = that.stepsAssignAccordion.getItems();
                    buttonAfected = that.labelsPanel.getItem("collapse-button");
                    buttonAfected.setDisabled(false);
                    this.setDisabled(true);
                    that.elementAccordionOpen.clear();
                    for (i = 0; i < items.length; i += 1) {
                        item = items[i];
                        item.expand();
                        that.elementAccordionOpen.insert(item);
                    }
                }
            }),
            new PMUI.ui.Button({
                buttonType: "link",
                "text": "Collapse all".translate(),
                id: "collapse-button",
                proportion: 0.7,
                disabled: true,
                handler: function () {
                    var items, i, item, buttonAfected;
                    buttonAfected = that.labelsPanel.getItem("expand-button");
                    buttonAfected.setDisabled(false);
                    this.setDisabled(true);
                    items = that.stepsAssignAccordion.getItems();
                    for (i = 0; i < items.length; i += 1) {
                        item = items[i];
                        that.elementAccordionOpen.remove(item);
                        item.collapse();
                    }
                }
            })
        ]
    });
    this.stepsAssignTree = new PMUI.panel.TreePanel({
        id: 'stepsAssignTree',
        proportion: 0.5,
        height: 475,
        filterable: true,
        autoBind: true,
        filterPlaceholder: 'Search ...'.translate(),
        emptyMessage: 'No records found'.translate(),
        nodeDefaultSettings: {
            labelDataBind: 'obj_title',
            autoBind: true,
            collapsed: false,
            itemsDataBind: 'items',
            childrenDefaultSettings: {
                labelDataBind: 'obj_title',
                autoBind: true
            },
            behavior: 'drag'
        },
        style: {
            cssProperties: {
                margin: '0px 0px 0px 0px',
                float: 'left',
                overflow: 'auto'
            },
            cssClasses: ['mafe-border-panel']
        }
    });
    this.stepsAssignAccordion = new PMUI.panel.AccordionPanel({
        id: 'stepsAssignAccordion',
        multipleSelection: true,
        hiddenTitle: true,
        proportion: 1.5,
        style: {
            cssProperties: {
                margin: '0px 0px 0px 0px'
            },
            cssClasses: ['mafe-border-panel']
        },
        listeners: {
            select: function (accordionItem, event) {
                var buttonExpand, buttonCollapse, itemsAccod;
                itemsAccod = that.stepsAssignAccordion.items;
                if (accordionItem.collapsed) {
                    if (that.elementAccordionOpen.indexOf(accordionItem) > -1) {
                        that.elementAccordionOpen.remove(accordionItem);
                    }
                } else {
                    if (that.elementAccordionOpen.indexOf(accordionItem) === -1) {
                        that.elementAccordionOpen.insert(accordionItem);
                    }
                }
                buttonCollapse = that.labelsPanel.getItem("collapse-button");
                buttonExpand = that.labelsPanel.getItem("expand-button");
                if (that.elementAccordionOpen.getSize() === 0) {
                    buttonExpand.setDisabled(false);
                    buttonCollapse.setDisabled(true);
                } else if (that.elementAccordionOpen.getSize() === itemsAccod.getSize()) {
                    buttonExpand.setDisabled(true);
                    buttonCollapse.setDisabled(false);
                } else {
                    buttonExpand.setDisabled(false);
                    buttonCollapse.setDisabled(false);
                }
            }
        }
    });
    this.stepsMainContainer = new PMUI.core.Panel({
        id: "stepsMainContainer",
        width: DEFAULT_WINDOW_WIDTH,
        height: DEFAULT_WINDOW_HEIGHT - 45,
        layout: 'hbox',
        height: 475,
        items: [
            this.stepsAssignTree,
            this.stepsAssignAccordion
        ]
    });
    this.mainContainer = new PMUI.core.Panel({
        id: "mainContainer",
        width: DEFAULT_WINDOW_WIDTH,
        height: DEFAULT_WINDOW_HEIGHT - 45,
        layout: 'vbox',
        items: [
            this.labelsPanel,
            this.stepsMainContainer
        ]
    });
    this.secondaryWindow = new PMUI.ui.Window({
        visibleFooter: true,
        title: 'Trigger'.translate(),
        footerAlign: 'right',
        footerItems: [
            {
                text: "@@",
                id: "secondaryWindow-criteria",
                handler: function () {
                },
                style: {
                    cssProperties: {
                        "background": "rgb(45, 62, 80)",
                        "border": "1px solid rgb(45, 62, 80)"
                    },
                    cssClasses: ["mafe-button-condition-trigger"]
                }
            }, {
                id: 'secondaryWindow-cancel',
                text: 'Cancel'.translate(),
                buttonType: 'error',
                height: 31,
                style: {
                    cssClasses: ["mafe-button-condition-trigger"]
                },
                handler: function () {
                }
            }, {
                id: 'secondaryWindow-save',
                text: 'Save'.translate(),
                buttonType: 'success',
                height: 31,
                style: {
                    cssClasses: ["mafe-button-condition-trigger"]
                },
                handler: function () {
                }
            }
        ]
    });
};
/**
 * This method loads the initial data module steps,for the components:
 * - stepsAssignTree
 * - stepsAssignAccordion
 */
stepsTask.prototype.loadInitialData = function () {
    this.loadTreePanelData(this.getTreePanelData());
    this.loadAccordionItems(this.getAccordionData());
};
/**
 * This method loads the options to stepsAssignAccordion
 * @param {Array} response An array where each element can be a {Element} object or a JSON object
 */
stepsTask.prototype.loadAccordionItems = function (response) {
    var firstResp = [],
        secondResp = [],
        i,
        item,
        assigmentConfig,
        firstRes = 0,
        secondRes = 1;
    if (jQuery.isArray(response) && response.length) {
        if (typeof response[firstRes] === "object") {
            firstResp = response[firstRes].response ? response[firstRes].response : [];
        }
        if (typeof response[secondRes] === "object") {
            secondResp = response[secondRes].response ? response[secondRes].response : [];
        }
    }
    if (firstResp.length) {
        for (i = 0; i < firstResp.length; i += 1) {
            item = this.createAccordionItem(firstResp[i], true, true);
            this.stepsAssignAccordion.addItem(item);
            item.dataItem = firstResp[i];
            this.customAccordionItemButtons(item.html, firstResp[i], item);
        }
    }
    assigmentConfig = {
        step_type_obj: "Assignment".translate(),
        triggers: secondResp,
        st_type: "ASSIGNMENT",
        obj_title: "Assignment".translate(),
        step_uid_obj: "Assignment"
    };
    item = this.createAccordionItem(assigmentConfig);
    this.stepsAssignAccordion.addItem(item);
    item.dataItem = assigmentConfig;
    assigmentConfig = {
        step_type_obj: "Routing".translate(),
        triggers: secondResp,
        obj_title: "Routing".translate(),
        st_type: 'ROUTING',
        step_uid_obj: "Routing"
    };
    item = this.createAccordionItem(assigmentConfig);
    this.stepsAssignAccordion.addItem(item);
    item.dataItem = assigmentConfig;
    this.stepsAssignAccordion.defineEvents();
};
/**
 * This method creates an element for stepsAssignAccordion
 * @param {data} It is an object with the settings to create the element
 * @returns {PMUI.item.AccordionItem}
 */
stepsTask.prototype.createAccordionItem = function (data) {
    var that = this,
        gridBefore,
        gridAfter,
        beforeTitle,
        afterTitle,
        i,
        textLabel;
    if (this.stepsType[data.step_type_obj]) {
        textLabel = this.stepsType[data.step_type_obj];
    } else {
        textLabel = data.step_type_obj;
    }
    beforeTitle = new PMUI.field.TextAnnotationField({
        text: 'Before'.translate() + ' ' + textLabel,
        proportion: 0.5,
        text_Align: 'left'
    });
    afterTitle = new PMUI.field.TextAnnotationField({
        text: 'After'.translate() + ' ' + textLabel,
        proportion: 0.5,
        text_Align: 'left',
        visible: data.st_type === "ASSIGNMENT" ? false : true
    });

    gridBefore = new PMUI.grid.GridPanel({
        behavior: 'dragdropsort',
        filterable: false,
        visibleHeaders: false,
        data: data.triggers,
        st_type: 'BEFORE',
        step_uid: data.step_uid,
        visibleFooter: false,
        width: '96%',
        emptyMessage: 'No records found'.translate(),
        style: {
            cssClasses: ['mafe-gridPanel']
        },
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
        },
        columns: [
            {
                title: '',
                dataType: 'string',
                alignmentCell: 'center',
                columnData: "st_position",
                width: 20
            }, {
                title: 'Before Output Document'.translate(),
                dataType: 'string',
                alignmentCell: 'left',
                columnData: 'tri_title',
                width: 360
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: function (row, data) {
                    return data.st_condition === '' ? 'Condition'.translate() : 'Condition *'.translate();
                },
                buttonStyle: {cssClasses: ['mafe-button-edit']},
                onButtonClick: function (row, grid) {
                    var data = row.getData();
                    that.editCondition(grid.step_uid, data.tri_uid, data.st_type, row);
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Edit'.translate(),
                buttonStyle: {cssClasses: ['mafe-button-edit']},
                onButtonClick: function (row, grid) {
                    var data = row.getData(),
                        restClient;
                    restClient = new PMRestClient({
                        endpoint: 'trigger/' + data.tri_uid,
                        typeRequest: 'get',
                        functionSuccess: function (xhr, response) {
                            that.editTrigger(response.tri_webbot, response.tri_uid);
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    });
                    restClient.executeRestClient();
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Remove'.translate(),
                buttonStyle: {cssClasses: ['mafe-button-delete']},
                onButtonClick: function (row, grid) {
                    that.removeTrigger(row, grid);
                }
            }
        ],
        onDrop: function (container, draggableItem, index) {
            var receiveData = draggableItem.getData();
            if (draggableItem instanceof PMUI.item.TreeNode) {
                that.receiveTreeNodeItem(receiveData, this, index);
            } else {
                that.receiveRowItem(receiveData, this, index, draggableItem);
            }
            that.updateIndexToGrid(this);
            return false;
        },
        onSort: function (container, item, index) {
            var receiveData = item.getData();
            that.sortableRowHandler(receiveData, this, index);
            that.updateIndexToGrid(this);
        }
    });
    if (data.st_type !== "ROUTING" && data.st_type !== "ASSIGNMENT") {
        gridBefore.st_type = 'BEFORE';
    } else if (data.st_type === "ROUTING") {
        gridBefore.st_type = "BEFORE_ROUTING";
    } else {
        gridBefore.st_type = "BEFORE_ASSIGNMENT";
    }
    gridBefore.step_uid = data.step_uid;
    gridBefore.clearItems();
    if (jQuery.isArray(data.triggers)) {
        for (i = 0; i < data.triggers.length; i += 1) {
            if (gridBefore.st_type === data.triggers[i].st_type) {
                gridBefore.addDataItem({
                    st_condition: data.triggers[i].st_condition,
                    st_position: data.triggers[i].st_position,
                    st_type: data.triggers[i].st_type,
                    tri_description: data.triggers[i].tri_description,
                    tri_title: data.triggers[i].tri_title,
                    tri_uid: data.triggers[i].tri_uid,
                    obj_title: data.triggers[i].tri_title,
                    obj_uid: data.triggers[i].tri_uid
                });
            }
        }
    }
    gridAfter = new PMUI.grid.GridPanel({
        behavior: 'dragdropsort',
        filterable: false,
        visibleHeaders: false,
        data: data.triggers,
        visibleFooter: false,
        width: '96%',
        visible: data.st_type === "ASSIGNMENT" ? false : true,
        emptyMessage: 'No records found'.translate(),
        style: {
            cssClasses: ['mafe-gridPanel']
        },
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
        },
        columns: [
            {
                title: '',
                dataType: 'string',
                alignmentCell: 'center',
                columnData: 'st_position',
                width: 20
            }, {
                title: 'Before Output Document'.translate(),
                dataType: 'string',
                alignmentCell: 'left',
                columnData: 'tri_title',
                width: 360
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: function (row, data) {
                    return data.st_condition === '' ? 'Condition'.translate() : 'Condition *'.translate();
                },
                buttonStyle: {cssClasses: ['mafe-button-edit']},
                onButtonClick: function (row, grid) {
                    var data = row.getData();
                    that.editCondition(grid.step_uid, data.tri_uid, data.st_type, row);
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Edit'.translate(),
                buttonStyle: {cssClasses: ['mafe-button-edit']},
                onButtonClick: function (row, grid) {
                    var data = row.getData(),
                        restClient;
                    restClient = new PMRestClient({
                        endpoint: 'trigger/' + data.tri_uid,
                        typeRequest: 'get',
                        functionSuccess: function (xhr, response) {
                            that.editTrigger(response.tri_webbot, response.tri_uid);
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    });
                    restClient.executeRestClient();
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Remove'.translate(),
                buttonStyle: {cssClasses: ['mafe-button-delete']},
                onButtonClick: function (row, grid) {
                    that.removeTrigger(row, grid);
                }
            }
        ],
        onDrop: function (container, draggableItem, index) {
            var receiveData = draggableItem.getData();
            if (draggableItem instanceof PMUI.item.TreeNode) {
                that.receiveTreeNodeItem(receiveData, this, index);
            } else {
                that.receiveRowItem(receiveData, this, index, draggableItem);
            }
            that.updateIndexToGrid(this);
            return false;
        },
        onSort: function (container, item, index) {
            var receiveData = item.getData();
            that.sortableRowHandler(receiveData, this, index);
            that.updateIndexToGrid(this);
        }
    });
    if (data.st_type !== "ROUTING" && data.st_type !== "ASSIGNMENT") {
        gridAfter.st_type = 'AFTER';
    } else if (data.st_type == "ROUTING") {
        gridAfter.st_type = "AFTER_ROUTING";
    } else {
        gridAfter.st_type = "AFTER_ASSIGNMENT";
    }
    gridAfter.step_uid = data.step_uid;
    if (jQuery.isArray(data.triggers)) {
        for (i = 0; i < data.triggers.length; i += 1) {
            if (gridAfter.st_type === data.triggers[i].st_type) {
                gridAfter.addDataItem({
                    st_condition: data.triggers[i].st_condition,
                    st_position: data.triggers[i].st_position,
                    st_type: data.triggers[i].st_type,
                    tri_description: data.triggers[i].tri_description,
                    tri_title: data.triggers[i].tri_title,
                    tri_uid: data.triggers[i].tri_uid,
                    obj_title: data.triggers[i].tri_title,
                    obj_uid: data.triggers[i].tri_uid
                });
            }
        }
    }
    var accordionItem = new PMUI.item.AccordionItem({
        id: 'id' + data.step_uid_obj,
        dataStep: data,
        closeable: true,
        body: new PMUI.core.Panel({
            layout: 'vbox',
            items: [
                beforeTitle,
                gridBefore,
                afterTitle,
                gridAfter
            ]
        })
    });
    if (this.stepsType[data.step_type_obj]) {
        accordionItem.setTitle(data.step_position + ".  " + data.obj_title + ' (' + this.stepsType[data.step_type_obj] + ')');
        this.stepsAssigned.insert(accordionItem);
    } else {
        accordionItem.setTitle((this.stepsAssignAccordion.items.getSize() + 1) + ". " + data.obj_title);
    }
    return accordionItem;
};
/**
 * styles that can not be handled with the library are customized PMUI
 * @chainable
 */
stepsTask.prototype.customStyles = function () {
    this.mainWindow.body.style.overflow = "hidden";
};
/**
 * run the endpoint 'activity/{activity_id}/available-steps' to get
 * dynaforms, output document, input Document and external, Unassigned or Availables
 * @returns {Array}
 */
stepsTask.prototype.getStepAvailables = function () {
    var resp = [];
    restClient = new PMRestClient({
        typeRequest: 'post',
        multipart: true,
        data: {
            calls: [
                {
                    url: 'activity/' + PMDesigner.act_uid + '/available-steps',
                    method: 'GET'
                }
            ]
        },
        functionSuccess: function (xhr, response) {
            if (jQuery.isArray(response)) {
                resp = response[0] ? response[0].response : [];
            }
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restClient.executeRestClient();
    return resp;
};
/**
 * run the endpoint 'activity/{activity_id}/available-steps' to get all Availables
 * triggres and dynaforms Unassigned or Availables
 * @returns {Array}
 */
stepsTask.prototype.getTreePanelData = function () {
    var resp = [];
    restClient = new PMRestClient({
        typeRequest: 'post',
        multipart: true,
        data: {
            calls: [{
                url: 'activity/' + PMDesigner.act_uid + '/available-steps',
                method: 'GET'
            }, {
                url: 'triggers',
                method: 'GET'
            }
            ]
        },
        functionSuccess: function (xhr, response) {
            resp = response;
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restClient.executeRestClient();
    return resp;
}
/**
 * this method loads the data to stepsAssignTree
 * @param response, the answer is an array containing all the elements
 * that will be loaded into the step stepsAssignTree
 * @chainable
 */
stepsTask.prototype.loadTreePanelData = function (response) {
    var that = this,
        data,
        i,
        j,
        type,
        label,
        items,
        labelTrigger,
        dataTree = [],
        treeNode;
    data = response[1].response;
    labelTrigger = 'Trigger (s)'.translate();
    if (data.length === 0) {
        dataTree.push({
            obj_title: labelTrigger,
            items: [this.notItemConfig()]
        });
    } else {
        items = [];
        for (i = 0; i < data.length; i += 1) {
            items.push({
                obj_title: data[i]['tri_title'],
                obj_type: data[i]['tri_type'],
                obj_uid: data[i]['tri_uid']
            });
        }
        dataTree.push({
            obj_title: labelTrigger,
            items: items,
            id: "TRIGGER"
        });
    }
    data = response[0].response;
    type = this.groupType;
    label = this.groupLabel;
    items = [];
    for (i = 0; i < type.length; i += 1) {
        items = [];
        for (j = 0; j < data.length; j += 1) {
            if (type[i] === data[j].obj_type) {
                items.push({
                    obj_title: data[j]['obj_title'],
                    obj_type: data[j]['obj_type'],
                    obj_uid: data[j]['obj_uid']
                });
            }
        }
        if (items.length === 0) {
            dataTree.push({
                obj_title: label[i].translate(),
                items: [this.notItemConfig()],
                behavior: '',
                id: type[i]
            });
        } else {
            dataTree.push({
                obj_title: label[i].translate(),
                items: items,
                behavior: 'drag',
                id: type[i]
            });
        }
    }
    this.stepsAssignTree.clearItems();
    for (i = 0; i < dataTree.length; i += 1) {
        this.stepsAssignTree.addDataItem(dataTree[i]);
        treeNode = this.stepsAssignTree.getItem(i);
        treeNode.setID(dataTree[i].id);
        this.updateIndexPosition(treeNode);
    }
    return this;
};
/**
 * run the endpoint 'activity/{activity_id}/steps' and 'activity/{activity_id}/step/triggers'
 * to get all triggres and dynaforms assigned
 * @returns {Array}
 */
stepsTask.prototype.getAccordionData = function () {
    var resp = [],
        restClient = new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'activity/' + PMDesigner.act_uid + '/steps',
                        method: 'GET'
                    }, {
                        url: 'activity/' + PMDesigner.act_uid + '/step/triggers',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                resp = response;
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            },
            messageError: 'There are problems getting the Steps, please try again.'.translate()
        });
    restClient.executeRestClient();
    return resp;
};
/**
 * checks whether a trigger is already assigned in a grid
 * @param grid, is instanceof PMUI.grid.Grid, in conducting the search
 * @param tri_uid, search parameter in the rows of the grid
 * @returns {boolean}
 */
stepsTask.prototype.isTriggerAssigned = function (grid, tri_uid) {
    var data, i, exist = false;
    data = grid.getData();
    if (grid && jQuery.isArray(data)) {
        for (i = 0; i < data.length; i += 1) {
            if (data[i].tri_uid === tri_uid) {
                exist = true;
                break;
            }
        }
    }
    return exist;
};
/**
 * retorna el tipo de de step, para la ejecucion de "endpoint"
 * @param st_type, this a step type, the accepted parameters are:
 *  - BEFORE_ASSIGNMENT
 *  - BEFORE_ROUTING
 *  - AFTER_ROUTING
 *  - BEFORE
 *  - AFTER
 * @returns {string}
 */
stepsTask.prototype.getStepType = function (st_type) {
    var value;
    switch (st_type) {
        case 'BEFORE_ASSIGNMENT':
            value = 'before-assignment';
            break;
        case 'BEFORE_ROUTING':
            value = 'before-routing';
            break;
        case 'AFTER_ROUTING':
            value = 'after-routing';
            break;
        case 'BEFORE':
            value = 'before';
            break;
        case 'AFTER':
            value = 'after';
            break;
        default:
            value = '';
            break;
    }
    return value;
};
/**
 * This method is executed when editing a "trigger" in a row of the grid.
 * secondary window opens with the current configuration of the trigger
 * @param trigger, is the return value when is update 'trigger' action  in the enpoint
 * @param triggerID, is the id of the trigger to update
 * @chainable
 */
stepsTask.prototype.editTrigger = function (trigger, triggerID) {
    var codeMirror,
        saveButton,
        cancelButton,
        criteriaButton,
        that = this;
    this.resetSecondaryWindow();
    codeMirror = new PMCodeMirror({
        id: "codeMirror"
    });
    CodeMirror.commands.autocomplete = function (cm) {
        CodeMirror.showHint(cm, CodeMirror.phpHint);
    };
    codeMirror.setValue(trigger);
    this.secondaryWindow.setWidth(DEFAULT_WINDOW_WIDTH);
    this.secondaryWindow.setHeight(DEFAULT_WINDOW_HEIGHT);
    this.secondaryWindow.setTitle("Trigger".translate());
    saveButton = this.secondaryWindow.footer.getItem("secondaryWindow-save");
    cancelButton = this.secondaryWindow.footer.getItem("secondaryWindow-cancel");
    criteriaButton = this.secondaryWindow.footer.getItem("secondaryWindow-criteria");
    if (saveButton) {
        saveButton.setHandler(function () {
            var restClient = new PMRestClient({
                endpoint: 'trigger/' + triggerID,
                typeRequest: 'update',
                data: {
                    tri_param: '',
                    tri_webbot: codeMirror.getValue()
                },
                messageError: 'There are problems updating the trigger, please try again.'.translate(),
                messageSuccess: 'Trigger updated correctly'.translate(),
                flashContainer: that.mainWindow,
                functionSuccess: function () {
                    that.secondaryWindow.close();
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        });
    }
    if (cancelButton) {
        cancelButton.setHandler(function () {
            that.secondaryWindow.close();
        });
    }
    if (criteriaButton) {
        criteriaButton.setVisible(true);
        criteriaButton.setHandler(function () {
            var picker = new VariablePicker();
            picker.open({
                success: function (variable) {
                    var cursorPos,
                        codemirror;
                    codemirror = codeMirror.cm;
                    cursorPos = codemirror.getCursor();
                    codemirror.replaceSelection(variable);
                    codemirror.setCursor(cursorPos.line, cursorPos.ch);
                }
            });
        });
    }
    this.secondaryWindow.open();
    this.secondaryWindow.addItem(codeMirror);
    codeMirror.cm.setSize(this.secondaryWindow.getWidth(), 380);
    $(".CodeMirror.cm-s-default.CodeMirror-wrap").after($ctrlSpaceMessage.css({
        "padding-left": "10px",
        "margin": "3px 0px 0px 0px"
    }));
    $(".pmui-window-body").css("overflow", "hidden");
};
/**
 * edit the selected trigger condition
 * @param stepID, It is the id of the step to upgrade
 * @param triggerID, is the id of the trigger to update
 * @param stepType, It is the kind of step to update
 * @param row, PMUI.grid.GridPanelRow, is the row affected
 */
stepsTask.prototype.editCondition = function (stepID, triggerID, stepType, row) {
    var saveButton,
        cancelButton,
        criteriaButton,
        form,
        dataRow,
        that = this;
    dataRow = row.getData();
    this.resetSecondaryWindow();
    this.secondaryWindow.setWidth(500);
    this.secondaryWindow.setHeight(350);
    this.secondaryWindow.setTitle('Condition Trigger'.translate());
    this.secondaryWindow.setTitle("Trigger".translate());
    form = new PMUI.form.Form({
        id: 'idFormEditCondition',
        width: 500,
        title: 'Condition Trigger'.translate(),
        visibleHeader: false,
        items: [
            new CriteriaField({
                id: 'st_condition',
                pmType: 'textarea',
                name: 'st_condition',
                valueType: 'string',
                label: 'Condition'.translate(),
                placeholder: 'Insert a condition'.translate(),
                rows: 150,
                controlsWidth: 250,
                renderType: 'textarea',
                value: dataRow.st_condition
            })
        ]
    });
    this.secondaryWindow.addItem(form);
    saveButton = this.secondaryWindow.footer.getItem("secondaryWindow-save");
    cancelButton = this.secondaryWindow.footer.getItem("secondaryWindow-cancel");
    criteriaButton = this.secondaryWindow.footer.getItem("secondaryWindow-criteria");
    if (saveButton) {
        saveButton.setHandler(function () {
            var data,
                restClient;
            data = form.getData();
            data.st_type = stepType;
            restClient = new PMRestClient({
                endpoint: 'activity/' + PMDesigner.act_uid + '/step/' + ((typeof(stepID) != "undefined") ? stepID + "/" : "") + 'trigger/' + triggerID,
                typeRequest: 'update',
                data: data,
                messageError: 'There are problems update the Step Trigger, please try again.'.translate(),
                functionSuccess: function (xhr, response) {
                    dataRow.st_condition = data.st_condition;
                    row.setData(dataRow);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
            that.secondaryWindow.close();
        });
    }
    if (cancelButton) {
        cancelButton.setHandler(function () {
            that.secondaryWindow.close();
        });
    }
    if (criteriaButton) {
        criteriaButton.setVisible(false);
        criteriaButton.handler = null;
    }
    this.secondaryWindow.open();
};
/**
 * eliminates the elements of the secondary window
 * @chainable
 */
stepsTask.prototype.resetSecondaryWindow = function () {
    var i, items;
    if (this.secondaryWindow && this.secondaryWindow.items.getSize() > 0) {
        items = this.secondaryWindow.items;
        for (i = 0; i < items.getSize(); i += 1) {
            this.secondaryWindow.removeItem(items.get(i));
        }
    }
};
/**
 * It establishes a PMUI.util.ArrayList that stores the
 * elements of "stepsAssignAccordion" that are open
 * @chainable
 */
stepsTask.prototype.elementsAccordionOpenFixed = function () {
    var i,
        accordionItems;
    if (this.stepsAssignAccordion) {
        accordionItems = this.stepsAssignAccordion.getItems();
        if ($.isArray(accordionItems)) {
            for (i = 0; i < accordionItems.length; i += 1) {
                if (!accordionItems[i].collapsed) {
                    this.elementAccordionOpen.insert(accordionItems[i]);
                }
            }
        }
    }
};
/**
 * It is an extension to add the "sortable" event "stepAssignAccordion".
 * when a node "treePanel" is added to stop runs and is where you choose if it's a sort or aggregation.
 * @chainable
 */
stepsTask.prototype.addEventSortableInAccordionElements = function () {
    var tagContainer,
        newIndex,
        index,
        treeNodeObject,
        treeNodeData,
        that = this;
    if (this.stepsAssignAccordion && this.stepsAssignAccordion.html) {
        tagContainer = this.stepsAssignAccordion.body;
        $(tagContainer).sortable({
            items: '>div:not(#idAssignment,#idRouting)',
            placeholder: 'steps-placeholder',
            receive: function (event, ui) {
                var item = ui ? ui.item : null;
                if (item && item instanceof jQuery && item.length) {
                    treeNodeObject = PMUI.getPMUIObject(item.get(0));
                    treeNodeData = treeNodeObject.getData();
                }
            },
            stop: function (event, ui) {
                var itemClone = ui ? ui.item : null,
                    accordionItems,
                    accordionItem,
                    dataEdited,
                    restClientMultipart,
                    restClient;
                var newIndex = ui.item.index();
                accordionItems = that.stepsAssignAccordion.getItems();
                if (itemClone && itemClone instanceof jQuery && itemClone.length) {
                    if (treeNodeObject) {
                        itemClone.remove();
                        if (newIndex + 1 > accordionItems.length) {
                            newIndex = that.stepsAssigned.getSize();
                        }
                        restClient = new PMRestClient({
                            endpoint: 'activity/' + PMDesigner.act_uid + '/step',
                            typeRequest: 'post',
                            data: {
                                step_type_obj: treeNodeData.obj_type,
                                step_uid_obj: treeNodeData.obj_uid,
                                step_condition: '',
                                step_position: newIndex + 1,
                                step_mode: 'EDIT'
                            },
                            functionSuccess: function (xhr, response) {
                                var item, buttonAfected, treeNode;
                                that.stepsAssignTree.removeItem(treeNodeObject);
                                treeNode = that.stepsAssignTree.items.find("id", response.step_type_obj);
                                if (treeNode.items.getSize() === 0) {
                                    treeNode.addDataItem(that.notItemConfig());
                                }
                                response.obj_description = '';
                                response.obj_title = treeNodeData.obj_title;
                                response.triggers = [];
                                item = that.createAccordionItem(response, true, true);
                                item.dataItem = response;
                                if (that.stepsAssignAccordion.items.getSize() === 2) {
                                    that.stepsAssignAccordion.addItem(item, 0);
                                } else {
                                    that.stepsAssignAccordion.addItem(item, newIndex);
                                }
                                that.stepsAssignAccordion.defineEvents();
                                that.customAccordionItemButtons(item.html, response, item);
                                that.updateItemIndexToAccordion();
                                that.addEventSortableInAccordionElements();
                                that.addEventSortableInTreePanelElements();
                                buttonAfected = that.labelsPanel.getItem("expand-button");
                                buttonAfected.setDisabled(false);
                            },
                            functionFailure: function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            },
                            messageError: 'An unexpected error while assigning the step, please try again later.'.translate(),
                            messageSuccess: 'Step assigned successfully.'.translate(),
                            flashContainer: that.stepsAssignAccordion.getParent()
                        });
                        restClient.executeRestClient();
                    } else {
                        accordionItem = PMUI.getPMUIObject(ui.item.get(0));
                        index = that.stepsAssignAccordion.items.indexOf(accordionItem);
                        if (newIndex !== index) {
                            that.stepsAssignAccordion.items.remove(accordionItem);
                            that.stepsAssignAccordion.items.insertAt(accordionItem, newIndex);
                            dataEdited = {
                                step_position: newIndex + 1,
                                step_uid: accordionItem.dataItem.step_uid,
                                step_type_obj: accordionItem.dataItem.step_type_obj,
                                step_uid_obj: accordionItem.dataItem.step_uid_obj
                            };
                            restClientMultipart = new PMRestClient({
                                endpoint: 'activity/' + PMDesigner.act_uid + '/step/' + accordionItem.dataItem.step_uid,
                                typeRequest: 'update',
                                data: dataEdited,
                                functionSuccess: function (xhr, response) {
                                    that.updateItemIndexToAccordion();
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: 'An unexpected error while editing the step, please try again later.'.translate(),
                                messageSuccess: 'Step editing successfully.'.translate(),
                                flashContainer: this.mainWindow
                            });
                            restClientMultipart.executeRestClient();
                        }
                    }
                }
            },
            start: function (e, ui) {
                newIndex = ui.item.index();
            }
        });
    }
};
/**
 * It is an extension to add the "sortable" event "stepsAssignTree".
 * when choosing a node treePanel and you want to add to the accordion or the grid
 * @chainable
 */
stepsTask.prototype.addEventSortableInTreePanelElements = function () {
    var items = this.stepsAssignTree.getItems(),
        connect,
        i,
        sw,
        that = this,
        nodeItems;
    for (i = 0; i < items.length; i += 1) {
        nodeItems = items[i].getItems();
        if (nodeItems.length && nodeItems[0].getData().obj_type) {
            sw = items[i].getItems()[0].getData().obj_type === "SCRIPT";
            connect = sw ? ".pmui-gridpanel-tbody" : ".pmui-accordion-panel-body";
            $(items[i].html).find('ul').find('>li').draggable({
                appendTo: document.body,
                revert: "invalid",
                helper: "clone",
                cursor: "move",
                zIndex: 1000,
                connectToSortable: connect,
                start: function (e) {
                    var i, nodeTag, node, nodeData, accordionItems, item;
                    nodeTag = e.target;
                    node = PMUI.getPMUIObject(nodeTag);
                    nodeData = node.getData();
                    accordionItems = that.stepsAssignAccordion.getItems();
                    $(that.stepsAssignAccordion.body).hide();
                    if (nodeData.obj_type !== "SCRIPT") {
                        for (i = 0; i < accordionItems.length; i += 1) {
                            item = accordionItems[i];
                            item.collapse();
                        }
                    }
                    $(that.stepsAssignAccordion.body).show();
                },
                stop: function () {
                    var i = 0, max;
                    if (that.elementAccordionOpen) {
                        max = that.elementAccordionOpen.getSize();
                        for (i = 0; i < max; i += 1) {
                            that.elementAccordionOpen.get(i).expand();
                        }
                    }
                }
            });
        } else {
            $(nodeItems[0].html).draggable("disable");
        }
    }
};
/**
 * add custom buttons on the head of an element of stepsAssignAccordion
 * are three buttons
 * properties
 * edit
 * remove
 * @param html, is the html of the header accordion item
 * @param step, the data of the step selected
 */
stepsTask.prototype.customAccordionItemButtons = function (html, step, accordionItem) {
    var propertiesStep,
        editStep,
        removeStep,
        $html,
        containerButtons,
        that = this,
        title;
    if (html) {
        $html = jQuery(html.getElementsByClassName("pmui-accordion-item-header"));
        title = step.obj_title + ' (' + step.step_type_obj + ')';
        $html.find(".pmui-accordion-item-title").get(0).title = title;
        containerButtons = $('<div></div>');
        containerButtons.addClass("propertiesTask-accordionItem");
        propertiesStep = $('<a>' + 'Properties'.translate() + '</a>');
        propertiesStep.addClass("mafe-button-edit propertiesTask-accordionButton");
        editStep = $('<a>' + 'Edit'.translate() + '</a>');
        editStep.addClass("mafe-button-edit propertiesTask-accordionButton");
        removeStep = $('<a>' + 'Remove'.translate() + '</a>');
        removeStep.addClass("mafe-button-delete propertiesTask-accordionButton");

        propertiesStep.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            that.propertiesStepShow(step);
            return false;
        });

        editStep.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            that.editStepShow(step, accordionItem);
            return false;
        });

        removeStep.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            that.removeStepShow(step);
            return false;
        });
        containerButtons.append(propertiesStep);
        containerButtons.append(editStep);
        containerButtons.append(removeStep);
        $html.append(containerButtons);
    }
};
/**
 * opens the properties of the selected step with the current settings
 * @param step, is the data of selected step
 * @chainable
 */
stepsTask.prototype.propertiesStepShow = function (step) {
    var form,
        saveButton,
        cancelButton,
        criteriaButton,
        that = this;
    this.resetSecondaryWindow();
    this.secondaryWindow.setWidth(520);
    this.secondaryWindow.setHeight(370);
    this.secondaryWindow.setTitle('Step Properties'.translate());
    form = new PMUI.form.Form({
        id: 'stepsEditCondition',
        width: 500,
        title: 'Condition Trigger'.translate(),
        visibleHeader: false,
        items: [
            {
                id: 'step_mode',
                pmType: 'radio',
                label: 'Mode'.translate(),
                value: '',
                visible: step.step_type_obj === "DYNAFORM" ? true : false,
                name: 'step_mode',
                options: [
                    {
                        id: 'modeEdit',
                        label: 'Edit'.translate(),
                        value: 'EDIT',
                        selected: true
                    }, {
                        id: 'modeView',
                        label: 'View'.translate(),
                        value: 'VIEW'
                    }
                ]
            },
            new CriteriaField({
                id: 'step_condition',
                pmType: 'textarea',
                name: 'step_condition',
                valueType: 'string',
                label: 'Condition'.translate(),
                placeholder: 'Insert a condition'.translate(),
                rows: 150,
                controlsWidth: 250,
                renderType: 'textarea'
            })
        ]
    });
    this.secondaryWindow.addItem(form);

    var restClient = new PMRestClient({
        endpoint: 'activity/' + PMDesigner.act_uid + '/step/' + step.step_uid,
        typeRequest: 'get',
        functionSuccess: function (xhr, response) {
            form.getField('step_mode').setValue(response.step_mode);
            form.getField('step_condition').setValue(response.step_condition);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restClient.executeRestClient();
    saveButton = this.secondaryWindow.footer.getItem("secondaryWindow-save");
    cancelButton = this.secondaryWindow.footer.getItem("secondaryWindow-cancel");
    criteriaButton = this.secondaryWindow.footer.getItem("secondaryWindow-criteria");
    if (saveButton) {
        saveButton.setHandler(function () {
            var restClient;
            if (form.isValid()) {
                restClient = new PMRestClient({
                    endpoint: 'activity/' + PMDesigner.act_uid + '/step/' + step.step_uid,
                    typeRequest: 'update',
                    data: form.getData(),
                    functionSuccess: function () {
                        that.secondaryWindow.close();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageError: 'There are problems update the Step Trigger, please try again.'.translate()
                });
                restClient.executeRestClient();
            }
        });
    }
    if (cancelButton) {
        cancelButton.setHandler(function () {
            that.secondaryWindow.close();
        });
    }
    if (criteriaButton) {
        criteriaButton.handler = null;
        criteriaButton.setVisible(false);
    }
    this.secondaryWindow.open();
};
/**
 * opens the step of the selected step with the current settings
 * @param step, is the data of selected step
 * @chainable
 */
stepsTask.prototype.editStepShow = function (step, accordioItem) {
    var inputDocument,
        that = this;
    switch (step.step_type_obj) {
        case 'DYNAFORM':
            var restProxy = new PMRestClient({
                endpoint: 'dynaform/' + step.step_uid_obj,
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    var old = PMUI.activeCanvas,
                        formDesigner;
                    PMUI.activeCanvas = false;
                    formDesigner = PMDesigner.dynaformDesigner(response);
                    formDesigner.onHide = function () {
                        var assignedDynaform,
                            i,
                            data,
                            title;
                        assignedDynaform = that.getStepsAssignedByCriteria("DYNAFORM");
                        if (jQuery.isArray(assignedDynaform)) {
                            for (i = 0; i < assignedDynaform.length; i += 1) {
                                data = assignedDynaform[i];
                                if (typeof data === "object") {
                                    if (data.step_uid === step.step_uid) {
                                        title = data.step_position + ". " + data.obj_title;
                                        title = title + ' (' + that.stepsType["DYNAFORM"] + ')';
                                        accordioItem.setTitle(title);
                                        accordioItem.dataItem = data;
                                    }
                                }
                            }
                        }
                        PMUI.activeCanvas = old;
                    };
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restProxy.executeRestClient();
            break;
        case 'OUTPUT_DOCUMENT':
            PMDesigner.output();
            PMDesigner.output.showTiny(step.step_uid_obj);
            break;
        case 'INPUT_DOCUMENT':
            inputDocument = new InputDocument({
                onUpdateInputDocumentHandler: function (data, inputDoc) {
                    var position, title;
                    position = accordioItem.dataItem.step_position;
                    title = position + ". " + data.inp_doc_title;
                    title = title + ' (' + that.stepsType["INPUT_DOCUMENT"] + ')';
                    accordioItem.dataItem.obj_title = data.inp_doc_title;
                    accordioItem.setTitle(title);
                    inputDoc.winMainInputDocument.close();
                }
            });
            inputDocument.build();
            inputDocument.openFormInMainWindow();
            inputDocument.inputDocumentFormGetProxy(step.step_uid_obj);
            break;
    }
};
/**
 * the window opens for confirmation of the removal step
 * @param step, the current step to remove
 * @chainable
 */
stepsTask.prototype.removeStepShow = function (step) {
    var title,
        yesButton,
        noButton,
        that = this,
        restClient;
    if (this.stepsType[step.step_type_obj] !== undefined) {
        title = "Step {0} ( {1} )".translate([step.obj_title, this.stepsType[step.step_type_obj]]);
        this.confirmWindow.setTitle(title);
    } else {
        this.confirmWindow.setTitle("Step " + step.step_type_obj.capitalize());
    }
    this.confirmWindow.setMessage("Do you want to remove the step '{0}'?".translate([step.obj_title]));
    yesButton = this.confirmWindow.footer.getItem("confirmWindow-footer-yes");
    noButton = this.confirmWindow.footer.getItem("confirmWindow-footer-no");
    if (yesButton) {
        yesButton.setHandler(function () {
            restClient = new PMRestClient({
                endpoint: 'activity/' + PMDesigner.act_uid + '/step/' + step.step_uid,
                typeRequest: 'remove',
                functionSuccess: function (xhr, response) {
                    that.removingStepTask(step, response);
                    that.confirmWindow.close();
                    that.updateItemIndexToAccordion();
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: 'An unexpected error while deleting the step, please try again later.'.translate(),
                messageSuccess: 'Step removed successfully'.translate(),
                flashContainer: that.mainWindow.getParent()
            });
            restClient.executeRestClient();
        });
    }
    if (noButton) {
        noButton.setHandler(function () {
            that.confirmWindow.close();
        });
    }
    this.confirmWindow.open();
};
/**
 * eliminates the step of step Assign Accordion
 * @param step, the current step to remove
 * @param response, data from the endpoint
 */
stepsTask.prototype.removingStepTask = function (step, response) {
    var stepObject,
        stepAvailable,
        treeNodeObject,
        stepAvailables,
        i,
        itemsTreeNode = [],
        items = [];
    stepObject = this.stepsAssignAccordion.getItem("id" + step.step_uid_obj);
    this.elementAccordionOpen.remove(stepObject);
    this.stepsAssigned.remove(stepObject);
    this.stepsAssignAccordion.removeItem(stepObject);
    if (stepObject) {
        stepAvailable = this.getStepAvailables();
        stepAvailables = this.getAvailablesStepsByCriteria(step.step_type_obj, stepAvailable);
        for (i = 0; i < stepAvailables.length; i += 1) {
            items.push({
                obj_title: stepAvailables[i]['obj_title'],
                obj_type: stepAvailables[i]['obj_type'],
                obj_uid: stepAvailables[i]['obj_uid']
            });
        }
        treeNodeObject = this.stepsAssignTree.getItem(step.step_type_obj);
        itemsTreeNode = treeNodeObject.getItems();
        for (i = 0; i < itemsTreeNode.length; i += 1) {
            treeNodeObject.removeItem(itemsTreeNode[i]);
        }
        treeNodeObject.clearItems();
        treeNodeObject.setDataItems(items);
        this.updateIndexPosition(treeNodeObject);
        this.addEventSortableInTreePanelElements();
        this.addEventSortableInAccordionElements();
    }
};
/**
 * get the steps is not assigned by a criterion
 * @param criteria, It is the filter criteria search
 * @param stepAvailable, all steps Unassigned
 * @returns {Array}, filtered items
 */
stepsTask.prototype.getAvailablesStepsByCriteria = function (criteria, stepAvailable) {
    var items = [],
        i;
    if (jQuery.isArray(stepAvailable)) {
        for (i = 0; i < stepAvailable.length; i += 1) {
            if (stepAvailable[i].obj_type === criteria) {
                items.push(stepAvailable[i]);
            }
        }
    }
    return items;
};
/**
 * This method is executed when an element stepsAssignTree, is assigned in a grid
 * @param receiveData, data of the droppable item
 * @param grid, the affected grid
 * @param index, the index position row
 * @returns {stepsTask}
 */
stepsTask.prototype.receiveTreeNodeItem = function (receiveData, grid, index) {
    var restClient, that = this, message;
    if (that.isTriggerAssigned(grid, receiveData.obj_uid)) {
        message = new PMUI.ui.FlashMessage({
            message: 'Trigger is assigned.'.translate(),
            duration: 3000,
            severity: 'error',
            appendTo: that.mainWindow
        });
        message.show();
        return;
    }
    restClient = new PMRestClient({
        endpoint: grid.step_uid === undefined ?
        'activity/' + PMDesigner.act_uid + '/step/trigger' :
        'activity/' + PMDesigner.act_uid + '/step/' + grid.step_uid + '/trigger',
        typeRequest: 'post',
        data: {
            tri_uid: receiveData.obj_uid,
            st_type: grid.st_type,
            st_condition: '',
            st_position: index + 1
        },
        functionSuccess: function (xhr, response) {
            grid.addDataItem({
                st_condition: '',
                st_position: index + 1,
                st_type: grid.st_type,
                tri_description: '',
                tri_title: receiveData.obj_title,
                tri_uid: receiveData.obj_uid,
                obj_title: receiveData.obj_title,
                obj_uid: receiveData.obj_uid
            }, index);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        }
    });
    restClient.executeRestClient();
    return this;
};
/**
 * This method is executed when a row is drop in another grid
 * @param receiveData, data of the droppable item
 * @param grid, the affected grid
 * @param index, the index position row
 * @param draggableItem
 * @returns {*}
 */
stepsTask.prototype.receiveRowItem = function (receiveData, grid, index, draggableItem) {
    var receiveParent = draggableItem.getParent(),
        message,
        restClient,
        that = this;
    if (this.isTriggerAssigned(grid, receiveData.obj_uid)) {
        message = new PMUI.ui.FlashMessage({
            message: 'Trigger is assigned.'.translate(),
            duration: 3000,
            severity: 'error',
            appendTo: that.mainWindow
        });
        index = receiveParent.items.indexOf(draggableItem);
        receiveParent.items.remove(draggableItem);
        receiveParent.addItem(draggableItem, index);
        message.show();
        return false;
    }
    restClient = new PMRestClient({
        typeRequest: 'post',
        multipart: true,
        data: {
            calls: [
                {
                    url: grid.step_uid === undefined ?
                    'activity/' + PMDesigner.act_uid + '/step/trigger' :
                    'activity/' + PMDesigner.act_uid + '/step/' + grid.step_uid + '/trigger',
                    method: 'POST',
                    data: {
                        tri_uid: receiveData.obj_uid,
                        st_type: grid.st_type,
                        st_condition: receiveData.st_condition,
                        st_position: index + 1
                    }
                }, {
                    url: receiveParent.step_uid === undefined ?
                    'activity/' + PMDesigner.act_uid + '/step/trigger/' + receiveData.obj_uid + '/' + that.getStepType(receiveParent.st_type) :
                    'activity/' + PMDesigner.act_uid + '/step/' + receiveParent.step_uid + '/trigger/' + receiveData.obj_uid + '/' + receiveParent.st_type.toLowerCase(),
                    method: 'DELETE'
                }
            ]
        },
        functionSuccess: function (xhr, response) {
            var data;
            data = receiveData;
            if (data.hasOwnProperty("st_type")) {
                data.st_type = grid.st_type;
                grid.addDataItem(receiveData, index);
            }
            receiveParent.removeItem(draggableItem);
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
        flashContainer: that.mainWindow,
        messageError: [
            'An unexpected error while assigning the trigger, please try again later.'.translate()
        ],
        messageSuccess: [
            'Trigger assigned successfully.'.translate()
        ]
    });
    restClient.executeRestClient();
    return this;
};
/**
 * This method is executed when a row is sorted in the grid
 * @param receiveData, data of the droppable item
 * @param grid, the affected grid
 * @param index, the new index position row
 * @returns {stepsTask}
 */
stepsTask.prototype.sortableRowHandler = function (receiveData, grid, index) {
    var that = this,
        restClient;
    restClient = new PMRestClient({
        endpoint: grid.step_uid === undefined ?
        'activity/' + PMDesigner.act_uid + "/step/trigger/" + receiveData.tri_uid :
        'activity/' + PMDesigner.act_uid + "/step/" + grid.step_uid + "/trigger/" + receiveData.tri_uid,
        typeRequest: 'update',
        data: {
            st_type: receiveData.st_type,
            st_condition: receiveData.st_condition,
            st_position: index + 1
        },
        functionFailure: function (xhr, response) {
            PMDesigner.msgWinError(response.error.message);
        },
        flashContainer: that.mainWindow,
        messageError: 'An unexpected error while assigning the trigger, please try again later.'.translate(),
        messageSuccess: 'Trigger assigned successfully.'.translate()
    });
    restClient.executeRestClient();
    return this;
};
/**
 * This method eliminates the list of triggers trigger an assigned step
 * @param row, the row affected or selected
 * @param grid, It is affected or grid to remove selected row
 */
stepsTask.prototype.removeTrigger = function (row, grid) {
    var message = 'Do you want to remove the trigger "',
        messageData = row.getData().tri_title ? row.getData().tri_title : "",
        yesButton,
        noButton,
        that = this,
        restClient;
    message = message + messageData + '"?';
    this.confirmWindow.setMessage(message.translate());
    yesButton = this.confirmWindow.footer.getItem("confirmWindow-footer-yes");
    if (yesButton) {
        yesButton.setHandler(function () {
            restClient = new PMRestClient({
                endpoint: grid.step_uid === undefined ?
                'activity/' + PMDesigner.act_uid + '/step/trigger/' + row.getData().tri_uid + '/' + that.getStepType(row.getData().st_type) :
                'activity/' + PMDesigner.act_uid + '/step/' + grid.step_uid + '/trigger/' + row.getData().tri_uid + '/' + that.getStepType(row.getData().st_type),
                typeRequest: 'remove',
                functionSuccess: function (xhr, response) {
                    grid.removeItem(row);
                    that.confirmWindow.close();
                    that.updateIndexToGrid(grid);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                flashContainer: that.mainWindow,
                messageError: 'An unexpected error while deleting the trigger, please try again later.'.translate(),
                messageSuccess: 'Trigger removed successfully'.translate()
            });
            restClient.executeRestClient();
        });
    }
    noButton = this.confirmWindow.footer.getItem("confirmWindow-footer-no");
    if (noButton) {
        noButton.setHandler(function () {
            that.confirmWindow.close();
        });
    }
    this.confirmWindow.open();
};
/**
 * updates indexes of elements selected grid
 * @param grid, It is affected or grid to remove selected row
 * @returns {stepsTask}
 */
stepsTask.prototype.updateIndexToGrid = function (grid) {
    var cell, rows, i, row;
    if (grid) {
        rows = grid.getItems();
        if (jQuery.isArray(rows)) {
            for (i = 0; i < rows.length; i += 1) {
                row = rows[i];
                cell = row.cells.find("columnData");
                if (cell) {
                    cell.setContent(i + 1);
                }
            }
        }
    }
    return this;
};
/**
 * get the steps assigned by a search criterion
 * @param criteria, search filter, after running the endpoint getAccordionData method
 * @returns {Array}, response with criteria
 */
stepsTask.prototype.getStepsAssignedByCriteria = function (criteria) {
    var allAssigned,
        i,
        elements,
        j,
        resp,
        response = [];
    allAssigned = this.getAccordionData();
    if (jQuery.isArray(allAssigned)) {
        for (i = 0; i < allAssigned.length; i += 1) {
            resp = allAssigned[i];
            if (typeof resp === "object") {
                elements = resp.response ? resp.response : [];
                for (j = 0; j < elements.length; j += 1) {
                    data = elements[j];
                    if (typeof data === "object") {
                        if (data.step_type_obj && data.step_type_obj === criteria) {
                            response.push(data);
                        }
                    }
                }
            }
        }
    }
    return response;
};
/**
 * updates indexes of elements assigned
 * @returns {stepsTask}
 */
stepsTask.prototype.updateItemIndexToAccordion = function () {
    var title,
        i,
        item,
        dataItem,
        items = this.stepsAssignAccordion.items,
        position,
        max;
    max = items.getSize();
    for (i = 0; i < max; i += 1) {
        item = items.get(i);
        position = items.indexOf(item);
        dataItem = item.dataItem;
        title = (position + 1) + ".  " + dataItem.obj_title;
        if (this.stepsType[dataItem.step_type_obj]){
            title = title + ' (' + this.stepsType[dataItem.step_type_obj] + ')';
        }
        item.dataItem.step_position = i + 1;
        item.setTitle(title);
    }
    return this;
};
/**
 * add tooltip in treeNode elements
 * @returns {stepsTask}
 */
stepsTask.prototype.updateIndexPosition = function (treeNode) {
    var items, i, item, $item, text, data;
    if (treeNode && treeNode.html) {
        items = treeNode.getItems();
        if (jQuery.isArray(items)) {
            for (i = 0; i < items.length; i += 1) {
                item = items[i];
                if (item.html) {
                    $item = $(item.html);
                    data = item.getData();
                    text = $item.find("a").get(0);
                    text.title = data.obj_title;
                }
            }
        }
    }
    return this;
};
/**
 * return the not items config.
 * @returns {{obj_title: *, obj_uid: string, id: string}}
 */
stepsTask.prototype.notItemConfig = function () {
    var config = {
        obj_title: 'N/A'.translate(),
        obj_uid: '',
        id: "notItem"
    };
    return config;
};