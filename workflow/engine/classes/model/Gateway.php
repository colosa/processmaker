<?php
/**
 * Gateway.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseGateway.php';

/**
 * Skeleton subclass for representing a row from the 'GATEWAY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Gateway extends BaseGateway
{

    public function create ($aData)
    {
        $oConnection = Propel::getConnection( GatewayPeer::DATABASE_NAME );
        try {
            $sGatewayUID = G::generateUniqueID();
            $aData['GAT_UID'] = $sGatewayUID;
            $oGateway = new Gateway();
            $oGateway->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oGateway->validate()) {
                $oConnection->begin();
                $iResult = $oGateway->save();
                $oConnection->commit();
                return $sGatewayUID;
            } else {
                $sMessage = '';
                $aValidationFailures = $oGateway->getValidationFailures();
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

    public function load ($GatewayUid)
    {
        try {
            $oRow = GatewayPeer::retrieveByPK( $GatewayUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $GatewayUid . "' in table Gateway doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function update ($fields)
    {
        $con = Propel::getConnection( GatewayPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['GAT_UID'] );
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

    public function remove ($GatewayUid)
    {
        $oConnection = Propel::getConnection( GatewayPeer::DATABASE_NAME );
        try {
            $oGateWay = GatewayPeer::retrieveByPK( $GatewayUid );
            if (! is_null( $oGateWay )) {
                $oConnection->begin();
                $iResult = $oGateWay->delete();
                $oConnection->commit();
                //return $iResult;
                return true;
            } else {
                throw (new Exception( 'This row does not exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * verify if Gateway row specified in [GatUid] exists.
     *
     * @param string $sProUid the uid of the Prolication
     */

    public function gatewayExists ($GatUid)
    {
        $con = Propel::getConnection( GatewayPeer::DATABASE_NAME );
        try {
            $oPro = GatewayPeer::retrieveByPk( $GatUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Gateway') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * create a new Gateway
     *
     * @param array $aData with new values
     * @return void
     */
    public function createRow ($aData)
    {
        $con = Propel::getConnection( GatewayPeer::DATABASE_NAME );
        try {
            $con->begin();

            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $this->setGatUid( (isset( $aData['GAT_UID'] ) ? $aData['GAT_UID'] : '') );
                $this->setProUid( (isset( $aData['PRO_UID'] ) ? $aData['PRO_UID'] : '') );
                $this->setTasUid( (isset( $aData['TAS_UID'] ) ? $aData['TAS_UID'] : '') );
                $this->setGatNextTask( (isset( $aData['GAT_NEXT_TASK'] ) ? $aData['GAT_NEXT_TASK'] : '') );
                $this->setGatX( (isset( $aData['GAT_X'] ) ? $aData['GAT_X'] : '') );
                $this->setGatY( (isset( $aData['GAT_Y'] ) ? $aData['GAT_Y'] : '') );
                $this->save();
                $con->commit();
                return;
            } else {
                $con->rollback();
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
}
// Gateway

