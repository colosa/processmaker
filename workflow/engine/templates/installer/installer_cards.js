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
      fieldLabel: _('ID_LANGUAGES'),
      store: store,
      labelWidth: 200,
      displayField:'label',
      typeAhead: true,
      mode: 'local',
      forceSelection: true,
      triggerAction: 'all',
      emptyText:_('ID_EMPTY_LANGUAGE'),
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

        //type = response.success ? 'success' : _('ID_WARNING');
        if (!response.success)
          PMExt.error(_('ID_WARNING'), response.notify + ' <a href="#" onclick="showPermissionInfo(1); return false;">Show non-writable files.</a>');

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
          PMExt.notify(_('ID_WARNING'), response.message, _('ID_WARNING'));

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
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_VALID_WORKSPACE'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('adminUsername').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_VALID_ADMIN_NAME'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() == '') {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_VALID_ADMIN_PASSWORD'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (Ext.getCmp('adminPassword').getValue() != Ext.getCmp('confirmPassword').getValue()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_PASSWORD_CONFIRMATION_INCORRECT'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('wfDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_WORKFLOW_DATABASE_NAME'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('rbDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_RBAC_DATABASE_NAME'), false));
      wizard.onClientValidation(4, false);
      return;
    }
    if (!Ext.getCmp('rpDatabase').isValid()) {
      Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_REPORT_DATABASE_NAME'), false));
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
        var existMsg = '<span style="color: red;">' + _('ID_EXIST') + '</span>';
        var noExistsMsg = '<span style="color: green;">' + _('ID_NO_EXIST') + '</span>';
        var response = Ext.util.JSON.decode(response.responseText);
        Ext.get('wfDatabaseSpan').dom.innerHTML = (response.wfDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rbDatabaseSpan').dom.innerHTML = (response.rbDatabaseExists ? existMsg : noExistsMsg);
        Ext.get('rpDatabaseSpan').dom.innerHTML = (response.rpDatabaseExists ? existMsg : noExistsMsg);

        var dbFlag = ((!response.wfDatabaseExists && !response.rbDatabaseExists && !response.rpDatabaseExists) || Ext.getCmp('deleteDB').getValue());
        wizard.onClientValidation(4, dbFlag);

        if (dbFlag) {
          Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_DATA_CORRECT'), true));
        }
        else {
          Ext.getCmp('finish_message').setValue(getFieldOutput(_('ID_NOT_PASSED'), false));
          PMExt.notify(_('ID_WARNING'), response.errMessage, _('ID_WARNING'), 4)
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
    title : _('ID_PREINSTALLATION'),
    monitorValid : false,
    labelAlign: 'left',
    labelWidth: 200,
    defaults     : {
    },
    items : [
      {
        border    : false,
        html      : _('ID_PREINSTALLATION'),
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
            html: _('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION')
          },

          {
            region: 'center',
            xtype : 'fieldset',
            labelWidth: 200,
            items:[
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_PHP'),
                id  : 'php'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_MYSQL'),
                id  : 'mysql'
              }/*,
              {
                xtype     : 'displayfield',
                fieldLabel: 'MSSQL Support (*)',
                id  : 'mssql'
              }*/,
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_CURL'),
                id  : 'curl'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_OPENSSL'),
                id  : 'openssl'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_DOMXML'),
                id  : 'dom'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_GD'),
                id  : 'gd'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_MULTIBYTESTRING'),
                id  : 'multibyte'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_SOAP'),
                id  : 'soap'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_LDAP'),
                id  : 'ldap'
              },
              {
                xtype     : 'displayfield',
                fieldLabel: _('ID_PROCESSMAKER_REQUIREMENTS_MEMORYLIMIT'),
                id: 'memory',
                value: '5.0 or greater'
              },
              new Ext.Button({
                text : _('ID_CHECK_AGAIN'),
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
    title:_('ID_DIRECTORY_FILE_PERMISSION'),
    monitorValid : false,
    labelAlign: 'left',
    labelWidth: 200,
    defaults     : {
        //labelStyle : 'font-size:11px'
    },
    items : [
      {
        border    : false,
        html: _('ID_DIRECTORY_FILE_PERMISSION'),
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
            html: _('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION_STEP3_1')
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
                fieldLabel: '<span id="pathConfigSpan"></span> '+_('ID_CONFIG_DIRECTORY'),
                id: 'pathConfig',
                width: 430,
                value: path_config,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathLanguagesSpan"></span> '+ _('ID_LANGUAJE_DIRECTORY'),
                id: 'pathLanguages',
                width: 430,
                value: path_languages,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathPluginsSpan"></span> ' + _('ID_PLUGINS_DIRECTORY'),
                id: 'pathPlugins',
                width: 430,
                value: path_plugins,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathXmlformsSpan"></span> ' + _('ID_XMLFROM_DIRECTORY'),
                id: 'pathXmlforms',
                width: 430,
                value: path_xmlforms,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathPublicSpan"></span> ' + _('ID_PUBLIC_INDEX_FILE'),
                id: 'pathPublic',
                width: 430,
                value: path_public,
                disabled: true
              },
              {
                xtype: 'textfield',
                fieldLabel: '<span id="pathSharedSpan"></span> ' + _('ID_WORFLOW_DATA_DIRECTORY'),
                id: 'pathShared',
                width: 430,
                value: path_shared,
                enableKeyEvents: true,
                allowBlank: false,
                blankText: _('ID_WORKFLOW_DATA_DIRECTORY_REQUIRED'),
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
                fieldLabel: '<span id="pathLogFileSpan"></span> ' + _('ID_INSTALLATION_LOG'),
                id: 'pathLogFile',
                width: 430,
                value: path_shared + 'log' + path_sep + 'install.log',
                disabled: true
              },
              new Ext.Button({
                text : _('ID_CHECK_AGAIN'),
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
    title:_('ID_PROCESSMAKER_LICENSE'),
    //monitorValid : false,
    defaults     : {
      labelStyle : 'font-size:12px'
    },
    items : [
      {
        border    : false,
        html:_('ID_PROCESSMAKER_LICENSE'),
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
    title        : _('ID_DATABASE_CONFIGURATION'),
    monitorValid : false,
    items : [
      {
        border    : false,
        html      : _('ID_DATABASE_CONFIGURATION'),
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
            html: _('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION_STEP4_1')
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
                    fieldLabel: _('ID_DATABASE_ENGINE'),
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
                    fieldLabel: _('ID_HOST_NAME_LABEL'),
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
                    fieldLabel: _('ID_PORT'),
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
                    fieldLabel: _('ID_USERNAME'),
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
                    fieldLabel: _('ID_PASSWORD'),
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
                    text : _('ID_TEST_CONNECTION'),
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
    title        : _('ID_WORKSPACE_CONFIGURATION'),
    monitorValid : false,
    defaults     : {
      labelStyle : 'font-size:11px'
    },
    items : [
      {
        border    : false,
        html      : _('ID_WORKSPACE_CONFIGURATION'),
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
            html: _('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION_STEP5')
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
                    fieldLabel: _('ID_WORKSPACE_NAME'),
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
                    fieldLabel: _('ID_ADMIN_USERNAME'),
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
                    fieldLabel: _('ID_ADMIN_PASSWORD_LABEL'),
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
                    fieldLabel: _('ID_ADMIN_PASSWORD'),
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
                   boxLabel: _('ID_CHANGE_DATABASE_NAME'),
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
                    fieldLabel: _('ID_WF_DATABASE_NAME') + ' <span id="wfDatabaseSpan"></span>',
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
                    fieldLabel: _('ID_RB_DATABASE_NAME') + ' <span id="rbDatabaseSpan"></span>',
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
                    fieldLabel: _('ID_RP_DATABASE_NAME') + ' <span id="rpDatabaseSpan"></span>',
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
                    boxLabel   : _('ID_DELETE_DATABASES'),
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
                    text: _('ID_CHECK_WORKSPACE_CONFIGURATION'),
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
    title: _('ID_NON_WRITABLE_FILES'),
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