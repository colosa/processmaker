<?php

/**
 * class.table.php
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
 *
 *
 *
 *
 *
 * Table class definition
 * Render table
 *
 * @package gulliver.system
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */

class Table
{
    var $Columns = null;
    var $Labels = null;
    var $rows_per_page = 25;
    var $show_nummbers = null;
    var $first_row = 0;
    var $row_pos = 0;
    var $Action = ""; //not used
    var $ActionLabel = "Continuar"; //not used
    var $_dbc = null;
    var $_dbses = null;
    var $_dbset = null;
    var $_source = "";
    var $DefaultOrder = "UID";
    var $DefaultOrderDir = 'ASC';
    var $CustomOrder = "";
    var $WhereClause = "";
    var $_row_values = null;
    var $_ordered = true;
    var $orderprefix = "";
    var $CountQry = "";
    var $filtro = 1;
    var $title = '';

    /**
     * Asocia un arreglo con valores de traducci?n/conversi?n a un contexto
     *
     * @var array
     */
    var $contexto = null;

    /**
     * Arreglo que contiene las cadenas que van a ser usadas al traducir/convertir
     *
     * @var array
     */
    var $translate = null;

    /**
     * Establece el ?ltimo contexto utilizado
     *
     * @var string
     */
    var $_contexto = '';

    /**
     * Set conecction using default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $objConnection connection string
     * @return void
     */
    function Table ($objConnection = null)
    {
        $this->SetTo( $objConnection );
    }

    /**
     * Set conecction using default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $objConnection connection string
     * @return void
     */
    function SetTo ($objConnection = null)
    {
        $this->_dbc = $objConnection;
    }

    /**
     * Set query string
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $stQry query string
     * @param string $stDefaultOrder index to order by, default value='UID'
     * @return void
     */
    function SetSource ($stQry = "", $stDefaultOrder = "UID", $stDefaultOrderDir = 'ASC')
    {
        //to fix missing value for variable orderDir, when between pages changes.
        $url1 = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?';
        $url2 = strstr( $_SERVER['HTTP_REFERER'] . '?', $_SERVER['HTTP_HOST'] );
        $url1 = substr( $url1, 0, strpos( $url1, '?' ) );
        $url2 = substr( $url2, 0, strpos( $url2, '?' ) );
        if ($url1 != $url2) {
            if (isset( $_SESSION['OrderBy'] )) {
                unset( $_SESSION['OrderBy'] );
            }
            if (isset( $_SESSION['OrderDir'] )) {
                unset( $_SESSION['OrderDir'] );
            }
        }
        $this->_source = $stQry;
        $this->DefaultOrder = $stDefaultOrder;
        $this->DefaultOrderDir = $stDefaultOrderDir;
    }

