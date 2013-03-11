/*
 * @author: Qennix
 * Feb 22nd, 2011
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
    		Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
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
			EditCalendarAction();
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
var copyButton;
var searchButton;

var searchText;
var contextMenu;
var pageSize;

var cal_default = '00000000000000000000000000000001';

Ext.onReady(function(){
    Ext.QuickTips.init();
    
    pageSize = parseInt(CONFIG.pageSize);
    
    newButton = new Ext.Action({
    	text: _('ID_NEW'),
    	iconCls: 'button_menu_ext ss_sprite  ss_add',
    	handler: NewCalendarAction
    });
    
    editButton = new Ext.Action({
    	text: _('ID_EDIT'),
    	iconCls: 'button_menu_ext ss_sprite  ss_pencil',
    	handler: EditCalendarAction,
    	disabled: true	
    });
    
    deleteButton = new Ext.Action({
    	text: _('ID_DELETE'),
    	iconCls: 'button_menu_ext ss_sprite  ss_delete',
    	handler: DeleteButtonAction,
    	disabled: true
    });
    
    copyButton = new Ext.Action({
    	text: _('ID_COPY'),
    	iconCls: 'button_menu_ext ss_sprite ss_calendar_add',
    	handler: CopyButtonAction,
    	disabled: true
    });
    
    searchButton = new Ext.Action({
    	text: _('ID_SEARCH'),
    	handler: DoSearch
    });
    
    contextMenu = new Ext.menu.Menu({
    	items: [editButton, deleteButton,'-',copyButton]
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
    			rowSelected = infoGrid.getSelectionModel().getSelected();
    			(rowSelected.data.CALENDAR_UID == cal_default) ? deleteButton.disable() : deleteButton.enable();
    			copyButton.enable();
    		},
    		rowdeselect: function(sm){
    			editButton.disable();
    			deleteButton.disable();
    			copyButton.disable();
    		}
    	}
    });
    
    store = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'calendar_Ajax?action=calendarList'
          }),
    	reader : new Ext.data.JsonReader( {
    		root: 'cals',
    		totalProperty: 'total_cals',
    		fields : [
    		    {name : 'CALENDAR_UID'},
    		    {name : 'CALENDAR_NAME'},
    		    {name : 'CALENDAR_DESCRIPTION'},
    		    {name : 'CALENDAR_STATUS'},
    		    {name : 'TOTAL_USERS', type: 'int'},
    		    {name : 'TOTAL_PROCESS', type: 'int'},
    		    {name : 'TOTAL_TASKS', type: 'int'}
    		    ]
    	})
    });
    
    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {id:'CALENDAR_UID', dataIndex: 'CALENDAR_UID', hidden:true, hideable:false},
            {header: _('ID_NAME'), dataIndex: 'CALENDAR_NAME', width: 200, align:'left'},
            {header: _('ID_DESCRIPTION'), dataIndex: 'CALENDAR_DESCRIPTION', width: 200, align:'left'},
            {header: _('ID_STATUS'), dataIndex: 'CALENDAR_STATUS', width: 130, align:'center', renderer: render_status},
            {header: _('ID_USERS'), dataIndex: 'TOTAL_USERS', width: 69, align:'center'},
            {header: _('ID_PROCESSES'), dataIndex: 'TOTAL_PROCESS', width: 69, align:'center'},
            {header: _('ID_TASKS'), dataIndex: 'TOTAL_TASKS', width: 69, align:'center'}
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
        displayMsg: _('ID_GRID_PAGE_DISPLAYING_CALENDAR_MESSAGE') + '&nbsp; &nbsp; ',
        emptyMsg: _('ID_GRID_PAGE_NO_CALENDAR_MESSAGE'),
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
    	title : _('ID_CALENDARS'),
    	store: store,
    	cm: cmodel,
    	sm: smodel,
    	tbar: [newButton, '-', editButton, deleteButton,'-',copyButton, {xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    	bbar: bbarpaging,
    	listeners: {
    		rowdblclick: EditCalendarAction
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

//Open New Calendar
NewCalendarAction = function() {
	//location.href = 'calendarEdit';
	location.href = '../admin/calendarEdit';
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

//Edit Calendar Action
EditCalendarAction = function() {
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){
	  location.href = '../admin/calendarEdit?edit=1&id=' + rowSelected.data.CALENDAR_UID;
  }
};

//Delete Button Action
DeleteButtonAction = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  viewport.getEl().mask(_('ID_PROCESSING'));
  Ext.Ajax.request({
    url: 'calendar_Ajax',
    params: {action: 'canDeleteCalendar', CAL_UID: rowSelected.data.CALENDAR_UID},
    success: function(r,o){
      viewport.getEl().unmask();
      var resp = Ext.util.JSON.decode(r.responseText);
      if (resp.success){
    	Ext.Msg.confirm(_('ID_CONFIRM'),_('ID_CONFIRM_DELETE_CALENDAR'),
    	  function(btn, text){
    		if (btn=='yes'){
    			viewport.getEl().mask(_('ID_PROCESSING'));
    	    	Ext.Ajax.request({
    	    		url: 'calendar_Ajax',
    	    		params: {action: 'deleteCalendar', CAL_UID: rowSelected.data.CALENDAR_UID},
    	    		success: function(r,o){
    	    		  viewport.getEl().unmask();
    	    		  editButton.disable();
    	    		  deleteButton.disable();
    	    		  copyButton.disable();
    	    		  DoSearch();
    	    		  PMExt.notify(_('ID_CALENDARS'),_('ID_CALENDAR_SUCCESS_DELETE'));
    	    		},
    	    		failure: function(r,o){
    	    		  viewport.getEl().unmask();
    	    		}
    	    	});	
    		}
    	  }		
    	);
      }else{
    	 PMExt.error(_('ID_CALENDARS'),_('ID_MSG_CANNOT_DELETE_CALENDAR')); 
      }	
    },
    failure: function(r,o){
      viewport.getEl().unmask();
    }
  });
};

//Render Status
render_status = function(v){
  switch(v){
  case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
  case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>';; break;
  case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>';; break;
  }
};

//Members Button Action
CopyButtonAction = function(){
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if (rowSelected){    
    location.href = '../admin/calendarEdit?cp=1&id=' + rowSelected.data.CALENDAR_UID;
  }
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
  url: 'calendar_Ajax',
  params: {action:'updatePageSize', size: pageSize}
  });
};