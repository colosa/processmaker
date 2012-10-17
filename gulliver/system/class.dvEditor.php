<?php

/**
 * class.dvEditor.php
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
//XmlForm_Field_DVEditor
/**
 * XmlForm_Field_HTML class definition
 * It is useful to see dynaforms how are built
 *
 * @package gulliver.system
 * @author
 *
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class XmlForm_Field_HTML extends XmlForm_Field
{
    public $toolbarSet = 'smallToolBar';
    public $width = '100%';
    public $height = '200';
    public $defaultValue = '<br/>';

    /**
     * render function is drawing the dynaform
     *
     * @author
     *
     *
     * @access public
     * @param string $value
     * @param string $owner
     * @return string
     *
     */
    public function render ($value, $owner = null)
    {
        $value = ($value == '') ? '<br/>' : $value;
        $html = "<div style='width:" . $this->width . ";'>";
        $html .= "<input id='form[" . $this->name . "]' name='form[" . $this->name . "]' type='hidden' value=' " . htmlentities( $value, ENT_QUOTES, 'UTF-8' ) . "' />";
        $html .= "</div>";
        return $html;
    }

    /**
     * attachEvents function is putting its events
     *
     * @author
     *
     *
     * @access public
     * @param string $element
     * @return string
     *
     */
    public function attachEvents ($element)
    {
        $html = 'window._editor' . $this->name . '=new DVEditor(getField("' . $this->name . '").parentNode,getField("' . $this->name . '").value,element,"' . $this->height . '","' . $this->mode . '");';
        if ($this->mode == "edit") {
            $html .= 'window._editor' . $this->name . '.loadToolBar("/js/dveditor/core/toolbars/' . $this->toolbarSet . '.html");';
        }
        $html .= 'window._editor' . $this->name . '.syncHidden("window._editor' . $this->name . '");';
        return $html;
    }
}

