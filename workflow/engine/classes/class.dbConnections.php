<?php
/**
 * Author: Erik Amaru Ortiz <erik@colosa.com>
 * Description:This is a class for load all additional connections; if exist in a particular proccess
 * Date: 15-05-2008
 *
 *
 * class.dbConnections.php
 *
 * Email bugs/suggestions to erik@colosa.com
 */

require_once 'model/DbSource.php';
require_once 'model/Content.php';

/**
 * dbConnections
 *
 *
 * @copyright 2008 Colosa
 * @package workflow.engine.classes
 *
 */
class dbConnections
{
    private $PRO_UID;
    public $connections;
    private $types;

    /*errors handle*/
    private $errno;
    private $errstr;

    private $encodesList;

    /**
     * construct of dbConnections
     *
     * @param string $pPRO_UID
     * @return void
     */
    public function __construct ($pPRO_UID = null)
    {
        $this->errno = 0;
        $this->errstr = "";
        $this->PRO_UID = $pPRO_UID;

        $this->getAllConnections();
    }

    /**
     * getAllConnections
     *
     * @return Array $connections
     */
    public function getAllConnections ()
    {
        if (isset( $this->PRO_UID )) {
            $oDBSource = new DbSource();
            $oContent = new Content();
            $connections = Array ();
            $types = Array ();
            $this->have_any_connectios = false;

            $c = new Criteria();

            $c->clearSelectColumns();
            $c->addSelectColumn( DbSourcePeer::DBS_UID );
            $c->addSelectColumn( DbSourcePeer::PRO_UID );
            $c->addSelectColumn( DbSourcePeer::DBS_TYPE );
            $c->addSelectColumn( DbSourcePeer::DBS_SERVER );
            $c->addSelectColumn( DbSourcePeer::DBS_DATABASE_NAME );
            $c->addSelectColumn( DbSourcePeer::DBS_USERNAME );
            $c->addSelectColumn( DbSourcePeer::DBS_PASSWORD );
            $c->addSelectColumn( DbSourcePeer::DBS_PORT );
            $c->addSelectColumn( DbSourcePeer::DBS_ENCODE );
            $c->addSelectColumn( ContentPeer::CON_VALUE );

            $c->add( DbSourcePeer::PRO_UID, $this->PRO_UID );
            $c->add( ContentPeer::CON_CATEGORY, 'DBS_DESCRIPTION' );
            $c->addJoin( DbSourcePeer::DBS_UID, ContentPeer::CON_ID );

            $result = DbSourcePeer::doSelectRS( $c );
            $result->next();
            $row = $result->getRow();

            while ($row = $result->getRow()) {
                $connections[] = Array ('DBS_UID' => $row[0],'DBS_TYPE' => $row[2],'DBS_SERVER' => $row[3],'DBS_DATABASE_NAME' => $row[4],'DBS_USERNAME' => $row[5],'DBS_PASSWORD' => $row[6],'DBS_PORT' => $row[7],'DBS_ENCODE' => $row[8],'CON_VALUE' => $row[9]
                );
                $result->next();
            }
            if (! in_array( $row[2], $types )) {
                $types[] = $row[2];
            }
            $this->connections = $connections;
            return $connections;
        }
    }

    /**
     * getConnections
     *
     * @param string $pType
     * @return Array $connections
     */
    public function getConnections ($pType)
    {
        $connections = Array ();
        foreach ($this->connections as $c) {
            if (trim( $pType ) == trim( $c['DBS_TYPE'] )) {
                $connections[] = $c;
            }
        }
        if (count( $connections ) > 0) {
            return $connections;
        } else {
            return false;
        }

    }

    /**
     * getConnectionsProUid
     *
     * @param string $pType
     * @return Array $connections
     */
    public function getConnectionsProUid ($pProUid)
    {
        $connections = Array ();
        $c = new Criteria();
        $c->clearSelectColumns();

        $c->addSelectColumn( DbSourcePeer::DBS_UID );
        $c->addSelectColumn( DbSourcePeer::PRO_UID );
        $c->addSelectColumn( DbSourcePeer::DBS_TYPE );
        $c->addSelectColumn( DbSourcePeer::DBS_SERVER );
        $c->addSelectColumn( DbSourcePeer::DBS_DATABASE_NAME );

        $result = DbSourcePeer::doSelectRS( $c );
        $result->next();
        $row = $result->getRow();
        while ($row = $result->getRow()) {
            if (trim( $pProUid ) == trim( $row[1] )) {
                $connections[] = Array ('DBS_UID' => $row[0],'DBS_NAME' => '[' . $row[3] . '] ' . $row[2] . ': ' . $row[4]
                );
            }
            $result->next();
        }

        if (count( $connections ) > 0) {
            return $connections;
        } else {
            return Array ();
        }

    }

    /**
     * loadAdditionalConnections
     *
     * @return void
     */
    public function loadAdditionalConnections ()
    {
        PROPEL::Init( PATH_METHODS . 'dbConnections/genericDbConnections.php' );
    }

