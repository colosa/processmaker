<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SUB_APPLICATION' table to 'workflow' DatabaseMap object.
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
class SubApplicationMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.SubApplicationMapBuilder';

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

        $tMap = $this->dbMap->addTable('SUB_APPLICATION');
        $tMap->setPhpName('SubApplication');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('APP_PARENT', 'AppParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('DEL_INDEX_PARENT', 'DelIndexParent', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addPrimaryKey('DEL_THREAD_PARENT', 'DelThreadParent', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SA_STATUS', 'SaStatus', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SA_VALUES_OUT', 'SaValuesOut', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('SA_VALUES_IN', 'SaValuesIn', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('SA_INIT_DATE', 'SaInitDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('SA_FINISH_DATE', 'SaFinishDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addValidator('SA_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|FINISHED|CANCELLED', 'Please select a valid value for SA_STATUS.');

    } // doBuild()

} // SubApplicationMapBuilder
