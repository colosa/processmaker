clickListener = function (node,event){
    if (node.attributes.href){
      parent.location.href = node.attributes.href;
//      Ext.get('casesSubFrame').src=node.attributes.href;
    }
};

Ext.onReady(function() {

var tree = new Ext.tree.TreePanel({
    renderTo: Ext.getBody(),
    title: treeTitle,
    width: 250,
    height: 250,
    userArrows: true,
    animate: true,
    autoScroll: true,
    rootVisible: false,
//    dataUrl: 'casesToReviseTreeContent?APP_UID=4425000044ce3eda54f6d41019986116&DEL_INDEX=3',
    dataUrl: casesPanelUrl,
    root: {
        nodeType : 'async',
        text     : _('ID_TO_REVISE'),
        id       : 'node-root'
    },
    listeners: {
        render: function() {
//            this.getRootNode().expand();
            this.expandAll();
//            this.getNodeById('node-dynaforms').expand();
//            this.getNodeById('node-input-documents').expand();
        },
        click: {
          fn:clickListener
        }
    }
})
    tree.render();
//    tree.expandAll();
//    tree.getNodeById('node-dynaforms').expand();
//    tree.getNodeById('node-input-documents').expand();
});