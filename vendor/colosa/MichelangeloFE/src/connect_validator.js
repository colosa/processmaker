var ConnectValidator = function () {

};
ConnectValidator.prototype.type = "ConnectValidator";

ConnectValidator.prototype.initRules = {
    'START': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'STARTMESSAGECATCH': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'INTERMEDIATE': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'INTERMEDIATEEMAIL': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'INTERMEDIATEMESSAGETHROW': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'INTERMEDIATEMESSAGECATCH': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'END': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'ENDEMAIL': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'ENDMESSAGETHROW': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'

    },
    'TASK': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'SUB_PROCESS': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'GATEWAY': {
        'START': 'sequencialRules',
        'INTERMEDIATE': 'sequencialRules',
        'INTERMEDIATEEMAIL': 'sequencialRules',
        'INTERMEDIATEMESSAGETHROW': 'sequencialRules',
        'INTERMEDIATEMESSAGECATCH': 'sequencialRules',
        'TASK': 'sequencialRules',
        'SUB_PROCESS': 'sequencialRules',
        'GATEWAY': 'sequencialRules',
        'END': 'sequencialRules',
        'ENDEMAIL': 'sequencialRules',
        'ENDMESSAGETHROW': 'sequencialRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'DATA': {
        'START': 'associationRules',
        'INTERMEDIATE': 'associationRules',
        'INTERMEDIATEEMAIL': 'associationRules',
        'INTERMEDIATEMESSAGETHROW': 'associationRules',
        'INTERMEDIATEMESSAGECATCH': 'associationRules',
        'TASK': 'associationRules',
        'SUB_PROCESS': 'associationRules',
        'GATEWAY': 'associationRules',
        'END': 'associationRules',
        'DATA': 'associationRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules'
    },
    'PARTICIPANT': {
        'START': 'messageRules',
        'STARTMESSAGECATCH': 'messageRules',
        'TASK': 'messageRules',
        'SUB_PROCESS': 'messageRules',
        'GATEWAY': 'messageRules',
        'END': 'messageRules',
        'ENDEMAIL': 'messageRules',
        'ENDMESSAGETHROW': 'messageRules',
        'DATA': 'messageRules',
        'PARTICIPANT': 'messageRules',
        'TEXT_ANNOTATION': 'annotationRules',
        'INTERMEDIATEMESSAGECATCH': 'messageRules'
    },
    'TEXT_ANNOTATION': {
        'START': 'annotationRules',
        'INTERMEDIATE': 'annotationRules',
        'INTERMEDIATEEMAIL': 'annotationRules',
        'INTERMEDIATEMESSAGETHROW': 'annotationRules',
        'INTERMEDIATEMESSAGECATCH': 'annotationRules',
        'TASK': 'annotationRules',
        'SUB_PROCESS': 'annotationRules',
        'GATEWAY': 'annotationRules',
        'END': 'annotationRules',
        'ENDEMAIL': 'annotationRules',
        'ENDMESSAGETHROW': 'annotationRules',
        'DATA': 'annotationRules',
        'PARTICIPANT': 'annotationRules',
        'TEXT_ANNOTATION': 'annotationRules'
    }
};

ConnectValidator.prototype.sequencialRules = {
    'START': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': false,
        'END': false
    },
    'STARTMESSAGECATCH': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': false,
        'END': false
    },
    'INTERMEDIATE': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true
    },
    'INTERMEDIATEEMAIL': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true
    },
    'INTERMEDIATEMESSAGETHROW': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true
    },
    'INTERMEDIATEMESSAGECATCH': {
        'START': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true
    },
    'END': {
        'START': false,
        'TASK': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'END': false,
        'INTERMEDIATE': false,
        'INTERMEDIATEEMAIL': false,
        'INTERMEDIATEMESSAGETHROW': false,
        'INTERMEDIATEMESSAGECATCH': false
    },
    'ENDEMAIL': {
        'START': false,
        'TASK': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'END': false,
        'INTERMEDIATE': false,
        'INTERMEDIATEEMAIL': false,
        'INTERMEDIATEMESSAGETHROW': false,
        'INTERMEDIATEMESSAGECATCH': false
    },
    'ENDMESSAGETHROW': {
        'START': false,
        'TASK': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'END': false,
        'INTERMEDIATE': false,
        'INTERMEDIATEEMAIL': false,
        'INTERMEDIATEMESSAGETHROW': false,
        'INTERMEDIATEMESSAGECATCH': false
    },
    'TASK': {
        'START': false,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true
    },
    'SUB_PROCESS': {
        'START': false,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true
    },
    'GATEWAY': {
        'START': false,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': true,
        'END': true,
        'ENDEMAIL': true,
        'ENDMESSAGETHROW': true,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true
    },
    'DATAOBJECT': {
        'TASK': true,
        'INTERMEDIATE': true,
        'INTERMEDIATEEMAIL': true,
        'INTERMEDIATEMESSAGETHROW': true,
        'INTERMEDIATEMESSAGECATCH': true
    }
};

