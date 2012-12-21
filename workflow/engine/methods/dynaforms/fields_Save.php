<?php

//print_r( $_POST); die;
/**
 * fields_Save.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
G::LoadClass('dynaformEditor');

if (($RBAC_Response = $RBAC->userCanAccess("PM_FACTORY")) != 1) {
    return $RBAC_Response;
}

//G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

G::LoadClass('dynaFormField');

$type = strtolower($_POST['form']['PME_TYPE']);
if (!(isset($_POST['form']['PME_A']) && $_POST['form']['PME_A'] !== '')) {
    return;
}

if (isset($_POST['form']['PME_REQUIRED'])) {
    if ($_POST['form']['PME_REQUIRED'] == '') {
        $_POST['form']['PME_REQUIRED'] = 0;
    }
} else {
    $_POST['form']['PME_REQUIRED'] = 0;
}

if (isset($_POST['form']['PME_READONLY'])) {
    if ($_POST['form']['PME_READONLY'] == '') {
        $_POST['form']['PME_READONLY'] = 0;
    }
} else {
    $_POST['form']['PME_READONLY'] = 0;
}

if (isset($_POST['form']['PME_SAVELABEL'])) {
    if ($_POST['form']['PME_SAVELABEL'] == '') {
        $_POST['form']['PME_SAVELABEL'] = 0;
    }
} else {
    $_POST['form']['PME_SAVELABEL'] = 0;
}
$A = $_POST['form']['PME_A'];
if (isset($_POST['form']['PME_SAVELABEL'])
        && isset($_POST['form']['PME_CODE'])
        && $_POST['form']['PME_TYPE'] === 'javascript') {
    $sType = $_POST['form']['PME_TYPE'];
    // $A         = $_POST['form']['PME_A'];
    $fieldName = $_POST['form']['PME_XMLNODE_NAME'];
    $pmeCode = $_POST['form']['PME_CODE'];
    $_POST['form']['PME_CODE'] = '';
    //    $pmeCode = str_replace("'", "''", $pmeCode);
    //    $pmeCode = str_replace('"', '""', $pmeCode);
    //    $pmeCode = preg_replace("/\)\s*\n/", ") //\n", $pmeCode);
    //    $_POST['form']['PME_CODE'] = $pmeCode;
}

$file = G::decrypt($_POST['form']['PME_A'], URL_KEY);
define('DB_XMLDB_HOST', PATH_DYNAFORM . $file . '.xml');
define('DB_XMLDB_USER', '');
define('DB_XMLDB_PASS', '');
define('DB_XMLDB_NAME', '');
define('DB_XMLDB_TYPE', 'myxml');

//  if (isset($_POST['form']['PME_XMLNODE_VALUE'])){
//    $_POST['form']['PME_XMLNODE_VALUE'] = str_replace("'", "''" , $_POST['form']['PME_XMLNODE_VALUE']);
//  }

if (file_exists(PATH_XMLFORM . 'dynaforms/fields/' . $type . '.xml')) {
    $form = new Form('dynaforms/fields/' . $type, PATH_XMLFORM);
    //TODO: Verify why validatePost removes PME_XMLGRID.
    $isGrid = isset($_POST['form']['PME_XMLGRID']);
    if ($isGrid) {
        $xmlGrid = $_POST['form']['PME_XMLGRID'];
    }
    //$form->validatePost();
    if ($isGrid) {
        $_POST['form']['PME_XMLGRID'] = $xmlGrid;
    }
    if ($type === 'checkbox') {
        // added by Gustavo Cruz
        if ($_POST['form']['PME_DEFAULTVALUE'] === "1") {
            $_POST['form']['PME_DEFAULTVALUE'] = $_POST['form']['PME_VALUE'];
        } else {
            $_POST['form']['PME_DEFAULTVALUE'] = $_POST['form']['PME_FALSEVALUE'];
        }
        // end added code
        // verify why $form->fields['PME_DEFAULTVALUE']->value doesn't capture the value 1
        //      if ($_POST['form']['PME_DEFAULTVALUE']===$form->fields['PME_DEFAULTVALUE']->value) {
        //        $_POST['form']['PME_DEFAULTVALUE']=$_POST['form']['PME_VALUE'];
        //      } else {
        //        $_POST['form']['PME_DEFAULTVALUE']=$_POST['form']['PME_FALSEVALUE'];
        //      }
    }
    if ($type === 'grid') {
        if (!isset($_POST['form']['PME_ADDROW']) || $_POST['form']['PME_ADDROW'] == '') {
            $_POST['form']['PME_ADDROW'] = '0';
        }
        if (!isset($_POST['form']['PME_DELETEROW']) || $_POST['form']['PME_DELETEROW'] == '') {
            $_POST['form']['PME_DELETEROW'] = '0';
        }
    }
    if ($type === 'dropdown' || $type === 'listbox') {
        if (isset($_POST['form']['PME_OPTIONS'][1]) && count($_POST['form']['PME_OPTIONS']) == 1) {
            if ($_POST['form']['PME_OPTIONS']['1']['NAME'] === "" &&
                    $_POST['form']['PME_OPTIONS']['1']['LABEL'] === "") {
                unset($_POST['form']['PME_OPTIONS']);
            }
        }
    }
}
foreach ($_POST['form'] as $key => $value) {
    if (substr($key, 0, 4) === 'PME_') {
        $res[substr($key, 4)] = $value;
    } else {
        $res[$key] = $value;
    }
}
$_POST['form'] = $res;

$dbc = new DBConnection(PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml');
$ses = new DBSession($dbc);

$fields = new DynaFormField($dbc);

if ($_POST['form']['XMLNODE_NAME'] === '') {
    return;
}

$attributes = $_POST['form'];

if (isset($attributes['HINT'])) {
    $attributes['HINT'] = addslashes($attributes['HINT']);
    $attributes['HINT'] = htmlspecialchars($attributes['HINT'], ENT_QUOTES, "UTF-8");
}

if (isset($attributes['CODE'])) {
    $attributes['XMLNODE_VALUE'] = ($attributes['CODE']);
}

$labels = array();
if (isset($attributes['LABEL'])) {
    $labels = array(SYS_LANG => $attributes['LABEL']);
}

unset($attributes['A']);
unset($attributes['ACCEPT']);
unset($attributes['LABEL']);
unset($attributes['PRO_UID']);

$options = null;
foreach ($attributes as $key => $value) {
    if ($key === 'OPTIONS') {
        if (is_array($value)) {
            if (is_array(reset($value))) {
                $langs = array();
                $options = array();
                $langs[] = SYS_LANG;
                $options[SYS_LANG] = array();
                foreach ($value as $row) {
                    foreach ($langs as $lang) {
                        $LANG = strtoupper($lang);
                        if (isset($row['LABEL'])) {
                            $options[$lang][$row['NAME']] = $row['LABEL'];
                        }
                    }
                }
                /* $first = reset($value);
                  foreach( $first as $optKey => $optValue ) {
                  if (substr($optKey,0,6)==='LABEL_') {
                  $langs[]=strtolower(substr($optKey,6));
                  $options[strtolower(substr($optKey,6))]=array();
                  }
                  }
                  foreach( $value as $row ) {
                  foreach( $langs as $lang ) {
                  $LANG = strtoupper($lang);
                  if (isset($row['LABEL_'.$LANG]))
                  $options[$lang][$row['NAME']]=$row['LABEL_'.$LANG];
                  }
                  } */
            }
        }
    } else {
        if (is_array($value)) {
            //Is a list:
            if (is_string(reset($value))) {
                $attributes[$key] = implode(',', $value);
            } else {
                //Is a grid.
            }
        }
    }
}
unset($attributes['VALIDATE_NAME']);
$fields->setFileName(PATH_DYNAFORM . $file . '.xml');

