<?php

class Services_Rest_Configuration
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $cfgUid, $objUid, $proUid, $usrUid, $appUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($cfgUid=null, $objUid=null, $proUid=null, $usrUid=null, $appUid=null)
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

                $criteria->addSelectColumn(ConfigurationPeer::CFG_UID);
                $criteria->addSelectColumn(ConfigurationPeer::OBJ_UID);
                $criteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);
                $criteria->addSelectColumn(ConfigurationPeer::PRO_UID);
                $criteria->addSelectColumn(ConfigurationPeer::USR_UID);
                $criteria->addSelectColumn(ConfigurationPeer::APP_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ConfigurationPeer::retrieveByPK($cfgUid, $objUid, $proUid, $usrUid, $appUid);
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
                    throw new RestException(417, "table Configuration ($paramValues)" );
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
