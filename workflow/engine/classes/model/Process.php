<?php
/**
 * Process.php
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

//require_once 'classes/model/om/BaseProcess.php';
//require_once 'classes/model/Content.php';
//require_once 'classes/model/ProcessCategory.php';

/**
 * Skeleton subclass for representing a row from the 'PROCESS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Process extends BaseProcess
{
    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $pro_title = '';

    /**
     * Get the [Pro_title] column value.
     *
     * @return string
     */
    public function getProTitle ()
    {
        if ($this->getProUid() == '') {
            throw (new Exception( "Error in getProTitle, the PRO_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->pro_title = Content::load( 'PRO_TITLE', '', $this->getProUid(), $lang );
        return $this->pro_title;
    }

    /**
     * Set the [Pro_title] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setProTitle ($v)
    {
        if ($this->getProUid() == '') {
            throw (new Exception( "Error in setProTitle, the PRO_UID can't be blank" . print_r( debug_backtrace(), 1 ) ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if ($this->pro_title !== $v || $v === '') {
            $this->pro_title = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

            $res = Content::addContent( 'PRO_TITLE', '', $this->getProUid(), $lang, $this->pro_title );
        }

    } // set()


    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $pro_description = '';

    /**
     * Get the [Pro_description] column value.
     *
     * @return string
     */
    public function getProDescription ()
    {
        if ($this->getProUid() == '') {
            throw (new Exception( "Error in getProDescription, the PRO_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->pro_description = Content::load( 'PRO_DESCRIPTION', '', $this->getProUid(), $lang );
        return $this->pro_description;
    }

    /**
     * Set the [Pro_description] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setProDescription ($v)
    {
        if ($this->getProUid() == '') {
            throw (new Exception( "Error in setProDescription, the PRO_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if ($this->pro_description !== $v || $v === '') {
            $this->pro_description = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

            $res = Content::addContent( 'PRO_DESCRIPTION', '', $this->getProUid(), $lang, $this->pro_description );
        }

    } // set()


    /**
     * Creates the Process
     *
     * @param array $aData Fields with :
     * $aData['PRO_UID'] the process id
     * $aData['USR_UID'] the userid
     * $aData['PRO_CATEGORY'] the id category
     * @return void
     */

    public function create ($aData)
    {
        if (! isset( $aData['USR_UID'] )) {
            throw (new PropelException( 'The process cannot be created. The USR_UID is empty.' ));
        }
        $con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
        try {
            do {
                $sNewProUid = G::generateUniqueID();
            } while ($this->processExists( $sNewProUid ));

            $this->setProUid( $sNewProUid );
            $this->setProParent( $sNewProUid );
            $this->setProTime( 1 );
            $this->setProTimeunit( 'DAYS' );
            $this->setProStatus( 'ACTIVE' );
            $this->setProTypeDay( '' );
            $this->setProType( 'NORMAL' );
            $this->setProAssignment( 'FALSE' );
            $this->setProShowMap( '' );
            $this->setProShowMessage( '' );
            $this->setProShowDelegate( '' );
            $this->setProShowDynaform( '' );
            $this->setProCategory( $aData['PRO_CATEGORY'] );
            $this->setProSubCategory( '' );
            $this->setProIndustry( '' );
            $this->setProCreateDate( 'now' );
            $this->setProCreateUser( $aData['USR_UID'] );
            $this->setProHeight( 5000 );
            $this->setProWidth( 10000 );
            $this->setProTitleX( 0 );
            $this->setProTitleY( 0 );
            $this->setProDynaforms( isset( $aData['PRO_DYNAFORMS'] ) ? (is_array( $aData['PRO_DYNAFORMS'] ) ? serialize( $aData['PRO_DYNAFORMS'] ) : $aData['PRO_DYNAFORMS']) : '' );

            if ($this->validate()) {
                $con->begin();
                $res = $this->save();

                if (isset( $aData['PRO_TITLE'] )) {
                    $this->setProTitle( $aData['PRO_TITLE'] );
                } else {
                    $this->setProTitle( 'Default Process Title' );
                }

                if (isset( $aData['PRO_DESCRIPTION'] )) {
                    $this->setProDescription( $aData['PRO_DESCRIPTION'] );
                } else {
                    $this->setProDescription( 'Default Process Description' );
                }

                $con->commit();

                $this->memcachedDelete();

                return $this->getProUid();
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
     * verify if Process row specified in [pro_id] exists.
     *
     * @param string $sProUid the uid of the Prolication
     */
    public function processExists ($ProUid)
    {
        $con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
        try {
            $oPro = ProcessPeer::retrieveByPk( $ProUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Process') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Load the Process row specified in [pro_id] column value.
     *
     * @param string $ProUid the uid of the Prolication
     * @return array $Fields the fields
     */
    public function load ($ProUid, $getAllLang = false)
    {
        $con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
        try {
            $oPro = ProcessPeer::retrieveByPk( $ProUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Process') {
                $aFields = $oPro->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                //optimized to avoid double and multiple execution of the same query
                //        $aFields['PRO_TITLE']       = $oPro->getProTitle();
                //        $aFields['PRO_DESCRIPTION'] = $oPro->getProDescription();
                //        $this->pro_title = $aFields['PRO_TITLE'];
                //        $this->pro_description = $aFields['PRO_DESCRIPTION'];

                $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn( ContentPeer::CON_CATEGORY );
                $c->addSelectColumn( ContentPeer::CON_VALUE );
                $c->add( ContentPeer::CON_ID, $ProUid );
                if (! $getAllLang) {
                    $c->add( ContentPeer::CON_LANG, $lang );
                }
                $rs = ProcessPeer::doSelectRS( $c );
                $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $rs->next();
                $row = $rs->getRow();

                while (is_array( $row )) {
                    switch ($row['CON_CATEGORY']) {
                        case 'PRO_TITLE':
                            $aFields['PRO_TITLE'] = $row['CON_VALUE'];
                            $this->pro_title = $row['CON_VALUE'];
                            if ($row['CON_VALUE'] !== '') {
                                $this->setProTitle( $aFields['PRO_TITLE'] );
                            }
                            break;
                        case 'PRO_DESCRIPTION':
                            $aFields['PRO_DESCRIPTION'] = $row['CON_VALUE'];
                            $this->pro_description = $row['CON_VALUE'];
                            if ($row['CON_VALUE'] !== '') {
                                $this->setProDescription( $aFields['PRO_DESCRIPTION'] );
                            }
                            break;
                    }
                    $rs->next();
                    $row = $rs->getRow();
                }

                //If the prev script doesn't return anithing try to create the values based on EN
                if (! isset( $aFields['PRO_TITLE'] )) {
                    $aFields['PRO_TITLE'] = $oPro->getProTitle();
                    $this->setProTitle( $aFields['PRO_TITLE'] );
                }
                if (! isset( $aFields['PRO_DESCRIPTION'] )) {
                    $aFields['PRO_DESCRIPTION'] = $oPro->getProDescription();
                    $this->setProDescription( $aFields['PRO_DESCRIPTION'] );
                }

                //the following code is to copy the parent in old process, when the parent was empty.
                if ($oPro->getProParent() == '') {
                    $oPro->setProParent( $oPro->getProUid() );
                    $oPro->save();
                }

                //Get category Name, by default No category
                $aFields['PRO_CATEGORY_LABEL'] = G::LoadTranslation( "ID_PROCESS_NO_CATEGORY" );
                if ($aFields['PRO_CATEGORY'] != "") {
                    $oProCat = ProcessCategoryPeer::retrieveByPk( $aFields['PRO_CATEGORY'] );
                    if (is_object( $oProCat ) && get_class( $oProCat ) == 'ProcessCategory') {
                        $aFields['PRO_CATEGORY_LABEL'] = $oProCat->getCategoryName();
                    }
                }

                $aFields['PRO_DYNAFORMS'] = @unserialize( $aFields['PRO_DYNAFORMS'] );

                return $aFields;
            } else {
                throw (new Exception( "The row '$ProUid' in table Process doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function getAll ()
    {
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( ProcessPeer::PRO_UID );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_PARENT );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_STATUS );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CATEGORY );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CREATE_DATE );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CREATE_USER );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_DEBUG );

        $oCriteria->add( ProcessPeer::PRO_UID, '', Criteria::NOT_EQUAL );
        $oCriteria->add( ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL );

        //execute the query
        $oDataset = ProcessPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $processes = Array ();
        $uids = array ();
        while ($oDataset->next()) {
            $processes[] = $oDataset->getRow();
            $uids[] = $processes[sizeof( $processes ) - 1]['PRO_UID'];
        }
        //process details will have the info about the processes
        $processesDetails = Array ();

        //now get the labels for all process, using an array of Uids,
        $c = new Criteria( 'workflow' );
        //$c->add ( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
        $c->add( ContentPeer::CON_LANG, defined( 'SYS_LANG' ) ? SYS_LANG : 'en', Criteria::EQUAL );
        $c->add( ContentPeer::CON_ID, $uids, Criteria::IN );

        $dt = ContentPeer::doSelectRS( $c );
        $dt->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($dt->next()) {
            $row = $dt->getRow();
            $processesDetails[$row['CON_ID']][$row['CON_CATEGORY']] = $row['CON_VALUE'];
        }

        G::loadClass( 'configuration' );
        $oConf = new Configurations();
        $oConf->loadConfig( $obj, 'ENVIRONMENT_SETTINGS', '' );

        foreach ($processes as $i => $process) {
            $processes[$i]['PRO_TITLE'] = $processes[$i]['PRO_DESCRIPTION'] = '';

            if (isset( $processesDetails[$process['PRO_UID']]['PRO_TITLE'] )) {
                $processes[$i]['PRO_TITLE'] = $processesDetails[$process['PRO_UID']]['PRO_TITLE'];
            }

            if (isset( $processesDetails[$process['PRO_UID']] )) {
                $processes[$i]['PRO_DESCRIPTION'] = $processesDetails[$process['PRO_UID']]['PRO_DESCRIPTION'];
            }
        }

        usort( $processes, 'ordProcessByProTitle' );
        return $processes;
    }

    /**
     * Update the Prolication row
     *
     * @param array $aData
     * @return variant
     *
     */
    public function update ($aData)
    {
        if (isset( $aData['PRO_DYNAFORMS'] ) && is_array( $aData['PRO_DYNAFORMS'] )) {
            $aData['PRO_DYNAFORMS'] = @serialize( $aData['PRO_DYNAFORMS'] );
        }

        $con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
        try {
            $con->begin();
            $oPro = ProcessPeer::retrieveByPK( $aData['PRO_UID'] );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Process') {
                $oPro->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oPro->validate()) {
                    if (isset( $aData['PRO_TITLE'] )) {
                        $oPro->setProTitle( $aData['PRO_TITLE'] );
                    }
                    if (isset( $aData['PRO_DESCRIPTION'] )) {
                        $oPro->setProDescription( $aData['PRO_DESCRIPTION'] );
                    }
                    $res = $oPro->save();
                    $con->commit();

                    $this->memcachedDelete();

                    return $res;
                } else {
                    $msg = '';
                    foreach ($oPro->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }

                    throw (new Exception( 'The row cannot be updated!' . $msg ));
                }
            } else {
                $con->rollback();
                throw (new Exception( "The row '" . $aData['PRO_UID'] . "' in table Process doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * creates an Application row
     *
     * @param array $aData
     * @return variant
     *
     */
    public function createRow ($aData)
    {
        $con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
        //$con->begin(); //does not allow dual BEGIN
        $this->setProUid( $aData['PRO_UID'] );
        $this->setProParent( $aData['PRO_PARENT'] );
        $this->setProTime( $aData['PRO_TIME'] );
        $this->setProTimeunit( $aData['PRO_TIMEUNIT'] );
        $this->setProStatus( $aData['PRO_STATUS'] );
        $this->setProTypeDay( $aData['PRO_TYPE_DAY'] );
        $this->setProType( $aData['PRO_TYPE'] );
        $this->setProAssignment( $aData['PRO_ASSIGNMENT'] );
        $this->setProShowMap( $aData['PRO_SHOW_MAP'] );
        $this->setProShowMessage( $aData['PRO_SHOW_MESSAGE'] );
        $this->setProSubprocess( isset( $aData['PRO_SUBPROCESS'] ) ? $aData['PRO_SUBPROCESS'] : '' );
        $this->setProTriDeleted( isset( $aData['PRO_TRI_DELETED'] ) ? $aData['PRO_TRI_DELETED'] : '' );
        $this->setProTriCanceled( isset( $aData['PRO_TRI_CANCELED'] ) ? $aData['PRO_TRI_CANCELED'] : '' );
        $this->setProTriPaused( isset( $aData['PRO_TRI_PAUSED'] ) ? $aData['PRO_TRI_PAUSED'] : '' );
        $this->setProTriReassigned( isset( $aData['PRO_TRI_REASSIGNED'] ) ? $aData['PRO_TRI_REASSIGNED'] : '' );
        $this->setProShowDelegate( $aData['PRO_SHOW_DELEGATE'] );
        $this->setProShowDynaform( $aData['PRO_SHOW_DYNAFORM'] );
        $this->setProDerivationScreenTpl($aData['PRO_DERIVATION_SCREEN_TPL']);

        // validate if the category exists
        $criteria = new Criteria( 'workflow' );
        $criteria->add( ProcessCategoryPeer::CATEGORY_UID, $aData['PRO_CATEGORY'] );
        $ds = ProcessCategoryPeer::doSelectRS( $criteria );
        $ds->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $ds->next();
        // if it is not set, set value as empty "No Category"
        if (! $ds->getRow()) {
            $aData['PRO_CATEGORY'] = '';
        }

        $this->setProCategory( $aData['PRO_CATEGORY'] );
        $this->setProSubCategory( $aData['PRO_SUB_CATEGORY'] );
        $this->setProIndustry( $aData['PRO_INDUSTRY'] );
        $this->setProCreateDate( $aData['PRO_CREATE_DATE'] );
        $this->setProCreateUser( $aData['PRO_CREATE_USER'] );
        $this->setProHeight( $aData['PRO_HEIGHT'] );
        $this->setProWidth( $aData['PRO_WIDTH'] );
        $this->setProTitleX( $aData['PRO_TITLE_X'] );
        $this->setProTitleY( $aData['PRO_TITLE_Y'] );
        $this->setProDynaforms( isset( $aData['PRO_DYNAFORMS'] ) ? (is_array( $aData['PRO_DYNAFORMS'] ) ? serialize( $aData['PRO_DYNAFORMS'] ) : $aData['PRO_DYNAFORMS']) : '' );
        if ($this->validate()) {
            $con->begin();
            $res = $this->save();

            if (isset( $aData['PRO_TITLE'] ) && trim( $aData['PRO_TITLE'] ) != '') {
                $this->setProTitle( $aData['PRO_TITLE'] );
            } else {
                $this->setProTitle( 'Default Process Title' );
            }
            if (isset( $aData['PRO_DESCRIPTION'] )) {
                $this->setProDescription( $aData['PRO_DESCRIPTION'] );
            } else {
                $this->setProDescription( 'Default Process Description' );
            }
            $con->commit();

            $this->memcachedDelete();

            return $this->getProUid();
        } else {
            $msg = '';
            foreach ($this->getValidationFailures() as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }

            throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));
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
            $ProUid = (isset( $ProUid['PRO_UID'] ) ? $ProUid['PRO_UID'] : '');
        }
        try {
            $oPro = ProcessPeer::retrieveByPK( $ProUid );
            if (! is_null( $oPro )) {
                Content::removeContent( 'PRO_TITLE', '', $oPro->getProUid() );
                Content::removeContent( 'PRO_DESCRIPTION', '', $oPro->getProUid() );

                $this->memcachedDelete();

                return $oPro->delete();
            } else {
                throw (new Exception( "The row '$ProUid' in table Process doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function exists ($ProUid)
    {
        $oPro = ProcessPeer::retrieveByPk( $ProUid );
        return (is_object( $oPro ) && get_class( $oPro ) == 'Process');
    }

    public function existsByProTitle ($PRO_TITLE)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( 'COUNT(*) AS PROCESS' );
        $oCriteria->add( ContentPeer::CON_CATEGORY, 'PRO_TITLE' );
        $oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );
        $oCriteria->add( ContentPeer::CON_VALUE, $PRO_TITLE );
        $oDataset = ContentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();

        return $aRow['PROCESS'] ? true : false;
    }

    public function getAllProcessesCount ()
    {
        $c = $this->tmpCriteria;
        $c->clearSelectColumns();
        $c->addSelectColumn( 'COUNT(*)' );
        $oDataset = ProcessPeer::doSelectRS( $c );
        $oDataset->next();
        $aRow = $oDataset->getRow();

        if (is_array( $aRow )) {
            return $aRow[0];
        } else {
            return 0;
        }
    }

    public function getAllProcesses ($start, $limit, $category = null, $processName = null, $counters = true, $reviewSubProcess = false)
    {
        require_once PATH_RBAC . "model/RbacUsers.php";
        require_once "classes/model/ProcessCategory.php";
        require_once "classes/model/Users.php";

        $user = new RbacUsers();
        $aProcesses = Array ();
        $categories = Array ();
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( ProcessPeer::PRO_UID );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_PARENT );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_STATUS );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CATEGORY );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CREATE_DATE );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CREATE_USER );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_DEBUG );

        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );

        $oCriteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_UID );
        $oCriteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_NAME );

        $oCriteria->add( ProcessPeer::PRO_UID, '', Criteria::NOT_EQUAL );
        $oCriteria->add( ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL );
        if ($reviewSubProcess) {
            $oCriteria->add( ProcessPeer::PRO_SUBPROCESS, '1', Criteria::NOT_EQUAL );
        }

        if (isset( $category )) {
            $oCriteria->add( ProcessPeer::PRO_CATEGORY, $category, Criteria::EQUAL );
        }

        $oCriteria->addJoin( ProcessPeer::PRO_CREATE_USER, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->addJoin( ProcessPeer::PRO_CATEGORY, ProcessCategoryPeer::CATEGORY_UID, Criteria::LEFT_JOIN );

        $this->tmpCriteria = clone $oCriteria;

        if ($start != '') {
            $oCriteria->setOffset( $start );
        }
        if ($limit != '' && ! isset( $category ) && ! isset( $processName )) {
            $oCriteria->setLimit( $limit );
        }

            //execute a query to obtain numbers, how many cases there are by process
        if ($counters) {
            $casesCnt = $this->getCasesCountInAllProcesses();
        }

        //execute the query
        $oDataset = ProcessPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $processes = Array ();
        $uids = array ();
        while ($oDataset->next()) {
            $processes[] = $oDataset->getRow();
            $uids[] = $processes[sizeof( $processes ) - 1]['PRO_UID'];
        }

        //process details will have the info about the processes
        $processesDetails = Array ();

        //now get the labels for all process, using an array of Uids,
        $c = new Criteria( 'workflow' );
        //$c->add ( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
        $c->add( ContentPeer::CON_LANG, defined( 'SYS_LANG' ) ? SYS_LANG : 'en', Criteria::EQUAL );
        $c->add( ContentPeer::CON_ID, $uids, Criteria::IN );

        $dt = ContentPeer::doSelectRS( $c );
        $dt->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($dt->next()) {
            $row = $dt->getRow();
            $processesDetails[$row['CON_ID']][$row['CON_CATEGORY']] = $row['CON_VALUE'];
        }

        G::loadClass( 'configuration' );
        $oConf = new Configurations();
        $oConf->loadConfig( $obj, 'ENVIRONMENT_SETTINGS', '' );

        foreach ($processes as $process) {
            $proTitle = isset( $processesDetails[$process['PRO_UID']] ) && isset( $processesDetails[$process['PRO_UID']]['PRO_TITLE'] ) ? $processesDetails[$process['PRO_UID']]['PRO_TITLE'] : '';
            $proDescription = isset( $processesDetails[$process['PRO_UID']] ) && isset( $processesDetails[$process['PRO_UID']]['PRO_DESCRIPTION'] ) ? $processesDetails[$process['PRO_UID']]['PRO_DESCRIPTION'] : '';

            // verify if the title is already set on the current language
            if (trim( $proTitle ) == '') {
                // if not, then load the record to generate content for current language
                $proData = $this->load( $process['PRO_UID'] );
                $proTitle = $proData['PRO_TITLE'];
                $proDescription = $proData['PRO_DESCRIPTION'];
            }

            //filtering by $processName
            if (isset( $processName ) && $processName != '' && stripos( $proTitle, $processName ) === false) {
                continue;
            }

            if ($counters) {
                $casesCountTotal = 0;
                if (isset( $casesCnt[$process['PRO_UID']] )) {
                    foreach ($casesCnt[$process['PRO_UID']] as $item) {
                        $casesCountTotal += $item;
                    }
                }
            }

            //get user format from configuration
            $userOwner = isset( $oConf->aConfig['format'] ) ? $oConf->aConfig['format'] : '';
            $creationDateMask = isset( $oConf->aConfig['dateFormat'] ) ? $oConf->aConfig['dateFormat'] : '';
            if ($userOwner != '') {
                $userOwner = str_replace( '@userName', $process['USR_USERNAME'], $userOwner );
                $userOwner = str_replace( '@firstName', $process['USR_FIRSTNAME'], $userOwner );
                $userOwner = str_replace( '@lastName', $process['USR_LASTNAME'], $userOwner );
                if ($userOwner == " ( )") {
                    $userOwner = '-';
                }
            } else {
                $userOwner = $process['USR_FIRSTNAME'] . ' ' . $process['USR_LASTNAME'];
            }

            //get date format from configuration
            if ($creationDateMask != '') {
                list ($date, $time) = explode( ' ', $process['PRO_CREATE_DATE'] );
                list ($y, $m, $d) = explode( '-', $date );
                list ($h, $i, $s) = explode( ':', $time );

                $process['PRO_CREATE_DATE'] = date( $creationDateMask, mktime( $h, $i, $s, $m, $d, $y ) );
            }

            $process['PRO_CATEGORY_LABEL'] = trim( $process['PRO_CATEGORY'] ) != '' ? $process['CATEGORY_NAME'] : G::LoadTranslation( 'ID_PROCESS_NO_CATEGORY' );
            $process['PRO_TITLE'] = $proTitle;
            $process['PRO_DESCRIPTION'] = $proDescription;
            $process['PRO_DEBUG'] = $process['PRO_DEBUG'];
            $process['PRO_DEBUG_LABEL'] = ($process['PRO_DEBUG'] == "1") ? G::LoadTranslation( 'ID_ON' ) : G::LoadTranslation( 'ID_OFF' );
            $process['PRO_STATUS_LABEL'] = $process['PRO_STATUS'] == 'ACTIVE' ? G::LoadTranslation( 'ID_ACTIVE' ) : G::LoadTranslation( 'ID_INACTIVE' );
            $process['PRO_CREATE_USER_LABEL'] = $userOwner;
            if ($counters) {
                $process['CASES_COUNT_TO_DO'] = (isset( $casesCnt[$process['PRO_UID']]['TO_DO'] ) ? $casesCnt[$process['PRO_UID']]['TO_DO'] : 0);
                $process['CASES_COUNT_COMPLETED'] = (isset( $casesCnt[$process['PRO_UID']]['COMPLETED'] ) ? $casesCnt[$process['PRO_UID']]['COMPLETED'] : 0);
                $process['CASES_COUNT_DRAFT'] = (isset( $casesCnt[$process['PRO_UID']]['DRAFT'] ) ? $casesCnt[$process['PRO_UID']]['DRAFT'] : 0);
                $process['CASES_COUNT_CANCELLED'] = (isset( $casesCnt[$process['PRO_UID']]['CANCELLED'] ) ? $casesCnt[$process['PRO_UID']]['CANCELLED'] : 0);
                $process['CASES_COUNT'] = $casesCountTotal;
            }

            unset( $process['PRO_CREATE_USER'] );

            $aProcesses[] = $process;

        }

        usort( $aProcesses, 'ordProcessByProTitle' );
        return $aProcesses;
    }

    public function getCasesCountInAllProcesses ()
    {
        /*SELECT PRO_UID, APP_STATUS, COUNT( * )
          FROM APPLICATION
          GROUP BY PRO_UID, APP_STATUS*/
        require_once 'classes/model/Application.php';

        $memcache = & PMmemcached::getSingleton( SYS_SYS );
        $memkey = 'getCasesCountInAllProcesses';
        if (($aProcesses = $memcache->get( $memkey )) === false) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria->addSelectColumn( ApplicationPeer::APP_STATUS );
            $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
            $oCriteria->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oCriteria->addGroupByColumn( ApplicationPeer::APP_STATUS );

            $oDataset = ProcessPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $aProcesses = Array ();
            while ($oDataset->next()) {
                $row = $oDataset->getRow();
                $aProcesses[$row['PRO_UID']][$row['APP_STATUS']] = $row['CNT'];
            }
            $memcache->set( $memkey, $aProcesses, PMmemcached::ONE_HOUR );
        }
        return $aProcesses;
    }

    public function getAllProcessesByCategory ()
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( ProcessPeer::PRO_CATEGORY );
        $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
        $oCriteria->addGroupByColumn( ProcessPeer::PRO_CATEGORY );
        $oDataSet = ProcessPeer::doSelectRS( $oCriteria );
        $oDataSet->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aProc = Array ();
        while ($oDataSet->next()) {
            $row = $oDataSet->getRow();
            $aProc[$row['PRO_CATEGORY']] = $row['CNT'];
        }
        return $aProc;
    }

    public function getTriggerWebBotProcess ($proUid, $action)
    {
        require_once ("classes/model/Triggers.php");

        if ((! isset( $proUid ) && $proUid == '') || (! isset( $action ) && $action == '')) {
            return false;
        }

        $action = G::toUpper( $action );
        $webBotTrigger = '';

        switch ($action) {
            case 'DELETED':
                $var = ProcessPeer::PRO_TRI_DELETED;
                break;
            case 'CANCELED':
                $var = ProcessPeer::PRO_TRI_CANCELED;
                break;
            case 'PAUSED':
                $var = ProcessPeer::PRO_TRI_PAUSED;
                break;
            case 'REASSIGNED':
                $var = ProcessPeer::PRO_TRI_REASSIGNED;
                break;
        }
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( $var );
        $oCriteria->addSelectColumn( TriggersPeer::TRI_WEBBOT );
        $oCriteria->addJoin( $var, TriggersPeer::TRI_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( ProcessPeer::PRO_UID, $proUid );
        $oDataSet = ProcessPeer::doSelectRS( $oCriteria );

        $oDataSet->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        if ($oDataSet->next()) {
            $row = $oDataSet->getRow();
            $webBotTrigger = $row['TRI_WEBBOT'];
        }
        return $webBotTrigger;
    }

    public function memcachedDelete ()
    {
        //Limit defined in processmaker/workflow/engine/templates/processes/main.js
        $limit = 25;
        $start = 0;

        $memcache = &PMmemcached::getSingleton( SYS_SYS );

        for ($start = 0; $start <= 50 - 1; $start ++) {
            $memkey = "processList-allProcesses-" . ($start * $limit) . "-" . $limit;
            $memkeyTotal = $memkey . "-total";

            $r = $memcache->delete( $memkey );
            $r = $memcache->delete( $memkeyTotal );
        }
    }
}

function ordProcessByProTitle ($a, $b)
{
    if ($a['PRO_TITLE'] > $b['PRO_TITLE']) {
        return 1;
    } elseif ($a['PRO_TITLE'] < $b['PRO_TITLE']) {
        return - 1;
    } else {
        return 0;
    }
}

