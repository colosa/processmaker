<?php
/**
 * class.pagedTable.php
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

G::LoadClass( 'filterForm' );
G::LoadClass( 'xmlMenu' );

/**
 * Class pagedTable
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */

class pagedTable
{
    public $xmlFormFile;
    public $currentPage;
    public $orderBy = '';
    public $filter = array ();
    public $filterType = array ();
    public $searchBy = '';
    public $fastSearch = '';
    public $order = '';
    public $template = 'templates/paged-table.html';
    public $tpl;
    public $style = array ();
    public $rowsPerPage = 25;
    public $ownerPage;
    public $popupPage;
    public $popupSubmit;
    public $popupWidth = 450;
    public $popupHeight = 200;
    public $ajaxServer;
    public $fields;
    public $query;
    public $totpages;

    //SQL QUERIES
    public $sql = '';
    public $sqlWhere = '';
    public $sqlGroupBy = '';
    public $sqlSelect = 'SELECT 1';
    public $sqlDelete = '';
    public $sqlInsert = '';
    public $sqlUpdate = '';
    public $fieldDataList = '';

    //Configuration
    public $xmlPopup = '';
    public $addRow = false;
    public $deleteRow = false;
    public $editRow = false;
    public $notFields = '  title button linknew begingrid2 endgrid2 '; // These are not considered to build the sql queries (update,insert,delete)


    //JavaScript Object attributes
    public $onUpdateField = "";
    public $onDeleteField = "";
    public $afterDeleteField = "";
    public $onInsertField = "";

    //New gulliver
    public $xmlForm;
    public $menu = '';
    public $filterForm = '';
    public $filterForm_Id = '';
    public $name = 'pagedTable';
    public $id = 'A1';
    public $disableFooter = false;
    //This attribute is used to set STYLES to groups of TD, using the field type "cellMark" (see XmlForm_Field_cellMark)
    public $tdStyle = '';
    public $tdClass = '';
    //Config Save definition
    public $__Configuration = 'orderBy,filter,fastSearch,style/*/showInTable'; //order,rowsPerPage,disableFooter';


