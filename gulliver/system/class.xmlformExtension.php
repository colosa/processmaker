<?php

/**
 *
 * @package gulliver.system
 */
class XmlFormFieldLabel extends XmlFormField
{
    public $withoutValue = true;
    public $align = 'left';
}

/**
 * Special class for pagedTable
 * condition: PHP expression whose result defines whether to "mark"
 * the following columns (that is if true)
 *
 * @package gulliver.system
 */
class XmlFormFieldCellMark extends XmlFormField
{
    /* Defines the style of the next tds
      of the pagedTable.
     */
    public $showInTable = "0";
    public $style = "";
    public $styleAlt = "";
    public $className = "";
    public $classNameAlt = "";
    public $condition = 'false';

    /**
     * tdStyle
     *
     * @param string $values
     * @param string $owner
     *
     * @return string $value
     */
    public function tdStyle($values, $owner)
    {
        $value = G::replaceDataField($this->condition, $owner->values);
        $value = @eval('return (' . $value . ');');
        $row = $values['row__'];
        $style = ((($row % 2) == 0) && ($this->styleAlt != 0)) ? $this->styleAlt : $this->style;
        return ($value) ? $style : '';
    }

    /**
     * tdClass
     *
     * @param string $values
     * @param string $owner
     *
     * @return $value
     */
    public function tdClass($values, $owner)
    {
        $value = G::replaceDataField($this->condition, $owner->values);
        $value = @eval('return (' . $value . ');');
        $row = $values['row__'];
        $style = (($row % 2) == 0) ? $this->classNameAlt : $this->className;
        return ($value) ? $style : '';
    }
}

/**
 * XmlFormFieldDVEditor
 *
 * extends XmlFormField
 *
 * @package gulliver.system
 *
 */
class XmlFormFieldDVEditor extends XmlFormField
{
    public $toolbarSet = 'toolbar2lines.html';
    public $width = '90%';
    public $height = '200';

    /**
     * render
     *
     * @param string $value
     * @param string $owner default value NULL
     *
     * @return string '<div> ... </div>'
     */
    public function render($value = null, $owner = null)
    {
        return '<div style="width:' . htmlentities($this->width, ENT_QUOTES, 'utf-8') . ';height:' . htmlentities($this->height, ENT_QUOTES, 'utf-8') . '"><input id="form[' . $this->name . ']" name="form[' . $this->name . ']" type="hidden" value="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"/></div>';
    }

    /**
     * attachEvents
     *
     * @param string $element
     *
     * @return $html
     */
    public function attachEvents($element)
    {
        $html = 'var _editor' . $this->name . '=new DVEditor(getField("form[' . $this->name . ']").parentNode,getField("form[' . $this->name . ']").value)';
        return $html;
    }
}

/**
 * Special field: Add a search box (fast search) for the related pagedTable
 *
 * The PAGED_TABLE_ID reserved field must be defined in the xml.
 * Use PAGED_TABLE_FAST_SEARCH reserved field, it contains the saved value for each table.
 * example:
 * Ex1.
 * <PAGED_TABLE_ID type="private"/>
 * <PAGED_TABLE_FAST_SEARCH type="FastSearch">
 * <en>Search</en>
 * </PAGED_TABLE_FAST_SEARCH>
 * Ex2 (Using type="text").
 * <PAGED_TABLE_ID type="private"/>
 * <PAGED_TABLE_FAST_SEARCH type="text" colAlign="right" colWidth="180" onkeypress="if (event.keyCode===13)@#PAGED_TABLE_ID.doFastSearch(this.value);if (event.keyCode===13)return false;">
 * <en>Search</en>
 * </PAGED_TABLE_FAST_SEARCH>
 *
 * @package gulliver.system
 */
class XmlFormFieldFastSearch extends XmlFormFieldText
{
    public $onkeypress = "if (event.keyCode===13)@#PAGED_TABLE_ID.doFastSearch(this.value);if (event.keyCode===13)return false;";
    public $colAlign = "right";
    public $colWidth = "180";
    public $label = "@G::LoadTranslation(ID_SEARCH)";
}

