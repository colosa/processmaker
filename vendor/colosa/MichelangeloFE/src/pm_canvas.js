var PMCanvas = function (options) {
    PMUI.draw.Canvas.call(this, options);
    this.project = null;
    this.items = null;
    /**
     * Minimum distance to "snap" to a guide
     * @type {number}
     */
    this.MIN_DISTANCE = 4;
    /**
     * Array which contains a list of all coordinates  to snap
     * @type {number}
     */
    this.guides = [];

    this.attachedListeners = null;

    this.hasClickEvent = false;
    this.isDragging = false;
    this.isGridLine = true;
    this.dragConnectHandlers = new PMUI.util.ArrayList();
    this.dropConnectHandlers = new PMUI.util.ArrayList();
    this.isDraggingConnectHandler = false;
    this.businessObject = {};
    this.isMouseOverHelper = false;
    this.canConnect = false;
    this.canCreateShape = false;
    this.canCreateShapeType = null;
    this.canCreateShapeClass = null;
    this.shapeHelper = null;
    this.coronaClick = false;
    this.connectStartShape = null;
    this.coronaShape = null;
    this.lassoEnabled = false;
    this.lassoLimits = null;
    PMCanvas.prototype.init.call(this, options);
};

PMCanvas.prototype = new PMUI.draw.Canvas();

PMCanvas.prototype.type = "PMCanvas";

this.canvasContainerBehavior = null;

PMCanvas.prototype.init = function (options) {
    var defaults = {
        project: null,
        snapToGuide: true,
        enabledMenu: false,
        hasClickEvent: false
    };
    jQuery.extend(true, defaults, options);
    this.setProject(defaults.project)
        .setEnabledMenu(defaults.enabledMenu)
        .setHasClickEvent(defaults.hasClickEvent)
        .setSnapToGuide(defaults.snapToGuide);

    this.items = new PMUI.util.ArrayList();
    this.attachedListeners = false;
};


PMCanvas.prototype.setHasClickEvent = function (value) {
    this.hasClickEvent = value;
    return this;
};
PMCanvas.prototype.setEnabledMenu = function (value) {
    this.enabledMenu = value;
    return this;
};
PMCanvas.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

PMCanvas.prototype.setProject = function (project) {
    if (project instanceof PMProject) {
        this.project = project;
    }
    return this;
};
PMCanvas.prototype.onCreateElementHandler = function (element) {
    var id,
        label,
        menuElement,
        shapeElement;
    if (this.project) {

        this.project.addElement(element);
        if (!this.project.loadingProcess) {
            if (element.relatedObject && (element.relatedObject.type === 'PMPool'
                || element.relatedObject.type === 'PMActivity')) {
                element.relatedObject.canvas.emptyCurrentSelection();
                element.relatedObject.canvas.addToSelection(element.relatedObject);
            }
        }
        if (element.type === "Connection") {
            return;
        }
    }
};

/**
 * Factory of canvas behaviors. It uses lazy instantiation to create
 * instances of the different container behaviors
 * @param {String} type An string that specifies the container behavior we want
 * an instance to have, it can be regular or nocontainer
 * @return {ContainerBehavior}
 */
PMCanvas.prototype.containerBehaviorFactory = function (type) {
    if (type === 'pmcanvas') {
        if (!this.canvasContainerBehavior) {
            this.canvasContainerBehavior = new CanvasContainerBehavior();
        }
        return this.canvasContainerBehavior;
    } else {
        return PMShape.prototype.containerBehaviorFactory.call(this, type);
    }
};

PMCanvas.prototype.dropBehaviorFactory = function (type, selectors) {
    if (type === 'canvasdrop') {
        if (!this.pmConnectionDropBehavior) {
            this.pmConnectionDropBehavior = new PMContainerDropBehavior(selectors);
        }
        return this.pmConnectionDropBehavior;
    } else {
        return PMUI.draw.CustomShape.prototype.dropBehaviorFactory.call(this, type, selectors);
    }
};

PMCanvas.prototype.triggerTextChangeEvent = function (element, oldText, newText) {
    var valid, reg, e, nText, mp, id;
    if (element.parent.getType() === 'PMActivity' && !this.validateName(element, newText, oldText)) {
        newText = oldText;
    }
    reg = /<[^\s]/g;
    nText = newText.trim();
    e = reg.test(nText);
    if (e) {
        nText = nText.replace(/</g, '< ');
    }

    this.updatedElement = [{
        id: element.parent.id,
        type: element.parent.type,
        parent: element.parent,
        fields: [{
            field: "name",
            oldVal: oldText,
            newVal: nText
        }]
    }];
    element.parent.setName(nText);
    element.updateDimension();
    element.parent.setBPPMName(nText);
    if (element.parent.atachedDiagram) {
        id = PMDesigner.canvasList.getID();
        $('#' + id + ' option[value=' + element.parent.atachedDiagram.getID() + ']')
            .text(nText);
    }

    jQuery(this.html).trigger("changeelement");
};

PMCanvas.prototype.triggerConnectionStateChangeEvent = function (connection) {
    var points = [],
        Point = PMUI.util.Point,
        point,
        i;
    for (i = 0; i < connection.points.length; i += 1) {
        point = connection.points[i];
        points.push(new Point(point.x / this.zoomFactor, point.y / this.zoomFactor));
    }
    this.updatedElement = [{
        id: connection.getID(),
        type: connection.type,
        fields: [
            {
                field: 'state',
                oldVal: connection.getOldPoints(),
                newVal: points
            }
        ],
        relatedObject: connection
    }];
    connection.algorithm = 'user';
    $(this.html).trigger('changeelement');
    this.hideDragConnectHandlers();
    return this;
};

PMCanvas.prototype.triggerUserStateChangeEvent = function (connection) {
    var points = [],
        Point = PMUI.util.Point,
        point,
        i;
    for (i = 0; i < connection.points.length; i += 1) {
        point = connection.points[i];
        points.push(new Point(point.x / this.zoomFactor, point.y / this.zoomFactor));
    }
    this.updatedElement = [{
        id: connection.getID(),
        type: connection.type,
        fields: [
            {
                field: 'state',
                oldVal: connection.getOldPoints(),
                newVal: points
            }
        ],
        relatedObject: connection
    }];
    connection.algorithm = 'user';
    return this;
};

PMCanvas.prototype.updateDimensionLabel = function (element) {
    var width,
        width = element.relatedObject.width;
    newWidth = Math.max(width, this.zoomWidth);
    element.relatedObject.label.setWidth(width);
    return this;
};
PMCanvas.prototype.onChangeElementHandler = function (element) {
    var textNode,
        currentElement;
    if (this.project && element.length > 0) {
        try {
            this.project.updateElement(element);

        } catch (e) {
            throw new Error("Error, There are problems updating the element".translate(), e);
        }
    }
};
PMCanvas.prototype.onRemoveElementHandler = function (elements) {
    var i,
        element,
        shapeElement;
    if (this.project) {
        this.project.removeElement(elements);
        try {
            for (i = 0; i < elements.length; i += 1) {
                if (elements[i].type === "Connection") {
                    element = elements[i];
                    element.updateIncomingAndOutgoingConnections("remove");
                    shapeElement = element.destPort.getParent();
                    if (shapeElement instanceof PMGateway) {
                        shapeElement.evaluateGatewayDirection();
                    }
                    shapeElement = element.srcPort.getParent();
                    if (shapeElement instanceof PMGateway) {
                        shapeElement.evaluateGatewayDirection();
                    }
                    PMDesigner.project.updateElement([]);
                    break;
                }
            }
        } catch (e) {
            throw new Error("Error, There are problems removing the element".translate(), e);
        }
    }
};
PMCanvas.prototype.onSelectElementHandler = function (element) {
    PMUI.removeCurrentMenu();
    if (element.length === 1) {
        switch (element[0].type) {
            case 'PMActivity':
            case 'PMEvent':
            case 'PMGateway':
                break;
        }
    }
    if (this.currentLabel != null) {
        this.hideAllFocusedLabels();
    }
    this.isSelected = true;
    return this;
};

