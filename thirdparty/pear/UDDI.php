<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */
// +----------------------------------------------------------------------+
// | UDDI: A PHP class library implementing the Universal Description,    |
// | Discovery and Integration API for locating and publishing Web        |
// | Services listings in a UBR (UDDI Business Registry)                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | General Public License for more details.                             |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this library; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, |
// | USA, or visit http://www.gnu.org/copyleft/lesser.html                |
// +----------------------------------------------------------------------+
// | Authors: Jon Stephens & Lee Reynolds (authors of non-PEAR version)   |
// |          Maintainers and PEARifiers:                                 |
// |          Christian Wenz <chw@hauser-wenz.de>                         |
// |          Tobias Hauser <th@hauser-wenz.de>                           |
// +----------------------------------------------------------------------+
//
//    $Id$
/* Original Credits:

  phpUDDI
  A PHP class library implementing the Universal Description,
  Discovery and Integration API for locating and publishing Web
  Services listings in a UBR (UDDI Business Registry).

  Copyright (C) 2002-2004 Lee Reynolds and Jon Stephens

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
  or point your web browser to http://www.gnu.org/licenses/lgpl.html.

  Conceived and developed in conjunction with the
  authors' work for the book "PHP Web Services" from Wrox Press
  Ltd. (ISBN-1861008074)

  Original developers:
  Lee Reynolds lee@annasart.com
  Jon Stephens jon@hiveminds.info

  Useful Links:

  Wrox Press "Programmer To Programmer" Discussion Groups
  Pro PHP: http://p2p.wrox.com/list.asp?list=pro_php
  Pro XML: http://p2p.wrox.com/list.asp?list=xml

  HiveMinds Group
  http://www.hiveminds.info/
  http://forums.hiveminds.info/

  Version History:

  0.1   -- 15 November 2002 -- Basic Proof of concept
  0.2   -- 20 November 2002 -- Base class redefinition to
                include public and private methods
  0.3   -- 25 November 2002 -- All UDDI 2.0 Inquiry APIs implemented
  0.3.1 -- 14 March 2004    -- small bugfixes (Christian); license change to LPGL (Lee, Jon)

  Objective for 1.0 release is complete implementation of the UDDI 2.0 API
  (http://www.uddi.org/pubs/ProgrammersAPI-V2.04-Published-20020719.pdf).

  Objective for 1.5 release is backwards compatibility with UDDI 1.0.

  Objective for 2.0 release is complete implementation of the UDDI 3.0 API
  (http://www.uddi.org/pubs/uddi-v3.00-published-20020719.pdf).

*/

require_once 'PEAR.php';

/**
 * PEAR::UDDI
 * class that implements the UDDI API
 * @link http://www.uddi.org/
 *
 * pearified version of phpUDDI by Stephens & Reynolds
 * 
 * 
 * @category Web Services
 * @package  UDDI
 * @version  0.2.0alpha4
 * @author   Christian Wenz <chw@hauser-wenz.de>
 * @author   Tobias Hauser <th@hauser-wenz.de>
 * @todo     fully implement UDDI 2.0 (and later 1.0, 3.0)
 * @todo     more helper functions (for analyzing the data returned by querying a UBR)


Name:
  UDDI.php - UDDI Registry access library (PEARified version)

Synopsis:
  require_once 'UDDI/UDDI.php';

Currently, PEAR::UDDI supports 1 function call in one UDDI API.

Variables supported:
  $UDDI::_api
  currently 2 values are supported, and they represent 2 of the 6 published APIs by UDDI.org.
  'Inquiry' which represents the search and _query API
  'Publish' which represents the publishing API

  Usage:
  <code>
    $UDDI::_api = 'Inquiry'; // (default)
    $UDDI::_api = 'Publish';
  </code>

  $UDDI::_uddiversion
  This is the API version of the UPI spec you want to use
  Values are either 1, 2, or 3.

  Usage:
  <code>
    $UDDI::_uddiversion = 2;
  </code>

  Default:
    currently, the version default is '1';

  Note: As stated above, we are aiming for a 1.0 release of this library which implements
  the UDDI 2.0 Programming API; we cannot guarantee at this time that any of the API functions
  as implemented here will work correctly (or at all) unless you set the version to 2 as shown
  under 'Usage' immediately above or by setting the version when you call the UDDI class constructor:

  <code>
  $my_uddi = new UDDI('Microsoft', 2);
  </code>

  At a later date we will make this class compatible with UDDI Versions 1.0 and 3.0.

  $UDDI::_regarray
  We currently support 2 test registry entries.  These are 'IBM' and 'Microsoft'.
  We also support 2 API interfaces. These are, as noted above, 'Inquiry' and 'Publish'.

  To add live registry entries, or your own test registry, you must append a multiple index array element
  to $UDDI::_regarray.  The form for this follows:
  <code>
    array('registry name' =>
    array('Inquiry' =>
      array('url' => 'url_for_inquiry',
        'port' => 80),
        'Publish' =>
      array('url' => 'url_for_publish',
        'port' => 443)));
  </code>
  Internally this is accessed as 
  <code>URL = $_regarray['registry_name']['Inquiry']['url']</code>
  , and
  <code>port = $_regarray['registry_name']['Inquiry']['port']</code>

  PLEASE NOTE: You're adding elements to this array, instead of overwriting old ones.

  Usage:
  <code>
    $UDDI::_regarray = array('private' =>
              array('Inquiry' =>
              array('url' =>'url_for_inquiry',
                  'port' => 80),
                  'Publish' =>
                      array('url' => 'url_for_publishing',
                          'port' => 443)));
  </code>

  $UDDI::_xmlns
  You can modify the XML namespace by reassigning a value to this

  Usage:
  <code>
    $UDDI::_xmlns = 'new_ns_definition';
  </code>

  Default:
     'urn:uddi-org:api'

  $UDDI::_debug
  Turns on debugging by echoing HTTP headers and UDDI queries.

  Usage:
  <code>
    $UDDI::_debug = true;
  </code>

  Default:
    false

  $UDDI::_transmit
  Turns on _posting of UDDI message to UBR.

  Usage:
  <code>
    $UDDI::_transmit = false;
  </code>

  Default:
    true;


EXAMPLE:

This queries IBM's UDDI Registry for the first 50 businesses whose names include
the word "Acme", matches sorted first in ascending order by name, then in descending
order by date last updated. The raw XML that's returned is escaped and echoed to the page.

<code>
$my_uddi = new UDDI('IBM', 1);
$result = htmlspecialchars($my_uddi->find_business(array('name' => '%Acme%', 'maxRows' => 50, 'findQualifiers' => 'sortByNameAsc,sortByDateAsc')));
echo "<pre>$result</pre>";
</code>

*/


