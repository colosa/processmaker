/**
 * Report tables Edit
 * @author Erik A. O. <erik@colosa.com>
 */

var win;

var store;
var storeP;
var storeA;
var cmodelP;
var smodelA;
var smodelP;
var availableGrid = {};
var assignedGridGlobal=new Array();
var MembersPanel;
var viewport;
var assignButton;
var assignAllButton;
var removeButton;
var removeAllButton;
var backButton;
var store;
var REP_TAB_TITLE;
var REP_TAB_NAME;
var REP_TAB_TYPE;
var REP_TAB_CONNECTION;
var reportGrid;
var reportTablesGlobal = {};
reportTablesGlobal.REP_TAB_UID = "";
reportTablesGlobal.REP_TAB_UID_EDIT = "";
reportTablesGlobal.REP_TAB_TITTLE = "";
var oo;

Ext.onReady(function(){

  var fm = Ext.form;
  var fieldsCount = 0;
  // store for available fields grid
  storeA = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'reportTables_Ajax'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'processFields',
      fields : [{name : 'FIELD_UID'}, {name : 'FIELD_NAME'}]
    }),
    listeners: {
      load: function() {

      }
    }
  });
  //column model for available fields grid
  cmodelA = new Ext.grid.ColumnModel({
    defaults: {
      width: 55,
      sortable: true
    },
    columns: [
      {
        id:'FIELD_UID',
        dataIndex: 'FIELD_UID',
        hidden:true,
        hideable:false
      }, {
        header : _("ID_DYNAFORM_FIELDS"),
        dataIndex : 'FIELD_NAME',
        sortable : true,
        align:'left'
      }
    ]
  });
  //selection model for available fields grid
  smodelA = new Ext.grid.RowSelectionModel({
    selectSingle: false,
    listeners:{
      selectionchange: function(sm){
        switch(sm.getCount()){
          case 0: Ext.getCmp('assignButton').disable(); break;
          default: Ext.getCmp('assignButton').enable(); break;
          }
        }
    }
  });
  //grid for table columns grid
  availableGrid = new Ext.grid.GridPanel({
    layout: 'fit',
    region: 'center',
    id: 'availableGrid',
    ddGroup         : 'assignedGridDDGroup',
    enableDragDrop  : true,
    stripeRows      : true,
    autoWidth       : true,
    stripeRows      : true,
    height          : 100,
    width           : 200,
    stateful        : true,
    stateId         : 'grid',
    enableColumnResize : true,
    enableHdMenu  : true,
    frame      : false,
    columnLines    : false,
    viewConfig    : {forceFit:true},
    cm: cmodelA,
    sm: smodelA,
    store: storeA,
    listeners: {rowdblclick: AssignFieldsAction}
  });

  //selecion model for table columns grid
  sm = new Ext.grid.RowSelectionModel({
    selectSingle: false,
    listeners:{
      selectionchange: function(sm){
          switch(sm.getCount()){
            case 0: Ext.getCmp('removeButton').disable(); break;
            default: Ext.getCmp('removeButton').enable(); break;
          }
        }
    }
  });
  //check column for table columns grid
  var checkColumn = new Ext.grid.CheckColumn({
    header: 'Filter',
    dataIndex: 'FIELD_FILTER',
    id: 'FIELD_FILTER',
    width: 55
  });
  //columns for table columns grid
  var cmColumns = [
      {
        id: 'uid',
        dataIndex: 'uid',
        hidden: true
      },
      {
          id: 'field_uid',
          dataIndex: 'field_uid',
          hidden: true
      },
      {
          id: 'field_key',
          dataIndex: 'field_key',
          hidden: true
      },
      {
          id: 'field_null',
          dataIndex: 'field_null',
          hidden: true
      },
      {
          id: 'field_dyn',
          header: _("ID_DYNAFORM_FIELD"),
          dataIndex: 'field_dyn',
          width: 220,
          // use shorthand alias defined above
          editor: {
            xtype: 'displayfield',
            readOnly: true,
            style: 'font-size:11px; font-weight:bold; padding-left:4px'
          }
      }, {
          id: 'field_name',
          header: _("ID_FIELD_NAME"),
          dataIndex: 'field_name',
          width: 220,
          editor: {
            xtype: 'textfield',
            allowBlank: true,
            style:'text-transform: uppercase',
            listeners:{
              specialkey: function(f,e){
                if(e.getKey()==e.ENTER){
                  this.setValue(this.getValue().toUpperCase())
                }
              }
            }
          }
      }, {
          id: 'field_label',
          header: _("ID_FIELD_LABEL"),
          dataIndex: 'field_label',
          width: 220,
          editor:{
            xtype: 'textfield',
            allowBlank: true
          }
      }, {
          id: 'field_type',
          header: _("ID_TYPE"),
          dataIndex: 'field_type',
          width: 130,
          editor: new fm.ComboBox({
              typeAhead: true,
              triggerAction: 'all',
              editable:false,
              lazyRender: true,
              mode: 'local',
              displayField:'type',
              valueField:'type_id',
              store: new Ext.data.SimpleStore({
                  fields: ['type_id', 'type'],
                  data : [['VARCHAR',_("ID_VARCHAR")],['TEXT',_("ID_TEXT")],['DATE',_("ID_DATE")],['INT',_("ID_INT")],['FLOAT',_("ID_FLOAT")]],
                  sortInfo: {field:'type_id', direction:'ASC'}
              })
          })
      }, {
          id: 'field_size',
          header: _("ID_SIZE"),
          dataIndex: 'field_size',
          width: 70,
          align: 'right',
          editor: new fm.NumberField({
            allowBlank: true
          })
      }
  ];

  //if permissions plugin is enabled
  if (TABLE !== false && TABLE.ADD_TAB_TAG == 'plugin@simplereport') {
    cmColumns.push({
        xtype: 'booleancolumn',
        header: _('ID_FILTER'),
        dataIndex: 'field_filter',
        align: 'center',
        width: 50,
        trueText: _('ID_YES'),
        falseText: _('ID_NO'),
        editor: {
            xtype: 'checkbox'
        }
    })
  }

  //column model for table columns grid
  var cm = new Ext.grid.ColumnModel({
    // specify any defaults for each column
    defaults: {
        sortable: true // columns are not sortable by default
    },
    columns:cmColumns
  });
  //store for table columns grid
  store = new Ext.data.ArrayStore({
      fields: [
          {name: 'uid', type: 'string'},
          {name: 'field_uid', type: 'string'},
          {name: 'field_key', type: 'string'},
          {name: 'field_name', type: 'string'},
          {name: 'field_label', type: 'string'},
          {name: 'field_type'},
          {name: 'field_size', type: 'float'},
          {name: 'field_null', type: 'float'},
          {name: 'field_filter', type: 'string'}
      ]
  });
  //row editor for table columns grid
  var editor = new Ext.ux.grid.RowEditor({
      saveText: _("ID_UPDATE")
  });

  editor.on({
    afteredit: function(roweditor, changes, record, rowIndex) {
      //
    },
    beforeedit: function(roweditor, rowIndex) {
      row = assignedGrid.getSelectionModel().getSelected();
      if (row.get('field_name') == 'APP_UID' || row.get('field_name') == 'APP_NUMBER' || row.get('field_name') == 'ROW') {
        return false;
      }
    }
  });

  //table columns grid
  assignedGrid = new Ext.grid.GridPanel({
    //title: 'Columns',
    region: 'center',
    id: 'assignedGrid',
    ddGroup         : 'availableGridDDGroup',
    enableDragDrop  : true,
    enableColumnResize : true,
    viewConfig    : {forceFit:true},
    cm: cm,
    sm: sm,
    store: store,
    plugins: [editor, checkColumn],
    tbar: [
      {
        text: _("ID_ADD_CUSTOM_COLUMN"),
        handler: function() {
          var PMRow = assignedGrid.getStore().recordType;
          //var meta = mapPMFieldType(records[i].data['FIELD_UID']);
          var row = new PMRow({
            uid  : '',
            field_uid  : '',
            field_dyn  : '',
            field_name  : '',
            field_label : '',
            field_type  : '',
            field_size  : '',
            field_key   : 0,
            field_null  : 1
          });

          //store.add(row);
          editor.stopEditing();
          store.insert(0, row);
          assignedGrid.getView().refresh();
          assignedGrid.getSelectionModel().selectRow(0);
          editor.startEditing(0);
        }
      }
    ]
  });

  assignedGrid.getSelectionModel().on('selectionchange', function(sm){
      //alert('s');
  });

  // (vertical) selection buttons
  buttonsPanel = new Ext.Panel({
    width      : 40,
    layout       : {
      type:'vbox',
      padding:'0',
      pack:'center',
      align:'center'
    },
    defaults:{margins:'0 0 35 0'},
    items:[
      { xtype:'button',text: '>',
        handler: AssignFieldsAction,
        id: 'assignButton', disabled: true
      },
      { xtype:'button',text: '&lt;',
        handler: RemoveFieldsAction,
        id: 'removeButton', disabled: true
      },
      { xtype:'button',text: '>>',
        handler: AssignAllFieldsAction,
        id: 'assignButtonAll', disabled: false},
      { xtype:'button',text: '&lt;&lt;',
        handler: RemoveAllFieldsAction,
        id: 'removeButtonAll', disabled: false
      }
    ]

  });


  FieldsPanel = new Ext.Panel({
    //title: _('ID_FIELDS'),
    region     : 'center',
    //autoWidth   : true,
    width: 150,
    layout       : 'hbox',
    defaults     : { flex : 1 }, //auto stretch
    layoutConfig : { align : 'stretch' },
    items        : [availableGrid,buttonsPanel,assignedGrid],
    viewConfig   : {forceFit:true}

  });

  searchTextA = new Ext.form.TextField ({
        id: 'searchTextA',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
              DoSearchA();
            }
          }
        }
    });

  searchTextP = new Ext.form.TextField ({
        id: 'searchTextP',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
              DoSearchP();
            }
          }
        }
    });

  var types = new Ext.data.SimpleStore({
    fields: ['REP_TAB_TYPE', 'type'],
    data : [['NORMAL',_("ID_GLOBAL")],['GRID',_("ID_GRID")]]
  });

  comboReport = new Ext.form.ComboBox({
    id : 'REP_TAB_TYPE',
    name: 'type',
    fieldLabel: 'Type',
    hiddenName : 'REP_TAB_TYPE',
    mode: 'local',
    store: types,
    displayField:'type',
    valueField:'REP_TAB_TYPE',
    width: 120,
    typeAhead: true,
    triggerAction: 'all',
    editable:false,
    lazyRender: true,
    value: typeof TABLE.ADD_TAB_TYPE != 'undefined'? TABLE.ADD_TAB_TYPE : 'NORMAL',
    listeners: {
      select: function(combo,record,index){
        if  (this.getValue()=='NORMAL') {
          Ext.getCmp('REP_TAB_GRID').setVisible(false);
          loadFieldNormal();
        } else {
          Ext.getCmp('availableGrid').store.removeAll();
          Ext.getCmp('REP_TAB_GRID').setVisible(true);
          Ext.getCmp('REP_TAB_GRID').setValue('');
          gridsListStore.reload({params:{PRO_UID : PRO_UID !== false ? PRO_UID : Ext.getCmp('PROCESS').getValue()}});
        }
      }
    }
  });

  dbConnectionsStore = new Ext.data.Store({
    //autoLoad: true,
    proxy : new Ext.data.HttpProxy({
      url: 'reportTables_Ajax',
      method : 'POST'
    }),
    baseParams : {
      action: 'getDbConnectionsList',
      PRO_UID : ''
    },
    reader : new Ext.data.JsonReader( {
      fields : [{name : 'DBS_UID'}, {name : 'DBS_NAME'}]
    }),
    listeners: {
      load: function() {
        if (TABLE !== false) { // is editing
          // set current editing process combobox
          var i = this.findExact('DBS_UID', TABLE.DBS_UID, 0);
          if (i > -1){
            comboDbConnections.setValue(this.getAt(i).data.DBS_UID);
            comboDbConnections.setRawValue(this.getAt(i).data.DBS_NAME);
            comboDbConnections.setDisabled(true);
          } else {
            // DB COnnection deleted
            Ext.Msg.alert( _('ID_ERROR'), _('ID_DB_CONNECTION_NO_EXIST'));
          }
        } else {
          comboDbConnections.setValue('rp');
        }
      }
    }
  });

  comboDbConnections = new Ext.form.ComboBox({
    id: 'REP_TAB_CONNECTION',
    fieldLabel : _("ID_DB_CONNECTION"),
    hiddenName : 'DBS_UID',
    store : dbConnectionsStore,
    //value: 'rp',
    valueField : 'DBS_UID',
    displayField : 'DBS_NAME',
    triggerAction : 'all',
    editable : false,
    mode:'local'
  });

  gridsListStore = new Ext.data.Store({
    //autoLoad: true,
    proxy : new Ext.data.HttpProxy({
      url: 'reportTables_Ajax',
      method : 'POST'
    }),
    baseParams : {
      action: 'availableFieldsReportTables',
      PRO_UID : '',
      TYPE: 'GRID'
    },
    reader : new Ext.data.JsonReader( {
      root : 'processFields',
      fields : [{name : 'FIELD_UID'}, {name : 'FIELD_NAME'}]
    }),
    listeners: {
      load: function(){
        if (TABLE !== false) {
          var i = this.findExact('FIELD_UID', TABLE.ADD_TAB_GRID, 0);
          if (i > -1){
            comboGridsList.setValue(this.getAt(i).data.FIELD_UID);
            comboGridsList.setRawValue(this.getAt(i).data.FIELD_NAME);
            comboGridsList.setDisabled(true);
          } else {
            Ext.Msg.alert( _('ID_ERROR'), _('ID_GRID_NO_EXIST'));
          }
        }
      }
    }
  });

  comboGridsList = new Ext.form.ComboBox({
    id: 'REP_TAB_GRID',
    fieldLabel : 'Grid',
    hiddenName : 'FIELD_UID',
    store : gridsListStore,
    emptyText: _("ID_SELECT_GRID"),
    //hidden:true,
    //hideLabel: true,
    //value: 'rp',
    valueField : 'FIELD_UID',
    displayField : 'FIELD_NAME',
    triggerAction : 'all',
    width: 120,
    editable : false,
    mode:'local',
    listeners:{
      afterrender: function(){
        //Ext.getCmp('REP_TAB_GRID').setVisible(false);
        //loadFieldNormal();
      },
      select: function(combo,record,index){

          //Ext.getCmp('REP_TAB_TYPE').setVisible(true);
         // Ext.getCmp('REP_TAB_GRID').setVisible(true);
         loadFieldsGrids();

      }
    }
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
    listeners: {
      load: function() {
        if (TABLE !== false) { // is editing
          // set current editing process combobox
          var i = this.findExact('PRO_UID', TABLE.PRO_UID, 0);
          if (i > -1){
            processComboBox.setValue(this.getAt(i).data.PRO_UID);
            processComboBox.setRawValue(this.getAt(i).data.PRO_TITLE);
            processComboBox.setDisabled(true);
          } else {
            // Process deleted
            Ext.Msg.alert( _('ID_ERROR'), _('ID_PROCESS_NO_EXIST'));
          }
          // setting table attributes for current editing process
          Ext.getCmp('REP_TAB_NAME').setValue(TABLE.ADD_TAB_NAME);
          Ext.getCmp('REP_TAB_NAME').setDisabled(true);
          Ext.getCmp('REP_TAB_DSC').setValue(TABLE.ADD_TAB_DESCRIPTION);

          // grid
          comboReport.setDisabled(true);
          if (TABLE.ADD_TAB_TYPE == 'GRID') {
            Ext.getCmp('REP_TAB_GRID').setVisible(true);
            gridsListStore.reload({params:{PRO_UID : Ext.getCmp('PROCESS').getValue()}});
          }
          // db connections
          comboDbConnections.getStore().reload({params:{PRO_UID : Ext.getCmp('PROCESS').getValue()}});

          // loading available fields
          //if (TABLE.ADD_TAB_TYPE == 'NORMAL') {
            loadAvFieldsFromArray(avFieldsList);
          //}

          // loading table fields
          loadTableRowsFromArray(TABLE.FIELDS);
        }
      }
    }
  });

  processComboBox = new Ext.form.ComboBox({
    id: 'PROCESS',
    fieldLabel : _("ID_CASESLIST_APP_PRO_TITLE"),
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
    forceSelection: true,

    listeners:{
      select: function(){
              comboDbConnections.getStore().reload({params:{PRO_UID : Ext.getCmp('PROCESS').getValue()}});
        if (Ext.getCmp('REP_TAB_TYPE').getValue() == 'GRID') {
          gridsListStore.reload({params:{PRO_UID : Ext.getCmp('PROCESS').getValue()}});
        } else {
          loadFieldNormal();
        }
      }
    }
  });

  var tbar = new Array();
  //if (_plugin_permissions !== false) {
  if (TABLE !== false && TABLE.ADD_TAB_TAG == 'plugin@simplereport') {
    tbar = [
      {
        text: _plugin_permissions.label,
        handler: function(){
          setTimeout(_plugin_permissions.fn, 0);
        }
      }
    ]
  }

  var items = new Array();
  if (PRO_UID === false)
    items.push(processComboBox);

  items.push({
    id: 'REP_TAB_NAME',
    fieldLabel: _("ID_TABLE_NAME"),
    xtype:'textfield',
    emptyText: _("ID_SET_A_TABLE_NAME"),
    width: 250,
    stripCharsRe: /(\W+)/g,
    style:'text-transform: uppercase',
    listeners:{
      change: function(){
        this.setValue(this.getValue().toUpperCase())
      }
    }
  });
  items.push({
    id: 'REP_TAB_DSC',
    fieldLabel: _("ID_DESCRIPTION"),
    xtype:'textarea',
    emptyText: _("ID_SET_TABLE_DESCRIPTION"),
    width: 250,
    height: 40,
    allowBlank: true
  });
  items.push({
    xtype: 'hidden',
    name: 'REP_TAB_GRID',
    value: 'GridComments-463650787492db06640c904001904930'
  });
  items.push({
    xtype: 'compositefield',
    fieldLabel: _("ID_TYPE"),
    msgTarget : 'side',
    anchor    : '-20',
    defaults  : {flex: 1 },
    items: [comboReport, comboGridsList]
  });
  items.push(comboDbConnections);


  var frmDetails = new Ext.FormPanel({
    id:'frmDetails',
    region: 'north',
    labelWidth: 120,
    labelAlign:'right',
    title: _('ID_NEW_REPORT_TABLE'),
    bodyStyle:'padding:10px',
    waitMsgTarget : true,
    frame: true,
    height: _plugin_permissions !== false ? 224 : 200,
    defaults: {
      allowBlank: false,
      msgTarget: 'side',
      align:'center'
    },
    items: items,
    tbar: tbar
  });


  southPanel = new Ext.FormPanel({
    region: 'south',
    buttons:[ {
        text: TABLE === false ? _("ID_CREATE") : _("ID_UPDATE"),
        handler: createReportTable
      }, {
        text:_("ID_CANCEL"),
        handler: function() {
          proParam = PRO_UID !== false ? '?PRO_UID='+PRO_UID : '';
          location.href = '../reportTables/main' + proParam; //history.back();
        }
    }]
  });

  var viewport = new Ext.Viewport({
    layout: 'border',
    autoScroll: false,
    items:[frmDetails, FieldsPanel, southPanel]
  });

  /*** Editing routines ***/
  if (TABLE !== false) {
    if(TABLE.ADD_TAB_TYPE != 'GRID')
      Ext.getCmp('REP_TAB_GRID').hide();
  } else {
    Ext.getCmp('REP_TAB_GRID').hide();
  }

  if (PRO_UID !== false) {
    comboDbConnections.getStore().reload({params:{PRO_UID : PRO_UID}});
    if (Ext.getCmp('REP_TAB_TYPE').getValue() == 'GRID') {
      gridsListStore.reload({params:{PRO_UID : PRO_UID}});
    }

    if (TABLE === false) {
      loadFieldNormal();
    } //else if(typeof avFieldsList != 'undefined')
      //loadAvFieldsFromArray(avFieldsList);

  } else {

  }

  DDLoadFields();

});