PMCanvas.prototype.defineEvents = function () {
    return PMUI.draw.Canvas.prototype.defineEvents.call(this);
};
PMCanvas.prototype.getContextMenu = function () {
    return {};
};
PMCanvas.prototype.onRightClick = function () {
    var that = this;
    return function (a, b, c) {
    };
};
/**
 * Set guide Lines to canvas and create vertican and horizontal snappers
 * @param {Boolean} snap new value to verify if canvas has enabled snappes
 * @chainable
 */
PMCanvas.prototype.setSnapToGuide = function (snap) {
    this.snapToGuide = snap;
    // create snappers

    this.horizontalSnapper = new PMSnapper({
        orientation: 'horizontal',
        canvas: this,
        width: 4000,
        height: 1
    });


    this.verticalSnapper = new PMSnapper({
        orientation: 'vertical',
        canvas: this,
        width: 1,
        height: 4000
    });

    return this;
};
/**
 * Build the data of the snappers recreating the arrays,
 * this method is called from {@link RegularDragBehavior#onDragStart} (it might
 * be an overrided method `onDragStart` if the instance of {@link RegularDragBehavior} was changed).
 * @chainable
 */
PMCanvas.prototype.startSnappers = function (event) {
    var shape, i, parent;
    this.horizontalSnapper.getHTML();
    this.verticalSnapper.getHTML();
    this.guides = [];
    for (i = 0; i < this.customShapes.getSize(); i += 1) {
        shape = this.customShapes.get(i);
        if (!this.currentSelection.find('id', shape.getID())) {
            this.computeGuidesForElement(shape);
        }
    }
    return this;

};

PMCanvas.prototype.computeGuidesForElement = function (shape) {
    var x = shape.getHTML().offsetLeft, y = shape.getHTML().offsetTop,
        w, h;

    w = shape.getZoomWidth() - 1;
    h = shape.getZoomHeight() - 1;
    this.guides.push(
        {type: "h", x: x, y: y},
        {type: "h", x: x, y: y + h},
        {type: "v", x: x, y: y},
        {type: "v", x: x + w, y: y}
    );
    return this;
};

/**
 * Process the snappers according to this criteria and show and hide:
 *
 * - To show the vertical snapper
 *      - `shape.absoluteX` must equal a value in the data of `this.verticalSnapper`
 *      - `shape.absoluteX + shape.width` must equal a value in the data of `this.verticalSnapper`
 *
 * - To show the horizontal snapper
 *      - `shape.absoluteY` must equal a value in the data of `this.horizontalSnapper`
 *      - `shape.absoluteY + shape.height` must equal a value in the data of `this.horizontalSnapper`
 *
 * @param {Object} e
 * @parem {Object} ui
 * @param {Shape} customShape
 * @chainable
 */
PMCanvas.prototype.processGuides = function (e, ui, customShape) {
    // iterate all guides, remember the closest h and v guides
    var guideV,
        guideH,
        distV = this.MIN_DISTANCE + 1,
        distH = this.MIN_DISTANCE + 1,
        offsetV,
        offsetH,
        mouseRelX,
        mouseRelY,
        pos,
        w = customShape.getZoomWidth() - 1,
        h = customShape.getZoomHeight() - 1,
        d;

    mouseRelY = e.originalEvent.pageY - ui.offset.top;
    mouseRelX = e.originalEvent.pageX - ui.offset.left;
    pos = {
        top: e.originalEvent.pageY - customShape.canvas.getY() - mouseRelY
        + customShape.canvas.getTopScroll(),
        left: e.originalEvent.pageX - customShape.canvas.getX() - mouseRelX
        + customShape.canvas.getLeftScroll()
    };
    $.each(this.guides, function (i, guide) {
        if (guide.type === "h") {
            d = Math.abs(pos.top - guide.y);
            if (d < distH) {
                distH = d;
                guideH = guide;
                offsetH = 0;
            }
            d = Math.abs(pos.top - guide.y + h);
            if (d < distH) {
                distH = d;
                guideH = guide;
                offsetH = h;
            }
        }
        if (guide.type === "v") {
            d = Math.abs(pos.left - guide.x);
            if (d < distV) {
                distV = d;
                guideV = guide;
                offsetV = 0;
            }
            d = Math.abs(pos.left - guide.x + w);
            if (d < distV) {
                distV = d;
                guideV = guide;
                offsetV = w;
            }
        }
    });

    if (distH <= this.MIN_DISTANCE) {
        $("#guide-h").css("top", guideH.y - this.absoluteY).show();
        if (customShape.parent.family !== 'Canvas') {
            ui.position.top = guideH.y - offsetH - customShape.getParent().getAbsoluteY();
        } else {
            ui.position.top = guideH.y - offsetH;
        }
    } else {
        $("#guide-h").hide();
    }

    if (distV <= this.MIN_DISTANCE) {
        $("#guide-v").css("left", guideV.x - this.absoluteX).show();
        if (customShape.parent.family !== 'Canvas') {
            ui.position.left = guideV.x - offsetV - customShape.getParent().getAbsoluteX();
        } else {
            ui.position.left = guideV.x - offsetV;
        }

    } else {
        $("#guide-v").hide();
    }
    return this;
};

/**
 * Fires the {@link PMUI.draw.Canvas#event-changeelement} event, and elaborates the structure of the object that will
 * be passed to the handlers, the structure contains the following fields (considering old values and new values):
 *
 * - x
 * - y
 * - parent (the shape that is parent of this shape)
 * - state (of the connection)
 *
 * @param {PMUI.draw.Port} port The port updated
 * @chainable
 */
PMCanvas.prototype.triggerPortChangeEvent = function (port) {
    var direction = port.connection.srcPort.getID() === port.getID() ?
            "src" : "dest",
        map = {
            src: {
                x: "x1",
                y: "y1",
                parent: "element_origin",
                type: 'element_origin_type'
            },
            dest: {
                x: "x2",
                y: "y2",
                parent: "element_dest",
                type: 'element_dest_type'
            }
        },
        point,
        state,
        zomeedState = [],
        i;
    state = port.connection.getPoints();

    for (i = 0; i < state.length; i += 1) {
        point = port.connection.points[i];
        zomeedState.push(new PMUI.util.Point(point.x / this.zoomFactor, point.y / this.zoomFactor));
    }
    point = direction === "src" ? zomeedState[0] : zomeedState[state.length - 1];

    this.updatedElement = [{
        id: port.connection.getID(),
        type: port.connection.type,
        fields: [
            {
                field: map[direction].x,
                oldVal: point.x,        // there's no old value
                newVal: point.x
            },
            {
                field: map[direction].y,
                oldVal: point.y,        // there's no old value
                newVal: point.y
            },
            {
                field: map[direction].parent,
                oldVal: (port.getOldParent()) ? port.getOldParent().getID() : null,
                newVal: port.getParent().getID()
            },
            {
                field: map[direction].type,
                oldVal: port.connection.getNativeType(port.getParent()).type,
                newVal: port.connection.getNativeType(port.getParent()).type
            },
            {
                field: "state",
                oldVal: port.connection.getOldPoints(),
                newVal: zomeedState
            },
            {
                field: "condition",
                oldVal: "",
                newVal: port.connection.getFlowCondition()
            }
        ],
        relatedObject: port
    }];
    $(this.html).trigger('changeelement');
};

/**
 * Attaches event listeners to this canvas, it also creates some custom triggers
 * used to save the data (to send it to the database later).
 *
 * The events attached to this canvas are:
 *
 * - {@link PMUI.draw.Canvas#event-mousedown Mouse down event}
 * - {@link PMUI.draw.Canvas#event-mousemove Mouse move event}
 * - {@link PMUI.draw.Canvas#event-mouseup Mouse up event}
 * - {@link PMUI.draw.Canvas#event-click Click event}
 * - {@link PMUI.draw.Canvas#event-scroll Scroll event}
 *
 * The custom events are:
 *
 * - {@link PMUI.draw.Canvas#event-createelement Create element event}
 * - {@link PMUI.draw.Canvas#event-removeelement Remove element event}
 * - {@link PMUI.draw.Canvas#event-changeelement Change element event}
 * - {@link PMUI.draw.Canvas#event-selectelement Select element event}
 * - {@link PMUI.draw.Canvas#event-rightclick Right click event}
 *
 * This method also initializes jQueryUI's droppable plugin (instantiated as `this.dropBehavior`)
 * @chainable
 */
