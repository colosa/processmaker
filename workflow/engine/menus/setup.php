<?php
/**
 * setup.php
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

use ProcessMaker\Plugins\PluginRegistry;

global $G_TMP_MENU;
global $RBAC;
$partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;

/*----------------------------------********---------------------------------*/
if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    $pmSetupPermission = true;
    if ($RBAC->userCanAccess('PM_SETUP_LOGO') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LOGO', '../admin/pmLogo',
            G::LoadTranslation('ID_LOGO'),
            'icon-pmlogo.png', '','settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_EMAIL') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            "EMAIL_SERVER", "../emailServer/emailServer",
            G::LoadTranslation("ID_EMAIL_SERVER_TITLE"),
            "icon-email-settings1.png", "", "settings"
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_CALENDAR') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'CALENDAR', 'calendarList',
            G::LoadTranslation('ID_CALENDAR'),
            'icon-calendar.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_PROCESS_CATEGORIES') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'PROCESS_CATEGORY', '../processCategory/processCategoryList',
            G::LoadTranslation('ID_PROCESS_CATEGORY'),
            "rules.png", '', 'settings'
        );
    }
}

if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_SKIN') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'SKINS', 'skinsList',
            G::LoadTranslation('ID_SKINS'),
            'icon-skins.png', '', 'settings'
        );
    }
    if (!$partnerFlag) {
        /**
         * Remove heartbeat config from core, it will probably be used again 
         * when the functionality will be redesigned.
         */
        if ($RBAC->userCanAccess('PM_SETUP_HEART_BEAT') === 1 && false) {
            $G_TMP_MENU->AddIdRawOption(
                'HEARTBEAT', 'processHeartBeatConfig',
                G::LoadTranslation('ID_HEARTBEAT_CONFIG'),
                "heartBeat.jpg", '', 'settings'
            );
        }
    }
    if ($RBAC->userCanAccess('PM_SETUP_ENVIRONMENT') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'ENVIRONMENT_SETTINGS', 'environmentSettings',
            G::LoadTranslation('ID_ENVIRONMENT_SETTINGS'),
            "", '', 'settings'
        );
    }
}

if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_CLEAR_CACHE') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'CLEAR_CACHE', 'clearCompiled',
            G::LoadTranslation('ID_CLEAR_CACHE'),
            'icon-rebuild-clean.png', "", 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_PM_TABLES') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'PM_TABLES', '../pmTables',
            G::LoadTranslation('ID_ADDITIONAL_TABLES'),
            'icon-tables.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_LOGIN') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LOGIN', 'loginSettings',
            G::LoadTranslation('LOGIN'),
            "", '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_DASHBOARDS') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'DASHBOARD', '../DashboardModule/dashletsList',
            ucfirst(G::LoadTranslation('ID_DASHBOARD')),
            '', '', 'settings'
        );
        /*----------------------------------********---------------------------------*/
    }
}
//tools options
if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_LANGUAGE') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LANGUAGES', 'languages',
            G::LoadTranslation('ID_LANGUAGES'),
            'icon-language.png','', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_CASES_LIST_CACHE_BUILDER') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'APPCACHEVIEW_SETUP', '../setup/appCacheViewConf',
            G::LoadTranslation('ID_APPCACHE_SETUP'),
            "", '', 'settings'
        );
    }
    if (!$partnerFlag) {
        if ($RBAC->userCanAccess('PM_SETUP_PLUGINS') === 1) {
            $G_TMP_MENU->AddIdRawOption(
                'PLUGINS', 'pluginsMain',
                G::LoadTranslation('ID_PLUGINS_MANAGER'),
                'icon-plugins.png', '', 'plugins'
            );
        }
    }
}

//users options
if ($RBAC->userCanAccess('PM_USERS') === 1) {
    $G_TMP_MENU->AddIdRawOption('USERS', '../users/users_List', G::LoadTranslation('ID_USERS_LIST'),
        'icon-webservices.png', '', 'users');
    $G_TMP_MENU->AddIdRawOption('GROUPS', '../groups/groups', G::LoadTranslation('ID_GROUPS'), '', '', 'users');
    $G_TMP_MENU->AddIdRawOption(
        'DEPARTAMENTS', '../departments/departments',
        G::LoadTranslation('ID_DEPARTMENTS_USERS'),
        '', '', 'users'
    );
    $G_TMP_MENU->AddIdRawOption('ROLES', '../roles/roles_List',
        G::LoadTranslation('ID_ROLES'),
        '', '', 'users'
    );
}

if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') === 1 && $RBAC->userCanAccess('PM_USERS') === 1 && $RBAC->userCanAccess
    ('PM_SETUP_USERS_AUTHENTICATION_SOURCES') === 1) {
    $G_TMP_MENU->AddIdRawOption(
        'AUTHSOURCES', '../authSources/authSources_List',
        G::LoadTranslation('ID_AUTH_SOURCES'),
        '', '', 'users'
    );
    $G_TMP_MENU->AddIdRawOption('UX', '../admin/uxList', G::LoadTranslation('ID_USER_EXPERIENCE'), '', '', 'users');
    $G_TMP_MENU->AddIdRawOption('SYSTEM', '../admin/system', G::LoadTranslation('ID_SYSTEM'), '', '', 'settings');
    $G_TMP_MENU->AddIdRawOption(
        'INFORMATION', '../setup/systemInfo?option=processInfo',
        G::LoadTranslation('ID_SYSTEM_INFO'),
        '', '', 'settings'
    );
}

if ($RBAC->userCanAccess('PM_SETUP') === 1 && $RBAC->userCanAccess('PM_SETUP_LOGS') === 1) {
    $G_TMP_MENU->AddIdRawOption('EVENT', '../events/eventList', G::LoadTranslation('ID_EVENTS'), '', '', 'logs');
    $G_TMP_MENU->AddIdRawOption(
        'LOG_CASE_SCHEDULER', '../cases/cases_Scheduler_Log',
        G::LoadTranslation('ID_CASE_SCHEDULER'),
        "icon-logs-list.png", '', 'logs'
    );
    $G_TMP_MENU->AddIdRawOption("CRON", "../setup/cron", G::LoadTranslation("ID_CRON_ACTIONS"), null, null, 'logs');
    $G_TMP_MENU->AddIdRawOption(
        'EMAILS', '../mails/emailList',
        ucfirst(strtolower(G::LoadTranslation('ID_EMAILS'))),
        '', '', 'logs'
    );
    /*----------------------------------********---------------------------------*/
}

if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    $G_TMP_MENU->AddIdRawOption(
        'PM_REQUIREMENTS', '../setup/systemInfo',
        G::LoadTranslation('ID_PROCESSMAKER_REQUIREMENTS_CHECK'),
        '', '', 'settings'
    );
    $G_TMP_MENU->AddIdRawOption(
        'PHP_INFO', '../setup/systemInfo?option=php',
        G::LoadTranslation('ID_PHP_INFO'),
        '','', 'settings'
    );
    /*----------------------------------********---------------------------------*/
}
/*----------------------------------********---------------------------------*/
if ($RBAC->userCanAccess('PM_SETUP') == 1) {
    /*----------------------------------********---------------------------------*/
}

/*----------------------------------********---------------------------------*/
