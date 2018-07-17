<?php
namespace ProcessMaker\Services\Api\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Cases\Variable Api Controller
 *
 * @protected
 */
class Variable extends Api
{
    private $variable;

    /**
     * Constructor of the class
     *
     * @return  void
     */
    public function __construct()
    {
        try {
            $this->variable = new \ProcessMaker\BusinessModel\Cases\Variable();
            $this->variable->setRunningWorkflow(false);
            $this->variable->setArrayVariableNameForException([
                '$applicationUid' => 'app_uid',
                '$delIndex'       => 'del_index',
                '$variableName'   => 'var_name',
                '$filter'   => 'filter',
                '$start'    => 'start',
                '$limit'    => 'limit',
                '$arrayKey' => 'keys'
            ]);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:app_uid/:del_index/variable/:var_name/paged
     * @url GET /:app_uid/:del_index/variable/:var_name
     *
     * @param string $app_uid   {@min 32}{@max 32}
     * @param int    $del_index {@min 1}
     * @param string $var_name
     */
    public function doGetVariable(
        $app_uid,
        $del_index,
        $var_name,
        $filter = null,
        $lfilter = null,
        $rfilter = null,
        $start = null,
        $limit = null
    ) {
        try {
            if (preg_match("/^.*\/paged.*$/", $this->restler->url)) {
                $arrayFilterData = [
                    'filter' => (!is_null($filter))?
                        $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                    'filterOption' => (!is_null($filter))?
                        '' : ((!is_null($lfilter))? 'LEFT' : ((!is_null($rfilter))? 'RIGHT' : ''))
                ];

                $response = $this->variable->getVariableByNamePaged(
                    $app_uid, $del_index, $var_name, $arrayFilterData, $start, $limit
                );
            } else {
                $response = $this->variable->getVariableByName($app_uid, $del_index, $var_name);
            }

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Create a variable in a case, meaning the variable is instantiated in the case.
     * 
     * @url POST /:app_uid/:del_index/variable/:var_name
     * @status 201
     * 
     * @param string $app_uid      {@min 32}{@max 32}
     * @param int    $del_index    {@min 1}
     * @param string $var_name
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostVariable($app_uid, $del_index, $var_name, array $request_data)
    {
        try {
            $response = $this->variable->create($app_uid, $del_index, $var_name, $request_data);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update variable.
     *
     * @url PUT /:app_uid/:del_index/variable/:var_name
     * @status 204
     *
     * @param string $app_uid      {@min 32}{@max 32}
     * @param int    $del_index    {@min 1}
     * @param string $var_name
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutVariable($app_uid, $del_index, $var_name, array $request_data)
    {
        try {
            $response = $this->variable->update($app_uid, $del_index, $var_name, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:app_uid/:del_index/variable/:var_name
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     *
     * @param string $app_uid   {@min 32}{@max 32}
     * @param int    $del_index {@min 1}
     * @param string $var_name
     */
    public function doDeleteVariable($app_uid, $del_index, $var_name, $keys = null)
    {
        try {
            $arrayKey = null;

            if (!is_null($keys)) {
                $keys = trim($keys, ' ,');
                $arrayKey = explode(',', $keys);

                if ($keys == '' || !is_array($arrayKey)) {
                    throw new \Exception(\G::LoadTranslation('ID_INVALID_VALUE', ['keys']));
                }
            }

            $response = $this->variable->delete($app_uid, $del_index, $var_name, $arrayKey);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

