<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AppDelayPeer::getOMClass()
include_once 'classes/model/AppDelay.php';

/**
 * Base static class for performing query and update operations on the 'APP_DELAY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppDelayPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'APP_DELAY';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AppDelay';

    /** The total number of columns. */
    const NUM_COLUMNS = 17;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_DELAY_UID field */
    const APP_DELAY_UID = 'APP_DELAY.APP_DELAY_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'APP_DELAY.PRO_UID';

    /** the column name for the APP_UID field */
    const APP_UID = 'APP_DELAY.APP_UID';

    /** the column name for the APP_NUMBER field */
    const APP_NUMBER = 'APP_DELAY.APP_NUMBER';

    /** the column name for the APP_THREAD_INDEX field */
    const APP_THREAD_INDEX = 'APP_DELAY.APP_THREAD_INDEX';

    /** the column name for the APP_DEL_INDEX field */
    const APP_DEL_INDEX = 'APP_DELAY.APP_DEL_INDEX';

    /** the column name for the APP_TYPE field */
    const APP_TYPE = 'APP_DELAY.APP_TYPE';

    /** the column name for the APP_STATUS field */
    const APP_STATUS = 'APP_DELAY.APP_STATUS';

    /** the column name for the APP_NEXT_TASK field */
    const APP_NEXT_TASK = 'APP_DELAY.APP_NEXT_TASK';

    /** the column name for the APP_DELEGATION_USER field */
    const APP_DELEGATION_USER = 'APP_DELAY.APP_DELEGATION_USER';

    /** the column name for the APP_ENABLE_ACTION_USER field */
    const APP_ENABLE_ACTION_USER = 'APP_DELAY.APP_ENABLE_ACTION_USER';

    /** the column name for the APP_ENABLE_ACTION_DATE field */
    const APP_ENABLE_ACTION_DATE = 'APP_DELAY.APP_ENABLE_ACTION_DATE';

    /** the column name for the APP_DISABLE_ACTION_USER field */
    const APP_DISABLE_ACTION_USER = 'APP_DELAY.APP_DISABLE_ACTION_USER';

    /** the column name for the APP_DISABLE_ACTION_DATE field */
    const APP_DISABLE_ACTION_DATE = 'APP_DELAY.APP_DISABLE_ACTION_DATE';

    /** the column name for the APP_AUTOMATIC_DISABLED_DATE field */
    const APP_AUTOMATIC_DISABLED_DATE = 'APP_DELAY.APP_AUTOMATIC_DISABLED_DATE';

    /** the column name for the APP_DELEGATION_USER_ID field */
    const APP_DELEGATION_USER_ID = 'APP_DELAY.APP_DELEGATION_USER_ID';

    /** the column name for the PRO_ID field */
    const PRO_ID = 'APP_DELAY.PRO_ID';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppDelayUid', 'ProUid', 'AppUid', 'AppNumber', 'AppThreadIndex', 'AppDelIndex', 'AppType', 'AppStatus', 'AppNextTask', 'AppDelegationUser', 'AppEnableActionUser', 'AppEnableActionDate', 'AppDisableActionUser', 'AppDisableActionDate', 'AppAutomaticDisabledDate', 'AppDelegationUserId', 'ProId', ),
        BasePeer::TYPE_COLNAME => array (AppDelayPeer::APP_DELAY_UID, AppDelayPeer::PRO_UID, AppDelayPeer::APP_UID, AppDelayPeer::APP_NUMBER, AppDelayPeer::APP_THREAD_INDEX, AppDelayPeer::APP_DEL_INDEX, AppDelayPeer::APP_TYPE, AppDelayPeer::APP_STATUS, AppDelayPeer::APP_NEXT_TASK, AppDelayPeer::APP_DELEGATION_USER, AppDelayPeer::APP_ENABLE_ACTION_USER, AppDelayPeer::APP_ENABLE_ACTION_DATE, AppDelayPeer::APP_DISABLE_ACTION_USER, AppDelayPeer::APP_DISABLE_ACTION_DATE, AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE, AppDelayPeer::APP_DELEGATION_USER_ID, AppDelayPeer::PRO_ID, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_DELAY_UID', 'PRO_UID', 'APP_UID', 'APP_NUMBER', 'APP_THREAD_INDEX', 'APP_DEL_INDEX', 'APP_TYPE', 'APP_STATUS', 'APP_NEXT_TASK', 'APP_DELEGATION_USER', 'APP_ENABLE_ACTION_USER', 'APP_ENABLE_ACTION_DATE', 'APP_DISABLE_ACTION_USER', 'APP_DISABLE_ACTION_DATE', 'APP_AUTOMATIC_DISABLED_DATE', 'APP_DELEGATION_USER_ID', 'PRO_ID', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppDelayUid' => 0, 'ProUid' => 1, 'AppUid' => 2, 'AppNumber' => 3, 'AppThreadIndex' => 4, 'AppDelIndex' => 5, 'AppType' => 6, 'AppStatus' => 7, 'AppNextTask' => 8, 'AppDelegationUser' => 9, 'AppEnableActionUser' => 10, 'AppEnableActionDate' => 11, 'AppDisableActionUser' => 12, 'AppDisableActionDate' => 13, 'AppAutomaticDisabledDate' => 14, 'AppDelegationUserId' => 15, 'ProId' => 16, ),
        BasePeer::TYPE_COLNAME => array (AppDelayPeer::APP_DELAY_UID => 0, AppDelayPeer::PRO_UID => 1, AppDelayPeer::APP_UID => 2, AppDelayPeer::APP_NUMBER => 3, AppDelayPeer::APP_THREAD_INDEX => 4, AppDelayPeer::APP_DEL_INDEX => 5, AppDelayPeer::APP_TYPE => 6, AppDelayPeer::APP_STATUS => 7, AppDelayPeer::APP_NEXT_TASK => 8, AppDelayPeer::APP_DELEGATION_USER => 9, AppDelayPeer::APP_ENABLE_ACTION_USER => 10, AppDelayPeer::APP_ENABLE_ACTION_DATE => 11, AppDelayPeer::APP_DISABLE_ACTION_USER => 12, AppDelayPeer::APP_DISABLE_ACTION_DATE => 13, AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE => 14, AppDelayPeer::APP_DELEGATION_USER_ID => 15, AppDelayPeer::PRO_ID => 16, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_DELAY_UID' => 0, 'PRO_UID' => 1, 'APP_UID' => 2, 'APP_NUMBER' => 3, 'APP_THREAD_INDEX' => 4, 'APP_DEL_INDEX' => 5, 'APP_TYPE' => 6, 'APP_STATUS' => 7, 'APP_NEXT_TASK' => 8, 'APP_DELEGATION_USER' => 9, 'APP_ENABLE_ACTION_USER' => 10, 'APP_ENABLE_ACTION_DATE' => 11, 'APP_DISABLE_ACTION_USER' => 12, 'APP_DISABLE_ACTION_DATE' => 13, 'APP_AUTOMATIC_DISABLED_DATE' => 14, 'APP_DELEGATION_USER_ID' => 15, 'PRO_ID' => 16, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AppDelayMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AppDelayMapBuilder');
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
            $map = AppDelayPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AppDelayPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AppDelayPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AppDelayPeer::APP_DELAY_UID);

        $criteria->addSelectColumn(AppDelayPeer::PRO_UID);

        $criteria->addSelectColumn(AppDelayPeer::APP_UID);

        $criteria->addSelectColumn(AppDelayPeer::APP_NUMBER);

        $criteria->addSelectColumn(AppDelayPeer::APP_THREAD_INDEX);

        $criteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);

        $criteria->addSelectColumn(AppDelayPeer::APP_TYPE);

        $criteria->addSelectColumn(AppDelayPeer::APP_STATUS);

        $criteria->addSelectColumn(AppDelayPeer::APP_NEXT_TASK);

        $criteria->addSelectColumn(AppDelayPeer::APP_DELEGATION_USER);

        $criteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_USER);

        $criteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);

        $criteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_USER);

        $criteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);

        $criteria->addSelectColumn(AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE);

        $criteria->addSelectColumn(AppDelayPeer::APP_DELEGATION_USER_ID);

        $criteria->addSelectColumn(AppDelayPeer::PRO_ID);

    }

    const COUNT = 'COUNT(APP_DELAY.APP_DELAY_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APP_DELAY.APP_DELAY_UID)';

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
            $criteria->addSelectColumn(AppDelayPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AppDelayPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AppDelayPeer::doSelectRS($criteria, $con);
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
     * @return     AppDelay
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AppDelayPeer::doSelect($critcopy, $con);
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
        return AppDelayPeer::populateObjects(AppDelayPeer::doSelectRS($criteria, $con));
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
            AppDelayPeer::addSelectColumns($criteria);
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
        $cls = AppDelayPeer::getOMClass();
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
        return AppDelayPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AppDelay or Criteria object.
     *
     * @param      mixed $values Criteria or AppDelay object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AppDelay object
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
     * Method perform an UPDATE on the database, given a AppDelay or Criteria object.
     *
     * @param      mixed $values Criteria or AppDelay object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AppDelayPeer::APP_DELAY_UID);
            $selectCriteria->add(AppDelayPeer::APP_DELAY_UID, $criteria->remove(AppDelayPeer::APP_DELAY_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the APP_DELAY table.
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
            $affectedRows += BasePeer::doDeleteAll(AppDelayPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AppDelay or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AppDelay object or primary key or array of primary keys
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
            $con = Propel::getConnection(AppDelayPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AppDelay) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(AppDelayPeer::APP_DELAY_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given AppDelay object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AppDelay $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AppDelay $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AppDelayPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AppDelayPeer::TABLE_NAME);

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

        return BasePeer::doValidate(AppDelayPeer::DATABASE_NAME, AppDelayPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     AppDelay
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(AppDelayPeer::DATABASE_NAME);

        $criteria->add(AppDelayPeer::APP_DELAY_UID, $pk);


        $v = AppDelayPeer::doSelect($criteria, $con);

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
            $criteria->add(AppDelayPeer::APP_DELAY_UID, $pks, Criteria::IN);
            $objs = AppDelayPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAppDelayPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AppDelayMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AppDelayMapBuilder');
}