PMCanvas.prototype.attachListeners = function () {
    if (this.attachedListeners === false) {
        var $canvas = $(this.html).click(this.onClick(this)),
            $canvasContainer = $canvas.parent();
        $canvas.dblclick(this.onDblClick(this));
        $canvas.mousedown(this.onMouseDown(this));
        $canvasContainer.scroll(this.onScroll(this, $canvasContainer));
        if (!this.readOnly) {
            $canvas.mousemove(this.onMouseMove(this));
            $canvas.mouseup(this.onMouseUp(this));
            //$canvas.mouseup(this.onMouseLeave(this));
        }

        $canvas.on("createelement", this.onCreateElement(this));
        $canvas.on("removeelement", this.onRemoveElement(this));
        $canvas.on("changeelement", this.onChangeElement(this));
        $canvas.on("selectelement", this.onSelectElement(this));
        $canvas.on("rightclick", this.onRightClick(this));
        $canvas.on("contextmenu", function (e) {
            e.preventDefault();
        });
        this.updateBehaviors();
        this.attachedListeners = true;
    }
    return this;
};
/**
 * enpty current selection extended
 * @returns {PMCanvas}
 */
PMCanvas.prototype.emptyCurrentSelection = function () {
    PMUI.draw.Canvas.prototype.emptyCurrentSelection.call(this);
    this.hideAllCoronas();
    return this;
};
/**
 * mouse move custom behavior
 * @param canvas
 * @returns {Function}
 */
PMCanvas.prototype.onMouseMove = function (canvas) {
    return function (e, ui) {
        if (canvas.lassoEnabled && canvas.isMouseDown && !canvas.rightClick) {
            canvas.isMouseDownAndMove = true;
            var x = e.pageX - canvas.getX() + canvas.getLeftScroll() - canvas.getAbsoluteX(),
                y = e.pageY - canvas.getY() + canvas.getTopScroll() - canvas.getAbsoluteY(),
                topLeftX,
                topLeftY,
                bottomRightX,
                bottomRightY;
            topLeftX = Math.min(x, canvas.multipleSelectionHelper.oldX);
            topLeftY = Math.min(y, canvas.multipleSelectionHelper.oldY);
            bottomRightX = Math.max(x, canvas.multipleSelectionHelper.oldX);
            bottomRightY = Math.max(y, canvas.multipleSelectionHelper.oldY);
            canvas.multipleSelectionHelper.setPosition(
                topLeftX / canvas.zoomFactor,
                topLeftY / canvas.zoomFactor
            );
            canvas.multipleSelectionHelper.setDimension(
                (bottomRightX - topLeftX) / canvas.zoomFactor,
                (bottomRightY - topLeftY) / canvas.zoomFactor
            );

        } else if (canvas.canConnect) {
            canvas.connectHelper(e)
            canvas.connectStartShape.corona.hide();
            canvas.hideAllFocusedLabels();
        } else if (canvas.canCreateShape) {
            canvas.createShapeHelper(e);
        }

    };
};
/**
 * on mouse up behavior
 * @param canvas
 * @returns {Function}
 */
PMCanvas.prototype.onMouseUp = function (canvas) {
    return function (e, ui) {
        var realPoint,
            x,
            y;
        e.preventDefault();
        if (canvas.canCreateShape) {
            canvas.manualCreateShape(canvas, e);
            canvas.canCreateShape = false;
            return true;
        }
        if (canvas.isMouseDownAndMove) {
            realPoint = canvas.relativePoint(e);
            x = realPoint.x;
            y = realPoint.y;
            canvas.multipleSelectionHelper.setPosition(
                Math.min(x, canvas.multipleSelectionHelper.zoomX) / canvas.zoomFactor,
                Math.min(y, canvas.multipleSelectionHelper.zoomY) / canvas.zoomFactor
            );
            if (canvas.multipleSelectionHelper) {
                canvas.multipleSelectionHelper.wrapElements();
                canvas.IncreaseAllConnectionZIndex();
            }
        } else {

            if (!canvas.multipleSelectionHelper.wasDragged) {
                canvas.multipleSelectionHelper.reset().setVisible(false);
            }
            if (canvas.isMouseDown) {
                canvas.onClickHandler(canvas, x, y);
            }
        }
        canvas.isMouseDown = false;
        canvas.isMouseDownAndMove = false;
        canvas.rightClick = false;
        //hide lasso tool
        $('.mafe-toolbar-lasso').css('background-color', 'rgb(233, 233, 233)');
        canvas.lassoEnabled = false;
    };
};
/**
 * Increacess z.Index to all connections
 * @constructor
 */
PMCanvas.prototype.IncreaseAllConnectionZIndex = function () {
    var i,
        connection;
    for (i = 0; i < this.sharedConnections.getSize(); i += 1) {
        connection = this.sharedConnections.get(i);
        connection.increaseZIndex();
    }
};
PMCanvas.prototype.createShapeHelper = function (e) {
    var realPoint = this.relativePoint(e);
    if (this.shapeHelper) {
        //remove the connection segment in order to create another one
        $(this.shapeHelper.html).remove();
    }
    this.shapeHelper = new CreateShapeHelper({
        x: realPoint.x * this.zoomFactor - this.getX(),
        y: realPoint.y * this.zoomFactor - this.getY(),
        parent: this,
        zOrder: 999,
        className: this.canCreateShapeClass
    });
    this.shapeHelper.paint();
};

PMCanvas.prototype.connectHelper = function (e) {
    var endPoint = {},
        realPoint,
        diff;

    if (this.canConnect) {
        if (this.connectionSegment) {
            //remove the connection segment in order to create another one
            $(this.connectionSegment.getHTML()).remove();
        }
        //start point
        this.startConnectionPoint = {
            x: this.connectStartShape.getAbsoluteX() + this.connectStartShape.xMidPoints[1],
            y: this.connectStartShape.getAbsoluteY() + this.connectStartShape.yMidPoints[1]
        };

        //Determine the point where the mouse currently is
        realPoint = this.relativePoint(e);

        endPoint.x = realPoint.x * this.zoomFactor - this.getX();
        endPoint.y = realPoint.y * this.zoomFactor - this.getY();

        endPoint.x += (endPoint.x - this.startConnectionPoint.x > 0) ? -5 : 5;
        endPoint.y += (endPoint.y - this.startConnectionPoint.y > 0) ? -5 : 5;

        //creates a new segment from where the helper was created to the
        // currently mouse location
        this.connectionSegment = new PMUI.draw.Segment({
            startPoint: this.startConnectionPoint,
            endPoint: endPoint,
            parent: this,
            zOrder: 9
        });
        this.connectionSegment.paint();
    }
};

