//var BpmnTreeWalker = require('./BpmnTreeWalker');


/**
 * Import the definitions into a diagram.
 *
 * Errors and warnings are reported through the specified callback.
 *
 * @param  {Diagram} diagram
 * @param  {ModdleElement} definitions
 * @param  {Function} done the callback, invoked with (err, [ warning ]) once the import is done
 */
var importBpmnDiagram = function (definitions) {
    var error;
    this.participants = [];
    this.lanes = [];
    this.laneRelPosition = 0;
    this.flowsArray = new PMUI.util.ArrayList();
    this.headerHeight = 0;
    this.eventMarkerMap = {
        'CATCH': {
            'bpmn:MessageEventDefinition': 'MESSAGECATCH',
            'bpmn:TimerEventDefinition': 'TIMER',
            'bpmn:ConditionalEventDefinition': 'CONDITIONAL',
            'bpmn:LinkEventDefinition': 'LINKCATCH',
            'bpmn:SignalEventDefinition': 'SIGNALCATCH',
            'bpmn:ErrorEventDefinition': 'ERRORCATCH',            
            'bpmn:CompensateEventDefinition': 'COMPENSATIONTHROW'
        },
        'THROW': {
            'bpmn:MessageEventDefinition': 'MESSAGETHROW',
            'bpmn:TimerEventDefinition': 'TIMER',
            'bpmn:ConditionalEventDefinition': 'CONDITIONAL',
            'bpmn:CompensateEventDefinition': 'COMPENSATIONTHROW',
            'bpmn:SignalEventDefinition': 'SIGNALTHROW',
            'bpmn:ErrorEventDefinition': 'ERRORTHROW',
            'bpmn:TerminateEventDefinition':'TERMINATETHROW'
        }

    };
    this.taskType = {
        'bpmn:Task': 'EMPTY',
        'bpmn:SendTask': 'SENDTASK',
        'bpmn:ReceiveTask': 'RECEIVETASK',
        'bpmn:UserTask': 'USERTASK',
        'bpmn:ServiceTask': 'SERVICETASK',
        'bpmn:ScriptTask': 'SCRIPTTASK',
        'bpmn:ManualTask': 'MANUALTASK',
        'bpmn:BusinessRuleTask': 'BUSINESSRULE'
    };

    this.loopType = {
        'bpmn:StandardLoopCharacteristics': 'LOOP',
        'bpmn:MultiInstanceLoopCharacteristics':'PARALLEL'        
    };

    this.dataObjectType = {
        'bpmn:DataInput': 'datainput',
        'bpmn:DataOutput':'dataoutput',
        'bpmn:DataObject':'dataobject'                  
    };

    //eventBus.fire('import.start');
    try {
        PMDesigner.businessObject = definitions;
        this.checkXML(definitions);
        if(PMDesigner.project.XMLSupported){
            diRefs = null;
            diRefs = new Refs({ name: 'bpmnElement', enumerable: true }, { name: 'di' });
            this.parse(definitions);    
        }
    } catch (e) {
        error = e;
        PMDesigner.project.setXMLSupported(false);
    }
};
importBpmnDiagram.prototype.parse = function (definitions) {
    var self =  this, sidebarCanvas = [], i, visitor;

    visitor = {

        root: function(element) {
            var businessObject = {},
                canvas,
                project;

            if (element.$type === 'bpmn:Collaboration') {
                // TODO IF THERE IS COLLABORATIONS
                return self.addParticipant(element);
            } else {
                businessObject.elem = element;
                businessObject.di = element.di;
                for (i = 0; i < PMDesigner.sidebar.length; i += 1) {
                    sidebarCanvas = sidebarCanvas.concat(PMDesigner.sidebar[i].getSelectors());
                    jQuery(".bpmn_shapes").append(PMDesigner.sidebar[i].getHTML());
                }
                sidebarCanvas.splice(15, 1);  //to remove lane selector
                //Remove Lasso and Validator
                sidebarCanvas.splice(17, 2);
                // sidebarCanvas = sidebarCanvas + ',.mafe-event-start';
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmevent');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmactivity');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmgateway');
                sidebarCanvas = sidebarCanvas.concat('.pmui-pmdata');
                sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-annotation');


                canvas =  PMDesigner.project.buildCanvas(sidebarCanvas, {name: element.$parent.name});
                PMUI.setActiveCanvas(canvas);
                project = PMUI.getActiveCanvas().project;
                project && project.userSettings && project.userSettings.enabled_grid
                    ? canvas.enableGridLine() : canvas.disableGridLine();

                jQuery("#p-center-layout").scroll(canvas.onScroll(canvas, jQuery("#p-center-layout")));
                PMDesigner.canvasList.setValue(canvas.getID());
                element.id = canvas.getID();

                canvas.businessObject = businessObject;

            }
        },

        element: function(element, parentShape) {

            return self.addElement(element, parentShape);

        },
        error: function(message, context) {            
            //warnings.push({ message: message, context: context });
        }
    };

    var walker = new BpmnTreeWalker(visitor);

    // import
    walker.handleDefinitions(definitions);
};

