<?php
/**
 * methodsPermissions_Ajax.php
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

define( 'GET_PERMISSION_REG_EXP', '/(G::\\s*genericForceLogin\\s*\\(\\s*[\'"])(\\w+)([\'"]\\s*,\\s*[\'"].+[\'"],.+\\)\\s*)|(\\$RBAC->userCanAccess\\s*\\(\\s*[\'"])(\\w+)([\'"]\\s*\\))/i' );
define( 'GET_PERMISSION_REG_EXP2', '/\\s*if\\s*\\(\\s*\\(\\s*\\$RBAC_Response\\s*=\\s*\\$RBAC->userCanAccess\\s*\\(\\s*[\'"]\\w+[\'"]\\s*\\)\\s*\\)\\s*!=1\\s*\\)\\s*return(?:.*)?;\\s*/i' );

class phpFile extends webResource
{

    function _get_permissions ($filename)
    {
        $aSource = file( $filename );
        $aOutSource = array ();
        $source = implode( '', $aSource );
        $regExp = GET_PERMISSION_REG_EXP;
        $permissions = array ();
        $lines = array ();
        $len = preg_match_all( $regExp, $source, $matches, PREG_OFFSET_CAPTURE );
        for ($r = 0; $r < $len; $r ++) {
            $match = $matches[0][$r][0];
            $permission = ($matches[2][$r][0] != '') ? $matches[2][$r][0] : $matches[5][$r][0];
            $toPrint = ($matches[2][$r][0] != '') ? (htmlentities( $matches[1][$r][0], ENT_QUOTES, 'utf-8' ) . '<b>' . htmlentities( $matches[2][$r][0], ENT_QUOTES, 'utf-8' ) . '</b>' . htmlentities( $matches[3][$r][0], ENT_QUOTES, 'utf-8' )) : (htmlentities( $matches[4][$r][0], ENT_QUOTES, 'utf-8' ) . '<b>' . htmlentities( $matches[5][$r][0], ENT_QUOTES, 'utf-8' ) . '</b>' . htmlentities( $matches[6][$r][0], ENT_QUOTES, 'utf-8' ));
            $row = $this->_getLine( $aSource, $matches[0][$r][1] );
            if (array_search( $permission, $permissions ) === false) {
                $permissions[] = $permission;
                $lines[] = $row;
            }
            //TODO: Need to htmlencode the rest of the line that is not in match. Ex. < ? php
            if ($row > 0)
                $aOutSource[$row - 1] = str_replace( $match, $toPrint, isset( $aOutSource[$row - 1] ) ? $aOutSource[$row - 1] : $aSource[$row - 1] );
            $aOutSource[$row] = str_replace( $match, $toPrint, isset( $aOutSource[$row] ) ? $aOutSource[$row] : $aSource[$row] );
            if ($row < (sizeof( $aSource ) - 1))
                $aOutSource[$row + 1] = str_replace( $match, $toPrint, isset( $aOutSource[$row + 1] ) ? $aOutSource[$row + 1] : $aSource[$row + 1] );
        }
        ksort( $aOutSource );
        $row0 = 0;
        $html = '';
        foreach ($aOutSource as $row => $line) {
            if (($row - 1) > $row0)
                $html .= $this->_printLine( $row, '...' );
            $html .= $this->_printLine( $row + 1, $line, true, $aSource[$row], $filename . '?' . $row );
            $row0 = $row;
        }
        return array (($html === '') ? 'Dont have RBAC validation!' : ('<table>' . $html . '</table>'),$permissions,$lines
        );
    }

    function get_permissions ($filename)
    {
        $res = $this->_get_permissions( $filename );
        return $res[0];
    }

