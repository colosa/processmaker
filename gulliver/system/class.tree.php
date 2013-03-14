<?php

/**
 * class.tree.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * �
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
G::LoadSystem('objectTemplate');

class Tree extends Xml_Node
{

    public $template = 'tree.html';
    public $nodeType = 'base';
    public $nodeClass = 'treeNode';
    public $contentClass = 'treeContent';
    public $width = '100%';
    public $contentWidth = '360';
    public $contracted = false;
    public $showSign = true;
    public $isChild = false;
    public $plus = "<span style='position:absolute; width:16px;height:22px;cursor:pointer;'onclick='tree.expand(this.parentNode);'>&nbsp;</span>";
    public $minus = "<span  style='position:absolute; width:16px;height:22px;cursor:pointer' onclick='tree.contract(this.parentNode);'>&nbsp;</span>";
    public $point = "<span style='position:absolute; width:5px;height:10px;cursor:pointer;'  onclick='tree.select(this.parentNode);'>&nbsp;</span>";

    /**
     * Tree
     *
     * @param array $xmlnode default value NULL
     *
     * @return none
     */
    public function Tree($xmlnode = null)
    {
        if (!isset($xmlnode)) {
            return;
        }
        if (isset($xmlnode->attributes['nodeType'])) {
            $this->nodeType = $xmlnode->attributes['nodeType'];
        }
        foreach ($xmlnode as $key => $value) {
            if ($key === 'children') {
                foreach ($xmlnode->children as $key => $value) {
                    $this->children[$key] = new Tree($value->toTree());
                }
            } elseif ($key === 'attributes') {
                foreach ($xmlnode->attributes as $key => $value) {
                    $this->{$key} = $value;
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * &addChild
     *
     * @param string $name
     * @param string $label
     * @param array $attributes
     *
     * @return object(Tree) $newNode
     */
    public function &addChild($name, $label, $attributes = array())
    {
        $newNode = new Tree(new Xml_Node($name, 'open', $label, $attributes));
        $this->children[] = & $newNode;
        return $newNode;
    }

    /**
     * printPlus
     *
     * @return string '<span>...</span>'
     */
    public function printPlus()
    {
        $plus = 'none';
        $minus = 'none';
        $point = 'none';
        if ($this->showSign) {
            if ((sizeof($this->children) > 0) && ($this->contracted)) {
                $plus = '';
            } elseif ((sizeof($this->children) > 0) && (!$this->contracted)) {
                $minus = '';
            } else {
                $point = '';
            }
        }
        return "<span class='treePlus'  name='plus' style='display:$plus;'>{$this->plus}</span>" . "<span class='treeMinus' name='minus' style='display:$minus'>{$this->minus}</span>" . "<span class='treePointer' name='point' style='display:$point'>{$this->point}</span>";
    }

    /**
     * printLabel
     *
     * @return $this->value
     */
    public function printLabel()
    {
        return $this->value;
    }

    /**
     * printContent
     *
     * @return string $html
     */
    public function printContent()
    {
        $html = '';
        $row = 0;
        foreach ($this->children as $child) {
            if ($row) {
                $child->nodeClass = 'treeNodeAlternate';
            }
            $html .= $child->render();
            $row = ($row + 1) % 2;
        }
        return $html;
    }

    /**
     * render
     *
     * @return $obj->printObject( array( 'node' => &$this ) )
     */
    public function render()
    {
        $obj = new objectTemplate($this->template);
        return $obj->printObject(array('node' => &$this));
    }
}

