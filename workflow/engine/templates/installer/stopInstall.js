Ext.onReady(function() {

    var formLicenseLog = new Ext.FormPanel({
        labelWidth : 60,
        frame : true,
        autoScroll: true,
        monitorValid : true,
        renderTo : 'bodyNoInsatalled',
        title : _('ID_TITLE_NO_INSTALL'),

        items:[
            {html: messageError}
        ]
    });
});

