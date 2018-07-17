<?php

/**
 * ToolBar - ToolBar
 * XmlFormFieldToolBar - XmlFormFieldToolBar class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlFormFieldToolBar extends XmlFormField
{
    public $xmlfile = '';
    public $type = 'toolbar';
    public $toolBar;
    public $home = '';
    public $withoutLabel = true;

    /**
     * Constructor of the class XmlFormFieldToolBar
     *
     * @param string $xmlNode
     * @param string $lang
     * @param string $home
     * @param string $owner
     * @return void
     */
    public function XmlFormFieldToolBar($xmlNode, $lang = 'en', $home = '', $owner = ' ')
    {
        parent::__construct($xmlNode, $lang, $home, $owner);
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
        $this->toolBar = new ToolBar($this->xmlfile, $this->home);
        $template = PATH_CORE . 'templates/' . $this->type . '.html';
        $out = $this->toolBar->render($template, $scriptCode);
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile($this->toolBar->scriptURL);
        $oHeadPublisher->addScriptCode($scriptCode);
        return $out;
    }
}
