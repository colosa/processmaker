/*
 * @author: Douglas Medrano
 * May 03, 2011
 */
    function deleteFiles(sDocUid, sVersion) {
      Ext.MessageBox.confirm(_('ID_CONFIRM'), _('ID_DELETE_DOCUMENT'), function(res){
        if(res == 'yes') {
          Ext.MessageBox.show({
            msg: _('ID_LOADING'),
            width:300,
            wait:true,
            waitConfig: {interval:200},
            animEl: 'mb7'
          });

          var requestParams = {
            action : 'delete',
            option: 'documents',
            item: sDocUid + '_' + sVersion,
            'selitems[]': sDocUid + '_' + sVersion
          };
          Ext.Ajax.request({
            url: '../appFolder/appFolderAjax.php',
            params : requestParams,
            success : function(response) {
              Ext.MessageBox.hide();
              store.load();
            },
            failure : function() {
              Ext.Msg.alert(TRANSLATIONS.ID_ERROR, TRANSLATIONS.ID_UNABLE_START_CASE);
            }
          });
        }
      });
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

    function ajaxPostRequestUrlIntact(url, callback_function, id){
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
              Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE') );
            }

        }
      }
    );

    Ext.onReady(function(){
      Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
      Ext.QuickTips.init();

      generateDocumentGrid();

    });


  var generateDocumentGridDownloadGlobal = {};
      generateDocumentGridDownloadGlobal.APP_DOC_UID = '';
      generateDocumentGridDownloadGlobal.FILEDOC = '';
      generateDocumentGridDownloadGlobal.FILEPDF = '';
      generateDocumentGridDownloadGlobal.DOWNLOAD = '';

  function generateDocumentGridDownload(){
    //!generateDocumentGridDownloadGlobalSystem
    var APP_DOC_UID = generateDocumentGridDownloadGlobal.APP_DOC_UID;
    var FILEDOC = generateDocumentGridDownloadGlobal.FILEDOC;
    var FILEPDF = generateDocumentGridDownloadGlobal.FILEPDF;
    var DOWNLOAD = generateDocumentGridDownloadGlobal.DOWNLOAD;

    //!dataSystem
    var downloadLink = '';

      if(DOWNLOAD=='FILEDOC'){
        downloadLink = FILEDOC;
      }
      else if(DOWNLOAD=='FILEPDF'){
        downloadLink = FILEPDF;
      }

    var d = new Date();
    var time = d.getTime();
    downloadLink = downloadLink + '&nocachetime='+time;


    window.location.href=  downloadLink;

  }

  var generateDocumentGridGlobal = {};
      generateDocumentGridGlobal.ref = "";

  function generateDocumentGrid(){

    //dataGlobalConstructor
    generateDocumentGridGlobal.ref = 'cases_Ajax.php';

    //dataGlobal
    generateDocumentGridGlobal.ref = generateDocumentGridGlobal.ref;

    //!dataSystemGlobal
    var ref = generateDocumentGridGlobal.ref;

    //!dataSystem
    var url = ref+'?action=generateDocumentGrid_Ajax';
      store = new Ext.data.GroupingStore({
        proxy : new Ext.data.HttpProxy
        (
          {
            url: url
          }
        ),
        reader : new Ext.data.JsonReader
        (
          {
            totalProperty: 'totalCount',
            root: 'data',
            fields :
            [
              {name : 'APP_DOC_UID'},
              {name : 'FILEDOCEXIST'},
              {name : 'FILEPDFEXIST'},
              {name : 'FILEDOC'},
              {name : 'FILEPDF'},
              {name : 'TITLE'},
              {name : 'OUTDOCTITLE'},
              {name : 'ORIGIN'},
              {name : 'CREATED_BY'},
              {name : 'CREATE_DATE'},
              {name : 'FILEDOCLABEL'},
              {name : 'FILEPDFLABEL'},
              {name : 'DELETE_FILE'},
              {name : 'DOC_VERSION'}
            ]
          }
        )
      });

      var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
          '<p><b>'+TRANSLATIONS.ID_PRO_DESCRIPTION+':</b> {PRO_DESCRIPTION}</p><br>'
        )
      });

      startDateRender = function (v)
      {
          var dateString = "-";

          if(v != "-") {
              dateString = _DF(v, FORMATS.casesListDateFormat);
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

      function renderDeleteFile(val,p,r) {
        if (r.data.DELETE_FILE) {
          return '<img src="/images/delete-16x16.gif" unselectable="off" onClick="deleteFiles(\'' + r.data.APP_DOC_UID + '\', \'' + r.data.DOC_VERSION + '\');">';
        } else {
          return '';
        }
      }

      var processesGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'processesGrid',
        height: '100%',
        //autoWidth : true,
        width:'',
        title : '',
        stateful : true,
        stateId : 'gridGenerateDoc',
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
          columns:
          [
            {id:'APP_DOC_UID', dataIndex: 'APP_DOC_UID', hidden:true, hideable:false},
            {id:'FILEDOCEXIST', dataIndex: 'FILEDOCEXIST', hidden:true, hideable:false},
            {id:'FILEPDFEXIST', dataIndex: 'FILEPDFEXIST', hidden:true, hideable:false},
            {id:'FILEDOC', dataIndex: 'FILEDOC', hidden:true, hideable:false},
            {id:'FILEPDF', dataIndex: 'FILEPDF', hidden:true, hideable:false},
            {header: _("ID_TITLE_FIELD"), dataIndex: 'TITLE', width: 70},
            {header: _("ID_OUTPUT_DOCUMENT"), dataIndex: 'OUTDOCTITLE', width: 70},
            {header: _("ID_ORIGIN_TASK"), dataIndex: 'ORIGIN', width: 70},
            {header: _("ID_CREATED_BY"), dataIndex: 'CREATED_BY', width: 70},
            {header: _("ID_CREATE_DATE"), dataIndex: 'CREATE_DATE', width: 70,renderer:startDateRender},
            {header: '', dataIndex: 'DELETE_FILE', width: 30,renderer:renderDeleteFile}

          ]
        }),
        store: store,
        tbar:[
          {
            text:_("ID_DOWNLOAD"),
            id:'ID_DOWNLOAD_DOC',
            iconCls: 'button_menu_ext',
            icon: '/images/extensionDoc.png',
            handler: function(){

              var rowSelected = processesGrid.getSelectionModel().getSelected();

              if( rowSelected ){
                  Ext.Ajax.request({
                         url : 'cases_ShowDocument' ,
                         params : {actionAjax : 'verifySession'},
                         success: function ( result, request ) {
                           var data = Ext.util.JSON.decode(result.responseText);
                           if( data.lostSession ) {
                            Ext.Msg.show({
                                   title: _('ID_ERROR'),
                                   msg: data.message,
                                   animEl: 'elId',
                                   icon: Ext.MessageBox.ERROR,
                                   buttons: Ext.MessageBox.OK,
                                   fn : function(btn) {
                                   top.location = top.location;
                                   }
                               });
                           } else {
							 //generateDocumentGridGlobal construct
							 generateDocumentGridDownloadGlobal.APP_DOC_UID = rowSelected.data.APP_DOC_UID;
							 generateDocumentGridDownloadGlobal.FILEDOC = rowSelected.data.FILEDOC;
							 generateDocumentGridDownloadGlobal.FILEPDF = rowSelected.data.FILEPDF;
							 generateDocumentGridDownloadGlobal.DOWNLOAD = 'FILEDOC';

							 var APP_DOC_UID = generateDocumentGridDownloadGlobal.APP_DOC_UID;
							 var FILEDOC = generateDocumentGridDownloadGlobal.FILEDOC;
							 var FILEPDF = generateDocumentGridDownloadGlobal.FILEPDF;
							 var DOWNLOAD = generateDocumentGridDownloadGlobal.DOWNLOAD;

							 generateDocumentGridDownload();
                           }
                         },
                         failure: function ( result, request) {
                           if (typeof(result.responseText) != 'undefined') {
                             Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                           }
                         }
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
            disabled:true
          },
          {
            xtype: 'tbseparator'
          },
          {
            text:_("ID_DOWNLOAD"),
            id:'ID_DOWNLOAD_PDF',
            iconCls: 'button_menu_ext',
            icon: '/images/extensionPdf.png',
            handler: function(){

              var rowSelected = processesGrid.getSelectionModel().getSelected();

              if( rowSelected ){
                  Ext.Ajax.request({
                         url : 'cases_ShowDocument' ,
                         params : {actionAjax : 'verifySession'},
                         success: function ( result, request ) {
                           var data = Ext.util.JSON.decode(result.responseText);
                           if( data.lostSession ) {
                            Ext.Msg.show({
                                   title: _('ID_ERROR'),
                                   msg: data.message,
                                   animEl: 'elId',
                                   icon: Ext.MessageBox.ERROR,
                                   buttons: Ext.MessageBox.OK,
                                   fn : function(btn) {
                                   top.location = top.location;
                                   }
                               });
                           } else {
								//generateDocumentGridGlobal construct
								generateDocumentGridDownloadGlobal.APP_DOC_UID = rowSelected.data.APP_DOC_UID;
								generateDocumentGridDownloadGlobal.FILEDOC = rowSelected.data.FILEDOC;
								generateDocumentGridDownloadGlobal.FILEPDF = rowSelected.data.FILEPDF;
								generateDocumentGridDownloadGlobal.DOWNLOAD = 'FILEPDF';

								var APP_DOC_UID = generateDocumentGridDownloadGlobal.APP_DOC_UID;
								var FILEDOC = generateDocumentGridDownloadGlobal.FILEDOC;
								var FILEPDF = generateDocumentGridDownloadGlobal.FILEPDF;
								var DOWNLOAD = generateDocumentGridDownloadGlobal.DOWNLOAD;

								generateDocumentGridDownload();
                           }
                         },
                         failure: function ( result, request) {
                           if (typeof(result.responseText) != 'undefined') {
                             Ext.MessageBox.alert( _('ID_FAILED'), result.responseText);
                           }
                         }
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
            disabled:true
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
          click: function(){


          var rowSelected = processesGrid.getSelectionModel().getSelected();

          if (rowSelected) {
            var FILEDOCEXIST = rowSelected.data.FILEDOCEXIST;
            var FILEPDFEXIST = rowSelected.data.FILEPDFEXIST;

            if (rowSelected.data.FILEDOCLABEL=='') {
              Ext.getCmp('ID_DOWNLOAD_DOC').setDisabled(true);
            }
            else {
              Ext.getCmp('ID_DOWNLOAD_DOC').setDisabled(false);
            }

            if (rowSelected.data.FILEPDFLABEL=='') {
              Ext.getCmp('ID_DOWNLOAD_PDF').setDisabled(true);
            }
            else {
              Ext.getCmp('ID_DOWNLOAD_PDF').setDisabled(false);
            }

            if ((rowSelected.data.FILEPDFLABEL=='') && (rowSelected.data.FILEDOCLABEL=='')) {
              Ext.getCmp('ID_DOWNLOAD_PDF').setDisabled(true);
              Ext.getCmp('ID_DOWNLOAD_DOC').setDisabled(true);
            }
          }

       /*   var ID_DOWNLOAD_PDF2_ = Ext.getCmp('ID_DOWNLOAD_PDF');
          var ID_DOWNLOAD_DOC2_ = Ext.getCmp('ID_DOWNLOAD_DOC');

            if(FILEDOCEXIST== 'javascript:alert("NO DOC")') {
             ID_DOWNLOAD_DOC2_.setDisabled(false);
            }
            else {
             ID_DOWNLOAD_DOC2_.setDisabled(true);
            }

            if(FILEPDFEXIST== 'javascript:alert("NO PDF")') {
             ID_DOWNLOAD_PDF2_.setDisabled(false);
            }
            else {
              ID_DOWNLOAD_PDF2_.setDisabled(true);
            }

            if (!(FILEPDFEXIST== 'javascript:alert("NO PDF")') && !(FILEDOCEXIST== 'javascript:alert("NO DOC")')) {
              ID_DOWNLOAD_DOC_.setDisabled(false);
              ID_DOWNLOAD_PDF2_.setDisabled(false);
            }*/
            /*else {
              ID_DOWNLOAD_PDF_.setDisabled(true);
              ID_DOWNLOAD_DOC_.setDisabled(true);
            }*/

          },
        rowdblclick: function(grid, rowIndex, e) {
            var rowSelected = store.getAt(rowIndex);
            generateDocumentGridDownloadGlobal.APP_DOC_UID = rowSelected.data.APP_DOC_UID;
            generateDocumentGridDownloadGlobal.FILEDOC = rowSelected.data.FILEDOC;
            generateDocumentGridDownloadGlobal.FILEPDF = rowSelected.data.FILEPDF;
            if (rowSelected.data.FILEPDFLABEL != '') {
                generateDocumentGridDownloadGlobal.DOWNLOAD = 'FILEPDF';
            } else {
                generateDocumentGridDownloadGlobal.DOWNLOAD = 'FILEDOC';
            }
            var APP_DOC_UID = generateDocumentGridDownloadGlobal.APP_DOC_UID;
            var FILEDOC = generateDocumentGridDownloadGlobal.FILEDOC;
            var FILEPDF = generateDocumentGridDownloadGlobal.FILEPDF;
            var DOWNLOAD = generateDocumentGridDownloadGlobal.DOWNLOAD;

            generateDocumentGridDownload();
        },
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
    }

    var viewport = new Ext.Viewport({
      layout: 'border',
      autoScroll: true,
      items: [
        processesGrid
      ]
    });
  }
