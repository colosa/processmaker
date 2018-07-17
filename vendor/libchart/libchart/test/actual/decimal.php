<?php
    require_once '../common.php';

    header("Content-type: image/png");

    $chart = new \Libchart\View\Chart\VerticalBarChart();

    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Jan 2005", 1));
    $dataSet->addPoint(new \Libchart\Model\Point("Feb 2005", 1));
    $dataSet->addPoint(new \Libchart\Model\Point("March 2005", 1));
    $dataSet->addPoint(new \Libchart\Model\Point("April 2005", 2.25));
    $dataSet->addPoint(new \Libchart\Model\Point("May 2005", 3.14156265));
    $dataSet->addPoint(new \Libchart\Model\Point("June 2005", 2.4));
    $dataSet->addPoint(new \Libchart\Model\Point("July 2005", 1));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("Monthly usage for www.example.com");
    $chart->render();
?>