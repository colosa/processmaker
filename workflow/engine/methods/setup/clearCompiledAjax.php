<?php
try {
    if (isset( $_POST['javascriptCache'] ) || isset( $_POST['metadataCache'] ) || isset( $_POST['htmlCache'] )) {

        if (isset( $_POST['javascriptCache'] )) {
            G::rm_dir( PATH_C . 'ExtJs' );
            $response->javascript = true;
        }

        if (isset( $_POST['metadataCache'] )) {
            G::rm_dir( PATH_C . 'xmlform' );
            $response->xmlform = true;
        }

        if (isset( $_POST['htmlCache'] )) {
            G::rm_dir( PATH_C . 'smarty' );
            $response->smarty = true;
        }

        $response->success = true;
    } else {
        $response->success = false;
    }
} catch (Exception $e) {
    $response->success = false;
    $response->message = $e->getMessage();
}
echo G::json_encode( $response );