importBpmnDiagram.prototype.checkXML = function (definitions) {
    var self =  this, 
        visitor,
        walker;

    visitor = {
        root: function(element) {
           
        },
        element: function(element, parentShape) {
            return (self.checkElement(element, parentShape));            
        },
        error: function(message, context) {
            console.log('add error');            
        }
    };

    var walker = new BpmnTreeWalker(visitor);

    // import
    walker.handleDefinitions(definitions);
};

/**
 *  Adds bpmn participants, that arrives from a .bpmn file as a collaboration node
 * @param element
 * @returns {importBpmnDiagram}
 */
importBpmnDiagram.prototype.addParticipant = function (collaboration) {
    var i,
        j,
        participants,
        participant,
        sidebarCanvas = [],
        canvas,
        rootElements,
        element,
        tempIndex = -1,
        isParticipant,
        project;

    participants = collaboration.participants;
    for (j = 0; j < PMDesigner.sidebar.length; j += 1) {
        sidebarCanvas = sidebarCanvas.concat(PMDesigner.sidebar[j].getSelectors());
        jQuery(".bpmn_shapes").append(PMDesigner.sidebar[j].getHTML());
    }

    sidebarCanvas.splice(15, 1);  //to remove lane selector
    //Remove Lasso and Validator
    sidebarCanvas.splice(17, 2);
    sidebarCanvas = sidebarCanvas.concat('.pmui-pmevent');
    sidebarCanvas = sidebarCanvas.concat('.pmui-pmactivity');
    sidebarCanvas = sidebarCanvas.concat('.pmui-pmgateway');
    sidebarCanvas = sidebarCanvas.concat('.pmui-pmdata');
    sidebarCanvas = sidebarCanvas.concat('.mafe-artifact-annotation');
    canvas =  PMDesigner.project.buildCanvas(sidebarCanvas, {name:  collaboration.$parent.name});
    PMUI.setActiveCanvas(canvas);
    project = PMUI.getActiveCanvas().project;
    project && project.userSettings && project.userSettings.enabled_grid
        ? canvas.enableGridLine() : canvas.disableGridLine();
    PMDesigner.canvasList.setValue(canvas.getID());

    jQuery("#p-center-layout").scroll(canvas.onScroll(canvas, jQuery("#p-center-layout")));

    rootElements = PMDesigner.definitions.rootElements;
    for (i = 0; i < participants.length; i += 1) {
        participant = participants[i];
        isParticipant = true;
        for (j = 0; j < rootElements.length; j += 1) {
            element = rootElements[j];

            if (element.$type === 'bpmn:Process' && participant.processRef && participant.processRef.id !== element.id) {
                tempIndex = j;
                isParticipant = false;
                break;
            }
        }
        if( typeof participant.processRef !== 'undefined') {
            this.participants.push(participant);
            canvas.businessObject.elem = participant.processRef;
        }
        if (canvas.businessObject && participant.$parent.di) {
            canvas.businessObject.di = participant.$parent.di;
        }
        canvas.buildingDiagram = true;
    }
    if (tempIndex > 0 ) {
        canvas.businessObject.elem = rootElements[tempIndex];
    }
    return this;
};

