<?php
function addNodox($obj, $padre, $indice, $contenido='', $atributos='')
{
    if (is_object($padre)) {
        if ($contenido=='') {
            $nodo = $obj->createElement($indice);
        } else {
            $nodo = $obj->createElement($indice, $contenido);
        }
        $padre->appendChild($nodo);
    } else {
        if ($contenido=='') {
            $nodo = $obj->createElement($indice);
        } else {
            $nodo = $obj->createElement($indice, $contenido);
        }

        $obj->appendChild($nodo);
    }

    if (is_array($atributos)) {
        foreach ($atributos as $key => $value) {
            $atributo = $obj->createAttribute($key);
            $nodo->appendChild($atributo);

            $texto = $obj->createTextNode($value);
            $atributo->appendChild($texto);
        }
    }
    return $nodo;
}

function derivationRules($aRoute, $doc, $nodo_derivationrule)
{
    $tam = count($aRoute);
    $c=0;
    switch ($aRoute[$c]['ROU_TYPE'])
    {
        case 'SEQUENTIAL':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'Sequential', '', '');
            $nodo_nexttask  = addNodox($doc, $nodo_routeType, 'NextTask', '', '');
            if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '',
                    array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
            } else {
                $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
            }
            break;
        case 'EVALUATE':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'Evaluations', '', '');
            while ($c < $tam) {
                $nodo_evaluation= addNodox($doc, $nodo_routeType, 'Evaluation', '', array('Condition'=> $aRoute[$c]['ROU_CONDITION']));
                $nodo_nexttask  = addNodox($doc, $nodo_evaluation, 'NextTask', '', '');
                if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '', array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
                } else {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
                }
                $c++;
            }
            break;
        case 'SELECT':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'Selections', '', '');
            while ($c < $tam) {
                $nodo_selection= addNodox($doc, $nodo_routeType, 'Selection', '', array('Description'=> $aRoute[$c]['ROU_CONDITION']));
                $nodo_nexttask  = addNodox($doc, $nodo_selection, 'NextTask', '', '');
                if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '', array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
                } else {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
                }
                $c++;
            }
            break;
        case 'PARALLEL':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'ParallelForks', '', '');
            while ($c < $tam) {
                $nodo_parallelfork= addNodox($doc, $nodo_routeType, 'ParallelFork', '', '');
                $nodo_nexttask  = addNodox($doc, $nodo_parallelfork, 'NextTask', '', '');

                if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '', array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
                } else {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
                }
                $c++;
            }
            break;
        case 'PARALLEL-BY-EVALUATION':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'ParallelForksByEvaluation', '', '');
            while ($c < $tam) {
                $nodo_evaluation= addNodox($doc, $nodo_routeType, 'Evaluation', '', array('Condition'=> $aRoute[$c]['ROU_CONDITION']));
                $nodo_nexttask  = addNodox($doc, $nodo_evaluation, 'NextTask', '', '');
                if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '', array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
                } else {
                    $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
                }
                $c++;
            }
            break;
        case 'SEC-JOIN':
            $nodo_routeType = addNodox($doc, $nodo_derivationrule, 'ParallelJoin', '', '');
            $nodo_nexttask  = addNodox($doc, $nodo_routeType, 'NextTask', '', '');
            if ($aRoute[$c]['ROU_NEXT_TASK'] != -1) {
                $nodo_taskref = addNodox($doc, $nodo_nexttask, 'TaskRef', '', array('TaskId'=> 'ID'.$aRoute[$c]['ROU_NEXT_TASK']));
            } else {
                $nodo_taskref = addNodox($doc, $nodo_nexttask, 'End', '', '');
            }
            break;
    }
}

/****-_--__---___----___---__--_-****/

G::LoadClass('tasks');
require_once 'classes/model/Process.php';

$doc = new DOMDocument('1.0', 'UTF-8');
$nodo_padre = addNodox($doc, '', 'Processes', '', array('xmlns:xsi'=>'http://www.w3.org/2001/XMLSchema-instance','xsi:noNamespaceSchemaLocation'=>'ColosaSchema.xsd'));

