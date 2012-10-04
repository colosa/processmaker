<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SubApplicationPeer::getOMClass()
include_once 'classes/model/SubApplication.php';

/**
 * Base static class for performing query and update operations on the 'SUB_APPLICATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSubApplicationPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'SUB_APPLICATION';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.SubApplication';

    /** The total number of columns. */
    const NUM_COLUMNS = 9;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_UID field */
    const APP_UID = 'SUB_APPLICATION.APP_UID';

    /** the column name for the APP_PARENT field */
    const APP_PARENT = 'SUB_APPLICATION.APP_PARENT';

    /** the column name for the DEL_INDEX_PARENT field */
    const DEL_INDEX_PARENT = 'SUB_APPLICATION.DEL_INDEX_PARENT';

    /** the column name for the DEL_THREAD_PARENT field */
    const DEL_THREAD_PARENT = 'SUB_APPLICATION.DEL_THREAD_PARENT';

    /** the column name for the SA_STATUS field */
    const SA_STATUS = 'SUB_APPLICATION.SA_STATUS';

    /** the column name for the SA_VALUES_OUT field */
    const SA_VALUES_OUT = 'SUB_APPLICATION.SA_VALUES_OUT';

    /** the column name for the SA_VALUES_IN field */
    const SA_VALUES_IN = 'SUB_APPLICATION.SA_VALUES_IN';

    /** the column name for the SA_INIT_DATE field */
    const SA_INIT_DATE = 'SUB_APPLICATION.SA_INIT_DATE';

    /** the column name for the SA_FINISH_DATE field */
    const SA_FINISH_DATE = 'SUB_APPLICATION.SA_FINISH_DATE';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid', 'AppParent', 'DelIndexParent', 'DelThreadParent', 'SaStatus', 'SaValuesOut', 'SaValuesIn', 'SaInitDate', 'SaFinishDate', ),
        BasePeer::TYPE_COLNAME => array (SubApplicationPeer::APP_UID, SubApplicationPeer::APP_PARENT, SubApplicationPeer::DEL_INDEX_PARENT, SubApplicationPeer::DEL_THREAD_PARENT, SubApplicationPeer::SA_STATUS, SubApplicationPeer::SA_VALUES_OUT, SubApplicationPeer::SA_VALUES_IN, SubApplicationPeer::SA_INIT_DATE, SubApplicationPeer::SA_FINISH_DATE, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID', 'APP_PARENT', 'DEL_INDEX_PARENT', 'DEL_THREAD_PARENT', 'SA_STATUS', 'SA_VALUES_OUT', 'SA_VALUES_IN', 'SA_INIT_DATE', 'SA_FINISH_DATE', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid' => 0, 'AppParent' => 1, 'DelIndexParent' => 2, 'DelThreadParent' => 3, 'SaStatus' => 4, 'SaValuesOut' => 5, 'SaValuesIn' => 6, 'SaInitDate' => 7, 'SaFinishDate' => 8, ),
        BasePeer::TYPE_COLNAME => array (SubApplicationPeer::APP_UID => 0, SubApplicationPeer::APP_PARENT => 1, SubApplicationPeer::DEL_INDEX_PARENT => 2, SubApplicationPeer::DEL_THREAD_PARENT => 3, SubApplicationPeer::SA_STATUS => 4, SubApplicationPeer::SA_VALUES_OUT => 5, SubApplicationPeer::SA_VALUES_IN => 6, SubApplicationPeer::SA_INIT_DATE => 7, SubApplicationPeer::SA_FINISH_DATE => 8, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID' => 0, 'APP_PARENT' => 1, 'DEL_INDEX_PARENT' => 2, 'DEL_THREAD_PARENT' => 3, 'SA_STATUS' => 4, 'SA_VALUES_OUT' => 5, 'SA_VALUES_IN' => 6, 'SA_INIT_DATE' => 7, 'SA_FINISH_DATE' => 8, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/SubApplicationMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.SubApplicationMapBuilder');
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
            $map = SubApplicationPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. SubApplicationPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SubApplicationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(SubApplicationPeer::APP_UID);

        $criteria->addSelectColumn(SubApplicationPeer::APP_PARENT);

        $criteria->addSelectColumn(SubApplicationPeer::DEL_INDEX_PARENT);

        $criteria->addSelectColumn(SubApplicationPeer::DEL_THREAD_PARENT);

        $criteria->addSelectColumn(SubApplicationPeer::SA_STATUS);

        $criteria->addSelectColumn(SubApplicationPeer::SA_VALUES_OUT);

        $criteria->addSelectColumn(SubApplicationPeer::SA_VALUES_IN);

        $criteria->addSelectColumn(SubApplicationPeer::SA_INIT_DATE);

        $criteria->addSelectColumn(SubApplicationPeer::SA_FINISH_DATE);

    }

    const COUNT = 'COUNT(SUB_APPLICATION.APP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT SUB_APPLICATION.APP_UID)';

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
            $criteria->addSelectColumn(SubApplicationPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(SubApplicationPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = SubApplicationPeer::doSelectRS($criteria, $con);
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
     * @return     SubApplication
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SubApplicationPeer::doSelect($critcopy, $con);
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
        return SubApplicationPeer::populateObjects(SubApplicationPeer::doSelectRS($criteria, $con));
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
            SubApplicationPeer::addSelectColumns($criteria);
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
        $cls = SubApplicationPeer::getOMClass();
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
        return SubApplicationPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a SubApplication or Criteria object.
     *
     * @param      mixed $values Criteria or SubApplication object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from SubApplication object
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
     * Method perform an UPDATE on the database, given a SubApplication or Criteria object.
     *
     * @param      mixed $values Criteria or SubApplication object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(SubApplicationPeer::APP_UID);
            $selectCriteria->add(SubApplicationPeer::APP_UID, $criteria->remove(SubApplicationPeer::APP_UID), $comparison);

            $comparison = $criteria->getComparison(SubApplicationPeer::APP_PARENT);
            $selectCriteria->add(SubApplicationPeer::APP_PARENT, $criteria->remove(SubApplicationPeer::APP_PARENT), $comparison);

            $comparison = $criteria->getComparison(SubApplicationPeer::DEL_INDEX_PARENT);
            $selectCriteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $criteria->remove(SubApplicationPeer::DEL_INDEX_PARENT), $comparison);

            $comparison = $criteria->getComparison(SubApplicationPeer::DEL_THREAD_PARENT);
            $selectCriteria->add(SubApplicationPeer::DEL_THREAD_PARENT, $criteria->remove(SubApplicationPeer::DEL_THREAD_PARENT), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the SUB_APPLICATION table.
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
            $affectedRows += BasePeer::doDeleteAll(SubApplicationPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a SubApplication or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or SubApplication object or primary key or array of primary keys
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
            $con = Propel::getConnection(SubApplicationPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof SubApplication) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey
            // values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            $vals = array();
            foreach ($values as $value) {

                $vals[0][] = $value[0];
                $vals[1][] = $value[1];
                $vals[2][] = $value[2];
                $vals[3][] = $value[3];
            }

            $criteria->add(SubApplicationPeer::APP_UID, $vals[0], Criteria::IN);
            $criteria->add(SubApplicationPeer::APP_PARENT, $vals[1], Criteria::IN);
            $criteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $vals[2], Criteria::IN);
            $criteria->add(SubApplicationPeer::DEL_THREAD_PARENT, $vals[3], Criteria::IN);
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
     * Validates all modified columns of given SubApplication object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      SubApplication $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(SubApplication $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SubApplicationPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SubApplicationPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(SubApplicationPeer::SA_STATUS))
            $columns[SubApplicationPeer::SA_STATUS] = $obj->getSaStatus();

        }

        return BasePeer::doValidate(SubApplicationPeer::DATABASE_NAME, SubApplicationPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $app_uid
       * @param string $app_parent
       * @param int $del_index_parent
       * @param int $del_thread_parent
        * @param      Connection $con
     * @return     SubApplication
     */
    public static function retrieveByPK($app_uid, $app_parent, $del_index_parent, $del_thread_parent, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(SubApplicationPeer::APP_UID, $app_uid);
        $criteria->add(SubApplicationPeer::APP_PARENT, $app_parent);
        $criteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $del_index_parent);
        $criteria->add(SubApplicationPeer::DEL_THREAD_PARENT, $del_thread_parent);
        $v = SubApplicationPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseSubApplicationPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/SubApplicationMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.SubApplicationMapBuilder');
}

