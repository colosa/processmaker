/*
 * ProcessMaker Login
 *  Created on date Jul 15, 2011
 * 
 * @author Erik Amaru Ortiz <erik@colosa.com>
 */

var loadMask = function(){
  return {
    init: function() {
      var loading = Ext.get('loading'); 
      var mask = Ext.get('loading-mask');
      mask.setOpacity(0.8);
      mask.shift({
        xy: loading.getXY(),
        width: loading.getWidth(),
        height: loading.getHeight(),
        remove: true,
        duration: 1,
        opacity: 0.3,
        easing: 'bounceOut',
        callback: function(){
          loading.fadeOut({
            duration: 0.2,
            remove: true
          });
          document.getElementById('loginLogo').style.display = 'block';
        }
      });
    }};
}();

Ext.onReady(loadMask.init, loadMask, true);

var Login = function() {
  return {
    /** Properties */
    form   : null,
    window : null,
    enableVirtualKeyboard : false,
    fieldsWidth : 200,
    
    /** Init method */
    init : function() {
      new Ext.KeyMap(document, {
        key: [10, 13],
        fn: function(keycode, e) {
          Login.submit();
        }
      });
      
      Ext.QuickTips.init();
      Ext.form.Field.prototype.msgTarget = 'side';

      if ((PMExt.cookie.read('x-pm-ws')))
        defaultWS = PMExt.cookie.read('x-pm-ws');

      if ((PMExt.cookie.read('x-pm-ws')))
        defaultLang = PMExt.cookie.read('x-pm-lang');
      
      this.initComponents();

      this.window.show();
      Ext.getCmp('userTxt').focus(true, 1000);

      if (typeof flyNotify != 'undefined') {
        Ext.msgBoxSlider.msgTopCenter(flyNotify.type, flyNotify.title, flyNotify.text, flyNotify.time);
      }
    }
  }
}();

Login.initComponents = function()
{
  var userTxt = {
    id        : 'userTxt',
    name      : 'form[USR_USERNAME]',
    fieldLabel: _('ID_USER'),
    allowBlank: false
  }
  
  var passwordTxt = {
    fieldLabel: _('ID_PASSWORD'),
    name      : 'form[USR_PASSWORD]',
    inputType : 'password',
    allowBlank: false,
    
    validationEvent : this.enableVirtualKeyboard == true ? 'blur' : 'keyup',
    enableKeyEvents : true,
    width: this.fieldsWidth, //this.enableVirtualKeyboard == true ? 183 : this.fieldsWidth,
    keyboardConfig: {
      showIcon: true,
      languageSelection: true
    },
    plugins: this.enableVirtualKeyboard == true ? new Ext.ux.plugins.VirtualKeyboard() : null,
    
    listeners: {
      render: function() {
        this.capsWarningTooltip = new Ext.ToolTip({
          target: this.id,
          anchor: 'top',
          width: 305,
          html: '<div class="ux-auth-warning">'+_('ID_CAPS_LOCK_IS_ON')+'</div><br />' +
                '<div>'+_('ID_CAPS_LOCK_ALERT1')+'</div><br />' +
                '<div>'+_('ID_CAPS_LOCK_ALERT2')+'</div>'
        });
        this.capsWarningTooltip.disable();
        this.capsWarningTooltip.on('enable', function() {
            this.disable();
        });
      },
  
      keypress: {
        fn: function(field, e) {
          if(this.forceVirtualKeyboard) {
            field.plugins.expand();
            e.stopEvent();
          }
          else {
            var charCode = e.getCharCode();
            if((e.shiftKey && charCode >= 97 && charCode <= 122) ||
              (!e.shiftKey && charCode >= 65 && charCode <= 90)) {

              field.capsWarningTooltip.show();
            }
            else {
              if(field.capsWarningTooltip.hidden == false) {
                  field.capsWarningTooltip.hide();
              }
            }
          }
        },
        scope: this
      },
      blur: function(field) {
          if(this.capsWarningTooltip.hidden == false) {
              this.capsWarningTooltip.hide();
          }
      }
    }
  }
  
  var workspaceField;
  
  if (wsPrivate) {
    workspaceField = {
      id: 'workspace',
      name: 'form[USER_ENV]',
      fieldLabel: _('ID_WORKSPACE'),
      allowBlank: false
    }
  } else {
    workspaceField = new Ext.form.ComboBox({
      id: 'workspace',
      fieldLabel: _('ID_WORKSPACE'),
      name : 'form[USER_ENV]',
      store: new Ext.data.ArrayStore({
        fields: ['id', 'name'],
        data : workspaces
      }),
      displayField:'name',
      typeAhead    : true,
      mode         : 'local',
      forceSelection: true,
      triggerAction: 'all',
      emptyText    : _('ID_SELECT_WORKSPACE'),
      allowBlank   : false,
      selectOnFocus: true,
      valueField   : 'id',
      editable     : true,
      listeners: {
        afterrender: function(){
          if (defaultWS == '') return;

          var store = workspaceField.getStore();
          var i = store.findExact('id', defaultWS, 0);
          if (i > -1){
            Ext.getCmp('workspace').setValue(store.getAt(i).data.id);
            Ext.getCmp('workspace').setRawValue(store.getAt(i).data.name);
          }
        }
      }
    });
  }

  var languagesCmb = new Ext.form.ComboBox({
    id          : 'language',
    fieldLabel  : _('ID_LAN_LANGUAGE'),
    name        : 'form[USER_LANG]',
    displayField: 'name',
    typeAhead   : true,
    mode        : 'local',
    emptyText   : _('ID_SELECT'),
    allowBlank  : false,
    valueField  : 'id',
    editable    : true,
    selectOnFocus : true,
    forceSelection: true,
    triggerAction : 'all',
    store         : new Ext.data.ArrayStore({
      fields: ['id', 'name'],
      data : languages
    }),
    listeners     : {
      afterrender : function() {
        if (sysLang == '') return;

        var store = languagesCmb.getStore();
        var i = store.findExact('id', sysLang, 0);
        if (i > -1) {
          Ext.getCmp('language').setValue(store.getAt(i).data.id);
          Ext.getCmp('language').setRawValue(store.getAt(i).data.name);
        }
      }
    }
  });

  this.form = new Ext.FormPanel({
    id        : 'login-form',
    name      : 'login_form',
    labelWidth: 80,
    labelAlign: 'right',
    url       : "../main/sysLoginVerify",
    frame     : true,
    width     : 230,
    padding   : 10,
    defaultType : 'textfield',
    monitorValid: true,
    defaults  : {
      width:200
    },
    items: [
      userTxt,
      passwordTxt,
      workspaceField,
      languagesCmb
    ],
    buttons: [{
      text: _('LOGIN'),
      formBind: true,
      handler: Login.submit
    }]
  });

  this.window = new Ext.Window({
    layout: 'fit',
    width: 380,
    height: 210,
    title     : _('LOGIN'),
    iconCls: 'ux-auth-header-icon',
    closable: false,
    resizable: false,
    plain: true,
    draggable: false,
    items: [this.form],
    bbar: new Ext.ux.StatusBar({
      defaultText: '',
      id: 'login-statusbar',
      statusAlign: 'right', // the magic config
      items: []
    })
  });

}

