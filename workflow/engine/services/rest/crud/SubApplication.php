<?php

class Services_Rest_SubApplication
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appUid, $appParent, $delIndexParent, $delThreadParent Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appUid=null, $appParent=null, $delIndexParent=null, $delThreadParent=null)
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

                $criteria->addSelectColumn(SubApplicationPeer::APP_UID);
                $criteria->addSelectColumn(SubApplicationPeer::APP_PARENT);
                $criteria->addSelectColumn(SubApplicationPeer::DEL_INDEX_PARENT);
                $criteria->addSelectColumn(SubApplicationPeer::DEL_THREAD_PARENT);
                $criteria->addSelectColumn(SubApplicationPeer::SA_STATUS);
                $criteria->addSelectColumn(SubApplicationPeer::SA_VALUES_OUT);
                $criteria->addSelectColumn(SubApplicationPeer::SA_VALUES_IN);
                $criteria->addSelectColumn(SubApplicationPeer::SA_INIT_DATE);
                $criteria->addSelectColumn(SubApplicationPeer::SA_FINISH_DATE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = SubApplicationPeer::retrieveByPK($appUid, $appParent, $delIndexParent, $delThreadParent);
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
                    throw new RestException(417, "table SubApplication ($paramValues)" );
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
