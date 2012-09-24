<?php

class Services_Rest_ObjectPermission
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $opUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($opUid=null)
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

                $criteria->addSelectColumn(ObjectPermissionPeer::OP_UID);
                $criteria->addSelectColumn(ObjectPermissionPeer::PRO_UID);
                $criteria->addSelectColumn(ObjectPermissionPeer::TAS_UID);
                $criteria->addSelectColumn(ObjectPermissionPeer::USR_UID);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_USER_RELATION);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_TASK_SOURCE);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_PARTICIPATE);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_OBJ_TYPE);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_OBJ_UID);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_ACTION);
                $criteria->addSelectColumn(ObjectPermissionPeer::OP_CASE_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ObjectPermissionPeer::retrieveByPK($opUid);
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
                    throw new RestException(417, "table ObjectPermission ($paramValues)" );
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