importBpmnDiagram.prototype.getLoopCharacteristics = function (element) {    
    var loopType;
    if(element.loopCharacteristics){
        loopType = this.loopType[element.loopCharacteristics.$type] === undefined ? "EMPTY": this.loopType[element.loopCharacteristics.$type];
        if(element.loopCharacteristics.isSequential){
            loopType = "SEQUENTIAL";
        }
    }
    return loopType;    
};

importBpmnDiagram.prototype.getOwnProperties = function (element) {
    var ownProp = false,
        parent,
        taskType,
        loopType,
        marker,
        behavior,
        type,
        typeMap = {
            'bpmn:Lane': 'LANE',
            'bpmn:Participant': 'POOL',
            'bpmn:StartEvent': 'START',
            'bpmn:EndEvent': 'END',
            'bpmn:BoundaryEvent': 'BOUNDARY',
            'bpmn:Task': 'TASK',
            'bpmn:SendTask': 'TASK',
            'bpmn:ReceiveTask': 'TASK',
            'bpmn:UserTask': 'TASK',
            'bpmn:ServiceTask': 'TASK',
            'bpmn:ScriptTask': 'TASK',
            'bpmn:ManualTask': 'TASK',
            'bpmn:CallActivity': 'TASK',
            'bpmn:BusinessRuleTask': 'TASK',
            'bpmn:SubProcess': 'SUB_PROCESS',
            'bpmn:ExclusiveGateway': 'EXCLUSIVE',
            'bpmn:ParallelGateway': 'PARALLEL',
            'bpmn:InclusiveGateway': 'INCLUSIVE',
            //'bpmn:EventBasedGateway': 'EVENTBASED',
            'bpmn:IntermediateCatchEvent': 'INTERMEDIATE',
            'bpmn:IntermediateThrowEvent': 'INTERMEDIATE',
            'bpmn:TextAnnotation': 'TEXT_ANNOTATION',
            'bpmn:DataStoreReference': 'DATASTORE',
            'bpmn:DataObjectReference': 'DATAOBJECT',
            'bpmn:Group': 'GROUP',
            'bpmn:DataInput': 'DATAINPUT',
            'bpmn:DataOutput': 'DATAOUTPUT'
            
        };
    type = typeMap[element.$type];
    switch (type) {
    case 'START':
        if (element.eventDefinitions && element.eventDefinitions[0]) {
            marker = this.eventMarkerMap['CATCH'][element.eventDefinitions[0].$type];
        }
        ownProp = {
            'evn_name': element.name,
            'evn_marker': marker ? marker : 'EMPTY'
        };
        break;
    case 'END':
        if (element.eventDefinitions && element.eventDefinitions[0]) {
            marker = this.eventMarkerMap['THROW'][element.eventDefinitions[0].$type];
        }

        ownProp = {
            'evn_name': element.name,
            'evn_marker': marker ? marker : 'EMPTY'
        };
        
        break;
    case 'INTERMEDIATE':
        behavior = (element.$type === 'bpmn:IntermediateCatchEvent') ? 'CATCH' : 'THROW';        
        if (element.eventDefinitions && element.eventDefinitions[0]) {
            marker = this.eventMarkerMap[behavior][element.eventDefinitions[0].$type];
        }        
        //marker = this.eventMarkerMap[behavior][element.eventDefinitions[0].$type];
        ownProp = {
            'evn_behavior': behavior,
            'evn_marker': marker ? marker : 'MESSAGECATCH',
            'evn_name': element.name
        };
        
        break;
    case 'BOUNDARY':
        if (element.eventDefinitions && element.eventDefinitions[0]) {
            marker = this.eventMarkerMap['CATCH'][element.eventDefinitions[0].$type];
        }
        if(typeof marker !== "undefined" && this.validateElementsForDesigner(element.$type,marker)){
            ownProp = {
                'evn_behavior': behavior,
                'evn_marker': marker ? marker : 'EMPTY',
                'evn_name': element.name,
                'bou_container': 'bpmnActivity',
                'bou_element': element.attachedToRef.id
            };
        }else{
             type = undefined;
        }
        break;
    case 'SUB_PROCESS':
        ownProp = {
            'act_name': element.name,
            'act_type': 'SUB_PROCESS'
        };
        break;
    case 'TASK':
        taskType = this.taskType[element.$type];
        loopType = this.getLoopCharacteristics(element);          
        ownProp = {
            'act_name': element.name,
            'act_task_type': taskType,
            'act_loop_type' : loopType
        };
        break;
    case 'EXCLUSIVE':
        ownProp = {
            'gat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'gat_name': element.name,
            'gat_type': 'EXCLUSIVE'
        };
        break;
    case 'PARALLEL':
        ownProp = {
            'gat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'gat_name': element.name,
            'gat_type': 'PARALLEL'
        };
        break;
    case 'INCLUSIVE':
        ownProp = {
            'gat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'gat_name': element.name,
            'gat_type': 'INCLUSIVE'
        };
        break;
    case 'EVENTBASED':
        ownProp = {
            'gat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'gat_name': element.name,
            'gat_type': 'EVENTBASED'
        };
        break;
    case 'POOL':
        ownProp = {
            'par_uid': 'pmui-' + PMUI.generateUniqueId(),
            'par_name': element.name
        };
        if (typeof element.processRef === 'undefined') {
            type = 'PARTICIPANT';
        }

        break;
    case 'LANE':
        this.lanes.push(element);
        parent = this.getParentData(element);
        this.laneRelPosition += 1;
        ownProp = {
            'lan_uid': 'pmui-' + PMUI.generateUniqueId(),
            'lan_name': element.name ? element.name : ' ',
            'bou_container': 'bpmnPool',
            'bou_element': parent.parentUid,
            'bou_rel_position': this.laneRelPosition,
            'bou_x': 40,
            'bou_y': element.di.bounds.y - parent.y
        };
        break;
    case 'GROUP':
    case 'TEXT_ANNOTATION':
        ownProp = {
            'art_uid': 'pmui-' + PMUI.generateUniqueId(),
            'art_name': element.text
        };
        break;
    case 'DATASTORE':
    case 'DATAOBJECT':
        ownProp = {
            'dat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'dat_name': element.name,
            'dat_object_type':this.dataObjectType[element.$type]?this.dataObjectType[element.$type]:'dataobject'
        };
        break;
    case 'DATAINPUT':
        ownProp = {
            'dat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'dat_name': element.name,
            'dat_object_type':this.dataObjectType[element.$type]?this.dataObjectType[element.$type]:'dataobject'
        };
        break;
    case 'DATAOUTPUT':
        ownProp = {
            'dat_uid': 'pmui-' + PMUI.generateUniqueId(),
            'dat_name': element.name,
            'dat_object_type':this.dataObjectType[element.$type]?this.dataObjectType[element.$type]:'dataobject'
        };
        break;            
    }
    return {'properties': ownProp, 'type': type};
};

