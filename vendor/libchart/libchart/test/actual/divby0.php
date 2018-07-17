<?php
    require_once '../common.php';
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\PieChart(500, 250);
    
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Mozilla Firefox (0)", 0));
    $dataSet->addPoint(new \Libchart\Model\Point("Konqueror (0)", 0));
    $dataSet->addPoint(new \Libchart\Model\Point("Other (0)", 0));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("User agents for www.example.com");
    $chart->render();
?>