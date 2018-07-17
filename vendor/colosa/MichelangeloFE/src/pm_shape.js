var PMShape = function (options) {
    PMUI.draw.CustomShape.call(this, options);
    this.extended = null;
    this.extendedType = null;
    this.relationship = null;

    this.midPointArray = [];
    this.htmlPorts = null;
    this.hasConnectHandlers = false;
    /**
     * Stores the label object used to show into the canvas
     * @type {Object}
     * @private
     */
    this.label = this.labels.get(0);
    this.diffXMidPoint = [-1, -4, -1, 4];
    this.diffYMidPoint = [4, -1, -4, -1];
    this.focusLabel = false;
    /**
     * Array of markers added to this activity
     * @type {Array}
     */
    this.markersArray = new PMUI.util.ArrayList();
    this.validatorMarker = null;
    this.errors = new PMUI.util.ArrayList();
    this.businessObject = {};
    /**
     * Connections going out from this element.
     * @type {Array}
     */
    this.outgoingConnections = null;
    /**
     * Connections going to this element.
     * @type {Array}
     */
    this.incomingConnections = null;
    PMShape.prototype.init.call(this, options);
};

PMShape.prototype = new PMUI.draw.CustomShape();

PMShape.prototype.type = 'PMShape';
PMShape.prototype.pmConnectionDropBehavior = null;
PMShape.prototype.pmContainerDropBehavior = null;
PMShape.prototype.supportedArray = [];
PMShape.prototype.init = function (options) {
    var defaults = {
        extended: {},
        relationship: {},
        focusLabel: false
    };
    if (options) {
        jQuery.extend(true, defaults, options);

        this.outgoingConnections = new PMUI.util.ArrayList();
        this.incomingConnections = new PMUI.util.ArrayList();

        this.setExtended(defaults.extended)
            .setExtendedType(defaults.extendedType)
            .setRelationship(defaults.relationship)
            .setIncomingConnections(defaults.incomingConnections)
            .setOutgoingConnections(defaults.outgoingConnections);
        if (defaults.markers) {
            this.addMarkers(defaults.markers, this);
        }
        if (defaults.validatorMarker) {
            this.addValidatorMarkers(defaults.validatorMarker, this);
        }
        if (defaults.corona) {
            this.setCorona(defaults.corona, this.getEspecificType(defaults));
        }
        this.focusLabel = defaults.focusLabel;
    }
};
/**
 * Sets the label element
 * @param {String} value
 * @return {*}
 */
PMShape.prototype.setName = function (value) {
    if (this.label) {
        this.label.setMessage(value);
    }
    return this;
};

/**
 * Returns the label text
 * @return {String}
 */
PMShape.prototype.getName = function () {
    var text = "";
    if (this.label) {
        text = this.label.getMessage();
    }
    return text;
};
PMShape.prototype.setExtendedType = function (type) {
    this.extendedType = type;
    return this;
};
PMShape.prototype.getDataObject = function () {
    return {};
};
PMShape.prototype.setRelationship = function (relationship) {
    this.relationship = relationship;
    return this;
};
PMShape.prototype.addRelationship = function (object) {
    if (typeof object === "object") {
        jQuery.extend(true, this.relationship, object);
    }
    return this;
};
PMShape.prototype.setExtended = function (extended) {
    var ext;
    ext = (typeof extended === 'object') ? extended : {};
    this.extended = ext;
    return this;
};
PMShape.prototype.getExtendedObject = function () {
    this.extended = {
        extendedType: this.extendedType
    };
    return this.extended;
};
PMShape.prototype.getMarkers = function () {
    return this.markersArray;
};
/**
 * Factory method for drop behaviors.
 * @param {String} type
 * @param {Array} selectors An array in which each element is a valid JQuery selector to specify the accepted elements by
 * the drop operation.
 * @returns {*}
 */
PMShape.prototype.dropBehaviorFactory = function (type, selectors) {
    if (type === 'pmconnection') {
        if (!this.pmConnectionDropBehavior) {
            this.pmConnectionDropBehavior = new PMConnectionDropBehavior(selectors);
        }
        return this.pmConnectionDropBehavior;
    } else if (type === 'pmcontainer') {
        if (!this.pmContainerDropBehavior) {
            this.pmContainerDropBehavior = new PMContainerDropBehavior(selectors);
        }
        return this.pmContainerDropBehavior;
    } else {
        return PMUI.draw.CustomShape.prototype.dropBehaviorFactory.call(this, type, selectors);
    }
};

PMShape.prototype.setDragBehavior = function (obj) {
    var factory = new PMUI.behavior.BehaviorFactory({
        products: {
            "pmsegment": PMSegmentDragBehavior,
            "customshapedrag": PMCustomShapeDragBehavior,
            "regulardrag": PMUI.behavior.RegularDragBehavior,
            "connectiondrag": PMUI.behavior.ConnectionDragBehavior,
            "connection": PMUI.behavior.ConnectionDragBehavior,
            "nodrag": PMUI.behavior.NoDragBehavior
        },
        defaultProduct: "nodrag"
    });
    this.drag = factory.make(obj);
    if (this.html && this.drag) {
        this.drag.attachDragBehavior(this);

    }
    if (this.canvas) {
        this.canvas.hideDragConnectHandlers();
    }
    return this;
};
/**
 * This function will attach all the listeners corresponding to the CustomShape
 * @chainable
 */
