/**
 * @class PMGateway
 * @param {Object} options
 */
var PMGateway = function (options) {
    PMShape.call(this, options);
    /**
     * Gateway id
     * @type {String}
     */
    this.gat_uid = null;
    /**
     * Gateway name
     * @type {String}
     */
    this.gat_name = null;
    /**
     * Gateway type, accept only: 'exclusive' and 'parallel' values
     * @type {String}
     */
    this.gat_type = null;
    /**
     * Gateway Direction, accept only 'unspecified', 'converging', 'diverging',
     * 'mixed'
     * @type {String}
     */
    this.gat_direction = null;
    /**
     * Instantiate property
     * @type {String}
     */
    this.gat_instantiate = null;
    /**
     * Event Gatewat Type property
     * @type {String}
     */
    this.gat_event_gateway_type = null;
    /**
     * Activation Count property
     * @type {Number}
     */
    this.gat_activation_count = null;
    /**
     * WaitingForStart property
     * @type {Boolean}
     */
    this.gat_waiting_for_start = null;
    /**
     * Default Flow property
     * @type {null}
     */
    this.gat_default_flow = null;
    this.defaltFlowMenuItem = null;
    this.businessObject = {};
    PMGateway.prototype.init.call(this, options);
};

PMGateway.prototype = new PMShape();
/**
 * Defines the object type
 * @type {String}
 */
PMGateway.prototype.type = 'PMGateway';
/**
 * Get data about object
 *
 */

PMGateway.prototype.mapBpmnType = {
    'EXCLUSIVE': 'bpmn:ExclusiveGateway',
    'INCLUSIVE': 'bpmn:InclusiveGateway',
    'PARALLEL': 'bpmn:ParallelGateway',
    'COMPLEX': 'bpmn:ComplexGateway',
    'EVENTBASED': 'bpmn:EventBasedGateway'
};
PMGateway.prototype.supportedArray = [
    'EXCLUSIVE',
    'INCLUSIVE',
    'PARALLEL'
];

PMGateway.prototype.getDataObject = function () {
    var name = this.getName(),
        container,
        element_id;
    switch (this.parent.type) {
        case 'PMCanvas':
            container = 'bpmnDiagram';
            element_id = this.canvas.id;
            break;
        case 'PMPool':
            container = 'bpmnPool';
            element_id = this.parent.id;
            break;
        case 'PMLane':
            container = 'bpmnLane';
            element_id = this.parent.id;
            break;
        default:
            container = 'bpmnDiagram';
            element_id = this.canvas.id;
            break;
    }
    return {
        gat_uid: this.getID(),
        gat_name: name,
        gat_type: this.gat_type,
        gat_direction: this.gat_direction,
        gat_instantiate: this.gat_instantiate,
        gat_event_gateway_type: this.gat_event_gateway_type,
        gat_activation_count: this.gat_activation_count,
        gat_waiting_for_start: this.gat_waiting_for_start,
        gat_default_flow: this.gat_default_flow,
        bou_x: this.x,
        bou_y: this.y,
        bou_width: this.width,
        bou_height: this.height,
        bou_container: container,
        bou_element: element_id,
        _extended: this.getExtendedObject()
    };
};
/**
 * Initialize the PMGateway object
 * @param options
 */
PMGateway.prototype.init = function (options) {
    var defaults = {
        gat_direction: 'DIVERGING',
        gat_instantiate: false,
        gat_event_gateway_type: 'NONE',
        gat_activation_count: 0,
        gat_waiting_for_start: true,
        gat_type: 'COMPLEX',
        gat_name: "Gateway",
        gat_default_flow: 0
    };
    jQuery.extend(true, defaults, options);
    this.setGatewayUid(defaults.gat_uid)
        .setGatewayType(defaults.gat_type)
        .setDirection(defaults.gat_direction)
        .setInstantiate(defaults.gat_instantiate)
        .setEventGatewayType(defaults.gat_event_gateway_type)
        .setActivationCount(defaults.gat_activation_count)
        .setWaitingForStart(defaults.gat_waiting_for_start)
        .setDefaultFlow(defaults.gat_default_flow);
    if (defaults.gat_name) {
        this.setName(defaults.gat_name);
    }
    this.setOnBeforeContextMenu(this.beforeContextMenu);
};

/**
 * Sets the Gateway ID
 * @param id
 * @return {*}
 */
PMGateway.prototype.setGatewayUid = function (id) {
    this.gat_uid = id;
    return this;
};
/**
 * Sets the label element
 * @param {String} value
 * @return {*}
 */