PMCanvas.prototype.connectProcedure = function (customShape, e) {
    var endPoint,
        tempPoint,
        initPoint,
        i,
        endPort,
        sourcePort,
        distance = 99999999,
        connection,
        endPoint2,
        validationResult;

    if (customShape.canvas.connectionSegment) {
        //remove the connection segment left
        $(customShape.canvas.connectionSegment.getHTML()).remove();

    }
    customShape.canvas.canConnect = false;
    $('body').css('cursor', 'default');

    validationResult = PMDesigner.connectValidator.isValid(customShape.canvas.connectStartShape, customShape);

    if (!validationResult.result) {
        //show invalid message
        PMDesigner.msgFlash(validationResult.msg, document.body, 'info', 3000, 5);
        return false;
    }

    sourcePort = new PMUI.draw.Port({
        width: 10,
        height: 10
    });
    endPort = new PMUI.draw.Port({
        width: 10,
        height: 10
    });
    endPoint = new PMUI.util.Point(
        e.pageX - customShape.canvas.getX() - customShape.getAbsoluteX() + customShape.canvas.getLeftScroll(),
        e.pageY - customShape.canvas.getY() - customShape.getAbsoluteY() + customShape.canvas.getTopScroll()
    );
    endPoint2 = new PMUI.util.Point(
        e.pageX - customShape.canvas.getX() + customShape.canvas.getLeftScroll(),
        e.pageY - customShape.canvas.getY() + customShape.canvas.getTopScroll()
    );

    for (i = 0; i < customShape.canvas.connectStartShape.xMidPoints.length; i += 1) {
        tempPoint = new PMUI.util.Point(
            customShape.canvas.connectStartShape.getAbsoluteX() + customShape.canvas.connectStartShape.xMidPoints[i],
            customShape.canvas.connectStartShape.getAbsoluteY() + customShape.canvas.connectStartShape.yMidPoints[i]
        );
        if (distance > tempPoint.getSquaredDistance(endPoint2)) {
            distance = tempPoint.getSquaredDistance(endPoint2);
            initPoint = new PMUI.util.Point(
                customShape.canvas.connectStartShape.xMidPoints[i],
                customShape.canvas.connectStartShape.yMidPoints[i]
            )
        }
    }

    customShape.canvas.connectStartShape.addPort(sourcePort, initPoint.x, initPoint.y);
    customShape.addPort(endPort, endPoint.x, endPoint.y, false, sourcePort);

    //add ports to the canvas array for regularShapes
    //create the connection
    connection = new PMFlow({
        srcPort: sourcePort,
        destPort: endPort,
        segmentColor: new PMUI.util.Color(0, 0, 0),
        name: " ",
        canvas: customShape.canvas,
        segmentStyle: customShape.connectionType.segmentStyle,
        flo_type: customShape.connectionType.type
    });

    connection.setSrcDecorator(new PMUI.draw.ConnectionDecorator({
        width: 11,
        height: 11,
        canvas: customShape.canvas,
        decoratorPrefix: (typeof customShape.connectionType.srcDecorator !== 'undefined'
        && customShape.connectionType.srcDecorator !== null) ?
            customShape.connectionType.srcDecorator : "mafe-decorator",
        decoratorType: "source",
        parent: connection
    }));

    connection.setDestDecorator(new PMUI.draw.ConnectionDecorator({
        width: 11,
        height: 11,
        canvas: customShape.canvas,
        decoratorPrefix: (typeof customShape.connectionType.destDecorator !== 'undefined'
        && customShape.connectionType.destDecorator !== null) ?
            customShape.connectionType.destDecorator : "mafe-decorator",
        decoratorType: "target",
        parent: connection
    }));
    connection.canvas.commandStack.add(new PMUI.command.CommandConnect(connection));

    //connect the two ports
    connection.connect();
    connection.setSegmentMoveHandlers();

    //add the connection to the canvas, that means insert its html to
    // the DOM and adding it to the connections array
    customShape.canvas.addConnection(connection);

    // Filling PMFlow fields
    connection.setTargetShape(endPort.parent);
    connection.setOriginShape(sourcePort.parent);

    // now that the connection was drawn try to create the intersections
    connection.checkAndCreateIntersectionsWithAll();

    //attaching port listeners
    sourcePort.attachListeners(sourcePort);
    endPort.attachListeners(endPort);

    // finally trigger createEvent
    customShape.canvas.triggerCreateEvent(connection, []);

};
/**
 * hides all corona shape
 */
PMCanvas.prototype.hideAllCoronas = function () {
    var i,
        shape;
    for (i = 0; i < this.currentSelection.getSize(); i += 1) {
        shape = this.currentSelection.get(i);
        if (shape.corona) {
            shape.corona.hide();
        }
    }
    return this;
};
/**
 * cancel connection action
 */
PMCanvas.prototype.cancelConnect = function () {
    if (this.connectionSegment) {
        $(this.connectionSegment.getHTML()).remove();
    }
    this.canConnect = false;
    $('body').css('cursor', 'default');
};
/**
 * doble click mouse behavior
 * @param canvas
 * @returns {Function}
 */
PMCanvas.prototype.onDblClick = function (canvas) {
    return function (e, ui) {
        var currentLabel = canvas.currentLabel, figure, realPoint, realPoint, oldConnection;
        e.stopPropagation();
        e.preventDefault();
        realPoint = canvas.relativePoint(e);
        realPoint.x = realPoint.x * canvas.zoomFactor - canvas.getX();
        realPoint.y = realPoint.y * canvas.zoomFactor - canvas.getY();
        figure = canvas.getBestConnecion(realPoint);
        if (figure !== null) {
            canvas.emptyCurrentSelection();
            figure.label.getFocus();

        }
    };
};

PMCanvas.prototype.hideAllFocusedLabels = function () {
    if (this.currentLabel != null)
        this.currentLabel.loseFocus();
    return true;
};

/**
 * @event mousedown
 * MouseDown Handler of the canvas. It does the following:
 *
 * - Trigger the {@link PMUI.draw.Canvas#event-rightclick Right Click event} if it detects a right click
 * - Empties `canvas.currentSelection`
 * - Hides `canvas.currentConnection` if there's one
 * - Resets the position of `canvas.multipleSelectionContainer` making it visible and setting its
 *      `[x, y]` to the point where the user did mouse down in the `canvas`.
 *
 * @param {PMUI.draw.Canvas} canvas
 */
PMCanvas.prototype.onMouseDown = function (canvas) {
    return function (e, ui) {
        var x = e.pageX - canvas.getX() + canvas.getLeftScroll() - canvas.getAbsoluteX(),
            y = e.pageY - canvas.getY() + canvas.getTopScroll() - canvas.getAbsoluteY();

        if (canvas.canConnect) {
            canvas.cancelConnect();
        }
        //hide corona
        if (canvas.coronaShape) {
            canvas.hideAllCoronas();
        }
        e.preventDefault();
        if (e.which === 3) {
            canvas.rightClick = true;
            $(canvas.html).trigger("rightclick", [e, canvas]);
        }
        canvas.isMouseDown = true;
        canvas.isMouseDownAndMove = false;
        // do not create the rectangle selection if a segment handler
        // is being dragged
        if (canvas.draggingASegmentHandler) {
            return;
        }
        // clear old selection
        canvas.emptyCurrentSelection();
        //verify lasso is enabled
        if (canvas.lassoEnabled) {
            // hide the currentConnection if there's one
            canvas.hideCurrentConnection();
            canvas.multipleSelectionHelper.reset();
            canvas.multipleSelectionHelper.setPosition(x / canvas.zoomFactor,
                y / canvas.zoomFactor);
            canvas.multipleSelectionHelper.oldX = x;
            canvas.multipleSelectionHelper.oldY = y;
            canvas.multipleSelectionHelper.setVisible(true);
            canvas.multipleSelectionHelper.changeOpacity(0.2);
        }
    };
};

PMCanvas.prototype.onClick = function (canvas) {
    return function (e, ui) {
        var currentLabel = canvas.currentLabel, figure, realPoint, realPoint, oldConnection;
        if (currentLabel) {
            currentLabel.loseFocus();
            $(currentLabel.textField).focusout();
        }
        realPoint = canvas.relativePoint(e);
        realPoint.x = realPoint.x * canvas.zoomFactor - canvas.getX();
        realPoint.y = realPoint.y * canvas.zoomFactor - canvas.getY();
        figure = canvas.getBestConnecion(realPoint);
        canvas.hideDropConnectHandlers();
        if (figure !== null && !canvas.isMouseDown) {
            oldConnection = canvas.currentConnection;
            canvas.emptyCurrentSelection();
            if (oldConnection) {
                oldConnection.hidePortsAndHandlers();
            }
            figure.showPortsAndHandlers();
            canvas.currentConnection = figure;
        }

    };
};

