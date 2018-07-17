var PMLane = function (options) {
    PMShape.call(this, options);
    /**
     *
     * @type {String}
     */
    this.name = '';
    /**
     *
     * @type {String}
     */
    this.proUid = '';
    /**
     *
     * @type {String}
     */
    this.childLaneset = '';
    /**
     *
     * @type {Number}
     */
    this.relPosition = 0;

    this.parentPool = null;

    this.childPool = null;
    /**
     * Private property which indicates if a shape was just created inside the lane.
     * @type {boolean}
     */
    this.shapeJustCreated = false;

    PMLane.prototype.initObject.call(this, options);
};

PMLane.prototype = new PMShape();
PMLane.prototype.type = 'PMLane';
PMLane.prototype.laneContainerBehavior = null;


PMLane.prototype.getDataObject = function () {
    var name = this.getName();
    return {
        lan_uid: this.id,
        lns_uid: this.parent.id,
        lan_name: name,
        bou_x: this.x,
        bou_y: this.y,
        bou_width: this.width,
        bou_height: this.height,
        bou_rel_position: this.getRelPosition(),
        element_id: this.parent.id,
        bou_container: 'bpmnPool',
        lan_parent_pool: this.parentPool,
        _extended: this.getExtendedObject()
    };
};

PMLane.prototype.initObject = function (options) {
    var defaultOptions = {
        lan_name: 'Lane',
        proUid: null,
        childLaneset: null,
        relPosition: 0,
        childPool: null,
        orientation: 'HORIZONTAL'
    };
    $.extend(true, defaultOptions, options);

    this.setName(defaultOptions.lan_name)
        .setProUid(defaultOptions.proUid)
        .setRelPosition(defaultOptions.relPosition)
        .setChildPool(defaultOptions.childPool)
        .setOrientation(defaultOptions.orientation);
};
PMLane.prototype.paint = function () {
    if (this.parent.orientation === 'VERTICAL') {
        this.getLabels().get(0).setOrientation('horizontal');
        this.getLabels().get(0).setLabelPosition('top');
    } else {
        this.getLabels().get(0).setOrientation('vertical');
        this.getLabels().get(0).setLabelPosition('center-left', 20, 0);

    }
    if (this.childPool) {
        this.getLabels().get(0).setVisible(false);
    }
    this.getLabels().get(0).setZOrder(1);
    if (this.corona) {
        this.corona.paint();
        this.corona.hide();
    }
};

/**
 * Factory of pool behaviors. It uses lazy instantiation to create
 * instances of the different container behaviors
 * @param {String} type An string that specifies the container behavior we want
 * an instance to have, it can be regular or nocontainer
 * @return {ContainerBehavior}
 */
PMLane.prototype.containerBehaviorFactory = function (type) {
    if (type === 'lane') {
        if (!this.laneContainerBehavior) {
            this.laneContainerBehavior = new LaneContainerBehavior();
        }
        return this.laneContainerBehavior;
    } else {

        return PMShape.prototype.containerBehaviorFactory.call(this, type);
    }
};
PMLane.prototype.setResizeBehavior = function (behavior) {
    var factory = new PMUI.behavior.BehaviorFactory({
        products: {
            "regularresize": PMUI.behavior.RegularResizeBehavior,
            "Resize": PMUI.behavior.RegularResizeBehavior,
            "yes": PMUI.behavior.RegularResizeBehavior,
            "resize": PMUI.behavior.RegularResizeBehavior,
            "noresize": PMUI.behavior.NoResizeBehavior,
            "NoResize": PMUI.behavior.NoResizeBehavior,
            "no": PMUI.behavior.NoResizeBehavior,
            "laneResize": PMLaneResizeBehavior
        },
        defaultProduct: "noresize"
    });
    this.resizeBehavior = factory.make(behavior);
    if (this.html) {
        this.resize.init(this);
    }
    return this;
};
/**
 * Convert a lane to Pool
 */
