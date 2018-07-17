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
    
    namespace Libchart\View\Color;

    /**
     * Color palette shared by all chart types.
     *
     * @author Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
     * Created on 25 july 2007
     */
    class Palette {
        // Plot attributes
        public $red;
        public $axisColor;
        public $backgroundColor;
        
        // Specific chart attributes
        public $barColorSet;
        public $lineColorSet;
        public $pieColorSet;
    
        /**
         * Palette constructor.
         */
        public function __construct() {
            $this->red = new \Libchart\View\Color\Color(255, 0, 0);
        
            // Set the colors for the horizontal and vertical axis
            $this->setAxisColor(array(
                    new \Libchart\View\Color\Color(201, 201, 201),
                    new \Libchart\View\Color\Color(158, 158, 158)
            ));

            // Set the colors for the background
            $this->setBackgroundColor(array(
                    new \Libchart\View\Color\Color(242, 242, 242),
                    new \Libchart\View\Color\Color(231, 231, 231),
                    new \Libchart\View\Color\Color(239, 239, 239),
                    new \Libchart\View\Color\Color(253, 253, 253)
            ));
            
            // Set the colors for the bars
            $this->setBarColor(array(
                    new \Libchart\View\Color\Color(42, 71, 181),
                    new \Libchart\View\Color\Color(243, 198, 118),
                    new \Libchart\View\Color\Color(128, 63, 35),
                    new \Libchart\View\Color\Color(195, 45, 28),
                    new \Libchart\View\Color\Color(224, 198, 165),
                    new \Libchart\View\Color\Color(239, 238, 218),
                    new \Libchart\View\Color\Color(40, 72, 59),
                    new \Libchart\View\Color\Color(71, 112, 132),
                    new \Libchart\View\Color\Color(167, 192, 199),
                    new \Libchart\View\Color\Color(218, 233, 202)
            ));

            // Set the colors for the lines
            $this->setLineColor(array(
                    new \Libchart\View\Color\Color(172, 172, 210),
                    new \Libchart\View\Color\Color(2, 78, 0),
                    new \Libchart\View\Color\Color(148, 170, 36),
                    new \Libchart\View\Color\Color(233, 191, 49),
                    new \Libchart\View\Color\Color(240, 127, 41),
                    new \Libchart\View\Color\Color(243, 63, 34),
                    new \Libchart\View\Color\Color(190, 71, 47),
                    new \Libchart\View\Color\Color(135, 81, 60),
                    new \Libchart\View\Color\Color(128, 78, 162),
                    new \Libchart\View\Color\Color(121, 75, 255),
                    new \Libchart\View\Color\Color(142, 165, 250),
                    new \Libchart\View\Color\Color(162, 254, 239),
                    new \Libchart\View\Color\Color(137, 240, 166),
                    new \Libchart\View\Color\Color(104, 221, 71),
                    new \Libchart\View\Color\Color(98, 174, 35),
                    new \Libchart\View\Color\Color(93, 129, 1)
            ));

            // Set the colors for the pie
            $this->setPieColor(array(
                    new \Libchart\View\Color\Color(2, 78, 0),
                    new \Libchart\View\Color\Color(148, 170, 36),
                    new \Libchart\View\Color\Color(233, 191, 49),
                    new \Libchart\View\Color\Color(240, 127, 41),
                    new \Libchart\View\Color\Color(243, 63, 34),
                    new \Libchart\View\Color\Color(190, 71, 47),
                    new \Libchart\View\Color\Color(135, 81, 60),
                    new \Libchart\View\Color\Color(128, 78, 162),
                    new \Libchart\View\Color\Color(121, 75, 255),
                    new \Libchart\View\Color\Color(142, 165, 250),
                    new \Libchart\View\Color\Color(162, 254, 239),
                    new \Libchart\View\Color\Color(137, 240, 166),
                    new \Libchart\View\Color\Color(104, 221, 71),
                    new \Libchart\View\Color\Color(98, 174, 35),
                    new \Libchart\View\Color\Color(93, 129, 1)
            ));
        }
        
        /**
         * Set the colors for the axis.
         *
         * @param colors Array of Color
         */
        public function setAxisColor($colors) {
            $this->axisColor = $colors;
        }
        
        /**
         * Set the colors for the background.
         *
         * @param colors Array of Color
         */
        public function setBackgroundColor($colors) {
            $this->backgroundColor = $colors;
        }
        
        /**
         * Set the colors for the bar charts.
         *
         * @param colors Array of Color
         */
        public function setBarColor($colors) {
            $this->barColorSet = new \Libchart\View\Color\ColorSet($colors, 0.75);
        }
        
        /**
         * Set the colors for the line charts.
         *
         * @param colors Array of Color
         */
        public function setLineColor($colors) {
            $this->lineColorSet = new \Libchart\View\Color\ColorSet($colors, 0.75);
        }
        
        /**
         * Set the colors for the pie charts.
         *
         * @param colors Array of Color
         */
        public function setPieColor($colors) {
            $this->pieColorSet = new \Libchart\View\Color\ColorSet($colors, 0.7);
        }
    }
?>