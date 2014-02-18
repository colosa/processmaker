<?php
/**
 * class.xmlform.php
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
 * Class XmlForm_Field
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field
{
    public $name = '';
    public $type = 'field';
    public $label = '';
    public $pmLabel = '';
    public $owner;
    public $language;
    public $group = 0;
    public $mode = '';
    public $defaultValue = null;
    public $gridFieldType = '';
    public $gridLabel = '';
    /* Hint value generic declaration */
    public $hint = '';
    /*to change the presentation*/
    public $enableHtml = false;
    public $style = '';
    public $withoutLabel = false;
    public $className = '';
    /*attributes for paged table*/
    public $colWidth = 140;
    public $colAlign = 'left';
    public $colClassName = '';
    public $titleAlign = '';
    public $align = '';
    public $showInTable = '';
    /*Events*/
    public $onclick = '';
    /*attributes for data filtering*/
    /*dataCompareField = field to be compared with*/
    public $dataCompareField = '';
    /* $dataCompareType : '=' ,'<>' , 'like', ... , 'contains'(== ' like "%value%"')
    */
    public $dataCompareType = '=';
    public $sql = '';
    public $sqlConnection = '';
    //Attributes for PM Tables integration (only ProcessMaker for now)
    public $pmtable = '';
    public $keys = '';
    public $pmconnection = '';
    public $pmfield = '';

    // For mode cases Grid
    public $modeGrid = '';
    public $modeForGrid = '';

    /**
     * Function XmlForm_Field
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string xmlNode
     * @param string lang
     * @param string home
     * @return string
     */
    public function XmlForm_Field ($xmlNode, $lang = 'en', $home = '', $owner = null)
    {
        //Loads any attribute that were defined in the xmlNode
        //except name and label.
        $myAttributes = get_class_vars( get_class( $this ) );
        foreach ($myAttributes as $k => $v) {
            $myAttributes[$k] = strtoupper( $k );
        }
        //$data: Includes labels and options.
        $data = &$xmlNode->findNode( $lang );
        if (! isset( $data->value )) {
            //It seems that in the actual language there are no value for the current field, so get the value in English
            $data = &$xmlNode->findNode( "en" );
            if (! isset( $data->value )) {
                //It seems that in the actual language there are no value for the current field, so get the value in First language
                if (isset( $xmlNode->children[0] )) {
                    //Lets find first child
                    if ((isset( $xmlNode->children[0]->name )) && (strlen( $xmlNode->children[0]->name ) == 2)) {
                        //Just to be sure that the node ocrresponds to a valid lang
                        $data = &$xmlNode->findNode( $xmlNode->children[0]->name );
                    }
                }
            }
        }
        @$this->label = $this->pmLabel = $data->value;

        /*Loads the field attributes*/
        foreach ($xmlNode->attributes as $k => $v) {
            $key = array_search( strtoupper( $k ), $myAttributes );
            if ($key) {
                eval( '$this->' . $key . '=$v;' );
            }
        }
        //Loads the main attributes
        $this->name = $xmlNode->name;
        $this->type = strtolower( $xmlNode->attributes['type'] );
        preg_match( '/\s*([^\s][\w\W]*)?/m', $xmlNode->value, $matches );
        $this->sql = (isset( $matches[1] )) ? $matches[1] : '';
        //List Options
        if (isset( $data->children )) {
            foreach ($data->children as $k => $v) {
                if ($v->type !== 'cdata') {
                    if (isset($v->name) && isset($v->attributes["name"])) {
                        $this->{$v->name}[$v->attributes["name"]] = $v->value;
                    }
                }
            }
        }
        $this->options = (isset( $this->option )) ? $this->option : array ();
        //Sql Options : cause warning because values are not setted yet.
        //if ($this->sql!=='') $this->executeSQL();
        if (isset( $owner )) {
            if (isset( $owner->mode )) {
                $ownerMode = $owner->mode;
            } else {
                $ownerMode = '';
            }
        } else {
            $ownerMode = '';
        }
        if ($ownerMode != '') {
            $this->mode = $ownerMode;
        }
        if ($this->mode == '') {
            $this->mode = 'edit';
        }
        $this->modeForGrid = $this->mode;
    }

    /**
     * validate if a value is setted
     *
     * @param $value
     * @return boolean true/false
     */
    public function validateValue ($value)
    {
        return isset( $value );
    }

    /**
     * execute a xml query
     *
     * @param &$owner reference of owner
     * @param $row
     * @return $result array of results
     */
    private function executeXmlDB (&$owner, $row = -1)
    {
        if (! $this->sqlConnection) {
            $dbc = new DBConnection();
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
                $dbc = new DBConnection( $res['DBC_SERVER'], $res['DBC_USERNAME'], $res['DBC_PASSWORD'], $res['DBC_DATABASE'], $res['DBC_TYPE'] );
            } else {
                $dbc0 = new DBConnection();
                $dbs0 = new DBSession( $dbc0 );
                $res = $dbs0->execute( "select * from  DB_CONNECTION WHERE DBC_UID=" . $this->sqlConnection );
                $res = $res->read();
                $dbc = new DBConnection( $res['DBC_SERVER'], $res['DBC_USERNAME'], $res['DBC_PASSWORD'], $res['DBC_DATABASE'] );
            }
        }
        $query = G::replaceDataField( $this->sql, $owner->values );
        $dbs = new DBSession( $dbc );
        $res = $dbs->execute( $query );
        $result = array ();
        while ($row = $res->Read()) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Execute a propel query
     *
     * @param &$owner reference
     * @param $row
     * @return $result array of
     */
    private function executePropel (&$owner, $row = -1)
    {
        //g::pr($row);
        if (! isset( $owner->values[$this->name] )) {
            if ($row > - 1) {
                $owner->values[$this->name] = array ();
            } else {
                $owner->values[$this->name] = '';
            }
        }
        if (! is_array( $owner->values[$this->name] )) {
            //echo '1';
            $query = G::replaceDataField( $this->sql, $owner->values );
        } else {
            $aAux = array ();
            foreach ($owner->values as $key => $data) {
                if (is_array( $data )) {
                    //echo '3:'.$key.' ';
                    if (isset( $data[$row] )) {
                        $qValue = $data[$row];
                    } else {
                        if (isset( $owner->fields[$key]->selectedValue )) {
                            $qValue = $owner->fields[$key]->selectedValue;
                        } else {
                            $qValue = '';
                        }
                    }
                    $aAux[$key] = $qValue;
                    //$aAux [$key] = isset ( $data [$row] ) ? $data [$row] : '';
                } else {
                    //echo '4'.$key.' ';
                    $aAux[$key] = $data;
                }
            }

            //echo '2';
            //g::pr($aAux);
            $query = G::replaceDataField( $this->sql, $aAux );
        }
        //echo $query;

        $result = array ();
        if ($this->sqlConnection == 'dbarray') {
            try {
                $con = Propel::getConnection( $this->sqlConnection );
                $stmt = $con->createStatement();
                $rs = $con->executeQuery( $query, ResultSet::FETCHMODE_NUM );
            } catch (Exception $e) {
                //dismiss error because dbarray shouldnt be defined in some contexts.
                return $result;
            }
        } else {
            try {
                $con = Propel::getConnection( $this->sqlConnection );
                $stmt = $con->createStatement();
                $rs = $stmt->executeQuery( $query, ResultSet::FETCHMODE_NUM );
            } catch (Exception $e) {
                //dismiss error because dbarray shouldnt be defined in some contexts.
                return $result;
            }
        }

        $rs->next();
        $row = $rs->getRow();
        while (is_array( $row )) {
            $result[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $result;
    }

    /**
     * Function executeSQL
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string owner
     * @return string
     */
    public function executeSQL (&$owner, $row = -1)
    {
        if (! isset( $this->sql )) {
            return 1;
        }
        if ($this->sql === '') {
            return 1;
        }
        if (! $this->sqlConnection) {
            $this->sqlConnection = 'workflow';
        }

            //Read the result of the query
        if ($this->sqlConnection === "XMLDB") {
            $result = $this->executeXmlDB( $owner, $row );
        } else {
            $result = $this->executePropel( $owner, $row );
        }
        $this->sqlOption = array ();
        $this->options = array ();
        if (isset( $this->option )) {
            foreach ($this->option as $k => $v) {
                $this->options[$k] = $v;
            }
        }
        for ($r = 0; $r < sizeof( $result ); $r ++) {
            $key = reset( $result[$r] );
            $this->sqlOption[$key] = next( $result[$r] );
            $this->options[$key] = $this->sqlOption[$key];
        }

        if ($this->type != 'listbox') {
            if (isset( $this->options ) && isset( $this->owner ) && isset( $this->owner->values[$this->name] )) {
                if ((! is_array( $this->owner->values[$this->name] )) && ! ((is_string( $this->owner->values[$this->name] ) || is_int( $this->owner->values[$this->name] )) && array_key_exists( $this->owner->values[$this->name], $this->options ))) {
                    reset( $this->options );
                    $firstElement = key( $this->options );
                    if (isset( $firstElement )) {
                        $this->owner->values[$this->name] = $firstElement;
                    } else {
                        $this->owner->values[$this->name] = '';
                    }
                }
            }
        }
        return 0;
    }

    /**
     * return the html entities of a value
     *
     * @param <any> $value
     * @param <type> $flags
     * @param <String> $encoding
     * @return <any>
     */

    public function htmlentities ($value, $flags = ENT_QUOTES, $encoding = 'utf-8')
    {

        if ($this->enableHtml) {
            return $value;
        } else {
            return htmlentities( $value, $flags, $encoding );
        }
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null)
    {
        //this is an unknown field type.
        return $this->htmlentities( $value != '' ? $value : $this->name, ENT_COMPAT, 'utf-8' );
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null, $onlyValue = false, $therow = -1)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $result[] = $this->render( $v, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue, $r, $therow );
            $r ++;
        }
        return $result;
    }

    /**
     * render the field in a table
     *
     * @param $values
     * @param $owner
     * @param <Boolean> $onlyValue
     * @return <String> $result
     */
    public function renderTable ($values = '', $owner = null, $onlyValue = false)
    {
        $r = 1;
        $result = $this->render( $values, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue );
        return $result;
    }

    /**
     * Function dependentOf
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return array
     */
    public function dependentOf ()
    {
        $fields = array ();
        if (isset( $this->formula )) {
            preg_match_all( "/\b[a-zA-Z][a-zA-Z_0-9]*\b/", $this->formula, $matches, PREG_PATTERN_ORDER );
            /*      if ($this->formula!=''){
            var_dump($this->formula);
            var_dump($matches);
            var_dump(array_keys($this->owner->fields));
            die;
            }*/
            foreach ($matches[0] as $field) {
                //if (array_key_exists( $this->owner->fields, $field ))
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * Function mask
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string format
     * @param string value
     * @return string
     */
    public function mask ($format, $value)
    {
        $value = explode( '', $value );
        for ($j = 0; $j < strlen( $format ); $j ++) {
            $result = '';
            $correct = true;
            for ($i = $j; $i < strlen( $format ); $i ++) {
                $a = substr( $format, $i, 1 );
                $e = $i < strlen( $value ) ? substr( $value, $i, 1 ) : '';
                //$e=$i<strlen($format)?substr($format, $i+1,1):'';
                switch ($a) {
                    case '0':
                        if ($e === '') {
                            $e = '0';
                        }
                    case '#':
                        if ($e === '') {
                            break 3;
                        }
                        if (strpos( '0123456789', $e ) !== false) {
                            $result .= $e;
                        } else {
                            $correct = false;
                            break 3;
                        }
                        break;
                    case '.':
                        if ($e === '') {
                            break 3;
                        }
                        if ($e === $a) {
                            break 1;
                        }
                        if ($e !== $a) {
                            break 2;
                        }
                    default:
                        if ($e === '') {
                            break 3;
                        }
                        $result .= $e;
                }
            }
        }
        if ($e !== '') {
            $correct = false;
        }
        //##,###.##   --> ^...$ no parece pero no, o mejor si, donde # es \d?, en general todos
        // es valida cuando no encuentra un caracter que no deberia estar, puede no terminar la mascara
        // pero si sobran caracteres en el value entonces no se cumple la mascara.
        return $correct ? $result : $correct;
    }

    /**
     * Function getAttributes
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function getAttributes ()
    {
        $attributes = array ();
        foreach ($this as $attribute => $value) {
            switch ($attribute) {
                case 'sql':
                case 'sqlConnection':
                case 'name':
                case 'type':
                case 'owner':
                    break;
                default:
                    if (substr( $attribute, 0, 2 ) !== 'on') {
                        $attributes[$attribute] = $value;
                    }
            }
        }
        if (sizeof( $attributes ) < 1) {
            return '{}';
        }
        //$json = new Services_JSON();
        //return $json->encode( $attributes );
        return G::json_encode( $attributes );
    }

    /**
     * Function getEvents
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    public function getEvents ()
    {
        $events = array ();
        foreach ($this as $attribute => $value) {
            if (substr( $attribute, 0, 2 ) === 'on') {
                $events[$attribute] = $value;
            }
        }
        if (sizeof( $events ) < 1) {
            return '{}';
        }
        //$json = new Services_JSON();
        //return $json->encode( $events );
        return G::json_encode( $events );
    }

    /**
     * Function attachEvents: Attaches events to a control using
     * leimnud.event.add
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @param $elementRef
     * @access public
     */
    public function attachEvents ($elementRef)
    {
        $events = '';
        foreach ($this as $attribute => $value) {
            if (substr( $attribute, 0, 2 ) == 'on') {
                $events = 'if (' . $elementRef . ') leimnud.event.add(' . $elementRef . ',"' . substr( $attribute, 2 ) . '",function(){' . $value . '});' . "\n";
            }
        }
    }

    /**
     * Function createXmlNode: Creates an Xml_Node object storing
     * the data of $this Xml_Field.
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return Xml_Node
     */
    public function createXmlNode ($includeDefaultValues = false)
    {
        /* Start Comment: Creates the corresponding XML Tag for $this
        *    object.
        */
        $attributesList = $this->getXmlAttributes( $includeDefaultValues );
        $node = new Xml_Node( $this->name, 'open', $this->sql, $attributesList );
        /* End Comment */
        /* Start Comment: Creates the languages nodes and options
        *   if exist.
        */
        $node->addChildNode( new Xml_Node( '', 'cdata', "\n" ) );
        $node->addChildNode( new Xml_Node( $this->language, 'open', $this->label ) );
        if (isset( $this->option )) {
            foreach ($this->option as $k => $v) {
                $node->children[1]->addChildNode( new Xml_Node( 'option', 'open', $v, array ('name' => $k
                ) ) );
            }
        }
        /* End Comment */
        return $node;
    }

    /**
     * Function updateXmlNode: Updates and existing Xml_Node
     * with the data of $this Xml_Field.
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return Xml_Node
     */
    public function &updateXmlNode (&$node, $includeDefaultValues = false)
    {
        /* Start Comment: Modify the node's attributes and value.
        */
        $attributesList = $this->getXmlAttributes( $includeDefaultValues );
        $node->name = $this->name;
        $node->value = $this->sql;
        $node->attributes = $attributesList;
        /* End Comment */
        /* Start Comment: Modifies the languages nodes
        */
        $langNode = & $node->findNode( $this->language );
        $langNode->value = $this->label;
        if (isset( $this->option )) {
            $langNode->children = array ();
            foreach ($this->option as $k => $v) {
                $langNode->addChildNode( new Xml_Node( 'option', 'open', $v, array ('name' => $k
                ) ) );
            }
        }
        /* End Comment */
        return $node;
    }

    /**
     * Function getXmlAttributes: Returns an associative array
     * with the attributes of $this Xml_field (only the modified ones).
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param boolean includeDefaultValues Includes attributes
     * with default values.
     * @return Xml_Node
     */
    public function getXmlAttributes ($includeDefaultValues = false)
    {
        $attributesList = array ();
        $class = get_class( $this );
        $default = new $class( new Xml_Node( 'DEFAULT', 'open', '', array ('type' => $this->type
        ) ) );
        foreach ($this as $k => $v) {
            switch ($k) {
                case 'owner':
                case 'name':
                case 'type':
                case 'language':
                case 'sql':
                    break;
                default:
                    if (($v !== $default->{$k}) || $includeDefaultValues) {
                        $attributesList[$k] = $v;
                    }
            }
        }
        return $attributesList;
    }

    /**
     * Used in Form::validatePost
     *
     * @param $value
     * @param &$owner
     * @return $value
     */
    public function maskValue ($value, &$owner)
    {
        return $value;
    }
    /*Close this object*/
    /**
     * clone the current object
     *
     * @return <Object>
     */
    public function cloneObject ()
    {
        //return unserialize( serialize( $this ) );//con este cambio los formularios ya no funcionan en php4
        return clone ($this);
    }

    /**
     * get a value from a PM Table
     *
     * @param <Object> $oOwner
     * @return <String> $sValue
     */
    public function getPMTableValue ($oOwner)
    {
        $sValue = '';
        if (isset( $oOwner->fields[$this->pmconnection] )) {
            if (defined( 'PATH_CORE' )) {
                if (file_exists( PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php' )) {
                    require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
                    $oAdditionalTables = new AdditionalTables();
                    try {
                        $aData = $oAdditionalTables->load( $oOwner->fields[$this->pmconnection]->pmtable, true );
                    } catch (Exception $oError) {
                        $aData = array ('FIELDS' => array ());
                    }
                    $aKeys = array ();
                    $aValues = explode( '|', $oOwner->fields[$this->pmconnection]->keys );
                    $i = 0;
                    foreach ($aData['FIELDS'] as $aField) {
                        if ($aField['FLD_KEY'] == '1') {
                            // note added by gustavo cruz gustavo[at]colosa[dot]com
                            // this additional [if] checks if a case variable has been set
                            // in the keys attribute, so it can be parsed and replaced for
                            // their respective value.
                            if (preg_match( "/@#/", $aValues[$i] )) {
                                // check if a case are running in order to prevent that preview is
                                // erroneous rendered.
                                if (isset( $_SESSION['APPLICATION'] )) {
                                    G::LoadClass( 'case' );
                                    $oApp = new Cases();
                                    if ($oApp->loadCase( $_SESSION['APPLICATION'] ) != null) {
                                        $aFields = $oApp->loadCase( $_SESSION['APPLICATION'] );
                                        $formVariable = substr( $aValues[$i], 2 );
                                        if (isset( $aFields['APP_DATA'][$formVariable] )) {
                                            $formVariableValue = $aFields['APP_DATA'][$formVariable];
                                            $aKeys[$aField['FLD_NAME']] = (isset( $formVariableValue ) ? G::replaceDataField( $formVariableValue, $oOwner->values ) : '');
                                        } else {
                                            $aKeys[$aField['FLD_NAME']] = '';
                                        }
                                    } else {
                                        $aKeys[$aField['FLD_NAME']] = '';
                                    }
                                } else {
                                    $aKeys[$aField['FLD_NAME']] = '';
                                }
                            } else {
                                $aKeys[$aField['FLD_NAME']] = (isset( $aValues[$i] ) ? G::replaceDataField( $aValues[$i], $oOwner->values ) : '');
                            }
                            $i ++;
                        }
                    }
                    try {
                        $aData = $oAdditionalTables->getDataTable( $oOwner->fields[$this->pmconnection]->pmtable, $aKeys );
                    } catch (Exception $oError) {
                        $aData = array ();
                    }
                    if (isset( $aData[$this->pmfield] )) {
                        $sValue = $aData[$this->pmfield];
                    }
                }
            }
        }
        return $sValue;
    }

    /**
     * Prepares NS Required Value
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param boolean optional (true = always show, false = show only if not empty)
     * @return string
     */

    public function NSRequiredValue ($show = false)
    {
        if (isset( $this->required )) {
            $req = ($this->required) ? '1' : '0';
        } else {
            $req = '0';
        }
        $idv = 'pm:required="' . $req . '"';
        if ($show) {
            return $idv;
        } else {
            return ($req != '0') ? $idv : '';
        }
    }

    /**
     * Prepares NS Required Value
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param boolean optional (true = always show, false = show only if not empty)
     * @return string
     */

    public function NSGridLabel ($show = false)
    {
        $idv = 'pm:label="' . htmlentities($this->pmLabel, ENT_COMPAT, 'utf-8') . '"';
        if ($show) {
            return $idv;
        } else {
            return ($this->pmLabel != '') ? $idv : '';
        }
    }

    /**
     * Prepares NS Default Text
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param boolean optional (true = always show, false = show only if not empty)
     * @return string
     */
    public function NSDefaultValue ($show = false)
    {
        $idv = 'pm:defaultvalue="' . $this->defaultValue . '"';
        if ($show) {
            return $idv;
        } else {
            return ($this->defaultValue != '') ? $idv : '';
        }
    }

    /**
     * Prepares NS Field Type
     *
     * @author Julio Cesar Laura <contact@julio-laura.com>
     * @return string
     */
    public function NSFieldType ()
    {
        return 'pm:fieldtype="' . $this->type . '"';
    }

    /**
     * Prepares NS Grid Type
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param boolean optional (true = always show, false = show only if not empty)
     * @return string
     */
    public function NSGridType ($show = false)
    {
        $igt = 'pm:gridtype="' . $this->gridFieldType . '"';
        
        if ($show) {
            return $igt;
        } else {
            return ($this->gridFieldType != '') ? $igt : '';
        }
    }

    /**
     * Prepares NS Grid Type
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param boolean optional (true = always show, false = show only if not empty)
     * @return string
     */
    public function NSDependentFields ($show = false)
    {
        $idf = 'pm:dependent="' . (($this->dependentFields != '') ? '1' : '0') . '"';
        if ($show) {
            return $idf;
        } else {
            return ($this->dependentFields != '') ? $idf : '';
        }
    }

    /**
     * Prepares Hint HTML if hint value is defined
     *
     * @author Enrique Ponce de Leon <enrique@colosa.com>
     * @param void
     * @return string
     *
     */

    public function renderHint ()
    {
        $_outHint = '';
        if ($this->hint != '' && $this->mode == 'edit') {
            $_outHint = '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \'' . $this->hint . '\');return false;">
                     <image src="/images/help4.gif" width="13" height="13" border="0"/>
                   </a>';
        }
        return $_outHint;
    }
}

/**
 * Class XmlForm_Field_Title
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Title extends XmlForm_Field
{

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, &$owner = null)
    {
        $this->label = G::replaceDataField( $this->label, $owner->values );
        return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\'' . $this->NSFieldType() . '>' . $this->htmlentities( $this->label ) . '</span>';
    }

    /**
     * A title node has no value
     *
     * @param $value
     * @return false
     */
    public function validateValue ($value)
    {
        return false;
    }
}

/**
 * Class XmlForm_Field_Subtitle
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Subtitle extends XmlForm_Field
{

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null)
    {
        return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\'' . $this->NSFieldType() . '>' . $this->htmlentities( $this->label ) . '</span>';
    }

    /**
     * A subtitle node has no value
     *
     * @param $value
     * @return false
     */
    public function validateValue ($value)
    {
        return false;
    }
}

/**
 * Class XmlForm_Field_SimpleText
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_SimpleText extends XmlForm_Field
{
    public $size = 15;
    public $maxLength = '';
    public $validate = 'Any';
    public $mask = '';
    /* Additional events */
    public $onkeypress = '';
    public $renderMode = '';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, &$owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSRequiredValue() . ' ' . $this->NSFieldType() . ' readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
            } else {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSRequiredValue() . ' ' . $this->NSFieldType() . ' style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
            }
        } elseif ($this->mode === 'view') {
            return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSRequiredValue() . ' ' . $this->NSFieldType() . ' style="display:none;' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities( $value, ENT_COMPAT, 'utf-8' );
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @param string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $result = array ();
        $r = 1;
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }

        foreach ($values as $v) {
            $html = '';
            if ($this->renderMode === 'edit') {
                //EDIT MODE
                $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
                $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
                $html .= 'value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" ';
                $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= $this->NSRequiredValue() . ' ';
                $html .= $this->NSGridType() . ' ';
                $html .= $this->NSGridLabel() . ' ';
                $html .= '/>';
            } else {
                //VIEW MODE
                $html .= $this->htmlentities( $v, ENT_QUOTES, 'utf-8' );
                $html .= '<input ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'type="hidden" value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" />';
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }
}