PMLane.prototype.transformToPool = function () {
    var customShape = new BPMNPool({
        width: this.getWidth() - 1,
        height: this.getHeight() - 1,
        "canvas": this.canvas,
        "connectAtMiddlePoints": false,
        container: 'pool',
        topLeft: true,
        drop: {
            type: 'bpmnconnectioncontainer',
            selectors: ["#BPMNLane",
                "#BPMNActivity", "#BPMNSubProcess", "#BPMNStartEvent",
                "#BPMNEndEvent", "#BPMNIntermediateEvent", "#BPMNGateway",
                "#BPMNDataObject", "#BPMNDataStore", "#BPMNGroup",
                "#BPMNAnnotation", ".bpmn_droppable", ".custom_shape"],
            overwrite: true
        },
        drag: 'noDrag',
        resizeBehavior: "no",
        resizeHandlers: {
            type: "Rectangle",
            total: 4,
            resizableStyle: {
                cssProperties: {
                    'background-color': "rgb(0, 255, 0)",
                    'border': '1px solid black'
                }
            },
            nonResizableStyle: {
                cssProperties: {
                    'background-color': "white",
                    'border': '1px solid black'
                }
            }
        },
        "style": {
            cssClasses: []
        },
        layers: [
            {
                layerName: "first-layer",
                priority: 2,
                visible: true,
                style: {
                    cssProperties: {}
                }
            }
        ],
        labels: [
            {
                message: this.getMessage(),
                width: 50,
                height: 10,
                orientation: 'vertical',
                position: {
                    location: 'center-left',
                    diffX: 20,
                    diffY: 0
                },
                updateParent: false
            }
        ],
        orientation: this.parent.getOrientation(),
        name: this.getName(),
        parentLane: this.getID()
    });
    this.labels.get(0).setVisible(false);
    this.setChildPool(customShape.getID());
    this.addElement(customShape, 1, 0, true);
    customShape.labels.get(0).setVisible(true);
    this.canvas.triggerSinglePropertyChangeEvent(this, 'childPool', this.getChildPool(), customShape.getID());
    customShape.attachListeners();
    this.canvas.updatedElement = customShape;
    var command = new jCore.CommandCreate(customShape);
    this.canvas.commandStack.add(command);
    command.execute();
};

PMLane.prototype.createHTML = function () {
    PMShape.prototype.createHTML.call(this);
    if (this.getRelPosition() > 1) {
        this.style.addProperties({
            'border-top': '2px solid #3b4753'
        });
    }
    return this.html;
};

PMLane.prototype.attachListeners = function () {
    var $lane = $(this.html);
    if (!this.html) {
        return this;
    }
    PMShape.prototype.attachListeners.call(this);
};

PMLane.prototype.onMouseDown = function (shape) {
    return function (e, ui) {
        if (shape.getType() === 'PMPool' || shape.getType() === 'PMLane') {
            shape.canvas.cancelConnect();
        }
        PMUI.draw.CustomShape.prototype.onMouseDown.call(this, shape)(e, ui);
        shape.setDragBehavior('nodrag');
    };
};

PMLane.prototype.destroy = function () {
    var parentPool = this.canvas.customShapes.find('id', this.parent.getID());
    if (parentPool) {
        parentPool.removeLane(this);
        parentPool.updateOnRemoveLane(this);

    }
    return this;
};
PMLane.prototype.setChildPool = function (pPool) {
    this.childPool = pPool;
    return this;
};
PMLane.prototype.setParentPool = function (pPool) {
    this.parentPool = pPool;
    return this;
};
/**
 * Set lane name
 * @param {String} name
 * @returns {BPMNLane}
 */
PMLane.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.lan_name = name;
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Set lane type
 * @param {String} newType
 * @returns {BPMNLane}
 */
PMLane.prototype.setType = function (newType) {
    this.type = newType;
    return this;
};
/**
 * Set process uid asociated to lane
 * @param {String} uid
 * @returns {BPMNLane}
 */
PMLane.prototype.setProUid = function (uid) {
    this.proUid = uid;
    return this;
};
/**
 * Set the relative position of lane
 * @param {Number} relPosition
 * @returns {BPMNLane}
 */
