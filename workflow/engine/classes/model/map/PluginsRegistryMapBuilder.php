<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PLUGINS_REGISTRY' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    workflow.classes.model.map
 */
class PluginsRegistryMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.PluginsRegistryMapBuilder';

    /**
     * The database map.
     */
    private $dbMap;

    /**
     * Tells us if this DatabaseMapBuilder is built so that we
     * don't have to re-build it every time.
     *
     * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
     */
    public function isBuilt()
    {
        return ($this->dbMap !== null);
    }

    /**
     * Gets the databasemap this map builder built.
     *
     * @return     the databasemap
     */
    public function getDatabaseMap()
    {
        return $this->dbMap;
    }

    /**
     * The doBuild() method builds the DatabaseMap
     *
     * @return     void
     * @throws     PropelException
     */
    public function doBuild()
    {
        $this->dbMap = Propel::getDatabaseMap('workflow');

        $tMap = $this->dbMap->addTable('PLUGINS_REGISTRY');
        $tMap->setPhpName('PluginsRegistry');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('PR_UID', 'PrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PLUGIN_NAMESPACE', 'PluginNamespace', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('PLUGIN_DESCRIPTION', 'PluginDescription', 'string', CreoleTypes::VARCHAR, false, 200);

        $tMap->addColumn('PLUGIN_CLASS_NAME', 'PluginClassName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('PLUGIN_FRIENDLY_NAME', 'PluginFriendlyName', 'string', CreoleTypes::VARCHAR, false, 150);

        $tMap->addColumn('PLUGIN_FILE', 'PluginFile', 'string', CreoleTypes::VARCHAR, true, 250);

        $tMap->addColumn('PLUGIN_FOLDER', 'PluginFolder', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('PLUGIN_SETUP_PAGE', 'PluginSetupPage', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('PLUGIN_COMPANY_LOGO', 'PluginCompanyLogo', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('PLUGIN_WORKSPACES', 'PluginWorkspaces', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('PLUGIN_VERSION', 'PluginVersion', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('PLUGIN_ENABLE', 'PluginEnable', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('PLUGIN_PRIVATE', 'PluginPrivate', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('PLUGIN_MENUS', 'PluginMenus', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_FOLDERS', 'PluginFolders', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_TRIGGERS', 'PluginTriggers', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_PM_FUNCTIONS', 'PluginPmFunctions', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_REDIRECT_LOGIN', 'PluginRedirectLogin', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_STEPS', 'PluginSteps', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_CSS', 'PluginCss', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_JS', 'PluginJs', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_REST_SERVICE', 'PluginRestService', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_TASK_EXTENDED_PROPERTIES', 'PluginTaskExtendedProperties', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PLUGIN_ATTRIBUTES', 'PluginAttributes', 'string', CreoleTypes::LONGVARCHAR, false, null);

    } // doBuild()

} // PluginsRegistryMapBuilder
