<?php
/**
 * class.webdav.php
 *
 * @package workflow.engine.classes
 */

require_once "HTTP/WebDAV/Server.php";
require_once "System.php";

/**
 * ProcessMaker Filesystem access using WebDAV
 *
 * @access public
 * @package workflow.engine.classes
 *
 */
class ProcessMakerWebDav extends HTTP_WebDAV_Server
{
    /**
     * Root directory for WebDAV access
     *
     * Defaults to webserver document root (set by ServeRequest)
     *
     * @access private
     * @var string
     */
    var $base = "";

    /**
     * Serve a webdav request
     *
     * @access public
     * @param string
     */
    function ServeRequest ($base = false)
    {
        //$this->base = '/';
        $this->uriBase = '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/webdav/';

        // let the base class do all the work
        parent::ServeRequest();
    }

    /**
     * No authentication is needed here
     *
     * @access private
     * @param string HTTP Authentication type (Basic, Digest, ...)
     * @param string Username
     * @param string Password
     * @return bool true on successful authentication
     */
    function check_auth ($type, $user, $pass)
    {
        return true;
    }

    /**
     * PROPFIND method handler
     *
     * @param array general parameter passing array
     * @param array return array for file properties
     * @return bool true on success
     */
    function PROPFIND (&$options, &$files)
    {
        $paths = $this->paths;
        // prepare property array
        $files["files"] = array ();

        $pathClasses = PATH_DB . PATH_SEP . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        if (count( $paths ) == 0 && is_dir( $pathClasses )) {
            $props = array ();
            $props[] = $this->mkprop( "displayname", 'Classes' );
            $props[] = $this->mkprop( "creationdate", filectime( $pathClasses ) );
            $props[] = $this->mkprop( "getlastmodified", filemtime( $pathClasses ) );
            $props[] = $this->mkprop( "lastaccessed", filemtime( $pathClasses ) );
            $props[] = $this->mkprop( "resourcetype", 'collection' );
            $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
            $files["files"][] = array ('path' => 'classes','props' => $props
            );
        }

        if (count( $paths ) > 0 && $paths[0] == 'classes' && is_dir( $pathClasses )) {
            // try to open directory
            $handle = @opendir( $pathClasses );
            if ($handle) {
                while ($filename = readdir( $handle )) {
                    $ext = array_pop( explode( '.', $filename ) );
                    if ($filename != "." && $filename != ".." && ! is_dir( $pathClasses . $filename ) && $ext == 'php') {
                        $props = array ();
                        $props[] = $this->mkprop( "displayname", $filename );
                        $props[] = $this->mkprop( "creationdate", filectime( $pathClasses . $filename ) );
                        $props[] = $this->mkprop( "getlastmodified", filemtime( $pathClasses . $filename ) );
                        $props[] = $this->mkprop( "getetag", fileatime( $pathClasses . $filename ) );
                        $props[] = $this->mkprop( "lastaccessed", filemtime( $pathClasses . $filename ) );
                        $props[] = $this->mkprop( "resourcetype", '' );
                        $props[] = $this->mkprop( "getcontenttype", 'text/plain' );
                        $props[] = $this->mkprop( "getcontentlength", filesize( $pathClasses . $filename ) );
                        if (count( $paths ) == 1 || (count( $paths ) == 2 && $paths[1] == $filename))
                            $files["files"][] = array ('path' => "classes/$filename",'props' => $props
                            );
                    }
                }
            }

        } //path classes


        $pathProcesses = PATH_DB . SYS_SYS . PATH_SEP;
        if (count( $paths ) == 0 && is_dir( $pathProcesses )) {
            $props = array ();
            $props[] = $this->mkprop( "displayname", 'Processes' );
            $props[] = $this->mkprop( "creationdate", filectime( $pathProcesses ) );
            $props[] = $this->mkprop( "getlastmodified", filemtime( $pathProcesses ) );
            $props[] = $this->mkprop( "resourcetype", 'collection' );
            $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
            $files["files"][] = array ('path' => 'processes','props' => $props
            );
        }

        //list all active processes
        if (count( $paths ) == 1 && $paths[0] == 'processes' && is_dir( $pathProcesses )) {
            // try to get the process directory list
            G::LoadClass( 'processMap' );
            G::LoadClass( 'model/Process' );
            $oProcessMap = new processMap();
            $oProcess = new Process();
            $c = $oProcessMap->getConditionProcessList();
            $oDataset = ProcessPeer::doSelectRS( $c );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                if ($aRow['PRO_STATUS'] == 'ACTIVE') {
                    $aProcess = $oProcess->load( $aRow['PRO_UID'] );
                    $props = array ();
                    $props[] = $this->mkprop( "displayname", $aProcess['PRO_TITLE'] );
                    $props[] = $this->mkprop( "creationdate", filectime( $pathProcesses ) );
                    $props[] = $this->mkprop( "getlastmodified", filemtime( $pathProcesses ) );
                    $props[] = $this->mkprop( "lastaccessed", filemtime( $pathProcesses ) );
                    $props[] = $this->mkprop( "resourcetype", 'collection' );
                    $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
                    $files["files"][] = array ('path' => "processes/" . $aRow['PRO_UID'],'props' => $props
                    );
                }
                $oDataset->next();
            }
        } //dir of processes


