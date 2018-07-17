<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$appUid    = isset($_POST["appUid"])? $_POST["appUid"] : "";
$dynUid    = isset($_POST["dynUid"])? htmlspecialchars($_POST["dynUid"]) : "";
$proUid    = isset($_POST["proUid"])? htmlspecialchars($_POST["proUid"]) : "";
$fieldName = isset($_POST["fieldName"])? $_POST["fieldName"] : "";

$filename = $proUid . PATH_SEP . $dynUid . ".xml";
$G_FORM = new xmlform();
$G_FORM->home = PATH_DYNAFORM;
$G_FORM->parseFile($filename, SYS_LANG, true);

//Load the variables
$oCase = new Cases();
$sqlQuery = null;
$array = array();
$aFields = $oCase->loadCase($appUid);

foreach ($G_FORM->fields as $key => $val) {
    if ($fieldName == $val->name) {
        if ($G_FORM->fields[$key]->sql != null) {
            $sqlQuery = G::replaceDataField($G_FORM->fields[$key]->sql, $aFields ["APP_DATA"]);
        }
        //$coma = "";
        //$data1 = "";
        if (is_array($val->options)) {
            foreach ($val->options as $key1 => $val1) {
                $array[] = array("value" => $key1, "text" => $val1);
            }
        }
    }
}

//echo ($sqlQuery);
if ($sqlQuery != null) {
    $aResult = executeQuery($sqlQuery);
    //var_dump($aResult);
    if ($aResult == "false" || $aResult == null) {
        $aResult = array();
    }
} else {
    $aResult = array();
}
//var_dump($aResult);
$arrayTmp = array();
foreach ($aResult as $field) {
    $i = 0;

    foreach ($field as $key => $value) {
        if ($i == 0) {
            $arrayTmp["value"] = $value;
            if (count($field) == 1) {
                $arrayTmp["text"]=$value;
            }
        }

        if ($i == 1) {
            $arrayTmp["text"] = $value;
        }
        $i++;
    }
    $array[] = $arrayTmp;
}

$response["records"] = $array;
$filter = new InputFilter();
echo G::json_encode($filter->xssFilterHard($response));
