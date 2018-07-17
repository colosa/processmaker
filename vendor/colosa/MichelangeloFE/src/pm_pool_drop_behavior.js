/**
 * @class PMPoolDropBehavior
 * The {@link PMUI.behavior.DropBehavior DropBehavior} for PMPool class.
 * @extend PMUI.behavior.DropBehavior
 * @param {Object} [options] css selectors that the drop behavior
 * will accept
 * @constructor
 */
var PMPoolDropBehavior = function (options) {
    PMUI.behavior.DropBehavior.call(this, options);
};

PMPoolDropBehavior.prototype = new PMUI.behavior.DropBehavior();

PMPoolDropBehavior.prototype.constructor = PMPoolDropBehavior;
/**
 * Type for the instance
 * @property {string}
 */
PMPoolDropBehavior.prototype.type = 'PMPoolDropBehavior';
/**
 * @inheritDoc
 */
PMPoolDropBehavior.prototype.onDrop = function (shape) {
    return function (e, ui) {
        var canvas = shape.getCanvas(),
            id = ui.draggable.attr('id'),
            shapesAdded = [],
            selection,
            position,
            droppedShape,
            command;

        if (canvas.readOnly) {
            return false;
        }

        droppedShape = canvas.shapeFactory(id);

        if (!droppedShape) {
            droppedShape = canvas.customShapes.find("id", id);
            if (!droppedShape || !shape.dropBehavior.dropHook(shape, droppedShape, e, ui)) {
                PMDesigner.msgFlash('Invalid operation.'.translate(), document.body, 'error', 5000, 5);
                droppedShape.setPosition(droppedShape.getOldX(), droppedShape.getOldY());
                return false;
            }
            if (droppedShape.parent.id !== shape.id) {

                selection = canvas.currentSelection;
                selection.asArray().forEach(function (item) {
                    var coordinates = PMUI.getPointRelativeToPage(item);

                    coordinates = PMUI.pageCoordinatesToShapeCoordinates(shape, null,
                        coordinates.x, coordinates.y, droppedShape);
                    shapesAdded.push({
                        shape: item,
                        container: shape,
                        x: coordinates.x,
                        y: coordinates.y,
                        topLeft: false
                    });
                });
                if (shape.getType() === 'PMLane') {
                    command = new PMCommandSwitchToLaneContainer(shapesAdded);
                } else {
                    command = new PMUI.command.CommandSwitchContainer(shapesAdded);
                }
                command.execute();
                canvas.commandStack.add(command);
                canvas.multipleDrop = true;
            }

            canvas.hideAllFocusLabels();
            shape.updateDimensions(10);
            canvas.updatedElement = null;

            droppedShape.ports.asArray().forEach(function (item) {
                var connectionx = item.connection,
                    result = PMDesigner.connectValidator.isValid(
                        connectionx.getSrcPort().parent,
                        connectionx.getDestPort().parent,
                        connectionx);

                if (result.conf && result.conf.segmentStyle !== connectionx.originalSegmentStyle) {
                    PMDesigner.msgFlash('Invalid flow between elements. Please delete the flow and reconnect the elements.'.translate(), document.body, 'error', 5000, 5);
                }
            });

            return;
        }

        if (droppedShape instanceof PMLane && _.find(shape.children.asArray(), function (i) { return !(i instanceof PMLane); })) {
            return PMDesigner.msgFlash('The lane can be dropped only over an empty pool. Please empty the pool before dropping a lane.'.translate(), document.body, 'error', 3000, 5);
        }

        position = PMUI.pageCoordinatesToShapeCoordinates(shape, e, null, null, droppedShape);


        command = new PMCommandCreateLane({
            pool: shape,
            lane: droppedShape,
            x: position.getX(),
            y: position.getY()
        });

        if (command) {
            canvas.hideAllCoronas();
            canvas.updatedElement = shape;
            canvas.commandStack.add(command);
            command.execute();

            canvas.hideAllFocusLabels();
            if (droppedShape.label && droppedShape.focusLabel) {
                droppedShape.label.getFocus();
            }
        }
    };
};
/**
 * Hook if PMEvent onDrop is correct
 * @param {PMUI.draw.Shape} shape
 * @param {PMUI.draw.Shape} customShape
 * @param {Object} e jQuery object that contains the properties on the
 * drop event
 * @param {Object} ui jQuery object that contains the properties on the
 * drop event
 * @returns {boolean}
 */
PMPoolDropBehavior.prototype.dropHook = function (shape, customShape, e, ui) {
    var result = true,
        shapesTypes = ['PMEvent', 'PMActivity'];
    if ((shapesTypes.indexOf(customShape.getType()) > -1) &&
        !PMDesigner.connectValidator.onDropMovementIsAllowed(shape, customShape)) {
        result = false;
    }
    return result;
};