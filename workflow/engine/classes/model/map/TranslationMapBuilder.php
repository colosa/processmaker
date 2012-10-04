<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TRANSLATION' table to 'workflow' DatabaseMap object.
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
class TranslationMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.TranslationMapBuilder';

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

        $tMap = $this->dbMap->addTable('TRANSLATION');
        $tMap->setPhpName('Translation');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('TRN_CATEGORY', 'TrnCategory', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addPrimaryKey('TRN_ID', 'TrnId', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addPrimaryKey('TRN_LANG', 'TrnLang', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('TRN_VALUE', 'TrnValue', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addColumn('TRN_UPDATE_DATE', 'TrnUpdateDate', 'int', CreoleTypes::DATE, false, null);

        $tMap->addValidator('TRN_CATEGORY', 'maxLength', 'propel.validator.MaxLengthValidator', '100', 'Category can be no larger than 100 in size');

        $tMap->addValidator('TRN_CATEGORY', 'required', 'propel.validator.RequiredValidator', '', 'Category is required.');

        $tMap->addValidator('TRN_ID', 'maxLength', 'propel.validator.MaxLengthValidator', '100', 'ID can be no larger than 100 in size');

        $tMap->addValidator('TRN_ID', 'required', 'propel.validator.RequiredValidator', '', 'ID is required.');

        $tMap->addValidator('TRN_LANG', 'maxLength', 'propel.validator.MaxLengthValidator', '5', 'Language can be no larger than 5 in size');

        $tMap->addValidator('TRN_LANG', 'required', 'propel.validator.RequiredValidator', '', 'Language is required.');

        $tMap->addValidator('TRN_VALUE', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'Value can be no larger than 200 in size');

        $tMap->addValidator('TRN_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'Value is required.');

    } // doBuild()

} // TranslationMapBuilder
