<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'LIST_PAUSED' table to 'workflow' DatabaseMap object.
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
class ListPausedMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ListPausedMapBuilder';

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

        $tMap = $this->dbMap->addTable('LIST_PAUSED');
        $tMap->setPhpName('ListPaused');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('DEL_INDEX', 'DelIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_NUMBER', 'AppNumber', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_TITLE', 'AppTitle', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('APP_PRO_TITLE', 'AppProTitle', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('APP_TAS_TITLE', 'AppTasTitle', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('APP_PAUSED_DATE', 'AppPausedDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_RESTART_DATE', 'AppRestartDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DEL_PREVIOUS_USR_UID', 'DelPreviousUsrUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DEL_PREVIOUS_USR_USERNAME', 'DelPreviousUsrUsername', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('DEL_PREVIOUS_USR_FIRSTNAME', 'DelPreviousUsrFirstname', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('DEL_PREVIOUS_USR_LASTNAME', 'DelPreviousUsrLastname', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('DEL_CURRENT_USR_USERNAME', 'DelCurrentUsrUsername', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('DEL_CURRENT_USR_FIRSTNAME', 'DelCurrentUsrFirstname', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('DEL_CURRENT_USR_LASTNAME', 'DelCurrentUsrLastname', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('DEL_DELEGATE_DATE', 'DelDelegateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DEL_INIT_DATE', 'DelInitDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('DEL_DUE_DATE', 'DelDueDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('DEL_PRIORITY', 'DelPriority', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_ID', 'ProId', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('USR_ID', 'UsrId', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('TAS_ID', 'TasId', 'int', CreoleTypes::INTEGER, false, null);

    } // doBuild()

} // ListPausedMapBuilder
