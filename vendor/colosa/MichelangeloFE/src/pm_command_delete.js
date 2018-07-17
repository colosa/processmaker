/**
 * @class CommandDelete
 * Class CommandDelete determines the actions executed when some shapes are deleted (redo) and the actions
 * executed when they're recreated (undo).
 *
 * Instances of this class are created in {@link Canvas#removeElements}.
 * @extends Command
 *
 * @constructor Creates an instance of the class CommandDelete
 * @param {Object} receiver The object that will execute the command
 */
PMCommandDelete = function (receiver) {
    PMUI.command.Command.call(this, receiver);
    /**
     * A stack of commandsConnect
     * @property {Array}
     */
    this.stackCommandConnect = [];
    /**
     * ArrayList that represents the selection that was active before deleting the elements
     * @property {ArrayList}
     */
    this.currentSelection = new PMUI.util.ArrayList();

    /**
     * Reference to the current connection in the canvas
     * @property {Connection}
     */
    this.currentConnection = null;
    /**
     * List of all the elements related to the commands
     * @property {Array}
     */
    this.relatedElements = [];
    this.beforeRelPositions = [];
    this.tempLanes = new PMUI.util.ArrayList();
    PMCommandDelete.prototype.initObject.call(this, receiver);
};

PMUI.inheritFrom('PMUI.command.Command', PMCommandDelete);

/**
 * Type of command
 * @property {String}
 */
PMCommandDelete.prototype.type = "PMCommandDelete";

/**
 * Instance initializer which uses options to extend the config options to initialize the instance
 * @param {Object} receiver The object that will execute the command
 * @private
 */
PMCommandDelete.prototype.initObject = function (receiver) {
    var i,
        shape;
    // move the current selection to this.currentSelection array
    for (i = 0; i < receiver.getCurrentSelection().getSize() > 0; i += 1) {
        shape = receiver.getCurrentSelection().get(i);
        this.currentSelection.insert(shape);
    }
    // save the currentConnection of the canvas if possible
    if (receiver.currentConnection) {
        this.currentConnection = receiver.currentConnection;
    }
};

/**
 * Saves and destroys connections and shapes
 * @private
 * @param {Object} shape
 * @param {boolean} root True if `shape` is a root element in the tree
 * @param {boolean} [fillArray] If set to true it'll fill `this.relatedElements` with the objects erased
 * @return {boolean}
 */
PMCommandDelete.prototype.saveAndDestroy = function (shape, root, fillArray) {
    var i,
        child,
        parent,
        children = null,
        connection,
        canvas = shape.canvas;

    if (shape.hasOwnProperty("children")) {
        children = shape.children;
    }
    // special function to be called as an afterwards
    // BIG NOTE: doesn't have to delete html
    if (shape.destroy) {
        shape.destroy();
    }
    for (i = 0; i < children.getSize(); i += 1) {
        child = children.get(i);
        this.saveAndDestroy(child, false, fillArray);
    }
    while (shape.ports && shape.ports.getSize() > 0) {
        connection = shape.ports.getFirst().connection;
        if (fillArray) {
            this.relatedElements.push(connection);
        }
        this.stackCommandConnect.push(
            new PMUI.command.CommandConnect(connection)
        );
        connection.saveAndDestroy();
    }
    // remove from the children array of its parent
    if (root) {
        parent = shape.parent;
        parent.getChildren().remove(shape);
        if (parent.isResizable()) {
            parent.resizeBehavior.updateResizeMinimums(shape.parent);
        }
        if (parent.getType() === 'PMLane') {
            parent.updateDimensions();
        }
        // remove from the currentSelection and from either the customShapes
        // arrayList or the regularShapes arrayList
        // remove the html only from the root
        shape.html = $(shape.html).detach()[0];
        if (shape.getType() === 'PMLane') {
            this.beforeRelPositions[shape.getID()] = shape.getRelPosition();
            shape.parent.bpmnLanes.remove(shape);
        }
    }
    if (fillArray) {
        this.relatedElements.push(shape);
    }
    // remove from the currentSelection and from either the customShapes
    // arrayList or the regularShapes arrayList
    canvas.removeFromList(shape);
    return true;
};
/**
 * Executes the command
 *
 * @chainable
 */
