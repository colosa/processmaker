<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ADDITIONAL_TABLES' table to 'workflow' DatabaseMap object.
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
class AdditionalTablesMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AdditionalTablesMapBuilder';

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

        $tMap = $this->dbMap->addTable('ADDITIONAL_TABLES');
        $tMap->setPhpName('AdditionalTables');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('ADD_TAB_UID', 'AddTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('ADD_TAB_NAME', 'AddTabName', 'string', CreoleTypes::VARCHAR, true, 60);

        $tMap->addColumn('ADD_TAB_CLASS_NAME', 'AddTabClassName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('ADD_TAB_DESCRIPTION', 'AddTabDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('ADD_TAB_SDW_LOG_INSERT', 'AddTabSdwLogInsert', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('ADD_TAB_SDW_LOG_UPDATE', 'AddTabSdwLogUpdate', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('ADD_TAB_SDW_LOG_DELETE', 'AddTabSdwLogDelete', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('ADD_TAB_SDW_LOG_SELECT', 'AddTabSdwLogSelect', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('ADD_TAB_SDW_MAX_LENGTH', 'AddTabSdwMaxLength', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('ADD_TAB_SDW_AUTO_DELETE', 'AddTabSdwAutoDelete', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('ADD_TAB_PLG_UID', 'AddTabPlgUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DBS_UID', 'DbsUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('ADD_TAB_TYPE', 'AddTabType', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('ADD_TAB_GRID', 'AddTabGrid', 'string', CreoleTypes::VARCHAR, false, 256);

        $tMap->addColumn('ADD_TAB_TAG', 'AddTabTag', 'string', CreoleTypes::VARCHAR, false, 256);

    } // doBuild()

} // AdditionalTablesMapBuilder
