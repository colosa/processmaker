<?php

namespace ProcessMaker\BusinessModel\Cases;

use AppDocument;
use Cases as ClassesCases;
use Dynaform;
use Exception;
use G;
use Propel;
use StdClass;
use Task as ClassesTask;

/**
 * Return the ChangeLog of a Dynaform
 */
class ChangeLog
{
    /**
     * List of variables that should not be considered
     * @var string[] $reserved
     */
    private $reserved = [
        'TASK',
        'INDEX',
        'DYN_CONTENT_HISTORY',
        '__VAR_CHANGED__',
    ];
    /**
     * List of reserved steps
     * @var string[] $reservedSteps
     */
    private $reservedSteps = [
        -1,
        -2,
    ];

    /**
     * Map of variables and its values
     * @var mixed[]
     */
    private $values = [];

    /**
     * List of variables changes
     * @var object[]
     */
    private $tree;

    /**
     * List of assigned permissions
     * @var string[]
     */
    private $permissions = [];

    public function getChangeLog($appUid, $proUid, $tasUid, $start, $limit)
    {
        $this->loadPermissions($appUid, $proUid, $tasUid);
        $result = $this->getResultSet($appUid);
        $totalCount = $this->readRecords($result, $start, $limit);
        return ['data' => $this->tree, 'totalCount' => $totalCount];
    }

    /**
     * This function get the appHistory related to the case
     *
     * @param string $appUid
     *
     * @return array;
    */
    private function getResultSet($appUid)
    {
        $conn = Propel::getConnection('workflow');
        $sql = 'SELECT APP_HISTORY.*, USERS.USR_USERNAME FROM APP_HISTORY'
            .' LEFT JOIN USERS ON(APP_HISTORY.USR_UID=USERS.USR_UID)'
            .' WHERE APP_UID=? ORDER BY HISTORY_DATE ASC';
        $stmt = $conn->prepareStatement($sql);
        $stmt->set(1, $appUid);
        if (!$stmt->executeQuery()) {
            throw Exception(G::LoadTranslation('ID_MSG_AJAX_FAILURE'));
        }
        return $stmt->getResultSet();
    }

    /**
     * This function read the records, related to the specific result and update the data
     *
     * @param object $result
     * @param integer $start
     * @param integer $limit
     *
     * @return integer;
     */
    private function readRecords($result, $start = 0, $limit = 15)
    {
        $index = 0;
        while ($result->next()) {
            $row = $result->getRow();
            $data = unserialize($row['HISTORY_DATA']);
            if ($this->isEmpty($data)) {
                continue;
            }
            if ($index < $start) {
                $index += $this->updateData(
                    $data, $row, $this->hasPermission($row['DYN_UID']), false);
                continue;
            }
            $a = $this->updateData($data, $row,
                                   $this->hasPermission($row['DYN_UID']), true);
            $limit-= $a;
            $index+= $a;
        }
        return $index;
    }

    /**
     * This function check if is empty
     *
     * @param array $data
     *
     * @return boolean;
     */
    private function isEmpty($data)
    {
        foreach ($data as $key => $value) {
            if (array_search($key, $this->reserved) !== false) {
                continue;
            }
            return false;
        }
        return true;
    }

    /**
     * This function update the data
     *
     * @param array $data
     * @param array $row
     * @param boolean $hasPermission
     * @param boolean $addToTree
     *
     * @return integer;
     */
    private function updateData($data, $row, $hasPermission, $addToTree = false)
    {
        $i = 0;
        foreach ($data as $key => $value) {
            if (array_search($key, $this->reserved) !== false) {
                continue;
            }
            if ($hasPermission && (!isset($this->values[$key]) || $this->values[$key]
                !== $value)) {
                if ($addToTree) {
                    $node = new StdClass();
                    $node->field = $key;
                    $previousValue = !isset($this->values[$key]) ? null : $this->values[$key];
                    if(!is_array($previousValue)){
                        $node->previousValue = (string) $previousValue;
                    } else {
                        $node->previousValue = "<br />".nl2br(print_r($previousValue, true));
                    }
                    if(!is_array($value)){
                        $node->currentValue = (string) $value;
                    } else {
                        $node->currentValue = "<br />".nl2br(print_r($value, true));
                    }
                    $node->previousValueType = gettype($previousValue);
                    $node->currentValueType = gettype($value);
                    $node->record = $this->getHistoryTitle($row);
                    $this->tree[] = $node;
                }
                $i++;
            }
            $this->values[$key] = $value;
        }
        return $i;
    }

    /**
     * This function get the title related to the row
     *
     * @param array $row
     *
     * @return string;
     */
    private function getHistoryTitle($row)
    {
        return $this->getObjectTitle($row['TAS_UID'], 'TASK')
            .' / '.$this->getObjectTitle($row['DYN_UID'], $row['OBJ_TYPE'])
            .' / '.G::LoadTranslation('ID_LAN_UPDATE_DATE').': '.$row['HISTORY_DATE']
            .' / '.G::LoadTranslation('ID_USER').': '.$row['USR_USERNAME'];
    }

    /**
     * This function get the object title
     *
     * @param string $uid
     * @param string $objType
     *
     * @return string;
     */
    private function getObjectTitle($uid, $objType)
    {
        switch ($objType) {
            case 'DYNAFORM':
                $obj = new Dynaform();
                $obj->Load($uid);
                $title = $obj->getDynTitle();
                break;
            case 'OUTPUT_DOCUMENT':
            case 'INPUT_DOCUMENT':
                $obj = new AppDocument();
                $obj->load($uid);
                $title = $obj->getDynTitle();
                break;
            case 'TASK':
                $obj = new ClassesTask();
                $obj->load($uid);
                $title = $obj->getTasTitle();
                break;
            default:
                $title = $uid;
        }
        return $title;
    }

    /**
     * This function get the permissions
     *
     * @param string $appUid
     * @param string $proUid
     * @param string $tasUid
     *
     * @return void;
     */
    private function loadPermissions($appUid, $proUid, $tasUid)
    {
        $oCase = new ClassesCases();
        $this->permissions = $oCase->getAllObjects(
            $proUid, $appUid, $tasUid, $_SESSION['USER_LOGGED']
        );
    }

    /**
     * This function verify if has permission
     *
     * @param string $uid
     *
     * @return boolean;
     */
    private function hasPermission($uid)
    {
        if(array_search($uid, $this->reservedSteps)!==false) {
            return false;
        }
        foreach ($this->permissions as $type => $ids) {
            if (array_search($uid, $ids) !== false) {
                return true;
            }
        }
        return false;
    }
}