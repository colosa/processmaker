<?php
/**
 * LogCasesScheduler.php
 * @package    workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseLogCasesScheduler.php';


/**
 * Skeleton subclass for representing a row from the 'LOG_CASES_SCHEDULER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class LogCasesScheduler extends BaseLogCasesScheduler
{
    public function getAllCriteria()
    {
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(LogCasesSchedulerPeer::LOG_CASE_UID);
        $c->addSelectColumn(LogCasesSchedulerPeer::PRO_UID);
        $c->addSelectColumn(LogCasesSchedulerPeer::TAS_UID);
        $c->addSelectColumn(LogCasesSchedulerPeer::USR_NAME);
        $c->addSelectColumn(LogCasesSchedulerPeer::EXEC_DATE);
        $c->addSelectColumn(LogCasesSchedulerPeer::EXEC_HOUR);
        $c->addSelectColumn(LogCasesSchedulerPeer::RESULT);
        $c->addSelectColumn(LogCasesSchedulerPeer::SCH_UID);
        $c->addSelectColumn(LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS);
        $c->addSelectColumn(LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS);
        return $c;
    }

    public function getAll()
    {
        $oCriteria = $this->getAllCriteria();
        $oDataset = LogCasesSchedulerPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRows = Array();
        while ($aRow = $oDataset->getRow() ) {
            $aRows[] = $aRow;
            $oDataset->next();
        }
        /*foreach($aRows as $k => $aRow){
          $oProcess = new Process();
          $aProcessRow = $oProcess->load($aRow['PRO_UID']);
          $oTask = new Task();
          $aTaskRow = $oTask->load($aRow['TAS_UID']);
          $aRows[$k] = array_merge($aRow, $aProcessRow, $aTaskRow);
        }*/

        return $aRows;
    }

    public function saveLogParameters($params)
    {
        if ( isset ( $params['LOG_CASE_UID'] ) && $params['LOG_CASE_UID']== '' ) {
            unset ( $params['LOG_CASE_UID'] );
        }
        if ( !isset ( $params['LOG_CASE_UID'] ) ) {
            $params['LOG_CASE_UID'] = G::generateUniqueID();
        }

        $this->setLogCaseUid($params['LOG_CASE_UID']);
        $this->setProUid($params['PRO_UID']);
        $this->setTasUid($params['TAS_UID']);
        $this->setSchUid($params['SCH_UID']);
        $this->setUsrName($params['USR_NAME']);
        $this->setExecDate($params['EXEC_DATE']);
        $this->setExecHour($params['EXEC_HOUR']);
        $this->setResult($params['RESULT']);
        $this->setWsCreateCaseStatus($params['WS_CREATE_CASE_STATUS']);
        $this->setWsRouteCaseStatus($params['WS_ROUTE_CASE_STATUS']);
    }
}

