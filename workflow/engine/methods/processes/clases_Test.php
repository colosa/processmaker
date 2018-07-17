<?php

/**
 * clases_Test.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */

$dbc = new DBConnection;
$obj = new Derivation;

/* derivando el primer caso */

$frm['APP_UID'] = '44706CAEA62AE0';
$frm['DEL_INDEX'] = '1';
$obj->executeDerivation($frm);
die;

/* derivando el primer caso */

/* CREANDO UN NUEVO CASO */
/*
  $frm['TAS_UID'] ='246F2CD0D4C79E';
  $frm['USER_UID'] ='00000000000000000000000000000001';
  $obj->startCase($frm);
  die;

 */
/* CREANDO UN NUEVO CASO END */
/** Application */
//$frm['PRO_UID'] ='ssddsfse32dd23s';
/* 	$frm['APP_PARENT'] ='135165FDS54654FD';
  $frm['PRO_UID'] ='SSDDSFSE32DD23S';
  $frm['APP_STATUS'] ='DRAFT';
  $frm['APP_PROC_STATUS']='TEST';
  $frm['APP_PARALLEL']='NO';
  $frm['APP_TITLE']='MAUI';
 */




/* 	$translation2 = $obj->generateFileTranslation();
  print_r($translation2); */



/* step */
/* $frm['TAS_UID'] ='sss';
  $frm['STEP_NAME_OBJ'] ='DYNA';
 */
/* ReqDynaform */
/* 	$frm['REQ_DYN_TITLE'] ='sss';
  $frm['REQ_DYN_DESCRIPTION'] ='eee';
  $frm['REQ_DYN_FILENAME'] ='33';
  $frm['REQ_DYN_UID']='346BB3D981FD9E'; */



/* ReqDocument */
/* 	$frm['REQ_DOC_TITLE'] ='titulosssss';
  $frm['REQ_DOC_DESCRIPTION'] ='descripcions';
  //$frm['REQ_DOC_ORIGINAL'] ='1';
  $frm['REQ_DOC_UID']='646BB2F6BB2037';
 */
/* Task */
/* $frm['TAS_UID']='846BB16A0D9C7A';
  $frm['PRO_UID']='746B67A9CC9A0E';
  //$frm['TAS_TYPE'] ='332';
  $frm['TAS_TITLE'] ='titulito MAUI13ss';
  $frm['TAS_DESCRIPTION'] ='Descripci�n MAUI13';
  $frm['TAS_DEF_TITLE'] = "13";
  $frm['TAS_DEF_DESCRIPTION']  = "23";
  $frm['TAS_DEF_PROC_CODE'] = "33";
  $frm['TAS_DEF_MESSAGE'] = "43"; */

/** SwimlanesElements */
/*
  $frm['PRO_UID'] ='ssddsfse32dd23s';
  $frm['SWI_TEXT'] ='maui';
  $frm['SWI_TYPE'] ='TEXT';
  $frm['SWI_X'] ='2';
  $frm['SWI_UID']='746BB217D7805E';

 */
/** Route */
/*
  $frm['PRO_UID'] ='ssddsfse32dd23s';
  $frm['TAS_UID'] ='cooo';
  $frm['ROU_NEXT_TASK'] ='654FD65S4F65SD';
  $frm['ROU_SOURCEANCHOR'] ='2';
  $frm['ROU_UID']='746BB8411A9C14';
 */



/* PROCESS */
/* $frm['PRO_UID'] = '446BB1B36E17FE';
  $frm['PRO_TITLE'] = 'PERDERD';
  $frm['PRO_PARENT']='746B67A9CC9ADDDDDDDD0E'; */

/** END PROCESS */
/* MESSAGE */
/*
  $frm['PRO_UID'] = '446BB1B36E17FE';
  $frm['MESS_UID'] = '146CDAC097D35A';
  $frm['MESS_TYPE'] = 'HTMLS';
  $frm['MESS_TITLE'] = 't�tulo del mensaje';
  $frm['MESS_DESCRIPTION'] = 'estimado SrS.';

  /** END MESSAGE */
/* STEP */
/*
  $frm['PRO_UID'] = '446BB1B36E17FE';
  $frm['TAS_UID'] = '146CDAC097D35A';
  $frm['STEP_NAME_OBJ'] = 'HTM';
  $frm['STEP_TYPE_OBJ'] = 'OUTPUT_DOCUMENT';
  $frm['STEP_UID_OBJ'] = 'estimado SrS.';
 */
/** END MESSAGE */
/** Delegation */
//$frm['PRO_UID'] ='ssddsfse32dd23s';
/* $frm['APP_UID'] ='135165FDS54654FD';
  $frm['APP_PARENT'] ='135165FDS54654FD';
  $frm['PRO_UID'] ='SSDDSFSE32DD23S';
  $frm['APP_STATUS'] ='DRAFT';
  $frm['APP_PROC_STATUS']='TEST';
  $frm['APP_PARALLEL']='NO';
  $frm['APP_TITLE']='MAUI';



  $prouid = $obj->Save ($frm);
  //$obj->load('746E99F0D23189'); print_r($obj->Fields);
  $obj->delete('046E99A56954AE');


  die("eliminado YA - ".$prouid);
 */

