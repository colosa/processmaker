<?php
/**
 * ReportTable.php
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

//require_once 'classes/model/Content.php';
if (!class_exists('BaseReportTable')) {
    require_once 'classes/model/om/BaseReportTable.php';
}

/**
 * Skeleton subclass for representing a row from the 'REPORT_TABLE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class ReportTable extends BaseReportTable
{
    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $rep_tab_title = '';

    /**
     * Get the rep_tab_title column value.
     *
     * @return string
     */
    public function getRepTabTitle ()
    {
        if ($this->getRepTabUid() == "") {
            throw (new Exception( "Error in getRepTabTitle, the getRepTabUid() can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->rep_tab_title = Content::load( 'REP_TAB_TITLE', '', $this->getRepTabUid(), $lang );
        return $this->rep_tab_title;
    }

    /**
     * Set the rep_tab_title column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setRepTabTitle ($v)
    {
        if ($this->getRepTabUid() == "") {
            throw (new Exception( "Error in setRepTabTitle, the setRepTabUid() can't be blank" ));
        }
        $v = isset( $v ) ? ((string) $v) : '';
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        if ($this->rep_tab_title !== $v || $v === "") {
            $this->rep_tab_title = $v;
            $res = Content::addContent( 'REP_TAB_TITLE', '', $this->getRepTabUid(), $lang, $this->rep_tab_title );
            return $res;
        }
        return 0;
    }

    public function load ($RepTabUid)
    {
        try {
            $oRow = ReportTablePeer::retrieveByPK( $RepTabUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                $this->setRepTabTitle( $aFields['REP_TAB_TITLE'] = $this->getRepTabTitle() );
                return $aFields;
            } else {
                //throw( new Exception( "The row '$RepTabUid' in table ReportTable doesn't exist!" ));
                return array ();
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $con = Propel::getConnection( ReportTablePeer::DATABASE_NAME );
        try {
            $con->begin();
            if (isset( $aData['REP_TAB_UID'] ) && $aData['REP_TAB_UID'] == '') {
                unset( $aData['REP_TAB_UID'] );
            }
            if (! isset( $aData['REP_TAB_UID'] )) {
                $this->setRepTabUid( G::generateUniqueID() );
            } else {
                $this->setRepTabUid( $aData['REP_TAB_UID'] );
            }

            $this->setProUid( $aData['PRO_UID'] );
            $this->setRepTabName( $aData['REP_TAB_NAME'] );
            $this->setRepTabType( $aData['REP_TAB_TYPE'] );
            if (! isset( $aData['REP_TAB_GRID'] )) {
                $this->setRepTabGrid( "" );
            } else {
                $this->setRepTabGrid( $aData['REP_TAB_GRID'] );
            }
            if (! isset( $aData['REP_TAB_CONNECTION'] )) {
                $this->setRepTabConnection( "report" );
            } else {
                $this->setRepTabConnection( $aData['REP_TAB_CONNECTION'] );
            }
            $this->setRepTabCreateDate( date( 'Y-m-d H:i:s' ) );
            $this->setRepTabStatus( 'ACTIVE' );

            if ($this->validate()) {
                if (! isset( $aData['REP_TAB_TITLE'] )) {
                    $this->setRepTabTitle( "" );
                } else {
                    $this->setRepTabTitle( $aData['REP_TAB_TITLE'] );
                }
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function update ($fields)
    {
        $con = Propel::getConnection( ReportTablePeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['REP_TAB_UID'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );

            $sDataBase = 'database_' . strtolower( DB_ADAPTER );
            if (G::LoadSystemExist( $sDataBase )) {
                G::LoadSystem( $sDataBase );
                $oDataBase = new database();
                $oValidate = $oDataBase->getValidate( $this->validate() );
            } else {
                $oValidate = $this->validate();
            }
            // if($this->validate())
            if ($oValidate) {
                $contentResult = 0;
                if (array_key_exists( "REP_TAB_TITLE", $fields )) {
                    $contentResult += $this->setRepTabTitle( $fields["REP_TAB_TITLE"] );
                }
                $result = $this->save();
                $result = ($result == 0) ? ($contentResult > 0 ? 1 : 0) : $result;
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                $validationE = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $validationE->aValidationFailures = $this->getValidationFailures();
                throw ($validationE);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove ($RepTabUid)
    {
        $con = Propel::getConnection( ReportTablePeer::DATABASE_NAME );
        try {
            $con->begin();
            $oRepTab = ReportTablePeer::retrieveByPK( $RepTabUid );
            if (! is_null( $oRepTab )) {
                Content::removeContent( 'REP_TAB_TITLE', '', $this->getRepTabUid() );
                $result = $oRepTab->delete();
                $con->commit();
            }
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function reportTableExists ($RepTabUid)
    {
        $con = Propel::getConnection( ReportTablePeer::DATABASE_NAME );
        try {
            $oRepTabUid = ReportTablePeer::retrieveByPk( $RepTabUid );
            if (is_object( $oRepTabUid ) && get_class( $oRepTabUid ) == 'ReportTable') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}

