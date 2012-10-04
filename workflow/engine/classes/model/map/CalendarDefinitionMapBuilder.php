<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CALENDAR_DEFINITION' table to 'workflow' DatabaseMap object.
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
class CalendarDefinitionMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CalendarDefinitionMapBuilder';

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

        $tMap = $this->dbMap->addTable('CALENDAR_DEFINITION');
        $tMap->setPhpName('CalendarDefinition');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CALENDAR_UID', 'CalendarUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CALENDAR_NAME', 'CalendarName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('CALENDAR_CREATE_DATE', 'CalendarCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('CALENDAR_UPDATE_DATE', 'CalendarUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('CALENDAR_WORK_DAYS', 'CalendarWorkDays', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('CALENDAR_DESCRIPTION', 'CalendarDescription', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('CALENDAR_STATUS', 'CalendarStatus', 'string', CreoleTypes::VARCHAR, true, 8);

        $tMap->addValidator('CALENDAR_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|INACTIVE|DELETED', 'Please select a valid Calendar Status.');

    } // doBuild()

} // CalendarDefinitionMapBuilder
