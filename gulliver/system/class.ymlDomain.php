<?php
/**
 * class.ymlDomain.php
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
 *
 * @package gulliver.system
 *
 */
require_once (PATH_THIRDPARTY . 'lime/yaml.class.php');

/**
 * ymlDomain class
 *
 * @copyright (C) 2002 by Colosa Development Team.
 * @package gulliver.system
 */

class ymlDomain
{
    public $global;

    /**
     * function ymlDomain
     *
     * @access public
     * @return void
     */
    public function ymlDomain ()
    {
        $this->global = sfYAML::Load( PATH_FIXTURES . 'domain.yml' );
    }

    /**
     * function addDomain
     *
     * @access public
     * @param string $domainName
     * @return boolean
     */
    public function addDomain ($domainName)
    {
        $keys = $this->name2keys( $domainName );
        $currDomain = & $this->global;
        $i = 0;
        for ($i = 0; $i < count( $keys ); $i ++) {
            if (is_array( $currDomain )) {
                if (! isset( $currDomain[$keys[$i]] )) {
                    $currDomain[$keys[$i]] = array ();
                }
                $currDomain = & $currDomain[$keys[$i]];
            } else {
                trigger_error( "Operation not possible: Subdomain is not present $domainName", E_USER_ERROR );
                return false;
            }
        }
        return true;
    }

    /**
     * function addDomainValue
     *
     * @access public
     * @param string $domainName
     * @param string $value
     * @return boolean
     */
    public function addDomainValue ($domainName, $value)
    {
        $keys = $this->name2keys( $domainName );
        $currDomain = & $this->global;
        $i = 0;
        for ($i = 0; $i < count( $keys ); $i ++) {
            if (is_array( $currDomain )) {
                $currDomain = & $currDomain[$keys[$i]];
            } else {
                trigger_error( "Operation not possible: Subdomain is not present $domainName", E_USER_ERROR );
                return false;
            }
        }
        $currDomain[] = $value;
        return true;
    }

    /**
     * function exists
     *
     * @access public
     * @param string $domainName
     * @return boolean
     */
    public function exists ($domainName)
    {
        $keys = $this->name2keys( $domainName );
        $currDomain = & $this->global;
        $i = 0;
        for ($i = 0; $i < count( $keys ); $i ++) {
            if (is_array( $currDomain ) && isset( $currDomain[$keys[$i]] )) {
                $currDomain = & $currDomain[$keys[$i]];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * function get
     *
     * @access public
     * @param string $resource
     * @return array
     */
    public function get ($resource)
    {
        if (is_array( $result = $this->load( $resource ) )) {
            //Get one value per each $item
            //Ex. *.first.name.es => Returns an array with all of the defined firstNames.
            //    first.name.es => Returns an array with one firstName.
            //    *.name.es => Returns an array with one value per each sub domain of name.es.
            //                  For example: if name.es has the subdomains:
            //                  first.name.es and last.name.es, it returns an array of
            //                  two elements: one firstName and one lastName.
            foreach ($result as $key => $item) {
                if (is_array( $item )) {
                    $subResult = $this->plainArray( $item );
                    $result[$key] = $subResult[array_rand( $subResult, 1 )];
                }
            }
            return $result;
        } else {
            return array ('');
        }
    }

    /**
     * function name2keys
     *
     * @access public
     * @param string $resource
     * @return array
     */
    public function name2keys ($resource)
    {
        if (strpos( $resource, '.' ) !== false) {
            $revKeys = explode( '.', $resource );
            $keys = array ();
            for ($i = count( $revKeys ) - 1; $i >= 0; $i --) {
                $keys[] = $revKeys[$i];
            }
        } elseif (strpos( $resource, '/' ) !== false) {
            $rootKeys = explode( '/', $resource );
            unset( $rootKeys[0] );
            $keys = array_values( $rootKeys );
        } else {
            $keys = array ($resource );
        }
        return $keys;
    }

    /**
     * function load
     *
     * @access public
     * @param string $resource
     * @return array
     */
    public function load ($resource)
    {
        $keys = $this->name2keys( $resource );
        //Find in global variable
        if (count( $this->getNode( $keys[0], $this->global ) ) > 0) {
            return $this->find( $keys, $this->global );
        } else {
            if (file_exists( PATH_FIXTURES . $keys[0] . '.yml' )) {
                $local = sfYAML::Load( PATH_FIXTURES . $keys[0] . '.yml' );
                unset( $keys[0] );
                $keys = array_values( $keys );
                return $this->find( $keys, $local );
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * function find
     *
     * @access public
     * @param string $nodesKey
     * @param string $where
     * @return array
     */
    public function find ($nodesKey, $where)
    {
        if (count( $nodesKey ) == 1) {
            return $this->getNode( $nodesKey[0], $where );
        } elseif (count( $nodesKey ) > 1) {
            $routes = $this->getNode( $nodesKey[0], $where );
            $result = array ();
            unset( $nodesKey[0] );
            $nodesKey = array_values( $nodesKey );
            foreach ($routes as $route) {
                if (is_array( $route )) {
                    $subResult = $this->find( $nodesKey, $route );
                    $this->arrayAppend( $result, $subResult );
                } else {
                    $result[] = $route;
                }
            }
            return $result;
        } else {
            return array ();
        }
    }

    /**
     * function getNode
     *
     * @access public
     * @param string $nodesKey
     * @param string $from
     * @return array
     */
    public function getNode ($nodeKey, $from)
    {
        if ($nodeKey === '*') {
            return array_values( $from );
        } elseif (array_key_exists( $nodeKey, $from )) {
            return array ($from[$nodeKey] );
        } else {
            return array ();
        }
    }

    /**
     * function plainArray
     *
     * @access public
     * @param array $array
     * @return array
     */
    public function plainArray ($array)
    {
        $result = array ();
        foreach ($array as $item) {
            if (is_array( $item )) {
                $appResult = $this->plainArray( $item );
                $this->arrayAppend( $result, $appResult );
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * function arrayAppend
     *
     * @access public
     * @param string $to
     * @param string $appendFrom
     * @return void
     */
    public function arrayAppend (&$to, $appendFrom)
    {
        foreach ($appendFrom as $appendItem) {
            $to[] = $appendItem;
        }
    }
}

