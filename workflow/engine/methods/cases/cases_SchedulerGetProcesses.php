<?php
//$oUserId = '2963666854afbb1cea372c4011254883';
$oUserId = $_POST['USR_UID'];
$process = isset( $_POST['PRO_UID'] ) ? $_POST['PRO_UID'] : $_SESSION['PROCESS'];
//echo '<select style="width: 300px;" readOnly name="form[PRO_UID]" id="form[PRO_UID]" class="module_app_input___gray" required="1" onChange="loadTasksDropdown(this.value,\''.$oUserId.'\');">';
require_once ("classes/model/TaskPeer.php");
require_once ("classes/model/ProcessPeer.php");
require_once ("classes/model/TaskUserPeer.php");
G::LoadClass( 'Content' );

$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( ProcessPeer::PRO_UID );
$oCriteria->setDistinct();
$oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
$oCriteria->addJoin( ProcessPeer::PRO_UID, TaskPeer::PRO_UID, Criteria::LEFT_JOIN );
$oCriteria->addJoin( ProcessPeer::PRO_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN );
$oCriteria->addJoin( TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN );
$oCriteria->add( TaskUserPeer::USR_UID, $oUserId );
$oCriteria->add( TaskPeer::TAS_START, 'true' );
$oCriteria->add( ContentPeer::CON_CATEGORY, 'PRO_TITLE' );
$oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );
$oCriteria->addAnd( ProcessPeer::PRO_UID, $process );

$resultSet = TaskUserPeer::doSelectRS( $oCriteria );
while ($resultSet->next()) {
    $row = $resultSet->getRow();

    echo $row[1];
    echo "<input name=\"form[PRO_UID]\" id=\"form[PRO_UID]\" type=\"hidden\" value=\"" . $row[0] . "\"></input>";
    //var_dump($row);
}
//echo "</select>";

