/**
 * @class PMFlow
 * Handle the designer flows
 *
 * @constructor
 * Create a new flow object
 * @param {Object} options
 */
var PMFlow = function (options) {
    PMUI.draw.Connection.call(this, options);
    /**
     * Unique Idenfier
     * @type {String}
     */
    this.flo_uid = null;
    /**
     * Defines the connecion/flow type
     * @type {String}
     */
    this.flo_type = null;
    /**
     * Defines the connection/flow name
     * @type {String}
     */
    this.flo_name = null;
    /**
     * Unique Identifier of the source shape
     * @type {String}
     */
    this.flo_element_origin = null;
    /**
     * Defines the type of shape for the source
     * @type {String}
     */
    this.flo_element_origin_type = null;
    /**
     * Unique Identifier of the target shape
     * @type {String}
     */
    this.flo_element_dest = null;
    /**
     * Defines the type of shape for the target
     * @type {String}
     */
    this.flo_element_dest_type = null;
    /**
     * Defines if the flow was followed inmediately
     * @type {Boolean}
     */
    this.flo_is_inmediate = null;
    /**
     * Defines the condition to follow the flow
     * @type {String}
     */
    this.flo_condition = null;
    /**
     * X1 Coordinate
     * @type {Number}
     */
    this.flo_x1 = null;
    /**
     * Y1 Coordinate
     * @type {Number}
     */
    this.flo_y1 = null;
    /**
     * X2 Coordinate
     * @type {Number}
     */
    this.flo_x2 = null;
    /**
     * Y2 Coordinate
     * @type {Number}
     */
    this.flo_y2 = null;
    /**
     * Array of segments that conform the connection
     * @type {Array}
     */
    this.flo_state = null;

    this.label = null;

    this.algorithm = 'manhattan';

    PMFlow.prototype.init.call(this, options);
};
/**
 * Return all the connections between two elements.
 * @param {PMShape} sourceElement The source element from which an outgoing connection will be searched
 * @param {PMShape} destElement The destination element the connection must income to.
 * @param {Boolean} [ignoreDirection = false] If true, all connections between the two elements will be returned regardless the direction.
 * @returns {Boolean}
 */
PMFlow.getConnections = function (sourceElement, destElement, ignoreDirection) {
    var result;

    result = sourceElement.getOutgoingConnections().filter(function (i) {
        return i.getDestPort().getParent() === destElement;
    });

    if (ignoreDirection) {
        result.concat(sourceElement.getIncomingConnections().filter(function (i) {
            return i.getSrcPort().getParent === destElement;
        }));
    }

    return result;
};
/**
 * Check if there is any connection between two elements.
 * @param {PMShape} sourceElement The source element from which an outgoing connection will be searched
 * @param {PMShape} destElement The destination element the connection must income to.
 * @param {Boolean} [ignoreDirection = false] If true, all connections between the two elements will be searched regardless the direction.
 * @returns {Boolean}
 */
PMFlow.existsConnection = function (sourceElement, destElement, ignoreDirection) {
    return PMFlow.getConnections(sourceElement, destElement, ignoreDirection).length > 0;
};

PMFlow.prototype = new PMUI.draw.Connection();
/**
 * Defines the object type
 * @type {String}
 */
PMFlow.prototype.type = "Connection";  //TODO Replace this type by PMFlow when jCore will be updated

/**
 * Initialize the object with default values
 * @param {Object} options
 */
PMFlow.prototype.init = function (options) {
    var defaults = {
        flo_type: 'SEQUENCE',
        flo_is_inmediate: true,
        flo_x1: 0,
        flo_y1: 0,
        flo_x2: 0,
        flo_y2: 0,
        name: ''
    };
    jQuery.extend(true, defaults, options);
    this.setFlowType(defaults.flo_type)
        .setFlowUid(defaults.flo_uid)
        .setIsInmediate(defaults.flo_is_inmediate)
        .setOriginPoint(defaults.flo_x1, defaults.flo_y1)
        .setTargetPoint(defaults.flo_x2, defaults.flo_y2);

    this.setFlowName(defaults.name || '');
    this.setFlowOrigin(defaults.flo_element_origin || null, defaults.flo_element_origin_type || null);
    this.setFlowTarget(defaults.flo_element_dest || null, defaults.flo_element_dest_type || null);
    this.setFlowCondition(defaults.flo_condition || null);
    this.setFlowState(defaults.flo_state || null);
};

