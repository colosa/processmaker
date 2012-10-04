<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'OBJECT_PERMISSION' table to 'workflow' DatabaseMap object.
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
class ObjectPermissionMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ObjectPermissionMapBuilder';

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

        $tMap = $this->dbMap->addTable('OBJECT_PERMISSION');
        $tMap->setPhpName('ObjectPermission');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('OP_UID', 'OpUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('OP_USER_RELATION', 'OpUserRelation', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('OP_TASK_SOURCE', 'OpTaskSource', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('OP_PARTICIPATE', 'OpParticipate', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('OP_OBJ_TYPE', 'OpObjType', 'string', CreoleTypes::VARCHAR, true, 15);

        $tMap->addColumn('OP_OBJ_UID', 'OpObjUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('OP_ACTION', 'OpAction', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('OP_CASE_STATUS', 'OpCaseStatus', 'string', CreoleTypes::VARCHAR, false, 10);

        $tMap->addValidator('OP_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Object permission UID can be no larger than 32 in size');

        $tMap->addValidator('OP_UID', 'required', 'propel.validator.RequiredValidator', '', 'Object permission UID is required.');

        $tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

        $tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

        $tMap->addValidator('TAS_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Task UID can be no larger than 32 in size');

        $tMap->addValidator('TAS_UID', 'required', 'propel.validator.RequiredValidator', '', 'Task UID is required.');

        $tMap->addValidator('USR_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'User or Group UID can be no larger than 32 in size');

        $tMap->addValidator('USR_UID', 'required', 'propel.validator.RequiredValidator', '', 'User or Group UID is required.');

        $tMap->addValidator('OP_USER_RELATION', 'validValues', 'propel.validator.ValidValuesValidator', '1|2', 'Please select a valid relation.');

        $tMap->addValidator('OP_USER_RELATION', 'required', 'propel.validator.RequiredValidator', '', 'Relation is required.');

        $tMap->addValidator('OP_TASK_SOURCE', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Source task UID can be no larger than 32 in size');

        $tMap->addValidator('OP_TASK_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'Source task is required.');

        $tMap->addValidator('OP_PARTICIPATE', 'validValues', 'propel.validator.ValidValuesValidator', '0|1', 'Please select a valid participation value.');

        $tMap->addValidator('OP_PARTICIPATE', 'required', 'propel.validator.RequiredValidator', '', 'Participation is required.');

        $tMap->addValidator('OP_OBJ_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '15', 'Object type can be no larger than 15 in size');

        $tMap->addValidator('OP_OBJ_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Object type is required.');

        $tMap->addValidator('OP_OBJ_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Object UID can be no larger than 32 in size');

        $tMap->addValidator('OP_OBJ_UID', 'required', 'propel.validator.RequiredValidator', '', 'Object UID is required.');

        $tMap->addValidator('OP_ACTION', 'maxLength', 'propel.validator.MaxLengthValidator', '15', 'Action can be no larger than 15 in size');

        $tMap->addValidator('OP_ACTION', 'required', 'propel.validator.RequiredValidator', '', 'Action is required.');

    } // doBuild()

} // ObjectPermissionMapBuilder
