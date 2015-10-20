<?php
/**
 * main.php Cases List main processor
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

//$oHeadPublisher = & headPublisher::getSingleton();
global $RBAC;
$RBAC->requirePermissions( 'PM_FACTORY' );

G::loadClass( 'configuration' );
$conf = new Configurations();

if (preg_match("/^([\d\.]+).*$/", System::getVersion(), $arrayMatch)) {
    $pmVersion = $arrayMatch[1];
} else {
    $pmVersion = ""; //Branch master
}

$arrayImportFileExtension = array("pm", "pmx", "bpmn");
$arrayMenuNewOption       = array("pm" => true, "bpmn" => true);

/*options menu*/
$arrayMenuNew = array();

$mnuNewBpmnProject = new stdClass();
$mnuNewBpmnProject->text = G::LoadTranslation("ID_NEW_BPMN_PROJECT");
$mnuNewBpmnProject->iconCls = "silk-add";
$mnuNewBpmnProject->icon = "";
$mnuNewBpmnProject->newProcessType = 'newProcess({type:"bpmnProject",title:"'.$mnuNewBpmnProject->text.'"})';

$mnuNewProject = new stdClass();
$mnuNewProject->text = G::LoadTranslation("ID_NEW_PROJECT");
$mnuNewProject->iconCls = "silk-add";
$mnuNewProject->icon = "";
$mnuNewProject->newProcessType = 'newProcess({type:"classicProject",title:"'.$mnuNewProject->text.'"})';

$menuOption = array("pm" => $mnuNewProject, "bpmn" => $mnuNewBpmnProject);

foreach($arrayMenuNewOption as $type => $val) {
    if($val) {
        array_push($arrayMenuNew, $menuOption[$type]);
    }    
}
/*right click menu*/
$contexMenuRightClick = array(
    (object)array(
        "text" => G::LoadTranslation("ID_EDIT"),
        "iconCls" => "button_menu_ext ss_sprite  ss_pencil",
        "handler" => "editProcess()"
    ),
    (object)array(
        "id" => "activator2",
        "text" => "",
        "icon" => "",
        "handler" => "activeDeactive()"
    ),
    (object)array(
        "id" => "debug",
        "text" => "",
        "handler" => "enableDisableDebug()"
    ),
    (object)array(
        "text" => G::LoadTranslation("ID_DELETE"),
        "iconCls" => "button_menu_ext ss_sprite ss_cross",
        "handler" => "deleteProcess()"
    ),
    (object)array(
        "text" => G::LoadTranslation("ID_EXPORT"),
        "icon" => "/images/export.png",
        "handler" => "exportProcess()"
    ),
    (object)array(
        "id" => "mnuGenerateBpmn",
        "text" => G::LoadTranslation("ID_GENERATE_BPMN_PROJECT"),
        "iconCls" => "button_menu_ext ss_sprite ss_page_white_go",
        "hidden" => true,
        "handler" => "generateBpmn()"
    )
);
/*end right click menu*/
/*get registered options from plugin*/
$oPluginRegistry =& PMPluginRegistry::getSingleton();
$fromPlugin = $oPluginRegistry->getDesignerNewOption();

$jsFromPlugin = false;
foreach($fromPlugin as $menuOptionFile) {
    $menuOptionsFromPlugin = include_once($menuOptionFile->menuOptionFile);
    if(isset($menuOptionsFromPlugin)) {
        if(is_array($menuOptionsFromPlugin) && sizeof($menuOptionsFromPlugin)) {
            if(is_array($menuOptionsFromPlugin[0]) && sizeof($menuOptionsFromPlugin[0])) {
                $arrayMenuNew = array_merge($arrayMenuNew,$menuOptionsFromPlugin[0]);
            }
            if(is_array($menuOptionsFromPlugin[1]) && sizeof($menuOptionsFromPlugin[1])) {
                $contexMenuRightClick = array_merge($contexMenuRightClick,$menuOptionsFromPlugin[1]);
            }
            if(isset($menuOptionsFromPlugin[2])) {
                if(file_exists(PATH_PLUGINS.implode("/",array_slice(explode("/",$menuOptionsFromPlugin[2]),2)))) {
                    $jsFromPlugin = $menuOptionsFromPlugin[2];
                }
            }
        }    
    }
} 
/*end get registered options from plugin*/
/*end options menu*/
if ($pmVersion != "") {
    $arrayImportFileExtension = (version_compare($pmVersion . "", "3", ">="))? $arrayImportFileExtension : array("pm");
    $arrayMenuNewOption       = (version_compare($pmVersion . "", "3", ">="))? array("bpmn" => true) : array("pm" => true);
}

$oHeadPublisher->addExtJsScript( 'processes/main', true ); //adding a javascript file .js
$oHeadPublisher->addContent( 'processes/main' ); //adding a html file  .html.

$partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;
$oHeadPublisher->assign( 'PARTNER_FLAG', $partnerFlag );
$oHeadPublisher->assign( 'pageSize', $conf->getEnvSetting( 'casesListRowNumber' ) );
$oHeadPublisher->assign("arrayImportFileExtension", $arrayImportFileExtension);
$oHeadPublisher->assign("arrayMenuNewOption", $arrayMenuNewOption);

$oHeadPublisher->assign("arrayMenuNew", $arrayMenuNew);
$oHeadPublisher->assign("contexMenu", $contexMenuRightClick);
$oHeadPublisher->assign("jsFromPlugin", $jsFromPlugin);

G::RenderPage( 'publish', 'extJs' );