importBpmnDiagram.prototype.checkElement = function (element) {
    var validation,
        type,
        marker,
        loop,
        parameters;

    parameters = {
        'bpmn:StartEvent':{
            "parallelMultiple":false
        }
    };

    loopTypes= {
        'bpmn:StandardLoopCharacteristics':true,
        'bpmn:MultiInstanceLoopCharacteristics':true,
        'empty':true
    };     

    validation ={
        'bpmn:Lane': true,
        'bpmn:ChoreographyTask':true,
        'bpmn:Participant': true,
        'bpmn:Task': true,
        'bpmn:SendTask': true,
        'bpmn:ReceiveTask': true,
        'bpmn:UserTask': true,
        'bpmn:ServiceTask': true,
        'bpmn:ScriptTask': true,
        'bpmn:ManualTask': true,
        'bpmn:CallActivity': false,
        'bpmn:BusinessRuleTask': true,          
                
        'bpmn:SubProcess': true,
        'bpmn:AdHocSubProcess':false,
        'bpmn:Transaction':false,        
        'bpmn:ExclusiveGateway': true,
        'bpmn:ParallelGateway': true,
        'bpmn:InclusiveGateway': true,
        'bpmn:EventBasedGateway': false,
            //'bpmn:IntermediateCatchEvent': 'INTERMEDIATE',
            //'bpmn:IntermediateThrowEvent': 'INTERMEDIATE',
        'bpmn:TextAnnotation': true,
        'bpmn:DataStoreReference': true,
        'bpmn:DataObjectReference': true,
        'bpmn:DataInput': true,
        'bpmn:DataOutput': true,        
        'bpmn:Group': true,

        'bpmn:SequenceFlow': true,
        'bpmn:Association': true,
        'bpmn:DataOutputAssociation': true,
        'bpmn:DataInputAssociation': true,
        'bpmn:MessageFlow': true,

        'bpmn:StartEvent':{            
            'bpmn:MessageEventDefinition': true,
            'bpmn:TimerEventDefinition': true,
            'bpmn:ConditionalEventDefinition': true,
            'bpmn:CompensateEventDefinition': false,
            'bpmn:SignalEventDefinition': true,
            'bpmn:ErrorEventDefinition': false,
            'bpmn:TerminateEventDefinition':false,
            'bpmn:MultipleEventDefinition':false,
            'bpmn:EscalationEventDefinition':false,
            'empty':true        
        },

        'bpmn:EndEvent':{            
            'bpmn:MessageEventDefinition': true,
            'bpmn:TimerEventDefinition': false,
            'bpmn:ConditionalEventDefinition': false,
            'bpmn:CompensateEventDefinition': false,
            'bpmn:SignalEventDefinition': true,
            'bpmn:ErrorEventDefinition': true,
            'bpmn:TerminateEventDefinition':true,
            'bpmn:MultipleEventDefinition':false,
            'bpmn:EscalationEventDefinition':false,
            'bpmn:CancelEventDefinition':false,            
            'empty':true 
        },

        'bpmn:IntermediateCatchEvent':{
            'bpmn:MessageEventDefinition': true,
            'bpmn:TimerEventDefinition': true,
            'bpmn:ConditionalEventDefinition': true,
            'bpmn:CompensateEventDefinition': false,
            'bpmn:SignalEventDefinition': true,
            'bpmn:ErrorEventDefinition': false,
            'bpmn:TerminateEventDefinition':false,
            'bpmn:MultipleEventDefinition':false,
            'bpmn:EscalationEventDefinition':false,        
            'bpmn:LinkEventDefinition':false,
            'empty':false 
        },
        
        'bpmn:IntermediateThrowEvent':{            
            'bpmn:MessageEventDefinition': true,
            'bpmn:TimerEventDefinition': false,
            'bpmn:ConditionalEventDefinition': false,
            'bpmn:CompensateEventDefinition': false,
            'bpmn:SignalEventDefinition': true,
            'bpmn:ErrorEventDefinition': false,
            'bpmn:TerminateEventDefinition':false,
            'bpmn:MultipleEventDefinition':false,
            'bpmn:EscalationEventDefinition':false,        
            'bpmn:LinkEventDefinition':false,
            'empty':false 
        },
        
        'bpmn:BoundaryEvent':{
            'bpmn:MessageEventDefinition': false,     //true
            'bpmn:TimerEventDefinition': false,       //true
            'bpmn:ConditionalEventDefinition': false, //true
            'bpmn:CompensateEventDefinition': false,
            'bpmn:SignalEventDefinition': false,      //true
            'bpmn:ErrorEventDefinition': false,       //true
            'bpmn:TerminateEventDefinition':false,
            'bpmn:MultipleEventDefinition':false,
            'bpmn:EscalationEventDefinition':false,        
            'bpmn:LinkEventDefinition':false,
            'empty':false
            },        
        };

    type = element.$type;
    //for events
    if (element.eventDefinitions && element.eventDefinitions[0]) {
        marker = element.eventDefinitions[0].$type;
    }else{
        if(element.$type == 'bpmn:StartEvent' ||  element.$type == 'bpmn:IntermediateThrowEvent' || element.$type == 'bpmn:IntermediateCatchEvent' || element.$type == 'bpmn:BoundaryEvent')
            marker = "empty";        
    } 

    //for subprocess
    if (element.loopCharacteristics) {
        if(!loopTypes[element.loopCharacteristics.$type]){
            marker = "unresolved";
        }
    }

    if(marker !== undefined){
        if(this.checkParametersOfElement(element)){
            if(!validation[type][marker]){
                PMDesigner.project.setXMLSupported(false);
            }
        }else{
            PMDesigner.project.setXMLSupported(false);
        }        
    }else{
        if(this.checkParametersOfElement(element)){
            if(!validation[type]){
                PMDesigner.project.setXMLSupported(false);
            }    
        }else{
            PMDesigner.project.setXMLSupported(false);
        }
                   
    }
};