ConnectValidator.prototype.messageRules = {
    'PARTICIPANT': {
        'PARTICIPANT': true,
        'TASK': true,
        'SUBPROCESS': true,
        'START': false,
        'STARTMESSAGECATCH': true,
        'END': false,
        'INTERMEDIATE': true,
        'INTERMEDIATEMESSAGECATCH': true,
        'GATEWAY': false
    },
    'START': {
        'PARTICIPANT': false
    },
    'STARTMESSAGECATCH': {
        'PARTICIPANT': false
    },
    'INTERMEDIATE': {
        'PARTICIPANT': false
    },
    'INTERMEDIATEMESSAGETHROW': {
        'PARTICIPANT': true
    },
    'INTERMEDIATEMESSAGECATCH': {
        'PARTICIPANT': false
    },
    'INTERMEDIATEEMAIL': {
        'PARTICIPANT': true
    },
    'TASK': {
        'PARTICIPANT': true
    },
    'SUB_PROCESS': {
        'PARTICIPANT': true
    },
    'END': {
        'PARTICIPANT': false
    },
    'GATEWAY': {
        'PARTICIPANT': false
    },
    'DATA': {
        'PARTICIPANT': false
    },
    'ENDMESSAGETHROW': {
        'PARTICIPANT': true
    },
    'ENDEMAIL': {
        'PARTICIPANT': true
    }

};

ConnectValidator.prototype.associationRules = {
    'PARTICIPANT': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': false,
        'END': false,
        'PARTICIPANT': false
    },
    'START': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': false,
        'END': false,
        'PARTICIPANT': false
    },
    'INTERMEDIATE': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': false,
        'END': false,
        'PARTICIPANT': false
    },
    'END': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': false,
        'END': false,
        'PARTICIPANT': false
    },
    'TASK': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': true,
        'END': false,
        'PARTICIPANT': false
    },
    'SUB_PROCESS': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATA': true,
        'END': false,
        'PARTICIPANT': false
    },
    'GATEWAY': {
        'TASK': false,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': false,
        'GATEWAY': false,
        'DATAOBJECT': false,
        'END': false
    },
    'DATA': {
        'TASK': true,
        'START': false,
        'INTERMEDIATE': false,
        'SUB_PROCESS': true,
        'GATEWAY': false,
        'DATA': false,
        'END': false,
        'PARTICIPANT': false
    }
};

ConnectValidator.prototype.proMessageRules = {
    'TASK': {
        'START': false,
        'INTERMEDIATE': false,
        'TASK': true,
        'SUB_PROCESS': true,
        'GATEWAY': false,
        'END': false
    }
};

