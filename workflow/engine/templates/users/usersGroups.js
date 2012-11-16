/*
* @author: Qennix
* Jan 25th, 2011
*/

//Keyboard Events
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
      document.location = document.location;
    }else{
      Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
    }
  }
});

var storeP;
var storeA;
var cmodelP;
var smodelA;
var smodelP;
var availableGrid;
var assignedGrid;
var GroupsPanel;
var AuthenticationPanel;
var northPanel;
var tabsPanel;
var viewport;
var assignButton;
var assignAllButton;
var removeButton;
var removeAllButton;
var backButton;
var discardChangesButton;
var saveChangesButton;
var sw_func_groups;
//var sw_func_reassign;
var sw_func_auth;
var sw_form_changed;
var sw_user_summary;

Ext.onReady(function(){
  sw_func_groups = false;
  //sw_func_reassign = false;
  sw_func_auth = false;
  sw_user_summary = false;

  editMembersButton = new Ext.Action({
    text: _('ID_ASSIGN_GROUP'),
    iconCls: 'button_menu_ext ss_sprite  ss_user_add',
    handler: EditMembersAction
  });

  cancelEditMembersButton = new Ext.Action({
    text: _('ID_CLOSE'),
    iconCls: 'button_menu_ext ss_sprite ss_cancel',
    handler: CancelEditMenbersAction
  });

  backButton = new Ext.Action({
    text: _('ID_BACK'),
    iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
    handler: BackToUsers
  });

  saveChangesButton = new Ext.Action({
    text: _('ID_SAVE_CHANGES'),
    //iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
    handler: SaveChangesAuthForm,
    disabled: true
  });

  discardChangesButton = new Ext.Action({
    text: _('ID_DISCARD_CHANGES'),
    //iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
    handler: LoadAuthForm,
    disabled: true
  });

  storeP = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'users_Ajax?function=assignedGroups&uUID=' + USERS.USR_UID
    }),
    reader : new Ext.data.JsonReader( {
      root: 'groups',
      fields : [
        {name : 'GRP_UID'},
        {name : 'GRP_STATUS'},
        {name : 'CON_VALUE'}
      ]
    })
  });

  storeA = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'users_Ajax?function=availableGroups&uUID=' + USERS.USR_UID
    }),
      reader : new Ext.data.JsonReader( {
      root: 'groups',
      fields : [
        {name : 'GRP_UID'},
        {name : 'GRP_STATUS'},
        {name : 'CON_VALUE'}
      ]
    })
  });

  cmodelP = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
      {id:'GRP_UID', dataIndex: 'GRP_UID', hidden:true, hideable:false},
      {header: _('ID_GROUP'), dataIndex: 'CON_VALUE', width: 60, align:'left'}
    ]
  });

  smodelA = new Ext.grid.RowSelectionModel({
    selectSingle: false,
    listeners:{
      selectionchange: function(sm){
        switch(sm.getCount()){
          case 0: Ext.getCmp('assignButton').disable(); break;
          default: Ext.getCmp('assignButton').enable(); break;
        }
      }
    }
  });

  smodelP = new Ext.grid.RowSelectionModel({
    selectSingle: false,
    listeners:{
      selectionchange: function(sm){
        switch(sm.getCount()){
          case 0: Ext.getCmp('removeButton').disable(); break;
          default: Ext.getCmp('removeButton').enable(); break;
        }
      }
    }
  });

  searchTextA = new Ext.form.TextField ({
    id: 'searchTextA',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 110,
    emptyText: _('ID_ENTER_SEARCH_TERM'),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearchA();
        }
      }
    }
  });

  clearTextButtonA = new Ext.Action({
    text: 'X',
    ctCls:'pm_search_x_button',
    handler: GridByDefaultA
  });

  searchTextP = new Ext.form.TextField ({
    id: 'searchTextP',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 110,
    emptyText: _('ID_ENTER_SEARCH_TERM'),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearchP();
        }
      }
    }
  });

  clearTextButtonP = new Ext.Action({
    text: 'X',
    ctCls:'pm_search_x_button',
    handler: GridByDefaultP
  });

  availableGrid = new Ext.grid.GridPanel({
    layout      : 'fit',
    title       : _('ID_AVAILABLE_GROUPS'),
    region          : 'center',
    ddGroup         : 'assignedGridDDGroup',
    store           : storeA,
    cm            : cmodelP,
    sm        : smodelA,
    enableDragDrop  : true,
    stripeRows      : true,
    autoExpandColumn: 'CON_VALUE',
    iconCls      : 'icon-grid',
    id        : 'availableGrid',
    height      : 100,
    autoWidth     : true,
    stateful     : true,
    stateId     : 'grid',
    enableColumnResize : true,
    enableHdMenu  : true,
    frame      : false,
    columnLines    : false,
    viewConfig    : {forceFit:true},
    tbar: [cancelEditMembersButton,{xtype: 'tbfill'},'-',searchTextA,clearTextButtonA],
    //bbar: [{xtype: 'tbfill'}, cancelEditMembersButton],
    listeners: {rowdblclick: AssignGroupsAction},
    hidden: true
  });

  assignedGrid = new Ext.grid.GridPanel({
    layout      : 'fit',
    title		: _('ID_ASSIGNED_GROUPS'),
    ddGroup         : 'availableGridDDGroup',
    store           : storeP,
    cm            : cmodelP,
    sm        : smodelP,
    enableDragDrop  : true,
    stripeRows      : true,
    autoExpandColumn: 'CON_VALUE',
    iconCls      : 'icon-grid',
    id        : 'assignedGrid',
    height      : 100,
    autoWidth     : true,
    stateful     : true,
    stateId     : 'grid',
    enableColumnResize : true,
    enableHdMenu  : true,
    frame      : false,
    columnLines    : false,
    viewConfig    : {forceFit:true},
    tbar: [editMembersButton,{xtype: 'tbfill'},'-',searchTextP,clearTextButtonP],
    listeners: {rowdblclick: function(){
      (availableGrid.hidden)? DoNothing() : RemoveGroupsAction();
    }}
  });

  buttonsPanel = new Ext.Panel({
    width      : 40,
    layout       : {
    type:'vbox',
    padding:'0',
    pack:'center',
    align:'center'
    },
    defaults:{margins:'0 0 35 0'},
    items:[
      {xtype:'button',text: '> ', handler: AssignGroupsAction, id: 'assignButton', disabled: true},
      {xtype:'button',text: '&lt;', handler: RemoveGroupsAction, id: 'removeButton', disabled: true},
      {xtype:'button',text: '>>', handler: AssignAllGroupsAction, id: 'assignButtonAll', disabled: false},
      {xtype:'button',text: '&lt;&lt;', handler: RemoveAllGroupsAction, id: 'removeButtonAll', disabled: false}
    ],
    hidden: true
  });

  //GROUPS DRAG AND DROP PANEL
  GroupsPanel = new Ext.Panel({
    title: _("ID_GROUPS"),
    autoWidth   : true,
    layout       : 'hbox',
    defaults     : { flex : 1 }, //auto stretch
    layoutConfig : { align : 'stretch' },
    items        : [availableGrid,buttonsPanel, assignedGrid],
    viewConfig   : {forceFit:true}
  });

  comboAuthSourcesStore = new Ext.data.GroupingStore({
    proxy : new Ext.data.HttpProxy({
      url: 'users_Ajax?function=authSources'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'sources',
      fields : [
        {id: 'AUTH_SOURCE_UID'},
        {name : 'AUTH_SOURCE_UID'},
        {name : 'AUTH_SOURCE_NAME'},
        {name : 'AUTH_SOURCE_PROVIDER'},
        {name : 'AUTH_SOURCE_SHOW'}
      ]
    }),
    autoLoad: true
  });

  //AUTHENTICATION FORM
  authForm = new Ext.FormPanel({
    url: 'users_Ajax?function=updateAuthServices',
    frame: true,
    title: _('ID_AUTHENTICATION_FORM_TITLE'),
    labelWidth: 150,
    items:[
      {xtype: 'textfield', name: 'usr_uid', hidden: true },
      {
        xtype: 'combo',
        fieldLabel: _('ID_AUTHENTICATION_SOURCE'),
        hiddenName: 'auth_source',
        name: 'auth_source_uid',
        typeAhead: true,
        mode: 'local',
        store: comboAuthSourcesStore,
        displayField: 'AUTH_SOURCE_SHOW',
        valueField:'AUTH_SOURCE_UID',
        allowBlank: false,
        submitValue: true,
        width: 350,
        //hiddenValue: 'AUTH_SOURCE_UID',
        triggerAction: 'all',
        emptyText: _('ID_SELECT_AUTH_SOURCE'),
        selectOnFocus:true,
        listeners:{
          select: function(c,r,i){
            ReportChanges();
            if (i==0){
              authForm.getForm().findField('auth_dn').disable();
            }else{
              authForm.getForm().findField('auth_dn').enable();
            }
          }
        }
      },
      {
        xtype: 'textfield',
        fieldLabel: _('ID_AUTHENTICATION_DN'),
        name: 'auth_dn',
        width: 380,
        allowBlank: true,
        enableKeyEvents: true,
        listeners: {keyup: function(f,e){ ReportChanges(); }}
      }

    ],
    buttons: [discardChangesButton,saveChangesButton]
  });

  //AUTHENTICATION EDITING PANEL
  AuthenticationPanel = new Ext.Panel({
    title     : _('ID_AUTHENTICATION'),
    autoWidth   : true,
    layout       : 'hbox',
    defaults     : { flex : 1 }, //auto stretch
    layoutConfig : { align : 'stretch' },
    items: [authForm],
    viewConfig   : {forceFit:true},
    hidden: true,
    hideLabel: true
  });

  //SUMMARY VIEW FORM

  userFields = new Ext.form.FieldSet({
    title: _('ID_USER_INFORMATION'),
    items: [
      {xtype: 'label', fieldLabel: _('ID_FIRST_NAME'), id: 'fname', width: 250},
      {xtype: 'label', fieldLabel: _('ID_LAST_NAME'), id: 'lname', width: 250},
      {xtype: 'label', fieldLabel: _('ID_USER_NAME'), id: 'uname', width: 250},
      {xtype: 'label', fieldLabel: _('ID_EMAIL'), id: 'email', width: 250},
      {xtype: 'label', fieldLabel: _('ID_ADDRESS'), id: 'address', width: 250},
      {xtype: 'label', fieldLabel: _('ID_ZIP_CODE'), id: 'zipcode', width: 250},
      {xtype: 'label', fieldLabel: _('ID_COUNTRY'), id: 'country', width: 250},
      {xtype: 'label', fieldLabel: _('ID_STATE_REGION'), id: 'state', width: 250},
      {xtype: 'label', fieldLabel: _('ID_LOCATION'), id: 'location', width: 250},
      {xtype: 'label', fieldLabel: _('ID_PHONE_NUMBER'), id: 'phone', width: 250},
      {xtype: 'label', fieldLabel: _('ID_POSITION'), id: 'position', width: 250},
      {xtype: 'label', fieldLabel: _('ID_DEPARTMENT'), id: 'department', width: 250},
      {xtype: 'label', fieldLabel: _('ID_REPLACED_BY'), id: 'replaced', width: 250},
      {xtype: 'label', fieldLabel: _('ID_EXPIRATION_DATE'), id: 'duedate', width: 250},
      {xtype: 'label', fieldLabel: _('ID_STATUS'), id: 'status', width: 250},
      {xtype: 'label', fieldLabel: _('ID_ROLE'), id: 'role', width: 250}
    ]
  });

  caseFields = new Ext.form.FieldSet({
    title: _('ID_CASES_SUMMARY'),
    labelWidth: 200,
    items: [
      {xtype: 'label', fieldLabel: _('ID_INBOX'), id: 'inbox', width: 250},
      {xtype: 'label', fieldLabel: _('ID_DRAFT'), id: 'draft', width: 250},
      {xtype: 'label', fieldLabel: _('ID_TITLE_PARTICIPATED'), id: 'participated', width: 250},
      {xtype: 'label', fieldLabel: _('ID_UNASSIGNED'), id: 'unassigned', width: 250},
      {xtype: 'label', fieldLabel: _('ID_PAUSED'), id: 'pause', width: 250}
    ]
  });

  userPhoto = new Ext.form.FieldSet({
    title: _('ID_PHOTO'),
    items: [
      {html: '<div class="thumb" align="center"><img src="users_ViewPhotoGrid?h='+Math.random()+'&pUID='+USERS.USR_UID+'"></div>'}
    ]
  });

  viewForm = new Ext.FormPanel({
    frame: true,
    //autoScroll: true,
    //autoWidth: true,
    layout: 'fit',
    items:[{
      layout: 'column',
      autoScroll: true,
      items:[
        {columnWidth:.6, padding: 3, layout: 'form', items: [userFields]},
        {columnWidth:.4, padding: 3, layout: 'form', items: [userPhoto, caseFields]}
      ]
    }],
    buttons: [
        {
            text: _("ID_EDIT"),
            handler: function () {
                location.href = "usersEdit?USR_UID=" + USERS.USR_UID + "&USR_AUTH_SOURCE=" + USERS.USR_AUTH_SOURCE + "&MODE=edit";
            }
        }
    ]
  });

  SummaryPanel = new Ext.Panel({
    title: _('ID_SUMMARY'),
    autoScroll   : true,
    layout       : 'fit',
    items: [viewForm],
    viewConfig   : {forceFit:true},
    hidden: true,
    hideLabel: true
  });

  //NORTH PANEL WITH TITLE AND ROLE DETAILS
  northPanel = new Ext.Panel({
    region: 'north',
    xtype: 'panel',
    tbar: ['<b>'+_('ID_USER') + ' : ' + parseFullName(USERS.USR_USERNAME,USERS.USR_FIRSTNAME,USERS.USR_LASTNAME,USERS.fullNameFormat) + '</b>',{xtype: 'tbfill'},backButton]
  });

  //TABS PANEL
  tabsPanelConfig =  {
    region: 'center',
    activeTab: USERS.CURRENT_TAB,
    listeners:{
      beforetabchange: function(p,t,c){
        switch(t.title){
          case _('ID_GROUPS'):
            if (sw_form_changed){
              Ext.Msg.confirm(_('ID_USERS'), _('ID_CONFIRM_DISCARD_CHANGES'),
                function(btn, text){
                  if (btn=="no"){
                    p.setActiveTab(c);
                  }else{
                    LoadAuthForm();
                  }
                });
            }
            break;
          case _('ID_SUMMARY'):
            if (sw_form_changed){
              Ext.Msg.confirm(_('ID_USERS'), _('ID_CONFIRM_DISCARD_CHANGES'),
              function(btn, text){
                if (btn=="no"){
                  p.setActiveTab(c);
                }else{
                  LoadAuthForm();
                }
              });
            }
            break;
          }
        },
      tabchange: function(p,t){
        switch(t.title){
          case _('ID_GROUPS'):
            sw_func_groups ? DoNothing() : RefreshGroups();
            sw_func_groups ? DoNothing() : DDLoadGroups();
            break;
          case _('ID_AUTHENTICATION'):
          //LoadAuthForm();
            sw_func_auth ? DoNothing() : LoadAuthForm();
            break;
          case _('ID_SUMMARY'):
            sw_user_summary ? DoNothing(): LoadSummary();
        }
      }
    }
  }
  tabsPanelConfig.items = new Array();
  tabsPanelConfig.items.push(SummaryPanel);
  tabsPanelConfig.items.push(GroupsPanel);
  if (typeof hasAuthPerm != 'undefined' && hasAuthPerm) {
    tabsPanelConfig.items.push(AuthenticationPanel);
  }

  tabsPanel = new Ext.TabPanel(tabsPanelConfig);

  //LOAD ALL PANELS
  viewport = new Ext.Viewport({
    layout: 'border',
    items: [northPanel, tabsPanel]
  });
});