/**
 * Class XmlForm_Field_Text
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Text extends XmlForm_Field_SimpleText
{
    public $size = 15;
    public $maxLength = 64;
    public $validate = 'Any';
    public $mask = '';
    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $linkField = '';
    //Possible values:(-|UPPER|LOWER|CAPITALIZE)
    public $strTo = '';
    public $readOnly = false;
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    //Attributes only for grids
    public $gridFieldType = 'text';
    public $formula = '';
    public $function = '';
    public $replaceTags = 0;
    public $renderMode = '';
    public $comma_separator = '.';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        } else {
            $this->executeSQL( $owner );
            $firstElement = key( $this->sqlOption );
            if (isset( $firstElement )) {
                $value = $firstElement;
            }
        }

        //NOTE: string functions must be in G class
        if ($this->strTo === 'UPPER') {
            $value = strtoupper( $value );
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower( $value );
        }
        if ($this->strTo === 'TITLE') {
            $value = strtolower( $value );
            $value = ucwords( $value );
        }
        if ($this->strTo === 'PHRASE') {
            //$value = strtolower( $value );

            $title = explode(" ",$value);

            $title[0] = ucwords( $title[0] );

            $value = implode(" ", $title);
        }
        //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
        if ($this->replaceTags == 1) {
            $value = G::replaceDataField( $value, $owner->values );
        }

        $html = '';
        if ($this->renderMode == 'edit') {
            //EDIT MODE
            $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
            $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
            $html .= 'value="' . $this->htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '" ';
            $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= 'onkeypress="' . $this->htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSFieldType() . ' ';
            $html .= 'pm:decimal_separator="' . $this->comma_separator . '" ';
            $html .= '/>';
        } else {
            //VIEW MODE
            $html .= $this->htmlentities( $value, ENT_QUOTES, 'utf-8' );
            $html .= '<input ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="hidden" value="' . $this->htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '" />';
        }

        $html .= $this->renderHint();
        if (($this->readOnly == 1) && ($this->renderMode == 'edit')) {
            $html = str_replace( "class=\"module_app_input___gray\"", "class=\"module_app_input___gray_readOnly\"", $html );
        }

        return $html;
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @param string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $result = $aux = array ();
        $r = 1;
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }

        foreach ($values as $v) {
            $this->executeSQL( $owner, $r );
            $firstElement = key( $this->sqlOption );
            if (isset( $firstElement )) {
                $v = $firstElement;
            }
            if ($this->replaceTags == 1) {
                $v = G::replaceDataField( $v, $owner->values );
            }
            $aux[$r] = $v;

            $html = '';
            if ($this->renderMode == 'edit') {
                //EDIT MODE
                $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
                $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
                $html .= 'value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" ';
                $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= $this->NSRequiredValue() . ' ';
                $html .= $this->NSGridLabel() . ' ';
                $html .= $this->NSGridType() . ' ';
                $html .= $this->NSDependentFields() . ' ';
                $html .= '/>';
            } else {
                //VIEW MODE
                $html .= $this->htmlentities( $v, ENT_QUOTES, 'utf-8' );
                $html .= '<input ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= 'type="hidden" value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" />';
            }

            $result[] = $html;
            $r ++;
        }
        $this->options = $aux;
        return $result;
    }

    public function renderTable ($values = '', $owner = null)
    {
        $result = $this->htmlentities( $values, ENT_COMPAT, 'utf-8' );
        return $result;
    }
}

/**
 * Class XmlForm_Field_Suggest
 *
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Suggest extends XmlForm_Field_SimpleText //by neyek
{
    public $size = 15;
    public $maxLength = 64;
    public $validate = 'Any';
    public $mask = '';
    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $linkField = '';
    //Possible values:(-|UPPER|LOWER|CAPITALIZE)
    public $strTo = '';
    public $readOnly = false;
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    public $searchType = "*searchtext*";
    //Atributes only for grids
    public $gridFieldType = 'suggest';
    public $formula = '';
    public $function = '';
    public $replaceTags = 0;

    public $ajaxServer = '../gulliver/genericAjax';
    public $maxresults = '6';
    public $savelabel = 1;
    public $shownoresults;
    public $callback = '';

    public $store_new_entry = '';
    public $table = '';
    public $table_data = '';
    public $primary_key = '';
    public $primary_key_data = '';
    public $primary_key_type = '';
    public $primary_key_type_data = '';

    public $field = '';

    /**
     * Function render
     *
     * @author Erik A. Ortiz.
     * @param $value
     * @param $owner
     * @return <String>
     */
    public function render ($value = null, $owner = null)
    {

        if (! $this->sqlConnection) {
            $this->sqlConnection = 'workflow';
        }

        //NOTE: string functions must be in G class
        if ($this->strTo === 'UPPER') {
            $value = strtoupper( $value );
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower( $value );
        }

            //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );

        if ($this->replaceTags == 1) {
            $value = G::replaceDataField( $value, $owner->values );
        }

        $aProperties = Array ('value' => '""','size' => '"' . $this->size . '"');

        $storeEntry = null;
        $storeEntryData = ", storeEntryData: [0]";

        if ($this->store_new_entry) {
            $storeEntry = ' title="' . G::LoadTranslation( "ID_FIELD_DYNAFORM_SUGGEST_INPUT_TITLE" ) . '"';
            $storeEntryData = ", storeEntryData: [1, \"form[" . $this->name . "_label]\", \"" . $this->sqlConnection . "\", \"" . $this->table . "\", \"" . $this->primary_key . "\", \"" . $this->primary_key_type . "\", \"" . $this->field . "\"]";
        }

        $formVariableValue = '';
        $formVariableKeyValue = '';
        G::LoadClass( 'case' );
        $oApp = new Cases();
        if (isset( $_SESSION['APPLICATION'] ) && ($_SESSION['APPLICATION'] != null && $oApp->loadCase( $_SESSION['APPLICATION'] ) != null)) {
            $aFields = $oApp->loadCase( $_SESSION['APPLICATION'] );
            if (isset( $aFields['APP_DATA'][$this->name . '_label'] )) {
                $formVariableValue = $aFields['APP_DATA'][$this->name . '_label'];
                $formVariableKeyValue = $aFields['APP_DATA'][$this->name];
            }
        }

        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSFieldType() . ' readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
            } else {
                //        $str = '<textarea '.$storeEntry.' class="module_app_input___gray" style="height:16px" rows=1 cols="'.$this->size.'" id="form[' . $this->name . ']" name="form[' . $this->name . ']" >'.$this->htmlentities($value, ENT_COMPAT, 'utf-8').'</textarea>';
                if (strlen( trim( $formVariableValue ) ) > 0) {
                    $value = $formVariableValue;
                }
                $name = "'" . $this->name . "'";
                $str = '<input type="text" ' . $storeEntry . ' class="module_app_input___gray" size="' . $this->size . '" id="form[' . $this->name . '_label]" name="form[' . $this->name . '_label]" value="' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '" ' . $this->NSFieldType() . ' onblur="idSet(' . $name . ');" ';
                $str .= $this->NSDependentFields( true ) . ' ';
                $str .= '/>';
                $str .= '<input ';
                $str .= 'id="form[' . $this->name . ']" ';
                $str .= 'name="form[' . $this->name . ']" ';
                $str .= 'value="' . $this->htmlentities( $formVariableKeyValue, ENT_COMPAT, 'utf-8' ) . '" ';
                $str .= 'type="hidden" />';

                $str .= $this->renderHint();
                if (trim( $this->callback ) != '') {
                    $sCallBack = 'try{' . $this->callback . '}catch(e){alert("Suggest Widget call back error: "+e)}';
                } else {
                    $sCallBack = '';
                }

                $hash = str_rot13( base64_encode( $this->sql . '@|' . $this->sqlConnection ) );
                $sSQL = $this->sql;
                $nCount = preg_match_all( '/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/', $sSQL, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );

                $aResult = array ();
                if ($nCount) {
                    for ($i = 0; $i < $nCount; $i ++) {
                        if (isset( $match[0][$i][0] ) && isset( $match[2][$i][0] )) {
                            $aResult[$match[0][$i][0]] = $match[2][$i][0];
                        }
                    }
                }

                $depValues = '';
                $i = 1;
                if (isset( $aResult ) && $aResult) {
                    $sResult = '"' . implode( '","', $aResult ) . '"';
                    $aResultKeys = array_keys( $aResult );
                    $sResultKeys = str_rot13( base64_encode( implode( '|', $aResultKeys ) ) );

                    foreach ($aResult as $key => $field) {
                        $depValues .= 'getField(\'' . $field . '\').value';
                        if ($i ++ < count( $aResult )) {
                            $depValues .= '+"|"+';
                        }

                    }
                    $depValues = '+' . $depValues . '+';
                } else {
                    $sResult = '';
                    $sResultKeys = '';
                    $depValues = '+';
                }

                $aDepFields = array ();
                $count = 0;
                if ($this->dependentFields !== '') {
                    $dependentFields = explode( ",", $this->dependentFields );
                    foreach ($dependentFields as $keyDependent => $valueDependent) {
                        $sqlDepField = $owner->fields[$valueDependent]->sql;
                        $count = preg_match_all( '/\@(?:([\@\%\#\=\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/', $sqlDepField, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
                        for ($cnt = 0; $cnt < $count; $cnt ++) {
                            $aDepFields[$cnt] = $match[2][$cnt][0];
                        }
                    }
                }

                $sOptions = 'script: function (input) { ';
                $sOptions .= '  var inputValue = base64_encode(getField(\'' . $this->name . '_label\').value); ';

                $sOptions .= '  return "' . $this->ajaxServer . '?request=suggest&json=true&limit=' . $this->maxresults;
                $sOptions .= '&hash=' . $hash . '&dependentFieldsKeys=' . $sResultKeys . '&dependentFieldsValue="';

                $sOptions .= $depValues . '"&input="+inputValue+"&inputEnconde64=enable&searchType=' . $this->searchType . '";';

                $sOptions .= '},';
                $sOptions .= 'json: true,';
                $sOptions .= 'limit: ' . $this->maxresults . ',';

                $sOptions .= 'shownoresults: ' . ($this->shownoresults ? 'true' : 'false') . ',';
                $sOptions .= 'maxresults: ' . $this->maxresults . ',';
                $sOptions .= 'chache: true,';

                $setValue = ($this->savelabel == '1') ? 'obj.value' : 'obj.id';

                $sOptions .= 'callback: function (obj) { ';
                $sOptions .= 'if (typeof obj != "undefined") { ';
                $sOptions .= ' var jField = { };';
                $sOptions .= ' var sField = "[]"; ';

                if ($count > 0) {
                    for ($cnt = 0; $cnt < $count; $cnt ++) {
                        if ( $this->name == $aDepFields[$cnt] ) {
                            $sOptions .= '  jField[\'' . $aDepFields[$cnt] . '\'] = obj.id;';
                        } else {
                            $sOptions .= '  jField[\'' . $aDepFields[$cnt] . '\'] = getField(\'' . $aDepFields[$cnt] . '\').value; ';
                        }
                    }
                }

                $sOptions .= ' var sField = "["+ encodeURIComponent(jField.toJSONString()) + "]"; ';

                $sOptions .= $sCallBack . '; getField("' . $this->name . '").value = obj.id;';
                $sOptions .= 'var response = ajax_function("../gulliver/defaultAjaxDynaform", "reloadField", ';
                $sOptions .= '               "form=' . $owner->id . '&fields=" + sField, "POST"); ';

                $sOptions .= 'if (response.substr(0,1) === \'[\') { ';
                $sOptions .= '  var newcont; ';
                $sOptions .= '  eval(\'newcont=\' + response + \';\'); ';
                $sOptions .= '  for(var i = 0; i<newcont.length; i++) { ';
                //$sOptions .= '    var j = getField(newcont[i].name); ';
                $sOptions .= '    getField(newcont[i].name).value = newcont[i].value; ';
                $sOptions .= '
                                  switch (newcont[i].content.type) {
                                      case "dropdown":
                                          dropDownSetOption({element: getField(newcont[i].name), name: newcont[i].name}, newcont[i].content);
                                          break;
                                      case "text":
                                          getField(newcont[i].name).value = "";

                                          if (newcont[i].content.options) {
                                              if (newcont[i].content.options[0]) {
                                                  getField(newcont[i].name).value = newcont[i].content.options[0].value;
                                              }
                                          }
                                          break;
                                  }
                ';
                $sOptions .= '  } ';
                $sOptions .= '} else { ';
                $sOptions .= '  alert(\'Invalid response: \' + response); ';
                $sOptions .= '} ';
                $sOptions .= '} ';
                $sOptions .= 'return false; ';
                $sOptions .= '}';

                $str .= '<script type="text/javascript">';
                $str .= 'var as_json = new bsn.AutoSuggest(\'form[' . $this->name . '_label]\', {' . $sOptions . $storeEntryData . '}, "' . $this->searchType . '");';
                $str .= '</script>';

                return $str;
            }
        } else {
            return $this->htmlentities( $formVariableValue, ENT_COMPAT, 'utf-8' );
        }
    }

   /**
     * render Field Grid
     *
     * @param type $value
     * @param type $owner
     * @param type $rowId
     * @param type $ownerName   Grid Name
     * @param type $index       Index on the grid
     * @return string
     */
    public function renderFieldGrid($value = null, $owner = null, $rowId = '', $ownerName = '', $index = 0)
    {
        $rowIdField = substr($rowId, 1);
        if (! $this->sqlConnection) {
            $this->sqlConnection = 'workflow';
        }

        if ($this->strTo === 'UPPER') {
            $value = strtoupper( $value );
        }
        if ($this->strTo === 'LOWER') {
            $value = strtolower( $value );
        }
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );

        if ($this->replaceTags == 1) {
            $value = G::replaceDataField( $value, $owner->values );
        }

        $storeEntry = null;
        $storeEntryData = ", storeEntryData: [0]";
        if ($this->store_new_entry) {
            $storeEntry = ' title="' . G::LoadTranslation( "ID_FIELD_DYNAFORM_SUGGEST_INPUT_TITLE" ) . '"';
            $storeEntryData = ", storeEntryData: [1, \"form" . $rowId . "[" . $this->name . "_label]\", \"" . $this->sqlConnection . "\", \"" . $this->table . "\", \"" . $this->primary_key . "\", \"" . $this->primary_key_type . "\", \"" . $this->field . "\"]";
        }

        $formVariableValue = '';
        $formVariableKeyValue = '';
        G::LoadClass( 'case' );
        $oApp = new Cases();
        if (isset( $_SESSION['APPLICATION'] ) && ($_SESSION['APPLICATION'] != null && $oApp->loadCase( $_SESSION['APPLICATION'] ) != null)) {
            $aFields = $oApp->loadCase( $_SESSION['APPLICATION'] );
            if (isset( $aFields['APP_DATA'][$ownerName][$index][$this->name . '_label'] )) {

                $formVariableValue = $aFields['APP_DATA'][$ownerName][$index][$this->name . '_label'];
                $formVariableKeyValue = $aFields['APP_DATA'][$ownerName][$index][$this->name];
            }
        }

        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form' . $rowId . '[' . $this->name . ']" name="form' . $rowId . '[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' .  $this->NSGridType() . ' readOnly="readOnly" style="' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
            } else {
                if (strlen( trim( $formVariableValue ) ) > 0) {
                    $value = $formVariableValue;
                }
                $name = "'" . $this->name . "'";
                $str = '<input type="text" ' . $storeEntry . ' class="module_app_input___gray" size="' . $this->size . '" id="form' . $rowId . '[' . $this->name . '_label]" name="form' . $rowId . '[' . $this->name . '_label]" value="' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '" ' . $this->NSGridType() . ' onblur="idSet(' . $name . ');" ';
                $str .= $this->NSDependentFields( true ) . ' ';
                $str .= $this->NSRequiredValue() . ' ';
                $str .= $this->NSGridLabel() . ' ';
                $str .= '/>';
                $str .= '<input ';
                $str .= 'id="form' . $rowId . '[' . $this->name . ']" ';
                $str .= 'name="form' . $rowId . '[' . $this->name . ']" ';
                $str .= 'value="' . $this->htmlentities( $formVariableKeyValue, ENT_COMPAT, 'utf-8' ) . '" ';
                $str .= 'type="hidden" />';

                //$str .= $this->renderHint();
                if (trim( $this->callback ) != '') {
                    $sCallBack = 'try{' . $this->callback . '}catch(e){alert("Suggest Widget call back error: "+e)}';
                } else {
                    $sCallBack = '';
                }

                $hash = str_rot13( base64_encode( $this->sql . '@|' . $this->sqlConnection ) );
                $sSQL = $this->sql;
                $nCount = preg_match_all( '/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/', $sSQL, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );

                $aResult = array ();
                if ($nCount) {
                    for ($i = 0; $i < $nCount; $i ++) {
                        if (isset( $match[0][$i][0] ) && isset( $match[2][$i][0] )) {
                            $aResult[$match[0][$i][0]] = $match[2][$i][0];
                        }
                    }
                }

                $depValues = '';
                $depFieldsLast = '';
                $i = 1;
                if (isset( $aResult ) && $aResult) {
                    $sResult = '"' . implode( '","', $aResult ) . '"';
                    $aResultKeys = array_keys( $aResult );
                    $sResultKeys = str_rot13( base64_encode( implode( '|', $aResultKeys ) ) );

                    foreach ($aResult as $key => $field) {
                        $depValues .= 'getField(\''. $rowIdField . '[' . $field . '\').value';

                        if ($i ++ < count( $aResult )) {
                            $depValues .= '+"|"+';
                        }

                    }
                    $depFieldsLast = 'getField(\''. $rowIdField . '[' . $field . '\').value';
                    $depValues = '+' . $depValues . '+';
                } else {
                    $sResult = '';
                    $sResultKeys = '';
                    $depValues = '+';
                }

                $sOptions = 'script: function (input) { ';

                $sOptions .= '  var inputValue = base64_encode(getField(\'' . $rowIdField . '[' . $this->name . '_label\').value); ';

                $sOptions .= '  return "' . $this->ajaxServer . '?request=suggest&json=true&limit=' . $this->maxresults;
                $sOptions .= '&hash=' . $hash . '&dependentFieldsKeys=' . $sResultKeys . '&dependentFieldsValue="';
                $sOptions .= $depValues . '"&input="+inputValue+"&inputEnconde64=enable&searchType=' . $this->searchType . '";';

                $sOptions .= '},';
                $sOptions .= 'json: true,';
                $sOptions .= 'limit: ' . $this->maxresults . ',';

                $sOptions .= 'shownoresults: ' . ($this->shownoresults ? 'true' : 'false') . ',';
                $sOptions .= 'maxresults: ' . $this->maxresults . ',';
                $sOptions .= 'chache: true,';

                $sOptions .= 'callback: function (obj) { ';

                $sOptions .= 'if (typeof obj != "undefined") { ';
                $sOptions .= ' var aFieldCurrent = {};';
                $sOptions .= ' aFieldCurrent[\'' . $this->name . '\'] = obj.id;';
                $sOptions .= ' var sFieldCurrent = "["+ encodeURIComponent(aFieldCurrent.toJSONString()) + "]"; ';
                $sOptions .= $sCallBack . '; getField("' . $rowIdField . '[' . $this->name . '").value = obj.id;';

                $sOwnerId = (isset($owner->owner->id))? $owner->owner->id : $owner->id;
                $sOptions .= 'var indexField =  "' . $rowIdField . '[' . $this->name . '";';
                $sOptions .= 'indexField = indexField.match(/\[[0-9]+\]/g); ';
                $sOptions .= 'indexFieldVal = indexField[0].replace(/\[|\]/g,""); ';
                $sOptions .= 'var gridField = gridGetAllFieldAndValue("' . $ownerName . '][" + indexFieldVal + "][' . $this->name . '", 0); '; //Not get current field
                $sOptions .= 'var response = ajax_function("../gulliver/defaultAjaxDynaform", "reloadField", "form=' . $sOwnerId . '&fields=" + sFieldCurrent + "&grid=' . $ownerName . '" + ((gridField != "")? "&gridField=" + encodeURIComponent("{" + gridField + "}") : "") + "&row=" + indexFieldVal, "POST"); ';
                $sOptions .= '';
                $sOptions .= 'if (response.substr(0,1) === \'[\') { ';
                $sOptions .= '  var newcont; ';
                $sOptions .= '  eval(\'newcont=\' + response + \';\'); ';
                $sOptions .= '';
                $sOptions .= '  for(var i = 0; i<newcont.length; i++) { ';
                $sOptions .= '    var depField = "' . $rowIdField . '[" + newcont[i].name; ';
                $sOptions .= '    getField(depField).value = newcont[i].value; ';
                $sOptions .= '
                                  switch (newcont[i].content.type) {
                                      case "dropdown":
                                          dropDownSetOption({element: getField(depField), name: depField}, newcont[i].content);
                                          break;
                                      case "text":
                                          getField(depField).value = "";

                                          if (newcont[i].content.options) {
                                              if (newcont[i].content.options[0]) {
                                                  getField(depField).value = newcont[i].content.options[0].value;
                                              }
                                          }
                                          break;
                                  }
                ';
                $sOptions .= '  } ';
                $sOptions .= '} else { ';
                $sOptions .= '  alert(\'Invalid response: \' + response); ';
                $sOptions .= '} ';
                $sOptions .= '} ';
                $sOptions .= 'return false; ';
                $sOptions .= '}';

                $str .= '<script type="text/javascript">';
                $str .= 'var as_json = new bsn.AutoSuggest(\'form' .  $rowId . '[' . $this->name . '_label]\', {' . $sOptions . $storeEntryData . '}, "' . $this->searchType . '"); ';
                $str .= '</script>';

                return $str;
            }
        } else {
            return $this->htmlentities( $formVariableValue, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @param string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $aResult = array();
        $r = 1;

        foreach ($values as $v) {
            $aResult[] = $this->renderFieldGrid( $v, $owner, '[' . $owner->name . '][' . $r . ']', $owner->name, $r );
            $r++;
        }
        return $aResult;
    }

    /**
     * render in a table
     *
     * @param <any> $values
     * @param <String> $owner
     * @return <String> $result
     */
    public function renderTable ($values = '', $owner = null)
    {
        $result = $this->htmlentities( $values, ENT_COMPAT, 'utf-8' );
        return $result;
    }
}

