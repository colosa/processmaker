<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CASE_TRACKER' table to 'workflow' DatabaseMap object.
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
class CaseTrackerMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CaseTrackerMapBuilder';

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

        $tMap = $this->dbMap->addTable('CASE_TRACKER');
        $tMap->setPhpName('CaseTracker');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CT_MAP_TYPE', 'CtMapType', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('CT_DERIVATION_HISTORY', 'CtDerivationHistory', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('CT_MESSAGE_HISTORY', 'CtMessageHistory', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

        $tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

        $tMap->addValidator('CT_MAP_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'NONE|PROCESSMAP|STAGES', 'Please select a valid map type.');

        $tMap->addValidator('CT_MAP_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Map type is required.');

        $tMap->addValidator('CT_DERIVATION_HISTORY', 'validValues', 'propel.validator.ValidValuesValidator', '0|1', 'Please select a valid derivation history status.');

        $tMap->addValidator('CT_DERIVATION_HISTORY', 'required', 'propel.validator.RequiredValidator', '', 'Derivation history status is required.');

        $tMap->addValidator('CT_MESSAGE_HISTORY', 'validValues', 'propel.validator.ValidValuesValidator', '0|1', 'Please select a valid message history status.');

        $tMap->addValidator('CT_MESSAGE_HISTORY', 'required', 'propel.validator.RequiredValidator', '', 'Message history status is required.');

    } // doBuild()

} // CaseTrackerMapBuilder
