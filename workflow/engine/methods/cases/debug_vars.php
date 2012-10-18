<?php
$request = isset( $_POST['request'] ) ? $_POST['request'] : '';
switch ($request) {
    case 'getRows':

        $fieldname = $_POST['fieldname'];
        G::LoadClass( 'case' );
        $oApp = new Cases();
        $aFields = $oApp->loadCase( $_SESSION['APPLICATION'] );
        $aVars = Array ();
        for ($i = 0; $i < count( $_SESSION['TRIGGER_DEBUG']['DATA'] ); $i ++) {
            $aVars[$_SESSION['TRIGGER_DEBUG']['DATA'][$i]['key']] = $_SESSION['TRIGGER_DEBUG']['DATA'][$i]['value'];
        }

        $aVars = array_merge( $aFields['APP_DATA'], $aVars );

        $field = $aVars[$fieldname];
        $response->headers = Array ();
        $response->columns = Array ();
        $response->rows = Array ();

        $sw = true;
        $j = 0;
        if (is_array( $field )) {
            foreach ($field as $row) {
                if ($sw) {
                    foreach ($row as $key => $value) {
                        $response->headers[] = Array ('name' => $key
                        );
                        $response->columns[] = Array ('header' => $key,'width' => 100,'dataIndex' => $key
                        );
                    }
                    $sw = false;
                }

                $tmp = Array ();
                foreach ($row as $key => $value) {
                    $tmp[] = $value;
                }
                $response->rows[$j ++] = $tmp;
            }
        } else {
            if (is_object( $field )) {
                $response->headers = Array (Array ('name' => 'name'
                ),Array ('name' => 'value'
                )
                );
                $response->columns = Array (Array ('header' => 'Property','width' => 100,'dataIndex' => 'name'
                ),Array ('header' => 'Value','width' => 100,'dataIndex' => 'value'
                )
                );

                foreach ($field as $key => $value) {
                    $response->rows[] = Array ($key,$value
                    );
                }
            }
        }

        echo G::json_encode( $response );
        break;
    default:
        G::LoadClass( 'case' );
        $oApp = new Cases();
        $aFields = $oApp->loadCase( $_SESSION['APPLICATION'] );

        $aVars = Array ();
        for ($i = 0; $i < count( $_SESSION['TRIGGER_DEBUG']['DATA'] ); $i ++) {
            $aVars[$_SESSION['TRIGGER_DEBUG']['DATA'][$i]['key']] = $_SESSION['TRIGGER_DEBUG']['DATA'][$i]['value'];
        }

        $aVars = array_merge( $aFields['APP_DATA'], $aVars );

        if (isset( $_POST['filter'] ) && $_POST['filter'] == 'dyn') {
            $sysVars = array_keys( G::getSystemConstants() );
            $varNames = array_keys( $aVars );
            foreach ($varNames as $var) {
                if (in_array( $var, $sysVars )) {
                    unset( $aVars[$var] );
                }
            }
        }
        if (isset( $_POST['filter'] ) && $_POST['filter'] == 'sys') {
            $aVars = G::getSystemConstants();
        }
        ksort( $aVars );
        $return_object->totalCount = 1;
        foreach ($aVars as $i => $var) {
            if (is_array( $var ) || is_object( $var )) {
                $aVars[$i] = print_r( $var, true );
            }
        }

        $return_object->data[0] = $aVars;

        echo G::json_encode( $return_object );
        break;
}

