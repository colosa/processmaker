<?php 


abstract class BasicEnum {
    private static $constCacheArray = NULL;

    private static function getConstants() {
        if (self::$constCacheArray == NULL) { 
            self::$constCacheArray = array();
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
}


abstract class ReportingPeriodicityEnum extends BasicEnum {
	//100s space to easy add more periods if in the future new periods are needed
    const NONE = 0;
    const MONTH = 100;
    const QUARTER = 200;
    const SEMESTER = 300;
    const YEAR = 400;

	public static function fromValue($value) {
		if ($value == ReportingPeriodicityEnum::NONE) return ReportingPeriodicityEnum::NONE;
		if ($value == ReportingPeriodicityEnum::MONTH) return ReportingPeriodicityEnum::MONTH;
		if ($value == ReportingPeriodicityEnum::QUARTER) return ReportingPeriodicityEnum::QUARTER;
		if ($value == ReportingPeriodicityEnum::SEMESTER) return ReportingPeriodicityEnum::SEMESTER;
		if ($value == ReportingPeriodicityEnum::YEAR) return ReportingPeriodicityEnum::YEAR;
		return ReportingPeriodicityEnum::MONTH;
	}

	public static function labelFromValue($value) {
		if ($value == ReportingPeriodicityEnum::MONTH) return "ID_MONTH" ;
		if ($value == ReportingPeriodicityEnum::QUARTER) return "ID_QUARTER";
		if ($value == ReportingPeriodicityEnum::SEMESTER) return "ID_SEMESTER";
		if ($value == ReportingPeriodicityEnum::YEAR) return "ID_YEAR";
		return "ID_MONTH";
	}
}

abstract class IndicatorDataSourcesEnum extends BasicEnum {
	//100s space to easy add more periods if in the future new periods are needed
	const USER = 0;
	const PROCESS = 100;
	const PROCESS_CATEGORY = 200;
	const USER_GROUP = 300;
}

abstract class ReportingIndicatorTypeEnum extends BasicEnum {
	const PEI = 1010;
	const UEI = 1030;
	const INBOX_STATUS = 1050;
}

class IndicatorsCalculator
{
	private $userReportingMetadata = array("tableName" => "USR_REPORTING", "keyField" => "PRO_UID");
	private $processReportingMetadata = array("tableName" => "PRO_REPORTING", "keyField" => "PRO_UID");
	private $userGroupReportingMetadata = array("tableName" => "USR_REPORTING", "keyField" => "PRO_UID");
	private $processCategoryReportingMetadata = array("tableName" => "PRO_REPORTING", "keyField" => "PRO_UID");

	private $peiCostFormula = " SUM(case when (TOTAL_TIME_BY_TASK + TOTAL_QUEUE_TIME_BY_TASK) > 0 then (TOTAL_CASES_OUT * CONFIGURED_TASK_TIME * PRO_COST - (TOTAL_TIME_BY_TASK * USER_HOUR_COST + TOTAL_QUEUE_TIME_BY_TASK * PRO_COST))  else 0 end)";
	private $peiFormula = "SUM(TOTAL_CASES_OUT*CONFIGURED_TASK_TIME) / SUM(SDV_TIME * TOTAL_CASES_OUT + (TOTAL_TIME_BY_TASK + TOTAL_QUEUE_TIME_BY_TASK))";

	private $ueiCostFormula = " SUM(case when (TOTAL_TIME_BY_TASK + TOTAL_QUEUE_TIME_BY_TASK) > 0 then (TOTAL_CASES_OUT * CONFIGURED_TASK_TIME * PRO_COST - (TOTAL_TIME_BY_TASK * USER_HOUR_COST + TOTAL_QUEUE_TIME_BY_TASK * PRO_COST)) else 0 end)";
	private $ueiFormula = "SUM(TOTAL_CASES_OUT * CONFIGURED_TASK_TIME) / SUM((TOTAL_TIME_BY_TASK + TOTAL_QUEUE_TIME_BY_TASK))";

	public function getSkewOfDataDistribution($table, $field) {
		/*$sqlString = "SET @median = (SELECT x.$field from $table x, $table y
						GROUP BY x.$field
						HAVING SUM(SIGN(1-SIGN(y.$field-x.$field)))/COUNT(*) > .5
						LIMIT 1)";
		*/

		$sqlString = "SELECT x.$field from $table x, $table y
						GROUP BY x.$field
						HAVING SUM(SIGN(1-SIGN(y.$field-x.$field)))/COUNT(*) > .5
						LIMIT 1";

		$returnValue = 0;
		$connection = $this->pdoConnection();
		$result = $this->pdoExecutorWithConnection($sqlString, array(), $connection);
		$result2 = $this->pdoExecutorWithConnection("select @median", array(), $connection);
		if (sizeof($result) > 0) {
			$returnValue = current(reset($result2));
		}
		return $returnValue;
	}

	public function peiHistoric($processId, $initDate, $endDate, $periodicity) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		/*$sqlString = $this->indicatorsBasicQueryBuilder(IndicatorDataSourcesEnum::USER
			, $processId, $periodicity, $initDate, $endDate
			, $this->peiFormula);*/
	
		$qryParams = Array();
		$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::USER
			, $processId, $periodicity, $initDate, $endDate
			, $this->peiFormula." As VALUE", $qryParams);

		//$returnValue = $this->propelExecutor($sqlString);

		$returnValue = $this->pdoExecutor($sqlString, $qryParams);
		return $returnValue;
	}

