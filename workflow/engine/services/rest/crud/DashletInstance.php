<?php

class Services_Rest_DashletInstance
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $dasInsUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($dasInsUid=null)
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

                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_UID);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_OWNER_TYPE);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_OWNER_UID);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_CREATE_DATE);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UPDATE_DATE);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = DashletInstancePeer::retrieveByPK($dasInsUid);
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
                    throw new RestException(417, "table DashletInstance ($paramValues)" );
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
