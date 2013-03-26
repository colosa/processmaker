var Actions = {};
var showCaseNavigatorPanel;
var hideCaseNavigatorPanel;
var informationMenu;
var caseMenuOpen = false;
var menuSelectedTitle = [];
var _ENV_CURRENT_DATE;
var winTree;

historyGridListChangeLogGlobal = {};
historyGridListChangeLogGlobal.idHistory = '';
historyGridListChangeLogGlobal.tasTitle = '';

historyGridListChangeLogGlobal.viewIdDin = '';
historyGridListChangeLogGlobal.viewIdHistory = '';
historyGridListChangeLogGlobal.viewDynaformName = '';
historyGridListChangeLogGlobal.dynDate = '';

ActionTabFrameGlobal = {};
ActionTabFrameGlobal.tabName = '';
ActionTabFrameGlobal.tabTitle = '';
ActionTabFrameGlobal.tabData = '';

Ext.onReady(function(){
  openToRevisePanel = function() {
    var treeToRevise = new Ext.tree.TreePanel({
      title: treeToReviseTitle,
      width: 250,
      height: 250,
      userArrows: true,
      animate: true,
      autoScroll: true,
      rootVisible: false,
      dataUrl: casesPanelUrl,
      root: {
        nodeType: 'async',
        text    : 'To Revise',
        id      : 'node-root'
      },
      listeners: {
        render: function() {
          this.expandAll();
        }
      }
    });

    if (typeof(winTree) == 'undefined') {
      winTree = new Ext.Window({
        id          : 'toReviseWindow',
        width       : 220,
        height      : 300,
        el          : 'toReviseTree',
        collapsible : true,
        plain       : true,
        x           : 100,
        y           : 100,
        constrain   : true,
        items       : [treeToRevise],
        closeAction : 'hide'
      });
    }
  };

  Ext.QuickTips.init();
  showCaseNavigatorPanel = function(app_status) {
    if (typeof(treeToReviseTitle) != 'undefined') {
      openToRevisePanel();
    }

    if (caseMenuOpen) {
      return false;
    }
    else {
      caseMenuOpen = true;
    }

    //get the menu
    Ext.Ajax.request({
      url : 'ajaxListener',
      params : {action : 'getCaseMenu', app_status:app_status},
      success: function ( result, request ) {
        var data = Ext.util.JSON.decode(result.responseText);
        for(i=0; i<data.length; i++) {
          switch(data[i].id) {
            case 'STEPS':
              if (typeof(treeToReviseTitle) == 'undefined') {
                Ext.getCmp('casesStepTree').root.reload();
              }
              Ext.getCmp('stepsMenu').enable();
              break;
            case 'NOTES':
              Ext.getCmp('caseNotes').show();
              break;

            case 'INFO':
              //filling information menu
              var informationMenu = Ext.getCmp('informationMenu');
              informationMenu.menu.removeAll();
              for(j=0; j<data[i].options.length; j++) {
                informationMenu.menu.add({
                  text: data[i].options[j].text,
                  handler: data[i].options[j].fn != '' ? Actions[data[i].options[j].fn] : function(){}
                });
                menuSelectedTitle[data[i].options[j].fn] = data[i].options[j].text;
              }
              informationMenu.show();
              break;

            case 'ACTIONS':
              var actionMenu = Ext.getCmp('actionMenu');
              actionMenu.menu.removeAll();
              for(j=0; j<data[i].options.length; j++) {
            	if(!data[i].options[j].hide){
                  actionMenu.menu.add({
                    text: data[i].options[j].text,
                    handler: data[i].options[j].fn != '' ? Actions[data[i].options[j].fn] : function(){}
                  });
                  menuSelectedTitle[data[i].options[j].fn] = data[i].options[j].text;
                }
              }
              actionMenu.show();
              break;

            // custom menus from plugins or others
            default:
              var navPanel = Ext.getCmp('navPanel');
              var tb = navPanel.getTopToolbar();

              var menu = new Ext.Action({
                id: data[i].id,
                text: data[i].label,
                handler: function(){
                  eval(this._action);
                },
                _action: data[i].action.replace('javascript:', '').replace(';', '')
              });

              tb.add(menu);
          }
        }

        if (Ext.getCmp('stepsMenu').disabled === true) {
          Ext.getCmp('stepsMenu').hide();
        }
      },
      failure: function ( result, request) {
        Ext.MessageBox.alert('Failed', result.responseText);
      }
    });
  }

  hideCaseNavigatorPanel = function(){
    Ext.getCmp('stepsMenu').pressed = false;
    caseMenuOpen = false;

    Ext.getCmp('navPanelWest').hide();
    Ext.getCmp('navPanelWest').ownerCt.doLayout();

  }

  function togglePreview(btn, pressed){
    if (typeof(treeToReviseTitle) == 'undefined') {
      var preview = Ext.getCmp('navPanelWest');
      preview[pressed ? 'show' : 'hide']();
      Ext.getCmp('navPanel').ownerCt.doLayout();
    }
    else {
      if (winTree.isVisible()) {
        winTree.hide();
      }
      else  {
        winTree.show();
      }
    }
  }

  if (typeof(treeToReviseTitle) == 'undefined') {
    var casesStepTree = new Ext.tree.TreePanel({
      id: 'casesStepTree',
      autoWidth: true,
      userArrows: true,
      animate: true,
      autoScroll: true,
      dataUrl: 'ajaxListener?action=steps',
      rootVisible: false,
      containerScroll: true,
      border: false,
      root: {
        nodeType: 'async'
      },

      listeners: {
        render: function() {
          this.getRootNode().expand();
        },
        click: function(tp) {
          if( tp.attributes.url ){
            document.getElementById('openCaseFrame').src = tp.attributes.url;
          }
        }
      }
    })

    var loader = casesStepTree.getLoader();
    loader.on("load", setNodeini);
  }
  else {
    var casesStepTree = {};
  }

  function setNodeini()
  {
    setNode(idfirstform);
  }

  var navPanelWest = {
    id: 'navPanelWest',
    region: 'west',
    xtype:'panel',
    width: 250,
    height: 500,
    maxSize: 400,
    split: true,
    collapsible: false,
    margins: '0 0 0 0',
    items:[casesStepTree]
  }

  var fnChangeStatus  =function(){
    alert('loaded');
  }

  var screenWidth = (PMExt.getBrowser().screen.width-140).toString() + 'px';

  var navPanelCenter = {
    id: 'navPanelCenter',
    region: 'center', layout:'fit',forceLayout: true,
    xtype:'panel',
    items:[{
        xtype:"tabpanel",
        id: 'caseTabPanel',
        deferredRender:false,
        defaults:{autoScroll: true},
        defaultType:"iframepanel",
        activeTab: 0,
        enableTabScroll: true,
        //defaults: Ext.apply({}, Ext.isGecko? {style:{position:'absolute'},hideMode:'visibility'}:false),

        items:[{
          id: 'casesTab',
          title: _('ID_CASE') +' ' + _APP_NUM,
          frameConfig:{name:'openCaseFrame', id:'openCaseFrame'},
          defaultSrc : uri,
          loadMask:{msg: _('ID_LOADING_GRID') },
          bodyStyle:{height: (PMExt.getBrowser().screen.height-60) + 'px', overflow:'auto'},
          width:screenWidth

          }
        ],
        listeners: {
          tabchange: function(panel){
            panel.ownerCt.doLayout();
          },
          render : function(panel){
            Ext.each([this.el, this[this.collapseEl]] ,
            function( elm ) {
              elm.setVisibilityMode(Ext.Element.VISIBILITY).originalDisplay ='visible';
            });
          }
        }
    }]
  };

  var navPanel = {
    id: 'navPanel',
    region: 'center',
    layout: 'border',
    items:[navPanelWest, navPanelCenter],
    tbar:[{
      id: 'stepsMenu',
      text: '&nbsp;&nbsp;'+_('ID_STEPS'),
      pressed: false,
      enableToggle:true,
      tooltip: {
        title: _('ID_CASES_STEPS'),
        text:_('ID_SHOW_HIDE_CASES_STEPS')
      },
      iconCls: 'ICON_STEPS',
      toggleHandler: togglePreview,
      disabled: true
    }, {
      id: 'informationMenu',
      text: _('ID_INFORMATION'),
      menu: []
    }, {
      id: 'actionMenu',
      text: _('ID_ACTIONS'),
      menu: []
    }, {
    id: 'caseNotes',
    pressed: false,
    enableToggle:true,
    text: '&nbsp;&nbsp;'+_('ID_CASES_NOTES'),
    iconCls: 'ICON_CASES_NOTES',
    tooltip: {
      title: _('ID_CASES_NOTES'),
      text:_('ID_SHOW_CASES_NOTES')
    },
    toggleHandler:function(btn, pressed){
      if(pressed){
        openCaseNotesWindow();
      }else{
        closeCaseNotesWindow();
      }
    }

  }]
  }

  var viewport = new Ext.Viewport({
      layout: 'border',
      items: [navPanel]
  });


  // Ext.getCmp('stepsMenu').hide();
  Ext.getCmp('caseNotes').hide();
  Ext.getCmp('informationMenu').hide();
  Ext.getCmp('actionMenu').hide();

  hideCaseNavigatorPanel();

  // Actions methods

  Actions.processMap = function()
  {
    Actions.tabFrame('processMap');
  }

  Actions.processInformation = function()
  {
    Ext.Ajax.request({
      url : 'ajaxListener' ,
      params : {action : 'getProcessInformation'},
      success: function ( result, request ) {
        var data = Ext.util.JSON.decode(result.responseText);

        fieldset = {
          xtype : 'fieldset',
          autoHeight  : true,
          defaults    : {
            width : 170,
            xtype:'label',
            labelStyle : 'padding: 0px;',
            style: 'font-weight: bold'
          },
          items       : [
            {fieldLabel: _('ID_TITLE'), text: data.PRO_TITLE},
            {fieldLabel: _('ID_DESCRIPTION'), text: data.PRO_DESCRIPTION},
            {fieldLabel: _('ID_CATEGORY'), text: data.PRO_CATEGORY_LABEL},
            {fieldLabel: _('ID_AUTHOR'), text: data.PRO_AUTHOR},
            {fieldLabel: _('ID_CREATE_DATE'), text: data.PRO_CREATE_DATE}
          ]
        }

        var frm = new Ext.FormPanel( {
          labelAlign : 'right',
          bodyStyle : 'padding:5px 5px 0',
          width : 400,
          autoScroll:true,
          items : [fieldset],
          buttons : [{
            text : 'OK',
            handler : function() {
              win.close();
            }
          }]
        });

        var win = new Ext.Window({
          title: '',
          width: 450,
          height: 280,
          layout:'fit',
          autoScroll:true,
          modal: true,
          maximizable: false,
          items: [frm]
        });
        win.show();
      },
      failure: function ( result, request) {
        Ext.MessageBox.alert( _('ID_FAILED') , result.responseText);
      }
    });
  }

  Actions.taskInformation = function()
  {
    Ext.Ajax.request({
      url : 'ajaxListener' ,
      params : {action : 'getTaskInformation'},
      success: function ( result, request ) {
        var data = Ext.util.JSON.decode(result.responseText);

        fieldset = {
          xtype : 'fieldset',
          autoHeight  : true,
          defaults    : {
            width : 170,
            xtype:'label',
            labelStyle : 'padding: 0px;',
            style: 'font-weight: bold'
          },
          items       : [
            {fieldLabel: _('ID_TITLE'), text: data.TAS_TITLE},
            {fieldLabel: _('ID_DESCRIPTION'), text: data.TAS_DESCRIPTION},
            {fieldLabel: _('ID_INIT_DATE'), text: data.INIT_DATE},
            {fieldLabel: _('ID_DUE_DATE'), text: data.DUE_DATE},
            {fieldLabel: _('ID_FINISH_DATE'), text: data.FINISH},
            {fieldLabel: _('ID_TASK_DURATION'), text: data.DURATION}
          ]
        }

        var frm = new Ext.FormPanel( {
          labelAlign : 'right',
          bodyStyle : 'padding:5px 5px 0',
          width : 400,
          autoScroll:true,
          items : [fieldset],
          buttons : [{
            text : 'OK',
            handler : function() {
              win.close();
            }
          }]
        });

        var win = new Ext.Window({
          title: '',
          width: 450,
          height: 280,
          layout:'fit',
          autoScroll:true,
          modal: true,
          maximizable: false,
          items: [frm]
        });
        win.show();
      },
      failure: function ( result, request) {
        Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
      }
    });
  }

  Actions.caseHistory = function()
  {
    Actions.tabFrame('caseHistory');
  }

  Actions.messageHistory = function()
  {
    Actions.tabFrame('messageHistory');
  }

  Actions.dynaformHistory = function()
  {
    Actions.tabFrame('dynaformHistory');
  }

  Actions.uploadedDocuments = function()
  {
    Actions.tabFrame('uploadedDocuments');
  }

  Actions.generatedDocuments = function()
  {
    Actions.tabFrame('generatedDocuments');
  }

  Actions.cancelCase = function()
  {
    var msgCancel =  new Ext.Window({
      width:500,
      plain: true,
      modal: true,
      title: _('ID_CONFIRM'),
      items: [
        new Ext.FormPanel({
          labelAlign: 'top',
          labelWidth: 75,
          border: false,
          frame: true,
          items: [
            {
              html: '<div align="center" style="font: 14px tahoma,arial,helvetica,sans-serif">' + _('ID_CONFIRM_CANCEL_CASE')+'? </div> <br/>'
            },
            {
              xtype: 'textarea',
              id: 'noteReason',
              fieldLabel: _('ID_CASE_CANCEL_REASON'),
              name: 'noteReason',
              width: 450,
              height: 50
            },
            {
              id: 'notifyReason',
              xtype:'checkbox',
              name: 'notifyReason',
              hideLabel: true,
              boxLabel: _('ID_NOTIFY_USERS_CASE')
            }
          ],

          buttonAlign: 'center',

          buttons: [{
            text: 'Ok',
            handler: function(){
              if (Ext.getCmp('noteReason').getValue() != '') {
                var noteReasonTxt = _('ID_CASE_CANCEL_LABEL_NOTE') + ' ' + Ext.getCmp('noteReason').getValue();
              } else {
                var noteReasonTxt = '';
              }
              var notifyReasonVal = Ext.getCmp('notifyReason').getValue() == true ? 1 : 0;

              Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
              Ext.Ajax.request({
                url : 'ajaxListener' ,
                params : {action : 'cancelCase', NOTE_REASON: noteReasonTxt, NOTIFY_PAUSE: notifyReasonVal},
                success: function ( result, request ) {
                  try {
                    parent.notify('', 'The case ' + parent._CASE_TITLE + ' was cancelled!');
                  }
                  catch (e) {
                  }
                  location.href = 'casesListExtJs';
                },
                failure: function ( result, request) {
                  Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                }
              });
            }
          },{
            text: _('ID_CANCEL'),
            handler: function(){
                msgCancel.close();
            }
          }]
        })
      ]
    });
    msgCancel.show(this);
  }

  Actions.getUsersToReassign = function()
  {
    var store = new Ext.data.Store( {
      autoLoad: true,
      proxy : new Ext.data.HttpProxy({
        url: 'ajaxListener?action=getUsersToReassign'
      }),
      reader : new Ext.data.JsonReader( {
        root: 'data',
        fields : [
          {name : 'USR_UID'},
          {name : 'USR_USERNAME'},
          {name : 'USR_FIRSTNAME'},
          {name : 'USR_LASTNAME'}
        ]
      })
    });

    var grid = new Ext.grid.GridPanel( {
      id: 'reassignGrid',
      height:300,
      width:'300',
      title : '',
      stateful : true,
      stateId : 'grid',
      enableColumnResize: true,
      enableHdMenu: true,
      frame:false,
      cls : 'grid_with_checkbox',
      columnLines: true,

      viewConfig: {
        forceFit:true
      },

      cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 200,
            sortable: true
        },
        columns: [
          {id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
          {header: _('ID_USER_NAME'), dataIndex: 'USR_USERNAME',  width: 300},
          {header: _('ID_FIRSTNAME'), dataIndex: 'USR_FIRSTNAME', width: 300},
          {header: _('ID_LASTNAME'),  dataIndex: 'USR_LASTNAME',  width: 300}
        ]
      }),

      store: store,

      tbar:[
        {
          text:_('ID_REASSIGN'),
          iconCls: 'ICON_CASES_TO_REASSIGN',
          handler: Actions.reassignCase
        }
      ],
      listeners: {
        //rowdblclick: openCase,
        render: function(){
          this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING')});
          this.ownerCt.doLayout();
        }
      }
    });

    var win = new Ext.Window({
      title: '',
      width: 450,
      height: 280,
      layout:'fit',
      autoScroll:true,
      modal: true,
      maximizable: false,
      items: [grid]
    });
    win.show();
  }

  Actions.reassignCase = function()
  {
    var rowSelected = Ext.getCmp('reassignGrid').getSelectionModel().getSelected();
    if( rowSelected ) {
      PMExt.confirm(_('ID_CONFIRM'), _('ID_REASSIGN_CONFIRM'), function(){
        Ext.Ajax.request({
          url : 'ajaxListener' ,
          params : {action : 'reassignCase', USR_UID: rowSelected.data.USR_UID},
          success: function ( result, request ) {
            var data = Ext.util.JSON.decode(result.responseText);
            if( data.status == 0 ) {
              try {
                parent.notify('', data.msg);
              }
              catch (e) {
              }
              location.href = 'casesListExtJs';
            } else {
              alert(data.msg);
            }
          },
          failure: function ( result, request) {
            Ext.MessageBox.alert( _('ID_FAILED') , result.responseText);
          }
        });
      });
    }
  }

  Actions.setUnpauseCaseDate = function()
  {
    curDate = _ENV_CURRENT_DATE_NO_FORMAT.split('-');
    filterDate = curDate[0]+'-'+curDate[1]+'-';
    nDay = '' + (parseInt(curDate[2])+1);
    nDay = nDay.length == 1 ? '0' + nDay : nDay;
    filterDate += nDay;
    filterTime = ('0' + curDate[3]).slice(-2) + ':' + ('0' + curDate[4]).slice(-2) + ' ' + curDate[5];

    var fieldset = {
      xtype : 'fieldset',
      labelWidth: 150,
      //autoHeight  : true,
      defaults    : {
        width : 170,
        xtype:'label',
        labelStyle : 'padding: 0px;',
        style: 'font-weight: bold'
      },
      items : [
        {fieldLabel: 'Case', text: parent._CASE_TITLE},
        {fieldLabel: 'Pause Date', text: _ENV_CURRENT_DATE},
        new Ext.form.DateField({
          id:   'unpauseDate',
          format: 'Y-m-d',
          fieldLabel: _('ID_UNPAUSE_DATE'),
          name: 'unpauseDate',
          allowBlank: false,
          value: filterDate,
          minValue: filterDate
        }),
        new Ext.form.TimeField({
          id: 'unpauseTime',
          fieldLabel: _('ID_UNPAUSE_TIME'),
          name: 'unpauseTime',
          value: filterTime,
          minValue: filterTime,
          format: 'h:i A'
        }),
        {
          xtype: 'textarea',
          id: 'noteReason',
          fieldLabel: _('ID_CASE_PAUSE_REASON'),
          name: 'noteReason',
          width: 170,
          height: 50
        },
        {
          id: 'notifyReason',
          xtype:'checkbox',
          name: 'notifyReason',
          fieldLabel: _('ID_NOTIFY_USERS_CASE')
        }
      ],
      buttons : [
        {
          id: 'submitPauseCase',
          text : _('ID_PAUSE_CASE'),
          handler : Actions.pauseCase,
          disabled:false
        },{
          text : _('ID_CANCEL'),
          handler : function() {
            win.close();
          }
        }
      ]
    }

    var frm = new Ext.FormPanel( {
      id: 'unpauseFrm',
      labelAlign : 'right',
      //bodyStyle : 'padding:5px 5px 0',
      width : 260,
      items : [fieldset]
    });


    var win = new Ext.Window({
      title: _('ID_PAUSE_CASE'),
      width: 380,
      height: 250,
      layout:'fit',
      autoScroll:true,
      modal: true,
      maximizable: false,
      items: [frm]
    });
    win.show();
  }

  Actions.pauseCase = function()
  {
    if (Ext.getCmp('noteReason').getValue() != '') {
      var noteReasonTxt = _('ID_CASE_PAUSE_LABEL_NOTE') + ' ' + Ext.getCmp('noteReason').getValue();
    } else {
      var noteReasonTxt = '';
    }
    var notifyReasonVal = Ext.getCmp('notifyReason').getValue() == true ? 1 : 0;
    var paramsNote = '&NOTE_REASON=' + noteReasonTxt + '&NOTIFY_PAUSE=' + notifyReasonVal;

    var unpauseDate = Ext.getCmp('unpauseDate').getValue();
    if( unpauseDate == '') {
      //Ext.getCmp('submitPauseCase').setDisabled(true);
      return;
    } else
      //Ext.getCmp('submitPauseCase').enable();

    unpauseDate = unpauseDate.format('Y-m-d');

    Ext.getCmp('unpauseFrm').getForm().submit({
        url:'ajaxListener',
        method  : 'post',
        params  : {
            action: 'pauseCase',
            unpauseDate:unpauseDate,
            NOTE_REASON: noteReasonTxt,
            NOTIFY_PAUSE: notifyReasonVal
            },
        waitMsg: _('ID_PAUSING_CASE')+parent._CASE_TITLE+'...',
        timeout : 36000,
        success : function(res, req) {
            if(req.result.success) {
                try {
                  parent.notify( _('ID_PAUSE_CASE') , req.result.msg);
                }
                catch (e) {
                }
                location.href = 'casesListExtJs';
            } else {
                PMExt.error( _('ID_ERROR'), req.result.msg);
            }
        }
    });
  }

  Actions.unpauseCase = function()
  {
    PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_UNPAUSE_CASE'), function(){
      var loadMask = new Ext.LoadMask(document.body, {msg: _('ID_UNPAUSING_CASE') });
      loadMask.show();

      Ext.Ajax.request({
        url : 'ajaxListener' ,
        params : {action : 'unpauseCase'},
        success: function ( result, request ) {
          loadMask.hide();
          var data = Ext.util.JSON.decode(result.responseText);
          if( data.success ) {
            try {
              parent.PMExt.notify(_('ID_UNPAUSE_ACTION'), data.msg);
            }
            catch (e) {
            }
            location.href = 'casesListExtJs';
          } else {
            PMExt.error(_('ID_ERROR'), data.msg);
          }
        },
        failure: function ( result, request) {
          Ext.MessageBox.alert( _('ID_FAILED') , result.responseText);
        }
      });
    });
  }

  Actions.deleteCase = function()
  {
    PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_CASE'), function(){
      var loadMask = new Ext.LoadMask(document.body, {msg: _('ID_DELETING_CASE') });
      loadMask.show();
      Ext.Ajax.request({
        url : '../adhocUserProxy/deleteCase',
        success: function ( result, request ) {
          loadMask.hide();
          var data = Ext.util.JSON.decode(result.responseText);
          if( data.success ) {
            try {
              parent.PMExt.notify(_('ID_DELETE_ACTION'), data.msg);
            }
            catch (e) {
            }
            location.href = 'casesListExtJs';
          } else {
            PMExt.error(_('ID_ERROR'), data.msg);
          }
        },
        failure: function ( result, request) {
          Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
        }
      });
    });
  }

  Actions.reactivateCase = function()
  {
    PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_REACTIVATE_CASE'), function(){
      var loadMask = new Ext.LoadMask(document.body, {msg: _('ID_REACTIVATING_CASE') });
      loadMask.show();
      Ext.Ajax.request({
        url : 'ajaxListener' ,
        params : {action : 'reactivateCase'},
        success: function ( result, request ) {
          loadMask.hide();
          var data = Ext.util.JSON.decode(result.responseText);
          if( data.success ) {
            try {
              parent.PMExt.notify(_('ID_REACTIVATE_ACTION'), data.msg);
            }
            catch (e) {
            }
            location.href = 'casesListExtJs';
          } else {
            PMExt.error(_('ID_ERROR'), data.msg);
          }
        },
        failure: function ( result, request) {
          Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
        }
      });
    });
  }

  //
  Actions.tabFrame = function(name)
  {
    tabId = name + 'MenuOption';
    var uri = 'ajaxListener?action=' + name;
    var TabPanel = Ext.getCmp('caseTabPanel');
    var tab = TabPanel.getItem(tabId);
    //!dataInput
    var tabName =  ActionTabFrameGlobal.tabName;
    var tabTitle = ActionTabFrameGlobal.tabTitle;

    //!dataSystem
    var loadMaskMsg = _('ID_LOADING_GRID');

    if (name == "dynaformViewFromHistory") {
      var responseObject = Ext.util.JSON.decode(historyGridListChangeLogGlobal.viewDynaformName);
      var dynTitle = responseObject.dynTitle;
      var md5Hash = responseObject.md5Hash;
      name = "dynaformViewFromHistory"+md5Hash;
    }

    var caseHistoryIframeRest = name!="caseHistory"?0:-20;
    tabId = name + 'MenuOption';
    var uri = 'ajaxListener?action=' + name;

    if (name.indexOf("changeLogTab") != -1) {
      var uri = 'ajaxListener?action=' + 'changeLogTab';
      //!historyGridListChangeLogGlobal
      historyGridListChangeLogGlobal.idHistory = historyGridListChangeLogGlobal.idHistory;
      historyGridListChangeLogGlobal.tasTitle = historyGridListChangeLogGlobal.tasTitle;
      //dataSystem
      idHistory = historyGridListChangeLogGlobal.idHistory;
      var tasTitle = historyGridListChangeLogGlobal.tasTitle;
      menuSelectedTitle[name] = tasTitle;
      Actions[name];
      uri += "&idHistory="+idHistory;
    }

    if (name.indexOf("dynaformViewFromHistory") != -1) {
      var uri = 'ajaxListener?action=' + 'dynaformViewFromHistory';
      uri += '&DYN_UID='+historyGridListChangeLogGlobal.viewIdDin+'&HISTORY_ID='+historyGridListChangeLogGlobal.viewIdHistory;
      menuSelectedTitle[name] = 'View('+dynTitle+' '+historyGridListChangeLogGlobal.dynDate+')';
    }

    if (name.indexOf("previewMessage") != -1) {
      var uri = 'caseMessageHistory_Ajax?actionAjax=' + 'showHistoryMessage';
      var tabNameArray = tabName.split('_');
      var APP_UID = tabNameArray[1];
      var APP_MSG_UID = tabNameArray[2];
      uri += '&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID;
      menuSelectedTitle[tabName] = tabTitle;
    }

    if (name.indexOf("previewMessage") != -1) {
      var uri = 'caseMessageHistory_Ajax?actionAjax=' + 'showHistoryMessage';
      var tabNameArray = tabName.split('_');
      var APP_UID = tabNameArray[1];
      var APP_MSG_UID = tabNameArray[2];
      uri += '&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID;
      menuSelectedTitle[tabName] = tabTitle;
    }

    if (name.indexOf("sendMailMessage") != -1) {
      var uri = 'caseMessageHistory_Ajax?actionAjax=' + 'sendMailMessage_JXP';
      var tabNameArray = tabName.split('_');
      var APP_UID = tabNameArray[1];
      var APP_MSG_UID = tabNameArray[2];
      uri += '&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID;
      menuSelectedTitle[tabName] = tabTitle;
    }

    if (name=="dynaformHistory") {
      var uri = 'casesHistoryDynaformPage_Ajax?actionAjax=historyDynaformPage';
    }

    if (name.indexOf("historyDynaformGridHistory") != -1) {
      var historyDynaformGridHistoryGlobal = Ext.util.JSON.decode(ActionTabFrameGlobal.tabData);
      var tabTitle = ActionTabFrameGlobal.tabTitle;
      var PRO_UID = historyDynaformGridHistoryGlobal.PRO_UID;
      var APP_UID = historyDynaformGridHistoryGlobal.APP_UID;
      var TAS_UID = historyDynaformGridHistoryGlobal.TAS_UID;
      var DYN_UID = historyDynaformGridHistoryGlobal.DYN_UID;
      var DYN_TITLE = historyDynaformGridHistoryGlobal.DYN_TITLE;
      var uri = 'casesHistoryDynaformPage_Ajax?actionAjax=showDynaformListHistory';
      uri += '&PRO_UID='+PRO_UID+'&APP_UID='+APP_UID+'&TAS_UID='+TAS_UID+'&DYN_UID='+DYN_UID;
      menuSelectedTitle[name] = tabTitle;
    }

    if (name.indexOf("dynaformChangeLogViewHistory") != -1) {
      var showDynaformHistoryGlobal = Ext.util.JSON.decode(ActionTabFrameGlobal.tabData);
      var tabTitle = ActionTabFrameGlobal.tabTitle;
      var dynUID = showDynaformHistoryGlobal.dynUID;
      var tablename = showDynaformHistoryGlobal.tablename;
      var dynDate = showDynaformHistoryGlobal.dynDate;
      var dynTitle = showDynaformHistoryGlobal.dynTitle;
      var uri = 'casesHistoryDynaformPage_Ajax?actionAjax=dynaformChangeLogViewHistory';
      uri += '&DYN_UID='+dynUID+'&HISTORY_ID='+tablename;
      menuSelectedTitle[name] = tabTitle;
    }

    if (name.indexOf("historyDynaformGridPreview") != -1) {
      var historyDynaformGridPreviewGlobal = Ext.util.JSON.decode(ActionTabFrameGlobal.tabData);
      var tabTitle = ActionTabFrameGlobal.tabTitle;
      var DYN_UID = historyDynaformGridPreviewGlobal.DYN_UID;
      var uri = 'casesHistoryDynaformPage_Ajax?actionAjax=historyDynaformGridPreview';
      uri += '&DYN_UID='+DYN_UID;
      menuSelectedTitle[name] = tabTitle;
    }

    if (name == "uploadDocumentGridDownload") {
      var uploadDocumentGridDownloadGlobal = Ext.util.JSON.decode(ActionTabFrameGlobal.tabData);
      var APP_DOC_UID = uploadDocumentGridDownloadGlobal.APP_DOC_UID;
      var DOWNLOAD_LINK = uploadDocumentGridDownloadGlobal.DOWNLOAD_LINK;
      var TITLE = uploadDocumentGridDownloadGlobal.TITLE;
      var uri = DOWNLOAD_LINK;
      menuSelectedTitle[name] = ActionTabFrameGlobal.tabTitle;
    }

    if (name == "generatedDocuments") {
      var uri = 'casesGenerateDocumentPage_Ajax.php?actionAjax=casesGenerateDocumentPage';
    }

    if( tab ) {
      TabPanel.setActiveTab(tabId);
    }
    else {
      TabPanel.add({
        id: tabId,
        title: menuSelectedTitle[name],
        frameConfig:{name: name + 'Frame', id: name + 'Frame'},
        defaultSrc : uri,
        loadMask:{msg:_('ID_LOADING_GRID')},
        autoWidth: true,
        closable:true,
        autoScroll: true,
        bodyStyle:{height: (PMExt.getBrowser().screen.height-60) + 'px', overflow:'auto'}
      }).show();

      TabPanel.doLayout();
    }
  }

});

  Actions.adhocAssignmentUsers = function()
  {
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
    Ext.QuickTips.init();
    store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url   : '../adhocUserProxy/adhocAssignUsersk'
     }),
    reader : new Ext.data.JsonReader( {
     root: 'data',
     fields : [
      {name : 'USR_UID'},
      {name : 'USR_FIRSTNAME'},
      {name : 'USR_LASTNAME'}
      ]
    })
    });

    cmk = new Ext.grid.ColumnModel({
     defaults: {
      width: 40,
      sortable: true
      },
     columns: [
      { id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
      { header : _('ID_FIRST_NAME'), dataIndex : 'USR_FIRSTNAME', sortable : true, width: 130, align:'center'},
      { header : _('ID_LAST_NAME'), dataIndex : 'USR_LASTNAME', sortable : true,width: 130, align:'center' }
     ]
    });

    pbark = new Ext.PagingToolbar({
     pageSize: 8,
     store: store,
     displayInfo: true,
     displayMsg: _('ID_GRID_PAGE_DISPLAYING_USERS_MESSAGE'),
     emptyMsg: "",
     items:[]
    });

    var adHocUserGrid = new Ext.grid.GridPanel( {
      region: 'center',
      layout: 'fit',
      id: 'adHocUserGrid',
      height:210,
      //autoWidth : true,
      width:'',
      title : '',
      stateful : true,
      stateId : 'grid',
      enableColumnResize: true,
      enableHdMenu: true,
      frame:false,
      columnLines: true,
      viewConfig: {
        forceFit:true
      },
      cm: cmk,
      store: store,
      tbar:[
        {
          text:_('ID_ASSIGN'),
          iconCls: 'silk-add',
          icon: '/images/cases-selfservice.png',
          handler: assignAdHocUser
        }
      ],
      bbar: '',
      listeners:{
        rowdblclick: assignAdHocUser
      }
    });

    var w = new Ext.Window({
      title: _('ID_ADHOC_ASSIGNMENT'),
      width: 500,
      height: 240,
      resizable: false,
      items: [ adHocUserGrid ],
      id: 'w'
    });
    adHocUserGrid.store.load();
    w.show();

  function assignAdHocUser()
  {
    rowSelected = adHocUserGrid.getSelectionModel().getSelected();
    PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_ADHOCUSER_CASE'), function(){
      var loadMask = new Ext.LoadMask(document.body, {msg:_('ID_ASSIGNMENT_CASE')});
      loadMask.show();
      Ext.Ajax.request({
        url : '../adhocUserProxy/reassignCase' ,
        method: 'POST',
        params : {USR_UID: rowSelected.data.USR_UID, THETYPE: 'ADHOC'},
        success: function ( result, request ) {
          loadMask.hide();
          var data = Ext.util.JSON.decode(result.responseText);
          if( data.success ) {
            CloseWindow();
            location.href = 'casesListExtJs';
          }
          else {
            PMExt.error(_('ID_ERROR'), data.msg);
          }
        },
        failure: function ( result, request) {
          Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
        }
      });
     });
    }
  }

  CloseWindow = function(){
    Ext.getCmp('w').hide();
  };

  setNode =  function(uid){
    var stepsTree = Ext.getCmp('casesStepTree');

    if (!stepsTree) {
      return false;
    }

    var node = stepsTree.getNodeById(uid);

    if (!node) {
      return false;
    }

    node.select();
  }
