Ext.chart.Chart.CHART_URL = '/images/charts.swf';
Ext.FlashComponent.EXPRESS_INSTALL_URL = '/images/expressinstall.swf';

var conn = new Ext.data.Connection();

//function getDefaultDashboard(dashboardTabPanels) {
//  var defaultDashboard = "mainDashboard";
//  dashboardTabPanels.setActiveTab(defaultDashboard);
//  Ext.getCmp("dashboardTabPanels").getEl().mask("Please wait, retrieving data...", "ext-el-mask-msg x-mask-loading");
//
//  conn.request({
//    url : 'casesStartPage_Ajax.php',
//    method : 'POST',
//    params : {action: 'getDefaultDashboard'},
//    success : function(responseObject) {
//      var responseData = Ext.decode(responseObject.responseText);
//      if (responseData.defaultTab) {
//        defaultDashboard = responseData.defaultTab;
//      }
//      if (dashboardTabPanels.getItem(defaultDashboard)) {
//        dashboardTabPanels.setActiveTab(defaultDashboard);
//      }
//      Ext.getCmp("dashboardTabPanels").getEl().unmask();
//    },
//    failure : function() {
//      Ext.getCmp("dashboardTabPanels").getEl().unmask();
//    }
//  });
//  return defaultDashboard;
//}
//
//function getOtherDashboards(dashboardTabPanels) {
//	connA.request({
//		url : 'casesStartPage_Ajax.php',
//		method : 'POST',
//		params : {action : 'getRegisteredDashboards'},
//		success : function(responseObject) {
//		  var i, tabInfo;
//			var response = Ext.util.JSON.decode(responseObject.responseText);
//			for (i = 0; i < response.length; i++) {
//				tabInfo = response[i];
//				if (tabInfo.sName) {
//					dashboardTabPanels.add({
//						title : tabInfo.sName,
//						id : tabInfo.sNamespace + "-" + tabInfo.sName,
//						iconCls : tabInfo.sIcon,// 'ICON_CASES_START_PAGE',
//						autoLoad : {
//							url : tabInfo.sPage,
//							scripts : true
//						}
//					});
//				}
//			}
//			getDefaultDashboard(dashboardTabPanels);
//		},
//		failure : function() {
//			// grid.getGridEl().unmask(true);
//			getDefaultDashboard(dashboardTabPanels);
//			Ext.Msg.alert('Status', 'Unable to get Dashboards');
//		}
//	});
//}

var Docs = {};
var MainPanel = function() {
	MainPanel.superclass.constructor.call(this, {
		id : 'doc-body',
		region : 'center',
		resizeTabs : true,
		minTabWidth : 135,
		tabWidth : 135,
		plugins : new Ext.ux.TabCloseMenu(),
		enableTabScroll : true,
		activeTab : 0,
		items : []
	});
};

// console.info("Main Panel - End");
Ext.extend(
  MainPanel,
	Ext.TabPanel,
	{
    initEvents : function(){
			MainPanel.superclass.initEvents.call(this);
		},
		onClick: function(e, target, elementselected){
			return;
			if (target = e.getTarget('a:not(.exi)', 3)) {
				var cls = Ext.fly(target).getAttributeNS('ext','cls');
				e.stopEvent();
				if (cls) {
					var member = Ext.fly(target).getAttributeNS('ext','member');
					this.loadClass(target.href, cls, member);
			  } else if (target.className == 'inner-link'){
					this.getActiveTab().scrollToSection(target.href.split('#')[1]);
				} else {
					window.open(target.href);
				}
			} else if (target = e.getTarget('.micon', 2)){
				e.stopEvent();
				var tr = Ext.fly(target.parentNode);
				if (tr.hasClass('expandable')) {
					tr.toggleClass('expanded');
				}
			}
		},
    activateDefaultTab : function() {
      var page = window.location.href.split('?')[1];
      if (page) {
        var ps = Ext.urlDecode(page);
        if (ps.action) {
          defaultDashboard = ps.action;
          if (this.getItem(defaultDashboard)) {
            this.setActiveTab(defaultDashboard);
          }
        }
      }
    },
		loadOtherDashboards : function(){
			dashboardTabPanels = this;
			conn.request({
				url : 'casesStartPage_Ajax.php',
				method : 'POST',
				params : {action : 'getRegisteredDashboards'},
				success : function(responseObject) {
					var response = Ext.util.JSON.decode(responseObject.responseText);
					var i, tabInfo;
					for (i=0;i < response.length; i++){
						tabInfo = response[i];
						if (tabInfo.sName) {
							dashboardTabPanels.add({
								title : tabInfo.sName,
								id : tabInfo.sNamespace + "-"	+ tabInfo.sName,
								iconCls : tabInfo.sIcon,// 'ICON_CASES_START_PAGE',
								autoLoad: {
								  url: tabInfo.sPage,
								  scripts: false
								}
						  });
						}
					}
					dashboardTabPanels.activateDefaultTab();
				},
				failure : function() {
					dashboardTabPanels.activateDefaultTab();
					Ext.Msg.alert( _('ID_STATUS'), _('ID_UNABLE_GET_DASHBOARDS'));
				}
			});
		}
	}
);

var mainPanel = new MainPanel();

Ext.onReady(function() {
  //The Quicktips are used for the toolbar and Tree mouseover tooltips!
  Ext.QuickTips.init();

	var Cookies = {};
	Cookies.set = function(name, value) {
		var argv = arguments;
		var argc = arguments.length;
		var expires = (argc > 2) ? argv[2] : null;
		var path = (argc > 3) ? argv[3] : '/';
		var domain = (argc > 4) ? argv[4] : null;
		var secure = (argc > 5) ? argv[5] : false;
		document.cookie = name
				+ "="
				+ escape(value)
				+ ((expires == null) ? "" : ("; expires=" + expires
						.toGMTString()))
				+ ((path == null) ? "" : ("; path=" + path))
				+ ((domain == null) ? "" : ("; domain=" + domain))
				+ ((secure == true) ? "; secure" : "");
	};

	Cookies.get = function(name) {
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;
		var j = 0;
		while (i < clen) {
			j = i + alen;
			if (document.cookie.substring(i, j) == arg)
				return Cookies.getCookieVal(j);
			i = document.cookie.indexOf(" ", i) + 1;
			if (i == 0)
				break;
		}
		return null;
	};

	Cookies.clear = function(name) {
		if (Cookies.get(name)) {
			document.cookie = name + "="
					+ "; expires=Thu, 01-Jan-70 00:00:01 GMT";
		}
	};

	Cookies.getCookieVal = function(offset) {
		var endstr = document.cookie.indexOf(";", offset);
		if (endstr == -1) {
			endstr = document.cookie.length;
		}
		return unescape(document.cookie.substring(offset, endstr));
	};

	mainPanel.on('tabchange', function(tp, tab) {
		if (tab.getUpdater) {
			var thisObj = tab.getUpdater();
			if (thisObj) {
				thisObj.refresh();
			}
		}
	});

	var viewport = new Ext.Viewport({
		layout : 'border',
		items : [mainPanel]
	});

	mainPanel.loadOtherDashboards();

	viewport.doLayout();

  //routine to hide the debug panel if it is open
  if( typeof parent != 'undefined' ){
    if( parent.PANEL_EAST_OPEN ){
      parent.PANEL_EAST_OPEN = false;
      parent.Ext.getCmp('debugPanel').hide();
      parent.Ext.getCmp('debugPanel').ownerCt.doLayout();
    }
  }
});