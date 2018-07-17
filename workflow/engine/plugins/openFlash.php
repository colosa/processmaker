<?php
  
 class openFlashPlugin extends PMPlugin 
 {
    function openFlashPlugin($sNamespace, $sFilename = null) 
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = 'openFlash Plugin';
        $this->sDescription  = 'Charts Plugin, with this plugin you can see many differents charts using interactive flash charts for ProcessMaker';
        $this->sPluginFolder = 'openFlash';
        $this->sSetupPage    = 'setupPage';
        $this->aWorkspaces = array (  );
        $this->aWorkspaces = array ( 'dev');
        $this->iVersion = 0.45;
        return $res;
    }

    function setup()
    {
      $this->registerDashboard();
    }

    function install()
    {
      
    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('openFlash', __FILE__);



  
  
