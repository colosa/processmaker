<?php

require_once 'classes/model/om/BaseNotificationDevice.php';


/**
 * Skeleton subclass for representing a row from the 'NOTIFICATION_DEVICE' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class NotificationDevice extends BaseNotificationDevice {

    public function create(array $arrayData)
    {

        $cnn = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);

        try {
            $this->setDevUid(G::generateUniqueID());
            $this->setUsrUid($arrayData['USR_UID']);
            $this->setSysLang($arrayData['SYS_LANG']);
            $this->setDevRegId($arrayData['DEV_REG_ID']);
            $this->setDevType($arrayData['DEV_TYPE']);
            $this->setDevCreate('now');
            $this->setDevUpdate('now');

            if ($this->validate()) {
                $cnn->begin();
                $result = $this->save();
                $cnn->commit();
            } else {
                throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
            }
        } catch (Exception $e) {
            $cnn->rollback();
            throw $e;
        }
        return $result;
    }

    public function createOrUpdate(array $arrayData)
    {
        $cnn = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
        try {
            if (!isset($arrayData['DEV_UID'])) {
                $arrayData['DEV_UID'] = G::generateUniqueID();
                $arrayData['DEV_CREATE'] = date('Y-m-d H:i:s');
                $arrayData['DEV_UPDATE'] = date('Y-m-d H:i:s');
                $mNotification = new NotificationDevice();
            } else {
                $arrayData['DEV_UPDATE'] = date('Y-m-d H:i:s');
                $mNotification = NotificationDevicePeer::retrieveByPK($arrayData['DEV_UID'],$arrayData['USR_UID']);
            }
            $mNotification->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);
            if ($mNotification->validate()) {
                $cnn->begin();
                $result = $mNotification->save();
                $cnn->commit();
            } else {
                throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
            }
        } catch (Exception $e) {
            $cnn->rollback();
            throw $e;
        }
        return isset($arrayData['DEV_UID']) ? $arrayData['DEV_UID'] : 0;
    }

    public function update(array $arrayData)
    {

        $cnn = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
        $rs = NotificationDevicePeer::retrieveByPK($arrayData['DEV_UID'],$arrayData['USR_UID']);
        try {
            $arrayData['DEV_UPDATE'] = date('Y-m-d H:i:s');
            $rs->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $cnn->begin();
                $result = $rs->save();
                $cnn->commit();
            } else {
                throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
            }
        } catch (Exception $e) {
            $cnn->rollback();
            throw $e;
        }
        return $result;
    }

    public function loadByDeviceId ($deviceId)
    {
        try {
            $criteria = new Criteria();
            $criteria->clearSelectColumns();
            $criteria->add(NotificationDevicePeer::DEV_REG_ID, $deviceId, Criteria::EQUAL);

            $rs = NotificationDevicePeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $device = array();
            while ($rs->next()) {
                $row = $rs->getRow();
                $device[] = $row;
            }
        } catch (Exception $error) {
            throw $error;
        }
        return $device;
    }

    public function loadByUsersId ($userId)
    {
        try {
            $criteria = new Criteria();
            $criteria->clearSelectColumns();
            $criteria->add(NotificationDevicePeer::USR_UID, $userId, Criteria::EQUAL);

            $rs = NotificationDevicePeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $device = array();
            while ($rs->next()) {
                $row = $rs->getRow();
                $device[] = $row;
            }
        } catch (Exception $error) {
            throw $error;
        }
        return $device;
    }

    public function getAll ()
    {
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( NotificationDevicePeer::DEV_UID );
        $oCriteria->addSelectColumn( NotificationDevicePeer::USR_UID );
        $oCriteria->addSelectColumn( NotificationDevicePeer::DEV_TYPE );
        $oCriteria->addSelectColumn( NotificationDevicePeer::DEV_REG_ID );
        $oCriteria->addSelectColumn( NotificationDevicePeer::DEV_CREATE );

        //execute the query
        $oDataset = NotificationDevicePeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRows = array ();
        while ($oDataset->next()) {
            $aRows[] = $oDataset->getRow();
        }
        return $aRows;
    }

    public function remove($devUid, $usrUid)
    {
        if (!$this->exists($devUid, $usrUid)) {
            throw new Exception(G::LoadTranslation("ID_RECORD_DOES_NOT_EXIST"));
        }

        $result = array();
        $this->setDevUid($devUid);
        $this->setUsrUid($usrUid);
        $this->delete();
        if ($this->isDeleted()) {
            $result["message"] = G::LoadTranslation("ID_DELETED_SUCCESSFULLY");
        }
        return $result;
    }

    public function exists($devUid, $usrUid)
    {
        $oRow = NotificationDevicePeer::retrieveByPK($devUid, $usrUid);
        return (( get_class ($oRow) == 'NotificationDevice' )&&(!is_null($oRow)));
    }

} // NotificationDevice
