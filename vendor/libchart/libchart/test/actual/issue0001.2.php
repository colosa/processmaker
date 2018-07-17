<?php
    require_once '../common.php';

    header("Content-type: image/png");
    
    $data = array (
        'Windows XP' => '751',
        'Windows Vista' => '342',
        'Linux' => '277',
        'Mac OS X' => '267',
        'Windows 2003' => '65',
        'Windows 2000' => '9',
        'Windows Server 2008' => '8',
        'Windows 7' => '4',
        'SunOS' => '3');
        
    $chart = new \Libchart\View\Chart\PieChart();
    $dataSet = new \Libchart\Model\XYDataSet();
    foreach($data as $key => $value) {
        $dataSet->addPoint(new \Libchart\Model\Point($key, $value));
    }
    $chart->setDataSet($dataSet);
    $chart->render();
?>