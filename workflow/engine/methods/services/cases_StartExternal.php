<?php
try {

    $oTask = new Task();
    $TaskFields = $oTask->load( $_POST['TASKS'] );

    $aDerivation['NEXT_TASK'] = $TaskFields;
    $oDerivation = new Derivation();
    $deriva = $oDerivation->getNextAssignedUser( $aDerivation );

    $oCase = new Cases();
    $aData = $oCase->startCase( $_POST['TASKS'], $deriva['USR_UID'] );

    $case = $oCase->loadCase( $aData['APPLICATION'], 1 );

    $Fields = array ();
    $Fields['APP_NUMBER'] = $case['APP_NUMBER'];
    $Fields['APP_PROC_STATUS'] = 'draft';
    $Fields['APP_DATA'] = $_POST['form'];
    $Fields['DEL_INDEX'] = 1;
    $Fields['TAS_UID'] = $_POST['TASKS'];
    //$Fields = $oCase->loadCase($aData['APPLICATION'], 1);
    $oCase->updateCase( $aData['APPLICATION'], $Fields );

    $s = 0;
    if (isset( $_SERVER['HTTP_REFERER'] )) {
        $dir = explode( '?', $_SERVER['HTTP_REFERER'] );
        if ($dir[1] == '__flag__=1') {
            $s = 1;
        } else {
            $dire = explode( '&', $dir[1] );
            for ($i = 0; $i <= count( $dire ); $i ++) {
                if ($dire[$i] == '__flag__=1')
                    $s = 1;
            }
        }

        //if(strpos($_SERVER['HTTP_REFERER'],'?') !== false)
        if ($s == 1) {
            G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
        } else {
            G::header( 'location: ' . $_SERVER['HTTP_REFERER'] . '?__flag__=1' );
        }
    } else
        echo G::LoadTranslation( 'ID_REQUEST_SENT' );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
}

