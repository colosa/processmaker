<?php
    require_once '../common.php';
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\PieChart(500, 250);

    $chart->getConfig()->setSortDataPoint(false);
    
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Item 1 (20)", 20));
    $dataSet->addPoint(new \Libchart\Model\Point("Item 2 (0)", 0));
    $dataSet->addPoint(new \Libchart\Model\Point("Item 3 (30)", 30));
    $dataSet->addPoint(new \Libchart\Model\Point("Item 4 (70)", 70));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("This example preserves item order");
    $chart->render();
?>