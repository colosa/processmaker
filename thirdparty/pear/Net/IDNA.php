<?php

// {{{ license

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */
//
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU Lesser General Public License as       |
// | published by the Free Software Foundation; either version 2.1 of the |
// | License, or (at your option) any later version.                      |
// |                                                                      |
// | This library is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 |
// | USA.                                                                 |
// +----------------------------------------------------------------------+
//

// }}}


/**
 * Encode/decode Internationalized Domain Names.
 * Factory class to get correct implementation either for php4 or php5.
 *
 * @author  Markus Nix <mnix@docuverse.de>
 * @author  Matthias Sommerfeld <mso@phlylabs.de>
 * @package Net
 * @version $Id: IDNA.php,v 1.1 2008/03/22 12:39:37 neufeind Exp $
 */

class Net_IDNA
{
    // {{{ factory
    /**
     * Attempts to return a concrete IDNA instance for either php4 or php5.
     *
     * @param  array  $params   Set of paramaters
     * @return object IDNA      The newly created concrete Log instance, or an
     *                          false on an error.
     * @access public
     */
    function &getInstance($params = array())
    {
        $version   = explode( '.', phpversion() );
        $handler   = ((int)$version[0] > 4) ? 'php5' : 'php4';
        $class     = 'Net_IDNA_' . $handler;
        $classfile = 'Net/IDNA/' . $handler . '.php';

        /*
         * Attempt to include our version of the named class, but don't treat
         * a failure as fatal.  The caller may have already included their own
         * version of the named class.
         */
        @include_once $classfile;

        /* If the class exists, return a new instance of it. */
        if (class_exists($class)) {
            $instance = &new $class($params);
            return $instance;
        }

        return false;
    }
    // }}}

    // {{{ singleton
    /**
     * Attempts to return a concrete IDNA instance for either php4 or php5,
     * only creating a new instance if no IDNA instance with the same
     * parameters currently exists.
     *
     * @param  array  $params   Set of paramaters
     * @return object IDNA      The newly created concrete Log instance, or an
     *                          false on an error.
     * @access public
     */
    function &singleton($params = array())
    {
        static $instances;
        if (!isset($instances)) {
            $instances = array();
        }

        $signature = serialize($params);
        if (!isset($instances[$signature])) {
            $instances[$signature] = &Net_IDNA::getInstance($params);
        }

        return $instances[$signature];
    }
    // }}}
}

?>
