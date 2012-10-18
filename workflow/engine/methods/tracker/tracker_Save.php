<?php
if (isset( $_POST['form'] ))
    $sValue = $_POST['form']; //For old processmap
else
    $sValue = $_POST;

unset( $sValue['SAVE'] );
if (! isset( $sValue['CT_DERIVATION_HISTORY'] )) {
    $sValue['CT_DERIVATION_HISTORY'] = 0;
}
if (! isset( $sValue['CT_MESSAGE_HISTORY'] )) {
    $sValue['CT_MESSAGE_HISTORY'] = 0;
}
require_once 'classes/model/CaseTracker.php';
$oCaseTracker = new CaseTracker();
$oCaseTracker->update( $sValue );

