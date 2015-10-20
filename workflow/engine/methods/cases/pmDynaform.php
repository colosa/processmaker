<?php

$DYN_UID = $_GET["dyn_uid"];
$_SESSION['PROCESS'] = $_GET["prj_uid"];
G::LoadClass('pmDynaform');
$a = new pmDynaform(array("CURRENT_DYNAFORM" => $DYN_UID));
$a->lang = null;
$a->printPmDynaform();