//////////////////////////////////////////////////////////////////////////////////////////

function createReportTable()
{
  //validate table name
  if(Ext.getCmp('REP_TAB_NAME').getValue().trim() == '') {
    Ext.getCmp('REP_TAB_NAME').focus();
    PMExt.error(_('ID_ERROR'), _('ID_TABLE_NAME_IS_REQUIRED'), function(){
      Ext.getCmp('REP_TAB_NAME').focus();
    });
    return false;
  }

  var allRows = assignedGrid.getStore();
  var columns = new Array();

  //validate columns count
  if(allRows.getCount() == 0) {
    PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT7'));
    return false;
  }

  for (var r=0; r < allRows.getCount(); r++) {
    row = allRows.getAt(r);
    //row.data['FIELD_FILTER'] = typeof(row.data['FIELD_FILTER']) != 'undefined' && row.data['FIELD_FILTER'] ? true : false;

    if(row.data['field_name'].trim() == '') {
      PMExt.error(_('ID_ERROR'), _('ID_FIELD_NAME_FOR')+'"'+row.data['field_dyn']+'"'+ _('ID_IS_REQUIRED'));
      return false;
    }

    if((row.data['field_type'] == 'VARCHAR' || row.data['field_type'] == 'INT') && row.data['field_name'] == '') {
      PMExt.error(_('ID_ERROR'), _('ID_FIELD_SIZE_FOR')+'"'+row.data['field_type']+'": '+row.data['field_name']+'"'+ _('ID_PLEASE'));
      return false;
    }

    columns.push(row.data);
  }

  Ext.Ajax.request({
    url: 'reportTables_Ajax',
    params: {
      action: 'save',
      REP_TAB_UID: TABLE !== false ? TABLE.ADD_TAB_UID : '',
      PRO_UID       : PRO_UID !== false? PRO_UID : Ext.getCmp('PROCESS').getValue(),
      REP_TAB_NAME  : Ext.getCmp('REP_TAB_NAME').getValue(),
      REP_TAB_DSC : Ext.getCmp('REP_TAB_DSC').getValue(),
      REP_TAB_CONNECTION : Ext.getCmp('REP_TAB_CONNECTION').getValue(),
      REP_TAB_TYPE  : Ext.getCmp('REP_TAB_TYPE').getValue(),
      REP_TAB_GRID: Ext.getCmp('REP_TAB_TYPE').getValue()=='GRID'? Ext.getCmp('REP_TAB_GRID').getValue(): '',
      columns: Ext.util.JSON.encode(columns)
    },
    success: function(resp){
      result = Ext.util.JSON.decode(resp.responseText);

      if (result.success) {
        proParam = PRO_UID !== false ? '?PRO_UID='+PRO_UID : '';
        location.href = '../reportTables/main' + proParam; //history.back();
      } else {
        Ext.Msg.alert( _('ID_ERROR'), result.msg);
      }
    },
    failure: function(obj, resp){
      Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
    }
  });
}
//end createReportTable

