<?php

class Services_Rest_Application
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appUid=null)
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

                $criteria->addSelectColumn(ApplicationPeer::APP_UID);
                $criteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
                $criteria->addSelectColumn(ApplicationPeer::APP_PARENT);
                $criteria->addSelectColumn(ApplicationPeer::APP_STATUS);
                $criteria->addSelectColumn(ApplicationPeer::PRO_UID);
                $criteria->addSelectColumn(ApplicationPeer::APP_PROC_STATUS);
                $criteria->addSelectColumn(ApplicationPeer::APP_PROC_CODE);
                $criteria->addSelectColumn(ApplicationPeer::APP_PARALLEL);
                $criteria->addSelectColumn(ApplicationPeer::APP_INIT_USER);
                $criteria->addSelectColumn(ApplicationPeer::APP_CUR_USER);
                $criteria->addSelectColumn(ApplicationPeer::APP_CREATE_DATE);
                $criteria->addSelectColumn(ApplicationPeer::APP_INIT_DATE);
                $criteria->addSelectColumn(ApplicationPeer::APP_FINISH_DATE);
                $criteria->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
                $criteria->addSelectColumn(ApplicationPeer::APP_DATA);
                $criteria->addSelectColumn(ApplicationPeer::APP_PIN);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ApplicationPeer::retrieveByPK($appUid);
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
                    throw new RestException(417, "table Application ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }

    /**
     * Implementation for 'POST' method for Rest API
     *
     * @param  mixed $appUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($appUid, $appNumber, $appParent, $appStatus, $proUid, $appProcStatus, $appProcCode, $appParallel, $appInitUser, $appCurUser, $appCreateDate, $appInitDate, $appFinishDate, $appUpdateDate, $appData, $appPin)
    {
        try {
            $result = array();
            $obj = new Application();

            $obj->setAppUid($appUid);
            $obj->setAppNumber($appNumber);
            $obj->setAppParent($appParent);
            $obj->setAppStatus($appStatus);
            $obj->setProUid($proUid);
            $obj->setAppProcStatus($appProcStatus);
            $obj->setAppProcCode($appProcCode);
            $obj->setAppParallel($appParallel);
            $obj->setAppInitUser($appInitUser);
            $obj->setAppCurUser($appCurUser);
            $obj->setAppCreateDate($appCreateDate);
            $obj->setAppInitDate($appInitDate);
            $obj->setAppFinishDate($appFinishDate);
            $obj->setAppUpdateDate($appUpdateDate);
            $obj->setAppData($appData);
            $obj->setAppPin($appPin);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $appUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($appUid, $appNumber, $appParent, $appStatus, $proUid, $appProcStatus, $appProcCode, $appParallel, $appInitUser, $appCurUser, $appCreateDate, $appInitDate, $appFinishDate, $appUpdateDate, $appData, $appPin)
    {
        try {
            $obj = ApplicationPeer::retrieveByPK($appUid);

            $obj->setAppNumber($appNumber);
            $obj->setAppParent($appParent);
            $obj->setAppStatus($appStatus);
            $obj->setProUid($proUid);
            $obj->setAppProcStatus($appProcStatus);
            $obj->setAppProcCode($appProcCode);
            $obj->setAppParallel($appParallel);
            $obj->setAppInitUser($appInitUser);
            $obj->setAppCurUser($appCurUser);
            $obj->setAppCreateDate($appCreateDate);
            $obj->setAppInitDate($appInitDate);
            $obj->setAppFinishDate($appFinishDate);
            $obj->setAppUpdateDate($appUpdateDate);
            $obj->setAppData($appData);
            $obj->setAppPin($appPin);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $appUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($appUid)
    {
        $conn = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
        
        try {
            $conn->begin();
        
            $obj = ApplicationPeer::retrieveByPK($appUid);
            if (! is_object($obj)) {
                throw new RestException(412, 'Record does not exist.');
            }
            $obj->delete();
        
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw new RestException(412, $e->getMessage());
        }
    }


}
