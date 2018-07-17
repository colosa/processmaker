<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +--------------------------------------------------------------------------+
// | Net_LDAP                                                                 |
// +--------------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                    |
// +--------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU Lesser General Public               |
// | License as published by the Free Software Foundation; either             |
// | version 2.1 of the License, or (at your option) any later version.       |
// |                                                                          |
// | This library is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU        |
// | Lesser General Public License for more details.                          |
// |                                                                          |
// | You should have received a copy of the GNU Lesser General Public         |
// | License along with this library; if not, write to the Free Software      |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA |
// +--------------------------------------------------------------------------+
// | Authors: Jan Wagner                                                      |
// +--------------------------------------------------------------------------+
//
// $Id: Util.php 4831 2006-02-06 09:59:09Z nbm $
  
/**
 * Utility Class for Net_LDAP
 *
 * @package Net_LDAP
 * @author Jan Wagner <wagner@netsols.de>
 * @version $Revision: 4831 $
 */
class Net_LDAP_Util extends PEAR 
{
    /**
     * Reference to LDAP object
     *
     * @access private
     * @var object Net_LDAP
     */
    var $_ldap = null;
    
    /**
     * Net_LDAP_Schema object
     *
     * @access private
     * @var object Net_LDAP_Schema
     */
    var $_schema = null;
    
    /**
     * Constructur
     *
     * Takes an LDAP object by reference and saves it. Then the schema will be fetched.
     *
     * @access public
     * @param object Net_LDAP
     */
    function Net_LDAP_Util(&$ldap)
    {
        if (is_object($ldap) && (strtolower(get_class($ldap)) == 'net_ldap')) {
            $this->_ldap = $ldap;
            $this->_schema = $this->_ldap->schema();
            if (Net_LDAP::isError($this->_schema)) $this->_schema = null;
        }
    }
    
    /**
     * Encodes given attributes to UTF8 if needed
     *
     * This function takes attributes in an array and then checks against the schema if they need
     * UTF8 encoding. If that is so, they will be encoded. An encoded array will be returned and
     * can be used for adding or modifying.
     *
     * @access public
     * @param array Array of attributes
     * @return array Array of UTF8 encoded attributes
     */
    function utf8Encode($attributes)
    {
        return $this->_utf8($attributes, 'utf8_encode');
    }
    
    /**
     * Decodes the given attribute values
     *
     * @access public
     * @param array Array of attributes
     * @return array Array with decoded attribute values
     */
    function utf8Decode($attributes)
    {
        return $this->_utf8($attributes, 'utf8_decode');
    }
    
    /**
     * Encodes or decodes attribute values if needed
     *
     * @access private
     * @param array Array of attributes
     * @param array Function to apply to attribute values
     * @return array Array of attributes with function applied to values
     */
    function _utf8($attributes, $function)
    {
        if (!$this->_ldap || !$this->_schema || !function_exists($function)) {
            return $attributes;
        }
        if (is_array($attributes) && count($attributes) > 0) {
            foreach( $attributes as $k => $v ) {
                $attr = $this->_schema->get('attribute', $k);
                if (Net_LDAP::isError($attr)) {
                    continue;
                }                
                if (false !== strpos($attr['syntax'], '1.3.6.1.4.1.1466.115.121.1.15')) {                    
                    if (is_array($v)) {
                        foreach ($v as $ak => $av ) {
                            $v[$ak] = call_user_func($function, $av );
                        }
                    } else {
                        $v = call_user_func($function, $v);
                    }
                }
                $attributes[$k] = $v;
            }
        }
        return $attributes;    
    }    
}

?>
