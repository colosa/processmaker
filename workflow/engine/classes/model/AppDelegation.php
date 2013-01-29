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

        //get max DEL_INDEX
        $criteria = new Criteria('workflow');
        $criteria->add( AppDelegationPeer::APP_UID, $sAppUid );
        $criteria->add( AppDelegationPeer::DEL_LAST_INDEX , 1);
        //$criteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
        $criteriaIndex = clone $criteria;

        $rs = AppDelegationPeer::doSelectRS( $criteriaIndex );
        $rs->next();
        $row = $rs->getRow();
        $delIndex = (isset($row['1'])) ? $row['1'] + 1 : 1;

        // update set
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
        $delTaskDueDate = $this->calculateDueDate( $sNextTasParam );

        $this->setDelTaskDueDate( $delTaskDueDate['DUE_DATE'] ); // Due date formatted


        if ((defined( "DEBUG_CALENDAR_LOG" )) && (DEBUG_CALENDAR_LOG)) {
            $this->setDelData( $delTaskDueDate['DUE_DATE_LOG'] ); // Log of actions made by Calendar Engine
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
            $data = new stdclass();
            $data->TAS_UID = $sTasUid;
            $data->APP_UID = $sAppUid;
            $data->DEL_INDEX = $delIndex;
            $data->USR_UID = $sUsrUid;
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $oPluginRegistry->executeTriggers( PM_CREATE_NEW_DELEGATION, $data );
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

        //use the dates class to calculate dates
        $dates = new dates();
        $iDueDate = $dates->calculateDate( $this->getDelDelegateDate(), $aData['TAS_DURATION'], $aData['TAS_TIMEUNIT'],         //hours or days, ( we only accept this two types or maybe weeks
        $aData['TAS_TYPE_DAY'],         //working or calendar days
        $this->getUsrUid(), $task->getProUid(), $aData['TAS_UID'], $aCalendarUID );

        return $iDueDate;
    }

    public function getDiffDate ($date1, $date2)
    {
        return ($date1 - $date2) / (24 * 60 * 60); //days
        return ($date1 - $date2) / 3600;
    }

    public function calculateDuration ($cron = 0)
    {
        try {
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
                if ($cron == 1) {
                    $arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );
                    $arrayCron["processcTimeStart"] = time();
                    @file_put_contents( PATH_DATA . "cron", serialize( $arrayCron ) );
                }

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
            //walk in all rows with DEL_STARTED = 0 or DEL_FINISHED = 0


            $c = new Criteria( 'workflow' );
            $c->clearSelectColumns();
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
            $c->addSelectColumn( TaskPeer::TAS_DURATION );
            $c->addSelectColumn( TaskPeer::TAS_TIMEUNIT );
            $c->addSelectColumn( TaskPeer::TAS_TYPE_DAY );

            $c->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );
            //$c->add(AppDelegationPeer::DEL_INIT_DATE, NULL, Criteria::ISNULL);
            //$c->add(AppDelegationPeer::APP_UID, '7694483844a37bfeb0931b1063501289');
            //$c->add(AppDelegationPeer::DEL_STARTED, 0);


            $cton1 = $c->getNewCriterion( AppDelegationPeer::DEL_STARTED, 0 );
            $cton2 = $c->getNewCriterion( AppDelegationPeer::DEL_FINISHED, 0 );
            $cton1->addOR( $cton2 );
            $c->add( $cton1 );

            $rs = AppDelegationPeer::doSelectRS( $c );
            $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $rs->next();
            $row = $rs->getRow();
            $i = 0;
            //print "<table colspacing='2' border='1'>";
            //print "<tr><td>iDelegateDate </td><td>iInitDate </td><td>iDueDate </td><td>iFinishDate </td><td>isStarted </td><td>isFinished </td><td>isDelayed </td><td>queueDuration </td><td>delDuration </td><td>delayDuration</td></tr>";


            $now = strtotime( 'now' );
            while (is_array( $row )) {
                if ($cron == 1) {
                    $arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );
                    $arrayCron["processcTimeStart"] = time();
                    @file_put_contents( PATH_DATA . "cron", serialize( $arrayCron ) );
                }

                $fTaskDuration = $row['TAS_DURATION'];
                $iDelegateDate = strtotime( $row['DEL_DELEGATE_DATE'] );
                $iInitDate = strtotime( $row['DEL_INIT_DATE'] );
                $iDueDate = strtotime( $row['DEL_TASK_DUE_DATE'] );
                $iFinishDate = strtotime( $row['DEL_FINISH_DATE'] );
                $isStarted = intval( $row['DEL_STARTED'] );
                $isFinished = intval( $row['DEL_FINISHED'] );
                $isDelayed = intval( $row['DEL_DELAYED'] );
                $queueDuration = $this->getDiffDate( $iInitDate, $iDelegateDate );
                $delDuration = 0;
                $delayDuration = 0;
                $overduePercentage = 0.0;
                //get the object,
                $oAppDel = AppDelegationPeer::retrieveByPk( $row['APP_UID'], $row['DEL_INDEX'] );

                //if the task is not started
                if ($isStarted == 0) {
                    if ($row['DEL_INIT_DATE'] != null && $row['DEL_INIT_DATE'] != '') {
                        $oAppDel->setDelStarted( 1 );
                        $queueDuration = $this->getDiffDate( $iInitDate, $iDelegateDate );
                        $oAppDel->setDelQueueDuration( $queueDuration );
                    } else {
                        //the task was not started
                        $queueDuration = $this->getDiffDate( $now, $iDelegateDate );
                        $oAppDel->setDelQueueDuration( $queueDuration );

                        //we are putting negative number if the task is not delayed, and positive number for the time the task is delayed
                        $delayDuration = $this->getDiffDate( $now, $iDueDate );
                        $oAppDel->setDelDelayDuration( $delayDuration );
                        if ($fTaskDuration != 0) {
                            $overduePercentage = $delayDuration / $fTaskDuration;
                            $oAppDel->setAppOverduePercentage( $overduePercentage );
                            if ($iDueDate < $now) {
                                $oAppDel->setDelDelayed( 1 );
                            }
                        }
                    }
                }

                //if the task was not finished
                if ($isFinished == 0) {
                    if ($row['DEL_FINISH_DATE'] != null && $row['DEL_FINISH_DATE'] != '') {
                        $oAppDel->setAppOverduePercentage( $overduePercentage );
                        $oAppDel->setDelFinished( 1 );

                        $delDuration = $this->getDiffDate( $iFinishDate, $iInitDate );
                        $oAppDel->setDelDuration( $delDuration );
                        //calculate due date if correspond
                        if ($iDueDate < $iFinishDate) {
                            $oAppDel->setDelDelayed( 1 );
                            $delayDuration = $this->getDiffDate( $iFinishDate, $iDueDate );
                        } else {
                            $oAppDel->setDelDelayed( 0 );
                            $delayDuration = 0;
                        }
                    } else {
                        //the task was not completed
                        if ($row['DEL_INIT_DATE'] != null && $row['DEL_INIT_DATE'] != '') {
                            $delDuration = $this->getDiffDate( $now, $iInitDate );
                        } else {
                            $delDuration = $this->getDiffDate( $now, $iDelegateDate );
                        }
                        $oAppDel->setDelDuration( $delDuration );

                        //we are putting negative number if the task is not delayed, and positive number for the time the task is delayed
                        $delayDuration = $this->getDiffDate( $now, $iDueDate );
                        $oAppDel->setDelDelayDuration( $delayDuration );
                        if ($fTaskDuration != 0) {
                            $overduePercentage = $delayDuration / $fTaskDuration;
                            $oAppDel->setAppOverduePercentage( $overduePercentage );
                            if ($iDueDate < $now) {
                                $oAppDel->setDelDelayed( 1 );
                            }
                        }
                    }

                }

                //and finally save the record
                $RES = $oAppDel->save();
                $rs->next();
                $row = $rs->getRow();
            }
        } catch (Exception $oError) {
            error_log( $oError->getMessage() );
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
}

