<?php

class Services_Rest_ShadowTable
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $shdUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($shdUid=null)
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

                $criteria->addSelectColumn(ShadowTablePeer::SHD_UID);
                $criteria->addSelectColumn(ShadowTablePeer::ADD_TAB_UID);
                $criteria->addSelectColumn(ShadowTablePeer::SHD_ACTION);
                $criteria->addSelectColumn(ShadowTablePeer::SHD_DETAILS);
                $criteria->addSelectColumn(ShadowTablePeer::USR_UID);
                $criteria->addSelectColumn(ShadowTablePeer::APP_UID);
                $criteria->addSelectColumn(ShadowTablePeer::SHD_DATE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ShadowTablePeer::retrieveByPK($shdUid);
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
                    throw new RestException(417, "table ShadowTable ($paramValues)" );
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
