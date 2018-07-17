<?php


/**
 * Class InputDocumentDrive
 */

/**
 * Class InputDocumentDrive
 */
class AppDocumentDrive
{
    /**
     * @var PmDrive $drive
     */
    private $drive;
    /**
     * @var Application $app
     */
    private $app;

    /**
     * @var Users $user
     */
    private $user;

    private $statusDrive;
    private $usersEmail = '';

    /**
     * InputDocumentDrive constructor.
     */
    public function __construct()
    {
        $this->drive = new PmDrive();
        $status = $this->drive->getServiceDriveStatus();
        $status = !empty($status) ? ($status == 1 ? true : false): false;
        $this->usersEmail = '';
        $this->setStatusDrive($status);
    }

    /**
     * @return boolean
     */
    public function getStatusDrive()
    {
        return $this->statusDrive;
    }

    /**
     * @param boolen $statusDrive
     */
    public function setStatusDrive($statusDrive)
    {
        $this->statusDrive = $statusDrive;
    }

    public function loadUser ($usrUid)
    {
        $this->user = new Users();
        $this->user->load($usrUid);
        $this->drive->setDriveUser($this->user->getUsrEmail());
    }

    public  function loadApplication ($appUid)
    {
        $this->app = new Application();
        $this->app->Load($appUid);
    }

    public function existAppFolderDrive ()
    {
        try {
            if ($this->app->getAppDriveFolderUid() == null) {
                $process = new Process();
                $process->setProUid($this->app->getProUid());

                //Set name folder, for cron process.
                $result = $this->drive->createFolder(
                    'Case #' . $this->app->getAppNumber(),
                    $this->drive->getFolderIdPMDrive($this->user->getUsrUid())
                );
                $this->app->setAppDriveFolderUid($result->id);
                $this->app->update($this->app->toArray(BasePeer::TYPE_FIELDNAME));
            }
        } catch (Exception $e) {
            error_log('Error create folder Drive: ' . $e->getMessage());
        }
    }

    public function permission ($appUid, $folderUid, $fileIdDrive)
    {
        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn(ApplicationPeer::PRO_UID);
        $criteria->addSelectColumn(TaskUserPeer::TAS_UID);
        $criteria->addSelectColumn(TaskUserPeer::USR_UID);
        $criteria->addSelectColumn(TaskUserPeer::TU_RELATION);

        $criteria->add(ApplicationPeer::APP_UID, $appUid);
        $criteria->addJoin(ApplicationPeer::PRO_UID, TaskPeer::PRO_UID, Criteria::LEFT_JOIN);
        $criteria->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);

        $rs = ApplicationPeer::doSelectRS($criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $userPermission = array();
        $user = new Users();

        while ($rs->next()) {
            $row = $rs->getRow();
            if ($row['TU_RELATION'] == 1) {
                //users
                $dataUser = $user->load($row['USR_UID']);
                if (array_search($dataUser['USR_EMAIL'], $userPermission) === false) {
                    $objectPermissions = $this->getAllObjects($row['PRO_UID'], $appUid, $row['TAS_UID'],
                        $row['USR_UID']);
                    $userPermission[] = $dataUser['USR_EMAIL'];
                }
            } else {
                //Groups
                $criteria = new Criteria('workflow');
                $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
                $criteria->addSelectColumn(UsersPeer::USR_UID);
                $criteria->add(GroupUserPeer::GRP_UID, $row['USR_UID']);
                $criteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

                $rsGroup = AppDelegationPeer::doSelectRS($criteria);
                $rsGroup->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($rsGroup->next()) {
                    $aRow = $rsGroup->getRow();
                    if (array_search($aRow['USR_EMAIL'], $userPermission) === false) {
                        $objectPermissions = $this->getAllObjects($row['PRO_UID'], $appUid,
                            $row['TAS_UID'], $aRow['USR_UID']);
                        $userPermission[] = $aRow['USR_EMAIL'];
                    }
                }
            }
        }
        $userPermission = array_unique($userPermission);

        foreach ($userPermission as $key => $val) {
            $this->drive->setPermission($folderUid, $val, 'user', 'writer');
            $this->drive->setPermission($fileIdDrive, $val);
        }
    }

    public function addUserEmail ($email)
    {
        if (empty($email)) {
            return;
        }
        if ($this->usersEmail == '') {
            $this->usersEmail = $email;
        } else {
            $emails = explode('|', $this->usersEmail);
            if (array_search($email, $emails) === false) {
                $this->usersEmail .= '|' . $email;
            }
        }
    }
    /**
     * Get email of task users to app_uid
     * @param $appUid id application
     *
     * @throws \Exception
     */
    public function getEmailUsersTask($appUid)
    {
        try {
            $criteria = new Criteria('workflow');
            $criteria->add(AppDelegationPeer::APP_UID, $appUid);
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');

            $rsAppDelegation = AppDelegationPeer::doSelectRS($criteria);
            $rsAppDelegation->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $group = new Groups();
            $user = new Users();
            $data = [];
            while ($rsAppDelegation->next()) {
                $row = $rsAppDelegation->getRow();
                if (!empty($row['USR_UID'])) {
                    if ($user->userExists($row['USR_UID'])) {
                        $data = [];
                        $data[] = $user->load($row['USR_UID']);
                    } else {
                        $data = $group->getUsersOfGroup($row['USR_UID']);
                    }

                    foreach ($data as $dataUser) {
                        $this->addUserEmail($dataUser["USR_EMAIL"]);
                    }
                }
            }
        } catch (Exception $exception) {
            error_log('Error: ' . $exception);
        }
    }

