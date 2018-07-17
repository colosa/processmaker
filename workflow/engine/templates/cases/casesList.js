new Ext.KeyMap(document, {
    key: Ext.EventObject.F5,
    fn: function(keycode, e) {
        if (! e.ctrlKey) {
            if (Ext.isIE) {
                // IE6 doesn't allow cancellation of the F5 key, so trick it into
                // thinking some other key was pressed (backspace in this case)
                e.browserEvent.keyCode = 8;
            }
            e.stopEvent();
            //document.location = document.location;
            storeCases.reload();
        }
        else
            Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
    }
});


/*** global variables **/
var storeCases;
var storeReassignCases;
var grid;
var textJump;
var ids = '';
var winReassignInCasesList;
var casesNewTab;

function formatAMPM(date, initVal, calendarDate) {

    var currentDate = new Date();
    var currentDay = currentDate.getDate();
    var currentMonth = currentDate.getMonth()+1;
    if (currentDay < 10) {
        currentDay = '0' + currentDay;
    }
    if (currentMonth < 10) {
        currentMonth = '0' + currentMonth;
    }
    currentDate = currentMonth + '-' + currentDay;
    if (currentDate == calendarDate) {
        var hours = date.getHours();
        var minutes = (initVal === true)? ((date.getMinutes()<15)? 15: ((date.getMinutes()<30)? 30: ((date.getMinutes()<45)? 45: 45))): date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
    } else {
        var strTime = '12:00 AM';
    }
    return strTime;
}

Ext.Ajax.timeout = 4 * 60 * 1000;

var caseSummary = function() {
    var rowModel = grid.getSelectionModel().getSelected();
    if (rowModel) {
        openSummaryWindow(rowModel.data.APP_UID, rowModel.data.DEL_INDEX, action);
    }
    else {
        msgBox(_('ID_INFORMATION'), _('ID_SELECT_ONE_AT_LEAST'));
    }
};

function caseNotes(){
    var rowModel = grid.getSelectionModel().getSelected();
    if(rowModel){
        var appUid   = rowModel.data.APP_UID;
        var delIndex = rowModel.data.DEL_INDEX;
        var caseTitle = (rowModel.data.APP_TITLE) ? rowModel.data.APP_TITLE : rowModel.data.APP_UID;
        var task = (typeof(rowModel.json.TAS_UID) != 'undefined') ? rowModel.json.TAS_UID : '';
        var proid = (typeof(rowModel.json.PRO_UID) != 'undefined') ? rowModel.json.PRO_UID : '';

        openCaseNotesWindow(appUid, delIndex, true, caseTitle, proid, task);
    }else{
        msgBox(_('ID_INFORMATION'), _('ID_SELECT_ONE_AT_LEAST') );
    }
}
function openCase(){

    var rowModel = grid.getSelectionModel().getSelected(),
        nameTab;
    if(rowModel){
        var appUid   = rowModel.data.APP_UID;
        var delIndex = rowModel.data.DEL_INDEX;
        var tasUid   = (typeof(rowModel.json.TAS_UID) != 'undefined') ? rowModel.json.TAS_UID : '';
        var caseTitle = (rowModel.data.APP_TITLE) ? rowModel.data.APP_TITLE : rowModel.data.APP_UID;
        if(!isIE) {
            Ext.Msg.show({
                msg: _('ID_OPEN_CASE') + ' ' + caseTitle,
                width:300,
                wait:true,
                waitConfig: {interval:200}
            });
        }
        params = '';
        switch(action){
            case 'to_revise':
                params += 'APP_UID=' + appUid;
                params += '&DEL_INDEX=' + delIndex;
                params += '&TAS_UID=' + tasUid;
                params += '&to_revise=true';
                params += '&actionFromList='+action;
                requestFile = 'open';
                break;
            case 'sent': // = participated
                params += 'APP_UID=' + appUid;
                params += '&DEL_INDEX=' + delIndex;
                //requestFile = '../cases/cases_Open';
                requestFile = 'open';
                break;
            case 'todo':
            case 'draft':
            case 'paused':
            case 'unassigned':
            default:
                params += 'APP_UID=' + appUid;
                params += '&DEL_INDEX=' + delIndex;
                //requestFile = '../cases/cases_Open';
                requestFile = 'open';
                break;
        }
        try {
            try {
                parent._CASE_TITLE  = caseTitle;
            }
            catch (e) {
                // Nothing to do
            }
        }
        catch (e) {
            // Nothing to do
        }
        params += '&action=' + action;
        if(isIE) {
            if(casesNewTab) {
                casesNewTab.close();
            }
            nameTab = PM.Sessions.getCookie('PM-TabPrimary') + '_openCase';
            casesNewTab = window.open(requestFile + '?' + params, nameTab);
        } else {
            redirect(requestFile + '?' + params);
        }

    } else
        msgBox(_('ID_INFORMATION'), _('ID_SELECT_ONE_AT_LEAST'));
}

function jumpToCase(appNumber){
    //  Code add by Brayan Pereyra - cochalo
    //  This ajax validate the appNumber exists
    Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
    Ext.Ajax.request({
        url: 'cases_Ajax',
        success: function(response) {
            var res = Ext.decode(response.responseText);
            if (res.exists === true) {
                params = 'APP_NUMBER=' + appNumber;
                params += '&action=jump';
                params += '&actionFromList='+action;

                requestFile = '../cases/open';
                redirect(requestFile + '?' + params);
            } else {
                Ext.MessageBox.hide();
                var message = new Array();
                message['CASE_NUMBER'] = appNumber;
                msgBox(_('ID_INPUT_ERROR'), _(res.message), 'error');
            }
        },
        params: {action:'previusJump', appNumber: appNumber, actionFromList: action}
    });
}

function deleteCase() {
    var rows = grid.getSelectionModel().getSelections();
    Ext.Ajax.request({
        url : 'casesList_Ajax' ,
        params : {actionAjax : 'verifySession'},
        success: function ( result, request ) {
            var data = Ext.util.JSON.decode(result.responseText);
            if( data.lostSession ) {
                Ext.Msg.show({
                    title: _('ID_ERROR'),
                    msg: data.message,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.MessageBox.OK,
                    fn : function(btn) {
                        try
                        {
                            prnt = parent.parent;
                            top.location = top.location;
                        }
                        catch (err)
                        {
                            parent.location = parent.location;
                        }
                    }
                });
            } else {
                if( rows.length > 0 ) {
                    ids = Array();
                    for(i=0; i<rows.length; i++)
                        ids[i] = rows[i].get('APP_UID');

                    APP_UIDS = ids.join(',');

                    Ext.Msg.confirm(
                        _('ID_CONFIRM'),
                        (rows.length == 1) ? _('ID_MSG_CONFIRM_DELETE_CASE') : _('ID_MSG_CONFIRM_DELETE_CASES'),
                        function(btn, text){
                            if ( btn == 'yes' ) {
                                Ext.MessageBox.show({ msg: _('ID_DELETING_ELEMENTS'), wait:true,waitConfig: {interval:200} });
                                Ext.Ajax.request({
                                    url: 'cases_Delete',
                                    success: function(response) {
                                        try {
                                            parent.updateCasesView(true);
                                        }
                                        catch (e) {
                                            // Nothing to do
                                        }
                                        Ext.MessageBox.hide();
                                        try {
                                            parent.updateCasesTree();
                                        }
                                        catch (e) {
                                            // Nothing to do
                                        }
                                    },
                                    params: {APP_UIDS:APP_UIDS}
                                });
                            }
                        }
                    );
                } else {
                    Ext.Msg.show({
                        title:'',
                        msg: _('ID_NO_SELECTION_WARNING'),
                        buttons: Ext.Msg.INFO,
                        fn: function(){},
                        animEl: 'elId',
                        icon: Ext.MessageBox.INFO,
                        buttons: Ext.MessageBox.OK
                    });
                }
            }
        },
        failure: function ( result, request) {
            if (typeof(result.responseText) != 'undefined') {
                Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
            }
        }
    });
}