    /**
     * getDbServicesAvailables
     *
     * @return array $servicesAvailables
     */
    public function getDbServicesAvailables ()
    {
        $servicesAvailables = Array ();

        $dbServices = Array ('mysql' => Array ('id' => 'mysql','command' => 'mysql_connect','name' => 'MySql'
        ),'pgsql' => Array ('id' => 'pgsql','command' => 'pg_connect','name' => 'PostgreSql'
        ),'mssql' => Array ('id' => 'mssql','command' => 'mssql_connect','name' => 'Microsoft SQL Server'
        ),'oracle' => Array ('id' => 'oracle','command' => 'oci_connect','name' => 'Oracle'
        )
        );
        /*,
      'informix'=> Array(
                'id'        => 'informix',
                'command'   => 'ifx_connect',
                'name'      => 'Informix'
            ),
      'sqlite' => Array(
                'id'        => 'sqlite',
                'command'   => 'sqlite_open',
                'name'      => 'SQLite'
            )
    */

        foreach ($dbServices as $service) {
            if (@function_exists( $service['command'] )) {
                $servicesAvailables[] = $service;
            }
        }
        return $servicesAvailables;
    }

    /**
     * showMsg
     *
     * @return void
     */
    public function showMsg ()
    {
        if ($this->errno != 0) {
            $msg = "
        <center>
        <fieldset style='width:90%'><legend>Class NET</legend>
          <div align=left>
          <font color='red'>
            <b>NET::ERROR NO -> $this->errno<br/>
            NET::ERROR MSG -> $this->errstr</b>
          </font>
          </div>
        </fieldset>
        <center>";
            print ($msg) ;
        }
    }

    /**
     * getEncondeList
     *
     * @param string $engine
     * @return $this->ordx($this->encodesList);
     */
    public function getEncondeList ($engine = '')
    {
        switch ($engine) {
            default:
            case 'mysql':
                $encodes = Array (Array ('big5','big5 - Big5 Traditional Chinese'
                ),Array ('dec8','dec8 - DEC West European'
                ),Array ('cp850','cp850 - DOS West European'
                ),Array ('hp8','hp8 - HP West European'
                ),Array ('koi8r','koi8r - KOI8-R Relcom Russian'
                ),Array ('latin1','latin1 - cp1252 West European'
                ),Array ('latin2','latin2 - ISO 8859-2 Central European'
                ),Array ('swe7','swe7 - 7bit Swedish'
                ),Array ('ascii','ascii - US ASCII'
                ),Array ('ujis','ujis - EUC-JP Japanese'
                ),Array ('sjis','sjis - Shift-JIS Japanese'
                ),Array ('hebrew','hebrew - ISO 8859-8 Hebrew'
                ),Array ('tis620','tis620 - TIS620 Thai'
                ),Array ('euckr','euckr - EUC-KR Korean'
                ),Array ('koi8u','koi8u - KOI8-U Ukrainian'
                ),Array ('gb2312','gb2312 - GB2312 Simplified Chinese'
                ),Array ('greek','greek - ISO 8859-7 Greek'
                ),Array ('cp1250','cp1250 - Windows Central European'
                ),Array ('gbk','gbk - GBK Simplified Chinese'
                ),Array ('latin5','latin5 - ISO 8859-9 Turkish'
                ),Array ('armscii8','armscii8 - ARMSCII-8 Armenian'
                ),Array ('utf8','utf8 - UTF-8 Unicode'
                ),Array ('ucs2','ucs2 - UCS-2 Unicode'
                ),Array ('cp866','cp866 - DOS Russian'
                ),Array ('keybcs2','keybcs2 - DOS Kamenicky Czech-Slovak'
                ),Array ('macce','macce - Mac Central European'
                ),Array ('macroman','macroman - Mac West European'
                ),Array ('cp852','cp852 - DOS Central European'
                ),Array ('latin7','atin7 - ISO 8859-13 Baltic'
                ),Array ('cp1251','cp1251 - Windows Cyrillic'
                ),Array ('cp1256','cp1256  - Windows Arabic'
                ),Array ('cp1257','cp1257  - Windows Baltic'
                ),Array ('binary','binary  - Binary pseudo charset'
                ),Array ('geostd8','geostd8 - GEOSTD8 Georgian'
                ),Array ('cp932','cp932] - SJIS for Windows Japanese'
                ),Array ('eucjpms','eucjpms - UJIS for Windows Japanese'
                )
                );

                break;
            case 'pgsql':
                $encodes = Array (Array ("BIG5","BIG5"
                ),Array ("EUC_CN","EUC_CN"
                ),Array ("EUC_JP","EUC_JP"
                ),Array ("EUC_KR","EUC_KR"
                ),Array ("EUC_TW","EUC_TW"
                ),Array ("GB18030","GB18030"
                ),Array ("GBK","GBK"
                ),Array ("ISO_8859_5","ISO_8859_5"
                ),Array ("ISO_8859_6","ISO_8859_6"
                ),Array ("ISO_8859_7","ISO_8859_7"
                ),Array ("ISO_8859_8","ISO_8859_8"
                ),Array ("JOHAB","JOHAB"
                ),Array ("KOI8","KOI8"
                ),Array ("selected","LATIN1"
                ),Array ("LATIN2","LATIN2"
                ),Array ("LATIN3","LATIN3"
                ),Array ("LATIN4","LATIN4"
                ),Array ("LATIN5","LATIN5"
                ),Array ("LATIN6","LATIN6"
                ),Array ("LATIN7","LATIN7"
                ),Array ("LATIN8","LATIN8"
                ),Array ("LATIN9","LATIN9"
                ),Array ("LATIN10","LATIN10"
                ),Array ("SJIS","SJIS"
                ),Array ("SQL_ASCII","SQL_ASCII"
                ),Array ("UHC","UHC"
                ),Array ("UTF8","UTF8"
                ),Array ("WIN866","WIN866"
                ),Array ("WIN874","WIN874"
                ),Array ("WIN1250","WIN1250"
                ),Array ("WIN1251","WIN1251"
                ),Array ("WIN1252","WIN1252"
                ),Array ("WIN1256","WIN1256"
                ),Array ("WIN1258","WIN1258"
                )
                );
                break;
            case 'mssql':
                $encodes = Array (Array ('utf8','utf8 - UTF-8 Unicode'
                )
                );
                break;
            case 'oracle':
                $encodes = Array ();
                break;
        }

        $this->encodesList = $encodes;
        return $this->ordx( $this->encodesList );
    }