/**
 * Returns the flow's name
 * @return {String}
 */
PMFlow.prototype.getName = function () {
    return this.flo_name;
};
/**
 * Sets the label element
 * @param {String} value
 * @return {*}
 */
PMFlow.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.flo_name = name;
        this.setBPMName(name);
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Returns the flow conditions
 * @return {String}
 */
PMFlow.prototype.getFlowCondition = function () {
    return this.flo_condition;
};

/**
 * Defines the unique identiier property
 * @param {String} value
 * @return {*}
 */
PMFlow.prototype.setFlowUid = function (value) {
    this.flo_uid = value;
    return this;
};

/**
 * Defines the connection type
 * @param {String} type
 * @return {*}
 */
PMFlow.prototype.setFlowType = function (type) {
    this.flo_type = type;
    return this;
};

/** Return Flow Type
 *
 * @returns {String}
 */
PMFlow.prototype.getFlowType = function () {
    return this.flo_type;
};

/**
 * Sets the inmediately behavior of the connection
 * @param {Boolean} value
 * @return {*}
 */
PMFlow.prototype.setIsInmediate = function (value) {
    this.flo_is_inmediate = value;
    return this;
};

/**
 * Sets the origin point
 * @param {Number} x
 * @param {Number} y
 * @return {*}
 */
PMFlow.prototype.setOriginPoint = function (x, y) {
    this.flo_x1 = x;
    this.flo_y1 = y;
    return this;
};

/**
 * Sets the target point
 * @param {Number} x
 * @param {Number} y
 * @return {*}
 */
PMFlow.prototype.setTargetPoint = function (x, y) {
    this.flo_x2 = x;
    this.flo_y2 = y;
    return this;
};

/**
 * Sets the connection label
 * @param {String} name
 * @return {*}
 */
PMFlow.prototype.setFlowName = function (name) {
    this.flo_name = name;
    return this;
};

/**
 * Set the shape origin using input data
 * @param {String} code
 * @param {String} type
 * @return {*}
 */
PMFlow.prototype.setFlowOrigin = function (code, type) {
    this.flo_element_origin = code;
    this.flo_element_origin_type = type;
    return this;
};

/**
 * Set the shape target using input data
 * @param {String} code
 * @param {String} type
 * @return {*}
 */
PMFlow.prototype.setFlowTarget = function (code, type) {
    this.flo_element_dest = code;
    this.flo_element_dest_type = type;
    return this;
};

/**
 * Sets the flow conditions
 * @param value
 * @return {*}
 */
PMFlow.prototype.setFlowCondition = function (value) {
    this.flo_condition = value;
    return this;
};

/**
 * Sets the array of segments that conform the connection
 * @param {Array} state
 * @return {*}
 */
PMFlow.prototype.setFlowState = function (state) {
    this.flo_state = state;
    return this;
};

/**
 * Sets the origin data from a Shape
 * @param {PMShape} shape
 * @return {*}
 */
PMFlow.prototype.setOriginShape = function (shape) {
    var data;
    if (shape instanceof PMShape) {
        data = this.getNativeType(shape);
        this.flo_element_origin = data.code;
        this.flo_element_origin_type = data.type;
    }
    return this;
};

/**
 * Sets the target data from a Shape
 * @param {PMShape} shape
 * @return {*}
 */
