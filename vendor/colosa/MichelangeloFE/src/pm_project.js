var PMProject;
PMProject = function (options) {
    this.diagrams = new PMUI.util.ArrayList();
    this.keys = null;
    this.waitingResponse = false;
    this.identifiers = {};
    this.isSave = false;
    this.XMLSupported = true;
    this.isClose = false;
    this.userSettings = null;
    this.definitions = null;
    this.dirtyElements = [
        {
            laneset: {},
            lanes: {},
            activities: {},
            events: {},
            gateways: {},
            flows: {},
            artifacts: {},
            lines: {},
            data: {},
            participants: {}
        },
        {
            laneset: {},
            lanes: {},
            activities: {},
            events: {},
            gateways: {},
            flows: {},
            artifacts: {},
            lines: {},
            data: {},
            participants: {}
        }
    ];
    PMProject.prototype.init.call(this, options);
};

PMProject.prototype.init = function (options) {
    var defaults = {
        projectId: "",
        projectName: "",
        description: "",
        remoteProxy: null,
        localProxy: null,
        keys: {
            access_token: null,
            expires_in: null,
            token_type: null,
            scope: null,
            refresh_token: null,
            client_id: null,
            client_secret: null
        },
        listeners: {
            create: function () {
            },
            remove: function () {
            },
            update: function () {
            },
            success: function () {
            },
            failure: function () {
            }
        }
    };
    jQuery.extend(true, defaults, options);

    this.setKeysClient(defaults.keys)
        .setID(defaults.id)
        .setTokens(defaults.keys)
        .setListeners(defaults.listeners);
    this.remoteProxy = new PMUI.proxy.RestProxy();
};

PMProject.prototype.setID = function (id) {
    this.id = id;
    return this;
};

PMProject.prototype.setProjectId = function (id) {
    if (typeof id === "string") {
        this.projectId = id;
    }
    return this;
};

PMProject.prototype.setXMLSupported = function (value) {
    if (typeof value == "boolean")
        this.XMLSupported = value;
    return this;
};

PMProject.prototype.setProjectName = function (name) {
    if (typeof name === "string") {
        this.projectName = name;
        jQuery(".navBar div").remove();
        if ($(".navBar h2").length > 0) {
            $(".navBar h2").text(name);
        } else {
            jQuery(".navBar").append("<h2>" + name + "</h2>");
        }

    }
    return this;
};
/**
 * Sets the user settings to the local property
 * @param settings
 * @returns {PMProject}
 */
PMProject.prototype.setUserSettings= function (settings) {
    this.userSettings = settings;
    return this;
};

PMProject.prototype.setDescription = function (description) {
    this.description = description;
    return this;
};

PMProject.prototype.setKeysClient = function (keys) {
    if (typeof keys === "object") {
        this.keys = keys;
    }
    return this;
};

PMProject.prototype.setListeners = function (listeners) {
    if (typeof listeners === "object") {
        this.listeners = listeners;
    }
    return this;
};
/**
 * Sets the time interval used to save automatically
 * @param {Number} interval Expressed in miliseconds
 * @return {*}
 */
PMProject.prototype.setSaveInterval = function (interval) {
    this.saveInterval = interval;
    return this;
};

PMProject.prototype.getKeysClient = function () {
    var keys = this.keys;
    return {
        access_token: keys.access_token,
        expires_in: keys.expires_in,
        token_type: keys.token_type,
        scope: keys.scope,
        refresh_token: keys.refresh_token,
        client_id: keys.client_id,
        client_secret: keys.client_secret
    };
};
PMProject.prototype.buildCanvas = function (selectors, options) {
    var canvas = new PMCanvas({
        id: PMUI.generateUniqueId(),
        project: PMDesigner.project,
        top: 77,
        width: 6000,
        height: 6000,
        style: {
            cssProperties: {
                overflow: "hidden"
            }
        },
        drop: {
            type: 'canvasdrop',
            selectors: selectors
        },
        container: "pmcanvas",
        readOnly: prj_readonly === 'true' ? true : false,
        hasClickEvent: true,
        copyAndPasteReferences: {
            PMEvent: PMEvent,
            PMGateway: PMGateway,
            PMActivity: PMActivity,
            PMArtifact: PMArtifact,
            PMFlow: PMFlow
        }
    });
    jQuery("#div-layout-canvas").append(canvas.getHTML());
    canvas.toogleGridLine();
    canvas.setShapeFactory(PMDesigner.shapeFactory);
    canvas.attachListeners();
    canvas.createConnectHandlers('', '');
    var menuCanvas = PMDesigner.getMenuFactory("CANVAS");
    canvas.setContextMenu(menuCanvas);
    //enable gridLines
    options.userSettings && options.userSettings.enabled_grid ?
        canvas.enableGridLine(): canvas.disableGridLine();
    PMDesigner.canvasList.addOption(
        {
            label: options.name,
            value: canvas.getID()
        });

    this.diagrams.insert(canvas);
    return canvas;
};

