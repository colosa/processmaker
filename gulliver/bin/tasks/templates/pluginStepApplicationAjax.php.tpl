<?php

try {
  //SYS_SYS     //Workspace name
  //PROCESS     //Process UID
  //APPLICATION //Case UID
  //INDEX       //Number delegation
  
  $oApp    = new Cases();
  $aFields = $oApp->loadCase($_SESSION["APPLICATION"]);
  $aData   = $aFields["APP_DATA"];
  
  $aResult = array();

  foreach ($aData as $index => $value) {
    $aResult[] = array("VARIABLE" => $index, "VALUE" => $value);
  }
  
  //echo "{success: " . true . ", resultTotal: " . count($aResult) . ", resultRoot: " . G::json_encode($aResult) . "}";
  echo G::json_encode(array("success" => true, "resultTotal" => count($aResult), "resultRoot" => $aResult));
} catch (Exception $e) {
  echo null;
}
?>