ConnectValidator.prototype.isValid = function (sourceShape, targetShape, connection) {
    var result = {
            result: false,
            msg: 'Invalid Connections'.translate()
        },
        connectionConf,
        rules,
        i,
        parentSource;
    //type of shapes
    if (this.getTypeToValidate(sourceShape) && this.getTypeToValidate(targetShape)) {
        result.msg = 'Invalid Connection';
        //validate if there is an current connection between same elements
        if ((!connection || (connection.getSrcPort().getParent() !== sourceShape || connection.getDestPort() !== targetShape)) && PMFlow.existsConnection(sourceShape, targetShape, false)) {
            return {
                result: false,
                msg: 'There is already a connection between these elements'.translate()
            };
        }
        //validate loop connections
        if (sourceShape.getID() === targetShape.getID()) {
            return result;
        }

        if (sourceShape.participantObject) {
            parentSource = sourceShape.participantObject.elem.$parent.id;
        } else {
            if (sourceShape.businessObject.elem.$type !== "bpmn:BoundaryEvent") {
                parentSource = sourceShape.businessObject.elem.$parent.id;
            } else {
                parentSource = sourceShape.businessObject.elem.attachedToRef.$parent.id;
            }
        }
        //verify if elenents are into the same process
        if (sourceShape.businessObject.elem
            && targetShape.businessObject.elem
            && targetShape.businessObject.elem.$parent
            && parentSource !== targetShape.businessObject.elem.$parent.id) {

            switch (sourceShape.type) {
                case 'PMActivity':
                    result.conf = {
                        type: 'MESSAGE',
                        segmentStyle: 'segmented',
                        destDecorator: 'mafe-message',
                        srcDecorator: 'mafe-message'
                    };
                    if (targetShape.type === 'PMActivity') {
                        result.result = true;
                        result.invalid = true;
                    } else if (targetShape.type === 'PMEvent' && targetShape.evn_marker === 'MESSAGECATCH') {
                        result.result = true;
                    } else if (targetShape.type === 'PMData') {
                        result.result = true;
                        result.conf = {
                            type: 'DATAASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-association'
                        };
                    } else if (targetShape.type === 'PMArtifact') {
                        result.result = true;
                        result.conf = {
                            type: 'ASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-default'
                        };
                    }
                    break;
                case 'PMEvent':
                    result.conf = {
                        type: 'MESSAGE',
                        segmentStyle: 'segmented',
                        destDecorator: 'mafe-message',
                        srcDecorator: 'mafe-message'
                    };
                    if (sourceShape.type === 'PMEvent'
                        && (sourceShape.evn_marker === 'MESSAGETHROW')) {
                        if (targetShape.type === 'PMActivity') {
                            result.result = true;
                        } else if (targetShape.type === 'PMEvent' && targetShape.evn_marker === 'MESSAGECATCH') {
                            result.result = true;
                        }
                    } else if (targetShape.type === 'PMArtifact') {
                        result.result = true;
                        result.conf = {
                            type: 'ASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-default'
                        };
                    }
                    break;
                case 'PMGateway':
                    if (targetShape.type === 'PMArtifact') {
                        result.result = true;
                        result.conf = {
                            type: 'ASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-default'
                        };
                    }
                    break;
                case 'PMData':
                    if (targetShape.type === 'PMActivity') {
                        result.result = true;
                        result.conf = {
                            type: 'DATAASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-association'
                        };
                    } else if (targetShape.type === 'PMArtifact') {
                        result.result = true;
                        result.conf = {
                            type: 'ASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-default'
                        };
                    }
                    break;
                case 'PMArtifact':
                    result.result = true;
                    result.conf = {
                        type: 'ASSOCIATION',
                        segmentStyle: 'dotted',
                        destDecorator: 'mafe-default'
                    };
                    break;
            }
            if (result.result) {
                // validate that the sourceShape allow the outgoing connection
                if ((!connection || (connection.getSrcPort().getParent() !== sourceShape)) && !this.canAcceptOutgoingConnection(sourceShape, result.conf.type)) {
                    return {
                        result: false,
                        msg: 'The source shape can not have more than one outgoing connection'.translate()
                    };
                }
            }
            if (result.result) {
                result.result = true;
                targetShape.setConnectionType(result.conf);
            }
        } else {
            if (this.initRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)]) {
                rules = this.initRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)];
                switch (rules) {
                    case 'sequencialRules':
                        if (this.sequencialRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)]) {
                            result.result = this.sequencialRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)];
                        }
                        connectionConf = {
                            type: 'SEQUENCE',
                            segmentStyle: 'regular',
                            destDecorator: 'mafe-sequence'
                        };
                        break;
                    case 'messageRules':
                        if (this.messageRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)]) {
                            result.result = this.messageRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)];
                            if (result.result) {
                                if (sourceShape.extendedType === 'START' && sourceShape.evn_marker === "MESSAGE") {
                                    result.result = false;
                                    result.msg = 'Start Event must not have any outgoing Message Flows'.translate();
                                }
                            }
                        }
                        connectionConf = {
                            type: 'MESSAGE',
                            segmentStyle: 'segmented',
                            destDecorator: 'mafe-message',
                            srcDecorator: 'mafe-message'
                        };
                        break;
                    case 'annotationRules':
                        result.result = true;
                        connectionConf = {
                            type: 'ASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-default'
                        };
                        break;
                    case 'associationRules':
                        if (this.associationRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)]) {
                            result.result = this.associationRules[this.getTypeToValidate(sourceShape)][this.getTypeToValidate(targetShape)];
                        }
                        connectionConf = {
                            type: 'DATAASSOCIATION',
                            segmentStyle: 'dotted',
                            destDecorator: 'mafe-association'
                        };
                        break;
                }
                if (result.result) {
                    // validate that the sourceShape allow the outgoing connection
                    if ((!connection || (connection.getSrcPort().getParent() !== sourceShape)) && !this.canAcceptOutgoingConnection(sourceShape, connectionConf.type)) {
                        return {
                            result: false,
                            msg: 'The source shape can not have more than one outgoing connection'.translate()
                        };
                    }
                }
                targetShape.setConnectionType(connectionConf);
            }
        }
    }
    return result;
};

