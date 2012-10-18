<?php
if (isset( $_SESSION['TRIGGER_DEBUG']['info'] )) {
    $aTriggers = $_SESSION['TRIGGER_DEBUG']['info'];
} else {
    $aTriggers[0] = $_SESSION['TRIGGER_DEBUG'];
}
//print_r($aTriggers);die;
$triggersList = Array ();

$i = 0;
foreach ($aTriggers as $aTrigger) {

    if ($aTrigger['NUM_TRIGGERS'] != 0) {

        foreach ($aTrigger['TRIGGERS_NAMES'] as $index => $name) {

            $triggersList[$i]['name'] = $name;
            $triggersList[$i]['execution_time'] = strtolower( $aTrigger['TIME'] );
            //$t_code = $aTrigger['TRIGGERS_VALUES'][$index]['TRI_WEBBOT'];
            //$t_code = str_replace('"', '\'',$t_code);
            //$t_code = addslashes($t_code);
            //$t_code = Only1br($t_code);
            //highlighting the trigger code using the geshi third party library
            G::LoadThirdParty( 'geshi', 'geshi' );
            $geshi = new GeSHi( $aTrigger['TRIGGERS_VALUES'][$index]['TRI_WEBBOT'], 'php' );
            $geshi->enable_line_numbers( GESHI_FANCY_LINE_NUMBERS, 2 );
            $geshi->set_line_style( 'background: #f0f0f0;' );

            $triggersList[$i]['code'] = $geshi->parse_code(); //$aTrigger['TRIGGERS_VALUES'][$index]['TRI_WEBBOT'];
            $i ++;
        }
    } else {

    }
}

//print_r($_SESSION['TRIGGER_DEBUG']['ERRORS']); die;
$DEBUG_ERRORS = array_unique( $_SESSION['TRIGGER_DEBUG']['ERRORS'] );

foreach ($DEBUG_ERRORS as $error) {
    if (isset( $error['ERROR'] ) and $error['ERROR'] != '') {
        $triggersList[$i]['name'] = 'Error';
        $triggersList[$i]['execution_time'] = 'error';
        $triggersList[$i]['code'] = $error['ERROR'];
        $i ++;
    }

    if (isset( $error['FATAL'] ) and $error['FATAL'] != '') {
        $error['FATAL'] = str_replace( "<br />", "\n", $error['FATAL'] );
        $tmp = explode( "\n", $error['FATAL'] );
        $triggersList[$i]['name'] = isset( $tmp[0] ) ? $tmp[0] : 'Fatal Error in trigger';
        $triggersList[$i]['execution_time'] = 'Fatal error';
        $triggersList[$i]['code'] = $error['FATAL'];
        $i ++;
    }
}

/*echo '{total:5, data:[
      {name:"trigger1", execution_time:"after"},
      {name:"trigger2", execution_time:"before"},
      {name:"trigger13", execution_time:"before"},
      ]}';

 */
$triggersRet->total = count( $triggersList );
$triggersRet->data = $triggersList;
echo G::json_encode( $triggersRet );

