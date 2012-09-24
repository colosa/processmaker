<?php

class Services_Rest_Route
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $rouUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($rouUid=null)
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

                $criteria->addSelectColumn(RoutePeer::ROU_UID);
                $criteria->addSelectColumn(RoutePeer::ROU_PARENT);
                $criteria->addSelectColumn(RoutePeer::PRO_UID);
                $criteria->addSelectColumn(RoutePeer::TAS_UID);
                $criteria->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
                $criteria->addSelectColumn(RoutePeer::ROU_CASE);
                $criteria->addSelectColumn(RoutePeer::ROU_TYPE);
                $criteria->addSelectColumn(RoutePeer::ROU_CONDITION);
                $criteria->addSelectColumn(RoutePeer::ROU_TO_LAST_USER);
                $criteria->addSelectColumn(RoutePeer::ROU_OPTIONAL);
                $criteria->addSelectColumn(RoutePeer::ROU_SEND_EMAIL);
                $criteria->addSelectColumn(RoutePeer::ROU_SOURCEANCHOR);
                $criteria->addSelectColumn(RoutePeer::ROU_TARGETANCHOR);
                $criteria->addSelectColumn(RoutePeer::ROU_TO_PORT);
                $criteria->addSelectColumn(RoutePeer::ROU_FROM_PORT);
                $criteria->addSelectColumn(RoutePeer::ROU_EVN_UID);
                $criteria->addSelectColumn(RoutePeer::GAT_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = RoutePeer::retrieveByPK($rouUid);
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
                    throw new RestException(417, "table Route ($paramValues)" );
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
