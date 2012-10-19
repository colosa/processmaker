<?php
/**
 * Step.php
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

require_once 'classes/model/om/BaseStep.php';

/**
 * Skeleton subclass for representing a row from the 'STEP' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Step extends BaseStep
{

    public function create ($aData)
    {
        $con = Propel::getConnection( StepPeer::DATABASE_NAME );
        try {
            if (isset( $aData['STEP_UID'] ) && $aData['STEP_UID'] == '') {
                unset( $aData['STEP_UID'] );
            }
            if (isset( $aData['STEP_UID'] )) {
                $sStepUID = $aData['STEP_UID'];
            } else {
                $sStepUID = G::generateUniqueID();
            }

            $con->begin();
            $this->setStepUid( $sStepUID );
            $this->setProUid( $aData['PRO_UID'] );
            $this->setTasUid( $aData['TAS_UID'] );

            if (isset( $aData['STEP_TYPE_OBJ'] )) {
                $this->setStepTypeObj( $aData['STEP_TYPE_OBJ'] );
            } else {
                $this->setStepTypeObj( "DYNAFORM" );
            }

            if (isset( $aData['STEP_UID_OBJ'] )) {
                $this->setStepUidObj( $aData['STEP_UID_OBJ'] );
            } else {
                $this->setStepUidObj( "" );
            }

            if (isset( $aData['STEP_CONDITION'] )) {
                $this->setStepCondition( $aData['STEP_CONDITION'] );
            } else {
                $this->setStepCondition( "" );
            }

            if (isset( $aData['STEP_POSITION'] )) {
                $this->setStepPosition( $aData['STEP_POSITION'] );
            } else {
                $this->setStepPosition( "" );
            }

            if (isset( $aData['STEP_MODE'] )) {
                $this->setStepMode( $aData['STEP_MODE'] );
            } else {
                $this->setStepMode( "" );
            }

            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $sStepUID;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }

        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function load ($StepUid)
    {
        try {
            $oRow = StepPeer::retrieveByPK( $StepUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '$StepUid' in table StepUid doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function loadByProcessTaskPosition ($sProUid, $sTasUid, $sPosition)
    {
        try {
            $c = new Criteria( 'workflow' );
            $c->add( StepPeer::PRO_UID, $sProUid );
            $c->add( StepPeer::TAS_UID, $sTasUid );
            $c->add( StepPeer::STEP_POSITION, $sPosition );
            if (StepPeer::doCount( $c ) > 0) {
                $rs = StepPeer::doSelect( $c );
                return $rs[0];
            } else {
                return null;
            }

        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /*
  * Load the step information using the Task UID, the type and the object UID
  * @param string $sTaskUID
  * @param string $sType
  * @param string $sUID
  * @return variant
  */
    public function loadByType ($sTasUid, $sType, $sUid)
    {
        try {
            $c = new Criteria( 'workflow' );
            $c->add( StepPeer::TAS_UID, $sTasUid );
            $c->add( StepPeer::STEP_TYPE_OBJ, $sType );
            $c->add( StepPeer::STEP_UID_OBJ, $sUid );
            $rs = StepPeer::doSelect( $c );
            if (! is_null( $rs ) && ! is_null( $rs[0] )) {
                return $rs[0];
            } else {
                throw (new Exception( "You tried to call to loadByType method without send the Task UID or Type or Object UID !" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /*
  * update the step information using an array with all values
  * @param array $fields
  * @return variant
  */
    public function update ($fields)
    {
        $con = Propel::getConnection( StepPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['STEP_UID'] );
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

    public function remove ($sStepUID)
    {
        require_once ('classes/model/StepTrigger.php');
        $oStepTriggers = new StepTrigger();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( StepTriggerPeer::STEP_UID, $sStepUID );
        $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            $oStepTriggers->remove( $aRow['STEP_UID'], $aRow['TAS_UID'], $aRow['TRI_UID'], $aRow['ST_TYPE'] );
        }
        /*$con = Propel::getConnection(StepPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      //$this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      $this->setStepUid($sStepUID);
      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }*/
        $oConnection = Propel::getConnection( StepPeer::DATABASE_NAME );
        try {
            $oStep = StepPeer::retrieveByPK( $sStepUID );
            if (! is_null( $oStep )) {
                $oConnection->begin();
                $iResult = $oStep->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function getNextPosition ($sTaskUID)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( '(COUNT(*) + 1) AS POSITION' );
            $oCriteria->add( StepPeer::TAS_UID, $sTaskUID );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            return (int) $aRow['POSITION'];
        } catch (Exception $oException) {
            throw $Exception;
        }
    }

    public function reOrder ($sStepUID, $iPosition)
    {
        try {
            /*$oCriteria1 = new Criteria('workflow');
      $oCriteria1->add(StepPeer::STEP_POSITION, StepPeer::STEP_POSITION);
      $oCriteria2 = new Criteria('workflow');
      $oCriteria2->add(StepPeer::TAS_UID,      $sTaskUID);
      $oCriteria2->add(StepPeer::STEP_POSITION, $iPosition, '>');
      BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));*/
            $oStep = StepPeer::retrieveByPK( $sStepUID );
            $sTaskUID = $oStep->getTasUid();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( StepPeer::STEP_POSITION, $iPosition, '>' );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oStep = StepPeer::retrieveByPK( $aRow['STEP_UID'] );
                $oStep->setStepPosition( ($aRow['STEP_POSITION']) - 1 );
                $oStep->save();
                $oDataset->next();
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function up ($sStepUID = '', $sTaskUID = '', $iPosition = 0)
    {
        try {
            if ($iPosition > 1) {
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepPeer::STEP_POSITION, $iPosition );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepPeer::STEP_POSITION, ($iPosition - 1) );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepPeer::STEP_POSITION, ($iPosition - 1) );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepPeer::STEP_UID, $sStepUID );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function down ($sStepUID = '', $sTaskUID = '', $iPosition = 0)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( 'COUNT(*) AS MAX_POSITION' );
            $oCriteria->add( StepPeer::TAS_UID, $sTaskUID );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if ($iPosition < (int) $aRow['MAX_POSITION']) {
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepPeer::STEP_POSITION, $iPosition );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepPeer::TAS_UID, $sTaskUID );
                $oCriteria2->add( StepPeer::STEP_POSITION, ($iPosition + 1) );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->add( StepPeer::STEP_POSITION, ($iPosition + 1) );
                $oCriteria2 = new Criteria( 'workflow' );
                $oCriteria2->add( StepPeer::STEP_UID, $sStepUID );
                BasePeer::doUpdate( $oCriteria2, $oCriteria1, Propel::getConnection( 'workflow' ) );
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function removeStep ($sType = '', $sObjUID = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepPeer::STEP_TYPE_OBJ, $sType );
            $oCriteria->add( StepPeer::STEP_UID_OBJ, $sObjUID );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $this->reOrder( $aRow['STEP_UID'], $aRow['STEP_POSITION'] );
                $this->remove( $aRow['STEP_UID'] );
                $oDataset->next();
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * verify if Step row specified in [sUid] exists.
     *
     * @param string $sUid the uid of the
     */

    public function StepExists ($sUid)
    {
        $con = Propel::getConnection( StepPeer::DATABASE_NAME );
        try {
            $oObj = StepPeer::retrieveByPk( $sUid );
            if (is_object( $oObj ) && get_class( $oObj ) == 'Step') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * verify if a dynaform is assigned some steps
     *
     * @param string $sproUid the uid of the process
     * @param string $sObjUID the uid of the dynaform
     */
    public function loadInfoAssigDynaform ($sproUid, $sObjUID)
    {

        require_once ("classes/model/DynaformPeer.php");
        G::LoadSystem( 'dynaformhandler' );

        $oC = new Criteria( 'workflow' );
        $oC->add( DynaformPeer::DYN_UID, $sObjUID );
        $oC->add( DynaformPeer::PRO_UID, $sproUid );
        $oDataset = DynaformPeer::doSelectRS( $oC );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();

        if ($aRow['DYN_TYPE'] != 'xmlform') {

            $oC1 = new Criteria( 'workflow' );
            $oC1->add( DynaformPeer::PRO_UID, $sproUid );
            $oC1->add( DynaformPeer::DYN_TYPE, "xmlform" );
            $oDataset = DynaformPeer::doSelectRS( $oC1 );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();
                //print_r($aRow1);
                $dynHandler = new dynaFormHandler( PATH_DYNAFORM . $_POST['PRO_UID'] . "/" . $aRow1['DYN_UID'] . ".xml" );
                $dynFields = $dynHandler->getFields();
                $sxmlgrid = '';
                $sType = '';
                $check = 0;
                foreach ($dynFields as $field) {
                    $sType = $this->getAttribute( $field, 'type' );
                    if ($sType == 'grid') {
                        $sxmlgrid = $this->getAttribute( $field, 'xmlgrid' );
                        $aGridInfo = explode( "/", $sxmlgrid );
                        if ($aGridInfo[0] == $sproUid && $aGridInfo[1] == $sObjUID) {
                            $check = 1;
                        }
                    }
                }
            }
            return ($check == 1) ? $aGridInfo : '';
        } else {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepPeer::PRO_UID, $sproUid );
            $oCriteria->add( StepPeer::STEP_UID_OBJ, $sObjUID );
            $oCriteria->add( StepPeer::STEP_TYPE_OBJ, 'DYNAFORM' );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            return ($aRow);
        }
        die();
    }

    public function getAttribute ($node, $attName)
    {

        foreach ($node->attributes as $attribute) {
            if ($attribute->name == $attName) {
                return $attribute->value;
            }

        }
    }

    /**
     * verify if a dbconnection is assigned in some dynaforms or triggers
     *
     * @param string $sproUid the uid of the process
     * @param string $sdbsUid the uid of the db connection
     */
    public function loadInfoAssigConnecctionDB ($sproUid, $sdbsUid)
    {
        require_once ("classes/model/DynaformPeer.php");
        G::LoadSystem( 'dynaformhandler' );
        $swDynaform = true;
        $swTriggers = true;
        //we are looking for triggers if there is at least one db connection
        $oC = new Criteria( 'workflow' );
        $oC->add( TriggersPeer::PRO_UID, $sproUid );
        $oDataset = TriggersPeer::doSelectRS( $oC );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        //print $sproUid;
        while ($aRowT = $oDataset->getRow()) {
            $uidConnection = preg_quote( $sdbsUid );
            if (strrpos( $uidConnection, $aRowT['TRI_WEBBOT'] )) {
                $swTriggers = false;
            }
            $oDataset->next();
        }
        //we are looking for dynaforms if there is at least one db connection
        $oC = new Criteria( 'workflow' );
        $oC->add( DynaformPeer::PRO_UID, $sproUid );
        $oDataset = DynaformPeer::doSelectRS( $oC );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            if ($aRow['DYN_TYPE'] == 'xmlform') {
                $dynHandler = new dynaFormHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . ".xml" );
                $dynFields = $dynHandler->getFields();
                $sxmlgrid = '';
                $sType = '';
                $check = 0;
                foreach ($dynFields as $field) {
                    $ssqlConnection = $this->getAttribute( $field, 'sqlconnection' );
                    if ($ssqlConnection == $sdbsUid)
                        $swDynaform = false;
                } //end foreach


            } //end if
            $oDataset->next();
        } //end while
        //is there a connecction?
        if ($swDynaform && $swTriggers) {
            //there is no db connection, you can delete this connection
            return true;
        } else {
            //there is a db connection, you can not delete this connection
            return false;
        }
        die();
    }

    /**
     * Get related steps for a determinated case
     *
     * @author Erik A. O. <erik@colosa.com>
     */
    public function getAllCaseSteps ($PRO_UID, $TAS_UID, $APP_UID)
    {

        $c = new Criteria();
        $c->addSelectColumn( '*' );
        $c->add( StepPeer::PRO_UID, $PRO_UID );
        $c->add( StepPeer::TAS_UID, $TAS_UID );
        $c->addAscendingOrderByColumn( StepPeer::STEP_POSITION );

        return StepPeer::doSelect( $c );
    }

    /**
     * Get the uids of the grids into a xml form
     *
     * @param string $sproUid the uid of the process
     * @param string $sdbsUid the uid of the db connection
     * @author krlos <carlos@colosa.com>
     */
    public function lookingforUidGrids ($sproUid, $sObjUID)
    {

        require_once ("classes/model/DynaformPeer.php");
        G::LoadSystem( 'dynaformhandler' );
        $uidsGrids = array ();
        $oC = new Criteria( 'workflow' );
        $oC->add( DynaformPeer::DYN_UID, $sObjUID );
        $oC->add( DynaformPeer::PRO_UID, $sproUid );
        $oDataset = DynaformPeer::doSelectRS( $oC );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();

        if ($aRow['DYN_TYPE'] == 'xmlform') {
            $oC1 = new Criteria( 'workflow' );
            $oC1->add( DynaformPeer::PRO_UID, $sproUid );
            $oC1->add( DynaformPeer::DYN_TYPE, "xmlform" );
            $oDataset = DynaformPeer::doSelectRS( $oC1 );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();

                $dynHandler = new dynaFormHandler( PATH_DYNAFORM . $sproUid . "/" . $sObjUID . ".xml" );
                $dynFields = $dynHandler->getFields();
                $sxmlgrid = '';
                $sType = '';
                $check = 0;
                foreach ($dynFields as $field) {
                    $sType = $this->getAttribute( $field, 'type' );

                    if ($sType == 'grid') {
                        $sxmlgrid = $this->getAttribute( $field, 'xmlgrid' );
                        //print_r($sxmlgrid);print"<hr>";
                        $aGridInfo = explode( "/", $sxmlgrid );
                        $uidsGrids[] = $aGridInfo[1];
                    }
                }
            }
            return ($uidsGrids);

        }
    }

}
// Step

