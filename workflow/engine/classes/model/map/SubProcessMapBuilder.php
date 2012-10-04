<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SUB_PROCESS' table to 'workflow' DatabaseMap object.
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
class SubProcessMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.SubProcessMapBuilder';

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

        $tMap = $this->dbMap->addTable('SUB_PROCESS');
        $tMap->setPhpName('SubProcess');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('SP_UID', 'SpUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_PARENT', 'ProParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_PARENT', 'TasParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SP_TYPE', 'SpType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('SP_SYNCHRONOUS', 'SpSynchronous', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SP_SYNCHRONOUS_TYPE', 'SpSynchronousType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('SP_SYNCHRONOUS_WAIT', 'SpSynchronousWait', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SP_VARIABLES_OUT', 'SpVariablesOut', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('SP_VARIABLES_IN', 'SpVariablesIn', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('SP_GRID_IN', 'SpGridIn', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addValidator('SP_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'SIMPLE|MULTIPLE', 'Please select a valid value for SP_TYPE.');

        $tMap->addValidator('SP_SYNCHRONOUS', 'validValues', 'propel.validator.ValidValuesValidator', '1|0', 'Please select a valid value for SP_SYNCHRONOUS.');

        $tMap->addValidator('SP_SYNCHRONOUS_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'ALL|INSTANCES|TIME', 'Please select a valid value for SP_SYNCHRONOUS_TYPE.');

    } // doBuild()

} // SubProcessMapBuilder
