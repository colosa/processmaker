<?php 
/**
 * processTemplate.php
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

 class processTemplatePlugin extends PMPlugin 
 {
    function processTemplatePlugin($sNamespace, $sFilename = null) 
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = 'Process Map Templates';
        $this->sDescription  = 'This plugin includes various templates for quick and easy Process Map creation. Users can customize Process Maps based on pre-defined templates of common process designs (including Parallel, Dual Start Task, and Selection).';
        $this->sPluginFolder = 'processTemplate';
        $this->sSetupPage    = null;
        $this->iVersion      = 0.78;
        $this->bPrivate      = true;
        $this->aWorkspaces   = array ( '__' );
        return $res;
    }

    function setup()
    {
      //$this->registerTrigger( PM_NEW_PROCESS_LIST, 'getNewProcessTemplateList' );
      //$this->registerTrigger( PM_NEW_PROCESS_SAVE, 'saveNewProcess' );
      //$this->registerTrigger( PM_NEW_DYNAFORM_LIST, 'getNewDynaformTemplateList' );
      //$this->registerTrigger( PM_NEW_DYNAFORM_SAVE, 'saveNewDynaform' );
    }
    
    function install()
    {
      
    }
    function enable(){
      
    }
    function disable(){
      
    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('processTemplate', __FILE__);



  
  
