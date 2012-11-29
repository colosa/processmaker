<?php

class Services_Rest_AdditionalTables
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $addTabUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($addTabUid=null)
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

                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_CLASS_NAME);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_PLG_UID);
                $criteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);
                $criteria->addSelectColumn(AdditionalTablesPeer::PRO_UID);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TYPE);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_GRID);
                $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TAG);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AdditionalTablesPeer::retrieveByPK($addTabUid);
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
                    throw new RestException(417, "table AdditionalTables ($paramValues)" );
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
