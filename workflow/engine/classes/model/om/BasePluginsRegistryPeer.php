<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by PluginsRegistryPeer::getOMClass()
include_once 'classes/model/PluginsRegistry.php';

/**
 * Base static class for performing query and update operations on the 'PLUGINS_REGISTRY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BasePluginsRegistryPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'PLUGINS_REGISTRY';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.PluginsRegistry';

    /** The total number of columns. */
    const NUM_COLUMNS = 24;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the PR_UID field */
    const PR_UID = 'PLUGINS_REGISTRY.PR_UID';

    /** the column name for the PLUGIN_NAMESPACE field */
    const PLUGIN_NAMESPACE = 'PLUGINS_REGISTRY.PLUGIN_NAMESPACE';

    /** the column name for the PLUGIN_DESCRIPTION field */
    const PLUGIN_DESCRIPTION = 'PLUGINS_REGISTRY.PLUGIN_DESCRIPTION';

    /** the column name for the PLUGIN_CLASS_NAME field */
    const PLUGIN_CLASS_NAME = 'PLUGINS_REGISTRY.PLUGIN_CLASS_NAME';

    /** the column name for the PLUGIN_FRIENDLY_NAME field */
    const PLUGIN_FRIENDLY_NAME = 'PLUGINS_REGISTRY.PLUGIN_FRIENDLY_NAME';

    /** the column name for the PLUGIN_FILE field */
    const PLUGIN_FILE = 'PLUGINS_REGISTRY.PLUGIN_FILE';

    /** the column name for the PLUGIN_FOLDER field */
    const PLUGIN_FOLDER = 'PLUGINS_REGISTRY.PLUGIN_FOLDER';

    /** the column name for the PLUGIN_SETUP_PAGE field */
    const PLUGIN_SETUP_PAGE = 'PLUGINS_REGISTRY.PLUGIN_SETUP_PAGE';

    /** the column name for the PLUGIN_COMPANY_LOGO field */
    const PLUGIN_COMPANY_LOGO = 'PLUGINS_REGISTRY.PLUGIN_COMPANY_LOGO';

    /** the column name for the PLUGIN_WORKSPACES field */
    const PLUGIN_WORKSPACES = 'PLUGINS_REGISTRY.PLUGIN_WORKSPACES';

    /** the column name for the PLUGIN_VERSION field */
    const PLUGIN_VERSION = 'PLUGINS_REGISTRY.PLUGIN_VERSION';

    /** the column name for the PLUGIN_ENABLE field */
    const PLUGIN_ENABLE = 'PLUGINS_REGISTRY.PLUGIN_ENABLE';

    /** the column name for the PLUGIN_PRIVATE field */
    const PLUGIN_PRIVATE = 'PLUGINS_REGISTRY.PLUGIN_PRIVATE';

    /** the column name for the PLUGIN_MENUS field */
    const PLUGIN_MENUS = 'PLUGINS_REGISTRY.PLUGIN_MENUS';

    /** the column name for the PLUGIN_FOLDERS field */
    const PLUGIN_FOLDERS = 'PLUGINS_REGISTRY.PLUGIN_FOLDERS';

    /** the column name for the PLUGIN_TRIGGERS field */
    const PLUGIN_TRIGGERS = 'PLUGINS_REGISTRY.PLUGIN_TRIGGERS';

    /** the column name for the PLUGIN_PM_FUNCTIONS field */
    const PLUGIN_PM_FUNCTIONS = 'PLUGINS_REGISTRY.PLUGIN_PM_FUNCTIONS';

    /** the column name for the PLUGIN_REDIRECT_LOGIN field */
    const PLUGIN_REDIRECT_LOGIN = 'PLUGINS_REGISTRY.PLUGIN_REDIRECT_LOGIN';

    /** the column name for the PLUGIN_STEPS field */
    const PLUGIN_STEPS = 'PLUGINS_REGISTRY.PLUGIN_STEPS';

    /** the column name for the PLUGIN_CSS field */
    const PLUGIN_CSS = 'PLUGINS_REGISTRY.PLUGIN_CSS';

    /** the column name for the PLUGIN_JS field */
    const PLUGIN_JS = 'PLUGINS_REGISTRY.PLUGIN_JS';

    /** the column name for the PLUGIN_REST_SERVICE field */
    const PLUGIN_REST_SERVICE = 'PLUGINS_REGISTRY.PLUGIN_REST_SERVICE';

    /** the column name for the PLUGIN_TASK_EXTENDED_PROPERTIES field */
    const PLUGIN_TASK_EXTENDED_PROPERTIES = 'PLUGINS_REGISTRY.PLUGIN_TASK_EXTENDED_PROPERTIES';

    /** the column name for the PLUGIN_ATTRIBUTES field */
    const PLUGIN_ATTRIBUTES = 'PLUGINS_REGISTRY.PLUGIN_ATTRIBUTES';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('PrUid', 'PluginNamespace', 'PluginDescription', 'PluginClassName', 'PluginFriendlyName', 'PluginFile', 'PluginFolder', 'PluginSetupPage', 'PluginCompanyLogo', 'PluginWorkspaces', 'PluginVersion', 'PluginEnable', 'PluginPrivate', 'PluginMenus', 'PluginFolders', 'PluginTriggers', 'PluginPmFunctions', 'PluginRedirectLogin', 'PluginSteps', 'PluginCss', 'PluginJs', 'PluginRestService', 'PluginTaskExtendedProperties', 'PluginAttributes', ),
        BasePeer::TYPE_COLNAME => array (PluginsRegistryPeer::PR_UID, PluginsRegistryPeer::PLUGIN_NAMESPACE, PluginsRegistryPeer::PLUGIN_DESCRIPTION, PluginsRegistryPeer::PLUGIN_CLASS_NAME, PluginsRegistryPeer::PLUGIN_FRIENDLY_NAME, PluginsRegistryPeer::PLUGIN_FILE, PluginsRegistryPeer::PLUGIN_FOLDER, PluginsRegistryPeer::PLUGIN_SETUP_PAGE, PluginsRegistryPeer::PLUGIN_COMPANY_LOGO, PluginsRegistryPeer::PLUGIN_WORKSPACES, PluginsRegistryPeer::PLUGIN_VERSION, PluginsRegistryPeer::PLUGIN_ENABLE, PluginsRegistryPeer::PLUGIN_PRIVATE, PluginsRegistryPeer::PLUGIN_MENUS, PluginsRegistryPeer::PLUGIN_FOLDERS, PluginsRegistryPeer::PLUGIN_TRIGGERS, PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS, PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN, PluginsRegistryPeer::PLUGIN_STEPS, PluginsRegistryPeer::PLUGIN_CSS, PluginsRegistryPeer::PLUGIN_JS, PluginsRegistryPeer::PLUGIN_REST_SERVICE, PluginsRegistryPeer::PLUGIN_TASK_EXTENDED_PROPERTIES, PluginsRegistryPeer::PLUGIN_ATTRIBUTES, ),
        BasePeer::TYPE_FIELDNAME => array ('PR_UID', 'PLUGIN_NAMESPACE', 'PLUGIN_DESCRIPTION', 'PLUGIN_CLASS_NAME', 'PLUGIN_FRIENDLY_NAME', 'PLUGIN_FILE', 'PLUGIN_FOLDER', 'PLUGIN_SETUP_PAGE', 'PLUGIN_COMPANY_LOGO', 'PLUGIN_WORKSPACES', 'PLUGIN_VERSION', 'PLUGIN_ENABLE', 'PLUGIN_PRIVATE', 'PLUGIN_MENUS', 'PLUGIN_FOLDERS', 'PLUGIN_TRIGGERS', 'PLUGIN_PM_FUNCTIONS', 'PLUGIN_REDIRECT_LOGIN', 'PLUGIN_STEPS', 'PLUGIN_CSS', 'PLUGIN_JS', 'PLUGIN_REST_SERVICE', 'PLUGIN_TASK_EXTENDED_PROPERTIES', 'PLUGIN_ATTRIBUTES', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('PrUid' => 0, 'PluginNamespace' => 1, 'PluginDescription' => 2, 'PluginClassName' => 3, 'PluginFriendlyName' => 4, 'PluginFile' => 5, 'PluginFolder' => 6, 'PluginSetupPage' => 7, 'PluginCompanyLogo' => 8, 'PluginWorkspaces' => 9, 'PluginVersion' => 10, 'PluginEnable' => 11, 'PluginPrivate' => 12, 'PluginMenus' => 13, 'PluginFolders' => 14, 'PluginTriggers' => 15, 'PluginPmFunctions' => 16, 'PluginRedirectLogin' => 17, 'PluginSteps' => 18, 'PluginCss' => 19, 'PluginJs' => 20, 'PluginRestService' => 21, 'PluginTaskExtendedProperties' => 22, 'PluginAttributes' => 23, ),
        BasePeer::TYPE_COLNAME => array (PluginsRegistryPeer::PR_UID => 0, PluginsRegistryPeer::PLUGIN_NAMESPACE => 1, PluginsRegistryPeer::PLUGIN_DESCRIPTION => 2, PluginsRegistryPeer::PLUGIN_CLASS_NAME => 3, PluginsRegistryPeer::PLUGIN_FRIENDLY_NAME => 4, PluginsRegistryPeer::PLUGIN_FILE => 5, PluginsRegistryPeer::PLUGIN_FOLDER => 6, PluginsRegistryPeer::PLUGIN_SETUP_PAGE => 7, PluginsRegistryPeer::PLUGIN_COMPANY_LOGO => 8, PluginsRegistryPeer::PLUGIN_WORKSPACES => 9, PluginsRegistryPeer::PLUGIN_VERSION => 10, PluginsRegistryPeer::PLUGIN_ENABLE => 11, PluginsRegistryPeer::PLUGIN_PRIVATE => 12, PluginsRegistryPeer::PLUGIN_MENUS => 13, PluginsRegistryPeer::PLUGIN_FOLDERS => 14, PluginsRegistryPeer::PLUGIN_TRIGGERS => 15, PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS => 16, PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN => 17, PluginsRegistryPeer::PLUGIN_STEPS => 18, PluginsRegistryPeer::PLUGIN_CSS => 19, PluginsRegistryPeer::PLUGIN_JS => 20, PluginsRegistryPeer::PLUGIN_REST_SERVICE => 21, PluginsRegistryPeer::PLUGIN_TASK_EXTENDED_PROPERTIES => 22, PluginsRegistryPeer::PLUGIN_ATTRIBUTES => 23, ),
        BasePeer::TYPE_FIELDNAME => array ('PR_UID' => 0, 'PLUGIN_NAMESPACE' => 1, 'PLUGIN_DESCRIPTION' => 2, 'PLUGIN_CLASS_NAME' => 3, 'PLUGIN_FRIENDLY_NAME' => 4, 'PLUGIN_FILE' => 5, 'PLUGIN_FOLDER' => 6, 'PLUGIN_SETUP_PAGE' => 7, 'PLUGIN_COMPANY_LOGO' => 8, 'PLUGIN_WORKSPACES' => 9, 'PLUGIN_VERSION' => 10, 'PLUGIN_ENABLE' => 11, 'PLUGIN_PRIVATE' => 12, 'PLUGIN_MENUS' => 13, 'PLUGIN_FOLDERS' => 14, 'PLUGIN_TRIGGERS' => 15, 'PLUGIN_PM_FUNCTIONS' => 16, 'PLUGIN_REDIRECT_LOGIN' => 17, 'PLUGIN_STEPS' => 18, 'PLUGIN_CSS' => 19, 'PLUGIN_JS' => 20, 'PLUGIN_REST_SERVICE' => 21, 'PLUGIN_TASK_EXTENDED_PROPERTIES' => 22, 'PLUGIN_ATTRIBUTES' => 23, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/PluginsRegistryMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.PluginsRegistryMapBuilder');
    }
    /**
     * Gets a map (hash) of PHP names to DB column names.
     *
     * @return     array The PHP to DB name map for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
     */
    public static function getPhpNameMap()
    {
        if (self::$phpNameMap === null) {
            $map = PluginsRegistryPeer::getTableMap();
            $columns = $map->getColumns();
            $nameMap = array();
            foreach ($columns as $column) {
                $nameMap[$column->getPhpName()] = $column->getColumnName();
            }
            self::$phpNameMap = $nameMap;
        }
        return self::$phpNameMap;
    }
    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants TYPE_PHPNAME,
     *                         TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return     string translated name of the field.
     */
    static public function translateFieldName($name, $fromType, $toType)
    {
        $toNames = self::getFieldNames($toType);
        $key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
        }
        return $toNames[$key];
    }

    /**
     * Returns an array of of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants TYPE_PHPNAME,
     *                      TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     array A list of field names
     */

    static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, self::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
        }
        return self::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *      $c->addAlias("alias1", TablePeer::TABLE_NAME);
     *      $c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. PluginsRegistryPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(PluginsRegistryPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      criteria object containing the columns to add.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria)
    {

        $criteria->addSelectColumn(PluginsRegistryPeer::PR_UID);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_NAMESPACE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_DESCRIPTION);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_CLASS_NAME);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_FRIENDLY_NAME);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_FILE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_FOLDER);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_SETUP_PAGE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_COMPANY_LOGO);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_WORKSPACES);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_VERSION);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_ENABLE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_PRIVATE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_MENUS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_FOLDERS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_TRIGGERS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_STEPS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_CSS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_JS);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_REST_SERVICE);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_TASK_EXTENDED_PROPERTIES);

        $criteria->addSelectColumn(PluginsRegistryPeer::PLUGIN_ATTRIBUTES);

    }

    const COUNT = 'COUNT(PLUGINS_REGISTRY.PR_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT PLUGINS_REGISTRY.PR_UID)';

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
     * @param      Connection $con
     * @return     int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, $con = null)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // clear out anything that might confuse the ORDER BY clause
        $criteria->clearSelectColumns()->clearOrderByColumns();
        if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->addSelectColumn(PluginsRegistryPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(PluginsRegistryPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = PluginsRegistryPeer::doSelectRS($criteria, $con);
        if ($rs->next()) {
            return $rs->getInt(1);
        } else {
            // no rows returned; we infer that means 0 matches.
            return 0;
        }
    }
    /**
     * Method to select one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      Connection $con
     * @return     PluginsRegistry
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = PluginsRegistryPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }
        return null;
    }
    /**
     * Method to do selects.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      Connection $con
     * @return     array Array of selected Objects
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, $con = null)
    {
        return PluginsRegistryPeer::populateObjects(PluginsRegistryPeer::doSelectRS($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect()
     * method to get a ResultSet.
     *
     * Use this method directly if you want to just get the resultset
     * (instead of an array of objects).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      Connection $con the connection to use
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     * @return     ResultSet The resultset object with numerically-indexed fields.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectRS(Criteria $criteria, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        if (!$criteria->getSelectColumns()) {
            $criteria = clone $criteria;
            PluginsRegistryPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        // BasePeer returns a Creole ResultSet, set to return
        // rows indexed numerically.
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function populateObjects(ResultSet $rs)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = PluginsRegistryPeer::getOMClass();
        $cls = Propel::import($cls);
        // populate the object(s)
        while ($rs->next()) {

            $obj = new $cls();
            $obj->hydrate($rs);
            $results[] = $obj;

        }
        return $results;
    }
    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return     TableMap
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
    }

    /**
     * The class that the Peer will make instances of.
     *
     * This uses a dot-path notation which is tranalted into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @return     string path.to.ClassName
     */
    public static function getOMClass()
    {
        return PluginsRegistryPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a PluginsRegistry or Criteria object.
     *
     * @param      mixed $values Criteria or PluginsRegistry object containing data that is used to create the INSERT statement.
     * @param      Connection $con the connection to use
     * @return     mixed The new primary key.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from PluginsRegistry object
        }


        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->begin();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }

        return $pk;
    }

    /**
     * Method perform an UPDATE on the database, given a PluginsRegistry or Criteria object.
     *
     * @param      mixed $values Criteria or PluginsRegistry object containing data create the UPDATE statement.
     * @param      Connection $con The connection to use (specify Connection exert more control over transactions).
     * @return     int The number of affected rows (if supported by underlying database driver).
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $selectCriteria = new Criteria(self::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(PluginsRegistryPeer::PR_UID);
            $selectCriteria->add(PluginsRegistryPeer::PR_UID, $criteria->remove(PluginsRegistryPeer::PR_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the PLUGINS_REGISTRY table.
     *
     * @return     int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll($con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();
            $affectedRows += BasePeer::doDeleteAll(PluginsRegistryPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a PluginsRegistry or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or PluginsRegistry object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      Connection $con the connection to use
     * @return     int  The number of affected rows (if supported by underlying database driver).
     *             This includes CASCADE-related rows
     *              if supported by native driver or if emulated using Propel.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
    */
    public static function doDelete($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof PluginsRegistry) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(PluginsRegistryPeer::PR_UID, (array) $values, Criteria::IN);
        }

        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given PluginsRegistry object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      PluginsRegistry $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(PluginsRegistry $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(PluginsRegistryPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(PluginsRegistryPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->containsColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(PluginsRegistryPeer::DATABASE_NAME, PluginsRegistryPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     PluginsRegistry
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(PluginsRegistryPeer::DATABASE_NAME);

        $criteria->add(PluginsRegistryPeer::PR_UID, $pk);


        $v = PluginsRegistryPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      Connection $con the connection to use
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria();
            $criteria->add(PluginsRegistryPeer::PR_UID, $pks, Criteria::IN);
            $objs = PluginsRegistryPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BasePluginsRegistryPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/PluginsRegistryMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.PluginsRegistryMapBuilder');
}

