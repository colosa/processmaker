<?php

/**
 * class.xmlDocument.php
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
 * Class Xml_Node
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class Xml_Node
{
    public $name = '';
    public $type = '';
    public $value = ''; //maybe not necesary
    public $attributes = array ();
    public $children = array ();

    /**
     * Function Xml_Node
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string name
     * @param eter string type
     * @param eter string value
     * @param eter string attributes
     * @return string
     */
    public function Xml_Node ($name, $type, $value, $attributes = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->attributes = $attributes;
    }

    /**
     * Function addAttribute
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string name
     * @param eter string value
     * @return string
     */
    public function addAttribute ($name, $value)
    {
        $this->attributes[$name] = $value;
        return true;
    }

    /**
     * Function addChildNode
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string childNode
     * @return string
     */
    public function addChildNode ($childNode)
    {
        if (is_object( $childNode ) && strcasecmp( get_class( $childNode ), 'Xml_Node' ) == 0) {
            $this->type = 'open';
            $childNode->parent = &$this;
            $this->children[] = &$childNode;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function toTree
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function toTree ()
    {
        $arr = new Xml_Node( $this->name, $this->type, $this->value, $this->attributes );
        unset( $arr->parent );
        foreach ($this->children as $k => $v) {
            unset( $v->parent );
            $arr->children[$k] = $v->toTree();
        }
        return $arr;
    }

    public function toArray ($obj = null)
    {
        $arr = array ();
        if (! isset( $obj )) {
            $obj = $this->toTree();
        }
        foreach ($obj as $att => $val) {
            if (is_array( $val ) || is_object( $val )) {
                $arr[$att] = Xml_Node::toArray( $val );
            } else {
                $arr[$att] = $val;
            }
        }
        return $arr;
    }

    /**
     * Function &findNode
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string xpath
     * @return string
     */
    public function &findNode ($xpath)
    {
        $n = null;
        $p = explode( '/', $xpath );
        if ($p[0] === '') {
            return $this;
        } elseif (substr( $p[0], 0, 1 ) === '@') {
            $p[0] = substr( $p[0], 1 );
            if (isset( $this->attributes[$p[0]] )) {
                return $this->attributes[$p[0]];
            }
        } elseif ($p[0] === '..') {
            array_shift( $p );
            $n = & $this->parent->findNode( implode( '/', $p ) );
            if (isset( $n )) {
                return $n;
            }
        } else {
            foreach ($this->children as $k => $v) {
                if (($v->type !== 'cdata') && ($v->name === $p[0])) {
                    if (sizeof( $p ) > 1) {
                        array_shift( $p );
                        $n = & $this->children[$k]->findNode( implode( '/', $p ) );
                        if (isset( $n )) {
                            return $n;
                        }
                    } else {
                        return $this->children[$k];
                    }
                }
            }
        }
        return $n;
    }

    /**
     * Function getXML
     * Returns a string of the node in XML notation.
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string xpath
     * @return string
     */
    public function getXML ()
    {
        switch ($this->type) {
            case 'open':
                $xml = '<' . $this->name;
                foreach ($this->attributes as $attib => $value) {
                    $value = htmlspecialchars( $value, ENT_QUOTES, 'utf-8' );
                    /*if(($attib == "hint")||($attib == "defaultvalue")){
                          $value = str_replace("&", "&amp;", $value);
                          $value = str_replace("'", "\'", $value);
                          $value = str_replace(">", "&gt;", $value);
                          $value = str_replace("<", "&lt;", $value);
                      }
                    //else{
                    $value = htmlentities( $value, ENT_QUOTES, 'utf-8' );
                    //}
                    */
                    $xml .= ' ' . $attib . '="' . $value . '"';
                    //check if the htmlentities result value is the euro symbol and
                    //replaced by their numeric character representation
                    if (strpos( $xml, '&euro;' ) !== false) {
                        $xml = str_replace( '&euro;', '&#8364;', $xml );
                    }
                }
                $xml .= '>' . $this->getCDATAValue();
                foreach ($this->children as $child) {
                    $xml .= $child->getXML();
                }
                $xml .= '</' . $this->name . '>';
                break;
            case 'close':
                $xml = '</' . $this->name . '>';
                break;
            case 'cdata':
                $xml = $this->getCDATAValue();
                break;
            case 'complete':
                $xml = '<' . $this->name;
                foreach ($this->attributes as $attib => $value) {
                    $xml .= ' ' . $attib . '="' . htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '"';
                    //check if the htmlentities result value is the euro symbol and
                    //replaced by their numeric character representation
                    if (strpos( $xml, '&euro;' ) !== false) {
                        $xml = str_replace( '&euro;', '&#8364;', $xml );
                    }
                }
                if ($this->value !== '') {
                    $xml .= '>' . $this->getCDATAValue();
                    $xml .= '</' . $this->name . '>';
                } else {
                    $xml .= '/>';
                }
                break;
        }
        return $xml;
    }

    public function getCDATAValue ()
    {
        $cdata = htmlentities( $this->value, ENT_QUOTES, 'utf-8' );
        if ($this->value === $cdata) {
            return $this->value;
        } else {
            return '<![CDATA[' . $this->value . ']]>';
        }
    }
}

/**
 * Class Xml_document
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class Xml_document extends Xml_Node
{
    public $currentNode;

    /**
     * Function Xml_document
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function Xml_document ()
    {
        $this->currentNode = &$this;
    }

    /**
     * Function parseXmlFile
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string filename
     * @param eter string content
     * @return string
     */
    public function parseXmlFile ($filename, $content = "")
    { //$content is a new variable, if it has any value then use it instead of the file content.
        if ($content == "") {
            if (! file_exists( $filename )) {
                throw (new Exception( "failed to open Xmlform File : No such file or directory in $filename " ));
            }
            $data = implode( '', file( $filename ) );
        } else {
            $data = $content;
        }

        $parser = xml_parser_create( 'utf-8' );
        xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
        xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 0 );
        xml_parse_into_struct( $parser, $data, $values, $tags );
        if (xml_get_error_code( $parser ) !== 0) {
            $msg = sprintf( "XML error in <b>%s</b>: %s at line %d", $filename, xml_error_string( xml_get_error_code( $parser ) ), xml_get_current_line_number( $parser ) );
            trigger_error( $msg );
        }
        xml_parser_free( $parser );

        $this->name = '#document';
        $this->type = 'open';
        $this->currentNode = &$this;
        $m = &$this;
        foreach ($values as $k => $v) {
            switch ($v['type']) {
                case 'open':
                    $this->currentNode->addChildNode( new Xml_Node( $v['tag'], $v['type'], isset( $v['value'] ) ? $v['value'] : '', isset( $v['attributes'] ) ? $v['attributes'] : array () ) );
                    $this->currentNode = &$this->findNode( $v['tag'] );
                    break;
                case 'close':
                    $this->currentNode = & $this->findNode( '..' );
                    break;
                case 'cdata':
                    $this->currentNode->addChildNode( new Xml_Node( '', $v['type'], isset( $v['value'] ) ? $v['value'] : '' ) );
                    break;
                case 'complete':
                    $this->currentNode->addChildNode( new Xml_Node( $v['tag'], $v['type'], isset( $v['value'] ) ? $v['value'] : '', isset( $v['attributes'] ) ? $v['attributes'] : array () ) );
                    break;
            }
        }
        return true;
    }

    /**
     * Function &findNode
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string xpath
     * @return string
     */
    public function &findNode ($xpath)
    {
        if (substr( $xpath, 0, 1 ) == '/') {
            return parent::findNode( substr( $xpath, 1 ) );
        } else {
            if (isset( $this->currentNode )) {
                if ($this->currentNode->name === $this->name) {
                    return parent::findNode( $xpath );
                } else {
                    return $this->currentNode->findNode( $xpath );
                }
            } else {
                return $null;
            }
        }
    } //function findNode


    /**
     * Function getXML
     *
     * @access public
     * @return string $xml
     */
    public function getXML ()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= $this->children[0]->getXML();
        return $xml;
    }

    /**
     * Function save
     *
     * @access public
     * @return void
     */
    public function save ($filename)
    {
        $xml = $this->getXML();
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, $xml );
        fclose( $fp );
    }
}

