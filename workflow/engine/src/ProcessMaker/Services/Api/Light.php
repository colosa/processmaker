<?php

namespace ProcessMaker\Services\Api;

use G;
use ProcessMaker\Project\Adapter;
use ProcessMaker\Services\Api;
use Luracast\Restler\RestException;
use ProcessMaker\BusinessModel\Validator;
use ProcessMaker\Util\DateTime;
use PmDynaform;
use Exception;
use ProcessMaker\BusinessModel\Light as BusinessModelLight;
use RBAC;
use ProcessMaker\BusinessModel\Cases as BusinessModelCases;
use Cases as ClassesCases;
use AppDelegation;
use ProcessMaker\BusinessModel\Lists;
use ProcessMaker\BusinessModel\Task;
use ProcessMaker\BusinessModel\ProcessMap;
use ProcessMaker\BusinessModel\Process;
use Criteria;
use StepPeer;
use stdclass;
use ProcessMaker\BusinessModel\DynaForm as BusinessModelDynaForm;
/**
 *
 * Process Api Controller
 *
 * @protected
 */
class Light extends Api
{

    private $regexNull = '/^null$/i';
    private $arrayFieldIso8601 = [
        // request lists
        'newerThan',
        'oldestthan',
        //return lists
        'date',
        'delegateDate',
        'dueDate',
        'delRiskDate'
    ];

