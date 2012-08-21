/**
 * Main Controller for processMaker v2.x
 * @date Jul 17, 2011
 * @author Erik Amaru Ortiz <erik@colosa.com>
 */

var Main = function() {
  return {
    /** properties */
    panels : new Array(),
    configuration: {},
    viewport : null,
    systemInfoWindow : null,

    /** init method */
    init : function() {
      
      Ext.QuickTips.init();
      Ext.form.Field.prototype.msgTarget = 'side';
      
      this.configureComponents();
      this.buildComponents();
      
      this.viewport = new Ext.Viewport({
        layout: 'border',
        items: [this.panels]
      });

      Ext.getCmp('eastPanel').hide();
      Ext.getCmp('westPanel').hide();
      Ext.getCmp('southPanel').hide();
      
      Ext.getCmp('northPanel').update(Ext.fly('header-content').dom.innerHTML);
      Ext.getCmp('centerPanel').ownerCt.doLayout();

      Ext.get('options-tool').on('click', function(eventObj, elRef) {
        var conn = new Ext.data.Connection();
        eventObj.stopEvent();
        if (!this.ctxMenu) {
          Main.buildUserMenu(this);
        }
        this.ctxMenu.show(elRef);
      });

      if (typeof flyNotify != 'undefined') {
        Ext.msgBoxSlider.msgTopCenter(flyNotify.type, flyNotify.title, flyNotify.text, flyNotify.time);
      }
    }
  }
}();

Main.configureComponents = function()
{
  this.configuration.eastPanel = {
    id:'eastPanel',    
    region: 'east',
    width: 200,
    height: 500,
    minSize: 175,
    maxSize: 400,
    split: true,
    collapsible: true,
    items: []
  };
  
  this.configuration.centerPanel = {
    id:'centerPanel',
    region: 'center',
    layout: 'fit',
    width: 200,
    margins: '0 0 0 0' // top right botom left
  };
  
  this.configuration.centerPanel.items = new Array();
  this.configuration.centerPanel.items.push({
    xtype:"tabpanel",
    id: 'mainTabPanel',
    defaultType:"iframepanel",
    enableTabScroll: true,
    activeTab: activeTab != '' ? activeTab : 0
  });
  
  this.configuration.westPanel = {
    id:'westPanel',
    title: '',
    region: 'west',
    width: 200,
    split: true,
    collapsible: true,
    items: []
  };
  
  this.configuration.northPanel = {
    id:'northPanel',
    region: 'north',
    height: 50,
    //applyTo: 'panel-header',
    margins: '0 0 0 0', // top right botom left
    items: []
  };
  
  this.configuration.southPanel = {
    id:'southPanel',
    region: 'south',
    height: 68,    
    margins: '0 0 0 0', // top right botom left
    items: []
  };
  
  this.configuration.userMenu = {}
  this.configuration.userMenu.items = new Array();

  if (switchInterface) {
    this.configuration.userMenu.items.push({
      text : _("ID_SWITCH_INTERFACE"),
      iconCls: 'ss_sprite ss_arrow_switch',
      handler: function() {
        var url = '../uxs/home';

        if (typeof parent != 'undefined') {
          parent.location.href = url;
        }
        else {
          location.href = url; 
        }
      }
    });    
  }

  this.configuration.userMenu.items.push({
    text : _("ID_VIEW_EDIT_PROFILE"),
    icon: '/images/profile-picture.png',
    handler: function() {
      Main._addTab('profile', 'Profile', 'users/usersInit');
    }
  });

  /*this.configuration.userMenu.items.push({
    id:'skinMenu',
    text : _("ID_SKINS"),
    icon: '/images/icon-pmskins.png'
  });*/
  
  if (showSystemInfo) {
    this.configuration.userMenu.items.push({
      text : _('ID_SYSTEM_INFO'),
      icon: '/images/sys-info-icon.png',
      handler: systemInfo
    });
  }

  this.configuration.userMenu.items.push({
    text : _('ID_LOGOUT'),
    icon: '/images/logout.gif',
    handler: function() {
      location.href = 'main/login';
    }
  });
};

Main.buildComponents = function()
{
  var centerTabPanelItems = new Array();  
  for (var i=0; i<meta.menu.length; i++) {
    menuItem = meta.menu[i];
    target = menuItem.target;

    if (activeTab != '') {
      if (i == activeTab) {
        target = menuItem.target + '?' + urlAddGetParams;
      }
    } 
    else {
      target = menuItem.target + '?' + urlAddGetParams;
    }

    centerTabPanelItems.push({
      id: 'pm-option-' + menuItem.idName.toLowerCase(),
      title: menuItem.label.toUpperCase(),
      iconCls: 'x-pm-tabmenu ' + menuItem.elementclass,
      defaultSrc: target,
      frameConfig:{
        name : 'pm-frame-' + menuItem.idName.toLowerCase(),
        id   : 'pm-frame-' + menuItem.idName.toLowerCase()
      },
      loadMask:{msg: _('ID_LOADING_GRID')}
    });
  }
  
  this.configuration.centerPanel.items[0].items = centerTabPanelItems;
  
  this.panels.push(new Ext.Panel(this.configuration.eastPanel));
  this.panels.push(new Ext.Panel(this.configuration.centerPanel));
  this.panels.push(new Ext.Panel(this.configuration.westPanel));
  this.panels.push(new Ext.Panel(this.configuration.southPanel));
  this.panels.push(new Ext.Panel(this.configuration.northPanel));
};

Main.buildUserMenu = function(obj)
{
  /*var skinMenu = new Ext.ux.menu.StoreMenu({ 
    url:'setup/skin_Ajax.php',
    baseParams: { 
      action: 'skinList',
      type: 'menu'
    } 
  });
  
  this.configuration.userMenu.items[1].menu = skinMenu;*/
  obj.ctxMenu = new Ext.menu.Menu(this.configuration.userMenu);
};

Main._addTab = function(id, title, src) 
{
  var TabPanel = Ext.getCmp('mainTabPanel');
  tabId = 'pm-maintab-' + id;
  var tab = TabPanel.getItem(tabId);
  
  if (!tab) {
    TabPanel.add({
      id: tabId,
      title: title.toUpperCase(),
      iconCls: 'x-pm-tabmenu x-pm-' + id,
      defaultSrc: src,
      frameConfig:{
        name : 'pm-frame-' + tabId.toLowerCase(),
        id   : 'pm-frame-' + tabId.toLowerCase()
      },
      loadMask:{msg: _('ID_LOADING_GRID')},
      closable:true
    }).show();
    
    TabPanel.doLayout();
    tab = Ext.getCmp(tabId); 
  }
  
  TabPanel.setActiveTab(tabId);
};

var systemInfo = function()
{
  if(Main.systemInfoWindow == null){
    var sysInfPanel = PMExt.createInfoPanel('main/getSystemInfo');

    Main.systemInfoWindow = new Ext.Window({
      layout:'fit',
      width:500,
      height:430,
      closeAction:'hide',
      items: [sysInfPanel]
    });
  }
  Main.systemInfoWindow.show(this);
}

function changeSkin(newSkin, currentSkin)
{
  currentLocation = top.location.href;
  newLocation = currentLocation.replace("/"+currentSkin+"/","/"+newSkin+"/");
  top.location.href = newLocation;
}

Ext.onReady(Main.init, Main, true);

