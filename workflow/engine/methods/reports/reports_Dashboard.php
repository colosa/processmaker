<?php
/**
 * reports_Dashboard.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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
 */

$_GET['sType'] = str_replace( '?', '', $_GET['sType'] );

G::LoadClass( 'report' );
$oReport = new Report();
switch ($_GET['sType']) {
    case 'ID_REPORT1':
        $oCriteria = $oReport->generatedReport1();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report1_dashboard', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'ID_REPORT2':
        $oCriteria = $oReport->generatedReport2();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report2_dashboard', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'ID_REPORT3':
        $oCriteria = $oReport->generatedReport3();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report3_dashboard', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'ID_REPORT4':
        $oCriteria = $oReport->generatedReport4();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report4_dashboard', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'ID_REPORT5':
        $oCriteria = $oReport->generatedReport5();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/report5_dashboard', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
}

