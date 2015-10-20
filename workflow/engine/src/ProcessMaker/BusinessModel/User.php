<?php

namespace ProcessMaker\BusinessModel;

use \G;



class User

{

    private $arrayFieldDefinition = array(

        "USR_UID"              => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrUid"),

        "USR_FIRSTNAME"        => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrFirstname"),

        "USR_LASTNAME"         => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrLastname"),

        "USR_USERNAME"         => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrUsername"),

        "USR_EMAIL"            => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrEmail"),

        "USR_ADDRESS"          => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrAddress"),

        "USR_ZIP_CODE"         => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrZipCode"),

        "USR_COUNTRY"          => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrCountry"),

        "USR_CITY"             => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrCity"),

        "USR_LOCATION"         => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrLocation"),

        "USR_PHONE"            => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrPhone"),

        "USR_POSITION"         => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrPosition"),

        "USR_REPLACED_BY"      => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrReplacedBy"),

        "USR_DUE_DATE"         => array("type" => "date",   "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrDueDate"),

        "USR_CALENDAR"         => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrCalendar"),

        "USR_STATUS"           => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE", "VACATION"),               "fieldNameAux" => "usrStatus"),

        "USR_ROLE"             => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrRole"),

        "USR_NEW_PASS"         => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                               "fieldNameAux" => "usrNewPass"),

