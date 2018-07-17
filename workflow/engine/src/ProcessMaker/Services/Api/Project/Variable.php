<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
/**
 * Project\Variable Api Controller
 *
 * @protected
 */
class Variable extends Api
{
    /**
     * @url GET /:prj_uid/process-variables
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetVariables($prj_uid)
    {
        try {
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $response = $variable->getVariables($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/process-variable/:var_uid
     *
     * @param string $var_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetVariable($var_uid, $prj_uid)
    {
        try {
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $response = $variable->getVariable($prj_uid, $var_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create a process variable.
     * 
     * @url POST /:prj_uid/process-variable
     * @status 201
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostVariable($prj_uid, $request_data)
    {
        try {
            $request_data = (array)($request_data);
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $arrayData = $variable->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update variable.
     *
     * @url PUT /:prj_uid/process-variable/:var_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $var_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutVariable($prj_uid, $var_uid, array $request_data)
    {
        try {
            $request_data = (array)($request_data);
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $variable->update($prj_uid, $var_uid, $request_data);

        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/process-variable/:var_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $var_uid {@min 32}{@max 32}
     */
    public function doDeleteVariable($prj_uid, $var_uid)
    {
        try {
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $variable->delete($prj_uid, $var_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Executes an SQL query of a dependent field, such as a dropdown box, checkgroup 
     * or radiogroup, that uses an SQL query with one or more dynamic variables 
     * to populate its list of options.
     * 
     * @url POST /:prj_uid/process-variable/:var_name/execute-query
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $var_name
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY, PM_CASES}
     */
    public function doPostVariableExecuteSql($prj_uid, $var_name = '', $request_data = array())
    {
        try {
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $arrayData = ($request_data != null)? $variable->executeSql($prj_uid, $var_name, $request_data) : $variable->executeSql($prj_uid, $var_name);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Gets the options in a suggest box, dropdown box, checkgroup or radiogroup, 
     * which uses an SQL query to populate its list of options (or uses a datasource 
     * which is "array variable" in version 3.0.1.8 or later).
     * 
     * @url POST /:prj_uid/process-variable/:var_name/execute-query-suggest
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $var_name
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY, PM_CASES}
     */
    public function doPostVariableExecuteSqlSuggest($prj_uid, $var_name, $request_data)
    {
        try {
            $variable = new \ProcessMaker\BusinessModel\Variable();

            $arrayData = ($request_data != null)? $variable->executeSqlSuggest($prj_uid, $var_name, $request_data) : $variable->executeSqlSuggest($prj_uid, $var_name);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

