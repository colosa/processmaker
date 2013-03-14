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
          Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
        }
    }
}
]);

var store;
var cmodel;
var eventsGrid;
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
    if ( !record.data['APP_EVN_LAST_EXECUTION_DATE'] ){
      record.data['APP_EVN_LAST_EXECUTION_DATE'] = ' - ';
    }
    switch (record.data['EVN_ACTION']) {
        case 'EXECUTE_TRIGGER': record.data['EVN_ACTION'] = _('ID_EVENT_TIMER');
            break
        case 'EXECUTE_CONDITIONAL_TRIGGER': record.data['EVN_ACTION'] = _('ID_EVENT_CONDITIONAL');
            break
        case 'SEND_MESSAGE': record.data['EVN_ACTION'] = _('ID_EVENT_MESSAGE');
            break
    }
    for (var i = 0; i < new_text.length -1 ; i++) {
      var chain = new_text[i] +";";
      if (chain.indexOf('width') == -1) {
        style = style + chain;
      }
    }
    metadata.attr = 'ext:qtip="' + data + '" style="'+ style +' white-space: normal; "';
    return data;
  };

    // ComboBox Status
    var comboStatus = new Ext.form.ComboBox({
      width         : 90,
      boxMaxWidth   : 90,
      editable      : false,
      mode          : 'local',
      emptyText		: _('ID_SELECT_STATUS'),
      store         : new Ext.data.ArrayStore({
          fields	: ['id', 'value'],
          data  	: statusValues
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

    // ComboBox Type
    var comboType = new Ext.form.ComboBox({
      width         : 150,
      boxMaxWidth   : 150,
      editable      : false,
      mode          : 'local',
      emptyText		: _('ID_EMPTY_TYPE'),
      store         : new Ext.data.ArrayStore({
          fields: ['id', 'value'],
          data  : typeValues
      }),
      valueField    : 'id',
      displayField  : 'value',
      triggerAction : 'all',
      listeners:{
          scope: this,
          'select': function() {
              filterType = comboType.value;
              store.setBaseParam( 'type', filterType);
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

    actions = _addPluginActions([ {xtype: 'tbfill'}, _('ID_PROCESS'), comboProcess, '-', _('ID_TYPE'), comboType, '-', _('ID_STATUS'), comboStatus]);

    var stepsFields = Ext.data.Record.create([
        {name : 'APP_EVN_ACTION_DATE',          type: 'string'},
        {name : 'APP_EVN_ATTEMPTS',             type: 'string'},
        {name : 'APP_EVN_LAST_EXECUTION_DATE',  type: 'string'},
        {name : 'APP_EVN_STATUS',               type: 'string'},
        {name : 'PRO_TITLE',                    type: 'string'},
        {name : 'EVN_ACTION',                   type: 'string'},
        {name : 'EVN_DESCRIPTION',              type: 'string'},
        {name : 'TAS_TITLE',                    type: 'string'},
        {name : 'APP_TITLE',                    type: 'string'}
  ]);
    
    store = new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy({
            url: 'eventsAjax?request=eventList'
          }),
        remoteSort  : true,
        sortInfo    : stepsFields,
        reader : new Ext.data.JsonReader( {
        root: 'data',
        totalProperty: 'totalCount',
        fields : [
            {name : 'APP_UID'},
            {name : 'DEL_INDEX'},
            {name : 'EVN_UID'},
            {name : 'APP_EVN_ACTION_DATE'},
            {name : 'APP_EVN_ATTEMPTS'},
            {name : 'APP_EVN_LAST_EXECUTION_DATE'},
            {name : 'APP_EVN_STATUS'},
            {name : 'PRO_UID'},
            {name : 'PRO_TITLE'},
            {name : 'EVN_WHEN_OCCURS'},
            {name : 'EVN_ACTION'},
            {name : 'EVN_DESCRIPTION'},
            {name : 'TAS_TITLE'},
            {name : 'APP_TITLE'}
            ]
      })
    });
    store.setDefaultSort('APP_EVN_ACTION_DATE', 'desc');

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50
        },
        columns: [
            {id:'APP_UID', dataIndex: 'APP_UID', hidden:true, hideable:false},
            {header: 'PRO_UID', dataIndex: 'PRO_UID', hidden:true, hideable:false},
            {header: 'EVN_UID', dataIndex: 'EVN_UID', hidden:true, hideable:false},
            {header: _('ID_PROCESS'), dataIndex: 'PRO_TITLE', width: 150, hidden: false,renderer: columnRenderer, sortable: true},
            {header: _('ID_TASKS'), dataIndex: 'TAS_TITLE', width: 150, hidden: false,renderer: columnRenderer, sortable: true},
            {header: _('ID_CASE_TITLE'), dataIndex: 'APP_TITLE', width: 150, hidden: false,renderer: columnRenderer, sortable: true},
            {header: _('ID_EVENT_ACTION_DATE'), dataIndex: 'APP_EVN_ACTION_DATE', width: 90, hidden: false,renderer: columnRenderer, sortable: true},
            {header: _('ID_EVENT_LAST_EXECUTION_DATE'), dataIndex: 'APP_EVN_LAST_EXECUTION_DATE', width: 90, hidden: false ,renderer: columnRenderer, sortable: true},
            {header: _('ID_EVENT_DESCRIPTION'), dataIndex: 'EVN_DESCRIPTION', width: 150, hidden: false,renderer: columnRenderer},
            {header: _('ID_EVENT_ACTION'), dataIndex: 'EVN_ACTION', width: 100, hidden: false,renderer: columnRenderer},
            
            {header: _('ID_DEL_INDEX'), dataIndex: 'DEL_INDEX', width: 40,hidden:true,hideable:false, renderer: columnRenderer},
            {header: _('APP_EVN_ATTEMPTS'), dataIndex: 'APP_EVN_ATTEMPTS', width: 50, hidden: true,hideable:false,renderer: columnRenderer},
            {header: _('ID_STATUS'), dataIndex: 'APP_EVN_STATUS', width: 40, hidden: false,renderer: columnRenderer}
        ]
    });

    bbarpaging = new Ext.PagingToolbar({
      pageSize      : 25,
      store         : store,
      displayInfo   : true,
      displayMsg    : _('ID_GRID_PAGE_DISPLAYING_EVENT_MESSAGE') + '&nbsp; &nbsp; ',
      emptyMsg      : _('ID_GRID_PAGE_NO_EVENT_MESSAGE')
    });

    eventsGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'eventsGrid',
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
        title : _('ID_EVENTS'),
        store: store,
        cm: cmodel,
        tbar: actions,
        bbar: bbarpaging,
        listeners: {
            render: function(){
                this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
            }
        }
    });

      eventsGrid.store.load();

      viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        items: [
           eventsGrid
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