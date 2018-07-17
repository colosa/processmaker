<?php

namespace ProcessMaker\Util;

use Propel;

/**
 * Class Cnn
 * @package ProcessMaker\Util
 */
class Cnn
{
    private $dbFile;
    private $workspace;

    /**
     * Establishes connection for the workspace
     * @param string $workspace Name workspace
     */
    public static function connect($workspace)
    {
        $cnn = new static();
        $cnn->workspace = $workspace;
        Propel::initConfiguration($cnn->buildParams());
    }

    /**
     * Loads the parameters required to connect to each workspace database
     * @return array
     */
    public function buildParams()
    {
        if ($this->readFileDBWorkspace()) {
            return $this->prepareDataSources();
        }
        return [];
    }

    /**
     * Reads the workspace db.php file
     * @return bool
     */
    private function readFileDBWorkspace()
    {
        if (file_exists(PATH_DB . $this->workspace . PATH_SEP . 'db.php')) {
            $this->dbFile = file_get_contents(PATH_DB . $this->workspace . PATH_SEP . 'db.php');
            return true;
        }
        return false;
    }

    /**
     * Prepares data resources
     * @return array
     */
    private function prepareDataSources()
    {
        $phpCode = preg_replace(
            '/define\s*\(\s*[\x22\x27](.*)[\x22\x27]\s*,\s*(\x22.*\x22|\x27.*\x27)\s*\)\s*;/i',
            '$$1 = $2;',
            $this->dbFile
        );
        $phpCode = str_replace(['<?php', '<?', '?>'], '', $phpCode);

        eval($phpCode);

        $dataSources = [];
        $dataSources['datasources'] = array(
            'workflow' => array(
                'connection' => $this->buildDsnString(
                    $DB_ADAPTER,
                    $DB_HOST,
                    $DB_NAME,
                    $DB_USER,
                    urlencode($DB_PASS)
                ),
                'adapter' => "mysql"
            ),
            'rbac' => array(
                'connection' => $this->buildDsnString(
                    $DB_ADAPTER,
                    $DB_RBAC_HOST,
                    $DB_RBAC_NAME,
                    $DB_RBAC_USER,
                    urlencode($DB_RBAC_PASS)
                ),
                'adapter' => "mysql"
            ),
            'report' => array(
                'connection' => $this->buildDsnString(
                    $DB_ADAPTER,
                    $DB_REPORT_HOST,
                    $DB_REPORT_NAME,
                    $DB_REPORT_USER,
                    urlencode($DB_REPORT_PASS)
                ),
                'adapter' => "mysql"
            )
        );
        return $dataSources;
    }

    /**
     * Builds the DSN string to be used by PROPEL
     * @param string $adapter
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $pass
     * @return string
     */
    private function buildDsnString($adapter, $host, $name, $user, $pass)
    {
        $dns = $adapter . "://" . $user . ":" . $pass . "@" . $host . "/" . $name;
        switch ($adapter) {
            case 'mysql':
                $dns .= '?encoding=utf8';
                break;
        }
        return $dns;
    }
}