    /**
     * getErrno
     *
     * @return integer $errno
     */
    public function getErrno ()
    {
        return $this->errno;
    }

    /**
     * getErrmsg
     *
     * @return string errstr
     */
    public function getErrmsg ()
    {
        return $this->errstr;
    }

    /**
     * getErrmsg
     *
     * @param array $m
     * @return array $aRet
     */
    public function ordx ($m)
    {
        $aTmp = Array ();
        $aRet = Array ();
        for ($i = 0; $i < count( $m ); $i ++) {
            array_push( $aTmp, $m[$i][0] . '|' . $m[$i][1] );
        }
        usort( $aTmp, "strnatcasecmp" );

        for ($i = 0; $i < count( $aTmp ); $i ++) {
            $x = explode( '|', $aTmp[$i] );
            array_push( $aRet, Array ($x[0],$x[1]
            ) );
        }
        return $aRet;
    }

    /**
     * Function encryptThepassw
     *
     * @author krlos Pacha C. <carlos@colosa.com>
     * @access public
     * @param string proUid
     * @return void
     */
    public function encryptThepassw ($proUid)
    {
        $oDBSource = new DbSource();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn( DbSourcePeer::DBS_UID );
        $c->addSelectColumn( DbSourcePeer::DBS_DATABASE_NAME );
        $c->addSelectColumn( DbSourcePeer::DBS_PASSWORD );
        $c->add( DbSourcePeer::PRO_UID, $proUid );
        $result = DbSourcePeer::doSelectRS( $c );
        $result->next();
        $row = $result->getRow();
        while ($row = $result->getRow()) {
            if ($row[2] != '') {
                $aPass = explode( '_', $row[2] );
                if (count( $aPass ) == 1) {
                    $passEncrypt = G::encrypt( $row[2], $row[1] );
                    $passEncrypt .= "_2NnV3ujj3w";
                    $c2 = new Criteria( 'workflow' );
                    $c2->add( DbSourcePeer::DBS_PASSWORD, $passEncrypt );
                    $c3 = new Criteria( 'workflow' );
                    $c3->add( DbSourcePeer::DBS_UID, $row[0] );
                    BasePeer::doUpdate( $c3, $c2, Propel::getConnection( 'workflow' ) );
                }
            }
            $result->next();
        }
        return 1;
    }

    /**
     * Function getPassWithoutEncrypt
     *
     * @author krlos Pacha C. <carlos@colosa.com>
     * @access public
     * @param string passw
     * @return string
     */
    public function getPassWithoutEncrypt ($aInfoCon)
    {
        $passw = '';
        if ($aInfoCon['DBS_PASSWORD'] != '') {
            $aPassw = explode( '_', $aInfoCon['DBS_PASSWORD'] );
            $passw = $aPassw[0];

            $flagTns = ($aInfoCon["DBS_TYPE"] == "oracle" && $aInfoCon["DBS_CONNECTION_TYPE"] == "TNS")? 1 : 0;

            if (sizeof($aPassw) > 1 && $flagTns == 0) {
                $passw = ($passw == "none")? "" : G::decrypt($passw, $aInfoCon["DBS_DATABASE_NAME"]);
            } else {
                $passw = ($passw == "none")? "" : G::decrypt($passw, $aInfoCon["DBS_TNS"]);
            }
        }
        return $passw;
    }
}
