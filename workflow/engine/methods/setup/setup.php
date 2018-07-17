<?php
/**
 * setup.php
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

/**
 * New Admin User interface
 *
 * @author Erik A. O. <erik@colosa.com, aortiz.erik@gmail.com>
 * @date Apr 5th, 2010
 */


$filter = new InputFilter();
$_GET['i18'] = $filter->xssFilterHard($_GET['i18']);
$_GET['newSite'] = $filter->xssFilterHard($_GET['newSite']);

if (($RBAC_Response = $RBAC->userCanAccess( "PM_SETUP" )) != 1)
    return $RBAC_Response;

$G_MAIN_MENU = "processmaker";
$G_ID_MENU_SELECTED = "SETUP";
$G_PUBLISH = new Publisher();
global $G_TMP_MENU;
$oMenu = new Menu();
$oMenu->load( 'setup' );
$toolItems = Array ();

foreach ($oMenu->Options as $i => $option) {
    $toolItems[] = Array ('id' => $oMenu->Id[$i],'link' => ($oMenu->Options[$i] != '') ? $oMenu->Options[$i] : '#','onclick' => ($oMenu->JS[$i] != '') ? $oMenu->JS[$i] : '','label' => $oMenu->Labels[$i],'icon' => ($oMenu->Icons[$i] != '') ? $oMenu->Icons[$i] : 'icon-pmlogo.png','target' => ($oMenu->JS[$i] != '') ? '' : 'admToolsContent'
    );
}

$template = new TemplatePower( PATH_TPL . 'setup' . PATH_SEP . 'tools.html' );
$template->prepare();

$template->assign( 'LeftWidth', '230' );
$template->assign( 'contentHeight', '520' );

if (isset( $_GET['i18'] )) {
    $_SESSION['TOOLS_VIEWTYPE'] = true;
    $template->assign( 'displayLanguageTool', 'block' );
} else {
    $template->assign( 'displayLanguageTool', 'none' );
}
if (isset( $_GET['newSite'] )) {
    $template->assign( 'displayNewSiteTool', 'block' );
} else {
    $template->assign( 'displayNewSiteTool', 'none' );
}

foreach ($toolItems as $item) {
    $template->newBlock( 'tool_options' );
    foreach ($item as $propertyName => $propertyValue)
        $template->assign( $propertyName, $propertyValue );
}

$G_PUBLISH->AddContent( 'template', '', '', '', $template );
G::RenderPage( 'publish' );
if (isset( $_GET['module'] )) {
  $module = $filter->xssFilterHard($_GET['module']);
    print '
  <script>
  admToolsContent.location=\''.$module.'\';
  </script>
  ';
}

