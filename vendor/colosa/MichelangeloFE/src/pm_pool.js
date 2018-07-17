var PMPool = function (options) {
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
    this.proType = '';
    /**
     *
     * @type {Boolean}
     */
    this.executable = false;
    /**
     *
     * @type {Boolean}
     */
    this.closed = false;

    /**
     *
     * @type {String}
     */
    this.parentLane = null;
    /**
     *
     * @type {Number}
     */
    this.relPosition = 0;
    /**
     *
     * @type {Boolean}
     */
    this.sizeIdentical = false;
    /**
     *
     * @type {Number}
     */
    this.participants = 0;
    this.graphic = null;
    this.headLineCoord = 40;
    this.orientation = 'HORIZONTAL';
    this.participantObject = null;
    this.hasMinimun = false;
    this.zoomPoolArray = [1, 5, 1, 2, 1];
    /**
     * Private property which indicates if a shape was just created inside the pool.
     * @type {boolean}
     */
    this.shapeJustCreated = false;
    this.bpmnLanes = new PMUI.util.ArrayList();

    PMPool.prototype.initObject.call(this, options);
};
PMPool.prototype = new PMShape();
PMPool.prototype.type = 'PMPool';

PMPool.prototype.poolContainerBehavior = null;
PMPool.prototype.poolResizeBehavior = null;

PMPool.prototype.getDataObject = function () {
    var name = this.getName();
    return {
        lns_uid: this.getID(),
        lns_name: name,
        bou_x: this.x,
        bou_y: this.y,
        bou_width: this.width,
        bou_height: this.height,
        bou_container: 'bpmnDiagram',
        _extended: this.getExtendedObject()
    };
};

/**
 * Object init method (internal)
 * @param {Object} options
 */
PMPool.prototype.initObject = function (options) {
    var defaultOptions = {
        lns_name: 'Pool',
        proUid: '',
        proType: '',
        executable: false,
        closed: false,
        parentLane: '',
        relPosition: 0,
        sizeIdentical: false,
        participants: 0,
        orientation: 'HORIZONTAL',
        resizing: false,
        parentLane: null,
        identicalSize: false
    };
    $.extend(true, defaultOptions, options);
    this.setName(defaultOptions.lns_name)
        .setProUid(defaultOptions.proUid)
        .setProType(defaultOptions.proType)
        .setExecutable(defaultOptions.executable)
        .setClosed(defaultOptions.closed)
        .setParentLane(defaultOptions.parentLane)
        .setRelPosition(defaultOptions.relPosition)
        .setSizeIdentical(defaultOptions.sizeIdentical)
        .setOrientation(defaultOptions.orientation)
        .setParentLane(defaultOptions.parentLane)
        .setIdenticalSize(defaultOptions.identicalSize);
};

/**
 * Creates the HTML representation of the layer
 * @returns {HTMLElement}
 */
PMPool.prototype.createHTML = function () {
    PMShape.prototype.createHTML.call(this);
    return this.html;
};

PMPool.prototype.decreaseZIndex = function () {
    this.fixZIndex(this, 3);
    return this;
};

PMPool.prototype.applyZoom = function () {
    PMShape.prototype.applyZoom.call(this);
    this.updateDimensions(10);
};

PMPool.prototype.onUpdateLabel = function () {
    var label = this.getLabels().get(0);
    label.text.style["text-overflow"] = "ellipsis";
    label.text.style["overflow"] = "hidden";
    label.text.style["white-space"] = "nowrap";
    label.text.style["margin-left"] = "2%";
    label.text.setAttribute("title", label.getMessage());
    return this;
};
/**
 * Paints the corresponding Pool, in this case adds the
 * corresponding css classes and margins
 * @chainable
 */
