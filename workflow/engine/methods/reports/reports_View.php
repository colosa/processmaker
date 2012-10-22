<?php
/**
 * reports_View.php
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

/**
 * Report - Report view
 *
 * @package ProcessMaker
 * @author Everth S. Berrios Morales
 * @copyright 2008 COLOSA
 */

global $RBAC;
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

try {

    //form type format hours in the form xml
    G::LoadClass( 'xmlfield_InputPM' );

    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'REPORTS';

    $RPT_UID = $_GET['RPT_UID'];

    switch ($RPT_UID) {
        case 1:
            $sw = 0;
            if (isset( $_POST['form'] )) {
                if ($_POST['form']['FROM'] != '0000-00-00' || $_POST['form']['TO'] != '0000-00-00') {
                    $sw = 1;
                }
                $fields['FROM'] = $_POST['form']['FROM'];
                $fields['TO'] = $_POST['form']['TO'];
                $fields['STARTEDBY'] = $_POST['form']['STARTEDBY'];
            } else {
                $fields['FROM'] = date( 'Y-m-d' );
                $fields['TO'] = date( 'Y-m-d' );
            }

            G::LoadClass( 'report' );
            $oReport = new Report();
            if ($sw == 0) {
                $c = $oReport->generatedReport1();
            } else {
                $c = $oReport->generatedReport1_filter( $_POST['form']['FROM'], $_POST['form']['TO'], $_POST['form']['STARTEDBY'] );
            }
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptFile( '/jscore/reports/reports.js' );

            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report1', $c );

            if (isset( $_POST['form'] )) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report1_search', '', $fields );
            } else {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report1_search' );
            }
            G::RenderPage( 'publish' );
            break;
        case 2:
            $sw = 0;
            if (isset( $_POST['form'] )) {
                if ($_POST['form']['FROM'] != '0000-00-00' || $_POST['form']['TO'] != '0000-00-00') {
                    $sw = 1;
                }
                $fields['FROM'] = $_POST['form']['FROM'];
                $fields['TO'] = $_POST['form']['TO'];
                $fields['STARTEDBY'] = $_POST['form']['STARTEDBY'];
            } else {
                $fields['FROM'] = date( 'Y-m-d' );
                $fields['TO'] = date( 'Y-m-d' );
            }

            G::LoadClass( 'report' );
            $oReport = new Report();

            if ($sw == 0) {
                $c = $oReport->generatedReport2();
            } else {
                $c = $oReport->generatedReport2_filter( $_POST['form']['FROM'], $_POST['form']['TO'], $_POST['form']['STARTEDBY'] );
            }
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptFile( '/jscore/reports/reports.js' );

            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report2', $c );

            if (isset( $_POST['form'] )) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report1_search', '', $fields );
            } else {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report1_search' );
            }
            G::RenderPage( 'publish' );
            break;
        case 3:
            $sw = 0;
            if (isset( $_POST['form'] )) {
                $sw = 1;
                $fields['PROCESS'] = $_POST['form']['PROCESS'];
                $fields['TASKS'] = $_POST['form']['TASKS'];
            } else {
                $fields['FROM'] = date( 'Y-m-d' );
                $fields['TO'] = date( 'Y-m-d' );
            }

            G::LoadClass( 'report' );
            $oReport = new Report();

            if ($sw == 0) {
                $c = $oReport->generatedReport3();
            } else {
                $c = $oReport->generatedReport3_filter( $_POST['form']['PROCESS'], $_POST['form']['TASKS'] );
            }
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptFile( '/jscore/reports/reports.js' );
            $G_PUBLISH = new Publisher();

            if (isset( $_POST['form'] )) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter', '', $fields );
            } else {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter' );
            }
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report3', $c );

            G::RenderPage( 'publish' );
            break;
        case 4:
            $sw = 0;
            if (isset( $_POST['form'] )) {
                $sw = 1;
                $fields['PROCESS'] = $_POST['form']['PROCESS'];
                $fields['TASKS'] = $_POST['form']['TASKS'];
            }

            G::LoadClass( 'report' );
            $oReport = new Report();

            if ($sw == 0) {
                $c = $oReport->generatedReport4();
            } else {
                $c = $oReport->generatedReport4_filter( $_POST['form']['PROCESS'], $_POST['form']['TASKS'] );
            }
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptFile( '/jscore/reports/reports.js' );
            $G_PUBLISH = new Publisher();

            if (isset( $_POST['form'] )) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter', '', $fields );
            } else {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter' );
            }
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report4', $c );

            G::RenderPage( 'publish' );
            break;
        case 5:
            $sw = 0;
            if (isset( $_POST['form'] )) {
                $sw = 1;
                $fields['PROCESS'] = $_POST['form']['PROCESS'];
                $fields['TASKS'] = $_POST['form']['TASKS'];
            }

            G::LoadClass( 'report' );
            $oReport = new Report();

            if ($sw == 0) {
                $c = $oReport->generatedReport5();
            } else {
                $c = $oReport->generatedReport5_filter( $_POST['form']['PROCESS'], $_POST['form']['TASKS'] );
            }
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptFile( '/jscore/reports/reports.js' );
            $G_PUBLISH = new Publisher();

            if (isset( $_POST['form'] )) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter', '', $fields );
            } else {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reports/report_filter' );
            }
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report5', $c );

            G::RenderPage( 'publish' );
            break;
        default:
            $foundReport = false;
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $aAvailableReports = $oPluginRegistry->getReports();
            foreach ($aAvailableReports as $sReportClass) {

                require_once PATH_PLUGINS . $sReportClass . PATH_SEP . 'class.' . $sReportClass . '.php';
                $sClassName = $sReportClass . 'Class';
                $oInstance = new $sClassName();
                $aReports = $oInstance->getAvailableReports();
                foreach ($aReports as $oReport) {
                    if ($RPT_UID == $oReport['uid'] && method_exists( $oInstance, $RPT_UID )) {
                        $foundReport = true;
                        $result = $oInstance->{$RPT_UID}();

                    }
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
                                $oInstance = new $className();
                                $aReports = $oInstance->getAvailableReports();
                                foreach ($aReports as $oReport) {
                                    if ($RPT_UID == $oReport['uid'] && method_exists( $oInstance, $RPT_UID )) {
                                        $foundReport = true;
                                        $result = $oInstance->{$RPT_UID}();
                                    }
                                }
                            }
                        }
                    }
                }
                closedir( $handle );
            }
            if (! $foundReport) {
                throw (new Exception( "Call to an nonexistent member function " . $RPT_UID . "() " ));
            }
    }

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}

