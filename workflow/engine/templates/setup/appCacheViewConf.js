Ext.onReady(function() {

  Ext.QuickTips.init();
  // turn on validation errors beside the field globally
  Ext.form.Field.prototype.msgTarget = 'side';
  var bd = Ext.getBody();

  // Store
  var store = new Ext.data.Store( {
    proxy: new Ext.data.HttpProxy({
      url: 'appCacheViewAjax',
      method: 'POST'
    }),    
    baseParams : { request : 'info'},
    reader : new Ext.data.JsonReader( {
      root : 'info',
      fields : [ {name : 'name'}, {name : 'value'} ]
    })
  });

  // create the Grid
  var infoGrid = new Ext.grid.GridPanel( {
    store : store,
    columns : [{
        id : 'name',
        header : '',
        width : 210,
        sortable : false,
        dataIndex : 'name'
      }, 
      {
        header : '',
        width : 190,
        sortable : false,
        dataIndex : 'value'
      }
      ],
      stripeRows : true,
      autoHeight : true,
      width : 400,
      title : _('ID_CACHE_TITLE_INFO'), // 'Workflow Applications Cache Info',
      // config options for stateful behavior
      stateful : true,
      stateId : 'gridAppCacheView',
      enableColumnHide: false,
      enableColumnResize: false,
      enableHdMenu: false
    });

    // render the grid to the specified div in the page
    infoGrid.render('info-panel');

    
    var fsf = new Ext.FormPanel( {
      labelWidth : 105, // label settings here cascade unless overridden
      url : '',
      frame : true,
      title : ' ',
      width : 400,
      items : [ ],
      buttons : [{
        text : _('ID_CACHE_BTN_BUILD'), // 'Build Cache',
        handler : function() {
          Ext.Msg.show ({ msg : _('ID_PROCESSING'), wait:true,waitConfig: {interval:400} });
          Ext.Ajax.request({
            url: 'appCacheViewAjax',
            success: function(response) {
              store.reload();
              Ext.MessageBox.hide();  
              res = Ext.decode ( response.responseText );            
              Ext.Msg.alert ( '', res.msg );
                
            },
            failure : function(response) {
              Ext.Msg.hide();              
              Ext.Msg.alert ( _('ID_ERROR'), response.responseText );
            },
            params: {request: 'build', lang: 'en' },
            waitMsg : _('ID_CACHE_BUILDING'), // 'Building Workflow Application Cache...',
            timeout : 1000*60*30 //30 mins
          });
        }
      }]  
    });
    
    var txtUser = {
      id   : 'txtUser',
      xtype: 'textfield',
      fieldLabel: _('ID_CACHE_USER'), // 'User',
      disabled: false,
      name: 'user',
      allowBlank: false
    };

    var txtHost = {
      id   : 'txtHost',
      xtype: 'textfield',
      fieldLabel: _('ID_CACHE_HOST'), // 'Host',
      disabled: false,
      name: 'host',
      allowBlank: false
    };
    
    var txtPasswd = {
      id   : 'txtPasswd',
      inputType: 'password',
      xtype:'textfield',
      fieldLabel: _('ID_CACHE_PASSWORD'), // 'Password',
      disabled: false,
      hidden: false,
      value: ''
    }
    
    fieldsetRoot = {
      xtype : 'fieldset',
      title : _('ID_CACHE_SUBTITLE_SETUP_DB'), // 'Setup MySql Root Password',
      collapsible : true,
      collapsed: true,
      autoHeight  : true,
      defaults    : { width : 170 },
      defaultType : 'textfield',
      items   : [txtHost, txtUser, txtPasswd ],
      buttons : [{
        text : _('ID_CACHE_BTN_SETUP_PASSWRD'), // 'Setup Password',
        handler : function() {
          if (!fsf.getForm().isValid()) {
            return;
          }
  
          Ext.Msg.show ({ msg : _('ID_PROCESSING'), wait:true,waitConfig: {interval:400} });
          Ext.Ajax.request({
            url: 'appCacheViewAjax',
            success: function(response) {
              store.reload();
              Ext.MessageBox.hide();              
              Ext.Msg.alert ( '', response.responseText );
            },
            failure : function(response) {
              Ext.Msg.hide();              
              Ext.Msg.alert ( _('ID_ERROR'), response.responseText );
            },
            params: { request: 'recreate-root', lang: 'en', host: Ext.getCmp('txtHost').getValue(), user: Ext.getCmp('txtUser').getValue(), password: Ext.getCmp('txtPasswd').getValue() },
            // timeout : 1000
            // 30 mins
            timeout : 1000*60*30 //30 mins
          });
        }
      }]      
    }

    fsf.add(fieldsetRoot);
    fsf.render(document.getElementById('main-panel'));

    //store.load(); instead call standard proxy we are calling ajax request, because we need to catch any error 
    Ext.Ajax.request({
      url: 'appCacheViewAjax',
      success: function(response) {
        myData = Ext.decode ( response.responseText );
        store.loadData(myData);  
        if ( myData.error ) {
          Warning( _('ID_ERROR'), myData.errorMsg );
      	}
      },
      failure : function(response) {
        Ext.Msg.alert ( _('ID_ERROR'), response.responseText );
      },
      params: {request: 'info' }
    });
  
  });  //ExtReady

var Warning = function( msgTitle, msgError ) {
  tplEl = Ext.get ('errorMsg');

  tplText = '<div style="font-size:12px; border: 1px solid #FF0000; background-color:#FFAAAA; display:block; padding:10px; color:#404000;"><b>' + msgTitle + ': </b>' + msgError + '</div>';
  tplEl.update ( tplText );

}
