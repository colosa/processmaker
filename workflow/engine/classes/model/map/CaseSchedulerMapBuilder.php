<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CASE_SCHEDULER' table to 'workflow' DatabaseMap object.
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
class CaseSchedulerMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CaseSchedulerMapBuilder';

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

        $tMap = $this->dbMap->addTable('CASE_SCHEDULER');
        $tMap->setPhpName('CaseScheduler');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('SCH_UID', 'SchUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SCH_DEL_USER_NAME', 'SchDelUserName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('SCH_DEL_USER_PASS', 'SchDelUserPass', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('SCH_DEL_USER_UID', 'SchDelUserUid', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('SCH_NAME', 'SchName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SCH_TIME_NEXT_RUN', 'SchTimeNextRun', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('SCH_LAST_RUN_TIME', 'SchLastRunTime', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('SCH_STATE', 'SchState', 'string', CreoleTypes::VARCHAR, true, 15);

        $tMap->addColumn('SCH_LAST_STATE', 'SchLastState', 'string', CreoleTypes::VARCHAR, true, 60);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SCH_OPTION', 'SchOption', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('SCH_START_TIME', 'SchStartTime', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('SCH_START_DATE', 'SchStartDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('SCH_DAYS_PERFORM_TASK', 'SchDaysPerformTask', 'string', CreoleTypes::CHAR, true, 5);

        $tMap->addColumn('SCH_EVERY_DAYS', 'SchEveryDays', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('SCH_WEEK_DAYS', 'SchWeekDays', 'string', CreoleTypes::CHAR, true, 14);

        $tMap->addColumn('SCH_START_DAY', 'SchStartDay', 'string', CreoleTypes::CHAR, true, 6);

        $tMap->addColumn('SCH_MONTHS', 'SchMonths', 'string', CreoleTypes::CHAR, true, 24);

        $tMap->addColumn('SCH_END_DATE', 'SchEndDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('SCH_REPEAT_EVERY', 'SchRepeatEvery', 'string', CreoleTypes::VARCHAR, true, 15);

        $tMap->addColumn('SCH_REPEAT_UNTIL', 'SchRepeatUntil', 'string', CreoleTypes::VARCHAR, true, 15);

        $tMap->addColumn('SCH_REPEAT_STOP_IF_RUNNING', 'SchRepeatStopIfRunning', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('CASE_SH_PLUGIN_UID', 'CaseShPluginUid', 'string', CreoleTypes::VARCHAR, false, 100);

    } // doBuild()

} // CaseSchedulerMapBuilder
