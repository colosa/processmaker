<?php
/**
 * triggers_CreateWizard.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
require_once ('classes/model/Triggers.php');

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'view', 'triggers/triggers_CreateWizard', '', '', $_GET, '' );
G::RenderPage( 'publish', 'raw' );

?>


<script>



	var _oVarsPanel_;
var showDynaformsFormVars = function(sFieldName, sAjaxServer, sProcess, sSymbol) {
	 //sFieldName = document.getElementById('TRI_ANSWER');
	_oVarsPanel_ = new leimnud.module.panel();
	_oVarsPanel_.options = {
    limit    : true,
    size     : {w:600,h:420},
    position : {x:0,y:0,center:true},
    title    : '',
    theme    : 'processmaker',
    statusBar: false,
    control  : {drag:false,resize:true,close:true},
    fx       : {opacity:true,rolled:false,modal:true}
  };
  _oVarsPanel_.make();
  _oVarsPanel_.events = {
    remove:function() {
      delete _oVarsPanel_;
    }.extend(this)
  };
  _oVarsPanel_.loader.show();
  oRPC = new leimnud.module.rpc.xmlhttp({
    url   : sAjaxServer,
    method: 'POST',
    args  : 'sFieldName=' + sFieldName + '&sProcess=' + sProcess + '&sSymbol=' + sSymbol
  });
  oRPC.callback = function(oRPC) {
    _oVarsPanel_.loader.hide();
    var scs = oRPC.xmlhttp.responseText.extractScript();
    _oVarsPanel_.addContent(oRPC.xmlhttp.responseText);
    scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var insertFormVar = function(sFieldName, sValue) {
	oAux = document.getElementById(sFieldName);
	if (oAux.setSelectionRange) {
		var rangeStart = oAux.selectionStart;
    var rangeEnd   = oAux.selectionEnd;
    var tempStr1   = oAux.value.substring(0,rangeStart);
    var tempStr2   = oAux.value.substring(rangeEnd);
    oAux.value     = tempStr1 + sValue + tempStr2;
	}
	else {
	  if (document.selection) {
	    oAux.focus();
      document.selection.createRange().text = sValue;
	  }
	}
	_oVarsPanel_.remove();
};


</script>