PMLane.prototype.setRelPosition = function (relPosition) {
    this.relPosition = relPosition;
    return this;
};

PMLane.prototype.setOrientation = function (orientation) {
    this.orientation = orientation;
    return this;
};
/**
 * Set ChildLaneset to lane
 * @param {BPMNPool} childLane
 * @returns {BPMNLane}
 */
PMLane.prototype.setChildLaneset = function (childLane) {
    this.childLaneset = childLane;
    return this;
};
/**
 * Get the lane name
 * @returns {String}
 */
PMLane.prototype.getName = function () {
    return this.lan_name;
};

PMLane.prototype.getType = function () {
    return this.type;
};
/**
 * Get pro uid asociated to lane
 * @returns {String}
 */
PMLane.prototype.getProUid = function () {
    return this.proUid;
};
/**
 * Get the relative position of lane
 * @returns {Number}
 */
PMLane.prototype.getRelPosition = function () {
    return this.relPosition;
};
PMLane.prototype.getOrientation = function () {
    return this.orientation;
};
/**
 * Get ChildLaneSet
 * @returns {BPMNPool}
 */
PMLane.prototype.getChildLaneset = function () {
    return this.childLaneset;
};
PMLane.prototype.getChildPool = function () {
    return this.childPool;
};
PMLane.prototype.applyZoom = function () {
    PMShape.prototype.applyZoom.call(this);
    this.updateDimensions();
};
PMLane.prototype.updateDimensions = function (margin) {
    var minW,
        minH,
        children = this.getChildren(),
        limits = children.getDimensionLimit(),
        margin = 30,
        $shape = $(this.getHTML());
    minW = (limits[1] + margin) * this.canvas.getZoomFactor();
    minH = (limits[2] + margin) * this.canvas.getZoomFactor();
    // update jQueryUI's minWidth and minHeight
    $shape.resizable();
    $shape.resizable('option', 'minWidth', minW);
    $shape.resizable('option', 'minHeight', minH);
    return this;
};

/**
 * That method shows or hides lane resize behaviors
 * @param visible
 * @returns {PMLane}
 */
PMLane.prototype.showOrHideResizeHandlers = function (visible) {
    var i;
    if (!visible) {
        visible = false;
    }
    for (i = 0; i < this.cornerResizeHandlers.getSize(); i += 1) {
        this.cornerResizeHandlers.get(i).setVisible(false);
    }

    for (i = 0; i < this.midResizeHandlers.getSize(); i += 1) {
        if (i === 2) {
            this.midResizeHandlers.get(i).setVisible(visible);
        } else {
            this.midResizeHandlers.get(i).setVisible(false);
        }
    }
    return this;
};

PMLane.prototype.updateAllRelatedDimensions = function (avoidWeight) {
    this.parent.updateAllLaneDimension(avoidWeight);
    this.parent.paint();
    return this;
};

PMLane.prototype.stringify = function () {
    var inheritedJSON = PMShape.prototype.stringify.call(this),
        thisJSON = {
            name: this.getName(),
            proUid: this.getProUid(),
            relPosition: this.getRelPosition(),
            childPool: this.getChildPool()
        };
    $.extend(true, inheritedJSON, thisJSON);
    return inheritedJSON;
};

PMLane.prototype.createBpmn = function (type) {
    var bpmnLaneset;
    if (!this.businessObject.elem) {
        if (!this.parent.businessObject.elem) {
            this.parent.createBusinesObject();
        }
        bpmnLaneset = this.createLaneset();
        this.createWithBpmn('bpmn:Lane', 'businessObject');
        this.updateBounds(this.businessObject.di);
        this.updateSemanticParent(this.businessObject, {elem: bpmnLaneset});
        this.updateDiParent(this.businessObject.di, this.parent.parent.businessObject.di);
    }
};

