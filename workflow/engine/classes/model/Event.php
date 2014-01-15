<?php
/**
 * Event.php
 *
 * @package workflow.engine.classes.model
 */

//require_once 'classes/model/Content.php';
//require_once 'classes/model/om/BaseEvent.php';

/**
 * Skeleton subclass for representing a row from the 'EVENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */

//require_once 'classes/model/AppDelegation.php';
//require_once 'classes/model/AppEvent.php';
//require_once 'classes/model/Triggers.php';

/**
 *
 * @package workflow.engine.classes.model
 */

class Event extends BaseEvent
{

    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $evn_description = '';

    /**
     * Get the evn_description column value.
     *
     * @return string
     */
    public function getEvnDescription ()
    {
        if ($this->getEvnUid() == "") {
            throw (new Exception( "Error in getEvnDescription, the getEvnUid() can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->evn_description = Content::load( 'EVN_DESCRIPTION', '', $this->getEvnUid(), $lang );
        return $this->evn_description;
    }

    /**
     * Set the evn_description column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setEvnDescription ($v)
    {
        if ($this->getEvnUid() == "") {
            throw (new Exception( "Error in setEvnDescription, the setEvnUid() can't be blank" ));
        }
        $v = isset( $v ) ? ((string) $v) : '';
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        if ($this->evn_description !== $v || $v === "") {
            $this->evn_description = $v;
            $res = Content::addContent( 'EVN_DESCRIPTION', '', $this->getEvnUid(), $lang, $this->evn_description );
            return $res;
        }
        return 0;
    }

    public function load ($sUID)
    {
        try {
            $oEvent = EventPeer::retrieveByPK( $sUID );
            if (! is_null( $oEvent )) {
                $aFields = '';
                $aFields = $oEvent->toArray( BasePeer::TYPE_FIELDNAME );
                if ($aFields['EVN_TIME_UNIT'] == 'HOURS') {
                    $aFields['EVN_TAS_ESTIMATED_DURATION'] = round( $aFields['EVN_TAS_ESTIMATED_DURATION'] * 24, 2 );
                }
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                $this->setEvnDescription( $aFields['EVN_DESCRIPTION'] = $this->getEvnDescription() );
                //$aFields['EVN_CONDITIONS']        = unserialize($aFields['EVN_CONDITIONS']);
                $aFields['EVN_ACTION_PARAMETERS'] = unserialize( $aFields['EVN_ACTION_PARAMETERS'] );

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
        if (! isset( $aData['EVN_UID'] ) || $aData['EVN_UID'] == '') {
            $aData['EVN_UID'] = G::generateUniqueID();
        }

        $oConnection = Propel::getConnection( EventPeer::DATABASE_NAME );
        try {
            $oEvent = new Event();
            $oEvent->setEvnUid( $aData['EVN_UID'] );
            $oEvent->setProUid( $aData['PRO_UID'] );
            if (isset( $aData['EVN_RELATED_TO'] )) {
                $oEvent->setEvnRelatedTo( $aData['EVN_RELATED_TO'] );
                if ($aData['EVN_RELATED_TO'] == 'SINGLE') {
                    if (isset( $aData['TAS_UID'] )) {
                        $oEvent->setTasUid( $aData['TAS_UID'] );
                    }
                    $oEvent->setEvnTasUidTo( '' );
                    $oEvent->setEvnTasUidFrom( '' );
                } else {
                    $oEvent->setTasUid( '' );
                    if (isset( $aData['EVN_TAS_UID_TO'] )) {
                        $oEvent->setEvnTasUidTo( $aData['EVN_TAS_UID_TO'] );
                    }
                    if (isset( $aData['EVN_TAS_UID_FROM'] )) {
                        $oEvent->setEvnTasUidFrom( $aData['EVN_TAS_UID_FROM'] );
                    }
                }
            }

            if (isset( $aData['EVN_POSX'] )) {
                $oEvent->setEvnPosx( $aData['EVN_POSX'] );
            }
            if (isset( $aData['EVN_POSY'] )) {
                $oEvent->setEvnPosy( $aData['EVN_POSY'] );
            }
            if (isset( $aData['EVN_TYPE'] )) {
                $oEvent->setEvnType( $aData['EVN_TYPE'] );
            }
            if (isset( $aData['EVN_TIME_UNIT'] )) {
                $oEvent->setEvnTimeUnit( $aData['EVN_TIME_UNIT'] );
                if (trim( $aData['EVN_TIME_UNIT'] ) == 'HOURS') {
                    $aData['EVN_TAS_ESTIMATED_DURATION'] = $aData['EVN_TAS_ESTIMATED_DURATION'] / 24;
                }
            }
            if (isset( $aData['EVN_TAS_ESTIMATED_DURATION'] )) {
                $oEvent->setEvnTasEstimatedDuration( $aData['EVN_TAS_ESTIMATED_DURATION'] );
            }

            if (isset( $aData['EVN_WHEN_OCCURS'] )) {
                $oEvent->setEvnWhenOccurs( $aData['EVN_WHEN_OCCURS'] );
            }

            if (isset( $aData['EVN_ACTION'] )) {
                $oEvent->setEvnAction( $aData['EVN_ACTION'] );
            }

            if (isset( $aData['EVN_CONDITIONS'] )) {
                $oEvent->setEvnConditions( $aData['EVN_CONDITIONS'] );
            }
            if (isset( $aData['EVN_STATUS'] )) {
                $oEvent->setEvnStatus( $aData['EVN_STATUS'] );
            }
            if (isset( $aData['EVN_WHEN'] )) {
                $oEvent->setEvnWhen( $aData['EVN_WHEN'] );
            }

            $oEvent->setEvnMaxAttempts( 3 );

            //start the transaction
            $oConnection->begin();
            if (isset( $aData['EVN_TYPE'] )) {
                if ($aData['EVN_TYPE'] === 'bpmnEventEmptyEnd') {
                    unset( $aData['TRI_UID'] );
                }
            }
            if (isset( $aData['TRI_UID'] )) {
                $oTrigger = new Triggers();
                if (trim( $aData['TRI_UID'] ) === "" || (! $oTrigger->TriggerExists( $aData['TRI_UID'] ))) {
                    //create an empty trigger
                    $aTrigger = array ();
                    $aTrigger['PRO_UID'] = $aData['PRO_UID'];
                    $aTrigger['TRI_TITLE'] = 'For event: ' . $aData['EVN_DESCRIPTION'];
                    $aTrigger['TRI_DESCRIPTION'] = 'Autogenerated ' . $aTrigger['TRI_TITLE'];
                    $aTrigger['TRI_WEBBOT'] = '// ' . $aTrigger['TRI_DESCRIPTION'];
                    ;
                    $oTrigger->create( $aTrigger );
                } else {
                    $oTrigger = TriggersPeer::retrieveByPk( $aData['TRI_UID'] );
                }

                $oEvent->setTriUid( $oTrigger->getTriUid() );

                $parameters = new StdClass();
                $parameters->hash = md5( $oTrigger->getTriWebbot() );

                if (isset( $aData['EVN_ACTION_PARAMETERS']->SUBJECT )) {
                    $parameters->SUBJECT = $aData['EVN_ACTION_PARAMETERS']->SUBJECT;
                    $parameters->TO = $aData['EVN_ACTION_PARAMETERS']->TO;
                    $parameters->CC = $aData['EVN_ACTION_PARAMETERS']->CC;
                    $parameters->BCC = $aData['EVN_ACTION_PARAMETERS']->BCC;
                    $parameters->TEMPLATE = $aData['EVN_ACTION_PARAMETERS']->TEMPLATE;
                }

                $oEvent->setEvnActionParameters( serialize( $parameters ) );
            }

            if ($oEvent->validate()) {
                $iResult = $oEvent->save();
                if (isset( $aData['EVN_DESCRIPTION'] )) {
                    $oEvent->setEvnDescription( $aData['EVN_DESCRIPTION'] );
                }
                $oConnection->commit();
                return $aData['EVN_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oEvent->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The row Event cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function update ($aData)
    {
        $oConnection = Propel::getConnection( EventPeer::DATABASE_NAME );
        try {
            $oEvent = EventPeer::retrieveByPK( $aData['EVN_UID'] );
            if (! is_null( $oEvent )) {

                //$oEvent->setProUid( $aData['PRO_UID'] );
                if (isset( $aData['EVN_RELATED_TO'] )) {
                    $oEvent->setEvnRelatedTo( $aData['EVN_RELATED_TO'] );
                    if ($aData['EVN_RELATED_TO'] == 'SINGLE') {
                        if (isset( $aData['TAS_UID'] ) && $aData['TAS_UID'] != '') {
                            $oEvent->setTasUid( $aData['TAS_UID'] );
                        }
                        $oEvent->setEvnTasUidTo( '' );
                        $oEvent->setEvnTasUidFrom( '' );
                    } else {
                        $oEvent->setTasUid( '' );
                        if (isset( $aData['EVN_TAS_UID_TO'] )) {
                            $oEvent->setEvnTasUidTo( $aData['EVN_TAS_UID_TO'] );
                        }
                        if (isset( $aData['EVN_TAS_UID_FROM'] )) {
                            $oEvent->setEvnTasUidFrom( $aData['EVN_TAS_UID_FROM'] );
                        }
                    }
                }

                if (isset( $aData['EVN_POSX'] )) {
                    $oEvent->setEvnPosx( $aData['EVN_POSX'] );
                }
                if (isset( $aData['EVN_POSY'] )) {
                    $oEvent->setEvnPosy( $aData['EVN_POSY'] );
                }
                if (isset( $aData['EVN_TIME_UNIT'] )) {
                    $oEvent->setEvnTimeUnit( $aData['EVN_TIME_UNIT'] );
                    if ($aData['EVN_TIME_UNIT'] == 'HOURS') {
                        $aData['EVN_TAS_ESTIMATED_DURATION'] = $aData['EVN_TAS_ESTIMATED_DURATION'] / 24;
                    }
                }
                if (isset( $aData['EVN_TAS_ESTIMATED_DURATION'] )) {
                    $oEvent->setEvnTasEstimatedDuration( $aData['EVN_TAS_ESTIMATED_DURATION'] );
                }

                if (isset( $aData['EVN_WHEN_OCCURS'] )) {
                    $oEvent->setEvnWhenOccurs( $aData['EVN_WHEN_OCCURS'] );
                }

                if (isset( $aData['EVN_STATUS'] )) {
                    $oEvent->setEvnStatus( $aData['EVN_STATUS'] );
                }

                if (isset( $aData['EVN_WHEN'] )) {
                    $oEvent->setEvnWhen( $aData['EVN_WHEN'] );
                }

                if (isset( $aData['TRI_UID'] )) {
                    $oEvent->setTriUid( $aData['TRI_UID'] );
                }

                if (isset( $aData['EVN_TYPE'] )) {
                    $oEvent->setEvnType( $aData['EVN_TYPE'] );
                }

                if (isset( $aData['EVN_CONDITIONS'] )) {
                    $oEvent->setEvnConditions( $aData['EVN_CONDITIONS'] );
                }

                if (isset( $aData['EVN_ACTION'] )) {
                    $oEvent->setEvnAction( $aData['EVN_ACTION'] );
                    //if ( isset ($aData['ENV_MAX_ATTEMPTS'] )) $oEvent->setEvnMaxAttempts( 3 );
                }

                if (isset( $aData['EVN_ACTION_PARAMETERS'] ) && $aData['EVN_ACTION_PARAMETERS'] != 0) {

                    $oTP = new TemplatePower( PATH_TPL . 'events' . PATH_SEP . 'sendMessage.tpl' );
                    $oTP->prepare();

                    $oTP->assign( 'from', '<info@processmaker.com>' );
                    $oTP->assign( 'subject', addslashes( $aData['EVN_ACTION_PARAMETERS']['SUBJECT'] ) );
                    $oTP->assign( 'template', $aData['EVN_ACTION_PARAMETERS']['TEMPLATE'] );
                    $oTP->assign( 'timestamp', date( "l jS \of F Y h:i:s A" ) );

                    $recipientTO = implode( ',', $aData['EVN_ACTION_PARAMETERS']['TO'] );
                    $recipientCC = implode( ',', $aData['EVN_ACTION_PARAMETERS']['CC'] );
                    $recipientBCC = implode( ',', $aData['EVN_ACTION_PARAMETERS']['BCC'] );

                    $oTP->assign( 'TO', addslashes( $recipientTO ) );
                    $oTP->assign( 'CC', addslashes( $recipientCC ) );
                    $oTP->assign( 'BCC', addslashes( $recipientBCC ) );

                    $sTrigger = $oTP->getOutputContent();

                    $oTrigger = new Triggers();
                    $aTrigger = $oTrigger->load( $oEvent->getTriUid() );
                    $aTrigger['TRI_WEBBOT'] = $sTrigger;
                    $oTrigger->update( $aTrigger );
                    $oParameters = new StdClass();
                    $oParameters->hash = md5( $sTrigger );
                    $oParameters->SUBJECT = $aData['EVN_ACTION_PARAMETERS']['SUBJECT'];
                    $oParameters->TO = $aData['EVN_ACTION_PARAMETERS']['TO'];
                    $oParameters->CC = $aData['EVN_ACTION_PARAMETERS']['CC'];
                    $oParameters->BCC = $aData['EVN_ACTION_PARAMETERS']['BCC'];
                    $oParameters->TEMPLATE = $aData['EVN_ACTION_PARAMETERS']['TEMPLATE'];

                    //$oParameters->TRI_UID  = $sTrigger->getTriUid();


                    $oEvent->setEvnActionParameters( serialize( $oParameters ) );
                }

                if ($oEvent->validate()) {
                    //start the transaction
                    $oConnection->begin();
                    if (array_key_exists( 'EVN_DESCRIPTION', $aData )) {
                        $oEvent->setEvnDescription( $aData['EVN_DESCRIPTION'] );
                    }
                    $iResult = $oEvent->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oEvent->getValidationFailures();
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

    public function remove ($sUID)
    {
        $oConnection = Propel::getConnection( EventPeer::DATABASE_NAME );
        try {
            $oEvent = EventPeer::retrieveByPK( $sUID );
            if (! is_null( $oEvent )) {
                /* with the new feature for events, a event can to relate a existing trigger
                   or more of one events can be reusing the same trigger
                   so, in this point we should't to delete the trigger

                   $oConnection->begin();
                   $oTrigger = new Triggers();
                   $oAppEvent = new AppEvent();

                   $oCriteria = new Criteria('workflow');
                   $oCriteria->clearSelectColumns();
                   $oCriteria->addSelectColumn( AppEventPeer::EVN_UID );
                   $oCriteria->addSelectColumn( EventPeer::TRI_UID );
                   $oCriteria->addSelectColumn( AppEventPeer::APP_UID );
                   $oCriteria->addSelectColumn( AppEventPeer::DEL_INDEX );
                   $oCriteria->add(AppEventPeer::EVN_UID, $sUID );
                   $oCriteria->addJoin(EventPeer::EVN_UID, AppEventPeer::EVN_UID, Criteria::JOIN);
                   $oDataset = AppEventPeer::doSelectRs($oCriteria);
                   $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                   $oDataset->next();

                   while ($row = $oDataset->getRow()) {
                   $oTrigger->remove($row['TRI_UID']);
                   $oAppEvent->remove( $row['APP_UID'], $row['DEL_INDEX'], $sUID );
                   $oDataset->next();
                 }*/
                Content::removeContent( 'EVN_DESCRIPTION', '', $oEvent->getEvnUid() );

                $iResult = $oEvent->delete();
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

    public function calculateEventsExecutionDate ()
    {
        $line1 = '';
        $line2 = '';
        $line1 = $this->calculateExecutionDateSingle();
        //$line2 = $this->calculateExecutionDateMultiple();
        return $line1 . "<br>\n" . $line2;
    }

    public function calculateExecutionDateSingle ()
    {
        try {
            $rowsCreated = 0;
            $rowsRejected = 0;
            G::LoadClass( 'calendar' );
            $oCalendar = new calendar();

            //SELECT
            //  EVENT.PRO_UID,
            //  EVENT.TAS_UID ,
            //  EVENT.EVN_TAS_ESTIMATED_DURATION ,
            //  EVENT.EVN_WHEN,
            //  APP_DELEGATION.APP_UID  ,
            //  APP_DELEGATION.DEL_INDEX  ,
            //  APP_DELEGATION.TAS_UID  ,
            //  APP_DELEGATION.DEL_DELEGATE_DATE  ,
            //  APP_DELEGATION.DEL_INIT_DATE  ,
            //  APP_DELEGATION.DEL_TASK_DUE_DATE  ,
            //  APP_DELEGATION.DEL_FINISH_DATE
            //from APP_DELEGATION
            //  JOIN EVENT ON ( APP_DELEGATION.TAS_UID = EVENT.TAS_UID AND APP_DELEGATION.DEL_FINISH_DATE IS NULL  )
            //  LEFT JOIN APP_EVENT ON ( APP_EVENT.APP_UID = APP_DELEGATION.APP_UID AND APP_EVENT.DEL_INDEX = APP_DELEGATION.DEL_INDEX )
            // WHERE
            //   APP_EVENT.APP_UID IS NULL
            //   and EVN_STATUS = 'ACTIVE'
            //   AND EVN_RELATED_TO = 'SINGLE'
            //   and DEL_FINISH_DATE IS NULL
            //--  and   APP_DELEGATION.DEL_DELEGATE_DATE > "2009-01-01 12:00:00"
            //ORDER BY    APP_DELEGATION.DEL_DELEGATE_DATE


            //get info about the Event and the APP_DELEGATION to process
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( EventPeer::EVN_UID );
            $oCriteria->addSelectColumn( EventPeer::PRO_UID );
            $oCriteria->addSelectColumn( EventPeer::TAS_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_ESTIMATED_DURATION );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN_OCCURS );
            $oCriteria->addSelectColumn( EventPeer::EVN_RELATED_TO );
            $oCriteria->addSelectColumn( EventPeer::EVN_MAX_ATTEMPTS );
            $oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::TAS_UID,EventPeer::TAS_UID
            );
            //$aConditions[] = array(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::IS_NULL ); //is null is supported by addJoinMC by the way.
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::APP_UID,AppEventPeer::APP_UID
            );
            $aConditions[] = array (AppDelegationPeer::DEL_INDEX,AppEventPeer::DEL_INDEX
            );
            $aConditions[] = array (EventPeer::EVN_UID,AppEventPeer::EVN_UID
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $oCriteria->add( AppEventPeer::APP_UID, null, Criteria::ISNULL );
            $oCriteria->add( EventPeer::EVN_STATUS, 'ACTIVE' );
            $oCriteria->add( EventPeer::EVN_RELATED_TO, 'SINGLE' );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
            //      $oCriteria->add(AppDelegationPeer::DEL_DELEGATE_DATE, date('Y-m-d') , Criteria::GREATER_THAN );
            $oDataset = EventPeer::doSelectRs( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $aRows = Array ();
            while ($oDataset->next()) {
                $aRows[] = $oDataset->getRow();
            }

            g::pr( $aRows );
            die();

            $oDataset->next();

            while ($aData = $oDataset->getRow()) {
                $estimatedDuration = (float) $aData['EVN_TAS_ESTIMATED_DURATION'];
                $when = (float) $aData['EVN_WHEN'];
                $whenOccurs = $aData['EVN_WHEN_OCCURS'];
 
                if ($oCalendar->pmCalendarUid == '') {
                	$oCalendar->getCalendar(null, $aData['PRO_UID'], $aData['TAS_UID']);
                	$oCalendar->getCalendarData();
                }
                
                if ($whenOccurs == 'AFTER_TIME') {
                    //for multiple $sDueDate = date('Y-m-d H:i:s', $oDates->calculateDate($aData['DEL_DELEGATE_DATE'], $estimatedDuration, 'days', 1));
                    $sDueDate = $aData['DEL_TASK_DUE_DATE'];
                    $calculatedDueDateA = $oCalendar->calculateDate( $sDueDate, $when, 'days' );
                    $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
                    $validStartDate = ($sActionDate >= $aData['DEL_DELEGATE_DATE']);
                } else {
                    $sDueDate = $aData['DEL_DELEGATE_DATE'];
                    $calculatedDueDateA = $oCalendar->calculateDate( $sDueDate, $when, 'days' );
                    $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
                    $validStartDate = ($sActionDate >= $aData['DEL_DELEGATE_DATE']);
                }
                $aData['APP_EVN_ACTION_DATE'] = $sActionDate;
                $aData['APP_EVN_ATTEMPTS'] = $aData['EVN_MAX_ATTEMPTS'];

                if ($validStartDate) {
                    $rowsCreated ++;
                    $oAppEvent = new AppEvent();
                    $oAppEvent->create( $aData );
                } else {
                    $rowsRejected ++;
                    $aData['APP_EVN_STATUS'] = 'INVALID';
                    $oAppEvent = new AppEvent();
                    $oAppEvent->create( $aData );
                }
                $oDataset->next();
            }
            return "Created $rowsCreated SINGLE rows in APP_EVENT and rejected $rowsRejected rows ";
        } catch (Exception $oError) {
            throw new Exception( $oError->getMessage() );
        }
    }

    public function calculateExecutionDateMultiple ()
    {
        try {
            $rowsCreated = 0;
            $rowsRejected = 0;
            G::LoadClass( 'calendar' );
            $oCalendar = new calendar();
            // SELECT TASK2.* ,
            //   EVENT.EVN_UID, EVENT.PRO_UID, EVENT.EVN_TAS_UID_FROM,
            //   EVENT.EVN_TAS_ESTIMATED_DURATION, EVENT.EVN_WHEN,
            //   EVENT.EVN_WHEN_OCCURS, EVENT.EVN_RELATED_TO, APP_DELEGATION.APP_UID, APP_DELEGATION.DEL_INDEX, APP_DELEGATION.TAS_UID,
            //   APP_DELEGATION.DEL_DELEGATE_DATE, APP_DELEGATION.DEL_INIT_DATE, APP_DELEGATION.DEL_TASK_DUE_DATE,
            //   APP_DELEGATION.DEL_FINISH_DATE
            // FROM
            //   APP_DELEGATION
            //   LEFT JOIN EVENT ON (APP_DELEGATION.TAS_UID=EVENT.EVN_TAS_UID_FROM)
            //   LEFT JOIN APP_EVENT ON (APP_DELEGATION.APP_UID=APP_EVENT.APP_UID AND APP_DELEGATION.DEL_INDEX=APP_EVENT.DEL_INDEX)
            //   LEFT JOIN APP_DELEGATION AS TASK2 ON (TASK2.TAS_UID = EVENT.EVN_TAS_UID_TO AND TASK2.APP_UID = APP_DELEGATION.APP_UID )
            //
            // WHERE
            //   APP_EVENT.APP_UID IS NULL
            //   AND EVENT.EVN_STATUS='ACTIVE'
            //   AND EVENT.EVN_RELATED_TO='MULTIPLE'
            //   AND TASK2.DEL_FINISH_DATE IS NULL
            //get info about the Event and the APP_DELEGATION to process
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( EventPeer::EVN_UID );
            $oCriteria->addSelectColumn( EventPeer::PRO_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_FROM );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_ESTIMATED_DURATION );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN_OCCURS );
            $oCriteria->addSelectColumn( EventPeer::EVN_RELATED_TO );
            $oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::TAS_UID,EventPeer::EVN_TAS_UID_FROM
            );
            //$aConditions[] = array(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::IS_NULL );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::APP_UID,AppEventPeer::APP_UID
            );
            $aConditions[] = array (AppDelegationPeer::DEL_INDEX,AppEventPeer::DEL_INDEX
            );
            $aConditions[] = array (EventPeer::EVN_UID,AppEventPeer::EVN_UID
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $oCriteria->addAlias( 'DEL2', 'APP_DELEGATION' );
            $aConditions = array ();
            $aConditions[] = array (AppDelegationPeer::APP_UID,'DEL2.APP_UID'
            );
            $aConditions[] = array (EventPeer::EVN_TAS_UID_TO,'DEL2.TAS_UID'
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

            $oCriteria->add( AppEventPeer::APP_UID, null, Criteria::ISNULL );
            $oCriteria->add( EventPeer::EVN_STATUS, 'ACTIVE' );
            $oCriteria->add( EventPeer::EVN_RELATED_TO, 'MULTIPLE' );
            $oCriteria->add( 'DEL2.DEL_FINISH_DATE', null, Criteria::ISNULL );
            //      $oCriteria->add(AppDelegationPeer::DEL_DELEGATE_DATE, date('Y-m-d') , Criteria::GREATER_THAN );
            $oDataset = EventPeer::doSelectRs( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aData = $oDataset->getRow()) {
                $estimatedDuration = (float) $aData['EVN_TAS_ESTIMATED_DURATION'];
                $when = (float) $aData['EVN_WHEN'];
                $whenOccurs = $aData['EVN_WHEN_OCCURS'];
                
                if ($oCalendar->pmCalendarUid == '') {
                	$oCalendar->getCalendar(null, $aData['PRO_UID'], $aData['TAS_UID']);
                	$oCalendar->getCalendarData();
                }

                if ($whenOccurs == 'AFTER_TIME') {
                    //for multiple $sDueDate = date('Y-m-d H:i:s', $oDates->calculateDate($aData['DEL_DELEGATE_DATE'], $estimatedDuration, 'days', 1));
                    $sDueDate = $aData['DEL_TASK_DUE_DATE'];
                    $calculatedDueDateA = $oCalendar->calculateDate( $sDueDate, $when, 'days' );
                    $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
                    $validStartDate = ($sActionDate >= $aData['DEL_DELEGATE_DATE']);
                } else {
                    $sDueDate = $aData['DEL_DELEGATE_DATE'];
                    $calculatedDueDateA = $oCalendar->calculateDate( $sDueDate, $when, 'days' );
                    $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
                    $validStartDate = ($sActionDate >= $aData['DEL_DELEGATE_DATE']);
                }
                $aData['APP_EVN_ACTION_DATE'] = $sActionDate;

                if ($validStartDate) {
                    $rowsCreated ++;
                    $oAppEvent = new AppEvent();
                    $oAppEvent->create( $aData );
                } else {
                    $rowsRejected ++;
                    $aData['APP_EVN_STATUS'] = 'INVALID';
                    $oAppEvent = new AppEvent();
                    $oAppEvent->create( $aData );
                }
                $oDataset->next();
            }
            return "Created $rowsCreated MULTIPLE rows in APP_EVENT and rejected $rowsRejected rows ";
        } catch (Exception $oError) {
            throw new Exception( $oError->getMessage() );
        }
    }

