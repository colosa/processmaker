<?php


$filter = new InputFilter();
$_SESSION['USER_LOGGED'] = $filter->xssFilterHard($_SESSION['USER_LOGGED']);

$callback = isset($_POST['callback']) ? $_POST['callback'] : 'stcCallback1001';
$callback = $filter->xssFilterHard($callback);
$dir      = isset($_POST['dir'])    ? $_POST['dir']    : 'DESC';
$dir      = $filter->xssFilterHard($dir);
$sort     = isset($_POST['sort'])   ? $_POST['sort']   : '';
$sort     = $filter->xssFilterHard($sort);
$query    = isset($_POST['query']) ? $_POST['query'] : '';
$query    = $filter->xssFilterHard($query);
$option = '';

if ( isset($_GET['t'] ) ) {
    $option = $_GET['t'];
    $option = $filter->xssFilterHard($option);
}

try {

    $sUIDUserLogged = $_SESSION['USER_LOGGED'];
    $oCriteria = new Criteria('workflow');
    $oCriteria->clearSelectColumns ( );
    $oCriteria->setDistinct();
    $oCriteria->addSelectColumn ( AdditionalTablesPeer::ADD_TAB_UID );
    $oCriteria->addSelectColumn ( AdditionalTablesPeer::ADD_TAB_NAME );
    $oCriteria->addSelectColumn ( FieldsPeer::FLD_NAME );
    if ( $query != '' ) {
        $oCriteria->add (AdditionalTablesPeer::ADD_TAB_NAME, $query . '%', Criteria::LIKE);
    }
    $oCriteria->addJoin(AdditionalTablesPeer::ADD_TAB_UID, FieldsPeer::ADD_TAB_UID);
    $oCriteria->add (AdditionalTablesPeer::DBS_UID, 'workflow', CRITERIA::EQUAL );
    $oCriteria->add (FieldsPeer::FLD_NAME, 'APP_UID', CRITERIA::EQUAL );

    if (isset($limit)) {
        $oCriteria->setLimit($limit);
    }

    if (isset($start)) {
        $oCriteria->setOffset($start);
    }

    if ($sort != '') {
        if ($dir == 'DESC') {
            $oCriteria->addDescendingOrderByColumn( $sort );
        } else {
            $oCriteria->addAscendingOrderByColumn( $sort );
        }
    }

    $oDataset = AdditionalTablesPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $result = array();
    $rows = array();
    $index =  isset($start) ? $start : 0;

    while ($aRow = $oDataset->getRow()) {
        $aRow['index'] = ++$index;
        $rows[] = $aRow;
        $oDataset->next();
    }
    $result['totalCount'] = count($rows);
    $result['data'] = $rows;
    print G::json_encode( $result );
} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::json_encode( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) ) );
}

