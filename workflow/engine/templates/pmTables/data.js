/**
 * Handle PMtables Data
 * @author Erik A. O. <erik@colosa.com>
 */

var newButton;
var editButton;
var deleteButton;
var importButton;
var backButton;

var store;
var cmodel;
var smodel;
var infoGrid;
var _fields;
var isReport;

Ext.onReady(function(){

  pageSize = 20; //parseInt(CONFIG.pageSize);
  
  isReport = tableDef.PRO_UID ? true : false;
  
  newButton = new Ext.Action({
    text    : _('ID_ADD_ROW'),
    iconCls : 'button_menu_ext ss_sprite ss_add',
    handler : NewPMTableRow,
    disabled: (isReport ? true : false)
  });
  
  editButton = new Ext.Action({
    text     : _('ID_EDIT'),
    iconCls  : 'button_menu_ext ss_sprite  ss_pencil',
    handler  : EditPMTableRow,
    disabled : true
  });

  deleteButton = new Ext.Action({
    text     : _('ID_DELETE'),
    iconCls  : 'button_menu_ext ss_sprite  ss_delete',
    handler  : DeletePMTableRow,
    disabled : true
  });

  importButton = new Ext.Action({
    text    : _('ID_IMPORT_CSV'),
    iconCls : 'silk-add',
    icon    : '/images/import.gif',
    handler : ImportPMTableCSV
  });

  exportButton = new Ext.Action({
    text    : _('ID_EXPORT_CSV'),
    iconCls : 'silk-add',
    icon    : '/images/export.png',
    handler : ExportPMTableCSV
  });

  genDataReportButton = new Ext.Action({
    text: _('ID_REGENERATE_DATA_REPORT'),
    iconCls: 'silk-add',
    icon: '/images/database-tool.png',
    handler: genDataReport,
    disabled: false
  });
  
  backButton = new Ext.Action({
    text    : _('ID_BACK'),
    icon    : '/images/back-icon.png',
    handler : BackPMList
  });
  
  contextMenu = new Ext.menu.Menu({
      items : [ editButton, deleteButton ]
  });
  
  //This loop loads columns and fields to store and column model
  _columns    = new Array();
  _fields     = new Array();
  _idProperty = '__index__';

 //default generated id
  _columns.push({
    id     : _idProperty,
    hidden : true
  });

  _fields.push({name: _idProperty});
  
  for (i=0;i<tableDef.FIELDS.length; i++) {
    switch (tableDef.FIELDS[i].FLD_TYPE) {
      case 'DATE':
        columnRenderer = function (value) { 
          if (!value) return;

          if (!value.dateFormat)
            return value;
          else
            return value.dateFormat('Y-m-d');
        }
        columnAlign = 'left';  
        columnEditor = {
          xtype      : 'datefield',
          format     : 'Y-m-d',
          allowBlank : true
        }; 
        break;

      case 'INTEGER': case 'INT': case 'FLOAT': case 'DOUBLE' :
        columnRenderer = {};
        columnAlign = 'right';
        columnEditor = {
          xtype      : 'numberfield',
          format     : 'Y-m-d',
          allowBlank : true
        }; 
        break;
      
      default:
        columnRenderer = {};
        columnAlign = 'left';
        columnEditor = {
          xtype      : 'textfield',
          allowBlank : true
        };
    }

    column = {
      id        : tableDef.FIELDS[i].FLD_NAME,
      header    : tableDef.FIELDS[i].FLD_DESCRIPTION,
      dataIndex : tableDef.FIELDS[i].FLD_NAME,
      width     : 95,
      align     : 'center',
      renderer  : columnRenderer
    };
    if (tableDef.FIELDS[i].FLD_AUTO_INCREMENT != 1) {
      column.editor = columnEditor
    }
    else {
      column.editor = {
        xtype : 'displayfield',
        style : 'font-size:11px; padding-left:7px'
      }
    }
    _columns.push(column);
    
    _fields.push({name: tableDef.FIELDS[i].FLD_NAME});

    if(_idProperty == '' && tableDef.FIELDS[i].FLD_KEY) {
      _idProperty = tableDef.FIELDS[i].FLD_NAME;
    }
  }
  
  
  smodel = new Ext.grid.CheckboxSelectionModel({
    listeners:{
      selectionchange : function(sm){
        if (isReport) return;

        var count_rows = sm.getCount();
        switch(count_rows){
           case 0:
             editButton.disable();
             deleteButton.disable();
             break;
           case 1:
             editButton.enable();
             deleteButton.enable();
             
             break;
           default:
             editButton.disable();
             //deleteButton.disable();
             break;
        }
      }
    }
  });

  //row editor for table columns grid
  if (!isReport) {
    editor = new Ext.ux.grid.RowEditor({
      saveText  : _("ID_UPDATE"),
      listeners : {
  	    afteredit : {
  	      fn:function(rowEditor, obj, data, rowIndex ){            	  
    		    if (data.phantom === true) {
    			  //store.reload(); // only if it is an insert 
    	    	}
  	      }
  	    }
      }
    });
  }

  Ext.data.DataProxy.addListener('write', function(proxy, action, result, res, rs) {
    //PMExt.notify(_('ID_UPDATE'), res.raw.message)
  });

  // all exception events
  Ext.data.DataProxy.addListener('exception', function(proxy, type, action, options, res) {
    
    if (typeof res.responseText != 'undefined') {
      response = Ext.util.JSON.decode(res.responseText);
      msg = response.message;
    }
    
    if (typeof res.raw != 'undefined') {
      msg = res.raw.msg;
    }

    if(msg == '$$' || typeof msg == 'undefined') 
      return false;
    

    PMExt.error(_('ID_ERROR'), msg);
    infoGrid.store.reload();
  });

  var proxy = new Ext.data.HttpProxy({
    //url: '../pmTablesProxy/getData?id=' + tableDef.ADD_TAB_UID
    api: {
      read   : '../pmTablesProxy/dataView?id=' + tableDef.ADD_TAB_UID,
      create : '../pmTablesProxy/dataCreate?id=' + tableDef.ADD_TAB_UID,
      update : '../pmTablesProxy/dataUpdate?id=' + tableDef.ADD_TAB_UID,
      destroy: '../pmTablesProxy/dataDestroy?id=' + tableDef.ADD_TAB_UID
    },
    baseParams : {id: tableDef.ADD_TAB_UID}
  })

  // The new DataWriter component.
  var writer = new Ext.data.JsonWriter({
      encode: true,
      writeAllFields: false
  });

  var reader = new Ext.data.JsonReader({
    root       : 'rows',
    idProperty : '__index__',
    fields     : _fields,
    idProperty : _idProperty,
    totalProperty: 'count'
  })
  
  store = new Ext.data.Store({
    proxy : proxy,
    reader : reader,
    writer : writer, // <-- plug a DataWriter into the store just as you would a Reader
    autoSave: true // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
  });

  cmodel = new Ext.grid.ColumnModel({
    defaults: {
        width: 50,
        sortable: true
    },
    columns: _columns
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
  
  if (!isReport) {
    tbar = [ 
      newButton,
      '-',
      editButton,
      deleteButton,
      '-',
      importButton,
      exportButton
    ];
  }
  else 
    tbar = [genDataReportButton];
  

  infoGridConfig = {
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:1000,
    autoWidth : true,
    //title : _('ID_PM_TABLE') + " : " + tableDef.ADD_TAB_NAME,
    stateful : true,
    stateId : 'grid',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: false,
    viewConfig: {
      forceFit:false
    },
    store: store,
    loadMask: true,
    cm: cmodel,
    sm: smodel,
    tbar: tbar,
    bbar: bbarpaging
  }
  
  if (!isReport) {
    infoGridConfig.plugins = new Array();
    infoGridConfig.plugins.push(editor);
  }
  
  infoGrid = new Ext.grid.GridPanel(infoGridConfig);
  
  if (!isReport) {
    infoGrid.on('rowcontextmenu', 
      function (grid, rowIndex, evt) {
          var sm = grid.getSelectionModel();
          sm.selectRow(rowIndex, sm.isSelected(rowIndex));
      },
      this
    );
      
    infoGrid.on('contextmenu', function(evt){evt.preventDefault();}, this);
    infoGrid.addListener('rowcontextmenu',onMessageContextMenu, this);
  }

  viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: false,
    items: [infoGrid]
  });

  infoGrid.store.load();

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

//Load New PM Table Row Forms
var props = function(){};

NewPMTableRow = function(){
  if (editor.editing) {
    return false;
  }
  var PMRow = infoGrid.getStore().recordType;
  //var meta = mapPMFieldType(records[i].data['FIELD_UID']);
  
  for(i=0; i<_fields.length; i++) {
    props.prototype[_fields[i].name] = '';
  }

  var row = new PMRow(new props);
  len = infoGrid.getStore().data.length;
  
  editor.stopEditing();
  store.insert(len, row);
  infoGrid.getView().refresh();
  infoGrid.getSelectionModel().selectRow(len);
  editor.startEditing(len);
};

//Load PM Table Edition Row Form
EditPMTableRow = function(){ 
  var row = Ext.getCmp('infoGrid').getSelectionModel().getSelected();
  var selIndex = store.indexOfId(row.id);
  editor.stopEditing();
  Ext.getCmp('infoGrid').getView().refresh();
  Ext.getCmp('infoGrid').getSelectionModel().selectRow(selIndex);
  editor.startEditing(selIndex);
};

//Confirm PM Table Row Deletion Tasks
DeletePMTableRow = function(){
  
  PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_REMOVE_FIELD'), function(){
    var records = Ext.getCmp('infoGrid').getSelectionModel().getSelections();
    Ext.each(records, Ext.getCmp('infoGrid').store.remove, Ext.getCmp('infoGrid').store);
  });

};

