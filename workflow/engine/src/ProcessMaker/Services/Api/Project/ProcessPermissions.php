<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ProcessPermissions Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class ProcessPermissions extends Api
{
    /**
     * @param string $prj_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:prj_uid/process-permissions
     */
    public function doGetProcessPermissions($prj_uid)
    {
        try {
            $processPermissions = new \ProcessMaker\BusinessModel\ProcessPermissions();
            $response = $processPermissions->getProcessPermissions($prj_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $ob_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:prj_uid/process-permission/:ob_uid
     */
    public function doGetProcessPermission($prj_uid, $ob_uid)
    {
        try {
            $processPermissions = new \ProcessMaker\BusinessModel\ProcessPermissions();
            $response = $processPermissions->getProcessPermissions($prj_uid, $ob_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $usr_uid {@from body} {@min 1} {@max 32}
     * @param string $op_user_relation {@from body} {@choice 1,2}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY,SUMMARY_FORM}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE,RESEND}
     * @param string $tas_uid {@from body}
     * @param string $op_task_source {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST /:prj_uid/process-permission/
     * @status 201
     */
    public function doPostProcessPermission(
        $prj_uid,
        $request_data,
        $usr_uid,
        $op_user_relation,
        $op_case_status,
        $op_participate,
        $op_obj_type,
        $op_action,
        $tas_uid = '',
        $op_task_source = '',
        $dynaforms = '',
        $inputs = '',
        $outputs = ''
    ) {
        try {
            $hiddenFields = array('task_target', 'group_user', 'task_source',
                'object_type', 'object', 'participated', 'action'
            );
            $request_data['pro_uid'] = $prj_uid;
            $processPermissions = new \ProcessMaker\BusinessModel\ProcessPermissions();
            $response = $processPermissions->saveProcessPermission($request_data);
            foreach ($response as $key => $eventData) {
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
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $ob_uid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $usr_uid {@from body} {@min 1} {@max 32}
     * @param string $op_user_relation {@from body} {@choice 1,2}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY,SUMMARY_FORM}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE,RESEND}
     * @param string $tas_uid {@from body}
     * @param string $op_task_source {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url PUT /:prj_uid/process-permission/:ob_uid
     */
    public function doPutProcessPermission(
        $prj_uid,
        $ob_uid,
        $request_data,
        $usr_uid,
        $op_user_relation,
        $op_case_status,
        $op_participate,
        $op_obj_type,
        $op_action,
        $tas_uid = '',
        $op_task_source = '',
        $dynaforms = '',
        $inputs = '',
        $outputs = ''
    ) {
        try {
            $request_data['pro_uid'] = $prj_uid;
            $processPermissions = new \ProcessMaker\BusinessModel\ProcessPermissions();
            $response = $processPermissions->saveProcessPermission($request_data, $ob_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $ob_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     *
     * @url DELETE /:prj_uid/process-permission/:ob_uid
     */
    public function doDeleteProcessPermission($prj_uid, $ob_uid)
    {
        try {
            $processPermissions = new \ProcessMaker\BusinessModel\ProcessPermissions();
            $response = $processPermissions->deleteProcessPermission($ob_uid, $prj_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

