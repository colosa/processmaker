<?php

/**
 * class.xmlfield_InputPM.php
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
 *
 * @package workflow.engine.classes
 **/

class XmlForm_Field_TextPM extends XmlForm_Field_SimpleText
{
    public $size = 15;
    public $maxLength = 64;
    public $validate = 'Any';
    public $mask = '';
    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $linkField = '';
    //Possible values:(-|UPPER|LOWER|CAPITALIZE)
    public $strTo = '';
    public $readOnly = false;
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    //Atributes only for grids
    public $formula = '';
    public $function = '';
    public $replaceTags = 0;
    public $showVars = 0;
    public $process = '';
    public $symbol = '@@';

    /**
     * Function render
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        //$this->executeSQL();
        //if (isset($this->sqlOption)) {
        //  reset($this->sqlOption);
        //  $firstElement=key($this->sqlOption);
        //  if (isset($firstElement)) $value = $firstElement;
        //}
        //NOTE: string functions must be in G class
        if ($this->strTo === 'UPPER') {
            $value = strtoupper( $value );
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower( $value );
        }
        //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
        if ($this->replaceTags == 1) {
            $value = G::replaceDataField( $value, $owner->values );
        }
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField( $this->process, $owner->values );
            //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
            } else {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' style="display:none;' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Function renderGrid
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter array values
     * @param eter string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            if ($this->replaceTags == 1) {
                $v = G::replaceDataField( $v, $owner->values );
            }
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField( $this->process, $owner->values );
                //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
            } else {
                $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
            }
            $r ++;
        }
        return $result;
    }

    /**
     * Function attachEvents
     *
     * @access public
     * @param eter string $element
     * @return string
     */
    public function attachEvents ($element)
    {
        return "myForm.aElements[i] = new G_Text(myForm, $element,'{$this->name}');
    myForm.aElements[i].setAttributes(" . $this->getAttributes() . ");";
    }
}

/**
 * Class XmlForm_Field_TextareaPM
 */
class XmlForm_Field_TextareaPM extends XmlForm_Field
{
    public $rows = 12;
    public $cols = 40;
    public $required = false;
    public $readOnly = false;
    public $wrap = 'OFF';
    public $showVars = 0;
    public $process = '';
    public $symbol = '@@';

    /**
     * Function render
     *
     * @author Julio Cesar Laura Avendao <juliocesar@colosa.com>
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner)
    {
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField( $this->process, $owner->values );
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class="FormTextPM" readOnly>' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>' . $sShowVars;
            } else {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class="FormTextPM" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" readOnly style="border:0px;backgroud-color:inherit;' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="FormTextPM" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
        } else {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="FormTextArea" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
        }
    }

    /**
     * Function renderGrid
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter string values
     * @param eter string owner
     * @return string
     */
    public function renderGrid ($owner, $values = null)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField( $this->process, $owner->values );
                //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '\' />' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                if (stristr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' )) {
                    //$result[] = '<div style="overflow:hidden;height:25px;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
                    $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                } else {
                    //$result[] = '<div style="overflow:hidden;width:inherit;height:2em;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
                    $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                }
            } else {
                $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
            }
            $r ++;
        }
        return $result;
    }
}

/**
 * Class XmlForm_Field_hours
 */
class XmlForm_Field_hours extends XmlForm_Field_SimpleText
{
    public $size = 15;
    public $maxLength = 64;
    public $validate = 'Any';
    public $mask = '';
    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $linkField = '';
    //Possible values:(-|UPPER|LOWER|CAPITALIZE)
    public $strTo = '';
    public $readOnly = false;
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    //Atributes only for grids
    public $formula = '';
    public $function = '';
    public $replaceTags = 0;
    public $showVars = 0;
    public $process = '';
    public $symbol = '@@';

    /**
     * Function render
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if ($this->strTo === 'UPPER') {
            $value = strtoupper( $value );
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower( $value );
        }
            //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
        if ($this->replaceTags == 1) {
            $value = G::replaceDataField( $value, $owner->values );
        }
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField( $this->process, $owner->values );
            //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
            } else {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' style="display:none;' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Function renderGrid
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter array values
     * @param eter string owner
     * @return string
     */
    public function renderGrid ($values = array(),$owner)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            if ($this->replaceTags == 1) {
                $v = G::replaceDataField( $v, $owner->values );
            }
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField( $this->process, $owner->values );
                //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                $result[] = '<p align="' . $this->align . '">' . $this->htmlentities( number_format( $v, 2 ), ENT_COMPAT, 'utf-8' ) . '</p>';
            } else {
                $result[] = '<p align="' . $this->align . '">' . $this->htmlentities( number_format( $v, 2 ), ENT_COMPAT, 'utf-8' ) . '</p>';
            }
            $r ++;
        }
        return $result;
    }

    /**
     * Function attachEvents
     *
     * @access public
     * @param eter string $element
     * @return string
     */
    public function attachEvents ($element)
    {
        return "myForm.aElements[i] = new G_Text(myForm, $element,'{$this->name}');
    myForm.aElements[i].setAttributes(" . $this->getAttributes() . ");";
    }
}