/**
 * prepare the field for printing
 *
 * @package gulliver.system
 */
class XmlForm_Field_Print extends XmlForm_Field_SimpleText //by neyek
{
    //Instead of public --> link
    public $link = '';
    public $value = '';
    public $target = '';
    public $colClassName = 'RowLink';

    //properties
    public $width;
    public $height;
    public $top;
    public $left;
    public $resizable;

    /**
     * Function render
     *
     * @param string value
     * @return string
     */
    //750, 450, 10, 32, 1
    public function render ($value = null, $owner = null)
    {
        $onclick = G::replaceDataField( $this->onclick, $owner->values );
        $link = G::replaceDataField( $this->link, $owner->values );
        $target = G::replaceDataField( $this->target, $owner->values );
        $value = G::replaceDataField( $this->value, $owner->values );
        $label = G::replaceDataField( $this->label, $owner->values );

        $html = '<a href="javascript:;" onclick="dynaFormPrint(\'' . $owner->parentFormId . '\', \'' . $this->htmlentities( $link, ENT_QUOTES, 'utf-8' ) . '\', ' . $this->width . ', ' . $this->height . ', ' . $this->left . ', ' . $this->top . ', ' . $this->resizable . '); return false;">
                  <image title="' . $this->htmlentities( $label, ENT_QUOTES, 'utf-8' ) . '" src="/images/printer.png" width="15" height="15" border="0"/>
                  </a>';

        return $html;
    }
}

/*DEPRECATED*/
/**
 * caption field for dynaforms
 *
 * @package gulliver.system
 */
class XmlForm_Field_Caption extends XmlForm_Field
{

    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $readonly = false;
    public $option = array ();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    public $saveLabel = 0;
    //public $hint;


    /**
     *
     * @param $value
     * @param $owner
     * @return true
     */
    public function validateValue ($value, &$owner)
    {
        /*$this->executeSQL( $owner );
        return isset($value) && ( array_key_exists( $value , $this->options ) );*/
        return true;
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string modified
     */
    public function render ($value = null, $owner = null, $rowId = '', $onlyValue = false, $row = -1, $therow = -1)
    {

        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        if ($therow == - 1) {
            //print_r($this->executeSQL ( $owner, $row ));print"<hr>";
            $this->executeSQL( $owner, $row );
        } else {
            if ($row == $therow) {
                $this->executeSQL( $owner, $row );
            }
        }
        $html = '';

        if (! $onlyValue) {
            foreach ($this->option as $optionName => $option) {
                if ($optionName == $value) {
                    $value = $option;
                }
            }
            foreach ($this->sqlOption as $optionName => $option) {
                if ($optionName == $value) {
                    $value = $option;
                }
            }

        } else {
            foreach ($this->option as $optionName => $option) {
                if ($optionName == $value) {
                    $$value = $option;
                }
            }
            foreach ($this->sqlOption as $optionName => $option) {
                if ($optionName == $value) {
                    $value = $option;
                }
            }
        }
        $pID = "form[$this->name]";
        $htm = $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        $htm .= '<input type="hidden" id="' . $pID . '" name="' . $pID . '" value="' . $value . '">';
        return $htm;
    }
}

/**
 * Class XmlForm_Field_Password
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Password extends XmlForm_Field
{
    public $size = 15;
    public $maxLength = 15;
    public $required = false;
    public $readOnly = false;
    public $autocomplete = "on";

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null)
    {
        if ($this->autocomplete === '1') {
            $this->autocomplete = "on";
        } else {
            if ($this->autocomplete === '0') {
                $this->autocomplete = "off";
            }
        }

        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" autocomplete="' . $this->autocomplete . '" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSFieldType() . 'readOnly="readOnly"/>';
            } else {
                $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" autocomplete="' . $this->autocomplete . '" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '\' ' . $this->NSFieldType() . '/>';
                $html .= $this->renderHint();
                return $html;
            }
        } elseif ($this->mode === 'view') {
            $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="hidden" autocomplete="' . $this->autocomplete . '" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) .  '\'' . $this->NSFieldType() . ' readOnly="readOnly"/>';
            $html .= $this->htmlentities( str_repeat( '*', 10 ), ENT_COMPAT, 'utf-8' );
            return $html;
        } else {
            return $this->htmlentities( str_repeat( '*', 10 ), ENT_COMPAT, 'utf-8' );
        }
    }
}

/**
 * Class XmlForm_Field_Textarea
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Textarea extends XmlForm_Field
{
    public $rows = 12;
    public $cols = 40;
    public $required = false;
    public $readOnly = false;
    public $wrap = 'OFF';
    public $className;
    public $renderMode = '';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {

        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        } else {
            $this->executeSQL( $owner );
            if (isset( $this->sqlOption )) {
                $firstElement = key( $this->sqlOption );
            }
            if (isset( $firstElement )) {
                $value = $firstElement;
            }
        }

        $className = isset( $this->className ) ? $this->className : 'module_app_input___gray';

        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }

        $html = '';
        $scrollStyle = $this->style . "overflow:scroll;overflow-y:scroll;overflow-x:hidden;overflow:-moz-scrollbars-vertical;";
        if ($this->renderMode == 'edit') {
            //EDIT MODE
            $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
            $html .= '<textarea ' . $readOnlyText . ' ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'wrap="soft" cols="' . $this->cols . '" rows="' . $this->rows . '" ';
            $html .= 'style="' . $scrollStyle . '" wrap="' . $this->htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSFieldType() . ' ';
            $html .= 'class="' . $className . '" >';
            $html .= $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
            $html .= '</textarea>';
        } else {
            //VIEW MODE
            $html .= '<textarea readOnly ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'wrap="soft" cols="' . $this->cols . '" rows="' . $this->rows . '" ';
            $html .= 'style="border:0px;backgroud-color:inherit;' . $scrollStyle . '" wrap="' . $this->htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" ';
            $html .= 'class="FormTextArea" >';
            $html .= $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
            $html .= '</textarea>';
        }

        $html .= $this->renderHint();
        return $html;
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $this->gridFieldType = 'textarea';

        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }

        $result = array();
        $arrayOptions = array();

        $r = 1;

        foreach ($values as $v) {
            $this->executeSQL($owner, $r);

            if (isset($this->sqlOption)) {
                $firstElement = key($this->sqlOption);
            }

            if (isset($firstElement)) {
                $v = $firstElement;
            }

            $arrayOptions[$r] = $v;

            $scrollStyle = $this->style . "overflow:scroll;overflow-y:scroll;overflow-x:hidden;overflow:-moz-scrollbars-vertical;";
            $html = '';
            if ($this->renderMode == 'edit') {
                //EDIT MODE
                $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
                $html .= '<textarea ' . $readOnlyText . ' class="module_app_input___gray" ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'wrap="soft" cols="' . $this->cols . '" rows="' . $this->rows . '" ';
                $html .= 'style="' . $scrollStyle . '" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= $this->NSRequiredValue() . ' ';
                $html .= $this->NSGridType() . ' ';
                $html .= $this->NSGridLabel() . ' ';
                $html .= '>';
                $html .= $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                $html .= '</textarea>';
            } else {
                //VIEW MODE
                $html .= '<textarea readOnly ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'wrap="soft" cols="' . $this->cols . '" rows="' . $this->rows . '" ';
                $html .= 'style="' . $scrollStyle . '" wrap="' . $this->htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" ';
                $html .= 'class="FormTextArea" >';
                $html .= $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                $html .= '</textarea>';
            }
            $result[] = $html;
            $r ++;
        }

        $this->options = $arrayOptions;
        return $result;
    }
}

/**
 * Class XmlForm_Field_Currency
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Currency extends XmlForm_Field_SimpleText
{
    public $group = 0;
    public $size = 15;
    public $required = false;
    public $linkField = '';
    public $readOnly = false;
    public $maxLength = 15;

    public $mask = '_###,###,###,###;###,###,###,###.## $';
    public $currency = '$';
    //Atributes only for grids
    public $formula = '';
    public $function = '';
    public $gridFieldType = 'currency';
    public $comma_separator = '.';

    /**
     * render the field in a dynaform
     *
     * @param <String> $value
     * @param <String> $owner
     * @return <String>
     */
    public function render ($value = null, $owner = null)
    {
        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );

        $html = '';
        $currency = preg_replace( '/([_;#,.])/', '', $this->mask );
        if (! $value) {
            $value = $currency;
        }
        if ($this->renderMode == 'edit') {
            //EDIT MODE
            $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
            $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
            $html .= 'value="' . $this->htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '" ';
            $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= 'onkeypress="' . $this->htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSFieldType() . ' ';
            $html .= 'pm:decimal_separator="' . $this->comma_separator . '" ';
            $html .= '/>';
        } else {
            //VIEW MODE
            $html .= $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
            $html .= '<input ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="hidden" value="' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '" />';
        }
        if (($this->readOnly == 1) && ($this->renderMode == 'edit')) {
            $html = str_replace( "class=\"module_app_input___gray\"", "class=\"module_app_input___gray_readOnly\"", $html );
        }
        $html .= $this->renderHint();

        return $html;

    }

    /**
     * Function renderGrid
     *
     * @author alvaro campos sanchez <alvaro@colosa.com>
     * @access public
     * @param string values
     * @param string owner
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $result = array ();
        $r = 1;
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }

        foreach ($values as $v) {
            $html = '';
            $currency = preg_replace( '/([_;#,.])/', '', $this->mask );
            if (! $v) {
                $v = $currency;
            }
            if ($this->renderMode === 'edit') {
                //EDIT MODE
                $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
                $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
                $html .= 'value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" ';
                $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= $this->NSRequiredValue() . ' ';
                $html .= $this->NSGridType() . ' ';
                $html .= $this->NSGridLabel() . ' ';
                $html .= '/>';
            } else {
                //VIEW MODE
                $html .= $this->htmlentities( $v, ENT_QUOTES, 'utf-8' );
                $html .= '<input ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'type="hidden" value="' . $this->htmlentities( $v, ENT_QUOTES, 'utf-8' ) . '" />';
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }
}

/*DEPRECATED*/
/**
 *
 * @package gulliver.system
 */
class XmlForm_Field_CaptionCurrency extends XmlForm_Field
{

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null)
    {
        return '$ ' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
    }
}

/**
 * Class XmlForm_Field_Percentage
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Percentage extends XmlForm_Field_SimpleText
{
    public $size = 15;
    public $required = false;
    public $linkField = '';
    public $readOnly = false;
    public $maxLength = 15;
    public $mask = '###.## %';
    //Atributes only for grids
    public $formula = '';
    public $function = '';
    public $gridFieldType = 'percentage';
    public $comma_separator = '.';

    public function render ($value = null, $owner = null)
    {

        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }
        $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );

        $html = '';

        if ($this->renderMode == 'edit') {
            //EDIT MODE
            $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
            $html .= '<input ' . $readOnlyText . ' class="module_app_input___gray" ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
            $html .= 'value="' . $this->htmlentities( $value, ENT_QUOTES, 'utf-8' ) . '" ';
            $html .= 'style="' . $this->htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= 'onkeypress="' . $this->htmlentities( $onkeypress, ENT_COMPAT, 'utf-8' ) . '" ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSFieldType() . ' ';
            $html .= 'pm:decimal_separator="' . $this->comma_separator . '" ';
            $html .= '/>';
        } else {
            //VIEW MODE
            $html .= $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
            $html .= '<input ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="hidden" value="' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '" />';
        }

        if (($this->readOnly == 1) && ($this->renderMode == 'edit')) {
            $html = str_replace( "class=\"module_app_input___gray\"", "class=\"module_app_input___gray_readOnly\"", $html );
        }
        $html .= $this->renderHint();
        return $html;

        //    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
        //    if ($this->mode === 'edit') {
        //      if ($this->readOnly)
        //        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
        //      else {
        //
        //        $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
        //
        //        if($this->hint){
        //           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
        //                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
        //                </a>';
        //        }
        //
        //        return $html;
        //      }
        //    } elseif ($this->mode === 'view') {
        //      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
        //    } else {
        //      return $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' );
        //    }


    }
}

/*DEPRECATED*/
/**
 *
 * @package gulliver.system
 */
class XmlForm_Field_CaptionPercentage extends XmlForm_Field
{

    public function render ($value = null)
    {
        return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
    }
}