PMFlow.prototype.setTargetShape = function (shape) {
    var data;
    if (shape instanceof PMShape) {
        data = this.getNativeType(shape);
        this.flo_element_dest = data.code;
        this.flo_element_dest_type = data.type;
    }
    return this;
};

/**
 * Returns the clean object to be sent to the backend
 * @return {Object}
 */
PMFlow.prototype.getDataObject = function () {
    var typeMap = {
            regular: 'SEQUENCE',
            segmented: 'MESSAGE',
            dotted: 'ASSOCIATION'
        },
        flo_x1 = 0,
        flo_y1 = 0,
        flo_x2 = 0,
        flo_y2 = 0,
        state = this.zoomPoints,
        flowElementOrigin,
        flowElementDest,
        portsOrigin,
        portOrigin,
        portsDest,
        portDest,
        k, j,
        bpmnMap = {
            'PMActivity': 'bpmnActivity',
            'PMEvent': 'bpmnEvent',
            'PMGateway': 'bpmnGateway',
            'PMArtifact': 'bpmnArtifact',
            'PMData': 'bpmnData',
            'PMParticipant': 'bpmnParticipant'
        },
        relatedObject;

    //For get initial port and end port
    flowElementOrigin = this.canvas.items.find("id", this.getSrcPort().parent.id);
    relatedObject = flowElementOrigin.relatedObject;
    this.evaluateRelatedObject(relatedObject);
    if (!flowElementOrigin) {
        throw new Error("Element not found!");
    }
    flowElementDest = this.canvas.items.find("id", this.getDestPort().parent.id);
    if (!flowElementDest) {
        throw new Error("Element not found!");
    }
    //Updating the positions, getting the last ports
    portsOrigin = flowElementOrigin.relatedObject.getPorts().asArray();
    for (k = 0; k < portsOrigin.length; k += 1) {
        if (portsOrigin[k].connection) {
            if (portsOrigin[k].connection.flo_uid === this.flo_uid) {
                portOrigin = portsOrigin[k];
            }
        }
    }
    if (!portOrigin) {
        portOrigin = {absoluteX: this.flo_x1, absoluteY: this.flo_y1};
    }
    portsDest = flowElementDest.relatedObject.getPorts().asArray();
    for (j = 0; j < portsDest.length; j += 1) {
        if (portsDest[j].connection) {
            if (portsDest[j].connection.flo_uid === this.flo_uid) {
                portDest = portsDest[j];
            }
        }
    }
    if (!portDest) {
        portDest = {absoluteX: this.flo_x2, absoluteY: this.flo_y2};
    }
    //get origin and target points from state array
    flo_x1 = state[0]['x'];
    flo_y1 = state[0]['y'];
    flo_x2 = state[state.length - 1]['x'];
    flo_y2 = state[state.length - 1]['y'];
    return {
        flo_uid: this.flo_uid,
        flo_type: this.flo_type,
        flo_name: this.flo_name,
        flo_element_origin: flowElementOrigin.id,
        flo_element_origin_type: bpmnMap[flowElementOrigin.type],
        flo_element_dest: flowElementDest.id,
        flo_element_dest_type: bpmnMap[flowElementDest.type],
        flo_is_inmediate: this.flo_is_inmediate,
        flo_condition: this.flo_condition,
        flo_state: state,
        flo_x1: flo_x1,
        flo_y1: flo_y1,
        flo_x2: flo_x2,
        flo_y2: flo_y2
    };
};
/**
 * evaluates the related object
 * @param {relatedObject} shape
 * @return {Object}
 */
PMFlow.prototype.evaluateRelatedObject = function (relatedObject) {
    var type, gat_direction, extendedType;
    if (relatedObject) {
        type = relatedObject.getType();
        if (type === 'PMGateway') {
            gat_direction = relatedObject.gat_direction;
            extendedType = relatedObject.extendedType;
            if (extendedType === 'EXCLUSIVE' && gat_direction === 'CONVERGING') {
                this.changeProperty("flo_condition", true);
            }
        }
    }
    return this;
};
/**
 * Change the property values if there are
 * @param {prop} property
 * @param {value} value
 * @return {Object}
 */
