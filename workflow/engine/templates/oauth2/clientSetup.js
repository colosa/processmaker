Ext.namespace("clientSetup");

clientSetup.application = {
    init: function ()
    {
        var OCLIENTOPTION = "";
        var loadMaskData = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_LOADING_GRID")});

        function oauthClientProcessAjax(option, oauthClientId)
        {
            //Message
            var msg = "";

            switch (option) {
                case "INS":
                    msg = "Insert data...";
                    break;
                case "UPD":
                    msg = "Update data...";
                    break;
                case "DEL":
                    msg = "Delete data...";
                    break;
                //case "LST":
                //    break;
            }

            var loadMaskAux = new Ext.LoadMask(Ext.getBody(), {msg: msg});
            loadMaskAux.show();

            //Data
            var p;

            switch (option) {
                case "INS":
                    p = {
                        option: option,
                        name: Ext.getCmp("txtName").getValue(),
                        description: Ext.getCmp("txtDescription").getValue(),
                        webSite: Ext.getCmp("txtWebSite").getValue(),
                        redirectUri: Ext.getCmp("txtRedirectUri").getValue()
                    };
                    break;
                case "UPD":
                    p = {
                        option: option,
                        oauthClientId: oauthClientId,
                        name: Ext.getCmp("txtName").getValue(),
                        description: Ext.getCmp("txtDescription").getValue(),
                        webSite: Ext.getCmp("txtWebSite").getValue(),
                        redirectUri: Ext.getCmp("txtRedirectUri").getValue()
                    };
                    break;
                case "DEL":
                    p = {
                        option: option,
                        oauthClientId: oauthClientId
                    };
                    break;
                //case "LST":
                //    break;
            }

            Ext.Ajax.request({
                url: "../oauth2/clientSetupAjax",
                method: "POST",
                params: p,

                success: function (response, opts)
                {
                    var dataResponse = Ext.util.JSON.decode(response.responseText);

                    switch (option) {
                        case "INS":
                        case "UPD":
                        case "DEL":
                            if (dataResponse.status) {
                                if (dataResponse.status == "OK") {
                                    pagingData.moveFirst();

                                    switch (option) {
                                        case "INS":
                                            dataResponse.data.CLIENT_NAME = Ext.getCmp("txtName").getValue();

                                            insertSuccessView(dataResponse.data);
                                            break;
                                    }
                                } else {
                                    Ext.MessageBox.alert(_("ID_ALERT"), dataResponse.message);
                                }
                            }
                            break;
                        //case "LST":
                        //    break;
                    }

                    loadMaskAux.hide();
                },
                failure: function (response, opts)
                {
                    loadMaskAux.hide();
                }
            });
        }

        function oauthClientSetForm(option, oauthClientId)
        {
            switch (option) {
                case "INS":
                    Ext.getCmp("oauthClientId").setValue("");
                    Ext.getCmp("txtName").setValue("");
                    Ext.getCmp("txtDescription").setValue("");
                    Ext.getCmp("txtWebSite").setValue("");
                    Ext.getCmp("txtRedirectUri").setValue("");

                    Ext.getCmp("txtName").allowBlank = true;

                    winData.setTitle("New Application");
                    winData.show();

                    Ext.getCmp("btnSubmit").btnEl.dom.innerHTML = "Register Application";

                    Ext.getCmp("txtName").allowBlank = false;
                    break;
                case "UPD":
                    var record = grdpnlMain.getSelectionModel().getSelected();

                    if (typeof(record) != "undefined") {
                        Ext.getCmp("oauthClientId").setValue(record.get("CLIENT_ID")); //oauthClientId
                        Ext.getCmp("txtName").setValue(record.get("CLIENT_NAME"));
                        Ext.getCmp("txtDescription").setValue(record.get("CLIENT_DESCRIPTION"));
                        Ext.getCmp("txtWebSite").setValue(record.get("CLIENT_WEBSITE"));
                        Ext.getCmp("txtRedirectUri").setValue(record.get("REDIRECT_URI"));

                        Ext.getCmp("txtName").allowBlank = true;

                        winData.setTitle("Edit Application");
                        winData.show();

                        Ext.getCmp("btnSubmit").btnEl.dom.innerHTML = "Save Changes";

                        Ext.getCmp("txtName").allowBlank = false;
                    }
                    break;
            }
        }

        function insertSuccessView(data)
        {
            var html = "Your application \"" + data.CLIENT_NAME + "\" was registered successfully!" + "<br /><br />";
            html = html + "<h3>" + "Application Credentials" + "</h3><br />";
            html = html + "&nbsp;&nbsp;<b>* " + "Client ID" + ":</b> " + data.CLIENT_ID + "<br />";
            html = html + "&nbsp;&nbsp;<b>* " + "Client Secret" + ":</b> " + data.CLIENT_SECRET + "<br /><br />";
            html = html + "<h3>" + "Next Steps" + "</h3><br />";
            html = html + "&nbsp;&nbsp;<b>* </b>" + "Make authorize requests" + "<br />";
            html = html + "&nbsp;&nbsp;<b>* </b>" + "Get access tokens" + "<br />";

            var formItems = Ext.getCmp("frmInsertSuccessView").form.items;
            formItems.items[0].setValue(html);

            winInsertSuccess.show();
        }

        function detailView()
        {
            var record = grdpnlMain.getSelectionModel().getSelected();

            if (typeof(record) != "undefined") {
                var html = "Your application \"" + record.get("CLIENT_NAME") + "\"" + "<br /><br />";
                html = html + "<h3>" + "Application Details" + "</h3><br />";
                html = html + "&nbsp;&nbsp;<b>* " + "Description" + ":</b> " + record.get("CLIENT_DESCRIPTION") + "<br />";
                html = html + "&nbsp;&nbsp;<b>* " + "Web Site" + ":</b> " + record.get("CLIENT_WEBSITE") + "<br />";
                html = html + "&nbsp;&nbsp;<b>* " + "Callback URL" + ":</b> " + record.get("REDIRECT_URI") + "<br /><br />";
                html = html + "<h3>" + "Application Credentials" + "</h3><br />";
                html = html + "&nbsp;&nbsp;<b>* " + "Client ID" + ":</b> " + record.get("CLIENT_ID") + "<br />";
                html = html + "&nbsp;&nbsp;<b>* " + "Client Secret" + ":</b> " + record.get("CLIENT_SECRET") + "<br /><br />";

                var formItems = Ext.getCmp("frmDetailView").form.items;
                formItems.items[0].setValue(html);

                winDetail.show();
            }
        }

        function onMnuContext(grid, rowIndex, e)
        {
            e.stopEvent();

            var coords = e.getXY();

            mnuContext.showAt([coords[0], coords[1]]);
        }

        //Variables
        var pageSize = parseInt(CONFIG.pageSize);

        //Stores
        var storeData = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({
                url: "../oauth2/clientSetupAjax",
                method: "POST"
            }),

            //baseParams: {"option": "LST", "pageSize": pageSize},

            reader: new Ext.data.JsonReader({
                totalProperty: "resultTotal",
                root: "resultRoot",
                fields: [
                    {name: "CLIENT_ID", type: "string"},
                    {name: "CLIENT_SECRET", type: "string"},
                    {name: "CLIENT_NAME", type: "string"},
                    {name: "CLIENT_DESCRIPTION", type: "string"},
                    {name: "CLIENT_WEBSITE", type: "string"},
                    {name: "REDIRECT_URI", type: "string"},
                    {name: "USR_UID", type: "string"}
                ]
            }),

            //autoLoad: true, //First call
            remoteSort: true,

            listeners: {
                beforeload: function (store, opt)
                {
                    loadMaskData.show();

                    btnEdit.setDisabled(true);
                    btnDelete.setDisabled(true);
                    btnDetail.setDisabled(true);

                    this.baseParams = {
                        option: "LST",
                        pageSize: pageSize,
                        search: Ext.getCmp("txtSearch").getValue()
                    };
                },
                load: function (store, record, opt)
                {
                    loadMaskData.hide();

                    if (CREATE_CLIENT == 1) {
                        OCLIENTOPTION = "INS";
                        CREATE_CLIENT = 0;

                        oauthClientSetForm(OCLIENTOPTION, "");
                    }
                }
            }
        });

        var storePageSize = new Ext.data.SimpleStore({
            fields: ["size"],
            data: [["20"], ["30"], ["40"], ["50"], ["100"]],
            autoLoad: true
        });
        
        //Components
        var winData = new Ext.Window({
            layout: "fit",
            width: 550,
            height: 475,
            //title: "",
            modal: true,
            resizable: false,
            closeAction: "hide",

            items: [
                new Ext.FormPanel({
                    id: "frmOauthClient",

                    frame: true,
                    labelAlign: "right",
                    labelWidth: 80,
                    autoWidth: true,
                    //height: 395,
                    autoScroll: false,

                    defaults: {width: 425},

                    items: [
                        {
                            xtype: "hidden",
                            id: "oauthClientId",
                            name: "oauthClientId",
                        },
                        {
                            xtype: "textfield",
                            id: "txtName",
                            name: "txtName",

                            fieldLabel: "Name",
                            validator: function(value){
                                var val = (value=='')? false: true;
                                return val;
                            }
                        },
                        {
                            xtype: "label",

                            fieldLabel: "&nbsp;",
                            labelSeparator: "",

                            html: "<span style=\"font-size: 11px;\">" + "Your application name. This is used to attribute the source in user-facing authorization screens. 32 characters max." + "</span><div style=\"height: 5px;\"></div>"
                        },
                        {
                            xtype: "textarea",
                            id: "txtDescription",
                            name: "txtDescription",

                            fieldLabel: "Description",
                            height: 55
                        },
                        {
                            xtype: "label",

                            fieldLabel: "&nbsp;",
                            labelSeparator: "",

                            html: "<span style=\"font-size: 11px;\">" + "Your application description, which will be shown in user-facing authorization screens. Between 10 and 200 characters max." + "</span><div style=\"height: 5px;\"></div>"
                        },
                        {
                            xtype: "textfield",
                            id: "txtWebSite",
                            name: "txtWebSite",

                            fieldLabel: "Web Site",                           
                            validator: function (value){ 
                                var regexStringIp = new RegExp(/^https?\:\/\/[^\/\s]+(\/.*)?$/);
                                var regexNumberIp = new RegExp(/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/);  
                                var result = (value.match(regexStringIp) || value.match(regexNumberIp)) ? true : false;
                                return result;                                   
                            }
                        },
                        {
                            xtype: "label",

                            fieldLabel: "&nbsp;",
                            labelSeparator: "",

                            html: "<span style=\"font-size: 11px;\">" + "Your application's publicly accessible home page, where users can go to download, make use of, or find out more information about your application. This fully-qualified URL is used in the source attribution for request created by your application and will be shown in user-facing authorization screens. (If you don't have a URL yet, just put a placeholder here but remember to change it later.)" + "</span><div style=\"height: 5px;\"></div>"
                        },
                        {
                            xtype: "textfield",
                            id: "txtRedirectUri",
                            name: "txtRedirectUri",

                            fieldLabel: "Callback URL"/*,
                            vtype: "url"*/
                        },
                        {
                            xtype: "label",

                            fieldLabel: "&nbsp;",
                            labelSeparator: "",

                            html: "<span style=\"font-size: 11px;\">" + "URL where redirected after successfully authenticating (calling the {workspace}/oauth2/authorize endpoint). This URL typically contains code to get the access token from the {workspace}/oauth2/token endpoint. To prevent your application from using callbacks, leave this field blank." + "</span>"
                        }
                    ]
                })
            ],
            buttons: [
                {
                    id: "btnSubmit",
                    text: "Save",
                    handler: function ()
                    {
                        if (Ext.getCmp("frmOauthClient").getForm().isValid()) {
                            oauthClientProcessAjax(OCLIENTOPTION, Ext.getCmp("oauthClientId").getValue());

                            Ext.getCmp("txtName").allowBlank = true;

                            winData.hide();
                        } else {
                            Ext.MessageBox.alert(_("ID_INVALID_DATA"), _("ID_CHECK_FIELDS_MARK_RED"));
                        }
                    }
                },
                {
                    text: _("ID_CANCEL"),
                    handler: function ()
                    {
                        Ext.getCmp("txtName").allowBlank = true;

                        winData.hide();
                    }
                }
            ]
        });

        var winInsertSuccess = new Ext.Window({
            layout: "fit",
            width: 450,
            height: 300,
            title: "Registration Success",
            modal: true,
            resizable: false,
            closeAction: "hide",

            items: [
                new Ext.FormPanel({
                    id: "frmInsertSuccessView",

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

        var winDetail = new Ext.Window({
            layout: "fit",
            width: 450,
            height: 300,
            title: "Detail",
            modal: true,
            resizable: false,
            closeAction: "hide",

            items: [
                new Ext.FormPanel({
                    id: "frmDetailView",

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

        var btnNew = new Ext.Action({
            id: "btnNew",

            text: _("ID_NEW"),
            iconCls: "button_menu_ext ss_sprite ss_add",

            handler: function ()
            {
                OCLIENTOPTION = "INS";

                oauthClientSetForm(OCLIENTOPTION, "");
            }
        });

        var btnEdit = new Ext.Action({
            id: "btnEdit",

            text: _("ID_EDIT"),
            iconCls: "button_menu_ext ss_sprite ss_pencil",

            handler: function ()
            {
                var record = grdpnlMain.getSelectionModel().getSelected();

                if (typeof(record) != "undefined") {
                    OCLIENTOPTION = "UPD";

                    oauthClientSetForm(OCLIENTOPTION, record.get("CLIENT_ID"));
                }
            }
        });

        var btnDelete = new Ext.Action({
            id: "btnDelete",

            text: _("ID_DELETE"),
            iconCls: "button_menu_ext ss_sprite ss_cross",

            handler: function ()
            {
                var record = grdpnlMain.getSelectionModel().getSelected();

                if (typeof(record) != "undefined") {
                    Ext.MessageBox.confirm(
                        _("ID_CONFIRM"),
                        "Do you want to delete selected Application?",
                        function (btn)
                        {
                            if (btn == "yes") {
                                OCLIENTOPTION = "DEL";

                                oauthClientProcessAjax(OCLIENTOPTION, record.get("CLIENT_ID"));
                            }
                        }
                    );
                }
            }
        });

        var btnDetail = new Ext.Action({
            id: "btnDetail",

            text: _("ID_DETAILS"),
            iconCls: "button_menu_ext ss_sprite ss_zoom",

            handler: function ()
            {
                detailView();
            }
        });

        var btnSearch = new Ext.Action({
            id: "btnSearch",

            text: _("ID_SEARCH"),

            handler: function ()
            {
                pagingData.moveFirst();
            }
        });

        var txtSearch = new Ext.form.TextField({
            id: "txtSearch",

            emptyText: _("ID_ENTER_SEARCH_TERM"),
            width: 150,
            allowBlank: true,

            listeners: {
                specialkey: function (f, e)
                {
                    if (e.getKey() == e.ENTER) {
                        pagingData.moveFirst();
                    }
                }
            }
        });

        var btnTextClear = new Ext.Action({
            id: "btnTextClear",

            text: "X",
            ctCls: "pm_search_x_button",
            handler: function ()
            {
                txtSearch.reset();
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

                    pagingData.pageSize = pageSize;
                    pagingData.moveFirst();
                }
            }
        });

        var pagingData = new Ext.PagingToolbar({
            id: "pagingData",

            pageSize: pageSize,
            store: storeData,
            displayInfo: true,
            displayMsg: "Displaying data " + "{" + "0" + "}" + " - " + "{" + "1" + "}" + " of " + "{" + "2" + "}",
            emptyMsg: "No data to display",
            items: ["-", "Page size:", cboPageSize]
        });

        var cmodel = new Ext.grid.ColumnModel({
            defaults: {
                sortable: true
            },

            columns: [
                {id: "CLIENT_ID", dataIndex: "CLIENT_ID", hidden: true, hideable: false},
                {id: "CLIENT_SECRET", dataIndex: "CLIENT_SECRET", hidden: true, hideable: false},
                {id: "CLIENT_NAME", dataIndex: "CLIENT_NAME", header: "Name", width: 200, align: "left"},
                {id: "CLIENT_DESCRIPTION", dataIndex: "CLIENT_DESCRIPTION", header: "Description", width: 250, align: "left"},
                {id: "CLIENT_WEBSITE", dataIndex: "CLIENT_WEBSITE", hidden: true, hideable: false},
                {id: "REDIRECT_URI", dataIndex: "REDIRECT_URI", hidden: true, hideable: false},
                {id: "USR_UID", dataIndex: "USR_UID", hidden: true, hideable: false}
            ]
        });

        var smodel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                rowselect: function (sm)
                {
                    btnEdit.setDisabled(false);
                    btnDelete.setDisabled(false);
                    btnDetail.setDisabled(false);
                },
                rowdeselect: function (sm)
                {
                    btnEdit.setDisabled(true);
                    btnDelete.setDisabled(true);
                    btnDetail.setDisabled(true);
                }
            }
        });

        var grdpnlMain = new Ext.grid.GridPanel({
            id: "grdpnlMain",

            store: storeData,
            colModel: cmodel,
            selModel: smodel,

            columnLines: true,
            viewConfig: {forceFit: true}, //Expand all columns
            enableColumnResize: true,
            enableHdMenu: true, //Menu of the column
            //autoExpandColumn: "CLIENT_DESCRIPTION",

            //tbar: [btnNew, "-", btnEdit, btnDelete, "-", btnDetail, "->", txtSearch, btnTextClear, btnSearch],
            tbar: [btnNew, "-", btnEdit, btnDelete, "-", btnDetail],
            //bbar: pagingData,

            //style: "margin: 0 auto 0 auto;",
            //width: 550,
            //height: 450,
            title: "<div><div style=\"float: left;\">" + "User Applications" + "</div><div id=\"divAccessTokenSetup\" style=\"float: right;\"></div><div style=\"clear: both; height: 0; line-height:0; font-size: 0;\"></div></div>",
            border: false,

            listeners: {
                afterrender: function (grid)
                {
//                    var btn = new Ext.Button({
//                        text: "&nbsp;" + "Applications",
//                        iconCls: "button_menu_ext ss_sprite ss_arrow_left",
//                        renderTo: "divAccessTokenSetup",
//
//                        handler: function ()
//                        {
//                            location.href = "accessTokenSetup";
//                        }
//                    });
                },
                rowdblclick: function (grid, rowIndex, evt)
                {
                    var record = grdpnlMain.getSelectionModel().getSelected();

                    if (typeof(record) != "undefined") {
                        OCLIENTOPTION = "UPD";

                        oauthClientSetForm(OCLIENTOPTION, record.get("CLIENT_ID"));
                    }
                }
            }
        });

        //Menu context
        var mnuContext = new Ext.menu.Menu({
            id: "mnuContext",

            items: [btnEdit, btnDelete, "-", btnDetail]
        });

        //Initialize events
        grdpnlMain.on(
            "rowcontextmenu",
            function (grid, rowIndex, evt)
            {
                var sm = grid.getSelectionModel();
                sm.selectRow(rowIndex, sm.isSelected(rowIndex));
            },
            this
        );

        grdpnlMain.addListener("rowcontextmenu", onMnuContext, this);

        cboPageSize.setValue(pageSize);

        grdpnlMain.store.load();

        //Load all panels
        var viewport = new Ext.Viewport({
            layout: "fit",
            autoScroll: false,
            items: [grdpnlMain]
        });
    }

}

Ext.onReady(clientSetup.application.init, clientSetup.application);