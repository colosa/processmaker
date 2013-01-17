<?php
/**
 * class.wsBase.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

//It works with the table CONFIGURATION in a WF dataBase
require_once ("classes/model/Application.php");
require_once ("classes/model/AppCacheView.php");
require_once ("classes/model/AppDelegation.php");
require_once ("classes/model/AppDocument.php");
require_once ("classes/model/AppDelay.php");
require_once ("classes/model/AppNotes.php");
require_once ("classes/model/AppThread.php");
require_once ("classes/model/Department.php");
require_once ("classes/model/Dynaform.php");
require_once ("classes/model/Groupwf.php");
require_once ("classes/model/InputDocument.php");
require_once ("classes/model/Language.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/ReportTable.php");
require_once ("classes/model/ReportVar.php");
require_once ("classes/model/Route.php");
require_once ("classes/model/Step.php");
require_once ("classes/model/StepTrigger.php");
require_once ("classes/model/Task.php");
require_once ("classes/model/TaskUser.php");
require_once ("classes/model/Triggers.php");
require_once ("classes/model/Users.php");
require_once ("classes/model/Session.php");
require_once ("classes/model/Content.php");
G::LoadClass( "ArrayPeer" );
G::LoadClass( "BasePeer" );
G::LoadClass( 'case' );
G::LoadClass( 'derivation' );
G::LoadClass( 'groups' );
G::LoadClass( 'sessions' );
G::LoadClass( 'processes' );
G::LoadClass( 'processMap' );
G::LoadClass( 'pmScript' );
G::LoadClass( 'spool' );
G::LoadClass( 'tasks' );
G::LoadClass( 'wsResponse' );

/**
 * Copyright (C) 2009 COLOSA
 * License: LGPL, see LICENSE
 * Last Modify: 26.06.2008 10:05:00
 * Last modify by: Erik Amaru Ortiz <erik@colosa.com>
 * Last Modify comment(26.06.2008): the session expired verification was removed from here to soap class
 *
 * @package workflow.engine.classes
 */

class wsBase
{
    public $stored_system_variables; //boolean
    public $wsSessionId; //web service session id, if the wsbase function is used from a WS request

    public function __construct ($params = null)
    {
        $this->stored_system_variables = false;

        if ($params != null) {
            $this->stored_system_variables = (isset( $params->stored_system_variables ) ? $params->stored_system_variables : false);

            $this->wsSessionId = isset( $params->wsSessionId ) ? $params->wsSessionId : '';
        }
    }

    /**
     * function to start a web services session in ProcessMaker
     *
     * @param string $userid
     * @param string $password
     * @return $wsResponse will return an object
     */
    public function login ($userid, $password)
    {
        global $RBAC;

        try {
            $uid = $RBAC->VerifyLogin( $userid, $password );

            switch ($uid) {
                case '':
                case - 1: //The user doesn't exist
                    $wsResponse = new wsResponse( 3, G::loadTranslation( 'ID_USER_NOT_REGISTERED' ) );
                    break;
                case - 2: //The password is incorrect
                    $wsResponse = new wsResponse( 4, G::loadTranslation( 'ID_WRONG_PASS' ) );
                    break;
                case - 3: //The user is inactive
                    $wsResponse = new wsResponse( 5, G::loadTranslation( 'ID_USER_INACTIVE' ) );
                    break;
                case - 4: //The Due date is finished
                    $wsResponse = new wsResponse( 5, G::loadTranslation( 'ID_USER_INACTIVE' ) );
                    break;
            }

            if ($uid < 0 || $uid == '') {
                throw (new Exception( serialize( $wsResponse ) ));
            }

            //check access to PM
            $RBAC->loadUserRolePermission( $RBAC->sSystem, $uid );
            $res = $RBAC->userCanAccess( "PM_LOGIN" );

            if ($res != 1) {
                //if ($res == -2)
                //  $wsResponse = new wsResponse(1, G::loadTranslation('ID_USER_HAVENT_RIGHTS_SYSTEM'));
                //else
                $wsResponse = new wsResponse( 2, G::loadTranslation( 'ID_USER_HAVENT_RIGHTS_SYSTEM' ) );
                throw (new Exception( serialize( $wsResponse ) ));
            }

            $sessionId = G::generateUniqueID();
            $wsResponse = new wsResponse( '0', $sessionId );

            $session = new Session();
            $session->setSesUid( $sessionId );
            $session->setSesStatus( 'ACTIVE' );
            $session->setUsrUid( $uid );
            $session->setSesRemoteIp( $_SERVER['REMOTE_ADDR'] );
            $session->setSesInitDate( date( 'Y-m-d H:i:s' ) );
            $session->setSesDueDate( date( 'Y-m-d H:i:s', mktime( date( 'H' ), date( 'i' ) + 15, date( 's' ), date( 'm' ), date( 'd' ), date( 'Y' ) ) ) );
            $session->setSesEndDate( '' );
            $session->Save();

            //save the session in DataBase


            return $wsResponse;
        } catch (Exception $e) {
            $wsResponse = unserialize( $e->getMessage() );

            return $wsResponse;
        }
    }

