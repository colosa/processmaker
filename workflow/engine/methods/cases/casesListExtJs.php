<?php

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

unset($_SESSION['APPLICATION']);

//get the action from GET or POST, default is todo
$action = isset( $_GET['action'] ) ? $_GET['action'] : (isset( $_POST['action'] ) ? $_POST['action'] : 'todo');
$openApplicationUid = (isset($_GET['openApplicationUid']))? $_GET['openApplicationUid'] : null;

/*----------------------------------********---------------------------------*/

//fix a previous inconsistency
$urlProxy = 'proxyCasesList';
if ($action == 'selfservice') {
    $action = 'unassigned';
}

/*----------------------------------********---------------------------------*/

$oHeadPublisher = & headPublisher::getSingleton();
// oHeadPublisher->setExtSkin( 'xtheme-blue');
//get the configuration for this action
$conf = new Configurations();
try {
    // the setup for search is the same as the Sent (participated)
    $confCasesList = $conf->getConfiguration( 'casesList', ($action == 'search' || $action == 'simple_search') ? 'search' : $action );

    $table = null;
    if (isset($confCasesList['PMTable'])) {
        $aditionalTable = new AdditionalTables();
        $table = $aditionalTable->load($confCasesList['PMTable']);
    }
    $confCasesList = ($table != null) ? $confCasesList : array ();

    $generalConfCasesList = $conf->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
} catch (Exception $e) {
    $confCasesList = array ();
    $generalConfCasesList = array ();
}

// reassign header configuration
$confReassignList = getReassignList();

// evaluates an action and the configuration for the list that will be rendered
$config = getAdditionalFields( $action, $confCasesList );

$columns = $config['caseColumns'];
$readerFields = $config['caseReaderFields'];
$reassignColumns = $confReassignList['caseColumns'];
$reassignReaderFields = $confReassignList['caseReaderFields'];

// if the general settings has been set the pagesize values are extracted from that record
if (isset( $generalConfCasesList['casesListRowNumber'] ) && ! empty( $generalConfCasesList['casesListRowNumber'] )) {
    $pageSize = intval( $generalConfCasesList['casesListRowNumber'] );
} else {
    $pageSize = intval( $config['rowsperpage'] );
}

// if the general settings has been set the dateFormat values are extracted from that record
if (isset( $generalConfCasesList['casesListDateFormat'] ) && ! empty( $generalConfCasesList['casesListDateFormat'] )) {
    $dateFormat = $generalConfCasesList['casesListDateFormat'];
} else {
    $dateFormat = $config['dateformat'];
}

if ($action == 'draft') {
    //array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'deleteLink' ) );
}
if ($action == 'selfservice') {
    array_unshift( $columns, array ('header' => '','width' => 50,'sortable' => false,'id' => 'viewLink') );
}

if ($action == 'paused') {
    //array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'unpauseLink' ) );
}

$userUid = (isset( $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] != '') ? $_SESSION['USER_LOGGED'] : null;
$oAppCache = new AppCacheView();
$oAppCache->confCasesList = $confCasesList;
$solrEnabled = 0;
if ($action == "todo" || $action == "draft" || $action == "sent" || $action == "selfservice" ||
    $action == "unassigned" || $action == "search") {
    $solrConfigured = ($solrConf = System::solrEnv()) !== false ? 1 : 0;
    if ($solrConfigured == 1) {
        $applicationSolrIndex = new AppSolr(
            $solrConf['solr_enabled'],
            $solrConf['solr_host'],
            $solrConf['solr_instance']
        );
        if ($applicationSolrIndex->isSolrEnabled()) {
            $solrEnabled = 1;
        }
    }
}

//get values for the comboBoxes
$processes[] = array('', G::LoadTranslation('ID_ALL_PROCESS'));
$status = getStatusArray($action, $userUid);
$category = getCategoryArray();
$columnToSearch = getColumnsSearchArray();
$oHeadPublisher->assign('reassignReaderFields', $reassignReaderFields); //sending the fields to get from proxy
$oHeadPublisher->addExtJsScript('cases/reassignList', false);
$enableEnterprise = false;
if (class_exists('enterprisePlugin')) {
    $enableEnterprise = true;
    $oHeadPublisher->addExtJsScript(PATH_PLUGINS . "enterprise" . PATH_SEP . "advancedTools" . PATH_SEP, false, true);
}

$oHeadPublisher->assign('pageSize', $pageSize); //sending the page size
$oHeadPublisher->assign('columns', $columns); //sending the columns to display in grid
$oHeadPublisher->assign('readerFields', $readerFields); //sending the fields to get from proxy
$oHeadPublisher->assign('reassignColumns', $reassignColumns); //sending the columns to display in grid
$oHeadPublisher->assign('action', $action); //sending the action to make
$oHeadPublisher->assign('urlProxy', $urlProxy); //sending the urlProxy to make
$oHeadPublisher->assign('PMDateFormat', $dateFormat); //sending the fields to get from proxy
$oHeadPublisher->assign('statusValues', $status); //Sending the listing of status
$oHeadPublisher->assign('processValues', $processes); //Sending the listing of processes
$oHeadPublisher->assign('categoryValues', $category); //Sending the listing of categories
$oHeadPublisher->assign('solrEnabled', $solrEnabled); //Sending the status of solar
$oHeadPublisher->assign('enableEnterprise', $enableEnterprise); //sending the page size
$oHeadPublisher->assign('columnSearchValues', $columnToSearch); //Sending the list of column for search: caseTitle, caseNumber, tasTitle


