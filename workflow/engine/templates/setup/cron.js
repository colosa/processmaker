Ext.namespace("cron");

cron.application = {
    init: function ()
    {
        var loadMaskCron = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_LOADING_GRID")});

        cronProcessAjax = function (option)
        {
            var p;

            switch (option) {
                case "EMPTY":
                    p = {
                        "option": option
                    };
                    break;
            }

            Ext.Ajax.request({
                url: "cronAjax",
                method: "POST",
                params: p,

                success: function (response, opts)
                {
                    var dataResponse = eval("(" + response.responseText + ")"); //json

                    switch (option) {
                        case "EMPTY":
                            if (dataResponse.status && dataResponse.status == "OK") {
                                pagingCron.moveFirst();
                            }
                            break;
                    }
                },
                failure: function (response, opts)
                {
                    //
                }
            });
        }

        infoView = function ()
        {
            var strData = "<b>" + _("ID_CRON_INFO") + "</b><br />";
            strData = strData + "<b>" + _("ID_STATUS") + ":</b> " + CRON.status + "<br />";
            strData = strData + "<b>" + _("ID_EVENT_LAST_EXECUTION_DATE") + ":</b> " + CRON.lastExecution + "<br /><br />";
            strData = strData + "<b>" + _("ID_LOG_INFO") + " (" + CRON.fileLogName + ")</b><br />";
            strData = strData + "<b>" + _("ID_FILENAME") + ":</b> " + CRON.fileLogName + "<br />";
            strData = strData + "<b>" + _("ID_SIZE") + ":</b> " + CRON.fileLogSize + " MB<br />";
            strData = strData + "<b>" + _("ID_PATH") + ":</b> " + CRON.fileLogPath + "<br />";

            var formItems = Ext.getCmp("frmInfoView").form.items;
            formItems.items[0].setValue(strData);

            winInfo.show();
        }

        logView = function ()
        {
            var record = grdpnlMain.getSelectionModel().getSelected();

            var strData = "<b>" + _("ID_DATE_LABEL") + "</b><br />" + record.get("DATE") + "<br />";
            strData = strData + "<b>" + _("ID_WORKSPACE") + "</b><br />" + record.get("WORKSPACE") + "<br />";
            strData = strData + "<b>" + _("ID_ACTION") + "</b><br />" + record.get("ACTION") + "<br />";
            strData = strData + "<b>" + _("ID_STATUS") + "</b><br />" + record.get("STATUS") + "<br />";
            strData = strData + "<b>" + _("ID_DESCRIPTION") + "</b><br />" + record.get("DESCRIPTION") + "<br />";

            var formItems = Ext.getCmp("frmLogView").form.items;
            formItems.items[0].setValue(strData);

            winLog.setTitle("Log - " + _("ID_WORKSPACE") + "&nbsp;" + record.get("WORKSPACE"));
            winLog.show();
        }

        //Variables
        var pageSize = parseInt(CONFIG.pageSize);

        var expander = new Ext.ux.grid.RowExpander({
            tpl: new Ext.Template(
                "<b>" + _("ID_DESCRIPTION") + "</b><br />{DESCRIPTION}"
            )
        });

        //Stores
        var storeCron = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({
                url: "cronAjax",
                method: "POST"
            }),

            reader: new Ext.data.JsonReader({
                root: "resultRoot",
                totalProperty: "resultTotal",
                fields: [
                    {name: "DATE"},
                    {name: "WORKSPACE"},
                    {name: "ACTION"},
                    {name: "STATUS"},
                    {name: "DESCRIPTION"}
                ]
            }),

            //autoLoad: true, //First call

            listeners: {
                beforeload: function (store)
                {
                    loadMaskCron.show();

                    this.baseParams = {
                        "option": "LST",
                        "pageSize": pageSize,
                        "workspace": Ext.getCmp("cboWorkspace").getValue(),
                        "status": Ext.getCmp("cboStatus").getValue(),
                        "dateFrom": Ext.getCmp("dateFrom").getValue(),
                        "dateTo": Ext.getCmp("dateTo").getValue()
                    };

                    //btnView.setDisabled(true);
                },
                load: function (store, record, opt)
                {
                    loadMaskCron.hide();
                }
            }
        });

        var storeWorkspace = new Ext.data.ArrayStore({
            idIndex: 0,
            fields: ["id", "value"],
            data: WORKSPACE
        });

        var storeStatus = new Ext.data.ArrayStore({
            idIndex: 0,
            fields: ["id", "value"],
            data: STATUS
        });

        var storePageSize = new Ext.data.SimpleStore({
            fields: ["size"],
            data: [["20"], ["30"], ["40"], ["50"], ["100"]],
            autoLoad: true
        });

        //Components
        var dateFrom = new Ext.form.DateField({
            id: "dateFrom",

            format: "Y-m-d",
            editable: false,
            width: 90,
            value: ""
        });

        var dateTo = new Ext.form.DateField({
            id: "dateTo",

            format: "Y-m-d",
            editable: false,
            width: 90,
            value: ""
        });

        var cboWorkspace = new Ext.form.ComboBox({
            id: "cboWorkspace",

            valueField: "id",
            displayField: "value",
            value: "ALL",
            store: storeWorkspace,

            triggerAction: "all",
            mode: "local",
            editable: false,

            width: 150,

            listeners: {
                select: function (combo, record, index)
                {
                    pagingCron.moveFirst();
                }
            }
        });

        var cboStatus = new Ext.form.ComboBox({
            id: "cboStatus",

            valueField: "id",
            displayField: "value",
            value: "ALL",
            store: storeStatus,

            triggerAction: "all",
            mode: "local",
            editable: false,

            width: 90,

            listeners: {
                select: function (combo, record, index)
                {
                    pagingCron.moveFirst();
                }
            }
        });

        var cboPageSize = new Ext.form.ComboBox({
            id: "cboPageSize",

            mode: "local",
            triggerAction: "all",
            store: storePageSize,
            valueField: "size",
            displayField: "size",
            width: 50,
            editable: false,

            listeners: {
                select: function (combo, record, index)
                {
                    pageSize = parseInt(record.data["size"]);

                    pagingCron.pageSize = pageSize;
                    pagingCron.moveFirst();
                }
            }
        });

        var btnInfoView = new Ext.Action({
            id: "btnInfoView",

            text: _("ID_VIEW_INFO"),
            iconCls: "button_menu_ext ss_sprite ss_zoom",

            handler: function ()
            {
                infoView();
            }
        });

        var btnLogClear = new Ext.Action({
            id: "btnLogClear",

            text: _("ID_CLEAR_LOG"),
            iconCls: "button_menu_ext ss_sprite ss_bin_empty",

            handler: function ()
            {
                Ext.MessageBox.confirm(
                    _("ID_CONFIRM"),
                    _("ID_CRON_LOG_CLEAR"),
                    function (btn, text)
                    {
                        if (btn == "yes") {
                            cronProcessAjax("EMPTY");
                        }
                    }
                );
            }
        });

        /*
        var btnView = new Ext.Action({
            id: "btnView",

            text: _("ID_VIEW"),
            iconCls: "button_menu_ext ss_sprite ss_zoom",
            disabled: true,

            handler: function ()
            {
                logView();
            }
        });
        */

        var pagingCron = new Ext.PagingToolbar({
            id: "pagingCron",

            pageSize: pageSize,
            store: storeCron,
            displayInfo: true,
            displayMsg: _("ID_CRON_GRID_PAGE_DISPLAYING_MESSAGE"),
            emptyMsg: _("ID_NO_RECORDS_FOUND"),
            items: ["-", _("ID_PAGE_SIZE") + "&nbsp;", cboPageSize]
        });

        var cmodel = new Ext.grid.ColumnModel({
            defaults: {
                width: 50,
                sortable: true
            },
            columns: [
                expander,
                {id: "ID", dataIndex: "DATE", hidden: true, hideable: false},
                {header: _("ID_DATE_LABEL"), dataIndex: "DATE", width: 10, align: "center"},
                {header: _("ID_WORKSPACE"), dataIndex: "WORKSPACE", width: 10},
                {header: _("ID_ACTION"), dataIndex: "ACTION", width: 10},
                {header: _("ID_STATUS"), dataIndex: "STATUS", width: 7, align: "center"},
                {header: _("ID_DESCRIPTION"), dataIndex: "DESCRIPTION"}
            ]
        });

        var smodel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                rowselect: function (sm)
                {
                    //btnView.setDisabled(false);
                },
                rowdeselect: function (sm)
                {
                    //btnView.setDisabled(true);
                }
            }
        });

        var winInfo = new Ext.Window({
            layout: "fit",
            width: 500,
            height: 250,
            title: _("ID_CRON_INFO"),
            modal: true,
            closeAction: "hide",

            items: [
                new Ext.FormPanel({
                    id: "frmInfoView",

                    frame: true,
                    labelAlign: "right",
                    labelWidth: 1,
                    autoWidth: true,
                    //height: 395,
                    autoScroll: true,
                    items: [
                        {
                            xtype: "displayfield",
                            fieldLabel: ""
                        }
                    ]
                })
            ]
        });

        var winLog = new Ext.Window({
            layout: "fit",
            width: 500,
            height: 250,
            title: "",
            modal: true,
            closeAction: "hide",

            items: [
                new Ext.FormPanel({
                    id: "frmLogView",

                    frame: true,
                    labelAlign: "right",
                    labelWidth: 1,
                    autoWidth: true,
                    //height: 395,
                    autoScroll: true,
                    items: [
                        {
                            xtype: "displayfield",
                            fieldLabel: ""
                        }
                    ]
                })
            ]
        });

        var grdpnlMain = new Ext.grid.GridPanel({
            id: "grdpnlMain",

            store: storeCron,
            colModel: cmodel,
            selModel: smodel,

            columnLines: true,
            viewConfig: {forceFit: true},
            enableColumnResize: true,
            enableHdMenu: false,
            plugins: expander,

            tbar: [
                {xtype: "tbtext", text: _("ID_CRON_STATUS") + ": "},
                {xtype: "tbtext", html: "<b>" + CRON.status + "</b>"},
                "-",
                btnInfoView,
                btnLogClear,
                //"-",
                //btnView,
                "->",
                {xtype: "tbtext", text: _("ID_WORKSPACE") + "&nbsp;"},
                cboWorkspace,
                "-",
                {xtype: "tbtext", text: _("ID_STATUS") + "&nbsp;"},
                cboStatus,
                "-",
                {xtype: "tbtext", text: _("ID_FROM") + "&nbsp;"},
                dateFrom,
                " ",
                {xtype: "tbtext", text: _("ID_TO") + "&nbsp;"},
                dateTo,
                " ",
                {
                    xtype: "button",
                    text: _("ID_FILTER"),

                    handler: function ()
                    {
                        pagingCron.moveFirst();
                    }
                },
                " ",
                {
                    xtype: "button",
                    text: _("ID_RESET_DATES"),

                    handler: function ()
                    {
                        Ext.getCmp("dateFrom").reset(),
                        Ext.getCmp("dateTo").reset()
                    }
                }
            ],
            bbar: pagingCron,

            title: _("ID_CRON_ACTIONS_LOG"),

            listeners: {
                rowdblclick: function ()
                {
                    logView();
                }
            }
        });

        //Initialize events
        storeCron.load();

        cboPageSize.setValue(pageSize);

        //Load all panels
        var viewport = new Ext.Viewport({
            layout: "fit",
            autoScroll: false,
            items: [grdpnlMain]
        });
    }
}

Ext.onReady(cron.application.init, cron.application);