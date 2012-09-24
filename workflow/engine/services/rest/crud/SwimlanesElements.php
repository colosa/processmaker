<?php

class Services_Rest_SwimlanesElements
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $swiUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($swiUid=null)
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

                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_UID);
                $criteria->addSelectColumn(SwimlanesElementsPeer::PRO_UID);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_TYPE);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_X);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_Y);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_WIDTH);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_HEIGHT);
                $criteria->addSelectColumn(SwimlanesElementsPeer::SWI_NEXT_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = SwimlanesElementsPeer::retrieveByPK($swiUid);
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
                    throw new RestException(417, "table SwimlanesElements ($paramValues)" );
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
