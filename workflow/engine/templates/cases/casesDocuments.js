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
      datastore.reload();
    // Ext.getCmp('dirTree').getRootNode().reload();
    }else{
  // Ext.Msg.alert(TRANSLATIONS.ID_REFRESH_LABEL,
  // TRANSLATIONS.ID_REFRESH_MESSAGE);
  }
  }
});

// Ext.BLANK_IMAGE_URL = 'resources/s.gif';

Ext.chart.Chart.CHART_URL = '/images/charts.swf';
Ext.FlashComponent.EXPRESS_INSTALL_URL = '/images/expressinstall.swf';
// The Quicktips are used for the toolbar and Tree mouseover tooltips!
Ext.QuickTips.init();

try{
  rc=new RegExp('^("(\\\\.|[^"\\\\\\n\\r])*?"|[,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t])+?$');
}
catch(z){
  rc=/^(true|false|null|\[.*\]|\{.*\}|".*"|\d+|\d+\.\d+)$/;
}

var conn = new Ext.data.Connection();

streamFilefromPM=function(fileStream) {
  Ext.Ajax.request({
    url:fileStream,
    params: {
      request:true
    },
    success: function(response) {
      results = Ext.decode(response.responseText);
      if(results.success=='success'){
        messageText=TRANSLATIONS.ID_DOWNLOADING_FILE+" "+results.message;
        statusBarMessage( messageText, true, true );
        try {
          Ext.destroy(Ext.get('downloadIframe'));
        }
        catch(e) {}
        Ext.DomHelper.append(document.body, {
          tag: 'iframe',
          id:'downloadIframe',
          frameBorder: 0,
          width: 0,
          height: 0,
          css: 'display:none;visibility:hidden;height:0px;',
          src: fileStream
        });
      }else{

        msgbox = Ext.Msg.alert('Error', results.message);
        msgbox.setIcon( Ext.MessageBox.ERROR );
      }
    },
    failure: function() {
      if (results.message) {
        Ext.Msg.alert('Infomation',results.message);
      }

    }
  });
};

var swHandleCallbackRootNodeLoad = 0;

function rootNodeCreate()
{
    var node = new Ext.tree.AsyncTreeNode({
        id: "root",
        text: "/",
        draggable: false,
        expanded: true,
        cls: "folder",

        listeners: {
            beforeload: function (nodeRoot) {
                nodeRoot.setIcon("");
            },
            load: function (nodeRoot) {
                nodeRoot.setIcon("/images/ext/default/tree/folder.gif");
            },
            expand: function (nodeRoot) {
                if (nodeRoot.hasChildNodes()) {
                    nodeRoot.setIcon("/images/ext/default/tree/folder-open.gif");
                }
            },
            collapse: function (nodeRoot) {
                nodeRoot.setIcon("/images/ext/default/tree/folder.gif");
            }
        }
    });

    return node;
}


