Ext.namespace("systemInfo");

systemInfo.application = {
    init: function ()
    {
        var loadMaskSystemInfo = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_CHECKING")});

        systemInfoProcessAjax = function (option)
        {
            var url = "";

            loadMaskSystemInfo.show();

            switch (option) {
                case "SYS":
                    url = "../installer/getSystemInfo"
                    break;
            }

            Ext.Ajax.request({
                url: url,
                method: "POST",

                success: function (response, opts)
                {
                    var dataResponse = eval("(" + response.responseText + ")"); //json

                    switch (option) {
                        case "SYS":
                            Ext.getCmp("php").setValue(fieldFormatValue(dataResponse.php.version, dataResponse.php.result));
                            Ext.getCmp("mysql").setValue(fieldFormatValue(dataResponse.mysql.version, dataResponse.mysql.result));
                            //Ext.getCmp("mssql").setValue(fieldFormatValue(dataResponse.mssql.version, dataResponse.mssql.result));
                            Ext.getCmp("curl").setValue(fieldFormatValue(dataResponse.curl.version, dataResponse.curl.result));
                            Ext.getCmp("openssl").setValue(fieldFormatValue(dataResponse.openssl.version, dataResponse.openssl.result));
                            Ext.getCmp("dom").setValue(fieldFormatValue(dataResponse.dom.version, dataResponse.dom.result));
                            Ext.getCmp("gd").setValue(fieldFormatValue(dataResponse.gd.version, dataResponse.gd.result));
                            Ext.getCmp("multibyte").setValue(fieldFormatValue(dataResponse.multibyte.version, dataResponse.multibyte.result));
                            Ext.getCmp("soap").setValue(fieldFormatValue(dataResponse.soap.version, dataResponse.soap.result));
                            Ext.getCmp("ldap").setValue(fieldFormatValue(dataResponse.ldap.version, dataResponse.ldap.result));
                            Ext.getCmp("memory").setValue(fieldFormatValue(dataResponse.memory.version, dataResponse.memory.result));
                            break;
                    }

                    loadMaskSystemInfo.hide();
                },
                failure: function (response, opts)
                {
                    loadMaskSystemInfo.hide();
                }
            });
        };

        fieldFormatValue = function (str, sw)
        {
            var img = "delete.png";
            var size = "width=\"15\" height=\"15\"";
            var color = "red";

            if (sw == true || sw == 1) {
                img = "dialog-ok-apply.png";
                size = "width=\"12\" height=\"12\"";
                color = "green";
            }

            return "<span style=\"color: " + color + ";\">" + str + "</span> <img src=\"/images/" + img + "\" " + size + " alt=\"\" />";
        };

        //Components
        var pnlWest = new Ext.Panel({
            id: "pnlWest",

            region: "west",

            margins: {top: 10, right: 0, bottom: 10, left: 10},
            border: false,
            bodyStyle: "padding: 10px; font: 0.80em arial;",
            width: 250,

            html: _("ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION")
        });

        var frmfsCenter = new Ext.form.FieldSet({
            id: "frmfsCenter",

            region: "center",

            margins: {top: 10, right: 10, bottom: 10, left: 0},
            border: false,
            labelWidth: 200,

            items: [
                {
                    xtype: "displayfield",
                    id: "php",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_PHP"),
                    value: eval("fieldFormatValue(" + SYSINFO_PHP + ");")
                },
                {
                    xtype: "displayfield",
                    id: "mysql",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_MYSQL"),
                    value: eval("fieldFormatValue(" + SYSINFO_MYSQL + ");")
                },
                /*
                {
                    xtype: "displayfield",
                    id: "mssql",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_MSSQL"),
                    value: eval("fieldFormatValue(" + SYSINFO_MSSQL + ");")
                },
                */
                {
                    xtype: "displayfield",
                    id: "curl",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_CURL"),
                    value: eval("fieldFormatValue(" + SYSINFO_CURL + ");")
                },
                {
                    xtype: "displayfield",
                    id: "openssl",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_OPENSSL"),
                    value: eval("fieldFormatValue(" + SYSINFO_OPENSSL + ");")
                },
                {
                    xtype: "displayfield",
                    id: "dom",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_DOMXML"),
                    value: eval("fieldFormatValue(" + SYSINFO_DOMXML + ");")
                },
                {
                    xtype: "displayfield",
                    id: "gd",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_GD"),
                    value: eval("fieldFormatValue(" + SYSINFO_GD + ");")
                },
                {
                    xtype: "displayfield",
                    id: "multibyte",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_MULTIBYTESTRING"),
                    value: eval("fieldFormatValue(" + SYSINFO_MULTIBYTESTRING + ");")
                },
                {
                    xtype: "displayfield",
                    id: "soap",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_SOAP"),
                    value: eval("fieldFormatValue(" + SYSINFO_SOAP + ");")
                },
                {
                    xtype: "displayfield",
                    id: "ldap",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_LDAP"),
                    value: eval("fieldFormatValue(" + SYSINFO_LDAP + ");")
                },
                {
                    xtype: "displayfield",
                    id: "memory",
                    fieldLabel: _("ID_PROCESSMAKER_REQUIREMENTS_MEMORYLIMIT"),
                    value: eval("fieldFormatValue(" + SYSINFO_MEMORYLIMIT + ");")
                },
                {
                    xtype: "displayfield",
                    fieldLabel: "",
                },
                new Ext.Button({
                    text: _("ID_CHECK_AGAIN"),
                    handler: function () {
                        systemInfoProcessAjax("SYS");
                    }
                })
            ]
        });

        var pnlMain = new Ext.Panel({
            id: "pnlMain",

            layout: "border",

            border: false,
            title: _("ID_PROCESSMAKER_REQUIREMENTS_CHECK"),

            items: [pnlWest, frmfsCenter]
        });

        //Load all panels
        var viewport = new Ext.Viewport({
            layout: "fit",
            items: [pnlMain]
        });
    }
}

Ext.onReady(systemInfo.application.init, systemInfo.application);

