<select style="width: 300px;" name="form[TAS_UID]" id="form[TAS_UID]"
	class="module_app_input___gray" required="1">
<?php

$oUserId = $_POST['USR_UID'];
$oProcessId = $_POST['PRO_UID'];

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

