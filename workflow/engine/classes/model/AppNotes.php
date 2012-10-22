<?php

require_once 'classes/model/om/BaseAppNotes.php';

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
    public function getNotesList ($appUid, $usrUid = '', $start = '', $limit = '')
    {
        require_once ("classes/model/Users.php");

        G::LoadClass( 'ArrayPeer' );

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

        $Criteria->add( appNotesPeer::APP_UID, $appUid, CRITERIA::EQUAL );

        if ($usrUid != '') {
            $Criteria->add( appNotesPeer::USR_UID, $usrUid, CRITERIA::EQUAL );
        }

        $Criteria->addDescendingOrderByColumn( AppNotesPeer::NOTE_DATE );

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
            $aRow['NOTE_CONTENT'] = stripslashes( $aRow['NOTE_CONTENT'] );
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
            $response['success'] = 'failure';
            $response['message'] = $msg;
        } else {
            $response['success'] = 'success';
            $response['message'] = 'Saved...';
        }

        if ($notify) {
            if ($noteRecipients == "") {
                $noteRecipientsA = array ();
                G::LoadClass( 'case' );
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
            require_once ('classes/model/Configuration.php');
            $oConfiguration = new Configuration();
            $sDelimiter = DBAdapter::getStringDelimiter();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ConfigurationPeer::CFG_UID, 'Emails' );
            $oCriteria->add( ConfigurationPeer::OBJ_UID, '' );
            $oCriteria->add( ConfigurationPeer::PRO_UID, '' );
            $oCriteria->add( ConfigurationPeer::USR_UID, '' );
            $oCriteria->add( ConfigurationPeer::APP_UID, '' );
            if (ConfigurationPeer::doCount( $oCriteria ) == 0) {
                $oConfiguration->create( array ('CFG_UID' => 'Emails','OBJ_UID' => '','CFG_VALUE' => '','PRO_UID' => '','USR_UID' => '','APP_UID' => '') );
                $aConfiguration = array ();
            } else {
                $aConfiguration = $oConfiguration->load( 'Emails', '', '', '', '' );
                if ($aConfiguration['CFG_VALUE'] != '') {
                    $aConfiguration = unserialize( $aConfiguration['CFG_VALUE'] );
                    $passwd = $aConfiguration['MESS_PASSWORD'];
                    $passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
                    $auxPass = explode( 'hash:', $passwdDec );
                    if (count( $auxPass ) > 1) {
                        if (count( $auxPass ) == 2) {
                            $passwd = $auxPass[1];
                        } else {
                            array_shift( $auxPass );
                            $passwd = implode( '', $auxPass );
                        }
                    }
                    $aConfiguration['MESS_PASSWORD'] = $passwd;
                } else {
                    $aConfiguration = array ();
                }
            }

            if (! isset( $aConfiguration['MESS_ENABLED'] ) || $aConfiguration['MESS_ENABLED'] != '1') {
                return false;
            }

            $oUser = new Users();
            $aUser = $oUser->load( $usrUid );
            $authorName = ((($aUser['USR_FIRSTNAME'] != '') || ($aUser['USR_LASTNAME'] != '')) ? $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' ' : '') . '<' . $aUser['USR_EMAIL'] . '>';

            G::LoadClass( 'case' );
            $oCase = new Cases();
            $aFields = $oCase->loadCase( $appUid );
            $configNoteNotification['subject'] = G::LoadTranslation( 'ID_MESSAGE_SUBJECT_NOTE_NOTIFICATION' ) . " @#APP_TITLE ";
            $configNoteNotification['body'] = G::LoadTranslation( 'ID_CASE' ) . ": @#APP_TITLE<br />" . G::LoadTranslation( 'ID_AUTHOR' ) . ": $authorName<br /><br />$noteContent";

            if ($sFrom == '') {
                $sFrom = '"ProcessMaker"';
            }

            $hasEmailFrom = preg_match( '/(.+)@(.+)\.(.+)/', $sFrom, $match );

            if (! $hasEmailFrom || strpos( $sFrom, $aConfiguration['MESS_ACCOUNT'] ) === false) {
                if (($aConfiguration['MESS_ENGINE'] != 'MAIL') && ($aConfiguration['MESS_ACCOUNT'] != '')) {
                    $sFrom .= ' <' . $aConfiguration['MESS_ACCOUNT'] . '>';
                } else {
                    if (($aConfiguration['MESS_ENGINE'] == 'MAIL')) {
                        $sFrom .= ' <info@' . gethostbyaddr( '127.0.0.1' ) . '>';
                    } else {
                        if ($aConfiguration['MESS_SERVER'] != '') {
                            if (($sAux = @gethostbyaddr( $aConfiguration['MESS_SERVER'] ))) {
                                $sFrom .= ' <info@' . $sAux . '>';
                            } else {
                                $sFrom .= ' <info@' . $aConfiguration['MESS_SERVER'] . '>';
                            }
                        } else {
                            $sFrom .= ' <info@processmaker.com>';
                        }
                    }
                }
            }

            $sSubject = G::replaceDataField( $configNoteNotification['subject'], $aFields );

            $sBody = nl2br( G::replaceDataField( $configNoteNotification['body'], $aFields ) );

            G::LoadClass( 'spool' );
            $oUser = new Users();
            $recipientsArray = explode( ",", $noteRecipients );

            foreach ($recipientsArray as $recipientUid) {

                $aUser = $oUser->load( $recipientUid );

                $sTo = ((($aUser['USR_FIRSTNAME'] != '') || ($aUser['USR_LASTNAME'] != '')) ? $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' ' : '') . '<' . $aUser['USR_EMAIL'] . '>';
                $oSpool = new spoolRun();
                $oSpool->setConfig( array ('MESS_ENGINE' => $aConfiguration['MESS_ENGINE'],'MESS_SERVER' => $aConfiguration['MESS_SERVER'],'MESS_PORT' => $aConfiguration['MESS_PORT'],'MESS_ACCOUNT' => $aConfiguration['MESS_ACCOUNT'],'MESS_PASSWORD' => $aConfiguration['MESS_PASSWORD'],'SMTPAuth' => $aConfiguration['MESS_RAUTH'] == '1' ? true : false,'SMTPSecure' => isset( $aConfiguration['SMTPSecure'] ) ? $aConfiguration['SMTPSecure'] : '') );
                $oSpool->create( array ('msg_uid' => '','app_uid' => $appUid,'del_index' => 1,'app_msg_type' => 'DERIVATION','app_msg_subject' => $sSubject,'app_msg_from' => $sFrom,'app_msg_to' => $sTo,'app_msg_body' => $sBody,'app_msg_cc' => '','app_msg_bcc' => '','app_msg_attach' => '','app_msg_template' => '','app_msg_status' => 'pending') );
                if (($aConfiguration['MESS_BACKGROUND'] == '') || ($aConfiguration['MESS_TRY_SEND_INMEDIATLY'] == '1')) {
                    $oSpool->sendMail();
                }

            }
            //Send derivation notification - End
        } catch (Exception $oException) {
            throw $oException;
        }
    }
}

