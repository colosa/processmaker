<?php
namespace ProcessMaker\BusinessModel;

use \G;

class ReportingIndicators
{


    /**
     * Returns the historic data of an indicator
     *
     * @param array $indicatorUid indicator from which will be extracted the information
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     */
    public function getHistoricData($indicatorUid,  $initDate, $endDate, $periodicity, $language)
    {
		$retval = "";
        $calculator = new \IndicatorsCalculator();
        $arr = $calculator->indicatorData($indicatorUid);
        $indicator = $arr[0];
		$processesId = $indicator['DAS_UID_PROCESS'];
		$indicatorType = $indicator['DAS_IND_TYPE'];
		switch ($indicatorType) {
			case \ReportingIndicatorTypeEnum::PEI:
				$retval = $calculator->peiHistoric($processesId, $initDate, $endDate, \ReportingPeriodicityEnum::fromValue($periodicity));
				break;
			case \ReportingIndicatorTypeEnum::UEI:
				$retval = $calculator->ueiHistoric($processesId, $initDate, $endDate, \ReportingPeriodicityEnum::fromValue($periodicity));
				break;
			default:
				throw new Exception("Can't retrive historic Data becasuse de indicator type " + $indicator['DAS_IND_TYPE'] + " has no operation associated.");
				break;
		}
        return $retval;
    }
	

    /**
     * Lists tasks of a process and it's statistics (efficiency, average times, etc.)
     *
     * @param array $processList array with the list of processes to filter the results.
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     */
    public function getPeiCompleteData($indicatorUid,  $compareDate, $measureDate, $language)
    {
        $calculator = new \IndicatorsCalculator();
        $processes = $calculator->peiProcesses($indicatorUid, $measureDate, $measureDate, $language);
        $arr = $calculator->indicatorData($indicatorUid);
        $indicator = $arr[0];
		$processesId = $indicator['DAS_UID_PROCESS'];
		$peiValue = current(reset($calculator->peiHistoric($processesId, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE)));
		$peiCost = current(reset($calculator->peiCostHistoric($processesId, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE)));
		$peiCompare = current(reset($calculator->peiHistoric($processesId, $compareDate, $compareDate, \ReportingPeriodicityEnum::NONE)));

		$retval = array(
						"id" => $indicatorUid,
						"efficiencyIndex" => $peiValue,
						"efficiencyIndexToCompare" => $peiCompare,
						"efficiencyVariation" => ($peiValue-$peiCompare),
						"inefficiencyCost" => $peiCost,
						"data"=>$processes);
        return $retval;
    }

    /**
     * Lists tasks of a employee and it's statistics (efficiency, average times, etc.)
     *
     * @param array $employeeList array with the list of employeees to filter the results.
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     */
    public function getUeiCompleteData($indicatorUid, $compareDate, $measureDate,$language)
    {
        $calculator = new \IndicatorsCalculator();
        $groups = $calculator->ueiUserGroups($indicatorUid, $measureDate, $measureDate, $language);

        //TODO think what if each indicators has a group or user subset assigned. Now are all
        $ueiValue = current(reset($calculator->ueiHistoric(null, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE)));
        $arrCost = $calculator->ueiUserGroups($indicatorUid, $measureDate, $measureDate, $language);

		$ueiCost = current(reset($calculator->ueiCostHistoric(null, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE)));
        $ueiCompare = current(reset($calculator->ueiHistoric(null, $compareDate, $compareDate, \ReportingPeriodicityEnum::NONE)));

		$retval = array(
						"id" => $indicatorUid,
						"efficiencyIndex" => $ueiValue,
                        "efficiencyVariation" => ($ueiValue-$ueiCompare),
                        "inefficiencyCost" => $ueiCost,
                        "efficiencyIndexToCompare" => $ueiCompare,
                        "data"=>$groups);
        return $retval;
    }

    /**
     * Lists tasks of a employee and it's statistics (efficiency, average times, etc.)
     *
     * @param array $employeeList array with the list of employeees to filter the results.
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     */
    public function getUeiGroupsStatistics($groupId, $initDate, $endDate, $language)
    {
        $calculator = new \IndicatorsCalculator();
        $retval = $calculator->groupEmployeesData($groupId, $initDate, $endDate, $language);
        return $retval;
    }

    /**
     * Lists tasks of a process and it's statistics (efficiency, average times, etc.)
     *
     * @param array $processList array with the list of processes to filter the results.
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     */
    public function getPeiTasksStatistics($processList, $initDate, $endDate, $language)
    {
        $calculator = new \IndicatorsCalculator();
        $retval = $calculator->peiTasks($processList, $initDate, $endDate, $language);
        return $retval;
    }

    /**
     * Lists tasks of a process and it's statistics (efficiency, average times, etc.)
     *
     * @param $indicatorId
     * @param DateTime $initDate date from the index will be calculated
     * @param DateTime $endDate date until the index will be calculated
     * @param string $language language for the names (en, es, etc.)
     *
     * return decimal  value
     * @return array
     */
    public function getGeneralIndicatorStatistics($indicatorId, $initDate, $endDate, $periodicity)
    {
        $calculator = new \IndicatorsCalculator();
        $arr = $calculator->generalIndicatorData($indicatorId, $initDate, $endDate, \ReportingPeriodicityEnum::NONE);
        $value = $arr[0]['value'];
        $dataList1 = $calculator->
			generalIndicatorData($indicatorId, 
				$initDate, $endDate, 
				\ReportingPeriodicityEnum::fromValue($arr[0]['frequency1Type']));

        $dataList2 = $calculator->
			generalIndicatorData($indicatorId, 
				$initDate, $endDate, 
				\ReportingPeriodicityEnum::fromValue($arr[0]['frequency2Type']));

        $returnValue = array("index" => $value,
			"graph1XLabel"=>$arr[0]['graph1XLabel'],  
			"graph1YLabel"=>$arr[0]['graph1YLabel'], 
			"graph2XLabel"=>$arr[0]['graph2XLabel'],  
			"graph2YLabel"=>$arr[0]['graph2YLabel'], 
			"graph1Type"=>$arr[0]['graph1Type'],
			"graph2Type"=>$arr[0]['graph2Type'],
			"frequency1Type"=>$arr[0]['frequency1Type'],
			"frequency2Type"=>$arr[0]['frequency2Type'],
			"graph1Data"=>$dataList1,
			"graph2Data"=>$dataList2
		);
        return $returnValue;
    }

    /**
     * Get list status indicator
     *
     * @access public
     * @param array $options, Data for list
     * @return array
     *
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getStatusIndicator($options = array())
    {
        Validator::isArray($options, '$options');

        $usrUid = isset( $options["usrUid"] ) ? $options["usrUid"] : "";

        $calculator = new \IndicatorsCalculator();
        $result = $calculator->statusIndicator($usrUid);
        return $result;
    }
}