/**
 * Class XmlForm_Field_Date
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Date2 extends XmlForm_Field_SimpleText
{
    //Instead of size --> startDate
    public $startDate = '';
    //Instead of maxLength --> endDate
    public $endDate = '';
    //for dinamically dates,   beforeDate << currentDate << afterDate
    // beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
    // afterDate='5y' means five year after,  afterDate='15d' means 15 days after
    // startDate and endDate have priority over beforeDate and AfterDate.
    public $afterDate = '';
    public $beforeDate = '';
    public $defaultValue = null;
    public $format = 'Y-m-d';
    public $required = false;
    public $readOnly = false;
    public $mask = 'yyyy-mm-dd';
    public $dependentFields = '';

    /**
     * Verify the date format
     *
     * @param $date
     * @return Boolean true/false
     */
    public function verifyDateFormat ($date)
    {
        $aux = explode( '-', $date );
        if (count( $aux ) != 3) {
            return false;
        }
        if (! (is_numeric( $aux[0] ) && is_numeric( $aux[1] ) && is_numeric( $aux[2] ))) {
            return false;
        }
        if ($aux[0] < 1900 || $aux[0] > 2100) {
            return false;
        }
        return true;
    }

    /**
     * checks if a date has he correct format
     *
     * @param $date
     * @return <Boolean>
     */
    public function isvalidBeforeFormat ($date)
    {
        $part1 = substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );
        if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y') {
            return false;
        }
        if (! is_numeric( $part1 )) {
            return false;
        }
        return true;
    }

    /**
     * Calculate the date before the format
     *
     * @param <type> $date
     * @param <type> $sign
     * @return <date> $res date based on the data insert
     */
    public function calculateBeforeFormat ($date, $sign)
    {
        $part1 = $sign * substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );
        switch ($part2) {
            case 'd':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + $part1, date( 'Y' ) ) );
                break;
            case 'm':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ) + $part1, date( 'd' ), date( 'Y' ) ) );
                break;
            case 'y':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + $part1 ) );
                break;

        }
        return $res;
    }

    /**
     * render the field in a dynaform
     *
     * @param $value
     * @param $owner
     * @return <String>
     */
    public function render ($value = null, $owner = null)
    {

        $value = G::replaceDataField( $value, $owner->values );
        $startDate = G::replaceDataField( $this->startDate, $owner->values );
        $endDate = G::replaceDataField( $this->endDate, $owner->values );
        $beforeDate = G::replaceDataField( $this->beforeDate, $owner->values );
        $afterDate = G::replaceDataField( $this->afterDate, $owner->values );
        //for backward compatibility size and maxlength
        if ($startDate != '') {
            if (! $this->verifyDateFormat( $startDate )) {
                $startDate = '';
            }
        }
        if (isset( $beforeDate ) && $beforeDate != '') {
            if ($this->isvalidBeforeFormat( $beforeDate )) {
                $startDate = $this->calculateBeforeFormat( $beforeDate, - 1 );
            }
        }

        if ($startDate == '' && isset( $this->size ) && is_numeric( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
            $startDate = $this->size . '-01-01';
        }

        if ($startDate == '') {
            $startDate = date( 'Y-m-d' ); // the default is the current date
        }

        //for backward compatibility maxlength
        //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
        //for backward compatibility size and maxlength
        if ($endDate != '') {
            if (! $this->verifyDateFormat( $endDate )) {
                $endDate = '';
            }
        }

        if (isset( $afterDate ) && $afterDate != '') {
            if ($this->isvalidBeforeFormat( $afterDate )) {
                $endDate = $this->calculateBeforeFormat( $afterDate, + 1 );
            }
            if ($endDate) {
                $sign = '1';
                $date = $afterDate;
                $part1 = $sign * substr( $date, 0, strlen( $date ) - 1 );
                $part2 = substr( $date, strlen( $date ) - 1 );
                switch ($part2) {
                    case 'd':
                        $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + $part1, date( 'Y' ) ) );
                        break;
                    case 'm':
                        $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ) + $part1, date( 'd' ) - 1, date( 'Y' ) ) );
                        break;
                    case 'y':
                        $res = (intVal( date( 'Y' ) ) + $part1) . '-' . date( 'm' ) . '-' . date( 'd' );
                        break;
                }

                $endDate = $res;

            }
        }

        if (isset( $this->maxlength ) && is_numeric( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
            $endDate = $this->maxlength . '-01-01';
        }
        if ($endDate == '') {
            //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
            $endDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) ); // the default is the current date + 2 years
        }
        if ($value == '') {
            $value = date( 'Y-m-d' );
        }
        $html = "<input type='hidden' id='form[" . $this->name . "]' name='form[" . $this->name . "]' value='" . $value . "'>";
        $html .= "<span id='span[" . $owner->id . "][" . $this->name . "]' name='span[" . $owner->id . "][" . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $value . " </span> ";
        if ($this->mode == 'edit') {
            $html .= "<a href='#' onclick=\"showDatePicker(event,'" . $owner->id . "', '" . $this->name . "', '" . $value . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
        }
        return $html;
    }

    /**
     * render the field in a grid
     *
     * @param $values
     * @param $owner
     * @param $onlyValue
     * @return <String>
     */
    public function renderGrid ($values = null, $owner = null, $onlyValue = false)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $v = G::replaceDataField( $v, $owner->values );
            $startDate = G::replaceDataField( $this->startDate, $owner->values );
            $endDate = G::replaceDataField( $this->endDate, $owner->values );
            $beforeDate = G::replaceDataField( $this->beforeDate, $owner->values );
            $afterDate = G::replaceDataField( $this->afterDate, $owner->values );
            //for backward compatibility size and maxlength
            if ($startDate != '') {
                if (! $this->verifyDateFormat( $startDate )) {
                    $startDate = '';
                }
            }
            if ($startDate == '' && isset( $beforeDate ) && $beforeDate != '') {
                if ($this->isvalidBeforeFormat( $beforeDate )) {
                    $startDate = $this->calculateBeforeFormat( $beforeDate, - 1 );
                }
            }

            if ($startDate == '' && isset( $this->size ) && is_numeric( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
                $startDate = $this->size . '-01-01';
            }

            if ($startDate == '') {
                $startDate = date( 'Y-m-d' ); // the default is the current date
            }

            //for backward compatibility maxlength
            //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
            //for backward compatibility size and maxlength
            if ($endDate != '') {
                if (! $this->verifyDateFormat( $endDate )) {
                    $endDate = '';
                }
            }

            if ($endDate == '' && isset( $afterDate ) && $afterDate != '') {
                if ($this->isvalidBeforeFormat( $afterDate )) {
                    $endDate = $this->calculateBeforeFormat( $afterDate, + 1 );
                }
            }

            if ($endDate == '' && isset( $this->maxlength ) && is_numeric( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
                $endDate = $this->maxlength . '-01-01';
            }
            if ($endDate == '') {
                //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
                $endDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) ); // the default is the current date + 2 years
            }
            if ($v == '') {
                $v = date( 'Y-m-d' );
            }
            if (! $onlyValue) {
                $html = "<input type='hidden' id='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' name='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' value='" . $v . "'>";
                if (isset( $owner->owner->id )) {
                    $html .= "<span id='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
                } else {
                    $html .= "<span id='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
                }
                if ($this->mode == 'edit') {
                    $html .= "<a href='#' onclick=\"showDatePicker(event,'" . (isset( $owner->owner ) ? $owner->owner->id : $owner->id) . "', '" . $owner->name . '][' . $r . '][' . $this->name . "', '" . $v . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
                }
            } else {
                $html = $v;
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }
}

/*DEPRECATED*/
/**
 *
 * @package gulliver.system
 */
class XmlForm_Field_DateView extends XmlForm_Field
{

    public function render ($value = null)
    {
        return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
    }
}

/**
 * Class XmlForm_Field_YesNo
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_YesNo extends XmlForm_Field
{
    public $required = false;
    public $readonly = false;
    public $renderMode = '';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        if ($value == '') {
            $value = '0';
        }
        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }
        $html = '';
        if ($this->renderMode == 'edit') {
            //EDIT MODE
            $readOnlyText = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
            $html .= '<select ' . $readOnlyText . ' class="module_app_input___gray" ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSFieldType() . ' ';
            $html .= '>';
            $html .= '<option value="0"' . (($value === '0') ? ' selected' : '') . '>' . G::LoadTranslation( 'ID_NO_VALUE' ) . '</option>';
            $html .= '<option value="1"' . (($value === '1') ? ' selected' : '') . '>' . G::LoadTranslation( 'ID_YES_VALUE' ) . '</option>';
            $html .= '</select>';
            if ($readOnlyText != '') {
                $html .= '<input ';
                $html .= 'id="form[' . $this->name . ']" ';
                $html .= 'name="form[' . $this->name . ']" ';
                $html .= 'type="hidden" value="' . (($value === '0') ? '0' : '1') . '" />';
            }
        } else {
            //VIEW MODE
            $html .= '<span id="form[' . $this->name . ']">';
            $html .= ($value === '0') ? G::LoadTranslation( 'ID_NO_VALUE' ) : G::LoadTranslation( 'ID_YES_VALUE' );
            $html .= '<input ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="hidden" value="' . (($value === '0') ? '0' : '1') . '" />';
        }

        $html .= $this->renderHint();
        return $html;
    }

    /**
     * render the field in a grid
     *
     * @param $values
     * @param $owner
     * @return <array>
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $this->gridFieldType = 'yesno';
        $result = array ();
        $r = 1;
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }
        foreach ($values as $v) {
            $html = '';
            if ($v == '') {
                $v = '0';
            }
            if ($this->renderMode == 'edit') {
                //EDIT MODE
                $readOnlyText = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
                $html .= '<select ' . $readOnlyText . ' class="module_app_input___gray" ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= $this->NSDefaultValue() . ' ';
                $html .= $this->NSRequiredValue() . ' ';
                $html .= $this->NSGridLabel() . ' ';
                $html .= $this->NSGridType() . ' ';
                $html .= '>';
                $html .= '<option value="0"' . (($v === '0') ? ' selected="selected"' : '') . '>' . G::LoadTranslation( 'ID_NO_VALUE' ) . '</option>';
                $html .= '<option value="1"' . (($v === '1') ? ' selected="selected"' : '') . '>' . G::LoadTranslation( 'ID_YES_VALUE' ) . '</option>';
                $html .= '</select>';
                if ($readOnlyText != '') {
                    $html .= '<input ';
                    $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                    $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                    $html .= 'type="hidden" value="' . (($v === '0') ? '0' : '1') . '" />';
                }
            } else {
                //VIEW MODE
                $html .= ($v === '0') ? G::LoadTranslation( 'ID_NO_VALUE' ) : G::LoadTranslation( 'ID_YES_VALUE' );
                $html .= '<input ';
                $html .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" ';
                $html .= $this->NSGridType() . ' ';
                $html .= 'type="hidden" value="' . (($v === '0') ? '0' : '1') . '" />';
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }
}

/**
 * Class XmlForm_Field_Link
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Link extends XmlForm_Field
{
    //Instead of var --> link
    public $link = '';
    public $value = '';
    public $target = '';
    public $style = '';
    public $colClassName = 'RowLink';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render($value = null, $label = null, $owner = null, $row = -1)
    {
        $id = null;
        $v = null;

        switch ($owner->type) {
            case "grid":
                $id = $owner->name . "][" . $row . "][" . $this->name;
                $v = (isset($owner->values[$owner->name][$row]))? $owner->values[$owner->name][$row] : array();
                break;
            default:
                $id = $this->name;
                $v = $owner->values;
                break;
        }

        $link = "";
        if ($this->link != "") {
            $link = G::replaceDataField($this->link, $v);
        } else {
            $link = !empty($value) ? $value : "";
        }
        $labelAux1 = (!empty($label))? $label : G::replaceDataField($this->label, $v);
        $labelAux2 = (!empty($label))? $label : G::replaceDataField($this->value, $v);
        $onclick = G::replaceDataField($this->onclick, $v);
        $target = G::replaceDataField($this->target, $v);

        $html = "<a class=\"tableOption\" href=\"" . $this->htmlentities($link, ENT_QUOTES, "utf-8") . "\"";
        $html = $html . " id=\"form[$id]\" name=\"form[$id]\" pm:field=\"pm:field\"";
        $html .= $this->NSFieldType() . ' ';
        if ((strrpos($_SERVER['HTTP_USER_AGENT'], "Chrome") === false ? false : true) && trim($this->htmlentities($link, ENT_QUOTES, "utf-8")) === "#") {
            $html = $html . (($this->onclick) ? " onclick=\"" . htmlentities($onclick, ENT_QUOTES, "utf-8") . " return false;\"" : " onclick=\" return false;\"");
        } else {
            $html = $html . (($this->onclick) ? " onclick=\"" . htmlentities($onclick, ENT_QUOTES, "utf-8") . "\"" : null);
        }
        $html = $html . (($this->target)? " target=\"" . htmlentities($target, ENT_QUOTES, "utf-8") . "\"" : null);

        switch ($owner->type) {
            case "grid":
                if ($this->mode == "view") {
                    $html = $html . " style=\"color: #006699; text-decoration: none; font-weight: normal;\"";
                }
                break;
            default:
                $html = $html . " style=\"" . htmlentities($this->style, ENT_QUOTES, "utf-8") . "\"";
                break;
        }

        $html = $html . ">" . $this->htmlentities(($this->value == "")? $labelAux1 : $labelAux2, ENT_QUOTES, "utf-8") . "</a>";

        switch ($owner->type) {
            case "grid":
                break;
            default:
                $html = $html . $this->renderHint();
                break;
        }

        return $html;
    }

    /**
     * render the field in a grid
     *
     * @param $values
     * @param $owner
     * @return <array>
     */
    public function renderGrid($value = array(), $label = array(), $owner = null)
    {
        $arrayResult = array();
        $row = 1;

        foreach ($value as $index => $v) {
            $arrayResult[] = $this->render(
                (isset($value[$index]))? $value[$index] : null,
                (isset($label[$index]))? $label[$index] : null,
                $owner,
                $row
            );

            $row = $row + 1;
        }

        return $arrayResult;
    }

    /**
     * render the field in a table
     *
     * @param $values
     * @param $owner
     * @return <String>
     */
    public function renderTable ($value = null, $owner = null)
    {
        $onclick = $this->htmlentities( G::replaceDataField( $this->onclick, $owner->values ), ENT_QUOTES, 'utf-8' );
        $link = $this->htmlentities( G::replaceDataField( $this->link, $owner->values ), ENT_QUOTES, 'utf-8' );
        $target = G::replaceDataField( $this->target, $owner->values );
        $value = G::replaceDataField( $this->value, $owner->values );
        $label = G::replaceDataField( $this->label, $owner->values );
        $aLabel = $this->htmlentities( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' );
        if (isset( $aLabel ) && strlen( $aLabel ) > 0) {
            return '<a class="tableOption" href=\'' . $link . '\'' . (($this->onclick) ? ' onclick="' . $onclick . '"' : '') . (($this->target) ? ' target="' . htmlentities( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>' . $aLabel . '</a>';
        } else {
            return '';
        }
    }
}

/**
 * Class XmlForm_Field_File
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_File extends XmlForm_Field
{
    public $required = false;
    public $input = null;

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null, $rowId = null, $row = -1, $therow = -1)
    {
        $permission = false;
        $url = null;

        if (isset( $_SESSION["APPLICATION"] ) && isset( $_SESSION["USER_LOGGED"] ) && isset( $_SESSION["TASK"] ) && isset( $this->input ) && $this->input != null && $this->mode == "view") {
            require_once ("classes/model/AppDocument.php");
            G::LoadClass( "case" );

            $case = new Cases();
            $arrayField = $case->loadCase( $_SESSION["APPLICATION"] );
            $arrayPermission = $case->getAllObjects( $arrayField["PRO_UID"], $_SESSION["APPLICATION"], $_SESSION["TASK"], $_SESSION["USER_LOGGED"] );

            $criteria = new Criteria();
            $criteria->add( AppDocumentPeer::APP_DOC_UID, $arrayPermission["INPUT_DOCUMENTS"], Criteria::IN );

            switch ($owner->type) {
                case "xmlform":
                    break;
                case "grid":
                    $criteria->add( AppDocumentPeer::APP_DOC_FIELDNAME, $owner->name . "_" . $row . "_" . $this->name );
                    break;
            }

            $criteria->addDescendingOrderByColumn( AppDocumentPeer::APP_DOC_CREATE_DATE );
            $rsCriteria = AppDocumentPeer::doSelectRS( $criteria );
            $rsCriteria->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $sw = 0;

            while (($rsCriteria->next()) && $sw == 0) {
                $row = $rsCriteria->getRow();

                if ($row["DOC_UID"] == $this->input) {
                    $permission = true;
                    $url = ((G::is_https()) ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . dirname( $_SERVER["REQUEST_URI"] ) . "/cases_ShowDocument?a=" . $row["APP_DOC_UID"] . "&v=" . $row["DOC_VERSION"];
                    $sw = 1;
                }
            }
        }

        $html1 = null;
        $html2 = null;
        $mode = ($this->mode == "view") ? " disabled=\"disabled\"" : null;
        $styleDisplay = null;

        if ($this->mode == "view") {
            if ($permission) {
                $html1 = "<a href=\"" . $url . "\"" . (($owner->type == "grid") ? " class=\"tableOption\" style=\"color: #006699; text-decoration: none; font-weight: normal;\"" : null) . ">";
                $html2 = "</a>";
            }

            $html1 = $html1 . $value;
            $styleDisplay = "display: none;";
        }

        $html = $html1 . "<input type=\"file\" id=\"form" . $rowId . "[" . $this->name . "]\" name=\"form" . $rowId . "[" . $this->name . "]\" " .$this->NSFieldType(). " value=\"" . $value . "\"class=\"module_app_input___gray_file\" style=\"" . $styleDisplay . "\"" . $mode . " " . $this->NSRequiredValue() . " />" . $html2;
        if (isset( $this->input ) && $this->input != null) {
            require_once ("classes/model/InputDocument.php");

            try {
                $indoc = new InputDocument();
                $aDoc = $indoc->load( $this->input );
                $aDoc["INP_DOC_TITLE"] = (isset( $aDoc["INP_DOC_TITLE"] )) ? $aDoc["INP_DOC_TITLE"] : null;
                $html = $html . "<label><img src=\"/images/inputdocument.gif\" width=\"22px\" width=\"22px\" alt=\"\" /><font size=\"1\">(" . trim( $aDoc["INP_DOC_TITLE"] ) . ")</font></label>";
            } catch (Exception $e) {
                //Then the input document doesn"t exits, id referencial broken
                $html = $html . "&nbsp;<font color=\"red\"><img src=\"/images/alert_icon.gif\" width=\"16px\" width=\"16px\" alt=\"\" /><font size=\"1\">(" . G::loadTranslation( "ID_INPUT_DOC_DOESNT_EXIST" ) . ")</font></font>";
            }
        }

        $html = $html . $this->renderHint();

        return $html;
    }

    public function renderGrid ($value = array(), $owner = null, $therow = -1)
    {
        $arrayResult = array ();
        $r = 1;

        foreach ($value as $v) {
            $arrayResult[] = $this->render( $v, $owner, "[" . $owner->name . "][" . $r . "]", $r, $therow );
            $r = $r + 1;
        }

        return $arrayResult;
    }
}

/**
 * Class XmlForm_Field_Dropdownpt
 * hook, dropdown field for Propel table
 *
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Dropdownpt extends XmlForm_Field
{
    public $value;

    public function render ($value = null, $owner = null)
    {
        $this->value = $value;

        $id = $this->value->id;
        $value = isset( $this->value->value ) ? $this->value->value : '';
        $items = $this->value->items;

        $res = '<select id="form[' . $id . ']" name="form[' . $this->name . ']" class="module_app_input___gray"><option value="0"></option>';
        foreach ($items as $k => $v) {
            $res .= '<option value="' . $k . '">' . $v . '</option>';
        }
        $res .= "</select>";
        return $res;
    }

    /* Used in Form::validatePost
    */
    public function maskValue ($value, &$owner)
    {
        return ($value === $this->value) ? $value : $this->falseValue;
    }
}

/**
 * Class XmlForm_Field_Checkboxpt
 * checkbox field for Propel table
 *
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Checkboxpt extends XmlForm_Field
{
    public $required = false;
    public $value = 'on';
    public $falseValue = 'off';
    public $labelOnRight = true;

    /**
     * Render the field in a dynaform
     *
     * @param $value
     * @param $owner
     * @return <>
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        $checked = (isset( $value ) && ($value == $this->value)) ? 'checked' : '';
        $res = "<input id='form[" . $this->name . "][{$this->value}]' value='{$this->value}' name='form[" . $this->name . "][{$this->value}]' type='checkbox' />";
        return $res;
    }

    /**
     * Render the field in a grid
     *
     * @param $value
     * @param $owner
     * @return <Array> result
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $checked = (($v == $this->value) ? 'checked="checked"' : '');
            $disabled = (($this->value == 'view') ? 'disabled="disabled"' : '');
            $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled />";
            $result[] = $html;
            $r ++;
        }
        return $result;
    }

    /**
     * Used in Form::validatePost
     *
     * @param $value
     * @param &$owner
     * @return either the value or falseValue attributes
     */
    public function maskValue ($value, &$owner)
    {
        return ($value === $this->value) ? $value : $this->falseValue;
    }
}

/**
 * Class XmlForm_Field_Checkbox
 *
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Checkbox extends XmlForm_Field
{
    public $required = false;
    public $value = 'on';
    public $falseValue = 'off';
    public $labelOnRight = false;
    public $readOnly = false;

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }

        $disabled = '';
        if ($this->readOnly === 'readonly' or $this->readOnly === '1') {
            $readOnly = 'readonly="readonly" onclick="javascript: return false;"'; //$disabled = "disabled";
        } else {
            $readOnly = '';
        }

        $checked = (isset( $value ) && ($value == $this->value)) ? 'checked' : '';

        if ($this->mode === 'edit') {
            //$readOnly = isset ( $this->readOnly ) && $this->readOnly ? 'disabled' : '';
            if ($this->labelOnRight) {
                $res = "<input id='form[" . $this->name . "]' value='{$this->value}' " . $this->NSFieldType() . " name='form[" . $this->name . "]' type='checkbox' $checked $readOnly $disabled><span class='FormCheck'>" . $this->label . '</span></input>';
            } else {
                $res = "<input id='form[" . $this->name . "]' value='{$this->value}' " . $this->NSFieldType() . " name='form[" . $this->name . "]' type='checkbox' $checked $readOnly $disabled/>";
            }
            $res .= $this->renderHint();

            //      $res = "<input id='form[" . $this->name . "]' value='" . $this->name . "' name='form[" .$this->name . "]' type='checkbox' $checked $readOnly >" . $this->label ;
            return $res;
        } elseif ($this->mode === 'view') {
            $checked = (isset( $value ) && ($value == $this->value)) ? 'checked' : '';
            if ($this->labelOnRight) {
                $html = '';
                $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly disabled >
                 <span class='FormCheck'>" . $this->label . '</span></input>';
            } else {
                $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly disabled/>";
            }
            $html .= "<input id='form[" . $this->name . "]' value='{$value}' name='form[" . $this->name . "]' type='hidden' />";
            //      if($this->hint){
            //           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
            //                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
            //                </a>';
            //      }
            return $html;
        }
    }

    /**
     * Render the field in a grid
     *
     * @param $value
     * @param $owner
     * @return <Array> result
     */
    public function renderGrid ($values = array(), $owner = null)
    {
        $this->gridFieldType = 'checkbox';
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $checked = (($v == $this->value) ? 'checked="checked"' : '');
            if ($this->readOnly === 'readonly' or $this->readOnly === '1') {
                $disabled = "disabled";
            } else {
                $disabled = '';
            }
            if ($this->mode === 'edit') {
                $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' falseValue= " . $this->falseValue . "  name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled readonly = '{$this->readOnly}' " . $this->NSDefaultValue() . " " . $this->NSGridType() . "/>";
                $result[] = $html;
                $r ++;
            } else {
                //$disabled = (($this->value == 'view') ? 'disabled="disabled"' : '');
                $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' falseValue= " . $this->falseValue . " name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked disabled readonly = '{$this->readOnly}' " . $this->NSDefaultValue() . " " . $this->NSGridType() . "/>";
                $result[] = $html;
                $r ++;
            }
        }
        return $result;
    }

    /**
     * Used in Form::validatePost
     *
     * @param $value
     * @param $owner
     * @return either the value or falseValue
     */
    public function maskValue ($value, &$owner)
    {
        return ($value === $this->value) ? $value : $this->falseValue;
    }
}

