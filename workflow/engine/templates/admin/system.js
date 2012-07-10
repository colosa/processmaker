Ext.onReady(function(){
  
  var cmbSkins = new Ext.form.ComboBox({
    fieldLabel : _('ID_DEFAULT_SKIN'),
    id         : 'default_skin',
    hiddenName : 'default_skin',
    store         : new Ext.data.ArrayStore({
      fields: ['ID', 'NAME'],
      data : skinsList
    }),
    mode        : 'local',
    emptyText   : _('ID_SELECT'),
    valueField     : 'ID',
    displayField   : 'NAME', 
    selectOnFocus  : true,
    editable       : true,
    triggerAction: 'all',
    allowBlank     : false,
    forceSelection: true,
    listeners:{
      select: function(){
        changeSettings();
      },
      afterrender: function(){
        i = cmbSkins.store.findExact('ID', sysConf.default_skin, 0);
        if (i == -1) return;
        cmbSkins.setValue(cmbSkins.store.getAt(i).data.ID);
        cmbSkins.setRawValue(cmbSkins.store.getAt(i).data.NAME);
      }
    }
  });

  var cmbLang = new Ext.form.ComboBox({
    fieldLabel : _('ID_DEFAULT_LANGUAGE'),
    id         : 'default_lang',
    hiddenName : 'default_lang',
    store         : new Ext.data.ArrayStore({
      fields: ['ID', 'NAME'],
      data : languagesList
    }),
    mode        : 'local',
    emptyText   : _('ID_SELECT'),
    valueField     : 'ID',
    displayField   : 'NAME', 
    selectOnFocus  : true,
    editable       : true,
    triggerAction: 'all',
    forceSelection: true,
    allowBlank     : false,
    listeners:{
      select: function(){
        changeSettings();
      },
      afterrender: function(){
        i = cmbLang.store.findExact('ID', sysConf.default_lang, 0);
        if (i == -1) return;
        cmbLang.setValue(cmbLang.store.getAt(i).data.ID);
        cmbLang.setRawValue(cmbLang.store.getAt(i).data.NAME);
      }
    }
  });

  var cmbTimeZone = new Ext.form.ComboBox({
    fieldLabel : _('ID_TIME_ZONE'),
    hiddenName : 'time_zone',
    store         : new Ext.data.ArrayStore({
      fields: ['ID', 'NAME'],
      data : timeZonesList
    }),
    mode        : 'local',
    emptyText   : _('ID_SELECT'),
    valueField     : 'ID',
    displayField   : 'NAME', 
    selectOnFocus  : true,
    editable       : true,
    triggerAction: 'all',
    forceSelection : true,
    allowBlank     : false,
    listeners:{
      select: function(){
        changeSettings();
      }
    }
  });

  cmbTimeZone.setValue(sysConf.time_zone);

  saveButton = new Ext.Action({
    text : _('ID_SAVE_SETTINGS'),
    disabled : true,
    handler : saveSettings
  });

  xfieldsUp = new Ext.form.FieldSet({
    title: _('ID_SYSTEM_SETTINGS'),
    items : [
      cmbTimeZone,
      {
        xtype: 'numberfield',
        id        : 'memory_limit',
        name      : 'memory_limit',
        fieldLabel: _('ID_MEMORY_LIMIT'),
        allowBlank: false,
        value: sysConf.memory_limit,
        listeners:{
          change: function(){
            changeSettings();
          }
        }
      }
    ]
  });

  xfieldsBelow = new Ext.form.FieldSet({
    title: '',
    items : [
      cmbSkins,
      cmbLang,
      {
        xtype: 'panel',
        anchor: '100%',
        bodyStyle:'padding:5px',
        frame: true,
        height: 'auto',
        html:'The language that is set by default applies directly when the domain is entered '+
            ' (eg.- http://127.0.0.1:8081), the system reads the configuration and redirects '+
            '  the link to the selected language (eg.- http://127.0.0.1:8081/sys/en/classic/login/login),'+
            ' it should be emphasized that this configuration is for all environments.'
      }
    ]
  });

  var frm = new Ext.FormPanel({
    title: '&nbsp',
    id:'frm',
    labelWidth: 170,
    width:460,
    labelAlign:'right',
    autoScroll: true,
    bodyStyle:'padding:5px',
    waitMsgTarget : true,
    frame: true,
    
    defaults: {
      allowBlank: false,
      msgTarget: 'side',
      align:'center'
    },
    items:[ xfieldsUp, xfieldsBelow ],
    buttons : [saveButton]
   
  });
  //render to process-panel
  frm.render(document.body);

}); //end onready()

function saveSettings() 
{
  Ext.getCmp('frm').getForm().submit( {  
    url : '../adminProxy/saveSystemConf',
    waitMsg : _('ID_SAVING_PROCESS'),
    timeout : 36000,
    success : function(obj, resp) {
      //nothing to do
      response = Ext.decode(resp.response.responseText);
      parent.PMExt.notify(_('ID_INFO'),_('ID_SAVED_SUCCESSFULLY'));

      if(response.restart) {
        PMExt.confirm(_('ID_CONFIRM'), _('ID_SYSTEM_REDIRECT_CONFIRM'), function(){
          if (typeof window.parent.parent != 'undefined')
            window.parent.parent.location.href = response.url;
          if (typeof window.parent != 'undefined')
            window.parent.location.href = response.url;
          else
            window.location.href = response.url;
        });
      }
      else
        saveButton.disable();
    },
    failure: function(obj, resp) {
      PMExt.error( _('ID_ERROR'), resp.result.message);
    }    
  });
}

changeSettings = function()
{
  saveButton.enable();
}
