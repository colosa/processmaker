<?php

/**
 * class.pmDrive.php
 *
 * @package workflow.engine.class
 *
 */
class PmDrive extends PmGoogleApi
{
    private $folderIdPMDrive = '';
    private $folderNamePMDrive;

    /**
     * Validate if exist folder PMDrive
     *
     * @param $userUid id user
     */
    private function validateFolderPMDrive($usrUid)
    {
        if ($this->folderIdPMDrive != '') {
            return;
        }

        $user = new Users();
        $dataUser = $user->load($usrUid);

        if (!empty($dataUser['USR_EMAIL'])) {
            $this->setDriveUser($dataUser['USR_EMAIL']);
        }
        $this->folderIdPMDrive = empty($dataUser['USR_PMDRIVE_FOLDER_UID']) ? '' : $dataUser['USR_PMDRIVE_FOLDER_UID'];

        $conf = $this->getConfigGmail();
        $this->folderNamePMDrive = empty($conf->aConfig['folderNamePMDrive']) ? 'PMDrive (' . SYS_SYS . ')' : $conf->aConfig['folderNamePMDrive'];

        if ($this->folderIdPMDrive == '') {
            $folderid = $this->createFolder($this->folderNamePMDrive);

            $this->folderIdPMDrive = $folderid->id;
            $dataUser['USR_PMDRIVE_FOLDER_UID'] = $folderid->id;
            $user->update($dataUser);
        }
    }

    public function getFolderIdPMDrive($usrUid)
    {
        $this->validateFolderPMDrive($usrUid);
        return $this->folderIdPMDrive;
    }

    /**
     * Set account user
     *
     * @param $user email user
     */
    public function setFolderNamePMDrive($name)
    {
        $conf = $this->getConfigGmail();
        $conf->aConfig['folderNamePMDrive'] = $name;
        $conf->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->folderNamePMDrive = $name;
    }

    /**
     * Set account user
     *
     * @param $user email user
     */
    public function setDriveUser($user)
    {
        $this->setUser($user);
    }

    /**
     * Instance google service Drive
     *
     * @return Google_Service_Drive $service Drive API service instance.
     */
    private function serviceDrive()
    {
        $client = $this->serviceClient();
        $service = new Google_Service_Drive($client);
        return $service;
    }

    /**
     * Retrieve a list of File resources.
     *
     * @param string $fileId uid file
     * @return Array List of Google_Service_Drive_DriveFile resources.
     */
    public function listFolder($fileId)
    {
        $this->setScope(static::DRIVE);
        $this->setScope(static::DRIVE_FILE);
        $this->setScope(static::DRIVE_READONLY);
        $this->setScope(static::DRIVE_METADATA);
        $this->setScope(static::DRIVE_METADATA_READONLY);
        $this->setScope(static::DRIVE_APPDATA);
        
        $service = $this->serviceDrive();

        try {
            $rows = array();
            $parameters['q'] = "'" . $fileId . "' in parents and trashed = false";
            $parents = $service->files->listFiles($parameters);

            foreach ($parents->getItems() as $parent) {
                $rows = $parent;
            }

        } catch (Exception $e) {
            error_log( G::LoadTranslation("ID_MSG_AJAX_FAILURE") . $e->getMessage());
        }
        return $rows;
    }

    /**
     * Retrieve a list of File resources.
     *
     * @param string $name Title of the file to insert, including the extension.
     * @param string $parentId Parent folder's ID.
     * @return Google_Service_Drive_DriveFile The file that was inserted. NULL is returned if an API error occurred.
     */
    public function createFolder($name, $parentId = null)
    {
        $this->setScope(static::DRIVE_FILE);

        $service = $this->serviceDrive();

        $file = new Google_Service_Drive_DriveFile();
        $file->setMimeType("application/vnd.google-apps.folder");
        $file->setTitle($name);

        if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }

        try {
            $createdFolder = $service->files->insert($file);
        } catch (Exception $e) {
            $createdFolder = null;
            error_log ( G::LoadTranslation("ID_MSG_AJAX_FAILURE") . $e->getMessage());
        }
        return $createdFolder;
    }

    /**
     * upload new file
     *
     * @param string $mime MIME type of the file to insert.
     * @param string $src location of the file to insert.
     * @param string $name Title of the file to insert, including the extension.
     * @return Google_Service_Drive_DriveFile The file that was inserted. NULL is returned if an API error occurred.
     */
    public function uploadFile($mime, $src, $name, $parentId = null)
    {
        $this->setScope(static::DRIVE_FILE);

        $service = $this->serviceDrive();

        $file = new Google_Service_Drive_DriveFile();
        $file->setMimeType($mime);
        $file->setTitle($name);

        // Set the parent folder.
        if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }

        $data = file_get_contents($src);

        try {
            $createdFile = $service->files->insert(
                $file,
                array(
                    'data' => $data,
                    'mimeType' => $mime,
                    'uploadType' => 'media'
                )
            );

        } catch (Exception $e) {
            error_log( G::LoadTranslation("ID_MSG_AJAX_FAILURE") . $e->getMessage());
        }
        return $createdFile;
    }

    /**
     * Download a file's content.
     *
     * @param string $fileId id file.
     * @return String The file's content if successful, null otherwise
     */
    public function downloadFile($fileId)
    {
        $this->setScope(static::DRIVE);
        $this->setScope(static::DRIVE_APPDATA);
        $this->setScope(static::DRIVE_APPS_READONLY);
        $this->setScope(static::DRIVE_FILE);
        $this->setScope(static::DRIVE_METADATA);
        $this->setScope(static::DRIVE_METADATA_READONLY);
        $this->setScope(static::DRIVE_READONLY);
        $service = $this->serviceDrive();
        $response = null;

        try {
            $file = $service->files->get($fileId);
            $downloadUrl = $file->getDownloadUrl();
            if ($downloadUrl) {
                $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
                $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
                if ($httpRequest->getResponseHttpCode() == 200) {
                    $response = $httpRequest->getResponseBody();
                } else {
                    error_log(G::LoadTranslation("ID_MSG_AJAX_FAILURE"));
                }
            } else {
                error_log(G::LoadTranslation("ID_PMDRIVE_NO_CONTENT_IN_FILE"));
            }
        } catch (Exception $e) {
            error_log( G::LoadTranslation("ID_MSG_AJAX_FAILURE") . $e->getMessage());
        }
        return $response;
    }

    /**
     * Insert a new permission.
     *
     * @param String $fileId ID of the file to insert permission for.
     * @param String $value User or group e-mail address, domain name or NULL for "default" type.
     * @param String $type The value "user", "group", "domain" or "default".
     * @param String $role The value "owner", "writer" or "reader".
     * @return Google_Servie_Drive_Permission The inserted permission. NULL is returned if an API error occurred.
     */
    public function setPermission($fileId, $value, $type = 'user', $role = 'reader', $sendNotification = false)
    {
        $this->setScope(static::DRIVE);
        $this->setScope(static::DRIVE_FILE);

        $service = $this->serviceDrive();
        $permission = null;

        $newPermission = new Google_Service_Drive_Permission();
        $newPermission->setValue($value);
        $newPermission->setType($type);
        $newPermission->setRole($role);

        try {
            $permission = $service->permissions->insert(
                $fileId,
                $newPermission,
                array(
                    'sendNotificationEmails' => $sendNotification
                )
            );

        } catch (Exception $e) {
            error_log(G::LoadTranslation("ID_MSG_AJAX_FAILURE") . $e->getMessage());
        }
        return $permission;
    }
}