////ASSIGNBUTON FUNCTIONALITY
AssignFieldsAction = function(){
  records = Ext.getCmp('availableGrid').getSelectionModel().getSelections();

  for(i=0; i < records.length; i++){
    var PMRow = assignedGrid.getStore().recordType;
    var meta = mapPMFieldType(records[i].data['FIELD_UID']);
    var row = new PMRow({
      uid  : '',
      field_uid  : records[i].data['FIELD_UID'],
      field_dyn  : records[i].data['FIELD_NAME'],
      field_name  : records[i].data['FIELD_NAME'].toUpperCase(),
      field_label : records[i].data['FIELD_NAME'].toUpperCase(),
      field_type  : meta.type,
      field_size  : meta.size,
      field_key   : 0,
      field_null  : 1
    });

    store.add(row);
  }

  //remove from source grid
  Ext.each(records, Ext.getCmp('availableGrid').store.remove, Ext.getCmp('availableGrid').store);
};
//RemoveButton Functionality
RemoveFieldsAction = function(){

  records = Ext.getCmp('assignedGrid').getSelectionModel().getSelections();
  var PMRow = availableGrid.getStore().recordType;
  for(i=0; i < records.length; i++){
    if (records[i].data['field_dyn'] != '' && records[i].data['field_name'] != 'APP_UID' && records[i].data['field_name'] != 'APP_NUMBER' && records[i].data['field_name'] != 'ROW') {
      var row = new PMRow({
        FIELD_UID  : records[i].data['field_uid'],
        FIELD_NAME  : records[i].data['field_dyn']
      });
      availableGrid.getStore().add(row);
    } else {
      records[i] = null;
    }
  }
  //remove from source grid
  Ext.each(records, Ext.getCmp('assignedGrid').store.remove, Ext.getCmp('assignedGrid').store);
};

