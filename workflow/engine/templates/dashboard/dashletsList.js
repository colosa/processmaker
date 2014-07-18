new Ext.KeyMap(document, [{
  key: Ext.EventObject.F5,
  fn: function(k, e) {
    if (!e.ctrlKey) {
      if (Ext.isIE) {
        e.browserEvent.keyCode = 8;
      }
      e.stopEvent();
      document.location = document.location;
    }
    else {
      Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
    }
  }
},
{
  key: Ext.EventObject.DELETE,
  fn: function(k, e) {
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected) {
      deleteDashletInstance();
    }
  }
},
{
  key: Ext.EventObject.F2,
  fn: function(k, e) {
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected){
      editDashletInstance();
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
var statusButton;
//var searchButton;
//var searchText;
//var clearTextButton;
var actionButtons;
var contextMenu;

Ext.onReady(function(){
  Ext.QuickTips.init();

  pageSize = 20; //parseInt(CONFIG.pageSize);

  newButton = new Ext.Action({
    text: _('ID_NEW'),
    iconCls: 'button_menu_ext ss_sprite ss_add',
    handler: newDashletInstance
  });

  editButton = new Ext.Action({
    text: _('ID_EDIT'),
    iconCls: 'button_menu_ext ss_sprite  ss_pencil',
    handler: editDashletInstance,
    disabled: true
  });

  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite  ss_delete',
    handler: deleteDashletInstance,
    disabled: true
  });

  statusButton = new Ext.Action({
	text: _('ID_STATUS'),
	iconCls: 'silk-add',
	handler: statusDashletInstance,
	disabled: true
  });

  /*searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: doSearch
  });

  searchText = new Ext.form.TextField ({
    id: 'searchText',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
    emptyText: _('ID_ENTER_SEARCH_TERM'),
    listeners: {
      specialkey: function(f, e){
        if (e.getKey() == e.ENTER) {
          doSearch();
        }
      },
      focus: function(f, e) {
        var row = infoGrid.getSelectionModel().getSelected();
        infoGrid.getSelectionModel().deselectRow(infoGrid.getStore().indexOf(row));
      }
    }
  });

  clearTextButton = new Ext.Action({
    text: 'X',
    ctCls:'pm_search_x_button',
    handler: gridByDefault
  });*/

  contextMenu = new Ext.menu.Menu({
    items: [editButton, deleteButton, statusButton]
  });

  actionButtons = [newButton, '-', editButton, deleteButton, statusButton/*, {xtype: 'tbfill'}, searchText, clearTextButton, searchButton*/];

  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm, index, record){
        editButton.enable();
        deleteButton.enable();
        statusButton.enable();
        if (typeof(_rowselect) !== 'undefined') {
          if (Ext.isArray(_rowselect)) {
            for (var i = 0; i < _rowselect.length; i++) {
              if (Ext.isFunction(_rowselect[i])) {
                _rowselect[i](sm, index, record);
              }
            }
          }
        }

        if( record.data.DAS_INS_STATUS == 1 ){
        	statusButton.setIconClass('icon-activate');
        	statusButton.setText( _('ID_DEACTIVATE') );
        } else {
        	statusButton.setIconClass('icon-deactivate');
        	statusButton.setText( _('ID_ACTIVATE') );
        } 
        
      },
      rowdeselect: function(sm, index, record){
        editButton.disable();
        deleteButton.disable();
        statusButton.disable();
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
      url: 'getDashletsInstances'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'dashletsInstances',
      totalProperty: 'totalDashletsInstances',
      fields : [
        {name : 'DAS_INS_UID'},
        {name : "DAS_INS_TITLE"},
        {name : 'DAS_TITLE'},
        {name : 'DAS_VERSION'},
        {name : 'DAS_INS_OWNER_TITLE'},
        {name : 'DAS_INS_UPDATE_DATE'},
        {name : 'DAS_INS_STATUS_LABEL'},
        {name : 'DAS_INS_STATUS'}
      ]
    }),
    sortInfo: {
        field: 'DAS_INS_TITLE',
        direction: 'ASC'
    }
  });

  function formatLineWrap(value){
      str = '<div class="title-dashboard-text">'+value+'</div>';
      return str;
  }

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
      {id:'DAS_INS_UID', dataIndex: 'DAS_INS_UID', hidden:true, hideable:false},
      {header: _("ID_TITLE"), dataIndex: "DAS_INS_TITLE", width: 150, hidden: false, align: "left", renderer : formatLineWrap},
      {header: _("ID_DASHLET"), dataIndex: "DAS_TITLE", width: 200, hidden: false, align: "left"},
      {header: _('ID_VERSION'), dataIndex: 'DAS_VERSION', width: 60, hidden: false, align: 'center'},
      {header: _('ID_ASSIGNED_TO'), dataIndex: 'DAS_INS_OWNER_TITLE', width: 200, hidden: false, align: 'center'},
      {header: _('ID_UPDATE_DATE'), dataIndex: 'DAS_INS_UPDATE_DATE', width: 80, hidden: false, align: 'center'},
      {header: _('ID_STATUS'), dataIndex: 'DAS_INS_STATUS_LABEL', width: 60, hidden: false, align: 'center'}
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
        //UpdatePageConfig(d.data['size']);
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
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_DASHLET_MESSAGE') + '&nbsp; &nbsp; ',
    //displayMsg: 'Displaying dashlets instances {0} - {1} of {2}' + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_DASHLET_MESSAGE'),
    //emptyMsg: 'No dashlets instances to display',
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'gridDashletList',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    //iconCls:'icon-grid',
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    //title : _('ID_DASHLETS_INSTANCES'),
    title : _('ID_DASHLETS_INSTANCES'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: actionButtons,
    bbar: bbarpaging,
    listeners: {
      rowdblclick: editDashletInstance,
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

  if (typeof(__DASHBOARD_ERROR__) !== 'undefined') {
    PMExt.notify(_('ID_DASHBOARD'), __DASHBOARD_ERROR__);
  }
});

