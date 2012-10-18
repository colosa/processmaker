<?php
try {
    require_once 'classes/model/Task.php';
    $oTask = new Task();
    $rows = $oTask->load( $_GET['tid'] );

    while (list ($key, $value) = each( $rows )) {

        if ($value == 'TRUE')
            $rows[$key] = true;
        else if ($value == 'FALSE')
            $rows[$key] = false;

        if ($key == 'TAS_TYPE_DAY' && $value == '1')
            $rows[$key] = 'Work Days';
        else if ($key == 'TAS_TYPE_DAY' && $value == '2')
            $rows[$key] = 'Calendar Days';

        if ($key == 'TAS_ASSIGN_TYPE') {
            switch ($value) {
                case 'SELF_SERVICE':
                    $rows[$value] = 'true';
                    break;
                case 'REPORT_TO':
                    $rows[$value] = 'true';
                    break;
                case 'BALANCED':
                    $rows[$value] = 'true';
                    break;
                case 'MANUAL':
                    $rows[$value] = 'true';
                    break;
                case 'EVALUATE':
                    $rows[$value] = 'true';
                    $rows['hideEvaluateField'] = 'false';
                    break;
                case 'STATIC_MI':
                    $rows[$value] = 'true';
                    $rows['hidePartialJoinField'] = 'false';
                    break;
                case 'CANCEL_MI':
                    $rows[$value] = 'true';
                    $rows['hidePartialJoinField'] = 'false';
                    break;
            }
        }
    }

    $tmpData = G::json_encode( $rows );
    $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


    $result = $tmpData;
    echo $result;

}

catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

