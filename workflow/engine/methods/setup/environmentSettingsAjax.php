<?php
/**
 *
 * @author Erik A.O. <erik@colosa.com>
 * @date Sept 13th, 2010
 *
 */

$request = isset( $_POST["request"] ) ? $_POST["request"] : (isset( $_GET["request"] ) ? $_GET["request"] : null);
$result = new stdclass();

switch ($request) {
    case "getUserMaskList":
        $result->rows = Configurations::getUserNameFormats();
        print (G::json_encode( $result )) ;
        break;
    case "getDateFormats":
        $result->rows = Configurations::getDateFormats();
        print (G::json_encode( $result )) ;
        break;
    case "getCasesListDateFormat":
        $result->rows = Configurations::getDateFormats();
        print (G::json_encode( $result )) ;
        break;
    case "getCasesListRowNumber":
        for ($i = 10; $i <= 50; $i += 5) {
            $formats[] = array ("id" => "$i","name" => "$i"
            );
        }

        $result->rows = $formats;
        print (G::json_encode( $result )) ;
        break;
    case "save":
        $conf = new Configurations();
        $config = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "", "", "", "", $outResult);
        if (is_numeric($config)) {
            $config = array();
        }
        if (is_array($outResult) && isset($outResult["directoryStructure"])) {
            $config["directoryStructure"] = $outResult["directoryStructure"];
        }
        if (isset($_POST["userFormat"])) {
            $config['format'] = $_POST["userFormat"]; 
        } 
        if (isset($_POST["dateFormat"])) {
            $config['dateFormat'] = $_POST["dateFormat"]; 
        }
        $config['startCaseHideProcessInf'] = ((isset( $_POST["hideProcessInf"] )) ? true : false);
        if (isset($_POST["casesListDateFormat"])) {
            $config['casesListDateFormat'] = $_POST["casesListDateFormat"]; 
        }
        if (isset($_POST["casesListDateFormat"])) {
            $config['casesListRowNumber'] = intval( $_POST["casesListRowNumber"] );
        }
        if (isset($_POST["txtCasesRefreshTime"])) {
            $config['casesListRefreshTime'] = intval( $_POST["txtCasesRefreshTime"]);
        }

        $conf->aConfig = $config;
        $conf->saveConfig( "ENVIRONMENT_SETTINGS", "" );

        G::auditLog("UpdateEnvironmentSettings", "UserNameDisplayFormat -> ".(isset($_POST["userFormat"]) ? $_POST["userFormat"] : '').", GlobalDateFormat -> ".(isset($_POST["dateFormat"]) ? $_POST["dateFormat"] : '').", HideProcessInformation -> ".(string)isset($_POST["hideProcessInf"]).", DateFormat -> ".(isset($_POST["casesListDateFormat"]) ? $_POST["casesListDateFormat"] : '').", NumberOfRowsPerPage -> ".(isset($_POST["casesListRowNumber"]) ? $_POST["casesListRowNumber"] : '').", RefreshTimeSeconds -> ".(isset($_POST["txtCasesRefreshTime"]) ? $_POST["txtCasesRefreshTime"] : ''));

        $response = new stdclass();
        $response->success = true;
        $response->msg = G::LoadTranslation( "ID_SAVED_SUCCESSFULLY" );

        echo G::json_encode( $response );
        break;
}

