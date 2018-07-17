<?php

$oUserId = $_POST['USR_UID'];
$process = isset( $_POST['PRO_UID'] ) ? $_POST['PRO_UID'] : $_SESSION['PROCESS'];


$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( ProcessPeer::PRO_UID );
$oCriteria->setDistinct();
$oCriteria->addSelectColumn( ProcessPeer::PRO_TITLE );
$oCriteria->addJoin( ProcessPeer::PRO_UID, TaskPeer::PRO_UID, Criteria::LEFT_JOIN );
$oCriteria->addJoin( TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN );
$oCriteria->add( TaskUserPeer::USR_UID, $oUserId );
$oCriteria->add( TaskPeer::TAS_START, 'true' );
$oCriteria->addAnd( ProcessPeer::PRO_UID, $process );

$resultSet = TaskUserPeer::doSelectRS( $oCriteria );
while ($resultSet->next()) {
    $row = $resultSet->getRow();

    echo $row[1];
    echo "<input name=\"form[PRO_UID]\" id=\"form[PRO_UID]\" type=\"hidden\" value=\"" . $row[0] . "\"></input>";
    //var_dump($row);
}
//echo "</select>";

