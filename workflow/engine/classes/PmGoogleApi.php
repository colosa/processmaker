<?php

/**
 * class.pmGoogleApi.php
 *
 */

require_once PATH_TRUNK . 'vendor' . PATH_SEP . 'google' . PATH_SEP . 'apiclient' . PATH_SEP . 'src' . PATH_SEP . 'Google' . PATH_SEP . 'autoload.php';

class PmGoogleApi
{
    const DRIVE = 'https://www.googleapis.com/auth/drive';
    const DRIVE_FILE = 'https://www.googleapis.com/auth/drive.file';
    const DRIVE_APPS_READONLY = 'https://www.googleapis.com/auth/drive.apps.readonly';
    const DRIVE_READONLY = 'https://www.googleapis.com/auth/drive.readonly';
    const DRIVE_METADATA = 'https://www.googleapis.com/auth/drive.metadata';
    const DRIVE_METADATA_READONLY = 'https://www.googleapis.com/auth/drive.metadata.readonly';
    const DRIVE_APPDATA = 'https://www.googleapis.com/auth/drive.appdata';
    const DRIVE_PHOTOS_READONLY = 'https://www.googleapis.com/auth/drive.photos.readonly';
    const GMAIL_MODIFY = 'https://www.googleapis.com/auth/gmail.modify';

    private $scope = array();
    private $serviceAccountEmail;
    private $serviceAccountCertificate;
    private $user;
    private $serviceGmailStatus = false;
    private $serviceDriveStatus = false;
    private $configuration;

    public function __construct()
    {
        $licensedFeatures = &PMLicensedFeatures::getSingleton();
        if (!($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09') || $licensedFeatures->verifyfeature('AhKNjBEVXZlWUFpWE8wVTREQ0FObmo0aTdhVzhvalFic1M='))) {
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        }
        $this->loadSettings();
    }

    public function setScope($scope)
    {
        $this->scope[] = $scope;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getConfigGmail()
    {
        $this->configuration = new Configurations();
        $this->configuration->loadConfig($gmail, 'GOOGLE_API_SETTINGS', '');
    }

    public function setConfigGmail ($id, $value)
    {
        $this->configuration->aConfig[$id] = $value;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');
    }

    public function setServiceAccountEmail($serviceAccountEmail)
    {
        $this->setConfigGmail('serviceAccountEmail', $serviceAccountEmail);
        $this->serviceAccountEmail = $serviceAccountEmail;
    }

    public function getServiceAccountEmail()
    {
        return $this->serviceAccountEmail;
    }

    public function setServiceAccountCertificate ($serviceAccountCertificate)
    {
        $this->setConfigGmail('serviceAccountCertificate', $serviceAccountCertificate);
        $this->serviceAccountCertificate = $serviceAccountCertificate;
    }

    public function getServiceAccountCertificate()
    {
        return $this->serviceAccountCertificate;
    }

    public function setServiceGmailStatus($status)
    {
        $licensedFeatures = &PMLicensedFeatures::getSingleton();
        if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
            $status = false;
        }
        $this->setConfigGmail('serviceGmailStatus', $status);
        $this->serviceGmailStatus = $status;
    }

    public function getServiceGmailStatus()
    {
        return $this->serviceGmailStatus;
    }

    public function setServiceDriveStatus($status)
    {
        $licensedFeatures = &PMLicensedFeatures::getSingleton();
        if (!$licensedFeatures->verifyfeature('AhKNjBEVXZlWUFpWE8wVTREQ0FObmo0aTdhVzhvalFic1M=')) {
            $status = false;
        }
        $this->setConfigGmail('serviceDriveStatus', $status);
        $this->serviceDriveStatus = $status;
    }

    public function getServiceDriveStatus()
    {
        return $this->serviceDriveStatus;
    }

    /**
     * load configuration gmail service account
     *
     */
    public function loadSettings()
    {
        $this->getConfigGmail();

        $serviceAccountCertificate = empty($this->configuration->aConfig['serviceAccountCertificate']) ? '' : $this->configuration->aConfig['serviceAccountCertificate'];
        $serviceAccountEmail = empty($this->configuration->aConfig['serviceAccountEmail']) ? '' : $this->configuration->aConfig['serviceAccountEmail'];
        $serviceGmailStatus = empty($this->configuration->aConfig['serviceGmailStatus']) ? false : $this->configuration->aConfig['serviceGmailStatus'];
        $serviceDriveStatus = empty($this->configuration->aConfig['serviceDriveStatus']) ? false : $this->configuration->aConfig['serviceDriveStatus'];

        $this->scope = array();

        $this->serviceAccountEmail = $serviceAccountEmail;
        $this->serviceAccountCertificate = $serviceAccountCertificate;
        $this->serviceGmailStatus = $serviceGmailStatus;
        $this->serviceDriveStatus = $serviceDriveStatus;
    }

    /**
     * New service client - Authentication google Api
     *
     * @return Google_Service_Client $service API service instance.
     */
    public function serviceClient()
    {
        $client = null;
        if (file_exists(PATH_DATA_SITE . $this->serviceAccountCertificate)) {
            $key = file_get_contents(PATH_DATA_SITE . $this->serviceAccountCertificate);
        } else {
            throw new Exception(G::LoadTranslation('ID_GOOGLE_CERTIFICATE_ERROR'));
        }

        $data = json_decode($key);
        $assertionCredentials = new Google_Auth_AssertionCredentials(
            $this->serviceAccountEmail,
            $this->scope,
            $data->private_key
        );

        $assertionCredentials->sub = $this->user;

        $client = new Google_Client();
        $client->setApplicationName("PMDrive");
        $client->setAssertionCredentials($assertionCredentials);


        return $client;
    }

    /**
     * New service client - Authentication google Api
     *
     * @param $credentials
     * @throws \Exception
     * @return \StdClass response.
     */
    public function testService($credentials)
    {

        $scope = array(
            static::DRIVE,
            static::DRIVE_FILE,
            static::DRIVE_READONLY,
            static::DRIVE_METADATA,
            static::DRIVE_METADATA_READONLY,
            static::DRIVE_APPDATA,
            static::DRIVE_PHOTOS_READONLY
        );

        if (file_exists($credentials->pathServiceAccountCertificate)) {
            $key = file_get_contents($credentials->pathServiceAccountCertificate);
        } else {
            throw new Exception(G::LoadTranslation('ID_GOOGLE_CERTIFICATE_ERROR'));
        }
        $data = json_decode($key);
        $assertionCredentials = new Google_Auth_AssertionCredentials(
            $credentials->emailServiceAccount,
            $scope,
            $data->private_key
        );
        $assertionCredentials->sub = $this->user;

        $client = new Google_Client();
        $client->setApplicationName("PMDrive");
        $client->setAssertionCredentials($assertionCredentials);

        $service = new Google_Service_Drive($client);

        $result = new StdClass();
        $result->success = true;

        $result->currentUserName = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->rootFolderId = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaType = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaBytesTotal = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaBytesUsed = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');

        try {
            $about = $service->about->get();

            $result->currentUserName = $about->getName();
            $result->rootFolderId = $about->getRootFolderId();
            $result->quotaType = $about->getQuotaType();
            $result->quotaBytesTotal = $about->getQuotaBytesTotal();
            $result->quotaBytesUsed = $about->getQuotaBytesUsed();
            $result->responseGmailTest = G::LoadTranslation('ID_SUCCESSFUL_CONNECTION');
        } catch (Exception $e) {
            $result->success = false;
            $result->responseGmailTest = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        }

        return $result;
    }
}
