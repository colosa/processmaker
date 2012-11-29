<?php
global $G_TMP_MENU;

$tmpId      = array();
$tmpTypes   = array();
$tmpEnabled = array();
$tmpOptions = array(); //link
$tmpLabels  = array();
$tmpJS      = array();
$tmpIcons   = array();
$tmpEClass  = array();

$i = 0;
$k = 0;

foreach ($G_TMP_MENU->Id as $index => $value) {
  if ($index == 3) { //Option CASES_INBOX
    $tmpId[$index + $k]      = "{menuId}_MNUCASE_01";
    $tmpTypes[$index + $k]   = "plugins";
    $tmpEnabled[$index + $k] = 1;
    $tmpOptions[$index + $k] = "../{phpClassName}/{phpClassName}Application"; //link
    $tmpLabels[$index + $k]  = "{className} application1";
    $tmpJS[$index + $k]      = null;
    $tmpIcons[$index + $k]   = null;
    $tmpEClass[$index + $k]  = null;
    
    $k = 1;
  }

  $tmpId[$index + $k]      = $G_TMP_MENU->Id[$index];
  $tmpTypes[$index + $k]   = $G_TMP_MENU->Types[$index];
  $tmpEnabled[$index + $k] = $G_TMP_MENU->Enabled[$index];
  $tmpOptions[$index + $k] = $G_TMP_MENU->Options[$index]; //link
  $tmpLabels[$index + $k]  = $G_TMP_MENU->Labels[$index];
  $tmpJS[$index + $k]      = $G_TMP_MENU->JS[$index];
  $tmpIcons[$index + $k]   = $G_TMP_MENU->Icons[$index];
  $tmpEClass[$index + $k]  = $G_TMP_MENU->ElementClass[$index];
  
  $i = $index + $k;
}

$i = $i + 1;

$tmpId[$i]      = "{menuId}_MNUCASE_02";
$tmpTypes[$i]   = "blockHeader";
$tmpEnabled[$i] = 1;
$tmpOptions[$i] = null; //link
$tmpLabels[$i]  = "{className} application2";
$tmpJS[$i]      = null;
$tmpIcons[$i]   = null;
$tmpEClass[$i]  = null;

$i = $i + 1;

$tmpId[$i]      = "{menuId}_MNUCASE_03";
$tmpTypes[$i]   = "plugins";
$tmpEnabled[$i] = 1;
$tmpOptions[$i] = "../{phpClassName}/{phpClassName}Application2"; //link
$tmpLabels[$i]  = "{className} application2";
$tmpJS[$i]      = null;
$tmpIcons[$i]   = null;
$tmpEClass[$i]  = null;

$i = $i + 1;

$tmpId[$i]      = "{menuId}_MNUCASE_04";
$tmpTypes[$i]   = "blockHeaderNoChild";
$tmpEnabled[$i] = 1;
$tmpOptions[$i] = "../{phpClassName}/{phpClassName}Application3"; //link
$tmpLabels[$i]  = "{className} application3";
$tmpJS[$i]      = null;
$tmpIcons[$i]   = null;
$tmpEClass[$i]  = null;

$G_TMP_MENU->Id      = $tmpId;
$G_TMP_MENU->Types   = $tmpTypes;
$G_TMP_MENU->Enabled = $tmpEnabled;
$G_TMP_MENU->Options = $tmpOptions; //link
$G_TMP_MENU->Labels  = $tmpLabels;
$G_TMP_MENU->JS      = $tmpJS;
$G_TMP_MENU->Icons   = $tmpIcons;
$G_TMP_MENU->ElementClass = $tmpEClass;
?>