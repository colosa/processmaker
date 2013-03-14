/*
 * @author: Qennix
 * Jan 31th, 2011
 */

function sizeHeight()
{
    var sHeight = 0;
    if (typeof window.innerHeight != 'undefined') {
        sHeight = window.innerHeight;
    } else if (typeof document.documentElement != 'undefined'
          && typeof document.documentElement.clientHeight != 'undefined'
          && document.documentElement.clientHeight != 0) {
        sHeight = document.documentElement.clientHeight;
    } else {
      sHeight = document.getElementsByTagName('body')[0].clientHeight;
    }
    return sHeight;
}

Ext.EventManager.onWindowResize(function () {
    treePanel.setSize('100%', sizeHeight());
});

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
     },
     {
       key: Ext.EventObject.DELETE,
       fn: function(k,e){ DeleteDepartmentAction();}
     },
     {
       key: Ext.EventObject.F2,
       fn: function(k,e){ EditDepartmentAction(); }
     }
   ]
);

var waitLoading = {};
waitLoading.show = function() {
  var mask = Ext.getBody().mask(_("ID_SAVING"), 'x-mask-loading', false);
  mask.setStyle('z-index', Ext.WindowMgr.zseed + 1000);
};
waitLoading.hide = function() {
  Ext.getBody().unmask();
};

var treePanel;
var rootNode;
var w;

Ext.onReady(function() {
  Ext.QuickTips.init();

  newButton = new Ext.Action({
    text: _('ID_NEW_DEPARTMENT'),
    iconCls: ' button_menu_ext ss_sprite ss_add',
    handler: NewRootDepartment
  });

  newSubButton = new Ext.Action({
    text: _('ID_NEW_SUB_DEPARTMENT'),
    iconCls: ' button_menu_ext ss_sprite ss_add',
    handler: NewSubDepartment,
    disabled: true
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
    iconCls: 'button_menu_ext ss_sprite ss_pencil',
    handler: EditDepartmentAction,
    disabled: true
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite ss_delete',
    handler: DeleteDepartmentAction,
    disabled: true
  });

  usersButton = new Ext.Action({
    text: _('ID_USERS'),
    iconCls: 'button_menu_ext ss_sprite ss_user_add',
    handler: UsersButtonAction,
    disabled: true
  });

  contextMenu = new Ext.menu.Menu({
    items: [newSubButton,'-',editButton, deleteButton,'-',usersButton]
  });

  smodel = new Ext.tree.DefaultSelectionModel({
    listeners:{
      selectionchange: function(sm, node){
        editButton.enable();
        newSubButton.enable();
        usersButton.enable();
        if (!node){
          deleteButton.disable();
        }else{
          if (node.attributes.leaf){
            deleteButton.enable();
          }else{
            deleteButton.disable();
          }
        }
      }
    }
  });

  comboStatusStore = new Ext.data.SimpleStore({
    fields: ['id','value'],
    data: [['ACTIVE',_('ID_ACTIVE')],['INACTIVE',_('ID_INACTIVE')]]
  });

  comboDepManager = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'departments_Ajax?action=usersByDepartment'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'users',
      fields : [
                {name : 'USR_UID'},
                {name : 'USR_VALUE'}
                ]
    })
  });

  newForm = new Ext.FormPanel({
    url: 'departments_Ajax?request=saveNewDepartment',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'parent', hidden: true},
           {xtype: 'textfield', fieldLabel: _('ID_DEPARTMENT_NAME'), name: 'dep_name', width: 230, allowBlank: false}
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveNewDepartment},
                     {text: _('ID_CANCEL'), handler: CloseWindow}

                     ],
                     labelWidth: 120
  });

  editForm = new Ext.FormPanel({
    url: 'departments_Ajax?request=saveEditDepartment',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'dep_uid', hidden: true},
           {xtype: 'textfield', name: 'dep_parent', hidden: true},
           {xtype: 'textfield', fieldLabel: _('ID_DEPARTMENT_NAME'), name: 'dep_name', width: 230, allowBlank: false},
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
             triggerAction: 'all',
             emptyText: _('ID_SELECT_STATUS'),
             selectOnFocus:true
           }
           ,
           {
             xtype: 'combo',
             fieldLabel: _('ID_MANAGER'),
             hiddenName: _('ID_MANAGER'),
             typeAhead: true,
             mode: 'local',
             store: comboDepManager,
             displayField: 'USR_VALUE',
             valueField:'USR_UID',
             allowBlank: true,
             triggerAction: 'all',
             emptyText: '',
             selectOnFocus:true
           }
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveEditDepartment},
                     {text: _('ID_CANCEL'), handler: CloseWindow}
                     ],
                     labelWidth: 120
  });

  rootNode = new Ext.tree.AsyncTreeNode({
    text: _('ID_DEPARTMENTS')
  });

  treePanel = new Ext.ux.tree.TreeGrid({
    title: _('ID_DEPARTMENTS'),
    autoScroll: true,
    width: '10%',
    height: sizeHeight(),
    id: 'treePanel',
    enableDD: true,
    columns:[{
      header: _('ID_DEPARTMENT_NAME'),
      dataIndex: 'DEP_TITLE',
      width: 380,
      tpl: new Ext.XTemplate('{DEP_TITLE:this.formatDepTitle}', {
        formatDepTitle: function(v) {
          return '<span style="white-space:normal !important;word-wrap: break-word;">' + v + '</span>';
        }
      })
    },{
      header: _('ID_STATUS'),
      width: 70,
      dataIndex: 'DEP_STATUS',
      align: 'center',
      tpl: new Ext.XTemplate('{DEP_STATUS:this.formatStatus}', {
        formatStatus: function(v) {
          switch(v){
            case 'ACTIVE':return '<font color="green">' + _('ID_ACTIVE') + '</font>';break;
            case 'INACTIVE':return '<font color="red">' + _('ID_INACTIVE') + '</font>';break;
          }
        }
      })
    },{
      header: _('ID_MANAGER'),
      width: 220,
      dataIndex: 'DEP_MANAGER_NAME'
    },{
      header: _('ID_USERS'),
      width: 70,
      dataIndex: 'DEP_TOTAL_USERS',
      align: 'center',
      sortType: 'asFloat'
    }
    ],
    selModel: smodel,
    tbar: [newButton,'-',newSubButton,'-',editButton, deleteButton,'-',usersButton],
    dataUrl:'departments_Ajax?action=departmentList',
    root: rootNode
  });

  treePanel.on('contextmenu', treeContextHandler);

  viewport = new Ext.Viewport({
    layout: 'anchor',
    //autoScroll: true,
    //autoHeight: true,
    items: [treePanel]
  });

});

