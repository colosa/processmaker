<?php
/**
 * uplogo.php
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

try {

  $uplogo = PATH_TPL . 'setup' . PATH_SEP . 'uplogo.html' ;
  $template = new TemplatePower( $uplogo );
  $template->prepare();
  $width="100%";
  
  $template->assign ('WIDTH_PANEL'              ,$width);
  $template->assign ('WIDTH_PANEL_20'              ,$width-20);
  
  $upload = new ReplacementLogo();
  $aFotoSelect = $upload->getNameLogo($_SESSION['USER_LOGGED']);
  $sFotoSelect = trim($aFotoSelect['DEFAULT_LOGO_NAME']);
  $check ='';
  $ainfoSite = explode("/",$_SERVER["REQUEST_URI"]);
  $dir=PATH_DATA."sites".PATH_SEP.str_replace("sys","",$ainfoSite[1]).PATH_SEP."files/logos";
  G::mk_dir ( $dir );
  $i=0;
  
  /** if we have at least one image it's load  */
  if (file_exists($dir)) {
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if(($file!=".")&&($file!="..")) {
          $extention=explode(".", $file);
          $aImageProp=getimagesize($dir.'/'.$file, $info);
          $sfileExtention = strtoupper($extention[count($extention)-1]);
//          if( $sfileExtention == 'JPG' || $sfileExtention == 'PNG' || $sfileExtention == 'GIF' ) {
          if( in_array($sfileExtention, array('JPG','JPEG','PNG','GIF') ) ) {
          	
            $check   = (!strcmp($file,$sFotoSelect))?'/images/toadd.png':'/images/delete.png';
            $onclick = (strcmp($file,$sFotoSelect))? "onclick ='deleteLogo(\" $file \");return false;'":'';
            /** if we have at least one image we show the restore image  */
            if($i==0){
            	$template->newBlock( 'logo_Detail');
            	$template->assign ('TR1'             , ($i%3==0)?'<tr>':'' );
              $template->assign ('TR2'             , ($i%3==2)?'</tr>':'');
              $template->assign ('LOG0_IMAGE'      , "/images/processmaker.logo.jpg");
              $logopm="Restore_the_default_logo";
              //$template->assign ('LOG0_NAME'       , str_replace("_"," ",$logopm));
              $template->assign ('LOGO_WIDTH'      , "250");
              $template->assign ('LOGO_HEIGHT'     , "60" );
              $template->assign ('LOG0_SIZE'       , "15.36");
              $template->assign ('LOG0_DELETE'     , "onclick ='changeLogo(\"  \");return false;'");
              $template->assign ('LOG0_CHECK'     , "/images/faviconpm.png");
              
              $i++;
            }
            $template->newBlock( 'logo_Detail');
            $template->assign ('TR1'             , ($i%3==0)?'<tr>':''                                       );
            $template->assign ('TR2'             , ($i%3==2)?'</tr>':''                                      );
            $template->assign ('LOG0_IMAGE'      , "showLogoFile.php?id=".base64_encode($file)         );
            $template->assign ('LOG0_NAME'       , $file                                                     );
            $template->assign ('LOG0_DESCRIPTION', $extention[count($extention)-1]                           );
            $template->assign ('LOGO_CHARACT'    , $aImageProp[3]                                            );
            $template->assign ('LOGO_WIDTH'      , $aImageProp[0]                                            );
            $template->assign ('LOGO_HEIGHT'     , $aImageProp[1]                                            );
            $template->assign ('LOG0_SIZE'       , round( (filesize($dir.'/'.$file) / 1024) *100)/100        );
            $template->assign ('LOG0_DELETE'     , $onclick                                                  );
            $template->assign ('LOG0_CHECK'      , $check                                                    );
            $i++;
          }
        }
      }
      closedir($handle);
    }
  }
  function changeNamelogo($snameLogo){
   $snameLogo = strtolower($snameLogo);
   //replace special characteres and others
   $buscar = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'ä', 'ë', 'ï', 'ö', 'ü', 'Ã¤', 'Ã«', 'Ã¯', 'Ã¶', 'Ã¼', 'Ã', 'Ã‰', 'Ã', 'Ã“', 'Ãš', 'Ã„', 'Ã‹', 'Ã', 'Ã–', 'Ãœ', 'Ã±');
   $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a',  'e',  'i',  'o',  'u',  'a', 'e', 'i', 'o', 'u', 'a',  'e',  'i',  'o',  'u',  'a',  'e',  'i',  'o',  'u',  'a',  'e',  'i',  'o',  'u',  'n');
   $snameLogo = str_replace($buscar, $repl, $snameLogo);
   // add some caracteres
   $lookforit = array(' ', '&', '\r\n', '\n', '+', '_');
   $snameLogo = str_replace($lookforit, '-', $snameLogo);
   // removing and replace others special characteres
   $lookforit = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
   $repl = array('.', '-', '.');
   $snameLogo = preg_replace ($lookforit, $repl, $snameLogo);
   return ($snameLogo);
  }

  // if we have at least one image we show the restore image 
  /*if($i>0) {
      $template->gotoBlock( "_ROOT" );
      $restoreLogo ="<tr><td>
          <a href ='#' onclick ='restoreLogo(\"{PARAMETER_TO_RESTORE1}\",\"{PARAMETER_TO_RESTORE2}\");return;'>
            <img src='/images/processmaker.logo.jpg'  border='0'/><br>
            <font color='#848484'>".G::LoadTranslation('ID_RESTORE_LOGO')."</font>
          </a></td></tr>";
      $template->assign ('SET_LOGO_PM' ,$restoreLogo);
  }*/

  if (sizeof($_POST)>0) {
    //G::SendTemporalMessage('ID_CHANGES_SAVED', 'info', 'labels');
    $formf = $_FILES['form'];
    $namefile  = $formf['name']['LOGO_FILENAME'];
    $typefile  = $formf['type']['LOGO_FILENAME'];
    $errorfile = $formf['error']['LOGO_FILENAME'];
    $tpnfile   = $formf['tmp_name']['LOGO_FILENAME'];
    $aMessage1 = array();
    $fileName = trim(str_replace(' ','_', $namefile));
    $fileName = changeNamelogo($fileName);
    G::uploadFile( $tpnfile, $dir . '/', 'tmp'.$fileName );
    $error = false;
    try {
      G::resizeImage($dir . '/tmp' . $fileName, 250, 60, $dir . '/' .$fileName);
    } catch (Exception $e) {
      $error = $e->getMessage();
    }
    unlink ($dir . '/tmp' . $fileName);
    if ($error === false)
      header('location: uplogo.php');
    else
      G::SendTemporalMessage($error, 'error', 'string');
  }
  $content = $template->getOutputContent();
  print $content;
}
catch (Exception $e) {
  $G_PUBLISH = new Publisher;
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage( 'publishBlank', 'blank' );
  die();
}
