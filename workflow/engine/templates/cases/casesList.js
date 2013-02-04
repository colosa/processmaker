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
    openCaseNotesWindow(appUid,true,caseTitle,proid,task);
  }else{
    msgBox(_('ID_INFORMATION'), _('ID_SELECT_ONE_AT_LEAST') );
  }
}
function openCase(){

    var rowModel = grid.getSelectionModel().getSelected();
    if(rowModel){
      var appUid   = rowModel.data.APP_UID;
      var delIndex = rowModel.data.DEL_INDEX;
      var caseTitle = (rowModel.data.APP_TITLE) ? rowModel.data.APP_TITLE : rowModel.data.APP_UID;

      Ext.Msg.show({
        msg: _('ID_OPEN_CASE') + ' ' + caseTitle,
        width:300,
        wait:true,
        waitConfig: {interval:200}
      });
      params = '';
      switch(action){
        case 'to_revise':
          params += 'APP_UID=' + appUid;
          params += '&DEL_INDEX=' + delIndex;
          params += '&to_revise=true';
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
      redirect(requestFile + '?' + params);

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
        requestFile = '../cases/open';
        redirect(requestFile + '?' + params);
      } else {
        Ext.MessageBox.hide();
        var message = new Array();
        message['CASE_NUMBER'] = appNumber;
        msgBox(_('ID_INPUT_ERROR'), _('ID_CASE_DOES_NOT_EXIST_JS', appNumber), 'error');
      }
    },
    params: {action:'previusJump', appNumber: appNumber}
  });
}

