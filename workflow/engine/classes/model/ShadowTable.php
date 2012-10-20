<?php
/**
 * ShadowTable.php
 * @package    workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseShadowTable.php';


/**
 * Skeleton subclass for representing a row from the 'SHADOW_TABLE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class ShadowTable extends BaseShadowTable
{
    public function load($sUID)
    {
        try {
            $oShadowTable = ShadowTablePeer::retrieveByPK($sUID);
            if (!is_null($oShadowTable)) {
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
        if (!isset($aData['SHD_UID'])) {
            $aData['SHD_UID'] = G::generateUniqueID();
        } else {
            if ($aData['SHD_UID'] == '') {
                $aData['SHD_UID'] = G::generateUniqueID();
            }
        }
        $oConnection = Propel::getConnection(ShadowTablePeer::DATABASE_NAME);
        try {
            $oShadowTable = new ShadowTable();
            $oShadowTable->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oShadowTable->validate()) {
                $oConnection->begin();
                $iResult = $oShadowTable->save();
                $oConnection->commit();
                return $aData['SHD_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oShadowTable->getValidationFailures();
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

    public function remove($sUID)
    {
        $oConnection = Propel::getConnection(ShadowTablePeer::DATABASE_NAME);
        try {
            $oShadowTable = ShadowTablePeer::retrieveByPK($sUID);
            if (!is_null($oShadowTable)) {
                $oConnection->begin();
                $iResult = $oShadowTable->delete();
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

