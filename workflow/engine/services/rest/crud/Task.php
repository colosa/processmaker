<?php

class Services_Rest_Task
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $tasUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($tasUid=null)
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

                $criteria->addSelectColumn(TaskPeer::PRO_UID);
                $criteria->addSelectColumn(TaskPeer::TAS_UID);
                $criteria->addSelectColumn(TaskPeer::TAS_TYPE);
                $criteria->addSelectColumn(TaskPeer::TAS_DURATION);
                $criteria->addSelectColumn(TaskPeer::TAS_DELAY_TYPE);
                $criteria->addSelectColumn(TaskPeer::TAS_TEMPORIZER);
                $criteria->addSelectColumn(TaskPeer::TAS_TYPE_DAY);
                $criteria->addSelectColumn(TaskPeer::TAS_TIMEUNIT);
                $criteria->addSelectColumn(TaskPeer::TAS_ALERT);
                $criteria->addSelectColumn(TaskPeer::TAS_PRIORITY_VARIABLE);
                $criteria->addSelectColumn(TaskPeer::TAS_ASSIGN_TYPE);
                $criteria->addSelectColumn(TaskPeer::TAS_ASSIGN_VARIABLE);
                $criteria->addSelectColumn(TaskPeer::TAS_GROUP_VARIABLE);
                $criteria->addSelectColumn(TaskPeer::TAS_MI_INSTANCE_VARIABLE);
                $criteria->addSelectColumn(TaskPeer::TAS_MI_COMPLETE_VARIABLE);
                $criteria->addSelectColumn(TaskPeer::TAS_ASSIGN_LOCATION);
                $criteria->addSelectColumn(TaskPeer::TAS_ASSIGN_LOCATION_ADHOC);
                $criteria->addSelectColumn(TaskPeer::TAS_TRANSFER_FLY);
                $criteria->addSelectColumn(TaskPeer::TAS_LAST_ASSIGNED);
                $criteria->addSelectColumn(TaskPeer::TAS_USER);
                $criteria->addSelectColumn(TaskPeer::TAS_CAN_UPLOAD);
                $criteria->addSelectColumn(TaskPeer::TAS_VIEW_UPLOAD);
                $criteria->addSelectColumn(TaskPeer::TAS_VIEW_ADDITIONAL_DOCUMENTATION);
                $criteria->addSelectColumn(TaskPeer::TAS_CAN_CANCEL);
                $criteria->addSelectColumn(TaskPeer::TAS_OWNER_APP);
                $criteria->addSelectColumn(TaskPeer::STG_UID);
                $criteria->addSelectColumn(TaskPeer::TAS_CAN_PAUSE);
                $criteria->addSelectColumn(TaskPeer::TAS_CAN_SEND_MESSAGE);
                $criteria->addSelectColumn(TaskPeer::TAS_CAN_DELETE_DOCS);
                $criteria->addSelectColumn(TaskPeer::TAS_SELF_SERVICE);
                $criteria->addSelectColumn(TaskPeer::TAS_START);
                $criteria->addSelectColumn(TaskPeer::TAS_TO_LAST_USER);
                $criteria->addSelectColumn(TaskPeer::TAS_SEND_LAST_EMAIL);
                $criteria->addSelectColumn(TaskPeer::TAS_DERIVATION);
                $criteria->addSelectColumn(TaskPeer::TAS_POSX);
                $criteria->addSelectColumn(TaskPeer::TAS_POSY);
                $criteria->addSelectColumn(TaskPeer::TAS_WIDTH);
                $criteria->addSelectColumn(TaskPeer::TAS_HEIGHT);
                $criteria->addSelectColumn(TaskPeer::TAS_COLOR);
                $criteria->addSelectColumn(TaskPeer::TAS_EVN_UID);
                $criteria->addSelectColumn(TaskPeer::TAS_BOUNDARY);
                $criteria->addSelectColumn(TaskPeer::TAS_DERIVATION_SCREEN_TPL);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = TaskPeer::retrieveByPK($tasUid);
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
                    throw new RestException(417, "table Task ($paramValues)" );
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
