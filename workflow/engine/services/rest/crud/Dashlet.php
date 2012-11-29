<?php

class Services_Rest_Dashlet
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $dasUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($dasUid=null)
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

                $criteria->addSelectColumn(DashletPeer::DAS_UID);
                $criteria->addSelectColumn(DashletPeer::DAS_CLASS);
                $criteria->addSelectColumn(DashletPeer::DAS_TITLE);
                $criteria->addSelectColumn(DashletPeer::DAS_DESCRIPTION);
                $criteria->addSelectColumn(DashletPeer::DAS_VERSION);
                $criteria->addSelectColumn(DashletPeer::DAS_CREATE_DATE);
                $criteria->addSelectColumn(DashletPeer::DAS_UPDATE_DATE);
                $criteria->addSelectColumn(DashletPeer::DAS_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = DashletPeer::retrieveByPK($dasUid);
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
                    throw new RestException(417, "table Dashlet ($paramValues)" );
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