ConnectValidator.prototype.getTypeToValidate = function (shape) {
    var type;
    switch (shape.extendedType) {
        case 'START':
            if (shape.getEventMarker() === 'MESSAGECATCH') {
                type = shape.extendedType + shape.getEventMarker();
            } else {
                type = shape.extendedType;
            }
            break;
        case 'END':
            if (shape.getEventMarker() === 'EMAIL'
                || shape.getEventMarker() === 'MESSAGETHROW') {
                type = shape.extendedType + shape.getEventMarker();
            } else {
                type = shape.extendedType;
            }
            break;
        case 'INTERMEDIATE':
            if (shape.getEventMarker() === 'EMAIL'
                || shape.getEventMarker() === 'MESSAGETHROW'
                || shape.getEventMarker() === 'MESSAGECATCH') {
                type = shape.extendedType + shape.getEventMarker();
            } else {
                type = shape.extendedType;
            }
            break;
        case 'TASK':
        case 'SUB_PROCESS':
        case 'PARTICIPANT':
        case 'TEXT_ANNOTATION':
            type = shape.extendedType;
            break;
        case 'EXCLUSIVE':
        case 'PARALLEL':
        case 'INCLUSIVE':
        case 'COMPLEX':
            type = 'GATEWAY';
            break;
        case 'DATAOBJECT':
        case 'DATASTORE':
        case 'DATAINPUT':
        case 'DATAOUTPUT':
            type = 'DATA';
            break;
        case 'GROUP':
            type = 'GROUP';
            break;
        case 'POOL':
            type = 'POOL';
            break;
        case 'LANE':
            type = 'LANE';
            break;
        default:
            type = 'TASK';
            break;
    }
    return type;
};
ConnectValidator.prototype.styleErrorMap = new PMUI.util.ArrayList();
ConnectValidator.prototype.hasNotMap = new PMUI.util.ArrayList();
/**
 * Validate All Shapes.
 * @returns {ConnectValidator}
 */
ConnectValidator.prototype.bpmnValidator = function () {
    var canvas,
        shape,
        i;
    canvas = PMUI.getActiveCanvas();
    PMDesigner.validTable.clear().draw();
    for (i = 0; i < canvas.customShapes.getSize(); i += 1) {
        shape = canvas.customShapes.get(i);
        this.bpmnValidatorShape(shape);
    }
    return this;
};
/**
 * Validate Shape
 * @param shape
 * @returns {ConnectValidator}
 */
ConnectValidator.prototype.bpmnValidatorShape = function (shape) {
    if (shape.validatorMarker) {
        this.bpmnFlowValidator(shape);
        shape.validatorMarker.hide();
        if (shape.getNumErrors() > 0 && shape.validatorMarker) {
            if (shape.validateWarning(shape.getArrayErrors())) {
                shape.setTypeErrors("warning");
            } else {
                shape.setTypeErrors("error");
            }
            shape.validatorMarker.show();
        }
    }
    return this;
};
/**
 * Call to Sequence flow Validator
 * @param shape
 * @returns {ConnectValidator}
 */
