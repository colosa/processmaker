<?php

/**
 * Class XmlFormFieldTextareaPm
 */
class XmlFormFieldTextareaPm extends XmlFormField
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
    public function render($value = null, $owner)
    {
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField($this->process, $owner->values);
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities($this->wrap, ENT_QUOTES, 'UTF-8') . '" class="FormTextPM" readOnly>' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '</textarea>' . $sShowVars;
            } else {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities($this->wrap, ENT_QUOTES, 'UTF-8') . '" class="FormTextPM" >' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '</textarea>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" readOnly style="border:0px;backgroud-color:inherit;' . $this->style . '" wrap="' . htmlentities($this->wrap, ENT_QUOTES, 'UTF-8') . '"  class="FormTextPM" >' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '</textarea>';
        } else {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities($this->wrap, ENT_QUOTES, 'UTF-8') . '"  class="FormTextArea" >' . $this->htmlentities($value, ENT_COMPAT, 'utf-8') . '</textarea>';
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
    public function renderGrid($owner, $values = null)
    {
        $result = array();
        $r = 1;
        foreach ($values as $v) {
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField($this->process, $owner->values);
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities($v, ENT_COMPAT, 'utf-8') . '\' readOnly="readOnly"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities($v, ENT_COMPAT, 'utf-8') . '\' />' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                if (stristr($_SERVER['HTTP_USER_AGENT'], 'iPhone')) {
                    $result[] = $this->htmlentities($v, ENT_COMPAT, 'utf-8');
                } else {
                    $result[] = $this->htmlentities($v, ENT_COMPAT, 'utf-8');
                }
            } else {
                $result[] = $this->htmlentities($v, ENT_COMPAT, 'utf-8');
            }
            $r ++;
        }
        return $result;
    }
}
