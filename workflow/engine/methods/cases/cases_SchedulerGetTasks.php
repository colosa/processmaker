<select style="width: 300px;" name="form[TAS_UID]" id="form[TAS_UID]"
	class="module_app_input___gray" required="1">
<?php
//$oUserId = '2963666854afbb1cea372c4011254883';
//$oProcessId = '9977730834afd2a0deecaf3040551794';
$oUserId = $_POST['USR_UID'];
$oProcessId = $_POST['PRO_UID'];
G::LoadClass( 'case' );
$oCase = new Cases();
$startTasks = $oCase->getStartCases( $oUserId );
foreach ($startTasks as $task) {
    if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $oProcessId)) {
        $taskValue = explode( '(', $task['value'] );
        $tasksLastIndex = count( $taskValue ) - 1;
        $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
        echo "<option value=\"" . $task['uid'] . "\">" . $taskValue[0] . "</option>";
    }
}
//print_r($startTasks);
//  echo "<option value=\"".$value."\">".$label."</option>";
?>
</select>
<?php

