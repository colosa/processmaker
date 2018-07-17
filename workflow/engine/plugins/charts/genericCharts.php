<?php
/**
 * dashboard.php
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
  //if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;

  require_once ( "class.charts.php" );




  header("Content-type: image/png");
  //type of chart, pie, vertical bar, horizontal, etc.
  $type      =  isset ( $_GET['type'])  ?  $_GET['type']  : '1' ;
  $chartType =  isset ( $_GET['chart']) ?  $_GET['chart']  : '1' ;
  $user      =  isset ( $_GET['user']) ?  $_GET['user']  : $_SESSION['USER_LOGGED'] ;
  $chartsObj = new chartsClass();

  //$chart = new PieChart(450,300);
  switch ( $type ) {
  	case '1' : 
      $chart = new VerticalBarChart(430, 280); break;
  	case '2' : 
      $chart = new HorizontalBarChart(430, 200); break;
  	case '3' : 
      $chart = new LineChart(430, 280); break;
  	case '4' : 
      $chart = new PieChart(430, 200 ); break;
  }

  switch ( $chartType ) {
  	case '1' :
      $dataSet = $chartsObj->getDatasetCasesByStatus(); break;
    default : 
      $dataSet = $chartsObj->getDatasetCasesByProcess(); break;
  }
  $chart->setDataSet($dataSet);
  $chart->setTitle( "Cases list" );
  $chart->render();