//Do Nothing Function
DoNothing = function(){};

//Return to Roles Main Page
BackToUsers = function(){
  location.href = 'users_List';
};

//Loads Drag N Drop Functionality for Permissions
DDLoadGroups = function(){
  //GROUPS DRAG N DROP AVAILABLE
  var availableGridDropTargetEl = availableGrid.getView().scroller.dom;
  var availableGridDropTarget = new Ext.dd.DropTarget(availableGridDropTargetEl, {
  ddGroup    : 'availableGridDDGroup',
  notifyDrop : function(ddSource, e, data){
    var records =  ddSource.dragData.selections;
    var arrAux = new Array();
    for (var r=0; r < records.length; r++){
      arrAux[r] = records[r].data['GRP_UID'];
    }
    DeleteGroupsUser(arrAux,RefreshGroups,FailureProcess);
    return true;
  }
});

  //GROUPS DRAG N DROP ASSIGNED
  var assignedGridDropTargetEl = assignedGrid.getView().scroller.dom;
  var assignedGridDropTarget = new Ext.dd.DropTarget(assignedGridDropTargetEl, {
    ddGroup    : 'assignedGridDDGroup',
    notifyDrop : function(ddSource, e, data){
      var records =  ddSource.dragData.selections;
      var arrAux = new Array();
      for (var r=0; r < records.length; r++){
        arrAux[r] = records[r].data['GRP_UID'];
      }
      SaveGroupsUser(arrAux,RefreshGroups,FailureProcess);
      return true;
    }
  });
  sw_func_groups = true;
};

