<?php

/**
 * evaluates the dynaform type and other parameters in order to
 * render the correct toolbar in each case
 *
 * @author gustavo cruz gustavo-at-colosa.com
 * @param POST
 */

G::LoadClass( 'toolBar' );

global $G_PUBLISH;
$script = '';
$G_PUBLISH = new Publisher();
$Parameters = array ('SYS_LANG' => SYS_LANG,'URL' => G::encrypt( $_POST['FILE'], URL_KEY ),'DYN_UID' => $_POST['DYN_UID'],'PRO_UID' => $_POST['PRO_UID'],'DYNAFORM_NAME' => $_POST['DYN_TITLE'],'FILE' => $_POST['FILE']);
//$Parameters = "";

if ($_POST['TOOLBAR'] == "grid") {
    $G_PUBLISH->AddContent( 'xmlform', 'toolbar', 'dynaforms/fields_ToolbarGrid', 'display:none', $Parameters, '', '' );
} else {
    $G_PUBLISH->AddContent( 'xmlform', 'toolbar', 'dynaforms/fields_Toolbar', 'display:none', $Parameters, '', '' );
}

G::RenderPage( 'publish', 'raw' );

