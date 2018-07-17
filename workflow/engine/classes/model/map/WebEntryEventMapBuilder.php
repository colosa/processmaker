<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'WEB_ENTRY_EVENT' table to 'workflow' DatabaseMap object.
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
class WebEntryEventMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.WebEntryEventMapBuilder';

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

        $tMap = $this->dbMap->addTable('WEB_ENTRY_EVENT');
        $tMap->setPhpName('WebEntryEvent');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('WEE_UID', 'WeeUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('WEE_TITLE', 'WeeTitle', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('WEE_DESCRIPTION', 'WeeDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PRJ_UID', 'PrjUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('EVN_UID', 'EvnUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('ACT_UID', 'ActUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DYN_UID', 'DynUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('WEE_STATUS', 'WeeStatus', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('WEE_WE_UID', 'WeeWeUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('WEE_WE_TAS_UID', 'WeeWeTasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addValidator('WEE_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ENABLED|DISABLED', 'Please enter a valid value for WEE_STATUS');

    } // doBuild()

} // WebEntryEventMapBuilder
