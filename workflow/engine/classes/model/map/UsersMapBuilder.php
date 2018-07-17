<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'USERS' table to 'workflow' DatabaseMap object.
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
class UsersMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.UsersMapBuilder';

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

        $tMap = $this->dbMap->addTable('USERS');
        $tMap->setPhpName('Users');

        $tMap->setUseIdGenerator(true);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_ID', 'UsrId', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('USR_USERNAME', 'UsrUsername', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('USR_PASSWORD', 'UsrPassword', 'string', CreoleTypes::VARCHAR, true, 128);

        $tMap->addColumn('USR_FIRSTNAME', 'UsrFirstname', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('USR_LASTNAME', 'UsrLastname', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('USR_EMAIL', 'UsrEmail', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('USR_DUE_DATE', 'UsrDueDate', 'int', CreoleTypes::DATE, true, null);

        $tMap->addColumn('USR_CREATE_DATE', 'UsrCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('USR_UPDATE_DATE', 'UsrUpdateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('USR_STATUS', 'UsrStatus', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_COUNTRY', 'UsrCountry', 'string', CreoleTypes::VARCHAR, true, 3);

        $tMap->addColumn('USR_CITY', 'UsrCity', 'string', CreoleTypes::VARCHAR, true, 3);

        $tMap->addColumn('USR_LOCATION', 'UsrLocation', 'string', CreoleTypes::VARCHAR, true, 3);

        $tMap->addColumn('USR_ADDRESS', 'UsrAddress', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('USR_PHONE', 'UsrPhone', 'string', CreoleTypes::VARCHAR, true, 24);

        $tMap->addColumn('USR_FAX', 'UsrFax', 'string', CreoleTypes::VARCHAR, true, 24);

        $tMap->addColumn('USR_CELLULAR', 'UsrCellular', 'string', CreoleTypes::VARCHAR, true, 24);

        $tMap->addColumn('USR_ZIP_CODE', 'UsrZipCode', 'string', CreoleTypes::VARCHAR, true, 16);

        $tMap->addColumn('DEP_UID', 'DepUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_POSITION', 'UsrPosition', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('USR_RESUME', 'UsrResume', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('USR_BIRTHDAY', 'UsrBirthday', 'int', CreoleTypes::DATE, false, null);

        $tMap->addColumn('USR_ROLE', 'UsrRole', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('USR_REPORTS_TO', 'UsrReportsTo', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('USR_REPLACED_BY', 'UsrReplacedBy', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('USR_UX', 'UsrUx', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addColumn('USR_COST_BY_HOUR', 'UsrCostByHour', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('USR_UNIT_COST', 'UsrUnitCost', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('USR_PMDRIVE_FOLDER_UID', 'UsrPmdriveFolderUid', 'string', CreoleTypes::VARCHAR, false, 128);

        $tMap->addColumn('USR_BOOKMARK_START_CASES', 'UsrBookmarkStartCases', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('USR_TIME_ZONE', 'UsrTimeZone', 'string', CreoleTypes::VARCHAR, false, 100);

        $tMap->addColumn('USR_DEFAULT_LANG', 'UsrDefaultLang', 'string', CreoleTypes::VARCHAR, false, 10);

        $tMap->addColumn('USR_LAST_LOGIN', 'UsrLastLogin', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addValidator('USR_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|INACTIVE|VACATION|CLOSED', 'Please select a valid type.');

        $tMap->addValidator('USR_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'Type is required.');

    } // doBuild()

} // UsersMapBuilder
