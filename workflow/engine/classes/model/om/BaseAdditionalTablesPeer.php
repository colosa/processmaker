<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AdditionalTablesPeer::getOMClass()
include_once 'classes/model/AdditionalTables.php';

/**
 * Base static class for performing query and update operations on the 'ADDITIONAL_TABLES' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAdditionalTablesPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'ADDITIONAL_TABLES';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AdditionalTables';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the ADD_TAB_UID field */
    const ADD_TAB_UID = 'ADDITIONAL_TABLES.ADD_TAB_UID';

    /** the column name for the ADD_TAB_NAME field */
    const ADD_TAB_NAME = 'ADDITIONAL_TABLES.ADD_TAB_NAME';

    /** the column name for the ADD_TAB_CLASS_NAME field */
    const ADD_TAB_CLASS_NAME = 'ADDITIONAL_TABLES.ADD_TAB_CLASS_NAME';

    /** the column name for the ADD_TAB_DESCRIPTION field */
    const ADD_TAB_DESCRIPTION = 'ADDITIONAL_TABLES.ADD_TAB_DESCRIPTION';

    /** the column name for the ADD_TAB_SDW_LOG_INSERT field */
    const ADD_TAB_SDW_LOG_INSERT = 'ADDITIONAL_TABLES.ADD_TAB_SDW_LOG_INSERT';

    /** the column name for the ADD_TAB_SDW_LOG_UPDATE field */
    const ADD_TAB_SDW_LOG_UPDATE = 'ADDITIONAL_TABLES.ADD_TAB_SDW_LOG_UPDATE';

    /** the column name for the ADD_TAB_SDW_LOG_DELETE field */
    const ADD_TAB_SDW_LOG_DELETE = 'ADDITIONAL_TABLES.ADD_TAB_SDW_LOG_DELETE';

    /** the column name for the ADD_TAB_SDW_LOG_SELECT field */
    const ADD_TAB_SDW_LOG_SELECT = 'ADDITIONAL_TABLES.ADD_TAB_SDW_LOG_SELECT';

    /** the column name for the ADD_TAB_SDW_MAX_LENGTH field */
    const ADD_TAB_SDW_MAX_LENGTH = 'ADDITIONAL_TABLES.ADD_TAB_SDW_MAX_LENGTH';

    /** the column name for the ADD_TAB_SDW_AUTO_DELETE field */
    const ADD_TAB_SDW_AUTO_DELETE = 'ADDITIONAL_TABLES.ADD_TAB_SDW_AUTO_DELETE';

    /** the column name for the ADD_TAB_PLG_UID field */
    const ADD_TAB_PLG_UID = 'ADDITIONAL_TABLES.ADD_TAB_PLG_UID';

    /** the column name for the DBS_UID field */
    const DBS_UID = 'ADDITIONAL_TABLES.DBS_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'ADDITIONAL_TABLES.PRO_UID';

    /** the column name for the ADD_TAB_TYPE field */
    const ADD_TAB_TYPE = 'ADDITIONAL_TABLES.ADD_TAB_TYPE';

    /** the column name for the ADD_TAB_GRID field */
    const ADD_TAB_GRID = 'ADDITIONAL_TABLES.ADD_TAB_GRID';

    /** the column name for the ADD_TAB_TAG field */
    const ADD_TAB_TAG = 'ADDITIONAL_TABLES.ADD_TAB_TAG';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AddTabUid', 'AddTabName', 'AddTabClassName', 'AddTabDescription', 'AddTabSdwLogInsert', 'AddTabSdwLogUpdate', 'AddTabSdwLogDelete', 'AddTabSdwLogSelect', 'AddTabSdwMaxLength', 'AddTabSdwAutoDelete', 'AddTabPlgUid', 'DbsUid', 'ProUid', 'AddTabType', 'AddTabGrid', 'AddTabTag', ),
        BasePeer::TYPE_COLNAME => array (AdditionalTablesPeer::ADD_TAB_UID, AdditionalTablesPeer::ADD_TAB_NAME, AdditionalTablesPeer::ADD_TAB_CLASS_NAME, AdditionalTablesPeer::ADD_TAB_DESCRIPTION, AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT, AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE, AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE, AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT, AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH, AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE, AdditionalTablesPeer::ADD_TAB_PLG_UID, AdditionalTablesPeer::DBS_UID, AdditionalTablesPeer::PRO_UID, AdditionalTablesPeer::ADD_TAB_TYPE, AdditionalTablesPeer::ADD_TAB_GRID, AdditionalTablesPeer::ADD_TAB_TAG, ),
        BasePeer::TYPE_FIELDNAME => array ('ADD_TAB_UID', 'ADD_TAB_NAME', 'ADD_TAB_CLASS_NAME', 'ADD_TAB_DESCRIPTION', 'ADD_TAB_SDW_LOG_INSERT', 'ADD_TAB_SDW_LOG_UPDATE', 'ADD_TAB_SDW_LOG_DELETE', 'ADD_TAB_SDW_LOG_SELECT', 'ADD_TAB_SDW_MAX_LENGTH', 'ADD_TAB_SDW_AUTO_DELETE', 'ADD_TAB_PLG_UID', 'DBS_UID', 'PRO_UID', 'ADD_TAB_TYPE', 'ADD_TAB_GRID', 'ADD_TAB_TAG', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AddTabUid' => 0, 'AddTabName' => 1, 'AddTabClassName' => 2, 'AddTabDescription' => 3, 'AddTabSdwLogInsert' => 4, 'AddTabSdwLogUpdate' => 5, 'AddTabSdwLogDelete' => 6, 'AddTabSdwLogSelect' => 7, 'AddTabSdwMaxLength' => 8, 'AddTabSdwAutoDelete' => 9, 'AddTabPlgUid' => 10, 'DbsUid' => 11, 'ProUid' => 12, 'AddTabType' => 13, 'AddTabGrid' => 14, 'AddTabTag' => 15, ),
        BasePeer::TYPE_COLNAME => array (AdditionalTablesPeer::ADD_TAB_UID => 0, AdditionalTablesPeer::ADD_TAB_NAME => 1, AdditionalTablesPeer::ADD_TAB_CLASS_NAME => 2, AdditionalTablesPeer::ADD_TAB_DESCRIPTION => 3, AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT => 4, AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE => 5, AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE => 6, AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT => 7, AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH => 8, AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE => 9, AdditionalTablesPeer::ADD_TAB_PLG_UID => 10, AdditionalTablesPeer::DBS_UID => 11, AdditionalTablesPeer::PRO_UID => 12, AdditionalTablesPeer::ADD_TAB_TYPE => 13, AdditionalTablesPeer::ADD_TAB_GRID => 14, AdditionalTablesPeer::ADD_TAB_TAG => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('ADD_TAB_UID' => 0, 'ADD_TAB_NAME' => 1, 'ADD_TAB_CLASS_NAME' => 2, 'ADD_TAB_DESCRIPTION' => 3, 'ADD_TAB_SDW_LOG_INSERT' => 4, 'ADD_TAB_SDW_LOG_UPDATE' => 5, 'ADD_TAB_SDW_LOG_DELETE' => 6, 'ADD_TAB_SDW_LOG_SELECT' => 7, 'ADD_TAB_SDW_MAX_LENGTH' => 8, 'ADD_TAB_SDW_AUTO_DELETE' => 9, 'ADD_TAB_PLG_UID' => 10, 'DBS_UID' => 11, 'PRO_UID' => 12, 'ADD_TAB_TYPE' => 13, 'ADD_TAB_GRID' => 14, 'ADD_TAB_TAG' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AdditionalTablesMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AdditionalTablesMapBuilder');
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
            $map = AdditionalTablesPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AdditionalTablesPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AdditionalTablesPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_CLASS_NAME);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_PLG_UID);

        $criteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);

        $criteria->addSelectColumn(AdditionalTablesPeer::PRO_UID);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TYPE);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_GRID);

        $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TAG);

    }

    const COUNT = 'COUNT(ADDITIONAL_TABLES.ADD_TAB_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT ADDITIONAL_TABLES.ADD_TAB_UID)';

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
            $criteria->addSelectColumn(AdditionalTablesPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AdditionalTablesPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AdditionalTablesPeer::doSelectRS($criteria, $con);
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
     * @return     AdditionalTables
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AdditionalTablesPeer::doSelect($critcopy, $con);
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
        return AdditionalTablesPeer::populateObjects(AdditionalTablesPeer::doSelectRS($criteria, $con));
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
            AdditionalTablesPeer::addSelectColumns($criteria);
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
        $cls = AdditionalTablesPeer::getOMClass();
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
        return AdditionalTablesPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AdditionalTables or Criteria object.
     *
     * @param      mixed $values Criteria or AdditionalTables object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AdditionalTables object
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
     * Method perform an UPDATE on the database, given a AdditionalTables or Criteria object.
     *
     * @param      mixed $values Criteria or AdditionalTables object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AdditionalTablesPeer::ADD_TAB_UID);
            $selectCriteria->add(AdditionalTablesPeer::ADD_TAB_UID, $criteria->remove(AdditionalTablesPeer::ADD_TAB_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the ADDITIONAL_TABLES table.
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
            $affectedRows += BasePeer::doDeleteAll(AdditionalTablesPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AdditionalTables or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AdditionalTables object or primary key or array of primary keys
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
            $con = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AdditionalTables) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given AdditionalTables object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AdditionalTables $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AdditionalTables $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AdditionalTablesPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AdditionalTablesPeer::TABLE_NAME);

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

        return BasePeer::doValidate(AdditionalTablesPeer::DATABASE_NAME, AdditionalTablesPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     AdditionalTables
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(AdditionalTablesPeer::DATABASE_NAME);

        $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, $pk);


        $v = AdditionalTablesPeer::doSelect($criteria, $con);

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
            $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, $pks, Criteria::IN);
            $objs = AdditionalTablesPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAdditionalTablesPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AdditionalTablesMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AdditionalTablesMapBuilder');
}