/*----------------------------------********---------------------------------*/

//menu permissions
$cnt = '';
$reassignCase    = ($RBAC->userCanAccess( 'PM_REASSIGNCASE' ) == 1) ? 'true' : 'false';
$reassignCaseSup = ($RBAC->userCanAccess( 'PM_REASSIGNCASE_SUPERVISOR' ) == 1) ? 'true':'false';
$oHeadPublisher->assign( 'varReassignCase', $reassignCase );
$oHeadPublisher->assign( 'varReassignCaseSupervisor', $reassignCaseSup );
$c = new Configurations();
$oHeadPublisher->addExtJsScript( 'app/main', true );
$oHeadPublisher->addExtJsScript( 'cases/casesList', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'cases/casesListExtJs' ); //adding a html file  .html.
$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
$oHeadPublisher->assign('extJsViewState', $oHeadPublisher->getExtJsViewState());
$oHeadPublisher->assign('isIE', Bootstrap::isIE());
$oHeadPublisher->assign('__OPEN_APPLICATION_UID__', $openApplicationUid);

$oPluginRegistry = PluginRegistry::loadSingleton();
$fromPlugin = $oPluginRegistry->getOpenReassignCallback();
$jsFunction = false;
if(sizeof($fromPlugin)) {
    /** @var \ProcessMaker\Plugins\Interfaces\OpenReassignCallback $jsFile */
    foreach($fromPlugin as $jsFile) {
        $jsFile = $jsFile->getCallBackFile();
        if(is_file($jsFile)) {
            $jsFile = file_get_contents($jsFile);
            if(!empty($jsFile)) {
                $jsFunction[] = $jsFile;
            }
        }
    }
}
$oHeadPublisher->assign( 'openReassignCallback', $jsFunction );
G::RenderPage( 'publish', 'extJs' );

function getCategoryArray ()
{
    global $oAppCache;
    require_once 'classes/model/ProcessCategory.php';
    $category[] = array ("",G::LoadTranslation( "ID_ALL_CATEGORIES" )
    );

    $criteria = new Criteria( 'workflow' );
    $criteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_UID );
    $criteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_NAME );
    $criteria->addAscendingOrderByColumn(ProcessCategoryPeer::CATEGORY_NAME);

    $dataset = ProcessCategoryPeer::doSelectRS( $criteria );
    $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $dataset->next();

    while ($row = $dataset->getRow()) {
        $category[] = array ($row['CATEGORY_UID'],$row['CATEGORY_NAME']);
        $dataset->next();
    }
    return $category;
}

function getStatusArray($action, $userUid)
{
    $status = array();
    $aStatus = Application::$app_status_values;
    $status[] = array('', G::LoadTranslation('ID_ALL_STATUS'));
    foreach ($aStatus as $key => $value) {
        if ($action == 'search') {
            $status[] =  array ($value, G::LoadTranslation( 'ID_CASES_STATUS_' . $key ));
        } else {
            $status[] =  array ($key, G::LoadTranslation( 'ID_CASES_STATUS_' . $key ));
        }
    }
    return $status;
}

/**
 * get the list configuration headers of the cases checked for reassign, for the
 * reassign cases list.
 */
