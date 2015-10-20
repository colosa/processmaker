<?php

namespace ProcessMaker\BusinessModel\Light;

use G;

class NotificationDevice
{
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
            throw new \Exception(\Api::STAT_APP_EXCEPTION, $e->getMessage());
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
    public function routeCaseNotification($currentUserId, $processId, $currentTaskId, $appFields, $aTasks, $nextIndex)
    {
        try {
            $oUser = new \Users();
            $aUser = $oUser->load( $currentUserId );

            $response = array();
            $task = new \Tasks();
            $group = new \Groups();
            foreach ($aTasks as $aTask) {
                $arrayTaskUser = array();
                switch ($aTask["TAS_ASSIGN_TYPE"]) {
                    case "SELF_SERVICE":
                        if (isset($aTask["TAS_UID"]) && !empty($aTask["TAS_UID"])) {
                            $arrayAux1 = $task->getGroupsOfTask($aTask["TAS_UID"], 1);
                            foreach ($arrayAux1 as $arrayGroup) {
                                $arrayAux2 = $group->getUsersOfGroup($arrayGroup["GRP_UID"]);
                                foreach ($arrayAux2 as $arrayUser) {
                                    $arrayTaskUser[] = $arrayUser["USR_UID"];
                                }
                            }
                            $arrayAux1 = $task->getUsersOfTask($aTask["TAS_UID"], 1);

                            foreach ($arrayAux1 as $arrayUser) {
                                $arrayTaskUser[] = $arrayUser["USR_UID"];
                            }
                        }
                        break;
                    default:
                        if (isset($aTask["USR_UID"]) && !empty($aTask["USR_UID"])) {
                            $arrayTaskUser = $aTask["USR_UID"];
                        }
                        break;
                }

//                $oTask = new \Task();
//                $currentTask = $oTask->load($aTask['TAS_UID']);
                $delIndex = null;
                foreach ($nextIndex as $nIndex) {
                    if($aTask['TAS_UID'] == $nIndex['TAS_UID']){
                        $delIndex = $nIndex['DEL_INDEX'];
                        break;
                    }
                }

                $userIds = $arrayTaskUser;
                $message = '#'. $appFields['APP_NUMBER'] . ' : '.$appFields['APP_TITLE'];
                $data = array(
                    'processId' => $processId,
                    'taskId' => $aTask["TAS_UID"],
                    'caseId' => $appFields['APP_UID'],
                    'caseTitle' => $appFields['APP_TITLE'],
                    'delIndex' => $delIndex,
                    'typeList' => 'todo'
                );

                if ($userIds) {

                    $oNoti = new \NotificationDevice();
                    $devices = array();
                    if (is_array($userIds)){
                        foreach ($userIds as $id) {
                            $deviceUser = $oNoti->loadByUsersId($id);
                            $devices = array_merge($devices, $deviceUser);
                        }
                    } else {
                        $devices = $oNoti->loadByUsersId($userIds);
                        $lists   = new \ProcessMaker\BusinessModel\Lists();
                        $counter = $lists->getCounters($userIds);
                        $light   = new \ProcessMaker\Services\Api\Light();
                        $result  = $light->parserCountersCases($counter);
                        $data['counters'] = $result;
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
                        $response['apple'] = $oNotification->send($message, $data);
                    }
                    if (count($devicesAndroidIds) > 0) {
                        $oNotification = new PushMessageAndroid();
                        $oNotification->setSettingNotification();
                        $oNotification->setDevices($devicesAndroidIds);
                        $response['android'] = $oNotification->send($message, $data);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(\Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

}
