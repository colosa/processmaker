<?php

/**
 * class.objectTemplate.php
 *
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * Class objectTemplate
 *
 * @package gulliver.system
 * @access public
 */

class objectTemplate extends Smarty
{

    /**
     * Function objectTemplate
     *
     * @access public
     * @param object $templateFile
     * @return void
     */

    function objectTemplate ($templateFile)
    {
        $this->template_dir = PATH_TPL;
        $this->compile_dir = PATH_SMARTY_C;
        $this->cache_dir = PATH_SMARTY_CACHE;
        $this->config_dir = PATH_THIRDPARTY . 'smarty/configs';
        $this->caching = false;
        $this->templateFile = $templateFile;
    }

    /**
     * Function printObject
     *
     * @access public
     * @param object $object
     * @return string
     */
    function printObject ($object)
    {
        $this->assign( $object );
        return $this->fetch( $this->templateFile );
    }
}

