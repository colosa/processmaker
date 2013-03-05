<?php

/**
 * class.toolBar.php
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
 * ToolBar - ToolBar class
 *
 * @package workflow.engine.ProcessMaker
 */
class ToolBar extends form
{
    public $type = 'toolbar';
    public $align = 'left';
}

/**
 * XmlForm_Field_ToolBar - XmlForm_Field_ToolBar class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlForm_Field_ToolBar extends XmlForm_Field
{

    public $xmlfile = '';
    public $type = 'toolbar';
    public $toolBar;
    public $home = '';
    public $withoutLabel = true;

    /**
     * Constructor of the class XmlForm_Field_ToolBar
     *
     * @param string $xmlNode
     * @param string $lang
     * @param string $home
     * @param string $owner
     * @return void
     */
    public function XmlForm_Field_ToolBar($xmlNode, $lang = 'en', $home = '', $owner = ' ')
    {
        parent::XmlForm_Field($xmlNode, $lang, $home, $owner);
        $this->home = $home;
    }

    /**
     * Prints the ToolBar
     *
     * @param string $value
     * @return string
     */
    public function render($value)
    {
        $this->toolBar = new toolBar($this->xmlfile, $this->home);
        $template = PATH_CORE . 'templates/' . $this->type . '.html';
        $out = $this->toolBar->render($template, $scriptCode);
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile($this->toolBar->scriptURL);
        $oHeadPublisher->addScriptCode($scriptCode);
        return $out;
    }
}

/**
 * XmlForm_Field_toolButton - XmlForm_Field_toolButton class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlForm_Field_toolButton extends XmlForm_Field
{

    public $file = '';
    public $fileAlt = '';
    public $url = '';
    public $urlAlt = '';
    public $home = 'public_html';
    /* types of buttons:
     *    image
     *    text
     *    image/text
     *    text/image
     */
    public $buttonType = 'image';
    public $withoutLabel = false;
    public $buttonStyle = '';
    /* $hoverMethod : back | switch */
    public $hoverMethod = 'back';
    public $class;

    /**
     * Prints the components of the toolBar
     *
     * @param string $value
     * @return string
     */
    public function render($value)
    {
        $url = $this->file;
        if ($this->home === "methods") {
            $url = G::encryptlink(SYS_URI . $url);
        }
        if ($this->home === "public_html") {
            $url = '/' . $url;
        }
        $urlAlt = $this->fileAlt;
        if ($this->fileAlt !== '') {
            if ($this->home === "methods") {
                $urlAlt = G::encryptlink(SYS_URI . $urlAlt);
            }
            if ($this->home === "public_html") {
                $urlAlt = '/' . $urlAlt;
            }
        }
        $this->url = $url;
        $this->urlAlt = $urlAlt;
        switch ($this->buttonType) {
            case 'image':
                $html = '';
                if ($this->hoverMethod === 'back') {
                    $html = '<img src="' . htmlentities($url, ENT_QUOTES, 'utf-8') . '"' . (($this->style) ? ' style="' . $this->style . '"' : '') . ' onmouseover=\'backImage(this,"url(' . htmlentities($urlAlt, ENT_QUOTES, 'utf-8') . ') no-repeat")\' onmouseout=\'backImage(this,"")\' title=\'' . addslashes($this->label) . '\' />';
                } elseif ($this->hoverMethod === 'switch') {
                    $html = '<img src="' . htmlentities($url, ENT_QUOTES, 'utf-8') . '"' . (($this->style) ? ' style="' . $this->style . '"' : '') . ' onmouseover=\'switchImage(this,"' . htmlentities($url, ENT_QUOTES, 'utf-8') . '","' . htmlentities($urlAlt, ENT_QUOTES, 'utf-8') . '")\' onmouseout=\'switchImage(this,"' . htmlentities($url, ENT_QUOTES, 'utf-8') . '","' . htmlentities($urlAlt, ENT_QUOTES, 'utf-8') . '")\'/>';
                } else {
                    $html = '<img src="' . htmlentities($url, ENT_QUOTES, 'utf-8') . '"' . (($this->style) ? ' style="' . $this->style . '"' : '') . '/>';
                }
                break;
            case 'text':
                $html = $this->htmlentities($this->label, ENT_QUOTES, 'utf-8');
                break;
            case 'html':
                $html = '<div ' . ' onmouseover=\'backImage(this,"url(' . htmlentities($urlAlt, ENT_QUOTES, 'utf-8') . ') no-repeat")\' onmouseout=\'backImage(this,"")\'  style="width:25px;height:25px;margin-bottom:3px">' . $this->label . '</div>';

                //$html=$this->label;
                break;
            case 'image/text':
                $html = '<img src="' . htmlentities($url, ENT_QUOTES, 'utf-8') . '"' . (($this->style) ? ' style="' . $this->style . '"' : '') . '/><br/>' . $this->htmlentities($this->label, ENT_QUOTES, 'utf-8');
                break;
            case 'text/image':
                $html = $this->htmlentities($this->label, ENT_QUOTES, 'utf-8') . '<br/><img src="' . htmlentities($url, ENT_QUOTES, 'utf-8') . '"' . (($this->style) ? ' style="' . $this->style . '"' : '') . '/>';
                break;
            case 'dropdown':
                $html = '';
                if (isset($this->owner->values['PRO_UID'])) {
                    G::LoadClass('processMap');
                    $criteria = processMap::getDynaformsCriteria($this->owner->values['PRO_UID']);
                    $dataset = DynaformPeer::doSelectRS($criteria);
                    if ($dataset->getRecordCount() > 0) {
                        $html .= '<span style="display:inline-block; font-size: 8pt;margin-left: 5px;margin-bottom: 3px;">' . G::LoadTranslation('ID_DYNAFORM');
                        $html .= ': <select id="_dynaformsList_" onchange="window.location = \'dynaforms_Editor?PRO_UID=' . $this->owner->values['PRO_UID'];
                        $html .= '&DYN_UID=\' + this.value;" class="module_app_input___gray">';
                        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $dataset->next();
                        while ($row = $dataset->getRow()) {
                            $html .= '<option value="' . $row['DYN_UID'] . '"';
                            $html .= ($this->owner->values['DYN_UID'] == $row['DYN_UID'] ? ' selected="selected"' : '') . '>';
                            $html .= htmlentities($row['DYN_TITLE'], ENT_QUOTES, 'utf-8') . '</option>';
                            $dataset->next();
                        }
                        $html .= '</select></span>';
                    }
                }
                return $html;
                break;
            case 'class':
                $html = '<a href="#" onclick="' . $this->onclick . '" onmouseover="backImage(this, \'url(/images/dynamicForm/hover.gif) no-repeat\')" onmouseout="backImage(this, \'\')"  style="height:25px;margin-bottom:3px">
                 <div class="' . $this->class . '" title="' . strip_tags($this->label) . '" style="height:25px;margin-bottom:3px"></div>
               </a>';
                return $html;
        }
        return '<a class="toolButton" ' . (($this->buttonStyle) ? ' style="' . $this->buttonStyle . '"' : '') . (($this->onclick) ? ' onclick="' . htmlentities($this->onclick, ENT_QUOTES, 'utf-8') . '"' : '') . '>' . $html . '</a>';
    }
}
 