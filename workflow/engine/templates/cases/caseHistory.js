/*
 * @author: Douglas Medrano
 * May 03, 2011
 */

    function onResizeIframe(idIframe){
      window.parent.tabIframeWidthFix2(idIframe);
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
          alert( _('ID_BROWSER_NOT_SUPPORTED') );
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
              Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
            }

        }
      }
    );

    Ext.onReady(function(){
      Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
      Ext.QuickTips.init();

      historyGridList();

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

  function historyGridList(){
      store = new Ext.data.GroupingStore({
        proxy : new Ext.data.HttpProxy
        (
          {
            url: 'caseHistory_Ajax.php?actionAjax=historyGridList_JXP'
          }
        ),

        reader : new Ext.data.JsonReader
        (
          {
            totalProperty: 'totalCount',
            root: 'data',
            fields :
            [
              {name : 'ID_HISTORY'},
              {name : 'USR_NAME'},
              {name : 'TAS_TITLE'},
              {name : 'PRO_STATUS'},
              {name : 'PRO_STATUS_LABEL'},
              {name : 'DEL_INIT_DATE'},
              {name : 'PRO_DEBUG'},
              {name : 'PRO_DEBUG_LABEL'},
              {name : 'DEL_DELEGATE_DATE'},
              {name : 'CASES_COUNT', type:'float'},
              {name : 'APP_TYPE'},
              {name : 'DEL_FINISH_DATE'},
              {name : 'APP_ENABLE_ACTION_DATE'},
              {name : 'APP_DISABLE_ACTION_DATE'}
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
          if(v != "-" && v != null){
            dateString = _DF(v,"m/d/Y H:i:s");
          }
        return dateString;
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


      processesGrid = new Ext.grid.GridPanel({
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
            {id:'ID_HISTORY', dataIndex: 'ID_HISTORY', hidden:true, hideable:false},
            {header: "", dataIndex: 'PRO_STATUS', width: 50, hidden:true, hideable:false},
            {header: _("ID_CASESLIST_APP_TAS_TITLE"), dataIndex: 'TAS_TITLE', width: 100},
            {header: _("ID_DELEGATE_USER"), dataIndex: 'USR_NAME', width: 60, hidden:false},
            /*{header: TRANSLATIONS.ID_STATUS, dataIndex: 'PRO_STATUS_LABEL', width: 50, renderer:function(v,p,r){
              color = r.get('PRO_STATUS') == 'ACTIVE'? 'green': 'red';
              return String.format("<font color='{0}'>{1}</font>", color, v);
            }},*/
            {header: _("ID_TASK_TRANSFER"), dataIndex: 'DEL_DELEGATE_DATE', width: 60, renderer:startDateRender},
            {header: _("ID_START_DATE"), dataIndex: 'DEL_INIT_DATE', width: 60, renderer: startDateRender},
            {header: _("ID_END_DATE"), dataIndex: 'DEL_FINISH_DATE', width: 60, renderer:startDateRender},
            {header: _("ID_ACTION"), dataIndex: 'APP_TYPE', width: 50, renderer: actionRenderingTranslation},
            {header: _("ID_ENABLE_ACTION"), dataIndex: 'APP_ENABLE_ACTION_DATE', width: 70, renderer:startDateRender},
            {header: _("ID_DISABLE_ACTION"), dataIndex: 'APP_DISABLE_ACTION_DATE', width: 70, renderer:startDateRender}
            //{header: TRANSLATIONS.ID_TOTAL_CASES, dataIndex: 'CASES_COUNT', width: 80,renderer:function(v){return "<b>"+v+"</b>";}, align:'right'},
            //{header: TRANSLATIONS.ID_PRO_DEBUG, dataIndex: 'PRO_DEBUG_LABEL', width: 50, align:'center'}
          ]
        }),
        store: store,
        tbar:[
          {
            text:_("ID_DYNAFORM_HISTORY"),
            id:'changueLogFormRadioId',
            iconCls: 'button_menu_ext',
            icon: '/images/ext/gray/shapes/hourglass.png',
            handler: function(){
              var rowSelected = processesGrid.getSelectionModel().getSelected();

              if( rowSelected ){
                window.parent.historyGridListChangeLogGlobal.idHistory = rowSelected.data.ID_HISTORY;
                window.parent.historyGridListChangeLogGlobal.tasTitle = rowSelected.data.TAS_TITLE;

                var idHistory = window.parent.historyGridListChangeLogGlobal.idHistory;
                window.parent.Actions.tabFrame('changeLogTab'+idHistory);
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
            xtype: 'tbfill'
          }
        ],
        bbar: new Ext.PagingToolbar({
          pageSize: 15,
          store: store,
          displayInfo: true,
          displayMsg: _('ID_DISPLAY_PROCESSES'),
          emptyMsg: "",
          items:[]
        }),
        listeners: {
          rowdblclick: emptyReturn,
          render: function(){
            this.loadMask = new Ext.LoadMask(this.body, {msg: _('ID_LOADING_GRID') });
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

    processesGrid.addListener('rowcontextmenu', emptyReturn,this);

    processesGrid.on('contextmenu', function (evt) {
      evt.preventDefault();
    }, this);

    function emptyReturn(){
      return;
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



