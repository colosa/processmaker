<?php

/**
 * class.dynaformhandler.php
 * @package gulliver.system

 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @date Aug 26th, 2009
 * @description This class is a Dynaform handler for modify directly into file
 * @package gulliver.system
 */
class dynaFormHandler
{

    private $xmlfile;
    private $dom;
    private $root;

    /**
     * Function constructor
     * @access public
     * @param  string $file
     * @return void
     */
    public function __construct($file = null)
    {
        if (!isset($file)) {
            throw new Exception('[Class dynaFormHandler] ERROR:  xml file was not set!!');
        }
        $this->xmlfile = $file;
        $this->load();
    }

    public function load()
    {
        $this->dom = new DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        if (is_file($this->xmlfile)) {
            if (@$this->dom->load($this->xmlfile) === true) {
                $this->root = $this->dom->firstChild;
            } else {
                throw new Exception('Error: ' . $this->xmlfile . ' is a invalid xml file!');
            }
        } else {
            throw new Exception('[Class dynaFormHandler] ERROR:  the (' . $this->xmlfile . ') file doesn\'t exits!!');
        }
    }

    public function reload()
    {
        $this->dom = null;
        $this->load();
    }

    /**
     * Function __cloneEmpty
     * @access public
     * @return void
     */
    public function __cloneEmpty()
    {
        $xPath = new DOMXPath($this->dom);
        $nodeList = $xPath->query('/dynaForm/*');
        foreach ($nodeList as $domElement) {
            $elements[] = $domElement->nodeName;
        }
        $this->remove($elements);
        //return $cloneObj;
    }

    /**
     * Function toString
     * @access public
     * @param  string $op
     * @return void
     */
    public function toString($op = '')
    {
        switch ($op) {
            case 'html': return htmlentities(file_get_contents($this->xmlfile));
                break;
            default: return file_get_contents($this->xmlfile);
                break;
        }
    }

    /**
     * Function getNode
     * @access public
     * @param  string $nodename
     * @return void
     */
    public function getNode($nodename)
    {
        return $this->root->getElementsByTagName($nodename)->item(0);
    }

    /**
     * Function getNode
     * @access public
     * @param  object $node
     * @return object
     */
    public function setNode($node)
    {
        $newnode = $this->root->appendChild($node);
        $this->save();
        return $newnode;
    }

    /**
     * Add Function
     * @param string $name
     * @param array $attributes
     * @param array $childs
     * @param array $childs_childs
     * @return void
     */
    //attributes (String node-name, Array attributes(atribute-name =>attribute-value, ..., ...), Array childs(child-name=>child-content), Array Child-childs())
    public function add($name, $attributes, $childs, $childs_childs = null)
    {

        $newnode = $this->root->appendChild($this->dom->createElement($name));
        if (isset($attributes['#cdata'])) {
            $newnode->appendChild($this->dom->createTextNode("\n"));
            $newnode->appendChild($this->dom->createCDATASection($attributes['#cdata']));
            $newnode->appendChild($this->dom->createTextNode("\n"));
            unset($attributes['#cdata']);
        }
        foreach ($attributes as $att_name => $att_value) {
            $newnode->setAttribute($att_name, $att_value);
        }
        if (is_array($childs)) {
            foreach ($childs as $child_name => $child_text) {
                $newnode_child = $newnode->appendChild($this->dom->createElement($child_name));
                if (strip_tags($child_text) !== $child_text) {
                    $newnode_child->appendChild($this->dom->createCDATASection($child_text));
                } else {
                   $newnode_child->appendChild($this->dom->createTextNode($child_text));
                }
                if ($childs_childs != null and is_array($childs_childs)) {
                    foreach ($childs_childs as $cc) {
                        $newnode_child->appendChild($this->dom->createTextNode("\n" . str_repeat(" ", 6)));
                        $ccmode = $newnode_child->appendChild($this->dom->createElement($cc['name']));
                        $ccmode->appendChild($this->dom->createTextNode($cc['value']));
                        foreach ($cc['attributes'] as $cc_att_name => $cc_att_value) {
                            $ccmode->setAttribute($cc_att_name, $cc_att_value);
                        }
                    }
                }
            }
        } else {
            $text_node = $childs;
            $newnode->appendChild($this->dom->createCDATASection($text_node));
        }
        $this->save();
    }