/*DEPRECATED*/
/**
 *
 * @package gulliver.system
 */
class XmlForm_Field_Checkbox2 extends XmlForm_Field
{
    public $required = false;

    public function render ($value = null)
    {
        return '<input class="FormCheck" name="' . $this->name . '" type ="checkbox" disabled>' . $this->label . '</input>';
    }
}

/**
 * Class XmlForm_Field_Button
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Button extends XmlForm_Field
{
    public $onclick = '';
    public $align = 'center';
    public $style;

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        $onclick = G::replaceDataField( $this->onclick, $owner->values );
        $label = G::replaceDataField( $this->label, $owner->values );
        if ($this->mode === 'edit') {
            $re = "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" " . $this->NSFieldType() . "name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
            return $re;
        } elseif ($this->mode === 'view') {
            return "<input style=\"{$this->style};display:none\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }
}

/**
 * Class XmlForm_Field_Reset
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Reset extends XmlForm_Field
{

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        $onclick = G::replaceDataField( $this->onclick, $owner->values );
        $mode = ($this->mode == 'view') ? ' disabled="disabled"' : '';
        //return '<input name="'.$this->name.'" type ="reset" value="'.$this->label.'"/>';
        //    return "<input style=\"{$this->style}\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
        if ($this->mode === 'edit') {
            return "<input style=\"{$this->style}\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" " . $this->NSFieldType() . "name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
        } elseif ($this->mode === 'view') {
            return "<input style=\"{$this->style};display:none\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }

    }
}

/**
 * Class XmlForm_Field_Submit
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Submit extends XmlForm_Field
{
    public $onclick = '';

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        $onclick = G::replaceDataField( $this->onclick, $owner->values );

        if ($this->mode === 'edit') {
            //      if ($this->readOnly)
            //        return '<input id="form['.$this->name.']" name="form['.$this->name.']" type=\'submit\' value=\''. $this->label .'\' disabled/>';
            return "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" " . $this->NSFieldType() . " name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
         } elseif ($this->mode === 'view') {
            //return "<input style=\"{$this->style};display:none\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
            //$sLinkNextStep = 'window.open("' . $owner->fields['__DYNAFORM_OPTIONS']->xmlMenu->values['NEXT_STEP'] . '", "_self");';
            $html = '';
            if (isset( $_SESSION['CURRENT_DYN_UID'] )) {
                $sLinkNextStep = 'window.location=("casesSaveDataView?UID=' . $_SESSION['CURRENT_DYN_UID'] . '");';
                $html = '<input style="' . $this->style . '" class="module_app_button___gray ' . $this->className . '" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type="button" value="' . G::LoadTranslation( 'ID_CONTINUE' ) . '"  onclick="' . htmlentities( $sLinkNextStep, ENT_COMPAT, 'utf-8' ) . '" />';
            }

            $html .= '<input ';
            $html .= 'id="form[' . $this->name . ']" ';
            $html .= 'name="form[' . $this->name . ']" ';
            $html .= 'type="hidden" value="' . $this->htmlentities( $this->label, ENT_QUOTES, 'utf-8' ) . '" />';
            return $html;
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }
}

/**
 * Class XmlForm_Field_Hidden
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Hidden extends XmlForm_Field
{
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    public $dependentFields = '';
    public $gridFieldType = 'hidden';
    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        } else {
            $this->executeSQL( $owner );

            if (isset( $this->sqlOption )) {
                reset( $this->sqlOption );
                $firstElement = key( $this->sqlOption );
                if (isset( $firstElement )) {
                    $value = $firstElement;
                }
            }
        }
        if ($this->mode === 'edit') {
            return '<input id="form[' . $this->name . ']" ' . $this->NSFieldType() . ' name="form[' . $this->name . ']" type=\'hidden\' value=\'' . $value . '\'/>';
        } elseif ($this->mode === 'view') {
            //a button? who wants a hidden field be showed like a button?? very strange.
            return '<input id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'text\' value=\'' . $value . '\' style="display:none"/>';
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Render the field in a grid
     *
     * @param $value
     * @param $owner
     * @return <Array> result
     */
    public function renderGrid ($values = null, $owner = null)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $result[] = '<input type="hidden" ' . $this->NSGridType() . 'value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" />';
            $r ++;
        }

        return $result;
    }

            //$arrayResult[] = $this->render( $v, $owner, "[" . $owner->name . "][" . $r . "]", $r, $therow );
        
    /**
     * Render the field in a table
     *
     * @param $value
     * @param $owner
     * @return <Array> result
     */
    public function renderTable ($value = '', $owner = null)
    {
        return '<input id="form[' . $this->name . ']"  name="form[' . $this->name . ']" type=\'hidden\' value=\'' . $value . '\'/>';
    }
}

/**
 * Class XmlForm_Field_Dropdown
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Dropdown extends XmlForm_Field
{
    public $defaultValue = '';
    public $required = false;
    public $dependentFields = '';
    public $readonly = false;
    public $optgroup = 0;
    public $option = array();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array();
    public $saveLabel = 0;
    public $modeGridDrop = '';
    public $renderMode = '';
    public $selectedValue = '';

    public function validateValue ($value, &$owner)
    {
        /*$this->executeSQL( $owner );
        return isset($value) && ( array_key_exists( $value , $this->options ) );*/
        return true;
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null, $rowId = '', $onlyValue = false, $row = -1, $therow = -1)
    {
        $displayStyle = '';

        //Returns value from a PMTable when it is exists.
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        //Recalculate SQL options if $therow is not defined or the row id equal
        if ($therow == - 1) {
            //echo 'Entro:'.$this->dependentFields;
            $this->executeSQL( $owner, $row );
        } else {
            if ($row == $therow) {
                $this->executeSQL( $owner, $row );
            }
        }

        $html = '';
        $displayLabel = '';

        if ($this->renderMode == '') {
            $this->renderMode = $this->mode;
        }

        if (! $onlyValue) {
            //Render Field if not defined onlyValue
            if ($this->renderMode != 'edit') {
                //EDIT MODE
                $displayStyle = 'display:none;';
            }
            $readOnlyField = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
            $html = '<select ' . $readOnlyField . ' class="module_app_input___gray" ';
            $html .= 'id="form' . $rowId . '[' . $this->name . ']" ';
            $html .= 'name="form' . $rowId . '[' . $this->name . ']" ';
            if ($this->style) {
                $html .= 'style="' . $displayStyle . $this->style . '" ';
            }
            if ($displayStyle != '') {
                $html .= 'style="' . $displayStyle . '" ';
            }
            $html .= $this->NSRequiredValue() . ' ';
            $html .= $this->NSDefaultValue() . ' ';
            $html .= $this->NSGridLabel() . ' ';
            $html .= $rowId == '' ? $this->NSFieldType() : $this->NSGridType() . ' ';
            $html .= $this->NSDependentFields( true ) . ' ';
            $html = $html . (($this->optgroup == 1)? "pm:optgroup=\"1\" " : null);
            $html = $html . ">";

            $findValue = '';
            $firstValue = '';
            $count = 0;
            $swOption = 0;

            $htmlOptGroup = null;
            $swOptGroupPrev = 0;
            $swAppend = 0;

            foreach ($this->option as $optValue => $optName) {
                settype($optValue, "string");

                if ($this->optgroup == 1 && preg_match("/^optgroup\d+$/", $optValue)) {
                    if ($swOptGroupPrev == 1 && $swAppend == 1) {
                        $html = $html . "</optgroup>";
                    }

                    $htmlOptGroup = "<optgroup label=\"$optName\">";
                    $swOptGroupPrev = 1;
                    $swAppend = 0;
                } else {
                    $html = $html . $htmlOptGroup . "<option value=\"$optValue\"" . (($optValue == $value)? " selected=\"selected\"" : null) . ">$optName</option>";
                    $htmlOptGroup = null;
                    $swAppend = 1;

                    if ($optValue === $value) {
                        $findValue = $optValue;
                        $displayLabel = $optName;
                    }

                    if ($firstValue == "") {
                        $firstValue = $optValue;
                    }

                    $count = $count + 1;
                    $swOption = 1;
                }
            }

            foreach ($this->sqlOption as $optValue => $optName) {
                settype($optValue, "string");

                if ($this->optgroup == 1 && preg_match("/^optgroup\d+$/", $optValue)) {
                    if ($swOptGroupPrev == 1 && $swAppend == 1) {
                        $html = $html . "</optgroup>";
                    }

                    $htmlOptGroup = "<optgroup label=\"$optName\">";
                    $swOptGroupPrev = 1;
                    $swAppend = 0;
                } else {
                    $html = $html . $htmlOptGroup . "<option value=\"$optValue\"" . (($optValue == $value)? " selected=\"selected\"" : null) . ">$optName</option>";
                    $htmlOptGroup = null;
                    $swAppend = 1;

                    if ($optValue === $value) {
                        $findValue = $optValue;
                        $displayLabel = $optName;
                    }

                    if ($firstValue == "") {
                        $firstValue = $optValue;
                    }

                    $swOption = 1;
                }
            }

            if ($swOption == 1) {
                if ($swOptGroupPrev == 1 && $swAppend == 1) {
                    $html = $html . "</optgroup>";
                }
            } else {
                $html = $html . "<option value=\"\"></option>";
            }

            $html .= '</select>';
            if ($readOnlyField != '') {
                $html .= '<input type="hidden" ';
                $html .= 'id="form' . $rowId . '[' . $this->name . ']" ';
                $html .= 'name="form' . $rowId . '[' . $this->name . ']" ';
                $html .= 'value="' . (($findValue != '') ? $findValue : $firstValue) . '" />';
            }

            $this->selectedValue = ($findValue != "")? $findValue : ($count == 0)? $firstValue : "";
        } else {
            //Render Field showing only value;
            foreach ($this->option as $optValue => $optName) {
                if ($optValue == $value) {
                    $html = $optName;
                }
            }
            foreach ($this->sqlOption as $optValue => $optName) {
                if ($optValue == $value) {
                    $html = $optName;
                }
            }
        }

        if ($this->gridFieldType == '') {
            $html .= $this->renderHint();
        }
        if ($displayStyle != '') {
            $html = $displayLabel . $html;
        }
        return $html;
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @return string
     */
    public function renderGrid ($values = array(), $owner = null, $onlyValue = false, $therow = -1)
    {
        $this->gridFieldType = 'dropdown';
        $result = array ();
        $r = 1;
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }

        foreach ($values as $v) {
            $result[] = $this->render( $v, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue, $r, $therow );
            $r ++;
        }
        return $result;
    }
}

/**
 * Class XmlForm_Field_Listbox
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Listbox extends XmlForm_Field
{
    public $defaultValue = '';
    public $required = false;
    public $option = array ();
    public $sqlConnection = 0;
    public $size = 4;
    public $width = '';
    public $sql = '';
    public $sqlOption = array ();

    public function validateValue ($value, $owner)
    {
        $this->executeSQL( $owner );
        return true; // isset($value) && ( array_key_exists( $value , $this->options ) );
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        $this->executeSQL( $owner );
        if (! is_array( $value )) {
            $value = explode( '|', $value );
        }

        $arrayAux = array();

        foreach ($value as $index2 => $value2) {
            $arrayAux[] = $value2 . "";
        }

        $value = $arrayAux;

        if ($this->mode === 'edit') {
            $itemWidth = '';
            if ($this->width != '') {
                $itemWidth = 'style="width:' . $this->width . '"';
            }
            $html = '<select multiple="multiple" id="form[' . $this->name . ']" ' . $this->NSFieldType() . ' name="form[' . $this->name . '][]" size="' . $this->size . '" ' . $itemWidth . '  ' . $this->NSFieldType() . '>';
            foreach ($this->option as $optionName => $option) {
                $html .= "<option value=\"" . $optionName . "\" " . ((in_array( $optionName . "", $value )) ? "selected=\"selected\"" : "") . ">" . $option . "</option>";
            }
            foreach ($this->sqlOption as $optionName => $option) {
                $html .= "<option value=\"" . $optionName . "\" " . ((in_array( $optionName . "", $value )) ? "selected=\"selected\" " : "") . ">" . $option . "</option>";
            }
            $html .= '</select>';

            $html .= $this->renderHint();
            return $html;
        } elseif ($this->mode === 'view') {
            $html = '<select multiple="multiple" id="form[' . $this->name . ']" name="form[' . $this->name . '][]" size="' . $this->size . '" ' . $this->NSFieldType() . ' style="background: none;" disabled="disabled">';
            foreach ($this->option as $optionName => $option) {
                $html .= "<option value=\"" . $optionName . "\" " . ((in_array( $optionName . "", $value )) ? "class=\"module_ListBoxView\" selected=\"selected\"" : "") . ">" . $option . "</option>";
            }
            foreach ($this->sqlOption as $optionName => $option) {
                $html .= "<option value=\"" . $optionName . "\" " . ((in_array( $optionName . "", $value )) ? "class=\"module_ListBoxView\" selected=\"selected\"" : "") . ">" . $option . "</option>";
            }
            $html .= '</select>';
            foreach ($this->option as $optionName => $option) {
                $html .= "<input type=\"hidden\" id=\"form[" . $this->name . "]\" name=\"form[" . $this->name . "][]\" value=\"" . ((in_array( $optionName . "", $value )) ? $optionName : "__NULL__") . "\">";
            }
            foreach ($this->sqlOption as $optionName => $option) {
                $html .= "<input type=\"hidden\" id=\"form[" . $this->name . "]\" name=\"form[" . $this->name . "][]\" value=\"" . ((in_array( $optionName . "", $value )) ? $optionName : "__NULL__") . "\">";
            }
            return $html;
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }

    /**
     * Render the field in a grid
     *
     * @param $value
     * @param $owner
     * @return <Array> result
     */
    public function renderGrid ($value = null, $owner = null)
    {
        return $this->render( $value, $owner );
    }
}

/**
 * Class XmlForm_Field_RadioGroup
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_RadioGroup extends XmlForm_Field
{
    public $defaultValue = '';
    public $required = false;
    public $option = array ();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    public $viewAlign = 'vertical';
    public $linkType;

    /**
     * validate the execution of a query
     *
     * @param $value
     * @param $owner
     * @return $value
     */
    public function validateValue ($value, $owner)
    {
        $this->executeSQL( $owner );
        return isset( $value ) && (array_key_exists( $value, $this->options ));
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        $this->executeSQL( $owner );
        if ($this->mode === 'edit') {
            $html = '';
            $i = 0;
            foreach ($this->options as $optionName => $option) {
                if (isset( $this->linkType ) && ($this->linkType == 1 || $this->linkType == "1")) {
                    $html .= '<input id="form[' . $this->name . '][' . $optionName . ']" ' . $this->NSFieldType() . ' name="form[' . $this->name . ']" type="radio" value="' . $optionName . '" ' . (($optionName == $value) ? ' checked' : '') . '><a href="#" onclick="executeEvent(\'form[' . $this->name . '][' . $optionName . ']\', \'click\'); return false;">' . $option . '</a></input>';
                } else {
                    $html .= '<input id="form[' . $this->name . '][' . $optionName . ']" ' . $this->NSFieldType() . ' name="form[' . $this->name . ']" type="radio" value="' . $optionName . '" ' . (($optionName == $value) ? ' checked' : '') . '><label for="form[' . $this->name . '][' . $optionName . ']">' . $option . '</label></input>';
                }
                if (++ $i == count( $this->options )) {
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->renderHint();
                }

                if ($this->viewAlign == 'horizontal') {
                    $html .= '&nbsp;';
                } else {
                    $html .= '<br>';
                }
            }
            return $html;
        } elseif ($this->mode === 'view') {
            $html = '';
            foreach ($this->options as $optionName => $option) {
                $html .= '<input class="module_app_input___gray" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" type=\'radio\' value="' . $optionName . '" ' . (($optionName == $value) ? 'checked' : '') . ' disabled><span class="FormCheck"><label for="form[' . $this->name . '][' . $optionName . ']">' . $option . '</label></span></input><br>';
                if ($optionName == $value) {
                    $html .= '<input type="hidden"  id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" value="' . (($optionName == $value) ? $optionName : '') . '">';
                }
            }
            return $html;
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }
    }
}

/*DEPRECATED*/
/**
 *
 * @package gulliver.system
 *
 */
class XmlForm_Field_RadioGroupView extends XmlForm_Field
{
    public $defaultValue = '';
    public $required = false;
    public $option = array ();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        $this->executeSQL( $owner );
        $html = '';
        foreach ($this->option as $optionName => $option) {
            $html .= '<input type=\'radio\'`disabled/><span class="FormCheck">' . $option . '</span><br>';
        }
        return $html;
    }
}

/**
 * Class XmlForm_Field_CheckGroup
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_CheckGroup extends XmlForm_Field
{
    public $required = false;
    public $option = array ();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();
    /*function validateValue( $value , $owner )
    {
    	$this->executeSQL( $owner );
    	return isset($value) && ( array_key_exists( $value , $this->options ) );
    }*/
    /**
    * Function render
    *
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string value
    * @param string owner
    * @return string
    */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        }
        $this->executeSQL( $owner );
        if (! is_array( $value )) {
            $value = explode( '|', $value );
        }

        $arrayAux = array();

        foreach ($value as $index2 => $value2) {
            $arrayAux[] = $value2 . "";
        }

        $value = $arrayAux;

        if ($this->mode === 'edit') {
            $i = 0;
            $html = '';
            foreach ($this->options as $optionName => $option) {
                $html .= "<input type=\"checkbox\" id=\"form[" . $this->name . "][" . $optionName . "]\" " . $this->NSFieldType() . " name=\"form[" . $this->name . "][]\"  value=\"" . $optionName . "\"" . (in_array( $optionName . "", $value ) ? "checked = \"checked\" " : "") . "><span class=\"FormCheck\"><label for=\"form[" . $this->name . "][" . $optionName . "]\">" . $option . "</label></span></input>";

                if (++ $i == count( $this->options )) {
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->renderHint();
                }
                $html .= '<br>';
            } //fin for
            return $html;
        } elseif ($this->mode === 'view') {
            $html = '';
            foreach ($this->options as $optionName => $option) {
                $html .= "<input class=\"FormCheck\" type=\"checkbox\" id=\"form[" . $this->name . "][" . $optionName . "]\" value=\"" . $optionName . "\"" . (in_array( $optionName . "", $value ) ? " checked=\"checked\" " : "") . " disabled=\"disabled\"><span class=\"FormCheck\"><label for=\"form[" . $this->name . "][" . $optionName . "]\">" . $option . "</label></span></input><br />";
                $html .= "<input type=\"hidden\" name=\"form[" . $this->name . "][]\"  value=\"" . ((in_array( $optionName . "", $value )) ? $optionName : "__NULL__") . "\">";
            }
            return $html;
        } else {
            return $this->htmlentities( $value, ENT_COMPAT, 'utf-8' );
        }

    }
}

/* TODO: DEPRECATED */
/**
 *
 * @package gulliver.system
 *
 */
class XmlForm_Field_CheckGroupView extends XmlForm_Field
{
    public $option = array ();
    public $sqlConnection = 0;
    public $sql = '';
    public $sqlOption = array ();

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null)
    {
        $html = '';
        foreach ($this->option as $optionName => $option) {
            $html .= '<input type=\'checkbox\' disabled/><span class="FormCheck">' . $option . '</span><br>';
        }
        return $html;
    }
}

