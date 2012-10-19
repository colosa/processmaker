<?php
/**
 * CaseTracker.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseCaseTracker.php';

/**
 * Skeleton subclass for representing a row from the 'CASE_TRACKER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class CaseTracker extends BaseCaseTracker
{

    public function load ($sProcessUID)
    {
        try {
            $oRow = CaseTrackerPeer::retrieveByPK( $sProcessUID );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '$sProcessUID' in table CASE_TRACKER doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $oConnection = Propel::getConnection( CaseTrackerPeer::DATABASE_NAME );
        try {
            if (! isset( $aData['CT_MAP_TYPE'] )) {
                $aData['CT_MAP_TYPE'] = 'PROCESSMAP';
            }
            $oCaseTracker = new CaseTracker();
            $oCaseTracker->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oCaseTracker->validate()) {
                $oConnection->begin();
                $iResult = $oCaseTracker->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oCaseTracker->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The registry cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function update ($aData)
    {
        $oConnection = Propel::getConnection( CaseTrackerPeer::DATABASE_NAME );
        try {
            $oCaseTracker = CaseTrackerPeer::retrieveByPK( $aData['PRO_UID'] );
            if (! is_null( $oCaseTracker )) {
                $oCaseTracker->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oCaseTracker->validate()) {
                    $oConnection->begin();
                    $iResult = $oCaseTracker->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oCaseTracker->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception( 'The registry cannot be updated!<br />' . $sMessage ));
                }
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function remove ($sProcessUID)
    {
        $oConnection = Propel::getConnection( CaseTrackerPeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->setProUid( $sProcessUID );
            $iResult = $this->delete();
            $oConnection->commit();
            return $iResult;
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    function caseTrackerExists ($sUid)
    {
        try {
            $oObj = CaseTrackerPeer::retrieveByPk( $sUid );
            return (is_object( $oObj ) && get_class( $oObj ) == 'CaseTracker');
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}
// CaseTracker