PMProject.prototype.getKeysClient = function () {
    var keys = this.keys;
    return {
        access_token: keys.access_token,
        expires_in: keys.expires_in,
        token_type: keys.token_type,
        scope: keys.scope,
        refresh_token: keys.refresh_token,
        client_id: keys.client_id,
        client_secret: keys.client_secret
    };
};

PMProject.prototype.load = function () {
    var keys = this.getKeysClient(),
        that = this;
    $.ajax({
        url: that.remoteProxy.url,
        type: 'GET',
        contentType: "application/json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Bearer " + keys.access_token);
            xhr.setRequestHeader("Accept-Language", LANG);
        },
        success: function (data, textStatus) {
            that.dirty = false;
            that.loadProject(data);
            $(".loader").fadeOut("slow");
        },
        error: function (xhr, textStatus, errorThrown) {
            $(".loader").fadeOut("slow");
            that.listeners.failure(that, xhr, response);
        }
    });
    return this;
};

PMProject.prototype.loadProject = function (project) {
    var that = this,
        i,
        j,
        diagram,
        canvas,
        sidebarCanvas = [];
    if (project) {
        this.loadingProcess = true;
        this.setProjectId(project.prj_uid);
        this.setProjectName(project.prj_name);
        this.setDescription(project.prj_description);
        this.setUserSettings(project.usr_setting_designer);
        if (project.prj_bpmn_file_upload) {
            that.importDiagram(project.prj_bpmn_file_upload);
        } else {
            for (i = 0; i < project.diagrams.length; i += 1) {
                diagram = project.diagrams[i];
                for (j = 0; j < PMDesigner.sidebar.length; j += 1) {
                    sidebarCanvas = sidebarCanvas.concat(PMDesigner.sidebar[j].getSelectors());
                    jQuery(".bpmn_shapes").append(PMDesigner.sidebar[j].getHTML());
                }
                //Remove Lane
                sidebarCanvas.splice(15, 1);
                //Remove Lasso and Validator
                sidebarCanvas.splice(17, 2);
                
                sidebarCanvas = sidebarCanvas.concat('.mafe-event-start');
                sidebarCanvas = sidebarCanvas.concat('.mafe-event-intermediate');
                sidebarCanvas = sidebarCanvas.concat('.mafe-event-end');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmactivity');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmgateway');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmdata');
                sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-annotation');
                sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-group');
                sidebarCanvas = sidebarCanvas.concat('.mafe-pool');
                sidebarCanvas = sidebarCanvas.concat('.mafe_participant');


                canvas = PMDesigner.project.buildCanvas(sidebarCanvas, {
                    name: 'Main',
                    userSettings: this.userSettings
                });

                PMUI.setActiveCanvas(canvas);
                jQuery("#p-center-layout").scroll(canvas.onScroll(canvas, jQuery("#p-center-layout")));

                var xmlStr =
                    '<?xml version="1.0" encoding="UTF-8"?>' +
                    '<bpmn2:definitions xmlns:bpmn2="http://www.omg.org/spec/BPMN/20100524/MODEL" id="BPMNProcessmaker" targetNamespace="http://bpmn.io/schema/bpmn">' +
                    '</bpmn2:definitions>';

                PMDesigner.moddle.fromXML(xmlStr, function (err, definitions) {
                    PMDesigner.businessObject = definitions;
                    canvas.buildDiagram(diagram);
                    if (!project.prj_update_date) {
                        canvas.setDefaultStartEvent();
                        PMDesigner.helper.startIntro();
                    }
                    that.setDirty(false);
                    that.loadingProcess = false;
                    that.loaded = true;
                    that.setSaveButtonDisabled();
                    PMDesigner.modeReadOnly();
                    PMDesigner.connectValidator.bpmnValidator();
                });

            }
        }

    }

};
/**
 * Imports a Diagram if this is a valid .bpmn file
 * @param data
 */