PMLane.prototype.createLaneset = function () {
    var bpmnLaneset;
    if (!(_.findWhere(this.parent.businessObject.elem.get('flowElements'), {$type: "bpmn:LaneSet"}))) {
        bpmnLaneset = PMDesigner.moddle.create('bpmn:LaneSet');
        bpmnLaneset.$parent = this.parent.businessObject.elem;
        this.parent.businessObject.elem.get('flowElements').push(bpmnLaneset);
    } else {
        bpmnLaneset = _.findWhere(this.parent.businessObject.elem.get('flowElements'), {$type: "bpmn:LaneSet"});

    }
    return bpmnLaneset;
};

PMLane.prototype.updateSemanticParent = function (businessObject, newParent) {
    var children;
    if (businessObject.elem.$parent === newParent.elem) {
        return;
    }
    if (businessObject.elem.$parent) {
        // remove from old parent
        children = businessObject.elem.$parent.get('lanes');
        CollectionRemove(children, businessObject.elem);
    }

    if (!newParent.elem) {
        businessObject.elem.$parent = null;
    } else {
        // add to new parent
        children = newParent.elem.get('lanes');
        children.push(businessObject.elem);
        businessObject.elem.$parent = newParent.elem;
    }
};

PMLane.prototype.removeBpmn = function () {
    var bpmnLaneset;
    this.parent.updateBounds(this.parent.participantObject.di);
    PMShape.prototype.removeBpmn.call(this);
    if ((_.findWhere(this.parent.businessObject.elem.get('flowElements'), {$type: "bpmn:LaneSet"}))) {
        bpmnLaneset = _.findWhere(this.parent.businessObject.elem.get('flowElements'), {$type: "bpmn:LaneSet"});
        if (bpmnLaneset.lanes < 1) {
            CollectionRemove(this.parent.businessObject.elem.get('flowElements'), bpmnLaneset);
        }
    }
};

PMLane.prototype.updateBpmn = function () {
    this.updateBounds(this.businessObject.di);
};

PMLane.prototype.laneRefreshConnections = function (delta) {
    var i,
        max,
        srcElem,
        destElem,
        connection,
        delta;
    for (i = 0, max = this.canvas.refreshArray.getSize(); i < max; i += 1) {
        connection = this.canvas.refreshArray.get(i);
        srcElem = connection.getSrcPort().parent;
        destElem = connection.getDestPort().parent;
        if (this.isConnetionIntoLane(srcElem)
            && this.isConnetionIntoLane(destElem)) {
            connection.reconnectUser(delta, false);
            connection.setSegmentMoveHandlers();
            connection.checkAndCreateIntersectionsWithAll();
            this.canvas.triggerUserStateChangeEvent(connection);
        } else {
            connection.reconnectManhattah();
            connection.setSegmentMoveHandlers();
            connection.checkAndCreateIntersectionsWithAll();
            this.canvas.triggerConnectionStateChangeEvent(connection);
        }


    }
};

PMLane.prototype.isConnetionIntoLane = function (shape) {
    var i,
        max,
        child;
    for (i = 0, max = this.children.getSize(); i < max; i += 1) {
        child = this.children.get(i);
        if (shape.getID() === child.getID()) {
            return true;
        }
    }
    return false;
};
/**
 * @inheritdoc
 */
PMLane.prototype.onMouseUp = function (customShape) {
    var that = this;
    return function (e, ui) {
        if (customShape.canvas.canCreateShape && customShape.canvas.connectStartShape.getID() !== customShape.getID()) {
            that.shapeJustCreated = true;
        }

        (PMShape.prototype.onMouseUp.call(that, customShape))(e, ui);
    };
};
/**
 * @inheritdoc
 */
PMLane.prototype.onClick = function (customShape) {
    var that = this;
    return function (e) {
        if (that.shapeJustCreated) {
            customShape.canvas.hideAllCoronas();
        } else {
            (PMShape.prototype.onClick.call(that, customShape))(e);
        }
        that.shapeJustCreated = false;
        if (this.helper) {
            $(this.helper.html).remove();
        }
        customShape.wasDragged = false;
        e.stopPropagation();
    };
};
