<?php

/**
 * upgrade_System.php
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

/**
 * class system for workflow mantanance routines
 *
 * author Erik A.O.<erik@colosa.com>
 * date May 12th, 2010
 *
 * @package workflow.engine.classes
 *
 */

class System
{
    public $sFilename;
    public $sFilesList;
    public $sUpgradeFileList;
    public $aErrors;
    public $aWorkspaces;
    public $sRevision;
    public $sPath;
    public $newSystemClass;

    /**
     * List currently installed plugins
     *
     * param
     *
     * @return array with the names of the plugins
     */
    public static function getPlugins ()
    {
        $plugins = array ();

        foreach (glob( PATH_PLUGINS . "*" ) as $filename) {
            $info = pathinfo( $filename );
            if (array_key_exists( "extension", $info ) && (strcmp( $info["extension"], "php" ) == 0)) {
                $plugins[] = basename( $filename, ".php" );
            }
        }

        sort( $plugins, SORT_STRING );
        return $plugins;
    }

    /**
     * Lists existing workspaces, returning an array of workspaceTools object
     * for each.
     * This is a class method, it does not require an instance.
     *
     * @author Alexandre Rosenfeld <alexandre@colosa.com>
     * @access public
     * @return array of workspace tools objects
     */
    public static function listWorkspaces ()
    {
        $oDirectory = dir( PATH_DB );
        $aWorkspaces = array ();
        foreach (glob( PATH_DB . "*" ) as $filename) {
            if (is_dir( $filename ) && file_exists( $filename . "/db.php" )) {
                $aWorkspaces[] = new workspaceTools( basename( $filename ) );
            }
        }
        return $aWorkspaces;
    }

    /**
     * Get the ProcessMaker version.
     * If version-pmos.php is not found, try to
     * retrieve the version from git.
     *
     * @author Alexandre Rosenfeld <alexandre@colosa.com>
     * @return string system
     */
    public static function getVersion ()
    {
        if (! defined( 'PM_VERSION' )) {
            if (file_exists( PATH_METHODS . 'login/version-pmos.php' )) {
                include (PATH_METHODS . 'login/version-pmos.php');
            } else {
                $version = self::getVersionFromGit();
                if ($version === false) {
                    $version = 'Development Version';
                }
                define( 'PM_VERSION', $version );
            }
        }
        return PM_VERSION;
    }

    /**
     * Get the branch and tag information from a git repository.
     *
     * @author Alexandre Rosenfeld <alexandre@colosa.com>
     * @return string branch and tag information
     */
    public static function getVersionFromGit ($dir = null)
    {
        if ($dir == null) {
            $dir = PATH_TRUNK;
        }
        if (! file_exists( "$dir/.git" )) {
            return false;
        }
        if (exec( "cd $dir && git branch --no-color 2> /dev/null | sed -e '/^[^*]/d' -e 's/^* \(.*\)$/(Branch \\1)/'", $target )) {
            exec( "cd $dir && git describe", $target );
            return implode( ' ', $target );
        }
        return false;
    }

    /**
     * Get system information
     *
     * param
     *
     * @return array with system information
     */
    public static function getSysInfo ()
    {
        $ipe = explode( " ", $_SERVER['SSH_CONNECTION'] );

        if (getenv( 'HTTP_CLIENT_IP' )) {
            $ip = getenv( 'HTTP_CLIENT_IP' );
        } elseif (getenv( 'HTTP_X_FORWARDED_FOR' )) {
            $ip = getenv( 'HTTP_X_FORWARDED_FOR' );
        } else {
            $ip = getenv( 'REMOTE_ADDR' );
        }

        /* For distros with the lsb_release, this returns a one-line description of
        * the distro name, such as "CentOS release 5.3 (Final)" or "Ubuntu 10.10"
        */
        $distro = exec( "lsb_release -d -s 2> /dev/null" );

        /* For distros without lsb_release, we look for *release (such as
        * redhat-release, gentoo-release, SuSE-release, etc) or *version (such as
        * debian_version, slackware-version, etc)
        */
        if (empty( $distro )) {
            foreach (glob( "/etc/*release" ) as $filename) {
                $distro = trim( file_get_contents( $filename ) );
                if (! empty( $distro )) {
                    break;
                }
            }
            if (empty( $distro )) {
                foreach (glob( "/etc/*version" ) as $filename) {
                    $distro = trim( file_get_contents( $filename ) );
                    if (! empty( $distro )) {
                        break;
                    }
                }
            }
        }

        /* CentOS returns a string with quotes, remove them and append
        * the OS name (such as LINUX, WINNT, DARWIN, etc)
        */
        $distro = trim( $distro, "\"" ) . " (" . PHP_OS . ")";

        $Fields = array ();
        $Fields['SYSTEM'] = $distro;
        $Fields['PHP'] = phpversion();
        $Fields['PM_VERSION'] = self::getVersion();
        $Fields['SERVER_ADDR'] = $ipe[2]; //lookup($ipe[2]);
        $Fields['IP'] = $ipe[0]; //lookup($ipe[0]);


        $Fields['PLUGINS_LIST'] = System::getPlugins();

        return $Fields;
    }