// {{{ constants
/**
 * version of corresponding phpUDDI version
 * still included so that moving from phpUDDI to PEAR::UDDI is easier 
 */

define('UDDI_PHP_LIB_VERSION', '0.3.1p'); //suffix p = PEAR :-)

// }}}
// {{{ UDDI

/**
 * UDDI
 *
 * class that implements the UDDI API
 * 
 * @package  UDDI
 * @author   Christian Wenz <chw@hauser-wenz.de>
 * @author   Tobias Hauser <th@hauser-wenz.de>
*/
class UDDI extends PEAR
{
    // {{{ properties

    /**
     * version of package
     * @var string $_version
     */
     var $_version = '0.2.0alpha4';

    /**
     * list of known registries
     * @var array $regarray
     */

    var $_regarray =
        array('IBM' =>
            array('Inquiry'  =>
                array('url'  => 'www-3.ibm.com/services/uddi/testregistry/inquiryapi',
                    'port' => 80),
            'Publish' =>
                array('url' => 'https://www-3.ibm.com/services/uddi/testregistry/protect/publishapi',
                    'port' => 443)),

            'Microsoft' =>
                array('Inquiry' =>
                    array('url' => 'test.uddi.microsoft.com/inquire',
                        'port' => 80),
            'Publish' =>
                array('url' => 'https://test.uddi.microsoft.com/publish',
                    'port' => 443)));


    /**
     * which API to use (Inquiry/Publish)
     * @var string $_api
     */
    var $_api = 'Inquiry';

    /**
     * used XML namespace
     * @var string $_xmlns
     */
    var $_xmlns = 'urn:uddi-org:api';

    /**
     * used UDDI version
     * @var string $_uddiversion
     */
    var $_uddiversion  = 1;

    /**
     * used XML generic version
     * @var string $_generic
     */
    var $_generic;

    /**
     * debug mode
     * @var boolean $_debug
     */
    var $_debug    = false;

    /**
     * Turns on _posting of UDDI message to UBR
     * @var boolean $_transmit
     */
    var $_transmit = true;

    /**
     * Host to use
     * @var string $_host
     */
    var $_host;

    /**
     * URL to use
     * @var string $_url
     */
    var $_url;

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @access   public
     * @param    string   $registry    name of registry to use
     * @param    integer  $version     UDDI version to use 
     */
    function UDDI($registry = 'IBM', $version = 1)
    {
        $this->splitUrl($registry, $version);
    }

    // }}}
    // {{{ splitUrl()

