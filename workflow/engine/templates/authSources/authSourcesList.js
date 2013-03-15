/*
 * @author: Qennix
 * Feb 11st, 2011
 */
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
        Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
      }
    }
},
{
  key: Ext.EventObject.DELETE,
  fn: function(k,e){
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected){
      DeleteAuthSource();
    }
  }
},
{
  key: Ext.EventObject.F2,
  fn: function(k,e){
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected){
      EditAuthSource();
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
var actionButtons;

Ext.onReady(function(){
    Ext.QuickTips.init();

    pageSize = parseInt(CONFIG.pageSize);

    newButton = new Ext.Action({
      text: _('ID_NEW'),
      iconCls: 'button_menu_ext ss_sprite ss_add',
      handler: NewAuthSource
    });

    editButton = new Ext.Action({
      text: _('ID_EDIT'),
      iconCls: 'button_menu_ext ss_sprite  ss_pencil',
      handler: EditAuthSource,
      disabled: true
    });

    deleteButton = new Ext.Action({
      text: _('ID_DELETE'),
      iconCls: 'button_menu_ext ss_sprite  ss_delete',
      handler: DeleteAuthSource,
      disabled: true
    });

    usersButton = new Ext.Action({
      text: _('ID_IMPORT_USERS'),
      iconCls: 'button_menu_ext ss_sprite  ss_user_add',
      handler: ImportUsers,
      disabled: true

    });

    searchButton = new Ext.Action({
      text: _('ID_SEARCH'),
      handler: DoSearch
    });

    contextMenu = new Ext.menu.Menu({
      items: [editButton, deleteButton,'-',usersButton]
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

    actionButtons = _addPluginActions([newButton, '-', editButton, deleteButton, '-', usersButton, {xtype: 'tbfill'}, searchText, clearTextButton, searchButton]);

    smodel = new Ext.grid.RowSelectionModel({
      singleSelect: true,
      listeners:{
        rowselect: function(sm, index, record){
          editButton.enable();
          deleteButton.enable();
          usersButton.enable();
          if (typeof(_rowselect) !== 'undefined') {
            if (Ext.isArray(_rowselect)) {
              for (var i = 0; i < _rowselect.length; i++) {
                if (Ext.isFunction(_rowselect[i])) {
                  _rowselect[i](sm, index, record);
                }
              }
            }
          }
        },
        rowdeselect: function(sm, index, record){
          editButton.disable();
          deleteButton.disable();
          usersButton.disable();
          if (typeof(_rowdeselect) !== 'undefined') {
            if (Ext.isArray(_rowdeselect)) {
              for (var i = 0; i < _rowdeselect.length; i++) {
                if (Ext.isFunction(_rowdeselect[i])) {
                  _rowdeselect[i](sm, index, record);
                }
              }
            }
          }
        }
      }
    });

    store = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'authSources_Ajax?action=authSourcesList'
          }),
      reader : new Ext.data.JsonReader( {
        root: 'sources',
        totalProperty: 'total_sources',
        fields : [
            {name : 'AUTH_SOURCE_UID'},
            {name : 'AUTH_SOURCE_NAME'},
            {name : 'AUTH_SOURCE_PROVIDER'},
            {name : 'AUTH_SOURCE_SERVER_NAME'},
            {name : 'AUTH_SOURCE_PORT'},
            {name : 'AUTH_SOURCE_ENABLED_TLS'},
            {name : 'AUTH_SOURCE_VERSION'},
            {name : 'AUTH_SOURCE_BASE_DN'},
            {name : 'AUTH_ANONYMOUS'},
            {name : 'AUTH_SOURCE_SEARCH_USER'},
            {name : 'AUTH_SOURCE_ATTRIBUTES'},
            {name : 'AUTH_SOURCE_OBJECT_CLASSES'},
            {name : 'CURRENT_USERS', type:'int'}
            ]
      })
    });

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {id:'AUTH_SOURCE_UID', dataIndex: 'AUTH_SOURCE_UID', hidden:true, hideable:false},
            {header: _('ID_NAME'), dataIndex: 'AUTH_SOURCE_NAME', width: 200, hidden:false, align:'left'},
            {header: _('ID_PROVIDER'), dataIndex: 'AUTH_SOURCE_PROVIDER', width: 120, hidden: false, align: 'center'},
            {header: _('ID_SERVER_NAME'), dataIndex: 'AUTH_SOURCE_SERVER_NAME', width: 180, hidden: false, align: 'center'},
            {header: _('ID_PORT'), dataIndex: 'AUTH_SOURCE_PORT', width: 60, hidden: false, align: 'center'},
            {header: _('ID_ENABLED_TLS'), dataIndex: 'AUTH_SOURCE_ENABLED_TLS', width: 90, hidden: false, align: 'center', renderer: show_enabled},
            {header: _('ID_ACTIVE_USERS'), dataIndex: 'CURRENT_USERS', width: 90, hidden: false, align: 'center'}
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
      displayMsg: _('ID_GRID_PAGE_DISPLAYING_AUTHENTICATION_MESSAGE') + '&nbsp; &nbsp; ',
      emptyMsg: _('ID_GRID_PAGE_NO_AUTHENTICATION_MESSAGE'),
      items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
    });

    infoGrid = new Ext.grid.GridPanel({
      region: 'center',
      layout: 'fit',
      id: 'infoGrid',
      height:100,
      autoWidth : true,
      stateful : true,
      stateId : 'grid',
      enableColumnResize: true,
      enableHdMenu: true,
      frame:false,
      //iconCls:'icon-grid',
      columnLines: false,
      viewConfig: {
        forceFit:true
      },
      title : _('ID_AUTH_SOURCES'),
      store: store,
      cm: cmodel,
      sm: smodel,
      tbar: actionButtons,
      bbar: bbarpaging,
      listeners: {
        rowdblclick: EditAuthSource,
        render: function(){
          this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
        }
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

//Render Function Enabled TLS
show_enabled = function(v){
  return (v==0) ? _('ID_DISABLED') : _('ID_ENABLED');
};

//New AuthSource Action
NewAuthSource = function(){
  location.href = 'authSources_SelectType';
};

//Edit AuthSource Action
EditAuthSource = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    location.href = 'authSources_Edit?sUID=' +rowSelected.data.AUTH_SOURCE_UID;
  }
};

//Delete AuthSource Action
DeleteAuthSource = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
      url: 'authSources_Ajax',
      params:  {action: 'canDeleteAuthSource', auth_uid: rowSelected.data.AUTH_SOURCE_UID},
      success: function(r,o){
          viewport.getEl().unmask();
          response = Ext.util.JSON.decode(r.responseText);
          if (response.success){
            Ext.Msg.confirm(_('ID_CONFIRM'),_('ID_CONFIRM_DELETE_AUTHENTICATION'),function(btn,text){
            if (btn=='yes'){
              viewport.getEl().mask(_('ID_PROCESSING'));
                Ext.Ajax.request({
                  url: 'authSources_Ajax',
                  params: {action: 'deleteAuthSource', auth_uid : rowSelected.data.AUTH_SOURCE_UID},
                  success: function(r,o){
                    viewport.getEl().unmask();
                    resp = Ext.util.JSON.decode(r.responseText);
                    if (resp.success){
                      PMExt.notify(_('ID_AUTH_SOURCES'),_('ID_AUTHENTICATION_SUCCESS_DELETE'));
                    }else{
                      PMExt.error(_('ID_AUTH_SOURCES'),resp.error);
                    }
                    DoSearch();
                    editButton.disable();
                    deleteButton.disable();
                    usersButton.disable();
                  },
                  failure: function(r,o){
                    viewport.getEl().unmask();
                  }
                });
            }
            });

          }else{
           PMExt.error(_('ID_AUTH_SOURCES'),_('ID_MSG_CANNOT_DELETE_AUTHENTICATION'));
          }
      },
      failure: function(r,o){
        viewport.getEl().unmask();
      }
    });
  }
};