importBpmnDiagram.prototype.checkParametersOfElement= function (element) {
    if(element.$type == 'bpmn:StartEvent'){
        if(element.isInterrupting == false){
            return false;   
        }
        if(element.parallelMultiple == true){
            return false;   
        }
    }
    if(element.$type == 'bpmn:IntermediateThrowEvent' || element.$type == 'bpmn:IntermediateCatchEvent'){
        if(element.isInterrupting == false){
            return false;   
        }
    }
    if(element.$type == 'bpmn:IntermediateThrowEvent' || element.$type == 'bpmn:IntermediateCatchEvent'){
        if(element.isInterrupting == false){
            return false;   
        }
    }
    if(element.$type == 'bpmn:DataObjectReference'){
        if(element.dataObjectRef.isCollection == true){
            return false;   
        }
    }
    if(element.$type == 'bpmn:DataInput'){
        if(element.isCollection == true){
            return false;   
        }
    }
    if(element.$type == 'bpmn:DataOutput'){
        if(element.isCollection == true){
            return false;   
        }
    }               
    
    return true;
};

/**
 * gets parent data
 * @param element
 * @param parentShape
 */
importBpmnDiagram.prototype.getParentData = function (element) {
    var i,
        participant,
        uid,
        canvas = PMUI.getActiveCanvas(),
        pool,
        currentProcess =  element.$parent.$parent;
    for (i = 0; i < this.participants.length; i += 1) {
        participant = this.participants[i];
        if (participant.processRef.id === currentProcess.id) {
            uid = participant.id;
            break;
        }
    }
    pool = canvas.customShapes.find('id', uid);
    return {parentUid: uid, container: pool.extendedType, y: pool.y};
};
importBpmnDiagram.prototype.getContainerShape = function (element) {
    var i,
        j,
        participant,
        uid = null,
        canvas = PMUI.getActiveCanvas(),
        container = null,
        refs,
        currentProcess =  element.$parent;
    if (element.$type !== 'bpmn:TextAnnotation'
        && element.$type !== 'bpmn:DataStoreReference') {
        for (i = 0; i < this.lanes.length; i += 1) {
            refs = this.lanes[i].get('flowNodeRef');
            for (j = 0; j < refs.length; j += 1) {
                if (refs[j].id === element.id) {
                    uid = this.lanes[i].id;
                    break;
                }
            }
        }
        if (!uid) {
            while (currentProcess.$parent.$parent) {
                currentProcess = currentProcess.$parent;
            }
            for (i = 0; i < this.participants.length; i += 1) {
                participant = this.participants[i];
                if (typeof participant.processRef !== 'undefined'
                    && participant.processRef.id === currentProcess.id) {
                    uid = participant.id;
                    break;
                }
            }
        }
        container = canvas.customShapes.find('id', uid);
    } else {
        container = this.getAuxContainer(element);
    }

    return container;
};
importBpmnDiagram.prototype.getAuxContainer = function (element) {
    var  container = null,
        uid = null,
        i,
        x,
        y,
        x1,
        y1,
        x2,
        y2,
        participant,
        canvas = PMUI.getActiveCanvas();
    for (i = 0; i < this.participants.length; i += 1) {
        participant = this.participants[i];
        //console.log(participant);
        x = element.di.bounds.x;

        y = element.di.bounds.y;

        x1 = participant.di.bounds.x;
        x2 = participant.di.bounds.x + participant.di.bounds.width;

        y1 = participant.di.bounds.y;
        y2 = participant.di.bounds.y + participant.di.bounds.height;
        if(x < x2 && x > x1 &&  y < y2 && y > y1) {
            uid = participant.id;
            break;
        }
    }
    if (uid) {
        container = canvas.customShapes.find('id', uid);
    }

    return container;

};
/**
 * import bpmn elements
 * @param element
 * @param parentShape
 */
