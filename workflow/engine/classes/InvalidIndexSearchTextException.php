<?php
/**
 * 
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2012 Colosa Inc.23
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
 * For more information, contact Colosa Inc, 5304 Ventura Drive,
 * Delray Beach, FL, 33484, USA, or email info@colosa.com.
 * 
 */

require_once "classes/model/Application.php";
require_once "classes/model/AppDelegation.php";
require_once "classes/model/AppThread.php";
require_once "classes/model/Content.php";
require_once "classes/model/Users.php";
require_once "classes/model/GroupUser.php";
require_once "classes/model/Task.php";
require_once "classes/model/TaskUser.php";
require_once "classes/model/Dynaform.php";
require_once "classes/model/ProcessVariables.php";
require_once "entities/SolrRequestData.php";
require_once "entities/SolrUpdateDocument.php";
require_once "entities/AppSolrQueue.php";
require_once "classes/model/AppSolrQueue.php";


/**
 * Invalid search text for Solr exception
 *
 * @author Herbert Saal Gutierrez
 *        
 */

/**
 * Invalid search text for Solr exception
 *
 * @author Herbert Saal Gutierrez
 *        
 */class InvalidIndexSearchTextException extends Exception
{
  // Redefine the exception so message isn't optional
  public function __construct($message, $code = 0)
  {
    // some code
    // make sure everything is assigned properly
    parent::__construct ($message, $code);
  }
  
  // custom string representation of object
  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