PMShape.prototype.attachListeners = function () {
    var that = this;
    if (this.html === null) {
        return this;
    }

    if (!this.canvas.readOnly) {
        var $customShape = $(this.html).click(this.onClick(this));
        $customShape.on("mousedown", this.onMouseDown(this));
        $customShape.mouseup(this.onMouseUp(this));
        $customShape.mouseover(this.onMouseOver(this));
        $customShape.mouseout(this.onMouseOut(this));
        $customShape.dblclick(this.onDblClick(this));
        $customShape.on("contextmenu", function (e) {
            e.preventDefault();
        });
        this.updateBehaviors();
    } else {
        if (this.canvas.hasClickEvent) {
            var $customShape = $(this.html).click(function (e) {
                if (that.hasClick) {
                    that.hasClick(e);
                }
            });
            this.updateBehaviors();
        }
    }
    return this;
};

PMShape.prototype.showConnectDropHelper = function (i, customShape) {
    var connectHandler, x, y;
    connectHandler = customShape.canvas.dropConnectHandlers.get(i);
    connectHandler.setDimension(18 * customShape.canvas.getZoomFactor(), 18 * customShape.canvas.getZoomFactor());
    x = customShape.getAbsoluteX() - customShape.canvas.getAbsoluteX() + customShape.xMidPoints[i] - connectHandler.width / 2 - 1;
    y = customShape.getAbsoluteY() - customShape.canvas.getAbsoluteY() + customShape.yMidPoints[i] - connectHandler.height / 2 - 1;
    if (customShape.parent.type !== 'PMCanvas') {
        x += 3;
        y += 2;
    }
    connectHandler.setPosition(x, y);
    connectHandler.relativeShape = customShape;
    connectHandler.attachDrop();
    connectHandler.setVisible(true);
    connectHandler.setZOrder(103);
};

/**
 * Handler for the onmousedown event, changes the draggable properties
 * according to the drag behavior that is being applied
 * @param {PMUI.draw.CustomShape} CustomShape
 * @returns {Function}
 */
PMShape.prototype.onMouseDown = function (customShape) {
    return function (e, ui) {
        var canvas = customShape.canvas;
        if (customShape.getType() === 'PMPool' || customShape.getType() === 'PMLane') {
            canvas.cancelConnect();
        }
        if (e.which === 3) {
            $(canvas.html).trigger("rightclick", [e, customShape]);
        } else {
            canvas.hideDragConnectHandlers();
            canvas.hideDropConnectHandlers();
            customShape.dragType = 2;
            if (customShape.dragType === customShape.DRAG) {
                if (!(customShape.getType() === 'PMEvent' && customShape.getEventType() === 'BOUNDARY')) {
                    customShape.setDragBehavior("customshapedrag");
                }
            } else if (customShape.dragType === customShape.CONNECT) {
            } else {
                customShape.setDragBehavior("nodrag");
            }
        }
        customShape.dragging = true;
        e.stopPropagation();
    };
};

/**
 * @event mouseup
 * Moused up callback fired when the user mouse ups on the `shape`
 * @param {PMUI.draw.Shape} shape
 */
PMShape.prototype.onMouseUp = function (customShape) {
    return function (e, ui) {
        e.preventDefault();
        if (customShape.canvas.canConnect
            && customShape.canvas.connectStartShape.getID() !== customShape.getID()
            && customShape.getType() !== 'PMPool'
            && customShape.getType() !== 'PMLane') {

            customShape.canvas.connectProcedure(customShape, e);
        }
        if (customShape.canvas.canCreateShape
            && customShape.canvas.connectStartShape.getID() !== customShape.getID()
            && (customShape.getType() === 'PMPool' || customShape.getType() === 'PMLane')) {
            customShape.canvas.manualCreateShape(customShape, e);
        }
    };
};
PMShape.prototype.showConnectDragHelpers = function (i, shape) {
    var y, x, connectHandler;
    connectHandler = shape.canvas.dragConnectHandlers.get(i);
    connectHandler.setDimension(15 * shape.canvas.getZoomFactor(), 15 * shape.canvas.getZoomFactor());
    x = shape.getAbsoluteX() - shape.canvas.getAbsoluteX() + shape.xMidPoints[i] - connectHandler.width / 2 - 1;
    y = shape.getAbsoluteY() - shape.canvas.getAbsoluteY() + 1 + shape.yMidPoints[i] - connectHandler.height / 2 - 1;
    if (shape.parent.type !== 'PMCanvas') {
        x += 3;
        y += 2;
    }
    connectHandler.setPosition(x, y);
    connectHandler.setVisible(true);
    connectHandler.relativeShape = shape;
    connectHandler.attachListeners();
};

PMShape.prototype.showAllConnectDragHelpers = function () {
    var shape = this, i, connectHandler;
    if (shape.canvas.isDragging || shape.canvas.currentLabel || shape.entered || shape.canvas.isResizing || PMUI.isCtrl) {
        shape.canvas.hideDragConnectHandlers();
        return;
    }
    if (!shape.canvas.isDraggingConnectHandler && !shape.dragging && !shape.canvas.currentSelection.find('id', shape.id) && !shape.canvas.currentConnection && !shape.canvas.isMouseDown) {
        if (shape.extendedType === "TEXT_ANNOTATION") {
            shape.canvas.hideDragConnectHandlers();
            shape.showConnectDragHelpers(3, shape);
            for (i = 0; i < shape.canvas.dragConnectHandlers.getSize(); i += 1) {
                connectHandler = shape.canvas.dragConnectHandlers.get(i);
                connectHandler.relativeShape = shape;
                shape.canvas.hightLightShape = shape;
                connectHandler.attachListeners();
            }
        } else {
            if (shape.extendedType !== "H_LABEL" && shape.extendedType !== "V_LABEL"
                && shape.extendedType !== "LANE" && shape.extendedType !== "POOL"
                && shape.extendedType !== "GROUP") {
                shape.canvas.hideDragConnectHandlers();
                shape.canvas.hightLightShape = shape;
                for (i = 0; i < 4; i += 1) {
                    shape.showConnectDragHelpers(i, shape);
                }
                shape.canvas.emptyCurrentSelection();
            }
        }
    }
};