function pauseCase(date){
    rowModel = grid.getSelectionModel().getSelected();

    if(rowModel) {
        unpauseDate = date.format('Y-m-d');
        var msgPause =  new Ext.Window({
            //layout:'fit',
            width:500,
            plain: true,
            modal: true,
            title: _('ID_CONFIRM'),

            items: [
                new Ext.FormPanel({
                    labelAlign: 'top',
                    labelWidth: 75,
                    border: false,
                    frame: true,
                    items: [
                        {
                            html: '<div align="center" style="font: 14px tahoma,arial,helvetica,sans-serif">' + _('ID_PAUSE_CASE_TO_DATE') +' '+date.format('M j, Y')+'? </div> <br/>'
                        },
                        new Ext.form.TimeField({
                            id: 'unpauseTime',
                            fieldLabel: _('ID_UNPAUSE_TIME'),
                            name: 'unpauseTime',
                            value: formatAMPM(new Date(), false, date.format('m-d')),
                            minValue: formatAMPM(new Date(), true, date.format('m-d')),
                            format: 'h:i A'
                        }),
                        {
                            xtype: 'textarea',
                            id: 'noteReason',
                            fieldLabel: _('ID_CASE_PAUSE_REASON'),
                            name: 'noteReason',
                            width: 450,
                            height: 50
                        },
                        {
                            id: 'notifyReason',
                            xtype:'checkbox',
                            name: 'notifyReason',
                            hideLabel: true,
                            boxLabel: _('ID_NOTIFY_USERS_CASE')
                        }
                    ],

                    buttonAlign: 'center',

                    buttons: [{
                        text: _('ID_OK'),
                        handler: function(){
                            if (Ext.getCmp('noteReason').getValue() != '') {
                                var noteReasonTxt = _('ID_CASE_PAUSE_LABEL_NOTE') + ' ' + Ext.getCmp('noteReason').getValue();
                            } else {
                                var noteReasonTxt = '';
                            }
                            var notifyReasonVal = Ext.getCmp('notifyReason').getValue() == true ? 1 : 0;

                            Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
                            Ext.Ajax.request({
                                url: 'cases_Ajax',
                                success: function(response) {
                                    try {
                                        parent.updateCasesView(true);
                                    }
                                    catch (e) {
                                        // Nothing to do
                                    }
                                    Ext.MessageBox.hide();
                                    try {
                                        parent.updateCasesTree();
                                    }
                                    catch (e) {
                                        // Nothing to do
                                    }
                                    Ext.MessageBox.hide();
                                    msgPause.close();
                                },
                                params: {
                                    action: 'pauseCase',
                                    unpausedate: unpauseDate,
                                    unpauseTime: Ext.getCmp('unpauseTime').getValue(),
                                    APP_UID: rowModel.data.APP_UID,
                                    DEL_INDEX: rowModel.data.DEL_INDEX,
                                    NOTE_REASON: noteReasonTxt,
                                    NOTIFY_PAUSE: notifyReasonVal,
                                    APP_TITLE: rowModel.data.APP_TITLE
                                }
                            });
                        }
                    },{
                        text: _('ID_CANCEL'), //COCHATRA
                        handler: function(){
                            msgPause.close();
                        }
                    }]
                })
            ]
        });
        msgPause.show(this);

    } else {
        Ext.Msg.show({
            title:'',
            msg: _('ID_NO_SELECTION_WARNING'),
            buttons: Ext.Msg.INFO,
            fn: function(){},
            animEl: 'elId',
            icon: Ext.MessageBox.INFO,
            buttons: Ext.MessageBox.OK
        });
    }
}


function cancelCase(){
    var rows = grid.getSelectionModel().getSelections();
    if( rows.length > 0 ) {
        app_uid = Array();
        del_index = Array();

        for(i=0; i<rows.length; i++){
            app_uid[i]   = rows[i].get('APP_UID');
            del_index[i] = rows[i].get('DEL_INDEX');
        }
        APP_UIDS    = app_uid.join(',');
        DEL_INDEXES = del_index.join(',');

        Ext.Msg.confirm(
            _('ID_CONFIRM'),
            (rows.length == 1) ? _('ID_MSG_CONFIRM_CANCEL_CASE') : _('ID_MSG_CONFIRM_CANCEL_CASES'),
            function(btn, text){
                if ( btn == 'yes' ) {
                    Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
                    Ext.Ajax.request({
                        url: 'cases_Ajax',
                        success: function(response) {
                            try {
                                parent.updateCasesView(true);
                            }
                            catch (e) {
                                // Nothing to do
                            }
                            Ext.MessageBox.hide();
                            try {
                                parent.updateCasesTree();
                            }
                            catch (e) {
                                // Nothing to do
                            }
                        },
                        params: {action:'cancelCase', APP_UID:APP_UIDS, DEL_INDEX:DEL_INDEXES}
                    });
                }
            }
        );
    } else {
        Ext.Msg.show({
            title:'',
            msg: _('ID_NO_SELECTION_WARNING'),
            buttons: Ext.Msg.INFO,
            fn: function(){},
            animEl: 'elId',
            icon: Ext.MessageBox.INFO,
            buttons: Ext.MessageBox.OK
        });
    }
}

function callbackUnpauseCase (btn, text) {
    if ( btn == 'yes' ) {
        Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
        Ext.Ajax.request({
            url: 'cases_Ajax',
            success: function(response) {
                try {
                    parent.updateCasesView(true);
                }
                catch (e) {
                    // Nothing to do
                }
                Ext.MessageBox.hide();
                try {
                    parent.updateCasesTree();
                }
                catch (e) {
                    // Nothing to do
                }
            },
            params: {action:'unpauseCase', sApplicationUID: caseIdToUnpause, iIndex: caseIndexToUnpause}
        });
    }
}

function unpauseCase() {
    rowModel = grid.getSelectionModel().getSelected();
    caseIdToUnpause    = rowModel.data.APP_UID;
    caseIndexToUnpause = rowModel.data.DEL_INDEX;

    Ext.Msg.confirm( _('ID_CONFIRM'), _('ID_CONFIRM_UNPAUSE_CASE') , function (btn, text) {
        if ( btn == 'yes' ) {
            Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
            Ext.Ajax.request({
                url: 'cases_Ajax',
                success: function(response) {
                    try {
                        parent.updateCasesView(true);
                    }
                    catch (e) {
                        // Nothing to do
                    }
                    Ext.MessageBox.hide();
                    try {
                        parent.updateCasesTree();
                    }
                    catch (e) {
                        // Nothing to do
                    }
                },
                params: {action:'unpauseCase', sApplicationUID: caseIdToUnpause, iIndex: caseIndexToUnpause}
            });
        }
    });
}

function redirect(href){
    window.location.href = href;
}