    public function closeAppEvents ($PRO_UID, $APP_UID, $DEL_INDEX, $TAS_UID)
    {
        $aAppEvents = $this->getAppEvents( $APP_UID, $DEL_INDEX );
        if ($aAppEvents) {
            foreach ($aAppEvents as $aRow) {
                if ($aRow['EVN_RELATED_TO'] == 'SINGLE' || ($aRow['EVN_RELATED_TO'] == $TAS_UID)) {
                    $oAppEvent = AppEventPeer::retrieveByPK( $aRow['APP_UID'], $aRow['DEL_INDEX'], $aRow['EVN_UID'] );
                    $oAppEvent->setAppEvnLastExecutionDate( date( 'Y-m-d H:i:s' ) );
                    $oAppEvent->setAppEvnStatus( 'CLOSE' );
                    $oAppEvent->save();
                }
            }
        }
    }

    public function createAppEvents ($PRO_UID, $APP_UID, $DEL_INDEX, $TAS_UID)
    {
        $aRows = Array ();
        $aEventsRows = $this->getBy( $PRO_UID, $TAS_UID );
        if ($aEventsRows !== false) {
            $aRows = array_merge( $aRows, $aEventsRows );
        }
        foreach ($aRows as $aData) {
            // if the events has a condition
            if (trim( $aData['EVN_CONDITIONS'] ) != '') {
                G::LoadClass( 'case' );
                $oCase = new Cases();
                $aFields = $oCase->loadCase( $APP_UID );

                $Fields = $aFields['APP_DATA'];
                $conditionContents = trim( $aData['EVN_CONDITIONS'] );

                //$sContent    = G::unhtmlentities($sContent);
                $iAux = 0;
                $iOcurrences = preg_match_all( '/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $conditionContents, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );

                if ($iOcurrences) {
                    for ($i = 0; $i < $iOcurrences; $i ++) {
                        preg_match_all( '/@>' . $aMatch[2][$i][0] . '([\w\W]*)' . '@<' . $aMatch[2][$i][0] . '/', $conditionContents, $aMatch2, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
                        $sGridName = $aMatch[2][$i][0];
                        $sStringToRepeat = $aMatch2[1][0][0];
                        if (isset( $Fields[$sGridName] )) {
                            if (is_array( $Fields[$sGridName] )) {
                                $sAux = '';
                                foreach ($Fields[$sGridName] as $aRow) {
                                    $sAux .= G::replaceDataField( $sStringToRepeat, $aRow );
                                }
                            }
                        }
                        $conditionContents = str_replace( '@>' . $sGridName . $sStringToRepeat . '@<' . $sGridName, $sAux, $conditionContents );
                    }
                }

                $sCondition = G::replaceDataField( $conditionContents, $Fields );

                $evalConditionResult = false;

                $sCond = 'try{ $evalConditionResult=(' . $sCondition . ')? true: false; } catch(Exception $e){$evalConditionResult=false;}';
                @eval( $sCond );

                if (! $evalConditionResult) {
                    continue;
                }
            }

            $appEventData['APP_UID'] = $APP_UID;
            $appEventData['DEL_INDEX'] = $DEL_INDEX;
            $appEventData['EVN_UID'] = $aData['EVN_UID'];
            $appEventData['APP_EVN_ACTION_DATE'] = $this->toCalculateTime( $aData );
            $appEventData['APP_EVN_ATTEMPTS'] = 3;
            $appEventData['APP_EVN_LAST_EXECUTION_DATE'] = null;
            $appEventData['APP_EVN_STATUS'] = 'OPEN';

            $oAppEvent = new AppEvent();
            $oAppEvent->create( $appEventData );

        }
    }

    public function verifyTaskbetween ($PRO_UID, $taskFrom, $taskTo, $taskVerify)
    {
        $criteria = new Criteria( 'workflow' );
        $criteria->addSelectColumn( RoutePeer::ROU_NEXT_TASK );

        $criteria->add( RoutePeer::PRO_UID, $PRO_UID );
        $criteria->add( RoutePeer::TAS_UID, $taskFrom );

        $dataset = RoutePeer::doSelectRs( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        if ($dataset->next()) {
            $routeData = $dataset->getRow();
            switch ($routeData['ROU_NEXT_TASK']) {
                case $taskVerify:
                    return true;
                    break;
                case $taskTo:
                case '-1':
                    return false;
                    break;
                default:
                    return $this->verifyTaskbetween( $PRO_UID, $routeData['ROU_NEXT_TASK'], $taskTo, $taskVerify );
                    break;
            }
        }
    }

    public function getBy ($PRO_UID, $taskUid)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( EventPeer::EVN_UID );
        $oCriteria->addSelectColumn( EventPeer::TAS_UID );
        $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_FROM );
        $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_TO );

        $oCriteria->add( EventPeer::EVN_STATUS, 'ACTIVE' );
        $oCriteria->add( EventPeer::EVN_ACTION, '', Criteria::NOT_EQUAL );
        $oCriteria->add( EventPeer::PRO_UID, $PRO_UID, Criteria::EQUAL );

        $oDataset = EventPeer::doSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $eventsTask = array ();
        while ($oDataset->next()) {
            $aDataEvent = $oDataset->getRow();

            if ($taskUid == $aDataEvent['TAS_UID'] || $taskUid == $aDataEvent['EVN_TAS_UID_FROM'] || $taskUid == $aDataEvent['EVN_TAS_UID_TO']) {
                $eventsTask[] = $aDataEvent['EVN_UID'];
            } else {
                $flag = $this->verifyTaskbetween( $PRO_UID, $aDataEvent['EVN_TAS_UID_FROM'], $aDataEvent['EVN_TAS_UID_TO'], $taskUid );
                if ($flag) {
                    $eventsTask[] = $aDataEvent['EVN_UID'];
                }
            }
        }

        $aRows = Array ();
        if (count( $eventsTask ) > 0) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( EventPeer::EVN_UID );
            $oCriteria->addSelectColumn( EventPeer::PRO_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_STATUS );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN_OCCURS );
            $oCriteria->addSelectColumn( EventPeer::EVN_RELATED_TO );
            $oCriteria->addSelectColumn( EventPeer::TAS_UID );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_FROM );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_TO );
            $oCriteria->addSelectColumn( EventPeer::EVN_TAS_ESTIMATED_DURATION );
            $oCriteria->addSelectColumn( EventPeer::EVN_WHEN );
            $oCriteria->addSelectColumn( EventPeer::EVN_MAX_ATTEMPTS );
            $oCriteria->addSelectColumn( EventPeer::EVN_ACTION );
            $oCriteria->addSelectColumn( EventPeer::EVN_CONDITIONS );
            $oCriteria->addSelectColumn( EventPeer::EVN_ACTION_PARAMETERS );
            $oCriteria->addSelectColumn( EventPeer::TRI_UID );

            $oCriteria->add( EventPeer::EVN_UID, (array) $eventsTask, Criteria::IN );

            $oDataset = EventPeer::doSelectRs( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            while ($oDataset->next()) {
                $aRows[] = $oDataset->getRow();
            }
        }

        return (count( $aRows ) > 0) ? $aRows : false;
    }

    public function getAppEvents ($APP_UID, $DEL_INDEX)
    {
        //for single task event
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( AppEventPeer::APP_UID );
        $oCriteria->addSelectColumn( AppEventPeer::DEL_INDEX );
        $oCriteria->addSelectColumn( AppEventPeer::EVN_UID );
        $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ACTION_DATE );
        $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_ATTEMPTS );
        $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_LAST_EXECUTION_DATE );
        $oCriteria->addSelectColumn( AppEventPeer::APP_EVN_STATUS );
        $oCriteria->addSelectColumn( EventPeer::EVN_UID );
        $oCriteria->addSelectColumn( EventPeer::PRO_UID );
        $oCriteria->addSelectColumn( EventPeer::EVN_STATUS );
        $oCriteria->addSelectColumn( EventPeer::EVN_WHEN_OCCURS );
        $oCriteria->addSelectColumn( EventPeer::EVN_RELATED_TO );
        $oCriteria->addSelectColumn( EventPeer::TAS_UID );
        $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_FROM );
        $oCriteria->addSelectColumn( EventPeer::EVN_TAS_UID_TO );
        $oCriteria->addSelectColumn( EventPeer::EVN_TAS_ESTIMATED_DURATION );
        $oCriteria->addSelectColumn( EventPeer::EVN_WHEN );
        $oCriteria->addSelectColumn( EventPeer::EVN_MAX_ATTEMPTS );
        $oCriteria->addSelectColumn( EventPeer::EVN_ACTION );
        $oCriteria->addSelectColumn( EventPeer::EVN_CONDITIONS );
        $oCriteria->addSelectColumn( EventPeer::EVN_ACTION_PARAMETERS );
        $oCriteria->addSelectColumn( EventPeer::TRI_UID );

        $oCriteria->addJoin( AppEventPeer::EVN_UID, EventPeer::EVN_UID );

        $oCriteria->add( AppEventPeer::APP_UID, $APP_UID );
        $oCriteria->add( AppEventPeer::DEL_INDEX, $DEL_INDEX );
        $oCriteria->add( AppEventPeer::APP_EVN_STATUS, 'OPEN' );

        $oDataset = AppEventPeer::doSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $aRows = Array ();
        while ($oDataset->next()) {
            $aRows[] = $oDataset->getRow();
        }

        return (count( $aRows ) > 0) ? $aRows : false;
    }

    public function toCalculateTime ($aData, $iDate = null)
    {
        G::LoadClass( 'calendar' );
        $oCalendar = new calendar();

        $iDate = isset( $iDate ) ? $iDate : date( 'Y-m-d H:i:s' );

        $estimatedDuration = $aData['EVN_TAS_ESTIMATED_DURATION']; //task duration
        $when = $aData['EVN_WHEN']; //how many days
        $whenOccurs = $aData['EVN_WHEN_OCCURS']; //time on action (AFTER_TIME/TASK_STARTED)

        if ($oCalendar->pmCalendarUid == '') {
        	$oCalendar->getCalendar(null, $aData['PRO_UID'], $aData['TAS_UID']);
        	$oCalendar->getCalendarData();
        }

        if ($whenOccurs == 'TASK_STARTED') {

            $calculatedDueDateA = $oCalendar->calculateDate( $iDate, $when, 'days' );

            $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
        } else {
            $calculatedDueDateA = $oCalendar->calculateDate( $iDate, $estimatedDuration + $when, 'days' );
            $sActionDate = date( 'Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS'] );
        }

        return $sActionDate;
    }

    public function Exists ($sUid)
    {
        try {
            $oObj = EventPeer::retrieveByPk( $sUid );
            return (is_object( $oObj ) && get_class( $oObj ) == 'Event');
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function existsByTaskUidFrom ($TAS_UID_FROM)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( 'COUNT(*) AS COUNT_EVENTS' );
        $oCriteria->add( EventPeer::EVN_TAS_UID_FROM, $TAS_UID_FROM );

        $oDataset = EventPeer::doSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();

        return ($aRow['COUNT_EVENTS'] != 0) ? true : false;
    }

    public function getRowByTaskUidFrom ($TAS_UID_FROM)
    {
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->add( EventPeer::EVN_TAS_UID_FROM, $TAS_UID_FROM );
        $oDataset = EventPeer::doSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return $aRow;
    }
}

