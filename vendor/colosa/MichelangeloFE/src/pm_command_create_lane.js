/**
 * Command create: command created when a lane is created (from the toolbar)
 * @class BPMNCommandCreateLane
 * @constructor
 */
var PMCommandCreateLane = function (settngs) {
    PMUI.command.Command.call(this, settngs);
};

PMCommandCreateLane.prototype = new PMUI.command.Command({});
/**
 * Type of command of this object
 * @type {String}
 */
PMCommandCreateLane.prototype.type = 'PMCommandCreateLane';

/**
 * Executes the command
 */
PMCommandCreateLane.prototype.execute = function () {
    var pool = this.receiver.pool,
        lane = this.receiver.lane;

    pool.addElement(lane, this.receiver.x, this.receiver.y, lane.topLeftOnCreation);
    lane.showOrHideResizeHandlers(false);
    lane.canvas.triggerCreateEvent(lane, []);
    if (lane instanceof PMLane) {
        pool.setLanePositionAndDimension(lane);
    }
};
/**
 * Inverse executes the command a.k.a. undo
 */
PMCommandCreateLane.prototype.undo = function () {
    var pool = this.receiver.pool,
        lane = this.receiver.lane;

    pool.removeElement(lane);
};
/**
 * Re-executes the command.
 * @returns {BPMNCommandCreateLane}
 */
PMCommandCreateLane.prototype.redo = function () {
    this.execute();
    return this;
};
