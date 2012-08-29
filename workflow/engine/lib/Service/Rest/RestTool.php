<?php
/**
 * Class Service_Rest_RestTool
 *
 * This tool generate a rest-config.ini file and build rest crud api for 'Restler' lib.
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com>
 */
class Service_Rest_RestTool
{
    protected $configFile = '';
    protected $config = array();
    protected $dbXmlSchemaFile = '';
    protected $dbInfo = array();
    protected $basePath = '';

    public function __construct()
    {
        $this->basePath        = PATH_CORE;
        $this->dbXmlSchemaFile = 'config/schema.xml';
        $this->configFile      = 'config/rest-config.ini';
    }

    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    protected function loadConfig()
    {
        $configFile = $this->basePath . $this->configFile;

        if (! file_exists($configFile)) {
            throw new Exception(sprintf("Runtime Error: Configuration file '%s' doesn't exist!", $configFile));
        }

        $this->config = @parse_ini_file($configFile, true);
    }

    protected function loadDbXmlSchema()
    {
        $dbXmlSchemaFile = $this->basePath . $this->dbXmlSchemaFile;

        if (! file_exists($dbXmlSchemaFile)) {
            throw new Exception(sprintf("Runtime Error: Configuration file '%s' doesn't exist!", $dbXmlSchemaFile));
        }

        $doc = new Xml_DOMDocumentExtended();
        $doc->load($dbXmlSchemaFile);
        $data = $doc->toArray();
        $tables = $data['database']['table'];

        foreach ($tables as $table) {
            $this->dbInfo[$table['@name']]['pKeys'] = array();
            $this->dbInfo[$table['@name']]['columns'] = array();
            $this->dbInfo[$table['@name']]['required_columns'] = array();

            foreach ($table['column'] as $column) {
                $this->dbInfo[$table['@name']]['columns'][] = $column['@name'];

                if (array_key_exists('@primaryKey', $column) && self::cast($column['@primaryKey'])) {
                    $this->dbInfo[$table['@name']]['pKeys'][] = $column['@name'];
                }

                if (array_key_exists('@required', $column) && self::cast($column['@required'])) {
                    $this->dbInfo[$table['@name']]['required_columns'][] = $column['@name'];
                }
            }
        }
    }

    public function buildConfigIni($filename = '')
    {
        $this->loadDbXmlSchema();

        $configFile    = empty($filename) ? $this->basePath . $this->configFile : $filename;
        $configIniStr  = "; -= ProcessMaker RestFul services configuration =-\n";
        $configIniStr .= "\n";
        $configIniStr .= "; On this configuration file you can customize some aspects to expose on rest service.\n";
        $configIniStr .= "; Configure what methods (GET,POST,PUT,DELETE) should be exposed by ProcessMaker Rest server.\n";
        $configIniStr .= "; Configure for each table/method what columns sould be exposed.\n";
        $configIniStr .= "\n";

        foreach ($this->dbInfo as $table => $columns) {
            $strColumns    = implode(' ', $columns['columns']);
            $configIniStr .= ";Rest Api for table $table with (".count($columns['columns']).") columns.\n";
            $configIniStr .= "[$table]\n";
            $configIniStr .= "  ; Param to set allowed methods (separeted by a single space). Complete example: ALLOW_METHODS = GET POST PUT DELETE\n";
            $configIniStr .= "  ALLOW_METHODS = GET\n";
            $configIniStr .= "  ; Params to set columns that should be exposed, you can use wildcard '*' to specify all columns.\n";
            $configIniStr .= "  EXPOSE_COLUMNS_GET  = *\n";
            $configIniStr .= "  EXPOSE_COLUMNS_POST = ".$strColumns."\n";
            $configIniStr .= "  EXPOSE_COLUMNS_PUT  = ".$strColumns."\n";
            $configIniStr .= "\n";
        }

        file_put_contents($configFile, $configIniStr);

        return $configFile;
    }

    public function buildApi()
    {
        /**
         * load configuration from /engine/config/rest-config.ini and
         * load database schemda from /engine/config/schema.xml
         */
        $this->loadConfig();
        $this->loadDbXmlSchema();

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

        foreach ($this->config as $table => $conf) {
            $classname = self::camelize($table, 'class');
            $allowedMethods = explode(' ', $conf['ALLOW_METHODS']);
            $methods = '';
            //$allowedMethods = array('DELETE');

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
                'methods' => $methods
            ), true);

            echo "saving $classname.php\n";
            file_put_contents($this->basePath . "services/rest/crud/$classname.php", $classContent);
        }
    }

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
}


