/*
 * @author: Qennix
 * Jan 27th, 2011
 */

//Keyboard Events
new Ext.KeyMap(document,
    [
     {
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
     }
     ,
     {
       key: Ext.EventObject.DELETE,
       fn: function(k,e){
         iGrid = Ext.getCmp('infoGrid');
         rowSelected = iGrid.getSelectionModel().getSelected();
         if (rowSelected){
           DeleteButtonAction();
         }
       }
     },
     {
       key: Ext.EventObject.F2,
       fn: function(k,e){
         iGrid = Ext.getCmp('infoGrid');
         rowSelected = iGrid.getSelectionModel().getSelected();
         if (rowSelected){
           EditGroupWindow();
         }
       }
     }
     ]);

var store;
var cmodel;
var infoGrid;
var viewport;
var smodel;

var newButton;
var editButton;
var deleteButton;
var searchButton;

var searchText;
var contextMenu;
var pageSize;
var w;

Ext.onReady(function(){
  Ext.QuickTips.init();

  pageSize = parseInt(CONFIG.pageSize);

  newButton = new Ext.Action({
    text: _('ID_NEW'),
    iconCls: 'button_menu_ext ss_sprite  ss_add',
    handler: NewGroupWindow
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
    iconCls: 'button_menu_ext ss_sprite  ss_pencil',
    handler: EditGroupWindow,
    disabled: true
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite  ss_delete',
    handler: DeleteButtonAction,
    disabled: true
  });

  membersButton = new Ext.Action({
    text: _('ID_USERS'),
    iconCls: 'button_menu_ext ss_sprite ss_user_add',
    handler: MembersAction,
    disabled: true
  });

  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  contextMenu = new Ext.menu.Menu({
    items: [editButton, deleteButton,'-',membersButton]
  });

  searchText = new Ext.form.TextField ({
    id: 'searchTxt',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
    emptyText: _('ID_ENTER_SEARCH_TERM'),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearch();
        }
      },
      focus: function(f,e) {
        var row = infoGrid.getSelectionModel().getSelected();
        infoGrid.getSelectionModel().deselectRow(infoGrid.getStore().indexOf(row));
      }
    }
  });

  clearTextButton = new Ext.Action({
    text: 'X',
    ctCls:'pm_search_x_button',
    handler: GridByDefault
  });


  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm){
        editButton.enable();
        deleteButton.enable();
        membersButton.enable();
      },
      rowdeselect: function(sm){
        editButton.disable();
        deleteButton.disable();
        membersButton.disable();
      }
    }
  });

  comboStatusStore = new Ext.data.SimpleStore({
    fields: ['id','value'],
    data: [['1','ACTIVE'],['0','INACTIVE']]
  });

  newForm = new Ext.FormPanel({
    url: 'groups_Ajax?action=saveNewGroup',
    frame: true,
    items:[
           {xtype: 'textfield', fieldLabel: _('ID_GROUP_NAME'), name: 'name', width: 200, allowBlank: false},
           {
             xtype: 'combo',
             id : 'status',
             name : 'status',
             fieldLabel: _('ID_STATUS'),
             hiddenName: 'status',
             typeAhead: true,
             mode: 'local',
             store: comboStatusStore,
             listeners   : {
                 beforerender: function(status){
                   status.setValue('ACTIVE');
                 }
             },
             displayField: 'value',
             valueField:'value',
             allowBlank: false,
             triggerAction: 'all',
             emptyText: _('ID_SELECT_STATUS'),
             selectOnFocus:true
           }
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveNewGroupAction},
                     {text: _('ID_CANCEL'), handler: CloseWindow}
                     ]
  });

  editForm = new Ext.FormPanel({
    url: 'groups_Ajax?action=saveEditGroup',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'grp_uid', hidden: true},
           {xtype: 'textfield', fieldLabel: _('ID_GROUP_NAME'), name: 'name', width: 200, allowBlank: false},
           {
             xtype: 'combo',
             fieldLabel: _('ID_STATUS'),
             hiddenName: 'status',
             typeAhead: true,
             mode: 'local',
             store: comboStatusStore,
             displayField: 'value',
             valueField:'value',
             allowBlank: false,
             editable:false,
             triggerAction: 'all',
             emptyText: _('ID_SELECT_STATUS'),
             selectOnFocus:true
           }
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveEditGroupAction},
                     {text: _('ID_CANCEL'), handler: CloseWindow}
                     ]
  });

  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'groups_Ajax?action=groupsList'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'groups',
      totalProperty: 'total_groups',
      fields : [
                {name : 'GRP_UID'},
                {name : 'GRP_STATUS'},
                {name : 'CON_VALUE'},
                {name : 'GRP_TASKS', type: 'int'},
                {name : 'GRP_USERS', type: 'int'}
                ]
    })
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    viewConfig: {
     // forceFit:true,
      cls:"x-grid-empty",
      emptyText: (TRANSLATIONS.ID_NO_RECORDS_FOUND)
    }
    ,
    columns: [
              {id:'GRP_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
              {header: _('ID_GROUP_NAME'), dataIndex: 'CON_VALUE', width: 400, align:'left'},
              {header: _('ID_STATUS'), dataIndex: 'GRP_STATUS', width: 130, align:'center', renderer: render_status},
              {header: _('ID_USERS'), dataIndex: 'GRP_USERS', width: 100, align:'center'},
              {header: _('ID_TASKS'), dataIndex: 'GRP_TASKS', width: 100, align:'center'}
              ]
  });

  storePageSize = new Ext.data.SimpleStore({
    fields: ['size'],
    data: [['20'],['30'],['40'],['50'],['100']],
    autoLoad: true
  });

  comboPageSize = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: storePageSize,
    valueField: 'size',
    displayField: 'size',
    width: 50,
    editable: false,
    listeners:{
      select: function(c,d,i){
        UpdatePageConfig(d.data['size']);
        bbarpaging.pageSize = parseInt(d.data['size']);
        bbarpaging.moveFirst();
      }
    }
  });

  comboPageSize.setValue(pageSize);

  bbarpaging = new Ext.PagingToolbar({
    pageSize: pageSize,
    store: store,
    displayInfo: true,
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_GROUPS_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_GROUPS_MESSAGE'),
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });


  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'gridGroupList',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    title : _('ID_GROUPS'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [newButton, '-', editButton, deleteButton,'-',membersButton, {xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: EditGroupWindow
    },
    view: new Ext.grid.GroupingView({
      forceFit:true,
      groupTextTpl: '{text}',
      cls:"x-grid-empty",
      emptyText: _('ID_NO_RECORDS_FOUND')
    })
  });

  infoGrid.on('rowcontextmenu',
      function (grid, rowIndex, evt) {
    var sm = grid.getSelectionModel();
    sm.selectRow(rowIndex, sm.isSelected(rowIndex));
  },
  this
  );

  infoGrid.on('contextmenu', function(evt){evt.preventDefault();}, this);
  infoGrid.addListener('rowcontextmenu',onMessageContextMenu, this);
  infoGrid.store.load();

  viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: false,
    items: [
            infoGrid
            ]
  });
});

