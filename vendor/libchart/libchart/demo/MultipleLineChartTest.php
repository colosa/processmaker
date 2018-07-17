<?php
	/* Libchart - PHP chart library
	 * Copyright (C) 2005-2011 Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
	 * 
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 * 
	 */
	
	/**
	 * Multiple line chart demonstration.
	 *
	 */

	include "../vendor/autoload.php";

	$chart = new \Libchart\View\Chart\LineChart();

	$serie1 = new \Libchart\Model\XYDataSet();
	$serie1->addPoint(new \Libchart\Model\Point("06-01", 273));
	$serie1->addPoint(new \Libchart\Model\Point("06-02", 421));
	$serie1->addPoint(new \Libchart\Model\Point("06-03", 642));
	$serie1->addPoint(new \Libchart\Model\Point("06-04", 799));
	$serie1->addPoint(new \Libchart\Model\Point("06-05", 1009));
	$serie1->addPoint(new \Libchart\Model\Point("06-06", 1106));
	
	$serie2 = new \Libchart\Model\XYDataSet();
	$serie2->addPoint(new \Libchart\Model\Point("06-01", 280));
	$serie2->addPoint(new \Libchart\Model\Point("06-02", 300));
	$serie2->addPoint(new \Libchart\Model\Point("06-03", 212));
	$serie2->addPoint(new \Libchart\Model\Point("06-04", 542));
	$serie2->addPoint(new \Libchart\Model\Point("06-05", 600));
	$serie2->addPoint(new \Libchart\Model\Point("06-06", 850));
	
	$serie3 = new \Libchart\Model\XYDataSet();
	$serie3->addPoint(new \Libchart\Model\Point("06-01", 180));
	$serie3->addPoint(new \Libchart\Model\Point("06-02", 400));
	$serie3->addPoint(new \Libchart\Model\Point("06-03", 512));
	$serie3->addPoint(new \Libchart\Model\Point("06-04", 642));
	$serie3->addPoint(new \Libchart\Model\Point("06-05", 700));
	$serie3->addPoint(new \Libchart\Model\Point("06-06", 900));
	
	$serie4 = new \Libchart\Model\XYDataSet();
	$serie4->addPoint(new \Libchart\Model\Point("06-01", 280));
	$serie4->addPoint(new \Libchart\Model\Point("06-02", 500));
	$serie4->addPoint(new \Libchart\Model\Point("06-03", 612));
	$serie4->addPoint(new \Libchart\Model\Point("06-04", 742));
	$serie4->addPoint(new \Libchart\Model\Point("06-05", 800));
	$serie4->addPoint(new \Libchart\Model\Point("06-06", 1000));
	
	$serie5 = new \Libchart\Model\XYDataSet();
	$serie5->addPoint(new \Libchart\Model\Point("06-01", 380));
	$serie5->addPoint(new \Libchart\Model\Point("06-02", 600));
	$serie5->addPoint(new \Libchart\Model\Point("06-03", 712));
	$serie5->addPoint(new \Libchart\Model\Point("06-04", 842));
	$serie5->addPoint(new \Libchart\Model\Point("06-05", 900));
	$serie5->addPoint(new \Libchart\Model\Point("06-06", 1200));
	
	$dataSet = new \Libchart\Model\XYSeriesDataSet();
	$dataSet->addSerie("Product 1", $serie1);
	$dataSet->addSerie("Product 2", $serie2);
	$dataSet->addSerie("Product 3", $serie3);
	$dataSet->addSerie("Product 4", $serie4);
	$dataSet->addSerie("Product 5", $serie5);
	$chart->setDataSet($dataSet);

	$chart->setTitle("Sales for 2006");
	$chart->getPlot()->setGraphCaptionRatio(0.62);
	$chart->render("generated/demo6.png");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Libchart line demonstration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
	<img alt="Line chart" src="generated/demo6.png" style="border: 1px solid gray;"/>
</body>
</html>
