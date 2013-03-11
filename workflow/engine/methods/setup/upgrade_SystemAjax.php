<?php
/**
 * upgrade_SystemAjax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.
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
 */
global $DB_ADAPTER;
global $DB_HOST;
global $DB_USER;
global $DB_PASS;
global $DB_NAME;
set_time_limit( 0 );

$id = '';
if (isset( $_POST['id'] ))
    $id = $_POST['id'];

G::LoadClass( 'languages' );
G::LoadSystem( 'database_mysql' );

$aUpgradeData = unserialize( file_get_contents( PATH_DATA . 'log' . PATH_SEP . "upgrade.data.bin" ) );
$aWorkspaces = $aUpgradeData['workspaces'];

if (is_array( $aWorkspaces ) && count( $aWorkspaces ) > 0) {
    $workspace = array_shift( $aUpgradeData['workspaces'] );

    eval( getDatabaseCredentials( PATH_DB . $workspace . PATH_SEP . 'db.php' ) );
    $oDataBase = new database( $DB_ADAPTER, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME );
    $oDataBase->iFetchType = MYSQL_NUM;

    //processing .po file
    if ($aUpgradeData['sPoFile'] != '') {
        $oLanguages = new languages();
        $oLanguages->importLanguage( $aUpgradeData['sPoFile'], $aUpgradeData['bForceXmlPoFile'] );
        $aUpgradeData['bForceXmlPoFile'] = false;
    }

    if ($aUpgradeData['sSchemaFile'] != '')
        processMasterSchemaFile( $aUpgradeData['sSchemaFile'] );

        //draw a gauge control indicating the progress in workspaces
    $gauge = intval( (($aUpgradeData['wsQuantity'] - count( $aWorkspaces ) + 1) / $aUpgradeData['wsQuantity']) * 301 );
    print "<table cellpadding=0><tr><td><img src='/images/ajax-loader.gif' border=0/></td><td>";
    print "<div style='border-style:solid;border-width:1px; border-color: #A1C868; width:300px; height:19px;' >";
    print "<div style='color:#FFF; height:16px; text-align:center; padding-top:3px; background-image: url(/skins/green/images/bm.jpg); background-repeat: repeat-x; width: {$gauge}px' ></div> </div>";
    print "</td><td width='40%'><font color=black> Upgrading the workspace </font><b><font color=green>$workspace</font></b></td><td width=250 align=right>| $id Remaining</td></tr></table>";
    file_put_contents( PATH_DATA . 'log' . PATH_SEP . "upgrade.data.bin", serialize( $aUpgradeData ) );
} else {
    print "<table cellpadding=0><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
    print "<div style='border-style:solid;border-width:1px; border-color: #A1C868; width:300px; height:19px;' >";
    print "<div style='color:#FFF; height:16px; text-align:center; padding-top:3px; background-image: url(/skins/green/images/bm.jpg); background-repeat: repeat-x; width: 301px' ></div> </div>";
    print "</td><td> Finished! All workspaces were upgraded successfully.</td></tr></table>";
}

die();

function getDatabaseCredentials ($dbFile)
{
    $sContent = file_get_contents( $dbFile );
    $sContent = str_replace( '<?php', '', $sContent );
    $sContent = str_replace( '<?', '', $sContent );
    $sContent = str_replace( '?>', '', $sContent );
    $sContent = str_replace( 'define', '', $sContent );
    $sContent = str_replace( "('", '$', $sContent );
    $sContent = str_replace( "',", '=', $sContent );
    $sContent = str_replace( ");", ';', $sContent );
    return $sContent;
}

