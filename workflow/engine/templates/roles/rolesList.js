/*
 * @author: Qennix
 * Jan 18th, 2011
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
           Ext.Msg.alert( _('ID_REFRESH_LABEL') , _('ID_REFRESH_MESSAGE') );
         }
       }
     },
     {
       key: Ext.EventObject.DELETE,
       fn: function(k,e){
         iGrid = Ext.getCmp('infoGrid');
         rowSelected = iGrid.getSelectionModel().getSelected();
         if (rowSelected){
           CanDeleteRole();
         }
       }
     },
     {
       key: Ext.EventObject.F2,
       fn: function(k,e){
         iGrid = Ext.getCmp('infoGrid');
         rowSelected = iGrid.getSelectionModel().getSelected();
         if (rowSelected){
           EditRole();
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
var usersButton;
var permissionsButton;
var searchButton;
var serachText;
var newForm;
var comboStatusStore;
var editForm;
var contextMenu;
var w;

Ext.onReady(function(){
  Ext.QuickTips.init();

  pageSize = parseInt(CONFIG.pageSize);

  newButton = new Ext.Action({
    text: _('ID_NEW'),
    iconCls: 'button_menu_ext ss_sprite  ss_add',
    handler: NewRoleWindow
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
    iconCls: 'button_menu_ext ss_sprite  ss_pencil',
    handler: EditRole,
    disabled: true	
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite  ss_delete',
    handler: CanDeleteRole,
    disabled: true
  });

  usersButton = new Ext.Action({
    text: _('ID_USERS'),
    iconCls: 'button_menu_ext ss_sprite  ss_user_add',
    handler: RolesUserPage,
    disabled: true
  });
  permissionsButton = new Ext.Action({
    text: _('ID_PERMISSIONS'),
    iconCls: 'button_menu_ext ss_sprite  ss_key_add',
    handler: RolesPermissionPage,
    disabled: true
  });

  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  contextMenu = new Ext.menu.Menu({
    items: [editButton, deleteButton,'-',usersButton, permissionsButton]
  });

  searchText = new Ext.form.TextField ({
    id: 'searchText',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
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

  comboStatusStore = new Ext.data.SimpleStore({
    fields: ['id','value'],
    data: [['1',_('ID_ACTIVE')],['0',_('ID_INACTIVE')]]
  });

  newForm = new Ext.FormPanel({
    url: 'roles_Ajax?request=saveNewRole',
    frame: true, 	
    items:[
           {xtype: 'textfield', fieldLabel: _('ID_CODE'), name: 'code', width: 250, allowBlank: false},
           {xtype: 'textfield', fieldLabel: _('ID_NAME'), name: 'name', width: 200, allowBlank: false},
           {
             xtype: 'combo', 
             fieldLabel: _('ID_STATUS'), 
             hiddenName: 'status',
             typeAhead: true,
             mode: 'local', 
             store: comboStatusStore, 
             displayField: 'value', 
             valueField:'id',
             allowBlank: false, 
             editable:false,
             triggerAction: 'all',
             emptyText: _('ID_SELECT_STATUS'),
             selectOnFocus:true
           }
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveNewRole},
                     {text: _('ID_CANCEL'), handler: CloseWindow}

                     ]
  });

  editForm = new Ext.FormPanel({
    url: 'roles_Ajax?request=updateRole',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'rol_uid', hidden: true },
           {xtype: 'textfield', fieldLabel: _('ID_CODE'), name: 'code', width: 250, allowBlank: false, readOnly: true, hidden: !PARTNER_FLAG ? false : true},
           {xtype: 'textfield', fieldLabel: _('ID_NAME'), name: 'name', width: 200, allowBlank: false},
           {
             xtype: 'combo', 
             fieldLabel: _('ID_STATUS'), 
             hiddenName: 'status',
             typeAhead: true,
             mode: 'local', 
             store: comboStatusStore, 
             displayField: 'value', 
             valueField:'id',
             allowBlank: false, 
             editable:false,
             triggerAction: 'all',
             emptyText: _('ID_SELECT_STATUS'),
             selectOnFocus:true
           }
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: UpdateRole},
                     {text: _('ID_CANCEL'), handler: CloseWindow}
                     ]
  });

  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm){
        editButton.enable();
        deleteButton.enable();
        usersButton.enable();
        permissionsButton.enable();
      },
      rowdeselect: function(sm){
        editButton.disable();
        deleteButton.disable();
        usersButton.disable();
        permissionsButton.disable();
      }
    }
  });

  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'roles_Ajax?request=rolesList'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'roles',
      totalProperty: 'total_roles',
      fields : [
                {name : 'ROL_UID'},
                {name : 'ROL_CODE'},
                {name : 'ROL_NAME'},
                {name : 'ROL_CREATE_DATE'},
                {name : 'ROL_UPDATE_DATE'},
                {name : 'ROL_STATUS'},
                {name : 'TOTAL_USERS'}
                ]
    })
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
              {id:'ROL_UID', dataIndex: 'ROL_UID', hidden:true, hideable:false},
              {header: _('ID_CODE'), dataIndex: 'ROL_CODE', width: 220, align:'left', hidden: !PARTNER_FLAG ? false : true},
              {header: _('ID_NAME'), dataIndex: 'ROL_NAME', width: 180, hidden:false, align:'left'},
              {header: _('ID_STATUS'), dataIndex: 'ROL_STATUS', width: 80, hidden: false, align: 'center', renderer: status_role},
              {header: _('ID_ACTIVE_USERS'), dataIndex: 'TOTAL_USERS', width: 80, hidden: false, align: 'center'},
              {header: _('ID_PRO_CREATE_DATE'), dataIndex: 'ROL_CREATE_DATE', width: 90, hidden:false, align:'center', renderer: render_date},
              {header: _('ID_LAN_UPDATE_DATE'), dataIndex: 'ROL_UPDATE_DATE', width: 90, hidden:false, align:'center', renderer: render_date}
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
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_ROLES_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_ROLES_MESSAGE'),
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'gridRolesList',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    //iconCls:'icon-grid',
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    title : _('ID_ROLES'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [newButton, '-', editButton, deleteButton,'-',usersButton, permissionsButton, {xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: EditRole
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

//Open New Role Form
NewRoleWindow = function(){
  newForm.getForm().reset();
  newForm.getForm().items.items[0].focus('',500);
  w = new Ext.Window({
    title: _('ID_CREATE_ROLE_TITLE'),
    autoHeight: true,
    id: 'w',
    modal: true,
    width: 420,
    items: [newForm]
  });
  w.show();
};

//Close Popup Window
CloseWindow = function(){
  Ext.getCmp('w').hide();
};

//Save New Role
SaveNewRole = function(){
  rol_code = newForm.getForm().findField('code').getValue();
  rol_code.trim();
  if (rol_code == '') return;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'roles_Ajax',
    params: {request: 'checkRoleCode', ROL_CODE: rol_code},
    success: function(r,o){
      viewport.getEl().unmask();
      resp = Ext.util.JSON.decode(r.responseText);
      if (resp.success){
        viewport.getEl().mask(_('ID_PROCESSING'));
        newForm.getForm().submit({
          success: function(f,a){
            viewport.getEl().unmask();
            CloseWindow(); //Hide popup widow
            newForm.getForm().reset(); //Set empty form to next use
            searchText.reset();
            infoGrid.store.load(); //Reload store grid
            PMExt.notify(_('ID_ROLES'),_('ID_ROLES_SUCCESS_NEW'));
          },
          failure: function(f,a){
            viewport.getEl().unmask();
            switch(a.failureType){
              case Ext.form.Action.CLIENT_INVALID:
                //Ext.Msg.alert('New Role Form','Invalid Data');
                break;
            }
          }
        });  
      }else{
        PMExt.error(_('ID_ROLES'),_('ID_ROLE_EXISTS'));        
      }
    },
    failure: function(r,o){
      viewport.getEl().unmask();
    }
  });

};

//Update Selected Role
UpdateRole = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  rol_code = editForm.getForm().findField('code').getValue();
  rol_code.trim();
  if (rol_code == '') return;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'roles_Ajax',
    params: {request: 'checkRoleCode', ROL_CODE: rol_code, ROL_UID: rowSelected.data.ROL_UID},
    success: function(r,o){
      viewport.getEl().unmask();
      resp = Ext.util.JSON.decode(r.responseText);
      if (resp.success){
        viewport.getEl().mask(_('ID_PROCESSING'));
        editForm.getForm().submit({
          success: function(f,a){
            viewport.getEl().unmask();
            CloseWindow(); //Hide popup widow
            DoSearch(); //Reload store grid
            editButton.disable();  //Disable Edit Button
            deleteButton.disable(); //Disable Delete Button
            PMExt.notify(_('ID_ROLES'),_('ID_ROLES_SUCCESS_UPDATE'));
          },
          failure: function(f,a){
            viewport.getEl().unmask();
            switch(a.failureType){
              case Ext.form.Action.CLIENT_INVALID:
                //Ext.Msg.alert('New Role Form','Invalid Data');
                break;
            }

          }
        });
      }else{
        PMExt.error(_('ID_ROLES'),_('ID_ROLE_EXISTS'));
      }
    },
    failure: function(r,o) {
      viewport.getEl().unmask();
    }
  });
};

//Edit Selected Role
EditRole = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    if (rowSelected.data.ROL_UID == '00000000000000000000000000000002'){
      PMExt.warning(_('ID_ROLES'),_('ID_ROLES_MSG'));
    }else{
      editForm.getForm().findField('rol_uid').setValue(rowSelected.data.ROL_UID);
      editForm.getForm().findField('code').setValue(rowSelected.data.ROL_CODE);
      editForm.getForm().findField('name').setValue(rowSelected.data.ROL_NAME);
      editForm.getForm().findField('status').setValue(rowSelected.data.ROL_STATUS);
      w = new Ext.Window({
        autoHeight: true,
        id: 'w',
        modal: true,
        width: 420,
        title: _('ID_EDIT_ROLE_TITLE'),
        items: [editForm]
      });
      w.show();
    }

  }
};

//Check Can Delete Role
CanDeleteRole = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    var swDelete = false;
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
      url: 'roles_Ajax',
      success: function(response, opts){
        viewport.getEl().unmask();
        swDelete = (response.responseText=='true') ? true : false;
        if (swDelete){
          Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_REMOVE_ROLE'),
              function(btn, text){
            if (btn=="yes"){
              viewport.getEl().mask(_('ID_PROCESSING'));
              Ext.Ajax.request({
                url: 'roles_Ajax',
                params: {request: 'deleteRole', ROL_UID: rowSelected.data.ROL_UID},
                success: function(r,o){
                  viewport.getEl().unmask();
                  infoGrid.store.load(); //Reload store grid
                  editButton.disable();  //Disable Edit Button
                  deleteButton.disable(); //Disable Delete Button
                  usersButton.disable(); //Disable Delete Button
                  permissionsButton.disable(); //Disable Delete Button
                  PMExt.notify(_('ID_ROLES'),_('ID_ROLES_SUCCESS_DELETE'));
                },
                failure: function(){viewport.getEl().unmask(); DoNothing();}
              });
            }
          });
        }else{
          PMExt.error(_('ID_ROLES'),_('ID_ROLES_CAN_NOT_DELETE'));
        }
      },
      failure: function(){viewport.getEl().unmask(); DoNothing();},
      params: {request: 'canDeleteRole', ROL_UID: rowSelected.data.ROL_UID}
    });
  }
};


//Open User-Roles Manager
RolesUserPage = function(value){
  iGrid = Ext.getCmp('infoGrid');
  rowSelected = iGrid.getSelectionModel().getSelected();
  if (rowSelected){
    value = rowSelected.data.ROL_UID;
    location.href = 'rolesUsersPermission?rUID=' + value + '&tab=users';
  }
};


//Open Permission-Roles Manager
RolesPermissionPage = function(value){
  iGrid = Ext.getCmp('infoGrid');
  rowSelected = iGrid.getSelectionModel().getSelected();
  if (rowSelected){
    value = rowSelected.data.ROL_UID;
    location.href = 'rolesUsersPermission?rUID=' + value + '&tab=permissions';
  }
};

//Renderer Active/Inactive Role
status_role = function(value){
  var aux;
  switch(value){
    case '1': aux = '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
    case '0': aux = '<font color="red">'+ _('ID_INACTIVE') + '</font>'; break;
  }
  return aux;
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

//Render Date Function
render_date = function(v){
  return _DF(v);
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
    url: 'roles_Ajax',
    params: {request:'updatePageSize', size: pageSize}
  });
};
