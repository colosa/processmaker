<?php
/**
 * AppEvent.php
 *
 * @package workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseAppEvent.php';

/**
 * Skeleton subclass for representing a row from the 'APP_EVENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class AppEvent extends BaseAppEvent
{

    public function load ($sApplicationUID, $iDelegation)
    {
        try {
            $oAppEvent = AppEventPeer::retrieveByPK( $sApplicationUID, $iDelegation );
            if (! is_null( $oAppEvent )) {
                $aFields = $oAppEvent->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                return $aFields;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $oConnection = Propel::getConnection( AppEventPeer::DATABASE_NAME );
        try {
            $oAppEvent = new AppEvent();
            $oAppEvent->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oAppEvent->validate()) {
                $oConnection->begin();
                $iResult = $oAppEvent->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oAppEvent->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The registry cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function update ($aData)
    {
        $oConnection = Propel::getConnection( AppEventPeer::DATABASE_NAME );
        try {
            $oAppEvent = AppEventPeer::retrieveByPK( $aData['APP_UID'], $aData['DEL_INDEX'] );
            if (! is_null( $oAppEvent )) {
                $oAppEvent->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oAppEvent->validate()) {
                    $oConnection->begin();
                    $iResult = $oAppEvent->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oAppEvent->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception( 'The registry cannot be updated!<br />' . $sMessage ));
                }
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function remove ($sApplicationUID, $iDelegation, $sEvnUid)
    {
        $oConnection = Propel::getConnection( AppEventPeer::DATABASE_NAME );
        try {
            $oAppEvent = AppEventPeer::retrieveByPK( $sApplicationUID, $iDelegation, $sEvnUid );
            if (! is_null( $oAppEvent )) {
                $oConnection->begin();
                $iResult = $oAppEvent->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function getAppEventsCriteria ($sProcessUid = '', $sStatus = '', $EVN_ACTION = '')
    {
        try {
            require_once 'classes/model/Event.php';
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppEventPeer::APP_UID );
            $oCriteria->addSelectColumn( AppEventPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppEventPeer::EVN_UID );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ACTION_DATE );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ATTEMPTS );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_LAST_EXECUTION_DATE );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_STATUS );
            $oCriteria->addSelectColumn( EventPeer::PRO_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN_OCCURS );
            $oCriteria->addSelectColumn( EventPeer::EVN_ACTION );
            $oCriteria->addAsColumn( 'EVN_DESCRIPTION', 'C1.CON_VALUE' );
            $oCriteria->addAsColumn( 'TAS_TITLE', 'C2.CON_VALUE' );
            $oCriteria->addAsColumn( 'APP_TITLE', 'C3.CON_VALUE' );
            $oCriteria->addAsColumn( 'PRO_TITLE', 'C4.CON_VALUE' );
            $oCriteria->addAlias( 'C1', 'CONTENT' );
            $oCriteria->addAlias( 'C2', 'CONTENT' );
            $oCriteria->addAlias( 'C3', 'CONTENT' );
            $oCriteria->addAlias( 'C4', 'CONTENT' );
            $oCriteria->addJoin( AppEventPeer::EVN_UID, EventPeer::EVN_UID, Criteria::LEFT_JOIN );
            $del = DBAdapter::getStringDelimiter();
            $aConditions = array ();
            $aConditions[] = array (EventPeer::EVN_UID,'C1.CON_ID'
            );
            $aConditions[] = array ('C1.CON_CATEGORY',$del . 'EVN_DESCRIPTION' . $del
            );
            $aConditions[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $aConditions = array ();
            $aConditions[] = array (AppEventPeer::APP_UID,AppDelegationPeer::APP_UID
            );
            $aConditions[] = array (AppEventPeer::DEL_INDEX,AppDelegationPeer::DEL_INDEX
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::TAS_UID,'C2.CON_ID'
            );
            $aConditions[] = array ('C2.CON_CATEGORY',$del . 'TAS_TITLE' . $del
            );
            $aConditions[] = array ('C2.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::APP_UID,'C3.CON_ID'
            );
            $aConditions[] = array ('C3.CON_CATEGORY',$del . 'APP_TITLE' . $del
            );
            $aConditions[] = array ('C3.CON_LANG',$del . SYS_LANG . $del
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::PRO_UID,'C4.CON_ID'
            );
            $aConditions[] = array ('C4.CON_CATEGORY',$del . 'PRO_TITLE' . $del
            );
            $aConditions[] = array ('C4.CON_LANG',$del . SYS_LANG . $del
            );

            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $oCriteria->add( AppEventPeer::EVN_UID, '', Criteria::NOT_EQUAL );
            if ($sProcessUid != '') {
                $oCriteria->add( EventPeer::PRO_UID, $sProcessUid );
            }
            if ($EVN_ACTION != '') {
                $oCriteria->add( EventPeer::EVN_ACTION, $EVN_ACTION );
            }
            switch ($sStatus) {
                case '':
                    //Nothing
                    break;
                case 'PENDING':
                    $oCriteria->add( AppEventPeer::APP_EVN_STATUS, 'OPEN' );
                    break;
                case 'COMPLETED':
                    $oCriteria->add( AppEventPeer::APP_EVN_STATUS, 'CLOSE' );
                    break;
            }
            //$oCriteria->addDescendingOrderByColumn(AppEventPeer::APP_EVN_ACTION_DATE);
            return $oCriteria;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function executeEvents ($sNow, $debug = false, &$log = array(), $cron = 0)
    {

        require_once 'classes/model/Configuration.php';
        require_once 'classes/model/Triggers.php';
        G::LoadClass( 'case' );

        $debug = 1;
        $oCase = new Cases();

        try {
            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->addSelectColumn( AppEventPeer::APP_UID );
            $oCriteria->addSelectColumn( AppEventPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppEventPeer::EVN_UID );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ACTION_DATE );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ATTEMPTS );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_LAST_EXECUTION_DATE );
            $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_STATUS );
            $oCriteria->addSelectColumn( EventPeer::PRO_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_ACTION );
            $oCriteria->addSelectColumn( EventPeer::TRI_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_ACTION_PARAMETERS );
            $oCriteria->addSelectColumn( EventPeer::EVN_RELATED_TO );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::USR_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

            $oCriteria->addJoin( AppEventPeer::EVN_UID, EventPeer::EVN_UID, Criteria::JOIN );

            $aConditions = array ();
            array_push( $aConditions, Array (AppEventPeer::APP_UID,AppDelegationPeer::APP_UID
            ) );
            array_push( $aConditions, Array (AppEventPeer::DEL_INDEX,AppDelegationPeer::DEL_INDEX
            ) );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $oCriteria->addJoin( ApplicationPeer::APP_UID, AppEventPeer::APP_UID );

            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL ); //by me
            $oCriteria->add( AppEventPeer::APP_EVN_STATUS, 'OPEN' );
            $oCriteria->add( AppEventPeer::APP_EVN_ACTION_DATE, $sNow, Criteria::LESS_EQUAL );

            $oDataset = AppEventPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $c = 0;
            while ($oDataset->next()) {
                if ($cron == 1) {
                    $arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );
                    $arrayCron["processcTimeStart"] = time();
                    @file_put_contents( PATH_DATA . "cron", serialize( $arrayCron ) );
                }

                $c ++;
                $aRow = $oDataset->getRow();
                $oTrigger = new Triggers();
                $aFields = $oCase->loadCase( $aRow['APP_UID'] );
                $oAppEvent = AppEventPeer::retrieveByPK( $aRow['APP_UID'], $aRow['DEL_INDEX'], $aRow['EVN_UID'] );

                //g::pr($aRow); //die;


                if ($debug) {
                    require_once 'classes/model/Application.php';
                    $oApp = ApplicationPeer::retrieveByPk( $aRow['APP_UID'] );
                    $oEv = EventPeer::retrieveByPk( $aRow['EVN_UID'] );
                    $log[] = 'Event ' . $oEv->getEvnDescription() . ' with ID ' . $aRow['EVN_UID'];

                    println( "\nOK+ event \"" . $oEv->getEvnDescription() . "\" with ID {} was found" );
                    println( " - PROCESS................" . $aRow['PRO_UID'] );
                    println( " - APPLICATION............" . $aRow['APP_UID'] . " CASE #" . $oApp->getAppNumber() );
                    println( " - ACTION DATE............" . $aRow['APP_EVN_ACTION_DATE'] );
                    println( " - ATTEMPTS..............." . $aRow['APP_EVN_ATTEMPTS'] );
                    println( " - INTERVAL WITH TASKS...." . $aRow['EVN_RELATED_TO'] );
                }

                if ($aRow['TRI_UID'] == '') {
                    //a rare case when the tri_uid is not set.
                    $log[] = " (!) Any trigger was set................................SKIPPED and will be CLOSED";
                    if ($debug) {
                        println( " (!) Any trigger was set................................SKIPPED and will be CLOSED" );
                    }
                    $oAppEvent->setAppEvnStatus( 'CLOSE' );
                    $oAppEvent->save();
                    continue;
                }

                $oTrigger = TriggersPeer::retrieveByPk( $aRow['TRI_UID'] );
                if (! is_object( $oTrigger )) {
                    //the trigger record doesn't exist..
                    $log[] = ' (!) The trigger ' . $aRow['TRI_UID'] . ' ' . $oTrigger->getTriTitle() . " doesn't exist.......SKIPPED and will be CLOSED";
                    if ($debug) {
                        println( " (!) The trigger {$aRow['TRI_UID']} {$oTrigger->getTriTitle()} doesn't exist.......SKIPPED and will be CLOSED" );
                    }
                    $oAppEvent->setAppEvnStatus( 'CLOSE' );
                    $oAppEvent->save();
                    continue;
                }

                global $oPMScript;
                $oPMScript = new PMScript();

                $task = new Task();
                $taskFields = $task->Load( $aRow['TAS_UID'] );
                $aFields['APP_DATA']['APP_NUMBER'] = $aFields['APP_NUMBER'];
                $aFields['APP_DATA']['TAS_TITLE'] = $taskFields['TAS_TITLE'];
                $aFields['APP_DATA']['DEL_TASK_DUE_DATE'] = $aRow['DEL_TASK_DUE_DATE'];
                $oPMScript->setFields( $aFields['APP_DATA'] );
                $oPMScript->setScript( $oTrigger->getTriWebbot() );

                $oPMScript->execute();

                $oAppEvent->setAppEvnLastExecutionDate( date( 'Y-m-d H:i:s' ) );

                if (sizeof( $_SESSION['TRIGGER_DEBUG']['ERRORS'] ) == 0) {
                    $log[] = ' - The trigger ' . $oTrigger->getTriTitle() . ' was executed successfully!';
                    if ($debug) {
                        println( " - The trigger '{$oTrigger->getTriTitle()}' was executed successfully!" );
                        //g::pr($aFields);
                    }
                    $aFields['APP_DATA'] = $oPMScript->aFields;
                    $oCase->updateCase( $aRow['APP_UID'], $aFields );
                    $oAppEvent->setAppEvnStatus( 'CLOSE' );
                } else {
                    if ($debug) {
                        $log[] = ' - The trigger ' . $aRow['TRI_UID'] . ' throw some errors!';
                        println( " - The trigger {$aRow['TRI_UID']} throw some errors!" );
                        print_r( $_SESSION['TRIGGER_DEBUG']['ERRORS'] );
                    }
                    if ($oAppEvent->getAppEvnAttempts() > 0) {
                        $oAppEvent->setAppEvnAttempts( $oAppEvent->getAppEvnAttempts() - 1 );
                    } else {
                        $oAppEvent->setAppEvnStatus( 'CLOSE' );
                    }
                }
                $oAppEvent->save();
            }
            return $c;
        } catch (Exception $oError) {
            $log[] = ' Error execute event : ' . $oError->getMessage();
            die( $oError->getMessage() );
            return $oError->getMessage();
        }
    }

    public function close ($APP_UID, $DEL_INDEX)
    {
        $aRow = $this->load( $APP_UID, $DEL_INDEX );
        $aRow['APP_EVN_STATUS'] = 'CLOSE';
        $this->update( $aRow );
    }
}

