<?php
//get the action from GET or POST, default is todo
$action = isset( $_GET['action'] ) ? $_GET['action'] : (isset( $_POST['action'] ) ? $_POST['action'] : 'todo');
//fix a previous inconsistency
if ($action == 'selfservice') {
    $action = 'unassigned';
    //if ( $action == 'sent' ) $action = 'participated';
}

G::LoadClass("BasePeer");
G::LoadClass("configuration");
//require_once ("classes/model/Fields.php");
//require_once ("classes/model/AppCacheView.php");
//require_once ("classes/model/Process.php");
//require_once ("classes/model/Users.php");

$oHeadPublisher = & headPublisher::getSingleton();
// oHeadPublisher->setExtSkin( 'xtheme-blue');
//get the configuration for this action
$conf = new Configurations();
try {
    // the setup for search is the same as the Sent (participated)
    $confCasesList = $conf->getConfiguration( 'casesList', ($action == 'search' || $action == 'simple_search') ? 'search' : $action );
    $generalConfCasesList = $conf->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
} catch (Exception $e) {
    $confCasesList = array ();
    $generalConfCasesList = array ();
}

// reassign header configuration
$confReassignList = getReassignList();

// evaluates an action and the configuration for the list that will be rendered
$config = getAdditionalFields( $action, (class_exists( 'enterprisePlugin' ) ? $confCasesList : array ()) );
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

if ($action == 'draft' /* &&  $action == 'cancelled' */) {
    //array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'deleteLink' ) );
}
if ($action == 'selfservice') {
    array_unshift( $columns, array ('header' => '','width' => 50,'sortable' => false,'id' => 'viewLink') );
}

if ($action == 'paused') {
    //array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'unpauseLink' ) );
}
/*
  if ( $action == 'to_reassign' ) {
    array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'reassignLink' ) );
  }
*/
//  if ( $action == 'cancelled' ) {
//    array_unshift ( $columns, array( 'header'=> '', 'width'=> 50, 'sortable'=> false, 'id'=> 'reactivateLink' ) );
//  }

$userUid = (isset( $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] != '') ? $_SESSION['USER_LOGGED'] : null;
$oAppCache = new AppCacheView();
$oAppCache->confCasesList = $confCasesList;
$solrEnabled = 0;
if ($action == "todo" || $action == "draft" || $action == "sent" || $action == "selfservice" ||
    $action == "unassigned" || $action == "search") {
    $solrConfigured = ($solrConf = System::solrEnv()) !== false ? 1 : 0;
    if ($solrConfigured == 1) {
        G::LoadClass('AppSolr');
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
$processes[] = array ('',G::LoadTranslation( 'ID_ALL_PROCESS' ));
$status = getStatusArray( $action, $userUid );
$category = getCategoryArray();
$users = getUserArray( $action, $userUid );
$allUsers = getAllUsersArray( $action );

$oHeadPublisher->assign( 'reassignReaderFields', $reassignReaderFields ); //sending the fields to get from proxy
$oHeadPublisher->addExtJsScript( 'cases/reassignList', false );
$oHeadPublisher->assign( 'pageSize', $pageSize ); //sending the page size
$oHeadPublisher->assign( 'columns', $columns ); //sending the columns to display in grid
$oHeadPublisher->assign( 'readerFields', $readerFields ); //sending the fields to get from proxy
$oHeadPublisher->assign( 'reassignColumns', $reassignColumns ); //sending the columns to display in grid
$oHeadPublisher->assign( 'action', $action ); //sending the action to make
$oHeadPublisher->assign( 'PMDateFormat', $dateFormat ); //sending the fields to get from proxy
$oHeadPublisher->assign( 'statusValues', $status ); //Sending the listing of status
$oHeadPublisher->assign( 'processValues', $processes ); //Sending the listing of processes
$oHeadPublisher->assign( 'categoryValues', $category ); //Sending the listing of categories
$oHeadPublisher->assign( 'userValues', $users ); //Sending the listing of users
$oHeadPublisher->assign( 'allUsersValues', $allUsers ); //Sending the listing of all users
$oHeadPublisher->assign( 'solrEnabled', $solrEnabled ); //Sending the status of solar

//menu permissions
/*$c = new Criteria('workflow');
  $c->clearSelectColumns();
  $c->addSelectColumn( AppThreadPeer::APP_THREAD_PARENT );
  $c->add(AppThreadPeer::APP_UID, $APP_UID );
  $c->add(AppThreadPeer::APP_THREAD_STATUS , 'OPEN' );
  $cnt = AppThreadPeer::doCount($c);*/
$cnt = '';
$menuPerms = '';
$menuPerms = $menuPerms . ($RBAC->userCanAccess( 'PM_REASSIGNCASE' ) == 1) ? 'R' : ''; //can reassign case
$oHeadPublisher->assign( '___p34315105', $menuPerms ); // user menu permissions
G::LoadClass( 'configuration' );
$c = new Configurations();

//$oHeadPublisher->addExtJsScript('cases/caseUtils', true);
$oHeadPublisher->addExtJsScript( 'app/main', true );
$oHeadPublisher->addExtJsScript( 'cases/casesList', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'cases/casesListExtJs' ); //adding a html file  .html.
$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
G::RenderPage( 'publish', 'extJs' );

function getUserArray ($action, $userUid)
{
    global $oAppCache;
    $status = array ();

    $users[] = array ("",G::LoadTranslation( "ID_ALL_USERS" ));
    $users[] = array ("CURRENT_USER",G::LoadTranslation( "ID_CURRENT_USER" ));

    //now get users, just for the Search action
    switch ($action) {
        case 'search_simple':
        case 'search':
            $cUsers = new Criteria( 'workflow' );
            $cUsers->clearSelectColumns();
            $cUsers->addSelectColumn( UsersPeer::USR_UID );
            $cUsers->addSelectColumn( UsersPeer::USR_FIRSTNAME );
            $cUsers->addSelectColumn( UsersPeer::USR_LASTNAME );
            $oDataset = UsersPeer::doSelectRS( $cUsers );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $users[] = array ($aRow['USR_UID'],$aRow['USR_LASTNAME'] . ' ' . $aRow['USR_FIRSTNAME']);
                $oDataset->next();
            }
            break;
        default:
            return $users;
            break;
    }
    return $users;
}

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

function getAllUsersArray ($action)
{
    global $oAppCache;
    $status = array ();
    $users[] = array ("CURRENT_USER",G::LoadTranslation( "ID_CURRENT_USER" )
    );
    $users[] = array ("",G::LoadTranslation( "ID_ALL_USERS" )
    );

    if ($action == 'to_reassign') {
        //now get users, just for the Search action
        $cUsers = $oAppCache->getToReassignListCriteria(null);
        $cUsers->addSelectColumn( AppCacheViewPeer::USR_UID );

        if (g::MySQLSintaxis()) {
            $cUsers->addGroupByColumn( AppCacheViewPeer::USR_UID );
        }

        $cUsers->addAscendingOrderByColumn( AppCacheViewPeer::APP_CURRENT_USER );
        $oDataset = AppCacheViewPeer::doSelectRS( $cUsers );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $users[] = array ($aRow['USR_UID'],$aRow['APP_CURRENT_USER']);
            $oDataset->next();
        }
    }
    return $users;
}