PMShape.prototype.onClick = function (customShape) {
    return function (e) {
        var isCtrl = false,
            canvas = customShape.canvas,
            currentSelection = canvas.currentSelection,
            currentLabel = canvas.currentLabel;
        //hide all coronas
        customShape.canvas.hideAllCoronas();
        if (e.ctrlKey) {
            isCtrl = true;
        }
        // hide the current connection if there was one
        customShape.canvas.hideCurrentConnection();
        if (e.which === 3) {
            e.preventDefault();
            // trigger right click
            customShape.canvas.triggerRightClickEvent(customShape);
        } else {
            if (!customShape.wasDragged) {
                // if the custom shape was not dragged (this var is set to true
                // in custom_shape_drag_behavior >> onDragEnd)
                if (isCtrl) {
                    if (currentSelection.contains(customShape)) {
                        // remove from the current selection
                        canvas.removeFromSelection(customShape);
                    } else {
                        // add to the current selection
                        canvas.addToSelection(customShape);
                    }

                } else {
                    canvas.emptyCurrentSelection();
                    canvas.addToSelection(customShape);
                    if (!customShape.canvas.canConnect) {
                        if (customShape.canvas.currentLabel === null && customShape.corona) {
                            customShape.corona.show();
                        }
                    }
                    canvas.coronaShape = customShape;
                }
            }
            if (!currentSelection.isEmpty()) {
                canvas.triggerSelectEvent(currentSelection.asArray());
            }
        }
        if (this.helper) {
            $(this.helper.html).remove();
        }
        customShape.wasDragged = false;
        e.stopPropagation();
    };
};
PMShape.prototype.onDblClick = function (customShape) {
    return function (e) {
        customShape.canvas.hideAllFocusLabels();
        customShape.label.getFocus();
        e.preventDefault();
        e.stopPropagation();
    };
};
PMShape.prototype.onMouseOver = function (customShape) {
    return function (e, ui) {
        var layer,
            canConnect = true;
        if (customShape.canvas.canConnect
            && customShape.canvas.connectStartShape.getID() !== customShape.getID()
            && customShape.getType() !== 'PMPool'
            && customShape.getType() !== 'PMLane') {
            //validate if can connect green, else is red
            layer = customShape.getLayers().find('layerName', 'second-layer');
            if (layer) {
                layer.setVisible(true);
                if (!PMDesigner.connectValidator.isValid(customShape.canvas.connectStartShape, customShape).result) {
                    canConnect = false;
                }
                layer.removeCSSClasses(['mafe-can-not-connect-layer', 'mafe-can-connect-layer']);
                if (canConnect) {
                    layer.addCSSClasses(['mafe-can-connect-layer']);
                } else {
                    layer.addCSSClasses(['mafe-can-not-connect-layer']);
                }
            }
        }
        e.preventDefault();
        e.stopPropagation();
    };
};
PMShape.prototype.onMouseOut = function (customShape) {
    var that = this;
    return function (e, ui) {
        var layer;
        customShape.dragging = false;
        e.stopPropagation();
        layer = customShape.getLayers().find('layerName', 'second-layer');
        if (layer) {
            layer.setVisible(false);
        }
    };
};

/**
 * Overwrite the parent function to set the dimension
 * @param {Number} x
 * @param {Number} y
 * @return {*}
 */
PMShape.prototype.setDimension = function (x, y) {
    var factor = 1;
    PMUI.draw.CustomShape.prototype.setDimension.call(this, x, y);
    if (this.getType() === 'PMEvent' || this.getType() === 'PMGateway' || this.getType() === 'PMData') {
        factor = 3;
    }
    if (this.label) {
        //validation for vertical labels case pool and lanes
        if (this.getType() === 'PMPool' || this.getType() === 'PMLane') {
            this.label.updateDimension(false);
        } else {
            this.label.setDimension((this.zoomWidth * 0.9 * factor) / this.canvas.zoomFactor,
                this.label.height);
            this.label.setLabelPosition(this.label.location, this.label.diffX, this.label.diffY);
            this.label.updateDimension(false);
        }
    }
    if (this.getType() === 'PMPool') {
        this.paint();
    }
    return this;
};
/**
 * Creates a drag helper for drag and drop operations for the helper property
 * in jquery ui draggable
 * @returns {String} html
 */
PMShape.prototype.createDragHelper = function () {
    var html = document.createElement("div");
    html.style.width = 8 + "px";
    html.style.height = 8 + "px";
    html.style.borderRadius = "5px";
    html.style.marginTop = "-5px";
    html.style.marginLeft = "-5px";
    html.style.backgroundColor = "rgb(92, 156, 204)";
    html.style.zIndex = 2 * PMUI.draw.Shape.prototype.MAX_ZINDEX;
    html.id = "drag-helper";
    html.className = "drag-helper";
    return html;
};

PMShape.prototype.getContextMenu = function () {
    return {};
};

