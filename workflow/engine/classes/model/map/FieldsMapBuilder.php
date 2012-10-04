<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'FIELDS' table to 'workflow' DatabaseMap object.
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
class FieldsMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.FieldsMapBuilder';

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

        $tMap = $this->dbMap->addTable('FIELDS');
        $tMap->setPhpName('Fields');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('FLD_UID', 'FldUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('ADD_TAB_UID', 'AddTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('FLD_INDEX', 'FldIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('FLD_NAME', 'FldName', 'string', CreoleTypes::VARCHAR, true, 60);

        $tMap->addColumn('FLD_DESCRIPTION', 'FldDescription', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('FLD_TYPE', 'FldType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('FLD_SIZE', 'FldSize', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('FLD_NULL', 'FldNull', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('FLD_AUTO_INCREMENT', 'FldAutoIncrement', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('FLD_KEY', 'FldKey', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('FLD_FOREIGN_KEY', 'FldForeignKey', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('FLD_FOREIGN_KEY_TABLE', 'FldForeignKeyTable', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('FLD_DYN_NAME', 'FldDynName', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addColumn('FLD_DYN_UID', 'FldDynUid', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addColumn('FLD_FILTER', 'FldFilter', 'int', CreoleTypes::TINYINT, false, null);

    } // doBuild()

} // FieldsMapBuilder
