<?php

/**
 * class.webResource.php
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
/* Web Resource  (v1.0 PHP4)
 * This class permit create PHP/Javascript classes accesible by
 *  HTTPRequest (see skins/grest.js)
 * _Function = don't encode
 * JS_function = javascript function.
 * @example: h2b2x/territoriesAjax.php
 * @dependencies: grest.js *
 */

/**
 *
 * @package gulliver.system
 */
class WebResource
{
    var $_uri;

    /**
     * WebResource
     *
     * @param string $uri
     * @param string $post
     *
     * @return none
     */
    function WebResource ($uri, $post)
    {
        $this->_uri = $uri;
        if (isset( $post['function'] ) && $post['function'] != '') {
            /*Call a function*/
            header( 'Content-Type: text/json' );
            //$parameters=G::json_decode((urldecode($post['parameters']))); //for %AC
            $parameters = G::json_decode( ($post['parameters']) );
            $paramsRef = array ();
            foreach ($parameters as $key => $value) {
                if (is_string( $key )) {
                    $paramsRef[] = "\$parameters['" . addcslashes( $key, '\\\'' ) . "']";
                } else {
                    $paramsRef[] = '$parameters[' . $key . ']';
                }
            }
            
            $paramsRef = implode( ',', $paramsRef );

            $filter = new InputFilter();
            $post['function'] = $filter->validateInput($post['function']);
            $paramsRef = $filter->validateInput($paramsRef);
            
            $res = eval( 'return ($this->' . $post['function'] . '(' . $paramsRef . '));' );
            $res = G::json_encode( $res );
            print ($res) ;
        } else {
            /*Print class definition*/
            $this->_encode();
        }
    }

    /**
     * _encode
     *
     * @return none
     */
    function _encode ()
    {

        $filter = new InputFilter();
        header( 'Content-Type: text/json' );
        $methods = get_class_methods( get_class( $this ) );
        $methods = $filter->xssFilterHard($methods);
        $this->_uri = $filter->xssFilterHard($this->_uri);
        print ('{') ;
        $first = true;
        foreach ($methods as $method) {
            //To avoid PHP version incompatibilities, put the $method name in lowercase
            $method = strtolower( $method );
            $method = $filter->xssFilterHard($method);
            if ((substr( $method, 0, 1 ) === '_') || (strcasecmp( $method, 'WebResource' ) == 0) || (strcasecmp( $method, get_class( $this ) ) == 0)) {
            } elseif (strcasecmp( substr( $method, 0, 3 ), 'js_' ) == 0) {
                if (! $first) {
                    print (',') ;
                }
                $this->{$method}();
                $first = false;
            } else {
                if (! $first) {
                    print (',') ;
                }
                print ($method . ':function(){return __wrCall("' . addslashes( $this->_uri ) . '","' . $method . '",arguments);}') ;
                $first = false;
            }
        }
        print ('}') ;
    }
}
/* end class WebResource */

/*if (! function_exists( 'json_encode' )) {


    function json_encode (&$value)
    {
        $json = new Services_JSON();
        return $json->encode( $value );
    }
}

if (! function_exists( 'json_decode' )) {


    function json_decode (&$value)
    {
        $json = new Services_JSON();
        return $json->decode( $value );
    }
}*/