PMProject.prototype.importDiagram = function (data) {
    this.isSave = true;
    PMDesigner.moddle.fromXML(data, function (err, definitions) {
        if (err) {
            PMDesigner.msgFlash('Import Error: '.translate() + err.message, document.body, 'error', 5000, 5);
        } else {
            PMDesigner.definitions = definitions;
            var imp = new importBpmnDiagram(definitions);
            if (PMDesigner.project.XMLSupported) {
                PMDesigner.businessObject = definitions;
                imp.completeImportFlows();
                PMUI.getActiveCanvas().buildingDiagram = false;
                PMDesigner.project.setDirty(true);
                PMDesigner.project.save(false);
                PMUI.getActiveCanvas().hideAllFocusLabels();
                PMDesigner.project.setXMLSupported(true);
            } else {
                PMDesigner.msgFlash('The process definition that you are trying to import contains BPMN elements that are not supported in ProcessMaker. Please try with other process.'.translate(), document.body, 'error', 5000, 5);
            }
        }
    });
}

/**
 * Represents a flag if the project was saved or not
 */
PMProject.prototype.isDirty = function () {
    return this.dirty;
};
/**
 *  Saves old bpmn project
 * @param options
 * @returns {PMProject}
 */
PMProject.prototype.save = function (options) {
    var keys = this.getKeysClient(),
        that = this;
    if (this.isDirty()) {
        $.ajax({
            url: that.remoteProxy.url,
            type: "PUT",
            contentType: "application/json",
            data: JSON.stringify(that.getDirtyObject()),
            beforeSend: function (xhr, settings) {
                xhr.setRequestHeader("Authorization", "Bearer " + keys.access_token);
                xhr.setRequestHeader("Accept-Language", LANG);
            },
            success: function (data, textStatus, xhr) {
                that.listeners.success(that, textStatus, data);

                that.isSave = false;
            },
            error: function (xhr, textStatus, errorThrown) {
                if (xhr.status == 401 && typeof(xhr.responseJSON.error) != "undefined") {
                    //Refresh Token
                    $.ajax({
                        url: HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/token",
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(
                            {
                                grant_type: "refresh_token",
                                client_id: that.keys.client_id,
                                client_secret: that.keys.client_secret,
                                refresh_token: that.keys.refresh_token
                            }
                        ),
                        success: function (data, textStatus, xhr) {
                            that.keys.access_token = xhr.responseJSON.access_token;
                            that.keys.expires_in = xhr.responseJSON.expires_in;
                            that.keys.token_type = xhr.responseJSON.token_type;
                            that.keys.scope = xhr.responseJSON.scope;
                            that.keys.refresh_token = xhr.responseJSON.refresh_token;

                            that.save(true);
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            that.listeners.failure(that, textStatus, xhr);

                            that.isSave = false;
                        }
                    });
                } else {
                    that.listeners.failure(that, textStatus, xhr);

                    that.isSave = false;
                }
            }
        });
    }
    return this;
};
PMProject.prototype.verifyAndSave = function () {
    var i,
        max,
        canvas,
        isDragging;
    this.isSave = true;
    this.isSave = true;
    for (i = 0, max = this.diagrams.getSize(); i < max; i += 1) {
        canvas = this.diagrams.get(i);
        if (canvas.isDragging) {
            isDragging = canvas.isDragging;
            break;
        }
    }
    if (!isDragging) {
        this.save(true);
    }
    return this;
};
PMProject.prototype.saveClose = function (options) {
    var keys = this.getKeysClient(),
        that = this;
    if (this.isDirty()) {
        $.ajax({
            url: that.remoteProxy.url,
            type: 'PUT',
            data: JSON.stringify(that.getDirtyObject()),
            contentType: "application/json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + keys.access_token);
                xhr.setRequestHeader("Accept-Language", LANG);
            },
            success: function (data, textStatus) {
                var message_window,
                    browser = PMDesigner.getBrowser();
                url = parent.location.href;
                that.listeners.success(that, textStatus, data);
                that.isSave = false;
                if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                    window.close();
                } else {
                    parent.location.href = url;
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                that.listeners.failure(that, textStatus, xhr);
                that.isSave = false;
            }
        });
    }
    return this;
};

