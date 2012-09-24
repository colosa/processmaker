<?php

class Services_Rest_Department
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $depUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($depUid=null)
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

                $criteria->addSelectColumn(DepartmentPeer::DEP_UID);
                $criteria->addSelectColumn(DepartmentPeer::DEP_PARENT);
                $criteria->addSelectColumn(DepartmentPeer::DEP_MANAGER);
                $criteria->addSelectColumn(DepartmentPeer::DEP_LOCATION);
                $criteria->addSelectColumn(DepartmentPeer::DEP_STATUS);
                $criteria->addSelectColumn(DepartmentPeer::DEP_REF_CODE);
                $criteria->addSelectColumn(DepartmentPeer::DEP_LDAP_DN);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = DepartmentPeer::retrieveByPK($depUid);
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
                    throw new RestException(417, "table Department ($paramValues)" );
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
