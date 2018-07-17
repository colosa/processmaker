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
// | Authors: Tarjej Huse                                                     |
// +--------------------------------------------------------------------------+
//
// $Id: Entry.php 4831 2006-02-06 09:59:09Z nbm $

/**
 * This class represents an LDAP entry
 *
 * @package Net_LDAP
 * @author Tarjei Huse
 * @version $Revision: 4831 $
 */
class Net_LDAP_Entry extends PEAR
{
    /**#@+
     * Array of the attributes
     *
     * @access private
     * @var array
     */
    var $_attrs = array();
    
    /**
     * Array of attributes to be deleted upon update()
     */    
    var $_delAttrs = array();

    /**
     * Array of attributes to be modified upon update()
     */    
    var $_modAttrs = array();

    /**
     * Array of attributes to be added upon update()
     */        
    var $_addAttrs = array();
    /**#@-*/
    
    /**
     * The distinguished name of the entry
     * 
     * @access private
     * @var string
     */
    var $_dn = '';
    
    /**
     * LDAP resource link
     * 
     * @access private
     * @var resource
     */
    var $_link = null;
    
    /**
     * Value of old DN if DN has changed
     *
     * @access private
     * @var string
     */
    var $_olddn = '';

    /**#@+
     * Array of errors for debugging class
     *
     * @access private
     */
    var $_error = array();

    /**
     * updatechecks
     */
    var $updateCheck = array('newdn'    => false,
                             'modify'   => false,
                             'newEntry' => true
                             ); // since the entry is not changed before the update();

    /**
     * Net_LDAP_Schema object TO BE REMOVED
     */                             
    var $_schema;
    /**#@-*/
    
    /** Constructor
     *
     * @param - link - ldap_resource_link, dn = string entry dn, attributes - array entry attributes array.
     * @return - none
     **/
    function Net_LDAP_Entry($link = null, $dn = null, $attributes = null)
    {
        if (!is_null($link)) {
            $this->_link = $link;
        }
        if (!is_null($dn)) {
            $this->_set_dn($dn);
        }
        if (is_array($attributes) && count($attributes) > 0) {
            $this->_set_attributes($attributes);
        } else {
            $this->updateCheck['newEntry'] = true;
        }
    }
    
    /** 
     * Set the reasourcelink to the ldapserver.
     *
     * @access private
     * @param resource LDAP link
     */
    function _set_link(&$link) 
    {
        $this->_link = $link;
    }
    
    /**
     * set the entrys DN 
     *
     * @access private
     * @param string
     */
    function _set_dn($dn)
    {
        $this->_dn = $dn;
    }

    /**
     * sets the internal array of the entrys attributes.
     *
     * @access private
     * @param array
     */
    function _set_attributes($attributes= array())
    {
        $this->_attrs = $attributes;
        // this is the sign that the entry exists in the first place: 
        $this->updateCheck['newEntry'] = false;
    }

   /** 
    * removes [count] entries from the array.
    * 
    * remove all the count elements in the array:
    * Used before ldap_modify, ldap_add
    * 
    * @access private
    * @return array Cleaned array of attributes
    */
    function _clean_entry()
    {
        $attributes = array();
        
        for ($i=0; $i < $this->_attrs['count'] ; $i++) {
        
            $attr = $this->_attrs[$i];
        
            if ($this->_attrs[$attr]['count'] == 1) {
                $attributes[$this->_attrs[$i]] = $this->_attrs[$attr][0];
            } else {
                $attributes[$attr] = $this->_attrs[$attr];
                unset ($attributes[ $attr ]['count']);
            }
        }
         
        return $attributes;

    }

   /**
    * returns an assosiative array of all the attributes in the array
    *
    * attributes -  returns an assosiative array of all the attributes in the array
    * of the form array ('attributename'=>'singelvalue' , 'attribute'=>array('multiple','values'))
    *
    * @param none
    * @return array Array of attributes and values.
    */
    function attributes()
    {
        return $this->_clean_entry();
    }

