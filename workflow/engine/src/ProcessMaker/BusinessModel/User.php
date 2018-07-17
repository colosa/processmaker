<?php

namespace ProcessMaker\BusinessModel;

use AppCacheView;
use ApplicationPeer;
use BasePeer;
use Bootstrap;
use Calendar;
use CalendarDefinitionPeer;
use Cases as ClassesCases;
use Configurations;
use Criteria;
use DashletInstancePeer;
use DateTimeZone;
use Department;
use Exception;
use G;
use Groups;
use IsoCountryPeer;
use IsoLocationPeer;
use IsoSubdivisionPeer;
use ListParticipatedLast;
use PMmemcached;
use ProcessMaker\Plugins\PluginRegistry;
use ProcessMaker\Util\DateTime;
use ProcessMaker\Util\System;
use Propel;
use RBAC;
use RbacUsers;
use RbacUsersPeer;
use ResultSet;
use Roles;
use RolesPeer;
use Systems;
use Tasks;
use Users;
use UsersPeer;
use UsersProperties;
use UsersRoles;
use UsersRolesPeer;

class User
{
    private $arrayFieldDefinition = array(
        "USR_UID" => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrUid"),
        "USR_FIRSTNAME" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrFirstname"),
        "USR_LASTNAME" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrLastname"),
        "USR_USERNAME" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrUsername"),
        "USR_EMAIL" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrEmail"),
        "USR_ADDRESS" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrAddress"),
        "USR_ZIP_CODE" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrZipCode"),
        "USR_COUNTRY" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrCountry"),
        "USR_CITY" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrCity"),
        "USR_LOCATION" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrLocation"),
        "USR_PHONE" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrPhone"),
        "USR_POSITION" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrPosition"),
        "USR_REPLACED_BY" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrReplacedBy"),
        "USR_DUE_DATE" => array("type" => "date", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrDueDate"),
        "USR_CALENDAR" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrCalendar"),
        "USR_STATUS" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE", "VACATION"), "fieldNameAux" => "usrStatus"),
        "USR_ROLE" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrRole"),
        "USR_NEW_PASS" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "usrNewPass"),
        "USR_UX" => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("NORMAL", "SIMPLIFIED", "SWITCHABLE", "SINGLE"), "fieldNameAux" => "usrUx"),
        "DEP_UID" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "depUid"),
        "USR_BIRTHDAY" => array("type" => "date", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrBirthday"),
        "USR_FAX" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrFax"),
        "USR_CELLULAR" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "usrCellular"),
        /*----------------------------------********---------------------------------*/
        'USR_LOGGED_NEXT_TIME' => ['type' => 'int', 'required' => false, 'empty' => false, 'defaultValues' => [0, 1], 'fieldNameAux' => 'usrLoggedNextTime'],
        'USR_TIME_ZONE' => ['type' => 'string', 'required' => false, 'empty' => true, 'defaultValues' => [], 'fieldNameAux' => 'usrTimeZone']
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "usrPhoto" => "USR_PHOTO"
    );

    private $arrayPermissionsForEditUser = array(
        'USR_FIRSTNAME' => 'PM_EDIT_USER_PROFILE_FIRST_NAME',
        'USR_LASTNAME' => 'PM_EDIT_USER_PROFILE_LAST_NAME',
        'USR_USERNAME' => 'PM_EDIT_USER_PROFILE_USERNAME',
        'USR_EMAIL' => 'PM_EDIT_USER_PROFILE_EMAIL',
        'USR_ADDRESS' => 'PM_EDIT_USER_PROFILE_ADDRESS',
        'USR_ZIP_CODE' => 'PM_EDIT_USER_PROFILE_ZIP_CODE',
        'USR_COUNTRY' => 'PM_EDIT_USER_PROFILE_COUNTRY',
        'USR_REGION' => 'PM_EDIT_USER_PROFILE_STATE_OR_REGION',
        'USR_LOCATION' => 'PM_EDIT_USER_PROFILE_LOCATION',
        'USR_PHONE' => 'PM_EDIT_USER_PROFILE_PHONE',
        'USR_POSITION' => 'PM_EDIT_USER_PROFILE_POSITION',
        'USR_REPLACED_BY' => 'PM_EDIT_USER_PROFILE_REPLACED_BY',
        'USR_DUE_DATE' => 'PM_EDIT_USER_PROFILE_EXPIRATION_DATE',
        'USR_CALENDAR' => 'PM_EDIT_USER_PROFILE_CALENDAR',
        'USR_STATUS' => 'PM_EDIT_USER_PROFILE_STATUS',
        'USR_ROLE' => 'PM_EDIT_USER_PROFILE_ROLE',
        'USR_TIME_ZONE' => 'PM_EDIT_USER_PROFILE_TIME_ZONE',
        'USR_DEFAULT_LANG' => 'PM_EDIT_USER_PROFILE_DEFAULT_LANGUAGE',
        'USR_COST_BY_HOUR' => 'PM_EDIT_USER_PROFILE_COSTS',
        'USR_UNIT_COST' => 'PM_EDIT_USER_PROFILE_COSTS',
        'USR_CUR_PASS' => 'PM_EDIT_USER_PROFILE_PASSWORD',
        'USR_NEW_PASS' => 'PM_EDIT_USER_PROFILE_PASSWORD',
        'USR_CNF_PASS' => 'PM_EDIT_USER_PROFILE_PASSWORD',
        'USR_LOGGED_NEXT_TIME' => 'PM_EDIT_USER_PROFILE_USER_MUST_CHANGE_PASSWORD_AT_NEXT_LOGON',
        'USR_PHOTO' => 'PM_EDIT_USER_PROFILE_PHOTO',
        'PREF_DEFAULT_MENUSELECTED' => 'PM_EDIT_USER_PROFILE_DEFAULT_MAIN_MENU_OPTIONS',
        'PREF_DEFAULT_CASES_MENUSELECTED' => 'PM_EDIT_USER_PROFILE_DEFAULT_CASES_MENU_OPTIONS'
    );

    private $guestUser = RBAC::GUEST_USER_UID;

    /**
     * Constructor of the class
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This function get the guest user defined
     *
     * @return string guestUser, uid related to this user
     */
    public function getGuestUser()
    {
        return $this->guestUser;
    }

    /**
     * @return array
     */
    public function getPermissionsForEdit()
    {
        return $this->arrayPermissionsForEditUser;
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     * @param bool $flag Value that set the format
     * @throws Exception
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception users for fields
     * @param array $arrayData Data with the fields
     * @throws Exception
     */
    public function setArrayFieldNameForException(array $arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     * @param string $fieldName Field name
     * @return string Return the field name according the format
     * @throws Exception
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase) ? strtoupper($fieldName) : strtolower($fieldName);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Name of a User
     * @param string $userName Name
     * @param string $userUidToExclude Unique id of User to exclude
     * @return bool Return true if exists the Name of a User, false otherwise
     * @throws Exception
     */
    public function existsName($userName, $userUidToExclude = "")
    {
        try {
            /** @var Criteria $criteria */
            $criteria = $this->getUserCriteria();

            if ($userUidToExclude != "") {
                $criteria->add(UsersPeer::USR_UID, $userUidToExclude, Criteria::NOT_EQUAL);
            }

            $criteria->add(UsersPeer::USR_USERNAME, $userName, Criteria::EQUAL);

            //QUERY
            $rsCriteria = UsersPeer::doSelectRS($criteria);

            return ($rsCriteria->next()) ? true : false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Name of a User
     * @param string $userName Name
     * @param string $fieldNameForException Field name for the exception
     * @param string $userUidToExclude Unique id of User to exclude
     * @throws Exception if exists the title of a User
     */
    public function throwExceptionIfExistsName($userName, $fieldNameForException, $userUidToExclude = "")
    {
        try {
            if ($this->existsName($userName, $userUidToExclude)) {
                throw new Exception(G::LoadTranslation("ID_USER_NAME_ALREADY_EXISTS", array($fieldNameForException, $userName)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify password
     * @param string $userPassword Password
     * @param string $fieldNameForException Field name for the exception
     * @throws Exception if password is invalid
     */
    public function throwExceptionIfPasswordIsInvalid($userPassword, $fieldNameForException)
    {
        try {
            $result = $this->testPassword($userPassword);

            if (!$result["STATUS"]) {
                throw new Exception($fieldNameForException . ": " . $result["DESCRIPTION"]);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     * @param string $userUid Unique id of User
     * @param array $arrayData Data
     * @throws Exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($userUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayUserData = ($userUid == "") ? array() : $this->getUser($userUid, true);
            $flagInsert = ($userUid == "") ? true : false;

            $arrayFinalData = array_merge($arrayUserData, $arrayData);

            //Verify data - Field definition.
            $process = new Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            //Verify data
            if (isset($arrayData["USR_USERNAME"])) {
                $this->throwExceptionIfExistsName($arrayData["USR_USERNAME"], $this->arrayFieldNameForException["usrUsername"], $userUid);
            }

            if (isset($arrayData["USR_EMAIL"])) {
                if (!filter_var($arrayData["USR_EMAIL"], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception($this->arrayFieldNameForException["usrEmail"] . ": " . G::LoadTranslation("ID_INCORRECT_EMAIL"));
                }
            }

            if (isset($arrayData["USR_NEW_PASS"])) {
                $this->throwExceptionIfPasswordIsInvalid($arrayData["USR_NEW_PASS"], $this->arrayFieldNameForException["usrNewPass"]);
            }

            if (isset($arrayData["USR_REPLACED_BY"]) && $arrayData["USR_REPLACED_BY"] != "") {
                $obj = UsersPeer::retrieveByPK($arrayData["USR_REPLACED_BY"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_USER_DOES_NOT_EXIST", array($this->arrayFieldNameForException["usrReplacedBy"], $arrayData["USR_REPLACED_BY"])));
                }
            }

            if (isset($arrayData["USR_DUE_DATE"])) {
                $arrayUserDueDate = explode("-", $arrayData["USR_DUE_DATE"]);

                if (ctype_digit($arrayUserDueDate[0])) {
                    if (!checkdate($arrayUserDueDate[1], $arrayUserDueDate[2], $arrayUserDueDate[0])) {
                        throw new Exception($this->arrayFieldNameForException["usrDueDate"] . ": " . G::LoadTranslation("ID_MSG_ERROR_DUE_DATE"));
                    }
                } else {
                    throw new Exception($this->arrayFieldNameForException["usrDueDate"] . ": " . G::LoadTranslation("ID_MSG_ERROR_DUE_DATE"));
                }
            }

            if (isset($arrayData["USR_ROLE"])) {
                require_once(PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Roles.php");

                $criteria = new Criteria("rbac");

                $criteria->add(RolesPeer::ROL_CODE, $arrayData["USR_ROLE"]);
                $rsCriteria = RolesPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrRole"])));
                }
            }

            if (isset($arrayData["USR_COUNTRY"]) && $arrayData["USR_COUNTRY"] != "") {
                $obj = IsoCountryPeer::retrieveByPK($arrayData["USR_COUNTRY"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));
                }
            }

            if (isset($arrayData["USR_CITY"]) && $arrayData["USR_CITY"] != "") {
                if (!isset($arrayFinalData["USR_COUNTRY"]) || $arrayFinalData["USR_COUNTRY"] == "") {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));
                }

                $obj = IsoSubdivisionPeer::retrieveByPK($arrayFinalData["USR_COUNTRY"], $arrayData["USR_CITY"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCity"])));
                }
            }

            if (isset($arrayData["USR_LOCATION"]) && $arrayData["USR_LOCATION"] != "") {
                if (!isset($arrayFinalData["USR_COUNTRY"]) || $arrayFinalData["USR_COUNTRY"] == "") {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));
                }

                $obj = IsoLocationPeer::retrieveByPK($arrayFinalData["USR_COUNTRY"], $arrayData["USR_LOCATION"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrLocation"])));
                }
            }

            if (isset($arrayData["USR_CALENDAR"]) && $arrayData["USR_CALENDAR"] != "") {
                $obj = CalendarDefinitionPeer::retrieveByPK($arrayData["USR_CALENDAR"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_CALENDAR_DOES_NOT_EXIST", array($this->arrayFieldNameForException["usrCalendar"], $arrayData["USR_CALENDAR"])));
                }
            }

            if (isset($arrayData["DEP_UID"]) && $arrayData["DEP_UID"] != "") {
                $department = new Department();

                if (!$department->existsDepartment($arrayData["DEP_UID"])) {
                    throw new Exception(G::LoadTranslation("ID_DEPARTMENT_NOT_EXIST", array($this->arrayFieldNameForException["depUid"], $arrayData["DEP_UID"])));
                }
            }

            if (isset($arrayData['USR_TIME_ZONE']) && $arrayData['USR_TIME_ZONE'] != '') {
                if (!in_array($arrayData['USR_TIME_ZONE'], DateTimeZone::listIdentifiers())) {
                    throw new Exception(G::LoadTranslation('ID_TIME_ZONE_DOES_NOT_EXIST', [$this->arrayFieldNameForException['usrTimeZone'], $arrayData['USR_TIME_ZONE']]));
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the User in table USERS
     * @param string $userUid Unique id of Email Server
     * @param string $fieldNameForException Field name for the exception
     * @throws Exception if does not exist the User in table USERS
     */
    public function throwExceptionIfNotExistsUser($userUid, $fieldNameForException)
    {
        try {
            $obj = UsersPeer::retrieveByPK($userUid);

            if (is_null($obj) || $obj->getUsrUsername() == "") {
                throw new Exception(G::LoadTranslation("ID_USER_DOES_NOT_EXIST", array($fieldNameForException, $userUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get User record
     * @param string $userUid Unique id of User
     * @param array $arrayVariableNameForException Variable name for exception
     * @param bool $throwException Flag to throw the exception if the main parameters are invalid or do not exist
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     * @return array|bool Returns an array with User record, ThrowTheException/FALSE otherwise
     * @throws Exception
     */
    public function getUserRecordByPk($userUid, array $arrayVariableNameForException, $throwException = true)
    {
        try {
            $obj = UsersPeer::retrieveByPK($userUid);

            if (is_null($obj)) {
                if ($throwException) {
                    throw new Exception(G::LoadTranslation(
                        'ID_USER_DOES_NOT_EXIST',
                        [$arrayVariableNameForException['$userUid'], $userUid]
                    ));
                } else {
                    return false;
                }
            }

            //Return
            return $obj->toArray(BasePeer::TYPE_FIELDNAME);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get custom record
     * @param array $record Record
     * @return array Return an array with custom record
     * @throws Exception
     */
    private function __getUserCustomRecordFromRecord(array $record)
    {
        try {
            //Get Calendar
            $calendar = new Calendar();
            $calendarInfo = $calendar->getCalendarFor($record["USR_UID"], "", "");
            $aFields["USR_CALENDAR_UID"] = ($calendarInfo["CALENDAR_APPLIED"] != "DEFAULT") ? $calendarInfo["CALENDAR_UID"] : "";
            $aFields["USR_CALENDAR"] = ($aFields["USR_CALENDAR_UID"] != "") ? $calendar->calendarName($aFields["USR_CALENDAR_UID"]) : $aFields["USR_CALENDAR_UID"];

            //Get photo
            $pathPhotoUser = PATH_IMAGES_ENVIRONMENT_USERS . $record["USR_UID"] . ".gif";
            if (!file_exists($pathPhotoUser)) {
                $pathPhotoUser = PATH_HOME . "public_html" . PATH_SEP . "images" . PATH_SEP . "user.gif";
            }

            $arrayResult = [];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_UID')] = $record['USR_UID'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_USERNAME')] = $record['USR_USERNAME'];
            //$arrayResult[$this->getFieldNameByFormatFieldName('USR_PASSWORD')]           = $record['USR_PASSWORD'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_FIRSTNAME')] = $record['USR_FIRSTNAME'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_LASTNAME')] = $record['USR_LASTNAME'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_EMAIL')] = $record['USR_EMAIL'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_DUE_DATE')] = $record['USR_DUE_DATE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_CREATE_DATE')] = $record['USR_CREATE_DATE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_UPDATE_DATE')] = $record['USR_UPDATE_DATE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_STATUS')] = $record['USR_STATUS'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_COUNTRY')] = $record['USR_COUNTRY'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_CITY')] = $record['USR_CITY'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_LOCATION')] = $record['USR_LOCATION'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_ADDRESS')] = $record['USR_ADDRESS'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_PHONE')] = $record['USR_PHONE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_FAX')] = $record['USR_FAX'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_CELLULAR')] = $record['USR_CELLULAR'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_ZIP_CODE')] = $record['USR_ZIP_CODE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('DEP_UID')] = $record['DEP_UID'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_POSITION')] = $record['USR_POSITION'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_RESUME')] = $record['USR_RESUME'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_BIRTHDAY')] = $record['USR_BIRTHDAY'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_ROLE')] = $record['USR_ROLE'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_REPORTS_TO')] = $record['USR_REPORTS_TO'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_REPLACED_BY')] = $record['USR_REPLACED_BY'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_CALENDAR_UID')] = $aFields['USR_CALENDAR_UID'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_CALENDAR_NAME')] = $aFields['USR_CALENDAR'];
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_UX')] = $record['USR_UX'];
            /*----------------------------------********---------------------------------*/
            $arrayResult[$this->getFieldNameByFormatFieldName('USR_PHOTO_PATH')] = $pathPhotoUser;

            if (isset($_SESSION['__SYSTEM_UTC_TIME_ZONE__']) && $_SESSION['__SYSTEM_UTC_TIME_ZONE__']) {
                $arrayResult[$this->getFieldNameByFormatFieldName('USR_TIME_ZONE')] = (trim($record['USR_TIME_ZONE']) != '') ? trim($record['USR_TIME_ZONE']) : System::getTimeZone();
            }

            //Return
            return $arrayResult;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for User
     * @return object
     * @throws Exception
     */
    public function getUserCriteria()
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(UsersPeer::USR_UID);
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_PASSWORD);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
            $criteria->addSelectColumn(UsersPeer::USR_DUE_DATE);
            $criteria->addSelectColumn(UsersPeer::USR_CREATE_DATE);
            $criteria->addSelectColumn(UsersPeer::USR_UPDATE_DATE);
            $criteria->addSelectColumn(UsersPeer::USR_STATUS);
            $criteria->addSelectColumn(UsersPeer::USR_COUNTRY);
            $criteria->addSelectColumn(UsersPeer::USR_CITY);
            $criteria->addSelectColumn(UsersPeer::USR_LOCATION);
            $criteria->addSelectColumn(UsersPeer::USR_ADDRESS);
            $criteria->addSelectColumn(UsersPeer::USR_PHONE);
            $criteria->addSelectColumn(UsersPeer::USR_FAX);
            $criteria->addSelectColumn(UsersPeer::USR_CELLULAR);
            $criteria->addSelectColumn(UsersPeer::USR_ZIP_CODE);
            $criteria->addSelectColumn(UsersPeer::DEP_UID);
            $criteria->addSelectColumn(UsersPeer::USR_POSITION);
            $criteria->addSelectColumn(UsersPeer::USR_RESUME);
            $criteria->addSelectColumn(UsersPeer::USR_BIRTHDAY);
            $criteria->addSelectColumn(UsersPeer::USR_ROLE);
            $criteria->addSelectColumn(UsersPeer::USR_REPORTS_TO);
            $criteria->addSelectColumn(UsersPeer::USR_REPLACED_BY);
            $criteria->addSelectColumn(UsersPeer::USR_UX);
            /*----------------------------------********---------------------------------*/
            $criteria->addSelectColumn(UsersPeer::USR_TIME_ZONE);

            //Return
            return $criteria;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create User
     * @param array $arrayData Data
     * @return array Return data of the new User created
     * @throws Exception
     */
    public function create(array $arrayData)
    {
        try {


            //Verify data
            $validator = new Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayDataAux = array_change_key_case($arrayData, CASE_UPPER);
            $arrayData = $arrayDataAux;

            unset(
                $arrayData['USR_UID'],
                $arrayData['USR_COST_BY_HOUR'],
                $arrayData['USR_UNIT_COST']
            );

            /*----------------------------------********---------------------------------*/

            $this->throwExceptionIfDataIsInvalid("", $arrayData);

            //Create
            $cnn = Propel::getConnection("workflow");

            try {
                $rbac = new RBAC();
                $user = new Users();

                $rbac->initRBAC();

                $arrayData["USR_PASSWORD"] = Bootstrap::hashPassword($arrayData["USR_NEW_PASS"]);

                $arrayData["USR_BIRTHDAY"] = (isset($arrayData["USR_BIRTHDAY"])) ? $arrayData["USR_BIRTHDAY"] : date("Y-m-d");
                $arrayData["USR_LOGGED_NEXT_TIME"] = (isset($arrayData["USR_LOGGED_NEXT_TIME"])) ? $arrayData["USR_LOGGED_NEXT_TIME"] : 0;
                $arrayData["USR_CREATE_DATE"] = date("Y-m-d H:i:s");
                $arrayData["USR_UPDATE_DATE"] = date("Y-m-d H:i:s");

                $userUid = $rbac->createUser($arrayData, $arrayData["USR_ROLE"]);

                //Create in workflow
                $arrayData["USR_UID"] = $userUid;
                $arrayData["USR_PASSWORD"] = "00000000000000000000000000000000";

                $result = $user->create($arrayData);

                //User Properties
                $userProperty = new UsersProperties();

                $aUserProperty = $userProperty->loadOrCreateIfNotExists($arrayData["USR_UID"], array("USR_PASSWORD_HISTORY" => serialize(array(Bootstrap::hashPassword($arrayData["USR_PASSWORD"])))));
                $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];

                $userProperty->update($aUserProperty);

                //Save Calendar assigment
                if (isset($arrayData["USR_CALENDAR"])) {
                    //Save Calendar ID for this user

                    $calendar = new Calendar();
                    $calendar->assignCalendarTo($arrayData["USR_UID"], $arrayData["USR_CALENDAR"], "USER");
                }

                //Return
                return $this->getUser($userUid);
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update User
     * @param string $userUid Unique id of User
     * @param array $arrayData Data
     * @param string $userUidLogged Unique id of User logged
     * @return array data of the User updated
     * @throws Exception
     */
    public function update($userUid, array $arrayData, $userUidLogged)
    {
        try {

            //check user guest
            if (RBAC::isGuestUserUid($userUid)) {
                throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($userUid)));
            }

            //Verify data
            $validator = new Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayDataAux = array_change_key_case($arrayData, CASE_UPPER);
            $arrayData = $arrayDataAux;
            $arrayDataBackup = $arrayData;

            unset(
                $arrayData['USR_COST_BY_HOUR'],
                $arrayData['USR_UNIT_COST']
            );

            /*----------------------------------********---------------------------------*/

            //Verify data
            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);

            $this->throwExceptionIfDataIsInvalid($userUid, $arrayData);

            //Permission Admin
            $countPermission = 0;

            $permission = $this->loadUserRolePermission("PROCESSMAKER", $userUidLogged);

            foreach ($permission as $key => $value) {
                if (preg_match('/^(?:PM_USERS|PM_EDITPERSONALINFO)$/', $value['PER_CODE'])) {
                    $countPermission = $countPermission + 1;
                    break;
                }
            }

            if ($countPermission == 0) {
                throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($userUidLogged)));
            }

            //Update
            $cnn = Propel::getConnection("workflow");

            try {
                $rbac = new RBAC();
                $user = new Users();

                $rbac->initRBAC();

                if (isset($arrayData['USR_PASSWORD'])) {
                    $arrayData['USR_PASSWORD'] = Bootstrap::hashPassword($arrayData['USR_PASSWORD']);
                } else {
                    if (isset($arrayData['USR_NEW_PASS'])) {
                        $arrayData['USR_PASSWORD'] = Bootstrap::hashPassword($arrayData['USR_NEW_PASS']);
                    }
                }

                $arrayData["USR_UID"] = $userUid;
                $arrayData["USR_LOGGED_NEXT_TIME"] = (isset($arrayData["USR_LOGGED_NEXT_TIME"])) ? $arrayData["USR_LOGGED_NEXT_TIME"] : 0;
                $arrayData["USR_UPDATE_DATE"] = date("Y-m-d H:i:s");

                $flagUserLoggedNextTime = false;

                if (isset($arrayData["USR_PASSWORD"])) {
                    if ($arrayData["USR_PASSWORD"] != "") {
                        //require_once 'classes/model/UsersProperties.php';

                        $userProperty = new UsersProperties();
                        $aUserProperty = $userProperty->loadOrCreateIfNotExists($userUid, array("USR_PASSWORD_HISTORY" => serialize(array(Bootstrap::hashPassword($arrayData["USR_PASSWORD"])))));

                        $memKey = "rbacSession" . session_id();
                        $memcache = &PMmemcached::getSingleton(defined("SYS_SYS") ? SYS_SYS : "");

                        if (($rbac->aUserInfo = $memcache->get($memKey)) == false) {
                            $rbac->loadUserRolePermission("PROCESSMAKER", $userUidLogged);
                            $memcache->set($memKey, $rbac->aUserInfo, PMmemcached::EIGHT_HOURS);
                        }

                        if ($rbac->aUserInfo["PROCESSMAKER"]["ROLE"]["ROL_CODE"] == "PROCESSMAKER_ADMIN") {
                            $aUserProperty["USR_LAST_UPDATE_DATE"] = date("Y-m-d H:i:s");
                            $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];
                            $userProperty->update($aUserProperty);
                        }

                        $aHistory = unserialize($aUserProperty["USR_PASSWORD_HISTORY"]);

                        if (!is_array($aHistory)) {
                            $aHistory = array();
                        }

                        if (!defined("PPP_PASSWORD_HISTORY")) {
                            define("PPP_PASSWORD_HISTORY", 0);
                        }

                        if (PPP_PASSWORD_HISTORY > 0) {
                            //it's looking a password igual into aHistory array that was send for post in md5 way
                            $c = 0;
                            $sw = 1;

                            while (count($aHistory) >= 1 && count($aHistory) > $c && $sw) {
                                if (strcmp(trim($aHistory[$c]), trim($arrayData['USR_PASSWORD'])) == 0) {
                                    $sw = 0;
                                }

                                $c++;
                            }

                            if ($sw == 0) {
                                $sDescription = G::LoadTranslation("ID_POLICY_ALERT") . ":\n\n";
                                $sDescription = $sDescription . " - " . G::LoadTranslation("PASSWORD_HISTORY") . ": " . PPP_PASSWORD_HISTORY . "\n";
                                $sDescription = $sDescription . "\n" . G::LoadTranslation("ID_PLEASE_CHANGE_PASSWORD_POLICY") . "";

                                throw new Exception($this->arrayFieldNameForException["usrNewPass"] . ": " . $sDescription);
                            }

                            if (count($aHistory) >= PPP_PASSWORD_HISTORY) {
                                $sLastPassw = array_shift($aHistory);
                            }

                            $aHistory[] = $arrayData["USR_PASSWORD"];
                        }

                        $aUserProperty["USR_LAST_UPDATE_DATE"] = date("Y-m-d H:i:s");
                        $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];
                        $aUserProperty["USR_PASSWORD_HISTORY"] = serialize($aHistory);
                        $userProperty->update($aUserProperty);
                    } else {
                        $flagUserLoggedNextTime = true;
                    }
                } else {
                    $flagUserLoggedNextTime = true;
                }

                if ($flagUserLoggedNextTime) {
                    $oUser = new Users();
                    $aUser = $oUser->load($userUid);
                    $oUserProperty = new UsersProperties();
                    $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($userUid, array("USR_PASSWORD_HISTORY" => serialize(array($oUser->getUsrPassword()))));
                    $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];
                    $oUserProperty->update($aUserProperty);
                }

                //Update in rbac
                $rbac->userObj = new RbacUsers();
                if (isset($arrayData["USR_ROLE"])) {
                    $rbac->updateUser($arrayData, $arrayData["USR_ROLE"]);
                } else {
                    $rbac->updateUser($arrayData);
                }

                //Update in workflow
                $result = $user->update($arrayData);

                //Save Calendar assigment
                if (isset($arrayData["USR_CALENDAR"])) {
                    //Save Calendar ID for this user

                    $calendar = new Calendar();
                    $calendar->assignCalendarTo($userUid, $arrayData["USR_CALENDAR"], "USER");
                }

                //Return
                $arrayData = $arrayDataBackup;

                if (!$this->formatFieldNameInUppercase) {
                    $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                }

                return $arrayData;
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a User
     * @param string $userUid Unique id of User
     * @param bool $flagGetRecord Value that set the getting
     * @return array Return an array with data of a User
     * @throws Exception
     */
    public function getUser($userUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);

            //Get data
            //SQL
            /** @var Criteria $criteria */
            $criteria = $this->getUserCriteria();

            $criteria->add(UsersPeer::USR_UID, $userUid, Criteria::EQUAL);

            $rsCriteria = UsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord) ? $this->__getUserCustomRecordFromRecord($row) : $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create User Uid
     * @param array $userData Data
     * @return int
     * @throws Exception
     */
    public function createUser($userData)
    {
        $this->userObj = new RbacUsers();
        if (class_exists('ProcessMaker\Plugins\PluginRegistry')) {
            $pluginRegistry = PluginRegistry::loadSingleton();
            if ($pluginRegistry->existsTrigger(PM_BEFORE_CREATE_USER)) {
                try {
                    $pluginRegistry->executeTriggers(PM_BEFORE_CREATE_USER, null);
                } catch (Exception $error) {
                    throw new Exception($error->getMessage());
                }
            }
        }
        $oConnection = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $oRBACUsers = new RbacUsers();
            do {
                $userData['USR_UID'] = G::generateUniqueID();
            } while ($oRBACUsers->load($userData['USR_UID']));
            $oRBACUsers->fromArray($userData, BasePeer::TYPE_FIELDNAME);
            $iResult = $oRBACUsers->save();
            return $userData['USR_UID'];
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    /**
     * to put role an user
     *
     * @access public
     * @param string $sUserUID
     * @param string $sRolCode
     * @return void
     */
    public function assignRoleToUser($sUserUID = '', $sRolCode = '')
    {
        $this->usersRolesObj = new UsersRoles();
        $this->rolesObj = new Roles();
        $aRol = $this->rolesObj->loadByCode($sRolCode);
        $this->usersRolesObj->create($sUserUID, $aRol['ROL_UID']);
    }

    /**
     * to test Password
     *
     * @access public
     * @param string $sPassword
     * @return array
     */
    public function testPassword($sPassword = '')
    {
        $oUserProperty = new UsersProperties();
        $aFields = array();
        $dateNow = date('Y-m-d H:i:s');
        $aErrors = $oUserProperty->validatePassword($sPassword, $dateNow, 0);
        if (!empty($aErrors)) {
            if (!defined('NO_DISPLAY_USERNAME')) {
                define('NO_DISPLAY_USERNAME', 1);
            }
            $aFields = array();
            $aFields['DESCRIPTION'] = G::LoadTranslation('ID_POLICY_ALERT');
            foreach ($aErrors as $sError) {
                switch ($sError) {
                    case 'ID_PPP_MINIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ': ' . PPP_MINIMUM_LENGTH . '. ';
                        $aFields[substr($sError, 3)] = PPP_MINIMUM_LENGTH;
                        break;
                    case 'ID_PPP_MAXIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ': ' . PPP_MAXIMUM_LENGTH . '. ';
                        $aFields[substr($sError, 3)] = PPP_MAXIMUM_LENGTH;
                        break;
                    case 'ID_PPP_EXPIRATION_IN':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '. ';
                        $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;
                        break;
                    default:
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError);
                        $aFields[substr($sError, 3)] = 1;
                        break;
                }
            }
            $aFields['DESCRIPTION'] .= G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');
            $aFields['STATUS'] = false;
        } else {
            $aFields['DESCRIPTION'] = G::LoadTranslation('ID_PASSWORD_COMPLIES_POLICIES');
            $aFields['STATUS'] = true;
        }
        return $aFields;
    }

    /**
     * change status of an user
     * @access public
     * @param string $sUserUID
     * @param string $sStatus
     */
    public function changeUserStatus($sUserUID = '', $sStatus = 'ACTIVE')
    {
        $this->userObj = new RbacUsers();
        if ($sStatus === 'ACTIVE') {
            $sStatus = 1;
        }
        $aFields = $this->userObj->load($sUserUID);
        $aFields['USR_STATUS'] = $sStatus;
        $this->userObj->update($aFields);
    }

    /**
     * remove a role from an user
     *
     * @access public
     * @param string $sUserUID
     * @return void
     */
    public function removeRolesFromUser($sUserUID = '')
    {
        $oCriteria = new Criteria('rbac');
        $oCriteria->add(UsersRolesPeer::USR_UID, $sUserUID);
        UsersRolesPeer::doDelete($oCriteria);
    }

    /**
     * updated an user
     *
     * @access public
     * @param array $userData
     * @param string $sRolCode
     * @return void
     */
    public function updateUser($userData = array(), $sRolCode = '')
    {
        $this->userObj = new RbacUsers();
        if (isset($userData['USR_STATUS'])) {
            if ($userData['USR_STATUS'] == 'ACTIVE') {
                $userData['USR_STATUS'] = 1;
            }
        }
        $this->userObj->update($userData);
        if ($sRolCode != '') {
            $this->removeRolesFromUser($userData['USR_UID']);
            $this->assignRoleToUser($userData['USR_UID'], $sRolCode);
        }
    }

    /**
     * Gets the roles and permission for one RBAC_user
     *
     * gets the Role and their permissions for one User
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
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
        $this->usersRolesObj = new UsersRoles();
        $this->systemObj = new Systems();
        $fieldsSystem = $this->systemObj->loadByCode($sSystem);
        $fieldsRoles = $this->usersRolesObj->getRolesBySystem($fieldsSystem['SYS_UID'], $sUser);
        $fieldsPermissions = $this->usersRolesObj->getAllPermissions($fieldsRoles['ROL_UID'], $sUser);
        $this->userObj = new RbacUsers();
        $this->aUserInfo['USER_INFO'] = $this->userObj->load($sUser);
        $this->aUserInfo[$sSystem]['SYS_UID'] = $fieldsSystem['SYS_UID'];
        $this->aUserInfo[$sSystem]['ROLE'] = $fieldsRoles;
        $this->aUserInfo[$sSystem]['PERMISSIONS'] = $fieldsPermissions;
        return $fieldsPermissions;
    }

    /**
     * Authenticate User
     * @param array $arrayUserData Data
     * @throws Exception
     */
    public function authenticate($arrayUserData)
    {
        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete User
     * @param string $usrUid Unique id of User
     * @throws Exception
     */
    public function delete($usrUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsUser($usrUid, $this->arrayFieldNameForException["usrUid"]);

            $oProcessMap = new ClassesCases();
            $USR_UID = $usrUid;
            $total = 0;
            $history = 0;
            $c = $oProcessMap->getCriteriaUsersCases('TO_DO', $USR_UID);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('DRAFT', $USR_UID);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('COMPLETED', $USR_UID);
            $history += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('CANCELLED', $USR_UID);
            $history += ApplicationPeer::doCount($c);
            
            //check user guest
            if (RBAC::isGuestUserUid($usrUid)) {
                throw new Exception(G::LoadTranslation("ID_MSG_CANNOT_DELETE_USER", array($USR_UID)));
            }

            if ($total > 0) {
                throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_BE_DELETED", array($USR_UID)));
            } else {
                $UID = $usrUid;
                $oTasks = new Tasks();
                $oTasks->ofToAssignUserOfAllTasks($UID);
                $oGroups = new Groups();
                $oGroups->removeUserOfAllGroups($UID);
                $this->changeUserStatus($UID, 'CLOSED');
                $_GET['USR_USERNAME'] = '';
                $this->updateUser(array('USR_UID' => $UID, 'USR_USERNAME' => $_GET['USR_USERNAME']), '');
                require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
                $oUser = new Users();
                $aFields = $oUser->load($UID);
                $aFields['USR_STATUS'] = 'CLOSED';
                $aFields['USR_USERNAME'] = '';
                $oUser->update($aFields);
                //Delete Dashboard
                require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashletInstance.php");
                $criteria = new Criteria('workflow');
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_UID, $UID);
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, 'USER');
                DashletInstancePeer::doDelete($criteria);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Users
     * @param array $arrayWhere Where (Condition and filters)
     * @param string $sortField Field name to sort
     * @param string $sortDir Direction of sorting (ASC, DESC)
     * @param int $start Start
     * @param int $limit Limit
     * @param bool $flagRecord Flag that set the "getting" of record
     * @param bool $throwException Flag to throw the exception (This only if the parameters are invalid)
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     * @param string $status The user's status, which can be "ACTIVE", "INACTIVE" or "VACATION"
     * @return array Return an array with all Users, ThrowTheException/FALSE otherwise
     * @throws Exception
     */
    public function getUsers(
        array $arrayWhere = null,
        $sortField = null,
        $sortDir = null,
        $start = null,
        $limit = null,
        $flagRecord = true,
        $throwException = true,
        $status = null
    ) {
        try {
            $arrayUser = array();

            $numRecTotal = 0;

            //Verify data and Set variables
            $flag = !is_null($arrayWhere) && is_array($arrayWhere);
            $flagCondition = $flag && isset($arrayWhere['condition']);
            $flagFilter = $flag && isset($arrayWhere['filter']);

            $result = Validator::validatePagerDataByPagerDefinition(
                ['$start' => $start, '$limit' => $limit],
                ['$start' => '$start', '$limit' => '$limit']
            );

            if ($result !== true) {
                if ($throwException) {
                    throw new Exception($result);
                } else {
                    return false;
                }
            }

            //Set variables
            $filterName = "filter";

            if ($flagFilter) {
                $arrayAux = array(
                    "" => "filter",
                    "LEFT" => "lfilter",
                    "RIGHT" => "rfilter"
                );

                $filterName = $arrayAux[(isset($arrayWhere['filterOption'])) ? $arrayWhere['filterOption'] : ''];
            }

            //Get data
            if (!is_null($limit) && (string)($limit) == '0') {
                //Return
                return array(
                    "total" => $numRecTotal,
                    "start" => (int)((!is_null($start)) ? $start : 0),
                    "limit" => (int)((!is_null($limit)) ? $limit : 0),
                    $filterName => ($flagFilter) ? $arrayWhere['filter'] : '',
                    "data" => $arrayUser
                );
            }

            //Query
            $criteria = $this->getUserCriteria();

            //Remove the guest user 
            $criteria->add(UsersPeer::USR_UID, RBAC::GUEST_USER_UID, Criteria::NOT_EQUAL);

            if ($flagCondition && !empty($arrayWhere['condition'])) {
                foreach ($arrayWhere['condition'] as $value) {
                    $criteria->add($value[0], $value[1], $value[2]);
                }
            } else {
                if (!is_null($status)) {
                    $criteria->add(UsersPeer::USR_STATUS, strtoupper($status), Criteria::EQUAL);
                }
            }

            if ($flagFilter && trim($arrayWhere['filter']) != '') {
                $arraySearch = [
                    '' => '%' . $arrayWhere['filter'] . '%',
                    'LEFT' => $arrayWhere['filter'] . '%',
                    'RIGHT' => '%' . $arrayWhere['filter']
                ];

                $search = $arraySearch[(isset($arrayWhere['filterOption'])) ? $arrayWhere['filterOption'] : ''];

                $criteria->add(
                    $criteria->getNewCriterion(UsersPeer::USR_USERNAME, $search, Criteria::LIKE)->addOr(
                        $criteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, $search, Criteria::LIKE)
                    )->addOr(
                        $criteria->getNewCriterion(UsersPeer::USR_LASTNAME, $search, Criteria::LIKE)
                    )
                );
            }

            //Number records total
            $numRecTotal = UsersPeer::doCount($criteria);

            //Query
            $conf = new Configurations();
            $sortFieldDefault = UsersPeer::TABLE_NAME . '.' . $conf->userNameFormatGetFirstFieldByUsersTable();

            if (!is_null($sortField) && trim($sortField) != "") {
                //SQL Injection via 'sortField' parameter
                if (!in_array($sortField, UsersPeer::getFieldNames(BasePeer::TYPE_FIELDNAME))) {
                    throw new Exception(G::LoadTranslation('ID_INVALID_VALUE_FOR', array('$sortField')));
                }
                $sortField = strtoupper($sortField);

                if (in_array(UsersPeer::TABLE_NAME . "." . $sortField, $criteria->getSelectColumns())) {
                    $sortField = UsersPeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = $sortFieldDefault;
                }
            } else {
                $sortField = $sortFieldDefault;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = UsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $record = $rsCriteria->getRow();

                $arrayUser[] = ($flagRecord) ? $record : $this->__getUserCustomRecordFromRecord($record);
            }

            //Return
            return array(
                "total" => $numRecTotal,
                "start" => (int)((!is_null($start)) ? $start : 0),
                "limit" => (int)((!is_null($limit)) ? $limit : 0),
                $filterName => ($flagFilter) ? $arrayWhere['filter'] : '',
                "data" => $arrayUser
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Upload image User
     * @param string $userUid Unique id of User
     * @throws Exception
     */
    public function uploadImage($userUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);

            if (!$_FILES) {
                throw new Exception(G::LoadTranslation("ID_UPLOAD_ERR_NO_FILE"));
            }

            if (!isset($_FILES["USR_PHOTO"])) {
                throw new Exception(G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayFieldNameForException["usrPhoto"])));
            }

            if ($_FILES['USR_PHOTO']['error'] != 1) {
                if ($_FILES['USR_PHOTO']['tmp_name'] != '') {
                    $aAux = explode('.', $_FILES['USR_PHOTO']['name']);
                    G::uploadFile($_FILES['USR_PHOTO']['tmp_name'], PATH_IMAGES_ENVIRONMENT_USERS, $userUid . '.' . $aAux[1]);
                    G::resizeImage(PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.gif');
                }
            } else {
                throw new Exception(G::LoadTranslation('ID_ERROR') . ' ' . $_FILES['USR_PHOTO']['error']);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * change Bookmarks of an user
     *
     * @access public
     * @param $userUID
     * @param $tasUid
     * @param $type
     * @return void
     */
    public function updateBookmark($userUID, $tasUid, $type)
    {
        $this->userObj = new Users();
        $fields = $this->userObj->load($userUID);
        $bookmark = empty($fields['USR_BOOKMARK_START_CASES']) ? array() : unserialize($fields['USR_BOOKMARK_START_CASES']);
        $position = array_search($tasUid, $bookmark);

        if ($type === 'INSERT' and $position === false) {
            $bookmark[] = $tasUid;
        } elseif ($type === 'DELETE' and $position !== false) {
            unset($bookmark[$position]);
        }
        $fields['USR_BOOKMARK_START_CASES'] = serialize($bookmark);
        $this->userObj->update($fields);
    }

    /**
     * @param $userUid
     * @param array $arrayPermission
     * @return User
     * @throws Exception
     */
    public function checkPermissionForEdit($userUid, $arrayPermission = array(), $form)
    {
        try {
            foreach ($arrayPermission as $key => $value) {
                $flagPermission = $this->checkPermission($userUid, $value);
                if (!$flagPermission) {
                    unset($form[$key]);
                }
            }
            return $form;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $aFields
     * @return array
     * @throws Exception
     */
    public function loadDetailedPermissions($aFields)
    {
        try {
            global $RBAC;
            $resultPermissionsForUser = array();
            if ($aFields['USR_UID'] != '') {
                foreach ($this->arrayPermissionsForEditUser as $index => $item) {
                    if ($RBAC->userCanAccess($item) !== 1) {
                        $resultPermissionsForUser[$index] = $item;
                    }
                }
                return $resultPermissionsForUser;
            } else {
                $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
                throw (new Exception(G::LoadTranslation("ID_USER_UID_DOESNT_EXIST", $lang, array("USR_UID" => $aFields['USR_UID']))));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Check permission
     * @param string $userUid Unique uid of User
     * @param string $permissionCode Permission code
     * @return bool
     * @throws Exception
     */
    public function checkPermission($userUid, $permissionCode)
    {
        try {
            $flagPermission = false;

            $arrayUserRolePermission = $this->loadUserRolePermission("PROCESSMAKER", $userUid);

            foreach ($arrayUserRolePermission as $value) {
                if ($value["PER_CODE"] == $permissionCode) {
                    $flagPermission = true;
                    break;
                }
            }

            //Return
            return $flagPermission;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get User-Logged Time Zone
     * @return string Return the User-Logged Time Zone; Time Zone system settings otherwise
     * @throws Exception
     */
    public static function getUserLoggedTimeZone()
    {
        try {
            $timeZone = 'UTC';

            if (isset($_SESSION['USR_TIME_ZONE'])) {
                $tz = trim($_SESSION['USR_TIME_ZONE']);

                $timeZone = ($tz != '') ? $tz : $timeZone;
            }

            //Return
            return $timeZone;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the User's Manager
     * @param string $userUid Unique id of User
     * @param bool $throwException Flag to throw the exception if the main parameters are invalid or do not exist
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     * @return string Returns an string with Unique id of User (Manager), ThrowTheException/FALSE otherwise
     * @throws Exception
     */
    public function getUsersManager($userUid, $throwException = true)
    {
        try {
            //Verify data and Set variables
            $arrayUserData = $this->getUserRecordByPk($userUid, ['$userUid' => '$userUid'], $throwException);

            if ($arrayUserData === false) {
                return false;
            }

            //Set variables
            $department = new \ProcessMaker\BusinessModel\Department();

            //Get Manager
            if ((string)($arrayUserData['USR_REPORTS_TO']) == '' ||
                (string)($arrayUserData['USR_REPORTS_TO']) == $userUid
            ) {
                if ((string)($arrayUserData['DEP_UID']) != '') {
                    $departmentUid = $arrayUserData['DEP_UID'];

                    do {
                        $flagd = false;

                        $arrayDepartmentData = $department->getDepartmentRecordByPk(
                            $departmentUid,
                            ['$departmentUid' => '$departmentUid'],
                            $throwException
                        );

                        if ($arrayDepartmentData === false) {
                            return false;
                        }

                        if ((string)($arrayDepartmentData['DEP_MANAGER']) == '' ||
                            (string)($arrayDepartmentData['DEP_MANAGER']) == $userUid
                        ) {
                            if ((string)($arrayDepartmentData['DEP_PARENT']) != '') {
                                $departmentUid = $arrayDepartmentData['DEP_PARENT'];
                                $flagd = true;
                            } else {
                                return false;
                            }
                        } else {
                            return $arrayDepartmentData['DEP_MANAGER'];
                        }
                    } while ($flagd);
                } else {
                    return false;
                }
            } else {
                return $arrayUserData['USR_REPORTS_TO'];
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * AuditLog
     * @param string $option Option
     * @param array $arrayData Data
     * @throws Exception
     */
    public function auditLog($option, array $arrayData)
    {
        try {
            $firstName = (array_key_exists('USR_FIRSTNAME', $arrayData)) ? ' - First Name: ' . $arrayData['USR_FIRSTNAME'] : '';
            $lastName = (array_key_exists('USR_LASTNAME', $arrayData)) ? ' - Last Name: ' . $arrayData['USR_LASTNAME'] : '';
            $email = (array_key_exists('USR_EMAIL', $arrayData)) ? ' - Email: ' . $arrayData['USR_EMAIL'] : '';
            $dueDate = (array_key_exists('USR_DUE_DATE', $arrayData)) ? ' - Due Date: ' . $arrayData['USR_DUE_DATE'] : '';
            $status = (array_key_exists('USR_STATUS', $arrayData)) ? ' - Status: ' . $arrayData['USR_STATUS'] : '';
            $address = (array_key_exists('USR_ADDRESS', $arrayData)) ? ' - Address: ' . $arrayData['USR_ADDRESS'] : '';
            $phone = (array_key_exists('USR_PHONE', $arrayData)) ? ' - Phone: ' . $arrayData['USR_PHONE'] : '';
            $zipCode = (array_key_exists('USR_ZIP_CODE', $arrayData)) ? ' - Zip Code: ' . $arrayData['USR_ZIP_CODE'] : '';
            $position = (array_key_exists('USR_POSITION', $arrayData)) ? ' - Position: ' . $arrayData['USR_POSITION'] : '';
            $role = (array_key_exists('USR_ROLE', $arrayData)) ? ' - Role: ' . $arrayData['USR_ROLE'] : '';
            $languageDef = (array_key_exists('USR_DEFAULT_LANG', $arrayData)) ? ' - Default Language: ' . $arrayData['USR_DEFAULT_LANG'] : '';
            $timeZone = (array_key_exists('USR_TIME_ZONE', $arrayData)) ? ' - Time Zone: ' . $arrayData['USR_TIME_ZONE'] : '';

            $str = 'User Name: ' . $arrayData['USR_USERNAME'] . ' - User ID: (' . $arrayData['USR_UID'] . ')' .
                $firstName . $lastName . $email . $dueDate . $status . $address . $phone . $zipCode . $position . $role . $timeZone . $languageDef;

            G::auditLog(($option == 'INS') ? 'CreateUser' : 'UpdateUser', $str);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This function get the list of users
     * @param string $authSource , authentication source
     * @param string $filter
     * @param string $sort
     * @param int $start
     * @param int $limit
     * @param string $dir related to order the column
     * @return array
     * @throws Exception
     */
    public function getAllUsersWithAuthSource(
        $authSource = '',
        $filter = '',
        $sort = '',
        $start = 0,
        $limit = 20,
        $dir = 'ASC'
    ) {
        global $RBAC;
        $aUsers = array();
        if ($authSource != '') {
            $aUsers = $RBAC->getListUsersByAuthSource($authSource);
        }
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn('COUNT(*) AS CNT');
        if ($filter != '') {
            $cc = $oCriteria->getNewCriterion(UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE)
                ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE)
                    ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE)
                        ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_EMAIL, '%' . $filter . '%', Criteria::LIKE))));
            $oCriteria->add($cc);
        }
        $oCriteria->add(UsersPeer::USR_STATUS, array('CLOSED'), Criteria::NOT_IN);

        //Remove the guest user 
        $oCriteria->add(UsersPeer::USR_UID, RBAC::GUEST_USER_UID, Criteria::NOT_EQUAL);

        if ($authSource != '') {
            $totalRows = sizeof($aUsers);
        } else {
            $oDataset = UsersPeer::DoSelectRs($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $row = $oDataset->getRow();
            $totalRows = $row['CNT'];
        }
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(UsersPeer::USR_UID);
        $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
        $oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
        $oCriteria->addSelectColumn(UsersPeer::USR_EMAIL);
        $oCriteria->addSelectColumn(UsersPeer::USR_ROLE);
        $oCriteria->addSelectColumn(UsersPeer::USR_DUE_DATE);
        $oCriteria->addSelectColumn(UsersPeer::USR_STATUS);
        $oCriteria->addSelectColumn(UsersPeer::USR_UX);
        $oCriteria->addSelectColumn(UsersPeer::DEP_UID);
        $oCriteria->addSelectColumn(UsersPeer::USR_LAST_LOGIN);
        $oCriteria->addAsColumn('LAST_LOGIN', 0);
        $oCriteria->addAsColumn('DEP_TITLE', 0);
        $oCriteria->addAsColumn('TOTAL_CASES', 0);
        $oCriteria->addAsColumn('DUE_DATE_OK', 1);
        $sep = "'";
        $oCriteria->add(UsersPeer::USR_STATUS, array('CLOSED'), Criteria::NOT_IN);

        //Remove the guest user
        $oCriteria->add(UsersPeer::USR_UID, RBAC::GUEST_USER_UID, Criteria::NOT_EQUAL);

        if ($filter != '') {
            $cc = $oCriteria->getNewCriterion(UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE)
                ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE)
                    ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE)
                        ->addOr($oCriteria->getNewCriterion(UsersPeer::USR_EMAIL, '%' . $filter . '%', Criteria::LIKE))));
            $oCriteria->add($cc);
        }
        if (sizeof($aUsers) > 0) {
            $oCriteria->add(UsersPeer::USR_UID, $aUsers, Criteria::IN);
        } elseif ($totalRows == 0 && $authSource != '') {
            $oCriteria->add(UsersPeer::USR_UID, '', Criteria::IN);
        }
        if ($sort != '') {
            //SQL Injection via 'sort' parameter
            if (!in_array($sort, UsersPeer::getFieldNames(BasePeer::TYPE_FIELDNAME))) {
                throw new Exception(G::LoadTranslation('ID_INVALID_VALUE_FOR', array('$sort')));
            }
            if ($dir == 'ASC') {
                $oCriteria->addAscendingOrderByColumn($sort);
            } else {
                $oCriteria->addDescendingOrderByColumn($sort);
            }
        }
        $oCriteria->setOffset($start);
        $oCriteria->setLimit($limit);
        $oDataset = UsersPeer::DoSelectRs($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        return array("data" => $oDataset, "totalRows" => $totalRows);
    }

    /**
     * This function get additional information related to the user
     * Information about the department, rol, cases, authentication
     *
     * @param criteria $oDatasetUsers , criteria for search users
     *
     * @return array $dataUsers array of users with the additional information
     */
    public function getAdditionalInfoFromUsers($oDatasetUsers)
    {
        global $RBAC;
        //Get the information about the department
        $Department = new Department();
        $aDepart = $Department->getAllDepartmentsByUser();

        //Get the authentication sources
        $aAuthSources = $RBAC->getAllAuthSourcesByUser();

        //Get roles
        $oRoles = new Roles();

        //Get cases
        $oParticipated = new ListParticipatedLast();
        $oAppCache = new AppCacheView();

        $rows = array();
        $uRole = array();
        $totalRows = 0;
        $dataUsers = array();
        while ($oDatasetUsers->next()) {
            $totalRows++;
            $row = $oDatasetUsers->getRow();

            //Add the role information related to the user
            try {
                $uRole = $oRoles->loadByCode($row['USR_ROLE']);
            } catch (exception $oError) {
                $uRole['ROL_NAME'] = G::loadTranslation('ID_DELETED');
            }
            $row['USR_ROLE_ID'] = $row['USR_ROLE'];
            $row['USR_ROLE'] = isset($uRole['ROL_NAME']) ? ($uRole['ROL_NAME'] != '' ? $uRole['ROL_NAME'] : $uRole['ROL_CODE']) : $uRole['ROL_CODE'];

                /*----------------------------------********---------------------------------*/
                $total = $oAppCache->getListCounters('sent', $row['USR_UID'], false);
            /*----------------------------------********---------------------------------*/
            $row['TOTAL_CASES'] = $total;

            $row['DUE_DATE_OK'] = (date('Y-m-d') > date('Y-m-d', strtotime($row['USR_DUE_DATE']))) ? 0 : 1;
            $row['LAST_LOGIN'] = isset($row['USR_LAST_LOGIN']) ? DateTime::convertUtcToTimeZone($row['USR_LAST_LOGIN']) : '';
            //Add the department information related to the user
            $row['DEP_TITLE'] = isset($aDepart[$row['USR_UID']]) ? $aDepart[$row['USR_UID']] : '';
            //Add the authentication information related to the user
            $row['USR_AUTH_SOURCE'] = isset($aAuthSources[$row['USR_UID']]) ? $aAuthSources[$row['USR_UID']] : 'ProcessMaker (MYSQL)';

            $rows[] = $row;
        }
        $dataUsers['data'] = $rows;
        $dataUsers['totalCount'] = $totalRows;

        return $dataUsers;
    }
}
