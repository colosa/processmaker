var storeCountry;
var storeRegion;
var storeLocation;
var storeReplacedBy;
var storeCalendar;
var storeRole;
var storeLanguage;

var storeDefaultMainMenuOption;
var storeDefaultCasesMenuOption;

var comboCountry;
var comboRegion;
var comboLocation;
var comboReplacedBy;
var comboCalendar;
var comboRole;
var cboTimeZone;
var comboLanguage;

var comboDefaultMainMenuOption;
var comboDefaultCasesMenuOption;

var frmDetails;
var frmSumary;

var allowBlackStatus;
var displayPreferences;
var box;
var infoMode;
var global = {};
var usernameText;
var previousUsername = '';
var canEdit = true;
var flagPoliciesPassword = false;
var flagValidateUsername = false;
//var rendeToPage='document.body';
var userRoleLoad = '';

var PROCESSMAKER_ADMIN = 'PROCESSMAKER_ADMIN';
var usertmp;

global.IC_UID        = '';
global.IS_UID        = '';
global.USR_FIRSTNAME = '';
global.aux           = '';

Ext.onReady(function () {
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

  box = new Ext.BoxComponent({
    width          : 100,
    height         : 80,
    fieldLabel     : '&nbsp',
    labelSeparator : '&nbsp',
    autoEl         : {
      tag   : 'img',
      src   : 'users_ViewPhotoGrid?h=' + Math.random() +'&pUID=' + USR_UID + '',
      align : 'left'
    }

  });

  displayPreferences = "display: block;";

  if (MODE == "edit" || MODE == "") {
      flagPoliciesPassword = true;
  }

  if (USR_UID != "") {
      //Mode edit
      allowBlackStatus = true;

      box.setVisible(true);
      box.enable();

      if (infoMode) {
          //Mode info
          box.setVisible(false);
          box.disable();
      } else {
          canEdit  = false;
      }
  } else {
      //Mode new
      allowBlackStatus = false;

      box.setVisible(false);
      box.disable();

      canEdit  = false;
  }

  var profileFields = new Ext.form.FieldSet({
    title : _('ID_PROFILE'),
    items : [
    box,
    {
        xtype      : 'fileuploadfield',
        id         : 'USR_PHOTO',
        emptyText  : _('ID_PLEASE_SELECT_PHOTO'),
        fieldLabel : _('ID_PHOTO'),
        name       : 'USR_PHOTO',
        buttonText : '',
        width      : 260,
        buttonCfg  : {
          iconCls : 'upload-icon'
        }

      },{
        xtype      : 'label',
        id         : 'lblMaxFileSize',
        fieldLabel : _('ID_MAX_FILE_SIZE'),
        text       : MAX_FILES_SIZE,
        width      : 400

      }
    ]
  });

  storeCountry = new Ext.data.Store({
        proxy : new Ext.data.HttpProxy({
          url    : "usersAjax",
          method : "POST"
        }),

        baseParams: {"action": "countryList"},

        reader : new Ext.data.JsonReader({
          fields : [ {
            name : "IC_UID"
          }, {
            name : "IC_NAME"
          }]
        })
  });

  comboCountry = new Ext.form.ComboBox({
      fieldLabel    : _("ID_COUNTRY"),
      hiddenName    : "USR_COUNTRY",
      id            : "USR_COUNTRY",
      store         : storeCountry,
      valueField    : "IC_UID",
      displayField  : "IC_NAME",
      triggerAction : "all",
      emptyText     : _("ID_SELECT"),
      selectOnFocus : true,
      width         : 180,
      autocomplete  : true,
      typeAhead     : true,
      mode          : "local",
      listeners : {
        select : function (combo, record, index) {
          global.IC_UID = this.getValue();
          comboRegion.store.removeAll();
          comboLocation.store.removeAll();
          comboRegion.clearValue();

          storeRegion.load({
              params : {
                  IC_UID : global.IC_UID
              }
          });

          comboLocation.setValue("");
          comboRegion.store.on("load", function (store) {
            comboRegion.setValue("");
          });
        }
      }
  });

  storeRegion  = new Ext.data.Store({
    proxy : new Ext.data.HttpProxy({
      url    : "usersAjax",
      method : "POST"
    }),

    baseParams: {"action": "stateList"},

    reader : new Ext.data.JsonReader({
      fields : [{
        name : "IS_UID"
      }, {
        name : "IS_NAME"
      }]
    })
  });

  comboRegion = new Ext.form.ComboBox({
    fieldLabel    : _("ID_STATE_REGION"),
    hiddenName    : "USR_REGION",
    id            : "USR_REGION",
    store         : storeRegion,
    valueField    : "IS_UID",
    displayField  : "IS_NAME",
    triggerAction : "all",
    emptyText     : _("ID_SELECT"),
    selectOnFocus : true,
    width         : 180,
    autocomplete  : true,
    typeAhead     : true,
    mode          : "local",
    listeners : {
      select : function (combo, record, index) {
        global.IS_UID = this.getValue();
        comboLocation.enable();
        comboLocation.clearValue();

        storeLocation.load({
          params : {
            IC_UID : global.IC_UID,
            IS_UID : global.IS_UID
          }
        });

        comboLocation.store.on("load", function (store) {
          comboLocation.setValue("");
        });
      }
    }
  });

  storeLocation = new Ext.data.Store({
    proxy : new Ext.data.HttpProxy({
      url : "usersAjax",
      method : "POST"
    }),

    baseParams: {"action": "locationList"},

    reader : new Ext.data.JsonReader({
      fields : [{
        name : "IL_UID"
      }, {
        name : "IL_NAME"
      }]
    })
  });

  comboLocation = new Ext.form.ComboBox({
    fieldLabel    : _('ID_LOCATION'),
    hiddenName    : 'USR_LOCATION',
    id            : 'USR_LOCATION',
    store         : storeLocation,
    valueField    : 'IL_UID',
    displayField  : 'IL_NAME',
    triggerAction : 'all',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 180,
    autocomplete  : true,
    typeAhead     : true,
    mode          : 'local'
  });

  storeReplacedBy = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
          url: "usersAjax",
          method: "POST"
      }),

      baseParams: {
          "action": "usersList",
          "USR_UID": USR_UID
      },

      reader: new Ext.data.JsonReader({
          fields: [
              {name : "USR_UID"},
              {name : "USER_FULLNAME"}
          ]
      })
  });

  comboReplacedBy = new Ext.form.ComboBox({
      id: "USR_REPLACED_BY",
      hiddenName: "USR_REPLACED_BY",

      store: storeReplacedBy,
      valueField: "USR_UID",
      displayField: "USER_FULLNAME",

      queryParam: "filter",

      fieldLabel: _("ID_REPLACED_BY"),
      emptyText: "- " + _("ID_NONE") + " -",
      minChars: 1,
      hideTrigger: true,

      width: 260,
      triggerAction: "all"
  });

  var dateField = new Ext.form.DateField({
    id         : "USR_DUE_DATE",
    fieldLabel : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _("ID_EXPIRATION_DATE")) +'"> * </span>' + _("ID_EXPIRATION_DATE"),
    format     : "Y-m-d",
    allowBlank:false,
    editable   : true,
    width      : 120,
    value      : (new Date().add(Date.YEAR, EXPIRATION_DATE)).format("Y-m-d")
  });

  storeCalendar = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
          url: "usersAjax",
          method : "POST"
      }),

      baseParams: {"action": "availableCalendars"},

      reader: new Ext.data.JsonReader({
          fields: [
              {name: "CALENDAR_UID"},
              {name: "CALENDAR_NAME"}
          ]
      })
  });

  comboCalendar = new Ext.form.ComboBox({
    fieldLabel : _('ID_CALENDAR'),
    hiddenName : 'USR_CALENDAR',
    id         : 'USR_CALENDAR',
    store      : storeCalendar,
    valueField    : 'CALENDAR_UID',
    displayField  : 'CALENDAR_NAME',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 180,
    selectOnFocus : true,
    editable      : false,
    allowBlank    : false,
    triggerAction : 'all',
    mode          : 'local',
    tpl: '<tpl for="."><div class="x-combo-list-item">{CALENDAR_NAME:htmlEncode}</div></tpl>'
  });

  var status = new Ext.data.SimpleStore({
      fields: ["USR_STATUS_VALUE", "status"],
      data: [["ACTIVE", _("ID_ACTIVE")], ["INACTIVE", _("ID_INACTIVE")], ["VACATION", _("ID_VACATION")]]
  });

  var comboStatus = new Ext.form.ComboBox({
    xtype         : 'combo',
    name          : 'status',
    fieldLabel    : _('ID_STATUS'),
    hiddenName    : 'USR_STATUS',
    id            : 'USR_STATUS',
    mode          : 'local',
    store         : status,
    displayField  : 'status',
    valueField    : 'USR_STATUS_VALUE',
    width         : 120,
    typeAhead     : true,
    triggerAction : 'all',
    editable      : false,
    value         : 'ACTIVE'
  });

  storeRole = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
          url: "usersAjax",
          method: "POST"
      }),

      baseParams: {"action" : "rolesList"},

      reader: new Ext.data.JsonReader({
          fields: [
              {name: "ROL_UID"},
              {name: "ROL_CODE"}
          ]
      })
  });

  comboRole = new Ext.form.ComboBox({
    fieldLabel    : _('ID_ROLE'),
    hiddenName    : 'USR_ROLE',
    id            : 'USR_ROLE',
    store         : storeRole,
    valueField    : 'ROL_UID',
    displayField  : 'ROL_CODE',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 260,
    selectOnFocus : true,
    editable      : false,
    allowBlank    : false,
    triggerAction : 'all',
    mode          : 'local'
  });

    cboTimeZone = new Ext.form.ComboBox({
        id: "cboTimeZone",
        name: "USR_TIME_ZONE",

        valueField: "id",
        displayField: "value",
        value: SYSTEM_TIME_ZONE,
        store: new Ext.data.ArrayStore({
            idIndex: 0,
            fields: ["id", "value"],
            data: TIME_ZONE_DATA
        }),

        fieldLabel: _("ID_TIME_ZONE"),

        triggerAction: "all",
        mode: "local",
        editable: false,
        width: 260,

        hidden: !(__SYSTEM_UTC_TIME_ZONE__ == 1)
    });

  /*----------------------------------********---------------------------------*/

    var informationFields = new Ext.form.FieldSet({
      title : _('ID_PERSONAL_INFORMATION'),
      items : [
        {
          id         : 'USR_FIRSTNAME',
          fieldLabel : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_FIRSTNAME')) +'"> * </span>' + _('ID_FIRSTNAME'),
          xtype      : 'textfield',
          width      : 260,
          allowBlank : false,
          listeners: {
            'change': function(field, newVal, oldVal){
                var fname = newVal.replace(/^\s+/,'').replace(/\s+$/,'');
                field.setValue(fname.trim());
              }
          }
        },
        {
          id         : 'USR_LASTNAME',
          fieldLabel : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_LASTNAME')) +'"> * </span>' + _('ID_LASTNAME'),
          xtype      : 'textfield',
          width      : 260,
          allowBlank : false,
          listeners: {
            'change': function(field, newVal, oldVal){
                var lname = newVal.replace(/^\s+/,'').replace(/\s+$/,'');
                field.setValue(lname.trim());
              }
          }
        },
        {
          id         : 'USR_USERNAME',
          fieldLabel : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_USER_ID')) +'"> * </span>' + _('ID_USER_ID'),
          xtype      : 'textfield',
          width      : 260,
          allowBlank : false,
          hidden     : (typeof EDITPROFILE != "undefined" && EDITPROFILE == 1)? true : false,
          listeners: {
            blur : function(ob)
            {
              // trim
              this.value = this.getValue().replace(/^\s+|\s+$/g,"");
              document.getElementById('USR_USERNAME').value = this.getValue().replace(/^\s+|\s+$/g,"");

              Ext.getCmp('saveB').disable();
              Ext.getCmp('cancelB').disable();

              var spanAjax  = '<span style="font: 9px tahoma,arial,helvetica,sans-serif;">';
              var imageAjax = '<img width="13" height="13" border="0" src="/images/ajax-loader.gif">';
              var labelAjax = _('ID_USERNAME_TESTING');

              Ext.getCmp('usernameReview').setText(spanAjax + imageAjax + labelAjax + '</span>', false);
              Ext.getCmp('usernameReview').setVisible(true);

              usernameText = this.getValue();

              validateUserName();

              Ext.getCmp('usernameReview').setVisible(true);
            }
          }
        },
        {
          xtype: 'label',
          fieldLabel: ' ',
          id:'usernameReview',
          width: 300,
          labelSeparator: ''
        },
        {
          id         : 'USR_EMAIL',
          fieldLabel : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_EMAIL')) +'"> * </span>' + _('ID_EMAIL'),
          vtype      : 'email',
          xtype      : 'textfield',
          width      : 260,
          allowBlank : false
        },
        {
          id             : 'USR_ADDRESS',
          xtype          : 'textarea',
          name           : 'USR_ADDRESS',
          fieldLabel     : _('ID_ADDRESS'),
          labelSeparator : '',
          height         : 50,
          width          : 260
        },
        {
          id         : 'USR_ZIP_CODE',
          fieldLabel : _('ID_ZIP_CODE'),
          xtype      : 'textfield',
          width      : 260
        },
        comboCountry,
        comboRegion,
        comboLocation,
        {
          id         : 'USR_PHONE',
          fieldLabel : _('ID_PHONE'),
          xtype      : 'textfield',
          width      : 260
        },
        {
          id         : 'USR_POSITION',
          fieldLabel : _('ID_POSITION'),
          xtype      : 'textfield',
          width      : 260
        },
        comboReplacedBy,
        dateField,
        comboCalendar,
        comboStatus,
        comboRole,
        cboTimeZone
        /*----------------------------------********---------------------------------*/
      ]
  });
    /*----------------------------------********---------------------------------*/
  var passwordFields = new Ext.form.FieldSet({
    title : _('ID_CHANGE_PASSWORD'),
    items : [
       {
          xtype      : "textfield",
          id         : "currentPassword",
          name       : "currentPassword",
          fieldLabel : _("ID_PASSWORD_CURRENT"),
          inputType  : "password",
          hidden     : (typeof EDITPROFILE != "undefined" && EDITPROFILE == 1)? false : true,
          width      : 260
       },
       {
          id         : 'USR_NEW_PASS',
          fieldLabel : MODE == 'edit' ? _('ID_NEW_PASSWORD') : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_NEW_PASSWORD')) +'"> * </span>' + _('ID_NEW_PASSWORD'),
          xtype      : 'textfield',
          inputType  : 'password',
          width      : 260,
          allowBlank : allowBlackStatus,
          listeners: {
            blur : function(ob)
            {
              Ext.getCmp('saveB').disable();
              Ext.getCmp('cancelB').disable();
              var spanAjax = '<span style="font: 9px tahoma,arial,helvetica,sans-serif;">';
              var imageAjax = '<img width="13" height="13" border="0" src="/images/ajax-loader.gif">';
              var labelAjax = _('ID_PASSWORD_TESTING');

              Ext.getCmp('passwordReview').setText(spanAjax + imageAjax + labelAjax + '</span>', false);
              Ext.getCmp('passwordReview').setVisible(true);

              var passwordText = this.getValue();

              Ext.Ajax.request({
                url    : 'usersAjax',
                method:'POST',
                params : {
                  'action'        : 'testPassword',
                  'PASSWORD_TEXT' : passwordText
                },
                success: function(r,o){
                  var resp = Ext.util.JSON.decode(r.responseText);

                  if (resp.STATUS) {
                    flagPoliciesPassword = true;
                  } else {
                    flagPoliciesPassword = false;
                  }

                  Ext.getCmp('passwordReview').setText(resp.DESCRIPTION, false);
                  Ext.getCmp('saveB').enable();
                  Ext.getCmp('cancelB').enable();
                },
                failure: function () {
                  Ext.MessageBox.show({
                    title: _('ID_ERROR'),
                    msg: _('ID_FAILED_STORE_DATA'),
                    buttons: Ext.MessageBox.OK,
                    animEl: 'mb9',
                    icon: Ext.MessageBox.ERROR
                  });
                  Ext.getCmp('saveB').enable();
                  Ext.getCmp('cancelB').enable();
                }
              });

              Ext.getCmp('passwordReview').setVisible(true);

              if (Ext.getCmp('USR_CNF_PASS').getValue() != '') {
                userExecuteEvent(document.getElementById('USR_CNF_PASS'), 'blur');
              }
            }
          }
        },
        {
          xtype: 'label',
          fieldLabel: ' ',
          id:'passwordReview',
          width: 300,
          labelSeparator: ''
        },
        {
          id         : 'USR_CNF_PASS',
          fieldLabel : MODE == 'edit' ? _('ID_CONFIRM_PASSWORD') : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_CONFIRM_PASSWORD')) +'"> * </span>' + _('ID_CONFIRM_PASSWORD'),
          xtype      : 'textfield',
          inputType  : 'password',
          width      : 260,
          allowBlank : allowBlackStatus,
          listeners: {
            blur : function(ob)
            {
              var passwordText    = Ext.getCmp('USR_NEW_PASS').getValue();
              var passwordConfirm = this.getValue();

              if (passwordText != passwordConfirm) {
                var spanErrorConfirm  = '<span style="color: red; font: 9px tahoma,arial,helvetica,sans-serif;">';
                var imageErrorConfirm = '<img width="13" height="13" border="0" src="/images/delete.png">';
                var labelErrorConfirm = _('ID_NEW_PASS_SAME_OLD_PASS');

                Ext.getCmp('passwordConfirm').setText(spanErrorConfirm + imageErrorConfirm + labelErrorConfirm + '</span>', false);
                Ext.getCmp('passwordConfirm').setVisible(true);
              } else {
                Ext.getCmp('passwordConfirm').setVisible(false);
              }
            }
          }
        },
        {
          xtype: 'label',
          fieldLabel: ' ',
          id:'passwordConfirm',
          width: 300,
          labelSeparator: ''
        }

      ]
    });

    var accountOptions = new Ext.form.FieldSet({
        title: _('ID_ACCOUNT_OPTIONS'),
        items: [{
            xtype: 'checkbox',
            id: 'USR_LOGGED_NEXT_TIME',
            name: 'USR_LOGGED_NEXT_TIME',
            boxLabel: _('ID_USER_MUST_CHANGE_PASSWORD_AT_NEXT_LOGON'),
            value: 0,
            inputValue: 1,
            uncheckedValue: 0
        }]
    });

  storeDefaultMainMenuOption = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
          url: "usersAjax",
          method: "POST"
      }),

      baseParams: {"action": "defaultMainMenuOptionList"},

      reader: new Ext.data.JsonReader({
          fields: [{
            name: "id"
          }, {
            name: "name"
          }]
      })
  });

  comboDefaultMainMenuOption = new Ext.form.ComboBox({
    fieldLabel : _("ID_DEFAULT_MAIN_MENU_OPTION"),
    hiddenName : "PREF_DEFAULT_MENUSELECTED",
    id         : "PREF_DEFAULT_MENUSELECTED",
    store      : storeDefaultMainMenuOption,
    valueField    : "id",
    displayField  : "name",
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 260,
    selectOnFocus : true,
    editable      : false,
    triggerAction : "all",
    mode          : "local"
  });

  storeDefaultCasesMenuOption = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
        url: "usersAjax",
        method: "POST"
      }),

      baseParams: {"action": "defaultCasesMenuOptionList"},

      reader: new Ext.data.JsonReader({
          fields: [{
            name : "id"
          }, {
            name : "name"
          }]
      })
  });

  comboDefaultCasesMenuOption = new Ext.form.ComboBox({
    fieldLabel : _("ID_DEFAULT_CASES_MENU_OPTION"),
    hiddenName : "PREF_DEFAULT_CASES_MENUSELECTED",
    id         : "PREF_DEFAULT_CASES_MENUSELECTED",
    store      : storeDefaultCasesMenuOption,
    valueField    : "id",
    displayField  : "name",
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 260,
    selectOnFocus : true,
    editable      : true,
    triggerAction : "all",
    mode          : "local"
  });

    comboDefaultCasesMenuOption.disable();
    comboDefaultMainMenuOption.on('select', function (cmb, record, index) {
        comboDefaultCasesMenuOption.disable();
        if (record.get('id') == 'PM_CASES') {
            comboDefaultCasesMenuOption.setReadOnly(false);
            comboDefaultCasesMenuOption.enable();
        }
    }, this);

  var preferencesFields = new Ext.form.FieldSet({
    title : _('ID_PREFERENCES'),
    // for display or not a preferences FieldSet
    style : displayPreferences,
    items : [{
        xtype : 'hidden',
        name  : 'PREF_DEFAULT_LANG',
        value : ''
      },
      comboDefaultMainMenuOption,
      comboDefaultCasesMenuOption
    ]
  });
  
  var csrfToken = {
      xtype : 'hidden',
      name  : '_token',
      value : document.querySelector('meta[name="csrf-token"]').content
    };

  frmDetails = new Ext.FormPanel({
    id            : 'frmDetails',
    labelWidth    : 250,
    labelAlign    :'right',
    autoScroll    : true,
    fileUpload    : true,
    width         : 800,
    bodyStyle     : 'padding:10px',
    waitMsgTarget : true,
    frame         : true,
    defaults : {
      anchor     : '100%',
      allowBlank : false,
      resizable  : true,
      msgTarget  : 'side',
      align      : 'center'
    },
    items : [
      csrfToken,
      informationFields,
      /*----------------------------------********---------------------------------*/
      passwordFields,
      accountOptions,
      profileFields,
      preferencesFields
    ],
    buttons : [
      {
        text   : _('ID_SAVE'),
        id     : 'saveB',
        handler: saveUser
      },
      {
        text    : _('ID_CANCEL'),
        id      : 'cancelB',
        handler : function(){
          if (!infoMode) {
            location.href = 'users_List';
          }
          else{
            frmDetails.hide();
            frmSumary.show();
          }
          //location.href = 'users_List';
        }
      }
    ]
  });

  //USERS SUMMARY
  box2 = new Ext.BoxComponent({
    width: 100,
    height: 80,
    fieldLabel     : '&nbsp',
    labelSeparator : '&nbsp',
    autoEl         : {
      tag   : 'img',
      src   : 'users_ViewPhotoGrid?h=' + Math.random() +'&pUID=' + USR_UID + '',
      align : 'left'}
  });

  profileFields2 = new Ext.form.FieldSet({
    title : _('ID_PROFILE'),
    items : [
      box2
    ]
  });

  informationFields2 = new Ext.form.FieldSet({
    title : _('ID_PERSONAL_INFORMATION'),
    items : [
      {
        id         : 'USR_FIRSTNAME2',
        fieldLabel : _('ID_FIRSTNAME'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_LASTNAME2',
        fieldLabel : _('ID_LASTNAME'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_USERNAME2',
        fieldLabel : _('ID_USER_ID'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_EMAIL2',
        fieldLabel : _('ID_EMAIL'),
        xtype      : 'label',
        width      : 260
      },
      {
        xtype      : 'label',
        id         : 'USR_ADDRESS2',
        fieldLabel : _('ID_ADDRESS'),
        width      : 260
      },
      {
        id         : 'USR_ZIP_CODE2',
        fieldLabel : _('ID_ZIP_CODE'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_COUNTRY2',
        fieldLabel : _('ID_COUNTRY'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_CITY2',
        fieldLabel : _('ID_STATE_REGION'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_LOCATION2',
        fieldLabel : _('ID_LOCATION'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_PHONE2',
        fieldLabel : _('ID_PHONE'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_POSITION2',
        fieldLabel : _('ID_POSITION'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_REPLACED_BY2',
        fieldLabel : _('ID_REPLACED_BY'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_DUE_DATE2',
        fieldLabel : _('ID_EXPIRATION_DATE'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_CALENDAR2',
        fieldLabel : _('ID_CALENDAR'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_STATUS2',
        fieldLabel : _('ID_STATUS'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'USR_ROLE2',
        fieldLabel : _('ID_ROLE'),
        xtype      : 'label',
        width      : 260
      },
      {
          id: "USR_TIME_ZONE2",
          fieldLabel: _("ID_TIME_ZONE"),
          xtype: "label",
          width: 260,

          hidden: !(__SYSTEM_UTC_TIME_ZONE__ == 1)
      },
      {
        id         : 'USR_DEFAULT_LANG2',
        fieldLabel : _('ID_DEFAULT_LANGUAGE'),
        xtype      : 'label',
        width      : 260,
        hidden     : !(LANGUAGE_MANAGEMENT == 1)
      }
    ]
  });
    /*----------------------------------********---------------------------------*/
  passwordFields2 = new Ext.form.FieldSet({
    title : _('ID_PASSWORD'),
    items : [
      {
        id         : 'USR_PASSWORD2',
        fieldLabel : _('ID_PASSWORD'),
        xtype      : 'label',
        width      : 260,
        text       : '  *******************'
      }
    ]
  });

  preferencesFields2 = new Ext.form.FieldSet({
    title : _('ID_PREFERENCES'),
    // for display or not a preferences FieldSet
    style : displayPreferences,
    items : [
      {
        id         : 'PREF_DEFAULT_MAIN_MENU_OPTION2',
        fieldLabel : _('ID_DEFAULT_MAIN_MENU_OPTION'),
        xtype      : 'label',
        width      : 260
      },
      {
        id         : 'PREF_DEFAULT_CASES_MENUSELECTED2',
        fieldLabel : _('ID_DEFAULT_CASES_MENU_OPTION'),
        xtype      : 'label',
        width      : 260
      }
    ]
  });

  frmSumary = new Ext.FormPanel({
    id            : 'frmSumary',
    labelWidth    : 320,
    labelAlign    : 'right',
    autoScroll    : true,
    fileUpload    : true,
    width         : 800,
    //height:1000,
    bodyStyle     : 'padding:10px',
    waitMsgTarget : true,
    frame         : true,
    items         : [
      box2,
      //profileFields2,
      informationFields2,
      /*----------------------------------********---------------------------------*/
      //passwordFields2,
      preferencesFields2
           ],
    buttons : [
      {
        text    : _('ID_EDIT'),
        handler : editUser,
        hidden  : canEdit
      }
    ]
  });

  if (USR_UID != "") {
      //Mode edit
      loadUserData();
  } else {
      //Mode new
      loadData();
  }

  if (infoMode) {
      document.body.appendChild(defineUserPanel());
      frmSumary.render('users-panel');
  } else {
      frmDetails.render(document.body);
  }

  Ext.getCmp('passwordReview').setVisible(false);
  Ext.getCmp('passwordConfirm').setVisible(false);
  Ext.getCmp('usernameReview').setVisible(false);

  var spanAjax  = '<span style="font: 9px tahoma,arial,helvetica,sans-serif;">';
  var imageAjax = '<img width="13" height="13" border="0" src="/images/ajax-loader.gif">';
  var labelAjax = _('ID_PASSWORD_TESTING');

  Ext.getCmp('passwordReview').setText(spanAjax + imageAjax + labelAjax + '</span>', false);

  var labelAjax = _('ID_USERNAME_TESTING');

  Ext.getCmp('usernameReview').setText(spanAjax + imageAjax + labelAjax + '</span>', false);
});

function defineUserPanel()
{
  var isIE           = ( navigator.userAgent.indexOf('MSIE')>0 ) ? true : false;
  var eDivPanel      = document.createElement("div");
  var eDivUsersPanel = document.createElement("div");
  eDivPanel.setAttribute('id', 'panel');
  eDivUsersPanel.setAttribute('id', 'users-panel');

  if (isIE) {
    eDivPanel.style.setAttribute('text-align','center');
    eDivPanel.style.setAttribute('margin','0px 0px');
    eDivUsersPanel.style.setAttribute('width','800px');
    eDivUsersPanel.style.setAttribute('margin','0px auto');
    eDivUsersPanel.style.setAttribute('text-align','left');
  } else {
    eDivPanel.style.setProperty('text-align','center',null);
    eDivPanel.style.setProperty('margin','0px 0px',null);
    eDivUsersPanel.style.setProperty('width','800px',null);
    eDivUsersPanel.style.setProperty('margin','0px auto',null);
    eDivUsersPanel.style.setProperty('text-align','left',null);
  }

  eDivPanel.appendChild(eDivUsersPanel);

  return eDivPanel;
}

function editUser()
{
    document.body.appendChild(defineUserPanel());
    frmDetails.render('users-panel');

    frmSumary.hide();

    if (typeof(usertmp) != "undefined") {
        frmDetails.getForm().findField("USR_REPLACED_BY").setValue(usertmp.USR_REPLACED_BY);
        frmDetails.getForm().findField("USR_REPLACED_BY").setRawValue(usertmp.REPLACED_NAME);
    }

    frmDetails.show();
    if (window.canEditCalendar === true) {
        comboCalendar.setReadOnly(false);
    }
}

function validateUserName() {
  Ext.Ajax.request({
    url    : 'usersAjax',
    method : 'POST',
    params : {
      'action'       : 'testUsername',
      'USR_UID'      : USR_UID,
      'NEW_USERNAME' : usernameText
    },
    success: function (r, o) {
      var resp = Ext.util.JSON.decode(r.responseText);

      if (resp.exists) {
        flagValidateUsername = false;
        Ext.getCmp('saveB').disable();
        usernameText = '';
      } else {
        flagValidateUsername = true;
      }

      Ext.getCmp('usernameReview').setText(resp.descriptionText, false);
      Ext.getCmp('saveB').enable();
      Ext.getCmp('cancelB').enable();
    },
    failure: function () {
      Ext.MessageBox.show({
        title: _('ID_ERROR'),
        msg: _('ID_FAILED_STORE_DATA'),
        buttons: Ext.MessageBox.OK,
        animEl: 'mb9',
        icon: Ext.MessageBox.ERROR
      });
      Ext.getCmp('saveB').enable();
      Ext.getCmp('cancelB').enable();
    }
  });
}

function userFrmEditSubmit()
{
    if (typeof(usertmp) !== "undefined" &&
        usertmp.REPLACED_NAME === frmDetails.getForm().findField("USR_REPLACED_BY").getRawValue()
    ) {
        frmDetails.getForm().findField("USR_REPLACED_BY").setValue(usertmp.USR_REPLACED_BY);
        frmDetails.getForm().findField("USR_REPLACED_BY").setRawValue(usertmp.REPLACED_NAME);
    }

    Ext.getCmp("frmDetails").getForm().submit({
      url    : "usersAjax",
      params : {
        action: __ACTION__,
        USR_UID  : USR_UID,
        USR_CITY : global.IS_UID
      },
      waitMsg : _("ID_SAVING"),
      waitTitle : "&nbsp;",
      timeout : 36000,
      success : function (obj, resp) {
        if (!infoMode) {
          location.href = "users_List";
        } else {
         location.href = "../users/myInfo?type=reload";
        }

      },
      failure : function (obj, resp) {
        if (typeof resp.result  == "undefined")
        {
          Ext.Msg.alert(_("ID_ERROR"), _("ID_SOME_FIELDS_REQUIRED"));
        } else{
          if (resp.result.msg){
            var message = resp.result.msg.split(",");
            Ext.Msg.alert(_("ID_WARNING"), "<strong>"+message[0]+"<strong><br/><br/>"+message[1]+"<br/><br/>"+message[2]);
          }

          if (resp.result.fileError) {
            Ext.Msg.alert(_("ID_ERROR"), _("ID_FILE_TOO_BIG"));
          }

          if (resp.result.error) {
            Ext.Msg.alert(_("ID_ERROR"), resp.result.error);
          }
        }
      }
    });
}


function saveUser()
{
  if (Ext.getCmp('USR_USERNAME').getValue() != '') {
    if (previousUsername != '') {
      if (Ext.getCmp('USR_USERNAME').getValue() != previousUsername) {
        if (!flagValidateUsername) {
          Ext.Msg.alert( _('ID_ERROR'), Ext.getCmp('usernameReview').html);
          return false;
        }
      }
    } else {
      if (!flagValidateUsername) {
        Ext.Msg.alert( _('ID_ERROR'), Ext.getCmp('usernameReview').html);
        return false;
      }
    }
    /*----------------------------------********---------------------------------*/

    if (USR_UID == '00000000000000000000000000000001') {
        if (Ext.getCmp('USR_ROLE').getValue() != PROCESSMAKER_ADMIN) {
            Ext.Msg.alert( _('ID_ERROR'), _('ID_ADMINISTRATOR_ROLE_CANT_CHANGED'));
            return false;
        }
    }

  } else {
    Ext.Msg.alert( _('ID_ERROR'), _('ID_MSG_ERROR_USR_USERNAME'));
    return false;
  }

  if (!flagPoliciesPassword) {
    if (Ext.getCmp('USR_NEW_PASS').getValue() == '') {
      Ext.Msg.alert( _('ID_ERROR'), _('ID_PASSWD_REQUIRED'));
    } else {
      Ext.Msg.alert( _('ID_ERROR'), Ext.getCmp('passwordReview').html);
    }
    return false;
  }

  var newPass  = frmDetails.getForm().findField('USR_NEW_PASS').getValue();
  var confPass = frmDetails.getForm().findField('USR_CNF_PASS').getValue();

  if (confPass === newPass) {
      if(typeof(EDITPROFILE) != "undefined" && EDITPROFILE == 1 && newPass != "") {
        var currentPassword = Ext.getCmp("currentPassword").getValue();

        if(currentPassword != "") {
            Ext.Ajax.request({
                url:    "usersAjax",
                method: "POST",

                params: {
                    action:   "passwordValidate",
                    password: currentPassword
                },

                success: function (response, opts) {
                    var dataRespuesta = Ext.util.JSON.decode(response.responseText);

                    if (dataRespuesta.result == "OK") {
                        Ext.Ajax.request({
                            url:    "usersAjax",
                            method: "POST",
                            params: {
                                action: "getUserLogedRole"
                            },
                            success: function (response, opts) {
                                var dataRetval = Ext.util.JSON.decode(response.responseText);
                                if (typeof(userRoleLoad) != 'undefined') {
                                    if (Ext.getCmp('USR_ROLE').getValue() != userRoleLoad ) {
                                        if (dataRetval.USR_ROLE != PROCESSMAKER_ADMIN && Ext.getCmp('USR_ROLE').getValue() == PROCESSMAKER_ADMIN) {
                                            Ext.Msg.alert( _('ID_ERROR'), dataRetval.USR_USERNAME + ' ' + _('ID_USER_ROLE_CANT_CHANGED_TO_ADMINISTRATOR'));
                                            return false;
                                        } else {
                                            userFrmEditSubmit();
                                        }
                                    } else {
                                        // Another field changed
                                        userFrmEditSubmit();
                                    }
                                }
                            },
                            failure: function (response, opts) {
                            }
                        });

                    } else {
                        Ext.MessageBox.alert(_("ID_ERROR"), _("ID_PASSWORD_CURRENT_INCORRECT"));
                    }
                },
                failure: function (response, opts){
                  //
                }
           });
        } else {
            Ext.MessageBox.alert(_("ID_ERROR"), _("ID_PASSWORD_CURRENT_ENTER"));
        }
    } else {
        Ext.Ajax.request({
            url:    "usersAjax",
            method: "POST",
            params: {
                action: "getUserLogedRole"
            },
            success: function (response, opts) {
                var dataRetval = Ext.util.JSON.decode(response.responseText);
                if (typeof(userRoleLoad) != 'undefined') {
                    if (Ext.getCmp('USR_ROLE').getValue() != userRoleLoad ) {
                        if (dataRetval.USR_ROLE != PROCESSMAKER_ADMIN && Ext.getCmp('USR_ROLE').getValue() == PROCESSMAKER_ADMIN) {
                            Ext.Msg.alert( _('ID_ERROR'), dataRetval.USR_USERNAME + ' ' + _('ID_USER_ROLE_CANT_CHANGED_TO_ADMINISTRATOR'));
                            return false;
                        } else {
                            userFrmEditSubmit();
                        }
                    } else {
                        // Another field changed
                        userFrmEditSubmit();
                    }
                }
            },
            failure: function (response, opts) {
            }
        });
    }

  } else {
    Ext.Msg.alert(_('ID_ERROR'), _('ID_PASSWORDS_DONT_MATCH'));
  }
}

//Load data
function loadData()
{
    comboCountry.store.load();

    comboCalendar.store.on("load", function (store) {
        comboCalendar.setValue(store.getAt(0).get("CALENDAR_UID"));
    });
    comboCalendar.store.load();


    comboRole.store.on("load", function (store) {
        comboRole.setValue(store.getAt(1).get("ROL_UID"));
    });
    comboRole.store.load();

    /*----------------------------------********---------------------------------*/

    setPreferencesData(false, null);
}

//Load data for Edit mode
function loadUserData() {
    Ext.Ajax.request({
        url: "usersAjax",
        method: "POST",
        params: {
            "action": "userData",
            USR_UID: USR_UID
        },
        waitMsg: _("ID_UPLOADING_PROCESS_FILE"),
        success: function (r, o) {
            var data = Ext.util.JSON.decode(r.responseText);

            usertmp = data.user;

            Ext.getCmp("frmDetails").getForm().setValues({
                USR_FIRSTNAME: data.user.USR_FIRSTNAME,
                USR_LASTNAME: data.user.USR_LASTNAME,
                USR_USERNAME: data.user.USR_USERNAME,
                USR_EMAIL: data.user.USR_EMAIL,
                USR_ADDRESS: data.user.USR_ADDRESS,
                USR_ZIP_CODE: data.user.USR_ZIP_CODE,
                USR_PHONE: data.user.USR_PHONE,
                USR_POSITION: data.user.USR_POSITION,
                USR_DUE_DATE: data.user.USR_DUE_DATE,
                USR_STATUS: data.user.USR_STATUS,
                /*----------------------------------********---------------------------------*/
                USR_LOGGED_NEXT_TIME: data.user.USR_LOGGED_NEXT_TIME
            });

            setReadOnlyItems(data.permission);

            if (infoMode) {
                Ext.getCmp("USR_FIRSTNAME2").setText(data.user.USR_FIRSTNAME);
                Ext.getCmp("USR_LASTNAME2").setText(data.user.USR_LASTNAME);
                Ext.getCmp("USR_USERNAME2").setText(data.user.USR_USERNAME);
                Ext.getCmp("USR_EMAIL2").setText(data.user.USR_EMAIL);
                Ext.getCmp("USR_ADDRESS2").setText(data.user.USR_ADDRESS);
                Ext.getCmp("USR_ZIP_CODE2").setText(data.user.USR_ZIP_CODE);

                Ext.getCmp("USR_COUNTRY2").setText(data.user.USR_COUNTRY_NAME);
                Ext.getCmp("USR_CITY2").setText(data.user.USR_CITY_NAME);
                Ext.getCmp("USR_LOCATION2").setText(data.user.USR_LOCATION_NAME);

                Ext.getCmp("USR_PHONE2").setText(data.user.USR_PHONE);
                Ext.getCmp("USR_POSITION2").setText(data.user.USR_POSITION);
                Ext.getCmp("USR_REPLACED_BY2").setText(data.user.REPLACED_NAME);
                Ext.getCmp("USR_DUE_DATE2").setText(data.user.USR_DUE_DATE);
                Ext.getCmp("USR_STATUS2").setText(_('ID_' + data.user.USR_STATUS));
                Ext.getCmp("USR_ROLE2").setText(data.user.USR_ROLE_NAME);
                Ext.getCmp("USR_TIME_ZONE2").setText((data.user.USR_TIME_ZONE != "") ? data.user.USR_TIME_ZONE : SYSTEM_TIME_ZONE);
                /*----------------------------------********---------------------------------*/
                Ext.getCmp("USR_CALENDAR2").setText(data.user.CALENDAR_NAME);
                Ext.getCmp("PREF_DEFAULT_MAIN_MENU_OPTION2").setText(data.user.MENUSELECTED_NAME);
                Ext.getCmp("PREF_DEFAULT_CASES_MENUSELECTED2").setText(data.user.CASES_MENUSELECTED_NAME);
            } else {
                //
            }

            userRoleLoad = data.user.USR_ROLE;

            comboCountry.store.on("load", function (store) {
                comboCountry.setValue(data.user.USR_COUNTRY);
            });

            global.IC_UID = data.user.USR_COUNTRY;

            comboRegion.store.on("load", function (store) {
                comboRegion.setValue(data.user.USR_CITY);
            });

            global.IS_UID = data.user.USR_CITY;

            comboLocation.store.on("load", function (store) {
                comboLocation.setValue(data.user.USR_LOCATION);
            });

            if (data.user.USR_REPLACED_BY != "") {
                comboReplacedBy.setValue(data.user.USR_REPLACED_BY);
                comboReplacedBy.setRawValue(data.user.REPLACED_NAME);
            }

            comboCalendar.store.on("load", function (store) {
                comboCalendar.setValue(data.user.USR_CALENDAR);
            });

            comboRole.store.on("load", function (store) {
                comboRole.setValue(data.user.USR_ROLE);
            });

            cboTimeZone.setValue((data.user.USR_TIME_ZONE != "") ? data.user.USR_TIME_ZONE : SYSTEM_TIME_ZONE);

            /*----------------------------------********---------------------------------*/
            setPreferencesData(true, data);
            previousUsername = Ext.getCmp("USR_USERNAME").getValue();

            storeCountry.load();

            storeRegion.load({
                params: {
                    IC_UID: data.user.USR_COUNTRY
                }
            });

            storeLocation.load({
                params: {
                    IC_UID: data.user.USR_COUNTRY,
                    IS_UID: data.user.USR_CITY
                }
            });

            storeCalendar.load();

            storeRole.load();

            /*----------------------------------********---------------------------------*/
        },
        failure: function (r, o) {
            //viewport.getEl().unmask();
        }
    });
}

function setPreferencesData(editOrCreate, data) {
    if (USR_UID != '' && editOrCreate) {
        comboDefaultMainMenuOption.store.on("load", function (store) {
            comboDefaultMainMenuOption.setValue(data.user.PREF_DEFAULT_MENUSELECTED);
        });
        comboDefaultCasesMenuOption.store.on("load", function (store) {
            comboDefaultCasesMenuOption.setValue(data.user.PREF_DEFAULT_CASES_MENUSELECTED);
            comboDefaultCasesMenuOption.enable();
            if (comboDefaultMainMenuOption.getValue() != 'PM_CASES') {
                disableAndReadOnly('PREF_DEFAULT_CASES_MENUSELECTED');
            }
        });
    } else {
        comboDefaultMainMenuOption.store.on("load", function (store) {
            comboDefaultMainMenuOption.setValue(store.getAt(0).get("id"));
        });
        comboDefaultCasesMenuOption.store.on("load", function (store) {
            comboDefaultCasesMenuOption.setValue(store.getAt(0).get("id"));
            comboDefaultCasesMenuOption.enable();
            if (comboDefaultMainMenuOption.getValue() != 'PM_CASES') {
                disableAndReadOnly('PREF_DEFAULT_CASES_MENUSELECTED');
            }
        });
    }
    storeDefaultMainMenuOption.load();
    storeDefaultCasesMenuOption.load();
}

function userExecuteEvent(element, event)
{
    if (document.createEventObject) {
        //IE
        var evt = document.createEventObject();

        return element.fireEvent("on" + event, evt)
    } else {
        //Firefox + Others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true); //event type,bubbling,cancelable

        return !element.dispatchEvent(evt);
    }
}

function setReadOnlyItems(permissions) {
    for (var key in permissions) {
        disableAndReadOnly(key)
    }
}
function disableAndReadOnly(idElement) {
    if(idElement == 'USR_TIME_ZONE'){
        idElement = 'cboTimeZone';
    }
    if(idElement == 'USR_CUR_PASS'){
        idElement = 'currentPassword';
    }
    var myBoxCmp = Ext.getCmp(idElement);
    if (myBoxCmp) {
        Ext.getCmp(idElement).setReadOnly(true);
        Ext.getCmp(idElement).disable();
    }
}
