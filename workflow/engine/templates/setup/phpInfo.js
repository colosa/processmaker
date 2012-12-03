Ext.namespace("phpInfo");

phpInfo.application = {
    init: function ()
    {
        //Components
        var pnlMain = new Ext.Panel({
            id: "pnlMain",

            layout: "border",

            border: false,
            title: _("ID_PHP_INFO"),

            items: [
                {
                    xtype: "panel",

                    region: "center",

                    margins: {top: 10, right: 10, bottom: 10, left: 10},
                    border: false,

                    html: "<iframe src=\"../setup/systemInfo?option=phpinfo\" width=\"100%\" height=\"100%\" frameborder=\"0\" style=\"border: 0;\"></iframe>"
                }
            ]
        });

        //Load all panels
        var viewport = new Ext.Viewport({
            layout: "fit",
            items: [pnlMain]
        });
    }
}

Ext.onReady(phpInfo.application.init, phpInfo.application);

