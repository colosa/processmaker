<?php

class EnterpriseClass extends PMPlugin
{
    public function __construct()
    {
        set_include_path(PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEPARATOR . get_include_path());
    }

    public function getFieldsForPageSetup()
    {
        return array();
    }

    //update fields
    public function updateFieldsForPageSetup($oData)
    {
        return array();
    }

    public function setup()
    {
    }

    public function enterpriseSystemUpdate($data) //$data = $oData
    {
        if (count(glob(PATH_DATA_SITE . 'license/*.dat')) == 0) {
            return;
        }
        require_once ("classes/model/Users.php");
        $user = $data;
        $criteria = new Criteria("workflow");

        //SELECT
        $criteria->addSelectColumn(UsersPeer::USR_UID);
        //FROM
        //WHERE
        $criteria->add(UsersPeer::USR_USERNAME, $user->lName); //$user->lPassword
        $criteria->add(UsersPeer::USR_ROLE, "PROCESSMAKER_ADMIN");

        //query
        $rsSQLUSR = UsersPeer::doSelectRS($criteria);
        $rsSQLUSR->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $sw = 0;

        if (UsersPeer::doCount($criteria) > 0) {
            //if ($rsSQLUSR->getRecordCount() > 0) {
            $sw = 1;
        }

        /*
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $sql = "SELECT USR.USR_UID
                FROM   USERS AS USR
                WHERE  USR.USR_USERNAME = '" . $user->lName . "' AND USR.USR_ROLE = 'PROCESSMAKER_ADMIN'";
        $rsSQLUSR = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $sw = 0;

        if ($rsSQLUSR->getRecordCount() > 0) {
            $sw = 1;
        }
        */

        if ($sw == 1) {
            //Upgrade available
            $swUpgrade = 0;

            $addonList = AddonsStore::addonList();
            $addon = $addonList["addons"];

            if (count($addon) > 0) {
                $status = array("ready", "upgrade", "available");
                $pmVersion = EnterpriseUtils::pmVersion(PM_VERSION);

                foreach ($addon as $index => $value) {
                    if ($addon[$index]["id"] == "processmaker") {
                        if (version_compare($pmVersion . "", (EnterpriseUtils::pmVersion($addon[$index]["version"])) . "", "<")) {
                            $swUpgrade = 1;
                            break;
                        }
                    } else {
                        if (in_array($addon[$index]["status"], $status)) {
                            $swUpgrade = 1;
                            break;
                        }
                    }
                }
            }

            if ($swUpgrade == 1) {
                $_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"] = 1;
            }
        }
    }

    public function enterpriseLimitCreateUser()
    {
        $oServerConf = &ServerConf::getSingleton();
        $infoLicense =$oServerConf->getProperty('LICENSE_INFO');
        if (isset($infoLicense[SYS_SYS]['LIMIT_USERS'])) {
            $criteria = new Criteria('workflow');
            $criteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
            $count = UsersPeer::doCount($criteria);
            if ($count >= $infoLicense[SYS_SYS]['LIMIT_USERS'] ) {
                throw new Exception("You can\'t add more users to the System, this reach the limit of allowed users by license that it has installed now");
            }
        }
    }

    public function setHashPassword ($object)
    {
        $type = array('md5', 'sha256');
        if (!in_array($object->hash, $type)) {
            throw new Exception( 'Type: ' . $object->hash. ' No valid.');
            return false;
        }

        $config = new Configurations();
        $typeEncrypt = $config->getConfiguration('ENTERPRISE_SETTING_ENCRYPT', '');
        if ($typeEncrypt == null) {
            $typeEncrypt = array('current' => $object->hash, 'previous' => 'md5');
        } else {
            $typeEncrypt['previous'] = $typeEncrypt['current'];
            $typeEncrypt['current'] = $object->hash;
        }
        if ($object->hash != $typeEncrypt['previous']) {
            $config->aConfig = $typeEncrypt;
            $config->saveConfig('ENTERPRISE_SETTING_ENCRYPT', '');
        }

        $userProperty = new UsersProperties();

        $criteria = new Criteria($object->workspace->dbInfo['DB_RBAC_NAME']);
        $criteria->add(RbacUsersPeer::USR_STATUS, 0, Criteria::NOT_EQUAL);
        $dataset = RbacUsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        
        while ($dataset->next()) {
            $row = $dataset->getRow();
            $property = $userProperty->loadOrCreateIfNotExists($row['USR_UID'], array());
            $property['USR_LOGGED_NEXT_TIME'] = 1;
            $userProperty->update($property);
        }
    }
}
