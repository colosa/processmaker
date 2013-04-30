/*
* @author: Qennix
* Feb 10th, 2011
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
      Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
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
    text: _('ID_ASSIGN_USERS'),
    iconCls: 'button_menu_ext ss_sprite ss_user_add',
    handler: EditMembersAction
  });

  cancelEditMembersButton = new Ext.Action({
    text: _('ID_CLOSE'),
    iconCls: 'button_menu_ext ss_sprite ss_cancel',
    handler: CancelEditMembersAction
    //hidden: true
  });
  
  supervisorButton = new Ext.Action({
    text: _('ID_SET_SUPERVISOR'),
    iconCls: 'button_menu_ext ss_sprite ss_user_edit',
    handler: UpdateSupervisor,
    disabled: true
  });

  backButton = new Ext.Action({
    text : _('ID_BACK'),
    iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
    handler: BackToUsers
  });
  
  contextMenu = new Ext.menu.Menu({
  	items: [supervisorButton]
  });

  storeP = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'departments_Ajax?action=assignedUsers&dUID=' + DEPARTMENT.DEP_UID
    }),
    reader : new Ext.data.JsonReader( {
      root: 'users',
      fields : [
        {name : 'USR_UID'},
        {name : 'USR_USERNAME'},
        {name : 'USR_FIRSTNAME'},
        {name : 'USR_LASTNAME'},
        {name : 'USR_SUPERVISOR'},
        {name : 'USR_STATUS'}
      ]
    })
  });

  storeA = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'departments_Ajax?action=availableUsers&dUID=' + DEPARTMENT.DEP_UID
    }),
      reader : new Ext.data.JsonReader( {
      root: 'users',
      fields : [
        {name : 'USR_UID'},
        {name : 'USR_USERNAME'},
        {name : 'USR_FIRSTNAME'},
        {name : 'USR_LASTNAME'},
        {name : 'USR_STATUS'}
      ]
    })
  });

  cmodelP = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
      {id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
      {header: _('ID_FULL_NAME'), dataIndex: 'USR_USERNAME', width: 200, align:'left', renderer: show_user},
      {header: _('ID_STATUS'), dataIndex: 'USR_STATUS', width: 100, align:'center', renderer: render_status}
    ]
  });

  smodelP = new Ext.grid.RowSelectionModel({
    selectSingle: false,
    listeners:{
      selectionchange: function(sm){
        switch(sm.getCount()){
          case 0: Ext.getCmp('removeButton').disable();
                  supervisorButton.disable();
                  break;
          case 1: Ext.getCmp('removeButton').enable();
                  supervisorButton.enable();
                  break;
          default: Ext.getCmp('removeButton').enable();
                   supervisorButton.disable();
                   break;
        }
      }
    }
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
    layout    : 'fit',
    title : _('ID_AVAILABLE_USERS'),
    region    : 'center',
    ddGroup   : 'assignedGridDDGroup',
    store     : storeA,
    cm        : cmodelP,
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
    title       : _('ID_ASSIGNED_USERS'),
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
    tbar: [editMembersButton,'-',supervisorButton,{xtype: 'tbfill'},'-',searchTextP,clearTextButtonP],
    //bbar: [{xtype: 'tbfill'},editMembersButton],
    listeners: {rowdblclick: function(){
         (availableGrid.hidden)? DoNothing() : RemoveGroupsAction();
       }
    }
  });
  
  assignedGrid.on('rowcontextmenu', 
  		function (grid, rowIndex, evt) {
      		var sm = grid.getSelectionModel();
      		sm.selectRow(rowIndex, sm.isSelected(rowIndex));
  		},
  		this
  );
  
  assignedGrid.on('contextmenu', function(evt){evt.preventDefault();}, this);
  assignedGrid.addListener('rowcontextmenu',onMessageContextMenu, this);

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
      {xtype:'button',text: '>', handler: AssignGroupsAction, id: 'assignButton', disabled: true},
      {xtype:'button',text: '&lt;', handler: RemoveGroupsAction, id: 'removeButton', disabled: true},
      {xtype:'button',text: '>>', handler: AssignAllGroupsAction, id: 'assignButtonAll', disabled: false},
      {xtype:'button',text: '&lt;&lt;', handler: RemoveAllGroupsAction, id: 'removeButtonAll', disabled: false}
    ],
    hidden: true
  });

  //GROUPS DRAG AND DROP PANEL
  UsersPanel = new Ext.Panel({
	region: 'center',
    //title     : _('ID_USERS'),
    autoWidth   : true,
    layout       : 'hbox',
    defaults     : { flex : 1 }, //auto stretch
    layoutConfig : { align : 'stretch' },
    items        : [availableGrid,buttonsPanel,assignedGrid],
    viewConfig   : {forceFit:true},
    bbar: [{xtype: 'tbfill'}]//,editMembersButton, cancelEditMembersButton]
  });

  //NORTH PANEL WITH TITLE AND ROLE DETAILS
  northPanel = new Ext.Panel({
    region: 'north',
    xtype: 'panel',
    tbar: ['<b>'+_('ID_DEPARTMENT') + ' : ' + DEPARTMENT.DEP_TITLE  + '</b>',{xtype: 'tbfill'},backButton]
  });

  //LOAD ALL PANELS
  viewport = new Ext.Viewport({
    layout: 'border',
    items: [northPanel, UsersPanel]
  });
  
  RefreshUsers();
  DDLoadUsers();

});

//Funtion Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    contextMenu.showAt([coords[0], coords[1]]);
};

//Do Nothing Function
DoNothing = function(){};

//Return to Roles Main Page
BackToUsers = function(){
  location.href = 'departments';
};

//Loads Drag N Drop Functionality for Permissions
DDLoadUsers = function(){
  //GROUPS DRAG N DROP AVAILABLE
  var availableGridDropTargetEl =  availableGrid.getView().scroller.dom;
  var availableGridDropTarget = new Ext.dd.DropTarget(availableGridDropTargetEl, {
  ddGroup    : 'availableGridDDGroup',
  notifyDrop : function(ddSource, e, data){
    var records =  ddSource.dragData.selections;
    var arrAux = new Array();
    for (var r=0; r < records.length; r++){
      if (records[r].data['USR_SUPERVISOR']==false) {
        arrAux[r] = records[r].data['USR_UID'];
      }else{
        PMExt.notify(_('ID_DEPARTMENTS'),_('ID_DELETE_SUPERVISOR'));
      };
    }
    DeleteDepartmentUser(arrAux,RefreshUsers,FailureProcess);
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
        arrAux[r] = records[r].data['USR_UID'];
      }
      SaveDepartmentUser(arrAux,RefreshUsers,FailureProcess);
      return true;
    }
  });
  sw_func_groups = true;
};

//REFRESH GROUPS GRIDS
RefreshUsers = function(){
  DoSearchA();
  DoSearchP();
};

//FAILURE AJAX FUNCTION
FailureProcess = function(){
  Ext.Msg.alert(_('ID_DEPARTMENTS'), _('ID_MSG_AJAX_FAILURE'));
};

//ASSIGN USERS TO A DEPARTMENT
SaveDepartmentUser = function(arr_usr, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'departments_Ajax',
    params: {action: 'assignDepartmentToUserMultiple', DEP_UID: DEPARTMENT.DEP_UID, USR_UID: arr_usr.join(',')},
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

//REMOVE USERS FROM A DEPARTMENT
DeleteDepartmentUser = function(arr_usr, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'departments_Ajax',
    params: {action: 'deleteDepartmentToUserMultiple', DEP_UID: DEPARTMENT.DEP_UID, USR_UID: arr_usr.join(',')},
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
    arrAux[a] = rowsSelected[a].get('USR_UID');
  }
  SaveDepartmentUser(arrAux,RefreshUsers,FailureProcess);
};

//RemoveButton Functionality
RemoveGroupsAction = function(){
  rowsSelected = assignedGrid.getSelectionModel().getSelections();
  var arrAux = new Array();
  for(var a=0; a < rowsSelected.length; a++){
    if (rowsSelected[a].get('USR_SUPERVISOR')==false) {
      arrAux[a] = rowsSelected[a].get('USR_UID');
    }else{
      PMExt.notify(_('ID_DEPARTMENTS'),_('ID_DELETE_SUPERVISOR'));
    };
  }
  DeleteDepartmentUser(arrAux,RefreshUsers,FailureProcess);
};

//AssignALLButton Functionality
AssignAllGroupsAction = function(){
  var allRows = availableGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['USR_UID'];
    }
    SaveDepartmentUser(arrAux,RefreshUsers,FailureProcess);
  }
};

//RevomeALLButton Functionality
RemoveAllGroupsAction = function(){
  var allRows = assignedGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['USR_UID'];
    }
    DeleteDepartmentUser(arrAux,RefreshUsers,FailureProcess);
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
  availableGrid.show();
  buttonsPanel.show();
  editMembersButton.disable();
  supervisorButton.disable();
  UsersPanel.doLayout();
};

//CancelEditMenbers Function
CancelEditMembersAction = function(){
  availableGrid.hide();
  buttonsPanel.hide();
  editMembersButton.enable();
  rowsSelected = assignedGrid.getSelectionModel().getSelections();
  if (rowsSelected.length == 1)
    supervisorButton.enable();
  else
	supervisorButton.disable();
  UsersPanel.doLayout();
};

//Render Full User Name
show_user = function(v,i,s){
	var sName = _FNF(v,s.data.USR_FIRSTNAME, s.data.USR_LASTNAME);
	if (s.data.USR_SUPERVISOR) sName = sName + '&nbsp;<font color="green">[' + _('ID_SUPERVISOR') + ']</font>';
	return sName;
};

//Update Department Supervisor
UpdateSupervisor = function(){
	rowsSelected = assignedGrid.getSelectionModel().getSelections();
	viewport.getEl().mask(_('ID_PROCESSING'));
	Ext.Ajax.request({
		url: 'departments_Ajax',
		params: {action: 'updateSupervisor', USR_UID: rowsSelected[0].get('USR_UID'), DEP_UID: DEPARTMENT.DEP_UID},
		success: function(r,o){
			viewport.getEl().unmask();
			supervisorButton.disable();
			DoSearchP();
			PMExt.notify(_('ID_DEPARTMENTS'),_('ID_SET_SUPERVISOR_SUCCESS'));
		},
		failure: function (r,o){
			viewport.getEl().unmask();
		}
	});
};

//Render Status
render_status = function(v){
  switch(v){
  case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
  case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>'; break;
  case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>'; break;
  }
};