<?php
/**
 *
 * @author Erik A.O. <erik@colosa.com>
 * @date Sept 13th, 2010
 *
 */

G::LoadClass( "configuration" );

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
        $config = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "" );
        $config['format'] = $_POST["userFormat"];
        $config['dateFormat'] = $_POST["dateFormat"];
        $config['startCaseHideProcessInf'] = ((isset( $_POST["hideProcessInf"] )) ? true : false);
        $config['casesListDateFormat'] = $_POST["casesListDateFormat"];
        $config['casesListRowNumber'] = intval( $_POST["casesListRowNumber"] );
        $config['casesListRefreshTime'] = intval( $_POST["txtCasesRefreshTime"]);

        $conf->aConfig = $config;
        $conf->saveConfig( "ENVIRONMENT_SETTINGS", "" );

        $response = new stdclass();
        $response->success = true;
        $response->msg = G::LoadTranslation( "ID_SAVED_SUCCESSFULLY" );

        echo G::json_encode( $response );
        break;
}