PMCanvas.prototype.onMouseLeave = function (canvas) {
    return function (e, ui) {
        if (parseInt(e.screenX + 10, 10) >= parseInt(document.body.clientWidth, 10)) {
            window.scrollBy(1, 0);
        }

        if (parseInt(e.screenY - 75, 10) >= parseInt(document.body.clientHeight, 10)) {
            window.scrollBy(0, 1);
        }
    };
};
PMCanvas.prototype.manualCreateShape = function (parent, e) {
    var customShape = this.shapeFactory(this.canCreateShapeType),
        command,
        endPoint = {},
        realPoint = this.relativePoint(e);

    endPoint.x = realPoint.x * this.zoomFactor - parent.getAbsoluteX();
    endPoint.y = realPoint.y * this.zoomFactor - parent.getAbsoluteY();
    endPoint.y -= this.getY();

    parent.addElement(customShape, endPoint.x, endPoint.y, false);
    this.updatedElement = customShape;
    customShape.canvas.emptyCurrentSelection();
    this.addToList(customShape);
    customShape.showOrHideResizeHandlers(false);
    if (customShape.getParent() instanceof PMLane) {
        command = new PMCommandCreateInLane(customShape);
    } else {
        command = new PMUI.command.CommandCreate(customShape);
    }
    this.commandStack.add(command);
    command.execute();

    this.addToSelection(customShape);
    customShape.corona.show();

    e.pageY += customShape.getZoomHeight() / 2;
    this.connectProcedure(customShape, e);
    this.canCreateShape = false;

    this.connectStartShape.corona.hide();
    if (this.shapeHelper) {
        //remove the connection segment in order to create another one
        $(this.shapeHelper.html).remove();
    }

    if (customShape.getType() === 'PMGateway'
        || customShape.getType() === 'PMEvent') {

        customShape.manualCreateMenu(e);
        customShape.canvas.hideAllCoronas();
    }

};

/**
 * Parses `options` creating shapes and connections and placing them in this canvas.
 * It does the following:
 *
 * - Creates each shape (in the same order as it is in the array `options.shapes`)
 * - Creates each    connection (in the same order as it is in the array `options.connections`)
 * - Creates the an instance of {@link PMUI.command.CommandPaste} (if possible)
 *
 * @param {Object} options
 * @param {Array} [options.shapes=[]] The config options of each shape to be placed in this canvas.
 * @param {Array} [options.connections=[]] The config options of each connection to be placed in this canvas.
 * @param {boolean} [options.uniqueID=false] If set to true, it'll assign a unique ID to each shape created.
 * @param {boolean} [options.selectAfterFinish=false] If set to true, it'll add the shapes that are
 * direct children of this canvas to `this.currentSelection` arrayList.
 * @param {string} [options.prependMessage=""] The message to be prepended to each shape's label.
 * @param {boolean}  [options.createCommand=true] If set to true it'll create a command for each creation
 * of a shape and connection (see {@link PMUI.command.CommandCreate},
 {@link PMUI.command.CommandConnect}) and save them in
 * a {@link PMUI.command.CommandPaste} (for undo-redo purposes).
 * @param {number} [options.diffX=0] The number of pixels on the x-coordinate to move the shape on creation
 * @param {number} [options.diffY=0] The number of pixels on the y-coordinate to move the shape on creation
 * @chainable
 */
PMCanvas.prototype.parse = function (options) {
    var defaults = {
            shapes: [],
            connections: [],
            uniqueID: false,
            selectAfterFinish: false,
            prependMessage: "",
            createCommand: true,
            diffX: 0,
            diffY: 0
        },
        i,
        j,
        id,
        oldID,
        shape,
        points,
        shapeOptions,
        connection,
        connectionOptions,
        sourcePort,
        sourcePortOptions,
        sourceShape,
        sourceBorder,
        destPort,
        destPortOptions,
        destShape,
        destBorder,
        command,
        diffX,
        diffY,
        stackCommandCreate = [],
        stackCommandConnect = [],
        canvasID = this.getID(),
        mapOldId = {},
        map = {};
    $.extend(true, defaults, options);
    // set the differentials (if the shapes are pasted in the canvas)
    diffX = defaults.diffX;
    diffY = defaults.diffY;
    // map the canvas
    map[canvasID] = this;
    mapOldId[canvasID] = canvasID;
    // empty the current selection and sharedConnections as a consequence
    // (so that the copy is selected after)
    if (defaults.selectAfterFinish) {
        this.emptyCurrentSelection();
    }
    for (i = 0; i < defaults.shapes.length; i += 1) {
        shapeOptions = {};
        $.extend(true, shapeOptions, defaults.shapes[i]);

        // set the canvas of <shape>
        shapeOptions.canvas = this;

        // create a map of the current id with a new id
        oldID = shapeOptions.id;

        // generate a unique id on user request
        if (defaults.uniqueID) {
            shapeOptions.id = PMUI.generateUniqueId();
        }
        mapOldId[oldID] = shapeOptions.id;

        // change labels' messages (using prependMessage)
        if (shapeOptions.labels) {
            for (j = 0; j < shapeOptions.labels.length; j += 1) {
                shapeOptions.labels[j].message = defaults.prependMessage +
                    shapeOptions.labels[j].message;
            }
        }

        // create an instance of the shape based on its type
        shape = this.shapeFactory(shapeOptions.extendedType, shapeOptions);

        // map the instance with its id
        map[shapeOptions.id] = shape;

        // if the shapes don't have a valid parent then set the parent
        // to be equal to the canvas
        // TODO: ADD shapeOptions.topLeftOnCreation TO EACH SHAPE
        if (!mapOldId[shapeOptions.parent]) {
            this.addElement(shape,
                shapeOptions.x + diffX, shapeOptions.y + diffY, true);
        } else if (shapeOptions.parent !== canvasID) {
            // get the parent of this shape
            map[mapOldId[shapeOptions.parent]].addElement(shape, shapeOptions.x,
                shapeOptions.y, true);
        } else {
            // move the shapes a little (so it can be seen that
            // they were duplicated)
            map[mapOldId[shapeOptions.parent]].addElement(shape,
                shapeOptions.x + diffX, shapeOptions.y + diffY, true);
        }

        // perform some extra actions defined for each shape
        shape.parseHook();
        shape.attachListeners();
        // execute command create but don't add it to the canvas.commandStack
        command = new PMUI.command.CommandCreate(shape);
        command.execute();
        stackCommandCreate.push(command);
    }
    for (i = 0; i < defaults.connections.length; i += 1) {
        connectionOptions = {};
        $.extend(true, connectionOptions, defaults.connections[i]);
        // state of the connection
        points = connectionOptions.state || [];
        // determine the shapes
        sourcePortOptions = connectionOptions.srcPort;
        sourceShape = map[mapOldId[sourcePortOptions.parent]];
        sourceBorder = sourceShape.getBorderConsideringLayers();

        destPortOptions = connectionOptions.destPort;
        destShape = map[mapOldId[destPortOptions.parent]];
        destBorder = destShape.getBorderConsideringLayers();

        // populate points if points has no info (backwards compatibility,
        // e.g. the flow state is null)
        if (points.length === 0) {
            points.push({
                x: sourcePortOptions.x + sourceShape.getAbsoluteX(),
                y: sourcePortOptions.y + sourceShape.getAbsoluteY()
            });
            points.push({
                x: destPortOptions.x + destShape.getAbsoluteX(),
                y: destPortOptions.y + destShape.getAbsoluteY()
            });
        }

        //create the ports
        sourcePort = new PMUI.draw.Port({
            width: 8,
            height: 8
        });
        destPort = new PMUI.draw.Port({
            width: 8,
            height: 8
        });
        // add the ports to the shapes
        // LOGIC: points is an array of points relative to the canvas.
        // CustomShape.addPort() requires that the point passed as an argument
        // is respect to the shape, so transform the point's coordinates (also
        // consider the border)
        sourceShape.addPort(
            sourcePort,
            points[0].x + diffX + sourceBorder -
            sourceShape.getAbsoluteX(),
            points[0].y + diffX + sourceBorder -
            sourceShape.getAbsoluteY()
        );
        destShape.addPort(
            destPort,
            points[points.length - 1].x + diffX + destBorder -
            destShape.getAbsoluteX(),
            points[points.length - 1].y + diffY + destBorder -
            destShape.getAbsoluteY(),
            false,
            sourcePort
        );

        connection = this.connectionFactory(
            connectionOptions.type,
            {
                srcPort: sourcePort,
                destPort: destPort,
                segmentColor: new PMUI.util.Color(92, 156, 204),
                canvas: this,
                segmentStyle: connectionOptions.segmentStyle
            }
        );
        connection.id = connectionOptions.id || PMUI.generateUniqueId();
        if (defaults.uniqueID) {
            connection.id = PMUI.generateUniqueId();
        }
        //set its decorators
        connection.setSrcDecorator(new PMUI.draw.ConnectionDecorator({
            width: 1,
            height: 1,
            canvas: this,
            decoratorPrefix: connectionOptions.srcDecoratorPrefix,
            decoratorType: "source",
            parent: connection
        }));
        connection.setDestDecorator(new PMUI.draw.ConnectionDecorator({
            width: 1,
            height: 1,
            canvas: this,
            decoratorPrefix: connectionOptions.destDecoratorPrefix,
            decoratorType: "target",
            parent: connection
        }));
        command = new PMUI.command.CommandConnect(connection);
        stackCommandConnect.push(command);
        // connect the two ports
        if (points.length >= 3) {
            connection.connect({
                algorithm: 'user',
                points: connectionOptions.state,
                dx: defaults.diffX,
                dy: defaults.diffY
            });
        } else {
            connection.connect();
        }
        connection.setSegmentMoveHandlers();
        // add the connection to the canvas, that means insert its html to
        // the DOM and adding it to the connections array
        this.addConnection(connection);
        // now that the connection was drawn try to create the intersections
        connection.checkAndCreateIntersectionsWithAll();
        //attaching port listeners
        sourcePort.attachListeners(sourcePort);
        destPort.attachListeners(destPort);

        this.triggerCreateEvent(connection, []);
    }

    // finally add to currentSelection each shape if possible (this method is
    // down here because of the zIndex problem with connections)
    if (defaults.selectAfterFinish) {
        for (id in map) {
            if (map.hasOwnProperty(id)) {
                if (map[id].family !== 'Canvas') {
                    this.addToSelection(map[id]);
                }
            }
        }
    }
    // create command if possible
    if (defaults.createCommand) {
        this.commandStack.add(new PMUI.command.CommandPaste(this, {
            stackCommandCreate: stackCommandCreate,
            stackCommandConnect: stackCommandConnect
        }));
    }
    return this;
};

