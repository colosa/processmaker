<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CASE_TRACKER_OBJECT' table to 'workflow' DatabaseMap object.
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
class CaseTrackerObjectMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CaseTrackerObjectMapBuilder';

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

        $tMap = $this->dbMap->addTable('CASE_TRACKER_OBJECT');
        $tMap->setPhpName('CaseTrackerObject');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CTO_UID', 'CtoUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CTO_TYPE_OBJ', 'CtoTypeObj', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('CTO_UID_OBJ', 'CtoUidObj', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CTO_CONDITION', 'CtoCondition', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('CTO_POSITION', 'CtoPosition', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addValidator('CTO_TYPE_OBJ', 'validValues', 'propel.validator.ValidValuesValidator', 'DYNAFORM|INPUT_DOCUMENT|OUTPUT_DOCUMENT|MESSAGE|EXTERNAL', 'Please select a valid value for CTO_TYPE_OBJ.');

    } // doBuild()

} // CaseTrackerObjectMapBuilder
