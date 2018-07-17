<?php

/**
 * tracker_Messages.php
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
/*
 * History messages for Case Tracker
 *
 * @author Everth S. Berrios Morales <everth@colosa.com>
 *
 */
if (! isset( $_SESSION['PROCESS'] ) || ! isset( $_SESSION['APPLICATION'] )) {
    G::header( 'location: login' );
    die;
}
$G_MAIN_MENU = 'caseTracker';
$G_ID_MENU_SELECTED = 'MESSAGES';

$oHeadPublisher->addScriptFile( '/jscore/tracker/tracker.js' );

$oCase = new Cases();
$aFields = $oCase->loadCase( $_SESSION['APPLICATION'] );

$idProcess = $_SESSION['PROCESS'];
$oProcess = new Process();
$aProcessFieds = $oProcess->load( $idProcess );
$noShowTitle = 0;
if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
    $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
}

if (isset( $aFields['TITLE'] )) {
    $aFields['APP_TITLE'] = $aFields['TITLE'];
}
if ($aFields['APP_PROC_CODE'] != '') {
    $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
}
$aFields['CASE'] = G::LoadTranslation( 'ID_CASE' );
$aFields['TITLE'] = G::LoadTranslation( 'ID_TITLE' );

$G_PUBLISH = new Publisher();
if ($noShowTitle == 0) {
    $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $aFields );
}
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_Messages', Cases::getHistoryMessagesTracker( $_SESSION['APPLICATION'] ), array ('VIEW' => G::LoadTranslation( 'ID_VIEW' )
) );

$bpmn = new ProcessMaker\Project\Bpmn();
$flagIsBpmn = ($bpmn->exists($_SESSION["PROCESS"]))? true : false;

if ($flagIsBpmn) {
    $urlTrackerProcessMap = "../designer?prj_uid=" . $_SESSION["PROCESS"] . "&prj_readonly=true&app_uid=" . $_SESSION["APPLICATION"] . "&tracker_designer=1";

    $_SESSION["TRACKER_JAVASCRIPT"] = "
        <script type=\"text/javascript\">
            var winTracker;

            if ((navigator.userAgent.indexOf(\"MSIE\") != -1) || (navigator.userAgent.indexOf(\"Trident\") != -1)) {
                var li1 = document.getElementById(\"MAP\");
                var a1 = li1.getElementsByTagName(\"a\");
                a1[0].onclick = function () {
                    winTracker = window.open(\"$urlTrackerProcessMap\", \"winTracker\");
                    li1.className = \"SelectedMenu\";
                    li2.className = \"mainMenu\";
                    li3.className = \"mainMenu\";
                    li4.className = \"mainMenu\";
                    document.getElementById(\"trackerContainer\").innerHTML = \"\";
                    
                    return false;
                };

                var li2 = document.getElementById(\"DYNADOC\");
                var a2= li2.getElementsByTagName(\"a\");
                a2[0].onclick = function () { if (winTracker) { winTracker.close(); } };

                var li3 = document.getElementById(\"HISTORY\");
                var a3 = li3.getElementsByTagName(\"a\");
                a3[0].onclick = function () { if (winTracker) { winTracker.close(); } };

                var li4 = document.getElementById(\"MESSAGES\");
                var a4 = li4.getElementsByTagName(\"a\");
                a4[0].onclick = function () { if (winTracker) { winTracker.close(); } };
            }
        </script>
    ";
}

G::RenderPage("publish");
