<?php
/**
 * dynaforms_FlatEditor.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}

    //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );


G::LoadClass( 'toolBar' );
G::LoadClass( 'dynaFormField' );
G::LoadClass( 'process' );
G::LoadClass( 'dynaform' );
//G::LoadClass('configuration');


$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'processes';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = 'FIELDS';

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

//Hardcode: UID of the library by default
$PRO_UID = isset( $_POST['PRO_UID'] ) ? $_POST['PRO_UID'] : '746B734DC23311';

$process = new Process( $dbc );
$process->Load( $PRO_UID );

$dynaform = new dynaform( $dbc );
$dynaform->Fields['DYN_UID'] = (isset( $_POST['DYN_UID'] )) ? urldecode( $_POST['DYN_UID'] ) : '0';
$dynaform->Load( $dynaform->Fields['DYN_UID'] );

if (isset( $_POST['DYN_UID'] ) && ($_POST['DYN_UID'] !== '')) {
    $file = $dynaform->Fields['DYN_FILENAME'];
} else {
    //Hardcode: Sample of xmlform.
    $file = $PRO_UID . '/' . 'myInfo';
}

    /* Start Comment: If file doesn't exist, it is created */
if (! file_exists( PATH_DYNAFORM . $file . '.xml' )) {
    $newDoc = new Xml_Document();
    $newDoc->addChildNode( new Xml_Node( 'dynaForm', 'open', '', array ('type' => 'xmlform','name' => $file
    ) ) );
    $newDoc->children[0]->addChildNode( new Xml_Node( '', 'cdata', "\n" ) );
    G::verifyPath( dirname( PATH_DYNAFORM . $file . '.xml' ), true );
    $newDoc->save( PATH_DYNAFORM . $file . '.xml' );
    unset( $newDoc );
}
/* End Comment */

  /* Start Comment: Create and temporal copy. */
  $copy = implode( '', file( PATH_DYNAFORM . $file . '.xml' ) );
$file .= '_tmp0';
$fcopy = fopen( PATH_DYNAFORM . $file . '.xml', "w" );
fwrite( $fcopy, $copy );
fclose( $fcopy );
/* End Comment */
//Removes any other CURRENT_DYNAFORM that could be pending because of a page refresh or a failure
unset( $_SESSION['CURRENT_DYNAFORM'] );

define( 'DB_XMLDB_HOST', PATH_DYNAFORM . $file . '.xml' );
define( 'DB_XMLDB_USER', '' );
define( 'DB_XMLDB_PASS', '' );
define( 'DB_XMLDB_NAME', '' );
define( 'DB_XMLDB_TYPE', 'myxml' );

$title = $process->Fields['PRO_TITLE'] . ' : ' . $dynaform->Fields['DYN_TITLE'];

$Parameters = array ('SYS_LANG' => SYS_LANG,'URL' => G::encrypt( $file, URL_KEY ),'DYN_UID' => $dynaform->Fields['DYN_UID'],'DYNAFORM_NAME' => $title
);

$openDoc = new Xml_Document();
$openDoc->parseXmlFile( PATH_DYNAFORM . $file . '.xml' );
$XmlEditor = array ('URL' => G::encrypt( $file, URL_KEY ),'XML' => $openDoc->getXml()
);

$form = new Form( $file, PATH_DYNAFORM, SYS_LANG, true );
$HtmlEditor = array ('URL' => G::encrypt( $file, URL_KEY ),'HTML' => $form->printTemplate( $form->template, $script )
);
$JSEditor = array ('URL' => G::encrypt( $file, URL_KEY ),'HTML' => $form->printTemplate( $form->template, $script )
);

/* Block : Loads the Editor configuration */
$defaultConfig = array ('Editor' => array ('left' => '0',//'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
'top' => '0',//'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
'width' => 'document.body.clientWidth-4','height' => 'document.body.clientHeight-2'  //'3/4*(document.body.clientWidth-getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))*2)',
),'Toolbar' => array ('left' => 'document.body.clientWidth-2-toolbar.clientWidth-24-3+7',//'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
'top' => '52'  //'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
),'FieldsList' => array ('left' => '4+toolbar.clientWidth+24','top' => 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))','width' => 268 - 24,'height' => 400
)
);
/*$configuration = new Configuration($dbc);
    $configuration->load( array('CFG_UID'=>'DynaformEditor') );
    if ($configuration->is_new) {
      $config = $defaultConfig;
      $configuration->Fields['CFG_UID']='DynaformEditor';
      $configuration->Fields['CFG_VALUE']=serialize( $config );
      //$configuration->Save();
    } else {
      $config = unserialize( $configuration->Fields['CFG_VALUE'] );
      $config = G::array_merges( $defaultConfig , $config );
    }*/