/**
 * Function getDynaformsVars
 *
 * @access public
 * @param eter string $sProcessUID
 * @param eter boolean $bSystemVars
 * @return array
 */
function getDynaformsVars ($sProcessUID, $bSystemVars = true, $bIncMulSelFields = 0)
{
    $aFields = array ();
    $aFieldsNames = array ();
    if ($bSystemVars) {
        $aAux = G::getSystemConstants();
        foreach ($aAux as $sName => $sValue) {
            $aFields[] = array ('sName' => $sName,'sType' => 'system','sLabel' => 'System variable');
        }
        //we're adding the ping variable to the system list
        $aFields[] = array ('sName' => 'PIN','sType' => 'system','sLabel' => 'System variable'
        );
    }

    $aInvalidTypes = array("title", "subtitle", "file", "button", "reset", "submit", "javascript");
    $aMultipleSelectionFields = array("listbox", "checkgroup", "grid");

    if ($bIncMulSelFields != 0) {
        $aInvalidTypes = array_merge( $aInvalidTypes, $aMultipleSelectionFields );
    }
    require_once 'classes/model/Dynaform.php';
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
    $oCriteria->add( DynaformPeer::PRO_UID, $sProcessUID );
    $oCriteria->add( DynaformPeer::DYN_TYPE, 'xmlform' );
    $oDataset = DynaformPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
        if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
            $G_FORM = new Form( $aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG );
            if (($G_FORM->type == 'xmlform') || ($G_FORM->type == '')) {
                foreach ($G_FORM->fields as $k => $v) {
                    if (! in_array( $v->type, $aInvalidTypes )) {
                        if (! in_array( $k, $aFieldsNames )) {
                            $aFields[] = array ('sName' => $k,'sType' => $v->type,'sLabel' => ($v->type != 'grid' ? $v->label : '[ ' . G::LoadTranslation( 'ID_GRID' ) . ' ]')
                            );
                            $aFieldsNames[] = $k;
                        }
                    }
                }
            }
        }
        $oDataset->next();
    }
    return $aFields;
}

/**
 * Function getGridsVars
 *
 * @access public
 * @param eter string $sProcessUID
 * @return array
 */
function getGridsVars ($sProcessUID)
{
    $aFields = array ();
    $aFieldsNames = array ();

    require_once 'classes/model/Dynaform.php';
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
    $oCriteria->add( DynaformPeer::PRO_UID, $sProcessUID );
    $oDataset = DynaformPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
        $G_FORM = new Form( $aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG );
        if ($G_FORM->type == 'xmlform') {
            foreach ($G_FORM->fields as $k => $v) {
                if ($v->type == 'grid') {
                    if (! in_array( $k, $aFieldsNames )) {
                        $aFields[] = array ('sName' => $k,'sXmlForm' => str_replace( $sProcessUID . '/', '', $v->xmlGrid ));
                        $aFieldsNames[] = $k;
                    }
                }
            }
        }
        $oDataset->next();
    }
    return $aFields;
}
/**
 * Function getVarsGrid returns all variables of Grid
 *
 * @access public
 * @param string proUid process ID
 * @param string dynUid dynaform ID
 * @return array
 */

function getVarsGrid ($proUid, $dynUid)
{
    G::LoadClass( 'dynaformhandler' );
    G::LoadClass( 'AppSolr' );

    $dynaformFields = array ();

    if (is_file( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/'. $proUid .'/'.$dynUid. '.xml' ) && filesize( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/'. $proUid .'/'. $dynUid .'.xml' ) > 0) {
        $dyn = new dynaFormHandler( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/' .$proUid. '/' . $dynUid .'.xml' );
        $dynaformFields[] = $dyn->getFields();
    }

    $dynaformFieldTypes = array ();

    foreach ($dynaformFields as $aDynFormFields) {
        foreach ($aDynFormFields as $field) {

            if ($field->getAttribute( 'validate' ) == 'Int') {
                $dynaformFieldTypes[$field->nodeName] = 'Int';
            } elseif ($field->getAttribute( 'validate' ) == 'Real') {
                $dynaformFieldTypes[$field->nodeName] = 'Real';
            } else {
                $dynaformFieldTypes[$field->nodeName] = $field->getAttribute( 'type' );
            }
        }
    }
    return $dynaformFieldTypes;
}


/**
 * Class XmlForm_Field_CheckBoxTable
 */
class XmlForm_Field_CheckBoxTable extends XmlForm_Field_Checkbox
{

    /**
     * Function render
     *
     * @author The Answer
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        //$optionName = $owner->values['USR_UID'];
        $optionName = $value;
        $onclick = (($this->onclick) ? ' onclick="' . G::replaceDataField( $this->onclick, $owner->values ) . '" ' : '');
        $html = '<input class="FormCheck" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][' . $optionName . ']" type=\'checkbox\' value="' . $value . '"' . $onclick . '> <span class="FormCheck"></span></input>';
        return $html;
    }
}

