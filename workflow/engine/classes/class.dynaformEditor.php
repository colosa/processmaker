<?php

/**
 * class.dynaformEditor.php
 *
 * @package workflow.engine.classes
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
 */
/**
 * Created on 21/12/2007
 * Dynaform - Dynaform class
 *
 * @copyright 2007 COLOSA
 * @author David Callizaya <davidsantos@colosa.com>
 */
G::LoadSystem("webResource");
G::LoadClass('toolBar');
G::LoadClass('dynaFormField');
require_once ('classes/model/Process.php');
require_once ('classes/model/Dynaform.php');
G::LoadClass('xmlDb');
G::LoadSystem('dynaformhandler');

/**
 *
 * @package workflow.engine.classes
 */
class dynaformEditor extends WebResource
{

    private $isOldCopy = false;
    public $file = '';
    public $title = 'New Dynaform';
    public $dyn_uid = '';
    public $dyn_type = '';
    public $home = '';

    /**
     * Other Options for Editor:
     * left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
     * top: 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
     * height: '3/4*(document.body.clientWidth-getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))*2)',
     * left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))'
     * left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))'
     *
     * Other Options for Toolbar:
     * left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
     * top: 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
     */
    public $defaultConfig = array('Editor' => array('left' => '0', 'top' => '0', 'width' => 'document.body.clientWidth-4', 'height' => 'document.body.clientHeight-4'),
        'Toolbar' => array('left' => 'document.body.clientWidth-2-toolbar.clientWidth-24-3+7', 'top' => '52'),
        'FieldsList' => array('left' => '4+toolbar.clientWidth+24', 'top' => 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))', 'width' => 244, 'height' => 400)
    );
    public $panelConf = array('style' => array('title' => array('textAlign' => 'center')),
        'width' => 700, 'height' => 600, 'tabWidth' => 120, 'modal' => true, 'drag' => false, 'resize' => false, 'blinkToFront' => false
    );

    /**
     * Constructor of the class dynaformEditor
     *
     * @param string $get
     * @return void
     */
    public function dynaformEditor($get)
    {
        $this->panelConf = array_merge($this->panelConf, $this->defaultConfig['Editor']);
        //'title' => G::LoadTranslation('ID_DYNAFORM_EDITOR').' - ['.$this->title.']',
    }

    /**
     * Create the xml form default
     *
     * @param string $filename
     * @return void
     */
    public function _createDefaultXmlForm($fileName)
    {
        //Create the default Dynaform
        $sampleForm = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sampleForm .= '<dynaForm type="' . $this->dyn_type . '" name="" width="500" enabletemplate="0" mode="edit">' . "\n";
        switch ($this->dyn_type) {
            case "xmlform":
                /* $sampleForm.='<title type="title" enablehtml="0">' . "\n" .
                  '  <en>Sample form</en>' . "\n" .
                  '</title>'."\n";
                  $sampleForm.='<submit type="submit" enablehtml="0" onclick="">' . "\n" .
                  '  <en>Submit</en>' . "\n" .
                  '</submit>'."\n"; */
                break;
            case "grid":
                /* $sampleForm.='<fieldA type="text" >' . "\n" .
                  '<en>A</en>' . "\n" .
                  '</fieldA>'."\n";
                  $sampleForm.='<fieldB type="text" >' . "\n" .
                  '<en>B</en>' . "\n" .
                  '</fieldB>'."\n"; */
                break;
        }
        $sampleForm .= '</dynaForm>';
        G::verifyPath(dirname($fileName), true);
        $fp = fopen($fileName, 'w');
        $sampleForm = str_replace('name=""', 'name="' . $this->_getFilename($this->file) . '"', $sampleForm);
        fwrite($fp, $sampleForm);
        fclose($fp);
    }

    /**
     * Prints the DynaformEditor
     *
     * @return void
     */
    public function _render()
    {
        global $G_PUBLISH;
        $script = '';
        /* Start Block: Load (Create if doesn't exist) the xmlform */
        $Parameters = array('SYS_LANG' => SYS_LANG, 'URL' => G::encrypt($this->file, URL_KEY), 'DYN_UID' => $this->dyn_uid, 'PRO_UID' => $this->pro_uid, 'DYNAFORM_NAME' => $this->dyn_title, 'FILE' => $this->file, 'DYN_EDITOR' => $this->dyn_editor
        );
        $_SESSION['Current_Dynafom']['Parameters'] = $Parameters;
        $XmlEditor = array('URL' => G::encrypt($this->file, URL_KEY), 'XML' => ''  //$openDoc->getXml()
        );
        $JSEditor = array('URL' => G::encrypt($this->file, URL_KEY)
        );

        $A = G::encrypt($this->file, URL_KEY);

        try {
            $openDoc = new Xml_Document();
            $fileName = $this->home . $this->file . '.xml';
            if (file_exists($fileName)) {
                $openDoc->parseXmlFile($fileName);
            } else {
                $this->_createDefaultXmlForm($fileName);
                $openDoc->parseXmlFile($fileName);
            }
            //$form = new Form( $this->file , $this->home, SYS_LANG, true );
            $Properties = dynaformEditorAjax::get_properties($A, $this->dyn_uid);
            /* Start Block: Prepare the XMLDB connection */
            define('DB_XMLDB_HOST', PATH_DYNAFORM . $this->file . '.xml');
            define('DB_XMLDB_USER', '');
            define('DB_XMLDB_PASS', '');
            define('DB_XMLDB_NAME', '');
            define('DB_XMLDB_TYPE', 'myxml');
            /* Start Block: Prepare the dynaformEditor */
            $G_PUBLISH = new Publisher();
            $sName = 'dynaformEditor';
            $G_PUBLISH->publisherId = $sName;
            $oHeadPublisher = & headPublisher::getSingleton();
            $labesTrans = G::getTranslations(Array('ID_FIELD_DYNAFORM_TEXT', 'ID_FIELD_DYNAFORM_CURRENCY', 'ID_FIELD_DYNAFORM_PERCENTAGE', 'ID_FIELD_DYNAFORM_PASSWORD', 'ID_FIELD_DYNAFORM_SUGGEST', 'ID_FIELD_DYNAFORM_TEXTAREA', 'ID_FIELD_DYNAFORM_TITLE', 'ID_FIELD_DYNAFORM_SUBTITLE', 'ID_FIELD_DYNAFORM_BUTTON', 'ID_FIELD_DYNAFORM_SUBMIT', 'ID_FIELD_DYNAFORM_RESET', 'ID_FIELD_DYNAFORM_DROPDOWN', 'ID_FIELD_DYNAFORM_YESNO', 'ID_FIELD_DYNAFORM_LISTBOX', 'ID_FIELD_DYNAFORM_CHECKBOX', 'ID_FIELD_DYNAFORM_CHECKGROUP', 'ID_FIELD_DYNAFORM_RADIOGROUP', 'DATE_LABEL', 'ID_FIELD_DYNAFORM_HIDDEN', 'ID_FIELD_DYNAFORM_LINK', 'ID_FIELD_DYNAFORM_LINK', 'ID_FIELD_DYNAFORM_FILE', 'ID_FIELD_DYNAFORM_JAVASCRIPT', 'ID_FIELD_DYNAFORM_GRID', 'ID_INDEX'
                    ));
            $oHeadPublisher->addScriptCode("var TRANSLATIONS = " . G::json_encode($labesTrans) . ";");
            $oHeadPublisher->setTitle(G::LoadTranslation('ID_DYNAFORM_EDITOR') . ' - ' . $Properties['DYN_TITLE']);
            $G_PUBLISH->AddContent('blank');
            $this->panelConf['title'] = '';
            $G_PUBLISH->AddContent('panel-init', 'mainPanel', $this->panelConf);
            if ($Properties['DYN_TYPE'] == 'xmlform') {
                $G_PUBLISH->AddContent('xmlform', 'toolbar', 'dynaforms/fields_Toolbar', 'display:none', $Parameters, '', '');
            } else {
                $G_PUBLISH->AddContent('xmlform', 'toolbar', 'dynaforms/fields_ToolbarGrid', 'display:none', $Parameters, '', '');
            }
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Editor', 'display:none', $Parameters, '', '');
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_XmlEditor', 'display:none', $XmlEditor, '', '');
            $G_PUBLISH->AddContent('blank');
            $i = 0;
            $aFields = array();
            $aFields[] = array('XMLNODE_NAME' => 'char', 'TYPE' => 'char', 'UP' => 'char', 'DOWN' => 'char'
            );
            $oSession = new DBSession(new DBConnection(PATH_DYNAFORM . $this->file . '.xml', '', '', '', 'myxml'));
            $oDataset = $oSession->Execute('SELECT * FROM dynaForm WHERE NOT( XMLNODE_NAME = "" ) AND TYPE <> "pmconnection"');
            $iMaximun = $oDataset->count();
            while ($aRow = $oDataset->Read()) {
                $aFields[] = array('XMLNODE_NAME' => $aRow['XMLNODE_NAME'], 'TYPE' => $aRow['TYPE'], 'UP' => ($i > 0 ? G::LoadTranslation('ID_UP') : ''), 'DOWN' => ($i < $iMaximun - 1 ? G::LoadTranslation('ID_DOWN') : ''), 'row__' => ($i + 1)
                );
                $i++;
                break;
            }
            global $_DBArray;
            $_DBArray['fields'] = $aFields;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('fields');
            /**
             * *@Erik-> this is deprecated,.
             * (unuseful) $G_PUBLISH->AddContent('propeltable', 'paged-table', 'dynaforms/fields_List', $oCriteria, $Parameters, '', SYS_URI.'dynaforms/dynaforms_PagedTableAjax');**
             */
            $G_PUBLISH->AddContent('blank');
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_JSEditor', 'display:none', $JSEditor, '', '');
        } catch (Exception $e) {

        }
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Properties', 'display:none', $Properties, '', '');
        //for showHide tab option @Neyek
        $G_PUBLISH->AddContent('blank');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_PREVIEW"), $sName . '[3]', 'dynaformEditor.changeToPreview', 'dynaformEditor.saveCurrentView');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_XML"), $sName . '[4]', 'dynaformEditor.changeToXmlCode', 'dynaformEditor.saveCurrentView');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_HTML"), $sName . '[5]', 'dynaformEditor.changeToHtmlCode', 'dynaformEditor.saveCurrentView');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_FIELDS_LIST"), $sName . '[6]', 'dynaformEditor.changeToFieldsList', 'dynaformEditor.saveCurrentView');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_JAVASCRIPTS"), $sName . '[7]', 'dynaformEditor.changeToJavascripts', 'dynaformEditor.saveCurrentView');
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_PROPERTIES"), $sName . '[8]', 'dynaformEditor.changeToProperties', 'dynaformEditor.saveCurrentView');
        //for showHide tab option @Neyek
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_CONDITIONS_EDITOR"), $sName . '[9]', 'dynaformEditor.changeToShowHide', 'dynaformEditor.saveShowHide');
        $G_PUBLISH->AddContent('panel-close');
        $oHeadPublisher->addScriptFile('/jscore/dynaformEditor/core/dynaformEditor.js');
        //$oHeadPublisher->addScriptFile('/js/dveditor/core/dveditor.js');
        //$oHeadPublisher->addScriptFile('/codepress/codepress.js',1);
        $oHeadPublisher->addScriptFile('/js/codemirror/js/codemirror.js', 1);

        $oHeadPublisher->addScriptFile('/js/grid/core/grid.js');
        $oHeadPublisher->addScriptCode('
    var DYNAFORM_URL="' . $Parameters['URL'] . '";
    leimnud.event.add(window,"load",function(){ loadEditor(); });
    ');
        $oHeadPublisher->addScriptCode(' var jsMeta;var usernameLogged = "' . (isset($_SESSION['USR_USERNAME']) ? $_SESSION['USR_USERNAME'] : '') . '";var SYS_LANG = "' . SYS_LANG . '";');
        G::RenderPage("publish", 'blank');
    }

    /**
     * Get the filename
     *
     * @param string $file
     * @return string
     */
    public function _getFilename($file)
    {
        return (strcasecmp(substr($file, - 5), '_tmp0') == 0) ? substr($file, 0, strlen($file) - 5) : $file;
    }

    /**
     * Set the temporal copy
     *
     * @param string $onOff
     * @return void
     */
    public function _setUseTemporalCopy($onOff)
    {
        $file = self::_getFilename($this->file);
        if ($onOff) {
            $this->file = $file . '_tmp0';
            self::_setTmpData(array('useTmpCopy' => true ));
            if (!file_exists(PATH_DYNAFORM . $file . '.xml')) {
                $this->_createDefaultXmlForm(PATH_DYNAFORM . $file . '.xml');
            }
            //Creates a copy if it doesn't exist, else, use the old copy
            if (!file_exists(PATH_DYNAFORM . $this->file . '.xml')) {
                self::_copyFile(PATH_DYNAFORM . $file . '.xml', PATH_DYNAFORM . $this->file . '.xml');
            }
            if (!file_exists(PATH_DYNAFORM . $this->file . '.html') && file_exists(PATH_DYNAFORM . $file . '.html')) {
                self::_copyFile(PATH_DYNAFORM . $file . '.html', PATH_DYNAFORM . $this->file . '.html');
            }
        } else {
            $this->file = $file;
            self::_setTmpData(array());
        }
    }

    /**
     * Set temporal data
     *
     * @param $data
     * @return void
     */
    public function _setTmpData($data)
    {
        G::verifyPath(PATH_C . 'dynEditor/', true);
        $fp = fopen(PATH_C . 'dynEditor/' . session_id() . '.php', 'w');
        fwrite($fp, '$tmpData=unserialize(\'' . addcslashes(serialize($data), '\\\'') . '\');');
        fclose($fp);
    }

    /**
     * Get temporal data
     *
     * @param string $filename
     * @return array
     */
    public function _getTmpData()
    {
        $tmpData = array();
        $file = PATH_C . 'dynEditor/' . session_id() . '.php';
        if (file_exists($file)) {
            eval(implode('', file($file)));
        }
        return $tmpData;
    }

    /**
     * Copy files
     *
     * @param file $from
     * @param file $to
     * @return void
     */
    public function _copyFile($from, $to)
    {
        $copy = implode('', file($from));
        $fcopy = fopen($to, "w");
        fwrite($fcopy, $copy);
        fclose($fcopy);
    }
}