//Funtion Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
  e.stopEvent();
  var coords = e.getXY();
  contextMenu.showAt([coords[0], coords[1]]);
};

//Do Nothing Function
DoNothing = function(){};

//Open New Group Form
NewGroupWindow = function(){
  newForm.getForm().reset();
  newForm.getForm().items.items[0].focus('',500);
  w = new Ext.Window({
    autoHeight: true,
    width: 400,
    title: _('ID_CREATE_GROUP_TITLE'),
    closable: false,
    modal: true,
    id: 'w',
    items: [newForm]
  });
  w.show();
};

//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  infoGrid.store.load();
};

//Do Search Function
DoSearch = function(){
  infoGrid.store.load({params: {textFilter: searchText.getValue()}});
};

//Close Popup Window
CloseWindow = function(){
  Ext.getCmp('w').hide();
};

//Check Group Name Availability
CheckGroupName = function(grp_name, function_success, function_failure){
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'groups_Ajax',
    params: {action: 'exitsGroupName', GRP_NAME: grp_name},
    success: function(resp, opt){
      viewport.getEl().unmask();
      var checked = eval(resp.responseText);
      (!checked) ? function_success() : function_failure();
    },
    failure: function(r,o) {
      viewport.getEl().unmask();
      function_failure();
    }
  });
};

