<?php
/**
 * Project:		Distrubution License Class
 * File:			class.license.app.php
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
 * @history---------------------------------------------
 * see CHANGELOG
 */
class license_application extends padl
{
    /**
     * The number of allowed differences between the $_SERVER vars and the vars
     * stored in the key
     *
     * @var number
     */
    public $_ALLOWED_SERVER_DIFS = 0;
    /**
     * The number of allowed differences between the $ip vars in the key and the ip
     * vars collected from the server
     *
     * @var number
     */
    public $_ALLOWED_IP_DIFS = 0;
    /**
     * the path of the license key file, remember this would be relative to the
     * include path of the class file.
     */
    public $_LICENSE_PATH;

    /**
     * Constructor
     *
     * @access public
     * @param $use_mcrypt boolean Determines if mcrypt encryption is used or not (defaults to true,
     * 					 however if mcrypt is not available, it is set to false)
     * @param $use_time boolean Sets if time binding should be used in the key (defaults to true)
     * @param $use_server boolean Sets if server binding should be used in the key (defaults to true)
     * @param $allow_local boolean Sets if server binding is in use then localhost servers are valid (defaults to false)
     * */
    public function license_application($license_path = 'license.dat', $use_mcrypt = true, $use_time = true, $use_server = true, $allow_local = false, $challenge = false)
    {
        //Check to see if the class has been secured
        if (isset($_SESSION)) {
            unset($_SESSION["__sw__"]);

            if ($challenge) {
                $_SESSION["__sw__"] = true;
            }
        }

        $this->_check_secure();
        $this->_LICENSE_PATH = $license_path;
        $this->init($use_mcrypt, $use_time, $use_server, $allow_local);
        if ($this->USE_SERVER) {
            $this->_MAC = $this->_get_mac_address();
        }
    }

    /**
     * set_server_vars
     *
     * to protect against spoofing you should copy the $_SERVER vars into a
     * seperate array right at the first line of your script so parameters can't
     * be changed in unencoded php files. This doesn't have to be set. If it is
     * not set then the $_SERVER is copied when _get_server_info (private) function
     * is called.
     *
     * @access public
     * @param $array array The copied $_SERVER array
     * */
    public function set_server_vars($array)
    {
        # check to see if the class has been secured
        $this->_check_secure();
        $this->_SERVER_VARS = $array;
        # some of the ip data is dependant on the $_SERVER vars, so update them
        # after the vars have been set
        $this->_IPS = $this->_get_ip_address();
        # update the server info
        $this->_SERVER_INFO = $this->_get_server_info();
    }

