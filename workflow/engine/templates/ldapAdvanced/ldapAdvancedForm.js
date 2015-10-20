var ldapFormAnonymousOnChange = function (combo, arrayObject)
{
    var flagAnonymous = (combo.getValue() == "1")? true : false;

    arrayObject["ldapFormSearchUser"].allowBlank = flagAnonymous;
    arrayObject["ldapFormPassword"].allowBlank = flagAnonymous;

    arrayObject["ldapFormSearchUser"].setVisible(!flagAnonymous);
    arrayObject["ldapFormPassword"].setVisible(!flagAnonymous);
};

var ldapFormId = new Ext.form.Hidden({
    name: 'AUTH_SOURCE_UID',
    id: 'AUTH_SOURCE_UID'
});

var ldapFormName = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _('ID_NAME'),
    name: 'AUTH_SOURCE_NAME',
    id: 'AUTH_SOURCE_NAME',
    autoCreate: {tag: 'input', type: 'text', maxlength: '50'},
    allowBlank: false,
    width: 210
});

var ldapFormProvider = new Ext.form.Hidden({
    name: 'AUTH_SOURCE_PROVIDER',
    id: 'AUTH_SOURCE_PROVIDER'
});

var ldapFormType = new Ext.form.ComboBox({
    valueField: 'ID',
    displayField: 'VALUE',
    value: 'ldap',

    fieldLabel: '<span style="color: red">*</span>' + _('ID_TYPE'),
    typeAhead: true,
    forceSelection: true,
    triggerAction: 'all',
    editable: true,
    name: 'LDAP_TYPE',
    id: 'LDAP_TYPE',
    width: 130,
    allowBlank: false,
    store: [["ldap", "OpenLDAP"], ["ad", "Active Directory"], ["ds", "389 DS"]],
    listeners:{
        select: function(combo, record) {
            ldapFormIdentifier.setValue((combo.getValue() == "ad")? "samaccountname" : "uid");
        }
    }
});

var ldapFormAutoRegister = new Ext.form.ComboBox({
    valueField: 'ID',
    displayField: 'VALUE',
    value: '0',

    fieldLabel: '<span style="color: red">*</span>' + _("ID_ENABLE_AUTOMATIC_REGISTER"),
    typeAhead: true,
    forceSelection: true,
    triggerAction: 'all',
    editable: true,
    name: 'AUTH_SOURCE_AUTO_REGISTER',
    id: 'AUTH_SOURCE_AUTO_REGISTER',
    width: 130,
    allowBlank: false,
    store: [['0',_('ID_NO')],['1',_('ID_YES')]]
});

var ldapFormServerName = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _("ID_SERVER_ADDRESS"),
    name: 'AUTH_SOURCE_SERVER_NAME',
    id: 'AUTH_SOURCE_SERVER_NAME',
    autoCreate: {tag: 'input', type: 'text', maxlength: '50'},
    allowBlank: false,
    width: 210
});

var ldapFormPort = new Ext.form.NumberField({
    fieldLabel: '<span style="color: red">*</span>' + _('ID_PORT'),
    name: 'AUTH_SOURCE_PORT',
    id: 'AUTH_SOURCE_PORT',
    allowBlank: true,
    width: 130,
    value: '389',
    autoCreate: {tag: 'input', type: 'text', maxlength: '5'}
});

var ldapFormTls = new Ext.form.ComboBox({
    valueField: 'ID',
    displayField: 'VALUE',

    fieldLabel: '<span style="color: red">*</span>' + _('ID_ENABLED_TLS'),
    typeAhead: true,
    forceSelection: true,
    triggerAction: 'all',
    editable: true,
    name: 'AUTH_SOURCE_ENABLED_TLS',
    id: 'AUTH_SOURCE_ENABLED_TLS',
    width: 130,
    allowBlank: false,
    value: '0',
    store: [['0',_('ID_NO')],['1',_('ID_YES')]]
});

var ldapFormBaseDN = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _('ID_BASE_DN'),
    name: 'AUTH_SOURCE_BASE_DN',
    id: 'AUTH_SOURCE_BASE_DN',
    autoCreate: {tag: 'input', type: 'text', maxlength: '128'},
    allowBlank: false,
    width: 210
});