    /**
     * @param array $appDocument
     * @param string $typeDocument type document INPUT, OUTPUT_DOC, OUTPUT_PDF, ATTACHED
     * @param string $mime MIME type of the file to insert.
     * @param string $src location of the file to insert.
     * @param string $name Title of the file to insert, including the extension.
     * return string uid
     */
    public function upload ($appDocument, $typeDocument, $mime, $src, $name)
    {
        try
        {
            $idFileDrive = null;
            $this->existAppFolderDrive();
            $appDoc = new AppDocument();
            $result = $this->drive->uploadFile(
                $mime,
                $src,
                $name,
                $this->app->getAppDriveFolderUid()
            );
            if ($result->id !== null) {
                $idFileDrive = $result->id;
                $appDoc->setDriveDownload($typeDocument, $result->id);
                $appDoc->update($appDocument);
            }
            return $idFileDrive;
        } catch (Exception $e) {
            error_log('Error upload file drive: ' . $e->getMessage());
        }
    }

    /**
     * Download file drive
     * @param $uidFileDrive
     */
    public function download ($uidFileDrive)
    {
        try
        {
            $result = $this->drive->downloadFile($uidFileDrive);

        } catch (Exception $e) {
            error_log('Error Download file drive: ' . $e->getMessage());
        }
        return $result;
    }


    /**
     * @param array $data
     * @param string $typeDoc value INPUT, OUTPUT_DOC, OUTPUT_PDF, ATTACHED
     *
     * @return string url drive
     */
    public function changeUrlDrive ($data, $typeDoc)
    {
        try
        {

            $urlDrive = $data['APP_DOC_DRIVE_DOWNLOAD'];
            if ($this->getStatusDrive()) {
                $driveDownload = @unserialize($data['APP_DOC_DRIVE_DOWNLOAD']);
                $urlDrive = $driveDownload !== false
                && is_array($driveDownload)
                && array_key_exists($typeDoc, $driveDownload) ?
                    $driveDownload[$typeDoc] : $urlDrive;
            }

        } catch (Exception $e) {
            error_log('Error change url drive: ' . $e->getMessage());
        }

        return $urlDrive;
    }

    /**
     * Synchronize documents drive
     *
     * @param boolean $log enable print cron
     */
    public function synchronizeDrive ($log)
    {
        if (!$this->statusDrive) {
            error_log("It has not enabled Feature Gmail");
            return;
        }
        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
        $criteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
        $criteria->add(
            $criteria->getNewCriterion(AppDocumentPeer::SYNC_WITH_DRIVE, 'UNSYNCHRONIZED', Criteria::EQUAL)->
            addOr($criteria->getNewCriterion(AppDocumentPeer::SYNC_PERMISSIONS, null, Criteria::NOT_EQUAL))
        );
        $criteria->add(AppDocumentPeer::APP_DOC_STATUS, 'ACTIVE');

        $criteria->addAscendingOrderByColumn('APP_DOC_CREATE_DATE');
        $criteria->addAscendingOrderByColumn('APP_UID');
        $rs = AppDocumentPeer::doSelectRS($criteria);
        $rs->setFetchMode(ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $row = $rs->getRow();
            $appDoc = new AppDocument();
            $fields = $appDoc->load($row['APP_DOC_UID'], $row['DOC_VERSION']);

            $appDocUid = $appDoc->getAppDocUid();
            $docVersion = $appDoc->getDocVersion();
            $filename = pathinfo($appDoc->getAppDocFilename());
            $name = !empty($filename['basename']) ? $filename['basename'] : '';
            $ext = !empty($filename['extension']) ? $filename['extension'] : '';
            $appUid = G::getPathFromUID($appDoc->getAppUid());
            $file = G::getPathFromFileUID($appDoc->getAppUid(), $appDocUid);


            $sw_file_exists_doc = false;
            $sw_file_exists_pdf = false;
            $sw_file_exists = false;
            $realPath = '';
            if ($appDoc->getAppDocType() === 'OUTPUT') {
                $realPathDoc = PATH_DOCUMENT . $appUid . '/outdocs/' . $appDocUid . '_' . $docVersion . '.' . 'doc';
                $realPathDoc1 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '_' . $docVersion . '.' . 'doc';
                $realPathDoc2 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '.' . 'doc';

                if (file_exists($realPathDoc)) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                } elseif (file_exists($realPathDoc1)) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                    $realPathDoc = $realPathDoc1;
                } elseif (file_exists($realPathDoc2)) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                    $realPathDoc = $realPathDoc2;
                }