importBpmnDiagram.prototype.addElement = function (element) {
    var canvas = PMUI.getActiveCanvas(),
        shape,

        x,
        y,
        businessObject = {
            elem: element,
            di: element.di
        },
        bounds = element.di.bounds,
        container,
        bouElement = null,
        bouType = "bpmnDiagram",
        conectionMap = {
            'bpmn:SequenceFlow': 'SEQUENCE',
            'bpmn:Association': 'ASSOCIATION',
            'bpmn:DataOutputAssociation': 'DATAASSOCIATION',
            'bpmn:DataInputAssociation': 'DATAASSOCIATION',
            'bpmn:MessageFlow': 'MESSAGE'
        },
        ownProp;
    //if (PMDesigner.businessObject.id
    //    && PMDesigner.businessObject.id !== 'BPMNProcessmaker'
    //    && bounds) {
    //    //bounds.y += this.headerHeight; // to consider header
    //}
    ownProp = this.getOwnProperties(element);
    if (ownProp.properties) {

        container = this.getContainerShape(element);

        x = bounds.x;
        y = bounds.y;
        // validate for camunda modeler datastore
        if (container) {
            bouElement = container.id;
            bouType = container.type;
            while (container.getType() !== 'PMCanvas') {
                x = x - container.getX();
                y = y - container.getY();
                container = container.parent;
            }
        }
        shape = {
            'bou_container': bouType,
            'bou_element': bouElement,
            'bou_height': bounds.height,
            'bou_width': bounds.width,
            'bou_x': x,
            'bou_y': y

        };
        $.extend(true, shape, ownProp.properties);

        if (element.$type === 'bpmn:Participant') {
            if (typeof element.processRef !== 'undefined') {
                this.laneRelPosition = 0;
                element.processRef.id = 'pmui-' + PMUI.generateUniqueId();
                businessObject.elem = element.processRef;
            }
        }

        canvas.loadShape(ownProp.type, shape, false, businessObject);

        if (element.$type === 'bpmn:Participant') {
            canvas.updatedElement.participantObject = element;
        }
        element.id = canvas.updatedElement.id;

    }
    if (conectionMap[element.$type] &&  !this.flowsArray.find('id', element.id)) {
        //here save a connection element because some elements not has ascending order
        this.flowsArray.insert(element);
    }
};

