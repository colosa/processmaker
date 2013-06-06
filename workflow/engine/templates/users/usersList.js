/*
 * @author: Qennix
 * Jan 24th, 2011
 */

//Keyboard Events
new Ext.KeyMap(document, [
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
      Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE') );
      }
   }
},
{
  key: Ext.EventObject.DELETE,
  fn: function(k,e){
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected){
      DeleteUserAction();
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
var changeStatusButton;
var deleteButton;
var summaryButton;
var groupsButton;
//var reassignButton;
var authenticationButton;
var searchButton;
var searchText;
var contextMenu;
var user_admin = '00000000000000000000000000000001';
var pageSize;
var fullNameFormat;
var dateFormat;
var comboAuthSources;
var storeAuthSources;

Ext.onReady(function(){
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

  fullNameFormat = CONFIG.fullNameFormat;
  dateFormat = CONFIG.dateFormat;
  pageSize = parseInt(CONFIG.pageSize);

  newButton = new Ext.Action({
    text: _('ID_NEW'),
     iconCls: 'button_menu_ext ss_sprite  ss_add',
     handler: NewUserAction
  });

  summaryButton = new Ext.Action({
    text: _('ID_SUMMARY'),
     iconCls: 'button_menu_ext ss_sprite  ss_table',
     handler: SummaryTabOpen,
    disabled: true
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
     iconCls: 'button_menu_ext ss_sprite  ss_pencil',
     handler: EditUserAction,
     disabled: true
  });

  changeStatusButton = new Ext.Button({
    text: _('ID_STATUS'),
    icon: '',
    iconCls: 'silk-add',
    handler: changeStatusCheck,
    disabled: true
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
     iconCls: 'button_menu_ext ss_sprite  ss_delete',
     handler: DeleteUserAction,
     disabled: true
  });

  groupsButton = new Ext.Action({
     text: _('ID_GROUPS'),
     iconCls: 'button_menu_ext ss_sprite ss_group_add',
     handler: UsersGroupPage,
     disabled: true
  });

//    reassignButton = new Ext.Action({
//      text: _('ID_REASSIGN_CASES'),
//      iconCls: 'button_menu_ext ss_sprite ss_arrow_rotate_clockwise',
//      handler: DoNothing,
//      disabled: true
//    });

  authenticationButton = new Ext.Action({
     text: _('ID_AUTHENTICATION'),
    iconCls: 'button_menu_ext ss_sprite ss_key',
     handler: AuthUserPage,
     disabled: true
  });


  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  contextMenuConfig = new Array();
  contextMenuConfig.push(editButton);
  contextMenuConfig.push(deleteButton);
  contextMenuConfig.push('-');
  contextMenuConfig.push(groupsButton);
  contextMenuConfig.push('-');
  contextMenuConfig.push(summaryButton);
  contextMenu = new Ext.menu.Menu(contextMenuConfig);

  searchText = new Ext.form.TextField ({
    id: 'searchTxt',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 100,
    emptyText: _('ID_ENTER_SEARCH_TERM'),//'enter search term',
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
        changeStatusButton.enable();
        deleteButton.enable();
        groupsButton.enable();
        //reassignButton.enable();
        authenticationButton.enable();
        summaryButton.enable();
      },
      rowdeselect: function(sm){
        editButton.disable();
        changeStatusButton.setIcon('');
        changeStatusButton.setText(_('ID_STATUS'));
        changeStatusButton.disable();
        deleteButton.disable();
        groupsButton.disable();
        //reassignButton.disable();
        authenticationButton.disable();
        summaryButton.disable();
      }
    }
  });
    var stepsFields = Ext.data.Record.create([
    {
      name : 'USR_USERNAME',
      type: 'string'
    },
    {
      name : 'USR_FIRSTNAME',
      type: 'string'
    },
    {
      name : 'USR_LASTNAME',
      type: 'string'
    },
    {
      name : 'USR_EMAIL',
      type: 'string'
    },
    {
      name : 'USR_ROLE',
      type: 'string'
    },
    {
      name : 'USR_DUE_DATE',
      type: 'string'
    },
    {
      name : 'DEP_TITLE',
      type: 'string'
    },
    {
      name : 'LAST_LOGIN',
      type: 'string'
    },
    {
      name : 'USR_STATUS',
      type: 'string'
    },
    {
      name : 'TOTAL_CASES',
      type: 'string'
    },
    {
      name : 'DUE_DATE_OK',
      type: 'string'
    },
  ]);

  store = new Ext.data.GroupingStore( {
    remoteSort  : true,
    sortInfo    : stepsFields,
    groupField  :'',
    proxy       : new Ext.data.HttpProxy({
      url       : 'users_Ajax?function=usersList'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'users',
      totalProperty: 'total_users',
      fields : [
        {name : 'USR_UID'},
        {name : 'USR_USERNAME'},
        {name : 'USR_FIRSTNAME'},
        {name : 'USR_LASTNAME'},
        {name : 'USR_EMAIL'},
        {name : 'USR_ROLE'},
        {name : 'USR_DUE_DATE'},
        {name : 'DEP_TITLE'},
        {name : 'LAST_LOGIN'},
        {name : 'USR_STATUS'},
        {name : 'TOTAL_CASES',type:'int'},
        {name : 'DUE_DATE_OK'},
        {name : 'USR_AUTH_SOURCE'}
      ]
    })
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

  storeAuthSources = new Ext.data.GroupingStore({
    autoLoad: true,
    proxy : new Ext.data.HttpProxy({
      url: 'users_Ajax?function=authSources&cmb=yes'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'sources',
      fields: [
        {name: 'AUTH_SOURCE_UID'},
        {name: 'AUTH_SOURCE_SHOW'}
      ]
    })
  });

  comboAuthSources = new Ext.form.ComboBox({
    mode: 'local',
    triggerAction: 'all',
    store: storeAuthSources,
    valueField: 'AUTH_SOURCE_UID',
    displayField: 'AUTH_SOURCE_SHOW',
    //emptyText: 'All',
    width: 160,
    editable: false,
    value: _('ID_ALL'),
    listeners:{
      select: function(c,d,i){
        store.setBaseParam('auths',d.data['AUTH_SOURCE_UID']);
        UpdateAuthSource(d.data['AUTH_SOURCE_UID']);
      }
    }
  });

  bbarpaging = new Ext.PagingToolbar({
    pageSize: pageSize,
     store: store,
     displayInfo: true,
     displayMsg: _('ID_GRID_PAGE_DISPLAYING_USERS_MESSAGE') + '&nbsp; &nbsp; ',
     emptyMsg: _('ID_GRID_PAGE_NO_USERS_MESSAGE'),
     items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50
    },
    columns: [
      {id: 'USR_UID', dataIndex: 'USR_UID', hidden: true, hideable: false},
      //{header: '', dataIndex: 'USR_UID', width: 30, align: 'center', renderer: photo_user},
      {header: _('ID_USER_NAME'), dataIndex: 'USR_USERNAME', width: 90, align: 'left', sortable: true},
      {header: _('ID_FULL_NAME'), dataIndex: 'USR_USERNAME', width: 175, align: 'left', renderer: full_name},
      {header: _('ID_EMAIL'), dataIndex: 'USR_EMAIL', width: 120, hidden: true, align: 'left', sortable: true},
      {header: _('ID_STATUS'), dataIndex: 'USR_STATUS', width: 50, align: 'center', renderer: render_status, sortable: true},
      {header: _('ID_ROLE'), dataIndex: 'USR_ROLE', width: 150, align:'left', sortable: true},
      {header: _('ID_DEPARTMENT'), dataIndex: 'DEP_TITLE', width: 150, hidden: true, align: 'left'},
      {header: _('ID_LAST_LOGIN'), dataIndex: 'LAST_LOGIN', width: 108, align: 'center', renderer: render_lastlogin},
      {header: _('ID_AUTHENTICATION_SOURCE'), dataIndex: 'USR_AUTH_SOURCE', width: 108, hidden: true, align: 'left'},
      {header: _('ID_CASES_NUM'), dataIndex: 'TOTAL_CASES', width: 75, align:'right', sortType: 'asInt'},
      {header: _('ID_DUE_DATE'), dataIndex: 'USR_DUE_DATE', width: 108, align:'center', renderer: render_duedate, sortable: true}
    ]
  });

  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
     layout: 'fit',
     id: 'infoGrid',
    height:100,
     autoWidth : true,
     stateful : true,
     stateId : 'gridUserLists',
     enableColumnResize: true,
     enableHdMenu: true,
     frame:false,
     columnLines: false,
     viewConfig: {
      forceFit:true
    },
    title : _('ID_USERS'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [newButton, '-',summaryButton,'-', editButton, changeStatusButton, deleteButton, '-', groupsButton,  '-',authenticationButton,  {xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick : EditUserAction,
      render: function() {
        infoGrid.getSelectionModel().on('rowselect', function() {
          var rowSelected = infoGrid.getSelectionModel().getSelected();
          changeStatusButton.enable();
          if (rowSelected.data.USR_STATUS == 'ACTIVE') {
            changeStatusButton.setIcon('/images/deactivate.png');
            changeStatusButton.setText(_('ID_DISABLE'));
          }
          else {
            changeStatusButton.setIcon('/images/activate.png');
            changeStatusButton.setText(_('ID_ENABLE'));
          }
        });
      }
    },
    view: new Ext.grid.GroupingView({
      forceFit:true,
      groupTextTpl: '{text}'
    })
  });

  infoGrid.on('rowcontextmenu',
    function (grid, rowIndex, evt) {
      var sm = grid.getSelectionModel();
      sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    },
    this
  );

  infoGrid.on('contextmenu',
    function (evt) {
      evt.preventDefault();
    },
    this
  );

  infoGrid.addListener('rowcontextmenu',onMessageContextMenu,this);

  infoGrid.store.load();

  viewport = new Ext.Viewport({
     layout: 'fit',
     autoScroll: false,
     items: [infoGrid]
  });
});

//Function Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
  e.stopEvent();
  var coords = e.getXY();
  contextMenu.showAt([coords[0], coords[1]]);
};