//AssignALLButton Functionality
AssignAllFieldsAction = function(){
  var available = Ext.getCmp('availableGrid');
  var allRows = available.getStore();
  var arrAux = new Array();
  records = new Array()

  if (allRows.getCount() > 0){
    var PMRow = assignedGrid.getStore().recordType;
    for (i=0; i < allRows.getCount(); i++){
      records[i] = allRows.getAt(i);
      var meta = mapPMFieldType(records[i].data['FIELD_UID']);
      var row = new PMRow({
        uid   : '',
        field_uid   : records[i].data['FIELD_UID'],
        field_dyn   : records[i].data['FIELD_NAME'],
        field_name  : records[i].data['FIELD_NAME'].toUpperCase(),
        field_label : records[i].data['FIELD_NAME'].toUpperCase(),
        field_type  : meta.type,
        field_size  : meta.size,
        field_key   : 0,
        field_null  : 1
      });

      store.add(row);
    }
    //remove from source grid
    Ext.each(records, Ext.getCmp('availableGrid').store.remove, Ext.getCmp('availableGrid').store);
  }

};

//RevomeALLButton Functionality
RemoveAllFieldsAction = function(){
  var allRows = Ext.getCmp('assignedGrid').getStore();
  var records = new Array();
  if (allRows.getCount() > 0) {
    var PMRow = availableGrid.getStore().recordType;
    for (var i=0; i < allRows.getCount(); i++){
      records[i] = allRows.getAt(i);
      if (records[i].data['field_dyn'] != '' && records[i].data['field_name'] != 'APP_UID' && records[i].data['field_name'] != 'APP_NUMBER' && records[i].data['field_name'] != 'ROW') {
        var row = new PMRow({
          FIELD_UID  : records[i].data['field_uid'],
          FIELD_NAME  : records[i].data['field_dyn']
        });
        availableGrid.getStore().add(row);
      } else {
        records[i] = null;
      }
    }
    //remove from source grid
    Ext.each(records, Ext.getCmp('assignedGrid').store.remove, Ext.getCmp('assignedGrid').store);
  }
};

  //ASSIGN BUTON FUNCTIONALITY
