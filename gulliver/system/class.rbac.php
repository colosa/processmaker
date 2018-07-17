<?php

/**
 * class.rbac.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */

use ProcessMaker\Exception\RBACException;

/**
 * File: $Id$
 *
 * RBAC class definition
 *
 * @package gulliver.system
 */

/**
 * Clase Wrapper
 *
 * @package gulliver.system
 */
class RBAC
{
    const SETUPERMISSIONUID = '00000000000000000000000000000002';
    const PER_SYSTEM = '00000000000000000000000000000002';
    const PM_GUEST_CASE = 'PM_GUEST_CASE';
    const PM_GUEST_CASE_UID = '00000000000000000000000000000066';
    const PROCESSMAKER_GUEST = 'PROCESSMAKER_GUEST';
    const PROCESSMAKER_GUEST_UID = '00000000000000000000000000000005';
    const GUEST_USER_UID = '00000000000000000000000000000002';

    /**
     *
     * @access private
     * @var $userObj
     */
    public $userObj;
    public $usersPermissionsObj;
    public $usersRolesObj;
    public $systemObj;
    public $rolesObj;
    public $permissionsObj;
    public $userloggedobj;
    public $currentSystemobj;
    public $rolesPermissionsObj;
    public $authSourcesObj;

    public $aUserInfo = [];
    public $aRbacPlugins = [];
    public $sSystem = '';

    public $singleSignOn = false;

    private static $instance = null;
    public $authorizedActions = [];

    private $aliasPermissions = [];

    /**
     * To enable compatibility with soap login.
     * @var bool
     */
    private $enableLoginHash = false;

    public function __construct()
    {
        $this->authorizedActions = [
            'users_Ajax.php' => [
                'availableUsers' => ['PM_FACTORY'],
                'assign' => ['PM_FACTORY'],
                'changeView' => [],
                'ofToAssign' => ['PM_FACTORY'],
                'usersGroup' => ['PM_FACTORY'],
                'canDeleteUser' => ['PM_USERS'],
                'deleteUser' => ['PM_USERS'],
                'changeUserStatus' => ['PM_USERS'],
                'availableGroups' => ['PM_USERS'],
                'assignedGroups' => ['PM_USERS'],
                'assignGroupsToUserMultiple' => ['PM_USERS'],
                'deleteGroupsToUserMultiple' => ['PM_USERS'],
                'authSources' => ['PM_USERS'],
                'loadAuthSourceByUID' => ['PM_USERS'],
                'updateAuthServices' => ['PM_USERS'],
                'usersList' => ['PM_USERS'],
                'updatePageSize' => [],
                'summaryUserData' => ['PM_USERS'],
                'verifyIfUserAssignedAsSupervisor' => ['PM_USERS']
            ],
            'skin_Ajax.php' => [
                'updatePageSize' => [],
                'skinList' => ['PM_SETUP_SKIN'],
                'newSkin' => ['PM_SETUP_SKIN'],
                'importSkin' => ['PM_SETUP_SKIN'],
                'exportSkin' => ['PM_SETUP_SKIN'],
                'deleteSkin' => ['PM_SETUP_SKIN'],
                'streamSkin' => ['PM_SETUP_SKIN'],
                'addTarFolder' => ['PM_SETUP_SKIN'],
                'copy_skin_folder' => ['PM_SETUP_SKIN']
            ],
            'processes_DownloadFile.php' => [
                'downloadFileHash' => ['PM_FACTORY']
            ],
            'processProxy.php' => [
                'categoriesList' => ['PM_SETUP_PROCESS_CATEGORIES'],
                'getCategoriesList' => ['PM_FACTORY'],
                'saveProcess' => ['PM_FACTORY'],
                'changeStatus' => ['PM_FACTORY'],
                'changeDebugMode' => ['PM_FACTORY'],
                'getUsers' => [],
                'getGroups' => [],
                'assignActorsTask' => [],
                'removeActorsTask' => [],
                'getActorsTask' => [],
                'getProcessDetail' => [],
                'getProperties' => [],
                'saveProperties' => [],
                'getCaledarList' => [],
                'getPMVariables' => [],
                'generateBpmn' => ['PM_FACTORY']
            ],
            'home.php' => [
                'login' => ['PM_LOGIN'],
                'index' => ['PM_CASES/strict'],
                'indexSingle' => ['PM_CASES/strict'],
                'appList' => ['PM_CASES/strict'],
                'appAdvancedSearch' => ['PM_ALLCASES'],
                'getApps' => ['PM_ALLCASES'],
                'getAppsData' => ['PM_ALLCASES'],
                'startCase' => ['PM_CASES/strict'],
                'error' => [],
                'getUserArray' => ['PM_ALLCASES'],
                'getCategoryArray' => ['PM_ALLCASES'],
                'getAllUsersArray' => ['PM_ALLCASES'],
                'getStatusArray' => ['PM_ALLCASES'],
                'getProcessArray' => ['PM_ALLCASES'],
                'getProcesses' => ['PM_ALLCASES'],
                'getUsers' => ['PM_ALLCASES']
            ],
            'newSite.php' => [
                'newSite.php' => ['PM_SETUP_ADVANCE']
            ],
            'emailsAjax.php' => [
                'MessageList' => ['PM_SETUP', 'PM_SETUP_LOGS'],
                'updateStatusMessage' => ['PM_SETUP', 'PM_SETUP_LOGS'],
            ],
            'processCategory_Ajax.php' => [
                'processCategoryList' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'updatePageSize' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'checkCategoryName' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'saveNewCategory' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'checkEditCategoryName' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'updateCategory' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'canDeleteCategory' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES'],
                'deleteCategory' => ['PM_SETUP', 'PM_SETUP_PROCESS_CATEGORIES']
            ],
            'emailServerAjax.php' => [
                'INS' => ['PM_SETUP'],
                'UPD' => ['PM_SETUP'],
                'DEL' => ['PM_SETUP'],
                'LST' => ['PM_SETUP'],
                'TEST' => ['PM_SETUP']
            ],
            'processes_GetFile.php' => [
                'mailTemplates' => ['PM_FACTORY'],
                'public' => ['PM_FACTORY']
            ]
        ];
        $this->aliasPermissions['PM_CASES'] = [self::PM_GUEST_CASE];
        $this->aliasPermissions['PM_LOGIN'] = [self::PM_GUEST_CASE];
    }

    /**
     * to get singleton instance
     *
     * @access public
     * @return object
     */
    public function &getSingleton()
    {
        if (self::$instance == null) {
            self::$instance = new RBAC();
        }

        return self::$instance;
    }

