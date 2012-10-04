<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by FieldsPeer::getOMClass()
include_once 'classes/model/Fields.php';

/**
 * Base static class for performing query and update operations on the 'FIELDS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFieldsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'FIELDS';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Fields';

    /** The total number of columns. */
    const NUM_COLUMNS = 15;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the FLD_UID field */
    const FLD_UID = 'FIELDS.FLD_UID';

    /** the column name for the ADD_TAB_UID field */
    const ADD_TAB_UID = 'FIELDS.ADD_TAB_UID';

    /** the column name for the FLD_INDEX field */
    const FLD_INDEX = 'FIELDS.FLD_INDEX';

    /** the column name for the FLD_NAME field */
    const FLD_NAME = 'FIELDS.FLD_NAME';

    /** the column name for the FLD_DESCRIPTION field */
    const FLD_DESCRIPTION = 'FIELDS.FLD_DESCRIPTION';

    /** the column name for the FLD_TYPE field */
    const FLD_TYPE = 'FIELDS.FLD_TYPE';

    /** the column name for the FLD_SIZE field */
    const FLD_SIZE = 'FIELDS.FLD_SIZE';

    /** the column name for the FLD_NULL field */
    const FLD_NULL = 'FIELDS.FLD_NULL';

    /** the column name for the FLD_AUTO_INCREMENT field */
    const FLD_AUTO_INCREMENT = 'FIELDS.FLD_AUTO_INCREMENT';

    /** the column name for the FLD_KEY field */
    const FLD_KEY = 'FIELDS.FLD_KEY';

    /** the column name for the FLD_FOREIGN_KEY field */
    const FLD_FOREIGN_KEY = 'FIELDS.FLD_FOREIGN_KEY';

    /** the column name for the FLD_FOREIGN_KEY_TABLE field */
    const FLD_FOREIGN_KEY_TABLE = 'FIELDS.FLD_FOREIGN_KEY_TABLE';

    /** the column name for the FLD_DYN_NAME field */
    const FLD_DYN_NAME = 'FIELDS.FLD_DYN_NAME';

    /** the column name for the FLD_DYN_UID field */
    const FLD_DYN_UID = 'FIELDS.FLD_DYN_UID';

    /** the column name for the FLD_FILTER field */
    const FLD_FILTER = 'FIELDS.FLD_FILTER';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('FldUid', 'AddTabUid', 'FldIndex', 'FldName', 'FldDescription', 'FldType', 'FldSize', 'FldNull', 'FldAutoIncrement', 'FldKey', 'FldForeignKey', 'FldForeignKeyTable', 'FldDynName', 'FldDynUid', 'FldFilter', ),
        BasePeer::TYPE_COLNAME => array (FieldsPeer::FLD_UID, FieldsPeer::ADD_TAB_UID, FieldsPeer::FLD_INDEX, FieldsPeer::FLD_NAME, FieldsPeer::FLD_DESCRIPTION, FieldsPeer::FLD_TYPE, FieldsPeer::FLD_SIZE, FieldsPeer::FLD_NULL, FieldsPeer::FLD_AUTO_INCREMENT, FieldsPeer::FLD_KEY, FieldsPeer::FLD_FOREIGN_KEY, FieldsPeer::FLD_FOREIGN_KEY_TABLE, FieldsPeer::FLD_DYN_NAME, FieldsPeer::FLD_DYN_UID, FieldsPeer::FLD_FILTER, ),
        BasePeer::TYPE_FIELDNAME => array ('FLD_UID', 'ADD_TAB_UID', 'FLD_INDEX', 'FLD_NAME', 'FLD_DESCRIPTION', 'FLD_TYPE', 'FLD_SIZE', 'FLD_NULL', 'FLD_AUTO_INCREMENT', 'FLD_KEY', 'FLD_FOREIGN_KEY', 'FLD_FOREIGN_KEY_TABLE', 'FLD_DYN_NAME', 'FLD_DYN_UID', 'FLD_FILTER', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('FldUid' => 0, 'AddTabUid' => 1, 'FldIndex' => 2, 'FldName' => 3, 'FldDescription' => 4, 'FldType' => 5, 'FldSize' => 6, 'FldNull' => 7, 'FldAutoIncrement' => 8, 'FldKey' => 9, 'FldForeignKey' => 10, 'FldForeignKeyTable' => 11, 'FldDynName' => 12, 'FldDynUid' => 13, 'FldFilter' => 14, ),
        BasePeer::TYPE_COLNAME => array (FieldsPeer::FLD_UID => 0, FieldsPeer::ADD_TAB_UID => 1, FieldsPeer::FLD_INDEX => 2, FieldsPeer::FLD_NAME => 3, FieldsPeer::FLD_DESCRIPTION => 4, FieldsPeer::FLD_TYPE => 5, FieldsPeer::FLD_SIZE => 6, FieldsPeer::FLD_NULL => 7, FieldsPeer::FLD_AUTO_INCREMENT => 8, FieldsPeer::FLD_KEY => 9, FieldsPeer::FLD_FOREIGN_KEY => 10, FieldsPeer::FLD_FOREIGN_KEY_TABLE => 11, FieldsPeer::FLD_DYN_NAME => 12, FieldsPeer::FLD_DYN_UID => 13, FieldsPeer::FLD_FILTER => 14, ),
        BasePeer::TYPE_FIELDNAME => array ('FLD_UID' => 0, 'ADD_TAB_UID' => 1, 'FLD_INDEX' => 2, 'FLD_NAME' => 3, 'FLD_DESCRIPTION' => 4, 'FLD_TYPE' => 5, 'FLD_SIZE' => 6, 'FLD_NULL' => 7, 'FLD_AUTO_INCREMENT' => 8, 'FLD_KEY' => 9, 'FLD_FOREIGN_KEY' => 10, 'FLD_FOREIGN_KEY_TABLE' => 11, 'FLD_DYN_NAME' => 12, 'FLD_DYN_UID' => 13, 'FLD_FILTER' => 14, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/FieldsMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.FieldsMapBuilder');
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
            $map = FieldsPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. FieldsPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(FieldsPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(FieldsPeer::FLD_UID);

        $criteria->addSelectColumn(FieldsPeer::ADD_TAB_UID);

        $criteria->addSelectColumn(FieldsPeer::FLD_INDEX);

        $criteria->addSelectColumn(FieldsPeer::FLD_NAME);

        $criteria->addSelectColumn(FieldsPeer::FLD_DESCRIPTION);

        $criteria->addSelectColumn(FieldsPeer::FLD_TYPE);

        $criteria->addSelectColumn(FieldsPeer::FLD_SIZE);

        $criteria->addSelectColumn(FieldsPeer::FLD_NULL);

        $criteria->addSelectColumn(FieldsPeer::FLD_AUTO_INCREMENT);

        $criteria->addSelectColumn(FieldsPeer::FLD_KEY);

        $criteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY);

        $criteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY_TABLE);

        $criteria->addSelectColumn(FieldsPeer::FLD_DYN_NAME);

        $criteria->addSelectColumn(FieldsPeer::FLD_DYN_UID);

        $criteria->addSelectColumn(FieldsPeer::FLD_FILTER);

    }

    const COUNT = 'COUNT(FIELDS.FLD_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT FIELDS.FLD_UID)';

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
            $criteria->addSelectColumn(FieldsPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(FieldsPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = FieldsPeer::doSelectRS($criteria, $con);
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
     * @return     Fields
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = FieldsPeer::doSelect($critcopy, $con);
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
        return FieldsPeer::populateObjects(FieldsPeer::doSelectRS($criteria, $con));
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
            FieldsPeer::addSelectColumns($criteria);
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
        $cls = FieldsPeer::getOMClass();
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
        return FieldsPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Fields or Criteria object.
     *
     * @param      mixed $values Criteria or Fields object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Fields object
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
     * Method perform an UPDATE on the database, given a Fields or Criteria object.
     *
     * @param      mixed $values Criteria or Fields object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(FieldsPeer::FLD_UID);
            $selectCriteria->add(FieldsPeer::FLD_UID, $criteria->remove(FieldsPeer::FLD_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the FIELDS table.
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
            $affectedRows += BasePeer::doDeleteAll(FieldsPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Fields or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Fields object or primary key or array of primary keys
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
            $con = Propel::getConnection(FieldsPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Fields) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(FieldsPeer::FLD_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Fields object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Fields $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Fields $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(FieldsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(FieldsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(FieldsPeer::DATABASE_NAME, FieldsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Fields
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(FieldsPeer::DATABASE_NAME);

        $criteria->add(FieldsPeer::FLD_UID, $pk);


        $v = FieldsPeer::doSelect($criteria, $con);

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
            $criteria->add(FieldsPeer::FLD_UID, $pks, Criteria::IN);
            $objs = FieldsPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseFieldsPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/FieldsMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.FieldsMapBuilder');
}