PMProject.prototype.getDirtyObject = function () {
    var that = this,
        diaArray = [],
        shape,
        isGridEnabled = false,
        diagram,
        lastDiagram;

    lastDiagram = this.diagrams.getSize() - 1;
    diagram = this.diagrams.get(lastDiagram);
    shape = this.getDataObject(diagram);
    diaArray.push({
        dia_uid: that.diagramId || PMUI.generateUniqueId(),
        pro_uid: that.id,
        laneset: shape.laneset,
        lanes: shape.lanes,
        activities: shape.activities,
        events: shape.events,
        gateways: shape.gateways,
        flows: shape.flows,
        artifacts: shape.artifacts,
        data: shape.data,
        participants: shape.participants
    });
    isGridEnabled = PMUI.getActiveCanvas().isGridLine &&  PMUI.getActiveCanvas().isGridLine? true: false;
    return {
        prj_uid: that.id,
        prj_name: that.projectName,
        prj_description: that.description,
        usr_setting_designer: {enabled_grid : isGridEnabled},
        diagrams: diaArray
    };
};

PMProject.prototype.getDataObject = function (canvas) {
    var object, i, elements, shapes;
    elements = canvas.items.asArray();
    shapes = {
        activities: [],
        gateways: [],
        events: [],
        flows: [],
        artifacts: [],
        laneset: [],
        lanes: [],
        data: [],
        participants: [],
        pools: []
    };
    if (canvas.items.getSize() > 0) {
        for (i = 0; i < elements.length; i += 1) {
            if (typeof elements[i].relatedObject.getDataObject() === "undefined") {
                object = elements[i].relatedObject;
            } else {
                object = elements[i].relatedObject.getDataObject();
            }
            switch (elements[i].type) {
                case "PMActivity":
                    shapes.activities.push(object);
                    break;
                case "PMGateway":
                    shapes.gateways.push(object);
                    break;
                case "PMEvent":
                    shapes.events.push(object);
                    break;
                case "PMFlow":
                case "Connection":
                    shapes.flows.push(object);
                    break;
                case "PMArtifact":
                    shapes.artifacts.push(object);
                    break;
                case "PMData":
                    shapes.data.push(object);
                    break;
                case "PMParticipant":
                    shapes.participants.push(object);
                    break;
                case "PMPool":
                    shapes.laneset.push(object);
                    break;
                case "PMLane":
                    shapes.lanes.push(object);
                    break;
            }
        }
    }
    return shapes;
};
PMProject.prototype.setDirty = function (dirty) {
    if (typeof dirty === "boolean") {
        this.dirty = dirty;
    }
    return this;
};