//Load Import PM Table From CSV Source
//ImportPMTableCSV = function(){
//  location.href = 'additionalTablesDataImportForm?sUID=' + TABLES.UID;
//};

ImportPMTableCSV = function(){
      
  var comboDelimiter = new Ext.data.SimpleStore({
                          fields : ['id', 'value'],
                          data   : [[';', 'SemiColon (;)'],
                                    [',', 'Comma (,)']]
                       });      
  var w = new Ext.Window({
    title       : '',
    width       : 440,
    height      : 180,
    modal       : true,
    autoScroll  : false,
    maximizable : false,
    resizable   : false,
    items: [
      new Ext.FormPanel({
        id         :'uploader',
        fileUpload : true,
        width      : 420,
        frame      : true,
        title      : _('ID_IMPORT_DATA_CSV'),
        autoHeight : false,
        bodyStyle  : 'padding: 10px 10px 0 10px;',
        labelWidth : 80,
        defaults   : {
            anchor     : '90%',
            allowBlank : false,
            msgTarget  : 'side'
        },
        items : [{
            xtype      : 'fileuploadfield',
            id         : 'csv-file',
            emptyText  : _('ID_SELECT_FILE'),
            fieldLabel : _('ID_CSV_FILE'),
            name       : 'form[CSV_FILE]',
            buttonText : '',
            buttonCfg  : {
                iconCls: 'upload-icon'
            }
        }, {
          xtype         : 'combo',
          id            : 'csv-delimiter',
          fieldLabel    : _('ID_DELIMITED_BY'),
          hiddenName    : 'form[CSV_DELIMITER]',
          mode          : 'local',
          store         : comboDelimiter,
          displayField  : 'value',
          valueField    : 'id',
          allowBlank    : false,
          triggerAction : 'all',
          emptyText     : _('ID_SELECT'),
          selectOnFocus : true
          
        },{
          xtype : 'hidden',
          name  : 'form[ADD_TAB_UID]',
          value : tableDef.ADD_TAB_UID
        }],
        buttons : [{
            text     : _('ID_UPLOAD'),
            handler  : function(){
              var filePath = Ext.getCmp('csv-file').getValue();
              var fileType = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
              if(fileType =='csv' ){
                var uploader  = Ext.getCmp('uploader');

                if(uploader.getForm().isValid()){
                  uploader.getForm().submit({
                    url      : '../pmTablesProxy/importCSV',
                    waitMsg  : _('ID_UPLOADING_FILE'),
                    success  : function(o, resp){
                      w.close();
                      infoGrid.store.reload();

                      PMExt.notify('IMPORT RESULT', resp.result.message);
                    },
                    failure: function(o, resp){
                      w.close();
                      Ext.MessageBox.show({title: '', 
                                            msg     : resp.result.message,
                                            buttons : Ext.MessageBox.OK, 
                                            animEl  : 'mb9', 
                                            fn      : function(){}, 
                                            icon    : Ext.MessageBox.ERROR
                                          });
                    }
                  });
                }
              } else {
                Ext.MessageBox.show({ title   : '', 
                                      msg     : _('ID_INVALID_EXTENSION') + ' ' + fileType,
                                      buttons : Ext.MessageBox.OK,
                                      animEl  : 'mb9', 
                                      fn      : function(){},
                                      icon    : Ext.MessageBox.ERROR
                                    });
              }
            }
        },{
          text    : TRANSLATIONS.ID_CANCEL,
          handler : function(){
            w.close();
          }
        }]
      })
    ]

  });
  w.show();
}