    public static function listPoFiles ()
    {
        $folders = glob( PATH_CORE . '/content/translations/*' );

        $items = glob( PATH_CORE . '/content/translations/*.po' );
        foreach ($folders as $folder) {
            if (is_dir( $folder )) {
                $add = glob( $folder . "/*.po" );
                $items = array_merge( $items, $add );
            }
        }

        return $items;
    }

    public static function verifyChecksum ()
    {
        if (! file_exists( PATH_TRUNK . "checksum.txt" )) {
            return false;
        }
        $lines = explode( "\n", file_get_contents( PATH_TRUNK . "checksum.txt" ) );
        $result = array ("diff" => array (),"missing" => array ()
        );
        foreach ($lines as $line) {
            if (empty( $line )) {
                continue;
            }
            list ($checksum, $empty, $filename) = explode( " ", $line );
            //Skip xmlform because these files always change.
            if (strpos( $filename, "/xmlform/" ) !== false) {
                continue;
            }
            if (file_exists( realpath( $filename ) )) {
                if (strcmp( $checksum, md5_file( realpath( $filename ) ) ) != 0) {
                    $result['diff'][] = $filename;
                }
            } else {
                $result['missing'][] = $filename;
            }
        }
        return $result;
    }

    /**
     * This function checks files to do updated to pm
     *
     *
     * @name verifyFileForUpgrade
     *
     * param
     * @return boolean
     */
    public function verifyFileForUpgrade ()
    {
        $upgradeFilename = isset( $_FILES['form']['name']['UPGRADE_FILENAME'] ) ? $_FILES['form']['name']['UPGRADE_FILENAME'] : '';
        $tempFilename = isset( $_FILES['form']['tmp_name']['UPGRADE_FILENAME'] ) ? $_FILES['form']['tmp_name']['UPGRADE_FILENAME'] : '';
        $this->sRevision = str_replace( '.tar.gz', '', str_replace( 'pmos-patch-', '', $upgradeFilename ) );
        $sTemFilename = $tempFilename;
        $this->sFilename = PATH_DATA . 'upgrade' . PATH_SEP . $upgradeFilename;
        $this->sPath = dirname( $this->sFilename ) . PATH_SEP;
        G::mk_dir( PATH_DATA . 'upgrade' );
        if (! move_uploaded_file( $sTemFilename, $this->sFilename )) {
            return false;
        }
        return true;
    }

    /**
     * This function gets files to do updated to pm
     *
     *
     * @name getUpgradedFilesList
     *
     * param
     * @return void
     */
    public function getUpgradedFilesList ()
    {
        G::LoadClass( 'archive' );
        $this->sFilesList = new gzip_file( $this->sFilename );
        $this->sFilesList->set_options( array ('basedir' => dirname( $this->sFilename ),'overwrite' => 1
        ) );
        $this->sFilesList->extract_files();
        if (count( $this->sFilesList->error ) > 0) {
            $msg = '';
            foreach ($this->sFilesList->error as $key => $val) {
                $msg .= $val . "\n";
            }
            throw new Exception( $msg );
        }
        if (count( $this->sFilesList->files ) == 0) {
            throw new Exception( 'The uploaded file is an invalid patch file.' );
        }
    }

    /**
     * This function checks to do updated for boot
     *
     *
     * @name verifyForBootstrapUpgrade
     *
     * param
     * @return boolean
     */
    public function verifyForBootstrapUpgrade ()
    {
        foreach ($this->sFilesList->files as $sFile) {
            if (basename( $sFile ) == 'schema.xml') {
                $this->newSystemClass = $sFile;
                return true;
            }
        }
        return false;
    }