//Do Nothing Function
DoNothing = function(){};

//Open New User Form
NewUserAction = function(){
  location.href = 'usersNew?MODE=new';
};

//Change user status
changeStatus = function(userUid, newUsrStatus) {
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'changeUserStatus', USR_UID: userUid, NEW_USR_STATUS: newUsrStatus},
    success: function(res, opt) {
      viewport.getEl().unmask();
      changeStatusButton.disable();
      changeStatusButton.setIcon('');
      changeStatusButton.setText(_('ID_STATUS'));
      DoSearch();
    },
    failure: DoNothing
  });
};

//Check change user status
changeStatusCheck = function() {
  var row = infoGrid.getSelectionModel().getSelected();
  if (row) {
    if (row.data.USR_UID == user_admin){
      Ext.Msg.alert(_('ID_USERS'), _('ID_CANNOT_CHANGE_STATUS_ADMIN_USER'));
    }
    else {
      viewport.getEl().mask(_('ID_PROCESSING'));
      Ext.Ajax.request({
        url: 'users_Ajax',
        params: {'function': 'canDeleteUser', uUID: row.data.USR_UID},
        success: function(res, opt) {
          viewport.getEl().unmask();
          response = Ext.util.JSON.decode(res.responseText);
          if (!response.candelete && row.data.USR_STATUS == 'ACTIVE') {
            Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_USERS_HAS_ASSIGNED_CASES'), function(btn) {
              if (btn == 'yes') {
                changeStatus(row.data.USR_UID, row.data.USR_STATUS == 'ACTIVE' ? 'INACTIVE' : 'ACTIVE');
              }
              else {
                viewport.getEl().unmask();
              }
            });
          }
          else {
            changeStatus(row.data.USR_UID, row.data.USR_STATUS == 'ACTIVE' ? 'INACTIVE' : 'ACTIVE');
          }
        },
        failure: function(r, o) {
          viewport.getEl().unmask();
          DoNothing();
        }
      });
    }
  }
};