                $realPathPdf = PATH_DOCUMENT . $appUid . '/outdocs/' . $appDocUid . '_' . $docVersion . '.' . 'pdf';
                $realPathPdf1 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '_' . $docVersion . '.' . 'pdf';
                $realPathPdf2 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '.' . 'pdf';

                if (file_exists($realPathPdf)) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                } elseif (file_exists($realPathPdf1)) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                    $realPathPdf = $realPathPdf1;
                } elseif (file_exists($realPathPdf2)) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                    $realPathPdf = $realPathPdf2;
                }
            } else {
                $realPath = PATH_DOCUMENT . $appUid . '/' . $file[0] . $file[1] . '_' . $docVersion . '.' . $ext;
                $realPath1 = PATH_DOCUMENT . $appUid . '/' . $file[0] . $file[1] . '.' . $ext;
                if (file_exists($realPath)) {
                    $sw_file_exists = true;
                } elseif (file_exists($realPath1)) {
                    $sw_file_exists = true;
                    $realPath = $realPath1;
                }
            }
            if ($sw_file_exists) {

                $this->loadApplication($appDoc->getAppUid());
                $this->loadUser($fields['USR_UID']);

                $emails = $appDoc->getSyncPermissions();
                $emails = !empty($emails) ? explode('|', $emails) : array();
                $result = null;
                foreach ($emails as $index => $email) {
                    if (!empty($email)) {

                        if ($index == 0 && $fields['SYNC_WITH_DRIVE'] == 'UNSYNCHRONIZED') {
                            if ($log) {
                                eprintln('upload file:' . $name, 'green');
                            }
                            $this->drive->setDriveUser($email);

                            if ($appDoc->getAppDocType() == 'OUTPUT') {

                                if ($sw_file_exists_doc) {
                                    $nameDoc = !empty($name)? $name : array_pop(explode('/', $realPathDoc));
                                    $result = $this->upload($fields, 'OUTPUT_DOC', 'application/msword', $realPathDoc,
                                        $nameDoc);
                                }
                                if ($sw_file_exists_pdf) {
                                    $namePdf = !empty($name)? $name : array_pop(explode('/', $realPathPdf));
                                    $info = finfo_open(FILEINFO_MIME_TYPE);
                                    $result = $this->upload($fields, 'OUTPUT_PDF', finfo_file($info, $realPathPdf),
                                        $realPathPdf, $namePdf);
                                }
                            } else {
                                $info = finfo_open(FILEINFO_MIME_TYPE);
                                $mime = finfo_file($info, $realPath);
                                $type = $appDoc->getAppDocType();
                                $result = $this->upload($fields, $type, $mime, $realPath, $name);
                            }

                        } else {
                            $result = $this->drive->setDriveUser($this->user->getUsrEmail());
                        }
                        if ($log) {
                            eprintln('Set Permission:' . $email, 'green');
                        }
                        $result = $this->drive->setPermission($this->app->getAppDriveFolderUid(), $email, 'user', 'writer');
                        $fields['SYNC_PERMISSIONS'] = null;
                    }
                }
                if ($result != null) {
                    $fields['SYNC_WITH_DRIVE'] = 'SYNCHRONIZED';
                    $fields['SYNC_PERMISSIONS'] = null;
                }
            } else {
                $fields['SYNC_WITH_DRIVE'] = 'NO_EXIST_FILE_PM';
                if ($log) {
                    eprintln('File no exists:' . $name, 'red');
                }
            }
            $appDoc->update($fields);
        }
    }

    /**
     * Add users to documents drive to give permissions
     *
     * @param      $appUid Id application
     * @param      $emails array with emails which can be null
     *
     * @throws \Exception
     */
    public function addUsersDocumentDrive ($appUid, $emails=null)
    {
        if (is_array($emails)) {
            foreach ($emails as $index => $email) {
                $this->addUserEmail($email);
            }
        } else {
            $this->getEmailUsersTask($appUid);
        }

        $criteria = new Criteria( 'workflow' );
        $criteria->add( AppDocumentPeer::APP_UID, $appUid );
        $criteria->addAscendingOrderByColumn( 'DOC_VERSION' );
        $rs = AppDocumentPeer::doSelectRS( $criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $appDoc = new AppDocument();
        while ($rs->next()) {
            $row = $rs->getRow();
            if (empty($row['SYNC_PERMISSIONS'])) {
                $row['SYNC_PERMISSIONS'] =  $this->usersEmail;
            } else {
                $emails = explode('|', $row['SYNC_PERMISSIONS']);
                foreach ($emails as $email) {
                    $this->addUserEmail($email);
                }
            }
            $appDoc->update($row);
        }
    }
}