PMFlow.prototype.changeProperty = function (prop, value) {
    if (prop !== undefined && prop !== null) {
        this[prop] = value;
    } else {
        throw new Error("property not exist!");
    }
    return this;
};

/**
 * Converts the type to be sent to backend
 * @param {PMShape} shape
 * @return {Object}
 */
PMFlow.prototype.getNativeType = function (shape) {
    var type,
        code;
    switch (shape.getType()) {
        case 'PMActivity':
            type = "bpmnActivity";
            code = shape.act_uid;
            break;
        case 'PMGateway':
            type = "bpmnGateway";
            code = shape.gat_uid;
            break;
        case 'PMEvent':
            type = 'bpmnEvent';
            code = shape.evn_uid;
            break;
        case 'PMArtifact':
            type = "bpmnArtifact";
            code = shape.art_uid;
            break;
        case 'PMData':
            type = "bpmnData";
            code = shape.dat_uid;
            break;
        case 'PMParticipant':
            type = "bpmnParticipant";
            code = shape.dat_uid;
            break;
    }
    return {
        "type": type,
        "code": code
    };
};

PMFlow.prototype.showMoveHandlers = function () {
    PMUI.draw.Connection.prototype.showMoveHandlers.call(this);
    this.canvas.updatedElement = [{
        relatedObject: this
    }];
    $(this.html).trigger('selectelement');
    return this;
};

/**
 * Get Segment Width
 * @returns {Number}
 */
PMFlow.prototype.getSegmentHeight = function (index) {
    if (this.lineSegments.getSize()) {
        return Math.abs(this.lineSegments.get(index).endPoint.y
            - this.lineSegments.get(index).startPoint.y);
    }
    return 0;
};
/**
 * Get Segment Width
 * @returns {Number}
 */
PMFlow.prototype.getSegmentWidth = function (index) {
    if (this.lineSegments.getSize()) {
        return Math.abs(this.lineSegments.get(index).endPoint.x
            - this.lineSegments.get(index).startPoint.x);
    }
    return 0;
};
/**
 * Get Label Coordinates
 * @returns {Point}
 */
PMFlow.prototype.getLabelCoordinates = function () {
    var x, y, index = 0, diffX, diffY, i, max;

    if (this.lineSegments.getSize()) {
        max = (this.getSegmentWidth(0) > this.getSegmentHeight(0)) ?
            this.getSegmentWidth(0) : this.getSegmentHeight(0);

        for (i = 1; i < this.lineSegments.getSize(); i += 1) {
            diffX = this.getSegmentWidth(i);
            diffY = this.getSegmentHeight(i);
            if (diffX > max + 1) {
                max = diffX;
                index = i;
            } else if (diffY > max + 1) {
                max = diffY;
                index = i;
            }
        }
        diffX = (this.lineSegments.get(index).endPoint.x
            - this.lineSegments.get(index).startPoint.x) / 2;
        diffY = (this.lineSegments.get(index).endPoint.y
            - this.lineSegments.get(index).startPoint.y) / 2;
        x = this.lineSegments.get(index).startPoint.x + diffX;
        y = this.lineSegments.get(index).startPoint.y + diffY;
    } else {
        x = this.srcPort.getAbsoluteX();
        y = this.srcPort.getAbsoluteY();
    }

    return new PMUI.util.Point(x, y);
};
/**
 * Extended paint connection
 * @param {Object} options Configuration options
 * @chainable
 */
PMFlow.prototype.paint = function (options) {
    PMUI.draw.Connection.prototype.paint.call(this, options);
    // force to z-order if container parent is the canvas
    if (this.getSrcPort().getParent().getParent().getType() === 'PMCanvas'
        && this.getDestPort().getParent().getParent().getType() === 'PMCanvas') {
        this.setZOrder(1);
    } else {
        this.setZOrder(102);
    }
};
/**
 * Connects two PM Figures
 * @returns {Connection}
 */
