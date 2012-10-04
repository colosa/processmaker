<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DIM_TIME_DELEGATE' table to 'workflow' DatabaseMap object.
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
class DimTimeDelegateMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.DimTimeDelegateMapBuilder';

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

        $tMap = $this->dbMap->addTable('DIM_TIME_DELEGATE');
        $tMap->setPhpName('DimTimeDelegate');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('TIME_ID', 'TimeId', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('MONTH_ID', 'MonthId', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('QTR_ID', 'QtrId', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('YEAR_ID', 'YearId', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('MONTH_NAME', 'MonthName', 'string', CreoleTypes::VARCHAR, true, 3);

        $tMap->addColumn('MONTH_DESC', 'MonthDesc', 'string', CreoleTypes::VARCHAR, true, 9);

        $tMap->addColumn('QTR_NAME', 'QtrName', 'string', CreoleTypes::VARCHAR, true, 4);

        $tMap->addColumn('QTR_DESC', 'QtrDesc', 'string', CreoleTypes::VARCHAR, true, 9);

    } // doBuild()

} // DimTimeDelegateMapBuilder