/**
 * That method must complete all shape connections
 */
importBpmnDiagram.prototype.completeImportFlows = function () {
    var i,
        conn,
        state,
        dest,
        origin,
        canvas = PMUI.getActiveCanvas(),
        conectionMap = {
            'bpmn:SequenceFlow': 'SEQUENCE',
            'bpmn:Association': 'ASSOCIATION',
            'bpmn:DataOutputAssociation': 'DATAASSOCIATION',
            'bpmn:DataInputAssociation': 'DATAASSOCIATION',
            'bpmn:MessageFlow': 'MESSAGE'
        },
        element,
       flowArraySize = this.flowsArray.getSize();

    for (i = 0; i < flowArraySize; i += 1) {
        element = this.flowsArray.get(i);
        if (element.$type === 'bpmn:DataInputAssociation') {
            //dest = element.targetRef ? element.targetRef.id : element.$parent.id;
            dest = element.$parent.id;
            origin = element.sourceRef ? element.sourceRef[0].id : element.$parent.id;
        } else if (element.$type === 'bpmn:DataOutputAssociation') {
            dest = element.targetRef ? element.targetRef.id : element.$parent.id;
            origin = element.$parent.id;
        }else {
            dest = element.targetRef ? element.targetRef.id : element.$parent.id;
            origin = element.sourceRef ? element.sourceRef.id : element.$parent.id;
        }
        state = this.getState(element);
        conn = {
            flo_uid: 'pmui-' + PMUI.generateUniqueId(),
            flo_condition: null,
            flo_element_dest: dest,
            flo_element_origin: origin,
            flo_is_inmediate: "1",
            flo_name: null,
            flo_state: state,
            flo_type: conectionMap[element.$type],
            flo_x1: state[0].x,
            flo_y1: state[0].y,
            flo_x2: state[state.length - 1].x,
            flo_y2: state[state.length - 1].y
        };

        canvas.loadFlow(conn, false);
        canvas.updatedElement = {
            id : (canvas.updatedElement && canvas.updatedElement.id) || null,
            type : (canvas.updatedElement && canvas.updatedElement.type) || null,
            relatedObject : canvas.updatedElement,
            relatedElements: []
        };
        canvas.items.insert(canvas.updatedElement);
        element.id = canvas.updatedElement.id;
        canvas.updatedElement.relatedObject.businessObject = element;
    }
};
/**
 * geting all states in xml file
 * @param element
 */
