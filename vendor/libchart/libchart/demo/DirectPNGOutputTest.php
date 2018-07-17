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
	 * Direct PNG output demonstration (image not saved to disk)
	 *
	 */

	include "../vendor/autoload.php";

	header("Content-type: image/png");

	$chart = new \Libchart\View\Chart\PieChart(500, 300);

	$dataSet = new \Libchart\Model\XYDataSet();
	$dataSet->addPoint(new \Libchart\Model\Point("Bleu d'Auvergne", 50));
	$dataSet->addPoint(new \Libchart\Model\Point("Tomme de Savoie", 75));
	$dataSet->addPoint(new \Libchart\Model\Point("Crottin de Chavignol", 30));
	$chart->setDataSet($dataSet);

	$chart->setTitle("Preferred Cheese");
	$chart->render();
?>