    /**
     * _get_os_var
     *
     * gets various vars depending on the os type
     *
     * @access private
     * @return string various values
     * */
    public function _get_os_var($var_name, $os)
    {
        $var_name = strtolower($var_name);
        # switch between the os's
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
                # darwin is mac os x
                # tested only on the client os
            case 'darwin':
                # switch the var name
                switch ($var_name) {
                    case 'conf':
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac':
                        $var = 'ether';
                        break;
                    case 'ip':
                        $var = 'inet ';
                        break;
                }
                break;
                # linux variation
                # tested on server
            case 'linux':
                # switch the var name
                switch ($var_name) {
                    case 'conf':
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac':
                        $var = 'HWaddr';
                        break;
                    case 'ip':
                        $var = 'inet addr:';
                        break;
                }
                break;
        }
        return $var;
    }

    /**
     * _get_config
     *
     * gets the server config file and returns it. tested on Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @access private
     * @return string config file data
     * */
    public function _get_config()
    {
        # check to see if the class has been secured
        $this->_check_secure();
        if (ini_get('safe_mode')) {
            # returns invalid because server is in safe mode thus not allowing
            # sbin reads but will still allow it to open. a bit weird that one.
            return 'SAFE_MODE';
        }
        # if anyone has any clues for windows environments
        # or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) == 'win') {
            # this windows version works on xp running apache
            # based server. it has not been tested with anything
            # else, however it should work with NT, and 2000 also
            # execute the ipconfig
            @exec('ipconfig/all', $lines);
            # count number of lines, if none returned return MAC_404
            # thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
            if (count($lines) == 0) {
                return 'ERROR_OPEN';
            }
            # $path the lines together
            $conf = implode($this->_LINEBREAK, $lines);
        } else {
            # get the conf file name
            $os_file = $this->_get_os_var('conf', $os);
            # open the ipconfig
            $fp = @popen($os_file, "rb");
            # returns invalid, cannot open ifconfig
            if (!$fp) {
                return 'ERROR_OPEN';
            }
            # read the config
            $conf = @fread($fp, 4096);
            @pclose($fp);
        }
        return $conf;
    }

    /**
     * _get_ip_address
     *
     * Used to get the MAC address of the host server. It works with Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @access private
     * @return array IP Address(s) if found (Note one machine may have more than one ip)
     * @return string ERROR_OPEN means config can't be found and thus not opened
     * @return string IP_404 means ip adress doesn't exist in the config file and can't be found in the $_SERVER
     * @return string SAFE_MODE means server is in safe mode so config can't be read
     * */
    public function _get_ip_address()
    {
        $ips = array();
        # get the cofig file
        $conf = $this->_get_config();
        # if the conf has returned and error return it
        if ($conf != 'SAFE_MODE' && $conf != 'ERROR_OPEN') {
            # if anyone has any clues for windows environments
            # or other server types let me know
            $os = strtolower(PHP_OS);
            if (substr($os, 0, 3) == 'win') {
                # anyone any clues on win ip's
            } else {
                # explode the conf into seperate lines for searching
                $lines = explode($this->_LINEBREAK, $conf);
                # get the ip delim
                $ip_delim = $this->_get_os_var('ip', $os);

                # ip pregmatch
                $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
                # seperate the lines
                foreach ($lines as $key => $line) {
                    # check for the ip signature in the line
                    if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ip_delim)) {
                        # seperate out the ip
                        $ip = substr($line, strpos($line, $ip_delim) + strlen($ip_delim));
                        $ip = trim(substr($ip, 0, strpos($ip, " ")));
                        # add the ip to the collection
                        if (!isset($ips[$ip])) {
                            $ips[$ip] = $ip;
                        }
                    }
                }
            }
        }

        # if the conf has returned nothing
        # attempt to use the $_SERVER data
        if (isset($this->_SERVER_VARS['SERVER_NAME'])) {
            $ip = gethostbyname($this->_SERVER_VARS['SERVER_NAME']);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
        }

        if (isset($this->_SERVER_VARS['SERVER_ADDR'])) {
            $name = @gethostbyaddr($this->_SERVER_VARS['SERVER_ADDR']);
            $ip = gethostbyname($name);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
            # if the $_SERVER addr is not the same as the returned ip include it aswell
            if ($ip != $this->_SERVER_VARS['SERVER_ADDR']) {
                if (!isset($ips[$this->_SERVER_VARS['SERVER_ADDR']])) {
                    $ips[$this->_SERVER_VARS['SERVER_ADDR']] = $this->_SERVER_VARS['SERVER_ADDR'];
                }
            }
        }
        # count return ips and return if found
        if (count($ips) > 0) {
            return $ips;
        }
        # failed to find an ip check for conf error or return 404
        if ($conf == 'SAFE_MODE' || $conf == 'ERROR_OPEN') {
            return $conf;
        }
        return 'IP_404';
    }

    /**
     * _get_mac_address
     *
     * Used to get the MAC address of the host server. It works with Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @access private
     * @return string Mac address if found
     * @return string ERROR_OPEN means config can't be found and thus not opened
     * @return string MAC_404 means mac adress doesn't exist in the config file
     * @return string SAFE_MODE means server is in safe mode so config can't be read
     * */
    public function _get_mac_address()
    {
        # open the config file
        $conf = $this->_get_config();

        # if anyone has any clues for windows environments
        # or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) == 'win') {
            # explode the conf into lines to search for the mac
            $lines = explode($this->_LINEBREAK, $conf);
            # seperate the lines for analysis
            foreach ($lines as $key => $line) {
                # check for the mac signature in the line
                # originally the check was checking for the existence of string 'physical address'
                # however Gert-Rainer Bitterlich pointed out this was for english language
                # based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
                if (preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
                    $trimmed_line = trim($line);
                    # take of the mac addres and return
                    return trim(substr($trimmed_line, strrpos($trimmed_line, " ")));
                }
            }
        } else {
            # get the mac delim
            $mac_delim = $this->_get_os_var('mac', $os);

            # get the pos of the os_var to look for
            $pos = strpos($conf, $mac_delim);
            if ($pos) {
                # seperate out the mac address
                $str1 = trim(substr($conf, ($pos + strlen($mac_delim))));
                return trim(substr($str1, 0, strpos($str1, "\n")));
            }
        }
        # failed to find the mac address
        return 'MAC_404';
    }

    /**
     * _get_server_info
     *
     * used to generate the server binds when server binding is needed.
     *
     * @access private
     * @return array server bindings
     * @return boolean false means that the number of bindings failed to
     * 		  meet the required number
     * */
    public function _get_server_info()
    {
        if (empty($this->_SERVER_VARS)) {
            $this->set_server_vars($_SERVER);
        }
        # get the server specific uris
        $a = array();
        if (isset($this->_SERVER_VARS['SERVER_ADDR']) && (!strrpos($this->_SERVER_VARS['SERVER_ADDR'], '127.0.0.1') || $this->ALLOW_LOCAL)) {
            $a['SERVER_ADDR'] = $this->_SERVER_VARS['SERVER_ADDR'];
        }
        # corrected by Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>, Thanks
        if (isset($this->_SERVER_VARS['HTTP_HOST']) && (!strrpos($this->_SERVER_VARS['HTTP_HOST'], '127.0.0.1') || $this->ALLOW_LOCAL)) {
            $a['HTTP_HOST'] = $this->_SERVER_VARS['HTTP_HOST'];
        }
        if (isset($this->_SERVER_VARS['SERVER_NAME'])) {
            $a['SERVER_NAME'] = $this->_SERVER_VARS['SERVER_NAME'];
        }
        if (isset($this->_SERVER_VARS['PATH_TRANSLATED'])) {
            $a['PATH_TRANSLATED'] = substr($this->_SERVER_VARS['PATH_TRANSLATED'], 0, strrpos($this->_SERVER_VARS['PATH_TRANSLATED'], '/'));
        } elseif (isset($this->_SERVER_VARS['SCRIPT_FILENAME'])) {
            $a['SCRIPT_FILENAME'] = substr($this->_SERVER_VARS['SCRIPT_FILENAME'], 0, strrpos($this->_SERVER_VARS['SCRIPT_FILENAME'], '/'));
        }
        if (isset($_SERVER['SCRIPT_URI'])) {
            $a['SCRIPT_URI'] = substr($this->_SERVER_VARS['SCRIPT_URI'], 0, strrpos($this->_SERVER_VARS['SCRIPT_URI'], '/'));
        }

        # if the number of different uris is less than the required amount,
        # fail the request
        if (count($a) < $this->REQUIRED_URIS) {
            return 'SERVER_FAILED';
        }

        return $a;
    }

    /**
     * validate
     *
     * validates the server key and returns a data array.
     *
     * @access public
     * @return array Main object in array is 'RESULT', it contains the result
     * 		 of the validation.
     * 		 OK 		- key is valid
     * 		 CORRUPT 	- key has been tampered with
     * 		 TMINUS 	- the key is being used before the valid start date
     * 		 EXPIRED 	- the key has expired
     * 		 ILLEGAL 	- the key is not on the same server the license was registered to
     * 		 ILLEGAL_LOCAL 	- the key is not allowed to be installed on a local machine
     * 		 INVALID 	- the the encryption key used to encrypt the key differs or the key is not complete
     * 		 EMPTY	 	- the the key is empty
     * 		 404	 	- the the key is missing
     * */
    public function validate($str = false, $dialhome = false, $dialhost = "", $dialpath = "", $dialport = "80")
    {
        # check to see if the class has been secured
        $this->_check_secure();
        # get the dat string
        $dat_str = (!$str) ? @file_get_contents($this->_LICENSE_PATH) : $str;
        if (strlen($dat_str) > 0) {
            # decrypt the data
            $DATA = $this->_unwrap_license($dat_str);
            if (is_array($DATA)) {
                # missing / incorrect id therefore it has been tampered with
                /*
                 *Disable to accept licenses from other workspaces
                 *if ($DATA['ID'] != G::encryptOld($this->ID1)) {
                    $DATA['RESULT'] = 'CORRUPT';
                }*/
                if ($this->USE_TIME) {
                    # the license is being used before it's official start
                    if ($DATA['DATE']['START'] > time() + $this->START_DIF) {
                        $DATA['RESULT'] = 'TMINUS';
                    }
                    # the license has expired
                    if ($DATA['DATE']['END'] - time() < 0 && $DATA['DATE']['SPAN'] != 'NEVER' && $DATA['DATE']['SPAN'] != '~') {
                        $DATA['RESULT'] = 'EXPIRED';
                    }
                    $DATA['DATE']['HUMAN']['START'] = date($this->DATE_STRING, $DATA['DATE']['START']);
                    if (($DATA['DATE']['END'] == "") || ($DATA['DATE']['END'] == "NEVER")) {
                        $DATA['DATE']['HUMAN']['END'] = "PERPETUAL";
                    } else {
                        $DATA['DATE']['HUMAN']['END'] = date($this->DATE_STRING, $DATA['DATE']['END']);
                    }
                }
                if ($this->USE_SERVER) {
                    $mac = $DATA['SERVER']['MAC'] == $this->_MAC;
                    $path = count(array_diff($this->_SERVER_INFO, $DATA['SERVER']['PATH'])) <= $this->_ALLOWED_SERVER_DIFS;
                    $domain = $this->_compare_domain_ip($DATA['SERVER']['DOMAIN'], $this->_IPS);
                    $ip = count(array_diff($this->_IPS, $DATA['SERVER']['IP'])) <= $this->_ALLOWED_IP_DIFS;

                    # the server details
                    if (!$mac || !$path || !$domain || !$ip) {
                        $DATA['RESULT'] = 'ILLEGAL';
                    }

                    # check if local
                    $local = $this->ALLOW_LOCAL && (in_array('127.0.0.1', $DATA['SERVER']['IP']) || $DATA['PATH']['SERVER_ADDR'] == '127.0.0.1' || $DATA['PATH']['HTTP_HOST'] == '127.0.0.1');
                    if (!$local) {
                        $DATA['RESULT'] = 'ILLEGAL_LOCAL';
                    }
                }
                # passed all current test so license is ok
                if (!isset($DATA['RESULT'])) {
                    # dial to home server if required
                    if ($dialhome) {
                        # create the details to send to the home server
                        $stuff_to_send = array();
                        $stuff_to_send['LICENSE_DATA'] = $DATA;
                        $stuff_to_send['LICENSE_DATA']['KEY'] = G::encryptOld($dat_str);
                        # dial home
                        $DATA['RESULT'] = $this->_call_home($stuff_to_send, $dialhost, $dialpath, $dialport);
                    } else {
                        # result is ok all test passed, license is legal
                        $DATA['RESULT'] = 'OK';
                    }
                }
                /*
                 */
                # data is returned for use
                return $DATA;
            } else {
                # the are two reason that mean a invalid return
                # 1 - the other hash key is different
                # 2 - the key has been tampered with
                return array('RESULT' => 'INVALID');
            }
        }
        # returns empty because there is nothing in the dat_string
        return array('RESULT' => 'EMPTY');
    }

    /**
     * _call_home
     *
     * calls the dial home server (your server) andvalidates the clients license
     * with the info in the mysql db
     *
     * @access private
     * @param $data array Array that contains the info to be validated
     * @param $dialhost string Host name of the server to be contacted
     * @param $dialpath string Path of the script for the data to be sent to
     * @param $dialport number Port Number to send the data through
     * @return string Returns: the encrypted server validation result from the dial home call
     * 						: SOCKET_FAILED		=> socket failed to connect to the server
     * */
    public function _call_home($data, $dialhost, $dialpath, $dialport)
    {
        print "_call_home($data, $dialhost, $dialpath, $dialport)";
        # post the data home
        $data = $this->_post_data($dialhost, $dialpath, $data, $dialport);
        return (empty($data['RESULT'])) ? 'SOCKET_FAILED' : $data['RESULT'];
    }
}