    public function __isAllowed()
    {
        try {
            $methodName = $this->restler->apiMethodInfo->methodName;
            $arrayArgs = $this->restler->apiMethodInfo->arguments;
            switch ($methodName) {
                case 'doIfAlreadyRoute':
                    $applicationUid = $this->parameters[$arrayArgs['app_uid']];
                    $delIndex = $this->parameters[$arrayArgs['cas_index']];
                    $userUid = $this->getUserId();
                    //Check if the user has the case
                    $appDelegation = new AppDelegation();
                    $aCurUser = $appDelegation->getCurrentUsers($applicationUid, $delIndex);
                    if (!empty($aCurUser) && in_array($userUid, $aCurUser)) {
                        return true;
                    }

                    return false;
                    break;
            }

            return false;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            /**
             * The variable $RBAC can be defined as follows:
             *
             * $RBAC = new stdclass();
             * $RBAC->aUserInfo['USER_INFO'] = ["USR_UID" => $this->getUserId()];
             *
             * Please consider removing the use of this variable in model class,
             * or perform a corresponding improvement.
             */
            global $RBAC;
            if (!isset($RBAC)) {

                $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
                $RBAC->sSystem = 'PROCESSMAKER';
                $RBAC->initRBAC();
                $RBAC->loadUserRolePermission($RBAC->sSystem, $this->getUserId());
            }
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get list counters
     * @return array
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /counters
     */
    public function countersCases()
    {
        try {
            $userId = $this->getUserId();

                /*----------------------------------********---------------------------------*/
                $case = new BusinessModelCases();
                $arrayListCounter = $case->getListCounters(
                    $userId,
                    ['to_do', 'draft', 'sent', 'selfservice', 'paused', 'completed', 'cancelled']
                );
            /*----------------------------------********---------------------------------*/

            $result = $this->parserCountersCases($arrayListCounter);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $result;
    }

    public function parserCountersCases($data)
    {
        $structure = array(
            "CASES_INBOX" => "toDo",
            "CASES_DRAFT" => "draft",
            "CASES_CANCELLED" => "cancelled",
            "CASES_SENT" => "participated",
            "CASES_PAUSED" => "paused",
            "CASES_COMPLETED" => "completed",
            "CASES_SELFSERVICE" => "unassigned",
            'to_do' => 'toDo',
            'draft' => 'draft',
            'cancelled' => 'cancelled',
            'sent' => 'participated',
            'paused' => 'paused',
            'completed' => 'completed',
            'selfservice' => 'unassigned'
        );
        $response = array();
        foreach ($data as $key => $counterList) {
            if (isset($structure[$counterList['item']])) {
                $name = $structure[$counterList['item']];
                $response[$name] = $counterList['count'];
            } else {
                if (isset($structure[$key])) {
                    $response[$structure[$key]] = $counterList;
                }
            }
        }

        return $response;
    }

    /**
     * Get list process start
     * @return array
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /start-case
     */
    public function getProcessListStartCase()
    {
        try {
            $oMobile = new BusinessModelLight();
            $processListStartCase = $oMobile->getProcessListStartCase($this->getUserId());
            $startCase = $processListStartCase;
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $startCase;
    }

    /**
     * Get list Case To Do
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /todo
     */
    public function doGetCasesListToDo(
        $start = 0,
        $limit = 10,
        $sort = 'DEL_DELEGATE_DATE',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = '',
        $filter = '',
        $date_from = '',
        $date_to = '',
        $newerThan = '',
        $oldestthan = ''
    ) {
        try {
            if (preg_match($this->regexNull, $newerThan)) {
                return [];
            }

            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged'] = true;
            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $dataList['filter'] = $filter;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['newerThan'] = $newerThan;
            $dataList['oldestthan'] = $oldestthan;

            Validator::throwExceptionIfDataNotMetIso8601Format($dataList, $this->arrayFieldIso8601);
            $dataList = DateTime::convertDataToUtc($dataList, $this->arrayFieldIso8601);

                /*----------------------------------********---------------------------------*/
                $case = new BusinessModelCases();
                $response = $case->getList($dataList);
            /*----------------------------------********---------------------------------*/

            //Complete the list information with some task properties
            $response['data'] = $this->addTaskProperties($response['data']);

            $result = $this->parserDataTodo($response['data']);

            return DateTime::convertUtcToIso8601($result, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function addTaskProperties($data)
    {
        $task = new Task();
        foreach ($data as $key => $value) {
            $valueUpper = array_change_key_case($value, CASE_UPPER);
            list($data[$key]['TAS_OFFLINE'], $data[$key]['TAS_AUTO_ROOT']) = $task->getColumnValues($valueUpper['PRO_UID'],
                $valueUpper['TAS_UID'], array('TAS_OFFLINE', 'TAS_AUTO_ROOT'));
        }

        return $data;

    }

    public function parserDataTodo($data)
    {
        $structure = array(
            'APP_UID' => 'caseId',
            'APP_TITLE' => 'caseTitle',
            'APP_NUMBER' => 'caseNumber',
            'APP_UPDATE_DATE' => 'date',
            'DEL_TASK_DUE_DATE' => 'dueDate',
            'DEL_INDEX' => 'delIndex',
            'DEL_DELEGATE_DATE' => 'delegateDate',
            'DEL_RISK_DATE' => 'delRiskDate',
            'user' => array(
                'USR_UID' => 'userId'
            ),
            'prevUser' => array(
                'PREVIOUS_USR_UID' => 'userId',
                'PREVIOUS_USR_FIRSTNAME' => 'firstName',
                'PREVIOUS_USR_LASTNAME' => 'lastName',
                'PREVIOUS_USR_USERNAME' => 'userName',
            ),
            'process' => array(
                'PRO_UID' => 'processId',
                'APP_PRO_TITLE' => 'name'
            ),
            'task' => array(
                'TAS_UID' => 'taskId',
                'APP_TAS_TITLE' => 'name',
                'TAS_OFFLINE' => 'offlineEnabled',
                'TAS_AUTO_ROOT' => 'autoRoot'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Get list Case Draft
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft
     */
    public function doGetCasesListDraft(
        $start = 0,
        $limit = 10,
        $sort = 'DEL_DELEGATE_DATE',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = '',
        $newerThan = '',
        $oldestthan = ''
    ) {
        try {
            if (preg_match($this->regexNull, $newerThan)) {
                return [];
            }

            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'draft';
            $dataList['paged'] = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $dataList['newerThan'] = $newerThan;
            $dataList['oldestthan'] = $oldestthan;

            Validator::throwExceptionIfDataNotMetIso8601Format($dataList, $this->arrayFieldIso8601);
            $dataList = DateTime::convertDataToUtc($dataList, $this->arrayFieldIso8601);

                /*----------------------------------********---------------------------------*/
                $case = new BusinessModelCases();
                $response = $case->getList($dataList);
            /*----------------------------------********---------------------------------*/

            //Complete the list information with some task properties
            $response['data'] = $this->addTaskProperties($response['data']);

            $result = $this->parserDataDraft($response['data']);

            return DateTime::convertUtcToIso8601($result, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataDraft($data)
    {
        $structure = array(
            'APP_UID' => 'caseId',
            'APP_TITLE' => 'caseTitle',
            'APP_NUMBER' => 'caseNumber',
            'APP_UPDATE_DATE' => 'date',
            'DEL_TASK_DUE_DATE' => 'dueDate',
            'DEL_INDEX' => 'delIndex',
            'DEL_DELEGATE_DATE' => 'delegateDate',
            'user' => array(
                'USR_UID' => 'userId'
            ),
            'currentUser' => array(
                'USR_UID' => 'userId',
                'USR_FIRSTNAME' => 'firstName',
                'USR_LASTNAME' => 'lastName',
                'USR_USERNAME' => 'userName',
            ),
            'process' => array(
                'PRO_UID' => 'processId',
                'APP_PRO_TITLE' => 'name'
            ),
            'task' => array(
                'TAS_UID' => 'taskId',
                'APP_TAS_TITLE' => 'name',
                'TAS_OFFLINE' => 'offlineEnabled',
                'TAS_AUTO_ROOT' => 'autoRoot'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Get list Cases Participated
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated
     */
    public function doGetCasesListParticipated(
        $count = true,
        $paged = true,
        $start = 0,
        $limit = 10,
        $sort = 'DEL_DELEGATE_DATE',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = '',
        $filter = '',
        $date_from = '',
        $date_to = '',
        $newerThan = '',
        $oldestthan = ''
    ) {
        try {
            if (preg_match($this->regexNull, $newerThan)) {
                return [];
            }

            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged'] = $paged;
            $dataList['count'] = $count;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;

            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $dataList['filter'] = $filter;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['newerThan'] = $newerThan;
            $dataList['oldestthan'] = $oldestthan;

            Validator::throwExceptionIfDataNotMetIso8601Format($dataList, $this->arrayFieldIso8601);
            $dataList = DateTime::convertDataToUtc($dataList, $this->arrayFieldIso8601);

                /*----------------------------------********---------------------------------*/
                $case = new BusinessModelCases();
                $response = $case->getList($dataList);
            /*----------------------------------********---------------------------------*/

            $result = $this->parserDataParticipated($response['data']);

            return DateTime::convertUtcToIso8601($result, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataParticipated($data)
    {
        $structure = array(
            'APP_UID' => 'caseId',
            'APP_TITLE' => 'caseTitle',
            'APP_NUMBER' => 'caseNumber',
            'APP_UPDATE_DATE' => 'date',
            'DEL_TASK_DUE_DATE' => 'dueDate',
            'DEL_INDEX' => 'delIndex',
            'DEL_DELEGATE_DATE' => 'delegateDate',
            'currentUser' => array(
                'USR_UID' => 'userId',
                'USR_FIRSTNAME' => 'firstName',
                'USR_LASTNAME' => 'lastName',
                'USR_USERNAME' => 'userName',
            ),
            'prevUser' => array(
                'PREVIOUS_USR_UID' => 'userId',
                'PREVIOUS_USR_FIRSTNAME' => 'firstName',
                'PREVIOUS_USR_LASTNAME' => 'lastName',
                'PREVIOUS_USR_USERNAME' => 'userName',
            ),
            'process' => array(
                'PRO_UID' => 'processId',
                'APP_PRO_TITLE' => 'name'
            ),
            'task' => array(
                'TAS_UID' => 'taskId',
                'APP_TAS_TITLE' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Get list Cases Paused
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused
     */
    public function doGetCasesListPaused(
        $start = 0,
        $limit = 10,
        $sort = 'APP_PAUSED_DATE',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = '',
        $filter = '',
        $date_from = '',
        $date_to = '',
        $newerThan = '',
        $oldestthan = ''
    ) {
        try {
            if (preg_match($this->regexNull, $newerThan)) {
                return [];
            }

            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList['paged'] = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $dataList['filter'] = $filter;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['newerThan'] = $newerThan;
            $dataList['oldestthan'] = $oldestthan;

                /*----------------------------------********---------------------------------*/
                $case = new BusinessModelCases();
                $response = $case->getList($dataList);
            /*----------------------------------********---------------------------------*/

            $result = $this->parserDataParticipated($response['data']);

            return DateTime::convertUtcToIso8601($result, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataPaused($data)
    {
        $structure = array(
            'APP_UID' => 'caseId',
            'APP_TITLE' => 'caseTitle',
            'APP_NUMBER' => 'caseNumber',
            'APP_UPDATE_DATE' => 'date',
            'DEL_TASK_DUE_DATE' => 'dueDate',
            'DEL_INDEX' => 'delIndex',
            'DEL_DELEGATE_DATE' => 'delegateDate',
            'currentUser' => array(
                'USR_UID' => 'userId',
                'DEL_CURRENT_USR_FIRSTNAME' => 'firstName',
                'DEL_CURRENT_USR_LASTNAME' => 'lastName',
                'DEL_CURRENT_USR_USERNAME' => 'userName',
            ),
            'prevUser' => array(
                'DEL_PREVIOUS_USR_UID' => 'userId',
                'DEL_PREVIOUS_USR_FIRSTNAME' => 'firstName',
                'DEL_PREVIOUS_USR_LASTNAME' => 'lastName',
                'DEL_PREVIOUS_USR_USERNAME' => 'userName',
            ),
            'process' => array(
                'PRO_UID' => 'processId',
                'APP_PRO_TITLE' => 'name'
            ),
            'task' => array(
                'TAS_UID' => 'taskId',
                'APP_TAS_TITLE' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Get list Cases Unassigned
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned
     */
    public function doGetCasesListUnassigned(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = '',
        $newerThan = '',
        $oldestthan = ''
    ) {
        try {
            if (preg_match($this->regexNull, $newerThan)) {
                return [];
            }

            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged'] = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $dataList['newerThan'] = $newerThan;
            $dataList['oldestthan'] = $oldestthan;
            Validator::throwExceptionIfDataNotMetIso8601Format($dataList, $this->arrayFieldIso8601);
            $dataList = DateTime::convertDataToUtc($dataList, $this->arrayFieldIso8601);

                /*----------------------------------********---------------------------------*/
                $oCases = new BusinessModelCases();
                $response = $oCases->getList($dataList);
            /*----------------------------------********---------------------------------*/
            $result = $this->parserDataUnassigned($response);

            return DateTime::convertUtcToIso8601($result, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataUnassigned($data)
    {
        $structure = array(
            'app_uid' => 'caseId',
            'app_title' => 'caseTitle',
            'app_number' => 'caseNumber',
            'app_update_date' => 'date',
            'del_task_due_date' => 'dueDate',
            'del_index' => 'delIndex',
            'del_delegate_date' => 'delegateDate',
            'currentUser' => array(
                'usrcr_usr_uid' => 'userId',
                'usrcr_usr_firstname' => 'firstName',
                'usrcr_usr_lastname' => 'lastName',
                'usrcr_usr_username' => 'userName',
            ),
            'prevUser' => array(
                'previous_usr_uid' => 'userId',
                'previous_usr_firstname' => 'firstName',
                'previous_usr_lastname' => 'lastName',
                'previous_usr_username' => 'userName',
            ),
            'process' => array(
                'pro_uid' => 'processId',
                'app_pro_title' => 'name'
            ),
            'task' => array(
                'tas_uid' => 'taskId',
                'app_tas_title' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    public function replaceFields($data, $structure)
    {
        $response = array();
        foreach ($data as $field => $d) {
            $field = preg_quote($field);
            if (is_array($d)) {
                $newData = array();
                foreach ($d as $field => $value) {
                    $field = preg_quote($field);
                    if (
                        preg_match(
                            '/\|(' . $field . ')\|/i',
                            '|' . implode('|', array_keys($structure)) . '|',
                            $arrayMatch
                        ) &&
                        !is_array($structure[$arrayMatch[1]])
                    ) {
                        $newName = $structure[$arrayMatch[1]];
                        $newData[$newName] = is_null($value) ? "" : $value;
                    } else {
                        foreach ($structure as $name => $str) {
                            if (is_array($str) &&
                                preg_match(
                                    '/\|(' . $field . ')\|/i',
                                    '|' . implode('|', array_keys($str)) . '|',
                                    $arrayMatch
                                ) &&
                                !is_array($str[$arrayMatch[1]])
                            ) {
                                $newName = $str[$arrayMatch[1]];
                                $newData[$name][$newName] = is_null($value) ? "" : $value;
                            }
                        }
                    }
                }
                if (count($newData) > 0) {
                    $response[] = $newData;
                }
            } else {
                if (
                    preg_match(
                        '/\|(' . $field . ')\|/i',
                        '|' . implode('|', array_keys($structure)) . '|',
                        $arrayMatch
                    ) &&
                    !is_array($structure[$arrayMatch[1]])
                ) {
                    $newName = $structure[$arrayMatch[1]];
                    $response[$newName] = is_null($d) ? "" : $d;
                } else {
                    foreach ($structure as $name => $str) {
                        if (is_array($str) &&
                            preg_match(
                                '/\|(' . $field . ')\|/i',
                                '|' . implode('|', array_keys($str)) . '|',
                                $arrayMatch
                            ) &&
                            !is_array($str[$arrayMatch[1]])
                        ) {
                            $newName = $str[$arrayMatch[1]];
                            $response[$name][$newName] = is_null($d) ? "" : $d;
                        }
                    }
                }
            }

        }

        return $response;
    }

    /**
     * Delete case
     *
     * @url DELETE /case/:app_uid/delete
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doDeleteCases($app_uid)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new BusinessModelCases();
            $cases->deleteCase($app_uid, $usr_uid);
            $result = array(
                "message" => G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY")
            );
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $result;
    }

    /**
     * Get list History case
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /history/:app_uid
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetCasesListHistory($app_uid)
    {
        try {
            $arrayFieldIso8601 = array('DEL_DELEGATE_DATE', 'DEL_INIT_DATE', 'DEL_FINISH_DATE');
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getCasesListHistory($app_uid);
            $response8601 = DateTime::convertUtcToIso8601($response['flow'], $arrayFieldIso8601);
            $response['flow'] = $this->parserDataHistory($response8601);
            $r = new stdclass();
            $r->data = $response;
            $r->totalCount = count($response['flow']);

            return $r;
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataHistory($data)
    {
        $structure = array(
            //'' => 'caseId',
            //'' => 'caseTitle',
            //'' => 'processName',
            //'' => 'ownerFullName',
            //'flow' => array(
            'TAS_TITLE' => 'taskName',
            //'' => 'userId',
            'USR_NAME' => 'userFullName',
            'APP_TYPE' => 'flowStatus', // is null default Router in FE
            'DEL_DELEGATE_DATE' => 'dueDate',
            //)
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Get Already Route
     *
     * @access protected
     * @class  AccessControl {@className \ProcessMaker\Services\Api\Light}
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $cas_index
     *
     * @status 204
     * @url GET /case/:app_uid/:cas_index
     */
    public function doIfAlreadyRoute($app_uid, $cas_index)
    {
        try {
            $oAppDelegate = new AppDelegation();
            $alreadyRouted = $oAppDelegate->alreadyRouted($app_uid, $cas_index);
            if ($alreadyRouted) {
                throw (new RestException(Api::STAT_APP_EXCEPTION,
                    G::LoadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED')));
            }
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     *
     * @url GET /project/:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));
            $_SESSION['PROCESS'] = $prj_uid;
            $response = $process->getDynaForms($prj_uid);
            $result = $this->parserDataDynaForm($response);

            $pmDynaForm = new PmDynaform();
            foreach ($result as $k => $form) {
                $result[$k]['formContent'] = (isset($form['formContent']) && $form['formContent'] != null) ? json_decode($form['formContent']) : "";
                $pmDynaForm->jsonr($result[$k]['formContent']);
                $result[$k]['index'] = $k;
            }
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $result;
    }

    /**
     * @url GET /project/:prj_uid/activity/:act_uid/steps
     *
     * @param string $act_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetActivitySteps($act_uid, $prj_uid)
    {
        try {
            $task = new Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid", "stepUid" => "step_uid"));

            $activitySteps = $task->getSteps($act_uid);
            $_SESSION['PROCESS'] = $prj_uid;
            $dynaForm = new BusinessModelDynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);
            $oMobile = new BusinessModelLight();
            $step = new \ProcessMaker\Services\Api\Project\Activity\Step();
            $response = array();
            for ($i = 0; $i < count($activitySteps); $i++) {
                if ($activitySteps[$i]['step_type_obj'] == "DYNAFORM") {
                    $dataForm = $dynaForm->getDynaForm($activitySteps[$i]['step_uid_obj']);
                    $result = $this->parserDataDynaForm($dataForm);
                    $result["formUpdateDate"] = DateTime::convertUtcToIso8601($result["formUpdateDate"]);
                    $result['index'] = $i;
                    $result['stepId'] = $activitySteps[$i]["step_uid"];
                    $result['stepUidObj'] = $activitySteps[$i]["step_uid_obj"];
                    $result['stepMode'] = $activitySteps[$i]['step_mode'];
                    $result['stepCondition'] = $activitySteps[$i]['step_condition'];
                    $result['stepPosition'] = $activitySteps[$i]['step_position'];
                    $trigger = $oMobile->statusTriggers($step->doGetActivityStepTriggers($activitySteps[$i]["step_uid"],
                        $act_uid, $prj_uid));
                    $result["triggers"] = $trigger;
                    unset($result["formContent"]);
                    $response[] = $result;
                }
            }
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * Execute Trigger case
     *
     * @url POST /process/:prj_uid/task/:act_uid/case/:cas_uid/step/:step_uid/execute-trigger/:type
     * 
     * @param string $prj_uid {@min 1}{@max 32}
     * @param string $act_uid {@min 1}{@max 32}
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $step_uid {@min 32}{@max 32}
     * @param string $type {@choice before,after}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutExecuteTriggerCase($prj_uid, $act_uid, $cas_uid, $step_uid, $type)
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->doExecuteTriggerCase($userUid, $prj_uid, $act_uid, $cas_uid, $step_uid, $type);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * Get next step
     *
     * @param string $pro_uid {@min 1}{@max 32}
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $cas_index
     * @param int $step_pos
     *
     * @url GET /process/:pro_uid/case/:app_uid/:cas_index/step/:step_pos
     */
    public function doGetNextStep($pro_uid, $app_uid, $cas_index, $step_pos)
    {
        try {
            $oCase = new ClassesCases();

            $oAppDelegate = new AppDelegation();
            $alreadyRouted = $oAppDelegate->alreadyRouted($app_uid, $cas_index);
            if ($alreadyRouted) {
                throw (new RestException(Api::STAT_APP_EXCEPTION,
                    G::LoadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED')));
            }

            $userUid = $this->getUserId();
            $_SESSION["APPLICATION"] = $app_uid;
            $_SESSION["PROCESS"] = $pro_uid;
            //$_SESSION["TASK"]         = "";
            $_SESSION["INDEX"] = $cas_index;
            $_SESSION["USER_LOGGED"] = $userUid;
            //$_SESSION["USR_USERNAME"] = "";
            $response = $oCase->getNextStep($pro_uid, $app_uid, $cas_index, $step_pos);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * @url GET /project/:prj_uid/dynaformprocessed/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaformProcessed($prj_uid, $dyn_uid, $app_uid = null, $del_index = 0)
    {
        try {
            $dynaForm = new BusinessModelDynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);
            $_SESSION['PROCESS'] = $prj_uid;
            $response = $dynaForm->getDynaForm($dyn_uid);
            $result = $this->parserDataDynaForm($response);
            $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null) ?
                G::json_decode($result['formContent']) : "";

            $caseVariables = array();
            if (!is_null($app_uid)) {
                $case = new ClassesCases();
                $fields = $case->loadCase($app_uid, $del_index);
                $caseVariables = array_merge($fields['APP_DATA'],
                    BusinessModelCases::getGlobalVariables($fields['APP_DATA']));

                $caseVariables = DateTime::convertUtcToTimeZone($caseVariables);
            }

            $pmDynaform = new PmDynaform(array("APP_DATA" => $caseVariables, "CURRENT_DYNAFORM" => $dyn_uid));
            $pmDynaform->jsonr($result['formContent']);

            return $result;
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /project/:prj_uid/dynaform/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForm($prj_uid, $dyn_uid)
    {
        try {
            $dynaForm = new BusinessModelDynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);
            $_SESSION['PROCESS'] = $prj_uid;
            $response = $dynaForm->getDynaForm($dyn_uid);
            $result = $this->parserDataDynaForm($response);
            $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null) ? G::json_decode($result['formContent']) : "";

            $pmDynaForm = new PmDynaform(["CURRENT_DYNAFORM" => $dyn_uid]);
            $pmDynaForm->jsonr($result['formContent']);

            return $result;
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /project/:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     *
     */
    public function doGetDynaFormsId($prj_uid, $request_data)
    {
        try {
            $dynaForm = new BusinessModelDynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $_SESSION['PROCESS'] = $prj_uid;
            $return = array();
            foreach ($request_data['formId'] as $dyn_uid) {
                $response = $dynaForm->getDynaForm($dyn_uid);
                $pmDynaForm = new PmDynaform(array("CURRENT_DYNAFORM" => $dyn_uid));
                $result = $this->parserDataDynaForm($response);
                $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null) ? json_decode($result['formContent']) : "";
                $pmDynaForm->jsonr($result['formContent']);
                $return[] = $result;
            }
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $return;
    }

    public function parserDataDynaForm($data)
    {
        $structure = array(
            'dyn_uid' => 'formId',
            'dyn_title' => 'formTitle',
            'dyn_description' => 'formDescription',
            'dyn_content' => 'formContent',
            'dyn_update_date' => 'formUpdateDate'
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Starts a new case and assign the logged-in user to work on the initial task 
     * in the case. Note that the logged-in user must be in the pool of assigned 
     * users of the initial task. Also note that the new case's status will be 
     * set to "DRAFT", not "TO_DO".
     * 
     * @url POST /process/:pro_uid/task/:task_uid/start-case
     * 
     * @param string $pro_uid {@min 32}{@max 32}
     * @param string $task_uid {@min 32}{@max 32}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function postStartCase($pro_uid, $task_uid)
    {
        try {
            $oMobile = new BusinessModelLight();
            $result = $oMobile->startCase($this->getUserId(), $pro_uid, $task_uid);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $result;
    }

    /**
     * Return Informaction User for derivate
     * assignment Users
     *
     * @url GET /task/:tas_uid/case/:app_uid/:del_index/assignment
     *
     * @param string $tas_uid {@min 32}{@max 32}
     * @param string $app_uid {@min 32}{@max 32}
     * @param int $del_index
     */
    public function doGetPrepareInformation($tas_uid, $app_uid, $del_index = null)
    {
        try {
            $usr_uid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getPrepareInformation($usr_uid, $tas_uid, $app_uid, $del_index);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * Route Case
     *
     * @url PUT /cases/:app_uid/route-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param int $del_index {@from body}
     * @param array $tasks {@from body}
     *
     * @return array
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutRouteCase($app_uid, $del_index = null, $tasks = array())
    {
        try {
            $oMobile = new BusinessModelLight();
            $response = $oMobile->updateRouteCase($app_uid, $this->getUserId(), $del_index, $tasks);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * @url GET /user/data
     */
    public function doGetUserData()
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getUserData($userUid);
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * @url POST /users/data
     */
    public function doGetUsersData($request_data)
    {
        try {
            $response = array();
            $oMobile = new BusinessModelLight();
            foreach ($request_data['user']['ids'] as $userUid) {
                $response[] = $oMobile->getUserData($userUid);
            }
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * Generates a Google Maps .jpg image file for a case. This image can be found 
     * in the Documents section under the Home tab.
     * 
     * @url POST /case/:app_uid/upload/location
     * 
     * @param string $app_uid { @min 32}{@max 32}
     * @param float $latitude {@min -90}{@max 90}
     * @param float $longitude {@min -180}{@max 180}
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function postInputDocumentLocation($app_uid, $latitude, $longitude)
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();

            $url = "http://maps.googleapis.com/maps/api/staticmap?center=" . $latitude . ',' . $longitude . "&format=jpg&size=600x600&zoom=15&markers=color:blue%7Clabel:S%7C" . $latitude . ',' . $longitude;
            $imageLocation = imagecreatefromjpeg($url);
            $tmpfname = tempnam("php://temp", "pmm");
            imagejpeg($imageLocation, $tmpfname);

            $_FILES["form"]["type"] = "image/jpeg";
            $_FILES["form"]["name"] = 'Location.jpg';
            $_FILES["form"]["tmp_name"] = $tmpfname;
            $_FILES["form"]["error"] = 0;
            $sizes = getimagesize($tmpfname);
            $_FILES["form"]["size"] = ($sizes['0'] * $sizes['1']);

            $request_data = array(array('name' => $_FILES["form"]["name"]));
            $file = $oMobile->postUidUploadFiles($userUid, $app_uid, $request_data);

            $strPathName = PATH_DOCUMENT . G::getPathFromUID($app_uid) . PATH_SEP;
            $strFileName = $file[0]['appDocUid'] . "_" . $file[0]['docVersion'] . ".jpg";
            if (!is_dir($strPathName)) {
                G::verifyPath($strPathName, true);
            }
            copy($tmpfname, $strPathName . $strFileName);
            unlink($tmpfname);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $file;
    }

    /**
     * Generates a base64 string of a file.
     * 
     * @url POST /case/:app_uid/download64
     * 
     * @param string $app_uid {@min 32}{@max 32}
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function postDownloadFile($app_uid, $request_data)
    {
        try {
            $oMobile = new BusinessModelLight();
            $files = $oMobile->downloadFile($app_uid, $request_data);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $files;
    }

    /**
     * @url POST /logout
     *
     * @param $access
     * @param $refresh
     * @return mixed
     */
    public function postLogout($access, $refresh)
    {
        try {
            $oMobile = new BusinessModelLight();
            $files = $oMobile->logout($access, $refresh);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $files;
    }

    /**
     * @url GET /:type/case/:app_uid
     *
     * @param $access
     * @param $refresh
     * @return mixed
     */
    public function getInformation($type, $app_uid)
    {
        try {
            $arrayFieldIso8601 = array(
                'caseCreateDate',
                'caseUpdateData',
                'delDelegateDate',
                'delInitDate',
                'delDueDate',
                'delFinishDate'
            );
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getInformation($userUid, $type, $app_uid);
            $response = $this->parserGetInformation($response);
            $response = DateTime::convertUtcToIso8601($response, $arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    public function parserGetInformation($data)
    {
        $structure = array(
            'case' => array(
                'PRO_TITLE' => 'processTitle',
                'APP_TITLE' => 'caseTitle',
                'APP_NUMBER' => 'caseNumber',
                'APP_STATUS' => 'caseStatus',
                'APP_UID' => 'caseId',
                'CREATOR' => 'caseCreator',
                'CREATE_DATE' => 'caseCreateDate',
                'UPDATE_DATE' => 'caseUpdateData',
                'DESCRIPTION' => 'caseDescription',
                'PRO_UID' => 'processId',
                'DEL_INDEX' => 'delIndex'
            ),
            'task' => array(
                'TAS_TITLE' => 'taskTitle',
                'CURRENT_USER' => 'currentUser',
                'DEL_DELEGATE_DATE' => 'delDelegateDate',
                'DEL_INIT_DATE' => 'delInitDate',
                'DEL_TASK_DUE_DATE' => 'delDueDate',
                'DEL_FINISH_DATE' => 'delFinishDate',
                'TAS_UID' => 'taskId'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Creates new case file record(s) in a specified case. These case files can 
     * be attached files (used by File controls), Input Document files, or Output Document 
     * files in version 1.0.1.8 and later. In version 1.0.1.7 and earlier, the case 
     * files can only be attached files. (Note that case files are known as AppDocuments 
     * in ProcessMaker). This endpoint adds new record(s) to the APP_DOCUMENT table 
     * and stores their file name(s) in the CONTENT table in the database. It returns 
     * the generated case file ID and version number for each file. This information 
     * can then be used to call the POST /light/case/{app_uid}/upload/{app_doc_uid} 
     * endpoint to upload each file.
     * 
     * @url POST /case/:app_uid/upload
     * 
     * @param $access
     * @param $refresh
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function uidUploadFiles($app_uid, $request_data)
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $filesUids = $oMobile->postUidUploadFiles($userUid, $app_uid, $request_data);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $filesUids;
    }

    /**
     * Creates new case file record(s) in a specified case. These case files can 
     * be attached files (used by File controls), Input Document files, or Output Document 
     * files in version 1.0.1.8 and later. In version 1.0.1.7 and earlier, the case 
     * files can only be attached files. (Note that case files are known as AppDocuments 
     * in ProcessMaker). This endpoint adds new record(s) to the APP_DOCUMENT table 
     * and stores their file name(s) in the CONTENT table in the database. It returns 
     * the generated case file ID and version number for each file. This information 
     * can then be used to call the POST /light/case/{app_uid}/upload/{app_doc_uid} 
     * endpoint to upload each file.
     * 
     * @url POST /case/:app_uid/upload/:app_doc_uid
     * 
     * @param $access
     * @param $refresh
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function documentUploadFiles($app_uid, $app_doc_uid, $request_data)
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->documentUploadFiles($userUid, $app_uid, $app_doc_uid, $request_data);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * Assign the user logged-in to an unassigned case.
     * 
     * @url POST /case/:app_uid/claim
     * 
     * @param $app_uid {@min 1}{@max 32}
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function claimCaseUser($app_uid)
    {
        try {
            $userUid = $this->getUserId();
            $oMobile = new BusinessModelLight();
            $response = $oMobile->claimCaseUser($userUid, $app_uid);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $response;
    }

    /**
     * Get Case Notes
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $usr_uid {@from path}
     * @param string $date_from {@from path}
     * @param string $date_to {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /case/:app_uid/notes
     */
    public function doGetCaseNotes(
        $app_uid,
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $usr_uid = '',
        $date_from = '',
        $date_to = '',
        $search = ''
    ) {
        try {
            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['user'] = $usr_uid;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['search'] = $search;

            $appNotes = new \AppNotes();
            $response = $appNotes->getNotesList($app_uid, '', $start, $limit);
            $response = $this->parserDataNotes($response['array']['notes']);

            return DateTime::convertUtcToIso8601($response, array('date'));
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataNotes($data)
    {
        $structure = array(
            'APP_UID' => 'caseId',
            'notes' => array(
                'NOTE_DATE' => 'date',
                'NOTE_CONTENT' => 'content'
            ),
            'user' => array(
                'USR_UID' => 'userId',
                'USR_USERNAME' => 'name',
                'USR_FIRSTNAME' => 'firstName',
                'USR_LASTNAME' => 'lastName',
                'USR_EMAIL' => 'email'
            )
        );

        $response = $this->replaceFields($data, $structure);

        return $response;
    }

    /**
     * Creates a new case note for a given case. Note that only users who are 
     * currently assigned to work on the case or have Process Permissions to access 
     * case notes may create a case note.
     *
     * @url POST /case/:app_uid/note
     * 
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $noteContent {@min 1}{@max 500}
     * @param int $sendMail {@choice 1,0}
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostCaseNote($app_uid, $noteContent, $sendMail = 0)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new BusinessModelCases();
            $sendMail = ($sendMail == 0) ? false : true;
            $cases->saveCaseNote($app_uid, $usr_uid, $noteContent, $sendMail);
            $result = array("status" => 'ok');
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $result;
    }

    /**
     * GET list category
     *
     * @return array
     * @throws RestException
     *
     * @url GET /category
     */
    public function getCategoryList()
    {
        try {
            $oLight = new BusinessModelLight();
            $category = $oLight->getCategoryList();
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $category;
    }

    /**
     * GET list process
     *
     * @return array
     * @throws RestException
     *
     * @param string $action {@min 1}{@max 32}
     * @param string $cat_uid {@max 32}{@from path}
     *
     * @url GET /process/:action
     */
    public function getProcessList($action, $cat_uid = null)
    {
        try {
            $usr_uid = $this->getUserId();
            $oLight = new BusinessModelLight();
            $process = $oLight->getProcessList($action, $cat_uid, $usr_uid);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $process;
    }

    /**
     * GET list process
     *
     * @return array
     * @throws RestException
     *
     * @param string $task_uid {@min 1}{@max 32}
     *
     * @url GET /userstoreassign/:task_uid
     */
    public function getUsersToReassign($task_uid)
    {
        try {
            $usr_uid = $this->getUserId();
            $oLight = new BusinessModelLight();
            $process = $oLight->getUsersToReassign($usr_uid, $task_uid);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $process;
    }

    /**
     * Reassign a case.
     * 
     * @url POST /reassign/:app_uid/user/:to_usr_uid
     * 
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $to_usr_uid {@min 1}{@max 32}
     * 
     * @return stdclass
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_REASSIGNCASE, PM_REASSIGNCASE_SUPERVISOR}
     */
    public function reassignCase($app_uid, $to_usr_uid)
    {
        try {
            $usr_uid = $this->getUserId();
            $oLight = new BusinessModelLight();
            $process = $oLight->reassignCase($usr_uid, $app_uid, $to_usr_uid);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $process;
    }

    /**
     * Paused Case
     *
     * @url POST /cases/:app_uid/pause
     * 
     * @param string $app_uid {@min 1}{@max 32}
     * 
     * @return stdclass
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function pauseCase($app_uid, $request_data)
    {
        try {
            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, array('unpauseDate'));
            $usr_uid = $this->getUserId();
            $oLight = new BusinessModelLight();
            $process = $oLight->pauseCase($usr_uid, $app_uid, $request_data);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $process;
    }

    /**
     * Unpaused Case
     *
     * @url POST /cases/:app_uid/unpause
     * 
     * @param string $app_uid {@min 1}{@max 32}
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function unpauseCase($app_uid)
    {
        $result = array();
        try {
            $usr_uid = $this->getUserId();
            $cases = new BusinessModelCases();
            $cases->putUnpauseCase($app_uid, $usr_uid);
            $result["status"] = "ok";
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }

        return $result;
    }

    /**
     * Cancels a case assigned to the user logged-in. The case's status is changed 
     * to "CANCELLED" and it is no longer possible to open or change the case, 
     * but all the case data will remain in the database.
     *
     * @url POST /cases/:app_uid/cancel
     * 
     * @param string $cas_uid {@min 1}{@max 32}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutCancelCase($app_uid)
    {
        $response = array("status" => "false");
        try {
            $userUid = $this->getUserId();
            $cases = new BusinessModelCases();
            $cases->putCancelCase($app_uid, $userUid);
            $response["status"] = "ok";
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * Get Case Variables
     *
     * @access protected
     * @class  AccessControl {@className \ProcessMaker\Services\Api\Cases}
     * @url GET /:app_uid/variables
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $dyn_uid
     * @param string $pro_uid
     * @param string $act_uid
     * @param int $app_index
     * @return mixed
     * @throws RestException
     */
    public function doGetCaseVariables($app_uid, $dyn_uid = null, $pro_uid = null, $act_uid = null, $app_index = null)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new BusinessModelCases();
            $response = $cases->getCaseVariables($app_uid, $usr_uid, $dyn_uid, $pro_uid, $act_uid, $app_index);

            return DateTime::convertUtcToTimeZone($response);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put Case Variables
     *
     * @url PUT /:app_uid/variable
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param array $request_data
     * @param string $dyn_uid {@from path}
     * @param int $del_index {@from path}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutCaseVariables($app_uid, $request_data, $dyn_uid = '', $del_index = 0)
    {
        try {
            //Check if the user is a supervisor or have permissions
            $usr_uid = $this->getUserId();
            $cases = new BusinessModelCases();
            $hasAccess = $cases->checkUserHasPermissionsOrSupervisor($usr_uid, $app_uid, $dyn_uid);

            //When the user is a supervisor del_index is 0
            if ($del_index <= 0 && !$hasAccess) {
                throw (new Exception(G::LoadTranslation('ID_INVALID_VALUE_EXPECTING_POSITIVE_INTEGER',
                    array('del_index')), Api::STAT_APP_EXCEPTION));
            }
            if ($del_index === null) {
                throw (new Exception(G::LoadTranslation('ID_CAN_NOT_BE_NULL', array('del_index')),
                    Api::STAT_APP_EXCEPTION));
            }
            if ($del_index > 0) {
                if ($cases->caseAlreadyRouted($app_uid, $del_index, $usr_uid)) {
                    throw (new Exception(G::LoadTranslation('ID_CASE_ALREADY_DERIVATED'), Api::STAT_APP_EXCEPTION));
                }
            }
            $request_data = DateTime::convertDataToUtc($request_data);
            $cases->setCaseVariables($app_uid, $request_data, $dyn_uid, $usr_uid, $del_index);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get in base64 the image process (processmap)
     *
     * @url GET /process/:pro_uid/case
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $app_uid {@min 32}{@max 32}{@from path}
     */
    public function doGetProcessMapImage($pro_uid, $app_uid = null)
    {
        $return = array();
        try {
            $oPMap = new ProcessMap();
            $schema = Adapter\BpmnWorkflow::getStruct($pro_uid);

            $schemaStatus = array();
            if (!is_null($app_uid)) {
                $case = new BusinessModelCases();
                $case->setFormatFieldNameInUppercase(false);
                $schemaStatus = $case->getTasks($app_uid);
            }

            $file = $oPMap->get_image($schema, $schemaStatus);
            ob_start();
            imagepng($file);
            $image = ob_get_clean();
            $return["map"] = base64_encode($image);

        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $return;
    }

    /**
     * Get configuration ProcessMaker
     *
     * @return array
     *
     * @access public
     * @url GET /config
     *
     * @param boolean $fileLimit {@from path}
     */
    public function getConfiguration($fileLimit = false)
    {
        try {
            $params = array('fileLimit' => $fileLimit);
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getConfiguration($params);
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * Get configuration ProcessMaker
     *
     * @return array
     *
     * @url GET /config-user
     *
     * @param boolean $fileLimit {@from path}
     * @param boolean $tz {@from path}
     */
    public function getConfigurationUser($fileLimit = false, $tz = false)
    {
        try {
            $params = array('fileLimit' => $fileLimit, 'tz' => $tz);
            $oMobile = new BusinessModelLight();
            $response = $oMobile->getConfiguration($params);
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * This endpoint executes the following three actions:
     * 1. Executes a trigger before a step: First, it executes any trigger assigned 
     * before the indicated step.
     * 2. Get the Next Step: It obtains the details about the next step of the case.
     * 3. Get Variables: Finally, it returns any variable stored or changed by 
     * actions 1 and 2.
     *
     * @url POST /get-next-step/:app_uid
     * 
     * @param string $app_uid
     * @param array $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetStep($app_uid, $request_data)
    {
        $pro_uid = $request_data["pro_uid"];
        $act_uid = $request_data["act_uid"];
        $step_uid = $request_data["step_uid"];
        $step_pos = $request_data["step_pos"];
        $app_index = $request_data["app_index"];
        $dyn_uid = isset($request_data["dyn_uid"]) ? $request_data["dyn_uid"] : null;
        $usr_uid = $this->getUserId();

        $response = array();

        //conditionalSteps
        $oCase = new ClassesCases();
        $oAppDelegate = new AppDelegation();
        $alreadyRouted = $oAppDelegate->alreadyRouted($app_uid, $app_index);
        if ($alreadyRouted) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, G::LoadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED')));
        }
        $_SESSION["APPLICATION"] = $app_uid;
        $_SESSION["PROCESS"] = $pro_uid;
        $_SESSION["INDEX"] = $app_index;
        $_SESSION["USER_LOGGED"] = $usr_uid;

        do {
            $conditionalSteps = $oCase->getNextStep($pro_uid, $app_uid, $app_index, $step_pos);
            if (is_array($conditionalSteps) && (
                    $conditionalSteps["TYPE"] === "DYNAFORM" ||
                    $conditionalSteps["TYPE"] === "DERIVATION")
            ) {
                break;
            } else {
                $step_pos = $step_pos + 1;
            }
        } while ($conditionalSteps !== false);
        $response["conditionalSteps"] = $conditionalSteps;

        //trigger before
        if ($conditionalSteps) {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(StepPeer::STEP_UID);
            $c->addSelectColumn(StepPeer::STEP_UID_OBJ);
            $c->add(StepPeer::TAS_UID, $act_uid);
            $c->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $c->add(StepPeer::STEP_UID_OBJ, $conditionalSteps['UID']);
            $rs = StepPeer::doSelectRS($c);
            $rs->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            if ($row) {
                $oMobile = new BusinessModelLight();
                $triggers = $oMobile->doExecuteTriggerCase($usr_uid, $pro_uid, $act_uid, $app_uid, $row['STEP_UID'],
                    "before", $app_index);
                if ($triggers["status"] === "ok") {
                    $triggers["status"] = "200";
                }
                $response["triggers"] = $triggers;
            }
        }

        //variables
        $cases = new BusinessModelCases();
        $variables = $cases->getCaseVariables($app_uid, $usr_uid, $dyn_uid, $pro_uid, $act_uid, $app_index);
        $variables = DateTime::convertUtcToTimeZone($variables);
        $response["variables"] = $variables;

        return $response;
    }

    /**
     * This function checks if the application uids sent are in the to do list for the specified user
     * @url POST /todo/check
     * @param array $requestData
     * @return array $response
     * @throws Exception
     */
    public function doGetTodoCheck($requestData)
    {
        try {
            $response = [];
            $userUid = $this->getUserId();
            $mobile = new BusinessModelLight();
            $response = $mobile->getListCheck($userUid, $requestData, 'inbox', 'todo');
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * This function checks if the application uids sent are in the draft list for 
     * the specified user.
     * 
     * @url POST /draft/check
     * 
     * @param array $requestData
     * 
     * @return array $response
     * @throws Exception
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetDraftCheck($requestData)
    {
        try {
            $response = [];
            $userUid = $this->getUserId();
            $mobile = new BusinessModelLight();
            $response = $mobile->getListCheck($userUid, $requestData, 'inbox', 'draft');
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * This function checks if the application uids sent are in the unassigned list for the specified user
     * @url POST /unassigned/check
     * @param array $requestData
     * @return array $response
     * @throws Exception
     */
    public function doGetUnassignedCheck($requestData)
    {
        try {
            $response = [];
            $userUid = $this->getUserId();
            $mobile = new BusinessModelLight();
            $response = $mobile->getListCheck($userUid, $requestData, 'unassigned');
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }
}