function processMasterSchemaFile ($sSchemaFile)
{
    global $DB_ADAPTER;
    global $DB_HOST;
    global $DB_USER;
    global $DB_PASS;
    global $DB_NAME;
    global $aUpgradeData;

    //convert newSchema to array
    if (isset( $aUpgradeData['aNewSchema'] )) {
        $aNewSchema = $aUpgradeData['aNewSchema'];
    } else {
        $aNewSchema = schemaToArray( $sSchemaFile );
        $aUpgradeData['aNewSchema'] = $aNewSchema;
    }
    $aOldSchema = processSchemaFile();
    if (is_null( $aOldSchema )) {
        return;
    }
    $aChanges = obtainChanges( $aOldSchema, $aNewSchema );

    $oDataBase = new database( $DB_ADAPTER, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME );
    if (! $oDataBase->isConnected()) {
        return;
    }
    $oDataBase->iFetchType = MYSQL_NUM;

    $oDataBase->logQuery( count( $aChanges ) );

    foreach ($aChanges['tablesToAdd'] as $sTable => $aColumns) {
        $oDataBase->executeQuery( $oDataBase->generateCreateTableSQL( $sTable, $aColumns ) );
        if (isset( $aChanges['tablesToAdd'][$sTable]['INDEXES'] )) {
            foreach ($aChanges['tablesToAdd'][$sTable]['INDEXES'] as $indexName => $aIndex) {
                $oDataBase->executeQuery( $oDataBase->generateAddKeysSQL( $sTable, $indexName, $aIndex ) );
            }
        }
    }

    foreach ($aChanges['tablesToAlter'] as $sTable => $aActions) {
        foreach ($aActions as $sAction => $aAction) {
            foreach ($aAction as $sColumn => $vData) {
                switch ($sAction) {
                    case 'DROP':
                        $oDataBase->executeQuery( $oDataBase->generateDropColumnSQL( $sTable, $vData ) );
                        break;
                    case 'ADD':
                        $oDataBase->executeQuery( $oDataBase->generateAddColumnSQL( $sTable, $sColumn, $vData ) );
                        break;
                    case 'CHANGE':
                        $oDataBase->executeQuery( $oDataBase->generateChangeColumnSQL( $sTable, $sColumn, $vData ) );
                        break;
                }
            }
        }
    }

    foreach ($aChanges['tablesWithNewIndex'] as $sTable => $aIndexes) {
        foreach ($aIndexes as $sIndexName => $aIndexFields) {
            $oDataBase->executeQuery( $oDataBase->generateAddKeysSQL( $sTable, $sIndexName, $aIndexFields ) );
        }
    }

    foreach ($aChanges['tablesToAlterIndex'] as $sTable => $aIndexes) {
        foreach ($aIndexes as $sIndexName => $aIndexFields) {
            $oDataBase->executeQuery( $oDataBase->generateDropKeySQL( $sTable, $sIndexName ) );
            $oDataBase->executeQuery( $oDataBase->generateAddKeysSQL( $sTable, $sIndexName, $aIndexFields ) );
        }
    }
    $oDataBase->close();
}