function chDir( directory, loadGridOnly ) {
    // console.info("**** Changing Directory: "+directory+" --
    // "+loadGridOnly);
    if( datastore.directory.replace( /\//g, '' ) == directory.replace( /\//g, '' )
      && datastore.getTotalCount() > 0 && directory != '') {
      // Prevent double loading
      return;
    }
    datastore.directory = directory;
    var conn = datastore.proxy.getConnection();
    if( directory == '' || conn && !conn.isLoading()) {
      datastore.load({
        params:{
          start: 0,
          limit: 100,
          dir: directory,
          node: directory,
          option:'gridDocuments',
          action:'expandNode',
          sendWhat: datastore.sendWhat
        }
      });
  }
  tb = ext_itemgrid.getTopToolbar();
  /*if(directory=="NA"){ // Disable create new folder under NA
    tb.items.get('tb_new').disable();
    tb.items.get('tb_upload').disable();
  }else{
    tb.items.get('tb_new').enable();
    tb.items.get('tb_upload').enable();
  }*/
  /*if( directory!='root'){
    if( permitodelete==1 || permitoaddfolder==1 || permitoaddfile==1) {
      tb.items.get('tb_delete').enable();
  //    tb.items.get('tb_new').enable();
      tb.items.get('tb_upload').enable();
    } else {
      tb.items.get('tb_delete').disable();
   //   tb.items.get('tb_new').disable();
      tb.items.get('tb_upload').disable();
    }
  } else {
      tb.items.get('tb_delete').disable();
  }*/
  /*
   * tb.items.get('tb_delete')[selections[0].get('is_deletable') ? 'enable' :
   * 'disable']();
   */
  if( !loadGridOnly ) {
    expandTreeToDir( null, directory );
  }

}

function expandTreeToDir( node, dir ) {
  // console.info("Expanding Tree to Dir "+node+" - "+dir);
  dir = dir ? dir : new String('');
  var dirs = dir.split('/');
  if( dirs[0] == '') {
    dirs.shift();
  }
  if( dirs.length > 0 ) {
    // console.log("Dir to expand... "+dirs[0]);
    node = dirTree.getNodeById( dirs[0] );
    if( !node ) return;
    if( node.isExpanded() ) {
      expandNode( node, dir );
      return;
    }
    node.on('load', function() {
      expandNode( node, dir );
    } );
    node.expand();
  }
}
function expandNode( node, dir ) {
  // console.info("Expanding Node "+node+" - "+dir);
  var fulldirpath, dirpath;

  var dirs = dir.split('/');
  if( dirs[0] == '') {
    dirs.shift();
  }
  if( dirs.length > 0 ) {
    fulldirpath = '';
    for( i=0; i < dirs.length; i++ ) {
      fulldirpath += dirs[i];
    }
    if( node.id.substr( 0, 5 ) != '_RRR_' ) {
      fulldirpath = fulldirpath.substr( 5 );
    }

    if( node.id != fulldirpath ) {
      dirpath = '';

      var nodedirs = node.id.split('_RRR_');
      if( nodedirs[0] == '' ) nodedirs.shift();
      for( i=0; i < dirs.length; i++ ) {
        if( nodedirs[i] ) {
          dirpath += '_RRR_'+ dirs[i];
        } else {
          dirpath += '_RRR_'+ dirs[i];
          // dirpath = dirpath.substr( 5 );
          var nextnode = dirTree.getNodeById( dirpath );
          if( !nextnode ) {
            return;
          }
          if( nextnode.isExpanded() ) {
            expandNode( nextnode, dir );
            return;
          }
          nextnode.on( 'load', function() {
            expandNode( nextnode, dir );
          } );

          nextnode.expand();
          break;
        }
      }
    }
    else {
      node.select();
    }

  }
}
function handleNodeClick( sm, node ) {
  if( node && node.id ) {
    // console.log("Node Clicked: "+node);
    chDir( node.id );
  }
}

function showLoadingIndicator( el, replaceContent ) {
  // console.info("showLoadingIndicator");
  if( !el ) return;
  var loadingimg = '/images/documents/_indicator.gif';
  var imgtag = '<' + 'img src="'+ loadingimg + '" alt="'+TRANSLATIONS.ID_LOADING+'" border="0" name="'+TRANSLATIONS.ID_LOADING+'" align="absmiddle" />';

  if( replaceContent ) {
    el.innerHTML = imgtag;
  }
  else {
    el.innerHTML += imgtag;
  }
}
function getURLParam( strParamName, myWindow){
  // console.info("getURLParam");
  // console.trace();
  if( !myWindow ){
    myWindow = window;
  }
  var strReturn = "";
  var strHref = myWindow.location.href;
  if ( strHref.indexOf("?") > -1 ){
    var strQueryString = strHref.substr(strHref.indexOf("?")).toLowerCase();
    var aQueryString = strQueryString.split("&");
    for ( var iParam = 0; iParam < aQueryString.length; iParam++ ){
      if ( aQueryString[iParam].indexOf(strParamName + "=") > -1 ){
        var aParam = aQueryString[iParam].split("=");
        strReturn = aParam[1];
        break;
      }
    }
  }
  return strReturn;
}

function openActionDialog( caller, action ) {
  // console.log("Dialog open: "+caller+" ->"+action);
  var dialog;
  var selectedRows = ext_itemgrid.getSelectionModel().getSelections();
  if( selectedRows.length < 1 ) {
    var selectedNode = dirTree.getSelectionModel().getSelectedNode();
    if( selectedNode ) {
      selectedRows = Array( dirTree.getSelectionModel().getSelectedNode().id.replace( /_RRR_/g, '/' ) );
    }
  }
  var dontNeedSelection = {
    newFolder:1,
    uploadDocument:1,
    search:1
  };
  if( dontNeedSelection[action] == null && selectedRows.length < 1 ) {
    Ext.Msg.alert( 'Error',TRANSLATIONS.ID_NO_ITEMS_SELECTED);
    return false;
  }

  switch( action ) {
    case 'copyAction':
    case 'edit':
    case 'newFolder':
    case 'moveAction':
    case 'rename':
    case 'search':
    case 'uploadDocument':
      requestParams = getRequestParams();
      requestParams.action = action;
      if( action != "edit" ) {
        dialog = new Ext.Window( {
          id: "dialog",
          autoCreate: true,
          modal:true,
          width:600,
          autoHeight: true,
          shadow:true,
          minWidth:300,
          minHeight:200,
          proxyDrag: true,
          resizable: true,
          // renderTo: Ext.getBody(),
          keys: {
            key: 27,
            fn  : function(){
              dialog.hide();
            }
          }// ,
        // animateTarget: typeof caller.getEl == 'function'
        // ? caller.getEl() : caller,
        // title: 'dialog_title'

        });
      }
      Ext.Ajax.request( {
        url: '../appFolder/appFolderAjax.php',
        params: Ext.urlEncode( requestParams ),
        scripts: true,
        callback: function(oElement, bSuccess, oResponse) {
          if( !bSuccess ) {
            msgbox = Ext.Msg.alert( TRANSLATIONS.ID_SERVER_COMMUNICATION_ERROR);
            msgbox.setIcon( Ext.MessageBox.ERROR );
          }
          if( oResponse && oResponse.responseText ) {

            // Ext.Msg.alert("Debug",
            // oResponse.responseText
            // );
            try{
              json = Ext.decode( oResponse.responseText );

              if( json.error && typeof json.error != 'xml' ) {
                Ext.Msg.alert( "error", json.error );
                dialog.destroy();
                return false;
              }
            } catch(e) {
              msgbox = Ext.Msg.alert( "error", "JSON Decode Error: " + e.message );
              msgbox.setIcon( Ext.MessageBox.ERROR );
              return false;
            }
            if( action == "edit" ) {
              Ext.getCmp("mainpanel").add(json);
              Ext.getCmp("mainpanel").activate(json.id);
            }
            else {
              // we expect the
              // returned JSON to
              // be an object that
              // contains an
              // "Ext.Component"
              // or derivative in
              // xtype notation
              // so we can simply
              // add it to the
              // Window
              // console.log(json);
              dialog.add(json);
              if( json.dialogtitle ) {
                // if the
                // component
                // delivers a
                // title for our
                // dialog we can
                // set the title
                // of the window
                dialog.setTitle(json.dialogtitle);
              }

              try {
                // recalculate
                // layout
                dialog.doLayout();
                // recalculate
                // dimensions,
                // based on
                // those of the
                // newly added
                // child
                // component
                firstComponent = dialog.getComponent(0);
                newWidth = firstComponent.getWidth() + dialog.getFrameWidth();
                newHeight = firstComponent.getHeight() + dialog.getFrameHeight();
                dialog.setSize( newWidth, newHeight );

              } catch(e) {}
              // alert( "Before:
              // Dialog.width: " +
              // dialog.getWidth()
              // + ", Client
              // Width: "+
              // Ext.getBody().getWidth());
              if( dialog.getWidth() >= Ext.getBody().getWidth() ) {
                dialog.setWidth( Ext.getBody().getWidth() * 0.8 );
              }
              // alert( "After:
              // Dialog.width: " +
              // dialog.getWidth()
              // + ", Client
              // Width: "+
              // Ext.getBody().getWidth());
              if( dialog.getHeight() >= Ext.getBody().getHeight() ) {
                dialog.setHeight( Ext.getBody().getHeight() * 0.7 );
              } else if( dialog.getHeight() < Ext.getBody().getHeight() * 0.3 ) {
                dialog.setHeight( Ext.getBody().getHeight() * 0.5 );
              }

              // recalculate
              // Window size
              dialog.syncSize();
              // center the window
              dialog.center();
            }
          } else if( !response || !oResponse.responseText) {
            msgbox = Ext.Msg.alert( "error", "Received an empty response");
            msgbox.setIcon( Ext.MessageBox.ERROR );

          }
        }
      });

      if( action != "edit" ) {
        dialog.on( 'hide', function() {
          dialog.destroy(true);
        } );
        dialog.show();
      }
      break;

    case 'delete':
      var num = selectedRows.length;
      Ext.Msg.confirm(TRANSLATIONS.ID_DELETE, String.format(TRANSLATIONS.ID_DELETE_SELECTED_ITEMS, num ), deleteFiles);
      break;

    case 'download':
      fileName=ext_itemgrid.getSelectionModel().getSelected().get('name');
      // alert(ext_itemgrid.getSelectionModel().getSelected().get('downloadLink'));
      // alert(ext_itemgrid.getSelectionModel().getSelected().get('downloadLabel'));

      var urlDownload = ext_itemgrid.getSelectionModel().getSelected().get("downloadLink");

      if (ext_itemgrid.getSelectionModel().getSelected().get("appDocPlugin") != "") {
        messageText = TRANSLATIONS.ID_DOWNLOADING_FILE + " " + ext_itemgrid.getSelectionModel().getSelected().get("name");
        statusBarMessage(messageText, true, true);

        try {
          Ext.destroy(Ext.get("downloadIframe"));
        }
        catch (e) {
        }

        Ext.DomHelper.append(document.body, {
          tag: "iframe",
          id: "downloadIframe",
          frameBorder: 0,
          width: 0,
          height: 0,
          css: "display: none; visibility: hidden; height: 0px;",
          src: urlDownload
        });
      }
      else {
         streamFilefromPM(urlDownload);
      }

      /*
			 * if(document.location =
			 * ext_itemgrid.getSelectionModel().getSelected().get('downloadLink')){
			 * messageText="Downloading file "+fileName; statusBarMessage(
			 * messageText, false, true ); }else{ alert("sadasd"); }
			 */
      break;
  }
}

function handleCallback(requestParams, node) {
  var conn = new Ext.data.Connection();

  conn.request({
    url: '../appFolder/appFolderAjax.php',
    params: requestParams,
    callback: function(options, success, response ) {
      if( success ) {
        json = Ext.decode( response.responseText );
        if( json.success ) {
          if( json.success == "success"){
            statusBarMessage( json.message, false, true );
            try {
              if( dropEvent) {
                dropEvent.target.parentNode.reload();
                dropEvent = null;
              }
              if( node ) {
                if( options.params.action == 'delete' || options.params.action == 'rename' ) {
                  node.parentNode.select();
                }
                node.parentNode.reload();
              } else {
                datastore.reload();
              }
            } catch(e) {
              datastore.reload();
            }
          }else{
            statusBarMessage( json.message, false, false );
          }
        } else {
          Ext.Msg.alert( 'Failure', json.error );
        }

        if (swHandleCallbackRootNodeLoad == 1) {
            Ext.getCmp("dirTreePanel").setRootNode(rootNodeCreate());
            swHandleCallbackRootNodeLoad = 0;
        }
      }
      else {
        Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
      }

    }
  });
}
function getRequestParams() {
  // console.info("Get Request params ");
  var selitems, dir, node;
  var selectedRows = ext_itemgrid.getSelectionModel().getSelections();
  if( selectedRows.length < 1 ) {
   sOptiondir='directory';
    node = dirTree.getSelectionModel().getSelectedNode();
    if( node ) {
      var dir = dirTree.getSelectionModel().getSelectedNode().id.replace( /_RRR_/g, '/' );
      var lastSlash = dir.lastIndexOf( '/' );
      if( lastSlash > 0 ) {
        selitems = Array( dir.substring(lastSlash+1) );
      } else {
        selitems = Array( dir );
      }
    } else {
      selitems = {};
    }
    dir = datastore.directory.substring( 0, datastore.directory.lastIndexOf('/'));
  }
  else {
    sOptiondir='documents';
    selitems = Array(selectedRows.length);

    if( selectedRows.length > 0 ) {
      for( i=0; i < selectedRows.length;i++) {
        selitems[i] = selectedRows[i].get('id');
      }
    }
    dir = datastore.directory;
  }
  // Ext.Msg.alert("Debug", datastore.directory );
  var requestParams = {
    option: sOptiondir,//'new',
    dir: datastore.directory,
    item: selitems.length > 0 ? selitems[0]:'',
    'selitems[]': selitems
  };
  return requestParams;

  }
/**
* Function for actions, which don't require a form like download,
* extraction, deletion etc.
*/
function deleteFiles(btn)
{
    if (btn != "yes") {
        return;
    }

    requestParams = getRequestParams();
    requestParams.action = "delete";

    if (!(requestParams.option == "documents")) {
        swHandleCallbackRootNodeLoad = 1;
    }

    handleCallback(requestParams);

    if (requestParams.option == "documents") {
        datastore.sendWhat = "files";
        loadDir();
    }
    //else {
    //    Ext.getCmp("dirTreePanel").setRootNode(rootNodeCreate());
    //}
}

function extractArchive(btn) {
  if( btn != 'yes') {
    return;
  }
  requestParams = getRequestParams();
  requestParams.action = 'extract';
  handleCallback(requestParams);
}

function deleteDir(btn, node)
{
    if (btn != "yes") {
        return;
    }

    requestParams          = getRequestParams();
    requestParams.dir      = datastore.directory.substring(0, datastore.directory.lastIndexOf("/"));
    requestParams.selitems = Array(node.id.replace(/_RRR_/g, "/"));
    requestParams.action   = "delete";

    swHandleCallbackRootNodeLoad = 1;

    handleCallback(requestParams, node);

    //Ext.getCmp("dirTreePanel").setRootNode(rootNodeCreate());
}

Ext.msgBoxSlider = function(){
  var msgCt;

  function createBox(t, s){
    return ['<div class="msg">',
    '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
    '<div class="x-box-ml"><div class="x-box-mr"><div id="x-box-mc-inner" class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
    '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
    '</div>'].join('');
  }
  return {
    msg : function(title, format){
      if(!msgCt){
        msgCt = Ext.DomHelper.insertFirst(document.body, {
          id:'msg-div'
        }, true);
      }
      msgCt.alignTo(document, 't-t');
      var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
      var m = Ext.DomHelper.append(msgCt, {
        html:createBox(title, s)
        }, true);
      m.setWidth(400 );
      m.position(null, 5000 );
      m.alignTo(document, 't-t');
      Ext.get('x-box-mc-inner' ).setStyle('background-image', 'url("/images/documents/_accept.png")');
      Ext.get('x-box-mc-inner' ).setStyle('background-position', '5px 10px');
      Ext.get('x-box-mc-inner' ).setStyle('background-repeat', 'no-repeat');
      Ext.get('x-box-mc-inner' ).setStyle('padding-left', '35px');
      m.slideIn('t').pause(3).ghost("t", {
        remove:true
      });
    }
  };
}();


function statusBarMessage( msg, isLoading, success ) {
  // console.log("Status Bar needed");
  // console.log(msg);
  var statusBar = Ext.getCmp('statusPanel');
  if( !statusBar ) return;
  // console.log("Status bar acceced: "+msg);
  if( isLoading ) {
    statusBar.showBusy();
  }
  else {
    statusBar.setStatus("Done.");
  }
  if( success ) {
    statusBar.setStatus({
      text: '' + msg,
      iconCls: 'success',
      clear: true
    });
    Ext.msgBoxSlider.msg('', msg );
  } else {
    statusBar.setStatus({
      text: 'Error: ' + msg,
      iconCls: 'error',
      clear: true
    });
    Ext.msgBoxSlider.msg('Error', msg );

  }


}

function selectFile( dir, file ) {
  // console.log("file selected: "+dir+" - "+file);
  chDir( dir );
  var conn = datastore.proxy.getConnection();
  if( conn.isLoading() ) {
    setTimeout( "selectFile(\"" + dir + "\", \""+ file + "\")", 1000 );
  }
  idx  = datastore.find( "name", file );
  if( idx >= 0 ) {
    ext_itemgrid.getSelectionModel().selectRow( idx );
  }
}

/**
		 * Debug Function, that works like print_r for Objects in Javascript
		 */
function var_dump(obj) {
  var vartext = "";
  for (var prop in obj) {
    if( isNaN( prop.toString() )) {
      vartext += "\t->"+prop+" = "+ eval( "obj."+prop.toString()) +"\n";
    }
  }
  if(typeof obj == "object") {
    return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "") + "\n" + vartext;
  } else {
    return "Type: "+typeof(obj)+"\n" + vartext;
  }
}// end function var_dump