$config = $defaultConfig;
/* End Block */

$G_PUBLISH = new Publisher();
$G_PUBLISH->publisherId = 'dynaformEditor';
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->setTitle( "Dynaform Editor" );

//$G_PUBLISH->AddContent('pagedtable', 'paged-table', 'dynaforms/fields_ShortList', '', $Parameters , '', SYS_URI.'dynaforms/dynaforms_PagedTableAjax');
$G_PUBLISH->AddContent( 'blank' );

$panelConf = array ('title' => G::LoadTranslation( 'ID_DYNAFORM_EDITOR' ) . ' - [' . $title . ']','style' => array ('title' => array ('textAlign' => 'left'
)
),'width' => 700,'height' => 600,'tabWidth' => 120,'modal' => true,'drag' => false,'resize' => false,'blinkToFront' => false
);
$panelConf = array_merge( $panelConf, $config['Editor'] );

$G_PUBLISH->AddContent( 'panel-init', 'mainPanel', $panelConf );
$G_PUBLISH->AddContent( 'xmlform', 'toolbar', 'dynaforms/fields_Toolbar', '', $Parameters, '', '' );
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_Editor', '', $Parameters, '', '' );
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_XmlEditor', '', $XmlEditor, '', '' );
//This space will be loaded dynamically by el js function: "changoToHtmlCode"
$G_PUBLISH->AddContent( 'blank' );
$G_PUBLISH->AddContent( 'pagedtable', 'paged-table', 'dynaforms/fields_List', '', $Parameters, '', SYS_URI . 'dynaforms/dynaforms_PagedTableAjax' );
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_JSEditor', '', $JSEditor, '', '' );
$G_PUBLISH->AddContent( 'blank' );
//  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Properties', '', $JSEditor , '', '');


$G_PUBLISH->AddContent( 'panel-tab', 'Preview', 'dynaformEditor[3]', 'changoToPreview', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-tab', 'XML Code', 'dynaformEditor[4]', 'changoToXmlCode', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-tab', 'HTML Template', 'dynaformEditor[5]', 'changoToHtmlCode', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-tab', 'Fields List', 'dynaformEditor[6]', 'changoToFieldsList', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-tab', 'JavaScripts', 'dynaformEditor[7]', 'changoToJavascripts', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-tab', 'Properties', 'dynaformEditor[8]', 'changoToProperties', 'saveCurrentView' );
$G_PUBLISH->AddContent( 'panel-close' );

G::RenderPage( "publish", "raw" );

?>
<script>
  var toolbar = document.getElementById('fields_Toolbar')
  var fieldsList = document.getElementById('dynaformEditor[0]')
  var tableHeight=<?php echo $config['FieldsList']['height'] ?>;
  var tableWidth=<?php echo $config['FieldsList']['width'] ?>;
  var toolbarTop=<?php echo $config['Toolbar']['top'] ?>;
  var toolbarLeft=<?php echo $config['Toolbar']['left'] ?>;
  var fieldsListTop=<?php echo $config['FieldsList']['top'] ?>//(toolbarTop+toolbar.clientHeight+44+8 );
  var fieldsListLeft=<?php echo $config['FieldsList']['left'] ?>;
  mainPanel.elements.headerBar.style.backgroundColor='#CBDAEF';
  mainPanel.elements.headerBar.style.borderBottom='1px solid #808080';
  mainPanel.elements.headerBar.appendChild(toolbar);
  //var fieldsListToolBar = toolbarWindow('Fields list', fieldsList , fieldsListLeft, fieldsListTop, tableWidth+24, tableHeight+44 );
  //var fieldsToolBar = toolbarWindow('Toolbar', toolbar, toolbarLeft, toolbarTop, toolbar.clientWidth+10, toolbar.clientHeight+44+4 );
  mainPanel.events.remove = function(){
    closeDyna();
    //fieldsListToolBar.remove();
    //fieldsToolBar.remove();
  }
  resizeXmlEditor();

  function toolbarWindow ( title , element, x, y, width, height, callbackFn )  {
  	var myPanel = new leimnud.module.panel();
  	myPanel.options = {
  	  size:{w:width,h:height},
  	  position:{x:x,y:y},
  		title: title,
  		theme: "processmaker",
  		control: { close :false, roll	:false, drag	:true, resize	:false},
      fx: {
        //shadow	:true,
        blinkToFront:true,
        opacity	:true,
        drag:true,
        modal: false,
        rolled:false
      }
  	};
  	myPanel.setStyle={
  	    modal:{backgroundColor:"transparent"},
  	    content:{'border':'0px solid white','backgroundColor':'transparent'}
  	  };
  	myPanel.make();
  	myPanel.addContent(element);
    return myPanel;
  }
</script>

