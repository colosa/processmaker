/*
 * @author Carlos P.C <carlos@colosa.com, pckrlos@gmail.com>
 * Oct 20th, 2011
 */

Ext.onReady(function(){

  var txtSourceId=new Ext.form.TextField({
  id: 'AUTH_SOURCE_UID',
  fieldLabel: 'krlos',
  xtype:'textfield',
  value: sUID,
  width: 200,
  hideLabel: true,
  hidden: true
  });

  var txtName=new Ext.form.TextField({
  id: 'AUTH_SOURCE_NAME',
  fieldLabel: _('ID_NAME'),
  xtype:'textfield',
  value:'',
  width: 200,
  autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '50'},
  allowBlank: false,
  listeners: {
              'render': function(c) {
                c.getEl().on('keyup', function() {
                }, c);
              }
            }
  });

  var my_values = [['ldap'],['Active Directory']];
  var cboxType = new Ext.form.ComboBox({
      fieldLabel: _('ID_TYPE'),
      hiddenName: 'LDAP_TYPE',
      store: new Ext.data.SimpleStore({
          fields: ['ldap','ad'],
          data : my_values
      }),
      displayField: 'ldap',
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:'Choose an option...',
      selectOnFocus:true,
      listeners:{
       select: function(c,d,i){
          if(i){
            formAuthSourceE.getForm().findField('AUTH_SOURCE_IDENTIFIER_FOR_USER').setValue('samaccountname');
          } else {
            formAuthSourceE.getForm().findField('AUTH_SOURCE_IDENTIFIER_FOR_USER').setValue('uid');
          }
       }
      }
  });

  var txtServerName=new Ext.form.TextField({
  id: 'AUTH_SOURCE_SERVER_NAME',
  fieldLabel: _('ID_SERVER_NAME'),
  xtype:'textfield',
  value:'',
  width: 200,
  autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '50'},
  allowBlank: false,
  listeners: {
              'render': function(c) {
                c.getEl().on('keyup', function() {
                }, c);
              }
            }
  });

  var txtPort=new Ext.form.TextField({
    id: 'AUTH_SOURCE_PORT',
    fieldLabel: _('ID_PORT'),
    xtype:'textfield',
    value:'389',
    width: 200,
    autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '5'},
    allowBlank: false,
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });
  var my_valuesTLS= [['no'],['yes']];
  var cboxTLS = new Ext.form.ComboBox({
      fieldLabel: _('ID_ENABLED_TLS'),
      hiddenName: 'AUTH_SOURCE_ENABLED_TLS',
      store: new Ext.data.SimpleStore({
          fields: ['no','yes'],
          data : my_valuesTLS
      }),
      displayField: 'no',
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:'Choose an option...',
      selectOnFocus:true
  });

  var my_values_version= [['2'],['3']];
  var cboxVersion = new Ext.form.ComboBox({
      fieldLabel: _('ID_VERSION'),
      hiddenName: 'AUTH_SOURCE_VERSION',
      store: new Ext.data.SimpleStore({
          fields: ['two','three'],
          data : my_values_version
      }),
      displayField: 'two',
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:'Choose an option...',
      selectOnFocus:true
  });


  var txtBaseDN=new Ext.form.TextField({
    id: 'AUTH_SOURCE_BASE_DN',
    fieldLabel: _('ID_BASE_DN'),
    xtype:'textfield',
    value:sUID,
    width: 300,
    autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '128'},
    allowBlank: false,
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });

  var my_values_Anonymous= [['no'],['yes']];
  var cboxAnonymous = new Ext.form.ComboBox({
      fieldLabel: _('ID_ANONYMOUS'),
      hiddenName: 'AUTH_ANONYMOUS',
      store: new Ext.data.SimpleStore({
          fields: ['no','yes'],
          data : my_values_Anonymous
      }),
      displayField: 'no',
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:'Choose an option...',
      selectOnFocus:true,
//      width: 110,
       listeners:{
            select: function(c,d,i){
            if (!i){
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").enable();
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").show();
                txtSearchUser.getEl().up('.x-form-item').setDisplayed(true);
                Ext.getCmp("AUTH_SOURCE_PASSWORD").enable();
                Ext.getCmp("AUTH_SOURCE_PASSWORD").show();
                txtPassword.getEl().up('.x-form-item').setDisplayed(true);
            }else{
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").disable();
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").hide();
                txtSearchUser.getEl().up('.x-form-item').setDisplayed(false);
                Ext.getCmp("AUTH_SOURCE_PASSWORD").disable();
                Ext.getCmp("AUTH_SOURCE_PASSWORD").hide();
                txtPassword.getEl().up('.x-form-item').setDisplayed(false);
            }
           }
          }
  });

  var txtSearchUser=new Ext.form.TextField({
    id: 'AUTH_SOURCE_SEARCH_USER',
    fieldLabel: _('ID_SEARCH_USER'),
    xtype:'textfield',
    value:'',
    width: 200,
    autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '128'},
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });

  var txtPassword=new Ext.form.TextField({
    id: 'AUTH_SOURCE_PASSWORD',
    fieldLabel: _('ID_CACHE_PASSWORD'),
    xtype:'textfield',
    inputType:'password',
    value:'',
    width: 200,
    autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '32'},
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });

