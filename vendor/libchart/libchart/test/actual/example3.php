<?php
    require_once '../common.php';
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\PieChart(500, 250);
    
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Mozilla Firefox (80)", 80));
    $dataSet->addPoint(new \Libchart\Model\Point("Konqueror (75)", 75));
    $dataSet->addPoint(new \Libchart\Model\Point("Other (50)", 50));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("User agents for www.example.com");
    $chart->render();
?>