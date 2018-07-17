<?php
/**
 * Project:		Distrubution License Class
 * File:			class.license.lib.php
 *
 * Copyright (C) 2005 Oliver Lillie
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by  the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @link http://www.buggedcom.co.uk/
 * @link http://www.phpclasses.org/browse/package/2298.html
 * @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
 * @version 0.1
 * @history---------------------------------------------
 * see CHANGELOG
 */

use ProcessMaker\Core\System;

/**
 * Project:		Distrubution License Class
 * File:			class.license.lib.php
 *
 * Copyright (C) 2005 Oliver Lillie
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by  the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @link http://www.buggedcom.co.uk/
 * @link http://www.phpclasses.org/browse/package/2298.html
 * @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
 * @version 0.1
 * @history---------------------------------------------
 * see CHANGELOG
 */
class Padl
{
    /**
     * hash key 1 used to encrypt the generate key data.
     * hash key 2 used to encrypt the request data
     * hash key 3 used to encrypt the dial home data
     * NOTE1 : there are three different hash keys for the three different operations
     * NOTE2 : these hash key's are for use by both mcrypt and alternate cryptions
     * 		  and although mcrypts keys are typically short they should be kept long
     * 		  for the sake of the other functions
     *
     * @var string
     * @var string
     * @var string
     */
    public $HASH_KEY1 = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35BDFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEzGDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHT';
    public $HASH_KEY2 = '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYDU52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wohX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4a';
    public $HASH_KEY3 = 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W66qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62T';
    /**
     * You may not want to use mcrypt even if your system has it installed
     * make this false to use a regular encryption method
     *
     * @var boolean
     */
    public $USE_MCRYPT = true;
    /**
     * The algorythm to be used by mcrypt
     *
     * @var string
     */
    public $ALGORITHM = 'blowfish';
    /**
     * use time binding vars inited.
     */
    public $USE_TIME;
    /**
     * time checking start period difference allowance ie if the user has slightly different time
     * setting on their server make an allowance for the diff period. carefull to not make it too
     * much otherwise they could just reset their server to a time period before the license expires.
     *
     * @var number (seconds)
     */
    public $START_DIF = 129600;
    /**
     * id 1 used to validate license keys
     * id 2 used to validate license key requests
     * id 2 used to validate dial home data
     *
     * @var string
     * @var string
     * @var string
     */
    # id to check for to validate source
    public $ID1 = 'nSpkAHRiFfM2hE588eB';
    public $ID2 = 'NWCy0s0JpGubCVKlkkK';
    public $ID3 = 'G95ZP2uS782cFey9x5A';
    /**
     * begining and end strings
     *
     * @var strings
     */
    public $BEGIN1 = 'BEGIN LICENSE KEY';
    public $END1 = 'END LICENSE KEY';
    /**
     * wrap key settings
     *
     * @var number
     * @var string
     * @var string
     */
    public $_WRAPTO = 80;
    public $_PAD = "-";
    /**
     * init the linebreak var
     */
    public $_LINEBREAK;
    /**
     * dial home return query deliminators
     *
     * @var string
     * @var string
     */
    public $BEGIN2 = '_DATA{';
    public $END2 = '}DATA_';
    /**
     * init the key data array.
     *
     * @var array
     */
    public $_DATA = array();
    /**
     * use server binding vars inited.
     */
    public $USE_SERVER;
    public $_SERV;
    public $_MAC;
    public $ALLOW_LOCAL;
    public $_SERVER_INFO = array();
    /**
     * this is the number of required server stats for the key generation to be successfull
     * if the server can't produce this number of details then the key fails to be generated
     * you can set it to however many you wish, the max is 5
     *
     * @var number
     */
    public $REQUIRED_URIS = 2;
    /**
     * the date string for human readable format
     *
     * @var string
     */
    public $DATE_STRING = 'M/d/Y H:i:s';

    /**
     * Constructor
     *
     * @access private
     * */
    public function padl()
    {
        # check to see if the class has been secured
        $this->_check_secure();
    }

    /**
     * init
     *
     * init the license class
     *
     * @access public
     * @param $use_mcrypt boolean Determines if mcrypt encryption is used or not (defaults to true,
     * 					 however if mcrypt is not available, it is set to false)
     * @param $use_time boolean Sets if time binding should be used in the key (defaults to true)
     * @param $use_server boolean Sets if server binding should be used in the key (defaults to true)
     * @param $allow_local boolean Sets if server binding is in use then localhost servers are valid (defaults to false)
     * */
    public function init($use_mcrypt = true, $use_time = true, $use_server = true, $allow_local = false, $challenge = false)
    {
        # check to see if the class has been secured
        if (!$challenge) {
            $this->_check_secure();
        }
        $this->USE_MCRYPT = ($use_mcrypt && function_exists('mcrypt_generic'));
        $this->USE_TIME = $use_time;
        $this->ALLOW_LOCAL = $allow_local;
        $this->USE_SERVER = $use_server;
        $this->_LINEBREAK = $this->_get_os_linebreak();
    }

