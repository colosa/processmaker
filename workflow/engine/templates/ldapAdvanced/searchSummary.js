var caseData = '';
var appTitle = new Ext.form.Label({
    fieldLabel: _('ID_CASE_TITLE'),
    labelStyle: 'font-weight:bold;padding-right:30px;'
});

var process = new Ext.form.Label({
    fieldLabel: _('ID_PROCESS_UID'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var processTitle = new Ext.form.Label({
    fieldLabel: _('ID_PROCESS'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var appUid = new Ext.form.Label({
    fieldLabel: _('ID_APP_UID'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var caseNumber = new Ext.form.Label({
    fieldLabel: _('ID_CASE_NUMBER'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var initUser = new Ext.form.Label({
    fieldLabel: _('ID_INIT_USER'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var finishUser = new Ext.form.Label({
    fieldLabel: _('ID_FINISH_USER'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var createDate = new Ext.form.Label({
    fieldLabel: _('ID_CREATE_DATE'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var finishDate = new Ext.form.Label({
    fieldLabel: _('ID_FINISH_DATE'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var fileName = new Ext.form.Label({
    fieldLabel: _('ID_FILE_NAME'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var statusCaseWin = new Ext.form.Label({
    fieldLabel: _('ID_CASESLIST_APP_STATUS'),
    labelStyle: 'font-weight:bold;padding-right:35px;'
});

var formCase = new Ext.FormPanel({
    labelWidth : 120,
    labelAlign : 'right',
    autoScroll: true,
    frame: true,
    bodyStyle   : 'padding-top:20px;padding-left:20px;',
    items:[
        appTitle,
        caseNumber,
        processTitle,
        initUser,
        finishUser,
        createDate,
        finishDate,
        fileName,
        statusCaseWin
    ],
    buttons:[{
        text : "Restore case",
        id: 'BUTTON_UNARCHIVE_CASE',
        iconCls: 'button_menu_ext ss_sprite ss_folder_go',
        formBind : true,
        handler : function(){
            if (caseData!='') {
                    Ext.MessageBox.confirm("Confirm", "Are you sure you want to restore the case?", function (val) {
                        if (val == 'yes') {
                            dataCase = caseData;
                            Ext.MessageBox.show({
                                msg: _('ID_RESTORING_CASE') + ' ' + dataCase.CASE_NUMBER + ' ...',
                                progressText: _('ID_SAVING'),
                                width:300,
                                wait:true,
                                waitConfig: {interval:200},
                                animEl: 'mb7'
                            });
                            Ext.Ajax.request({
                                params: {
                                    'APP_UID': dataCase.APP_UID,
                                    'FILENAME_TAR': dataCase.FILENAME_TAR,
                                    'functionExecute': 'unarchiveCase'
                                },
                                url : 'controllers/searchListProxy.php',
                                success: function (returnData) {
                                    Ext.MessageBox.hide();

                                    var resp = Ext.decode(returnData.responseText);
                                    if (resp.success) {
                                        Ext.MessageBox.show({
                                            title: _('ID_CASE_UNARHIVE'),
                                            msg: _('ID_CASE') + ' ' + dataCase.CASE_NUMBER + ' ' + _('ID_RESTORED_SUCESSFULLY'),
                                            buttons: Ext.MessageBox.OK,
                                            animEl: 'mb9',
                                            icon: Ext.MessageBox.INFO
                                        });
                                    } else {
                                        Ext.MessageBox.show({
                                            title: _('ID_ERROR'),
                                            msg: resp.message,
                                            buttons: Ext.MessageBox.OK,
                                            animEl: 'mb9',
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }

                                    storeGridSearch.load();
                                },
                                failure: function () {
                                    Ext.MessageBox.alert("Error", _('ID_ERROR_IN_SERVER'));
                                }
                            });
                        }
                    });
            }
        }
    },
    {
        text : _('ID_CLOSE'),
        iconCls: 'button_menu_ext ss_sprite ss_folder_delete',
        formBind : true,
        handler : function(){
            summaryWindow.hide();
        }
    }]
});

var summaryWindow = new Ext.Window({
    title: _('ID_DETAIL_CASE'),
    layout: 'fit',
    width: 500,
    height: 320,
    resizable: true,
    closable: true,
    closeAction : 'hide',
    modal: true,
    autoScroll:true,
    constrain: true,
    items: [formCase]
});

function showCaseSummary(dataCase) {
    if (dataCase) {
        caseData = dataCase;

        if(dataCase.STATUS == 'RESTORED'){
            Ext.getCmp('BUTTON_UNARCHIVE_CASE').disable();
        } else {
            Ext.getCmp('BUTTON_UNARCHIVE_CASE').enable();
        }

        appTitle.setText(dataCase.APP_TITLE, false);
        process.setText(dataCase.PRO_UID, false);
        processTitle.setText(dataCase.PRO_TITLE, false);
        appUid.setText(dataCase.APP_UID, false);
        caseNumber.setText(dataCase.CASE_NUMBER, false);
        initUser.setText(dataCase.INIT_USER_NAME, false);
        finishUser.setText(dataCase.FINISH_USER_NAME, false);
        createDate.setText(renderDate(dataCase.CREATE_DATE, false));
        finishDate.setText(renderDate(dataCase.FINISH_DATE, false));
        fileName.setText(dataCase.FILENAME_TAR+'.tar', false);
        statusCaseWin.setText(dataCase.STATUS, false);

        summaryWindow.show();
    }
}