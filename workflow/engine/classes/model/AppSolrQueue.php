<?php

require_once 'classes/model/om/BaseAppSolrQueue.php';
require_once 'classes/entities/AppSolrQueue.php';


/**
 * Skeleton subclass for representing a row from the 'APP_SOLR_QUEUE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppSolrQueue extends BaseAppSolrQueue
{
    public function exists($sAppUid)
    {
        try {
            $oRow = AppSolrQueuePeer::retrieveByPK( $sAppUid );
            if (!is_null($oRow)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            return false;
        }
    }

    public function createUpdate($sAppUid, $iUpdated)
    {
        $con = Propel::getConnection(AppSolrQueuePeer::DATABASE_NAME);
        try {
            if ($this->exists($sAppUid)) {
                $con->begin();
                //update record
                //$oRow = AppSolrQueuePeer::retrieveByPK( $sAppUid );
                //$aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                //$this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                //set field
                $this->setAppUid($sAppUid);
                $this->setAppUpdated($iUpdated);
                if ($this->validate()) {
                    $result=$this->save();
                } else {
                    $con->rollback();
                    throw(new Exception("Failed Validation in class ".get_class($this)."."));
                }
                $con->commit();
                return $result;
            } else {
                //create record
                //set values
                $this->setAppUid($sAppUid);
                $this->setAppUpdated($iUpdated);
                if ($this->validate()) {
                    $result=$this->save();
                } else {
                    $e=new Exception("Failed Validation in class ".get_class($this).".");
                    //$e->aValidationFailures=$this->getValidationFailures();
                    throw($e);
                }
                $con->commit();
                return $result;
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    /**
     * Returns the list of updated applications
     * array of Entity_AppSolrQueue
     */
    public function getListUpdatedApplications()
    {
        $updatedApplications = array();
        try {
            $c = new Criteria();

            $c->addSelectColumn(AppSolrQueuePeer::APP_UID);
            $c->addSelectColumn(AppSolrQueuePeer::APP_UPDATED);

            //"WHERE
            $c->add(AppSolrQueuePeer::APP_UPDATED, 0, Criteria::NOT_EQUAL);

            $rs = AppSolrQueuePeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            //echo $c->toString();
            $rs->next();
            $row = $rs->getRow();

            while (is_array($row)) {
                $appSolrQueue = Entity_AppSolrQueue::createEmpty();
                $appSolrQueue->appUid = $row['APP_UID'];
                $appSolrQueue->appUpdated = $row['APP_UPDATED'];
                $updatedApplications[] = $appSolrQueue;
                $rs->next();
                $row = $rs->getRow();
            }
            return $updatedApplications;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }
}

