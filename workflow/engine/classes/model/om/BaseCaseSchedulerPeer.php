<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by CaseSchedulerPeer::getOMClass()
//include_once 'classes/model/CaseScheduler.php';

/**
 * Base static class for performing query and update operations on the 'CASE_SCHEDULER' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseCaseSchedulerPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'CASE_SCHEDULER';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.CaseScheduler';

    /** The total number of columns. */
    const NUM_COLUMNS = 25;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the SCH_UID field */
    const SCH_UID = 'CASE_SCHEDULER.SCH_UID';

    /** the column name for the SCH_DEL_USER_NAME field */
    const SCH_DEL_USER_NAME = 'CASE_SCHEDULER.SCH_DEL_USER_NAME';

    /** the column name for the SCH_DEL_USER_PASS field */
    const SCH_DEL_USER_PASS = 'CASE_SCHEDULER.SCH_DEL_USER_PASS';

    /** the column name for the SCH_DEL_USER_UID field */
    const SCH_DEL_USER_UID = 'CASE_SCHEDULER.SCH_DEL_USER_UID';

    /** the column name for the SCH_NAME field */
    const SCH_NAME = 'CASE_SCHEDULER.SCH_NAME';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'CASE_SCHEDULER.PRO_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'CASE_SCHEDULER.TAS_UID';

    /** the column name for the SCH_TIME_NEXT_RUN field */
    const SCH_TIME_NEXT_RUN = 'CASE_SCHEDULER.SCH_TIME_NEXT_RUN';

    /** the column name for the SCH_LAST_RUN_TIME field */
    const SCH_LAST_RUN_TIME = 'CASE_SCHEDULER.SCH_LAST_RUN_TIME';

    /** the column name for the SCH_STATE field */
    const SCH_STATE = 'CASE_SCHEDULER.SCH_STATE';

    /** the column name for the SCH_LAST_STATE field */
    const SCH_LAST_STATE = 'CASE_SCHEDULER.SCH_LAST_STATE';

    /** the column name for the USR_UID field */
    const USR_UID = 'CASE_SCHEDULER.USR_UID';

    /** the column name for the SCH_OPTION field */
    const SCH_OPTION = 'CASE_SCHEDULER.SCH_OPTION';

    /** the column name for the SCH_START_TIME field */
    const SCH_START_TIME = 'CASE_SCHEDULER.SCH_START_TIME';

    /** the column name for the SCH_START_DATE field */
    const SCH_START_DATE = 'CASE_SCHEDULER.SCH_START_DATE';

    /** the column name for the SCH_DAYS_PERFORM_TASK field */
    const SCH_DAYS_PERFORM_TASK = 'CASE_SCHEDULER.SCH_DAYS_PERFORM_TASK';

    /** the column name for the SCH_EVERY_DAYS field */
    const SCH_EVERY_DAYS = 'CASE_SCHEDULER.SCH_EVERY_DAYS';

    /** the column name for the SCH_WEEK_DAYS field */
    const SCH_WEEK_DAYS = 'CASE_SCHEDULER.SCH_WEEK_DAYS';

    /** the column name for the SCH_START_DAY field */
    const SCH_START_DAY = 'CASE_SCHEDULER.SCH_START_DAY';

    /** the column name for the SCH_MONTHS field */
    const SCH_MONTHS = 'CASE_SCHEDULER.SCH_MONTHS';

    /** the column name for the SCH_END_DATE field */
    const SCH_END_DATE = 'CASE_SCHEDULER.SCH_END_DATE';

    /** the column name for the SCH_REPEAT_EVERY field */
    const SCH_REPEAT_EVERY = 'CASE_SCHEDULER.SCH_REPEAT_EVERY';

    /** the column name for the SCH_REPEAT_UNTIL field */
    const SCH_REPEAT_UNTIL = 'CASE_SCHEDULER.SCH_REPEAT_UNTIL';

    /** the column name for the SCH_REPEAT_STOP_IF_RUNNING field */
    const SCH_REPEAT_STOP_IF_RUNNING = 'CASE_SCHEDULER.SCH_REPEAT_STOP_IF_RUNNING';

    /** the column name for the CASE_SH_PLUGIN_UID field */
    const CASE_SH_PLUGIN_UID = 'CASE_SCHEDULER.CASE_SH_PLUGIN_UID';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('SchUid', 'SchDelUserName', 'SchDelUserPass', 'SchDelUserUid', 'SchName', 'ProUid', 'TasUid', 'SchTimeNextRun', 'SchLastRunTime', 'SchState', 'SchLastState', 'UsrUid', 'SchOption', 'SchStartTime', 'SchStartDate', 'SchDaysPerformTask', 'SchEveryDays', 'SchWeekDays', 'SchStartDay', 'SchMonths', 'SchEndDate', 'SchRepeatEvery', 'SchRepeatUntil', 'SchRepeatStopIfRunning', 'CaseShPluginUid', ),
        BasePeer::TYPE_COLNAME => array (CaseSchedulerPeer::SCH_UID, CaseSchedulerPeer::SCH_DEL_USER_NAME, CaseSchedulerPeer::SCH_DEL_USER_PASS, CaseSchedulerPeer::SCH_DEL_USER_UID, CaseSchedulerPeer::SCH_NAME, CaseSchedulerPeer::PRO_UID, CaseSchedulerPeer::TAS_UID, CaseSchedulerPeer::SCH_TIME_NEXT_RUN, CaseSchedulerPeer::SCH_LAST_RUN_TIME, CaseSchedulerPeer::SCH_STATE, CaseSchedulerPeer::SCH_LAST_STATE, CaseSchedulerPeer::USR_UID, CaseSchedulerPeer::SCH_OPTION, CaseSchedulerPeer::SCH_START_TIME, CaseSchedulerPeer::SCH_START_DATE, CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK, CaseSchedulerPeer::SCH_EVERY_DAYS, CaseSchedulerPeer::SCH_WEEK_DAYS, CaseSchedulerPeer::SCH_START_DAY, CaseSchedulerPeer::SCH_MONTHS, CaseSchedulerPeer::SCH_END_DATE, CaseSchedulerPeer::SCH_REPEAT_EVERY, CaseSchedulerPeer::SCH_REPEAT_UNTIL, CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING, CaseSchedulerPeer::CASE_SH_PLUGIN_UID, ),
        BasePeer::TYPE_FIELDNAME => array ('SCH_UID', 'SCH_DEL_USER_NAME', 'SCH_DEL_USER_PASS', 'SCH_DEL_USER_UID', 'SCH_NAME', 'PRO_UID', 'TAS_UID', 'SCH_TIME_NEXT_RUN', 'SCH_LAST_RUN_TIME', 'SCH_STATE', 'SCH_LAST_STATE', 'USR_UID', 'SCH_OPTION', 'SCH_START_TIME', 'SCH_START_DATE', 'SCH_DAYS_PERFORM_TASK', 'SCH_EVERY_DAYS', 'SCH_WEEK_DAYS', 'SCH_START_DAY', 'SCH_MONTHS', 'SCH_END_DATE', 'SCH_REPEAT_EVERY', 'SCH_REPEAT_UNTIL', 'SCH_REPEAT_STOP_IF_RUNNING', 'CASE_SH_PLUGIN_UID', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('SchUid' => 0, 'SchDelUserName' => 1, 'SchDelUserPass' => 2, 'SchDelUserUid' => 3, 'SchName' => 4, 'ProUid' => 5, 'TasUid' => 6, 'SchTimeNextRun' => 7, 'SchLastRunTime' => 8, 'SchState' => 9, 'SchLastState' => 10, 'UsrUid' => 11, 'SchOption' => 12, 'SchStartTime' => 13, 'SchStartDate' => 14, 'SchDaysPerformTask' => 15, 'SchEveryDays' => 16, 'SchWeekDays' => 17, 'SchStartDay' => 18, 'SchMonths' => 19, 'SchEndDate' => 20, 'SchRepeatEvery' => 21, 'SchRepeatUntil' => 22, 'SchRepeatStopIfRunning' => 23, 'CaseShPluginUid' => 24, ),
        BasePeer::TYPE_COLNAME => array (CaseSchedulerPeer::SCH_UID => 0, CaseSchedulerPeer::SCH_DEL_USER_NAME => 1, CaseSchedulerPeer::SCH_DEL_USER_PASS => 2, CaseSchedulerPeer::SCH_DEL_USER_UID => 3, CaseSchedulerPeer::SCH_NAME => 4, CaseSchedulerPeer::PRO_UID => 5, CaseSchedulerPeer::TAS_UID => 6, CaseSchedulerPeer::SCH_TIME_NEXT_RUN => 7, CaseSchedulerPeer::SCH_LAST_RUN_TIME => 8, CaseSchedulerPeer::SCH_STATE => 9, CaseSchedulerPeer::SCH_LAST_STATE => 10, CaseSchedulerPeer::USR_UID => 11, CaseSchedulerPeer::SCH_OPTION => 12, CaseSchedulerPeer::SCH_START_TIME => 13, CaseSchedulerPeer::SCH_START_DATE => 14, CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK => 15, CaseSchedulerPeer::SCH_EVERY_DAYS => 16, CaseSchedulerPeer::SCH_WEEK_DAYS => 17, CaseSchedulerPeer::SCH_START_DAY => 18, CaseSchedulerPeer::SCH_MONTHS => 19, CaseSchedulerPeer::SCH_END_DATE => 20, CaseSchedulerPeer::SCH_REPEAT_EVERY => 21, CaseSchedulerPeer::SCH_REPEAT_UNTIL => 22, CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING => 23, CaseSchedulerPeer::CASE_SH_PLUGIN_UID => 24, ),
        BasePeer::TYPE_FIELDNAME => array ('SCH_UID' => 0, 'SCH_DEL_USER_NAME' => 1, 'SCH_DEL_USER_PASS' => 2, 'SCH_DEL_USER_UID' => 3, 'SCH_NAME' => 4, 'PRO_UID' => 5, 'TAS_UID' => 6, 'SCH_TIME_NEXT_RUN' => 7, 'SCH_LAST_RUN_TIME' => 8, 'SCH_STATE' => 9, 'SCH_LAST_STATE' => 10, 'USR_UID' => 11, 'SCH_OPTION' => 12, 'SCH_START_TIME' => 13, 'SCH_START_DATE' => 14, 'SCH_DAYS_PERFORM_TASK' => 15, 'SCH_EVERY_DAYS' => 16, 'SCH_WEEK_DAYS' => 17, 'SCH_START_DAY' => 18, 'SCH_MONTHS' => 19, 'SCH_END_DATE' => 20, 'SCH_REPEAT_EVERY' => 21, 'SCH_REPEAT_UNTIL' => 22, 'SCH_REPEAT_STOP_IF_RUNNING' => 23, 'CASE_SH_PLUGIN_UID' => 24, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/CaseSchedulerMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.CaseSchedulerMapBuilder');
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
            $map = CaseSchedulerPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. CaseSchedulerPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CaseSchedulerPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_UID);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_NAME);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_PASS);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_UID);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_NAME);

        $criteria->addSelectColumn(CaseSchedulerPeer::PRO_UID);

        $criteria->addSelectColumn(CaseSchedulerPeer::TAS_UID);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_TIME_NEXT_RUN);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_LAST_RUN_TIME);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_STATE);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_LAST_STATE);

        $criteria->addSelectColumn(CaseSchedulerPeer::USR_UID);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_OPTION);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_TIME);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_DATE);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_EVERY_DAYS);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_WEEK_DAYS);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_DAY);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_MONTHS);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_END_DATE);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_EVERY);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_UNTIL);

        $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING);

        $criteria->addSelectColumn(CaseSchedulerPeer::CASE_SH_PLUGIN_UID);

    }

    const COUNT = 'COUNT(CASE_SCHEDULER.SCH_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT CASE_SCHEDULER.SCH_UID)';

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
            $criteria->addSelectColumn(CaseSchedulerPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(CaseSchedulerPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = CaseSchedulerPeer::doSelectRS($criteria, $con);
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
     * @return     CaseScheduler
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CaseSchedulerPeer::doSelect($critcopy, $con);
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
        return CaseSchedulerPeer::populateObjects(CaseSchedulerPeer::doSelectRS($criteria, $con));
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
            CaseSchedulerPeer::addSelectColumns($criteria);
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
        $cls = CaseSchedulerPeer::getOMClass();
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
        return CaseSchedulerPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a CaseScheduler or Criteria object.
     *
     * @param      mixed $values Criteria or CaseScheduler object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from CaseScheduler object
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
     * Method perform an UPDATE on the database, given a CaseScheduler or Criteria object.
     *
     * @param      mixed $values Criteria or CaseScheduler object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(CaseSchedulerPeer::SCH_UID);
            $selectCriteria->add(CaseSchedulerPeer::SCH_UID, $criteria->remove(CaseSchedulerPeer::SCH_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the CASE_SCHEDULER table.
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
            $affectedRows += BasePeer::doDeleteAll(CaseSchedulerPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a CaseScheduler or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CaseScheduler object or primary key or array of primary keys
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
            $con = Propel::getConnection(CaseSchedulerPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof CaseScheduler) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(CaseSchedulerPeer::SCH_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given CaseScheduler object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      CaseScheduler $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(CaseScheduler $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CaseSchedulerPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CaseSchedulerPeer::TABLE_NAME);

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

        return BasePeer::doValidate(CaseSchedulerPeer::DATABASE_NAME, CaseSchedulerPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     CaseScheduler
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(CaseSchedulerPeer::DATABASE_NAME);

        $criteria->add(CaseSchedulerPeer::SCH_UID, $pk);


        $v = CaseSchedulerPeer::doSelect($criteria, $con);

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
            $criteria->add(CaseSchedulerPeer::SCH_UID, $pks, Criteria::IN);
            $objs = CaseSchedulerPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseCaseSchedulerPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/CaseSchedulerMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.CaseSchedulerMapBuilder');
}

