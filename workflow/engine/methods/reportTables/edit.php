<?php

$id = isset( $_GET['id'] ) ? $_GET['id'] : false;
$table = false;
$oHeadPublisher = & headPublisher::getSingleton();

$oHeadPublisher->addExtJsScript( 'reportTables/edit', true );
$oHeadPublisher->assign( 'ADD_TAB_UID', $id );

if ($id) {
    // if is a edit request
    require_once 'classes/model/AdditionalTables.php';
    require_once 'classes/model/Fields.php';
    G::LoadClass( 'xmlfield_InputPM' );

    $additionalTables = new AdditionalTables();
    $table = $additionalTables->load( $id, true );
    $tableFields = array ();
    $fieldsList = array ();

    // list the case fields
    foreach ($table['FIELDS'] as $i => $field) {
        /*if ($field['FLD_NAME'] == 'APP_UID' || $field['FLD_NAME'] == 'APP_NUMBER' || $field['FLD_NAME'] == 'ROW') {
        unset($table['FIELDS'][$i]);
        continue;
        }*/
        array_push( $tableFields, $field['FLD_DYN_NAME'] );
    }

    //list dynaform fields
    if ($table['ADD_TAB_TYPE'] == 'NORMAL') {
        $fields = getDynaformsVars( $table['PRO_UID'], false );
        foreach ($fields as $field) {
            //select to not assigned fields for available grid
            if (! in_array( $field['sName'], $tableFields )) {
                $fieldsList[] = array ('FIELD_UID' => $field['sName'] . '-' . $field['sType'],'FIELD_NAME' => $field['sName']);
            }
        }
    } else {
        list ($gridName, $gridId) = explode( '-', $table['ADD_TAB_GRID'] );

        $G_FORM = new Form( $table['PRO_UID'] . '/' . $gridId, PATH_DYNAFORM, SYS_LANG, false );
        $gridFields = $G_FORM->getVars( false );

        foreach ($gridFields as $gfield) {
            if (! in_array( $gfield['sName'], $tableFields )) {
                $fieldsList[] = array ('FIELD_UID' => $gfield['sName'] . '-' . $gfield['sType'],'FIELD_NAME' => $gfield['sName']);
            }
        }
    }

    $oHeadPublisher->assign( 'avFieldsList', $fieldsList );
}

$repTabPluginPermissions = false;
global $G_TMP_MENU;
$oMenu = new Menu();
$oMenu->load( 'setup' );

foreach ($oMenu->Options as $i => $option) {
    if ($oMenu->Types[$i] == 'private' && $oMenu->Id[$i] == 'PLUGIN_REPTAB_PERMISSIONS') {
        $repTabPluginPermissions = array ();
        $repTabPluginPermissions['label'] = $oMenu->Labels[$i];
        $repTabPluginPermissions['fn'] = $oMenu->Options[$i];
        break;
    }
}

$oHeadPublisher->assign( '_plugin_permissions', $repTabPluginPermissions );

$oHeadPublisher->assign( 'PRO_UID', isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : false );
$oHeadPublisher->assign( 'TABLE', $table );

G::RenderPage( 'publish', 'extJs' );

