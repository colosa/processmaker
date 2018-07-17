var PoolContainerBehavior = function () {
    this.addElementLane = false;
};

PoolContainerBehavior.prototype = new PMUI.behavior.RegularContainerBehavior();
PoolContainerBehavior.prototype.type = "PoolContainerBehavior";

PoolContainerBehavior.prototype.addToContainer = function (container,
                                                           shape, x, y,
                                                           topLeftCorner) {
    var shapeLeft = 0,
        shapeTop = 0,
        shapeWidth,
        shapeHeight,
        canvas,
        topLeftFactor = (topLeftCorner === true) ? 0 : 1;
    this.addElementLane = false;
    if (shape.type === 'PMLane') {
        if (container.type === "Canvas") {
            canvas = container;
        } else {
            canvas = container.canvas;
        }
        // have not lanes childrens
        if (container.children.getSize() !== container.bpmnLanes.getSize()) {
            PMDesigner.msgFlash('The lane can be dropped only over an empty pool. Please empty the pool before dropping a lane.'.translate(), document.body, 'error', 3000, 5);
            this.addElementLane = true;
        } else {
            shapeWidth = container.getWidth() - container.headLineCoord;
            shapeHeight = container.getHeight();
            shapeLeft += x - (shapeWidth / 2) * topLeftFactor;
            shapeTop += y - (shapeHeight / 2) * topLeftFactor;
            shapeLeft /= canvas.zoomFactor;
            shapeTop /= canvas.zoomFactor;
            shape.setParent(container);
            container.getChildren().insert(shape);
            this.addShape(container, shape, shapeLeft, shapeTop);
            container.addLane(shape);
            shape.fixZIndex(shape, 0);
            // fix resize minWidth and minHeight and also fix the dimension
            // of this shape (if a child made it grow)
            //container.resizeBehavior.updateResizeMinimums(container);
            // adds the shape to either the customShape arrayList or the regularShapes
            // arrayList if possible
            canvas.addToList(shape);
        }
    } else {
        if (PMUI.draw.Geometry.pointInRectangle(new PMUI.util.Point(x, y),
                new PMUI.util.Point(container.headLineCoord, 0),
                new PMUI.util.Point(container.getZoomWidth(),
                    container.getZoomHeight())
            )) {
            if (container.getChildren().find('type', 'PMLane')) {
                this.addToCanvas(container, shape);

            } else {
                PMUI.behavior.RegularContainerBehavior.prototype.addToContainer.call(this, container,
                    shape, x, y, topLeftCorner);
                shape.absoluteX += 2;
                shape.absoluteY += 2;
            }
        } else {
            this.addToCanvas(container, shape);
        }
    }
    if (!this.addElementLane) {
        shape.canvas.triggerCreateEvent(shape, []);
    }
};
/**
 * @inheritDoc
 */
PoolContainerBehavior.prototype.removeFromContainer = function (shape) {
    var pool = shape.getParent();

    pool.getChildren().remove(shape);

    if (pool.isResizable()) {
        pool.resizeBehavior.updateResizeMinimums(shape.parent);
    }

    if (shape instanceof PMLane) {
        pool.removeLane(shape);
        pool.updateOnRemoveLane(shape);  
    }

    shape.saveAndDestroy();
    shape.canvas.triggerRemoveEvent(shape, []);
    shape.setParent(null);

    return this;
};
/**
 * Force to add a shape to canvas
 * @param container
 * @param shape
 */
PoolContainerBehavior.prototype.addToCanvas = function (container, shape) {
    shape.setParent(container.canvas);
    container.canvas.getChildren().insert(shape);
    this.addShape(container.canvas, shape, shape.getOldX(), shape.getOldY());
    shape.fixZIndex(shape, 0);
    // adds the shape to either the customShape arrayList or the regularShapes
    // arrayList if possible
    container.canvas.addToList(shape);
};

PoolContainerBehavior.prototype.addShape = function (container, shape, x, y) {
    shape.setPosition(x, y);
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
