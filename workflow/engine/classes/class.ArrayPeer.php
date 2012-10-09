<?php
/**
 * class.ArrayPeer.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ApplicationPeer::getOMClass()
include_once 'classes/model/Application.php';

/**
 * Base static class for performing query and update operations on the 'APPLICATION' table.
 *
 * @package workflow.engine.classes
 */
abstract class ArrayBasePeer
{

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'dbarray';

    /**
     * The table name for this class
     */
    //const TABLE_NAME = 'APPLICATION';


    /**
     * A class that can be returned by this peer.
     */
    const CLASS_DEFAULT = 'classes.model.Application';

    /**
     * The total number of columns.
     */
    const NUM_COLUMNS = 15;

    /**
     * The number of lazy-loaded columns.
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The column name for the APP_UID field
     */
    const APP_UID = 'APPLICATION.APP_UID';

    /**
     * The column name for the APP_NUMBER field
     */
    const APP_NUMBER = 'APPLICATION.APP_NUMBER';

    /**
     * The column name for the APP_PARENT field
     */
    const APP_PARENT = 'APPLICATION.APP_PARENT';

    /**
     * The column name for the APP_STATUS field
     */
    const APP_STATUS = 'APPLICATION.APP_STATUS';

    /**
     * The column name for the PRO_UID field
     */
    const PRO_UID = 'APPLICATION.PRO_UID';

    /**
     * The column name for the APP_PROC_STATUS field
     */
    const APP_PROC_STATUS = 'APPLICATION.APP_PROC_STATUS';

    /**
     * The column name for the APP_PROC_CODE field
     */
    const APP_PROC_CODE = 'APPLICATION.APP_PROC_CODE';

    /**
     * The column name for the APP_PARALLEL field
     */
    const APP_PARALLEL = 'APPLICATION.APP_PARALLEL';

    /**
     * The column name for the APP_INIT_USER field
     */
    const APP_INIT_USER = 'APPLICATION.APP_INIT_USER';

    /**
     * The column name for the APP_CUR_USER field
     */
    const APP_CUR_USER = 'APPLICATION.APP_CUR_USER';

    /**
     * The column name for the APP_CREATE_DATE field
     */
    const APP_CREATE_DATE = 'APPLICATION.APP_CREATE_DATE';

    /**
     * The column name for the APP_INIT_DATE field
     */
    const APP_INIT_DATE = 'APPLICATION.APP_INIT_DATE';

    /**
     * The column name for the APP_FINISH_DATE field
     */
    const APP_FINISH_DATE = 'APPLICATION.APP_FINISH_DATE';

    /**
     * The column name for the APP_UPDATE_DATE field
     */
    const APP_UPDATE_DATE = 'APPLICATION.APP_UPDATE_DATE';

    /**
     * The column name for the APP_DATA field
     */
    const APP_DATA = 'APPLICATION.APP_DATA';

    /**
     * The PHP to DB Name Mapping
     */
    private static $phpNameMap = null;

