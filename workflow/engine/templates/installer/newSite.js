Ext.onReady(function(){
  var  getFieldOutput = function(txt, assert) {
    if(assert == true) {
      img = 'dialog-ok-apply.png';
      size = 'width=12 height=12';
      color = 'green';
    } else {
      img = 'delete.png';
      size = 'width=15 height=15';
      color = 'red';
    }
    return  '<font color=' + color + '>' + txt + '</font> <img src="/images/' + img + '" ' + size + '/>';
  };

  var testConnection = function() {
    wizard.showLoadMask(true);
  	if ((Ext.getCmp('db_engine').getValue() == '') || !Ext.getCmp('db_hostname').isValid() || !Ext.getCmp('db_username').isValid()) {
      wizard.onClientValidation(1, false);
      wizard.showLoadMask(false);
      return false;
  	}
    Ext.Ajax.request({
      url: 'newSite',
      success: function(response){
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.getCmp('db_message').setValue(getFieldOutput(response.message, response.result));
        wizard.onClientValidation(1, response.result);
        wizard.showLoadMask(false);
      },
      failure: function(){},
      params: {
        'action': 'testConnection',
      	'db_engine': Ext.getCmp('db_engine').getValue(),
      	'db_hostname': Ext.getCmp('db_hostname').getValue(),
      	'db_port': Ext.getCmp('db_port').getValue(),
      	'db_username': Ext.getCmp('db_username').getValue(),
      	'db_password': Ext.getCmp('db_password').getValue()
      }
    });
  };

  var ckeckDBEnginesValuesLoaded = function() {
    wizard.showLoadMask(true);
    if (Ext.getCmp('db_engine').store.getCount() == 0) {
      Ext.getCmp('db_engine').store.load();
   }
    else {
      testConnection();
   }
  };

  var checkWorkspaceConfiguration = function() {
    var canInstall = false;
    if (!Ext.getCmp('workspace').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter a valid Workspace Name.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (!Ext.getCmp('adminUsername').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter a valid Admin Username.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() == '') {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Admin Password.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() != Ext.getCmp('confirmPassword').getValue()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('The password confirmation is incorrect.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (!Ext.getCmp('wfDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Workflow Database Name.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (!Ext.getCmp('rbDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Rbac Database Name.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    if (!Ext.getCmp('rpDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Report Database Name.', false));
      wizard.onClientValidation(2, false);
      return;
    }
    checkDatabases();
  };

  var checkDatabases = function() {
    wizard.showLoadMask(true);
    Ext.Ajax.request({
      url: 'newSite',
      success: function(response){
        var existMsg = '<span style="color: red;">(Exists)</span>';
        var noExistsMsg = '<span style="color: green;">(No exists)</span>';
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.get('wfDatabaseSpan').dom.innerHTML = (response.wfDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rbDatabaseSpan').dom.innerHTML = (response.rbDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rpDatabaseSpan').dom.innerHTML = (response.rpDatabaseExists ? existMsg : noExistsMsg);
        var dbFlag = ((!response.wfDatabaseExists && !response.rbDatabaseExists && !response.rpDatabaseExists) || Ext.getCmp('deleteDB').getValue());
        wizard.onClientValidation(2, dbFlag);
        if (dbFlag) {
          Ext.getCmp('finish_message').setValue(getFieldOutput('The data is correct.', true));
        }
        else {
          Ext.getCmp('finish_message').setValue(getFieldOutput('Rename the databases names or workspace name or check the "Delete Databases if exists" to overwrite the exiting databases.', false));
        }
        wizard.showLoadMask(false);
      },
      failure: function(){},
      params: {
        'action': 'checkDatabases',
      	'db_engine': Ext.getCmp('db_engine').getValue(),
      	'db_hostname': Ext.getCmp('db_hostname').getValue(),
      	'db_username': Ext.getCmp('db_username').getValue(),
      	'db_password': Ext.getCmp('db_password').getValue(),
      	'db_port': Ext.getCmp('db_port').getValue(),
      	'wfDatabase': Ext.getCmp('wfDatabase').getValue(),
      	'rbDatabase': Ext.getCmp('rbDatabase').getValue(),
      	'rpDatabase': Ext.getCmp('rpDatabase').getValue()
      	}
    });
  };

  var steps = [];
  var setIndex = 0;
  var storeDatabase = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({url: 'newSite?action=getEngines', method: 'POST'}),
    reader: new Ext.data.JsonReader({
      fields: [{name: 'id'},{name: 'label'}]
    }),
    listeners: {load: function() {
      Ext.getCmp('db_engine').setValue(DB_ADAPTER);
      testConnection();
    }}
  });

  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title: 'Database Configuration',
    monitorValid: false,
    items: [
      {
        border: false,
        html: 'Database Configuration',
        bodyStyle: 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 360,
        items: [
          {
            region: 'west',
            width: 200,
            bodyStyle: 'padding:10px;font-size:1.2em;',
            html: textStep1
          },
          {
            region: 'center',
            xtype: 'panel',
            bodyStyle: 'background:none;padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;font-size:1.2em;',
            items:[
              {
                xtype:'fieldset',
                labelAlign: 'left',
                labelWidth: 160,
                items: [
                  new Ext.form.ComboBox({
                    fieldLabel: 'Database Engine',
                    width: 200,
                    store: storeDatabase,
                    displayField: 'label',
                    valueField: 'id',
                    mode: 'local',
                    editable: false,
                    forceSelection: true,
                    allowBlank: false,
                    triggerAction: 'all',
                    id: 'db_engine',
                    selectOnFocus: true,
                    listeners: {select: function() {
                      if (this.value == 'mysql') {
                        Ext.getCmp('db_port').setValue('3306');
                        Ext.getCmp('db_username').setValue('root');
                      }
                      else {
                        Ext.getCmp('db_port').setValue('1433');
                        Ext.getCmp('db_username').setValue('sa');
                      }
                      wizard.onClientValidation(1, false);
                    }}
                  }),
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Host Name',
                    width: 180,
                    id: 'db_hostname',
                    value: DB_HOST,
                    allowBlank: false,
                    validator: function(v){
                      var t = /^[0-9\.a-zA-Z_\-]+$/;
                      return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(1, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Port',
                    width: 180,
                    id: 'db_port',
                    value: DB_PORT,
                    allowBlank: false,
                    validator: function(v){
                      var t = /^[0-9]+$/;
                      return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(1, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Username',
                    width: 180,
                    id: 'db_username',
                    value: DB_USER,
                    allowBlank: false,
                    validator: function(v){
                      var t = /^[.a-zA-Z_\-]+$/;
                      return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(1, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Password',
	                  inputType: 'password',
                    width: 180,
                    id: 'db_password',
                    value: DB_PASS,
                    allowBlank: true,
                    listeners: {change: function() {
                      wizard.onClientValidation(1, false);
                    }}
                  },
                  {
                    xtype: 'displayfield',
                    id: 'db_message'
                  },
                  new Ext.Button({
                    text: ' Test Connection',
                    handler: testConnection,
                    scope: this
                  })
                ]
              },
            ]
          }
        ]
      }
    ],
    listeners: {
      show: ckeckDBEnginesValuesLoaded
    }
  });

  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title: 'Workspace Configuration',
    monitorValid: false,
    defaults: {
      labelStyle: 'font-size:11px'
    },
    items: [
      {
        border: false,
        html: 'Workspace Configuration',
        bodyStyle: 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 360,
        items:[
          {
            region: 'west',
            width: 200,
            bodyStyle: 'padding:10px;font-size:1.2em;',
            html: textStep2
          },
          {
            region: 'center',
            xtype: 'panel',
            bodyStyle: 'background:none;padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;font-size:1.2em;',
            items: [
              {
                xtype:'fieldset',
                //labelAlign: 'right',
                labelWidth: 210,
                items:[
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Workspace Name',
                    value:'workflow',
                    maxLength: 29,
                    validator: function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    id: 'workspace',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                      if (!Ext.getCmp('changeDBNames').getValue()) {
                        Ext.getCmp('wfDatabase').setValue('wf_' + this.getValue());
                        Ext.getCmp('rbDatabase').setValue('rb_' + this.getValue());
                        Ext.getCmp('rpDatabase').setValue('rp_' + this.getValue());
                      }
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Admin Username',
                    value:'admin',
                    validator: function(v){
                        var t = /^[a-zA-Z_0-9.@-]+$/;
                        return t.test(v);
                    },
                    id: 'adminUsername',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Admin Password',
                    inputType: 'password',
                    id: 'adminPassword',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Confirm Admin Password',
                    inputType: 'password',
                    id: 'confirmPassword',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  }
                ]
              },
              {
                xtype: 'fieldset',
                labelAlign: 'left',
                labelWidth: 210,
                //labelWidth: 200,
                //title: 'ProcessMaker Databases',
                items:[
                 new Ext.form.Checkbox({
              	   boxLabel: 'Change Database names',
              	   id: 'changeDBNames',
                   handler: function() {
                     if (this.getValue()) {
                       Ext.getCmp('wfDatabase').enable();
                       Ext.getCmp('rbDatabase').enable();
                       Ext.getCmp('rpDatabase').enable();
                       Ext.getCmp('wfDatabase').validate();
                       Ext.getCmp('rbDatabase').validate();
                       Ext.getCmp('rpDatabase').validate();
                     }
                     else {
                       Ext.getCmp('wfDatabase').setValue('wf_' + Ext.getCmp('workspace').getValue());
                       Ext.getCmp('rbDatabase').setValue('rb_' + Ext.getCmp('workspace').getValue());
                       Ext.getCmp('rpDatabase').setValue('rp_' + Ext.getCmp('workspace').getValue());
                       Ext.getCmp('wfDatabase').disable();
                       Ext.getCmp('rbDatabase').disable();
                       Ext.getCmp('rpDatabase').disable();
                     }
                     wizard.onClientValidation(2, false);
                   }
                 }),
                 {
                    xtype: 'textfield',
                    fieldLabel: 'Workflow Database Name <span id="wfDatabaseSpan"></span>',
                    id: 'wfDatabase',
                    value:'wf_workflow',
                    allowBlank: false,
                    maxLength: 32,
                    validator: function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Rbac Database Name <span id="rbDatabaseSpan"></span>',
                    id: 'rbDatabase',
                    value:'rb_workflow',
                    allowBlank: false,
                    maxLength: 32,
                    validator: function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  },
                  {
                    xtype: 'textfield',
                    fieldLabel: 'Report Database Name <span id="rpDatabaseSpan"></span>',
                    id: 'rpDatabase',
                    value:'rp_workflow',
                    allowBlank: false,
                    maxLength: 32,
                    validator: function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(2, false);
                    }}
                  },
                  new Ext.form.Checkbox({
              	    boxLabel: "Delete Databases if exists",
              	    id: 'deleteDB',
              	    handler: function() {
              	      wizard.onClientValidation(2, false);
              	    }
                  }),
                  {
                    xtype: 'displayfield',
                    id: 'finish_message'
                  },
                  new Ext.Button({
                    id: 'checkWSConfiguration',
                    text: ' Check Workspace Configuration',
                    handler: checkWorkspaceConfiguration,
                    scope: this
                  })
                ]
              }
            ]
          }
        ]
      }
    ],
    listeners: {
      show: function() {
        checkWorkspaceConfiguration();
      }
    }
  });

  var wizard = new Ext.ux.Wiz({
    height: 520,
    width: 780,
    id: 'wizard',
    closable: false,
    headerConfig: {
      title: '&nbsp'
    },
    cardPanelConfig: {
      defaults: {
        bodyStyle: 'padding:20px 10px 10px 20px;background-color:#F6F6F6;',
        border: false
      }
    },
    cards: steps,
    loadMaskConfig: {
      default: 'Checking...',
      finishing: 'Finishing...'
    },
    listeners: {
      finish: function(){
        wizard.showLoadMask(true, 'finishing');
        Ext.Ajax.request({
          url: 'newSite',
          success: function(response){
            var response = Ext.util.JSON.decode(response.responseText);
            Ext.getCmp('finish_message').setValue(getFieldOutput(response.message, response.result));
            wizard.showLoadMask(false);
            if (response.result) {
              Ext.Msg.alert('ProcessMaker was successfully installed', 'Workspace "' + Ext.getCmp('workspace').getValue() + '" was installed correctly now you will be redirected to your new workspace.', function() {window.location = response.url;});
            }
          },
          failure: function(){wizard.showLoadMask(false);},
          params: {
            'action': 'createWorkspace',
          	'db_engine': Ext.getCmp('db_engine').getValue(),
          	'db_hostname': Ext.getCmp('db_hostname').getValue(),
        	  'db_username': Ext.getCmp('db_username').getValue(),
          	'db_password': Ext.getCmp('db_password').getValue(),
          	'db_port': Ext.getCmp('db_port').getValue(),
            'pathConfig': pathConfig,
            'pathLanguages': pathLanguages,
            'pathPlugins': pathPlugins,
            'pathXmlforms': pathXmlforms,
            'pathShared': pathShared,
            'workspace': Ext.getCmp('workspace').getValue(),
            'adminUsername': Ext.getCmp('adminUsername').getValue(),
            'adminPassword': Ext.getCmp('adminPassword').getValue(),
            'wfDatabase': Ext.getCmp('wfDatabase').getValue(),
            'rbDatabase': Ext.getCmp('rbDatabase').getValue(),
            'rpDatabase': Ext.getCmp('rpDatabase').getValue(),
            'deleteDB': Ext.getCmp('deleteDB').getValue()
          },
          timeout: 180000
        });
      }
    }
  });

  wizard.show();
});