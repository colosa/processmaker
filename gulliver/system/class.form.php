<?php

/**
 * class.database_base.php
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
 * Class Form
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class Form extends XmlForm
{
    public $id = '';
    public $width = 600;
    public $title = '';
    public $fields = array ();
    public $values = array ();
    public $action = '';
    public $ajaxServer = '';
    public $enableTemplate = false;
    public $ajaxSubmit = false;
    public $callback = 'function(){}';
    public $in_progress = 'function(){}';
    public $template;
    public $className = "formDefault";
    public $objectRequiredFields = null;
    public $nextstepsave = '';
    public $printdynaform = '';
    public $adjustgridswidth = '0';

    public $visual_frontend;

    /**
     * Function setDefaultValues
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */

    public function setDefaultValues ()
    {
        foreach ($this->fields as $name => $content) {
            if (is_object( $content ) && get_class( $content ) != '__PHP_Incomplete_Class') {
                if (isset( $content->defaultValue )) {
                    switch ($content->type) {
                        case "checkgroup":
                        case "listbox":
                            $defaultValueAux = trim($content->defaultValue);

                            if ($defaultValueAux != "") {
                                $this->values[$name] = $content->defaultValue;
                            } else {
                                $this->values[$name] = "__NULL__";
                            }
                            break;
                        default:
                            $this->values[$name] = $content->defaultValue;
                            break;
                    }
                } else {
                    switch ($content->type) {
                        case "checkgroup":
                        case "listbox":
                            $this->values[$name] = "__NULL__";
                            break;
                        default:
                            $this->values[$name] = "";
                            break;
                    }
                }
            } else {
                $this->values[$name] = '';
            }
        }

        foreach ($this->fields as $k => $v) {
            if (is_object( $v )) {
                //julichu
                $this->fields[$k]->owner = & $this;
            }
        }
    }

    /**
     * Function Form
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string filename
     * @param string home
     * @param string language
     * @param string forceParse
     * @param string $visual_frontend
     * @return string
     */
    public function Form ($filename, $home = '', $language = '', $forceParse = false, $visual_frontend = null)
    {
        $this->visual_frontend = $visual_frontend;
        if ($language === '') {
            $language = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        }
        if ($home === '') {
            $home = defined( 'PATH_XMLFORM' ) ? PATH_XMLFORM : (defined( 'PATH_DYNAFORM' ) ? PATH_DYNAFORM : '');
        }
            //to do: obtain the error code in case the xml parsing has errors: DONE
            //Load and parse the xml file
        if (substr( $filename, - 4 ) !== '.xml') {
            $filename = $filename . '.xml';
        }
        $this->home = $home;
        $res = parent::parseFile( $filename, $language, $forceParse );
        if ($res == 1) {
            trigger_error( 'Faild to parse file ' . $filename . '.', E_USER_ERROR );
        }
        if ($res == 2) {
            trigger_error( 'Faild to create cache file "' . $xmlform->parsedFile . '".', E_USER_ERROR );
        }
        $this->setDefaultValues();

        //to do: review if you can use the same form twice. in order to use once or not.
        //DONE: Use require to be able to use the same xmlform more than once.
        foreach ($this->fields as $k => $v) {
            //too memory? but it fails if it's loaded with baneco.xml with SYS_LANG='es'
            //NOTE: This fails apparently when class of  ($this->fields[$k]) is PHP_Incomplete_Class (because of cache)
            if (is_object( $v )) {
                //julichu
                $this->fields[$k]->owner = & $this;
                if ($this->fields[$k]->type === 'grid') {
                    $this->fields[$k]->parseFile( $home, $language );
                }
            }
        }
        $this->template = PATH_CORE . 'templates/' . $this->type . '.html';
    }

    /**
     * Function printTemplate
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string template
     * @param string scriptContent
     * @return string
     */
    public function printTemplate ($template, &$scriptContent)
    {
        if (! file_exists( $template )) {
            throw (new Exception( 'Template "' . basename( $template ) . '" doesn`t exist.' ));
        }
        $o = new xmlformTemplate( $this, $template );
        if (is_array( reset( $this->values ) )) {
            $this->rows = count( reset( $this->values ) );
        }
        if ($this->enableTemplate) {
            $filename = substr( $this->fileName, 0, - 3 ) . ($this->type === 'xmlform' ? '' : '.' . $this->type) . 'html';
            if (! file_exists( $filename )) {
                $o->template = $o->printTemplate( $this );
                $f = fopen( $filename, 'w+' );
                fwrite( $f, $o->template );
                fclose( $f );
            }
            $o->template = implode( '', file( $filename ) );
        } else {
            $o->template = $o->printTemplate( $this );
        }
        return $o->template;
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string template
     * @param string scriptContent
     * @return string
     */
    public function render ($template, &$scriptContent)
    {
        /**
         * *
         * This section was added for store the current used template.
         */
        $tmp_var = explode( '/', $template );
        if (is_array( $tmp_var )) {
            $tmp_var = $tmp_var[sizeof( $tmp_var ) - 1];
            $this->using_template = $tmp_var;
        }
        /**
         */
        $this->template = $template;
        $o = new xmlformTemplate( $this, $template );
        $values = $this->values;
        $aValuekeys = array_keys( $values );
        if (isset( $aValuekeys[0] ) && ((int) $aValuekeys[0] == 1)) {
            $values = XmlForm_Field_Grid::flipValues( $values );
        }
            //TODO: Review when $values of a grid has only one row it is converted as a $values for a list (when template="grid" at addContent())
        if (is_array( reset( $values ) )) {
            $this->rows = count( reset( $values ) );
        }
        if ($this->enableTemplate) {
            $filename = substr( $this->fileName, 0, - 3 ) . 'html';
            if (! file_exists( $filename )) {
                $o->template = $o->printTemplate( $this );
                $f = fopen( $filename, 'w+' );
                fwrite( $f, $o->template );
                fclose( $f );
            }
            $o->template = implode( '', file( $filename ) );
        } else {
            $o->template = $o->printTemplate( $this );
        }
        $scriptContent = $o->printJavaScript( $this );
        $content = $o->printObject( $this );
        return $content;
    }

    /**
     * Function setValues
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param array $newValues
     * @return string
     */
    public function setValues ($newValues = array())
    {
        if (! is_array( $newValues )) {
            return;
        }

        foreach ($this->fields as $k => $v) {
            if (array_key_exists( $k, $newValues )) {
                if (is_array( $newValues[$k] )) {
                    $this->values[$k] = array ();
                    foreach ($newValues[$k] as $j => $item) {
                        if ($this->fields[$k]->validateValue( $newValues[$k][$j], $this )) {
                            $this->values[$k][$j] = $newValues[$k][$j];

                            switch ($this->fields[$k]->type) {
                                case "link":
                                    if (isset($newValues[$k . "_label"][$j])) {
                                        $this->values[$k . "_label"][$j] = $newValues[$k . "_label"][$j];
                                    }
                                    break;
                            }
                        }
                    }
                    if ((sizeof( $this->values[$k] ) === 1) && ($v->type !== 'grid') && isset( $this->values[$k][0] )) {
                        $this->values[$k] = $this->values[$k][0];
                    }
                    if (sizeof( $this->values[$k] ) === 0) {
                        $this->values[$k] = '';
                    }
                } else {
                    if ($this->fields[$k]->validateValue( $newValues[$k], $this )) {
                        $this->values[$k] = $newValues[$k];

                        switch ($this->fields[$k]->type) {
                            case "link":
                                if (isset($newValues[$k . "_label"])) {
                                    $this->values[$k . "_label"] = $newValues[$k . "_label"];
                                }
                                break;
                        }
                    }
                }
            }
        }
        foreach ($newValues as $k => $v) {
            if (strpos( $k, 'SYS_GRID_AGGREGATE_' ) !== false) {
                $this->values[$k] = $newValues[$k];
            }
        }
        foreach ($this->fields as $k => $v) {
            if (is_object( $this->fields[$k] ) && get_class( $this->fields[$k] ) != '__PHP_Incomplete_Class') {
                $this->fields[$k]->owner = & $this;
            }
        }
        if (isset( $this->labelWidth )) {
            $nMaxPorcent = 1024;
            $nWidth = stripos( $this->width, '%' );
            if ($nWidth > 0) {
                $sStrFind = $this->width;
                $result = substr( $sStrFind, 0, strpos( $sStrFind, '%' ) );
                $nWidth = (int) (($nMaxPorcent / 100) * $result);
            } else {
                $nWidth = (int) $this->width;
                $nMaxPorcent = $nWidth;
            }
            $nLabelWidth = stripos( $this->labelWidth, '%' );
            if ($nLabelWidth > 0) {
                $sStrFind = $this->labelWidth;
                $result = substr( $sStrFind, 0, strpos( $sStrFind, '%' ) );
                $nLabelWidth = (int) (($nWidth / 100) * $result);
            } else {
                $nLabelWidth = (int) $this->labelWidth;
            }
            // krumo($nWidth,$nLabelWidth);
            if (($nWidth - $nLabelWidth) > 0) {
                $this->fieldContentWidth = (int) ($nWidth - $nLabelWidth);
            }
        }
    }

    /**
     * Function getFields
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string template
     * @param int $therow
     * @return string
     */
    public function getFields ($template, $therow = -1)
    {
        $o = new xmlformTemplate( $this, $template );
        return $o->getFields( $this, $therow );
    }

    /**
     * Function that validates the values retrieved in $_POST
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return array $_POST['form']
     */
    public function validatePost ()
    {
        $_POST['form'] = $this->validateFields( $_POST['form'] );
        return $_POST['form'] = $this->validateArray( $_POST['form'] );
    }

    /**
     * Function that validates the values retrieved in an Array:
     * ex $_POST['form']
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param array $newValues
     * @return array
     */
    public function validateArray ($newValues)
    {
        $values = array ();
        foreach ($this->fields as $k => $v) {
            if (($v->type != 'submit')) {
                if ($v->type != 'file') {
                    if (array_key_exists( $k, $newValues )) {
                        switch ($v->type) {
                            case 'radiogroup':
                                $values[$k] = $newValues[$k];
                                $values[$k . "_label"] = $newValues[$k . "_label"] = $v->options[$newValues[$k]];
                                break;
                            case 'suggest':
                                $values[$k] = $newValues[$k];
                                $values[$k . "_label"] = $newValues[$k . "_label"];
                                break;
                            case 'checkgroup':
                            case 'listbox':
                                if (is_array( $newValues[$k] )) {
                                    $values[$k] = $values[$k . "_label"] = null;
                                    foreach ($newValues[$k] as $i => $value) {
                                        //if $value is empty continue with the next loop, because this is a not selected/checked item
                                        //if (trim( $value ) == '') {
                                        //    continue;
                                        //}

                                        $values[$k] .= (($i != 0) ? "|" : null) . $value;

                                        if (isset( $v->options[$value] )) {
                                            $values[$k . "_label"] .= (($i != 0) ? "|" : null) . $v->options[$value];
                                        } else {
                                            // if hasn't options try execute a sql sentence
                                            $query = G::replaceDataField( $this->fields[$k]->sql, $newValues );

                                            if ($query != '') {
                                                // execute just if a query was set, it should be not empty
                                                //we do the query to the external connection and we've got the label
                                                $con = Propel::getConnection( $this->fields[$k]->sqlConnection != "" ? $this->fields[$k]->sqlConnection : "workflow" ); //use default connection workflow if connection is not defined. Same as Dynaforms


                                                $stmt = $con->prepareStatement( $query );
                                                $rs = $stmt->executeQuery( ResultSet::FETCHMODE_NUM );

                                                while ($rs->next()) {
                                                    list ($rowId, $rowContent) = array_values( $rs->getRow() ); //This to be sure that the array is numeric. Some cases when is DBArray result it returns an associative. By JHL


                                                    if ($value == $rowId) {
                                                        $values[$k . "_label"] .= (($i != 0) ? "|" : null) . $rowContent;
                                                        break;
                                                    }
                                                }
                                            }

                                        }
                                    }

                                    $newValues[$k . "_label"] = (isset( $values[$k . "_label"] )) ? $values[$k . "_label"] : null;
                                } else {
                                    $values[$k] = $newValues[$k];
                                    $values[$k . "_label"] = (isset( $newValues[$k . "_label"] )) ? $newValues[$k . "_label"] : null;
                                }
                                break;
                            case 'dropdown':
                                $values[$k] = $newValues[$k];

                                if (isset( $v->options[$newValues[$k]] )) {
                                    $values[$k . "_label"] = $newValues[$k . "_label"] = $v->options[$newValues[$k]];
                                } else {
                                    $query = G::replaceDataField( $this->fields[$k]->sql, $newValues );

                                    // execute just if a query was set, it should be not empty
                                    if (trim( $query ) == '') {
                                        continue; //if it is  empty string skip it
                                    }

                                    //we do the query to the external connection and we've got the label
                                    $con = Propel::getConnection( $this->fields[$k]->sqlConnection != "" ? $this->fields[$k]->sqlConnection : "workflow" );
                                    $stmt = $con->prepareStatement( $query );
                                    $rs = $stmt->executeQuery( ResultSet::FETCHMODE_NUM );
                                    while ($rs->next()) {
                                        list ($rowId, $rowContent) = $rs->getRow();
                                        if ($newValues[$k] == $rowId) {
                                            $values[$k . "_label"] = $rowContent;
                                            break;
                                        }
                                    }
                                }
                                break;
                            case "link":
                                $values[$k] = $newValues[$k];
                                $values[$k . "_label"] = $newValues[$k . "_label"];
                                break;
                            case 'grid':
                                foreach ($newValues[$k] as $j => $item) {
                                    if (is_array( $item )) {
                                        $values[$k][$j] = $this->fields[$k]->maskValue( $newValues[$k][$j], $this );
                                        foreach ($item as $kk => $vv) {
                                            if (isset($this->fields[$k]->fields[$kk])) {
                                                if ($this->fields[$k]->fields[$kk]->type != "file") {
                                                    switch ($this->fields[$k]->fields[$kk]->type) {
                                                        case "dropdown":
                                                            //We need to know which fields are dropdowns
                                                            $values[$k][$j][$kk] = $newValues[$k][$j][$kk];

                                                            if ($this->fields[$k]->validateValue( $newValues[$k][$j], $this )) {
                                                                //If the dropdown has otions
                                                                if (isset( $this->fields[$k]->fields[$kk]->options[$vv] )) {
                                                                    $values[$k][$j][$kk . "_label"] = $newValues[$k][$j][$kk . "_label"] = $this->fields[$k]->fields[$kk]->options[$vv];
                                                                } else {
                                                                    //If hasn't options try execute a sql sentence
                                                                    $query = G::replaceDataField( $this->fields[$k]->fields[$kk]->sql, $values[$k][$j] );
                                                                    $con = Propel::getConnection( (! empty( $this->fields[$k]->fields[$kk]->sqlConnection )) ? $this->fields[$k]->fields[$kk]->sqlConnection : "workflow" );
                                                                    $stmt = $con->prepareStatement( $query );

                                                                    //Execute just if a query was set, it should be not empty
                                                                    if (trim( $query ) == "") {
                                                                        //if it is  empty string skip it
                                                                        continue;
                                                                    }

                                                                    $rs = $stmt->executeQuery( ResultSet::FETCHMODE_NUM );

                                                                    while ($rs->next()) {
                                                                        //From the query executed we only need certain elements
                                                                        //note added by krlos pacha carlos[at]colosa[dot]com
                                                                        //the following line has the correct values because the query return an associative array. Related 7945 bug
                                                                        list ($rowId, $rowContent) = explode( ",", implode( ",", $rs->getRow() ) );

                                                                        if ($vv == $rowId) {
                                                                            $values[$k][$j][$kk . "_label"] = $newValues[$k][$j][$kk . "_label"] = $rowContent;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                        case "link":
                                                            $values[$k][$j][$kk] = $newValues[$k][$j][$kk];
                                                            $values[$k][$j][$kk . "_label"] = $newValues[$k][$j][$kk . "_label"];
                                                            break;
                                                        case 'date':
                                                            $values[$k][$j][$kk] = $this->fields[$k]->fields[$kk]->maskDateValue( $newValues[$k][$j][$kk], $this->fields[$k]->fields[$kk] );
                                                            break;
                                                        default:
                                                            //If there are no dropdowns previously setted and the evaluated field is not a dropdown
                                                            //only then rewritte the $values
                                                            $values[$k][$j][$kk] = $this->fields[$k]->fields[$kk]->maskValue( $newValues[$k][$j][$kk], $this->fields[$k]->fields[$kk] );
                                                            break;
                                                    }
                                                } else {
                                                    if (isset( $_FILES["form"]["name"][$k][$j][$kk] )) {
                                                        $values[$k][$j][$kk] = $_FILES["form"]["name"][$k][$j][$kk];
                                                    }

                                                    if (isset( $this->fields[$k]->fields[$kk]->input ) && ! empty( $this->fields[$k]->fields[$kk]->input )) {
                                                        //$_POST["INPUTS"][$k][$j][$kk] = $this->fields[$k]->fields[$kk]->input;
                                                        $_POST["INPUTS"][$k][$kk] = $this->fields[$k]->fields[$kk]->input;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $values[$k][$j] = $this->fields[$k]->maskValue( $newValues[$k][$j], $this );
                                    }
                                }
                                break;
                            case 'date':
                                $values[$k] = $this->fields[$k]->maskDateValue( $newValues[$k], $this->fields[$k] );
                                break;
                            default:
                                if ($this->fields[$k]->validateValue( $newValues[$k], $this )) {
                                    $values[$k] = $this->fields[$k]->maskValue( $newValues[$k], $this );
                                }
                        }
                    } else {
                        switch ($v->type) {
                            case "checkgroup":
                            case "listbox":
                                //This value is added when the user does not mark any checkbox
                                $values[$k] = "__NULL__";
                                break;
                        }
                    }
                } else {
                    if (isset( $_FILES["form"]["name"][$k] )) {
                        $values[$k] = $_FILES["form"]["name"][$k];
                    }

                    if (isset( $v->input ) && ! empty( $v->input )) {
                        $_POST["INPUTS"][$k] = $v->input;
                    }
                }
            }
        }

        foreach ($newValues as $k => $v) {
            if (strpos( $k, 'SYS_GRID_AGGREGATE_' ) !== false) {
                $values[$k] = $v;
            }
        }

        return $values;
    }

    /**
     * Function that return the valid fields to replace
     *
     * @author Julio Cesar Laura Avendao?=o <juliocesar@colosa.com>
     * @access public
     * @param boolean $bWhitSystemVars
     * @return array
     */
    public function getVars ($bWhitSystemVars = true)
    {
        $aFields = array ();
        if ($bWhitSystemVars) {
            $aAux = G::getSystemConstants();
            foreach ($aAux as $sName => $sValue) {
                $aFields[] = array ('sName' => $sName,'sType' => 'system'
                );
            }
        }
        foreach ($this->fields as $k => $v) {
            if (($v->type != 'title') && ($v->type != 'subtitle') && ($v->type != 'link') && ($v->type != 'file') && ($v->type != 'button') && ($v->type != 'reset') && ($v->type != 'submit') && ($v->type != 'listbox') && ($v->type != 'checkgroup') && ($v->type != 'grid') && ($v->type != 'javascript')) {
                $aFields[] = array ('sName' => trim( $k ),'sType' => trim( $v->type )
                );
            }
        }
        return $aFields;
    }

    /**
     * Function that verify the required fields without a correct value
     *
     * @author Erik Amaru Ortiz <erik@colosa.com>
     * @access public
     * @param array $dataFields
     * @param array $noRequired
     * @return array/false
     */
    public function validateRequiredFields ($dataFields, $noRequired = array())
    {
        if (! is_array( $noRequired )) {
            $noRequired = array ();
        }
        $requiredFields = array ();
        $notPassedFields = array ();
        $skippedFieldsTypes = array ('javascript','checkbox','yesno','submit','button','title','subtitle','button','submit','reset','hidden','link'
        );
        $requiredFieldsGrids = array ();
        $grids = array ();

        foreach ($this->fields as $field) {
            // verify fields in grids
            if ($field->type == 'grid') {
                array_push( $grids, $field->name );
                foreach ($field->fields as $fieldGrid) {
                    if (is_object( $fieldGrid ) && isset( $fieldGrid->required ) && $fieldGrid->required) {
                        if (! in_array( $fieldGrid->type, $skippedFieldsTypes )) {
                            if (!isset($requiredFieldsGrids[$field->name])) {
                                $requiredFieldsGrids[$field->name] = array();
                            }
                            if (! (is_array( $requiredFieldsGrids[$field->name] ))) {
                                $requiredFieldsGrids[$field->name] = array ();
                            }
                            array_push( $requiredFieldsGrids[$field->name], $fieldGrid->name );
                        }
                    }
                }
            }

            // verify fields the form
            if (is_object( $field ) && isset( $field->required ) && $field->required) {
                if (! in_array( $field->type, $skippedFieldsTypes )) {
                    array_push( $requiredFields, $field->name );
                }
            }
        }

        foreach ($dataFields as $dataFieldName => $dataField) {
            if (in_array( $dataFieldName, $grids )) {
                foreach ($dataField as $indexGrid => $dataGrid) {
                    foreach ($dataGrid as $fieldGridName => $fieldGridValue) {
                        if (!isset($requiredFieldsGrids[$dataFieldName])) {
                            $requiredFieldsGrids[$dataFieldName] = array();
                        }
                        if (! is_array( $requiredFieldsGrids[$dataFieldName] )) {
                            $requiredFieldsGrids[$dataFieldName] = array ();
                        }
                        if (in_array( $fieldGridName, $requiredFieldsGrids[$dataFieldName] ) && ! in_array( $fieldGridName, $noRequired ) && trim( $fieldGridValue ) == '') {
                            if (! (isset( $notPassedFields[$dataFieldName] ))) {
                                $notPassedFields[$dataFieldName] = array ();
                            }
                            if (! (is_array( $notPassedFields[$dataFieldName] ))) {
                                $notPassedFields[$dataFieldName] = array ();
                            }
                            $notPassedFields[$dataFieldName][$indexGrid][] = $fieldGridName;
                        }
                    }
                }
            }

            //verify if the requiered field is in $requiredFields array
            if (in_array( $dataFieldName, $requiredFields ) && ! in_array( $dataFieldName, $noRequired ) && trim( $dataField ) == '') {
                $notPassedFields[] = $dataFieldName;
            }
        }

        return count( $notPassedFields ) > 0 ? $notPassedFields : false;
    }

    public function validateFields ($data)
    {
        $excludeTypes = array ("submit","file" );

        foreach ($this->fields as $k => $v) {
            if (! in_array( $v->type, $excludeTypes )) {
                switch ($v->type) {
                    case "checkbox":
                        $data[$v->name] = (isset( $data[$v->name] )) ? $data[$v->name] : ((isset( $v->falseValue )) ? $v->falseValue : null);
                        break;
                    case "grid":
                        $i = 0;
                        foreach ($data[$v->name] as $dataGrid) {
                            $i = $i + 1;

                            foreach ($v->fields as $gridField) {
                                switch ($gridField->type) {
                                    case "file":
                                        $data[$v->name][$i][$gridField->name] = (isset( $_FILES["form"]["name"][$v->name][$i][$gridField->name] )) ? $_FILES["form"]["name"][$v->name][$i][$gridField->name] : ((isset( $gridField->falseValue )) ? $gridField->falseValue : null);
                                        break;
                                    case "checkbox":
                                        $data[$v->name][$i][$gridField->name] = (isset( $data[$v->name][$i][$gridField->name] )) ? $data[$v->name][$i][$gridField->name] : ((isset( $gridField->falseValue )) ? $gridField->falseValue : null);
                                        break;
                                }
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $data;
    }
}

