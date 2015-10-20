<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AbeConfigurationPeer::getOMClass()
include_once 'classes/model/AbeConfiguration.php';

/**
 * Base static class for performing query and update operations on the 'ABE_CONFIGURATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAbeConfigurationPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'ABE_CONFIGURATION';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AbeConfiguration';

    /** The total number of columns. */
    const NUM_COLUMNS = 14;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the ABE_UID field */
    const ABE_UID = 'ABE_CONFIGURATION.ABE_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'ABE_CONFIGURATION.PRO_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'ABE_CONFIGURATION.TAS_UID';

    /** the column name for the ABE_TYPE field */
    const ABE_TYPE = 'ABE_CONFIGURATION.ABE_TYPE';

    /** the column name for the ABE_TEMPLATE field */
    const ABE_TEMPLATE = 'ABE_CONFIGURATION.ABE_TEMPLATE';

    /** the column name for the ABE_DYN_TYPE field */
    const ABE_DYN_TYPE = 'ABE_CONFIGURATION.ABE_DYN_TYPE';

    /** the column name for the DYN_UID field */
    const DYN_UID = 'ABE_CONFIGURATION.DYN_UID';

    /** the column name for the ABE_EMAIL_FIELD field */
    const ABE_EMAIL_FIELD = 'ABE_CONFIGURATION.ABE_EMAIL_FIELD';

    /** the column name for the ABE_ACTION_FIELD field */
    const ABE_ACTION_FIELD = 'ABE_CONFIGURATION.ABE_ACTION_FIELD';

    /** the column name for the ABE_CASE_NOTE_IN_RESPONSE field */
    const ABE_CASE_NOTE_IN_RESPONSE = 'ABE_CONFIGURATION.ABE_CASE_NOTE_IN_RESPONSE';

    /** the column name for the ABE_CREATE_DATE field */
    const ABE_CREATE_DATE = 'ABE_CONFIGURATION.ABE_CREATE_DATE';

    /** the column name for the ABE_UPDATE_DATE field */
    const ABE_UPDATE_DATE = 'ABE_CONFIGURATION.ABE_UPDATE_DATE';

    /** the column name for the ABE_SUBJECT_FIELD field */
    const ABE_SUBJECT_FIELD = 'ABE_CONFIGURATION.ABE_SUBJECT_FIELD';

    /** the column name for the ABE_MAILSERVER_OR_MAILCURRENT field */
    const ABE_MAILSERVER_OR_MAILCURRENT = 'ABE_CONFIGURATION.ABE_MAILSERVER_OR_MAILCURRENT';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AbeUid', 'ProUid', 'TasUid', 'AbeType', 'AbeTemplate', 'AbeDynType', 'DynUid', 'AbeEmailField', 'AbeActionField', 'AbeCaseNoteInResponse', 'AbeCreateDate', 'AbeUpdateDate', 'AbeSubjectField', 'AbeMailserverOrMailcurrent', ),
        BasePeer::TYPE_COLNAME => array (AbeConfigurationPeer::ABE_UID, AbeConfigurationPeer::PRO_UID, AbeConfigurationPeer::TAS_UID, AbeConfigurationPeer::ABE_TYPE, AbeConfigurationPeer::ABE_TEMPLATE, AbeConfigurationPeer::ABE_DYN_TYPE, AbeConfigurationPeer::DYN_UID, AbeConfigurationPeer::ABE_EMAIL_FIELD, AbeConfigurationPeer::ABE_ACTION_FIELD, AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE, AbeConfigurationPeer::ABE_CREATE_DATE, AbeConfigurationPeer::ABE_UPDATE_DATE, AbeConfigurationPeer::ABE_SUBJECT_FIELD, AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT, ),
        BasePeer::TYPE_FIELDNAME => array ('ABE_UID', 'PRO_UID', 'TAS_UID', 'ABE_TYPE', 'ABE_TEMPLATE', 'ABE_DYN_TYPE', 'DYN_UID', 'ABE_EMAIL_FIELD', 'ABE_ACTION_FIELD', 'ABE_CASE_NOTE_IN_RESPONSE', 'ABE_CREATE_DATE', 'ABE_UPDATE_DATE', 'ABE_SUBJECT_FIELD', 'ABE_MAILSERVER_OR_MAILCURRENT', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AbeUid' => 0, 'ProUid' => 1, 'TasUid' => 2, 'AbeType' => 3, 'AbeTemplate' => 4, 'AbeDynType' => 5, 'DynUid' => 6, 'AbeEmailField' => 7, 'AbeActionField' => 8, 'AbeCaseNoteInResponse' => 9, 'AbeCreateDate' => 10, 'AbeUpdateDate' => 11, 'AbeSubjectField' => 12, 'AbeMailserverOrMailcurrent' => 13, ),
        BasePeer::TYPE_COLNAME => array (AbeConfigurationPeer::ABE_UID => 0, AbeConfigurationPeer::PRO_UID => 1, AbeConfigurationPeer::TAS_UID => 2, AbeConfigurationPeer::ABE_TYPE => 3, AbeConfigurationPeer::ABE_TEMPLATE => 4, AbeConfigurationPeer::ABE_DYN_TYPE => 5, AbeConfigurationPeer::DYN_UID => 6, AbeConfigurationPeer::ABE_EMAIL_FIELD => 7, AbeConfigurationPeer::ABE_ACTION_FIELD => 8, AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE => 9, AbeConfigurationPeer::ABE_CREATE_DATE => 10, AbeConfigurationPeer::ABE_UPDATE_DATE => 11, AbeConfigurationPeer::ABE_SUBJECT_FIELD => 12, AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT => 13, ),
        BasePeer::TYPE_FIELDNAME => array ('ABE_UID' => 0, 'PRO_UID' => 1, 'TAS_UID' => 2, 'ABE_TYPE' => 3, 'ABE_TEMPLATE' => 4, 'ABE_DYN_TYPE' => 5, 'DYN_UID' => 6, 'ABE_EMAIL_FIELD' => 7, 'ABE_ACTION_FIELD' => 8, 'ABE_CASE_NOTE_IN_RESPONSE' => 9, 'ABE_CREATE_DATE' => 10, 'ABE_UPDATE_DATE' => 11, 'ABE_SUBJECT_FIELD' => 12, 'ABE_MAILSERVER_OR_MAILCURRENT' => 13, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AbeConfigurationMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AbeConfigurationMapBuilder');
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
            $map = AbeConfigurationPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. AbeConfigurationPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AbeConfigurationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_UID);

        $criteria->addSelectColumn(AbeConfigurationPeer::PRO_UID);

        $criteria->addSelectColumn(AbeConfigurationPeer::TAS_UID);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_TYPE);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_TEMPLATE);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_DYN_TYPE);

        $criteria->addSelectColumn(AbeConfigurationPeer::DYN_UID);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_EMAIL_FIELD);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_ACTION_FIELD);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_CREATE_DATE);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_UPDATE_DATE);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_SUBJECT_FIELD);

        $criteria->addSelectColumn(AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT);

    }

    const COUNT = 'COUNT(ABE_CONFIGURATION.ABE_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT ABE_CONFIGURATION.ABE_UID)';

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
            $criteria->addSelectColumn(AbeConfigurationPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AbeConfigurationPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AbeConfigurationPeer::doSelectRS($criteria, $con);
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
     * @return     AbeConfiguration
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AbeConfigurationPeer::doSelect($critcopy, $con);
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
        return AbeConfigurationPeer::populateObjects(AbeConfigurationPeer::doSelectRS($criteria, $con));
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
            AbeConfigurationPeer::addSelectColumns($criteria);
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
        $cls = AbeConfigurationPeer::getOMClass();
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
        return AbeConfigurationPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AbeConfiguration or Criteria object.
     *
     * @param      mixed $values Criteria or AbeConfiguration object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from AbeConfiguration object
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
     * Method perform an UPDATE on the database, given a AbeConfiguration or Criteria object.
     *
     * @param      mixed $values Criteria or AbeConfiguration object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(AbeConfigurationPeer::ABE_UID);
            $selectCriteria->add(AbeConfigurationPeer::ABE_UID, $criteria->remove(AbeConfigurationPeer::ABE_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the ABE_CONFIGURATION table.
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
            $affectedRows += BasePeer::doDeleteAll(AbeConfigurationPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AbeConfiguration or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AbeConfiguration object or primary key or array of primary keys
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
            $con = Propel::getConnection(AbeConfigurationPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AbeConfiguration) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(AbeConfigurationPeer::ABE_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given AbeConfiguration object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AbeConfiguration $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AbeConfiguration $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AbeConfigurationPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AbeConfigurationPeer::TABLE_NAME);

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

        return BasePeer::doValidate(AbeConfigurationPeer::DATABASE_NAME, AbeConfigurationPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     AbeConfiguration
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(AbeConfigurationPeer::DATABASE_NAME);

        $criteria->add(AbeConfigurationPeer::ABE_UID, $pk);


        $v = AbeConfigurationPeer::doSelect($criteria, $con);

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
            $criteria->add(AbeConfigurationPeer::ABE_UID, $pks, Criteria::IN);
            $objs = AbeConfigurationPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAbeConfigurationPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AbeConfigurationMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AbeConfigurationMapBuilder');
}

