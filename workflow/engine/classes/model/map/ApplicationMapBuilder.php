<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APPLICATION' table to 'workflow' DatabaseMap object.
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
class ApplicationMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ApplicationMapBuilder';

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

        $tMap = $this->dbMap->addTable('APPLICATION');
        $tMap->setPhpName('Application');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_TITLE', 'AppTitle', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('APP_DESCRIPTION', 'AppDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('APP_NUMBER', 'AppNumber', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_PARENT', 'AppParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_STATUS', 'AppStatus', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('APP_STATUS_ID', 'AppStatusId', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_PROC_STATUS', 'AppProcStatus', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('APP_PROC_CODE', 'AppProcCode', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('APP_PARALLEL', 'AppParallel', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_INIT_USER', 'AppInitUser', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_CUR_USER', 'AppCurUser', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_CREATE_DATE', 'AppCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_INIT_DATE', 'AppInitDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_FINISH_DATE', 'AppFinishDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_UPDATE_DATE', 'AppUpdateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_DATA', 'AppData', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('APP_PIN', 'AppPin', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_DURATION', 'AppDuration', 'double', CreoleTypes::DOUBLE, false, null);

        $tMap->addColumn('APP_DELAY_DURATION', 'AppDelayDuration', 'double', CreoleTypes::DOUBLE, false, null);

        $tMap->addColumn('APP_DRIVE_FOLDER_UID', 'AppDriveFolderUid', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addColumn('APP_ROUTING_DATA', 'AppRoutingData', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addValidator('APP_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'DRAFT|TO_DO|PAUSED|COMPLETED|CANCELLED', 'Please select a valid status.');

    } // doBuild()

} // ApplicationMapBuilder
