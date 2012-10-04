<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'REPORT_TABLE' table to 'workflow' DatabaseMap object.
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
class ReportTableMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ReportTableMapBuilder';

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

        $tMap = $this->dbMap->addTable('REPORT_TABLE');
        $tMap->setPhpName('ReportTable');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('REP_TAB_UID', 'RepTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('REP_TAB_NAME', 'RepTabName', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('REP_TAB_TYPE', 'RepTabType', 'string', CreoleTypes::VARCHAR, true, 6);

        $tMap->addColumn('REP_TAB_GRID', 'RepTabGrid', 'string', CreoleTypes::VARCHAR, false, 150);

        $tMap->addColumn('REP_TAB_CONNECTION', 'RepTabConnection', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('REP_TAB_CREATE_DATE', 'RepTabCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('REP_TAB_STATUS', 'RepTabStatus', 'string', CreoleTypes::CHAR, true, 8);

        $tMap->addValidator('REP_TAB_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Report table UID can be no larger than 32 in size');

        $tMap->addValidator('REP_TAB_UID', 'required', 'propel.validator.RequiredValidator', '', 'Report table UID is required.');

        $tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

        $tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

        $tMap->addValidator('REP_TAB_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '100', 'Report table name can be no larger than 100 in size');

        $tMap->addValidator('REP_TAB_NAME', 'required', 'propel.validator.RequiredValidator', '', 'Report table name is required.');

        $tMap->addValidator('REP_TAB_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'NORMAL|GRID', 'Please select a valid type.');

        $tMap->addValidator('REP_TAB_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Report table type is required.');

        $tMap->addValidator('REP_TAB_CONNECTION', 'maxLength', 'propel.validator.MaxLengthValidator', '10', 'Report table connection can be no larger than 10 in size');

        $tMap->addValidator('REP_TAB_CONNECTION', 'required', 'propel.validator.RequiredValidator', '', 'Report table connection is required.');

        $tMap->addValidator('REP_TAB_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|INACTIVE', 'Please select a valid status.');

        $tMap->addValidator('REP_TAB_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'Report table status is required.');

    } // doBuild()

} // ReportTableMapBuilder
