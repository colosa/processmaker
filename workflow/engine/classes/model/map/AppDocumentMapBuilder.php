<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_DOCUMENT' table to 'workflow' DatabaseMap object.
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
class AppDocumentMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppDocumentMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_DOCUMENT');
        $tMap->setPhpName('AppDocument');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_DOC_UID', 'AppDocUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('DOC_VERSION', 'DocVersion', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DEL_INDEX', 'DelIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('DOC_UID', 'DocUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_DOC_TYPE', 'AppDocType', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_DOC_CREATE_DATE', 'AppDocCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APP_DOC_INDEX', 'AppDocIndex', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('FOLDER_UID', 'FolderUid', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('APP_DOC_PLUGIN', 'AppDocPlugin', 'string', CreoleTypes::VARCHAR, false, 150);

        $tMap->addColumn('APP_DOC_TAGS', 'AppDocTags', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('APP_DOC_STATUS', 'AppDocStatus', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_DOC_STATUS_DATE', 'AppDocStatusDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_DOC_FIELDNAME', 'AppDocFieldname', 'string', CreoleTypes::VARCHAR, false, 150);

        $tMap->addValidator('APP_DOC_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Application Document UID can be no larger than 32 in size');

        $tMap->addValidator('APP_DOC_UID', 'required', 'propel.validator.RequiredValidator', '', 'Application Document UID is required.');

        $tMap->addValidator('APP_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Application UID can be no larger than 32 in size');

        $tMap->addValidator('APP_UID', 'required', 'propel.validator.RequiredValidator', '', 'Application UID is required.');

        $tMap->addValidator('DEL_INDEX', 'minValue', 'propel.validator.MinValueValidator', '1', 'Delegation Index must be greater than 0');

        $tMap->addValidator('DEL_INDEX', 'required', 'propel.validator.RequiredValidator', '', 'Delegation Index is required.');

        $tMap->addValidator('DOC_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Document UID can be no larger than 32 in size');

        $tMap->addValidator('DOC_UID', 'required', 'propel.validator.RequiredValidator', '', 'Document UID (building block) is required.');

        $tMap->addValidator('USR_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'User UID can be no larger than 32 in size');

        $tMap->addValidator('USR_UID', 'required', 'propel.validator.RequiredValidator', '', 'User UID is required.');

        $tMap->addValidator('APP_DOC_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'INPUT|OUTPUT|ATTACHED', 'Please select a valid document type.');

        $tMap->addValidator('APP_DOC_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Application Document Type is required.');

        $tMap->addValidator('APP_DOC_CREATE_DATE', 'required', 'propel.validator.RequiredValidator', '', 'Application Document Creation Date is required.');

        $tMap->addValidator('APP_DOC_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'ACTIVE|DELETED', 'Please select a valid document status (ACTIVE|DELETED).');

        $tMap->addValidator('APP_DOC_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'Application Document Status is required.');

    } // doBuild()

} // AppDocumentMapBuilder
