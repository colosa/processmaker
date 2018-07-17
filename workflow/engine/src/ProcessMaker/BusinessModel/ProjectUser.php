<?php
namespace ProcessMaker\BusinessModel;

use \G;

class ProjectUser
{
    /**
     * Return the users to assigned to a process
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectUsers($sProcessUID)
    {
        try {
            Validator::proUid($sProcessUID, '$prj_uid');
            $aUsers = array();
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->setDistinct();
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);
            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);
            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);
            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);
            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
            $oCriteria->addJoin(\TaskUserPeer::TAS_UID, \TaskPeer::TAS_UID,  \Criteria::LEFT_JOIN);
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                if ($aRow['TU_RELATION'] == 1) {
                    $aUsers[] = array('usr_uid' => $aRow['USR_UID'],
                                      'usr_username' => $aRow['USR_USERNAME'],
                                      'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                      'usr_lastname' => $aRow['USR_LASTNAME']);
                } else {
                    $criteria = new \Criteria("workflow");
                    $criteria->addSelectColumn(\UsersPeer::USR_UID);
                    $criteria->addJoin(\GroupUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::INNER_JOIN);
                    $criteria->add(\GroupUserPeer::GRP_UID, $aRow['USR_UID'], \Criteria::EQUAL);
                    $criteria->add(\UsersPeer::USR_STATUS, "CLOSED", \Criteria::NOT_EQUAL);
                    $rsCriteria = \GroupUserPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                    while ($rsCriteria->next()) {
                        $row = $rsCriteria->getRow();
                        $oCriteriaU = new \Criteria('workflow');
                        $oCriteriaU->setDistinct();
                        $oCriteriaU->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
                        $oCriteriaU->addSelectColumn(\UsersPeer::USR_LASTNAME);
                        $oCriteriaU->addSelectColumn(\UsersPeer::USR_USERNAME);
                        $oCriteriaU->addSelectColumn(\UsersPeer::USR_EMAIL);
                        $oCriteriaU->add(\UsersPeer::USR_UID, $row['USR_UID']);
                        $oDatasetU = \UsersPeer::doSelectRS($oCriteriaU);
                        $oDatasetU->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                        while ($oDatasetU->next()) {
                            $aRowU = $oDatasetU->getRow();
                            $aUsers[] = array('usr_uid' => $row['USR_UID'],
                                              'usr_username' => $aRowU['USR_USERNAME'],
                                              'usr_firstname' => $aRowU['USR_FIRSTNAME'],
                                              'usr_lastname' => $aRowU['USR_LASTNAME']);
                        }
                    }
                }
                $oDataset->next();
            }
            $aUsersGroups = array();
            $exclude = array("");
            for ($i = 0; $i<=count($aUsers)-1; $i++) {
                if (!in_array(trim($aUsers[$i]["usr_uid"]) ,$exclude)) {
                    $aUsersGroups[] = $aUsers[$i];
                    $exclude[] = trim($aUsers[$i]["usr_uid"]);
                }
            }
            return $aUsersGroups;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return starting task
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectStartingTasks($sProcessUID)
    {
        try {
            Validator::proUid($sProcessUID, '$prj_uid');
            $aUsers = array();
            $usersIds = array();

            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);
            $oCriteria->addJoin(\TaskPeer::TAS_UID, \TaskUserPeer::TAS_UID,  \Criteria::LEFT_JOIN);
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);
            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();
                if (!in_array($aRow['USR_UID'], $usersIds)) {
                    $usersIds[] = $aRow['USR_UID'];
                }
            }

            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\GroupUserPeer::USR_UID);
            $oCriteria->addJoin(\TaskPeer::TAS_UID, \TaskUserPeer::TAS_UID,  \Criteria::LEFT_JOIN);
            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \GroupUserPeer::GRP_UID, \Criteria::LEFT_JOIN);
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);
            $oCriteria->add(\TaskUserPeer::TU_RELATION, 2);
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();
                if (!in_array($aRow['USR_UID'], $usersIds)) {
                    $usersIds[] = $aRow['USR_UID'];
                }
            }

            foreach($usersIds as $value) {
                $oCase = new \Cases();
                $startTasks = $oCase->getStartCases( $value );
                foreach ($startTasks as $task) {
                    if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $sProcessUID) ) {
                        $taskValue = explode( '(', $task['value'] );
                        $tasksLastIndex = count( $taskValue ) - 1;
                        $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
                        $aUsers[] = array('act_name' => $taskValue[0],
                                          'act_uid' => $task['uid']);
                    }
                }
            }
            $new = array();
            $exclude = array("");
            for ($i = 0; $i<=count($aUsers)-1; $i++) {
                if (!in_array(trim($aUsers[$i]["act_uid"]) ,$exclude)) {
                     $new[] = $aUsers[$i];
                     $exclude[] = trim($aUsers[$i]["act_uid"]);
                }
            }
            return $new;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return starting task by users
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $sUserUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectStartingTaskUsers($sProcessUID, $sUserUID)
    {
        try {
            Validator::proUid($sProcessUID, '$prj_uid');
            Validator::usrUid($sUserUID, '$usr_uid');
            $aUsers = array();
            $oCase = new \Cases();
            $startTasks = $oCase->getStartCases($sUserUID);
            if (sizeof($startTasks) > 1) {
                foreach ($startTasks as $task) {
                    if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $sProcessUID)) {
                        $taskValue = explode( '(', $task['value'] );
                        $tasksLastIndex = count( $taskValue ) - 1;
                        $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
                        $aUsers[] = array('act_uid' => $task['uid'],
                                          'act_name' => $taskValue[0]);
                    }
                }
            }
            if (sizeof($aUsers) < 1) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_INITIAL ACTIVITIES", array($sUserUID)));
            }
            return $aUsers;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the user that can start a task
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $sActivityUID {@min 32} {@max 32}
     * @param array  $oData
     *
     * return array
     *
     * @access public
     */
    public function projectWsUserCanStartTask($sProcessUID, $sActivityUID, $oData)
    {
        try {
            Validator::proUid($sProcessUID, '$prj_uid');
            /**
             * process_webEntryValidate
             * validates if the username and password are valid data and if the user assigned
             * to the webentry has the rights and persmissions required
             */
            $sTASKS = $sActivityUID;
            $sWS_USER = trim( $oData['username'] );
            $sWS_PASS = trim( $oData['password'] );
            if (\G::is_https()) {
                $http = 'https://';
            } else {
                $http = 'http://';
            }
            $endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
            @$client = new \SoapClient( $endpoint );
            $user = $sWS_USER;
            $pass = $sWS_PASS;
            $params = array ('userid' => $user,'password' => $pass);
            $result = $client->__SoapCall('login', array ($params));
            $fields['status_code'] = $result->status_code;
            $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
            $fields['version'] = $result->version;
            $fields['time_stamp'] = $result->timestamp;
            $messageCode = 1;

            /**
             * note added by gustavo cruz gustavo-at-colosa-dot-com
             * This is a little check to see if the GroupUser class has been declared or not.
             * Seems that the problem its present in a windows installation of PM however.
             * It's seems that could be replicated in a Linux server easily.
             * I recomend that in some way check already if a imported class is declared
             * somewhere else or maybe delegate the task to the G Class LoadClass method.
             */

            // if the user has been authenticated, then check if has the rights or
            // permissions to create the webentry
            if ($result->status_code == 0) {
                $oCriteria = new \Criteria( 'workflow' );
                $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::TAS_UID );
                $oCriteria->addSelectColumn( \UsersPeer::USR_USERNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_FIRSTNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_LASTNAME );
                $oCriteria->addSelectColumn( \TaskPeer::PRO_UID );
                $oCriteria->addJoin( \TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN );
                $oCriteria->addJoin( \TaskUserPeer::TAS_UID, \TaskPeer::TAS_UID, \Criteria::LEFT_JOIN );
                if ($sTASKS) {
                    $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                }
                $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                $oCriteria->add( \TaskPeer::PRO_UID, $sProcessUID );
                $userIsAssigned = \TaskUserPeer::doCount( $oCriteria );
                // if the user is not assigned directly, maybe a have the task a group with the user
                if ($userIsAssigned < 1) {
                    $oCriteria = new \Criteria( 'workflow' );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_USERNAME );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_FIRSTNAME );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_LASTNAME );
                    $oCriteria->addJoin( \UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->addJoin( \GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->addJoin( \TaskUserPeer::TAS_UID, \TaskPeer::TAS_UID, \Criteria::LEFT_JOIN );
                    if ($sTASKS) {
                        $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                    }
                    $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                    $oCriteria->add( \TaskPeer::PRO_UID, $sProcessUID );
                    $userIsAssigned = \GroupUserPeer::doCount( $oCriteria );
                    if (! ($userIsAssigned >= 1)) {
                        if ($sTASKS) {
                            throw new \Exception(\G::LoadTranslation("ID_USER_NOT_ID_ACTIVITY", array($sWS_USER, $sTASKS)));
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_USER_NOT_ACTIVITY", array($sWS_USER)));
                        }
                    }
                }
                $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                while ($aRow = $oDataset->getRow()) {
                    $messageCode = array('usr_uid' => $aRow['USR_UID'],
                                         'usr_username' => $aRow['USR_USERNAME'],
                                         'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                         'usr_lastname' => $aRow['USR_LASTNAME']);
                    $oDataset->next();
                }
            } else {
                throw (new \Exception( $result->message));
            }
            return $messageCode;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * User Login
     *
     * @param string $username Username
     * @param string $password Password
     *
     * return object Return object $response
     *               $response->status_code, 0 when User has been authenticated, any number otherwise
     *               $response->message, message
     */
    public function userLogin($username, $password)
    {
        try {
            $http = (\G::is_https())? "https://" : "http://";

            $client = new \SoapClient($http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/wsdl2");

            $params = array(
                "userid"   => $username,
                "password" => Bootstrap::hashPassword($password, '', true)
            );

            $response = $client->login($params);

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the User is assigned to Task
     *
     * @param string $userUid Unique id of User
     * @param string $taskUid Unique id of Task
     *
     * return bool Return true if the User is assigned to Task, false otherwise
     */
    public function userIsAssignedToTask($userUid, $taskUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TaskUserPeer::TAS_UID);
            $criteria->add(\TaskUserPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->add(\TaskUserPeer::USR_UID, $userUid, \Criteria::EQUAL);

            $rsCriteria = \TaskUserPeer::doSelectRS($criteria);

            //If the User is not assigned directly, maybe a have the Task a Group with the User
            if (!$rsCriteria->next()) {
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\UsersPeer::USR_UID);
                $criteria->addJoin(\UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN);
                $criteria->addJoin(\GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN);
                $criteria->add(\TaskUserPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                $criteria->add(\UsersPeer::USR_UID, $userUid, \Criteria::EQUAL);

                $rsCriteria = \UsersPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

