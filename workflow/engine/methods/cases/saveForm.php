<?php
/**
 * saveForm.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2013 Colosa Inc.23
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


try {
    if ($_GET['APP_UID'] !== $_SESSION['APPLICATION']) {
        die( G::LoadTranslation( 'ID_INVALID_APPLICATION_ID_MSG', array ('<a href=\'' . $_SERVER['HTTP_REFERER'] . '\'>{1}</a>',G::LoadTranslation( 'ID_REOPEN' )
        ) ) );
    }

    $oForm = new Form( $_SESSION["PROCESS"] . "/" . $_GET["UID"], PATH_DYNAFORM );
    $oForm->validatePost();

    //Load the variables
    $oCase = new Cases();
    $oCase->thisIsTheCurrentUser( $_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["USER_LOGGED"], "REDIRECT", "casesListExtJs" );
    $Fields = $oCase->loadCase( $_SESSION["APPLICATION"] );

    $Fields["APP_DATA"] = array_merge( $Fields["APP_DATA"], G::getSystemConstants() );
    $Fields["APP_DATA"] = array_merge( $Fields["APP_DATA"], $_POST["form"] );

    //If no variables are submitted and the $_POST variable is empty
    if (!isset($_POST['form'])) {
        $_POST['form'] = array();
    }

    //save data in PM Tables if necessary
    $newValues = array ();
    foreach ($_POST['form'] as $sField => $sAux) {
        if (isset( $oForm->fields[$sField]->pmconnection ) && isset( $oForm->fields[$sField]->pmfield )) {
            if (($oForm->fields[$sField]->pmconnection != '') && ($oForm->fields[$sField]->pmfield != '')) {
                if (isset( $oForm->fields[$oForm->fields[$sField]->pmconnection] )) {
                    require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
                    $oAdditionalTables = new AdditionalTables();
                    try {
                        $aData = $oAdditionalTables->load( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, true );
                    } catch (Exception $oError) {
                        $aData = array ('FIELDS' => array ()
                        );
                    }
                    $aKeys = array ();
                    $aAux = explode( '|', $oForm->fields[$oForm->fields[$sField]->pmconnection]->keys );
                    $i = 0;
                    $aValues = array ();
                    foreach ($aData['FIELDS'] as $aField) {
                        if ($aField['FLD_KEY'] == '1') {
                            $aKeys[$aField['FLD_NAME']] = (isset( $aAux[$i] ) ? G::replaceDataField( $aAux[$i], $Fields['APP_DATA'] ) : '');
                            $i ++;
                        }
                        if ($aField['FLD_NAME'] == $oForm->fields[$sField]->pmfield) {
                            $aValues[$aField['FLD_NAME']] = $Fields['APP_DATA'][$sField];
                        } else {
                            $aValues[$aField['FLD_NAME']] = '';
                        }
                    }
                    try {
                        $aRow = $oAdditionalTables->getDataTable( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, $aKeys );
                    } catch (Exception $oError) {
                        $aRow = false;
                    }
                    if ($aRow) {
                        foreach ($aValues as $sKey => $sValue) {
                            if ($sKey != $oForm->fields[$sField]->pmfield) {
                                $aValues[$sKey] = $aRow[$sKey];
                            }
                        }
                        try {
                            $oAdditionalTables->updateDataInTable( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, $aValues );
                        } catch (Exception $oError) {
                            //Nothing
                        }
                    } else {
                        try {
                            // assembling the field list in order to save the data ina new record of a pm table
                            if (empty( $newValues )) {
                                $newValues = $aValues;
                            } else {
                                foreach ($aValues as $aValueKey => $aValueCont) {
                                    if (trim( $newValues[$aValueKey] ) == '') {
                                        $newValues[$aValueKey] = $aValueCont;
                                    }
                                }
                            }
                            //$oAdditionalTables->saveDataInTable ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, $aValues );
                        } catch (Exception $oError) {
                            //Nothing
                        }
                    }
                }
            }
        }
    }

    //save data
    $aData = array ();
    $aData['APP_NUMBER'] = $Fields['APP_NUMBER'];
    $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
    $aData['APP_DATA'] = $Fields['APP_DATA'];
    $aData['DEL_INDEX'] = $_SESSION['INDEX'];
    $aData['TAS_UID'] = $_SESSION['TASK'];
    $aData['CURRENT_DYNAFORM'] = $_GET['UID'];
    $aData['USER_UID'] = $_SESSION['USER_LOGGED'];
    $aData['APP_STATUS'] = $Fields['APP_STATUS'];
    $aData['PRO_UID'] = $_SESSION['PROCESS'];

    $oCase->updateCase( $_SESSION['APPLICATION'], $aData );

    // saving the data ina pm table in case that is a new record
    if (! empty( $newValues )) {
        $id = key( $newValues );
        if (! $oAdditionalTables->updateDataInTable( $oForm->fields[$oForm->fields[$id]->pmconnection]->pmtable, $newValues )) {
            //<--This is to know if it is a new registry on the PM Table
            $oAdditionalTables->saveDataInTable( $oForm->fields[$oForm->fields[$id]->pmconnection]->pmtable, $newValues );
        }
    }

    die('OK');

} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}