PMProject.prototype.addElement = function (element) {
    var object,
        pk_name,
        list,
        i,
        pasteElement,
        elementUndo,
        sh,
        contDivergent = 0,
        contConvergent = 0;
    if (element.relatedElements.length > 0) {
        for (i = element.relatedElements.length - 1; i >= 0; i -= 1) {
            pasteElement = element.relatedElements[i];
            list = this.getUpdateList(pasteElement.type);
            if (list === undefined) {
                return;
            }

            list[pasteElement.id] = object;
            elementUndo = {
                id: pasteElement.id,
                relatedElements: [],
                relatedObject: pasteElement,
                type: pasteElement.type || pasteElement.extendedType
            };
            PMUI.getActiveCanvas().items.insert(elementUndo);
            if (!(pasteElement instanceof PMUI.draw.MultipleSelectionContainer)
                && !(pasteElement instanceof PMLine)
                && !(pasteElement instanceof PMLabel)) {
                pasteElement.createBpmn(pasteElement.getBpmnElementType());
            }
        }
    } else {
        switch (element.type) {
            case "Connection":
                pk_name = this.formatProperty(element.type, 'uid');
                list = this.getUpdateList(element.type);
                element.relatedObject[pk_name] = element.id;

                if (typeof element.relatedObject.getDataObject === "undefined") {
                    object = element.relatedObject;
                }
                list[element.id] = object;
                break;
            default:
                pk_name = this.formatProperty(element.type, 'uid');
                list = this.getUpdateList(element.type);
                element.relatedObject[pk_name] = element.id;
                list[element.id] = object;
                break;
        }
        PMUI.getActiveCanvas().items.insert(element);

        var shape = element.relatedObject;
        if (!(shape instanceof PMUI.draw.MultipleSelectionContainer)
            && !(shape instanceof PMLine)
            && !(shape instanceof PMLabel)) {
            shape.createBpmn(shape.getBpmnElementType());
        }
    }
    this.dirty = true;
    //Call to Create callBack
    this.listeners.create(this, element);
    if (!this.loadingProcess) {
        PMDesigner.connectValidator.bpmnValidator();
    }
};

PMProject.prototype.updateElement = function (updateElement) {
    var element,
        i,
        shape,
        object,
        list,
        item;
    for (i = 0; i < updateElement.length; i += 1) {
        element = updateElement[i];
        shape = element.relatedObject;
        object = this.formatObject(element);
        list = this.getUpdateList(element.type);
        if (list[element.id]) {
            jQuery.extend(true, list[element.id], object);
            if (element.type === 'Connection') {
                list[element.id].flo_state = object.flo_state;
                item = PMUI.getActiveCanvas().items.find("id", element.id);
                item.relatedObject.flo_state = object.flo_state;
            }
        } else {
            list[element.id] = object;
        }
        if (shape) {
            if (shape instanceof PMUI.draw.Port) {
                shape.connection.updateBpmn();
            } else {
                if (!(shape instanceof PMUI.draw.MultipleSelectionContainer)
                    && !(shape instanceof PMLine)
                    && !(shape instanceof PMLabel)) {
                    shape.updateBpmn();
                }
            }
        }
    }
    this.dirty = true;
    //Call to Update callBack
    this.listeners.update(this, updateElement);
    //run the process validator only when the project has been loaded
    if(!this.loadingProcess){
        PMDesigner.connectValidator.bpmnValidator();
    }
};


PMProject.prototype.removeElement = function (updateElement) {
    var object,
        dirtyEmptyCounter,
        element,
        i,
        pk_name,
        list,
        emptyObject = {},
        currentItem;

    for (i = 0; i < updateElement.length; i += 1) {
        element = updateElement[i];
        currentItem = PMUI.getActiveCanvas().items.find("id", updateElement[i].id);
        PMUI.getActiveCanvas().items.remove(currentItem);

        list = this.getUpdateList(element.type);
        if (list) {
            pk_name = this.formatProperty(element.type, 'uid');
            if (list[element.id]) {
                delete list[element.id];
            } else {
                pk_name = this.formatProperty(element.type, 'uid');
                object = {};
                object[pk_name] = element.id;
                list[element.id] = object;
            }
        }
        // to remove BpmnModdle in de exported xml
        if (!(element instanceof PMUI.draw.MultipleSelectionContainer)
            && !(element instanceof PMLine)
            && !(element instanceof PMLabel)) {
            element.removeBpmn();
            if (element.atachedDiagram) {
                this.removeAttachedDiagram(element);
            }
        }

    }

    if (!this.isWaitingResponse()) {
        dirtyEmptyCounter = true;
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].activities === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].gateways === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].events === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].artifacts === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].flows === emptyObject);
        if (dirtyEmptyCounter) {
            this.dirty = false;
        }
    }
    this.dirty = true;
    //Call to Remove callBack
    this.listeners.remove(this, updateElement);
    PMDesigner.connectValidator.bpmnValidator();
};

