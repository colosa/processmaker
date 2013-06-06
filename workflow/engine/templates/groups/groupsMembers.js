/*
 * @author: Qennix
 * Jan 27th, 2011
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

var MembersPanel;
var viewport;

var assignButton;
var assignAllButton;
var removeButton;
var removeAllButton;
var backButton;
var pageSize = 20;
var bbarpaging;


Ext.onReady(function(){
  
  editMembersButton = new Ext.Action({
      text: _('ID_ASSIGN_USERS'),
      iconCls: 'button_menu_ext ss_sprite  ss_user_add',
      handler: EditMembersAction
    });

  cancelEditMembersButton = new Ext.Action({
      text: _('ID_CLOSE'),
      iconCls: 'button_menu_ext ss_sprite ss_cancel',
      handler: CancelEditMembersAction
    });
  
  backButton = new Ext.Action({
    text: _('ID_BACK'),
    iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
    handler: BackToGroups
  });
  
  storeP = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'groups_Ajax?action=assignedMembers&gUID=' + GROUPS.GRP_UID
          }),
      reader : new Ext.data.JsonReader( {
        root: 'members',
        totalProperty: 'total_users',
        fields : [
            {name : 'USR_UID'},
            {name : 'USR_USERNAME'},
            {name : 'USR_FIRSTNAME'},
            {name : 'USR_LASTNAME'},
            {name : 'USR_EMAIL'},
            {name : 'USR_STATUS'}
        ]
      })
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
  
  storeA = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'groups_Ajax?action=availableMembers&gUID=' + GROUPS.GRP_UID
          }),
      reader : new Ext.data.JsonReader( {
        root: 'members',
        totalProperty: 'total_users',
        fields : [
            {name : 'USR_UID'},
            {name : 'USR_USERNAME'},
            {name : 'USR_FIRSTNAME'},
            {name : 'USR_LASTNAME'},
            {name : 'USR_EMAIL'},
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
            {header: _('ID_USER_NAME'), dataIndex: 'USR_USERNAME', width: 140, align:'left'},
            {header: _('ID_FIRST_NAME'), dataIndex: 'USR_FIRSTNAME', width: 180, align:'left'},
            {header: _('ID_LAST_NAME'), dataIndex: 'USR_LASTNAME', width: 180, align:'left'},
            {header: _('ID_STATUS'), dataIndex: 'USR_STATUS', width: 100, align:'center', renderer: render_status}
            
            
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
  
  storePageSize = new Ext.data.SimpleStore({
    fields   : ['size'],
    data     : [['20'],['30'],['40'],['50'],['100']],
    autoLoad : true
  });

  comboPageSizeAvailable = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store         : storePageSize,
    valueField    : 'size',
    displayField  : 'size',
    width         : 50,
    editable      : false,
    listeners     : {
      select : function(c, d, i) {
        bbarpagingAvailable.pageSize = parseInt(d.data['size']);
        bbarpagingAvailable.moveFirst();
      }
    }
  });
  comboPageSizeAvailable.setValue(pageSize);
  
  bbarpagingAvailable = new Ext.PagingToolbar({
    pageSize    : pageSize,
    store       : storeA,
    displayInfo : true,
    displayMsg  : '{0} - {1} of {2}',
    emptyMsg    : _('ID_NO_RECORDS_FOUND') ,
    items: ['-', _('ID_PAGE_SIZE')+':', comboPageSizeAvailable ]
  });

  availableGrid = new Ext.grid.GridPanel({
    layout             : 'fit',
    title              : _('ID_AVAILABLE_USERS'),
    region             : 'center',
    ddGroup            : 'assignedGridDDGroup',
    store              : storeA,
    cm                 : cmodelP,
    sm                 : smodelA,
    enableDragDrop     : true,
    stripeRows         : true,
    autoExpandColumn   : 'USR_USERNAME',
    iconCls            : 'icon-grid',
    id                 : 'availableGrid',
    height             : 100,
    autoWidth          : true,
    stateful           : true,
    stateId            : 'gridAvailable',
    enableColumnResize : true,
    enableHdMenu       : true,
    frame              : false,
    columnLines        : false,
    viewConfig : {
      forceFit  : true,
      cls       : "x-grid-empty",
      emptyText : (TRANSLATIONS.ID_NO_RECORDS_FOUND)
    },
    tbar : [cancelEditMembersButton,{xtype: 'tbfill'},'-',searchTextA,clearTextButtonA],
    //bbar: [{xtype: 'tbfill'}, assignAllButton],
    listeners : {rowdblclick: AssignUsersAction},
    hidden    : true,
    bbar      : bbarpagingAvailable
  });


  comboPageSizeAssigned = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store         : storePageSize,
    valueField    : 'size',
    displayField  : 'size',
    width         : 50,
    editable      : false,
    listeners     : {
      select : function(c, d, i) {
        bbarpagingAssigned.pageSize = parseInt(d.data['size']);
        bbarpagingAssigned.moveFirst();
      }
    }
  });
  comboPageSizeAssigned.setValue(pageSize);

  bbarpagingAssigned = new Ext.PagingToolbar({
    pageSize    : pageSize,
    store       : storeP,
    displayInfo : true,
    displayMsg  : '{0} - {1} of {2}',
    emptyMsg    : _('ID_NO_RECORDS_FOUND'),
    items: ['-', _('ID_PAGE_SIZE')+':', comboPageSizeAssigned ]
  });

  assignedGrid = new Ext.grid.GridPanel({
    layout             : 'fit',
    title              : _('ID_ASSIGNED_USERS'),
    ddGroup            : 'availableGridDDGroup',
    store              : storeP,
    cm                 : cmodelP,
    sm                 : smodelP,
    enableDragDrop     : true,
    stripeRows         : true,
    autoExpandColumn   : 'USR_USERNAME',
    iconCls            : 'icon-grid',
    id                 : 'assignedGrid',
    height             : 100,
    autoWidth          : true,
    stateful           : true,
    stateId            : 'gridAssigned',
    enableColumnResize : true,
    enableHdMenu       : true,
    frame              : false,
    columnLines        : false,
    viewConfig         : {forceFit:true},
    tbar : [editMembersButton,{xtype: 'tbfill'},'-',searchTextP,clearTextButtonP],
    //bbar: [{xtype: 'tbfill'},removeAllButton],
    listeners : {
      rowdblclick: function() {
        (availableGrid.hidden) ? DoNothing() : RemoveUsersAction();
      }
    },
    bbar : bbarpagingAssigned
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
               {xtype:'button',text: '>', handler: AssignUsersAction, id: 'assignButton', disabled: true},
               {xtype:'button',text: '&lt;', handler: RemoveUsersAction, id: 'removeButton', disabled: true},
               {xtype:'button',text: '>>', handler: AssignAllUsersAction, id: 'assignButtonAll', disabled: false},
               {xtype:'button',text: '&lt;&lt;', handler: RemoveAllUsersAction, id: 'removeButtonAll', disabled: false}
               ],
       hidden: true
    });
    
    RefreshMembers();

    //MEMBERS DRAG AND DROP PANEL
    MembersPanel = new Ext.Panel({
          region     : 'center',
        autoWidth   : true,
        layout       : 'hbox',
           defaults     : { flex : 1 }, //auto stretch
        layoutConfig : { align : 'stretch' },
        items        : [availableGrid,buttonsPanel,assignedGrid],
        viewConfig   : {forceFit:true},
        tbar: ['<b>'+_('ID_GROUP') + ' : ' + GROUPS.GRP_TITLE+'</b>' ,{xtype: 'tbfill'},backButton]
        //bbar: [{xtype: 'tbfill'},editMembersButton, cancelEditMembersButton]

    });
   
    //LOAD ALL PANELS
    viewport = new Ext.Viewport({
      layout: 'fit',
      items: [MembersPanel]
    });
    
    DDLoadUsers();  //Load DND functionality
    
});

//Do Nothing Function
DoNothing = function(){};

//Return to Groups Main Page
BackToGroups = function(){
  location.href = 'groups';
};

//Loads Drag N Drop Functionality for Users
DDLoadUsers = function(){
  //MEMBERS DRAG N DROP AVAILABLE
  var availableGridDropTargetEl =  availableGrid.getView().scroller.dom;
    var availableGridDropTarget = new Ext.dd.DropTarget(availableGridDropTargetEl, {
                    ddGroup    : 'availableGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                              arrAux[r] = records[r].data['USR_UID'];
                            }
                            DeleteGroupsUser(arrAux,RefreshMembers,FailureProcess);
                            return true;
                    }
    });
  
    //MEMBERS DRAG N DROP ASSIGNED
    var assignedGridDropTargetEl = assignedGrid.getView().scroller.dom;
    var assignedGridDropTarget = new Ext.dd.DropTarget(assignedGridDropTargetEl, {
                    ddGroup    : 'assignedGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                              arrAux[r] = records[r].data['USR_UID'];
                            }
                            SaveGroupsUser(arrAux,RefreshMembers,FailureProcess);
                            return true;
                    }
     });
};

//REFRESH GROUPS GRIDS
RefreshMembers = function(){
  DoSearchA();
  DoSearchP();
};

//FAILURE AJAX FUNCTION
FailureProcess = function(){
  Ext.Msg.alert(_('ID_GROUPS'), _('ID_MSG_AJAX_FAILURE'));
};

//ASSIGN GROUPS TO A USER
SaveGroupsUser = function(arr_usr, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'groups_Ajax',
    params: {action: 'assignUsersToGroupsMultiple', GRP_UID: GROUPS.GRP_UID, USR_UID: arr_usr.join(',')},
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

//REMOVE USERS FROM A GROUP
DeleteGroupsUser = function(arr_usr, function_success, function_failure){
  var sw_response;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'groups_Ajax',
    params: {action: 'deleteUsersToGroupsMultiple', GRP_UID: GROUPS.GRP_UID, USR_UID: arr_usr.join(',')},
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
AssignUsersAction = function(){
  rowsSelected = availableGrid.getSelectionModel().getSelections();
  var arrAux = new Array();
  for(var a=0; a < rowsSelected.length; a++){
    arrAux[a] = rowsSelected[a].get('USR_UID');
  }
  SaveGroupsUser(arrAux,RefreshMembers,FailureProcess);
};

//RemoveButton Functionality
RemoveUsersAction = function(){
  rowsSelected = assignedGrid.getSelectionModel().getSelections();
  var arrAux = new Array();
  for(var a=0; a < rowsSelected.length; a++){
    arrAux[a] = rowsSelected[a].get('USR_UID');
  }
  DeleteGroupsUser(arrAux,RefreshMembers,FailureProcess);
};

//AssignALLButton Functionality
AssignAllUsersAction = function(){
  var allRows = availableGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['USR_UID'];
    }
    SaveGroupsUser(arrAux,RefreshMembers,FailureProcess);
  }
};

//RevomeALLButton Functionality
RemoveAllUsersAction = function(){
  var allRows = assignedGrid.getStore();
  var arrAux = new Array();
  if (allRows.getCount()>0){
    for (var r=0; r < allRows.getCount(); r++){
      row = allRows.getAt(r);
      arrAux[r] = row.data['USR_UID'];
    }
    DeleteGroupsUser(arrAux,RefreshMembers,FailureProcess);
  }
};

//Function DoSearch Available
DoSearchA = function(){
    numPage = parseInt(bbarpagingAvailable.getPageData().activePage);
    availableGrid.store.setBaseParam( 'textFilter', searchTextA.getValue());
    availableGrid.store.load();
    total = parseInt(bbarpagingAvailable.getPageData().total);
    if (((numPage-1)*pageSize) >= total) {
        numPage--;
    }
    availableGrid.getBottomToolbar().changePage(numPage);
};

//Function DoSearch Assigned
DoSearchP = function(){
  assignedGrid.store.load({params: {textFilter: searchTextP.getValue()}});
};

//Load Grid By Default Available Members
GridByDefaultA = function(){
  searchTextA.reset();
  availableGrid.store.setBaseParam( 'textFilter', '');
  availableGrid.store.load();
};


//Load Grid By Default Assigned Members
GridByDefaultP = function(){
  searchTextP.reset();
  assignedGrid.store.load();
};

//edit members action
EditMembersAction = function(){
  assignedGrid.setWidth(Ext.getBody().getWidth(true));
  availableGrid.show();
  buttonsPanel.show();
  editMembersButton.disable();
  MembersPanel.doLayout();
};

//CancelEditMenbers Function
CancelEditMembersAction = function(){
  availableGrid.hide();
  buttonsPanel.hide();
  editMembersButton.enable();
  MembersPanel.doLayout();
  assignedGrid.setWidth(Ext.getBody().getWidth(true));
};

//Render Status
render_status = function(v){
  switch(v){
  case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
  case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>';; break;
  case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>';; break;
  }
};
