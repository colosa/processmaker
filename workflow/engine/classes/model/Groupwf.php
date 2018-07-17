<?php
/**
 * Groupwf.php
 *
 * @package workflow.engine.classes.model
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
 *
 */

require_once 'classes/model/om/BaseGroupwf.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'GROUPWF' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Groupwf extends BaseGroupwf
{

    protected $grp_title_content = '';
    /**
     * Set the [grp_title] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setGrpTitleContent ($v)
    {
        if ($this->getGrpUid() == '') {
            throw (new Exception( "Error in setGrpTitle, the GRP_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if (in_array(GroupwfPeer::GRP_TITLE, $this->modifiedColumns) !== $v || $v
        === '') {
            $this->grp_title_content = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
            $res = Content::addContent( 'GRP_TITLE', '', $this->getGrpUid(), $lang, $this->grp_title_content );
        }

    } // set()

    /**
     * Creates the Group
     *
     * @param array $aData $oData is not necessary
     * @return void
     */
    public function create ($aData)
    {
        //$oData is not necessary
        $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
        try {
            if (isset( $aData['GRP_UID'] )) {
                $this->setGrpUid( $aData['GRP_UID'] );
            } else {
                $this->setGrpUid( G::generateUniqueID() );
            }

            if (isset( $aData['GRP_TITLE'] )) {
                $this->setGrpTitle( $aData['GRP_TITLE'] );
            } else {
                $this->setGrpTitle( 'Default Group Title' );
            }

            if (isset( $aData['GRP_STATUS'] )) {
                $this->setGrpStatus( $aData['GRP_STATUS'] );
            } else {
                $this->setGrpStatus( 'ACTIVE' );
            }

            if (isset( $aData['GRP_LDAP_DN'] )) {
                $this->setGrpLdapDn( $aData['GRP_LDAP_DN'] );
            } else {
                $this->setGrpLdapDn( '' );
            }

            if ($this->validate()) {
                $con->begin();
                if (isset( $aData['GRP_TITLE'] )) {
                    $this->setGrpTitleContent( $aData['GRP_TITLE'] );
                } else {
                    $this->setGrpTitleContent( 'Default Group Title' );
                }
                $res = $this->save();
                $con->commit();
                return $this->getGrpUid();
            } else {
                $msg = '';
                foreach ($this->getValidationFailures() as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                }

                throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));
            }

        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Load the Process row specified in [grp_id] column value.
     *
     * @param string $ProUid the uid of the Prolication
     * @return array $Fields the fields
     */
    public function Load ($ProUid)
    {
        $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
        try {
            $oPro = GroupwfPeer::retrieveByPk( $ProUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Groupwf') {
                $aFields = $oPro->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                return $aFields;
            } else {
                throw (new Exception( "The row '$ProUid' in table Group doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Update the Group row
     *
     * @param array $aData
     * @return variant
     *
     */
    public function update ($aData)
    {
        $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
        try {
            $con->begin();
            $oPro = GroupwfPeer::retrieveByPK( $aData['GRP_UID'] );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Groupwf') {
                $oPro->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oPro->validate()) {
                    if (isset( $aData['GRP_TITLE'] )) {
                        $oPro->setGrpTitleContent( $aData['GRP_TITLE'] );
                    }
                    $res = $oPro->save();
                    $con->commit();
                    return $res;
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }

                    throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));
                }
            } else {
                $con->rollback();
                throw (new Exception( "The row '" . $aData['GRP_UID'] . "' in table Group doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Remove the Prolication document registry
     *
     * @param array $aData or string $ProUid
     * @return string
     *
     */
    public function remove ($ProUid)
    {
        if (is_array( $ProUid )) {
            $ProUid = (isset( $ProUid['GRP_UID'] ) ? $ProUid['GRP_UID'] : '');
        }
        try {
            $oPro = GroupwfPeer::retrieveByPK( $ProUid );
            if (! is_null( $oPro )) {
                Content::removeContent( 'GRP_TITLE', '', $oPro->getGrpUid() );
                return $oPro->delete();
            } else {
                throw (new Exception( "The row '$ProUid' in table Group doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * verify if row specified in [GrpUid] exists.
     *
     * @param string $sProUid the uid of the Prolication
     */

    public function GroupwfExists ($GrpUid)
    {
        $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
        try {
            $oPro = GroupwfPeer::retrieveByPk( $GrpUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Groupwf') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function loadByGroupname ($Groupname)
    {
        $c = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();

        $c->clearSelectColumns();
        $c->addSelectColumn( GroupwfPeer::GRP_TITLE );
        $c->add( GroupwfPeer::GRP_TITLE, $Groupname );
        return $c;
    }

    public function loadByGroupUid ($UidGroup)
    {
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addAsColumn('CON_VALUE', GroupwfPeer::GRP_TITLE);
        $c->add( GroupwfPeer::GRP_UID, $UidGroup );
        $dataset = GroupwfPeer::doSelectRS( $c );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $dataset->next();
        $row = $dataset->getRow();
        return $row;
    }

    public function getAll ($start = null, $limit = null, $search = null)
    {
        $totalCount = 0;
        $criteria = new Criteria( 'workflow' );
        $criteria->addSelectColumn( GroupwfPeer::GRP_UID );
        $criteria->addSelectColumn( GroupwfPeer::GRP_TITLE );
        $criteria->addAsColumn('CON_VALUE', GroupwfPeer::GRP_TITLE);
        $criteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
        $criteria->addSelectColumn( GroupwfPeer::GRP_LDAP_DN );
        $criteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
        $criteria->addAscendingOrderByColumn( GroupwfPeer::GRP_TITLE );

        if ($search) {
            $criteria->add( GroupwfPeer::GRP_TITLE, '%' . $search . '%', Criteria::LIKE );
        }

        $c = clone $criteria;
        $c->clearSelectColumns();
        $c->addSelectColumn( 'COUNT(*)' );
        $dataset = GroupwfPeer::doSelectRS( $c );
        $dataset->next();
        $rowCount = $dataset->getRow();

        if (is_array( $rowCount )) {
            $totalCount = $rowCount[0];
        }

        if ($start) {
            $criteria->setOffset( $start );
        }
        if ($limit) {
            $criteria->setLimit( $limit );
        }

        $rs = GroupwfPeer::doSelectRS( $criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rows = Array ();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }

        $result = new stdClass();
        $result->data = $rows;
        $result->totalCount = $totalCount;

        return $result;
    }

    public function getAllGroup($start = null, $limit = null, $search = null, $sortField = null, $sortDir = null, $countUsers = false)
    {
        require_once PATH_RBAC . "model/RbacUsers.php";
        require_once 'classes/model/TaskUser.php';
        require_once 'classes/model/GroupUser.php';
        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn(GroupwfPeer::GRP_UID);
        $criteria->addSelectColumn(GroupwfPeer::GRP_TITLE);
        $criteria->addSelectColumn(GroupwfPeer::GRP_STATUS);
        $criteria->addSelectColumn(GroupwfPeer::GRP_UX);
        if (is_null($sortField) || trim($sortField) == "") {
            $sortField = GroupwfPeer::GRP_TITLE;
        }

        if ($search) {
            $criteria->add(GroupwfPeer::GRP_TITLE, '%' . $search . '%', Criteria::LIKE);
        }
        $totalRows = GroupwfPeer::doCount($criteria);

        if ($countUsers) {
            //This query must be changed in the next version from Propel
            $criteria->addAsColumn("GRP_USERS",
            "(SELECT 
                COUNT(" . UsersPeer::USR_UID . ")
            FROM
                " . GroupUserPeer::TABLE_NAME . "
            LEFT JOIN
              " . UsersPeer::TABLE_NAME . "
            ON (" . GroupUserPeer::USR_UID . " = " . UsersPeer::USR_UID . ")
            WHERE
              " . GroupUserPeer::GRP_UID . " = " . GroupwfPeer::GRP_UID . " AND
              " . UsersPeer::USR_STATUS . " <> 'CLOSED')");
        }

        if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
            $criteria->addDescendingOrderByColumn($sortField);
        } else {
            $criteria->addAscendingOrderByColumn($sortField);
        }

        if ($start != '') {
            $criteria->setOffset($start);
        }

        if ($limit != '') {
            $criteria->setLimit($limit);
        }

        $oDataset = GroupwfPeer::doSelectRS($criteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $processes = array();
        $uids = array();
        $groups = array();
        $aGroups = array();
        while ($oDataset->next()) {
            $groups[] = $oDataset->getRow();
        }

        return array('rows' => $groups, 'totalCount' => $totalRows);
    }

    public function filterGroup ($filter, $start, $limit)
    {
        $co = new Configurations();
        $config = $co->getConfiguration( 'groupList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $co->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );
        if ($filter != '') {
            $oCriteria->add( GroupwfPeer::GRP_TITLE, '%' . $filter . '%', Criteria::LIKE );
        }
        $totalRows = GroupwfPeer::doCount( $oCriteria );

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );
        $oCriteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
        $oCriteria->addAsColumn( 'GRP_TASKS', 0 );
        $oCriteria->addAsColumn( 'GRP_USERS', 0 );
        if ($filter != '') {
            $oCriteria->add( GroupwfPeer::GRP_TITLE, '%' . $filter . '%', Criteria::LIKE );
        }
        $oCriteria->setOffset( $start );
        $oCriteria->setLimit( $limit );

        $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

    }
}