    /**
     * This function updates to the files
     *
     *
     * @name upgrade
     *
     * param
     * @return array
     */
    public function upgrade ()
    {
        //get special files
        $sListFile = '';
        $sCheckListFile = '';
        $sPatchVersionFile = '';
        $sPoFile = '';
        $sSchemaFile = '';
        $sSchemaRBACFile = '';
        foreach ($this->sFilesList->files as $sFile) {
            if (basename( $sFile ) == 'schema.xml') {
                if (strpos( $sFile, '/rbac/engine/' ) === false) {
                    $sOldSchema = '';
                    $sSchemaFile = $sFile;
                } else {
                    $sOldSchemaRBAC = '';
                    $sSchemaRBACFile = $sFile;
                }
            }

            //files.lst
            if (basename( $sFile ) == 'files.lst') {
                $this->sUpgradeFileList = $sFile;
            }

            //files.lst
            if (basename( $sFile ) == 'patch.version.txt') {
                $sPatchVersionFile = $sFile;
            }

            //files.rev.txt
            if (substr( basename( $sFile ), 0, 6 ) == 'files.' && substr( basename( $sFile ), - 4 ) == '.txt') {
                $sCheckListFile = $sFile;
            }

            //po files
            $sExtension = substr( $sFile, strrpos( $sFile, '.' ) + 1, strlen( $sFile ) );
            if ($sExtension == 'po') {
                $sPoFile = $sFile;
            }
        }

        $pmVersion = explode( '-', self::getVersion() );
        array_shift( $pmVersion );
        $patchVersion = explode( '-', $this->sRevision );

        if ($sPatchVersionFile != '' && file_exists( $sPatchVersionFile )) {
            $this->sRevision = file_get_contents( $sPatchVersionFile );
            $patchVersion = explode( '-', $this->sRevision );
        }

        if (! file_exists( PATH_DATA . 'log' . PATH_SEP )) {
            G::mk_dir( PATH_DATA . 'log' . PATH_SEP );
        }

        //empty query log
        $sqlLog = PATH_DATA . 'log' . PATH_SEP . "query.log";
        $fp = fopen( $sqlLog, "w+" );
        fwrite( $fp, "" );
        fclose( $fp );

        $aEnvironmentsUpdated = array ();
        $aEnvironmentsDiff = array ();
        $aErrors = array ();

        //now will verify each folder and file has permissions to write and add files.
        if ($this->sUpgradeFileList != '') {
            $bCopySchema = true;
            $oFile = fopen( $this->sUpgradeFileList, 'r' );
            while ($sLine = trim( fgets( $oFile ) )) {
                $sLine = substr( $sLine, 1 );
                $aAux = explode( PATH_SEP, $sLine );
                array_shift( $aAux );
                $sFilePath = implode( PATH_SEP, $aAux );
                $targetFileName = PATH_TRUNK . $sFilePath;
                if (! is_dir( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath )) {
                    //if we are updating or deleting a file
                    if (file_exists( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath )) {
                        if (file_exists( $targetFileName )) {
                            if (! is_writable( $targetFileName )) {
                                throw (new Exception( "File $targetFileName is not writable." ));
                            }
                        } else {
                            //verify parent folder, and ask if that folder is writable
                            $auxDir = explode( '/', $targetFileName );
                            array_pop( $auxDir );
                            $parentDir = implode( '/', $auxDir );
                            if (! is_dir( $parentDir )) {
                                //throw (new Exception("File $parentDir is an invalid directory."));
                                G::mk_dir( $parentDir );
                            }
                            if (! is_writable( $parentDir )) {
                                throw (new Exception( "Directory $parentDir is not writable." ));
                            }
                        }
                    } else {
                        //delete unused files
                        if (file_exists( $targetFileName ) && ! is_writable( $targetFileName )) {
                            throw (new Exception( "File $targetFileName is not writable." ));
                        }
                    }
                } else {
                    $dirName = PATH_TRUNK . $sFilePath;
                    if ($dirName[strlen( $dirName ) - 1] == '/') {
                        $dirName = substr( $dirName, 0, strlen( $dirName ) - 1 );
                    }
                    $auxDir = explode( '/', $dirName );
                    array_pop( $auxDir );
                    $parentDir = implode( '/', $auxDir );
                    if (file_exists( $dirName )) {
                        if (is_writable( $dirName )) {
                            //print "e. ok $dirName <br>";
                        } else {
                            throw (new Exception( "$dirName  is not writable" ));
                        }
                    } else {
                        if (is_writable( $parentDir )) {
                            mkdir( $dirName, 0777 );
                        } else {
                            throw (new Exception( "$dirName does not exist and parent folder $parentDir is not writable" ));
                        }
                    }
                }
            }
        }

        //processing list file files.lst
        if ($this->sUpgradeFileList != '') {
            $bCopySchema = true;
            $oFile = fopen( $this->sUpgradeFileList, 'r' );
            while ($sLine = trim( fgets( $oFile ) )) {
                $action = substr( $sLine, 0, 1 );
                $sLine = substr( $sLine, 1 );
                $aAux = explode( PATH_SEP, $sLine );
                array_shift( $aAux );
                $sFilePath = implode( PATH_SEP, $aAux );
                $targetFileName = PATH_TRUNK . $sFilePath;
                if (strtoupper( $action ) != 'D') {
                    if (! is_dir( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath )) {
                        if (file_exists( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath )) {
                            if (strpos( $sFilePath, 'schema.xml' ) !== false && $bCopySchema) {
                                $bCopySchema = false;
                                $sOldSchema = str_replace( 'schema.xml', 'schema_' . date( 'Ymd' ) . '.xml', PATH_TRUNK . $sFilePath );
                                $this->pm_copy( PATH_TRUNK . $sFilePath, $sOldSchema );
                            }
                            if (file_exists( $targetFileName )) {
                                if (is_writable( $targetFileName )) {
                                    $this->pm_copy( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath, $targetFileName );
                                    @chmod( $targetFileName, 0666 );
                                } else {
                                    throw (new Exception( "Failed to open file: Permission denied in $targetFileName." ));
                                }
                            } else {
                                $this->pm_copy( $this->sPath . 'processmaker' . PATH_SEP . $sFilePath, $targetFileName );
                                @chmod( $targetFileName, 0666 );
                            }
                        } else {
                            //delete unused files
                            if (file_exists( $targetFileName )) {
                                @unlink( $targetFileName );
                            }
                        }
                    } else {
                        if (! file_exists( PATH_TRUNK . $sFilePath )) {
                            mkdir( PATH_TRUNK . $sFilePath, 0777 );
                        }
                    }
                } elseif (file_exists( PATH_TRUNK . $sFilePath ) && $sFilePath != 'workflow/engine/gulliver') {
                    @unlink( PATH_TRUNK . $sFilePath );
                }
            }
        }

        //end files copied.
        $missedFiles = '';
        $distinctFiles = '';
        $missed = 0;
        $distinct = 0;
        //checking files of this installation server with the files in Repository Code.
        if ($sCheckListFile != '') {
            $fp = fopen( $sCheckListFile, 'r' );
            while (! feof( $fp )) {
                $line = explode( ' ', fgets( $fp ) );
                if (count( $line ) == 3) {
                    $file = PATH_TRUNK . trim( $line[2] );
                    if (is_readable( $file )) {
                        $size = sprintf( "%07d", filesize( $file ) );
                        $checksum = sprintf( "%010u", crc32( file_get_contents( $file ) ) );
                        if (! ($line[0] == $size && $line[1] == $checksum) && substr( $file, - 4 ) != '.xml') {
                            $distinctFiles .= $file . "\n";
                            $distinct ++;
                        }
                    } else {
                        $missedFiles .= $file . "\n";
                        $missed ++;
                    }
                }
            }
            fclose( $fp );
        }

        if ($missed > 0) {
            $aErrors[] = "Warning: there are $missed missed files. ";
        }
        $aErrors[] = $missedFiles;

        if ($distinct > 0) {
            $aErrors[] = "Warning: there are $distinct files with differences. ";
            $aErrors[] = $distinctFiles;
        }

        //now include the files and classes needed for upgrade databases, dont move this files, because we
        //are getting the last files in this point.  Even the files was in the patch we will take the new ones.
        include PATH_METHODS . PATH_SEP . 'setup' . PATH_SEP . 'upgrade_RBAC.php';
        G::LoadClass( 'languages' );
        G::LoadSystem( 'database_mysql' );

        $bForceXml = true;
        $bParseSchema = true;
        $bParseSchemaRBAC = true;
        $oDirectory = dir( PATH_DB );

        //count db.php files ( workspaces )
        $aWorkspaces = array ();
        while (($sObject = $oDirectory->read())) {
            if (is_dir( PATH_DB . $sObject ) && substr( $sObject, 0, 1 ) != '.' && file_exists( PATH_DB . $sObject . PATH_SEP . 'db.php' )) {
                $aWorkspaces[] = $sObject;
            }
        }
        $aUpgradeData = array ();
        $aUpgradeData['workspaces'] = $aWorkspaces;
        $aUpgradeData['wsQuantity'] = count( $aWorkspaces );
        $aUpgradeData['sPoFile'] = $sPoFile;
        $aUpgradeData['bForceXmlPoFile'] = true;
        $aUpgradeData['sSchemaFile'] = $sSchemaFile;
        $aUpgradeData['sSchemaRBACFile'] = $sSchemaRBACFile;

        file_put_contents( PATH_DATA . 'log' . PATH_SEP . "upgrade.data.bin", serialize( $aUpgradeData ) );

        $sSchemaFile = '';
        $sPoFile = '';
        $sSchemaRBACFile = '';

        $oDirectory = dir( PATH_DB );
        while (($sObject = $oDirectory->read())) {
            if (is_dir( PATH_DB . $sObject ) && substr( $sObject, 0, 1 ) != '.') {
                if (file_exists( PATH_DB . $sObject . PATH_SEP . 'db.php' )) {
                    eval( $this->getDatabaseCredentials( PATH_DB . $sObject . PATH_SEP . 'db.php' ) );
                }
                $aEnvironmentsUpdated[] = $sObject;
                $aEnvironmentsDiff[] = $sObject;
            }
        }
        $oDirectory->close();
        @unlink( PATH_CORE . 'config/_databases_.php' );

        //clean up smarty directory
        $oDirectory = dir( PATH_SMARTY_C );
        while ($sFilename = $oDirectory->read()) {
            if (($sFilename != '.') && ($sFilename != '..')) {
                @unlink( PATH_SMARTY_C . PATH_SEP . $sFilename );
            }
        }

        //clean up xmlform folders
        $sDir = PATH_C . 'xmlform';
        if (file_exists( $sDir ) && is_dir( $sDir )) {
            $oDirectory = dir( $sDir );
            while ($sObjectName = $oDirectory->read()) {
                if (($sObjectName != '.') && ($sObjectName != '..')) {
                    if (is_dir( $sDir . PATH_SEP . $sObjectName )) {
                        G::rm_dir( $sDir . PATH_SEP . $sObjectName );
                    }
                }
            }
            $oDirectory->close();
        }

        //changing the PM_VERSION according the patch file name
        $oFile = fopen( PATH_METHODS . 'login/version-pmos.php', 'w+' );
        if (isset( $this->sRevision ) && $this->sRevision != '') {
            fwrite( $oFile, "<?php\n  define ( 'PM_VERSION' , str_replace ( ' ','',  '1.6-" . $this->sRevision . "' ));\n?>" );
        } else {
            fwrite( $oFile, "<?php\n  define ( 'PM_VERSION' , str_replace ( ' ','',  'unknow' ));\n?>" );
        }
        fclose( $oFile );
        $ver = explode( "-", $this->sRevision );
        $this->aErrors = $aErrors;
        $this->aWorkspaces = $aWorkspaces;

        return $ver;
    }

