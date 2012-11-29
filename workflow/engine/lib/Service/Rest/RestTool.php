<?php
/**
 * Class Service_Rest_RestTool
 *
 * This tool generates a rest-config.ini file and build rest crud api for 'Restler' lib.
 * Class since: Aug 22, 2012
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com>
 */
class Service_Rest_RestTool
{
    /**
     * Stores absolute file path of rest configuration ini file (rest-config.ini)
     * @var string
     */
    protected $configFile = '';

    /**
     * Stores configuration loaded from configuration ini file
     * @var array
     */
    protected $config = array();

    /**
     * Stores absolute filename patgh of database xml schema
     * @var string
     */
    protected $dbXmlSchemaFile = '';

    /**
     * Stores information of pmos tables
     * @var array
     */
    protected $dbInfo = array();

    /**
     * Stores obsolute base path to output generated files
     * @var string
     */
    protected $basePath = '';

    /**
     * Init method to initialize the class after previous configurations
     */
    public function init()
    {
        self::out('ProcessMaker Rest Crud Api Generator Tool v1.0', 'success', true);
        echo PHP_EOL;

        // configuring paths by default if there were not configured before.
        if (empty($this->dbXmlSchemaFile)) {
            $this->dbXmlSchemaFile = $this->basePath . 'config/schema.xml';
        }
        if (empty($this->configFile)) {
            $this->configFile = $this->basePath . 'config/rest-config.ini';
        }
    }

    public function setConfigFile($configFile)
    {
        $this->configFile = $configFile;
    }

