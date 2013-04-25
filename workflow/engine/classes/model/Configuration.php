<?php
/**
 * Configuration.php
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

if (!class_exists('BaseConfiguration')) {
    require_once 'classes/model/om/BaseConfiguration.php';
}
//require_once 'classes/model/Content.php';


/**
 * Skeleton subclass for representing a row from the 'CONFIGURATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Configuration extends BaseConfiguration
{
    public function create($aData)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setCfgUid($aData['CFG_UID']);
            $this->setObjUid($aData['OBJ_UID']);
            $this->setCfgValue(isset($aData['CFG_VALUE'])?$aData['CFG_VALUE']:'');
            $this->setProUid($aData['PRO_UID']);
            $this->setUsrUid($aData['USR_UID']);
            $this->setAppUid($aData['APP_UID']);
            if ($this->validate()) {
                $result=$this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw(new Exception("Failed Validation in class ".get_class($this)."."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function load($CfgUid, $ObjUid = '', $ProUid = '', $UsrUid = '', $AppUid = '')
    {
        try {
            $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw(new Exception( "The row '$CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid' in table Configuration doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function update($fields)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['CFG_UID'], $fields['OBJ_UID'], $fields['PRO_UID'], $fields['USR_UID'], $fields['APP_UID']);
            $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $contentResult=0;
                $result=$this->save();
                $result=($result==0)?($contentResult>0?1:0):$result;
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw(new Exception("Failed Validation in class ".get_class($this)."."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function remove($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setCfgUid($CfgUid);
            $this->setObjUid($ObjUid);
            $this->setProUid($ProUid);
            $this->setUsrUid($UsrUid);
            $this->setAppUid($AppUid);
            $result=$this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function exists($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
    {
        $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
        return (( get_class ($oRow) == 'Configuration' )&&(!is_null($oRow)));
    }

    public function getAll ()
    {
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( ConfigurationPeer::CFG_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::OBJ_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::CFG_VALUE );
        $oCriteria->addSelectColumn( ConfigurationPeer::PRO_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::USR_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::APP_UID );

        //execute the query
        $oDataset = ConfigurationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRows = array ();
        while ($oDataset->next()) {
            $aRows[] = $oDataset->getRow();
        }
        return $aRows;
    }
}