        //content of any process  ( the three major folders of Processes )
        $pathXmlform = $pathProcesses . 'xmlForms' . PATH_SEP;
        if (count( $paths ) == 2 && $paths[0] == 'processes' && is_dir( $pathProcesses )) {
            $props = array ();
            $props[] = $this->mkprop( "displayname", 'xmlforms' );
            $props[] = $this->mkprop( "creationdate", filectime( $pathXmlform ) );
            $props[] = $this->mkprop( "getlastmodified", filemtime( $pathXmlform ) );
            $props[] = $this->mkprop( "lastaccessed", filemtime( $pathXmlform ) );
            $props[] = $this->mkprop( "resourcetype", 'collection' );
            $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
            $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/xmlforms','props' => $props
            );

            $props[] = $this->mkprop( "displayname", 'mailTemplates' );
            $props[] = $this->mkprop( "creationdate", filectime( $pathProcesses ) );
            $props[] = $this->mkprop( "getlastmodified", filemtime( $pathProcesses ) );
            $props[] = $this->mkprop( "lastaccessed", filemtime( $pathProcesses ) );
            $props[] = $this->mkprop( "resourcetype", 'collection' );
            $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
            $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/mailTemplates','props' => $props
            );

            $props[] = $this->mkprop( "displayname", 'public_html' );
            $props[] = $this->mkprop( "creationdate", filectime( $pathProcesses ) );
            $props[] = $this->mkprop( "getlastmodified", filemtime( $pathProcesses ) );
            $props[] = $this->mkprop( "lastaccessed", filemtime( $pathProcesses ) );
            $props[] = $this->mkprop( "resourcetype", 'collection' );
            $props[] = $this->mkprop( "getcontenttype", 'httpd/unix-directory' );
            $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/public_html','props' => $props
            );
        } //content of any processes


        //list available xmlforms
        if (count( $paths ) == 3 && $paths[0] == 'processes' && $paths[2] == 'xmlforms' && is_dir( $pathXmlform )) {
            $pathXmlform = $pathProcesses . 'xmlForms' . PATH_SEP . $paths[1] . PATH_SEP;

            $handle = @opendir( $pathXmlform );
            if ($handle) {
                while ($filename = readdir( $handle )) {
                    $ext = array_pop( explode( '.', $filename ) );
                    if ($filename != "." && $filename != ".." && ! is_dir( $pathXmlform . $filename ) && ($ext == 'xml' || $ext == 'html')) {
                        $props = array ();
                        $props[] = $this->mkprop( "displayname", $filename );
                        $props[] = $this->mkprop( "creationdate", filectime( $pathXmlform . $filename ) );
                        $props[] = $this->mkprop( "getlastmodified", filemtime( $pathXmlform . $filename ) );
                        $props[] = $this->mkprop( "getetag", fileatime( $pathXmlform . $filename ) );
                        $props[] = $this->mkprop( "lastaccessed", filemtime( $pathXmlform . $filename ) );
                        $props[] = $this->mkprop( "resourcetype", '' );
                        $props[] = $this->mkprop( "getcontenttype", 'text/plain' );
                        $props[] = $this->mkprop( "getcontentlength", filesize( $pathXmlform . $filename ) );
                        //if ( count( $paths ) == 1 || ( count( $paths ) == 2 && $paths[1] == $filename ) )
                        $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/xmlforms/' . $filename,'props' => $props
                        );
                    }
                }
            }
        } //content of xmlforms


        //list available mailTemplates
        $pathTemplates = $pathProcesses . 'mailTemplates' . PATH_SEP;
        if (count( $paths ) == 3 && $paths[0] == 'processes' && $paths[2] == 'mailTemplates' && is_dir( $pathTemplates )) {
            $pathTemplates = $pathProcesses . 'mailTemplates' . PATH_SEP . $paths[1] . PATH_SEP;

            $handle = @opendir( $pathTemplates );
            if ($handle) {
                while ($filename = readdir( $handle )) {
                    $ext = array_pop( explode( '.', $filename ) );
                    if ($filename != "." && $filename != ".." && ! is_dir( $pathTemplates . $filename ) /* && ( $ext == 'xml' || $ext == 'html' ) */ ) {
                        $props = array ();
                        $props[] = $this->mkprop( "displayname", $filename );
                        $props[] = $this->mkprop( "creationdate", filectime( $pathTemplates . $filename ) );
                        $props[] = $this->mkprop( "getlastmodified", filemtime( $pathTemplates . $filename ) );
                        $props[] = $this->mkprop( "getetag", fileatime( $pathTemplates . $filename ) );
                        $props[] = $this->mkprop( "lastaccessed", filemtime( $pathTemplates . $filename ) );
                        $props[] = $this->mkprop( "resourcetype", '' );
                        $props[] = $this->mkprop( "getcontenttype", 'text/plain' );
                        $props[] = $this->mkprop( "getcontentlength", filesize( $pathTemplates . $filename ) );
                        //if ( count( $paths ) == 1 || ( count( $paths ) == 2 && $paths[1] == $filename ) )
                        $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/mailTemplates/' . $filename,'props' => $props
                        );
                    }
                }
            }
        } //content of mailTemplates


        //list available public_html files
        $pathPublic = $pathProcesses . 'public' . PATH_SEP;
        if (count( $paths ) == 3 && $paths[0] == 'processes' && $paths[2] == 'public_html' && is_dir( $pathTemplates )) {
            $pathPublic = $pathProcesses . 'public' . PATH_SEP . $paths[1] . PATH_SEP;

            $handle = @opendir( $pathPublic );
            if ($handle) {
                while ($filename = readdir( $handle )) {
                    $ext = array_pop( explode( '.', $filename ) );
                    if ($filename != "." && $filename != ".." && ! is_dir( $pathPublic . $filename ) /* && ( $ext == 'xml' || $ext == 'html' ) */ ) {
                        $props = array ();
                        $props[] = $this->mkprop( "displayname", $filename );
                        $props[] = $this->mkprop( "creationdate", filectime( $pathPublic . $filename ) );
                        $props[] = $this->mkprop( "getlastmodified", filemtime( $pathPublic . $filename ) );
                        $props[] = $this->mkprop( "getetag", fileatime( $pathPublic . $filename ) );
                        $props[] = $this->mkprop( "lastaccessed", filemtime( $pathPublic . $filename ) );
                        $props[] = $this->mkprop( "resourcetype", '' );
                        $props[] = $this->mkprop( "getcontenttype", 'text/plain' );
                        $props[] = $this->mkprop( "getcontentlength", filesize( $pathPublic . $filename ) );
                        //if ( count( $paths ) == 1 || ( count( $paths ) == 2 && $paths[1] == $filename ) )
                        $files["files"][] = array ('path' => 'processes/' . $paths[1] . '/public_html/' . $filename,'props' => $props
                        );
                    }
                }
            }
        } //content of public_html files


        /*
    if ( 1 ) {
      $props = array ();
      $props[] = $this->mkprop("displayname",     print_r ($pathPublic, 1) );
      $props[] = $this->mkprop("creationdate",    filectime( PATH_DB ) );
      $props[] = $this->mkprop("getlastmodified", filemtime( PATH_DB ) );
      $props[] = $this->mkprop("resourcetype",    'collection' );
      $props[] = $this->mkprop("getcontenttype",  'httpd/unix-directory' );
      $files["files"][] = array ( 'path' => '/' , 'props' => $props);
    } */

        // ok, all done
        return true;
    }

    /**
     * detect if a given program is found in the search PATH
     *
     * helper function used by _mimetype() to detect if the
     * external 'file' utility is available
     *
     * @param string program name
     * @param string optional search path, defaults to $PATH
     * @return bool true if executable program found in path
     */
    function _can_execute ($name, $path = false)
    {
        // path defaults to PATH from environment if not set
        if ($path === false) {
            $path = getenv( "PATH" );
        }

        // check method depends on operating system
        if (! strncmp( PHP_OS, "WIN", 3 )) {
            // on Windows an appropriate COM or EXE file needs to exist
            $exts = array (".exe",".com"
            );
            $check_fn = "file_exists";
        } else {
            // anywhere else we look for an executable file of that name
            $exts = array (""
            );
            $check_fn = "is_executable";
        }

        // now check the directories in the path for the program
        foreach (explode( PATH_SEPARATOR, $path ) as $dir) {
            // skip invalid path entries
            if (! file_exists( $dir ))
                continue;
            if (! is_dir( $dir ))
                continue;

                // and now look for the file
            foreach ($exts as $ext) {
                if ($check_fn( "$dir/$name" . $ext ))
                    return true;
            }
        }

        return false;
    }

    /**
     * try to detect the mime type of a file
     *
     * @param string file path
     * @return string guessed mime type
     */
    function _mimetype ($fspath)
    {
        if (@is_dir( $fspath )) {
            // directories are easy
            return "httpd/unix-directory";
        } else if (function_exists( "mime_content_type" )) {
            // use mime magic extension if available
            $mime_type = mime_content_type( $fspath );
        } else if ($this->_can_execute( "file" )) {
            // it looks like we have a 'file' command,
            // lets see it it does have mime support
            $fp = popen( "file -i '$fspath' 2>/dev/null", "r" );
            $reply = fgets( $fp );
            pclose( $fp );

            // popen will not return an error if the binary was not found
            // and find may not have mime support using "-i"
            // so we test the format of the returned string


            // the reply begins with the requested filename
            if (! strncmp( $reply, "$fspath: ", strlen( $fspath ) + 2 )) {
                $reply = substr( $reply, strlen( $fspath ) + 2 );
                // followed by the mime type (maybe including options)
                if (preg_match( '/^[[:alnum:]_-]+/[[:alnum:]_-]+;?.*/', $reply, $matches )) {
                    $mime_type = $matches[0];
                }
            }
        }

        if (empty( $mime_type )) {
            // Fallback solution: try to guess the type by the file extension
            // TODO: add more ...
            // TODO: it has been suggested to delegate mimetype detection
            //       to apache but this has at least three issues:
            //       - works only with apache
            //       - needs file to be within the document tree
            //       - requires apache mod_magic
            // TODO: can we use the registry for this on Windows?
            //       OTOH if the server is Windos the clients are likely to
            //       be Windows, too, and tend do ignore the Content-Type
            //       anyway (overriding it with information taken from
            //       the registry)
            // TODO: have a seperate PEAR class for mimetype detection?
            switch (strtolower( strrchr( basename( $fspath ), "." ) )) {
                case ".html":
                    $mime_type = "text/html";
                    break;
                case ".gif":
                    $mime_type = "image/gif";
                    break;
                case ".jpg":
                    $mime_type = "image/jpeg";
                    break;
                default:
                    $mime_type = "application/octet-stream";
                    break;
            }
        }

        return $mime_type;
    }

    /**
     * GET method handler
     *
     * @param array parameter passing array
     * @return bool true on success
     */
    function GET (&$options)
    {
        $paths = $this->paths;

        $pathClasses = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        if (count( $paths ) > 0 && $paths[0] == 'classes' && is_dir( $pathClasses )) {
            $fsFile = $pathClasses . $paths[1];
            if (count( $paths ) == 2 && file_exists( $fsFile )) {
                $content = file_get_contents( $fsFile );
                print $content;
                header( "Content-Type: " . mime_content_type( $fsFile ) );
                header( "Last-Modified: " . date( "D, j M Y H:m:s ", file_mtime( $fsFile ) ) . "GMT" );
                header( "Content-Length: " . filesize( $fsFile ) );
                return true;
            }
        }

        $pathProcesses = PATH_DB . SYS_SYS . PATH_SEP;
        if (count( $paths ) > 0 && $paths[0] == 'processes' && is_dir( $pathProcesses )) {
            if (count( $paths ) == 4 && $paths[2] == 'xmlforms') {
                $pathXmlform = $pathProcesses . 'xmlForms' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathXmlform . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $content = file_get_contents( $fsFile );
                    print $content;
                    header( "Content-Type: " . mime_content_type( $fsFile ) );
                    header( "Last-Modified: " . date( "D, j M Y H:m:s ", file_mtime( $fsFile ) ) . "GMT" );
                    header( "Content-Length: " . filesize( $fsFile ) );
                    return true;
                }
            }

            if (count( $paths ) == 4 && $paths[2] == 'mailTemplates') {
                $pathTemplates = $pathProcesses . 'mailTemplates' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathTemplates . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $content = file_get_contents( $fsFile );
                    print $content;
                    header( "Content-Type: " . mime_content_type( $fsFile ) );
                    header( "Last-Modified: " . date( "D, j M Y H:m:s ", file_mtime( $fsFile ) ) . "GMT" );
                    header( "Content-Length: " . filesize( $fsFile ) );
                    return true;
                }
            }

            if (count( $paths ) == 4 && $paths[2] == 'public_html') {
                $pathPublic = $pathProcesses . 'public' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathPublic . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $content = file_get_contents( $fsFile );
                    print $content;
                    header( "Content-Type: " . mime_content_type( $fsFile ) );
                    header( "Last-Modified: " . date( "D, j M Y H:m:s ", file_mtime( $fsFile ) ) . "GMT" );
                    header( "Content-Length: " . filesize( $fsFile ) );
                    return true;
                }
            }

        }

        print_r( $paths );
        return true;

        if ($options["path"] == '/') {
            return $this->getRoot( $options );
        }
        //print_r ($options);


        // get absolute fs path to requested resource
        $fspath = $this->base . $options["path"];

        // sanity check
        if (! file_exists( $fspath ))
            return false;

            // is this a collection?
        if (is_dir( $fspath )) {
            return $this->GetDir( $fspath, $options );
        }

        // detect resource type
        $options['mimetype'] = $this->_mimetype( $fspath );

        // detect modification time
        // see rfc2518, section 13.7
        // some clients seem to treat this as a reverse rule
        // requiering a Last-Modified header if the getlastmodified header was set
        $options['mtime'] = filemtime( $fspath );

        // detect resource size
        $options['size'] = filesize( $fspath );

        // no need to check result here, it is handled by the base class
        $options['stream'] = fopen( $fspath, "r" );

        return true;
    }

    /**
     * getRoot
     *
     * @param string &$options
     * @return boolean false
     */
    function getRoot (&$options)
    {
        $path = $this->_slashify( $options["path"] );
        // fixed width directory column format
        $format = "%15s  %-19s  %-s\n";

        echo "<html><head><title>Index of " . htmlspecialchars( $options['path'] ) . "</title></head>\n";
        echo "<h1>Index of " . htmlspecialchars( $options['path'] ) . "</h1>\n";

        echo "<pre>";
        printf( $format, "Size", "Last modified", "Filename" );
        echo "<hr>";

        $pathRoot = array ('xmlforms','public_html','dir1','dir2'
        );

        foreach ($pathRoot as $key => $val) {
            $fullpath = $fspath . "/" . $filename;
            $name = htmlspecialchars( $val );
            printf( $format, number_format( filesize( $fullpath ) ), strftime( "%Y-%m-%d %H:%M:%S", filemtime( $fullpath ) ), "<a href='$this->base_uri$path$name'>$name</a>" );
        }

        echo "</pre>";
        echo "</html>\n";

        die();

        $handle = @opendir( $fspath );
        if (! $handle) {
            return false;
        }

        while ($filename = readdir( $handle )) {
            if ($filename != "." && $filename != "..") {
                $fullpath = $fspath . "/" . $filename;
                $name = htmlspecialchars( $filename );
                printf( $format, number_format( filesize( $fullpath ) ), strftime( "%Y-%m-%d %H:%M:%S", filemtime( $fullpath ) ), "<a href='$this->base_uri$path$name'>$name</a>" );
            }
        }

        echo "</pre>";

        closedir( $handle );

        echo "</html>\n";

        exit();
    }

    /**
     * GET method handler for directories
     *
     * This is a very simple mod_index lookalike.
     * See RFC 2518, Section 8.4 on GET/HEAD for collections
     *
     * @param string directory path
     * @return void function has to handle HTTP response itself
     */
    function GetDir ($fspath, &$options)
    {
        $path = $this->_slashify( $options["path"] );
        if ($path != $options["path"]) {
            header( "Location: " . $this->base_uri . $path );
            exit();
        }

        // fixed width directory column format
        $format = "%15s  %-19s  %-s\n";

        $handle = @opendir( $fspath );
        if (! $handle) {
            return false;
        }

        echo "<html><head><title>Index of " . htmlspecialchars( $options['path'] ) . "</title></head>\n";

        echo "<h1>Index of " . htmlspecialchars( $options['path'] ) . "</h1>\n";

        echo "<pre>";
        printf( $format, "Size", "Last modified", "Filename" );
        echo "<hr>";

        while ($filename = readdir( $handle )) {
            if ($filename != "." && $filename != "..") {
                $fullpath = $fspath . "/" . $filename;
                $name = htmlspecialchars( $filename );
                printf( $format, number_format( filesize( $fullpath ) ), strftime( "%Y-%m-%d %H:%M:%S", filemtime( $fullpath ) ), "<a href='$this->base_uri$path$name'>$name</a>" );
            }
        }

        echo "</pre>";

        closedir( $handle );

        echo "</html>\n";

        exit();
    }

    /**
     * PUT method handler
     *
     * @param array parameter passing array
     * @return bool true on success
     */
    function PUT (&$options)
    {
        $paths = $this->paths;

        $pathClasses = PATH_DB . PATH_SEP . 'classes' . PATH_SEP;
        if (count( $paths ) > 0 && $paths[0] == 'classes' && is_dir( $pathClasses )) {
            $fsFile = $pathClasses . $paths[1];
            if (count( $paths ) == 2 && file_exists( $fsFile )) {
                $fp = fopen( $fsFile, "w" );
                if (is_resource( $fp ) && is_resource( $options["stream"] )) {
                    while (! feof( $options["stream"] )) {
                        fwrite( $fp, fread( $options["stream"], 4096 ) );
                    }
                    fclose( $fp );
                    fclose( $options["stream"] );
                }
                return "201 Created " . $fsFile;
            }
        }

        $pathProcesses = PATH_DB . SYS_SYS . PATH_SEP;
        if (count( $paths ) > 0 && $paths[0] == 'processes' && is_dir( $pathProcesses )) {
            if ($paths[2] == 'xmlforms') {
                $pathTemplates = $pathProcesses . 'xmlForms' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathTemplates . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $fp = fopen( $fsFile, "w" );
                    if (is_resource( $fp ) && is_resource( $options["stream"] )) {
                        while (! feof( $options["stream"] )) {
                            fwrite( $fp, fread( $options["stream"], 4096 ) );
                        }
                        fclose( $fp );
                        fclose( $options["stream"] );
                    }
                    return "201 Created " . $fsFile;
                }
            }

            if ($paths[2] == 'mailTemplates') {
                $pathTemplates = $pathProcesses . 'mailTemplates' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathTemplates . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $fp = fopen( $fsFile, "w" );
                    if (is_resource( $fp ) && is_resource( $options["stream"] )) {
                        while (! feof( $options["stream"] )) {
                            fwrite( $fp, fread( $options["stream"], 4096 ) );
                        }
                        fclose( $fp );
                        fclose( $options["stream"] );
                    }
                    return "201 Created " . $fsFile;
                }
            }

            if ($paths[2] == 'public_html') {
                $pathPublic = $pathProcesses . 'public' . PATH_SEP . $paths[1] . PATH_SEP;
                $fsFile = $pathPublic . $paths[3];
                if (count( $paths ) == 4 && file_exists( $fsFile )) {
                    $fp = fopen( $fsFile, "w" );
                    if (is_resource( $fp ) && is_resource( $options["stream"] )) {
                        while (! feof( $options["stream"] )) {
                            fwrite( $fp, fread( $options["stream"], 4096 ) );
                        }
                        fclose( $fp );
                        fclose( $options["stream"] );
                    }
                    return "201 Created " . $fsFile;
                }
            }

        }

        return "409 Conflict";

    }

    /**
     * MKCOL method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function MKCOL ($options)
    {
        $path = $this->base . $options["path"];
        $parent = dirname( $path );
        $name = basename( $path );

        if (! file_exists( $parent )) {
            return "409 Conflict";
        }

        if (! is_dir( $parent )) {
            return "403 Forbidden";
        }

        if (file_exists( $parent . "/" . $name )) {
            return "405 Method not allowed";
        }

        if (! empty( $_SERVER["CONTENT_LENGTH"] )) { // no body parsing yet
            return "415 Unsupported media type";
        }

        $stat = mkdir( $parent . "/" . $name, 0777 );
        if (! $stat) {
            return "403 Forbidden";
        }

        return ("201 Created");
    }

    /**
     * DELETE method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function DELETE ($options)
    {
        $path = $this->base . "/" . $options["path"];

        if (! file_exists( $path )) {
            return "404 Not found";
        }

        if (is_dir( $path )) {
            $query = "DELETE FROM properties WHERE path LIKE '" . $this->_slashify( $options["path"] ) . "%'";
            mysql_query( $query );
            System::rm( "-rf $path" );
        } else {
            unlink( $path );
        }
        $query = "DELETE FROM properties WHERE path = '$options[path]'";
        mysql_query( $query );

        return "204 No Content";
    }

    /**
     * MOVE method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function MOVE ($options)
    {
        return "423 Locked";
        //return $this->COPY($options, true);
    }

    /**
     * COPY method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function COPY ($options, $del = false)
    {
        // TODO Property updates still broken (Litmus should detect this?)


        if (! empty( $_SERVER["CONTENT_LENGTH"] )) { // no body parsing yet
            return "415 Unsupported media type";
        }

        // no copying to different WebDAV Servers yet
        if (isset( $options["dest_url"] )) {
            return "502 bad gateway";
        }

        $source = $this->base . $options["path"];
        if (! file_exists( $source ))
            return "404 Not found";

        $dest = $this->base . $options["dest"];

        $new = ! file_exists( $dest );
        $existing_col = false;

        if (! $new) {
            if ($del && is_dir( $dest )) {
                if (! $options["overwrite"]) {
                    return "412 precondition failed";
                }
                $dest .= basename( $source );
                if (file_exists( $dest )) {
                    $options["dest"] .= basename( $source );
                } else {
                    $new = true;
                    $existing_col = true;
                }
            }
        }

        if (! $new) {
            if ($options["overwrite"]) {
                $stat = $this->DELETE( array ("path" => $options["dest"]
                ) );
                if (($stat{0} != "2") && (substr( $stat, 0, 3 ) != "404")) {
                    return $stat;
                }
            } else {
                return "412 precondition failed";
            }
        }

        if (is_dir( $source ) && ($options["depth"] != "infinity")) {
            // RFC 2518 Section 9.2, last paragraph
            return "400 Bad request";
        }

        if ($del) {
            if (! rename( $source, $dest )) {
                return "500 Internal server error";
            }
            $destpath = $this->_unslashify( $options["dest"] );
            if (is_dir( $source )) {
                $query = "UPDATE properties
                  SET path = REPLACE(path, '" . $options["path"] . "', '" . $destpath . "')
                  WHERE path LIKE '" . $this->_slashify( $options["path"] ) . "%'";
                mysql_query( $query );
            }

            $query = "UPDATE properties
                SET path = '" . $destpath . "'
                WHERE path = '" . $options["path"] . "'";
            mysql_query( $query );
        } else {
            if (is_dir( $source )) {
                $files = System::find( $source );
                $files = array_reverse( $files );
            } else {
                $files = array ($source
                );
            }

            if (! is_array( $files ) || empty( $files )) {
                return "500 Internal server error";
            }

            foreach ($files as $file) {
                if (is_dir( $file )) {
                    $file = $this->_slashify( $file );
                }

                $destfile = str_replace( $source, $dest, $file );

                if (is_dir( $file )) {
                    if (! is_dir( $destfile )) {
                        // TODO "mkdir -p" here? (only natively supported by PHP 5)
                        if (! mkdir( $destfile )) {
                            return "409 Conflict";
                        }
                    } else {
                        error_log( "existing dir '$destfile'" );
                    }
                } else {
                    if (! copy( $file, $destfile )) {
                        return "409 Conflict";
                    }
                }
            }

            $query = "INSERT INTO properties SELECT ... FROM properties WHERE path = '" . $options['path'] . "'";
        }

        return ($new && ! $existing_col) ? "201 Created" : "204 No Content";
    }

    /**
     * PROPPATCH method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function PROPPATCH (&$options)
    {
        global $prefs, $tab;

        $msg = "";

        $path = $options["path"];

        $dir = dirname( $path ) . "/";
        $base = basename( $path );

        foreach ($options["props"] as $key => $prop) {
            if ($prop["ns"] == "DAV:") {
                $options["props"][$key]['status'] = "403 Forbidden";
            } else {
                if (isset( $prop["val"] )) {
                    $query = "REPLACE INTO properties SET path = '$options[path]', name = '$prop[name]', ns= '$prop[ns]', value = '$prop[val]'";
                    error_log( $query );
                } else {
                    $query = "DELETE FROM properties WHERE path = '$options[path]' AND name = '$prop[name]' AND ns = '$prop[ns]'";
                }
                mysql_query( $query );
            }
        }
        return "";
    }

    /**
     * LOCK method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function LOCK (&$options)
    {
        if (isset( $options["update"] )) { // Lock Update
            $query = "UPDATE locks SET expires = " . (time() + 300);
            mysql_query( $query );

            if (mysql_affected_rows()) {
                $options["timeout"] = 300; // 5min hardcoded
                return true;
            } else {
                return false;
            }
        }

        $options["timeout"] = time() + 300; // 5min. hardcoded


        $query = "INSERT INTO locks
                SET token   = '$options[locktoken]'
                  , path    = '$options[path]'
                  , owner   = '$options[owner]'
                  , expires = '$options[timeout]'
                  , exclusivelock  = " . ($options['scope'] === "exclusive" ? "1" : "0");
        mysql_query( $query );

        return mysql_affected_rows() ? "200 OK" : "409 Conflict";
    }

    /**
     * UNLOCK method handler
     *
     * @param array general parameter passing array
     * @return bool true on success
     */
    function UNLOCK (&$options)
    {
        $query = "DELETE FROM locks
              WHERE path = '$options[path]'
              AND token  = '$options[token]'";
        mysql_query( $query );

        return mysql_affected_rows() ? "204 No Content" : "409 Conflict";
    }

    /**
     * checkLock() helper
     *
     * @param string resource path to check for locks
     * @return bool true on success
     */
    function checkLock ($path)
    {
        $result = false;

        $query = "SELECT owner, token, expires, exclusivelock
              FROM locks
            WHERE path = '$path' ";
        $res = mysql_query( $query );

        if ($res) {
            $row = mysql_fetch_array( $res );
            mysql_free_result( $res );

            if ($row) {
                $result = array ("type" => "write","scope" => $row["exclusivelock"] ? "exclusive" : "shared","depth" => 0,"owner" => $row['owner'],"token" => $row['token'],"expires" => $row['expires']
                );
            }
        }

        return $result;
    }

    /**
     * create database tables for property and lock storage
     *
     * @param void
     * @return bool true on success
     */
    function create_database ()
    {
        // TODO
        return false;
    }

}

?>
