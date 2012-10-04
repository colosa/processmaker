<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SESSION' table to 'workflow' DatabaseMap object.
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
class SessionMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.SessionMapBuilder';

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

        $tMap = $this->dbMap->addTable('SESSION');
        $tMap->setPhpName('Session');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('SES_UID', 'SesUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SES_STATUS', 'SesStatus', 'string', CreoleTypes::VARCHAR, true, 16);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SES_REMOTE_IP', 'SesRemoteIp', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SES_INIT_DATE', 'SesInitDate', 'string', CreoleTypes::VARCHAR, true, 19);

        $tMap->addColumn('SES_DUE_DATE', 'SesDueDate', 'string', CreoleTypes::VARCHAR, true, 19);

        $tMap->addColumn('SES_END_DATE', 'SesEndDate', 'string', CreoleTypes::VARCHAR, true, 19);

    } // doBuild()

} // SessionMapBuilder