saveReportTables = function(){
  var fields = Ext.getCmp('fieldsGrid');
  var allRows = fields.getStore();
  var arrUID = new Array();
  var arrDYNAFORM = new Array();
  var arrNAME = new Array();
  var arrTYPE = new Array();
  var arrSIZE = new Array();
  var arrFILTER = new Array();
  var data = new Array();

  for (var r=0; r < allRows.getCount(); r++) {
    row = allRows.getAt(r);
    row.data['FIELD_FILTER'] = typeof(row.data['FIELD_FILTER']) != 'undefined' && row.data['FIELD_FILTER'] ? true : false;

    if(row.data['FIELD_NAME'].trim() == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT8') + '"'+row.data['FIELD_DYNAFORM']+'"'+_('ID_DYNAFORM_FIELD')+' '+ _('ID_PLEASE'));
      return false;
    }

    if((row.data['FIELD_TYPE'] == 'VARCHAR' || row.data['FIELD_TYPE'] == 'INT') && row.data['FIELD_SIZE'] == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT5') + '"'+row.data['FIELD_TYPE']+'": '+row.data['FIELD_NAME'] + '"' + _('ID_PLEASE'));
      return false;
    }

    data.push(row.data);
  }


  if (REP_TAB_TYPE=='NORMAL'){
    Ext.Ajax.request({
      url: 'reportTables_Ajax',
      params: {
        action: 'SaveChagedFieds',
        REP_TAB_TITLE : REP_TAB_TITLE,
        PRO_UID       : PRO_UID,
        REP_TAB_NAME  : REP_TAB_NAME,
        REP_TAB_TYPE  : REP_TAB_TYPE,
        REP_TAB_CONNECTION : REP_TAB_CONNECTION,
        REP_TAB_UID: reportTablesGlobal.REP_TAB_UID_EDIT,
        data: Ext.util.JSON.encode(data)
      },
      success: function(obj, resp){
        PMExt.notify( _('ID_DONE') , _('ID_REPORT_SAVE') );
        Ext.getCmp('reportTableGrid').getStore().reload();
        Ext.getCmp('newRepTab').close();
        Ext.getCmp('winEditFields').close();
      },
      failure: function(obj, resp){
        Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
      }
    });
  } else { //then is a grid type
      Ext.Ajax.request({
      url: 'reportTables_Ajax',
      params: { action: 'SaveChagedFieds',

                PRO_UID:PRO_UID,
                REP_TAB_TITLE:REP_TAB_TITLE,
                REP_TAB_NAME:REP_TAB_NAME,
                REP_TAB_TYPE:REP_TAB_TYPE,
                REP_TAB_UID: reportTablesGlobal.REP_TAB_UID,
                UIDS: arrUID.join(','),
                REP_TAB_GRID: arrUID.join(','),
                DYNAFORMS:arrDYNAFORM.join(','),
                NAMES:arrNAME.join(','),
                TYPES:arrTYPE.join(','),
                SIZES:arrSIZE.join(','),
                FILTERS:arrFILTER.join(',')

              },
      success: function(obj, resp){
            win.close();
            Ext.getCmp('reportTableGrid').getStore().reload();


            },
      failure: function(obj, resp){
            Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
            //viewport.getEl().unmask();
      }
    });
  }

};

