<?php

/**
 * class.xmlfield_Image.php
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
 */

// DEPRECATED this class is also part of the xmlform package this class will be also removed
// in future releases of pm, its discouraged the inclusion this class in the future from a
// external file like this
/**
 *
 * @package workflow.engine.classes
 */

class XmlForm_Field_Image extends XmlForm_Field
{
    public $file = '';
    public $home = 'public_html';
    public $withoutLabel = false;

    public function render ($value, $owner = null)
    {
        $url = G::replaceDataField( $this->file, $owner->values );
        if ($this->home === "methods") {
            $url = G::encryptlink( SYS_URI . $url );
        }
        if ($this->home === "public_html") {
            $url = '/' . $url;
        }
        return '<img src="' . htmlentities( $url, ENT_QUOTES, 'utf-8' ) . '" ' . (($this->style) ? 'style="' . $this->style . '"' : '') . ' alt ="' . htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '"/>';
    }
}

