<?php
/**
 *
 * @author Erik A.O. <erik@colosa.com>
 * @date Sept 13th, 2010
 *
 */

G::LoadClass( "configuration" );

$request = isset( $_POST["request"] ) ? $_POST["request"] : (isset( $_GET["request"] ) ? $_GET["request"] : null);

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
        ;
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

        $conf->aConfig = array ("format" => $_POST["userFormat"],"dateFormat" => $_POST["dateFormat"],"startCaseHideProcessInf" => ((isset( $_POST["hideProcessInf"] )) ? true : false),"casesListDateFormat" => $_POST["casesListDateFormat"],"casesListRowNumber" => intval( $_POST["casesListRowNumber"] ),"casesListRefreshTime" => intval( $_POST["txtCasesRefreshTime"] )
        );

        $conf->saveConfig( "ENVIRONMENT_SETTINGS", "" );

        $response = new stdclass();
        $response->success = true;
        $response->msg = G::LoadTranslation( "ID_SAVED_SUCCESSFULLY" );

        echo G::json_encode( $response );
        break;
}