var datastore;
datastore = new Ext.data.Store({
  proxy : new Ext.data.HttpProxy({
    url : "../appFolder/appFolderAjax.php",
    directory : "/",
    params : {
      start: 0,
      limit: 100,
      dir : this.directory,
      node : this.directory,
      option : "gridDocuments",
      action : "expandNode"
    }
  }),
  directory : "/",
  sendWhat : "files",
  // create reader that reads the File records
  reader : new Ext.data.JsonReader({
    root : "items",
    totalProperty : "totalCount"
  }, Ext.data.Record.create([ {
    name : "name"
  }, {
    name : "size"
  }, {
    name : "type"
  }, {
    name : "modified"
  }, {
    name : "perms"
  }, {
    name : "icon"
  }, {
    name : "owner"
  }, {
    name : "owner_firstname"
  }, {
    name : "owner_lastname"
  }, {
    name : "is_deletable"
  }, {
    name : "is_file"
  }, {
    name : "is_archive"
  }, {
    name : "is_writable"
  }, {
    name : "is_chmodable"
  }, {
    name : "is_readable"
  }, {
    name : "is_deletable"
  }, {
    name : "is_editable"
  }, {
    name : "id"
  }, {
    name : "docVersion"
  }, {
    name : "appDocType"
  }, {
    name : "appDocCreateDate"
  }, {
    name : "appDocPlugin"
  }, {
    name : "appLabel"
  }, {
    name : "proTitle"
  }, {
    name : "appDocVersionable"
  },{
    name : "downloadLink"
  },{
    name : "downloadLabel"
  }

  ])),

  // turn on remote sorting
  remoteSort : false
});
datastore.paramNames["dir"] = "direction";
datastore.paramNames["sort"] = "order";

