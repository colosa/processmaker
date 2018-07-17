<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EMAIL_EVENT' table to 'workflow' DatabaseMap object.
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
class EmailEventMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.EmailEventMapBuilder';

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

        $tMap = $this->dbMap->addTable('EMAIL_EVENT');
        $tMap->setPhpName('EmailEvent');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('EMAIL_EVENT_UID', 'EmailEventUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRJ_UID', 'PrjUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('EVN_UID', 'EvnUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('EMAIL_EVENT_FROM', 'EmailEventFrom', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('EMAIL_EVENT_TO', 'EmailEventTo', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('EMAIL_EVENT_SUBJECT', 'EmailEventSubject', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('PRF_UID', 'PrfUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('EMAIL_SERVER_UID', 'EmailServerUid', 'string', CreoleTypes::VARCHAR, false, 32);

    } // doBuild()

} // EmailEventMapBuilder