    /**
     * This function does to clean up to the upgrate directory
     *
     *
     * @name cleanupUpgradeDirectory
     *
     * param
     * @return array
     */
    public function cleanupUpgradeDirectory ()
    {
        G::rm_dir( PATH_DATA . "upgrade" . PATH_SEP . "processmaker" );
    }

    /**
     * This function creates a directory
     *
     *
     * @name pm_copy
     *
     * @param string $source
     * @param string $target
     * @return void
     */
    public function pm_copy ($source, $target)
    {
        if (! is_dir( dirname( $target ) )) {
            G::mk_dir( dirname( $target ) );
        }
        if (! copy( $source, $target )) {
            krumo( $source );
            krumo( $target );
        }
    }

    /**
     * This function gets info about db
     *
     *
     * @name getDatabaseCredentials
     *
     * @param string $dbFile
     * @return $sContent
     */
    public function getDatabaseCredentials ($dbFile)
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

    /**
     * Retrieves the system schema.
     *
     * @return schema content in an array
     */
    public static function getSystemSchema ()
    {
        return System::getSchema( PATH_TRUNK . "workflow/engine/config/schema.xml" );
    }

    /**
     * Retrieves the system schema rbac.
     *
     * @return schema content in an array
     */
    public static function getSystemSchemaRbac ()
    {
    	return System::getSchema( PATH_TRUNK . "rbac/engine/config/schema.xml" );
    }

