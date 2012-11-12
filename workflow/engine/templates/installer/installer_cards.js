var steps = [];

Ext.onReady(function(){
  PMExt.notify_time_out = 2;

  var storeDatabase = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({url: 'getEngines', method:'POST'}),
    reader: new Ext.data.JsonReader({
      fields: [{name: 'id'},{name: 'label'}]
    }),
    listeners: {load: function() {
      Ext.getCmp('db_engine').setValue(Ext.getCmp('db_engine').store.getAt(0).id);
      if (Ext.getCmp('db_engine').store.getAt(0).id == 'mysql') {
        Ext.getCmp('db_port').setValue('3306');
        Ext.getCmp('db_username').setValue('root');
      }
      else {
        Ext.getCmp('db_port').setValue('1433');
        Ext.getCmp('db_username').setValue('sa');
      }
      testConnection();
    }}
  });

  var store = new Ext.data.ArrayStore({
      fields: ['id', 'label'],
      data : [['en', 'English'],['es', 'Spanish']]
  });

  var cmbLanguages = new Ext.form.ComboBox({
      fieldLabel: 'Language',
      store: store,
      labelWidth: 200,
      displayField:'label',
      typeAhead: true,
      mode: 'local',
      forceSelection: true,
      triggerAction: 'all',
      emptyText:'Select a language...',
      selectOnFocus:true
  });

  // getting the system info
  function getSystemInfo() {
    wizard.showLoadMask(true);
    Ext.Ajax.request({
      url: 'getSystemInfo',
      success: function(response){
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.getCmp('php').setValue       (getFieldOutput(response.php.version,      response.php.result));
        Ext.getCmp('mysql').setValue     (getFieldOutput(response.mysql.version,    response.mysql.result));
        //Ext.getCmp('mssql').setValue     (getFieldOutput(response.mssql.version,    response.mssql.result));
        Ext.getCmp('curl').setValue      (getFieldOutput(response.curl.version,     response.curl.result));
        Ext.getCmp('openssl').setValue   (getFieldOutput(response.openssl.version,  response.openssl.result));
        Ext.getCmp('dom').setValue       (getFieldOutput(response.dom.version,      response.dom.result));
        Ext.getCmp('gd').setValue        (getFieldOutput(response.gd.version,       response.gd.result));
        Ext.getCmp('multibyte').setValue (getFieldOutput(response.multibyte.version,response.multibyte.result));
        Ext.getCmp('soap').setValue      (getFieldOutput(response.soap.version,     response.soap.result));
        Ext.getCmp('ldap').setValue      (getFieldOutput(response.ldap.version,     response.ldap.result));
        Ext.getCmp('memory').setValue    (getFieldOutput(response.memory.version,   response.memory.result));

        dbReq  = response.mysql.result || response.mssql.result;
        phpReq = response.php.result && response.curl.result && response.dom.result && response.gd.result && response.multibyte.result && response.soap.result && response.memory.result;
        wizard.onClientValidation(0, dbReq && phpReq);
        wizard.showLoadMask(false);
      },
      failure: function(){},
      params: {'clientBrowser': PMExt.getBrowser().name}
    });
  }

  // getting the system info
  function getPermissionInfo() {
    wizard.showLoadMask(true);

    Ext.Ajax.request({
      url: 'getPermissionInfo',
      success: function(response) {
        var okImage = '<img src="/images/dialog-ok-apply.png" width="12" height="12" />';
        var badImage = '<img src="/images/delete.png" width="15" height="15" />';
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.get('pathConfigSpan').dom.innerHTML    = (response.pathConfig.result ? okImage : badImage);
        Ext.get('pathLanguagesSpan').dom.innerHTML = (response.pathLanguages.result ? okImage : badImage);
        Ext.get('pathPluginsSpan').dom.innerHTML   = (response.pathPlugins.result ? okImage : badImage);
        Ext.get('pathXmlformsSpan').dom.innerHTML  = (response.pathXmlforms.result ? okImage : badImage);
        Ext.get('pathPublicSpan').dom.innerHTML    = (response.pathPublic.result ? okImage : badImage);
        Ext.get('pathSharedSpan').dom.innerHTML    = (response.pathShared.result ? okImage : badImage);
        Ext.get('pathLogFileSpan').dom.innerHTML    = (response.pathLogFile.result ? okImage : badImage);

        wizard.onClientValidation(1,
          response.pathConfig.result &&
          response.pathLanguages.result &&
          response.pathPlugins.result &&
          response.pathXmlforms.result &&
          response.pathPublic.result &&
          response.pathShared.result &&
          response.pathLogFile.result
        );

        wizard.showLoadMask(false);

        permissionInfo.error1 = response.noWritableFiles

        //type = response.success ? 'success' : 'warning';
        if (!response.success)
          PMExt.error('WARNING', response.notify + ' <a href="#" onclick="showPermissionInfo(1); return false;">Show non-writable files.</a>');

      },
      failure: function(){},
      params: {
        'pathConfig': Ext.getCmp('pathConfig').getValue(),
        'pathLanguages': Ext.getCmp('pathLanguages').getValue(),
        'pathPlugins': Ext.getCmp('pathPlugins').getValue(),
        'pathXmlforms': Ext.getCmp('pathXmlforms').getValue(),
        'pathShared': Ext.getCmp('pathShared').getValue(),
        'pathLogFile': Ext.getCmp('pathLogFile').getValue(),
        'pathPublic': Ext.getCmp('pathPublic').getValue()
      }
    });
  }

  function checkLicenseAgree() {
    //wizard.onClientValidation(2, Ext.getCmp('agreeCheckbox').checked);
    wizard.onClientValidation(2, false);
  }

  function ckeckDBEnginesValuesLoaded() {
    wizard.showLoadMask(true);
    if (Ext.getCmp('db_engine').store.getCount() == 0) {
      Ext.getCmp('db_engine').store.load();
    }
    else {
      testConnection();
    }
  }

  // test database Connection
  function testConnection() {
    wizard.showLoadMask(true);
  	if ((Ext.getCmp('db_engine').getValue() == '') || !Ext.getCmp('db_hostname').isValid() || !Ext.getCmp('db_username').isValid()) {
      wizard.onClientValidation(3, false);
      wizard.showLoadMask(false);
      return false;
  	}
    Ext.Ajax.request({
      url: 'testConnection',
      success: function(response){
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.getCmp('db_message').setValue(getFieldOutput(response.message, response.result));

        if (!response.result)
          PMExt.notify('WARNING', response.message, 'warning');

        wizard.onClientValidation(3, response.result);
        wizard.showLoadMask(false);
      },
      failure: function(){},
      params: {
      	'db_engine'  : Ext.getCmp('db_engine').getValue(),
      	'db_hostname': Ext.getCmp('db_hostname').getValue(),
      	'db_username': Ext.getCmp('db_username').getValue(),
      	'db_password': Ext.getCmp('db_password').getValue(),
      	'db_port'    : Ext.getCmp('db_port').getValue()
      }
    });
  }

  function checkWorkspaceConfiguration() {
    var canInstall = false;
    if (!Ext.getCmp('workspace').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter a valid Workspace Name.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('adminUsername').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter a valid Admin Username.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() == '') {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Admin Password.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() != Ext.getCmp('confirmPassword').getValue()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('The password confirmation is incorrect.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('wfDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Workflow Database Name.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('rbDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Rbac Database Name.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('rpDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput('Please enter the Report Database Name.', false));
      wizard.onClientValidation(4, false);
      return;
    }
    checkDatabases();
  }

  function checkDatabases() {
    wizard.showLoadMask(true);
    Ext.Ajax.request({
      url: 'checkDatabases',
      success: function(response){
        var existMsg = '<span style="color: red;">(Exists)</span>';
        var noExistsMsg = '<span style="color: green;">(No exists)</span>';
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.get('wfDatabaseSpan').dom.innerHTML = (response.wfDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rbDatabaseSpan').dom.innerHTML = (response.rbDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rpDatabaseSpan').dom.innerHTML = (response.rpDatabaseExists ? existMsg : noExistsMsg);

        var dbFlag = ((!response.wfDatabaseExists && !response.rbDatabaseExists && !response.rpDatabaseExists) || Ext.getCmp('deleteDB').getValue());
        wizard.onClientValidation(4, dbFlag);

        if (dbFlag) {
          Ext.getCmp('finish_message').setValue(getFieldOutput('The data is correct.', true));
        }
        else {
          Ext.getCmp('finish_message').setValue(getFieldOutput('Not Passed.', false));
          PMExt.notify('WARNING', response.errMessage, 'warning', 4)
        }
        wizard.showLoadMask(false);
      },
      failure: function(){},
      params: {
      	'db_engine'  : Ext.getCmp('db_engine').getValue(),
      	'db_hostname': Ext.getCmp('db_hostname').getValue(),
      	'db_username': Ext.getCmp('db_username').getValue(),
      	'db_password': Ext.getCmp('db_password').getValue(),
      	'db_port'    : Ext.getCmp('db_port').getValue(),
      	'wfDatabase' : Ext.getCmp('wfDatabase').getValue(),
      	'rbDatabase' : Ext.getCmp('rbDatabase').getValue(),
      	'rpDatabase' : Ext.getCmp('rpDatabase').getValue()
      	}
    });
  }


  var setIndex = 0;

  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title : 'Pre-installation check',
    monitorValid : false,
    labelAlign: 'left',
    labelWidth: 200,
    defaults     : {
    },
    items : [
      {
        border    : false,
        html      : 'Pre-installation check',
        bodyStyle : 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 340,
        items:[
          {
            region: 'west',
            width: 250,
            bodyStyle : 'padding:10px;font-size:1.2em;',
            html: step1_txt
          },

          {
            region: 'center',
            xtype : 'fieldset',
            labelWidth: 200,
            items:[
              {
                xtype     : 'displayfield',
                fieldLabel: 'PHP Version >= 5.2.10',
                id  : 'php'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'MySQL Support',
                id  : 'mysql'
              }/*,
              {
                xtype     : 'displayfield',
                fieldLabel: 'MSSQL Support (*)',
                id  : 'mssql'
              }*/,
              {
                xtype     : 'displayfield',
                fieldLabel: 'cURL Version',
                id  : 'curl'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'OpenSSL Version (*)',
                id  : 'openssl'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'DOM/XML Support',
                id  : 'dom'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'GD Support',
                id  : 'gd'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'Multibyte Strings Support',
                id  : 'multibyte'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'Soap Support',
                id  : 'soap'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'LDAP Support (*)',
                id  : 'ldap'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: 'Memory Limit >= 80M',
                id: 'memory',
                value: '5.0 or greater'
              },
              new Ext.Button({
                text : 'Check Again',
                handler  : getSystemInfo,
                scope    : this
              })
            ]
          }
        ]
      }
    ],
    listeners: {
      show: getSystemInfo
    }
  });

  // third card with Directory File Permission
  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title:'Directory File Permission',
    monitorValid : false,
    labelAlign: 'left',
    labelWidth: 200,
    defaults     : {
        //labelStyle : 'font-size:11px'
    },
    items : [
      {
        border    : false,
        html:'Directory/File Permission',
        bodyStyle : 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 300,
        items:[
          {
            region: 'north',
            height: 55,
            bodyStyle : 'padding:10px;font-size:1.2em;',
            html: step3_txt
          },
          {
            region: 'center',
            xtype : 'fieldset',
            alignField : 'left',
            bodyStyle : 'padding-left:40px;font-size:12;',
            labelWidth: 180,
            items:[
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathConfigSpan"></span> Config Directory',
                id: 'pathConfig',
                width: 430,
                value: path_config,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathLanguagesSpan"></span> Language Directory',
                id: 'pathLanguages',
                width: 430,
                value: path_languages,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathPluginsSpan"></span> Plugins Directory',
                id: 'pathPlugins',
                width: 430,
                value: path_plugins,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathXmlformsSpan"></span> Xmlform Directory Directory',
                id: 'pathXmlforms',
                width: 430,
                value: path_xmlforms,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathPublicSpan"></span> Public Index file',
                id: 'pathPublic',
                width: 430,
                value: path_public,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathSharedSpan"></span> Workflow Data Directory',
                id: 'pathShared',
                width: 430,
                value: path_shared,
                enableKeyEvents: true,
                allowBlank: false,
                blankText: '"Workflow Data Directory" is required',
                selectOnFocus: true,
                msgTarget: 'side',
                listeners: {keyup: function() {
                  wizard.onClientValidation(2, false);
                  if (Ext.getCmp('pathShared').getValue().substr(-1, 1) != path_sep) {
                    Ext.getCmp('pathLogFile').setValue(Ext.getCmp('pathShared').getValue() + path_sep + 'log' + path_sep + 'install.log');
                  }
                  else {
                    Ext.getCmp('pathLogFile').setValue(Ext.getCmp('pathShared').getValue() + 'log' + path_sep + 'install.log');
                  }
                }}
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathLogFileSpan"></span> Installation log file',
                id: 'pathLogFile',
                width: 430,
                value: path_shared + 'log' + path_sep + 'install.log',
                disabled: true
              },
              new Ext.Button({
                text : 'Check Again',
                handler  : getPermissionInfo,
                scope    : this
              })
            ]
          }
        ]
      }
    ],
    listeners: {
      show: getPermissionInfo
    }

  } );


  // third card with input field email-address
  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title:'ProcessMaker Open Source License',
    //monitorValid : false,
    defaults     : {
      labelStyle : 'font-size:12px'
    },
    items : [
      {
        border    : false,
        html:'ProcessMaker Open Source License',
        bodyStyle : 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 340,
        items:[
          {
            region: 'center',
            xtype : 'fieldset',
            items:[
              new Ext.form.TextArea({
                name       : 'license',
                readOnly   : true,
                width      : 510,
                height     : 280,
                style      : 'font-size:13px',
                value      : licenseTxt
              }),
              new Ext.form.Checkbox({
                boxLabel   : 'I agree',
                id : 'agreeCheckbox',
                handler: function() {
                  wizard.onClientValidation(2, this.getValue());
                }
              })
            ]
          }
        ]
      }
    ],
    listeners: {
      show: function() {
        setTimeout(function(){
          var iAgree = Ext.getCmp('agreeCheckbox').getValue();

          wizard.onClientValidation(2, iAgree);
        }, 100);
      }
    }

  });

