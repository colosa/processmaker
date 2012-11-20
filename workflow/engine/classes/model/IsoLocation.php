<?php
/**
 * IsoLocation.php
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

//require_once 'classes/model/om/BaseIsoLocation.php';


/**
 * Skeleton subclass for representing a row from the 'ISO_LOCATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class IsoLocation extends BaseIsoLocation
{
    public function findById($IC_UID, $IS_UID, $IL_UID)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoLocationPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IS_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IL_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IL_NAME);
        $oCriteria->add(IsoLocationPeer::IC_UID, $IC_UID);
        $oCriteria->add(IsoLocationPeer::IS_UID, $IS_UID);
        $oCriteria->add(IsoLocationPeer::IL_UID, $IL_UID);
        $oDataset = IsoLocationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }

    public function findByIcName($IL_NAME)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoLocationPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IS_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IL_UID);
        $oCriteria->addSelectColumn(IsoLocationPeer::IL_NAME);
        $oCriteria->add(IsoLocationPeer::IL_NAME, $IL_NAME);
        $oDataset = IsoLocationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }

    public function getAllRowsLike($word)
    {
        try {
            $c = new Criteria('workflow');
            $c->addSelectColumn(IsoLocationPeer::IC_UID);
            $c->addSelectColumn(IsoLocationPeer::IL_NORMAL_NAME);
            $c->add(IsoLocationPeer::IL_NORMAL_NAME, $word."%", Criteria::LIKE);

            $rs = IsoLocationPeer::doSelectRS($c);
            //$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rows = Array();
            while ($rs->next()) {
                array_push($rows, $rs->getRow());
            }

            return $rows;
        } catch (Exception $oException) {
            throw $oException;
        }
    }
}