/**
 * Fires the {@link PMUI.draw.Canvas#event-removeelement} event,
 and elaborates the structure of the object that will
 * be passed to the handlers.
 * @param {PMUI.draw.CustomShape} shape The shape created
 * @param {Array} relatedElements The array with the other elements created
 * @chainable
 */
PMCanvas.prototype.triggerRemoveEvent = function (shape, relatedElements) {
    if (relatedElements.length === 0) {
        if (shape) {
            relatedElements.push(shape);
        }
    }
    this.updatedElement = {
        id: (shape && shape.id) || null,
        type: (shape && shape.type) || null,
        relatedObject: shape,
        relatedElements: relatedElements
    };
    this.canvas.hideDragConnectHandlers();
    if (shape && shape.validatorMarker) {
        shape.validatorMarker.removeBoxMarker();
    }
    $(this.html).trigger('removeelement');
    return this;
};

PMCanvas.prototype.createConnectHandlers = function (resizableStyle, nonResizableStyle) {
    var i,
        number = 20,
        connectHandler;
    //add the rest to the mid list
    for (i = 0; i < number; i += 1) {
        connectHandler = new PMConnectHandler({
            parent: this,
            zOrder: PMUI.util.Style.MAX_ZINDEX + 4,
            representation: new PMUI.draw.Rectangle(),
            resizableStyle: resizableStyle,
            nonResizableStyle: nonResizableStyle
        });
        this.dragConnectHandlers.insert(
            connectHandler
        );
        if (!this.html) {
            return;
        }
        this.html.appendChild(connectHandler.getHTML());
        connectHandler.setPosition(100, 100);
        connectHandler.setCategory("dragConnectHandler");
        connectHandler.attachListeners();
        connectHandler.paint();
    }

    for (i = 0; i < number; i += 1) {
        connectHandler = new PMConnectHandler({
            parent: this,
            zOrder: PMUI.util.Style.MAX_ZINDEX + 1,
            representation: new PMUI.draw.Rectangle(),
            resizableStyle: resizableStyle,
            nonResizableStyle: nonResizableStyle
        });
        this.dropConnectHandlers.insert(
            connectHandler
        );
        if (!this.html) {
            return;
        }
        this.html.appendChild(connectHandler.getHTML());
        connectHandler.setPosition(400, 100);
        connectHandler.setCategory("dropConnectHandler");
        connectHandler.attachListeners();
        connectHandler.paint();
    }
    return this;
};

PMCanvas.prototype.hideDragConnectHandlers = function () {
    var connectHandler,
        i;
    for (i = 0; i < this.dragConnectHandlers.getSize(); i += 1) {
        connectHandler = this.dragConnectHandlers.get(i);
        connectHandler.setVisible(false);
    }

    return this;
};

PMCanvas.prototype.hideDropConnectHandlers = function () {
    var connectHandler,
        i;
    for (i = 0; i < this.dropConnectHandlers.getSize(); i += 1) {
        connectHandler = this.dropConnectHandlers.get(i);
        connectHandler.setVisible(false);
    }
    return this;
};

PMCanvas.prototype.applyZoom = function (scale) {
    this.hideDragConnectHandlers();
    this.hideDropConnectHandlers();
    PMUI.draw.Canvas.prototype.applyZoom.call(this, scale);
    return this;
};
PMCanvas.prototype.existThatName = function (element, name) {
    var i,
        shape,
        result = false;
    for (i = 0; i < this.customShapes.getSize(); i += 1) {
        shape = this.customShapes.get(i);
        if (shape.getID() !== element.getID() && shape.getName() === element.getName()) {
            result = true;
            break;
        }
    }
    return result;
};

PMCanvas.prototype.validateName = function (element, newText, oldText) {
    var result = true;
    if ((typeof newText === "string") && (newText.trim() === "")) {
        result = false;
        PMDesigner.msgFlash("Task/sub-process name can't be empty".translate(), document.body, 'error', 3000, 5);
    } else if (this.existThatName(element.parent, newText)) {
        result = false;
        PMDesigner.msgFlash('This name already exists.'.translate(), document.body, 'error', 3000, 5);
    }
    return result;
};

PMCanvas.prototype.addConnection = function (conn) {
    var shapeElement;
    PMUI.draw.Canvas.prototype.addConnection.call(this, conn);
    if (conn.flo_state) {
        conn.algorithm = 'user';
        conn.disconnect(true).connect({
            algorithm: 'user',
            points: conn.flo_state
        });
        conn.setSegmentMoveHandlers();
    }
    conn.updateIncomingAndOutgoingConnections("create");
    shapeElement = conn.destPort.getParent();
    if (shapeElement instanceof PMGateway) {
        shapeElement.evaluateGatewayDirection();
    }
    shapeElement = conn.srcPort.getParent();
    if (shapeElement instanceof PMGateway) {
        shapeElement.evaluateGatewayDirection();
    }
    PMDesigner.project.updateElement([]);
};
/**
 * This method hide all flows into a container (shape);
 * @param {BPMNShape} shape
 */
PMCanvas.prototype.hideFlowRecursively = function (shape) {
    var i,
        child,
        j,
        flow;
    for (i = 0; i < shape.getChildren().getSize(); i += 1) {
        child = shape.getChildren().get(i);
        for (j = 0; j < child.getPorts().getSize(); j += 1) {
            flow = child.getPorts().get(j).connection;
            flow.disconnect();
        }
        if (child.getChildren().getSize() > 0) {
            this.hideFlowRecursively(child);
        }
    }
};

