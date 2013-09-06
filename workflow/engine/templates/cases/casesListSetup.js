Ext.onReady(function () {
    var currentIndexRowSelected;

    casesListProcessAjax = function (option)
    {
        var p;
        var i = 0;

        switch (option) {
            case "FIELD_SET":
                p = {
                    "xaction": option,
                    "action": currentAction
                };
                break;
            case "FIELD_RESET":
            case "FIELD_RESET_ID":
                p = {
                    "xaction": option,
                    "action": currentAction
                };
                break;
            case "FIELD_COMPLETE":
            case "FIELD_COMPLETE_ID":
            case "FIELD_LABEL_RESET":
            case "FIELD_LABEL_RESET_ID":
            case "FIELD_SAVE":

                var rs = firstGrid.store.data.items;
                var fv = [];

                for (i = 0; i <= rs.length - 1; i++) {
                    fv[i] = rs[i].data["name"];
                }

                var rs = secondGrid.store.data.items;
                var sv = [];

                for (i = 0; i <= rs.length - 1; i++) {
                    //sv[i]= rs[i].data["name"];
                    sv[i] = rs[i].data;
                }

                p = {
                    "xaction": option,
                    "action":  currentAction,
                    "first":   Ext.util.JSON.encode(fv),
                    "second":  Ext.util.JSON.encode(sv),
                    "pmtable": pmTablesDropdown.getValue()
                };
                break;
        }

        Ext.Ajax.request({
            url: "proxyPMTablesFieldList",
            method: "POST",
            params: p,

            success: function (response, opts)
            {
                var dataResponse = eval("(" + response.responseText + ")"); //json

                switch (option) {
                    case "FIELD_SET":
                        configDefaultResponseText = response.responseText;
                        fieldSet(dataResponse);
                        break;
                    case "FIELD_RESET":
                    case "FIELD_RESET_ID":
                        fieldSet(dataResponse);
                        break;
                    case "FIELD_COMPLETE":
                    case "FIELD_COMPLETE_ID":
                        fieldSet(dataResponse);
                        break;
                    case "FIELD_LABEL_RESET":
                    case "FIELD_LABEL_RESET_ID":
                        fieldSet(dataResponse);
                        break;
                    case "FIELD_SAVE":
                        configDefaultResponseText = response.responseText;
                        Ext.Msg.alert(_("ID_INFO"), _("ID_SAVED"));
                        location.reload(true);
                        break;
                }
            },
            failure: function (response, opts)
            {
                //
            }
        });
    };

    fieldSet = function (dataResponse)
    {
        remotePmFieldsStore.loadData(dataResponse.first);
        secondGridStore.loadData(dataResponse.second);

        //Remove APP_UID and DEL_INDEX from second grid, this is only to avoid display in this grid
        var fieldName = "";
        var i = 0;

        while (i <= secondGrid.store.data.items.length - 1) {
            fieldName = secondGrid.store.data.items[i].data["name"];

            if (fieldName == "APP_UID" ||
                fieldName == "DEL_INDEX" ||
                fieldName == "USR_UID" ||
                fieldName == "PREVIOUS_USR_UID"
            ) {
                secondGrid.store.removeAt(i);
            } else {
                i = i + 1;
            }
        }

        //Set also the selected table value in the comboBox element.
        if (PmTableStore.getTotalCount() > 0) {
            pmTablesDropdown.setValue(dataResponse.PMTable);
        } else {
            pmTablesDropdown.setValue("");
        }
    };

    sendGridFieldsRequest = function (action)
    {
        currentAction = action;

        casesListProcessAjax("FIELD_SET");
    };

    //Variables
    var currentAction = "";
    var configDefaultResponseText = "";
    var tabIndex = 0;

  // Generic fields array to use in both store defs.
  var pmFields = [
    {name: 'name', mapping : 'name'},
    {name: 'gridIndex', mapping : 'gridIndex'},
    {name: 'fieldType', mapping : 'fieldType'},
    {name: 'label', mapping : 'label'},
    //{name: 'width', mapping : 'width'},
    {name: 'align', mapping : 'align'}
  ];

  //Dropdown to select the PMTable
  var PmTableStore = new Ext.data.JsonStore({
     root            : 'data',
     url             : 'proxyPMTablesList',
     totalProperty   : 'totalCount',
     idProperty      : 'gridIndex',
     remoteSort      : false, //true,
     autoLoad        : false,
     fields          : [
       'ADD_TAB_UID', 'ADD_TAB_NAME'
     ],
     listeners       : {load: function() {
         tabs.setActiveTab(tabIndex);
     }}
  });

  // create the Data Store to list PMTables in the dropdown
  var pmTablesDropdown = new Ext.form.ComboBox ({
    width        : '180',
    xtype        : 'combo',
    emptyText: _("ID_EMPTY_PMTABLE"),
    displayField : 'ADD_TAB_NAME',
    valueField   : 'ADD_TAB_UID',
    triggerAction: 'all',
    store        : PmTableStore,
    listeners: {
      'select': function() {
        var tableUid  =  this.value;
        Ext.Ajax.request({
          url: 'proxyPMTablesFieldList',
          success: function(response) {
            var dataResponse = Ext.util.JSON.decode(response.responseText);
            var rec = Ext.data.Record.create(pmFields);
            //alert(firstGrid.store);
            var index;
            var record;
            var count = firstGrid.store.getTotalCount();

            // removing all the PM Table fields from the first grid
            do {
              index = firstGrid.store.find('fieldType','PM Table');
              record = firstGrid.store.getAt(index);
              if (index>=0) {
                firstGrid.store.remove(record);
              }
            } while (index>=0);

            // removing all the PM Table fields from the second grid
            do {
              index = secondGrid.store.find('fieldType','PM Table');
              record = secondGrid.store.getAt(index);
              if (index>=0) {
                secondGrid.store.remove(record);
              }
            } while (index>=0);

            for (var i = 0; i <= dataResponse.data.length-1; i++) {
              var d = new rec( dataResponse.data[i] );
              firstGrid.store.add(d);
            }
            firstGrid.store.commitChanges();
          },
          failure: function(){},
          params: {xaction: 'getFieldsFromPMTable', table: tableUid }
        });

      }
    }
  });

  // COMPONENT DEPRECATED remove it in the next revision of the enterprise plugin
  // create the Dropdown for rows per page
  var pmRowsPerPage = new Ext.form.ComboBox ({
    width         : 60,
    boxMaxWidth   : 70,
    editable      : false,
    triggerAction : 'all',
    mode          : 'local',
    store        : new Ext.data.ArrayStore({
      fields: ['id'],
      data  : [[5], [6], [7], [8], [9], [10], [12], [15], [18], [20], [25], [30], [50], [100] ]
    }),
    valueField    : 'id',
    displayField  : 'id',
    triggerAction : 'all'
  });

  // COMPONENT DEPRECATED remove it in the next revision of the enterprise plugin
  // create the Dropdown for date formats
  var pmDateFormat = new Ext.form.ComboBox ({
    width         : 80,
    boxMaxWidth   : 90,
    editable      : false,
    triggerAction : 'all',
    mode          : 'local',
    store        : new Ext.data.ArrayStore({
      fields: ['id'],
      data  : [['M d, Y'],['M d Y'],['M d Y H:i:s'],['d M Y'],['d M Y H:i:s'],['Y-m-d'],['Y-m-d H:i:s'],['Y/m/d '],['Y/m/d H:i:s'],['D d M Y'] ]
    }),
    valueField    : 'id',
    displayField  : 'id',
    triggerAction : 'all'
  });

  PmTableStore.setDefaultSort('ADD_TAB_NAME', 'asc');
  PmTableStore.load();


  var remoteFieldsProxy = new Ext.data.HttpProxy({
    url : 'proxyPMTablesFieldList',
    autoSave: true,
    totalProperty: 'totalCount',
    successProperty: 'success',
    idProperty: 'gridIndex',
    root: 'data',
    messageProperty: 'message'
  });

  var readerPmFields = new Ext.data.JsonReader({
    totalProperty : 'totalCount',
    idProperty    : 'index',
    root          : 'data'
    }, pmFields
  );

  //currently we are not using this , but it is here just for complete definition
  var writerPmFields = new Ext.data.JsonWriter({
    writeAllFields: false
  });

  var remotePmFieldsStore = new Ext.data.Store({
    remoteSort : true,
    proxy      : remoteFieldsProxy,
    reader     : readerPmFields,
    writer     : writerPmFields,  // <-- plug a DataWriter into the store just as you would a Reader
    autoSave   : false // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
  });

  // fields array used in second grid
  var fieldsSecond = [
    {name: 'name', mapping : 'name'},
    {name: 'gridIndex', mapping : 'gridIndex'},
    {name: 'fieldType', mapping : 'fieldType'},
    {name: 'label', mapping : 'label'},
    //{name: 'width', mapping : 'width'},
    {name: 'align_label', mapping : 'align_label'},
    {name: 'align', mapping : 'align'}
  ];

  var labelTextField = new Ext.form.TextField ({
    allowBlank: true
  });

  var alignComboBox = new Ext.form.ComboBox ({
    editable : false,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        //id: 0,
        fields: [
            'id',
            'label'
        ],
        data: [['left', "left"], ['center', "center"], ['right', "right"]]
    }),
    valueField: 'id',
    displayField: 'label',
  });

  var alignComboBoxLabel = new Ext.form.ComboBox ({
    editable : false,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        //id: 0,
        fields: [
            'id',
            'label'
        ],
        data: [['left', "left"], ['center', "center"], ['right', "right"]]
    }),
    valueField: 'id',
    displayField: 'label',
    listeners:{

      select: function(obj){

        var row = Ext.getCmp('secondGrid').getSelectionModel().getSelected();
        var selIndex = Ext.getCmp('secondGrid').getStore().indexOfId(row.id);

        row.data['align'] = obj.getValue();
        obj.value = obj.lastSelectionText;
      }
    }
  });

  var widthTextField = new Ext.form.NumberField({
    allowBlank: false,
    allowNegative: false,
    maxValue: 800,
    minValue: 0
  });

  //Column Model shortcut array
  var cols = [
    {header: _("ID_HEADER_NUMBER"), width: 25, sortable: false, dataIndex: "gridIndex", hidden: true, hideable: false},
    {header: _("ID_HEADER_FIELD_NAME"), width: 160, sortable: false, dataIndex: "name"},
    {header: _("ID_HEADER_FIELD_TYPE"), width: 70, sortable: false, dataIndex: "fieldType"}
  ];

  //Column Model shortcut array
  var colsSecond = new Ext.grid.ColumnModel({
        // specify any defaults for each column
        defaults: {
            sortable: false // columns are not sortable by default
        },
        columns: [
          {header: _("ID_HEADER_NUMBER"), width: 25, dataIndex: "gridIndex", hidden: true, hideable: false},
          {header: _("ID_HEADER_FIELD_NAME"), width: 160, dataIndex: "name"},
          {header: _("ID_HEADER_FIELD_TYPE"), width: 70, dataIndex: "fieldType"},
          {
              header: _("ID_HEADER_LABEL"),
              width: 160,
              dataIndex: "label",
              editor: labelTextField,
              renderer: function (value, metaData, record, rowIndex, colIndex, store)
              {
                  var arrayMatch = [];
                  var newValue = value;

                  if ((arrayMatch = /^\*\*(.+)\*\*$/.exec(value))) {
                      newValue = _(arrayMatch[1]);
                  }

                  return newValue;
              }
          },
          //{header: _("ID_HEADER_ALIGN"), width: 60, dataIndex: "align", editor: alignComboBox},
          {header: _("ID_HEADER_ALIGN"), width: 60, dataIndex: "align_label", editor: alignComboBoxLabel}
        ]
    });

  // declare the source Grid
  var firstGrid = new Ext.grid.GridPanel({
      enableDragDrop: true,
      width: 240,
      ddGroup: "secondGridDDGroup",
      ddText: "{0} " + _("ID_SELECTED_FIELD") + "{1}",
      store: remotePmFieldsStore,
      columns: cols,
      stripeRows: true,
      title: _("ID_AVAILABLE_FIELDS")
  });

  var secondGridStore = new Ext.data.JsonStore({
    root            : 'data',
    totalProperty   : 'totalCount',
    fields          : fieldsSecond,
    remoteSort      : false,
    successProperty : 'success'
  });

  // create the destination Grid
  var secondGrid = new Ext.grid.EditorGridPanel({
      id: 'secondGrid',
      enableDragDrop: true,
      ddGroup: "firstGridDDGroup",
      selModel: new Ext.grid.RowSelectionModel({singleSelect: true}),
      store: secondGridStore,
      ddText: "{0} " + _("ID_SELECTED_FIELD") + "{1}",
      clicksToEdit: 1,
      cm: colsSecond,
      sm: new Ext.grid.RowSelectionModel({
        listeners:{
          selectionchange: function(sm,a,b,c){
            if (sm.lastActive !== false) {
              currentIndexRowSelected = sm.lastActive;
            }

          }
        }
      }),
      stripeRows: true,
      title: _("ID_CASES_LIST_FIELDS")
  });

  var inboxPanel = new Ext.Panel({
    title: _("ID_TITLE_INBOX"),
    listeners: {'activate': function() {
        tabIndex = 0;
        sendGridFieldsRequest("todo");
    }}
  });

  var draftPanel = new Ext.Panel({
    title: _("ID_TITLE_DRAFT"),
    listeners: {'activate': function() {
        tabIndex = 1;
        sendGridFieldsRequest("draft");
    }}
  });

  var participatedPanel = new Ext.Panel({
    title: _("ID_TITLE_PARTICIPATED"),
    listeners: {'activate': function() {
        tabIndex = 2;
        sendGridFieldsRequest("sent");
    }}
  });

  var unassignedPanel = new Ext.Panel({
    title: _("ID_TITLE_UNASSIGNED"),
    listeners: {'activate': function() {
        tabIndex = 3;
        sendGridFieldsRequest("unassigned");
    }}
  });

  var pausedPanel = new Ext.Panel({
    title: _("ID_TITLE_PAUSED"),
    listeners: {'activate': function() {
        tabIndex = 4;
        sendGridFieldsRequest("paused");
    }}
  });

  var completedPanel = new Ext.Panel({
    title: _("ID_TITLE_COMPLETED"),
    listeners: {'activate': function() {
        tabIndex = 5;
        sendGridFieldsRequest("completed");
    }}
  });

  var cancelledPanel = new Ext.Panel({
    title: _("ID_TITLE_CANCELLED"),
    listeners: {'activate': function() {
        tabIndex = 6;
        sendGridFieldsRequest("cancelled");
    }}
  });

  var mainPanel = new Ext.Panel({
    title        : '',
    renderTo     : 'alt-panel',
    width        : 750,
    height       : 460,
    layout       : 'hbox',
    layoutConfig : {align : 'stretch'},
    tbar         : new Ext.Toolbar({
      items: [
          _("ID_PM_TABLE"),
          pmTablesDropdown,
          "->",
          new Ext.Action({
              text: _("ID_OPTIONS"),
              iconCls: "button_menu_ext ss_sprite ss_table_gear",
              menu: new Ext.menu.Menu({
                  width: 250,
                  defaults: {
                      iconCls: "silk-add",
                      icon: "/images/ext/default/menu/group-checked.gif"
                  },
                  items: [
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_RESET") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_RESET");
                          }
                      },
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_RESET_ID") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_RESET_ID");
                          }
                      },
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_COMPLETE") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_COMPLETE");
                          }
                      },
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_COMPLETE_ID") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_COMPLETE_ID");
                          }
                      },
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_LABEL_RESET") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_LABEL_RESET");
                          }
                      },
                      {
                          text: "<div style=\"white-space: pre-wrap;\">" + _("ID_CASESLIST_FIELD_LABEL_RESET_ID") + "</div>",
                          handler: function ()
                          {
                              casesListProcessAjax("FIELD_LABEL_RESET_ID");
                          }
                      }
                  ]
              })
          })
      ]
    }),
    items: [
        firstGrid,
        secondGrid
    ],
    bbar: [
        "->",
        {
            text: _("ID_RESET"),
            handler: function ()
            {
                var dataResponse = eval("(" + configDefaultResponseText + ")"); //json

                fieldSet(dataResponse);
            }
        },
        " ",
        {
            text: _("ID_APPLY_CHANGES"),
            handler: function ()
            {
                casesListProcessAjax("FIELD_SAVE");
            }
        }
    ]
  });

