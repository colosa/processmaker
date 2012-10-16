<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ProcessPeer::getOMClass()
include_once 'classes/model/Process.php';

/**
 * Base static class for performing query and update operations on the 'PROCESS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseProcessPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'PROCESS';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Process';

    /** The total number of columns. */
    const NUM_COLUMNS = 30;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the PRO_UID field */
    const PRO_UID = 'PROCESS.PRO_UID';

    /** the column name for the PRO_PARENT field */
    const PRO_PARENT = 'PROCESS.PRO_PARENT';

    /** the column name for the PRO_TIME field */
    const PRO_TIME = 'PROCESS.PRO_TIME';

    /** the column name for the PRO_TIMEUNIT field */
    const PRO_TIMEUNIT = 'PROCESS.PRO_TIMEUNIT';

    /** the column name for the PRO_STATUS field */
    const PRO_STATUS = 'PROCESS.PRO_STATUS';

    /** the column name for the PRO_TYPE_DAY field */
    const PRO_TYPE_DAY = 'PROCESS.PRO_TYPE_DAY';

    /** the column name for the PRO_TYPE field */
    const PRO_TYPE = 'PROCESS.PRO_TYPE';

    /** the column name for the PRO_ASSIGNMENT field */
    const PRO_ASSIGNMENT = 'PROCESS.PRO_ASSIGNMENT';

    /** the column name for the PRO_SHOW_MAP field */
    const PRO_SHOW_MAP = 'PROCESS.PRO_SHOW_MAP';

    /** the column name for the PRO_SHOW_MESSAGE field */
    const PRO_SHOW_MESSAGE = 'PROCESS.PRO_SHOW_MESSAGE';

    /** the column name for the PRO_SUBPROCESS field */
    const PRO_SUBPROCESS = 'PROCESS.PRO_SUBPROCESS';

    /** the column name for the PRO_TRI_DELETED field */
    const PRO_TRI_DELETED = 'PROCESS.PRO_TRI_DELETED';

    /** the column name for the PRO_TRI_CANCELED field */
    const PRO_TRI_CANCELED = 'PROCESS.PRO_TRI_CANCELED';

    /** the column name for the PRO_TRI_PAUSED field */
    const PRO_TRI_PAUSED = 'PROCESS.PRO_TRI_PAUSED';

    /** the column name for the PRO_TRI_REASSIGNED field */
    const PRO_TRI_REASSIGNED = 'PROCESS.PRO_TRI_REASSIGNED';

    /** the column name for the PRO_SHOW_DELEGATE field */
    const PRO_SHOW_DELEGATE = 'PROCESS.PRO_SHOW_DELEGATE';

    /** the column name for the PRO_SHOW_DYNAFORM field */
    const PRO_SHOW_DYNAFORM = 'PROCESS.PRO_SHOW_DYNAFORM';

    /** the column name for the PRO_CATEGORY field */
    const PRO_CATEGORY = 'PROCESS.PRO_CATEGORY';

    /** the column name for the PRO_SUB_CATEGORY field */
    const PRO_SUB_CATEGORY = 'PROCESS.PRO_SUB_CATEGORY';

    /** the column name for the PRO_INDUSTRY field */
    const PRO_INDUSTRY = 'PROCESS.PRO_INDUSTRY';

    /** the column name for the PRO_UPDATE_DATE field */
    const PRO_UPDATE_DATE = 'PROCESS.PRO_UPDATE_DATE';

    /** the column name for the PRO_CREATE_DATE field */
    const PRO_CREATE_DATE = 'PROCESS.PRO_CREATE_DATE';

    /** the column name for the PRO_CREATE_USER field */
    const PRO_CREATE_USER = 'PROCESS.PRO_CREATE_USER';

    /** the column name for the PRO_HEIGHT field */
    const PRO_HEIGHT = 'PROCESS.PRO_HEIGHT';

    /** the column name for the PRO_WIDTH field */
    const PRO_WIDTH = 'PROCESS.PRO_WIDTH';

    /** the column name for the PRO_TITLE_X field */
    const PRO_TITLE_X = 'PROCESS.PRO_TITLE_X';

    /** the column name for the PRO_TITLE_Y field */
    const PRO_TITLE_Y = 'PROCESS.PRO_TITLE_Y';

    /** the column name for the PRO_DEBUG field */
    const PRO_DEBUG = 'PROCESS.PRO_DEBUG';

    /** the column name for the PRO_DYNAFORMS field */
    const PRO_DYNAFORMS = 'PROCESS.PRO_DYNAFORMS';

    /** the column name for the PRO_DERIVATION_SCREEN_TPL field */
    const PRO_DERIVATION_SCREEN_TPL = 'PROCESS.PRO_DERIVATION_SCREEN_TPL';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('ProUid', 'ProParent', 'ProTime', 'ProTimeunit', 'ProStatus', 'ProTypeDay', 'ProType', 'ProAssignment', 'ProShowMap', 'ProShowMessage', 'ProSubprocess', 'ProTriDeleted', 'ProTriCanceled', 'ProTriPaused', 'ProTriReassigned', 'ProShowDelegate', 'ProShowDynaform', 'ProCategory', 'ProSubCategory', 'ProIndustry', 'ProUpdateDate', 'ProCreateDate', 'ProCreateUser', 'ProHeight', 'ProWidth', 'ProTitleX', 'ProTitleY', 'ProDebug', 'ProDynaforms', 'ProDerivationScreenTpl', ),
        BasePeer::TYPE_COLNAME => array (ProcessPeer::PRO_UID, ProcessPeer::PRO_PARENT, ProcessPeer::PRO_TIME, ProcessPeer::PRO_TIMEUNIT, ProcessPeer::PRO_STATUS, ProcessPeer::PRO_TYPE_DAY, ProcessPeer::PRO_TYPE, ProcessPeer::PRO_ASSIGNMENT, ProcessPeer::PRO_SHOW_MAP, ProcessPeer::PRO_SHOW_MESSAGE, ProcessPeer::PRO_SUBPROCESS, ProcessPeer::PRO_TRI_DELETED, ProcessPeer::PRO_TRI_CANCELED, ProcessPeer::PRO_TRI_PAUSED, ProcessPeer::PRO_TRI_REASSIGNED, ProcessPeer::PRO_SHOW_DELEGATE, ProcessPeer::PRO_SHOW_DYNAFORM, ProcessPeer::PRO_CATEGORY, ProcessPeer::PRO_SUB_CATEGORY, ProcessPeer::PRO_INDUSTRY, ProcessPeer::PRO_UPDATE_DATE, ProcessPeer::PRO_CREATE_DATE, ProcessPeer::PRO_CREATE_USER, ProcessPeer::PRO_HEIGHT, ProcessPeer::PRO_WIDTH, ProcessPeer::PRO_TITLE_X, ProcessPeer::PRO_TITLE_Y, ProcessPeer::PRO_DEBUG, ProcessPeer::PRO_DYNAFORMS, ProcessPeer::PRO_DERIVATION_SCREEN_TPL, ),
        BasePeer::TYPE_FIELDNAME => array ('PRO_UID', 'PRO_PARENT', 'PRO_TIME', 'PRO_TIMEUNIT', 'PRO_STATUS', 'PRO_TYPE_DAY', 'PRO_TYPE', 'PRO_ASSIGNMENT', 'PRO_SHOW_MAP', 'PRO_SHOW_MESSAGE', 'PRO_SUBPROCESS', 'PRO_TRI_DELETED', 'PRO_TRI_CANCELED', 'PRO_TRI_PAUSED', 'PRO_TRI_REASSIGNED', 'PRO_SHOW_DELEGATE', 'PRO_SHOW_DYNAFORM', 'PRO_CATEGORY', 'PRO_SUB_CATEGORY', 'PRO_INDUSTRY', 'PRO_UPDATE_DATE', 'PRO_CREATE_DATE', 'PRO_CREATE_USER', 'PRO_HEIGHT', 'PRO_WIDTH', 'PRO_TITLE_X', 'PRO_TITLE_Y', 'PRO_DEBUG', 'PRO_DYNAFORMS', 'PRO_DERIVATION_SCREEN_TPL', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('ProUid' => 0, 'ProParent' => 1, 'ProTime' => 2, 'ProTimeunit' => 3, 'ProStatus' => 4, 'ProTypeDay' => 5, 'ProType' => 6, 'ProAssignment' => 7, 'ProShowMap' => 8, 'ProShowMessage' => 9, 'ProSubprocess' => 10, 'ProTriDeleted' => 11, 'ProTriCanceled' => 12, 'ProTriPaused' => 13, 'ProTriReassigned' => 14, 'ProShowDelegate' => 15, 'ProShowDynaform' => 16, 'ProCategory' => 17, 'ProSubCategory' => 18, 'ProIndustry' => 19, 'ProUpdateDate' => 20, 'ProCreateDate' => 21, 'ProCreateUser' => 22, 'ProHeight' => 23, 'ProWidth' => 24, 'ProTitleX' => 25, 'ProTitleY' => 26, 'ProDebug' => 27, 'ProDynaforms' => 28, 'ProDerivationScreenTpl' => 29, ),
        BasePeer::TYPE_COLNAME => array (ProcessPeer::PRO_UID => 0, ProcessPeer::PRO_PARENT => 1, ProcessPeer::PRO_TIME => 2, ProcessPeer::PRO_TIMEUNIT => 3, ProcessPeer::PRO_STATUS => 4, ProcessPeer::PRO_TYPE_DAY => 5, ProcessPeer::PRO_TYPE => 6, ProcessPeer::PRO_ASSIGNMENT => 7, ProcessPeer::PRO_SHOW_MAP => 8, ProcessPeer::PRO_SHOW_MESSAGE => 9, ProcessPeer::PRO_SUBPROCESS => 10, ProcessPeer::PRO_TRI_DELETED => 11, ProcessPeer::PRO_TRI_CANCELED => 12, ProcessPeer::PRO_TRI_PAUSED => 13, ProcessPeer::PRO_TRI_REASSIGNED => 14, ProcessPeer::PRO_SHOW_DELEGATE => 15, ProcessPeer::PRO_SHOW_DYNAFORM => 16, ProcessPeer::PRO_CATEGORY => 17, ProcessPeer::PRO_SUB_CATEGORY => 18, ProcessPeer::PRO_INDUSTRY => 19, ProcessPeer::PRO_UPDATE_DATE => 20, ProcessPeer::PRO_CREATE_DATE => 21, ProcessPeer::PRO_CREATE_USER => 22, ProcessPeer::PRO_HEIGHT => 23, ProcessPeer::PRO_WIDTH => 24, ProcessPeer::PRO_TITLE_X => 25, ProcessPeer::PRO_TITLE_Y => 26, ProcessPeer::PRO_DEBUG => 27, ProcessPeer::PRO_DYNAFORMS => 28, ProcessPeer::PRO_DERIVATION_SCREEN_TPL => 29, ),
        BasePeer::TYPE_FIELDNAME => array ('PRO_UID' => 0, 'PRO_PARENT' => 1, 'PRO_TIME' => 2, 'PRO_TIMEUNIT' => 3, 'PRO_STATUS' => 4, 'PRO_TYPE_DAY' => 5, 'PRO_TYPE' => 6, 'PRO_ASSIGNMENT' => 7, 'PRO_SHOW_MAP' => 8, 'PRO_SHOW_MESSAGE' => 9, 'PRO_SUBPROCESS' => 10, 'PRO_TRI_DELETED' => 11, 'PRO_TRI_CANCELED' => 12, 'PRO_TRI_PAUSED' => 13, 'PRO_TRI_REASSIGNED' => 14, 'PRO_SHOW_DELEGATE' => 15, 'PRO_SHOW_DYNAFORM' => 16, 'PRO_CATEGORY' => 17, 'PRO_SUB_CATEGORY' => 18, 'PRO_INDUSTRY' => 19, 'PRO_UPDATE_DATE' => 20, 'PRO_CREATE_DATE' => 21, 'PRO_CREATE_USER' => 22, 'PRO_HEIGHT' => 23, 'PRO_WIDTH' => 24, 'PRO_TITLE_X' => 25, 'PRO_TITLE_Y' => 26, 'PRO_DEBUG' => 27, 'PRO_DYNAFORMS' => 28, 'PRO_DERIVATION_SCREEN_TPL' => 29, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/ProcessMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.ProcessMapBuilder');
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
            $map = ProcessPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. ProcessPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ProcessPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(ProcessPeer::PRO_UID);

        $criteria->addSelectColumn(ProcessPeer::PRO_PARENT);

        $criteria->addSelectColumn(ProcessPeer::PRO_TIME);

        $criteria->addSelectColumn(ProcessPeer::PRO_TIMEUNIT);

        $criteria->addSelectColumn(ProcessPeer::PRO_STATUS);

        $criteria->addSelectColumn(ProcessPeer::PRO_TYPE_DAY);

        $criteria->addSelectColumn(ProcessPeer::PRO_TYPE);

        $criteria->addSelectColumn(ProcessPeer::PRO_ASSIGNMENT);

        $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_MAP);

        $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_MESSAGE);

        $criteria->addSelectColumn(ProcessPeer::PRO_SUBPROCESS);

        $criteria->addSelectColumn(ProcessPeer::PRO_TRI_DELETED);

        $criteria->addSelectColumn(ProcessPeer::PRO_TRI_CANCELED);

        $criteria->addSelectColumn(ProcessPeer::PRO_TRI_PAUSED);

        $criteria->addSelectColumn(ProcessPeer::PRO_TRI_REASSIGNED);

        $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_DELEGATE);

        $criteria->addSelectColumn(ProcessPeer::PRO_SHOW_DYNAFORM);

        $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);

        $criteria->addSelectColumn(ProcessPeer::PRO_SUB_CATEGORY);

        $criteria->addSelectColumn(ProcessPeer::PRO_INDUSTRY);

        $criteria->addSelectColumn(ProcessPeer::PRO_UPDATE_DATE);

        $criteria->addSelectColumn(ProcessPeer::PRO_CREATE_DATE);

        $criteria->addSelectColumn(ProcessPeer::PRO_CREATE_USER);

        $criteria->addSelectColumn(ProcessPeer::PRO_HEIGHT);

        $criteria->addSelectColumn(ProcessPeer::PRO_WIDTH);

        $criteria->addSelectColumn(ProcessPeer::PRO_TITLE_X);

        $criteria->addSelectColumn(ProcessPeer::PRO_TITLE_Y);

        $criteria->addSelectColumn(ProcessPeer::PRO_DEBUG);

        $criteria->addSelectColumn(ProcessPeer::PRO_DYNAFORMS);

        $criteria->addSelectColumn(ProcessPeer::PRO_DERIVATION_SCREEN_TPL);

    }

    const COUNT = 'COUNT(PROCESS.PRO_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT PROCESS.PRO_UID)';

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
            $criteria->addSelectColumn(ProcessPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(ProcessPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = ProcessPeer::doSelectRS($criteria, $con);
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
     * @return     Process
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ProcessPeer::doSelect($critcopy, $con);
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
        return ProcessPeer::populateObjects(ProcessPeer::doSelectRS($criteria, $con));
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
            ProcessPeer::addSelectColumns($criteria);
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
        $cls = ProcessPeer::getOMClass();
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
        return ProcessPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Process or Criteria object.
     *
     * @param      mixed $values Criteria or Process object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Process object
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
     * Method perform an UPDATE on the database, given a Process or Criteria object.
     *
     * @param      mixed $values Criteria or Process object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(ProcessPeer::PRO_UID);
            $selectCriteria->add(ProcessPeer::PRO_UID, $criteria->remove(ProcessPeer::PRO_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the PROCESS table.
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
            $affectedRows += BasePeer::doDeleteAll(ProcessPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Process or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Process object or primary key or array of primary keys
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
            $con = Propel::getConnection(ProcessPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Process) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(ProcessPeer::PRO_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Process object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Process $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Process $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ProcessPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ProcessPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(ProcessPeer::PRO_TIMEUNIT))
            $columns[ProcessPeer::PRO_TIMEUNIT] = $obj->getProTimeunit();

        if ($obj->isNew() || $obj->isColumnModified(ProcessPeer::PRO_STATUS))
            $columns[ProcessPeer::PRO_STATUS] = $obj->getProStatus();

        if ($obj->isNew() || $obj->isColumnModified(ProcessPeer::PRO_TYPE))
            $columns[ProcessPeer::PRO_TYPE] = $obj->getProType();

        if ($obj->isNew() || $obj->isColumnModified(ProcessPeer::PRO_ASSIGNMENT))
            $columns[ProcessPeer::PRO_ASSIGNMENT] = $obj->getProAssignment();

        }

        return BasePeer::doValidate(ProcessPeer::DATABASE_NAME, ProcessPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Process
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(ProcessPeer::DATABASE_NAME);

        $criteria->add(ProcessPeer::PRO_UID, $pk);


        $v = ProcessPeer::doSelect($criteria, $con);

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
            $criteria->add(ProcessPeer::PRO_UID, $pks, Criteria::IN);
            $objs = ProcessPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseProcessPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/ProcessMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.ProcessMapBuilder');
}

