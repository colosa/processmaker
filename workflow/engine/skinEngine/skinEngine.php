<?php
/**
 * Class SkinEngine
 *
 * This class load and dispatch the main systems layouts
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @author Hugo Loza
 */

define('SE_LAYOUT_NOT_FOUND', 6);

class SkinEngine
{

  private $layout   = '';
  private $template = '';
  private $skin     = '';
  private $content  = '';
  private $mainSkin = '';

  private $skinFiles = array();

  private $forceTemplateCompile = true;
  private $skinVariants = array();

  private $skinsBasePath     = array();
  private $configurationFile = array();
  private $layoutFile        = array();
  private $layoutFileBlank   = array();
  private $layoutFileExtjs   = array();
  private $layoutFileRaw     = array();
  private $layoutFileTracker = array();
  private $layoutFileSubmenu = array();

  private $cssFileName = '';

  public function __construct($template, $skin, $content)
  {
    $this->template = $template;
    $this->skin = $skin;
    $this->content = $content;
    $this->skinVariants = array('blank','extjs','raw','tracker','submenu');
    $this->skinsBasePath = G::ExpandPath("skinEngine");

    $this->_init();
  }

  private function _init()
  {

    // setting default skin
    if (!isset($this->skin) || $this->skin == "") {
      $this->skin = "classic";
    }

    // deprecated submenu type ""green-submenu"" now is mapped to "submenu"
    if ($this->skin == "green-submenu") {
      $this->skin = "submenu";
    }

    if (!in_array(strtolower($this->skin), $this->skinVariants)) {
      $this->forceTemplateCompile = true; //Only save in session the main SKIN

      if (isset($_SESSION['currentSkin']) && $_SESSION['currentSkin'] != $this->skin) {
        $this->forceTemplateCompile = true;
      }
      $_SESSION['currentSkin'] = SYS_SKIN;
    }
    else {
      $_SESSION['currentSkin'] = SYS_SKIN;
      $_SESSION['currentSkinVariant'] = $this->skin;
    }

    // setting default skin
    if (!isset($_SESSION['currentSkin'])) {
      $_SESSION['currentSkin'] = "classic";
    }

    $this->mainSkin = $_SESSION['currentSkin'];


    $skinObject = null;

    //Set defaults "classic"
    $configurationFile = $this->skinsBasePath . 'base' . PATH_SEP . 'config.xml';
    $layoutFile        = $this->skinsBasePath . 'base' . PATH_SEP . 'layout.html';
    $layoutFileBlank   = $this->skinsBasePath . 'base' . PATH_SEP . 'layout-blank.html';
    $layoutFileExtjs   = $this->skinsBasePath . 'base' . PATH_SEP . 'layout-extjs.html';
    $layoutFileRaw     = $this->skinsBasePath . 'base' . PATH_SEP . 'layout-raw.html';
    $layoutFileTracker = $this->skinsBasePath . 'base' . PATH_SEP . 'layout-tracker.html';
    $layoutFileSubmenu = $this->skinsBasePath . 'base' . PATH_SEP . 'layout-submenu.html';


    //Based on requested Skin look if there is any registered with that name
    if (strtolower($this->mainSkin) != "classic") {
      if (defined('PATH_CUSTOM_SKINS') && is_dir(PATH_CUSTOM_SKINS . $this->mainSkin)) { // check this skin on user skins path
        $skinObject = PATH_CUSTOM_SKINS . $this->mainSkin;
      }
      else if (is_dir($this->skinsBasePath . $this->mainSkin)) { // check this skin on core skins path
        $skinObject = $this->skinsBasePath . $this->mainSkin;
      }
      else { //Skin doesn't exist
        $this->mainSkin = "classic";
      }
    }

    //This should have an XML definition and a layout html
    if ($skinObject && file_exists($skinObject . PATH_SEP . 'config.xml')
      && file_exists($skinObject . PATH_SEP . 'layout.html')) {

      $configurationFile = $skinObject . PATH_SEP . 'config.xml';
      $layoutFile        = $skinObject . PATH_SEP . 'layout.html';

      if (file_exists($skinObject . PATH_SEP . 'layout-blank.html')){
        $layoutFileBlank = $skinObject . PATH_SEP . 'layout-blank.html';
      }
      if (file_exists($skinObject . PATH_SEP . 'layout-extjs.html')){
        $layoutFileExtjs = $skinObject . PATH_SEP . 'layout-extjs.html' ;
      }
      if (file_exists($skinObject . PATH_SEP . 'layout-raw.html')){
        $layoutFileRaw   = $skinObject . PATH_SEP . 'layout-raw.html';
      }
      if (file_exists($skinObject . PATH_SEP . 'layout-tracker.html')){
        $layoutFileTracker = $skinObject . PATH_SEP . 'layout-tracker.html';
      }
      if (file_exists($skinObject . PATH_SEP . 'layout-submenu.html')){
        $layoutFileSubmenu = $skinObject . PATH_SEP . 'layout-submenu.html';
      }
    }

    $this->layoutFile        = pathInfo($layoutFile);
    $this->layoutFileBlank   = pathInfo($layoutFileBlank);
    $this->layoutFileExtjs   = pathInfo($layoutFileExtjs);
    $this->layoutFileTracker = pathInfo($layoutFileTracker);
    $this->layoutFileRaw     = pathInfo($layoutFileRaw);
    $this->layoutFileSubmenu = pathInfo($layoutFileSubmenu);

    $this->cssFileName = $this->mainSkin;

    if ($this->skin != $this->mainSkin && in_array(strtolower($this->skin), $this->skinVariants)) {
      $this->cssFileName .= "-" . $this->skin;
    }
  }

  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  public function dispatch()
  {
    $skinMethod = '_' . strtolower($this->skin);

    try {
      if (!method_exists($this, $skinMethod)) {
        $skinMethod = '_default';
      }

      $this->$skinMethod();
    }
    catch (Exception $e) {
      switch ($e->getCode()) {
        case SE_LAYOUT_NOT_FOUND:

          $data['exception_type']     = G::LoadTranslation('ID_SKIN_EXCEPTION');
          $data['exception_title']    = G::LoadTranslation('ID_SKIN_LAYOUT_NOT_FOUND');
          $data['exception_message']  = G::LoadTranslation('ID_SKIN_INCORRECT_VERIFY_URL');
          $data['exception_list']     = array();
          if (substr($this->mainSkin, 0, 2) != 'ux') {
            $url = '../login/login';
          }
          else {
            $url = '../main/login';
          }

          $link = '<a href="'.$url.'">Try Now</a>';

          $data['exception_notes'][] = G::LoadTranslation('ID_REDIRECT_URL'). $link;

          G::renderTemplate(PATH_TPL . 'exception', $data);
          break;
      }

      exit(0);
    }
  }