Ext.onReady ( function() {
    setExtStateManagerSetProvider('casesGrid', action);

    var ids = '';
    var filterProcess = '';
    var filterCategory = '';
    var filterUser    = '';
    var caseIdToDelete = '';
    var caseIdToUnpause = '';
    var caseIndexToUnpause = '';
    try {
        parent._action = action;
    }
    catch (e) {
        // Nothing to do
    }
    var columnRenderer = function(data, metadata, record, rowIndex,columnIndex, store) {
        var new_text = metadata.style.split(';');
        var style = '';
        if ( !record.data['DEL_INIT_DATE'] ){
            style = style + "font-weight: bold; ";
        }
        for (var i = 0; i < new_text.length -1 ; i++) {
            var chain = new_text[i] +";";
            if (chain.indexOf('width') == -1) {
                style = style + chain;
            }
        }

        data = Ext.util.Format.htmlEncode(data);
        metadata.attr = 'ext:qtip="' + data + '" style="'+ style +' white-space: normal; "';
        return data;
    };

    function openLink(value, p, r){
        return String.format("<a class='button_pm' href='../cases/cases_Open?APP_UID={0}&DEL_INDEX={1}&content=inner'>" + _('ID_VIEW') + "</a>", r.data['APP_UID'], r.data['DEL_INDEX'], r.data['APP_TITLE']);
    }

    function deleteLink(value, p, r){
        return String.format("<a class='button_pm ss_sprite ss_bullet_red' href='#' onclick='deleteCase(\"{0}\")'>" + _('ID_DELETE') + "</a>", r.data['APP_UID'] );
    }

    function viewLink(value, p, r){
        return String.format("<a href='../cases/cases_Open?APP_UID={0}&DEL_INDEX={1}&content=inner'>" + _('ID_VIEW') + "</a>", r.data['APP_UID'], r.data['DEL_INDEX'], r.data['APP_TITLE']);
    }

    function unpauseLink(value, p, r){
        return String.format("<a href='#' onclick='unpauseCaseFunction(\"{0}\",\"{1}\")'>" + _('ID_UNPAUSE') + "</a>", r.data['APP_UID'], r.data['DEL_INDEX'] );
    }

    function convertDate ( value ) {
        myDate = new Date( 1900,0,1,0,0,0);
        try{
            if(!Ext.isDate( value )){
                var myArray = value.split(' ');
                var myArrayDate = myArray[0].split('-');
                if ( myArray.length > 1 )
                    var myArrayHour = myArray[1].split(':');
                else
                    var myArrayHour = new Array('0','0','0');
                var myDate = new Date( myArrayDate[0], myArrayDate[1]-1, myArrayDate[2], myArrayHour[0], myArrayHour[1], myArrayHour[2] );
            }
        }
        catch(e){};

        return myDate;
    }
    function showDate (value,p,r) {
        var myDate = convertDate( value );
        return String.format("{0}", myDate.dateFormat( FORMATS.casesListDateFormat ));
    }

    function dueDate(value, p, r){
        if (value) {
            var myDate = convertDate( value );
            var myColor =  (myDate < new Date()) ? " color:red;" : 'color:green;';
            return String.format("<span style='{1}'>{0}</span>", myDate.dateFormat(FORMATS.casesListDateFormat), myColor );
        }
        else {
            return '';
        }
    }

    var renderSummary = function (val, p, r) {
        var summaryIcon = '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ss_layout_header" unselectable="off" id="extdd-17" ';
        summaryIcon += 'onclick="openSummaryWindow(' + "'" + r.data['APP_UID'] + "'" + ', ' + r.data['DEL_INDEX'] + ', action)" title="' + _('ID_SUMMARY') + '" />';
        return summaryIcon;
    };

    function renderNote(val,p,r) {
        var pro = r.json.PRO_UID;
        var tas = r.json.TAS_UID;
        var appUid = r.data.APP_UID;
        var delIndex = r.data.DEL_INDEX;
        var title = Ext.util.Format.htmlEncode(r.data.APP_TITLE);

        return "<img src=\"/images/ext/default/s.gif\" class=\"x-tree-node-icon ICON_CASES_NOTES\" unselectable=\"off\" id=\"extdd-17\" onClick=\"openCaseNotesWindow(\'" + appUid + "\', " + delIndex + ", true, \'" + title + "\', \'" + pro + "\', \'" + tas + "\');\" />";
    }

    //Render Full Name
    full_name = function (v, x, s) {
        var resp;
        if (s.data.USR_UID && s.data.USR_USERNAME) {
            resp = _FNF(s.data.USR_USERNAME, s.data.USR_FIRSTNAME, s.data.USR_LASTNAME);
        } else if (s && s.json && s.json["APP_TAS_TYPE"] === "SUBPROCESS") {
            resp = '';
        } else {
            resp = '[' + _('ID_UNASSIGNED').toUpperCase() + ']';
        }
        return resp;
    };

    previous_full_name = function(v, x, s) {
        if (s.data.PREVIOUS_USR_UID) {
            switch (s.data.PREVIOUS_USR_UID) {
                case "SCRIPT-TASK":
                    return _("ID_SCRIPT_TASK");
                    break;
                default:
                    return _FNF(s.data.PREVIOUS_USR_USERNAME, s.data.PREVIOUS_USR_FIRSTNAME, s.data.PREVIOUS_USR_LASTNAME);
                    break;
            }
        } else {
            return '';
        }
    };

    for(var i = 0, len = columns.length; i < len; i++){
        var c = columns[i];
        c.renderer = columnRenderer;
        if( c.dataIndex == 'DEL_TASK_DUE_DATE')     c.renderer = dueDate;
        if( c.dataIndex == 'APP_UPDATE_DATE')       c.renderer = showDate;
        if( c.id == 'deleteLink')                   c.renderer = deleteLink;
        if( c.id == 'viewLink')                     c.renderer = viewLink;
        if( c.id == 'unpauseLink')                  c.renderer = unpauseLink;
        if( c.dataIndex == 'CASE_SUMMARY')          c.renderer = renderSummary;
        if( c.dataIndex == 'CASE_NOTES_COUNT')      c.renderer = renderNote;

        if( c.dataIndex == 'CASE_SUMMARY')          c.sortable = false;
        if( c.dataIndex == 'CASE_NOTES_COUNT')      c.sortable = false;

        //Format the name if is disabled solr, otherwise show without format
        if (solrEnabled == 0) {
            if( c.dataIndex == 'APP_DEL_PREVIOUS_USER') c.renderer = previous_full_name;
            if( c.dataIndex == 'APP_CURRENT_USER')      c.renderer = full_name;
        }
        c.header = _(c.header);
    }

    //adding the hidden field DEL_INIT_DATE
    readerFields.push ( {name: "DEL_INIT_DATE"});
    readerFields.push ( {name: "APP_UID"});
    readerFields.push ( {name: "DEL_INDEX"});

    readerFields.push ( {name: "USR_FIRSTNAME"});
    readerFields.push ( {name: "USR_LASTNAME"});
    readerFields.push ( {name: "USR_USERNAME"});

    for (i=0; i<columns.length; i++) {
        if (columns[i].dataIndex == 'USR_UID') {
            columns[i].hideable = false;
        }
        if(columns[i].dataIndex == 'PREVIOUS_USR_UID') {
            columns[i].hideable=false;
        }
    }

    var cm = new Ext.grid.ColumnModel({
        defaults: {
            sortable: true // columns are sortable by default
        },
        listeners: {
            hiddenchange: function (columnModel, columnIndex, hidden) {
                var grid = Ext.getCmp('casesGrid');
                if (!hidden && grid && grid.getView) {
                    grid.getView().refresh();
                }
            }
        },
        columns: columns
    });

    for (var i in reassignColumns) {
        if (reassignColumns[i].dataIndex === 'APP_REASSIGN_USER') {
            reassignColumns[i].editor = comboUsersToReassign;
        }
        if (reassignColumns[i].dataIndex === 'NOTE_REASON') {
            reassignColumns[i].editor = new Ext.form.TextArea({allowBlank: false});
        }
        if (reassignColumns[i].dataIndex === 'NOTIFY_REASSIGN') {
            reassignColumns[i].editor = new Ext.form.Checkbox({});
            reassignColumns[i].renderer = function (v, x, s) {
                if (s.data.NOTIFY_REASSIGN === true) {
                    return _('ID_YES');
                }
                if (s.data.NOTIFY_REASSIGN === true) {
                    return _('ID_NO');
                }
                return s.data.NOTIFY_REASSIGN;
            };
        }
    }
    var reassignCm = new Ext.grid.ColumnModel({
        defaults: {
            sortable: true // columns are sortable by default
        },
        columns: reassignColumns
    });

    var newPopUp = new Ext.Window({
        id       : Ext.id(),
        el       : 'reassign-panel',
        title    : _('ID_REASSIGN_ALL_CASES_BY_TASK'),
        width    : 750,
        height   : 350,
        frame    : true,
        closable: false
    });

    var btnCloseReassign = new Ext.Button ({
        text: _('ID_CLOSE'),
        //    text: TRANSLATIONS.LABEL_SELECT_ALL,
        handler: function(){
            newPopUp.hide();
        }
    });

    var btnExecReassign = new Ext.Button ({
        text: _('ID_REASSIGN_ALL'),
        // text: 'Reassign All',
        //    text: TRANSLATIONS.LABEL_SELECT_ALL,
        handler: function(){

            var rs = storeReassignCases.getModifiedRecords();
            var sv = [];
            for(var i = 0; i <= rs.length-1; i++){
                //sv[i]= rs[i].data['name'];
                sv[i]= rs[i].data;
            }
            var gridData = storeReassignCases.getModifiedRecords();

            Ext.Ajax.request({
                url: 'proxySaveReassignCasesList',
                success: function(response) {
                    newPopUp.hide();
                    storeCases.reload();
                },
                params: { APP_UIDS:ids, data:Ext.util.JSON.encode(sv), selected:false }
            });

            /*
             storeReassignCases.setBaseParam('selected', false);
             var result = storeReassignCases.save();
             newPopUp.hide();
             storeCases.reload();
             */
            //storeReassignCases.reload();
        }
    });

    var ExecReassign = function () {
        newPopUp.hide();
        var rs = storeReassignCases.getModifiedRecords();

        var sv = [];
        for(var i = 0; i <= rs.length-1; i++){
            sv[i]= rs[i].data;
        }
        var gridData = storeReassignCases.getModifiedRecords();
        Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
        Ext.Ajax.request({
            url: 'proxySaveReassignCasesList',
            success: function(response) {
                Ext.MessageBox.hide();
                storeCases.reload();
                var ajaxServerResponse = Ext.util.JSON.decode(response.responseText);
                var count;
                var message = '';

                for (count in ajaxServerResponse) {
                    if ( ajaxServerResponse[count]['TAS_TITLE'] != undefined ){
                        message = message + _('ID_CASE') + ": " + ajaxServerResponse[count]['APP_TITLE'] + " - " + _('ID_REASSIGNED_TO') + ": " + ajaxServerResponse[count]['APP_REASSIGN_USER'] + "<br>" ;
                    };
                }

                if (ajaxServerResponse['TOTAL']!=undefined&&ajaxServerResponse['TOTAL']!=-1){
                    message = message + "<br> " + _('ID_TOTAL_CASES_REASSIGNED') + ": " + ajaxServerResponse['TOTAL'];
                } else {
                    message = "";
                };

                if (message!=""){
                    Ext.MessageBox.alert( _('ID_STATUS_REASSIGNMENT'), message, '' );
                }
            },
            params: { APP_UIDS:ids, data:Ext.util.JSON.encode(sv), selected:true }
        });
    }

    // Create HttpProxy instance, all CRUD requests will be directed to single proxy url.
    var proxyCasesList = new Ext.data.HttpProxy({
        api: {
            read : urlProxy
        }
    });

    // Typical JsonReader with additional meta-data params for defining the core attributes of your json-response
    // the readerFields is defined in PHP server side
    var readerCasesList = new Ext.data.JsonReader({
            totalProperty: 'totalCount',
            successProperty: 'success',
            idProperty: 'index',
            root: 'data',
            messageProperty: 'message'
        },
        readerFields
    );

    // The new DataWriter component.
    //currently we are not using this in casesList, but it is here just for complete definition
    var writerCasesList = new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true
    });

    var proxyReassignCasesList = new Ext.data.HttpProxy({
        api: {
            read    : 'proxyReassignCasesList'
            //destroy : 'proxyReassignCasesList'
        }
    });

    var readerReassignCasesList = new Ext.data.JsonReader({
            totalProperty: 'totalCount',
            successProperty: 'success',
            idProperty: 'index',
            root: 'data',
            messageProperty: 'message'
        },
        reassignReaderFields
    );

    // The new DataWriter component.
    //currently we are not using this in casesList, but it is here just for complete definition
    var writerReassignCasesList = new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true
    });



    // Typical Store collecting the Proxy, Reader and Writer together.
    // This is the store for Cases List
    storeCases = new Ext.data.Store({
        remoteSort: true,
        proxy: proxyCasesList,
        reader: readerCasesList,
        writer: writerCasesList,  // <-- plug a DataWriter into the store just as you would a Reader
        autoSave: true, // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
        sortInfo:{field: 'APP_NUMBER', direction: "DESC"},
        listeners: {
            beforeload: function (store, options)
            {
                this.setBaseParam(
                    "openApplicationUid", (__OPEN_APPLICATION_UID__ !== null)? __OPEN_APPLICATION_UID__ : ""
                );
            },
            load: function(response){

                if (response.reader.jsonData.result === false) {
                    PMExt.notify('ERROR', response.reader.jsonData.message);
                    //PMExt.error
                }
            },
            exception: function(dp, type, action, options, response, arg)  {
                responseObject = Ext.util.JSON.decode(response.responseText);
                if (typeof(responseObject.error) != 'undefined') {
                    Ext.Msg.show({
                        title: _('ID_ERROR'),
                        msg: responseObject.error,
                        fn: function(){parent.parent.location = '../login/login';},
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.MessageBox.OK
                    });
                }
            }
        }
    });

    storeReassignCases = new Ext.data.Store({
        remoteSort: false,
        proxy : proxyReassignCasesList,
        reader: readerReassignCasesList
        //writer: writerReassignCasesList,  // <-- plug a DataWriter into the store just as you would a Reader
        //autoSave: false // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
    });

    //Layout Resizing
    /*----------------------------------********---------------------------------*/
    storeCases.on('load',function(){var viewport = Ext.getCmp("viewportcases");viewport.doLayout();})

    // create the Data Store for processes
    var storeProcesses = new Ext.data.JsonStore({
        root: 'data',
        totalProperty: 'totalCount',
        idProperty: 'index',
        remoteSort: true,
        fields: [
            'PRO_UID', 'APP_PRO_TITLE'
        ],
        proxy: new Ext.data.HttpProxy({
            url: 'proxyProcessList?t=new'
        })
    });
    storeProcesses.setDefaultSort('APP_PRO_TITLE', 'asc');

    // creating the button for filters
    var btnRead = new Ext.Button ({
        id: 'read',
        text: _('ID_OPT_READ'),
        enableToggle: true,
        toggleHandler: onItemToggle,
        allowDepress: false,
        pressed: false
    });

    var btnUnread = new Ext.Button ({
        id: 'unread',
        text: _('ID_OPT_UNREAD'),
        enableToggle: true,
        toggleHandler: onItemToggle,
        allowDepress: false,
        pressed: false
    });

    var btnAll = new Ext.Button ({
        id: 'all',
        text: _('ID_OPT_ALL'),
        enableToggle: true,
        toggleHandler: onItemToggle,
        allowDepress: false,
        pressed: true
    });

    var btnStarted = new Ext.Button ({
        id: 'started',
//    text: 'started by me',
        text: _('ID_OPT_STARTED'),
        enableToggle: true,
        toggleHandler: onItemToggle,
        allowDepress: true,
        pressed: false
    });

    var btnCompleted = new Ext.Button ({
        id: 'completed',
//    text: 'Completed by me',
        text: _('ID_OPT_COMPLETED'),
        enableToggle: true,
        toggleHandler: onItemToggle,
        allowDepress: true,
        pressed: false
    });

    // ComboBox creation processValues
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="x-combo-list-item" style="white-space:normal !important;word-wrap: break-word;">',
        '<span> {APP_PRO_TITLE}</span>',
        '</div></tpl>'
    );

    Ext.Ajax.request({
        url : 'casesList_Ajax',
        params : {
            actionAjax : 'processListExtJs',
            action: action,
            CATEGORY_UID: filterCategory
        },
        success: function ( result, request ) {
            processValues = Ext.util.JSON.decode(result.responseText);
            suggestProcess.getStore().removeAll();
            suggestProcess.getStore().loadData(processValues);
        },
        failure: function ( result, request) {
            if (typeof(result.responseText) != 'undefined') {
                Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
            }
        }
    });

    var processStore =  new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy( {
            url : 'casesList_Ajax?actionAjax=processListExtJs&action='+action,
            method : 'POST'
        }),
        reader : new Ext.data.JsonReader( {
            fields : [ {
                name : 'PRO_UID'
            }, {
                name : 'PRO_TITLE'
            } ]
        })
    });

    var suggestProcess = new Ext.form.ComboBox({
        store: processStore,
        valueField : 'PRO_UID',
        displayField:'PRO_TITLE',
        typeAhead: false,
        triggerAction: 'all',
        emptyText : _('ID_EMPTY_PROCESSES'),
        selectOnFocus : true,
        editable : true,
        width: 150,
        allowBlank : true,
        autocomplete: true,
        minChars: 1,
        hideTrigger:true,
        listeners:{
            scope: this,
            'select': function() {
                filterProcess = suggestProcess.value;
                if ( action == 'search' ){
                    storeCases.setBaseParam('dateFrom', dateFrom.getValue());
                    storeCases.setBaseParam('dateTo', dateTo.getValue());
                }
                storeCases.setBaseParam('process', filterProcess);
            }
        }
    });

    var resetProcessButton = {
        text:'X',
        ctCls:"pm_search_x_button_des",
        handler: function(){
            storeCases.setBaseParam('process', '');
            suggestProcess.setValue('');
            doSearch();
        }
    };

    var comboCategory = new Ext.form.ComboBox({
        width           : 180,
        boxMaxWidth     : 200,
        editable        : false,
        displayField    : 'CATEGORY_NAME',
        valueField      : 'CATEGORY_UID',
        forceSelection  : false,
        emptyText       : _('ID_PROCESS_NO_CATEGORY'),
        selectOnFocus   : true,
        typeAhead       : true,
        mode            : 'local',
        autocomplete    : true,
        triggerAction   : 'all',

        store         : new Ext.data.ArrayStore({
            fields : ['CATEGORY_UID','CATEGORY_NAME'],
            data   : categoryValues
        }),
        listeners:{
            scope: this,
            'select': function() {

                filterCategory = comboCategory.value;
                storeCases.setBaseParam('category', filterCategory);
                storeCases.setBaseParam('process', '');
                //storeCases.load({params:{category: filterCategory, start : 0 , limit : pageSize}});

                Ext.Ajax.request({
                    url : 'casesList_Ajax' ,
                    params : {actionAjax : 'processListExtJs',
                        action: action,
                        CATEGORY_UID: filterCategory},
                    success: function ( result, request ) {
                        var data = Ext.util.JSON.decode(result.responseText);
                        suggestProcess.getStore().removeAll();
                        suggestProcess.getStore().loadData( data );
                        suggestProcess.setValue('');
                    },
                    failure: function ( result, request) {
                        if (typeof(result.responseText) != 'undefined') {
                            Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
                        }
                    }
                });
            }},
        iconCls: 'no-icon'
    });

    if (typeof filterStatus == 'undefined') {
        filterStatus = [];
    }

    /*----------------------------------********---------------------------------*/

    var btnSelectAll = new Ext.Button ({
        text: _('CHECK_ALL'),
        // text: 'Check All',
        // text: TRANSLATIONS.LABEL_SELECT_ALL,
        handler: function(){
            grid.getSelectionModel().selectAll();
        }
    });

    var btnUnSelectAll = new Ext.Button ({
        text: _('UNCHECK_ALL'),
        // text: 'Un-Check All',
        // text: TRANSLATIONS.LABEL_UNSELECT_ALL,
        handler: function(){
            grid.getSelectionModel().clearSelections();
        }
    });

    var btnReassign = new Ext.Button ({
        text: _('ID_REASSIGN'),
        // text: 'Reassign',
        // text: TRANSLATIONS.LABEL_UNSELECT_ALL,
        handler: function(){
            if(openReassignCallback) {
                for(var key in openReassignCallback){
                    var callbackFunction = new Function(openReassignCallback[key]);
                    callbackFunction.call();
                }
                return;
            }
            reassign();
        }
    });

