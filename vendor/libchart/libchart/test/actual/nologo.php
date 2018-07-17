<?php
    require_once '../common.php';
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\VerticalBarChart(500, 250);

    $chart->getPlot()->setLogoFilename(null);
    
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Jan 2005", 273));
    $dataSet->addPoint(new \Libchart\Model\Point("Feb 2005", 321));
    $dataSet->addPoint(new \Libchart\Model\Point("March 2005", 442));
    $dataSet->addPoint(new \Libchart\Model\Point("April 2005", 711));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("Monthly usage for www.example.com");
    $chart->render();
?>