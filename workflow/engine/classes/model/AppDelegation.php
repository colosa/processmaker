<?php

/**

 * AppDelegation.php

 *

 * @package workflow.engine.classes.model

 *

 * ProcessMaker Open Source Edition

 * Copyright (C) 2004 - 2011 Colosa Inc.

 *

 * This program is free software: you can redistribute it and/or modify

 * it under the terms of the GNU Affero General Public License as

 * published by the Free Software Foundation, either version 3 of the

 * License, or (at your option) any later version.

 *

 * This program is distributed in the hope that it will be useful,

 * but WITHOUT ANY WARRANTY; without even the implied warranty of

 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the

 * GNU Affero General Public License for more details.

 *

 * You should have received a copy of the GNU Affero General Public License

 * along with this program. If not, see <http://www.gnu.org/licenses/>.

 *

 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,

 * Coral Gables, FL, 33134, USA, or email info@colosa.com.

 *

 */



//require_once 'classes/model/om/BaseAppDelegation.php';

//require_once ("classes/model/HolidayPeer.php");

//require_once ("classes/model/TaskPeer.php");

//require_once ("classes/model/Task.php");

//G::LoadClass( "dates" );



/**

 * Skeleton subclass for representing a row from the 'APP_DELEGATION' table.

 *

 *

 *

 * You should add additional methods to this class to meet the

 * application requirements. This class will only be generated as

 * long as it does not already exist in the output directory.

 *

 * @package workflow.engine.classes.model

 */

class AppDelegation extends BaseAppDelegation

{



    /**

     * create an application delegation

     *

     * @param $sProUid process Uid

     * @param $sAppUid Application Uid

     * @param $sTasUid Task Uid

     * @param $sUsrUid User Uid

     * @param $iPriority delegation priority

     * @param $isSubprocess is a subprocess inside a process?

     * @return delegation index of the application delegation.

     */

    public function createAppDelegation ($sProUid, $sAppUid, $sTasUid, $sUsrUid, $sAppThread, $iPriority = 3, $isSubprocess = false, $sPrevious = -1, $sNextTasParam = null)

    {

        if (! isset( $sProUid ) || strlen( $sProUid ) == 0) {

            throw (new Exception( 'Column "PRO_UID" cannot be null.' ));

        }



        if (! isset( $sAppUid ) || strlen( $sAppUid ) == 0) {

            throw (new Exception( 'Column "APP_UID" cannot be null.' ));

        }



        if (! isset( $sTasUid ) || strlen( $sTasUid ) == 0) {

            throw (new Exception( 'Column "TAS_UID" cannot be null.' ));

        }



        if (! isset( $sUsrUid ) /*|| strlen($sUsrUid ) == 0*/ ) {

            throw (new Exception( 'Column "USR_UID" cannot be null.' ));

        }



        if (! isset( $sAppThread ) || strlen( $sAppThread ) == 0) {

            throw (new Exception( 'Column "APP_THREAD" cannot be null.' ));

        }



        //Get max DEL_INDEX

        $criteria = new Criteria("workflow");

        $criteria->add(AppDelegationPeer::APP_UID, $sAppUid);

        $criteria->add(AppDelegationPeer::DEL_LAST_INDEX, 1);



        $criteriaIndex = clone $criteria;



        $rs = AppDelegationPeer::doSelectRS($criteriaIndex);

        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);



        $delIndex = 1;

        $delPreviusUsrUid = $sUsrUid;

        if ($rs->next()) {

            $row = $rs->getRow();



            $delIndex = (isset($row["DEL_INDEX"]))? $row["DEL_INDEX"] + 1 : 1;

            $delPreviusUsrUid = $row["USR_UID"];

        } else {

            $criteriaDelIndex = new Criteria("workflow");



            $criteriaDelIndex->addSelectColumn(AppDelegationPeer::DEL_INDEX);

            $criteriaDelIndex->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

            $criteriaDelIndex->add(AppDelegationPeer::APP_UID, $sAppUid);

            $criteriaDelIndex->addDescendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);



            $rsCriteriaDelIndex = AppDelegationPeer::doSelectRS($criteriaDelIndex);