PMShape.prototype.getHTMLPorts = function () {
    return this.htmlPorts;
};

PMShape.prototype.updatePropertiesHTMLPorts = function () {
    var items = jQuery(this.htmlPorts).children(), k,
        point = this.midPointArray;
    for (k = 0; k < items.length; k += 1) {
        items[k].style.left = point[k].x + "px";
        items[k].style.top = point[k].y + "px";
    }

    return this;
};

/**
 * Adds markers to the arrayMarker property
 * @param {Array} markers
 * @param {Object} parent
 * @return {*}
 */
PMShape.prototype.addMarkers = function (markers, parent) {
    var newMarker, i, factoryMarker;
    if (jQuery.isArray(markers)) {
        for (i = 0; i < markers.length; i += 1) {
            factoryMarker = markers[i];
            factoryMarker.parent = parent;
            factoryMarker.canvas = parent.canvas;
            newMarker = new PMMarker(factoryMarker);
            this.markersArray.insert(newMarker);
        }
    }
    return this;
};
/**
 * Adds markers to the arrayMarker property
 * @param {Array} markers
 * @param {Object} parent
 * @return {*}
 */
PMShape.prototype.addValidatorMarkers = function (marker, parent) {
    marker.parent = parent;
    marker.canvas = parent.canvas;
    this.validatorMarker = new PMVAlidatorMarker(marker);
    return this;
};
/**
 * Adds a corona to shape
 * @param corona
 */
PMShape.prototype.setCorona = function (options, type) {
    var self = this,
        objectCrown,
        typeEspecific = "DEFAULT";

    if (this.getType() !== "PMActivity") {
        typeEspecific = type;
    }
    objectCrown = {
        parent: self,
        parentType: typeEspecific,
        canvas: self.canvas
    };
    $.extend(true, objectCrown, options);
    this.corona = new Corona(objectCrown);
    return this;
};

PMShape.prototype.updateLabelsPosition = function () {
    var i,
        label;
    for (i = 0; i < this.labels.getSize(); i += 1) {
        label = this.labels.get(i);
        label.setLabelPosition(label.location, label.diffX, label.diffY);
        label.paint();
    }
    return this;
};
/**
 * Paint the shape
 */
PMShape.prototype.paint = function () {
    var m, marker, size, width;
    PMUI.draw.CustomShape.prototype.paint.call(this);
    size = this.markersArray.getSize();
    for (m = 0; m < size; m += 1) {
        marker = this.markersArray.get(m);
        marker.paint();
    }
    if (this.validatorMarker) {
        this.validatorMarker.paint();
    }
    if (this.corona) {
        this.corona.paint();
        this.corona.hide();
    }
    if (this.getType() === 'PMActivity') {
        width = this.getWidth() - 20;
        this.label.textField.style.width = width + 'px';
    }
};
/**
 * Extends fixZIndex from shape class
 * @chainable
 */
PMShape.prototype.fixZIndex = function (shape, value) {
    var i,
        port;
    PMUI.draw.CustomShape.prototype.fixZIndex.call(this, shape, value);
    // force to z-order if container parent is the canvas
    for (i = 0; i < shape.ports.getSize(); i += 1) {
        port = shape.ports.get(i);
        if (port.connection.getSrcPort().getParent().getParent().getType() === 'PMCanvas' &&
            port.connection.getDestPort().getParent().getParent().getType() === 'PMCanvas') {
            port.connection.setZOrder(1);
        }
    }
};

/**
 *  Extend applyZoom of CustomShape for apply Zoom into Markers
 *  @return {*}
 */
PMShape.prototype.applyZoom = function () {
    var i, marker;
    PMUI.draw.CustomShape.prototype.applyZoom.call(this);
    for (i = 0; i < this.markersArray.getSize(); i += 1) {
        marker = this.markersArray.get(i);
        marker.applyZoom();
    }
    if (this.validatorMarker) {
        this.validatorMarker.applyZoom();
    }
    return this;
};
/**
 * Set flow as a default and update the other flows
 * @param {String} destID
 * @returns {AdamShape}
 */
PMShape.prototype.setDefaultFlow = function (floID) {
    var i,
        port,
        connection;
    for (i = 0; i < this.getPorts().getSize(); i += 1) {
        port = this.getPorts().get(i);
        connection = port.connection;
        this.updateDefaultFlow(0);
        if (connection.srcPort.parent.getID() === this.getID()) {
            if (connection.getID() === floID) {
                this.updateDefaultFlow(floID);
                connection.setFlowCondition("");
                connection.changeFlowType('default');
                connection.setFlowType("DEFAULT");
            } else if (connection.getFlowType() === 'DEFAULT') {
                connection.changeFlowType('sequence');
                connection.setFlowType("SEQUENCE");
            }
        }

    }
    return this;
};

PMShape.prototype.hideAllChilds = function () {
    var i,
        child,
        j,
        flow,
        arrayFlow = {};
    for (i = 0; i < this.getChildren().getSize(); i += 1) {
        child = this.getChildren().get(i);
        child.hideElement();
    }
    this.canvas.hideFlowRecursively(this);
};

PMShape.prototype.showAllChilds = function () {
    var i, child;
    for (i = 0; i < this.getChildren().getSize(); i += 1) {
        child = this.getChildren().get(i);
        child.showElement();
    }
};

PMShape.prototype.hideElement = function () {
    this.html.style.visibility = 'hidden';
    return this;
};

PMShape.prototype.showElement = function () {
    this.html.style.visibility = 'visible';
    return this;
};

