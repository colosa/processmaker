<?php
    require_once '../common.php';

    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\LineChart();

#    $chart->addPoint(new \Libchart\Model\Point("06-01", 0));
#    $chart->addPoint(new \Libchart\Model\Point("06-02", 10));

    $chart->setTitle("Sales for 2006");
    $chart->render();
?>