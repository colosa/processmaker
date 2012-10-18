<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['eid'] )) {
        //Getting available Steps Criteria that have been not selected for a particular task
        require_once "classes/model/Event.php";
        $oEvent = new Event();
        $oData = $oEvent->load( $_GET['eid'] );
        $sch_uid = $oData['EVN_ACTION'];

        if ($sch_uid != '') {
            $rows = $oProcessMap->caseNewSchedulerList( $sch_uid );
            if ($rows['SCH_OPTION'] == '3') {
                $sch_start_day = explode( '|', $rows['SCH_START_DAY'] );
                $count = count( $sch_start_day );
                switch ($count) {
                    case 1:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        break;
                    case 2:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        $rows['SCH_START_DAY_OPT_2_WEEKS'] = $sch_start_day[1];
                        break;
                    case 3:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        $rows['SCH_START_DAY_OPT_2_WEEKS'] = $sch_start_day[1];
                        $rows['SCH_START_DAY_OPT_2_DAYS_WEEK'] = $sch_start_day[2];
                        break;
                }

            }

            if ($rows['SCH_START_DATE'] != '') {
                $sch_str_dt = explode( ' ', $rows['SCH_START_DATE'] );
                $rows['SCH_START_DATE'] = $sch_str_dt[0];
            }
            if ($rows['SCH_END_DATE'] != '') {
                $sch_str_dt = explode( ' ', $rows['SCH_END_DATE'] );
                $rows['SCH_END_DATE'] = $sch_str_dt[0];
            }

            $result = G::json_encode( $rows );
            $result = str_replace( "\\/", "/", '{success:true,data:' . $result . '}' ); // unescape the slashes
        } else {
            $result = '{failure:true}'; // unescape the slashes
        }
        echo $result;
    }
    //print G::json_encode( $result ) ;
} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

