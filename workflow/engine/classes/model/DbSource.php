<?php

/**
 * DbSource.php
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
//require_once 'classes/model/Content.php';
//require_once 'classes/model/om/BaseDbSource.php';

/**
 * Skeleton subclass for representing a row from the 'DB_SOURCE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class DbSource extends BaseDbSource
{

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $db_source_description = '';

    /**
     * Get the rep_tab_title column value.
     * @return     string
     */
    public function getDBSourceDescription()
    {
        if ($this->getDbsUid() == "") {
            throw ( new Exception("Error in getDBSourceDescription, the getDbsUid() can't be blank") );
        }
        $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
        $this->db_source_description = Content::load('DBS_DESCRIPTION', '', $this->getDbsUid(), $lang);
        return $this->db_source_description;
    }

    public function getCriteriaDBSList($sProcessUID)
    {
        $sDelimiter = DBAdapter::getStringDelimiter();
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_UID);
        $oCriteria->addSelectColumn(DbSourcePeer::PRO_UID);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_TYPE);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_SERVER);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_DATABASE_NAME);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_USERNAME);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_PASSWORD);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_PORT);
        $oCriteria->addAsColumn('DBS_DESCRIPTION', 'C.CON_VALUE');
        $oCriteria->addAlias('C', 'CONTENT');
        $aConditions = array();
        $aConditions[] = array(DbSourcePeer::DBS_UID, 'C.CON_ID');
        $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'DBS_DESCRIPTION' . $sDelimiter);
        $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(DbSourcePeer::PRO_UID, $sProcessUID);
        return $oCriteria;
    }

    public function load($Uid, $ProUID = '')
    {
        try {
            $oRow = DbSourcePeer::retrieveByPK($Uid, $ProUID);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $aFields['DBS_DESCRIPTION'] = $this->getDBSourceDescription();
                $this->setNew(false);
                return $aFields;
            } else {
                throw(new Exception("The row '$Uid'/'$ProUID' in table DbSource doesn't exist!"));
            }
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    public function getValProUid($Uid)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(DbSourcePeer::PRO_UID);
        $oCriteria->add(DbSourcePeer::DBS_UID, $Uid);
        $result = DbSourcePeer::doSelectRS($oCriteria);
        $result->next();
        $aRow = $result->getRow();
        return $aRow[0];
    }

    public function Exists($Uid, $ProUID)
    {
        try {
            $oPro = DbSourcePeer::retrieveByPk($Uid, $ProUID);
            if (is_object($oPro) && get_class($oPro) == 'DbSource') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function update($fields)
    {
        if ($fields['DBS_ENCODE'] == '0') {
            unset($fields['DBS_ENCODE']);
        }
        $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['DBS_UID'], $fields['PRO_UID']);
            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove($DbsUid, $ProUID)
    {
        $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setDbsUid($DbsUid);
            $this->setProUid($ProUID);
            // note added by gustavo cruz gustavo-at-colosa-dot-com
            // we assure that the _delete attribute must be set to false
            // if a record exists in the database with that uid.
            if ($this->Exists($DbsUid, $ProUID)) {
                $this->setDeleted(false);
            }
            $result = $this->delete();
            $con->commit();
            return $result;
        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function create($aData)
    {
        if ($aData['DBS_ENCODE'] == '0') {
            unset($aData['DBS_ENCODE']);
        }
        $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        try {
            if (isset($aData['DBS_UID']) && $aData['DBS_UID'] == '') {
                unset($aData['DBS_UID']);
            }
            if (!isset($aData['DBS_UID'])) {
                $aData['DBS_UID'] = G::generateUniqueID();
            }
            $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $this->getDbsUid();
        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
}

