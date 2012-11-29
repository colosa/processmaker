<?php

class Services_Rest_Process
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $proUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($proUid=null)
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

                $criteria->addSelectColumn(ProcessPeer::PRO_UID);
                $criteria->addSelectColumn(ProcessPeer::PRO_PARENT);
                $criteria->addSelectColumn(ProcessPeer::PRO_TIME);
                $criteria->addSelectColumn(ProcessPeer::PRO_TIMEUNIT);
                $criteria->addSelectColumn(ProcessPeer::PRO_STATUS);
                $criteria->addSelectColumn(ProcessPeer::PRO_TYPE_DAY);
                $criteria->addSelectColumn(ProcessPeer::PRO_TYPE);
                $criteria->addSelectColumn(ProcessPeer::PRO_ASSIGNMENT);
                $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_MAP);
                $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_MESSAGE);
                $criteria->addSelectColumn(ProcessPeer::PRO_SUBPROCESS);
                $criteria->addSelectColumn(ProcessPeer::PRO_TRI_DELETED);
                $criteria->addSelectColumn(ProcessPeer::PRO_TRI_CANCELED);
                $criteria->addSelectColumn(ProcessPeer::PRO_TRI_PAUSED);
                $criteria->addSelectColumn(ProcessPeer::PRO_TRI_REASSIGNED);
                $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_DELEGATE);
                $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_DYNAFORM);
                $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
                $criteria->addSelectColumn(ProcessPeer::PRO_SUB_CATEGORY);
                $criteria->addSelectColumn(ProcessPeer::PRO_INDUSTRY);
                $criteria->addSelectColumn(ProcessPeer::PRO_UPDATE_DATE);
                $criteria->addSelectColumn(ProcessPeer::PRO_CREATE_DATE);
                $criteria->addSelectColumn(ProcessPeer::PRO_CREATE_USER);
                $criteria->addSelectColumn(ProcessPeer::PRO_HEIGHT);
                $criteria->addSelectColumn(ProcessPeer::PRO_WIDTH);
                $criteria->addSelectColumn(ProcessPeer::PRO_TITLE_X);
                $criteria->addSelectColumn(ProcessPeer::PRO_TITLE_Y);
                $criteria->addSelectColumn(ProcessPeer::PRO_DEBUG);
                $criteria->addSelectColumn(ProcessPeer::PRO_DYNAFORMS);
                $criteria->addSelectColumn(ProcessPeer::PRO_DERIVATION_SCREEN_TPL);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = ProcessPeer::retrieveByPK($proUid);
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
                    throw new RestException(417, "table Process ($paramValues)" );
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
     * @param  mixed $proUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($proUid, $proParent, $proTime, $proTimeunit, $proStatus, $proTypeDay, $proType, $proAssignment, $proShowMap, $proShowMessage, $proShowDelegate, $proShowDynaform, $proCategory, $proSubCategory, $proIndustry, $proUpdateDate, $proCreateDate, $proCreateUser, $proHeight, $proWidth, $proTitleX, $proTitleY, $proDebug, $proDynaforms, $proDerivationScreenTpl)
    {
        try {
            $result = array();
            $obj = new Process();

            $obj->setProUid($proUid);
            $obj->setProParent($proParent);
            $obj->setProTime($proTime);
            $obj->setProTimeunit($proTimeunit);
            $obj->setProStatus($proStatus);
            $obj->setProTypeDay($proTypeDay);
            $obj->setProType($proType);
            $obj->setProAssignment($proAssignment);
            $obj->setProShowMap($proShowMap);
            $obj->setProShowMessage($proShowMessage);
            $obj->setProShowDelegate($proShowDelegate);
            $obj->setProShowDynaform($proShowDynaform);
            $obj->setProCategory($proCategory);
            $obj->setProSubCategory($proSubCategory);
            $obj->setProIndustry($proIndustry);
            $obj->setProUpdateDate($proUpdateDate);
            $obj->setProCreateDate($proCreateDate);
            $obj->setProCreateUser($proCreateUser);
            $obj->setProHeight($proHeight);
            $obj->setProWidth($proWidth);
            $obj->setProTitleX($proTitleX);
            $obj->setProTitleY($proTitleY);
            $obj->setProDebug($proDebug);
            $obj->setProDynaforms($proDynaforms);
            $obj->setProDerivationScreenTpl($proDerivationScreenTpl);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $proUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($proUid, $proParent, $proTime, $proTimeunit, $proStatus, $proTypeDay, $proType, $proAssignment, $proShowMap, $proShowMessage, $proShowDelegate, $proShowDynaform, $proCategory, $proSubCategory, $proIndustry, $proUpdateDate, $proCreateDate, $proCreateUser, $proHeight, $proWidth, $proTitleX, $proTitleY, $proDebug, $proDynaforms, $proDerivationScreenTpl)
    {
        try {
            $obj = ProcessPeer::retrieveByPK($proUid);

            $obj->setProParent($proParent);
            $obj->setProTime($proTime);
            $obj->setProTimeunit($proTimeunit);
            $obj->setProStatus($proStatus);
            $obj->setProTypeDay($proTypeDay);
            $obj->setProType($proType);
            $obj->setProAssignment($proAssignment);
            $obj->setProShowMap($proShowMap);
            $obj->setProShowMessage($proShowMessage);
            $obj->setProShowDelegate($proShowDelegate);
            $obj->setProShowDynaform($proShowDynaform);
            $obj->setProCategory($proCategory);
            $obj->setProSubCategory($proSubCategory);
            $obj->setProIndustry($proIndustry);
            $obj->setProUpdateDate($proUpdateDate);
            $obj->setProCreateDate($proCreateDate);
            $obj->setProCreateUser($proCreateUser);
            $obj->setProHeight($proHeight);
            $obj->setProWidth($proWidth);
            $obj->setProTitleX($proTitleX);
            $obj->setProTitleY($proTitleY);
            $obj->setProDebug($proDebug);
            $obj->setProDynaforms($proDynaforms);
            $obj->setProDerivationScreenTpl($proDerivationScreenTpl);
            
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $proUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($proUid)
    {
        $conn = Propel::getConnection(ProcessPeer::DATABASE_NAME);
        
        try {
            $conn->begin();
        
            $obj = ProcessPeer::retrieveByPK($proUid);
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
