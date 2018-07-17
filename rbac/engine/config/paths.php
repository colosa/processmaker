<?php
/**
 * paths.php
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

//****** Basic defines **** //
//  define('PATH_HOME',     $pathhome . PATH_SEP );
//  define('PATH_TRUNK',    $pathTrunk . PATH_SEP );
//  define('PATH_OUTTRUNK', $pathOutTrunk . PATH_SEP );

//***************** System Directories & Paths **************************

  //***************** RBAC Paths **************************
  define( 'PATH_RBAC_HOME',     PATH_TRUNK . 'rbac' . PATH_SEP );

//***************** GULLIVER Paths **************************
  define( 'PATH_GULLIVER_HOME', PATH_TRUNK . 'gulliver'  . PATH_SEP );
  define( 'PATH_GULLIVER',      PATH_GULLIVER_HOME . 'system' . PATH_SEP );   //gulliver system classes
  define( 'PATH_GULLIVER_BIN',  PATH_GULLIVER_HOME . 'bin' . PATH_SEP );   //gulliver bin classes
  define( 'PATH_TEMPLATE',      PATH_GULLIVER_HOME . 'templates' . PATH_SEP );
  define( 'PATH_THIRDPARTY',    PATH_TRUNK . 'thirdparty' . PATH_SEP );

  define( 'PATH_RBAC',          PATH_RBAC_HOME .     'engine'  . PATH_SEP . 'classes' . PATH_SEP );  //to enable rbac version 2

  define( 'PATH_HTML',          PATH_HOME .          'public_html' . PATH_SEP );

//***************** PM Paths CORE **************************
  define( 'PATH_CORE',        PATH_HOME . 'engine'       . PATH_SEP );
  define( 'PATH_SKINS',       PATH_CORE . 'skins'        . PATH_SEP );
  define( 'PATH_METHODS',     PATH_CORE . 'methods'      . PATH_SEP );
  define( 'PATH_XMLFORM',     PATH_CORE . 'xmlform'      . PATH_SEP );
  define( 'PATH_PLUGINS',     PATH_CORE . 'plugins'      . PATH_SEP  );
  define( 'PATH_HTMLMAIL',    PATH_CORE . 'html_templates' . PATH_SEP );
  define( 'PATH_TPL',         PATH_CORE . 'templates'    . PATH_SEP );
  define( 'PATH_TEST',        PATH_CORE . 'test'         . PATH_SEP );
  define( 'PATH_FIXTURES',    PATH_TEST . 'fixtures'     . PATH_SEP );
  define( 'PATH_RTFDOCS' ,    PATH_CORE . 'rtf_templates' . PATH_SEP );
  define( 'PATH_DYNACONT',    PATH_CORE . 'content' . PATH_SEP . 'dynaform' . PATH_SEP );
  define( 'PATH_LANGUAGECONT',PATH_CORE . 'content' . PATH_SEP . 'languages' . PATH_SEP );
  define( 'SYS_UPLOAD_PATH',  PATH_HOME . "public_html/files/" );
  define( 'PATH_UPLOAD',      PATH_HTML . 'files' . PATH_SEP);
  define( 'PATH_DATA',        '/shared/workflow_data'  . PATH_SEP);
  define( 'PATH_DB'    ,      PATH_DATA . 'sites' . PATH_SEP );

  // the Compiled Directories
  define( 'PATH_C',          PATH_OUTTRUNK . 'compiled' . PATH_SEP);

//***************** set include path  ***********************
  set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear'. PATH_SEPARATOR .
    get_include_path()
  );

//************ include Gulliver Class **************
 require_once( PATH_GULLIVER . PATH_SEP . 'class.g.php');
//************ the Smarty Directories **************
    define( 'PATH_SMARTY_C',       PATH_C . 'smarty' . PATH_SEP . 'c' );
    define( 'PATH_SMARTY_CACHE',   PATH_C . 'smarty' . PATH_SEP . 'cache' );
  if (!is_dir(PATH_SMARTY_C)) G::mk_dir(PATH_SMARTY_C);
  if (!is_dir(PATH_SMARTY_CACHE)) G::mk_dir(PATH_SMARTY_CACHE);

G::defineConstants();