    /**
     * _get_os_linebreak
     *
     * get's the os linebreak
     *
     * @access private
     * @param $true_val boolean If the true value is needed for writing files, make true
     * 							defaults to false
     * @return string Returns the os linebreak
     * */
    public function _get_os_linebreak($true_val = false)
    {
        $os = strtolower(PHP_OS);
        switch ($os) {
            # not sure if the string is correct for FreeBSD
            # not tested
            case 'freebsd':
                # not sure if the string is correct for NetBSD
                # not tested
            case 'netbsd':
                # not sure if the string is correct for Solaris
                # not tested
            case 'solaris':
                # not sure if the string is correct for SunOS
                # not tested
            case 'sunos':
                # linux variation
                # tested on server
            case 'linux':
                $nl = "\n";
                break;
                # darwin is mac os x
                # tested only on the client os
            case 'darwin':
                # note os x has \r line returns however it appears that the ifcofig
                # file used to source much data uses \n. let me know if this is
                # just my setup and i will attempt to fix.
                if ($true_val) {
                    $nl = "\r";
                } else {
                    $nl = "\n";
                }
                break;
                # defaults to a win system format;
            default:
                $nl = "\r\n";
        }
        return $nl;
    }

    public function do_post_request($url, $data, $optional_headers = null)
    {
        $params = array('http' => array(
        'method' => 'POST',
        'content' => $data
        ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }

        // Proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            if (!is_array($params['http'])) {
                $params['http'] = array();
            }
            $params['http']['request_fulluri'] = true;
            $params['http']['proxy'] = 'tcp://' . $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '');
            if ($sysConf['proxy_user'] != '') {
                if (!isset($params['http']['header'])) {
                    $params['http']['header'] = '';
                }
                $params['http']['header'] .= 'Proxy-Authorization: Basic ' . base64_encode($sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
        }

        $ctx = stream_context_create($params);
        //G::pr($ctx);
        $fp = @fopen($url, 'rb', false, $ctx);
        //G::pr($fp);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }
        return $response;
    }

    /**
     * _post_data
     *
     * Posts data to and recieves data from dial home server. Returned info
     * contains the dial home validation result
     *
     * @access private
     * @param $host string Host name of the server to be contacted
     * @param $path string Path of the script for the data to be sent to
     * @param $query_array array Array that contains the license key info to be validated
     * @param $port number Port Number to send the data through
     * @return array Result of the dialhome validation
     * @return string - SOCKET_FAILED will be returned if it was not possible to open a socket to the home server
     * */
    public function _post_data($host, $path, $query_array, $port = 80)
    {
        # generate the post query info
        $query = 'POSTDATA=' . $this->_encrypt($query_array, 'HOMEKEY');
        $query .= '&MCRYPT=' . $this->USE_MCRYPT;
        $query .= '&TYPE=' . $query_array['DATA']['TYPE'];
        $query .= '&PLAN=' . $query_array['DATA']['PLAN'];
        //G::pr($path);
        //G::pr($query);
        # init the return string
        //$return  = '';
        //$return=$this->do_post_request($host.$path, $query);
        # separate out the data using the delims
        //$leftpos = strpos($return, $this->BEGIN2)+strlen($this->BEGIN2);
        //$rightpos = strpos($return, $this->END2)-$leftpos;
        # decrypt and return the data
        //return $this->_decrypt(substr($return, $leftpos, $rightpos), 'HOMEKEY');
        //die;
        # generate the post headers
        $post = "POST $path HTTP/1.1\r\n";
        $post .= "Host: $host\r\n";
        $post .= "Content-type: application/x-www-form-urlencoded\r\n";
        $post .= "Content-length: " . strlen($query) . "\r\n";
        $post .= "Connection: close\r\n";
        $post .= "\r\n";
        $post .= $query;

        # open a socket
        $ip = gethostbyname($host);
        if ($ip == $host) {
            $ip = rtrim(`/usr/bin/dig $host A +short | /usr/bin/tail -1`);
        }
        $header = @fsockopen($ip, $port);
        //print "fsockopen($host, $port)";
        //G::pr($header);
        if (!$header) {
            # if the socket fails return failed
            return array('RESULT' => 'SOCKET_FAILED');
        }
        @fputs($header, $post);
        //G::pr($post);
        # read the returned data
        while (!@feof($header)) {
            $return .= @ fgets($header, 1024);
        }
        fclose($header);

        # separate out the data using the delims
        $leftpos = strpos($return, $this->BEGIN2) + strlen($this->BEGIN2);
        $rightpos = strpos($return, $this->END2) - $leftpos;

        #trace($return);
        # decrypt and return the data
        return $this->_decrypt(substr($return, $leftpos, $rightpos), 'HOMEKEY');
    }

