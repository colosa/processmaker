<?php
/**
 * reportsList.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
global $RBAC;

use ProcessMaker\Plugins\PluginRegistry;

switch ($RBAC->userCanAccess( 'PM_REPORTS' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'REPORTS';

$reports = array ();

$reports[] = array ('RPT_UID' => '','RPT_TITLE' => '','VIEW' => ''
);

$reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => 1,'RPT_TITLE' => G::LoadTranslation( 'ID_REPORT1' ),'VIEW' => G::LoadTranslation( 'ID_VIEW' )
);

$reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => 2,'RPT_TITLE' => G::LoadTranslation( 'ID_REPORT2' ),'VIEW' => G::LoadTranslation( 'ID_VIEW' )
);

$reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => 3,'RPT_TITLE' => G::LoadTranslation( 'ID_REPORT3' ),'VIEW' => G::LoadTranslation( 'ID_VIEW' )
);

$reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => 4,'RPT_TITLE' => G::LoadTranslation( 'ID_REPORT4' ),'VIEW' => G::LoadTranslation( 'ID_VIEW' )
);

$reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => 5,'RPT_TITLE' => G::LoadTranslation( 'ID_REPORT5' ),'VIEW' => G::LoadTranslation( 'ID_VIEW' )
);

/*$reports[] = array('RPT_UID'   => 6,
      	                'RPT_TITLE' => "Report 6",//G::LoadTranslation('ID_REPORT6'),
      	                'VIEW'  => G::LoadTranslation('ID_VIEW'));

     $reports[] = array('RPT_UID'   => 7,
      	                'RPT_TITLE' => "Report 7",//G::LoadTranslation('ID_REPORT6'),
      	                'VIEW'  => G::LoadTranslation('ID_VIEW'));

     $reports[] = array('RPT_UID'   => 8,
      	                'RPT_TITLE' => "Report 8",//G::LoadTranslation('ID_REPORT6'),
      	                'VIEW'  => G::LoadTranslation('ID_VIEW'));

     $reports[] = array('RPT_UID'   => 9,
      	                'RPT_TITLE' => "Report 9",//G::LoadTranslation('ID_REPORT6'),
      	                'VIEW'  => G::LoadTranslation('ID_VIEW'));*/

$oPluginRegistry = PluginRegistry::loadSingleton();
$aAvailableReports = $oPluginRegistry->getReports();

//$aReports = array();
foreach ($aAvailableReports as $sReportClass) {

    require_once PATH_PLUGINS . $sReportClass . PATH_SEP . 'class.' . $sReportClass . '.php';
    $sClassName = $sReportClass . 'Class';
    $oInstance = new $sClassName();
    $aReports = $oInstance->getAvailableReports();
    foreach ($aReports as $oReport) {
        $reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => $oReport['uid'],'RPT_TITLE' => $oReport['title'],'VIEW' => G::LoadTranslation( 'ID_VIEW' )
        );
    }
}

//now check if there are customized reports inside the processes
if (file_exists( PATH_DATA_PUBLIC ) && is_dir( PATH_DATA_PUBLIC )) {
    if ($handle = opendir( PATH_DATA_PUBLIC )) {
        while (false !== ($dir = readdir( $handle ))) {
            if ($dir[0] != '.' && file_exists( PATH_DATA_PUBLIC . $dir . PATH_SEP . 'reports.php' )) {
                include_once (PATH_DATA_PUBLIC . $dir . PATH_SEP . 'reports.php');
                $className = 'report' . $dir;
                if (class_exists( $className )) {
                    $oReport = new $className();
                    $aReports = $oReport->getAvailableReports();
                    foreach ($aReports as $oReport) {
                        $reports[] = array ('RPT_NUMBER' => count( $reports ),'RPT_UID' => $oReport['uid'],'RPT_TITLE' => $oReport['title'],'VIEW' => G::LoadTranslation( 'ID_VIEW' )
                        );
                    }
                }
            }
        }
    }
    closedir( $handle );
}

global $_DBArray;
$_DBArray['reports'] = $reports;

$_SESSION['_DBArray'] = $_DBArray;

$oCriteria = new Criteria( 'dbarray' );
$oCriteria->setDBArrayTable( 'reports' );

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/reportsList', $oCriteria );
G::RenderPage( 'publish' );

