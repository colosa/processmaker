<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
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
// | Authors: Alexander Merz <alexander.merz@web.de>				  |
// +----------------------------------------------------------------------+
//
// $Id: IPv6.php,v 1.12 2005/09/01 12:42:00 alexmerz Exp $

/**
* Class to validate and to work with IPv6
*
* @author  Alexander Merz <alexander.merz@t-online.de>
* @author elfrink at introweb dot nl
* @package Net_IPv6
* @version $Id: IPv6.php,v 1.12 2005/09/01 12:42:00 alexmerz Exp $
* @access  public
*/
class Net_IPv6 {

    // {{{ Uncompress()

    /**
     * Uncompresses an IPv6 adress
     *
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and expands the '::' to
     * the required zeros.
     *
     * Example:  FF01::101	->  FF01:0:0:0:0:0:0:101
     *           ::1        ->  0:0:0:0:0:0:0:1
     *
     * @access public
     * @see Compress()
     * @static
     * @param string $ip	a valid IPv6-adress (hex format)
     * @return string	the uncompressed IPv6-adress (hex format)
	 */
    function Uncompress($ip) {
        $uip = $ip;
        $c1 = -1;
        $c2 = -1;
        if (false !== strpos($ip, '::') ) {
            list($ip1, $ip2) = explode('::', $ip);
            if(""==$ip1) {
                $c1 = -1;
            } else {
               	$pos = 0;
                if(0 < ($pos = substr_count($ip1, ':'))) {
                    $c1 = $pos;
                } else {
                    $c1 = 0;
                }
            }
            if(""==$ip2) {
                $c2 = -1;
            } else {
                $pos = 0;
                if(0 < ($pos = substr_count($ip2, ':'))) {
                    $c2 = $pos;
                } else {
                    $c2 = 0;
                }
            }
            if(strstr($ip2, '.')) {
                $c2++;
            }
            if(-1 == $c1 && -1 == $c2) { // ::
                $uip = "0:0:0:0:0:0:0:0";
            } else if(-1==$c1) {              // ::xxx
                $fill = str_repeat('0:', 7-$c2);
                $uip =  str_replace('::', $fill, $uip);
            } else if(-1==$c2) {              // xxx::
                $fill = str_repeat(':0', 7-$c1);
                $uip =  str_replace('::', $fill, $uip);
            } else {                          // xxx::xxx
                $fill = str_repeat(':0:', 6-$c2-$c1);
                $uip =  str_replace('::', $fill, $uip);
                $uip =  str_replace('::', ':', $uip);
            }
        }
        return $uip;
    }

    // }}}
    // {{{ Compress()

    /**
     * Compresses an IPv6 adress
     *
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and compresses successive zeros
     * to '::'
     *
     * Example:  FF01:0:0:0:0:0:0:101 	-> FF01::101
     *           0:0:0:0:0:0:0:1        -> ::1
     *
     * @access public
     * @see Uncompress()
     * @static
     * @param string $ip	a valid IPv6-adress (hex format)     
     * @return string	the compressed IPv6-adress (hex format)
     * @author elfrink at introweb dot nl
     */
    function Compress($ip)	{
        $cip = $ip;

        if (!strstr($ip, '::')) {
             $ipp = explode(':',$ip);
             for($i=0; $i<count($ipp); $i++) {
                 $ipp[$i] = dechex(hexdec($ipp[$i]));
             }
            $cip = ':' . join(':',$ipp) . ':';
			preg_match_all("/(:0)+/", $cip, $zeros);
    		if (count($zeros[0])>0) {
				$match = '';
				foreach($zeros[0] as $zero) {
    				if (strlen($zero) > strlen($match))
						$match = $zero;
				}
				$cip = preg_replace('/' . $match . '/', ':', $cip, 1);
			}
			$cip = preg_replace('/((^:)|(:$))/', '' ,$cip);
            $cip = preg_replace('/((^:)|(:$))/', '::' ,$cip);
         }
         return $cip;
    }

    // }}}
    // {{{ SplitV64()

    /**
     * Splits an IPv6 adress into the IPv6 and a possible IPv4 part
     *
     * RFC 2373 allows you to note the last two parts of an IPv6 adress as
     * an IPv4 compatible adress
     *
     * Example:  0:0:0:0:0:0:13.1.68.3
     *           0:0:0:0:0:FFFF:129.144.52.38
     *
     * @access public
     * @static
     * @param string $ip	a valid IPv6-adress (hex format)
     * @return array		[0] contains the IPv6 part, [1] the IPv4 part (hex format)
     */
    function SplitV64($ip) {
        $ip = Net_IPv6::Uncompress($ip);
        if (strstr($ip, '.')) {
            $pos = strrpos($ip, ':');
            $ip{$pos} = '_';
            $ipPart = explode('_', $ip);
            return $ipPart;
        } else {
            return array($ip, "");
        }
    }

    // }}}
    // {{{ checkIPv6

    /**
     * Checks an IPv6 adress
     *
     * Checks if the given IP is IPv6-compatible
     *
     * @access public
     * @static
     * @param string $ip	a valid IPv6-adress
     * @return boolean	true if $ip is an IPv6 adress
     */
    function checkIPv6($ip) {

        $ipPart = Net_IPv6::SplitV64($ip);
        $count = 0;
        if (!empty($ipPart[0])) {
            $ipv6 =explode(':', $ipPart[0]);
            for ($i = 0; $i < count($ipv6); $i++) {
                $dec = hexdec($ipv6[$i]);
                $hex = strtoupper(preg_replace("/^[0]{1,3}(.*[0-9a-fA-F])$/", "\\1", $ipv6[$i]));
                if ($ipv6[$i] >= 0 && $dec <= 65535 && $hex == strtoupper(dechex($dec))) {
                    $count++;
                }
            }
            if (8 == $count) {
                return true;
            } elseif (6 == $count and !empty($ipPart[1])) {
                $ipv4 = explode('.',$ipPart[1]);
                $count = 0;
                for ($i = 0; $i < count($ipv4); $i++) {
                    if ($ipv4[$i] >= 0 && (integer)$ipv4[$i] <= 255 && preg_match("/^\d{1,3}$/", $ipv4[$i])) {
                        $count++;
                    }
                }
                if (4 == $count) {
                    return true;
                }
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
    // }}}
}
?>
