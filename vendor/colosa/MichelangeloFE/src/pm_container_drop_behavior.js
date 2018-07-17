/**
 * @class PMUI.behavior.ContainerDropBehavior
 * Encapsulates the drop behavior of a container
 * @extends PMUI.behavior.DropBehavior
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Array} [selectors=[]] css selectors that this drop behavior will
 * accept
 */
var PMContainerDropBehavior = function (selectors) {
    PMUI.behavior.DropBehavior.call(this, selectors);
};
PMContainerDropBehavior.prototype = new PMUI.behavior.DropBehavior();
/**
 * Type of the instances
 * @property {String}
 */
PMContainerDropBehavior.prototype.type = "PMContainerDropBehavior";
/**
 * Default selectors for this drop behavior
 * @property {String}
 */
PMContainerDropBehavior.prototype.defaultSelector = "";

/**
 * On drop handler for this drop behavior, creates shapes when dropped from the
 * toolbar, or move shapes among containers
 * @param {PMUI.draw.Shape} shape
 * @return {Function}
 */
PMContainerDropBehavior.prototype.onDrop = function (shape) {
    return function (e, ui) {
        var customShape = null,
            canvas = shape.getCanvas(),
            selection,
            sibling,
            i,
            command,
            coordinates,
            id,
            shapesAdded = [];
        if (canvas.readOnly) {
            return false;
        }

        shape.entered = false;
        if (ui.helper && ui.helper.attr('id') === "drag-helper") {
            return false;
        }
        id = ui.draggable.attr('id');

        customShape = canvas.shapeFactory(id);
        if (customShape === null) {

            customShape = canvas.customShapes.find('id', id);
            if (!customShape || !shape.dropBehavior.dropHook(shape, customShape, e, ui)) {
                PMDesigner.msgFlash('Invalid operation.'.translate(), document.body, 'error', 5000, 5);
                customShape.setPosition(customShape.getOldX(), customShape.getOldY());
                return false;
            }
            if (customShape.getParent().getType() === 'PMLane'
                && shape.getType() === 'PMPool'
                && shape.bpmnLanes.getSize() > 0) {
                PMDesigner.msgFlash('Invalid operation.'.translate(), document.body, 'error', 5000, 5);
                customShape.setPosition(customShape.getOldX(), customShape.getOldY());
                return false;
            }

            if (!(customShape.parent &&
                customShape.parent.id === shape.id)) {

                selection = canvas.currentSelection;
                for (i = 0; i < selection.getSize(); i += 1) {
                    sibling = selection.get(i);
                    coordinates = PMUI.getPointRelativeToPage(sibling);
                    coordinates = PMUI.pageCoordinatesToShapeCoordinates(shape, null,
                        coordinates.x, coordinates.y, customShape);
                    shapesAdded.push({
                        shape: sibling,
                        container: shape,
                        x: coordinates.x,
                        y: coordinates.y,
                        topLeft: false
                    });
                }
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
            var portx,
                connectionx,
                result;
            for (var i = 0; i < customShape.ports.asArray().length; i += 1) {
                portx = customShape.ports.asArray()[i];
                connectionx = portx.connection;

                result = PMDesigner.connectValidator.isValid(connectionx.getSrcPort().parent, connectionx.getDestPort().parent, connectionx);
                if (result.conf && result.conf.segmentStyle !== connectionx.originalSegmentStyle) {
                    PMDesigner.msgFlash('Invalid flow between elements. Please delete the flow and reconnect the elements.'.translate(), document.body, 'error', 5000, 5);
                }
            }
        } else {
            coordinates = PMUI.pageCoordinatesToShapeCoordinates(shape, e, null, null, customShape);
            var result = shape.addElement(customShape, coordinates.x, coordinates.y,
                customShape.topLeftOnCreation);
            if (shape.containerBehavior.addElementLane !== true) {
                //since it is a new element in the designer, we triggered the
                //custom on create element event
                canvas.hideAllCoronas();
                canvas.updatedElement = customShape;
                if (shape.getType() == 'PMLane') {
                    command = new PMCommandAddToLane(shape, customShape, coordinates);
                } else if (customShape.getType() === 'PMLane') {
                    command = new PMCommandCreateLane(customShape);
                } else {
                    // create the command for this new shape
                    command = new PMUI.command.CommandCreate(customShape);
                }
                canvas.commandStack.add(command);
                command.execute();
                canvas.hideAllFocusLabels();
                if (customShape.label && customShape.focusLabel) {
                    customShape.label.getFocus();
                }
            }
            canvas.hideAllFocusLabels();
            if (customShape.label && customShape.focusLabel) {
                customShape.label.getFocus();
            }
        }
    };
};

PMContainerDropBehavior.prototype.setSelectors = function (selectors, overwrite) {
    PMUI.behavior.DropBehavior.prototype
        .setSelectors.call(this, selectors, overwrite);
    this.selectors.push(".port");
    return this;
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
PMContainerDropBehavior.prototype.dropHook = function (shape, customShape, e, ui) {
    var result = true,
        shapesTypes = ['PMEvent', 'PMActivity'];
    if ((shapesTypes.indexOf(customShape.getType()) > -1) &&
        !PMDesigner.connectValidator.onDropMovementIsAllowed(shape, customShape)) {
        result = false;
    }
    return result;
};