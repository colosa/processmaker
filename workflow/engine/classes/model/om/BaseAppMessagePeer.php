<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AppMessagePeer::getOMClass()
include_once 'classes/model/AppMessage.php';

/**
 * Base static class for performing query and update operations on the 'APP_MESSAGE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppMessagePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'APP_MESSAGE';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AppMessage';

    /** The total number of columns. */
    const NUM_COLUMNS = 17;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_MSG_UID field */
    const APP_MSG_UID = 'APP_MESSAGE.APP_MSG_UID';

    /** the column name for the MSG_UID field */
    const MSG_UID = 'APP_MESSAGE.MSG_UID';

    /** the column name for the APP_UID field */
    const APP_UID = 'APP_MESSAGE.APP_UID';

    /** the column name for the DEL_INDEX field */
    const DEL_INDEX = 'APP_MESSAGE.DEL_INDEX';

    /** the column name for the APP_MSG_TYPE field */
    const APP_MSG_TYPE = 'APP_MESSAGE.APP_MSG_TYPE';

    /** the column name for the APP_MSG_SUBJECT field */
    const APP_MSG_SUBJECT = 'APP_MESSAGE.APP_MSG_SUBJECT';

    /** the column name for the APP_MSG_FROM field */
    const APP_MSG_FROM = 'APP_MESSAGE.APP_MSG_FROM';

    /** the column name for the APP_MSG_TO field */
    const APP_MSG_TO = 'APP_MESSAGE.APP_MSG_TO';

    /** the column name for the APP_MSG_BODY field */
    const APP_MSG_BODY = 'APP_MESSAGE.APP_MSG_BODY';

    /** the column name for the APP_MSG_DATE field */
    const APP_MSG_DATE = 'APP_MESSAGE.APP_MSG_DATE';

    /** the column name for the APP_MSG_CC field */
    const APP_MSG_CC = 'APP_MESSAGE.APP_MSG_CC';

    /** the column name for the APP_MSG_BCC field */
    const APP_MSG_BCC = 'APP_MESSAGE.APP_MSG_BCC';

    /** the column name for the APP_MSG_TEMPLATE field */
    const APP_MSG_TEMPLATE = 'APP_MESSAGE.APP_MSG_TEMPLATE';

    /** the column name for the APP_MSG_STATUS field */
    const APP_MSG_STATUS = 'APP_MESSAGE.APP_MSG_STATUS';

    /** the column name for the APP_MSG_ATTACH field */
    const APP_MSG_ATTACH = 'APP_MESSAGE.APP_MSG_ATTACH';

    /** the column name for the APP_MSG_SEND_DATE field */
    const APP_MSG_SEND_DATE = 'APP_MESSAGE.APP_MSG_SEND_DATE';

    /** the column name for the APP_MSG_SHOW_MESSAGE field */
    const APP_MSG_SHOW_MESSAGE = 'APP_MESSAGE.APP_MSG_SHOW_MESSAGE';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppMsgUid', 'MsgUid', 'AppUid', 'DelIndex', 'AppMsgType', 'AppMsgSubject', 'AppMsgFrom', 'AppMsgTo', 'AppMsgBody', 'AppMsgDate', 'AppMsgCc', 'AppMsgBcc', 'AppMsgTemplate', 'AppMsgStatus', 'AppMsgAttach', 'AppMsgSendDate', 'AppMsgShowMessage', ),
        BasePeer::TYPE_COLNAME => array (AppMessagePeer::APP_MSG_UID, AppMessagePeer::MSG_UID, AppMessagePeer::APP_UID, AppMessagePeer::DEL_INDEX, AppMessagePeer::APP_MSG_TYPE, AppMessagePeer::APP_MSG_SUBJECT, AppMessagePeer::APP_MSG_FROM, AppMessagePeer::APP_MSG_TO, AppMessagePeer::APP_MSG_BODY, AppMessagePeer::APP_MSG_DATE, AppMessagePeer::APP_MSG_CC, AppMessagePeer::APP_MSG_BCC, AppMessagePeer::APP_MSG_TEMPLATE, AppMessagePeer::APP_MSG_STATUS, AppMessagePeer::APP_MSG_ATTACH, AppMessagePeer::APP_MSG_SEND_DATE, AppMessagePeer::APP_MSG_SHOW_MESSAGE, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_MSG_UID', 'MSG_UID', 'APP_UID', 'DEL_INDEX', 'APP_MSG_TYPE', 'APP_MSG_SUBJECT', 'APP_MSG_FROM', 'APP_MSG_TO', 'APP_MSG_BODY', 'APP_MSG_DATE', 'APP_MSG_CC', 'APP_MSG_BCC', 'APP_MSG_TEMPLATE', 'APP_MSG_STATUS', 'APP_MSG_ATTACH', 'APP_MSG_SEND_DATE', 'APP_MSG_SHOW_MESSAGE', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppMsgUid' => 0, 'MsgUid' => 1, 'AppUid' => 2, 'DelIndex' => 3, 'AppMsgType' => 4, 'AppMsgSubject' => 5, 'AppMsgFrom' => 6, 'AppMsgTo' => 7, 'AppMsgBody' => 8, 'AppMsgDate' => 9, 'AppMsgCc' => 10, 'AppMsgBcc' => 11, 'AppMsgTemplate' => 12, 'AppMsgStatus' => 13, 'AppMsgAttach' => 14, 'AppMsgSendDate' => 15, 'AppMsgShowMessage' => 16, ),
        BasePeer::TYPE_COLNAME => array (AppMessagePeer::APP_MSG_UID => 0, AppMessagePeer::MSG_UID => 1, AppMessagePeer::APP_UID => 2, AppMessagePeer::DEL_INDEX => 3, AppMessagePeer::APP_MSG_TYPE => 4, AppMessagePeer::APP_MSG_SUBJECT => 5, AppMessagePeer::APP_MSG_FROM => 6, AppMessagePeer::APP_MSG_TO => 7, AppMessagePeer::APP_MSG_BODY => 8, AppMessagePeer::APP_MSG_DATE => 9, AppMessagePeer::APP_MSG_CC => 10, AppMessagePeer::APP_MSG_BCC => 11, AppMessagePeer::APP_MSG_TEMPLATE => 12, AppMessagePeer::APP_MSG_STATUS => 13, AppMessagePeer::APP_MSG_ATTACH => 14, AppMessagePeer::APP_MSG_SEND_DATE => 15, AppMessagePeer::APP_MSG_SHOW_MESSAGE => 16, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_MSG_UID' => 0, 'MSG_UID' => 1, 'APP_UID' => 2, 'DEL_INDEX' => 3, 'APP_MSG_TYPE' => 4, 'APP_MSG_SUBJECT' => 5, 'APP_MSG_FROM' => 6, 'APP_MSG_TO' => 7, 'APP_MSG_BODY' => 8, 'APP_MSG_DATE' => 9, 'APP_MSG_CC' => 10, 'APP_MSG_BCC' => 11, 'APP_MSG_TEMPLATE' => 12, 'APP_MSG_STATUS' => 13, 'APP_MSG_ATTACH' => 14, 'APP_MSG_SEND_DATE' => 15, 'APP_MSG_SHOW_MESSAGE' => 16, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AppMessageMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AppMessageMapBuilder');
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
            $map = AppMessagePeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AppMessagePeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AppMessagePeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_UID);

        $criteria->addSelectColumn(AppMessagePeer::MSG_UID);

        $criteria->addSelectColumn(AppMessagePeer::APP_UID);

        $criteria->addSelectColumn(AppMessagePeer::DEL_INDEX);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TYPE);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SUBJECT);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_FROM);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TO);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_BODY);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_DATE);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_CC);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_BCC);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TEMPLATE);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_STATUS);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_ATTACH);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SEND_DATE);

        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SHOW_MESSAGE);

    }

    const COUNT = 'COUNT(APP_MESSAGE.APP_MSG_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APP_MESSAGE.APP_MSG_UID)';

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
            $criteria->addSelectColumn(AppMessagePeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AppMessagePeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AppMessagePeer::doSelectRS($criteria, $con);
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
     * @return     AppMessage
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AppMessagePeer::doSelect($critcopy, $con);
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
        return AppMessagePeer::populateObjects(AppMessagePeer::doSelectRS($criteria, $con));
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
            AppMessagePeer::addSelectColumns($criteria);
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
        $cls = AppMessagePeer::getOMClass();
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
        return AppMessagePeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AppMessage or Criteria object.
     *
     * @param      mixed $values Criteria or AppMessage object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AppMessage object
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
     * Method perform an UPDATE on the database, given a AppMessage or Criteria object.
     *
     * @param      mixed $values Criteria or AppMessage object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AppMessagePeer::APP_MSG_UID);
            $selectCriteria->add(AppMessagePeer::APP_MSG_UID, $criteria->remove(AppMessagePeer::APP_MSG_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the APP_MESSAGE table.
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
            $affectedRows += BasePeer::doDeleteAll(AppMessagePeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AppMessage or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AppMessage object or primary key or array of primary keys
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
            $con = Propel::getConnection(AppMessagePeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AppMessage) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(AppMessagePeer::APP_MSG_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given AppMessage object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AppMessage $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AppMessage $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AppMessagePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AppMessagePeer::TABLE_NAME);

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

        return BasePeer::doValidate(AppMessagePeer::DATABASE_NAME, AppMessagePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     AppMessage
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(AppMessagePeer::DATABASE_NAME);

        $criteria->add(AppMessagePeer::APP_MSG_UID, $pk);


        $v = AppMessagePeer::doSelect($criteria, $con);

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
            $criteria->add(AppMessagePeer::APP_MSG_UID, $pks, Criteria::IN);
            $objs = AppMessagePeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAppMessagePeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AppMessageMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AppMessageMapBuilder');
}

