<?php
namespace ProcessMaker\BusinessModel;

use Behat\Behat\Exception\Exception;
use \G;
use \Criteria;
use \ObjectPermissionPeer;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class ProcessPermissions
{
    /**
     * Get list for Process Permissions
     *
     * @var string $pro_uid. Uid for Process
     * @var string $op_uid. Uid for Process Permission
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getProcessPermissions($pro_uid, $op_uid = '')
    {
        $pro_uid = $this->validateProUid($pro_uid);
        if ($op_uid != '') {
            $op_uid  = $this->validateOpUid($op_uid);
        }

        $aObjectsPermissions = array();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(ObjectPermissionPeer::PRO_UID, $pro_uid);
        if ($op_uid != '') {
            $oCriteria->add(ObjectPermissionPeer::OP_UID, $op_uid);
        }
        $oDataset = ObjectPermissionPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            //Obtain task target
            if (($aRow['TAS_UID'] != '') && ($aRow['TAS_UID'] != '0')) {
                try {
                    $oTask = new \Task();
                    $aFields = $oTask->load($aRow['TAS_UID']);
                    $sTaskTarget = $aFields['TAS_TITLE'];
                } catch (\Exception $oError) {
                    $sTaskTarget = 'All Tasks';
                }
            } else {
                $sTaskTarget = G::LoadTranslation('ID_ANY_TASK');
            }
            //Obtain user or group
            if ($aRow['OP_USER_RELATION'] == 1) {
                $oUser = new \Users();
                $aFields = $oUser->load($aRow['USR_UID']);
                $sUserGroup = $aFields['USR_FIRSTNAME'] . ' ' . $aFields['USR_LASTNAME'] . ' (' . $aFields['USR_USERNAME'] . ')';
            } else {
                $oGroup = new \Groupwf();
                if ($aRow['USR_UID'] != '') {
                    try {
                        $aFields = $oGroup->load($aRow['USR_UID']);
                        $sUserGroup = $aFields['GRP_TITLE'];
                    } catch (\Exception $oError) {
                        $sUserGroup = '(GROUP DELETED)';
                    }
                } else {
                    $sUserGroup = G::LoadTranslation('ID_ANY');
                }
            }
            //Obtain task source
            if (($aRow['OP_TASK_SOURCE'] != '') && ($aRow['OP_TASK_SOURCE'] != '0')) {
                try {
                    $oTask = new \Task();
                    $aFields = $oTask->load($aRow['OP_TASK_SOURCE']);
                    $sTaskSource = $aFields['TAS_TITLE'];
                } catch (\Exception $oError) {
                    $sTaskSource = 'All Tasks';
                }
            } else {
                $sTaskSource = G::LoadTranslation('ID_ANY_TASK');
            }
            //Obtain object and type
            switch ($aRow['OP_OBJ_TYPE']) {
                case 'ALL':
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                case 'ANY': //For backward compatibility (some process with ANY instead of ALL
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                /* case 'ANY_DYNAFORM':
                  $sObjectType = G::LoadTranslation('ID_ANY_DYNAFORM');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break;
                  case 'ANY_INPUT':
                  $sObjectType = G::LoadTranslation('ID_ANY_INPUT');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break;
                  case 'ANY_OUTPUT':
                  $sObjectType = G::LoadTranslation('ID_ANY_OUTPUT');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break; */
                case 'DYNAFORM':
                    $sObjectType = G::LoadTranslation('ID_DYNAFORM');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oDynaform = new \Dynaform();
                        try {
                            $aFields = $oDynaform->load($aRow['OP_OBJ_UID']);
                            $sObject = $aFields['DYN_TITLE'];
                        } catch (\Exception $errorNotExists) {
                            error_log($errorNotExists->getMessage() . ' - ' . G::LoadTranslation('ID_PROCESS_PERMISSIONS') . 
                                      ' - ' . $aRow['OP_OBJ_TYPE'] . ' - ' . $aRow['OP_OBJ_UID']);
                            $oDataset->next();
                            continue 2;
                        }
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'INPUT':
                    $sObjectType = G::LoadTranslation('ID_INPUT_DOCUMENT');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oInputDocument = new \InputDocument();
                        try {
                            $aFields = $oInputDocument->load($aRow['OP_OBJ_UID']);
                            $sObject = $aFields['INP_DOC_TITLE'];
                        } catch (\Exception $errorNotExists) {
                            error_log($errorNotExists->getMessage() . ' - ' . G::LoadTranslation('ID_PROCESS_PERMISSIONS') . 
                                      ' - ' . $aRow['OP_OBJ_TYPE'] . ' - ' . $aRow['OP_OBJ_UID']);
                            $oDataset->next();
                            continue 2;
                        }
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'OUTPUT':
                    $sObjectType = G::LoadTranslation('ID_OUTPUT_DOCUMENT');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oOutputDocument = new \OutputDocument();
                        try {
                            $aFields = $oOutputDocument->load($aRow['OP_OBJ_UID']);
                            $sObject = $aFields['OUT_DOC_TITLE'];
                        } catch (\Exception $errorNotExists) {
                            error_log($errorNotExists->getMessage() . ' - ' . G::LoadTranslation('ID_PROCESS_PERMISSIONS') . 
                                      ' - ' . $aRow['OP_OBJ_TYPE'] . ' - ' . $aRow['OP_OBJ_UID']);
                            $oDataset->next();
                            continue 2;
                        }
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'CASES_NOTES':
                    $sObjectType = G::LoadTranslation('ID_CASES_NOTES');
                    $sObject = 'N/A';
                    break;
                case 'MSGS_HISTORY':
                    $sObjectType = G::LoadTranslation('MSGS_HISTORY');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                default:
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
            }
            //Participated
            if ($aRow['OP_PARTICIPATE'] == 0) {
                $sParticipated = G::LoadTranslation('ID_NO');
            } else {
                $sParticipated = G::LoadTranslation('ID_YES');
            }
            //Obtain action (permission)
            $sAction = G::LoadTranslation('ID_' . $aRow['OP_ACTION']);
            //Add to array
            $arrayTemp = array();
            $arrayTemp = array_merge($aRow, array(
                'OP_UID'        => $aRow['OP_UID'],
                'TASK_TARGET'   => $sTaskTarget,
                'GROUP_USER'    => $sUserGroup,
                'TASK_SOURCE'   => $sTaskSource,
                'OBJECT_TYPE'   => $sObjectType,
                'OBJECT'        => $sObject,
                'PARTICIPATED'  => $sParticipated,
                'ACTION'        => $sAction,
                'OP_CASE_STATUS' => $aRow['OP_CASE_STATUS'])
            );
            $aObjectsPermissions[] = array_change_key_case($arrayTemp, CASE_LOWER);
            $oDataset->next();
        }

        if ($op_uid != '' && empty($aObjectsPermissions)) {
            throw (new \Exception(\G::LoadTranslation("ID_ROW_DOES_NOT_EXIST")));
        } elseif ($op_uid != '' && !empty($aObjectsPermissions)) {
            $aObjectsPermissions = array_change_key_case($aObjectsPermissions, CASE_LOWER);
            return current($aObjectsPermissions);
        }
        $aObjectsPermissions = array_change_key_case($aObjectsPermissions, CASE_LOWER);
        return $aObjectsPermissions;
    }

    /**
     * Save Process Permission
     *
     * @var array $data, Data for Process Permission
     * @var string $opUid, Uid for Process Permission
     *
     * @access public
     *
     * @return void
     * @throws Exception
     */
    public function saveProcessPermission($data, $opUid = '')
    {
        try {
            $data = array_change_key_case($data, CASE_UPPER);

            $this->validateProUid($data['PRO_UID']);
            if ($opUid != '') {
                $opUid  = $this->validateOpUid($opUid);
            }
            if ($data['OP_USER_RELATION'] == "1") {
                $this->validateUsrUid($data['USR_UID']);
            } else {
                $this->validateGrpUid($data['USR_UID']);
            }
            if (isset($data['TAS_UID']) && ($data['TAS_UID'] != '')) {
                $this->validateTasUid($data['TAS_UID']);
            } else {
                $data['TAS_UID'] = '';
            }
            if (isset($data['OP_TASK_SOURCE']) && ($data['OP_TASK_SOURCE'] != '')) {
                $this->validateTasUid($data['OP_TASK_SOURCE']);
            } else {
                $data['OP_TASK_SOURCE'] = '';
            }

            $sObjectUID = '';
            switch ($data['OP_OBJ_TYPE']) {
                case 'ANY':
                    //case 'ANY_DYNAFORM':CASES_NOTES
                    //case 'ANY_INPUT':
                    //case 'ANY_OUTPUT':
                    $sObjectUID = '';
                    break;
                case 'DYNAFORM':
                    $data['DYNAFORMS'] = $data['DYNAFORMS'] == 0 ? '': $data['DYNAFORMS'];
                    if ($data['DYNAFORMS'] != '') {
                        $this->validateDynUid($data['DYNAFORMS']);
                    }
                    $sObjectUID = $data['DYNAFORMS'];
                    break;
                case 'ATTACHED':
                    $sObjectUID = '';
                    break;
                case 'INPUT':
                    $data['INPUTS'] = $data['INPUTS'] == 0 ? '': $data['INPUTS'];
                    if ($data['INPUTS'] != '') {
                        $this->validateInpUid($data['INPUTS']);
                    }
                    $sObjectUID = $data['INPUTS'];
                    break;
                case 'OUTPUT':
                    $data['OUTPUTS'] = $data['OUTPUTS'] == 0 ? '': $data['OUTPUTS'];
                    if ($data['OUTPUTS'] != '') {
                        $this->validateOutUid($data['OUTPUTS']);
                    }
                    $sObjectUID = $data['OUTPUTS'];
                    break;
            }
            $oOP = new \ObjectPermission();
            $permissionUid = ($opUid != '') ? $opUid : G::generateUniqueID();
            $data['OP_UID'] = $permissionUid;
            $data['OP_OBJ_UID'] = $sObjectUID;

            if ($opUid == '') {
                $oOP->fromArray( $data, \BasePeer::TYPE_FIELDNAME );
                $oOP->save();
                $daraRes = $oOP->load($permissionUid);
                $daraRes = array_change_key_case($daraRes, CASE_LOWER);
                return $daraRes;
            } else {
                $data['TAS_UID'] = $data['TAS_UID'] != '' ? $data['TAS_UID'] : '0';
                $data['OP_TASK_SOURCE'] = $data['OP_TASK_SOURCE'] != '' ? $data['OP_TASK_SOURCE'] : '0';
                $data['OP_PARTICIPATE'] = $data['OP_PARTICIPATE'] != '' ? $data['OP_PARTICIPATE'] : 0;
                $data['OP_OBJ_TYPE'] = $data['OP_OBJ_TYPE'] != '' ? $data['OP_OBJ_TYPE'] : '0';
                $data['OP_OBJ_UID'] = $data['OP_OBJ_UID'] != '' ? $data['OP_OBJ_UID'] : '0';
                $data['OP_ACTION'] = $data['OP_ACTION'] != '' ? $data['OP_ACTION'] : '0';
                $data['OP_CASE_STATUS'] = $data['OP_CASE_STATUS'] != '' ? $data['OP_CASE_STATUS'] : '0';
                $oOP->update($data);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Process Permission
     *
     * @var string $op_uid. Uid for Process Permission
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteProcessPermission($op_uid, $pro_uid)
    {
        try {
            $pro_uid = $this->validateProUid($pro_uid);
            $op_uid  = $this->validateOpUid($op_uid);

            $oOP = new \ObjectPermission();
            $oOP = ObjectPermissionPeer::retrieveByPK( $op_uid );
            $oOP->delete();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate Process Uid
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateProUid ($pro_uid)
    {
        $pro_uid = trim($pro_uid);
        if ($pro_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_PROJECT_NOT_EXIST", array('prj_uid',''))));
        }
        $oProcess = new \Process();
        if (!($oProcess->processExists($pro_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_PROJECT_NOT_EXIST", array('prj_uid',$pro_uid))));
        }
        return $pro_uid;
    }

    /**
     * Validate Process Permission Uid
     * @var string $op_uid. Uid for process permission
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateOpUid ($op_uid)
    {
        $op_uid = trim($op_uid);
        if ($op_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_PROCESS_PERMISSION_NOT_EXIST", array('op_uid',''))));
        }
        $oObjectPermission = new \ObjectPermission();
        if (!($oObjectPermission->Exists($op_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_PROCESS_PERMISSION_NOT_EXIST", array('op_uid',$op_uid))));
        }
        return $op_uid;
    }

    /**
     * Validate User Uid
     * @var string $usr_uid. Uid for user
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateUsrUid($usr_uid)
    {
        $usr_uid = trim($usr_uid);
        if ($usr_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_USER_NOT_EXIST", array('usr_uid',''))));
        }
        $oUsers = new \Users();
        if (!($oUsers->userExists($usr_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_USER_NOT_EXIST", array('usr_uid',$usr_uid))));
        }
        return $usr_uid;
    }

    /**
     * Validate Group Uid
     * @var string $grp_uid. Uid for group
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateGrpUid($grp_uid)
    {
        $grp_uid = trim($grp_uid);
        if ($grp_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_GROUP_NOT_EXIST", array('grp_uid',''))));
        }
        $oGroup = new \Groupwf();
        if (!($oGroup->GroupwfExists($grp_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_GROUP_NOT_EXIST", array('grp_uid',$grp_uid))));
        }
        return $grp_uid;
    }

    /**
     * Validate Task Uid
     * @var string $tas_uid. Uid for task
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateTasUid($tas_uid)
    {
        $tas_uid = trim($tas_uid);
        if ($tas_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_TASK_NOT_EXIST", array('tas_uid',''))));
        }
        $oTask = new \Task();
        if (!($oTask->taskExists($tas_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_TASK_NOT_EXIST", array('tas_uid',$tas_uid))));
        }
        return $tas_uid;
    }

    /**
     * Validate Dynaform Uid
     * @var string $dyn_uid. Uid for dynaform
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateDynUid($dyn_uid)
    {
        $dyn_uid = trim($dyn_uid);
        if ($dyn_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_DYNAFORM_NOT_EXIST", array('dyn_uid',''))));
        }
        $oDynaform = new \Dynaform();
        if (!($oDynaform->dynaformExists($dyn_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_DYNAFORM_NOT_EXIST", array('dyn_uid',$dyn_uid))));
        }
        return $dyn_uid;
    }

    /**
     * Validate Input Uid
     * @var string $inp_uid. Uid for dynaform
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateInpUid($inp_uid)
    {
        $inp_uid = trim($inp_uid);
        if ($inp_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_INPUT_NOT_EXIST", array('inp_uid',''))));
        }
        $oInputDocument = new \InputDocument();
        if (!($oInputDocument->InputExists($inp_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_INPUT_NOT_EXIST", array('inp_uid',$inp_uid))));
        }
        return $inp_uid;
    }

    /**
     * Validate Output Uid
     * @var string $out_uid. Uid for output
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateOutUid($out_uid)
    {
        $out_uid = trim($out_uid);
        if ($out_uid == '') {
            throw (new \Exception(\G::LoadTranslation("ID_OUTPUT_NOT_EXIST", array('out_uid',''))));
        }
        $oOutputDocument = new \OutputDocument();
        if (!($oOutputDocument->OutputExists($out_uid))) {
            throw (new \Exception(\G::LoadTranslation("ID_OUTPUT_NOT_EXIST", array('out_uid',$out_uid))));
        }
        return $out_uid;
    }
}