//Identifier for an imported user
  var txtIdentifier=new Ext.form.TextField({
    id: 'AUTH_SOURCE_IDENTIFIER_FOR_USER',
    fieldLabel: _('ID_IDENTIFIER_IMPORT_USER'),
    xtype:'textfield',
    value:'',
    width: 200,
    autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '50'},
    allowBlank: false,
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });
//Additional Filter
  var txtoAddFilter=new Ext.form.TextField({
    id: 'AUTH_SOURCE_ADDITIONAL_FILTER',
    fieldLabel: _('ID_ADDITIONAL_FILTER'),
    xtype:'textfield',
    value:'',
    width: 300,
    autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '200'},
    allowBlank: true,
    listeners: {
                'render': function(c) {
                  c.getEl().on('keyup', function() {
                  }, c);
                }
              }
    });
//here we are setting the fields
  fieldsAS = new Ext.form.FieldSet({
    title: _('ID_AUTHENTICATION_SOURCE_INF_TITLE'),
    items: [
            txtSourceId,
            txtName,
            cboxType,
            txtServerName,
            txtPort,
            cboxTLS,
            cboxVersion,
            txtBaseDN,
            cboxAnonymous,
            txtSearchUser,
            txtPassword ,
            txtIdentifier,
            txtoAddFilter,
            ]
    });

  formAuthSourceE = new Ext.FormPanel({
    id:'formAuthSourceE',
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
    fieldsAS
    ],
    buttons: [
      {
        text: _('ID_SAVE'),
        handler: saveAuthSources
      },
      {
        text: _('ID_CANCEL'),
        handler: goback
      }
    ]

  });

   formAuthSourceE.render(document.body);
   loadAuthSourceData(sUID, txtSearchUser, txtPassword);

 });
 function goback(){
    window.location = 'authSources_List';
 }
 function saveAuthSources(){
   formAuthSourceE.getForm().submit({
      waitTitle : "",
      url: '../adminProxy/saveAuthSources',
      params: {
  //                            action : 'tryit',
       AUTH_SOURCE_UID:  formAuthSourceE.getForm().findField('AUTH_SOURCE_UID').getValue(),
       AUTH_SOURCE_NAME: formAuthSourceE.getForm().findField('AUTH_SOURCE_NAME').getValue(),
       LDAP_TYPE: formAuthSourceE.getForm().findField('LDAP_TYPE').getValue(),
       AUTH_SOURCE_SERVER_NAME: formAuthSourceE.getForm().findField('AUTH_SOURCE_SERVER_NAME').getValue(),
       AUTH_SOURCE_PORT: formAuthSourceE.getForm().findField('AUTH_SOURCE_PORT').getValue(),
       AUTH_SOURCE_ENABLED_TLS: formAuthSourceE.getForm().findField('AUTH_SOURCE_ENABLED_TLS').getValue(),
       AUTH_ANONYMOUS: formAuthSourceE.getForm().findField('AUTH_ANONYMOUS').getValue(),
       AUTH_SOURCE_SEARCH_USER: formAuthSourceE.getForm().findField('AUTH_SOURCE_SEARCH_USER').getValue(),
       AUTH_SOURCE_PASSWORD: formAuthSourceE.getForm().findField('AUTH_SOURCE_PASSWORD').getValue(),
       AUTH_SOURCE_VERSION: formAuthSourceE.getForm().findField('AUTH_SOURCE_VERSION').getValue(),
       AUTH_SOURCE_BASE_DN: formAuthSourceE.getForm().findField('AUTH_SOURCE_BASE_DN').getValue()
      },
      waitMsg : _('ID_SAVING'),
      timeout : 3600,
      success: function(f,a){

       resp = Ext.util.JSON.decode(a.response.responseText);
       if (resp.success){
         window.location = 'authSources_List';
       }
      },
      failure: function(f,a){
          if (a.failureType === Ext.form.Action.CONNECT_FAILURE){
              Ext.Msg.alert('Failure', 'Server reported:'+a.response.status+' '+a.response.statusText);
          }
          if (a.failureType === Ext.form.Action.SERVER_INVALID){
              Ext.Msg.alert('Warning', 'you have an error');
          }
      }
    });
 }

 // Load authosource data for the Edit mode
