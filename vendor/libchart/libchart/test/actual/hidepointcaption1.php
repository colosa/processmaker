<?php
    require_once '../common.php';

    header("Content-type: image/png");

    $chart = new \Libchart\View\Chart\PieChart();

    $chart->getConfig()->setShowPointCaption(false);

    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Some part", 20));
    $dataSet->addPoint(new \Libchart\Model\Point("Another part", 35));
    $dataSet->addPoint(new \Libchart\Model\Point("Biggest part", 70));
    $chart->setDataSet($dataSet);

    $chart->setTitle("This is a pie");
    $chart->render();
?>