    /**
     * Retrieves the schema for a plugin.
     *
     * @param string $pluginName name of the plugin
     * @return $sContent
     */
    public static function getPluginSchema ($pluginName)
    {
        if (file_exists( PATH_PLUGINS . $pluginName . "/config/schema.xml" )) {
            return System::getSchema( PATH_PLUGINS . $pluginName . "/config/schema.xml" );
        } else {
            return false;
        }
    }

    /**
     * Retrieves a schema array from a file.
     *
     * @param string $sSchemaFile schema filename
     * @return $sContent
     */
    public static function getSchema ($sSchemaFile)
    {
        /* This is the MySQL mapping that Propel uses (from MysqlPlatform.php) */
        $mysqlTypes = array ('NUMERIC' => "DECIMAL",'LONGVARCHAR' => "MEDIUMTEXT",'TIMESTAMP' => "DATETIME",'BU_TIMESTAMP' => "DATETIME",'BINARY' => "BLOB",'VARBINARY' => "MEDIUMBLOB",'LONGVARBINARY' => "LONGBLOB",'BLOB' => "LONGBLOB",'CLOB' => "LONGTEXT",
        /* This is not from Propel, but is required to get INT right */
        'INTEGER' => "INT"
        );

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

                /* Get the field type. Propel uses VARCHAR if nothing else is specified */
                $type = $oColumn->hasAttribute( 'type' ) ? strtoupper( $oColumn->getAttribute( 'type' ) ) : "VARCHAR";

                /* Convert type to MySQL type according to Propel */
                if (array_key_exists( $type, $mysqlTypes )) {
                    $type = $mysqlTypes[$type];
                }

                $size = $oColumn->hasAttribute( 'size' ) ? $oColumn->getAttribute( 'size' ) : null;
                /* Add default sizes from MySQL */
                if ($type == "TINYINT" && ! $size) {
                    $size = "4";
                }
                if ($type == "INT" && ! $size) {
                    $size = "11";
                }

                if ($size) {
                    $type = "$type($size)";
                }

                $required = $oColumn->hasAttribute( 'required' ) ? $oColumn->getAttribute( 'required' ) : null;
                /* Convert $required to a bool */
                $required = (in_array( strtolower( $required ), array ('1','true'
                ) ));

                $default = $oColumn->hasAttribute( 'default' ) ? $oColumn->getAttribute( 'default' ) : null;

                $primaryKey = $oColumn->hasAttribute( 'primaryKey' ) ? $oColumn->getAttribute( 'primaryKey' ) : null;
                /* Convert $primaryKey to a bool */
                $primaryKey = (in_array( strtolower( $primaryKey ), array ('1','true'
                ) ));
                if ($primaryKey) {
                    $aPrimaryKeys[] = $sColumName;
                }
                $aSchema[$sTableName][$sColumName] = array ('Field' => $sColumName,'Type' => $type,'Null' => $required ? "NO" : "YES",'Default' => $default
                );
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
    }