//INITIAL FIELDS GRIDS
loadFieldNormal = function(){
  Ext.getCmp('availableGrid').store.removeAll();
  Ext.getCmp('availableGrid').store.load({
    params: {
      action: "availableFieldsReportTables",
      PRO_UID: PRO_UID !== false ? PRO_UID : Ext.getCmp('PROCESS').getValue()
    }
  });
  Ext.getCmp('assignedGrid').store.removeAll();
};

loadFieldsGrids = function(){
  var available = Ext.getCmp('availableGrid');
  available.store.removeAll();

  available.store.load({
    params: {
      action: "availableFieldsReportTables",
      PRO_UID: PRO_UID !== false ? PRO_UID : Ext.getCmp('PROCESS').getValue(),
      TYPE: 'GRID',
      GRID_UID: Ext.getCmp('REP_TAB_GRID').getValue()
    }
  });

  var assigned = Ext.getCmp('assignedGrid');
  assigned.store.removeAll();

};

//REFRESH FIELDS GRIDS
RefreshFields = function(){
  var available = Ext.getCmp('availableGrid');
  available.store.load({params: {"action":"deleteFieldsReportTables", "PRO_UID":PRO_UID }});
  var assigned = Ext.getCmp('assignedGrid');
  assigned.store.load({params: {"action":"assignedFieldsReportTables", "PRO_UID":PRO_UID }});
};

