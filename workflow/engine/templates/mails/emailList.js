/*
 * @author: Marco Antonio
 * Agos 17st, 2012
 */
new Ext.KeyMap(document, [{
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
        } else {
          Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
        }
    }
}
]);

var store;
var cmodel;
var emailsGrid;
var actions;
var filterStatus = '';

Ext.onReady(function(){
    Ext.QuickTips.init();
    var resultTpl = new Ext.XTemplate(
      '<tpl for="."><div class="x-combo-list-item" style="white-space:normal !important;word-wrap: break-word;">',
          '<span> {APP_PRO_TITLE}</span>',
      '</div></tpl>'
    );
    
    var columnRenderer = function(data, metadata, record, rowIndex,columnIndex, store) {
    var new_text = metadata.style.split(';');
    var style = '';
    for (var i = 0; i < new_text.length -1 ; i++) {
      var chain = new_text[i] +";";
      if (chain.indexOf('width') == -1) {
        style = style + chain;
      }
    }
    metadata.attr = 'ext:qtip="' + data + '" style="'+ style +' white-space: normal; "';
    return data;
  };

    var dateFrom = new Ext.form.DateField({
        id:'dateFrom',
        format: 'Y-m-d',
        width: 120,
        value: ''
    });

    var dateTo = new Ext.form.DateField({
        id:'dateTo',
        format: 'Y-m-d',
        width: 120,
        value: ''
    });

    // ComboBox Status
    var comboStatus = new Ext.form.ComboBox({
      width         : 90,
      boxMaxWidth   : 90,
      editable      : false,
      mode          : 'local',
      emptyText: _('ID_SELECT_STATUS'),
      store         : new Ext.data.ArrayStore({
          fields: ['id', 'value'],
          data  : statusValues
      }),
      valueField    : 'id',
      displayField  : 'value',
      triggerAction : 'all',
      listeners:{
          scope: this,
          'select': function() {
              filterStatus = comboStatus.value;
              store.setBaseParam( 'status', filterStatus);
              store.setBaseParam( 'start', 0);
              store.setBaseParam( 'limit', 25);
              store.load();
          }
      },
      iconCls: 'no-icon'
    });

    var comboProcess = new Ext.form.ComboBox({
        width         : 200,
        boxMaxWidth   : 200,
        editable      : true,
        displayField  : 'APP_PRO_TITLE',
        valueField    : 'PRO_UID',
        forceSelection: false,
        emptyText: _('ID_EMPTY_PROCESSES'),
        selectOnFocus: true,
        tpl: resultTpl,

        typeAhead: true,
        mode: 'local',
        autocomplete: true,
        triggerAction: 'all',

        store         : new Ext.data.ArrayStore({
            fields : ['PRO_UID','APP_PRO_TITLE'],
            data   : processValues
        }),
        listeners:{
          scope: this,
          'select': function() {
            filterProcess = comboProcess.value;

            store.setBaseParam('process', filterProcess);
            store.setBaseParam( 'start', 0);
            store.setBaseParam( 'limit', 25);
            store.load();
        }},
        iconCls: 'no-icon'
    });

    actions = _addPluginActions([ {xtype: 'tbfill'}, _('ID_PROCESS'), comboProcess, '-', /*_('ID_TYPE'), comboType, '-',*/ _('ID_STATUS'), comboStatus, _('ID_DELEGATE_DATE_FROM'),
      dateFrom,
      ' ',
      _('ID_TO'),
      dateTo,
      new Ext.Button ({
        text: _('ID_FILTER_BY_DELEGATED_DATE'),
        handler: function(){
          store.setBaseParam('dateFrom', dateFrom.getValue());
          store.setBaseParam('dateTo', dateTo.getValue());
          store.load({params:{ start : 0 , limit : 25 }});
        }
      })
    ]);

    var stepsFields = Ext.data.Record.create([
        {name : 'APP_MSG_TYPE',     type: 'string'},
        {name : 'APP_MSG_FROM',     type: 'string'},
        {name : 'APP_MSG_TO',       type: 'string'},
        {name : 'APP_MSG_DATE',     type: 'string'},
        {name : 'APP_MSG_STATUS',   type: 'string'}
        
  ]);
    
    store = new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy({
            url: 'emailsAjax?request=MessageList'
          }),
        remoteSort  : true,
        sortInfo    : stepsFields,
        reader : new Ext.data.JsonReader( {
        root: 'data',
        totalProperty: 'totalCount',
        fields : [
            {name : 'APP_MSG_UID'},
            {name : 'APP_UID'},
            {name : 'DEL_INDEX'},
            {name : 'PRO_UID'},
            {name : 'TAS_UID'},
            {name : 'PRO_TITLE'},
            {name : 'TAS_TITLE'},
            {name : 'APP_MSG_TYPE'},
            {name : 'APP_MSG_SUBJECT'},
            {name : 'APP_MSG_FROM'},
            {name : 'APP_MSG_TO'},
            {name : 'APP_MSG_STATUS'},
            {name : 'APP_MSG_DATE'},
            {name : 'APP_MSG_SEND_DATE'},
            {name : 'APP_MSG_BODY'}
        ]
      })
    });
    store.setDefaultSort('APP_MSG_DATE', 'desc');

    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><hr/><b>'+_('ID_PREVIEW')+':</b><br/>'+
            '<center>'+
            '<div style = "display: table; width: 100%; margin: 5px;">'+
                '<div style="float: left; width: 10%;">&nbsp;</div>'+
                '<div style="float: left; width: 80%; border: 0px dashed #660000;"> {APP_MSG_BODY}</div>'+
                '<div style="float: left; width: 10%;">&nbsp;</div>'+
            '</div>'+
            '</center>'+
            '</p><hr/><br>'
        )
    });

    var statusValuesGrid = statusValues;
    statusValuesGrid.splice( 0, 1 );
    cmodel = new Ext.grid.ColumnModel({
        viewConfig: {
            forceFit:true,
            cls:"x-grid-empty",
            emptyText: _('ID_NO_RECORDS_FOUND')
        },
        defaults: {
            width: 50
        },
        columns: [
            expander,
            {id:'APP_MSG_UID', dataIndex: 'APP_MSG_UID', hidden:true, hideable:false},
            {header: 'APP_UID', dataIndex: 'APP_UID', hidden:true, hideable:false},
            {header: 'DEL_INDEX', dataIndex: 'DEL_INDEX', hidden:true, hideable:false},
            {header: 'PRO_UID', dataIndex: 'PRO_UID', hidden:true, hideable:false, sortable: false},
            {header: 'TAS_UID', dataIndex: 'TAS_UID', hidden:true, hideable:false, sortable: false},
            {header: _('ID_PROCESS'), dataIndex: 'PRO_TITLE', width: 100, hidden: false,renderer: columnRenderer, sortable: false},
            {header: _('ID_TASK'), dataIndex: 'TAS_TITLE', width: 100, hidden: false,renderer: columnRenderer, sortable: false},
            {header: _('ID_TYPE'), dataIndex: 'APP_MSG_TYPE', width: 50, hidden: false,renderer: columnRenderer, sortable: true},
            {header: _('ID_DATE_LABEL'), dataIndex: 'APP_MSG_DATE', width: 80,hidden:false, renderer: columnRenderer, sortable: true},
            //{header: _('ID_DERIVED'), dataIndex: 'APP_MSG_SEND_DATE', width: 80,hidden:false,hideable:false, renderer: columnRenderer, sortable: true},
            {header: _('ID_SUBJECT'), dataIndex: 'APP_MSG_SUBJECT', width: 80,hidden:false, renderer: columnRenderer, sortable: true},
            {header: _('ID_FROM'), dataIndex: 'APP_MSG_FROM', width: 80,hidden:false,renderer: columnRenderer, sortable: true},
            {header: _('ID_TO'), dataIndex: 'APP_MSG_TO', width: 80,hidden:false,renderer: columnRenderer, sortable: true},
            //{header: _('ID_STATUS'), dataIndex: 'APP_MSG_STATUS', width: 50,hidden:false,hideable:false, renderer: columnRenderer, sortable: true}
            {
                header:  _('ID_STATUS'),
                dataIndex: 'APP_MSG_STATUS',
                width: 50,
                //hidden:false,
                //hideable:false,
                renderer: columnRenderer,
                //sortable: true,
                editor: new Ext.form.ComboBox({
                  listClass: 'x-combo-list-small',
                  mode: 'local',
                  displayField:'value',
                  lazyRender: true,
                  triggerAction: 'all',
                  valueField:'id',
                  editable: false,
                  store: new Ext.data.ArrayStore({
                        fields: ['id', 'value'],
                        data  : statusValuesGrid
                  }),
                  listeners: {
                    select: function(a, b) {
                      var row = emailsGrid.getSelectionModel().getSelected();
                      Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
                        Ext.Ajax.request({
                            url : 'emailsAjax' ,
                            params : {
                                request : 'updateStatusMessage',
                                APP_MSG_UID: row.data.APP_MSG_UID,
                                APP_MSG_STATUS: this.value
                            },
                            success: function ( result, request ) {
                                Ext.MessageBox.hide();
                            },
                            failure: function ( result, request) {
                                if (typeof(result.responseText) != 'undefined') {
                                    Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
                                }
                            }
                        });
                    }
                  }
                })
            }
        ]
    });

    smodel = new Ext.grid.RowSelectionModel({
        singleSelect: true
    });

    bbarpaging = new Ext.PagingToolbar({
      pageSize      : 25,
      store         : store,
      displayInfo   : true,
      displayMsg    : _('ID_GRID_PAGE_DISPLAYING_EMAIL_MESSAGE') + '&nbsp; &nbsp; ',
      emptyMsg      : _('ID_GRID_PAGE_NO_EMAIL_MESSAGE')
    });

    emailsGrid = new Ext.grid.EditorGridPanel({
        region: 'center',
        layout: 'fit',
        id: 'emailsGrid',
        height:100,
        autoWidth : true,
        stateful : true,
        stateId : 'grid',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        columnLines: false,
        viewConfig: {
            forceFit:true
        },
        clicksToEdit: 1,
        title : _('ID_EMAILS'),
        store: store,
        cm: cmodel,
        sm: smodel,
        tbar: actions,
        bbar: bbarpaging,
        plugins: expander,
        listeners: {
            render: function(){
                this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
            }
        }
    });

      emailsGrid.store.load();

      viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        items: [
           emailsGrid
        ]
      });
    });

var _addPluginActions = function(defaultactions) {
  try {
    if (Ext.isArray(_pluginactions)) {
      if (_pluginactions.length > 0) {
        var positionToInsert = _tbfillPosition(defaultactions);
        var leftactions = defaultactions.slice(0, positionToInsert);
        var rightactions = defaultactions.slice(positionToInsert, defaultactions.length - 1);
        return leftactions.concat(_pluginactions.concat(rightactions));
      }
      else {
        return defaultactions;
      }
    }
    else {
      return defaultactions;
    }
  }
  catch (error) {
    return defaultactions;
  }
};

var _tbfillPosition = function(actions) {
  try {
    for (var i = 0; i < actions.length; i++) {
      if (Ext.isObject(actions[i])) {
        if (actions[i].xtype == 'tbfill') {
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