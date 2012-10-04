<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DASHLET_INSTANCE' table to 'workflow' DatabaseMap object.
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
class DashletInstanceMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.DashletInstanceMapBuilder';

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

        $tMap = $this->dbMap->addTable('DASHLET_INSTANCE');
        $tMap->setPhpName('DashletInstance');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('DAS_INS_UID', 'DasInsUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DAS_UID', 'DasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DAS_INS_OWNER_TYPE', 'DasInsOwnerType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('DAS_INS_OWNER_UID', 'DasInsOwnerUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DAS_INS_ADDITIONAL_PROPERTIES', 'DasInsAdditionalProperties', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('DAS_INS_CREATE_DATE', 'DasInsCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DAS_INS_UPDATE_DATE', 'DasInsUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('DAS_INS_STATUS', 'DasInsStatus', 'int', CreoleTypes::TINYINT, true, null);

    } // doBuild()

} // DashletInstanceMapBuilder
