<?php

/**
 * class.xmlDb.php
 *
 * @package workflow.engine.ProcessMaker
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
 * XMLDB
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */
class XMLDB
{

    /**
     * &connect
     *
     * @param string $dsn
     * @return array $options
     */
    public function &connect ($dsn, $options = array())
    {
        //Needed for $mysql_real_escape_string
        $mresdbc = new DBConnection();

        if (! file_exists( $dsn )) {
            $err = new DB_Error( "File $dsn not found." );
            return $err;
        }
        $dbc = new XMLConnection( $dsn );
        return $dbc;
    }

    /**
     * isError
     *
     * @param string $result
     * @return boolean is_a($result, 'DB_Error')
     */
    public function isError ($result)
    {
        return is_a( $result, 'DB_Error' );
    }
}

/**
 * XMLConnection
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */
class XMLConnection
{
    var $phptype = 'myxml';
    var $caseFolding = true;
    var $xmldoc = null;
    var $xmlFile = '';

    /**
     * XMLConnection
     *
     * @param string $file
     * @return void
     */
    public function XMLConnection ($file)
    {
        $this->xmldoc = new Xml_Document();
        $this->xmldoc->parseXmlFile( $file );
        $this->xmlFile = $file;
    }

    /**
     * &query
     * Actualy the only one supported query is simple SELECT.
     *
     * @param string $sql
     * @return object(XMLResult) $result
     */
    public function &query ($sql)
    {
        if (! isset( $this->xmldoc )) {
            $err = new DB_Error( "Error: Closed xmlConnection." );
            return $err;
        }
        if (1 === preg_match( '/^\s*SELECT\s+([\w\W]+?)(?:\s+FROM\s+`?([^`]+?)`?)(?:\s+WHERE\s+([\w\W]+?))?(?:\s+GROUP\s+BY\s+([\w\W]+?))?(?:\s+ORDER\s+BY\s+([\w\W]+?))?(?:\s+BETWEEN\s+([\w\W]+?)\s+AND\s+([\w\W]+?))?(?:\s+LIMIT\s+(\d+)\s*,\s*(\d+))?\s*$/im', $sql, $matches )) {
            $sqlColumns = $matches[1];
            $sqlFrom = isset( $matches[2] ) ? $matches[2] : '';
            $sqlWhere = isset( $matches[3] ) ? $matches[3] : '';
            $sqlGroupBy = isset( $matches[4] ) ? $matches[4] : '';
            $sqlOrderBy = isset( $matches[5] ) ? $matches[5] : '';
            $sqlLowLimit = isset( $matches[8] ) ? $matches[8] : '';
            $sqlHighLimit = isset( $matches[9] ) ? $matches[9] : '';
            /* Start Block: Fields list */
            $count = preg_match_all( '/\s*(\*|[\w\.]+)(?:\s+AS\s+([\w\.]+))?/im', $sqlColumns, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
            $fieldsList = array ();
            for ($r = 0; $r < $count; $r ++) {
                $name = (is_array( $match[2][$r] ) && $match[2][$r][0] !== '') ? $match[2][$r][0] : $match[1][$r][0];
                $fieldsList[$name] = $match[1][$r][0];
            }
            /* End Block */
            /* Start Block: Order list */
            $count = preg_match_all( '/\s*(\*|[\w\.]+)(\s+ASC|\s+DESC)?\s*,?/im', $sqlOrderBy, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
            $orderList = array ();
            for ($r = $count - 1; $r >= 0; $r --) {
                $direction = (is_array( $match[2][$r] ) && $match[2][$r][0] !== '') ? $match[2][$r][0] : 'ASC';
                $direction = strtoupper( $direction );
                $orderList[$match[1][$r][0]] = $direction;
            }
            /* End Block */
            $xmlFrom = '/' . str_replace( '.', '/', $sqlFrom );
            $node = & $this->xmldoc->findNode( $xmlFrom );
            if (! isset( $node )) {
                //$err = new DB_Error( "$xmlFrom node not found in $dsn." );
                throw new Exception( "$xmlFrom node not found in " . $this->xmlFile . "." );
                return $err;
            } else {
                $res = $this->fetchChildren( $node );
            }
            /* Start Block: WHERE*/
            if ($sqlWhere !== '') {
                /*Start Block: Replace the operator */
                $blocks = preg_split( '/("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', $sqlWhere, - 1, PREG_SPLIT_DELIM_CAPTURE );
                $sqlWhere = '';
                for ($r = 0; $r < sizeof( $blocks ); $r ++) {
                    if (($r % 2) === 0) {
                        $blocks[$r] = str_replace( '=', '==', $blocks[$r] );
                        $blocks[$r] = str_replace( '<>', '!=', $blocks[$r] );
                        $blocks[$r] = str_replace( 'AND', '&&', $blocks[$r] );
                        $blocks[$r] = str_replace( 'and', '&&', $blocks[$r] );
                        $blocks[$r] = str_replace( 'OR', '||', $blocks[$r] );
                        $blocks[$r] = str_replace( 'or', '||', $blocks[$r] );
                        $blocks[$r] = str_replace( 'NOT', '!', $blocks[$r] );
                        $blocks[$r] = str_replace( 'not', '!', $blocks[$r] );
                        $blocks[$r] = preg_replace( '/\b[a-zA-Z_][\w\.]*\b/im', '$res[$r][\'$0\']', $blocks[$r] );
                        $blocks[$r] = preg_replace( '/\$res\[\$r\]\[\'(like)\'\]/im', '$1', $blocks[$r] );
                    }
                    $sqlWhere .= $blocks[$r];
                }
                $sqlWhere = preg_replace_callback( '/(.+)\s+like\s+("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', array ('XMLConnection','sqlWhereLike'
                ), $sqlWhere );
                $sqlWhere = preg_replace_callback( '/"(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'/im', array ('XMLConnection','sqlString'
                ), $sqlWhere );
                $newRes = array ();
                for ($r = 0; $r < sizeof( $res ); $r ++) {
                    $evalWhere = false;
                    @eval( '$evalWhere = ' . $sqlWhere . ';' );
                    if ($evalWhere) {
                        $newRes[] = $res[$r];
                    }
                }
                $res = $newRes;
            }
            /* End Block */
            /* Start Block: Expands the resultant data according to fill an array
             *              with the required fields in the query.
             */
            for ($r = 0; $r < sizeof( $res ); $r ++) {
                $res[$r] = $this->expandFields( $res[$r], $fieldsList );
            }
            /* End Block */
            /* Start Block: ORDER BY*/
            foreach ($orderList as $field => $direction) {
                for ($i = 0; $i < sizeof( $res ); $i ++) {
                    for ($j = $i + 1; $j < sizeof( $res ); $j ++) {
                        $condition = ($direction === 'ASC') ? ($res[$j] < $res[$i]) : ($res[$j] > $res[$i]);
                        if ($condition) {
                            $swap = $res[$i];
                            $res[$i] = $res[$j];
                            $res[$j] = $swap;
                        }
                    }
                }
            }
            /* End Block */
            /* Start Block: Apply limits */
            if ($sqlLowLimit != '' && $sqlHighLimit != '') {
                $sqlLowLimit = (int) $sqlLowLimit;
                $sqlHighLimit = (int) $sqlHighLimit;
                $res = array_slice( $res, $sqlLowLimit, $sqlHighLimit );
            }
            /* End Block */
            $result = new XMLResult( $res );
            return $result;
        } elseif (1 === preg_match( '/^\s*DELETE\s+FROM\s+`?([^`]+?)`?(?:\s+WHERE\s+([\w\W]+?))?\s*$/im', $sql, $matches )) {
            $sqlFrom = isset( $matches[1] ) ? $matches[1] : '';
            $sqlWhere = isset( $matches[2] ) ? $matches[2] : '1';
            /* Start Block: WHERE*/
            /*Start Block: Replace the operator */
            $blocks = preg_split( '/("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', $sqlWhere, - 1, PREG_SPLIT_DELIM_CAPTURE );
            $sqlWhere = '';
            for ($r = 0; $r < sizeof( $blocks ); $r ++) {
                if (($r % 2) === 0) {
                    $blocks[$r] = str_replace( '=', '==', $blocks[$r] );
                    $blocks[$r] = str_replace( '<>', '!=', $blocks[$r] );
                    $blocks[$r] = str_replace( 'AND', '&&', $blocks[$r] );
                    $blocks[$r] = str_replace( 'and', '&&', $blocks[$r] );
                    $blocks[$r] = str_replace( 'OR', '||', $blocks[$r] );
                    $blocks[$r] = str_replace( 'or', '||', $blocks[$r] );
                    $blocks[$r] = str_replace( 'NOT', '!', $blocks[$r] );
                    $blocks[$r] = str_replace( 'not', '!', $blocks[$r] );
                    $blocks[$r] = preg_replace( '/\b[a-zA-Z_][\w\.]*\b/im', '$res[$r][\'$0\']', $blocks[$r] );
                    $blocks[$r] = preg_replace( '/\$res\[\$r\]\[\'(like)\'\]/im', '$1', $blocks[$r] );
                }
                $sqlWhere .= $blocks[$r];
            }
            /* End Block */
            $sqlWhere = preg_replace_callback( '/(.+)\s+like\s+("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', array ('XMLConnection','sqlWhereLike'
            ), $sqlWhere );
            $sqlWhere = preg_replace_callback( '/"(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'/im', array ('XMLConnection','sqlString'
            ), $sqlWhere );

            /*Start Block: Removing fields */
            $xmlFrom = '/' . str_replace( '.', '/', $sqlFrom );
            $node = & $this->xmldoc->findNode( $xmlFrom );
            if (! isset( $node )) {
                $err = new DB_Error( "$xmlFrom node not found!." );
                return $err;
            } else {
                $res = $this->fetchChildren( $node );
            }
            $newRes = array ();
            for ($r = 0; $r < sizeof( $res ); $r ++) {
                $evalWhere = false;
                @eval( '$evalWhere = ' . $sqlWhere . ';' );
                if ($evalWhere) {
                    unset( $node->children[$r] );
                    $newRes[] = $res[$r];
                }
            }
            //Re-index
            $node->children = array_values( $node->children );
            /* End Block */
            $this->xmldoc->save( $this->xmlFile );
            $result = new XMLResult( $newRes );
            return $result;
        } elseif (1 === preg_match( '/^\s*INSERT\s+INTO\s+`?([^`]+?)`?\s*\(([\w\W]+?)\)\s+VALUES\s*\(([\w\W]+?)\)\s*$/im', $sql, $matches )) {
            $sqlFrom = isset( $matches[1] ) ? $matches[1] : '';
            $sqlColumns = isset( $matches[2] ) ? $matches[2] : '1';
            $sqlValues = isset( $matches[3] ) ? $matches[3] : '1';
            $xmlFrom = '/' . str_replace( '.', '/', $sqlFrom );
            $node = & $this->xmldoc->findNode( $xmlFrom );
            /* Start Block: Fields list */
            $count = preg_match_all( '/([\w\.]+)/im', $sqlColumns, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
            $fieldsList = array ();
            for ($r = 0; $r < $count; $r ++) {
                $fieldsList[] = $match[1][$r][0];
            }
            /* End Block */
            /* Start Block: Fields Values */
            $count = preg_match_all( '/("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'|\d+)/im', $sqlValues, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
            $fieldsValues = array ();
            for ($r = 0; $r < $count; $r ++) {
                if (substr( $match[1][$r][0], 0, 1 ) === '"') {
                    $match[1][$r][0] = substr( $match[1][$r][0], 1, - 1 );
                    $match[1][$r][0] = str_replace( '""', '"', $match[1][$r][0] );
                    $match[1][$r][0] = str_replace( "''", "'", $match[1][$r][0] );
                }
                if (substr( $match[1][$r][0], 0, 1 ) === "'") {
                    $match[1][$r][0] = substr( $match[1][$r][0], 1, - 1 );
                    $match[1][$r][0] = str_replace( "''", "'", $match[1][$r][0] );
                    $match[1][$r][0] = str_replace( '""', '"', $match[1][$r][0] );
                }
                $fieldsValues[$fieldsList[$r]] = $match[1][$r][0];
            }
            /* End Block */
            $AAA = getNames( $this->xmldoc->children[0]->children );
            $this->insertRow( $node, $fieldsValues );
            $DDD = getNames( $this->xmldoc->children[0]->children );
            $this->xmldoc->save( $this->xmlFile );
            $result = new XMLResult( array ($fieldsValues
            ) );
            return $result;
        } elseif (1 === preg_match( '/^\s*UPDATE\s+`?([^`]+?)`?\s+SET\s+((?:(?:[a-z][\w\.]*)\s*=\s*(?:"(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'|\d+)\s*(?:,\s*)?)+)(?:\s+WHERE\s+([\w\W]+?))?\s*$/im', $sql, $matches )) {
            $sqlFrom = isset( $matches[1] ) ? $matches[1] : '';
            $sqlColumns = isset( $matches[2] ) ? $matches[2] : '';
            $sqlWhere = isset( $matches[3] ) ? $matches[3] : '1';
            $count = preg_match_all( '/([a-z][\w\.]*)\s*=\s*("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'|\d+)/im', $sqlColumns, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
            $fieldsValues = array ();
            for ($r = 0; $r < $count; $r ++) {
                if (substr( $match[2][$r][0], 0, 1 ) === '"') {
                    $match[2][$r][0] = substr( $match[2][$r][0], 1, - 1 );
                    $match[2][$r][0] = str_replace( '""', '"', $match[2][$r][0] );
                    $match[2][$r][0] = str_replace( "''", "'", $match[2][$r][0] );
                }
                if (substr( $match[2][$r][0], 0, 1 ) === "'") {
                    $match[2][$r][0] = substr( $match[2][$r][0], 1, - 1 );
                    $match[2][$r][0] = str_replace( "''", "'", $match[2][$r][0] );
                    $match[2][$r][0] = str_replace( '""', '"', $match[2][$r][0] );
                }
                $fieldsValues[$match[1][$r][0]] = $match[2][$r][0];
            }
            /* Start Block: WHERE*/
            /*Start Block: Replace the operator */
            $blocks = preg_split( '/("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', $sqlWhere, - 1, PREG_SPLIT_DELIM_CAPTURE );
            $sqlWhere = '';
            for ($r = 0; $r < sizeof( $blocks ); $r ++) {
                if (($r % 2) === 0) {
                    $blocks[$r] = str_replace( '=', '==', $blocks[$r] );
                    $blocks[$r] = str_replace( '<>', '!=', $blocks[$r] );
                    $blocks[$r] = str_replace( 'AND', '&&', $blocks[$r] );
                    $blocks[$r] = str_replace( 'and', '&&', $blocks[$r] );
                    $blocks[$r] = str_replace( 'OR', '||', $blocks[$r] );
                    $blocks[$r] = str_replace( 'or', '||', $blocks[$r] );
                    $blocks[$r] = str_replace( 'NOT', '!', $blocks[$r] );
                    $blocks[$r] = str_replace( 'not', '!', $blocks[$r] );
                    $blocks[$r] = preg_replace( '/\b[a-zA-Z_][\w\.]*\b/im', '$res[$r][\'$0\']', $blocks[$r] );
                    $blocks[$r] = preg_replace( '/\$res\[\$r\]\[\'(like)\'\]/im', '$1', $blocks[$r] );
                }
                $sqlWhere .= $blocks[$r];
            }
            /* End Block */
            $sqlWhere = preg_replace_callback( '/(.+)\s+like\s+("(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\')/im', array ('XMLConnection','sqlWhereLike'
            ), $sqlWhere );
            $sqlWhere = preg_replace_callback( '/"(?:(?:[^"]|"")*)"|\'(?:(?:[^\']|\'\')*)\'/im', array ('XMLConnection','sqlString'
            ), $sqlWhere );
            /*Start Block: Removing fields */
            $xmlFrom = '/' . str_replace( '.', '/', $sqlFrom );
            $node = & $this->xmldoc->findNode( $xmlFrom );
            if (! isset( $node )) {
                $err = new DB_Error( "$xmlFrom node not found in $dsn." );
                return $err;
            } else {
                $res = $this->fetchChildren( $node );
            }
            $newRes = array ();
            for ($r = 0; $r < sizeof( $res ); $r ++) {
                $evalWhere = false;
                @eval( '$evalWhere = ' . $sqlWhere . ';' );
                if ($evalWhere) {
                    $this->updateRow( $node->children[$r], $fieldsValues );
                    $newRes[] = array_merge( $res[$r], $fieldsValues );
                }
            }
            /* End Block */
            $nodeTEST = & $this->xmldoc->findNode( $xmlFrom );
            $this->xmldoc->save( $this->xmlFile );
            $result = new XMLResult( $newRes );
            return $result;
        } else {
            echo ($sql);
            $err = new DB_Error( "SQL Query is not well formed." );
            return $err;
        }
    }

    /**
     * sqlLike
     *
     * @param string $a
     * @return void $b
     */
    public function sqlLike ($a, $b)
    {
        $b = addcslashes( $b, '[]()\/{}.?' );
        $b = str_replace( "%", '.*', $b );
        $b = '/^' . $b . '$/im';
        return preg_match( $b, $a );
    }

    /**
     * expandFields
     *
     * @param string $resRow
     * @param string $fieldsList
     * @return array $res
     */
    public function expandFields ($resRow, $fieldsList)
    {
        $res = array ();
        foreach ($fieldsList as $key => $value) {
            if ($key === '*') {
                foreach ($resRow as $k => $v) {
                    $res[$k] = $v;
                }
            } else {
                $res[$key] = array_key_exists( $value, $resRow ) ? $resRow[$value] : null;
            }
        }
        return $res;
    }

    /**
     * fetchNode
     *
     * @param object &$node
     * @return array $res
     */
    public function fetchNode (&$node)
    {
        $res = array ('XMLNODE_NAME' => $node->name,'XMLNODE_TYPE' => $node->type,'XMLNODE_VALUE' => $node->value
        );
        foreach ($node->attributes as $name => $value) {
            if ($this->caseFolding) {
                $name = strtoupper( $name );
            }
            $res[$name] = $value;
        }
        return $res;
    }

    /**
     * fetchChildren
     *
     * @param string &$node
     * @return array $res
     */
    public function fetchChildren (&$node)
    {
        $res = array ();
        foreach ($node->children as $name => $child) {
            $res[] = $this->fetchNode( $child );
        }
        return $res;
    }

    /**
     * disconnect
     *
     * @return void
     */
    public function disconnect ()
    {
        unset( $this->xmldoc );
    }

    /**
     *
     * @param array $match
     * @return object(DB_Error) $err
     */
    public function sqlWhereLike ($match)
    {
        switch (substr( $match[2], 0, 1 )) {
            case '"':
                return ' $this->sqlLike( ' . $match[1] . ', ' . $match[2] . ' ) ';
                break;
            case "'":
                return ' $this->sqlLike( ' . $match[1] . ', ' . $match[2] . ' ) ';
                break;
            default:
                $err = new DB_Error( "XMLDB: Syntax error on $match[0]" );
                die();
                return $err;
        }
    }

    /**
     * sqlString
     *
     * @param array $match
     * @return object(DB_Error) $err
     */
    public function sqlString ($match)
    {
        switch (substr( $match[0], 0, 1 )) {
            case '"':
                $match[0] = substr( $match[0], 1, - 1 );
                $match[0] = str_replace( '""', '"', $match[0] );
                $match[0] = str_replace( "''", "'", $match[0] );
                $match[0] = addcslashes( $match[0], '\\\'' );
                return "'$match[0]'";
                break;
            case "'":
                $match[0] = substr( $match[0], 1, - 1 );
                $match[0] = str_replace( "''", "'", $match[0] );
                $match[0] = str_replace( '""', '"', $match[0] );
                $match[0] = addcslashes( $match[0], '\\\'' );
                return "'$match[0]'";
                break;
            default:
                $err = new DB_Error( "XMLDB: Syntax error on $match[0]" );
                die();
                return $err;
        }
    }

    /**
     * insertRow
     *
     * @param string &$node
     * @param object $values
     * @return void
     */
    public function insertRow (&$node, $values)
    {
        $attributes = array ();
        foreach ($values as $field => $value) {
            switch ($field) {
                case 'XMLNODE_NAME':
                case 'XMLNODE_TYPE':
                case 'XMLNODE_VALUE':
                    break;
                default:
                    $attributes[strtolower( $field )] = $value;
            }
        }
        $values['XMLNODE_NAME'] = ! isset( $values['XMLNODE_NAME'] ) ? '' : $values['XMLNODE_NAME'];
        $values['XMLNODE_TYPE'] = ! isset( $values['XMLNODE_TYPE'] ) ? 'open' : $values['XMLNODE_TYPE'];
        $values['XMLNODE_VALUE'] = ! isset( $values['XMLNODE_VALUE'] ) ? '' : $values['XMLNODE_VALUE'];
        $node->addChildNode( new Xml_Node( $values['XMLNODE_NAME'], $values['XMLNODE_TYPE'], $values['XMLNODE_VALUE'], $attributes ) );
    }

    /**
     * updateRow
     *
     * @param string &$node
     * @param object $values
     * @return void
     */
    public function updateRow (&$node, $values)
    {
        foreach ($values as $field => $value) {
            switch ($field) {
                case 'XMLNODE_NAME':
                    $node->name = $value;
                    break;
                case 'XMLNODE_TYPE':
                    $node->type = $value;
                    break;
                case 'XMLNODE_VALUE':
                    $node->value = $value;
                    break;
                default:
                    $node->attributes[strtolower( $field )] = $value;
            }
        }
    }
}

/**
 * XMLResult
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */
class XMLResult
{
    var $result = array ();
    var $cursor = 0;

    /**
     * XMLResult
     *
     * @param array $result
     * @return void
     */
    public function XMLResult ($result = array())
    {
        $this->result = $result;
        $this->cursor = 0;
    }

    /**
     * numRows
     *
     * @return integer sizeof($this->result)
     */
    public function numRows ()
    {
        return sizeof( $this->result );
    }

    /**
     * fetchRow
     *
     * @param string $const
     * @return integer $this->result[ $this->cursor-1 ];
     */
    public function fetchRow ($const)
    {
        if ($this->cursor >= $this->numRows()) {
            return null;
        }
        $this->cursor ++;
        return $this->result[$this->cursor - 1];
    }
}

/**
 * getNames
 *
 * @param object $children
 * @return array $names
 */
function getNames ($children)
{
    $names = array ();
    $r = 0;
    foreach ($children as $child) {
        $names[$r] = $child->name;
        $r ++;
    }
    return $names;
}

