/**
 * PM tables Edit
 * @author Erik A. O. <erik@colosa.com>
 */

var store;
var storeP;
var storeA;
var cmodelP;
var smodelA;
var smodelP;
var availableGrid;
var assignButton;
var assignAllButton;
var removeButton;
var removeAllButton;

var store;
var editor;
var selCombo='';

Ext.onReady(function(){

  var fm = Ext.form;
  var fieldsCount = 0;
  // store for available fields grid
  storeA = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: '../pmTablesProxy/getDynafields'
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
    stateId         : 'gridEdit',
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
            case 0:
              //Ext.getCmp('removeButton').disable();
              Ext.getCmp('editColumn').disable();
              Ext.getCmp('removeColumn').disable();
              break;
            case 1:
              Ext.getCmp('editColumn').enable();
              Ext.getCmp('removeColumn').enable();
              break;
            default:
              //Ext.getCmp('removeButton').enable();
              Ext.getCmp('editColumn').disable();
              Ext.getCmp('removeColumn').enable();
              break;
          }
        }
    }
  });

  var sizeField = new fm.NumberField({
                      name: 'sizeEdit',
                      id: 'sizeEdit',
                      allowBlank: true,
                      allowDecimals: false,
                      allowNegative: false,
                      disabled: true,
                      nanText: 'This field should content a number',
                      minValue: 1,
                      maxValue: 99,
                      minLength: 0
                  });

  //check column for tablesizeField columns grid
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
          hidden: true
      }, {
          id: 'field_name',
          header: _("ID_FIELD_NAME"),
          dataIndex: 'field_name',
          width: 220,
          editor: {
            xtype: 'textfield',
            allowBlank: true,
            listeners:{
              change: function(f,e){
            	this.setValue(this.getValue().replace(/\s/g,'').toUpperCase());
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
          width: 75,
          editor: new fm.ComboBox({
              typeAhead: true,
              editable: false,
              lazyRender: true,
              mode: 'local',
              displayField:'type',
              valueField:'type_id',
              autocomplete: true,
              triggerAction: 'all',
              forceSelection: true,
              store: new Ext.data.SimpleStore({
                  fields: ['type_id', 'type'],
                  data: columnsTypes,
                  sortInfo: {field:'type_id', direction:'ASC'}
              }),
              listeners: {
                  beforerender: function (combo)
                  {
                      if (combo.getValue() == "" && combo.store.getAt(0)) {
                          combo.setValue(combo.store.getAt(0).get(combo.valueField));
                      }
                  },
                  beforeshow: function (combo)
                  {
                        if (combo.getValue() == "" && combo.store.getAt(0)) {
                            combo.setValue(combo.store.getAt(0).get(combo.valueField));
                        }
                  },
                  'select': function(combo, row, index) {
                      if( cm && cm instanceof Ext.grid.ColumnModel) {
                          if(selCombo != combo.getValue()) {
                              Ext.getCmp('sizeEdit').setValue('');
                          }
                          selCombo = combo.getValue();

                          var swSize = 1; //Enable
                          var swNull = 1;
                          var swPK = 1;
                          var swAI = 1;

                          //Date
                          if (selCombo == "DATE" || selCombo == "DATETIME" || selCombo == "TIME") {
                              swSize = 0; //Disable
                              swPK = 0;
                              swAI = 0;
                          }

                          //Numbers
                          if (selCombo == "INTEGER" || selCombo == "BIGINT" || selCombo == "TINYINT" || selCombo == "SMALLINT") {
                             //Enable All
                          }

                          if (selCombo == "DECIMAL" || selCombo == "DOUBLE" || selCombo == "FLOAT" || selCombo == "REAL") {
                              swPK = 0;
                              swAI = 0;
                          }

                          //String
                          if (selCombo == "CHAR" || selCombo == "VARCHAR") {
                              swAI = 0;
                          }


                          if (selCombo == "LONGVARCHAR") {
                              swPK = 0;
                              swAI = 0;
                          }

                          //Boolean
                          if (selCombo == "BOOLEAN") {
                              swSize = 0;
                              swNull = 0;
                              swPK = 0;
                              swAI = 0;
                          }

                          if (swNull == 1) {
                              Ext.getCmp("field_null").enable();
                          } else {
                              Ext.getCmp("field_null").disable();
                              Ext.getCmp("field_null").setValue(false);
                          }

                          if (swPK == 1) {
                              Ext.getCmp("field_primary_key").enable();
                          } else {
                              Ext.getCmp("field_primary_key").disable();
                              Ext.getCmp("field_primary_key").setValue(false);
                          }

                          if (swAI == 1) {
                              Ext.getCmp("field_incre").enable();
                          } else {
                              Ext.getCmp("field_incre").disable();
                              Ext.getCmp("field_incre").setValue(false);

                          }

                          if (swSize == 1) {
                              Ext.getCmp("sizeEdit").enable();

                              if (selCombo == "CHAR" || selCombo == "VARCHAR" || selCombo == "LONGVARCHAR") {
                                  Ext.getCmp("sizeEdit").setMaxValue(((selCombo == "CHAR") ? 255 : 999));
                                  sizeField.getEl().dom.maxLength = 3;
                              } else {
                                  Ext.getCmp("sizeEdit").setMaxValue(99);
                                  sizeField.getEl().dom.maxLength = 2;
                              }
                          } else {
                              Ext.getCmp("sizeEdit").disable();
                              Ext.getCmp("sizeEdit").setValue("");
                          }
                      }
                  }//select
              }
          })
      }, {
          id: 'field_size',
          header: _("ID_SIZE"),
          dataIndex: 'field_size',
          width: 50,
          align: 'right',
          editor: sizeField
      }, {
        xtype: 'booleancolumn',
        header: _('ID_NULL'),
        dataIndex: 'field_null',
        align: 'center',
        width: 50,
        trueText: _('ID_YES'),
        falseText: _('ID_NO'),
        editor: {
            xtype: 'checkbox',
            id: 'field_null',
            checked: false,
            disabled: true
        }
      }, {
        xtype: 'booleancolumn',
        header: _('ID_PRIMARY_KEY'),
        dataIndex: 'field_key',
        align: 'center',
        width: 80,
        trueText: _('ID_YES'),
        falseText: _('ID_NO'),
        editor: {
            xtype: 'checkbox',
            id: 'field_primary_key',
            disabled: true,
            inputValue: 'always'
        }
      }, {
        xtype: 'booleancolumn',
        header: _('ID_AUTO_INCREMENT'),
        dataIndex: 'field_autoincrement',
        align: 'center',
        width: 80,
        trueText: _('ID_YES'),
        falseText: _('ID_NO'),
        editor: {
            xtype: 'checkbox',
            id: 'field_incre',
            disabled: true,
            inputValue: 'always'
        }
      }
  ];

  //if permissions plugin is enabled
  if (TABLE !== false && TABLE.ADD_TAB_TAG == 'plugin@simplereport') {
    cmColumns.push({
        xtype: 'booleancolumn',
        header: 'Filter',
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
        resizable: false,
        sortable:  true // columns are not sortable by default
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
          {name: 'field_autoincrement', type: 'float'},
          {name: 'field_filter', type: 'string'}
      ]
  });
  //row editor for table columns grid
  editor = new Ext.ux.grid.RowEditor({
    saveText: _("ID_UPDATE"),
    listeners: {
      canceledit: function(grid,obj){
        if ( grid.record.data.field_label == '' && grid.record.data.field_name == '') {
          store.remove(grid.record);
        }
      }
    }
  });

  editor.on({
    afteredit: function(roweditor, changes, record, rowIndex) {
      //
    },
    afteredit: function(roweditor, rowIndex) {
      row = assignedGrid.getSelectionModel().getSelected();
      //if a column was set as PK so can't be null
      if (row.get('field_key') == true) {
        row.data.field_null = false;

      }
      row.commit();
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
        icon: '/images/add-row-after.png',
        text: _("ID_ADD_FIELD"),
        handler: addColumn
      },  {
        id: 'editColumn',
        icon: '/images/edit-row.png',
        text: _("ID_EDIT_FIELD"),
        disabled: true,
        handler: editColumn
      }, {
        id: 'removeColumn',
        icon: '/images/delete-row.png',
        text: _("ID_REMOVE_FIELD"),
        disabled: true,
        handler: removeColumn
      }
    ],

    border: false,

    listeners: {
      render: function(grid) {
        var ddrow = new Ext.dd.DropTarget(grid.getView().mainBody, {
          ddGroup: 'availableGridDDGroup',
          copy: false,
            notifyDrop: function(dd, e, data) {
            var ds = grid.store;
            var sm = grid.getSelectionModel();
            var rows = sm.getSelections();
            if (dd.getDragData(e)) {
              var cindex = dd.getDragData(e).rowIndex;
              //skipping primary keys, we can't reorder
              if (store.data.items[cindex].data.field_key)
                return;

              if (typeof(cindex) != "undefined") {
                for(var i = 0; i < rows.length; i++) {
                  //skipping primary keys, we can't reorder
                  if (rows[i].data.field_key )
                    continue;

                  var srcIndex = ds.indexOfId(rows[i].id);
                  ds.remove(ds.getById(rows[i].id));
                  if (i > 0 && cindex < srcIndex) {
                    cindex++;
                  }
                  ds.insert(cindex, rows[i]);
                }
                sm.selectRecords(rows);
              }
            }
          }
        });
      }
    }
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
    items        : [assignedGrid],
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
    autoLoad: false,
    proxy : new Ext.data.HttpProxy({
      url: '../pmTablesProxy/getDbConnectionsList',
      method : 'POST'
    }),
    baseParams : {
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
            Ext.Msg.alert( _('ID_ERROR'), _('ID_DB_CONNECTION_NOT_EXIST') );
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

  var tbar = new Array();
  var items = new Array();

  items.push({
    id: 'REP_TAB_NAME',
    fieldLabel: _("ID_TABLE_NAME") + ' <span style="font-size:9">('+_("ID_AUTO_PREFIX") + ' "PMT")</span>',
    xtype:'textfield',
    emptyText: _("ID_SET_A_TABLE_NAME"),
    width: 250,
    autoCreate: {tag: "input", type: "text", autocomplete: "off", maxlength: sizeTableName },
    stripCharsRe: /(\W+)/g,
    listeners:{
      change: function(){
        this.setValue(this.getValue().toUpperCase());
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

  items.push(
      {
          layout: "column",
          style: "margin-left: 255px;",
          hidden: (dataNumRows > 0)? false : true,
          items: [
              {
                  xtype: "checkbox",
                  id: "chkKeepData",
                  name: "chkKeepData",
                  boxLabel: _("ID_PMTABLE_DATA_KEEP")
              }
          ]
      }
  );

  //items.push(comboDbConnections);

  var frmDetails = new Ext.FormPanel({
    id         :'frmDetails',
    region     : 'north',
    labelWidth: 250,
    labelAlign :'right',
    title      : ADD_TAB_UID ? _('ID_PMTABLE') : _('ID_NEW_PMTABLE'),
    bodyStyle  :'padding:10px',
    frame      : true,
    height: 170,
    items      : items,
    //tbar       : tbar,
    waitMsgTarget : true,
    defaults: {
      allowBlank : false,
      msgTarget  : 'side',
      align      :'center'
    }
  });


  southPanel = new Ext.FormPanel({
    region: 'south',
    buttons:[
      {
        text: TABLE === false ? _("ID_CREATE") : _("ID_UPDATE"),
        handler: function() {
          if (TABLE === false || dataNumRows == 0 || Ext.getCmp("chkKeepData").checked == true) {
            createReportTable();
          }
          else {
            PMExt.confirm(_('ID_CONFIRM'), _('ID_PMTABLE_SAVE_AND_DATA_LOST'), createReportTable);
          }
        }
      }, {
        text:_("ID_CANCEL"),
        handler: function() {
          proParam = PRO_UID !== false ? '?PRO_UID='+PRO_UID : '';
          location.href = '../pmTables' + proParam;
        }
      }
    ]
  });

  var viewport = new Ext.Viewport({
    layout: 'border',
    autoScroll: false,
    items:[frmDetails, FieldsPanel, southPanel]
  });

  /*** Editing routines ***/
  if (TABLE !== false) {
    Ext.getCmp('REP_TAB_NAME').setValue(TABLE.ADD_TAB_NAME);
    Ext.getCmp('REP_TAB_NAME').setDisabled(true);
    Ext.getCmp('REP_TAB_DSC').setValue(TABLE.ADD_TAB_DESCRIPTION);

    loadTableRowsFromArray(TABLE.FIELDS);
  }

  if (dataNumRows > 0) {
    var tpl = new Ext.Template(
        '<div id="fb" style="border: 1px solid #FF0000; background-color:#FFAAAA; display:none; padding:15px; color:#000000; font-size:12px;">'+
        '<b>Warning: </b> ' + dataNumRows + ' ' + _('ID_PMTABLE_DATA_EXISTS_WARNINIG') + ' <a href="#" id="hideWarning">[ '+_('ID_HIDE')+' ]</a></div>'
    );
    var newEl = tpl.insertFirst(document.getElementById('assignedGrid'));

    Ext.fly('hideWarning').on('click', function() {
      Ext.fly(newEl).slideOut('t',{remove:true});
    });
    Ext.fly(newEl).slideIn();
  }

});

// actions

function createReportTable()
{
  var tableName        = Ext.getCmp('REP_TAB_NAME').getValue().trim();
  var tableDescription = Ext.getCmp('REP_TAB_DSC').getValue().trim();

  //validate table name
  if (tableName == '') {
    Ext.getCmp('REP_TAB_NAME').focus();
    PMExt.error(_('ID_ERROR'), _('ID_TABLE_NAME_IS_REQUIRED'), function(){
      Ext.getCmp('REP_TAB_NAME').focus();
    });
    return false;
  }

  // validate table name length
  if(tableName.length < 4) {
    PMExt.error(_('ID_ERROR'), _('ID_TABLE_NAME_TOO_SHORT'), function(){
      Ext.getCmp('REP_TAB_NAME').focus();
    });
    return false;
  }

  var allRows = assignedGrid.getStore();
  var columns = new Array();
  var hasSomePrimaryKey = false;

  //validate columns count
  if(allRows.getCount() == 0) {
    PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT7'));
    return false;
  }
  var fieldsNames = new Array();
  // Reserved Words
  var reservedWords = new Array('DESC');


  for (var i = 0; i < allRows.getCount(); i++) {
    row = allRows.getAt(i);

    if (in_array(row.data['field_name'], fieldsNames)) {
      PMExt.error(_('ID_ERROR'),_('ID_PMTABLES_ALERT1') + ' <b>' + row.data['field_name']+'</b>');
      return false;
    }

    for (j=0; j < reservedWords.length; j++) {
      if (row.data['field_name'] == reservedWords[j]) {
        PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_RESERVED_FIELDNAME_WARNING', reservedWords[j]));
        return false;
      }
    }

    // validate that fieldname is not empty
    if(row.data['field_name'].trim() == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT2'));
      return false;
    }

    if(row.data['field_label'].trim() == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT3'));
      return false;
    }

    if (row.data['field_type'] == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT4') + ' <b>'+row.data['field_name']+'</b>');
      return false;
    }

    // validate field size for varchar & int column types
    if ((row.data['field_type'] == 'VARCHAR' || row.data['field_type'] == 'INTEGER') && row.data['field_size'] == '') {
      PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT5')+' '+row.data['field_name']+' ('+row.data['field_type']+').');
      return false;
    }

    if (row.data['field_key']) {
      hasSomePrimaryKey = true;
    }
    fieldsNames.push(row.data['field_name']);
    columns.push(row.data);
  }

  if (!hasSomePrimaryKey) {
    PMExt.error(_('ID_ERROR'), _('ID_PMTABLES_ALERT6'));
    return;
  }

  Ext.Msg.show({
    title : '',
    msg : TABLE !== false ? _('ID_UPDATING_TABLE') : _('ID_CREATING_TABLE'),
    wait:true,
    waitConfig: {interval:500}
  });

  var p = {
      REP_TAB_UID: (TABLE !== false)? TABLE.ADD_TAB_UID : "",
      PRO_UID: "",
      REP_TAB_NAME: (TABLE !== false)? tableName : "PMT_" + tableName,
      REP_TAB_DSC: tableDescription,
      REP_TAB_CONNECTION: "workflow",
      REP_TAB_TYPE: "",
      REP_TAB_GRID: "",
      columns: Ext.util.JSON.encode(columns)
  };

  if (dataNumRows > 0) {
      p.keepData = (Ext.getCmp("chkKeepData").checked == true)? 1 : 0;
  }

  Ext.Ajax.request({
    url: '../pmTablesProxy/save',
    params: p,
    success: function(resp){
      try {
        result = Ext.util.JSON.decode(resp.responseText);
        Ext.MessageBox.hide();

        if (result.success) {
          proParam = PRO_UID !== false ? '?PRO_UID='+PRO_UID : '';
          location.href = '../pmTables' + proParam; //history.back();
        } else {
          PMExt.error(_('ID_ERROR'), result.type +': '+result.msg);
          if (window.console && window.console.firebug) {
            window.console.log(result.msg);
            window.console.log(result.trace);
          }
        }
      } catch (e) {
        if (dbg) {
          _showDebugWin(resp.responseText);
        } else {
          PMExt.error( _('ID_ERROR'), _('ID_SOMETHING_WRONG'));
        }
      }
    },
    failure: function(obj, resp){
      Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
    }
  });
}
//end createReportTable

function _showDebugWin(content)
{
  dbgWin = new Ext.Window({
    title: '',
    id: 'dbgWin',
    layout: 'fit',
    width: 570,
    height: 400,
    modal: false,
    autoScroll: true,
    maximizable: true,
    //closeAction: 'hide',
    maximizable : false,
    items: [],
    x: 0,
    y: 0,
    html: '<pre>' + content + '</pre>'
  });

  dbgWin.show();
}

function addColumn() {
  var PMRow = assignedGrid.getStore().recordType;
  //var meta = mapPMFieldType(records[i].data['FIELD_UID']);
  var row = new PMRow({
    uid : '',
    field_uid  : '',
    field_dyn  : '',
    field_name : '',
    field_label: '',
    field_type : '',
    field_size : '',
    field_key  : 0,
    field_null : 1
  });
  var len = assignedGrid.getStore().data.length;

  editor.stopEditing();
  store.insert(len, row);
  assignedGrid.getView().refresh();
  assignedGrid.getSelectionModel().selectRow(len);
  editor.startEditing(len);
}

function editColumn()
{
  var row = Ext.getCmp('assignedGrid').getSelectionModel().getSelected();
  var selIndex = store.indexOfId(row.id);
  editor.stopEditing();
  assignedGrid.getView().refresh();
  assignedGrid.getSelectionModel().selectRow(selIndex);
  editor.startEditing(selIndex);
}

function removeColumn()
{
  PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_REMOVE_FIELD'), function(){
    var records = Ext.getCmp('assignedGrid').getSelectionModel().getSelections();
    Ext.each(records, Ext.getCmp('assignedGrid').store.remove, Ext.getCmp('assignedGrid').store);
  });
}


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

// drag & drop handler
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

  for (i=0;i<records.length; i++) {
    var row = new PMRow({
      uid        : records[i].FLD_UID,
      field_uid  : records[i].FLD_DYN_UID,
      field_dyn  : records[i].FLD_DYN_NAME,
      field_name : records[i].FLD_NAME,
      field_label: records[i].FLD_DESCRIPTION,
      field_type : records[i].FLD_TYPE,
      field_size : records[i].FLD_SIZE,
      field_key  : records[i].FLD_KEY == '1' ? true : false,
      field_null : records[i].FLD_NULL  == '1' ? true : false,
      field_autoincrement  : records[i].FLD_AUTO_INCREMENT  == '1' ? true : false,
      field_filter: records[i].FLD_FILTER == '1' ? true : false
    });
    store.add(row);
  }
}


function in_array(needle, haystack) {
  for(var i in haystack) {
      if(haystack[i] == needle) return true;
  }
  return false;
}