//Funtion Handles Context Menu Opening
treeContextHandler = function(node, evt){
  node.select();
  var coords = evt.getXY();
  contextMenu.showAt([coords[0], coords[1]]);
};

//Do Nothing Function
DoNothing = function(){};

//Call New Department at Root
NewRootDepartment = function(){
  newForm.getForm().reset();
  newForm.getForm().findField('parent').setValue('');
  newForm.getForm().items.items[1].focus('',500);
  //newForm.getForm().items.items[1].setValue('');
  w = new Ext.Window({
    title: _('ID_NEW_DEPARTMENT'),
    autoHeight: true,
    modal: true,
    closable: false,
    width: 420,
    items: [newForm],
    id: 'w'
  });
  w.show();
};

//Call New Sub Department at Parent
NewSubDepartment = function(){
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  newForm.getForm().reset();
  newForm.getForm().findField('parent').setValue(dep_node.attributes.DEP_UID);
  newForm.getForm().items.items[1].focus('',500);
  w = new Ext.Window({
    title: _('ID_NEW_SUB_DEPARTMENT'),
    autoHeight: true,
    modal: true,
    closable: false,
    width: 420,
    items: [newForm],
    id: 'w'
  });
  w.show();
};

//Close PopUp Window
CloseWindow = function(){
  Ext.getCmp('w').hide();
};
SaveNewDepartment = function(){
  waitLoading.show();
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  if (dep_node) dep_node.unselect();
  var dep_name = newForm.getForm().findField('dep_name').getValue();
  dep_name = dep_name.trim();
  if (dep_name==''){
    waitLoading.hide();
    return;
  }
  var dep_parent = newForm.getForm().findField('parent').getValue();
  Ext.Ajax.request({
    url: 'departments_Ajax',
    params: {action: 'checkDepartmentName', name: dep_name, parent: dep_parent},
    success: function(resp, opt){
      var res_ok = eval(resp.responseText);
      if (res_ok){
        Ext.Ajax.request({
          url: 'departments_Ajax',
          params: {action: 'saveDepartment', name: dep_name, parent: dep_parent},
          success: function(r,o){
            waitLoading.hide();
            var xtree = Ext.getCmp('treePanel');
            treePanel.getLoader().load(rootNode);
            newSubButton.disable();
            editButton.disable();
            deleteButton.disable();
            usersButton.disable();
            newForm.getForm().findField('dep_name').reset();
            CloseWindow();
            PMExt.notify(_('ID_DEPARTMENTS'), _('ID_DEPARTMENT_SUCCESS_NEW'));
          },
          failure: function(r,o){
            waitLoading.hide();
            DoNothing();
          }
        });
      }else{
        waitLoading.hide();
        PMExt.error(_('ID_DEPARTMENTS'), _('ID_DEPARTMENT_EXISTS'));
      }
    },
    failure: function(resp, opt){
      waitLoading.hide();
      DoNothing();
    }
  });
};