var tabs = new Ext.TabPanel({
    renderTo       : 'panel',
    //activeTab      : 0,
    width          : 750,
    items          : [
    inboxPanel,
    draftPanel,
    participatedPanel,
    unassignedPanel,
    pausedPanel
  ]
});

// used to add records to the destination stores

  // Setup Drop Targets
  // This will make sure we only drop to the  view scroller element
  var firstGridDropTargetEl =  firstGrid.getView().scroller.dom;
  var firstGridDropTarget = new Ext.dd.DropTarget(firstGridDropTargetEl, {
       ddGroup    : 'firstGridDDGroup',
       notifyDrop : function(ddSource, e, data){
         var records =  ddSource.dragData.selections;
         Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
         firstGrid.store.add(records);
         firstGrid.store.commitChanges();
         return true
       }
  });




  // This will make sure we only drop to the view scroller element
  var secondGridDropTargetEl = secondGrid.getView().scroller.dom;
  var secondGridDropTarget = new Ext.dd.DropTarget(secondGridDropTargetEl, {

      notifyDrop : function(ddSource, e, data){

        if ( ddSource.ddGroup == 'firstGridDDGroup') {
          var selectedRecord = secondGrid.getSelectionModel().getSelected();
          // to get value of a field in the record
          var valSource = selectedRecord.get('gridIndex');

          var rowTargetId = secondGrid.getView().findRowIndex(e.getTarget());
          var recTarget = secondGrid.getStore().getAt(rowTargetId);
          var valTarget = recTarget.get('gridIndex');

          var newIndex = 0;
          for (i=0; i< secondGrid.store.getCount(); i++) {
            var record = secondGrid.getStore().getAt(i);
            if (record.get('gridIndex') == valSource) {
              record.set('gridIndex',valTarget);
            } else {
              incIndexB = 1;
              isBrecord = 0;
              if ( record.get('gridIndex') == valTarget ) {
                isBrecord = true;
              }
              if ( isBrecord && newIndex == record.get('gridIndex') ) {
                newIndex++;incIndexB = false;
              }
              record.set('gridIndex', newIndex);
              newIndex++;
              if ( isBrecord && incIndexB ) {
                newIndex++;
              }
            }
          }
          secondGrid.store.sort('gridIndex', 'ASC');
          return true;
        };

        var records =  ddSource.dragData.selections;
        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
        secondGrid.store.add(records);

        //reorder fields, putting a secuencial index for all records
        for (i=0; i< secondGrid.store.getCount(); i++) {
          var record = secondGrid.getStore().getAt(i);
          record.set('gridIndex', i );
        }
        secondGrid.store.commitChanges();
        return true
      }
  });
  secondGridDropTarget.addToGroup('secondGridDDGroup');
  secondGridDropTarget.addToGroup('firstGridDDGroup');

});
