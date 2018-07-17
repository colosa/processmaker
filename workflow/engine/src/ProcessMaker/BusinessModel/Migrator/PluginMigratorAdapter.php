<?php
namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Plugins\PluginRegistry;

/**
 * Class PluginMigratorAdapter
 * @package ProcessMaker\BusinessModel\Migrator
 */
class PluginMigratorAdapter implements  Exportable, Importable
{

    private $migrator;

    /**
     * PluginMigratorAdapter constructor.
     */
    public function __construct($pluginName)
    {
        $registry = PluginRegistry::loadSingleton();
        $plugin = $registry->getPluginByCode($pluginName);
        require_once (
            PATH_PLUGINS.PATH_SEP.
            $plugin->sPluginFolder.PATH_SEP.
            'classes'.PATH_SEP.
            $plugin->sMigratorClassName.'.php'
        );
        $this->migrator = new $plugin->sMigratorClassName();
    }

    public function beforeExport()
    {
        return $this->migrator->beforeExport();
    }

    public function export($prj_uid)
    {
        $data = $this->migrator->export($prj_uid);
        foreach ($data['plugin-data'] as $key => $plugin) {
            $newKey = str_replace("MIGRATOR", "", strtoupper(get_class($this->migrator))).'.'.$key;
            $data['plugin-data'][$newKey] = $plugin;
            unset($data['plugin-data'][$key]);
        }
        return $data;
    }

    public function afterExport()
    {
        return $this->migrator->afterExport();
    }

    public function beforeImport($data)
    {
        return $this->migrator->beforeImport($data);
    }

    public function import($data, $replace)
    {
        return $this->migrator->import($data, $replace);
    }

    public function afterImport($data)
    {
        return $this->migrator->afterImport($data);
    }

}