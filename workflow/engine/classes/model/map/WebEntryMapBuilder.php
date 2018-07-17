<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'WEB_ENTRY' table to 'workflow' DatabaseMap object.
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
class WebEntryMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.WebEntryMapBuilder';

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

        $tMap = $this->dbMap->addTable('WEB_ENTRY');
        $tMap->setPhpName('WebEntry');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('WE_UID', 'WeUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DYN_UID', 'DynUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('WE_METHOD', 'WeMethod', 'string', CreoleTypes::VARCHAR, false, 4);

        $tMap->addColumn('WE_INPUT_DOCUMENT_ACCESS', 'WeInputDocumentAccess', 'int', CreoleTypes::INTEGER, false, null);

        $tMap->addColumn('WE_DATA', 'WeData', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('WE_CREATE_USR_UID', 'WeCreateUsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('WE_UPDATE_USR_UID', 'WeUpdateUsrUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('WE_CREATE_DATE', 'WeCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('WE_UPDATE_DATE', 'WeUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('WE_TYPE', 'WeType', 'string', CreoleTypes::VARCHAR, true, 8);

        $tMap->addColumn('WE_CUSTOM_TITLE', 'WeCustomTitle', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('WE_AUTHENTICATION', 'WeAuthentication', 'string', CreoleTypes::VARCHAR, true, 14);

        $tMap->addColumn('WE_HIDE_INFORMATION_BAR', 'WeHideInformationBar', 'string', CreoleTypes::CHAR, false, 1);

        $tMap->addColumn('WE_CALLBACK', 'WeCallback', 'string', CreoleTypes::VARCHAR, true, 13);

        $tMap->addColumn('WE_CALLBACK_URL', 'WeCallbackUrl', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('WE_LINK_GENERATION', 'WeLinkGeneration', 'string', CreoleTypes::VARCHAR, true, 8);

        $tMap->addColumn('WE_LINK_SKIN', 'WeLinkSkin', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('WE_LINK_LANGUAGE', 'WeLinkLanguage', 'string', CreoleTypes::VARCHAR, false, 255);

        $tMap->addColumn('WE_LINK_DOMAIN', 'WeLinkDomain', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('WE_SHOW_IN_NEW_CASE', 'WeShowInNewCase', 'string', CreoleTypes::CHAR, false, 1);

        $tMap->addValidator('WE_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'SINGLE|MULTIPLE', 'Please enter a valid value for WE_TYPE');

        $tMap->addValidator('WE_AUTHENTICATION', 'validValues', 'propel.validator.ValidValuesValidator', 'ANONYMOUS|LOGIN_REQUIRED', 'Please enter a valid value for WE_AUTHENTICATION');

        $tMap->addValidator('WE_CALLBACK', 'validValues', 'propel.validator.ValidValuesValidator', 'PROCESSMAKER|CUSTOM|CUSTOM_CLEAR', 'Please enter a valid value for WE_CALLBACK');

        $tMap->addValidator('WE_LINK_GENERATION', 'validValues', 'propel.validator.ValidValuesValidator', 'DEFAULT|ADVANCED', 'Please enter a valid value for WE_LINK_GENERATION');

    } // doBuild()

} // WebEntryMapBuilder
