<?php
// +----------------------------------------------------------------------+
// | PHP version 4.2                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Lucas Nealan <lucas@facebook.com>                           |
// +----------------------------------------------------------------------+

// $Id: APC.php,v 1.1 2007/09/19 21:35:22 jrust Exp $

require_once 'Net_UserAgent/detect.php';

class Net_UserAgent_Detect_APC extends Net_UserAgent_Detect
{
    var $key = '';

    function Net_UserAgent_Detect_APC($in_userAgent = null, $in_detect = null, $ua_cache_window = 600)
    {
        $data = '';
        $restored = false;
        $ua_cache_timeout = apc_fetch('useragent:cache_timeout');               // don't cache after time period

        if(!class_exists('G')){
          $realdocuroot = str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] );
          $docuroot = explode( '/', $realdocuroot );
          array_pop( $docuroot );
          $pathhome = implode( '/', $docuroot ) . '/';
          array_pop( $docuroot );
          $pathTrunk = implode( '/', $docuroot ) . '/';
          require_once($pathTrunk.'gulliver/system/class.g.php');
        }
        if ($ua_cache_window > 0) {
            if (!$ua_cache_timeout) {
                // check apc uptime and disable after x mins
                $apc_data = apc_cache_info('file', true);

                if (isset($apc_data['start_time'])) {
                    $uptime = $apc_data['start_time'];

                    if (time() - $uptime > $ua_cache_window) { // timeout and disable after 10 minutes of uptime
                        apc_store('useragent:cache_timeout', true);
                        $ua_cache_timeout = true; // don't cache this one either
                    }
                }
            }

            if (!$this->key) {
                $key_flags = '';
                if ($in_detect !== null) {
                    $key_flags = implode('-', $in_detect);
                }
                $this->key = 'useragent:'.G::encryptOld($in_userAgent.$key_flags);
            }

            if ($data = apc_fetch($this->key)) {
                $success = null;
                $data = unserialize($data);
                if ($data) {
                    $restored = $this->cache_restore($data);
                }
            }
        }

        if (!$data) {
            $this->detect($in_userAgent, $in_detect);

            if ($ua_cache_window > 0 && !$ua_cache_timeout) {
                $this->cache_save();
            }
        }
    }

    function &singleton($in_userAgent = null, $in_detect = null) 
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Net_UserAgent_Detect_APC($in_userAgent, $in_detect);
        }

        return $instance;
    }

    function cache_restore($cache) 
    {
        if (is_array($cache)) {
            foreach($cache as $prop => $value) {
                $ptr = Net_UserAgent_Detect::_getStaticProperty($prop);
                $ptr = $value;
            }
            return true;
        }
        return false;
    }

    function cache_save() 
    {
        if ($this->key) {
            $data = array('browser'           => Net_UserAgent_Detect::_getStaticProperty('browser'),
                          'features'          => Net_UserAgent_Detect::_getStaticProperty('features'),
                          'leadingIdentifier' => Net_UserAgent_Detect::_getStaticProperty('leadingIdentifier'),
                          'majorVersion'      => Net_UserAgent_Detect::_getStaticProperty('majorVersion'),
                          'options'           => Net_UserAgent_Detect::_getStaticProperty('options'),
                          'os'                => Net_UserAgent_Detect::_getStaticProperty('os'),
                          'quirks'            => Net_UserAgent_Detect::_getStaticProperty('quirks'),
                          'subVersion'        => Net_UserAgent_Detect::_getStaticProperty('subVersion'),
                          'userAgent'         => Net_UserAgent_Detect::_getStaticProperty('userAgent'),
                          'version'           => Net_UserAgent_Detect::_getStaticProperty('version'),
                         );
            apc_store($this->key, serialize($data));
        }
    }
}
?>