    private function hasChild($p)
    {
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $c) {
                if ($c->nodeType == XML_ELEMENT_NODE) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getChildNode($x)
    {
        $chidNode = array();
        foreach ($x->childNodes as $p) {
            if ($this->hasChild($p)) {
                getChildNode($p);
            } else {
                if ($p->nodeType == XML_ELEMENT_NODE) {

                    $chidNode[] = array('node' => $x->nodeName, 'nodeName' => $p->nodeName,
                        'name' => $p->getAttribute('name'), 'nodeValue' => $p->nodeValue);
                }
            }
        }
        return array($x->nodeName => $chidNode);
    }

    /**
     * Function replace
     * @access public
     * @param string $replaced
     * @param string $name
     * @param array $attributes
     * @param array $childs
     * @param array $childs_childs
     * @return void
     */
    public function replace($replaced, $name, $attributes, $childs = null, $childs_childs = null)
    {
        $chidNode = array();
        $element = $this->root->getElementsByTagName($replaced)->item(0);
        $this->root->replaceChild($this->dom->createElement($name), $element);
        // $newnode = $element = $this->root->getElementsByTagName($name)->item(0);
        $newnode = $this->root->getElementsByTagName($name)->item(0);

        if (isset($attributes['#text'])) {
            $newnode->appendChild($this->dom->createTextNode($attributes['#text']));
            unset($attributes['#text']);
        }
        if (isset($attributes['#cdata'])) {
            $newnode->appendChild($this->dom->createCDATASection($attributes['#cdata']));
            unset($attributes['#cdata']);
        }

        foreach ($attributes as $att_name => $att_value) {
            if (!is_array($att_value)) {
                $newnode->setAttribute($att_name, $att_value);
            }
        }
        if (is_array($childs)) {
            foreach ($element->childNodes as $pNode) {
                if ($pNode->nodeName != SYS_LANG && $pNode->nodeName != '#cdata-section' && $pNode->nodeName != '#text') {
                    $chidNode[] = $this->getChildNode($pNode);
                    $childs[$pNode->nodeName] = $pNode->firstChild->nodeValue;
                }
            }

            foreach ($childs as $child_name => $child_text) {

                $newnode->appendChild($this->dom->createTextNode("\n" . str_repeat(" ", 4)));
                $newnode_child = $newnode->appendChild($this->dom->createElement($child_name));
                if (is_string($child_text)) {
                    if (strip_tags($child_text) !== $child_text) {
                        $newnode_child->appendChild($this->dom->createCDATASection($child_text));
                    } else {
                       $newnode_child->appendChild($this->dom->createTextNode($child_text));
                    }
                } else {

                    if (is_array($child_text) && isset($child_text['cdata'])) {
                        $newnode_child->appendChild($this->dom->createCDATASection($child_text));
                    }
                }
                if ($child_name == SYS_LANG) {
                    if ($childs_childs != null and is_array($childs_childs)) {
                        foreach ($childs_childs as $cc) {
                            $newnode_child->appendChild($this->dom->createTextNode("\n" . str_repeat(" ", 6)));
                            $ccmode = $newnode_child->appendChild($this->dom->createElement($cc['name']));
                            $ccmode->appendChild($this->dom->createTextNode($cc['value'] ));
                            foreach ($cc['attributes'] as $cc_att_name => $cc_att_value) {
                                $ccmode->setAttribute($cc_att_name, $cc_att_value);
                            }
                        }
                    }
                } else {
                    foreach ($chidNode as $valueNode) {
                        if (array_key_exists($child_name, $valueNode)) {
                            foreach ($valueNode[$child_name] as $valOption) {
                                $ccmode = $newnode_child->appendChild($this->dom->createElement($valOption['nodeName']));
                                $ccmode->appendChild($this->dom->createTextNode($valOption['nodeValue']));
                                $ccmode->setAttribute('name', $valOption['name']);
                            }
                        }
                    }
                }
                $newnode->appendChild($this->dom->createTextNode("\n" . str_repeat(" ", 2)));
            }
        } else {
            if (isset($childs)) {
                $text_node = $childs;
                $newnode->appendChild($this->dom->createTextNode($text_node));
            }
        }
        $this->save();
    }

    /**
     * Function save
     * @param string $fname
     * @return void
     */
    public function save($fname = null)
    {
        if (!is_writable($this->xmlfile)) {
            throw new Exception("The file {$this->xmlfile} is not writeable!");
        }

        if (!isset($fname)) {
            $this->dom->save($this->xmlfile);
        } else {
            $this->xmlfile = $fname;
            $this->dom->save($this->xmlfile);
        }
    }

    /**
     * Function fixXmlFile
     * @return void
     */
    public function fixXmlFile()
    {
        $newxml = '';
        $content = file($this->xmlfile);
        foreach ($content as $line) {
            if (trim($line) != '') {
                $newxml .= $line;
            }
        }
        file_put_contents($this->xmlfile, $newxml);
    }

    /**
     * Function setHeaderAttribute
     * @param string $att_name
     * @param string $att_value
     * @return void
     */
    public function setHeaderAttribute($att_name, $att_value)
    {
        $this->root->setAttribute($att_name, $att_value);
        $this->save();
    }

    public function getHeaderAttribute($att_name)
    {
        return $this->root->getAttribute($att_name);
    }

    /**
     * Function modifyHeaderAttribute
     * @param string $att_name
     * @param string $att_new_value
     * @return void
     */
    public function modifyHeaderAttribute($att_name, $att_new_value)
    {
        $this->root->removeAttribute($att_name);
        $this->root->setAttribute($att_name, $att_new_value);
        $this->save();
    }

    /**
     * Function updateAttribute
     * @param string $node_name
     * @param string $att_name
     * @param string $att_new_value
     * @return void
     */
    public function updateAttribute($node_name, $att_name, $att_new_value)
    {
        $xpath = new DOMXPath($this->dom);
        $nodeList = $xpath->query("/dynaForm/$node_name");
        $node = $nodeList->item(0);
        $node->removeAttribute($att_name);
        $node->setAttribute($att_name, $att_new_value);
        $this->save();
    }

    /**
     * Function remove
     * @param string $v
     * @return void
     */
    public function remove($v)
    {
        if (!is_array($v)) {
            $av[0] = $v;
        } else {
            $av = $v;
        }
        foreach ($av as $e) {
            $xnode = $this->root->getElementsByTagName($e)->item(0);
            if (isset($xnode->nodeType)) {
                if ($xnode->nodeType == XML_ELEMENT_NODE) {
                    $dropednode = $this->root->removeChild($xnode);
                    /* evaluation field aditional routines */
                    $xpath = new DOMXPath($this->dom);
                    $nodeList = $xpath->query("/dynaForm/JS_$e");
                    if ($nodeList->length != 0) {
                        $tmp_node = $nodeList->item(0);
                        $this->root->removeChild($tmp_node);
                    }
                } else {
                    print("[Class dynaFormHandler] ERROR:  The \"$e\" element doesn't exist!<br>");
                }
            } else {
                print("[Class dynaFormHandler] ERROR:  The \"$e\" element doesn't exist!<br>");
            }
        }
        $this->save();
    }

    /**
     * Function nodeExists
     * @param string $node_name
     * @return boolean
     */
    public function nodeExists($node_name)
    {
        $xpath = new DOMXPath($this->dom);
        $nodeList = $xpath->query("/dynaForm/$node_name");
        $node = $nodeList->item(0);
        if ($nodeList->length != 0) {
            return true;
        } else {
            return false;
        }
    }

    //new features
    /**
     * Function moveUp
     * @param string $selected_node
     * @return void
     */
    public function moveUp($selected_node)
    {
        /* DOMNode DOMNode::insertBefore  ( DOMNode $newnode  [, DOMNode $refnode  ] )
          This function inserts a new node right before the reference node. If you plan
          to do further modifications on the appended child you must use the returned node. */
        $xpath = new DOMXPath($this->dom);
        $nodeList = $xpath->query("/dynaForm/*");
        $flag = false;
        for ($i = 0; $i < $nodeList->length; $i++) {
            $xnode = $nodeList->item($i);
            if ($selected_node == $xnode->nodeName) {
                //if is a first node move it to final with a circular logic
                if ($flag === false) {
                    $removed_node = $this->root->removeChild($xnode);
                    $this->root->appendChild($removed_node);
                    break;
                } else {
                    $removed_node = $this->root->removeChild($xnode);
                    $predecessor_node = $nodeList->item($i - 1);
                    $this->root->insertBefore($removed_node, $predecessor_node);
                    break;
                }
            }
            $flag = true;
        }
        $this->save();
    }

    /**
     * Function moveDown
     * @param string $selected_node
     * @return void
     */
    public function moveDown($selected_node)
    {
        /* DOMNode DOMNode::insertBefore  ( DOMNode $newnode  [, DOMNode $refnode  ] )
          This function inserts a new node right before the reference node. If you plan
          to do further modifications on the appended child you must use the returned node. */
        $xpath = new DOMXPath($this->dom);
        $nodeList = $xpath->query("/dynaForm/*");
        $real_length = $nodeList->length;
        for ($i = 0; $i < $nodeList->length; $i++) {
            $xnode = $nodeList->item($i);
            if ($selected_node == $xnode->nodeName) {
                //if is a last node move it to final with a circular logic
                if (($i + 1) == $real_length) {
                    if ($real_length != 1) {
                        $first_node = $nodeList->item(0);
                        $removed_node = $this->root->removeChild($xnode);
                        $this->root->insertBefore($removed_node, $first_node);
                    }
                    break;
                } else {
                    if (($i + 3) <= $real_length) {
                        $removed_node = $this->root->removeChild($xnode);
                        $predecessor_node = $nodeList->item($i + 2);
                        $this->root->insertBefore($removed_node, $predecessor_node);
                        break;
                    } else {
                        $removed_node = $this->root->removeChild($xnode);
                        $this->root->appendChild($removed_node);
                        break;
                    }
                }
            }
        }
        $this->save();
    }

    /**
     * Function getFields
     * @param array $aFilter
     * @return array
     */
    public function getFields($aFilter = Array())
    {
        $xpath = new DOMXPath($this->dom);
        $nodeList = $xpath->query("/dynaForm/*");
        $aList = Array();
        for ($i = 0; $i < $nodeList->length; $i++) {
            $xnode = $nodeList->item($i);
            if (is_array($aFilter) && sizeof($aFilter) > 0) {
                if (isset($aFilter['IN'])) {
                    if (isset($aFilter['NOT_IN'])) {
                        if (in_array($xnode->nodeName, $aFilter['IN']) && !in_array($xnode->nodeName, $aFilter['NOT_IN'])) {
                            array_push($aList, $xnode);
                        }
                    } else {
                        if (in_array($xnode->nodeName, $aFilter['IN'])) {
                            array_push($aList, $xnode);
                        }
                    }
                } else if (isset($aFilter['NOT_IN'])) {
                    if (!in_array($xnode->nodeName, $aFilter['NOT_IN'])) {
                        array_push($aList, $xnode);
                    }
                } else {
                    array_push($aList, $xnode);
                }
            } else {
                array_push($aList, $xnode);
            }
        }
        return $aList;
    }

    /**
     * Function getFieldNames
     * @param array $aFilter
     * @return array
     */
    public function getFieldNames($aFilter = Array())
    {
        $aList = $this->getFields($aFilter);
        $aFieldNames = Array();
        foreach ($aList as $item) {
            array_push($aFieldNames, $item->nodeName);
        }
        return $aFieldNames;
    }


    public function addChilds($name, $childs, $childs_childs = null)
    {
        $xpath = new DOMXPath($this->dom);
        $nodeList = @$xpath->query("/dynaForm/$name");
        if (!$nodeList) {
            throw new Exception("Error trying get the field dynaform $name, maybe it doesn't exist in {$this->xmlfile}");
        }

        if ($nodeList->length == 0) {
            $element = $this->root->appendChild($this->dom->createElement($name));
        } else
            $element = $this->root->getElementsByTagName($name)->item(0);

        if (is_array($childs)) {
            foreach ($childs as $child_name => $child_text) {

                $nodeList = $xpath->query("/dynaForm/$name/$child_name");

                if ($nodeList->length == 0) {
                    //the node doesn't exist
                    //$newnode_child
                    $childNode = $element->appendChild($this->dom->createElement($child_name));
                    $childNode->appendChild($this->dom->createCDATASection($child_text));
                } else {
                    // the node already exists
                    //update its value
                    $childNode = $element->getElementsByTagName($child_name)->item(0);

                    if ($child_text !== null) {
                        $xnode = $this->dom->createElement($childNode->nodeName);
                        $xnode->appendChild($this->dom->createCDATASection($child_text));

                        $element->replaceChild($xnode, $childNode);
                        $childNode = $element->getElementsByTagName($child_name)->item(0);
                    }
                }

                if ($childs_childs != null and is_array($childs_childs)) {
                    foreach ($childs_childs as $cc) {
                        $ccnode = $childNode->appendChild($this->dom->createElement($cc['name']));
                        $ccnode->appendChild($this->dom->createCDATASection($cc['value']));
                        foreach ($cc['attributes'] as $cc_att_name => $cc_att_value) {
                            $ccnode->setAttribute($cc_att_name, $cc_att_value);
                        }
                    }
                }
            }
        } else {
            $text_node = $childs;
            $newnode->appendChild($this->dom->createTextNode($text_node));
        }
        $this->save();
    }

    public function addOrUpdateChild($xnode, $childName, $childValue, $childAttributes)
    {
        $newNode = $this->dom->createElement($childName);
        $newNode->appendChild($this->dom->createCDATASection($childValue));

        foreach ($childAttributes as $attName => $attValue) {
            $newNode->setAttribute($attName, $attValue);
        }

        if ($xnode->hasChildNodes()) {
            foreach ($xnode->childNodes as $cnode) {
                if ($cnode->nodeName == $childName) {
                    $xnode->replaceChild($newNode, $cnode);
                    break;
                }
            }
        } else {
            $xnode->appendChild($newNode);
        }
    }

    public function getArray($node, $attributes = null)
    {
        $array = false;
        $array['__nodeName__'] = $node->nodeName;
        $text = simplexml_import_dom($node);
        $array['__nodeText__'] = trim((string) $text);

        if ($node->hasAttributes()) {
            if (isset($attributes)) {
                foreach ($attributes as $attr) {
                    if ($node->hasAttribute($attr)) {
                        $array[$attr] = $node->getAttribute($attr);
                    }
                }
            } else {
                foreach ($node->attributes as $attr) {
                    $array[$attr->nodeName] = $attr->nodeValue;
                }
            }
        }

        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 0) {
                $return;
            } else {
                foreach ($node->childNodes as $childNode) {
                    $childNode->normalize();
                    //if ($childNode->nodeType == XML_TEXT_NODE || $childNode->nodeType == XML_CDATA_SECTION_NODE) {
                    if ($childNode->nodeType == XML_ELEMENT_NODE) {
                        $array[$childNode->nodeName][] = $this->getArray($childNode);
                    } else if ($childNode->nodeType == XML_TEXT_NODE || $childNode->nodeType == XML_CDATA_SECTION_NODE) {
                        //$array[$childNode->nodeName] = $childNode->textContent;
                        $text = simplexml_import_dom($childNode->parentNode);
                        $array['__nodeText__'] = trim((string) $text);
                    }
                }
            }
        }

        return $array;
    }
}