//LOAD AUTHTENTICATION FORM DATA
LoadAuthForm = function(){
  Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'loadAuthSourceByUID', uUID: USERS.USR_UID},
    success: function(resp, opt){
      var user = Ext.util.JSON.decode(resp.responseText);
      authForm.getForm().findField('usr_uid').setValue(user.data.USR_UID);
      authForm.getForm().findField('auth_source').setValue(user.auth.AUTH_SOURCE_NAME);
      authForm.getForm().findField('auth_dn').setValue(user.data.USR_AUTH_USER_DN);
      if (user.auth.AUTH_SOURCE_NAME=='ProcessMaker'){
        authForm.getForm().findField('auth_dn').disable();
      }else{
        authForm.getForm().findField('auth_dn').enable();
      }
    },
    failure: DoNothing
  });
  sw_func_auth = true;
  sw_form_changed = false;
  saveChangesButton.disable();
  discardChangesButton.disable();
};

//ReportChanges
ReportChanges = function(){
  saveChangesButton.enable();
  discardChangesButton.enable();
  sw_form_changed = true;
};


//REFRESH GROUPS GRIDS
RefreshGroups = function(){
  DoSearchA();
  DoSearchP();
};

//SAVE AUTHENTICATION CHANGES

SaveChangesAuthForm = function(){
  viewport.getEl().mask(_('ID_PROCESSING'));
  authForm.getForm().submit({
    success: function(f,a){
      LoadAuthForm();
      viewport.getEl().unmask();
    },
    failure: function(f,a){
      FailureProcess();
      viewport.getEl().unmask();
    }
  });
};

