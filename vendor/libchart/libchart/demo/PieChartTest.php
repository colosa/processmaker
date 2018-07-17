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
	 * Pie chart demonstration
	 *
	 */

	include "../vendor/autoload.php";

	$chart = new \Libchart\View\Chart\PieChart();

	$dataSet = new \Libchart\Model\XYDataSet();
	$dataSet->addPoint(new \Libchart\Model\Point("Mozilla Firefox (80)", 80));
	$dataSet->addPoint(new \Libchart\Model\Point("Konqueror (75)", 75));
	$dataSet->addPoint(new \Libchart\Model\Point("Opera (50)", 50));
	$dataSet->addPoint(new \Libchart\Model\Point("Safari (37)", 37));
	$dataSet->addPoint(new \Libchart\Model\Point("Dillo (37)", 37));
	$dataSet->addPoint(new \Libchart\Model\Point("Other (72)", 70));
	$chart->setDataSet($dataSet);

	$chart->setTitle("User agents for www.example.com");
	$chart->render("generated/demo3.png");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Libchart pie chart demonstration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
	<img alt="Pie chart"  src="generated/demo3.png" style="border: 1px solid gray;"/>
</body>
</html>
