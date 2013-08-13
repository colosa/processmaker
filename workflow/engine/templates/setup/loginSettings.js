Ext.onReady(function(){
  
  var cmbLanguages = new Ext.form.ComboBox({
      fieldLabel : _('ID_DEFAULT_LANGUAGE'),
      hiddenName : 'lang',
      store : new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy( {
          url : 'loginSettingsAjax',
          method : 'POST'
        }),
        baseParams : {request : 'getLangList'},
        reader : new Ext.data.JsonReader( {
          root : 'rows',
          fields : [ {name : 'LAN_ID'}, {name : 'LAN_NAME'} ]
        })
      }),
      valueField     : 'LAN_ID',
      displayField   : 'LAN_NAME', 
      emptyText      : _('ID_SELECT'),
      selectOnFocus  : true,
      editable       : false,
      allowBlank     : false,
      listeners:{
        select: function(){
          changeSettings();
        }
      }
    });
  
  cmbLanguages.store.on('load',function(){ cmbLanguages.setValue ( currentLang ) });
  cmbLanguages.store.load();
  saveButton = new Ext.Action({
    text : _('ID_SAVE_SETTINGS'),
    disabled : true,
    handler : saveSettings
  });
  
  loginFields = new Ext.form.FieldSet({
    title: _('ID_LOGIN_SETTINGS'),
    items : [
      cmbLanguages,
      {
        name: 'forgotPasswd',
        xtype: 'checkbox',
        checked: forgotPasswd,
        fieldLabel: _('ID_ENABLE_FOTGOT_PASSWORD'),
        listeners:{
          check:function(){
            changeSettings();
          }
        }
      },
      {
        name: 'virtualKeyboad',
        xtype: 'checkbox',
        checked: virtualKeyboad,
        fieldLabel: _('ID_ENABLE_VIRTUAL_KEYBOARD'),
        listeners:{
          check:function(){
            changeSettings();
          }
        }
      },
      {
        xtype: 'panel',
        anchor: '100%',
        bodyStyle:'padding:5px',
        frame: true,
        height: 'auto',
        html: _('ID_MESSAGE_LOGIN')
      }
    ],
    buttons : [saveButton]
  });

  
  var frm = new Ext.FormPanel({
    title: '&nbsp',
    id:'frm',
    labelWidth: 150,
    width:460,
    labelAlign:'right',
    autoScroll: true,
    bodyStyle:'padding:2px',
    waitMsgTarget : true,
    frame: true,
    
    defaults: {
      allowBlank: false,
      msgTarget: 'side',
      align:'center'
    },
    items:[ loginFields ]
   
  });
  //render to process-panel
  frm.render(document.body);

}); //end onready()

function saveSettings() 
{
  Ext.getCmp('frm').getForm().submit( {  
    url : 'loginSettingsAjax?request=saveSettings',
    waitMsg : _('ID_SAVING_PROCESS'),
    waitTitle : "",
    timeout : 36000,
    success : function(obj, resp) {
      //nothing to do
      response = Ext.decode(resp.response.responseText);
      parent.PMExt.notify(_('ID_INFO'),_('ID_SAVED_SUCCESSFULLY'));
      saveButton.disable();
    },
    failure: function(obj, resp) {
      Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
    }    
  });
}

changeSettings = function()
{
  saveButton.enable();
}
