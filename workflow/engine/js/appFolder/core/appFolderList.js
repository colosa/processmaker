function openPMFolder( uid, rootfolder ){

    currentFolder = uid;
    if((document.getElementById('child_'+uid).innerHTML!="")&&(uid!=rootfolder)){
      document.getElementById('child_'+uid).innerHTML="";
      getPMFolderContent(uid);
     return;
    }
    document.getElementById('child_'+uid).innerHTML = "<img src='/images/classic/loader_B.gif' >";


	var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : 'appFolderAjax',
      async : true,
      method: 'POST',
      args  : 'action=openPMFolder&folderID=' + uid+'&rootfolder='+rootfolder
    });
    oRPC.callback = function(rpc) {
        document.getElementById('child_'+uid).innerHTML = rpc.xmlhttp.responseText;
        var scs = rpc.xmlhttp.responseText.extractScript();
        scs.evalScript();

        getPMFolderContent(uid);
    }.extend(this);
    oRPC.make();

  if(uid==rootfolder){//Only refresh tags cloud if we are loading the root folder
    getPMFolderTags(rootfolder);
  }
}

function getPMFolderContent(uid){
  document.getElementById('spanFolderContent').innerHTML = "<img src='/images/classic/loader_B.gif' >";//"Loading..";
	var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : 'appFolderAjax',
      async : true,
      method: 'POST',
      args  : 'action=getPMFolderContent&folderID=' + uid
    });
    oRPC.callback = function(rpc) {
        document.getElementById('spanFolderContent').innerHTML = oRPC.xmlhttp.responseText;
        var scs = oRPC.xmlhttp.responseText.extractScript();
        scs.evalScript();


    }.extend(this);
    oRPC.make();



}

function getPMFolderSearchResult(searchKeyword,type){
	  document.getElementById('spanFolderContent').innerHTML = "<img src='/images/classic/loader_B.gif' >";//"Loading..";
		var oRPC = new leimnud.module.rpc.xmlhttp({
	      url   : 'appFolderAjax',
	      async : true,
	      method: 'POST',
	      args  : 'action=getPMFolderContent&searchKeyword=' + searchKeyword + '&type=' + type
	    });
	    oRPC.callback = function(rpc) {
	        document.getElementById('spanFolderContent').innerHTML = oRPC.xmlhttp.responseText;
	        var scs = oRPC.xmlhttp.responseText.extractScript();
	        scs.evalScript();


	    }.extend(this);
	    oRPC.make();



	}

function getPMFolderTags(rootfolder){
  document.getElementById('tags_cloud').innerHTML = "<img src='/images/classic/loader_B.gif' >";//"Loading..";
	var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : 'appFolderAjax',
      async : false,
      method: 'POST',
      args  : 'action=getPMFolderTags&rootFolder=' + rootfolder
    });
    oRPC.make();
    document.getElementById('tags_cloud').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();

}

var uploadDocument = function(docID,appDocId,docVersion,actionType,appId,docType){
    if(actionType){
        if(actionType=="R"){
            windowTitle=G_STRINGS.ID_UPLOAD_REPLACE_INPUT;
        }
        if(actionType=="NV"){
            windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT_VERSION;
        }
    }else{
        windowTitle=G_STRINGS.ID_UPLOAD_NEW_INPUT;
        docVersion=1;
        actionType="";
        appDocId="";
    }
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:550,h:300},
  	position:{x:0,y:0,center:true},
  	title	:windowTitle,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'appFolderAjax',
  	args: "action=uploadDocument&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType+"&appId="+appId+"&docType="+docType
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var oPanel;
var gUSER_UID;
var uploadExternalDocument = function(folderID){
  gUSER_UID = folderID;
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:550,h:300},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_UPLOAD_EXTERNAL_DOCUMENT,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'appFolderAjax',
  	args: "action=uploadExternalDocument&folderID="+folderID
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var oPanel;
var gUSER_UID;
var newFolder = function(folderID){
  gUSER_UID = folderID;    
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:550,h:250},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_NEW_FOLDER,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'appFolderAjax',
  	args: "action=newFolder&folderID="+folderID
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var documentVersionHistory = function(folderID,appDocId){
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:550,h:300},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_INPUT_DOCUMENT_HISTORY,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'appFolderAjax',
  	args: "action=documentVersionHistory&folderID="+folderID+"&appDocId="+appDocId
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var documentInfo = function(docID,appDocId,docVersion,actionType,appId,docType,usrUid){
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:400,h:270},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_DOCUMENT_INFO,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'appFolderAjax',
  	args: "action=documentInfo&docID="+docID+"&appDocId="+appDocId+"&docVersion="+docVersion+"&actionType="+actionType+"&appId="+appId+"&docType="+docType+"&usrUid="+usrUid
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var documentdelete = function(docID,appDocId,docVersion,actionType,appId,docType,usrUid){
	new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'appFolderAjax',
        async : true,
        method:'POST',
        args: 'action=documentdelete&sFileUID='+appDocId+'&docVersion='+docVersion
        
      });
      oRPC.callback = function(oRPC) {
        window.location = 'appFolderList';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
  
	
	//ajax_function('appFolderAjax','documentdelete','sFileUID='+encodeURIComponent(appDocId),'POST');
	//window.location = 'appFolderList';

};


function deletePMFolder( uid, rootfolder ){

  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'appFolderAjax',
        async : true,
        method:'POST',
        args: 'action=deletePMFolder&sFileUID='+uid+'&rootfolder='+rootfolder
        
      });
      oRPC.callback = function(oRPC) {
        window.location = 'appFolderList';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
  
}