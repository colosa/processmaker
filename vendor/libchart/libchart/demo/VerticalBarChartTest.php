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
	
	/*
	 * Vertical bar chart demonstration
	 *
	 */

	include "../vendor/autoload.php";

	$chart = new \Libchart\View\Chart\VerticalBarChart();

	$dataSet = new \Libchart\Model\XYDataSet();
	$dataSet->addPoint(new \Libchart\Model\Point("Jan 2005", 273));
	$dataSet->addPoint(new \Libchart\Model\Point("Feb 2005", 421));
	$dataSet->addPoint(new \Libchart\Model\Point("March 2005", 642));
	$dataSet->addPoint(new \Libchart\Model\Point("April 2005", 800));
	$dataSet->addPoint(new \Libchart\Model\Point("May 2005", 1200));
	$dataSet->addPoint(new \Libchart\Model\Point("June 2005", 1500));
	$dataSet->addPoint(new \Libchart\Model\Point("July 2005", 2600));
	$chart->setDataSet($dataSet);

	$chart->setTitle("Monthly usage for www.example.com");
	$chart->render("generated/demo1.png");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Libchart vertical bars demonstration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
	<img alt="Vertical bars chart" src="generated/demo1.png" style="border: 1px solid gray;"/>
</body>
</html>
