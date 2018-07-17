<?php

/**
 * Created on 21/12/2007
 * Dynaform - Dynaform 
/**
 * DynaformEditorAjax - DynaformEditorAjax class
 *
 * @package workflow.engine.classes
 */
class DynaformEditorAjax extends DynaformEditor implements IDynaformEditorAjax
{

    /**
     * Constructor of the class dynaformEditorAjax
     *
     * @param var $post
     * @return void
     */
    public function __construct($post)
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
        $HtmlEditor = array('URL' => $A, 'HTML' => $html, 'DYN_UID' => $file );
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
            $form->fields = G::array_merges(array("__DYNAFORM_OPTIONS" => new XmlFormFieldXmlMenu(new Xml_Node("__DYNAFORM_OPTIONS", "complete", "", array("type" => "xmlmenu", "xmlfile" => "gulliver/dynaforms_Options"
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

        $filter = new InputFilter();
        $script = null;
        $fileTmp = G::decrypt($A, URL_KEY);
        $form = new Form($fileTmp, PATH_DYNAFORM, SYS_LANG, true);

        //Navigation Bar
        $form->fields = G::array_merges(array("__DYNAFORM_OPTIONS" => new XmlFormFieldXmlMenu(new Xml_Node("__DYNAFORM_OPTIONS", "complete", "", array("type" => "xmlmenu", "xmlfile" => "gulliver/dynaforms_Options"
                                    )), SYS_LANG, PATH_XMLFORM, $form)
                        ), $form->fields);

        $form->enableTemplate = false;
        $html = $form->printTemplate($form->template, $script);
        $html = str_replace('{$form_className}', 'formDefault', $html);
        $pathTmp = $filter->xssFilterHard(PATH_DYNAFORM . $fileTmp . '.html', 'path');
        if (file_exists($pathTmp)) {
            unlink($pathTmp);
        }
        $fp = fopen($pathTmp, 'w');
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

            $filter = new InputFilter();
            $iOcurrences = preg_match_all('/\{\$.*?\}/im', $htmlcode, $matches);
            if ($iOcurrences) {
                if (isset($matches[0])) {
                    $tagsHtml = $matches[0];
                    foreach ($tagsHtml as $value) {
                        $aTagVar = strip_tags($value);
                        if ($value != $aTagVar) {
                            $htmlcode = str_replace($value, $aTagVar, $htmlcode);
                        }
                    }
                }
            }
            $file = G::decrypt($A, URL_KEY);
            $form = new Form($file, PATH_DYNAFORM, SYS_LANG, true);
            $filename = substr($form->fileName, 0, - 3) . ($form->type === 'xmlform' ? '' : '.' . $form->type) . 'html';
            $filename = $filter->xssFilterHard($filename, 'path');
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

        $filter = new InputFilter();
        $xmlcode = urldecode($xmlcode);
        $file = G::decrypt($A, URL_KEY);
        $xmlcode = str_replace('&nbsp;', ' ', trim($xmlcode));
        $pathFile = $filter->xssFilterHard(PATH_DYNAFORM . $file . '.xml', "path");
        $fp = fopen($pathFile, 'w');
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

        $filter = new InputFilter();
        $fieldName = $filter->xssFilterHard($fieldName, 'path');
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


            $pathFile = $filter->xssFilterHard(PATH_DYNAFORM . "{$file}.xml", 'path');
            $dynaform = new DynaformHandler($pathFile);
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
            $dynaform = new Dynaform();
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

            $filter = new InputFilter();
            $post = array();
            parse_str($getFields, $post);
            $Fields = $post['form'];
            //if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";
            $file = G::decrypt($A, URL_KEY);
            $tmp = self::_getTmpData();
            if (!isset($tmp['useTmpCopy'])) {
                $dynaform = new Dynaform();
                $dynaform->update($Fields);
            } else {
                $tmp['Properties'] = $Fields;
                self::_setTmpData($tmp);
            }
            $pathFile = $filter->xssFilterHard(PATH_DYNAFORM . "{$file}.xml", 'path');
            $dynaform = new DynaformHandler($pathFile);
            $dbc2 = new DBConnection($pathFile, '', '', '', 'myxml');
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

        $filter = new InputFilter();
        $file = G::decrypt($A, URL_KEY);
        $value = $value == "1" ? "1" : "0";
        // $dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' );
        // $ses2 = new DBSession( $dbc2 );
        // $ses2->execute( "UPDATE . SET ENABLETEMPLATE = '$value'" );
        $pathFile = $filter->xssFilterHard(PATH_DYNAFORM . "{$file}.xml", 'path');
        $dynaform = new DynaformHandler($pathFile);
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
            if (isset($tmp['Properties'])){
              $fileFirst =  $tmp['Properties']['PRO_UID'].'/'.$tmp['Properties']['DYN_UID'];
            }
            if (isset($tmp['useTmpCopy'])) {
                /* Save Register */
                $dynaform = new Dynaform();
                $dynaform->update($tmp['Properties']);
                /* Save file */
                $copyFirst = implode('', file(PATH_DYNAFORM . $fileFirst . '.xml'));
                $copy = implode('', file(PATH_DYNAFORM . $file . '.xml'));
                /*Check differences between XML*/
                $elementFirst = new SimpleXMLElement($copyFirst);
                $elementCopy  = new SimpleXMLElement($copy);
                $desAdd = '';
                $desDel = '';
                //Check the new fields
                foreach ($elementCopy as $key1 => $row1){
                  $swAll = true;
                  foreach ($elementFirst as $key2 => $row2){
                    if ($key1 == $key2){
                      $swAll = false;
                      break;
                    }
                  }
                  if ($swAll){
                    $desAdd .= $key1." ";
                  }
                }
                //Check the delete fields
                foreach ($elementFirst as $key1 => $row1){
                  $swAll = true;
                  foreach ($elementCopy as $key2 => $row2){
                    if ($key1 == $key2){
                      $swAll = false;
                      break;
                    }
                  }
                  if ($swAll){
                    $desDel .= $key1." ";
                  }
                }
                
                $mode    = empty($tmp['Properties']['MODE'])? 'Determined by Fields' : $tmp['Properties']['MODE'];
                $auditDescription = "Dynaform Title: ".$tmp['Properties']['DYN_TITLE'].", Type: ".$tmp['Properties']['DYN_TYPE'].", Description: ".$tmp['Properties']['DYN_DESCRIPTION'].", Mode: ".$mode;
                if($desAdd != ''){
                  $auditDescription .= ", Field(s) Add: ".$desAdd;
                }
                if($desDel != ''){
                  $auditDescription .= ", Field(s) Delete: ".$desDel;
                }
                //Add Audit Log
                G::auditLog("UpdateDynaform", $auditDescription);

                
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
                $file = DynaformEditor::_getFilename($file);
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
                if ($file !== DynaformEditor::_getFilename($file)) {
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
            $dynaform = new Dynaform();
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
            $fileOrigen = DynaformEditor::_getFilename($file);
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
    /*
    Functionality: Funcion que convierte objecto en array
    Parameters :   Object $object que queremos convertir
    Return:        Array 
    */
    public function convertObjectToArray($object){ 
      if( !is_object( $object ) && !is_array( $object ) ){
        return $object;
      }
      if( is_object( $object ) ){
        $object = get_object_vars( $object );
      }
      return array_map( 'objectToArray', $object );
    }
}
