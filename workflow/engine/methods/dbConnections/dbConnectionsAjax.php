<?php

/**
 * upgrade.php
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

/*
 * Data base connections routines for ajax request
 * @Author Erik Amaru Ortiz <erik@colosa.com>
 * @Last update May 20th, 2009
 * @Param  var action from POST request
 */

if (isset( $_POST['action'] ) || isset( $_POST['function'] )) {
    $action = (isset( $_POST['action'] )) ? $_POST['action'] : $_POST['function'];
} else {
    throw new Exception( 'dbconnections Fatal error, No action defined!...' );
}

if (isset( $_POST['PROCESS'] )) {
    $_SESSION['PROCESS'] = $_POST['PROCESS'];
}

    #Global Definitions
require_once 'classes/model/DbSource.php';
require_once 'classes/model/Content.php';

$G_PUBLISH = new Publisher();
G::LoadClass( 'processMap' );
G::LoadClass( 'ArrayPeer' );
G::LoadClass( 'dbConnections' );
global $_DBArray;

switch ($action) {
    case 'loadInfoAssigConnecctionDB':
        $oStep = new Step();
        return print ($oStep->loadInfoAssigConnecctionDB( $_POST['PRO_UID'], $_POST['DBS_UID'] )) ;
        break;
    case 'showDbConnectionsList':
        $oProcess = new processMap();
        $oCriteria = $oProcess->getConditionProcessList();
        if (ProcessPeer::doCount( $oCriteria ) > 0) {
            $aProcesses = array ();
            $aProcesses[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char'
            );
            $oDataset = ArrayBasePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $sProcessUID = '';
            while ($aRow = $oDataset->getRow()) {
                if ($sProcessUID == '') {
                    $sProcessUID = $aRow['PRO_UID'];
                }
                $aProcesses[] = array ('PRO_UID' => (isset( $aRow['PRO_UID'] ) ? $aRow['PRO_UID'] : ''),'PRO_TITLE' => (isset( $aRow['PRO_TITLE'] ) ? $aRow['PRO_TITLE'] : '')
                );
                $oDataset->next();
            }

            $_DBArray['PROCESSES'] = $aProcesses;
            $_SESSION['_DBArray'] = $_DBArray;
            $_SESSION['PROCESS'] = (isset( $_POST['PRO_UID'] ) ? $_POST['PRO_UID'] : '');

            $oDBSource = new DbSource();
            $oCriteria = $oDBSource->getCriteriaDBSList( $_SESSION['PROCESS'] );
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'dbConnections/dbConnections', $oCriteria );
        }
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showConnections':
        $oDBSource = new DbSource();
        $oCriteria = $oDBSource->getCriteriaDBSList( $_SESSION['PROCESS'] );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'dbConnections/dbConnections', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'newDdConnection':
        $dbs = new dbConnections( $_SESSION['PROCESS'] );
        $dbServices = $dbs->getDbServicesAvailables();
        $dbService = $dbs->getEncondeList();

        //we are updating the passwords with encrupt info
        $dbs->encryptThepassw( $_SESSION['PROCESS'] );
        //end updating

        $rows[] = array ('uid' => 'char','name' => 'char'
        );

        foreach ($dbServices as $srv) {
            $rows[] = array ('uid' => $srv['id'],'name' => $srv['name']
            );
        }

        $_DBArray['BDCONNECTIONS'] = $rows;

        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dbConnections/dbConnections_New', '', '' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'editDdConnection':
        $dbs = new dbConnections( $_SESSION['PROCESS'] );
        $dbServices = $dbs->getDbServicesAvailables();

        $rows[] = array ('uid' => 'char','name' => 'char'
        );
        foreach ($dbServices as $srv) {
            $rows[] = array ('uid' => $srv['id'],'name' => $srv['name']
            );
        }

        $_DBArray['BDCONNECTIONS'] = $rows;
        $_SESSION['_DBArray'] = $_DBArray;

        $o = new DbSource();
        $aFields = $o->load( $_POST['DBS_UID'], $_SESSION['PROCESS'] );
        if ($aFields['DBS_PORT'] == '0') {
            $aFields['DBS_PORT'] = '';
        }
        $aFields['DBS_PASSWORD'] = $dbs->getPassWithoutEncrypt( $aFields );
        $aFields['DBS_PASSWORD'] = ($aFields['DBS_PASSWORD'] == 'none') ? "" : $aFields['DBS_PASSWORD'];
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dbConnections/dbConnections_Edit', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'saveEditConnection':
        $oDBSource = new DbSource();
        $oContent = new Content();
        if (strpos( $_POST['server'], "\\" )) {
            $_POST['port'] = 'none';
        }

        $flagTns = ($_POST["type"] == "oracle" && $_POST["connectionType"] == "TNS")? 1 : 0;

        if ($flagTns == 0) {
            $_POST["connectionType"] = "NORMAL";

            $aData = array("DBS_UID" => $_POST["dbs_uid"], "PRO_UID" => $_SESSION["PROCESS"], "DBS_TYPE" => $_POST["type"], "DBS_SERVER" => $_POST["server"], "DBS_DATABASE_NAME" => $_POST["db_name"], "DBS_USERNAME" => $_POST["user"], "DBS_PASSWORD" => (($_POST["passwd"] == "none")? "" : G::encrypt($_POST["passwd"], $_POST["db_name"])) . "_2NnV3ujj3w", "DBS_PORT" => (($_POST["port"] == "none")? "" : $_POST["port"]), "DBS_ENCODE" => $_POST["enc"], "DBS_CONNECTION_TYPE" => $_POST["connectionType"], "DBS_TNS" => "");
        } else {
            $aData = array("DBS_UID" => $_POST["dbs_uid"], "PRO_UID" => $_SESSION["PROCESS"], "DBS_TYPE" => $_POST["type"], "DBS_SERVER" => "", "DBS_DATABASE_NAME" => "", "DBS_USERNAME" => $_POST["user"], "DBS_PASSWORD" => (($_POST["passwd"] == "none")? "" : G::encrypt($_POST["passwd"], $_POST["tns"])) . "_2NnV3ujj3w", "DBS_PORT" => "", "DBS_ENCODE" => "", "DBS_CONNECTION_TYPE" => $_POST["connectionType"], "DBS_TNS" => $_POST["tns"]);
        }

        $oDBSource->update( $aData );
        $oContent->addContent( 'DBS_DESCRIPTION', '', $_POST['dbs_uid'], SYS_LANG, $_POST['desc'] );
        break;
    case 'saveConnection':
        $oDBSource = new DbSource();
        $oContent = new Content();
        if (strpos( $_POST['server'], "\\" )) {
            $_POST['port'] = 'none';
        }

        $flagTns = ($_POST["type"] == "oracle" && $_POST["connectionType"] == "TNS")? 1 : 0;

        if ($flagTns == 0) {
            $_POST["connectionType"] = "NORMAL";

            $aData = array("PRO_UID" => $_SESSION["PROCESS"], "DBS_TYPE" => $_POST["type"], "DBS_SERVER" => $_POST["server"], "DBS_DATABASE_NAME" => $_POST["db_name"], "DBS_USERNAME" => $_POST["user"], "DBS_PASSWORD" => (($_POST["passwd"] == "none")? "" : G::encrypt($_POST["passwd"], $_POST["db_name"])) . "_2NnV3ujj3w", "DBS_PORT" => (($_POST["port"] == "none") ? "" : $_POST["port"]), "DBS_ENCODE" => $_POST["enc"], "DBS_CONNECTION_TYPE" => $_POST["connectionType"], "DBS_TNS" => "");
        } else {
            $aData = array("PRO_UID" => $_SESSION["PROCESS"], "DBS_TYPE" => $_POST["type"], "DBS_SERVER" => "", "DBS_DATABASE_NAME" => "", "DBS_USERNAME" => $_POST["user"], "DBS_PASSWORD" => (($_POST["passwd"] == "none")? "" : G::encrypt($_POST["passwd"], $_POST["tns"])) . "_2NnV3ujj3w", "DBS_PORT" => "", "DBS_ENCODE" => "", "DBS_CONNECTION_TYPE" => $_POST["connectionType"], "DBS_TNS" => $_POST["tns"]);
        }

        $newid = $oDBSource->create( $aData );
        $sDelimiter = DBAdapter::getStringDelimiter();
        $oContent->addContent( 'DBS_DESCRIPTION', '', $newid, SYS_LANG, $_POST['desc'] );
        break;
    case 'deleteDbConnection':
        try {
            $oDBSource = new DbSource();
            $oContent = new Content();

            $DBS_UID = $_POST['dbs_uid'];
            $PRO_UID = $_SESSION['PROCESS'];
            $oDBSource->remove( $DBS_UID, $PRO_UID );
            $oContent->removeContent( 'DBS_DESCRIPTION', "", $DBS_UID );
            $result->success = true;
            $result->msg = G::LoadTranslation( 'ID_DBCONNECTION_REMOVED' );
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode( $result );
        break;
    case 'showTestConnection':
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'dbConnections/dbConnections' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'testConnection':
        sleep( 0 );

        G::LoadClass("net");

        define("SUCCESSFULL", "SUCCESSFULL");
        define("FAILED", "FAILED");

        $step = $_POST["step"];
        $type = $_POST["type"];

        $user = $_POST["user"];
        $passwd = ($_POST["passwd"] == "none")? "" : $_POST["passwd"];

        $flagTns = ($_POST["type"] == "oracle" && $_POST["connectionType"] == "TNS")? 1 : 0;

        if ($flagTns == 0) {
            $server = $_POST["server"];
            $db_name = $_POST["db_name"];
            $port = $_POST["port"];

            if ($port == "none" || $port == 0) {
                //setting defaults ports
                switch ($type) {
                    case "mysql":
                        $port = 3306;
                        break;
                    case "pgsql":
                        $port = 5432;
                        break;
                    case "mssql":
                        $port = 1433;
                        break;
                    case "oracle":
                        $port = 1521;
                        break;
                }
            }

            $Server = new NET($server);

            switch ($step) {
                case 1:
                    if ($Server->getErrno() == 0) {
                        echo SUCCESSFULL . ",";
                    } else {
                        echo FAILED . "," . $Server->error;
                    }
                    break;
                case 2:
                    $Server->scannPort($port);

                    if ($Server->getErrno() == 0) {
                        echo SUCCESSFULL . ",";
                    } else {
                        echo FAILED . "," . $Server->error;
                    }
                    break;
                case 3:
                    $Server->loginDbServer($user, $passwd);
                    $Server->setDataBase($db_name, $port);

                    if ($Server->errno == 0) {
                        $response = $Server->tryConnectServer($type);

                        if ($response->status == "SUCCESS") {
                            echo SUCCESSFULL . ",";
                        } else {
                            echo FAILED . "," . $Server->error;
                        }
                    } else {
                        echo FAILED . "," . $Server->error;
                    }
                    break;
                case 4:
                    $Server->loginDbServer($user, $passwd);
                    $Server->setDataBase($db_name, $port);

                    if ($Server->errno == 0) {
                        $response = $Server->tryConnectServer($type);

                        if ($response->status == "SUCCESS") {
                            $response = $Server->tryOpenDataBase($type);

                            if ($response->status == "SUCCESS") {
                                echo SUCCESSFULL . "," . $Server->error;
                            } else {
                                echo FAILED . "," . $Server->error;
                            }
                        } else {
                            echo FAILED . "," . $Server->error;
                        }
                    } else {
                        echo FAILED . "," . $Server->error;
                    }
                    break;
                default:
                    echo "finished";
                    break;
            }
        } else {
            $connectionType = $_POST["connectionType"];
            $tns = $_POST["tns"];

            $net = new NET();

            switch ($step) {
                case 1:
                    $net->loginDbServer($user, $passwd);

                    if ($net->errno == 0) {
                        $arrayServerData = array("connectionType" => $connectionType, "tns" => $tns);

                        $response = $net->tryConnectServer($type, $arrayServerData);

                        if ($response->status == "SUCCESS") {
                            $response = $net->tryOpenDataBase($type, $arrayServerData);

                            if ($response->status == "SUCCESS") {
                                echo SUCCESSFULL . "," . $net->error;
                            } else {
                                echo FAILED . "," . $net->error;
                            }
                        } else {
                            echo FAILED . "," . $net->error;
                        }
                    } else {
                        echo FAILED . "," . $net->error;
                    }
                    break;
                default:
                    echo "finished";
                    break;
            }
        }
        break;
    case 'showEncodes':
        //G::LoadThirdParty( 'pear/json', 'class.json' );
        //$oJSON = new Services_JSON();
        $engine = $_POST['engine'];

        if ($engine != "0") {
            $dbs = new dbConnections();
            echo Bootstrap::json_encode( $dbs->getEncondeList( $engine ) );

        } else {
            echo '[["0","..."]]';
        }
        break;
}