    /**
     * retrieves information from URL and sets params
     *
     * @access   public
     * @param    string   $registry    name of registry to use
     * @param    integer  $version     UDDI version to use 
     */
    function splitUrl($registry, $version)
    {
        $this->_registry = $registry;
        $reg = $this->_regarray[$this->_registry][$this->_api]['url'];
        $reg = str_replace('http://', '', $reg);
        $pos = strpos($reg, '/')
            or PEAR::raiseError("Invalid registry (POS = $pos, URL = '$reg')\n");
        $this->_host = substr($reg, 0, $pos);
        $this->_url = substr($reg, $pos, strlen($reg) - 1);

        if ($version > 1) {
            $this->_xmlns .= "_v$version";
        }

        $this->_generic = "$version.0";
    }

    // }}}
    // {{{ post()

    /**
     * assembles HTTP headers and posts these and the UDDI message to the UBR
     *
     * @access   public
     * @param    string  $message    the UDDI message to send
     * @return   string  $data       data returned from the UBR
     */
    function post($message)
    {
        $msg_length = strlen($message);
        $php_version = phpversion();
        $date = str_replace('+0000', 'GMT', gmdate('r', time()));

        $header = '';
        $header .= "POST $this->_url HTTP/1.0\r\n";
        $header .= "Date: $date\r\n";
        $header .= "Content-Type: text/xml; charset=UTF-8\r\n";
        $header .= "User-agent: PEAR::UDDI/$this->_version php/$php_version\r\n";
        $header .= "Host: $this->_host\r\n";
        $header .= "SOAPAction: \"\"\r\n";
        $header .= "Content-Length: $msg_length\r\n\r\n";

        //  echoes HTTP header and UDDI message to page if true
        if ($this->_debug) {
            echo '<pre>' . htmlspecialchars(str_replace('><', ">\n<", $header . $message)) . '</pre>';
        }

        //  sends header and message to UBR if true
        if ($this->_transmit) {
            $port = $this->_regarray[$this->_registry][$this->_api]['port'];
            $fp = fsockopen($this->_host, $port, $errno, $errstr, 5)
                or PEAR::raiseError("Couldn't connect to server at $this->_host:$port.<br />Error #$errno: $errstr.");

            fputs($fp, $header)
                or PEAR::raiseError('Couldn\'t send HTTP headers.');
            fputs($fp, "$message\n\n")
                or PEAR::raiseError('Couldn\'t send UDDI message.');

            $response = '';
            while (!feof($fp)) {
                $response .= fgets($fp, 1024)
                    or PEAR::raiseError('No response from server.');
            }
            fclose($fp)
                or PEAR::raiseError('Warning: Couldn\'t close HTTP connection.');

            $response = str_replace('><', ">\n<", $response);
            return $response;
        }
    }

    // }}}
    // {{{ query()

    /**
     * sends and UDDI query to the registry server
     *
     * @access   public
     * @param    string  $method     the UDDI message to send
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function query($method, $params)
    {
        $message = $this->assemble($method, $params);

        return $this->post($message);
    }

    // }}}
    // {{{ assemble()

    /**
     * generate XML creating the UDDI query
     *
     * @access   public
     * @param    string  $method     the UDDI message to send
     * @param    array   $params     parameters for the query
     * @return   string  $data       the desired XML query code
     */
    function assemble($method, $params)
    {
        $head = '<?xml version="1.0" encoding="utf-8"?>';
        $head .= '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">';
        $head .= '<Body>';

        $end = "</$method></Body></Envelope>";

        $attrib = '';
        $element = '';

        if (isset($params['discoveryURLs']) && ($params['discoveryURLs'] != '')) {
            $element .= '<discoveryURLs>' . $params['discoveryURLs'] . '</discoveryURLs>';
        }

        if (isset($params['bindingKey']) && ($params['bindingKey'] != '')) {
            $element .= '<bindingKey>' . $params['bindingKey'] . '</bindingKey>';
        }

        if (isset($params['businessKey']) && ($params['businessKey'] != '')) {
            $element .= '<businessKey>' . $params['businessKey'] . '</businessKey>';
        }

        if (isset($params['serviceKey']) && ($params['serviceKey'] != '')) {
    
            if ($method == 'find_binding') {
                $attrib .= ' serviceKey="' . $params['serviceKey'] . '"';
            }
            if ($method == 'get_serviceDetail') {
                $element .= '<serviceKey>' . $params['serviceKey'] . '</serviceKey>';
            }
        }

        if (isset($params['tModelKey']) && ($params['tModelKey'] != '')) {
            $element .= '<tModelKey>uuid:' . $params['tModelKey'] . '</tModelKey>';
        }

        if (isset($params['findQualifiers']) && ($params['findQualifiers'] != '')) {
            $element .= '<findQualifiers>';
            $findQualifiers = explode(',', $params['findQualifiers']);
            for ($i = 0; $i < count($findQualifiers); $i++) {
                $element .= '<findQualifier>' . $findQualifiers[$i] . '</findQualifier>';
            }
            $element .= '</findQualifiers>';
        }

        if (isset($params['tModelBag']) && ($params['tModelBag'] != '')) {
            $tModelKey = explode(',', $params['tModelBag']);
            $element .= '<tModelBag>';
            for ($i = 0; $i < count($tModelKey); $i++) {
                $element .= '<tModelKey>uuid:' . $tModelKey[$i] . '</tModelKey>';
                $element .= '</tModelBag>';
            }
        }

        if (isset($params['name']) && ($params['name'] != '')) {
            $lang = '';
            if (isset($params['lang']) && ($params['lang'] != '')) {
                $lang = "xml:lang=\"$lang\"";
            }
            $element .= '<name ' . $lang . '>' . $params['name'] . '</name>';
        }

        if (isset($params['identifierBag']) && ($params['identifierBag'] != '')) {
            $element .= '<identifierBag>';
            $keyedReference = explode(',', $params['identifierBag']);
            for ($i = 0; $i < count($keyedReference); $i++) {
                $element .= '<keyedReference>' . $keyedReference[$i] . '</keyedReference>';
            }
            $element .= '</identifierBag>';
        }

        if (isset($params['categoryBag']) && ($params['categoryBag'] != '')) {
            $element .= '<categoryBag>';
            $keyedReference = explode(',', $params['identifierBag']);
            for ($i = 0; $i<count($keyedReference); $i++) {
                $element .= '<keyedReference>' . $keyedReference[$i] . '</keyedReference>';
            }
            $element .= '</categoryBag>';
        }

        if (isset($params['maxRows']) && ($params['maxRows'] != '')) {
            $attrib .= ' maxRows="' . $params['maxRows'] . '"';
        }

        $head .= "<$method $attrib xmlns=\"$this->_xmlns\" generic=\"$this->_generic\">";

        $message = $head;
        $message .= $element;
        $message .= $end;

        return $message;
    }