PMProject.prototype.formatProperty = function (type, property) {
    var prefixes = {
            "PMActivity": "act",
            "PMGateway": "gat",
            "PMEvent": "evn",
            "PMArtifact": "art",
            "PMData": "dat",
            "PMParticipant": "par",
            "PMPool": "swl",
            "PMLane": "lan"
        },
        map = {
            x: "bou_x",
            y: "bou_y",
            width: "bou_width",
            height: "bou_height"
        },
        out;

    if (type === "PMFlow" || type === 'Connection') {
        out = "flo_" + property;
    } else if (map[property]) {
        out = map[property];
    } else {
        out = prefixes[type] + '_' + property;
    }
    return out;
};
PMProject.prototype.getUpdateList = function (type) {
    var listName = {
            "PMActivity": "activities",
            "PMGateway": "gateways",
            "PMEvent": "events",
            "PMFlow": "flows",
            "PMArtifact": "artifacts",
            "PMLabel": "artifacts",
            "Connection": "flows",
            "PMData": "data",
            "PMParticipant": "participants",
            "PMPool": "laneset",
            "PMLane": "lanes"
        },
        dirtyArray;
    dirtyArray = (this.isWaitingResponse()) ? 1 : 0;
    return this.dirtyElements[dirtyArray][listName[type]];
};

/**
 * Represents if the proxy is waiting any response from the server
 */
PMProject.prototype.isWaitingResponse = function () {
    return this.waitingResponse;
};

PMProject.prototype.updateIdentifiers = function (response) {
    var i, shape, that = this, connection, shapeCanvas;
    if (typeof response === "object") {
        for (i = 0; i < response.length; i += 1) {
            shape = PMUI.getActiveCanvas().items.find("id", response[i].old_uid);
            shapeCanvas = PMUI.getActiveCanvas().children.find("id", response[i].old_uid);
            connection = PMUI.getActiveCanvas().connections.find("flo_uid", response[i].old_uid);
            this.identifiers[response[i].old_uid] = response[i].new_uid;
            if (shape) {
                shape.id = response[i].new_uid;

                shape.relatedObject.id = response[i].new_uid;
                shape.relatedObject.html.id = response[i].new_uid;
                switch (shape.type) {
                    case "Connection":
                        shape.relatedObject.flo_uid = response[i].new_uid;
                        break;
                    case "PMActivity":
                        shape.relatedObject.act_uid = response[i].new_uid;
                        break;
                    case "PMEvent":
                        shape.relatedObject.evn_uid = response[i].new_uid;
                        break;
                    case "PMGateway":
                        shape.relatedObject.gat_uid = response[i].new_uid;
                        break;
                    case "PMArtifact":
                        shape.relatedObject.art_uid = response[i].new_uid;
                        break;
                    case "PMData":
                        shape.relatedObject.dat_uid = response[i].new_uid;
                        break;
                    case "PMParticipant":
                        shape.relatedObject.par_uid = response[i].new_uid;
                        break;
                    case "PMPool":
                        shape.relatedObject.lns_uid = response[i].new_uid;
                        shape.relatedObject.participantObject.id = 'el_' + response[i].new_uid;
                        break;
                    case "PMLane":
                        shape.relatedObject.lan_uid = response[i].new_uid;
                        break;
                }
            }
            if (shapeCanvas) {
                shapeCanvas.id = response[i].new_uid;
            }
            if (connection) {
                connection.flo_uid = response[i].new_uid;
                connection.id = response[i].new_uid;
            }
        }
    }
};

PMProject.prototype.formatObject = function (element) {
    var i,
        field,
        formattedElement = {},
        property;
    formattedElement[this.formatProperty(element.type, 'uid')] = element.id;

    if (element.adam) {
        for (i = 0; i < element.fields.length; i += 1) {
            field = element.fields[i];
            formattedElement[field.field] = field.newVal;
        }
    } else if (element.fields) {
        for (i = 0; i < element.fields.length; i += 1) {
            field = element.fields[i];
            property = this.formatProperty(element.type, field.field);
            if (property === "element_uid") {
                field.newVal = field.newVal.id;
            }
            formattedElement[property] = field.newVal;
        }
    }
    return formattedElement;
};

