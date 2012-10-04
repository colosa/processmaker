<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SWIMLANES_ELEMENTS' table to 'workflow' DatabaseMap object.
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
class SwimlanesElementsMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.SwimlanesElementsMapBuilder';

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

        $tMap = $this->dbMap->addTable('SWIMLANES_ELEMENTS');
        $tMap->setPhpName('SwimlanesElements');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('SWI_UID', 'SwiUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SWI_TYPE', 'SwiType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('SWI_X', 'SwiX', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SWI_Y', 'SwiY', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SWI_WIDTH', 'SwiWidth', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SWI_HEIGHT', 'SwiHeight', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SWI_NEXT_UID', 'SwiNextUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addValidator('SWI_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Swimlane Element UID can be no larger than 32 in size');

        $tMap->addValidator('SWI_UID', 'required', 'propel.validator.RequiredValidator', '', 'Swimlane Element UID is required.');

        $tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

        $tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

        $tMap->addValidator('SWI_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'LINE|TEXT', 'Please select a valid Swimlane Element type.');

        $tMap->addValidator('SWI_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Swimlane Element type is required.');

    } // doBuild()

} // SwimlanesElementsMapBuilder
