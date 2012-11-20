<?php
/**
 * AppThread.php
 * @package    workflow.engine.classes.model
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

//require_once 'classes/model/om/BaseAppThread.php';


/**
 * Skeleton subclass for representing a row from the 'APP_THREAD' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class AppThread extends BaseAppThread
{
    public function createAppThread ($sAppUid, $iDelIndex, $iParent)
    {
        if (!isset($sAppUid) || strlen($sAppUid ) == 0 ) {
            throw ( new Exception ( 'Column "APP_UID" cannot be null.' ) );
        }

        if (!isset($iDelIndex) || strlen($iDelIndex ) == 0 ) {
            throw ( new Exception ( 'Column "DEL_INDEX" cannot be null.' ) );
        }

        if (!isset($iParent) || strlen($iDelIndex ) == 0 ) {
            throw ( new Exception ( 'Column "APP_THREAD_INDEX" cannot be null.' ) );
        }

        $c = new Criteria ();
        $c->clearSelectColumns();
        $c->addSelectColumn ( 'MAX(' . AppThreadPeer::APP_THREAD_INDEX . ') ' );
        $c->add ( AppThreadPeer::APP_UID, $sAppUid );
        $rs = AppThreadPeer::doSelectRS ( $c );
        $rs->next();
        $row = $rs->getRow();
        $iAppThreadIndex = $row[0] + 1;

        $this->setAppUid          ( $sAppUid );
        $this->setAppThreadIndex  ( $iAppThreadIndex );
        $this->setAppThreadParent ( $iParent );
        $this->setAppThreadStatus ( 'OPEN' );
        $this->setDelIndex        ( $iDelIndex );

        if ($this->validate() ) {
            try {
                $res = $this->save();
            } catch ( PropelException $e ) {
                throw ( $e );
            }
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $this->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage();
            }
            throw ( new Exception ( 'Failed Data validation. ' . $msg ) );
        }
        return $iAppThreadIndex;
    }

    public function update($aData)
    {
        $con = Propel::getConnection( AppThreadPeer::DATABASE_NAME );
        try {
            $con->begin();
            $oApp = AppThreadPeer::retrieveByPK( $aData['APP_UID'], $aData['APP_THREAD_INDEX'] );
            if (is_object($oApp) && get_class ($oApp) == 'AppThread' ) {
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
                    throw ( new PropelException ( 'The AppThread row cannot be created!', new PropelException ( $msg ) ) );
                }
            } else {
                $con->rollback();
                throw(new Exception( "This AppThread row doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }
}

