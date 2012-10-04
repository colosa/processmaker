<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CALENDAR_HOLIDAYS' table to 'workflow' DatabaseMap object.
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
class CalendarHolidaysMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CalendarHolidaysMapBuilder';

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

        $tMap = $this->dbMap->addTable('CALENDAR_HOLIDAYS');
        $tMap->setPhpName('CalendarHolidays');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CALENDAR_UID', 'CalendarUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('CALENDAR_HOLIDAY_NAME', 'CalendarHolidayName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('CALENDAR_HOLIDAY_START', 'CalendarHolidayStart', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('CALENDAR_HOLIDAY_END', 'CalendarHolidayEnd', 'int', CreoleTypes::TIMESTAMP, true, null);

    } // doBuild()

} // CalendarHolidaysMapBuilder
