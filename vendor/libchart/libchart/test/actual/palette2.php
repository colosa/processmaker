<?php
    require_once '../common.php';
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\HorizontalBarChart(500, 250);
    
    $chart->getConfig()->setUseMultipleColor(true);
    $chart->getPlot()->getPalette()->setBarColor(array(
        new \Libchart\View\Color\Color(255, 0, 0),
        new \Libchart\View\Color\Color(44, 70, 181),
        new \Libchart\View\Color\Color(126, 209, 59),
        new \Libchart\View\Color\Color(247, 150, 71)
    ));
    
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Jan 2005", 273));
    $dataSet->addPoint(new \Libchart\Model\Point("Feb 2005", 321));
    $dataSet->addPoint(new \Libchart\Model\Point("Mar 2005", 442));
    $dataSet->addPoint(new \Libchart\Model\Point("Apr 2005", 711));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("Monthly usage for www.example.com");
    $chart->render();
?>
