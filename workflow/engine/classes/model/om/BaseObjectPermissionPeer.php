<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ObjectPermissionPeer::getOMClass()
include_once 'classes/model/ObjectPermission.php';

/**
 * Base static class for performing query and update operations on the 'OBJECT_PERMISSION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseObjectPermissionPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'OBJECT_PERMISSION';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.ObjectPermission';

    /** The total number of columns. */
    const NUM_COLUMNS = 11;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the OP_UID field */
    const OP_UID = 'OBJECT_PERMISSION.OP_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'OBJECT_PERMISSION.PRO_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'OBJECT_PERMISSION.TAS_UID';

    /** the column name for the USR_UID field */
    const USR_UID = 'OBJECT_PERMISSION.USR_UID';

    /** the column name for the OP_USER_RELATION field */
    const OP_USER_RELATION = 'OBJECT_PERMISSION.OP_USER_RELATION';

    /** the column name for the OP_TASK_SOURCE field */
    const OP_TASK_SOURCE = 'OBJECT_PERMISSION.OP_TASK_SOURCE';

    /** the column name for the OP_PARTICIPATE field */
    const OP_PARTICIPATE = 'OBJECT_PERMISSION.OP_PARTICIPATE';

    /** the column name for the OP_OBJ_TYPE field */
    const OP_OBJ_TYPE = 'OBJECT_PERMISSION.OP_OBJ_TYPE';

    /** the column name for the OP_OBJ_UID field */
    const OP_OBJ_UID = 'OBJECT_PERMISSION.OP_OBJ_UID';

    /** the column name for the OP_ACTION field */
    const OP_ACTION = 'OBJECT_PERMISSION.OP_ACTION';

    /** the column name for the OP_CASE_STATUS field */
    const OP_CASE_STATUS = 'OBJECT_PERMISSION.OP_CASE_STATUS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('OpUid', 'ProUid', 'TasUid', 'UsrUid', 'OpUserRelation', 'OpTaskSource', 'OpParticipate', 'OpObjType', 'OpObjUid', 'OpAction', 'OpCaseStatus', ),
        BasePeer::TYPE_COLNAME => array (ObjectPermissionPeer::OP_UID, ObjectPermissionPeer::PRO_UID, ObjectPermissionPeer::TAS_UID, ObjectPermissionPeer::USR_UID, ObjectPermissionPeer::OP_USER_RELATION, ObjectPermissionPeer::OP_TASK_SOURCE, ObjectPermissionPeer::OP_PARTICIPATE, ObjectPermissionPeer::OP_OBJ_TYPE, ObjectPermissionPeer::OP_OBJ_UID, ObjectPermissionPeer::OP_ACTION, ObjectPermissionPeer::OP_CASE_STATUS, ),
        BasePeer::TYPE_FIELDNAME => array ('OP_UID', 'PRO_UID', 'TAS_UID', 'USR_UID', 'OP_USER_RELATION', 'OP_TASK_SOURCE', 'OP_PARTICIPATE', 'OP_OBJ_TYPE', 'OP_OBJ_UID', 'OP_ACTION', 'OP_CASE_STATUS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('OpUid' => 0, 'ProUid' => 1, 'TasUid' => 2, 'UsrUid' => 3, 'OpUserRelation' => 4, 'OpTaskSource' => 5, 'OpParticipate' => 6, 'OpObjType' => 7, 'OpObjUid' => 8, 'OpAction' => 9, 'OpCaseStatus' => 10, ),
        BasePeer::TYPE_COLNAME => array (ObjectPermissionPeer::OP_UID => 0, ObjectPermissionPeer::PRO_UID => 1, ObjectPermissionPeer::TAS_UID => 2, ObjectPermissionPeer::USR_UID => 3, ObjectPermissionPeer::OP_USER_RELATION => 4, ObjectPermissionPeer::OP_TASK_SOURCE => 5, ObjectPermissionPeer::OP_PARTICIPATE => 6, ObjectPermissionPeer::OP_OBJ_TYPE => 7, ObjectPermissionPeer::OP_OBJ_UID => 8, ObjectPermissionPeer::OP_ACTION => 9, ObjectPermissionPeer::OP_CASE_STATUS => 10, ),
        BasePeer::TYPE_FIELDNAME => array ('OP_UID' => 0, 'PRO_UID' => 1, 'TAS_UID' => 2, 'USR_UID' => 3, 'OP_USER_RELATION' => 4, 'OP_TASK_SOURCE' => 5, 'OP_PARTICIPATE' => 6, 'OP_OBJ_TYPE' => 7, 'OP_OBJ_UID' => 8, 'OP_ACTION' => 9, 'OP_CASE_STATUS' => 10, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/ObjectPermissionMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.ObjectPermissionMapBuilder');
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
            $map = ObjectPermissionPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. ObjectPermissionPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ObjectPermissionPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_UID);

        $criteria->addSelectColumn(ObjectPermissionPeer::PRO_UID);

        $criteria->addSelectColumn(ObjectPermissionPeer::TAS_UID);

        $criteria->addSelectColumn(ObjectPermissionPeer::USR_UID);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_USER_RELATION);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_TASK_SOURCE);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_PARTICIPATE);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_OBJ_TYPE);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_OBJ_UID);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_ACTION);

        $criteria->addSelectColumn(ObjectPermissionPeer::OP_CASE_STATUS);

    }

    const COUNT = 'COUNT(OBJECT_PERMISSION.OP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT OBJECT_PERMISSION.OP_UID)';

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
            $criteria->addSelectColumn(ObjectPermissionPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(ObjectPermissionPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = ObjectPermissionPeer::doSelectRS($criteria, $con);
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
     * @return     ObjectPermission
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ObjectPermissionPeer::doSelect($critcopy, $con);
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
        return ObjectPermissionPeer::populateObjects(ObjectPermissionPeer::doSelectRS($criteria, $con));
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
            ObjectPermissionPeer::addSelectColumns($criteria);
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
        $cls = ObjectPermissionPeer::getOMClass();
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
        return ObjectPermissionPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a ObjectPermission or Criteria object.
     *
     * @param      mixed $values Criteria or ObjectPermission object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from ObjectPermission object
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
     * Method perform an UPDATE on the database, given a ObjectPermission or Criteria object.
     *
     * @param      mixed $values Criteria or ObjectPermission object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(ObjectPermissionPeer::OP_UID);
            $selectCriteria->add(ObjectPermissionPeer::OP_UID, $criteria->remove(ObjectPermissionPeer::OP_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the OBJECT_PERMISSION table.
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
            $affectedRows += BasePeer::doDeleteAll(ObjectPermissionPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a ObjectPermission or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or ObjectPermission object or primary key or array of primary keys
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
            $con = Propel::getConnection(ObjectPermissionPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof ObjectPermission) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(ObjectPermissionPeer::OP_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given ObjectPermission object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      ObjectPermission $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(ObjectPermission $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ObjectPermissionPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ObjectPermissionPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_UID))
            $columns[ObjectPermissionPeer::OP_UID] = $obj->getOpUid();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::PRO_UID))
            $columns[ObjectPermissionPeer::PRO_UID] = $obj->getProUid();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::TAS_UID))
            $columns[ObjectPermissionPeer::TAS_UID] = $obj->getTasUid();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::USR_UID))
            $columns[ObjectPermissionPeer::USR_UID] = $obj->getUsrUid();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_USER_RELATION))
            $columns[ObjectPermissionPeer::OP_USER_RELATION] = $obj->getOpUserRelation();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_TASK_SOURCE))
            $columns[ObjectPermissionPeer::OP_TASK_SOURCE] = $obj->getOpTaskSource();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_PARTICIPATE))
            $columns[ObjectPermissionPeer::OP_PARTICIPATE] = $obj->getOpParticipate();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_OBJ_TYPE))
            $columns[ObjectPermissionPeer::OP_OBJ_TYPE] = $obj->getOpObjType();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_OBJ_UID))
            $columns[ObjectPermissionPeer::OP_OBJ_UID] = $obj->getOpObjUid();

        if ($obj->isNew() || $obj->isColumnModified(ObjectPermissionPeer::OP_ACTION))
            $columns[ObjectPermissionPeer::OP_ACTION] = $obj->getOpAction();

        }

        return BasePeer::doValidate(ObjectPermissionPeer::DATABASE_NAME, ObjectPermissionPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     ObjectPermission
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(ObjectPermissionPeer::DATABASE_NAME);

        $criteria->add(ObjectPermissionPeer::OP_UID, $pk);


        $v = ObjectPermissionPeer::doSelect($criteria, $con);

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
            $criteria->add(ObjectPermissionPeer::OP_UID, $pks, Criteria::IN);
            $objs = ObjectPermissionPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseObjectPermissionPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/ObjectPermissionMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.ObjectPermissionMapBuilder');
}