ConnectValidator.prototype.bpmnFlowValidator = function (shape) {
    this.sequenceFlowValidator(shape);
    return this;
};
/**
 * Check the type bpmn Shape and get rules of validation
 * @param shape
 */
ConnectValidator.prototype.sequenceFlowValidator = function (shape) {
    var ref = shape.businessObject.elem,
        criteria,
        errorCandidates,
        i,
        error = {},
        type,
        max;
    shape.errors.clear();
    if (shape.getType() === 'PMParticipant') {
        ref = shape.participantObject.elem;
    }
    switch (ref.$type) {
        case 'bpmn:Task':
        case 'bpmn:SendTask':
        case 'bpmn:ReceiveTask':
        case 'bpmn:ServiceTask':
        case 'bpmn:UserTask':
        case 'bpmn:ScriptTask':
        case 'bpmn:ManualTask':
        case 'bpmn:BusinessRuleTask':
            type = 'bpmnActivity';
            break;
        case 'bpmn:SubProcess':
            type = 'bpmnSubProcess';
            break;
        case 'bpmn:IntermediateThrowEvent':
        case 'bpmn:IntermediateCatchEvent':
            type = 'bpmnIntermediateEvent';
            break;
        case 'bpmn:ExclusiveGateway':
        case 'bpmn:ParallelGateway':
        case 'bpmn:InclusiveGateway':
            type = 'bpmnGateway';
            break;
        case 'bpmn:DataObjectReference':
            type = 'bpmnDataObject';
            break;
        case 'bpmn:DataStoreReference':
            type = 'bpmnDataStore';
            break;
        default:
            type = ref.$type;
            break;
    }
    if (PMDesigner.modelRules.getStatus()) {
        errorCandidates = PMDesigner.modelRules.getCollectionType(type);
    }
    if (errorCandidates) {
        for (i = 0, max = errorCandidates.getSize(); i < max; i += 1) {
            error = errorCandidates.get(i);
            criteria = error.getCriteria();
            if (criteria && typeof criteria === "function") {
                criteria(shape, _.extend({}, error));
            }
        }
    }
};
/**
 * Verify if the supplied element can accept one outgoing connection more.
 * @param {PMShape} shape
 * @returns {boolean}
 */
ConnectValidator.prototype.canAcceptOutgoingConnection = function (shape, connectionType) {
    if (!(shape instanceof PMGateway) && connectionType === 'SEQUENCE') {
        return !shape.getOutgoingConnections(connectionType).length;
    }
    return true;
};
/**
 * Validate if PMActivity/PMEvent onDrop is correct
 * @param {PMUI.draw.Shape} shape
 * @param {PMUI.draw.Shape} customShape
 * @returns {boolean}
 */
ConnectValidator.prototype.onDropMovementIsAllowed = function (shape, customShape) {
    var result = true,
        i,
        connection,
        ports,
        len,
        sourceShape,
        targetShape,
        elemShape,
        flowType,
        oldParent = customShape.getOldParent().getParent();

    if (shape.getID() !== customShape.getParent().getID()) {
        ports = customShape.getPorts();
        len = ports.getSize();
        for (i = 0; i < len; i += 1) {
            //Get sourceShape and targetShape (PMevent/PMactivity)
            connection = ports.get(i).getConnection();
            sourceShape = connection.getSrcPort().getParent();
            targetShape = connection.getDestPort().getParent();
            elemShape = shape.businessObject.elem;
            elemShape = (elemShape && elemShape.$parent && elemShape.$parent.$parent) ? elemShape : false;
            flowType = (connection.getFlowType() === 'MESSAGE') ? true : false;

            if (flowType &&
                (sourceShape.getParent().getID() === shape.getID()
                    || targetShape.getParent().getID() === shape.getID())) {
                result = false;
                break;
            } else if (elemShape && flowType &&
                (elemShape.$parent.$parent.id === sourceShape.businessObject.elem.$parent.id ||
                    elemShape.$parent.$parent.id === targetShape.businessObject.elem.$parent.id)) {
                result = (oldParent) ? (oldParent.getID() !== shape.getParent().getID()) ? false : true : false;
                break;
            }
        }
    }
    return result;
};
