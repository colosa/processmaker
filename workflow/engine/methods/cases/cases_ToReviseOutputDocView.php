<?php
/**
 * cases_ToReviseOutputDocView.php
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

/* Permissions */
switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

/* Includes */
G::LoadClass( 'case' );

/* Menues */
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REVISE';

/* Prepare page before to show */
$oTemplatePower = new TemplatePower( PATH_TPL . 'cases/cases_Step.html' );
$oTemplatePower->prepare();
$G_PUBLISH = new Publisher();

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptCode( '
var Cse = {};
Cse.panels = {};
var leimnud = new maborak();
leimnud.make();
leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
leimnud.Package.Load("json",{Type:"file"});
leimnud.Package.Load("cases",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases.js"});
leimnud.Package.Load("cases_Step",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases_Step.js"});
leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
leimnud.exec(leimnud.fix.memoryLeak);
leimnud.event.add(window,"load",function(){
  ' . (isset( $_SESSION['showCasesWindow'] ) ? 'try{' . $_SESSION['showCasesWindow'] . '}catch(e){}' : '') . '});
' );
$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
//
require_once 'classes/model/OutputDocument.php';
$oOutputDocument = new OutputDocument();
$aOD = $oOutputDocument->load( $_GET['UID'] );
require_once 'classes/model/AppDocument.php';
$oAppDocument = new AppDocument();
$aFields = $oAppDocument->load( $_GET['DOC'] );
$aFields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
switch ($aOD['OUT_DOC_GENERATE']) {
    case 'DOC':
        $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
        break;
    case 'PDF':
        $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
        break;
    case 'BOTH':
        $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
        $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
        break;
}

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewOutputDocumentToRevise', '', G::array_merges( $aOD, $aFields ), '' );
//
G::RenderPage( 'publish', 'blank' );

if (! isset( $_GET['ex'] ))
    $_GET['ex'] = 0;

?>
<script type="text/javascript">
/*------------------------------ To Revise Routines ---------------------------*/
function setSelect()
{
  var ex=<?php echo $_GET['ex']?>;
  try{
    for(i=1; i<50; i++)
    {
      if(i == ex){
        document.getElementById('focus'+i).innerHTML = '<img src="/images/bulletButton.gif" />';
      }
      else{
        document.getElementById('focus'+i).innerHTML = '';
      }
    }
  } catch (e){
    return 0;
  }
}
</script>

