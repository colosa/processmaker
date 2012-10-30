<?php

/**
 * class.popupMenu.php
 *
 * @package workflow.engine.ProcessMaker
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
 * popupMenu - popupMenu class
 *
 * @package workflow.engine.ProcessMaker
 * @copyright COLOSA
 */

class popupMenu extends form
{
    var $type = 'popupMenu';
    var $theme = 'processmaker';

    /**
     * Print the popup
     *
     * @param string $tableId
     * @param array $tableFields
     * @return array
     */
    function renderPopup ($tableId, $tableFields)
    {
        $this->name = $tableId;
        $fields = array_keys( $tableFields );
        foreach ($fields as $f) {
            switch (strtolower( $tableFields[$f]['Type'] )) {
                case 'javascript':
                case 'button':
                case 'private':
                case 'hidden':
                case 'cellmark':
                    break;
                default:
                    $label = ($tableFields[$f]['Label'] != '') ? $tableFields[$f]['Label'] : $f;
                    $label = str_replace( "\n", ' ', $label );
                    $pmXmlNode = new Xml_Node( $f, 'complete', '', array ('label' => $label,'type' => 'popupOption','launch' => $tableId . '.showHideField("' . $f . '")'
                    ) );
                    $this->fields[$f] = new XmlForm_Field_popupOption( $pmXmlNode );
                    $this->values[$f] = '';
            }
        }
        $scTemp = '';
        $this->values['PAGED_TABLE_ID'] = $tableId;
        print (parent::render( PATH_CORE . 'templates/popupMenu.html', $scTemp )) ;
        $sc = "<script type=\"text/javascript\">\n$scTemp\n loadPopupMenu_$tableId(); \n</script>";
        return $sc;
    }
}

/**
 * XmlForm_Field_popupOption - XmlForm_Field_popupOption class
 *
 * @package workflow.engine.ProcessMaker
 * @copyright COLOSA
 */

class XmlForm_Field_popupOption extends XmlForm_Field
{
    var $launch = '';

    /**
     * Get Events
     *
     * @return string
     */
    function getEvents ()
    {
        $script = '{name:"' . $this->name . '",text:"' . addcslashes( $this->label, '\\"' ) . '", launch:leimnud.closure({Function:function(target){' . $this->launch . '}, args:target})}';
        return $script;
    }
}

