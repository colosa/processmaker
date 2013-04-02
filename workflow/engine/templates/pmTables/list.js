var newButton;
var editButton;
var deleteButton;
var importButton;
var exportButton;
var dataButton;

var store;
var expander;
var cmodel;
var chkSelModel;
var infoGrid;
var viewport;

var rowsSelected;
var importOption;
var externalOption;
var externalPermissions;
var currentSelectedRow = -1;

Ext.onReady(function(){
    ///Keyboard Events
    new Ext.KeyMap(document, {
      key: Ext.EventObject.F5,
        fn: function(keycode, e) {
          if (! e.ctrlKey) {
            if (Ext.isIE) {
              e.browserEvent.keyCode = 8;
            }
            e.stopEvent();
            document.location = document.location;
          }
          else{
            Ext.Msg.alert( _('ID_REFRESH_LABEL') , _('ID_REFRESH_MESSAGE') );
          }
        }
    });

    Ext.QuickTips.init();

    pageSize = parseInt(CONFIG.pageSize);

    var newMenuOptions = new Array();
    if (PRO_UID == false) {
      newMenuOptions.push({
        text: _('ID_NEW_PMTABLE'),
        handler: newPMTable
      });
    }

    newMenuOptions.push({
      text: _('ID_NEW_REPORT_TABLE'),
      handler: NewReportTable
    });

    if (PRO_UID !== false) {
      newMenuOptions.push({
        text: _('ID_NEW_REPORT_TABLE_OLD'),
        handler: NewReportTableOld
      });
    }

    newButton = new Ext.Action({
      text: _('ID_NEW'),
      icon: '/images/add-table.png',
      menu: newMenuOptions
    });

    editButton = new Ext.Action({
      text: _('ID_EDIT'),
      icon: '/images/edit-table.png',
      handler: EditPMTable,
      disabled: true
    });

    deleteButton = new Ext.Action({
      text: _('ID_DELETE'),
      icon: '/images/delete-table.png',
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
      icon: '/images/database-start.png',
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
    contextMenuItems.push('-');
    contextMenuItems.push(dataButton);
    contextMenuItems.push(exportButton);

    if (_PLUGIN_SIMPLEREPORTS !== false) {
        externalOption = new Ext.Action({
            text:'',
            iconCls: 'x-btn-text button_menu_ext ss_sprite ss_report_picture',
            handler: function() {
                updateTag('plugin@simplereport');
            },
            disabled: false
        });
        externalPermissions = new Ext.Action({
            text: _('ID_PERMISSIONS'),
            iconCls: 'x-btn-text button_menu_ext ss_sprite  ss_key_add',
            handler: function() {
                updateTagPermissions('plugin@simplereport');
            },
            disabled: false
        });
        contextMenuItems.push('-');
        contextMenuItems.push(externalOption);
        contextMenuItems.push(externalPermissions);
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

    store = new Ext.data.GroupingStore( {
      autoLoad: false,
      proxy : new Ext.data.HttpProxy({
        url: 'pmTablesProxy/getList' + (PRO_UID? '?pro_uid='+PRO_UID: '')
      }),
      reader : new Ext.data.JsonReader( {
        root: 'rows',
        totalProperty: 'count',
        fields : [
          {name : 'ADD_TAB_UID'},
          {name : 'ADD_TAB_NAME'},
          {name : 'ADD_TAB_DESCRIPTION'},
          {name : 'PRO_TITLE'},
          {name : 'TYPE'},
          {name : 'ADD_TAB_TYPE'},
          {name : 'ADD_TAB_TAG'},
          {name : 'PRO_UID'},
          {name : "DBS_UID"},
          {name : 'NUM_ROWS'}
        ]
      }),
      listeners: {
        load: function(a,b){
          if (currentSelectedRow != -1) {
            Ext.getCmp('infoGrid').getSelectionModel().selectRow(currentSelectedRow);
            Ext.getCmp('infoGrid').fireEvent('rowclick', Ext.getCmp('infoGrid'), currentSelectedRow)
          }
        }
      }
    });

    chkSelModel = new Ext.grid.CheckboxSelectionModel({
      listeners:{
        selectionchange: function(sm){
          if (sm.last !== false) {
            var count_rows = sm.getCount();
            //var isReport = sm.getSelected().get('PRO_UID') != '';

            currentSelectedRow = sm.last;
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
      }
    });

    cmodelColumns = new Array();
    cmodelColumns.push(chkSelModel);
    cmodelColumns.push({id:'ADD_TAB_UID', dataIndex: 'ADD_TAB_UID', hidden:true, hideable:false});
    cmodelColumns.push({dataIndex: 'ADD_TAB_TAG', hidden:true, hideable:false});
    cmodelColumns.push({header: _('ID_NAME'), dataIndex: 'ADD_TAB_NAME', width: 300, align:'left', renderer: function(v,p,r){
      return r.get('TYPE') == 'CLASSIC'? v + '&nbsp<span style="font-size:9px; color:green">('+ _('ID_OLD_VERSION') +')</font>' : v;
    }});
    cmodelColumns.push({header: _('ID_DESCRIPTION'), dataIndex: 'ADD_TAB_DESCRIPTION', sortable:false, width: 400, hidden:false, align:'left', renderer: function(v,p,r){
      if (r.get('ADD_TAB_TAG')) {
        tag = r.get('ADD_TAB_TAG').replace('plugin@', '');
        tag = tag.charAt(0).toUpperCase() + tag.slice(1);
        switch(tag.toLowerCase()){
          case 'simplereport':
            tag = _('ID_SIMPLE_REPORT');
            break;
        }
      }
      return r.get('ADD_TAB_TAG') ? '<span style="font-size:9px; color:green">'+tag+':</span> '+ v : v;
    }});

    cmodelColumns.push({header: _('ID_TABLE_TYPE'), dataIndex: 'PRO_UID', width: 120, align:'left', renderer: function(v,p,r){
      color = r.get('PRO_UID') ? 'blue' : 'green';
      value = r.get('PRO_UID') ? _('ID_REPORT_TABLE') : _('ID_PMTABLE');
      return '<span style="color:'+color+'">'+value+'</span> ';
    }});

    cmodelColumns.push({dataIndex: "DBS_UID", hidden: true, hideable: false});

    cmodelColumns.push({header: _('ID_RECORDS'), dataIndex: 'NUM_ROWS', width: 90, align:'left'});

    if (PRO_UID === false) {
      cmodelColumns.push({header: _('ID_PROCESS'), dataIndex: 'PRO_TITLE', width: 180, align:'left'});
    }

    cmodelColumns.push({header: _('ID_TYPE'), dataIndex: 'ADD_TAB_TYPE', width: 400, hidden:true, align:'left'});

    cmodel = new Ext.grid.ColumnModel({
      defaults: {
        width: 50,
        sortable: true
      },
      columns: cmodelColumns
    });

    bbarpaging = new Ext.PagingToolbar({
        pageSize: pageSize,
        store: store,
        displayInfo: true,
        displayMsg: (PRO_UID? _('ID_GRID_PAGE_DISPLAYING_REPORTABLES_MESSAGE') : _('ID_GRID_PAGE_DISPLAYING_PMTABLES_MESSAGE')) + '&nbsp; &nbsp; ',
        emptyMsg: _('ID_GRID_PAGE_NO_PMTABLES_MESSAGE'),
        items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
    });

    infoGrid = new Ext.grid.GridPanel({
      region: 'center',
      layout: 'fit',
      id: 'infoGrid',
      height:100,
      autoWidth : true,
      title : (PRO_UID? _('ID_REPORT_TABLES') : _('ID_PMTABLE')),
      stateful : true,
      stateId : 'grid',
      enableColumnResize: true,
      enableHdMenu: true,
      frame:false,
      columnLines: false,
      viewConfig: {
        forceFit:true
      },
      store: store,
      loadMask: true,
      cm: cmodel,
      sm: chkSelModel,
      tbar: [
        newButton,
        editButton,
        deleteButton,'-',
        dataButton,'-' ,
        importButton,
        exportButton,
        '->',
        searchText,
        clearTextButton,
        searchButton],
      bbar: bbarpaging,
      listeners: {
        rowdblclick: EditPMTable,
        render: function(){
          this.loadMask = new Ext.LoadMask(this.body, {msg: _('ID_LOADING_GRID')});
        }
      },
      view: new Ext.grid.GroupingView({
        forceFit:true,
        groupTextTpl: '{text}'
      })
    });

    infoGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
        var sm = grid.getSelectionModel();
        sm.selectRow(rowIndex, sm.isSelected(rowIndex));

        var rowsSelected = Ext.getCmp('infoGrid').getSelectionModel().getSelections();

        if (externalOption) {
            tag = rowsSelected[0].get('ADD_TAB_TAG');
            if (tag) {
                text = _('ID_CONVERT_NATIVE_REP_TABLE');
                externalPermissions.setDisabled(false);
            } else {
                text = _('ID_CONVERT_SIMPLE_REPORT');
                externalPermissions.setDisabled(true);
            }
            externalOption.setText(text);
            if (rowsSelected[0].get('PRO_UID')) {
                externalOption.setDisabled(false);
            } else {
                externalOption.setDisabled(true);
            }
            externalOption.setHidden((rowsSelected[0].get("TYPE") != "CLASSIC" && rowsSelected[0].get("DBS_UID") == "workflow")? false : true);
        }
    },this);

    infoGrid.on('contextmenu', function(evt){evt.preventDefault();}, this);
    infoGrid.addListener('rowcontextmenu',onMessageContextMenu, this);

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

//Load New PM Table Forms
NewReportTable = function(){
  if(PRO_UID !== false)
    location.href = 'pmTables/edit?PRO_UID='+PRO_UID+'&tableType=report';
  else
    location.href = 'pmTables/edit?tableType=report';
};

NewReportTableOld = function(){
  //location.href = 'reportTables/edit?PRO_UID='+PRO_UID+'&tableType=report';
  //parent.reportTables2();
  //parent.Pm.data.render.buildingBlocks.injector('reportTables2');
  location.href = 'reportTables/reportTables_Edit?PRO_UID='+PRO_UID;
};

newPMTable = function(){
  location.href = 'pmTables/edit?tableType=table';
};

EditPMTable = function(){
    var row   = Ext.getCmp('infoGrid').getSelectionModel().getSelected();

    if (row.data.TYPE != 'CLASSIC') {
      tableType = row.data.PRO_UID ? 'report' : 'table';
      proParam = PRO_UID !== false ? '&PRO_UID='+PRO_UID : '';
      location.href = 'pmTables/edit?id='+row.data.ADD_TAB_UID+'&tableType=' + tableType + proParam;
    }
    else { //edit old report table
      location.href = 'reportTables/reportTables_Edit?REP_TAB_UID='+row.data.ADD_TAB_UID
    }
};

//Confirm PM Table Deletion Tasks
DeletePMTable = function() {
  var rows = Ext.getCmp('infoGrid').getSelectionModel().getSelections();
  var selections = new Array();

  for(var i=0; i<rows.length; i++) {
    selections[i] = {id: rows[i].get('ADD_TAB_UID'), type: rows[i].get('TYPE')};
  }

  Ext.Msg.confirm( _('ID_CONFIRM'), _('ID_CONFIRM_DELETE_PM_TABLE'),
    function(btn, text) {
      if (btn == "yes") {
        Ext.Msg.show({
          title : '',
          msg : _('ID_REMOVING_SELECTED_TABLES'),
          wait:true,
          waitConfig: {interval:500}
        });

        Ext.Ajax.request ({
          url: 'pmTablesProxy/delete',
          params: {
            rows: Ext.util.JSON.encode(selections)
          },
          success: function(resp){
            Ext.MessageBox.hide();
            result = Ext.util.JSON.decode(resp.responseText);
            Ext.getCmp('infoGrid').getStore().reload();

            if (result.success) {
                currentSelectedRow = -1;
                PMExt.notify(_("ID_DELETION_SUCCESSFULLY"), _("ID_ALL_RECORDS_DELETED_SUCESSFULLY"));
            } else {
                PMExt.error(_("ID_ERROR"), result.message.nl2br());
            }
          },
          failure: function(obj, resp){
            Ext.MessageBox.hide();
            Ext.getCmp('infoGrid').getStore().reload();
            Ext.Msg.alert( _('ID_ERROR'), resp.result.message);
          }
        });
      }
    }
  );
};

//Load Import PM Table Form
ImportPMTable = function(){

  var w = new Ext.Window({
    title: '',
    width: 420,
    height: 160,
    modal: true,
    autoScroll: false,
    maximizable: false,
    resizable: false,
    items: [
      new Ext.FormPanel({
        /*renderTo: 'form-panel',*/
        id:'uploader',
        fileUpload: true,
        width: 400,
        frame: true,
        title: (PRO_UID? _('ID_IMPORT_RT') : _('ID_IMPORT_PMT')),
        autoHeight: false,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 50,
        defaults: {
            anchor: '90%',
            allowBlank: false,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fileuploadfield',
            id: 'form-file',
            emptyText: _('ID_SELECT_PM_FILE'),
            fieldLabel: _('ID_FILE'),
            name: 'form[FILENAME]',
            buttonText: '',
            buttonCfg: {
                iconCls: 'upload-icon'
            }
        }, {
          xtype: 'checkbox',
          fieldLabel: '',
          boxLabel: _('ID_OVERWRITE_EXIST'), // 'Overwrite if exists?',
          name: 'form[OVERWRITE]'
        }],
        buttons: [{
            text: _('ID_UPLOAD'),
            handler: function(){
              var uploader = Ext.getCmp('uploader');

              if(uploader.getForm().isValid()){
                uploader.getForm().submit({
                  url: 'pmTablesProxy/import',
                  waitMsg: _('ID_UPLOADING_FILE'),
                  success: function(o, resp){
                    var result = Ext.util.JSON.decode(resp.response.responseText);

                    if (result.success) {
                      PMExt.notify(_('ID_IMPORT_RESULT'), result.message);
                    }
                    else {
                      win = new Ext.Window({
                        applyTo:'hello-win',
                        layout:'fit',
                        width:500,
                        height:300,
                        closeAction:'hide',
                        plain: true,
                        html: '<h3>' + _('ID_IMPORTING_ERROR') + '</h3>' + result.message,
                        items: [],

                        buttons: [{
                          text: 'Close',
                          handler: function(){
                              win.hide();
                          }
                        }]
                      });
                      win.show(this);
                    }

                    w.close();
                    infoGrid.store.reload();
                  },
                  failure: function(o, resp){
                    w.close();
                    infoGrid.store.reload();

                    var result = Ext.util.JSON.decode(resp.response.responseText);
                    if (result.errorType == 'warning') {
                      PMExt.warning(_('ID_WARNING'), result.message.replace(/\n/g,' <br>'));
                    }
                    else {
                      PMExt.error(_('ID_ERROR'), result.message.replace(/\n/g,' <br>'));
                    }
                  }
                });
              }
            }
        }/*,{
            text: 'Reset',
            handler: function(){
              uploader = Ext.getCmp('uploader');
              uploader.getForm().reset();
            }
        }*/,{
            text: TRANSLATIONS.ID_CANCEL,
            handler: function(){
              w.close();
            }
        }]
      })
    ]
  });
  w.show();
}

//Load Export PM Tables Form
ExportPMTable = function(){
  var rows = Ext.getCmp('infoGrid').getSelectionModel().getSelections();
  var toExportRows = new Array();

  for(var i=0; i<rows.length; i++){
    toExportRows.push([
      rows[i].get('ADD_TAB_UID'),
      rows[i].get('PRO_UID'),
      rows[i].get('ADD_TAB_NAME'),
      (rows[i].get('PRO_UID') ? _('ID_REPORT_TABLE'): _('ID_PMTABLE')),
      true,
      (rows[i].get('PRO_UID') ? false : true)
    ]);
  }

  Export.targetGrid.store.loadData(toExportRows);
  Export.window.show();
};

//Load PM TAble Data
PMTableData = function()
{
  var row = Ext.getCmp('infoGrid').getSelectionModel().getSelected();
  var type = row.get('PRO_UID');

  //location.href = 'pmTables/data?id='+row.get('ADD_TAB_UID');
  if (row.get('TYPE') != '') {
    PMExt.info(_('ID_INFO'), _('ID_DATA_LIST_NOT_AVAILABLE_FOR_OLDVER'));
    return;
  }

  win = new Ext.Window({
    layout: 'fit',
    width: 700,
    height: 400,
    title: ((type != '')? _('ID_REPORT_TABLE') : _('ID_PMTABLE')) +': '+ row.get('ADD_TAB_NAME'),
    modal: true,
    maximizable: true,
    constrain: true,
    //closeAction:'hide',
    plain: true,
    items: [{
      xtype:"iframepanel",
      defaultSrc : 'pmTables/data?id='+row.get('ADD_TAB_UID')+'&type='+row.get('TYPE'),
      loadMask:{msg: _('ID_LOADING')}
    }],
    listeners: {
      close: function() {
        store.reload();
      }
    }
  });

  win.show();
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

//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  infoGrid.store.load();
};

function updateTag(value)
{
  var rowsSelected = Ext.getCmp('infoGrid').getSelectionModel().getSelections();

  Ext.Ajax.request({
    url: 'pmTablesProxy/updateTag',
    params: {
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

function updateTagPermissions(){
    var rowsSelected = Ext.getCmp('infoGrid').getSelectionModel().getSelections();
    if (rowsSelected){
        location.href = 'pmReports/reportsAjax?action=permissionList&ADD_TAB_NAME='+ rowsSelected[0].get('ADD_TAB_NAME') +'&ADD_TAB_UID='+ rowsSelected[0].get('ADD_TAB_UID')+'&pro_uid='+PRO_UID;
    }
};

 function PopupCenter(pageURL, title,w,h) {
    var left = (Ext.getBody().getViewSize().width/3);
    var top = (Ext.getBody().getViewSize().height/3);
    var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  }
