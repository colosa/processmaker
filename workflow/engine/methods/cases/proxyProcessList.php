<?php
$callback = isset( $_POST['callback'] ) ? $_POST['callback'] : 'stcCallback1001';
$dir = isset( $_POST['dir'] ) ? $_POST['dir'] : 'DESC';
$sort = isset( $_POST['sort'] ) ? $_POST['sort'] : '';
$query = isset( $_POST['query'] ) ? $_POST['query'] : '';
//$action = isset($_GET['action']) ? $_GET['action'] : 'read';
$option = '';
if (isset( $_GET['t'] ))
    $option = $_GET['t'];
try {

    G::LoadClass( "BasePeer" );
    require_once ("classes/model/Process.php");
    require_once ("classes/model/AppCacheView.php");

    $sUIDUserLogged = $_SESSION['USER_LOGGED'];

    $Criteria = new Criteria( 'workflow' );

    $Criteria->clearSelectColumns();
    $Criteria->setDistinct();
    $Criteria->addSelectColumn( AppCacheViewPeer::PRO_UID );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_PRO_TITLE );

    if ($query != '') {
        $Criteria->add( AppCacheViewPeer::APP_PRO_TITLE, $query . '%', Criteria::LIKE );
    }

    $Criteria->add( AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL );
    $Criteria->add( AppCacheViewPeer::USR_UID, $sUIDUserLogged );

    //$totalCount = AppCacheViewPeer::doCount( $Criteria );


    if (isset( $limit ))
        $Criteria->setLimit( $limit );
    if (isset( $start ))
        $Criteria->setOffset( $start );

    if ($sort != '') {
        if ($dir == 'DESC')
            $Criteria->addDescendingOrderByColumn( $sort );
        else
            $Criteria->addAscendingOrderByColumn( $sort );
    }
    $oDataset = AppCacheViewPeer::doSelectRS( $Criteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();

    $result = array ();
    $rows = array ();
    $index = isset( $start ) ? $start : 0;
    while ($aRow = $oDataset->getRow()) {
        $aRow['index'] = ++ $index;
        $rows[] = $aRow;

        $oDataset->next();
    }
    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;

    print G::json_encode( $result );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}