//  var conn = new Ext.data.Connection();
    var nav = new Ext.FormPanel({
        labelWidth:100,
        frame:true,
        width:300,
        collapsible:true,
        defaultType:'textfield',
        items:[{
            fieldLabel: _('ID_REASSIGN_TO'),
            name:'txt_stock_in',
            allowBlank:true
        }]
    });

    var reassignPopup = new Ext.Window({
        el:'reassign-panel',
        modal:true,
        layout:'fit',
        width:300,
        height:300,
        closable:false,
        resizable:false,
        plain:true,
        items:[nav],
        buttons:[{
            text: _('ID_SUBMIT'),
            handler:function(){
                Ext.Msg.alert('OK','save ?');
                Ext.Msg.prompt(_('ID_NAME'),'please enter your name: ',function(btn,text){
                    if(btn=='ok') {
                        alert('ok');
                    }
                });
            }
        }, {
            text: _('ID_CLOSE'),
            handler:function() {
                reassignPopup.hide();
            }
        }]
    });
    // ComboBox creation
    var comboStatus = new Ext.form.ComboBox({
        width         : 80,
        boxMaxWidth   : 90,
        editable      : false,
        mode          : 'local',
        store         : new Ext.data.ArrayStore({
            fields: ['id', 'value'],
            data  : statusValues
        }),
        valueField    : 'id',
        displayField  : 'value',
        triggerAction : 'all',

        //typeAhead: true,
        //forceSelection: true,
        //emptyText: 'Select a status...',
        //selectOnFocus: true,
        //getListParent: function() {
        //  return this.el.up('.x-menu');
        //},
        listeners:{
            scope: this,
            'select': function() {
                filterStatus = comboStatus.value;
                storeCases.setBaseParam( 'filterStatus', filterStatus);
                storeCases.setBaseParam( 'start', 0);
                storeCases.setBaseParam( 'limit', pageSize);
                //storeCases.load();
            }},
        iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
    });

    // ComboBox creation for the columnSearch: caseTitle, appNumber, tasTitle
    var comboColumnSearch = new Ext.form.ComboBox({
        width         : 80,
        boxMaxWidth   : 90,
        editable      : false,
        mode          : 'local',
        store         : new Ext.data.ArrayStore({
            fields: ['id', 'value'],
            data  : columnSearchValues
        }),
        valueField    : 'id',
        displayField  : 'value',
        triggerAction : 'all',
        listeners:{
            scope: this,
            'select': function() {
                var filter = comboColumnSearch.value;
                storeCases.setBaseParam('columnSearch', filter);
            }
        },
        iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
    });

    // ComboBox creation processValues
    var userStore =  new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy( {
            url : 'casesList_Ajax?actionAjax=userValues&action='+action,
            method : 'POST'
        }),
        reader : new Ext.data.JsonReader( {
            fields : [ {
                name : 'USR_UID'
            }, {
                name : 'USR_FULLNAME'
            } ]
        })
    });

    var suggestUser = new Ext.form.ComboBox({
        store: userStore,
        valueField : 'USR_UID',
        displayField:'USR_FULLNAME',
        typeAhead: false,
        triggerAction: 'all',
        emptyText: '- ' + _('ID_ALL_USERS') + ' -',
        selectOnFocus : true,
        editable : true,
        width: 180,
        allowBlank : true,
        autocomplete: true,
        minChars: 1,
        hideTrigger:true,
        listeners:{
            scope: this,
            'select': function() {
                //storeCases.setBaseParam( 'user', comboUser.store.getAt(0).get(comboUser.valueField));
                filterUser = suggestUser.value;
                storeCases.setBaseParam( 'user', filterUser);
                storeCases.setBaseParam( 'start', 0);
                storeCases.setBaseParam( 'limit', pageSize);
                doSearch();
            }
        }
    });

    var textSearch = new Ext.form.TextField ({
        allowBlank: true,
        ctCls:'pm_search_text_field',
        width: 140,
        emptyText: _('ID_EMPTY_SEARCH'),
        listeners: {
            specialkey: function(f,e){
                if (e.getKey() == e.ENTER) {
                    doSearch();
                }
            }
        }
    });

    var btnSearch = new Ext.Button ({
        text: _('ID_SEARCH'),
        iconCls: 'button_menu_ext ss_sprite ss_page_find',
        //cls: 'x-form-toolbar-standardButton',
        handler: doSearch
    });

    function doSearch(){
        //var viewText = Ext.getCmp('casesGrid').getView();
        viewText.emptyText = _('ID_NO_RECORDS_FOUND');
        //Ext.getCmp('casesGrid').getView().refresh();

        searchText = textSearch.getValue();
        storeCases.setBaseParam('dateFrom', dateFrom.getValue());
        storeCases.setBaseParam('dateTo', dateTo.getValue());
        storeCases.setBaseParam( 'search', searchText);
        storeCases.load({params:{ start : 0 , limit : pageSize }});
    }

    var resetSearchButton = {
        text:'X',
        ctCls:"pm_search_x_button_des",
        handler: function(){
            textSearch.setValue('');
            doSearch();
        }
    }

    var resetSuggestButton = {
        text:'X',
        ctCls:"pm_search_x_button_des",
        handler: function(){
            suggestUser.setValue('');
            storeCases.setBaseParam('user', '');
            doSearch();
        }
    }

    textJump = {
        xtype: 'numberfield',
        id   : 'textJump',
        allowBlank: true,
        width: 50,
        emptyText: _('ID_CASESLIST_APP_UID'),
        listeners: {
            specialkey: function(f,e){
                if (e.getKey() == e.ENTER) {
                    // defining an id and using the Ext.getCmp method improves the accesibility of Ext components
                    caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
                    if( caseNumber )
                        jumpToCase(caseNumber);
                    else
                        msgBox(_('ID_INPUT_ERROR'), _('ID_INVALID_APPLICATION_NUMBER'), 'error');
                }
            }
        }
    };

    var btnJump = new Ext.Button ({
        text: _('ID_OPT_JUMP'),
        handler: function(){
            var caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
            if (caseNumber){
                jumpToCase(caseNumber);
            } else {
                msgBox(_('ID_INPUT_ERROR'), _('ID_INVALID_APPLICATION_NUMBER'), 'error');
            }
        }
    });

    /*** menu and toolbars **/

    function onMessageContextMenu(grid, rowIndex, e) {
        e.stopEvent();
        var coords = e.getXY();
        messageContextMenu.showAt([coords[0], coords[1]]);
        enableDisableMenuOption();
    }

    function enableDisableMenuOption(){
        var rows = grid.getSelectionModel().getSelections();
        switch(action){
            case 'todo':
                if( rows.length == 0 ) {
                    optionMenuOpen.setDisabled(true);
                    optionMenuPause.setDisabled(true);
                    optionMenuReassign.setDisabled(true);
                    optionMenuCancel.setDisabled(true);
                } else if( rows.length == 1 ) {
                    optionMenuOpen.setDisabled(false);
                    optionMenuPause.setDisabled(false);
                    optionMenuReassign.setDisabled(false);
                    optionMenuCancel.setDisabled(false);
                } else {
                    optionMenuOpen.setDisabled(true);
                    optionMenuPause.setDisabled(true);
                    optionMenuReassign.setDisabled(true);
                    optionMenuCancel.setDisabled(false);
                }
                break;
            case 'draft':
                if( rows.length == 0 ) {
                    optionMenuOpen.setDisabled(true);
                    optionMenuPause.setDisabled(true);
                    optionMenuReassign.setDisabled(true);
                    optionMenuDelete.setDisabled(true);
                } else if( rows.length == 1 ) {
                    optionMenuOpen.setDisabled(false);
                    optionMenuPause.setDisabled(false);
                    optionMenuReassign.setDisabled(false);
                    optionMenuDelete.setDisabled(false);
                } else {
                    optionMenuOpen.setDisabled(true);
                    optionMenuPause.setDisabled(true);
                    optionMenuReassign.setDisabled(true);
                    optionMenuDelete.setDisabled(false);
                }
                break;
            default:
                if( rows.length == 0 ) {
                    optionMenuOpen.setDisabled(true);
                }
                else {
                    optionMenuOpen.setDisabled(false);
                }
        }
    }

    var menuItems;
    //alert(action);
    optionMenuOpen = new Ext.Action({
        text: _('ID_OPEN'),
        handler: openCase,
        disabled: true
    });

    optionMenuUnpause = new Ext.Action({
        text: _('ID_UNPAUSE_CASE'),
        iconCls: 'ICON_CASES_UNPAUSE',
        handler: unpauseCase
    });

    optionMenuPause = new Ext.Action({
        text: _('ID_PAUSE_CASE'),
        iconCls: 'ICON_CASES_PAUSED',
        menu: new Ext.menu.DateMenu({
            //vtype: 'daterange',
            handler: function(dp, date){
                Ext.Ajax.request({
                    url : 'casesList_Ajax' ,
                    params : {actionAjax : 'verifySession'},
                    success: function ( result, request ) {
                        var data = Ext.util.JSON.decode(result.responseText);
                        if( data.lostSession ) {
                            Ext.Msg.show({
                                title: _('ID_ERROR'),
                                msg: data.message,
                                animEl: 'elId',
                                icon: Ext.MessageBox.ERROR,
                                buttons: Ext.MessageBox.OK,
                                fn : function(btn) {
                                    try
                                    {
                                        prnt = parent.parent;
                                        top.location = top.location;
                                    }
                                    catch (err)
                                    {
                                        parent.location = parent.location;
                                    }
                                }
                            });
                        } else {
                            pauseCase(date);
                        }
                    },
                    failure: function ( result, request) {
                        if (typeof(result.responseText) != 'undefined') {
                            Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                        }
                    }
                });
            }
        })
    });

    var optionMenuSummary = new Ext.Action({
        text: _('ID_SUMMARY'),
        iconCls: 'x-tree-node-icon ss_application_form',
        handler: caseSummary
    });

    optionMenuNotes = new Ext.Action({
        text: _('ID_CASES_NOTES'),
        iconCls: 'ICON_CASES_NOTES',
        handler: caseNotes
    });

    reassingCaseToUser = function()
    {
        var APP_UID = optionMenuReassignGlobal.APP_UID;
        var DEL_INDEX = optionMenuReassignGlobal.DEL_INDEX;

        var rowSelected = Ext.getCmp("grdpnlUsersToReassign").getSelectionModel().getSelected();

        if( rowSelected ) {
            if (Ext.getCmp('idTextareaReasonCasesList').getValue() === '') {
                Ext.Msg.alert(_('ID_ALERT'), _('ID_THE_REASON_REASSIGN_USER_EMPTY'));
                return;
            }
            PMExt.confirm(_('ID_CONFIRM'), _('ID_REASSIGN_CONFIRM'), function(){
                var loadMask = new Ext.LoadMask(winReassignInCasesList.getEl(), {msg: _('ID_PROCESSING')});
                loadMask.show();
                Ext.Ajax.request({
                    url : 'casesList_Ajax' ,
                    params : {actionAjax : 'reassignCase', USR_UID: rowSelected.data.USR_UID, APP_UID: APP_UID, DEL_INDEX:DEL_INDEX, NOTE_REASON: Ext.getCmp('idTextareaReasonCasesList').getValue(), NOTIFY_REASSIGN: Ext.getCmp('idCheckboxReasonCasesList').getValue()},
                    success: function ( result, request ) {
                        var data = Ext.util.JSON.decode(result.responseText);
                        if( data.status == 0 ) {
                            try {
                                parent.notify('', data.msg);
                            }
                            catch (e) {
                                // Nothing to do
                            }
                            location.href = 'casesListExtJs';
                        } else {
                            var loadMask = new Ext.LoadMask(winReassignInCasesList.getEl(), {msg: _('ID_PROCESSING')});
                            loadMask.hide();
                            winReassignInCasesList.hide();
                            alert(data.msg);
                        }
                    },
                    failure: function ( result, request) {
                        var loadMask = new Ext.LoadMask(winReassignInCasesList.getEl(), {msg: _('ID_PROCESSING')});
                        loadMask.hide();
                        winReassignInCasesList.hide();
                        if (typeof(result.responseText) != 'undefined') {
                            Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                        }
                    }
                });
            });
        }
    }

    //optionMenuPause.setMinValue('2010-11-04');

    var loadMaskUsersToReassign = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_LOADING_GRID")});

    var optionMenuReassignGlobal = {};
    optionMenuReassignGlobal.APP_UID = "";
    optionMenuReassignGlobal.DEL_INDEX = "";

    optionMenuReassign = new Ext.Action({
        text: _('ID_REASSIGN'),
        iconCls: 'ICON_CASES_TO_REASSIGN',
        handler: function() {
            Ext.Ajax.request({
                url : 'casesList_Ajax' ,
                params : {actionAjax : 'verifySession'},
                success: function ( result, request ) {
                    var data = Ext.util.JSON.decode(result.responseText);
                    if( data.lostSession ) {
                        Ext.Msg.show({
                            title: _('ID_ERROR'),
                            msg: data.message,
                            animEl: 'elId',
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.MessageBox.OK,
                            fn : function(btn) {
                                try
                                {
                                    prnt = parent.parent;
                                    top.location = top.location;
                                }
                                catch (err)
                                {
                                    parent.location = parent.location;
                                }
                            }
                        });
                    } else {
                        var casesGrid_ = Ext.getCmp('casesGrid');
                        var rowSelected = casesGrid_.getSelectionModel().getSelected();
                        var rowAllJsonArray = casesGrid_.store.reader.jsonData.data;
                        var rowSelectedIndex = casesGrid_.getSelectionModel().lastActive;
                        var rowSelectedJsonArray = rowAllJsonArray[rowSelectedIndex];

                        var TAS_UID = rowSelectedJsonArray.TAS_UID;
                        var PRO_UID = rowSelectedJsonArray.PRO_UID;
                        var USR_UID = rowSelectedJsonArray.USR_UID;

                        var APP_UID = rowSelectedJsonArray.APP_UID;
                        var DEL_INDEX = rowSelectedJsonArray.DEL_INDEX;

                        optionMenuReassignGlobal.APP_UID = APP_UID;
                        optionMenuReassignGlobal.DEL_INDEX = DEL_INDEX;

                        //Check if the user is a supervisor to this Process
                        var reassigncase = false;
                        if(varReassignCase == 'true'){
                            reassigncase = true;
                        } else if (varReassignCaseSupervisor == 'true') {
                            aProcessList= JSON.parse(data.processeslist);
                            for(var i=0; i < aProcessList.length; ++i) {
                                if(aProcessList[i] == PRO_UID){
                                    reassigncase = true;
                                }
                            }
                        }

                        if(!reassigncase) {
                            Ext.Msg.show({
                              title: _('ID_WARNING'),
                              msg: data.message,
                              animEl: 'elId',
                              icon: Ext.MessageBox.WARNING,
                              buttons: Ext.MessageBox.OK,
                              fn : function(btn) {
                              }
                            });
                        } else if( rowSelected ){
                            //Variables
                            var pageSizeUsersToReassign = 10;

                            //Stores
                            var storeUsersToReassign = new Ext.data.Store({
                                proxy: new Ext.data.HttpProxy({
                                    url: "casesList_Ajax",
                                    method: "POST"
                                }),

                                reader: new Ext.data.JsonReader({
                                    root: "resultRoot",
                                    totalProperty: "resultTotal",
                                    fields: [
                                        {name : "USR_UID"},
                                        {name : "USR_USERNAME"},
                                        {name : "USR_FIRSTNAME"},
                                        {name : "USR_LASTNAME"}
                                    ]
                                }),

                                remoteSort: true,

                                listeners: {
                                    beforeload: function (store)
                                    {
                                        winReassignInCasesList.setDisabled(true);

                                        loadMaskUsersToReassign.show();

                                        this.baseParams = {
                                            actionAjax: "getUsersToReassign",
                                            taskUid: TAS_UID,
                                            search: Ext.getCmp("txtSearchUsersToReassign").getValue(),
                                            pageSize: pageSizeUsersToReassign
                                        };
                                    },
                                    load: function (store, record, opt)
                                    {
                                        winReassignInCasesList.setDisabled(false);

                                        loadMaskUsersToReassign.hide();
                                    }
                                }
                            });

                            //Components
                            var pagingUsersToReassign = new Ext.PagingToolbar({
                                id: "pagingUsersToReassign",

                                pageSize: pageSizeUsersToReassign,
                                store: storeUsersToReassign,
                                displayInfo: true,
                                displayMsg: _("ID_DISPLAY_ITEMS"),
                                emptyMsg: _("ID_NO_RECORDS_FOUND")
                            });

                            var cmodelUsersToReassign = new Ext.grid.ColumnModel({
                                defaults: {
                                    width: 200,
                                    sortable: true
                                },
                                columns: [
                                    {id: "USR_UID",       dataIndex: "USR_UID", hidden: true, hideable: false},
                                    {id: "USR_FIRSTNAME", dataIndex: "USR_FIRSTNAME", header: _("ID_FIRSTNAME"), width: 300},
                                    {id: "USR_LASTNAME",  dataIndex: "USR_LASTNAME", header: _("ID_LASTNAME"), width: 300}
                                ]
                            });

                            var smodelUsersToReassign = new Ext.grid.RowSelectionModel({
                                singleSelect: true
                            });
                            
                            var textareaReason = new Ext.form.TextArea({
                                id: 'idTextareaReasonCasesList',
                                disabled: true,
                                fieldLabel : _('ID_REASON_REASSIGN'),
                                emptyText: _('ID_REASON_REASSIGN') + '...',
                                enableKeyEvents: true,
                                width: 200
                            });

                            var checkboxReason = new Ext.form.Checkbox({
                                id: 'idCheckboxReasonCasesList',
                                disabled: true,
                                fieldLabel : _('ID_NOTIFY_USERS_CASE'),
                                labelSeparator: '',
                                labelStyle: 'margin-left:150px;position:absolute;'
                            });

                            var grdpnlUsersToReassign = new Ext.grid.GridPanel({
                                id: "grdpnlUsersToReassign",

                                store: storeUsersToReassign,
                                colModel: cmodelUsersToReassign,
                                selModel: smodelUsersToReassign,
                                height: 200,
                                columnLines: true,
                                viewConfig: {forceFit: true},
                                enableColumnResize: true,
                                enableHdMenu: true,

                                tbar: [
                                    {
                                        text: _("ID_REASSIGN"),
                                        iconCls: "ICON_CASES_TO_REASSIGN",

                                        handler: function ()
                                        {
                                            reassingCaseToUser();
                                        }
                                    },
                                    "->",
                                    {
                                        xtype: "textfield",
                                        id: "txtSearchUsersToReassign",

                                        emptyText: _("ID_EMPTY_SEARCH"),
                                        width: 150,
                                        allowBlank: true,

                                        listeners: {
                                            specialkey: function (f, e)
                                            {
                                                if (e.getKey() == e.ENTER) {
                                                    pagingUsersToReassign.moveFirst();
                                                }
                                            }
                                        }
                                    },
                                    {
                                        text: "X",
                                        ctCls: "pm_search_x_button",

                                        handler: function ()
                                        {
                                            Ext.getCmp("txtSearchUsersToReassign").reset();
                                        }
                                    },
                                    {
                                        text: _("ID_SEARCH"),

                                        handler: function ()
                                        {
                                            pagingUsersToReassign.moveFirst();
                                        }
                                    }
                                ],
                                bbar: pagingUsersToReassign,

                                title: "",
                                listeners: {
                                    click: function () {
                                        textareaReason.enable();
                                        checkboxReason.enable();
                                    }
                                }
                            });

                            winReassignInCasesList = new Ext.Window({
                                title: '',
                                width: 450,
                                height: 350,
                                layout:'auto',
                                autoScroll:true,
                                modal: true,
                                resizable: false,
                                maximizable: false,
                                items: [{
                                        xtype: 'fieldset',
                                        labelWidth: 130,
                                        border: false,
                                        items: [
                                            textareaReason,
                                            checkboxReason
                                        ]
                                    },
                                    grdpnlUsersToReassign
                                ]
                            });

                            winReassignInCasesList.show();

                            grdpnlUsersToReassign.store.load();
                        }
                    }
                },
                failure: function ( result, request) {
                    if (typeof(result.responseText) != 'undefined') {
                        Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                    }
                }
            });
        }
    });
    optionMenuDelete = new Ext.Action({
        text: _('ID_DELETE'),
        iconCls: 'ICON_CASES_DELETE',
        handler: deleteCase
    });
    optionMenuCancel = new Ext.Action({
        text: _('ID_CANCEL'),
        iconCls: 'ICON_CASES_CANCEL',
        handler: cancelCase
    });


    switch(action){
        case 'todo':
            menuItems = [optionMenuPause, optionMenuSummary, optionMenuNotes];

            if( varReassignCase == 'true' || varReassignCaseSupervisor == 'true'){
                menuItems.push(optionMenuReassign);
            }

            break;

        case 'draft':
            menuItems = [optionMenuPause, optionMenuSummary, optionMenuNotes];
            if( varReassignCase == 'true' || varReassignCaseSupervisor == 'true'){
                menuItems.push(optionMenuReassign);
            }
            menuItems.push(optionMenuDelete);

            break;

        case 'paused':
            menuItems = [optionMenuUnpause, optionMenuSummary, optionMenuNotes];
            break;

        default:
            menuItems = [optionMenuSummary, optionMenuNotes]
    }

    contextMenuItems = new Array();
    contextMenuItems.push(optionMenuOpen);
    for (i=0; i<menuItems.length; i++) {
        contextMenuItems.push(menuItems[i]);
    }
    var messageContextMenu = new Ext.menu.Menu({
        id: 'messageContextMenu',
        items: contextMenuItems
    });

    //

    var dateFrom = new Ext.form.DateField({
        id:'dateFrom',
        format: 'Y-m-d',
        width: 120,
        editable: false,
        value: ''
    });

    var dateTo = new Ext.form.DateField({
        id:'dateTo',
        format: 'Y-m-d',
        width: 120,
        editable: false,
        value: ''
    });

    var toolbarTodo = [
        optionMenuOpen,
        {
            xtype: 'tbbutton',
            text: _('ID_ACTIONS'),
            menu: menuItems
        },

        '-',
        btnRead,
        '-',
        btnUnread,
        '-',
        btnAll,
        '->', // begin using the right-justified button container
        filterStatus.length != 0 ?[
            _('ID_OVERDUE'),
            comboFilterStatus
        ] : [
        ],
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];

    var toolbarGeneral = [
        optionMenuOpen,
        btnRead,
        '-',
        btnUnread,
        '-',
        btnAll,
        '->', // begin using the right-justified button container
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];

    var toolbarUnassigned = [
        optionMenuOpen,
        '-',
        '->', // begin using the right-justified button container
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];



    var toolbarDraft = [
        optionMenuOpen,
        {
            xtype: 'tbbutton',
            text: _('ID_ACTIONS'),
            menu: menuItems
        },
        '->',
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];

    var toolbarToRevise = [
        optionMenuOpen,
        '->', // begin using the right-justified button container
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];

    var toolbarToReassign = [
        optionMenuOpen,
        "-",
        btnSelectAll,
        btnUnSelectAll,
        "-",
        btnReassign,
        "->",
        _("ID_USER"),
        suggestUser,
        resetSuggestButton,
        "-",
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _("ID_PROCESS"),
        suggestProcess,
        resetProcessButton,
        textSearch,
        resetSearchButton,
        btnSearch,
        " ",
        " "
    ];

    var toolbarSent = [
        optionMenuOpen,
        btnStarted,
        '-',
        btnCompleted,
        '-',
        btnAll,
        '->', // begin using the right-justified button container
        _("ID_CATEGORY"),
        comboCategory,
        "-",
        _('ID_PROCESS'),
        suggestProcess,
        resetProcessButton,
        '-',
        _('ID_STATUS'),
        comboStatus,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch,
        '-',
        textJump,
        btnJump,
        ' ',
        ' '
    ];

    var clearDateFrom = new Ext.Action({
        text:  "X",
        ctCls: "pm_search_x_button_des",
        handler: function(){
            Ext.getCmp("dateFrom").setValue("");
        }
    });

    var clearDateTo = new Ext.Action({
        text:  "X",
        ctCls: "pm_search_x_button_des",
        handler: function(){
            Ext.getCmp("dateTo").setValue("");
        }
    });

    var toolbarSearch = [
        ' ',
        _('ID_DELEGATE_DATE_FROM'),
        dateFrom,
        clearDateFrom,
        ' ',
        _('ID_TO'),
        dateTo,
        clearDateTo,
        "->",
        '-',
        _('ID_FILTER_BY'),
        comboColumnSearch,
        '-',
        textSearch,
        resetSearchButton,
        btnSearch ,
        '&nbsp;&nbsp;&nbsp;'
    ];

    var firstToolbarSearch = new Ext.Toolbar({
        region: 'north',
        width: '100%',
        autoHeight: true,
        items: [
            optionMenuOpen,
            '->',
            _("ID_CATEGORY"),
            comboCategory,
            "-",
            _('ID_PROCESS'),
            suggestProcess,
            resetProcessButton,
            '-',
            _('ID_STATUS'),
            comboStatus,
            "-",
            _("ID_USER"),
            //comboUser,
            suggestUser,
            resetSuggestButton,
            '&nbsp;&nbsp;&nbsp;'
            //'-',
            //textSearch,
            //resetSearchButton,
            //btnSearch
        ]
    });
    //alert(action);
    switch (action) {
        case 'draft'      : itemToolbar = toolbarDraft; break;
        case 'sent'       : itemToolbar = toolbarSent;  break;
        case 'to_revise'  : itemToolbar = toolbarToRevise;  break;
        case 'to_reassign': itemToolbar = toolbarToReassign; break;
        case 'search'     : itemToolbar = toolbarSearch;     break;
        case 'unassigned' : itemToolbar = toolbarUnassigned; break;
        case 'gral'       : itemToolbar = toolbarGeneral;    break;
        default           : itemToolbar = toolbarTodo; break;
    }

    var tb = new Ext.Toolbar({
        height: 33,
        items: itemToolbar
    });

    var pagingToolBar;
    if (action === 'search') {
        pagingToolBar = new Ext.PagingToolbar({
            pageSize: pageSize,
            afterPageText: '',
            store: storeCases,
            displayInfo: true,
            displayMsg: '',
            emptyMsg: '',
            listeners: {
                afterlayout: function (toolbar, opts) {
                    var buttons = Ext.query('em', toolbar.el.dom);
                    //Hide the "First Page" and "Last Page" buttons
                    buttons[0].style.display = 'none';
                    buttons[3].style.display = 'none';
                }
            }
        });
    } else {
        pagingToolBar = new Ext.PagingToolbar({
            pageSize: pageSize,
            store: storeCases,
            displayInfo: true,
            //displayMsg: 'Displaying items {0} - {1} of {2} ' + ' &nbsp; ' ,
            displayMsg: _('ID_DISPLAY_ITEMS') + ' &nbsp; ',
            emptyMsg: _('ID_DISPLAY_EMPTY')
        })
    }
    var mask = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_LOADING')});
    // create the editor grid
    grid = new Ext.grid.GridPanel({
        region: 'center',
        id: 'casesGrid',
        store: storeCases,
        cm: cm,
        loadMask: mask,

        sm: new Ext.grid.RowSelectionModel({
            selectSingle: false,
            listeners:{
                selectionchange: function(sm){
                    enableDisableMenuOption();
                    // switch(sm.getCount()){
                    //   case 0: Ext.getCmp('assignButton').disable(); break;
                    //   default: Ext.getCmp('assignButton').enable(); break;
                    // }
                }
            }
        }),
        //autoHeight: true,
        layout: 'fit',
        viewConfig: {
            forceFit:true,
            cls:"x-grid-empty",
            emptyText: ( _('ID_NO_RECORDS_FOUND') )
        },
        listeners: {
            rowdblclick: openCase,
            render: function(){
                //this.loadMask = new Ext.LoadMask(this.body, {msg:TRANSLATIONS.LABEL_GRID_LOADING});
                //this.ownerCt.doLayout();
            }
        },

        tbar: tb,
        // paging bar on the bottom
        bbar: pagingToolBar
    });


    grid.on('rowcontextmenu', function (grid, rowIndex, evt) {
        var sm = grid.getSelectionModel();
        sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    }, this);
    grid.on('contextmenu', function (evt) {
        evt.preventDefault();
    }, this);

    grid.addListener('rowcontextmenu', onMessageContextMenu,this);

    // patch in order to hide the USR_UIR and PREVIOUS_USR_UID columns
    var userIndex     = grid.getColumnModel().findColumnIndex('USR_UID');
    if ( userIndex >= 0 ) grid.getColumnModel().setHidden(userIndex, true);
    var prevUserIndex = grid.getColumnModel().findColumnIndex('PREVIOUS_USR_UID');
    if ( prevUserIndex >= 0 ) grid.getColumnModel().setHidden(prevUserIndex, true);

    if (action=='to_reassign'){
        //grid.getColumnModel().setHidden(0, true);
        grid.getColumnModel().setHidden(1, true);
    }

    // create reusable renderer


    // create the editor grid
    var reassignGrid = new Ext.grid.EditorGridPanel({
        id : Ext.id(),
        region: 'center',
        store: storeReassignCases,
        cm: reassignCm,

        autoHeight: true,
        viewConfig: {
            forceFit:true
        }
    });

    var btnExecReassignSelected = new Ext.Button({
        text: _('ID_REASSIGN'),
        handler: function () {
            var rs = storeReassignCases.getModifiedRecords();
            if (rs.length < storeReassignCases.totalLength) {
                Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_TO_REASSIGN'), function (btn, text) {
                    if (btn == 'yes') {
                        if (!isValidNoteReason(rs)) {
                            return;
                        }
                        ExecReassign();
                    }
                })
            } else {
                if (!isValidNoteReason(rs)) {
                    return;
                }
                ExecReassign();
            }
        }
    });

    function isValidNoteReason(data) {
        var row, sw = true;
        for (var i = 0; i < data.length; i++) {
            row = data[i].data;
            if (!(row.APP_REASSIGN_USER_UID !== '' && row.NOTE_REASON !== undefined && row.NOTE_REASON !== '')) {
                sw = false;
            }
        }
        if (!sw) {
            Ext.Msg.alert(_('ID_ALERT'), _('ID_THE_REASON_REASSIGN_USER_EMPTY'));
        }
        return sw;
    }

    var gridForm = new Ext.FormPanel({
        id: 'reassign-form',
        border: true,
        labelAlign: 'left',
        width: 736,
        items: [{
                id: 'tasksGrid',
                columnWidth: 0.60,
                layout: 'fit',
                items: {
                    id: 'TasksToReassign',
                    xtype: 'editorgrid',
                    ds: storeReassignCases,
                    cm: reassignCm,
                    sm: new Ext.grid.RowSelectionModel({
                        singleSelect: true
                    }),
                    //autoExpandColumn: 'company',
                    height: 278,
                    title: _('ID_CASES_TO_REASSIGN_TASK_LIST'),
                    border: true,
                    listeners: {
                        click: function () {
                            rows = this.getSelectionModel().getSelections();
                            var application = '';
                            var task = '';
                            var currentUser = '';
                            comboUsersToReassign.disable();
                            if (rows.length > 0) {
                                comboUsersToReassign.enable();
                                var ids = '';
                                for (var i = 0; i < rows.length; i++) {
                                    // filtering duplicate tasks
                                    application = rows[i].get('APP_UID');
                                    task = rows[i].get('TAS_UID');
                                    currentUser = rows[i].get('USR_UID');
                                }
                            } else {
                            }
                            comboUsersToReassign.clearValue();
                            storeUsersToReassign.removeAll();
                            storeUsersToReassign.setBaseParam('application', application);
                            storeUsersToReassign.setBaseParam('task', task);
                            storeUsersToReassign.setBaseParam('currentUser', currentUser);
                            storeUsersToReassign.load();
                            //alert(record.USERS);
                        } // Allow rows to be rendered.
                    }
                }
            }
        ]
        //renderTo: bd
    });

    //Manually trigger the data store load
    switch (action) {
        case "draft":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "sent":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("status", comboStatus.store.getAt(0).get(comboStatus.valueField));
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "to_revise":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "to_reassign":
            storeCases.setBaseParam("user", "");
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "search":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("status", comboStatus.store.getAt(0).get(comboStatus.valueField));
            storeCases.setBaseParam("columnSearch", comboColumnSearch.store.getAt(0).get(comboColumnSearch.valueField));
            storeCases.setBaseParam("search", textSearch.getValue());
            storeCases.setBaseParam("dateFrom", dateFrom.getValue());
            storeCases.setBaseParam("dateTo", dateTo.getValue());
            break;
        case "unassigned":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "gral":
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        default:
            //todo
            //paused
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
    }

    storeCases.setBaseParam("action", action);
    storeCases.setBaseParam("start", 0);
    storeCases.setBaseParam("limit", pageSize);

    var viewText = Ext.getCmp('casesGrid').getView();
    storeCases.removeAll();

    if (action != "search" || __OPEN_APPLICATION_UID__ !== null) {
        storeCases.load();
    } else {
        viewText.emptyText = _('ID_ENTER_SEARCH_CRITERIA');
        storeCases.load( {params: { first: true}} );
    }

    __OPEN_APPLICATION_UID__ = null;

    //newPopUp.add(reassignGrid);
    newPopUp.add(gridForm);
    newPopUp.addButton(btnExecReassignSelected);
    //newPopUp.addButton(btnExecReassign);
    newPopUp.addButton(btnCloseReassign);

    //storeProcesses.load();

    function onItemToggle(item, pressed){
        switch ( item.id ) {
            case 'read' :
                btnUnread.toggle( false, true);
                btnAll.toggle( false, true);
                break;
            case 'unread' :
                btnRead.toggle( false, true);
                btnAll.toggle( false, true);
                break;
            case 'started' :
                btnAll.toggle( false, true);
                btnCompleted.toggle( false, true);
                break;
            case 'completed' :
                btnAll.toggle( false, true);
                btnStarted.toggle( false, true);
                break;
            case 'all' :
                btnRead.toggle( false, true);
                btnUnread.toggle( false, true);
                btnStarted.toggle( false, true);
                btnCompleted.toggle( false, true);
                break;
        }
        if(pressed){
            storeCases.setBaseParam( 'filter', item.id );
        } else {
            storeCases.setBaseParam( 'filter', '' );
        }
        storeCases.setBaseParam( 'start',  0 );
        storeCases.setBaseParam( 'limit',  pageSize );
        storeCases.load();
        //storeProcesses.load();
    }


    $configViewport = {
        layout: 'border',
        autoScroll: true,
        id:'viewportcases',
        items: [grid]
    }

    if ( action == 'search' )
        $configViewport.items.push(firstToolbarSearch);

    var viewport = new Ext.Viewport($configViewport);

    //routine to hide the debug panel if it is open
    if( typeof parent != 'undefined' ){
        if( parent.PANEL_EAST_OPEN ){
            parent.PANEL_EAST_OPEN = false;
            parent.Ext.getCmp('debugPanel').hide();
            parent.Ext.getCmp('debugPanel').ownerCt.doLayout();
        }
    }

    _nodeId = '';
    switch(action){
        case 'draft':
            _nodeId = "CASES_DRAFT";
            break;
        case 'sent':
            _nodeId = "CASES_SENT";
            break;
        case 'unassigned':
            _nodeId = "CASES_SELFSERVICE";
            break;
        case 'paused':
            _nodeId = "CASES_PAUSED";
            break;
        case 'todo':
            _nodeId = "CASES_INBOX";
            break;
    }

    try {
        if ( _nodeId != '' ){
            treePanel1 = parent.Ext.getCmp('tree-panel')
            if(treePanel1)
                node = treePanel1.getNodeById(_nodeId);
            if(node)
                node.select();
        }
    }
    catch (e) {
        // Nothing to do
    }

    try {
        parent.updateCasesTree();
    }
    catch (e) {
        // Nothing to do
    }

    comboCategory.setValue("");
    suggestProcess.setValue("");
    comboStatus.setValue("");
    comboColumnSearch.setValue("APP_TITLE");
    /*----------------------------------********---------------------------------*/
    if(typeof(comboUser) != 'undefined'){
        comboUser.setValue("");
    }

    function reassign(){
        storeReassignCases.removeAll();
        var rows  = grid.getSelectionModel().getSelections();
        storeReassignCases.rejectChanges();
        var tasks = [];
        var sw = 0;
        Ext.Ajax.request({
            url : 'proxyReassignCasesList' ,
            params : {actionAjax : 'verifySession'},
            success: function ( result, request ) {
                var data = Ext.util.JSON.decode(result.responseText);
                if( data.lostSession ) {
                    Ext.Msg.show({
                        title: _('ID_ERROR'),
                        msg: data.message,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.MessageBox.OK,
                        fn : function(btn) {
                            try
                            {
                                prnt = parent.parent;
                                top.location = top.location;
                            }
                            catch (err)
                            {
                                parent.location = parent.location;
                            }
                        }
                    });
                } else {
                    if( rows.length > 0 ) {
                        ids = '';
                        for(i=0; i<rows.length; i++) {
                            // filtering duplicate tasks

                            if( i != 0 ) ids += ',';
                            ids += rows[i].get('APP_UID') + "|" + rows[i].get('TAS_UID')+ "|" + rows[i].get('DEL_INDEX');
                        }
                        storeReassignCases.setBaseParam( 'APP_UIDS', ids);
                        //storeReassignCases.setBaseParam( 'action', 'to_reassign');
                        storeReassignCases.load();

                        newPopUp.show();
                        comboUsersToReassign.disable();

                        //grid = reassignGrid.store.data;
                        //Ext.Msg.alert ( grid );
                        /*
                         for( var i =0; i < grid.length; i++) {
                         grid[i].data.APP_UID = grid[i].data.USERS[0];
                         }
                         */
                    }
                    else {
                        Ext.Msg.show({
                            title:'',
                            msg: _('ID_NO_SELECTION_WARNING'),
                            buttons: Ext.Msg.INFO,
                            fn: function(){},
                            animEl: 'elId',
                            icon: Ext.MessageBox.INFO,
                            buttons: Ext.MessageBox.OK
                        });
                    }
                }
            },
            failure: function ( result, request) {
                if (typeof(result.responseText) != 'undefined') {
                    Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                }
            }
        });
    }

    function inArray(arr, obj) {
        for(var i=0; i<arr.length; i++) {
            if (arr[i] == obj) return true;
        }
        return false;
    }


