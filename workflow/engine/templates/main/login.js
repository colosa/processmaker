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
    enableForgotPassword : false,
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

      this.enableVirtualKeyboard = virtualKeyboad;
      this.enableForgotPassword = forgotPasswd;

      this.initComponents();

      this.window.show();
      Ext.getCmp('userTxt').focus(true, 1000);

      if (typeof errMsg != 'undefined') {
        Ext.msgBoxSlider.msgTopCenter('alert', _('ID_ERROR') , errMsg, 10);
      }

      if (typeof flyNotify != 'undefined') {
        Ext.msgBoxSlider.msgTopCenter(flyNotify.type, flyNotify.title, flyNotify.text, flyNotify.time);
      }

      if (flagGettingStarted) {
        this.gettingStartedWindow.show();
      }
      if (flagHeartBeat) {
        processHbInfo();
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

  var usernameTxt = {
    id        : 'usernameTxt',
    name      : 'username',
    fieldLabel: _('ID_USER'),
    allowBlank: false
  }

  var emailTxt = {
    id        : 'emailTxt',
    name      : 'email',
    fieldLabel: _('ID_EMAIL'),
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

    /*,
    listeners : {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          Login.submit();
        }
      }
    }*/
  }

  var forgotPasswordLink = {
    xtype: 'box',
    autoEl: {
        html: '<div style="text-align: right; width: 340; left:0; top: 0px; position:relative; padding-bottom:8px">' +
            '<a href="#" onclick="Login.forgotPassword()" class="login">'+
            _('ID_FORGOT_PASSWORD_Q') + '</a></div>'
    }
  };
  var forgotPasswordBox = {
    xtype: 'box',
    autoEl: 'div',
    height: 4
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
      afterrender : function(){
        var store = languagesCmb.getStore();
        var i = store.findExact('id', defaultLang, 0);
        if (i > -1){
          Ext.getCmp('language').setValue(store.getAt(i).data.id);
          Ext.getCmp('language').setRawValue(store.getAt(i).data.name);
        }
      }
    }
  });


  var formConfig = {
    id        : 'login-form',
    labelWidth: 80,
    labelAlign: 'right',
    bodyStyle   : "padding: 10px;",
    url       : "authentication",
    closeAction: 'hide',
    frame     : true,
    width     : 230,
    padding   : 10,
    defaultType : 'textfield',
    monitorValid: true,
    defaults  : {
      width : this.fieldsWidth
    },
    buttons: [{
      id: 'submit-btn',
      text: _('LOGIN'),
      formBind: true,
      handler: Login.submit
    }]
  };

  formConfig.items = new Array();

  if (this.enableForgotPassword) {
    formConfig.items.push(forgotPasswordLink);
  }
  formConfig.items.push(userTxt);
  formConfig.items.push(passwordTxt);
  formConfig.items.push(languagesCmb);


  this.form = new Ext.FormPanel(formConfig);

  this.forgotPasswordForm = new Ext.FormPanel({
    id        : 'fp-form',
    labelWidth: 80,
    labelAlign: 'right',
    bodyStyle   : "padding: 10px;",
    url       : "forgotPassword",
    closeAction: 'hide',
    frame     : true,
    width     : 230,
    padding   : 10,
    defaultType : 'textfield',
    monitorValid: true,
    defaults  : {
      width : this.fieldsWidth
    },
    items: [
      usernameTxt,
      emailTxt
    ],
    buttons: [{
      id: 'send-btn',
      text: _('ID_SEND'),
      formBind: true,
      handler: Login.sendFpRequest
    }, {
      id: 'cancel-btn',
      text: _('ID_CANCEL'),
      handler: Login.restore
    }]
  });

  this.forgotPasswordForm = new Ext.FormPanel({
    id        : 'fp-form',
    labelWidth: 80,
    labelAlign: 'right',
    bodyStyle   : "padding: 10px;",
    url       : "forgotPassword",
    closeAction: 'hide',
    frame     : true,
    width     : 230,
    padding   : 10,
    defaultType : 'textfield',
    monitorValid: true,
    defaults  : {
      width : this.fieldsWidth
    },
    items: [
      usernameTxt,
      emailTxt
    ],
    buttons: [{
      id: 'send-btn',
      text: _('ID_SEND'),
      formBind: true,
      handler: Login.sendFpRequest
    }, {
      id: 'cancel-btn',
      text: _('ID_CANCEL'),
      handler: Login.restore
    }]
  });

  this.window = new Ext.Window({
    cls: 'x-window-login',
    layout: 'fit',
    title: _('LOGIN'),
    width: 380,
    height: 194, //180,
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

  this.fpWindow = new Ext.Window({
    layout: 'fit',
    title: _('ID_FORGOT_PASSWORD'),
    width: 380,
    height: 150, //180,
    //iconCls: 'ux-auth-header-icon',
    closable: false,
    resizable: false,
    plain: true,
    draggable: false,
    items: [this.forgotPasswordForm],
    bbar: new Ext.ux.StatusBar({
      defaultText: '',
      id: 'login-statusbar2',
      statusAlign: 'right'
    })
  });

  this.gettingStartedWindow = new Ext.Window({
    id: 'gettingStartedWindow',
    layout: 'fit',
    title: '',
    width: 640,
    height: 500, //180,
    //iconCls: 'ux-auth-header-icon',
    closable: true,
    resizable: false,
    plain: true,
    draggable: false,
    modal:true,
    //autoLoad: '../services/login_getStarted.php'
    items: [
      {
        xtype: 'iframepanel',
        defaultSrc : '../services/login_getStarted.php',
        loadMask:{msg:_('ID_LOADING')},
        bodyStyle:{height: (PMExt.getBrowser().screen.height-60) + 'px', overflow:'auto'},
        width:588
      }
    ]
  });
  //Ext.getCmp('login-form').hide();
}

processHbInfo = function() {
  Ext.Ajax.request({
    url : '../services/processHeartBeat_Ajax' ,
    params : {action:'processInformation'},
    success: function ( result, request ) {
      //console.info("");
    },
    failure: function ( result, request) {
      //Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
    }
  });
}

Login.forgotPassword = function()
{
  this.window.hide();
  this.fpWindow.show();
}
Login.restore = function()
{
  Login.window.show();
  Login.fpWindow.hide();
}


Login.sendFpRequest = function()
{
  Ext.getCmp('login-statusbar2').showBusy();

  if (!Login.forgotPasswordForm.getForm().isValid()) {
    Ext.getCmp('login-statusbar2').setStatus({
      text: _('ID_VALIDATION_ERRORS'),
      iconCls: 'x-status-error',
      clear: true
    });
    return;
  }

  Login.forgotPasswordForm.getForm().submit({
    method: 'POST',
    waitTitle: '',
    waitMsg: _('ID_SENDING_REQUEST'),
    success: function(form, action)
    {
      serverResponse = Ext.util.JSON.decode(action.response.responseText);
      Ext.getCmp('login-statusbar2').setStatus({
          text: _('ID_SUCCESS'),
          iconCls: 'x-status-valid',
          clear: true // auto-clear after a set interval
      });
      Ext.msgBoxSlider.msgTopCenter('info', _('ID_INFO'), serverResponse.message, 10);
      setTimeout('Login.restore()', 4000);
    },
    failure: function(form, action)
    {

      if (action.failureType == 'server') {
        serverResponse = Ext.util.JSON.decode(action.response.responseText);
        Ext.getCmp('login-statusbar2').setStatus({
            text: serverResponse.message,
            iconCls: 'x-status-error',
            clear: true // auto-clear after a set interval
        });
        Login.submiting = false;
        //Ext.msgBoxSlider.msgTopCenter('alert', 'LOGIN ERROR', serverResponse.message, 10);
      }
      else {
        Ext.Msg.alert('ERROR', _('ID_SERVER_PROBLEM') + ' ' + action.response.responseText);
      }
      //Login.form.getForm().reset();
    }
  });

}


Login.submiting = false;
Login.submit = function()
{
  if (Login.submiting) {
    return false;
  }

  Login.submiting = true;

  document.getElementById('language').value = Ext.getCmp('language').getValue();
  document.forms[0].action = '../login/authentication';
  document.forms[0].submit();
  return;


  Ext.getCmp('login-statusbar').showBusy();

  if (!Login.form.getForm().isValid()) {
    Ext.getCmp('login-statusbar').setStatus({
      text: _('ID_VALIDATION_ERRORS'),
      iconCls: 'x-status-error',
      clear: true
    });
    return;
  }

  Login.form.getForm().submit({
    method: 'POST',
    //waitTitle: '',
    //waitMsg: 'Verifying User...',
    success: function(form, action)
    {
      serverResponse = Ext.util.JSON.decode(action.response.responseText);
      Ext.getCmp('login-statusbar').setStatus({
          text: serverResponse.message,
          iconCls: 'x-status-valid',
          clear: true // auto-clear after a set interval
      });

      if (typeof urlRequested != 'undefined') {
        window.location = urlRequested;
      }
      else {
        window.location = serverResponse.url;
      }
    },
    failure: function(form, action)
    {

      if (action.failureType == 'server') {
        serverResponse = Ext.util.JSON.decode(action.response.responseText);
        Ext.getCmp('login-statusbar').setStatus({
            text: serverResponse.message,
            iconCls: 'x-status-error',
            clear: true // auto-clear after a set interval
        });
        Login.submiting = false;
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