//Delete User Action
DeleteUserAction = function(){
  var uid = infoGrid.getSelectionModel().getSelected();
  if (uid){
  if (uid.data.USR_UID==user_admin){
    Ext.Msg.alert(_('ID_USERS'), _('ID_CANNOT_DELETE_ADMIN_USER'));
  }else{
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
    url: 'users_Ajax',
    params: {'function': 'canDeleteUser', uUID: uid.data.USR_UID},
    success: function(res, opt){
      viewport.getEl().unmask();
      response = Ext.util.JSON.decode(res.responseText);
      if (response.candelete){
      if (response.hashistory){
        Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_USERS_DELETE_WITH_HISTORY'),
        function(btn){
          if (btn=='yes') DeleteUser(uid.data.USR_UID);
        }
        );
      }else{
        Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_MSG_CONFIRM_DELETE_USER'),
        function(btn){
          if (btn=='yes') DeleteUser(uid.data.USR_UID);
          }
        );
        }
      }else{
      PMExt.error(_('ID_USERS'), _('ID_MSG_CANNOT_DELETE_USER'));
      }
    },
    failure: function(r,o){
      viewport.getEl().unmask();
      DoNothing();
    }
    });
  }
  }
};

//Open User-Groups Manager
UsersGroupPage = function(value){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
  value = rowSelected.data.USR_UID;
  location.href = 'usersGroups?uUID=' + value + '&type=group';
  }
};

