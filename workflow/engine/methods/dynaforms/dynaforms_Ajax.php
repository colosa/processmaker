<?php
/**
 * dynaforms_Ajax.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
/*
 * Created on 07/01/2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
global $_DBArray;
if (! isset( $_DBArray )) {
    $_DBArray = array ();
}
G::LoadClass( 'dynaformEditor' );
$oDynaformEditorAjax = new dynaformEditorAjax( $_POST );

//if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;
//
//  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );
//
//	G::LoadInclude('ajax');
//  G::LoadClass('toolBar');
//  G::LoadClass('dynaFormField');
//  G::LoadClass('dynaform');
//
//  if (!((isset($_POST['A']) && $_POST['A']!=='')||(isset($_GET['A']) && $_GET['A']!==''))) return;
//
//  $file = G::decrypt( get_ajax_value('A') , URL_KEY );
//
//	$function=get_ajax_value('function');
//
//	switch ( $function ) {
//	  case 'saveDyna':
//	    /*Save register*/
//      $DYN_UID=get_ajax_value('DYN_UID');
//      if (isset($_SESSION['CURRENT_DYNAFORM'])
//        && $_SESSION['CURRENT_DYNAFORM']['DYN_UID']===$DYN_UID) {
//        $dbc = new DBConnection();
//        $ses = new DBSession($dbc);
//        $dynaform = new dynaform( $dbc );
//        $Fields=$_SESSION['CURRENT_DYNAFORM'];
//        //$dynaform->Fields=$Fields;
//        $dynaform->Save( $Fields );
//        unset($dynaform->Fields);
//        $dynaform->Fields['DYN_UID']=$DYN_UID;
//        $dynaform->Load( $_SESSION['CURRENT_DYNAFORM']['DYN_UID'] );
//        $_SESSION['CURRENT_DYNAFORM']=$dynaform->Fields;
//      }
//	    break;
//	    /*Save file*/
//      $copy = implode('',file(PATH_DYNAFORM  . $file . '.xml'));
//      $copyHtml = implode('',file(PATH_DYNAFORM  . $file . '.html'));
//      $file = (strcasecmp(substr($file,-5),'_tmp0')==0)? substr($file,0,strlen($file)-5) : $file;
//      $fcopy=fopen(PATH_DYNAFORM  . $file . '.xml',"w");
//      fwrite($fcopy, $copy);
//      fclose($fcopy);
//      $fcopy=fopen(PATH_DYNAFORM  . $file . '.html',"w");
//      fwrite($fcopy, $copyHtml);
//      fclose($fcopy);
//	    /*TODO: Delete temporal file*/
//	    break;
//	  case 'closeDyna':
//	    unset($_SESSION['CURRENT_DYNAFORM']);
//	    /*TODO: Delete temporal file*/
//	    break;
//	  case 'isModified':
//      $DYN_UID=get_ajax_value('DYN_UID');
//      $modified = false;
//      if (isset($_SESSION['CURRENT_DYNAFORM'])
//        && $_SESSION['CURRENT_DYNAFORM']['DYN_UID']===$DYN_UID) {
//        $dbc = new DBConnection();
//        $ses = new DBSession($dbc);
//        $dynaform = new dynaform( $dbc );
//        $dynaform->Fields['DYN_UID']=$DYN_UID;
//        $dynaform->Load( $dynaform->Fields['DYN_UID'] );
//        $modified = $modified || ($_SESSION['CURRENT_DYNAFORM']!==$dynaform->Fields);
//      }
//      $copy = implode('',file(PATH_DYNAFORM  . $file . '.xml'));
//      $fileOrigen = (strcasecmp(substr($file,-5),'_tmp0')==0)? substr($file,0,strlen($file)-5) : $file;
//      $origen = implode('',file(PATH_DYNAFORM  . $fileOrigen . '.xml'));
//      $modified = $modified || ($copy!==$origen);
//      print($modified?'true':'false');
//	    break;
//    case 'preview':
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//      print($form->render( $form->template , $script ));
//      break;
//    case 'xmlcode':
//      $openDoc = new Xml_Document();
//      $openDoc->parseXmlFile(PATH_DYNAFORM  . $file . '.xml');
//      print($openDoc->getXml());
//      break;
//    case 'htmlcode':
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//      $G_PUBLISH = new Publisher;
//      $G_PUBLISH->publisherId='';
//      /* Navigation Bar */
//      	$form->fields=G::array_merges(
//      		array('__DYNAFORM_OPTIONS' => new XmlForm_Field_XmlMenu(
//      			new Xml_Node(
//      				'__DYNAFORM_OPTIONS',
//      				'complete',
//      				'',
//      				array('type'=>'xmlmenu','xmlfile'=>'gulliver/dynaforms_Options')
//      				),SYS_LANG,PATH_XMLFORM,$form) ),
//      		$form->fields);
//
//      /**/
//      $html=$form->printTemplate( $form->template , $script );
//      $html=str_replace('{$form_className}','formDefault', $html );
//      $HtmlEditor = array(
//        'URL'=> G::encrypt( $file , URL_KEY ),
//        'HTML'=> $html
//      );
//      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_HtmlEditor', '', $HtmlEditor , '', '');
//      G::RenderPage( "publish", 'raw' );
//      break;
//    case 'xmlcodeSave':
//      //BUG::var_dump("Saving xml code ...");
//      //BUG::var_dump($_POST);
//      //BUG::var_dump($_GET);
//  	  $xmlcode = stripslashes(urldecode(get_ajax_value('xmlcode')));
//      $fp=fopen(PATH_DYNAFORM  . $file . '.xml', 'w');
//      fwrite($fp, $xmlcode );
//      fclose($fp);
//      break;
//    case 'htmlcodeSave':
//  	  $htmlcode = stripslashes(urldecode(get_ajax_value('htmlcode')));
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//  	  $filename = substr($form->fileName , 0, -3) .
//  	    ( $form->type==='xmlform' ? '' : '.' . $form->type  ) . 'html';
//      $fp=fopen($filename, 'w');
//      fwrite($fp, $htmlcode );
//      fclose($fp);
//      break;
//    case 'resetTemplate':
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//  	  $filename = substr($form->fileName , 0, -3) .
//  	    ( $form->type==='xmlform' ? '' : '.' . $form->type  ) . 'html';
//      $fp=fopen($filename, 'w');
//      fwrite($fp, $htmlcode );
//      fclose($fp);
//      break;
//    case 'javascripts':
//      $dbc = new DBConnection();
//      $ses = new DBSession($dbc);
//      $dynaform = new dynaform( $dbc );
//      $dynaform->Fields['DYN_UID']=get_ajax_value('DYN_UID');
//      $dynaform->Load( $dynaform->Fields['DYN_UID'] );
//
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//      $Properties=array(
//        'A'=>G::encrypt( $file , URL_KEY ),
//        'DYN_UID'=>$dynaform->Fields['DYN_UID'],
//        'PRO_UID'=>$dynaform->Fields['PRO_UID'],
//        'DYN_TITLE'=>$dynaform->Fields['DYN_TITLE'],
//        'DYN_TYPE'=>$dynaform->Fields['DYN_TYPE'],
//        'DYN_DESCRIPTION'=>$dynaform->Fields['DYN_DESCRIPTION'],
//        'WIDTH'=>$form->width,
//        'ENABLETEMPLATE'=>$form->enableTemplate,
//        'MODE'=>$form->mode
//        );
//
//      define('DB_XMLDB_HOST', PATH_DYNAFORM  . $file . '.xml' );
//      define('DB_XMLDB_USER','');
//      define('DB_XMLDB_PASS','');
//      define('DB_XMLDB_NAME','');
//      define('DB_XMLDB_TYPE','myxml');
//
//      $G_PUBLISH = new Publisher;
//      $G_PUBLISH->publisherId='';
//      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_JSEditor', '', $Properties , '', '');
//      G::RenderPage( "publish" , "raw" );
//      break;
//    case 'properties':
//      $dbc = new DBConnection();
//      $ses = new DBSession($dbc);
//      $DYN_UID=get_ajax_value('DYN_UID');
//      if (isset($_SESSION['CURRENT_DYNAFORM'])
//        && $_SESSION['CURRENT_DYNAFORM']['DYN_UID']===$DYN_UID) {
//        $dynaform = new dynaform( $dbc );
//        $dynaform->Fields=$_SESSION['CURRENT_DYNAFORM'];
//      }else{
//        $dynaform = new dynaform( $dbc );
//        $dynaform->Fields['DYN_UID']=$DYN_UID;
//        $dynaform->Load( $dynaform->Fields['DYN_UID'] );
//        $_SESSION['CURRENT_DYNAFORM']=$dynaform->Fields;
//      }
//
//      $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
//      $Properties=array(
//        'A'=>G::encrypt( $file , URL_KEY ),
//        'DYN_UID'=>$dynaform->Fields['DYN_UID'],
//        'PRO_UID'=>$dynaform->Fields['PRO_UID'],
//        'DYN_TITLE'=>$dynaform->Fields['DYN_TITLE'],
//        'DYN_TYPE'=>$dynaform->Fields['DYN_TYPE'],
//        'DYN_DESCRIPTION'=>$dynaform->Fields['DYN_DESCRIPTION'],
//        'WIDTH'=>$form->width,
//        'ENABLETEMPLATE'=>$form->enableTemplate,
//        'MODE'=>$form->mode
//        );
//      $G_PUBLISH = new Publisher;
//      $G_PUBLISH->publisherId='';
//      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Properties', 'visivility:hidden', $Properties , SYS_URI.'dynaforms/dynaforms_SaveProperties');
//      G::RenderPage( "publish" , "raw" );
//      break;
//	}
//

