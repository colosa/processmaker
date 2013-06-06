/*
 * @author: Erik A. Ortiz
 * Aug 20th, 2010
 */
var processesGrid;
var store;
var comboCategory;
new Ext.KeyMap(document, {
  key: Ext.EventObject.F5,
  fn: function(keycode, e) {
      if (! e.ctrlKey) {
        if (Ext.isIE)
            e.browserEvent.keyCode = 8;
        e.stopEvent();
        document.location = document.location;
      }
      else
        Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE') );
  }
});


Ext.onReady(function(){
  //Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

  store = new Ext.data.GroupingStore( {
  //var store = new Ext.data.Store( {
    proxy : new Ext.data.HttpProxy({
      url: 'pluginsList'
    }),

    reader : new Ext.data.JsonReader( {
      fields : [
        {name : 'id'},
        {name : 'namespace'},
        {name : 'title'},
        {name : 'className'},
        {name : 'description'},
        {name : 'version'},
        {name : 'setupPage'},
        {name : 'status'},
        {name : 'status_label'},
        {name : 'setup'},

        {name : 'sFile'},
        {name : 'sStatusFile'}
      ]
    })//,
    //sortInfo:{field: 'PRO_TITLE', direction: "ASC"}
    //groupField:'PRO_CATEGORY_LABEL'

  });


  var expander = new Ext.ux.grid.RowExpander({
    tpl : new Ext.Template(
        '<p><b>' + _('ID_DESCRIPTION') + ': </b> {description}</p><br>'
    )
  });

  var selModel = new Ext.grid.RowSelectionModel({
    singleSelect : true
  })

  Grid = new Ext.grid.GridPanel( {
    region: 'center',
    layout: 'fit',
    id: 'processesGrid',
    height:500,
    //autoWidth : true,
    width:'',
    title : '',
    stateful : true,
    stateId : 'gridPluginMain',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    plugins: expander,
    cls : '',
    columnLines: true,
    viewConfig: {
      forceFit:true
    },
    cm: new Ext.grid.ColumnModel({
      defaults: {
          width: 200,
          sortable: true
      },
      columns: [
        expander,
        {id:'id', dataIndex: 'id', hidden:true, hideable:false},
        {header: _('ID_DESCRIPTION'), dataIndex: 'description', width: 100, hidden:true, hideable:false},
        {header: '', dataIndex: 'namespace', width: 100, hidden:true, hideable:false},
        {header: 'classname', dataIndex: 'className', width: 300, hidden:true, hideable:false},
        {header: _('ID_TITLE'), dataIndex: 'title'},
        {header: _('ID_VERSION'), dataIndex: 'version', width: 50, sortable: false},
        {header: _('ID_STATUS'), dataIndex: 'status_label', width: 40, renderer:function(v,p,r){
          color = r.get('status') == '1'? 'green': 'red';
          return String.format("<font color='{0}'>{1}</font>", color, v);
        }},

        {header: '', dataIndex: 'setup' , hidden:true, hideable:false},
        {header: '', dataIndex: 'sFile', hidden:true, hideable:false },
        {header: '', dataIndex: 'sStatusFile', hidden:true, hideable:false}
      ]
    }),

    selModel:selModel,
    store: store,

    tbar:[{
        text   : _('ID_IMPORT'),
        iconCls: 'silk-add',
        icon   : '/images/import.gif',
        handler: importProcess
      },{
        xtype: 'tbseparator'
      },
      {
        id     : 'setup',
        text   : _('ID_CONFIGURE'),
        iconCls: 'silk-add',
        icon   : '/images/options.png',
        handler: configure
      },
      {
        text    : _('ID_STATUS'),
        id      : 'activator',
        icon    : '',
        iconCls : 'silk-add',
        handler : activeDeactive,
        disabled: true
      },{
        text   : _('ID_DELETE'),
        iconCls: 'silk-add',
        icon   : '/images/delete-16x16.gif',
        handler: deletePlugin
      }
    ],
    listeners: {
      render: function(){
        this.loadMask = new Ext.LoadMask(this.body, {msg: _('ID_LOADING_GRID')});
        //this.ownerCt.doLayout();
        Grid.getSelectionModel().on('rowselect', function(){

        var rowSelected = Grid.getSelectionModel().getSelected();
        //alert(rowSelected.data.PRO_STATUS);
        var activator = Ext.getCmp('activator');
        var setup = Ext.getCmp('setup');
        activator.setDisabled(false);

        if( rowSelected.data.status == 1 ){
          activator.setIcon('/images/deactivate.png');
          activator.setText(_('ID_DISABLE'));//activator.setText(TRANSLATIONS.ID_DISABLE);//activator.setText('Deactivate');
        } else {
          activator.setIcon('/images/activate.png');
         activator.setText(_('ID_ENABLE'));//activator.setText(TRANSLATIONS.ID_ENABLE); //activator.setText('Activate');
        }
        //alert(rowSelected.data.setup);
        if( rowSelected.data.setup == 1 ){
          setup.setDisabled(false);
        } else {
          setup.setDisabled(true);
        }

        });
      }
  }

  });

  Grid.store.load({params: {"function":"pluginsList"}});
  //store.load({params: {"function":"xml"}});
  var viewport = new Ext.Viewport({
    layout: 'border',
    autoScroll: true,
    items: [Grid]
  });

  if (typeof(__PLUGIN_ERROR__) !== 'undefined') {
    PMExt.notify(_('ID_PLUGINS'), __PLUGIN_ERROR__);
  }
});


