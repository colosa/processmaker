<?php

class Services_Rest_ReportTable
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $repTabUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($repTabUid=null)
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

                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_UID);
                $criteria->addSelectColumn(ReportTablePeer::PRO_UID);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_NAME);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_TYPE);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_GRID);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_CONNECTION);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_CREATE_DATE);
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ReportTablePeer::retrieveByPK($repTabUid);
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
                    throw new RestException(417, "table ReportTable ($paramValues)" );
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
