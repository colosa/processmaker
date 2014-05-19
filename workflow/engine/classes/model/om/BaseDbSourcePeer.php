<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by DbSourcePeer::getOMClass()
include_once 'classes/model/DbSource.php';

/**
 * Base static class for performing query and update operations on the 'DB_SOURCE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDbSourcePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'DB_SOURCE';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.DbSource';

    /** The total number of columns. */
    const NUM_COLUMNS = 11;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the DBS_UID field */
    const DBS_UID = 'DB_SOURCE.DBS_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'DB_SOURCE.PRO_UID';

    /** the column name for the DBS_TYPE field */
    const DBS_TYPE = 'DB_SOURCE.DBS_TYPE';

    /** the column name for the DBS_SERVER field */
    const DBS_SERVER = 'DB_SOURCE.DBS_SERVER';

    /** the column name for the DBS_DATABASE_NAME field */
    const DBS_DATABASE_NAME = 'DB_SOURCE.DBS_DATABASE_NAME';

    /** the column name for the DBS_USERNAME field */
    const DBS_USERNAME = 'DB_SOURCE.DBS_USERNAME';

    /** the column name for the DBS_PASSWORD field */
    const DBS_PASSWORD = 'DB_SOURCE.DBS_PASSWORD';

    /** the column name for the DBS_PORT field */
    const DBS_PORT = 'DB_SOURCE.DBS_PORT';

    /** the column name for the DBS_ENCODE field */
    const DBS_ENCODE = 'DB_SOURCE.DBS_ENCODE';

    /** the column name for the DBS_CONNECTION_TYPE field */
    const DBS_CONNECTION_TYPE = 'DB_SOURCE.DBS_CONNECTION_TYPE';

    /** the column name for the DBS_TNS field */
    const DBS_TNS = 'DB_SOURCE.DBS_TNS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbsUid', 'ProUid', 'DbsType', 'DbsServer', 'DbsDatabaseName', 'DbsUsername', 'DbsPassword', 'DbsPort', 'DbsEncode', 'DbsConnectionType', 'DbsTns', ),
        BasePeer::TYPE_COLNAME => array (DbSourcePeer::DBS_UID, DbSourcePeer::PRO_UID, DbSourcePeer::DBS_TYPE, DbSourcePeer::DBS_SERVER, DbSourcePeer::DBS_DATABASE_NAME, DbSourcePeer::DBS_USERNAME, DbSourcePeer::DBS_PASSWORD, DbSourcePeer::DBS_PORT, DbSourcePeer::DBS_ENCODE, DbSourcePeer::DBS_CONNECTION_TYPE, DbSourcePeer::DBS_TNS, ),
        BasePeer::TYPE_FIELDNAME => array ('DBS_UID', 'PRO_UID', 'DBS_TYPE', 'DBS_SERVER', 'DBS_DATABASE_NAME', 'DBS_USERNAME', 'DBS_PASSWORD', 'DBS_PORT', 'DBS_ENCODE', 'DBS_CONNECTION_TYPE', 'DBS_TNS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbsUid' => 0, 'ProUid' => 1, 'DbsType' => 2, 'DbsServer' => 3, 'DbsDatabaseName' => 4, 'DbsUsername' => 5, 'DbsPassword' => 6, 'DbsPort' => 7, 'DbsEncode' => 8, 'DbsConnectionType' => 9, 'DbsTns' => 10, ),
        BasePeer::TYPE_COLNAME => array (DbSourcePeer::DBS_UID => 0, DbSourcePeer::PRO_UID => 1, DbSourcePeer::DBS_TYPE => 2, DbSourcePeer::DBS_SERVER => 3, DbSourcePeer::DBS_DATABASE_NAME => 4, DbSourcePeer::DBS_USERNAME => 5, DbSourcePeer::DBS_PASSWORD => 6, DbSourcePeer::DBS_PORT => 7, DbSourcePeer::DBS_ENCODE => 8, DbSourcePeer::DBS_CONNECTION_TYPE => 9, DbSourcePeer::DBS_TNS => 10, ),
        BasePeer::TYPE_FIELDNAME => array ('DBS_UID' => 0, 'PRO_UID' => 1, 'DBS_TYPE' => 2, 'DBS_SERVER' => 3, 'DBS_DATABASE_NAME' => 4, 'DBS_USERNAME' => 5, 'DBS_PASSWORD' => 6, 'DBS_PORT' => 7, 'DBS_ENCODE' => 8, 'DBS_CONNECTION_TYPE' => 9, 'DBS_TNS' => 10, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/DbSourceMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.DbSourceMapBuilder');
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
            $map = DbSourcePeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. DbSourcePeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(DbSourcePeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(DbSourcePeer::DBS_UID);

        $criteria->addSelectColumn(DbSourcePeer::PRO_UID);

        $criteria->addSelectColumn(DbSourcePeer::DBS_TYPE);

        $criteria->addSelectColumn(DbSourcePeer::DBS_SERVER);

        $criteria->addSelectColumn(DbSourcePeer::DBS_DATABASE_NAME);

        $criteria->addSelectColumn(DbSourcePeer::DBS_USERNAME);

        $criteria->addSelectColumn(DbSourcePeer::DBS_PASSWORD);

        $criteria->addSelectColumn(DbSourcePeer::DBS_PORT);

        $criteria->addSelectColumn(DbSourcePeer::DBS_ENCODE);

        $criteria->addSelectColumn(DbSourcePeer::DBS_CONNECTION_TYPE);

        $criteria->addSelectColumn(DbSourcePeer::DBS_TNS);

    }

    const COUNT = 'COUNT(DB_SOURCE.DBS_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT DB_SOURCE.DBS_UID)';

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
            $criteria->addSelectColumn(DbSourcePeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(DbSourcePeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = DbSourcePeer::doSelectRS($criteria, $con);
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
     * @return     DbSource
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = DbSourcePeer::doSelect($critcopy, $con);
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
        return DbSourcePeer::populateObjects(DbSourcePeer::doSelectRS($criteria, $con));
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
            DbSourcePeer::addSelectColumns($criteria);
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
        $cls = DbSourcePeer::getOMClass();
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
        return DbSourcePeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a DbSource or Criteria object.
     *
     * @param      mixed $values Criteria or DbSource object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from DbSource object
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
     * Method perform an UPDATE on the database, given a DbSource or Criteria object.
     *
     * @param      mixed $values Criteria or DbSource object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(DbSourcePeer::DBS_UID);
            $selectCriteria->add(DbSourcePeer::DBS_UID, $criteria->remove(DbSourcePeer::DBS_UID), $comparison);

            $comparison = $criteria->getComparison(DbSourcePeer::PRO_UID);
            $selectCriteria->add(DbSourcePeer::PRO_UID, $criteria->remove(DbSourcePeer::PRO_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the DB_SOURCE table.
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
            $affectedRows += BasePeer::doDeleteAll(DbSourcePeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a DbSource or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or DbSource object or primary key or array of primary keys
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
            $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof DbSource) {

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
            }

            $criteria->add(DbSourcePeer::DBS_UID, $vals[0], Criteria::IN);
            $criteria->add(DbSourcePeer::PRO_UID, $vals[1], Criteria::IN);
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
     * Validates all modified columns of given DbSource object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      DbSource $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(DbSource $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(DbSourcePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(DbSourcePeer::TABLE_NAME);

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

        return BasePeer::doValidate(DbSourcePeer::DATABASE_NAME, DbSourcePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $dbs_uid
       * @param string $pro_uid
        * @param      Connection $con
     * @return     DbSource
     */
    public static function retrieveByPK($dbs_uid, $pro_uid, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(DbSourcePeer::DBS_UID, $dbs_uid);
        $criteria->add(DbSourcePeer::PRO_UID, $pro_uid);
        $v = DbSourcePeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseDbSourcePeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/DbSourceMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.DbSourceMapBuilder');
}

