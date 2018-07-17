<?php
    require_once '../common.php';
    
    class ThousandLabelGenerator implements \Libchart\View\Label\LabelGenerator {
        function generateLabel($value) {
            return ((int) ($value / 1000)) . "k";
        }
    }
    
    header("Content-type: image/png");
    
    $chart = new \Libchart\View\Chart\VerticalBarChart(500, 250);
     
    $dataSet = new \Libchart\Model\XYDataSet();
    $dataSet->addPoint(new \Libchart\Model\Point("Jan 2005", 27300));
    $dataSet->addPoint(new \Libchart\Model\Point("Feb 2005", 32100));
    $dataSet->addPoint(new \Libchart\Model\Point("March 2005", 44200));
    $dataSet->addPoint(new \Libchart\Model\Point("April 2005", 71100));
    $chart->setDataSet($dataSet);
    
    $chart->setTitle("Monthly usage for www.example.com");
    $chart->getPlot()->setLabelGenerator(new ThousandLabelGenerator());
    $chart->render();
?>