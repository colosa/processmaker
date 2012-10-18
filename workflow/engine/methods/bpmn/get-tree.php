<?php

try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['tid'] )) {
        $rows = $oProcessMap->getExtStepsCriteria( $_GET['tid'] ); //Getting all assigned steps on a task
        array_shift( $rows );
    }

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

$steps = array ();

//@@stepsChildren is an array that stores before and after triggers tree data for a step
$stepsChildren = array ();
$assignChildren = array ();

//@@assignTaskChildren is an array that stores Before Assignment,Before Derivation and After Derivation triggers tree data for assigned task
$assignTaskChildren = array ();

//Creating steps array for populating tree depending on count of assigned steps to a task
for ($i = 0; $i < count( $rows ); $i ++) {
    //Getting beforeTriggersCount for a step
    $beforeTriggers = $oProcessMap->getStepTriggersCriteria( $rows[$i]['STEP_UID'], $_GET['tid'], 'BEFORE' );
    $beforeTriggersCount = StepTriggerPeer::doCount( $beforeTriggers );

    //Getting afterTriggersCount for a step
    $afterTriggers = $oProcessMap->getStepTriggersCriteria( $rows[$i]['STEP_UID'], $_GET['tid'], 'AFTER' );
    $afterTriggersCount = StepTriggerPeer::doCount( $afterTriggers );

    $iTotal = $beforeTriggersCount + $afterTriggersCount;

    //Tree level 3 nodes i.e. final nodes (Leaf Nodes)
    $beforeTriggerChildren[0] = array ('text' => 'Assign / Show Before Triggers','id' => $rows[$i]['STEP_UID'] . '|BEFORE','leaf' => true);
    $afterTriggerChildren[0] = array ('text' => 'Assign / Show  After Triggers','id' => $rows[$i]['STEP_UID'] . '|AFTER','leaf' => true);

    //Tree level 2 nodes i.e. Before and After Triggers for level 1 nodes
    $stepsChildren[0] = array ('text' => 'Before - Triggers (' . $beforeTriggersCount . ')',
    //'id'        => $rows[$i]['STEP_UID'].'-BEFORE',
    'children' => $beforeTriggerChildren);
    $stepsChildren[1] = array ('text' => 'After - Triggers (' . $afterTriggersCount . ')',
    //'id'        => $rows[$i]['STEP_UID'].'-AFTER',
    'children' => $afterTriggerChildren);

    //Tree level 1 nodes (Main steps)
    $steps[] = array ('text' => $rows[$i]['STEP_TITLE'] . ' - Triggers (' . $iTotal . ')',
    //'id'        => 'ssaas',
    'children' => $stepsChildren);
}

//Creating tree for Assign Task Step
$beforeAssignmentChildren[] = array ('text' => 'Assign / Show Triggers','id' => '-1|BEFORE','leaf' => true);
$beforeDerivationChildren[] = array ('text' => 'Assign / Show Triggers','id' => '-2|BEFORE','leaf' => true);
$afterDerivationChildren[] = array ('text' => 'Assign / Show Triggers','id' => '-2|AFTER','leaf' => true);

//Getting counts for Before Assignment,Before Derivation and After Derivation triggers for a step
$beforeAssignmentTriggers = $oProcessMap->getStepTriggersCriteria( '-1', $_GET['tid'], 'BEFORE' );
$beforeAssignmentTriggersCount = StepTriggerPeer::doCount( $beforeAssignmentTriggers );

$beforeDerivationTriggers = $oProcessMap->getStepTriggersCriteria( '-2', $_GET['tid'], 'BEFORE' );
$beforeDerivationTriggersCount = StepTriggerPeer::doCount( $beforeDerivationTriggers );

$afterDerivationTriggers = $oProcessMap->getStepTriggersCriteria( '-2', $_GET['tid'], 'AFTER' );
$afterDerivationTriggersCount = StepTriggerPeer::doCount( $afterDerivationTriggers );

$iTotal = $beforeAssignmentTriggersCount + $beforeDerivationTriggersCount + $afterDerivationTriggersCount;
$assignTaskChildren[] = array ('text' => 'Before Assignment - Triggers (' . $beforeAssignmentTriggersCount . ')','children' => $beforeAssignmentChildren);
$assignTaskChildren[] = array ('text' => 'Before Derivation - Triggers (' . $beforeDerivationTriggersCount . ')','children' => $beforeDerivationChildren);
$assignTaskChildren[] = array ('text' => 'After Derivation - Triggers (' . $afterDerivationTriggersCount . ')','children' => $afterDerivationChildren);

//Adding last value in an array for "Assign Task"
$steps[] = array ('text' => '[ Assign Task ] - Triggers (' . $iTotal . ')',
//'id'   => $rows[$i]['STEP_UID'],
'children' => $assignTaskChildren
);

/* $nodes = "[{
    text: 'Step 1 - Triggers (0)',
    cls:  'blank',
    iconCls:  'blank',
    children: [{
        text: 'Before - Triggers (0)',
        cls:  'blank',
        iconCls:  'blank',
        children: [{
            text: 'Assign Before Trigger',
            leaf: true,
        }]
    },{
        text: 'After - Triggers (0)',
        leaf: true,
        cls:  'blank',
        iconCls:  'blank',
    }]
},{
    text: 'Step 2 - Triggers (0)',

    children: [{
        text: 'Before - Triggers (0)',
        leaf: true,

    },{
        text: 'After - Triggers (0)',
        leaf: true,

    }]
},{
    text: 'Assign Task - Triggers(0)',

    children: [{
        text: 'Before Assigment - Triggers(0)',
        leaf: true,

    },{
        text: 'Before Derivation - Triggers(0)',
        leaf: true,

    },{
        text: 'After Derivation - Triggers(0)',
        leaf: true,

    }]
}]";*/
//echo $nodes;
echo G::json_encode( $steps );

