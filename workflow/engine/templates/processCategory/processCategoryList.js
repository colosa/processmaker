/*
 * @author: Qennix
 * Feb 11st, 2011
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
                                DeleteCategory();
                              }
                            }
                          },
                          {
                            key: Ext.EventObject.F2,
                            fn: function(k,e){
                              iGrid = Ext.getCmp('infoGrid');
                              rowSelected = iGrid.getSelectionModel().getSelected();
                              if (rowSelected){
                                EditCategory();
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
    iconCls: 'button_menu_ext ss_sprite ss_add',
    handler: NewCategoryWindow
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
    iconCls: 'button_menu_ext ss_sprite  ss_pencil',
    handler: EditCategory,
    disabled: true	
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite  ss_delete',
    handler: DeleteCategory,
    disabled: true
  });

  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  contextMenu = new Ext.menu.Menu({
    items: [editButton, deleteButton]
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

  newForm = new Ext.FormPanel({
    url: 'processCategory_Ajax?action=saveNewCategory',
    frame: true, 	
    items:[
           {xtype: 'textfield', fieldLabel: _('ID_CATEGORY_NAME'), name: 'category', width: 250, allowBlank: false}
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: SaveNewCategory},
                     {text: _('ID_CANCEL'), handler: CloseWindow}

                     ]
  });

  editForm = new Ext.FormPanel({
    url: 'processCategory_Ajax?action=updateCategory',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'cat_uid', hidden: true },
           {xtype: 'textfield', fieldLabel: _('ID_CATEGORY_NAME'), name: 'category', width: 250, allowBlank: false}
           ],
           buttons: [
                     {text: _('ID_SAVE'), handler: UpdateCategory},
                     {text: _('ID_CANCEL'), handler: CloseWindow}
                     ]
  });

  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm){
        editButton.enable();
        deleteButton.enable();
      },
      rowdeselect: function(sm){
        editButton.disable();
        deleteButton.disable();
      }
    }
  });

  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'processCategory_Ajax?action=processCategoryList'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'categories',
      totalProperty: 'total_categories',
      fields : [
                {name : 'CATEGORY_UID'},
                {name : 'CATEGORY_PARENT'},
                {name : 'CATEGORY_NAME'},
                {name : 'CATEGORY_ICON'},
                {name : 'TOTAL_PROCESSES', type:'int'}
                ]
    })
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
              {id:'CATEGORY_UID', dataIndex: 'CATEGORY_UID', hidden:true, hideable:false},
              {header: _('ID_CATEGORY_NAME'), dataIndex: 'CATEGORY_NAME', width: 500, hidden:false, align:'left'},
              {header: _('ID_PROCESSES'), dataIndex: 'TOTAL_PROCESSES', width: 100, hidden: false, align: 'center'}  
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
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_CATEGORY_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_CATEGORY_MESSAGE'),
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'gridProcessCategory',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    //iconCls:'icon-grid',
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    title : _('ID_PROCESS_CATEGORY'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [newButton, '-', editButton, deleteButton, {xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: EditCategory,
      render: function(){
        this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
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

//Open New Category Form
NewCategoryWindow = function(){
  newForm.getForm().reset();
  newForm.getForm().items.items[0].focus('',500);
  w = new Ext.Window({
    title: _('ID_NEW_CATEGORY'),
    autoHeight: true,
    width: 420,
    items: [newForm],
    id: 'w',
    model: true
  });
  w.show();
};

//Close Popup Window
CloseWindow = function(){
  Ext.getCmp('w').hide();
};

//Save New Category
SaveNewCategory = function(){
  catName = newForm.getForm().findField('category').getValue();
  catName = catName.trim();
  if (catName == '') return;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'processCategory_Ajax',
    params : {action: 'checkCategoryName', cat_name: catName},
    success: function(r,o){
      viewport.getEl().unmask();
      resp = eval(r.responseText);
      if (resp){
        viewport.getEl().mask(_('ID_PROCESSING'));
        newForm.getForm().submit({
          success: function(f,a){
            viewport.getEl().unmask();
            CloseWindow(); //Hide popup widow
            newForm.getForm().reset(); //Set empty form to next use
            searchText.reset();
            infoGrid.store.load();
            response = Ext.decode(a.response.responseText);
            if (response.success){
              PMExt.notify(_('ID_PROCESS_CATEGORY'),_('ID_CATEGORY_SUCCESS_NEW'));
            }else{
              PMExt.error(_('ID_ERROR'),_('ID_MSG_AJAX_FAILURE'));
            }
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
        PMExt.error(_('ID_PROCESS_CATEGORY'),_('ID_CATEGORY_EXISTS'));
      }
    },
    failure: function(r,o){
      viewport.getEl().unmask();
    }
  });

};

//Update Selected Role
UpdateCategory = function(){
  catUID = editForm.getForm().findField('cat_uid').getValue();
  catName = editForm.getForm().findField('category').getValue();
  catName = catName.trim();
  if (catName == '') return;
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'processCategory_Ajax',
    params : {action: 'checkEditCategoryName', cat_name: catName, cat_uid: catUID},
    success: function(r,o){
      viewport.getEl().unmask();
      resp = eval(r.responseText);
      if (resp){
        viewport.getEl().mask(_('ID_PROCESSING'));
        editForm.getForm().submit({
          success: function(f,a){
            viewport.getEl().unmask();
            CloseWindow(); //Hide popup widow
            newForm.getForm().reset(); //Set empty form to next use
            searchText.reset();
            infoGrid.store.load();
            response = Ext.decode(a.response.responseText);
            if (response.success){
              PMExt.notify(_('ID_PROCESS_CATEGORY'),_('ID_CATEGORY_SUCCESS_UPDATE'));
            }else{
              PMExt.error(_('ID_ERROR'),_('ID_MSG_AJAX_FAILURE'));
            }
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
        PMExt.error(_('ID_PROCESS_CATEGORY'),_('ID_CATEGORY_EXISTS'));
      }
    },
    failure: function(r,o){
      viewport.getEl().unmask();
    }
  });
};

//Edit Selected Role
EditCategory = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    editForm.getForm().findField('cat_uid').setValue(rowSelected.data.CATEGORY_UID);
    editForm.getForm().findField('category').setValue(rowSelected.data.CATEGORY_NAME);
    w = new Ext.Window({
      autoHeight: true,
      width: 420,
      title: _('ID_EDIT_CATEGORY'),
      items: [editForm],
      id: 'w',
      modal: true
    });
    w.show();
  }
};

//Check Can Delete Category
DeleteCategory = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    var swDelete = false;
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
      url: 'processCategory_Ajax',
      params: {action: 'canDeleteCategory', CAT_UID: rowSelected.data.CATEGORY_UID},
      success: function(response, opts){
        viewport.getEl().unmask();
        swDelete = eval(response.responseText);
        if (swDelete){
          Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_CATEGORY'),
              function(btn, text){
            if (btn=="yes"){
              viewport.getEl().mask(_('ID_PROCESSING'));
              Ext.Ajax.request({
                url: 'processCategory_Ajax',
                params: {action: 'deleteCategory', cat_uid: rowSelected.data.CATEGORY_UID},
                success: function(r,o){
                  viewport.getEl().unmask();
                  infoGrid.store.load(); //Reload store grid
                  editButton.disable();  //Disable Edit Button
                  deleteButton.disable(); //Disable Delete Button
                  resp = Ext.decode(r.responseText);
                  if (resp.success){
                    PMExt.notify(_('ID_PROCESS_CATEGORY'),_('ID_CATEGORY_SUCCESS_DELETE'));	
                  }else{
                    PMExt.error(_('ID_ERROR'),_('ID_MSG_AJAX_FAILURE'));
                  }

                },
                failure: function(){viewport.getEl().unmask();}
              });
            }
          });
        }else{
          PMExt.error(_('ID_PROCESS_CATEGORY'),_('ID_MSG_CANNOT_DELETE_CATEGORY'));
        }
      },
      failure: function(){viewport.getEl().unmask(); DoNothing();}

    });
  }
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

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
    url: 'processCategory_Ajax',
    params: {action:'updatePageSize', size: pageSize}
  });
};
