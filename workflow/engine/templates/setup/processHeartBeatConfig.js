Ext.onReady(function() {
  HeartFields = new Ext.form.FieldSet({
   
    title : _('ID_HEARTBEAT_DISPLAY'),
    items : [
      {
        xtype      : 'checkbox',
        checked    : heartBeatChecked,
        name       : 'acceptHB',
        fieldLabel : _('ID_TERMS_USE'),
        hideLabel  : true,
        id         : 'ch_ii',
        style      : 'margin-top:15px',
        boxLabel   : '<b>' + _('ID_ENABLE_HEART_BEAT') + '</b>',
        listeners : {
          check : function(){
            Ext.getCmp('btn_save').enable();
          }
        }

      },
      {
        xtype  : 'box',
        autoEl : { tag  : 'div', 
                   html : '<br />' + _('ID_HEART_BEAT_DETAILS_1') + _('ID_HEART_BEAT_DETAILS_2')
                          + '<br>' + _('ID_SEE') + ' <a href="' + 'http://wiki.processmaker.com/index.php/Heartbeat'
                          + '" target="_blank" align="center">' + _('ID_MORE_INFORMATION') + '</a>.'
                 },
        style : 'margin-left:20px'        
      }
    ],    
    buttons : [{
      id      : 'btn_save',
      text    : _('ID_SAVE'),
      disabled: true,
      handler : saveOption    
    }]
  });
  
  
  var frm = new Ext.FormPanel( {
    title         : '&nbsp',
    id            : 'frmHeart',
    labelWidth    : 150,
    width         : 600,
    labelAlign    : 'right',
    autoScroll    : true,
    bodyStyle     : 'padding:2px',
    waitMsgTarget : true,
    frame         : true,
    
    defaults: {
      allowBlank : false,
      msgTarget  : 'side',
      align      : 'center'
    },
    items : [ HeartFields ]
   
  });
  //render to process-panel
  frm.render(document.body);
});

function saveOption()
{
  Ext.getCmp('btn_save').disable();
  Ext.getCmp('frmHeart').getForm().submit( {  
    url     : 'processHeartBeatAjax?action=saveOption',
    waitMsg : _('ID_SAVING_PROCESS'),
    waitTitle : "",
    timeout : 36000,
    success : function(obj, resp) {
      //nothing to do
      response = Ext.decode(resp.response.responseText);
      if (response.enable) {
        parent.PMExt.notify(_('ID_HEARTBEAT_DISPLAY'), _('ID_HEART_BEAT_ENABLED'));        
      }
      else {
        parent.PMExt.notify(_('ID_HEARTBEAT_DISPLAY'), _('ID_HEART_BEAT_DISABLED'));
      }
    },
    failure : function(obj, resp) {
      Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
    }
  });
} 


