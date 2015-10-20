<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PROCESS_VARIABLES' table to 'workflow' DatabaseMap object.
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
class ProcessVariablesMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ProcessVariablesMapBuilder';

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

        $tMap = $this->dbMap->addTable('PROCESS_VARIABLES');
        $tMap->setPhpName('ProcessVariables');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('VAR_UID', 'VarUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRJ_UID', 'PrjUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('VAR_NAME', 'VarName', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('VAR_FIELD_TYPE', 'VarFieldType', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('VAR_FIELD_SIZE', 'VarFieldSize', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('VAR_LABEL', 'VarLabel', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('VAR_DBCONNECTION', 'VarDbconnection', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('VAR_SQL', 'VarSql', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('VAR_NULL', 'VarNull', 'int', CreoleTypes::TINYINT, false, 32);

        $tMap->addColumn('VAR_DEFAULT', 'VarDefault', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('VAR_ACCEPTED_VALUES', 'VarAcceptedValues', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('INP_DOC_UID', 'InpDocUid', 'string', CreoleTypes::VARCHAR, false, 32);

    } // doBuild()

} // ProcessVariablesMapBuilder