$FieldAttributes = $attributes;
$FieldAttrib = array();
unset($FieldAttributes['XMLNODE_NAME']);
unset($FieldAttributes['XMLNODE_NAME_OLD']);
unset($FieldAttributes['XMLNODE_VALUE']);
unset($FieldAttributes['BTN_CANCEL']);
unset($FieldAttributes['SAVELABEL']);
foreach ($FieldAttributes as $key => $value) {
    if ($value != "") {
        $FieldAttrib[strtolower($key)] = $value;
    }
}

$fields->saveField($attributes, $FieldAttrib, $labels);

G::LoadClass('xmlDb');
$i = 0;
$aFields = array();
$aFields[] = array('XMLNODE_NAME' => 'char',
    'TYPE' => 'char',
    'UP' => 'char',
    'DOWN' => 'char',
    'row__' => 'integer');
$oSession = new DBSession(new DBConnection(PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml'));
$oDataset = $oSession->Execute('SELECT * FROM dynaForm WHERE NOT( XMLNODE_NAME = "" )');
$iMaximun = $oDataset->count();
while ($aRow = $oDataset->Read()) {
    $aFields[] = array('XMLNODE_NAME' => $aRow['XMLNODE_NAME'],
        'TYPE' => isset($aRow['TYPE']) ? $aRow['TYPE'] : '',
        'UP' => ($i > 0 ? G::LoadTranslation('ID_UP') : ''),
        'DOWN' => ($i < $iMaximun - 1 ? G::LoadTranslation('ID_DOWN') : ''),
        'row__' => ($i + 1));
    $i++;
}
global $_DBArray;
$_DBArray['fields'] = $aFields;
$_SESSION['_DBArray'] = $_DBArray;

// Additions to javascript
if (isset($sType) && $sType === 'javascript') {
    $sCode = urlencode($pmeCode);
    $editor = new dynaformEditorAjax($_POST);
    $editor->set_javascript($A, $fieldName, $sCode);
}