$aProcesses   = array();
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(ProcessPeer::PRO_UID);
//$oCriteria->add(ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL);
//$oCriteria->add(ProcessPeer::PRO_UID, '946679494980c3d0ba0814088444708');
$oDataset = ProcessPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$oProcess = new Process();

while ($aRow = $oDataset->getRow()) {
    $aProcess     = $oProcess->load($aRow['PRO_UID']);
    $nodo_process = addNodox($doc, $nodo_padre, 'Process', '', array('Title'=> $aProcess['PRO_TITLE'],'Description'=> $aProcess['PRO_DESCRIPTION']));
    $nodo_tasks = addNodox($doc, $nodo_process, 'Tasks', '', '');

    $oTask = new Tasks();
    $aTasks= $oTask->getAllTasks($aProcess['PRO_UID']);
    foreach ($aTasks as $key => $value) {
        //print_r($value); echo "<br>";
        $aRoute = $oTask->getRoute($aProcess['PRO_UID'], $value['TAS_UID']);
        //print_r($aRoute[0]['ROU_UID']); echo "<hr>";
        /*foreach($aRoute as $k => $v)
        echo $k."-->".$v."<br>";
        */
        if ($value['TAS_TYPE']=='NORMAL') {
            $ini = ($value['TAS_START']=='TRUE') ? 'true' : 'false';

            $nodo_task = addNodox($doc, $nodo_tasks, 'Task', '', array('Title'=> $value['TAS_TITLE'],'Description'=> $value['TAS_DESCRIPTION'],'Id'=> 'ID'.$value['TAS_UID'],'StartingTask'=> $ini));
            $nodo_coordinates = addNodox($doc, $nodo_task, 'Coordinates', '', array('XCoordinate'=> $value['TAS_POSX'],'YCoordinate'=> $value['TAS_POSY']));
            $nodo_derivationrule = addNodox($doc, $nodo_task, 'DerivationRule', '', '');

            derivationRules($aRoute, $doc, $nodo_derivationrule);

            $nodo_assignmentrules = addNodox($doc, $nodo_task, 'AssignmentRules', '', '');
            $nodo_cyclicalassignment = addNodox($doc, $nodo_assignmentrules, 'CyclicalAssignment', '', '');
            $nodo_timingcontrol = addNodox($doc, $nodo_task, 'TimingControl', '', array('TaskDuration'=> $value['TAS_DURATION']));
            $nodo_permissions = addNodox($doc, $nodo_task, 'Permissions', '', '');
            $nodo_caselabels = addNodox($doc, $nodo_task, 'CaseLabels', '', '');
            $nodo_notifications = addNodox($doc, $nodo_task, 'Notifications', '', '');
        } else {
            require_once ( "classes/model/SubProcess.php" );
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(SubProcessPeer::PRO_PARENT, $value['PRO_UID']);
            $oCriteria->add(SubProcessPeer::TAS_PARENT, $value['TAS_UID']);
            $oDataset = SubProcessPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            $nodo_task = addNodox($doc, $nodo_tasks, 'SubProcess', '', array('Title'=> $value['TAS_TITLE'],'Description'=> $value['TAS_DESCRIPTION'],'Id'=> 'ID'.$value['TAS_UID'], 'ProcessRef'=>$aRow['PRO_UID']));
            $nodo_coordinates = addNodox($doc, $nodo_task, 'Coordinates', '', array('XCoordinate'=> $value['TAS_POSX'],'YCoordinate'=> $value['TAS_POSY']));
            $nodo_derivationrule = addNodox($doc, $nodo_task, 'DerivationRule', '', '');

            derivationRules($aRoute, $doc, $nodo_derivationrule);
        }
    }
    $oDataset->next();
}
//die;
$doc->preserveWhiteSpace = false;
$doc->formatOutput   = true;
$doc->save(PATH_METHODS.'services/test_xpdl.xml');
echo "xml for xpdl creado!!!<br>";