	public function indicatorData($indicatorId)
	{
		$qryParams = Array();
		$qryParams[':indicatorId'] = $indicatorId;
		$sqlString = "select * from DASHBOARD_INDICATOR  where DAS_IND_UID= :indicatorId";
		$returnValue = $this->pdoExecutor($sqlString, $qryParams);

		/*$sqlString = "select * from DASHBOARD_INDICATOR  where DAS_IND_UID= '$indicatorId'";
		$retval = $this->propelExecutor($sqlString);*/
		return $returnValue;
	}

	public function peiProcesses($indicatorId, $initDate, $endDate, $language)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;
		$params[":language"] = $language;

		$sqlString = "select
                        i.PRO_UID as uid,
                        tp.CON_VALUE as name,
                        efficiencyIndex,
                        inefficiencyCost,
						@curRow := @curRow + 1 AS rank
                    from
                    (	select
                            PRO_UID,
                            $this->peiFormula as efficiencyIndex,
                            $this->peiCostFormula as inefficiencyCost
                        from  USR_REPORTING
                            WHERE
							(
								PRO_UID = (select DAS_UID_PROCESS from  DASHBOARD_INDICATOR where DAS_IND_UID = '$indicatorId')
								or
								(select DAS_UID_PROCESS from  DASHBOARD_INDICATOR where DAS_IND_UID = '$indicatorId')= '0'
							)
							AND
								IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)
                        group by PRO_UID
						order by $this->peiFormula DESC
                    ) i
                    left join (select *
								from CONTENT
								where CON_CATEGORY = 'PRO_TITLE'
										and CON_LANG = :language
									) tp on i.PRO_UID = tp.CON_ID
					join  (SELECT @curRow := 0) order_table"; 

		//$retval = $this->propelExecutor($sqlString);
		$retval = $this->pdoExecutor($sqlString, $params);
		return $retval;
	}

	public function ueiUserGroups($indicatorId, $initDate, $endDate, $language)
	{
		//for the moment all the indicator summarizes ALL users, so indicatorId is not used in this function.
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;
		$params[":language"] = $language;

		//TODO ADD to USR_REPORTING the user's Group to speed up the query.
		$sqlString = "
				select
						 IFNULL(i.GRP_UID, '0') as uid,
						 IFNULL(tp.CON_VALUE, 'No Group') as name,
						 efficiencyIndex,
						 inefficiencyCost,
						 averageTime,
						 deviationTime,
						 @curRow := @curRow + 1 AS rank
				from
				(	select
					   gu.GRP_UID,
					   $this->ueiFormula as efficiencyIndex,
					   $this->ueiCostFormula as inefficiencyCost,
					   AVG(AVG_TIME) as averageTime,
					   AVG(SDV_TIME) as deviationTime 
				   from  USR_REPORTING ur
				   left join
				   GROUP_USER gu on gu.USR_UID = ur.USR_UID
				   WHERE
					IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)
				   group by gu.GRP_UID
					order by $this->ueiFormula DESC
				) i
				left join (select *
								from CONTENT
							where CON_CATEGORY = 'GRP_TITLE'
									and CON_LANG = :language 
						   ) tp on i.GRP_UID = tp.CON_ID
				join  (SELECT @curRow := 0) order_table"; 

		$retval = $this->pdoExecutor($sqlString, $params);
		//$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function groupEmployeesData($groupId, $initDate, $endDate, $language)
	{
		//TODO what if we are analizing empty user group (users without group)
		//for the moment all the indicator summarizes ALL users, so indicatorId is not used in this function.
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;
		$params[":language"] = $language;
		$params[":groupId"] = $groupId;

		$sqlString = " select
						   i.USR_UID as uid,
						   i.name,
						   efficiencyIndex,
						   inefficiencyCost,
						   averageTime,
						   deviationTime,
						 @curRow := @curRow + 1 AS rank
						from
						(	select
							   u.USR_UID,
							   concat(u.USR_FIRSTNAME, ' ', u.USR_LASTNAME) as name,
							   $this->ueiFormula as efficiencyIndex,
							   $this->ueiCostFormula as inefficiencyCost,
							   AVG(AVG_TIME) as averageTime,
							   AVG(SDV_TIME) as deviationTime 
						   from  USR_REPORTING ur
						   left join
							   GROUP_USER gu on gu.USR_UID = ur.USR_UID
						   LEFT JOIN USERS u on u.USR_UID = ur.USR_UID
						   where (gu.GRP_UID = :groupId or (:groupId = '0' && gu.GRP_UID is null ))
							   AND
								IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)
						   group by ur.USR_UID
							order by $this->ueiFormula DESC
						) i
						join  (SELECT @curRow := 0) order_table"; 
		$retval = $this->pdoExecutor($sqlString, $params);
		//$returnValue = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function ueiHistoric($employeeId, $initDate, $endDate, $periodicity)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$qryParams = Array();
		$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::USER
			, $employeeId, $periodicity, $initDate, $endDate
			, $this->ueiFormula." as VALUE", $qryParams);

		$retval = $this->pdoExecutor($sqlString, $qryParams);
		//$returnValue = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function peiCostHistoric($processId, $initDate, $endDate, $periodicity)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;
		$params[":processId"] = $processId;

		$filterCondition = "";
		if ($processId != null && $processId > 0) {
			$filterCondition = " AND PRO_UID =  :processId";
		}

		$sqlString = "SELECT $periodicitySelectFields " . $this->peiCostFormula . " as PEC
						FROM  USR_REPORTING
						WHERE
							IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)"
						. $filterCondition
						. $periodicityGroup;

		$retval = $this->pdoExecutor($sqlString, $params);
		//$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function ueiCostHistoric($employeeId, $initDate, $endDate, $periodicity)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;


		$sqlString = "SELECT $periodicitySelectFields " . $this->ueiCostFormula . " as EEC
						FROM  USR_REPORTING
						WHERE
							IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)"
						. $periodicityGroup;

		$retval = $this->pdoExecutor($sqlString, $params);
		//$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	//TODO: delte this function that is used nowhere
	public function generalIndicatorData($indicatorId, $initDate, $endDate, $periodicity) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$arrayT = $this->indicatorData($indicatorId);
		if (sizeof($arrayT) == 0 ) {
			return array();
		}

		$indicator = $arrayT[0];
		$indicatorProcessId = $indicator["DAS_UID_PROCESS"];
		$indicatorType = $indicator["DAS_IND_TYPE"];
		if ($indicatorProcessId == "0" || strlen($indicatorProcessId) ==0) {
			$indicatorProcessId = null;
		}

        $graph1 = $indicator['DAS_IND_FIRST_FIGURE'];
        $freq1 = $indicator['DAS_IND_FIRST_FREQUENCY'];
        $graph2 = $indicator['DAS_IND_SECOND_FIGURE'];
        $freq2 = $indicator['DAS_IND_SECOND_FREQUENCY'];

		$graph1XLabel = G::loadTranslation(ReportingPeriodicityEnum::labelFromValue($freq1));
		$graph1YLabel = "Value";

		$graph2XLabel = G::loadTranslation(ReportingPeriodicityEnum::labelFromValue($freq2));
		$graph2YLabel = "Value";

		$graphConfigurationString = "'$graph1XLabel' as graph1XLabel,  
										'$graph1YLabel' as graph1YLabel, 
										'$graph2XLabel' as graph2XLabel,  
										'$graph2YLabel' as graph2YLabel, 
										'$graph1' as graph1Type, 
										'$freq1' as frequency1Type, 
										'$graph2' as graph2Type, 
										'$freq2' as frequency2Type,";

		$params = Array();

        switch ($indicatorType) {
			//process inefficience
			case "1020":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_TIME_BY_TASK) / SUM(CONFIGURED_TASK_TIME) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::USER
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			//employee inefficience
			case "1040":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_TIME_BY_TASK) / SUM(CONFIGURED_TASK_TIME) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::USER
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			//overdue
			case "1050":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_OVERDUE) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::USER
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			//new cases
			case "1060":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_IN) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::PROCESS
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			//completed
			case "1070":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_OUT) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::PROCESS
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			case "1080":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_OPEN) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				$sqlString = $this->indicatorsParamsQueryBuilder(IndicatorDataSourcesEnum::PROCESS
									, $indicatorProcessId, $periodicity
									, $initDate, $endDate
									, $calcField, $params);
				break;
			default:
				throw new Exception(" The indicator id '$indicatorId' with type $indicatorType hasn't an associated operation.");
		}

		$retval = $this->pdoExecutor($sqlString, $params);
		//$returnValue = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function peiTasks($processList, $initDate, $endDate, $language)
	{
		$processCondition = "";
		if ($processList != null && sizeof($processList) > 0) {
			$processCondition = " WHERE PRO_UID IN " . "('" . implode("','", $processList) . "')";
		}
		$params[':language'] = $language;

		$sqlString = " select
                            i.TAS_UID as uid,
                            t.CON_VALUE as name,
                            i.efficiencyIndex,
                            i.inefficiencyCost,
                            i.averageTime,
                            i.deviationTime,
                            i.configuredTime
                         FROM
                        (	select
                                TAS_UID,
                                $this->peiFormula as efficiencyIndex,
								$this->peiCostFormula as inefficiencyCost,
                                AVG(AVG_TIME) as averageTime,
                                AVG(SDV_TIME) as deviationTime,
                                CONFIGURED_TASK_TIME as configuredTime
                            from USR_REPORTING
                            $processCondition
                            group by TAS_UID
                        ) i
                        left join (select *
                                            from CONTENT
                                            where CON_CATEGORY = 'TAS_TITLE'
                                                    and CON_LANG = :language 
                                    ) t on i.TAS_UID = t.CON_ID";
		$retval = $this->pdoExecutor($sqlString, $params);
		//$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

    public function statusIndicatorGeneral ($usrUid)
    {
        $params[':usrUid'] = $usrUid;

        $sqlString = "SELECT
            COALESCE( SUM( TIMEDIFF( DEL_DUE_DATE , NOW( ) ) <  0 ) , 0 ) AS OVERDUE,
            COALESCE( SUM( TIMEDIFF( DEL_RISK_DATE , NOW( ) ) >  0 ) , 0 ) AS ONTIME,
            COALESCE( SUM( TIMEDIFF( DEL_RISK_DATE , NOW( ) ) < 0 && TIMEDIFF( DEL_DUE_DATE , NOW( ) ) >  0) , 0 ) AS ATRISK
            FROM  LIST_INBOX
            WHERE  USR_UID =  :usrUid
            AND APP_STATUS = 'TO_DO'
            AND  DEL_DUE_DATE IS NOT NULL ";

        return $this->pdoExecutor($sqlString, $params);
    }

    public function statusIndicatorDetail ($usrUid)
    {
        $params[':usrUid'] = $usrUid;

        $sqlString = "SELECT
            TAS_UID as tasUid,
            PRO_UID as proUid,
            APP_TAS_TITLE AS taskTitle,
            APP_PRO_TITLE AS proTitle,

            COALESCE( SUM( TIMEDIFF( DEL_DUE_DATE , NOW( ) ) <  0 ) , 0 ) AS overdue,
            COALESCE( SUM( TIMEDIFF( DEL_RISK_DATE , NOW( ) ) >  0 ) , 0 ) AS onTime,
            COALESCE( SUM( TIMEDIFF( DEL_RISK_DATE , NOW( ) ) < 0 && TIMEDIFF( DEL_DUE_DATE , NOW( ) ) >  0) , 0 ) AS atRisk
            FROM  LIST_INBOX
            WHERE  USR_UID =  :usrUid
            AND APP_STATUS = 'TO_DO'
            AND  DEL_DUE_DATE IS NOT NULL
            GROUP BY TAS_UID";

        return $this->pdoExecutor($sqlString, $params);
    }

    public function statusIndicator($usrUid)
    {
        $response = array();
        $result = $this->statusIndicatorGeneral($usrUid);

        $response['overdue'] = 0;
        $response['atRisk'] = 0;
        $response['onTime'] = 0;
        $response['percentageOverdue'] = 0;
        $response['percentageAtRisk'] = 0;
        $response['percentageOnTime'] = 0;
        $response['dataList'] = array();

        if (is_array($result) && isset($result[0])) {
            $response['overdue'] = $result[0]['OVERDUE'];
            $response['atRisk'] = $result[0]['ATRISK'];
            $response['onTime'] = $result[0]['ONTIME'];

            $total = $response['overdue'] + $response['atRisk'] + $response['onTime'];
            if ($total != 0) {
                $response['percentageOverdue'] = ($response['overdue']*100)/$total;
                $response['percentageAtRisk']  = ($response['atRisk']*100)/$total;
                $response['percentageOnTime']  = ($response['onTime']*100)/$total;
            }
        }

        $result = $this->statusIndicatorDetail($usrUid);

        foreach ($result as $key => $value) {
            $result[$key]['overdue'] = $value['overdue'];
            $result[$key]['atRisk'] = $value['atRisk'];
            $result[$key]['onTime'] = $value['onTime'];
            $result[$key]['percentageOverdue'] = 0;
            $result[$key]['percentageAtRisk']  = 0;
            $result[$key]['percentageOnTime']  = 0;
            $result[$key]['percentageTotalOverdue'] = 0;
            $result[$key]['percentageTotalAtRisk']  = 0;
            $result[$key]['percentageTotalOnTime']  = 0;
            $total = $value['overdue'] + $value['onTime'] + $value['atRisk'];
            if ($total != 0) {
                $result[$key]['percentageOverdue'] = ($value['overdue']*100)/$total;
                $result[$key]['percentageAtRisk']  = ($value['atRisk']*100)/$total;
                $result[$key]['percentageOnTime']  = ($value['onTime']*100)/$total;
                $result[$key]['percentageTotalOverdue'] = $response['overdue'] != 0 ? ($value['overdue']*100)/$response['overdue']: 0;
                $result[$key]['percentageTotalAtRisk']  = $response['atRisk'] != 0 ? ($value['atRisk']*100)/$response['atRisk'] : 0;
                $result[$key]['percentageTotalOnTime']  = $response['onTime'] != 0 ? ($value['onTime']*100)/$response['onTime']: 0;
            }
        }
        $response['dataList'] = $result;
        return $response;
    }

	private function periodicityFieldsForSelect($periodicity) {
		$periodicityFields = $this->periodicityFieldsString($periodicity);
		//add a comma if there are periodicity fields
		return $periodicityFields
		. ((strlen($periodicityFields) > 0)
			? ", "
			: "");
	}

	private function periodicityFieldsForGrouping($periodicity) {
		$periodicityFields = $this->periodicityFieldsString($periodicity);

		return ((strlen($periodicityFields) > 0)
			? " GROUP BY "
			: "") . str_replace(" AS QUARTER", "", str_replace(" AS SEMESTER", "", $periodicityFields));
	}

	private function periodicityFieldsString($periodicity) {
		if (!ReportingPeriodicityEnum::isValidValue($periodicity)) throw new ArgumentException('Not supported periodicity: ', 0, 'periodicity');

		$retval = "";
		switch ($periodicity) {
			case ReportingPeriodicityEnum::MONTH;
				$retval = "`YEAR`, `MONTH` ";
				break;
			case ReportingPeriodicityEnum::SEMESTER;
				$retval = "`YEAR`, IF (`MONTH` <= 6, 1, 2) AS SEMESTER";
				break;
			case ReportingPeriodicityEnum::QUARTER;
				$retval = "`YEAR`,  CASE WHEN `MONTH` BETWEEN 1 AND 3 THEN 1 WHEN `MONTH` BETWEEN 4 AND 6 THEN 2 WHEN `MONTH` BETWEEN 7 AND 9 THEN 3 WHEN `MONTH` BETWEEN 10 AND 12 THEN 4 END AS QUARTER";
				break;
			case ReportingPeriodicityEnum::YEAR;
				$retval = "`YEAR`  ";
				break;
		}
		return $retval;
	}

	private function pdoExecutor($sqlString, $params) {

		$connection = $this->pdoConnection ();
		$result = $this->pdoExecutorWithConnection($sqlString, $params, $connection);
		return $result;
	}

	private function pdoConnection() {
	    $currentWS = defined('SYS_SYS') ? SYS_SYS : 'Wokspace Undefined';
		$workSpace = new WorkspaceTools($currentWS);
  	        $arrayHost = explode(':', $workSpace->dbHost);
		$host = "host=".$arrayHost[0];
		$port = count($arrayHost) > 1 ? ";port=".$arrayHost[1] : "";
		$db = ";dbname=".$workSpace->dbName;
		$user = $workSpace->dbUser;
		$pass = $workSpace->dbPass;
		$connString = "mysql:$host$port$db;charset=utf8;";

		$dbh = new PDO($connString, $user, $pass);
		return $dbh;
	}

	private function pdoExecutorWithConnection($sqlString, $params, $connection) {
		$statement = $connection->prepare($sqlString);
		$statement->execute($params);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	private function indicatorsParamsQueryBuilder($reportingTable, $filterId, $periodicity, $initDate, $endDate, $fields, &$params) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$tableMetadata = $this->metadataForTable($reportingTable);
		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		$filterCondition = "";
		if ($filterId != null && $filterId > 0) {
			$filterCondition = " AND ".$tableMetadata["keyField"]." = '$filterId'";
		}

		//$params[":initYear"] = $initYear;
		//$params[":initMonth"] = $initMonth;
		$params[":endYear"] = $endYear;
		$params[":endMonth"] = $endMonth;

		$sqlString = "SELECT $periodicitySelectFields $fields
						FROM  ".$tableMetadata["tableName"].
					" WHERE
						IF(`YEAR` = :endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = :endYear, :endMonth, :endYear)"
					. $filterCondition
					. $periodicityGroup;
		return $sqlString;
	}


	private function metadataForTable($table)  {
		$returnVal = null;
		switch (strtolower($table)) {
			case IndicatorDataSourcesEnum::USER:
				$returnVal = $this->userReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::PROCESS:
				$returnVal = $this->processReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::USER_GROUP:
				$returnVal = $this->userGroupReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::PROCESS_CATEGORY:
				$returnVal = $this->processCategoryReportingMetadata;
				break;
		}
		if ($returnVal == null) {
			throw new Exception("'$table' it's not supportes. It has not associated a template.");
		}
		return $returnVal;
	}

    public function suggestedTimeForTask ($taskId) {
		$qryParams = Array();
		$qryParams[':taskId'] = $taskId;
		$sqlString = "select 
				ROUND(AVG(TOTAL_TIME_BY_TASK/TOTAL_CASES_OUT), 2) as average,
				 ROUND(STDDEV(TOTAL_TIME_BY_TASK/TOTAL_CASES_OUT), 2) as sdv
				from USR_REPORTING  where TAS_UID = :taskId";
		$retval = $this->pdoExecutor($sqlString, $qryParams);
		return $retval[0];
    }


	
	/* For debug only:
	 * public function interpolateQuery($query, $params) {
		$keys = array();
		# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			echo "<br>key", $key, " -- value", $value;
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}
		}
		$query = preg_replace($keys, $params, $query, 1, $count);
		return $query;
	}*/
}