//Import Users Action
ImportUsers = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
    if (rowSelected){
      location.href = 'authSources_SearchUsers?sUID=' +rowSelected.data.AUTH_SOURCE_UID;
    }
};

// Mover a un archivo más genérico - Start
var _pluginActionButtons = [];
var _rowselect = [];
var _rowdeselect = [];

var _addPluginActions = function(defaultActionButtons) {
  try {
    if (Ext.isArray(_pluginActionButtons)) {
      if (_pluginActionButtons.length > 0) {
        var positionToInsert = _tbfillPosition(defaultActionButtons);
        var leftActionButtons = defaultActionButtons.slice(0, positionToInsert);
        var rightActionButtons = defaultActionButtons.slice(positionToInsert, defaultActionButtons.length - 1);
        return leftActionButtons.concat(_pluginActionButtons.concat(rightActionButtons));
      }
      else {
        return defaultActionButtons;
      }
    }
    else {
      return defaultActionButtons;
    }
  }
  catch (error) {
    return defaultActionButtons;
  }
};

var _tbfillPosition = function(actionButtons) {
  try {
    for (var i = 0; i < actionButtons.length; i++) {
      if (Ext.isObject(actionButtons[i])) {
        if (actionButtons[i].xtype == 'tbfill') {
          return i;
        }
      }
    }
    return i;
  }
  catch (error) {
    return 0;
  }
};

// Mover a un archivo más genérico - End