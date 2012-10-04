<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TASK_USER' table to 'workflow' DatabaseMap object.
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
class TaskUserMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.TaskUserMapBuilder';

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

        $tMap = $this->dbMap->addTable('TASK_USER');
        $tMap->setPhpName('TaskUser');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('TU_TYPE', 'TuType', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addPrimaryKey('TU_RELATION', 'TuRelation', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addValidator('TAS_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Task UID can be no larger than 32 in size');

        $tMap->addValidator('TAS_UID', 'required', 'propel.validator.RequiredValidator', '', 'Task UID is required.');

        $tMap->addValidator('USR_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'User UID can be no larger than 32 in size');

        $tMap->addValidator('USR_UID', 'required', 'propel.validator.RequiredValidator', '', 'User UID is required.');

        $tMap->addValidator('TU_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', '1|2', 'Please select a valid type.');

        $tMap->addValidator('TU_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Type is required.');

        $tMap->addValidator('TU_RELATION', 'validValues', 'propel.validator.ValidValuesValidator', '1|2', 'Please select a valid relation.');

        $tMap->addValidator('TU_RELATION', 'required', 'propel.validator.RequiredValidator', '', 'Relation is required.');

    } // doBuild()

} // TaskUserMapBuilder
