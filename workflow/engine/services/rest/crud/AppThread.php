<?php

class Services_Rest_AppThread
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appUid, $appThreadIndex Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appUid=null, $appThreadIndex=null)
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

                $criteria->addSelectColumn(AppThreadPeer::APP_UID);
                $criteria->addSelectColumn(AppThreadPeer::APP_THREAD_INDEX);
                $criteria->addSelectColumn(AppThreadPeer::APP_THREAD_PARENT);
                $criteria->addSelectColumn(AppThreadPeer::APP_THREAD_STATUS);
                $criteria->addSelectColumn(AppThreadPeer::DEL_INDEX);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppThreadPeer::retrieveByPK($appUid, $appThreadIndex);
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
                    throw new RestException(417, "table AppThread ($paramValues)" );
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
     * @param  mixed $appUid, $appThreadIndex Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($appUid, $appThreadIndex, $appThreadParent, $appThreadStatus, $delIndex)
    {
        try {
            $result = array();
            $obj = new AppThread();

            $obj->setAppUid($appUid);
            $obj->setAppThreadIndex($appThreadIndex);
            $obj->setAppThreadParent($appThreadParent);
            $obj->setAppThreadStatus($appThreadStatus);
            $obj->setDelIndex($delIndex);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $appUid, $appThreadIndex Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($appUid, $appThreadIndex, $appThreadParent, $appThreadStatus, $delIndex)
    {
        try {
            $obj = AppThreadPeer::retrieveByPK($appUid, $appThreadIndex);

            $obj->setAppThreadParent($appThreadParent);
            $obj->setAppThreadStatus($appThreadStatus);
            $obj->setDelIndex($delIndex);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $appUid, $appThreadIndex Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($appUid, $appThreadIndex)
    {
        $conn = Propel::getConnection(AppThreadPeer::DATABASE_NAME);
        
        try {
            $conn->begin();
        
            $obj = AppThreadPeer::retrieveByPK($appUid, $appThreadIndex);
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