var ldapFormAnonymous = new Ext.form.ComboBox({
    valueField: 'ID',
    displayField: 'VALUE',

    fieldLabel: '<span style="color: red">*</span>' + _('ID_ANONYMOUS'),
    typeAhead: true,
    forceSelection: true,
    triggerAction: 'all',
    editable: true,
    name: 'AUTH_ANONYMOUS',
    id: 'AUTH_ANONYMOUS',
    width: 130,
    allowBlank: false,
    value: '0',
    store: [['0',_('ID_NO')],['1',_('ID_YES')]],
    listeners:{
        select: function(combo, record) {
            var arrayObject = [];
            arrayObject["ldapFormSearchUser"] = ldapFormSearchUser;
            arrayObject["ldapFormPassword"] = ldapFormPassword;

            ldapFormAnonymousOnChange(combo, arrayObject);
        }
    }
});

var ldapFormSearchUser = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _('ID_USERNAME'),
    name: 'AUTH_SOURCE_SEARCH_USER',
    id: 'AUTH_SOURCE_SEARCH_USER',
    autoCreate: {tag: 'input', type: 'text', maxlength: '128'},
    allowBlank: false,
    width: 210
});

var ldapFormPassword = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _('ID_PASSWORD'),
    inputType: 'password',
    name: 'AUTH_SOURCE_PASSWORD',
    id: 'AUTH_SOURCE_PASSWORD',
    autoCreate: {tag: 'input', type: 'text', maxlength: '32'},
    allowBlank: false,
    width: 210
});

var ldapFormIdentifier = new Ext.form.TextField({
    fieldLabel: '<span style="color: red">*</span>' + _("ID_USER_IDENTIFIER"),
    name: 'AUTH_SOURCE_IDENTIFIER_FOR_USER',
    id: 'AUTH_SOURCE_IDENTIFIER_FOR_USER',
    autoCreate: {tag: 'input', type: 'text', maxlength: '20'},
    allowBlank: false,
    width: 210,
    value: 'uid'
});

var ldapFormUsersFilter = new Ext.form.TextField({
    fieldLabel: _("ID_FILTER_TO_SEARCH_USERS"),
    name: 'AUTH_SOURCE_USERS_FILTER',
    id: 'AUTH_SOURCE_USERS_FILTER',
    autoCreate: {tag: 'input', type: 'text', maxlength: '200'},
    allowBlank: true,
    width: 210
});

var ldapFormRetiredEmployees = new Ext.form.TextField({
    fieldLabel: _("ID_OU_FOR_RETIRED_EMPLOYEES_OU"),
    name: 'AUTH_SOURCE_RETIRED_OU',
    id: 'AUTH_SOURCE_RETIRED_OU',
    autoCreate: {tag: 'input', type: 'text', maxlength: '128'},
    allowBlank: true,
    width: 210
});

var ldapFormAttrinuteIds = new Ext.form.Hidden({
    name: 'AUTH_SOURCE_ATTRIBUTE_IDS',
    id: 'AUTH_SOURCE_ATTRIBUTE_IDS'
});

var ldapFormShowGrid = new Ext.form.Hidden({
    name: 'AUTH_SOURCE_SHOWGRID',
    id: 'AUTH_SOURCE_SHOWGRID'
});

var ldapFormGridText = new Ext.form.Hidden({
    name: 'AUTH_SOURCE_GRID_TEXT',
    id: 'AUTH_SOURCE_GRID_TEXT'
});


///////////////////////////////////////////////////////////////////////////////////////

var ldapFormData = new Ext.form.FieldSet({
    style: {
        border: "0px"
    },

    labelWidth : 170,
    items :[
        ldapFormId, ldapFormName, ldapFormProvider, ldapFormType, ldapFormAutoRegister, ldapFormServerName,
        ldapFormPort, ldapFormTls, ldapFormBaseDN, ldapFormAnonymous, ldapFormSearchUser, ldapFormPassword,
        ldapFormIdentifier, ldapFormUsersFilter, ldapFormRetiredEmployees,
        {
            xtype: 'label',
            fieldLabel: ' ',
            id:'passwordReview',
            width: 300,
            style: 'font: 9px tahoma,arial,helvetica,sans-serif;',
            text: _("ID_DEFAULT_SET_TO"),
            labelSeparator: ''
        }, ldapFormAttrinuteIds, ldapFormShowGrid, ldapFormGridText
    ]
});

var pnlData = new Ext.Panel({
    height: 425,

    bodyStyle: "border-top: 0px; padding-top: 10px;",

    title: "<div style=\"height: 20px;\">" + _("ID_INFORMATION") + "</div>",

    items: [ldapFormData]
});