PMCommandDelete.prototype.execute = function () {
    var shape,
        i,
        canvas = this.receiver,
        currentConnection,
        stringified,
        fillArray = false,
        mainShape = null,
        data,
        self = this,
        url = '/project/' + PMDesigner.project.id + '/activity/validate-active-cases';
    if (this.relatedElements.length === 0) {
        fillArray = true;
    }
    canvas.emptyCurrentSelection();
    // copy from this.currentConnection
    if (this.currentSelection.getSize() === 1) {
        shape = this.currentSelection.get(0);
        if (shape.getType() === 'PMActivity') {
            data = {
                act_uid: shape.getID(),
                case_type: 'assigned'
            };
            PMDesigner.restApi.execute({
                data: JSON.stringify(data),
                method: "update",
                url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + url,
                async: false,
                success: function (data, textStatus, xhr) {
                    if (data.result) {
                        canvas.addToSelection(shape);
                    } else {
                        shape.corona.hide();
                        PMDesigner.msgFlash(data.message, document.body, 'error', 3000, 5);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    PMDesigner.msgFlash('There are problems removing task'.translate(), document.body, 'error', 3000, 5);
                }
            });
        } else {
            canvas.addToSelection(shape);
        }
    } else if (this.currentSelection.getSize() !== 0) {
        PMDesigner.msgFlash('Invalid operation, please delete elements individually'.translate(), document.body, 'error', 3000, 5);
    }

    if (canvas.currentSelection.getSize() === 1) {
        mainShape = shape;
    }
    // remove the elements in the canvas current selection
    stringified = [];
    if (shape && shape.getType() === 'PMPool') {
        for (i = 0; i < shape.bpmnLanes.getSize(); i += 1) {
            this.tempLanes.insert(shape.bpmnLanes.get(i));
        }
    }

    while (canvas.getCurrentSelection().getSize() > 0) {
        shape = canvas.getCurrentSelection().getFirst();
        this.saveAndDestroy(shape, true, fillArray);
    }
    if (shape && shape.getType() === 'PMLane') {
        for (i = 0; i < shape.parent.bpmnLanes.getSize(); i += 1) {
            shape.parent.bpmnLanes.get(i)
                .refreshChildrenPositions(true)
                .refreshConnections(false, false);
        }
    }
    // destroy the currentConnection
    canvas.currentConnection = this.currentConnection;
    currentConnection = canvas.currentConnection;
    if (currentConnection) {
        // add to relatedElements just in the case when only a connection is
        // selected and deleted
        this.relatedElements.push(currentConnection);

        this.stackCommandConnect.push(
            new PMUI.command.CommandConnect(currentConnection)
        );

        currentConnection.saveAndDestroy();
        currentConnection = null;
        mainShape = currentConnection;
    }
    canvas.triggerRemoveEvent(mainShape, this.relatedElements);
    return this;
};

/**
 * Inverse executes the command a.k.a. undo
 *
 * @chainable
 */
PMCommandDelete.prototype.undo = function () {
    // undo recreates the shapes
    var i,
        shape,
        mainShape = this.currentSelection.getFirst() || this.currentConnection,
        size,
        haveLanes = false,
        shapeBefore,
        j,
        element;
    this.currentSelection.sort(function (lane1, lane2) {
        return lane1.relPosition > lane2.relPosition;
    });

    for (i = 0; i < this.currentSelection.getSize(); i += 1) {
        shape = this.currentSelection.get(i);
        shapeBefore = null;
        // add to the canvas array of regularShapes and customShapes
        shape.canvas.addToList(shape);
        // add to the children of the parent
        shape.parent.getChildren().insert(shape);
        shape.showOrHideResizeHandlers(false);

        if (shape.getType() !== 'PMLane') {
            shape.parent.html.appendChild(shape.getHTML());
        } else {
            shapeBefore = shape.parent.bpmnLanes
                .get(this.beforeRelPositions[shape.getID()] - 1);
            if (shapeBefore) {
                shape.parent.html
                    .insertBefore(shape.getHTML(), shapeBefore.getHTML());
            } else {
                shape.parent.html.appendChild(shape.getHTML());
            }
            size = shape.parent.bpmnLanes.getSize();
            for (j = this.beforeRelPositions[shape.getID()] - 1; j < size; j += 1) {
                element = shape.parent.bpmnLanes.get(j);
                element.setRelPosition(j + 2);
            }
            shape.setRelPosition(this.beforeRelPositions[shape.getID()]);
            shape.parent.bpmnLanes.insert(shape);
            shape.parent.bpmnLanes.sort(shape.parent.comparisonFunction);
            shape.parent.updateAllLaneDimension();
        }
        if (shape.getType() === 'PMPool') {
            for (i = 0; i < this.tempLanes.getSize(); i += 1) {
                shape.bpmnLanes.insert(this.tempLanes.get(i));
            }
            if (shape.bpmnLanes.getSize() > 0) {
                shape.updateAllLaneDimension();
            }
        }
        shape.corona.hide();
    }
    // reconnect using the stack of commandConnect
    for (i = this.stackCommandConnect.length - 1; i >= 0; i -= 1) {
        this.stackCommandConnect[i].buildConnection();
    }
    this.receiver.triggerCreateEvent(mainShape, this.relatedElements);
    if (haveLanes) {
        for (i = 0; i < mainShape.parent.bpmnLanes.getSize(); i += 1) {
            mainShape.parent.bpmnLanes.get(i)
                .refreshChildrenPositions(true)
                .refreshConnections(false);
        }
    }
    return this;
};
/**
 * Executes the command (a.k.a redo)
 * @chainable
 */
PMCommandDelete.prototype.redo = function () {
    this.execute();
    return this;
};