    /**
     * _compare_domain_ip
     *
     * uses the supplied domain in the key and runs a check against the collected
     * ip addresses. If there are matching ips it returns true as the domain
     * and ip address match up
     *
     * @access private
     * @return boolean
     * */
    public function _compare_domain_ip($domain, $ips = false)
    {
        # if no ips are supplied get the ip addresses for the server
        if (!$ips) {
            $ips = $this->_get_ip_address();
        }
        # get the domain ip list
        $domain_ips = gethostbynamel($domain);
        # loop through the collected ip's searching for matches against the domain ips
        if (is_array($domain_ips) && count($domain_ips) > 0) {
            foreach ($domain_ips as $ip) {
                if (in_array($ip, $ips)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * _pad
     *
     * pad out the begin and end seperators
     *
     * @access private
     * @param $str string The string to be padded
     * @return string Returns the padded string
     * */
    public function _pad($str)
    {
        $str_len = strlen($str);
        $spaces = ($this->_WRAPTO - $str_len) / 2;
        $str1 = '';
        for ($i = 0; $i < $spaces; $i++) {
            $str1 = $str1 . $this->_PAD;
        }
        if ($spaces / 2 != round($spaces / 2)) {
            $str = substr($str1, 0, strlen($str1) - 1) . $str;
        } else {
            $str = $str1 . $str;
        }
        $str = $str . $str1;
        return $str;
    }

    /**
     * _get_key
     *
     * gets the hash key for the current encryption
     *
     * @access private
     * @param $key_type string The license key type being produced
     * @return string Returns the hash key
     * */
    public function _get_key($key_type)
    {
        switch ($key_type) {
            case 'KEY':
                return $this->HASH_KEY1;
            case 'REQUESTKEY':
                return $this->HASH_KEY2;
            case 'HOMEKEY':
                return $this->HASH_KEY3;
            default:
        }
    }

    /**
     * _get_begin
     *
     * gets the begining license key seperator text
     *
     * @access private
     * @param $key_type string The license key type being produced
     * @return string Returns the begining string
     * */
    public function _get_begin($key_type)
    {
        switch ($key_type) {
            case 'KEY':
                return $this->BEGIN1;
            case 'REQUESTKEY':
                return $this->BEGIN2;
            case 'HOMEKEY':
                return '';
        }
    }

    /**
     * _get_end
     *
     * gets the ending license key seperator text
     *
     * @access private
     * @param $key_type string The license key type being produced
     * @return string Returns the ending string
     * */
    public function _get_end($key_type)
    {
        switch ($key_type) {
            case 'KEY':
                return $this->END1;
            case 'REQUESTKEY':
                return $this->_END2;
            case 'HOMEKEY':
                return '';
        }
    }

    /**
     * _generate_random_string
     *
     * generates a random string
     *
     * @access private
     * @param $length number The length of the random string
     * @param $seeds string The string to pluck the characters from
     * @return string Returns random string
     * */
    public function _generate_random_string($length = 10, $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890123456789')
    {
        $str = '';
        $seeds_count = strlen($seeds);

        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seeds_count - 1)};
        }
        return $str;
    }

    /**
     * _encrypt
     *
     * encrypts the key
     *
     * @access private
     * @param $src_array array The data array that contains the key data
     * @return string Returns the encrypted string
     * */
    public function _encrypt($src_array, $key_type = 'KEY')
    {
        # check to see if the class has been secured
        $this->_check_secure();

        $rand_add_on = $this->_generate_random_string(3);
        # get the key
        $key = $this->_get_key($key_type);
        $key = $rand_add_on . $key;

        # check to see if mycrypt exists
        if ($this->USE_MCRYPT) {
            # openup mcrypt
            $td = mcrypt_module_open($this->ALGORITHM, '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            # process the key
            $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            # init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            # encrypt data
            # double base64 gets makes all the characters alpha numeric
            # and gets rig of the special characters
            $crypt = mcrypt_generic($td, serialize($src_array));

            # shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            # if mcrypt doesn't exist use regular encryption method
            # init the vars
            $crypt = '';
            $str = serialize($src_array);

            # loop through the str and encrypt it
            for ($i = 1; $i <= strlen($str); $i++) {
                $char = substr($str, $i - 1, 1);
                $keychar = substr($key, ($i % strlen($key)) - 1, 1);
                $char = chr(ord($char) + ord($keychar));
                $crypt .= $char;
            }
        }
        # return the key
        return $rand_add_on . base64_encode(base64_encode(trim($crypt)));
    }

    /**
     * _decrypt
     *
     * decrypts the key
     *
     * @access private
     * @param $enc_string string The key string that contains the data
     * @return array Returns decrypted array
     * */
    public function _decrypt($str, $key_type = 'KEY')
    {
        # check to see if the class has been secured
        $this->_check_secure();

        $rand_add_on = substr($str, 0, 3);
        $str = base64_decode(base64_decode(substr($str, 3)));
        # get the key
        $key = $rand_add_on . $this->_get_key($key_type);

        # check to see if mycrypt exists
        if ($this->USE_MCRYPT) {
            # openup mcrypt
            $td = mcrypt_module_open($this->ALGORITHM, '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            # process the key
            $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            # init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            # decrypt the data and return
            $decrypt = mdecrypt_generic($td, $str);

            # shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            # if mcrypt doesn't exist use regular decryption method
            # init the decrypt vars
            $decrypt = '';

            # loop through the text and decode the string
            for ($i = 1; $i <= strlen($str); $i++) {
                $char = substr($str, $i - 1, 1);
                $keychar = substr($key, ($i % strlen($key)) - 1, 1);
                $char = chr(ord($char) - ord($keychar));
                $decrypt .= $char;
            }
        }
        # return the key
        return unserialize($decrypt);
    }

    /**
     * _wrap_license
     *
     * wraps up the license key in a nice little package
     *
     * @access private
     * @param $src_array array The array that needs to be turned into a license str
     * @param $key_type string The type of key to be wrapped (KEY=license key, REQUESTKEY=license request key)
     * @return string Returns encrypted and formatted license key
     * */
    public function _wrap_license($src_array, $key_type = 'KEY')
    {
        # sort the variables
        $begin = $this->_pad($this->_get_begin($key_type));
        $end = $this->_pad($this->_get_end($key_type));

        # encrypt the data
        $str = $this->_encrypt($src_array, $key_type);

        # return the wrap
        return $begin . $this->_LINEBREAK . wordwrap($str, $this->_WRAPTO, $this->_LINEBREAK, 1) . $this->_LINEBREAK . $end;
    }

    /**
     * _unwrap_license
     *
     * unwraps license key back into it's data array
     *
     * @access private
     * @param $enc_str string The encrypted license key string that needs to be decrypted
     * @param $key_type string The type of key to be unwrapped (KEY=license key, REQUESTKEY=license request key)
     * @return array Returns license data array
     * */
    public function _unwrap_license($enc_str, $key_type = 'KEY')
    {
        # sort the variables
        $begin = $this->_pad($this->_get_begin($key_type));
        $end = $this->_pad($this->_get_end($key_type));

        # get string without seperators
        $str = trim(str_replace(array($begin, $end, "\r", "\n", "\t"), '', $enc_str));

        # decrypt and return the key
        return $this->_decrypt($str, $key_type);
    }

    /**
     * make_secure
     *
     * deletes all class values to prevent re-writing of a key;
     *
     * @access public
     * */
    public function make_secure($report = false)
    {
        if ($report) {
            define('_PADL_REPORT_ABUSE_', true);
        }
        # walkthrough and delete the class vars
        foreach (array_keys(get_object_vars($this)) as $value) {
            unset($this->$value);
        }
        # define that class is secure
        define('_PADL_SECURE_', 1);
    }

    /**
     * _check_secure
     *
     * checks to see if the class has been made secure
     *
     * @access private
     * */
    public function _check_secure()
    {
        if (!isset($_SESSION['__sw__'])) {
            # check to see if padl has been made secure
            if (defined('_PADL_SECURE_')) {
                # if(defined('_PADL_REPORT_ABUSE_')) $this->_post_data($this->_HOST, $this->_PATH, array());
                # trigger the error because user has attempted to access secured functions
                # after the call has been made to 'make_secure'
                trigger_error("<br /><br /><span style='color: #F00;font-weight: bold;'>The PHP Application Distribution License System (PADL) has been made secure.<br />You have attempted to use functions that have been protected and this has terminated your script.<br /><br /></span>", E_USER_ERROR);
                exit;
            }
        }
    }
}