//Open Summary Tab
SummaryTabOpen = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    value = rowSelected.data.USR_UID;
    location.href = 'usersGroups?uUID=' + value + '&type=summary';
  }
};

//Edit User Action
EditUserAction = function(){
  var uid = infoGrid.getSelectionModel().getSelected();
  if (uid) {
    location.href = 'usersEdit?USR_UID=' + uid.data.USR_UID+'&USR_AUTH_SOURCE=' + uid.data.USR_AUTH_SOURCE+'&MODE=edit';
  }
};

//Open Authentication-User Manager
AuthUserPage = function(value){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
  value = rowSelected.data.USR_UID;
  location.href = 'usersGroups?uUID=' + value + '&type=auth';;
  }
};

////Renderer Active/Inactive Role
//photo_user = function(value){
//  return '<img border="0" src="users_ViewPhotoGrid?h=' + Math.random() +'&pUID=' + value + '" width="20" />';
//};

//Render Full Name
full_name = function(v,x,s){
  return _FNF(v, s.data.USR_FIRSTNAME, s.data.USR_LASTNAME);
};

//Render Status
render_status = function(v){
  switch(v){
    case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
    case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>';; break;
    case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>';; break;
  }
  return true;
};

//Render Due Date
render_duedate = function(v,x,s){
  if (s.data.DUE_DATE_OK)
    return _DF(v);
  else
    return '<font color="red">' + _DF(v) + '</font>';
};

render_lastlogin = function(v){
	return _DF(v);
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

//Delete User Function
DeleteUser = function(uid){
  Ext.Ajax.request({
  url: 'users_Ajax',
  params: {'function': 'deleteUser', USR_UID: uid},
  success: function(res, opt){
    DoSearch();
    PMExt.notify(_('ID_USERS'),_('ID_USERS_SUCCESS_DELETE'));
  },
  failure: DoNothing
  });
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
  url: 'users_Ajax',
  params: {'function':'updatePageSize', size: pageSize}
  });
};

//Update Authentication Source Filter
UpdateAuthSource = function(index){
  searchText.reset();
  infoGrid.store.load({params: {auths: index}});
};
