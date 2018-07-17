var PMParticipant = function (options) {
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
    this.childLaneset = '';
    /**
     *
     * @type {Number}
     */
    this.numParticipants = 0;
    this.graphic = null;
    this.headLineCoord = 40;
    this.orientation = 'HORIZONTAL';
    PMParticipant.prototype.initObject.call(this, options);
};

PMParticipant.prototype = new PMShape();
PMParticipant.prototype.type = 'PMParticipant';
PMParticipant.prototype.participantResizeBehavior = null;

PMParticipant.prototype.getDataObject = function () {
    var name = this.getName();
    return {
        par_uid: this.getID(),
        par_name: name,
        bou_x: this.x,
        bou_y: this.y,
        bou_width: this.width,
        bou_height: this.height,
        bou_container: 'bpmnDiagram',
        _extended: this.getExtendedObject()
    };
};

PMParticipant.prototype.initObject = function (options) {
    var defaultOptions = {
        name: 'Participant',
        numParticipants: 0,
        orientation: 'HORIZONTAL'
    };
    $.extend(true, defaultOptions, options);

    this.setName(defaultOptions.par_name)
        .setNumparticipants(defaultOptions.numParticipants)
        .setOrientation(defaultOptions.orientation);
};

PMParticipant.prototype.createHTML = function () {
    PMShape.prototype.createHTML.call(this);
    this.style.addClasses(['mafe_participant']);
    return this.html;
};
PMParticipant.prototype.decreaseZIndex = function () {
    this.fixZIndex(this, 1);
    return this;
};
PMParticipant.prototype.onUpdateLabel = function () {
    var label = this.getLabels().get(0);
    label.text.style["text-overflow"] = "ellipsis";
    label.text.style["overflow"] = "hidden";
    label.text.style["white-space"] = "nowrap";
    label.text.style["margin-left"] = "2%";
    label.text.setAttribute("title", label.getMessage());
    return this;
};
PMParticipant.prototype.paint = function () {
    var zoomFactor = this.canvas.zoomFactor;
    if (typeof this.graphic === 'undefined' || this.graphic === null) {
        this.graphic = new JSGraphics(this.id);
    } else {
        this.graphic.clear();
    }

    if (this.validatorMarker) {
        this.validatorMarker.paint();
        this.validatorMarker.hide();
    }
    this.graphic.setColor('#3b4753');
    this.graphic.setStroke(2);
    if (this.orientation === 'VERTICAL') {
        this.graphic.drawLine(0, this.headLineCoord * zoomFactor, this.zoomWidth,
            this.headLineCoord * zoomFactor);
        this.getLabels().get(0).setOrientation('horizontal');
        this.getLabels().get(0).setLabelPosition('top');
    } else {
        this.graphic.drawLine(this.headLineCoord * zoomFactor, 0,
            this.headLineCoord * zoomFactor,
            this.zoomHeight - 5);
        this.getLabels().get(0).setOrientation('vertical');
        this.getLabels().get(0).setLabelPosition('center-left', 20, 0);

    }
    this.onUpdateLabel();
    this.graphic.paint();
    if (this.corona) {
        this.corona.paint();
        this.corona.hide();
    }
};

/**
 * Change pool orientation dinamically
 * @param {String} orientation
 * @returns {BPMNPool}
 */
PMParticipant.prototype.changeOrientation = function (orientation) {
    var command = new BPMNCommandUpdateOrientation(this, {
        before: this.getOrientation(),
        after: orientation.toUpperCase()
    });
    command.execute();
    this.getCanvas().commandStack.add(command);
    return this;
};
/**
 * Set participant name
 * @param {String} name
 * @returns {BPMNLane}
 */
PMParticipant.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.par_name = name;
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Set participant name
 * @param {String} name
 * @returns {BPMNLane}
 */
PMParticipant.prototype.setType = function (newType) {
    this.type = newType;
    return this;
};
/**
 * Set process uid asociated to participant
 * @param {String} uid
 * @returns {BPMNLane}
 */
PMParticipant.prototype.setProUid = function (uid) {
    this.proUid = uid;
    return this;
};
/**
 * Set number of participants
 * @param {Number} uid
 * @returns {BPMNLane}
 */
