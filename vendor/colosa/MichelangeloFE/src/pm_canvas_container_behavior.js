var CanvasContainerBehavior = function () {
};

CanvasContainerBehavior.prototype = new PMUI.behavior.RegularContainerBehavior();
CanvasContainerBehavior.prototype.type = "CanvasContainerBehavior";

/**
 * Adds a shape to a given container given its coordinates
 * @param {PMUI.draw.BehavioralElement} container container using this behavior
 * @param {PMUI.draw.Shape} shape shape to be added
 * @param {number} x x coordinate where the shape will be added
 * @param {number} y y coordinate where the shape will be added
 * @param {boolean} topLeftCorner Determines whether the x and y coordinates
 * will be considered from the top left corner or from the center
 */
CanvasContainerBehavior.prototype.addToContainer = function (container, shape, x, y, topLeftCorner) {
    var shapeLeft = 0,
        shapeTop = 0,
        shapeWidth,
        shapeHeight,
        canvas,
        topLeftFactor = (topLeftCorner === true) ? 0 : 1;

    if (container.family === "Canvas") {
        canvas = container;
    } else {
        canvas = container.canvas;
    }
    shapeWidth = shape.getZoomWidth();
    shapeHeight = shape.getZoomHeight();

    shapeLeft += x - (shapeWidth / 2) * topLeftFactor;
    shapeTop += y - (shapeHeight / 2) * topLeftFactor;

    shapeLeft /= canvas.zoomFactor;
    shapeTop /= canvas.zoomFactor;

    shape.setParent(container);
    container.getChildren().insert(shape);
    this.addShape(container, shape, shapeLeft, shapeTop);

    // fix the zIndex of this shape and it's children
    if (shape.getType() === 'PMPool' || shape.getType() === 'PMParticipant') {
        shape.fixZIndex(shape, 3);
    } else {
        shape.fixZIndex(shape, 2);
    }
    // fix resize minWidth and minHeight and also fix the dimension
    // of this shape (if a child made it grow)
    container.updateDimensions(10);
    // adds the shape to either the customShape arrayList or the regularShapes
    // arrayList if possible
    canvas.addToList(shape); 
    canvas.triggerCreateEvent(shape, []);
};
CanvasContainerBehavior.prototype.addShape = function (container, shape, x, y) {
    shape.setPosition(x, y);
    //insert the shape HTML to the DOM
    if (shape instanceof PMArtifact && shape.art_type === 'GROUP') {
        $(container.getHTML()).prepend(shape.getHTML());
    } else {
        container.getHTML().appendChild(shape.getHTML());
    }
    shape.updateHTML();
    shape.paint();
    shape.applyBehaviors();
    shape.attachListeners();
    return this;
};
/**
 * Removes shape from its current container
 * @param {PMUI.draw.Shape} shape shape to be removed
 * @template
 * @protected
 */
CanvasContainerBehavior.prototype.removeFromContainer = function (shape) {
    var canvas = shape.getCanvas();
    canvas.triggerRemoveEvent(shape, []);
    PMUI.behavior.RegularContainerBehavior.prototype.removeFromContainer.call(this, shape);
};
