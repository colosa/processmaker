<?php
/**
 * SubProcess.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseSubProcess.php';

/**
 * Skeleton subclass for representing a row from the 'SUB_PROCESS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class SubProcess extends BaseSubProcess
{

    public function load ($SP_UID)
    {
        try {
            $oRow = SubProcessPeer::retrieveByPK( $SP_UID );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '$SP_UID' in table SubProcess doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $con = Propel::getConnection( SubProcessPeer::DATABASE_NAME );
        try {
            $con->begin();
            if (isset( $aData['SP_UID'] ) && $aData['SP_UID'] == '') {
                unset( $aData['SP_UID'] );
            }
            if (! isset( $aData['SP_UID'] )) {
                $this->setSpUid( G::generateUniqueID() );
            } else {
                $this->setSpUid( $aData['SP_UID'] );
            }

            $this->setProUid( $aData['PRO_UID'] );

            $this->setTasUid( $aData['TAS_UID'] );

            $this->setProParent( $aData['PRO_PARENT'] );

            $this->setTasParent( $aData['TAS_PARENT'] );

            $this->setSpType( $aData['SP_TYPE'] );

            $this->setSpSynchronous( $aData['SP_SYNCHRONOUS'] );

            $this->setSpSynchronousType( $aData['SP_SYNCHRONOUS_TYPE'] );

            $this->setSpSynchronousWait( $aData['SP_SYNCHRONOUS_WAIT'] );

            $this->setSpVariablesOut( $aData['SP_VARIABLES_OUT'] );

            $this->setSpVariablesIn( $aData['SP_VARIABLES_IN'] );

            $this->setSpGridIn( $aData['SP_GRID_IN'] );

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

    public function update ($fields)
    {
        $con = Propel::getConnection( SubProcessPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['SP_UID'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                $validationE = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $validationE->aValidationFailures = $this->getValidationFailures();
                throw ($validationE);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove ($SP_UID)
    {
        $con = Propel::getConnection( SubProcessPeer::DATABASE_NAME );
        try {
            $con->begin();
            $oRepTab = SubProcessPeer::retrieveByPK( $SP_UID );
            if (! is_null( $oRepTab )) {
                $result = $oRepTab->delete();
                $con->commit();
            }
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * verify if Trigger row specified in [sUid] exists.
     *
     * @param string $sUid the uid of the Prolication
     */

    public function subProcessExists ($sUid)
    {
        $con = Propel::getConnection( SubProcessPeer::DATABASE_NAME );
        try {
            $oObj = SubProcessPeer::retrieveByPk( $sUid );
            if (is_object( $oObj ) && get_class( $oObj ) == 'SubProcess') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}

