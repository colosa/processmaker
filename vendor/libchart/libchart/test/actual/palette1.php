<?php
    require_once '../common.php';

    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\PieChart();

    $chart->getPlot()->getPalette()->setPieColor(array(
        new \Libchart\View\Color\Color(255, 0, 0),
        new \Libchart\View\Color\Color(255, 255, 255)
    ));

    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Amanita abrupta", 80));
    $dataSet->addPoint(new \Libchart\Model\Point("Amanita arocheae", 75));
    $dataSet->addPoint(new \Libchart\Model\Point("Clitocybe dealbata", 50));
    $dataSet->addPoint(new \Libchart\Model\Point("Cortinarius rubellus", 70));
    $dataSet->addPoint(new \Libchart\Model\Point("Gyromitra esculenta", 37));
    $dataSet->addPoint(new \Libchart\Model\Point("Lepiota castanea", 37));
    $chart->setDataSet($dataSet);

    $chart->setTitle("Deadly mushrooms");
    $chart->render();
?>