    /**
     * Returns the difference between two schema arrays
     *
     * @param array $aOldSchema original schema array
     * @param array $aNewSchema new schema array
     * @return array with tablesToAdd, tablesToAlter, tablesWithNewIndex and tablesToAlterIndex
     */
    public static function compareSchema ($aOldSchema, $aNewSchema)
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
                        if (! isset( $aOldSchema[$sTableName][$sColumName] )) {
                            //this column doesnt exist in oldschema
                            if (! isset( $aChanges['tablesToAlter'][$sTableName] )) {
                                $aChanges['tablesToAlter'][$sTableName] = array ('DROP' => array (),'ADD' => array (),'CHANGE' => array ()
                                );
                            }
                            $aChanges['tablesToAlter'][$sTableName]['ADD'][$sColumName] = $aParameters;
                        } else {
                            //the column exists
                            $newField = $aNewSchema[$sTableName][$sColumName];
                            $oldField = $aOldSchema[$sTableName][$sColumName];
                            //both are null, no change is required
                            if (! isset( $newField['Default'] ) && ! isset( $oldField['Default'] )) {
                                $changeDefaultAttr = false;
                                //one of them is null, change IS required
                            }
                            if (! isset( $newField['Default'] ) && isset( $oldField['Default'] ) && $oldField['Default'] != '') {
                                $changeDefaultAttr = true;
                            }
                            if (isset( $newField['Default'] ) && ! isset( $oldField['Default'] )) {
                                $changeDefaultAttr = true;
                                //both are defined and they are different.
                            }
                            if (isset( $newField['Default'] ) && isset( $oldField['Default'] )) {
                                if ($newField['Default'] != $oldField['Default']) {
                                    $changeDefaultAttr = true;
                                } else {
                                    $changeDefaultAttr = false;
                                }
                            }
                            //special cases
                            // BLOB and TEXT columns cannot have DEFAULT values.  http://dev.mysql.com/doc/refman/5.0/en/blob.html
                            if (in_array( strtolower( $newField['Type'] ), array ('text','mediumtext') )) {
                                $changeDefaultAttr = false;
                            }
                                //#1067 - Invalid default value for datetime field
                            if (in_array( $newField['Type'], array ('datetime') ) && isset( $newField['Default'] ) && $newField['Default'] == '') {
                                $changeDefaultAttr = false;
                            }

                            //#1067 - Invalid default value for int field
                            if (substr( $newField['Type'], 0, 3 ) == "int" && isset( $newField['Default'] ) && $newField['Default'] == '') {
                                $changeDefaultAttr = false;
                            }

                            //if any difference exists, then insert the difference in aChanges
                            if (strcasecmp( $newField['Field'], $oldField['Field'] ) !== 0 || strcasecmp( $newField['Type'], $oldField['Type'] ) !== 0 || strcasecmp( $newField['Null'], $oldField['Null'] ) !== 0 || $changeDefaultAttr) {
                                if (! isset( $aChanges['tablesToAlter'][$sTableName] )) {
                                    $aChanges['tablesToAlter'][$sTableName] = array ('DROP' => array (),'ADD' => array (),'CHANGE' => array ());
                                }
                                $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Field'] = $newField['Field'];
                                $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Type'] = $newField['Type'];
                                $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Null'] = $newField['Null'];
                                if (isset( $newField['Default'] )) {
                                    $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Default'] = $newField['Default'];
                                } else {
                                    $aChanges['tablesToAlter'][$sTableName]['CHANGE'][$sColumName]['Default'] = null;
                                }
                            }
                        }
                    }
                    //only columns, no the indexes column
                }
                //foreach $aColumns

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
            }
            //for-else table exists
        }
        //for new schema
        return $aChanges;
    }

    public function getEmailConfiguration ()
    {
        G::LoadClass( 'configuration' );
        $conf = new Configurations();
        $config = $conf->load( 'Emails' );

        return $config;
    }

    public function getSkingList ()
    {
        //Create Skins custom folder if it doesn't exists
        if (! is_dir( PATH_CUSTOM_SKINS )) {
            G::verifyPath( PATH_CUSTOM_SKINS, true );
        }

        //Get Skin Config files
        $skinListArray = array ();
        $customSkins = glob( PATH_CUSTOM_SKINS . "*/config.xml" );

        if (!is_array($customSkins)) {
            $customSkins = array();
        }

        // getting al base skins
        $baseSkins = glob( G::ExpandPath( "skinEngine" ) . '*/config.xml' );

        // filtering no public skins (uxs, simplified)
        foreach ($baseSkins as $i => $skinName) {
            if (strpos( $skinName, 'simplified' ) !== false || strpos( $skinName, 'uxs' ) !== false || strpos( $skinName, 'uxmodern' ) !== false) {
                unset( $baseSkins[$i] );
            }
        }

        $customSkins = array_merge( $baseSkins, $customSkins );
        $global = G::LoadTranslation('ID_GLOBAL');

        //Read and parse each Configuration File
        foreach ($customSkins as $key => $configInformation) {
            $folderId = basename( dirname( $configInformation ) );

            if ($folderId == 'base') {
                $folderId = 'classic';
            }

            $partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;
            if ($partnerFlag && ($folderId == 'classic')){
                continue;
            }

            $xmlConfiguration = file_get_contents( $configInformation );
            $xmlConfigurationObj = G::xmlParser( $xmlConfiguration );

            if (isset( $xmlConfigurationObj->result['skinConfiguration'] )) {
                $skinInformationArray = $skinFilesArray = $xmlConfigurationObj->result['skinConfiguration']['__CONTENT__']['information']['__CONTENT__'];
                $res = array ();
                $res['SKIN_FOLDER_ID'] = strtolower( $folderId );

                foreach ($skinInformationArray as $keyInfo => $infoValue) {
                    $res['SKIN_' . strtoupper( $keyInfo )] = (isset($infoValue['__VALUE__'])) ? $infoValue['__VALUE__'] : '';
                }
                $res['SKIN_CREATEDATE'] = (isset($res['SKIN_CREATEDATE'])) ? $res['SKIN_CREATEDATE']: '';
                $res['SKIN_MODIFIEDDATE'] = (isset($res['SKIN_MODIFIEDDATE'])) ? $res['SKIN_MODIFIEDDATE']: '';
                $res['SKIN_WORKSPACE'] = (isset($res['SKIN_WORKSPACE'])) ? ( ($res['SKIN_WORKSPACE'] != '')? $res['SKIN_WORKSPACE'] : $global): $global;

                $swWS = true;
                if ($res['SKIN_WORKSPACE'] != $global) {
                    $workspace = explode("|", $res['SKIN_WORKSPACE']);
                    $swWS = false;
                    foreach ($workspace as $key => $value) {
                        if ($value == SYS_SYS) {
                            $swWS = true;
                            break;
                        }
                    }
                }
                if ($swWS) {
                    $skinListArray['skins'][] = $res;
                }
            }
        }

        $skinListArray['currentSkin'] = SYS_SKIN;

        return $skinListArray;
    }

    public function getAllTimeZones ()
    {
        $timezones = DateTimeZone::listAbbreviations();

        $cities = array ();
        foreach ($timezones as $key => $zones) {
            foreach ($zones as $id => $zone) {
                /**
                 * Only get timezones explicitely not part of "Others".
                 *
                 * @see http://www.php.net/manual/en/timezones.others.php
                 */
                if (preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Africa|Europe|Indian|Pacific)\//', $zone['timezone_id'] ) && $zone['timezone_id']) {
                    $cities[$zone['timezone_id']][] = $key;
                }
            }
        }

        // For each city, have a comma separated list of all possible timezones for that city.
        foreach ($cities as $key => $value) {
            $cities[$key] = join( ', ', $value );
        }
        // Only keep one city (the first and also most important) for each set of possibilities.
        $cities = array_unique( $cities );

        // Sort by area/city name.
        ksort( $cities );

        return $cities;
    }

    public static function getSystemConfiguration ($globalIniFile = '', $wsIniFile = '', $wsName = '')
    {
        $readGlobalIniFile = false;
        $readWsIniFile = false;

        if (empty( $globalIniFile )) {
            $globalIniFile = PATH_CORE . 'config' . PATH_SEP . 'env.ini';
        }

        if (empty( $wsIniFile )) {
            if (defined( 'PATH_DB' )) {
                // if we're on a valid workspace env.
                if (empty( $wsName )) {
                    $uriParts = explode( '/', getenv( "REQUEST_URI" ) );
                    if (isset( $uriParts[1] )) {
                        if (substr( $uriParts[1], 0, 3 ) == 'sys') {
                            $wsName = substr( $uriParts[1], 3 );
                        }
                    }
                }
                $wsIniFile = PATH_DB . $wsName . PATH_SEP . 'env.ini';
            }
        }

        $readGlobalIniFile = file_exists( $globalIniFile ) ? true : false;
        $readWsIniFile = file_exists( $wsIniFile ) ? true : false;

        if (isset( $_SESSION['PROCESSMAKER_ENV'] )) {
            $md5 = array ();

            if ($readGlobalIniFile) {
                $md5[] = md5_file( $globalIniFile );
            }
            if ($readWsIniFile) {
                $md5[] = md5_file( $wsIniFile );
            }
            $hash = implode( '-', $md5 );

            if ($_SESSION['PROCESSMAKER_ENV_HASH'] === $hash) {
                $_SESSION['PROCESSMAKER_ENV']['from_cache'] = 1;
                return $_SESSION['PROCESSMAKER_ENV'];
            }
        }

        // default configuration
        $config = array ('debug' => 0,'debug_sql' => 0,'debug_time' => 0,'debug_calendar' => 0,'wsdl_cache' => 1,'memory_limit' => "256M", 'time_zone' => 'America/New_York','memcached' => 0,'memcached_server' => '','default_skin' => 'neoclassic','default_lang' => 'en','proxy_host' => '','proxy_port' => '','proxy_user' => '','proxy_pass' => '');

        // read the global env.ini configuration file
        if ($readGlobalIniFile && ($globalConf = @parse_ini_file( $globalIniFile )) !== false) {
            $config = array_merge( $config, $globalConf );
        }

        // Workspace environment configuration
        if ($readWsIniFile && ($wsConf = @parse_ini_file( $wsIniFile )) !== false) {
            $config = array_merge( $config, $wsConf );
        }

        // validation debug config, only binary value is valid; debug = 1, to enable
        $config['debug'] = $config['debug'] == 1 ? 1 : 0;

        if ($config['proxy_pass'] != '') {
            $config['proxy_pass'] = G::decrypt( $config['proxy_pass'], 'proxy_pass' );
        }

        $md5 = array ();
        if ($readGlobalIniFile) {
            $md5[] = @md5_file( $globalIniFile );
        }
        if ($readWsIniFile) {
            $md5[] = @md5_file( $wsIniFile );
        }
        $hash = implode( '-', $md5 );

        $_SESSION['PROCESSMAKER_ENV'] = $config;
        $_SESSION['PROCESSMAKER_ENV_HASH'] = $hash;

        return $config;
    }

    public function updateIndexFile ($conf)
    {
        if (! file_exists( PATH_HTML . 'index.html' )) {
            throw new Exception( 'The public index file "' . PATH_HTML . 'index.html" does not exist!' );
        } else {
            if (! is_writable( PATH_HTML . 'index.html' )) {
                throw new Exception( 'The index.html file is not writable on workflow/public_html directory.' );
            }
        }

        $content = file_get_contents( PATH_HTML . 'index.html' );
        $result = false;

        $patt = '/<meta\s+http\-equiv="REFRESH"\s+content=\"0;URL=(.+)\"\s*\/>/';

        @preg_match( $patt, $content, $match );

        if (is_array( $match ) && count( $match ) > 0 && isset( $match[1] )) {
            $newUrl = 'sys/' . $conf['lang'] . '/' . $conf['skin'] . '/login/login';

            $newMetaStr = str_replace( $match[1], $newUrl, $match[0] );
            $newContent = str_replace( $match[0], $newMetaStr, $content );

            $result = (@file_put_contents( PATH_HTML . 'index.html', $newContent ) !== false);
        }

        return $result;
    }

    public function solrEnv ($sysName = '')
    {
        if (empty( $sysName )) {
            $conf = System::getSystemConfiguration();
        } else {
            $conf = System::getSystemConfiguration( '', '', $sysName );
        }

        if (! isset( $conf['solr_enabled'] ) || ! isset( $conf['solr_host'] ) || ! isset( $conf['solr_instance'] )) {
            return false;
        }

        if ($conf['solr_enabled']) {
            return array ('solr_enabled' => $conf['solr_enabled'],'solr_host' => $conf['solr_host'],'solr_instance' => $conf['solr_instance']
            );
        }

        return false;
    }
}
// end System class