    public function setDbXmlSchemaFile($dbXmlSchemaFile)
    {
        $this->dbXmlSchemaFile = $dbXmlSchemaFile;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Load rest ini. configuration
     */
    protected function loadConfig()
    {
        if (! file_exists($this->configFile)) {
            throw new Exception(sprintf("Runtime Error: Configuration file '%s' doesn't exist!", $this->configFile));
        }

        self::out('Loading config from: ', 'info', false);
        echo  $this->configFile . "\n";

        $config = @parse_ini_file($this->configFile, true);

        // parse composed sections names like [TABLE:SOME_TABLE]
        foreach ($config as $key => $value) {
            $sectionParts = explode(':', $key);

            if (count($sectionParts) == 2 && strtolower($sectionParts[0]) == 'table') {
                $this->config['_tables'][$sectionParts[1]] = $value;
            } else {
                $this->config[$key] = $value;
            }
        }
    }

    /**
     * Load db schema from xml file
     */
    protected function loadDbXmlSchema()
    {
        if (! file_exists($this->dbXmlSchemaFile)) {
            throw new Exception(sprintf(
                "Runtime Error: Configuration file '%s' doesn't exist!", $this->dbXmlSchemaFile
            ));
        }

        print "\n";
        self::out('Loading Xml Schema from: ', 'info', false);
        print $this->dbXmlSchemaFile . "\n";

        $doc = new Xml_DOMDocumentExtended();
        $doc->load($this->dbXmlSchemaFile);

        // dump to array
        $data = $doc->toArray();
        $tables = $data['database']['table'];

        // process just relevant information
        foreach ($tables as $table) {
            $this->dbInfo[$table['@name']]['pKeys'] = array();
            $this->dbInfo[$table['@name']]['columns'] = array();
            $this->dbInfo[$table['@name']]['required_columns'] = array();
            //Adding data types
            $this->dbInfo[$table['@name']]['type']['name'] = array();
            $this->dbInfo[$table['@name']]['type']['Length'] = array();

            foreach ($table['column'] as $column) {
                $this->dbInfo[$table['@name']]['columns'][] = $column['@name'];
                $this->dbInfo[$table['@name']]['type']['name'][] = $column['@type'];
                //adding size to typeLength if exists
                if (array_key_exists('@size', $column) && self::cast($column['@size'])) {
                   $this->dbInfo[$table['@name']]['type']['Length'][] = $column['@size'];
                }
                else{
                    $this->dbInfo[$table['@name']]['type']['Length'][] = '0';
                }
                //adding name to pkeys if exists primary key exists
                if (array_key_exists('@primaryKey', $column) && self::cast($column['@primaryKey'])) {
                    $this->dbInfo[$table['@name']]['pKeys'][] = $column['@name'];
                }
                //adding name to requiered_columns if required field exists
                if (array_key_exists('@required', $column) && self::cast($column['@required'])) {
                    $this->dbInfo[$table['@name']]['required_columns'][] = $column['@name'];
                }
            }
        }
    }

    /**
     * Build Rest configuration file
     * @param  string $filename configutaion filename to be generated
     */
    public function buildConfigIni($filename = '')
    {
        $this->loadDbXmlSchema();

        $configFile    = empty($filename) ? $this->configFile : $filename;

        $configIniStr  = <<<EOT
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;             -= ProcessMaker RestFul services configuration =-                ;
;                                                                              ;
; On this configuration file you can customize the Processmaker Rest Service.  ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Rest Service Configuration ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

enable_service = true

; add headers to rest server responses
[HEADERS]
  ; Enable this header to allow "Cross Domain AJAX" requests;
  ; This works because processmaker is handling correctly requests with method 'OPTIONS'
  ; that automatically is sent by a client using XmlHttpRequest or similar.
  ;Access-Control-Allow-Origin = *

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; DB Tables Crud generation Config ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;
; This configuration section is used by ./rest-gen command to build the Rest Crud Api
;
; Configure what methods GET, POST, PUT, DELETE should be exposed.
; Configure for each table/method what columns sould be exposed.
;
; Configuration for each table must starts with a section like:
; [TABLE:<table-name>]
;
; inside of each section there are two config keys:
;
; "ALLOW_METHODS" -> Use this param to set allowed methods (separeted by a single space).
; Complete example:
;   ALLOW_METHODS = GET POST PUT DELETE
;
; The others params are: "EXPOSE_COLUMNS_GET", "EXPOSE_COLUMNS_POST" and "EXPOSE_COLUMNS_PUT"
; this params are used to configure the columns that should be exposed;
; wildcard '*' can be used to speccify all columns.
;
; Example:
;
;[TABLE:MY_TABLE]
;  ALLOW_METHODS = GET POST PUT DELETE
;  EXPOSE_COLUMNS_GET  = *
;  EXPOSE_COLUMNS_POST = FIELD1 FIELD2 FIELD3 FIELD4
;  EXPOSE_COLUMNS_PUT  = FIELD1 FIELD2 FIELD3
;
EOT;
        $configIniStr .= "\n\n";

        foreach ($this->dbInfo as $table => $columns) {
            $strColumns    = implode(' ', $columns['columns']);
            $configIniStr .= ";Table '$table' with ".count($columns['columns'])." columns.\n";
            $configIniStr .= "[TABLE:$table]\n";
            $configIniStr .= "  ALLOW_METHODS = GET\n";
            $configIniStr .= "  EXPOSE_COLUMNS_GET  = *\n";
            $configIniStr .= "  EXPOSE_COLUMNS_POST = ".$strColumns."\n";
            $configIniStr .= "  EXPOSE_COLUMNS_PUT  = ".$strColumns."\n";
            $configIniStr .= "\n";
        }

        file_put_contents($configFile, $configIniStr);

        return $configFile;
    }

    /**
     * Build Rest Crud Api
     */
    public function buildCrudApi()
    {
        /**
         * load configuration from /engine/config/rest-config.ini and
         * load database schemda from /engine/config/schema.xml
         */
        $this->loadConfig();
        $this->loadDbXmlSchema();

        self::out('Output folder: ', 'info', false);
        echo $this->basePath . "services/rest/crud";

        if (! is_dir($this->basePath . "services/rest/crud/")) {
            G::mk_dir($this->basePath . "services/rest/crud/");
            echo ' (created)';
        }

        echo "\n\n";

        if (! is_writable($this->basePath . "services/rest/crud/")) {
            throw new Exception(fprintf(
                "Runtime Error: Output folder '%s' is not writable.",
                $this->basePath . "services/rest/crud/"
            ));
        }

        Haanga::configure(array(
            'template_dir' => dirname(__FILE__) . '/templates/',
            'cache_dir' => sys_get_temp_dir() . '/haanga_cache/',
            'compiler' => array(
                'compiler' => array( /* opts for the tpl compiler */
                    'if_empty' => false,
                    'autoescape' => true,
                    'strip_whitespace' => true,
                    'allow_exec'  => true
                ),
            )
        ));

        //new feature adding columns types as commentary.
        $infoExtra = array();
        foreach ($this->dbInfo as $tablename => $columns){
            $maxArray = count($columns['columns']);
            for($ptr = 0; $ptr < $maxArray; $ptr++){
                $columnName = $columns['columns'][$ptr];
                $type = $columns['type'];
                $typeName = $type['name'][$ptr];
                $typelength = $type['Length'][$ptr];
                $infoExtra[$tablename][] = "Column: " . $columnName . " of type ". $typeName . (($typelength != '0')?("[" . $typelength . "]"):"");
            }
        }
      
        $c = 0;
        //foreach ($this->config['_tables'] as $table => $conf) {
        foreach ($this->config['_tables'] as $table => $conf) {
            $classname = self::camelize($table, 'class');
            $allowedMethods = explode(' ', $conf['ALLOW_METHODS']);
            $methods = '';
            
            // Getting data for every method.
            foreach ($allowedMethods as $method) {
                // validation for a valid method
                if (! in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
                    throw new Exception("Invalid method '$method'.");
                }

                $method = strtoupper($method);
                $exposedColumns = array();
                $params = array();
                $paramsStr = array();
                $primaryKeys = array();

                // get columns to expose
                if (array_key_exists('EXPOSE_COLUMNS_'.$method, $conf)) {
                    if ($conf['EXPOSE_COLUMNS_'.$method] == '*') {
                        $exposedColumns = $this->dbInfo[$table]['columns'];
                    } else {
                        $exposedColumns = explode(' ', $conf['EXPOSE_COLUMNS_'.$method]);

                        // validation for valid columns
                        $tableColumns = $this->dbInfo[$table]['columns'];

                        foreach ($exposedColumns as $column) {
                            if (! in_array($column, $tableColumns)) {
                                throw new Exception("Invalid column '$column' for table $table, it does not exist!.");
                            }
                        }

                        // validate that all required columns are in exposed columns array
                        if ($method == 'POST') {
                            // intersect required columns with exposed columns
                            // to verify is all required columns are exposed
                            $intersect = array_intersect($this->dbInfo[$table]['required_columns'], $exposedColumns);

                            // the diff should be empty
                            $diff = array_diff($this->dbInfo[$table]['required_columns'], $intersect);

                            if (! empty($diff)) {
                                throw new Exception(sprintf(
                                    "Error: All required columns for table '%s' must be exposed for POST method.\n" .
                                    "PLease add all required columns on rule 'EXPOSE_COLUMNS_POST' or select all " .
                                    "with '*' selector.\n\n" .
                                    "Missing (%s) required fields for [%s] table:\n" .
                                    implode("\n", $diff),
                                    $table, count($diff), $table
                                ));
                            }
                        }
                    }
                }

                switch ($method) {
                    case 'GET':
                        foreach ($this->dbInfo[$table]['pKeys'] as $i => $pk) {
                            $paramsStr[$i] = "\$".self::camelize($pk).'=null';
                            $params[$i] = self::camelize($pk);
                        }
                        break;

                    case 'PUT':
                        foreach ($exposedColumns as $i => $column) {
                            $paramsStr[$i] = "\$".self::camelize($column);

                            if (! in_array($column, $this->dbInfo[$table]['pKeys'])) {
                                $params[$i] = self::camelize($column);
                            }
                        }
                        break;

                    case 'POST':
                        foreach ($exposedColumns as $i => $column) {
                            $paramsStr[$i] = "\$".self::camelize($column);
                            $params[$i] = self::camelize($column);
                        }
                        break;
                }
                $paramsStr = implode(', ', $paramsStr);

                // formatting primary keys for template
                foreach ($this->dbInfo[$table]['pKeys'] as $i => $pk) {
                    $primaryKeys[$i] = "\$".self::camelize($pk);
                }
                $primaryKeys = implode(', ', $primaryKeys);

                $methods .= Haanga::Load(
                    'method'.self::camelize($method, 'class').'.tpl',
                    array(
                        'params'      => $params,
                        'paramsStr'   => $paramsStr,
                        'primaryKeys' => $primaryKeys,
                        'columns'     => $exposedColumns,
                        'classname'   => $classname,
                    ),
                    true
                );

                $methods .= "\n";
            }

            $classContent = Haanga::Load('class.tpl', array(
                'classname' => $classname,
                'type' => $infoExtra[$table],
                'tablename' => $table,
                'methods'   => $methods
            ), true);

            //echo "File #$c - $classname.php saved!\n";
            ++$c;
            file_put_contents($this->basePath . "services/rest/crud/$classname.php", $classContent);
        }

        printf("Done, generated %s Rest Class Files.\n\n", self::out("($c)", 'success', false, true));
    }

    /**
     * Camilize a string
     * Example: some_underscored_string to SomeUnderscoredString
     *
     * @param  string $str  string to camelze
     * @param  string $type if the type is 'var' do not capitalize the first character.
     * @return string       camelized string
     */
    protected static function camelize($str, $type = 'var')
    {
        if (is_array($str)) {
            foreach ($str as $i => $value) {
                $str[$i] = self::camelize($value);
            }
        } elseif (is_string($str)) {
            $str = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($str))));
        }

        if ($type == 'var') {
            $str = substr(strtolower($str), 0, 1) . substr($str, 1);
        }

        return $str;
    }

    /**
     * Try convert a string to its native variable type
     * Example:
     * for a string 'true' => true
     * for a string 'false' => false
     * for a string '1.0' => 1.0
     *
     * @param  string $value string to try cast to its native variable type
     * @return mixed         value converted to its native type, if wasn't possible the same string will be returned
     */
    protected static function cast($value)
    {
        if ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        } elseif (is_numeric($value)) {
            return $value * 1;
        }

        return $value;
    }

    /**
     * colorize output
     */
    static public function out($text, $color = null, $newLine = true, $ret = false)
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            $hasColorSupport = false !== getenv('ANSICON');
        } else {
            $hasColorSupport = true;
        }

        $styles = array(
            'success' => "\033[0;32m%s\033[0m",
            'error' => "\033[31;31m%s\033[0m",
            'info' => "\033[33;33m%s\033[0m"
        );

        $format = '%s';

        if (isset($styles[$color]) && $hasColorSupport) {
            $format = $styles[$color];
        }

        if ($newLine) {
            $format .= PHP_EOL;
        }

        if ($ret) {
            return sprintf($format, $text);
        } else {
            printf($format, $text);
        }
    }
}


