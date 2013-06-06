/*
 * @author: Qennix
 * Jan 10th, 2011
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
        Ext.Msg.alert( _('ID_REFRESH_LABEL') , _('ID_REFRESH_MESSAGE') );
      }
    }
});

var newButton;
var newReportTableButton;
var editButton;
var deleteButton;
var importButton;
var exportButton;

var dataButton;

var store;
var expander;
var cmodel;
var infoGrid;
var viewport;
var smodel;

var rowsSelected;
var externalOption;

Ext.onReady(function(){
    Ext.QuickTips.init();

    pageSize = parseInt(CONFIG.pageSize);

    newButton = new Ext.Action({
      text: _('ID_NEW_ADD_TABLE'),
      iconCls: 'button_menu_ext ss_sprite ss_add',
      handler: NewPMTable
    });

    newReportTableButton = new Ext.Action({
        text: _("ID_NEW"),
        //iconCls: 'button_menu_ext ss_sprite ss_add',
        icon: '/images/addc.png',
        handler: newReportTable
    });

    editButton = new Ext.Action({
      text: _('ID_EDIT'),
      //iconCls: 'button_menu_ext ss_sprite ss_pencil',
      icon: '/images/icon-edit.png',
        handler: EditPMTable,
      disabled: true
    });

    deleteButton = new Ext.Action({
      text: _('ID_DELETE'),
      //iconCls: 'button_menu_ext ss_sprite  ss_delete',
      icon: '/images/delete-16x16.gif',
        handler: DeletePMTable,
      disabled: true
    });

    importButton = new Ext.Action({
      text: _('ID_IMPORT'),
      iconCls: 'silk-add',
      icon: '/images/import.gif',
      handler: ImportPMTable
    });

    exportButton = new Ext.Action({
      text: _('ID_EXPORT'),
      iconCls: 'silk-add',
      icon: '/images/export.png',
      handler: ExportPMTable,
      disabled: true
    });

    dataButton = new Ext.Action({
      text: '&nbsp;' + _('ID_DATA'),
      iconCls: 'silk-add',
      icon: '/images/icon-pmtables.png',
      handler: PMTableData,
      disabled: true
    });

    searchButton = new Ext.Action({
      text: _('ID_SEARCH'),
      handler: DoSearch
    });

    

    var contextMenuItems = new Array();
    contextMenuItems.push(editButton);  
    contextMenuItems.push(deleteButton);
    if (_PLUGIN_SIMPLEREPORTS !== false) {
      
      externalOption = new Ext.Action({
        text:'',
        handler: function() {
          updateTag('plugin@simplereport');
        },
        disabled: false
      });

      contextMenuItems.push(externalOption);
    }

    contextMenu = new Ext.menu.Menu({
      items: contextMenuItems
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

    clearTextButton1 = new Ext.Action({
      text: 'X',
      ctCls:'pm_search_x_button',
      handler: GridByDefault1
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

    cmodelColumns = new Array();
    
    cmodelColumns.push({id:'ADD_TAB_UID', dataIndex: 'ADD_TAB_UID', hidden:true, hideable:false});
    cmodelColumns.push({dataIndex: 'ADD_TAB_TAG', hidden:true, hideable:false});
    cmodelColumns.push({header: _('ID_NAME'), dataIndex: 'ADD_TAB_NAME', width: 300, align:'left', renderer: function(v,p,r){
      return r.get('TYPE') == 'CLASSIC'? v + '&nbsp<span style="font-size:9px; color:green">(old version)</font>' : v;
    }});
    cmodelColumns.push({header: _('ID_DESCRIPTION'), dataIndex: 'ADD_TAB_DESCRIPTION', width: 400, hidden:false, align:'left', renderer: function(v,p,r){
      if (r.get('ADD_TAB_TAG')) {
        tag = r.get('ADD_TAB_TAG').replace('plugin@', '');
        tag = tag.charAt(0).toUpperCase() + tag.slice(1);
      } 
      return r.get('ADD_TAB_TAG') ? '<span style="font-size:9px; color:green">'+tag+':</span> '+ v : v;
    }});
    if (PRO_UID === false) {
      cmodelColumns.push({header: _('ID_PROCESS'), dataIndex: 'PRO_TITLE', width: 300, align:'left'});     
    }
    
    cmodelColumns.push({header: _('ID_TYPE'), dataIndex: 'ADD_TAB_TYPE', width: 400, hidden:true, align:'left'});
    
    cmodel = new Ext.grid.ColumnModel({
      defaults: {
        width: 50,
        sortable: true
      },
      columns: cmodelColumns
    });

    store = new Ext.data.GroupingStore( {
      proxy : new Ext.data.HttpProxy({
        url: 'reportTables_Ajax?action=list' + (PRO_UID !== false ? '&pro_uid=' + PRO_UID : '')
      }),
      reader : new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'count',
        fields : [
          {name : 'ADD_TAB_UID'},
          {name : 'ADD_TAB_NAME'},
          {name : 'ADD_TAB_DESCRIPTION'},
          {name : 'PRO_TITLE'},
          {name : 'TYPE'},
          {name : 'ADD_TAB_TYPE'},
          {name : 'ADD_TAB_TAG'}
        ]
      })
    });

    smodel = new Ext.grid.CheckboxSelectionModel({
      listeners:{
        selectionchange: function(sm){
          var count_rows = sm.getCount();
          switch(count_rows){
          case 0:
            editButton.disable();
            deleteButton.disable();
            exportButton.disable();
            dataButton.disable();
            break;
          case 1:
            editButton.enable();
            deleteButton.enable();
            exportButton.enable();
            dataButton.enable();
            break;
          default:
            editButton.disable();
          deleteButton.enable();
          exportButton.enable();
          dataButton.disable();
            break;
          }
        }
      }
    });

    bbarpaging = new Ext.PagingToolbar({
      pageSize: pageSize,
      store: store,
      displayInfo: true,
      displayMsg: _('ID_GRID_PAGE_DISPLAYING_PMTABLES_MESSAGE') + '&nbsp; &nbsp; ',
      emptyMsg: _('ID_GRID_PAGE_NO_PMTABLES_MESSAGE'),
      items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
    });

    processStore = new Ext.data.Store( {
      autoLoad: true,
      proxy : new Ext.data.HttpProxy({
        url: '../reportTables/reportTables_Ajax',
        method : 'POST'
      }),
      baseParams : {
        action: 'getProcessList'
      },
      reader : new Ext.data.JsonReader( {
        fields : [{name : 'PRO_UID'}, {name : 'PRO_TITLE'},{name : 'PRO_DESCRIPTION'}]
      }),
      listeners: {}
    });

    processComboBox = new Ext.form.ComboBox({
      id: 'PROCESS',
      fieldLabel : 'Process1',
      hiddenName : 'PRO_UID',
      store : processStore,
      emptyText: _("ID_EMPTY_PROCESSES"),
      valueField : 'PRO_UID',
      displayField : 'PRO_TITLE',

      //width: 180,
      editable : true,
      typeAhead: true,
      mode: 'local',
      autocomplete: true,
      triggerAction: 'all',
      forceSelection: false,

      listeners:{
        select: doSearchByProcess
      }
    });

    tbar = new Array();
    tbar.push(newReportTableButton);
    tbar.push('-');
    tbar.push(editButton);
    tbar.push(deleteButton)
    tbar.push('-');
      //dataButton,'-' ,
    tbar.push({xtype: 'tbfill'})
    if (PRO_UID === false) {
      tbar.push(_("ID_PROCESS"));
      tbar.push(processComboBox);
      tbar.push(clearTextButton1);
    }
     
    tbar.push('-');
    tbar.push(searchText);
    tbar.push(clearTextButton);
    tbar.push(searchButton);
    
    
    infoGrid = new Ext.grid.GridPanel({
      region: 'center',
      layout: 'fit',
      id: 'infoGrid',
      height:100,
      autoWidth : true,
      title : _('ID_REPORT_TABLES'),
      stateful : true,
      stateId : 'gridReportMain',
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
      tbar: tbar,
      bbar: bbarpaging,
      listeners: {
        rowdblclick: EditPMTable,
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

            var rowsSelected = Ext.getCmp('infoGrid').getSelectionModel().getSelections();
            tag = rowsSelected[0].get('ADD_TAB_TAG');
            text = tag? _('ID_CONVERT_NATIVE_REP_TABLE'): _('ID_CONVERT_SIMPLE_REPORT');
            if (externalOption) {
              externalOption.setText(text);
            }
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

/////JS FUNCTIONS

//Capitalize String Function
capitalize = function(s){
  s = s.toLowerCase();
  return s.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
};

//Do Nothing Function
DoNothing = function(){};

//Load New PM Table Forms
NewPMTable = function(){
  location.href = 'additionalTablesNew';
};

newReportTable = function(){
  if(PRO_UID !== false)
    location.href = '../reportTables/edit?PRO_UID='+PRO_UID;
  else
    location.href = '../reportTables/edit';
};

//Load PM Table Edition Forms
EditPMTable = function(){
    iGrid = Ext.getCmp('infoGrid');
    row = iGrid.getSelectionModel().getSelected();
    
    if (row.data.TYPE != 'CLASSIC') {
      if(PRO_UID !== false)
          location.href = 'edit?PRO_UID='+PRO_UID+'&id='+row.data.ADD_TAB_UID
      else
          location.href = 'edit?id='+row.data.ADD_TAB_UID
    } else { //old rep tab
      location.href = '../reportTables/reportTables_Edit?REP_TAB_UID='+row.data.ADD_TAB_UID
    }
};

//Confirm PM Table Deletion Tasks
DeletePMTable = function(){
  iGrid = Ext.getCmp('infoGrid');
  rowsSelected = iGrid.getSelectionModel().getSelections();
  var selections = new Array();
  for(i=0; i<rowsSelected.length; i++){
    selections[i] = {id: rowsSelected[i].get('ADD_TAB_UID'), type: rowsSelected[i].get('TYPE')};
  }
  
  Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_PM_TABLE'),
    function(btn, text){
      if (btn=="yes"){
        Ext.Ajax.request({
          url: 'reportTables_Ajax',
          params: {
            action: 'delete',
            rows: Ext.util.JSON.encode(selections)
          },
          success: function(resp){
            result = Ext.util.JSON.decode(resp.responseText);
            if (result.success) {
            iGrid.getStore().reload();
            PMExt.notify(_("ID_DELETION_SUCCESSFULLY"), _("ID_ALL_RECORDS_DELETED_SUCESSFULLY"));
            } else {
            Ext.Msg.alert( _('ID_ERROR'), result.msg);
            }
          },
          failure: function(obj, resp){
            Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
          }
        });
      }
    });
  };

//Load Import PM Table Form
ImportPMTable = function(){
  location.href = 'additionalTablesToImport';
};

//Load Export PM Tables Form
ExportPMTable = function(){
  iGrid = Ext.getCmp('infoGrid');
  rowsSelected = iGrid.getSelectionModel().getSelections();
  location.href = 'additionalTablesToExport?sUID='+RetrieveRowsID(rowsSelected)+'&rand='+Math.random();
};

//Load PM TAble Data
PMTableData = function(){
  iGrid = Ext.getCmp('infoGrid');
  rowsSelected = iGrid.getSelectionModel().getSelections();
    location.href = 'additionalTablesData?sUID='+RetrieveRowsID(rowsSelected)+'&rand='+Math.random();
};

//Gets UIDs from a array of rows
RetrieveRowsID = function(rows){
  var arrAux = new Array();
  for(var c=0; c<rows.length; c++){
    arrAux[c] = rows[c].get('ADD_TAB_UID');
  }
  return arrAux.join(',');
};
//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
  url: 'additionalTablesAjax',
  params: {action:'updatePageSize', size: pageSize}
  });
};

//Do Search Function
DoSearch = function(){
   infoGrid.store.load({params: {textFilter: searchText.getValue()}});
};

//Do Search by process
doSearchByProcess = function(){
   infoGrid.store.load({params: {pro_uid: Ext.getCmp('PROCESS').getValue()}});
};

//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  infoGrid.store.load();
};

GridByDefault1 = function(){
  Ext.getCmp('PROCESS').setValue('');
  infoGrid.store.load();
};

function updateTag(value)
{
  var rowsSelected = Ext.getCmp('infoGrid').getSelectionModel().getSelections();

  Ext.Ajax.request({
    url: 'reportTables_Ajax',
    params: {
      action: 'updateTag',
      ADD_TAB_UID: rowsSelected[0].get('ADD_TAB_UID'),
      value: rowsSelected[0].get('ADD_TAB_TAG') ? '': value
    },
    success: function(resp){
      Ext.getCmp('infoGrid').store.reload();
    },
    failure: function(obj, resp){
      Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
    }
  });
}
