<?php

/**
 * class.publisher.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */

/**
 * Publisher class definition
 * It is to publish all content in a page
 *
 * @package gulliver.system
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class Publisher
{
    public $Parts = null;
    public $dbc = null;
    public $scriptFile = '';
    public $publisherId = 'publisherContent';
    public $localMode = '';

    public $publishType;
    public $ROWS_PER_PAGE = null;

    /* PHP 4 doesn't provide destructor where to free $scriptFileHandler resource */
    //var $scriptFileHandler = false;


    /**
     * Add content in $Parts array
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     *
     * @param $strType
     * @param $strLayout
     * @param $strName
     * @param $strContent
     * @param $arrData
     * @param $strTarget
     * @return void
     *
     */
    public function AddContent ($strType = "form", $strLayout = "form", $strName = "", $strContent = "", $arrData = null, $strTarget = "", $ajaxServer = '', $mode = '', $bAbsolutePath = false)
    {
        if ($mode != '') {
            $this->localMode = $mode;
        }
        $pos = 0;
        if (is_array( $this->Parts )) {
            $pos = count( $this->Parts );
        }
        $this->Parts[$pos] = array ('Type' => $strType,'Template' => $strLayout,'File' => $strName,'Content' => $strContent,'Data' => $arrData,'Target' => $strTarget,'ajaxServer' => $ajaxServer,'AbsolutePath' => $bAbsolutePath
        );

        //This is needed to prepare the "header content"
        //before to send the body content. ($oHeadPublisher)
        ob_start();

        $this->RenderContent0( $pos );
        if ((ob_get_contents() !== '') && ($this->publisherId !== '') && ($strType != 'template')) {
            $this->Parts[$pos]['RenderedContent'] = '<DIV id="' . $this->publisherId . '[' . $pos . ']" style="' . ((is_string( $strContent )) ? $strContent : '') . '; margin:0px;" align="center">';
            $this->Parts[$pos]['RenderedContent'] .= ob_get_contents();
            $this->Parts[$pos]['RenderedContent'] .= '</DIV>';
        } else {
            $this->Parts[$pos]['RenderedContent'] = ob_get_contents();
        }
        ob_end_clean();
    }

    /**
     * Function RenderContent
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string intPos
     * @param eter string showXMLFormName
     * @return string
     */
    public function RenderContent ($intPos = 0)
    {
        print $this->Parts[$intPos]['RenderedContent'];
    }

    /**
     * It Renders content according to Part['Type']
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     *
     * @param intPos = 0
     * @return void
     *
     */
    public function RenderContent0 ($intPos = 0, $showXMLFormName = false)
    {
        global $G_FORM;
        global $G_TABLE;
        global $G_TMP_TARGET;
        global $G_OP_MENU;
        global $G_IMAGE_FILENAME;
        global $G_IMAGE_PARTS;
        global $_SESSION; //Changed from $HTTP_SESSION_VARS
        global $G_OBJGRAPH; //For graphLayout component
        $this->intPos = $intPos;
        $Part = $this->Parts[$intPos];
        $this->publishType = $Part['Type'];

        switch ($this->publishType) {
            case 'externalContent':
                $G_CONTENT = new Content();
                if ($Part['Content'] != "") {
                    $G_CONTENT = G::LoadContent( $Part['Content'] );
                }
                G::LoadTemplateExternal( $Part['Template'] );
                break;
            case 'image':
                $G_IMAGE_FILENAME = $Part['File'];
                $G_IMAGE_PARTS = $Part['Data'];
                break;
            case 'appform':
                global $APP_FORM;
                $G_FORM = $APP_FORM;
                break;
            case 'xmlform':
            case 'dynaform':
                global $G_FORM;

                if ($Part['AbsolutePath']) {
                    $sPath = $Part['AbsolutePath'];
                } else {
                    if ($this->publishType == 'xmlform') {
                        $sPath = PATH_XMLFORM;
                    } else {
                        $sPath = PATH_DYNAFORM;
                    }
                }

                //if the xmlform file doesn't exists, then try with the plugins folders
                if (! is_file( $sPath . $Part['File'] . '.xml' )) {
                    $aux = explode( PATH_SEP, $Part['File'] );
                    //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
                    if (count( $aux ) > 2) {
                        //Subfolders
                        $filename = array_pop( $aux );
                        $aux0 = implode( PATH_SEP, $aux );
                        $aux = array ();
                        $aux[0] = $aux0;
                        $aux[1] = $filename;
                    }
                    if (count( $aux ) == 2 && defined( 'G_PLUGIN_CLASS' )) {
                        $oPluginRegistry = & PMPluginRegistry::getSingleton();
                        if ($response = $oPluginRegistry->isRegisteredFolder( $aux[0] )) {
                            if ($response !== true) {
                                $sPath = PATH_PLUGINS . $response . PATH_SEP;
                            } else {
                                $sPath = PATH_PLUGINS;
                            }
                        }
                    }
                }
                if (! class_exists( $Part['Template'] ) || $Part['Template'] === 'xmlform') {
                    $G_FORM = new Form( $Part['File'], $sPath, SYS_LANG, false );
                } else {
                    eval( '$G_FORM = new ' . $Part['Template'] . ' ( $Part[\'File\'] , "' . $sPath . '");' );
                }

                if (($this->publishType == 'dynaform') && (($Part['Template'] == 'xmlform') || ($Part['Template'] == 'xmlform_preview'))) {
                    $dynaformShow = (isset( $G_FORM->printdynaform ) && ($G_FORM->printdynaform)) ? 'gulliver/dynaforms_OptionsPrint' : 'gulliver/dynaforms_Options';
                    $G_FORM->fields = G::array_merges( array ('__DYNAFORM_OPTIONS' => new XmlForm_Field_XmlMenu( new Xml_Node( '__DYNAFORM_OPTIONS', 'complete', '', array ('type' => 'xmlmenu','xmlfile' => $dynaformShow
                    ) ), SYS_LANG, PATH_XMLFORM, $G_FORM )
                    ), $G_FORM->fields );
                }

                //Needed to make ajax calls

                //The action in the form tag.
                if (defined( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes') {
                    $G_FORM->action = urlencode( G::encrypt( $Part['Target'], URL_KEY ) );
                } else {
                    $G_FORM->action = $Part['Target'];
                }

                if (! (isset( $Part['ajaxServer'] ) && ($Part['ajaxServer'] !== ''))) {
                    if ($this->publishType == 'dynaform') {
                        $Part['ajaxServer'] = '../gulliver/defaultAjaxDynaform';
                    } else {
                        $Part['ajaxServer'] = '../gulliver/defaultAjax';
                    }
                }
                if (defined( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes') {
                    $G_FORM->ajaxServer = urlencode( G::encrypt( $Part['ajaxServer'], URL_KEY ) );
                } else {
                    $G_FORM->ajaxServer = $Part['ajaxServer'];
                }

                $G_FORM->setValues( $Part['Data'] );

                $G_FORM->setValues( array ('G_FORM_ID' => $G_FORM->id ) );

                //Asegurese de que no entre cuando $Part['Template']=="grid"
                //de hecho soo deberia usarse cuando $Part['Template']=="xmlform"
                if ((($this->publishType == 'dynaform') && $Part['Template'] == "xmlform") || ($Part['Template'] == "xmlform")) {
                    $G_FORM->values = G::array_merges( array ('__DYNAFORM_OPTIONS' => isset( $Part['Data']['__DYNAFORM_OPTIONS'] ) ? $Part['Data']['__DYNAFORM_OPTIONS'] : ''
                    ), $G_FORM->values );
                    if (isset( $G_FORM->nextstepsave )) {
                        switch ($G_FORM->nextstepsave) {
                            // this condition validates if the next step link is configured to Save and Go the next step or show a prompt
                            case 'save':
                                // Save and Next only if there are no required fields can submit the form.
                                $G_FORM->values['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'if (document.getElementById("' . $G_FORM->id . '")&&validateForm(document.getElementById(\'DynaformRequiredFields\').value)) {document.getElementById("' . $G_FORM->id . '").submit();}return false;';
                                break;
                            case 'prompt':
                                // Show Prompt only if there are no required fields can submit the form.
                                $G_FORM->values['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'if (document.getElementById("' . $G_FORM->id . '")&&validateForm(document.getElementById(\'DynaformRequiredFields\').value)) {new leimnud.module.app.confirm().make({label:"@G::LoadTranslation(ID_DYNAFORM_SAVE_CHANGES)",action:function(){document.getElementById("' . $G_FORM->id . '").submit();}.extend(this),cancel:function(){window.location = getField("DYN_FORWARD").href;}.extend(this)});return false;} return false;';
                                break;
                        }
                    }
                }
                if (isset( $_SESSION )) {
                    $_SESSION[$G_FORM->id] = $G_FORM->values;
                }
                // by default load the core template
                if ($Part['Template'] == 'xmlform_preview') {
                    $Part['Template'] = 'xmlform';
                }
                $template = PATH_CORE . 'templates/' . $Part['Template'] . '.html';

                //erik: new feature, now templates such as xmlform.html can be personalized via skins
                if (defined( 'SYS_SKIN' ) && strtolower( SYS_SKIN ) != 'classic') {
                    // First, verify if the template exists on Custom skins path
                    if (is_file( PATH_CUSTOM_SKINS . SYS_SKIN . PATH_SEP . $Part['Template'] . '.html' )) {
                        $template = PATH_CUSTOM_SKINS . SYS_SKIN . PATH_SEP . $Part['Template'] . '.html';
                        //Second, verify if the template exists on base skins path
                    } elseif (is_file( G::ExpandPath( "skinEngine" ) . SYS_SKIN . PATH_SEP . $Part['Template'] . '.html' )) {
                        $template = G::ExpandPath( "skinEngine" ) . SYS_SKIN . PATH_SEP . $Part['Template'] . '.html';
                    }
                }
                //end new feature


                if ($Part['Template'] == 'grid') {
                    print ('<form class="formDefault">') ;
                }
                $scriptCode = '';

                if ($this->localMode != '') {
                    // @# las modification by erik in 09/06/2008
                    $G_FORM->mode = $this->localMode;
                }
                print $G_FORM->render( $template, $scriptCode );
                if ($Part['Template'] == 'grid') {
                    print ('</form>') ;
                }
                $oHeadPublisher = & headPublisher::getSingleton();
                $oHeadPublisher->addScriptFile( $G_FORM->scriptURL );
                $oHeadPublisher->addScriptCode( $scriptCode );

                /**
                 * We've implemented the conditional show hide fields..
                 *
                 * @author Erik A. Ortiz <erik@colosa.com>
                 * @date Fri Feb 19, 2009
                 */
                if ($this->publishType == 'dynaform') {
                    if (isset( $_SESSION['CURRENT_DYN_UID'] )) {
                        require_once "classes/model/FieldCondition.php";
                        $oFieldCondition = new FieldCondition();

                        #This dynaform has show/hide field conditions
                        $ConditionalShowHideRoutines = $oFieldCondition->getConditionScript( $_SESSION['CURRENT_DYN_UID'] );
                    }
                }

                if (isset( $ConditionalShowHideRoutines ) && $ConditionalShowHideRoutines) {
                    G::evalJScript( $ConditionalShowHideRoutines );
                }
                break;
            case 'pagedtable':
                global $G_FORM;
                //if the xmlform file doesn't exists, then try with the plugins folders
                $sPath = PATH_XMLFORM;
                if (! is_file( $sPath . $Part['File'] )) {
                    $aux = explode( PATH_SEP, $Part['File'] );
                    if (count( $aux ) == 2) {
                        $oPluginRegistry = & PMPluginRegistry::getSingleton();
                        if ($oPluginRegistry->isRegisteredFolder( $aux[0] )) {
                            $sPath = PATH_PLUGINS; // . $aux[0] . PATH_SEP ;
                        }
                    }
                }
                $G_FORM = new Form( $Part['File'], $sPath, SYS_LANG, true );

                if (defined( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes') {
                    $G_FORM->ajaxServer = urlencode( G::encrypt( $Part['ajaxServer'], URL_KEY ) );
                } else {
                    $G_FORM->ajaxServer = $Part['ajaxServer'];
                }

                $G_FORM->setValues( $Part['Data'] );
                if (isset( $_SESSION )) {
                    $_SESSION[$G_FORM->id] = $G_FORM->values;
                }

                G::LoadSystem( 'pagedTable' );
                $oTable = new pagedTable();
                $oTable->template = 'templates/' . $Part['Template'] . '.html';
                $G_FORM->xmlform = '';
                $G_FORM->xmlform->fileXml = $G_FORM->fileName;
                $G_FORM->xmlform->home = $G_FORM->home;
                $G_FORM->xmlform->tree->attribute = $G_FORM->tree->attributes;
                $G_FORM->values = array_merge( $G_FORM->values, $Part['Data'] );

                $oTable->setupFromXmlform( $G_FORM );

                if (isset( $Part['ajaxServer'] ) && ($Part['ajaxServer'] !== '')) {
                    $oTable->ajaxServer = $Part['ajaxServer'];
                }
                /* Start Block: Load user configuration for the pagedTable */
                G::LoadClass( 'configuration' );
                $objUID = $Part['File'];
                $conf = new Configurations();
                $conf->loadConfig( $oTable, 'pagedTable', $objUID, '', (isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : ''), '' );
                $oTable->__OBJ_UID = $objUID;
                /* End Block */

                /* Start Block: PagedTable Right Click */
                G::LoadClass( 'popupMenu' );
                $pm = new popupMenu( 'gulliver/pagedTable_PopupMenu' );
                $pm->name = $oTable->id;
                $fields = array_keys( $oTable->fields );
                foreach ($fields as $f) {
                    switch (strtolower( $oTable->fields[$f]['Type'] )) {
                        case 'javascript':
                        case 'button':
                        case 'private':
                        case 'hidden':
                        case 'cellmark':
                            break;
                        default:
                            $label = ($oTable->fields[$f]['Label'] != '') ? $oTable->fields[$f]['Label'] : $f;
                            $label = str_replace( "\n", ' ', $label );
                            $pm->fields[$f] = new XmlForm_Field_popupOption( new Xml_Node( $f, 'complete', '', array ('label' => $label,'type' => 'popupOption','launch' => $oTable->id . '.showHideField("' . $f . '")' ) ) );
                            $pm->values[$f] = '';
                    }
                }
                $sc = '';
                $pm->values['PAGED_TABLE_ID'] = $oTable->id;
                print ($pm->render( PATH_CORE . 'templates/popupMenu.html', $sc )) ;
                /* End Block */

                $oTable->renderTable();

                /* Start Block: Load PagedTable Right Click */
                print ('<script type="text/javascript">') ;
                print ($sc) ;
                print ('loadPopupMenu_' . $oTable->id . '();') ;
                print ('</script>') ;
                /* End Block */
                break;
            case 'propeltable':
                global $G_FORM;
                //if the xmlform file doesn't exists, then try with the plugins folders
                if ($Part['AbsolutePath']) {
                    $sPath = '';
                } else {
                    $sPath = PATH_XMLFORM;
                }
                if (! is_file( $sPath . $Part['File'] )) {
                    $aux = explode( PATH_SEP, $Part['File'] );

                    //search in PLUGINS folder, probably the file is in plugin
                    if (count( $aux ) == 2) {
                        $oPluginRegistry = & PMPluginRegistry::getSingleton();
                        if ($oPluginRegistry->isRegisteredFolder( $aux[0] )) {
                            $sPath = PATH_PLUGINS; // . $aux[0] . PATH_SEP ;
                        }
                    }

                    //search in PATH_DYNAFORM folder
                    if (! is_file( $sPath . PATH_SEP . $Part['File'] . '.xml' )) {
                        $sPath = PATH_DYNAFORM;
                    }

                }

                //PATH_DATA_PUBLIC ???
                if (! file_exists( $sPath . PATH_SEP . $Part['File'] . '.xml' ) && defined( 'PATH_DATA_PUBLIC' )) {
                    $sPath = PATH_DATA_PUBLIC;
                }

                $G_FORM = new Form( $Part['File'], $sPath, SYS_LANG, true );

                if (defined( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes') {
                    $G_FORM->ajaxServer = urlencode( G::encrypt( $Part['ajaxServer'], URL_KEY ) );
                } else {
                    $G_FORM->ajaxServer = $Part['ajaxServer'];
                }

                if (isset( $_SESSION )) {
                    $_SESSION[$G_FORM->id] = $G_FORM->values;
                }

                G::LoadClass( 'propelTable' );

                $oTable = new propelTable();
                $oTable->template = $Part['Template'];
                $oTable->criteria = $Part['Content'];
                if (isset( $Part['ajaxServer'] ) && ($Part['ajaxServer'] !== '')) {
                    $oTable->ajaxServer = $Part['ajaxServer'];
                }
                if (!isset($G_FORM->xmlform)) {
                    $G_FORM->xmlform = new stdclass();
                }
                $G_FORM->xmlform->fileXml = $G_FORM->fileName;
                $G_FORM->xmlform->home = $G_FORM->home;
                if (!isset($G_FORM->xmlform->tree)) {
                    $G_FORM->xmlform->tree = new stdclass();
                }
                $G_FORM->xmlform->tree->attribute = $G_FORM->tree->attributes;
                if (is_array( $Part['Data'] )) {
                    $G_FORM->values = array_merge( $G_FORM->values, $Part['Data'] );
                }

                $oTable->setupFromXmlform( $G_FORM );
                /* Start Block: Load user configuration for the pagedTable */
                G::LoadClass( 'configuration' );
                $objUID = $Part['File'];
                $conf = new Configurations( $oTable );
                $conf->loadConfig( $oTable, 'pagedTable', $objUID, '', (isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : ''), '' );
                $oTable->__OBJ_UID = $objUID;

                //$oTable->__OBJ_UID = '';
                /* End Block */

                /* Start Block: PagedTable Right Click */
                G::LoadClass( 'popupMenu' );
                $pm = new popupMenu( 'gulliver/pagedTable_PopupMenu' );
                $sc = $pm->renderPopup( $oTable->id, $oTable->fields );
                /* End Block */
                //krumo ( $Part );


                if ($this->ROWS_PER_PAGE) {
                    $oTable->rowsPerPage = $this->ROWS_PER_PAGE;
                }
                try {
                    if (is_array( $Part['Data'] )) {
                        $oTable->renderTable( '', $Part['Data'] );
                    } else {
                        $oTable->renderTable();
                    }
                    print ($sc) ;
                } catch (Exception $e) {
                    $aMessage['MESSAGE'] = $e->getMessage();
                    $this->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
                }
                break;
            case 'panel-init':
                global $mainPanelScript;
                global $panelName;
                global $tabCount;
                //G::LoadThirdParty( 'pear/json', 'class.json' );
                //$json = new Services_JSON();
                $tabCount = 0;
                $panelName = $Part['Template'];
                $data = $Part['File'];
                if (! is_array( $data )) {
                    $data = array ();
                }
                $data = G::array_merges( array ('title' => '','style' => array (),'left' => 'getAbsoluteLeft(mycontent)','top' => 'getAbsoluteTop(mycontent)','width' => 700,'height' => 600,'drag' => true,'close' => true,'modal' => true,'roll' => false,'resize' => false,'tabWidth' => 120,'tabStep' => 3,'blinkToFront' => true,'tabSpace' => 10 ), $data );
                $mainPanelScript = 'var ' . $panelName . '={},' . $panelName . 'Tabs=[];' . 'leimnud.event.add(window,"load",function(){'
                . $panelName . ' = new leimnud.module.panel();' . 'var mycontent=document.getElementById("' . $this->publisherId . '['
                . $intPos . ']");' . $panelName . '.options={' . 'size:{w:' . $data['width'] . ',h:' . $data['height'] . '},' . 'position:{x:'
                . $data['left'] . ',y:' . $data['top'] . '},' . 'title:"' . addcslashes( $data['title'], '\\"' ) . '",' . 'theme:"processmaker",'
                . 'statusBar:true,' . 'headerBar:true,' . 'control:{' . ' close:' . ($data['close'] ? 'true' : 'false') . ',' . ' roll:'
                . ($data['roll'] ? 'true' : 'false') . ',' . ' drag:' . ($data['drag'] ? 'true' : 'false') . ',' . ' resize:'
                . ($data['resize'] ? 'true' : 'false') . '},' . 'fx:{' . ' drag:' . ($data['drag'] ? 'true' : 'false') . ',' . ' modal:'
                . ($data['modal'] ? 'true' : 'false') . ',' . ' blinkToFront:' . ($data['blinkToFront'] ? 'true' : 'false') . '}' . '};'
                . $panelName . '.setStyle=' . Bootstrap::json_encode( $data['style'] ) . ';' . $panelName . '.tab={' . 'width:'
                . ($data['tabWidth'] + $data['tabSpace']) . ',' . 'optWidth:' . $data['tabWidth'] . ',' . 'step :' . $data['tabStep']
                . ',' . 'options:[]' . '};';
                print (' ') ;
                break;
            case 'panel-tab':
                global $tabCount;
                global $mainPanelScript;
                global $panelName;
                $onChange = $Part['Content'];
                $beforeChange = $Part['Data'];
                if (SYS_LANG == 'es') {
                    $mainPanelScript = str_replace( "120", "150", $mainPanelScript );
                } else {
                    $mainPanelScript = str_replace( "150", "120", $mainPanelScript );
                }
                $mainPanelScript .= $panelName . 'Tabs[' . $tabCount . ']=' . 'document.getElementById("' . $Part['File'] . '");' . $panelName . '.tab.options[' . $panelName . '.tab.options.length]=' . '{' . 'title  :"' . addcslashes( $Part['Template'], '\\"' ) . '",' . 'noClear  :true,' . 'content  :function(){' . ($beforeChange != '' ? ('if (typeof(' . $beforeChange . ')!=="undefined") {' . $beforeChange . '();}') : '') . $panelName . 'Clear();' . $panelName . 'Tabs[' . $tabCount . '].style.display="";' .
                //              'this.addContent('.$panelName.'Tabs['.$tabCount.']);'.
                //              'this.addContent(document.getElementById("'.$Part['File'].'"));'.
                //              $panelName.'Tabs['.$tabCount.']='.$panelName.'Tabs['.$tabCount.'].cloneNode( true );'.
                ($onChange != '' ? ('if (typeof(' . $onChange . ')!=="undefined") {' . $onChange . '();}') : '') . '}.extend(' . $panelName . '),' . 'selected:' . ($tabCount == 0 ? 'true' : 'false') . '};';
                $tabCount ++;

                break;
            case 'panel-close':
                global $mainPanelScript;
                global $panelName;
                global $tabCount;
                $mainPanelScript .= $panelName . '.make();';
                $mainPanelScript .= 'for(var r=0;r<' . $tabCount . ';r++)' . 'if (' . $panelName . 'Tabs[r])' . $panelName . '.addContent(' . $panelName . 'Tabs[r]);';
                $mainPanelScript .= '});';
                $mainPanelScript .= 'function ' . $panelName . 'Clear(){';
                $mainPanelScript .= 'for(var r=0;r<' . $tabCount . ';r++)' . 'if (' . $panelName . 'Tabs[r])' . $panelName . 'Tabs[r].style.display="none";}';
                $oHeadPublisher = & headPublisher::getSingleton();
                $oHeadPublisher->addScriptCode( $mainPanelScript );

                break;
            case 'blank':
                print (' ') ;
                break;
            case 'varform':
                global $G_FORM;
                $G_FORM = new Form();
                G::LoadSystem( "varform" );
                $xml = new varForm();
                //$xml->parseFile (  );
                $xml->renderForm( $G_FORM, $Part['File'] );
                $G_FORM->Values = $Part['Data'];
                $G_FORM->SetUp( $Part['Target'] );
                $G_FORM->width = 500;
                break;
            case 'table':
                $G_TMP_TARGET = $Part['Target'];
                $G_TABLE = G::LoadRawTable( $Part['File'], $this->dbc, $Part['Data'] );
                break;
            case 'menu':
                $G_TMP_TARGET = $Part['Target'];
                $G_OP_MENU = new Menu();
                $G_OP_MENU->Load( $Part['File'] );
                break;
            case 'smarty': //To do: Please check it 26/06/07
                $template = new Smarty();
                $template->compile_dir = PATH_SMARTY_C;
                $template->cache_dir = PATH_SMARTY_CACHE;
                $template->config_dir = PATH_THIRDPARTY . 'smarty/configs';
                $template->caching = false;
                $dataArray = $Part['Data'];

                // verify if there are templates folders registered, template and method folders are the same
                $folderTemplate = explode( '/', $Part['Template'] );
                $oPluginRegistry = & PMPluginRegistry::getSingleton();
                if ($oPluginRegistry->isRegisteredFolder( $folderTemplate[0] )) {
                    $template->templateFile = PATH_PLUGINS . $Part['Template'] . '.html';
                } else {
                    $template->templateFile = PATH_TPL . $Part['Template'] . '.html';
                }
                // last change to load the template, maybe absolute path was given
                if (! is_file( $template->templateFile )) {
                    $template->templateFile = strpos( $Part['Template'], '.html' ) !== false ? $Part['Template'] : $Part['Template'] . '.html';
                }

                //assign the variables and use the template $template
                $template->assign( $dataArray );
                print $template->fetch( $template->templateFile );
                break;
            case 'template': //To do: Please check it 26/06/07
                if (gettype( $Part['Data'] ) == 'array') {
                    G::LoadSystem( 'template' ); //template phpBB
                    $template = new Template();
                    $template->set_filenames( array ('body' => $Part['Template'] . '.html' ) );
                    $dataArray = $Part['Data'];
                    if (is_array( $dataArray )) {
                        foreach ($dataArray as $key => $val) {
                            if (is_array( $val )) {
                                foreach ($val as $key_val => $val_array) {
                                    $template->assign_block_vars( $key, $val_array );
                                }
                            } else {
                                $template->assign_vars( array ($key => $val ) );
                            }
                        }
                    }
                    $template->pparse( 'body' );
                }
                if (gettype( $Part['Data'] ) == 'object' && strtolower( get_class( $Part['Data'] ) ) == 'templatepower') {
                    $Part['Data']->printToScreen();
                }
                return;
                break;
            case 'view':
            case 'content':
                //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
                $aux = explode( PATH_SEP, $Part['Template'] );
                if (count( $aux ) == 2 && defined( 'G_PLUGIN_CLASS' )) {
                    //if the template doesn't exists, then try it with the plugins folders, after the normal Template
                    $userTemplate = G::ExpandPath( 'templates' ) . $Part['Template'];
                    $globalTemplate = PATH_TEMPLATE . $Part['Template'];
                    if (! is_file( $userTemplate ) && ! is_file( $globalTemplate )) {
                        $oPluginRegistry = & PMPluginRegistry::getSingleton();
                        if ($oPluginRegistry->isRegisteredFolder( $aux[0] )) {
                            $pluginTemplate = PATH_PLUGINS . $Part['Template'] . '.php';
                            include ($pluginTemplate);

                        }
                    }
                }

                break;
            case 'graphLayout': //Added by JHL to render GraphLayout component
                $G_OBJGRAPH = $Part['Data'];
                $G_TMP_TARGET = $Part['Target'];
                $G_TMP_FILE = $Part['File'];
                break;
        }

        //krumo( $Part['Template'] );
        //check if this LoadTemplate is used, byOnti 12th Aug 2008
        G::LoadTemplate( $Part['Template'] );
        $G_TABLE = null;
    }
}

