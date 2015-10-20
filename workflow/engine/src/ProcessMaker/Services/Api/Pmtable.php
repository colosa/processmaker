<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Pmtable Api Controller
 *
 * @protected
 */
class Pmtable extends Api
{
    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();

            $usrUid = $this->getUserId();

            if (!$user->checkPermission($usrUid, "PM_SETUP")) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($usrUid)));
            }
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET
     */
    public function doGetPmTables()
    {
        try {
            $oPmTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oPmTable->getTables();
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:pmt_uid
     */
    public function doGetPmTable($pmt_uid)
    {
        try {
            $oPmTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oPmTable->getTable($pmt_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:pmt_uid/data
     */
    public function doGetPmTableData($pmt_uid)
    {
        try {
            $oPmTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oPmTable->getTableData($pmt_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param array $request_data
     * @param string $pmt_tab_name {@from body}
     * @param string $pmt_tab_dsc {@from body}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST
     * @status 201
     */
    public function doPostPmTable(
        $request_data,
        $pmt_tab_name,
        $pmt_tab_dsc = ''
    ) {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->saveTable($request_data);
            if (isset($response['pro_uid'])) {
                unset($response['pro_uid']);
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @param array $request_data
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST /:pmt_uid/data
     * @status 201
     */
    public function doPostPmTableData(
        $pmt_uid,
        $request_data
    ) {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->saveTableData($pmt_uid, $request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @param array $request_data
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:pmt_uid
     */
    public function doPutPmTable(
        $pmt_uid,
        $request_data
    ) {
        try {
            $request_data['pmt_uid'] = $pmt_uid;
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->updateTable($request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @param array $request_data
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:pmt_uid/data
     */
    public function doPutPmTableData(
        $pmt_uid,
        $request_data
    ) {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->updateTableData($pmt_uid, $request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:pmt_uid
     */
    public function doDeletePmTable($pmt_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->deleteTable($pmt_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     * @param string $key1 {@min 1}
     * @param string $value1 {@min 1}
     * @param string $key2
     * @param string $value2
     * @param string $key3
     * @param string $value3
     *
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:pmt_uid/data/:key1/:value1
     * @url DELETE /:pmt_uid/data/:key1/:value1/:key2/:value2
     * @url DELETE /:pmt_uid/data/:key1/:value1/:key2/:value2/:key3/:value3
     */
    public function doDeletePmTableData($pmt_uid, $key1, $value1, $key2 = '', $value2 = '', $key3 = '', $value3 = '')
    {
        try {
            $rows = array($key1 => $value1);
            if ($key2 != '') {
                $rows[$key2] = $value2;
            }
            if ($key3 != '') {
                $rows[$key3] = $value3;
            }
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->deleteTableData($pmt_uid, $rows);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

