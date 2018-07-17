<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'USERS_PROPERTIES' table to 'workflow' DatabaseMap object.
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
class UsersPropertiesMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.UsersPropertiesMapBuilder';

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

        $tMap = $this->dbMap->addTable('USERS_PROPERTIES');
        $tMap->setPhpName('UsersProperties');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_LAST_UPDATE_DATE', 'UsrLastUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('USR_LOGGED_NEXT_TIME', 'UsrLoggedNextTime', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('USR_PASSWORD_HISTORY', 'UsrPasswordHistory', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('USR_SETTING_DESIGNER', 'UsrSettingDesigner', 'string', CreoleTypes::LONGVARCHAR, false, null);

    } // doBuild()

} // UsersPropertiesMapBuilder
