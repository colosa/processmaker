<?php
/**
 * StepTrigger.php
 *
 * @package workflow.engine.classes.model
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
 *
 */

//require_once 'classes/model/om/BaseStepTrigger.php';

/**
 * Skeleton subclass for representing a row from the 'STEP_TRIGGER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class StepTrigger extends BaseStepTrigger
{

    public function create ($aData)
    {
        $con = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            //delete old StepTrigger Rows, because is not safe insert previous verify old rows.
            $criteria = new Criteria();
            $criteria->add( StepTriggerPeer::STEP_UID, $aData['STEP_UID'] );
            $criteria->add( StepTriggerPeer::TAS_UID, $aData['TAS_UID'] );
            $criteria->add( StepTriggerPeer::TRI_UID, $aData['TRI_UID'] );
            $criteria->add( StepTriggerPeer::ST_TYPE, $aData['ST_TYPE'] );
            $objects = StepTriggerPeer::doSelect( $criteria, $con );
            $con->begin();
            foreach ($objects as $row) {
                $this->remove( $row->getStepUid(), $row->getTasUid(), $row->getTriUid(), $row->getStType() );
            }
            $con->commit();

            $con->begin();
            $this->setStepUid( $aData['STEP_UID'] );
            $this->setTasUid( $aData['TAS_UID'] );
            $this->setTriUid( $aData['TRI_UID'] );
            $this->setStType( $aData['ST_TYPE'] );
            $this->setStCondition( "" );
            $this->setStPosition( "" );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function load ($StepUid, $TasUid, $TriUid, $StType)
    {
        try {
            $oRow = StepTriggerPeer::retrieveByPK( $StepUid, $TasUid, $TriUid, $StType );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '$StepUid, $TasUid, $TriUid, $StType' in table StepTrigger doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function update ($fields)
    {
        $con = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['STEP_UID'], $fields['TAS_UID'], $fields['TRI_UID'], $fields['ST_TYPE'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove ($StepUid, $TasUid, $TriUid, $StType)
    {
        $oConnection = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            $oStepTrigger = StepTriggerPeer::retrieveByPK( $StepUid, $TasUid, $TriUid, $StType );
            if (! is_null( $oStepTrigger )) {
                $oConnection->begin();
                $iResult = $oStepTrigger->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new Exception( "The row '$StepUid, $TasUid, $TriUid, $StType' in table StepTrigger doesn't exist!" ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function stepTriggerExists ($StepUid, $TasUid, $TriUid, $StType)
    {
        $con = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            $oObj = StepTriggerPeer::retrieveByPk( $StepUid, $TasUid, $TriUid, $StType );
            if (is_object( $oObj ) && get_class( $oObj ) == 'StepTrigger') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function removeTrigger ($TriUid)
    {
        $con = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            $criteria = new Criteria();
            //$criteria->add(StepTriggerPeer::STEP_UID, $step_uid);
            //$criteria->add(StepTriggerPeer::TAS_UID, $tas_uid);
            $criteria->add( StepTriggerPeer::TRI_UID, $TriUid );
            //$criteria->add(StepTriggerPeer::ST_TYPE, $st_type);
            $objects = StepTriggerPeer::doSelect( $criteria, $con );
            $con->begin();
            foreach ($objects as $v) {
                $this->remove( $v->getStepUid, $v->getTasUid, $v->getTriUid, $v->getStType );
            }
            $con->commit();
            return count( $objects );
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function getNextPosition ($sStepUID, $sType, $sTaskId = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( '(COUNT(*) + 1) AS POSITION' );
            $oCriteria->add( StepTriggerPeer::STEP_UID, $sStepUID );
            $oCriteria->add( StepTriggerPeer::ST_TYPE, $sType );
            if ($sTaskId != '') {
                $oCriteria->add( StepTriggerPeer::TAS_UID , $sTaskId );
            }
            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            return (int) $aRow['POSITION'];
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     *
     *
     * @param type $sStepUID
     * @param type $sTaskUID
     * @param type $sType
     *
     * @throws Exception
     */
    public function orderPosition ($sStepUID, $sTaskUID, $sType)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepTriggerPeer::STEP_UID, $sStepUID );
            $oCriteria->add( StepTriggerPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( StepTriggerPeer::ST_TYPE, $sType );
            $oCriteria->addAscendingOrderByColumn(StepTriggerPeer::ST_POSITION);
            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if ((int)$aRow['ST_POSITION'] > 1 ) {
                $rowNro = 1;
                while ($aRow = $oDataset->getRow()) {
                    $oStep = StepTriggerPeer::retrieveByPK( $aRow['STEP_UID'], $aRow['TAS_UID'], $aRow['TRI_UID'], $aRow['ST_TYPE'] );
                    $oStep->setStPosition( $rowNro );
                    $oStep->save();
                    $oDataset->next();
                    $rowNro++;
                }
            }

        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function reOrder ($sStepUID, $sTaskUID, $sType, $iPosition)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepTriggerPeer::STEP_UID, $sStepUID );
            $oCriteria->add( StepTriggerPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( StepTriggerPeer::ST_TYPE, $sType );
            $oCriteria->add( StepTriggerPeer::ST_POSITION, $iPosition, '>' );
            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oStep = StepTriggerPeer::retrieveByPK( $aRow['STEP_UID'], $aRow['TAS_UID'], $aRow['TRI_UID'], $aRow['ST_TYPE'] );
                $oStep->setStPosition( ($aRow['ST_POSITION']) - 1 );
                $oStep->save();
                $oDataset->next();
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function up ($sStepUID = '', $sTaskUID = '', $sTriggerUID = '', $sType = '', $iPosition = 0)
    {
        try {
            if ($iPosition > 1) {
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepTriggerPeer::ST_POSITION, $iPosition );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepTriggerPeer::STEP_UID, $sStepUID );
                $oCriteria2->add( StepTriggerPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepTriggerPeer::ST_TYPE, $sType );
                $oCriteria2->add( StepTriggerPeer::ST_POSITION, ($iPosition - 1) );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepTriggerPeer::ST_POSITION, ($iPosition - 1) );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepTriggerPeer::STEP_UID, $sStepUID );
                $oCriteria2->add( StepTriggerPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepTriggerPeer::TRI_UID, $sTriggerUID );
                $oCriteria2->add( StepTriggerPeer::ST_TYPE, $sType );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function down ($sStepUID = '', $sTaskUID = '', $sTriggerUID = '', $sType = '', $iPosition = 0)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( 'COUNT(*) AS MAX_POSITION' );
            $oCriteria->add( StepTriggerPeer::STEP_UID, $sStepUID );
            $oCriteria->add( StepTriggerPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( StepTriggerPeer::ST_TYPE, $sType );
            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if ($iPosition < (int) $aRow['MAX_POSITION']) {
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepTriggerPeer::ST_POSITION, $iPosition );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepTriggerPeer::STEP_UID, $sStepUID );
                $oCriteria2->add( StepTriggerPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepTriggerPeer::ST_TYPE, $sType );
                $oCriteria2->add( StepTriggerPeer::ST_POSITION, ($iPosition + 1) );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepTriggerPeer::ST_POSITION, ($iPosition + 1) );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepTriggerPeer::STEP_UID, $sStepUID );
                $oCriteria2->add( StepTriggerPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepTriggerPeer::TRI_UID, $sTriggerUID );
                $oCriteria2->add( StepTriggerPeer::ST_TYPE, $sType );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function createRow ($aData)
    {
        $con = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $this->setStepUid( $aData['STEP_UID'] );
                $this->setTasUid( $aData['TAS_UID'] );
                $this->setTriUid( $aData['TRI_UID'] );
                $this->setStType( $aData['ST_TYPE'] );
                $this->setStCondition( $aData['ST_CONDITION'] );
                $this->setStPosition( $aData['ST_POSITION'] );

                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
}

