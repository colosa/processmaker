<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'GROUPWF' table to 'workflow' DatabaseMap object.
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
class GroupwfMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.GroupwfMapBuilder';

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

        $tMap = $this->dbMap->addTable('GROUPWF');
        $tMap->setPhpName('Groupwf');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('GRP_UID', 'GrpUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('GRP_STATUS', 'GrpStatus', 'string', CreoleTypes::CHAR, true, 8);

        $tMap->addColumn('GRP_LDAP_DN', 'GrpLdapDn', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('GRP_UX', 'GrpUx', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addValidator('GRP_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|INACTIVE', 'Please select a valid status.');

        $tMap->addValidator('GRP_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'Application Document UID is required.');

    } // doBuild()

} // GroupwfMapBuilder
