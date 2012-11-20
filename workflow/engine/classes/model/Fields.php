<?php
/**
 * Fields.php
 * @package    workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseFields.php';

/**
 * Skeleton subclass for representing a row from the 'FIELDS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Fields extends BaseFields
{
    public function load($sUID)
    {
        try {
            $oFields = FieldsPeer::retrieveByPK($sUID);
            if (!is_null($oFields)) {
                $aFields = $oFields->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                return $aFields;
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function create($aData)
    {
        if (!isset($aData['FLD_UID'])) {
            $aData['FLD_UID'] = G::generateUniqueID();
        } else {
            if ($aData['FLD_UID'] == '') {
                $aData['FLD_UID'] = G::generateUniqueID();
            }
        }
        $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
        try {
            $oFields = new Fields();
            $oFields->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oFields->validate()) {
                $oConnection->begin();
                $iResult = $oFields->save();
                $oConnection->commit();
                return $aData['FLD_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oFields->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw(new Exception('The registry cannot be created!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function update($aData)
    {
        $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
        try {
            $oFields = FieldsPeer::retrieveByPK($aData['FLD_UID']);
            if (!is_null($oFields)) {
                $oFields->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oFields->validate()) {
                    $oConnection->begin();
                    $iResult = $oFields->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oFields->getValidationFailures();
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

    public function remove($sUID)
    {
        $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
        try {
            $oFields = FieldsPeer::retrieveByPK($sUID);
            if (!is_null($oFields)) {
                $oConnection->begin();
                $iResult = $oFields->delete();
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
}