// fourth card Database Configuration
  steps[setIndex++] = new Ext.ux.Wiz.Card({
    title        : 'Database Configuration',
    monitorValid : false,
    items : [
      {
        border    : false,
        html      : 'Database Configuration',
        bodyStyle : 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 360,
        items:[
          {
            region: 'west',
            width: 200,
            bodyStyle : 'padding:10px;font-size:1.2em;',
            html: step4_txt
          },
          {
            region: 'center',
            xtype : 'panel',
            bodyStyle : 'background:none;padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;font-size:1.2em;',
            items:[
              {
                xtype:'fieldset',
                labelAlign: 'left',
                labelWidth: 160,
                items:[
                  new Ext.form.ComboBox({
                    fieldLabel: 'Database Engine',
                    width : 200,
                    store : storeDatabase,
                    displayField : 'label',
                    valueField   : 'id',
                    mode         : 'local',
                    editable : false,
                    forceSelection: true,
                    allowBlank: false,
                    triggerAction: 'all',
                    id: 'db_engine',
                    selectOnFocus : true,
                    listeners: {select: function() {
                      if (this.value == 'mysql') {
                        Ext.getCmp('db_port').setValue('3306');
                        Ext.getCmp('db_username').setValue('root');
                      }
                      else {
                        Ext.getCmp('db_port').setValue('1433');
                        Ext.getCmp('db_username').setValue('sa');
                      }
                      wizard.onClientValidation(3, false);
                    }}
                  }),
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Host Name',
                    width : 180,
                    id: 'db_hostname',
                    value :'localhost',
                    allowBlank : false,
                    validator  : function(v){
                        var t = /^[0-9\.a-zA-Z_\-]+$/;
                        return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(3, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Port',
                    width : 180,
                    id: 'db_port',
                    value :'',
                    allowBlank : false,
                    validator  : function(v){
                        var t = /^[0-9]+$/;
                        return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(3, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Username',
                    width : 180,
                    id: 'db_username',
                    value :'root',
                    allowBlank : false,
                    validator  : function(v){
                        var t = /^[.a-zA-Z_\-]+$/;
                        return t.test(v);
                    },
                    listeners: {change: function() {
                      wizard.onClientValidation(3, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Password',
                    inputType : 'password',
                    value     : '',
                    width : 180,
                    id: 'db_password',
                    allowBlank : true,
                    listeners: {change: function() {
                      wizard.onClientValidation(3, false);
                    }}
                  },
                  {
                    xtype     : 'displayfield',
                    //fieldLabel: ',
                    id  : 'db_message'
                  },
                  new Ext.Button({
                    text : ' Test Connection',
                    handler  : testConnection,
                    scope    : this
                  })
                ]
              }
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
    title        : 'Workspace Configuration',
    monitorValid : false,
    defaults     : {
      labelStyle : 'font-size:11px'
    },
    items : [
      {
        border    : false,
        html      : 'Workspace Configuration',
        bodyStyle : 'background:none;padding-top:0px;padding-bottom:5px;font-weight:bold;font-size:1.3em;'
      },
      {
        xtype:'panel',
        layout:'border',
        height: 360,
        items:[
          {
            region: 'west',
            width: 200,
            bodyStyle : 'padding:10px;font-size:1.2em;',
            html: step5_txt
          },
          {
            region: 'center',
            xtype : 'panel',
            bodyStyle : 'background:none;padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;font-size:1.2em;',
            items:[
              {
                xtype:'fieldset',
                //labelAlign: 'right',
                labelWidth: 210,
                items:[
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Workspace Name',
                    value  :'workflow',
                    maxLength: 29,
                    validator  : function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    id : 'workspace',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                      if (!Ext.getCmp('changeDBNames').getValue()) {
                        Ext.getCmp('wfDatabase').setValue('wf_' + this.getValue());
                        Ext.getCmp('rbDatabase').setValue('rb_' + this.getValue());
                        Ext.getCmp('rpDatabase').setValue('rp_' + this.getValue());
                      }
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Admin Username',
                    value  :'admin',
                    validator  : function(v){
                        var t = /^[a-zA-Z_0-9.@-]+$/;
                        return t.test(v);
                    },
                    id : 'adminUsername',
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Admin Password',
                    inputType : 'password',
                    id: 'adminPassword',
                    enableKeyEvents: true,
                    allowBlank: false,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Confirm Admin Password',
                    inputType : 'password',
                    id : 'confirmPassword',
                    enableKeyEvents: true,
                    allowBlank: false,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  }
                ]
              },
              {
                xtype     : 'fieldset',
                labelAlign: 'left',
                labelWidth: 210,
                //labelWidth: 200,
                //title: 'ProcessMaker Databases',
                items:[
                 new Ext.form.Checkbox({
                   boxLabel: 'Change Database names',
                   id : 'changeDBNames',
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
                     wizard.onClientValidation(4, false);
                   }
                 }),
                 {
                    xtype     : 'textfield',
                    fieldLabel: 'Workflow Database Name <span id="wfDatabaseSpan"></span>',
                    id : 'wfDatabase',
                    value  :'wf_workflow',
                    allowBlank : false,
                    maxLength: 32,
                    validator  : function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Rbac Database Name <span id="rbDatabaseSpan"></span>',
                    id : 'rbDatabase',
                    value  :'rb_workflow',
                    allowBlank : false,
                    maxLength: 32,
                    validator  : function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  },
                  {
                    xtype     : 'textfield',
                    fieldLabel: 'Report Database Name <span id="rpDatabaseSpan"></span>',
                    id : 'rpDatabase',
                    value  :'rp_workflow',
                    allowBlank : false,
                    maxLength: 32,
                    validator  : function(v){
                        var t = /^[a-zA-Z_0-9]+$/;
                        return t.test(v);
                    },
                    disabled: true,
                    enableKeyEvents: true,
                    listeners: {keyup: function() {
                      wizard.onClientValidation(4, false);
                    }}
                  },
                  new Ext.form.Checkbox({
                    boxLabel   : "Delete Databases if exists",
                    id : 'deleteDB',
                    handler: function() {
                      wizard.onClientValidation(4, false);
                    }
                  }),
                  {
                    xtype     : 'displayfield',
                    id  : 'finish_message'
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

});

permissionInfo = {};
function showPermissionInfo()
{
  var text = '';

  for (i=0; i < permissionInfo.error1.length; i++) {
    text += (i+1)+'. '+permissionInfo.error1[i] + "\n";
  }

  w = new Ext.Window({
    layout: 'fit',
    title: 'Non-writable Files',
    width: 550,
    height: 180,
    closable: true,
    resizable: true,
    //html: text,
    plain: true,
    items: [{
      xtype: 'textarea',
      id        : 'permissionInfoText',
      fieldLabel: '',
      anchor: "100%",
      value: text,
      readOnly: true
    }],
    bbar: new Ext.ux.StatusBar({
      defaultText: '',
      id: 'login-statusbar2',
      statusAlign: 'right'
    })
  });

  w.show();
}