function getReassignList ()
{
    $caseColumns = array ();
    $caseColumns[] = array ('header' => '#','dataIndex' => 'APP_NUMBER','width' => 40);
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_SUMMARY' ),'dataIndex' => 'CASE_SUMMARY','width' => 45,'hidden' => true
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_CASES_NOTES' ),'dataIndex' => 'CASE_NOTES_COUNT','width' => 45,'hidden' => true
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_CASE' ),'dataIndex' => 'APP_TITLE','width' => 100,'hidden' => true
    );
    $caseColumns[] = array ('header' => 'CaseId','dataIndex' => 'APP_UID','width' => 200,'hidden' => true,'hideable' => false
    );
    $caseColumns[] = array ('header' => 'User','dataIndex' => 'USR_UID','width' => 200,'hidden' => true,'hideable' => false
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_TASK' ),'dataIndex' => 'APP_TAS_TITLE','width' => 120
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_PROCESS' ),'dataIndex' => 'APP_PRO_TITLE','width' => 120
    );
    $caseColumns[] = array ('header' => 'Reassigned Uid','dataIndex' => 'APP_REASSIGN_USER_UID','width' => 120,'hidden' => true,'hideable' => false
    );
    $caseColumns[] = array ('header' => 'Reassigned Uid','dataIndex' => 'TAS_UID','width' => 120,'hidden' => true,'hideable' => false
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_ASSIGNED_TO' ),'dataIndex' => 'APP_CURRENT_USER','width' => 170
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_REASSIGNED_TO' ),'dataIndex' => 'APP_REASSIGN_USER','width' => 170
    );
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_REASON' ),'dataIndex' => 'NOTE_REASON','width' => 170
    );
    $caseColumns[] = array('header' => G::LoadTranslation('ID_NOTIFY'), 'dataIndex' => 'NOTIFY_REASSIGN', 'width' => 100
    );

    $caseReaderFields = array ();
    $caseReaderFields[] = array ('name' => 'APP_NUMBER');
    $caseReaderFields[] = array ('name' => 'APP_TITLE');
    $caseReaderFields[] = array ('name' => 'APP_UID');
    $caseReaderFields[] = array ('name' => 'USR_UID');
    $caseReaderFields[] = array ('name' => 'APP_TAS_TITLE');
    $caseReaderFields[] = array ('name' => 'APP_PRO_TITLE');
    $caseReaderFields[] = array ('name' => 'APP_REASSIGN_USER_UID');
    $caseReaderFields[] = array ('name' => 'TAS_UID');
    $caseReaderFields[] = array ('name' => 'APP_REASSIGN_USER');
    $caseReaderFields[] = array ('name' => 'CASE_SUMMARY');
    $caseReaderFields[] = array ('name' => 'CASE_NOTES_COUNT');
    $caseReaderFields[] = array ('name' => 'APP_CURRENT_USER');

    return array ('caseColumns' => $caseColumns,'caseReaderFields' => $caseReaderFields,'rowsperpage' => 20,'dateformat' => 'M d, Y'
    );
}

function getReassignUsersList ()
{
    $caseColumns = array ();

    $caseReaderFields = array ();
    $caseReaderFields[] = array ('name' => 'userUid'
    );
    $caseReaderFields[] = array ('name' => 'userFullname'
    );

    return array ('caseColumns' => $caseColumns,'caseReaderFields' => $caseReaderFields,'rowsperpage' => 20,'dateformat' => 'M d, Y'
    );
}

/**
 * loads the PM Table field list from the database based in an action parameter
 * then assemble the List of fields with these data, for the configuration in cases list.
 *
 * @param String $action
 * @return Array $config
 *
 */
function getAdditionalFields($action, $confCasesList = array())
{
    $config = new Configurations();
    $arrayConfig = $config->casesListDefaultFieldsAndConfig($action);

    if (is_array($confCasesList) && count($confCasesList) > 0 && isset($confCasesList["second"]) && count($confCasesList["second"]["data"]) > 0) {
        //For the case list builder in the enterprise plugin
        $caseColumns = array();
        $caseReaderFields = array();
        $caseReaderFieldsAux = array();

        foreach ($confCasesList["second"]["data"] as $index1 => $value1) {
            $arrayField = $value1;

            if ($arrayField["fieldType"] != "key" && $arrayField["name"] != "USR_UID" && $arrayField["name"] != "PREVIOUS_USR_UID") {
                $arrayAux = array();

                foreach ($arrayField as $index2 => $value2) {
                    if ($index2 != "gridIndex" && $index2 != "fieldType") {
                        $indexAux = $index2;
                        $valueAux = $value2;

                        switch ($index2) {
                            case "name":
                                $indexAux = "dataIndex";
                                break;
                            case "label":
                                $indexAux = "header";

                                if (preg_match("/^\*\*(.+)\*\*$/", $value2, $arrayMatch)) {
                                    $valueAux = G::LoadTranslation($arrayMatch[1]);
                                }
                                break;
                        }
                        $arrayAux[$indexAux] = $valueAux;
                    }
                }

                $caseColumns[] = $arrayAux;
                $caseReaderFields[] = array("name" => $arrayField["name"]);

                $caseReaderFieldsAux[] = $arrayField["name"];
            }
        }
        foreach ($arrayConfig["caseReaderFields"] as $index => $value) {
            if (!in_array($value["name"], $caseReaderFieldsAux)) {
                $caseReaderFields[] = $value;
            }
        }

        $arrayConfig = array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => $confCasesList["rowsperpage"], "dateformat" => $confCasesList["dateformat"]);
    }

    return $arrayConfig;
}

/**
 * This function define the possibles columns for apply the specific search
 * @return array $filters values of the dropdown
 */
function getColumnsSearchArray ()
{
    $filters = [];
    $filters[] = ['APP_TITLE', G::LoadTranslation('ID_CASE_TITLE')];
    $filters[] = ['APP_NUMBER', G::LoadTranslation('ID_CASE_NUMBER')];
    $filters[] = ['TAS_TITLE', G::LoadTranslation('ID_TASK')];
    return $filters;
}

/*----------------------------------********---------------------------------*/