Login.submiting = false;

Login.submit = function()
{
  if (Login.submiting) {
    return false;
  }
  
  Ext.getCmp('login-statusbar').showBusy();
  
  if (!Login.form.getForm().isValid()) {
    Ext.getCmp('login-statusbar').setStatus({
      text: _('ID_VALIDATION_ERRORS'),
      iconCls: 'x-status-error',
      clear: true
    });
    return;
  }

  Login.submiting = true;

  document.getElementById('language').value = Ext.getCmp('language').getValue();

  // persistene on cookie
  PMExt.cookie.create('x-pm-ws', Ext.getCmp('workspace').getValue(), 30);
  PMExt.cookie.create('x-pm-lang', document.getElementById('language').value, 30);

  document.forms[0].action = '../login/sysLoginVerify';
  document.forms[0].submit();
  return;

  Login.form.getForm().submit({
    method: 'POST',
    //waitTitle: '',
    //waitMsg: 'Verifying User...',
    success: function(form, action)
    {
      // persistene on cookie
      PMExt.cookie.create('x-pm-ws', Ext.getCmp('workspace').getValue(), 30);


      serverResponse = Ext.util.JSON.decode(action.response.responseText);
      Ext.getCmp('login-statusbar').setStatus({
          text: serverResponse.message,
          iconCls: 'x-status-valid',
          clear: true // auto-clear after a set interval
      });
      window.location = serverResponse.url;
    },
    failure: function(form, action)
    {
      Login.submiting = false;
      if (action.failureType == 'server') {
        serverResponse = Ext.util.JSON.decode(action.response.responseText);
        Ext.getCmp('login-statusbar').setStatus({
            text: serverResponse.message,
            iconCls: 'x-status-error',
            clear: true // auto-clear after a set interval
        });
        //Ext.msgBoxSlider.msgTopCenter('alert', 'LOGIN ERROR', serverResponse.message, 10);
      }
      else {
        Ext.Msg.alert('ERROR', _('ID_SERVER_PROBLEM') + ' ' + action.response.responseText);
      }
      //Login.form.getForm().reset();
    }
  });
}

Ext.onReady(Login.init, Login, true);

