<?php

$DYN_UID = $_GET["dyn_uid"];
$_SESSION['PROCESS'] = $_GET["prj_uid"];
$a = new PmDynaform(array("CURRENT_DYNAFORM" => $DYN_UID));
$a->lang = null;
$a->printPmDynaform();
