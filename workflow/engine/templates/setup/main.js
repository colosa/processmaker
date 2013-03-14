/*
 * @author: Erik A. Ortiz
 * Aug 20th, 2010 
 */

var _NODE_SELECTED;
var main = function(){
  var cookiep =  new Ext.state.CookieProvider();
  
  var items = [];
  var nodeLoad = [];
  var i;
  
  if (tabActive != "") {
    for(i = 0; i<= tabItems.length - 1; i++) {
      if (tabItems[i].id == tabActive) {
        cookiep.set("admin-tabpanel", {"activeTab": i});
      }
    }
  }
  
  Ext.state.Manager.setProvider(cookiep);
 
  for(i=0; i<tabItems.length; i++){
    
    items[i] = new Ext.tree.TreePanel({
      title: tabItems[i].title,
      id: tabItems[i].id,
      animate:true,
      autoScroll:true,
      loader: new Ext.tree.TreeLoader({
        dataUrl:'mainAjax?request=loadMenu&menu='+tabItems[i].id+'&r='+Math.random()
      }),
      enableDD:true,
      containerScroll: true,
      border: false,
        
      width: 250,
      height: 120,
      dropConfig: {appendOnly:true},
      margins: '0 2 2 2',
      cmargins: '2 2 2 2',
      rootVisible: false,
      root: new Ext.tree.AsyncTreeNode(),
      listeners: {
        click: function (node, e) {
          if (node.attributes.url) {
            document.getElementById("setup-frame").src = node.attributes.url;
            
            _NODE_SELECTED = node.attributes.id;
          }
        },
        render: function (tp) {
          var loader = tp.getLoader();
          var node;
          
          loader.on("load", function () {
            if (_item_selected != "") {
              node = tp.getNodeById(_item_selected);
              
              if (typeof node == "undefined") {
                node = tp.getRootNode().childNodes[0];
              }
            }
            else {
              node = tp.getRootNode().childNodes[0];
            }
            
            if (node) {
              if (node.attributes.url) {
                document.getElementById("setup-frame").src = node.attributes.url;
              
                node.select();
                _NODE_SELECTED = node.attributes.id;
              }
            }
            
            if (typeof(nodeLoad[tp.id]) == "undefined") {
              node = tp.getRootNode().childNodes[0];
              
              if (node) {
                nodeLoad[tp.id] = [];
                nodeLoad[tp.id]["id"] = node.attributes.id;
              }
            }
          });
        },
        show: function (tp) {
          if (!(typeof(nodeLoad[tp.id]) == "undefined")) {
            //true - load url
            var node = tp.getNodeById(nodeLoad[tp.id]["id"]);

            if (node.attributes.url) {
              document.getElementById("setup-frame").src = node.attributes.url;
              
              node.select();
              _NODE_SELECTED = node.attributes.id;
            }
          }
          //else {
          //  //false - load url
          //}
        }
      }
    });
  }

  var viewport = new Ext.Viewport({
    layout: 'border',
    items: [
      new Ext.TabPanel({
        region: 'west',
        id: 'west-panel', // see Ext.getCmp() below
        title: 'West',
        split: true,
        width: 240,
        minSize: 175,
        maxSize: 400,
        collapsible: true,
        animCollapse: true,
        
        margins: '0 0 0 5',
        activeTab: 0,
        enableTabScroll: true,
        stateId:'admin-tabpanel',
        stateEvents:['tabchange'],
        getState:function() {
          return {
            activeTab:this.items.indexOf(this.getActiveTab())
          };
        },
        items: items
      }),
      {
        region: 'center',
        xtype  : 'iframepanel',
        frameConfig:{
          name : 'setup-frame',
          id   : 'setup-frame'
        },
        deferredRender: false
      }
    ]
  });
  //oClientWinSize = parent.getClientWindowSize();
  //parent.document.getElementById('adminFrame').style.height = oClientWinSize.height-105;  
}

new Ext.KeyMap(document, {
  key: Ext.EventObject.F5,
    fn: function(keycode, e) {
    	if (! e.ctrlKey) {
        if (Ext.isIE) {
            // IE6 doesn't allow cancellation of the F5 key, so trick it into
            // thinking some other key was pressed (backspace in this case)
            e.browserEvent.keyCode = 8;
        }
        e.stopEvent();
        //document.location = document.location;
        document.getElementById('setup-frame').src = document.getElementById('setup-frame').src;
      }
      else
    Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
  }
});

Ext.onReady(main);


