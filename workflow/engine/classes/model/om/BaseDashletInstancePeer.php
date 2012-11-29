<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by DashletInstancePeer::getOMClass()
include_once 'classes/model/DashletInstance.php';

/**
 * Base static class for performing query and update operations on the 'DASHLET_INSTANCE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashletInstancePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'DASHLET_INSTANCE';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.DashletInstance';

    /** The total number of columns. */
    const NUM_COLUMNS = 8;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the DAS_INS_UID field */
    const DAS_INS_UID = 'DASHLET_INSTANCE.DAS_INS_UID';

    /** the column name for the DAS_UID field */
    const DAS_UID = 'DASHLET_INSTANCE.DAS_UID';

    /** the column name for the DAS_INS_OWNER_TYPE field */
    const DAS_INS_OWNER_TYPE = 'DASHLET_INSTANCE.DAS_INS_OWNER_TYPE';

    /** the column name for the DAS_INS_OWNER_UID field */
    const DAS_INS_OWNER_UID = 'DASHLET_INSTANCE.DAS_INS_OWNER_UID';

    /** the column name for the DAS_INS_ADDITIONAL_PROPERTIES field */
    const DAS_INS_ADDITIONAL_PROPERTIES = 'DASHLET_INSTANCE.DAS_INS_ADDITIONAL_PROPERTIES';

    /** the column name for the DAS_INS_CREATE_DATE field */
    const DAS_INS_CREATE_DATE = 'DASHLET_INSTANCE.DAS_INS_CREATE_DATE';

    /** the column name for the DAS_INS_UPDATE_DATE field */
    const DAS_INS_UPDATE_DATE = 'DASHLET_INSTANCE.DAS_INS_UPDATE_DATE';

    /** the column name for the DAS_INS_STATUS field */
    const DAS_INS_STATUS = 'DASHLET_INSTANCE.DAS_INS_STATUS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DasInsUid', 'DasUid', 'DasInsOwnerType', 'DasInsOwnerUid', 'DasInsAdditionalProperties', 'DasInsCreateDate', 'DasInsUpdateDate', 'DasInsStatus', ),
        BasePeer::TYPE_COLNAME => array (DashletInstancePeer::DAS_INS_UID, DashletInstancePeer::DAS_UID, DashletInstancePeer::DAS_INS_OWNER_TYPE, DashletInstancePeer::DAS_INS_OWNER_UID, DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES, DashletInstancePeer::DAS_INS_CREATE_DATE, DashletInstancePeer::DAS_INS_UPDATE_DATE, DashletInstancePeer::DAS_INS_STATUS, ),
        BasePeer::TYPE_FIELDNAME => array ('DAS_INS_UID', 'DAS_UID', 'DAS_INS_OWNER_TYPE', 'DAS_INS_OWNER_UID', 'DAS_INS_ADDITIONAL_PROPERTIES', 'DAS_INS_CREATE_DATE', 'DAS_INS_UPDATE_DATE', 'DAS_INS_STATUS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DasInsUid' => 0, 'DasUid' => 1, 'DasInsOwnerType' => 2, 'DasInsOwnerUid' => 3, 'DasInsAdditionalProperties' => 4, 'DasInsCreateDate' => 5, 'DasInsUpdateDate' => 6, 'DasInsStatus' => 7, ),
        BasePeer::TYPE_COLNAME => array (DashletInstancePeer::DAS_INS_UID => 0, DashletInstancePeer::DAS_UID => 1, DashletInstancePeer::DAS_INS_OWNER_TYPE => 2, DashletInstancePeer::DAS_INS_OWNER_UID => 3, DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES => 4, DashletInstancePeer::DAS_INS_CREATE_DATE => 5, DashletInstancePeer::DAS_INS_UPDATE_DATE => 6, DashletInstancePeer::DAS_INS_STATUS => 7, ),
        BasePeer::TYPE_FIELDNAME => array ('DAS_INS_UID' => 0, 'DAS_UID' => 1, 'DAS_INS_OWNER_TYPE' => 2, 'DAS_INS_OWNER_UID' => 3, 'DAS_INS_ADDITIONAL_PROPERTIES' => 4, 'DAS_INS_CREATE_DATE' => 5, 'DAS_INS_UPDATE_DATE' => 6, 'DAS_INS_STATUS' => 7, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/DashletInstanceMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.DashletInstanceMapBuilder');
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
            $map = DashletInstancePeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. DashletInstancePeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(DashletInstancePeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_UID);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_OWNER_TYPE);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_OWNER_UID);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_CREATE_DATE);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UPDATE_DATE);

        $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_STATUS);

    }

    const COUNT = 'COUNT(DASHLET_INSTANCE.DAS_INS_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT DASHLET_INSTANCE.DAS_INS_UID)';

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
            $criteria->addSelectColumn(DashletInstancePeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(DashletInstancePeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = DashletInstancePeer::doSelectRS($criteria, $con);
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
     * @return     DashletInstance
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = DashletInstancePeer::doSelect($critcopy, $con);
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
        return DashletInstancePeer::populateObjects(DashletInstancePeer::doSelectRS($criteria, $con));
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
            DashletInstancePeer::addSelectColumns($criteria);
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
        $cls = DashletInstancePeer::getOMClass();
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
        return DashletInstancePeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a DashletInstance or Criteria object.
     *
     * @param      mixed $values Criteria or DashletInstance object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from DashletInstance object
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
     * Method perform an UPDATE on the database, given a DashletInstance or Criteria object.
     *
     * @param      mixed $values Criteria or DashletInstance object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(DashletInstancePeer::DAS_INS_UID);
            $selectCriteria->add(DashletInstancePeer::DAS_INS_UID, $criteria->remove(DashletInstancePeer::DAS_INS_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the DASHLET_INSTANCE table.
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
            $affectedRows += BasePeer::doDeleteAll(DashletInstancePeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a DashletInstance or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or DashletInstance object or primary key or array of primary keys
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
            $con = Propel::getConnection(DashletInstancePeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof DashletInstance) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(DashletInstancePeer::DAS_INS_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given DashletInstance object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      DashletInstance $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(DashletInstance $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(DashletInstancePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(DashletInstancePeer::TABLE_NAME);

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

        return BasePeer::doValidate(DashletInstancePeer::DATABASE_NAME, DashletInstancePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     DashletInstance
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(DashletInstancePeer::DATABASE_NAME);

        $criteria->add(DashletInstancePeer::DAS_INS_UID, $pk);


        $v = DashletInstancePeer::doSelect($criteria, $con);

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
            $criteria->add(DashletInstancePeer::DAS_INS_UID, $pks, Criteria::IN);
            $objs = DashletInstancePeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseDashletInstancePeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/DashletInstanceMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.DashletInstanceMapBuilder');
}