    /**
     * Holds an array of fieldnames
     *
     * First dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (BasePeer::TYPE_PHPNAME => array ('AppUid','AppNumber','AppParent','AppStatus','ProUid','AppProcStatus','AppProcCode','AppParallel','AppInitUser','AppCurUser','AppCreateDate','AppInitDate','AppFinishDate','AppUpdateDate','AppData'
    ),BasePeer::TYPE_COLNAME => array (ApplicationPeer::APP_UID,ApplicationPeer::APP_NUMBER,ApplicationPeer::APP_PARENT,ApplicationPeer::APP_STATUS,ApplicationPeer::PRO_UID,ApplicationPeer::APP_PROC_STATUS,ApplicationPeer::APP_PROC_CODE,ApplicationPeer::APP_PARALLEL,ApplicationPeer::APP_INIT_USER,ApplicationPeer::APP_CUR_USER,ApplicationPeer::APP_CREATE_DATE,ApplicationPeer::APP_INIT_DATE,ApplicationPeer::APP_FINISH_DATE,ApplicationPeer::APP_UPDATE_DATE,ApplicationPeer::APP_DATA
    ),BasePeer::TYPE_FIELDNAME => array ('APP_UID','APP_NUMBER','APP_PARENT','APP_STATUS','PRO_UID','APP_PROC_STATUS','APP_PROC_CODE','APP_PARALLEL','APP_INIT_USER','APP_CUR_USER','APP_CREATE_DATE','APP_INIT_DATE','APP_FINISH_DATE','APP_UPDATE_DATE','APP_DATA'
    ),BasePeer::TYPE_NUM => array (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14
    )
    );

    /**
     * Holds an array of keys for quick access to the fieldnames array
     *
     * First dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (BasePeer::TYPE_PHPNAME => array ('AppUid' => 0,'AppNumber' => 1,'AppParent' => 2,'AppStatus' => 3,'ProUid' => 4,'AppProcStatus' => 5,'AppProcCode' => 6,'AppParallel' => 7,'AppInitUser' => 8,'AppCurUser' => 9,'AppCreateDate' => 10,'AppInitDate' => 11,'AppFinishDate' => 12,'AppUpdateDate' => 13,'AppData' => 14
    ),BasePeer::TYPE_COLNAME => array (ApplicationPeer::APP_UID => 0,ApplicationPeer::APP_NUMBER => 1,ApplicationPeer::APP_PARENT => 2,ApplicationPeer::APP_STATUS => 3,ApplicationPeer::PRO_UID => 4,ApplicationPeer::APP_PROC_STATUS => 5,ApplicationPeer::APP_PROC_CODE => 6,ApplicationPeer::APP_PARALLEL => 7,ApplicationPeer::APP_INIT_USER => 8,ApplicationPeer::APP_CUR_USER => 9,ApplicationPeer::APP_CREATE_DATE => 10,ApplicationPeer::APP_INIT_DATE => 11,ApplicationPeer::APP_FINISH_DATE => 12,ApplicationPeer::APP_UPDATE_DATE => 13,ApplicationPeer::APP_DATA => 14
    ),BasePeer::TYPE_FIELDNAME => array ('APP_UID' => 0,'APP_NUMBER' => 1,'APP_PARENT' => 2,'APP_STATUS' => 3,'PRO_UID' => 4,'APP_PROC_STATUS' => 5,'APP_PROC_CODE' => 6,'APP_PARALLEL' => 7,'APP_INIT_USER' => 8,'APP_CUR_USER' => 9,'APP_CREATE_DATE' => 10,'APP_INIT_DATE' => 11,'APP_FINISH_DATE' => 12,'APP_UPDATE_DATE' => 13,'APP_DATA' => 14
    ),BasePeer::TYPE_NUM => array (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14
    )
    );

    /**
     *
     * @return MapBuilder the map builder for this peer
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder ()
    {
        include_once 'classes/model/map/ApplicationMapBuilder.php';
        return BasePeer::getMapBuilder( 'classes.model.map.ApplicationMapBuilder' );
    }

    /**
     * Gets a map (hash) of PHP names to DB column names.
     *
     * @return array The PHP to DB name map for this peer
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
     */
    public static function getPhpNameMap ()
    {
        if (self::$phpNameMap === null) {
            $map = ApplicationPeer::getTableMap();
            $columns = $map->getColumns();
            $nameMap = array ();
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
     * @param string $name field name
     * @param string $fromType One of the class type constants TYPE_PHPNAME,
     * TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @param string $toType One of the class type constants
     * @return string translated name of the field.
     */
    static public function translateFieldName ($name, $fromType, $toType)
    {
        $toNames = self::getFieldNames( $toType );
        $key = isset( self::$fieldKeys[$fromType][$name] ) ? self::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException( "'$name' could not be found in the field names of type '$fromType'. These are: " . print_r( self::$fieldKeys[$fromType], true ) );
        }
        return $toNames[$key];
    }

    /**
     * Returns an array of of field names.
     *
     * @param string $type The type of fieldnames to return:
     * One of the class type constants TYPE_PHPNAME,
     * TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return array A list of field names
     */

    static public function getFieldNames ($type = BasePeer::TYPE_PHPNAME)
    {
        if (! array_key_exists( $type, self::$fieldNames )) {
            throw new PropelException( 'Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.' );
        }
        return self::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     * $c->addAlias("alias1", TablePeer::TABLE_NAME);
     * $c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     *
     * @param string $alias The alias for the current table.
     * @param string $column The column name for current table. (i.e. ApplicationPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias ($alias, $column)
    {
        return str_replace( ApplicationPeer::TABLE_NAME . '.', $alias . '.', $column );
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns (Criteria $criteria)
    {

    }

    const COUNT = 'COUNT(APPLICATION.APP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APPLICATION.APP_UID)';

    /**
     * Returns the number of rows matching criteria.
     *
     * @param Criteria $criteria
     * @param boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
     * @param Connection $con
     * @return int Number of matching rows.
     * @todo Review declarated constant d'not used COUNT, COUNT_DISTINCT
     */
    public static function doCount (Criteria $criteria, $distinct = false, $con = null)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // clear out anything that might confuse the ORDER BY clause
        $criteria->clearSelectColumns()->clearOrderByColumns();
        /*
    krumo ( ApplicationPeer::COUNT_DISTINCT );
    if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
      $criteria->addSelectColumn(ApplicationPeer::COUNT_DISTINCT);
    } else {
      $criteria->addSelectColumn(ApplicationPeer::COUNT);
    }
    */
        $criteria->addSelectColumn( 'COUNT(*)' );

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn( $column );
        }

        $rs = ArrayBasePeer::doSelectRS( $criteria, $con );
        if ($rs->next()) {
            $row = $rs->getRow();
            return $row[1];
        } else {
            // no rows returned; we infer that means 0 matches.
            return 0;
        }
    }

    /**
     * Method to select one object from the DB.
     *
     * @param Criteria $criteria object used to create the SELECT statement.
     * @param Connection $con
     * @return Application
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function doSelectOne (Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit( 1 );
        $objects = ApplicationPeer::doSelect( $critcopy, $con );
        if ($objects) {
            return $objects[0];
        }
        return null;
    }

    /**
     * Method to create select SQL.
     *
     * @param Criteria $criteria object used to create the SELECT statement.
     * @param String $tableName
     * @param Array &$params
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    private function createSelectSql ($criteria, $tableName, &$params)
    {
        $db = Propel::getDB( $criteria->getDbName() );

        // redundant definition $selectModifiers = array();
        $selectClause = array ();
        $fromClause = array ();
        $joinClause = array ();
        $joinTables = array ();
        $whereClause = array ();
        $orderByClause = array ();
        $groupByClause = array ();

        $orderBy = $criteria->getOrderByColumns();
        $groupBy = $criteria->getGroupByColumns();
        $ignoreCase = $criteria->isIgnoreCase();
        $select = $criteria->getSelectColumns();
        $aliases = $criteria->getAsColumns();

        $fromClause[] = $criteria->getDBArrayTable();

        // simple copy
        $selectModifiers = $criteria->getSelectModifiers();
        // get selected columns
        foreach ($select as $columnName) {
            $tableName = null;
            $selectClause[] = $columnName; // the full column name: e.g. MAX(books.price)


            $parenPos = strpos( $columnName, '(' );
            $dotPos = strpos( $columnName, '.' );

            // [HL] I think we really only want to worry about adding stuff to
            // the fromClause if this function has a TABLE.COLUMN in it at all.
            // e.g. COUNT(*) should not need this treatment -- or there needs to
            // be special treatment for '*'
            if ($dotPos !== false) {

                if ($parenPos === false) { // table.column
                    $tableName = substr( $columnName, 0, $dotPos );
                } else { // FUNC(table.column)
                    $tableName = substr( $columnName, $parenPos + 1, $dotPos - ($parenPos + 1) );
                    // functions may contain qualifiers so only take the last
                    // word as the table name.
                    // COUNT(DISTINCT books.price)
                    $lastSpace = strpos( $tableName, ' ' );
                    if ($lastSpace !== false) { // COUNT(DISTINCT books.price)
                        $tableName = substr( $tableName, $lastSpace + 1 );
                    }
                }
                $tableName2 = $criteria->getTableForAlias( $tableName );
                if ($tableName2 !== null) {
                    $fromClause[] = $tableName2 . ' ' . $tableName;
                } else {
                    $fromClause[] = $tableName;
                }

            } // if $dotPost !== null
        }

        // set the aliases
        foreach ($aliases as $alias => $col) {
            $selectClause[] = $col . " AS " . $alias;
        }

        // add the criteria to WHERE clause
        foreach ($criteria->keys() as $key) {

            $criterion = $criteria->getCriterion( $key );
            $someCriteria = $criterion->getAttachedCriterion();
            $someCriteriaLength = count( $someCriteria );
            $table = null;
            for ($i = 0; $i < $someCriteriaLength; $i ++) {
                $tableName = $someCriteria[$i]->getTable();
                $table = $criteria->getTableForAlias( $tableName );
                if ($table !== null) {
                    $fromClause[] = $table . ' ' . $tableName;
                } else {
                    $fromClause[] = $tableName;
                    $table = $tableName;
                }

                $ignoreCase = (($criteria->isIgnoreCase() || $someCriteria[$i]->isIgnoreCase()) && ($dbMap->getTable( $table )->getColumn( $someCriteria[$i]->getColumn() )->getType() == "string"));

                $someCriteria[$i]->setIgnoreCase( $ignoreCase );
            }
            $criterion->setDB( $db );

            $cri['table'] = $criterion->table;
            $cri['field'] = $criterion->column;
            $cri['comparison'] = $criterion->comparison == '=' ? '==' : $criterion->comparison;
            $cri['value'] = $criterion->getValue();
            $sb = "";
            $sb .= "\$row['" . $cri['field'] . "'] " . $cri['comparison'] . "'" . $cri['value'] . "'";
            $params[] = $cri;
            //$criterion->appendPsTo($sb, $params);


            $whereClause[] = $sb;

        }
        // Unique from clause elements
        $fromClause = array_unique( $fromClause );

        if (! empty( $orderBy )) {

            foreach ($orderBy as $orderByColumn) {
                // Add function expression as-is.
                if (strpos( $orderByColumn, '(' ) !== false) {
                    $orderByClause[] = $orderByColumn;
                    continue;
                }

                // Split orderByColumn (i.e. "table.column DESC")
                $dotPos = strpos( $orderByColumn, '.' );
                if ($dotPos !== false) {
                    $tableName = substr( $orderByColumn, 0, $dotPos );
                    $columnName = substr( $orderByColumn, $dotPos + 1 );
                } else {
                    $tableName = '';
                    $columnName = $orderByColumn;
                }

                $spacePos = strpos( $columnName, ' ' );
                if ($spacePos !== false) {
                    $direction = substr( $columnName, $spacePos );
                    $columnName = substr( $columnName, 0, $spacePos );
                } else {
                    $direction = '';
                }
                $orderByClause[] = array ('columnName' => $columnName,'direction' => $direction
                );
            }
        }

        // Build the SQL from the arrays we compiled
        $sql = "SELECT " . ($selectModifiers ? implode( " ", $selectModifiers ) . " " : "") . implode( ", ", $selectClause ) . " FROM " . $fromClause[0] . ($whereClause ? " WHERE " . implode( " AND ", $whereClause ) : "") . ($groupByClause ? " GROUP BY " . implode( ",", $groupByClause ) : "");

        $dataSql['selectClause'] = $selectClause;
        $dataSql['fromClause'] = $fromClause;
        $dataSql['whereClause'] = $whereClause;
        $dataSql['orderByClause'] = $orderByClause;
        $dataSql['sql'] = $sql;

        return $dataSql;
    }

    /**
     * Method to do selects.
     *
     * @param Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param Connection $con
     * @return array Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function doSelect (Criteria $criteria, $tableName, $con = null)
    {
        $dbMap = Propel::getDatabaseMap( $criteria->getDbName() );

        $stmt = null;

        try {
            $params = array ();
            $sql = self::createSelectSql( $criteria, $tableName, $params );
            $sql['params'] = $params;
            $stmt = $con->prepareStatement( $sql );
            //$stmt->setLimit($criteria->getLimit());
            $sql['limit'] = $criteria->getLimit();
            //$stmt->setOffset($criteria->getOffset());
            $sql['offset'] = $criteria->getOffset();
            //$rs = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
            $rs = $con->executeQuery( $sql, ResultSet::FETCHMODE_NUM );
        } catch (Exception $e) {
            if ($stmt)
                $stmt->close();
            throw new PropelException( $e );
        }

        return $rs;
    }

    /**
     * Prepares the Criteria object and uses the parent doSelect()
     * method to get a ResultSet.
     *
     * Use this method directly if you want to just get the resultset
     * (instead of an array of objects).
     *
     * @param Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param Connection $con the connection to use
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     * @return ResultSet The resultset object with numerically-indexed fields.
     * @see BasePeer::doSelect()
     */
    public static function doSelectRS (Criteria $criteria, $con = null)
    {
        global $_DBArray;
        if (! isset( $_DBArray )) {
            $_DBArray = $_SESSION['_DBArray'];
        }
        $tableName = $criteria->getDBArrayTable();
        if (! isset( $_DBArray[$tableName] )) {
            throw new Exception( "Error: the table '$tableName' doesn't exist in DBArray " );
        }

        $arrayTable = $_DBArray[$tableName];

        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }

        if (! $criteria->getSelectColumns()) {
            foreach (array_keys( $_DBArray[$tableName][0] ) as $key => $val)
                $criteria->addSelectColumn( $tableName . '.' . $val );
        }

        // Set the correct dbName
        $criteria->setDbName( self::DATABASE_NAME );

        // BasePeer returns a Creole ResultSet, set to return
        // rows indexed numerically.
        return ArrayBasePeer::doSelect( $criteria, $tableName, $con );
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function populateObjects (ResultSet $rs)
    {
        $results = array ();

        // set the class once to avoid overhead in the loop
        $cls = ApplicationPeer::getOMClass();
        $cls = Propel::import( $cls );
        // populate the object(s)
        while ($rs->next()) {
            $obj = new $cls();
            $obj->hydrate( $rs );
            $results[] = $obj;
        }
        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     *
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function getTableMap ()
    {
        return Propel::getDatabaseMap( self::DATABASE_NAME )->getTable( self::TABLE_NAME );
    }

    /**
     * The class that the Peer will make instances of.
     *
     * This uses a dot-path notation which is tranalted into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @return string path.to.ClassName
     */
    public static function getOMClass ()
    {
        return ApplicationPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Application or Criteria object.
     *
     * @param mixed $values Criteria or Application object containing data that is used to create the INSERT statement.
     * @param Connection $con the connection to use
     * @return mixed The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function doInsert ($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Application object
        }

        // Set the correct dbName
        $criteria->setDbName( self::DATABASE_NAME );

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->begin();
            $pk = BasePeer::doInsert( $criteria, $con );
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }

        return $pk;
    }

    /**
     * Method perform an UPDATE on the database, given a Application or Criteria object.
     *
     * @param mixed $values Criteria or Application object containing data that is used to create the UPDATE statement.
     * @param Connection $con The connection to use (specify Connection object to exert more control over transactions).
     * @return int The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function doUpdate ($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }

        $selectCriteria = new Criteria( self::DATABASE_NAME );

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity


            $comparison = $criteria->getComparison( ApplicationPeer::APP_UID );
            $selectCriteria->add( ApplicationPeer::APP_UID, $criteria->remove( ApplicationPeer::APP_UID ), $comparison );

        } else { // $values is Application object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName( self::DATABASE_NAME );

        return BasePeer::doUpdate( $selectCriteria, $criteria, $con );
    }

    /**
     * Method to DELETE all rows from the APPLICATION table.
     *
     * @param Connection $con The connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll ($con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();
            $affectedRows += BasePeer::doDeleteAll( ApplicationPeer::TABLE_NAME, $con );
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Application or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or Application object or primary key or array of primary keys
     * which is used to create the DELETE statement
     * @param Connection $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver). This includes CASCADE-related rows
     * if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function doDelete ($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( ApplicationPeer::DATABASE_NAME );
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            if ($values instanceof Application) {
                $criteria = $values->buildPkeyCriteria();
            } else {
                // it must be the primary key
                $criteria = new Criteria( self::DATABASE_NAME );
                $criteria->add( ApplicationPeer::APP_UID, (array) $values, Criteria::IN );
            }
        }
        // Set the correct dbName
        $criteria->setDbName( self::DATABASE_NAME );

        $affectedRows = 0; // initialize var to track total num of affected rows


        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();

            $affectedRows += BasePeer::doDelete( $criteria, $con );
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Application object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Application $obj The object to validate.
     * @param mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate (Application $obj, $cols = null)
    {
        $columns = array ();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap( ApplicationPeer::DATABASE_NAME );
            $tableMap = $dbMap->getTable( ApplicationPeer::TABLE_NAME );

            if (! is_array( $cols )) {
                $cols = array ($cols
                );
            }

            foreach ($cols as $colName) {
                if ($tableMap->containsColumn( $colName )) {
                    $get = 'get' . $tableMap->getColumn( $colName )->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

            if ($obj->isNew() || $obj->isColumnModified( ApplicationPeer::APP_STATUS ))
                $columns[ApplicationPeer::APP_STATUS] = $obj->getAppStatus();

        }

        return BasePeer::doValidate( ApplicationPeer::DATABASE_NAME, ApplicationPeer::TABLE_NAME, $columns );
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param mixed $pk the primary key.
     * @param Connection $con the connection to use
     * @return Application
     */
    public static function retrieveByPK ($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }

        $criteria = new Criteria( ApplicationPeer::DATABASE_NAME );

        $criteria->add( ApplicationPeer::APP_UID, $pk );

        $v = ApplicationPeer::doSelect( $criteria, $con );

        return ! empty( $v ) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param array $pks List of primary keys
     * @param Connection $con the connection to use
     * @throws PropelException Any exceptions caught during processing will be
     * rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs ($pks, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection( self::DATABASE_NAME );
        }

        $objs = null;
        if (empty( $pks )) {
            $objs = array ();
        } else {
            $criteria = new Criteria();
            $criteria->add( ApplicationPeer::APP_UID, $pks, Criteria::IN );
            $objs = ApplicationPeer::doSelect( $criteria, $con );
        }
        return $objs;
    }

} // BaseApplicationPeer


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseApplicationPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log( 'Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR );
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/ApplicationMapBuilder.php';
    Propel::registerMapBuilder( 'classes.model.map.ApplicationMapBuilder' );
}