   /**
    * Add one or more attribute to the entry
    *
    * The values given will be added to the values which already exist for the given attributes.
    * usage:
    * $entry->add ( array('sn'=>'huse',objectclass=>array(top,posixAccount)))
    *
    * @param array Array of attributes
    * @return mixed Net_Ldap_Error if error, else true.
    */
    function add($attr = array())
    {
        if (!isset($this->_attrs['count'])) {
            $this->_attrs['count'] = 0;
        }
        if (!is_array($attr)) {
            return $this->raiseError("Net_LDAP::add : the parameter supplied is not an array, $attr", 1000);   
        }
        /* if you passed an empty array, that is your problem! */
        if (count ($attr)==0) {
            return true;        
        }
        foreach ($attr as $k => $v ) {
            // empty entrys should not be added to the entry.
            if ($v == '') {
                continue;
            }

            if ($this->exists($k)) {
                if (!is_array($this->_attrs[$k])) {
                    return $this->raiseError("Possible malformed array as parameter to Net_LDAP::add().");
                }
                array_push($this->_attrs[$k],$v);
                $this->_attrs[$k]['count']++;
            } else {
                $this->_attrs[$k][0] = $v;
                $this->_attrs[$k]['count'] = 1;
                $this->_attrs[$this->_attrs['count']] = $k;
                $this->_attrs['count']++;
            }
            // Fix for bug #952
            if (empty($this->_addAttrs[$k])) {
                $this->_addAttrs[$k] = array();
            }
            if (false == is_array($v)) {
                $v = array($v);
            }
            foreach ($v as $value) {
                array_push($this->_addAttrs[$k], $value);
            }
        }
        return true;
    }

   /**
    * Set or get the DN for the object
    *
    * If a new dn is supplied, this will move the object when running $obj->update();
    *
    * @param string DN
    */
    function dn($newdn = '')
    {
        if ($newdn == '') {
            return $this->_dn;
        }
      
        $this->_olddn = $this->_dn;
        $this->_dn = $newdn;
        $this->updateCheck['newdn'] = true;
    }
   
   /**
    * check if a certain attribute exists in the directory
    *
    * @param string attribute name.
    * @return boolean
    */
    function exists($attr)
    {
        if (array_key_exists($attr, $this->_attrs)) {
            return true;
        }   
        return false;
    }

   /**
    * get_value get the values for a attribute
    *
    * returns either an array or a string
    * possible values for option:
    *           alloptions - returns an array with the values + a countfield.
    *                       i.e.: array (count=>1, 'sn'=>'huse');
    *           single - returns the, first value in the array as a string.
    *
    * @param $attr string attribute name
    * @param $options array 
    */
    function get_value($attr = '', $options = '')
    {
        if (array_key_exists($attr, $this->_attrs)) {

            if ($options == 'single') {
                if (is_array($this->_attrs[$attr])) {
                    return $this->_attrs[$attr][0];
                } else {
                    return $this->_attrs[$attr];
                }
            }

            $value = $this->_attrs[$attr];
            
            if (!$options == 'alloptions') {
                unset ($value['count']);
            }
            return $value;            
        } else {
            return '';
        }
    }

    /**
     * add/delete/modify attributes
     *
     * this function tries to do all the things that replace(),delete() and add() does on an object.
     * Syntax:
     * array ( 'attribute' => newval, 'delattribute' => '', newattrivute => newval);
     * Note: You cannot use this function to modify parts of an attribute. You must modify the whole attribute.
     * You may call the function many times before running $entry->update();
     *
     * @param array attributes to be modified
     * @return mixed errorObject if failure, true if success.
     */
    function modify($attrs = array()) {
    
        if (!is_array($attrs) || count ($attrs) < 1 ) {
            return $this->raiseError("You did not supply an array as expected",1000);
        }

        foreach ($attrs as $k => $v) {
            // empty values are deleted (ldap v3 handling is in update() )            
            if ($v == '' && $this->exists($k)) {
                $this->_delAttrs[$k] = '';
                continue;
            }
            /* existing attributes are modified*/
            if ($this->exists($k) ) {
                if (is_array($v)) {
                     $this->_modAttrs[$k] = $v;
                } else {
                    $this->_modAttrs[$k][0] = $v;
                } 
            } else {
                /* new ones are created  */
                if (is_array($v) ) {
                    // an empty array is deleted...
                    if (count($v) == 0 ) {
                        $this->_delAttrs[$k] = '';
                    } else {
                        $this->_addAttrs[$k] = $v;
                    }
                } else {
                    // dont't add empty attributes
                    if ($v != null) $this->_addAttrs[$k][0] = $v;
                }
            }        
        }
        return true;
    }

    
   /**
    * replace a certain attributes value
    *
    * replace - replace a certain attributes value
    * example:
    * $entry->replace(array('uid'=>array('tarjei')));
    *
    * @param array attributes to be replaced
    * @return mixed error if failure, true if sucess.
    */
    function replace($attrs = array() )
    {
        foreach ($attrs as $k => $v) {
           
            if ($this->exists($k)) {
                
                if (is_array($v)) {
                    $this->_attrs[$k] = $v;
                    $this->_attrs[$k]['count'] = count($v);
                    $this->_modAttrs[$k] = $v;
                } else {
                    $this->_attrs[$k]['count'] = 1;
                    $this->_attrs[$k][0] = $v;
                    $this->_modAttrs[$k][0] = $v;
                }
            } else {
                return $this->raiseError("Attribute $k does not exist",16); // 16 = no such attribute exists.
            }
        }
        return true;
    }

