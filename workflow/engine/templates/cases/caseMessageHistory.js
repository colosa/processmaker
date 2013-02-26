/*
 * @author: Douglas Medrano
 * May 03, 2011
 */

    function onResizeIframe(idIframe){
      window.parent.tabIframeWidthFix2(idIframe);
    }

    previewMessage = function() {
      var rowSelected =  Ext.getCmp('processesGrid').getSelectionModel().getSelected();
      if (rowSelected) {
        windowMessage = new Ext.Window({
          title: '',
          width: 600,
          height: 420,
          border: false,
          layout : 'fit',
          items:
          [
            {
              xtype: 'form',
              frame: true,
              border: false,
              defaults: {
                width: 150
              },
              items: [
                {
                  xtype: 'textfield',
                  fieldLabel: _("ID_FROM"),
                  id:'From',
                  anchor: '100%',
                  arrowAlign:'center',
                  readOnly: true,
                  name: 'From'
                },
                {
                  xtype: 'textfield',
                  fieldLabel: _("ID_TO"),
                  id: 'To',
                  anchor: '100%',
                  arrowAlign:'center',
                  readOnly: true,
                  name: 'To'
                },
                {
                  xtype: 'textfield',
                  fieldLabel: _('ID_SUBJECT'),
                  id: 'Subjet',
                  anchor: '100%',
                  arrowAlign:'center',
                  readOnly: true,
                  name: 'Subjet'
                },
                {
                  xtype: 'textfield',
                  fieldLabel: _("DATE_LABEL"),
                  id: 'date',
                  arrowAlign:'center',
                  readOnly: true,
                  name: 'Status'
                },
                {
                  name : 'body',
                  id:'body',
                  hideLabel:true,
                  xtype: 'htmleditor',
                  autoScroll: true,
                  readOnly: true,
                  x: 1,
                  y: 1,
                  enableAlignments:false,
                  enableColors:false,
                  enableFont:false,
                  enableFontSize:false,
                  enableFormat:false,
                  enableLinks:false,
                  enableLists:false,
                  enableSourceEdit:false,
                  anchor: '100%',
                  height: 260
                }
              ]
            }
          ]
        });

        //load fields from rowSelect
        Ext.getCmp('From').setValue(rowSelected.data.APP_MSG_FROM);
        Ext.getCmp('To').setValue(rowSelected.data.APP_MSG_TO);
        Ext.getCmp('Subjet').setValue(rowSelected.data.APP_MSG_SUBJECT);
        Ext.getCmp('date').setValue(rowSelected.data.APP_MSG_DATE);
        Ext.getCmp('body').setValue(rowSelected.data.APP_MSG_BODY);

        //show windows message
        windowMessage.show(windowMessage);
      }
      else {
        Ext.Msg.show({
          title:'',
          msg: _("ID_NO_SELECTION_WARNING"),
          buttons: Ext.Msg.INFO,
          fn: function(){},
          animEl: 'elId',
          icon: Ext.MessageBox.INFO,
          buttons: Ext.MessageBox.OK
        });
      }
    }

    function ajaxPostRequest(url, callback_function, id){
      var d = new Date();
      var time = d.getTime();
      url= url + '&nocachetime='+time;
      var return_xml=false;
      var http_request = false;

        if (window.XMLHttpRequest){ // Mozilla, Safari,...
          http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType){
              http_request.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject){// IE
          try{
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
          }
          catch (e){
              try{
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
              }
              catch (e){

              }
          }
        }
        if (!http_request){
          alert('This browser is not supported.');
          return false;
        }

        http_request.onreadystatechange = function(){
            if (http_request.readyState == 4){
                if (http_request.status == 200){
                    if (return_xml){
                      eval(callback_function + '(http_request.responseXML)');
                    }
                    else{
                      eval(callback_function + '(http_request.responseText, \''+id+'\')');
                    }
                }
                else{
                  alert('Error found on request:(Code: ' + http_request.status + ')');
                }
            }
        }
      http_request.open('GET', url, true);
      http_request.send(null);
    }

  var processesGrid;
  var store;
