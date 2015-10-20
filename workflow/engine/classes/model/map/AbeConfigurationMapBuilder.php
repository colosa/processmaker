<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ABE_CONFIGURATION' table to 'workflow' DatabaseMap object.
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
class AbeConfigurationMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AbeConfigurationMapBuilder';

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

        $tMap = $this->dbMap->addTable('ABE_CONFIGURATION');
        $tMap->setPhpName('AbeConfiguration');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('ABE_UID', 'AbeUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('ABE_TYPE', 'AbeType', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('ABE_TEMPLATE', 'AbeTemplate', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('ABE_DYN_TYPE', 'AbeDynType', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('DYN_UID', 'DynUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('ABE_EMAIL_FIELD', 'AbeEmailField', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('ABE_ACTION_FIELD', 'AbeActionField', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('ABE_CASE_NOTE_IN_RESPONSE', 'AbeCaseNoteInResponse', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('ABE_CREATE_DATE', 'AbeCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('ABE_UPDATE_DATE', 'AbeUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('ABE_SUBJECT_FIELD', 'AbeSubjectField', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('ABE_MAILSERVER_OR_MAILCURRENT', 'AbeMailserverOrMailcurrent', 'int', CreoleTypes::INTEGER, false, null);

    } // doBuild()

} // AbeConfigurationMapBuilder