//FAILURE AJAX FUNCTION
FailureProcess = function(){
  Ext.Msg.alert(_('ID_USERS'), _('ID_MSG_AJAX_FAILURE'));
};

//ASSIGN GROUPS TO A USER
SaveGroupsUser = function(arr_grp, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'assignGroupsToUserMultiple', USR_UID: USERS.USR_UID, GRP_UID: arr_grp.join(',')},
    success: function(){
      function_success();
      viewport.getEl().unmask();
    },
    failure: function(){
      function_failure();
      viewport.getEl().unmask();
    }
  });
};

//REMOVE GROUPS FROM A USER
DeleteGroupsUser = function(arr_grp, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'deleteGroupsToUserMultiple', USR_UID: USERS.USR_UID, GRP_UID: arr_grp.join(',')},
    success: function(){
      function_success();
      viewport.getEl().unmask();
    },
    failure: function(){
      function_failure();
      viewport.getEl().unmask();
    }
  });
};

//AssignButton Functionality
AssignGroupsAction = function(){
  rowsSelected = availableGrid.getSelectionModel().getSelections();
  var arrAux = new Array();
  for(var a=0; a < rowsSelected.length; a++){
    arrAux[a] = rowsSelected[a].get('GRP_UID');
  }
  SaveGroupsUser(arrAux,RefreshGroups,FailureProcess);
};

