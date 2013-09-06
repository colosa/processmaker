/*
 * @author: Erik A. Ortiz
 * Aug 20th, 2010 
 */

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
      }
      else
    Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
  }
});

var removeOption;
var installOption;
var exportOption;

Ext.Ajax.timeout = 300000;
Ext.onReady(function(){
  
  //Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

  installOption = new Ext.Action({
    text: _('ID_LANG_INSTALL_UPDATE'),
    iconCls: 'silk-add',
    icon: '/images/import.gif',
    handler: function(){
      var w = new Ext.Window({
        title: '',
        width: 420,
        height: 140,
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
            title: _('ID_LAN_UPLOAD_TITLE'),
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
                emptyText: _('ID_LAN_FILE_WATER_LABEL'),
                fieldLabel: _('ID_LAN_FILE'),
                name: 'form[LANGUAGE_FILENAME]',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            }],
            buttons: [{
                text: _('ID_UPLOAD'),
                handler: function(){
                  var uploader = Ext.getCmp('uploader');

                  if(uploader.getForm().isValid()){
                    uploader.getForm().submit({
                      url: 'languages_Import',
                      waitTitle:'&nbsp;',
                      waitMsg: _('ID_UPLOADING_TRANSLATION_FILE'),
                      success: function(o, resp){
                        w.close();
                        infoGrid.store.reload();

                        Ext.MessageBox.show({
                          title: '',
                          width: 500,
                          height: 500,
                          msg: "<pre style='font-size:10px'>"+resp.result.msg+"</pre>",
                          buttons: Ext.MessageBox.OK,
                          animEl: 'mb9',
                          fn: function(){},
                          icon: Ext.MessageBox.INFO
                        });
                      },
                      failure: function(o, resp){
                        w.close();
                        //alert('ERROR "'+resp.result.msg+'"');
                        Ext.MessageBox.show({title: '', msg: resp.result.msg, buttons:
                        Ext.MessageBox.OK, animEl: 'mb9', fn: function(){}, icon:
                        Ext.MessageBox.ERROR});
                        //setTimeout(function(){Ext.MessageBox.hide(); }, 2000);
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
                text: _('ID_CANCEL'),
                handler: function(){
                  w.close();
                }
            }]
          })
        ]/*,
        listeners:{
          show:function() {
            this.loadMask = new Ext.LoadMask(this.body, {
              msg:'Loading. Please wait...'
            });
          }
        }*/
      });
      w.show();
    }
  });

  exportOption = new Ext.Action({
  text: _('ID_EXPORT'),
  iconCls: 'silk-add',
  icon: '/images/export.png',
  handler: function(){
    iGrid = Ext.getCmp('infoGrid');
    var rowSelected = iGrid.getSelectionModel().getSelected();
    if( rowSelected ) {
      location.href = 'languages_Export?LOCALE='+rowSelected.data.LOCALE+'&rand='+Math.random()
    } else {
       Ext.Msg.show({
        title:'',
        msg: _('ID_SELECT_LANGUAGE_FROM_LIST'),
        buttons: Ext.Msg.INFO,
        fn: function(){},
        animEl: 'elId',
        icon: Ext.MessageBox.INFO,
        buttons: Ext.MessageBox.OK
      });
    }
  }
  });


  removeOption = new Ext.Action({
    text: _('ID_DELETE_LANGUAGE'),
    iconCls: 'silk-add',
    icon: '/images/delete-16x16.gif',
    handler: function(){
      iGrid = Ext.getCmp('infoGrid');
      var rowSelected = iGrid.getSelectionModel().getSelected();
      if( rowSelected ) {
        langId      = rowSelected.data.LAN_ID;
        langName    = rowSelected.data.LAN_NAME;
        countryName = rowSelected.data.COUNTRY_NAME;
        locale      = rowSelected.data.LOCALE;

        confirmMsg = _('ID_DELETE_LANGUAGE_CONFIRM');
        confirmMsg = confirmMsg.replace('{0}', locale);
        Ext.Msg.show({
          title:'',
          msg: confirmMsg,
          buttons: Ext.Msg.YESNO,
          fn: function(btn){
            if( btn == 'yes' ) {
              Ext.Ajax.request({
                url: 'language_Ajax',
                success: function(response){
                  infoGrid.store.reload();

                  Ext.Msg.show({
                    title:'',
                    msg: response.responseText,
                    buttons: Ext.Msg.INFO,
                    fn: function(){},
                    animEl: 'elId',
                    icon: Ext.MessageBox.INFO,
                    buttons: Ext.MessageBox.OK
                  });
                },
                failure: function(){},
                params: {
                  'function': 'delete',
                  'LOCALE': locale,
                  'LAN_ID': langId
                }
              });
            }
          },
          animEl: 'elId',
          icon: Ext.MessageBox.QUESTION
        });
      } else {
        Ext.Msg.show({
          title:'',
          msg: _('ID_DELETE_LANGUAGE_WARNING'),
          buttons: Ext.Msg.INFO,
          fn: function(){},
          animEl: 'elId',
          icon: Ext.MessageBox.INFO,
          buttons: Ext.MessageBox.OK
        });
      }
      
    }
  });
  
  var infoGrid = new Ext.grid.GridPanel( {
    /*renderTo: 'list-panel',*/
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    stripeRows : true,
    autoHeight : true,
    autoWidth : true,
    title : '',
    stateful : true,
    stateId : 'gridSetupLanguage',
    enableColumnHide: false,
    enableColumnResize: true,
    enableHdMenu: false,
    collapsible: false,
    animCollapse: false,

    view: new Ext.grid.GroupingView({
        forceFit:true,
        //groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        groupTextTpl: '{text}'
    }),


    columns : [
      {
        dataIndex : 'LAN_FLAG',
        id : 'LAN_FLAG',
        header : '',
        width : 30,
        sortable : false,
        renderer: function (value, p, r){
          //return String.format("<span style='{1}'>{0}</span>", value, 'color:green;' );
          var e = (value == 'international') ? 'png': 'gif';
          return String.format("<img src='/images/flags/{0}.{1}' />", value, e);
        }
      }, {
        dataIndex : 'LAN_ID',
        id : 'LAN_ID',
        header : '',
        width : 35,
        sortable : false,
        hidden: true
      }, {
        dataIndex : 'LOCALE',
        id : 'LOCALE',
        header : _('ID_LAN_LOCALE'),
        width : 60,
        sortable : false
      }, {
        dataIndex : 'LAN_NAME',
        header : _('ID_LAN_LANGUAGE'),
        width : 120,
        sortable : false,
        hidden: true,
        renderer: function (value, p, r){
          if( r.data.DEFAULT == '1' )
            return String.format(
              "{0} <font color=green style='font-size:9px'>({1})</font>",
              value,
              _('ID_LANG_PREDETERMINED')
            );
          else
            return value;
        }
      }, {
        dataIndex : 'COUNTRY_NAME',
        header : _('ID_LAN_COUNTRY'),
        width : 120,
        sortable : false,
        renderer: function (value, p, r){
          if( r.data.LAN_FLAG == 'international' )
            return String.format("<font color=green style='font-size:9px'>({0})</font>", value);
          else
            return value;
        }
      }, {
        dataIndex : 'DATE',
        header : _('ID_LAN_UPDATE_DATE'),
        width : 120,
        sortable : false
      }, {
        dataIndex : 'REV_DATE',
        header : _('ID_LAN_REV_DATE'),
        width : 110,
        sortable : false
      }, {
        dataIndex : 'VERSION',
        header : _('ID_LAN_VERSION'),
        width : 40,
        sortable : false
      }, {
        dataIndex : 'TRANSLATOR',
        header : _('ID_LAN_TRANSLATOR'),
        width : 150,
        sortable : false,
        hidden: false
      }, {
        dataIndex : 'NUM_RECORDS',
        header : _('ID_LAN_NUM_RECORDS'),
        width : 60,
        sortable : false
      }
    ],

    store: new Ext.data.GroupingStore( {
      proxy : new Ext.data.HttpProxy({
        url: 'language_Ajax'
      }),

      reader : new Ext.data.JsonReader( {
        root : 'data',
        fields : [
          {name : 'LAN_FLAG'},
          {name : 'LAN_ID'},
          {name : 'LOCALE'},
          {name : 'LAN_NAME'},
          {name : 'COUNTRY_NAME'},
          {name : 'DATE'},
          {name : 'REV_DATE'},
          {name : 'VERSION'},
          {name : 'TRANSLATOR'},
          {name : 'NUM_RECORDS'}
        ]
      }),
      groupField:'LAN_NAME'

    }),

    tbar:[{
      xtype: 'tbsplit',
      text: _('ID_ACTIONS'),
      menu: [removeOption]
    }, '-', installOption, exportOption]

  });

  infoGrid.store.load({params: {"function":"languagesList"}});

  //////////////////////store.load({params: {"function":"xml"}});

  //infoGrid.render('list-panel');
  //infoGrid.render(document.body);
  //fp.render('form-panel');


  var viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: true,
    items: [
      infoGrid
    ]
  });

});


capitalize = function(s){
  s = s.toLowerCase();
  return s.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
};
