<?php
/**
 * cases.php
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
global $RBAC;
global $G_TMP_MENU;

$G_TMP_MENU->AddIdRawOption('FOLDERS', '', G::LoadTranslation('ID_CASES_MENU_FOLDERS'), '', '', 'blockHeader');
$G_TMP_MENU->AddIdRawOption('CASES_START_CASE', 'casesStartPage?action=startCase',
G::LoadTranslation('ID_NEW_CASE'), '');

$G_TMP_MENU->AddIdRawOption('CASES_INBOX', 'casesListExtJs?action=todo', G::LoadTranslation('ID_INBOX'),
    'icon-cases-inbox.png');
$G_TMP_MENU->AddIdRawOption('CASES_DRAFT', 'casesListExtJs?action=draft', G::LoadTranslation('ID_DRAFT'),
    'mail-mark-task.png'  );
$G_TMP_MENU->AddIdRawOption('CASES_SENT', 'casesListExtJs?action=sent', G::LoadTranslation('ID_SENT'),
    'icon-cases-outbox.png');
$G_TMP_MENU->AddIdRawOption('CASES_SELFSERVICE', 'casesListExtJs?action=selfservice',
    G::LoadTranslation('ID_UNASSIGNED'),'rotate_cw.png');
$G_TMP_MENU->AddIdRawOption('CASES_PAUSED', 'casesListExtJs?action=paused', G::LoadTranslation('ID_PAUSED'),
    'mail-queue.png');

if ($RBAC->userCanAccess('PM_ALLCASES') == 1) {
    //$G_TMP_MENU->AddIdRawOption('CASES_GRAL', 'casesListExtJs?action=gral', G::LoadTranslation('ID_GENERAL'));
}

$G_TMP_MENU->AddIdRawOption('SEARCHS', '', G::LoadTranslation('ID_CASES_MENU_SEARCH'), '', '', 'blockHeader');

if ($RBAC->userCanAccess('PM_ALLCASES') == 1) {
    $G_TMP_MENU->AddIdRawOption('CASES_SEARCH', 'casesListExtJs?action=search',
        G::LoadTranslation('ID_ADVANCEDSEARCH'), 'system-search.png'    );
}

$G_TMP_MENU->AddIdRawOption('ADMIN', '', G::LoadTranslation('ID_CASES_MENU_ADMIN'), '', '', 'blockHeader');
if ($RBAC->userCanAccess('PM_SUPERVISOR') == 1) {
    //$G_TMP_MENU->AddIdRawOption('CASES_TO_REVISE_NORMAL', 'cases_List?l=to_revise',
    //G::LoadTranslation('ID_TO_REVISE'), 'document-review.png' );
    $G_TMP_MENU->AddIdRawOption('CASES_TO_REVISE', 'casesListExtJs?action=to_revise',
        G::LoadTranslation('ID_TO_REVISE'), 'document-review.png');
    //$G_TMP_MENU->AddIdRawOption('CASES_SCHEDULER','cases_Scheduler_List',G::LoadTranslation('ID_SCHEDULER_LIST'));
    //$G_TMP_MENU->AddIdRawOption('CASES_SCHEDULER_LOG','cases_Scheduler_Log',G::LoadTranslation('ID_SCHEDULER_LOG'));
}

if ($RBAC->userCanAccess('PM_REASSIGNCASE') == 1) {
    //$G_TMP_MENU->AddIdRawOption('CASES_TO_REASSIGN_NORMAL', 'cases_List?l=to_reassign',
    //G::LoadTranslation('ID_TO_REASSIGN'), 'reassing.png' );
    $G_TMP_MENU->AddIdRawOption('CASES_TO_REASSIGN', 'casesListExtJs?action=to_reassign',
        G::LoadTranslation('ID_TO_REASSIGN'), 'reassing.png' );
}

if ($RBAC->userCanAccess('PM_FOLDERS_VIEW') == 1) {
    $G_TMP_MENU->AddIdRawOption('CASES_FOLDERS', 'casesStartPage?action=documents', G::LoadTranslation('ID_FOLDERS'),
        'folderV2.gif', '', 'blockHeaderNoChild' );
}



//$G_TMP_MENU->AddIdRawOption('CASES_START_PAGE', 'casesStartPage?action=mainDashboard',
//ucwords(strtolower(G::LoadTranslation('ID_DASHBOARD'))),'');

//Load Other registered Dashboards (From plugins)
$oPluginRegistry = & PMPluginRegistry::getSingleton ();
$dashBoardPages = $oPluginRegistry->getDashboardPages ();
if (count($dashBoardPages)>0) {
    $G_TMP_MENU->AddIdRawOption('PLUGINS', '', G::LoadTranslation('ID_PLUGINS'), '', '', 'blockHeader');
    foreach ($dashBoardPages as $key => $tabInfo) {
        $tabNameSpace=$tabInfo->sNamespace;
        $tabName=$tabInfo->sName;
        $tabIcon=str_replace("ICON_","",$tabInfo->sIcon);
        if ($tabName!= "") {
            $G_TMP_MENU->AddIdRawOption($tabIcon, 'casesStartPage?action='.$tabNameSpace.'-'.$tabName,
                ucwords(strtolower($tabName)), '');
        }
    }
}

