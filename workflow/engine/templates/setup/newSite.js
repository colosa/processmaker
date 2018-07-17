/*
 * @author krlos P.C <carlos@colosa.com>
 * Jan 15th, 2011
 */

Ext.Ajax.timeout = 300000;

Ext.onReady(function(){

  var fieldNameWS=new Ext.form.TextField({
  id: 'NW_TITLE',
  fieldLabel: _('ID_NAME'),
  xtype:'textfield',
  value:'sample',
  width: 200,
  autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '30'},
  allowBlank: false,
  listeners: {
              'render': function(c) {
                c.getEl().on('keyup', function() {
                  namews=formNewSite.getForm().findField('NW_TITLE').getValue();
                  formNewSite.getForm().findField('AO_DB_WF').setValue('wf_'+namews);
                  formNewSite.getForm().findField('AO_DB_WF').setValue('wf_'+namews);
                  formNewSite.getForm().findField('AO_DB_WF').setValue('wf_'+namews);
                }, c);
              }
            }
  });
  nameWS = new Ext.form.FieldSet({
    title: _('ID_NEW_WORKSPACE'),
    items: [
      fieldNameWS
    ]
  });
  dbOptionsWS = new Ext.form.FieldSet({
    title: _('ID_DATABASE_OPTIONS'),
    items: [
      {
        id: 'AO_DB_WF',
        fieldLabel: _('ID_WORKFLOW_DATABASE'),
        xtype:'textfield',
        value:'wf_sample',
        width: 200,
        regex: /^\w+$/,
        autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '13'},
        allowBlank: false,
        msgTarget: 'under',
        validator: function(v) {
          var valueInputField= /^\w+$/.test(v)?true:"Invalid Workflow Database";
          if (valueInputField==true) {
            Ext.getCmp('_idTest').enable();
          }else{
            Ext.getCmp('_idTest').disable();
          }
          return valueInputField;
        }
      },
      /*{
        id: 'AO_DB_RB',
        fieldLabel: _('ID_RBAC_DATABASE'),
        xtype:'textfield',
        value:'rb_sample',
        width: 200,
        autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '13'},
        allowBlank: false
      },
      {
        id: 'AO_DB_RP',
        fieldLabel: _('ID_REPORT_DATABASE'),
        xtype:'textfield',
        value:'rp_sample',
        width: 200,
        autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '13'},
        allowBlank: false
      },*/
      {
            xtype: 'checkbox',
            fieldLabel: _('ID_DROP_DATABASE_EXISTS'),
            name: 'AO_DB_DROP',
            id: 'id-active'
       }
     ]
  });

  wspaceAdmWS = new Ext.form.FieldSet({
    title: _('ID_WORKSPACE_ADMINISTRATOR'),
    items: [
      {
        id: 'NW_USERNAME',
        fieldLabel: _('ID_USERNAME'),
        xtype:'textfield',
        value:'admin',
        width: 200,
        allowBlank: false
      },
      {
        id: 'NW_PASSWORD',
        fieldLabel: _('ID_PASSWORD_ADMIN'),
        xtype:'textfield',
        inputType:'password',
        value:'admin',
        width: 200,
        allowBlank: false,
          validator: function (v) {
              v = v.trim();
              return !/^\s+$/.test(v);
          }
      },
      {
        id: 'NW_PASSWORD2',
        fieldLabel: _('ID_PASSWORD_ADMIN_RETYPE'),
        xtype:'textfield',
        inputType:'password',
        value:'admin',
        width: 200,
        allowBlank: false,
          validator: function (v) {
              v = v.trim();
              return !/^\s+$/.test(v);
          }
      }
      ]
  });


  formNewSite = new Ext.FormPanel({
    id:'formNewSite',
    labelWidth: 250,
    labelAlign:'right',
    autoScroll: true,
    fileUpload: true,
    width:800,
    bodyStyle:'padding:10px',
    waitMsgTarget : true,
    frame: true,
    defaults: {
      anchor: '100%',
      allowBlank: false,
      resizable: true,
      msgTarget: 'side',
      align:'center'
    },
    items:[
    nameWS,
    dbOptionsWS,
    wspaceAdmWS
      ],
    buttons: [
      {
        text: _('ID_RESET'),
        handler: resetfields

      },
      {
        id: '_idTest',
        text: _('ID_TEST'),
        handler: TestSite
      }
    ]

  });

    formNewSite.render(document.body);

 });
 function resetfields(){
    formNewSite.getForm().reset();
    Ext.getCmp('_idTest').enable();
 }
 function TestSite(){
 formNewSite.getForm().submit({
                            url: '../newSiteProxy/testingNW',
                            params: {
                            action : 'test'
                            },
                            waitMsg : _('ID_NEW_SITE_TESTING'),
                            waitTitle : "&nbsp;",
                            success: function(f,a){
                             nwTitle    =formNewSite.getForm().findField('NW_TITLE').getValue();
                             aoDbWf     =formNewSite.getForm().findField('AO_DB_WF').getValue();
                             aoDbRb     =aoDbWf;
                             aoDbRp     =aoDbWf;
                             nwUsername =formNewSite.getForm().findField('NW_USERNAME').getValue();
                             nwPassword = formNewSite.getForm().findField('NW_PASSWORD').getValue().trim();
                             nwPassword2 = formNewSite.getForm().findField('NW_PASSWORD2').getValue().trim();
                             aoDbDrop=formNewSite.getForm().findField('AO_DB_DROP').getValue();
                             createNW(nwTitle, aoDbWf, aoDbRb, aoDbRp, nwUsername, nwPassword, nwPassword2);
                            },
                            failure: function(f,a){
                                if (a.failureType === Ext.form.Action.CONNECT_FAILURE){
                                    Ext.Msg.alert(_('ID_FAILURE'), _('ID_SERVER_REPORTED') + ':' + a.response.status+' '+a.response.statusText);
                                }
                                if (a.failureType === Ext.form.Action.SERVER_INVALID){
                                    var text = JSON.parse(a.response.responseText);
                                    if (typeof(text.message) != 'undefined') {
                                        Ext.Msg.alert(_('ID_ERROR'), _('ID_MYSQL_ERROR', text.message));
                                    } else {
                                        Ext.Msg.alert(_('ID_WARNING'), _('NEW_SITE_NOT_AVAILABLE'));
                                    }
                                }
                            }
                        });
 }

  function createNW(nwTitle, aoDbWf, aoDbRb, aoDbRp, nwUsername, nwPassword, nwPassword2){
    PMExt.confirm(_('ID_CONFIRM'), _('NEW_SITE_CONFIRM_TO_CREATE'), function(){
    var loadMask = new Ext.LoadMask(document.body, {msg : _('ID_SITE_CREATING')});
    var oParams = {
        action : 'create',
        NW_TITLE : nwTitle,
        AO_DB_WF : aoDbWf,
        AO_DB_RB : aoDbRb,
        AO_DB_RP : aoDbRp,
        NW_USERNAME : nwUsername,
        NW_PASSWORD : nwPassword,
        NW_PASSWORD2 : nwPassword2
    };
    if(aoDbDrop){
        oParams.AO_DB_DROP = 'On';
    }    
    loadMask.show();
     Ext.Ajax.request({
      url: '../newSiteProxy/testingNW',
      params: oParams,
      method: 'POST',
      success: function ( result, request ) {
      loadMask.hide();
      var data = Ext.util.JSON.decode(result.responseText);
      if( data.success ) {
        PMExt.confirm(_('ID_CONFIRM'), _('NEW_SITE_SUCCESS') +" "+nwTitle+"<br/>"+ _('NEW_SITE_SUCCESS_CONFIRM')+"<br/>"+ _('NEW_SITE_SUCCESS_CONFIRMNOTE'), function(){
         nwTitle = formNewSite.getForm().findField('NW_TITLE').getValue();
         if (typeof window.parent.parent.parent != 'undefined') {
           parent.parent.parent.window.location = "/sys" + nwTitle + "/" + SYS_LANG + "/" + SYS_SKIN + "/login/login";
         }
         else {
           parent.parent.window.location = "/sys" + nwTitle + "/" + SYS_LANG + "/" + SYS_SKIN + "/login/login";
         }
       });
      } else {
       PMExt.error(_('ID_ERROR'), data.msg);
      }
      },
      failure: function ( result, request) {
       Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
      }
     });
    });
   }
