/*
 * @author: Qennix
 * Jan 18th, 2011
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

var store;
var cmodel;
var smodel;
var infoGrid;
var viewport;
var bbarpaging;
var w;

var viewButton;
var searchButton;
var contextMenu;
var pageSize;


Ext.onReady(function(){
  Ext.QuickTips.init();

  pageSize = parseInt(CONFIG.pageSize);

  viewButton = new Ext.Action({
    text: _('ID_VIEW'),
    iconCls: 'button_menu_ext ss_sprite  ss_table',
    handler: ShowSelectedLog,
    disabled: true
  });

  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  contextMenu = new Ext.menu.Menu({
    items: [viewButton]
  });

  searchText = new Ext.form.TextField ({
    id: 'searchTxt',
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
    ctCls:"pm_search_x_button_des",
    handler: GridByDefault
  });

  viewForm = new Ext.FormPanel({
    url: 'data_casesSchedulerLog',
    frame: true,
    title: _('ID_TITLE_LOG_DETAIL'),
    labelWidth: 150,
    items:[
           {xtype: 'label', fieldLabel: _('ID_SCHEDULER_TASK'), id: 'stask', width: 250},
           {xtype: 'label', fieldLabel: _('ID_TASK_ID'), id: 'task', width: 250},
           {xtype: 'label', fieldLabel: _('ID_PROCESS_ID'), id: 'process', width: 250},
           {xtype: 'label', fieldLabel: _('ID_USER'), id: 'user', width: 250},
           {xtype: 'label', fieldLabel: _('ID_EXECUTION_DATE'), id: 'date', width: 250},
           {xtype: 'label', fieldLabel: _('ID_EXECUTION_HOUR'), id: 'hour', width: 250},
           {xtype: 'label', fieldLabel: _('ID_EXECUTION_STATUS'), id: 'status', width: 250},
           {xtype: 'label', fieldLabel: _('ID_CREATED_CASE_STATUS'), id: 'cstatus', width: 250},
           {xtype: 'label', fieldLabel: _('ID_ROUTED_CASE_STATUS'), id: 'rstatus', width: 250}
           ],

           buttons: [
                     {text: _('ID_CLOSE'), handler: CloseView}
                     ]
  });

  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'data_casesSchedulerLog'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'rows',
      totalProperty: 'results',
      fields : [
                {name : 'LOG_CASE_UID'},
                {name : 'PRO_UID'},
                {name : 'TAS_UID'},
                {name : 'USR_NAME'},
                {name : 'EXEC_DATE'},
                {name : 'EXEC_HOUR'},
                {name : 'RESULT'},
                {name : 'SCH_UID'},
                {name : 'WS_CREATE_CASE_STATUS'},
                {name : 'WS_ROUTE_CASE_STATUS'}
                ]
    })
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
              {id:'LOG_CASE_UID', dataIndex: 'LOG_CASE_UID', hidden:true, hideable:false},
              {header: _('ID_DATE_LABEL'), dataIndex: 'EXEC_DATE', width: 30, align:'left'},
              {header: _('ID_TIME_LABEL'), dataIndex: 'EXEC_HOUR', width: 30, hidden:false, align:'left'},
              {header: _('ID_USER'), dataIndex: 'USR_NAME', width: 40, hidden:false, align:'left'},
              {header: _('ID_RESULT'), dataIndex: 'RESULT', width: 40, hidden:false, align:'left'},
              {header: _('ID_CREATED_CASE_STATUS'), dataIndex: 'WS_CREATE_CASE_STATUS', width: 80, hidden:false, align:'left'},
              {header: _('ID_ROUTED_CASE_STATUS'), dataIndex: 'WS_ROUTE_CASE_STATUS', width: 80, hidden:false, align:'left'}
              ]
  });

  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm){
        viewButton.enable();
      },
      rowdeselect: function(sm){
        viewButton.disable();
      }
    }
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
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_ROWS_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_ROWS_MESSAGE'),
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  infoGrid = new Ext.grid.GridPanel({
    title : _('ID_LOG_CASE_SCHEDULER'),
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'gridCasesSchedule',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [viewButton,{xtype: 'tbfill'},searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: ShowSelectedLog
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

  GridByDefault();

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
}

//Do Nothing Function
DoNothing = function(){}

//Handles DoubleClick's Grid
ShowSelectedLog = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    Ext.getCmp('stask').setText(rowSelected.data.SCH_UID);
    Ext.getCmp('task').setText(rowSelected.data.TAS_UID);
    Ext.getCmp('process').setText(rowSelected.data.PRO_UID);
    Ext.getCmp('user').setText(rowSelected.data.USR_NAME);
    Ext.getCmp('date').setText(rowSelected.data.EXEC_DATE);
    Ext.getCmp('hour').setText(rowSelected.data.EXEC_HOUR);
    Ext.getCmp('status').setText(rowSelected.data.RESULT);
    Ext.getCmp('cstatus').setText(rowSelected.data.WS_CREATE_CASE_STATUS);
    Ext.getCmp('rstatus').setText(rowSelected.data.WS_ROUTE_CASE_STATUS);
    w = new Ext.Window({
      autoHeight: true,
      width: 550,
      resizable: false,
      closable: false,
      modal: true,
      items: [viewForm]
    });
    w.show();
  }
}


//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  infoGrid.store.load();
}

//Close View Dialog
CloseView = function(){
  w.hide();
}

//Do Search Function
DoSearch = function(){
  infoGrid.store.load({params: {textFilter: searchText.getValue()}});
}

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
    url: 'casesSchedulerLog_Ajax',
    params: {action:'updatePageSize', size: pageSize}
  });
};