PMShape.prototype.getBpmnElementType = function () {
    var map = {
        'TASK': 'bpmn:Task',
        'START': 'bpmn:StartEvent',
        'END': 'bpmn:EndEvent',
        'EXCLUSIVE': 'bpmn:ExclusiveGateway',
        'INCLUSIVE': 'bpmn:InclusiveGateway',
        'PARALLEL': 'bpmn:ParallelGateway',
        'COMPLEX': 'bpmn:ComplexGateway',
        'EVENTBASED': 'bpmn:EventBasedGateway',
        'SUB_PROCESS': 'bpmn:SubProcess',
        'INTERMEDIATE': 'bpmn:IntermediateCatchEvent',
        'BOUNDARY': 'bpmn:BoundaryEvent',
        'TEXT_ANNOTATION': 'bpmn:TextAnnotation',
        'GROUP': 'bpmn:Group',
        'PARTICIPANT': 'bpmn:Participant',
        'POOL': 'bpmn:Participant',
        'LANE': 'bpmn:Lane',
        'DATASTORE': 'bpmn:DataStore'
    };
    if (this.evn_type === 'INTERMEDIATE' && this.evn_behavior === 'THROW') {
        return 'bpmn:IntermediateThrowEvent';
    } else {
        return map[this.extendedType];
    }
};

PMShape.prototype.createWithBpmn = function (bpmnElementType, name) {
    var businessObject = {};
    businessObject.elem = PMDesigner.bpmnFactory.create(bpmnElementType, {id: 'el_' + this.id, name: this.getName()});
    if (!businessObject.di) {
        if (this.type === 'PMParticipant' || this.type === 'PMPool' || this.type === 'PMLane') {
            businessObject.di = PMDesigner.bpmnFactory.createDiShape(businessObject.elem, {}, {
                id: 'di_' + businessObject.elem.id,
                isHorizontal: true
            });
        } else {
            businessObject.di = PMDesigner.bpmnFactory.createDiShape(businessObject.elem, {}, {
                id: 'di_' + businessObject.elem.id
            });
        }
    }
    this[name] = businessObject;
};

PMShape.prototype.createHandlers = function (type, number, resizableStyle, nonResizableStyle) {
    var i;
    if (type === "Rectangle") {
        //First determine how many ResizeHandlers we are to create
        if (!number || (number !== 8 &&
            number !== 4 && number !== 0)) {
            number = 4;
        }
        //Then insert the corners first
        for (i = 0; i < number && i < 4; i += 1) {
            this.cornerResizeHandlers.insert(
                new PMUI.draw.ResizeHandler({
                    parent: this,
                    zOrder: PMUI.util.Style.MAX_ZINDEX + 23,
                    representation: new PMUI.draw.Rectangle(),
                    orientation: this.cornersIdentifiers[i],
                    resizableStyle: resizableStyle,
                    nonResizableStyle: nonResizableStyle
                })
            );
        }
        //subtract 4 just added resize points to the total
        number -= 4;
        //add the rest to the mid list
        for (i = 0; i < number; i += 1) {
            this.midResizeHandlers.insert(
                new PMUI.draw.ResizeHandler({
                    parent: this,
                    zOrder: PMUI.util.Style.MAX_ZINDEX + 23,
                    representation: new PMUI.draw.Rectangle(),
                    orientation: this.midPointIdentifiers[i],
                    resizableStyle: resizableStyle,
                    nonResizableStyle: nonResizableStyle
                })
            );
        }
    }
    return this;
};

/**
 * Recursive method to get correct parent busines object
 * @param root
 * @returns {PMPool}
 */
PMShape.prototype.getPoolParent = function (root) {
    while (root.getType() !== 'PMPool') {
        root = root.parent;
    }
    return root;
};
/**
 *
 * @param businessObject
 * @param parentBusinessObject
 */
PMShape.prototype.updateShapeParent = function (businessObject, parentBusinessObject) {
    var pool,
        parentDi,
        parentBusinessObjectAux = {};
    parentDi = parentBusinessObject && parentBusinessObject.di;
    //regular parent change
    if (parentBusinessObject.elem &&
        parentBusinessObject.elem.$type === 'bpmn:Lane') {
        //text annotation Data store Data object into lane
        if (!businessObject.elem.$lane && parentBusinessObject.elem) {
            businessObject.elem.$lane = parentBusinessObject.elem;
        }
        if (businessObject.elem.$type !== 'bpmn:TextAnnotation'
            && businessObject.elem.$type !== 'bpmn:DataStoreReference'
            && businessObject.elem.$type !== 'bpmn:DataObjectReference') {
            this.parent.updateLaneSetParent(businessObject, parentBusinessObject);
        }
        pool = this.getPoolParent(this.parent);
        parentBusinessObjectAux = pool.businessObject;
        parentDi = parentBusinessObjectAux && parentBusinessObjectAux.di;
        this.updateSemanticParent(businessObject, parentBusinessObjectAux);
    } else {
        this.updateSemanticParent(businessObject, parentBusinessObject);
    }
    this.updateDiParent(businessObject.di, parentDi);
};


PMShape.prototype.updateSemanticParent = function (businessObject, newParent) {
    var children;
    if (businessObject.elem.$parent === newParent.elem) {
        return;
    }
    if (businessObject.elem.$parent) {
        // remove from old parent
        children = businessObject.elem.$parent.get('flowElements');
        CollectionRemove(children, businessObject.elem);
    }

    if (!newParent.elem) {
        businessObject.elem.$parent = null;
    } else {
        // add to new parent
        children = newParent.elem.get('flowElements');
        children.push(businessObject.elem);
        businessObject.elem.$parent = newParent.elem;
    }
};

