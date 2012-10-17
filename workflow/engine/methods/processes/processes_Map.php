<?php
/**
 * processes_Map.php
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
global $RBAC;
$access = $RBAC->userCanAccess( 'PM_FACTORY' );
if ($access != 1) {
    switch ($access) {
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
}
$processUID = $_GET['PRO_UID'];

$_SESSION['PROCESS'] = $processUID;
$_SESSION['PROCESSMAP'] = 'LEIMNUD';

G::LoadClass( 'processMap' );

$oTemplatePower = new TemplatePower( PATH_TPL . 'processes/processes_Map.html' );
$oTemplatePower->prepare();

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_SUB_MENU = 'processes';
$G_ID_SUB_MENU_SELECTED = '_';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile( '/jscore/dbConnections/main.js' );
$oHeadPublisher->addScriptCode( '
    var maximunX = ' . processMap::getMaximunTaskX( $processUID ) . ';
	var leimnud = new maborak();
	leimnud.make();
	leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
	leimnud.Package.Load("json",{Type:"file"});
	leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
	leimnud.Package.Load("processes_Map",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processes_Map.js"});
	leimnud.Package.Load("stagesmap",{Type:"file",Absolute:true,Path:"/jscore/stagesmap/core/stagesmap.js"});
	leimnud.exec(leimnud.fix.memoryLeak);
	leimnud.event.add(window,"load",function(){
		var pb=leimnud.dom.capture("tag.body 0");
		Pm=new processmap();
		Pm.options={
			target		:"pm_target",
			dataServer	:"processes_Ajax.php",
			uid		:"' . $processUID . '",
			lang		:"' . SYS_LANG . '",
			theme		:"processmaker",
			size		:{w:pb.offsetWidth-10,h:pb.offsetHeight},
			images_dir	:"/jscore/processmap/core/images/"
		}
		Pm.make();
	});
	var changesSavedLabel = "' . addslashes( G::LoadTranslation( 'ID_SAVED_SUCCESSFULLY' ) ) . '";' );

if (! isset( $_GET['raw'] ))
    G::RenderPage( 'publish', 'green-submenu' );
else
    G::RenderPage( 'publish', 'raw' );