  /**
   * Skins Alternatives
   */

  private function _raw()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php

    G::verifyPath ( PATH_SMARTY_C,   true );
    G::verifyPath ( PATH_SMARTY_CACHE, true );

    $smarty = new Smarty();
    $oHeadPublisher =& headPublisher::getSingleton();

    $smarty->template_dir = $this->layoutFileRaw['dirname'];
    $smarty->compile_dir  = PATH_SMARTY_C;
    $smarty->cache_dir    = PATH_SMARTY_CACHE;
    $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

    if (isset($oHeadPublisher)) {
      $header = $oHeadPublisher->printRawHeader();
    }

    $smarty->assign('header', $header );
    $smarty->force_compile = $this->forceTemplateCompile;
    $smarty->display($this->layoutFileRaw['basename']);
  }

  private function _plain()
  {
    $oHeadPublisher = & headPublisher::getSingleton();
    echo $oHeadPublisher->renderExtJs();
  }

  private function _extjs()
  {
    G::LoadClass('serverConfiguration');
    $oServerConf    =& serverConf::getSingleton();
    $oHeadPublisher =& headPublisher::getSingleton();

    if( $oHeadPublisher->extJsInit === true){
      $header = $oHeadPublisher->getExtJsVariablesScript();
      $styles = $oHeadPublisher->getExtJsStylesheets($this->cssFileName);
      $body   = $oHeadPublisher->getExtJsScripts();

      //default
      $templateFile = G::ExpandPath( "skinEngine" ).'base'.PATH_SEP .'extJsInitLoad.html';
      //Custom skins
      if (defined('PATH_CUSTOM_SKINS') && is_dir(PATH_CUSTOM_SKINS . $this->mainSkin)) {
        $templateFile = PATH_CUSTOM_SKINS . $this->mainSkin . PATH_SEP .'extJsInitLoad.html';
      }
      //Skin uxs - simplified
      if (!isset($_SESSION['user_experience'])) {
        $_SESSION['user_experience'] = 'NORMAL';
      }
      if ($_SESSION['user_experience'] != 'NORMAL') {
        $templateFile = (is_dir(PATH_CUSTOM_SKINS . 'uxs')) ? PATH_CUSTOM_SKINS . 'simplified' . PATH_SEP . 'extJsInitLoad.html' : $templateFile;
      }
    }
    else {
      $styles  = "";
      $header  = $oHeadPublisher->getExtJsStylesheets($this->cssFileName);
      $header .= $oHeadPublisher->includeExtJs();
      $body    = $oHeadPublisher->renderExtJs();

      $templateFile = $this->layoutFile['dirname'] . PATH_SEP . $this->layoutFileExtjs['basename'];
    }

    $template = new TemplatePower($templateFile);
    $template->prepare();
    $template->assign('header', $header);
    $template->assign('styles', $styles);
    $template->assign('bodyTemplate', $body);

    $doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $meta    = null;
    $dirBody = null;

    if (preg_match("/^.*\(.*MSIE (\d+)\..+\).*$/", $_SERVER["HTTP_USER_AGENT"], $arrayMatch)) {
        $ie = intval($arrayMatch[1]);
        $swTrident = (preg_match("/^.*Trident.*$/", $_SERVER["HTTP_USER_AGENT"]))? 1 : 0; //Trident only in IE8+

        $sw = 1;

        if ((($ie == 7 && $swTrident == 1) || $ie == 8) && !preg_match("/^ux.+$/", SYS_SKIN)) { //IE8
            $sw = 0;
        }

        if ($sw == 1) {
            $doctype = null;
            $meta    = "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=$ie\" />";
        }
    }

    $serverConf = &serverConf::getSingleton();

    if ($serverConf->isRtl(SYS_LANG)) {
        $dirBody = "dir=\"RTL\"";
    }

    $template->assign("doctype", $doctype);
    $template->assign("meta", $meta);
    $template->assign("dirBody", $dirBody);

    echo $template->getOutputContent();
  }

  private function _blank()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php

    G::verifyPath(PATH_SMARTY_C,   true);
    G::verifyPath(PATH_SMARTY_CACHE, true);

    $smarty = new Smarty();
    $oHeadPublisher =& headPublisher::getSingleton();

    $smarty->template_dir = $this->layoutFileBlank['dirname'];
    $smarty->compile_dir  = PATH_SMARTY_C;
    $smarty->cache_dir    = PATH_SMARTY_CACHE;
    $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

    if (isset($oHeadPublisher)) {
      $header = $oHeadPublisher->printHeader();
      $header .= $oHeadPublisher->getExtJsStylesheets($this->cssFileName);
    }

    $smarty->assign('username', (isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ' ' . G::LoadTranslation('ID_IN') . ' ' . SYS_SYS . ')' : '') );
    $smarty->assign('header', $header );
    $smarty->force_compile = $this->forceTemplateCompile;

    // display
    $smarty->display($this->layoutFileBlank['basename']);
  }

  private function _submenu()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php
    global $G_ENABLE_BLANK_SKIN;
    //menu
    global $G_MAIN_MENU;
    global $G_SUB_MENU;
    global $G_MENU_SELECTED;
    global $G_SUB_MENU_SELECTED;
    global $G_ID_MENU_SELECTED;
    global $G_ID_SUB_MENU_SELECTED;

    G::verifyPath(PATH_SMARTY_C, true);
    G::verifyPath(PATH_SMARTY_CACHE, true);

    $smarty = new Smarty();
    $oHeadPublisher = & headPublisher::getSingleton();

    $smarty->template_dir = $this->layoutFileSubmenu['dirname'];
    $smarty->compile_dir  = PATH_SMARTY_C;
    $smarty->cache_dir    = PATH_SMARTY_CACHE;
    $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

    if (isset($G_ENABLE_BLANK_SKIN) && $G_ENABLE_BLANK_SKIN) {
      $smarty->display($layoutFileBlank['basename']);
    }
    else {
      $header = '';

      if (isset($oHeadPublisher)) {
        $oHeadPublisher->title = isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ' ' . G::LoadTranslation('ID_IN') . ' ' . SYS_SYS . ')' : '';
        $header = $oHeadPublisher->printHeader();
        $header .= $oHeadPublisher->getExtJsStylesheets($this->cssFileName);
      }

      $footer = '';

      if (strpos($_SERVER['REQUEST_URI'], '/login/login') !== false) {
        $freeOfChargeText = "";
        if (! defined('SKIP_FREE_OF_CHARGE_TEXT')) {
          $freeOfChargeText = "Supplied free of charge with no support, certification, warranty, <br>maintenance nor indemnity by Colosa and its Certified Partners.";
        }
        $footer .= "<br />Copyright &copy; 2003-" . date('Y') . " <a href=\"http://www.colosa.com\" alt=\"Colosa, Inc.\" target=\"_blank\">Colosa, Inc.</a> All rights reserved.<br /> $freeOfChargeText " . "<br><br/><a href=\"http://www.processmaker.com\" alt=\"Powered by ProcessMaker - Open Source Workflow & Business Process Management (BPM) Management Software\" title=\"Powered by ProcessMaker\" target=\"_blank\"><img src=\"/images/PowerdbyProcessMaker.png\" border=\"0\" /></a>";
      }

      $oMenu = new Menu();
      $menus = $oMenu->generateArrayForTemplate($G_MAIN_MENU, 'SelectedMenu', 'mainMenu', $G_MENU_SELECTED, $G_ID_MENU_SELECTED);
      $smarty->assign('menus', $menus);

      if (substr(SYS_SKIN, 0, 2) == 'ux') {
        $smarty->assign('exit_editor', 1);
        $smarty->assign('exit_editor_label', G::loadTranslation('ID_CLOSE_EDITOR'));
      }

      $oSubMenu = new Menu();
      $subMenus = $oSubMenu->generateArrayForTemplate($G_SUB_MENU, 'selectedSubMenu', 'subMenu', $G_SUB_MENU_SELECTED, $G_ID_SUB_MENU_SELECTED);
      $smarty->assign('subMenus', $subMenus);

      if (! defined('NO_DISPLAY_USERNAME')) {
        define('NO_DISPLAY_USERNAME', 0);
      }
      if (NO_DISPLAY_USERNAME == 0) {
        $smarty->assign('userfullname', isset($_SESSION['USR_FULLNAME']) ? $_SESSION['USR_FULLNAME'] : '');
        $smarty->assign('user', isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ')' : '');
        $smarty->assign('rolename', isset($_SESSION['USR_ROLENAME']) ? $_SESSION['USR_ROLENAME'] . '' : '');
        $smarty->assign('pipe', isset($_SESSION['USR_USERNAME']) ? ' | ' : '');
        $smarty->assign('logout', G::LoadTranslation('ID_LOGOUT'));
        $smarty->assign('workspace', defined('SYS_SYS')?SYS_SYS: '');
        $uws = (isset($_SESSION['USR_ROLENAME']) && $_SESSION['USR_ROLENAME'] != '')? strtolower(G::LoadTranslation('ID_WORKSPACE_USING')): G::LoadTranslation('ID_WORKSPACE_USING');
        $smarty->assign('workspace_label', $uws);
        $smarty->assign('udate', G::getformatedDate(date('Y-m-d'), 'M d, yyyy', SYS_LANG));

      }

      if (defined('SYS_SYS')) {
        $logout = '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/login/login';
      }
      else {
        $logout = '/sys/' . SYS_LANG . '/' . SYS_SKIN . '/login/login';
      }

      $smarty->assign('linklogout', $logout);
      $smarty->assign('header', $header);
      $smarty->assign('footer', $footer);
      $smarty->assign('tpl_menu', PATH_TEMPLATE . 'menu.html');
      $smarty->assign('tpl_submenu', PATH_TEMPLATE . 'submenu.html');

      if (class_exists('PMPluginRegistry')) {
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        $sCompanyLogo = $oPluginRegistry->getCompanyLogo('/images/processmaker.logo.jpg');
      }
      else {
        $sCompanyLogo = '/images/processmaker.logo.jpg';
      }

      $smarty->assign('logo_company', $sCompanyLogo);
      $smarty->display($this->layoutFileSubmenu['basename']);
    }
  }

  private function _tracker()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php
    global $G_ENABLE_BLANK_SKIN;

    G::verifyPath ( PATH_SMARTY_C,   true );
    G::verifyPath ( PATH_SMARTY_CACHE, true );

    $smarty = new Smarty();
    $oHeadPublisher =& headPublisher::getSingleton();

    $smarty->template_dir = PATH_SKINS;
    $smarty->compile_dir  = PATH_SMARTY_C;
    $smarty->cache_dir    = PATH_SMARTY_CACHE;
    $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

    if ( isset($G_ENABLE_BLANK_SKIN) && $G_ENABLE_BLANK_SKIN ) {
      $smarty->force_compile = $this->forceTemplateCompile;
      $smarty->display($this->layoutFileBlank['basename']);
    }
    else {
      $header = '';

      if (isset($oHeadPublisher)) {
        $oHeadPublisher->title = isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ' ' . G::LoadTranslation('ID_IN') . ' ' . SYS_SYS . ')' : '';
        $header = $oHeadPublisher->printHeader();
      }

      $footer = '';

      if (strpos($_SERVER['REQUEST_URI'], '/login/login') !== false) {
        $footer .= "<br />Copyright &copy; 2003-" . date('Y') . " Colosa, Inc. All rights reserved.";
      }

      //menu
      global $G_MAIN_MENU;
      global $G_SUB_MENU;
      global $G_MENU_SELECTED;
      global $G_SUB_MENU_SELECTED;
      global $G_ID_MENU_SELECTED;
      global $G_ID_SUB_MENU_SELECTED;

      $oMenu = new Menu();
      $menus = $oMenu->generateArrayForTemplate ( $G_MAIN_MENU,'SelectedMenu', 'mainMenu',$G_MENU_SELECTED, $G_ID_MENU_SELECTED );
      $smarty->assign('menus', $menus  );

      $oSubMenu = new Menu();
      $subMenus = $oSubMenu->generateArrayForTemplate ( $G_SUB_MENU,'selectedSubMenu', 'subMenu',$G_SUB_MENU_SELECTED, $G_ID_SUB_MENU_SELECTED );
      $smarty->assign('subMenus', $subMenus  );

      $smarty->assign('user',   isset($_SESSION['USR_USERNAME']) ? $_SESSION['USR_USERNAME'] : '');
      $smarty->assign('pipe',   isset($_SESSION['USR_USERNAME']) ? ' | ' : '');
      $smarty->assign('logout', G::LoadTranslation('ID_LOGOUT'));
      $smarty->assign('header', $header );
      $smarty->assign('tpl_menu', PATH_TEMPLATE . 'menu.html' );
      $smarty->assign('tpl_submenu', PATH_TEMPLATE . 'submenu.html' );

      if (class_exists('PMPluginRegistry')) {
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        $sCompanyLogo = $oPluginRegistry->getCompanyLogo ( '/images/processmaker.logo.jpg' );
      }
      else
      $sCompanyLogo = '/images/processmaker.logo.jpg';

      $smarty->assign('logo_company', $sCompanyLogo );
      $smarty->force_compile = $this->forceTemplateCompile;
      $smarty->display($this->layoutFileTracker['basename']);
    }
  }

  private function _mvc()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php
    G::LoadClass('serverConfiguration');
    $oServerConf =& serverConf::getSingleton();
    $oHeadPublisher =& headPublisher::getSingleton();

    $smarty = new Smarty();

    $smarty->compile_dir  = PATH_SMARTY_C;
    $smarty->cache_dir    = PATH_SMARTY_CACHE;
    $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

    $viewVars = $oHeadPublisher->getVars();

    // verify if is using extJs engine
    if (count($oHeadPublisher->extJsScript) > 0) {
      $header  = $oHeadPublisher->getExtJsStylesheets($this->cssFileName.'-extJs');
      $header .= $oHeadPublisher->includeExtJs();

      $smarty->assign('_header', $header);
    }

    $contentFiles = $oHeadPublisher->getContent();
    $viewFile = isset($contentFiles[0]) ? $contentFiles[0] : '';

    if (empty($this->layout)) {
      $smarty->template_dir  = PATH_TPL;
      $tpl = $viewFile . '.html';
    }
    else {
      $smarty->template_dir = $this->layoutFile['dirname'];
      $tpl = 'layout-'.$this->layout.'.html';
      //die($smarty->template_dir.PATH_SEP.$tpl);

      if (!file_exists($smarty->template_dir . PATH_SEP . $tpl)) {
        $e = new Exception("Layout $tpl does not exist!", SE_LAYOUT_NOT_FOUND);
        $e->layoutFile = $smarty->template_dir . PATH_SEP . $tpl;

        throw $e;
      }
      $smarty->assign('_content_file', $viewFile);
    }

    if (strpos($viewFile, '.') === false) {
      $viewFile .= '.html';
    }

    foreach ($viewVars as $key => $value) {
      $smarty->assign($key, $value);
    }

    if (defined('DEBUG') && DEBUG ) {
      $smarty->force_compile = true;
    }

    $smarty->assign('_skin', $this->mainSkin);

    $smarty->display($tpl);
  }

  private function _default()
  {
    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; // put full path to Smarty.class.php
    global $G_ENABLE_BLANK_SKIN;
    //menu
    global $G_PUBLISH;
    global $G_MAIN_MENU;
    global $G_SUB_MENU;
    global $G_MENU_SELECTED;
    global $G_SUB_MENU_SELECTED;
    global $G_ID_MENU_SELECTED;
    global $G_ID_SUB_MENU_SELECTED;

    G::verifyPath(PATH_SMARTY_C, true);
    G::verifyPath(PATH_SMARTY_CACHE, true);

    $smarty = new Smarty();
    $oHeadPublisher = & headPublisher::getSingleton();

    $smarty->compile_dir = PATH_SMARTY_C;
    $smarty->cache_dir = PATH_SMARTY_CACHE;
    $smarty->config_dir = PATH_THIRDPARTY . 'smarty/configs';

    //To setup en extJS Theme for this Skin
    G::LoadClass('serverConfiguration');
    $oServerConf =& serverConf::getSingleton();
    $extSkin = $oServerConf->getProperty("extSkin");

    if(!$extSkin) {
      $extSkin = array();
    }

    $extSkin[SYS_SKIN]="xtheme-gray";
    $oServerConf->setProperty("extSkin",$extSkin);
    //End of extJS Theme setup

    if (isset($G_ENABLE_BLANK_SKIN) && $G_ENABLE_BLANK_SKIN) {
      $smarty->template_dir  = $this->layoutFileBlank['dirname'];
      $smarty->force_compile = $this->forceTemplateCompile;

      $smarty->display($layoutFileBlank['basename']);
    }
    else {
      $smarty->template_dir = $this->layoutFile['dirname'];
      $header = '';

      if (isset($oHeadPublisher)) {
        if (defined('SYS_SYS')) {
            $oHeadPublisher->title = isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ' ' . G::LoadTranslation('ID_IN') . ' ' . SYS_SYS . ')' : '';
        }
        $header = $oHeadPublisher->printHeader();
        $header .= $oHeadPublisher->getExtJsStylesheets($this->cssFileName);
      }

      $footer = '';

      if (strpos($_SERVER['REQUEST_URI'], '/login/login') !== false) {

        $freeOfChargeText = "";
        if (! defined('SKIP_FREE_OF_CHARGE_TEXT'))
        $freeOfChargeText = "Supplied free of charge with no support, certification, warranty, <br>maintenance nor indemnity by Colosa and its Certified Partners.";
        if(class_exists('pmLicenseManager')) $freeOfChargeText="";
        $footer .= "<br />Copyright &copy; 2003-" . date('Y') . " <a href=\"http://www.colosa.com\" alt=\"Colosa, Inc.\" target=\"_blank\">Colosa, Inc.</a> All rights reserved.<br /> $freeOfChargeText " . "<br><br/><a href=\"http://www.processmaker.com\" alt=\"Powered by ProcessMaker - Open Source Workflow & Business Process Management (BPM) Management Software\" title=\"Powered by ProcessMaker\" target=\"_blank\"><img src=\"/images/PowerdbyProcessMaker.png\" border=\"0\" /></a>";
      }

      $oMenu = new Menu();
      $menus = $oMenu->generateArrayForTemplate($G_MAIN_MENU, 'SelectedMenu', 'mainMenu', $G_MENU_SELECTED, $G_ID_MENU_SELECTED);
      $smarty->assign('menus', $menus);

      $oSubMenu = new Menu();
      $subMenus = $oSubMenu->generateArrayForTemplate($G_SUB_MENU, 'selectedSubMenu', 'subMenu', $G_SUB_MENU_SELECTED, $G_ID_SUB_MENU_SELECTED);
      $smarty->assign('subMenus', $subMenus);

      if (! defined('NO_DISPLAY_USERNAME')) {
        define('NO_DISPLAY_USERNAME', 0);
      }
      if (NO_DISPLAY_USERNAME == 0) {
        $switch_interface = isset($_SESSION['user_experience']) && $_SESSION['user_experience'] == 'SWITCHABLE';

        $smarty->assign('user_logged', (isset($_SESSION['USER_LOGGED'])? $_SESSION['USER_LOGGED'] : ''));
        $smarty->assign('switch_interface', $switch_interface);
        $smarty->assign('switch_interface_label', G::LoadTranslation('ID_SWITCH_INTERFACE'));

        $smarty->assign('userfullname', isset($_SESSION['USR_FULLNAME']) ? htmlentities($_SESSION['USR_FULLNAME'] , ENT_QUOTES, 'UTF-8'): '');
        $smarty->assign('user', isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ')' : '');
        $smarty->assign('rolename', isset($_SESSION['USR_ROLENAME']) ? $_SESSION['USR_ROLENAME'] . '' : '');
        $smarty->assign('pipe', isset($_SESSION['USR_USERNAME']) ? ' | ' : '');
        $smarty->assign('logout', G::LoadTranslation('ID_LOGOUT'));
        $smarty->assign('workspace', defined('SYS_SYS')?SYS_SYS: '');
        $uws = (isset($_SESSION['USR_ROLENAME']) && $_SESSION['USR_ROLENAME'] != '')? strtolower(G::LoadTranslation('ID_WORKSPACE_USING')): G::LoadTranslation('ID_WORKSPACE_USING');
        $smarty->assign('workspace_label', $uws);
        $smarty->assign('udate', G::getformatedDate(date('Y-m-d'), 'M d, yyyy', SYS_LANG));

      }
      if(class_exists('pmLicenseManager')){
        $pmLicenseManagerO = &pmLicenseManager::getSingleton();
        $expireIn          = $pmLicenseManagerO->getExpireIn();
        $expireInLabel     = $pmLicenseManagerO->getExpireInLabel();
        //if($expireIn<=30){
        if($expireInLabel != ""){
          $smarty->assign('msgVer', '<label class="textBlack">'.$expireInLabel.'</label>&nbsp;&nbsp;');
        }
        //}
      }

      if (defined('SYS_SYS')) {
        $logout = '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/login/login';
      }
      else {
        $logout = '/sys/' . SYS_LANG . '/' . SYS_SKIN . '/login/login';
      }

      $smarty->assign('linklogout', $logout);
      $smarty->assign('header', $header);
      $smarty->assign('footer', $footer);
      $smarty->assign('tpl_menu', PATH_TEMPLATE . 'menu.html');
      $smarty->assign('tpl_submenu', PATH_TEMPLATE . 'submenu.html');

      G::LoadClass( 'replacementLogo' );
      $oLogoR = new replacementLogo();

      if(defined("SYS_SYS")){
        $aFotoSelect = $oLogoR->getNameLogo((isset($_SESSION['USER_LOGGED']))?$_SESSION['USER_LOGGED']:'');

        if (is_array($aFotoSelect)) {
          $sFotoSelect   = trim($aFotoSelect['DEFAULT_LOGO_NAME']);
          $sWspaceSelect = trim($aFotoSelect['WORKSPACE_LOGO_NAME']);
        }
      }
      if (class_exists('PMPluginRegistry')) {
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        if ( isset($sFotoSelect) && $sFotoSelect!='' && !(strcmp($sWspaceSelect, SYS_SYS)) ){
          $sCompanyLogo = $oPluginRegistry->getCompanyLogo($sFotoSelect);
          $sCompanyLogo = "/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/setup/showLogoFile.php?id=".base64_encode($sCompanyLogo);
        }
        else {
          $sCompanyLogo = $oPluginRegistry->getCompanyLogo('/images/processmaker.logo.jpg');
        }
      }
      else {
        $sCompanyLogo = '/images/processmaker.logo.jpg';
      }

      $smarty->assign('logo_company', $sCompanyLogo);
      $smarty->force_compile = $this->forceTemplateCompile;

      $smarty->display($this->layoutFile['basename']);
    }
  }

}