    /**
     * get all groups
     *
     * @param none
     * @return $result will return an object
     */
    public function processList ()
    {
        try {
            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            //$oCriteria->add(ProcessPeer::PRO_STATUS , 'ACTIVE');
            $oCriteria->add( ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL );
            $oDataset = ProcessPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $oProcess = new Process();
                $arrayProcess = $oProcess->Load( $aRow['PRO_UID'] );
                $result[] = array ('guid' => $aRow['PRO_UID'],'name' => $arrayProcess['PRO_TITLE']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get all roles, to see all roles
     *
     * @param none
     * @return $result will return an object
     */
    public function roleList ()
    {
        try {
            $result = array ();

            $RBAC = & RBAC::getSingleton();
            $RBAC->initRBAC();
            $oCriteria = $RBAC->listAllRoles();
            $oDataset = GulliverBasePeer::doSelectRs( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array ('guid' => $aRow['ROL_UID'],'name' => $aRow['ROL_CODE']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get all groups
     *
     * @param none
     * @return $result will return an object
     */
    public function groupList ()
    {
        try {
            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $oGroupwf = new Groupwf();
                $arrayGroupwf = $oGroupwf->Load( $aRow['GRP_UID'] );
                $result[] = array ('guid' => $aRow['GRP_UID'],'name' => $arrayGroupwf['GRP_TITLE']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get all department
     *
     * @param none
     * @return $result will return an object
     */
    public function departmentList ()
    {
        try {
            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( DepartmentPeer::DEP_STATUS, 'ACTIVE' );
            $oDataset = DepartmentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $oDepartment = new Department();
                $aDepartment = $oDepartment->Load( $aRow['DEP_UID'] );
                $node['guid'] = $aRow['DEP_UID'];
                $node['name'] = $aDepartment['DEPO_TITLE'];
                $node['parentUID'] = $aDepartment['DEP_PARENT'];
                $node['dn'] = $aDepartment['DEP_LDAP_DN'];

                //get the users from this department
                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn( 'COUNT(*)' );
                $c->add( UsersPeer::DEP_UID, $aRow['DEP_UID'] );
                $rs = UsersPeer::doSelectRS( $c );
                $rs->next();
                $row = $rs->getRow();
                $count = $row[0];

                $node['users'] = $count;
                $result[] = $node;
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * Get case list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function caseList ($userId)
    {
        try {
            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $del = DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn( ApplicationPeer::APP_UID );
            $oCriteria->addSelectColumn( ApplicationPeer::APP_NUMBER );
            $oCriteria->addSelectColumn( ApplicationPeer::APP_STATUS );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria->addAsColumn( 'CASE_TITLE', 'C1.CON_VALUE' );
            $oCriteria->addAlias( "C1", 'CONTENT' );
            $caseTitleConds = array ();
            $caseTitleConds[] = array (ApplicationPeer::APP_UID,'C1.CON_ID'
            );
            $caseTitleConds[] = array ('C1.CON_CATEGORY',$del . 'APP_TITLE' . $del
            );
            $caseTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $caseTitleConds, Criteria::LEFT_JOIN );

            $oCriteria->addJoin( ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN );

            $oCriteria->add( ApplicationPeer::APP_STATUS, array ('TO_DO','DRAFT'
            ), Criteria::IN );
            $oCriteria->add( AppDelegationPeer::USR_UID, $userId );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
            $oCriteria->addDescendingOrderByColumn( ApplicationPeer::APP_NUMBER );
            $oDataset = ApplicationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                /*
                $result[] = array(
                    'guid'     => $aRow['APP_UID'],
                    'name'     => $aRow['CASE_TITLE'],
                    'status'   => $aRow['APP_STATUS'],
                    'delIndex' => $aRow['DEL_INDEX']
                );
                */
                $result[] = array('guid' => $aRow['APP_UID'],
                                  'name' => $aRow['APP_NUMBER'],
                                  'status' => $aRow['APP_STATUS'],
                                  'delIndex' => $aRow['DEL_INDEX'],
                                  'processId' => $aRow['PRO_UID']);
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),
                               'name' => $e->getMessage(),
                               'status' => $e->getMessage(),
                               'status' => $e->getMessage(),
                               'processId' => $e->getMessage());

            return $result;
        }
    }

    /**
     * Get unassigned case list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function unassignedCaseList ($userId)
    {
        try {
            $result = array ();
            $oAppCache = new AppCacheView();
            $Criteria = $oAppCache->getUnassignedListCriteria( $userId );
            $oDataset = AppCacheViewPeer::doSelectRS( $Criteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array ('guid' => $aRow['APP_UID'],
                                   'name' => $aRow['APP_NUMBER'],
                                   'delIndex' => $aRow['DEL_INDEX'],
                                   'processId' => $aRow['PRO_UID']);

                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),
                               'name' => $e->getMessage(),
                               'status' => $e->getMessage(),
                               'status' => $e->getMessage(),
                               'processId' => $e->getMessage());

            return $result;
        }
    }

    /**
     * get all groups
     *
     * @param none
     * @return $result will return an object
     */
    public function userList ()
    {
        try {
            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                //$oProcess = new User();
                //$arrayProcess = $oUser->Load($aRow['PRO_UID']);
                $result[] = array ('guid' => $aRow['USR_UID'],'name' => $aRow['USR_USERNAME']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get list of all the available triggers in a workspace
     *
     * @param none
     * @return $result will return an object
     */
    public function triggerList ()
    {
        try {
            $del = DBAdapter::getStringDelimiter();

            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( TriggersPeer::TRI_UID );
            $oCriteria->addSelectColumn( TriggersPeer::PRO_UID );
            $oCriteria->addAsColumn( 'TITLE', 'C1.CON_VALUE' );
            $oCriteria->addAlias( "C1", 'CONTENT' );

            $caseTitleConds = array ();
            $caseTitleConds[] = array (TriggersPeer::TRI_UID,'C1.CON_ID'
            );
            $caseTitleConds[] = array ('C1.CON_CATEGORY',$del . 'TRI_TITLE' . $del
            );
            $caseTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $caseTitleConds, Criteria::LEFT_JOIN );
            //$oCriteria->add(TriggersPeer::USR_STATUS, 'ACTIVE');
            $oDataset = TriggersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array ('guid' => $aRow['TRI_UID'],'name' => $aRow['TITLE'],'processId' => $aRow['PRO_UID']
                );

                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get list of the uploaded documents for a given case
     *
     * @param string $sApplicationUID
     * @param string $sUserUID
     * @return $result
     */
    public function inputDocumentList ($sApplicationUID, $sUserUID)
    {
        try {
            $oCase = new Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = $oCase->getAllUploadedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );

            $result = array ();
            global $_DBArray;

            foreach ($_DBArray['inputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['guid'] = $row['APP_DOC_UID'];
                    $docrow['filename'] = $row['APP_DOC_FILENAME'];
                    $docrow['docId'] = $row['DOC_UID'];
                    $docrow['version'] = $row['DOC_VERSION'];
                    $docrow['createDate'] = $row['CREATE_DATE'];
                    $docrow['createBy'] = $row['CREATED_BY'];
                    $docrow['type'] = $row['TYPE'];
                    $docrow['index'] = $row['APP_DOC_INDEX'];
                    $docrow['link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * input document process list
     *
     * @param string $sProcessUID
     * @return $result will return an object
     */
    public function inputDocumentProcessList ($sProcessUID)
    {
        try {
            global $_DBArray;

            $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');

            $oMap = new processMap();
            $oCriteria = $oMap->getInputDocumentsCriteria( $sProcessUID );
            $oDataset = InputDocumentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            $result = array ();

            /*
            //not necesary for SOAP message
            $result[] = array(
                'guid' => 'char',
                'name' => 'name',
                'description' => 'description'
            );
            */

            while ($aRow = $oDataset->getRow()) {
                if ($aRow['INP_DOC_TITLE'] == null) {
                    //There is no transaltion for this Document name, try to get/regenerate the label
                    $inputDocument = new InputDocument();
                    $inputDocumentObj = $inputDocument->load( $aRow['INP_DOC_UID'] );
                    $aRow['INP_DOC_TITLE'] = $inputDocumentObj['INP_DOC_TITLE'];
                    $aRow['INP_DOC_DESCRIPTION'] = $inputDocumentObj['INP_DOC_DESCRIPTION'];
                }

                $docrow = array ();
                $docrow['guid'] = $aRow['INP_DOC_UID'];
                $docrow['name'] = $aRow['INP_DOC_TITLE'];
                $docrow['description'] = $aRow['INP_DOC_DESCRIPTION'];
                $result[] = $docrow;
                $oDataset->next();
            }

            //$_DBArray['inputDocArray'] = $inputDocArray;


            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * output document list
     *
     * @param string $sApplicationUID
     * @param string $sUserUID
     * @return $result will return an object
     */
    public function outputDocumentList ($sApplicationUID, $sUserUID)
    {
        try {
            $oCase = new Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = $oCase->getAllGeneratedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );

            $result = array ();
            global $_DBArray;

            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['guid'] = $row['APP_DOC_UID'];
                    $docrow['filename'] = $row['DOWNLOAD_FILE'];

                    $docrow['docId'] = $row['DOC_UID'];
                    $docrow['version'] = $row['DOC_VERSION'];
                    $docrow['createDate'] = $row['CREATE_DATE'];
                    $docrow['createBy'] = $row['CREATED_BY'];
                    $docrow['type'] = $row['TYPE'];
                    $docrow['index'] = $row['APP_DOC_INDEX'];
                    $docrow['link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * remove document
     *
     * @param string $appDocUid
     * @return $result will return an object
     */
    public function removeDocument ($appDocUid)
    {
        try {
            $oAppDocument = new AppDocument();
            $oAppDocument->remove( $appDocUid, 1 ); //always send version 1
            $result = new wsResponse( 0, " $appDocUid" );

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * get task list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function taskList ($userId)
    {
        try {
            g::loadClass( 'groups' );
            $oGroup = new Groups();
            $aGroups = $oGroup->getActiveGroupsForAnUser( $userId );

            $result = array ();
            $oCriteria = new Criteria( 'workflow' );
            $del = DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn( TaskPeer::PRO_UID );
            $oCriteria->addSelectColumn( TaskPeer::TAS_UID );
            $oCriteria->addSelectColumn( TaskPeer::TAS_START );
            $oCriteria->setDistinct();
            $oCriteria->addAsColumn( 'TAS_TITLE', 'C1.CON_VALUE' );
            $oCriteria->addAlias( "C1", 'CONTENT' );
            $tasTitleConds = array ();
            $tasTitleConds[] = array (TaskPeer::TAS_UID,'C1.CON_ID'
            );
            $tasTitleConds[] = array ('C1.CON_CATEGORY',$del . 'TAS_TITLE' . $del
            );
            $tasTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $tasTitleConds, Criteria::LEFT_JOIN );

            $oCriteria->addJoin( TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN );
            $oCriteria->addOr( TaskUserPeer::USR_UID, $userId );
            $oCriteria->addOr( TaskUserPeer::USR_UID, $aGroups, Criteria::IN );

            $oDataset = TaskPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array ('guid' => $aRow['TAS_UID'],
                                   'name' => $aRow['TAS_TITLE'],
                                   'processId' => $aRow['PRO_UID'],
                                   'initialTask' => $aRow['TAS_START'] == 'TRUE' ? '1' : '0');
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * send message
     *
     * @param string $caseId
     * @param string $sFrom
     * @param string $sTo
     * @param string $sCc
     * @param string $sBcc
     * @param string $sSubject
     * @param string $sTemplate
     * @param $appFields = null
     * @param $aAttachment = null
     * @param boolean $showMessage = true
     * @param int $delIndex = 0
     * @return $result will return an object
     */
    public function sendMessage(
        $caseId,
        $sFrom,
        $sTo,
        $sCc,
        $sBcc,
        $sSubject,
        $sTemplate,
        $appFields = null,
        $aAttachment = null,
        $showMessage = true,
        $delIndex = 0
    ) {
        try {
            G::LoadClass("system");

            $aSetup = System::getEmailConfiguration();

            $passwd = $aSetup['MESS_PASSWORD'];
            $passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
            $auxPass = explode( 'hash:', $passwdDec );
            if (count( $auxPass ) > 1) {
                if (count( $auxPass ) == 2) {
                    $passwd = $auxPass[1];
                } else {
                    array_shift( $auxPass );
                    $passwd = implode( '', $auxPass );
                }
            }
            $aSetup['MESS_PASSWORD'] = $passwd;
            if ($aSetup['MESS_RAUTH'] == false || (is_string($aSetup['MESS_RAUTH']) && $aSetup['MESS_RAUTH'] == 'false')) {
                $aSetup['MESS_RAUTH'] = 0;
            } else {
                $aSetup['MESS_RAUTH'] = 1;
            }

            $oSpool = new spoolRun();
            $oSpool->setConfig( array ('MESS_ENGINE' => $aSetup['MESS_ENGINE'],'MESS_SERVER' => $aSetup['MESS_SERVER'],'MESS_PORT' => $aSetup['MESS_PORT'],'MESS_ACCOUNT' => $aSetup['MESS_ACCOUNT'],'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],'SMTPAuth' => $aSetup['MESS_RAUTH']
            ) );

            $oCase = new Cases();
            $oldFields = $oCase->loadCase( $caseId );

            $pathEmail = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $oldFields['PRO_UID'] . PATH_SEP;
            $fileTemplate = $pathEmail . $sTemplate;
            G::mk_dir( $pathEmail, 0777, true );

            if (! file_exists( $fileTemplate )) {
                $data['FILE_TEMPLATE'] = $fileTemplate;
                $result = new wsResponse( 28, G::LoadTranslation( 'ID_TEMPLATE_FILE_NOT_EXIST', SYS_LANG, $data ) );

                return $result;
            }

            if ($appFields == null) {
                $Fields = $oldFields['APP_DATA'];
            } else {
                $Fields = array_merge( $oldFields['APP_DATA'], $appFields );
            }

            $sBody = G::replaceDataGridField(file_get_contents($fileTemplate), $Fields);

            $hasEmailFrom = preg_match( '/(.+)@(.+)\.(.+)/', $sFrom, $match );

            if (! $hasEmailFrom || strpos( $sFrom, $aSetup['MESS_ACCOUNT'] ) === false) {
                $sFrom = '"' . stripslashes( $sFrom ) . '" <' . $aSetup['MESS_ACCOUNT'] . ">";
            }
            $showMessage = ($showMessage) ? 1 : 0 ;

            $messageArray = array(
                "msg_uid" => "",
                "app_uid" => $caseId,
                "del_index"    => $delIndex,
                "app_msg_type" => "TRIGGER",
                "app_msg_subject" => $sSubject,
                "app_msg_from"    => $sFrom,
                "app_msg_to"   => $sTo,
                "app_msg_body" => $sBody,
                "app_msg_cc"   => $sCc,
                "app_msg_bcc"  => $sBcc,
                "app_msg_attach"   => $aAttachment,
                "app_msg_template" => "",
                "app_msg_status"   => "pending",
                "app_msg_show_message" => $showMessage,
                "contentTypeIsHtml" => (preg_match("/^.+\.html?$/i", $fileTemplate))? true : false
            );

            $oSpool->create( $messageArray );
            $oSpool->sendMail();

            if ($oSpool->status == 'sent') {
                $result = new wsResponse( 0, G::loadTranslation( 'ID_MESSAGE_SENT' ) . ": " . $sTo );
            } else {
                $result = new wsResponse( 29, $oSpool->status . ' ' . $oSpool->error . print_r( $aSetup, 1 ) );
            }

            return $result;
        } catch (Exception $e) {
            return new wsResponse( 100, $e->getMessage() );
        }
    }

    /**
     * get case information
     *
     * @param string $caseId
     * @param string $iDelIndex
     * @return $result will return an object
     */
    public function getCaseInfo ($caseId, $iDelIndex)
    {
        try {
            $oCase = new Cases();
            $aRows = $oCase->loadCase( $caseId, $iDelIndex );

            if (count( $aRows ) == 0) {
                $data['CASE_NUMBER'] = $caseNumber;
                $result = new wsResponse( 16, G::loadTranslation( 'ID_CASE_DOES_NOT_EXIST', SYS_LANG, $data ) );

                return $result;
            }

            $oProcess = new Process();

            try {
                $uFields = $oProcess->load( $aRows['PRO_UID'] );
                $processName = $uFields['PRO_TITLE'];
            } catch (Exception $e) {
                $processName = '';
            }

            $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULLY' ) );
            $result->caseId = $aRows['APP_UID'];
            $result->caseNumber = $aRows['APP_NUMBER'];
            $result->caseName = $aRows['TITLE'];
            $result->caseStatus = $aRows['APP_STATUS'];
            $result->caseParalell = $aRows['APP_PARALLEL'];
            $result->caseCreatorUser = $aRows['APP_INIT_USER'];
            $result->caseCreatorUserName = $aRows['CREATOR'];
            $result->processId = $aRows['PRO_UID'];
            $result->processName = $processName;
            $result->createDate = $aRows['CREATE_DATE'];
            $result->updateDate = $aRows['UPDATE_DATE'];

            //now fill the array of AppDelegationPeer
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::USR_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_THREAD );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_THREAD_STATUS );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
            $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );

            $oCriteria->addAscendingOrderByColumn( AppDelegationPeer::DEL_INDEX );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $aCurrentUsers = array ();

            while ($oDataset->next()) {
                $aAppDel = $oDataset->getRow();

                $oUser = new Users();

                try {
                    $oUser->load( $aAppDel['USR_UID'] );
                    $uFields = $oUser->toArray( BasePeer::TYPE_FIELDNAME );
                    $currentUserName = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
                } catch (Exception $e) {
                    $currentUserName = '';
                }

                $oTask = new Task();

                try {
                    $uFields = $oTask->load( $aAppDel['TAS_UID'] );
                    $taskName = $uFields['TAS_TITLE'];
                } catch (Exception $e) {
                    $taskName = '';
                }

                $currentUser = new stdClass();
                $currentUser->userId = $aAppDel['USR_UID'];
                $currentUser->userName = $currentUserName;
                $currentUser->taskId = $aAppDel['TAS_UID'];
                $currentUser->taskName = $taskName;
                $currentUser->delIndex = $aAppDel['DEL_INDEX'];
                $currentUser->delThread = $aAppDel['DEL_THREAD'];
                $currentUser->delThreadStatus = $aAppDel['DEL_THREAD_STATUS'];
                $aCurrentUsers[] = $currentUser;
            }

            $result->currentUsers = $aCurrentUsers;

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * Create an new user
     *
     * @param string sessionId : The session ID.
     * @param string userName : The username for the new user.
     * @param string firstName : The user's first name.
     * @param string lastName : The user's last name.
     * @param string email : The user's email address.
     * @param string role : The user's role, such as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
     * @param string password : The user's password such as "Be@gle2" (It will be automatically encrypted
     * with an MD5 hash).
     * @param string dueDate : Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
     * @param string status : Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
     * @return $result will return an object
     */
    public function createUser ($userName, $firstName, $lastName, $email, $role, $password, $dueDate = null, $status = null)
    {
        try {
            global $RBAC;

            $RBAC->initRBAC();

            if (empty( $userName )) {
                $result = new wsCreateUserResponse( 25, G::loadTranslation( "ID_USERNAME_REQUIRED" ), null );

                return $result;
            }

            if (empty( $firstName )) {
                $result = new wsCreateUserResponse( 27, G::loadTranslation( "ID_MSG_ERROR_USR_FIRSTNAME" ), null );

                return $result;
            }

            if (empty( $password )) {
                $result = new wsCreateUserResponse( 26, G::loadTranslation( "ID_PASSWD_REQUIRED" ), null );

                return $result;
            }

            $mktimeDueDate = 0;

            if (! empty( $dueDate )) {
                if (! preg_match( "/^(\d{4})-(\d{2})-(\d{2})$/", $dueDate, $arrayMatch )) {
                    $result = new wsCreateUserResponse( - 1, G::loadTranslation( "ID_INVALID_DATA" ) . " $dueDate", null );

                    return $result;
                } else {
                    $mktimeDueDate = mktime( 0, 0, 0, intval( $arrayMatch[2] ), intval( $arrayMatch[3] ), intval( $arrayMatch[1] ) );
                }
            } else {
                $mktimeDueDate = mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) + 1 );
            }

            if (! empty( $status )) {
                if ($status != "ACTIVE" && $status != "INACTIVE" && $status != "VACATION") {
                    $result = new wsCreateUserResponse( - 1, G::loadTranslation( "ID_INVALID_DATA" ) . " $status", null );

                    return $result;
                }
            } else {
                $status = "ACTIVE";
            }

            $arrayRole = $RBAC->loadById( $role );
            $strRole = null;

            if (is_array( $arrayRole )) {
                $strRole = $arrayRole["ROL_CODE"];
            } else {
                $strRole = $role;

                if ($RBAC->verifyByCode( $role ) == 0) {
                    $data = array ();
                    $data["ROLE"] = $role;

                    $result = new wsCreateUserResponse( 6, G::loadTranslation( "ID_INVALID_ROLE", SYS_LANG, $data ), null );

                    return $result;
                }
            }

            if (strlen( $password ) > 20) {
                $result = new wsCreateUserResponse( - 1, G::loadTranslation( "ID_PASSWORD_SURPRASES" ), null );

                return $result;
            }

            if ($RBAC->verifyUser( $userName ) == 1) {
                $data = array ();
                $data["USER_ID"] = $userName;

                $result = new wsCreateUserResponse( 7, G::loadTranslation( "ID_USERNAME_ALREADY_EXISTS", SYS_LANG, $data ), null );

                return $result;
            }

            //Set fields
            $arrayData = array ();

            $arrayData["USR_USERNAME"] = $userName;
            $arrayData["USR_PASSWORD"] = md5( $password );
            $arrayData["USR_FIRSTNAME"] = $firstName;
            $arrayData["USR_LASTNAME"] = $lastName;
            $arrayData["USR_EMAIL"] = $email;
            $arrayData["USR_DUE_DATE"] = $mktimeDueDate;
            $arrayData["USR_CREATE_DATE"] = date( "Y-m-d H:i:s" );
            $arrayData["USR_UPDATE_DATE"] = date( "Y-m-d H:i:s" );
            $arrayData["USR_BIRTHDAY"] = date( "Y-m-d" );
            $arrayData["USR_AUTH_USER_DN"] = "";
            $arrayData["USR_STATUS"] = ($status == "ACTIVE") ? 1 : 0;

            $userUid = $RBAC->createUser( $arrayData, $strRole );

            $arrayData["USR_UID"] = $userUid;
            $arrayData["USR_STATUS"] = $status;
            //$arrayData["USR_PASSWORD"] = md5($userUid);
            $arrayData["USR_COUNTRY"] = "";
            $arrayData["USR_CITY"] = "";
            $arrayData["USR_LOCATION"] = "";
            $arrayData["USR_ADDRESS"] = "";
            $arrayData["USR_PHONE"] = "";
            $arrayData["USR_ZIP_CODE"] = "";
            $arrayData["USR_POSITION"] = "";
            //$arrayData["USR_RESUME"]
            $arrayData["USR_ROLE"] = $strRole;
            //$arrayData["USR_REPLACED_BY"]


            $user = new Users();
            $user->create( $arrayData );

            //Response
            $data = array ();
            $data["FIRSTNAME"] = $firstName;
            $data["LASTNAME"] = $lastName;
            $data["USER_ID"] = $userName;

            $res = new wsResponse( 0, G::loadTranslation( "ID_USER_CREATED_SUCCESSFULLY", SYS_LANG, $data ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"userUID" => $userUid,"timestamp" => $res->timestamp
            );

            return $result;
        } catch (Exception $e) {
            $result = wsCreateUserResponse( 100, $e->getMessage(), null );

            return $result;
        }
    }

    /**
     * Update user
     *
     * @param string userUid : The user UID.
     * @param string userName : The username for the user.
     * @param string firstName : Optional parameter. The user's first name.
     * @param string lastName : Optional parameter. The user's last name.
     * @param string email : Optional parameter. The user's email address.
     * @param string dueDate : Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
     * @param string status : Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
     * @param string role : Optional parameter. The user's role, such
     * as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
     * @param string password : Optional parameter. The user's password such as "Be@gle2" (It will be automatically
     * encrypted with an MD5 hash).
     * @return $result will return an object
     */
    public function updateUser ($userUid, $userName, $firstName = null, $lastName = null, $email = null, $dueDate = null, $status = null, $role = null, $password = null)
    {
        try {
            global $RBAC;

            $RBAC->initRBAC();

            if (empty( $userUid )) {
                $result = new wsResponse( 25, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " userUid" );

                return $result;
            }

            if (empty( $userName )) {
                $result = new wsResponse( 25, G::LoadTranslation( "ID_USERNAME_REQUIRED" ) );

                return $result;
            }

            if ($RBAC->verifyUserId( $userUid ) == 0) {
                $result = new wsResponse( 3, G::loadTranslation( "ID_USER_NOT_REGISTERED_SYSTEM" ) );

                return $result;
            }

            $mktimeDueDate = 0;

            if (! empty( $dueDate )) {
                if (! preg_match( "/^(\d{4})-(\d{2})-(\d{2})$/", $dueDate, $arrayMatch )) {
                    $result = new wsResponse( - 1, G::LoadTranslation( "ID_INVALID_DATA" ) . " $dueDate" );

                    return $result;
                } else {
                    $mktimeDueDate = mktime( 0, 0, 0, intval( $arrayMatch[2] ), intval( $arrayMatch[3] ), intval( $arrayMatch[1] ) );
                }
            }

            if (! empty( $status )) {
                if ($status != "ACTIVE" && $status != "INACTIVE" && $status != "VACATION") {
                    $result = new wsResponse( - 1, G::LoadTranslation( "ID_INVALID_DATA" ) . " $status" );

                    return $result;
                }
            }

            $strRole = null;

            if (! empty( $role )) {
                $arrayRole = $RBAC->loadById( $role );

                if (is_array( $arrayRole )) {
                    $strRole = $arrayRole["ROL_CODE"];
                } else {
                    $strRole = $role;

                    if ($RBAC->verifyByCode( $role ) == 0) {
                        $data = array ();
                        $data["ROLE"] = $role;

                        $result = new wsResponse( 6, G::LoadTranslation( "ID_INVALID_ROLE", SYS_LANG, $data ) );

                        return $result;
                    }
                }
            }

            if (! empty( $password ) && strlen( $password ) > 20) {
                $result = new wsResponse( - 1, G::LoadTranslation( "ID_PASSWORD_SURPRASES" ) );

                return $result;
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn( UsersPeer::USR_UID );
            $criteria->add( UsersPeer::USR_USERNAME, $userName );
            $criteria->add( UsersPeer::USR_UID, $userUid, Criteria::NOT_EQUAL );
            $rs = UsersPeer::doSelectRS( $criteria );

            if ($rs->next()) {
                $data = array ();
                $data["USER_ID"] = $userName;

                $result = new wsResponse( 7, G::LoadTranslation( "ID_USERNAME_ALREADY_EXISTS", SYS_LANG, $data ) );

                return $result;
            }

            //Set fields
            $arrayData = array ();

            $arrayData["USR_UID"] = $userUid;
            $arrayData["USR_USERNAME"] = $userName;

            if (! empty( $firstName )) {
                $arrayData["USR_FIRSTNAME"] = $firstName;
            }

            if (! empty( $lastName )) {
                $arrayData["USR_LASTNAME"] = $lastName;
            }

            if (! empty( $email )) {
                $arrayData["USR_EMAIL"] = $email;
            }

            if ($mktimeDueDate != 0) {
                $arrayData["USR_DUE_DATE"] = $mktimeDueDate;
            }

            $arrayData["USR_UPDATE_DATE"] = date( "Y-m-d H:i:s" );

            if (! empty( $status )) {
                $arrayData["USR_STATUS"] = $status;
            }

            if ($strRole != null) {
                $arrayData["USR_ROLE"] = $strRole;
            }

            if (! empty( $password )) {
                $arrayData["USR_PASSWORD"] = md5( $password );
            }

            //Update user
            if ($strRole != null) {
                $RBAC->updateUser( $arrayData, $strRole );
            } else {
                $RBAC->updateUser( $arrayData );
            }

            $user = new Users();
            $user->update( $arrayData );

            //Response
            //$res = new wsResponse(0, G::LoadTranslation("ID_OBJECT_UPDATE"));
            $res = new wsResponse( 0, G::LoadTranslation( "ID_UPDATED_SUCCESSFULLY" ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"timestamp" => $res->timestamp
            );

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * Information User
     * @param string userUid : The user UID.
     * @return $result will return an object
     */
    public function informationUser($userUid)
    {
        try {
            if (empty($userUid)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                return $result;
            }

            $user = new Users();
            $userInfo = $user->getAllInformation($userUid);

            //Response
            $res = new wsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = new stdClass();
            $result->status_code = $res->status_code;
            $result->message     = $res->message;
            $result->timestamp   = $res->timestamp;
            $result->info = $userInfo;

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * create Group
     *
     * @param string $groupName
     * @return $result will return an object
     */
    public function createGroup ($groupName)
    {
        try {
            if (trim( $groupName ) == '') {
                $result = new wsCreateGroupResponse( 25, G::loadTranslation( 'ID_GROUP_NAME_REQUIRED' ), '' );
                return $result;
            }

            $group = new Groupwf();
            $grpRow['GRP_TITLE'] = $groupName;
            $groupId = $group->create( $grpRow );

            $data['GROUP_NAME'] = $groupName;

            $result = new wsCreateGroupResponse( 0, G::loadTranslation( 'ID_GROUP_CREATED_SUCCESSFULLY', SYS_LANG, $data ), $groupId );

            return $result;
        } catch (Exception $e) {
            $result = wsCreateGroupResponse( 100, $e->getMessage(), '' );

            return $result;
        }
    }

    /**
     * Create New Department link on the top section of the left pane allows you to create a root-level department.
     *
     * @param string $departmentName
     * @param string $parentUID
     * @return $result will return an object
     */
    public function createDepartment ($departmentName, $parentUID)
    {
        try {
            if (trim( $departmentName ) == '') {
                $result = new wsCreateDepartmentResponse( 25, G::loadTranslation( 'ID_DEPARTMENT_NAME_REQUIRED' ), '' );

                return $result;
            }

            $department = new Department();

            if (($parentUID != '') && ! ($department->existsDepartment( $parentUID ))) {
                $result = new wsCreateDepartmentResponse( 26, G::loadTranslation( 'ID_PARENT_DEPARTMENT_NOT_EXIST' ), $parentUID );

                return $result;
            }

            if ($department->checkDepartmentName( $departmentName, $parentUID )) {
                $result = new wsCreateDepartmentResponse( 27, G::loadTranslation( 'ID_DEPARTMENT_EXISTS' ), '' );

                return $result;
            }

            $row['DEP_TITLE'] = $departmentName;
            $row['DEP_PARENT'] = $parentUID;

            $departmentId = $department->create( $row );

            $data['DEPARTMENT_NAME'] = $departmentName;
            $data['PARENT_UID'] = $parentUID;
            $data['DEPARTMENT_NAME'] = $departmentName;

            $result = new wsCreateDepartmentResponse( 0, G::loadTranslation( 'ID_DEPARTMENT_CREATED_SUCCESSFULLY', SYS_LANG, $data ), $departmentId );

            return $result;
        } catch (Exception $e) {
            $result = wsCreateDepartmentResponse( 100, $e->getMessage(), '' );

            return $result;
        }
    }

    /**
     * remove user from group
     *
     * @param string $appDocUid
     * @return $result will return an object
     */
    public function removeUserFromGroup ($userId, $groupId)
    {
        try {
            G::LoadClass( 'groups' );
            global $RBAC;

            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId( $userId );

            if ($user == 0) {
                $result = new wsResponse( 3, G::loadTranslation( 'ID_USER_NOT_REGISTERED_SYSTEM' ) );

                return $result;
            }

            $groups = new Groups();
            $very_group = $groups->verifyGroup( $groupId );

            if ($very_group == 0) {
                $result = new wsResponse( 9, G::loadTranslation( 'ID_GROUP_NOT_REGISTERED_SYSTEM' ) );

                return $result;
            }

            $very_user = $groups->verifyUsertoGroup( $groupId, $userId );

            if ($very_user == 1) {
                $oGroup = new Groups();
                $oGroup->removeUserOfGroup( $groupId, $userId );
                $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULY' ) );

                return $result;
            }

            //$oGroup->removeUserOfGroup($_POST['GRP_UID'], $_POST['USR_UID']);
            $result = new wsResponse( 8, G::loadTranslation( 'ID_USER_NOT_REGISTERED_GROUP' ) );

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }

        //G::LoadClass('groups');
        //$oGroup = new Groups();
        //$oGroup->removeUserOfGroup($_POST['GRP_UID'], $_POST['USR_UID']);
    }

    /**
     * assigns a user to a group
     *
     * @param string $userId
     * @param string $groupId
     * @return $result will return an object
     */
    public function assignUserToGroup ($userId, $groupId)
    {
        try {
            global $RBAC;
            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId( $userId );

            if ($user == 0) {
                $result = new wsResponse( 3, G::loadTranslation( 'ID_USER_NOT_REGISTERED_SYSTEM' ) );
                return $result;
            }

            $groups = new Groups();
            $very_group = $groups->verifyGroup( $groupId );

            if ($very_group == 0) {
                $result = new wsResponse( 9, G::loadTranslation( 'ID_GROUP_NOT_REGISTERED_SYSTEM' ) );

                return $result;
            }

            $very_user = $groups->verifyUsertoGroup( $groupId, $userId );

            if ($very_user == 1) {
                $result = new wsResponse( 8, G::loadTranslation( 'ID_USER_ALREADY_EXISTS_GROUP' ) );

                return $result;
            }

            $groups->addUserToGroup( $groupId, $userId );
            $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULY' ) );

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * assigns user to department
     *
     * @param string $userId
     * @param string $depId
     * @param string $manager
     * @return $result will return an object
     */
    public function assignUserToDepartment ($userId, $depId, $manager)
    {
        try {
            global $RBAC;
            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId( $userId );

            if ($user == 0) {
                $result = new wsResponse( 3, G::loadTranslation( 'ID_USER_NOT_REGISTERED_SYSTEM' ) );

                return $result;
            }

            $deps = new Department();

            if (! $deps->existsDepartment( $depId )) {
                $data['DEP_ID'] = $depId;

                $result = new wsResponse( 100, G::loadTranslation( 'ID_DEPARTMENT_NOT_REGISTERED_SYSTEM', SYS_LANG, $data ) );

                return $result;
            }

            if (! $deps->existsUserInDepartment( $depId, $userId )) {
                $deps->addUserToDepartment( $depId, $userId, $manager, true );
            }

            $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULY' ) );

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * sends variables to a case
     *
     * @param string $caseId
     * @param string $variables
     * @return $result will return an object
     */
    public function sendVariables ($caseId, $variables)
    {
        //delegation where app uid (caseId) y usruid(session) ordenar delindes descendente y agaarr el primero
        //delfinishdate != null error
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
            $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );

            $oCriteria->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $cnt = 0;

            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();
                $cnt ++;
            }

            if ($cnt == 0) {
                $result = new wsResponse( 18, G::loadTranslation( 'ID_CASE_DELEGATION_ALREADY_CLOSED' ) );

                return $result;
            }

            if (is_array( $variables )) {
                $cant = count( $variables );

                if ($cant > 0) {
                    $oCase = new Cases();
                    $oldFields = $oCase->loadCase( $caseId );
                    $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $variables );
                    ob_start();
                    print_r( $variables );
                    $cdata = ob_get_contents();
                    ob_end_clean();
                    $up_case = $oCase->updateCase( $caseId, $oldFields );

                    $result = new wsResponse( 0, $cant . " " . G::loadTranslation( 'ID_VARIABLES_RECEIVED' ) . ": \n" . trim( str_replace( 'Array', '', $cdata ) ) );

                    return $result;
                } else {
                    $result = new wsResponse( 23, G::loadTranslation( 'ID_VARIABLES_PARAM_ZERO' ) );

                    return $result;
                }
            } else {
                $result = new wsResponse( 24, G::loadTranslation( 'ID_VARIABLES_PARAM_NOT_ARRAY' ) );

                return $result;
            }
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * get variables The variables can be system variables and/or case variables
     *
     * @param string $caseId
     * @param string $variables
     * @return $result will return an object
     */
    public function getVariables ($caseId, $variables)
    {
        try {
            if (is_array( $variables )) {
                $cant = count( $variables );

                if ($cant > 0) {
                    $oCase = new Cases();

                    $caseFields = $oCase->loadCase( $caseId );
                    $oldFields = $caseFields['APP_DATA'];
                    $resFields = array ();

                    foreach ($variables as $key => $val) {
                        $a .= $val->name . ', ';

                        if (isset( $oldFields[$val->name] )) {
                            if (! is_array( $oldFields[$val->name] )) {
                                $node = new stdClass();
                                $node->name = $val->name;
                                $node->value = $oldFields[$val->name];
                                $resFields[] = $node;
                            } else {
                                foreach ($oldFields[$val->name] as $gridKey => $gridRow) {
                                    //Special Variables like grids or checkgroups
                                    if (is_array( $gridRow )) {
                                        //Grids
                                        foreach ($gridRow as $col => $colValue) {
                                            $node = new stdClass();
                                            $node->name = $val->name . "][" . $gridKey . "][" . $col;
                                            $node->value = $colValue;
                                            $resFields[] = $node;
                                        }
                                    } else {
                                        //Checkgroups, Radiogroups
                                        $node = new stdClass();
                                        $node->name = $key;
                                        $node->value = implode( "|", $val );
                                        $resFields[] = $node;
                                    }
                                }
                            }
                        }
                    }

                    $result = new wsGetVariableResponse( 0, count( $resFields ) . G::loadTranslation( 'ID_VARIABLES_SENT' ), $resFields );

                    return $result;
                } else {
                    $result = new wsGetVariableResponse( 23, G::loadTranslation( 'ID_VARIABLES_PARAM_ZERO' ), null );

                    return $result;
                }
            } else {
                $result = new wsGetVariableResponse( 24, G::loadTranslation( 'ID_VARIABLES_PARAM_NOT_ARRAY' ), null );
                return $result;
            }
        } catch (Exception $e) {
            $result = new wsGetVariableResponse( 100, $e->getMessage(), null );

            return $result;
        }
    }

    /**
     * get all variables the system and case selected
     *
     * @param string $caseId
     *
     * @return $result will return an object
     */
    public function getVariablesNames ($caseId)
    {
        try {

            $oCase = new Cases();

            $caseFields = $oCase->loadCase( $caseId );

            $oldFields = $caseFields['APP_DATA'];
            $resFields = array ();

            foreach ($oldFields as $key => $val) {
                $node = new stdClass();
                $node->name = $key;
                $resFields[] = $node;
            }

            $result = new wsGetVariableResponse( 0, count( $resFields ) . G::loadTranslation( 'ID_VARIABLES_SENT' ), $resFields );

            return $result;

        } catch (Exception $e) {
            $result = new wsGetVariableResponse( 100, $e->getMessage(), null );

            return $result;
        }
    }

    /**
     * new Case begins a new case under the name of the logged-in user.
     *
     * @param string $processId
     * @param string $userId
     * @param string $taskId
     * @param string $variables
     * @return $result will return an object
     */
    public function newCase ($processId, $userId, $taskId, $variables)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["PROCESS"] = $processId;
            $_SESSION["TASK"] = $taskId;
            $_SESSION["USER_LOGGED"] = $userId;

            $Fields = array ();

            if (is_array( $variables ) && count( $variables ) > 0) {
                $Fields = $variables;
            }

            $oProcesses = new Processes();
            $pro = $oProcesses->processExists( $processId );

            if (! $pro) {
                $result = new wsResponse( 11, G::loadTranslation( 'ID_INVALID_PROCESS' ) . " " . $processId );

                $g->sessionVarRestore();

                return $result;
            }

            $oCase = new Cases();
            $oTask = new Tasks();
            $startingTasks = $oCase->getStartCases( $userId );
            array_shift( $startingTasks ); //remove the first row, the header row
            $founded = '';
            $tasksInThisProcess = 0;
            $validTaskId = $taskId;

            foreach ($startingTasks as $key => $val) {
                if ($val['pro_uid'] == $processId) {
                    $tasksInThisProcess ++;
                    $validTaskId = $val['uid'];
                }

                if ($val['uid'] == $taskId) {
                    $founded = $val['value'];
                }
            }

            if ($taskId == '') {
                if ($tasksInThisProcess == 1) {
                    $founded = $validTaskId;
                    $taskId = $validTaskId;
                }

                if ($tasksInThisProcess > 1) {
                    $result = new wsResponse( 13, G::loadTranslation( 'ID_MULTIPLE_STARTING_TASKS' ) );

                    $g->sessionVarRestore();

                    return $result;
                }
            }

            if ($founded == '') {
                $result = new wsResponse( 14, G::loadTranslation( 'ID_TASK_INVALID_USER_NOT_ASSIGNED_TASK' ) );

                $g->sessionVarRestore();

                return $result;
            }

            $case = $oCase->startCase( $taskId, $userId );

            $_SESSION['APPLICATION'] = $case['APPLICATION'];
            $_SESSION['PROCESS'] = $case['PROCESS'];
            $_SESSION['TASK'] = $taskId;
            $_SESSION['INDEX'] = $case['INDEX'];
            $_SESSION['USER_LOGGED'] = $userId;
            $_SESSION['USR_USERNAME'] = $case['USR_USERNAME'];
            $_SESSION['STEP_POSITION'] = 0;

            $caseId = $case['APPLICATION'];
            $caseNr = $case['CASE_NUMBER'];

            $oldFields = $oCase->loadCase( $caseId );

            $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields );

            $up_case = $oCase->updateCase( $caseId, $oldFields );

            $result = new wsResponse( 0, G::loadTranslation( 'ID_STARTED_SUCCESSFULLY' ) );
            $result->caseId = $caseId;
            $result->caseNumber = $caseNr;

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * creates a new case impersonating a user who has the proper privileges to create new cases
     *
     * @param string $processId
     * @param string $userId
     * @param string $variables
     * @return $result will return an object
     */
    public function newCaseImpersonate ($processId, $userId, $variables)
    {
        try {
            if (is_array( $variables )) {
                if (count( $variables ) > 0) {
                    $c = count( $variables );
                    $Fields = $variables;

                    if ($c == 0) {
                        //Si no tenenmos ninguna variables en el array variables.
                        $result = new wsResponse( 10, G::loadTranslation( 'ID_ARRAY_VARIABLES_EMPTY' ) );

                        return $result;
                    }
                }
            } else {
                $result = new wsResponse( 10, G::loadTranslation( 'ID_VARIABLES_PARAM_NOT_ARRAY' ) );

                return $result;
            }

            $processes = new Processes();

            if (! $processes->processExists( $processId )) {
                $result = new wsResponse( 11, G::loadTranslation( 'ID_INVALID_PROCESS' ) . " " . $processId . "!!" );

                return $result;
            }

            $user = new Users();

            if (! $user->userExists( $userId )) {
                $result = new wsResponse( 11, G::loadTranslation( 'ID_USER_NOT_REGISTERED' ) . " " . $userId . "!!" );

                return $result;
            }

            $oCase = new Cases();

            $arrayTask = $processes->getStartingTaskForUser( $processId, null );
            $numTasks = count( $arrayTask );

            if ($numTasks == 1) {
                $case = $oCase->startCase( $arrayTask[0]['TAS_UID'], $userId );
                $caseId = $case['APPLICATION'];
                $caseNumber = $case['CASE_NUMBER'];

                $oldFields = $oCase->loadCase( $caseId );

                $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields );

                $up_case = $oCase->updateCase( $caseId, $oldFields );

                $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULLY' ) );

                $result->caseId = $caseId;
                $result->caseNumber = $caseNumber;

                return $result;
            } else {
                if ($numTasks == 0) {
                    $result = new wsResponse( 12, G::loadTranslation( 'ID_NO_STARTING_TASK' ) );

                    return $result;
                }

                if ($numTasks > 1) {
                    $result = new wsResponse( 13, G::loadTranslation( 'ID_MULTIPLE_STARTING_TASKS' ) );

                    return $result;
                }
            }
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * derivate Case moves the case to the next task in the process according to the routing rules
     *
     * @param string $userId
     * @param string $caseId
     * @param string $delIndex
     * @return $result will return an object
     */
    public function derivateCase ($userId, $caseId, $delIndex, $bExecuteTriggersBeforeAssignment = false)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userId;

            $sStatus = 'TO_DO';

            $varResponse = '';
            $varTriggers = "\n";

            if ($delIndex == '') {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
                $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
                $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );

                if (AppDelegationPeer::doCount( $oCriteria ) > 1) {
                    $result = new wsResponse( 20, G::loadTranslation( 'ID_SPECIFY_DELEGATION_INDEX' ) );
                    return $result;
                }

                $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
                $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $oDataset->next();
                $aRow = $oDataset->getRow();
                $delIndex = $aRow['DEL_INDEX'];
            }

            $oAppDel = new AppDelegation();
            $appdel = $oAppDel->Load( $caseId, $delIndex );

            if ($userId != $appdel['USR_UID']) {
                $result = new wsResponse( 17, G::loadTranslation( 'ID_CASE_ASSIGNED_ANOTHER_USER' ) );

                return $result;
            }

            if ($appdel['DEL_FINISH_DATE'] != null) {
                $result = new wsResponse( 18, G::loadTranslation( 'ID_CASE_DELEGATION_ALREADY_CLOSED' ) );

                return $result;
            }

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelayPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelayPeer::APP_DEL_INDEX );
            $oCriteria->add( AppDelayPeer::APP_TYPE, '' );
            $oCriteria->add( $oCriteria->getNewCriterion( AppDelayPeer::APP_TYPE, 'PAUSE' )->addOr( $oCriteria->getNewCriterion( AppDelayPeer::APP_TYPE, 'CANCEL' ) ) );
            $oCriteria->addAscendingOrderByColumn( AppDelayPeer::APP_ENABLE_ACTION_DATE );
            $oDataset = AppDelayPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (is_array( $aRow )) {
                if (isset( $aRow['APP_DISABLE_ACTION_USER'] ) && $aRow['APP_DISABLE_ACTION_USER'] != 0 && isset( $aRow['APP_DISABLE_ACTION_DATE'] ) && $aRow['APP_DISABLE_ACTION_DATE'] != '') {
                    $result = new wsResponse( 19, G::loadTranslation( 'ID_CASE_IN_STATUS' ) . " " . $aRow['APP_TYPE'] );

                    return $result;
                }
            }

            $aData['APP_UID'] = $caseId;
            $aData['DEL_INDEX'] = $delIndex;
            $aData['USER_UID'] = $userId;

            //Load data
            $oCase = new Cases();
            $appFields = $oCase->loadCase( $caseId, $delIndex );

            if (is_null( $appFields["DEL_INIT_DATE"] )) {
                $oCase->setDelInitDate( $caseId, $delIndex );
                $appFields = $oCase->loadCase( $caseId, $delIndex );
            }

            $appFields["APP_DATA"]["APPLICATION"] = $caseId;

            if (! isset( $_SESSION["PROCESS"] )) {
                $_SESSION["PROCESS"] = $appFields["PRO_UID"];
            }

            if ($bExecuteTriggersBeforeAssignment) {
                //Execute triggers before assignment
                $aTriggers = $oCase->loadTriggers( $appdel['TAS_UID'], 'ASSIGN_TASK', - 1, 'BEFORE' );

                if (count( $aTriggers ) > 0) {
                    $oPMScript = new PMScript();

                    foreach ($aTriggers as $aTrigger) {
                        //$appFields = $oCase->loadCase( $caseId );
                        //$appFields['APP_DATA']['APPLICATION'] = $caseId;


                        //Set variables
                        $params = new stdClass();
                        $params->appData = $appFields["APP_DATA"];

                        if ($this->stored_system_variables) {
                            $params->option = "STORED SESSION";
                            $params->SID = $this->wsSessionId;
                        }

                        $appFields["APP_DATA"] = array_merge( $appFields["APP_DATA"], G::getSystemConstants( $params ) );

                        //PMScript
                        $oPMScript->setFields( $appFields['APP_DATA'] );
                        $bExecute = true;

                        if ($aTrigger['ST_CONDITION'] !== '') {
                            $oPMScript->setScript( $aTrigger['ST_CONDITION'] );
                            $bExecute = $oPMScript->evaluate();
                        }

                        if ($bExecute) {
                            $oPMScript->setScript( $aTrigger['TRI_WEBBOT'] );
                            $oPMScript->execute();

                            $varTriggers .= "<br/><b>-= Before Assignment =-</b><br/>" . nl2br( htmlentities( $aTrigger['TRI_WEBBOT'], ENT_QUOTES ) ) . "<br/>";

                            //$appFields = $oCase->loadCase( $caseId );
                            $appFields['APP_DATA'] = $oPMScript->aFields;
                            $oCase->updateCase( $caseId, $appFields );
                        }
                    }
                }
            }

            //Execute triggers before derivation
            $aTriggers = $oCase->loadTriggers( $appdel['TAS_UID'], 'ASSIGN_TASK', - 2, 'BEFORE' );

            if (count( $aTriggers ) > 0) {
                $varTriggers .= "<b>-= Before Derivation =-</b><br/>";

                $oPMScript = new PMScript();

                foreach ($aTriggers as $aTrigger) {
                    //$appFields = $oCase->loadCase( $caseId );
                    //$appFields['APP_DATA']['APPLICATION'] = $caseId;


                    //Set variables
                    $params = new stdClass();
                    $params->appData = $appFields["APP_DATA"];

                    if ($this->stored_system_variables) {
                        $params->option = "STORED SESSION";
                        $params->SID = $this->wsSessionId;
                    }

                    $appFields["APP_DATA"] = array_merge( $appFields["APP_DATA"], G::getSystemConstants( $params ) );

                    //PMScript
                    $oPMScript->setFields( $appFields['APP_DATA'] );
                    $bExecute = true;

                    if ($aTrigger['ST_CONDITION'] !== '') {
                        $oPMScript->setScript( $aTrigger['ST_CONDITION'] );
                        $bExecute = $oPMScript->evaluate();
                    }

                    if ($bExecute) {
                        $oPMScript->setScript( $aTrigger['TRI_WEBBOT'] );
                        $oPMScript->execute();

                        $oTrigger = TriggersPeer::retrieveByPk( $aTrigger['TRI_UID'] );
                        $varTriggers .= "&nbsp;- " . nl2br( htmlentities( $oTrigger->getTriTitle(), ENT_QUOTES ) ) . "<br/>";
                        //$appFields = $oCase->loadCase( $caseId );
                        $appFields['APP_DATA'] = $oPMScript->aFields;
                        //$appFields['APP_DATA']['APPLICATION'] = $caseId;
                        $oCase->updateCase( $caseId, $appFields );
                    }
                }
            }

            $oDerivation = new Derivation();
            $derive = $oDerivation->prepareInformation( $aData );

            if (isset( $derive[1] )) {
                if ($derive[1]['ROU_TYPE'] == 'SELECT') {
                    $result = new wsResponse( 21, G::loadTranslation( 'ID_CAN_NOT_ROUTE_CASE_USING_WEBSERVICES' ) );

                    return $result;
                }
            } else {
                $result = new wsResponse( 22, G::loadTranslation( 'ID_TASK_DOES_NOT_HAVE_ROUTING_RULE' ) );

                return $result;
            }

            foreach ($derive as $key => $val) {
                if ($val['NEXT_TASK']['TAS_ASSIGN_TYPE'] == 'MANUAL') {
                    $result = new wsResponse( 15, G::loadTranslation( 'ID_TASK_DEFINED_MANUAL_ASSIGNMENT' ) );

                    return $result;
                }

                //Routed to the next task, if end process then not exist user
                $nodeNext = array ();
                $usrasgdUid = null;
                $usrasgdUserName = null;

                if (isset( $val['NEXT_TASK']['USER_ASSIGNED'] )) {
                    $usrasgdUid = $val['NEXT_TASK']['USER_ASSIGNED']['USR_UID'];
                    $usrasgdUserName = '(' . $val['NEXT_TASK']['USER_ASSIGNED']['USR_USERNAME'] . ')';
                }

                $nodeNext['TAS_UID'] = $val['NEXT_TASK']['TAS_UID'];
                $nodeNext['USR_UID'] = $usrasgdUid;
                $nodeNext['TAS_ASSIGN_TYPE'] = $val['NEXT_TASK']['TAS_ASSIGN_TYPE'];
                $nodeNext['TAS_DEF_PROC_CODE'] = $val['NEXT_TASK']['TAS_DEF_PROC_CODE'];
                $nodeNext['DEL_PRIORITY'] = $appdel['DEL_PRIORITY'];
                $nodeNext['TAS_PARENT'] = $val['NEXT_TASK']['TAS_PARENT'];

                $nextDelegations[] = $nodeNext;
                $varResponse = $varResponse . (($varResponse != '') ? ',' : '') . $val['NEXT_TASK']['TAS_TITLE'] . $usrasgdUserName;
            }

            $appFields['DEL_INDEX'] = $delIndex;

            if (isset( $derive['TAS_UID'] )) {
                $appFields['TAS_UID'] = $derive['TAS_UID'];
            }

            //Save data - Start
            //$appFields = $oCase->loadCase( $caseId );
            //$oCase->updateCase ( $caseId, $appFields );
            //Save data - End


            $row = array ();
            $oCriteria = new Criteria( 'workflow' );
            $del = DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn( RoutePeer::ROU_TYPE );
            $oCriteria->addSelectColumn( RoutePeer::ROU_NEXT_TASK );
            $oCriteria->add( RoutePeer::TAS_UID, $appdel['TAS_UID'] );
            $oDataset = TaskPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $row[] = array ('ROU_TYPE' => $aRow['ROU_TYPE'],'ROU_NEXT_TASK' => $aRow['ROU_NEXT_TASK']
                );
                $oDataset->next();
            }

            //derivate case
            $aCurrentDerivation = array ('APP_UID' => $caseId,'DEL_INDEX' => $delIndex,'APP_STATUS' => $sStatus,'TAS_UID' => $appdel['TAS_UID'],'ROU_TYPE' => $row[0]['ROU_TYPE']
            );

            $oDerivation->derivate( $aCurrentDerivation, $nextDelegations );
            $appFields = $oCase->loadCase( $caseId );

            //Execute triggers after derivation
            $aTriggers = $oCase->loadTriggers( $appdel['TAS_UID'], 'ASSIGN_TASK', - 2, 'AFTER' );

            if (count( $aTriggers ) > 0) {
                //$appFields['APP_DATA']['APPLICATION'] = $caseId;


                //Set variables
                $params = new stdClass();
                $params->appData = $appFields["APP_DATA"];

                if ($this->stored_system_variables) {
                    $params->option = "STORED SESSION";
                    $params->SID = $this->wsSessionId;
                }

                $appFields["APP_DATA"] = array_merge( $appFields["APP_DATA"], G::getSystemConstants( $params ) );

                //PMScript
                $oPMScript = new PMScript();
                $oPMScript->setFields( $appFields['APP_DATA'] );

                $varTriggers .= "<b>-= After Derivation =-</b><br/>";

                foreach ($aTriggers as $aTrigger) {
                    $bExecute = true;

                    if ($aTrigger['ST_CONDITION'] !== '') {
                        $oPMScript->setScript( $aTrigger['ST_CONDITION'] );
                        $bExecute = $oPMScript->evaluate();
                    }

                    if ($bExecute) {
                        $oPMScript->setScript( $aTrigger['TRI_WEBBOT'] );
                        $oPMScript->execute();

                        $oTrigger = TriggersPeer::retrieveByPk( $aTrigger['TRI_UID'] );
                        $varTriggers .= "&nbsp;- " . nl2br( htmlentities( $oTrigger->getTriTitle(), ENT_QUOTES ) ) . "<br/>";
                        //$appFields = $oCase->loadCase($caseId);
                        $appFields['APP_DATA'] = $oPMScript->aFields;
                        //$appFields['APP_DATA']['APPLICATION'] = $caseId;
                        //$appFields = $oCase->loadCase($caseId);
                        $oCase->updateCase( $caseId, $appFields );
                    }
                }
            }

            $oUser = new Users();
            $aUser = $oUser->load( $userId );

            if (trim( $aUser['USR_EMAIL'] ) == '') {
                $aUser['USR_EMAIL'] = 'info@' . $_SERVER['HTTP_HOST'];
            }

            $sFromName = '"' . $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . '" <' . $aUser['USR_EMAIL'] . '>';
            $oCase->sendNotifications( $appdel['TAS_UID'], $nextDelegations, $appFields['APP_DATA'], $caseId, $delIndex, $sFromName );

            //Save data - Start
            //$appFields = $oCase->loadCase($caseId);
            //$oCase->updateCase($caseId, $appFields);
            //Save data - End


            $oProcess = new Process();
            $oProcessFieds = $oProcess->Load( $appFields['PRO_UID'] );
            //here dubug mode in web entry


            if (isset( $oProcessFieds['PRO_DEBUG'] ) && $oProcessFieds['PRO_DEBUG']) {
                $result = new wsResponse( 0, $varResponse . "
                        <br><br><table width='100%' cellpadding='0' cellspacing='0'><tr><td class='FormTitle'>
                    " . G::LoadTranslation( 'ID_DEBUG_MESSAGE' ) . "</td></tr></table>" . $varTriggers );
            } else {
                $result = new wsResponse( 0, $varResponse . " --- " . $oProcessFieds['PRO_DEBUG'] );
            }

            $res = $result->getPayloadArray();

            //now fill the array of AppDelegationPeer
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::USR_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_THREAD );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_THREAD_STATUS );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
            $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
            $oCriteria->add( AppDelegationPeer::DEL_PREVIOUS, $delIndex );
            $oCriteria->addAscendingOrderByColumn( AppDelegationPeer::DEL_INDEX );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $aCurrentUsers = array ();

            while ($oDataset->next()) {
                $aAppDel = $oDataset->getRow();

                $oUser = new Users();

                try {
                    $oUser->load( $aAppDel['USR_UID'] );
                    $uFields = $oUser->toArray( BasePeer::TYPE_FIELDNAME );
                    $currentUserName = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
                } catch (Exception $e) {
                    $currentUserName = '';
                }

                $oTask = new Task();

                try {
                    $uFields = $oTask->load( $aAppDel['TAS_UID'] );
                    $taskName = $uFields['TAS_TITLE'];
                } catch (Exception $e) {
                    $taskName = '';
                }

                // execute events
                $eventPro = $_SESSION["PROCESS"];
                $eventApp = $caseId;
                $eventInd = $aAppDel['DEL_INDEX'];
                $eventTas = $aAppDel['TAS_UID'];

                $oEvent = new Event();
                $oEvent->createAppEvents( $eventPro, $eventApp, $eventInd, $eventTas );
                // end events


                $currentUser = new stdClass();
                $currentUser->userId = $aAppDel['USR_UID'];
                $currentUser->userName = $currentUserName;
                $currentUser->taskId = $aAppDel['TAS_UID'];
                $currentUser->taskName = $taskName;
                $currentUser->delIndex = $aAppDel['DEL_INDEX'];
                $currentUser->delThread = $aAppDel['DEL_THREAD'];
                $currentUser->delThreadStatus = $aAppDel['DEL_THREAD_STATUS'];
                $aCurrentUsers[] = $currentUser;
            }

            $res['routing'] = $aCurrentUsers;

            $g->sessionVarRestore();

            return $res;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * execute Trigger, executes a ProcessMaker trigger.
     * Note that triggers which are tied to case derivation
     * will executing automatically.
     *
     * @param string $userId
     * @param string $caseId
     * @param string $delIndex
     * @return $result will return an object
     */
    public function executeTrigger ($userId, $caseId, $triggerIndex, $delIndex)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userId;

            $oAppDel = new AppDelegation();
            $appdel = $oAppDel->Load( $caseId, $delIndex );

            if ($userId != $appdel['USR_UID']) {
                $result = new wsResponse( 17, G::loadTranslation( 'ID_CASE_ASSIGNED_ANOTHER_USER' ) );

                $g->sessionVarRestore();

                return $result;
            }

            if ($appdel['DEL_FINISH_DATE'] != null) {
                $result = new wsResponse( 18, G::loadTranslation( 'ID_CASE_DELEGATION_ALREADY_CLOSED' ) );

                $g->sessionVarRestore();

                return $result;
            }

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelayPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelayPeer::APP_DEL_INDEX );
            $oCriteria->add( AppDelayPeer::APP_TYPE, '' );
            $oCriteria->add( $oCriteria->getNewCriterion( AppDelayPeer::APP_TYPE, 'PAUSE' )->addOr( $oCriteria->getNewCriterion( AppDelayPeer::APP_TYPE, 'CANCEL' ) ) );
            $oCriteria->addAscendingOrderByColumn( AppDelayPeer::APP_ENABLE_ACTION_DATE );
            $oDataset = AppDelayPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (is_array( $aRow )) {
                if ($aRow['APP_DISABLE_ACTION_USER'] != 0 && $aRow['APP_DISABLE_ACTION_DATE'] != '') {
                    $result = new wsResponse( 19, G::loadTranslation( 'ID_CASE_IN_STATUS' ) . " " . $aRow['APP_TYPE'] );

                    $g->sessionVarRestore();

                    return $result;
                }
            }

            //Load data
            $oCase = new Cases();
            $appFields = $oCase->loadCase( $caseId );

            $appFields["APP_DATA"]["APPLICATION"] = $caseId;

            if (! isset( $_SESSION["PROCESS"] )) {
                $_SESSION["PROCESS"] = $appFields["PRO_UID"];
            }

            //executeTrigger
            $aTriggers = array ();
            $c = new Criteria();
            $c->add( TriggersPeer::TRI_UID, $triggerIndex );
            $rs = TriggersPeer::doSelectRS( $c );
            $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $rs->next();
            $row = $rs->getRow();

            if (is_array( $row ) && $row['TRI_TYPE'] == 'SCRIPT') {
                $aTriggers[] = $row;

                $oPMScript = new PMScript();
                $oPMScript->setFields( $appFields['APP_DATA'] );
                $oPMScript->setScript( $row['TRI_WEBBOT'] );
                $oPMScript->execute();

                //Save data - Start
                $appFields['APP_DATA'] = $oPMScript->aFields;
                //$appFields = $oCase->loadCase($caseId);
                $oCase->updateCase( $caseId, $appFields );
                //Save data - End
            } else {
                $data['TRIGGER_INDEX'] = $triggerIndex;
                $result = new wsResponse( 100, G::loadTranslation( 'ID_INVALID_TRIGGER', SYS_LANG, $data ) );

                $g->sessionVarRestore();

                return $result;
            }

            $result = new wsResponse( 0, G::loadTranslation( 'ID_EXECUTED' ) . ": " . trim( $row['TRI_WEBBOT'] ) );
            //$result = new wsResponse(0, 'executed: ' . print_r($oPMScript, 1));

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * task Case
     *
     * @param string sessionId : The session ID which is obtained when logging in
     * @param string caseId : The case ID. The caseList() function can be used to find the ID number for cases
     * @return $result returns the current task for a given case. Note that the logged-in user must have privileges
     * to access the task
     */
    public function taskCase ($caseId)
    {
        $result = array ();
        try {
            $oCriteria = new Criteria( 'workflow' );
            $del = DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );

            $oCriteria->addAsColumn( 'TAS_TITLE', 'C1.CON_VALUE' );
            $oCriteria->addAlias( "C1", 'CONTENT' );
            $tasTitleConds = array ();
            $tasTitleConds[] = array (AppDelegationPeer::TAS_UID,'C1.CON_ID');
            $tasTitleConds[] = array ('C1.CON_CATEGORY',$del . 'TAS_TITLE' . $del);
            $tasTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del);
            $oCriteria->addJoinMC( $tasTitleConds, Criteria::LEFT_JOIN );

            $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
            $oCriteria->add( AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN' );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array (
                    'guid'     => $aRow['TAS_UID'],
                    'name'     => $aRow['TAS_TITLE'],
                    'delegate' => $aRow['DEL_INDEX']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * process list verified
     *
     * @param string sessionId : The session ID which is obtained when logging in
     * @param string userId :
     * @return $result will return an object
     */
    public function processListVerified ($userId)
    {
        try {
            $oCase = new Cases();
            $rows = $oCase->getStartCases( $userId );
            $result = array ();

            foreach ($rows as $key => $val) {
                if ($key != 0) {
                    $result[] = array ('guid' => $val['pro_uid'],'name' => $val['value']
                    );
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * reassign Case
     *
     * @param string sessionId : The session ID (which was obtained during login)
     * @param string caseId : The case ID (which can be obtained with the caseList() function)
     * @param string delIndex : The delegation index number of the case (which can be obtained with the caseList()
     * function).
     * @param string userIdSource : The user who is currently assigned the case.
     * @param string userIdTarget : The target user who will be newly assigned to the case.
     * @return $result will return an object
     */
    public function reassignCase ($sessionId, $caseId, $delIndex, $userIdSource, $userIdTarget)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userIdSource;

            if ($userIdTarget == $userIdSource) {
                $result = new wsResponse( 30, G::loadTranslation( 'ID_TARGET_ORIGIN_USER_SAME' ) );

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 1 )*****************
             */
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            $oCriteria->add( UsersPeer::USR_UID, $userIdSource );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (! is_array( $aRow )) {
                $result = new wsResponse( 31, G::loadTranslation( 'ID_INVALID_ORIGIN_USER' ) );

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 2 )*****************
             */
            $oCase = new Cases();
            $rows = $oCase->loadCase( $caseId );

            if (! is_array( $aRow )) {
                $result = new wsResponse( 32, G::loadTranslation( 'ID_CASE_NOT_OPEN' ) );

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 3 )*****************
             */
            $oCriteria = new Criteria( 'workflow' );
            $aConditions = array ();
            // $aConditions[] = array(AppDelegationPeer::USR_UID, TaskUserPeer::USR_UID);
            // $aConditions[] = array(AppDelegationPeer::TAS_UID, TaskUserPeer::TAS_UID);
            // $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
            //$oCriteria->addJoin(AppDelegationPeer::USR_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN);
            $oCriteria->add( AppDelegationPeer::APP_UID, $caseId );
            $oCriteria->add( AppDelegationPeer::USR_UID, $userIdSource );
            $oCriteria->add( AppDelegationPeer::DEL_INDEX, $delIndex );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (! is_array( $aRow )) {
                $result = new wsResponse( 33, G::loadTranslation( 'ID_INVALID_CASE_DELEGATION_INDEX' ) );

                $g->sessionVarRestore();

                return $result;
            }

            $tasUid = $aRow['TAS_UID'];
            $derivation = new Derivation();
            $userList = $derivation->getAllUsersFromAnyTask( $tasUid );

            if (! in_array( $userIdTarget, $userList )) {
                $result = new wsResponse( 34, G::loadTranslation( 'ID_TARGET_USER_DOES_NOT_HAVE_RIGHTS' ) );

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 4 )*****************
             */
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            $oCriteria->add( UsersPeer::USR_UID, $userIdTarget );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (! is_array( $aRow )) {
                $result = new wsResponse( 35, G::loadTranslation( 'ID_TARGET_USER_DESTINATION_INVALID' ) );

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 5 )*****************
             */
            $var = $oCase->reassignCase( $caseId, $delIndex, $userIdSource, $userIdTarget );

            if (! $var) {
                $result = new wsResponse( 36, G::loadTranslation( 'ID_CASE_COULD_NOT_REASSIGNED' ) );

                $g->sessionVarRestore();

                return $result;
            }

            $result = new wsResponse( 0, G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULLY' ) );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result[] = array ('guid' => $e->getMessage(),'name' => $e->getMessage()
            );

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * get system information
     *
     * @param string sessionId : The session ID (which was obtained at login)
     * @return $eturns information about the WAMP/LAMP stack, the workspace database, the IP number and version
     * of ProcessMaker, and the IP number and version of web browser of the user
     */
    public function systemInformation ()
    {
        try {
            define( 'SKIP_RENDER_SYSTEM_INFORMATION', true );

            require_once (PATH_METHODS . 'login' . PATH_SEP . 'dbInfo.php');

            $result->status_code = 0;
            $result->message = G::loadTranslation( 'ID_SUCESSFUL' );
            $result->timestamp = date( 'Y-m-d H:i:s' );
            G::LoadClass( "system" );
            $result->version = System::getVersion();
            $result->operatingSystem = $redhat;
            $result->webServer = getenv( 'SERVER_SOFTWARE' );
            $result->serverName = getenv( 'SERVER_NAME' );
            $result->serverIp = $Fields['IP']; //lookup ($ip);
            $result->phpVersion = phpversion();
            $result->databaseVersion = $Fields['DATABASE'];
            $result->databaseServerIp = $Fields['DATABASE_SERVER'];
            $result->databaseName = $Fields['DATABASE_NAME'];
            $result->availableDatabases = $Fields['AVAILABLE_DB'];
            $result->userBrowser = $Fields['HTTP_USER_AGENT'];
            $result->userIp = $Fields['IP'];

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * import process fromLibrary: downloads and imports a process from the ProcessMaker library
     *
     * @param string sessionId : The session ID (which was obtained at login).
     * @param string processId :
     * @param string version :
     * @param string importOption :
     * @param string usernameLibrary : The username to obtain access to the ProcessMaker library.
     * @param string passwordLibrary : The password to obtain access to the ProcessMaker library.
     * @return $eturns will return an object
     */
    public function importProcessFromLibrary ($processId, $version = '', $importOption = '', $usernameLibrary = '', $passwordLibrary = '')
    {
        try {
            G::LoadClass( 'processes' );
            //$versionReq = $_GET['v'];
            //. (isset($_GET['s']) ? '&s=' . $_GET['s'] : '')
            $ipaddress = $_SERVER['REMOTE_ADDR'];
            $oProcesses = new Processes();
            $oProcesses->ws_open_public();
            $oProcess = $oProcesses->ws_processGetData( $processId );

            if ($oProcess->status_code != 0) {
                throw (new Exception( $oProcess->message ));
            }

            $privacy = $oProcess->privacy;

            $strSession = '';

            if ($privacy != 'FREE') {
                global $sessionId;
                $antSession = $sessionId;
                $oProcesses->ws_open( $usernameLibrary, $passwordLibrary );
                $strSession = "&s=" . $sessionId;
                $sessionId = $antSession;
            }

            //downloading the file
            $localPath = PATH_DOCUMENT . 'input' . PATH_SEP;
            G::mk_dir( $localPath );
            $newfilename = G::GenerateUniqueId() . '.pm';

            $downloadUrl = PML_DOWNLOAD_URL . '?id=' . $processId . $strSession;

            $oProcess = new Processes();
            $oProcess->downloadFile( $downloadUrl, $localPath, $newfilename );

            //getting the ProUid from the file recently downloaded
            $oData = $oProcess->getProcessData( $localPath . $newfilename );

            if (is_null( $oData )) {
                $data['DOWNLOAD_URL'] = $downloadUrl;
                $data['LOCAL_PATH'] = $localPath;
                $data['NEW_FILENAME'] = $newfilename;

                throw new Exception( G::loadTranslation( 'ID_ERROR_URL_PROCESS_INVALID', SYS_LANG, $data ) );
            }

            $sProUid = $oData->process['PRO_UID'];
            $oData->process['PRO_UID_OLD'] = $sProUid;

            //if the process exists, we need to check the $importOption to and re-import if the user wants,
            if ($oProcess->processExists( $sProUid )) {
                //Update the current Process, overwriting all tasks and steps
                if ($importOption == 1) {
                    $oProcess->updateProcessFromData( $oData, $localPath . $newfilename );
                    //delete the xmlform cache


                    if (file_exists( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid )) {
                        $oDirectory = dir( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid );

                        while ($sObjectName = $oDirectory->read()) {
                            if (($sObjectName != '.') && ($sObjectName != '..')) {
                                $strAux = PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP;
                                $strAux = $strAux . $sProUid . PATH_SEP . $sObjectName;

                                unlink( $strAux );
                            }
                        }

                        $oDirectory->close();
                    }

                    $sNewProUid = $sProUid;
                }

                //Disable current Process and create a new version of the Process
                if ($importOption == 2) {
                    $oProcess->disablePreviousProcesses( $sProUid );
                    $sNewProUid = $oProcess->getUnusedProcessGUID();
                    $oProcess->setProcessGuid( $oData, $sNewProUid );
                    $oProcess->setProcessParent( $oData, $sProUid );
                    $oData->process['PRO_TITLE'] = "New - " . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );
                    $oProcess->renewAll( $oData );
                    $oProcess->createProcessFromData( $oData, $localPath . $newfilename );
                }

                //Create a completely new Process without change the current Process
                if ($importOption == 3) {
                    //krumo ($oData); die;
                    $sNewProUid = $oProcess->getUnusedProcessGUID();
                    $oProcess->setProcessGuid( $oData, $sNewProUid );

                    $strAux = "Copy of  - " . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );

                    $oData->process['PRO_TITLE'] = $strAux;
                    $oProcess->renewAll( $oData );
                    $oProcess->createProcessFromData( $oData, $localPath . $newfilename );
                }

                if ($importOption != 1 && $importOption != 2 && $importOption != 3) {
                    throw new Exception( G::loadTranslation( 'ID_PROCESS_ALREADY_IN_SYSTEM' ) );
                }
            }

            //finally, creating the process if the process doesn't exist
            if (! $oProcess->processExists( $processId )) {
                $oProcess->createProcessFromData( $oData, $localPath . $newfilename );
            }

            //show the info after the imported process
            $oProcess = new Processes();
            $oProcess->ws_open_public();
            $processData = $oProcess->ws_processGetData( $processId );

            $result->status_code = 0;
            $result->message = G::loadTranslation( 'ID_COMMAND_EXECUTED_SUCCESSFULLY' );
            $result->timestamp = date( 'Y-m-d H:i:s' );
            $result->processId = $processId;
            $result->processTitle = $processData->title;
            $result->category = (isset( $processData->category ) ? $processData->category : '');
            $result->version = $processData->version;

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    public function getCaseNotes ($applicationID, $userUid = '')
    {
        try {
            G::LoadClass( 'case' );

            $result = new wsGetCaseNotesResponse( 0, G::loadTranslation( 'ID_SUCCESS' ), Cases::getCaseNotes( $applicationID, 'array', $userUid ) );

            $var = array ();

            foreach ($result->notes as $key => $value) {
                $var2 = array ();

                foreach ($value as $keys => $values) {
                    $field = strtolower( $keys );
                    $var2[$field] = $values;
                }

                $var[] = $var2;
            }

            $result->notes = $var;

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse( 100, $e->getMessage() );

            return $result;
        }
    }

    /**
     * Delete case
     *
     * @param string caseUid : ID of the case.
     * @return $result will return an object
     */
    public function deleteCase ($caseUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;

            if (empty( $caseUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " caseUid" );

                $g->sessionVarRestore();

                return $result;
            }

            $case = new Cases();
            $case->removeCase( $caseUid );

            //Response
            $res = new wsResponse( 0, G::LoadTranslation( "ID_COMMAND_EXECUTED_SUCCESSFULLY" ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Cancel case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will cancel the case.
     * @return $result will return an object
     */
    public function cancelCase ($caseUid, $delIndex, $userUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty( $caseUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " caseUid" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $delIndex )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " delIndex" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $userUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " userUid" );

                $g->sessionVarRestore();

                return $result;
            }

            $case = new Cases();
            $case->cancelCase( $caseUid, $delIndex, $userUid );

            //Response
            $res = new wsResponse( 0, G::LoadTranslation( "ID_COMMAND_EXECUTED_SUCCESSFULLY" ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Pause case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will pause the case.
     * @param string unpauseDate : Optional parameter. The date in the format "yyyy-mm-dd" indicating when to unpause
     * the case.
     * @return $result will return an object
     */
    public function pauseCase ($caseUid, $delIndex, $userUid, $unpauseDate = null)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty( $caseUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " caseUid" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $delIndex )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " delIndex" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $userUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " userUid" );

                $g->sessionVarRestore();

                return $result;
            }

            if (! empty( $unpauseDate )) {
                if (! preg_match( "/^\d{4}-\d{2}-\d{2}$/", $unpauseDate )) {
                    $result = new wsResponse( 100, G::LoadTranslation( "ID_INVALID_DATA" ) . " $unpauseDate" );

                    $g->sessionVarRestore();

                    return $result;
                }
            }

            $case = new Cases();
            $case->pauseCase( $caseUid, $delIndex, $userUid, $unpauseDate );

            //Response
            $res = new wsResponse( 0, G::LoadTranslation( "ID_COMMAND_EXECUTED_SUCCESSFULLY" ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Unpause case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will unpause the case.
     * @return $result will return an object
     */
    public function unpauseCase ($caseUid, $delIndex, $userUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty( $caseUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " caseUid" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $delIndex )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " delIndex" );

                $g->sessionVarRestore();

                return $result;
            }

            if (empty( $userUid )) {
                $result = new wsResponse( 100, G::LoadTranslation( "ID_REQUIRED_FIELD" ) . " userUid" );

                $g->sessionVarRestore();

                return $result;
            }

            $case = new Cases();
            $case->unpauseCase( $caseUid, $delIndex, $userUid );

            //Response
            $res = new wsResponse( 0, G::LoadTranslation( "ID_COMMAND_EXECUTED_SUCCESSFULLY" ) );

            $result = array ("status_code" => $res->status_code,"message" => $res->message,"timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Add case note
     *
     * @param string caseUid : ID of the case.
     * @param string processUid : ID of the process.
     * @param string taskUid : ID of the task.
     * @param string userUid : The unique ID of the user who will add note case.
     * @param string note : Note of the case.
     * @param int sendMail : Optional parameter. If set to 1, will send an email to all participants in the case.
     * @return $result will return an object
     */
    public function addCaseNote($caseUid, $processUid, $taskUid, $userUid, $note, $sendMail = 1)
    {
        try {
            if (empty($caseUid)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                return $result;
            }

            if (empty($processUid)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " processUid");

                return $result;
            }

            if (empty($taskUid)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " taskUid");

                return $result;
            }

            if (empty($userUid)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                return $result;
            }

            if (empty($note)) {
                $result = new wsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " note");

                return $result;
            }

            $case = new Cases();

            $respView = $case->getAllObjectsFrom($processUid, $caseUid, $taskUid, $userUid, "VIEW");
            $respBlock = $case->getAllObjectsFrom($processUid, $caseUid, $taskUid, $userUid, "BLOCK");

            if ($respView["CASES_NOTES"] == 0 && $respBlock["CASES_NOTES"] == 0) {
                $result = new wsResponse(100, G::LoadTranslation("ID_CASES_NOTES_NO_PERMISSIONS"));

                return $result;
            }

            //Add note case
            $appNote = new AppNotes();
            $response = $appNote->addCaseNote($caseUid, $userUid, $note, $sendMail);

            //Response
            $result = new wsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            return $result;
        } catch (Exception $e) {
            $result = new wsResponse(100, $e->getMessage());

            return $result;
        }
    }
}

