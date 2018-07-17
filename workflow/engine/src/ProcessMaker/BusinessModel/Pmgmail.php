<?php
namespace ProcessMaker\BusinessModel;

use \G;
use \Criteria;
use \UsersPeer;
use \GroupUserPeer;
use \ResultSet;
use ProcessMaker\Core\System;

/**
 * @copyright Colosa - Bolivia
 */
class Pmgmail {

    /**
     * Get User by usrGmail
     *
     * @param string $usr_gmail Unique id of User
     *
     * return uid
     *
     */
    public function getUserByEmail($usr_gmail)
    {
        //getting the user data
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
        $oUsers = new \Users();
        $response['user'] = $oUsers->loadByUserEmailInArray($usr_gmail);

        //getting the skin
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.system.php");
        $sysConf = new System();
        $responseSysConfig = $sysConf->getSystemConfiguration( PATH_CONFIG . 'env.ini' );
        $response['enviroment'] = $responseSysConfig['default_skin'];

        return $response;
    }

    /**
     * Get Application data by appUid
     *
     * @param string $app_uid Unique id of the app
     * @param string $index
     *
     * return row app_cache_view
     *
     */
    public function getDraftApp($app_uid, $index=1)
    {
        $c = new \Criteria( 'workflow' );

        $c->clearSelectColumns();
        $c->addSelectColumn( \AppCacheViewPeer::APP_NUMBER );
        $c->addSelectColumn( \AppCacheViewPeer::APP_STATUS );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_INDEX );
        $c->addSelectColumn( \AppCacheViewPeer::APP_DEL_PREVIOUS_USER );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( \AppCacheViewPeer::USR_UID );
        $c->addSelectColumn( \AppCacheViewPeer::PRO_UID );
        $c->addSelectColumn( \AppCacheViewPeer::APP_PRO_TITLE );
        $c->addSelectColumn( \AppCacheViewPeer::APP_TAS_TITLE );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_THREAD_STATUS );
        $c->addSelectColumn( \AppCacheViewPeer::TAS_UID );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_LAST_INDEX );
        $c->addSelectColumn( \AppCacheViewPeer::APP_UID );

        $c->add( \AppCacheViewPeer::APP_UID, $app_uid );
        $c->add( \AppCacheViewPeer::DEL_INDEX, $index );

        $rs = \AppCacheViewPeer::doSelectRS( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        $rows = Array ();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }
        return $rows;
    }

    public function gmailsForRouting($sUsrUid, $sTasUid, $sAppUid, $delIndex, $isSubprocess) {

        $taskProxy =  new \Task();
        $taskData = $taskProxy-> load($sTasUid);

        //guard condition, message events do not need to send emails
        if ($taskData['TAS_TYPE'] === 'START-MESSAGE-EVENT') {
            return;
        }

        if($sUsrUid === "")  {
            $targetEmails = $this->targetEmailsForUnassigned($sTasUid, $sAppUid);
            if ($targetEmails['to'] !== "" && $targetEmails['to'] !== null ) {
                $this->sendGmail($sAppUid, $targetEmails['to'].','.$targetEmails['cc'], $delIndex, $isSubprocess, true, null, null);
            }
        }
        else {
            $userObject = new \Users();
            $userData = $userObject->loadDetails($sUsrUid);
            if ($userData !== null) {
                $this->sendGmail($sAppUid, $userData['USR_EMAIL'], $delIndex, $isSubprocess, false, null, null);
            }
        }
    }

    public function gmailsIfSelfServiceValueBased($app_uid, $index, $arrayTask, $arrayData)
    {
        require_once(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Application.php");
        $resultMail = "";
        foreach ($arrayTask as $aTask) {
            //just self service tasks are processed in this function
            if ($aTask ["TAS_ASSIGN_TYPE"] !== "SELF_SERVICE") {
                continue;
            }
            $appData = $this->getDraftApp($app_uid, $index);
            if (count($appData) === 0) {
                return "appUid not found";
            }
            if (!isset ($aTask ["USR_UID"])) {
                $aTask ["USR_UID"] = "";
            }

            $application = $appData[0];
            $labelID = "PMUASS";
            $isSelfServiceValueBased = false;

            if ($aTask["TAS_ASSIGN_TYPE"] === "SELF_SERVICE") {
                $task = \TaskPeer::retrieveByPK($aTask["TAS_UID"]);
                if (trim($task->getTasGroupVariable()) != '') {
                    $isSelfServiceValueBased = true;
                }
            }

            if ($isSelfServiceValueBased) {
                $mailToAddresses = '';
                $mailCcAddresses = '';
                $targetIds = array();
                $criteria = new \Criteria ("workflow");
                $criteria->addSelectColumn(\AppAssignSelfServiceValueGroupPeer::GRP_UID);
                $criteria->addJoin(\AppAssignSelfServiceValuePeer::ID, \AppAssignSelfServiceValueGroupPeer::ID, \Criteria::LEFT_JOIN);
                $criteria->add(\AppAssignSelfServiceValuePeer::APP_UID, $app_uid);
                $criteria->add(\AppAssignSelfServiceValuePeer::DEL_INDEX, $aTask["DEL_INDEX"]);
                $rsCriteria = \AppAssignSelfServiceValuePeer::doSelectRs($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();
                    $targetIds[] = $row['GRP_UID'];
                }

                $usersToSend = $this->getSelfServiceValueBasedUsers($targetIds);

                foreach($usersToSend as $record) {
                    $toAux = (($record['USR_FIRSTNAME'] != '' || $record['USR_LASTNAME'] != '')
                                ? $record['USR_FIRSTNAME'] . ' ' . $record['USR_LASTNAME'] . ' '
                                : '') . '<' . $record['USR_EMAIL'] . '>';
                    if ($mailToAddresses == '') {
                        $mailToAddresses = $toAux;
                    } else {
                        $mailCcAddresses .= (($mailCcAddresses != '')? ',' : '') . $toAux;
                    }
                }

                $resultMail = $this->sendEmailWithApplicationData($application,  $labelID, $mailToAddresses, $mailCcAddresses);
            }
        }
        return $resultMail;
    }

    /**
     * Send email using appUid and mail
     *
     * @param string $app_uid Unique id of the app
     * @param string $mail
     *
     * return uid
     *
     */
    public function sendGmail($app_uid, $mailToAddresses, $index, $isSubprocess = false, $isSelfService = false, $arrayTask = null, $arrayData = null)
    {
        //getting the default email server
        $defaultEmail = $this->emailAccount();
        if ($defaultEmail === null) {
            error_log(G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_ENABLED'));
            return false;
        }

        $mailCcAddresses = "";

        $appData =  $this->getDraftApp($app_uid, $index);
        if (count($appData) === 0) {
            return;
        }
        $application = $appData[0];
        $this->sendEmailWithApplicationData($application,
                $this->getEmailType($index, $isSubprocess, $isSelfService),
                $mailToAddresses,
                $mailCcAddresses);
    }

    /**
     * Get if the license has the feature
     *
     * return uid
     *
     */
    public function hasGmailFeature()
    {
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.licensedFeatures.php");

        $licensedFeatures = new \PMLicensedFeatures();
        if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
            return false;
        }else {
            return true;
        }
    }

    /**
     * Get the default 'email from account' that is used to send emails in the server email in PM
     *
     * return uid
     *
     */
    public function emailAccount()
    {
        $emailServer = new \EmailServer();
        $response = $emailServer->loadDefaultAccount();

        return $response['MESS_ACCOUNT'];
    }

    /**
     * Business Model to delete all the labels of an acount
     *
     * @param string $mail
     *
     * return uid
     *
     */
    public function deleteLabels($mail)
    {
        $oLabels = new \LabelsGmail();

        $response = $oLabels->deletePMGmailLabels($mail);

        return $response;
    }

    public function modifyMailToPauseCase($appUid, $appDelIndex)
    {
        $oLabels = new \LabelsGmail();
        $oResponse = $oLabels->setLabelsToPauseCase($appUid, $appDelIndex);
    }

    public function modifyMailToUnpauseCase($appUid, $appDelIndex)
    {
        $oLabels = new \LabelsGmail();
        $oResponse = $oLabels->setLabelsToUnpauseCase($appUid, $appDelIndex);
    }

    private function getEmailType($index, $isSubprocess, $isSelfService) {
        $retval = "";
        if ($isSelfService) {
            $retval = "PMUASS";
        }
        else {
            $retval = ($index === 1 && !$isSubprocess) ? "PMDRFT" : "PMIBX";
        }
        if ($retval === "") {
            throw new Exception('Is not possible to determine the email type');
        }
        return $retval;
    }

    private function createMailTemplateFile()
    {
        $pathTemplate = PATH_DATA_SITE . "mailTemplates" . PATH_SEP . "pmGmail.html";
        if (!file_exists($pathTemplate)) {
            $file = @fopen($pathTemplate, "w");
            fwrite($file, '<div>');
            fwrite($file, '<span style="display: none !important;">');
            fwrite($file, '-**- Process Name: @#proName<br/>');
            fwrite($file, '-**- Case Number: @#appNumber<br/>');
            fwrite($file, '-**- Case UID: @#caseUid<br/>');
            fwrite($file, '-**- Task Name: @#taskName<br/>');
            fwrite($file, '-**- Index: @#index<br/>');
            fwrite($file, '-**- Action: @#action<br/>');
            fwrite($file, '-**- Previous User: @#prevUser<br/>');
            fwrite($file, '-**- Delegate Date: @#delDate<br/>');
            fwrite($file, '-**- Process Id: @#proUid<br/>');
            fwrite($file, '-**- Type: @#type<br/>');
            fwrite($file, '-**- FormFields: <br/>');
            fwrite($file, '</span>');
            fwrite($file, '</div>');
            fclose($file);
        }
    }

    private function getFormData($appUid, $index) {
        $oApplication = new \Application();
        $formData = $oApplication->Load($appUid);

        $frmData = unserialize($formData['APP_DATA']);
        $dataFormToShowString = "";
        foreach ($frmData as $field => $value) {
            if (($field != 'SYS_LANG') &&
                ($field != 'SYS_SKIN') &&
                ($field != 'SYS_SYS') &&
                ($field != 'APPLICATION') &&
                ($field != 'PROCESS') &&
                ($field != 'TASK') &&
                ($field != 'INDEX') &&
                ($field != 'USER_LOGGED') &&
                ($field != 'USR_USERNAME') &&
                ($field != 'DYN_CONTENT_HISTORY') &&
                ($field != 'PIN') &&
                (!is_array($value))
            ) {
                $dataFormToShowString .= " " . $field . " " . $value;
            }
        }
        $change = array('[', ']', '"');
        $fdata = str_replace($change, ' ', $dataFormToShowString);
        return $fdata;
    }

    private function sendEmailWithApplicationData ($application,  $emailTypeLabel, $mailToAddresses, $mailCcAddresses) {
        $dataFormToShowString = '';
        $this->createMailTemplateFile();
        $change = array('[', ']', '"');
        $fdata = str_replace($change, ' ', $dataFormToShowString);
        $aFields = array(
            'proName' => $application['APP_PRO_TITLE'],
            'appNumber' => $application['APP_NUMBER'],
            'caseUid' => $application['APP_UID'],
            'taskName' => $application['APP_TAS_TITLE'],
            'index' =>  $application['DEL_INDEX'],
            'action' => $application['APP_STATUS'],
            'prevUser' => $application['APP_DEL_PREVIOUS_USER'],
            'delDate' => $application['DEL_DELEGATE_DATE'],
            'proUid' => $application['PRO_UID'],
            'type' => $emailTypeLabel,
            'oform' => $fdata
        );

        $subject = "[PM] " . $application['APP_PRO_TITLE'] . " (" . $application['DEL_INDEX'] . ") Case: " . $application['APP_NUMBER'];

        //getting the default email server
        $defaultEmail = $this->emailAccount();

        if ($defaultEmail === null) {
            error_log(G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_ENABLED'));
            return false;
        }

        $ws = new \WsBase();
        $resultMail = $ws->sendMessage(
            $application['APP_UID'],
            $defaultEmail, //From,
            $mailToAddresses,//$To,
            $mailCcAddresses,//$Cc
            '',
            $subject,
            'pmGmail.html',//template
            $aFields, //fields
            array(),
            true,
            0,
            array(),
            1
        );
    }

    private function targetEmailsForUnassigned($taskUid, $appUid)
    {
        $sTo = null;
        $sCc = null;
        $arrayResp = array ();
        $task = new \Tasks ();
        $group = new \Groups ();
        $oUser = new \Users ();

        $to = null;
        $cc = null;

        if (isset ( $taskUid ) && ! empty ( $taskUid )) {
            $arrayTaskUser = array ();

            $arrayAux1 = $task->getGroupsOfTask ( $taskUid, 1 );

            foreach ( $arrayAux1 as $arrayGroup ) {
                $arrayAux2 = $group->getUsersOfGroup ( $arrayGroup ["GRP_UID"] );

                foreach ( $arrayAux2 as $arrayUser ) {
                    $arrayTaskUser [] = $arrayUser ["USR_UID"];
                }
            }

            $arrayAux1 = $task->getUsersOfTask ( $taskUid, 1 );

            foreach ( $arrayAux1 as $arrayUser ) {
                $arrayTaskUser [] = $arrayUser ["USR_UID"];
            }

            $arrayTaskUser = array_unique($arrayTaskUser);

            $criteria = new \Criteria ( "workflow" );

            $criteria->addSelectColumn ( \UsersPeer::USR_UID );
            $criteria->addSelectColumn ( \UsersPeer::USR_USERNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_FIRSTNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_LASTNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_EMAIL );
            $criteria->add (\UsersPeer::USR_UID, $arrayTaskUser, \Criteria::IN);
            $rsCriteria = \UsersPeer::doSelectRs ( $criteria );
            $rsCriteria->setFetchmode (\ResultSet::FETCHMODE_ASSOC);

            $sw = 1;

            while ( $rsCriteria->next () ) {
                $row = $rsCriteria->getRow ();

                $toAux = ((($row ["USR_FIRSTNAME"] != "") || ($row ["USR_LASTNAME"] != "")) ? $row ["USR_FIRSTNAME"] . " " . $row ["USR_LASTNAME"] . " " : "") . "<" . $row ["USR_EMAIL"] . ">";

                if ($sw == 1) {
                    $to = $toAux;
                    $sw = 0;
                } else {
                    $cc = $cc . (($cc != null) ? "," : null) . $toAux;
                }
            }


            $arrayResp ['to'] = $to;
            $arrayResp ['cc'] = $cc;
        }
        return $arrayResp;
    }

    /**
     * Returns a list of users emails that are the destion of the emails
     * that will be sent, based in the list of groups or users that are
     * passed to this function.
     * @param $targetIds, array or single value of usr ids or group ids
     * @return array, list of emails
     */
    private function getSelfServiceValueBasedUsers($targetIds) {
        $result = [];
        $criteria = new Criteria('workflow');
        $criteria->setDistinct();
        $criteria->addSelectColumn(UsersPeer::USR_UID);
        $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
        $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
        $criteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
        $criteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
        $rsCriteria = null;

        if (is_array($targetIds)) {
            $criteriaUid = $criteria->getNewCriterion(UsersPeer::USR_UID, $targetIds, Criteria::IN);
            $criteriaUid->addOr($criteria->getNewCriterion(GroupUserPeer::GRP_UID, $targetIds, Criteria::IN));
            $criteria->add($criteriaUid);
            $rsCriteria = GroupUserPeer::doSelectRS($criteria);
        }
        else {
            $criteriaUid = $criteria->getNewCriterion(UsersPeer::USR_UID, $targetIds, Criteria::EQUAL);
            $criteriaUid->addOr($criteria->getNewCriterion(GroupUserPeer::GRP_UID, $targetIds, Criteria::EQUAL));
            $criteria->add($criteriaUid);
            $rsCriteria = GroupUserPeer::doSelectRS($criteria);
        }

        if (!is_null($rsCriteria)) {
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $record = $rsCriteria->getRow();
                $result [] = $record;
            }
        }

        return $result;
    }
}