PMShape.prototype.updateDiParent = function (di, parentDi) {
    var planeElements;
    if (parentDi && !parentDi.$instanceOf('bpmndi:BPMNPlane')) {
        parentDi = parentDi.$parent;
    }
    if (di.$parent === parentDi) {
        return;
    }
    planeElements = (parentDi || di.$parent).get('planeElement');
    if (parentDi) {
        planeElements.push(di);
        di.$parent = parentDi;
    } else {
        CollectionRemove(planeElements, di);
        di.$parent = null;
    }
};

PMShape.prototype.updateBounds = function (di) {
    var bounds = this.type === 'label' ? this._getLabel(di).bounds : di.bounds,
        x = this.getX(), y = this.getY(),
        parent = this.parent;
    while (parent.type !== 'PMCanvas') {
        x = parent.getX() + x;
        y = parent.getY() + y;
        parent = parent.parent;
    }

    _.extend(bounds, {
        x: x,
        y: y,
        width: this.width,
        height: this.height
    });
};

PMShape.prototype._getLabel = function (di) {
    if (!di.label) {
        di.label = PMDesigner.bpmnFactory.createDiLabel();
    }
    return di.label;
};

PMShape.prototype.createBpmn = function (type) {
    if (!this.businessObject.elem && !(this instanceof PMUI.draw.MultipleSelectionContainer)) {
        this.createWithBpmn(type, 'businessObject');
    }
    this.updateBounds(this.businessObject.di);
    if (this.parent.getType() === 'PMCanvas' && !this.parent.businessObject.di) {
        this.canvas.createBPMNDiagram();
    }
    if (this.parent.businessObject.elem) {
        this.updateShapeParent(this.businessObject, this.parent.businessObject);
    } else {
        this.parent.createBusinesObject();
        this.updateShapeParent(this.businessObject, this.parent.businessObject);
    }
};

PMShape.prototype.removeBpmn = function () {
    var parentShape = this.parent;
    var businessObject = this.businessObject,
        parentBusinessObject = parentShape && parentShape.businessObject.elem,
        parentDi = parentBusinessObject && parentBusinessObject.di;

    if (this.parent.businessObject.elem
        && this.parent.businessObject.elem.$type === 'bpmn:Lane') {
        this.parent.updateLaneSetParent(businessObject, {elem: null});
    } else if (this.parent.type === 'PMCanvas') {
        this.parent.updateCanvasProcess();
    }
    this.updateSemanticParent(businessObject, {elem: null});
    this.updateDiParent(businessObject.di);
};

PMShape.prototype.updateBpmn = function () {
    this.updateBounds(this.businessObject.di);
    if (!this.parent.businessObject.elem) {
        this.parent.createBusinesObject();
    }
    this.updateShapeParent(this.businessObject, this.parent.businessObject);
};

PMShape.prototype.updateLaneSetParent = function (businessObject, newParent) {
    if (businessObject.elem.$lane) {
        children = businessObject.elem.$lane.get('flowNodeRef');
        CollectionRemove(children, businessObject.elem);
    }
    if (!newParent.elem) {
    } else {
        // add to new parent
        children = newParent.elem.get('flowNodeRef');
        children.push(businessObject.elem);
        businessObject.elem.$lane = newParent.elem;
    }
};

PMShape.prototype.setBPPMName = function (name) {
    if (this.businessObject && this.businessObject.elem) {
        this.businessObject.elem.name = name;
    }
    if (this.participantObject && this.participantObject.elem) {
        this.participantObject.elem.name = name;
    }
};

PMShape.prototype.isSupported = function () {
    return true;
};
/**
 * Get the number total of Errors and Warnings.
 * @returns {*}
 */
PMShape.prototype.getNumErrors = function () {
    var numErrors = 0;
    if (this.errors) {
        numErrors = this.errors.getSize();
    }
    return numErrors;
};
/**
 * Get Array of Errors.
 * @returns {*|Array}
 */
PMShape.prototype.getArrayErrors = function () {
    var arrayErrors = [];
    if (this.errors) {
        arrayErrors = this.errors.asArray();
    }
    return arrayErrors;
};
/**
 * Add new Error to Array of Errors.
 * @param error
 */
PMShape.prototype.addErrorLog = function (error) {
    error.name = this.getName();
    this.errors.insert(error);
    error.id = this.getID();
    PMDesigner.validTable.row.add([
            null,
            error.id,
            error.severity,
            error.name,
            error.type,
            error.description
        ]
    ).draw();
};
/**
 * Validate the Number of Errors of type Warning.
 * @param arrayErrors
 * @returns {Boolean}
 */
PMShape.prototype.validateWarning = function (arrayErrors) {
    var arrayLength,
        sw = false,
        i = 0;

    if (_.isArray(arrayErrors)) {
        sw = true;
        arrayLength = arrayErrors.length;
        while (arrayLength > 0) {
            if (arrayErrors[i].severity !== "Warning") {
                sw = false;
                arrayLength = 0;
            }
            arrayLength -= 1;
            i += 1;
        }
    }

    return sw;
};
/**
 * Set the severity Error (Error, Warning).
 * @param typeString
 */
