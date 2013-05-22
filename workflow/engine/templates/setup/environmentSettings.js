var formSettings;
var fsSamples;
var fsNames;
var fsDates;
var fsCases;
var _firstName, _lastName, _userName, _dateSample;

var txtCasesRefreshTime;

Ext.onReady(function() {
  Ext.QuickTips.init();

  _firstName = 'John';
  _lastName = 'Deere';
  _userName = 'johndeere';
  _dateSample = '2011-02-17 19:15:38';

  fsSample = new Ext.form.FieldSet({
    title: _('ID_SAMPLES'),
    labelWidth: 250,
    autoHeight: true,
    frame: true,
    items: [
      {xtype: 'label', fieldLabel: _('IS_USER_NAME_DISPLAY_FORMAT'), id: 'lblFullName', width: 400},
      {xtype: 'label', fieldLabel: _('ID_GLOBAL_DATE_FORMAT'), id: 'lblDateFormat', width: 400},
      {xtype: 'label', fieldLabel: _('ID_CASE_LIST') +': '+_('ID_CASES_DATE_MASK'), id: 'lblCasesDateFormat', width: 400}//,
      //{xtype: "label", fieldLabel: _("ID_CASE_LIST") + ": " +_("ID_CASES_ROW_NUMBER"), id: "lblCasesRowsList", width: 400},
      //{xtype: "label", fieldLabel: _("ID_CASE_LIST") + ": " + _("ID_REFRESH_TIME_SECONDS"), id: "lblCasesRefreshTime", width: 400}
    ]
  });

  storeUsernameFormat = new Ext.data.GroupingStore({
    proxy : new Ext.data.HttpProxy({
      url: 'environmentSettingsAjax?request=getUserMaskList'
    }),
    reader : new Ext.data.JsonReader({
      root: 'rows',
      fields : [{name : 'id'}, {name : 'name'}]
    }),
    listeners:{
      load: function(){
        default_format = FORMATS.format;
        i = cmbUsernameFormat.store.findExact('id', default_format, 0);
        cmbUsernameFormat.setValue(cmbUsernameFormat.store.getAt(i).data.id);
        cmbUsernameFormat.setRawValue(cmbUsernameFormat.store.getAt(i).data.name);
      }
    }
  });

  cmbUsernameFormat = new Ext.form.ComboBox({
    fieldLabel : _('IS_USER_NAME_DISPLAY_FORMAT'),
    hiddenName : 'userFormat',
    store : storeUsernameFormat,
    valueField : 'id',
    displayField : 'name',
    triggerAction : 'all',
    emptyText : _('ID_SELECT'),
    editable : false,
    allowBlank : false,
    width: 400,
    allowBlankText : _('ID_ENVIRONMENT_SETTINGS_MSG_1'),
    mode:'local',
    listeners:{
      afterrender:function(){
        cmbUsernameFormat.store.load();
      },
      select: function ()
      {
          changeSettings(1);
      }
    }
  });

  storeDateFormat = new Ext.data.Store( {
    proxy : new Ext.data.HttpProxy( {
          url : 'environmentSettingsAjax?request=getDateFormats',
          method : 'POST'
    }),
    reader: new Ext.data.JsonReader( {
      root: 'rows',
      fields: [
        {name : 'id'},
        {name : 'name'}
      ]
    }),
    listeners:{
      load: function(){
      default_date_format = FORMATS.dateFormat,
        i = cmbDateFormat.store.findExact('id', default_date_format, 0);
        cmbDateFormat.setValue(cmbDateFormat.store.getAt(i).data.id);
        cmbDateFormat.setRawValue(cmbDateFormat.store.getAt(i).data.name);
      }
    }
  });

  cmbDateFormat = new Ext.form.ComboBox({
    fieldLabel : _('ID_GLOBAL_DATE_FORMAT'),
    hiddenName : 'dateFormat',
    width: 330,
    store : storeDateFormat,
    mode: 'remote',
    valueField : 'id',
    displayField : 'name',
    triggerAction : 'all',
    emptyText : _('ID_SELECT'),
    editable : false,
    allowBlank : false,
    allowBlankText : _('ID_ENVIRONMENT_SETTINGS_MSG_1'),
    mode:'local',
    listeners:{
      afterrender:function(){
        cmbDateFormat.store.load();
      },
      select: function ()
      {
          changeSettings(2);
      }
    }
  });

  storeCasesDateFormat = new Ext.data.Store({
    proxy : new Ext.data.HttpProxy({
      url : 'environmentSettingsAjax?request=getCasesListDateFormat',
      method : 'POST'
    }),
    reader: new Ext.data.JsonReader({
      root: 'rows',
      fields: [
        {name: 'id'},
        {name : 'name'}
      ]
    }),
    listeners:{
      load: function(){
        default_caseslist_date_format = FORMATS.casesListDateFormat;
        i = cmbCasesDateFormat.store.findExact('id', default_caseslist_date_format, 0);
        cmbCasesDateFormat.setValue(cmbCasesDateFormat.store.getAt(i).data.id);
        cmbCasesDateFormat.setRawValue(cmbCasesDateFormat.store.getAt(i).data.name);
      }
    }
  });

  cmbCasesDateFormat = new Ext.form.ComboBox({
    fieldLabel : _('ID_CASES_DATE_MASK'),
    hiddenName : 'casesListDateFormat',
    width: 330,
    store : storeCasesDateFormat,
    valueField : 'id',
    displayField : 'name',
    triggerAction : 'all',
    emptyText : _('ID_SELECT'),
    editable : false,
    allowBlank : false,
    allowBlankText : _('ID_ENVIRONMENT_SETTINGS_MSG_1'),
    mode:'local',
    listeners:{
      afterrender:function(){
        cmbCasesDateFormat.store.load();
      },
      select: function ()
      {
          changeSettings(4);
      }
    }
  });

  storeCasesRowNumber = new Ext.data.Store({
  proxy : new Ext.data.HttpProxy( {
    url : 'environmentSettingsAjax?request=getCasesListRowNumber',
    method : 'POST'
  }),
  reader: new Ext.data.JsonReader( {
    root: 'rows',
    fields :[
      {name : 'id'},
      {name : 'name'}
    ]
  }),
  listeners:{
      load: function ()
      {
          cmbCasesRowNumber.setValue(FORMATS.casesListRowNumber + "");
      }
  }
  });

  cmbCasesRowNumber = new Ext.form.ComboBox({
    fieldLabel : _('ID_CASES_ROW_NUMBER'),
    hiddenName : 'casesListRowNumber',
    store : storeCasesRowNumber,
    valueField : 'id',
    displayField : 'name',
    triggerAction : 'all',
    emptyText : _('ID_SELECT'),
    editable : false,
    allowBlank : false,
    allowBlankText : _('ID_ENVIRONMENT_SETTINGS_MSG_1'),
    mode:'local',
    listeners:{
      afterrender:function(){
        cmbCasesRowNumber.store.load();
      },
      select: function ()
      {
          changeSettings(5);
      }
    }
  });

  txtCasesRefreshTime = new Ext.form.NumberField({
      id: "txtCasesRefreshTime",
      name: "txtCasesRefreshTime",

      value: FORMATS.casesListRefreshTime,
      fieldLabel: _("ID_REFRESH_TIME_SECONDS"),
      maskRe: /^\d*$/,
      enableKeyEvents: true,
      
      minValue: 90,
      maxValue: 14400,

      listeners: {
          keyup: function (txt, e)
          {
              changeSettings(6);
          }
      }
  });

  fsNames = new Ext.form.FieldSet({
    title: _('ID_PM_ENV_SETTINGS_USERFIELDSET_TITLE'),
    labelAlign: 'right',
    items: [cmbUsernameFormat]
  });

  fsDates = new Ext.form.FieldSet({
    title: _('ID_PM_ENV_SETTINGS_REGIONFIELDSET_TITLE'),
    labelAlign: 'right',
    items: [cmbDateFormat]
  });

  fsCases = new Ext.form.FieldSet({
    title: _('ID_HOME_SETTINGS'),//_('ID_PM_ENV_SETTINGS_CASESLIST_TITLE'),
    labelAlign: 'right',
    items: [
      new Ext.form.FieldSet({
        title: _('ID_NEW_CASE_PANEL'),
        labelAlign: 'right',
        items: [
          {
            xtype: 'checkbox',
            checked: FORMATS.startCaseHideProcessInf,
            name: 'hideProcessInf',
            fieldLabel: _('ID_HIDE_PROCESS_INF'),
            listeners:{
              check:function(){
                saveButton.enable();
              }
            }
          }
        ]
      }),
      new Ext.form.FieldSet({
          title: _("ID_CASES_LIST_SETUP"),
          labelAlign: "right",
          items: [cmbCasesDateFormat, cmbCasesRowNumber, txtCasesRefreshTime]
      })
    ]
  });

  saveButton = new Ext.Action({
    text : _('ID_SAVE_SETTINGS'),
    disabled : true,
    handler : function() {
      formSettings.getForm().submit({
        url : 'environmentSettingsAjax?request=save&r=' + Math.random(),
        waitMsg : _('ID_SAVING_ENVIRONMENT_SETTINGS')+'...',
        timeout : 36000,
        success : function(res, req) {
        PMExt.notify(_('ID_PM_ENV_SETTINGS_TITLE'), req.result.msg);
        saveButton.disable();
        }
      });
    }
  });

  formSettings = new Ext.FormPanel( {
  region: 'center',
    labelWidth : 170, // label settings here cascade unless overridden
    //labelAlign: 'right',
    frame : true,
    title : _('ID_PM_ENV_SETTINGS_TITLE'),
    //bodyStyle : 'padding:5px 5px 0',
    width : 800,
    autoScroll: true,

    items : [fsSample,fsNames,fsDates, fsCases],
    buttons : [saveButton]
  });

  loadSamples();

  /*viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: false,
    items: [
       formSettings
    ]
  });*/

  formSettings.render(document.body);
});