PMPool.prototype.paint = function () {
    var zoomFactor = this.canvas.zoomFactor,
        label = this.getLabels().get(0);
    if (typeof this.graphic === 'undefined' || this.graphic === null) {
        this.graphic = new JSGraphics(this.id);
    } else {
        this.graphic.clear();
    }
    this.graphic.setColor('#3b4753'); //change color
    this.graphic.setStroke(2);
    if (this.orientation === 'VERTICAL') {
        this.graphic.drawLine(0, this.headLineCoord * zoomFactor,
            this.zoomWidth, this.headLineCoord * zoomFactor);
        label.setOrientation('horizontal');
        label.setLabelPosition('top');
    } else {
        this.graphic.drawLine(this.headLineCoord * zoomFactor, 0,
            this.headLineCoord * zoomFactor, this.zoomHeight - 5);
    }
    this.onUpdateLabel();
    this.graphic.paint();
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
PMPool.prototype.containerBehaviorFactory = function (type) {
    if (type === 'pool') {
        if (!this.poolContainerBehavior) {
            this.poolContainerBehavior = new PoolContainerBehavior();
        }
        return this.poolContainerBehavior;
    } else {

        return PMShape.prototype.containerBehaviorFactory.call(this, type);
    }
};

/**
 * Handler for the onmousedown event, changes the draggable properties
 * according to the drag behavior that is being applied
 * @param {CustomShape} CustomShape
 * @returns {Function}
 * TODO Implement Mouse Down handler
 */
PMPool.prototype.onMouseDown = function (shape) {
    if (shape.getParentLane()) {
        return function (e, ui) {
            e.stopPropagation();
        };
    } else {
        return PMShape.prototype.onMouseDown.call(this, shape);
    }
};

PMPool.prototype.setResizeBehavior = function (behavior) {
    var factory = new PMUI.behavior.BehaviorFactory({
        products: {
            "regularresize": PMUI.behavior.RegularResizeBehavior,
            "Resize": PMUI.behavior.RegularResizeBehavior,
            "yes": PMUI.behavior.RegularResizeBehavior,
            "resize": PMUI.behavior.RegularResizeBehavior,
            "noresize": PMUI.behavior.NoResizeBehavior,
            "NoResize": PMUI.behavior.NoResizeBehavior,
            "no": PMUI.behavior.NoResizeBehavior,
            "poolResize": PMPoolResizeBehavior
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
 * Add a lane and refactor all lane positions and dimensions
 * @param {BPMNLane} newLane
 * @returns {PMPool}
 */
PMPool.prototype.addLane = function (newLane) {
    this.bpmnLanes.insert(newLane);
    newLane.setRelPosition(this.bpmnLanes.getSize());
    newLane.setParentPool(this.getID());
    return this;
};

/**
 * remove a lane and refactor all lane positions and dimensions
 * @param {BPMNLane} lane
 * @chainable
 */
PMPool.prototype.removeLane = function (lane) {
    this.bpmnLanes.remove(lane);
    return this;
};

/**
 * Get number of lanes into a pool
 * @return {Number}
 */
PMPool.prototype.getAllChildLanesNum = function (num) {
    var i, lane, childPool;
    for (i = 0; i < this.bpmnLanes.getSize(); i += 1) {
        lane = this.bpmnLanes.get(i);
        childPool = this.canvas.customShapes.find('id', lane.getChildPool());
        if (childPool) {
            num += childPool.getAllChildLanesNum(0);
        } else {
            num += 1;
        }
    }
    return num;
};

/**
 * Destroy a pool and update lane parentLane property if is a child pool
 * @chainable
 */
PMPool.prototype.destroy = function () {
    var parentLane = this.canvas.customShapes.find('id', this.getParentLane());
    if (parentLane) {
        this.parent.labels.get(0).setVisible(true);
        parentLane.childPool = null;

    }
    return this;
};

/**
 * Comparison function for ordering layers according to priority
 * @param {BPMNLane} lane1
 * @param {BPMNLane} lane2
 * @returns {boolean}
 */
PMPool.prototype.comparisonFunction = function (lane1, lane2) {
    return lane1.relPosition > lane2.relPosition;
};

/**
 * Refactor recursively all lane position an dimensions
 * because lanes has a particular behavior
 * @chainable
 */
PMPool.prototype.setLanePositionAndDimension = function (lane) {
    var numLanes,
        oldX,
        oldY,
        newx,
        newy,
        oldWidth,
        oldHeight,
        newWidth,
        newHeight,
        oldRelPosition,
        lane,
        i,
        label,
        numLanes2,
        newHeight2,
        newWidth2,
        childPool;

    numLanes = this.bpmnLanes.getSize();
    newx = (this.orientation === 'HORIZONTAL') ? this.headLineCoord : -1;
    newy = (this.orientation === 'HORIZONTAL') ? 0 : this.headLineCoord;
    //new lane width to update
    newWidth = (this.orientation === 'HORIZONTAL') ?
    this.getWidth() - this.headLineCoord - 2 : this.getWidth() / numLanes;

    //new lane height to update
    newHeight = (this.orientation === 'HORIZONTAL') ?
    (this.getHeight() - 1) / numLanes : this.getHeight() - this.headLineCoord;
    // FOR IDENTICAL OPTION
    numLanes2 = this.getAllChildLanesNum(0);
    newWidth2 = (this.orientation === 'HORIZONTAL') ?
    this.getWidth() - this.headLineCoord - 2 : this.getWidth() / numLanes2;
    newHeight2 = (this.orientation === 'HORIZONTAL') ?
    this.getHeight() / numLanes2 : this.getHeight() - this.headLineCoord;
    if (numLanes > 1) {
        lane.style.addProperties({
            'border-top': '2px solid #3b4753'
        });
        lane.setDimension(newWidth, lane.getHeight());
        lane.setPosition(newx, newy + this.getHeight());
        lane.setRelPosition(numLanes);
        //TODO: We need remove this method to implement Vertical direction POOL

        this.setDimension(this.getWidth(), this.getHeight() + lane.getHeight());
        this.label.updateDimension(false);
        this.paint();
    } else {
        lane.style.removeProperties(['border-top', 'border-left']);
        lane.setDimension(newWidth, this.getHeight());
        lane.setPosition(newx, newy);
        lane.setRelPosition(numLanes);
    }
    lane.label.updateDimension(false);
    this.label.updateDimension(false);
    lane.paint();
    lane.updateBounds(lane.businessObject.di);
    this.updateBounds(this.participantObject.di);
    return this;
};

/**
 * Updates all child lanes when a lane has been removed
 * @param lane
 * @returns {PMPool}
 */
PMPool.prototype.updateOnRemoveLane = function (lane) {
    var i, diffH = 0, nextLane, tempLane = lane, tempX = lane.getX(), tempY = lane.getY(), newY = 0;
    for (i = lane.getRelPosition() - 1; i < this.bpmnLanes.getSize(); i += 1) {
        nextLane = this.bpmnLanes.get(i);
        if (i === 0) {
            nextLane.style.removeProperties(['border-top', 'border-left']);
        }
        if (i > 0) {
            newY += this.bpmnLanes.get(i - 1).getHeight();
        }
        nextLane.setPosition(lane.getX(), newY);
        nextLane.setRelPosition(nextLane.getRelPosition() - 1);
        nextLane.paint();
    }
    if (this.bpmnLanes.getSize() > 0) {
        this.setDimension(this.getWidth(), this.getHeight() - lane.getHeight());
        this.paint();
    }

    return this;
};
/**
 * Updates all bpmn child lanes when resize event has been finished
 */
PMPool.prototype.updateBpmnOnResize = function () {
    var lane,
        i;
    this.updateBpmn();
    for (i = 0; i < this.bpmnLanes.getSize(); i += 1) {
        lane = this.bpmnLanes.get(i);
        lane.updateBpmn();
    }
};

PMPool.prototype.setMinimunsResize = function (top) {
    var $shape = $(this.getHTML()),
        lane,
        i,
        j,
        shape,
        limits,
        margin = 15,
        laneChildrens = new PMUI.util.ArrayList(),
        minH,
        hdiff,
        laneHeightMin = 0,
        minW = 200;
    if (!this.hasMinimun) {
        if (top) {
            lane = this.bpmnLanes.getFirst();
            minH = this.getHeight() - lane.getHeight() + (30 * this.canvas.getZoomFactor());
            for (i = this.bpmnLanes.getSize() - 1; i >= 0; i -= 1) {
                lane = this.bpmnLanes.get(i);
                for (j = 0; j < lane.getChildren().getSize(); j += 1) {
                    shape = lane.getChildren().get(j);
                    laneChildrens.insert(
                        {
                            x: shape.x,
                            y: shape.y + laneHeightMin,
                            width: shape.width,
                            height: shape.height
                        }
                    );
                }
                laneHeightMin += lane.height;
            }


        } else {
            lane = this.bpmnLanes.getLast();
            minH = lane.getY() + (30 * this.canvas.getZoomFactor());
            for (i = 0; i < this.bpmnLanes.getSize(); i += 1) {
                lane = this.bpmnLanes.get(i);
                for (j = 0; j < lane.getChildren().getSize(); j += 1) {
                    shape = lane.getChildren().get(j);
                    laneChildrens.insert(
                        {
                            x: shape.x,
                            y: shape.y + laneHeightMin,
                            width: shape.width,
                            height: shape.height
                        }
                    );
                }
                laneHeightMin += lane.height;
            }
        }
        if (laneChildrens.getSize() > 0) {
            laneChildrens.insert(
                {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: minH
                }
            );
            limits = laneChildrens.getDimensionLimit();
            minW = (limits[1] + margin + this.headLineCoord) * this.canvas.getZoomFactor();
            minH = (limits[2] + margin) * this.canvas.getZoomFactor();
        }
        $shape.resizable('option', 'minWidth', minW);
        $shape.resizable('option', 'minHeight', minH);
        this.hasMinimun = true;
    }
};

/**
 * Updates all child lanes  connections when resize event has been finished
 */
PMPool.prototype.updateConnectionsChildrens = function () {
    var i,
        j,
        shapeLane,
        shape = this;
    for (i = 0; i < shape.children.asArray().length; i++) {
        if (shape.children.asArray()[i].type == "PMLane") {
            shapeLane = shape.children.asArray()[i];
            for (j = 0; j < shapeLane.children.asArray().length; j++) {
                shapeLane.children.asArray()[i].refreshConnections(false, true);
            }
        } else {
            shape.children.asArray()[i].refreshConnections(false, true);
        }
    }
};
/**
 * Updates dimesions and positions to pool lane childs
 */

PMPool.prototype.updateAllLaneDimension = function(avoidWeight) {
    var newWidth = 0,
        lane,
        newY = 0,
        i,
        parentHeight = 0,
        laneOldY,
        containerWidth,
        delta;
    for (i = 0; i < this.bpmnLanes.getSize(); i += 1) {
        lane = this.bpmnLanes.get(i);
        if (lane.getWidth() > newWidth) {
            newWidth = lane.getWidth();
        }
        laneOldY = lane.y;
        lane.setDimension(newWidth, lane.getHeight());
        lane.setRelPosition(i + 1);
        if (i > 0) {
            newY += this.bpmnLanes.get(i - 1).getHeight();
            lane.setPosition(lane.getX(), newY);
            lane.style.addProperties({
                'border-top': '2px solid #3b4753'
            });
        } else {
            lane.style.removeProperties(['border-top', 'border-left']);

        }
        parentHeight += lane.getHeight();
        lane.paint();
        lane.updateBounds(lane.businessObject.di);
        //updating connections into a lane
        lane.canvas.refreshArray.clear();
        delta = {
            dx: 0,
            dy: lane.y - laneOldY
        };
        lane.fixConnectionsOnResize(lane.resizing, true);
        lane.laneRefreshConnections(delta);
    }
    newWidth = newWidth && !avoidWeight ? newWidth + this.headLineCoord + 2.1: this.getWidth();
    this.setDimension(newWidth, parentHeight);
    this.paint();
    this.updateBounds(this.participantObject.di);
};

/**
 * to multiple pool support
 * @returns {*}
 */
PMPool.prototype.getMasterPool = function () {
    if (this.parent.family !== 'Canvas') {
        return this.parent.parent.getMasterPool();
    } else {
        return this;
    }
};
/**
 * Set pool name
 * @param {String} name
 * @returns {PMPool}
 */
PMPool.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.lns_name = name;
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Set pool type
 * @param {String} name
 * @returns {PMPool}
 */
PMPool.prototype.setType = function (newType) {
    this.type = newType;
    return this;
};
/**
 * Set proUid attached to pool
 * @param {String} uid
 * @returns {PMPool}
 */
PMPool.prototype.setProUid = function (uid) {
    this.proUid = uid;
    return this;
};
/**
 * Set pool type
 * @param {String} proType
 * @returns {PMPool}
 */
PMPool.prototype.setProType = function (proType) {
    this.proType = proType;
    return this;
};
/**
 * Set executable mode to pool
 * @param {Boolean} executable
 * @returns {PMPool}
 */
PMPool.prototype.setExecutable = function (executable) {
    this.executable = executable;
    return this;
};
/**
 * Set closed mode to pool
 * @param {Boolean} closed
 * @returns {PMPool}
 */
PMPool.prototype.setClosed = function (closed) {
    this.closed = closed;
    return this;
};
/**
 * Set pool orientation
 * @param {String} orientation
 * @returns {PMPool}
 */
PMPool.prototype.setOrientation = function (orientation) {
    this.orientation = orientation;
    return this;
};
/**
 * Set pool resizing mode
 * @param {String} orientation
 * @returns {PMPool}
 */
PMPool.prototype.setResizing = function (resizing) {
    this.resizing = resizing;
    return this;
};
/**
 * Set parent Lane UID to pool
 * @param {String} parentLane
 * @returns {PMPool}
 */
PMPool.prototype.setParentLane = function (parentLane) {
    this.parentLane = parentLane;
    return this;
};
/**
 * Set pool rel position
 * @param {Number} relPosition
 * @returns {PMPool}
 */
PMPool.prototype.setRelPosition = function (relPosition) {
    this.relPosition = relPosition;
    return this;
};
/**
 * Set size identical mode if we want identical size of pool childs
 * @param {Number} relPosition
 * @returns {PMPool}
 */
PMPool.prototype.setSizeIdentical = function (sizeIdentical) {
    this.sizeIdentical = sizeIdentical;
    return this;
};
/**
 * Set number of participants into a pool
 * @param {Number} num
 * @returns {PMPool}
 */
PMPool.prototype.setParticipants = function (num) {
    this.participants = num;
    return this;
};

/**
 * Set identical Size to a pool
 * @param {Boolean} val
 * @returns {PMPool}
 */
PMPool.prototype.setIdenticalSize = function (val) {
    this.identicalSize = val;
    return this;
};
/**
 * Get the pool name
 * @returns {String}
 */
PMPool.prototype.getName = function () {
    return this.lns_name;
};
/**
 * Get the pool type
 * @returns {String}
 */
PMPool.prototype.getType = function () {
    return this.type;
};
/**
 * Get the process uid attached to pool
 * @returns {String}
 */
PMPool.prototype.getProUid = function () {
    return this.proUid;
};
/**
 * Get the process uid attached to pool
 * @returns {String}
 */
PMPool.getProcessType = function () {
    return this.proType;
};
/**
 * Get a boolean value if the pool is in executable mode
 * @returns {Boolean}
 */
PMPool.isExecutable = function () {
    return this.executable;
};
/**
 * Get a boolean value if the pool is closed
 * @returns {Boolean}
 */
PMPool.prototype.isClosed = function () {
    return this.closed;
};
/**
 * Get the pool orientation
 * @returns {Sring}
 */
PMPool.prototype.getOrientation = function () {
    return this.orientation;
};
/**
 * Get the pool resizing mode
 * @returns {String}
 */
PMPool.prototype.getResizing = function () {
    return this.resizing;
};
/**
 * Get the relative position of the pool
 * @returns {Number}
 */
PMPool.prototype.getRelPosition = function () {
    return this.relPosition;
};
/**
 * Get a boolean value if pool have identical size
 * @returns {Boolean}
 */
PMPool.prototype.isSizeIdentical = function () {
    return this.sizeIdentical;
};
/**
 * Get a parent lane
 * @returns {BPMNLane}
 */
PMPool.prototype.getParentLane = function () {
    return this.parentLane;
};
/**
 * Get a number of participants asociate to pool
 * @returns {Boolean}
 */
PMPool.prototype.getParticipants = function () {
    return this.participants;
};

/**
 * Get a identical size value
 * @returns {Boolean}
 */
PMPool.prototype.getIdenticalSize = function () {
    return this.identicalSize;
};
/**
 * Updates the dimensions and position of this shape (note: 'this' is a shape)
 * @param {Number} margin the margin for this element to consider towards the
 * shapes near its borders
 * @chainable
 */
PMPool.prototype.updateDimensions = function (margin) {
    // update its size (if an child grew out of the shape)
    // only if it's not the canvas
    if (this.family !== 'Canvas') {
        this.updateSize(margin);
        this.refreshConnections();
        this.resizeBehavior.updateResizeMinimums(this);
        this.updateDimensions.call(this.parent, margin);
    }
    return this;
};
/**
 * Updates the dimensions and position of this pool relative to lane childs (note: 'this' is a shape)
 * @chainable
 */
PMPool.prototype.updateDimensionsWithLanes = function (margin) {
    // update its size (if an child grew out of the shape)
    // only if it's not the canvas
    var lane,
        i,
        j,
        limits,
        minW,
        minH,
        childrens,
        margin = 15,
        shape,
        $shape = $(this.getHTML()),
        laneHeightMin = 0,
        laneHeightSum = 0,
        lastLane,
        laneChildrens = new PMUI.util.ArrayList();

    for (i = 0; i < this.bpmnLanes.getSize(); i += 1) {
        lane = this.bpmnLanes.get(i);
        childrens = lane.getChildren();
        for (j = 0; j < childrens.getSize(); j += 1) {
            shape = childrens.get(j);
            if (i < this.bpmnLanes.getSize() - 1) {
                laneHeightSum += shape.parent.height;
            } else {
                laneHeightSum = 0;
            }
            laneChildrens.insert(
                {
                    x: shape.x,
                    y: shape.y + laneHeightMin,
                    width: shape.width,
                    height: shape.height
                }
            );
        }
        laneHeightMin += lane.height;
    }
    if (laneChildrens.getSize() > 0) {
        limits = laneChildrens.getDimensionLimit();
        minW = (limits[1] + margin + this.headLineCoord) * this.canvas.getZoomFactor();
        minH = (limits[2] + margin) * this.canvas.getZoomFactor();
    } else {
        minW = 300 * this.canvas.getZoomFactor();
        lastLane = this.bpmnLanes.get(this.bpmnLanes.getSize() - 2);
        if (lastLane) {
            minH = lastLane.getY() + lastLane.getHeight() + (30 * this.canvas.getZoomFactor());
        }
    }
    $shape.resizable();
    $shape.resizable('option', 'minWidth', minW);
    $shape.resizable('option', 'minHeight', minH);
    return this;
};

PMPool.prototype.stringify = function () {
    var inheritedJSON = PMShape.prototype.stringify.call(this),
        thisJSON = {
            name: this.getName(),
            proUid: this.getProUid(),
            proType: this.proType,
            executable: this.executable,
            closed: this.isClosed(),
            parentLane: this.parentLane,
            relPosition: this.getRelPosition(),
            sizeIdentical: this.isSizeIdentical(),
            participants: this.getParticipants(),
            orientation: this.getOrientation(),
            parentLane: this.getParentLane()
        };
    $.extend(true, inheritedJSON, thisJSON);
    return inheritedJSON;
};
/**
 * Creates bussines model to export to standard bpmn file
 * @param type xml tag to export bpmn file
 */
PMPool.prototype.createBpmn = function (type) {
    var bpmnCollaboration;
    if (!this.parent.businessObject.di) {
        this.parent.createBPMNDiagram();
    }
    if (!(_.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"}))) {
        bpmnCollaboration = PMDesigner.moddle.create('bpmn:Collaboration', {id: 'pmui-' + PMUI.generateUniqueId()});
        PMDesigner.businessObject.get('rootElements').unshift(bpmnCollaboration);
        this.parent.businessObject.di.bpmnElement = bpmnCollaboration;
        bpmnCollaboration.participants = [];
    } else {
        bpmnCollaboration = _.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"});
    }
    if (!this.businessObject.elem) {
        this.createWithBpmn(type, 'participantObject');
        this.updateBounds(this.participantObject.di);
        this.updateSemanticParent(this.participantObject, {elem: bpmnCollaboration});
        this.updateDiParent(this.participantObject.di, this.parent.businessObject.di);
    }
};
/**
 * update participant parent .bpmn file
 * @param businessObject
 * @param newParent
 */
PMPool.prototype.updateSemanticParent = function (businessObject, newParent) {
    var children;
    if (businessObject.elem.$parent && businessObject.elem.$parent === newParent.elem) {
        return;
    }
    if (businessObject.elem.$parent) {
        // remove from old parent
        children = businessObject.elem.$parent.get('participants');
        CollectionRemove(children, businessObject.elem);
    }
    if (!newParent.elem) {
        businessObject.elem.$parent = null;
    } else {
        // add to new parent
        children = newParent.elem.get('participants');
        children.push(businessObject.elem);
        businessObject.elem.$parent = newParent.elem;
    }
};

PMPool.prototype.updateDiParent = function (di, parentDi) {
    PMShape.prototype.updateDiParent.call(this, di, parentDi);
};


/**
 * Overwrite the parent function to set the dimension
 * @param {Number} x
 * @param {Number} y
 * @return {*}
 */
PMPool.prototype.setDimension = function (x, y) {
    var factorArray = [10.5, 5, 1.1, 2, 1],  //factors used to fix browsers round problems
        zoomFactor = this.getCanvas().getZoomFactor(),
        i,
        lanesCount = this.bpmnLanes.getSize(),
        lane,
        containerWidth = x - this.headLineCoord - (factorArray[this.getCanvas().zoomPropertiesIndex] * zoomFactor);
    PMUI.draw.CustomShape.prototype.setDimension.call(this, x, y);
    if (this.label) {
        //validation for vertical labels case pool and lanes
        this.label.updateDimension(false);
    }

    for (i = 0; i < lanesCount; i += 1) {
        lane = this.bpmnLanes.get(i);
        lane.setDimension(containerWidth, lane.getHeight());
    }

    this.paint();
    return this;
};

/**
 * create busines object to moodle bpmn export
 */
PMPool.prototype.createBusinesObject = function () {
    var participant = _.findWhere(this.participantObject.elem.$parent.get('participants'),
            {id: this.participantObject.elem.id}),
        bpmnProcess = PMDesigner.moddle.create('bpmn:Process',
            {id: 'pmui-' + PMUI.generateUniqueId()});

    PMDesigner.businessObject.get('rootElements').push(bpmnProcess);
    this.businessObject.elem = bpmnProcess;
    this.businessObject.di = this.canvas.businessObject.di;
    participant.processRef = bpmnProcess;
};
/**
 * @inheritDoc
 */
PMPool.prototype.dropBehaviorFactory = function (type, selectors) {
    if (type === 'pmcontainer') {
        this.pmConnectionDropBehavior = this.pmConnectionDropBehavior || new PMPoolDropBehavior(selectors);
        return this.pmConnectionDropBehavior;
    }
    return PMShape.prototype.dropBehaviorFactory.apply(this, arguments);
};

PMPool.prototype.removeBpmn = function () {
    var coll, children, pros;

    if ((_.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"}))) {
        coll = _.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"});
        if (coll.participants.length === 1) {
            children = PMDesigner.businessObject.get('rootElements');
            CollectionRemove(children, coll);
            //PMDesigner.businessObject.get
            if (this.parent.businessObject.di) {
                this.parent.businessObject.di.bpmnElement = this.parent.businessObject;
            }
        }
    }
    if (this.businessObject.elem && (_.findWhere(PMDesigner.businessObject.get('rootElements'), {
            $type: "bpmn:Process",
            id: this.businessObject.elem.id
        }))) {
        pros = _.findWhere(PMDesigner.businessObject.get('rootElements'), {
            $type: "bpmn:Process",
            id: this.businessObject.elem.id
        });
        children = PMDesigner.businessObject.get('rootElements');
        CollectionRemove(children, pros);
        this.businessObject.elem = null;
    }
    this.updateSemanticParent(this.participantObject, {elem: null});
    this.updateDiParent(this.participantObject.di);
    if (this.businessObject.di
        && this.businessObject.di.planeElement
        && this.businessObject.di.planeElement.length === 0) {
        this.parent.removeBPMNDiagram();
    }
};

PMPool.prototype.updateBpmn = function () {
    this.updateBounds(this.participantObject.di);
};
/**
 * @inheritdoc
 */
PMPool.prototype.onMouseUp = function (customShape) {
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
PMPool.prototype.onClick = function (customShape) {
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