//Funtion Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    contextMenu.showAt([coords[0], coords[1]]);
};

//Load Grid By Default
gridByDefault = function(){
  //searchText.reset();
  infoGrid.store.load();
};

//Do Search Function
/*doSearch = function(){
   infoGrid.store.load({params: {textFilter: searchText.getValue()}});
};*/

//Update Page Size Configuration
/*updatePageConfig = function(pageSize) {
  Ext.Ajax.request({
    url: 'updatePageConfig',
    params: {size: pageSize}
  });
};*/

//New Dashlet Instance Action
newDashletInstance = function() {
  location.href = 'dashletInstanceForm';
};

//Edit Dashlet Instance Action
editDashletInstance = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    location.href = 'dashletInstanceForm?DAS_INS_UID=' + rowSelected.data.DAS_INS_UID;
  }
};

//Delete Dashlet Instance Action
deleteDashletInstance = function(){
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
    Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_DASHLET_INSTANCE'),function(btn, text)
    //Ext.Msg.confirm(_('ID_CONFIRM'), 'Do you want to delete this Dashlet Instance?', function(btn, text)
    {
      if (btn == 'yes') {
        viewport.getEl().mask(_('ID_PROCESSING'));
        Ext.Ajax.request({
          url: 'deleteDashletInstance',
          params: {DAS_INS_UID: rowSelected.data.DAS_INS_UID},
          success: function(r, o){
            viewport.getEl().unmask();
            response = Ext.util.JSON.decode(r.responseText);
            if (response.status == 'OK') {
              PMExt.notify(_('ID_DASHLET_INSTANCE'),_('ID_DASHLET_SUCCESS_DELETE'));
              //PMExt.notify('Dashlet Instance', 'Dashlet instance deleted sucessfully.');
            }
            else {
              PMExt.error(_('ID_DASHLET_INSTANCE'), response.message);
              //PMExt.error('Dashlet Instance', response.message);
            }
            //doSearch();
            editButton.disable();
            deleteButton.disable();
            statusButton.disable();
            infoGrid.store.load();
          },
          failure: function(r, o){
            viewport.getEl().unmask();
          }
        });
      }
    });
  }
};

//Status Dashlet Instance Action
statusDashletInstance = function(){
	  var rows = infoGrid.getSelectionModel().getSelections();
	  if( rows.length > 0 ) {
	    for(i=0; i<rows.length; i++) {
	    	var status;
	    	if(rows[i].data.DAS_INS_STATUS == 1){
	    		status = 0;
	    	} else {
	    		status = 1;
	    	}

	    	var data = {
	    		DAS_INS_UID: rows[i].data.DAS_INS_UID,
	    		DAS_INS_TITLE: rows[i].data.DAS_INS_TITLE,
	    		DAS_INS_STATUS: status
	    	};
	    	
	    	Ext.Ajax.request({
	             url:      'saveDashletInstance',
	             method:   'POST',
	             params:   data,
	             success:  function (result, request) {
	                         editButton.disable();
	                         deleteButton.disable();
	                         statusButton.disable();

	                         statusButton.setIconClass('silk-add');
	                     	 statusButton.setText( _('ID_STATUS') );
	                     	 infoGrid.store.load();
	                      },
	             failure: function (result, request) {
	                        Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
	                      }
	        });
	    }
	  } else {
	     Ext.Msg.show({
	      title:'',
	      msg: _('ID_NO_SELECTION_WARNING'),
	      buttons: Ext.Msg.INFO,
	      fn: function(){},
	      animEl: 'elId',
	      icon: Ext.MessageBox.INFO,
	      buttons: Ext.MessageBox.OK
	    });
	  }
};
