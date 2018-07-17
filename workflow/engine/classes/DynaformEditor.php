<?php

/**
 * Created on 21/12/2007
 * Dynaform - Dynaform 
/**
 *
 * @package workflow.engine.classes
 */
class DynaformEditor extends WebResource
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
    public function __construct($get)
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
            $Properties = DynaformEditorAjax::get_properties($A, $this->dyn_uid);
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
        if ($Properties['DYN_TYPE'] != 'grid') {
            $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_HTML"), $sName . '[5]', 'dynaformEditor.changeToHtmlCode', 'dynaformEditor.saveCurrentView');
        }
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_FIELDS_LIST"), $sName . '[6]', 'dynaformEditor.changeToFieldsList', 'dynaformEditor.saveCurrentView');
        if ($Properties["DYN_TYPE"] != "grid") {
            $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_JAVASCRIPTS"), $sName . '[7]', 'dynaformEditor.changeToJavascripts', 'dynaformEditor.saveCurrentView');
        }
        $G_PUBLISH->AddContent('panel-tab', G::LoadTranslation("ID_PROPERTIES"), $sName . '[8]', 'dynaformEditor.changeToProperties', 'dynaformEditor.saveCurrentView');

        //for showHide tab option @Neyek
        if ($Properties["DYN_TYPE"] != "grid") {
            $G_PUBLISH->AddContent("panel-tab", G::LoadTranslation("ID_CONDITIONS_EDITOR"), $sName . "[9]", "dynaformEditor.changeToShowHide", "dynaformEditor.saveShowHide");
        }

        $G_PUBLISH->AddContent('panel-close');
        $oHeadPublisher->addScriptFile("/js/maborak/core/maborak.loader.js",2);
        $oHeadPublisher->addScriptFile('/jscore/dynaformEditor/core/dynaformEditor.js');

        $oHeadPublisher->addScriptFile('/js/codemirrorOld/js/codemirror.js',1);

        $oHeadPublisher->addScriptFile('/js/grid/core/grid.js');
        $oHeadPublisher->addScriptCode('
        var DYNAFORM_URL="' . $Parameters['URL'] . '";
        leimnud.event.add(window,"load",function(){ loadEditor(); });
        ');
        $oHeadPublisher->addScriptCode(' var jsMeta;var __usernameLoggedDE__ = "' . (isset($_SESSION['USR_USERNAME']) ? $_SESSION['USR_USERNAME'] : '') . '";var SYS_LANG = "' . SYS_LANG . '";var __DYN_UID__ = "' . $this->dyn_uid . '";');

        $arrayParameterAux = $Parameters;
        $arrayParameterAux["DYNAFORM_NAME"] = base64_encode($arrayParameterAux["DYNAFORM_NAME"]);
        $oHeadPublisher->addScriptCode('var dynaformEditorParams = \'' . serialize($arrayParameterAux) . '\';');

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
