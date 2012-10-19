<?php
/**
 * webServiceAjax.php
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
ini_set( "soap.wsdl_cache_enabled", "0" ); // enabling WSDL cache


G::LoadClass( 'ArrayPeer' );
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_FACTORY' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

G::LoadInclude( 'ajax' );
//G::pr($_SESSION);
$_POST['action'] = get_ajax_value( 'action' );
if ($_POST['action'] == '') {
    $_POST['action'] = (isset( $_GET['action'] )) ? $_GET['action'] : '';
}

switch ($_POST['action']) {
    case 'showForm':
        global $G_PUBLISH;
        $xmlform = isset( $_POST['wsID'] ) ? 'setup/ws' . $_POST['wsID'] : '';
        if (file_exists( PATH_XMLFORM . $xmlform . '.xml' )) {

            global $_DBArray;
            $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
            $G_PUBLISH = new Publisher();
            $fields['SESSION_ID'] = isset( $_SESSION['WS_SESSION_ID'] ) ? $_SESSION['WS_SESSION_ID'] : '';
            $fields['ACTION'] = $_POST['wsID'];
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $fields, '../setup/webServicesAjax' );
            G::RenderPage( 'publish', 'raw' );
        }
        break;
    case 'showDetails':
        G::LoadClass( 'groups' );

        $dbc = new DBConnection();
        $ses = new DBSession( $dbc );

        if (! isset( $_SESSION['END_POINT'] )) {
            $aFields['WS_HOST'] = $_SERVER['HTTP_HOST'];
            $aFields['WS_WORKSPACE'] = SYS_SYS;
        } else {
            if (strpos( $_SESSION['END_POINT'], 'https' ) !== false) {
                preg_match( '@^(?:https://)?([^/]+)@i', $_SESSION['END_POINT'], $coincidencias );
            } else {
                preg_match( '@^(?:http://)?([^/]+)@i', $_SESSION['END_POINT'], $coincidencias );
            }
            $aAux = explode( ':', $coincidencias[1] );
            $aFields['WS_HOST'] = $aAux[0];
            $aFields['WS_PORT'] = (isset( $aAux[1] ) ? $aAux[1] : '');
            $aAux = explode( $aAux[0] . (isset( $aAux[1] ) ? ':' . $aAux[1] : ''), $_SESSION['END_POINT'] );
            $aAux = explode( '/', $aAux[1] );
            $aFields['WS_WORKSPACE'] = substr( $aAux[1], 3 );
        }

        $rows[] = array ('uid' => 'char','name' => 'char','age' => 'integer','balance' => 'float'
        );
        $rows[] = array ('uid' => 'http','name' => 'http'
        );
        $rows[] = array ('uid' => 'https','name' => 'https'
        );

        global $_DBArray;
        $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
        $_DBArray['protocol'] = $rows;
        $_SESSION['_DBArray'] = $_DBArray;

        if (! isset( $_SESSION['END_POINT'] )) {
            //$wsdl = 'http://'.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS. '/'. SYS_LANG .'/classic/services/wsdl';
            $wsdl = 'http://' . $_SERVER['HTTP_HOST'];
            $workspace = SYS_SYS;
        } else {
            $wsdl = $_SESSION['END_POINT'];
            $workspace = $_SESSION['WS_WORKSPACE'];
        }

        $defaultEndpoint = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/classic/services/wsdl2';

        $wsdl = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : $defaultEndpoint;

        $wsSessionId = '';
        if (isset( $_SESSION['WS_SESSION_ID'] )) {
            $wsSessionId = $_SESSION['WS_SESSION_ID'];
        }

        $aFields['WSDL'] = $wsdl;
        $aFields['OS'] = $workspace;
        $aFields['WSID'] = $wsSessionId;

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/webServicesDetails', '', $aFields, 'webServicesSetupSave' );

        G::RenderPage( "publish", "raw" );
        break;
    case 'showUploadFilesForm':
        global $G_PUBLISH;
        $xmlform = 'setup/wsSendFiles';
        if (file_exists( PATH_XMLFORM . $xmlform . '.xml' )) {

            global $_DBArray;
            $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');

            $G_PUBLISH = new Publisher();
            $fields['SESSION_ID'] = isset( $_SESSION['WS_SESSION_ID'] ) ? $_SESSION['WS_SESSION_ID'] : '';
            $fields['ACTION'] = 'wsSendFiles';
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $fields, '../setup/webServicesAjax' );
            G::RenderPage( 'publish', 'blank' );
        }
        break;
}
try {
    global $G_PUBLISH;
    if (isset( $_POST['form']['ACTION'] )) {
        $frm = $_POST['form'];
        $action = $frm['ACTION'];
        if (isset( $_POST["epr"] )) {
            $_SESSION['END_POINT'] = $_POST["epr"];
        }
        $defaultEndpoint = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/classic/services/wsdl2';

        $endpoint = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : $defaultEndpoint;

        $sessionId = isset( $_SESSION['SESSION_ID'] ) ? $_SESSION['SESSION_ID'] : '';

        //Apply proxy settings
        $proxy = array ();
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            $proxy['proxy_host'] = $sysConf['proxy_host'];
            if ($sysConf['proxy_port'] != '') {
                $proxy['proxy_port'] = $sysConf['proxy_port'];
            }
            if ($sysConf['proxy_user'] != '') {
                $proxy['proxy_login'] = $sysConf['proxy_user'];
            }
            if ($sysConf['proxy_pass'] != '') {
                $proxy['proxy_password'] = $sysConf['proxy_pass'];
            }
        }

        @$client = new SoapClient( $endpoint, $proxy );

        switch ($action) {
            case "Login":
                $user = $frm["USER_ID"];
                $pass = $frm["PASSWORD"];
                $params = array ('userid' => $user,'password' => $pass
                );
                $result = $client->__SoapCall( 'login', array ($params
                ) );
                $_SESSION['WS_SESSION_ID'] = '';
                if ($result->status_code == 0) {
                    $_SESSION['WS_SESSION_ID'] = $result->message;
                }
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
                $fields['version'] = $result->version;
                $fields['time_stamp'] = $result->timestamp;
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "ProcessList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );

                $wsResponse = $client->__SoapCall( 'ProcessList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'processes' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );

                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                        }

                        $rows[] = array ('guid' => $guid,'name' => $name
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['process'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'process' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrProcessList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "RoleList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );
                $wsResponse = $client->__SoapCall( 'RoleList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'roles' );

                $G_PUBLISH = new Publisher();

                $rows[] = array ('guid' => 'char','name' => 'char'
                );
                if (is_array( $result )) {

                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                        }

                        $rows[] = array ('guid' => $guid,'name' => $name
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['role'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'role' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrRoleList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "GroupList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );
                $wsResponse = $client->__SoapCall( 'GroupList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'groups' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );
                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                        }

                        $rows[] = array ('guid' => $guid,'name' => $name
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['group'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'group' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrGroupList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "CaseList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );
                $wsResponse = $client->__SoapCall( 'CaseList', array ($params
                ) );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char','status' => 'char','delIndex' => 'char'
                );

                $result = G::PMWSCompositeResponse( $wsResponse, 'cases' );

                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'status') {
                                    $status = $val->value;
                                }
                                if ($val->key == 'delIndex') {
                                    $delIndex = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'status') {
                                    $status = $val->value;
                                }
                                if ($val->key == 'delIndex') {
                                    $delIndex = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                            if (isset( $item->status )) {
                                $status = $item->status;
                            }
                            if (isset( $item->delIndex )) {
                                $delIndex = $item->delIndex;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'name' => $name,'status' => $status,'delIndex' => $delIndex
                        );

                    }

                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['case'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'case' );
                    //$c->addAscendingOrderByColumn ( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrCaseList', $c );

                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "UnassignedCaseList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );

                $wsResponse = $client->__SoapCall( 'UnassignedCaseList', array ($params
                ) );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char','delIndex' => 'char'
                );
                $result = G::PMWSCompositeResponse( $wsResponse, 'cases' );

                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'delIndex') {
                                    $delIndex = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'delIndex') {
                                    $delIndex = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                            if (isset( $item->delIndex )) {
                                $delIndex = $item->delIndex;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'name' => $name,'delIndex' => $delIndex
                        );
                    }

                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['case'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'case' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrUnassignedCaseList', $c );

                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "UserList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );
                $wsResponse = $client->__SoapCall( 'UserList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'users' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );
                if (is_array( $result )) {

                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                        }

                        $rows[] = array ('guid' => $guid,'name' => $name
                        );
                    }

                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['user'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'user' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrUserList', $c );

                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }
                G::RenderPage( 'publish', 'raw' );
                break;
            case "SendMessage":
                require_once ('classes/model/Application.php');
                $sessionId = $frm["SESSION_ID"];
                $from = $frm["FROM"];
                $to = $frm["TO_EMAIL"];
                $cc = isset( $frm["CC_MAIL"] ) ? $frm["CC_MAIL"] : '';
                $bcc = isset( $frm["BCC_MAIL"] ) ? $frm["BCC_MAIL"] : '';
                $caseId = $frm["CASE_ID"];
                $subject = $frm["SUBJECT"];
                $message = $frm["MESSAGE"];
                // getting the proUid variable
                $oCases = new Application();
                $oCases->load( $caseId );
                $proUid = $oCases->getProUid();
                $caseNumber = $oCases->getAppNumber();

                // generating the path for the template msj
                $templateFile = PATH_DB . SYS_SYS . PATH_SEP . 'mailTemplates' . PATH_SEP . $proUid . PATH_SEP . 'tempTemplate.hml';
                // generating the file adding the msj variable
                $messageBody = "message for case: " . $caseNumber . "<br>" . $message;
                file_put_contents( $templateFile, $messageBody );

                $params = array ('sessionId' => $sessionId,'caseId' => $caseId,'from' => $from,'to' => $to,'cc' => $cc,'bcc' => $bcc,'subject' => $subject,'template' => 'tempTemplate.hml'
                );
                $result = $client->__SoapCall( 'sendMessage', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "SendVariables":
                $sessionId = $frm["SESSION_ID"];
                $caseId = $frm["CASE_ID"];
                $variables = Array ();

                $o = new stdClass();
                $o->name = $frm["NAME1"];
                $o->value = $frm["VALUE1"];
                array_push( $variables, $o );
                $o = new stdClass();
                $o->name = $frm["NAME2"];
                $o->value = $frm["VALUE2"];
                array_push( $variables, $o );

                $params = array ('sessionId' => $sessionId,'caseId' => $caseId,'variables' => $variables
                );
                $result = $client->__SoapCall( 'SendVariables', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "DerivateCase":
                $sessionId = $frm["SESSION_ID"];
                $caseId = $frm["CASE_ID"];
                $delIndex = $frm["DEL_INDEX"];

                $params = array ('sessionId' => $sessionId,'caseId' => $caseId,'delIndex' => $delIndex
                );
                $result = $client->__SoapCall( 'RouteCase', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "ReassignCase":
                $sessionId = $frm["SESSION_ID"];
                $caseId = $frm["CASE_ID"];
                $delIndex = $frm["DEL_INDEX"];
                $userIdSource = $frm['USERIDSOURCE'];
                $userIdTarget = $frm['USERIDTARGET'];

                $params = array ('sessionId' => $sessionId,'caseId' => $caseId,'delIndex' => $delIndex,'userIdSource' => $userIdSource,'userIdTarget' => $userIdTarget
                );
                $result = $client->__SoapCall( 'reassignCase', array ($params
                ) );

                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "NewCaseImpersonate":
                $sessionId = $frm["SESSION_ID"];
                $processId = $frm["PROCESS_ID"];
                $userId = $frm["USER_ID"];
                $variables = Array ();
                foreach ($frm['VARIABLES'] as $iRow => $aRow) {
                    $o = new stdClass();
                    $o->name = $aRow['NAME'];
                    $o->value = $aRow['VALUE'];
                    array_push( $variables, $o );
                }
                $params = array ('sessionId' => $sessionId,'processId' => $processId,'userId' => $userId,'variables' => $variables
                );
                $result = $client->__SoapCall( 'NewCaseImpersonate', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "NewCase":
                $sessionId = $frm["SESSION_ID"];
                $processId = $frm["PROCESS_ID"];
                $taskId = $frm["TASK_ID"];

                $variables = Array ();
                foreach ($frm['VARIABLES'] as $iRow => $aRow) {
                    $o = new stdClass();
                    $o->name = $aRow['NAME'];
                    $o->value = $aRow['VALUE'];
                    array_push( $variables, $o );
                }
                $params = array ('sessionId' => $sessionId,'processId' => $processId,'taskId' => $taskId,'variables' => $variables
                );
                $result = $client->__SoapCall( 'NewCase', array ($params
                ) );

                $G_PUBLISH = new Publisher();

                $fields['status_code'] = $result->status_code;
                $fields['time_stamp'] = $result->timestamp;
                if (isset( $result->caseId )) {
                    $fields['message'] = "Case ID: " . $result->caseId . "\nCase Number: " . $result->caseNumber . "\n" . $result->message;
                } else {
                    $fields['message'] = '';
                }
                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "AssignUserToGroup":
                $sessionId = $frm["SESSION_ID"];
                $userId = $frm["USER_ID"];
                $groupId = $frm["GROUP_ID"];
                $params = array ('sessionId' => $sessionId,'userId' => $userId,'groupId' => $groupId
                );
                $result = $client->__SoapCall( 'AssignUserToGroup', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "CreateUser":
                $sessionId = $frm["SESSION_ID"];
                $userId = $frm["USER_ID"];
                $firstname = $frm["FIRST_NAME"];
                $lastname = $frm["LAST_NAME"];
                $email = $frm["EMAIL"];
                $role = $frm["ROLE"];
                $password = $frm["PASSWORD"];

                $params = array ('sessionId' => $sessionId,'userId' => $userId,'firstname' => $firstname,'lastname' => $lastname,'email' => $email,'role' => $role,'password' => $password
                );
                $result = $client->__SoapCall( 'CreateUser', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "TaskList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );
                $wsResponse = $client->__SoapCall( 'TaskList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'tasks' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );

                if (is_array( $result )) {

                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                        }

                        $rows[] = array ('guid' => $guid,'name' => $name
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['task'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'task' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrTaskList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }
                G::RenderPage( 'publish', 'raw' );
                break;
            case "TriggerList":
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId
                );

                $wsResponse = $client->__SoapCall( 'triggerList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'triggers' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char','processId' => 'char'
                );

                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'processId') {
                                    $processId = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'processId') {
                                    $processId = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                            if (isset( $item->processId )) {
                                $processId = $item->processId;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'name' => $name,'processId' => $processId
                        );
                    }

                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');

                    foreach ($rows as $key => $row) {
                        $proId = $row['processId'];
                        if (isset( $_DBArray['process'] ) && is_array( $_DBArray['process'] )) {
                            foreach ($_DBArray['process'] as $pkey => $prow) {
                                if ($proId == $prow['guid']) {
                                    $rows[$key]['processId'] = $prow['name'];
                                }
                            }
                        }
                    }

                    $_DBArray['triggers'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'triggers' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrTriggerList', $c );

                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            case "InputDocumentList":
                $caseId = $frm["CASE_ID"];
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId,'caseId' => $caseId
                );

                $wsResponse = $client->__SoapCall( 'InputDocumentList', array ($params
                ) );

                //g::pr($wsResponse);
                $result = G::PMWSCompositeResponse( $wsResponse, 'documents' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char','processId' => 'char'
                );

                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'filename') {
                                    $filename = $val->value;
                                }
                                if ($val->key == 'docId') {
                                    $docId = $val->value;
                                }
                                if ($val->key == 'version') {
                                    $version = $val->value;
                                }
                                if ($val->key == 'createDate') {
                                    $createDate = $val->value;
                                }
                                if ($val->key == 'createBy') {
                                    $createBy = $val->value;
                                }
                                if ($val->key == 'type') {
                                    $type = $val->value;
                                }
                                if ($val->key == 'link') {
                                    $link = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'filename') {
                                    $filename = $val->value;
                                }
                                if ($val->key == 'docId') {
                                    $docId = $val->value;
                                }
                                if ($val->key == 'version') {
                                    $version = $val->value;
                                }
                                if ($val->key == 'createDate') {
                                    $createDate = $val->value;
                                }
                                if ($val->key == 'createBy') {
                                    $createBy = $val->value;
                                }
                                if ($val->key == 'type') {
                                    $type = $val->value;
                                }
                                if ($val->key == 'link') {
                                    $link = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->filename )) {
                                $filename = $item->filename;
                            }
                            if (isset( $item->docId )) {
                                $docId = $item->docId;
                            }
                            if (isset( $item->version )) {
                                $version = $item->version;
                            }
                            if (isset( $item->createDate )) {
                                $createDate = $item->createDate;
                            }
                            if (isset( $item->createBy )) {
                                $createBy = $item->createBy;
                            }
                            if (isset( $item->type )) {
                                $type = $item->type;
                            }
                            if (isset( $item->link )) {
                                $link = $item->link;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'filename' => $filename,'docId' => $docId,'version' => $version,'createDate' => $createDate,'createBy' => $createBy,'type' => $type,'link' => $link
                        );
                    }

                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['inputDocument'] = $rows;
                    $documentArray = array ();
                    $documentArray[] = array ('guid' => 'char','filename' => 'char'
                    );
                    if (isset( $_DBArray['inputDocument'] )) {
                        foreach ($_DBArray['inputDocument'] as $key => $val) {
                            if ($key != 0 && isset( $val['filename'] )) {
                                $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                                );
                            }
                        }
                    }
                    if (isset( $_DBArray['outputDocument'] )) {
                        foreach ($_DBArray['outputDocument'] as $key => $val) {
                            if ($key != 0 && isset( $val['filename'] )) {
                                $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                                );
                            }
                        }
                    }
                    $_DBArray['documents'] = $documentArray;
                    $_DBArray['WS_TMP_CASE_UID'] = $frm["CASE_ID"];
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'inputDocument' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrInputDocumentList', $c );

                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }
                G::RenderPage( 'publish', 'raw' );
                break;
            case "InputDocumentProcessList":
                $processId = $frm["PROCESS_ID"];
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId,'processId' => $processId
                );

                $wsResponse = $client->__SoapCall( 'InputDocumentProcessList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'documents' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char','description' => 'char'
                );
                if (is_array( $result )) {
                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'description') {
                                    $description = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                                if ($val->key == 'description') {
                                    $description = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->name )) {
                                $name = $item->name;
                            }
                            if (isset( $item->description )) {
                                $description = $item->description;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'name' => $name,'description' => $description
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['inputDocuments'] = $rows;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'inputDocuments' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrInputDocumentProcessList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }
                G::RenderPage( 'publish', 'raw' );
                break;
            case "OutputDocumentList":
                $caseId = $frm["CASE_ID"];
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId,'caseId' => $caseId
                );

                $wsResponse = $client->__SoapCall( 'outputDocumentList', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'documents' );

                $G_PUBLISH = new Publisher();
                $rows = array ();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );
                if (is_array( $result )) {

                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'filename') {
                                    $filename = $val->value;
                                }
                                if ($val->key == 'docId') {
                                    $docId = $val->value;
                                }
                                if ($val->key == 'version') {
                                    $version = $val->value;
                                }
                                if ($val->key == 'createDate') {
                                    $createDate = $val->value;
                                }
                                if ($val->key == 'createBy') {
                                    $createBy = $val->value;
                                }
                                if ($val->key == 'type') {
                                    $type = $val->value;
                                }
                                if ($val->key == 'link') {
                                    $link = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'filename') {
                                    $filename = $val->value;
                                }
                                if ($val->key == 'docId') {
                                    $docId = $val->value;
                                }
                                if ($val->key == 'version') {
                                    $version = $val->value;
                                }
                                if ($val->key == 'createDate') {
                                    $createDate = $val->value;
                                }
                                if ($val->key == 'createBy') {
                                    $createBy = $val->value;
                                }
                                if ($val->key == 'type') {
                                    $type = $val->value;
                                }
                                if ($val->key == 'link') {
                                    $link = $val->value;
                                }
                            }
                        } else {
                            if (isset( $item->guid )) {
                                $guid = $item->guid;
                            }
                            if (isset( $item->filename )) {
                                $filename = $item->filename;
                            }
                            if (isset( $item->docId )) {
                                $docId = $item->docId;
                            }
                            if (isset( $item->version )) {
                                $version = $item->version;
                            }
                            if (isset( $item->createDate )) {
                                $createDate = $item->createDate;
                            }
                            if (isset( $item->createBy )) {
                                $createBy = $item->createBy;
                            }
                            if (isset( $item->type )) {
                                $type = $item->type;
                            }
                            if (isset( $item->link )) {
                                $link = $item->link;
                            }
                        }
                        $rows[] = array ('guid' => $guid,'filename' => $filename,'docId' => $docId,'version' => $version,'createDate' => $createDate,'createBy' => $createBy,'type' => $type,'link' => $link
                        );
                    }
                    global $_DBArray;
                    $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                    $_DBArray['outputDocument'] = $rows;
                    $documentArray = array ();
                    $documentArray[] = array ('guid' => 'char','filename' => 'char'
                    );
                    if (isset( $_DBArray['inputDocument'] )) {
                        foreach ($_DBArray['inputDocument'] as $key => $val) {
                            if ($key != 0 && isset( $val['filename'] )) {
                                $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                                );
                            }
                        }
                    }
                    if (isset( $_DBArray['outputDocument'] )) {
                        foreach ($_DBArray['outputDocument'] as $key => $val) {
                            if ($key != 0 && isset( $val['filename'] )) {
                                $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                                );
                            }
                        }
                    }
                    $_DBArray['documents'] = $documentArray;
                    $_SESSION['_DBArray'] = $_DBArray;

                    G::LoadClass( 'ArrayPeer' );
                    $c = new Criteria( 'dbarray' );
                    $c->setDBArrayTable( 'outputDocument' );
                    $c->addAscendingOrderByColumn( 'name' );
                    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrOutputDocumentList', $c );
                } elseif (is_object( $result )) {
                    $_SESSION['WS_SESSION_ID'] = '';
                    $fields['status_code'] = $result->status_code;
                    $fields['message'] = $result->message;
                    $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                }

                G::RenderPage( 'publish', 'raw' );
                break;
            //add removeUserFromGroup
            case "removeUserFromGroup":
                $sessionId = $frm["SESSION_ID"];
                $userId = $frm["USER_ID"];
                $groupId = $frm["GROUP_ID"];
                $params = array ('sessionId' => $sessionId,'userId' => $userId,'groupId' => $groupId
                );
                $result = $client->__SoapCall( 'removeUserFromGroup', array ($params
                ) );
                $G_PUBLISH = new Publisher();
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;
                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            //end add
            case "RemoveDocument":
                $appDocUid = $frm["APP_DOC_UID"];
                $sessionId = $frm["SESSION_ID"];
                $params = array ('sessionId' => $sessionId,'appDocUid' => $appDocUid
                );
                $result = $client->__SoapCall( 'RemoveDocument', array ($params
                ) );
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = $result->timestamp;

                if ($result->status_code == 9) {
                    $_SESSION['WS_SESSION_ID'] = '';
                }

                $G_PUBLISH = new Publisher();
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
                G::RenderPage( 'publish', 'raw' );
                break;
            case "TaskCase":
                $sessionId = $frm["SESSION_ID"];
                $caseId = $frm["CASE_ID"];

                $params = array ('sessionId' => $sessionId,'caseId' => $caseId
                );
                $wsResponse = $client->__SoapCall( 'TaskCase', array ($params
                ) );
                $result = G::PMWSCompositeResponse( $wsResponse, 'taskCases' );

                $G_PUBLISH = new Publisher();
                $rows[] = array ('guid' => 'char','name' => 'char'
                );

                if (is_array( $result )) {

                    foreach ($result as $key => $item) {
                        if (isset( $item->item )) {
                            foreach ($item->item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                                if ($val->key == 'name') {
                                    $name = $val->value;
                                }
                            }
                        } elseif (is_array( $item )) {
                            foreach ($item as $index => $val) {
                                if ($val->key == 'guid') {
                                    $guid = $val->value;
                                }
                            }
                        }
                        if ($val->key == 'name') {
                            $name = $val->value;
                        }
                    } else {
                        if (isset( $item->guid )) {
                            $guid = $item->guid;
                        }
                        if (isset( $item->name )) {
                            $name = $item->name;
                        }
                    }

                    $rows[] = array ('guid' => $guid,'name' => $name
                    );
                }

                global $_DBArray;
                $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                $_DBArray['taskCases'] = $rows;
                $_SESSION['_DBArray'] = $_DBArray;

                G::LoadClass( 'ArrayPeer' );
                $c = new Criteria( 'dbarray' );
                $c->setDBArrayTable( 'taskCases' );
                $c->addAscendingOrderByColumn( 'name' );
                $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrTaskCase', $c );
        } elseif (is_object( $result ))
        {
            $_SESSION['WS_SESSION_ID'] = '';
            $fields['status_code'] = $result->status_code;
            $fields['message'] = $result->message;
            $fields['time_stamp'] = date( "Y-m-d H:i:s" );
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
        }

        G::RenderPage( 'publish', 'raw' );
        break;
        case "wsSendFiles":
            if (isset( $_FILES['form'] )) {
                foreach ($_FILES['form']['name'] as $sFieldName => $vValue) {
                    if ($_FILES['form']['error'][$sFieldName] == 0) {
                        file_put_contents( G::sys_get_temp_dir() . PATH_SEP . $_FILES['form']['name'][$sFieldName], file_get_contents( $_FILES['form']['tmp_name'][$sFieldName] ) );
                        $filename = G::sys_get_temp_dir() . PATH_SEP . $_FILES['form']['name'][$sFieldName];
                    }
                }
            }

            //                              G::pr ( $_SESSION );
            if (! isset( $_POST['form']['INPUT_DOCUMENT'] )) {
                $_POST['form']['INPUT_DOCUMENT'] = '';
            }

            if (isset( $_SESSION['_DBArray']['inputDocument'] )) {
                foreach ($_SESSION['_DBArray']['inputDocument'] as $inputDocument) {
                    if ($inputDocument['guid'] == $_POST['form']['INPUT_DOCUMENT']) {
                        $doc_uid = $inputDocument['docId'];
                    }
                }
            } else {
                $doc_uid = "default";
            }
            if (! isset( $_SESSION['_DBArray']['WS_TMP_CASE_UID'] )) {
                $_SESSION['_DBArray']['WS_TMP_CASE_UID'] = '';
            }
            $usr_uid = $_SESSION['USER_LOGGED'];
            $app_uid = $_SESSION['_DBArray']['WS_TMP_CASE_UID'];
            $del_index = 1;

            function sendFile ($FILENAME, $USR_UID, $APP_UID, $DEL_INDEX = 1, $DOC_UID = null, $title = null, $comment = null)
            {
                $defaultEndpoint = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/classic/services/upload';
                $upload = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : $defaultEndpoint;

                $DOC_UID = ($DOC_UID != null) ? $DOC_UID : - 1;
                $APP_DOC_TYPE = ($DOC_UID == - 1) ? 'ATTACHED' : 'INPUT';
                $title = ($title != null) ? $title : $FILENAME;
                $comment = ($comment != null) ? $comment : '';

                $params = array ('ATTACH_FILE' => "@$FILENAME",'APPLICATION' => $APP_UID,'INDEX' => $DEL_INDEX,'USR_UID' => $USR_UID,'DOC_UID' => $DOC_UID,'APP_DOC_TYPE' => $APP_DOC_TYPE,'TITLE' => $title,'COMMENT' => $comment
                );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $defaultEndpoint );
                //curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
                $response = curl_exec( $ch );
                curl_close( $ch );
                return $response;
            }

            $fields['status_code'] = 0;
            $fields['time_stamp'] = date( "Y-m-d H:i:s" );
            if ($_POST['form']['UPLOAD_OPTION'] == '1') {
                // G::pr($doc_uid);
                $fields['message'] = sendFile( $filename, $usr_uid, $app_uid, 1, $doc_uid );

            } else {
                $fields['message'] = sendFile( $filename, $usr_uid, $app_uid );
            }
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
            G::RenderPage( 'publish', 'blank' );
            die();
            break;
        default:

            print_r( $_POST );
    }
}






    global $_DBArray;

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'raw' );
}

