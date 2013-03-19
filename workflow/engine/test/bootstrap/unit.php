<?php
/**
 * unit.php
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
  ini_set('short_open_tag', 'on');
  ini_set('asp_tags', 'on');
  ini_set('memory_limit', '80M');

    if ( PHP_OS == 'WINNT' ) 
      define('PATH_SEP', '\\');
    else
      define('PATH_SEP', '/');

      
    //***************** Defining the Home Directory *********************************
    $docuroot =  explode ( PATH_SEP , $_SERVER['PWD'] );
    array_pop($docuroot);
    $pathhome = implode( PATH_SEP, $docuroot );
    define('PATH_HOME', $pathhome . PATH_SEP );
    $gulliverConfig =  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';  
    $definesConfig =  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php';  

    //try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
    //in a normal installation you don't need to change it.
    array_pop($docuroot);
    $pathTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
    array_pop($docuroot);
    $pathOutTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
    // to do: check previous algorith for Windows  $pathTrunk = "c:/home/"; 

    define('PATH_TRUNK', $pathTrunk );
    define('PATH_OUTTRUNK', $pathOutTrunk );
       
    if (file_exists( $gulliverConfig ))   {
      include ( $gulliverConfig );
    }
 
    if (file_exists( $definesConfig ))   {
      include ( $definesConfig );
    }
 
  //$_test_dir = realpath(dirname(__FILE__).'/..');
  //require_once( 'lime/lime.php');

  require_once (PATH_GULLIVER . "class.bootstrap.php");
  spl_autoload_register(array('Bootstrap', 'autoloadClass'));
  Bootstrap::registerClass('G', PATH_GULLIVER . "class.g.php");
  Bootstrap::registerClass('System',        PATH_HOME . "engine/classes/class.system.php");
// Call more Classes
  Bootstrap::registerClass('headPublisher', PATH_GULLIVER . "class.headPublisher.php");
  Bootstrap::registerClass('publisher', PATH_GULLIVER . "class.publisher.php");
  Bootstrap::registerClass('xmlform', PATH_GULLIVER . "class.xmlform.php");
  Bootstrap::registerClass('XmlForm_Field', PATH_GULLIVER . "class.xmlform.php");
  Bootstrap::registerClass('xmlformExtension', PATH_GULLIVER . "class.xmlformExtension.php");
  Bootstrap::registerClass('form',         PATH_GULLIVER . "class.form.php");
  Bootstrap::registerClass('menu',         PATH_GULLIVER . "class.menu.php");
  Bootstrap::registerClass('Xml_Document', PATH_GULLIVER . "class.xmlDocument.php");
  Bootstrap::registerClass('DBSession',    PATH_GULLIVER . "class.dbsession.php");
  Bootstrap::registerClass('DBConnection', PATH_GULLIVER . "class.dbconnection.php");
  Bootstrap::registerClass('DBRecordset',  PATH_GULLIVER . "class.dbrecordset.php");
  Bootstrap::registerClass('DBTable',      PATH_GULLIVER . "class.dbtable.php");
  Bootstrap::registerClass('xmlMenu',      PATH_GULLIVER . "class.xmlMenu.php");
  Bootstrap::registerClass('XmlForm_Field_FastSearch', PATH_GULLIVER . "class.xmlformExtension.php");
  Bootstrap::registerClass('XmlForm_Field_XmlMenu', PATH_GULLIVER . "class.xmlMenu.php");
  Bootstrap::registerClass('XmlForm_Field_HTML',  PATH_GULLIVER . "class.dvEditor.php");
  Bootstrap::registerClass('XmlForm_Field_WYSIWYG_EDITOR',  PATH_GULLIVER . "class.wysiwygEditor.php");
  Bootstrap::registerClass('Controller',          PATH_GULLIVER . "class.controller.php");
  Bootstrap::registerClass('HttpProxyController', PATH_GULLIVER . "class.httpProxyController.php");
  Bootstrap::registerClass('templatePower',            PATH_GULLIVER . "class.templatePower.php");
  Bootstrap::registerClass('XmlForm_Field_SimpleText', PATH_GULLIVER . "class.xmlformExtension.php");
  Bootstrap::registerClass('Propel',          PATH_THIRDPARTY . "propel/Propel.php");
  Bootstrap::registerClass('Creole',          PATH_THIRDPARTY . "creole/Creole.php");
  Bootstrap::registerClass('Criteria',        PATH_THIRDPARTY . "propel/util/Criteria.php");
  Bootstrap::registerClass('Groups',       PATH_HOME . "engine/classes/class.groups.php");
  Bootstrap::registerClass('Tasks',        PATH_HOME . "engine/classes/class.tasks.php");
  Bootstrap::registerClass('Calendar',     PATH_HOME . "engine/classes/class.calendar.php");
  Bootstrap::registerClass('processMap',   PATH_HOME . "engine/classes/class.processMap.php");

  Bootstrap::registerSystemClasses();
  
  require_once( PATH_THIRDPARTY . 'pake' . PATH_SEP . 'pakeFunction.php');
  require_once( PATH_THIRDPARTY . 'pake' . PATH_SEP . 'pakeGetopt.class.php');
  require_once( PATH_CORE . 'config' . PATH_SEP . 'environments.php');

  if ( !defined ( 'G_ENVIRONMENT') )
    define ( 'G_ENVIRONMENT', G_TEST_ENV );

