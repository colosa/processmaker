var configCrown = {
    'PMActivity': {
        'DEFAULT': {
            order: ["task", "gateway", "intermediate", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 3
        }
    },
    'PMGateway': {
        'PARALLEL': {
            order: ["task", "gateway", "end", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'EXCLUSIVE': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'INCLUSIVE': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        }
    },
    'PMEvent': {
        'START_EMPTY': {
            order: ["task", "gateway", "intermediate", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'START_MESSAGECATCH': {
            order: ["task", "gateway", "intermediate", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'START_TIMER': {
            order: ["task", "gateway", "intermediate", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'START_CONDITIONAL': {
            order: ["task", "gateway", "intermediate", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'START_SIGNALCATCH': {
            order: ["task", "gateway", "intermediate", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_EMAIL': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_MESSAGETHROW': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_SIGNALTHROW': {
            order: ["task", "gateway", "end", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_MESSAGECATCH': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_TIMER': {
            order: ["task", "gateway", "end", "flow", "properties", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_CONDITIONAL': {
            order: ["task", "gateway", "end", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'INTERMEDIATE_SIGNALCATCH': {
            order: ["task", "gateway", "end", "flow", "delete"],
            rows: 3,
            cols: 2
        },
        'END_EMPTY': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'END_EMAIL': {
            order: ["flow", "properties", "delete"],
            rows: 2,
            cols: 2
        },
        'END_MESSAGETHROW': {
            order: ["flow", "properties", "delete"],
            rows: 2,
            cols: 2
        },
        'END_ERRORTHROW': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'END_SIGNALTHROW': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'END_TERMINATETHROW': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        }
    },
    'PMPool': {
        'DEFAULT': {
            order: ["delete"],
            rows: 1,
            cols: 1
        }
    },
    'PMParticipant': {
        'DEFAULT': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        }
    },
    'PMArtifact': {
        'TEXT_ANNOTATION': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'GROUP': {
            order: ["delete"],
            rows: 1,
            cols: 1
        }
    },
    'PMData': {
        'DATAOBJECT': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'DATAINPUT': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'DATAOUTPUT': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        },
        'DATASTORE': {
            order: ["flow", "delete"],
            rows: 2,
            cols: 1
        }
    }
};