PMFlow.prototype.connect = function (options) {
    var labelPoint;
    PMUI.draw.Connection.prototype.connect.call(this, options);
    labelPoint = this.getLabelCoordinates();
    this.label = new PMUI.draw.Label({
        message: this.getName(),
        canvas: this.canvas,
        parent: this,
        position: {
            location: "bottom",
            diffX: labelPoint.getX() / this.canvas.zoomFactor,
            diffY: labelPoint.getY() / this.canvas.zoomFactor + 10

        }
    });
    this.html.appendChild(this.label.getHTML());
    this.label.paint()
    this.label.attachListeners();
    this.label.setDimension(100, "auto");
    this.label.setLabelPosition(this.label.location, this.label.diffX, this.label.diffY);
    return this;
};

PMFlow.prototype.changeFlowType = function (type) {
    var segmentStyle, destDecorator,
        typeMap = {
            'default': {
                srcPrefix: 'mafe-default',
                destPrefix: 'mafe-sequence'
            },
            'conditional': {
                srcPrefix: 'mafe-decorator_conditional',
                destPrefix: 'mafe-decorator_default'
            },
            'sequence': {
                srcPrefix: 'mafe-sequence',
                destPrefix: 'mafe-sequence'
            }
        }, srcDecorator;

    if (type === 'association') {
        segmentStyle = "dotted";
        destDecorator = "con-none";
    } else {
        segmentStyle = "regular";
    }
    this.setSegmentStyle(segmentStyle);
    this.originalSegmentStyle = segmentStyle;
    if (type === 'association') {
        if (srcDecorator && this.srcDecorator) {
            this.srcDecorator
                .setDecoratorPrefix(srcDecorator);
        } else {
            this.srcDecorator
                .setDecoratorPrefix("mafe-decorator");
        }
        this.srcDecorator.paint();
    } else {
        this.srcDecorator.setDecoratorPrefix(typeMap[type].srcPrefix)
            .setDecoratorType("source")
            .paint();

        this.destDecorator.setDecoratorPrefix(typeMap[type].destPrefix)
            .setDecoratorType("target")
            .paint();
        this.disconnect()
            .connect()
            .setSegmentMoveHandlers()
            .checkAndCreateIntersectionsWithAll();
        return this;
    }
    if (destDecorator && this.srcDecorator) {
        this.destDecorator
            .setDecoratorPrefix(destDecorator);
    } else {
        this.destDecorator
            .setDecoratorPrefix("mafe-decorator");
    }
    this.srcDecorator.paint();
    this.disconnect();
    this.connect();
    return this;
};

PMFlow.prototype.saveAndDestroy = function () {
    var otherConnection, sizeIntersection, bar;
    sizeIntersection = this.intersectionWith.getSize();
    PMUI.draw.Connection.prototype.saveAndDestroy.call(this);
    bar = this.intersectionWith.asArray().slice();
    bar.reverse();
    for (i = 0; i < sizeIntersection; i += 1) {
        otherConnection = bar[i];
        otherConnection
            .setSegmentColor(otherConnection.originalSegmentColor, false)
            .setSegmentStyle(otherConnection.originalSegmentStyle, false)
            .disconnect()
            .connect();
        otherConnection.setSegmentMoveHandlers();
        otherConnection.checkAndCreateIntersectionsWithAll();
    }
    if (this.getFlowType() === 'DEFAULT') {
        this.getSrcPort().getParent().updateDefaultFlow("");
    }
    this.updateIncomingAndOutgoingConnections("remove");

    return;
};

PMFlow.prototype.showPortsAndHandlers = function () {
    this.canvas.hideAllCoronas();
    this.showMoveHandlers();
    this.showPorts();
    return this;
};

