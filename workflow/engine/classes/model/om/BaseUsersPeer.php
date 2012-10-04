<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by UsersPeer::getOMClass()
include_once 'classes/model/Users.php';

/**
 * Base static class for performing query and update operations on the 'USERS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseUsersPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'USERS';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Users';

    /** The total number of columns. */
    const NUM_COLUMNS = 26;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the USR_UID field */
    const USR_UID = 'USERS.USR_UID';

    /** the column name for the USR_USERNAME field */
    const USR_USERNAME = 'USERS.USR_USERNAME';

    /** the column name for the USR_PASSWORD field */
    const USR_PASSWORD = 'USERS.USR_PASSWORD';

    /** the column name for the USR_FIRSTNAME field */
    const USR_FIRSTNAME = 'USERS.USR_FIRSTNAME';

    /** the column name for the USR_LASTNAME field */
    const USR_LASTNAME = 'USERS.USR_LASTNAME';

    /** the column name for the USR_EMAIL field */
    const USR_EMAIL = 'USERS.USR_EMAIL';

    /** the column name for the USR_DUE_DATE field */
    const USR_DUE_DATE = 'USERS.USR_DUE_DATE';

    /** the column name for the USR_CREATE_DATE field */
    const USR_CREATE_DATE = 'USERS.USR_CREATE_DATE';

    /** the column name for the USR_UPDATE_DATE field */
    const USR_UPDATE_DATE = 'USERS.USR_UPDATE_DATE';

    /** the column name for the USR_STATUS field */
    const USR_STATUS = 'USERS.USR_STATUS';

    /** the column name for the USR_COUNTRY field */
    const USR_COUNTRY = 'USERS.USR_COUNTRY';

    /** the column name for the USR_CITY field */
    const USR_CITY = 'USERS.USR_CITY';

    /** the column name for the USR_LOCATION field */
    const USR_LOCATION = 'USERS.USR_LOCATION';

    /** the column name for the USR_ADDRESS field */
    const USR_ADDRESS = 'USERS.USR_ADDRESS';

    /** the column name for the USR_PHONE field */
    const USR_PHONE = 'USERS.USR_PHONE';

    /** the column name for the USR_FAX field */
    const USR_FAX = 'USERS.USR_FAX';

    /** the column name for the USR_CELLULAR field */
    const USR_CELLULAR = 'USERS.USR_CELLULAR';

    /** the column name for the USR_ZIP_CODE field */
    const USR_ZIP_CODE = 'USERS.USR_ZIP_CODE';

    /** the column name for the DEP_UID field */
    const DEP_UID = 'USERS.DEP_UID';

    /** the column name for the USR_POSITION field */
    const USR_POSITION = 'USERS.USR_POSITION';

    /** the column name for the USR_RESUME field */
    const USR_RESUME = 'USERS.USR_RESUME';

    /** the column name for the USR_BIRTHDAY field */
    const USR_BIRTHDAY = 'USERS.USR_BIRTHDAY';

    /** the column name for the USR_ROLE field */
    const USR_ROLE = 'USERS.USR_ROLE';

    /** the column name for the USR_REPORTS_TO field */
    const USR_REPORTS_TO = 'USERS.USR_REPORTS_TO';

    /** the column name for the USR_REPLACED_BY field */
    const USR_REPLACED_BY = 'USERS.USR_REPLACED_BY';

    /** the column name for the USR_UX field */
    const USR_UX = 'USERS.USR_UX';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('UsrUid', 'UsrUsername', 'UsrPassword', 'UsrFirstname', 'UsrLastname', 'UsrEmail', 'UsrDueDate', 'UsrCreateDate', 'UsrUpdateDate', 'UsrStatus', 'UsrCountry', 'UsrCity', 'UsrLocation', 'UsrAddress', 'UsrPhone', 'UsrFax', 'UsrCellular', 'UsrZipCode', 'DepUid', 'UsrPosition', 'UsrResume', 'UsrBirthday', 'UsrRole', 'UsrReportsTo', 'UsrReplacedBy', 'UsrUx', ),
        BasePeer::TYPE_COLNAME => array (UsersPeer::USR_UID, UsersPeer::USR_USERNAME, UsersPeer::USR_PASSWORD, UsersPeer::USR_FIRSTNAME, UsersPeer::USR_LASTNAME, UsersPeer::USR_EMAIL, UsersPeer::USR_DUE_DATE, UsersPeer::USR_CREATE_DATE, UsersPeer::USR_UPDATE_DATE, UsersPeer::USR_STATUS, UsersPeer::USR_COUNTRY, UsersPeer::USR_CITY, UsersPeer::USR_LOCATION, UsersPeer::USR_ADDRESS, UsersPeer::USR_PHONE, UsersPeer::USR_FAX, UsersPeer::USR_CELLULAR, UsersPeer::USR_ZIP_CODE, UsersPeer::DEP_UID, UsersPeer::USR_POSITION, UsersPeer::USR_RESUME, UsersPeer::USR_BIRTHDAY, UsersPeer::USR_ROLE, UsersPeer::USR_REPORTS_TO, UsersPeer::USR_REPLACED_BY, UsersPeer::USR_UX, ),
        BasePeer::TYPE_FIELDNAME => array ('USR_UID', 'USR_USERNAME', 'USR_PASSWORD', 'USR_FIRSTNAME', 'USR_LASTNAME', 'USR_EMAIL', 'USR_DUE_DATE', 'USR_CREATE_DATE', 'USR_UPDATE_DATE', 'USR_STATUS', 'USR_COUNTRY', 'USR_CITY', 'USR_LOCATION', 'USR_ADDRESS', 'USR_PHONE', 'USR_FAX', 'USR_CELLULAR', 'USR_ZIP_CODE', 'DEP_UID', 'USR_POSITION', 'USR_RESUME', 'USR_BIRTHDAY', 'USR_ROLE', 'USR_REPORTS_TO', 'USR_REPLACED_BY', 'USR_UX', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('UsrUid' => 0, 'UsrUsername' => 1, 'UsrPassword' => 2, 'UsrFirstname' => 3, 'UsrLastname' => 4, 'UsrEmail' => 5, 'UsrDueDate' => 6, 'UsrCreateDate' => 7, 'UsrUpdateDate' => 8, 'UsrStatus' => 9, 'UsrCountry' => 10, 'UsrCity' => 11, 'UsrLocation' => 12, 'UsrAddress' => 13, 'UsrPhone' => 14, 'UsrFax' => 15, 'UsrCellular' => 16, 'UsrZipCode' => 17, 'DepUid' => 18, 'UsrPosition' => 19, 'UsrResume' => 20, 'UsrBirthday' => 21, 'UsrRole' => 22, 'UsrReportsTo' => 23, 'UsrReplacedBy' => 24, 'UsrUx' => 25, ),
        BasePeer::TYPE_COLNAME => array (UsersPeer::USR_UID => 0, UsersPeer::USR_USERNAME => 1, UsersPeer::USR_PASSWORD => 2, UsersPeer::USR_FIRSTNAME => 3, UsersPeer::USR_LASTNAME => 4, UsersPeer::USR_EMAIL => 5, UsersPeer::USR_DUE_DATE => 6, UsersPeer::USR_CREATE_DATE => 7, UsersPeer::USR_UPDATE_DATE => 8, UsersPeer::USR_STATUS => 9, UsersPeer::USR_COUNTRY => 10, UsersPeer::USR_CITY => 11, UsersPeer::USR_LOCATION => 12, UsersPeer::USR_ADDRESS => 13, UsersPeer::USR_PHONE => 14, UsersPeer::USR_FAX => 15, UsersPeer::USR_CELLULAR => 16, UsersPeer::USR_ZIP_CODE => 17, UsersPeer::DEP_UID => 18, UsersPeer::USR_POSITION => 19, UsersPeer::USR_RESUME => 20, UsersPeer::USR_BIRTHDAY => 21, UsersPeer::USR_ROLE => 22, UsersPeer::USR_REPORTS_TO => 23, UsersPeer::USR_REPLACED_BY => 24, UsersPeer::USR_UX => 25, ),
        BasePeer::TYPE_FIELDNAME => array ('USR_UID' => 0, 'USR_USERNAME' => 1, 'USR_PASSWORD' => 2, 'USR_FIRSTNAME' => 3, 'USR_LASTNAME' => 4, 'USR_EMAIL' => 5, 'USR_DUE_DATE' => 6, 'USR_CREATE_DATE' => 7, 'USR_UPDATE_DATE' => 8, 'USR_STATUS' => 9, 'USR_COUNTRY' => 10, 'USR_CITY' => 11, 'USR_LOCATION' => 12, 'USR_ADDRESS' => 13, 'USR_PHONE' => 14, 'USR_FAX' => 15, 'USR_CELLULAR' => 16, 'USR_ZIP_CODE' => 17, 'DEP_UID' => 18, 'USR_POSITION' => 19, 'USR_RESUME' => 20, 'USR_BIRTHDAY' => 21, 'USR_ROLE' => 22, 'USR_REPORTS_TO' => 23, 'USR_REPLACED_BY' => 24, 'USR_UX' => 25, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/UsersMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.UsersMapBuilder');
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
            $map = UsersPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. UsersPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(UsersPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(UsersPeer::USR_UID);

        $criteria->addSelectColumn(UsersPeer::USR_USERNAME);

        $criteria->addSelectColumn(UsersPeer::USR_PASSWORD);

        $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);

        $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);

        $criteria->addSelectColumn(UsersPeer::USR_EMAIL);

        $criteria->addSelectColumn(UsersPeer::USR_DUE_DATE);

        $criteria->addSelectColumn(UsersPeer::USR_CREATE_DATE);

        $criteria->addSelectColumn(UsersPeer::USR_UPDATE_DATE);

        $criteria->addSelectColumn(UsersPeer::USR_STATUS);

        $criteria->addSelectColumn(UsersPeer::USR_COUNTRY);

        $criteria->addSelectColumn(UsersPeer::USR_CITY);

        $criteria->addSelectColumn(UsersPeer::USR_LOCATION);

        $criteria->addSelectColumn(UsersPeer::USR_ADDRESS);

        $criteria->addSelectColumn(UsersPeer::USR_PHONE);

        $criteria->addSelectColumn(UsersPeer::USR_FAX);

        $criteria->addSelectColumn(UsersPeer::USR_CELLULAR);

        $criteria->addSelectColumn(UsersPeer::USR_ZIP_CODE);

        $criteria->addSelectColumn(UsersPeer::DEP_UID);

        $criteria->addSelectColumn(UsersPeer::USR_POSITION);

        $criteria->addSelectColumn(UsersPeer::USR_RESUME);

        $criteria->addSelectColumn(UsersPeer::USR_BIRTHDAY);

        $criteria->addSelectColumn(UsersPeer::USR_ROLE);

        $criteria->addSelectColumn(UsersPeer::USR_REPORTS_TO);

        $criteria->addSelectColumn(UsersPeer::USR_REPLACED_BY);

        $criteria->addSelectColumn(UsersPeer::USR_UX);

    }

    const COUNT = 'COUNT(USERS.USR_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT USERS.USR_UID)';

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
            $criteria->addSelectColumn(UsersPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(UsersPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = UsersPeer::doSelectRS($criteria, $con);
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
     * @return     Users
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = UsersPeer::doSelect($critcopy, $con);
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
        return UsersPeer::populateObjects(UsersPeer::doSelectRS($criteria, $con));
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
            UsersPeer::addSelectColumns($criteria);
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
        $cls = UsersPeer::getOMClass();
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
        return UsersPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Users or Criteria object.
     *
     * @param      mixed $values Criteria or Users object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Users object
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
     * Method perform an UPDATE on the database, given a Users or Criteria object.
     *
     * @param      mixed $values Criteria or Users object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(UsersPeer::USR_UID);
            $selectCriteria->add(UsersPeer::USR_UID, $criteria->remove(UsersPeer::USR_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the USERS table.
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
            $affectedRows += BasePeer::doDeleteAll(UsersPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Users or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Users object or primary key or array of primary keys
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
            $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Users) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(UsersPeer::USR_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Users object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Users $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Users $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(UsersPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(UsersPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(UsersPeer::USR_STATUS))
            $columns[UsersPeer::USR_STATUS] = $obj->getUsrStatus();

        }

        return BasePeer::doValidate(UsersPeer::DATABASE_NAME, UsersPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Users
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(UsersPeer::DATABASE_NAME);

        $criteria->add(UsersPeer::USR_UID, $pk);


        $v = UsersPeer::doSelect($criteria, $con);

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
            $criteria->add(UsersPeer::USR_UID, $pks, Criteria::IN);
            $objs = UsersPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseUsersPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/UsersMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.UsersMapBuilder');
}

