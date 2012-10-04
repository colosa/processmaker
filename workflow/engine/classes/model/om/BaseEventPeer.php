<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by EventPeer::getOMClass()
include_once 'classes/model/Event.php';

/**
 * Base static class for performing query and update operations on the 'EVENT' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseEventPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'EVENT';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Event';

    /** The total number of columns. */
    const NUM_COLUMNS = 20;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the EVN_UID field */
    const EVN_UID = 'EVENT.EVN_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'EVENT.PRO_UID';

    /** the column name for the EVN_STATUS field */
    const EVN_STATUS = 'EVENT.EVN_STATUS';

    /** the column name for the EVN_WHEN_OCCURS field */
    const EVN_WHEN_OCCURS = 'EVENT.EVN_WHEN_OCCURS';

    /** the column name for the EVN_RELATED_TO field */
    const EVN_RELATED_TO = 'EVENT.EVN_RELATED_TO';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'EVENT.TAS_UID';

    /** the column name for the EVN_TAS_UID_FROM field */
    const EVN_TAS_UID_FROM = 'EVENT.EVN_TAS_UID_FROM';

    /** the column name for the EVN_TAS_UID_TO field */
    const EVN_TAS_UID_TO = 'EVENT.EVN_TAS_UID_TO';

    /** the column name for the EVN_TAS_ESTIMATED_DURATION field */
    const EVN_TAS_ESTIMATED_DURATION = 'EVENT.EVN_TAS_ESTIMATED_DURATION';

    /** the column name for the EVN_TIME_UNIT field */
    const EVN_TIME_UNIT = 'EVENT.EVN_TIME_UNIT';

    /** the column name for the EVN_WHEN field */
    const EVN_WHEN = 'EVENT.EVN_WHEN';

    /** the column name for the EVN_MAX_ATTEMPTS field */
    const EVN_MAX_ATTEMPTS = 'EVENT.EVN_MAX_ATTEMPTS';

    /** the column name for the EVN_ACTION field */
    const EVN_ACTION = 'EVENT.EVN_ACTION';

    /** the column name for the EVN_CONDITIONS field */
    const EVN_CONDITIONS = 'EVENT.EVN_CONDITIONS';

    /** the column name for the EVN_ACTION_PARAMETERS field */
    const EVN_ACTION_PARAMETERS = 'EVENT.EVN_ACTION_PARAMETERS';

    /** the column name for the TRI_UID field */
    const TRI_UID = 'EVENT.TRI_UID';

    /** the column name for the EVN_POSX field */
    const EVN_POSX = 'EVENT.EVN_POSX';

    /** the column name for the EVN_POSY field */
    const EVN_POSY = 'EVENT.EVN_POSY';

    /** the column name for the EVN_TYPE field */
    const EVN_TYPE = 'EVENT.EVN_TYPE';

    /** the column name for the TAS_EVN_UID field */
    const TAS_EVN_UID = 'EVENT.TAS_EVN_UID';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('EvnUid', 'ProUid', 'EvnStatus', 'EvnWhenOccurs', 'EvnRelatedTo', 'TasUid', 'EvnTasUidFrom', 'EvnTasUidTo', 'EvnTasEstimatedDuration', 'EvnTimeUnit', 'EvnWhen', 'EvnMaxAttempts', 'EvnAction', 'EvnConditions', 'EvnActionParameters', 'TriUid', 'EvnPosx', 'EvnPosy', 'EvnType', 'TasEvnUid', ),
        BasePeer::TYPE_COLNAME => array (EventPeer::EVN_UID, EventPeer::PRO_UID, EventPeer::EVN_STATUS, EventPeer::EVN_WHEN_OCCURS, EventPeer::EVN_RELATED_TO, EventPeer::TAS_UID, EventPeer::EVN_TAS_UID_FROM, EventPeer::EVN_TAS_UID_TO, EventPeer::EVN_TAS_ESTIMATED_DURATION, EventPeer::EVN_TIME_UNIT, EventPeer::EVN_WHEN, EventPeer::EVN_MAX_ATTEMPTS, EventPeer::EVN_ACTION, EventPeer::EVN_CONDITIONS, EventPeer::EVN_ACTION_PARAMETERS, EventPeer::TRI_UID, EventPeer::EVN_POSX, EventPeer::EVN_POSY, EventPeer::EVN_TYPE, EventPeer::TAS_EVN_UID, ),
        BasePeer::TYPE_FIELDNAME => array ('EVN_UID', 'PRO_UID', 'EVN_STATUS', 'EVN_WHEN_OCCURS', 'EVN_RELATED_TO', 'TAS_UID', 'EVN_TAS_UID_FROM', 'EVN_TAS_UID_TO', 'EVN_TAS_ESTIMATED_DURATION', 'EVN_TIME_UNIT', 'EVN_WHEN', 'EVN_MAX_ATTEMPTS', 'EVN_ACTION', 'EVN_CONDITIONS', 'EVN_ACTION_PARAMETERS', 'TRI_UID', 'EVN_POSX', 'EVN_POSY', 'EVN_TYPE', 'TAS_EVN_UID', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('EvnUid' => 0, 'ProUid' => 1, 'EvnStatus' => 2, 'EvnWhenOccurs' => 3, 'EvnRelatedTo' => 4, 'TasUid' => 5, 'EvnTasUidFrom' => 6, 'EvnTasUidTo' => 7, 'EvnTasEstimatedDuration' => 8, 'EvnTimeUnit' => 9, 'EvnWhen' => 10, 'EvnMaxAttempts' => 11, 'EvnAction' => 12, 'EvnConditions' => 13, 'EvnActionParameters' => 14, 'TriUid' => 15, 'EvnPosx' => 16, 'EvnPosy' => 17, 'EvnType' => 18, 'TasEvnUid' => 19, ),
        BasePeer::TYPE_COLNAME => array (EventPeer::EVN_UID => 0, EventPeer::PRO_UID => 1, EventPeer::EVN_STATUS => 2, EventPeer::EVN_WHEN_OCCURS => 3, EventPeer::EVN_RELATED_TO => 4, EventPeer::TAS_UID => 5, EventPeer::EVN_TAS_UID_FROM => 6, EventPeer::EVN_TAS_UID_TO => 7, EventPeer::EVN_TAS_ESTIMATED_DURATION => 8, EventPeer::EVN_TIME_UNIT => 9, EventPeer::EVN_WHEN => 10, EventPeer::EVN_MAX_ATTEMPTS => 11, EventPeer::EVN_ACTION => 12, EventPeer::EVN_CONDITIONS => 13, EventPeer::EVN_ACTION_PARAMETERS => 14, EventPeer::TRI_UID => 15, EventPeer::EVN_POSX => 16, EventPeer::EVN_POSY => 17, EventPeer::EVN_TYPE => 18, EventPeer::TAS_EVN_UID => 19, ),
        BasePeer::TYPE_FIELDNAME => array ('EVN_UID' => 0, 'PRO_UID' => 1, 'EVN_STATUS' => 2, 'EVN_WHEN_OCCURS' => 3, 'EVN_RELATED_TO' => 4, 'TAS_UID' => 5, 'EVN_TAS_UID_FROM' => 6, 'EVN_TAS_UID_TO' => 7, 'EVN_TAS_ESTIMATED_DURATION' => 8, 'EVN_TIME_UNIT' => 9, 'EVN_WHEN' => 10, 'EVN_MAX_ATTEMPTS' => 11, 'EVN_ACTION' => 12, 'EVN_CONDITIONS' => 13, 'EVN_ACTION_PARAMETERS' => 14, 'TRI_UID' => 15, 'EVN_POSX' => 16, 'EVN_POSY' => 17, 'EVN_TYPE' => 18, 'TAS_EVN_UID' => 19, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/EventMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.EventMapBuilder');
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
            $map = EventPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. EventPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(EventPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(EventPeer::EVN_UID);

        $criteria->addSelectColumn(EventPeer::PRO_UID);

        $criteria->addSelectColumn(EventPeer::EVN_STATUS);

        $criteria->addSelectColumn(EventPeer::EVN_WHEN_OCCURS);

        $criteria->addSelectColumn(EventPeer::EVN_RELATED_TO);

        $criteria->addSelectColumn(EventPeer::TAS_UID);

        $criteria->addSelectColumn(EventPeer::EVN_TAS_UID_FROM);

        $criteria->addSelectColumn(EventPeer::EVN_TAS_UID_TO);

        $criteria->addSelectColumn(EventPeer::EVN_TAS_ESTIMATED_DURATION);

        $criteria->addSelectColumn(EventPeer::EVN_TIME_UNIT);

        $criteria->addSelectColumn(EventPeer::EVN_WHEN);

        $criteria->addSelectColumn(EventPeer::EVN_MAX_ATTEMPTS);

        $criteria->addSelectColumn(EventPeer::EVN_ACTION);

        $criteria->addSelectColumn(EventPeer::EVN_CONDITIONS);

        $criteria->addSelectColumn(EventPeer::EVN_ACTION_PARAMETERS);

        $criteria->addSelectColumn(EventPeer::TRI_UID);

        $criteria->addSelectColumn(EventPeer::EVN_POSX);

        $criteria->addSelectColumn(EventPeer::EVN_POSY);

        $criteria->addSelectColumn(EventPeer::EVN_TYPE);

        $criteria->addSelectColumn(EventPeer::TAS_EVN_UID);

    }

    const COUNT = 'COUNT(EVENT.EVN_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT EVENT.EVN_UID)';

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
            $criteria->addSelectColumn(EventPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(EventPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = EventPeer::doSelectRS($criteria, $con);
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
     * @return     Event
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = EventPeer::doSelect($critcopy, $con);
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
        return EventPeer::populateObjects(EventPeer::doSelectRS($criteria, $con));
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
            EventPeer::addSelectColumns($criteria);
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
        $cls = EventPeer::getOMClass();
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
        return EventPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Event or Criteria object.
     *
     * @param      mixed $values Criteria or Event object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Event object
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
     * Method perform an UPDATE on the database, given a Event or Criteria object.
     *
     * @param      mixed $values Criteria or Event object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(EventPeer::EVN_UID);
            $selectCriteria->add(EventPeer::EVN_UID, $criteria->remove(EventPeer::EVN_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the EVENT table.
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
            $affectedRows += BasePeer::doDeleteAll(EventPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Event or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Event object or primary key or array of primary keys
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
            $con = Propel::getConnection(EventPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Event) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(EventPeer::EVN_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Event object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Event $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Event $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(EventPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(EventPeer::TABLE_NAME);

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

        return BasePeer::doValidate(EventPeer::DATABASE_NAME, EventPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Event
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(EventPeer::DATABASE_NAME);

        $criteria->add(EventPeer::EVN_UID, $pk);


        $v = EventPeer::doSelect($criteria, $con);

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
            $criteria->add(EventPeer::EVN_UID, $pks, Criteria::IN);
            $objs = EventPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseEventPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/EventMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.EventMapBuilder');
}

