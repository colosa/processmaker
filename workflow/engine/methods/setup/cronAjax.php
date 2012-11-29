<?php

function mktimeDate ($date)
{
    $arrayAux = getdate( strtotime( $date ) );

    $mktDate = mktime( $arrayAux["hours"], $arrayAux["minutes"], $arrayAux["seconds"], $arrayAux["mon"], $arrayAux["mday"], $arrayAux["year"] );

    return $mktDate;
}

function cronArraySet ($str, $filter)
{
    $arrayAux = explode( "|", $str );

    $date = "";
    $workspace = "";
    $action = "";
    $status = "";
    $description = trim( $arrayAux[0] );

    if (count( $arrayAux ) > 1) {
        $date = (isset( $arrayAux[0] )) ? trim( $arrayAux[0] ) : "";
        $workspace = (isset( $arrayAux[1] )) ? trim( $arrayAux[1] ) : "";
        $action = (isset( $arrayAux[2] )) ? trim( $arrayAux[2] ) : "";
        $status = (isset( $arrayAux[3] )) ? trim( $arrayAux[3] ) : "";
        $description = (isset( $arrayAux[4] )) ? trim( $arrayAux[4] ) : "";
    }

    $mktDate = (! empty( $date )) ? mktimeDate( $date ) : 0;

    //Filter
    $sw = 1;

    if ($filter["workspace"] != "ALL" && $workspace != $filter["workspace"]) {
        $sw = 0;
    }

    if ($filter["status"] != "ALL") {
        switch ($filter["status"]) {
            case "COMPLETED":
                if ($status != "action") {
                    $sw = 0;
                }
                break;
            case "FAILED":
                if ($status == "action") {
                    $sw = 0;
                }
                break;
        }
    }

    if (! empty( $filter["dateFrom"] ) && $mktDate > 0) {
        if (! (mktimeDate( $filter["dateFrom"] ) <= $mktDate)) {
            $sw = 0;
        }
    }

    if (! empty( $filter["dateTo"] ) && $mktDate > 0) {
        if (! ($mktDate <= mktimeDate( $filter["dateTo"] . " 23:59:59" ))) {
            $sw = 0;
        }
    }

    $arrayData = array ();

    if ($sw == 1) {
        $arrayData = array ("DATE" => $date,"WORKSPACE" => $workspace,"ACTION" => $action,"STATUS" => $status,"DESCRIPTION" => $description
        );
    }

    return $arrayData;
}

function cronDataGet ($filter, $r, $i)
{
    $r = $r + 1; //+ 1, to determine the next page
    $i = $i + 1;

    $arrayData = array ();
    $strAux = null;
    $numRec = 0;
    $cont = 0;

    $file = PATH_DATA . "log" . PATH_SEP . "cron.log";

    if (file_exists( $file )) {
        $fh = fopen( $file, "r" );

        for ($pos = 0; fseek( $fh, $pos, SEEK_END ) !== - 1; $pos --) {
            $char = fgetc( $fh );

            if ($char == "\n") {
                $strAux = trim( $strAux );

                if (! empty( $strAux )) {
                    $arrayAux = cronArraySet( $strAux, $filter );

                    if (count( $arrayAux ) > 0) {
                        $cont = $cont + 1;

                        if ($cont >= $i && count( $arrayData ) + 1 <= $r) {
                            $arrayData[] = $arrayAux;
                            $numRec = $cont;
                        }
                    }
                }

                if (count( $arrayData ) == $r) {
                    break;
                }

                $strAux = null;
                $char = null;
            }

            $strAux = $char . $strAux;
        }

        $strAux = trim( $strAux );

        if (! empty( $strAux )) {
            $arrayAux = cronArraySet( $strAux, $filter );

            if (count( $arrayAux ) > 0) {
                $cont = $cont + 1;

                if ($cont >= $i && count( $arrayData ) + 1 <= $r) {
                    $arrayData[] = $arrayAux;
                    $numRec = $cont;
                }
            }
        }

        fclose( $fh );
    }

    //Delete element
    $r = $r - 1;

    if (count( $arrayData ) > $r) {
        $arrayAux = array_pop( $arrayData );
    }

    return array ($numRec,$arrayData
    );
}

$option = (isset( $_REQUEST["option"] )) ? $_REQUEST["option"] : null;

$response = array ();

switch ($option) {
    case "LST":
        $pageSize = $_REQUEST["pageSize"];
        $workspace = $_REQUEST["workspace"];
        $status = $_REQUEST["status"];
        $dateFrom = $_REQUEST["dateFrom"];
        $dateTo = $_REQUEST["dateTo"];

        $arrayFilter = array ("workspace" => $workspace,"status" => $status,"dateFrom" => str_replace( "T00:00:00", null, $dateFrom ),"dateTo" => str_replace( "T00:00:00", null, $dateTo )
        );

        $limit = isset( $_REQUEST["limit"] ) ? $_REQUEST["limit"] : $pageSize;
        $start = isset( $_REQUEST["start"] ) ? $_REQUEST["start"] : 0;

        list ($numRec, $data) = cronDataGet( $arrayFilter, $limit, $start );

        $response = array ("success" => true,"resultTotal" => $numRec,"resultRoot" => $data
        );
        break;
    case "EMPTY":
        $status = 1;

        try {
            $file = PATH_DATA . "log" . PATH_SEP . "cron.log";

            if (file_exists( $file )) {
                //file_put_contents($file, null);
                unlink( $file );
            }

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response["status"] = "ERROR";
        }
        break;
}

echo G::json_encode( $response );