PMFlow.prototype.showPorts = function () {
    var connectHandler,
        connectHandler2,
        portPoint,
        handlerOriginalSize = 15,
        handlerDimension = handlerOriginalSize * this.canvas.getZoomFactor();

    this.canvas.hideDragConnectHandlers();

    portPoint = this.srcPort.getPoint();
    connectHandler = this.canvas.dragConnectHandlers.get(0);
    connectHandler.setDimension(handlerDimension, handlerDimension);
    connectHandler.setPosition(portPoint.x - Math.floor(handlerDimension / 2), portPoint.y - Math.floor(handlerDimension / 2));
    connectHandler.setVisible(true);
    connectHandler.relativeShape = this.srcPort;
    connectHandler.attachListeners();
    
    portPoint = this.destPort.getPoint();
    connectHandler2 = this.canvas.dragConnectHandlers.get(1);
    connectHandler2.setDimension(handlerDimension, handlerDimension);
    connectHandler2.setPosition(portPoint.x - Math.floor(handlerDimension / 2), portPoint.y - Math.floor(handlerDimension / 2));
    connectHandler2.setVisible(true);
    connectHandler2.relativeShape = this.destPort;
    connectHandler2.attachListeners();
    return this;
};

PMFlow.prototype.hidePortsAndHandlers = function () {
    this.hideMoveHandlers();
    this.canvas.hideDragConnectHandlers();
    return this
};

PMFlow.prototype.getBpmnElementType = function () {
    var map = {
        'SEQUENCE': 'bpmn:SequenceFlow',
        'ASSOCIATION': 'bpmn:Association',
        'MESSAGE': 'bpmn:MessageFlow'
    };
    var type = map[this.flo_type] || 'bpmn:SequenceFlow';
    if (this.flo_type === 'DATAASSOCIATION') {
        if (this.flo_element_origin_type === 'bpmnData') {
            type = 'bpmn:DataInputAssociation';
        } else {
            type = 'bpmn:DataOutputAssociation';
        }
    }
    return type;
};

PMFlow.prototype.createWithBpmn = function (bpmnElementType) {
    var businessObject = PMDesigner.bpmnFactory.create(bpmnElementType, {
        id: 'flo_' + this.id,
        name: this.getName() ? this.getName() : ""
    });
    businessObject.di = PMDesigner.bpmnFactory.createDiEdge(businessObject, [], {
        id: businessObject.id + '_di'
    });
    this.businessObject = businessObject;
};

PMFlow.prototype.updateConnectionWaypoints = function () {
    this.businessObject.di.set('waypoint', PMDesigner.bpmnFactory.createDiWaypoints(this.waypoints));
};

PMFlow.prototype.updateConnection = function (newSource, newTarget) {
    var businessObject = this.businessObject,
        children;
    if (this.flo_type === 'DATAASSOCIATION') {
        if (this.flo_element_origin_type === 'bpmnData') {
            children = newTarget.elem.get('dataInputAssociations');
            CollectionRemove(children, businessObject);
            businessObject.sourceRef = [];
            if (!children) {
                newTarget.elem.dataInputAssociations = [];
                newTarget.elem.dataInputAssociations.push(businessObject);
            } else {
                children.push(businessObject);
            }
            businessObject.sourceRef.push(newSource.elem);
        } else {
            children = newSource.elem.get('dataOutputAssociations');
            CollectionRemove(children, businessObject);
            businessObject.targetRef = [];
            newSource.elem.get('dataOutputAssociations').push(businessObject);
            businessObject.targetRef = newTarget.elem;
        }
    } else {
        var inverseSet = businessObject.$instanceOf('bpmn:SequenceFlow');
        if (businessObject.sourceRef !== newSource.elem) {
            if (inverseSet) {
                CollectionRemove(businessObject.sourceRef && businessObject.sourceRef.get('outgoing'), businessObject);

                if (newSource.elem) {
                    newSource.elem.get('outgoing').push(businessObject);
                }
            }

            businessObject.sourceRef = newSource.elem;
        }
        if (businessObject.targetRef !== newTarget.elem) {
            if (inverseSet) {
                CollectionRemove(businessObject.targetRef && businessObject.targetRef.get('incoming'), businessObject);

                if (newTarget.elem) {
                    newTarget.elem.get('incoming').push(businessObject);
                }
            }
            businessObject.targetRef = newTarget.elem;
        }
    }
    businessObject.di.set('waypoint', PMDesigner.bpmnFactory.createDiWaypoints(this.points));
};

