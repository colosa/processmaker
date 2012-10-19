<?php
/**
 * ObjectPermission.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseObjectPermission.php';

/**
 * Skeleton subclass for representing a row from the 'OBJECT_PERMISSION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class ObjectPermission extends BaseObjectPermission
{

    public function load ($UID)
    {
        try {
            $oRow = ObjectPermissionPeer::retrieveByPK( $UID );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $UsrUid . "' in table USER doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    function create ($aData)
    {
        try {
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $result = $this->save();
            return $result;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    function Exists ($Uid)
    {
        try {
            $oPro = ObjectPermissionPeer::retrieveByPk( $Uid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'ObjectPermission') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    function remove ($Uid)
    {
        $con = Propel::getConnection( ObjectPermissionPeer::DATABASE_NAME );
        try {
            $oObjPer = ObjectPermissionPeer::retrieveByPK( $Uid );
            if (is_object( $oObjPer ) && get_class( $oObjPer ) == 'ObjectPermission') {
                $con->begin();
                $iResult = $oObjPer->delete();
                $con->commit();
                return $iResult;
            } else {
                throw (new Exception( "The row '" . $Uid . "' in table CaseTrackerObject doesn't exist!" ));
            }
        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    function update ($aFields)
    {
        $oConnection = Propel::getConnection( ObjectPermissionPeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->load( $aFields['OP_UID'] );
            $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $iResult = $this->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $oConnection->rollback();
                throw (new Exception( 'Failed Validation in class ' . get_class( $this ) . '.' ));
            }
        } catch (Exception $e) {
            $oConnection->rollback();
            throw ($e);
        }
    }

    function removeByObject ($sType, $sObjUid)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ObjectPermissionPeer::OP_OBJ_TYPE, $sType );
            $oCriteria->add( ObjectPermissionPeer::OP_OBJ_UID, $sObjUid );
            ObjectPermissionPeer::doDelete( $oCriteria );
        } catch (Exception $e) {
            throw ($e);
        }
    }

    function loadInfo ($sObjUID)
    {

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( ObjectPermissionPeer::OP_OBJ_UID, $sObjUID );
        $oDataset = ObjectPermissionPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return ($aRow);
    }
}
// ObjectPermission