            $rsCriteriaDelIndex->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            if ($rsCriteriaDelIndex->next()) {

                $row = $rsCriteriaDelIndex->getRow();



                $delIndex = (isset($row["DEL_INDEX"]))? $row["DEL_INDEX"] + 1 : 1;

            }

        }



        //Update set

        $criteriaUpdate = new Criteria('workflow');

        $criteriaUpdate->add(AppDelegationPeer::DEL_LAST_INDEX, 0);

        BasePeer::doUpdate($criteria, $criteriaUpdate, Propel::getConnection('workflow'));



        $this->setAppUid( $sAppUid );

        $this->setProUid( $sProUid );

        $this->setTasUid( $sTasUid );

        $this->setDelIndex( $delIndex );

        $this->setDelLastIndex(1);

        $this->setDelPrevious( $sPrevious == - 1 ? 0 : $sPrevious );

        $this->setUsrUid( $sUsrUid );

        $this->setDelType( 'NORMAL' );

        $this->setDelPriority( ($iPriority != '' ? $iPriority : '3') );

        $this->setDelThread( $sAppThread );

        $this->setDelThreadStatus( 'OPEN' );

        $this->setDelDelegateDate( 'now' );



        //The function return an array now.  By JHL

        $delTaskDueDate = $this->calculateDueDate($sNextTasParam);

        $delRiskDate    = $this->calculateRiskDate($sNextTasParam, $this->getRisk());



        //$this->setDelTaskDueDate( $delTaskDueDate['DUE_DATE'] ); // Due date formatted

        $this->setDelTaskDueDate($delTaskDueDate);

        $this->setDelRiskDate($delRiskDate);



        if ((defined( "DEBUG_CALENDAR_LOG" )) && (DEBUG_CALENDAR_LOG)) {

            //$this->setDelData( $delTaskDueDate['DUE_DATE_LOG'] ); // Log of actions made by Calendar Engine

        	$this->setDelData( $delTaskDueDate );

        } else {

            $this->setDelData( '' );

        }



        // this condition assures that an internal delegation like a subprocess dont have an initial date setted

        if ($delIndex == 1 && ! $isSubprocess) {

            //the first delegation, init date this should be now for draft applications, in other cases, should be null.

            $this->setDelInitDate( 'now' );

        }



        if ($this->validate()) {

            try {

                $res = $this->save();

            } catch (PropelException $e) {

                throw ($e);

            }

        } else {

            // Something went wrong. We can now get the validationFailures and handle them.

            $msg = '';

            $validationFailuresArray = $this->getValidationFailures();

            foreach ($validationFailuresArray as $objValidationFailure) {

                $msg .= $objValidationFailure->getMessage() . "<br/>";

            }

            throw (new Exception( 'Failed Data validation. ' . $msg ));

        }



        $delIndex = $this->getDelIndex();



        // Hook for the trigger PM_CREATE_NEW_DELEGATION

        if (defined( 'PM_CREATE_NEW_DELEGATION' )) {

            

            $bpmn = new \ProcessMaker\Project\Bpmn();

            $flagActionsByEmail = true;



            $data = new stdclass();

            $data->TAS_UID = $sTasUid;

            $data->APP_UID = $sAppUid;

            $data->DEL_INDEX = $delIndex;

            $data->USR_UID = $sUsrUid;

            $data->PREVIOUS_USR_UID = $delPreviusUsrUid;



            if ($bpmn->exists($sProUid)) {

                /*----------------------------------********---------------------------------*/



            }



            if ($flagActionsByEmail) {                

                $oPluginRegistry = &PMPluginRegistry::getSingleton();

                $oPluginRegistry->executeTriggers(PM_CREATE_NEW_DELEGATION, $data);

            } 

        }



        return $delIndex;

    }



    /**

     * Load the Application Delegation row specified in [app_id] column value.

     *

     * @param string $AppUid the uid of the application

     * @return array $Fields the fields

     */



    public function Load ($AppUid, $sDelIndex)

    {

        $con = Propel::getConnection( AppDelegationPeer::DATABASE_NAME );

        try {

            $oAppDel = AppDelegationPeer::retrieveByPk( $AppUid, $sDelIndex );

            if (is_object( $oAppDel ) && get_class( $oAppDel ) == 'AppDelegation') {

                $aFields = $oAppDel->toArray( BasePeer::TYPE_FIELDNAME );

                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );

                return $aFields;

            } else {

                throw (new Exception( "The row '$AppUid, $sDelIndex' in table AppDelegation doesn't exist!" ));

            }

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /* Load the Application Delegation row specified in [app_id] column value.

     *

     * @param string $AppUid the uid of the application

     * @param string $index the index of the delegation

     * @return array $Fields the fields

     */



    public function LoadParallel ($AppUid, $index = "")

    {

        $aCases = array();



        $c = new Criteria( 'workflow' );

        $c->addSelectColumn( AppDelegationPeer::APP_UID );

        $c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

        $c->addSelectColumn( AppDelegationPeer::PRO_UID );

        $c->addSelectColumn( AppDelegationPeer::TAS_UID );

        $c->addSelectColumn( AppDelegationPeer::USR_UID );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );



        $c->add( AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN' );

        $c->add( AppDelegationPeer::APP_UID, $AppUid );

        if(!empty($index)) {

            $c->add( AppDelegationPeer::DEL_INDEX, $index );

        }

        $c->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );

        $rs = AppDelegationPeer::doSelectRS( $c );

        $row= $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );



        $rs->next();

        $row = $rs->getRow();



        while (is_array($row)) {

            $case = array();

            $case['TAS_UID']   = $row['TAS_UID'];

            $case['USR_UID']   = $row['USR_UID'];

            $case['DEL_INDEX'] = $row['DEL_INDEX'];

            $case['TAS_UID']   = $row['TAS_UID'];

            $case['DEL_DELEGATE_DATE'] = $row['DEL_DELEGATE_DATE'];

            $case['DEL_INIT_DATE']     = $row['DEL_INIT_DATE'];

            $case['DEL_TASK_DUE_DATE'] = $row['DEL_TASK_DUE_DATE'];

            $case['DEL_FINISH_DATE']   = $row['DEL_FINISH_DATE'];

            $aCases[] = $case;

            $rs->next();

            $row = $rs->getRow();

        }



        return $aCases;

    }



    /**

     * Update the application row

     *

     * @param array $aData

     * @return variant

     *

     */



    public function update ($aData)

    {

        $con = Propel::getConnection( AppDelegationPeer::DATABASE_NAME );

        try {

            $con->begin();

            $oApp = AppDelegationPeer::retrieveByPK( $aData['APP_UID'], $aData['DEL_INDEX'] );

            if (is_object( $oApp ) && get_class( $oApp ) == 'AppDelegation') {

                $oApp->fromArray( $aData, BasePeer::TYPE_FIELDNAME );

                if ($oApp->validate()) {

                    $res = $oApp->save();

                    $con->commit();

                    return $res;

                } else {

                    $msg = '';

                    foreach ($this->getValidationFailures() as $objValidationFailure) {

                        $msg .= $objValidationFailure->getMessage() . "<br/>";

                    }



                    throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));

                }

            } else {

                $con->rollback();

                throw (new Exception( "This AppDelegation row doesn't exist!" ));

            }

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    public function remove ($sApplicationUID, $iDelegationIndex)

    {

        $oConnection = Propel::getConnection( StepTriggerPeer::DATABASE_NAME );

        try {

            $oConnection->begin();

            $oApp = AppDelegationPeer::retrieveByPK( $sApplicationUID, $iDelegationIndex );

            if (is_object( $oApp ) && get_class( $oApp ) == 'AppDelegation') {

                $result = $oApp->delete();

            }

            $oConnection->commit();

            return $result;

        } catch (Exception $e) {

            $oConnection->rollback();

            throw ($e);

        }

    }



    // TasTypeDay = 1  => working days

    // TasTypeDay = 2  => calendar days

    public function calculateDueDate ($sNextTasParam)

    {

        //Get Task properties

        $task = TaskPeer::retrieveByPK( $this->getTasUid() );



        $aData['TAS_UID'] = $this->getTasUid();

        //Added to allow User defined Timing Control at Run time from Derivation screen

        if (isset( $sNextTasParam['NEXT_TASK']['TAS_TRANSFER_HIDDEN_FLY'] ) && $sNextTasParam['NEXT_TASK']['TAS_TRANSFER_HIDDEN_FLY'] == 'true') {

            $aData['TAS_DURATION'] = $sNextTasParam['NEXT_TASK']['TAS_DURATION'];

            $aData['TAS_TIMEUNIT'] = $sNextTasParam['NEXT_TASK']['TAS_TIMEUNIT'];

            $aData['TAS_TYPE_DAY'] = $sNextTasParam['NEXT_TASK']['TAS_TYPE_DAY'];



            if (isset( $sNextTasParam['NEXT_TASK']['TAS_CALENDAR'] ) && $sNextTasParam['NEXT_TASK']['TAS_CALENDAR'] != '') {

                $aCalendarUID = $sNextTasParam['NEXT_TASK']['TAS_CALENDAR'];

            } else {

                $aCalendarUID = '';

            }



            //Updating the task Table , so that user will see updated values in the assign screen in consequent cases

            $oTask = new Task();

            $oTask->update( $aData );

        } else {

            if (is_null( $task )) {

                return 0;

            }

            $aData['TAS_DURATION'] = $task->getTasDuration();

            $aData['TAS_TIMEUNIT'] = $task->getTasTimeUnit();

            $aData['TAS_TYPE_DAY'] = $task->getTasTypeDay();

            $aCalendarUID = '';

        }



        //Calendar - Use the dates class to calculate dates

        $calendar = new calendar();



        $arrayCalendarData = array();



        if ($calendar->pmCalendarUid == "") {

            $calendar->getCalendar(null, $this->getProUid(), $this->getTasUid());



            $arrayCalendarData = $calendar->getCalendarData();

        }



        //Due date

        /*$iDueDate = $calendar->calculateDate( $this->getDelDelegateDate(), $aData['TAS_DURATION'], $aData['TAS_TIMEUNIT']         //hours or days, ( we only accept this two types or maybe weeks

        );*/

        $dueDate = $calendar->dashCalculateDate($this->getDelDelegateDate(), $aData["TAS_DURATION"], $aData["TAS_TIMEUNIT"], $arrayCalendarData);



        //Return

        return $dueDate;

    }



    public function calculateRiskDate($dueDate, $risk)

    {

        try {



            $data = array();

            if (isset( $sNextTasParam['NEXT_TASK']['TAS_TRANSFER_HIDDEN_FLY'] ) && $sNextTasParam['NEXT_TASK']['TAS_TRANSFER_HIDDEN_FLY'] == 'true') {

                $data['TAS_DURATION'] = $sNextTasParam['NEXT_TASK']['TAS_DURATION'];

                $data['TAS_TIMEUNIT'] = $sNextTasParam['NEXT_TASK']['TAS_TIMEUNIT'];

            } else {

                $task = TaskPeer::retrieveByPK( $this->getTasUid() );

                $data['TAS_DURATION'] = $task->getTasDuration();

                $data['TAS_TIMEUNIT'] = $task->getTasTimeUnit();

            }



            $riskTime = $data['TAS_DURATION'] - ($data['TAS_DURATION'] * $risk);



            //Calendar - Use the dates class to calculate dates

            $calendar = new calendar();



            $arrayCalendarData = array();



            if ($calendar->pmCalendarUid == "") {

                $calendar->getCalendar(null, $this->getProUid(), $this->getTasUid());



                $arrayCalendarData = $calendar->getCalendarData();

            }



            //Risk date

            $riskDate = $calendar->dashCalculateDate($this->getDelDelegateDate(), $riskTime, $data['TAS_TIMEUNIT'], $arrayCalendarData);



            return $riskDate;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getDiffDate ($date1, $date2)

    {

        return ($date1 - $date2) / (24 * 60 * 60); //days

        return ($date1 - $date2) / 3600;

    }



	//usually this function is called when routing in the flow, so by default cron =0

	public function calculateDuration($cron = 0) 

	{

		$this->writeFileIfCalledFromCronForCalculateDuration($cron);

		$this->patchDataWithValuesForCalculateDuration();

		$rs = $this->recordSetForCalculateDuration();

		$rs->next();

		$row = $rs->getRow();

		$i = 0;

		$calendar = new calendar();

		$now = new DateTime();

		while (is_array ($row)) {

			$oAppDel = AppDelegationPeer::retrieveByPk( $row['APP_UID'], $row['DEL_INDEX'] );

            $calendar = new calendar();

            $calendar->getCalendar($row['USR_UID'], $row['PRO_UID'], $row['TAS_UID']);

            $calData = $calendar->getCalendarData();

            $calculatedValues = $this->getValuesToStoreForCalculateDuration($row, $calendar, $calData, $now);



			$oAppDel->setDelStarted($calculatedValues['isStarted']);

			$oAppDel->setDelFinished($calculatedValues['isFinished']);

			$oAppDel->setDelDelayed($calculatedValues['isDelayed']);

			$oAppDel->setDelQueueDuration($calculatedValues['queueTime']);

			$oAppDel->setDelDelayDuration($calculatedValues['delayTime']);

			$oAppDel->setDelDuration($calculatedValues['durationTime']);

			$oAppDel->setAppOverduePercentage($calculatedValues['percentDelay']);

			$RES = $oAppDel->save();

			$rs->next();

			$row = $rs->getRow();

		}

	}



	public function getValuesToStoreForCalculateDuration($row, $calendar, $calData, $nowDate) 

	{

        $rowValues = $this->completeRowDataForCalculateDuration($row, $nowDate);

		return Array(

            'isStarted'    => $this->createDateFromString($row['DEL_INIT_DATE']) != null ? 1 : 0,

            'isFinished'   => $this->createDateFromString($row['DEL_FINISH_DATE']) != null ? 1: 0,

            'isDelayed'    => $this->calculateDelayTime($calendar, $calData, $rowValues) > 0 ? 1 : 0,

            'queueTime'    => $this->calculateQueueTime($calendar, $calData, $rowValues),

            'delayTime'    => $this->calculateDelayTime($calendar, $calData, $rowValues),

            'durationTime' => $this->calculateNetProcessingTime($calendar, $calData, $rowValues),

            'percentDelay' => $this->calculateOverduePercentage($calendar, $calData, $rowValues)

		);

	}



	private function calculateOverduePercentage($calendar, $calData, $rowValues)

	{

        if ($rowValues['fTaskDuration'] == 0) {

            return 0;

        }

        //TODO 8 daily/hours must be extracted from calendar

        $taskTime = ($rowValues['cTaskDurationUnit'] == 'DAYS') 

                    ? $rowValues['fTaskDuration'] * 8 / 24

                    : $rowValues['fTaskDuration'] / 24;



        return $this->calculateDelayTime($calendar, $calData, $rowValues)  * 100/ $taskTime;

	}



	//time in days from init or delegate date to finish or today's date

	private function calculateNetProcessingTime($calendar, $calData, $rowValues)

	{

		$initDateForCalc = $this->selectDate ($rowValues['dInitDate'], $rowValues['dDelegateDate'], 'max'); 

		$endDateForCalc = $this->selectDate ($rowValues['dFinishDate'], $rowValues['dNow'], 'min'); 

		return $calendar->dashCalculateDurationWithCalendar(

							$initDateForCalc->format('Y-m-d H:i:s'), 

							$endDateForCalc->format('Y-m-d H:i:s'), 

							$calData)/(24*60*60);  

	}

	

	//time in days from delegate date to init date

	private function calculateQueueTime($calendar,  $calData, $rowValues)

	{

		$initDateForCalc = $rowValues['dDelegateDate']; 

		$endDateForCalc = $this->selectDate ($rowValues['dInitDate'], $rowValues['dNow'], 'min'); 

		return $calendar->dashCalculateDurationWithCalendar(

							$initDateForCalc->format('Y-m-d H:i:s'), 

							$endDateForCalc->format('Y-m-d H:i:s'), 

							$calData)/(24*60*60);  

	}



	//time in days from due date to finish or today date

	private function calculateDelayTime($calendar, $calData, $rowValues)

	{

		$initDateForCalc = $this->selectDate($rowValues['dDueDate'], $rowValues['dDelegateDate'], 'max'); 

		$endDateForCalc = $this->selectDate ($rowValues['dFinishDate'], $rowValues['dNow'], 'min'); 

		return $calendar->dashCalculateDurationWithCalendar(

							$initDateForCalc->format('Y-m-d H:i:s'), 

							$endDateForCalc->format('Y-m-d H:i:s'), 

							$calData)/(24*60*60);  

	}



    //to avoid aplying many times the same conversions and functions the row data

    //is used to create dates as DateTime objects and other fields are stracted also, 

    //so the array returned will work as a "context" object for the rest of the functions.

	private function completeRowDataForCalculateDuration($row, $nowDate) 

	{

		return Array(

			'dDelegateDate'       => $this->createDateFromString ($row['DEL_DELEGATE_DATE']),

			'dInitDate'           => $this->createDateFromString ($row['DEL_INIT_DATE']),

			'dDueDate'            => $this->createDateFromString ($row['DEL_TASK_DUE_DATE']),

			'dFinishDate'         => $this->createDateFromString ($row['DEL_FINISH_DATE']),

			'fTaskDuration'       => $row['TAS_DURATION'] * 1.0,

			'cTaskDurationUnit'   => $row['TAS_TIMEUNIT'],

			'dNow'                => $nowDate,

			'row'                 => $row

		);

	}



	//by default min function returns de null value if one of the params is null

    //to avoid that behaviour this function was created so the function returns the first

    //not null date or if both are not null the mix/max date

    //NOTE date1 and date2 are DateTime objects.

	private function selectDate($date1, $date2, $compareFunction)

	{

		if ($date1 == null)	

			return $date2;



		if ($date2 == null)	

			return $date1;



		return $compareFunction($date1, $date2);

	}



    //Creates a DateTime object from a string. If the string is null or empty a null object is returned

	private function createDateFromString($stringDate) {

		if ($stringDate == null || $stringDate == '')

			return null;

		return new DateTime($stringDate);

	}



	private function recordSetForCalculateDuration()

	{

		//walk in all rows with DEL_STARTED = 0 or DEL_FINISHED = 0

		$c = new Criteria( 'workflow' );

		$c->clearSelectColumns();

		$c->addSelectColumn( AppDelegationPeer::APP_UID );

		$c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

		$c->addSelectColumn( AppDelegationPeer::USR_UID);

		$c->addSelectColumn( AppDelegationPeer::PRO_UID);

		$c->addSelectColumn( AppDelegationPeer::TAS_UID);

		$c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );

		$c->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );

		$c->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );

		$c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

		$c->addSelectColumn( AppDelegationPeer::DEL_DURATION );

		$c->addSelectColumn( AppDelegationPeer::DEL_QUEUE_DURATION );

		$c->addSelectColumn( AppDelegationPeer::DEL_DELAY_DURATION );

		$c->addSelectColumn( AppDelegationPeer::DEL_STARTED );

		$c->addSelectColumn( AppDelegationPeer::DEL_FINISHED );

		$c->addSelectColumn( AppDelegationPeer::DEL_DELAYED );

		$c->addSelectColumn( TaskPeer::TAS_DURATION );

		$c->addSelectColumn( TaskPeer::TAS_TIMEUNIT );

		$c->addSelectColumn( TaskPeer::TAS_TYPE_DAY );



		$c->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );

		$cton1 = $c->getNewCriterion( AppDelegationPeer::DEL_STARTED, 0 );

		$cton2 = $c->getNewCriterion( AppDelegationPeer::DEL_FINISHED, 0 );

		$cton1->addOR( $cton2 );

		$c->add( $cton1 );

		$rs = AppDelegationPeer::doSelectRS( $c );

		$rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

		return $rs;

	}

    private function writeFileIfCalledFromCronForCalculateDuration($cron) 

	{

		if ($cron == 1) {

			$arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );

			$arrayCron["processcTimeStart"] = time();

			@file_put_contents( PATH_DATA . "cron", serialize( $arrayCron ) );

		}

	}



	private function patchDataWithValuesForCalculateDuration() 

	{

		//patch  rows with initdate = null and finish_date

		$c = new Criteria();

		$c->clearSelectColumns();

		$c->addSelectColumn( AppDelegationPeer::APP_UID );

		$c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

		$c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );

		$c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

		$c->add( AppDelegationPeer::DEL_INIT_DATE, null, Criteria::ISNULL );

		$c->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNOTNULL );

		//$c->add(AppDelegationPeer::DEL_INDEX, 1);





		$rs = AppDelegationPeer::doSelectRS( $c );

		$rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

		$rs->next();

		$row = $rs->getRow();



		while (is_array( $row )) {

			$oAppDel = AppDelegationPeer::retrieveByPk( $row['APP_UID'], $row['DEL_INDEX'] );

			if (isset( $row['DEL_FINISH_DATE'] )) {

				$oAppDel->setDelInitDate( $row['DEL_FINISH_DATE'] );

			} else {

				$oAppDel->setDelInitDate( $row['DEL_INIT_DATE'] );

			}

			$oAppDel->save();



			$rs->next();

			$row = $rs->getRow();

		}

	}

    





    public function getLastDeleration ($APP_UID)

    {

        $c = new Criteria( 'workflow' );

        $c->addSelectColumn( AppDelegationPeer::APP_UID );

        $c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

        $c->addSelectColumn( AppDelegationPeer::DEL_DURATION );

        $c->addSelectColumn( AppDelegationPeer::DEL_QUEUE_DURATION );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELAY_DURATION );

        $c->addSelectColumn( AppDelegationPeer::DEL_STARTED );

        $c->addSelectColumn( AppDelegationPeer::DEL_FINISHED );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELAYED );

        $c->addSelectColumn( AppDelegationPeer::USR_UID );



        $c->add( AppDelegationPeer::APP_UID, $APP_UID );

        $c->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );

        $rs = AppDelegationPeer::doSelectRS( $c );

        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rs->next();

        return $rs->getRow();

    }



    public function getCurrentIndex ($appUid)

    {

        $oCriteria = new Criteria();

        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );

        $oCriteria->add( AppDelegationPeer::APP_UID, $appUid );

        $oCriteria->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );

        $oRuleSet = AppDelegationPeer::doSelectRS( $oCriteria );

        $oRuleSet->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oRuleSet->next();

        $data = $oRuleSet->getRow();

        return (int)$data['DEL_INDEX'];

    }



    public function getCurrentTask ($appUid)

    {

        $oCriteria = new Criteria();

        $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );

        $oCriteria->add( AppDelegationPeer::APP_UID, $appUid );

        $oCriteria->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );

        $oRuleSet = AppDelegationPeer::doSelectRS( $oCriteria );

        $oRuleSet->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oRuleSet->next();

        $data = $oRuleSet->getRow();

        return $data['TAS_UID'];

    }



    /**

    * Verify if the current case is already routed.

    *

    * @param string $AppUid the uid of the application

    * @return array $Fields the fields

    */



    public function alreadyRouted ($appUid, $sDelIndex)

    {

        $c = new Criteria("workflow");

        $c->clearSelectColumns();

        $c->addSelectColumn(AppDelegationPeer::APP_UID);

        $c->add(AppDelegationPeer::APP_UID, $appUid);

        $c->add(AppDelegationPeer::DEL_INDEX, $sDelIndex);

        $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNOTNULL);

        $result = AppDelegationPeer::doSelectRS($c);

        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        if($result->next()) {

            return true;

        } else {

            return false;

        }

    }



    public function getRisk()

    {

        try {

            $risk = 0.2;



            //Return

            return $risk;

        } catch (Exception $e) {

            throw $e;

        }

    }

}


