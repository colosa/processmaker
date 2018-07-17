<?php

/**
 * ToolBar - ToolBar
 * XmlFormFieldToolButton - XmlFormFieldToolButton class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlFormFieldToolButton extends XmlFormField
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
                    $criteria = ProcessMap::getDynaformsCriteria($this->owner->values['PRO_UID']);
                    $dataset = DynaformPeer::doSelectRS($criteria);
                    if ($dataset->getRecordCount() > 0) {
                        $html .= '<span style="display:inline-block; font-size: 8pt;margin-left: 5px;margin-bottom: 3px;">' . G::LoadTranslation('ID_DYNAFORM');
                        $html .= ': <select id="_dynaformsList_" onchange="window.location = \'dynaforms_Editor?PRO_UID=' . $this->owner->values['PRO_UID'];
                        $html .= '&DYN_UID=\' + this.value + \'' . (isset($_REQUEST['processMap3']) ? '&processMap3=1' : '') . '\';" class="module_app_input___gray">';
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