PMFlow.prototype.updateShapeParent = function (businessObject, parentBusinessObject) {
    var parentBusinessObjectAux = {};
    if (this.flo_type === 'MESSAGE') {
        if (this.srcPort.parent.businessObject.elem
            && this.destPort.parent.businessObject.elem
            && this.srcPort.parent.businessObject.elem.$parent.id !== this.destPort.parent.businessObject.elem.$parent.id) {

            parentBusinessObjectAux.elem = _.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"});
        } else {
            if (this.srcPort.parent.type === 'PMParticipant') {
                parentBusinessObjectAux.elem = this.srcPort.parent && this.srcPort.parent.participantObject.elem.$parent;
            } else if (this.srcPort.parent) {
                parentBusinessObjectAux.elem = this.destPort.parent && this.destPort.parent.participantObject.elem.$parent;
            }
        }
    }
    if (parentBusinessObjectAux.elem) {
        this.updateSemanticParent(businessObject, parentBusinessObjectAux);
    } else {
        this.updateSemanticParent(businessObject, parentBusinessObject);
    }

    this.updateDiParent(businessObject.di, parentBusinessObject.di);
};

PMFlow.prototype.updateSemanticParent = function (businessObject, newParent) {
    var children;
    if (businessObject.$parent === newParent.elem) {
        return;
    }
    if (this.flo_type !== 'DATAASSOCIATION') {
        if (this.flo_type === 'MESSAGE') {
            //HERE MESSAGE FLOW SET TO COLLABORATIONS
            if (businessObject.$parent) {
                // remove from old parent
                children = businessObject.$parent.get('messageFlows');
                CollectionRemove(children, businessObject);
            }

            if (!newParent.elem) {
                businessObject.$parent = null;
            } else {
                children = newParent.elem.get('messageFlows');
                children.push(businessObject);
                businessObject.$parent = newParent.elem;
            }
        } else {
            if (businessObject.$parent) {
                // remove from old parent
                children = businessObject.$parent.get('flowElements');
                CollectionRemove(children, businessObject);
            }

            if (!newParent.elem) {
                businessObject.$parent = null;
            } else {
                // add to new parent
                if (newParent.elem.$type === 'bpmn:Lane') {
                    children = newParent.elem.$parent.$parent.get('flowElements');
                } else if (this.getSrcPort().getParent().getType() === 'PMEvent'
                    && this.getSrcPort().getParent().getEventType() === 'BOUNDARY') {
                    children = newParent.elem.$parent.get('flowElements');
                } else {
                    children = newParent.elem.get('flowElements');
                }
                children.push(businessObject);
                businessObject.$parent = newParent.elem;
            }
        }
    }
};

PMFlow.prototype.updateDiParent = function (di, parentDi) {

    if (parentDi && !parentDi.$instanceOf('bpmndi:BPMNPlane')) {
        parentDi = parentDi.$parent;
    }

    if (di.$parent === parentDi) {
        return;
    }
    var planeElements = (parentDi || di.$parent).get('planeElement');
    if (parentDi) {
        planeElements.push(di);
        di.$parent = parentDi;
    } else {
        CollectionRemove(planeElements, di);
    }
};

