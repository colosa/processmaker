<?php

/**
 * tracker_ViewMap.php
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
 * Map for Case Tracker
 *
 * @author Everth S. Berrios Morales <everth@colosa.com>
 *
 */
require_once 'classes/model/Process.php';
if (! isset( $_SESSION['PROCESS'] )) {
    G::header( 'location: login' );
}
$G_MAIN_MENU = 'caseTracker';
$G_ID_MENU_SELECTED = 'MAP';

require_once 'classes/model/CaseTracker.php';
$oCaseTracker = new CaseTracker();
$aCaseTracker = $oCaseTracker->load( $_SESSION['PROCESS'] );

$idProcess = $_SESSION['PROCESS'];
$oProcess = new Process();
$aProcessFieds = $oProcess->load( $idProcess );
$noShowTitle = 0;
if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
    $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
}
switch (($aCaseTracker['CT_MAP_TYPE'])) {
    case 'NONE':
        //Nothing
        break;
    case 'PROCESSMAP':
        G::LoadClass( 'case' );
        G::LoadClass( 'processMap' );
        $oCase = new Cases();
        $aFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
        if (isset( $aFields['TITLE'] )) {
            $aFields['APP_TITLE'] = $aFields['TITLE'];
        }
        if ($aFields['APP_PROC_CODE'] != '') {
            $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
        }
        $aFields['CASE'] = G::LoadTranslation( 'ID_CASE' );
        $aFields['TITLE'] = G::LoadTranslation( 'ID_TITLE' );
        $oTemplatePower = new TemplatePower( PATH_TPL . 'processes/processes_Map.html' );
        $oTemplatePower->prepare();
        $G_PUBLISH = new Publisher();
        if ($noShowTitle == 0) {
            $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $aFields );
        }
        $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptCode( '
        var maximunX = ' . processMap::getMaximunTaskX( $_SESSION['PROCESS'] ) . ';
        leimnud.event.add(window,"load",function(){
          var pb = leimnud.dom.capture("tag.body 0");
          pm = new processmap();
          pm.options = {
            target    : "pm_target",
            dataServer: "../processes/processes_Ajax",
            uid       : "' . $_SESSION['PROCESS'] . '",
            lang      : "' . SYS_LANG . '",
            theme     : "processmaker",
            size      : {w:pb.offsetWidth-10,h:pb.offsetHeight},
            images_dir: "/jscore/processmap/core/images/",
            rw        : false,
            mi        : false,
            ct        : true,
            hideMenu  : false
          }

          pm.make();

          ///////
          var pnlLegend = new leimnud.module.panel();

          pnlLegend.options = {
            size: {w: 260, h: 140},
            position: {
              x: ((document.body.clientWidth * 95) / 100) - ((document.body.clientWidth * 95) / 100 - (((document.body.clientWidth * 95) / 100) - 260)),
              y: 175,
              center: false
            },
            title: G_STRINGS.ID_COLOR_LEYENDS,
            theme: "processmaker",
            statusBar: false,
            control: {resize: false, roll: false, drag: true, close: false},
            fx: {modal: false, opacity: false, blinkToFront: true, fadeIn: false, drag: false}
          };

          pnlLegend.setStyle = {
            content: {overflow: "hidden"}
          };

          pnlLegend.events = {
            remove: function () { delete(pnlLegend); }.extend(this)
          };

          pnlLegend.make();
          pnlLegend.loader.show();

          ///////
          var rpcRequest = new leimnud.module.rpc.xmlhttp({
            url : "tracker_Ajax",
            args: "action=processMapLegend"
          });

          rpcRequest.callback = function (rpc) {
            pnlLegend.loader.hide();
            pnlLegend.addContent(rpc.xmlhttp.responseText);
          }.extend(this);

          rpcRequest.make();
        });' );
        G::RenderPage( 'publish' );
        break;
    case 'STAGES':
        G::LoadClass( 'case' );
        $oCase = new Cases();
        $aFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
        if (isset( $aFields['TITLE'] )) {
            $aFields['APP_TITLE'] = $aFields['TITLE'];
        }
        if ($aFields['APP_PROC_CODE'] != '') {
            $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
        }
        $aFields['CASE'] = G::LoadTranslation( 'ID_CASE' );
        $aFields['TITLE'] = G::LoadTranslation( 'ID_TITLE' );
        $oTemplatePower = new TemplatePower( PATH_TPL . 'tracker/stages_Map.html' );
        $oTemplatePower->prepare();
        $G_PUBLISH = new Publisher();
        if ($noShowTitle == 0) {
            $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $aFields );
        }
        $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptCode( '
        leimnud.Package.Load("stagesmap",{Type:"file",Absolute:true,Path:"/jscore/stagesmap/core/stagesmap.js"});
        leimnud.event.add(window,"load",function(){
          var pb=leimnud.dom.capture("tag.body 0");
          Sm=new stagesmap();
          Sm.options = {
            target    : "sm_target",
            dataServer: "../tracker/tracker_Ajax",
            uid       : "' . $_SESSION['PROCESS'] . '",
            lang      : "' . SYS_LANG . '",
            theme     : "processmaker",
            size      : {w:"780",h:"540"},
            //size    : {w:pb.offsetWidth-10,h:pb.offsetHeight},
            images_dir: "/jscore/processmap/core/images/",
            rw        : false,
            hideMenu  : false
          };
          Sm.make();
        });' );
        G::RenderPage( 'publish' );
        break;
}

