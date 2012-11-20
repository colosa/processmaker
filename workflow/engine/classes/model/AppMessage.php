<?php
/**
 * AppMessage.php
 * * @package workflow.engine.classes.model
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
 */

//require_once 'classes/model/om/BaseAppMessage.php';

/**
 * Skeleton subclass for representing a row from the 'APP_MESSAGE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class AppMessage extends BaseAppMessage
{

    private $data_spool;
    private $status_spool;
    private $error_spool;

    public function getSpoolStatus ()
    {
        return $this->status_spool;
    }

    public function getSpoolError ()
    {
        return $this->error_spool;
    }

    /**
     * AppMessgae quick Save method
     *
     * @param Array(msg_uid, app_uid, del_index, app_msg_type, app_msg_subject, app_msg_from, app_msg_to,
     * app_msg_body, app_msg_cc, app_msg_bcc, app_msg_attach, app_msg_template, app_msg_status )
     *
     * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmai.com>
     * Date Aug 31th, 2009
     */
    public function quickSave2 ($data_spool)
    {
        $this->data_spool = $data_spool;

        $sUID = G::generateUniqueID();
        $spool = new AppMessage();

        $spool->setAppMsgUid( $sUID );
        $spool->setMsgUid( $data_spool['msg_uid'] );
        $spool->setAppUid( $data_spool['app_uid'] );
        $spool->setDelIndex( $data_spool['del_index'] );
        $spool->setAppMsgType( $data_spool['app_msg_type'] );
        $spool->setAppMsgSubject( $data_spool['app_msg_subject'] );
        $spool->setAppMsgFrom( $data_spool['app_msg_from'] );
        $spool->setAppMsgTo( $data_spool['app_msg_to'] );
        $spool->setAppMsgBody( $data_spool['app_msg_body'] );
        $spool->setAppMsgDate( date( 'Y-m-d H:i:s' ) );
        $spool->setAppMsgCc( $data_spool['app_msg_cc'] );
        $spool->setAppMsgBcc( $data_spool['app_msg_bcc'] );
        $spool->setappMsgAttach( $data_spool['app_msg_attach'] );
        $spool->setAppMsgTemplate( $data_spool['app_msg_template'] );
        $spool->setAppMsgStatus( $data_spool['app_msg_status'] );

        if (! $spool->validate()) {
            $this->error_spool = $spool->getValidationFailures();
            $this->status_spool = 'error';

            $error_msg = "AppMessage::quickSave(): Validation error: \n";
            foreach ($errors as $key => $value) {
                $error_msg .= $value->getMessage( $key ) . "\n";
            }
            throw new Exception( $error_msg );
        } else {
            //echo "Saving - validation ok\n";
            $this->error_spool = '';
            $this->status = 'success';
            $spool->save();
        }
        return $sUID;
    }

    public function quickSave ($aData)
    {
        if (isset( $aData['app_msg_uid'] )) {
            $o = EmployeePeer::retrieveByPk( $aData['app_msg_uid'] );
        }
        if (isset( $o ) && is_object( $o ) && get_class( $o ) == 'AppMessage') {
            $o->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $o->setAppMsgDate( date( 'Y-m-d H:i:s' ) );
            $o->save();
            return $o->getAppMsgUid();
        } else {
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $this->setAppMsgDate( date( 'Y-m-d H:i:s' ) );
            $this->save();
            return $this->getAppMsgUid();
        }
    }
}

