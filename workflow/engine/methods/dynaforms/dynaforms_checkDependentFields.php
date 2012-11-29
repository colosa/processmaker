<?php

/**
 * dynaforms_checkDependentFields.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.23
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
 * this file is a fix to a dependency bug it was just a minor improvement,
 * also the functionality of dependent fields in grids doesn't depends in this
 * file so this is somewhat expendable.
 */
function subDependencies ($k, &$G_FORM, &$aux, $grid = '')
{
    $myDependentFields = '';
    if (array_search( $k, $aux ) !== false) {
        return array ();
    }
    if ($grid == '') {
        if (! array_key_exists( $k, $G_FORM->fields )) {
            return array ();
        }
        if (! isset( $G_FORM->fields[$k]->dependentFields )) {
            return array ();
        }
        $aux[] = $k;
        $mydependentFields = $G_FORM->fields[$k]->dependentFields;

    } else {
        if (! array_key_exists( $k, $G_FORM->fields[$grid]->fields )) {
            return array ();
        }
        if (! isset( $G_FORM->fields[$grid]->fields[$k]->dependentFields )) {
            return array ();
        }
        $myDependentFields = $G_FORM->fields[$grid]->fields[$k]->dependentFields;
        $myDependentFields = explode( ',', $G_FORM->fields[$grid]->fields[$k]->dependentFields );

    }
    return $myDependentFields;
}

if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
    // the script responds an ajax request in order to check the dependent fields,
    // and generate a json output of the values that the dependent field must have.
$sDynUid = G::getUIDName( urlDecode( $_POST['DYN_UID'] ) );
//$json = new Services_JSON();
$formValues = (Bootstrap::json_decode( $_POST['fields'] ));
$sFieldName = $_POST['fieldName'];
$sMasterField = '';
$sPath = PATH_DYNAFORM;
$G_FORM = new form( $sDynUid, $sPath );
$aux = array ();
$newValues = Bootstrap::json_decode( urlDecode( stripslashes( $_POST['form'] ) ) );

if (isset( $_POST['grid'] )) {
    $_POST['row'] = (int) $_POST['row'];
    $aAux = array ();
    foreach ($newValues as $sKey => $newValue) {
        $newValue = (array) $newValue;
        $aKeys = array_keys( $newValue );
        $aValues = array ();
        for ($i = 1; $i <= ($_POST['row'] - 1); $i ++) {
            $aValues[$i] = array ($aKeys[0] => ''
            );
        }
        $aValues[$_POST['row']] = array ($aKeys[0] => $newValue[$aKeys[0]] );
        $newValues[$sKey]->$_POST['grid'] = $aValues;
        unset( $newValues[$sKey]->$aKeys[0] );
    }
}

$dependentFields = array ();
$aux = array ();
$found = false;
for ($r = 0; $r < sizeof( $newValues ); $r ++) {
    $newValues[$r] = (array) $newValues[$r];
    $G_FORM->setValues( $newValues[$r] );
    //Search dependent fields
    foreach ($newValues[$r] as $k => $v) {
        if (! is_array( $v )) {
            $myDependentFields = subDependencies( $k, $G_FORM, $aux );
            if (! $found) {
                if (in_array( $sFieldName, $myDependentFields )) {
                    $sMasterField = $k;
                    $found = true;
                }
            }
            $_SESSION[$G_FORM->id][$k] = $v;
        } else {
            foreach ($v[$_POST['row']] as $k1 => $v1) {
                $myDependentFields = subDependencies( $k1, $G_FORM, $aux, $_POST['grid'] );
                if (! $found) {
                    if (in_array( $sFieldName, $myDependentFields )) {
                        $sMasterField = $k1;
                        $found = true;
                    }
                }
                $_SESSION[$G_FORM->id][$_POST['grid']][$_POST['row']][$k1] = $v1;
            }
        }
        $dependentFields = array_merge( $dependentFields, $myDependentFields );
    }
}
switch ($_POST['function']) {
    case 'showDependentFields':
        echo $json->encode( array_unique( $dependentFields ) );
        break;
    case 'showDependentOf':
        echo $sMasterField;
        break;
}

