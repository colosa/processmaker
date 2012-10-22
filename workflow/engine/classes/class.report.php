<?php

/**
 * class.report.php
 *
 * @package workflow.engine.ProcessMaker
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

/**
 * Report - Report class
 *
 * @package workflow.engine.ProcessMaker
 * @author Everth S. Berrios Morales
 * @copyright 2008 COLOSA
 */

class Report
{

    /**
     * This function does a sql statment to a report
     *
     *
     * @name generatedReport1
     *
     * param
     * @return object
     */
    public function generatedReport1 ()
    {
        $this->reportsPatch();

        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "TOTALDUR", "SUM(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "PROMEDIO", "AVG(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( 'PRO_TITLE', 'C1.CON_VALUE' );
        $oCriteria->addAlias( "C1", 'CONTENT' );
        $proTitleConds = array ();
        $proTitleConds[] = array (AppDelegationPeer::PRO_UID,'C1.CON_ID'
        );
        $proTitleConds[] = array ('C1.CON_CATEGORY',$del . 'PRO_TITLE' . $del
        );
        $proTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proTitleConds, Criteria::LEFT_JOIN );
        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addGroupByColumn( 'C1.CON_VALUE' );

        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aProcess[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );
        while ($aRow = $oDataset->getRow()) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );

            $aProcess[] = array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['PRO_TITLE'],'CANTCASES' => ApplicationPeer::doCount( $oCriteria ),'MIN' => number_format( $aRow['MIN'], 2 ),'MAX' => number_format( $aRow['MAX'], 2 ),'TOTALDUR' => number_format( $aRow['TOTALDUR'], 2 ),'PROMEDIO' => number_format( $aRow['PROMEDIO'], 2 )
            );
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['reports'] = $aProcess;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function does a sql statment to a report wiht a condition
     * or maybe when you're looking for some specials cases
     *
     * @name generatedReport1_filter
     *
     * @param string $from
     * @param string $to
     * @param string $startedby
     * @return object
     */
    public function generatedReport1_filter ($from, $to, $startedby)
    {
        $this->reportsPatch();

        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "TOTALDUR", "SUM(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "PROMEDIO", "AVG(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( 'PRO_TITLE', 'C1.CON_VALUE' );
        $oCriteria->addAlias( "C1", 'CONTENT' );
        $proTitleConds = array ();
        $proTitleConds[] = array (AppDelegationPeer::PRO_UID,'C1.CON_ID'
        );
        $proTitleConds[] = array ('C1.CON_CATEGORY',$del . 'PRO_TITLE' . $del
        );
        $proTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proTitleConds, Criteria::LEFT_JOIN );
        $oCriteria->addJoin( AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        //$oCriteria->add(AppDelegationPeer::DEL_DURATION,  $from, Criteria::GREATER_EQUAL);
        //$oCriteria->add(AppDelegationPeer::DEL_DURATION,  $to, Criteria::LESS_EQUAL);
        //$aAux1 = explode('-', $from);  date('Y-m-d H:i:s', mktime(0, 0, 0, $aAux1[1], $aAux1[2], $aAux1[0]))
        $oCriteria->add( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $from . ' 00:00:00', Criteria::GREATER_EQUAL )->addAnd( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $to . ' 23:59:59', Criteria::LESS_EQUAL ) ) );

        if ($startedby != '') {
            $oCriteria->add( ApplicationPeer::APP_INIT_USER, $startedby );
        }

        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addGroupByColumn( 'C1.CON_VALUE' );

        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        $aProcess[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );
        while ($aRow = $oDataset->getRow()) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            if ($startedby != '') {
                $oCriteria->add( ApplicationPeer::APP_INIT_USER, $startedby );
            }

            $aProcess[] = array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['PRO_TITLE'],'CANTCASES' => ApplicationPeer::doCount( $oCriteria ),'MIN' => number_format( $aRow['MIN'], 2 ),'MAX' => number_format( $aRow['MAX'], 2 ),'TOTALDUR' => number_format( $aRow['TOTALDUR'], 2 ),'PROMEDIO' => number_format( $aRow['PROMEDIO'], 2 ));
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['reports'] = $aProcess;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function gets info about a report
     *
     *
     * @name descriptionReport1
     *
     * @param string $PRO_UID
     * @return object
     */
    public function descriptionReport1 ($PRO_UID)
    {
        $this->reportsPatch();

        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Task.php';
        require_once 'classes/model/Content.php';

        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "TOTALDUR", "SUM(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "PROMEDIO", "AVG(" . AppDelegationPeer::DEL_DURATION . ")" );

        $oCriteria->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );

        $oCriteria->addAsColumn( 'TAS_TITLE', 'C.CON_VALUE' );
        $oCriteria->addAlias( "C", 'CONTENT' );

        $proContentConds = array ();
        $proContentConds[] = array (AppDelegationPeer::TAS_UID,'C.CON_ID'
        );
        $proContentConds[] = array ('C.CON_CATEGORY',$del . 'TAS_TITLE' . $del
        );
        $proContentConds[] = array ('C.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proContentConds, Criteria::LEFT_JOIN );

        $oCriteria->add( AppDelegationPeer::PRO_UID, $PRO_UID );

        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addGroupByColumn( 'C.CON_VALUE' );

        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        $aProcess[] = array ('TAS_TITLE' => 'char','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );
        while ($aRow = $oDataset->getRow()) {
            $aProcess[] = array ('TAS_TITLE' => $aRow['TAS_TITLE'],'MIN' => number_format( $aRow['MIN'], 2 ),'MAX' => number_format( $aRow['MAX'], 2 ),'TOTALDUR' => number_format( $aRow['TOTALDUR'], 2 ),'PROMEDIO' => number_format( $aRow['PROMEDIO'], 2 )
            );
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['reports'] = $aProcess;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function generates a other kind of report
     *
     *
     * @name generatedReport2
     *
     * param
     * @return object
     */
    public function generatedReport2 ()
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( 'PRO_TITLE', 'C1.CON_VALUE' );
        $oCriteria->addAlias( "C1", 'CONTENT' );
        $proTitleConds = array ();
        $proTitleConds[] = array (AppDelegationPeer::PRO_UID,'C1.CON_ID'
        );
        $proTitleConds[] = array ('C1.CON_CATEGORY',$del . 'PRO_TITLE' . $del
        );
        $proTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proTitleConds, Criteria::LEFT_JOIN );
        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );

        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        $month = date( 'Y-m-d', mktime( 0, 0, 0, date( "m" ) - 1, date( "d" ), date( "Y" ) ) );
        $lastmonth = date( 'Y-m-d', mktime( 0, 0, 0, date( "m" ) - 2, date( "d" ), date( "Y" ) ) );

        $day = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 1, date( "Y" ) );
        $lastday = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 2, date( "Y" ) );

        $aProcess[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','CASELASTMONTH' => 'integer','CASELASTDAY' => 'integer'
        );

        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant = $aRow2['CANTCASES'];

            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $lastmonth, Criteria::GREATER_EQUAL );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $month, Criteria::LESS_EQUAL );
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant1 = $aRow2['CANTCASES'];

            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $lastday, Criteria::GREATER_EQUAL );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $day, Criteria::LESS_EQUAL );
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant2 = $aRow2['CANTCASES'];

            $aProcess[] = array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['PRO_TITLE'],'CANTCASES' => $cant,'MIN' => number_format( $aRow['MIN'], 2 ),'MAX' => number_format( $aRow['MAX'], 2 ),'CASELASTMONTH' => number_format( $cant1, 2 ),'CASELASTDAY' => number_format( $cant2, 2 )
            );
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['reports'] = $aProcess;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );
        return $oCriteria;
    }

    /**
     * This function generates the description about a report
     *
     *
     * @name reports_Description_filter
     *
     * @param string $from
     * @param string $to
     * @param string $startedby
     * @param string $PRO_UID
     * @return object
     */
    public function reports_Description_filter ($from, $to, $startedby, $PRO_UID)
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Task.php';
        require_once 'classes/model/Content.php';
        require_once 'classes/model/Users.php';

        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "TOTALDUR", "SUM(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "PROMEDIO", "AVG(" . AppDelegationPeer::DEL_DURATION . ")" );

        $oCriteria->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );

        $oCriteria->addAsColumn( 'TAS_TITLE', 'C.CON_VALUE' );
        $oCriteria->addAlias( "C", 'CONTENT' );

        $proContentConds = array ();
        $proContentConds[] = array (AppDelegationPeer::TAS_UID,'C.CON_ID'
        );
        $proContentConds[] = array ('C.CON_CATEGORY',$del . 'TAS_TITLE' . $del
        );
        $proContentConds[] = array ('C.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proContentConds, Criteria::LEFT_JOIN );

        $oCriteria->add( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $from . ' 00:00:00', Criteria::GREATER_EQUAL )->addAnd( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $to . ' 23:59:59', Criteria::LESS_EQUAL ) ) );

        if ($startedby != '') {
            $oCriteria->add( AppDelegationPeer::USR_UID, $startedby );
        }

        $oCriteria->add( AppDelegationPeer::PRO_UID, $PRO_UID );

        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addGroupByColumn( 'C.CON_VALUE' );

        return $oCriteria;
    }

    /**
     * This function looks for an special case it has a condition
     *
     *
     * @name generatedReport2_filter
     *
     * @param string $from
     * @param string $to
     * @param string $startedby
     * @return object
     */
    public function generatedReport2_filter ($from, $to, $startedby)
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Users.php';

        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addAsColumn( "MIN", "MIN(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( "MAX", "MAX(" . AppDelegationPeer::DEL_DURATION . ")" );
        $oCriteria->addAsColumn( 'PRO_TITLE', 'C1.CON_VALUE' );
        $oCriteria->addAlias( "C1", 'CONTENT' );
        $proTitleConds = array ();
        $proTitleConds[] = array (AppDelegationPeer::PRO_UID,'C1.CON_ID'
        );
        $proTitleConds[] = array ('C1.CON_CATEGORY',$del . 'PRO_TITLE' . $del
        );
        $proTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del
        );
        $oCriteria->addJoinMC( $proTitleConds, Criteria::LEFT_JOIN );
        $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );
        $oCriteria->addGroupByColumn( 'C1.CON_VALUE' );

        $oCriteria->add( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $from . ' 00:00:00', Criteria::GREATER_EQUAL )->addAnd( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_INIT_DATE, $to . ' 23:59:59', Criteria::LESS_EQUAL ) ) );

        if ($startedby != '') {
            $oCriteria->add( AppDelegationPeer::USR_UID, $startedby );
        }

        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        $month = date( 'Y-m-d', mktime( 0, 0, 0, date( "m" ) - 1, date( "d" ), date( "Y" ) ) );
        $lastmonth = date( 'Y-m-d', mktime( 0, 0, 0, date( "m" ) - 2, date( "d" ), date( "Y" ) ) );
        $day = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 1, date( "Y" ) );
        $lastday = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 2, date( "Y" ) );
        $aProcess[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','CASELASTMONTH' => 'integer','CASELASTDAY' => 'integer');

        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            if ($startedby != '') {
                $oCriteria2->add( ApplicationPeer::APP_INIT_USER, $startedby );
            }
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant = $aRow2['CANTCASES'];

            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $lastmonth, Criteria::GREATER_EQUAL );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $month, Criteria::LESS_EQUAL );
            if ($startedby != '') {
                $oCriteria2->add( ApplicationPeer::APP_INIT_USER, $startedby );
            }
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant1 = $aRow2['CANTCASES'];

            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->addSelectColumn( ApplicationPeer::PRO_UID );
            $oCriteria2->addAsColumn( "CANTCASES", "COUNT(*)" );
            $oCriteria2->add( ApplicationPeer::PRO_UID, $aRow['PRO_UID'] );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $lastday, Criteria::GREATER_EQUAL );
            $oCriteria2->add( ApplicationPeer::APP_INIT_DATE, $day, Criteria::LESS_EQUAL );
            if ($startedby != '') {
                $oCriteria2->add( ApplicationPeer::APP_INIT_USER, $startedby );
            }
            $oCriteria2->addGroupByColumn( ApplicationPeer::PRO_UID );
            $oDataset2 = AppDelegationPeer::doSelectRS( $oCriteria2 );
            $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $cant2 = $aRow2['CANTCASES'];

            /*$aProcess[] = array('PRO_UID'   => $aRow['PRO_UID'],
                            'PRO_TITLE' => $aRow['PRO_TITLE'],
                            'CANTCASES' => $cant,
                            'CASELASTMONTH' => $cant1,
                            'CASELASTDAY' => $cant2
                           );*/
            $aProcess[] = array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['PRO_TITLE'],'CANTCASES' => $cant,'MIN' => number_format( $aRow['MIN'], 2 ),'MAX' => number_format( $aRow['MAX'], 2 ),'CASELASTMONTH' => number_format( $cant1, 2 ),'CASELASTDAY' => number_format( $cant2, 2 ));
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['reports'] = $aProcess;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );
        return $oCriteria;
    }

    /**
     * This function looks for an special case it has a condition
     *
     *
     * @name generatedReport2_filter
     *
     * @param string $from
     * @param string $to
     * @param string $startedby
     * @return object
     */
    public function generatedReport3 ()
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $sql = "SELECT CONCAT(SUBSTRING(AD.DEL_INIT_DATE,6,2),'-', SUBSTRING(AD.DEL_INIT_DATE,1,4)) AS FECHA,
              COUNT(DISTINCT(AD.APP_UID)) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              WHERE AD.APP_UID<>'' AND P.PRO_STATUS<>'DISABLED'
              GROUP BY FECHA";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        $ROW[] = array ('FECHA' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float');

        while ($rs->next()) {
            $ROW[] = array ('FECHA' => $rs->getString( 'FECHA' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 ));
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;

    }

    /**
     * This function looks for an special case it has a condition
     *
     *
     * @name generatedReport3_filter
     *
     * @param string $process
     * @param string $task
     * @return object
     */
    public function generatedReport3_filter ($process, $task)
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        if ($process == '') {
            $var = " WHERE P.PRO_STATUS<>'DISABLED'";
        } else {
            if ($task == '') {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
                WHERE P.PRO_STATUS<>'DISABLED' AND AD.PRO_UID='" . $process . "'";
            } else {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
             WHERE P.PRO_STATUS<>'DISABLED' AND AD.PRO_UID='" . $process . "' AND AD.TAS_UID='" . $task . "' ";
            }
        }
        $sql = "SELECT CONCAT(SUBSTRING(AD.DEL_INIT_DATE,6,2),'-', SUBSTRING(AD.DEL_INIT_DATE,1,4)) AS FECHA,
              COUNT(DISTINCT(AD.APP_UID)) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              " . $var . "
              GROUP BY FECHA";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();
        $ROW[] = array ('FECHA' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float');

        while ($rs->next()) {
            $ROW[] = array ('FECHA' => $rs->getString( 'FECHA' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 ));
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function generates a report
     *
     *
     * @name generatedReport4
     *
     * param
     * @return object
     */
    public function generatedReport4 ()
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Process.php';
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $sql = "SELECT CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER,
              COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APPLICATION AS A
              LEFT JOIN APP_DELEGATION AS AD ON(A.APP_UID = AD.APP_UID AND AD.DEL_INDEX=1)
              LEFT JOIN USERS AS U ON(U.USR_UID = A.APP_INIT_USER)
              WHERE A.APP_UID<>''
              GROUP BY USER";
        // AND P.PRO_STATUS<>'DISABLED' that hapens when it is created to new version it exists at the moment to import
        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        $ROW[] = array ('USER' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );

        while ($rs->next()) {
            $ROW[] = array ('USER' => $rs->getString( 'USER' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 )
            );
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;

    }

    /**
     * This function generates a filter to report 4
     *
     *
     * @name generatedReport4_filter
     *
     * @param string process
     * @param string task
     * @return object
     */
    public function generatedReport4_filter ($process, $task)
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Process.php';
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        if ($process == '') {
            $var = " ";
        } else {
            if ($task == '') {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
                WHERE AD.PRO_UID='" . $process . "'";
            } else {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
               WHERE AD.PRO_UID='" . $process . "' AND AD.TAS_UID='" . $task . "' ";
            }
        }

        $sql = "SELECT CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER,
              COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APPLICATION AS A
              LEFT JOIN APP_DELEGATION AS AD ON(A.APP_UID = AD.APP_UID AND AD.DEL_INDEX=1)
              LEFT JOIN USERS AS U ON(U.USR_UID = A.APP_INIT_USER)
              " . $var . "
              GROUP BY USER";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        $ROW[] = array ('USER' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );

        while ($rs->next()) {
            $ROW[] = array ('USER' => $rs->getString( 'USER' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 )
            );
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function generates a Report
     *
     *
     * @name generatedReport4_filter
     *
     * @param string process
     * @param string task
     * @return object
     */
    public function generatedReport5 ()
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Process.php';
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $sql = "SELECT CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER,
              COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              LEFT JOIN USERS AS U ON(U.USR_UID = AD.USR_UID)
              WHERE AD.APP_UID<>'' AND AD.DEL_FINISH_DATE IS NULL
              GROUP BY USER";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        $ROW[] = array ('USER' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );

        while ($rs->next()) {
            $ROW[] = array ('USER' => $rs->getString( 'USER' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 )
            );
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;

    }

    /**
     * This function generates a filter to report 5
     *
     *
     * @name generatedReport5_filter
     *
     * @param string process
     * @param string task
     * @return object
     */
    public function generatedReport5_filter ($process, $task)
    {
        $this->reportsPatch();
        require_once 'classes/model/AppDelegation.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Process.php';
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        if ($process == '') {
            $var = " WHERE AD.DEL_FINISH_DATE IS NULL";
        } else {
            if ($task == '') {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
                WHERE AD.PRO_UID='" . $process . "' AND AD.DEL_FINISH_DATE IS NULL";
            } else {
                $var = " LEFT JOIN TASK AS T ON (AD.TAS_UID = T.TAS_UID)
             WHERE AD.PRO_UID='" . $process . "' AND AD.TAS_UID='" . $task . "' ";
            }
        }
        $sql = "SELECT CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER,
              COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              LEFT JOIN USERS AS U ON(U.USR_UID = AD.USR_UID)
              " . $var . "
              GROUP BY USER";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        $ROW[] = array ('USER' => 'char','CANTCASES' => 'integer','MIN' => 'float','MAX' => 'float','TOTALDUR' => 'float','PROMEDIO' => 'float'
        );

        while ($rs->next()) {
            $ROW[] = array ('USER' => $rs->getString( 'USER' ),'CANTCASES' => $rs->getString( 'CANTCASES' ),'MIN' => number_format( $rs->getString( 'MIN' ), 2 ),'MAX' => number_format( $rs->getString( 'MAX' ), 2 ),'TOTALDUR' => number_format( $rs->getString( 'TOTALDUR' ), 2 ),'PROMEDIO' => number_format( $rs->getString( 'PROMEDIO' ), 2 )
            );
        }

        global $_DBArray;
        $_DBArray['reports'] = $ROW;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reports' );

        return $oCriteria;
    }

    /**
     * This function returns an array, it has the reports' names
     *
     *
     * @name getAvailableReports
     *
     * param
     * @return array
     */
    public function getAvailableReports ()
    {
        return array ('ID_REPORT1','ID_REPORT2','ID_REPORT3','ID_REPORT4','ID_REPORT5'
        );
    }

    /**
     * Patch for reports by The Answer (17-10-2k8)
     *
     *
     * @name reportsPatch
     *
     * param
     * @return void
     */

    public function reportsPatch ()
    {
        require_once 'classes/model/AppDelegation.php';

        $oCriteria = new Criteria( 'workflow' );
        $del = DBAdapter::getStringDelimiter();
        $oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
        $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_DURATION );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $oAppDelegation = new AppDelegation();

            $aData['APP_UID'] = $aRow['APP_UID'];
            $aData['DEL_INDEX'] = $aRow['DEL_INDEX'];
            $aData['DEL_DELEGATE_DATE'] = $aRow['DEL_DELEGATE_DATE'];

            if ($aRow['DEL_INIT_DATE'] == null) {
                $aData['DEL_INIT_DATE'] = $aRow['DEL_DELEGATE_DATE'];
            } else {
                $aData['DEL_INIT_DATE'] = $aRow['DEL_INIT_DATE'];
            }
                //$aData['DEL_FINISH_DATE']=$aRow['DEL_FINISH_DATE'];
            if ($aRow['DEL_DURATION'] != 0) {
                G::LoadClass( 'dates' );
                $oDates = new dates();
                $aData['DEL_DURATION'] = $oDates->calculateDuration( $aData['DEL_INIT_DATE'], $aRow['DEL_FINISH_DATE'], null, null, $aRow['TAS_UID'] );
            }

            $oAppDelegation->update( $aData );

            $oDataset->next();
        }
        return;
    }
}

