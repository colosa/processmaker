 <?php

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}
try {

    $form = $_POST['form'];
    $CategoryUid = $form['CATEGORY_UID'];

    //we'are looking for data into process with this CategoryUid
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( ProcessPeer::PRO_CATEGORY, $CategoryUid );
    $oDataset = ProcessPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    while ($oDataset->next()) {
        $aDataProcess = $oDataset->getRow();
        $oCriteria1 = new Criteria( 'workflow' );
        $oCriteria1->add( ProcessPeer::PRO_CATEGORY, '' );
        $oCriteria2 = new Criteria( 'workflow' );
        $oCriteria2->add( ProcessPeer::PRO_UID, $aDataProcess['PRO_UID'] );
        BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );

    }

    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ProcessCategoryPeer::retrieveByPK( $CategoryUid );
    if ((is_object( $tr ) && get_class( $tr ) == 'ProcessCategory')) {
        $tr->delete();
    }

    G::Header( 'location: processCategoryList' );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