SaveEditDepartment = function(){
  var dep_name = editForm.getForm().findField('dep_name').getValue();
  dep_name = dep_name.trim();
  if (dep_name=='') return;
  var dep_parent = editForm.getForm().findField('dep_parent').getValue();
  var dep_uid = editForm.getForm().findField('dep_uid').getValue();
  var dep_status = editForm.getForm().findField('status').getValue();
  var dep_manager = editForm.getForm().findField('manager').getValue();
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  if (dep_node) dep_node.unselect();
  Ext.Ajax.request({
    url: 'departments_Ajax',
    params: {action: 'checkEditDepartmentName', name: dep_name, parent: dep_parent, uid: dep_uid},
    success: function(resp, opt){
      var res_ok = eval(resp.responseText);
      if (res_ok){
        Ext.Ajax.request({
          url: 'departments_Ajax',
          params: {action: 'updateDepartment', uid: dep_uid, name: dep_name, status: dep_status, manager: dep_manager},
          success: function(r,o){
            var xtree = Ext.getCmp('treePanel');
            xtree.getLoader().load(xtree.root);
            newSubButton.disable();
            editButton.disable();
            deleteButton.disable();
            usersButton.disable();
            newForm.getForm().findField('dep_name').reset();
            CloseWindow();
            PMExt.notify(_('ID_DEPARTMENTS'), _('ID_DEPARTMENT_SUCCESS_UPDATE'));
          },
          failure: function(r,o){
            DoNothing();
          }
        });
      }else{
        PMExt.error(_('ID_DEPARTMENTS'), _('ID_DEPARTMENT_EXISTS'));
      }
    },
    failure: function(resp, opt){
      DoNothing();
    }
  });
};

//Edit Department Action
EditDepartmentAction = function(){
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  var strName = stringReplace("&lt;", "<", dep_node.attributes.DEP_TITLE);
  strName = stringReplace("&gt;", ">", strName);

  editForm.getForm().findField('dep_uid').setValue(dep_node.attributes.DEP_UID);
  editForm.getForm().findField('dep_parent').setValue(dep_node.attributes.DEP_PARENT);
  editForm.getForm().findField('dep_name').setValue(strName);
  editForm.getForm().findField('status').setValue(dep_node.attributes.DEP_STATUS);
  editForm.getForm().findField('manager').getStore().addListener('load',function(s,r,o){
    editForm.getForm().findField('manager').setValue(dep_node.attributes.DEP_MANAGER);
  });
  editForm.getForm().findField('manager').store.load({params: {DEP_UID: dep_node.attributes.DEP_UID}});
  w = new Ext.Window({
    title: _('ID_EDIT_DEPARTMENT'),
    autoHeight: true,
    modal: true,
    closable: false,
    width: 420,
    items: [editForm],
    id: 'w'
  });
  w.show();
};

//Delete Department Action
DeleteDepartmentAction = function(){
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  if (!dep_node.attributes.leaf) return;
  var DEP_UID = dep_node.attributes.DEP_UID;
  if (dep_node) dep_node.unselect();
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'departments_Ajax',
    params: {action: 'canDeleteDepartment', dep_uid: DEP_UID},
    success: function(r,o){
      viewport.getEl().unmask();
      var response = Ext.util.JSON.decode(r.responseText);
      if (response.success){

        Ext.Msg.confirm(_('ID_DEPARTMENTS'), _('ID_CONFIRM_DELETE_DEPARTMENT'),
            function(btn, text){
          if (btn=='yes'){
            viewport.getEl().mask(_('ID_PROCESSING'));
            Ext.Ajax.request({
              url: 'departments_Ajax',
              params: {action: 'deleteDepartment', DEP_UID: DEP_UID},
              success: function(r,o){
                viewport.getEl().unmask();
                treePanel.getRootNode().reload();
                newSubButton.disable();
                editButton.disable();
                deleteButton.disable();
                usersButton.disable();
                PMExt.notify(_('ID_DEPARTMENTS'), _('ID_DEPARTMENT_SUCCESS_DELETE'));
              },
              failure: function(r,o){
                viewport.getEl().unmask();
              }
            });
          }
        });

      }else{
        PMExt.error(_('ID_DEPARTMENTS'),_('ID_MSG_CANNOT_DELETE_DEPARTMENT'));
      }
    },
    failure: function(r,o){
      viewport.getEl().unmask();

    }
  });
};

//User Assined Action
UsersButtonAction = function(){
  var dep_node = Ext.getCmp('treePanel').getSelectionModel().getSelectedNode();
  var DEP_UID = dep_node.attributes.DEP_UID;
  location.href= 'departmentUsers?dUID=' + DEP_UID;
};