/**
 * Class XmlForm_Field_Grid
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Grid extends XmlForm_Field
{
    public $xmlGrid = '';
    public $initRows = 1;
    public $group = 0;
    public $addRow = "1";
    public $deleteRow = "1";
    public $editRow = "0";
    public $sql = '';
    //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
    public $fillType = 0;
    public $fields = array ();
    public $scriptURL;
    public $id = '';

    /**
     * Function XmlForm_Field_Grid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string xmlnode
     * @param string language
     * @param string home
     * @return string
     */
    public function XmlForm_Field_Grid ($xmlnode, $language, $home)
    {
        parent::XmlForm_Field( $xmlnode, $language );
        $this->parseFile( $home, $language );
    }

    /**
     * Function parseFile
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string home
     * @param string language
     * @return string
     */
    public function parseFile ($home, $language)
    {
        if (file_exists( $home . $this->xmlGrid . '.xml' )) {
            $this->xmlform = new XmlForm();
            $this->xmlform->home = $home;
            $this->xmlform->parseFile( $this->xmlGrid . '.xml', $language, false );
            $this->fields = $this->xmlform->fields;
            $this->scriptURL = $this->xmlform->scriptURL;
            $this->id = $this->xmlform->id;
            $this->modeGrid = $this->xmlform->mode;
            unset( $this->xmlform );
        }
    }

    /**
     * Render the field in a dynaform
     *
     * @param $value
     * @param $owner
     * @return <Template Object>
     */

    public function render ($values, $owner = null)
    {
        $emptyRow = $this->setScrollStyle( $owner );
        return $this->renderGrid( $emptyRow, $owner );

    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @return string
     */
    public function renderGrid ($values, $owner = null, $therow = -1)
    {
        $this->id = $this->owner->id . $this->name;
        $using_template = 'grid';

        if (isset( $this->mode )) {
            $ownerMode = $this->mode;
        } else {
            $ownerMode = '';
        }

        if ($ownerMode != '') {
            $this->mode = $ownerMode;
        }

        if ($this->mode == '') {
            $this->mode = 'edit';
        }

        $this->modeForGrid = $this->mode;

        if ($this->mode == 'view') {
            $using_template = 'grid_view';
        }

        $tpl = new xmlformTemplate($this, PATH_CORE . "templates" . PATH_SEP . "$using_template.html");

        if (! isset( $values ) || ! is_array( $values ) || sizeof( $values ) == 0) {
            $values = array_keys( $this->fields );
        }

        if ($therow != - 1) {
            //Check if values arrary is complete to can flip.
            $xValues = array ();
            if (isset( $values[$therow] )) {
                $aRow = $values[$therow];
            } else {
                $aRow = array ();
            }
            for ($c = 1; $c <= $therow; $c ++) {
                if ($c == $therow) {
                    $xValues[$therow] = $aRow;
                } else {
                    if (is_array( $aRow )) {
                        foreach ($aRow as $key => $value) {
                            $xValues[$c][$key] = "";
                        }
                    } else {
                        //
                    }
                }
            }
            $values = $xValues;
        }

        $aValuekeys = array_keys( $values );

        if (count( $aValuekeys ) > 0 && (int) $aValuekeys[0] == 1) {
            $values = $this->flipValues( $values );
        }

        //if ($therow == 1)g::pr($values);
        $this->rows = count( reset( $values ) );

        //Fields required in grid (view in sql attribute)
        $arrayField = array();
        $arrayFieldRequired = array();

        foreach ($this->fields as $index1 => $value1) {
            $field = $value1;

            $arrayField[] = $field->name;
            $arrayFieldRequired[] = "SYS_GRID_AGGREGATE_" . $this->name . "_" . $field->name;

            preg_match_all("/@[@%#\?\$\=]([A-Za-z_]\w*)/", $field->sql, $arrayMatch, PREG_SET_ORDER);

            foreach ($arrayMatch as $value2) {
                $arrayFieldRequired[] = $value2[1];
            }
        }

        $arrayFieldRequired = array_unique($arrayFieldRequired);
        $arrayFieldRequired = array_diff($arrayFieldRequired, $arrayField);

        //Fields Grid only required fields of the grid, no all fields of dynaform main
        if (isset($owner->values) && count($arrayFieldRequired) > 0) {
            foreach ($owner->values as $key => $value) {
                if (in_array($key, $arrayFieldRequired) && !isset($values[$key])) {
                    $values[$key] = array();
                    //for($r=0; $r < $this->rows ; $r++ ) {
                    $values[$key] = $value;
                    //}
                }
            }
        }

        foreach ($this->fields as $k => $v) {
            if (isset( $values['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k] )) {
                $this->fields[$k]->aggregate = $values['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k];
            } else {
                $this->fields[$k]->aggregate = '0';
            }
        }

        $this->values = $values;

        $this->NewLabel = G::LoadTranslation( 'ID_NEW' );
        $this->DeleteLabel = G::LoadTranslation( 'ID_DELETE' );

        $tpl->template = $tpl->printTemplate( $this );
        //In the header
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile( $this->scriptURL );
        $oHeadPublisher->addScriptCode( $tpl->printJavaScript( $this ) );
        return $tpl->printObject( $this, $therow );
    }

    /**
     * Change the columns for rows and rows to columns
     *
     * @param <array> $arrayData
     * @return <array>
     */
    public function flipValues ($arrayData)
    {
        $flipped = array ();

        foreach ($arrayData as $rowIndex => $rowValue) {
            if (is_array( $rowValue )) {
                foreach ($rowValue as $colIndex => $colValue) {
                    if (! isset( $flipped[$colIndex] ) || ! is_array( $flipped[$colIndex] )) {
                        $flipped[$colIndex] = array ();
                    }

                    $flipped[$colIndex][$rowIndex] = $colValue;
                }
            }
        }

        return $flipped;
    }

    public function setScrollStyle($owner) {
        $arrayKeys = array_keys( $this->fields );
        $emptyRow = array ();
        $fieldsSize = 0;
        foreach ($arrayKeys as $key) {
            if (isset( $this->fields[$key]->defaultValue )) {
                $emptyValue = $this->fields[$key]->defaultValue;
            } else {
                $emptyValue = '';
            }
            if (isset( $this->fields[$key]->size )) {
                $size = $this->fields[$key]->size;
            }
            if (! isset( $size )) {
                $size = 15;
            }
            $fieldsSize += $size;
            $emptyRow[$key] = array ($emptyValue);
        }

        if (isset( $owner->adjustgridswidth ) && $owner->adjustgridswidth == '1') {
            // 400w -> 34s to Firefox
            // 400w -> 43s to Chrome
            $baseWidth = 400;
            $minusWidth = 30;
            if (eregi( 'chrome', $_SERVER['HTTP_USER_AGENT'] )) {
                $baseSize = 43;
            } else {
                if (strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) !== false) {
                    $minusWidth = 20;
                }
                $baseSize = 34;
            }

            $baseWidth = 400;
            $formWidth = (int) $owner->width;
            $maxSize = (($formWidth * $baseSize) / $baseWidth);

            if ($fieldsSize > $maxSize) {
                $this->scrollStyle = 'height:100%; overflow-x: scroll; width:';
                $this->scrollStyle .= $formWidth - $minusWidth . ';';
            }
        }

        return $emptyRow;
    }
}

/**
 * Class XmlForm_Field_JavaScript
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_JavaScript extends XmlForm_Field
{
    public $code = '';
    public $replaceTags = true;

    /**
    * Function XmlForm_Field_JavaScript
    *
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string xmlNode
    * @param string lang
    * @param string home
    * @return string
    */
    public function XmlForm_Field_JavaScript ($xmlNode, $lang = 'en', $home = '')
    {
        //Loads any attribute that were defined in the xmlNode
        //except name and label.
        $myAttributes = get_class_vars( get_class( $this ) );
        foreach ($myAttributes as $k => $v) {
            $myAttributes[$k] = strtoupper( $k );
        }
        foreach ($xmlNode->attributes as $k => $v) {
            $key = array_search( strtoupper( $k ), $myAttributes );
            if ($key) {
                eval( '$this->' . $key . '=$v;' );
            }
        }
        //Loads the main attributes
        $this->name = $xmlNode->name;
        $this->type = strtolower( $xmlNode->attributes['type'] );
        //$data: Includes labels and options.
        $this->code = $xmlNode->value;
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        $code = ($this->replaceTags) ? G::replaceDataField( $this->code, $owner->values ) : $this->code;
        return $code;
    }

    /**
     * Function renderGrid
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string value
     * @param string owner
     * @return string
     */
    public function renderGrid ($value, $owner)
    {
        return array ('');
    }

    /**
     * A javascript node has no value
     *
     * @param $value
     * @return false
     */
    public function validateValue ($value)
    {
        return false;
    }
}

/**
 *
 * @author Erik amaru Ortiz <erik@colosa.com>
 * Comment Working for after and before date attributes
 * @package gulliver.system
 */
class XmlForm_Field_Date extends XmlForm_Field_SimpleText
{
    public $required = false;
    public $readOnly = false;

    public $startDate = '';
    public $endDate = '';
    /*
    * for dinamically dates,   beforeDate << currentDate << afterDate
    * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
    * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
    * startDate and endDate have priority over beforeDate and AfterDate
    */
    public $afterDate = '';
    public $beforeDate = '';
    public $defaultValue = null;
    public $format = '%Y-%m-%d';

    public $mask = '%Y-%m-%d';
    public $dependentFields = '';
    public $editable;
    public $onchange;
    public $renderMode = '';
    public $gridFieldType = '';

    /*
    * Verify the format of a date
    * @param <Date> $date
    * @return <Boolean> true/false
    */
    public function verifyDateFormat ($date)
    {
        $dateTime = explode( " ", $date ); //To accept the Hour part
        $aux = explode( '-', $dateTime[0] );
        if (count( $aux ) != 3) {
            return false;
        }
        if (! (is_numeric( $aux[0] ) && is_numeric( $aux[1] ) && is_numeric( $aux[2] ))) {
            return false;
        }
        if ($aux[0] < 1900 || $aux[0] > 2100) {
            return false;
        }
        return true;
    }

    /**
     * Check if a date had a valid format before
     *
     * @param <Date> $date
     * @return <Boolean> true/false
     */
    public function isvalidBeforeFormat ($date)
    {
        $part1 = substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );
        if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y') {
            return false;
        }
        if (! is_numeric( $part1 )) {
            return false;
        }
        return true;
    }

    /**
     * Calculations in Date
     *
     * @param <Date> $date
     * @param $sign
     * @return <Date>
     */
    public function calculateBeforeFormat ($date, $sign)
    {
        $part1 = $sign * substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );
        switch ($part2) {
            case 'd':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + $part1, date( 'Y' ) ) );
                break;
            case 'm':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ) + $part1, date( 'd' ), date( 'Y' ) ) );
                break;
            case 'y':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + $part1 ) );
                break;
        }
        return $res;
    }

    /**
     * render the field in a dynaform
     *
     * @param $value
     * @param $owner
     * @return renderized widget
     */
    public function render ($value = null, $owner = null)
    {
        $this->renderMode = $this->mode;
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        } else {
            $value = G::replaceDataField( $value, $owner->values );
        }
        //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
        $id = "form[$this->name]";

        return $this->__draw_widget( $id, $value, $owner );
    }

    /**
     * render the field in a grid
     *
     * @param $values
     * @param $owner
     * @param $onlyValue
     * @return Array $result
     */
    public function renderGrid ($values = null, $owner = null, $onlyValue = false)
    {
        $this->gridFieldType = 'date';
        $result = array ();
        $r = 1;
        /*    if( ! isset($owner->modeGrid)) $owner->modeGrid = '';
        $this->mode = $this->modeForGrid;*/
        if ($owner->mode != 'view') {
            $this->renderMode = $this->modeForGrid;
        }
        foreach ($values as $v) {
            $v = G::replaceDataField( $v, $owner->values );
            if (! $onlyValue) {
                if ($this->mode === 'view' || (isset( $owner->modeGrid ) && $owner->modeGrid === 'view')) {
                    if ($this->required) {
                        $isRequired = '1';
                    } else {
                        $isRequired = '0';
                    }
                    $mask = str_replace( "%", "", $this->mask );
                    if (trim($v) !== "") {
                        $v = date( masktophp($mask, $v) );
                    }
                    $html = '<input ' . $this->NSRequiredValue() .' class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '" pm:required="' . $isRequired . '" style="display:none;' . htmlentities( $this->style, ENT_COMPAT, 'utf-8' ) . '" />' . htmlentities( $v, ENT_COMPAT, 'utf-8' );
                } else {
                    $id = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
                    $html = $this->__draw_widget( $id, $v, $owner, true );
                }
            } else {
                $html = $v;
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }

    /**
     * Returns the html code to draw the widget
     *
     * @param $pID
     * @param $value
     * @param $owner
     * @return <String>
     */
    public function __draw_widget ($pID, $value, $owner = '', $inGrid = false)
    {
        $startDate = G::replaceDataField( $this->startDate, $owner->values );
        $endDate = G::replaceDataField( $this->endDate, $owner->values );
        $beforeDate = G::replaceDataField( $this->beforeDate, $owner->values );
        $afterDate = G::replaceDataField( $this->afterDate, $owner->values );
        $defaultValue = $this->defaultValue;
        if ($startDate != '') {
            if (! $this->verifyDateFormat( $startDate )) {
                $startDate = '';
            }
        }

        if (isset( $beforeDate ) && $beforeDate != '') {
            if ($this->isvalidBeforeFormat( $beforeDate )) {
                $startDate = $this->calculateBeforeFormat( $beforeDate, 1 );
            }
        }

        if ($startDate == '' && isset( $this->size ) && is_numeric( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
            $startDate = $this->size . '-01-01';
        }

        if ($endDate != '') {
            if (! $this->verifyDateFormat( $endDate )) {
                $endDate = '';
            }
        }

        if (isset( $afterDate ) && $afterDate != '') {
            if ($this->isvalidBeforeFormat( $afterDate )) {
                $endDate = $this->calculateBeforeFormat( $afterDate, + 1 );
            }
        }

        if (isset( $this->maxlength ) && is_numeric( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
            $endDate = $this->maxlength . '-01-01';
        }

        if ($endDate == '') {
            // the default is the current date + 2 years
            $endDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) );
        }

        //validating the mask, if it is not set,
        if (isset( $this->mask ) && $this->mask != '') {
            $mask = $this->mask;
        } else {
            $mask = '%Y-%m-%d'; //set default
        }

        $valueDemo = masktophp($mask, "today");

        if ($this->defaultValue != "") {
            $defaultValue = masktophp( $mask, $defaultValue);
        }

        if (strpos( $mask, '%' ) === false) {
            if (strpos( $mask, '-' ) !== false) {
                $separator = '-';
            }
            if (strpos( $mask, '/' ) !== false) {
                $separator = '/';
            }
            if (strpos( $mask, '.' ) !== false) {
                $separator = '.';
            }

            $maskparts = explode( $separator, $mask );
            $mask = '';
            foreach ($maskparts as $part) {
                if ($mask != '') {
                    $mask .= $separator;
                }
                if ($part == 'yyyy') {
                    $part = 'Y';
                }
                if ($part == 'dd') {
                    $part = 'd';
                }
                if ($part == 'mm') {
                    $part = 'm';
                }
                if ($part == 'yy') {
                    $part = 'y';
                }
                $mask .= '%' . $part;
            }
        }

        $tmp = str_replace( "%", "", $mask );
        if (trim( $value ) == '' or $value == null) {
            $value = ''; //date ($tmp);
        } else {
            if ($value != "") {
                $value = masktophp( $mask, $value);
            }
        }

        //onchange
        if (isset( $this->onchange ) && $this->onchange != '') {
            $onchange = 'onchange="' . $this->onchange . '"';
        } else {
            $onchange = '';
        }

        if ($this->renderMode == 'edit') {
            $maskleng = strlen( $mask );
            $hour = '%H';
            $min = '%M';
            $sec = '%S';
            $sizehour = strpos( $mask, $hour );
            $sizemin = strpos( $mask, $min );
            $sizesec = strpos( $mask, $sec );
            $Time = 'false';

            if (($sizehour !== false) && ($sizemin !== false) && ($sizesec !== false)) {
                $Time = "true";
            }

            //$sizeend = strlen($valueDemo) + 3;
            $sizeend = $this->size;

            if ($this->required) {
                $isRequired = '1';
            } else {
                $isRequired = '0';
            }

            if ($this->editable != "0") {
                $html = '<input pm:required="' . $isRequired . '" id="' . $pID . '" name="' . $pID . '" pm:mask="' . $mask . '" pm:start="' . $startDate . '" pm:end="' . $endDate . '" pm:time="' . $Time . '" ' . $onchange . ' class="module_app_input___gray" size="' . $sizeend . '" value="' . $value . '" pm:defaultvalue="' . $defaultValue . '"  />' . '<a onclick="removeValue(\'' . $pID . '\'); return false;" style="position:relative;left:-17px;top:2px;" >' . '  <img src="/images/icons_silk/calendar_x_button.png" />' . '</a>' . '<a id="' . $pID . '[btn]" style="position: relative; top: 2px; left: -16px;" >' . '  <img src="/images/pmdateicon.png" border="0" width="12" height="14" />' . '</a>' . '<script>datePicker4("", \'' . $pID . '\', \'' . $mask . '\', \'' . $startDate . '\', \'' . $endDate . '\',' . $Time . ')</script>';
            } else {
                $html = '<input ' . $this->NSFieldType() . 'pm:required="' . $isRequired . '" id="' . $pID . '" name="' . $pID . '" pm:mask="' . $mask . '" pm:start="' . $startDate . '" pm:end="' . $endDate . '" pm:time="' . $Time . '" ' . $onchange . ' class="module_app_input___gray" size="' . $sizeend . '" value="' . $value . '" pm:defaultvalue="' . $defaultValue . '" readonly="readonly"  />' . '<a onclick="removeValue(\'' . $pID . '\'); return false;" style="position:relative;left:-17px;top:2px;" >' . '  <img src="/images/icons_silk/calendar_x_button.png" />' . '</a>' . '<a id="' . $pID . '[btn]" style="position: relative; top: 2px; left: -16px;" >' . '  <img src="/images/pmdateicon.png" border="0" width="12" height="14" />' . '</a>' . '<script>datePicker4("", \'' . $pID . '\', \'' . $mask . '\', \'' . $startDate . '\', \'' . $endDate . '\',' . $Time . ')</script>';
            }
        } else {
            $html = "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>" . '<input type="hidden" id="' . $pID . '" name="' . $pID . '" pm:mask="' . $mask . '" pm:start="' . $startDate . '"' . 'pm:end="' . $endDate . '"  ' . $onchange . ' class="module_app_input___gray" value="' . $value . '"/>';
        }
        /**
         * * Commented because seems is not working well *
         * $idIsoDate = substr($pID,0,strlen($pID)-1).'_isodate]';
         * $amask = explode('-',str_replace('%','',$mask));
         * $axDate = explode('-',$value);
         * $valisoDate = '';
         *
         * if ( sizeof($amask) == sizeof($axDate) ) {
         * $aisoDate = array_combine($amask, $axDate);
         * if ( isset($aisoDate['Y']) && isset($aisoDate['m']) && isset($aisoDate['d']) )
         * $valisoDate = $aisoDate['Y'].'-'.$aisoDate['m'].'-'.$aisoDate['d'];
         * }
         *
         * $html .= '<input type="hidden" id="'.$idIsoDate.'" name="'.$idIsoDate.'" value="'.$valisoDate.'"/>';
         * *
         */
        if ($this->gridFieldType == '') {
            $html .= $this->renderHint();
        }
        return $html;
    }

    public function maskDateValue ($value, $field)
    {
        $value = trim($value);
        $mask = $field->mask;
        if ($value == '' || $mask == '') {
            return $value;
        }
        if (strpos( $mask, '%' ) === false) {
            if (strpos( $mask, '-' ) !== false) {
                $separator = '-';
            }
            if (strpos( $mask, '/' ) !== false) {
                $separator = '/';
            }
            if (strpos( $mask, '.' ) !== false) {
                $separator = '.';
            }

            $maskparts = explode( $separator, $mask );
            $mask = '';
            foreach ($maskparts as $part) {
                if ($mask != '') {
                    $mask .= $separator;
                }
                if ($part == 'yyyy') {
                    $part = 'Y';
                }
                if ($part == 'dd') {
                    $part = 'd';
                }
                if ($part == 'mm') {
                    $part = 'm';
                }
                if ($part == 'yy') {
                    $part = 'y';
                }
                $mask .= '%' . $part;
            }
        }

        $withHours = (strpos($mask, '%H') !== false || strpos($mask, '%M') !== false || strpos($mask, '%S') !== false);

        $tmp = str_replace( "%", "", $mask );
        return $this->date_create_from_format($tmp, $value, $withHours);
    }

    function date_create_from_format( $dformat, $dvalue, $withHours = false )
    {
        $schedule = $dvalue;
        $schedule_format = str_replace(array('Y','m','d','H','M','S'),array('%Y','%m','%d','%H','%M','%S') ,$dformat);
        $ugly = strptime($schedule, $schedule_format);
        $ymd = sprintf(
            '%04d-%02d-%02d %02d:%02d:%02d',
            $ugly['tm_year'] + 1900,
            $ugly['tm_mon'] + 1,
            $ugly['tm_mday'],
            $ugly['tm_hour'],
            $ugly['tm_min'],
            $ugly['tm_sec']
        );
        try {
            $new_schedule = new DateTime($ymd);
        } catch (Exception $error) {
            $new_schedule = new DateTime();
        }
        return $new_schedule->format('Y-m-d' . ($withHours ? ' H:i:s' : ''));
    }
}

/**
 * Calendar Widget with Javascript Routines
 *
 * @author Erik amaru Ortiz <aortiz@gmail.com, erik@colosa.com>
 * @package gulliver.system
 */
class XmlForm_Field_Date5 extends XmlForm_Field_SimpleText
{
    public $required = false;
    public $readOnly = false;

    public $startDate = '';
    public $endDate = '';
    /*
    * for dinamically dates,   beforeDate << currentDate << afterDate
    * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
    * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
    * startDate and endDate have priority over beforeDate and AfterDate
    */
    public $afterDate = '';
    public $beforeDate = '';
    public $defaultValue = null;
    public $format = 'Y-m-d';

    public $mask = 'Y-m-d';
    public $dependentFields = '';

    public $showtime;
    public $onchange;
    public $editable;
    public $relativeDates;

    //var $hint;


    /**
     * Verify the format of a date
     *
     * @param <Date> $date
     * @return <Boolean> true/false
     */
    public function verifyDateFormat ($date, $mask = '')
    {
        $dateTime = explode( " ", $date ); //To accept the Hour part
        $aDate = explode( '-', str_replace( "/", "-", $dateTime[0] ) );
        $bResult = true;

        foreach ($aDate as $sDate) {
            if (! is_numeric( $sDate )) {
                $bResult = false;
                break;
            }
        }

        if ($mask != '') {
            $aDate = $this->getSplitDate( $dateTime[0], $mask );
            $aDate[0] = ($aDate[0] == '') ? date( 'Y' ) : $aDate[0];
            $aDate[1] = ($aDate[1] == '') ? date( 'm' ) : $aDate[1];
            $aDate[2] = ($aDate[2] == '') ? date( 'd' ) : $aDate[2];

            return true;
            if (checkdate( $aDate[1], $aDate[2], $aDate[0] )) {
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a date had a valid format before
     *
     * @param <Date> $date
     * @return <Boolean> true/false
     */
    public function isvalidBeforeFormat ($date)
    {
        $part1 = substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );
        if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y') {
            return false;
        }
        if (! is_numeric( $part1 )) {
            return false;
        }
        return true;
    }

    public function calculateBeforeFormat ($date, $sign)
    {
        $part1 = $sign * substr( $date, 0, strlen( $date ) - 1 );
        $part2 = substr( $date, strlen( $date ) - 1 );

        #TODO
        # neyek
        /*
        * Because mktime has the restriccion for:
        * The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069 and 70-100 to 1970-2000.
        * On systems where time_t is a 32bit signed integer, as most common today, the valid range for year  is somewhere
        * between 1901 and 2038. However, before PHP 5.1.0 this range was limited from 1970 to 2038 on some systems (e.g. Windows).
        */
        # improving required

        switch ($part2) {
            case 'd':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + $part1, date( 'Y' ) ) );
                break;
            case 'm':
                $res = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ) + $part1, date( 'd' ) - 1, date( 'Y' ) ) );
                break;
            case 'y':
                //$res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1) );
                //hook
                $res = (intVal( date( 'Y' ) ) + $part1) . '-' . date( 'm' ) . '-' . date( 'd' );
                break;
        }

        return $res;
    }

    /**
     * render the field in a dynaform
     *
     * @param $value
     * @param $owner
     * @return renderized widget
     */
    public function render ($value = null, $owner = null)
    {
        if (($this->pmconnection != '') && ($this->pmfield != '') && $value == null) {
            $value = $this->getPMTableValue( $owner );
        } else {
            $value = G::replaceDataField( $value, $owner->values );
        }
        //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
        $id = "form[$this->name]";
        return $this->__draw_widget( $id, $value, $owner );
    }

    /**
     * render the field in a grid
     *
     * @param $values
     * @param $owner
     * @param $onlyValue
     * @return Array $result
     */
    public function renderGrid ($values = null, $owner = null, $onlyValue = false)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            $v = ($v != '') ? G::replaceDataField( $v, $owner->values ) : $this->defaultValue;
            if (! $onlyValue) {
                $id = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
                $html = $this->__draw_widget( $id, $v, $owner );
            } else {
                $html = $v;
            }
            $result[] = $html;
            $r ++;
        }
        return $result;
    }

    /**
     * Returns the html code to draw the widget
     *
     * @param $pID
     * @param $value
     * @param $owner
     * @return <String>
     */
    public function __draw_widget ($pID, $value, $owner = '')
    {

        /*for deprecated mask definitions...*/

        #first deprecated simple (yyyy-mm-dd) and personalizes combinations
        $this->mask = str_replace( 'yyyy', 'Y', $this->mask );
        $this->mask = str_replace( 'yy', 'y', $this->mask );
        $this->mask = str_replace( 'mm', 'm', $this->mask );
        $this->mask = str_replace( 'dd', 'd', $this->mask );

        #second deprecated (%Y-%m-%d) and other combinations
        $this->mask = str_replace( '%', '', $this->mask );

        if (isset( $this->mask ) && $this->mask != '') {
            $mask = $this->mask;
        } else {
            #Default mask
            $mask = 'Y-m-d';
        }

        // Note added by Gustavo Cruz
        // set the variable isRequired if the needs to be validated
        //
        if ($this->required) {
            $isRequired = '1';
        } else {
            $isRequired = '0';
        }

        $startDate = G::replaceDataField( $this->startDate, $owner->values );
        $endDate = G::replaceDataField( $this->endDate, $owner->values );

        $beforeDate = G::replaceDataField( $this->beforeDate, $owner->values );
        $afterDate = G::replaceDataField( $this->afterDate, $owner->values );

        if ($startDate != '') {
            if (! $this->verifyDateFormat( $startDate )) {
                $startDate = '';
            }
        }
        if (isset( $beforeDate ) && $beforeDate != '') {
            if ($this->isvalidBeforeFormat( $beforeDate )) {
                $startDate = $this->calculateBeforeFormat( $beforeDate, 1 );
            }
        }

        if ($startDate == '' && isset( $this->size ) && is_numeric( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
            $startDate = $this->size . '-01-01';
        }

        if ($startDate == '') {
            //$startDate = date ( 'Y-m-d' ); // the default is the current date
        }

        if ($endDate != '') {
            if (! $this->verifyDateFormat( $endDate )) {
                $endDate = '';
            }
        }

        if (isset( $afterDate ) && $afterDate != '') {
            if ($this->isvalidBeforeFormat( $afterDate )) {
                $endDate = $this->calculateBeforeFormat( $afterDate, + 1 );
            }
        }

        if (isset( $this->maxlength ) && is_numeric( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
            $endDate = $this->maxlength . '-01-01';
        }
        if ($endDate == '') {
            //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
            $endDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) ); // the default is the current date + 2 years
        }

        $tmp = str_replace( "%", "", $mask );
        if (trim( $value ) == '' or $value == null) {
            $value = ''; //date ($tmp);
        } else {
            switch (strtolower( $value )) {
                case 'today':
                    $value = date( $tmp );
                    break;
                default:
                    if (! $this->verifyDateFormat( $value, $mask )) {
                        $value = '';
                    }
                    break;
            }
        }

        if ($value == '') {
            $valueDate = Array (date( 'Y' ),date( 'm' ),date( 'd' )
            );
        } else {
            $valueDate = $this->getSplitDate( $value, $mask );
        }

        $startDate = $this->getSplitDate( $startDate, 'Y-m-d' );
        //adatation for new js calendar widget
        $startDate[2] = $startDate[2] - 1;

        $endDate = $this->getSplitDate( $endDate, 'Y-m-d' );
        //adatation for new js calendar widget
        $endDate[2] = $endDate[2] + 1;

        $extra = (defined( 'SYS_LANG_DIRECTION' ) && SYS_LANG_DIRECTION == 'R') ? 'direction:rtl; float:right' : 'direction:ltr';

        if (isset( $this->showtime ) && $this->showtime) {
            $mask .= ' h:i';
            $img = (defined( 'SYS_LANG_DIRECTION' ) && SYS_LANG_DIRECTION == 'R') ? 'pmdatetimeiw.png' : 'pmdatetime.png';
            $style = 'background-image:url(/images/' . $img . ');float:left; width:131px; height:22px;padding:2px 1px 1px 3px;cursor:pointer;color:#000; ' . $extra . ';';
            $showTime = 'true';
        } else {
            $img = (defined( 'SYS_LANG_DIRECTION' ) && SYS_LANG_DIRECTION == 'R') ? 'pmdateiw.png' : 'pmdate.png';
            $style = 'background-image:url(/images/' . $img . ');float:left; width:100px; height:22px;padding:2px 1px 1px 3px;cursor:pointer;color:#000; direction:' . $extra . ';';
            $showTime = 'false';
        }

        if ($this->editable == "1") {
            $style = '';
        }

        // Note added by Gustavo Cruz
        // also the fields rendered in a grid needs now have an attribute required set to 0 or 1
        // that it means not required or required respectively.
        if ($this->mode == 'edit' && $this->readOnly != "1") {
            if ($this->editable != "1") {
                $html = '<input type="text" required="' . $isRequired . '" style="display:none" id="' . $pID . '" name="' . $pID . '" value="' . $value . '" onchange="' . $this->onchange . '"/>';
                $html .= '<div id="' . $pID . '[div]" name="' . $pID . '[div]" onclick="var oc=new NeyekCalendar(\'' . $pID . '\');
            oc.picker(
              {\'year\':\'' . $valueDate[0] . '\',\'month\':\'' . $valueDate[1] . '\',\'day\':\'' . $valueDate[2] . '\'},
            \'' . $mask . '\',
            \'' . SYS_LANG . '\',
            {\'year\':\'' . $startDate[0] . '\',\'month\':\'' . $startDate[1] . '\',\'day\':\'' . $startDate[2] . '\'},
            {\'year\':\'' . $endDate[0] . '\',\'month\':\'' . $endDate[1] . '\',\'day\':\'' . $endDate[2] . '\'},
            ' . $showTime . ',
            event
          ); return false;" style="' . $style . '">&nbsp;' . $value . '</div>';
            } else {
                $html = '<input id="' . $pID . '" name="' . $pID . '" style="' . $style . '" value="' . $value . '" size="14" class="module_app_input___gray" onchange="' . $this->onchange . '">&nbsp;';
                $html .= '<a href="#" onclick="var oc=new NeyekCalendar(\'' . $pID . '\', 1);
            oc.picker(
              {\'year\':\'' . $valueDate[0] . '\',\'month\':\'' . $valueDate[1] . '\',\'day\':\'' . $valueDate[2] . '\'},
            \'' . $mask . '\',
            \'' . SYS_LANG . '\',
            {\'year\':\'' . $startDate[0] . '\',\'month\':\'' . $startDate[1] . '\',\'day\':\'' . $startDate[2] . '\'},
            {\'year\':\'' . $endDate[0] . '\',\'month\':\'' . $endDate[1] . '\',\'day\':\'' . $endDate[2] . '\'},
            ' . $showTime . ',
            event
          ); return false;"><img src="/images/pmdateicon.png" width="16px" height="18px" border="0"></a>';
            }

        } else {
            $html = '<input type="hidden" id="' . $pID . '" name="' . $pID . '" value="' . $value . '" onchange="' . $this->onchange . '"/>';
            $html .= "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>";
        }
        //    if($this->hint){
        //           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
        //                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
        //                </a>';
        //    }
        //print '<input type="text" id="'.$pID.'" name="'.$pID.'" value="'.$value.'" onchange="'.$this->onchange.'"/>';
        $html .= $this->renderHint();
        return $html;
    }

    /**
     * modify the date format
     *
     * @param <Date> $date
     * @param $mask
     * @return <type>
     */
    public function getSplitDate ($date, $mask)
    {
        $sw1 = false;
        for ($i = 0; $i < 3; $i ++) {
            $item = substr( $mask, $i * 2, 1 );
            switch ($item) {
                case 'Y':
                    switch ($i) {
                        case 0:
                            $d1 = substr( $date, 0, 4 );
                            break;
                        case 1:
                            $d1 = substr( $date, 3, 4 );
                            break;
                        case 2:
                            $d1 = substr( $date, 6, 4 );
                            break;
                    }
                    $sw1 = true;
                    break;
                case 'y':
                    switch ($i) {
                        case 0:
                            $d1 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d1 = substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d1 = substr( $date, 6, 2 );
                            break;
                    }
                    break;
                case 'm':
                    switch ($i) {
                        case 0:
                            $d2 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d2 = ($sw1) ? substr( $date, 5, 2 ) : substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d2 = ($sw1) ? substr( $date, 8, 2 ) : substr( $date, 5, 2 );
                            break;
                    }
                    break;
                case 'd':
                    switch ($i) {
                        case 0:
                            $d3 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d3 = ($sw1) ? substr( $date, 5, 2 ) : substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d3 = ($sw1) ? substr( $date, 8, 2 ) : substr( $date, 5, 2 );
                            break;
                    }
                    break;
            }
        }
        return Array (isset( $d1 ) ? $d1 : '',isset( $d2 ) ? $d2 : '',isset( $d3 ) ? $d3 : ''
        );
    }
}

/**
 *
 * @package gulliver.system
 * AVOID TO ENTER HERE : EXPERIMENTAL !!!
 * by Caleeli.
 *
 */
class XmlForm_Field_Xmlform extends XmlForm_Field
{
    public $xmlfile = '';
    public $initRows = 1;
    public $group = 0;
    public $addRow = true;
    public $deleteRow = false;
    public $editRow = false;
    public $sql = '';
    //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
    public $fillType = 0;
    public $fields = array ();
    public $scriptURL;
    public $id = '';

    /**
     * Function XmlForm_Field_Xmlform
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string xmlnode
     * @param string language
     * @param string home
     * @return string
     */
    public function XmlForm_Field_Xmlform ($xmlnode, $language, $home)
    {
        parent::XmlForm_Field( $xmlnode, $language );
        $this->parseFile( $home, $language );
    }

    /**
     * Function parseFile
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string home
     * @param string language
     * @return string
     */
    public function parseFile ($home, $language)
    {
        $this->xmlform = new XmlForm();
        $this->xmlform->home = $home;
        $this->xmlform->parseFile( $this->xmlfile . '.xml', $language, false );
        $this->fields = $this->xmlform->fields;
        $this->scriptURL = $this->xmlform->scriptURL;
        $this->id = $this->xmlform->id;
        unset( $this->xmlform );
    }

    /**
     * Function render
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string values
     * @return string
     */
    public function render ($values)
    {
        $html = '';
        foreach ($this->fields as $f => $v) {
            $html .= $v->render( '' );
        }
        $this->id = $this->owner->id . $this->name;
        $tpl = new xmlformTemplate( $this, PATH_CORE . 'templates/xmlform.html' );
        $this->values = $values;
        //$this->rows=count(reset($values));
        $tpl->template = $tpl->printTemplate( $this );
        //In the header
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile( $this->scriptURL );
        $oHeadPublisher->addScriptCode( $tpl->printJavaScript( $this ) );
        return $tpl->printObject( $this );
    }
}

/**
 * Class XmlForm
 * Main Class
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm
{
    public $tree;
    public $id = '';
    public $name = '';
    public $language;
    /* @attribute string version 0.xxx = Previous to pre-open source
    */
    public $version = '0.3';
    public $fields = array ();
    public $title = '';
    public $home = '';
    public $parsedFile = '';
    public $type = 'xmlform';
    public $fileName = '';
    public $scriptFile = '';
    public $scriptURL = '';
    /* Special propose attributes*/
    public $sql;
    public $sqlConnection;
    /*Attributes for the xmlform template*/
    public $width = 600;
    public $height = "100%";
    public $border = 1;
    public $mode = '';
    // public $labelWidth = 140;
    // public $labelWidth        = 180;
    public $labelWidth = "40%";
    public $onsubmit = '';
    public $requiredFields = array ();
    public $fieldContentWidth = 450;

    /**
     * Function xmlformTemplate
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @param string templateFile
     * @return string
     */
    public function parseFile ($filename, $language, $forceParse)
    {
        $this->language = $language;
        $filenameInitial = $filename;
        $filename = $this->home . $filename;

        //if the xmlform file doesn't exists, then try with the plugins folders
        if (! is_file( $filename )) {
            $aux = explode( PATH_SEP, $filenameInitial );
            //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
            if (count( $aux ) > 2) {
                //Subfolders
                $filename = array_pop( $aux );
                $aux0 = implode( PATH_SEP, $aux );
                $aux = array ();
                $aux[0] = $aux0;
                $aux[1] = $filename;
            }
            if (count( $aux ) == 2 && defined( 'G_PLUGIN_CLASS' )) {
                $oPluginRegistry = & PMPluginRegistry::getSingleton();
                if ($response = $oPluginRegistry->isRegisteredFolder( $aux[0] )) {
                    if ($response !== true) {
                        $sPath = PATH_PLUGINS . $response . PATH_SEP;
                    } else {
                        $sPath = PATH_PLUGINS;
                    }
                    $filename = $sPath . $aux[0] . PATH_SEP . $aux[1];
                }
            }
        }

        $this->fileName = $filename;
        $parsedFile = dirname( $filename ) . PATH_SEP . basename( $filename, 'xml' ) . $language;

        $parsedFilePath = defined( 'PATH_C' ) ? (defined( 'SYS_SYS' ) ? PATH_C . 'ws' . PATH_SEP . SYS_SYS . PATH_SEP : PATH_C) : PATH_DATA;
        $parsedFilePath .= 'xmlform/' . substr( $parsedFile, strlen( $this->home ) );

        // Improvement for the js cache - Start
        $realPath = substr( realpath( $this->fileName ), strlen( realpath( $this->home ) ), - 4 );
        if (substr( $realPath, 0, 1 ) != PATH_SEP) {
            $realPath = PATH_SEP . $realPath;
        }
        $filesToDelete = substr( (defined( 'PATH_C' ) ? PATH_C : PATH_DATA) . 'xmlform/', 0, - 1 ) . $realPath . '.*.js';
        $auxPath = explode( PATH_SEP, $realPath );
        $auxPath[count( $auxPath ) - 1] = $auxPath[count( $auxPath ) - 1] . '.' . md5( filemtime( $this->fileName ) );
        $realPath = implode( PATH_SEP, $auxPath );
        // Improvement for the js cache - End
        $this->parsedFile = $parsedFilePath;
        $this->scriptURL = '/jsform' . $realPath . '.js';
        $this->scriptFile = substr( (defined( 'PATH_C' ) ? PATH_C : PATH_DATA) . 'xmlform/', 0, - 1 ) . substr( $this->scriptURL, 7 );
        $this->id = G::createUID( '', substr( $this->fileName, strlen( $this->home ) ) );
        $this->scriptURL = str_replace( '\\', '/', $this->scriptURL );

        $newVersion = false;
        if ($forceParse || ((! file_exists( $this->parsedFile )) || (filemtime( $filename ) > filemtime( $this->parsedFile )) || (filemtime( __FILE__ ) > filemtime( $this->parsedFile ))) || (! file_exists( $this->scriptFile )) || (filemtime( $filename ) > filemtime( $this->scriptFile ))) {
            if (glob( $filesToDelete )) {
                foreach (glob( $filesToDelete ) as $fileToDelete) {
                    @unlink( $fileToDelete );
                }
            }
            $this->tree = new Xml_Document();
            $this->tree->parseXmlFile( $filename );
            //$this->tree->unsetParent();
            if (! is_object( $this->tree->children[0] )) {
                throw new Exception( 'Failure loading root node.' );
            }
            $this->tree = &$this->tree->children[0]->toTree();
            //ERROR CODE [1] : Failed to read the xml document
            if (! isset( $this->tree )) {
                return 1;
            }
            $xmlNode = & $this->tree->children;

            //Set the form's attributes
            $myAttributes = get_class_vars( get_class( $this ) );
            foreach ($myAttributes as $k => $v) {
                $myAttributes[$k] = strtolower( $k );
            }
            foreach ($this->tree->attributes as $k => $v) {
                $key = array_search( strtolower( $k ), $myAttributes );
                if (($key !== false) && (strtolower( $k ) !== 'fields') && (strtolower( $k ) !== 'values')) {
                    $this->{$key} = $v;
                }
            }
            //Reeplace non valid characters in xmlform name with "_"
            $this->name = preg_replace( '/\W/', '_', $this->name );
            //Create fields


            foreach ($xmlNode as $k => $v) {
                if (($xmlNode[$k]->type !== 'cdata') && isset( $xmlNode[$k]->attributes['type'] )) {
                    if (class_exists( 'XmlForm_Field_' . $xmlNode[$k]->attributes['type'] )) {
                        $x = '$field = new XmlForm_Field_' . $xmlNode[$k]->attributes['type'] . '( $xmlNode[$k], $language, $this->home, $this);';

                        eval( $x );
                    } else {
                        $field = new XmlForm_Field( $xmlNode[$k], $language, $this->home, $this );
                    }

                    $field->language = $this->language;
                    $this->fields[$field->name] = $field;
                }

                if (isset( $xmlNode[$k]->attributes['required'] ) || isset( $xmlNode[$k]->attributes['validate'] )) {
                    // the fields or xml nodes with a required attribute are put in an array that is passed to the view file
                    $isEditMode = isset( $xmlNode[$k]->attributes['mode'] ) && $xmlNode[$k]->attributes['mode'] == 'view' ? false : true;

                    if ($isEditMode && $this->mode != 'view') {

                        $validateValue = "";
                        if (isset( $xmlNode[$k]->attributes['validate'] )) {
                            $validateValue = $xmlNode[$k]->attributes['validate'];
                        }
                        $requiredValue = "0";
                        if (isset( $xmlNode[$k]->attributes['required'] )) {
                            $requiredValue = $xmlNode[$k]->attributes['required'] == 1 ? '1' : '0';
                        }

                        $this->requiredFields[] = array ('name' => $field->name,'type' => $xmlNode[$k]->attributes['type'],'label' => addslashes( trim( $field->label ) ),'validate' => $validateValue,'required' => $requiredValue
                        );
                    }

                }
            }

            //$oJSON = new Services_JSON();
            $jsonRequired =  G::json_encode( $this->requiredFields );
            $this->objectRequiredFields = str_replace( '"', "%27", str_replace( "'", "%39", $jsonRequired ) );

            //Load the default values
            //$this->setDefaultValues();
            //Save the cache file
            if (! is_dir( dirname( $this->parsedFile ) )) {
                G::mk_dir( dirname( $this->parsedFile ) );
            }
            $f = fopen( $this->parsedFile, 'w+' );
            //ERROR CODE [2] : Failed to open cache file
            if ($f === false) {
                return 2;
            }
            fwrite( $f, "<?php\n" );
            /*  fwrite ($f, '$this = unserialize( \'' .
                  addcslashes( serialize ( $this ), '\\\'' ) . '\' );' . "\n" );*/
            foreach ($this as $key => $value) {
                //cho $key .'<br/>';
                switch ($key) {
                    case 'home':
                    case 'fileName':
                    case 'parsedFile':
                    case 'scriptFile':
                    case 'scriptURL':
                        break;
                    default:
                        switch (true) {
                            case is_string( $this->{$key} ):
                                fwrite( $f, '$this->' . $key . '=\'' . addcslashes( $this->{$key}, '\\\'' ) . '\'' . ";\n" );
                                break;
                            case is_bool( $this->{$key} ):
                                fwrite( $f, '$this->' . $key . '=' . (($this->{$key}) ? 'true;' : 'false') . ";\n" );
                                break;
                            case is_null( $this->{$key} ):
                                fwrite( $f, '$this->' . $key . '=null' . ";\n" );
                                break;
                            case is_float( $this->{$key} ):
                            case is_int( $this->{$key} ):
                                fwrite( $f, '$this->' . $key . '=' . $this->{$key} . ";\n" );
                                break;
                            default:
                                fwrite( $f, '$this->' . $key . ' = unserialize( \'' . addcslashes( serialize( $this->{$key} ), '\\\'' ) . '\' );' . "\n" );
                        }
                }
            }
            fwrite( $f, "?>" );
            fclose( $f );
            $newVersion = true;
        } //if $forceParse
        //Loads the parsedFile.
        require ($this->parsedFile);
        $this->fileName = $filename;
        $this->parsedFile = $parsedFile;

        //RECREATE LA JS file
        //Note: Template defined with publisher doesn't affect the .js file
        //created at this point.
        if ($newVersion) {
            $template = PATH_CORE . 'templates/' . $this->type . '.html';
            //If the type is not the correct template name, use xmlform.html
            //if (!file_exists($template)) $template = PATH_CORE . 'templates/xmlform.html';
            if (($template !== '') && (file_exists( $template ))) {
                if (! is_dir( dirname( $this->scriptFile ) )) {
                    G::mk_dir( dirname( $this->scriptFile ) );
                }
                $f = fopen( $this->scriptFile, 'w' );
                $o = new xmlformTemplate( $this, $template );
                $scriptContent = $o->printJSFile( $this );
                unset( $o );
                fwrite( $f, $scriptContent );
                fclose( $f );
            }
        }
        return 0;
    }

    /**
     * Generic function to set values for the current object.
     *
     * @param $newValues
     * @return void
     */
    public function setValues ($newValues = array())
    {
        foreach ($this->fields as $k => $v) {
            if (array_key_exists( $k, $newValues )) {
                $this->values[$k] = $newValues[$k];
            }
        }
        foreach ($this->fields as $k => $v) {
            if (is_object( $this->fields[$k] ) && get_class( $this->fields[$k] ) != '__PHP_Incomplete_Class') {
                $this->fields[$k]->owner = & $this;
            }

        }
    }

    /**
     * Generic function to print the current object.
     *
     * @param $template
     * @param &$scriptContent
     * @return string
     */
    public function render ($template, &$scriptContent)
    {
        $o = new xmlformTemplate( $this, $template );
        if (is_array( reset( $this->values ) )) {
            $this->rows = count( reset( $this->values ) );
        }
        $o->template = $o->printTemplate( $this );
        $scriptContent = $o->printJavaScript( $this );
        return $o->printObject( $this );
    }

    /**
     * Clone the current object
     *
     * @return Object
     */
    public function cloneObject ()
    {
        return unserialize( serialize( $this ) );
    }
}

/**
 * Class xmlformTemplate
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class xmlformTemplate extends Smarty
{
    public $template;
    public $templateFile;

    /**
     * Function xmlformTemplate
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @param string templateFile
     * @return string
     */
    public function xmlformTemplate (&$form, $templateFile)
    {
        $this->template_dir = PATH_XMLFORM;
        $this->compile_dir = PATH_SMARTY_C;
        $this->cache_dir = PATH_SMARTY_CACHE;
        $this->config_dir = PATH_THIRDPARTY . 'smarty/configs';
        $this->caching = false;

        // register the resource name "db"
        $this->templateFile = $templateFile;
    }

    /**
     * Function printTemplate
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @param string target
     * @return string
     */
    public function printTemplate (&$form, $target = 'smarty')
    {
        if (strcasecmp( $target, 'smarty' ) === 0) {
            $varPrefix = '$';
        }
        if (strcasecmp( $target, 'templatePower' ) === 0) {
            $varPrefix = '';
        }

        $ft = new StdClass();
        foreach ($form as $name => $value) {
            if (($name !== 'fields') && ($value !== '')) {
                $ft->{$name} = '{$form_' . $name . '}';
            }
            if ($name === 'cols') {
                $ft->{$name} = $value;
            }
            if ($name === 'owner') {
                $ft->owner = & $form->owner;
            }
            if ($name === 'deleteRow') {
                $ft->deleteRow = $form->deleteRow;
            }
            if ($name === 'addRow') {
                $ft->addRow = $form->addRow;
            }
            if ($name === 'editRow') {
                $ft->editRow = $form->editRow;
            }
        }
        if (! isset( $ft->action )) {
            $ft->action = '{$form_action}';
        }
        $hasRequiredFields = false;

        foreach ($form->fields as $k => $v) {
            $ft->fields[$k] = $v->cloneObject();
            $ft->fields[$k]->label = '{' . $varPrefix . $k . '}';

            if ($form->type === 'grid') {
                if (strcasecmp( $target, 'smarty' ) === 0) {
                    $ft->fields[$k]->field = '{' . $varPrefix . 'form.' . $k . '[row]}';
                }
                if (strcasecmp( $target, 'templatePower' ) === 0) {
                    $ft->fields[$k]->field = '{' . $varPrefix . 'form[' . $k . '][row]}';
                }
            } else {
                if (strcasecmp( $target, 'smarty' ) === 0) {
                    $ft->fields[$k]->field = '{' . $varPrefix . 'form.' . $k . '}';
                }
                if (strcasecmp( $target, 'templatePower' ) === 0) {
                    $ft->fields[$k]->field = '{' . $varPrefix . 'form[' . $k . ']}';
                }
            }

            $hasRequiredFields = $hasRequiredFields | (isset( $v->required ) && ($v->required == '1') && ($v->mode == 'edit'));

            if ($v->type == 'xmlmenu') {
                $menu = $v;
            }
        }

        if (isset( $menu )) {
            if (isset( $menu->owner->values['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] )) {
                $prevStep_url = $menu->owner->values['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'];

                $this->assign( 'prevStep_url', $prevStep_url );
                $this->assign( 'prevStep_label', G::loadTranslation( 'ID_BACK' ) );
            }
        }

        $this->assign( 'hasRequiredFields', $hasRequiredFields );
        $this->assign( 'form', $ft );
        $this->assign( 'printTemplate', true );
        $this->assign( 'printJSFile', false );
        $this->assign( 'printJavaScript', false );
        //$this->assign ( 'dynaformSetFocus', "try {literal}{{/literal} dynaformSetFocus();}catch(e){literal}{{/literal}}" );
        return $this->fetch( $this->templateFile );
    }

    /**
     * Function printJavaScript
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @return string
     */
    public function printJavaScript (&$form)
    {
        $this->assign( 'form', $form );
        $this->assign( 'printTemplate', false );
        $this->assign( 'printJSFile', false );
        $this->assign( 'printJavaScript', true );
        return $this->fetch( $this->templateFile );
    }

    /**
     * Function printJSFile
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @return string
     */
    public function printJSFile (&$form)
    {
        //JS designer>preview
        if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"]) && preg_match("/^.*dynaforms_Editor\?.*PRO_UID=.*DYN_UID=.*$/", $_SERVER["HTTP_REFERER"]) && preg_match("/^.*dynaforms\/dynaforms_Ajax.*$/", $_SERVER["REQUEST_URI"])) {
            $js = null;

            foreach ($form->fields as $index => $value) {
                $field = $value;

                if ($field->type == "javascript" && !empty($field->code)) {
                    $js = $js . " " . $field->code;
                }
            }

            if ($js != null) {
                $form->jsDesignerPreview = "
                //JS designer>preview
                $js

                loadForm_" . $form->id . "(\"../gulliver/defaultAjaxDynaform\");

                if (typeof(dynaformOnload) != \"undefined\") {
                    dynaformOnload();
                }
                ";
            }
        }

        $this->assign( 'form', $form );
        $this->assign( 'printTemplate', false );
        $this->assign( 'printJSFile', true );
        $this->assign( 'printJavaScript', false );
        return $this->fetch( $this->templateFile );
    }

    /**
     * Function getFields
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param string form
     * @return string
     */
    public function getFields (&$form, $therow = -1)
    {
        $result = array ();

        foreach ($form->fields as $k => $v) {
            $field = $v;

            if ($form->mode != '') {
                #@ last modification: erik
                $field->mode = $form->mode; #@
            } #@

            //if (isset($form->fields[$k]->sql)) $form->fields[$k]->executeSQL( $form );
            $value = (isset( $form->values[$k] )) ? $form->values[$k] : null;
            $result[$k] = G::replaceDataField( $form->fields[$k]->label, $form->values );

            if ($form->type == 'xmlform') {
                if (in_array($field->type, array("text", "currency", "percentage", "password", "suggest", "textarea", "dropdown", "yesno", "listbox", "checkbox", "date", "link", "file"))) {
                    $result[$k] = '<label for="form[' . $k . ']">' . $result[$k] . '</label>';
                }
            }

            if (! is_array( $value )) {
                if ($form->type == 'grid') {
                    $aAux = array ();
                    if (!isset($form->values[$form->name])) {
                        $form->values[$form->name] = array();
                    }
                    if ($therow == - 1) {
                        for ($i = 0; $i < count( $form->values[$form->name] ); $i ++) {
                            $aAux[] = '';
                        }
                    } else {
                        for ($i = 0; $i < $therow; $i ++) {
                            $aAux[] = '';
                        }
                    }

                    switch ($field->type) {
                        case "link":
                            $result["form"][$k] = $form->fields[$k]->renderGrid($aAux, array(), $form);
                            break;
                        default:
                            $result["form"][$k] = $form->fields[$k]->renderGrid($aAux, $form);
                            break;
                    }
                } else {
                    switch ($field->type) {
                        case "link":
                            $result["form"][$k] = $form->fields[$k]->render(
                                $value,
                                (isset($form->values[$k . "_label"]))? $form->values[$k . "_label"] : null,
                                $form
                            );
                            break;
                        default:
                            $result["form"][$k] = $form->fields[$k]->render($value, $form);
                            break;
                    }
                }
            } else {
                /*if (isset ( $form->owner )) {
                    if (count ( $value ) < count ( $form->owner->values [$form->name] )) {
		                $i = count ( $value );
		                $j = count ( $form->owner->values [$form->name] );

		                for($i; $i < $j; $i ++) {
		                    $value [] = '';
                        }
                    }
                }*/

                if ($field->type == "grid") {
                    // Fix data for grids
                    if (is_array($form->fields[$k]->fields)) {
                        foreach ($form->fields[$k]->fields as $gridFieldName => $gridField) {
                            $valueLength = count($value);
                            for ($i = 1; $i <= $valueLength; $i++) {
                                if (!isset($value[$i][$gridFieldName])) {
                                    switch ($gridField->type) {
                                        case 'checkbox':
                                            $defaultAttribute = 'falseValue';
                                            break;
                                        default:
                                            $defaultAttribute = 'defaultValue';
                                            break;
                                    }
                                    $value[$i][$gridFieldName] = isset($gridField->$defaultAttribute) ? $gridField->$defaultAttribute : '';
                                }
                            }
                        }
                    }
                    $form->fields[$k]->setScrollStyle( $form );
                    $result["form"][$k] = $form->fields[$k]->renderGrid( $value, $form, $therow );
                } else {
                    switch ($field->type) {
                        case "dropdown":
                            $result["form"][$k] = $form->fields[$k]->renderGrid( $value, $form, false, $therow );
                            break;
                        case "file":
                            $result["form"][$k] = $form->fields[$k]->renderGrid( $value, $form, $therow );
                            break;
                        case "link":
                            $result["form"][$k] = $form->fields[$k]->renderGrid(
                                $value,
                                (isset($form->values[$k . "_label"]))? $form->values[$k . "_label"] : array(),
                                $form
                            );
                            break;
                        default:
                            $result["form"][$k] = $form->fields[$k]->renderGrid( $value, $form );
                            break;
                    }
                }
            }
        }

        foreach ($form as $name => $value) {
            if ($name !== 'fields') {
                $result['form_' . $name] = $value;
            }
        }

        return $result;
    }

    /**
    * Function printObject
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string form
    * @return string
    */
    public function printObject(&$form, $therow = -1)
    {
        //to do: generate the template for templatePower.
        //DONE: The template was generated in printTemplate, to use it
        // is necesary to load the file with templatePower and send the array
        //result
        $this->register_resource ( 'mem', array (array (&$this, '_get_template' ), array ($this, '_get_timestamp' ), array ($this, '_get_secure' ), array ($this, '_get_trusted' ) ) );
        $result = $this->getFields ( $form, $therow );

        $this->assign ( array ('PATH_TPL' => PATH_TPL ) );
        $this->assign ( $result );
        if ( defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' ) {
            switch( $form->type ){
                case 'toolbar':
                    $form->align = 'right';
                    break;
            }
        }

        $this->assign ( array ('_form' => $form ) );
        //'mem:defaultTemplate'.$form->name obtains the template generated for the
        //current "form" object, then this resource y saved by Smarty in the
        //cache_dir. To avoiding troubles when two forms with the same id are being
        //drawed in a same page with different templates, add an . rand(1,1000)
        //to the resource name. This is because the process of creating templates
        //(with the method "printTemplate") and painting takes less than 1 second
        //so the new template resource generally will had the same timestamp.
        $output = $this->fetch ( 'mem:defaultTemplate' . $form->name );
        return $output;
    }

    /**
    * Smarty plugin
    * -------------------------------------------------------------
    * Type:     resource
    * Name:     mem
    * Purpose:  Fetches templates from this object
    * -------------------------------------------------------------
    */
    public function _get_template($tpl_name, &$tpl_source, &$smarty_obj)
    {
        $tpl_source = $this->template;
        return true;
    }

    /**
    * Function _get_timestamp
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string tpl_name
    * @param string tpl_timestamp
    * @param string smarty_obj
    * @return string
    */
    public function _get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
        //NOTE: +1 prevents to load the smarty cache instead of this resource
        $tpl_timestamp = time () + 1;
        return true;
    }

    /**
    * Function _get_secure
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string tpl_name
    * @param string smarty_obj
    * @return string
    */
    public function _get_secure($tpl_name, &$smarty_obj)
    {
        // assume all templates are secure
        return true;
    }

    /**
    * Function _get_trusted
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string tpl_name
    * @param string smarty_obj
    * @return string
    */
    public function _get_trusted($tpl_name, &$smarty_obj)
    {
        // not used for templates
    }
}

/**
* @package gulliver.system
*/
class XmlForm_Field_Image extends XmlForm_Field
{
    public $file         = '';
    public $home         = 'public_html';
    public $withoutLabel = false;

    /**
    * Function render
    * @author David S. Callizaya S. <davidsantos@colosa.com>
    * @access public
    * @param string values
    * @return string
    */
    public function render ($value, $owner = null)
    {
        $url = G::replaceDataField($this->file, $owner->values);
        if ($this->home === "methods") {
            $url = G::encryptlink( SYS_URI . $url );
        }
        if ($this->home === "public_html") {
            $url ='/' . $url ;
        }
        return '<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'" '.
            (($this->style)?'style="'.$this->style.'"':'')
            .' alt ="'.htmlentities($value,ENT_QUOTES,'utf-8').'"/>';
    }
}

//mask function to php
function masktophp ($mask, $value)
{
    $tmp = str_replace("%", "", $mask);
    if (preg_match('/M/',$tmp)) {
        $tmp = str_replace("M", "i", $tmp);
    }
    if (preg_match('/b/',$tmp)) {
        $tmp = str_replace("b", "M", $tmp);
    }
    if (preg_match('/B/',$tmp)) {
        $tmp = str_replace("B", "F", $tmp);
    }
    if (preg_match('/S/',$tmp)) {
        $tmp = str_replace("S", "s", $tmp);
    }
    if (preg_match('/o/',$tmp)) {
        $tmp = str_replace("o", "n", $tmp);
    }
    if (preg_match('/a/',$tmp)) {
        $tmp = str_replace("a", "D", $tmp);
    }
    if (preg_match('/l/',$tmp)) {
        $tmp = str_replace("l", "g", $tmp);
    }
    if (preg_match('/A/',$tmp)) {
        $tmp = str_replace("A", "l", $tmp);
    }
    if (preg_match('/I/',$tmp)) {
        $tmp = str_replace("I", "h", $tmp);
    }
    if (preg_match('/j/',$tmp)) {
        $tmp = str_replace("j", "z", $tmp);
    }
    if (preg_match('/k/',$tmp)) {
        $tmp = str_replace("k", "G", $tmp);
    }
    if (preg_match('/e/',$tmp)) {
        $tmp = str_replace("e", "j", $tmp);
    }
    if (preg_match('/u/',$tmp)) {
        $tmp = str_replace("u", "N", $tmp);
    }
    if (preg_match('/p/',$tmp)) {
        $tmp = str_replace("p", "A", $tmp);
    }
    if (preg_match('/P/',$tmp)) {
        $tmp = str_replace("P", "a", $tmp);
    }

    if ($value == 'today') {
        $value = date($tmp);
    } else {
        $value = date($tmp, strtotime ($value));
    }
    return $value;
}

if (!function_exists('strptime')) {
    function strptime($date, $format) {
        $masks = array(
            '%d' => '(?P<d>[0-9]{2})',
            '%m' => '(?P<m>[0-9]{2})',
            '%Y' => '(?P<Y>[0-9]{4})',
            '%H' => '(?P<H>[0-9]{2})',
            '%M' => '(?P<M>[0-9]{2})',
            '%S' => '(?P<S>[0-9]{2})',
            // usw..
        );

        $rexep = "#".strtr(preg_quote($format), $masks)."#";
        if(!preg_match($rexep, $date, $out)) {
            return false;
        }

        if (!isset($out['S'])) {
            $out['S'] = 0;
        } else {
            $out['S'] = (int) $out['S'];
        }

        if (!isset($out['M'])) {
            $out['M'] = 0;
        } else {
            $out['M'] = (int) $out['M'];
        }

        if (!isset($out['H'])) {
            $out['H'] = 0;
        } else {
            $out['H'] = (int) $out['H'];
        }

        if (!isset($out['d'])) {
            $out['d'] = 0;
        } else {
            $out['d'] = (int) $out['d'];
        }

        if (!isset($out['m'])) {
            $out['m'] = 0;
        } else {
            $out['m'] = (int) $out['m'];
        }

        if (!isset($out['Y'])) {
            $out['Y'] = 0;
        } else {
            $out['Y'] = (int) $out['Y'];
        }

        $ret = array(
            "tm_sec"  => $out['S'],
            "tm_min"  => $out['M'],
            "tm_hour" => $out['H'],
            "tm_mday" => $out['d'],
            "tm_mon"  => $out['m'] ? $out['m'] - 1 : 0,
            "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
        );
        return $ret;
    }
}