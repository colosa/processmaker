<?php
/**
 * fields_Ajax.php
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

if (!isset($_SESSION['USER_LOGGED'])) {
  $aux1 = new stdclass();
  $aux2 = new stdclass();
  $aux2->type = 'hidden';
  $aux2->options = array();
  $aux1->name = 'PME_VALIDATE_NAME';
  $aux1->content = $aux2;
  $aux1->value = '__error_session__';
  $result = array();
  $result[] = array('name' => 'PME_VALIDATE_NAME',
                    'content' => array('type' => 'hidden',
                                       'options' => array(array('key' => '__error_session__',
                                                                'value' => '__error_session__'))),
                    'value' => '__error_session__');
  die(Bootstrap::json_encode($result));
}

if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
    /*NEXT LINE: Runs any configuration defined to be executed before dependent fields recalc*/
if (isset( $_SESSION['CURRENT_PAGE_INITILIZATION'] )) {
    eval( $_SESSION['CURRENT_PAGE_INITILIZATION'] );
}

$G_FORM = new Form( G::getUIDName( urlDecode( $_POST['form'] ) ) );
$G_FORM->id = urlDecode( $_POST['form'] );
$G_FORM->values = $_SESSION[$G_FORM->id];

$file = G::decrypt( $G_FORM->values['PME_A'], URL_KEY );
define( 'DB_XMLDB_HOST', PATH_DYNAFORM . $file . '.xml' );
define( 'DB_XMLDB_USER', '' );
define( 'DB_XMLDB_PASS', '' );
define( 'DB_XMLDB_NAME', '' );
define( 'DB_XMLDB_TYPE', 'myxml' );

$newValues = (Bootstrap::json_decode( urlDecode( stripslashes( $_POST['fields'] ) ) ));
//Resolve dependencies
//Returns an array ($dependentFields) with the names of the fields
//that depends of fields passed through AJAX ($_GET/$_POST)
$dependentFields = array ();
$aux = array ();
for ($r = 0; $r < sizeof( $newValues ); $r ++) {
    $newValues[$r] = (array) $newValues[$r];
    $G_FORM->setValues( $newValues[$r] );
    //Search dependent fields
    foreach ($newValues[$r] as $k => $v) {
        $myDependentFields = subDependencies( $k, $G_FORM, $aux );
        $dependentFields = array_merge( $dependentFields, $myDependentFields );
    }
}
$dependentFields = array_unique( $dependentFields );

//Parse and update the new content
$template = PATH_CORE . 'templates/xmlform.html';
$newContent = $G_FORM->getFields( $template );

//Returns the dependentFields's content
$sendContent = array ();
$r = 0;
foreach ($dependentFields as $d) {
    $sendContent[$r] = new stdclass();
    $sendContent[$r]->name = $d;
    $sendContent[$r]->content = new stdclass();
    foreach ($G_FORM->fields[$d] as $attribute => $value) {
        switch ($attribute) {
            case 'type':
                $sendContent[$r]->content->{$attribute} = $value;
                break;
            case 'options':
                $sendContent[$r]->content->{$attribute} = toJSArray( $value );
                break;
        }
    }
    $sendContent[$r]->value = $G_FORM->values[$d];
    $r ++;
}
echo (Bootstrap::json_encode( $sendContent ));

function toJSArray ($array)
{
    $result = array ();
    foreach ($array as $k => $v) {
        $o = new stdclass();
        $o->key = $k;
        $o->value = $v;
        $result[] = $o;
    }
    return $result;
}

function subDependencies ($k, &$G_FORM, &$aux)
{
    if (array_search( $k, $aux ) !== false) {
        return array ();
    }
    if (! array_key_exists( $k, $G_FORM->fields )) {
        return array ();
    }
    if (! isset( $G_FORM->fields[$k]->dependentFields )) {
        return array ();
    }
    $aux[] = $k;
    $myDependentFields = explode( ',', $G_FORM->fields[$k]->dependentFields );
    for ($r = 0; $r < sizeof( $myDependentFields ); $r ++) {
        if ($myDependentFields[$r] == "") {
            unset( $myDependentFields[$r] );
        }
    }
    $mD = $myDependentFields;
    foreach ($mD as $ki) {
        $myDependentFields = array_merge( $myDependentFields, subDependencies( $ki, $G_FORM, $aux ) );
    }
    return $myDependentFields;
}