    function modify_line ($filename, $row, $value)
    {
        $aSource = file( $filename );
        $line = $aSource[$row];
        $nl = (strlen( $line ) >= 2) && (substr( $line, - 2, 2 ) == "\r\n") ? "\r\n" : ((strlen( $line ) >= 1) && (substr( $line, - 1, 1 ) == "\n") ? "\n" : "");
        $aSource[$row] = $value . $nl;
        /*Save change*/
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, implode( '', $aSource ) );
        fclose( $fp );
        /*Format line*/
        $regExp = GET_PERMISSION_REG_EXP;
        $line = $aSource[$row];
        $len = preg_match_all( $regExp, $line, $matches, PREG_OFFSET_CAPTURE );
        for ($r = 0; $r < $len; $r ++) {
            $match = $matches[0][$r][0];
            $toPrint = ($matches[2][$r][0] != '') ? (htmlentities( $matches[1][$r][0], ENT_QUOTES, 'utf-8' ) . '<b>' . htmlentities( $matches[2][$r][0], ENT_QUOTES, 'utf-8' ) . '</b>' . htmlentities( $matches[3][$r][0], ENT_QUOTES, 'utf-8' )) : (htmlentities( $matches[4][$r][0], ENT_QUOTES, 'utf-8' ) . '<b>' . htmlentities( $matches[5][$r][0], ENT_QUOTES, 'utf-8' ) . '</b>' . htmlentities( $matches[6][$r][0], ENT_QUOTES, 'utf-8' ));
            $line = str_replace( $match, $toPrint, $line );
        }
        return array ($line,$aSource[$row]
        );
    }

    function set_header ($filename, $value)
    {
        $aFields = array ("_FILENAME_" => basename( $filename )
        );
        $value = G::replaceDataField( $value, $aFields );
        $aOrigin = file( $filename );
        //It suposse that allway start with <?. or <?php
        $line = $aOrigin[0];
        $nl = (strlen( $line ) >= 2) && (substr( $line, - 2, 2 ) == "\r\n") ? "\r\n" : ((strlen( $line ) >= 1) && (substr( $line, - 1, 1 ) == "\n") ? "\n" : "");

        $codigo = implode( '', $aOrigin );
        $pattern = '/\/\*[\w\W]+\* ' . 'ProcessMaker Open Source' . '[\w\W]+?\*\//i';
        if (preg_match( $pattern, $codigo )) {
            $codigo = preg_replace( $pattern, $value, $codigo );
        } else {
            $aSource = array ();
            $aSource[0] = $aOrigin[0];
            $aSource[1] = $value . $nl;
            for ($r = 1; $r < sizeof( $aOrigin ); $r ++) {
                $aSource[] = $aOrigin[$r];
            }
            $codigo = implode( '', $aSource );
        }
        /*Save change*/
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, $codigo );
        fclose( $fp );
        return $this->get_permissions( $filename );
    }

    function add_permission ($filename, $value)
    {
        $aOrigin = file( $filename );
        //It suposse that allway start with <?. or <?php
        $aSource[0] = $aOrigin[0];
        $line = $aOrigin[0];
        $nl = (strlen( $line ) >= 2) && (substr( $line, - 2, 2 ) == "\r\n") ? "\r\n" : ((strlen( $line ) >= 1) && (substr( $line, - 1, 1 ) == "\n") ? "\n" : "");
        $aSource[1] = $value . $nl;
        for ($r = 1; $r < sizeof( $aOrigin ); $r ++) {
            $aSource[] = $aOrigin[$r];
        }
        /*Save change*/
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, implode( '', $aSource ) );
        fclose( $fp );
        return $this->get_permissions( $filename );
    }

    function _getLine (&$aSource, $pos)
    {
        $i = 1;
        while ($pos > sizeof( $aSource[$i] )) {
            $pos -= strlen( $aSource[$i] );
            $i ++;
        }
        return $i - 1;
    }

    function _printLine ($row, $txt, $editable = false, $editValue = '', $name = '')
    {
        if ($editable) {
            return '<tr><td><input value="X" type="button" style="width:100%;" ' . ' name="' . htmlentities( $name, ENT_QUOTES, 'utf-8' ) . '"' . ' onclick="removeLine(this);"/></td>' . '<td class="treeContent" align="right">' . $row . '</td>
        <td class="treeNode"><span onclick="switchViewEdit(this,this.nextSibling);">' . $txt . '</span>' . '<input class="treeNode"' . ' name="' . htmlentities( $name, ENT_QUOTES, 'utf-8' ) . '"' . ' style="border:none;width:100%;display:none;"' . ' onblur="switchEditView(this.previousSibling,this);"' . ' value="' . htmlentities( $editValue, ENT_QUOTES, 'utf-8' ) . '"/></td></tr>';
        } else {
            return '<tr><td></td>' . '<td class="treeContent" align="right">' . $row . '</td>' . '<td class="treeNode">' . $txt . '</td></tr>';
        }
    }

    function set_permission ($filename, $permission)
    {
        list ($html, $permissions) = $this->_get_permissions( $filename );
        if (array_search( $permission, $permissions ) === false) {
            $this->add_permission( $filename, 'if (($RBAC_Response=$RBAC->userCanAccess("' . $permission . '"))!=1) return $RBAC_Response;' );
        }
        return $this->get_permissions( $filename );
    }

    function set_path_permission ($path, $permission)
    {
        $files = glob( $path . '*.php' );
        foreach ($files as $file) {
            $this->set_permission( $file, $permission );
        }
    }

    function set_path_header ($path, $header)
    {
        $files = glob( $path . '*.php' );
        $filesMod = array ();
        foreach ($files as $file) {
            $filesMod[] = $file;
            $this->set_header( $file, $header );
        }
        $dirs = glob( $path . '*', GLOB_MARK );
        foreach ($dirs as $dir) {
            if (substr( $dir, - 1, 1 ) == '/')
                $this->set_path_header( $dir, $header );
        }
        return $filesMod;
    }

    function remove_path_permission ($path, $permission)
    {
        $files = glob( $path . '*.php' );
        foreach ($files as $file) {
            $this->remove_permission( $file, $permission );
        }
    }

    function remove_line ($filename, $line)
    {
        $aSource = file( $filename );
        unset( $aSource[$line] );
        /*Save change*/
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, implode( '', $aSource ) );
        fclose( $fp );
        return $this->get_permissions( $filename );
    }

    function remove_permission ($filename, $permission)
    {
        $aSource = file( $filename );
        list ($html, $permissions, $lines) = $this->_get_permissions( $filename );
        if (($row = array_search( $permission, $permissions )) !== false) {
            $line = $lines[$row];
            if (preg_match( GET_PERMISSION_REG_EXP2, $aSource[$line] )) {
                unset( $aSource[$line] );
                $msg = "Removed.";
            } else {
                $msg = "Can not be removed!";
            }
        }
        /*Save change*/
        $fp = fopen( $filename, 'w' );
        fwrite( $fp, implode( '', $aSource ) );
        fclose( $fp );
        return $this->get_permissions( $filename );
    }
}
$phpFile = new phpFile( 'methodsPermissions_Ajax', $_POST );

