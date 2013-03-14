<?php

class Services_Rest_Session
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $sesUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($sesUid=null)
    {
        $result = array();
        try {
            $noArguments = true;
            $argumentList = func_get_args();
            foreach ($argumentList as $arg) {
                if (!is_null($arg)) {
                    $noArguments = false;
                }
            }

            if ($noArguments) {
                $criteria = new Criteria('workflow');

                $criteria->addSelectColumn(SessionPeer::SES_UID);
                $criteria->addSelectColumn(SessionPeer::SES_STATUS);
                $criteria->addSelectColumn(SessionPeer::USR_UID);
                $criteria->addSelectColumn(SessionPeer::SES_REMOTE_IP);
                $criteria->addSelectColumn(SessionPeer::SES_INIT_DATE);
                $criteria->addSelectColumn(SessionPeer::SES_DUE_DATE);
                $criteria->addSelectColumn(SessionPeer::SES_END_DATE);

                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = SessionPeer::retrieveByPK($sesUid);
                if ($record) {
                    $result = $record->toArray(BasePeer::TYPE_FIELDNAME);
                } else {
                    $paramValues = "";
                    foreach ($argumentList as $arg) {
                        $paramValues .= (strlen($paramValues) ) ? ', ' : '';
                        if (!is_null($arg)) {
                            $paramValues .= "$arg";
                        } else {
                            $paramValues .= "NULL";
                        }
                    }
                    throw new RestException(417, "table Session ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }

    /**
     * Implementation for 'POST' method for Rest API
     *
     * @param  mixed $sesUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($sesUid, $sesStatus, $usrUid, $sesRemoteIp, $sesInitDate, $sesDueDate, $sesEndDate)
    {
        try {
            $result = array();
            $obj = new Session();

            $obj->setSesUid($sesUid);
            $obj->setSesStatus($sesStatus);
            $obj->setUsrUid($usrUid);
            $obj->setSesRemoteIp($sesRemoteIp);
            $obj->setSesInitDate($sesInitDate);
            $obj->setSesDueDate($sesDueDate);
            $obj->setSesEndDate($sesEndDate);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $sesUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($sesUid, $sesStatus, $usrUid, $sesRemoteIp, $sesInitDate, $sesDueDate, $sesEndDate)
    {
        try {
            $obj = SessionPeer::retrieveByPK($sesUid);

            $obj->setSesStatus($sesStatus);
            $obj->setUsrUid($usrUid);
            $obj->setSesRemoteIp($sesRemoteIp);
            $obj->setSesInitDate($sesInitDate);
            $obj->setSesDueDate($sesDueDate);
            $obj->setSesEndDate($sesEndDate);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $sesUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($sesUid)
    {
        $conn = Propel::getConnection(SessionPeer::DATABASE_NAME);

        try {
            $conn->begin();

            $obj = SessionPeer::retrieveByPK($sesUid);
            if (! is_object($obj)) {
                throw new RestException(412, G::LoadTranslation('ID_RECORD_DOES_NOT_EXIST'));
            }
            $obj->delete();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw new RestException(412, $e->getMessage());
        }
    }


}
