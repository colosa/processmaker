<?php
/**
 * dynaforms_Editor.php
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
 /*
 * Created on 21/12/2007
 *
 */
G::LoadClass( 'dynaformEditor' );
G::LoadClass( 'toolBar' );
G::LoadClass( 'dynaFormField' );

//G::LoadClass('configuration');
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'processes';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = 'FIELDS';

$PRO_UID = isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : '0';
$DYN_UID = (isset( $_GET['DYN_UID'] )) ? urldecode( $_GET['DYN_UID'] ) : '0';
$_SESSION['PROCESS'] = $_GET['PRO_UID'];

if ($PRO_UID === '0') {
    return;
}
$process = new Process();
if ($process->exists( $PRO_UID )) {
    $process->load( $PRO_UID );
} else {
    //TODO
    print ("$PRO_UID doesn't exist, continue? yes") ;
}

$dynaform = new dynaform();

if ($dynaform->exists( $DYN_UID )) {
    $dynaform->load( $DYN_UID );
    $_SESSION['CURRENT_DYN_UID'] = $DYN_UID;
} else {
    /* New Dynaform
    *
    */
    $dynaform->create( array ('PRO_UID' => $PRO_UID) );
}

//creating SESSION for redirecting to new bpmn editor after closing Dynaform
if (isset( $_GET['bpmn'] ) && $_GET['bpmn'] == '1') {
    $_SESSION['dynaform_editor'] = 'bpmn';
} elseif (! isset( $_GET['bpmn'] )) {
    $_SESSION['dynaform_editor'] = 'processmap';
}

$editor = new dynaformEditor( $_POST );
$editor->file = $dynaform->getDynFilename();
$editor->home = PATH_DYNAFORM;
$editor->title = $dynaform->getDynTitle();
$editor->dyn_uid = $dynaform->getDynUid();
$editor->pro_uid = $dynaform->getProUid();
$editor->dyn_type = $dynaform->getDynType();
$editor->dyn_title = $dynaform->getDynTitle();
$editor->dyn_description = $dynaform->getDynDescription();
$editor->dyn_editor = $_SESSION['dynaform_editor'];
$editor->_setUseTemporalCopy( true );
$editor->_render();

