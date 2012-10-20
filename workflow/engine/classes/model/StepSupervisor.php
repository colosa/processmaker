<?php
/**
 * StepSupervisor.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseStepSupervisor.php';

/**
 * Skeleton subclass for representing a row from the 'STEP_SUPERVISOR' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class StepSupervisor extends BaseStepSupervisor
{
    public function load ($Uid)
    {
        try {
            $oRow = StepSupervisorPeer::retrieveByPK( $Uid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '$Uid' in table StepSupervisor doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function Exists ($Uid)
    {
        try {
            $oPro = StepSupervisorPeer::retrieveByPk( $Uid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'StepSupervisor') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Create the step supervisor registry
     *
     * @param array $aData
     * @return boolean
     *
     */
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( StepSupervisorPeer::DATABASE_NAME );
        try {
            if (isset( $aData['STEP_UID'] ) && $aData['STEP_UID'] == '') {
                unset( $aData['STEP_UID'] );
            }
            if (! isset( $aData['STEP_UID'] )) {
                $aData['STEP_UID'] = G::generateUniqueID();
            }
            $oStepSupervisor = new StepSupervisor();
            $oStepSupervisor->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oStepSupervisor->validate()) {
                $oConnection->begin();
                $iResult = $oStepSupervisor->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oStepSupervisor->getValidationFailures();
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

    /**
     * Update the step supervisor registry
     *
     * @param array $aData
     * @return integer
     *
     */
    public function update ($aData)
    {
        $oConnection = Propel::getConnection( StepSupervisorPeer::DATABASE_NAME );
        try {
            $oStepSupervisor = StepSupervisorPeer::retrieveByPK( $aData['STEP_UID'] );
            if (! is_null( $oStepSupervisor )) {
                $oStepSupervisor->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oStepSupervisor->validate()) {
                    $oConnection->begin();
                    $iResult = $oStepSupervisor->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oStepSupervisor->getValidationFailures();
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

    /**
     * Remove the step supervisor registry
     *
     * @param string $sStepUID
     * @return integer
     *
     */
    public function remove ($sStepUID)
    {
        $oConnection = Propel::getConnection( StepSupervisorPeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->setStepUid( $sStepUID );
            $iResult = $this->delete();
            $oConnection->commit();
            return $iResult;
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Get the next position for a atep
     *
     * @param string $sProcessUID
     * @return integer
     *
     */
    public function getNextPosition ($sProcessUID, $sType)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( '(COUNT(*) + 1) AS POSITION' );
            $oCriteria->add( StepSupervisorPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( StepSupervisorPeer::STEP_TYPE_OBJ, $sType );
            $oDataset = StepSupervisorPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            return (int) $aRow['POSITION'];
        } catch (Exception $oException) {
            throw $Exception;
        }
    }

    /**
     * Reorder the steps positions
     *
     * @param string $sProcessUID
     * @param string $iPosition
     *
     */
    public function reorderPositions ($sProcessUID, $iPosition, $sType)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepSupervisorPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( StepSupervisorPeer::STEP_TYPE_OBJ, $sType );
            $oCriteria->add( StepSupervisorPeer::STEP_POSITION, $iPosition, '>' );
            $oDataset = StepSupervisorPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            var_dump( StepSupervisorPeer::doCount( $oCriteria ) );
            while ($aRow = $oDataset->getRow()) {
                var_dump( $aRow );
                echo "\n";
                $this->update( array ('STEP_UID' => $aRow['STEP_UID'],'PRO_UID' => $aRow['PRO_UID'],'STEP_TYPE_OBJ' => $aRow['STEP_TYPE_OBJ'],'STEP_UID_OBJ' => $aRow['STEP_UID_OBJ'],'STEP_POSITION' => $aRow['STEP_POSITION'] - 1
                ) );
                $oDataset->next();
            }
        } catch (Exception $oException) {
            throw $Exception;
        }
    }

    public function removeByObject ($sType, $sObjUid)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepSupervisorPeer::STEP_TYPE_OBJ, $sType );
            $oCriteria->add( StepSupervisorPeer::STEP_UID_OBJ, $sObjUid );
            StepSupervisorPeer::doDelete( $oCriteria );
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public function loadInfo ($sObjUID)
    {

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( StepSupervisorPeer::STEP_UID_OBJ, $sObjUID );
        $oDataset = StepSupervisorPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return ($aRow);
    }
}

