<?php

use ProcessMaker\Core\System;

$action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : "consolidated";
$arrayTabItem = array();

$oCriteria = new Criteria("workflow");
$oCriteria->add(CaseConsolidatedCorePeer::CON_STATUS, 'ACTIVE');
$activeNumRows = CaseConsolidatedCorePeer::doCount($oCriteria);

$headPublisher = &headPublisher::getSingleton();
$usrUid = $_SESSION["USER_LOGGED"];
$conf = new Configurations();

try {
    $confCasesList = $conf->getConfiguration("casesList", $action);
    $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
} catch (Exception $e) {
    $confCasesList = array();
    $generalConfCasesList = array();
}

if (isset($generalConfCasesList["casesListRowNumber"]) && !empty($generalConfCasesList["casesListRowNumber"])) {
    $pageSize = intval($generalConfCasesList["casesListRowNumber"]);
} else {
    $config = getAdditionalFields($action, $confCasesList);
    $pageSize = intval($config["rowsperpage"]);
}

$criteria = new Criteria();
$criteria->addAsColumn('NUMREC', 'COUNT(' . ListInboxPeer::TAS_UID . ')');
$criteria->addSelectColumn(ListInboxPeer::PRO_UID);
$criteria->addSelectColumn(ProcessPeer::PRO_TITLE);
$criteria->addSelectColumn(ListInboxPeer::TAS_UID);
$criteria->addSelectColumn(TaskPeer::TAS_TITLE);
$criteria->addSelectColumn(CaseConsolidatedCorePeer::DYN_UID);
$criteria->addJoin(CaseConsolidatedCorePeer::TAS_UID, ListInboxPeer::TAS_UID, Criteria::LEFT_JOIN);
$criteria->addJoin(ListInboxPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
$criteria->addJoin(ListInboxPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
$criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
$criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
$criteria->addGroupByColumn(ListInboxPeer::TAS_UID);
$rsSql = CaseConsolidatedCorePeer::doSelectRS($criteria);
$rsSql->setFetchmode(ResultSet::FETCHMODE_ASSOC);

while ($rsSql->next()) {
    $row = $rsSql->getRow();

    $processUid = $row['PRO_UID'];
    $proTitle = $row['PRO_TITLE'];
    $taskUid = $row['TAS_UID'];
    $taskTitle = $row['TAS_TITLE'];
    $dynaformUid = $row['DYN_UID'];

    $tabTitle = $taskTitle . " (" . (($activeNumRows > 0) ? $row["NUMREC"] : 0) . ")";

    $grdTitle = htmlentities($proTitle . " / " . $tabTitle, ENT_QUOTES, "UTF-8");
    $tabTitle = htmlentities(substr($proTitle, 0, 25) . ((strlen($proTitle) > 25) ? "..." : null) . " / " . $tabTitle, ENT_QUOTES, "UTF-8");

    $arrayTabItem[] = "
    {
        title: \"<span onmouseover=\\\"toolTipTab('$grdTitle', 1);\\\" onmouseout=\\\"toolTipTab('', 0);\\\">$tabTitle</span>\",
        listeners: {
            activate: function ()
            {
                  generateGrid(\"$processUid\", \"$taskUid\", \"$dynaformUid\");
            }
        }
    }";
}

if (count($arrayTabItem) > 0) {
    $urlProxy = System::getHttpServerHostnameRequestsFrontEnd() . '/api/1.0/' . SYS_SYS . '/consolidated/';
    $clientId = 'x-pm-local-client';
    $client = getClientCredentials($clientId);
    $authCode = getAuthorizationCode($client);
    $debug = false; //System::isDebugMode();

    $loader = Maveriks\Util\ClassLoader::getInstance();
    $loader->add(PATH_TRUNK . 'vendor/bshaffer/oauth2-server-php/src/', "OAuth2");

    $request = array(
        'grant_type' => 'authorization_code',
        'code' => $authCode
    );
    $server = array(
        'REQUEST_METHOD' => 'POST'
    );
    $headers = array(
        "PHP_AUTH_USER" => $client['CLIENT_ID'],
        "PHP_AUTH_PW" => $client['CLIENT_SECRET'],
        "Content-Type" => "multipart/form-data;",
        "Authorization" => "Basic " . base64_encode($client['CLIENT_ID'] . ":" . $client['CLIENT_SECRET'])
    );

    $request = new \OAuth2\Request(array(), $request, array(), array(), array(), $server, null, $headers);
    $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
    $response = $oauthServer->postToken($request, true);

    $clientToken = $response->getParameters();
    $clientToken["client_id"] = $client['CLIENT_ID'];
    $clientToken["client_secret"] = $client['CLIENT_SECRET'];


    $items = "[" . implode(",", $arrayTabItem) ."]";

    $userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;
    $processes = getProcessArray($action, $userUid);

    $headPublisher->assign("pageSize", $pageSize);          //Sending the page size
    $headPublisher->assign("action", $action);              //Sending the fields to get from proxy
    $headPublisher->assign("Items", $items);
    $headPublisher->assign("processValues", $processes);    //Sending the columns to display in grid
    $headPublisher->assign("varSkin", SYS_SKIN);            //Sending the current Skin
    $headPublisher->assign("FORMATS", $conf->getFormats());
    $headPublisher->assign("urlProxy", $urlProxy);
    $headPublisher->assign('credentials', $clientToken );

    $oHeadPublisher->assign('isIE', Bootstrap::isIE());

    $headPublisher->addExtJsScript("app/main", true);
    $headPublisher->addExtJsScript("cases/casesListConsolidated", false);   //Adding a JavaScript file .js
    $headPublisher->addContent("cases/casesListConsolidated");              //Adding a HTML file .html

    G::RenderPage("publish", "extJs");
} else {
    echo "<span style=\"font: 0.75em normal arial, verdana, helvetica, sans-serif;\">" . G::LoadTranslation("ID_NO_RECORDS_FOUND") . "</span>";
}

function getProcessArray($action, $userUid)
{
    $processes = array();
    $processes[] = array("", G::LoadTranslation("ID_ALL_PROCESS"));
    $cProcess = new Criteria("workflow");
    switch ($action) {
        case "simple_search":
        case "search":
            //In search action, the query to obtain all process is too slow, so we need to query directly to
            //process and content tables, and for that reason we need the current language in AppCacheView.
            $cProcess->clearSelectColumns();
            $cProcess->addSelectColumn(ProcessPeer::PRO_UID);
            $cProcess->addSelectColumn(ProcessPeer::PRO_TITLE);

            $cProcess->add(ProcessPeer::PRO_STATUS, "ACTIVE");
            $oDataset = ProcessPeer::doSelectRS($cProcess);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $processes[] = array($aRow["PRO_UID"], $aRow["PRO_TITLE"]);
                $oDataset->next();
            }

            return ($processes);
            break;
        case "consolidated":
        default:
            break;
    }

    $cProcess->clearSelectColumns();
    $cProcess->setDistinct();
    $cProcess->addSelectColumn(ProcessPeer::PRO_UID);
    $cProcess->addSelectColumn(ProcessPeer::PRO_TITLE);
    $oDataset = ProcessPeer::doSelectRS($cProcess);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();

    while ($aRow = $oDataset->getRow()) {
        $processes[] = array($aRow["PRO_UID"], $aRow["PRO_TITLE"]);
        $oDataset->next();
    }

    return ($processes);
}

function getConsolidated()
{
    $caseColumns = array ();
    $caseColumns[] = array("header" =>"#",           "dataIndex" => "APP_NUMBER",            "width" => 45, "align" => "center");
    $caseColumns[] = array("header" =>"Case",        "dataIndex" => "APP_TITLE",             "width" => 150);
    $caseColumns[] = array("header" =>"UserUid",     "dataIndex" => "USR_UID",               "width" => 50, "hidden" => true, "hideable" => false);
    $caseColumns[] = array("header" =>"PreUsrUid",   "dataIndex" => "PREVIOUS_USR_UID",      "width" => 50, "hidden" => true, "hideable" => false);
    $caseColumns[] = array("header" =>"Task",        "dataIndex" => "APP_TAS_TITLE",         "width" => 120);
    $caseColumns[] = array("header" =>"Process",     "dataIndex" => "APP_PRO_TITLE",         "width" => 120);
    $caseColumns[] = array("header" =>"Sent by",     "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
    $caseColumns[] = array("header" =>"Due Date",    "dataIndex" => "DEL_TASK_DUE_DATE",     "width" => 110);
    $caseColumns[] = array("header" =>"Last Modify", "dataIndex" => "APP_UPDATE_DATE",       "width" => 110);
    $caseColumns[] = array("header" =>"Priority",    "dataIndex" => "DEL_PRIORITY",          "width" => 50);

    $caseReaderFields = array();
    $caseReaderFields[] = array("name" => "APP_UID");
    $caseReaderFields[] = array("name" => "USR_UID");
    $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
    $caseReaderFields[] = array("name" => "DEL_INDEX");
    $caseReaderFields[] = array("name" => "APP_NUMBER");
    $caseReaderFields[] = array("name" => "APP_TITLE");
    $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
    $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
    $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
    $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
    $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
    $caseReaderFields[] = array("name" => "DEL_PRIORITY");
    $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
    $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
    $caseReaderFields[] = array("name" => "APP_STATUS");

    return (array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => 20, "dateformat" => "M d, Y"));
}

function getAdditionalFields($action, $confCasesList)
{
    $caseColumns = array();
    $caseReaderFields = array();

    if (!empty($confCasesList) && !empty($confCasesList["second"]["data"])) {
        foreach ($confCasesList["second"]["data"] as $fieldData) {
            if ($fieldData["fieldType"] != "key") {
                $label = $fieldData["label"];
                $caseColumns[]      = array("header" => $label, "dataIndex" => $fieldData["name"], "width" => $fieldData["width"], "align" => $fieldData["align"]);
                $caseReaderFields[] = array("name"   => $fieldData["name"]);
            }
        }
        return (array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => $confCasesList["rowsperpage"], "dateformat" => $confCasesList["dateformat"]));
    } else {
        switch ($action) {
            case "consolidated":
            default:
                $action = "consolidated";
                $config = getConsolidated();
                break;
        }
        return ($config);
    }
}

function getClientCredentials($clientId)
{
    $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo(getDsn());
    return $oauthQuery->getClientDetails($clientId);
}

function getDsn()
{
    list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
    $port = empty($port) ? '' : ";port=$port";
    $dsn = DB_ADAPTER.':host='.$host.';dbname='.DB_NAME.$port;

    return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
}


function getAuthorizationCode($client)
{
    \ProcessMaker\Services\OAuth2\Server::setDatabaseSource(getDsn());
    \ProcessMaker\Services\OAuth2\Server::setPmClientId($client['CLIENT_ID']);

    $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
    $userId = $_SESSION['USER_LOGGED'];
    $authorize = true;
    $_GET = array_merge($_GET, array(
        'response_type' => 'code',
        'client_id' => $client['CLIENT_ID'],
        'scope' => implode(' ', $oauthServer->getScope())
    ));

    $response = $oauthServer->postAuthorize($authorize, $userId, true);
    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

    return $code;
}