// Add the additional 'advanced' VTypes -- [Begin]
    Ext.apply(Ext.form.VTypes, {
        daterange : function(val, field) {
            var date = field.parseDate(val);

            if(!date){
                return;
            }
            if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
                var start = Ext.getCmp(field.startDateField);
                start.setMaxValue(date);
                start.validate();
                this.dateRangeMax = date;
            }
            else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
                var end = Ext.getCmp(field.endDateField);
                end.setMinValue(date);
                end.validate();
                this.dateRangeMin = date;
            }
            /*
             * Always return true since we're only using this vtype to set the
             * min/max allowed values (these are tested for after the vtype test)
             */
            return true;
        }
    });
// Add the additional 'advanced' VTypes -- [End]


});

function msgBox(title, msg, type){
    if( typeof('type') == 'undefined' )
        type = 'info';

    switch(type){
        case 'error':
            icon = Ext.MessageBox.ERROR;
            break;
        case 'info':
        default:
            icon = Ext.MessageBox.INFO;
            break;
    }

    Ext.Msg.show({
        title: title,
        msg: msg,
        fn: function(){},
        animEl: 'elId',
        icon: icon,
        buttons: Ext.MessageBox.OK
    });
}

Ext.EventManager.on(window, 'beforeunload', function () {
    if(casesNewTab) {
        casesNewTab.close();
    }
});
