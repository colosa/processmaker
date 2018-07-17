<?php
namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Plugins\PluginRegistry;
use AppDocument;
use AppDocumentPeer;
use Exception;
use Criteria;
use ResultSet;
use G;
use ObjectPermissionPeer;
use StepPeer;
use StepSupervisorPeer;
use AppDelegation;
use AppDelegationPeer;
use Users;
use Configurations;
use Bootstrap;
use WsBase;
use ApplicationPeer;
use ProcessMaker\BusinessModel\ProcessSupervisor;
use ProcessMaker\BusinessModel\Cases AS BusinessModelCases;
use Cases;
use ProcessUserPeer;
use AppFolder;


class InputDocument
{
    /**
     * Verify exists app_doc_uid in table APP_DOCUMENT
     *
     * @param string $appDocumentUid
     *
     * @return void Throw exception
     * @throws Exception
     */
    private function throwExceptionIfNotExistsAppDocument($appDocumentUid)
    {
        try {
            $appDocument = AppDocumentPeer::retrieveByPK($appDocumentUid, 1);

            if (is_null($appDocument)) {
                throw new Exception(G::LoadTranslation("ID_CASES_INPUT_DOES_NOT_EXIST", array($appDocumentUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if the user has permissions
     *
     * @param string $applicationUid   Unique id of Case
     * @param string $delIndex         Delegataion index
     * @param string $userUid          Unique id of User
     * @param string $appDocumentUid
     *
     * @return void Throw exception the user does not have permission to delete
     * @throws Exception
     */
    public function throwExceptionIfHaventPermissionToDelete($applicationUid, $delIndex, $userUid, $appDocumentUid)
    {
        try {
            //Verify data inbox
            $case = new BusinessModelCases();
            $arrayResult = $case->getStatusInfo($applicationUid, $delIndex, $userUid);

            $flagInbox = 1;

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                $flagInbox = 0;
            }

            //Verify data Supervisor
            $application = ApplicationPeer::retrieveByPK($applicationUid);

            $flagSupervisor = 0;

            $supervisor = new ProcessSupervisor();
            $processSupervisor= $supervisor->getProcessSupervisors($application->getProUid(), "ASSIGNED");

            $arraySupervisor = $processSupervisor["data"];

            foreach ($arraySupervisor as $value) {
                if($value["usr_uid"] == $userUid) {
                   $flagSupervisor = 1;
                   break;
                }
            }

            if ($flagInbox == 0 && $flagSupervisor == 0) {
                throw new Exception(G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_INPUT_DOCUMENT", array($userUid)));
            }

            //verfiry exists $appDocumentUid
            $this->throwExceptionIfNotExistsAppDocument($appDocumentUid);

            //Verify data permission
            $flagPermission = 0;

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppDocumentPeer::DOC_UID);

            $criteria->add(AppDocumentPeer::APP_DOC_UID, $appDocumentUid, Criteria::EQUAL);
            $criteria->add(AppDocumentPeer::APP_UID, $applicationUid, Criteria::EQUAL);
            $criteria->add(AppDocumentPeer::APP_DOC_TYPE, "INPUT", Criteria::EQUAL);

            $rsCriteria = AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $inputDocumentUid = $row["DOC_UID"];

                //Criteria
                $criteria2 = new Criteria("workflow");

                $criteria2->addSelectColumn(ObjectPermissionPeer::OP_UID);

                $criteria2->add(ObjectPermissionPeer::PRO_UID, $application->getProUid(), Criteria::EQUAL);
                $criteria2->add(ObjectPermissionPeer::OP_OBJ_TYPE, "INPUT", Criteria::EQUAL);
                $criteria2->add(
                    $criteria2->getNewCriterion(ObjectPermissionPeer::OP_OBJ_UID, $inputDocumentUid, Criteria::EQUAL)->addOr(
                    $criteria2->getNewCriterion(ObjectPermissionPeer::OP_OBJ_UID, "0", Criteria::EQUAL))->addOr(
                    $criteria2->getNewCriterion(ObjectPermissionPeer::OP_OBJ_UID, "", Criteria::EQUAL))
                );
                $criteria2->add(ObjectPermissionPeer::OP_ACTION, "DELETE", Criteria::EQUAL);

                //User
                $criteriaU = clone $criteria2;

                $criteriaU->add(ObjectPermissionPeer::OP_USER_RELATION, 1, Criteria::EQUAL);
                $criteriaU->add(ObjectPermissionPeer::USR_UID, $userUid, Criteria::EQUAL);

                $rsCriteriaU = ObjectPermissionPeer::doSelectRS($criteriaU);
                $rsCriteriaU->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                if ($rsCriteriaU->next()) {
                    $flagPermission = 1;
                }

                //Group
                if ($flagPermission == 0) {
                    $criteriaG = clone $criteria2;

                    $criteriaG->add(ObjectPermissionPeer::OP_USER_RELATION, 2, Criteria::EQUAL);

                    $criteriaG->addJoin(ObjectPermissionPeer::USR_UID, GroupUserPeer::GRP_UID, Criteria::LEFT_JOIN);
                    $criteriaG->add(GroupUserPeer::USR_UID, $userUid, Criteria::EQUAL);

                    $rsCriteriaG = ObjectPermissionPeer::doSelectRS($criteriaG);
                    $rsCriteriaG->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                    if ($rsCriteriaG->next()) {
                        $flagPermission = 1;
                    }
                }
            }

            if ($flagPermission == 0) {
                throw new Exception(G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_INPUT_DOCUMENT", array($userUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if not exists input Document in Steps
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegataion index
     * @param string $appDocumentUid
     *
     * @return void Throw exception if not exists input Document in Steps
     * @throws Exception
     */
    public function throwExceptionIfInputDocumentNotExistsInSteps($applicationUid, $delIndex, $appDocumentUid)
    {
        try {
            //Verify Case
            $appDelegation = AppDelegationPeer::retrieveByPK($applicationUid, $delIndex);

            if (is_null($appDelegation)) {
                throw new Exception(G::LoadTranslation("ID_CASE_DEL_INDEX_DOES_NOT_EXIST", array("app_uid", $applicationUid, "del_index", $delIndex)));
            }

            $taskUid = $appDelegation->getTasUid();

            //Verify Steps
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppDocumentPeer::DOC_UID);

            $criteria->add(AppDocumentPeer::APP_DOC_UID, $appDocumentUid, Criteria::EQUAL);
            $criteria->add(AppDocumentPeer::APP_UID, $applicationUid, Criteria::EQUAL);
            $criteria->add(AppDocumentPeer::APP_DOC_TYPE, "INPUT", Criteria::EQUAL);

            $rsCriteria = AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $inputDocumentUid = $row["DOC_UID"];

                $criteria = new Criteria("workflow");

                $criteria->addSelectColumn(StepPeer::STEP_UID);

                $criteria->add(StepPeer::TAS_UID, $taskUid, Criteria::EQUAL);
                $criteria->add(StepPeer::STEP_TYPE_OBJ, "INPUT_DOCUMENT", Criteria::EQUAL);
                $criteria->add(StepPeer::STEP_UID_OBJ, $inputDocumentUid, Criteria::EQUAL);

                $rsCriteria = StepPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                if (!$rsCriteria->next()) {
                    throw new Exception(G::LoadTranslation("ID_CASES_INPUT_DOCUMENT_DOES_NOT_EXIST", array($appDocumentUid)));
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for AppDocument
     *
     * @return object
     * @throws Exception
     */
    public function getAppDocumentCriteriaByData($applicationUid)
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
            $criteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
            $criteria->addSelectColumn(AppDocumentPeer::DOC_UID);
            $criteria->addSelectColumn(AppDocumentPeer::USR_UID);
            $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
            $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
            $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_INDEX);

            $sql = "
            SELECT MAX(APPDOC.DOC_VERSION)
            FROM   " . AppDocumentPeer::TABLE_NAME . " AS APPDOC
            WHERE  APPDOC.APP_DOC_UID = " . AppDocumentPeer::APP_DOC_UID . "
            ";

            $criteria->add(
                $criteria->getNewCriterion(AppDocumentPeer::APP_UID, $applicationUid, Criteria::EQUAL)->addAnd(
                $criteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, array("INPUT", "ATTACHED"), Criteria::IN))->addAnd(
                $criteria->getNewCriterion(AppDocumentPeer::APP_DOC_STATUS, array("ACTIVE"), Criteria::IN))->addAnd(
                $criteria->getNewCriterion(AppDocumentPeer::DOC_VERSION, AppDocumentPeer::DOC_VERSION . " IN ($sql)", Criteria::CUSTOM))
            );

            $criteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);

            //Return
            return $criteria;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a AppDocument from a record
     *
     * @param array $record Record
     *
     * @return array Return an array with data AppDocument
     * @throws Exception
     */
    public function getAppDocumentDataFromRecord(array $record)
    {
        try {
            $newArray = array();
            if (isset($record["APP_DOC_UID"])) {
                $newArray["app_doc_uid"] = $record["APP_DOC_UID"];
            }
            if (isset($record["APP_DOC_FILENAME"])) {
                $newArray["app_doc_filename"] = $record["APP_DOC_FILENAME"];
            }
            if (isset($record["DOC_UID"])) {
                $newArray["doc_uid"] = $record["DOC_UID"];
            }
            if (isset($record["DOC_VERSION"])) {
                $newArray["app_doc_version"] = $record["DOC_VERSION"];
            }
            if (isset($record["APP_DOC_CREATE_DATE"])) {
                $newArray["app_doc_create_date"] = $record["APP_DOC_CREATE_DATE"];
            }
            if (isset($record["APP_DOC_CREATE_USER"])) {
                $newArray["app_doc_create_user"] = $record["APP_DOC_CREATE_USER"];
            }
            if (isset($record["APP_DOC_TYPE"])) {
                $newArray["app_doc_type"] = $record["APP_DOC_TYPE"];
            }
            if (isset($record["APP_DOC_INDEX"])) {
                $newArray["app_doc_index"] = $record["APP_DOC_INDEX"];
            }
            if (isset($record["APP_DOC_LINK"])) {
                $newArray["app_doc_link"] = $record["APP_DOC_LINK"];
            }

            return $newArray;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if the user is Supervisor
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid        Unique id of User
     *
     * @return array Return data of input documents
     * @throws Exception
     */
    public function getCasesInputDocumentsBySupervisor($applicationUid, $userUid)
    {
        try {
            //Verify data Supervisor
            $application = ApplicationPeer::retrieveByPK($applicationUid);

            $flagSupervisor = 0;

            $supervisor = new ProcessSupervisor();
            $processSupervisor = $supervisor->getProcessSupervisors($application->getProUid(), "ASSIGNED");
            $arraySupervisor = $processSupervisor["data"];

            foreach ($arraySupervisor as $value) {
                if(!empty($value["usr_uid"]) && $value["usr_uid"] == $userUid) {
                   $flagSupervisor = 1;
                   break;
                }
            }

            $user = new Users();
            $appDocument = new AppDocument();
            $configuraction = new Configurations();

            $confEnvSetting = $configuraction->getFormats();

            $arrayInputDocument = array();

            //Query
            $criteria = $this->getAppDocumentCriteriaByData($applicationUid);

            $rsCriteria = AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayUserData = $user->load($row["USR_UID"]);

                $arrayAppDocument = $appDocument->load($row["APP_DOC_UID"], $row["DOC_VERSION"]);

                $row["APP_DOC_FILENAME"] = $arrayAppDocument["APP_DOC_FILENAME"];
                $row["APP_DOC_CREATE_USER"] = $configuraction->usersNameFormatBySetParameters($confEnvSetting["format"], $arrayUserData["USR_USERNAME"], $arrayUserData["USR_FIRSTNAME"], $arrayUserData["USR_LASTNAME"]);
                $row["APP_DOC_LINK"] = "cases/cases_ShowDocument?a=" . $row["APP_DOC_UID"] . "&v=" . $row["DOC_VERSION"];

                $arrayInputDocument[] = $this->getAppDocumentDataFromRecord($row);
            }

            if (!empty($arrayInputDocument) && $flagSupervisor == 0) {
                throw new Exception(G::LoadTranslation("ID_USER_IS_NOT_SUPERVISOR"));
            }

            //Return
            return $arrayInputDocument;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     *
     * @return array Return an array with data of an InputDocument
     * @throws Exception
     */
    public function getCasesInputDocuments($applicationUid, $userUid)
    {
        try {
            //Verify data inbox
            $case = new BusinessModelCases();
            $arrayResult = $case->getStatusInfo($applicationUid, 0, $userUid);

            $flagInbox = true;

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                $flagInbox = false;
            }

            $user = new Users();
            $appDocument = new AppDocument();
            $configuraction = new Configurations();

            $confEnvSetting = $configuraction->getFormats();

            $arrayInputDocument = array();

            //Query
            $criteria = $this->getAppDocumentCriteriaByData($applicationUid);

            if (!$flagInbox) {
                $criteria->add(AppDocumentPeer::USR_UID, $userUid, Criteria::EQUAL);
            }

            $rsCriteria = AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $sUser = '***';
                if ($row["USR_UID"] !== '-1') {
                    $arrayUserData = $user->load($row["USR_UID"]);
                    $sUser = $configuraction->usersNameFormatBySetParameters($confEnvSetting["format"], $arrayUserData["USR_USERNAME"], $arrayUserData["USR_FIRSTNAME"], $arrayUserData["USR_LASTNAME"]);
                }
                $arrayAppDocument = $appDocument->load($row["APP_DOC_UID"], $row["DOC_VERSION"]);


                $row["APP_DOC_FILENAME"] = $arrayAppDocument["APP_DOC_FILENAME"];
                $row["APP_DOC_CREATE_USER"] = $sUser;
                $row["APP_DOC_LINK"] = "cases/cases_ShowDocument?a=" . $row["APP_DOC_UID"] . "&v=" . $row["DOC_VERSION"];

                $arrayInputDocument[] = $this->getAppDocumentDataFromRecord($row);
            }

            //Return
            return $arrayInputDocument;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     * @param string $inputDocumentUid
     *
     * @return array Return an array with data of an InputDocument
     * @throws Exception
     */
    public function getCasesInputDocument($applicationUid, $userUid, $inputDocumentUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;

            $oCase = new Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new BusinessModelCases();
            $oCaseRest->getAllUploadedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );
            $result = array ();
            global $_DBArray;
            $flagInputDocument = false;

            foreach ($_DBArray['inputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['APP_DOC_FILENAME'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = $row['DOWNLOAD_LINK'];

                    if ($docrow["app_doc_uid"] == $inputDocumentUid) {
                        $flagInputDocument = true;

                        $appDocument = AppDocumentPeer::retrieveByPK($inputDocumentUid, $row["DOC_VERSION"]);

                        if (is_null($appDocument)) {
                            $flagInputDocument = false;
                        }

                        $result = $docrow;
                        break;
                    }
                }
            }

            if (!$flagInputDocument) {
                throw new Exception(G::LoadTranslation("ID_CASES_INPUT_DOES_NOT_EXIST", array($inputDocumentUid)));
            }

            $oResponse = json_decode(json_encode($result), false);
            return $oResponse;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Download InputDocument
     *
     * @param $app_uid
     * @param $app_doc_uid
     * @param $version
     * @throws Exception
     */
    public function downloadInputDocument($app_uid, $app_doc_uid, $version)
    {
        try {
            $oAppDocument = new AppDocument();
            if ($version == 0) {
                $docVersion = $oAppDocument->getLastAppDocVersion($app_doc_uid);
            } else {
                $docVersion = $version;
            }
            $oAppDocument->Fields = $oAppDocument->load($app_doc_uid, $docVersion);
            $sAppDocUid = $oAppDocument->getAppDocUid();
            $iDocVersion = $oAppDocument->getDocVersion();
            $info = pathinfo($oAppDocument->getAppDocFilename());

            $app_uid = G::getPathFromUID($oAppDocument->Fields['APP_UID']);
            $file = G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

            $ext = (isset($info['extension']) ? $info['extension'] : '');
            $realPath = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
            $realPath1 = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '.' . $ext;
            if (!file_exists($realPath) && file_exists($realPath1)) {
                $realPath = $realPath1;
            }
            $filename = $info['basename'];
            $mimeType = $this->mime_content_type($filename);
            header('HTTP/1.0 206');
            header('Pragma: public');
            header('Expires: -1');
            header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
            header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Length: " . filesize($realPath));
            header("Content-Type: $mimeType");
            header("Content-Description: File Transfer");

            if ($fp = fopen($realPath, 'rb')) {
                ob_end_clean();
                while (!feof($fp) and (connection_status() == 0)) {
                    print(fread($fp, 8192));
                    flush();
                }
                @fclose($fp);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function mime_content_type($filename) {
        $idx = explode( '.', $filename );
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode-1]);

        $mimet = array(
            'ai' =>'application/postscript',
            'aif' =>'audio/x-aiff',
            'aifc' =>'audio/x-aiff',
            'aiff' =>'audio/x-aiff',
            'asc' =>'text/plain',
            'atom' =>'application/atom+xml',
            'avi' =>'video/x-msvideo',
            'bcpio' =>'application/x-bcpio',
            'bmp' =>'image/bmp',
            'cdf' =>'application/x-netcdf',
            'cgm' =>'image/cgm',
            'cpio' =>'application/x-cpio',
            'cpt' =>'application/mac-compactpro',
            'crl' =>'application/x-pkcs7-crl',
            'crt' =>'application/x-x509-ca-cert',
            'csh' =>'application/x-csh',
            'css' =>'text/css',
            'dcr' =>'application/x-director',
            'dir' =>'application/x-director',
            'djv' =>'image/vnd.djvu',
            'djvu' =>'image/vnd.djvu',
            'doc' =>'application/msword',
            'dtd' =>'application/xml-dtd',
            'dvi' =>'application/x-dvi',
            'dxr' =>'application/x-director',
            'eps' =>'application/postscript',
            'etx' =>'text/x-setext',
            'ez' =>'application/andrew-inset',
            'gif' =>'image/gif',
            'gram' =>'application/srgs',
            'grxml' =>'application/srgs+xml',
            'gtar' =>'application/x-gtar',
            'hdf' =>'application/x-hdf',
            'hqx' =>'application/mac-binhex40',
            'html' =>'text/html',
            'html' =>'text/html',
            'ice' =>'x-conference/x-cooltalk',
            'ico' =>'image/x-icon',
            'ics' =>'text/calendar',
            'ief' =>'image/ief',
            'ifb' =>'text/calendar',
            'iges' =>'model/iges',
            'igs' =>'model/iges',
            'jpe' =>'image/jpeg',
            'jpeg' =>'image/jpeg',
            'jpg' =>'image/jpeg',
            'js' =>'application/x-javascript',
            'kar' =>'audio/midi',
            'latex' =>'application/x-latex',
            'm3u' =>'audio/x-mpegurl',
            'man' =>'application/x-troff-man',
            'mathml' =>'application/mathml+xml',
            'me' =>'application/x-troff-me',
            'mesh' =>'model/mesh',
            'mid' =>'audio/midi',
            'midi' =>'audio/midi',
            'mif' =>'application/vnd.mif',
            'mov' =>'video/quicktime',
            'movie' =>'video/x-sgi-movie',
            'mp2' =>'audio/mpeg',
            'mp3' =>'audio/mpeg',
            'mpe' =>'video/mpeg',
            'mpeg' =>'video/mpeg',
            'mpg' =>'video/mpeg',
            'mpga' =>'audio/mpeg',
            'ms' =>'application/x-troff-ms',
            'msh' =>'model/mesh',
            'mxu m4u' =>'video/vnd.mpegurl',
            'nc' =>'application/x-netcdf',
            'oda' =>'application/oda',
            'ogg' =>'application/ogg',
            'pbm' =>'image/x-portable-bitmap',
            'pdb' =>'chemical/x-pdb',
            'pdf' =>'application/pdf',
            'pgm' =>'image/x-portable-graymap',
            'pgn' =>'application/x-chess-pgn',
            'php' =>'application/x-httpd-php',
            'php4' =>'application/x-httpd-php',
            'php3' =>'application/x-httpd-php',
            'phtml' =>'application/x-httpd-php',
            'phps' =>'application/x-httpd-php-source',
            'png' =>'image/png',
            'pnm' =>'image/x-portable-anymap',
            'ppm' =>'image/x-portable-pixmap',
            'ppt' =>'application/vnd.ms-powerpoint',
            'ps' =>'application/postscript',
            'qt' =>'video/quicktime',
            'ra' =>'audio/x-pn-realaudio',
            'ram' =>'audio/x-pn-realaudio',
            'ras' =>'image/x-cmu-raster',
            'rdf' =>'application/rdf+xml',
            'rgb' =>'image/x-rgb',
            'rm' =>'application/vnd.rn-realmedia',
            'roff' =>'application/x-troff',
            'rtf' =>'text/rtf',
            'rtx' =>'text/richtext',
            'sgm' =>'text/sgml',
            'sgml' =>'text/sgml',
            'sh' =>'application/x-sh',
            'shar' =>'application/x-shar',
            'shtml' =>'text/html',
            'silo' =>'model/mesh',
            'sit' =>'application/x-stuffit',
            'skd' =>'application/x-koan',
            'skm' =>'application/x-koan',
            'skp' =>'application/x-koan',
            'skt' =>'application/x-koan',
            'smi' =>'application/smil',
            'smil' =>'application/smil',
            'snd' =>'audio/basic',
            'spl' =>'application/x-futuresplash',
            'src' =>'application/x-wais-source',
            'sv4cpio' =>'application/x-sv4cpio',
            'sv4crc' =>'application/x-sv4crc',
            'svg' =>'image/svg+xml',
            'swf' =>'application/x-shockwave-flash',
            't' =>'application/x-troff',
            'tar' =>'application/x-tar',
            'tcl' =>'application/x-tcl',
            'tex' =>'application/x-tex',
            'texi' =>'application/x-texinfo',
            'texinfo' =>'application/x-texinfo',
            'tgz' =>'application/x-tar',
            'tif' =>'image/tiff',
            'tiff' =>'image/tiff',
            'tr' =>'application/x-troff',
            'tsv' =>'text/tab-separated-values',
            'txt' =>'text/plain',
            'ustar' =>'application/x-ustar',
            'vcd' =>'application/x-cdlink',
            'vrml' =>'model/vrml',
            'vxml' =>'application/voicexml+xml',
            'wav' =>'audio/x-wav',
            'wbmp' =>'image/vnd.wap.wbmp',
            'wbxml' =>'application/vnd.wap.wbxml',
            'wml' =>'text/vnd.wap.wml',
            'wmlc' =>'application/vnd.wap.wmlc',
            'wmlc' =>'application/vnd.wap.wmlc',
            'wmls' =>'text/vnd.wap.wmlscript',
            'wmlsc' =>'application/vnd.wap.wmlscriptc',
            'wmlsc' =>'application/vnd.wap.wmlscriptc',
            'wrl' =>'model/vrml',
            'xbm' =>'image/x-xbitmap',
            'xht' =>'application/xhtml+xml',
            'xhtml' =>'application/xhtml+xml',
            'xls' =>'application/vnd.ms-excel',
            'xml xsl' =>'application/xml',
            'xpm' =>'image/x-xpixmap',
            'xslt' =>'application/xslt+xml',
            'xul' =>'application/vnd.mozilla.xul+xml',
            'xwd' =>'image/x-xwindowdump',
            'xyz' =>'chemical/x-xyz',
            'zip' =>'application/zip'
        );

        if (isset( $mimet[$idx] )) {
            return $mimet[$idx];
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * Delete InputDocument
     *
     * @param string $inputDocumentUid
     *
     * @return array Return an array with data of an InputDocument
     * @throws Exception
     */
    public function removeInputDocument($inputDocumentUid)
    {
        try {
            $oAppDocument = AppDocumentPeer::retrieveByPK( $inputDocumentUid, 1 );
            if (is_null( $oAppDocument ) || $oAppDocument->getAppDocStatus() == 'DELETED') {
                throw new Exception(G::LoadTranslation("ID_CASES_INPUT_DOES_NOT_EXIST", array($inputDocumentUid)));
            }

            $ws = new WsBase();
            $ws->removeDocument($inputDocumentUid);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $taskUid
     * @param string $appDocComment
     * @param string $inputDocumentUid
     * @param string $userUid
     *
     * @return array Return an array with data of an InputDocument
     * @throws Exception
     */
    public function addCasesInputDocument($applicationUid, $taskUid, $appDocComment, $inputDocumentUid, $userUid, $runningWorkflow = true)
    {
        try {
            if ((isset( $_FILES['form'] )) && ($_FILES['form']['error'] != 0)) {
                $code = $_FILES['form']['error'];
                switch ($code) {
                    case UPLOAD_ERR_INI_SIZE:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_INI_SIZE' );
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_FORM_SIZE' );
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_PARTIAL' );
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_NO_FILE' );
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_NO_TMP_DIR' );
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_CANT_WRITE' );
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_EXTENSION' );
                        break;
                    default:
                        $message = G::LoadTranslation( 'ID_UPLOAD_ERR_UNKNOWN' );
                        break;
                }
                G::SendMessageText( $message, "ERROR" );
                $backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
                G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
                die();
            }

            $appDocUid = G::generateUniqueID();
            $docVersion = '';
            $appDocType = 'INPUT';
            $case = new Cases();
            $delIndex = AppDelegation::getCurrentIndex($applicationUid);

            if ($runningWorkflow) {
                $case->thisIsTheCurrentUser($applicationUid, $delIndex, $userUid, 'REDIRECT', 'casesListExtJs');
            } else {
                $criteria = new Criteria('workflow');

                $criteria->add(AppDelegationPeer::APP_UID, $applicationUid);
                $criteria->add(AppDelegationPeer::DEL_INDEX, $delIndex);
                $criteria->add(AppDelegationPeer::USR_UID, $userUid);

                $rsCriteria = ProcessUserPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    $case2 = new BusinessModelCases();

                    $arrayApplicationData = $case2->getApplicationRecordByPk($applicationUid, [], false);

                    $msg = '';

                    $supervisor = new ProcessSupervisor();
                    $flagps = $supervisor->isUserProcessSupervisor($arrayApplicationData['PRO_UID'], $userUid);

                    if ($flagps == false) {
                        $msg = G::LoadTranslation('ID_USER_NOT_IT_BELONGS_CASE_OR_NOT_SUPERVISOR');
                    }

                    if ($msg == '') {
                        $criteria = new Criteria('workflow');

                        $criteria->add(StepSupervisorPeer::PRO_UID, $arrayApplicationData['PRO_UID'], Criteria::EQUAL);
                        $criteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT', Criteria::EQUAL);
                        $criteria->add(StepSupervisorPeer::STEP_UID_OBJ, $inputDocumentUid, Criteria::EQUAL);

                        $rsCriteria = StepSupervisorPeer::doSelectRS($criteria);

                        if (!$rsCriteria->next()) {
                            $msg = G::LoadTranslation('ID_USER_IS_SUPERVISOR_DOES_NOT_ASSOCIATED_INPUT_DOCUMENT');
                        }
                    }

                    if ($msg != '') {
                        if ($runningWorkflow) {
                            G::SendMessageText($msg, 'ERROR');
                            $backUrlObj = explode('sys' . SYS_SYS, $_SERVER['HTTP_REFERER']);

                            G::header('location: ' . '/sys' . SYS_SYS . $backUrlObj[1]);
                            exit(0);
                        } else {
                            throw new Exception($msg);
                        }
                    }
                }
            }

            //Load the fields
            $arrayField = $case->loadCase($applicationUid);
            $arrayField["APP_DATA"] = array_merge($arrayField["APP_DATA"], G::getSystemConstants());
            //Validate Process Uid and Input Document Process Uid
            $inputDocumentInstance = new \InputDocument();
            $inputDocumentFields = $inputDocumentInstance->load($inputDocumentUid);
            if ($arrayField['PRO_UID'] != $inputDocumentFields['PRO_UID']) {
                throw new Exception(G::LoadTranslation("ID_INPUT_DOCUMENT_DOES_NOT_EXIST",
                                     array('UID=' . $inputDocumentUid, 'PRO_UID=' . $arrayField['PRO_UID'])));
            }
            //Triggers
            $arrayTrigger = $case->loadTriggers($taskUid, "INPUT_DOCUMENT", $inputDocumentUid, "AFTER");
            //Add Input Document
            if (empty($_FILES)) {
                throw new Exception(G::LoadTranslation("ID_CASES_INPUT_FILENAME_DOES_NOT_EXIST"));
            }
            if (!$_FILES["form"]["error"]) {
                $_FILES["form"]["error"] = 0;
            }
            if (isset($_FILES) && isset($_FILES["form"]) && count($_FILES["form"]) > 0) {
                $appDocUid = $case->addInputDocument($inputDocumentUid,
                    $appDocUid,
                    $docVersion,
                    $appDocType,
                    $appDocComment,
                    '',
                    $applicationUid,
                    $delIndex,
                    $taskUid,
                    $userUid,
                    "xmlform",
                    $_FILES["form"]["name"],
                    $_FILES["form"]["error"],
                    $_FILES["form"]["tmp_name"]);
            }
            //Trigger - Execute after - Start
            $arrayField["APP_DATA"] = $case->executeTriggers ($taskUid,
                "INPUT_DOCUMENT",
                $inputDocumentUid,
                "AFTER",
                $arrayField["APP_DATA"]);
            //Trigger - Execute after - End
            //Save data
            $arrayData = array();
            $arrayData["APP_NUMBER"] = $arrayField["APP_NUMBER"];
            //$arrayData["APP_PROC_STATUS"] = $arrayField["APP_PROC_STATUS"];
            $arrayData["APP_DATA"]  = $arrayField["APP_DATA"];
            $arrayData["DEL_INDEX"] = $delIndex;
            $arrayData["TAS_UID"]   = $taskUid;
            $case->updateCase($applicationUid, $arrayData);
            return($this->getCasesInputDocument($applicationUid, $userUid, $appDocUid));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $files $_FILES request files
     * @param $caseInstance Cases object class.cases
     * @param $aData array data case
     * @param $userUid string user id
     * @param $appUid string application id
     * @param $delIndex int the index case
     */
    public function uploadFileCase($files, $caseInstance, $aData, $userUid, $appUid, $delIndex)
    {
        $arrayField = array();
        $arrayFileName = array();
        $arrayFileTmpName = array();
        $arrayFileError = array();
        $i = 0;
        foreach ($files["form"]["name"] as $fieldIndex => $fieldValue) {
            if (is_array($fieldValue)) {
                foreach ($fieldValue as $index => $value) {
                    if (is_array($value)) {
                        foreach ($value as $grdFieldIndex => $grdFieldValue) {
                            $arrayField[$i]["grdName"] = $fieldIndex;
                            $arrayField[$i]["grdFieldName"] = $grdFieldIndex;
                            $arrayField[$i]["index"] = $index;

                            $arrayFileName[$i] = $files["form"]["name"][$fieldIndex][$index][$grdFieldIndex];
                            $arrayFileTmpName[$i] = $files["form"]["tmp_name"][$fieldIndex][$index][$grdFieldIndex];
                            $arrayFileError[$i] = $files["form"]["error"][$fieldIndex][$index][$grdFieldIndex];
                            $i = $i + 1;
                        }
                    }
                }
            } else {
                $arrayField[$i] = $fieldIndex;

                $arrayFileName[$i] = $files["form"]["name"][$fieldIndex];
                $arrayFileTmpName[$i] = $files["form"]["tmp_name"][$fieldIndex];
                $arrayFileError[$i] = $files["form"]["error"][$fieldIndex];
                $i = $i + 1;
            }
        }
        if (count($arrayField) > 0) {
            foreach ($arrayField as $i => $item) {
            //for ($i = 0; $i <= count($arrayField) - 1; $i++) {
                if ($arrayFileError[$i] == 0) {
                    $indocUid = null;
                    $fieldName = null;
                    $fileSizeByField = 0;

                    if (is_array($arrayField[$i])) {
                        if (isset($_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]]) && !empty($_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]])) {
                            $indocUid = $_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]];
                        }
                        $fieldName = $arrayField[$i]["grdName"] . "_" . $arrayField[$i]["index"] . "_" . $arrayField[$i]["grdFieldName"];
                        if (isset($files["form"]["size"][$arrayField[$i]["grdName"]][$arrayField[$i]["index"]][$arrayField[$i]["grdFieldName"]])) {
                            $fileSizeByField = $files["form"]["size"][$arrayField[$i]["grdName"]][$arrayField[$i]["index"]][$arrayField[$i]["grdFieldName"]];
                        }
                    } else {
                        if (isset($_POST["INPUTS"][$arrayField[$i]]) && !empty($_POST["INPUTS"][$arrayField[$i]])) {
                            $indocUid = $_POST["INPUTS"][$arrayField[$i]];
                        }
                        $fieldName = $arrayField[$i];
                        if (isset($files["form"]["size"][$fieldName])) {
                            $fileSizeByField = $files["form"]["size"][$fieldName];
                        }
                    }
                    if ($indocUid != null) {
                        $oInputDocument = new \InputDocument();
                        $aID = $oInputDocument->load($indocUid);

                        //Get the Custom Folder ID (create if necessary)
                        $oFolder = new AppFolder();

                        //***Validating the file allowed extensions***
                        $res = G::verifyInputDocExtension($aID['INP_DOC_TYPE_FILE'], $arrayFileName[$i], $arrayFileTmpName[$i]);
                        if ($res->status == 0) {
                            //The value of the variable "_label" is cleared because the file load failed.
                            //The validation of the die command should be improved.
                            if (isset($aData["APP_DATA"][$item . "_label"]) && !empty($aData["APP_DATA"][$item . "_label"])) {
                                unset($aData["APP_DATA"][$item . "_label"]);
                                $caseInstance->updateCase($appUid, $aData);
                            }
                            $message = $res->message;
                            G::SendMessageText($message, "ERROR");
                            $backUrlObj = explode("sys" . SYS_SYS, $_SERVER['HTTP_REFERER']);
                            G::header("location: " . "/sys" . SYS_SYS . $backUrlObj[1]);
                            die();
                        }

                        //--- Validate Filesize of $_FILE
                        $inpDocMaxFilesize = $aID["INP_DOC_MAX_FILESIZE"];
                        $inpDocMaxFilesizeUnit = $aID["INP_DOC_MAX_FILESIZE_UNIT"];

                        $inpDocMaxFilesize = $inpDocMaxFilesize * (($inpDocMaxFilesizeUnit == "MB") ? 1024 * 1024 : 1024); //Bytes

                        if ($inpDocMaxFilesize > 0 && $fileSizeByField > 0) {
                            if ($fileSizeByField > $inpDocMaxFilesize) {
                                G::SendMessageText(G::LoadTranslation("ID_SIZE_VERY_LARGE_PERMITTED"), "ERROR");
                                $arrayAux1 = explode("sys" . SYS_SYS, $_SERVER["HTTP_REFERER"]);
                                G::header("location: /sys" . SYS_SYS . $arrayAux1[1]);
                                exit(0);
                            }
                        }

                        $aFields = array("APP_UID" => $appUid, "DEL_INDEX" => $delIndex, "USR_UID" => $userUid, "DOC_UID" => $indocUid, "APP_DOC_TYPE" => "INPUT", "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"), "APP_DOC_COMMENT" => "", "APP_DOC_TITLE" => "", "APP_DOC_FILENAME" => $arrayFileName[$i], "FOLDER_UID" => $oFolder->createFromPath($aID["INP_DOC_DESTINATION_PATH"]), "APP_DOC_TAGS" => $oFolder->parseTags($aID["INP_DOC_TAGS"]), "APP_DOC_FIELDNAME" => $fieldName);
                    } else {
                        $aFields = array("APP_UID" => $appUid, "DEL_INDEX" => $delIndex, "USR_UID" => $userUid, "DOC_UID" => -1, "APP_DOC_TYPE" => "ATTACHED", "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"), "APP_DOC_COMMENT" => "", "APP_DOC_TITLE" => "", "APP_DOC_FILENAME" => $arrayFileName[$i], "APP_DOC_FIELDNAME" => $fieldName);
                    }

                    $sExtension = pathinfo($aFields["APP_DOC_FILENAME"]);
                    if (Bootstrap::getDisablePhpUploadExecution() === 1 && $sExtension["extension"] === 'php') {
                        $message = G::LoadTranslation('THE_UPLOAD_OF_PHP_FILES_WAS_DISABLED');
                        Bootstrap::registerMonologPhpUploadExecution('phpUpload', 550, $message, 'processmaker.log');
                        G::SendMessageText($message, "ERROR");
                        $backUrlObj = explode("sys" . SYS_SYS, $_SERVER['HTTP_REFERER']);
                        G::header("location: " . "/sys" . SYS_SYS . $backUrlObj[1]);
                        die();
                    }

                    $oAppDocument = new AppDocument();
                    $oAppDocument->create($aFields);

                    $iDocVersion = $oAppDocument->getDocVersion();
                    $sAppDocUid = $oAppDocument->getAppDocUid();
                    $aInfo = pathinfo($oAppDocument->getAppDocFilename());
                    $sExtension = ((isset($aInfo["extension"])) ? $aInfo["extension"] : "");
                    $pathUID = G::getPathFromUID($appUid);
                    $sPathName = PATH_DOCUMENT . $pathUID . PATH_SEP;
                    $sFileName = $sAppDocUid . "_" . $iDocVersion . "." . $sExtension;

                    G::uploadFile($arrayFileTmpName[$i], $sPathName, $sFileName);

                    //set variable for APP_DOC_UID
                    $aData["APP_DATA"][$oAppDocument->getAppDocFieldname()] = G::json_encode([$oAppDocument->getAppDocUid()]);
                    $aData["APP_DATA"][$oAppDocument->getAppDocFieldname() . "_label"] = G::json_encode([$oAppDocument->getAppDocFilename()]);
                    $caseInstance->updateCase($appUid, $aData);

                    //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
                    $oPluginRegistry = PluginRegistry::loadSingleton();

                    if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists("uploadDocumentData")) {
                        $triggerDetail = $oPluginRegistry->getTriggerInfo(PM_UPLOAD_DOCUMENT);
                        $documentData = new \uploadDocumentData($appUid, $userUid, $sPathName . $sFileName, $aFields["APP_DOC_FILENAME"], $sAppDocUid, $iDocVersion);
                        $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);

                        if ($uploadReturn) {
                            $aFields["APP_DOC_PLUGIN"] = $triggerDetail->getNamespace();
                            if (!isset($aFields["APP_DOC_UID"])) {
                                $aFields["APP_DOC_UID"] = $sAppDocUid;
                            }
                            if (!isset($aFields["DOC_VERSION"])) {
                                $aFields["DOC_VERSION"] = $iDocVersion;
                            }
                            $oAppDocument->update($aFields);
                            unlink($sPathName . $sFileName);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get all versions related to the appDocUid
     * @param string $appUid, Uid of the case
     * @param string $appDocUid, Uid of the document
     * @param string $docUid, Uid of the inputDocument
     * @param string $userUid, Uid of user
     * @param string $status, It can be ACTIVE, DELETED
     * @param string $docType, It can be ATTACHED, INPUT
     * @param string $docTags, It can be EXTERNAL, INPUT
     *
     * @return array $docVersion
     * @throws Exception
     */
    public function getAllVersionByDocUid ($appUid, $appDocUid, $docUid = '', $userUid = '', $status = 'ACTIVE', $docType = '', $docTags = '')
    {
        try {
            $criteria = new Criteria('workflow');
            $criteria->add(AppDocumentPeer::APP_UID, $appUid);
            $criteria->add(AppDocumentPeer::APP_DOC_UID, $appDocUid);
            $criteria->add(AppDocumentPeer::APP_DOC_STATUS, $status, Criteria::EQUAL);
            if (!empty($docUid)) {
                $criteria->add(AppDocumentPeer::DOC_UID, $docUid);
            }
            if (!empty($userUid)) {
                $criteria->add(AppDocumentPeer::USR_UID, $userUid);
            }
            if (!empty($docType)) {
                $criteria->add(AppDocumentPeer::APP_DOC_TYPE, $docType);
            }
            if (!empty($docTags)) {
                $criteria->add(AppDocumentPeer::APP_DOC_TAGS, $docTags);
            }
            $criteria->addDescendingOrderByColumn(AppDocumentPeer::DOC_VERSION);
            $dataset = AppDocumentPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $config = new Configurations();
            $confEnvSetting = $config->getFormats();
            $user = new Users();

            $arrayInputDocument = array();
            while ($row = $dataset->getRow()) {
                //todo, we use this *** in others endpoint for mark that user not exist, but we need to change
                $userInfo = '***';
                if ($row["USR_UID"] !== '-1') {
                    $arrayUserData = $user->load($row["USR_UID"]);
                    $userInfo = $config->usersNameFormatBySetParameters($confEnvSetting["format"], $arrayUserData["USR_USERNAME"], $arrayUserData["USR_FIRSTNAME"], $arrayUserData["USR_LASTNAME"]);
                }
                $row["APP_DOC_CREATE_USER"] = $userInfo;
                $arrayInputDocument[] = $this->getAppDocumentDataFromRecord($row);
                $dataset->next();
            }
            return $arrayInputDocument;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
