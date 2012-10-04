<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CONTENT' table to 'workflow' DatabaseMap object.
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
class ContentMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.ContentMapBuilder';

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

        $tMap = $this->dbMap->addTable('CONTENT');
        $tMap->setPhpName('Content');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CON_CATEGORY', 'ConCategory', 'string', CreoleTypes::VARCHAR, true, 30);

        $tMap->addPrimaryKey('CON_PARENT', 'ConParent', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('CON_ID', 'ConId', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addPrimaryKey('CON_LANG', 'ConLang', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('CON_VALUE', 'ConValue', 'string', CreoleTypes::LONGVARCHAR, true, null);

        $tMap->addValidator('CON_LANG', 'maxLength', 'propel.validator.MaxLengthValidator', '5', 'Language can be no larger than 5 in size');

        $tMap->addValidator('CON_LANG', 'required', 'propel.validator.RequiredValidator', '', 'Language is required.');

    } // doBuild()

} // ContentMapBuilder