//RemoveButton Functionality
RemoveGroupsAction = function(){
  rowsSelected = assignedGrid.getSelectionModel().getSelections();
  var arrAux = new Array();
  for(var a=0; a < rowsSelected.length; a++){
    arrAux[a] = rowsSelected[a].get('GRP_UID');
  }
  DeleteGroupsUser(arrAux,RefreshGroups,FailureProcess);
};

//AssignALLButton Functionality
AssignAllGroupsAction = function(){
  var allRows = availableGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['GRP_UID'];
    }
    SaveGroupsUser(arrAux,RefreshGroups,FailureProcess);
  }
};

//RevomeALLButton Functionality
RemoveAllGroupsAction = function(){
  var allRows = assignedGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['GRP_UID'];
    }
    DeleteGroupsUser(arrAux,RefreshGroups,FailureProcess);
  }
};

//Function DoSearch Available
DoSearchA = function(){
  availableGrid.store.load({params: {textFilter: searchTextA.getValue()}});
};

//Function DoSearch Assigned
DoSearchP = function(){
  assignedGrid.store.load({params: {textFilter: searchTextP.getValue()}});
};

//Load Grid By Default Available Members
GridByDefaultA = function(){
  searchTextA.reset();
  availableGrid.store.load();
};

//Load Grid By Default Assigned Members
GridByDefaultP = function(){
  searchTextP.reset();
  assignedGrid.store.load();
};