//FAILURE AJAX FUNCTION
FailureFields = function(){
  Ext.Msg.alert(_('ID_GROUPS'), _('ID_MSG_AJAX_FAILURE'));
};


//ASSIGN FIELDS TO REPORT TABLE
SaveFieldsReportTable = function(arr_avail, function_success, function_failure){
  var sw_response;
  //Ext.MessageBox.show({ msg: 'Match Fields', wait:true,waitConfig: {interval:200} });
  lmask = new Ext.LoadMask(Ext.getBody(),{msg:_('ID_PROCESSING')});
  lmask.show();

  Ext.Ajax.request({
    url: 'reportTables_Ajax',
    params: {
      action: 'assignFieldsToReportTable',
      REP_TAB_UID: reportTablesGlobal.REP_TAB_UID,
      FIELDS: arr_avail.join(',')
    },
    success: function(){
      function_success();
      //Ext.MessageBox.hide();
      lmask.hide();
    },
    failure: function(){
      function_failure();
      //viewport.getEl().unmask();
    }
  });
};

//REMOVE GROUPS FROM A USER
DeleteFieldsReportTable = function(arr_asign, function_success, function_failure){
  var sw_response;
  Ext.MessageBox.show({ msg: _('ID_DELETING_ELEMENTS'), wait:true,waitConfig: {interval:200} });

  Ext.Ajax.request({
    url: 'reportTables_Ajax',
    params: {action: 'deleteFieldsToReportTable',
            REP_TAB_UID: reportTablesGlobal.REP_TAB_UID,
            FIELDS: arr_asign.join(','),
            TYPE: Ext.getCmp('REP_TAB_TYPE').getValue(),
            GRID_UID: Ext.getCmp('REP_TAB_TYPE').getValue()=='GRID'? Ext.getCmp('REP_TAB_GRID').getValue() : ''
    },
    success: function(){
      function_success();
      Ext.MessageBox.hide();
    },
    failure: function(){
      function_failure();
     // viewport.getEl().unmask();
    }
  });
};