    // }}}
    // {{{ find_binding()

    /**
     * Sends find_binding inquiry to UBR (searchs for bindings within a businessService element)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function find_binding($params)
    {
        $data = $this->query('find_binding', $params);
        return $data;
    }

    // }}}
    // {{{ find_business()

    /**
     * Sends find_business inquiry to UBR (searchs businessEntity elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function find_business($params)
    {
        $data = $this->query('find_business', $params);
        return $data;
    }

    // }}}
    // {{{ find_relatedBusinesses()

    /**
     * Sends find_relatedBusinesses inquiry to UBR (searchs for related businessEntity elements for a given businessKey)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function find_relatedBusinesses($params)
    {
        $data = $this->query('find_relatedBusinesses', $params);
        return $data;
    }

    // }}}
    // {{{ find_service()

    /**
     * Sends find_service inquiry to UBR (searchs for businessService elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function find_service($params)
    {
        $data = $this->query('find_service', $params);
        return $data;
    }

    // }}}
    // {{{ find_tModel()

    /**
     * Sends find_tModel inquiry to UBR (searchs for tModel elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function find_tModel($params)
    {
        $data = $this->query('find_tModel', $params);
        return $data;
    }

    // }}}
    // {{{ get_bindingDetail()

    /**
     * Sends get_bindingDetail inquiry to UBR (returns bindingDetail elements for one or more bindingKey elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function get_bindingDetail($params)
    {
        $data = $this->query('get_bindingDetail', $params);
        return $data;
    }

    // }}}
    // {{{ get_businessDetail()

    /**
     * Sends get_businessDetail inquiry to UBR (returns information about one or more businessEntity elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function get_businessDetail($params)
    {
        $data = $this->query('get_businessDetail', $params);
        return $data;
    }

    // }}}
    // {{{ get_businessDetailExt()

    /**
     * Sends get_businessDetailExt inquiry to UBR (returns extended information about one or more businessEntity elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function get_businessDetailExt($params)
    {
        $data = $this->query('get_businessDetailExt', $params);
        return $data;
    }

    // }}}
    // {{{ get_serviceDetail()

    /**
     * Sends get_serviceDetail inquiry to UBR (returns information about one or more businessService elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function get_serviceDetail($params)
    {
        $data = $this->query('get_serviceDetail', $params);
        return $data;
    }

    // }}}
    // {{{ get_tModelDetail()

    /**
     * Sends get_tModelDetail inquiry to UBR (returns information about one or more tModel elements)
     *
     * @access   public
     * @param    array   $params     parameters for the query
     * @return   string  $data       response from the registry server
     */
    function get_tModelDetail($params)
    {
        $data = $this->query('get_tModelDetail', $params);
        return $data;
    }

    // }}}

}

// }}}

?>
