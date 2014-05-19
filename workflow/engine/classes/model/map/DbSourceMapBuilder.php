<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DB_SOURCE' table to 'workflow' DatabaseMap object.
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
class DbSourceMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.DbSourceMapBuilder';

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

        $tMap = $this->dbMap->addTable('DB_SOURCE');
        $tMap->setPhpName('DbSource');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('DBS_UID', 'DbsUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DBS_TYPE', 'DbsType', 'string', CreoleTypes::VARCHAR, true, 8);

        $tMap->addColumn('DBS_SERVER', 'DbsServer', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('DBS_DATABASE_NAME', 'DbsDatabaseName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('DBS_USERNAME', 'DbsUsername', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DBS_PASSWORD', 'DbsPassword', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DBS_PORT', 'DbsPort', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('DBS_ENCODE', 'DbsEncode', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DBS_CONNECTION_TYPE', 'DbsConnectionType', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DBS_TNS', 'DbsTns', 'string', CreoleTypes::VARCHAR, false, 256);

    } // doBuild()

} // DbSourceMapBuilder
