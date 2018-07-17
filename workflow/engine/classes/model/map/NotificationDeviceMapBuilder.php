<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'NOTIFICATION_DEVICE' table to 'workflow' DatabaseMap object.
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
class NotificationDeviceMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.NotificationDeviceMapBuilder';

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

        $tMap = $this->dbMap->addTable('NOTIFICATION_DEVICE');
        $tMap->setPhpName('NotificationDevice');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('DEV_UID', 'DevUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SYS_LANG', 'SysLang', 'string', CreoleTypes::VARCHAR, false, 10);

        $tMap->addColumn('DEV_REG_ID', 'DevRegId', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('DEV_TYPE', 'DevType', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('DEV_CREATE', 'DevCreate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DEV_UPDATE', 'DevUpdate', 'int', CreoleTypes::TIMESTAMP, true, null);

    } // doBuild()

} // NotificationDeviceMapBuilder
