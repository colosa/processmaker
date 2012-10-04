<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by RoutePeer::getOMClass()
include_once 'classes/model/Route.php';

/**
 * Base static class for performing query and update operations on the 'ROUTE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseRoutePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'ROUTE';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Route';

    /** The total number of columns. */
    const NUM_COLUMNS = 17;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the ROU_UID field */
    const ROU_UID = 'ROUTE.ROU_UID';

    /** the column name for the ROU_PARENT field */
    const ROU_PARENT = 'ROUTE.ROU_PARENT';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'ROUTE.PRO_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'ROUTE.TAS_UID';

    /** the column name for the ROU_NEXT_TASK field */
    const ROU_NEXT_TASK = 'ROUTE.ROU_NEXT_TASK';

    /** the column name for the ROU_CASE field */
    const ROU_CASE = 'ROUTE.ROU_CASE';

    /** the column name for the ROU_TYPE field */
    const ROU_TYPE = 'ROUTE.ROU_TYPE';

    /** the column name for the ROU_CONDITION field */
    const ROU_CONDITION = 'ROUTE.ROU_CONDITION';

    /** the column name for the ROU_TO_LAST_USER field */
    const ROU_TO_LAST_USER = 'ROUTE.ROU_TO_LAST_USER';

    /** the column name for the ROU_OPTIONAL field */
    const ROU_OPTIONAL = 'ROUTE.ROU_OPTIONAL';

    /** the column name for the ROU_SEND_EMAIL field */
    const ROU_SEND_EMAIL = 'ROUTE.ROU_SEND_EMAIL';

    /** the column name for the ROU_SOURCEANCHOR field */
    const ROU_SOURCEANCHOR = 'ROUTE.ROU_SOURCEANCHOR';

    /** the column name for the ROU_TARGETANCHOR field */
    const ROU_TARGETANCHOR = 'ROUTE.ROU_TARGETANCHOR';

    /** the column name for the ROU_TO_PORT field */
    const ROU_TO_PORT = 'ROUTE.ROU_TO_PORT';

    /** the column name for the ROU_FROM_PORT field */
    const ROU_FROM_PORT = 'ROUTE.ROU_FROM_PORT';

    /** the column name for the ROU_EVN_UID field */
    const ROU_EVN_UID = 'ROUTE.ROU_EVN_UID';

    /** the column name for the GAT_UID field */
    const GAT_UID = 'ROUTE.GAT_UID';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('RouUid', 'RouParent', 'ProUid', 'TasUid', 'RouNextTask', 'RouCase', 'RouType', 'RouCondition', 'RouToLastUser', 'RouOptional', 'RouSendEmail', 'RouSourceanchor', 'RouTargetanchor', 'RouToPort', 'RouFromPort', 'RouEvnUid', 'GatUid', ),
        BasePeer::TYPE_COLNAME => array (RoutePeer::ROU_UID, RoutePeer::ROU_PARENT, RoutePeer::PRO_UID, RoutePeer::TAS_UID, RoutePeer::ROU_NEXT_TASK, RoutePeer::ROU_CASE, RoutePeer::ROU_TYPE, RoutePeer::ROU_CONDITION, RoutePeer::ROU_TO_LAST_USER, RoutePeer::ROU_OPTIONAL, RoutePeer::ROU_SEND_EMAIL, RoutePeer::ROU_SOURCEANCHOR, RoutePeer::ROU_TARGETANCHOR, RoutePeer::ROU_TO_PORT, RoutePeer::ROU_FROM_PORT, RoutePeer::ROU_EVN_UID, RoutePeer::GAT_UID, ),
        BasePeer::TYPE_FIELDNAME => array ('ROU_UID', 'ROU_PARENT', 'PRO_UID', 'TAS_UID', 'ROU_NEXT_TASK', 'ROU_CASE', 'ROU_TYPE', 'ROU_CONDITION', 'ROU_TO_LAST_USER', 'ROU_OPTIONAL', 'ROU_SEND_EMAIL', 'ROU_SOURCEANCHOR', 'ROU_TARGETANCHOR', 'ROU_TO_PORT', 'ROU_FROM_PORT', 'ROU_EVN_UID', 'GAT_UID', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('RouUid' => 0, 'RouParent' => 1, 'ProUid' => 2, 'TasUid' => 3, 'RouNextTask' => 4, 'RouCase' => 5, 'RouType' => 6, 'RouCondition' => 7, 'RouToLastUser' => 8, 'RouOptional' => 9, 'RouSendEmail' => 10, 'RouSourceanchor' => 11, 'RouTargetanchor' => 12, 'RouToPort' => 13, 'RouFromPort' => 14, 'RouEvnUid' => 15, 'GatUid' => 16, ),
        BasePeer::TYPE_COLNAME => array (RoutePeer::ROU_UID => 0, RoutePeer::ROU_PARENT => 1, RoutePeer::PRO_UID => 2, RoutePeer::TAS_UID => 3, RoutePeer::ROU_NEXT_TASK => 4, RoutePeer::ROU_CASE => 5, RoutePeer::ROU_TYPE => 6, RoutePeer::ROU_CONDITION => 7, RoutePeer::ROU_TO_LAST_USER => 8, RoutePeer::ROU_OPTIONAL => 9, RoutePeer::ROU_SEND_EMAIL => 10, RoutePeer::ROU_SOURCEANCHOR => 11, RoutePeer::ROU_TARGETANCHOR => 12, RoutePeer::ROU_TO_PORT => 13, RoutePeer::ROU_FROM_PORT => 14, RoutePeer::ROU_EVN_UID => 15, RoutePeer::GAT_UID => 16, ),
        BasePeer::TYPE_FIELDNAME => array ('ROU_UID' => 0, 'ROU_PARENT' => 1, 'PRO_UID' => 2, 'TAS_UID' => 3, 'ROU_NEXT_TASK' => 4, 'ROU_CASE' => 5, 'ROU_TYPE' => 6, 'ROU_CONDITION' => 7, 'ROU_TO_LAST_USER' => 8, 'ROU_OPTIONAL' => 9, 'ROU_SEND_EMAIL' => 10, 'ROU_SOURCEANCHOR' => 11, 'ROU_TARGETANCHOR' => 12, 'ROU_TO_PORT' => 13, 'ROU_FROM_PORT' => 14, 'ROU_EVN_UID' => 15, 'GAT_UID' => 16, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/RouteMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.RouteMapBuilder');
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
            $map = RoutePeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. RoutePeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(RoutePeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(RoutePeer::ROU_UID);

        $criteria->addSelectColumn(RoutePeer::ROU_PARENT);

        $criteria->addSelectColumn(RoutePeer::PRO_UID);

        $criteria->addSelectColumn(RoutePeer::TAS_UID);

        $criteria->addSelectColumn(RoutePeer::ROU_NEXT_TASK);

        $criteria->addSelectColumn(RoutePeer::ROU_CASE);

        $criteria->addSelectColumn(RoutePeer::ROU_TYPE);

        $criteria->addSelectColumn(RoutePeer::ROU_CONDITION);

        $criteria->addSelectColumn(RoutePeer::ROU_TO_LAST_USER);

        $criteria->addSelectColumn(RoutePeer::ROU_OPTIONAL);

        $criteria->addSelectColumn(RoutePeer::ROU_SEND_EMAIL);

        $criteria->addSelectColumn(RoutePeer::ROU_SOURCEANCHOR);

        $criteria->addSelectColumn(RoutePeer::ROU_TARGETANCHOR);

        $criteria->addSelectColumn(RoutePeer::ROU_TO_PORT);

        $criteria->addSelectColumn(RoutePeer::ROU_FROM_PORT);

        $criteria->addSelectColumn(RoutePeer::ROU_EVN_UID);

        $criteria->addSelectColumn(RoutePeer::GAT_UID);

    }

    const COUNT = 'COUNT(ROUTE.ROU_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT ROUTE.ROU_UID)';

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
            $criteria->addSelectColumn(RoutePeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(RoutePeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = RoutePeer::doSelectRS($criteria, $con);
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
     * @return     Route
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = RoutePeer::doSelect($critcopy, $con);
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
        return RoutePeer::populateObjects(RoutePeer::doSelectRS($criteria, $con));
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
            RoutePeer::addSelectColumns($criteria);
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
        $cls = RoutePeer::getOMClass();
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
        return RoutePeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Route or Criteria object.
     *
     * @param      mixed $values Criteria or Route object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Route object
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
     * Method perform an UPDATE on the database, given a Route or Criteria object.
     *
     * @param      mixed $values Criteria or Route object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(RoutePeer::ROU_UID);
            $selectCriteria->add(RoutePeer::ROU_UID, $criteria->remove(RoutePeer::ROU_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the ROUTE table.
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
            $affectedRows += BasePeer::doDeleteAll(RoutePeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Route or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Route object or primary key or array of primary keys
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
            $con = Propel::getConnection(RoutePeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Route) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(RoutePeer::ROU_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Route object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Route $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Route $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(RoutePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(RoutePeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_UID))
            $columns[RoutePeer::ROU_UID] = $obj->getRouUid();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::PRO_UID))
            $columns[RoutePeer::PRO_UID] = $obj->getProUid();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::TAS_UID))
            $columns[RoutePeer::TAS_UID] = $obj->getTasUid();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_NEXT_TASK))
            $columns[RoutePeer::ROU_NEXT_TASK] = $obj->getRouNextTask();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_TYPE))
            $columns[RoutePeer::ROU_TYPE] = $obj->getRouType();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_TO_LAST_USER))
            $columns[RoutePeer::ROU_TO_LAST_USER] = $obj->getRouToLastUser();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_OPTIONAL))
            $columns[RoutePeer::ROU_OPTIONAL] = $obj->getRouOptional();

        if ($obj->isNew() || $obj->isColumnModified(RoutePeer::ROU_SEND_EMAIL))
            $columns[RoutePeer::ROU_SEND_EMAIL] = $obj->getRouSendEmail();

        }

        return BasePeer::doValidate(RoutePeer::DATABASE_NAME, RoutePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Route
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(RoutePeer::DATABASE_NAME);

        $criteria->add(RoutePeer::ROU_UID, $pk);


        $v = RoutePeer::doSelect($criteria, $con);

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
            $criteria->add(RoutePeer::ROU_UID, $pks, Criteria::IN);
            $objs = RoutePeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseRoutePeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/RouteMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.RouteMapBuilder');
}

