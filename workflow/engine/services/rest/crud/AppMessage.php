<?php

class Services_Rest_AppMessage
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appMsgUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appMsgUid=null)
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

                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_UID);
                $criteria->addSelectColumn(AppMessagePeer::MSG_UID);
                $criteria->addSelectColumn(AppMessagePeer::APP_UID);
                $criteria->addSelectColumn(AppMessagePeer::DEL_INDEX);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TYPE);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SUBJECT);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_FROM);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TO);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_BODY);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_DATE);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_CC);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_BCC);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TEMPLATE);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_STATUS);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_ATTACH);
                $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SEND_DATE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppMessagePeer::retrieveByPK($appMsgUid);
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
                    throw new RestException(417, "table AppMessage ($paramValues)" );
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
