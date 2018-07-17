<?php

/**
 * class.helper.php
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * Class Helper
 * @author Erik Amaru Ortiz. <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class Helper
{

    public $content;
    public $gzipEnabled;
    public $minified;
    public $gzipModuleEnabled;
    public $contentType;

    public function __construct()
    {
        $this->content = '';
        $this->gzipEnabled = true;
        $this->minified = true;
        $this->gzipModuleEnabled = false;
        $this->contentType = 'text/html';
    }

    public function addFile($file)
    {
        if (is_file($file)) {
            $this->content .= file_get_contents($file);
        }
    }

    public function addContent($content)
    {
        $this->content = $content;
    }

    public function setContentType($ctype)
    {
        $this->contentType = $ctype;
    }

    public function init()
    {
        header("Content-type: {$this->contentType}");
        header('Pragma: cache');
        header('Cache-Control: public');

        if ($this->gzipEnabled && extension_loaded('zlib')) {
            $this->gzipModuleEnabled = true;
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
    }

    public function minify()
    {
        if ($this->contentType != 'text/css') {

            $this->content = JSMin::minify($this->content);
        }
    }

    public function flush()
    {
        if ($this->minified) {
            $this->minify();
        }
        print($this->content);
        ob_end_flush();
    }

    public function serve($type = null)
    {
        if (isset($type)) {
            $this->setContentType($ctype);
        }
        $this->init();
        $this->flush();
    }
}

/*
function minify($buffer) {
  return G::removeComments($buffer);
}
*/
 