var DDLoadFields = function(){
  var availableGridDropTargetEl = availableGrid.getView().scroller.dom;
  var availableGridDropTarget = new Ext.dd.DropTarget(availableGridDropTargetEl, {
    ddGroup    : 'availableGridDDGroup',
    notifyDrop : function(ddSource, e, data){

      var records =  ddSource.dragData.selections;
      var PMRow = availableGrid.getStore().recordType;

      for (i=0; i < records.length; i++){
        if (records[i].data['field_dyn'] != '' && records[i].data['field_name'] != 'APP_UID' && records[i].data['field_name'] != 'APP_NUMBER' && records[i].data['field_name'] != 'ROW') {
          var row = new PMRow({
            FIELD_UID: records[i].data['field_uid'],
            FIELD_NAME: records[i].data['field_dyn']
          });
          availableGrid.getStore().add(row);
        } else if (records[i].data['field_dyn'] != '') {
          records[i] = null;
        }
      }

      Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
      return true;
    }
  });

  //droptarget on grid forassignment
  var assignedGridDropTargetEl = assignedGrid.getView().scroller.dom;
  var assignedGridDropTarget = new Ext.dd.DropTarget(assignedGridDropTargetEl, {
    ddGroup    : 'assignedGridDDGroup',
    notifyDrop : function(ddSource, e, data){

      var records =  ddSource.dragData.selections;
      var PMRow = assignedGrid.getStore().recordType;

      //add on target grid
      for (i=0; i < records.length; i++){
        //arrAux[r] = records[r].data['FIELD_UID'];
        var meta = mapPMFieldType(records[i].data['FIELD_UID']);
        var row = new PMRow({
          uid  : '',
          field_uid  : records[i].data['FIELD_UID'],
          field_dyn  : records[i].data['FIELD_NAME'],
          field_name  : records[i].data['FIELD_NAME'].toUpperCase(),
          field_label : records[i].data['FIELD_NAME'].toUpperCase(),
          field_type  : meta.type,
          field_size  : meta.size,
          field_key   : 0,
          field_null  : 1
        });

        store.add(row);
      }
      //remove from source grid
      Ext.each(records, availableGrid.store.remove, availableGrid.store);

      return true;
    }
  });
  //sw_func_groups = true;
};

function loadTableRowsFromArray(records)
{
  var PMRow = assignedGrid.getStore().recordType;
  if (records.length == 0) return;

  for(i in records) {
    var row = new PMRow({
      uid        : records[i].FLD_UID,
      field_uid  : records[i].FLD_DYN_UID,
      field_dyn  : records[i].FLD_DYN_NAME,
      field_name : records[i].FLD_NAME,
      field_label: records[i].FLD_DESCRIPTION,
      field_type : records[i].FLD_TYPE,
      field_size : records[i].FLD_SIZE,
      field_key  : records[i].FLD_KEY,
      field_null  : records[i].FLD_NULL,
      field_filter: records[i].FLD_FILTER == '1' ? true : false
    });

    store.add(row);
  }
}

function loadAvFieldsFromArray(records)
{
  var PMRow = availableGrid.getStore().recordType;

  for(i=0; i<records.length; i++) {
    var row = new PMRow({
      FIELD_UID: records[i].FIELD_UID,
      FIELD_NAME: records[i].FIELD_NAME
    });

    availableGrid.getStore().add(row);
  }
}

function mapPMFieldType(id)
{
  var meta = id.split('-');

  switch(meta[1]) {
      case 'text':
      case 'password':
      case 'dropdown':
      case 'yesno':
      case 'checkbox':
      case 'radiogroup':
      case 'hidden':
        typeField='VARCHAR';
        sizeField='32';
        break;
      case 'currency':
        typeField='INT';
        sizeField='11';
        break;
      case 'percentage':
        typeField='FLOAT';
        sizeField='11';
        break;
      case 'date':
        typeField='DATE';
        sizeField='';
        break;
      case 'textarea':
        typeField='TEXT';
        sizeField='';
        break;

      default:
        typeField='TEXT';
        sizeField='';
        break;
  }

  return {type:typeField, size:sizeField};
}

Ext.override(Ext.form.TextField, {
    initComponent: Ext.form.TextField.prototype.initComponent.createInterceptor(function(){
        // This interceptor calls the original TextFields' initComponent method
        // after executing this function.
        if (this.convertToUpperCase) {
            // The following style makes all letters uppercase when typing,
            // but it only affects the display, actual characters are preserved
            // as typed.  That is why we need to override the getValue function.
            this.style = "textTransform: uppercase;"
        }
    }),

    getValue: function(){
        var value = Ext.form.TextField.superclass.getValue.call(this);
        if (this.convertToUpperCase) {
            value = value.toUpperCase();
        }
        return value;
    }
});