<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AppCacheViewPeer::getOMClass()
include_once 'classes/model/AppCacheView.php';

/**
 * Base static class for performing query and update operations on the 'APP_CACHE_VIEW' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppCacheViewPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'APP_CACHE_VIEW';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AppCacheView';

    /** The total number of columns. */
    const NUM_COLUMNS = 31;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_UID field */
    const APP_UID = 'APP_CACHE_VIEW.APP_UID';

    /** the column name for the DEL_INDEX field */
    const DEL_INDEX = 'APP_CACHE_VIEW.DEL_INDEX';

    /** the column name for the DEL_LAST_INDEX field */
    const DEL_LAST_INDEX = 'APP_CACHE_VIEW.DEL_LAST_INDEX';

    /** the column name for the APP_NUMBER field */
    const APP_NUMBER = 'APP_CACHE_VIEW.APP_NUMBER';

    /** the column name for the APP_STATUS field */
    const APP_STATUS = 'APP_CACHE_VIEW.APP_STATUS';

    /** the column name for the USR_UID field */
    const USR_UID = 'APP_CACHE_VIEW.USR_UID';

    /** the column name for the PREVIOUS_USR_UID field */
    const PREVIOUS_USR_UID = 'APP_CACHE_VIEW.PREVIOUS_USR_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'APP_CACHE_VIEW.TAS_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'APP_CACHE_VIEW.PRO_UID';

    /** the column name for the DEL_DELEGATE_DATE field */
    const DEL_DELEGATE_DATE = 'APP_CACHE_VIEW.DEL_DELEGATE_DATE';

    /** the column name for the DEL_INIT_DATE field */
    const DEL_INIT_DATE = 'APP_CACHE_VIEW.DEL_INIT_DATE';

    /** the column name for the DEL_TASK_DUE_DATE field */
    const DEL_TASK_DUE_DATE = 'APP_CACHE_VIEW.DEL_TASK_DUE_DATE';

    /** the column name for the DEL_FINISH_DATE field */
    const DEL_FINISH_DATE = 'APP_CACHE_VIEW.DEL_FINISH_DATE';

    /** the column name for the DEL_THREAD_STATUS field */
    const DEL_THREAD_STATUS = 'APP_CACHE_VIEW.DEL_THREAD_STATUS';

    /** the column name for the APP_THREAD_STATUS field */
    const APP_THREAD_STATUS = 'APP_CACHE_VIEW.APP_THREAD_STATUS';

    /** the column name for the APP_TITLE field */
    const APP_TITLE = 'APP_CACHE_VIEW.APP_TITLE';

    /** the column name for the APP_PRO_TITLE field */
    const APP_PRO_TITLE = 'APP_CACHE_VIEW.APP_PRO_TITLE';

    /** the column name for the APP_TAS_TITLE field */
    const APP_TAS_TITLE = 'APP_CACHE_VIEW.APP_TAS_TITLE';

    /** the column name for the APP_CURRENT_USER field */
    const APP_CURRENT_USER = 'APP_CACHE_VIEW.APP_CURRENT_USER';

    /** the column name for the APP_DEL_PREVIOUS_USER field */
    const APP_DEL_PREVIOUS_USER = 'APP_CACHE_VIEW.APP_DEL_PREVIOUS_USER';

    /** the column name for the DEL_PRIORITY field */
    const DEL_PRIORITY = 'APP_CACHE_VIEW.DEL_PRIORITY';

    /** the column name for the DEL_DURATION field */
    const DEL_DURATION = 'APP_CACHE_VIEW.DEL_DURATION';

    /** the column name for the DEL_QUEUE_DURATION field */
    const DEL_QUEUE_DURATION = 'APP_CACHE_VIEW.DEL_QUEUE_DURATION';

    /** the column name for the DEL_DELAY_DURATION field */
    const DEL_DELAY_DURATION = 'APP_CACHE_VIEW.DEL_DELAY_DURATION';

    /** the column name for the DEL_STARTED field */
    const DEL_STARTED = 'APP_CACHE_VIEW.DEL_STARTED';

    /** the column name for the DEL_FINISHED field */
    const DEL_FINISHED = 'APP_CACHE_VIEW.DEL_FINISHED';

    /** the column name for the DEL_DELAYED field */
    const DEL_DELAYED = 'APP_CACHE_VIEW.DEL_DELAYED';

    /** the column name for the APP_CREATE_DATE field */
    const APP_CREATE_DATE = 'APP_CACHE_VIEW.APP_CREATE_DATE';

    /** the column name for the APP_FINISH_DATE field */
    const APP_FINISH_DATE = 'APP_CACHE_VIEW.APP_FINISH_DATE';

    /** the column name for the APP_UPDATE_DATE field */
    const APP_UPDATE_DATE = 'APP_CACHE_VIEW.APP_UPDATE_DATE';

    /** the column name for the APP_OVERDUE_PERCENTAGE field */
    const APP_OVERDUE_PERCENTAGE = 'APP_CACHE_VIEW.APP_OVERDUE_PERCENTAGE';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid', 'DelIndex', 'DelLastIndex', 'AppNumber', 'AppStatus', 'UsrUid', 'PreviousUsrUid', 'TasUid', 'ProUid', 'DelDelegateDate', 'DelInitDate', 'DelTaskDueDate', 'DelFinishDate', 'DelThreadStatus', 'AppThreadStatus', 'AppTitle', 'AppProTitle', 'AppTasTitle', 'AppCurrentUser', 'AppDelPreviousUser', 'DelPriority', 'DelDuration', 'DelQueueDuration', 'DelDelayDuration', 'DelStarted', 'DelFinished', 'DelDelayed', 'AppCreateDate', 'AppFinishDate', 'AppUpdateDate', 'AppOverduePercentage', ),
        BasePeer::TYPE_COLNAME => array (AppCacheViewPeer::APP_UID, AppCacheViewPeer::DEL_INDEX, AppCacheViewPeer::DEL_LAST_INDEX, AppCacheViewPeer::APP_NUMBER, AppCacheViewPeer::APP_STATUS, AppCacheViewPeer::USR_UID, AppCacheViewPeer::PREVIOUS_USR_UID, AppCacheViewPeer::TAS_UID, AppCacheViewPeer::PRO_UID, AppCacheViewPeer::DEL_DELEGATE_DATE, AppCacheViewPeer::DEL_INIT_DATE, AppCacheViewPeer::DEL_TASK_DUE_DATE, AppCacheViewPeer::DEL_FINISH_DATE, AppCacheViewPeer::DEL_THREAD_STATUS, AppCacheViewPeer::APP_THREAD_STATUS, AppCacheViewPeer::APP_TITLE, AppCacheViewPeer::APP_PRO_TITLE, AppCacheViewPeer::APP_TAS_TITLE, AppCacheViewPeer::APP_CURRENT_USER, AppCacheViewPeer::APP_DEL_PREVIOUS_USER, AppCacheViewPeer::DEL_PRIORITY, AppCacheViewPeer::DEL_DURATION, AppCacheViewPeer::DEL_QUEUE_DURATION, AppCacheViewPeer::DEL_DELAY_DURATION, AppCacheViewPeer::DEL_STARTED, AppCacheViewPeer::DEL_FINISHED, AppCacheViewPeer::DEL_DELAYED, AppCacheViewPeer::APP_CREATE_DATE, AppCacheViewPeer::APP_FINISH_DATE, AppCacheViewPeer::APP_UPDATE_DATE, AppCacheViewPeer::APP_OVERDUE_PERCENTAGE, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID', 'DEL_INDEX', 'DEL_LAST_INDEX', 'APP_NUMBER', 'APP_STATUS', 'USR_UID', 'PREVIOUS_USR_UID', 'TAS_UID', 'PRO_UID', 'DEL_DELEGATE_DATE', 'DEL_INIT_DATE', 'DEL_TASK_DUE_DATE', 'DEL_FINISH_DATE', 'DEL_THREAD_STATUS', 'APP_THREAD_STATUS', 'APP_TITLE', 'APP_PRO_TITLE', 'APP_TAS_TITLE', 'APP_CURRENT_USER', 'APP_DEL_PREVIOUS_USER', 'DEL_PRIORITY', 'DEL_DURATION', 'DEL_QUEUE_DURATION', 'DEL_DELAY_DURATION', 'DEL_STARTED', 'DEL_FINISHED', 'DEL_DELAYED', 'APP_CREATE_DATE', 'APP_FINISH_DATE', 'APP_UPDATE_DATE', 'APP_OVERDUE_PERCENTAGE', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid' => 0, 'DelIndex' => 1, 'DelLastIndex' => 2, 'AppNumber' => 3, 'AppStatus' => 4, 'UsrUid' => 5, 'PreviousUsrUid' => 6, 'TasUid' => 7, 'ProUid' => 8, 'DelDelegateDate' => 9, 'DelInitDate' => 10, 'DelTaskDueDate' => 11, 'DelFinishDate' => 12, 'DelThreadStatus' => 13, 'AppThreadStatus' => 14, 'AppTitle' => 15, 'AppProTitle' => 16, 'AppTasTitle' => 17, 'AppCurrentUser' => 18, 'AppDelPreviousUser' => 19, 'DelPriority' => 20, 'DelDuration' => 21, 'DelQueueDuration' => 22, 'DelDelayDuration' => 23, 'DelStarted' => 24, 'DelFinished' => 25, 'DelDelayed' => 26, 'AppCreateDate' => 27, 'AppFinishDate' => 28, 'AppUpdateDate' => 29, 'AppOverduePercentage' => 30, ),
        BasePeer::TYPE_COLNAME => array (AppCacheViewPeer::APP_UID => 0, AppCacheViewPeer::DEL_INDEX => 1, AppCacheViewPeer::DEL_LAST_INDEX => 2, AppCacheViewPeer::APP_NUMBER => 3, AppCacheViewPeer::APP_STATUS => 4, AppCacheViewPeer::USR_UID => 5, AppCacheViewPeer::PREVIOUS_USR_UID => 6, AppCacheViewPeer::TAS_UID => 7, AppCacheViewPeer::PRO_UID => 8, AppCacheViewPeer::DEL_DELEGATE_DATE => 9, AppCacheViewPeer::DEL_INIT_DATE => 10, AppCacheViewPeer::DEL_TASK_DUE_DATE => 11, AppCacheViewPeer::DEL_FINISH_DATE => 12, AppCacheViewPeer::DEL_THREAD_STATUS => 13, AppCacheViewPeer::APP_THREAD_STATUS => 14, AppCacheViewPeer::APP_TITLE => 15, AppCacheViewPeer::APP_PRO_TITLE => 16, AppCacheViewPeer::APP_TAS_TITLE => 17, AppCacheViewPeer::APP_CURRENT_USER => 18, AppCacheViewPeer::APP_DEL_PREVIOUS_USER => 19, AppCacheViewPeer::DEL_PRIORITY => 20, AppCacheViewPeer::DEL_DURATION => 21, AppCacheViewPeer::DEL_QUEUE_DURATION => 22, AppCacheViewPeer::DEL_DELAY_DURATION => 23, AppCacheViewPeer::DEL_STARTED => 24, AppCacheViewPeer::DEL_FINISHED => 25, AppCacheViewPeer::DEL_DELAYED => 26, AppCacheViewPeer::APP_CREATE_DATE => 27, AppCacheViewPeer::APP_FINISH_DATE => 28, AppCacheViewPeer::APP_UPDATE_DATE => 29, AppCacheViewPeer::APP_OVERDUE_PERCENTAGE => 30, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID' => 0, 'DEL_INDEX' => 1, 'DEL_LAST_INDEX' => 2, 'APP_NUMBER' => 3, 'APP_STATUS' => 4, 'USR_UID' => 5, 'PREVIOUS_USR_UID' => 6, 'TAS_UID' => 7, 'PRO_UID' => 8, 'DEL_DELEGATE_DATE' => 9, 'DEL_INIT_DATE' => 10, 'DEL_TASK_DUE_DATE' => 11, 'DEL_FINISH_DATE' => 12, 'DEL_THREAD_STATUS' => 13, 'APP_THREAD_STATUS' => 14, 'APP_TITLE' => 15, 'APP_PRO_TITLE' => 16, 'APP_TAS_TITLE' => 17, 'APP_CURRENT_USER' => 18, 'APP_DEL_PREVIOUS_USER' => 19, 'DEL_PRIORITY' => 20, 'DEL_DURATION' => 21, 'DEL_QUEUE_DURATION' => 22, 'DEL_DELAY_DURATION' => 23, 'DEL_STARTED' => 24, 'DEL_FINISHED' => 25, 'DEL_DELAYED' => 26, 'APP_CREATE_DATE' => 27, 'APP_FINISH_DATE' => 28, 'APP_UPDATE_DATE' => 29, 'APP_OVERDUE_PERCENTAGE' => 30, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AppCacheViewMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AppCacheViewMapBuilder');
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
            $map = AppCacheViewPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AppCacheViewPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AppCacheViewPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_LAST_INDEX);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_STATUS);

        $criteria->addSelectColumn(AppCacheViewPeer::USR_UID);

        $criteria->addSelectColumn(AppCacheViewPeer::PREVIOUS_USR_UID);

        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);

        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELEGATE_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INIT_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_TASK_DUE_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISH_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_THREAD_STATUS);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_THREAD_STATUS);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_TITLE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_TAS_TITLE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_CURRENT_USER);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_DEL_PREVIOUS_USER);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_PRIORITY);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DURATION);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_QUEUE_DURATION);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAY_DURATION);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_STARTED);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISHED);

        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAYED);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_CREATE_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_FINISH_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_UPDATE_DATE);

        $criteria->addSelectColumn(AppCacheViewPeer::APP_OVERDUE_PERCENTAGE);

    }

    const COUNT = 'COUNT(APP_CACHE_VIEW.APP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APP_CACHE_VIEW.APP_UID)';

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
            $criteria->addSelectColumn(AppCacheViewPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AppCacheViewPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AppCacheViewPeer::doSelectRS($criteria, $con);
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
     * @return     AppCacheView
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AppCacheViewPeer::doSelect($critcopy, $con);
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
        return AppCacheViewPeer::populateObjects(AppCacheViewPeer::doSelectRS($criteria, $con));
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
            AppCacheViewPeer::addSelectColumns($criteria);
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
        $cls = AppCacheViewPeer::getOMClass();
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
        return AppCacheViewPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AppCacheView or Criteria object.
     *
     * @param      mixed $values Criteria or AppCacheView object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AppCacheView object
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
     * Method perform an UPDATE on the database, given a AppCacheView or Criteria object.
     *
     * @param      mixed $values Criteria or AppCacheView object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AppCacheViewPeer::APP_UID);
            $selectCriteria->add(AppCacheViewPeer::APP_UID, $criteria->remove(AppCacheViewPeer::APP_UID), $comparison);

            $comparison = $criteria->getComparison(AppCacheViewPeer::DEL_INDEX);
            $selectCriteria->add(AppCacheViewPeer::DEL_INDEX, $criteria->remove(AppCacheViewPeer::DEL_INDEX), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the APP_CACHE_VIEW table.
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
            $affectedRows += BasePeer::doDeleteAll(AppCacheViewPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AppCacheView or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AppCacheView object or primary key or array of primary keys
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
            $con = Propel::getConnection(AppCacheViewPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AppCacheView) {

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

            $criteria->add(AppCacheViewPeer::APP_UID, $vals[0], Criteria::IN);
            $criteria->add(AppCacheViewPeer::DEL_INDEX, $vals[1], Criteria::IN);
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
     * Validates all modified columns of given AppCacheView object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AppCacheView $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AppCacheView $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AppCacheViewPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AppCacheViewPeer::TABLE_NAME);

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

        return BasePeer::doValidate(AppCacheViewPeer::DATABASE_NAME, AppCacheViewPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $app_uid
       * @param int $del_index
        * @param      Connection $con
     * @return     AppCacheView
     */
    public static function retrieveByPK($app_uid, $del_index, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(AppCacheViewPeer::APP_UID, $app_uid);
        $criteria->add(AppCacheViewPeer::DEL_INDEX, $del_index);
        $v = AppCacheViewPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAppCacheViewPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AppCacheViewMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AppCacheViewMapBuilder');
}

