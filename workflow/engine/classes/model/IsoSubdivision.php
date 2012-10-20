<?php
/**
 * IsoSubdivision.php
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

require_once 'classes/model/om/BaseIsoSubdivision.php';


/**
 * Skeleton subclass for representing a row from the 'ISO_SUBDIVISION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class IsoSubdivision extends BaseIsoSubdivision
{
    public function findById($IC_UID, $IS_UID)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IS_UID);
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IS_NAME);
        $oCriteria->add(IsoSubdivisionPeer::IC_UID, $IC_UID);
        $oCriteria->add(IsoSubdivisionPeer::IS_UID, $IS_UID);
        $oDataset = IsoSubdivisionPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }

    public function findByIcName($IS_NAME)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IS_UID);
        $oCriteria->addSelectColumn(IsoSubdivisionPeer::IS_NAME);
        $oCriteria->add(IsoSubdivisionPeer::IS_NAME, $IS_UID);
        $oDataset = IsoSubdivisionPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }
}

