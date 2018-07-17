<?php
/**
 * dvServices.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
/*
 * Created on 11-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */


class dvServices extends WebResource
{

    function get_session_vars ()
    {
        $cur = array_keys( $_SESSION );
        $res = '';
        foreach ($cur as $key) {
            $res .= '* ' . $key . '<br/>';
        }
        return $res;
    }

    function get_session_xmlforms ()
    {
        $cur = array_keys( $_SESSION );
        $res = '';
        $colors = array ('white','#EEFFFF'
        );
        $colori = 0;
        $count = 0;
        //Get xmlforms in session
        foreach ($cur as $key) {
            $res .= '<div style="background-color:' . $colors[$colori] . ';">';
            $xml = G::getUIDName( $key, '' );
            if (strpos( $xml, '.xml' ) !== false) {
                $res .= '<i>FORM:</i>  ' . $xml;
                $colori = $colori ^ 1;
                $count ++;
            }
            $res .= '</div>';
        }
        //Get pagedTable in session
        foreach ($cur as $key) {
            $res .= '<div style="background-color:' . $colors[$colori] . ';">';
            if (substr( $key, 0, 11 ) === "pagedTable[") {
                $xml = G::getUIDName( substr( $key, 11, - 1 ), '' );
                $res .= '<i>TABLE:</i> ' . $xml;
                $colori = $colori ^ 1;
                $count ++;
            }
            $res .= '</div>';
        }
        return array ("count" => $count,"html" => $res
        );
    }
}
$o = new dvServices( $_SERVER['REQUEST_URI'], $_POST );
//av.buenos aires maxparedes
//tienda viva.
//122