    /**
     * Function analizeSql
     * You can to distribute the component of the query like: Select, Where, Group by and order by
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function analizeSql ()
    {
        if (1 === preg_match( '/^\s*SELECT\s+(.+?)(?:\s+FROM\s+(.+?))(?:\s+WHERE\s+(.+?))?(?:\s+GROUP\s+BY\s+(.+?))?(?:\s+ORDER\s+BY\s+(.+?))?(?:\s+BETWEEN\s+(.+?)\s+AND\s+(.+?))?\s*$/im', $this->sqlSelect, $matches )) {
            $this->sqlSelect = 'SELECT ' . $matches[1] . (($matches[2] != '') ? ' FROM ' . $matches[2] : '');
            $this->sqlSelect = 'SELECT ' . $matches[1] . (($matches[2] != '') ? ' FROM ' . $matches[2] : '');
        } else {
            //echo('Warning: SQL Query is not well formed.');
            return;
        }
        $this->sqlFrom = isset( $matches[2] ) ? $matches[2] : '';
        $this->sqlWhere = isset( $matches[3] ) ? $matches[3] : '';
        $this->sqlGroupBy = isset( $matches[4] ) ? $matches[4] : '';
        $this->sqlOrderBy = isset( $matches[5] ) ? $matches[5] : '';
        $this->order = '';
        if ($this->sqlOrderBy != '') {
            if ($n = preg_match_all( '/\b([\w\.]+)\b(?:\s+(ASC|DESC))?,?/im', $this->sqlOrderBy, $matches, PREG_SET_ORDER )) {
                for ($r = 0; $r < $n; $r ++) {
                    if (! isset( $matches[$r][2] )) {
                        $matches[$r][2] = '';
                    }
                    if ($matches[$r][2] == '') {
                        $matches[$r][2] = 'ASC';
                    }
                    $ord = G::createUID( '', $matches[$r][1] ) . '=' . urlencode( $matches[$r][2] );
                    if ($this->order == '') {
                        $this->order = $ord;
                    } else {
                        $this->order .= '&' . $ord;
                    }
                }
                //Orden ascendente
                if ($n == 1) {
                    $this->order = G::createUID( '', $matches[0][1] ) . '=' . $matches[0][2];
                }
            }
        }
        //Generate: $uniqueWhere=Identify a row bys its data content
        //$this->fieldDataList=url text that dentify a row bys its data content
        $uniqueWhere = '';
        $this->fieldDataList = '';
        foreach ($this->fields as $r => $field) {
            if ((strpos( $this->notFields, ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                if ($uniqueWhere == '') {
                    $uniqueWhere = (($this->sqlWhere != '') ? ('(' . $this->sqlWhere . ') AND (') : '(');
                } else {
                    $uniqueWhere .= ' AND ';
                }
                $uniqueWhere .= $this->fields[$r]['Name'] . '=' . '@@' . $this->fields[$r]['Name'];
                if ($this->fieldDataList == '') {
                    $this->fieldDataList = '';
                } else {
                    $this->fieldDataList .= '&';
                }
                $this->fieldDataList .= $this->fields[$r]['Name'] . '=' . '@@_' . $this->fields[$r]['Name'];
            }
        }
        if ($uniqueWhere != '') {
            $uniqueWhere .= ')';
        }
    }

    /**
     * Function prepareQuery
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function prepareQuery ()
    {
        //DBConnection
        if (! $this->sqlConnection) {
            $this->dbc = new DBConnection();
        } else {
            if (defined( 'DB_' . $this->sqlConnection . '_USER' )) {
                if (defined( 'DB_' . $this->sqlConnection . '_HOST' )) {
                    eval( '$res[\'DBC_SERVER\'] = DB_' . $this->sqlConnection . '_HOST;' );
                } else {
                    $res['DBC_SERVER'] = DB_HOST;
                }
                if (defined( 'DB_' . $this->sqlConnection . '_USER' )) {
                    eval( '$res[\'DBC_USERNAME\'] = DB_' . $this->sqlConnection . '_USER;' );
                }
                if (defined( 'DB_' . $this->sqlConnection . '_PASS' )) {
                    eval( '$res[\'DBC_PASSWORD\'] = DB_' . $this->sqlConnection . '_PASS;' );
                } else {
                    $res['DBC_PASSWORD'] = DB_PASS;
                }
                if (defined( 'DB_' . $this->sqlConnection . '_NAME' )) {
                    eval( '$res[\'DBC_DATABASE\'] = DB_' . $this->sqlConnection . '_NAME;' );
                } else {
                    $res['DBC_DATABASE'] = DB_NAME;
                }
                if (defined( 'DB_' . $this->sqlConnection . '_TYPE' )) {
                    eval( '$res[\'DBC_TYPE\'] = DB_' . $this->sqlConnection . '_TYPE;' );
                } else {
                    $res['DBC_TYPE'] = defined( 'DB_TYPE' ) ? DB_TYPE : 'mysql';
                }
                $this->dbc = new DBConnection( $res['DBC_SERVER'], $res['DBC_USERNAME'], $res['DBC_PASSWORD'], $res['DBC_DATABASE'], $res['DBC_TYPE'] );
            } else {
                $dbc = new DBConnection();
                $dbs = new DBSession( $dbc );
                $res = $dbs->execute( "select * from  DB_CONNECTION WHERE DBC_UID=" . $this->sqlConnection );
                $res = $res->read();
                $this->dbc = new DBConnection( $res['DBC_SERVER'], $res['DBC_USERNAME'], $res['DBC_PASSWORD'], $res['DBC_DATABASE'] );
            }
        }
        $this->ses = new DBSession( $this->dbc );
        //Query
        //Filter
        if (is_array( $this->filter )) {
            $filterFields = $this->filter;
        } else {
            parse_str( $this->filter, $filterFields );
        }
        $this->aFilter = $filterFields;
        $filter = '';
        foreach ($filterFields as $field => $like) {
            if ($like != '') {
                if ($filter !== '') {
                    $filter .= ' AND ';
                }
                if (isset( $this->filterType[$field] )) {
                    switch ($this->filterType[$field]) {
                        case '=':
                            $filter .= $field . ' = "' . mysql_real_escape_string( $like ) . '"';
                            break;
                        case '<>':
                            $filter .= $field . ' <> "' . mysql_real_escape_string( $like ) . '"';
                            break;
                        case 'contains':
                            $filter .= $field . ' LIKE "%' . mysql_real_escape_string( $like ) . '%"';
                            break;
                        case 'like':
                            $filter .= $field . ' LIKE "' . mysql_real_escape_string( $like ) . '"';
                            break;
                    }
                } else {
                    $filter .= $field . ' = "' . mysql_real_escape_string( $like ) . '"';
                }
            }
        }
        /*
        * QuickSearch
        */
        if ($this->searchBy !== '') {
            $aSB = explode( '|', $this->searchBy );
            $subFilter = '';
            foreach ($aSB as $sBy) {
                $subFilter .= ($subFilter !== '') ? ' OR ' : '';
                $subFilter .= $sBy . ' LIKE "%' . G::sqlEscape( $this->fastSearch, $this->dbc->type ) . '%"';
            }
            if ($subFilter !== '') {
                $filter .= ($filter !== '') ? ' AND ' : '';
                $filter .= '(' . $subFilter . ')';
            }
        }
        //Merge sort array defined by USER with the array defined by SQL
        parse_str( $this->order, $orderFields );
        parse_str( $this->orderBy, $orderFields2 );
        //User sort is more important (first in merge).
        $orderFields3 = array_merge( $orderFields2, $orderFields );
        //User sort is overwrites XMLs definition.
        $orderFields = array_merge( $orderFields3, $orderFields2 );
        //Order (BY SQL DEFINITION AND USER'S DEFINITION)
        $this->aOrder = array ();
        $order = '';
        foreach ($orderFields as $field => $fieldOrder) {
            $field = G::getUIDName( $field, '' );
            $fieldOrder = strtoupper( $fieldOrder );
            if ($fieldOrder === 'A') {
                $fieldOrder = 'ASC';
            }
            if ($fieldOrder === 'D') {
                $fieldOrder = 'DESC';
            }
            switch ($fieldOrder) {
                case 'ASC':
                case 'DESC':
                    if ($order !== '') {
                        $order .= ', ';
                    }
                    $order .= $field . ' ' . $fieldOrder;
                    $this->aOrder[$field] = $fieldOrder;
            }
        }
        $this->sql = $this->sqlSelect . ((($this->sqlWhere != '') || ($filter != '')) ? ' WHERE ' : '') . (($this->sqlWhere != '') ? '(' . $this->sqlWhere . ')' : '') . ((($this->sqlWhere != '') && ($filter != '')) ? ' AND ' : '') . (($filter != '') ? '(' . $filter . ')' : '') . (($this->sqlGroupBy != '') ? ' GROUP BY ' . $this->sqlGroupBy : '') . (($order != '') ? ' ORDER BY ' . $order : '');
        //$this->query=$this->ses->execute($this->sql);
        //$this->totpages=ceil($this->query->count()/$this->rowsPerPage);
        return;
    }

    /**
     * Function setupFromXmlform
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string xmlForm
     * @return string
     */
    public function setupFromXmlform ($xmlForm)
    {
        $this->xmlForm = $xmlForm;
        //Config
        $this->name = $xmlForm->name;
        $this->id = $xmlForm->id;
        $this->sqlConnection = ((isset( $this->xmlForm->sqlConnection )) ? $this->xmlForm->sqlConnection : '');
        if (isset( $_GET['page'] )) {
            $this->currentPage = $_GET['page'];
        } else {
            $this->currentPage = 1;
        }
        if (isset( $_GET['order'] )) {
            $this->orderBy = urldecode( $_GET['order'] );
        } else {
            $this->orderBy = "";
        }
        if (isset( $_GET['filter'] )) {
            $this->filter = urldecode( $_GET['filter'] );
        } else {
            $this->filter = "";
        }
        $this->ajaxServer = G::encryptLink( '../gulliver/pagedTableAjax' );
        $this->ownerPage = G::encryptLink( SYS_CURRENT_URI );
        //Needed for $mysql_real_escape_string
        $auxDbc = new DBConnection();
        if (isset( $this->xmlForm->sql )) {
            $this->sqlSelect = G::replaceDataField( $this->xmlForm->sql, $this->xmlForm->values );
        } else {
            trigger_Error( 'Warning: sql query is empty', E_USER_WARNING );
        }
        // Config attributes from XMLFORM file
        $myAttributes = get_class_vars( get_class( $this ) );
        foreach ($this->xmlForm->xmlform->tree->attribute as $atrib => $value) {
            if (array_key_exists( $atrib, $myAttributes )) {
                eval( 'settype($value,gettype($this->' . $atrib . '));' );
                if ($value !== '') {
                    eval( '$this->' . $atrib . '=$value;' );
                }
            }
        }
        //Prepare the fields
        $this->style = array ();
        $this->gridWidth = "";
        $this->gridFields = "";
        $this->fieldsType = array ();
        foreach ($this->xmlForm->fields as $f => $v) {
            $r = $f;
            $this->fields[$r]['Name'] = $this->xmlForm->fields[$f]->name;
            $this->fields[$r]['Type'] = $this->xmlForm->fields[$f]->type;
            if (isset( $this->xmlForm->fields[$f]->size )) {
                $this->fields[$r]['Size'] = $this->xmlForm->fields[$f]->size;
            }
            $this->fields[$r]['Label'] = $this->xmlForm->fields[$f]->label;
        }
        //Autocomplete the sql queries
        // Here we can to distribute the component of the query like: Select, Where, Group by and order by
        $this->analizeSql();
        //Set the default settings
        $this->defaultStyle();
        //continue whith the setup
        $this->gridWidth = '';
        $this->gridFields = '';
        foreach ($this->xmlForm->fields as $f => $v) {
            $r = $f;
            //Parse the column properties
            foreach ($this->xmlForm->fields[$f] as $attribute => $value) {
                if (! is_object( $value )) {
                    $this->style[$r][$attribute] = $value;
                }
            }
            //Needed for javascript
            //only the visible columns's width and name are stored
            if ($this->style[$r]['showInTable'] != '0') {
                $this->gridWidth .= ',' . $this->style[$r]['colWidth'];
                $this->gridFields .= ',"form[' . $this->fields[$r]['Name'] . ']"';
            }
        }
        $totalWidth = 0;
        foreach ($this->fields as $r => $rval) {
            if ($this->style[$r]['showInTable'] != '0') {
                $totalWidth += $this->style[$r]['colWidth'];
            }
        }
        $this->totalWidth = $totalWidth;
    }

    /**
     * Function setupFromTable
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string table
     * @return string
     */
    //  function setupFromTable($table)
    //  {
    ////  var_dump2($table);
    //    //Config
    //      $this->rowsPerPage=25;
    //      if (isset($_GET['page']))
    //        $this->currentPage = $_GET['page'];
    //      else
    //        $this->currentPage = 1;
    //      if (isset($_GET['order']))
    //        $this->orderBy = urldecode($_GET['order']);
    //      else
    //        $this->orderBy = "";
    //      if (isset($_GET['filter']))
    //        $this->filter = urldecode($_GET['filter']);
    //      else
    //        $this->filter = "";
    //      $xmlPopup='';
    //      $this->xmlFormFile="";
    ///*      if ($table->Action)
    //        $this->ajaxServer=G::encryptLink($table->Action);
    //      else*/
    //      $this->ajaxServer=G::encryptLink('../gulliver/pagedTableAjax');
    //      $this->popupPage = $this->ajaxServer . '?function=printForm&filename=' . urlencode($xmlPopup);
    //      $this->ownerPage=G::encryptLink(SYS_CURRENT_URI);
    //      $this->sqlConnection='';
    //      if (isset($table->_source))
    //        $this->sqlSelect=$table->_source;
    //      if (isset($table->WhereClause)){
    //        if (strpos(strtoupper($table->WhereClause),'GROUP BY')!==FALSE)
    //          preg_match("/(.+)(GROUP BY)(.*)/",$table->WhereClause,$matches);
    //        else{
    //          $matches[1]=$table->WhereClause;$matches[2]='';
    //        }
    //        $this->sqlWhere=$matches[1];
    //        if (strcasecmp($matches[2],'GROUP BY')==0)
    //          $this->sqlGroupBy=' GROUP BY '.$matches[3];
    //      }
    //      if (strpos(strtoupper($this->sqlSelect),'WHERE')!==FALSE){
    //        preg_match("/SELECT(.+)FROM(.+)WHERE(.+)/",$this->sqlSelect,$matches);
    //        $this->sqlSelect='SELECT '.$matches[1].' FROM '.$matches[2];
    //        $this->sqlWhere=$matches[3];
    //      }
    //    // DBConnection
    //    //      $this->prepareQuery();
    //    //Prepare the fields
    //      if ($table->show_nummbers=='YES'){
    //        $r=-1;
    //        $this->fields[$r]['Name']='numberlabel';
    //        $this->fields[$r]['Type']='numberlabel';
    //        $this->fields[$r]['Label']='#';
    //      }
    //      foreach ($table->Columns as $r => $value){
    //        $this->fields[$r]['Name']=$value['Name'];
    //        $this->fields[$r]['Type']=$value['Type'];
    //        $this->fields[$r]['Label']=((isset($table->Labels[$r]))?$table->Labels[$r]:'');
    //        //Default values for Label if it was empty
    //        if ($this->fields[$r]['Label']=='')
    //          switch($table->Columns[$r]['Type']){
    //           case 'image':
    //           case 'image-text':
    //           case 'jslink':
    //             //var_dump2($table->Columns[$r]);
    //            $this->fields[$r]['Label']=$value['Name'];
    //        }
    //        //Print the type of the field
    //        //$this->fields[$r]['Label'].='('.$this->fields[$r]['Type'].')';
    //        $r++;
    //      }
    //    //Add a delete column if sqlDelete is established
    //    /*  if ($this->sqlDelete!='')
    //      {
    //        $this->fields[$r]['Name']='';
    //        $this->fields[$r]['Type']='linknew';
    //        $this->fields[$r]['Label']=G::LoadXml('labels','ID_DELETE');
    //      }*/
    //    //Set the default settings
    //      $this->defaultStyle();
    //    /*  if ($this->sqlDelete!='')
    //      {
    //        $this->style[$r]['href']="#";
    //        $this->style[$r]['onclick']="document.getElementById('pagedTable').outerHTML=ajax_function('{$this->ajaxServer}','delete','".$this->fieldDataList."');";
    //      }*/
    //    //Prepare the columns's properties
    //      if ($table->show_nummbers=='YES'){
    //        $r=-1;
    //        $this->style[$r]['data']='@@_row__';
    //        $this->style[$r]['colWidth']=30;
    //      }
    //      $this->gridWidth='';
    //      $this->gridFields='';
    //      foreach ($table->Columns as $r => $value){
    //        //var_dump($value['Width']);
    //        $this->style[$r]['colWidth']=$value['Width'];
    //        $this->style[$r]['titleAlign']=$value['Align'];
    //        $this->style[$r]['href']=$value['Target'];
    //        // Add the row reference
    //        switch ($this->fields[$r]['Type']){
    //          case 'image-text':
    //          case 'textimage':
    //          case 'image':
    //          case 'link':
    //            //$this->style[$r]['href'].='/@@_row__.html';  // No
    //            if (substr($value['Content'],0,1)=='&')
    //              $this->style[$r]['href'].='/@@_'.substr($value['Content'],1).'.html';
    //        }
    //        // Extra events for each field
    //        $this->style[$r]['event']=$value['Extra'];
    //        if ($this->fields[$r]['Label']==''){
    //          $this->style[$r]['titleVisibility']='0';
    //          $this->fields[$r]['Label']=$this->fields[$r]['Name'];
    //        }
    //        //if ($value['orderByThis']===true) $this->orderBy=$value['Name'];
    //        //Needed for javascript
    //        //only the visible columns's width and name are stored
    //        if ($this->style[$r]['showInTable']!='0'){
    //          $this->gridWidth.=','.$this->style[$r]['colWidth'];
    //          $this->gridFields.=',"form['.$this->fields[$r]['Name'].']"';
    //        }
    //        $r++;
    //      }
    //      echo('<br>');
    ////    var_dump2($this);
    //  }


    /**
     * Function count
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function count ()
    {
        $this->prepareQuery();
        return $this->query->count();
    }

    /**
     * Function renderTitle
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function renderTitle ()
    {
        //Render Title
        $thereisnotitle = true;
        foreach ($this->fields as $r => $rval) {
            if ($this->fields[$r]['Type'] === 'title') {
                $this->tpl->assign( "title", $this->fields[$r]['Label'] );
                $thereisnotitle = false;
            }
        }
        if ($thereisnotitle) {
            $this->tpl->assign( "title", '  ' );
        }
        //Render headers
        $this->colCount = 0;
        $this->shownFields = '[';
        foreach ($this->fields as $r => $rval) {
            if ($this->style[$r]['showInTable'] != '0') {
                $this->tpl->newBlock( "headers" );
                $sortOrder = (((isset( $this->aOrder[$this->fields[$r]['Name']] )) && ($this->aOrder[$this->fields[$r]['Name']] === 'ASC')) ? 'DESC' : 'ASC');
                $sortOrder = (((isset( $this->aOrder[$this->fields[$r]['Name']] )) && ($this->aOrder[$this->fields[$r]['Name']] === 'DESC')) ? '' : $sortOrder);
                $this->style[$r]['href'] = $this->ownerPage . '?order=' . ($sortOrder !== '' ? urlencode( G::createUID( '', $this->fields[$r]['Name'] ) . '=' . $sortOrder ) : '') . '&page=' . $this->currentPage;
                $this->style[$r]['onsort'] = $this->id . '.doSort("' . G::createUID( '', $this->fields[$r]['Name'] ) . '" , "' . $sortOrder . '");return false;';
                if (isset( $this->style[$r]['href'] )) {
                    $this->tpl->assign( "href", $this->style[$r]['href'] );
                }
                if (isset( $this->style[$r]['onsort'] )) {
                    $this->tpl->assign( "onclick", htmlentities( $this->style[$r]['onsort'], ENT_QUOTES, 'UTF-8' ) );
                }
                if (isset( $this->style[$r]['colWidth'] )) {
                    $this->tpl->assign( "width", $this->style[$r]['colWidth'] );
                }
                if (isset( $this->style[$r]['colWidth'] )) {
                    $this->tpl->assign( "widthPercent", ($this->style[$r]['colWidth'] * 100 / $this->totalWidth) . "%" );
                }
                if (isset( $this->style[$r]['titleAlign'] )) {
                    $this->tpl->assign( "align", 'text-align:' . $this->style[$r]['titleAlign'] . ';' );
                }
                if ($this->style[$r]['titleVisibility'] != '0') {
                    $sortOrder = (((isset( $this->aOrder[$this->fields[$r]['Name']] )) && ($this->aOrder[$this->fields[$r]['Name']] === 'ASC')) ? 'b2' : '');
                    $sortOrder = (((isset( $this->aOrder[$this->fields[$r]['Name']] )) && ($this->aOrder[$this->fields[$r]['Name']] === 'DESC')) ? 'b<' : $sortOrder);
                    $this->tpl->assign( "header", $this->fields[$r]['Label'] . $sortOrder );
                    $this->tpl->assign( 'displaySeparator', (($this->colCount == 0) || (! isset( $this->fields[$r]['Label'] )) || ($this->fields[$r]['Label'] === '')) ? 'display:none;' : '' );
                } else {
                    $this->tpl->assign( 'displaySeparator', 'display:none;' );
                }
                $this->colCount += 2;
                $this->shownFields .= ($this->shownFields !== '[') ? ',' : '';
                $this->shownFields .= '"' . $r . '"';
            }
        }
        $this->shownFields .= ']';
    }

    /**
     * Function renderField
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string row
     * @param eter string r
     * @param eter string result
     * @return string
     */
    public function renderField ($row, $r, $result)
    {
        global $G_DATE_FORMAT;
        //BEGIN: Special content: __sqlEdit__,__sqlDelete__
        $result['sqlDelete__'] = "pagedTable.event='Delete';pagedTable_DoIt=true;if (pagedTable.onDeleteField) pagedTable_DoIt=eval(pagedTable.onDeleteField);if (pagedTable_DoIt) document.getElementById('pagedTable').outerHTML=ajax_function('{$this->ajaxServer}','delete','field='+encodeURIComponent('" . ($this->fieldDataList) . "'));if (pagedTable.afterDeleteField) return eval(pagedTable.afterDeleteField); else return false;";
        $result['sqlEdit__'] = "pagedTable.event='Update';pagedTable.field=encodeURIComponent('" . $this->fieldDataList . "');pagedTable.updateField(pagedTable.field);return false;";
        $result['pagedTableField__'] = "'" . $this->fieldDataList . "'";
        $result['row__'] = $row;
        //END: Special content.
        //Merge $result with $xmlForm values (for default valuesSettings)
        $result = array_merge( $this->xmlForm->values, $result );
        switch (true) {
            case ($this->style[$r]['data'] != ''):
                $value = ((isset( $result[$this->style[$r]['data']] )) ? $result[$this->style[$r]['data']] : '');
                break;
            default:
                $value = $this->fields[$r]['Label'];
        }
        switch ($this->fields[$r]['Type']) {
            case 'date':
                /*Accept dates like 20070515 without - or / to separate its parts*/
                if (strlen( $value ) <= 10 && strlen( $value ) > 4) {
                    $value = str_replace( '/', '-', $value );
                    if (strpos( $value, '-' ) === false) {
                        $value = substr( $value, 0, 4 ) . '-' . substr( $value, 4, 2 ) . '-' . substr( $value, 6, 2 );
                    }
                }
        }
        $this->tpl->newBlock( "field" );
        $this->tpl->assign( 'width', $this->style[$r]['colWidth'] );
        $this->tpl->assign( 'widthPercent', ($this->style[$r]['colWidth'] * 100 / $this->totalWidth) . '%' );
        $this->tpl->assign( 'className', (isset( $this->style[$r]['colClassName'] ) && ($this->style[$r]['colClassName'])) ? $this->style[$r]['colClassName'] : $this->tdClass );
        $this->tpl->assign( 'style', $this->tdStyle );
        if (isset( $this->style[$r]['align'] )) {
            $this->tpl->assign( "align", $this->style[$r]['align'] );
        }
        if (isset( $this->style[$r]['colAlign'] )) {
            $this->tpl->assign( "align", $this->style[$r]['colAlign'] );
        }
        /**
         * BEGIN : Reeplace of @@, @%,...
         * in field's attributes like onclick, link,
         * ...
         */
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->onclick )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->onclick = G::replaceDataField( $this->style[$r]['onclick'], $result );
        }
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->link )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->link = G::replaceDataField( $this->style[$r]['link'], $result );
        }
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->value )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->value = G::replaceDataField( $this->style[$r]['value'], $result );
        }
        /**
         * BREAK : Reeplace of @@, @%,...
         */
        /**
         * Rendering of the field
         */
        $this->xmlForm->setDefaultValues();
        $this->xmlForm->setValues( $result );
        $this->xmlForm->fields[$this->fields[$r]['Name']]->mode = 'view';
        if ((array_search( 'rendergrid', get_class_methods( get_class( $this->xmlForm->fields[$this->fields[$r]['Name']] ) ) ) !== false) || (array_search( 'renderGrid', get_class_methods( get_class( $this->xmlForm->fields[$this->fields[$r]['Name']] ) ) ) !== false)) {
            $htmlField = $this->xmlForm->fields[$this->fields[$r]['Name']]->renderGrid( array ($value
            ), $this->xmlForm );
            $this->tpl->assign( "value", $htmlField[0] );
        } else {
        }
        /**
         * CONTINUE : Reeplace of @@, @%,...
         */
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->onclick )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->onclick = $this->style[$r]['onclick'];
        }
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->link )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->link = $this->style[$r]['link'];
        }
        if (isset( $this->xmlForm->fields[$this->fields[$r]['Name']]->value )) {
            $this->xmlForm->fields[$this->fields[$r]['Name']]->value = $this->style[$r]['value'];
        }
        /**
         * END : Reeplace of @@, @%,...
         */
        return $this->fields[$r]['Type'];
    }

    /**
     * Function defaultStyle
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function defaultStyle ()
    {
        //    for($r=1;$r<=sizeof($this->fields);$r++)
        foreach ($this->fields as $r => $rval) {
            $this->style[$r] = array ('showInTable' => '1','titleVisibility' => '1','colWidth' => '150','onclick' => '','event' => ''
            );
            //Some widths
            if (! (strpos( '  date linknew ', ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                $this->style[$r]['colWidth'] = '70';
                //Data source:
            }
            if (! (strpos( '  title button linknew image-text jslink ', ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                $this->style[$r]['data'] = ''; //If the control is a link it shows the label
            } else {
                $this->style[$r]['data'] = $this->fields[$r]['Name']; //ELSE: The data value for that field
            }
                //Hidden fields
            if (! isset( $this->style[$r]['showInTable'] )) {
                if (! (strpos( '  title button endgrid2 submit password ', ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                    $this->style[$r]['showInTable'] = '0';
                } else {
                    $this->style[$r]['showInTable'] = '1';
                }
            }
            //Hidden titles
            if (! (strpos( '  linknew button endgrid2 ', ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                $this->style[$r]['titleVisibility'] = '0';
            }
            //Align titles
            $this->style[$r]['titleAlign'] = 'center';
            //Align fields
            if (isset( $_SESSION['SET_DIRECTION'] ) && (strcasecmp( $_SESSION['SET_DIRECTION'], 'rtl' ) === 0)) {
                $this->style[$r]['align'] = 'right';
            } else {
                $this->style[$r]['align'] = 'left';
            }
            if (! (strpos( ' linknew date ', ' ' . $this->fields[$r]['Type'] . ' ' ) === false)) {
                $this->style[$r]['align'] = 'center';
            }
        }
        // Adjust the columns width to prevent overflow the page width
        //Render headers
        $totalWidth = 0;
        foreach ($this->fields as $r => $rval) {
            if ($this->style[$r]['showInTable'] != '0') {
                $totalWidth += $this->style[$r]['colWidth'];
            }
        }
        $this->totalWidth = $totalWidth;
        $maxWidth = 1800;
        $proportion = $totalWidth / $maxWidth;
        if ($proportion > 1) {
            $this->totalWidth = 1800;
        }
        if ($proportion > 1) {
            foreach ($this->fields as $r => $rval) {
                if ($this->style[$r]['showInTable'] != '0') {
                    $this->style[$r]['colWidth'] = $this->style[$r]['colWidth'] / $proportion;
                }
            }
        }
    }

    /**
     * Function renderTable
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @param $block : = 'content'(Prints contentBlock only)
     * @access public
     * @return string
     */
    public function renderTable ($block = '')
    {
        // DBConnection
        $this->prepareQuery();
        //Query for get the number of rows
        $this->query = $this->ses->execute( $this->sql );
        $this->totRows = $this->query->count();
        $this->totpages = ceil( $this->query->count() / $this->rowsPerPage );
        //Query for obtain the records
        $this->query = $this->ses->execute( $this->sql . ' LIMIT ' . (($this->currentPage - 1) * $this->rowsPerPage) . ', ' . $this->rowsPerPage );
        // Prepare the template
        $this->tpl = new TemplatePower( PATH_CORE . $this->template );
        $this->tpl->prepare();
        /**
         * ******** HEAD BLOCK **************
         */
        if (($block === '') || ($block === 'head')) {
            $this->tpl->newBlock( 'headBlock' );
            $this->tpl->assign( 'pagedTable_Id', $this->id );
            $this->tpl->assign( 'pagedTable_Name', $this->name );
            $this->tpl->assign( 'pagedTable_Height', $this->xmlForm->height );
            if (file_exists( $this->xmlForm->home . $this->filterForm . '.xml' )) {
                $filterForm = new filterForm( $this->filterForm, $this->xmlForm->home );
                if ($this->menu === '') {
                    $this->menu = 'gulliver/pagedTable_Options';
                }
            }
            if (file_exists( $this->xmlForm->home . $this->menu . '.xml' )) {
                $menu = new xmlMenu( $this->menu, $this->xmlForm->home );
                $this->tpl->newBlock( 'headerBlock' );
                $template = PATH_CORE . 'templates' . PATH_SEP . $menu->type . '.html';
                $menu->setValues( $this->xmlForm->values );
                $menu->setValues( array ('PAGED_TABLE_ID' => $this->id
                ) );
                $menu->setValues( array ('PAGED_TABLE_FAST_SEARCH' => $this->fastSearch
                ) );
                if (isset( $filterForm->name )) {
                    $menu->setValues( array ('SEARCH_FILTER_FORM' => $filterForm->name
                    ) );
                }
                $this->tpl->assign( 'content', $menu->render( $template, $scriptCode ) );
                $oHeadPublisher = & headPublisher::getSingleton();
                $oHeadPublisher->addScriptFile( $menu->scriptURL );
                $oHeadPublisher->addScriptCode( $scriptCode );
            }
            if (file_exists( $this->xmlForm->home . $this->filterForm . '.xml' )) {
                $this->tpl->newBlock( 'headerBlock' );
                $this->filterForm_Id = $filterForm->id;
                $filterForm->type = 'filterform';
                $filterForm->ajaxServer = '../gulliver/defaultAjax';
                $template = PATH_CORE . 'templates/' . $filterForm->type . '.html';
                $filterForm->setValues( $this->xmlForm->values );
                $filterForm->setValues( array ('PAGED_TABLE_ID' => $this->id
                ) );
                $filterForm->setValues( array ('PAGED_TABLE_FAST_SEARCH' => $this->fastSearch
                ) );
                $this->tpl->assign( 'content', $filterForm->render( $template, $scriptCode ) );
                $oHeadPublisher = & headPublisher::getSingleton();
                $oHeadPublisher->addScriptFile( $filterForm->scriptURL );
                $oHeadPublisher->addScriptCode( $scriptCode );
                if (isset( $_SESSION )) {
                    $_SESSION[$filterForm->id] = $filterForm->values;
                }
            }
        }
        /**
         * ******** CONTENT BLOCK **************
         */
        if (($block === '') || ($block === 'content')) {
            $this->tpl->newBlock( 'contentBlock' );
            $this->tpl->assign( 'gridWidth', '=[' . substr( $this->gridWidth, 1 ) . ']' );
            $this->tpl->assign( 'fieldNames', '=[' . substr( $this->gridFields, 1 ) . ']' );
            $this->tpl->assign( 'ajaxUri', '="' . addslashes( $this->ajaxServer ) . '"' );
            $this->tpl->assign( 'currentUri', '="' . addslashes( $this->ownerPage ) . '"' );
            $this->tpl->assign( 'currentOrder', '="' . addslashes( $this->orderBy ) . '"' );
            $this->tpl->assign( 'currentPage', '=' . $this->currentPage );
            $this->tpl->assign( 'currentFilter', '="' . '"' );
            $this->tpl->assign( 'totalRows', '=' . $this->query->count() );
            $this->tpl->assign( 'rowsPerPage', '=' . $this->rowsPerPage );
            $this->tpl->assign( 'popupPage', '="' . addslashes( $this->popupPage ) . '"' );
            $this->tpl->assign( 'popupWidth', '=' . $this->popupWidth );
            $this->tpl->assign( 'popupHeight', '=' . $this->popupHeight );
            $this->tpl->assign( 'pagedTable_Id', $this->id );
            $this->tpl->assign( 'pagedTable_Name', $this->name );
            $this->tpl->assign( "pagedTable_JS", "{$this->id}.element=document.getElementById('pagedtable[{$this->id}]');" );
            $this->renderTitle();
            //Render rows
            $gridRows = 0;
            for ($j = 0; $j < $this->query->count(); $j ++) {
                $result = $this->query->read();
                //if (($j>=(($this->currentPage-1)*$this->rowsPerPage))&&($j<(($this->currentPage)*$this->rowsPerPage)))
                //{
                $gridRows ++;
                $this->tpl->newBlock( "row" );
                $this->tpl->assign( "class", "Row" . (($j % 2) + 1) );
                $this->tdStyle = '';
                $this->tdClass = '';
                foreach ($this->fields as $r => $rval) {
                    if (strcasecmp( $this->fields[$r]['Type'], 'cellMark' ) == 0) {
                        $result1 = $result;
                        $result1['row__'] = $j + 1;
                        $this->xmlForm->setDefaultValues();
                        $this->xmlForm->setValues( $result1 );
                        $result1 = array_merge( $this->xmlForm->values, $result1 );
                        $this->tdStyle = $this->xmlForm->fields[$this->fields[$r]['Name']]->tdStyle( $result1, $this->xmlForm );
                        $this->tdClass = $this->xmlForm->fields[$this->fields[$r]['Name']]->tdClass( $result1, $this->xmlForm );
                    } elseif ($this->style[$r]['showInTable'] != '0') {
                        $this->renderField( $j + 1, $r, $result );
                    }
                }
                //}
            }
            $this->tpl->assign( '_ROOT.gridRows', '=' . $gridRows ); //number of rows in the current page
            $this->tpl->newBlock( 'rowTag' );
            $this->tpl->assign( 'rowId', 'insertAtLast' );
            if ($this->currentPage > 1) {
                $firstUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=1';
                $firstAjax = $this->id . ".doGoToPage(1);return false;";
                $prevpage = $this->currentPage - 1;
                $prevUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $prevpage;
                $prevAjax = $this->id . ".doGoToPage(" . $prevpage . ");return false;";
                $first = "<a href=\"" . htmlentities( $firstUrl, ENT_QUOTES, 'utf-8' ) . "\" onclick=\"" . $firstAjax . "\" class='firstPage'>&nbsp;</a>";
                $prev = "<a href=\"" . htmlentities( $prevUrl, ENT_QUOTES, 'utf-8' ) . "\"  onclick=\"" . $prevAjax . "\" class='previousPage'>&nbsp;</a>";
            } else {
                $first = "<a class='noFirstPage'>&nbsp;</a>";
                $prev = "<a class='noPreviousPage'>&nbsp;</a>";
            }
            if ($this->currentPage < $this->totpages) {
                $lastUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $this->totpages;
                $lastAjax = $this->id . ".doGoToPage(" . $this->totpages . ");return false;";
                $nextpage = $this->currentPage + 1;
                $nextUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $nextpage;
                $nextAjax = $this->id . ".doGoToPage(" . $nextpage . ");return false;";
                $next = "<a href=\"" . htmlentities( $nextUrl, ENT_QUOTES, 'utf-8' ) . "\" onclick=\"" . $nextAjax . "\" class='nextPage'>&nbsp;</a>";
                $last = "<a href=\"" . htmlentities( $lastUrl, ENT_QUOTES, 'utf-8' ) . "\" onclick=\"" . $lastAjax . "\" class='lastPage'>&nbsp;</a>";
            } else {
                $next = "<a class='noNextPage'>&nbsp;</a>";
                $last = "<a class='noLastPage'>&nbsp;</a>";
            }
            $pagesEnum = '';
            for ($r = 1; $r <= $this->totpages; $r ++) {
                if (($r >= ($this->currentPage - 5)) && ($r <= ($this->currentPage + 5))) {
                    $pageAjax = $this->id . ".doGoToPage(" . $r . ");return false;";
                    if ($r != $this->currentPage) {
                        $pagesEnum .= "&nbsp;<a href=\"" . htmlentities( $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $r, ENT_QUOTES, 'utf-8' ) . "\" onclick=\"" . $pageAjax . "\">" . $r . "</a>";
                    } else {
                        $pagesEnum .= "&nbsp;<a>" . $r . "</a>";
                    }
                }
            }
            if ($this->query->count() === 0) {
                $this->tpl->newBlock( 'norecords' );
                $this->tpl->assign( "columnCount", $this->colCount );
                $noRecordsFound = 'ID_NO_RECORDS_FOUND';
                if (G::LoadTranslation( $noRecordsFound )) {
                    $noRecordsFound = G::LoadTranslation( $noRecordsFound );
                }
                $this->tpl->assign( "noRecordsFound", $noRecordsFound );
            }
            if (! $this->disableFooter) {
                $this->tpl->newBlock( "bottomFooter" );
                $this->tpl->assign( "columnCount", $this->colCount );
                $this->tpl->assign( "pagedTableId", $this->id );
                if (($this->query->count() !== 0)) {
                    if ($this->totpages > 1) {
                        $this->tpl->assign( "first", $first );
                        $this->tpl->assign( "prev", $prev );
                        $this->tpl->assign( "next", $next );
                        $this->tpl->assign( "last", $last );
                    }
                    $this->tpl->assign( "currentPage", $this->currentPage );
                    $this->tpl->assign( "totalPages", $this->totpages );
                    $firstRow = ($this->currentPage - 1) * $this->rowsPerPage + 1;
                    $lastRow = $firstRow + $this->query->count() - 1;
                    $this->tpl->assign( "firstRow", $firstRow );
                    $this->tpl->assign( "lastRow", $lastRow );
                    $this->tpl->assign( "totalRows", $this->totRows );
                } else {
                    $this->tpl->assign( "indexStyle", 'visibility:hidden;' );
                }
                if ($this->searchBy) {
                    $this->tpl->assign( "fastSearchValue", $this->fastSearch );
                } else {
                    $this->tpl->assign( "fastSearchStyle", 'visibility:hidden;' );
                }
                if ($this->addRow) {
                    if ($this->sqlInsert != '') {
                        $this->tpl->assign( "insert", '<a href="#" onclick="pagedTable.event=\'Insert\';popup(\'' . $this->popupPage . '\');return false;">'./*G::LoadXml('labels','ID_ADD_NEW')*/ 'ID_ADD_NEW' . '</a>' );
                    }
                }
                $this->tpl->assign( "pagesEnum", $pagesEnum );
            }
            ?>
    <script language='JavaScript'>
            var <?php echo $this->id?><?php echo ($this->name != '' ? '='.$this->name : '')?>=new G_PagedTable();
    <?php echo $this->id?>.id<?php echo '="'. addslashes($this->id) . '"'?>;
    <?php echo $this->id?>.name<?php echo '="'. addslashes($this->name) . '"'?>;
    <?php echo $this->id?>.ajaxUri<?php echo '="'. addslashes($this->ajaxServer) . '?ptID='.$this->id.'"'?>;
    <?php echo $this->id?>.currentOrder<?php echo '="'. addslashes($this->orderBy) . '"'?>;
    <?php echo $this->id?>.currentFilter;
    <?php echo $this->id?>.currentPage<?php echo '='. $this->currentPage?>;
    <?php echo $this->id?>.totalRows<?php echo '='.$this->query->count()?>;
    <?php echo $this->id?>.rowsPerPage<?php echo '='.$this->rowsPerPage?>;
    <?php echo $this->id?>.popupPage<?php echo '="'. addslashes($this->popupPage) . '?ptID='.$this->id.'"'?>;
    <?php echo $this->id?>.onUpdateField<?php echo '="'. addslashes($this->onUpdateField) . '"'?>;
    <?php echo $this->id?>.shownFields<?php echo '='.$this->shownFields ?>;

            var panelPopup;
            var popupWidth<?php echo '='.$this->popupWidth?>;
            var popupHeight<?php echo '='.$this->popupHeight?>;
            </script>
    <?php
        }
        /**
         * ******** CLOSE BLOCK **************
         */
        if (($block === '') || ($block === 'close')) {
            $this->tpl->newBlock( "closeBlock" );
        }
        $this->tpl->printToScreen();
        unset( $this->tpl );
        unset( $this->dbc );
        unset( $this->ses );
        $_SESSION['pagedTable[' . $this->id . ']'] = serialize( $this );
        return;
    }

    /**
     * Function printForm
     *
     * @access public
     * @param string $filename
     * @param array $data
     * @return void
     */
    public function printForm ($filename, $data = array())
    {
        //    $G_FORM = new Form($filename, PATH_XMLFORM);
        //    echo $G_FORM->render(PATH_TPL . 'xmlform.html', $scriptContent);
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $filename, '', $data, $this->popupSubmit );
        G::RenderPage( "publish", "blank" );
    }
}

/**
 * Function var_dump2
 *
 * @access public
 * @param string $o
 * @return void
 */
function var_dump2 ($o)
{
    if (is_object( $o ) || is_array( $o )) {
        foreach ($o as $key => $value) {
            echo ('<b>');
            var_dump( $key );
            echo ('</b>');
            print_r( $value );
            echo ('<br>');
        }
    } else {
        var_dump( $o );
    }
}

