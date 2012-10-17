<?php
/**
 * class.error.php
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

require_once 'PEAR.php';

/**
 * G_Error implements a class for reporting portable database error
 * messages
 */

define( 'G_ERROR', - 100 );
define( 'DB_ERROR_FEATURE_NOT_AVAILABLE', - 101 );
define( 'DB_ERROR_OBJECT_NOT_DEFINED', - 102 );
define( 'G_ERROR_WARNING_MESSAGE', - 103 );
define( 'G_ERROR_DBCONNECTION', - 104 );
define( 'G_ERROR_SYSTEM_UID', - 105 );
define( 'G_ERROR_SYSTEM_CODE', - 106 );
define( 'G_ERROR_ROLE_UID', - 107 );
define( 'G_ERROR_ROLE_CODE', - 108 );
define( 'G_ERROR_PERMISSION_UID', - 109 );
define( 'G_ERROR_PERMISSION_CODE', - 110 );
define( 'G_ERROR_USER_UID', - 111 );
define( 'G_ERROR_USER_USERNAME', - 112 );
define( 'G_ERROR_USERNAME_EMPTY', - 113 );
define( 'G_ERROR_PASSWORD_EMPTY', - 114 );
define( 'G_ERROR_PASSWORD_INCORRECT', - 115 );
define( 'G_ERROR_USER_INACTIVE', - 116 );
define( 'G_ERROR_DUE_DATE', - 117 );
define( 'G_ERROR_ALREADY_ASSIGNED', - 118 );

/**
 *
 * @package gulliver.system
 *
 */

class G_Error extends PEAR_Error
{

    /**
     * G_Error constructor
     *
     * @param mixed $code error code, or string with error message
     * @param int $mode what "error mode" to operate in
     * @param int $level what error level to use for $mode &PEAR_ERROR_TRIGGER
     * @param mixed $debuginfo additional debug info, such as the last query
     *
     * @see PEAR_Error
     */
    public function G_Error ($code = G_ERROR, $mode = PEAR_ERROR_RETURN, $level = E_USER_NOTICE, $debuginfo = null)
    {
        if (is_int( $code )) {
            $this->PEAR_Error( 'G Error: ' . G_Error::errorMessage( $code ), $code, $mode, $level, $debuginfo );
        } else {
            $this->PEAR_Error( "G Error: $code", DB_ERROR, $mode, $level, $debuginfo );
        }
    }

    /**
     * this function returns the kind of Error
     *
     * @author
     *
     * @access public
     * @param string $code
     * @return string
     *
     */
    public function errorMessage ($code)
    {
        static $gErrorMessages;
        if ($code < 0 && $code > - 100) {
            return DB::errorMessage( $code );
        } else {
            if (! isset( $gErrorMessages )) {
                $gErrorMessages = array (G_ERROR => 'unknown error',DB_ERROR_FEATURE_NOT_AVAILABLE => 'this function or feature is not available for this database engine',DB_ERROR_OBJECT_NOT_DEFINED => 'Object or variable not defined',G_ERROR_WARNING_MESSAGE => 'Warning message'
                );
            }
            /*
            if (DB::isError($code)) {
              $code = $code->getCode();
            }
            */
            return isset( $gErrorMessages[$code] ) ? $gErrorMessages[$code] : (isset( $errorMessages ) ? $errorMessages['G_ERROR'] : '');
        }
    }
}