importBpmnDiagram.prototype.getState = function (element) {
    var state = [],
        waypoint = element.di.waypoint,
        i;
    for (i = 0; i < waypoint.length; i += 1) {
        //if (PMDesigner.businessObject.id && PMDesigner.businessObject.id !== 'BPMNProcessmaker') {
        //    //waypoint[i].y += 80;   //to consider header
        //}
        state.push({x: waypoint[i].x - 1, y: waypoint[i].y});
    }
    return state;
};

/**
 * geting all states in xml file
 * @param element
 */
importBpmnDiagram.prototype.validateElementsForDesigner = function (mapType, marker) {
    var resp = true, 
        validation;
    validation ={           
        'bpmn:EndEvent':{
            'MESSAGETHROW':true,
            'TIMER':false,
            'SCALATIONTHROW':false,
            'COMPENSATIONTHROW':false,
            'SIGNALTHROW':true,
            'ERRORTHROW':true,
            'TERMINATETHROW':true,
            'MULTIPLETHROW':false,
        },
        'bpmn:IntermediateCatchEvent':{
            'MESSAGECATCH': true,
            'TIMER': true,
            'CONDITIONAL':true,
            'LINKCATCH':false,
            'SIGNALCATCH':true,
            'COMPENSATIONTHROW':false,
            'MULTIPLECATCH':false,
            'PARALLELCATCH':false                
        },
        'bpmn:IntermediateThrowEvent':{
            'MESSAGETHROW':true,
            'SIGNALTHROW':true,            
            'SCALATIONTHROW':false,            
            'COMPENSATIONTHROW':false,
            'LINKTHROW':false
        },
        'bpmn:BoundaryEvent':{
            'MESSAGECATCH': true,
            'TIMER': true,
            'CONDITIONAL':true,
            'LINKCATCH':false,
            'SIGNALCATCH':true,
            'COMPENSATIONTHROW':false,
            'MULTIPLECATCH':false,
            'PARALLELCATCH':false,
            'ERRORTHROW':true  
        }
    };

    if(validation[mapType]){
        if(typeof validation[mapType][marker] !== "undefined"){
            return validation[mapType][marker];
        }      
    }
    return resp;
};
