<?php
/**
 * IsoCountry.php
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

//require_once 'classes/model/om/BaseIsoCountry.php';


/**
 * Skeleton subclass for representing a row from the 'ISO_COUNTRY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class IsoCountry extends BaseIsoCountry
{
    public function findById($UID)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoCountryPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoCountryPeer::IC_NAME);
        $oCriteria->add(IsoCountryPeer::IC_UID, $UID);
        $oDataset = IsoCountryPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }

    public function findByIcName($IC_NAME)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(IsoCountryPeer::IC_UID);
        $oCriteria->addSelectColumn(IsoCountryPeer::IC_NAME);
        $oCriteria->add(IsoCountryPeer::IC_NAME, $IC_NAME);
        $oDataset = IsoCountryPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        return $oDataset->getRow();
    }
}

