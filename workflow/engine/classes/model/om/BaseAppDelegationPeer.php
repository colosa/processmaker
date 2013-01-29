<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AppDelegationPeer::getOMClass()
include_once 'classes/model/AppDelegation.php';

/**
 * Base static class for performing query and update operations on the 'APP_DELEGATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppDelegationPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'APP_DELEGATION';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AppDelegation';

    /** The total number of columns. */
    const NUM_COLUMNS = 23;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_UID field */
    const APP_UID = 'APP_DELEGATION.APP_UID';

    /** the column name for the DEL_INDEX field */
    const DEL_INDEX = 'APP_DELEGATION.DEL_INDEX';

    /** the column name for the DEL_PREVIOUS field */
    const DEL_PREVIOUS = 'APP_DELEGATION.DEL_PREVIOUS';

    /** the column name for the DEL_LAST_INDEX field */
    const DEL_LAST_INDEX = 'APP_DELEGATION.DEL_LAST_INDEX';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'APP_DELEGATION.PRO_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'APP_DELEGATION.TAS_UID';

    /** the column name for the USR_UID field */
    const USR_UID = 'APP_DELEGATION.USR_UID';

    /** the column name for the DEL_TYPE field */
    const DEL_TYPE = 'APP_DELEGATION.DEL_TYPE';

    /** the column name for the DEL_THREAD field */
    const DEL_THREAD = 'APP_DELEGATION.DEL_THREAD';

    /** the column name for the DEL_THREAD_STATUS field */
    const DEL_THREAD_STATUS = 'APP_DELEGATION.DEL_THREAD_STATUS';

    /** the column name for the DEL_PRIORITY field */
    const DEL_PRIORITY = 'APP_DELEGATION.DEL_PRIORITY';

    /** the column name for the DEL_DELEGATE_DATE field */
    const DEL_DELEGATE_DATE = 'APP_DELEGATION.DEL_DELEGATE_DATE';

    /** the column name for the DEL_INIT_DATE field */
    const DEL_INIT_DATE = 'APP_DELEGATION.DEL_INIT_DATE';

    /** the column name for the DEL_TASK_DUE_DATE field */
    const DEL_TASK_DUE_DATE = 'APP_DELEGATION.DEL_TASK_DUE_DATE';

    /** the column name for the DEL_FINISH_DATE field */
    const DEL_FINISH_DATE = 'APP_DELEGATION.DEL_FINISH_DATE';

    /** the column name for the DEL_DURATION field */
    const DEL_DURATION = 'APP_DELEGATION.DEL_DURATION';

    /** the column name for the DEL_QUEUE_DURATION field */
    const DEL_QUEUE_DURATION = 'APP_DELEGATION.DEL_QUEUE_DURATION';

    /** the column name for the DEL_DELAY_DURATION field */
    const DEL_DELAY_DURATION = 'APP_DELEGATION.DEL_DELAY_DURATION';

    /** the column name for the DEL_STARTED field */
    const DEL_STARTED = 'APP_DELEGATION.DEL_STARTED';

    /** the column name for the DEL_FINISHED field */
    const DEL_FINISHED = 'APP_DELEGATION.DEL_FINISHED';

    /** the column name for the DEL_DELAYED field */
    const DEL_DELAYED = 'APP_DELEGATION.DEL_DELAYED';

    /** the column name for the DEL_DATA field */
    const DEL_DATA = 'APP_DELEGATION.DEL_DATA';

    /** the column name for the APP_OVERDUE_PERCENTAGE field */
    const APP_OVERDUE_PERCENTAGE = 'APP_DELEGATION.APP_OVERDUE_PERCENTAGE';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid', 'DelIndex', 'DelPrevious', 'DelLastIndex', 'ProUid', 'TasUid', 'UsrUid', 'DelType', 'DelThread', 'DelThreadStatus', 'DelPriority', 'DelDelegateDate', 'DelInitDate', 'DelTaskDueDate', 'DelFinishDate', 'DelDuration', 'DelQueueDuration', 'DelDelayDuration', 'DelStarted', 'DelFinished', 'DelDelayed', 'DelData', 'AppOverduePercentage', ),
        BasePeer::TYPE_COLNAME => array (AppDelegationPeer::APP_UID, AppDelegationPeer::DEL_INDEX, AppDelegationPeer::DEL_PREVIOUS, AppDelegationPeer::DEL_LAST_INDEX, AppDelegationPeer::PRO_UID, AppDelegationPeer::TAS_UID, AppDelegationPeer::USR_UID, AppDelegationPeer::DEL_TYPE, AppDelegationPeer::DEL_THREAD, AppDelegationPeer::DEL_THREAD_STATUS, AppDelegationPeer::DEL_PRIORITY, AppDelegationPeer::DEL_DELEGATE_DATE, AppDelegationPeer::DEL_INIT_DATE, AppDelegationPeer::DEL_TASK_DUE_DATE, AppDelegationPeer::DEL_FINISH_DATE, AppDelegationPeer::DEL_DURATION, AppDelegationPeer::DEL_QUEUE_DURATION, AppDelegationPeer::DEL_DELAY_DURATION, AppDelegationPeer::DEL_STARTED, AppDelegationPeer::DEL_FINISHED, AppDelegationPeer::DEL_DELAYED, AppDelegationPeer::DEL_DATA, AppDelegationPeer::APP_OVERDUE_PERCENTAGE, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID', 'DEL_INDEX', 'DEL_PREVIOUS', 'DEL_LAST_INDEX', 'PRO_UID', 'TAS_UID', 'USR_UID', 'DEL_TYPE', 'DEL_THREAD', 'DEL_THREAD_STATUS', 'DEL_PRIORITY', 'DEL_DELEGATE_DATE', 'DEL_INIT_DATE', 'DEL_TASK_DUE_DATE', 'DEL_FINISH_DATE', 'DEL_DURATION', 'DEL_QUEUE_DURATION', 'DEL_DELAY_DURATION', 'DEL_STARTED', 'DEL_FINISHED', 'DEL_DELAYED', 'DEL_DATA', 'APP_OVERDUE_PERCENTAGE', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid' => 0, 'DelIndex' => 1, 'DelPrevious' => 2, 'DelLastIndex' => 3, 'ProUid' => 4, 'TasUid' => 5, 'UsrUid' => 6, 'DelType' => 7, 'DelThread' => 8, 'DelThreadStatus' => 9, 'DelPriority' => 10, 'DelDelegateDate' => 11, 'DelInitDate' => 12, 'DelTaskDueDate' => 13, 'DelFinishDate' => 14, 'DelDuration' => 15, 'DelQueueDuration' => 16, 'DelDelayDuration' => 17, 'DelStarted' => 18, 'DelFinished' => 19, 'DelDelayed' => 20, 'DelData' => 21, 'AppOverduePercentage' => 22, ),
        BasePeer::TYPE_COLNAME => array (AppDelegationPeer::APP_UID => 0, AppDelegationPeer::DEL_INDEX => 1, AppDelegationPeer::DEL_PREVIOUS => 2, AppDelegationPeer::DEL_LAST_INDEX => 3, AppDelegationPeer::PRO_UID => 4, AppDelegationPeer::TAS_UID => 5, AppDelegationPeer::USR_UID => 6, AppDelegationPeer::DEL_TYPE => 7, AppDelegationPeer::DEL_THREAD => 8, AppDelegationPeer::DEL_THREAD_STATUS => 9, AppDelegationPeer::DEL_PRIORITY => 10, AppDelegationPeer::DEL_DELEGATE_DATE => 11, AppDelegationPeer::DEL_INIT_DATE => 12, AppDelegationPeer::DEL_TASK_DUE_DATE => 13, AppDelegationPeer::DEL_FINISH_DATE => 14, AppDelegationPeer::DEL_DURATION => 15, AppDelegationPeer::DEL_QUEUE_DURATION => 16, AppDelegationPeer::DEL_DELAY_DURATION => 17, AppDelegationPeer::DEL_STARTED => 18, AppDelegationPeer::DEL_FINISHED => 19, AppDelegationPeer::DEL_DELAYED => 20, AppDelegationPeer::DEL_DATA => 21, AppDelegationPeer::APP_OVERDUE_PERCENTAGE => 22, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID' => 0, 'DEL_INDEX' => 1, 'DEL_PREVIOUS' => 2, 'DEL_LAST_INDEX' => 3, 'PRO_UID' => 4, 'TAS_UID' => 5, 'USR_UID' => 6, 'DEL_TYPE' => 7, 'DEL_THREAD' => 8, 'DEL_THREAD_STATUS' => 9, 'DEL_PRIORITY' => 10, 'DEL_DELEGATE_DATE' => 11, 'DEL_INIT_DATE' => 12, 'DEL_TASK_DUE_DATE' => 13, 'DEL_FINISH_DATE' => 14, 'DEL_DURATION' => 15, 'DEL_QUEUE_DURATION' => 16, 'DEL_DELAY_DURATION' => 17, 'DEL_STARTED' => 18, 'DEL_FINISHED' => 19, 'DEL_DELAYED' => 20, 'DEL_DATA' => 21, 'APP_OVERDUE_PERCENTAGE' => 22, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AppDelegationMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AppDelegationMapBuilder');
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
            $map = AppDelegationPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AppDelegationPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AppDelegationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AppDelegationPeer::APP_UID);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_LAST_INDEX);

        $criteria->addSelectColumn(AppDelegationPeer::PRO_UID);

        $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);

        $criteria->addSelectColumn(AppDelegationPeer::USR_UID);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_TYPE);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_THREAD);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_DURATION);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_QUEUE_DURATION);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_DELAY_DURATION);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_STARTED);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_FINISHED);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_DELAYED);

        $criteria->addSelectColumn(AppDelegationPeer::DEL_DATA);

        $criteria->addSelectColumn(AppDelegationPeer::APP_OVERDUE_PERCENTAGE);

    }

    const COUNT = 'COUNT(APP_DELEGATION.APP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APP_DELEGATION.APP_UID)';

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
            $criteria->addSelectColumn(AppDelegationPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AppDelegationPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AppDelegationPeer::doSelectRS($criteria, $con);
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
     * @return     AppDelegation
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AppDelegationPeer::doSelect($critcopy, $con);
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
        return AppDelegationPeer::populateObjects(AppDelegationPeer::doSelectRS($criteria, $con));
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
            AppDelegationPeer::addSelectColumns($criteria);
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
        $cls = AppDelegationPeer::getOMClass();
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
        return AppDelegationPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AppDelegation or Criteria object.
     *
     * @param      mixed $values Criteria or AppDelegation object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AppDelegation object
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
     * Method perform an UPDATE on the database, given a AppDelegation or Criteria object.
     *
     * @param      mixed $values Criteria or AppDelegation object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AppDelegationPeer::APP_UID);
            $selectCriteria->add(AppDelegationPeer::APP_UID, $criteria->remove(AppDelegationPeer::APP_UID), $comparison);

            $comparison = $criteria->getComparison(AppDelegationPeer::DEL_INDEX);
            $selectCriteria->add(AppDelegationPeer::DEL_INDEX, $criteria->remove(AppDelegationPeer::DEL_INDEX), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the APP_DELEGATION table.
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
            $affectedRows += BasePeer::doDeleteAll(AppDelegationPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AppDelegation or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AppDelegation object or primary key or array of primary keys
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
            $con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AppDelegation) {

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

            $criteria->add(AppDelegationPeer::APP_UID, $vals[0], Criteria::IN);
            $criteria->add(AppDelegationPeer::DEL_INDEX, $vals[1], Criteria::IN);
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
     * Validates all modified columns of given AppDelegation object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AppDelegation $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AppDelegation $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AppDelegationPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AppDelegationPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(AppDelegationPeer::DEL_TYPE))
            $columns[AppDelegationPeer::DEL_TYPE] = $obj->getDelType();

        if ($obj->isNew() || $obj->isColumnModified(AppDelegationPeer::DEL_PRIORITY))
            $columns[AppDelegationPeer::DEL_PRIORITY] = $obj->getDelPriority();

        if ($obj->isNew() || $obj->isColumnModified(AppDelegationPeer::DEL_THREAD_STATUS))
            $columns[AppDelegationPeer::DEL_THREAD_STATUS] = $obj->getDelThreadStatus();

        }

        return BasePeer::doValidate(AppDelegationPeer::DATABASE_NAME, AppDelegationPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $app_uid
       * @param int $del_index
        * @param      Connection $con
     * @return     AppDelegation
     */
    public static function retrieveByPK($app_uid, $del_index, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(AppDelegationPeer::APP_UID, $app_uid);
        $criteria->add(AppDelegationPeer::DEL_INDEX, $del_index);
        $v = AppDelegationPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAppDelegationPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AppDelegationMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AppDelegationMapBuilder');
}