var ActionTabFrameGlobal = '';
    function toggleTable(tablename){
      table= document.getElementById(tablename);
        if(table.style.display == ''){
          table.style.display = 'none';
        }else{
          table.style.display = '';
        }
    }

    new Ext.KeyMap(
      document,
      {
        key: Ext.EventObject.F5,
        fn: function(keycode, e){
            if (! e.ctrlKey){
              if (Ext.isIE)
                  e.browserEvent.keyCode = 8;
              e.stopEvent();
              document.location = document.location;
            }
            else{
              Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
            }

        }
      }
    );

    Ext.onReady(function(){
      Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
      Ext.QuickTips.init();

      messageHistoryGridList();

    });

    //!historyGridList|
    //!!historyGridList|changeLog
    function historyGridListChangeLogPanelBody_RSP(resp,id){
      var historyGridListChangeLogPanel_id_ = Ext.getCmp('historyGridListChangeLogPanel_id');
      historyGridListChangeLogPanel_id_.show();
      var historyGridListChangeLogPanelBody_= document.getElementById('historyGridListChangeLogPanelBody_JXP');
      historyGridListChangeLogPanelBody_.innerHTML= resp;
    }

    function historyGridListChangeLogPanelBody_JXP(){
      //!historyGridListChangeLogGlobal
      historyGridListChangeLogGlobal.idHistory = historyGridListChangeLogGlobal.idHistory;

      //dataSystem
      var idHistory = historyGridListChangeLogGlobal.idHistory;

      var url = "caseHistory_Ajax.php?actionAjax=historyGridListChangeLogPanelBody_JXP&idHistory="+idHistory;
      ajaxPostRequest(url,'historyGridListChangeLogPanelBody_RSP');
    }

    function historyGridListChangeLogPanel(){
        var w = new Ext.Window({
          //draggable: Ext.util.Draggable,
          title: _('Change log'),
          width: 920,
          id:'historyGridListChangeLogPanel_id',
          autoHeight: false,
          height: 400,
          modal: true,
          //autoScroll: false,
          maximizable: false,
          resizable: false,
          items:
          [
            {
              xtype: 'box',
              autoEl: { tag: 'div',html: '<div id="historyGridListChangeLogPanelBody_JXP"  ></div>'}
            },
            {
              name:'ajaxAction',
              xtype:'hidden',
              value:'uploadFileNewProcess'
            }
          ]
        });
      historyGridListChangeLogPanelBody_JXP();
    }

  var historyGridListChangeLogGlobal = {};
  historyGridListChangeLogGlobal.idHistory ='';

    function historyGridListChangeLog(){

      historyGridListChangeLogGlobal.idHistory = historyGridListChangeLogGlobal.idHistory;
      var rowSelected = processesGrid.getSelectionModel().getSelected();
        if( rowSelected ){
          var idHistory = rowSelected.data.ID_HISTORY;
          historyGridListChangeLogGlobal.idHistory = idHistory;
          historyGridListChangeLogPanel();
        }
        else{
            Ext.Msg.show({
              title:'',
              msg: TRANSLATIONS.ID_NO_SELECTION_WARNING,
              buttons: Ext.Msg.INFO,
              fn: function(){},
              animEl: 'elId',
              icon: Ext.MessageBox.INFO,
              buttons: Ext.MessageBox.OK
            });
        }

    }
  //!!historyGridList|changeLog

  function caseMessageHistory_RSP(response,id){
    messageHistoryGridListMask.hide();
    if(response==""){

      Ext.Msg.show({
        title:'',
        msg: _('ID_MAIL_SENT_SUCCESSFULLY'),
        buttons: Ext.Msg.INFO,
        fn: function(){},
        animEl: 'elId',
        icon: Ext.MessageBox.INFO,
        buttons: Ext.MessageBox.OK
      });







      Ext.destroy(Ext.getCmp('processesGrid'));

      messageHistoryGridList();
    }
    else{
      alert(response);
    }
  }


  function messageHistoryGridList(){
      store = new Ext.data.GroupingStore({
        proxy : new Ext.data.HttpProxy
        (
          {
            url: 'caseMessageHistory_Ajax.php?actionAjax=messageHistoryGridList_JXP'
          }
        ),

        reader : new Ext.data.JsonReader
        (
          {
            totalProperty: 'totalCount',
            root: 'data',
            fields :
            [
              {name : 'ID_MESSAGE'},
              {name : 'APP_MSG_TYPE'},
              {name : 'APP_MSG_DATE'},
              {name : 'APP_MSG_SUBJECT'},
              {name : 'APP_MSG_FROM'},
              {name : 'APP_MSG_TO'},
              {name : 'APP_MSG_STATUS'},
	      {name : 'APP_MSG_BODY'}

            ]
          }
        )
      });

      var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
          '<p><b>'+TRANSLATIONS.ID_PRO_DESCRIPTION+':</b> {PRO_DESCRIPTION}</p><br>'
        )
      });


      startDateRender = function(v){
        var dateString = "-";
          if(v!="-"){
            dateString = _DF(v,"m/d/Y H:i:s");
          }
        return dateString;
      }
      escapeHtml = function(v){
        var pre = document.createElement('pre');
        var text = document.createTextNode( v );
        pre.appendChild(text);
        return pre.innerHTML;
      }

      actionRenderingTranslation = function(v){
        var actionTranslate = "";
        if(v=="PAUSE"){
          actionTranslate = _("ID_PAUSED");
        }
        else if(v=="CANCEL"){
          actionTranslate = _("ID_CANCELLED");
        }
        else if(v=="IN_PROGRESS"){
          actionTranslate = _("ID_IN_PROGRESS");
        }
        else if(v=="REASSIGN"){
          actionTranslate = _("ID_REASSIGNED");
        }
        else if(v==""||v==null){
          actionTranslate = _("ID_DERIVATED");
        }
        return actionTranslate;
      };


      var processesGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'processesGrid',
        height:500,
        //autoWidth : true,
        width:'',
        title : '',
        stateful : true,
        stateId : 'grid',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        //plugins: expander,
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
            //expander,
            {id:'ID_MESSAGE', dataIndex: 'ID_MESSAGE', hidden:true, hideable:false},
            {header: _("ID_TYPE"), dataIndex: 'APP_MSG_TYPE', width: 70},
            {header: _("ID_DATE_LABEL"), dataIndex: 'APP_MSG_DATE', width: 60, renderer: startDateRender},
            {header: _("ID_SUBJECT"), dataIndex: 'APP_MSG_SUBJECT', width: 60},
            {header: _("ID_FROM"), dataIndex: 'APP_MSG_FROM', width: 60, renderer: escapeHtml},
            {header: _("ID_TO"), dataIndex: 'APP_MSG_TO', width: 60, renderer: escapeHtml},
            {header: _("ID_STATUS"), dataIndex: 'APP_MSG_STATUS', width: 50},
            {header: _("ID_APP_MSG_BODY"), dataIndex: 'APP_MSG_BODY', width: 50,hidden:true}          ]
        }),
        store: store,
        tbar:[
          {
            text:_("ID_RESEND"),
            id:'sendMailMessageFormRadioId',
            iconCls: 'button_menu_ext',
            icon: '/images/mail-send16x16.png',
            handler: function(){

              var rowSelected = processesGrid.getSelectionModel().getSelected();

                if( rowSelected ){
                  //!dataGrid


                  // Show a dialog using config options:
                  Ext.Msg.show({
                    title:'',
                    msg: _('ID_ARE_YOU_SURE_RESEND')+"?",
                    buttons: Ext.Msg.OKCANCEL,
                    icon: Ext.MessageBox.QUESTION,
                    fn: function(btn, text){
                      if(btn=='ok'){
                        //!dataGrid
                        var idMessage = rowSelected.data.ID_MESSAGE;
                        var subjectMessage = rowSelected.data.APP_MSG_SUBJECT;
                        var dateMessage = rowSelected.data.APP_MSG_DATE;

                        var tabName = 'sendMailMessage_'+idMessage;
                        var tabTitle = 'Resend('+subjectMessage+' '+dateMessage+')';

                        ActionTabFrameGlobal.tabName = tabName;
                        ActionTabFrameGlobal.tabTitle = tabTitle;

                        //window.parent.Actions.tabFrame(tabName);
                        var tabNameArray = tabName.split('_');
                        var APP_UID = tabNameArray[1];
                        var APP_MSG_UID = tabNameArray[2];


                        messageHistoryGridListMask = new Ext.LoadMask(Ext.getBody(), {msg:_('ID_LOADING')});
                        messageHistoryGridListMask.show();



                        var url = "caseMessageHistory_Ajax.php?actionAjax=sendMailMessage_JXP&APP_UID="+APP_UID+"&APP_MSG_UID="+APP_MSG_UID;
                        ajaxPostRequest(url,'caseMessageHistory_RSP');


                      }

                    },
                    animEl: 'elId'
                  });

                }
                else{
                  Ext.Msg.show({
                    title:'',
                    msg: TRANSLATIONS.ID_NO_SELECTION_WARNING,
                    buttons: Ext.Msg.INFO,
                    fn: function(){},
                    animEl: 'elId',
                    icon: Ext.MessageBox.INFO,
                    buttons: Ext.MessageBox.OK

                  });
                }



            },
            disabled:false
          },
          {
            xtype: 'tbseparator'
          },
          {
            text:_("ID_PREVIEW"),
            id:'viewMailMessageFormRadioId',
            iconCls: 'button_menu_ext',
            icon: '/images/documents/_filefind.png',
            handler: function(){
	      var rowSelected = processesGrid.getSelectionModel().getSelected();

	      if (rowSelected) {
                previewMessage();
	      }
	      else {
		Ext.Msg.show({
		  title:'',
		  msg: _("ID_NO_SELECTION_WARNING"),
		  buttons: Ext.Msg.INFO,
		  fn: function(){},
		  animEl: 'elId',
		  icon: Ext.MessageBox.INFO,
		  buttons: Ext.MessageBox.OK
		});
	      }
            },
            disabled:false
          },
          {
            xtype: 'tbfill'
          }
        ],
        bbar: new Ext.PagingToolbar({
          pageSize: 10,
          store: store,
          displayInfo: true,
          displayMsg: _('ID_DISPLAY_PROCESSES'),
          emptyMsg: "",
          items:[]
        }),
        listeners: {
          rowdblclick: previewMessage,
          render: function(){
            this.loadMask = new Ext.LoadMask(this.body, {msg:'Loading...'});
            processesGrid.getSelectionModel().on('rowselect', function(){
              var rowSelected = processesGrid.getSelectionModel().getSelected();

            });
          }
        }
      });

    processesGrid.store.load({params: {"function":"languagesList"}});

      processesGrid.store.on(
        'load',
        function()
        {
        //window.parent.resize_iframe();
        },
        this,
        {
          single: true
        }
      );

    processesGrid.on('contextmenu', function (evt) {
      evt.preventDefault();
    }, this);
    function emptyReturn(){
    }

    var viewport = new Ext.Viewport({
      layout: 'border',
      autoScroll: true,
      items: [
        processesGrid
      ]
    });
  }
  //!historyGridList|