PMGateway.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.gat_name = name;
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Sets the gateway type
 * @param type
 * @return {*}
 */
PMGateway.prototype.setGatewayType = function (type) {
    type = type.toUpperCase();
    var defaultTypes = {
        COMPLEX: 'COMPLEX',
        EXCLUSIVE: 'EXCLUSIVE',
        PARALLEL: 'PARALLEL',
        INCLUSIVE: 'INCLUSIVE',
        EVENTBASED: 'EVENTBASED',
        EXCLUSIVEEVENTBASED: 'EXCLUSIVEEVENTBASED',
        PARALLELEVENTBASED: 'PARALLELEVENTBASED'

    };
    if (defaultTypes[type]) {
        this.gat_type = defaultTypes[type];
    }
    return this;
};
/**
 * Sets the Gateway direction
 * @param direction
 * @return {*}
 */
PMGateway.prototype.setDirection = function (direction) {
    direction = direction.toLowerCase();
    var defaultDir = {
        unspecified: 'UNSPECIFIED',
        diverging: 'DIVERGING',
        converging: 'CONVERGING',
        mixed: 'MIXED'
    };
    if (defaultDir[direction]) {
        this.gat_direction = defaultDir[direction];
    }
    return this;
};
/**
 * Sets the instantiate property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setInstantiate = function (value) {
    this.gat_instantiate = value;
    return this;
};
/**
 * Sets the event_gateway_type property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setEventGatewayType = function (value) {
    this.gat_event_gateway_type = value;
    return this;
};
/**
 * Sets the activation_count property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setActivationCount = function (value) {
    this.gat_activation_count = value;
    return this;
};
/**
 * Sets the waiting_for_start property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setWaitingForStart = function (value) {
    this.gat_waiting_for_start = value;
    return this;
};
/**
 * Sets te default_flow property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setDefaultFlow = function (value) {
    if (this.html) {
        PMShape.prototype.setDefaultFlow.call(this, value);
    }
    this.gat_default_flow = value;
    return this;
};

PMGateway.prototype.getGatewayType = function () {
    return this.gat_type;
};


PMGateway.prototype.createConfigureAction = function () {

};

PMGateway.prototype.cleanFlowConditions = function () {
    var i, port, connection, oldValues;
    for (i = 0; i < this.getPorts().getSize(); i += 1) {
        port = this.getPorts().get(i);
        connection = port.connection;
        if (connection.srcPort.parent.getID() === this.getID()) {
            oldValues = {
                condition: connection.getFlowCondition(),
                type: connection.getFlowType()
            };
            connection.setFlowCondition('');
            connection.canvas
                .triggerFlowConditionChangeEvent(connection, oldValues);
        }
    }
};
PMGateway.prototype.updateGatewayType = function (newType) {

};

PMGateway.prototype.updateDirection = function (newDirection) {

};

PMGateway.prototype.updateDefaultFlow = function (destID) {

};

PMGateway.prototype.changeTypeTo = function (type) {
    var command = new CommandChangeGatewayType(this, type);
    this.canvas.commandStack.add(command);
    command.execute();
    return this;
};

/**
 * Sets te default_flow property
 * @param value
 * @return {*}
 */
PMGateway.prototype.setDefaultFlow = function (value) {
    if (this.html) {
        PMShape.prototype.setDefaultFlow.call(this, value);
    }
    this.gat_default_flow = value;
    return this;
};

PMGateway.prototype.manualCreateMenu = function (e) {
    var tempMenu = new PMUI.menu.Menu({
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
                },
                disabled: true
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
    });
    tempMenu.setTargetElement(this);
    tempMenu.show(e.pageX, e.pageY + this.getZoomHeight() / 2 + 4);
};

