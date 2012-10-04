<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'FIELD_CONDITION' table to 'workflow' DatabaseMap object.
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
class FieldConditionMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.FieldConditionMapBuilder';

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

        $tMap = $this->dbMap->addTable('FIELD_CONDITION');
        $tMap->setPhpName('FieldCondition');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('FCD_UID', 'FcdUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('FCD_FUNCTION', 'FcdFunction', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('FCD_FIELDS', 'FcdFields', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('FCD_CONDITION', 'FcdCondition', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('FCD_EVENTS', 'FcdEvents', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('FCD_EVENT_OWNERS', 'FcdEventOwners', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('FCD_STATUS', 'FcdStatus', 'string', CreoleTypes::VARCHAR, false, 10);

        $tMap->addColumn('FCD_DYN_UID', 'FcdDynUid', 'string', CreoleTypes::VARCHAR, true, 32);

    } // doBuild()

} // FieldConditionMapBuilder
