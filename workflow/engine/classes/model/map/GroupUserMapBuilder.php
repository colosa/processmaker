<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'GROUP_USER' table to 'workflow' DatabaseMap object.
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
class GroupUserMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.GroupUserMapBuilder';

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

        $tMap = $this->dbMap->addTable('GROUP_USER');
        $tMap->setPhpName('GroupUser');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('GRP_UID', 'GrpUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addValidator('GRP_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Group UID can be no larger than 32 in size');

        $tMap->addValidator('GRP_UID', 'required', 'propel.validator.RequiredValidator', '', 'Group UID is required.');

        $tMap->addValidator('USR_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'User UID can be no larger than 32 in size');

        $tMap->addValidator('USR_UID', 'required', 'propel.validator.RequiredValidator', '', 'User UID is required.');

    } // doBuild()

} // GroupUserMapBuilder