//edit members action
EditMembersAction = function(){
  //if (editMembersButton.pressed){
	availableGrid.show();
	buttonsPanel.show();
	editMembersButton.disable();
	GroupsPanel.doLayout();
  //}else{
  //  availableGrid.hide();
  //  buttonsPanel.hide();
  //  GroupsPanel.doLayout();
  //}
};

//CancelEditMenbers Function
CancelEditMenbersAction = function(){
  availableGrid.hide();
  buttonsPanel.hide();
  editMembersButton.enable();
  //cancelEditMembersButton.hide();
  GroupsPanel.doLayout();
};

//Function Parse Full Name Format
parseFullName = function(uN, fN, lN, f){
  var aux = f;
  aux = aux.replace('@userName',uN);
  aux = aux.replace('@firstName',fN);
  aux = aux.replace('@lastName',lN);
  return aux;
};

//Load Summary Function
LoadSummary = function(){
  //viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'summaryUserData', USR_UID: USERS.USR_UID},
    success: function(r,o){
      //viewport.getEl().unmask();
      sw_user_summary = true;
      var user = Ext.util.JSON.decode(r.responseText);
      Ext.getCmp('fname').setText(user.userdata.USR_FIRSTNAME);
      Ext.getCmp('lname').setText(user.userdata.USR_LASTNAME);
      Ext.getCmp('uname').setText(user.userdata.USR_USERNAME);
      Ext.getCmp('email').setText(user.userdata.USR_EMAIL);
      Ext.getCmp('country').setText(user.userdata.USR_COUNTRY_NAME);
      Ext.getCmp('state').setText(user.userdata.USR_CITY_NAME);
      Ext.getCmp('location').setText(user.userdata.USR_LOCATION_NAME);
      Ext.getCmp('role').setText(user.userdata.USR_ROLE);
      Ext.getCmp('address').setText(user.userdata.USR_ADDRESS);
      Ext.getCmp('phone').setText(user.userdata.USR_PHONE);
      Ext.getCmp('zipcode').setText(user.userdata.USR_ZIP_CODE);
      Ext.getCmp('duedate').setText(user.userdata.USR_DUE_DATE);
      Ext.getCmp('status').setText(user.userdata.USR_STATUS);
      Ext.getCmp('replaced').setText(user.misc.REPLACED_NAME);
      Ext.getCmp('department').setText(user.misc.DEP_TITLE);
      Ext.getCmp('position').setText(user.userdata.USR_POSITION);

      Ext.getCmp('inbox').setText(user.cases.to_do);
      Ext.getCmp('draft').setText(user.cases.draft);
      Ext.getCmp('participated').setText(user.cases.sent);
      Ext.getCmp('unassigned').setText(user.cases.selfservice);
      Ext.getCmp('pause').setText(user.cases.paused);

    },
    failure:function(r,o){
      //viewport.getEl().unmask();
    }
  });
};