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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

global $RBAC;
G::LoadClass( 'replacementLogo' );

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    die();
}

//calculating the max upload file size;
$POST_MAX_SIZE = ini_get( 'post_max_size' );
$mul = substr( $POST_MAX_SIZE, - 1 );
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$postMaxSize = (int) $POST_MAX_SIZE * $mul;

$UPLOAD_MAX_SIZE = ini_get( 'upload_max_filesize' );
$mul = substr( $UPLOAD_MAX_SIZE, - 1 );
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$uploadMaxSize = (int) $UPLOAD_MAX_SIZE * $mul;

if ($postMaxSize < $uploadMaxSize)
    $uploadMaxSize = $postMaxSize;
$Fields['MAX_FILE_SIZE'] = $uploadMaxSize . " (" . $UPLOAD_MAX_SIZE . ") ";

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'LOGO';

$G_PUBLISH = new Publisher();
$oHeadPublisher = & headPublisher::getSingleton();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/uplogo', '', $Fields );

$G_PUBLISH->AddContent( 'view', 'setup/uplogo' );
G::RenderPage( "publishBlank", "blank" );

?>
<script>
  /*
   * By krlos April 07, 2010
   * we're going to change to the logo choosed
   * parameter logo name
   */
  var changeLogo= function (nameLogo){
    new leimnud.module.app.confirm().make({
      label:G_STRINGS.ID_APPLY_LOGO,
      action:function(){
      ajax_function('replacementLogo','replacementLogo','NAMELOGO='+encodeURIComponent(nameLogo),'GET') ;
      parent.parent.window.location = 'main?s=LOGO';
    }});

  }

  /*
   * By krlos April 07, 2010
   * to delete logo choosed
   * parameter logo name
   */
  function deleteLogo(nameLogo) {
    new leimnud.module.app.confirm().make({
      label:G_STRINGS.ID_REMOVE_LOGO,
      action:function(){
        ajax_function('logo_Delete','','NAMELOGO='+nameLogo,'GET') ;
        window.location = 'uplogo';
      }
    });
    return false;
  }

  /*
   * By krlos April 07, 2010
   * to put processmaker logo
   * parameters file db and user id
   */
  var restoreLogo = function (optfiledb, usrUid){
    ajax_function('replacementLogo','restoreLogo','OPTFILEDB='+optfiledb+'&USRUID='+usrUid,'GET') ;
    window.location = 'uplogo';
  }
</script>

