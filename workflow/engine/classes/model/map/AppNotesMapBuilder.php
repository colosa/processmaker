<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_NOTES' table to 'workflow' DatabaseMap object.
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
class AppNotesMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppNotesMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_NOTES');
        $tMap->setPhpName('AppNotes');

        $tMap->setUseIdGenerator(false);

        $tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('NOTE_DATE', 'NoteDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('NOTE_CONTENT', 'NoteContent', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('NOTE_TYPE', 'NoteType', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('NOTE_AVAILABILITY', 'NoteAvailability', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('NOTE_ORIGIN_OBJ', 'NoteOriginObj', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('NOTE_AFFECTED_OBJ1', 'NoteAffectedObj1', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('NOTE_AFFECTED_OBJ2', 'NoteAffectedObj2', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('NOTE_RECIPIENTS', 'NoteRecipients', 'string', CreoleTypes::LONGVARCHAR, false, null);

    } // doBuild()

} // AppNotesMapBuilder
