<?php
/**
 * SubApplication.php
 * @package    workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseSubApplication.php';


/**
 * Skeleton subclass for representing a row from the 'SUB_APPLICATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class SubApplication extends BaseSubApplication
{
    public function load($sAppUID, $sAppParent, $iIndexParent, $iThreadParent)
    {
        try {
            $oRow = SubApplicationPeer::retrieveByPK($sAppUID, $sAppParent, $iIndexParent, $iThreadParent);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw new Exception("The row '$sAppUID, $sAppParent, $iIndexParent, $iThreadParent' in table SubApplication doesn't exist!");
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function create($aData)
    {
        $oConnection = Propel::getConnection(SubApplicationPeer::DATABASE_NAME);
        try {
            $oSubApplication = new SubApplication();
            $oSubApplication->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oSubApplication->validate()) {
                $oConnection->begin();
                $iResult = $oSubApplication->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $sMessage = '';
                $aValidationFailures = $oSubApplication->getValidationFailures();
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

    public function update($aData)
    {
        $oConnection = Propel::getConnection(SubApplicationPeer::DATABASE_NAME);
        try {
            $oSubApplication = SubApplicationPeer::retrieveByPK($aData['APP_UID'], $aData['APP_PARENT'], $aData['DEL_INDEX_PARENT'], $aData['DEL_THREAD_PARENT']);
            if (!is_null($oSubApplication)) {
                $oSubApplication->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oSubApplication->validate()) {
                    $oConnection->begin();
                    $iResult = $oSubApplication->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oSubApplication->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw(new Exception('The registry cannot be updated!<br />'.$sMessage));
                }
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }
}