//Save Group Button
SaveNewGroupAction = function(){
  var group = newForm.getForm().findField('name').getValue();
  group = group.trim();
  if (group != '') CheckGroupName(group, SaveNewGroup, DuplicateGroupName);
};

//Show Duplicate Group Name Message
DuplicateGroupName = function(){
  PMExt.warning(_('ID_GROUPS'), _('ID_MSG_GROUP_NAME_EXISTS'));
};

//Save New Group
SaveNewGroup = function(){
  newForm.getForm().submit({
    waitTitle : "",
    success: function(f,a){
      CloseWindow(); //Hide popup widow
      newForm.getForm().reset(); //Set empty form to next use
      searchText.reset();
      infoGrid.store.load(); //Reload store grid
      PMExt.notify(_('ID_GROUPS'),_('ID_GROUPS_SUCCESS_NEW'));
      //Ext.Msg.alert(_('ID_GROUPS,_('ID_GROUPS_SUCCESS_NEW);
    },
    failure: function(f,a){
      switch(a.failureType){
        case Ext.form.Action.CLIENT_INVALID:
          //Ext.Msg.alert('New Role Form','Invalid Data');
          break;
      }

    }
  });
};

//Open Edit Group Form
EditGroupWindow = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  var strName = stringReplace("&lt;", "<", rowSelected.data.CON_VALUE);
  strName = stringReplace("&gt;", ">", strName);

  editForm.getForm().findField('grp_uid').setValue(rowSelected.data.GRP_UID);
  editForm.getForm().findField('name').setValue(strName);
  editForm.getForm().findField('status').setValue(rowSelected.data.GRP_STATUS);
  w = new Ext.Window({
    autoHeight: true,
    width: 440,
    title: _('ID_EDIT_GROUP_TITLE'),
    closable: false,
    modal: true,
    id: 'w',
    items: [editForm]
  });
  w.show();
};

//Save Edit Group Button
SaveEditGroupAction = function(){
  var group = editForm.getForm().findField('name').getValue();
  group = group.trim();
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (group != ''){
    if (rowSelected.data.CON_VALUE.toUpperCase() == group.toUpperCase()){
      SaveEditGroup();
    }else{
      CheckGroupName(group, SaveEditGroup, DuplicateGroupName);
    }
  }
};

//Save Edit Group
SaveEditGroup = function(){
  editForm.getForm().submit({
    waitTitle : "",
    success: function(f,a){
      CloseWindow(); //Hide popup widow
      DoSearch(); //Reload store grid
      editButton.disable();  //Disable Edit Button
      deleteButton.disable(); //Disable Delete Button
      membersButton.disable(); //Disable Members Button
      PMExt.notify(_('ID_GROUPS'),_('ID_GROUPS_SUCCESS_UPDATE'));
    },
    failure: function(f,a){
      switch(a.failureType){
        case Ext.form.Action.CLIENT_INVALID:
          //Ext.Msg.alert('New Role Form','Invalid Data');
          break;
      }

    }
  });
};

//Delete Button Action
DeleteButtonAction = function(){
  Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_MSG_CONFIRM_DELETE_GROUP'),
      function(btn, text){
    if (btn=="yes"){
      rowSelected = infoGrid.getSelectionModel().getSelected();
      viewport.getEl().mask(_('ID_PROCESSING'));
      Ext.Ajax.request({
        url: 'groups_Ajax',
        params: {action: 'deleteGroup', GRP_UID: rowSelected.data.GRP_UID},
        success: function(r,o){
          viewport.getEl().unmask();
          DoSearch();
          editButton.disable();  //Disable Edit Button
          deleteButton.disable(); //Disable Delete Button
          membersButton.disable(); //Disable Members Button
          PMExt.notify(_('ID_GROUPS'), _('ID_GROUPS_SUCCESS_DELETE'));
        },
        failure: function(){
          viewport.getEl().unmask();
        }
      });

    }
  }
  );
};

//Render Status
render_status = function(v){
  switch(v){
    case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
    case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>'; break;
    case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>'; break;
  }
};

//Members Button Action
MembersAction = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  location.href = 'groupsMembers?GRP_UID=' + rowSelected.data.GRP_UID;
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
    url: 'groups_Ajax',
    params: {action:'updatePageSize', size: pageSize}
  });
};