ExportPMTableCSV = function(){

  var comboDelimiter = new Ext.data.SimpleStore({
                          fields : ['id', 'value'],
                          data   : [[';', 'SemiColon (;)'],
                                    [',', 'Comma (,)']]
                       });
  var w = new Ext.Window({
    title       : '',
    width       : 320,
    height      : 140,
    modal       : true,
    autoScroll  : false,
    maximizable : false,
    resizable   : false,
    items: [
      new Ext.FormPanel({
        id         :'uploader',
        fileUpload : true,
        width      : 300,
        frame      : true,
        title      : _('ID_EXPORT_DATA_CSV'),
        autoHeight : false,
        bodyStyle  : 'padding: 10px 10px 0 10px;',
        labelWidth : 80,
        defaults   : {
            anchor     : '90%',
            allowBlank : false,
            msgTarget  : 'side'
        },
        items : [{
          xtype         : 'combo',
          id            : 'csv_delimiter',
          fieldLabel    : _('ID_DELIMITED_BY'),
          hiddenName    : 'form[CSV_DELIMITER]',
          mode          : 'local',
          store         : comboDelimiter,
          displayField  : 'value',
          valueField    : 'id',
          allowBlank    : false,
          triggerAction : 'all',
          emptyText     : _('ID_SELECT'),
          selectOnFocus : true

        },{
          xtype : 'hidden',
          name  : 'form[ADD_TAB_UID]',
          value : tableDef.ADD_TAB_UID
        } ],
        buttons : [{
            text     : _('ID_EXPORT'),
            handler  : function(){
              var csvDelimiter = Ext.getCmp('csv_delimiter').getValue();
              if(csvDelimiter != '') {
                Ext.Msg.show({
                  title      : '',
                  msg        : _('ID_PROCESSING'),
                  wait       : true,
                  waitConfig : {interval:500}
                });

                Ext.Ajax.request({
                  url: '../pmTablesProxy/exportCSV',
                  params: {
                    ADD_TAB_UID   : tableDef.ADD_TAB_UID,
                    CSV_DELIMITER : Ext.getCmp('csv_delimiter').getValue()
                  },
                  success: function(resp){

                    Ext.Msg.hide();
                    w.close();

                    result = Ext.util.JSON.decode(resp.responseText);

                    if (result.success) {
                      location.href = result.link;
                    } else {
                      PMExt.error(_('ID_ERROR', result.message));
                    }

                  },
                  failure: function(obj, resp){
                    Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
                  }
                });
              } else {
                Ext.MessageBox.show({ title   : '',
                                      msg     : _('ID_INVALID_DELIMITER'),
                                      buttons : Ext.MessageBox.OK,
                                      animEl  : 'mb9',
                                      fn      : function(){},
                                      icon    : Ext.MessageBox.ERROR
                                    });
              }
            }
        },{
          text    : TRANSLATIONS.ID_CANCEL,
          handler : function(){
            w.close();
          }
        }]
      })
    ]
    
  });
  w.show();
}


//Load PM Table List
BackPMList = function(){
  //location.href = 'additionalTablesList';
  history.back();
};

//Gets UIDs from a array of rows
RetrieveRowsID = function(rows){
  var arrAux = new Array();
  var arrPKF = new Array();
  arrPKF = TABLES.PKF.split(',');
  if(rows.length>0){
    var c = 0;
    for(var i=0; i<arrPKF.length; i++){
      arrAux[i] = rows[c].get(arrPKF[i]);
    }
  }
  return arrAux.join(',');
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
  url: 'additionalTablesAjax',
  params: {action:'updatePageSizeData', size: pageSize}
  });
};

genDataReport = function()
{
  Ext.Ajax.request({
    url: '../pmTablesProxy/genDataReport',
    params: {id: tableDef.ADD_TAB_UID},
    success: function(resp){
      response = Ext.util.JSON.decode(resp.responseText);
      PMExt.notify(_('ID_UPDATE'), response.message)
      Ext.getCmp('infoGrid').store.reload();
    },
    failure: function(obj, resp){
      PMExt.error( _('ID_ERROR'), resp.result.message);
    }
  });
}