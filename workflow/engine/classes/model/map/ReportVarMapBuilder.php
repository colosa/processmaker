<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'REPORT_VAR' table to 'workflow' DatabaseMap object.
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
class ReportVarMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ReportVarMapBuilder';

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

        $tMap = $this->dbMap->addTable('REPORT_VAR');
        $tMap->setPhpName('ReportVar');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('REP_VAR_UID', 'RepVarUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('REP_TAB_UID', 'RepTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('REP_VAR_NAME', 'RepVarName', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('REP_VAR_TYPE', 'RepVarType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addValidator('REP_VAR_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Report variable UID can be no larger than 32 in size');

        $tMap->addValidator('REP_VAR_UID', 'required', 'propel.validator.RequiredValidator', '', 'Report variable UID is required.');

        $tMap->addValidator('REP_TAB_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Report table UID can be no larger than 32 in size');

        $tMap->addValidator('REP_TAB_UID', 'required', 'propel.validator.RequiredValidator', '', 'Report variable UID is required.');

        $tMap->addValidator('REP_VAR_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '255', 'Report variable name can be no larger than 255 in size');

        $tMap->addValidator('REP_VAR_NAME', 'required', 'propel.validator.RequiredValidator', '', 'Report variable name is required.');

        $tMap->addValidator('REP_VAR_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '20', 'Report variable type can be no larger than 20 in size');

        $tMap->addValidator('REP_VAR_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Report variable type is required.');

    } // doBuild()

} // ReportVarMapBuilder