    /**
     * Obtains query string asociated
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function GetSource ()
    {
        global $HTTP_GET_VARS;
        global $HTTP_SESSION_VARS;
        $stOrderByDir = $this->DefaultOrderDir;
        if (isset( $HTTP_SESSION_VARS['OrderDir'] ) && ($HTTP_SESSION_VARS['OrderDir'] == 'DESC' || $HTTP_SESSION_VARS['OrderDir'] == 'ASC')) {
            $stOrderByDir = $HTTP_SESSION_VARS['OrderDir'];
        }

        $stQry = $this->_source;
        if ($this->WhereClause != "") {
            $stQry .= " WHERE " . $this->WhereClause;
        }

        if ($this->_ordered == true) {
            $stOrderBy = (isset( $HTTP_GET_VARS[$this->orderprefix . 'order'] ) ? $HTTP_GET_VARS[$this->orderprefix . 'order'] : '');
            $stOrderLb = (isset( $HTTP_GET_VARS[$this->orderprefix . 'label'] ) ? $HTTP_GET_VARS[$this->orderprefix . 'label'] : '');

            //if( isset( $HTTP_SESSION_VARS['OrderDir'] ) && $HTTP_SESSION_VARS['OrderDir'] == $stOrderBy ) {
            if ($stOrderLb) {
                if ($HTTP_SESSION_VARS['OrderDir'] == 'ASC') {
                    $stOrderByDir = 'DESC';
                } elseif ($HTTP_SESSION_VARS['OrderDir'] == 'DESC') {
                    $stOrderByDir = 'ASC';
                }
            } elseif (isset( $HTTP_SESSION_VARS['OrderDir'] ) && $HTTP_SESSION_VARS['OrderDir'] != '') {
                $stOrderByDir = $HTTP_SESSION_VARS['OrderDir'];
            } else {
                $stOrderByDir = $this->DefaultOrderDir;
            }

            if ($stOrderBy == "") {
                if ($this->DefaultOrder != "") {
                    $aux = str_replace( ' ASC|', '', $this->DefaultOrder . '|' );
                    $aux = str_replace( ' DESC|', '', $aux );
                    $aux = str_replace( '|', '', $aux );
                    $stQry .= " ORDER BY " . $aux . " " . $stOrderByDir;
                }
            } else {
                $stQry .= " ORDER BY " . $stOrderBy;
                if ($stOrderByDir != "") {
                    $stQry .= "  $stOrderByDir";

                }
            }
        } else {
            if ($this->DefaultOrder != "") {
                $stQry .= " ORDER BY " . $this->DefaultOrder . "  " . (isset( $stOrderBy ) ? $stOrderBy : '');
            }
        }
        //print $stQry;


        $HTTP_SESSION_VARS['OrderBy'] = isset( $stOrderBy ) ? $stOrderBy : '';
        $HTTP_SESSION_VARS['OrderDir'] = $stOrderByDir;

        $page = (isset( $HTTP_GET_VARS["page"] ) ? $HTTP_GET_VARS["page"] : '');

        $tr = (isset( $HTTP_SESSION_VARS['TP'] ) ? $HTTP_SESSION_VARS['TP'] : '');

        $desde = 0;

        if ($page != "") {
            //$desde=(($page-1)*25);
            $desde = (($page - 1) * $this->rows_per_page);

            //$strLimit = " LIMIT $desde , 25";
            $strLimit = " LIMIT $desde , $this->rows_per_page";
            if (PEAR_DATABASE == 'pgsql') {
                //$strLimit = " OFFSET $desde LIMIT 25";
                $strLimit = " OFFSET $desde LIMIT $this->rows_per_page";
            }
            $stQry .= $strLimit;
        }

        //print $stQry;
        $this->_dbses = new DBSession( $this->_dbc );
        $this->_dbses->UseDB( DB_NAME );
        $this->_dbses->Query( $stQry );
        $this->_dbset = new DBRecordset( $this->_dbses->result );
    }

    /**
     * Obtains number of elements of asociated query
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return int
     */
    function TotalCount ()
    {
        global $HTTP_GET_VARS;
        global $HTTP_SESSION_VARS;

        $stQry = $this->_source;
        if ($this->WhereClause != "") {
            $stQry .= " WHERE " . $this->WhereClause;
        }
        if ($this->_ordered == true) {
            $stOrderBy = (isset( $HTTP_GET_VARS[$this->orderprefix . 'order'] ) ? $HTTP_GET_VARS[$this->orderprefix . 'order'] : '');
            if ($stOrderBy == "") {
                if ($this->DefaultOrder != "") {
                    $stQry .= " ORDER BY " . $this->DefaultOrder;
                }
            } else {
                $stQry .= " ORDER BY " . $stOrderBy;
            }
        } else {
            if ($this->DefaultOrder != "") {
                $stQry .= " ORDER BY " . $this->DefaultOrder;
            }
        }

        $dbses = new DBSession( $this->_dbc );
        $dbses->UseDB( DB_NAME );
        $dset = $dbses->Execute( $stQry );
        return $dset->Count();
    }

    /**
     * Obtains number of elements of asociated recordset
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return int
     */
    function Count ()
    {
        if (is_object( $this->_dbset )) {
            return $this->_dbset->Count();
        } else {
            return 0;
        }
    }

    /**
     * Obtains row position
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return int
     */
    function CurRow ()
    {
        return $this->row_pos;
    }

    /**
     * Obtains number columns
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return int
     */
    function ColumnCount ()
    {
        $result = 0;
        if (is_array( $this->Columns )) {
            $result = count( $this->Columns );
        }
        return $result;
    }

    /**
     * Obtains a row array and moves the internal data pointer ahead
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return array
     */
    function Read ()
    {
        $this->_row_values = $this->_dbset->Read();
        $this->row_pos ++;
        return $this->_row_values;
    }

    /**
     * Moves the internal row pointer
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param int $intPos position to seek
     * @return int
     */
    function Seek ($intPos = 0)
    {
        $result = $this->_dbset->Seek( $intPos );
        if ($result) {
            $this->row_pos = $intPos;
        }
        return $result;
    }

    /**
     * Moves the internal row pointer to first position
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return int
     */
    function MoveFirst ()
    {
        if ($this->Count() != 0) {
            if ($this->first_row < $this->Count()) {
                $this->Seek( $this->first_row );
            }
        }
    }

