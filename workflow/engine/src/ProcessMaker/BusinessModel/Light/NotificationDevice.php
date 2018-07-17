<?php

namespace ProcessMaker\BusinessModel\Light;

use ProcessMaker\Core\System;
use \ProcessMaker\Services\Api;
use G;

class NotificationDevice
{


    public function checkMobileNotifications()
    {
        $conf = System::getSystemConfiguration('', '', SYS_SYS);
        $activeNotifications = true;
        if (isset($conf['mobileNotifications'])) {
            $activeNotifications = $conf['mobileNotifications'] == 1 ? true : false;
        }
        return $activeNotifications;
    }

    /**
     * Post Create register device with userUid
     *
     * @param array $request_data
     * @param string $use_uid
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     *
     */
    public function saveDevice($use_uid, $request_data)
    {
        $arrayData = array();
        $arrayData['USR_UID'] = $use_uid;
        $arrayData['DEV_REG_ID'] = $request_data['deviceIdToken'];
        $arrayData['SYS_LANG'] = $request_data['sysLanguage'];
        $arrayData['DEV_TYPE'] = $request_data['deviceType'];
        $arrayData['DEV_STATUS'] = 'active';

        $oNoti = new \NotificationDevice();
        $devices = $oNoti->loadByDeviceId($request_data['deviceIdToken']);
        $response = array();
        if (!$devices){
            if($oNoti->create($arrayData)){
                $response["devUid"] = $oNoti->getDevUid();
                $response["message"] = G:: LoadTranslation("ID_RECORD_SAVED_SUCCESFULLY");
                G::auditLog("Create", "Device Save: Device ID (".$oNoti->getDevUid().") ");
            }
        } else {
            if($oNoti->remove($devices[0]['DEV_UID'],$devices[0]['USR_UID'])){
                $arrayData['USR_UID'] = $use_uid;
                $arrayData['DEV_REG_ID'] = $devices[0]['DEV_REG_ID'];
                $arrayData['SYS_LANG'] = $devices[0]['SYS_LANG'];
                $arrayData['DEV_TYPE'] = $devices[0]['DEV_TYPE'];
                $arrayData['DEV_STATUS'] = 'active';

                if($devUid = $oNoti->createOrUpdate($arrayData)){
                    $response["devUid"] = $devUid;
                    $response["message"] = G:: LoadTranslation("ID_RECORD_SAVED_SUCCESFULLY");
                    G::auditLog("Create", "Device Save: Device ID (".$oNoti->getDevUid().") ");
                } else {
                    throw new \Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
                }
            } else {
                throw new \Exception(G::LoadTranslation("ID_RECORD_DOES_NOT_EXIST"));
            }
        }
        return $response;
    }

    /**
     * Update register device with userUid
     *
     * @param array $request_data
     * @param string $dev_uid
     * @param string $use_uid
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     *
     */
    public function updateDevice($dev_uid, $use_uid, $request_data)
    {

        $arrayData = array();
        $arrayData['USR_UID'] = $use_uid;
        $arrayData['DEV_UID'] = $dev_uid;
        if(isset($request_data['deviceIdToken'])){
            $arrayData['DEV_REG_ID'] = $request_data['deviceIdToken'];
        }
        if(isset($request_data['sysLanguage'])) {
            $arrayData['SYS_LANG'] = $request_data['sysLanguage'];
        }
        if(isset($request_data['deviceType'])) {
            $arrayData['DEV_TYPE'] = $request_data['deviceType'];
        }
        $oNoti = new \NotificationDevice();
        $response = array();
        if($oNoti->update($arrayData)){
            $response["message"] = G:: LoadTranslation("ID_RECORD_SAVED_SUCCESFULLY");
            G::auditLog("Update", "Device Save: Device ID (".$oNoti->getDevUid().") ");
        }
        return $response;

    }