interface iDynaformEditorAjax
{
    //public function render_preview($A);
}

/**
 * DynaformEditorAjax - DynaformEditorAjax class
 *
 * @package workflow.engine.classes
 */
class dynaformEditorAjax extends dynaformEditor implements iDynaformEditorAjax
{

    /**
     * Constructor of the class dynaformEditorAjax
     *
     * @param var $post
     * @return void
     */
    public function dynaformEditorAjax($post)
    {
        $this->_run($post);
    }

    /**
     * Function Run
     *
     * @param var $post
     * @return void
     */
    public function _run($post)
    {
        WebResource::WebResource($_SERVER['REQUEST_URI'], $post);
    }

    /**
     * Prints the DynaformEditorAjax
     *
     * @param object $A
     * @return ob_get_clean
     */
    public function render_preview($A)
    {
        ob_start();
        $file = G::decrypt($A, URL_KEY);
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->publisherId = 'preview';
        $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true, $G_PUBLISH->publisherId);
        switch (basename($form->template, '.html')) {
            case 'grid':
                $template = 'grid';
                $aAux = array_keys($form->fields);
                if (count($aAux) > 0) {
                    $aFields = (array_combine($aAux, $aAux));
                } else {
                    $aFields = $aAux;
                }
                if (is_array($aFields)) {
                    foreach ($aFields as $key => $val) {
                        $aFields[$key] = array(1 => "", 2 => "", 3 => "", 4 => "", 5 => "");
                    }
                }
                break;
            default:
                $template = 'xmlform_' . $G_PUBLISH->publisherId;
                $aFields = array('__DYNAFORM_OPTIONS' => array('PREVIOUS_STEP' => '#', 'NEXT_STEP' => '#', 'NEXT_STEP_LABEL' => G::loadTranslation('ID_NEXT_STEP'), 'PREVIOUS_ACTION' => 'return false;', 'NEXT_ACTION' => 'return false;'
                    )
                );
        }
        $G_PUBLISH->AddContent('dynaform', $template, $file, '', $aFields, '');
        G::RenderPage('publish', 'raw');
        return ob_get_clean();
    }

    /**
     * Prints the Dynaform in format HTML
     *
     * @param object $A
     * @return array
     */
    public function render_htmledit($A)
    {
        $script = '';
        $file = G::decrypt($A, URL_KEY);
        ob_start();
        global $G_PUBLISH;
        $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->publisherId = '';
        $html = $this->get_htmlcode($A);
        if (!is_string($html)) {
            $error = $html;
            $html = '';
        } else {
            $error = 0;
        }
        $HtmlEditor = array('URL' => $A, 'HTML' => $html
        );
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_HtmlEditor', '', $HtmlEditor, '', '');
        G::RenderPage("publish", 'raw');
        return array('error' => $error, 'html' => ob_get_clean()
        );
    }

    /**
     * Get the html code
     * Loads the stored HTML or the default Template if
     * it doesn't exist.
     *
     * @param object $A
     * @return code html
     */
    public function get_htmlcode($A)
    {
        try {
            $script = null;
            $fileTmp = G::decrypt($A, URL_KEY);
            $form = new Form($fileTmp, PATH_DYNAFORM, SYS_LANG, true);

            //Navigation Bar
            $form->fields = G::array_merges(array("__DYNAFORM_OPTIONS" => new XmlForm_Field_XmlMenu(new Xml_Node("__DYNAFORM_OPTIONS", "complete", "", array("type" => "xmlmenu", "xmlfile" => "gulliver/dynaforms_Options"
                                        )), SYS_LANG, PATH_XMLFORM, $form)
                            ), $form->fields);

            //Loads the stored HTML or the default Template if
            //it doesn't exist.
            $filename = substr($form->fileName, 0, - 3) . ($form->type === "xmlform" ? "" : "." . $form->type) . "html";

            if (!file_exists($filename)) {
                $html = $form->printTemplate($form->template, $script);
            } else {
                $html = implode("", file($filename));
            }

            /*
             * It adds the new fields automatically at the bottom of the form.
             * TODO: �TOP OR BOTTOM?
             * Improving detection algorithm of new fields.
             * Current: Do not check the fields that have already been reviewed (saving)
             * Already checked the temporary file dynaforms editor.
             */
            $tmp = self::_getTmpData();
            if (!isset($tmp['OLD_FIELDS'])) {
                $tmp['OLD_FIELDS'] = array(); //var_dump($html);die;
            }
            $aAux = explode('</form>', $html);
            foreach ($form->fields as $field) {
                if ((strpos($html, '{$form.' . $field->name . '}') === false) && (strpos($html, '{$' . $field->name . '}') === false)) {
                    //Aparantly is new (but could be a deleted or non visible like private type fields)
                    switch (strtolower($field->type)) {
                        case 'private':
                        case 'phpvariable':
                            break;
                        default:
                            if (array_search($field->name, $tmp['OLD_FIELDS']) === false) {
                                //TOP
                                $aAux[0] .= '<br/>{$' . $field->name . '}' . '{$form.' . $field->name . '}';
                                //$html.='<br/>{$'.$field->name.'}'.'{$form.'.$field->name.'}';
                                //BOTTOM
                                //$html='{$'.$field->name.'}'.'{$form.'.$field->name.'}'.$html;
                                //$tmp['OLD_FIELDS'][]=$field->name;
                            }
                    }
                }
            }
            self::_setTmpData($tmp);
            //$html=str_replace('{$form_className}','formDefault', $html );
            $html = str_replace('{$form_className}', 'formDefault', $aAux[0] . '</form>');

            return $html;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Restore the html code
     *
     * @param object $A
     * @return code html
     */
    public function restore_html($A)
    {
        $script = null;
        $fileTmp = G::decrypt($A, URL_KEY);
        $form = new Form($fileTmp, PATH_DYNAFORM, SYS_LANG, true);

        //Navigation Bar
        $form->fields = G::array_merges(array("__DYNAFORM_OPTIONS" => new XmlForm_Field_XmlMenu(new Xml_Node("__DYNAFORM_OPTIONS", "complete", "", array("type" => "xmlmenu", "xmlfile" => "gulliver/dynaforms_Options"
                                    )), SYS_LANG, PATH_XMLFORM, $form)
                        ), $form->fields);

        $form->enableTemplate = false;
        $html = $form->printTemplate($form->template, $script);
        $html = str_replace('{$form_className}', 'formDefault', $html);
        if (file_exists(PATH_DYNAFORM . $fileTmp . '.html')) {
            unlink(PATH_DYNAFORM . $fileTmp . '.html');
        }
        $fp = fopen(PATH_DYNAFORM . $fileTmp . '.html', 'w');
        fwrite($fp, $html);
        fclose($fp);

        return $html;
    }

    /**
     * Set the html code
     *
     * @param object $A
     * @return array
     */
    public function set_htmlcode($A, $htmlcode)
    {
        try {
            $file = G::decrypt($A, URL_KEY);
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $filename = substr($form->fileName, 0, - 3) . ($form->type === 'xmlform' ? '' : '.' . $form->type) . 'html';
            $fp = fopen($filename, 'w');
            fwrite($fp, $htmlcode);
            fclose($fp);
            return 0;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Get the xml code
     *
     * @param object $A
     * @return array
     */
    public function get_xmlcode($A)
    {
        try {
            $file = G::decrypt($A, URL_KEY);
            $xmlcode = implode('', file(PATH_DYNAFORM . $file . '.xml'));
            return array("xmlcode" => $xmlcode, "error" => 0
            );
        } catch (Exception $e) {
            return array("xmlcode" => "", "error" => (array) $e
            );
        }
    }

    /**
     * Set the xml code
     *
     * @param object $A
     * @param array $xmlcode
     * @return string
     */
    public function set_xmlcode($A, $xmlcode)
    {
        $xmlcode = urldecode($xmlcode);
        $file = G::decrypt($A, URL_KEY);
        $xmlcode = str_replace('&nbsp;', ' ', trim($xmlcode));
        $fp = fopen(PATH_DYNAFORM . $file . '.xml', 'w');
        fwrite($fp, $xmlcode);
        fclose($fp);
        return "";
    }

    /**
     * Get the javascript code
     *
     * @param object $A
     * @param string $fieldName
     * @return array
     */
    public function get_javascripts($A, $fieldName)
    {
        try {
            $file = G::decrypt($A, URL_KEY);
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $aOptions = array();
            $sCode = '';
            foreach ($form->fields as $name => $value) {
                if (strcasecmp($value->type, "javascript") == 0) {
                    $aOptions[] = array('key' => $name, 'value' => $name
                    );
                    if ($name == $fieldName) {
                        $sCode = $value->code;
                    }
                }
            }
            return array('aOptions' => $aOptions, 'sCode' => $sCode
            );
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Set the javascript code
     *
     * @param object $A
     * @param string $fieldName
     * @param string $sCode
     * @return array
     */
    public function set_javascript($A, $fieldName, $sCode, $meta = '')
    {
        if ($fieldName == '___pm_boot_strap___') {
            return 0;
        }

        $sCode = urldecode($sCode);
        try {
            $sCode = rtrim($sCode);
            $file = G::decrypt($A, URL_KEY);
            /* $dbc2  = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
              $ses2  = new DBSession($dbc2);
              $ses2->execute(G::replaceDataField("UPDATE dynaForm SET XMLNODE_VALUE = @@CODE WHERE XMLNODE_NAME = @@FIELDNAME ", array('FIELDNAME'=>$fieldName,'CODE'=>$sCode), "myxml" ));
             */

            G::LoadSystem('dynaformhandler');

            $dynaform = new dynaFormHandler(PATH_DYNAFORM . "{$file}.xml");
            $dynaform->replace($fieldName, $fieldName, Array('type' => 'javascript', 'meta' => $meta, '#cdata' => $sCode
            ));

            return 0;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Get properties of the dynaForm
     *
     * @param file $A
     * @param string $DYN_UID
     * @return array
     */
    public function get_properties($A, $DYN_UID)
    {
        $file = G::decrypt($A, URL_KEY);
        $tmp = self::_getTmpData();
        if (!(isset($tmp['Properties']) && isset($tmp['useTmpCopy']))) {
            $dynaform = new dynaform();
            $dynaform->load($DYN_UID);
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $Properties = array('A' => $A, 'DYN_UID' => $dynaform->getDynUid(), 'PRO_UID' => $dynaform->getProUid(), 'DYN_TITLE' => $dynaform->getDynTitle(), 'DYN_TYPE' => $dynaform->getDynType(), 'DYN_DESCRIPTION' => $dynaform->getDynDescription(), 'WIDTH' => $form->width,
                //'ENABLETEMPLATE'=> $form->enableTemplate,
                'MODE' => $form->mode, 'PRINTDYNAFORM' => $form->printdynaform, 'ADJUSTGRIDSWIDTH' => $form->adjustgridswidth, 'NEXTSTEPSAVE' => $form->nextstepsave
            );
            $tmp['Properties'] = $Properties;
            self::_setTmpData($tmp);
        } else {
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $Properties = $tmp['Properties'];
            if (!isset($Properties['ENABLETEMPLATE'])) {
                $Properties['ENABLETEMPLATE'] = "0";
            }
            $Properties['WIDTH'] = $form->width;
            $Properties['MODE'] = $form->mode;
        }
        return $Properties;
    }

    /**
     * Set properties of the dynaForm
     *
     * @param file $A
     * @param string $DYN_UID
     * @param array $getFields
     * @return array
     */
    public function set_properties($A, $DYN_UID, $getFields)
    {
        try {
            $post = array();
            parse_str($getFields, $post);
            $Fields = $post['form'];
            //if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";
            $file = G::decrypt($A, URL_KEY);
            $tmp = self::_getTmpData();
            if (!isset($tmp['useTmpCopy'])) {
                $dynaform = new dynaform();
                $dynaform->update($Fields);
            } else {
                $tmp['Properties'] = $Fields;
                self::_setTmpData($tmp);
            }
            $dynaform = new dynaFormHandler(PATH_DYNAFORM . "{$file}.xml");
            $dbc2 = new DBConnection(PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml');
            $ses2 = new DBSession($dbc2);
            //if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";

            /* if (isset($Fields['ENABLETEMPLATE'])) {
              $ses2->execute(G::replaceDataField("UPDATE . SET ENABLETEMPLATE = @@ENABLETEMPLATE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
              } */
            if (isset($Fields['DYN_TYPE'])) {
                //$ses2->execute( G::replaceDataField( "UPDATE . SET TYPE = @@DYN_TYPE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('type', $Fields['DYN_TYPE']);
            }
            if (isset($Fields['WIDTH'])) {
                // $ses2->execute( G::replaceDataField( "UPDATE . SET WIDTH = @@WIDTH WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('width', $Fields['WIDTH']);
                //g::pr($dynaform->getHeaderAttribute('width'));
            }
            if (isset($Fields['MODE'])) {
                // $ses2->execute( G::replaceDataField( "UPDATE . SET MODE = @@MODE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('mode', $Fields['MODE']);
            }
            if (isset($Fields['NEXTSTEPSAVE'])) {
                //$ses2->execute( G::replaceDataField( "UPDATE . SET NEXTSTEPSAVE = @@NEXTSTEPSAVE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('nextstepsave', $Fields['NEXTSTEPSAVE']);
            }
            if (isset($Fields['PRINTDYNAFORM'])) {
                //$ses2->execute( G::replaceDataField( "UPDATE . SET PRINTDYNAFORM = @@PRINTDYNAFORM WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('printdynaform', $Fields['PRINTDYNAFORM']);
            }
            if (isset($Fields['ADJUSTGRIDSWIDTH'])) {
                //$ses2->execute( G::replaceDataField( "UPDATE . SET ADJUSTGRIDSWIDTH = @@ADJUSTGRIDSWIDTH WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
                $dynaform->modifyHeaderAttribute('adjustgridswidth', $Fields['ADJUSTGRIDSWIDTH']);
            }

            return 0;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Get enable template
     *
     * @param object $A
     * @return string
     */
    public function get_enabletemplate($A)
    {
        $file = G::decrypt($A, URL_KEY);
        $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
        return $form->enableTemplate;
    }

    /**
     * Set enable template
     *
     * @param object $A
     * @param string $value
     * @return string
     */
    public function set_enabletemplate($A, $value)
    {
        $file = G::decrypt($A, URL_KEY);
        $value = $value == "1" ? "1" : "0";
        // $dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' );
        // $ses2 = new DBSession( $dbc2 );
        // $ses2->execute( "UPDATE . SET ENABLETEMPLATE = '$value'" );

        $dynaform = new dynaFormHandler(PATH_DYNAFORM . "{$file}.xml");
        $dynaform->modifyHeaderAttribute('enabletemplate', $value);

        return $value;
    }

    /**
     * Save a dynaForm
     *
     * @param object $A
     * @param string $DYN_UID
     * @return array
     */
    public function save($A, $DYN_UID)
    {
        try {
            $answer = 0;
            $file = G::decrypt($A, URL_KEY);
            $tmp = self::_getTmpData();
            if (isset($tmp['useTmpCopy'])) {
                /* Save Register */
                $dynaform = new dynaform();
                $dynaform->update($tmp['Properties']);
                /* Save file */
                $copy = implode('', file(PATH_DYNAFORM . $file . '.xml'));
                /*
                 * added by krlos carlos/a/colosa.com
                 * in here we are validation if a xmlform has a submit action
                 */
                //      if (!preg_match("/type=\"submit\"/",$copy) && !preg_match("/type=\"grid\"/",$copy) && !isset($_SESSION['submitAction']) ){
                if (!preg_match("/type=\"submit\"/", $copy) && !preg_match("/type=\"grid\"/", $copy)) {
                    //        $_SESSION['submitAction'] = 1;
                    $answer = 'noSub';
                }
                $copyHtml = false;
                if (file_exists(PATH_DYNAFORM . $file . '.html')) {
                    $copyHtml = implode('', file(PATH_DYNAFORM . $file . '.html'));
                }
                $file = dynaformEditor::_getFilename($file);
                $fcopy = fopen(PATH_DYNAFORM . $file . '.xml', "w");
                fwrite($fcopy, $copy);
                fclose($fcopy);
                if ($copyHtml) {
                    $fcopy = fopen(PATH_DYNAFORM . $file . '.html', "w");
                    fwrite($fcopy, $copyHtml);
                    fclose($fcopy);
                }
            } else {
                //throw new Exception("It should not come here unless you have disabled the temporary copy.");
            }
            return $answer;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Close a dynaform
     *
     * @param object $A
     * @return array
     */
    public function close($A)
    {
        try {
            /*
             * we are unseting this variable. It's our control about to save the xmlfrom
             */
            //   	unset($_SESSION['submitAction']);
            $file = G::decrypt($A, URL_KEY);
            //return(array('response'=>PATH_DYNAFORM  . $file . '.xml'));
            /* Delete the temporal copy */
            $tmp = self::_getTmpData();
            $xmlFile = PATH_DYNAFORM . $file . '.xml';
            $htmlFile = PATH_DYNAFORM . $file . '.html';
            //return(array('response'=>$tmp['useTmpCopy']));
            if (isset($tmp['useTmpCopy'])) {
                //return(array('response'=>PATH_DYNAFORM  . $file . '.xml'));
                if ($file !== dynaformEditor::_getFilename($file)) {
                    //          return(array('response'=>PATH_DYNAFORM  . $file . '.xml'));
                    if (file_exists($xmlFile)) {
                        unlink($xmlFile);
                    }
                    if (file_exists($htmlFile)) {
                        unlink($htmlFile);
                    }
                }
            }
            return 0;
        } catch (Exception $e) {
            return (array) $e;
        }
    }

    /**
     * Checks if a dynaform was changed
     *
     * @param file $A
     * @param string $DYN_UID
     * @return array
     */
    public function is_modified($A, $DYN_UID)
    {
        $file = G::decrypt($A, URL_KEY);
        try {
            /* Compare Properties */
            $dynaform = new dynaform();
            $dynaform->load($DYN_UID);
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $sp = array('A' => $A, 'DYN_UID' => $dynaform->getDynUid(), 'PRO_UID' => $dynaform->getProUid(), 'DYN_TITLE' => $dynaform->getDynTitle(), 'DYN_TYPE' => $dynaform->getDynType(), 'DYN_DESCRIPTION' => $dynaform->getDynDescription(), 'WIDTH' => $form->width, 'ENABLETEMPLATE' => $form->enableTemplate, 'MODE' => $form->mode
            );
            $P = self::get_properties($A, $DYN_UID);
            if (!isset($P['DYN_TITLE'])) {
                $P['DYN_TITLE'] = $sp['DYN_TITLE'];
            }
            if (!isset($P['DYN_TYPE'])) {
                $P['DYN_TYPE'] = $sp['DYN_TYPE'];
            }
            if (!isset($P['DYN_DESCRIPTION'])) {
                $P['DYN_DESCRIPTION'] = $sp['DYN_DESCRIPTION'];
            }
            if (!isset($P['WIDTH'])) {
                $P['WIDTH'] = $sp['WIDTH'];
            }
            if (!isset($P['ENABLETEMPLATE'])) {
                $P['ENABLETEMPLATE'] = $sp['ENABLETEMPLATE'];
            }
            if (!isset($P['MODE'])) {
                $P['MODE'] = $sp['MODE'];
            }
            $modPro = ($sp['DYN_TITLE'] != $P['DYN_TITLE']) || ($sp['DYN_TYPE'] != $P['DYN_TYPE']) || ($sp['DYN_DESCRIPTION'] != $P['DYN_DESCRIPTION']);
            /* ||
            ($sp['WIDTH']!=$P['WIDTH']) ||
            ($sp['ENABLETEMPLATE']!=$P['ENABLETEMPLATE']) ||
            ($sp['MODE']!=$P['MODE']) */
            /* Compare copies */
            $fileOrigen = dynaformEditor::_getFilename($file);
            $copy = implode('', file(PATH_DYNAFORM . $file . '.xml'));
            $origen = implode('', file(PATH_DYNAFORM . $fileOrigen . '.xml'));
            $copyHTML = file_exists(PATH_DYNAFORM . $file . '.html') ? implode('', file(PATH_DYNAFORM . $file . '.html')) : false;
            $origenHTML = file_exists(PATH_DYNAFORM . $fileOrigen . '.html') ? implode('', file(PATH_DYNAFORM . $fileOrigen . '.html')) : false;
            $modFile = ($copy !== $origen) || ($origenHTML && ($copyHTML !== $origenHTML));
            //Return
            //return array("*message"=>sprintf("%s, (%s= %s %s):", $modPro?"1":"0" , $modFile?"1":"0", ($copy!==$origen)?"1":"0" , ($origenHTML && ($copyHTML!==$origenHTML))?"1":"0" ));
            //die("c'est fini");
            return $modPro || $modFile;
        } catch (Exception $e) {
            return (array) $e;
        }
    }
}