    /**
     * to get start with some classes
     *
     * @access public
     * @return object
     */
    public function initRBAC()
    {
        if (is_null($this->userObj)) {

            $this->userObj = new RbacUsers();
        }

        if (is_null($this->systemObj)) {

            $this->systemObj = new Systems();
        }

        if (is_null($this->usersRolesObj)) {

            $this->usersRolesObj = new UsersRoles();
        }

        if (is_null($this->rolesObj)) {

            $this->rolesObj = new Roles();
        }

        if (is_null($this->permissionsObj)) {

            $this->permissionsObj = new Permissions();
        }

        if (is_null($this->rolesPermissionsObj)) {

            $this->rolesPermissionsObj = new RolesPermissions();
        }

        if (is_null($this->authSourcesObj)) {

            $this->authSourcesObj = new AuthenticationSource();
        }
        //hook for RBAC plugins
        $pathPlugins = PATH_RBAC . 'plugins';
        if (is_dir($pathPlugins)) {
            if ($handle = opendir($pathPlugins)) {
                while (false !== ($file = readdir($handle))) {
                    if (strpos($file, '.php', 1) && is_file($pathPlugins . PATH_SEP . $file) && substr($file, 0,
                            6) == 'class.' && substr($file, -4) == '.php') {

                        $className = substr($file, 6, strlen($file) - 10);
                        require_once($pathPlugins . PATH_SEP . $file);
                        $this->aRbacPlugins[] = $className;

                    }
                }
            }
        }
        if (!in_array('ldapAdvanced', $this->aRbacPlugins)) {
            if (class_exists('ldapAdvanced')) {
                $this->aRbacPlugins[] = 'ldapAdvanced';
            }
        }
    }

    /**
     * gets the Role and their permissions for Administrator Processmaker
     *
     * @access public
     * @return $this->permissionsAdmin[ $permissionsAdmin ]
     */
    public function loadPermissionAdmin()
    {
        $permissionsAdmin = [
            [
                "PER_UID" => "00000000000000000000000000000001",
                "PER_CODE" => "PM_LOGIN",
                "PER_NAME" => "Login"
            ],
            [
                "PER_UID" => "00000000000000000000000000000002",
                "PER_CODE" => "PM_SETUP",
                "PER_NAME" => "Setup"
            ],
            [
                "PER_UID" => "00000000000000000000000000000003",
                "PER_CODE" => "PM_USERS",
                "PER_NAME" => "Users"
            ],
            [
                "PER_UID" => "00000000000000000000000000000004",
                "PER_CODE" => "PM_FACTORY",
                "PER_NAME" => "Design Process"
            ],
            [
                "PER_UID" => "00000000000000000000000000000005",
                "PER_CODE" => "PM_CASES",
                "PER_NAME" => "Create Users"
            ],
            [
                "PER_UID" => "00000000000000000000000000000006",
                "PER_CODE" => "PM_ALLCASES",
                "PER_NAME" => "All Cases"
            ],
            [
                "PER_UID" => "00000000000000000000000000000007",
                "PER_CODE" => "PM_REASSIGNCASE",
                "PER_NAME" => "Reassign case"
            ],
            [
                "PER_UID" => "00000000000000000000000000000008",
                "PER_CODE" => "PM_REPORTS",
                "PER_NAME" => "PM reports"
            ],
            [
                "PER_UID" => "00000000000000000000000000000009",
                "PER_CODE" => "PM_SUPERVISOR",
                "PER_NAME" => "Supervisor"
            ],
            [
                "PER_UID" => "00000000000000000000000000000010",
                "PER_CODE" => "PM_SETUP_ADVANCE",
                "PER_NAME" => "Setup Advanced"
            ],
            [
                "PER_UID" => "00000000000000000000000000000011",
                "PER_CODE" => "PM_DASHBOARD",
                "PER_NAME" => "Dashboard"
            ],
            [
                "PER_UID" => "00000000000000000000000000000012",
                "PER_CODE" => "PM_WEBDAV",
                "PER_NAME" => "WebDav"
            ],
            [
                "PER_UID" => "00000000000000000000000000000013",
                "PER_CODE" => "PM_DELETECASE",
                "PER_NAME" => "Cancel cases"
            ],
            [
                "PER_UID" => "00000000000000000000000000000014",
                "PER_CODE" => "PM_EDITPERSONALINFO",
                "PER_NAME" => "Edit Personal Info"
            ],
            [
                "PER_UID" => "00000000000000000000000000000015",
                "PER_CODE" => "PM_FOLDERS_VIEW",
                "PER_NAME" => "View Folders"
            ],
            [
                "PER_UID" => "00000000000000000000000000000016",
                "PER_CODE" => "PM_FOLDERS_ADD_FOLDER",
                "PER_NAME" => "Delete folders"
            ],
            [
                "PER_UID" => "00000000000000000000000000000017",
                "PER_CODE" => "PM_FOLDERS_ADD_FILE",
                "PER_NAME" =>
                    "Delete folders"
            ],
            [
                "PER_UID" => "00000000000000000000000000000018",
                "PER_CODE" => "PM_CANCELCASE",
                "PER_NAME" => "Cancel cases"
            ],
            [
                "PER_UID" => "00000000000000000000000000000019",
                "PER_CODE" => "PM_FOLDER_DELETE",
                "PER_NAME" => "Cancel cases"
            ],
            [
                "PER_UID" => "00000000000000000000000000000020",
                "PER_CODE" => "PM_SETUP_LOGO",
                "PER_NAME" => "Setup Logo"
            ],
            [
                "PER_UID" => "00000000000000000000000000000021",
                "PER_CODE" => "PM_SETUP_EMAIL",
                "PER_NAME" => "Setup Email"
            ],
            [
                "PER_UID" => "00000000000000000000000000000022",
                "PER_CODE" => "PM_SETUP_CALENDAR",
                "PER_NAME" => "Setup Calendar"
            ],
            [
                "PER_UID" => "00000000000000000000000000000023",
                "PER_CODE" => "PM_SETUP_PROCESS_CATEGORIES",
                "PER_NAME" => "Setup Process Categories"
            ],
            [
                "PER_UID" => "00000000000000000000000000000024",
                "PER_CODE" => "PM_SETUP_CLEAR_CACHE",
                "PER_NAME" => "Setup Clear Cache"
            ],
            [
                "PER_UID" => "00000000000000000000000000000025",
                "PER_CODE" => "PM_SETUP_HEART_BEAT",
                "PER_NAME" => "Setup Heart Beat"
            ],
            [
                "PER_UID" => "00000000000000000000000000000026",
                "PER_CODE" => "PM_SETUP_ENVIRONMENT",
                "PER_NAME" => "Setup Environment"
            ],
            [
                "PER_UID" => "00000000000000000000000000000027",
                "PER_CODE" => "PM_SETUP_PM_TABLES",
                "PER_NAME" => "Setup PM Tables"
            ],
            [
                "PER_UID" => "00000000000000000000000000000028",
                "PER_CODE" => "PM_SETUP_LOGIN",
                "PER_NAME" => "Setup Login"
            ],
            [
                "PER_UID" => "00000000000000000000000000000029",
                "PER_CODE" => "PM_SETUP_DASHBOARDS",
                "PER_NAME" => "Setup Dashboards"
            ],
            [
                "PER_UID" => "00000000000000000000000000000030",
                "PER_CODE" => "PM_SETUP_LANGUAGE",
                "PER_NAME" => "Setup Language"
            ],
            [
                "PER_UID" => "00000000000000000000000000000031",
                "PER_CODE" => "PM_SETUP_SKIN",
                "PER_NAME" => "Setup Skin"
            ],
            [
                "PER_UID" => "00000000000000000000000000000032",
                "PER_CODE" => "PM_SETUP_CASES_LIST_CACHE_BUILDER",
                "PER_NAME" => "Setup Case List Cache Builder"
            ],
            [
                "PER_UID" => "00000000000000000000000000000033",
                "PER_CODE" => "PM_SETUP_PLUGINS",
                "PER_NAME" => "Setup Plugins"
            ],
            [
                "PER_UID" => "00000000000000000000000000000034",
                "PER_CODE" => "PM_SETUP_USERS_AUTHENTICATION_SOURCES",
                "PER_NAME" => "Setup User Authentication Sources"
            ],
            [
                "PER_UID" => "00000000000000000000000000000035",
                "PER_CODE" => "PM_SETUP_LOGS",
                "PER_NAME" => "Setup Logs"
            ],
            [
                "PER_UID" => "00000000000000000000000000000036",
                "PER_CODE" => "PM_DELETE_PROCESS_CASES",
                "PER_NAME" => "Delete process cases"
            ],
            [
                "PER_UID" => "00000000000000000000000000000037",
                "PER_CODE" => "PM_EDITPERSONALINFO_CALENDAR",
                "PER_NAME" => "Edit personal info Calendar"
            ],
            [
                "PER_UID" => "00000000000000000000000000000038",
                "PER_CODE" => "PM_UNCANCELCASE",
                "PER_NAME" => "Undo cancel case"
            ],
            [
                "PER_UID" => "00000000000000000000000000000039",
                "PER_CODE" => "PM_REST_API_APPLICATIONS",
                "PER_NAME" => "Create rest API Aplications"
            ],
            [
                "PER_UID" => "00000000000000000000000000000040",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_FIRST_NAME",
                "PER_NAME" => "Edit User profile First Name"
            ],
            [
                "PER_UID" => "00000000000000000000000000000041",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_LAST_NAME",
                "PER_NAME" => "Edit User profile Last Name"
            ],
            [
                "PER_UID" => "00000000000000000000000000000042",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_USERNAME",
                "PER_NAME" => "Edit User profile Username"
            ],
            [
                "PER_UID" => "00000000000000000000000000000043",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_EMAIL",
                "PER_NAME" => "Edit User profile Email"
            ],
            [
                "PER_UID" => "00000000000000000000000000000044",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_ADDRESS",
                "PER_NAME" => "Edit User profile Address"
            ],
            [
                "PER_UID" => "00000000000000000000000000000045",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_ZIP_CODE",
                "PER_NAME" => "Edit User profile Zip Code"
            ],
            [
                "PER_UID" => "00000000000000000000000000000046",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_COUNTRY",
                "PER_NAME" => "Edit User profile Country"
            ],
            [
                "PER_UID" => "00000000000000000000000000000047",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_STATE_OR_REGION",
                "PER_NAME" => "Edit User profile State or Region"
            ],
            [
                "PER_UID" => "00000000000000000000000000000048",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_LOCATION",
                "PER_NAME" => "Edit User profile Location"
            ],
            [
                "PER_UID" => "00000000000000000000000000000049",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_PHONE",
                "PER_NAME" => "Edit User profile Phone"
            ],
            [
                "PER_UID" => "00000000000000000000000000000050",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_POSITION",
                "PER_NAME" => "Edit User profile Position"
            ],
            [
                "PER_UID" => "00000000000000000000000000000051",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_REPLACED_BY",
                "PER_NAME" => "Edit User profile Replaced By"
            ],
            [
                "PER_UID" => "00000000000000000000000000000052",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_EXPIRATION_DATE",
                "PER_NAME" => "Edit User profile Expiration Date"
            ],
            [
                "PER_UID" => "00000000000000000000000000000053",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_CALENDAR",
                "PER_NAME" => "Edit User profile Calendar"
            ],
            [
                "PER_UID" => "00000000000000000000000000000054",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_STATUS",
                "PER_NAME" => "Edit User profile Status"
            ],
            [
                "PER_UID" => "00000000000000000000000000000055",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_ROLE",
                "PER_NAME" => "Edit User profile Role"
            ],
            [
                "PER_UID" => "00000000000000000000000000000056",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_TIME_ZONE",
                "PER_NAME" => "Edit User profile Time Zone"
            ],
            [
                "PER_UID" => "00000000000000000000000000000057",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_DEFAULT_LANGUAGE",
                "PER_NAME" => "Edit User profile Default Language"
            ],
            [
                "PER_UID" => "00000000000000000000000000000058",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_COSTS",
                "PER_NAME" => "Edit User profile Costs"
            ],
            [
                "PER_UID" => "00000000000000000000000000000059",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_PASSWORD",
                "PER_NAME" => "Edit User profile Password"
            ],
            [
                "PER_UID" => "00000000000000000000000000000060",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_USER_MUST_CHANGE_PASSWORD_AT_NEXT_LOGON",
                "PER_NAME" => "Edit User profile Must Change Password at next Logon"
            ],
            [
                "PER_UID" => "00000000000000000000000000000061",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_PHOTO",
                "PER_NAME" => "Edit User profile Photo"
            ],
            [
                "PER_UID" => "00000000000000000000000000000062",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_DEFAULT_MAIN_MENU_OPTIONS",
                "PER_NAME" => "Edit User profile Default Main Menu Options"
            ],
            [
                "PER_UID" => "00000000000000000000000000000063",
                "PER_CODE" => "PM_EDIT_USER_PROFILE_DEFAULT_CASES_MENU_OPTIONS",
                "PER_NAME" => "Edit User profile Default Cases Menu Options"
            ],
            [
                "PER_UID" => "00000000000000000000000000000064",
                "PER_CODE" => "PM_REASSIGNCASE_SUPERVISOR",
                "PER_NAME" => "Reassign case supervisor"
            ],
            [
                "PER_UID" => "00000000000000000000000000000065",
                "PER_CODE" => "PM_SETUP_CUSTOM_CASES_LIST",
                "PER_NAME" => "Setup Custom Cases List"
            ]
        ];

        return $permissionsAdmin;
    }