/**
 * Remove all selected elements, it destroy the shapes and all references to them.
 * @chainable
 */
PMCanvas.prototype.removeElements = function () {
    // destroy the shapes (also destroy all the references to them)
    var shape,
        command;
    if (!this.canCreateShape && !this.isDragging) {
        command = new PMCommandDelete(this);
        this.commandStack.add(command);
        command.execute();
    }
    return this;
};

PMCanvas.prototype.triggerTaskTypeChangeEvent = function (element) {
    this.updatedElement = [{
        id: element.id,
        type: element.type,
        fields: [
            {
                field: "act_task_type",
                oldVal: '',
                newVal: this.act_task_type
            },
            {
                field: "act_task_type",
                oldVal: '',
                newVal: this.act_task_type
            }
        ],
        relatedObject: element
    }];
    $(this.html).trigger('changeelement');
    return this;
};

PMCanvas.prototype.buildDiagram = function (diagram) {
    var that = this;
    this.buildingDiagram = true;
    jQuery.each(diagram.laneset, function (index, val) {
        laneset = diagram.laneset[index];
        if (that.propertiesReview("laneset", laneset)) {
            that.loadShape('POOL', laneset, true);
        }
    });

    jQuery.each(diagram.lanes, function (index, val) {
        lanes = diagram.lanes[index];
        if (that.propertiesReview("lanes", lanes)) {
            that.loadShape('LANE', lanes, true);

        }
    });

    jQuery.each(diagram.activities, function (index, val) {
        activities = diagram.activities[index];
        if (that.propertiesReview("activities", activities)) {
            that.loadShape(activities.act_type, activities, true);
        }
    });
    jQuery.each(diagram.events, function (index, val) {
        events = diagram.events[index];
        if (that.propertiesReview("events", events)) {
            that.loadShape(events.evn_type, events, true);
        }
    });
    jQuery.each(diagram.gateways, function (index, val) {
        gateways = diagram.gateways[index];
        if (that.propertiesReview("gateways", gateways)) {
            that.loadShape(gateways.gat_type, gateways, true);
        }
    });
    jQuery.each(diagram.artifacts, function (index, val) {
        artifacts = diagram.artifacts[index];
        if (that.propertiesReview("artifacts", artifacts)) {
            that.loadShape(artifacts.art_type, artifacts, true);
        }
    });
    jQuery.each(diagram.data, function (index, val) {
        data = diagram.data[index];
        if (that.propertiesReview("data", data)) {
            that.loadShape(data.dat_type, data, true);
        }
    });
    jQuery.each(diagram.participants, function (index, val) {
        participants = diagram.participants[index];
        if (that.propertiesReview("participants", participants)) {
            that.loadShape('PARTICIPANT', participants, true);
        }
    });
    jQuery.each(diagram.flows, function (index, val) {
        connections = diagram.flows[index];
        if (that.propertiesReview("flows", connections)) {
            that.loadFlow(connections, true);
        }
    });

    this.buildingDiagram = false;
};
/**
 * Adds a start event as a defaul init canvas
 */
PMCanvas.prototype.setDefaultStartEvent = function () {
    var customShape = this.shapeFactory('START'),
        command;
    this.addElement(customShape, 100, 100, customShape.topLeftOnCreation);
    this.updatedElement = customShape;
    command = new PMUI.command.CommandCreate(customShape);
    this.commandStack.add(command);
    command.execute();

    this.addToSelection(customShape);
    customShape.corona.show();

};

PMCanvas.prototype.propertiesReview = function (type, currenShape) {
    var passed = true, shape, i;

    shape = {
        laneset: [],
        lanes: [],
        activities: [
            "act_uid",
            "act_name",
            "act_type"
        ],
        events: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ],
        gateways: [
            "gat_uid",
            "gat_name",
            "gat_type"
        ],
        flows: [
            "flo_uid",
            "flo_type",
            "flo_element_dest",
            "flo_element_origin",
            "flo_x1",
            "flo_x2",
            "flo_y1",
            "flo_y2"
        ],
        artifacts: [],
        startMessageEvent: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ],
        startTimerEvent: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ]

    };

    if (shape[type]) {
        for (i = 0; i < shape[type].length; i += 1) {
            if (currenShape[shape[type][i]]) {
                if (currenShape[shape[type][i]] === null && currenShape[shape[type][i]] === "") {
                    currenShape[shape[type][i]] = " ";
                }
            }
        }
    }
    return true;
};
/**
 * Loads the shape provided by the shape factory.
 * @param type
 * @param shape
 * @param fireTrigger
 * @param businessObject
 */

PMCanvas.prototype.loadShape = function (type, shape, fireTrigger, businessObject) {
    var customShape,
        command,
        transformShape,
        container;

    transformShape = this.setShapeValues(type, shape);
    customShape = this.shapeFactory(type, transformShape);

    if (customShape) {
        //to import .bpmn diagram
        if (businessObject) {
            customShape.businessObject = businessObject;
        }
        customShape.extendedType = type;
        if (shape.bou_container === 'bpmnDiagram') {
            this.addElement(customShape, parseInt(shape.bou_x, 10), parseInt(shape.bou_y, 10), true);
        } else {
            container = this.customShapes.find('id', shape.bou_element);
            container.addElement(customShape, parseInt(shape.bou_x, 10), parseInt(shape.bou_y, 10), true);
        }
        this.updatedElement = customShape;
        this.addToList(customShape);
        customShape.showOrHideResizeHandlers(false);
        if (fireTrigger) {
            this.triggerCreateEvent(customShape, []);
        }
    }
};

PMCanvas.prototype.setShapeValues = function (type, options) {
    var newShape;
    switch (type) {
        case "TASK":
        case "SUB_PROCESS":
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.id = options.act_uid;
            options.labels = [
                {
                    message: options.act_name
                }
            ];
            break;
        case "START":
        case "END":
        case "INTERMEDIATE":
        case "BOUNDARY":
            options.id = options.evn_uid;
            options.labels = [
                {
                    message: options.evn_name
                }
            ];
            break;

        case "TEXT_ANNOTATION":
        case "GROUP":
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.id = options.art_uid;
            options.labels = [
                {
                    message: options.art_name
                }
            ];
            break;
        case "COMPLEX":
        case "EXCLUSIVE":
        case "PARALLEL":
        case "INCLUSIVE":
        case "EVENTBASED":
            options.id = options.gat_uid;
            options.labels = [
                {
                    message: options.gat_name
                }
            ];
            break;
        case "DATAOBJECT":
        case "DATASTORE":
        case "DATAINPUT":
        case "DATAOUTPUT":
            options.id = options.dat_uid;
            options.labels = [
                {
                    message: options.dat_name
                }
            ];
            break;
        case "PARTICIPANT":
            options.id = options.par_uid;
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.par_name
                }
            ];
            break;
        case "POOL":
            options.id = options.lns_uid;
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.lns_name
                }
            ];
            break;
        case "LANE":
            options.id = options.lan_uid;
            options.relPosition = parseInt(options.bou_rel_position, 10);
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.lan_name
                }
            ];
            break;
    }
    return options;
};

