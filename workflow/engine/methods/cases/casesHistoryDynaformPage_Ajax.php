<?php
/**
 * casesHistoryDynaformPage_Ajax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

$actionAjax = isset( $_REQUEST['actionAjax'] ) ? $_REQUEST['actionAjax'] : null;

if ($actionAjax == "historyDynaformPage") {
    global $G_PUBLISH;
    $oHeadPublisher = & headPublisher::getSingleton();
    G::loadClass( 'configuration' );
    $conf = new Configurations();
    $oHeadPublisher->addExtJsScript( 'cases/caseHistoryDynaformPage', true ); //adding a javascript file .js
    $oHeadPublisher->addContent( 'cases/caseHistoryDynaformPage' ); //adding a html file  .html.
    $oHeadPublisher->assign( 'pageSize', $conf->getEnvSetting( 'casesListRowNumber' ) );
    G::RenderPage( 'publish', 'extJs' );
}
if ($actionAjax == 'historyDynaformGrid_Ajax') {
    G::LoadClass( 'case' );
    G::LoadClass( "BasePeer" );

    global $G_PUBLISH;
    $oCase = new Cases();

    $aProcesses = Array ();
    $c = $oCase->getallDynaformsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );

    if ($c->getDbName() == 'dbarray') {
        $rs = ArrayBasePeer::doSelectRs( $c );
    } else {
        $rs = GulliverBasePeer::doSelectRs( $c );
    }

    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();

    for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
        $result = $rs->getRow();
        //$result["ID_HISTORY"] = $result["PRO_UID"].'_'.$result["APP_UID"].'_'.$result["TAS_UID"];
        $aProcesses[] = $result;
        $rs->next();
    }

    $newDir = '/tmp/test/directory';
    $r = G::verifyPath( $newDir );
    $r->data = $aProcesses;
    $r->totalCount = 2;

    echo G::json_encode( $r );
}

if ($actionAjax == 'showHistoryMessage') {
    ?>
<link rel="stylesheet" type="text/css" href="/css/classic.css" />
<style type="text/css">
html {
	color: black !important;
}

body {
	color: black !important;
}
</style>
<script language="Javascript">
        //!Code that simulated reload library javascript maborak
        var leimnud = {};
        leimnud.exec = "";
        leimnud.fix = {};
        leimnud.fix.memoryLeak  = "";
        leimnud.browser = {};
        leimnud.browser.isIphone  = "";
        leimnud.iphone = {};
        leimnud.iphone.make = function(){};
        function ajax_function(ajax_server, funcion, parameters, method){
        }
        //!
      </script>
<?php

    G::LoadClass( 'case' );
    $oCase = new Cases();

    $_POST["APP_UID"] = $_REQUEST["APP_UID"];
    $_POST['APP_MSG_UID'] = $_REQUEST["APP_MSG_UID"];

    $G_PUBLISH = new Publisher();
    $oCase = new Cases();

    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_MessagesView', '', $oCase->getHistoryMessagesTrackerView( $_POST['APP_UID'], $_POST['APP_MSG_UID'] ) );

    ?>
<script language="javascript">
<?php
    global $G_FORM;
    ?>
          function loadForm_<?php echo $G_FORM->id;?>(parametro1){
          }
        </script>
<?php

    G::RenderPage( 'publish', 'raw' );
}

if ($actionAjax == 'showDynaformListHistory') {

    //!dataIndex
    $_POST["APP_UID"] = $_REQUEST["APP_UID"];
    $_POST["DYN_UID"] = $_REQUEST["DYN_UID"];
    $_POST["PRO_UID"] = $_REQUEST["PRO_UID"];
    $_POST["TAS_UID"] = $_REQUEST["TAS_UID"];

    ?>
<link rel="stylesheet" type="text/css" href="/css/classic.css" />
<style type="text/css">
html {
	color: black !important;
}

body {
	color: black !important;
}
</style>
<script language="Javascript">
        globalMd5Return=function(s,raw,hexcase,chrsz){raw=raw||false;hexcase=hexcase||false;chrsz=chrsz||8;function safe_add(x,y){var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF)}function bit_rol(num,cnt){return(num<<cnt)|(num>>>(32-cnt))}function md5_cmn(q,a,b,x,s,t){return safe_add(bit_rol(safe_add(safe_add(a,q),safe_add(x,t)),s),b)}function md5_ff(a,b,c,d,x,s,t){return md5_cmn((b&c)|((~b)&d),a,b,x,s,t)}function md5_gg(a,b,c,d,x,s,t){return md5_cmn((b&d)|(c&(~d)),a,b,x,s,t)}function md5_hh(a,b,c,d,x,s,t){return md5_cmn(b^c^d,a,b,x,s,t)}function md5_ii(a,b,c,d,x,s,t){return md5_cmn(c^(b|(~d)),a,b,x,s,t)}function core_md5(x,len){x[len>>5]|=0x80<<((len)%32);x[(((len+64)>>>9)<<4)+14]=len;var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;for(var i=0;i<x.length;i+=16){var olda=a;var oldb=b;var oldc=c;var oldd=d;a=md5_ff(a,b,c,d,x[i+0],7,-680876936);d=md5_ff(d,a,b,c,x[i+1],12,-389564586);c=md5_ff(c,d,a,b,x[i+2],17,606105819);b=md5_ff(b,c,d,a,x[i+3],22,-1044525330);a=md5_ff(a,b,c,d,x[i+4],7,-176418897);d=md5_ff(d,a,b,c,x[i+5],12,1200080426);c=md5_ff(c,d,a,b,x[i+6],17,-1473231341);b=md5_ff(b,c,d,a,x[i+7],22,-45705983);a=md5_ff(a,b,c,d,x[i+8],7,1770035416);d=md5_ff(d,a,b,c,x[i+9],12,-1958414417);c=md5_ff(c,d,a,b,x[i+10],17,-42063);b=md5_ff(b,c,d,a,x[i+11],22,-1990404162);a=md5_ff(a,b,c,d,x[i+12],7,1804603682);d=md5_ff(d,a,b,c,x[i+13],12,-40341101);c=md5_ff(c,d,a,b,x[i+14],17,-1502002290);b=md5_ff(b,c,d,a,x[i+15],22,1236535329);a=md5_gg(a,b,c,d,x[i+1],5,-165796510);d=md5_gg(d,a,b,c,x[i+6],9,-1069501632);c=md5_gg(c,d,a,b,x[i+11],14,643717713);b=md5_gg(b,c,d,a,x[i+0],20,-373897302);a=md5_gg(a,b,c,d,x[i+5],5,-701558691);d=md5_gg(d,a,b,c,x[i+10],9,38016083);c=md5_gg(c,d,a,b,x[i+15],14,-660478335);b=md5_gg(b,c,d,a,x[i+4],20,-405537848);a=md5_gg(a,b,c,d,x[i+9],5,568446438);d=md5_gg(d,a,b,c,x[i+14],9,-1019803690);c=md5_gg(c,d,a,b,x[i+3],14,-187363961);b=md5_gg(b,c,d,a,x[i+8],20,1163531501);a=md5_gg(a,b,c,d,x[i+13],5,-1444681467);d=md5_gg(d,a,b,c,x[i+2],9,-51403784);c=md5_gg(c,d,a,b,x[i+7],14,1735328473);b=md5_gg(b,c,d,a,x[i+12],20,-1926607734);a=md5_hh(a,b,c,d,x[i+5],4,-378558);d=md5_hh(d,a,b,c,x[i+8],11,-2022574463);c=md5_hh(c,d,a,b,x[i+11],16,1839030562);b=md5_hh(b,c,d,a,x[i+14],23,-35309556);a=md5_hh(a,b,c,d,x[i+1],4,-1530992060);d=md5_hh(d,a,b,c,x[i+4],11,1272893353);c=md5_hh(c,d,a,b,x[i+7],16,-155497632);b=md5_hh(b,c,d,a,x[i+10],23,-1094730640);a=md5_hh(a,b,c,d,x[i+13],4,681279174);d=md5_hh(d,a,b,c,x[i+0],11,-358537222);c=md5_hh(c,d,a,b,x[i+3],16,-722521979);b=md5_hh(b,c,d,a,x[i+6],23,76029189);a=md5_hh(a,b,c,d,x[i+9],4,-640364487);d=md5_hh(d,a,b,c,x[i+12],11,-421815835);c=md5_hh(c,d,a,b,x[i+15],16,530742520);b=md5_hh(b,c,d,a,x[i+2],23,-995338651);a=md5_ii(a,b,c,d,x[i+0],6,-198630844);d=md5_ii(d,a,b,c,x[i+7],10,1126891415);c=md5_ii(c,d,a,b,x[i+14],15,-1416354905);b=md5_ii(b,c,d,a,x[i+5],21,-57434055);a=md5_ii(a,b,c,d,x[i+12],6,1700485571);d=md5_ii(d,a,b,c,x[i+3],10,-1894986606);c=md5_ii(c,d,a,b,x[i+10],15,-1051523);b=md5_ii(b,c,d,a,x[i+1],21,-2054922799);a=md5_ii(a,b,c,d,x[i+8],6,1873313359);d=md5_ii(d,a,b,c,x[i+15],10,-30611744);c=md5_ii(c,d,a,b,x[i+6],15,-1560198380);b=md5_ii(b,c,d,a,x[i+13],21,1309151649);a=md5_ii(a,b,c,d,x[i+4],6,-145523070);d=md5_ii(d,a,b,c,x[i+11],10,-1120210379);c=md5_ii(c,d,a,b,x[i+2],15,718787259);b=md5_ii(b,c,d,a,x[i+9],21,-343485551);a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd)}return[a,b,c,d]}function str2binl(str){var bin=[];var mask=(1<<chrsz)-1;for(var i=0;i<str.length*chrsz;i+=chrsz){bin[i>>5]|=(str.charCodeAt(i/chrsz)&mask)<<(i%32)}return bin}function binl2str(bin){var str="";var mask=(1<<chrsz)-1;for(var i=0;i<bin.length*32;i+=chrsz){str+=String.fromCharCode((bin[i>>5]>>>(i%32))&mask)}return str}function binl2hex(binarray){var hex_tab=hexcase?"0123456789ABCDEF":"0123456789abcdef";var str="";for(var i=0;i<binarray.length*4;i++){str+=hex_tab.charAt((binarray[i>>2]>>((i%4)*8+4))&0xF)+hex_tab.charAt((binarray[i>>2]>>((i%4)*8))&0xF)}return str}return(raw?binl2str(core_md5(str2binl(s),s.length*chrsz)):binl2hex(core_md5(str2binl(s),s.length*chrsz)))};

        //!Code that simulated reload library javascript maborak
        var leimnud = {};
        leimnud.exec = "";
        leimnud.fix = {};
        leimnud.fix.memoryLeak  = "";
        leimnud.browser = {};
        leimnud.browser.isIphone  = "";
        leimnud.iphone = {};
        leimnud.iphone.make = function(){};
          function ajax_function(ajax_server, funcion, parameters, method){
          }

          function toggleTable(tablename){
            //table= document.getElementByName(tablename);
            table= document.getElementById(tablename);
            if(table.style.display == ''){
              table.style.display = 'none';
            }else{
              table.style.display = '';
            }
          }

          function noesFuncion(idIframe) {
            window.parent.tabIframeWidthFix2(idIframe);
          }

          function onResizeIframe(idIframe){


            window.onresize = noesFuncion(idIframe);

          }

        var showDynaformHistoryGlobal = {};
        showDynaformHistoryGlobal.dynUID = '';
        showDynaformHistoryGlobal.tablename = '';
        showDynaformHistoryGlobal.dynDate = '';
        showDynaformHistoryGlobal.dynTitle = '';
          function showDynaformHistory(dynUID,tablename,dynDate,dynTitle){
            showDynaformHistoryGlobal.dynUID = dynUID;
            showDynaformHistoryGlobal.tablename = tablename;
            showDynaformHistoryGlobal.dynDate = dynDate;
            showDynaformHistoryGlobal.dynTitle = dynTitle;

            var dynUID = showDynaformHistoryGlobal.dynUID;
            var tablename = showDynaformHistoryGlobal.tablename;
            var dynDate = showDynaformHistoryGlobal.dynDate;
            var dynTitle = showDynaformHistoryGlobal.dynTitle;

            var idUnique = globalMd5Return(dynUID+tablename+dynDate+dynTitle);

            var tabData =  window.parent.Ext.util.JSON.encode(showDynaformHistoryGlobal);
            var tabName = 'dynaformChangeLogViewHistory'+idUnique;
            var tabTitle = 'View('+dynTitle+' '+dynDate+')';

            window.parent.ActionTabFrameGlobal.tabData = tabData;
            window.parent.ActionTabFrameGlobal.tabName = tabName;
            window.parent.ActionTabFrameGlobal.tabTitle = tabTitle;

            window.parent.Actions.tabFrame(tabName);
          }
      </script>
<?php

    require_once 'classes/model/AppHistory.php';
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'view', 'cases/cases_DynaformHistory' );

    G::RenderPage( 'publish', 'raw' );
}

if ($actionAjax == 'dynaformChangeLogViewHistory') {

    ?>
<link rel="stylesheet" type="text/css" href="/css/classic.css" />
<style type="text/css">
html {
	color: black !important;
}

body {
	color: black !important;
}
</style>
<script language="Javascript">



        //!Code that simulated reload library javascript maborak
        var leimnud = {};
        leimnud.exec = "";
        leimnud.fix = {};
        leimnud.fix.memoryLeak  = "";
        leimnud.browser = {};
        leimnud.browser.isIphone  = "";
        leimnud.iphone = {};
        leimnud.iphone.make = function(){};
        function ajax_function(ajax_server, funcion, parameters, method){
        }
        //!
      </script>
<?php

    $_POST['DYN_UID'] = $_REQUEST['DYN_UID'];
    $_POST['HISTORY_ID'] = $_REQUEST['HISTORY_ID'];

    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    $FieldsHistory = unserialize( $_SESSION['HISTORY_DATA'] );
    $Fields['APP_DATA'] = $FieldsHistory[$_POST['HISTORY_ID']];
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'return false;';
    $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_POST['DYN_UID'], '', $Fields['APP_DATA'], '', '', 'view' );

    ?>
<script language="javascript">
<?php
    global $G_FORM;
    ?>
          function loadForm_<?php echo $G_FORM->id;?>(parametro1){

          }
        </script>
<?php

    G::RenderPage( 'publish', 'raw' );

}
if ($actionAjax == 'historyDynaformGridPreview') {
    ?>
<link rel="stylesheet" type="text/css" href="/css/classic.css" />
<style type="text/css">
html {
	color: black !important;
}

body {
	color: black !important;
}
</style>
<script language="Javascript">



        //!Code that simulated reload library javascript maborak
        var leimnud = {};
        leimnud.exec = "";
        leimnud.fix = {};
        leimnud.fix.memoryLeak  = "";
        leimnud.browser = {};
        leimnud.browser.isIphone  = "";
        leimnud.iphone = {};
        leimnud.iphone.make = function(){};
        function ajax_function(ajax_server, funcion, parameters, method){
        }
        //!
      </script>
<?php

    //!dataIndex
    $_POST["DYN_UID"] = $_REQUEST["DYN_UID"];

    G::LoadClass( 'case' );

    $G_PUBLISH = new Publisher();
    $oCase = new Cases();
    $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'return false;';
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['DYNUIDPRINT'] = $_POST['DYN_UID'];

    $_SESSION['DYN_UID_PRINT'] = $_POST['DYN_UID'];
    $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_POST['DYN_UID'], '', $Fields['APP_DATA'], '', '', 'view' );

    ?>
<script language="javascript">
function popUp(URL, width, height, left, top, resizable) {
    window.open(URL, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable='+resizable+',width='+width+',height='+height+',left = '+left+',top = '+top+'');
}
<?php
    global $G_FORM;
    ?>
function loadForm_<?php echo $G_FORM->id;?>(parametro1) {
}
</script>
<?php

    G::RenderPage( 'publish', 'raw' );
}

