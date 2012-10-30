<?php
/**
 * Triggers.php
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

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseTriggers.php';


/**
 * Skeleton subclass for representing a row from the 'TRIGGER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Triggers extends BaseTriggers
{
    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tri_title = '';

    /**
    * Get the tri_title column value.
    * @return     string
    */
    public function getTriTitle()
    {
        if ($this->getTriUid() == "") {
            throw ( new Exception( "Error in getTriTitle, the getTriUid() can't be blank") );
        }
        $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
        $this->tri_title = Content::load ( 'TRI_TITLE', '', $this->getTriUid(), $lang );
        return $this->tri_title;
    }

    /**
     * Set the tri_title column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTriTitle($v)
    {
        if ($this->getTriUid() == "") {
            throw ( new Exception( "Error in setTriTitle, the getTriUid() can't be blank") );
        }
        $v=isset($v)?((string)$v):'';
        $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
        if ($this->tri_title !== $v || $v==="") {
            $this->tri_title = $v;
            $res = Content::addContent( 'TRI_TITLE', '', $this->getTriUid(), $lang, $this->tri_title );
            return $res;
        }
        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tri_description = '';
    /**
     * Get the tri_description column value.
     * @return     string
     */

    public function getTriDescription()
    {
        if ($this->getTriUid() == "") {
            throw ( new Exception( "Error in getTriDescription, the getTriUid() can't be blank") );
        }
        $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
        $this->tri_description = Content::load ( 'TRI_DESCRIPTION', '', $this->getTriUid(), $lang );
        return $this->tri_description;
    }

    /**
     * Set the tri_description column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTriDescription($v)
    {
        if ($this->getTriUid() == "") {
            throw ( new Exception( "Error in setTriDescription, the getTriUid() can't be blank") );
        }
        $v=isset($v)?((string)$v):'';
        $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
        if ($this->tri_description !== $v || $v==="") {
            $this->tri_description = $v;
            $res = Content::addContent( 'TRI_DESCRIPTION', '', $this->getTriUid(), $lang, $this->tri_description );
            return $res;
        }
        return 0;
    }

    public function load($TriUid)
    {
        try {
            $oRow = TriggersPeer::retrieveByPK( $TriUid );
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                $this->setTriTitle($aFields['TRI_TITLE']=$this->getTriTitle());
                $this->setTriDescription($aFields['TRI_DESCRIPTION']=$this->getTriDescription());
                return $aFields;
            } else {
                throw( new Exception( "The row '$TriUid' in table TRIGGERS doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function create($aData)
    {
        $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
        try {
            $con->begin();
            if (isset ( $aData['TRI_UID'] ) && $aData['TRI_UID']== '') {
                unset ( $aData['TRI_UID'] );
            }
            if ( !isset ( $aData['TRI_UID'] ) ) {
                $this->setTriUid(G::generateUniqueID());
            } else {
                $this->setTriUid($aData['TRI_UID'] );
            }

            $this->setProUid($aData['PRO_UID']);
            $this->setTriType("SCRIPT");

            if (!isset ( $aData['TRI_WEBBOT'] )) {
                $this->setTriWebbot("");
            } else {
                $this->setTriWebbot( $aData['TRI_WEBBOT'] );
            }

            if ($this->validate()) {
                if (!isset ( $aData['TRI_TITLE'] )) {
                    $this->setTriTitle("");
                } else {
                    $this->setTriTitle( $aData['TRI_TITLE'] );
                }
                if (!isset ( $aData['TRI_DESCRIPTION'] )) {
                    $this->setTriDescription("");
                } else {
                    $this->setTriDescription( $aData['TRI_DESCRIPTION'] );
                }
                if (!isset ( $aData['TRI_PARAM'] )) {
                    $this->setTriParam("");
                } else {
                    $this->setTriParam( $aData['TRI_PARAM'] );
                }
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

    public function update($fields)
    {
        $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['TRI_UID']);
            $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $contentResult=0;
                if (array_key_exists("TRI_TITLE", $fields)) {
                    $contentResult+=$this->setTriTitle($fields["TRI_TITLE"]);
                }
                if (array_key_exists("TRI_DESCRIPTION", $fields)) {
                    $contentResult+=$this->setTriDescription($fields["TRI_DESCRIPTION"]);
                }
                $result=$this->save();
                $result=($result==0)?($contentResult>0?1:0):$result;
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                $validationE=new Exception("Failed Validation in class ".get_class($this).".");
                $validationE->aValidationFailures = $this->getValidationFailures();
                throw($validationE);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function remove($TriUid)
    {
        $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
        try {
            $result = false;
            $con->begin();
            $oTri = TriggersPeer::retrieveByPK( $TriUid );
            if (!is_null($oTri)) {
                Content::removeContent( 'TRI_TITLE', '', $this->getTriUid());
                Content::removeContent( 'TRI_DESCRIPTION', '', $this->getTriUid());
                $result = $oTri->delete();
                $con->commit();
            }
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    /**
     * verify if Trigger row specified in [sUid] exists.
     *
     * @param      string $sUid   the uid of the Prolication
     */
    public function TriggerExists ($sUid)
    {
        $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
        try {
            $oObj = TriggersPeer::retrieveByPk( $sUid );
            if (is_object($oObj) && get_class ($oObj) == 'Triggers' ) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function verifyDependecies($TRI_UID)
    {
        require_once "classes/model/Event.php";
        require_once "classes/model/StepTrigger.php";

        $oResult = new stdClass();
        $oResult->dependencies = Array();

        $oCriteria = new Criteria();
        $oCriteria->addSelectColumn(EventPeer::EVN_UID);
        $oCriteria->addSelectColumn(EventPeer::TRI_UID);
        $oCriteria->add(EventPeer::EVN_ACTION, '', Criteria::NOT_EQUAL);
        $oCriteria->add(EventPeer::TRI_UID, $TRI_UID);

        $oDataset = EventPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aRows = Array();
        while ($oDataset->next()) {
            array_push($aRows, $oDataset->getRow());
        }

        $oResult->dependencies['Events'] = Array();
        if (count($aRows) == 0) {
            $oResult->code = 0;
        } else {
            $oResult->code = 1;
            foreach ($aRows as $row) {
                $oTrigger = TriggersPeer::retrieveByPK($row['TRI_UID']);
                array_push($oResult->dependencies['Events'], Array('UID'=>($oTrigger->getTriUid()), 'DESCRIPTION'=>($oTrigger->getTriTitle())));
            }
        }

        //for tasks dependencies
        $oCriteria = new Criteria();
        $oCriteria->addSelectColumn(StepTriggerPeer::TAS_UID);
        $oCriteria->addSelectColumn(StepTriggerPeer::TRI_UID);
        $oCriteria->add(StepTriggerPeer::TRI_UID, $TRI_UID);

        $oDataset = StepTriggerPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aRows = Array();
        while ( $oDataset->next() ) {
            array_push($aRows, $oDataset->getRow());
        }

        $oResult->dependencies['Tasks'] = Array();
        if ($oResult->code == 0 && count($aRows) == 0) {
            $oResult->code = 0;
        } elseif (count($aRows) > 0) {
            $oResult->code = 1;
            foreach ($aRows as $row) {
                $oTask = TaskPeer::retrieveByPK($row['TAS_UID']);
                array_push($oResult->dependencies['Tasks'], Array('UID'=>($oTask->getTasUid()), 'DESCRIPTION'=>($oTask->getTasTitle())));
            }
        }

        //Tasks, assignment rules dependencies
        $criteria = new Criteria();

        $criteria->addSelectColumn(TaskPeer::TAS_UID);
        $criteria->add(TaskPeer::TAS_SELFSERVICE_TIMEOUT, 1);
        $criteria->add(TaskPeer::TAS_SELFSERVICE_TRIGGER_UID, $TRI_UID);

        $rsCriteria = TaskPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $arrayRow = array();

        while ($rsCriteria->next()) {
            array_push($arrayRow, $rsCriteria->getRow());
        }

        $oResult->dependencies["Assignment rules"] = array();

        if ($oResult->code == 0 && count($arrayRow) == 0) {
            $oResult->code = 0;
        } else {
            if (count($arrayRow) > 0) {
                foreach ($arrayRow as $row) {
                    $task = TaskPeer::retrieveByPK($row["TAS_UID"]);
                    array_push($oResult->dependencies["Assignment rules"], array("UID" => $task->getTasUid(), "DESCRIPTION" => $task->getTasTitle()));
                }

                $oResult->code = 1;
            }
        }

        return $oResult;
    }
}

