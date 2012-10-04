<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'LOG_CASES_SCHEDULER' table to 'workflow' DatabaseMap object.
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
class LogCasesSchedulerMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.LogCasesSchedulerMapBuilder';

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

        $tMap = $this->dbMap->addTable('LOG_CASES_SCHEDULER');
        $tMap->setPhpName('LogCasesScheduler');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('LOG_CASE_UID', 'LogCaseUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_NAME', 'UsrName', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('EXEC_DATE', 'ExecDate', 'int', CreoleTypes::DATE, true, null);

        $tMap->addColumn('EXEC_HOUR', 'ExecHour', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('RESULT', 'Result', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SCH_UID', 'SchUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('WS_CREATE_CASE_STATUS', 'WsCreateCaseStatus', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('WS_ROUTE_CASE_STATUS', 'WsRouteCaseStatus', 'string', CreoleTypes::LONGVARCHAR, true, null);

    } // doBuild()

} // LogCasesSchedulerMapBuilder