PMCanvas.prototype.loadFlow = function (conn, trigger) {
    var sourceObj,
        targetObj,
        startPoint,
        endPoint,
        sourcePort,
        targetPort,
        connection,
        segmentMap = {
            'SEQUENCE': 'regular',
            'MESSAGE': 'segmented',
            'DATAASSOCIATION': 'dotted',
            'ASSOCIATION': 'dotted',
            'DEFAULT': 'regular',
            'CONDITIONAL': 'regular'
        },
        srcDecorator = {
            'SEQUENCE': 'mafe-decorator',
            'MESSAGE': 'mafe-message',
            'DATAASSOCIATION': 'mafe-association',
            'ASSOCIATION': 'mafe-decorator',
            'DEFAULT': 'mafe-default',
            'CONDITIONAL': 'mafe-decorator_conditional'
        },
        destDecorator = {
            'SEQUENCE': 'mafe-sequence',
            'MESSAGE': 'mafe-message',
            'DATAASSOCIATION': 'mafe-association',
            'ASSOCIATION': 'mafe-decorator_association',
            'DEFAULT': 'mafe-sequence',
            'CONDITIONAL': 'mafe-sequence'
        },
        positionSourceX,
        positionSourceY,
        positionTargetX,
        positionTargetY;

    sourceObj = this.getElementByUid(conn.flo_element_origin);
    targetObj = this.getElementByUid(conn.flo_element_dest);

    if (typeof sourceObj === "object" && typeof targetObj === "object") {
        startPoint = new PMUI.util.Point(conn.flo_x1, conn.flo_y1);
        endPoint = new PMUI.util.Point(conn.flo_x2, conn.flo_y2);

        sourcePort = new PMUI.draw.Port({
            width: 10,
            height: 10
        });

        targetPort = new PMUI.draw.Port({
            width: 10,
            height: 10
        });

        positionSourceX = startPoint.x - sourceObj.absoluteX + this.canvas.absoluteX;
        positionSourceY = startPoint.y - sourceObj.absoluteY + this.canvas.absoluteY;

        positionTargetX = endPoint.x - targetObj.absoluteX + this.canvas.absoluteX;
        positionTargetY = endPoint.y - targetObj.absoluteY + this.canvas.absoluteY;

        sourceObj.addPort(sourcePort, positionSourceX, positionSourceY);
        targetObj.addPort(targetPort, positionTargetX, positionTargetY, false, sourcePort);

        if (!conn.flo_name) {
            conn.flo_name = ' ';
        }
        connection = new PMFlow({
            id: conn.flo_uid,
            srcPort: sourcePort,
            destPort: targetPort,
            canvas: this.canvas,
            segmentStyle: segmentMap[conn.flo_type],
            segmentColor: new PMUI.util.Color(0, 0, 0),
            flo_type: conn.flo_type,
            name: conn.flo_name,
            flo_condition: conn.flo_condition,
            flo_state: conn.flo_state,
            flo_uid: (conn.flo_uid) ? conn.flo_uid : null
        });

        connection.setSrcDecorator(new PMUI.draw.ConnectionDecorator({
            decoratorPrefix: srcDecorator[conn.flo_type],
            decoratorType: "source",
            style: {
                cssClasses: []
            },
            width: 11,
            height: 11,
            canvas: this.canvas,
            parent: connection
        }));

        connection.setDestDecorator(new PMUI.draw.ConnectionDecorator({
            decoratorPrefix: destDecorator[conn.flo_type],
            decoratorType: "target",
            style: {
                cssClasses: []
            },
            width: 11,
            height: 11,
            canvas: this.canvas,
            parent: connection
        }));
        connection.setSegmentMoveHandlers();
        //add the connection to the canvas, that means insert its html to
        // the DOM and adding it to the connections array
        this.addConnection(connection);
        // Filling mafeFlow fields
        connection.setTargetShape(targetPort.parent);
        connection.setOriginShape(sourcePort.parent);
        // now that the connection was drawn try to create the intersections
        connection.checkAndCreateIntersectionsWithAll();
        //attaching port listeners
        sourcePort.attachListeners(sourcePort);
        targetPort.attachListeners(targetPort);
        this.updatedElement = connection;
        if (trigger) {
            this.triggerCreateEvent(connection, []);
        }
    } else {
        throw new Error("No elements found to connect.".translate());
    }
};

PMCanvas.prototype.getElementByUid = function (uid) {
    var element;
    element = this.items.find('id', uid);
    if (!element) {
        element = this.getCustomShapes().find('id', uid);
    }
    return element.relatedObject;
};

PMCanvas.prototype.createBPMNDiagram = function () {
    var bpmnDia = PMDesigner.moddle.create('bpmndi:BPMNDiagram', {id: 'dia_' + PMUI.generateUniqueId()});
    var bpmnPlane = PMDesigner.moddle.create('bpmndi:BPMNPlane', {
        'bpmnElement': this.businessObject.elem,
        id: 'plane_' + PMUI.generateUniqueId()
    });
    bpmnDia.plane = bpmnPlane;
    this.businessObject.diagram = bpmnDia;
    PMDesigner.businessObject.get('diagrams').push(bpmnDia);
    this.businessObject.di = bpmnPlane;
};

PMCanvas.prototype.createBusinesObject = function (createProcess) {
    this.businessObject.elem = {};
    var bpmnProcess = PMDesigner.moddle.create('bpmn:Process', {id: 'pmui-' + PMUI.generateUniqueId()});
    PMDesigner.businessObject.get('rootElements').push(bpmnProcess);
    this.businessObject.elem = bpmnProcess;
    if (this.businessObject.di
        && (typeof this.businessObject.di.bpmnElement === 'undefined'
        || !this.businessObject.di.bpmnElement)) {
        this.businessObject.di.bpmnElement = this.businessObject.elem;
    }
};

PMCanvas.prototype.updateCanvasProcess = function () {
    var process,
        children;

    if (this.businessObject.elem && (_.findWhere(PMDesigner.businessObject.get('rootElements'), {
            $type: "bpmn:Process",
            id: this.businessObject.elem.id
        }))) {
        process = _.findWhere(PMDesigner.businessObject.get('rootElements'), {
            $type: "bpmn:Process",
            id: this.businessObject.elem.id
        });
        if (process.flowElements.length === 1) {
            children = PMDesigner.businessObject.get('rootElements');
            CollectionRemove(children, process);
            this.businessObject.elem = null;
        }
        if (this.businessObject.di && this.businessObject.di.planeElement.length <= 1) {
            this.removeBPMNDiagram();
        }
    }
};
PMCanvas.prototype.removeBPMNDiagram = function () {
    var dia,
        children;

    dia = _.findWhere(PMDesigner.businessObject.get('diagrams'), {
        $type: "bpmndi:BPMNDiagram",
        id: this.businessObject.diagram.id
    });
    children = PMDesigner.businessObject.get('diagrams');

    CollectionRemove(children, dia);
    this.businessObject.di = null;
};
PMCanvas.prototype.toogleGridLine = function () {
    if (this.isGridLine === true) {
        this.disableGridLine();
    } else {
        this.enableGridLine();
    }
    //force to update the designer
    PMDesigner.project.updateElement([]);
    return this.isGridLine;
};
/**
 * Disable grid lines, removing the class pmui-pmcanvas
 * @returns {PMCanvas}
 */
PMCanvas.prototype.disableGridLine = function () {
    this.html.classList.remove("pmui-pmcanvas");
    this.isGridLine = false;

    return this;
};

/**
 * Enable grid lines, adding the class pmui-pmcanvas
 * @returns {PMCanvas}
 */
PMCanvas.prototype.enableGridLine = function () {
    this.html.classList.add("pmui-pmcanvas");
    this.isGridLine = true;
    return this;
};


/**
 * Return GridLine boolean property
 * @returns {PMCanvas}
 */
PMCanvas.prototype.getGridLine = function () {
    return this.isGridLine;
};

/**
 * Override method "fixSnapData" of PUI.draw.Canvas
 */
PMCanvas.prototype.fixSnapData = function () {
    //TODO complete fixSnapData function
};
/**
 * Sets and calculate the limits to work with the lasso tool.
 * calculate this.lassoLimits to validate if the selected shapes can be dragged surpasses the limits of the canvas.
 * @returns {PMCanvas}
 */
PMCanvas.prototype.setLassoLimits = function () {
    var minXObj = {
            "limit": 99999,
            "shape": null
        },
        minYObj = {
            "limit": 99999,
            "shape": null
        },
        shape,
        i;
    for (i = 0; i < this.currentSelection.getSize(); i += 1) {
        shape = this.currentSelection.get(i);
        if (shape.getX() < minXObj.limit) {
            minXObj = {
                "limit": shape.getX(),
                "shape": shape
            }
        }
        if (shape.getY() < minYObj.limit) {
            minYObj = {
                "limit": shape.getY(),
                "shape": shape
            }
        }
    }
    this.lassoLimits = {
        "x": minXObj,
        "y": minYObj
    };
    return this;
};
