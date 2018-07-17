<?php

/**
 * Class XmlFormFieldCheckBoxTable
 *
 */
class XmlFormFieldCheckBoxTable extends XmlFormFieldCheckbox
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
    public function render($value = null, $owner = null)
    {
        $optionName = $value;
        $onclick = (($this->onclick) ? ' onclick="' . G::replaceDataField($this->onclick, $owner->values) . '" ' : '');
        $html = '<input class="FormCheck" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][' . $optionName . ']" type=\'checkbox\' value="' . $value . '"' . $onclick . '> <span class="FormCheck"></span></input>';
        return $html;
    }
}