function deleteCase() {
  var rows = grid.getSelectionModel().getSelections();
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
                parent.updateCasesView();
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
              text: 'Ok',
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
                        parent.updateCasesView();
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
                    params: {action:'pauseCase', unpausedate:unpauseDate, APP_UID:rowModel.data.APP_UID, DEL_INDEX: rowModel.data.DEL_INDEX, NOTE_REASON: noteReasonTxt, NOTIFY_PAUSE: notifyReasonVal}
                  });
              }
          },{
              text: 'Cancel', //COCHATRA
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
                parent.updateCasesView();
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
    Ext.MessageBox.show({ progressText: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
    Ext.Ajax.request({
      url: 'cases_Ajax',
      success: function(response) {
        try {
          parent.updateCasesView();
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
      Ext.MessageBox.show({ progressText: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
      Ext.Ajax.request({
        url: 'cases_Ajax',
        success: function(response) {
          try {
            parent.updateCasesView();
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
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

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
    pro = r.json.PRO_UID;
    tas = r.json.TAS_UID;

    appUid = r.data['APP_UID'];
    title  = r.data['APP_TITLE'];
    return '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ICON_CASES_NOTES" unselectable="off" id="extdd-17" onClick="openCaseNotesWindow(\''+appUid+'\', true, \''+title+'\', \''+pro+'\', \''+tas+'\')">';
  }

  //Render Full Name
  full_name = function(v, x, s) {
      if (s.data.USR_UID) {
        return _FNF(s.data.USR_USERNAME, s.data.USR_FIRSTNAME, s.data.USR_LASTNAME);
      }
      else {
        return '[' + _('ID_UNASSIGNED').toUpperCase() + ']';
      }
  };

  previous_full_name = function(v, x, s) {
      if (s.data.PREVIOUS_USR_UID) {
        return _FNF(s.data.PREVIOUS_USR_USERNAME, s.data.PREVIOUS_USR_FIRSTNAME, s.data.PREVIOUS_USR_LASTNAME);
      }
      else {
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
    if (solrEnabled == 0) {
        if( c.dataIndex == 'APP_DEL_PREVIOUS_USER') c.renderer = previous_full_name;
        if( c.dataIndex == 'APP_CURRENT_USER')      c.renderer = full_name;
    }
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
      columns: columns
    });

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
      read :   'proxyCasesList'
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
    sortInfo:{field: 'APP_CACHE_VIEW.APP_NUMBER', direction: "DESC"},
    listeners: {
    	load: function(response){
    		//console.log(response.reader.jsonData);
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
        url : 'casesList_Ajax' ,
        params : {actionAjax : 'processListExtJs',
        action: action,
        CATEGORY_UID: filterCategory},
        success: function ( result, request ) {
            processValues = Ext.util.JSON.decode(result.responseText);
            comboProcess.getStore().removeAll();
            comboProcess.getStore().loadData(processValues);
        },
        failure: function ( result, request) {
            if (typeof(result.responseText) != 'undefined') {
                Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
            }
        }
    });

  var comboProcess = new Ext.form.ComboBox({
    width         : 180,
    boxMaxWidth   : 200,
    editable      : true,
    displayField  : 'APP_PRO_TITLE',
    valueField    : 'PRO_UID',
    forceSelection: false,
    emptyText: _('ID_EMPTY_PROCESSES'),
    selectOnFocus: true,
    tpl: resultTpl,

    typeAhead: true,
    mode: 'local',
    autocomplete: true,
    triggerAction: 'all',

    store         : new Ext.data.ArrayStore({
      fields : ['PRO_UID','APP_PRO_TITLE'],
      data   : processValues
    }),
    listeners:{
      scope: this,
      'select': function() {
        filterProcess = comboProcess.value;
        if ( action == 'search' ){
          storeCases.setBaseParam('dateFrom', dateFrom.getValue());
          storeCases.setBaseParam('dateTo', dateTo.getValue());
        }
        storeCases.setBaseParam('process', filterProcess);
        storeCases.load({params:{process: filterProcess, start : 0 , limit : pageSize}});
      }},
    iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
  });

  var comboAllUsers = new Ext.form.ComboBox({
    width         : 180,
    boxMaxWidth   : 180,
    editable      : false,
    displayField  : 'USR_FULLNAME',
    valueField    : 'USR_UID',
    //typeAhead     : true,
    mode          : 'local',
    forceSelection: true,
    triggerAction: 'all',

    emptyText: _('ID_EMPTY_USERS'),
    selectOnFocus: true,
    //getListParent: function() {
    //  return this.el.up('.x-menu');
    //},
    store         : new Ext.data.ArrayStore({
      fields: ['USR_UID','USR_FULLNAME'],
      data  : allUsersValues
    }),
    listeners:{
      scope: this,
      'select': function() {
        filterProcess = comboAllUsers.value;

        /*if (filterProcess==''){
          btnSelectAll.hide();
          btnUnSelectAll.hide();
          btnReassign.hide();
        }
        else  {
          btnSelectAll.show();
          btnUnSelectAll.show();
          btnReassign.show();
        }*/
        storeCases.setBaseParam( 'user', filterProcess);
        storeCases.load({params:{user: filterProcess, start : 0 , limit : pageSize}});
      }},
    iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
  });

    var comboCategory = new Ext.form.ComboBox({
        width           : 180,
        boxMaxWidth     : 200,
        editable        : true,
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
            storeCases.load({params:{category: filterCategory, start : 0 , limit : pageSize}});

            Ext.Ajax.request({
                url : 'casesList_Ajax' ,
                params : {actionAjax : 'processListExtJs',
                action: action,
                CATEGORY_UID: filterCategory},
                success: function ( result, request ) {
                    var data = Ext.util.JSON.decode(result.responseText);
                    comboProcess.getStore().removeAll();
                    comboProcess.getStore().loadData( data );
                    comboProcess.setValue('');

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
        Ext.Msg.prompt('Name','please enter your name: ',function(btn,text){
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
        storeCases.setBaseParam( 'status', filterStatus);
        storeCases.setBaseParam( 'start', 0);
        storeCases.setBaseParam( 'limit', pageSize);
        storeCases.load();
      }},
    iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
  });

  // ComboBox creation processValues
  var comboUser = new Ext.form.ComboBox({
    width         : 160,
    boxMaxWidth   : 180,
    editable      : true,
    displayField  : 'USR_FULLNAME',
    valueField    : 'USR_UID',
    mode          : 'local',
    forceSelection: false,
    emptyText: _('ID_SELECT'),
    selectOnFocus: true,

    typeAhead: true,
    mode: 'local',
    autocomplete: true,
    triggerAction: 'all',

    store         : new Ext.data.ArrayStore({
      fields: ['USR_UID','USR_FULLNAME'],
      data  : userValues
    }),
    listeners:{
      scope: this,
      'select': function() {
        filterUser = comboUser.value;
        storeCases.setBaseParam( 'user', filterUser);
        storeCases.setBaseParam( 'start', 0);
        storeCases.setBaseParam( 'limit', pageSize);
        storeCases.load();
      }},
    iconCls: 'no-icon'  //use iconCls if placing within menu to shift to right side of menu
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
    handler: doSearch
  });

  function doSearch(){
    searchText = textSearch.getValue();
    storeCases.setBaseParam( 'search', searchText);
    storeCases.load({params:{ start : 0 , limit : pageSize }});
  }

  var resetSearchButton = {
    text:'X',
	  ctCls:'pm_search_x_button',
    handler: function(){
      textSearch.setValue('');
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
        pauseCase(date);
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

    var rowSelected = Ext.getCmp('reassignGrid').getSelectionModel().getSelected();
    if( rowSelected ) {
      PMExt.confirm(_('ID_CONFIRM'), _('ID_REASSIGN_CONFIRM'), function(){
        Ext.Ajax.request({
          url : 'casesList_Ajax' ,
          params : {actionAjax : 'reassignCase', USR_UID: rowSelected.data.USR_UID, APP_UID: APP_UID, DEL_INDEX:DEL_INDEX},
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
              alert(data.msg);
            }
          },
          failure: function ( result, request) {
            if (typeof(result.responseText) != 'undefined') {
              Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
            }
          }
        });
      });
    }
  }

  reassingCaseToUser = function()
  {
    var APP_UID = optionMenuReassignGlobal.APP_UID;
    var DEL_INDEX = optionMenuReassignGlobal.DEL_INDEX;

    var rowSelected = Ext.getCmp('reassignGrid').getSelectionModel().getSelected();
    if( rowSelected ) {
      PMExt.confirm(_('ID_CONFIRM'), _('ID_REASSIGN_CONFIRM'), function(){
        Ext.Ajax.request({
          url : 'casesList_Ajax' ,
          params : {actionAjax : 'reassignCase', USR_UID: rowSelected.data.USR_UID, APP_UID: APP_UID, DEL_INDEX:DEL_INDEX},
          success: function ( result, request ) {
            var data = Ext.util.JSON.decode(result.responseText);
            if( data.status == 0 ) {
              parent.notify('', data.msg);
              location.href = 'casesListExtJs';
            } else {
              alert(data.msg);
            }
          },
          failure: function ( result, request) {
            if (typeof(result.responseText) != 'undefined') {
              Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
            }
          }
        });
      });
    }
  }
  //optionMenuPause.setMinValue('2010-11-04');
  var optionMenuReassignGlobal = {};
  optionMenuReassignGlobal.APP_UID = "";
  optionMenuReassignGlobal.DEL_INDEX = "";

  optionMenuReassign = new Ext.Action({
    text: _('ID_REASSIGN'),
    iconCls: 'ICON_CASES_TO_REASSIGN',
    handler: function() {

    var casesGrid_ = Ext.getCmp('casesGrid');
    var rowSelected = casesGrid_.getSelectionModel().getSelected();
    var rowAllJsonArray = casesGrid_.store.reader.jsonData.data;
    var rowSelectedIndex = casesGrid_.getSelectionModel().lastActive;
    var rowSelectedJsonArray = rowAllJsonArray[rowSelectedIndex];

    var TAS_UID = rowSelectedJsonArray.TAS_UID;
    var USR_UID = rowSelectedJsonArray.USR_UID;

    var APP_UID = rowSelectedJsonArray.APP_UID;
    var DEL_INDEX = rowSelectedJsonArray.DEL_INDEX;

    optionMenuReassignGlobal.APP_UID = APP_UID;
    optionMenuReassignGlobal.DEL_INDEX = DEL_INDEX;
      if( rowSelected ){
        var store = new Ext.data.Store( {
          autoLoad: true,
          proxy : new Ext.data.HttpProxy({
            url: 'casesList_Ajax?actionAjax=getUsersToReassign&TAS_UID='+TAS_UID
          }),
          reader : new Ext.data.JsonReader( {
            root: 'data',
            fields : [
              {name : 'USR_UID'},
              {name : 'PRO_USERNAME'},
              {name : 'USR_FIRSTNAME'},
              {name : 'PRO_LASTNAME'}
            ]
          })
        });

        var grid = new Ext.grid.GridPanel( {
          id: 'reassignGrid',
          height:300,
          width:'300',
          title : '',
          stateful : true,
          stateId : 'grid',
          enableColumnResize: true,
          enableHdMenu: true,
          frame:false,
          cls : 'grid_with_checkbox',
          columnLines: true,

          viewConfig: {
            forceFit:true
          },

          cm: new Ext.grid.ColumnModel({
            defaults: {
                width: 200,
                sortable: true
            },
            columns: [
              {id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
              {header: _('ID_FIRSTNAME'), dataIndex: 'USR_FIRSTNAME', width: 300},
              {header: _('ID_LASTNAME'), dataIndex: 'USR_LASTNAME', width: 300}
            ]
          }),

          store: store,

          tbar:[
            {
              text:_('ID_REASSIGN'),
              iconCls: 'ICON_CASES_TO_REASSIGN',
              handler: function(){
                //Actions.reassignCase
                 reassingCaseToUser();
              }
            }
          ],
          listeners: {
            //rowdblclick: openCase,
            render: function(){
              this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING')});
              this.ownerCt.doLayout();
            }
          }
        });

        var win = new Ext.Window({
          title: '',
          width: 450,
          height: 280,
          layout:'fit',
          autoScroll:true,
          modal: true,
          maximizable: false,
          items: [grid]
        });
        win.show();
      }
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

      if( ___p34315105.search('R') != -1 )
        menuItems.push(optionMenuReassign);

      break;

      case 'draft':
      menuItems = [optionMenuPause, optionMenuSummary, optionMenuNotes];
      if( ___p34315105.search('R') != -1 )
        menuItems.push(optionMenuReassign);
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
    value: ''
  });

  var dateTo = new Ext.form.DateField({
    id:'dateTo',
    format: 'Y-m-d',
    width: 120,
    value: ''
  });

  var toolbarTodo = [
    optionMenuOpen,
    {
      xtype: 'tbsplit',
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
    _("ID_CATEGORY"),
    comboCategory,
    "-",
    _('ID_PROCESS'),
    comboProcess,
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
    comboProcess,
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
    comboProcess,
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
      xtype: 'tbsplit',
      text: _('ID_ACTIONS'),
      menu: menuItems
    },
    '->',
    _("ID_CATEGORY"),
    comboCategory,
    "-",
    _('ID_PROCESS'),
    comboProcess,
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
    comboProcess,
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
      comboAllUsers,
      "-",
      _("ID_CATEGORY"),
      comboCategory,
      "-",
      _("ID_PROCESS"),
      comboProcess,
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
    comboProcess,
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



  var toolbarSearch = [
      ' ',
      _('ID_DELEGATE_DATE_FROM'),
      dateFrom,
      ' ',
      _('ID_TO'),
      dateTo,
      new Ext.Button ({
        text: _('ID_FILTER_BY_DELEGATED_DATE'),
        handler: function(){
          storeCases.setBaseParam('dateFrom', dateFrom.getValue());
          storeCases.setBaseParam('dateTo', dateTo.getValue());
          storeCases.load({params:{ start : 0 , limit : pageSize }});
        }
      })
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
      comboProcess,
      '-',
      _('ID_STATUS'),
      comboStatus,
      "-",
      _("ID_USER"),
      comboUser,
      '-',
      textSearch,
      resetSearchButton,
      btnSearch
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


  // create the editor grid
  grid = new Ext.grid.GridPanel({
    region: 'center',
    id: 'casesGrid',
    store: storeCases,
    cm: cm,

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
    bbar: new Ext.PagingToolbar({
      pageSize: pageSize,
      store: storeCases,
      displayInfo: true,
      //displayMsg: 'Displaying items {0} - {1} of {2} ' + ' &nbsp; ' ,
      displayMsg: _('ID_DISPLAY_ITEMS') + ' &nbsp; ',
      emptyMsg: _('ID_DISPLAY_EMPTY')
    })
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

  var btnExecReassignSelected = new Ext.Button ({
    text: _('ID_REASSIGN'),
    handler: function(){
      var rs = storeReassignCases.getModifiedRecords();
      if (rs.length < storeReassignCases.totalLength) {
        Ext.Msg.confirm( _('ID_CONFIRM'), _('ID_CONFIRM_TO_REASSIGN'), function (btn, text) {
          if ( btn == 'yes' ) {
            ExecReassign();
          }
        })
      } else {
        ExecReassign();
      }
    }
  });

var gridForm = new Ext.FormPanel({
        id: 'reassign-form',
        frame: true,
        labelAlign: 'left',
        //title: 'Company data',
        bodyStyle:'padding:5px',
        width: 750,


        layout: 'column',    // Specifies that the items will now be arranged in columns
        items: [{
            id : 'tasksGrid',
            columnWidth: 0.60,
            layout: 'fit',
            items: {
                id: 'TasksToReassign',
                xtype: 'grid',
                ds: storeReassignCases,
                cm: reassignCm,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                    /*listeners: {
                        rowselect: function(sm, row, rec) {
                            Ext.getCmp("reassign-form").getForm().loadRecord(rec);
                        }
                    }*/
                }),
                //autoExpandColumn: 'company',
                height: 350,
                title  : _('ID_CASES_TO_REASSIGN_TASK_LIST'),
                border : true,

                listeners: {

                    click: function() {
                        rows = this.getSelectionModel().getSelections();
                        var application = '';
                        var task = '';
                        var currentUser = '';
                        comboUsersToReassign.disable();
                        if( rows.length > 0 ) {
                          comboUsersToReassign.enable();
                            var ids = '';
                            for(i=0; i<rows.length; i++) {
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
        },{
            columnWidth: 0.4,
            xtype: 'fieldset',
            labelWidth: 50,
            title: _('ID_USER_LIST'),
            defaults: {width: 200, border:false},    // Default config options for child items
            defaultType: 'textfield',
            autoHeight: true,
            bodyStyle: Ext.isIE ? 'text-align: left;padding:0 0 5px 15px;' : 'text-align: left; padding:10px 5px;',
            border: false,
            //style: {
            //    "margin-left": "10px", // when you add custom margin in IE 6...
            //    "margin-right": Ext.isIE6 ? (Ext.isStrict ? "-10px" : "-13px") : "0"  // you have to adjust for it somewhere else
            //},
            items:
              [
                comboUsersToReassign,
                {
                  xtype: 'fieldset',
                  border : true,
                  defaultType: 'textfield',
                  title: _('ID_INSTRUCTIONS'),
                  autoHeight:true,
                  html: _('ID_INSTRUCTIONS_TEXT')
                }
              ]
        }]
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
            storeCases.setBaseParam("user", comboAllUsers.store.getAt(0).get(comboAllUsers.valueField));
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("search", textSearch.getValue());
            break;
        case "search":
            storeCases.setBaseParam("category", "");
            storeCases.setBaseParam("process", "");
            storeCases.setBaseParam("status", comboStatus.store.getAt(0).get(comboStatus.valueField));
            storeCases.setBaseParam("user", comboUser.store.getAt(0).get(comboUser.valueField));
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

    if (action != 'search') {
        storeCases.load();
    } else {
        storeCases.load( {params: { first: true}} );
        PMExt.notify_time_out = 5;
        PMExt.notify(_('ID_ADVANCEDSEARCH'), _('ID_ENTER_SEARCH_CRITERIA'));
    }
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
  comboProcess.setValue("");
  comboStatus.setValue("");
  comboUser.setValue("");
  comboAllUsers.setValue("CURRENT_USER");

  // hidding the buttons for the reassign
//  if (action=='to_reassign'){
//    btnSelectAll.hide();
//    btnUnSelectAll.hide();
//    btnReassign.hide();
//  }


function reassign(){
  storeReassignCases.removeAll();
  var rows  = grid.getSelectionModel().getSelections();
  storeReassignCases.rejectChanges();
  var tasks = [];
  var sw = 0;
  if( rows.length > 0 ) {
    ids = '';
    for(i=0; i<rows.length; i++) {
      // filtering duplicate tasks

         if( i != 0 ) ids += ',';
         ids += rows[i].get('APP_UID') + "|" + rows[i].get('TAS_UID')+ "|" + rows[i].get('DEL_INDEX');
    }
    storeReassignCases.setBaseParam( 'APP_UIDS', ids);
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

