<?php
namespace ProcessMaker\BusinessModel;

class EmailEvent
{
    /*private $arrayFieldDefinition = array(
        "EMAIL_EVENT_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "emailEventUid"),
        "PRJ_UID"           => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "projectUid"),
        "EVN_UID"           => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "eventUid"),
        "EMAIL_EVENT_FROM"          => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "messageTypeUid"),
        "EMAIL_EVENT_TO"     => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "EmailEventUserUid"),
        "EMAIL_EVENT_SUBJECT"   => array("type" => "array",  "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "EmailEventVariables"),
        "PRF_UID" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "EmailEventCorrelation")
    );
    */
    
    /**
     * Get the email accounts of the current workspace
     *     
     * return array
     */
    public function getEmailEventAccounts()
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->clearSelectColumns();
            $criteria->addSelectColumn(\UsersPeer::USR_UID);
            $criteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $criteria->addAsColumn('UID', 'USR_UID');
            $criteria->addAsColumn('EMAIL', 'USR_EMAIL');
            $criteria->add(\UsersPeer::USR_STATUS, "ACTIVE");
            $result = \UsersPeer::doSelectRS($criteria);
            $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $result->next();
            $accountsArray = array();
            while ($aRow = $result->getRow()) {
                if (($aRow['USR_EMAIL'] != null) || ($aRow['USR_EMAIL'] != "")) {
                    $accountsArray[] = array_change_key_case($aRow, CASE_LOWER);
                } 
                $result->next();
            }
            return $accountsArray;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the email server accounts of the current workspace
     *     
     * return array
     */
    public function getEmailEventServerAccounts()
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->clearSelectColumns();
            $criteria->addSelectColumn(\EmailServerPeer::MESS_UID);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_FROM_MAIL);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_ACCOUNT);
            $criteria->addAsColumn('UID', 'MESS_UID');
            $result = \EmailServerPeer::doSelectRS($criteria);
            $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $result->next();
            $accountsArray = array();
            while ($aRow = $result->getRow()) {
                if ($aRow['MESS_UID'] != null) {
                    if($aRow['MESS_FROM_MAIL'] == "") {
                        $aRow['EMAIL'] = $aRow['MESS_ACCOUNT'];  
                    } else {
                        $aRow['EMAIL'] = $aRow['MESS_FROM_MAIL']; 
                    }
                    $accountsArray[] = array_change_key_case($aRow, CASE_LOWER);
                } 
                $result->next();
            }
            return $accountsArray;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the Email-Event data
     * @var string $evn_uid. uid for activity  
     * @var string $pro_uid. uid for process  
     * return array
     */
    public function getEmailEventData($pro_uid, $evn_uid)
    {
        try {
            //Get data
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::EVN_UID, $evn_uid, \Criteria::EQUAL);
            $criteria->add(\EmailEventPeer::PRJ_UID, $pro_uid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if(is_array($row)) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the Email-Event data
     * @var string $emailEventUid. uid for email event
     * @var string $pro_uid. uid for process  
     * return array
     */
    public function getEmailEventDataByUid($pro_uid, $emailEventUid)
    {
        try {
            //Get data
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::EMAIL_EVENT_UID, $emailEventUid, \Criteria::EQUAL);
            $criteria->add(\EmailEventPeer::PRJ_UID, $pro_uid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if(is_array($row)) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Save Data for Email-Event
     * @var string $prj_uid. Uid for Process
     * @var string $arrayData. Data for Trigger
     *     
     * return array
     */
    public function save($prj_uid = '', $arrayData = array())
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($prj_uid, "projectUid");

            //Create
            $db = \Propel::getConnection("workflow");

            try {
                $emailEvent = new \EmailEvent();
                
                $emailEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);
                
                $emailEventUid = \ProcessMaker\Util\Common::generateUID();

                $emailEvent->setEmailEventUid($emailEventUid);
                $emailEvent->setPrjUid($prj_uid);

                $db->begin();
                $result = $emailEvent->save();
                $db->commit();
                
                return $this->getEmailEvent($emailEventUid);
            } catch (\Exception $e) {
                $db->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }    
    }
    
    /**
     * Update Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     * @param array  $arrayData Data
     *
     * return array Return data of the Email-Event updated
     */
    public function update($emailEventUid, array $arrayData)
    {
        try {
            //Verify data
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $arrayDataBackup = $arrayData;

            //Set variables
            $arrayEmailEventData = $this->getEmailEvent($emailEventUid);

            //Verify data
            $this->verifyIfEmailEventExists($emailEventUid); 

            //Update
            $db = \Propel::getConnection("workflow");

            try {
                $emailEvent = \EmailEventPeer::retrieveByPK($emailEventUid);
                $emailEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);
         
                $db->begin();
                $result = $emailEvent->save();
                $db->commit();

                $arrayData = $arrayDataBackup;
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
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
     * Delete Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     *
     * return void
     */
    public function delete($pro_uid, $emailEventUid, $passValidation = true)
    {
        try {
            //Verify data
            if($passValidation) {
                $this->verifyIfEmailEventExists($emailEventUid);
            
                //Delete file
                $filesManager = new \ProcessMaker\BusinessModel\FilesManager();
                $arrayData = $this->getEmailEventDataByUid($pro_uid, $emailEventUid); 
                $arrayData = array_change_key_case($arrayData, CASE_UPPER);
                if(sizeof($arrayData)) {
                    $prfUid = $arrayData['PRF_UID'];
                    $filesManager->deleteProcessFilesManager('',$prfUid);
                }
            }
            //Delete Email event
            $criteria = new \Criteria("workflow");
            $criteria->add(\EmailEventPeer::EMAIL_EVENT_UID, $emailEventUid, \Criteria::EQUAL);
            $result = \EmailEventPeer::doDelete($criteria);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Delete Email-Event by event uid
     *
     * @param string $emailEventUid Unique id of Email-Event
     *
     * return void
     */
    public function deleteByEvent($prj_uid, $evn_uid)
    {
        try {
            //Verify data
            if (!$this->existsEvent($prj_uid, $evn_uid)) {
                throw new \Exception(\G::LoadTranslation("ID_EMAIL_EVENT_DEFINITION_DOES_NOT_EXIST"));
            }
            $arrayData = $this->existsEvent($prj_uid, $evn_uid);
            $this->delete($prj_uid, $arrayData[0]);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get data of a Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     * @param bool   $flagGetRecord             Value that set the getting
     *
     * return array Return an array with data of a Email-Event
     */
    public function getEmailEvent($emailEventUid)
    {
        try {
            //Verify data
           $this->verifyIfEmailEventExists($emailEventUid);

            //Get data
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::EMAIL_EVENT_UID, $emailEventUid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();

            //Return
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Verify if exists the Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     *
     * return bool Return true if exists the Email-Event, false otherwise
     */
    public function exists($emailEventUid)
    {
        try {
            $obj = \EmailEventPeer::retrieveByPK($emailEventUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get criteria for Email-Event
     *
     * return object
     */
    public function getEmailEventCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_UID);
            $criteria->addSelectColumn(\EmailEventPeer::PRJ_UID);
            $criteria->addSelectColumn(\EmailEventPeer::EVN_UID);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_FROM);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_TO);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_SUBJECT);
            $criteria->addSelectColumn(\EmailEventPeer::PRF_UID);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function verifyIfEmailEventExists($emailEventUid)
    {
        if (!$this->exists($emailEventUid)) {
            throw new \Exception(\G::LoadTranslation("ID_EMAIL_EVENT_DEFINITION_DOES_NOT_EXIST", array("Email Event Uid", $emailEventUid)));
        }
    }
    
    /**
     * Verify if exists the Event of a Message-Event-Definition
     *
     * @param string $projectUid                         Unique id of Project
     * @param string $eventUid                           Unique id of Event
     *
     * return bool Return true if exists the Event of a Message-Event-Definition, false otherwise
     */
    public function existsEvent($projectUid, $eventUid)
    {
        try {
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\EmailEventPeer::EVN_UID, $eventUid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if(is_array($row)) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            return (sizeof($row))? $row : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Email-event for the Case
     *
     * @param string $elementOriginUid     Unique id of Element Origin (unique id of Task)
     * @param string $elementDestUid       Unique id of Element Dest   (unique id of Task)
     * @param array  $arrayApplicationData Case data
     *
     * return void
     */
    public function emailEventBetweenElementOriginAndElementDest($elementOriginUid, $elementDestUid, array $arrayApplicationData)
    {
        try {
            //Verify if the Project is BPMN
            $bpmn = new \ProcessMaker\Project\Bpmn();
            if (!$bpmn->exists($arrayApplicationData["PRO_UID"])) {
                return;
            }

            //Element origin and dest
            $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();

            $arrayElement = array(
                "elementOrigin" => array("uid" => $elementOriginUid, "type" => "bpmnActivity"),
                "elementDest"   => array("uid" => $elementDestUid,   "type" => "bpmnActivity")
            );
            
            foreach ($arrayElement as $key => $value) {
                $arrayElementTaskRelationData = $elementTaskRelation->getElementTaskRelationWhere(
                    array(
                        \ElementTaskRelationPeer::PRJ_UID      => $arrayApplicationData["PRO_UID"],
                        \ElementTaskRelationPeer::ELEMENT_TYPE => "bpmnEvent",
                        \ElementTaskRelationPeer::TAS_UID      => $arrayElement[$key]["uid"]
                    ),
                    true
                );

                if (!is_null($arrayElementTaskRelationData)) {
                    $arrayElement[$key]["uid"]  = $arrayElementTaskRelationData["ELEMENT_UID"];
                    $arrayElement[$key]["type"] = "bpmnEvent";
                }
            }

            $elementOriginUid  = $arrayElement["elementOrigin"]["uid"];
            $elementOriginType = $arrayElement["elementOrigin"]["type"];
            $elementDestUid    = $arrayElement["elementDest"]["uid"];
            $elementDestType   = $arrayElement["elementDest"]["type"];

            //Get Message-Events of throw type
            $arrayEvent = $bpmn->getEmailEventTypeBetweenElementOriginAndElementDest(
                $elementOriginUid,
                $elementOriginType,
                $elementDestUid,
                $elementDestType
            );

            //Email-event
            foreach ($arrayEvent as $value) {
                $result = $this->sendEmail($arrayApplicationData["APP_UID"], $arrayApplicationData["PRO_UID"], $value[0], $arrayApplicationData);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Email-event do function
     *
     * @param string $appUID               Unique id of application
     * @param string $prj_uid              Unique id of Project
     * @param string $eventUid             Unique id of event
     * @param array  $arrayApplicationData Case data
     *
     * return void
     */
    public function sendEmail($appUID, $prj_uid, $eventUid, $arrayApplicationData) 
    {
        if (!$this->existsEvent($prj_uid, $eventUid)) {
            throw new \Exception(\G::LoadTranslation("ID_EMAIL_EVENT_DEFINITION_DOES_NOT_EXIST"));
        }
        $arrayData = $this->existsEvent($prj_uid, $eventUid);
        if(sizeof($arrayData)) {
            $emailGroupTo = array();
            $emailTo = "";
            $prfUid = $arrayData[6];
            $filesManager = new \ProcessMaker\BusinessModel\FilesManager();
            $contentFile = $filesManager->getProcessFileManager($prj_uid, $prfUid);
            if(strpos($arrayData[4],",")) {
                $emailsArray = explode(",",$arrayData[4]);
                foreach($emailsArray as $email) {
                    if(substr($email,0,1) == "@") {
                        $email = substr($email, 2,strlen($email));
                        if(isset($arrayApplicationData['APP_DATA'])) {
                            if(is_array($arrayApplicationData['APP_DATA']) && isset( $arrayApplicationData['APP_DATA'][$email])) {
                                $emailGroupTo[] = $arrayApplicationData['APP_DATA'][$email];
                            }   
                        } 
                    } else {
                        $emailGroupTo[] = $email;
                    }
                }
                $emailTo = implode(",",array_unique(array_filter($emailGroupTo)));
            } else {
                $email = $arrayData[4];
                if(substr($email,0,1) == "@") {
                    $email = substr($email, 2,strlen($email));
                    if(isset($arrayApplicationData['APP_DATA'])) {
                        if(is_array($arrayApplicationData['APP_DATA']) && isset( $arrayApplicationData['APP_DATA'][$email])) {
                            $emailTo = $arrayApplicationData['APP_DATA'][$email];
                        }  
                    }  
                } else {
                    $emailTo = $email;
                }   
            }
            if(!empty($emailTo)) {
                \PMFSendMessage($appUID, $arrayData[3], $emailTo, '', '', $arrayData[5], $contentFile['prf_filename'], array());
            }
        }
    }
    
    /**
     * Update process file Uid
     *
     * @param string $oldUid                         Unique id of old process file
     * @param string $newUid                         Unique id of new process file
     * @param string $projectUid                     Unique id of Project
     *
     * return bool Return array if exists, false otherwise
     */
    public function updatePrfUid($oldUid, $newUid, $projectUid) {
        try {
            $newValues = array();
            $rowData = $this->verifyIfEmailEventExistsByPrfUid($oldUid, $projectUid);    
            if(is_array($rowData)) {
                $newValues['PRF_UID'] = $newUid;
                $this->update($rowData['EMAIL_EVENT_UID'], $newValues);
                
            }
        } catch (\Exception $e) {
            throw $e;
        } 
    }
    
    /**
     * Verify if exists the Email Event of
     *
     * @param string $oldUid                         Unique id of old process file
     * @param string $projectUid                     Unique id of Project
     *
     * return bool Return array if exists, false otherwise
     */
    public function verifyIfEmailEventExistsByPrfUid($oldUid, $projectUid)
    {
        try {
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\EmailEventPeer::PRF_UID, $oldUid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if(is_array($row)) {
                $row = array_change_key_case($row, CASE_UPPER);
            }
            return (sizeof($row))? $row : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

