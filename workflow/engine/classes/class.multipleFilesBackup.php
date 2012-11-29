<?php

G::LoadSystem( 'dbMaintenance' );
G::LoadClass( "cli" );

/**
 * Class MultipleFilesBackup
 * create a backup of this workspace
 *
 * Exports the database and copies the files to an tar archive o several if the max filesize is reached.
 */
class multipleFilesBackup
{
    private $dir_to_compress = "";
    private $filename = "backUpProcessMaker.tar";
    private $fileSize = "1000";
    // 1 GB by default.
    private $sizeDescriptor = "m";
    //megabytes
    private $tempDirectories = array ();

    /* Constructor
    *  @filename contains the path and filename of the comppress file(s).
    *  @size     got the Max size of the compressed files, by default if the $size less to zero will mantains 1000 Mb as Max size.
    */
    public function multipleFilesBackup ($filename, $size)
    {
        if (! empty( $filename )) {
            $this->filename = $filename;
        }
        if (! empty( $size ) && (int) $size > 0) {
            $this->fileSize = $size;
        }
    }

    /* Gets workspace information enough to make its backup.
    *  @workspace contains the workspace to be add to the commpression process.
    */
    public function addToBackup ($workspace)
    {
        //verifing if workspace exists.
        if (! $workspace->workspaceExists()) {
            echo "Workspace {$workspace->name} not found\n";
            return false;
        }
        //create destination path
        if (! file_exists( PATH_DATA . "upgrade/" )) {
            mkdir( PATH_DATA . "upgrade/" );
        }
        $tempDirectory = PATH_DATA . "upgrade/" . basename( tempnam( __FILE__, '' ) );
        mkdir( $tempDirectory );
        $metadata = $workspace->getMetadata();
        CLI::logging( "Temporing up database...\n" );
        $metadata["databases"] = $workspace->exportDatabase( $tempDirectory );
        $metadata["directories"] = array ("{$workspace->name}.files");
        $metadata["version"] = 1;
        $metaFilename = "$tempDirectory/{$workspace->name}.meta";
        if (! file_put_contents( $metaFilename, str_replace( array (",","{","}"), array (",\n  ","{\n  ","\n}\n"), G::json_encode( $metadata ) ) )) {
            CLI::logging( "Could not create backup metadata" );
        }
        CLI::logging( "Adding database to backup...\n" );
        $this->addDirToBackup( $tempDirectory );
        CLI::logging( "Adding files to backup...\n" );
        $this->addDirToBackup( $workspace->path );
        $this->tempDirectories[] = $tempDirectory;
    }

    /* Add a directory containing Db files or info files to be commpressed
    *  @directory the name and path of the directory to be add to the commpression process.
    */
    private function addDirToBackup ($directory)
    {
        if (! empty( $directory )) {
            $this->dir_to_compress .= $directory . " ";
        }
    }

    // Commpress the DB and files into a single or several files with numerical series extentions