PMParticipant.prototype.setNumparticipants = function (numParticipants) {
    this.numParticipants = numParticipants;
    return this;
};

/**
 * Set pool orientation
 * @param {String} orientation
 * @returns {BPMNLane}
 */
PMParticipant.prototype.setOrientation = function (orientation) {
    this.orientation = orientation;
    return this;
};
/**
 * Get the participant name
 * @returns {String}
 */
PMParticipant.prototype.getName = function () {
    return this.par_name;
};
/**
 * Get the participant process uid
 * @returns {String}
 */
PMParticipant.prototype.getProUid = function () {
    return this.proUid;
};
/**
 * Get the number of participants
 * @returns {Number}
 */
PMParticipant.prototype.getNumParticipants = function () {
    return this.numParticipants;
};
/**
 * Get the type
 * @returns {Number}
 */
PMParticipant.prototype.getType = function () {
    return this.type;
};
/**
 * Get orientation
 * @returns {Number}
 */
PMParticipant.prototype.getOrientation = function () {
    return this.orientation;
};

PMParticipant.prototype.setResizeBehavior = function (behavior) {
    var factory = new PMUI.behavior.BehaviorFactory({
        products: {
            "regularresize": PMUI.behavior.RegularResizeBehavior,
            "Resize": PMUI.behavior.RegularResizeBehavior,
            "yes": PMUI.behavior.RegularResizeBehavior,
            "resize": PMUI.behavior.RegularResizeBehavior,
            "noresize": PMUI.behavior.NoResizeBehavior,
            "NoResize": PMUI.behavior.NoResizeBehavior,
            "no": PMUI.behavior.NoResizeBehavior,
            "participantResize": PMParticipantResizeBehavior
        },
        defaultProduct: "noresize"
    });
    this.resizeBehavior = factory.make(behavior);
    if (this.html) {
        this.resize.init(this);
    }
    return this;
};

PMParticipant.prototype.createBpmn = function (type) {
    var bpmnCollaboration;
    if (!this.parent.businessObject.di) {
        this.parent.createBPMNDiagram();
    }
    if (!(_.findWhere(PMDesigner.businessObject.get('rootElements'), {$type: "bpmn:Collaboration"}))) {
        bpmnCollaboration = PMDesigner.moddle.create('bpmn:Collaboration', {id: 'pmui-' + PMUI.generateUniqueId()});
        PMDesigner.businessObject.get('rootElements').push(bpmnCollaboration);
        this.parent.businessObject.di.bpmnElement = bpmnCollaboration;
        bpmnCollaboration.participants = [];
        bpmnCollaboration.messageFlows = [];
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
PMParticipant.prototype.updateSemanticParent = function (businessObject, newParent) {
    var children;
    if (businessObject.elem.$parent === newParent.elem) {
        return;
    }
    if (businessObject.elem.$parent) {
        children = businessObject.elem.$parent.get('participants');
        CollectionRemove(children, businessObject.elem);
    }
    if (!newParent.elem) {
        businessObject.elem.$parent = null;
    } else {
        children = newParent.elem.get('participants');
        children.push(businessObject.elem);
        businessObject.elem.$parent = newParent.elem;
    }
};

PMParticipant.prototype.updateDiParent = function (di, parentDi) {
    PMShape.prototype.updateDiParent.call(this, di, parentDi);
};

/**
 * create busines object to moodle bpmn export
 */
PMParticipant.prototype.createBusinesObject = function () {
    var participant = _.findWhere(this.participantObject.elem.$parent.get('participants'),
            {id: this.participantObject.elem.id}),
        bpmnProcess = PMDesigner.moddle.create('bpmn:Process',
            {id: 'pmui-' + PMUI.generateUniqueId()});

    PMDesigner.businessObject.get('rootElements').push(bpmnProcess);
    this.businessObject.elem = bpmnProcess;
    this.businessObject.di = this.canvas.businessObject.di;
    participant.processRef = bpmnProcess;
};

PMParticipant.prototype.removeBpmn = function () {
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
        && this.businessObject.di.planeElement.length == 0) {
        this.parent.removeBPMNDiagram();
    }
};

PMParticipant.prototype.updateBpmn = function () {
    this.updateBounds(this.participantObject.di);

};