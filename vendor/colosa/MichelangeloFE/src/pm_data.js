/**
 * @class PMGateway
 * @param {Object} options
 */
var PMData = function (options) {
    PMShape.call(this, options);
    this.dat_name = '';
    this.dat_type = 'DATAOBJECT';
    this.dat_is_collection = false;
    this.dat_is_global = false;
    this.dat_object_ref = '';
    this.dat_is_unlimited = false;
    this.dat_capacity = 0;

    this.businessObject = {};
    PMData.prototype.init.call(this, options);
};

PMData.prototype = new PMShape();
/**
 * Defines the object type
 * @type {String}
 */
PMData.prototype.type = 'PMData';

PMData.prototype.mapBpmnType = {
    'DATAOBJECT': 'bpmn:DataObject',
    'DATAINPUT': 'bpmn:DataInput',
    'DATAOUTPUT': 'bpmn:DataOutput'
};

PMData.prototype.getDataObject = function () {
    var name = this.getName(),
        container,
        element_id;
    switch (this.parent.type) {
        case 'PMCanvas':
            container = 'bpmnDiagram';
            element_id = this.canvas.id;
            break;
        case 'PMPool':
            container = 'bpmnPool';
            element_id = this.parent.id;
            break;
        case 'PMLane':
            container = 'bpmnLane';
            element_id = this.parent.id;
            break;
        default:
            container = 'bpmnDiagram';
            element_id = this.canvas.id;
            break;
    }
    return {
        dat_uid: this.dat_uid,
        dat_name: name,
        dat_type: this.dat_type,
        bou_x: this.x,
        bou_y: this.y,
        bou_width: this.width,
        bou_height: this.height,
        bou_container: container,
        bou_element: element_id,
        _extended: this.getExtendedObject()
    };
};
/**
 * Initialize the PMData object
 * @param options
 */
PMData.prototype.init = function (options) {
    var defaults = {
        dat_name: '',
        dat_type: '',
        dat_is_collection: false,
        dat_is_global: false,
        dat_object_ref: '',
        dat_is_unlimited: false,
        dat_capacity: 0
    };
    jQuery.extend(true, defaults, options);
    this.setDataUid(defaults.dat_uid)
        .setDataType(defaults.dat_type);
    if (defaults.dat_name) {
        this.setName(defaults.dat_name);
    }
    this.setOnBeforeContextMenu(this.beforeContextMenu);
};
/**
 * Gets BPMNShape type
 * @returns {string}
 */
PMData.prototype.getDataType = function () {
    return this.dat_type;
};

/**
 * Gets Data Name
 * @returns {string}
 */
PMData.prototype.getDataName = function () {
    return this.dat_name;
};

/**
 * Gets Data Capasity
 * @returns {*}
 */
PMData.prototype.getCapacity = function () {
    return this.dat_capacity;
};

/**
 * Gets Data global value
 * @returns {boolean}
 */
PMData.prototype.getIsGlobal = function () {
    return this.dat_is_global;
};

/**
 * Gets Data Unlimited value
 * @returns {boolean}
 */
PMData.prototype.getUnlimited = function () {
    return this.dat_is_unlimited;
};
/**
 * Gets Data collection status
 * @returns {boolean}
 */
PMData.prototype.getDataCollection = function () {
    return this.dat_is_collection;
};
/**
 * Gets Data type
 * @returns {string}
 */
PMData.prototype.getDataType = function () {
    return this.dat_type;
};
/**
 * Sets the act_uid property
 * @param {String} value
 * @return {*}
 */
PMData.prototype.setDataUid = function (value) {
    this.dat_uid = value;
    return this;
};
/**
 * Sets Data name
 * @param {string} newName
 * @chainable
 */
PMData.prototype.setName = function (name) {
    if (typeof name !== 'undefined') {
        this.act_name = name;
        if (this.label) {
            this.label.setMessage(name);
        }
    }
    return this;
};
/**
 * Sets BPMNData type
 * @param {string} newType
 * @chainable
 */
PMData.prototype.setDataType = function (newType) {
    if (!newType || typeof newType === 'undefined') {
        return;
    }
    this.dat_type = newType;
    return this;
};
/**
 * Sets Data global mode
 * @param {boolean} isGlobal
 * @returns {*}
 */
PMData.prototype.setIsGlobal = function (isGlobal) {
    if (typeof isGlobal !== 'undefined') {
        this.dat_is_global = isGlobal;
    }
    return this;
};
/**
 * Sets Data capasity
 * @param {string} capacity
 * @returns {*}
 */
PMData.prototype.setCapacity = function (capacity) {
    if (typeof capacity === 'undefined')
        return;
    this.dat_capacity = capacity;
    return this;
};
/**
 * Setd data unlimited mode
 * @param {boolean} unlimited
 * @returns {*}
 */