PMShape.prototype.setTypeErrors = function (typeString) {
    var styleMarker;
    if (this.validatorMarker.styleMarker) {
        styleMarker = this.validatorMarker.styleMarker;
    }
    styleMarker.setTypeMarker(typeString);
};
/**
 * Validate connections between shapes.
 * @returns {boolean}
 * @constructor
 */
PMShape.prototype.ValidateConnections = function () {
    var ports = this.getPorts().asArray(),
        connection,
        segmentStyle,
        i,
        shapeSrc,
        shapeDest,
        isValid = true,
        maxPorts = ports.length;

    for (i = 0; i < maxPorts; i += 1) {
        connection = ports[i].getConnection();
        segmentStyle = connection.getSegmentStyle();
        if (connection && segmentStyle === "regular") {
            shapeSrc = connection.getSrcPort().getParent();
            shapeDest = connection.getDestPort().getParent();

            if (!this.ValidateContainer(shapeSrc.getParent(), shapeDest.getParent())) {
                isValid = false;
            }
        }
    }

    return isValid;
};
/**
 * Validate the container between two shapes.
 * @param shapeSrc
 * @param shapeDest
 * @returns {boolean}
 * @constructor
 */
PMShape.prototype.ValidateContainer = function (shapeSrc, shapeDest) {
    var equLevel = true;

    if (shapeSrc.getType() === shapeDest.getType()) {
        switch (shapeSrc.getType()) {
            case "PMPool":
                equLevel = (shapeSrc.id !== shapeDest.id) ? false : true;
                break;
            case "PMLane":
                equLevel = (this.getPoolParent(shapeSrc).id !== this.getPoolParent(shapeDest).id) ? false : true;
                break;
            default:
                equLevel = true;
        }
    } else {
        equLevel = false;
    }

    return equLevel;
};

PMShape.prototype.poolChildConnectionOnResize = function (resizing, root, delta, rootType) {
    var i,
        port,
        child,
        connection;
    if (root) {
        if (this.ports) {
            for (i = 0; i < this.ports.getSize(); i += 1) {
                port = this.ports.get(i);
                connection = port.connection;
                this.recalculatePortPosition(port);
                connection.disconnect().connect();
                if (!this.resizing) {
                    connection.setSegmentMoveHandlers();
                    connection.checkAndCreateIntersectionsWithAll();
                }
            }
        }
    } else {
        if (this.ports) {
            this.registerResizeConnection();
        }
    }
    // children
    for (i = 0; i < this.children.getSize(); i += 1) {
        child = this.children.get(i);
        child.setPosition(child.x, child.y);
        child.poolChildConnectionOnResize(child.resizing, false, delta, rootType);
    }
    return this;
};

PMShape.prototype.registerResizeConnection = function () {
    var i,
        port,
        connection;
    for (i = 0; i < this.ports.getSize(); i += 1) {
        port = this.ports.get(i);
        port.setPosition(port.x, port.y);
        connection = port.connection;
        if (!this.canvas.refreshArray.contains(connection)) {
            connection.canvas.refreshArray.insert(connection);
        }
    }
};

PMShape.prototype.fixConnectionsOnResize = function (resizing, root) {
    var i,
        port,
        child,
        connection,
        zoomFactor = this.canvas.zoomFactor;
    if (root) {
        if (this.ports) {
            // connections
            for (i = 0; i < this.ports.getSize(); i += 1) {
                port = this.ports.get(i);
                connection = port.connection;
                this.recalculatePortPosition(port);
                connection.disconnect().connect();
                if (!this.resizing) {
                    connection.setSegmentMoveHandlers();
                    connection.checkAndCreateIntersectionsWithAll();
                }
            }
        }
    } else {
        if (this.ports) {
            this.registerResizeConnection();
        }
    }
    // children
    for (i = 0; i < this.children.getSize(); i += 1) {
        child = this.children.get(i);
        child.setPosition(child.x, child.y);
        child.fixConnectionsOnResize(child.resizing, false);
    }
    return this;
};

PMShape.prototype.refreshChildrenPositions = function (onCommand, delta) {
    var i,
        children = this.children,
        child;
    for (i = 0; i < children.getSize(); i += 1) {
        child = children.get(i);
        child.setPosition(child.getX(), child.getY());
        if (onCommand) {
            child.refreshAllMovedConnections(false, delta);
        }
        child.refreshChildrenPositions(onCommand, delta);
    }
    return this;
};

PMShape.prototype.refreshAllPoolConnections = function (inContainer, delta, rootType) {
    var i,
        connection,
        max;
    for (i = 0, max = this.canvas.refreshArray.getSize(); i < max; i += 1) {
        connection = this.canvas.refreshArray.get(i);
        connection.reconectSwitcher(delta, inContainer, rootType);
    }
    return this;
};

PMShape.prototype.laneRefreshConnections = function (inContainer, delta, rootType) {
    var i,
        connection,
        max;
    for (i = 0, max = this.canvas.refreshArray.getSize(); i < max; i += 1) {
        connection = this.canvas.refreshArray.get(i);
        connection.reconectSwitcher(delta, inContainer, rootType);
    }
    return this;
};

PMShape.prototype.refreshAllMovedConnections = function (inContainer, delta, rootType) {
    var i,
        connection,
        ports = this.ports,
        port,
        max;

    for (i = 0, max = ports.getSize(); i < max; i += 1) {
        port = ports.get(i);
        port.setPosition(port.x, port.y);
        connection = port.connection;
        if (!this.canvas.connToRefresh.contains(connection)) {
            connection.canvas.connToRefresh.insert(connection);
        }
    }
    return this;
};

