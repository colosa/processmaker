Ext.namespace("userMain");

userMain.application = {
    init: function ()
    {
        var treepnlMenu = new Ext.tree.TreePanel({
            id: "treepnlMenu",
            region: "west",

            //title: "",
            width: 240,
            collapsible: true,
            collapseMode: "mini",
            hideCollapseTool: true,
            split: true,
            rootVisible: false,
            loader: new Ext.tree.TreeLoader(),
            root: new Ext.tree.AsyncTreeNode({
                expanded: true,
                children: [
                    {
                        id: "nodeInfo",
                        text: "Personal Information",
                        leaf: true,
                        url: "../users/usersInit"

                    }
                ]
            }),
            listeners: {
                click: function (node, evt)
                {
                    document.getElementById("iframe").src = node.attributes.url;
                },
                afterrender: function (treepnl)
                {
                    var index = (CREATE_CLIENT == 1)? 1 : 0;

                    var node = treepnl.getRootNode().childNodes[index];
                    node.select();

                    setTimeout(function () { document.getElementById("iframe").src = (CREATE_CLIENT == 1)? "../oauth2/clientSetup?create_app" : node.attributes.url; }, 5);
                }
            }
        });

        var viewport = new Ext.Viewport({
            layout: "border",
            items: [
                treepnlMenu,
                {
                    xtype: "iframepanel",
                    id: "iframepnlIframe",
                    region: "center",

                    frameConfig: {
                        name: "iframe",
                        id: "iframe"
                    },
                    deferredRender: false
                }
            ]
        });
    }
}

Ext.onReady(userMain.application.init, userMain.application);