datastore.on("beforeload",
  function(ds, options) {
    options.params.dir = options.params.dir ? options.params.dir
    : ds.directory;
    options.params.node = options.params.dir ? options.params.dir : ds.directory;
    options.params.option = "gridDocuments";
    options.params.action = "expandNode";
    options.params.sendWhat = datastore.sendWhat;
  });
// pluggable renders
function renderFileName(value, p, record) {
  return String.format(
    '<img src="{0}" alt="* " align="absmiddle" />&nbsp;<b>{1}</b>',
    record.get('icon'), value);
}
function renderType(value, p, record) {
  if(record.get('appDocType')!=""){
    return String.format('{1}, {0}', value,record.get('appDocType'));
  }else{
    return String.format('<i>{0}</i>', value);
  }
}
function renderVersion(value, p, record) {
  if(record.get("appDocVersionable")=="1"){
    if(value>1){
      // return String.format('<b>{0}</b>&nbsp;&nbsp;&nbsp;<a
      // href="#"><img src="{1}" border="0" title="Upload New Version"
      // valign="absmiddle" onClick="alert(\'{2}\');return false;"/></a>',
      // value,'/images/documents/_up.png','Upload new Version');
      //return String.format('<b>{0}</b><table cellspacing="0" class="x-btn x-btn-icon" id="tb_upload"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" id="ext-gen100" class=" x-btn-text button_menu_ext ss_sprite ss_page_white_get">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table>', value);
      return String.format('<b>{0}</b>', value);
    }else{
      // return String.format('{0}&nbsp;&nbsp;&nbsp;<a href="#"><img
      // src="{1}" border="0" title="Upload New Version"
      // valign="absmiddle" onClick="alert(\'{2}\');return false;"/></a>',
      // value,'/images/documents/_up.png','Upload new Version');
      //return String.format('{0}<table cellspacing="0" class="x-btn x-btn-icon" id="tb_upload"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" id="ext-gen100" class=" x-btn-text button_menu_ext ss_sprite ss_page_white_get">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table>', value);
      return String.format('{0}', value);
    }
  }else{

    return String.format('<b>-</b>',value);
  }
}
function renderVersionExpander(value, p, record) {
  // addcc.png
  // system-search.png
  p.cellAttr = 'rowspan="2"';
  // return '<div class="x-grid3-row-expander">&#160;</div>';
  if(record.get("appDocVersionable")=="1"){
    if(value>1){
      return '<div class="x-grid3-row-expander">&#160;</div>';
    // return String.format('<div
    // class="x-grid3-row-expander">{0}</div>', value);
    }else{
      return '';
    }
  }else{

    return String.format('',value);
  }
}
//Render Full Name
renderFullName = function(value, p, record){
  return _FNF(value, record.get('owner_firstname'), record.get('owner_lastname'));
};

renderModifiedDate = function(value, p, record){
  return _DF(value);
};