function getStatusArray ($action, $userUid)
{
    global $oAppCache;
    $status = array ();
    $status[] = array ('',G::LoadTranslation( 'ID_ALL_STATUS' ));
    //get the list based in the action provided
    switch ($action) {
        case 'sent':
            $cStatus = $oAppCache->getSentListProcessCriteria( $userUid ); // a little slow
            break;
        case 'simple_search':
        case 'search':
            $cStatus = new Criteria( 'workflow' );
            $cStatus->clearSelectColumns();
            $cStatus->setDistinct();
            $cStatus->addSelectColumn( ApplicationPeer::APP_STATUS );
            $oDataset = ApplicationPeer::doSelectRS( $cStatus );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $status[] = array ($aRow['APP_STATUS'],G::LoadTranslation( 'ID_CASES_STATUS_' . $aRow['APP_STATUS'] )
                ); //here we can have a translation for the status ( the second param)
                $oDataset->next();
            }
            return $status;
            break;
        case 'selfservice':
            $cStatus = $oAppCache->getUnassignedListCriteria( $userUid );
            break;
        case 'paused':
            $cStatus = $oAppCache->getPausedListCriteria( $userUid );
            break;
        case 'to_revise':
            $cStatus = $oAppCache->getToReviseListCriteria( $userUid );
            //           $cStatus       = $oAppCache->getPausedListCriteria($userUid);
            break;
        case 'to_reassign':
            $cStatus = $oAppCache->getToReassignListCriteria($userUid);
            break;
        case 'todo':
        case 'draft':
        case 'gral':
            //      case 'to_revise' :
        default:
            return $status;
            break;
    }

    //get the status for this user in this action only for participated, unassigned, paused
    //    if ( $action != 'todo' && $action != 'draft' && $action != 'to_revise') {
    if ($action != 'todo' && $action != 'draft') {
        //$cStatus = new Criteria('workflow');
        $cStatus->clearSelectColumns();
        $cStatus->setDistinct();
        $cStatus->addSelectColumn( AppCacheViewPeer::APP_STATUS );
        $oDataset = AppCacheViewPeer::doSelectRS( $cStatus );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $status[] = array ($aRow['APP_STATUS'],G::LoadTranslation( 'ID_CASES_STATUS_' . $aRow['APP_STATUS'] ));
            //here we can have a translation for the status ( the second param)
            $oDataset->next();
        }
    }
    return $status;
}

//these getXX function gets the default fields in casesListSetup

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
    $caseColumns[] = array ('header' => G::LoadTranslation( 'ID_REASSIGN_TO' ),'dataIndex' => 'APP_REASSIGN_USER','width' => 170
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

    if (is_array($confCasesList) && count($confCasesList) > 0 && count($confCasesList["second"]["data"]) > 0) {
        //For the case list builder in the enterprise plugin
        $caseColumns = array();
        $caseReaderFields = array();
        $caseReaderFieldsAux = array();

        foreach ($confCasesList["second"]["data"] as $index1 => $value1) {
            $arrayField = $value1;

            if ($arrayField["fieldType"] != "key") {
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

