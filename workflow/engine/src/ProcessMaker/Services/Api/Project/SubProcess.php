<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Subprocess Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Subprocess extends Api
{
    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $tas_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:prj_uid/subprocess/:tas_uid
     */
    public function doGetSubprocesss($prj_uid, $tas_uid)
    {
        try {
            $hiddenFields = array('spr_uid', 'spr_pro_parent', 'spr_tas_parent');
            $oSubProcess = new \ProcessMaker\BusinessModel\Subprocess();
            $response = $oSubProcess->getSubprocesss($prj_uid, $tas_uid);
            foreach ($response as $key => $value) {
                if (in_array($key, $hiddenFields)) {
                    unset($response[$key]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update subprocess.
     *
     * @url PUT /:prj_uid/subprocess/:tas_uid
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $tas_uid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @return void
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutSubprocess($prj_uid, $tas_uid, $request_data)
    {
        try {
            $oSubProcess = new \ProcessMaker\BusinessModel\Subprocess();
            $oSubProcess->putSubprocesss($prj_uid, $tas_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

