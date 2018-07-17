<?php
/**
 * showDBFiles.php
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
	$path = PATH_DB;
  //using the opendir function
  if ( ! $dir_handle = @opendir( PATH_DB ))
  {
    header ("location: /errors/error704.php");
    die;
  }


  $filter = new InputFilter();
  
  echo "<table class='basicTable' cellpadding='5' cellspacing='0' border='0'>";
  echo "<tr class='Record'><td colspan='2' class='formTitle'>Please select a valid workspace to continue</td></tr>";
  echo "<tr valign='top'>";
  $curPage = getenv( "REQUEST_URI" );
  $curPage = $filter->xssFilterHard($curPage,"url");
  //running the while loop
  $first = 0;
  while ($file = readdir($dir_handle))
  {
  	if ( substr($file,0,3) == 'db_' ) {
      if ( $first == 0 ) {
       echo "<td><table class='Record' ><tr class='formLabel''><td>RBAC built-in workspaces</td></tr>";
       $first = 1;
      }
      $name = substr ( substr($file,0, strlen($file)-4) , 3 );
      $link = str_replace ( "/sys/", "/sys$name/" , $curPage );
     echo "<tr><td><li><a href='$link'>$name</a></td></tr>";
    }
  }
  //closing the directory
  closedir($dir_handle);
  if ( $first != 0 ) print "</table><br></td>";

  $second = 0;
  if ( ! $dir_handle = @opendir( PATH_DB ))
  {
    header ("location: /errors/error704.php");
    die;
  }

  $DB_INDEX = 0;
  $DB_MODULE = array();
  
  while ($file = readdir($dir_handle))
  {
  	if ( substr($file,0,9) == 'dbmodule_' ) {
      $module = substr ( substr($file,0, strlen($file)-4) , 9 );
      require_once ( PATH_DB . $file );
      $moduleName = $DB_MODULE[$DB_INDEX]['name'];
      echo "<td><table class='Record' style='width:200px'><tr class='formLabel'><td>RBAC Module : $moduleName</td></tr>";

      if ( $DB_MODULE[$DB_INDEX]['type'] == 'single-file' ) {
        $third = 0;
        if ( ! $module_handle = @opendir( $DB_MODULE[$DB_INDEX]['path'] )) {
          echo ( 'error in this path ' . $DB_MODULE[$DB_INDEX]['path']  );
        }
        else {
          while ($moduleFile = readdir($module_handle)) {
          	if ( substr($moduleFile,0,3) == 'db_' ) {
              $name = substr ( substr($moduleFile,0, strlen($moduleFile)-4) , 3 );
              $link = str_replace ( "/sys/", "/sys-$module-$name/" , $curPage );
              echo "<tr class='formLabel'><td><li><a href='$link'>$name</a></td></tr>";
            }
          }
        }
      }
      else {
        $third = 0;
        if ( ! $module_handle = @opendir( $DB_MODULE[$DB_INDEX]['path'] )) {
          echo ( "<tr><td><font color='red'>Path invalid: " . $DB_MODULE[$DB_INDEX]['path'] ."</font></td></tr>" );
        }
        else {
          while ($moduleFile = readdir($module_handle)) {
            $dbFile = $DB_MODULE[$DB_INDEX]['path'] . $moduleFile . '/db.php';
          	if ( file_exists ($dbFile) && substr($moduleFile,0,1) != '.' ) {
              $name = $moduleFile;
              $link = str_replace ( "/sys/", "/sys-$module-$name/" , $curPage );
              echo "<tr class='formField'><td><li><a href='$link'>$name</a></td></tr>";
            }
          }
        }
      }
      print "</table><br></td>";
    }
  }
  //closing the directory
  closedir($dir_handle);

  print "</table>";

?>