var LaneContainerBehavior = function () {
};

LaneContainerBehavior.prototype = new PMUI.behavior.RegularContainerBehavior();
LaneContainerBehavior.prototype.type = "LaneContainerBehavior";
/**
 * @inheritDoc
 */
LaneContainerBehavior.prototype.addToContainer = function (container, shape, x, y, topLeftCorner) {
    PMUI.behavior.RegularContainerBehavior.prototype.addToContainer.call(this, container, shape, x, y, topLeftCorner);
    shape.getCanvas().triggerCreateEvent(shape, []);
};

LaneContainerBehavior.prototype.addShape = function (container, shape, x, y) {
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
 * @inheritDoc
 */
LaneContainerBehavior.prototype.removeFromContainer = function (shape) {
    shape.getCanvas().triggerRemoveEvent(shape, []);
    PMUI.behavior.RegularContainerBehavior.prototype.removeFromContainer.call(this, shape);
};