PMFlow.prototype.createBpmn = function (bpmnElementType) {
    var newSource, newTarget;
    this.createWithBpmn(bpmnElementType);

    this.updateShapeParent(this.businessObject, this.srcPort.parent.parent.businessObject);
    newSource = this.srcPort.parent && this.srcPort.parent.businessObject;
    newTarget = this.destPort.parent && this.destPort.parent.businessObject;
    if (this.srcPort.parent.type == 'PMParticipant') {
        newSource = this.srcPort.parent && this.srcPort.parent.participantObject;
    }
    if (this.destPort.parent.type == 'PMParticipant') {
        newTarget = this.destPort.parent && this.destPort.parent.participantObject;
    }
    this.updateConnection(newSource, newTarget);
};
PMFlow.prototype.removeBpmn = function () {
    var parentShape,
        businessObject,
        newSource,
        newTarget,
        children,
        parentBusinessObject,
        parentDi;
    businessObject = this.businessObject;

    this.updateSemanticParent(businessObject, {elem: null});
    this.updateDiParent(businessObject.di);

    if (this.flo_type !== 'DATAASSOCIATION') {
        parentShape = this.parent;
        parentBusinessObject = parentShape && parentShape.businessObject;
        parentDi = parentBusinessObject && parentBusinessObject.di;
        CollectionRemove(businessObject.sourceRef && businessObject.sourceRef.get('outgoing'), businessObject);
        CollectionRemove(businessObject.targetRef && businessObject.targetRef.get('incoming'), businessObject);
    } else {
        newSource = this.srcPort.parent && this.srcPort.parent.businessObject.elem,
            newTarget = this.destPort.parent && this.destPort.parent.businessObject.elem;

        if (this.flo_element_origin_type === 'bpmnData') {
            children = newTarget.get('dataInputAssociations');
            CollectionRemove(children, businessObject);

        } else {
            children = newSource.get('dataOutputAssociations');
            CollectionRemove(children, businessObject);
        }
    }
};

PMFlow.prototype.updateBpmn = function () {
    var newSource = this.srcPort.parent && this.srcPort.parent.businessObject,
        newTarget = this.destPort.parent && this.destPort.parent.businessObject;
    this.updateConnection(newSource, newTarget);
};

PMFlow.prototype.setBPPMName = function (name) {
    if (this.businessObject || this.participantObject) {
        this.businessObject.name = name;
    }
};
/**
 * Sets flow name to export as bpmn xml standard
 * @param name
 */
PMFlow.prototype.setBPMName = function (name) {
    if (this.businessObject) {
        this.businessObject.name = name;
    }
};

PMFlow.prototype.reconectSwitcher = function (delta, inContainer, rootType) {
    var srcElem,
        destElem;
    //get source and target element
    srcElem = this.getSrcPort().parent;
    destElem = this.getDestPort().parent;
    //verify if is the same process
    if (srcElem.businessObject.elem
        && destElem.businessObject.elem
        && srcElem.businessObject.elem.$parent.id === destElem.businessObject.elem.$parent.id) {
        if (rootType && rootType === 'PMLane') {
            this.reconnectManhattah(inContainer);
        } else {
            this.reconnectUser(delta, inContainer);
        }
    } else {
        this.reconnectManhattah(inContainer);
    }
    this.setSegmentMoveHandlers();
    this.checkAndCreateIntersectionsWithAll();
    this.canvas.triggerUserStateChangeEvent(this);
};
/**
 * updates the list of items related input and output
 * @param action {String}, You can have two values, "remove" or "create"
 * @returns {PMFlow}
 */
PMFlow.prototype.updateIncomingAndOutgoingConnections = function(action){
    var source, destiny, sourceShape, destinyShape;
    source = this.srcPort;
    destiny = this.destPort;
    sourceShape = source.parent;
    destinyShape = destiny.parent;
    if (sourceShape && destinyShape){
        if (action === "remove"){
            sourceShape.removeOutgoingConnection(this);
            destinyShape.removeIncomingConnection(this);
        } else if (action === "create"){
            sourceShape.addOutgoingConnection(this);
            destinyShape.addIncomingConnection(this);
        }
    }
    return this;
};