PMData.prototype.setIsUnlimited = function (unlimited) {
    if (typeof unlimited === 'undefined')
        return;
    this.dat_is_unlimited = unlimited;
    return this;
};
/**
 * Sets Data collection mode
 * @param {boolean} collection
 * @returns {*}
 */
PMData.prototype.setIsCollection = function (collection) {
    if (typeof collection === 'undefined')
        return;
    this.dat_is_collection = collection;
    return this;
};
/**
 * Change data type
 * @param {String} type
 * @returns {*}
 */
PMData.prototype.switchDataType = function (type) {
    var marker = this.markersArray.get(0),
        lowerType = type.toLowerCase();
    marker.removeAllClasses();
    marker.setMarkerZoomClasses([
        "mafe-" + lowerType + "-marker-10",
        "mafe-" + lowerType + "-marker-15",
        "mafe-" + lowerType + "-marker-21",
        "mafe-" + lowerType + "-marker-26",
        "mafe-" + lowerType + "-marker-31"
    ]);
    marker.paint(true);
    this.setDataType(type);
    this.updateBpmnDataType(this.mapBpmnType[this.getDataType()], type);
    PMDesigner.project.updateElement([]);
    return this;
};

PMData.prototype.updateBpmnDataType = function (newBpmnType, dataType) {
    this.verifyDataIOEspecification();
    this.removeBpmn();
    this.businessObject.elem = null;
    this.createBpmn(newBpmnType, dataType);
};

PMData.prototype.createWithBpmn = function (bpmnElementType, name) {
    var businessObject = {};
    if (this.extendedType === 'DATASTORE') {
        var ds = PMDesigner.bpmnFactory.create('bpmn:DataStore', {id: this.id, name: this.getName()});
        PMDesigner.businessObject.get('rootElements').push(ds);
        businessObject.elem = PMDesigner.bpmnFactory.create('bpmn:DataStoreReference', {
            id: this.id + '_ref',
            name: this.getName(),
            dataStoreRef: ds
        });
        businessObject.elem.dsRef = ds;
    } else {

        if (bpmnElementType == 'bpmn:DataObject' || bpmnElementType === undefined) {
            var dObj = PMDesigner.bpmnFactory.create('bpmn:DataObject', {id: this.id, name: this.getName()});
            businessObject.elem = PMDesigner.bpmnFactory.create('bpmn:DataObjectReference', {
                id: this.id + '_ref',
                name: this.getName(),
                dataObjectRef: dObj
            });
            // validate if container is a lane because data store is always into process tag
            if (this.parent.getType() === 'PMLane') {
                this.updateSemanticParent({elem: dObj}, this.parent.parent.businessObject);
            } else {
                this.updateSemanticParent({elem: dObj}, this.parent.businessObject);
            }
            businessObject.elem.doRef = dObj;
        }
        if (bpmnElementType == 'bpmn:DataInput' || bpmnElementType == 'bpmn:DataOutput') {
            businessObject = this.createDataIOBusinessObject(bpmnElementType);
        }
    }
    if (!businessObject.di) {
        businessObject.di = PMDesigner.bpmnFactory.createDiShape(businessObject.elem, {}, {
            id: businessObject.elem.id + '_di'
        });
    }
    this[name] = businessObject;
};

PMData.prototype.beforeContextMenu = function () {
    var items, i,
        menuItem,
        hasMarker = false;
    this.canvas.hideAllCoronas();
    if (this.getDataType() === 'DATAOBJECT' || this.getDataType() === 'DATAOUTPUT' || this.getDataType() === 'DATAINPUT') {
        items = this.menu.items.find('id', 'dataType').childMenu.items;
        for (i = 0; i < items.getSize(); i += 1) {
            menuItem = items.get(i);
            if (menuItem.id === this.getDataType().toLowerCase()) {
                menuItem.disable();
                hasMarker = true;
            } else {
                menuItem.enable();
            }
        }
    }
};

PMData.prototype.getDataType = function () {
    return this.dat_type;
};

PMData.prototype.createDataIOEspecification = function (element) {
    var ioEspecification = PMDesigner.bpmnFactory.create('bpmn:InputOutputSpecification', {
        id: this.id + '_ioEspecification',
        name: this.getName() + '_ioEspecification'
    });
    ioEspecification['dataInputs'] = [];
    this.parent.businessObject.elem['ioSpecification'] = ioEspecification;
};

PMData.prototype.verifyDataIOEspecification = function () {
    if (!this.parent.businessObject.elem || !this.parent.businessObject.elem.get('ioSpecification')) {
        this.createDataIOEspecification(this.parent.businessObject);
    }
};

PMData.prototype.createDataBusinessObject = function () {
    return this;
};

PMData.prototype.createDataIOBusinessObject = function (bpmnElementType) {
    var dObj = PMDesigner.bpmnFactory.create(bpmnElementType, {id: this.id, name: this.getName()});
    this.parent.businessObject.elem['ioSpecification'].dataInputs.push(dObj);
    return {elem: dObj};
};