PMShape.prototype.refreshChildConnections = function (inContainer, delta, rootType) {
    var i,
        connection,
        max;
    for (i = 0, max = this.canvas.refreshArray.getSize(); i < max; i += 1) {
        connection = this.canvas.refreshArray.get(i);
        connection.reconectSwitcher(delta, inContainer, rootType);
    }
    return this;
};

PMShape.prototype.refreshShape = function () {
    PMUI.draw.Shape.prototype.refreshShape.call(this);
    this.updatePortsOnZoom();
    this.paint();
    return this;
};
/**
 * Sets the incoming connections for the element.
 * @param elements{Array}
 * @returns {PMShape}
 */
PMShape.prototype.setIncomingConnections = function(elements){
    var i;
    if (jQuery.isArray(elements)) {
        this.incomingConnections.clear();
        for (i = 0; i < elements.length; i += 1) {
            this.addIncomingConnection(elements[i]);
        }
    }
    return this;
};
/**
 * Add an incoming connection.
 * @param element{PMShape}
 * @returns {PMShape}
 */
PMShape.prototype.addIncomingConnection = function(element){
    this.incomingConnections.insert(element);
    return this;
};
/**
 * Remove an incoming connection.
 * @param element{PMShape}
 * @returns {PMShape}
 */
PMShape.prototype.removeIncomingConnection = function(element){
    this.incomingConnections.remove(element);
    return this;
};
/**
 * Return the list of incoming connections.
 * @param {...String} [types] Optional, returns only the connections of the specified types.
 * @returns {Array}
 */
PMShape.prototype.getIncomingConnections = function(){
    var validTypes = [],
        i;

    if (arguments.length) {
        for (i = 0; i < arguments.length; i += 1) {
            validTypes.push(arguments[i]);
        }

        return this.incomingConnections.asArray().filter(function (i) {
            return validTypes.indexOf(i.flo_type) >= 0;
        });
    }
    return this.incomingConnections.asArray().slice(0);
};
/**
 * Returns the list of the elements connected to this element's incoming connections.
 * @param {...String} [connection_types] The incoming elements whose connections are of this specified type, optional.
 * @returns {Array|*|{applyDefaultStyles, childOptions, initChildLayout, destroyChildLayout, resizeChildLayout, resizeNestedLayout, resizeWhileDragging, resizeContentWhileDragging, triggerEventsWhileDragging, maskIframesOnResize, useStateCookie, [cookie.autoLoad], [cookie.autoSave], [cookie.keys], [cookie.name], [cookie.domain], [cookie.path], [cookie.expires], [cookie.secure], noRoomToOpenTip, togglerTip_open, togglerTip_closed, resizerTip, sliderTip}}
 */
PMShape.prototype.getIncomingElements = function () {
    return this.getIncomingConnections.apply(this, arguments).map(function (i) {
        return i.getSrcPort().getParent();
    });
};
/**
 * Sets all the outgoing connections for the element.
 * @param elements{Array}
 * @returns {PMShape}
 */
PMShape.prototype.setOutgoingConnections = function(elements){
    var i;
    if (jQuery.isArray(elements)) {
        this.outgoingConnections.clear();
        for (i = 0; i < elements.length; i += 1) {
            this.addOutgoinConnections(elements[i]);
        }
    }
    return this;
};
/**
 * Add an outgoing connection to the element.
 * @param element{PMShape}
 * @returns {PMShape}
 */
PMShape.prototype.addOutgoingConnection = function(element){
    this.outgoingConnections.insert(element);
    return this;
};
/**
 * Remove an outgoing connection to the element.
 * @param element{PMShape}
 * @returns {PMShape}
 */
PMShape.prototype.removeOutgoingConnection = function(element){
    this.outgoingConnections.remove(element);
    return this;
};
/**
 * Return the list of outgoing connections.
 * @param {...String} [types] Optional, returns only the connections of the specified types.
 * @returns {Array}
 */
PMShape.prototype.getOutgoingConnections = function(){
    var validTypes = [],
        i;

    if (arguments.length) {
        for (i = 0; i < arguments.length; i += 1) {
            validTypes.push(arguments[i]);
        }

        return this.outgoingConnections.asArray().filter(function (i) {
            return validTypes.indexOf(i.flo_type) >= 0;
        });
    }

    return this.outgoingConnections.asArray().slice(0);
};
/**
 * Returns a list of the elements connected to the element's outgoing connections.
 * @param {...String} [type] Optional, returns only the connections of the specified type.
 * @returns {Array}
 */
PMShape.prototype.getOutgoingElements = function (type) {
    return this.getOutgoingConnections.apply(this, arguments).map(function (i) {
        return i.getDestPort().getParent();
    });
};
/**
 * Get Especific Type of the Shape or the object parameter
 * @param object
 * @returns {string}
 */
PMShape.prototype.getEspecificType = function (object) {
    var especificType = "DEFAULT",
        typeShape = this.getType(),
        shape = this;
    if (typeof object === "object" && !jQuery.isEmptyObject(object)) {
        shape = object;
    }
    switch (typeShape) {
        case "PMActivity":
            especificType = shape.act_task_type;
            break;
        case "PMGateway":
            especificType = shape.gat_type;
            break;
        case "PMEvent":
            especificType = shape.evn_type + "_" + shape.evn_marker;
            break;
        case "PMArtifact":
            especificType = shape.art_type;
            break;
        case "PMData":
            especificType = shape.dat_type;
            break;
    }
    return especificType;
};