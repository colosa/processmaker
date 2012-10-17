<?php
/**
 * control.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_FACTORY' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

$G_MAIN_MENU = 'processmaker';
//$G_SUB_MENU             = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
//$G_ID_SUB_MENU_SELECTED = 'WEBSERVICES';


if (! extension_loaded( 'soap' )) {
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsMessage' );
    G::RenderPage( "publish" );
} else {
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'view', 'setup/webServicesTree' );
    $G_PUBLISH->AddContent( 'smarty', 'groups/groups_usersList', '', '', array () );

    G::RenderPage( "publish-treeview", 'blank' );
}

$link_Edit = G::encryptlink( 'webServicesSetup' );
$link_List = G::encryptlink( 'webServicesList' );

?>
<script>
  document.body.style.backgroundColor="#fff";
  var oAux = document.getElementById("publisherContent[0]");
  oAux.id = "publisherContent[666]";
  var currentGroup=false;

  function webServicesSetup(){
    popupWindow('' , '<?php echo $link_Edit ?>' , 500 , 225 );
  }

  function showFormWS( uid, element ){

    currentGroup = uid;
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : false,
      method: 'POST',
      args  : 'action=showForm&wsID=' + uid
    });
    oRPC.make();
    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
    if ((uid == 'NewCase') || (uid == 'NewCaseImpersonate')) {
      var scs=oRPC.xmlhttp.responseText.extractScript();scs.evalScript();
    }
  }
  function execWebService( uid) {
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : true,
      method: 'POST',
      args  : 'action=execWebService&wsID=' + uid
    });

    oRPC.callback = function(rpc) {

	  	var scs = rpc.xmlhttp.responseText.extractScript();
	  	document.getElementById('spanUsersList').innerHTML = rpc.xmlhttp.responseText;
	  	scs.evalScript();

  	}.extend(this);

    oRPC.make();

  }

  submitThisForm = function(oForm) {
    var oAux;
    var bContinue = true;
    if(bContinue) {
	  result = ajax_post(oForm.action, oForm, 'POST', function(response){
		var scs = response.extractScript();
		document.getElementById('spanUsersList').innerHTML = response;
		scs.evalScript();
	  });
      refreshTree();
    }
  };




  function callbackWebService( ) {
/*
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : false,
      method: 'POST',
      args  : 'action=execWebService&wsID=' + uid
    });
    oRPC.make();
    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
*/
    document.getElementById('spanUsersList').innerHTML = 'hola';
  }
  function saveGroup( form ) {
    ajax_post( form.action, form, 'POST' );
    currentPopupWindow.remove();
    refreshTree();
  }

  function refreshTree(){
    tree.refresh( document.getElementById("publisherContent[666]") , '<?php echo $link_List ?>');
  }

  function showDetails(){
	  var oRPC = new leimnud.module.rpc.xmlhttp({
	      url   : '../setup/webServicesAjax',
	      async : false,
	      method: 'POST',
	      args  : 'action=showDetails'
	    });
	    oRPC.make();
	    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
  }
  showDetails();

  function showUploadFilesForm(){
     oIFrame = window.document.createElement('iframe');
     oIFrame.style.border = '0';
     oIFrame.style.width  = '700px';
     oIFrame.style.height = '400px';
     oIFrame.src          = 'webServicesAjax?action=showUploadFilesForm&';
     document.getElementById('spanUsersList').innerHTML = '';
     document.getElementById('spanUsersList').appendChild(oIFrame);
  }
</script>