<?php

//require_once 'classes/model/om/BaseAppNotes.php';
use ProcessMaker\Core\System;

/**
 * Skeleton subclass for representing a row from the 'APP_NOTES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package classes.model
 */
class AppNotes extends BaseAppNotes
{
    public function getNotesList (
        $appUid,
        $usrUid = '',
        $start = '',
        $limit = 25,
        $sort = 'APP_NOTES.NOTE_DATE',
        $dir = 'DESC',
        $dateFrom = '',
        $dateTo = '',
        $search = '')
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->clearSelectColumns();

        $Criteria->addSelectColumn( AppNotesPeer::APP_UID );
        $Criteria->addSelectColumn( AppNotesPeer::USR_UID );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_DATE );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_CONTENT );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_TYPE );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_AVAILABILITY );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_ORIGIN_OBJ );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_AFFECTED_OBJ1 );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_AFFECTED_OBJ2 );
        $Criteria->addSelectColumn( AppNotesPeer::NOTE_RECIPIENTS );
        $Criteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $Criteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $Criteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $Criteria->addSelectColumn( UsersPeer::USR_EMAIL );

        $Criteria->addJoin( AppNotesPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );

        $Criteria->add( AppNotesPeer::APP_UID, $appUid, Criteria::EQUAL );

        if ($usrUid != '') {
            $Criteria->add( AppNotesPeer::USR_UID, $usrUid, Criteria::EQUAL );
        }
        if ($dateFrom != '') {
            $Criteria->add( AppNotesPeer::NOTE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
        }
        if ($dateTo != '') {
            $Criteria->add( AppNotesPeer::NOTE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }
        if ($search != '') {
            $Criteria->add( AppNotesPeer::NOTE_CONTENT, '%'.$search.'%', Criteria::LIKE );
        }

        if ($dir == 'DESC') {
            $Criteria->addDescendingOrderByColumn($sort);
        } else {
            $Criteria->addAscendingOrderByColumn($sort);
        }

        $response = array ();
        $totalCount = AppNotesPeer::doCount( $Criteria );
        $response['totalCount'] = $totalCount;
        $response['notes'] = array ();

        if ($start != '') {
            $Criteria->setLimit( $limit );
            $Criteria->setOffset( $start );
        }

        $oDataset = appNotesPeer::doSelectRS( $Criteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            $aRow['NOTE_CONTENT'] = htmlentities(stripslashes($aRow['NOTE_CONTENT']), ENT_QUOTES, 'UTF-8');
            $response['notes'][] = $aRow;
            $oDataset->next();
        }

        $result['criteria'] = $Criteria;
        $result['array'] = $response;

        return $result;
    }

    public function postNewNote ($appUid, $usrUid, $noteContent, $notify = true, $noteAvalibility = "PUBLIC", $noteRecipients = "", $noteType = "USER", $noteDate = "now")
    {
        $this->setAppUid( $appUid );
        $this->setUsrUid( $usrUid );
        $this->setNoteDate( $noteDate );
        $this->setNoteContent( $noteContent );
        $this->setNoteType( $noteType );
        $this->setNoteAvailability( $noteAvalibility );
        $this->setNoteOriginObj( '' );
        $this->setNoteAffectedObj1( '' );
        $this->setNoteAffectedObj2( '' );
        $this->setNoteRecipients( $noteRecipients );

        if ($this->validate()) {
            // we save it, since we get no validation errors, or do whatever else you like.
            $res = $this->save();
            $msg = '';
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $this->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }
        if ($msg != "") {
            $response['success'] = G::LoadTranslation("ID_FAILURE");
            $response['message'] = $msg;
        } else {
            $response['success'] = 'success';
            $response['message'] = '';
        }

        if ($notify) {
            if ($noteRecipients == "") {
                $noteRecipientsA = array ();
                $oCase = new Cases();
                $p = $oCase->getUsersParticipatedInCase( $appUid );
                foreach ($p['array'] as $key => $userParticipated) {
                    $noteRecipientsA[] = $key;
                }
                $noteRecipients = implode( ",", $noteRecipientsA );
            }

            $this->sendNoteNotification( $appUid, $usrUid, $noteContent, $noteRecipients );
        }

        return $response;
    }

    public function sendNoteNotification ($appUid, $usrUid, $noteContent, $noteRecipients, $sFrom = "")
    {
        try {

            $aConfiguration = System::getEmailConfiguration();

            $msgError = "";
            if (! isset( $aConfiguration['MESS_ENABLED'] ) || $aConfiguration['MESS_ENABLED'] != '1') {
                $msgError = "The default configuration wasn't defined";
                $aConfiguration['MESS_ENGINE'] = '';
            }

            $oUser = new Users();
            $aUser = $oUser->load( $usrUid );
            $authorName = ((($aUser['USR_FIRSTNAME'] != '') || ($aUser['USR_LASTNAME'] != '')) ? $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' ' : '') . '<' . $aUser['USR_EMAIL'] . '>';

            $oCase = new Cases();
            $aFields = $oCase->loadCase( $appUid );
            $configNoteNotification['subject'] = G::LoadTranslation( 'ID_MESSAGE_SUBJECT_NOTE_NOTIFICATION' ) . " @#APP_TITLE ";
            //Define the body for the notification
            $body = G::LoadTranslation('ID_CASE_TITLE') . ": @#APP_TITLE<br />";
            $body .= G::LoadTranslation('ID_CASE_NUMBER') . ": @#APP_NUMBER<br />";
            $body .= G::LoadTranslation('ID_AUTHOR') . ": $authorName<br /><br />$noteContent";
            $configNoteNotification['body'] = $body;

            $sFrom = G::buildFrom($aConfiguration, $sFrom);
            $sSubject = G::replaceDataField( $configNoteNotification['subject'], $aFields );
            $sBody = nl2br( G::replaceDataField( $configNoteNotification['body'], $aFields ) );

            $oUser = new Users();
            $recipientsArray = explode( ",", $noteRecipients );

            foreach ($recipientsArray as $recipientUid) {

                $aUser = $oUser->load( $recipientUid );

                $sTo = ((($aUser['USR_FIRSTNAME'] != '') || ($aUser['USR_LASTNAME'] != '')) ? $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' ' : '') . '<' . $aUser['USR_EMAIL'] . '>';
                $oSpool = new SpoolRun();

                $oSpool->setConfig($aConfiguration);
                $oSpool->create(
                    array ('msg_uid' => '',
                           'app_uid' => $appUid,
                           'del_index' => 0,
                           'app_msg_type' => 'DERIVATION',
                           'app_msg_subject' => $sSubject,
                           'app_msg_from' => $sFrom,
                           'app_msg_to' => $sTo,
                           'app_msg_body' => $sBody,
                           'app_msg_cc' => '',
                           'app_msg_bcc' => '',
                           'app_msg_attach' => '',
                           'app_msg_template' => '',
                           'app_msg_status' => 'pending',
                           'app_msg_error' => $msgError
                           )
                    );
                if ($msgError == '') {
                    if (($aConfiguration['MESS_BACKGROUND'] == '') || ($aConfiguration['MESS_TRY_SEND_INMEDIATLY'] == '1')) {
                        $oSpool->sendMail();
                    }
                }

            }
            //Send derivation notification - End
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function addCaseNote($applicationUid, $userUid, $note, $sendMail)
    {
        $response = $this->postNewNote($applicationUid, $userUid, $note, false);

        if ($sendMail == 1) {

            $case = new Cases();

            $p = $case->getUsersParticipatedInCase($applicationUid, 'ACTIVE');
            $noteRecipientsList = array();

            foreach ($p["array"] as $key => $userParticipated) {
                if ($key != '') {
                    $noteRecipientsList[] = $key;
                }
            }

            $noteRecipients = implode(",", $noteRecipientsList);
            $note = stripslashes($note);

            $this->sendNoteNotification($applicationUid, $userUid, $note, $noteRecipients);
        }

        return $response;
    }
}

