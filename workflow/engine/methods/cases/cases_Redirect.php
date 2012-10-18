<?php
/**
 * cases_Redirect.php
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
/*
 * Created on 19-03-2009
 *
 * @author Everth S . Berrios <everth@colosa.com>
 */
require_once 'classes/model/AppDocument.php';
$oAppDocument = new AppDocument();
$aFields = $oAppDocument->load( $_GET['a'] );
require_once 'classes/model/OutputDocument.php';
$oOutputDocument = new OutputDocument();
$aOD = $oOutputDocument->load( $aFields['DOC_UID'] );
$a = $_GET['a'];
$ext = strtolower( $aOD['OUT_DOC_GENERATE'] );

G::header( 'location: cases_ShowOutputDocument?a=' . $a . '&ext=' . $ext );

