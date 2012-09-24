<?php

class Services_Rest_Fields
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $fldUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($fldUid=null)
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

                $criteria->addSelectColumn(FieldsPeer::FLD_UID);
                $criteria->addSelectColumn(FieldsPeer::ADD_TAB_UID);
                $criteria->addSelectColumn(FieldsPeer::FLD_INDEX);
                $criteria->addSelectColumn(FieldsPeer::FLD_NAME);
                $criteria->addSelectColumn(FieldsPeer::FLD_DESCRIPTION);
                $criteria->addSelectColumn(FieldsPeer::FLD_TYPE);
                $criteria->addSelectColumn(FieldsPeer::FLD_SIZE);
                $criteria->addSelectColumn(FieldsPeer::FLD_NULL);
                $criteria->addSelectColumn(FieldsPeer::FLD_AUTO_INCREMENT);
                $criteria->addSelectColumn(FieldsPeer::FLD_KEY);
                $criteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY);
                $criteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY_TABLE);
                $criteria->addSelectColumn(FieldsPeer::FLD_DYN_NAME);
                $criteria->addSelectColumn(FieldsPeer::FLD_DYN_UID);
                $criteria->addSelectColumn(FieldsPeer::FLD_FILTER);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = FieldsPeer::retrieveByPK($fldUid);
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
                    throw new RestException(417, "table Fields ($paramValues)" );
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
