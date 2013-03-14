<?php

/**
 * class.xmlformExtension.php
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
 *
 * @package gulliver.system
 */
class XmlForm_Field_Label extends XmlForm_Field
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
class XmlForm_Field_cellMark extends XmlForm_Field
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
 * XmlForm_Field_DVEditor
 *
 * extends XmlForm_Field
 *
 * @package gulliver.system
 *
 */
class XmlForm_Field_DVEditor extends XmlForm_Field
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
    public function render($value, $owner = null)
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
class XmlForm_Field_FastSearch extends XmlForm_Field_Text
{
    public $onkeypress = "if (event.keyCode===13)@#PAGED_TABLE_ID.doFastSearch(this.value);if (event.keyCode===13)return false;";
    public $colAlign = "right";
    public $colWidth = "180";
    public $label = "@G::LoadTranslation(ID_SEARCH)";
}