    /**
     * Verify if row position is in the end
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     */
    function EOF ()
    {
        $result = false;
        if ($this->Count() == 0) {
            $result = true;
        } else {
            if ($this->row_pos >= $this->Count()) {
                $result = true;
            } else {
                if ($this->rows_per_page != 0) {
                    if ($this->row_pos >= $this->first_row + $this->rows_per_page) {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Set values to add a column to show in the dynaform
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $strLabel
     * @param $strType
     * @param $strName
     * @param $strAlign
     * @param $intWidth
     * @param $strTarget
     * @param $strContent
     * @return void
     */
    function AddColumn ($strLabel = "", $strType = "text", $strName = "", $strAlign = "left", $intWidth = 0, $strTarget = "", $strContent = "")
    {
        $tmpCol = array ("Name" => $strName,"Type" => $strType,"Width" => $intWidth,"Align" => $strAlign,"Target" => $strTarget,"Content" => $strContent
        );
        $pos = 0;
        if (is_array( $this->Columns )) {
            $pos = count( $this->Columns );
        }
        $this->Columns[$pos] = $tmpCol;
        $this->Labels[$pos] = $strLabel;
    }

    /**
     * Set values to add a column to show in the dynaform
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $strType
     * @param $strName
     * @param $strAlign
     * @param $intWidth
     * @param $strTarget
     * @param $strContent
     * @param $strExtra
     * @param $strCondition
     * @param $orderByThis
     * @return void
     */
    function AddRawColumn ($strType = "text", $strName = "", $strAlign = "left", $intWidth = 0, $strTarget = "", $strContent = "", $strExtra = "", $strCondition = "", $orderByThis = true)
    {
        $tmpCol = array ("Name" => $strName,"Type" => $strType,"Width" => $intWidth,"Align" => $strAlign,"Target" => $strTarget,"Content" => $strContent,"Extra" => $strExtra,"Condition" => $strCondition,"orderByThis" => $orderByThis
        );
        $pos = 0;
        if (is_array( $this->Columns )) {
            $pos = count( $this->Columns );
        }
        $this->Columns[$pos] = $tmpCol;
        $this->Labels[$pos] = "";
    }

    /**
     * Show dynaform's title
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $pa
     * @param $intPos
     * @param $strClass
     * @return void
     */
    function RenderTitle ($pa, $intPos = 1, $strClass = "tblHeader")
    {
        if (! defined( 'ENABLE_ENCRYPT' )) {
            define( 'ENABLE_ENCRYPT', 'no' );
        }
        global $HTTP_SESSION_VARS;
        $col = $this->Columns[$intPos];
        $order = ! ($col["Type"] == "image");
        if ($this->_ordered == true && $order) {
            $res = "<th class=\"$strClass\" align=\"left\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">";

            //$res .= "<a class=\"" . $strClass . "Link\" href=\"";
            $res .= "<a class=\"" . $strClass . "\" href=\"";
            $res .= (ENABLE_ENCRYPT == 'yes' ? str_replace( G::encrypt( 'sys' . SYS_SYS, URL_KEY ), SYS_SYS, G::encryptUrl( urldecode( SYS_CURRENT_URI ), URL_KEY ) ) : SYS_CURRENT_URI) . "?order=" . $this->Columns[$intPos]['Name'] . "&page=" . $pa . "&label=true";
            //$res .= $_SERVER['REDIRECT_URL'] . "?order=" . $this->Columns[$intPos]['Name']."&page=".$pa."&label=true";
            $res .= "\">" . $this->Labels[$intPos] . "</a>";

            $res .= "</th>\n"; //echo $res;die;
        } else {
            $res = "<th class=\"$strClass\" align=\"left\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">";
            $res .= $this->Labels[$intPos] . "</th>\n";
        }
        return $res;
    }

    /**
     * Show dynaform's title using ajax
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $pa
     * @param $intPos
     * @param $strClass
     * @return void
     */
    function RenderTitle_ajax ($pa, $intPos = 1, $strClass = "tblHeader")
    {
        global $HTTP_SESSION_VARS;
        $col = $this->Columns[$intPos];
        $order = ! (($col["Type"] == "image") || ($col["Type"] == "jsimglink"));

        if ($this->_ordered == true && $order) {
            $res = "<th class=\"$strClass\" align=\"left\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">";

            //$res .= "<a class=\"" . $strClass . "Link\" href=\"";
            $res .= "<a class=\"" . $strClass . "\" href=\"";
            $_temp_var = $this->Columns[$intPos]['Name'];
            $res .= "Javascript:changetableOrder('$_temp_var',$pa)";
            //$res .= $_SERVER['REDIRECT_URL'] . "?order=" . $this->Columns[$intPos]['Name']."&page=".$pa."&label=true";
            $res .= "\">" . $this->Labels[$intPos] . "</a>";
            if ($HTTP_SESSION_VARS['OrderBy'] == $this->Columns[$intPos]['Name']) {
                if ($HTTP_SESSION_VARS['OrderDir'] == 'DESC') {
                    $res .= "&nbsp;<img src='/images/arrow_order_desc.gif' border=0>";
                } else {
                    $res .= "&nbsp;<img src='/images/arrow_order_asc.gif' border=0>";
                }
            }

            $res .= "</th>\n"; //echo $res;die;
        } else {
            $res = "<th class=\"$strClass\" align=\"left\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">";
            $res .= $this->Labels[$intPos] . "</th>\n";
        }
        return $res;
    }

    /**
     * Show dynaform title
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $pa
     * @param $fil
     * @param $intPos
     * @param $strClass
     * @param $auxgetval
     * @return void
     */
    function RenderTitle2 ($pa, $fil, $intPos, $strClass = "tblHeader", $auxgetval = '')
    {
        if (! defined( 'ENABLE_ENCRYPT' )) {
            define( 'ENABLE_ENCRYPT', 'no' );
        }
        global $HTTP_SESSION_VARS;

        if ($auxgetval == '') {
            $targ = SYS_TARGET . ".html";
        } else {
            $targ = SYS_TARGET . '.html?' . $auxgetval;
        }
        $target = (ENABLE_ENCRYPT == 'yes' ? G::encryptUrl( urldecode( $targ ), URL_KEY ) : $targ);

        $col = $this->Columns[$intPos];

        if ($col['Type'] == 'hidden') {
            return '';
        }
        $order = ! ($col["Type"] == "image");

        if (($this->_ordered == true) && ($order) && ($this->Columns[$intPos]['orderByThis'])) {
            $res = "";
            if (($this->show_nummbers) and ($intPos == 0)) {
                $res = "<th class=\"$strClass\" align=\"left\" height=\"25\">#</th> ";
            }
            $res .= "<th class=\"$strClass\" align=\"left\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">&nbsp;";

            $res .= "<a class=\"" . $strClass . "\" href=\"";
            if ($fil != '') {
                $fil .= '&';
            }
            $direccion = $target . "?" . $fil . "order=" . $this->Columns[$intPos]['Name'] . "&page=" . $pa . "&label=true";
            $res .= "javascript:bsearch('$direccion')";
            //$res .= $target . "?".$fil."order=" . $this->Columns[$intPos]['Name']."&page=".$pa."&label=true";
            $res .= "\">" . $this->Labels[$intPos] . "</a>";

            $res .= "</th>\n";
        } else {
            $col = $this->Columns[$intPos];
            $res = "<th class=\"$strClass\" align=\"center\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">";
            $res .= (isset( $this->Labels[$intPos] ) ? $this->Labels[$intPos] : '') . "</th>\n";
        }
        return $res;
    }

    /**
     * Show dynaform column
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param $intPos
     * @param $strClass
     * @param $strClassLink
     * @param $number
     * @param $renderTD if this value = 1, this function will include the TD tags
     * @return void
     */
    function RenderColumn ($intPos = 0, $strClass = "tblCell", $strClassLink = "tblCellA", $number = 0, $renderTD = 1)
    {
        if (! defined( 'ENABLE_ENCRYPT' )) {
            define( 'ENABLE_ENCRYPT', 'no' );
        }
        global $G_DATE_FORMAT;
        global $G_TABLE_DATE_FORMAT;
        $col = $this->Columns[$intPos];

        switch (substr( $col['Name'], 0, 1 )) {
            case '=':
                // Si empieza con '=' entonces se toma como valor constante
                $val = substr( $col['Name'], 1, strlen( $col['Name'] ) - 1 );
                break;
            case '%':
                // Si empieza con '%' entonces traducir/convertir el valor
                $fieldname = substr( $col['Name'], 1, strlen( $col['Name'] ) - 1 );
                $val = $this->_row_values[$fieldname];
                $val = $this->translateValue( $this->_contexto, $val, SYS_LANG );
                break;
            default:
                $fieldname = $col['Name'];
                $val = isset( $this->_row_values[$fieldname] ) ? $this->_row_values[$fieldname] : '';
        }

        $res = "";
        if (($this->show_nummbers) and ($intPos == 0)) {
            $res = "<td>$number</td>";
        }
        if (! (stristr( $val, "script" ) === false)) {
            $val = htmlentities( $val, ENT_QUOTES, 'utf-8' );
        }

        if ($renderTD == 1) {
            $res .= "<td class=\"$strClass\" align=\"" . $col["Align"] . "\" height=\"25\"";
            if ($col["Width"] > 0) {
                $res .= " width=\"" . $col["Width"] . "\"";
            }
            $res .= ">&nbsp;";
        }

        switch ($col["Type"]) {
            case 'hidden':
                return '';
                break;
            case "text":
                if ($val != "") {
                    $res .= G::unhtmlentities( $val, ENT_QUOTES, 'utf-8' );
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "text-dontSearch":
                if ($val != "") {
                    $res .= G::unhtmlentities( $val );
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "html":
                if ($val != "") {
                    $res .= ($val);
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "textPlain":
                if ($val != "") {
                    $res .= ($this->ParsingFromHtml( G::unhtmlentities( $val ), "300" ));
                    //if ( $val != "" ) $res .= (($val));
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "currency":
                if ($val != "") {
                    $aux = explode( ' ', $val );
                    $format = number_format( (float) $aux[0], 2, ".", "," );
                    $res .= htmlentities( $format . ' ' . (isset( $aux[1] ) ? $aux[1] : ''), ENT_QUOTES, 'utf-8' );
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "currency2":
                if ($val != "") {
                    $res .= G::NumberToCurrency( $val );
                } else {
                    $res .= "$ 0.00";
                }
                break;
            case "percentage2":
                if ($val != "") {
                    $res .= G::NumberToPercentage( $val );
                } else {
                    $res .= "0.00 %";
                }
                break;
            case "percentage":
                if ($val != "") {
                    $res .= htmlentities( number_format( (float) $val, 2, ".", "," ) . " %", ENT_QUOTES, 'utf-8' );
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "date":
                if ($val != "" && $val != '0000-00-00 00:00:00') {
                    $part = explode( ' ', $val );
                    $aux = explode( '-', $part[0] );

                    switch ($G_DATE_FORMAT) {
                        case 'DD/MM/AAAA':
                            $res .= formatDate( '$d/$m/$Y $H:$i:$s', $val );
                            break;
                        case 'MM/DD/AAAA':
                            $res .= formatDate( '$m/$d/$Y $H:$i:$s <small>EST</small>', $val );
                            break;
                        case 'AAAA/MM/DD':
                            $res .= formatDate( '$Y/$m/$d $H:$i:$s', $val );
                            break;
                        case 'LITERAL':
                            $res .= formatDate( '$M $d $Y', $val );
                            break;
                    }
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "email":
                if ($val != "") {
                    $res .= "<a href=\"mailto:" . $val . "\">";
                    $res .= $val;
                    $res .= "</a>";
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "ifpdf":
                if ($val == '1') {
                    $image = "<img border=0 src='/images/pdf.gif'>";
                    //valor
                    $tlabel = substr( $col["Content"], 0, 1 );
                    $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                    $lval = $this->_row_values[$vname];
                    //$res .= "<a href='" . $col["Target"] . "/" . $lval . "' target='_new' > $image</a> "; //It open a new window... better the other way By JHL 16/11/06
                    $res .= "<a href='" . $col["Target"] . "/" . $lval . "'  > $image</a> ";
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "ifimg":
                $image = "<img border=0 src='" . $col['Extra'] . "' >";
                if ($val == '1') {
                    //valor
                    $tlabel = substr( $col["Content"], 0, 1 );
                    $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                    $lval = $this->_row_values[$vname];
                    $res .= "<a href='" . $col["Target"] . "/" . $lval . "' > $image</a> ";
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "ifrtf":
                if ($val == '1') {
                    $image = "<img border=0 src='/images/word.gif'>";
                    //valor
                    $tlabel = substr( $col["Content"], 0, 1 );
                    $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                    $lval = $this->_row_values[$vname];
                    //$res .= "<a href='" . $col["Target"] . $lval . "' target='_new' > $image</a> "; //It open a new window... better the other way By JHL 16/11/06
                    $res .= "<a href='" . $col["Target"] . $lval . "' > $image</a> ";
                } else {
                    $res .= "&nbsp;";
                }
                break;
            case "image":
                if (is_array( $col["Condition"] )) {
                    //By JHL to enable Condition to display a image -- New parameter Condition in Addrawcolumn
                    $field_compare = $col["Condition"]['field'];
                    $tlabel = substr( $field_compare, 0, 1 );
                    switch ($tlabel) {
                        case "&":
                            $vname = substr( $field_compare, 1, (strlen( $field_compare ) - 1) );
                            $field_val = $this->_row_values[$vname];
                            break;
                    }

                } else {
                    $val = "<img border=0 src='$fieldname'>";
                }
                 //      break;
            case "textimage":
                $AAS = $col['Extra'];
                $val1 = " <img border=0 src='$AAS' align='middle'>";
                //      break;
            case "image-text":
                if (is_array( $col['Content'] ) && $col['Content'] != "") {
                    // Hay mas de un valor para el link
                    $values = $col['Content'];
                    $n = count( $values );

                    $res .= "<a class='$txtin3' $title href=\"" . (ENABLE_ENCRYPT == 'yes' ? G::encryptUrl( urldecode( $col["Target"] ), URL_KEY ) : $col["Target"]) . "/";

                    for ($i = 0; $i < $n; $i ++) {
                        $element = $values[$i];

                        $tlabel = substr( $element, 0, 1 );
                        switch ($tlabel) {
                            case "&":
                                $vname = substr( $element, 1, (strlen( $element ) - 1) );
                                $lval = $this->_row_values[$vname];

                                $res .= $i == $n - 1 ? $lval : $lval . "/";
                                break;
                        }
                    }
                    $res .= "\"><span class='txtin3'>" . strtoupper( $fieldname ) . "$val</span></a>";
                } else {
                    $val2 = "<span class='txtin3'>" . strtoupper( $fieldname ) . "</span>";
                }
                //      break;
            case "link":
                if ($val == "") {
                    $res .= "&nbsp;";
                }
                $title = '';
                if ($col["Type"] == 'link' && trim( isset( $this->_row_values['TOOLTIP'] ) ? $this->_row_values['TOOLTIP'] : '' ))
                    ;
                $title = (isset( $this->_row_values['TOOLTIP'] ) ? "title=\" " . $this->_row_values['TOOLTIP'] . " \"" : '');
                if (is_array( $col['Content'] ) && $col['Content'] != "") {
                    // Hay mas de un valor para el link
                    $values = $col['Content'];
                    $n = count( $values );

                    $res .= "<a class='$strClassLink' $title href=\"" . (ENABLE_ENCRYPT == 'yes' ? G::encryptUrl( urldecode( $col["Target"] ), URL_KEY ) : $col["Target"]) . "/";

                    for ($i = 0; $i < $n; $i ++) {
                        $element = $values[$i];

                        $tlabel = substr( $element, 0, 1 );
                        switch ($tlabel) {
                            case "&":
                                $vname = substr( $element, 1, (strlen( $element ) - 1) );
                                $lval = $this->_row_values[$vname];

                                $res .= $i == $n - 1 ? $lval : $lval . "/";
                                break;
                        }
                    }
                    $res .= "\">$val</a>";
                } elseif ($col["Content"] != "" && ! is_array( $col['Content'] )) {
                    $tlabel = substr( $col["Content"], 0, 1 );
                    switch ($tlabel) {
                        case "&":
                            $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                            $lval = $this->_row_values[$vname];
                            if (ENABLE_ENCRYPT == 'yes') {

                                //$encoded = G::encrypt ( $col["Target"] . "/" . $lval . ".html", URL_KEY );
                                $encoded = G::encryptUrl( $col["Target"] . "/" . $lval . ".html", URL_KEY );
                                $res .= "<a class='$strClassLink' $title href=\"" . $encoded . "\" " . $col['Extra'] . ">";
                                if ($col["Type"] == "textimage") {
                                    $res .= $val1;
                                    $val = " (" . $val . ")";
                                }
                                if ($col["Type"] == "image-text") {

                                    $res .= $val2;
                                }
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                            } else {
                                $res .= "<a class='$strClassLink' $title href=\"" . $col["Target"] . "/" . $lval . ".html\" " . $col['Extra'] . ">";
                                if ($col["Type"] == "textimage") {
                                    $res .= $val1;
                                    $val = " (" . $val . ")";
                                }
                                if ($col["Type"] == "image-text") {

                                    $res .= $val2;
                                }
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                            }
                            break;
                        case "$":
                            $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                            $lval = $HTTP_SESSION_VARS[$vname];
                            $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $lval . ".html\" " . $col['Extra'] . ">";
                            $res .= $val;
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                        default:
                            $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" " . $col['Extra'] . ">";
                            $res .= $col["Content"];
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                    }
                } else {
                    $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" " . $col['Extra'] . ">";
                    $res .= $val;
                    $res .= "</a" . $col['Extra'] . ">";
                }
                break;
            case "linknew":
                if ($val == "") {
                    $res .= "&nbsp;";
                }
                if ($col["Content"] != "") {
                    $tlabel = substr( $col["Content"], 0, 1 );
                    switch ($tlabel) {
                        case "&":
                            if (ENABLE_ENCRYPT == 'yes') {
                                $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                                $lval = $this->_row_values[$vname];
                                //$encoded = G::encryptUrl ( $col["Target"] , URL_KEY ). "/" . $lval . ".html";
                                $encoded = G::encryptUrl( $col["Target"] . "/" . $lval . "", URL_KEY );
                                $res .= "<a class='$strClassLink' href=\"" . $encoded . "\" " . " target=\"_new\"" . $col['Extra'] . ">";
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                            } else {
                                $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                                $lval = $this->_row_values[$vname];
                                $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $lval . "\" target=\"_new\"" . $col['Extra'] . ">";
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                            }
                            break;
                        case "$":
                            $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                            $lval = $HTTP_SESSION_VARS[$vname];
                            $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $lval . ".html\" target=\"_new\"" . $col['Extra'] . ">";
                            $res .= $val;
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                        default:
                            $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" target=\"_new\"" . $col['Extra'] . ">";
                            $res .= $col["Content"];
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                    }
                } else {
                    $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" target=\"_new\"" . $col['Extra'] . ">";
                    $res .= $val;
                    $res .= "</a" . $col['Extra'] . ">";
                }
                break;
            case "iflink":
                if ($col["Content"] != "") {
                    $tlabel = substr( $col["Content"], 0, 1 );
                    if ($val != "") {
                        switch ($tlabel) {
                            case "&":
                                $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                                $lval = $this->_row_values[$vname];
                                $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $lval . ".html\" " . $col['Extra'] . ">";
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                                break;
                            case "$":
                                $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                                $lval = $HTTP_SESSION_VARS[$vname];
                                $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $lval . ".html\" " . $col['Extra'] . ">";
                                $res .= $val;
                                $res .= "</a" . $col['Extra'] . ">";
                                break;
                            default:
                                $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" " . $col['Extra'] . ">";
                                $res .= $col["Content"];
                                $res .= "</a" . $col['Extra'] . ">";
                                break;
                        }
                    } else {
                        $res .= "&nbsp;";
                    }
                } else {
                    $res .= "<a class='$strClassLink' href=\"" . $col["Target"] . "/" . $val . ".html\" " . $col['Extra'] . ">";
                    $res .= $val;
                    $res .= "</a" . $col['Extra'] . ">";
                }
                break;
            case "jsimglink":
                $val = "<img border=0 src='$fieldname'>";
            case "jslink":
                if ($val == "") {
                    $val .= "<span class='txtin3'> " . $col['Name'] . '<span>';
                }
                if ($val == "") {
                    $res .= "&nbsp;";
                }
                if ($col["Content"] != "") {
                    $tlabel = substr( $col["Content"], 0, 1 );
                    switch ($tlabel) {
                        case "&":
                            $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                            $lval = $this->_row_values[$vname];
                            $res .= "<a class='$strClassLink' href=\"javascript:" . $col["Target"] . "('" . $lval . "')\"" . $col['Extra'] . ">";
                            $res .= $val;
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                        case "$":
                            $vname = substr( $col["Content"], 1, (strlen( $col["Content"] ) - 1) );
                            $lval = $HTTP_SESSION_VARS[$vname];
                            $res .= "<a class='$strClassLink' href=\"javascript:" . $col["Target"] . "('" . $lval . "')\"" . $col['Extra'] . ">";
                            $res .= $val;
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                        case '_':
                            $Values = explode( ',', substr( $col['Content'], 1, strlen( $col['Content'] ) ) );
                            $res .= "<a class='$strClassLink' href=\"javascript:" . $col["Target"] . "(";
                            foreach ($Values as $Value) {
                                if (substr( $Value, 0, 1 ) == '&') {
                                    if (is_numeric( $Value )) {
                                        $res .= $this->_row_values[substr( $Value, 1, strlen( $Value ) )] . ',';
                                    } else {
                                        $res .= "'" . $this->_row_values[substr( $Value, 1, strlen( $Value ) )] . "',";
                                    }
                                } else {
                                    $res .= $Value . ',';
                                }
                            }
                            $res = substr( $res, 0, strlen( $res ) - 1 );
                            $res .= ")\"" . $col['Extra'] . ">";
                            $res .= $val;
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                        default:
                            $res .= "<a class='$strClassLink' href=\"javascript:" . $col["Target"] . "('" . $val . "')\"" . $col['Extra'] . ">";
                            $res .= $col["Content"];
                            $res .= "</a" . $col['Extra'] . ">";
                            break;
                    }
                } else {
                    $res .= "<a  class='$strClassLink' href=\"javascript:" . $col["Target"] . "(" . $val . ")\"" . $col['Extra'] . ">";
                    $res .= $val;
                    $res .= "</a" . $col['Extra'] . ">";
                }
                break;
            case "checkbox":
                $res .= "<input type='checkbox' name=\"form[" . $fieldname . "][" . $val . "]\" ";
                if ($val == '1' || $val == 'TRUE' || $val == 'yes') {
                    $res .= " checked ";
                }
                $res .= " disabled='disabled' >";
                break;
        }
        if ($renderTD == 1) {
            $res .= "</td>\n";
        }
        return $res;
        //return $res . $strClass;
    }

    /**
     * Set next action
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $strAction Next action to do
     * @param string $strLabel Label
     * @return void
     */
    function SetAction ($strAction, $strLabel = "Continue")
    {
        $this->Action = $strAction;
        $this->ActionLabel = $strLabel;
    }

    /**
     * Set contaxt and table (array) of translate
     *
     * @author Hardy Beltran Monasterios <hardy@acm.org>
     * @param string $contexto Contexto en el cual se busca la traducci?n
     * @param array $tabla Tabla con valores para traducir
     * @param string $nombre Nombre del array $tabla
     * @access public
     * @return void
     */
    function setTranslate ($contexto, $tabla, $nombre)
    {
        if (is_array( $this->contexto )) {
            $this->contexto[0][] = $contexto;
            $this->contexto[1][] = $nombre;
        } else {
            $this->contexto = array ();
            $this->contexto[0][] = $contexto;
            $this->contexto[1][] = $nombre;
            // array_push($this->contexto[0], $contexto);
            // array_push($this->contexto[1], $nombre);
        }
        if (is_array( $this->translate )) {
            $this->translate = array ();
            $this->translate[$nombre] = $tabla;
        } else {
            $this->translate[$nombre] = $tabla;
        }
        // Fijamos ultimo contexto usado
        $this->_contexto = $contexto;
    }

    /**
     * Search value in the table of translation and returns last accourding to choised context
     * Retorna el valor a su equivalente traducido/convertido
     *
     * @author Hardy Beltran Monasterios <hardy@acm.org>
     * @param string $contexto Contexto en el cual se busca la traducci?n
     * @param mixed $valor Valor que se va traducir/convertir
     * @param string $lang El lenguaje que se va utilizar
     * @return mixed
     */
    function translateValue ($contexto, $valor, $lang)
    {
        // Verificar si exite el contexto
        if (in_array( $contexto, $this->contexto[0] )) {
            $j = count( $this->contexto[0] );
            for ($i = 0; $i < $j; $i ++) {
                if ($contexto == $this->contexto[0][$i]) {
                    $origen = $this->contexto[1][$i];
                }
            }
            $tabla = $this->translate[$origen];
            if (isset( $tabla[$lang][$valor] )) {
                return $tabla[$lang][$valor];
            } else {
                print ("l10n error:no lang or value.") ;
            }
        } else {
            print ("l10n error:no context.") ;
        }
    }

    /**
     * Estable el contexto de traducci?n/conversi?n
     *
     * @author Hardy Beltran Monasterios <hardy@acm.org>
     * @param string $contexto Contexto en el cual se busca la traducci?n
     * @return void
     */
    function setContext ($contexto)
    {
        $this->_context = $contexto;
    }

    /**
     * Parse from HTML
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function ParsingFromHtml ($value, $number = '100000000')
    {
        $car = substr( $value, 0, 1 );
        $len = strlen( $value );
        $Flag = 1;
        $token = '';
        $i = 0;

        while ($i <= $len and $i <= $number) {
            $car = substr( $value, $i, 1 );
            $br = strtoupper( substr( $value, $i, 4 ) );
            if ($car == '<') {
                $Flag = 0;
            }
            if ($car == '>') {
                $Flag = 1;
            }
            if ($br == '<BR>' || $br == '</P>') {
                $token .= "<BR>";
            }

            if (($Flag == 1) && ($car != '>')) {
                $token .= $car;
                if ($i == $number) {
                    $token .= "... ";
                }
            }
            $i = $i + 1;
        }
        return $token;
    }
}

