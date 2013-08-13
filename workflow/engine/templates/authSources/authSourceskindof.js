/*
 * @author Carlos P.C <carlos@colosa.com, pckrlos@gmail.com>
 * Oct 20th, 2011
 */

Ext.onReady(function(){

  var txtSourceId=new Ext.form.TextField({
  id: 'AUTH_SOURCE_UID',
  fieldLabel: 'krlos',
  xtype: 'textfield',
  value: '',
  width: 200,
  hideLabel: true,
  hidden: true
  });

  var txtSourceProvider=new Ext.form.TextField({
  id: 'AUTH_SOURCE_PROVIDER',
  fieldLabel: 'krlos',
  xtype: 'textfield',
  value: sprovider,
  width: 200,
  hideLabel: true,
  hidden: true
  });

  var txtName=new Ext.form.TextField({
  id: 'AUTH_SOURCE_NAME',
  fieldLabel: _('ID_NAME'),
  xtype: 'textfield',
  value: '',
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
      emptyText: _('ID_CHOOSE_OPTION') + '...',
        editable: false,
      selectOnFocus:true,
      listeners:{
       select: function(c,d,i){
          if(i){
            formAuthSource.getForm().findField('AUTH_SOURCE_IDENTIFIER_FOR_USER').setValue('samaccountname');
          } else {
            formAuthSource.getForm().findField('AUTH_SOURCE_IDENTIFIER_FOR_USER').setValue('uid');
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
      allowBlank: false,
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:  _('ID_CHOOSE_OPTION') + '...',
      editable: false,
      selectOnFocus:true
  });
   //cboxTLS.setValue('no');

  var my_values_version= [['2'],['3']];
  var cboxVersion = new Ext.form.ComboBox({
      fieldLabel: _('ID_VERSION'),
      hiddenName: 'AUTH_SOURCE_VERSION',
      store: new Ext.data.SimpleStore({
          fields: ['two','three'],
          data : my_values_version
      }),
      allowBlank: false,
      displayField: 'two',
      typeAhead: true,
      mode: 'local',
      triggerAction: 'all',
      emptyText:  _('ID_CHOOSE_OPTION') + '...',
      editable: false,
      selectOnFocus:true
  });


  var txtBaseDN=new Ext.form.TextField({
    id: 'AUTH_SOURCE_BASE_DN',
    fieldLabel: _('ID_BASE_DN'),
    xtype:'textfield',
    value:'',
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
          fields: ['0','1'],
          data : my_values_Anonymous
      }),
      displayField: '0',
      typeAhead: true,
      mode: 'local',
      allowBlank: false,
      triggerAction: 'all',
      emptyText:  _('ID_CHOOSE_OPTION') + '...',
      editable: false,
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
    value:'uid',
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
    title: _('ID_AUTHENTICATION_SOURCE_INFORMATION'),
    items: [
            txtSourceId,
            txtSourceProvider,
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
            txtoAddFilter
            ]
    });


  formAuthSource = new Ext.FormPanel({
    id:'formAuthSource',
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
        handler: TestSite

      },
      {
        text: _('ID_CANCEL'),
        handler: goback
      }
    ]

  });

    formAuthSource.render(document.body);

 });
 function goback(){
    window.location = 'authSources_List';
 }
 function TestSite(){
   formAuthSource.getForm().submit({
    waitTitle : "",
    url: '../adminProxy/saveAuthSources',
      params: {
    //                            action : 'tryit',
       AUTH_SOURCE_UID:  formAuthSource.getForm().findField('AUTH_SOURCE_UID').getValue(),
       AUTH_SOURCE_NAME: formAuthSource.getForm().findField('AUTH_SOURCE_NAME').getValue(),
       LDAP_TYPE: formAuthSource.getForm().findField('LDAP_TYPE').getValue(),
       AUTH_SOURCE_SERVER_NAME: formAuthSource.getForm().findField('AUTH_SOURCE_SERVER_NAME').getValue(),
       AUTH_SOURCE_PORT: formAuthSource.getForm().findField('AUTH_SOURCE_PORT').getValue(),
       AUTH_SOURCE_ENABLED_TLS: formAuthSource.getForm().findField('AUTH_SOURCE_ENABLED_TLS').getValue(),
       AUTH_ANONYMOUS: formAuthSource.getForm().findField('AUTH_ANONYMOUS').getValue(),
       AUTH_SOURCE_SEARCH_USER: formAuthSource.getForm().findField('AUTH_SOURCE_SEARCH_USER').getValue(),
       AUTH_SOURCE_PASSWORD: formAuthSource.getForm().findField('AUTH_SOURCE_PASSWORD').getValue(),
       AUTH_SOURCE_VERSION: formAuthSource.getForm().findField('AUTH_SOURCE_VERSION').getValue(),
       AUTH_SOURCE_BASE_DN: formAuthSource.getForm().findField('AUTH_SOURCE_BASE_DN').getValue(),
       AUTH_SOURCE_ADDITIONAL_FILTER: formAuthSource.getForm().findField('AUTH_SOURCE_ADDITIONAL_FILTER').getValue()

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
              Ext.Msg.alert(_('ID_FAILURE'),  _('ID_SERVER_REPORTED') + ':'+a.response.status+' '+a.response.statusText);
          }
          if (a.failureType === Ext.form.Action.SERVER_INVALID){
              Ext.Msg.alert( _('ID_WARNING'), _('ID_YOU_HAVE_ERROR') );
          }
      }
    });
 }