<?php
/**
 * cases_ShowDocument.php
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
/*
 * Created on 13-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */

if (!isset($_SESSION['PROCESS'])) {
  G::header('location: login');
}

require_once 'classes/model/AppDocumentPeer.php';

$oAppDocument = new AppDocument();
$oAppDocument->Fields = $oAppDocument->load($_GET['a']);

$sAppDocUid = $oAppDocument->getAppDocUid();
$info = pathinfo($oAppDocument->getAppDocFilename());
$iDocVersion = $oAppDocument->getDocVersion();
$ext = $info['extension'];

$realPath = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/' . $sAppDocUid .'_'.$iDocVersion . '.' . $ext;
if (!file_exists($realPath)) {
  $realPath = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/' . $sAppDocUid . '.' . $ext;
}
if (!file_exists($realPath)) {
  G::streamFile($realPath, true, $oAppDocument->Fields['APP_DOC_FILENAME']);
}
else {
  die("'" . $oAppDocument->Fields['APP_DOC_FILENAME'] . "' " . G::LoadTranslation('ID_ERROR_STREAMING_FILE'));
}