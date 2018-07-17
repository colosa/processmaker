(function () {
    /**
     * @class PMUI.menu.ContextMenu
     * Handles the context menu of designer
     * contains all the menus for elements
     *
     * @extend PMUI.util.Factory
     * @constructor
     * This method creates a new instance of this object
     * @param {Object} [settings] Constructor settings
     */
    "use strict";
    var ContextMenu = function (settings) {
        this.factory = null;
        ContextMenu.prototype.init.call(this, settings);
    };

    ContextMenu.prototype.type = 'ContextMenu';

    ContextMenu.prototype.family = 'ContextMenu';

    /**
     * Initializes the object.
     * @param  {Object} settings A JSON object with the config options.
     * @private
     */
    ContextMenu.prototype.init = function (settings) {
        jQuery.extend(true, defaults, settings);
        this.factory = new PMUI.util.Factory(defaults.factory);
    };

    /**
     * Register a new context menu object into a product.
     *
     * Usage example:
     *      @example
     *       //Remember, this is an abstract class so it shouldn't be instantiate,
     *       //anyway we are instantiating it just for this example
     *       var additionalMenu = {
     *          id: "additionalMenu",
     *          text: "New Menu",
     *          onClick: function () {
     *              PMDesigner.msgWinWarning('This is a new Menu');
     *              }
     *          };
     *       // Assuming that you're using PMDesigner.contextMenuFactory as the default contextMenuFactory
     *       PMDesigner.contextMenuFactory.registerMenu("CANVAS", additionalMenu);
     *
     * @param  {String} productName Name of the shape or product in factory.
     * @param  {Object} menu Object with menu values and actions.
     * @private
     */
    ContextMenu.prototype.registerMenu = function (productName, menu) {
        if (typeof this.factory.products[productName] === 'undefined') {
            console.log('Context Menu Warning: Cannot add Menu into: ' + productName + '. Please, review name.');
        } else {
            if (this.factory.products[productName].items instanceof Array) {
                this.factory.products[productName].items.push(menu);
            }
        }
    };

    /**
     * Removes context menu object from product.
     *
     * Usage example:
     *      @example
     *       // Assuming that you're using PMDesigner.contextMenuFactory as the default contextMenuFactory
     *       // this example removes Canvas gridLines option
     *       PMDesigner.contextMenuFactory.removeMenu("CANVAS", "id", "menuGridLines");
     *
     * @param  {String} product Name of the shape or product in factory.
     * @param  {String} id Key to look through product.
     * @param  {String} value Name of the elementValue.
     * @private
     */
    ContextMenu.prototype.removeMenu = function (product, id, value) {
        if (typeof this.factory.products[product] !== 'undefined') {
            this.factory.products[product].items = _.reject(this.factory.products[product].items, function (menuValue) {
                return menuValue[id] === value;
            });
        } else {
            console.log('Context Menu Warning: Cannot find ' + product + ' Menu. Please, review name.');
        }
    };

    /**
     * Returns specific product.
     * @param  {String} type Name of product.
     * @private
     */
    ContextMenu.prototype.getProduct = function (type) {
        return this.factory.products[type];
    };

    var menuMessages = {
            'START': {
                'TIMER': 'Please configure cron to create cases.'.translate(),
                'CONDITIONAL': 'Please configure cron to create cases in base to a condition.'.translate(),
                'SIGNALCATCH': 'Please configure cron to create cases in base to a signal.'.translate()
            },
            'INTERMEDIATE': {
                'CATCH': {
                    'TIMER': 'Please configure cron to wait for time event.'.translate(),
                    'CONDITIONAL': 'Please configure cron to wait for time condition.'.translate(),
                    'SIGNALCATCH': 'Please configure script to wait for a signal.'.translate()
                },
                'THROW': {
                    'SIGNALTHROW': 'Please configure a script to send a signal.'.translate()
                }
            },
            'END': {
                'ERRORTHROW': 'Please configure script to end with error status.'.translate(),
                'SIGNALTHROW': 'Please configure script to send a signal.'.translate(),
                'TERMINATETHROW': 'Please configure script to terminate case.'.translate()
            }
        },
        menu = {},
        rootMenu,
        elementActivite,
        typeMenu = {
            text: "Gateway Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "gatewaytype",
            items: [
                {
                    id: "gatewayexclusive",
                    text: "Exclusive (XOR) Gateway".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeTypeTo('EXCLUSIVE');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "gatewayparallel",
                    text: "Parallel (AND) Gateway".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeTypeTo('PARALLEL');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "gatewayinclusive",
                    text: "Inclusive (OR) Gateway".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeTypeTo('INCLUSIVE');
                        PMDesigner.project.updateElement([]);
                    }
                }
            ]
        },
        endMarker = {
            text: "End Event Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "result",
            items: [
                {
                    id: "endempty",
                    text: "Empty".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('EMPTY', 'Empty');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "endemail",
                    text: "Email Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('EMAIL', 'Email Message');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "endmessagethrow",
                    text: "Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('MESSAGETHROW', 'Message');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "enderrorthrow",
                    text: "Error".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('ERRORTHROW', 'Error');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "endsignalthrow",
                    text: "Signal".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('SIGNALTHROW', 'Signal');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "endterminatethrow",
                    text: "Terminate".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('TERMINATETHROW', 'Terminate');
                        PMDesigner.project.updateElement([]);
                    }
                }
            ]
        },
        intermediateCatchMarker = {
            text: "Intermediate Event Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "trigger",
            items: [
                {
                    id: "intermediatemessagecatch",
                    text: "Receive Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('MESSAGECATCH', 'Receive Message');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "intermediatetimer",
                    text: "Timer".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('TIMER', 'Timer');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "intermediateconditional",
                    text: "Conditional".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('CONDITIONAL', 'Conditional');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "intermediatesignalcatch",
                    text: "Signal".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('SIGNALCATCH', 'Signal');
                        PMDesigner.project.updateElement([]);
                    }
                }
            ]
        },
        boundaryCatchMarker = {
            text: "Boundary Event Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "eventType",
            items: [
                {
                    id: "messageCatch",
                    text: "Receive Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('MESSAGECATCH', 'Receive Message');
                    }
                },
                {
                    id: "boundaryTimer",
                    text: "Timer".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('TIMER', 'Timer');
                    }
                },
                {
                    id: "BoudaryConditional",
                    text: "Conditional".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('CONDITIONAL', 'Conditional');
                    }
                },
                {
                    id: "BoudarySignal",
                    text: "Signal".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('SIGNALCATCH', 'Signal');
                    }
                },
                {
                    id: "BoudaryError",
                    text: "Error".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('ERRORCATCH', 'Error');
                    }
                }
            ]
        },
        intermediateThrowMarker = {
            text: "Intermediate Event Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "result",
            items: [
                {
                    id: "intermediateemail",
                    text: "Email Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('EMAIL', 'Email Message');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "intermediatemessagethrow",
                    text: "Send Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('MESSAGETHROW', 'Send Message');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "intermediatesignalthrow",
                    text: "Signal".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('SIGNALTHROW', 'Signal');
                        PMDesigner.project.updateElement([]);
                    }
                }
            ]
        },
        startCatchMarker = {
            text: "Start Event Type".translate(),
            icon: "mafe-menu-properties-action",
            id: "trigger",
            items: [
                {
                    id: "startempty",
                    text: "Empty".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('EMPTY', 'Empty');
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "startmessagecatch",
                    text: "Receive Message".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('MESSAGECATCH', 'Receive Message');
                        rootMenu = menuOption.getRootMenu();
                        rootMenu.getItems()[3].disable();
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "starttimer",
                    text: "Timer".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('TIMER', 'Timer');
                        rootMenu = menuOption.getRootMenu();
                        rootMenu.getItems()[3].disable();
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "startconditional",
                    text: "Conditional".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('CONDITIONAL', 'Conditional');
                        rootMenu = menuOption.getRootMenu();
                        rootMenu.getItems()[3].disable();
                        PMDesigner.project.updateElement([]);
                    }
                },
                {
                    id: "startsignalcatch",
                    text: "Signal".translate(),
                    onClick: function (menuOption) {
                        var targetElement = menuOption
                            .getMenuTargetElement();
                        targetElement.changeMarkerTo('SIGNALCATCH', 'Signal');
                        rootMenu = menuOption.getRootMenu();
                        rootMenu.getItems()[3].disable();
                        PMDesigner.project.updateElement([]);
                    }
                }
            ]
        },
        canvas = {
            id: "menuCanvas",
            width: 150,
            items: [
                {
                    id: "menuEditProcess",
                    text: "Edit Process".translate(),
                    onClick: function (menuOption) {
                        PMDesigner.propertiesProcess();
                    }
                },
                {
                    id: "menuGridLines",
                    text: "Enable Grid Lines".translate(),
                    onClick: function () {
                        var canvas = PMUI.getActiveCanvas();
                        if (canvas.toogleGridLine()) {
                            this.setText("Disable Grid Lines".translate());
                        } else {
                            this.setText("Enable Grid Lines".translate());
                        }
                    }
                }
            ],
            onShow: function (menu) {
                var canvas = PMUI.getActiveCanvas();
                if (canvas.currentConnection) {
                    canvas.currentConnection.hidePortsAndHandlers();
                }
                if (canvas.isGridLine) {
                    menu.items.find('id', 'menuGridLines').setText("Disable Grid Lines".translate());
                }

            }
        },
        task = {
            id: "menuTask",
            items: [
                {
                    id: "taskType",
                    text: "Task Type".translate(),
                    items: [
                        {
                            id: "empty",
                            text: "Empty Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "sendtask",
                            text: "Send Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "receivetask",
                            text: "Receive Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "usertask",
                            text: "User Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "servicetask",
                            text: "Service Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "scripttask",
                            text: "Script Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "manualtask",
                            text: "Manual Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        },
                        {
                            id: "businessrule",
                            text: "Business Rule Task".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerType(menuOption);
                            }
                        }
                    ]
                },
                {
                    id: "loopType",
                    text: "Marker Type".translate(),
                    items: [
                        {
                            id: "empty",
                            text: "None".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerLoopType(menuOption);
                            }
                        },
                        {
                            id: "loop",
                            text: "Loop".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerLoopType(menuOption);
                            }
                        },
                        {
                            id: "parallel",
                            text: "Parallel".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerLoopType(menuOption);
                            }
                        },
                        {
                            id: "sequential",
                            text: "Sequential".translate(),
                            onClick: function (menuOption) {
                                handlerMarkerLoopType(menuOption);
                            }
                        }
                    ]
                },
                {
                    id: "menuTaskSteps",
                    text: "Steps".translate(),
                    icon: "mafe-menu-task-steps",
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    PMDesigner.act_name = menuOption.getMenuTargetElement().act_name;
                                    PMDesigner.act_uid = menuOption.getMenuTargetElement().act_uid;
                                    PMDesigner.stepsTask = new stepsTask();
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            PMDesigner.act_name = menuOption.getMenuTargetElement().act_name;
                            PMDesigner.act_uid = menuOption.getMenuTargetElement().act_uid;
                            PMDesigner.stepsTask = new stepsTask();
                        }
                    }
                },
                {
                    id: "menuTaskAssignedRules",
                    text: "Assignment Rules".translate(),
                    icon: "mafe-menu-users-action",
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    PMDesigner.assigmentRules(menuOption.getMenuTargetElement());
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            PMDesigner.assigmentRules(menuOption.getMenuTargetElement());
                        }
                    }
                },
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.parent.hide();

                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        menuOption.parent.hide();

                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    id: "menuTaskProperties",
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action",
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    PMDesigner.activityProperties(menuOption.getMenuTargetElement());
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            PMDesigner.activityProperties(menuOption.getMenuTargetElement());
                        }
                    }
                }
            ],
            onShow: function (menu) {
                var targetElement = menu.getTargetElement();
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(targetElement);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection
                        .hidePortsAndHandlers();
                }

                if (targetElement.act_task_type == "SCRIPTTASK") {
                    menu.getItems()[1].setVisible(false);
                    menu.getItems()[2].setVisible(false);
                    menu.getItems()[3].setVisible(false);
                } else {
                    menu.getItems()[1].setVisible(true);
                    menu.getItems()[2].setVisible(true);
                    menu.getItems()[3].setVisible(true);
                }

                if (targetElement.act_task_type == "MANUALTASK" || targetElement.act_task_type == "USERTASK" || targetElement.act_task_type == "EMPTY") {
                    menu.getItems()[1].getItems()[2].setVisible(true);
                } else {
                    menu.getItems()[1].getItems()[2].setVisible(false);
                }
            }
        },
        subProcess = {
            id: "menuSubProcess",
            items: [
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.parent.hide();

                        menuOption.getMenuTargetElement().label.canvas
                            .hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete Routing Rule".translate(),
                    icon: "mafe-menu-delete-rules-action",
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    PMDesigner.RoutingRuleDeleteAllFlow(menuOption.getMenuTargetElement());
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            PMDesigner.RoutingRuleDeleteAllFlow(menuOption.getMenuTargetElement());
                        }
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        menuOption.parent.hide();

                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    id: "menuSubProperties",
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action",
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    PMDesigner.propertiesSubProcess(menuOption
                                        .getMenuTargetElement(menuOption.getMenuTargetElement()));

                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            PMDesigner.propertiesSubProcess(menuOption
                                .getMenuTargetElement(menuOption.getMenuTargetElement()));

                        }
                    }
                }
            ],
            onShow: function (menu) {
                var targetElement = menu.getTargetElement();
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(targetElement);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection
                        .hidePortsAndHandlers();
                }
            }
        },
        start = {
            id: 'menuStart',
            items: [
                startCatchMarker,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    id: "menuStartWebEntry",
                    text: "Web Entry".translate(),
                    icon: "mafe-menu-start-message-view",
                    //visible: false,
                    onClick: function (menuOption) {
                        var splitedID = menuOption.getMenuTargetElement().getID().split("-"), webEntry;
                        menuOption.parent.hide();
                        if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                            PMDesigner.restApi.execute({
                                data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                method: "update",
                                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                success: function (data, textStatus, xhr) {
                                    var webEntry;
                                    PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                    PMDesigner.project.isSave = false;
                                    //open property form
                                    webEntry = new WebEntry(menuOption.getMenuTargetElement());
                                    webEntry.render();
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                    PMDesigner.project.isSave = false;
                                }
                            });
                        } else {
                            webEntry = new WebEntry(menuOption.getMenuTargetElement());
                            webEntry.render();
                        }
                    }
                },
                {
                    id: "menuStartProperties",
                    text: "Properties".translate(),
                    icon: "mafe-menu-start-message-view",
                    onClick: function (menuOption) {
                        var a = menuOption.getMenuTargetElement(), message_window,
                            splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        switch (a.getEventMarker()) {
                            case "TIMER":
                                menuOption.parent.hide();
                                if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                    PMDesigner.restApi.execute({
                                        data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                        method: "update",
                                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                        success: function (data, textStatus, xhr) {
                                            PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                            PMDesigner.project.isSave = false;
                                            //open property form
                                            PMDesigner.timerEventProperties(menuOption.getMenuTargetElement());
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                            PMDesigner.project.isSave = false;
                                        }
                                    });
                                } else {
                                    PMDesigner.timerEventProperties(menuOption.getMenuTargetElement());
                                }
                                break;
                            default :
                                var messageEventDefinition,
                                    eventCurrent = menuOption.getMenuTargetElement();
                                if (menuMessages[eventCurrent.evn_type][eventCurrent.evn_marker] !== undefined) {
                                    message_window = new PMUI.ui.MessageWindow({
                                        id: "cancelMessageTriggers",
                                        width: 490,
                                        title: 'Information'.translate(),
                                        windowMessageType: 'info',
                                        bodyHeight: 'auto',
                                        message: menuMessages[eventCurrent.evn_type][eventCurrent.evn_marker],
                                        footerItems: [
                                            {
                                                text: 'Ok'.translate(),
                                                handler: function () {
                                                    message_window.close();
                                                },
                                                buttonType: "success"
                                            }
                                        ]
                                    });
                                    message_window.open();
                                    message_window.showFooter();
                                } else {
                                    menuOption.parent.hide();
                                    if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                        PMDesigner.restApi.execute({
                                            data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                            method: "update",
                                            url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                            success: function (data, textStatus, xhr) {
                                                PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                                PMDesigner.project.isSave = false;
                                                //open property form
                                                messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                            },
                                            error: function (xhr, textStatus, errorThrown) {
                                                PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                                PMDesigner.project.isSave = false;
                                            }
                                        });
                                    } else {
                                        messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                    }
                                }
                        }
                    }
                }
            ],
            onShow: function (menu) {
                var targetElement = menu.getTargetElement(),
                    shape,
                    propertyMap = ['MESSAGECATCH', 'TIMER'];
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(targetElement);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (targetElement.evn_marker == 'TIMER') {
                }
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                if (targetElement.evn_marker === 'MESSAGECATCH') {
                    var dt = menu.getItems();
                    for (var i = 0; i < dt.length; i += 1) {
                        if (dt[i].id === 'idReceiveMessage') {
                            dt[i].setVisible(true);
                        }
                    }
                }
                //disabled weebentry for others pmEvent no support
                if (targetElement.getPorts().asArray().length) {
                    shape = targetElement.getPorts().getFirst().getConnection().getDestPort().parent;
                    if (shape && (shape instanceof PMActivity) && targetElement.evn_marker === "EMPTY") {
                        menu.items.find('id', 'menuStartWebEntry').enable();
                    } else if (shape && (shape instanceof PMEvent) && shape.evn_marker === "EMAIL"
                        && targetElement.evn_marker === "EMPTY") {
                        menu.items.find('id', 'menuStartWebEntry').enable();
                    } else {
                        menu.items.find('id', 'menuStartWebEntry').disable();
                    }
                } else {
                    menu.items.find('id', 'menuStartWebEntry').disable();
                }

                //Enable && Disabled - Properties
                if (targetElement.evn_type === "START" && propertyMap.indexOf(targetElement.evn_marker) >= 0) {
                    menu.items.find('id', 'menuStartProperties').enable();
                } else {
                    menu.items.find('id', 'menuStartProperties').disable();
                }
            },
            onHide: function (menu) {
                var dt = menu.getItems(), i;
                for (i = 0; i < dt.length; i += 1) {
                    if (dt[i].id === 'idReceiveMessage') {
                        dt[i].setVisible(false);
                    }
                }
            }
        },
        end = {
            id: 'menuEnd',
            items: [
                endMarker,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    id: "menuEndProperties",
                    text: "Properties".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        var a = menuOption.getMenuTargetElement(), message_window,
                            splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        switch (a.getEventMarker()) {
                            case "EMAIL":
                                menuOption.parent.hide();
                                if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                    PMDesigner.restApi.execute({
                                        data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                        method: "update",
                                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                        success: function (data, textStatus, xhr) {
                                            PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                            PMDesigner.project.isSave = false;
                                            //open property form
                                            PMDesigner.emailEventProperties(menuOption.getMenuTargetElement());
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                            PMDesigner.project.isSave = false;
                                        }
                                    });
                                } else {
                                    PMDesigner.emailEventProperties(menuOption.getMenuTargetElement());
                                }
                                break;
                            default :
                                var messageEventDefinition,
                                    eventCurrent = menuOption.getMenuTargetElement();
                                if (menuMessages[eventCurrent.evn_type][eventCurrent.evn_marker] !== undefined) {
                                    message_window = new PMUI.ui.MessageWindow({
                                        id: "cancelMessageTriggers",
                                        width: 490,
                                        title: 'Information'.translate(),
                                        windowMessageType: 'info',
                                        bodyHeight: 'auto',
                                        message: menuMessages[eventCurrent.evn_type][eventCurrent.evn_marker],
                                        footerItems: [
                                            {
                                                text: 'Ok'.translate(),
                                                handler: function () {
                                                    message_window.close();
                                                },
                                                buttonType: "success"
                                            }
                                        ]
                                    });
                                    message_window.open();
                                    message_window.showFooter();
                                } else {
                                    if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                        PMDesigner.restApi.execute({
                                            data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                            method: "update",
                                            url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                            success: function (data, textStatus, xhr) {
                                                PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                                PMDesigner.project.isSave = false;
                                                //open property form
                                                messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                            },
                                            error: function (xhr, textStatus, errorThrown) {
                                                PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                                PMDesigner.project.isSave = false;
                                            }
                                        });
                                    } else {
                                        messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                    }
                                }
                        }
                    }
                }
            ],
            onShow: function (menu) {
                var propertyMap = ['MESSAGETHROW', 'EMAIL'],
                    targetElement = menu.getTargetElement();
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(targetElement);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }


                //Enable && Disabled - Properties
                if (targetElement.evn_type === "END" && propertyMap.indexOf(targetElement.evn_marker) >= 0) {
                    menu.items.find('id', 'menuEndProperties').enable();
                } else {
                    menu.items.find('id', 'menuEndProperties').disable();
                }
            }
        },
        selection = {
            id: 'menuSelection',
            items: [
                {
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action"
                },
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action"
                }
            ]
        },
        evaluation = {
            id: 'menuEvaluation',
            items: [

                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        alert("Working on this feature...");
                    }
                },
                {
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action",
                    onClick: function (menuOption) {
                        alert("Working on this feature...");
                    }
                }
            ]
        },
        parallel = {
            id: 'menuParallel',
            items: [
                typeMenu,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            listeners: {},
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
            }
        },
        exclusive = {
            id: 'menuExclusive',
            items: [
                typeMenu,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: "Delete".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action",
                    onClick: function (menuOption) {
                        PMDesigner.RoutingRule(menuOption.getMenuTargetElement());
                    }
                }
            ],
            onShow: function (menu) {
                var element = menu.getTargetElement(), propertyOption;
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(element);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                if (menu.items) {
                    if (element && element.gat_direction) {
                        propertyOption = menu.items.find("text", "Properties");
                        if (propertyOption) {
                            if (element.gat_direction === 'CONVERGING') {
                                propertyOption.disable();
                            } else {
                                propertyOption.enable();

                            }
                        }
                    }
                }

            }
        },
        inclusive = {
            id: 'menuInclusive',
            items: [
                typeMenu,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    text: 'Properties'.translate(),
                    icon: 'mafe-menu-properties-action',
                    onClick: function (menuOption) {
                        PMDesigner.RoutingRule(menuOption.getMenuTargetElement());
                    }
                }
            ],
            onShow: function (menu) {
                var element = menu.getTargetElement(), propertyOption;
                PMUI.getActiveCanvas().addToSelection(element);
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();

                if (menu.items) {
                    if (element && element.gat_direction) {
                        propertyOption = menu.items.find("text", "Properties");
                        if (propertyOption) {
                            if (element.gat_direction === 'CONVERGING') {
                                propertyOption.disable();
                            } else {
                                propertyOption.enable();

                            }
                        }
                    }
                }
            }
        },
        complex = {
            id: 'menuComplex',
            items: [
                typeMenu,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    text: "Properties".translate(),
                    icon: "mafe-menu-properties-action",
                    onClick: function (menuOption) {
                        PMDesigner.complexRoutingRule(menuOption.getMenuTargetElement());
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        },
        group = {
            id: 'menuLine',
            items: [
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        },
        dataObject = {
            id: 'menuDataObject',
            items: [
                {
                    id: "dataType",
                    text: "Data Type".translate(),
                    items: [
                        {
                            id: "dataobject",
                            text: "Data Empty".translate(),
                            onClick: function (menuOption) {
                                menuOption.getMenuTargetElement().switchDataType('DATAOBJECT');
                            }
                        },
                        {
                            id: "datainput",
                            text: "Data Input".translate(),
                            onClick: function (menuOption) {
                                menuOption.getMenuTargetElement().switchDataType('DATAINPUT');
                            }
                        },
                        {
                            id: "dataoutput",
                            text: "Data Output".translate(),
                            onClick: function (menuOption) {
                                menuOption.getMenuTargetElement().switchDataType('DATAOUTPUT');
                            }
                        }
                    ]
                },
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        },
        dataStore = {
            id: 'menuDataObject',
            items: [
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        },
        textAnnotation = {
            id: 'menuDataObject',
            items: [
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        },
        intermediate = {
            id: 'intermediate',
            items: [
                intermediateCatchMarker,
                intermediateThrowMarker,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                },
                {
                    id: "intermediateProperties",
                    text: "Properties".translate(),
                    icon: "mafe-menu-delete-action",
                    onClick: function (menuOption) {
                        var a = menuOption.getMenuTargetElement(), message_window,
                            splitedID = menuOption.getMenuTargetElement().getID().split("-");
                        switch (a.getEventMarker()) {
                            case "EMAIL":
                                menuOption.parent.hide();
                                if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                    PMDesigner.restApi.execute({
                                        data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                        method: "update",
                                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                        success: function (data, textStatus, xhr) {
                                            PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                            PMDesigner.project.isSave = false;
                                            //open property form
                                            PMDesigner.emailEventProperties(menuOption.getMenuTargetElement());
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                            PMDesigner.project.isSave = false;
                                        }
                                    });
                                } else {
                                    PMDesigner.emailEventProperties(menuOption.getMenuTargetElement());
                                }
                                break;
                            case "TIMER":
                                menuOption.parent.hide();
                                if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                    PMDesigner.restApi.execute({
                                        data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                        method: "update",
                                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                        success: function (data, textStatus, xhr) {
                                            PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                            PMDesigner.project.isSave = false;
                                            //open property form
                                            PMDesigner.timerEventProperties(menuOption.getMenuTargetElement());
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                            PMDesigner.project.isSave = false;
                                        }
                                    });
                                } else {
                                    PMDesigner.timerEventProperties(menuOption.getMenuTargetElement());
                                }
                                break;
                            default :
                                var messageEventDefinition,
                                    eventCurrent = menuOption.getMenuTargetElement();
                                if (menuMessages[eventCurrent.evn_type][eventCurrent.evn_behavior][eventCurrent.evn_marker] !== undefined) {
                                    message_window = new PMUI.ui.MessageWindow({
                                        id: "cancelMessageTriggers",
                                        width: 490,
                                        title: 'Information'.translate(),
                                        windowMessageType: 'info',
                                        bodyHeight: 'auto',
                                        message: menuMessages[eventCurrent.evn_type][eventCurrent.evn_behavior][eventCurrent.evn_marker],
                                        footerItems: [
                                            {
                                                text: 'Ok'.translate(),
                                                handler: function () {
                                                    message_window.close();
                                                },
                                                buttonType: "success"
                                            }
                                        ]
                                    });
                                    message_window.open();
                                    message_window.showFooter();
                                } else {
                                    menuOption.parent.hide();
                                    if ((splitedID && splitedID[0] === 'pmui') || PMDesigner.project.isDirty()) {
                                        PMDesigner.restApi.execute({
                                            data: JSON.stringify(PMDesigner.project.getDirtyObject()),
                                            method: "update",
                                            url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id,
                                            success: function (data, textStatus, xhr) {
                                                PMDesigner.project.listeners.success(PMDesigner.project, textStatus, data);
                                                PMDesigner.project.isSave = false;
                                                //open property form
                                                messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                            },
                                            error: function (xhr, textStatus, errorThrown) {
                                                PMDesigner.project.listeners.failure(that, textStatus, xhr);
                                                PMDesigner.project.isSave = false;
                                            }
                                        });
                                    } else {
                                        messageEventDefinition = new MessageEventDefinition(eventCurrent);
                                    }
                                }
                        }

                    }
                }
            ],
            onShow: function (menu) {
                var targetElement = menu.getTargetElement(),
                    propertyEnabled = ['MESSAGECATCH', 'MESSAGETHROW', 'EMAIL', 'TIMER'];

                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();

                if (targetElement.evn_behavior === 'CATCH') {
                    targetElement.menu.getItems()[0].setVisible(true);
                    targetElement.menu.getItems()[1].setVisible(false);
                } else {
                    targetElement.menu.getItems()[0].setVisible(false);
                    targetElement.menu.getItems()[1].setVisible(true);
                }

                //Enable && Disabled - Properties
                if (propertyEnabled.indexOf(targetElement.evn_marker) >= 0) {

                    menu.items.find('id', 'intermediateProperties').enable();
                } else {
                    menu.items.find('id', 'intermediateProperties').disable();
                }
            }
        },
        boundary = {
            id: 'menuDataObject',
            items: [
                boundaryCatchMarker,
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                var targetElement = menu.getTargetElement();

                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();

            }
        },
        participant = {
            id: 'menuDataObject',
            items: [
                {
                    text: "Edit Label".translate(),
                    icon: "mafe-menu-edit-label-action",
                    onClick: function (menuOption) {
                        menuOption.getMenuTargetElement().label.canvas.hideAllFocusLabels();
                        menuOption.getMenuTargetElement().label.getFocus();
                    }
                },
                {
                    text: 'Delete'.translate(),
                    icon: 'mafe-menu-delete-action',
                    onClick: function (menuOption) {
                        PMUI.getActiveCanvas().removeElements();
                    }
                }
            ],
            onShow: function (menu) {
                PMUI.getActiveCanvas().emptyCurrentSelection();
                PMUI.getActiveCanvas().addToSelection(menu.getTargetElement());
                if (PMUI.getActiveCanvas().currentConnection) {
                    PMUI.getActiveCanvas().currentConnection.hidePortsAndHandlers();
                }
                PMUI.getActiveCanvas().hideDragConnectHandlers();
                PMUI.getActiveCanvas().hideAllFocusLabels();
            }
        };
    var defaults = {
        factory: {
            products: {
                'CANVAS': canvas,
                'TASK': task,
                'SUB_PROCESS': subProcess,
                'START': start,
                'END': end,
                'SELECTION': selection,
                'EVALUATION': evaluation,
                'PARALLEL': parallel,
                'EXCLUSIVE': exclusive,
                'EVENTBASED': exclusive,
                'EXCLUSIVEEVENTBASED': exclusive,
                'PARALLELEVENTBASED': exclusive,
                'INCLUSIVE': inclusive,
                'COMPLEX': complex,
                'VERTICAL_LINE': group,
                'HORIZONTAL_LINE': group,
                'GROUP': group,
                'LANE': participant,
                'DATAOBJECT': dataObject,
                'DATASTORE': dataStore,
                'TEXT_ANNOTATION': textAnnotation,
                'V_LABEL': textAnnotation,
                'H_LABEL': textAnnotation,
                'INTERMEDIATE': intermediate,
                'BOUNDARY': boundary,
                'PARTICIPANT': participant,
                'POOL': participant
            },

            defaultProduct: 'START'
        }
    };

    PMDesigner.contextMenuFactory = new ContextMenu(defaults.factory);
    PMDesigner.getMenuFactory = function (type) {
        if (prj_readonly === 'true') {
            return {};
        }
        return PMDesigner.contextMenuFactory.getProduct(type);
    };
    /**
     * Change task type marker
     * @param menuOption
     */
    function handlerMarkerType(menuOption) {
        var result = true,
            tempType = menuOption.getMenuTargetElement().getTaskType();
        menuOption.parent.hide();

        menuOption.getMenuTargetElement().switchTaskType(menuOption.id.toUpperCase());

        //validate if act loop type is parallel
        if (menuOption.getMenuTargetElement().act_loop_type === "PARALLEL") {
            handlerMarkerLoopType(menuOption, tempType)
        }

        //to enable save button
        PMDesigner.project.updateElement([]);

    }

    /**
     * Change marker loop type
     * @param menuOption
     * @param type
     * @returns {boolean}
     */
    function handlerMarkerLoopType(menuOption, type) {
        var message_window,
            loopType = menuOption.id.toUpperCase(),
            taskType = menuOption.getMenuTargetElement().act_task_type;
        if (typeof type !== 'undefined' && type !== null) {
            taskType = type;
        }

        if (menuOption.getMenuTargetElement().act_loop_type === "PARALLEL") {

            message_window = new PMUI.ui.MessageWindow({
                id: "handlerMarkerType",
                width: 490,
                title: 'Parallel Marker Type'.translate(),
                windowMessageType: 'warning',
                bodyHeight: 'auto',
                message: "The configuring of multiple instances will be lost".translate(),
                footerItems: [
                    {
                        text: 'Cancel'.translate(),
                        handler: function () {
                            menuOption.getMenuTargetElement().switchLoopType('PARALLEL');
                            menuOption.getMenuTargetElement().switchTaskType(taskType);
                            message_window.close();
                            return false;
                        },
                        buttonType: "error"
                    },
                    {
                        text: 'Continue'.translate(),
                        handler: function () {
                            try {
                                if (loopType == "loop" || loopType == "sequential") {
                                    menuOption.getMenuTargetElement().switchLoopType(loopType.toUpperCase());
                                } else {
                                    menuOption.getMenuTargetElement().switchLoopType('EMPTY');
                                }
                                PMDesigner.project.updateElement([]);
                            } catch (e) {
                                console.error("loop marker error", e.message);
                            }
                            message_window.close();
                            return true;
                        },
                        buttonType: "success"
                    }

                ]
            });
            message_window.open();
            message_window.showFooter();
        } else {
            menuOption.parent.hide();
            menuOption.getMenuTargetElement().switchLoopType(loopType.toUpperCase());
            PMDesigner.project.updateElement([]);
            return true;
        }
    }

}());
