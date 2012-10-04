<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'GATEWAY' table to 'workflow' DatabaseMap object.
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
class GatewayMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.GatewayMapBuilder';

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

        $tMap = $this->dbMap->addTable('GATEWAY');
        $tMap->setPhpName('Gateway');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('GAT_UID', 'GatUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('GAT_NEXT_TASK', 'GatNextTask', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('GAT_X', 'GatX', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('GAT_Y', 'GatY', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('GAT_TYPE', 'GatType', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addValidator('GAT_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Gateway UID can be no larger than 32 in size');

        $tMap->addValidator('GAT_UID', 'required', 'propel.validator.RequiredValidator', '', 'Gateway Element UID is required.');

        $tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

        $tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

    } // doBuild()

} // GatewayMapBuilder