var gridtb = new Ext.Toolbar(
  [
  {
    xtype : "tbbutton",
    id : 'tb_home',
    iconCls: 'button_menu_ext ss_sprite ss_house',// icon :
    // '/images/documents/_home.png',
    // text : 'Root',
    tooltip : TRANSLATIONS.ID_ROOT_FOLDER,
    // cls : 'x-btn-text-icon',
    cls : 'x-btn-icon',
    handler : function() {
      chDir('');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_reload',
    iconCls: 'button_menu_ext ss_sprite ss_arrow_refresh',// icon
    // :
    // '/images/documents/_reload.png',
    // text : 'Reload',
    tooltip : TRANSLATIONS.ID_RELOAD,
    // cls : 'x-btn-text-icon',
    cls : 'x-btn-icon',
    handler : loadDir
  },

  {
    xtype : "tbbutton",
    id : 'tb_search',
    icon : '/images/documents/_filefind.png',
    // text : 'Search',
    tooltip : TRANSLATIONS.ID_SEARCH,
    // cls : 'x-btn-text-icon',
    cls : 'x-btn-icon',
    disabled : true,
    hidden: true,
    handler : function() {
      openActionDialog(this, 'search');
    }
  },
  '-',
  {
    xtype : "tbbutton",
    id : 'tb_new',
    iconCls: 'button_menu_ext ss_sprite ss_folder_add',// icon
    // :
    // '/images/documents/_filenew.png',
    tooltip : TRANSLATIONS.ID_NEW_FOLDER,
    cls : 'x-btn-icon',
   // disabled : false,
    handler : function() {
      openActionDialog(this, 'newFolder');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_copy',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_copy',// icon :
    // '/images/documents/_editcopy.png',

    tooltip : TRANSLATIONS.ID_COPY,
    cls : 'x-btn-icon',
    disabled : false,
    hidden: true,
    handler : function() {
      openActionDialog(this, 'copyAction');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_move',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_go',// icon :
    // '/images/documents/_move.png',
    tooltip : TRANSLATIONS.ID_MOVE,
    cls : 'x-btn-icon',
    disabled : false,
    hidden: true,
    handler : function() {
      openActionDialog(this, 'moveAction');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_delete',
    iconCls: 'button_menu_ext ss_sprite ss_folder_delete',// icon
    // :
    // '/images/documents/_editdelete.png',
    tooltip : TRANSLATIONS.ID_DELETE,
    cls : 'x-btn-icon',
    disabled : false,
//    hidden: (showdelete==1)?false:true,
    handler : function() {
      openActionDialog(this, 'delete');
//      openActionDialog(this, 'deleteDir');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_rename',
    iconCls: 'button_menu_ext ss_sprite ss_textfield_rename',// icon :
    // '/images/documents/_fonts.png',
    tooltip : TRANSLATIONS.ID_RENAME,
    cls : 'x-btn-icon',
    disabled : true,
    hidden: true,
    handler : function() {
      openActionDialog(this, 'rename');
    }
  },
  '-',
  {
    xtype : "tbbutton",
    id : 'tb_download',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_put',// icon
    // :
    // '/images/documents/_down.png',
    tooltip : TRANSLATIONS.ID_DOWNLOAD,
    cls : 'x-btn-icon',
   // disabled : true,
    handler : function() {
      openActionDialog(this, 'download');
    }
  },
  {
    xtype : "tbbutton",
    id : 'tb_upload',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_get',// icon
    // :
    // '/images/documents/_up.png',
    tooltip : TRANSLATIONS.ID_UPLOAD,
    cls : 'x-btn-icon',
    disabled : false,
    handler : function() {
      /*
						 * Ext.ux.OnDemandLoad
						 * .load("/scripts/extjs3-ext/ux.swfupload/SwfUploadPanel.css");
						 * Ext.ux.OnDemandLoad
						 * .load("/scripts/extjs3-ext/ux.swfupload/SwfUpload.js");
						 * Ext.ux.OnDemandLoad .load(
						 * "/scripts/extjs3-ext/ux.swfupload/SwfUploadPanel.js",
						 * function(options) { openActionDialog(this, 'upload');
						 * });
						 */
      openActionDialog(this, 'uploadDocument');
    }
  },
  '-',
  new Ext.Toolbar.Button({
    text : TRANSLATIONS.ID_SHOW_DIRS,
    enableToggle : true,
    pressed : false,
    handler : function(btn, e) {
      if (btn.pressed) {
        datastore.sendWhat = 'both';
        loadDir();
      } else {
        datastore.sendWhat = 'files';
        loadDir();
      }
    }
  }), '-', new Ext.form.TextField({
    name : "filterValue",
    id : "filterField",
    enableKeyEvents : true,
    title : TRANSLATIONS.ID_FILTER_CURRENT_VIEW,
    emptyText : TRANSLATIONS.ID_FILTER_CURRENT_VIEW,
    listeners : {
      "keypress" : {
        fn : function(textfield, e) {
          if (e.getKey() == Ext.EventObject.ENTER) {
            filterDataStore();
          }
        }
      }
    }
  }), new Ext.Toolbar.Button({
    text : '&nbsp;X&nbsp;',
    handler : function() {
      datastore.clearFilter();
      Ext.getCmp("filterField").setValue("");
    }
  })

  ]);
function filterDataStore(btn, e) {
  var filterVal = Ext.getCmp("filterField").getValue();
  if (filterVal.length > 1) {
    datastore.filter('name', eval('/' + filterVal + '/gi'));
  } else {
    datastore.clearFilter();
  }
}
// add a paging toolbar to the grid's footer
var gridbb = new Ext.PagingToolbar({
  store: datastore,
  pageSize: 100,
  displayInfo: true,
  displayMsg: _("ID_DISPLAY_TOTAL"),
  emptyMsg: _("ID_DISPLAY_EMPTY"),
  beforePageText : TRANSLATIONS.ID_PAGE,
  // afterPageText : 'of %',
  firstText : TRANSLATIONS.ID_FIRST,
  lastText : TRANSLATIONS.ID_LAST,
  nextText : TRANSLATIONS.ID_NEXT,
  prevText : TRANSLATIONS.ID_PREVIOUS,
  refreshText: TRANSLATIONS.ID_RELOAD
});

var grid;
var getGrid = function( data, element) {
  // var grid = Ext.getCmp('gridpanel');

   grid = new Ext.grid.GridPanel({
    store: datastore,
    cm: cm,
    stripeRows: true,
    // autoExpandColumn: 'company',
    autoHeight: true,
    border: false,
    width: '100%',
    stateful: true,
    stateId: 'grid',
    header:false,
    headerAsText:false,
    hideHeaders:true,
    plugins: expander
  });

  element && grid.render( element);
  return grid;
};

var expander = new Ext.ux.grid.RowExpander({
  tpl              : '<div class="ux-row-expander-box" style="border: 2px solid red;"></div>',
  // header:'Version',
  /*
 * tpl : new Ext.Template( '<p><b>Company:</b> {company}</p><br>', '<p><b>Summary:</b>
 * {desc}</p>' ),
 */


  // width : 50,
  // align : 'center',
  expandOnEnter: false,
  expandOnDblClick: false,
  fixed: false,
  dataIndex: 'docVersion',
  actAsTree        : true,
  treeLeafProperty : 'is_leaf',
  listeners        : {
    expand : function( expander, record, body, rowIndex) {
      data = new Array();
      getGrid( data, Ext.get( this.grid.getView().getRow( rowIndex)).child( '.ux-row-expander-box'));
    // alert( Ext.ComponentMgr.all.length);
    }
  },
  renderer : renderVersionExpander
});

//The column model has information about grid columns
//dataIndex maps the column to the specific data field in
//the data store
var cm = new Ext.grid.ColumnModel([{
  id: "gridcm", //id assigned so we can apply custom css (e.g. -> .x-grid-col-topic b { color:#333 })
  header: _("ID_NAME"),
  dataIndex: "name",
  width: 200,
  renderer: renderFileName,
  sortable: true,
  groupable: true,
  editor: new Ext.form.TextField({
    allowBlank: false
  }),
  css: "white-space:normal;"
}, {
  header: _("ID_VERSION"),
  dataIndex: "docVersion",
  width: 100,
  align: "center",
  renderer: renderVersion
}, /* expander, */{
  header: _("ID_MODIFIED"),
  dataIndex: "appDocCreateDate",
  width: 100,
  renderer: renderModifiedDate
}, {
  header: _("ID_OWNER"),
  dataIndex: "owner",
  width: 100,
  sortable: true,
  groupable: true,
  renderer: renderFullName
  //sortable: false
}, {
  header: _("ID_DOCUMENT_TYPE"),
  dataIndex: "appDocType",
  width: 100,
  hidden: true
  //align: "right"
  //renderer: renderType
}, {
  dataIndex: "appDocPlugin",
  hidden: true,
  hideable: false
}, {
  header: _("ID_TYPE"),
  dataIndex: "type",
  width: 100,
  sortable: true,
  groupable: true,
  //align: "right",
  renderer: renderType
}, {
  header: _("ID_PROCESS"),
  dataIndex: "proTitle",
  width: 150,
  sortable: true,
  groupable: true
  //align: "right"
  //renderer: renderType
}, {
  header: _("ID_CASE"),
  dataIndex: "appLabel",
  width: 150,
  sortable: true,
  groupable: true
  //align: "right"
  //renderer: renderType
},{
  header: _("ID_SIZE"),
  dataIndex: "size",
  width: 50,
  hidden: true
}, {
  header: _("ID_PERMISSIONS"),
  dataIndex: "perms",
  width: 100,
  hidden: true
}, {
  dataIndex: "is_deletable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_file",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_archive",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_writable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_chmodable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_readable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_deletable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "is_editable",
  hidden: true,
  hideable: false
}, {
  dataIndex: "id",
  hidden: true,
  hideable: false
}]);

//By default columns are sortable
cm.defaultSortable = true;

function handleRowClick(sm, rowIndex) {//alert(rowIndex);
  // console.log("Row Clicked: "+rowIndex);
  var selections = sm.getSelections();
  tb = ext_itemgrid.getTopToolbar();
  if (selections.length > 1) {
//    tb.items.get('tb_delete').enable();
    tb.items.get('tb_delete')[permitodelete==1 ? 'enable': 'disable']();
    tb.items.get('tb_rename').disable();
   tb.items.get('tb_download').hide();
    //tb.items.get('tb_download').disable();
  } else if (selections.length == 1) {

//    tb.items.get('tb_delete')[selections[0].get('is_deletable') ? 'enable': 'disable']();
    tb.items.get('tb_delete')[permitodelete==1 ? 'enable': 'disable']();
    tb.items.get('tb_rename')[selections[0].get('is_deletable') ? 'disable': 'disable']();
    tb.items.get('tb_download')[selections[0].get('is_readable')
    && selections[0].get('is_file') ? 'show' : 'hide']();
  } else {
    tb.items.get('tb_delete').disable();
    tb.items.get('tb_rename').disable();
    tb.items.get('tb_download').hide();
  }
  return true;
}


// trigger the data store load
function loadDir() {
  // console.info("loadDir");
  // console.trace();
  datastore.load({
    params : {
      start: 0,
      limit: 100,
      dir : datastore.directory,
      node : datastore.directory,
      option : 'gridDocuments',
      action : 'expandNode',
      sendWhat : datastore.sendWhat
    }
  });
}

function rowContextMenu(grid, rowIndex, e, f) {
  // console.log("Context menu: "+grid+" - "+rowIndex);
  if (typeof e == 'object') {
    e.preventDefault();
  } else {
    e = f;
  }
  gsm = ext_itemgrid.getSelectionModel();
  gsm.clickedRow = rowIndex;
  var selections = gsm.getSelections();

  if (selections.length > 1) {
//    gridCtxMenu.items.get('gc_delete').enable();
    gridCtxMenu.items.get('gc_delete')[  permitodelete==1 ? 'enable': 'disable']();
    gridCtxMenu.items.get('gc_rename').disable();
    gridCtxMenu.items.get('gc_download').disable();
  } else if (selections.length == 1) {
    gridCtxMenu.items.get('gc_delete')[  permitodelete==1 ? 'enable': 'disable']();
//    gridCtxMenu.items.get('gc_delete')[selections[0].get('is_deletable') ? 'enable': 'disable']();
    gridCtxMenu.items.get('gc_rename')[selections[0].get('is_deletable') ? 'disable': 'disable']();
    gridCtxMenu.items.get('gc_download')[selections[0].get('is_readable')
    && selections[0].get('is_file') ? 'enable' : 'disable']();
  }
  gridCtxMenu.show(e.getTarget(), 'tr-br?');

}
gridCtxMenu = new Ext.menu.Menu({
  id : 'gridCtxMenu',

  items : [ {
    id : 'gc_rename',
    iconCls: 'button_menu_ext ss_sprite ss_textfield_rename',// icon :
    hidden : true,															// '/images/documents/_fonts.png',
    text : TRANSLATIONS.ID_RENAME,
    handler : function() {
      ext_itemgrid.onCellDblClick(ext_itemgrid, gsm.clickedRow, 0);
      gsm.clickedRow = null;
    }
  }, /*{
    id : 'gc_copy',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_copy',// icon :
    // '/images/documents/_editcopy.png',
    text : TRANSLATIONS.ID_COPY,
    handler : function() {
      openActionDialog(this, 'copyAction');
    }
  }, {
    id : 'gc_move',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_go',// icon :
    // '/images/documents/_move.png',
    text : TRANSLATIONS.ID_MOVE,
    handler : function() {
      openActionDialog(this, 'moveAction');
    }
  },*/ {
    id : 'gc_delete',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_delete',// icon :
    // '/images/documents/_editdelete.png',
    text : TRANSLATIONS.ID_DELETE,
    handler : function() {
      openActionDialog(this, 'delete');
//      openActionDialog(this, 'deleteDocument');

    }
  }, '-', {
    id : 'gc_download',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_put',// icon :
    // '/images/documents/_down.png',
    text : TRANSLATIONS.ID_DOWNLOAD,
    handler : function() {
      openActionDialog(this, 'download');
    }
  },

  '-', {
    id : 'cancel',
    iconCls: 'button_menu_ext ss_sprite ss_cross',// icon :
    // '/images/documents/_cancel.png',
    text : TRANSLATIONS.ID_CANCEL,
    handler : function() {
      gridCtxMenu.hide();
    }
  } ]
});
//function that used for measure the  permissions and so assign buttons.
function revisePermission(){

  dirCtxMenu.items.get('dirCtxMenu_reload').hide();
  gridCtxMenu.items.get('cancel').hide();
  dirCtxMenu.items.get('dirCtxMenu_cancel').hide();
  if(permitoaddfolder=='1'){
    gridtb.items.get('tb_new').show();
   // tb.items.get('tb_new').enable();
    //dirCtxMenu.items.get('dirCtxMenu_new').enable();
  }
  else{
    gridtb.items.get('tb_new').hide();

   // tb.items.get('tb_new').disable();
    //dirCtxMenu.items.get('dirCtxMenu_new').disable();
  }


  if(permitodelete=='1') {
    gridtb.items.get('tb_delete').show();
   // tb.items.get('tb_delete').enable();
   // dirCtxMenu.items.get('dirCtxMenu_remove').enable();
  }
  else {
    gridtb.items.get('tb_delete').hide();

   // tb.items.get('tb_delete').disable();
   // dirCtxMenu.items.get('dirCtxMenu_remove').disable();
  }


  if(permitoaddfile=='1')
    gridtb.items.get('tb_upload').show();
  else
    gridtb.items.get('tb_upload').hide();


};

function dirContext(node, e) {
  // console.log("Dir context menu: "+node);
  // Select the node that was right clicked
  node.select();
  // Unselect all files in the grid
  ext_itemgrid.getSelectionModel().clearSelections();

  dirCtxMenu.items.get('dirCtxMenu_rename')[node.attributes.is_deletable ? 'disable': 'disable']();
//  dirCtxMenu.items.get('dirCtxMenu_remove')[node.attributes.is_deletable ? 'enable':'disable']();
  dirCtxMenu.items.get('dirCtxMenu_remove')[permitodelete==1 && node.attributes.id!='root' ? 'show':'hide']();

//  dirCtxMenu.items.get('dirCtxMenu_new')[node.attributes.id!='NA' ? 'enable':'disable']();
  dirCtxMenu.items.get('dirCtxMenu_new')[permitoaddfolder==1 ? 'show':'hide']();
  dirCtxMenu.items.get('dirCtxMenu_copy')[node.attributes.id!='NA' ? 'enable':'disable']();
  dirCtxMenu.items.get('dirCtxMenu_move')[node.attributes.id!='NA' ? 'enable'
  : 'disable']();
//  dirCtxMenu.items.get('dirCtxMenu_remove')[node.attributes.id!='NA' ? 'enable': 'disable']();

  dirCtxMenu.node = node;
  dirCtxMenu.show(e.getTarget(), 't-b?');

}

function copymove(action) {
  var s = dropEvent.data.selections, r = [];
  if (s) {
    // Dragged from the Grid
    requestParams = getRequestParams();
    requestParams.new_dir = dropEvent.target.id.replace(/_RRR_/g, '/');
    requestParams.new_dir = requestParams.new_dir.replace(/ext_root/g, '');
    requestParams.confirm = 'true';
    requestParams.action = action;
    handleCallback(requestParams);
  } else {
    // Dragged from inside the tree
    // alert('Move ' + dropEvent.data.node.id.replace( /_RRR_/g, '/' )+' to
    // '+ dropEvent.target.id.replace( /_RRR_/g, '/' ));
    requestParams = getRequestParams();
    requestParams.dir = datastore.directory.substring(0,
      datastore.directory.lastIndexOf('/'));
    requestParams.new_dir = dropEvent.target.id.replace(/_RRR_/g, '/');
    requestParams.new_dir = requestParams.new_dir.replace(/ext_root/g, '');
    requestParams.selitems = Array(dropEvent.data.node.id.replace(/_RRR_/g,
      '/'));
    requestParams.confirm = 'true';
    requestParams.action = action;
    handleCallback(requestParams);
  }
}
// context menus
var dirCtxMenu = new Ext.menu.Menu(
{
  id : 'dirCtxMenu',
  items : [
  {
    id : 'dirCtxMenu_new',
    iconCls: 'button_menu_ext ss_sprite ss_folder_add',// icon
    // :
    // '/images/documents/_folder_new.png',
    text : TRANSLATIONS.ID_NEW_FOLDER,
    handler : function() {
      dirCtxMenu.hide();
      openActionDialog(this, 'newFolder');
    }
  },
  {
    id : 'dirCtxMenu_rename',
    iconCls: 'button_menu_ext ss_sprite ss_textfield_rename',// icon
    // :
    hidden: true,															// '/images/documents/_fonts.png',
    text : TRANSLATIONS.ID_RENAME,
    handler : function() {
      dirCtxMenu.hide();
      openActionDialog(this, 'rename');
    }
  },
  {
    id : 'dirCtxMenu_copy',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_copy',// icon :
    // '/images/documents/_editcopy.png',

    text : TRANSLATIONS.ID_COPY,
    handler : function() {
      dirCtxMenu.hide();
      openActionDialog(this, 'copyAction');
    }
  },
  {
    id : 'dirCtxMenu_move',
    iconCls: 'button_menu_ext ss_sprite ss_folder_go',// icon
    // :
    // '/images/documents/_move.png',
    text : TRANSLATIONS.ID_MOVE,
    handler : function() {
      dirCtxMenu.hide();
      openActionDialog(this, 'moveAction');
    }
  },
  {
    id : 'dirCtxMenu_remove',
    iconCls: 'button_menu_ext ss_sprite ss_folder_delete',// icon
    // :
    // '/images/documents/_editdelete.png',

    text : TRANSLATIONS.ID_DELETE,
    handler : function() {
      dirCtxMenu.hide();
      var num = 1;
      Ext.Msg
      .confirm(
        TRANSLATIONS.ID_CONFIRM,
        String
        .format(
          TRANSLATIONS.ID_DELETE_SELECTED_ITEMS,
          num),
        function(btn) {
          deleteDir(btn, dirCtxMenu.node);
        });
    }
  }, '-', {
    id : 'dirCtxMenu_reload',
    iconCls: 'button_menu_ext ss_sprite ss_arrow_refresh',// icon
    // :
    // '/images/documents/_reload.png',
    text : TRANSLATIONS.ID_REFRESH_LABEL,
    handler : function() {
      dirCtxMenu.hide();
      dirCtxMenu.node.reload();
    }
  },   {
    id : 'dirCtxMenu_cancel',
    iconCls: 'button_menu_ext ss_sprite ss_cross',// icon
    // :
    // '/images/documents/_cancel.png',
    text : TRANSLATIONS.ID_CANCEL,
    handler : function() {
      dirCtxMenu.hide();
    }
  } ]
});
var copymoveCtxMenu = new Ext.menu.Menu({
  id : 'copyCtx',
  items : [ {
    id : 'copymoveCtxMenu_copy',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_copy',// icon :
    // '/images/documents/_editcopy.png',
    text : TRANSLATIONS.ID_COPY,
    handler : function() {
      copymoveCtxMenu.hide();
      copymove('copyExecute');
    }
  }, {
    id : 'copymoveCtxMenu_move',
    iconCls: 'button_menu_ext ss_sprite ss_page_white_go',// icon :
    // '/images/documents/_move.png',
    text : TRANSLATIONS.ID_MOVE,
    handler : function() {
      copymoveCtxMenu.hide();
      copymove('moveExecute');
    }
  }, '-', {
    id : 'copymoveCtxMenu_cancel',
    iconCls: 'button_menu_ext ss_sprite ss_cross',// icon :
    // '/images/documents/_cancel.png',
    text : TRANSLATIONS.ID_CANCEL,
    handler : function() {
      copymoveCtxMenu.hide();
    }
  } ]
});

function copymoveCtx(e) {
  /*ctxMenu.items.get('remove')[node.attributes.allowDelete ? 'enable' :
  'disable']();
  copymoveCtxMenu.showAt(e.rawEvent.getXY());
  copymoveCtxMenu.hide();*/
  copymove('moveExecute');
}

var documentsTab = {
  id : 'documents',
  // title : 'Documents',
  iconCls : 'ICON_FOLDERS',
  layout : 'border',
  region: 'center',
  defaults : {
    split : true
  },
  items : [
  {
    xtype : "treepanel",
    id : "dirTreePanel",
    region : "west",
    title : TRANSLATIONS.ID_DIRECTORY,
    closable : false,
    collapsible: true,
    collapseMode: 'mini',
    // collapsed:true,
    width : 180,
    titlebar : true,
    autoScroll : true,
    animate : true,
    tools:[
    {
        id: "refresh",
        handler: function () {
            //Ext.getCmp("dirTreePanel").getRootNode().reload();
            Ext.getCmp("dirTreePanel").setRootNode(rootNodeCreate());
        }
    }
    ],
    // rootVisible: false,
    loader : new Ext.tree.TreeLoader({
      preloadChildren : true,
      dataUrl : '../appFolder/appFolderAjax.php',
      baseParams : {
        action : 'expandNode',
        sendWhat : 'dirs',
        renderTree : 1
      }
    }),
    containerScroll : true,
    enableDD : true,
    ddGroup : 'TreeDD',
    listeners : {
      // "load": { fn: function(node) { chDir( node.id.replace(
      // /_RRR_/g, '/' ), true ); } },
      'contextmenu' : {
        fn : dirContext
      },
      'textchange' : {
        fn : function(node, text, oldText) {
          if (text == oldText)
            return true;
          var requestParams = getRequestParams();
          var dir = node.parentNode.id.replace(/_RRR_/g, '/');
          if (dir == 'root')
            dir = '';
          requestParams.dir = dir;
          requestParams.newitemname = text;
          requestParams.item = oldText;

          requestParams.confirm = 'true';
          requestParams.action = 'rename';
          handleCallback(requestParams);
          ext_itemgrid.stopEditing();
          return true;
        }
      },
      'beforenodedrop' : {
        fn : function(e) {
          dropEvent = e;
          copymoveCtx(e);
          datastore.reload();
        }
      },
      'beforemove' : {
        fn : function() {
          return false;
        }
      }
    },

    root: rootNodeCreate()
  },
  {
    layout : "border",
    region : "center",
    items : [
    {
      region : "north",
      xtype : "locationbar",
      id : "locationbarcmp",
      height : 28,
      tree : Ext.getCmp("dirTreePanel")
    },
    {
      // region : "center",
      // layout:'fit',
      // items : [ {
      region : "center",
      // xtype : "tabpanel",
      layout:'fit',
      id : "mainpanel",
      // autoHeight : true,
      // enableTabScroll : true,
      // activeTab : 0,
      // hideTabStripItem:0,
      items : [ {
        xtype : "editorgrid",
        layout:'fit',
        region : "center",
        // title : "Documents",
        // autoHeight : true,
        // autoScroll : true,
        // collapsible : false,
        // closeOnTab : true,
        id : "gridpanel",
        ds : datastore,
        cm : cm,
        tbar : gridtb,
        bbar : gridbb,
        ddGroup : 'TreeDD',
        enableDragDrop: true,
        plugins: expander,
        selModel : new Ext.grid.RowSelectionModel({
          listeners : {
            'rowselect' : {
              fn : handleRowClick
            },
            'selectionchange' : {
              fn : handleRowClick
            }
          }
        }),
        loadMask : true,
        keys : [
        {
          key : 'c',
          ctrl : true,
          stopEvent : true,
          handler : function() {
            openActionDialog(this,
              'copyAction');
          }

        },
        {
          key : 'x',
          ctrl : true,
          stopEvent : true,
          handler : function() {
            openActionDialog(this,
              'moveAction');
          }

        },
        {
          key : 'a',
          ctrl : true,
          stopEvent : true,
          handler : function() {
            ext_itemgrid
            .getSelectionModel()
            .selectAll();
          }
        },
        {
          key : Ext.EventObject.DELETE,
          handler : function() {
            openActionDialog(this,
              'delete');
          }
        } ],
        listeners : {
          'rowcontextmenu' : {
            fn : rowContextMenu
          },
          'celldblclick' : {
            fn : function(grid, rowIndex,
              columnIndex, e) {
              if (Ext.isOpera) {
                // because Opera <= 9
                // doesn't support the
                // right-mouse-button-clicked
                // event (contextmenu)
                // we need to simulate it
                // using the ondblclick
                // event
                rowContextMenu(grid,
                  rowIndex, e);
              } else {
                gsm = ext_itemgrid
                .getSelectionModel();
                gsm.clickedRow = rowIndex;
                var selections = gsm
                .getSelections();
                if (!selections[0]
                  .get('is_file')) {
                  // console.log(datastore.directory);
                  chDir(/*
																 * datastore.directory +
																 * "/"+
																 */selections[0]
                    .get('id'));
                } else if (selections[0]
                  .get('is_editable')) {
                  openActionDialog(this,
                    'edit');
                } else if (selections[0]
                  .get('is_readable')) {
                  openActionDialog(this,
                    'view');
                }
              }
            }
          },
          'validateedit' : {
            fn : function(e) {
              if (e.value == e.originalValue)
                return true;
              var requestParams = getRequestParams();
              requestParams.newitemname = e.value;
              requestParams.item = e.originalValue;

              requestParams.confirm = 'true';
              requestParams.action = 'rename';
              handleCallback(requestParams);
              return true;
            }
          }
        }

      } ]// another level

    // } /* jj */]
    }
    ]
  } ],

  listeners : {
    "afterlayout" : {
      fn : function() {
        revisePermission();
        // alert(Ext.getCmp("locationbarcmp"));
        // Ext.getCmp("documents").
        /*
						 * if(typeof(sw_afterlayout)!="undefined"){
						 * //console.log("starting locatiobar");
						 * Ext.getCmp("locationbarcmp").tree =
						 * Ext.getCmp("dirTreePanel");
						 * Ext.getCmp("locationbarcmp").initComponent();
						 * //console.log("location abr started"); return; }
						 */
        // console.log(typeof(sw_afterlayout));
        sw_afterlayout=true;

        ext_itemgrid = Ext.getCmp("gridpanel");

        // console.log("variable ext_itemgrid created");
        // console.trace();
        ext_itemgrid.un('celldblclick', ext_itemgrid.onCellDblClick);
        // console.log("celldoublde click removed");

        dirTree = Ext.getCmp("dirTreePanel");
        // console.log("dirtree created");

        /*
						 * dirTree.loader.on('load', function(loader, o,
						 * response ) { if( response && response.responseText ) {
						 * var json = Ext.decode( response.responseText ); if(
						 * json && json.error ) { Ext.Msg.alert('Error',
						 * json.error +'onLoad'); } } });
						 */

        var tsm = dirTree.getSelectionModel();
        // console.log("tried to gtet selection model");
        tsm.on('selectionchange',
          handleNodeClick);



        // create the editor for the directory
        // tree
        var dirTreeEd = new Ext.tree.TreeEditor(
          dirTree,
          {
            allowBlank : false,
            blankText : 'A name is required',
            selectOnFocus : true
          });
        // console.log("tree editor created");

        // console.log("before the first chdir");
        chDir('');
        // console.log("starting locatiobar first time");
        Ext.getCmp("locationbarcmp").tree = Ext.getCmp("dirTreePanel");
        Ext.getCmp("locationbarcmp").initComponent();
        var node = dirTree.getNodeById("root");
        node.select();
        datastore.directory = 'root';
      // console.log("location abr started first time");

      }

    }
  }

};
Ext.onReady(function() {

  var viewport = new Ext.Viewport({
    layout : 'border',
    items : [
    documentsTab ]
  });

  // console.info("viewport -end");

  viewport.doLayout();

  // routine to hide the debug panel if it is open
  if (parent.PANEL_EAST_OPEN) {
    parent.PANEL_EAST_OPEN = false;
    parent.Ext.getCmp('debugPanel').hide();
    parent.Ext.getCmp('debugPanel').ownerCt.doLayout();
  }

});