    public function letsBackup ()
    {
        // creating command
        $CommpressCommand = "tar czv ";
        $CommpressCommand .= $this->dir_to_compress;
        $CommpressCommand .= "| split -b ";
        $CommpressCommand .= $this->fileSize;
        $CommpressCommand .= "m -d - ";
        $CommpressCommand .= $this->filename . ".";
        //executing command to create the files
        echo exec( $CommpressCommand );
        //Remove leftovers dirs.
        foreach ($this->tempDirectories as $tempDirectory) {
            CLI::logging( "Deleting: " . $tempDirectory . "\n" );
            G::rm_dir( $tempDirectory );
        }
    }
    /* Restore from file(s) commpressed by letsBackup function, into a temporary directory
    *  @ filename     got the name and path of the compressed file(s), if there are many files with file extention as a numerical series, the extention should be discriminated.
    *  @ srcWorkspace contains the workspace to be restored.
    *  @ dstWorkspace contains the workspace to be overwriting.
    *  @ overwrite    got the option true if the workspace will be overwrite.
    */
    static public function letsRestore ($filename, $srcWorkspace, $dstWorkspace = null, $overwrite = true)
    {
        // Needed info:
        // TEMPDIR  /shared/workflow_data/upgrade/
        // BACKUPS  /shared/workflow_data/backups/
        // Creating command  cat myfiles_split.tgz_* | tar xz
        $DecommpressCommand = "cat " . $filename . ".* ";
        $DecommpressCommand .= " | tar xzv";

        $tempDirectory = PATH_DATA . "upgrade/" . basename( tempnam( __FILE__, '' ) );
        $parentDirectory = PATH_DATA . "upgrade";
        if (is_writable( $parentDirectory )) {
            mkdir( $tempDirectory );
        } else {
            throw new Exception( "Could not create directory:" . $parentDirectory );
        }
        //Extract all backup files, including database scripts and workspace files
        CLI::logging( "Restoring into " . $tempDirectory . "\n" );
        chdir( $tempDirectory );
        echo exec( $DecommpressCommand );
        CLI::logging( "\nUncompressed into: " . $tempDirectory . "\n" );

        //Search for metafiles in the new standard (the old standard would contain meta files.
        $metaFiles = glob( $tempDirectory . "/*.meta" );
        if (empty( $metaFiles )) {
            $metaFiles = glob( $tempDirectory . "/*.txt" );
            if (! empty( $metaFiles )) {
                return workspaceTools::restoreLegacy( $tempDirectory );
            } else {
                throw new Exception( "No metadata found in backup" );
            }
        } else {
            CLI::logging( "Found " . count( $metaFiles ) . " workspaces in backup:\n" );
            foreach ($metaFiles as $metafile) {
                CLI::logging( "-> " . basename( $metafile ) . "\n" );
            }
        }
        if (count( $metaFiles ) > 1 && (! isset( $srcWorkspace ))) {
            throw new Exception( "Multiple workspaces in backup but no workspace specified to restore" );
        }
        if (isset( $srcWorkspace ) && ! in_array( "$srcWorkspace.meta", array_map( basename, $metaFiles ) )) {
            throw new Exception( "Workspace $srcWorkspace not found in backup" );
        }
        foreach ($metaFiles as $metaFile) {
            $metadata = G::json_decode( file_get_contents( $metaFile ) );
            if ($metadata->version != 1) {
                throw new Exception( "Backup version {$metadata->version} not supported" );
            }
            $backupWorkspace = $metadata->WORKSPACE_NAME;
            if (isset( $dstWorkspace )) {
                $workspaceName = $dstWorkspace;
                $createWorkspace = true;
            } else {
                $workspaceName = $metadata->WORKSPACE_NAME;
                $createWorkspace = false;
            }
            if (isset( $srcWorkspace ) && strcmp( $metadata->WORKSPACE_NAME, $srcWorkspace ) != 0) {
                CLI::logging( CLI::warning( "> Workspace $backupWorkspace found, but not restoring." ) . "\n" );
                continue;
            } else {
                CLI::logging( "> Restoring " . CLI::info( $backupWorkspace ) . " to " . CLI::info( $workspaceName ) . "\n" );
            }
            $workspace = new workspaceTools( $workspaceName );
            if ($workspace->workspaceExists()) {
                if ($overwrite) {
                    CLI::logging( CLI::warning( "> Workspace $workspaceName already exist, overwriting!" ) . "\n" );
                } else {
                    throw new Exception( "Destination workspace already exist (use -o to overwrite)" );
                }
            }
            if (file_exists( $workspace->path )) {
                G::rm_dir( $workspace->path );
            }
            foreach ($metadata->directories as $dir) {
                CLI::logging( "+> Restoring directory '$dir'\n" );
                if (! rename( "$tempDirectory/$dir", $workspace->path )) {
                    throw new Exception( "There was an error copying the backup files ($tempDirectory/$dir) to the workspace directory {$workspace->path}." );
                }
            }

            CLI::logging( "> Changing file permissions\n" );
            $shared_stat = stat( PATH_DATA );
            if ($shared_stat !== false) {
                workspaceTools::dirPerms( $workspace->path, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode'] );
            } else {
                CLI::logging( CLI::error( "Could not get the shared folder permissions, not changing workspace permissions" ) . "\n" );
            }

            list ($dbHost, $dbUser, $dbPass) = @explode( SYSTEM_HASH, G::decrypt( HASH_INSTALLATION, SYSTEM_HASH ) );

            CLI::logging( "> Connecting to system database in '$dbHost'\n" );
            $link = mysql_connect( $dbHost, $dbUser, $dbPass );
            @mysql_query( "SET NAMES 'utf8';" );
            @mysql_query( "SET FOREIGN_KEY_CHECKS=0;" );
            if (! $link) {
                throw new Exception( 'Could not connect to system database: ' . mysql_error() );
            }

            $newDBNames = $workspace->resetDBInfo( $dbHost, $createWorkspace );

            foreach ($metadata->databases as $db) {
                $dbName = $newDBNames[$db->name];
                CLI::logging( "+> Restoring database {$db->name} to $dbName\n" );
                $workspace->executeSQLScript( $dbName, "$tempDirectory/{$db->name}.sql" );
                $workspace->createDBUser( $dbName, $db->pass, "localhost", $dbName );
                $workspace->createDBUser( $dbName, $db->pass, "%", $dbName );
            }
            $workspace->upgradeCacheView( false );
            mysql_close( $link );

        }
        CLI::logging( "Removing temporary files\n" );
        G::rm_dir( $tempDirectory );
        CLI::logging( CLI::info( "Done restoring" ) . "\n" );
    }
}

