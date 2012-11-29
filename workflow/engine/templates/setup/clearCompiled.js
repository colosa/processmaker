Ext.onReady(function() {
  cacheFields = new Ext.form.FieldSet({

    title : _('ID_CLEAR_CACHE'),
    items : [
      {
        xtype      : 'checkbox',
        name       : 'javascriptCache',
        fieldLabel : 'Terms of Use',
        hideLabel  : true,
        id         : 'javascriptCache',
        boxLabel   : _('ID_JAVASCRIPT_CACHE'),
        listeners  : {
          check : enableBtn
        }
      },
      {
        xtype      : 'checkbox',
        name       : 'metadataCache',
        fieldLabel : 'Terms of Use',
        hideLabel  : true,
        id         : 'metadataCache',
        boxLabel   : _('ID_FORMS_METADATA_CACHE'),
        listeners  : {
          check : enableBtn
        }
      },
      {
        xtype      : 'checkbox',
        name       : 'htmlCache',
        fieldLabel : 'Terms of Use',
        hideLabel  : true,
        id         : 'htmlCache',
        boxLabel   : _('ID_FORMS_HTML_CACHE'),
        listeners  : {
          check : enableBtn
        }
      }
    ],
    buttons   : [{
      id      : 'btn_save',
      text    : _('ID_CLEAR'),
      disabled: true,
      handler : clearCache
    }]
  });

  var frm = new Ext.FormPanel( {
    title         : '&nbsp',
    id            : 'frmCache',
    width         : 400,
    labelWidth    : 150,
    labelAlign    : 'right',
    autoScroll    : true,
    bodyStyle     : 'padding:2px',
    waitMsgTarget : true,
    frame         : true,
    defaults : {
      allowBlank : false,
      resizable  : true,
      msgTarget  : 'side',
      align      : 'center'
    },
    items : [ cacheFields ]

  });
  
  frm.render(document.body);
});

function enableBtn() {
  Ext.getCmp('btn_save').enable();
}

function clearCache () {
  Ext.getCmp('frmCache').getForm().submit({
    url     : 'clearCompiledAjax',
    waitMsg : _('ID_SAVING_PROCESS'),
    timeout : 36000,
    success : function(obj, resp) {
      message = '';
      response = Ext.decode(resp.response.responseText);

      if (response.javascript) {
        message += _('ID_JAVASCRIPT_CACHE') + '<br />';
      }
      
      if (response.xmlform) {
        message += _('ID_FORMS_METADATA_CACHE') + '<br />';
      }
      
      if (response.smarty) {
        message += _('ID_FORMS_HTML_CACHE') + '<br />';
      }
      
      PMExt.notify(_('ID_CLEAR_CACHE'), message + _('ID_HAS_BEEN_DELETED'));

      setTimeout(function() {
        window.location.href = window.location.href;
      }, 1500);
    },
    failure : function(obj, resp) {
      if (typeof resp.response.responseText != 'undefined')
        PMExt.error(_('ID_ERROR'), resp.response.responseText);
    }
  });
}