function loadAuthSourceData(sUID, txtSearchUser, txtPassword){
  Ext.Ajax.request({
    url: 'authSources_Ajax',
    params: {
      'action': 'loadauthSourceData',
      sUID:sUID
    },
    waitMsg: _('ID_UPLOADING_PROCESS_FILE'),
    success: function(r,o){
      var data = Ext.util.JSON.decode(r.responseText);

          if (!data.sources.AUTH_ANONYMOUS){
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").enable();
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").show();
                txtSearchUser.getEl().up('.x-form-item').setDisplayed(true);
                Ext.getCmp("AUTH_SOURCE_PASSWORD").enable();
                Ext.getCmp("AUTH_SOURCE_PASSWORD").show();
                txtPassword.getEl().up('.x-form-item').setDisplayed(true);
            }else{
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").disable();
                Ext.getCmp("AUTH_SOURCE_SEARCH_USER").hide();
                txtSearchUser.getEl().up('.x-form-item').setDisplayed(false);
                Ext.getCmp("AUTH_SOURCE_PASSWORD").disable();
                Ext.getCmp("AUTH_SOURCE_PASSWORD").hide();
                txtPassword.getEl().up('.x-form-item').setDisplayed(false);
            }

      Ext.getCmp('formAuthSourceE').getForm().setValues({

       AUTH_SOURCE_UID:  data.sources.AUTH_SOURCE_UID,
       AUTH_SOURCE_NAME: data.sources.AUTH_SOURCE_NAME ,
       LDAP_TYPE: (data.sources.LDAP_TYPE=='ad')?'Active Directory':data.sources.LDAP_TYPE,
       AUTH_SOURCE_SERVER_NAME: data.sources.AUTH_SOURCE_SERVER_NAME,
       AUTH_SOURCE_PORT: data.sources.AUTH_SOURCE_PORT,
       AUTH_SOURCE_ENABLED_TLS: (data.sources.AUTH_SOURCE_ENABLED_TLS)?'yes':'no',
       AUTH_ANONYMOUS: (data.sources.AUTH_ANONYMOUS)?'yes':'no',
       AUTH_SOURCE_SEARCH_USER: data.sources.AUTH_SOURCE_SEARCH_USER,
       AUTH_SOURCE_PASSWORD: data.sources.AUTH_SOURCE_PASSWORD,
       AUTH_SOURCE_IDENTIFIER_FOR_USER: data.sources.AUTH_SOURCE_IDENTIFIER_FOR_USER,
       AUTH_SOURCE_VERSION: data.sources.AUTH_SOURCE_VERSION,
       AUTH_SOURCE_BASE_DN: data.sources.AUTH_SOURCE_BASE_DN,
       AUTH_SOURCE_ADDITIONAL_FILTER:data.sources.AUTH_SOURCE_ADDITIONAL_FILTER
      })
    },
    failure:function(r,o){
      //viewport.getEl().unmask();
    }
  });
}