    /**
     * Create if not exists GUEST user.
     *
     * @param Roles $role
     * @throws Exception
     */
    private function verifyGuestUser(Roles $role)
    {
        try {
            $strRole = $role->getRolCode();

            $arrayData = [];
            $arrayData["USR_UID"] = self::GUEST_USER_UID;
            $arrayData["USR_USERNAME"] = 'guest';
            $arrayData["USR_PASSWORD"] = '674ba9750749d735ec9787d606170d78';
            $arrayData["USR_FIRSTNAME"] = 'Guest';
            $arrayData["USR_LASTNAME"] = '';
            $arrayData["USR_EMAIL"] = 'guest@processmaker.com';
            $arrayData["USR_DUE_DATE"] = '2030-01-01';
            $arrayData["USR_CREATE_DATE"] = date("Y-m-d H:i:s");
            $arrayData["USR_UPDATE_DATE"] = date("Y-m-d H:i:s");
            $arrayData["USR_BIRTHDAY"] = '2009-02-01';
            $arrayData["USR_AUTH_USER_DN"] = "";
            $arrayData["USR_STATUS"] = 0;

            $rbacUserExists = RbacUsersPeer::retrieveByPK(self::GUEST_USER_UID);
            $isNotRbacUserGuest = !empty($rbacUserExists)
                && $rbacUserExists instanceof RbacUsers
                && $rbacUserExists->getUserRole($rbacUserExists->getUsrUid())['ROL_CODE']
                !== self::PROCESSMAKER_GUEST;
            if (empty($rbacUserExists)) {
                $rbacUser = new RbacUsers();
                $rbacUser->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);
                $rbacUser->save();

                $arrayData["USR_UID"] = $rbacUser->getUsrUid();
                $arrayData["USR_STATUS"] = 'INACTIVE';
                $arrayData["USR_COUNTRY"] = "";
                $arrayData["USR_CITY"] = "";
                $arrayData["USR_LOCATION"] = "";
                $arrayData["USR_ADDRESS"] = "";
                $arrayData["USR_PHONE"] = "";
                $arrayData["USR_ZIP_CODE"] = "";
                $arrayData["USR_POSITION"] = "";
                $arrayData["USR_ROLE"] = $strRole;

                $user = new Users();
                $user->create($arrayData);
                $this->assignRoleToUser($user->getUsrUid(), $strRole);
            } elseif ($isNotRbacUserGuest) {
                $this->assignRoleToUser($rbacUserExists->getUsrUid(), $strRole);
            }
        } catch (Exception $exception) {
            throw new Exception(
                "Can not create guest user: " . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    /**
     * Create if not exists GUEST role.
     *
     * @param type $permissions
     * @return type
     * @throws Exception
     */
    private function verifyGuestRole($permissions)
    {
        try {
            $criteria = new Criteria;
            $criteria->add(RolesPeer::ROL_CODE, self::PROCESSMAKER_GUEST);
            $roleExists = RolesPeer::doSelectOne($criteria);
            if (!empty($roleExists)) {
                return $roleExists;
            }
            $dataCase = [
                'ROL_UID' => self::PROCESSMAKER_GUEST_UID,
                'ROL_CODE' => self::PROCESSMAKER_GUEST,
                'ROL_SYSTEM' => self::PER_SYSTEM,
                'ROL_STATUS' => 1,
                'ROL_NAME' => self::PROCESSMAKER_GUEST,
                'ROL_CREATE_DATE' => date('Y-m-d H:i:s'),
                'ROL_UPDATE_DATE' => date('Y-m-d H:i:s'),
            ];
            $this->createRole($dataCase);
            $role = RolesPeer::doSelectOne($criteria);
            foreach ($permissions as $permission) {
                $o = new RolesPermissions();
                $o->setPerUid($permission->getPerUid());
                $o->setPermissionName('Guest case');
                $o->setRolUid($role->getRolUid());
                $o->save();
            }

            return $role;
        } catch (Exception $exception) {
            throw new Exception(
                "Can not create guest role: " . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    /**
     * Create if not exists GUEST permissions.
     *
     * @return type
     * @throws Exception
     */
    private function verifyGuestPermissions()
    {
        try {
            $criteria = new Criteria();
            $criteria->add(PermissionsPeer::PER_CODE, self::PM_GUEST_CASE);
            $perm = PermissionsPeer::doSelectOne($criteria);
            if (!empty($perm)) {
                return [$perm];
            }
            $permission = new Permissions();
            $permission->setPerUid(self::PM_GUEST_CASE_UID);
            $permission->setPerCode(self::PM_GUEST_CASE);
            $permission->setPerCreateDate(date('Y-m-d H:i:s'));
            $permission->setPerUpdateDate(date('Y-m-d H:i:s'));
            $permission->setPerStatus(1);
            $permission->setPerSystem(self::PER_SYSTEM);
            $permission->save();

            return [$permission];
        } catch (Exception $exception) {
            throw new Exception(
                "Can not set guest permissions: " . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    /**
     * Create if not exists GUEST user.
     * Create if not exists GUEST role.
     * Create if not exists GUEST permissions.
     *
     * @throws Exception
     */
    private function verifyGuestUserRolePermission()
    {
        $permissions = $this->verifyGuestPermissions();
        $role = $this->verifyGuestRole($permissions);
        $this->verifyGuestUser($role);
    }

    /**
     * Gets the roles and permission for one RBAC_user
     *
     * gets the Role and their permissions for one User
     *
     * @access public
     *
     * @param string $sSystem the system
     * @param string $sUser the user
     * @return $this->aUserInfo[ $sSystem ]
     */
    public function loadUserRolePermission($sSystem, $sUser)
    {
        //in previous versions  we provided a path data and session we will cache the session Info for this user
        //now this is deprecated, and all the aUserInfo is in the memcache
        $this->sSystem = $sSystem;
        $fieldsSystem = $this->systemObj->loadByCode($sSystem);
        $fieldsRoles = $this->usersRolesObj->getRolesBySystem($fieldsSystem['SYS_UID'], $sUser);
        $fieldsPermissions = $this->usersRolesObj->getAllPermissions($fieldsRoles['ROL_UID'], $sUser);
        $this->aUserInfo['USER_INFO'] = $this->userObj->load($sUser);
        $this->aUserInfo[$sSystem]['SYS_UID'] = $fieldsSystem['SYS_UID'];
        $this->aUserInfo[$sSystem]['ROLE'] = $fieldsRoles;
        $this->aUserInfo[$sSystem]['PERMISSIONS'] = $fieldsPermissions;
    }

    /**
     * verification the register automatic
     *
     *
     * @access public
     * @param string $strUser the system
     * @param string $strPass the password
     * @return $res
     */
    public function checkAutomaticRegister($strUser, $strPass)
    {
        $result = -1; //default return value,

        foreach ($this->aRbacPlugins as $className) {
            $plugin = new $className();
            if (method_exists($plugin, 'automaticRegister')) {
                $criteria = new Criteria('rbac');
                $criteria->add(AuthenticationSourcePeer::AUTH_SOURCE_PROVIDER, $className);
                $criteria->addAscendingOrderByColumn(AuthenticationSourcePeer::AUTH_SOURCE_NAME);
                $dataset = AuthenticationSourcePeer::doSelectRS($criteria, Propel::getDbConnection('rbac_ro'));
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $dataset->next();
                $row = $dataset->getRow();
                while (is_array($row)) {
                    $row = array_merge($row, unserialize($row['AUTH_SOURCE_DATA']));
                    //Check if this authsource is enabled for AutoRegister, if not skip this
                    if ($row['AUTH_SOURCE_AUTO_REGISTER'] == 1) {
                        $plugin->sAuthSource = $row['AUTH_SOURCE_UID'];
                        $plugin->sSystem = $this->sSystem;
                        //search the usersRolesObj
                        //create the users in ProcessMaker
                        $res = $plugin->automaticRegister($row, $strUser, $strPass);
                        if ($res == 1) {
                            return $res;
                        }
                    }
                    $dataset->next();
                    $row = $dataset->getRow();
                }
            }
        }

    }

    /**
     * this function is checking the register automatic without authentication
     *
     *
     * @access public
     * @param string $authType
     * @param string $userFields
     * @param string $strPass
     * @return number -2: wrong password
     * -3: inactive user
     * -4: due date
     * -5: invalid authentication source
     */
    public function VerifyWithOtherAuthenticationSource($authType, $userFields, $strPass)
    {
        if ($authType == '' || $authType == 'MYSQL') {
            //check if the user is active
            if ($userFields['USR_STATUS'] != 1) {
                return -3; //inactive user
            }

            //check if the user's due date is valid
            if ($userFields['USR_DUE_DATE'] < date('Y-m-d')) {
                return -4; //due date
            }
        }

        foreach ($this->aRbacPlugins as $className) {
            if (strtolower($className) == strtolower($authType)) {
                $plugin = new $className();
                $plugin->sAuthSource = $userFields["UID_AUTH_SOURCE"];
                $plugin->sSystem = $this->sSystem;

                $bValidUser = false;
                $bValidUser = $plugin->VerifyLogin($userFields["USR_AUTH_USER_DN"], $strPass);
                if ($bValidUser === true) {
                    return ($userFields['USR_UID']);
                } else {
                    return -2; //wrong password
                }
            }
        }

        return -5; //invalid authentication source
    }

    /**
     * authentication of an user through of class RBAC_user
     *
     * checking that an user has right to start an application
     *
     * @access public
     *
     * @param string $strUser UserId (login) an user
     * @param string $strPass Password
     * @return -1: no user
     * -2: wrong password
     * -3: inactive usuario
     * -4: due date
     * -5: invalid authentication source ( **new )
     * n : uid of user
     */
    public function VerifyLogin($strUser, $strPass)
    {
        /*----------------------------------********---------------------------------*/

        if (strlen($strPass) == 0) {
            return -2;
        }
        //check if the user exists in the table RB_WORKFLOW.USERS
        $this->initRBAC();
        //if the user exists, the VerifyUser function will return the user properties
        if ($this->userObj->verifyUser($strUser) == 0) {
            //here we are checking if the automatic user Register is enabled, ioc return -1
            $res = $this->checkAutomaticRegister($strUser, $strPass);
            if ($res == 1) {
                $this->userObj->verifyUser($strUser);
            } else {
                return $res;
            }
        }

        //default values
        $authType = 'mysql';
        if (isset($this->userObj->fields['USR_AUTH_TYPE'])) {
            $authType = strtolower($this->userObj->fields['USR_AUTH_TYPE']);
        }
        //Hook for RBAC plugins
        if ($authType != "mysql" && $authType != "") {
            $res = $this->VerifyWithOtherAuthenticationSource($authType, $this->userObj->fields, $strPass);

            return $res;
        } else {
            $this->userObj->reuseUserFields = true;
            $res = $this->userObj->VerifyLogin($strUser, $strPass);

            return $res;
        }
    }

    /**
     * Verify if the user exist or not exists, the argument is the UserName
     *
     * @author Everth S. Berrios
     * @access public
     * @param string $strUser
     * @return $res
     */
    public function verifyUser($strUser)
    {
        $res = $this->userObj->verifyUser($strUser);

        return $res;
    }

    /**
     * Verify if the user exist or not exists, the argument is the UserUID
     *
     * @access public
     * @param string $strUserId
     * @return $res
     */
    public function verifyUserId($strUserId)
    {
        $res = $this->userObj->verifyUserId($strUserId);

        return $res;
    }

    /**
     * Verify if the user has a right over the permission. Ex.
     *      $rbac->userCanAccess("PM_CASES");
     *
     * Alias of permissions:
     *      PM_CASES has alias: PM_GUEST_CASE
     * This means that a role with PM_GUEST_CASE could access like one with PM_CASES
     * unless the permission is required as strict, like this:
     *      $rbac->userCanAccess("PM_CASES/strict");
     *
     * @access public
     * @param string $uid id of user
     * @param string $system Code of System
     * @param string $permBase id of Permissions
     * @return int 1: If it is ok
     * -1: System doesn't exists
     * -2: The User has not a Role
     * -3: The User has not this Permission.
     */
    public function userCanAccess($permBase)
    {
        $strict = substr($permBase, -7, 7) === '/strict';
        $perm = $strict ? substr($permBase, 0, -7) : $permBase;
        if (isset($this->aUserInfo[$this->sSystem]['PERMISSIONS'])) {
            $res = -3;
            foreach ($this->aUserInfo[$this->sSystem]['PERMISSIONS'] as $key => $val) {
                if ($perm == $val['PER_CODE']) {
                    $res = 1;
                }
                $hasAliasPermission = !$strict
                    && isset($this->aliasPermissions[$perm])
                    && array_search(
                        $val['PER_CODE'],
                        $this->aliasPermissions[$perm]
                    ) !== false;
                if ($hasAliasPermission) {
                    $res = 1;
                    break;
                }
            }
        } else {
            $res = -1;
        }

        return $res;
    }

    /**
     * to create an user
     *
     * @access public
     * @param array $dataCase
     * @param string $rolCode
     * @return $userUid
     */
    public function createUser($dataCase = [], $rolCode = '')
    {
        if ($dataCase["USR_STATUS"] . "" == "1") {
            $dataCase["USR_STATUS"] = "ACTIVE";
        }

        if ($dataCase["USR_STATUS"] . "" == "0") {
            $dataCase["USR_STATUS"] = "INACTIVE";
        }

        if ($dataCase['USR_STATUS'] == 'ACTIVE') {
            $dataCase['USR_STATUS'] = 1;
        }
        if ($dataCase['USR_STATUS'] == 'INACTIVE') {
            $dataCase['USR_STATUS'] = 0;
        }

        $userUid = $this->userObj->create($dataCase);

        if ($rolCode != '') {
            $this->assignRoleToUser($userUid, $rolCode);
        }

        return $userUid;
    }

    /**
     * Update an user
     *
     * @access public
     * @param array $dataCase
     * @param string $rolCode
     * @return void
     */
    public function updateUser($dataCase = [], $rolCode = '')
    {
        if (isset($dataCase['USR_STATUS'])) {
            if ($dataCase['USR_STATUS'] == 'ACTIVE') {
                $dataCase['USR_STATUS'] = 1;
            }
        }

        $this->userObj->update($dataCase);
        if ($rolCode != '') {
            $this->removeRolesFromUser($dataCase['USR_UID']);
            $this->assignRoleToUser($dataCase['USR_UID'], $rolCode);
        }
    }

    /**
     * To put role an user
     *
     * @access public
     * @param string $userUid
     * @param string $rolCode
     * @return void
     */
    public function assignRoleToUser($userUid = '', $rolCode = '')
    {
        $aRol = $this->rolesObj->loadByCode($rolCode);
        $this->usersRolesObj->create($userUid, $aRol['ROL_UID']);
    }

    /**
     * Remove a role from an user
     *
     * @access public
     * @param string $userUid
     * @return void
     */
    public function removeRolesFromUser($userUid = '')
    {
        $criteria = new Criteria('rbac');
        $criteria->add(UsersRolesPeer::USR_UID, $userUid);
        $criteria->add(UsersRolesPeer::ROL_UID, [RBAC::PROCESSMAKER_GUEST_UID], Criteria::NOT_IN);
        UsersRolesPeer::doDelete($criteria);
    }

    /**
     * change status of an user
     *
     * @access public
     * @param string $userUid
     * @param string $userStatus
     * @return void
     */
    public function changeUserStatus($userUid = '', $userStatus = 'ACTIVE')
    {
        if ($userStatus === 'ACTIVE') {
            $userStatus = 1;
        }

        $aFields = $this->userObj->load($userUid);
        $aFields['USR_STATUS'] = $userStatus;
        $this->userObj->update($aFields);
    }

    /**
     * remove an user
     *
     * @access public
     * @param string $userUid
     * @return void
     */
    public function removeUser($userUid = '')
    {
        $this->userObj->remove($userUid);
        $this->removeRolesFromUser($userUid);
    }
    //


    /**
     * getting user's basic information (rbac)
     *
     * getting datas that is saved in rbac
     *
     * @access public
     *
     * @param string $uid id user
     * @return array with info of an user
     */
    public function load($uid)
    {
        $this->initRBAC();
        $this->userObj->Fields = $this->userObj->load($uid);

        $fieldsSystem = $this->systemObj->loadByCode($this->sSystem);
        $fieldsRoles = $this->usersRolesObj->getRolesBySystem($fieldsSystem['SYS_UID'], $uid);
        $this->userObj->Fields['USR_ROLE'] = $fieldsRoles['ROL_CODE'];

        return $this->userObj->Fields;
    }

    /**
     * create permission
     *
     *
     * @access public
     *
     * @param string $code
     * @return void
     */
    public function createPermision($code)
    {
        return $this->permissionsObj->create(array('PER_CODE' => $code));
    }

    /**
     * list all roles
     *
     *
     * @access public
     *
     * @param string $systemCode
     * @return $this->rolesObj
     */

    public function listAllRoles($systemCode = 'PROCESSMAKER')
    {
        return $this->rolesObj->listAllRoles($systemCode);
    }

    /**
     * getting all roles
     *
     *
     * @access public
     *
     * @param string $systemCode
     * @return $this->rolesObj->getAllRoles
     */
    public function getAllRoles($systemCode = 'PROCESSMAKER')
    {
        return $this->rolesObj->getAllRoles($systemCode);
    }

    /**
     * getting all roles by filter
     *
     *
     * @access public
     * @param string $filter
     * @return $this->rolesObj->getAllRolesFilter
     */
    public function getAllRolesFilter($start, $limit, $filter)
    {
        return $this->rolesObj->getAllRolesFilter($start, $limit, $filter);
    }

    /**
     * list all permission
     *
     *
     * @access public
     *
     * @param string $systemCode
     * @return $this->rolesObj->listAllPermissions
     */
    public function listAllPermissions($systemCode = 'PROCESSMAKER')
    {
        return $this->rolesObj->listAllPermissions($systemCode);
    }

    /**
     * this function creates a role
     *
     *
     * @access public
     *
     * @param array $dataCase
     * @return $this->rolesObj->createRole
     */
    public function createRole($dataCase)
    {
        return $this->rolesObj->createRole($dataCase);
    }

    /**
     * this function removes a role
     *
     *
     * @access public
     *
     * @param string $rolUid
     * $@return $this->rolesObj->removeRole
     */
    public function removeRole($rolUid)
    {
        return $this->rolesObj->removeRole($rolUid);
    }

    /**
     * this function checks a new role
     *
     *
     * @access public
     *
     * @param string $code
     * @return $this->rolesObj->verifyNewRole
     */
    public function verifyNewRole($code)
    {
        return $this->rolesObj->verifyNewRole($code);
    }

    /**
     * this function updates a role
     *
     *
     * @access public
     *
     * @param string $fields
     * @return $this->rolesObj->updateRole
     */
    public function updateRole($fields)
    {
        return $this->rolesObj->updateRole($fields);
    }

    /**
     * this function loads by ID
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->loadById
     */
    public function loadById($rolUid)
    {
        return $this->rolesObj->loadById($rolUid);
    }

    /**
     * this function gets the user's roles
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->getRoleUsers
     */
    public function getRoleUsers($rolUid, $filter = '')
    {
        throw new Exception(__METHOD__ . ': The method is deprecated');
    }

    /**
     * this function gets the number of users by roles
     *
     *
     * @access public
     *
     * @return $this->rolesObj->getAllUsersByRole
     */
    public function getAllUsersByRole()
    {
        return $this->rolesObj->getAllUsersByRole();
    }

    /**
     * this function gets the number of users by department
     *
     *
     * @access public
     *
     * @return $this->rolesObj->getAllUsersByRole
     */
    public function getAllUsersByDepartment()
    {
        return $this->rolesObj->getAllUsersByDepartment();
    }

    /**
     * this function gets roles code
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->getRoleCode
     */
    public function getRoleCode($rolUid)
    {
        return $this->rolesObj->getRoleCode($rolUid);
    }

    /**
     * this function removes role from an user
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @param string $USR_UID
     * @return $this->rolesObj->deleteUserRole
     */
    public function deleteUserRole($rolUid, $USR_UID)
    {
        return $this->rolesObj->deleteUserRole($rolUid, $USR_UID);
    }

    /**
     * this function gets all user
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->getAllUsers
     */
    public function getAllUsers($rolUid, $filter = '')
    {
        throw new Exception(__METHOD__ . ': The method is deprecated');
    }

    /**
     * this function assigns role an user
     *
     *
     * @access public
     *
     * @param array $dataCase
     * @return $this->rolesObj->assignUserToRole
     */
    public function assignUserToRole($dataCase)
    {
        return $this->rolesObj->assignUserToRole($dataCase);
    }

    /**
     * this function gets role permission
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->getRolePermissionsByPerUid
     */
    public function getRolePermissionsByPerUid($rolUid)
    {
        return $this->rolesObj->getRolePermissionsByPerUid($rolUid);
    }

    /**
     * this function is Assignee role permission
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->isAssigneRolePermission
     */
    public function getPermissionAssignedRole($rolUid, $perUid)
    {
        return $this->rolesObj->getPermissionAssignedRole($rolUid, $perUid);
    }

    /**
     * this function gets role permission
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @param string $filter
     * @param string $status
     * @return $this->rolesObj->getRolePermissions
     */
    public function getRolePermissions($rolUid, $filter = '', $status = null)
    {
        return $this->rolesObj->getRolePermissions($rolUid, $filter, $status);
    }

    /**
     * this function gets all permissions
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @param string $perSystem
     * @param string $filter
     * @param string $status
     * @return $this->rolesObj->getAllPermissions
     */
    public function getAllPermissions($rolUid, $perSystem = "", $filter = '', $status = null)
    {
        return $this->rolesObj->getAllPermissions($rolUid, $perSystem, $filter, $status);
    }

    /**
     * this function assigns permissions and role
     *
     *
     * @access public
     *
     * @param array $dataCase
     * @return $this->rolesObj->assignPermissionRole
     */
    public function assignPermissionRole($dataCase)
    {
        return $this->rolesObj->assignPermissionRole($dataCase);
    }

    /**
     * this function assigns permissions to a role
     *
     *
     * @access public
     *
     * @param string $roleUid
     * @param string $permissionUid
     * @return $this->rolesPermissionsObj->create
     */
    public function assignPermissionToRole($roleUid, $permissionUid)
    {
        return $this->rolesPermissionsObj->create(array('ROL_UID' => $roleUid, 'PER_UID' => $permissionUid));
    }

    /**
     * this function removes permission to role
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @param string $perUid
     * @return $this->rolesObj->deletePermissionRole
     */
    public function deletePermissionRole($rolUid, $perUid)
    {
        return $this->rolesObj->deletePermissionRole($rolUid, $perUid);
    }

    /**
     * this function counts number of user without role
     *
     *
     * @access public
     *
     * @param string $rolUid
     * @return $this->rolesObj->numUsersWithRole
     */
    public function numUsersWithRole($rolUid)
    {
        return $this->rolesObj->numUsersWithRole($rolUid);
    }

    /**
     * this function creates system code
     *
     *
     * @access public
     *
     * @param string $code
     * @return $this->systemObj->create
     */
    public function createSystem($code)
    {
        return $this->systemObj->create(array(
            'SYS_CODE' => $code
        ));
    }

    /**
     * this function checks by code
     *
     *
     * @access public
     *
     * @param string $code
     * @return $this->rolesObj->verifyByCode
     */
    public function verifyByCode($code)
    {
        return $this->rolesObj->verifyByCode($code);
    }

    /**
     * this function gets all authentication source
     * Authentication Sources
     *
     * @access public
     *
     * @param void
     * @return $this->authSourcesObj->getAllAuthSources()
     */

    public function getAllAuthSources()
    {
        return $this->authSourcesObj->getAllAuthSources();
    }

    /**
     * this function gets all authentication source
     * Authentication Sources By User
     *
     * @access public
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param void
     * @return $this->authSourcesObj->getAllAuthSources()
     */

    public function getAllAuthSourcesByUser()
    {
        return $this->authSourcesObj->getAllAuthSourcesByUser();
    }

    /**
     * this function gets all authentication source
     * Authentication Sources based at parameters
     *
     * @access public
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param int $start offset value to paging grid
     * @param int $limit limit value to paging grid
     * @param string $filter value to search or filter select
     * @return $this->authSourcesObj->getAuthenticationSources()
     */

    public function getAuthenticationSources($start, $limit, $filter = '')
    {
        return $this->authSourcesObj->getAuthenticationSources($start, $limit, $filter);
    }

    /**
     * this function gets all authentication source
     * Authentication Sources
     *
     * @access public
     *
     * @param string $uid
     * @return $this->authSourcesObj->load
     */
    public function getAuthSource($uid)
    {
        $data = $this->authSourcesObj->load($uid);
        $pass = explode("_", $data['AUTH_SOURCE_PASSWORD']);
        foreach ($pass as $index => $value) {
            if ($value == '2NnV3ujj3w') {
                $data['AUTH_SOURCE_PASSWORD'] = G::decrypt($pass[0], $data['AUTH_SOURCE_SERVER_NAME']);
            }
        }
        $this->authSourcesObj->Fields = $data;

        return $this->authSourcesObj->Fields;
    }

    /**
     * this function creates an authentication source
     * Authentication Sources
     *
     * @access public
     *
     * @param array $dataCase
     * @return $this->authSourcesObj->create
     */
    public function createAuthSource($dataCase)
    {
        $dataCase['AUTH_SOURCE_PASSWORD'] = G::encrypt($dataCase['AUTH_SOURCE_PASSWORD'],
                $dataCase['AUTH_SOURCE_SERVER_NAME']) . "_2NnV3ujj3w";
        $this->authSourcesObj->create($dataCase);
    }

    /**
     * this function updates an authentication source
     * Authentication Sources
     *
     * @access public
     *
     * @param array $dataCase
     * @return $this->authSourcesObj->create
     */
    public function updateAuthSource($dataCase)
    {
        $dataCase['AUTH_SOURCE_PASSWORD'] = G::encrypt($dataCase['AUTH_SOURCE_PASSWORD'],
                $dataCase['AUTH_SOURCE_SERVER_NAME']) . "_2NnV3ujj3w";
        $this->authSourcesObj->update($dataCase);
    }

    /**
     * this function removes an authentication source
     * Authentication Sources
     *
     * @access public
     *
     * @param string $uid
     * @return $this->authSourcesObj->remove
     */
    public function removeAuthSource($uid)
    {
        $this->authSourcesObj->remove($uid);
    }

    /**
     * this function gets all users by authentication source
     *
     * @access public
     *
     * @param void
     * @return $this->userObj->getAllUsersByAuthSource()
     */

    public function getAllUsersByAuthSource()
    {
        return $this->userObj->getAllUsersByAuthSource();
    }

    /**
     * this function gets all users by authentication source
     *
     * @access public
     *
     * @param void
     * @return $this->userObj->getAllUsersByAuthSource()
     */

    public function getListUsersByAuthSource($source)
    {
        return $this->userObj->getListUsersByAuthSource($source);
    }

    /**
     * this function searchs users
     *
     *
     * @access public
     *
     * @param string $uid
     * @param string $keyword
     * @return array
     */
    public function searchUsers($uid, $keyword)
    {
        $aAuthSource = $this->getAuthSource($uid);
        $authType = strtolower($aAuthSource['AUTH_SOURCE_PROVIDER']);
        foreach ($this->aRbacPlugins as $className) {
            if (strtolower($className) == $authType) {
                $plugin = new $className();
                $plugin->sAuthSource = $uid;
                $plugin->sSystem = $this->sSystem;

                return $plugin->searchUsers($keyword);
            }
        }

        return [];
    }

    public function requirePermissions($permissions)
    {
        $numPerms = func_num_args();
        $permissions = func_get_args();

        $access = -1;

        if ($numPerms == 1) {
            $access = $this->userCanAccess($permissions[0]);
        } elseif ($numPerms > 0) {
            foreach ($permissions as $perm) {
                $access = $this->userCanAccess($perm);
                if ($access == 1) {
                    $access = 1;
                    break;
                }
            }
        } else {
            throw new Exception('function requirePermissions() ->ERROR: Parameters missing!');
        }

        if ($access == 1) {
            return true;
        } else {
            switch ($access) {
                case -2:
                    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
                    G::header('location: ../login/login');
                    break;
                case -1:
                default:
                    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
                    G::header('location: ../login/login');
                    break;
            }
            exit(0);
        }
    }

    private function getAllFiles($directory, $recursive = true)
    {
        $result = [];
        if (is_dir($directory)) {
            $handle = opendir($directory);
            while ($datei = readdir($handle)) {
                if (($datei != '.') && ($datei != '..')) {
                    $file = $directory . $datei;
                    if (is_dir($file)) {
                        if ($recursive) {
                            $result = array_merge($result, getAllFiles($file . '/'));
                        }
                    } else {
                        $result[] = $file;
                    }
                }
            }
            closedir($handle);
        }

        return $result;
    }

    private function getFilesTimestamp($directory, $recursive = true)
    {
        $allFiles = self::getAllFiles($directory, $recursive);
        $fileArray = [];
        foreach ($allFiles as $val) {
            $timeResult['file'] = $val;
            $timeResult['timestamp'] = filemtime($val);
            $fileArray[] = $timeResult;
        }

        return $fileArray;
    }

    public function cleanSessionFiles($hours = 72)
    {
        $currentTime = strtotime("now");
        $timeDifference = $hours * 60 * 60;
        $limitTime = $currentTime - $timeDifference;
        $sessionsPath = PATH_DATA . 'session' . PATH_SEP;
        $filesResult = self::getFilesTimestamp($sessionsPath);
        $count = 0;
        foreach ($filesResult as $file) {
            if ($file['timestamp'] < $limitTime) {
                unlink($file['file']);
                $count++;
            }
        }
    }

    /**
     * This function verify the permissions
     *
     * @access public
     *
     * @return array
     */
    public function verifyPermissions()
    {
        $message = [];
        $this->verifyGuestUserRolePermission();
        $listPermissions = $this->loadPermissionAdmin();
        $criteria = new Criteria('rbac');
        $dataset = PermissionsPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        while (is_array($aRow)) {
            foreach ($listPermissions as $key => $item) {
                if ($aRow['PER_UID'] == $item['PER_UID']) {
                    unset($listPermissions[$key]);
                    break;
                }
            }
            $dataset->next();
            $aRow = $dataset->getRow();
        }
        foreach ($listPermissions as $key => $item) {
            //Adding new permissions
            $data = [];
            $data['PER_UID'] = $item['PER_UID'];
            $data['PER_CODE'] = $item['PER_CODE'];
            $data['PER_CREATE_DATE'] = date('Y-m-d H:i:s');
            $data['PER_UPDATE_DATE'] = $data['PER_CREATE_DATE'];
            $data['PER_STATUS'] = 1;
            $permission = new Permissions();
            $permission->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $permission->save();
            $message[] = 'Add permission missing ' . $item['PER_CODE'];
            //Adding new labels for new permissions
            $o = new RolesPermissions();
            $o->setPerUid($item['PER_UID']);
            $o->setPermissionName($item['PER_NAME']);
            //assigning new permissions
            $this->assigningNewPermissionsPmSetup($item);
            $this->assigningNewPermissionsPmEditProfile($item);
        }

        return $message;
    }

    /**
     * Permissions for tab ADMIN
     * @param array $item
     */
    public function assigningNewPermissionsPmSetup($item = [])
    {
        if (strpos($item['PER_CODE'], 'PM_SETUP_') !== false) {
            $rolesWithPermissionSetup = $this->getRolePermissionsByPerUid(self::SETUPERMISSIONUID);
            $rolesWithPermissionSetup->next();
            while ($aRow = $rolesWithPermissionSetup->getRow()) {
                $isAssignedNewpermissions = $this->getPermissionAssignedRole($aRow['ROL_UID'], $item['PER_UID']);
                if (!$isAssignedNewpermissions) {
                    $dataPermissions = [];
                    $dataPermissions['ROL_UID'] = $aRow['ROL_UID'];
                    $dataPermissions['PER_UID'] = $item['PER_UID'];
                    $this->assignPermissionRole($dataPermissions);
                }
                $rolesWithPermissionSetup->next();
            }
        }
    }

    /**
     * Permissions for Edit Profile User
     * @param array $item
     */
    public function assigningNewPermissionsPmEditProfile($item = [])
    {
        if (strpos($item['PER_CODE'], 'PM_EDIT_USER_PROFILE_') !== false) {
            $allRolesRolUid = $this->getAllRoles('PROCESSMAKER');
            $perCodePM = array('PROCESSMAKER_ADMIN', 'PROCESSMAKER_OPERATOR', 'PROCESSMAKER_MANAGER');
            $permissionsForOperator = array(
                'PM_EDIT_USER_PROFILE_POSITION',
                'PM_EDIT_USER_PROFILE_REPLACED_BY',
                'PM_EDIT_USER_PROFILE_EXPIRATION_DATE',
                'PM_EDIT_USER_PROFILE_STATUS',
                'PM_EDIT_USER_PROFILE_ROLE',
                'PM_EDIT_USER_PROFILE_COSTS',
                'PM_EDIT_USER_PROFILE_USER_MUST_CHANGE_PASSWORD_AT_NEXT_LOGON',
                'PM_EDIT_USER_PROFILE_DEFAULT_MAIN_MENU_OPTIONS',
                'PM_EDIT_USER_PROFILE_DEFAULT_CASES_MENU_OPTIONS'
            );
            foreach ($allRolesRolUid as $index => $aRow) {
                $isAssignedNewpermissions = $this->getPermissionAssignedRole($aRow['ROL_UID'], $item['PER_UID']);
                $assignPermissions = true;
                if (!$isAssignedNewpermissions) {
                    if ($aRow['ROL_CODE'] == 'PROCESSMAKER_OPERATOR' && in_array($item['PER_CODE'],
                            $permissionsForOperator)) {
                        $assignPermissions = false;
                    }
                    if (!in_array($aRow['ROL_CODE'], $perCodePM)) {
                        $assignPermissions = false;
                        $checkPermisionEdit = $this->getPermissionAssignedRole($aRow['ROL_UID'],
                            '00000000000000000000000000000014');
                        if ($checkPermisionEdit && !in_array($item['PER_CODE'], $permissionsForOperator)) {
                            $assignPermissions = true;
                        }
                    }
                    if ($assignPermissions) {
                        $dataPermissions = [];
                        $dataPermissions['ROL_UID'] = $aRow['ROL_UID'];
                        $dataPermissions['PER_UID'] = $item['PER_UID'];
                        $this->assignPermissionRole($dataPermissions);
                    }
                }
            }
        }
    }

    /**
     * This function verify if the user allows to the file with a specific action
     * If the action is not defined in the authorizedActions we give the allow
     * @param string $file
     * @param string $action
     *
     * @return void
     * @throws RBACException
     */
    public function allows($file, $action)
    {
        $access = false;
        if (isset($this->authorizedActions[$file][$action])) {
            $permissions = $this->authorizedActions[$file][$action];
            $totalPermissions = count($permissions);
            $countAccess = 0;
            foreach ($permissions as $key => $value) {
                if ($this->userCanAccess($value) == 1) {
                    $countAccess++;
                }
            }
            //Check if the user has all permissions that needed
            if ($countAccess == $totalPermissions) {
                $access = true;
            }
        }

        if (!$access) {
            throw new RBACException('ID_ACCESS_DENIED', 403);
        }
    }

    /**
     * Enable compatibility with hash login
     */
    public function enableLoginWithHash()
    {
        $this->enableLoginHash = true;
    }

    /**
     * Disable compatibility with hash login
     */
    public function disableLoginWithHash()
    {
        $this->enableLoginHash = false;
    }

    /**
     * Return status login with hash
     *
     * @return bool
     */
    public function loginWithHash()
    {
        return $this->enableLoginHash;
    }

    /**
     * Returns true in case the parameter corresponds to the invited user,
     * otherwise it returns false.
     *
     * @param string $usrUid
     * @return boolean
     */
    public static function isGuestUserUid($usrUid)
    {
        return self::GUEST_USER_UID === $usrUid;
    }
}