deletePlugin = function(){
  var rowSelected = Grid.getSelectionModel().getSelected();
  if( rowSelected ) {
    namespace = rowSelected.get('namespace');
    status    = rowSelected.get('status');
    if(status == 0) {
      Ext.Msg.confirm(
        _('ID_CONFIRM'), _('ID_MSG_REMOVE_PLUGIN'),
        function(btn, text){
          if ( btn == 'yes' ){
            Ext.MessageBox.show({ msg: _('ID_DELETING_ELEMENTS'), wait:true,waitConfig: {interval:200} });
            Ext.Ajax.request({
              url: 'pluginsRemove',
              success: function(response) {
                Ext.MessageBox.hide();
                Grid.store.reload();
              },
              params: {pluginUid:namespace}
            });
          }
        }
      );
    } else {
      Ext.Msg.show({
        title  : '',
        msg    : _('ID_PLUGIN_CANT_DELETE'),
        buttons: Ext.Msg.INFO,
        fn     : function(){},
        animEl : 'elId',
        icon   : Ext.MessageBox.INFO,
        buttons: Ext.MessageBox.OK
      });
    }
  } else {
    Ext.Msg.show({
      title  : '',
      msg    : _('ID_NO_SELECTION_WARNING'),
      buttons: Ext.Msg.INFO,
      fn     : function(){},
      animEl : 'elId',
      icon   : Ext.MessageBox.INFO,
      buttons: Ext.MessageBox.OK
    });
  }
}

importProcess = function(){
  window.location = 'pluginsImport';
}

browseLibrary = function(){
  window.location = 'processes_Library';
}

function activeDeactive(){
  var rowSelected = Grid.getSelectionModel().getSelected();
  //var rows = Grid.getSelectionModel().getSelections();

  if( rowSelected ) {
    var ids = '';

    var status = rowSelected.get('status');
    var file = rowSelected.get('sFile');

    Ext.Ajax.request({
      url : 'pluginsChange?id='+file+'&status='+status ,
      params : { UIDS : ids },
      method: 'GET',
      success: function ( result, request ) {
        //Ext.MessageBox.alert('Success', 'Data return from the server: '+ result.responseText);
        var site = '';
        if (SYS_SKIN.substring(0,2) == 'ux') {
            site = PROCESSMAKER_URL + '/main?st=admin&s='+parent._NODE_SELECTED;
        } else {
            site = PROCESSMAKER_URL + "/setup/main?s="+parent._NODE_SELECTED;
        }
        parent.parent.location.href = site

        return;

        store.reload();

        plugins = parent.Ext.getCmp('plugins');

        plugins.root.reload();

        var activator = Ext.getCmp('activator');
        activator.setDisabled(true);
        activator.setText('Status');
        activator.setIcon('');
      },
      failure: function ( result, request) {
        Ext.MessageBox.alert('Failed', result.responseText);
      }
    });

    //window.location = 'processes_ChangeStatus?PRO_UID='+rowSelected.data.PRO_UID;
  } else {
     Ext.Msg.show({
      title  : '',
      msg    : _('ID_NO_SELECTION_WARNING'),
      buttons: Ext.Msg.INFO,
      fn     : function(){},
      animEl : 'elId',
      icon   : Ext.MessageBox.INFO,
      buttons: Ext.MessageBox.OK
    });
  }
}

var configure = function(){
  var rowSelected = Grid.getSelectionModel().getSelected();
  //var rows = Grid.getSelectionModel().getSelections();

  if( rowSelected ) {
    file = rowSelected.get('sFile');
    window.location = 'pluginsSetup?id='+file;

  } else {
     Ext.Msg.show({
      title  : '',
      msg    : _('ID_NO_SELECTION_WARNING'),
      buttons: Ext.Msg.INFO,
      fn     : function(){},
      animEl : 'elId',
      icon   : Ext.MessageBox.INFO,
      buttons: Ext.MessageBox.OK
    });
  }
}

capitalize = function(s){
  s = s.toLowerCase();
  return s.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
};
