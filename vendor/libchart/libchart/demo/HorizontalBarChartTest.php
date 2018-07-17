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
	 * Horizontal bar chart demonstration
	 *
	 */

	include "../vendor/autoload.php";

	$chart = new \Libchart\View\Chart\HorizontalBarChart(600, 170);

	$dataSet = new \Libchart\Model\XYDataSet();
	$dataSet->addPoint(new \Libchart\Model\Point("/wiki/Instant_messenger", 50));
	$dataSet->addPoint(new \Libchart\Model\Point("/wiki/Web_Browser", 75));
	$dataSet->addPoint(new \Libchart\Model\Point("/wiki/World_Wide_Web", 122));
	$chart->setDataSet($dataSet);
	$chart->getPlot()->setGraphPadding(new \Libchart\View\Primitive\Padding(5, 30, 20, 140));

	$chart->setTitle("Most visited pages for www.example.com");
	$chart->render("generated/demo2.png");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Libchart horizontal bars demonstration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
	<img alt="Horizontal bars chart"  src="generated/demo2.png" style="border: 1px solid gray;"/>
</body>
</html>
