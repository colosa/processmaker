<?php
/**
 * processes_Export.php
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
use  ProcessMaker\Util\Common;

$response = new StdClass();
$outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;

try {
	if(empty($_GET)){
		$proUid = Bootstrap::json_decode( $_POST['data']);
		$_GET["pro_uid"] = $proUid->pro_uid;
        /*----------------------------------********---------------------------------*/
	}
    if (\BpmnProject::exists($_GET["pro_uid"]) && isset($_GET['objects'])) {
        /*----------------------------------********---------------------------------*/
            $exporter = new ProcessMaker\Exporter\XmlExporter($_GET["pro_uid"]);
            $projectName = $exporter->getProjectName();
            $getProjectName = $exporter->truncateName($projectName, false);

            $version = Common::getLastVersionSpecialCharacters($outputDir, $getProjectName, "pmx") + 1;
            $outputFilename = sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");
            $outputFilename = $exporter->saveExport($outputDir . $outputFilename);
        /*----------------------------------********---------------------------------*/
        G::auditLog('ExportProcess','Export process "' . $projectName . '"');
    } else {
        $oProcess = new Processes();
        $proFields = $oProcess->serializeProcess($_GET["pro_uid"]);
        $result = $oProcess->saveSerializedProcess($proFields);
        $outputFilename = $result["FILENAME"];

        rename($outputDir . $outputFilename . "tpm", $outputDir . $outputFilename);
        G::auditLog('ExportProcess','Export process "' . $result["PRO_TITLE"] . '"');
    }
    $response->file_hash = base64_encode($outputFilename);
    $response->success = true;

    /* Render page */
    if (isset( $_REQUEST["processMap"] ) && $_REQUEST["processMap"] == 1) {
    	$link = parse_url($result['FILENAME_LINK']);
    	$result['FILENAME_LINK'] = $link['path'] . '?file_hash=' . $response->file_hash;

    	$G_PUBLISH = new Publisher();
    	$G_PUBLISH->AddContent( "xmlform", "xmlform", "processes/processes_Export", "", $result );

    	G::RenderPage( "publish", "raw" );
    } else{
    	echo json_encode($response);
    }
} catch (Exception $e) {
    $response->message = $e->getMessage();
    $response->success = false;
}
