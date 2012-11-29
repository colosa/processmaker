<?php

class Services_Rest_AppNotes
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed  Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get()
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

                $criteria->addSelectColumn(AppNotesPeer::APP_UID);
                $criteria->addSelectColumn(AppNotesPeer::USR_UID);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_DATE);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_CONTENT);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_TYPE);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_AVAILABILITY);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_ORIGIN_OBJ);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_AFFECTED_OBJ1);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_AFFECTED_OBJ2);
                $criteria->addSelectColumn(AppNotesPeer::NOTE_RECIPIENTS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppNotesPeer::retrieveByPK();
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
                    throw new RestException(417, "table AppNotes ($paramValues)" );
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
