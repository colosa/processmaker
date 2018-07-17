<?php
    require_once '../common.php';

    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\LineChart();

    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("06-01", 0));
    $dataSet->addPoint(new \Libchart\Model\Point("06-02", 0));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("Sales for 2006");
    $chart->render();
?>