function processSchemaFile ()
{
    global $DB_ADAPTER;
    global $DB_HOST;
    global $DB_USER;
    global $DB_PASS;
    global $DB_NAME;

    try {
        G::LoadSystem( 'database_' . strtolower( $DB_ADAPTER ) );

        $aOldSchema = array ();
        $oDataBase = new database( $DB_ADAPTER, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME );

        if (! $oDataBase->isConnected()) {
            $oDataBase->logQuery( G::LoadTranslation('ID_DOES_NOT_EXIST_AVAILABLE_CONNECTION') );
            return null;
        }

        $oDataBase->iFetchType = MYSQL_NUM;
        $oDataset1 = $oDataBase->executeQuery( $oDataBase->generateShowTablesSQL() );

    } catch (Exception $e) {
        $oDataBase->logQuery( $e->getmessage() );
        return null;
    }

    //going thru all tables in current WF_ database
    while ($aRow1 = $oDataBase->getRegistry( $oDataset1 )) {
        $aPrimaryKeys = array ();
        $sTable = strtoupper( $aRow1[0] );

        //get description of each table, ( column and primary keys )
        //$oDataset2 = $oDataBase->executeQuery( $oDataBase->generateDescTableSQL($aRow1[0]) );
        $oDataset2 = $oDataBase->executeQuery( $oDataBase->generateDescTableSQL( $sTable ) );
        $aOldSchema[$sTable] = array ();
        $oDataBase->iFetchType = MYSQL_ASSOC;
        while ($aRow2 = $oDataBase->getRegistry( $oDataset2 )) {
            $aOldSchema[$sTable][$aRow2['Field']]['Field'] = $aRow2['Field'];
            $aOldSchema[$sTable][$aRow2['Field']]['Type'] = $aRow2['Type'];
            $aOldSchema[$sTable][$aRow2['Field']]['Null'] = $aRow2['Null'];
            $aOldSchema[$sTable][$aRow2['Field']]['Default'] = $aRow2['Default'];
        }

        //get indexes of each table  SHOW INDEX FROM `ADDITIONAL_TABLES`;   -- WHERE Key_name <> 'PRIMARY'
        $oDataset2 = $oDataBase->executeQuery( $oDataBase->generateTableIndexSQL( $aRow1[0] ) );
        $oDataBase->iFetchType = MYSQL_ASSOC;
        while ($aRow2 = $oDataBase->getRegistry( $oDataset2 )) {
            if (! isset( $aOldSchema[$sTable]['INDEXES'] )) {
                $aOldSchema[$sTable]['INDEXES'] = array ();
            }
            if (! isset( $aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']] )) {
                $aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']] = array ();
            }
            $aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']][] = $aRow2['Column_name'];
        }

        $oDataBase->iFetchType = MYSQL_NUM; //this line is neccesary because the next fetch needs to be with MYSQL_NUM
    }
    //finally return the array with old schema obtained from the Database
    if (count( $aOldSchema ) == 0)
        $aOldSchema = null;
    return $aOldSchema;
}

//process the schema file in the patch file, and obtain an array
function schemaToArray ($sSchemaFile)
{
    try {
        $aSchema = array ();
        $oXml = new DomDocument();
        $oXml->load( $sSchemaFile );
        $aTables = $oXml->getElementsByTagName( 'table' );
        foreach ($aTables as $oTable) {
            $aPrimaryKeys = array ();
            $sTableName = $oTable->getAttribute( 'name' );
            $aSchema[$sTableName] = array ();
            $aColumns = $oTable->getElementsByTagName( 'column' );
            foreach ($aColumns as $oColumn) {
                $sColumName = $oColumn->getAttribute( 'name' );
                $aSchema[$sTableName][$sColumName] = array ();
                $aVendors = $oColumn->getElementsByTagName( 'vendor' );
                foreach ($aVendors as $oVendor) {
                    if ($oVendor->getAttribute( 'type' ) == DB_ADAPTER) {
                        break;
                    }
                }
                $aParameters = $oColumn->getElementsByTagName( 'parameter' );
                foreach ($aParameters as $oParameter) {
                    $parameterName = ucwords( $oParameter->getAttribute( 'name' ) );
                    if ($parameterName == 'Key' && strtoupper( $oParameter->getAttribute( 'value' ) ) == 'PRI') {
                        $aPrimaryKeys[] = $oColumn->getAttribute( 'name' );
                    }

                    if (in_array( $parameterName, array ('Field','Type','Null','Default'
                    ) )) {
                        $aSchema[$sTableName][$sColumName][$parameterName] = $oParameter->getAttribute( 'value' );
                    }
                }
            }

            if (is_array( $aPrimaryKeys ) && count( $aPrimaryKeys ) > 0) {
                $aSchema[$sTableName]['INDEXES']['PRIMARY'] = $aPrimaryKeys;
            }
            $aIndexes = $oTable->getElementsByTagName( 'index' );
            foreach ($aIndexes as $oIndex) {
                $aIndex = array ();
                $aIndexesColumns = $oIndex->getElementsByTagName( 'index-column' );
                foreach ($aIndexesColumns as $oIndexColumn) {
                    $aIndex[] = $oIndexColumn->getAttribute( 'name' );
                }
                $aSchema[$sTableName]['INDEXES'][$oIndex->getAttribute( 'name' )] = $aIndex;
            }
        }
        return $aSchema;
    } catch (Exception $oError) {
        throw $oError;
    }
}

function obtainChanges ($aOldSchema, $aNewSchema)
{
    //$aChanges = array('tablesToDelete' => array(), 'tablesToAdd' => array(), 'tablesToAlter' => array());
    //Tables to delete, but this is disabled
    //foreach ($aOldSchema as $sTableName => $aColumns) {
    //  if ( !isset($aNewSchema[$sTableName])) {
    //    if (!in_array($sTableName, array('KT_APPLICATION', 'KT_DOCUMENT', 'KT_PROCESS'))) {
    //      $aChanges['tablesToDelete'][] = $sTableName;
    //    }
    //  }
    //}


    $aChanges = array ('tablesToAdd' => array (),'tablesToAlter' => array (),'tablesWithNewIndex' => array (),'tablesToAlterIndex' => array ()
    );

    //new tables  to create and alter
    foreach ($aNewSchema as $sTableName => $aColumns) {
        if (! isset( $aOldSchema[$sTableName] )) {
            $aChanges['tablesToAdd'][$sTableName] = $aColumns;
        } else {
            //drop old columns
            foreach ($aOldSchema[$sTableName] as $sColumName => $aParameters) {
                if (! isset( $aNewSchema[$sTableName][$sColumName] )) {
                    if (! isset( $aChanges['tablesToAlter'][$sTableName] )) {
                        $aChanges['tablesToAlter'][$sTableName] = array ('DROP' => array (),'ADD' => array (),'CHANGE' => array ()
                        );
                    }
                    $aChanges['tablesToAlter'][$sTableName]['DROP'][$sColumName] = $sColumName;
                }
            }

            //create new columns
            //foreach ($aNewSchema[$sTableName] as $sColumName => $aParameters) {
            foreach ($aColumns as $sColumName => $aParameters) {
                if ($sColumName != 'INDEXES') {
                    if (! isset( $aOldSchema[$sTableName][$sColumName] )) { //this column doesnt exist in oldschema
                        if (! isset( $aChanges['tablesToAlter'][$sTableName] )) {
                            $aChanges['tablesToAlter'][$sTableName] = array ('DROP' => array (),'ADD' => array (),'CHANGE' => array ()
                            );
                        }
                        $aChanges['tablesToAlter'][$sTableName]['ADD'][$sColumName] = $aParameters;
                    } else { //the column exists
                        $newField = $aNewSchema[$sTableName][$sColumName];
                        $oldField = $aOldSchema[$sTableName][$sColumName];
                        //both are null, no change is required
                        if (! isset( $newField['Default'] ) && ! isset( $oldField['Default'] ))
                            $changeDefaultAttr = false;
                            //one of them is null, change IS required
                        if (! isset( $newField['Default'] ) && isset( $oldField['Default'] ) && $oldField['Default'] != '')
                            $changeDefaultAttr = true;
                        if (isset( $newField['Default'] ) && ! isset( $oldField['Default'] ))
                            $changeDefaultAttr = true;
                            //both are defined and they are different.
                        if (isset( $newField['Default'] ) && isset( $oldField['Default'] )) {
                            if ($newField['Default'] != $oldField['Default'])
                                $changeDefaultAttr = true;
                            else
                                $changeDefaultAttr = false;
                        }
                        //special cases
                        // BLOB and TEXT columns cannot have DEFAULT values.  http://dev.mysql.com/doc/refman/5.0/en/blob.html
                        if (in_array( strtolower( $newField['Type'] ), array ('text','mediumtext'
                        ) ))
                            $changeDefaultAttr = false;

                            //#1067 - Invalid default value for datetime field
                        if (in_array( $newField['Type'], array ('datetime'
                        ) ) && isset( $newField['Default'] ) && $newField['Default'] == '')
                            $changeDefaultAttr = false;

                            //#1067 - Invalid default value for int field
                        if (substr( $newField['Type'], 0, 3 ) && isset( $newField['Default'] ) && $newField['Default'] == '')
                            $changeDefaultAttr = false;

                            //if any difference exists, then insert the difference in aChanges
                        if ($newField['Field'] != $oldField['Field'] || $newField['Type'] != $oldField['Type'] || $newField['Null'] != $oldField['Null'] || $changeDefaultAttr) {
                            if (! isset( $aChanges['tablesToAlter'][$sTableName] )) {
                                $aChanges['tablesToAlter'][$sTableName] = array ('DROP' => array (),'ADD' => array (),'CHANGE' => array ()
                                );
                            }
                            $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Field'] = $newField['Field'];
                            $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Type'] = $newField['Type'];
                            $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Null'] = $newField['Null'];
                            if (isset( $newField['Default'] ))
                                $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Default'] = $newField['Default'];
                            else
                                $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Default'] = null;

                        }
                    }
                } //only columns, no the indexes column
            } //foreach $aColumns


            //now check the indexes of table
            if (isset( $aNewSchema[$sTableName]['INDEXES'] )) {
                foreach ($aNewSchema[$sTableName]['INDEXES'] as $indexName => $indexFields) {
                    if (! isset( $aOldSchema[$sTableName]['INDEXES'][$indexName] )) {
                        if (! isset( $aChanges['tablesWithNewIndex'][$sTableName] )) {
                            $aChanges['tablesWithNewIndex'][$sTableName] = array ();
                        }
                        $aChanges['tablesWithNewIndex'][$sTableName][$indexName] = $indexFields;
                    } else {
                        if ($aOldSchema[$sTableName]['INDEXES'][$indexName] != $indexFields) {
                            if (! isset( $aChanges['tablesToAlterIndex'][$sTableName] )) {
                                $aChanges['tablesToAlterIndex'][$sTableName] = array ();
                            }
                            $aChanges['tablesToAlterIndex'][$sTableName][$indexName] = $indexFields;
                        }
                    }
                }
            }
        } //for-else table exists
    } //for new schema
    return $aChanges;
}