PMGateway.prototype.beforeContextMenu = function () {
    var i, port, connection, shape, defaultflowItems = [], items, item, name,
        target, menuItem, hasMarker;
    this.canvas.hideAllCoronas();
    items = this.menu.items.find('id', 'gatewaytype').childMenu.items;
    for (i = 0; i < items.getSize(); i += 1) {
        menuItem = items.get(i);
        if (menuItem.id === 'gateway' +
            this.getGatewayType().toLowerCase()) {
            menuItem.disable();
            hasMarker = true;
        } else {
            menuItem.enable();
        }
    }


    defaultflowItems.push({
        text: 'None'.translate(),
        id: 'emptyFlow',
        disabled: (0 === parseInt(this.gat_default_flow)) ? true : false,
        onClick: function (menuOption) {
            target = menuOption.getMenuTargetElement();
            var cmd = new CommandDefaultFlow(target, 0);
            cmd.execute();
            target.canvas.commandStack.add(cmd);
        }
    });

    for (i = 0; i < this.getPorts().getSize(); i += 1) {
        port = this.getPorts().get(i);
        connection = port.connection;
        if (connection.srcPort.parent.getID() === this.getID()) {
            shape = connection.destPort.parent;
            switch (shape.getType()) {
                case 'PMActivity':
                    name = (shape.getName() !== '') ? shape.getName() : 'Task'
                        .translate();
                    break;
                case 'PMEvent':
                    name = (shape.getName() !== '') ? shape.getName() : 'Event'
                        .translate();
                    break;
                case 'PMGateway':
                    name = (shape.getName() !== '') ? shape.getName() : 'Gateway'
                        .translate();
                    break;
            }
            defaultflowItems.push(
                {
                    text: name,
                    id: connection.getID(),
                    disabled: (connection.getID() === this.gat_default_flow)
                        ? true : false,
                    onClick: function (menuOption) {
                        target = menuOption.getMenuTargetElement();
                        //target.setDefaultFlow(menuOption.id);
                        var cmd = new CommandDefaultFlow(target, menuOption.id);
                        cmd.execute();
                        target.canvas.commandStack.add(cmd);
                    }
                });
        }
    }
    if (this.defaltFlowMenuItem) {
        this.menu.removeItem('defaultflowMenu');
    }
    if ((defaultflowItems.length > 0) && (this.gat_type != "PARALLEL")) {
        this.defaltFlowMenuItem = {
            id: 'defaultflowMenu',
            text: "Default Flow".translate(),
            items: defaultflowItems
        };
        var last = this.menu.items.getLast();
        this.menu.addItem(this.defaltFlowMenuItem);
        this.menu.addItem(last);
    }
};

PMGateway.prototype.updateBpmGatewayType = function (newBpmnType) {
    var outgoing,
        incoming;

    outgoing = ( this.businessObject.elem && this.businessObject.elem.outgoing) ?
        this.businessObject.elem.outgoing : null;
    incoming = ( this.businessObject.elem && this.businessObject.elem.incoming) ?
        this.businessObject.elem.incoming : null;

    this.removeBpmn();
    this.businessObject.elem = null;
    this.createBpmn(newBpmnType);
    this.businessObject.elem.incoming = (incoming) ? incoming : null;
    this.businessObject.elem.outgoing = (outgoing) ? outgoing : null;
};

PMGateway.prototype.isSupported = function () {
    var isSupported = false;
    if (this.supportedArray.indexOf(this.getGatewayType()) !== -1) {
        isSupported = true;
    }
    return isSupported;
};
/**
 * evaluates the gateway address, according to the amount of input and output connections
 * @returns {PMGateway}
 */
PMGateway.prototype.evaluateGatewayDirection = function(){
    var incomings = this.getIncomingConnections('SEQUENCE', 'DEFAULT') || [],
        outgoings = this.getOutgoingConnections('SEQUENCE', 'DEFAULT') || [],
        direction = "DIVERGING";
    if (outgoings.length < incomings.length) {
        if (incomings.length === 1 && outgoings.length === 0){
            direction = "DIVERGING";
        }else{
            direction = "CONVERGING";
        }
    }
    this.setDirection(direction);
    return this;
};

/**
 * @inheritdoc
 **/
PMGateway.prototype.setIncomingConnections = function() {
    PMShape.prototype.setIncomingConnections.apply(this, arguments);
    return this.evaluateGatewayDirection();
};
/**
 * @inheritdoc
 **/
PMGateway.prototype.addIncomingConnection = function() {
    PMShape.prototype.addIncomingConnection.apply(this, arguments);
    return this.evaluateGatewayDirection();
};
/**
 * @inheritdoc
 **/
PMGateway.prototype.removeIncomingConnection = function() {
    PMShape.prototype.removeIncomingConnection.apply(this, arguments);
    return this.evaluateGatewayDirection();
};
/**
 * @inheritdoc
 **/
PMGateway.prototype.setOutgoingConnections = function() {
    PMShape.prototype.setOutgoingConnections.apply(this, arguments);
    return this.evaluateGatewayDirection();
};
/**
 * @inheritdoc
 **/
PMGateway.prototype.addOutgoingConnection = function() {
    PMShape.prototype.addOutgoingConnection.apply(this, arguments);
    return this.evaluateGatewayDirection();
};
/**
 * @inheritdoc
 **/
PMGateway.prototype.removeOutgoingConnection = function() {
    PMShape.prototype.removeOutgoingConnection.apply(this, arguments);
    return this.evaluateGatewayDirection();
};