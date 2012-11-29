<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DASHLET' table to 'workflow' DatabaseMap object.
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
class DashletMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.DashletMapBuilder';

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

        $tMap = $this->dbMap->addTable('DASHLET');
        $tMap->setPhpName('Dashlet');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('DAS_UID', 'DasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DAS_CLASS', 'DasClass', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('DAS_TITLE', 'DasTitle', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('DAS_DESCRIPTION', 'DasDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('DAS_VERSION', 'DasVersion', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('DAS_CREATE_DATE', 'DasCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DAS_UPDATE_DATE', 'DasUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('DAS_STATUS', 'DasStatus', 'int', CreoleTypes::TINYINT, true, null);

    } // doBuild()

} // DashletMapBuilder