        "USR_UX"               => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("NORMAL", "SIMPLIFIED", "SWITCHABLE", "SINGLE"), "fieldNameAux" => "usrUx"),

        "DEP_UID"              => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "depUid"),

        "USR_BIRTHDAY"         => array("type" => "date",   "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrBirthday"),

        "USR_FAX"              => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrFax"),

        "USR_CELLULAR"         => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                               "fieldNameAux" => "usrCellular"),

        /*----------------------------------********---------------------------------*/

        "USR_LOGGED_NEXT_TIME" => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                                           "fieldNameAux" => "usrLoggedNextTime")

    );



    private $formatFieldNameInUppercase = true;



    private $arrayFieldNameForException = array(

        "usrPhoto"  => "USR_PHOTO"

    );



    /**

     * Constructor of the class

     *

     * return void

     */

    public function __construct()

    {

        try {

            foreach ($this->arrayFieldDefinition as $key => $value) {

                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Set the format of the fields name (uppercase, lowercase)

     *

     * @param bool $flag Value that set the format

     *

     * return void

     */

    public function setFormatFieldNameInUppercase($flag)

    {

        try {

            $this->formatFieldNameInUppercase = $flag;



            $this->setArrayFieldNameForException($this->arrayFieldNameForException);

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Set exception users for fields

     *

     * @param array $arrayData Data with the fields

     *

     * return void

     */

    public function setArrayFieldNameForException(array $arrayData)

    {

        try {

            foreach ($arrayData as $key => $value) {

                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get the name of the field according to the format

     *

     * @param string $fieldName Field name

     *

     * return string Return the field name according the format

     */

    public function getFieldNameByFormatFieldName($fieldName)

    {

        try {

            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Verify if exists the Name of a User

     *

     * @param string $userName         Name

     * @param string $userUidToExclude Unique id of User to exclude

     *

     * return bool Return true if exists the Name of a User, false otherwise

     */

    public function existsName($userName, $userUidToExclude = "")

    {

        try {

            $criteria = $this->getUserCriteria();



            if ($userUidToExclude != "") {

                $criteria->add(\UsersPeer::USR_UID, $userUidToExclude, \Criteria::NOT_EQUAL);

            }



            $criteria->add(\UsersPeer::USR_USERNAME, $userName, \Criteria::EQUAL);



            //QUERY

            $rsCriteria = \UsersPeer::doSelectRS($criteria);



            return ($rsCriteria->next())? true : false;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Verify if exists the Name of a User

     *

     * @param string $userName              Name

     * @param string $fieldNameForException Field name for the exception

     * @param string $userUidToExclude      Unique id of User to exclude

     *

     * return void Throw exception if exists the title of a User

     */

    public function throwExceptionIfExistsName($userName, $fieldNameForException, $userUidToExclude = "")

    {

        try {

            if ($this->existsName($userName, $userUidToExclude)) {

                throw new \Exception(\G::LoadTranslation("ID_USER_NAME_ALREADY_EXISTS", array($fieldNameForException, $userName)));

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Verify password

     *

     * @param string $userPassword          Password

     * @param string $fieldNameForException Field name for the exception

     *

     * return void Throw exception if password is invalid

     */

    public function throwExceptionIfPasswordIsInvalid($userPassword, $fieldNameForException)

    {

        try {

            $result = $this->testPassword($userPassword);



            if (!$result["STATUS"]) {

                throw new \Exception($fieldNameForException . ": " . $result["DESCRIPTION"]);

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Validate the data if they are invalid (INSERT and UPDATE)

     *

     * @param string $userUid   Unique id of User

     * @param array  $arrayData Data

     *

     * return void Throw exception if data has an invalid value

     */

    public function throwExceptionIfDataIsInvalid($userUid, array $arrayData)

    {

        try {

            //Set variables

            $arrayUserData = ($userUid == "")? array() : $this->getUser($userUid, true);

            $flagInsert = ($userUid == "")? true : false;



            $arrayFinalData = array_merge($arrayUserData, $arrayData);



            //Verify data - Field definition.

            $process = new \ProcessMaker\BusinessModel\Process();



            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);



            //Verify data

            if (isset($arrayData["USR_USERNAME"])) {

                $this->throwExceptionIfExistsName($arrayData["USR_USERNAME"], $this->arrayFieldNameForException["usrUsername"], $userUid);

            }



            if (isset($arrayData["USR_EMAIL"])) {

                if (!filter_var($arrayData["USR_EMAIL"], FILTER_VALIDATE_EMAIL)) {

                    throw new \Exception($this->arrayFieldNameForException["usrEmail"] . ": " . \G::LoadTranslation("ID_INCORRECT_EMAIL"));

                }

            }



            if (isset($arrayData["USR_NEW_PASS"])) {

                $this->throwExceptionIfPasswordIsInvalid($arrayData["USR_NEW_PASS"], $this->arrayFieldNameForException["usrNewPass"]);

            }



            if (isset($arrayData["USR_REPLACED_BY"]) && $arrayData["USR_REPLACED_BY"] != "") {

                $obj = \UsersPeer::retrieveByPK($arrayData["USR_REPLACED_BY"]);



                if (is_null($obj)) {

                    throw new \Exception(\G::LoadTranslation("ID_USER_DOES_NOT_EXIST", array($this->arrayFieldNameForException["usrReplacedBy"], $arrayData["USR_REPLACED_BY"])));

                }

            }



            if (isset($arrayData["USR_DUE_DATE"])) {

                $arrayUserDueDate = explode("-", $arrayData["USR_DUE_DATE"]);



                if (ctype_digit($arrayUserDueDate[0])) {

                    if (!checkdate($arrayUserDueDate[1], $arrayUserDueDate[2], $arrayUserDueDate[0])) {

                        throw new \Exception($this->arrayFieldNameForException["usrDueDate"] . ": " . \G::LoadTranslation("ID_MSG_ERROR_DUE_DATE"));

                    }

                } else {

                    throw new \Exception($this->arrayFieldNameForException["usrDueDate"] . ": " . \G::LoadTranslation("ID_MSG_ERROR_DUE_DATE"));

                }

            }



            if (isset($arrayData["USR_ROLE"])) {

                require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Roles.php");



                $criteria = new \Criteria("rbac");



                $criteria->add(\RolesPeer::ROL_CODE, $arrayData["USR_ROLE"]);

                $rsCriteria = \RolesPeer::doSelectRS($criteria);



                if (!$rsCriteria->next()) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrRole"])));

                }

            }



            if (isset($arrayData["USR_COUNTRY"]) && $arrayData["USR_COUNTRY"] != "") {

                $obj = \IsoCountryPeer::retrieveByPK($arrayData["USR_COUNTRY"]);



                if (is_null($obj)) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));

                }

            }



            if (isset($arrayData["USR_CITY"]) && $arrayData["USR_CITY"] != "") {

                if (!isset($arrayFinalData["USR_COUNTRY"]) || $arrayFinalData["USR_COUNTRY"] == "") {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));

                }



                $obj = \IsoSubdivisionPeer::retrieveByPK($arrayFinalData["USR_COUNTRY"], $arrayData["USR_CITY"]);



                if (is_null($obj)) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCity"])));

                }

            }



            if (isset($arrayData["USR_LOCATION"]) && $arrayData["USR_LOCATION"] != "") {

                if (!isset($arrayFinalData["USR_COUNTRY"]) || $arrayFinalData["USR_COUNTRY"] == "") {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrCountry"])));

                }



                $obj = \IsoLocationPeer::retrieveByPK($arrayFinalData["USR_COUNTRY"], $arrayData["USR_LOCATION"]);



                if (is_null($obj)) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array($this->arrayFieldNameForException["usrLocation"])));

                }

            }



            if (isset($arrayData["USR_CALENDAR"]) && $arrayData["USR_CALENDAR"] != "") {

                $obj = \CalendarDefinitionPeer::retrieveByPK($arrayData["USR_CALENDAR"]);



                if (is_null($obj)) {

                    throw new \Exception(\G::LoadTranslation("ID_CALENDAR_DOES_NOT_EXIST", array($this->arrayFieldNameForException["usrCalendar"], $arrayData["USR_CALENDAR"])));

                }

            }



            if (isset($arrayData["DEP_UID"]) && $arrayData["DEP_UID"] != "") {

                $department = new \Department();



                if (!$department->existsDepartment($arrayData["DEP_UID"])) {

                    throw new \Exception(\G::LoadTranslation("ID_DEPARTMENT_NOT_EXIST", array($this->arrayFieldNameForException["depUid"], $arrayData["DEP_UID"])));

                }

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Verify if does not exist the User in table USERS

     *

     * @param string $userUid               Unique id of Email Server

     * @param string $fieldNameForException Field name for the exception

     *

     * return void Throw exception if does not exist the User in table USERS

     */

    public function throwExceptionIfNotExistsUser($userUid, $fieldNameForException)

    {

        try {

            $obj = \UsersPeer::retrieveByPK($userUid);



            if (is_null($obj) || $obj->getUsrUsername() == "") {

                throw new \Exception(\G::LoadTranslation("ID_USER_DOES_NOT_EXIST", array($fieldNameForException, $userUid)));

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get data of a from a record

     *

     * @param array $record Record

     *

     * return array Return an array with data User

     */

    public function getUserDataFromRecord(array $record)

    {

        try {

            //Get Calendar

            $calendar = new \Calendar();

            $calendarInfo = $calendar->getCalendarFor( $record["USR_UID"], "", "" );

            $aFields["USR_CALENDAR_UID"] = ($calendarInfo["CALENDAR_APPLIED"] != "DEFAULT") ? $calendarInfo["CALENDAR_UID"] : "";

            $aFields["USR_CALENDAR"] = ($aFields["USR_CALENDAR_UID"] != "") ? $calendar->calendarName( $aFields["USR_CALENDAR_UID"] ) : $aFields["USR_CALENDAR_UID"];



            //Get photo

            $pathPhotoUser = PATH_IMAGES_ENVIRONMENT_USERS . $record["USR_UID"] . ".gif";

            if (! file_exists( $pathPhotoUser )) {

                $pathPhotoUser = PATH_HOME . "public_html" . PATH_SEP . "images" . PATH_SEP . "user.gif";

            }



            return array(

                $this->getFieldNameByFormatFieldName("USR_UID")                => $record["USR_UID"],

                $this->getFieldNameByFormatFieldName("USR_USERNAME")           => $record["USR_USERNAME"],

                //$this->getFieldNameByFormatFieldName("USR_PASSWORD")           => $record["USR_PASSWORD"],

                $this->getFieldNameByFormatFieldName("USR_FIRSTNAME")          => $record["USR_FIRSTNAME"],

                $this->getFieldNameByFormatFieldName("USR_LASTNAME")           => $record["USR_LASTNAME"],

                $this->getFieldNameByFormatFieldName("USR_EMAIL")              => $record["USR_EMAIL"],

                $this->getFieldNameByFormatFieldName("USR_DUE_DATE")           => $record["USR_DUE_DATE"],

                $this->getFieldNameByFormatFieldName("USR_CREATE_DATE")        => $record["USR_CREATE_DATE"],

                $this->getFieldNameByFormatFieldName("USR_UPDATE_DATE")        => $record["USR_UPDATE_DATE"],

                $this->getFieldNameByFormatFieldName("USR_STATUS")             => $record["USR_STATUS"],

                $this->getFieldNameByFormatFieldName("USR_COUNTRY")            => $record["USR_COUNTRY"],

                $this->getFieldNameByFormatFieldName("USR_CITY")               => $record["USR_CITY"],

                $this->getFieldNameByFormatFieldName("USR_LOCATION")           => $record["USR_LOCATION"],

                $this->getFieldNameByFormatFieldName("USR_ADDRESS")            => $record["USR_ADDRESS"],

                $this->getFieldNameByFormatFieldName("USR_PHONE")              => $record["USR_PHONE"],

                $this->getFieldNameByFormatFieldName("USR_FAX")                => $record["USR_FAX"],

                $this->getFieldNameByFormatFieldName("USR_CELLULAR")           => $record["USR_CELLULAR"],

                $this->getFieldNameByFormatFieldName("USR_ZIP_CODE")           => $record["USR_ZIP_CODE"],

                $this->getFieldNameByFormatFieldName("DEP_UID")                => $record["DEP_UID"],

                $this->getFieldNameByFormatFieldName("USR_POSITION")           => $record["USR_POSITION"],

                $this->getFieldNameByFormatFieldName("USR_RESUME")             => $record["USR_RESUME"],

                $this->getFieldNameByFormatFieldName("USR_BIRTHDAY")           => $record["USR_BIRTHDAY"],

                $this->getFieldNameByFormatFieldName("USR_ROLE")               => $record["USR_ROLE"],

                $this->getFieldNameByFormatFieldName("USR_REPORTS_TO")         => $record["USR_REPORTS_TO"],

                $this->getFieldNameByFormatFieldName("USR_REPLACED_BY")        => $record["USR_REPLACED_BY"],

                $this->getFieldNameByFormatFieldName("USR_CALENDAR_UID")       => $aFields["USR_CALENDAR_UID"],

                $this->getFieldNameByFormatFieldName("USR_CALENDAR_NAME")           => $aFields["USR_CALENDAR"],

                $this->getFieldNameByFormatFieldName("USR_UX")                 => $record["USR_UX"],

                /*----------------------------------********---------------------------------*/

                $this->getFieldNameByFormatFieldName("USR_TOTAL_INBOX")        => $record["USR_TOTAL_INBOX"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_DRAFT")        => $record["USR_TOTAL_DRAFT"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_CANCELLED")    => $record["USR_TOTAL_CANCELLED"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_PARTICIPATED") => $record["USR_TOTAL_PARTICIPATED"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_PAUSED")       => $record["USR_TOTAL_PAUSED"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_COMPLETED")    => $record["USR_TOTAL_COMPLETED"],

                $this->getFieldNameByFormatFieldName("USR_TOTAL_UNASSIGNED")   => $record["USR_TOTAL_UNASSIGNED"],

                $this->getFieldNameByFormatFieldName("USR_PHOTO_PATH")   => $pathPhotoUser

            );

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get criteria for User

     *

     * return object

     */

    public function getUserCriteria()

    {

        try {

            $criteria = new \Criteria("workflow");



            $criteria->addSelectColumn(\UsersPeer::USR_UID);

            $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $criteria->addSelectColumn(\UsersPeer::USR_PASSWORD);

            $criteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $criteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $criteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            $criteria->addSelectColumn(\UsersPeer::USR_DUE_DATE);

            $criteria->addSelectColumn(\UsersPeer::USR_CREATE_DATE);

            $criteria->addSelectColumn(\UsersPeer::USR_UPDATE_DATE);

            $criteria->addSelectColumn(\UsersPeer::USR_STATUS);

            $criteria->addSelectColumn(\UsersPeer::USR_COUNTRY);

            $criteria->addSelectColumn(\UsersPeer::USR_CITY);

            $criteria->addSelectColumn(\UsersPeer::USR_LOCATION);

            $criteria->addSelectColumn(\UsersPeer::USR_ADDRESS);

            $criteria->addSelectColumn(\UsersPeer::USR_PHONE);

            $criteria->addSelectColumn(\UsersPeer::USR_FAX);

            $criteria->addSelectColumn(\UsersPeer::USR_CELLULAR);

            $criteria->addSelectColumn(\UsersPeer::USR_ZIP_CODE);

            $criteria->addSelectColumn(\UsersPeer::DEP_UID);

            $criteria->addSelectColumn(\UsersPeer::USR_POSITION);

            $criteria->addSelectColumn(\UsersPeer::USR_RESUME);

            $criteria->addSelectColumn(\UsersPeer::USR_BIRTHDAY);

            $criteria->addSelectColumn(\UsersPeer::USR_ROLE);

            $criteria->addSelectColumn(\UsersPeer::USR_REPORTS_TO);

            $criteria->addSelectColumn(\UsersPeer::USR_REPLACED_BY);

            $criteria->addSelectColumn(\UsersPeer::USR_UX);

            /*----------------------------------********---------------------------------*/

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_INBOX);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_DRAFT);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_CANCELLED);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_PARTICIPATED);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_PAUSED);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_COMPLETED);

            $criteria->addSelectColumn(\UsersPeer::USR_TOTAL_UNASSIGNED);



            return $criteria;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Create User

     *

     * @param array $arrayData Data

     *

     * return array Return data of the new User created

     */

    public function create(array $arrayData)

    {

        try {

            \G::LoadSystem("rbac");



            //Verify data

            $process = new \ProcessMaker\BusinessModel\Process();

            $validator = new \ProcessMaker\BusinessModel\Validator();



            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");

            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");



            //Set data

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);



            unset($arrayData["USR_UID"]);



            $this->throwExceptionIfDataIsInvalid("", $arrayData);



            //Create

            $cnn = \Propel::getConnection("workflow");



            try {

                $rbac = new \RBAC();

                $user = new \Users();



                $rbac->initRBAC();



                $arrayData["USR_PASSWORD"]         = \Bootstrap::hashPassword($arrayData["USR_NEW_PASS"]);



                $arrayData["USR_BIRTHDAY"]         = (isset($arrayData["USR_BIRTHDAY"]))?         $arrayData["USR_BIRTHDAY"] : date("Y-m-d");

                $arrayData["USR_LOGGED_NEXT_TIME"] = (isset($arrayData["USR_LOGGED_NEXT_TIME"]))? $arrayData["USR_LOGGED_NEXT_TIME"] : 0;

                $arrayData["USR_CREATE_DATE"]      = date("Y-m-d H:i:s");

                $arrayData["USR_UPDATE_DATE"]      = date("Y-m-d H:i:s");



                //Create in rbac

                //$userStatus = $arrayData["USR_STATUS"];

                //

                //if ($arrayData["USR_STATUS"] == "ACTIVE") {

                //    $arrayData["USR_STATUS"] = 1;

                //}

                //

                //if ($arrayData["USR_STATUS"] == "INACTIVE") {

                //    $arrayData["USR_STATUS"] = 0;

                //}

                //

                //$userUid = $this->createUser($arrayData);

                //

                //if ($arrayData["USR_ROLE"] != "") {

                //    $this->assignRoleToUser($userUid, $arrayData["USR_ROLE"]);

                //}

                //

                //$arrayData["USR_STATUS"] = $userStatus;



                $userUid = $rbac->createUser($arrayData, $arrayData["USR_ROLE"]);



                //Create in workflow

                $arrayData["USR_UID"] = $userUid;

                $arrayData["USR_PASSWORD"] = "00000000000000000000000000000000";



                $result = $user->create($arrayData);



                //User Properties

                $userProperty = new \UsersProperties();



                $aUserProperty = $userProperty->loadOrCreateIfNotExists($arrayData["USR_UID"], array("USR_PASSWORD_HISTORY" => serialize(array(\Bootstrap::hashPassword($arrayData["USR_PASSWORD"])))));

                $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];



                $userProperty->update($aUserProperty);



                //Save Calendar assigment

                if (isset($arrayData["USR_CALENDAR"])) {

                    //Save Calendar ID for this user

                    \G::LoadClass("calendar");



                    $calendar = new \Calendar();

                    $calendar->assignCalendarTo($arrayData["USR_UID"], $arrayData["USR_CALENDAR"], "USER");

                }



                //Return

                return $this->getUser($userUid);

            } catch (\Exception $e) {

                $cnn->rollback();



                throw $e;

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Update User

     *

     * @param string $userUid       Unique id of User

     * @param array  $arrayData     Data

     * @param string $userUidLogged Unique id of User logged

     *

     * return array Return data of the User updated

     */

    public function update($userUid, array $arrayData, $userUidLogged)

    {

        try {

            \G::LoadSystem("rbac");



            //Verify data

            $process = new \ProcessMaker\BusinessModel\Process();

            $validator = new \ProcessMaker\BusinessModel\Validator();



            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");

            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");



            //Set data

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $arrayDataBackup = $arrayData;



            //Verify data

            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);



            $this->throwExceptionIfDataIsInvalid($userUid, $arrayData);



            //Permission Admin

            $countPermission = 0;



            $permission = $this->loadUserRolePermission("PROCESSMAKER", $userUidLogged);



            foreach ($permission as $key => $value) {

                if ($value["PER_CODE"] == "PM_USERS") {

                    $countPermission = $countPermission + 1;

                }

            }



            if ($countPermission != 1) {

                throw new \Exception(\G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($userUidLogged)));

            }



            //Update

            $cnn = \Propel::getConnection("workflow");



            try {

                $rbac = new \RBAC();

                $user = new \Users();



                $rbac->initRBAC();



                if (isset($arrayData["USR_NEW_PASS"])) {

                    $arrayData["USR_PASSWORD"] = \Bootstrap::hashPassword($arrayData["USR_NEW_PASS"]);

                }



                $arrayData["USR_UID"]              = $userUid;

                $arrayData["USR_LOGGED_NEXT_TIME"] = (isset($arrayData["USR_LOGGED_NEXT_TIME"]))? $arrayData["USR_LOGGED_NEXT_TIME"] : 0;

                $arrayData["USR_UPDATE_DATE"]      = date("Y-m-d H:i:s");



                $flagUserLoggedNextTime = false;



                if (isset($arrayData["USR_PASSWORD"])) {

                    if ($arrayData["USR_PASSWORD"] != "") {

                        //require_once 'classes/model/UsersProperties.php';



                        $userProperty = new \UsersProperties();

                        $aUserProperty = $userProperty->loadOrCreateIfNotExists($userUid, array("USR_PASSWORD_HISTORY" => serialize(array(\Bootstrap::hashPassword($arrayData["USR_PASSWORD"])))));



                        $memKey = "rbacSession" . session_id();

                        $memcache = & \PMmemcached::getSingleton(defined("SYS_SYS")? SYS_SYS : "");



                        if (($rbac->aUserInfo = $memcache->get($memKey)) == false) {

                            $rbac->loadUserRolePermission("PROCESSMAKER", $userUidLogged);

                            $memcache->set($memKey, $rbac->aUserInfo, \PMmemcached::EIGHT_HOURS);

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



                                throw new \Exception($this->arrayFieldNameForException["usrNewPass"] . ": " . $sDescription);

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

                    //require_once "classes/model/Users.php";

                    $oUser = new \Users();

                    $aUser = $oUser->load($userUid);

                    //require_once "classes/model/UsersProperties.php";

                    $oUserProperty = new \UsersProperties();

                    $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($userUid, array("USR_PASSWORD_HISTORY" => serialize(array($aUser["USR_PASSWORD"]))));

                    $aUserProperty["USR_LOGGED_NEXT_TIME"] = $arrayData["USR_LOGGED_NEXT_TIME"];

                    $oUserProperty->update($aUserProperty);

                }



                //Update in rbac

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

                    \G::LoadClass("calendar");



                    $calendar = new \Calendar();

                    $calendar->assignCalendarTo($userUid, $arrayData["USR_CALENDAR"], "USER");

                }



                //Return

                $arrayData = $arrayDataBackup;



                if (!$this->formatFieldNameInUppercase) {

                    $arrayData = array_change_key_case($arrayData, CASE_LOWER);

                }



                return $arrayData;

            } catch (\Exception $e) {

                $cnn->rollback();



                throw $e;

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get data of a User

     *

     * @param string $userUid       Unique id of User

     * @param bool   $flagGetRecord Value that set the getting

     *

     * return array Return an array with data of a User

     */

    public function getUser($userUid, $flagGetRecord = false)

    {

        try {

            //Verify data

            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);



            //Get data

            //SQL

            $criteria = $this->getUserCriteria();



            $criteria->add(\UsersPeer::USR_UID, $userUid, \Criteria::EQUAL);



            $rsCriteria = \UsersPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            $rsCriteria->next();



            $row = $rsCriteria->getRow();



            //Return

            return (!$flagGetRecord)? $this->getUserDataFromRecord($row) : $row;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Create User Uid

     *

     * @param array $arrayUserData Data

     *

     * return id

     */

    public function createUser($userData)

    {

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

        $this->userObj = new \RbacUsers();

        if (class_exists('PMPluginRegistry')) {

            $pluginRegistry = & \PMPluginRegistry::getSingleton();

            if ($pluginRegistry->existsTrigger(PM_BEFORE_CREATE_USER)) {

                try {

                    $pluginRegistry->executeTriggers(PM_BEFORE_CREATE_USER, null);

                } catch (Exception $error) {

                    throw new Exception($error->getMessage());

                }

            }

        }

        $oConnection = \Propel::getConnection(\RbacUsersPeer::DATABASE_NAME);

        try {

            $oRBACUsers = new \RbacUsers();

            do {

                $userData['USR_UID'] = \G::generateUniqueID();

            } while ($oRBACUsers->load($userData['USR_UID']));

            $oRBACUsers->fromArray($userData, \BasePeer::TYPE_FIELDNAME);

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

    public function assignRoleToUser ($sUserUID = '', $sRolCode = '')

    {

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Roles.php");

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersRoles.php");

        $this->usersRolesObj = new \UsersRoles();

        $this->rolesObj = new \Roles();

        $aRol = $this->rolesObj->loadByCode( $sRolCode );

        $this->usersRolesObj->create( $sUserUID, $aRol['ROL_UID'] );

    }



    /**

     * to test Password

     *

     * @access public

     * @param string $sPassword

     * @return array

     */

    public function testPassword ($sPassword = '')

    {

        require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersProperties.php");

        $oUserProperty = new \UsersProperties();

        $aFields = array();

        $dateNow = date('Y-m-d H:i:s');

        $aErrors = $oUserProperty->validatePassword($sPassword, $dateNow, 0);

        if (!empty($aErrors)) {

            if (!defined('NO_DISPLAY_USERNAME')) {

                define('NO_DISPLAY_USERNAME', 1);

            }

            $aFields = array();

            $aFields['DESCRIPTION'] = \G::LoadTranslation('ID_POLICY_ALERT');

            foreach ($aErrors as $sError) {

                switch ($sError) {

                    case 'ID_PPP_MINIMUM_LENGTH':

                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MINIMUM_LENGTH .'. ';

                        $aFields[substr($sError, 3)] = PPP_MINIMUM_LENGTH;

                        break;

                    case 'ID_PPP_MAXIMUM_LENGTH':

                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MAXIMUM_LENGTH .'. ';

                        $aFields[substr($sError, 3)] = PPP_MAXIMUM_LENGTH;

                        break;

                    case 'ID_PPP_EXPIRATION_IN':

                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ' ' . PPP_EXPIRATION_IN . ' ' . \G::LoadTranslation('ID_DAYS') .'. ';

                        $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;

                        break;

                    default:

                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError);

                        $aFields[substr($sError, 3)] = 1;

                        break;

                }

            }

            $aFields['DESCRIPTION'] .= \G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');

            $aFields['STATUS'] = false;

        } else {

            $aFields['DESCRIPTION'] = \G::LoadTranslation('ID_PASSWORD_COMPLIES_POLICIES');

            $aFields['STATUS'] = true;

        }

        return $aFields;

    }



    /**

     * change status of an user

     *

     * @access public

     * @param array $sUserUID

     * @return void

     */

    public function changeUserStatus ($sUserUID = '', $sStatus = 'ACTIVE')

    {

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

        $this->userObj = new \RbacUsers();

        if ($sStatus === 'ACTIVE') {

            $sStatus = 1;

        }

        $aFields = $this->userObj->load( $sUserUID );

        $aFields['USR_STATUS'] = $sStatus;

        $this->userObj->update( $aFields );

    }



    /**

     * remove a role from an user

     *

     * @access public

     * @param array $sUserUID

     * @return void

     */

    public function removeRolesFromUser ($sUserUID = '')

    {

        $oCriteria = new \Criteria( 'rbac' );

        $oCriteria->add( \UsersRolesPeer::USR_UID, $sUserUID );

        \UsersRolesPeer::doDelete( $oCriteria );

    }



    /**

     * updated an user

     *

     * @access public

     * @param array $userData

     * @param string $sRolCode

     * @return void

     */

    public function updateUser ($userData = array(), $sRolCode = '')

    {

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

        $this->userObj = new \RbacUsers();

        if (isset( $userData['USR_STATUS'] )) {

            if ($userData['USR_STATUS'] == 'ACTIVE') {

                $userData['USR_STATUS'] = 1;

            }

        }

        $this->userObj->update( $userData );

        if ($sRolCode != '') {

            $this->removeRolesFromUser( $userData['USR_UID'] );

            $this->assignRoleToUser( $userData['USR_UID'], $sRolCode );

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

    public function loadUserRolePermission ($sSystem, $sUser)

    {

        //in previous versions  we provided a path data and session we will cache the session Info for this user

        //now this is deprecated, and all the aUserInfo is in the memcache

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersRoles.php");

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Systems.php");

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RolesPeer.php");

        $this->sSystem = $sSystem;

        $this->usersRolesObj = new \UsersRoles();

        $this->systemObj = new \Systems();

        $fieldsSystem = $this->systemObj->loadByCode( $sSystem );

        $fieldsRoles = $this->usersRolesObj->getRolesBySystem( $fieldsSystem['SYS_UID'], $sUser );

        $fieldsPermissions = $this->usersRolesObj->getAllPermissions( $fieldsRoles['ROL_UID'], $sUser );

        $this->userObj = new \RbacUsers();

        $this->aUserInfo['USER_INFO'] = $this->userObj->load( $sUser );

        $this->aUserInfo[$sSystem]['SYS_UID'] = $fieldsSystem['SYS_UID'];

        $this->aUserInfo[$sSystem]['ROLE'] = $fieldsRoles;

        $this->aUserInfo[$sSystem]['PERMISSIONS'] = $fieldsPermissions;

        return $fieldsPermissions;

    }



    /**

     * Authenticate User

     *

     * @param array  $arrayUserData Data

     *

     * return array Return data of the User updated

     */

    public function authenticate($arrayUserData)

    {

        try {



        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Delete User

     *

     * @param string $usrUid Unique id of User

     *

     * return void

     */

    public function delete($usrUid)

    {

        try {

            //Verify data

            $this->throwExceptionIfNotExistsUser($usrUid, $this->arrayFieldNameForException["usrUid"]);



            \G::LoadClass('case');

            $oProcessMap = new \Cases();

            $USR_UID = $usrUid;

            $total = 0;

            $history = 0;

            $c = $oProcessMap->getCriteriaUsersCases('TO_DO', $USR_UID);

            $total += \ApplicationPeer::doCount($c);

            $c = $oProcessMap->getCriteriaUsersCases('DRAFT', $USR_UID);

            $total += \ApplicationPeer::doCount($c);

            $c = $oProcessMap->getCriteriaUsersCases('COMPLETED', $USR_UID);

            $history += \ApplicationPeer::doCount($c);

            $c = $oProcessMap->getCriteriaUsersCases('CANCELLED', $USR_UID);

            $history += \ApplicationPeer::doCount($c);

            if ($total > 0) {

                throw new \Exception(\G::LoadTranslation("ID_USER_CAN_NOT_BE_DELETED", array($USR_UID)));

            } else {

                $UID = $usrUid;

                \G::LoadClass('tasks');

                $oTasks = new \Tasks();

                $oTasks->ofToAssignUserOfAllTasks($UID);

                \G::LoadClass('groups');

                $oGroups = new \Groups();

                $oGroups->removeUserOfAllGroups($UID);

                $this->changeUserStatus($UID, 'CLOSED');

                $_GET['USR_USERNAME'] = '';

                $this->updateUser(array('USR_UID' => $UID, 'USR_USERNAME' => $_GET['USR_USERNAME']), '');

                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");

                $oUser = new \Users();

                $aFields = $oUser->load($UID);

                $aFields['USR_STATUS'] = 'CLOSED';

                $aFields['USR_USERNAME'] = '';

                $oUser->update($aFields);

                //Delete Dashboard

                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashletInstance.php");

                $criteria = new \Criteria( 'workflow' );

                $criteria->add( \DashletInstancePeer::DAS_INS_OWNER_UID, $UID );

                $criteria->add( \DashletInstancePeer::DAS_INS_OWNER_TYPE , 'USER');

                \DashletInstancePeer::doDelete( $criteria );

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get all Users

     *

     * @param array  $arrayFilterData Data of the filters

     * @param string $sortField       Field name to sort

     * @param string $sortDir         Direction of sorting (ASC, DESC)

     * @param int    $start           Start

     * @param int    $limit           Limit

     *

     * return array Return an array with all Users

     */

    public function getUsers($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)

    {

        try {

            $arrayUser = array();



            $numRecTotal = 0;



            //Verify data

            $process = new \ProcessMaker\BusinessModel\Process();



            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), array("start" => "start", "limit" => "limit"));



            //Set variables

            $filterName = "filter";



            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"])) {

                $arrayAux = array(

                    ""      => "filter",

                    "LEFT"  => "lfilter",

                    "RIGHT" => "rfilter"

                );



                $filterName = $arrayAux[(isset($arrayFilterData["filterOption"]))? $arrayFilterData["filterOption"] : ""];

            }



            //Get data

            if (!is_null($limit) && $limit . "" == "0") {

                //Return

                return array(

                    "total"     => $numRecTotal,

                    "start"     => (int)((!is_null($start))? $start : 0),

                    "limit"     => (int)((!is_null($limit))? $limit : 0),

                    $filterName => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",

                    "data"      => $arrayUser

                );

            }



            //Query

            $criteria = $this->getUserCriteria();



            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {

                $arraySearch = array(

                    ""      => "%" . $arrayFilterData["filter"] . "%",

                    "LEFT"  => $arrayFilterData["filter"] . "%",

                    "RIGHT" => "%" . $arrayFilterData["filter"]

                );



                $search = $arraySearch[(isset($arrayFilterData["filterOption"]))? $arrayFilterData["filterOption"] : ""];



                $criteria->add(

                    $criteria->getNewCriterion(\UsersPeer::USR_USERNAME,  $search, \Criteria::LIKE)->addOr(

                    $criteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, $search, \Criteria::LIKE))->addOr(

                    $criteria->getNewCriterion(\UsersPeer::USR_LASTNAME,  $search, \Criteria::LIKE))

                );

            }



            $criteria->add(\UsersPeer::USR_STATUS, "ACTIVE", \Criteria::EQUAL);



            //Number records total

            $criteriaCount = clone $criteria;



            $criteriaCount->clearSelectColumns();

            $criteriaCount->addSelectColumn("COUNT(" . \UsersPeer::USR_UID . ") AS NUM_REC");



            $rsCriteriaCount = \UsersPeer::doSelectRS($criteriaCount);

            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            $result = $rsCriteriaCount->next();

            $row = $rsCriteriaCount->getRow();



            $numRecTotal = (int)($row["NUM_REC"]);



            //Query

            if (!is_null($sortField) && trim($sortField) != "") {

                $sortField = strtoupper($sortField);



                if (in_array(\UsersPeer::TABLE_NAME . "." . $sortField, $criteria->getSelectColumns())) {

                    $sortField = \UsersPeer::TABLE_NAME . "." . $sortField;

                } else {

                    $sortField = \UsersPeer::USR_FIRSTNAME;

                }

            } else {

                $sortField = \UsersPeer::USR_FIRSTNAME;

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



            $rsCriteria = \UsersPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayUser[] = $this->getUserDataFromRecord($row);

            }



            //Return

            return array(

                "total"     => $numRecTotal,

                "start"     => (int)((!is_null($start))? $start : 0),

                "limit"     => (int)((!is_null($limit))? $limit : 0),

                $filterName => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",

                "data"      => $arrayUser

            );

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Upload image User

     *

     * @param string $userUid Unique id of User

     *

     */

    public function uploadImage($userUid)

    {

        try {

            //Verify data

            $this->throwExceptionIfNotExistsUser($userUid, $this->arrayFieldNameForException["usrUid"]);



            if (!$_FILES) {

                throw new \Exception(\G::LoadTranslation("ID_UPLOAD_ERR_NO_FILE"));

            }



            if (!isset($_FILES["USR_PHOTO"])) {

                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayFieldNameForException["usrPhoto"])));

            }



            if ($_FILES['USR_PHOTO']['error'] != 1) {

                if ($_FILES['USR_PHOTO']['tmp_name'] != '') {

                    $aAux = explode('.', $_FILES['USR_PHOTO']['name']);

                    \G::uploadFile($_FILES['USR_PHOTO']['tmp_name'], PATH_IMAGES_ENVIRONMENT_USERS, $userUid . '.' . $aAux[1]);

                    \G::resizeImage(PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.gif');

                }

            } else {

                $result->success = false;

                $result->fileError = true;

                throw (new \Exception($result));

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Check permission

     *

     * @param string $userUid        Unique uid of User

     * @param string $permissionCode Permission code

     *

     * return bool

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

        } catch (\Exception $e) {

            throw $e;

        }

    }

}


