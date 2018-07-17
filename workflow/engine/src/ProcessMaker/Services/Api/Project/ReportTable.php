<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ReportTable Api Controller
 *
 * @author Brayan Pereyra <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class ReportTable extends Api
{
    /**
     * @param string $prj_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:prj_uid/report-tables
     */
    public function doGetReportTables($prj_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->getTables($prj_uid, true);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid
     */
    public function doGetReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->getTable($rep_uid, $prj_uid, true);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid/populate
     */
    public function doGetPopulateReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->generateDataReport($prj_uid, $rep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid/data
     */
    public function doGetReportTableData($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->getTableData($rep_uid, $prj_uid, null, true);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Creates a new Report Table.
     * 
     * @url POST /:prj_uid/report-table
     * @status 201
     * 
     * @param string $prj_uid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $rep_tab_name {@from body}
     * @param string $rep_tab_dsc {@from body}
     * @param string $rep_tab_connection {@from body}
     * @param string $rep_tab_type {@from body} {@choice NORMAL,GRID}
     * @param string $rep_tab_grid {@from body}
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostReportTable(
        $prj_uid,
        $request_data,
        $rep_tab_name,
        $rep_tab_dsc,
        $rep_tab_connection,
        $rep_tab_type,
        $rep_tab_grid = ''
    ) {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->saveTable($request_data, $prj_uid, true);
            if (isset($response['pro_uid'])) {
                unset($response['pro_uid']);
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update report table.
     *
     * @url PUT /:prj_uid/report-table/:rep_uid
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $rep_tab_dsc {@from body}
     *
     * @return void
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutReportTable(
        $prj_uid,
        $rep_uid,
        $request_data,
        $rep_tab_dsc = ''
    ) {
        try {
            $request_data['rep_uid'] = $rep_uid;
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->updateTable($request_data, $prj_uid, true);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/report-table/:rep_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return void
     *
     */
    public function doDeleteReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \ProcessMaker\BusinessModel\Table();
            $response = $oReportTable->deleteTable($rep_uid, $prj_uid, true);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