//Load Samples Label
loadSamples = function ()
{
    Ext.getCmp("lblFullName").setText(_FNF(_userName, _firstName, _lastName, FORMATS.format));
    Ext.getCmp("lblDateFormat").setText(_DF(_dateSample, FORMATS.dateFormat));
    Ext.getCmp("lblCasesDateFormat").setText(_DF(_dateSample, FORMATS.casesListDateFormat, FORMATS.casesListDateFormat));
    //Ext.getCmp("lblCasesRowsList").setText(FORMATS.casesListRowNumber);
    //Ext.getCmp("lblCasesRefreshTime").setText(FORMATS.casesListRefreshTime);
};

//Change Some Setting
changeSettings = function (iType)
{
    saveButton.enable();

    switch (iType) {
        case 1:
            var f = FORMATS.format;

            FORMATS.format = cmbUsernameFormat.getValue();
            Ext.getCmp("lblFullName").setText(_FNF(_userName, _firstName, _lastName, cmbUsernameFormat.getValue()));
            FORMATS.format = f;
            break;
        case 2:
            Ext.getCmp("lblDateFormat").setText(_DF(_dateSample, cmbDateFormat.getValue()));
            break;
        case 3:
            break;
        case 4:
            Ext.getCmp("lblCasesDateFormat").setText(_DF(_dateSample, cmbCasesDateFormat.getValue()));
            break;
        case 5:
            //Ext.getCmp("lblCasesRowsList").setText(cmbCasesRowNumber.getValue());
            break;
        case 6:
            //Ext.getCmp("lblCasesRefreshTime").setText(txtCasesRefreshTime.getValue());
            break;
    }
};