PMProject.prototype.subProcessDiagram = function (element) {
    var sidebarCanvas = [], opt = {name: element.act_name}, s, newCanvas, di;
    PMUI.getActiveCanvas().getHTML().style.display = 'none';
    if (!element.atachedDiagram) {
        for (s = 0; s < PMDesigner.sidebar.length; s += 1) {
            sidebarCanvas = sidebarCanvas.concat(PMDesigner.sidebar[s].getSelectors());
            jQuery(".bpmn_shapes").append(PMDesigner.sidebar[s].getHTML());
        }

        sidebarCanvas.splice(17, 1);
        sidebarCanvas.splice(5, 1);
        sidebarCanvas = sidebarCanvas.concat('.pmui-pmevent');
        sidebarCanvas = sidebarCanvas.concat('.pmui-pmactivity');
        sidebarCanvas = sidebarCanvas.concat('.pmui-pmgateway');
        sidebarCanvas = sidebarCanvas.concat('.pmui-pmdata');
        sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-annotation');
        sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-group');
        sidebarCanvas = sidebarCanvas.concat('.mafe-pool');
        sidebarCanvas = sidebarCanvas.concat('.mafe_participant');
        newCanvas = this.buildCanvas(sidebarCanvas, opt);
        PMUI.setActiveCanvas(newCanvas);
        jQuery("#p-center-layout").scroll(newCanvas.onScroll(newCanvas, jQuery("#p-center-layout")));
        newCanvas.getHTML().style.display = 'inline';
        element.atachedDiagram = newCanvas;
        PMDesigner.canvasList.setValue(newCanvas.getID());

        di = newCanvas.createBPMNDiagram();
        newCanvas.businessObject = element.businessObject;
        di.bpmnElement = element.businessObject; //update reference
        newCanvas.businessObject.di = di;
    } else {
        newCanvas = element.atachedDiagram;
        PMUI.setActiveCanvas(newCanvas);
        newCanvas.getHTML().style.display = 'inline';
        PMDesigner.canvasList.setValue(newCanvas.getID());
    }

};

PMProject.prototype.removeAttachedDiagram = function (element) {
    var canvas = element.atachedDiagram;
    this.diagrams.remove(canvas);
    if (canvas.html !== undefined) {
        jQuery(canvas.html).remove();
        canvas.html = null;
    }
    element.atachedDiagram = null;
    PMDesigner.canvasList.removeOption(canvas.getID());
};
PMProject.prototype.setTokens = function (response) {
    this.tokens = response;
    return this;
};

PMProject.prototype.setSaveButtonDisabled = function () {
    if (this.isDirty()) {
        if (document.getElementsByClassName("mafe-save-process").length > 0) {
            document.getElementsByClassName("mafe-save-process")[0].removeAttribute("style");
            document.getElementsByClassName("mafe-save-process")[0].childNodes[0].style.color = "#FFF";

            var mafebuttonMenu = document.getElementsByClassName("mafe-button-menu")[0];
            mafebuttonMenu.style.backgroundColor = "#0C9778";
            mafebuttonMenu.firstChild.src = "/lib/img/caret-down-w.png";
        }
    } else {
        if (document.getElementsByClassName("mafe-save-process").length > 0) {
            document.getElementsByClassName("mafe-save-process")[0].style.backgroundColor = "#e8e8e8";
            document.getElementsByClassName("mafe-save-process")[0].style.color = "#000";
            document.getElementsByClassName("mafe-save-process")[0].childNodes[0].style.color = "#000";
            document.getElementsByClassName("mafe-save-process")[0].childNodes[0].text = "Save".translate();

            var mafebuttonMenu = document.getElementsByClassName("mafe-button-menu")[0];
            mafebuttonMenu.style.backgroundColor = "#e8e8e8";
            mafebuttonMenu.firstChild.src = "/lib/img/caret-down.png";
        }
    }
};

