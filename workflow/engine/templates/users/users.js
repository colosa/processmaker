var storelocation;
var comboLocation;
var storeCountry;
var frmDetails;
var allowBlackStatus;
var displayPreferences;
var passwordFields;
var box;
var infoMode;
var global = {};
var readMode;
var canEdit = true;
var flagPoliciesPassword = false;
//var rendeToPage='document.body';
global.IC_UID        = '';
global.IS_UID        = '';
global.USR_FIRSTNAME = '';
global.aux           = '';
Ext.onReady(function() {
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

  //EDIT MODE
  if (USR_UID != '') {
    allowBlackStatus = true;

    box.setVisible(true);
    box.enable();

    // INFO MODE
    if (infoMode) {
      displayPreferences = 'display:block;';
      loadUserView();
      readMode = true;
      box.setVisible(false);
      box.disable();
        
    }
    else
    {
      displayPreferences = 'display:none;';
      loadUserData();
      readMode = false;
      canEdit  = false;      
    }			
      
  }
  else {
      allowBlackStatus=false;
      box.setVisible(false);
      box.disable();
      displayPreferences = 'display:none;';
      readMode           = false;
      canEdit            = false;
  }


  profileFields = new Ext.form.FieldSet({
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
/*
      ,{
        xtype: 'fileuploadfield',
        id: 'USR_RESUME',
        emptyText: _('ID_PLEASE_SELECT_FILE'),
        fieldLabel: _('ID_RESUME'),
        name: 'USR_RESUME',
        buttonText: '',
        width: 260,
        buttonCfg:{
          iconCls: 'upload-icon'
        }
      }
*/

    ]    
  });
  storeCountry = new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy( {
          url    : 'usersAjax',
          method : 'POST'
        }),
        reader : new Ext.data.JsonReader( {
          fields : [ {
            name : 'IC_UID'
          }, {
            name : 'IC_NAME'
          } ]
        })
  });
  comboCountry = new Ext.form.ComboBox({
      fieldLabel    : _('ID_COUNTRY'),
      hiddenName    : 'USR_COUNTRY',
      id            : 'USR_COUNTRY',
      store         : storeCountry,
      valueField    : 'IC_UID',
      displayField  : 'IC_NAME',
      triggerAction : 'all',
      emptyText     : _('ID_SELECT'),
      selectOnFocus : true,
      width         : 180,
      autocomplete  : true,
      typeAhead     : true,
      mode          : 'local',
      listeners : {
        select : function(combo,record,index){
          global.IC_UID = this.getValue(); 
          comboRegion.store.removeAll();
          comboLocation.store.removeAll();
          comboRegion.clearValue();     
          storeRegion.load({          
              params : {
                  action : 'stateList',
                  IC_UID : global.IC_UID
              }
          });
          comboLocation.setValue('');
          comboRegion.store.on('load',function(store) {
            comboRegion.setValue('');
          });
        }
      }
    });
  storeCountry.load({
    params : {"action" : "countryList"}
  });
  
  storeRegion  = new Ext.data.Store( {
    proxy : new Ext.data.HttpProxy( {
      url    : 'usersAjax',
      method : 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      fields : [ {
        name : 'IS_UID'
      }, {
        name : 'IS_NAME' 
      } ]
    })
  });
  comboRegion  = new Ext.form.ComboBox({
    fieldLabel    : _('ID_STATE_REGION'),
    hiddenName    : 'USR_REGION',
    id            : 'USR_REGION',
    store         : storeRegion,
    valueField    : 'IS_UID',
    displayField  : 'IS_NAME',
    triggerAction : 'all',
    emptyText     : _('ID_SELECT'),
    selectOnFocus : true,
    width         : 180,
    autocomplete  : true,
    typeAhead     : true,
    mode          : 'local',
    listeners : {
      select : function(combo, record, index) {
        global.IS_UID = this.getValue();
        comboLocation.enable();         
        comboLocation.clearValue();     
        storelocation.load({          
          params : {
            action : 'locationList',
            IC_UID : global.IC_UID,
            IS_UID : global.IS_UID 
          }  
        });
        comboLocation.store.on('load', function(store) {
          comboLocation.setValue('');
        });      
      }
    }
  });

  storelocation = new Ext.data.Store( {
    proxy : new Ext.data.HttpProxy( {
      url : 'usersAjax',
      method : 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      fields : [ {
        name : 'IL_UID'
      }, {
        name : 'IL_NAME'
      } ]
    })
  });
  comboLocation = new Ext.form.ComboBox({
    fieldLabel    : _('ID_LOCATION'),
    hiddenName    : 'USR_LOCATION',
    id            : 'USR_LOCATION',
    store         : storelocation,
    valueField    : 'IL_UID',
    displayField  : 'IL_NAME',
    triggerAction : 'all',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 180,
    autocomplete  : true,
    typeAhead     : true,
    mode          : 'local'
    
  });
  
  comboReplacedBy = new Ext.form.ComboBox({
    fieldLabel    : _('ID_REPLACED_BY'),
    hiddenName    : 'USR_REPLACED_BY',
    id            : 'USR_REPLACED_BY',   
    store         : new Ext.data.Store( {
      proxy : new Ext.data.HttpProxy( {
        url : 'usersAjax',
        method : 'POST'
      }),
      baseParams : {action : 'usersList'},
      reader     : new Ext.data.JsonReader( {
        fields : [ {
          name : 'USR_UID'
        }, {
          name : 'USER_FULLNAME'
        } ]
      }),
      autoLoad:true
    }),    
    valueField    : 'USR_UID',
    displayField  : 'USER_FULLNAME',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 180, 
    selectOnFocus : true,
    editable      : false,
    triggerAction: 'all',
    mode: 'local'

  });


  dateField = new Ext.form.DateField({
    id         : 'USR_DUE_DATE',
    fieldLabel : _('ID_EXPIRATION_DATE'),
    format     : 'Y-m-d',
    editable   : false,
    readOnly   : readMode,
    width      : 120,
    value      : (new Date().add(Date.YEAR, 1)).format('Y-m-d')
  });


  comboCalendar = new Ext.form.ComboBox({
    fieldLabel : _('ID_CALENDAR'),
    hiddenName : 'USR_CALENDAR',
    id         : 'USR_CALENDAR',
    readOnly   : readMode,
    store      : new Ext.data.Store( {
      proxy : new Ext.data.HttpProxy( {
        url    : 'usersAjax',
        method : 'POST'
      }),
      baseParams : {action : 'availableCalendars'},
      reader     : new Ext.data.JsonReader( {
        fields : [ {
          name : 'CALENDAR_UID'
        }, {
          name : 'CALENDAR_NAME'
        } ]
      }),
      autoLoad : true
    }),
    
    valueField    : 'CALENDAR_UID',
    displayField  : 'CALENDAR_NAME',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 180,
    selectOnFocus : true,
    editable      : false,
    allowBlank    : false,
    triggerAction : 'all',
    mode          : 'local'
    
  });
  comboCalendar.store.on('load', function(store) {
    comboCalendar.setValue(store.getAt(0).get('CALENDAR_UID'));
  });

  var status = new Ext.data.SimpleStore({
    fields : ['USR_STATUS', 'status'],
    data   : [['ACTIVE', 'ACTIVE'], ['INACTIVE', 'INACTIVE'], ['VACATION', 'ON VACATION']]
  });  
  comboStatus = new Ext.form.ComboBox({
    xtype         : 'combo',
    name          : 'status',
    fieldLabel    : _('ID_STATUS'),
    hiddenName    : 'USR_STATUS',
    id            : 'USR_STATUS',
    mode          : 'local',
    store         : status,
    displayField  : 'status',
    valueField    : 'USR_STATUS',
    width         : 120,
    typeAhead     : true,
    triggerAction : 'all',
    editable      : false,
    value         : 'ACTIVE',
    readOnly      : readMode
  });

  
  comboRole = new Ext.form.ComboBox({
    fieldLabel    : _('ID_ROLE'),
    hiddenName    : 'USR_ROLE',
    id            : 'USR_ROLE',
    readOnly      : readMode,
    store         : new Ext.data.Store( {
      proxy : new Ext.data.HttpProxy( {
        url    : 'usersAjax',
        method : 'POST'
      }),
      baseParams : {action : 'rolesList'},
      reader     : new Ext.data.JsonReader( {
        fields : [ {
          name : 'ROL_UID'
        }, {
          name : 'ROL_CODE'
        } ]
      }),
      autoLoad : true
    }),
    
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
  comboRole.store.on('load',function(store) {
      comboRole.setValue(store.getAt(0).get('ROL_UID'));
  })

  informationFields = new Ext.form.FieldSet({
    title : _('ID_PERSONAL_INFORMATION'),  
    items : [
      {
        id         : 'USR_FIRSTNAME',
        fieldLabel : _('ID_FIRSTNAME'), 
        xtype      : 'textfield',
        width      : 260,
        allowBlank : false
      },      
      {
        id         : 'USR_LASTNAME',
        fieldLabel : _('ID_LASTNAME'),
        xtype      : 'textfield',
        width      : 260,
        allowBlank : false
      },      
      {
        id         : 'USR_USERNAME',
        fieldLabel : _('ID_USER_ID'),
        xtype      : 'textfield',
        width      : 260,
        allowBlank : false
      },
      {
        id         : 'USR_EMAIL',
        fieldLabel : _('ID_EMAIL'),
        vtype      : 'email',
        xtype      : 'textfield',
        width      : 260,
        allowBlank : false
      },
      {
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
      comboRole

      ]
  });
  passwordFields = new Ext.form.FieldSet({
    title : _('ID_CHANGE_PASSWORD'),
    items : [
      {
        id         : 'USR_NEW_PASS',
        fieldLabel : _('ID_NEW_PASSWORD'),
        xtype      : 'textfield',
        inputType  : 'password',
        width      : 260,
        allowBlank : allowBlackStatus,
        listeners: {
          blur : function(ob)
          {
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
              },
              failure: function () {
                Ext.MessageBox.show({
                  title: 'Error',
                  msg: 'Failed to store data',
                  buttons: Ext.MessageBox.OK,
                  animEl: 'mb9',
                  icon: Ext.MessageBox.ERROR
                });
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
        fieldLabel : _('ID_CONFIRM_PASSWORD'),
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

  comboDefaultMainMenuOption = new Ext.form.ComboBox({
    fieldLabel : _('ID_DEFAULT_MAIN_MENU_OPTION'),
    hiddenName : 'PREF_DEFAULT_MENUSELECTED',
    id         : 'PREF_DEFAULT_MENUSELECTED',
    store      : new Ext.data.Store( {
      proxy : new Ext.data.HttpProxy( {
        url : 'usersAjax',
        method : 'POST'
      }),
      baseParams : {action : 'defaultMainMenuOptionList'},
      reader : new Ext.data.JsonReader( {
        fields : [ {
          name : 'id'
        }, {
          name : 'name'
        } ]
      }),
      autoLoad : true
    }),    
    valueField    : 'id',
    displayField  : 'name',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 260,
    selectOnFocus : true,
    editable      : false,
    triggerAction : 'all',
    mode          : 'local'
  });
  comboDefaultMainMenuOption.store.on('load',function(store) {
      comboDefaultMainMenuOption.setValue(store.getAt(0).get('id'));
  });
  comboDefaultCasesMenuOption = new Ext.form.ComboBox({
    fieldLabel : _('ID_DEFAULT_CASES_MENU_OPTION'),
    hiddenName : 'PREF_DEFAULT_CASES_MENUSELECTED',
    id         : 'PREF_DEFAULT_CASES_MENUSELECTED',
    store      : new Ext.data.Store( {
      proxy : new Ext.data.HttpProxy( {
        url : 'usersAjax',
        method : 'POST'
      }),
      baseParams : {action : 'defaultCasesMenuOptionList'},
      reader     : new Ext.data.JsonReader( {
        fields : [ {
          name : 'id'
        }, {
          name : 'name'
        } ]
      }),
      autoLoad : true
    }),    
    valueField    : 'id',
    displayField  : 'name',
    emptyText     : TRANSLATIONS.ID_SELECT,
    width         : 260,
    selectOnFocus : true,
    editable      : false,
    triggerAction : 'all',
    mode          : 'local'
  });
  comboDefaultCasesMenuOption.store.on('load',function(store) {
      comboDefaultCasesMenuOption.setValue(store.getAt(0).get('id'));
  });

  preferencesFields = new Ext.form.FieldSet({
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
      informationFields,
      passwordFields,
      profileFields,
      preferencesFields
    ],
    buttons : [
      {
        text   : _('ID_SAVE'),
        handler: saveUser

      
      },
      {     
        text    : _('ID_CANCEL'),
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
        //hidden:readMode
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
      }

    ]
  });
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

  if (infoMode) {
    document.body.appendChild(defineUserPanel());
    frmSumary.render('users-panel');
  }
  else {
    frmDetails.render(document.body);
  }

  Ext.getCmp('passwordReview').setVisible(false);
  Ext.getCmp('passwordConfirm').setVisible(false);

  var spanAjax = '<span style="font: 9px tahoma,arial,helvetica,sans-serif;">';
  var imageAjax = '<img width="13" height="13" border="0" src="/images/ajax-loader.gif">';
  var labelAjax = _('ID_PASSWORD_TESTING');
  
  Ext.getCmp('passwordReview').setText(spanAjax + imageAjax + labelAjax + '</span>', false);
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
  }
  else {
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
    frmDetails.show();
}
function saveUser()
{
  if (flagPoliciesPassword != true) {
    Ext.Msg.alert( _('ID_ERROR'), Ext.getCmp('passwordReview').html);
    return false;
  }

  var newPass  = frmDetails.getForm().findField('USR_NEW_PASS').getValue();
  var confPass = frmDetails.getForm().findField('USR_CNF_PASS').getValue();	
  if (confPass === newPass) {
    Ext.getCmp('frmDetails').getForm().submit( {

      url    : 'usersAjax',
      params : {
        action   : 'saveUser',
        USR_UID  : USR_UID,
        USR_CITY : global.IS_UID
      },
      waitMsg : _('ID_SAVING_PROCESS'),
      timeout : 36000,
      success : function(obj, resp) {
        if (!infoMode) { 
          location.href = 'users_List';
        }
        else {
         location.href = '../users/myInfo?type=reload';
        }

      },
      failure : function(obj, resp) {
        if (typeof resp.result  == "undefined")
        {
          Ext.Msg.alert( _('ID_ERROR'),_('ID_SOME_FIELDS_REQUIRED'));
        }
        else{
          if (resp.result.msg){
            var message = resp.result.msg.split(',');
            Ext.Msg.alert( _('ID_WARNING'), '<strong>'+message[0]+'<strong><br/><br/>'+message[1]+'<br/><br/>'+message[2]);
          }
          if (resp.result.fileError) {
            Ext.Msg.alert( _('ID_ERROR'),_('ID_FILE_TOO_BIG'));
          }
          if (resp.result.error) {
            Ext.Msg.alert( _('ID_ERROR'), resp.result.error);
          }
        }

      }
    });
  }
  else
    Ext.Msg.alert( _('ID_ERROR'), _('ID_PASSWORDS_DONT_MATCH'));
}
 
 
// Load data for Edit mode
function loadUserData()
{
  Ext.Ajax.request({
    url    : 'usersAjax',
    params : {
      'action' : 'userData',
      USR_UID  : USR_UID
    },
    waitMsg : _('ID_UPLOADING_PROCESS_FILE'), 
    success : function(r,o){			
      var data = Ext.util.JSON.decode(r.responseText);

      Ext.getCmp('frmDetails').getForm().setValues({
        USR_FIRSTNAME : data.user.USR_FIRSTNAME, 
        USR_LASTNAME  : data.user.USR_LASTNAME,
        USR_USERNAME  : data.user.USR_USERNAME,
        USR_EMAIL     : data.user.USR_EMAIL,
        USR_ADDRESS   : data.user.USR_ADDRESS,
        USR_ZIP_CODE  : data.user.USR_ZIP_CODE,
        USR_PHONE     : data.user.USR_PHONE,
        USR_POSITION  : data.user.USR_POSITION,
        USR_DUE_DATE  : data.user.USR_DUE_DATE,
        USR_STATUS    : data.user.USR_STATUS
      })
      
        
      storeCountry.load({
          params : {
            action : 'countryList'
        }
      });

      storeRegion.load({
        params : {
          action : 'stateList',
          IC_UID : data.user.USR_COUNTRY
        }
      });

      storelocation.load({
        params : {
          action : 'locationList',
          IC_UID : data.user.USR_COUNTRY,
          IS_UID : data.user.USR_CITY 
        }  
      });
      comboCountry.store.on('load',function(store) {
        comboCountry.setValue(data.user.USR_COUNTRY);
      });
      global.IC_UID = data.user.USR_COUNTRY;
     
      comboRegion.store.on('load',function(store) {
        comboRegion.setValue(data.user.USR_CITY);
      });
      
      global.IS_UID = data.user.USR_CITY;
      comboLocation.store.on('load',function(store) {
        comboLocation.setValue(data.user.USR_LOCATION);
      });
   
      comboReplacedBy.store.on('load',function(store) {
        comboReplacedBy.setValue(data.user.USR_REPLACED_BY);
      });
      comboRole.store.on('load',function(store) {
        comboRole.setValue(data.user.USR_ROLE);
      });
      comboCalendar.store.on('load',function(store) {
        comboCalendar.setValue(data.user.USR_CALENDAR);
      });
        
    },

    failure : function(r, o) {
      //viewport.getEl().unmask();
    }
  });
}
// Load data for Edit mode
function loadUserView()
{
  Ext.Ajax.request({
    url    : 'usersAjax',
    params : {
      'action' : 'userData',
      USR_UID  : USR_UID
    },
    waitMsg : _('ID_UPLOADING_PROCESS_FILE'), 
    success : function(r,o){			
      var data = Ext.util.JSON.decode(r.responseText);
      
      Ext.getCmp('frmDetails').getForm().setValues({
        USR_FIRSTNAME : data.user.USR_FIRSTNAME, 
        USR_LASTNAME  : data.user.USR_LASTNAME,
        USR_USERNAME  : data.user.USR_USERNAME,
        USR_EMAIL     : data.user.USR_EMAIL,
        USR_ADDRESS   : data.user.USR_ADDRESS,
        USR_ZIP_CODE  : data.user.USR_ZIP_CODE,
        USR_PHONE     : data.user.USR_PHONE,
        USR_POSITION  : data.user.USR_POSITION,
        USR_DUE_DATE  : data.user.USR_DUE_DATE,
        USR_STATUS    : data.user.USR_STATUS
      });
      Ext.getCmp('USR_FIRSTNAME2').setText(data.user.USR_FIRSTNAME);
      Ext.getCmp('USR_LASTNAME2').setText(data.user.USR_LASTNAME);
      Ext.getCmp('USR_USERNAME2').setText(data.user.USR_USERNAME);
      Ext.getCmp('USR_EMAIL2').setText(data.user.USR_EMAIL);
      Ext.getCmp('USR_ADDRESS2').setText(data.user.USR_ADDRESS);
      Ext.getCmp('USR_ZIP_CODE2').setText(data.user.USR_ZIP_CODE);
      
      Ext.getCmp('USR_COUNTRY2').setText(data.user.USR_COUNTRY_NAME);
      Ext.getCmp('USR_CITY2').setText(data.user.USR_CITY_NAME);
      Ext.getCmp('USR_LOCATION2').setText(data.user.USR_LOCATION_NAME);
      
      Ext.getCmp('USR_PHONE2').setText(data.user.USR_PHONE);
      Ext.getCmp('USR_POSITION2').setText(data.user.USR_POSITION);
      Ext.getCmp('USR_REPLACED_BY2').setText(data.user.REPLACED_NAME);
      Ext.getCmp('USR_DUE_DATE2').setText(data.user.USR_DUE_DATE);
      Ext.getCmp('USR_STATUS2').setText(data.user.USR_STATUS);
      Ext.getCmp('USR_ROLE2').setText(data.user.USR_ROLE);
      
      
      Ext.getCmp('PREF_DEFAULT_MAIN_MENU_OPTION2').setText(data.user.MENUSELECTED_NAME);
      Ext.getCmp('PREF_DEFAULT_CASES_MENUSELECTED2').setText(data.user.CASES_MENUSELECTED_NAME);

      storeCountry.load({
          params : {
            action : 'countryList'
        }
      });

      storeRegion.load({
        params : {
          action : 'stateList',
          IC_UID : data.user.USR_COUNTRY
        }
      });

      storelocation.load({
        params : {
          action : 'locationList',
          IC_UID : data.user.USR_COUNTRY,
          IS_UID : data.user.USR_CITY 
        }  
      });
      comboCountry.store.on('load',function(store) {
        comboCountry.setValue(data.user.USR_COUNTRY);
      });
      global.IC_UID = data.user.USR_COUNTRY;
     
      comboRegion.store.on('load',function(store) {
        comboRegion.setValue(data.user.USR_CITY);
      });
      
      global.IS_UID = data.user.USR_CITY;
      comboLocation.store.on('load',function(store) {
        comboLocation.setValue(data.user.USR_LOCATION);
      });
           
      comboReplacedBy.store.on('load',function(store) {
        comboReplacedBy.setValue(data.user.USR_REPLACED_BY);
      });
      comboRole.store.on('load',function(store) {
        comboRole.setValue(data.user.USR_ROLE);
      });
      comboCalendar.store.on('load',function(store) {
        comboCalendar.setValue(data.user.USR_CALENDAR);
      });
       
      //for preferences on the configurations table
      comboDefaultMainMenuOption.store.on('load',function(store) {
        comboDefaultMainMenuOption.setValue(data.user.PREF_DEFAULT_MENUSELECTED);
      });
      comboDefaultCasesMenuOption.store.on('load',function(store) {
        //comboDefaultCasesMenuOption.setValue('');
        comboDefaultCasesMenuOption.setValue(data.user.PREF_DEFAULT_CASES_MENUSELECTED);
      });
    },
    failure:function(r,o) {
      //viewport.getEl().unmask();
    }
  });

}

function userExecuteEvent (element,event) {
  if ( document.createEventObject ) {
    // IE
    var evt = document.createEventObject();
    return element.fireEvent('on'+event,evt)
  } else {
    // firefox + others
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent(event, true, true ); // event type,bubbling,cancelable
    return !element.dispatchEvent(evt);
  }
}