<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_FOLDER' table to 'workflow' DatabaseMap object.
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
class AppFolderMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppFolderMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_FOLDER');
        $tMap->setPhpName('AppFolder');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('FOLDER_UID', 'FolderUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('FOLDER_PARENT_UID', 'FolderParentUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('FOLDER_NAME', 'FolderName', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('FOLDER_CREATE_DATE', 'FolderCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('FOLDER_UPDATE_DATE', 'FolderUpdateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

    } // doBuild()

} // AppFolderMapBuilder
