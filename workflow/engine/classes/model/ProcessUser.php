<?php
/**
 * ProcessUser.php
 * @package    workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseProcessUser.php';


/**
 * Skeleton subclass for representing a row from the 'PROCESS_USER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class ProcessUser extends BaseProcessUser
{
    public function create($aData)
    {
        $oConnection = Propel::getConnection(ProcessUserPeer::DATABASE_NAME);
        try {
            $criteria = new Criteria('workflow');
            $criteria->add(ProcessUserPeer::PU_UID,  $aData['PU_UID'] );
            $criteria->add(ProcessUserPeer::PRO_UID,  $aData['PRO_UID']  );
            $criteria->add(ProcessUserPeer::USR_UID,  $aData['USR_UID']  );
            $criteria->add(ProcessUserPeer::PU_TYPE,  $aData['PU_TYPE']  );
            $objects = ProcessUserPeer::doSelect($criteria, $oConnection);
            $oConnection->begin();
            foreach ($objects as $row) {
                $this->remove($row->getTasUid(), $row->getUsrUid(), $row->getTuType(), $row->getTuRelation() );
            }
            $oConnection->commit();

            $oProcessUser = new ProcessUser();
            $oProcessUser->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oProcessUser->validate()) {
                $oConnection->begin();
                $iResult = $oProcessUser->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $sMessage = '';
                $aValidationFailures = $oProcessUser->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw(new Exception('The registry cannot be created!<br />'.$sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    /**
     * Remove the application document registry
     * @param string $sPuUid
     * @param string $sProUid
     * @param string $sUserUid
     * @return string
    **/
    public function remove($sPuUid)
    {
        $oConnection = Propel::getConnection(ProcessUserPeer::DATABASE_NAME);
        try {
            $oProcessUser = ProcessUserPeer::retrieveByPK($sPuUid);
            if (!is_null($oProcessUser)) {
                $oConnection->begin();
                $iResult = $oProcessUser->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function Exists ($sUid)
    {
        try {
            $oObj = ProcessUserPeer::retrieveByPk($sUid);
            return (is_object($oObj) && get_class($oObj) == 'ProcessUser');
        } catch (Exception $oError) {
            throw($oError);
        }
    }
}

