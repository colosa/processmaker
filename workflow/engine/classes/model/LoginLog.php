<?php
/**
 * LoginLog.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseLoginLog.php';

/**
 * Skeleton subclass for representing a row from the 'LOGIN_LOG' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class LoginLog extends BaseLoginLog
{
    public function create ($aData)
    {
        $con = Propel::getConnection( LoginLogPeer::DATABASE_NAME );
        try {
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function load ($LogUid)
    {
        try {
            $oRow = LoginLogPeer::retrieveByPK( $LogUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $LogUid . "' in table LOGIN_LOG doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function update ($fields)
    {
        $con = Propel::getConnection( LoginLogPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['LOG_UID'] );
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

    public function remove ($LogUid)
    {
        $con = Propel::getConnection( LoginLogPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setWlUid( $LogUid );
            $result = $this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    //Added by Qennix
    public function getLastLoginByUser ($sUID)
    {
        $c = new Criteria();
        $c->addSelectColumn( LoginLogPeer::LOG_INIT_DATE );
        $c->add( LoginLogPeer::USR_UID, $sUID );
        $c->setLimit( 1 );
        $c->addDescendingOrderByColumn( LoginLogPeer::LOG_INIT_DATE );
        $Dat = LoginLogPeer::doSelectRS( $c );
        $Dat->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $Dat->next();
        $aRow = $Dat->getRow();
        return isset( $aRow['LOG_INIT_DATE'] ) ? $aRow['LOG_INIT_DATE'] : '';
    }

    //Added by Qennix
    public function getLastLoginAllUsers ()
    {
        $c = new Criteria();
        $c->addSelectColumn( LoginLogPeer::USR_UID );
        $c->addAsColumn( 'LAST_LOGIN', 'MAX(LOG_INIT_DATE)' );
        $c->addGroupByColumn( LoginLogPeer::USR_UID );
        $Dat = LoginLogPeer::doSelectRS( $c );
        $Dat->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRows = Array ();
        while ($Dat->next()) {
            $row = $Dat->getRow();
            $aRows[$row['USR_UID']] = $row['LAST_LOGIN'];
        }
        return $aRows;
    }
}

