<?php

class Services_Rest_LoginLog
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $logUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($logUid=null)
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

                $criteria->addSelectColumn(LoginLogPeer::LOG_UID);
                $criteria->addSelectColumn(LoginLogPeer::LOG_STATUS);
                $criteria->addSelectColumn(LoginLogPeer::LOG_IP);
                $criteria->addSelectColumn(LoginLogPeer::LOG_SID);
                $criteria->addSelectColumn(LoginLogPeer::LOG_INIT_DATE);
                $criteria->addSelectColumn(LoginLogPeer::LOG_END_DATE);
                $criteria->addSelectColumn(LoginLogPeer::LOG_CLIENT_HOSTNAME);
                $criteria->addSelectColumn(LoginLogPeer::USR_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = LoginLogPeer::retrieveByPK($logUid);
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
                    throw new RestException(417, "table LoginLog ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }


}