   /** 
    * delete attributes
    *
    * Use this function to delete certain attributes from an object.
    *
    * @param - array of attributes to be deleted
    * @return mixed Net_Ldap_Error if failure, true if success.
    */
    function delete($attrs = array())
    {
        foreach ($attrs as $k => $v) {
            
            if ($this->exists ($k)) {
                // if v is a null, then remove the whole attribute, else only the value.
                if ($v == '') {
                    unset($this->_attrs[$k]);
                    $this->_delAttrs[$k] = "";                    
                // else we remove only the correct value.
                } else {                
                    for ($i = 0;$i< $this->_attrs[$k]['count'];$i++) {
                        if ($this->_attrs[$k][$i] == $v ) {
                            unset ($this->_attrs[$k][$i]);
                            $this->_delAttrs[$k] = $v;
                            continue;
                        }
                    }                    
                }                
            } else {
                $this->raiseError("You tried to delete a nonexisting attribute!",16);
            }
        }        
        return true;
    }

   /**
    * update the Entry in LDAP
    *
    * After modifying an object, you must run update() to
    * make the updates on the ldap server. Before that, they only exists in the object.
    *
    * @param object Net_LDAP
    * @return mixed Net_LDAP_Error object on failure or true on success
    */
    function update ($ldapObject = null)
    {
        if ($ldapObject == null && $this->_link == null ) {
            $this->raiseError("No link to database");
        }

        if ($ldapObject != null) {
            $this->_link =& $ldapObject->_link;
        }

        //if it's a new 
        if ($this->updateCheck['newdn'] && !$this->updateCheck['newEntry']) {
            if (@ldap_get_option( $this->_link, LDAP_OPT_PROTOCOL_VERSION, $version) && $version != 3) {
                return $this->raiseError("Moving or renaming an dn is only supported in LDAP V3!", 80);
            }

            $newparent = ldap_explode_dn($this->_dn, 0);
            unset($newparent['count']);
            $relativeDn = array_shift($newparent);            
            $newparent = join(',', $newparent);
            
            if (!@ldap_rename($this->_link, $this->_olddn, $relativeDn, $newparent, true)) {
                 return $this->raiseError("DN not renamed: " . ldap_error($this->_link), ldap_errno($this->_link));
            }
        }

        if ($this->updateCheck['newEntry']) {
           //print "<br>"; print_r($this->_clean_entry());

            if (!@ldap_add($this->_link, $this->dn(), $this->_clean_entry()) ) {
                  return $this->raiseError("Entry" . $this->dn() . " not added!" . 
                         ldap_error($this->_link), ldap_errno($this->_link));
            } else {
                return true;
            }
        // update existing entry
        } else {
            $this->_error['first'] = $this->_modAttrs;
            $this->_error['count'] = count($this->_modAttrs); 
            
            // modified attributes
            if (( count($this->_modAttrs)>0) &&
                  !ldap_modify($this->_link, $this->dn(), $this->_modAttrs))
            {
                return $this->raiseError("Entry " . $this->dn() . " not modified(attribs not modified): " .
                                         ldap_error($this->_link),ldap_errno($this->_link));
            }
            
            // attributes to be deleted
            if (( count($this->_delAttrs) > 0 ))
            {
                // in ldap v3 we need to supply the old attribute values for deleting
                if (@ldap_get_option( $this->_link, LDAP_OPT_PROTOCOL_VERSION, $version) && $version == 3) {
                    foreach ( $this->_delAttrs as $k => $v ) {
                        if ( $v == '' && $this->exists($k) ) {
                            $this->_delAttrs[$k] = $this->get_value( $k );
                        }
                    }
                }
                if ( !ldap_mod_del($this->_link, $this->dn(), $this->_delAttrs) ) {
                    return $this->raiseError("Entry " . $this->dn() . " not modified (attributes not deleted): " .
                                             ldap_error($this->_link),ldap_errno($this->_link));
                }
            }
            
            // new attributes
            if ((count($this->_addAttrs)) > 0 && !ldap_modify($this->_link, $this->dn(), $this->_addAttrs)) {
                return $this->raiseError( "Entry " . $this->dn() . " not modified (attributes not added): " .
                                          ldap_error($this->_link),ldap_errno($this->_link));
            }                        
            return true;
        }
    }
}

?>
