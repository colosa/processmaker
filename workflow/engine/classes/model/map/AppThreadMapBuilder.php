<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_THREAD' table to 'workflow' DatabaseMap object.
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
class AppThreadMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppThreadMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_THREAD');
        $tMap->setPhpName('AppThread');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('APP_THREAD_INDEX', 'AppThreadIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_THREAD_PARENT', 'AppThreadParent', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_THREAD_STATUS', 'AppThreadStatus', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DEL_INDEX', 'DelIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addValidator('APP_THREAD_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'CLOSED|OPEN', 'Please select a valid status.');

    } // doBuild()

} // AppThreadMapBuilder
