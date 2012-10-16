<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PROCESS' table to 'workflow' DatabaseMap object.
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
class ProcessMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ProcessMapBuilder';

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

        $tMap = $this->dbMap->addTable('PROCESS');
        $tMap->setPhpName('Process');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_PARENT', 'ProParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_TIME', 'ProTime', 'double', CreoleTypes::DOUBLE, true, null);

        $tMap->addColumn('PRO_TIMEUNIT', 'ProTimeunit', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('PRO_STATUS', 'ProStatus', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('PRO_TYPE_DAY', 'ProTypeDay', 'string', CreoleTypes::CHAR, true, 1);

        $tMap->addColumn('PRO_TYPE', 'ProType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('PRO_ASSIGNMENT', 'ProAssignment', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('PRO_SHOW_MAP', 'ProShowMap', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_SHOW_MESSAGE', 'ProShowMessage', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_SUBPROCESS', 'ProSubprocess', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_TRI_DELETED', 'ProTriDeleted', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_TRI_CANCELED', 'ProTriCanceled', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_TRI_PAUSED', 'ProTriPaused', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_TRI_REASSIGNED', 'ProTriReassigned', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_SHOW_DELEGATE', 'ProShowDelegate', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_SHOW_DYNAFORM', 'ProShowDynaform', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_CATEGORY', 'ProCategory', 'string', CreoleTypes::VARCHAR, true, 48);

        $tMap->addColumn('PRO_SUB_CATEGORY', 'ProSubCategory', 'string', CreoleTypes::VARCHAR, true, 48);

        $tMap->addColumn('PRO_INDUSTRY', 'ProIndustry', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_UPDATE_DATE', 'ProUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('PRO_CREATE_DATE', 'ProCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('PRO_CREATE_USER', 'ProCreateUser', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_HEIGHT', 'ProHeight', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_WIDTH', 'ProWidth', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_TITLE_X', 'ProTitleX', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_TITLE_Y', 'ProTitleY', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_DEBUG', 'ProDebug', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('PRO_DYNAFORMS', 'ProDynaforms', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('PRO_DERIVATION_SCREEN_TPL', 'ProDerivationScreenTpl', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addValidator('PRO_TIMEUNIT', 'validValues', 'propel.validator.ValidValuesValidator', 'WEEKS|MONTHS|DAYS|HOURS|MINUTES', 'Please select a valid Time Unit.');

        $tMap->addValidator('PRO_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|INACTIVE|DISABLED', 'Please select a valid Process Status.');

        $tMap->addValidator('PRO_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'NORMAL', 'Please select a valid Process Type.');

        $tMap->addValidator('PRO_ASSIGNMENT', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid Process Assignment');

    } // doBuild()

} // ProcessMapBuilder
