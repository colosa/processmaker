<?php
/**
 * error404.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */
$session = session_id();
if (empty($session)) {
    session_start();
}

if ( isset ( $_SESSION['phpFileNotFound'] ) ) {
    $uri = $_SESSION['phpFileNotFound'];
} elseif ( isset ( $_GET['l'] ) ){
    $uri = htmlentities($_GET['l'], ENT_QUOTES, "UTF-8");
} else {
    $uri = 'undefined';
}
$referer =  isset ( $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] :
    (isset($_SESSION['phpLastFileFound']) ? $_SESSION['phpLastFileFound'] : '');

$ERROR_TEXT = "404 Not Found ";
$ERROR_DESCRIPTION = "
    Your browser (or proxy) sent a request
    that this server could not understand.<br />
    <br />
    <strong>Possible reasons: </strong><br />
    Your link is broken. This may occur when you receive
    a link via email, but your client software adds line breaks, thus distorting
    long URLs. <br />
    <br />
    The page you requested is no longer active. <br />
    <br />
    There is a typographic
    error in the link, in case you entered the URL into the browser's address
    toolbar.<br />
    <br />
    <br />
    <table>
    <tr><td><small>url</small></td>    <td><small>$uri</small></td></tr>
    <tr><td><small>referer</small></td><td><small>$referer</small></td></tr>
    </table>
 ";

$fileHeader = PATH_GULLIVER_HOME . 'methods/errors/header.php' ;
include ( $fileHeader);

