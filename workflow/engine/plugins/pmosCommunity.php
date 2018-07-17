<?php

 class pmosCommunityPlugin extends PMPlugin
 {
    function pmosCommunityPlugin($sNamespace, $sFilename = null)
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = 'PMOS Community Plugin';
        $this->sDescription  = 'Community Charts Plugin, with this plugin you can see many differents charts related to ProcessMaker Open Source Community';
        $this->sPluginFolder = 'pmosCommunity';
        $this->sSetupPage    = 'setupPage';
        $this->iVersion = 0.45;
        $this->aWorkspaces = array ( 'os' );
        return $res;
    }

    function setup()
    {
      //$this->registerTrigger( 10000, 'createCaseFolder' );
      $this->registerDashboard();
    }

    function install()
    {

    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('pmosCommunity', __FILE__);