    /**
     * Send Message each user id
     *
     * @param array $request_data
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     *
     */
    public function sendMessage($userIds, $message, $data = null)
    {
        try {
            $oNoti = new \NotificationDevice();
            $devices = array();
            if (is_array($userIds)){
                foreach ($userIds as $id) {
                    $deviceUser = $oNoti->loadByUsersId($id);
                    $devices = array_merge($devices, $deviceUser);
                }
            } else {
                $devices = $oNoti->loadByUsersId($userIds);
            }

            $devicesAndroidIds = array();
            $devicesAppleIds = array();
            foreach ($devices as $dev) {
                switch ($dev['DEV_TYPE']) {
                    case "apple":
                        $devicesAppleIds[] = $dev['DEV_REG_ID'];
                        break;
                    case "android":
                        $devicesAndroidIds[] = $dev['DEV_REG_ID'];
                        break;
                }
            }
            if (count($devicesAppleIds) > 0) {
                $oNotification = new PushMessageIOS();
                $oNotification->setSettingNotification();
                $oNotification->setDevices($devicesAppleIds);
                $response['android'] = $oNotification->send($message, $data);
            }
            if (count($devicesAndroidIds) > 0) {
                $oNotification = new PushMessageAndroid();
                $oNotification->setSettingNotification();
                $oNotification->setDevices($devicesAndroidIds);
                $response['apple'] = $oNotification->send($message, $data);
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), Api::STAT_APP_EXCEPTION);
        }
        return $response;
    }

    /**
     * @param $appFields
     * @param $nextDel
     * @param $iNewDelIndex
     * @return array
     * @throws \Exception
     */
    public function routeCaseNotificationDevice($appFields, $nextDel, $iNewDelIndex)
    {
        try {
            $response = array();
            $typeList = 'todo';
            $arrayTaskUser = array();
            switch ((array_key_exists('TAS_ASSIGN_TYPE', $nextDel))? $nextDel['TAS_ASSIGN_TYPE'] : '') {
                case "SELF_SERVICE":
                    $arrayTaskUser = $this->getTaskUserSelfService($nextDel["TAS_UID"], $appFields);
                    $typeList = 'unassigned';
                    break;
                default:
                    if (isset($nextDel["USR_UID"]) && !empty($nextDel["USR_UID"])) {
                        $arrayTaskUser = $nextDel["USR_UID"];
                    }
                    break;
            }

            $userIds = $arrayTaskUser;
            //sub process
            $taskAssignType = (isset($nextDel["TAS_ASSIGN_TYPE"])) ? $nextDel["TAS_ASSIGN_TYPE"] : $nextDel["SP_TYPE"];
            $message = '#' . $appFields['APP_NUMBER'] . ' : ' . $appFields['APP_TITLE'];
            $data = array(
                'processId' => $appFields['PRO_UID'],
                'taskId' => $nextDel["TAS_UID"],
                'taskAssignType' => $taskAssignType,
                'caseId' => $appFields['APP_UID'],
                'caseTitle' => $appFields['APP_TITLE'],
                'delIndex' => $iNewDelIndex,
                'typeList' => $typeList,
                'caseNumber' => $appFields['APP_NUMBER']
            );

            if ($userIds) {
                $oNoti = new \NotificationDevice();
                if (is_array($userIds)) {
                    $devices = $oNoti->loadUsersArrayId($userIds);
                } else {
                    $devices = $oNoti->loadByUsersId($userIds);
                }

                $devicesAndroidIds = array();
                $devicesAppleIds = array();
                foreach ($devices as $dev) {
                    switch ($dev['DEV_TYPE']) {
                        case "apple":
                            $devicesAppleIds[] = $dev['DEV_REG_ID'];
                            break;
                        case "android":
                            $devicesAndroidIds[] = $dev['DEV_REG_ID'];
                            break;
                    }
                }
                if (count($devicesAppleIds) > 0) {
                    $arrayData = array();
                    $arrayData['NOT_FROM'] = $appFields['APP_CUR_USER'];
                    $arrayData['DEV_TYPE'] = 'apple';
                    $arrayData['DEV_UID'] = serialize($devicesAppleIds);
                    $arrayData['NOT_MSG'] = $message;
                    $arrayData['NOT_DATA'] = serialize($data);
                    $arrayData['NOT_STATUS'] = "pending";
                    $arrayData['APP_UID'] = $appFields['APP_UID'];
                    $arrayData['DEL_INDEX'] = $iNewDelIndex;
                    $notQueue = new \NotificationQueue();
                    $notQueue->create($arrayData);
                }
                if (count($devicesAndroidIds) > 0) {
                    $arrayData = array();
                    $arrayData['NOT_FROM'] = $appFields['APP_CUR_USER'];
                    $arrayData['DEV_TYPE'] = 'android';
                    $arrayData['DEV_UID'] = serialize($devicesAndroidIds);
                    $arrayData['NOT_MSG'] = $message;
                    $arrayData['NOT_DATA'] = serialize($data);
                    $arrayData['NOT_STATUS'] = "pending";
                    $arrayData['APP_UID'] = $appFields['APP_UID'];
                    $arrayData['DEL_INDEX'] = $iNewDelIndex;
                    $notQueue = new \NotificationQueue();
                    $notQueue->create($arrayData);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), Api::STAT_APP_EXCEPTION);
        }
        return $response;
    }

    public function getTaskUserSelfService($tas_uid, $appFields)
    {
        $oTask = new \Tasks();
        $oGroup = new \Groups();
        $taskNextDel = \TaskPeer::retrieveByPK($tas_uid);
        $arrayTaskUser = array();

        if ($taskNextDel->getTasAssignType() == "SELF_SERVICE" && trim($taskNextDel->getTasGroupVariable()) != "") {
            // Self Service Value Based Assignment
            $nextTaskGroupVariable = trim($taskNextDel->getTasGroupVariable(), " @#");
            if (isset($appFields["APP_DATA"][$nextTaskGroupVariable])) {
                $dataGroupVariable = $appFields["APP_DATA"][$nextTaskGroupVariable];
                $dataGroupVariable = (is_array($dataGroupVariable))? $dataGroupVariable : trim($dataGroupVariable);
                if (!empty($dataGroupVariable) && is_array($dataGroupVariable)){
                    $arrayTaskUser[] = $dataGroupVariable;
                } elseif(!empty($dataGroupVariable)) {
                    $arrayUsersOfGroup = $oGroup->getUsersOfGroup($dataGroupVariable);
                    foreach ($arrayUsersOfGroup as $arrayUser) {
                        $arrayTaskUser[] = $arrayUser["USR_UID"];
                    }
                }
            }
        } else { // Self Service
            $arrayGroupsOfTask = $oTask->getGroupsOfTask($tas_uid, 1);
            foreach ($arrayGroupsOfTask as $arrayGroup) {
                $arrayUsersOfGroup = $oGroup->getUsersOfGroup($arrayGroup["GRP_UID"]);
                foreach ($arrayUsersOfGroup as $arrayUser) {
                    $arrayTaskUser[] = $arrayUser["USR_UID"];
                }
            }
            $arrayUsersOfTask = $oTask->getUsersOfTask($tas_uid, 1);
            foreach ($arrayUsersOfTask as $arrayUser) {
                $arrayTaskUser[] = $arrayUser["USR_UID"];
            }
        }

        return $arrayTaskUser;
    }
}
