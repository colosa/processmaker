<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_DELAY' table to 'workflow' DatabaseMap object.
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
class AppDelayMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppDelayMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_DELAY');
        $tMap->setPhpName('AppDelay');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_DELAY_UID', 'AppDelayUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_NUMBER', 'AppNumber', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('APP_THREAD_INDEX', 'AppThreadIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_DEL_INDEX', 'AppDelIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_TYPE', 'AppType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('APP_STATUS', 'AppStatus', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('APP_NEXT_TASK', 'AppNextTask', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('APP_DELEGATION_USER', 'AppDelegationUser', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('APP_ENABLE_ACTION_USER', 'AppEnableActionUser', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_ENABLE_ACTION_DATE', 'AppEnableActionDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_DISABLE_ACTION_USER', 'AppDisableActionUser', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('APP_DISABLE_ACTION_DATE', 'AppDisableActionDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_AUTOMATIC_DISABLED_DATE', 'AppAutomaticDisabledDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_DELEGATION_USER_ID', 'AppDelegationUserId', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('PRO_ID', 'ProId', 'int', CreoleTypes::INTEGER, false, null);

    } // doBuild()

} // AppDelayMapBuilder
