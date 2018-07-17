<?php

/**
 * Class XmlFormFieldHours
 *
 */
class XmlFormFieldHours extends XmlFormFieldSimpleText
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
    public $sqlOption = array();
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
    public function render($value = null, $owner = null)
    {
        if ($this->strTo === 'UPPER') {
            $value = strtoupper($value);
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower($value);
        }
            //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
        $onkeypress = G::replaceDataField($this->onkeypress, $owner->values);
        if ($this->replaceTags == 1) {
            $value = G::replaceDataField($value, $owner->values);
        }
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField($this->process, $owner->values);
            //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '\' readOnly="readOnly" style="' . htmlentities($this->style, ENT_COMPAT, 'utf-8') . '" onkeypress="' . htmlentities($onkeypress, ENT_COMPAT, 'utf-8') . '"/>' . $sShowVars;
            } else {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '\' style="' . htmlentities($this->style, ENT_COMPAT, 'utf-8') . '" onkeypress="' . htmlentities($onkeypress, ENT_COMPAT, 'utf-8') . '"/>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '\' style="display:none;' . htmlentities($this->style, ENT_COMPAT, 'utf-8') . '" onkeypress="' . htmlentities($onkeypress, ENT_COMPAT, 'utf-8') . '"/>' . $this->htmlentities($value, ENT_COMPAT, 'utf-8');
        } else {
            return $this->htmlentities($value, ENT_COMPAT, 'utf-8');
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
    public function renderGrid($values = array(), $owner)
    {
        $result = array();
        $r = 1;
        foreach ($values as $v) {
            if ($this->replaceTags == 1) {
                $v = G::replaceDataField($v, $owner->values);
            }
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField($this->process, $owner->values);
                //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities($v, ENT_COMPAT, 'utf-8') . '" readOnly="readOnly" style="' . htmlentities($this->style, ENT_COMPAT, 'utf-8') . '"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities($v, ENT_COMPAT, 'utf-8') . '" style="' . htmlentities($this->style, ENT_COMPAT, 'utf-8') . '"/>' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                $result[] = '<p align="' . $this->align . '">' . $this->htmlentities(number_format($v, 2), ENT_COMPAT, 'utf-8') . '</p>';
            } else {
                $result[] = '<p align="' . $this->align . '">' . $this->htmlentities(number_format($v, 2), ENT_COMPAT, 'utf-8') . '</p>';
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
    public function attachEvents($element)
    {
        return "myForm.aElements[i] = new G_Text(myForm, $element,'{$this->name}');
    myForm.aElements[i].setAttributes(" . $this->getAttributes() . ");";
    }
}
