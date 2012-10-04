<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CALENDAR_BUSINESS_HOURS' table to 'workflow' DatabaseMap object.
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
class CalendarBusinessHoursMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CalendarBusinessHoursMapBuilder';

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

        $tMap = $this->dbMap->addTable('CALENDAR_BUSINESS_HOURS');
        $tMap->setPhpName('CalendarBusinessHours');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CALENDAR_UID', 'CalendarUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('CALENDAR_BUSINESS_DAY', 'CalendarBusinessDay', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addPrimaryKey('CALENDAR_BUSINESS_START', 'CalendarBusinessStart', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addPrimaryKey('CALENDAR_BUSINESS_END', 'CalendarBusinessEnd', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addValidator('CALENDAR_BUSINESS_DAY', 'validValues', 'propel.validator.ValidValuesValidator', '0|1|2|3|4|5|6|7', 'Please select a valid Day.');

    } // doBuild()

} // CalendarBusinessHoursMapBuilder
