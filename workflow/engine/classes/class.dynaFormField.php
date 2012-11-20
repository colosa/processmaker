<?php

/**
 * class.dynaFormField.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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
//G::LoadClass('xmlDb');
//G::LoadSystem('dynaformhandler');

/**
 * Dynaform Field - DynaformField class
 *
 * @package workflow.engine.classes
 */
class DynaFormField extends DBTable
{

    private $fileName;

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Function SetTo
     *
     * @param string $objConnection
     * @return void
     */
    public function SetTo($objConnection)
    {
        DBTable::SetTo($objConnection, 'dynaForm', array('XMLNODE_NAME'
        ));
    }

    /**
     * Load a dynaForm
     *
     * @param string $sUID
     * @return void
     */
    public function Load($sUID)
    {
        parent::Load($sUID);
        if (is_array($this->Fields)) {
            foreach ($this->Fields as $name => $value) {
                if (strcasecmp($name, 'dependentfields') == 0) {
                    $this->Fields[$name] = explode(',', $value);
                }
            }
        }
    }

    /**
     * Delete Fields of a dynaForm
     *
     * @param string $uid
     * @return void
     */
    public function Delete($uid)
    {
        $this->Fields['XMLNODE_NAME'] = $uid;
        parent::Delete();
    }

    /**
     * Save Fields of a dynaform
     *
     * @param array $Fields
     * @param array $labels
     * @param array $options
     * @return void
     */
    public function Save($Fields, $labels = array(), $options = array())
    {

        if ($Fields['TYPE'] === 'javascript') {
            $Fields['XMLNODE_VALUE'] = $Fields['CODE'];
            unset($Fields['CODE']);
            $labels = array();
        }
        if ($Fields['XMLNODE_NAME_OLD'] == '') {
            if (($Fields['XMLNODE_NAME'][0] == '1') || ($Fields['XMLNODE_NAME'][0] == '2') || ($Fields['XMLNODE_NAME'][0] == '3') || ($Fields['XMLNODE_NAME'][0] == '4') || ($Fields['XMLNODE_NAME'][0] == '5') || ($Fields['XMLNODE_NAME'][0] == '6') || ($Fields['XMLNODE_NAME'][0] == '7') || ($Fields['XMLNODE_NAME'][0] == '8') || ($Fields['XMLNODE_NAME'][0] == '9') || ($Fields['XMLNODE_NAME'][0] == '10')) {
                $Fields['XMLNODE_NAME'] = '_' . $Fields['XMLNODE_NAME'];
            }
            $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="' . $Fields['XMLNODE_NAME'] . '"');
        } else {
            if (($Fields['XMLNODE_NAME_OLD'][0] == '1') || ($Fields['XMLNODE_NAME_OLD'][0] == '2') || ($Fields['XMLNODE_NAME_OLD'][0] == '3') || ($Fields['XMLNODE_NAME_OLD'][0] == '4') || ($Fields['XMLNODE_NAME_OLD'][0] == '5') || ($Fields['XMLNODE_NAME_OLD'][0] == '6') || ($Fields['XMLNODE_NAME_OLD'][0] == '7') || ($Fields['XMLNODE_NAME_OLD'][0] == '8') || ($Fields['XMLNODE_NAME_OLD'][0] == '9') || ($Fields['XMLNODE_NAME_OLD'][0] == '10')) {
                $Fields['XMLNODE_NAME_OLD'] = '_' . $Fields['XMLNODE_NAME_OLD'];
            }
            $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="' . $Fields['XMLNODE_NAME_OLD'] . '"');
        }
        $this->is_new = ($res->count() == 0);
        $this->Fields = $Fields;
        unset($this->Fields['XMLNODE_NAME_OLD']);
        /*
         *  MPD-10 to create fields that do not appear many attributes, only the main ones?
         * The show those who are not white
         */
        if ($this->is_new) {
            foreach ($this->Fields as $key => $value) {
                if ($value == "") {
                    unset($this->Fields[$key]);
                }
            }
        } else {
            $this->Fields['XMLNODE_NAME'] = $Fields['XMLNODE_NAME_OLD'];
        }
        /* $res = $this->_dbses->Execute('INSERT INTO dynaForm'.
          ' (XMLNODE_TYPE,XMLNODE_VALUE)'.
          ' VALUES ("cdata", "'."\n".'")'); */
        parent::Save();
        if ($this->is_new) {
            /*
             * Create a new field.
             */
            foreach ($labels as $lang => $value) {
                /* $res = $this->_dbses->Execute('INSERT INTO dynaForm'.
                  ' (XMLNODE_TYPE,XMLNODE_VALUE)'.
                  ' VALUES ("cdata", "'."\n".'")'); */
                $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . ' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) ' . 'VALUES ("","' . "\n  " . '","cdata")');
                $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . ' (XMLNODE_NAME,XMLNODE_VALUE) ' . 'VALUES ("' . $lang . '","' . str_replace('"', '""', $value)/* ."\n  " */ . '")');
                if (isset($options[$lang])) {
                    foreach ($options[$lang] as $option => $text) {
                        $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . '.' . $lang . ' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) ' . 'VALUES ("","' . "  " . '","cdata")');
                        $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . '.' . $lang . ' (XMLNODE_NAME,XMLNODE_VALUE,name) ' . 'VALUES ("option","' . str_replace('"', '""', $text) . '","' . str_replace('"', '""', $option) . '")');
                        $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . '.' . $lang . ' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) ' . 'VALUES ("","' . "\n  " . '","cdata")');
                    }
                }
                $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . ' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) ' . 'VALUES ("","' . "\n" . '","cdata")');
            }
            $res = $this->_dbses->Execute('INSERT INTO dynaForm' . ' (XMLNODE_TYPE,XMLNODE_VALUE)' . ' VALUES ("cdata", "' . "\n" . '")');
        } else {
            /*
             * Update an existing field.
             */
            $this->_dbses->Execute('UPDATE dynaForm SET XMLNODE_NAME = "' . $Fields['XMLNODE_NAME'] . '" WHERE XMLNODE_NAME = "' . $Fields['XMLNODE_NAME_OLD'] . '"');
            foreach ($labels as $lang => $value) {
                $res = $this->_dbses->Execute('SELECT * FROM dynaForm.' . $Fields['XMLNODE_NAME'] . ' WHERE XMLNODE_NAME ="' . $lang . '"');
                if ($res->count() > 0) {
                    $res = $this->_dbses->Execute('UPDATE dynaForm.' . $Fields['XMLNODE_NAME'] . ' SET XMLNODE_VALUE = ' . '"' . str_replace('"', '""', $value) . '" WHERE XMLNODE_NAME ="' . $lang . '"');
                } else {
                    $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . ' (XMLNODE_NAME,XMLNODE_VALUE) ' . 'VALUES ("' . $lang . '","' . str_replace('"', '""', $value) . '")');
                }
                if (isset($options[$lang])) {
                    $res = $this->_dbses->Execute('DELETE FROM dynaForm.' . $Fields['XMLNODE_NAME'] . '.' . $lang . ' WHERE 1');
                    foreach ($options[$lang] as $option => $text) {
                        $res = $this->_dbses->Execute('INSERT INTO dynaForm.' . $Fields['XMLNODE_NAME'] . '.' . $lang . ' (XMLNODE_NAME,XMLNODE_VALUE,name) ' . 'VALUES ("option","' . str_replace('"', '""', $text) . '","' . str_replace('"', '""', $option) . '")');
                    }
                }
            }
        }
    }

    public function saveField($Fields, $attributes = array(), $options = array())
    {
        $dynaform = new dynaFormHandler($this->getFileName());
        if ($Fields['TYPE'] === 'javascript') {
            $Fields['XMLNODE_VALUE'] = $Fields['CODE'];
            unset($Fields['CODE']);
            $attributes = array();
        }
        if ($Fields['XMLNODE_NAME_OLD'] == '') {
            if (($Fields['XMLNODE_NAME'][0] == '1') || ($Fields['XMLNODE_NAME'][0] == '2') || ($Fields['XMLNODE_NAME'][0] == '3') || ($Fields['XMLNODE_NAME'][0] == '4') || ($Fields['XMLNODE_NAME'][0] == '5') || ($Fields['XMLNODE_NAME'][0] == '6') || ($Fields['XMLNODE_NAME'][0] == '7') || ($Fields['XMLNODE_NAME'][0] == '8') || ($Fields['XMLNODE_NAME'][0] == '9') || ($Fields['XMLNODE_NAME'][0] == '10')) {
                $Fields['XMLNODE_NAME'] = '_' . $Fields['XMLNODE_NAME'];
            }
            $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="' . $Fields['XMLNODE_NAME'] . '"');
        } else {
            if (($Fields['XMLNODE_NAME_OLD'][0] == '1') || ($Fields['XMLNODE_NAME_OLD'][0] == '2') || ($Fields['XMLNODE_NAME_OLD'][0] == '3') || ($Fields['XMLNODE_NAME_OLD'][0] == '4') || ($Fields['XMLNODE_NAME_OLD'][0] == '5') || ($Fields['XMLNODE_NAME_OLD'][0] == '6') || ($Fields['XMLNODE_NAME_OLD'][0] == '7') || ($Fields['XMLNODE_NAME_OLD'][0] == '8') || ($Fields['XMLNODE_NAME_OLD'][0] == '9') || ($Fields['XMLNODE_NAME_OLD'][0] == '10')) {
                $Fields['XMLNODE_NAME_OLD'] = '_' . $Fields['XMLNODE_NAME_OLD'];
            }
            $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="' . $Fields['XMLNODE_NAME_OLD'] . '"');
        }
        $this->is_new = ($res->count() == 0);
        $this->Fields = $Fields;
        unset($this->Fields['XMLNODE_NAME_OLD']);
        /*
         *  MPD-10 to create fields that do not appear many attributes, only the main ones?
         * The show those who are not white
         */
        if ($this->is_new) {
            foreach ($this->Fields as $key => $value) {
                if ($value == "") {
                    unset($this->Fields[$key]);
                }
            }
        } else {
            $this->Fields['XMLNODE_NAME'] = $Fields['XMLNODE_NAME_OLD'];
        }

        // parent::Save();
        if (trim($Fields['XMLNODE_VALUE']) != "") {
            $attributes['#cdata'] = $Fields['XMLNODE_VALUE'];
        }

        $aOptions = array();
        if (isset($Fields['OPTIONS']) && is_array($Fields['OPTIONS'])) {
            foreach ($Fields['OPTIONS'] as $key => $value) {
                $aOptions[] = Array('name' => 'option', 'value' => $value['LABEL'],
                    'attributes' => array('name' => $value['NAME']));
            }
        }

        if ($this->is_new) {
            // Create a new field
            $dynaform->add($Fields['XMLNODE_NAME'], $attributes, $options, $aOptions);
        } else {
            $dynaform->replace($Fields['XMLNODE_NAME_OLD'], $Fields['XMLNODE_NAME'], $attributes, $options, $aOptions);
        }
    }

    /**
     * Verify if is New the Field
     *
     * @return array
     */
    public function isNew()
    {
        $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="' . $this->Fields['XMLNODE_NAME'] . '"');
        return ($res->count() == 0);
    }
}
 