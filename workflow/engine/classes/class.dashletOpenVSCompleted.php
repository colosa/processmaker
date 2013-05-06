<?php

require_once 'classes/interfaces/dashletInterface.php';

class dashletOpenVSCompleted implements DashletInterface
{

    const version = '1.0';

    private $value;
    private $open;
    private $completed;
    private $centerLabel;
    private $redFrom;
    private $redTo;
    private $yellowFrom;
    private $yellowTo;
    private $greenFrom;
    private $greenTo;

    public static function getAdditionalFields ($className)
    {
        $additionalFields = array ();

        $contextTimeStore = new stdclass();
        $contextTimeStore->xtype = 'arraystore';
        $contextTimeStore->fields = array ('id','value'
        );
        if (defined('PARTNER_FLAG')) {
            $contextTimeStore->data = array (array ('TODAY','Hoje'
            ),array ('YESTERDAY','Ontem'
            ),array ('THIS_WEEK','Essa semana'
            ),array ('PREVIOUS_WEEK','Semana anterior'
            ),array ('THIS_MONTH','Este mês'
            ),array ('PREVIOUS_MONTH','Mês Anterior'
            ),array ('THIS_YEAR','Este ano'
            ),array ('PREVIOUS_YEAR','Ano anterior'
            )
            );
        } else {
            $contextTimeStore->data = array (array ('TODAY','Today'
            ),array ('YESTERDAY','Yesterday'
            ),array ('THIS_WEEK','This week'
            ),array ('PREVIOUS_WEEK','Previous Week'
            ),array ('THIS_MONTH','This Month'
            ),array ('PREVIOUS_MONTH','Previous Month'
            ),array ('THIS_YEAR','This Year'
            ),array ('PREVIOUS_YEAR','Previous Year'
            )
            );
        }

        $contextTime = new stdclass();
        $contextTime->xtype = 'combo';
        $contextTime->name = 'DAS_INS_CONTEXT_TIME';
        $contextTime->fieldLabel = G::LoadTranslation( 'ID_PERIOD' );
        $contextTime->editable = false;
        $contextTime->width = 320;
        $contextTime->store = $contextTimeStore;
        $contextTime->mode = 'local';
        $contextTime->triggerAction = 'all';
        $contextTime->valueField = 'id';
        $contextTime->displayField = 'value';
        $contextTime->value = 'TODAY';
        $additionalFields[] = $contextTime;

        $redFrom = new stdclass();
        $redFrom->xtype = 'numberfield';
        $redFrom->name = 'DAS_RED_FROM';
        $redFrom->fieldLabel = G::LoadTranslation( 'ID_RED_STARTS_IN' );
        $redFrom->width = 50;
        $redFrom->maxLength = 3;
        $redFrom->maxValue = 100;
        $redFrom->minValue = 0;
        $redFrom->allowBlank = false;
        $redFrom->value = 0;
        $additionalFields[] = $redFrom;

        $redTo = new stdclass();
        $redTo->xtype = 'numberfield';
        $redTo->name = 'DAS_RED_TO';
        $redTo->fieldLabel = G::LoadTranslation( 'ID_RED_ENDS_IN' );
        $redTo->width = 50;
        $redTo->maxLength = 3;
        $redTo->maxValue = 100;
        $redTo->minValue = 0;
        $redTo->allowBlank = false;
        $redTo->value = 30;
        $additionalFields[] = $redTo;

        $yellowFrom = new stdclass();
        $yellowFrom->xtype = 'numberfield';
        $yellowFrom->name = 'DAS_YELLOW_FROM';
        $yellowFrom->fieldLabel = G::LoadTranslation( 'ID_YELLOW_STARTS_IN' );
        $yellowFrom->width = 50;
        $yellowFrom->maxLength = 3;
        $yellowFrom->maxValue = 100;
        $yellowFrom->minValue = 0;
        $yellowFrom->allowBlank = false;
        $yellowFrom->value = 30;
        $additionalFields[] = $yellowFrom;

        $yellowTo = new stdclass();
        $yellowTo->xtype = 'numberfield';
        $yellowTo->name = 'DAS_YELLOW_TO';
        $yellowTo->fieldLabel = G::LoadTranslation( 'ID_YELLOW_ENDS_IN' );
        $yellowTo->width = 50;
        $yellowTo->maxLength = 3;
        $yellowTo->maxValue = 100;
        $yellowTo->minValue = 0;
        $yellowTo->allowBlank = false;
        $yellowTo->value = 50;
        $additionalFields[] = $yellowTo;

        $greenFrom = new stdclass();
        $greenFrom->xtype = 'numberfield';
        $greenFrom->name = 'DAS_GREEN_FROM';
        $greenFrom->fieldLabel = G::LoadTranslation( 'ID_GREEN_STARTS_IN' );
        $greenFrom->width = 50;
        $greenFrom->maxLength = 3;
        $greenFrom->maxValue = 100;
        $greenFrom->minValue = 0;
        $greenFrom->allowBlank = false;
        $greenFrom->value = 50;
        $additionalFields[] = $greenFrom;

        $greenTo = new stdclass();
        $greenTo->xtype = 'numberfield';
        $greenTo->name = 'DAS_GREEN_TO';
        $greenTo->fieldLabel = G::LoadTranslation( 'ID_GREEN_ENDS_IN' );
        $greenTo->width = 50;
        $greenTo->maxLength = 3;
        $greenTo->maxValue = 100;
        $greenTo->minValue = 0;
        $greenTo->allowBlank = false;
        $greenTo->value = 100;
        $additionalFields[] = $greenTo;

        return $additionalFields;
    }

    public static function getXTemplate ($className)
    {
        return "<img src='{page}?w={width}&r={random}&DAS_INS_UID={id}'>";
    }

    public function setup ($config)
    {
        $this->redFrom = isset( $config['DAS_RED_FROM'] ) ? (int) $config['DAS_RED_FROM'] : 0;
        $this->redTo = isset( $config['DAS_RED_TO'] ) ? (int) $config['DAS_RED_TO'] : 30;
        $this->yellowFrom = isset( $config['DAS_YELLOW_FROM'] ) ? (int) $config['DAS_YELLOW_FROM'] : 30;
        $this->yellowTo = isset( $config['DAS_YELLOW_TO'] ) ? (int) $config['DAS_YELLOW_TO'] : 50;
        $this->greenFrom = isset( $config['DAS_GREEN_FROM'] ) ? (int) $config['DAS_GREEN_FROM'] : 50;
        $this->greenTo = isset( $config['DAS_GREEN_TO'] ) ? (int) $config['DAS_GREEN_TO'] : 100;

        $thisYear = date( 'Y' );
        $lastYear = $thisYear - 1;
        $thisMonth = date( 'M' );
        $lastMonth = date( 'M', strtotime( "31 days ago" ) );

        $todayIni = date( 'Y-m-d H:i:s', strtotime( "today 00:00:00" ) );
        $todayEnd = date( 'Y-m-d H:i:s', strtotime( "today 23:59:59" ) );
        $yesterdayIni = date( 'Y-m-d H:i:s', strtotime( "yesterday 00:00:00" ) );
        $yesterdayEnd = date( 'Y-m-d H:i:s', strtotime( "yesterday 23:59:59" ) );
        $thisWeekIni = date( 'Y-m-d H:i:s', strtotime( "monday 00:00:00" ) );
        $thisWeekEnd = date( 'Y-m-d H:i:s', strtotime( "sunday 23:59:59" ) );
        $previousWeekIni = date( 'Y-m-d H:i:s', strtotime( "last monday 00:00:00" ) );
        $previousWeekEnd = date( 'Y-m-d H:i:s', strtotime( "last sunday 23:59:59" ) );

        $thisMonthIni = date( 'Y-m-d H:i:s', strtotime( "$thisMonth 1st 00:00:00" ) );
        $thisMonthEnd = date( 'Y-m-d H:i:s', strtotime( "$thisMonth last day 23:59:59" ) );

        $previousMonthIni = date( 'Y-m-d H:i:s', strtotime( "$lastMonth 1st 00:00:00" ) );
        $previousMonthEnd = date( 'Y-m-d H:i:s', strtotime( "$lastMonth last day 23:59:59" ) );

        $thisYearIni = date( 'Y-m-d H:i:s', strtotime( "jan $thisYear 00:00:00" ) );
        $thisYearEnd = date( 'Y-m-d H:i:s', strtotime( "Dec 31 $thisYear 23:59:59" ) );
        $previousYearIni = date( 'Y-m-d H:i:s', strtotime( "jan $lastYear 00:00:00" ) );
        $previousYearEnd = date( 'Y-m-d H:i:s', strtotime( "Dec 31 $lastYear 23:59:59" ) );

        if (! isset( $config['DAS_INS_CONTEXT_TIME'] )) {
            $config['DAS_INS_CONTEXT_TIME'] = 'TODAY';
        }

        switch ($config['DAS_INS_CONTEXT_TIME']) {
            case 'TODAY':
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case 'YESTERDAY':
                $dateIni = $yesterdayIni;
                $dateEnd = $yesterdayEnd;
                break;
            case 'THIS_WEEK':
                $dateIni = $thisWeekIni;
                $dateEnd = $thisWeekEnd;
                break;
            case 'PREVIOUS_WEEK':
                $dateIni = $previousWeekIni;
                $dateEnd = $previousWeekEnd;
                break;
            case 'THIS_MONTH':
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case 'PREVIOUS_MONTH':
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case 'THIS_QUARTER':
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case 'PREVIOUS_QUARTER':
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case 'THIS_YEAR':
                $dateIni = $thisYearIni;
                $dateEnd = $thisYearEnd;
                break;
            case 'PREVIOUS_YEAR':
                $dateIni = $previousYearIni;
                $dateEnd = $previousYearEnd;
                break;
        }

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->createStatement();
        $sql = "select count(*) as CANT from APPLICATION where APP_STATUS in ( 'DRAFT', 'TO_DO' ) ";
        $sql .= "and APP_CREATE_DATE > '$dateIni' and APP_CREATE_DATE <= '$dateEnd' ";
        $rs = $stmt->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();
        $casesTodo = $row['CANT'];

        $stmt = $con->createStatement();
        $sql = "select count(*) as CANT from APPLICATION where APP_STATUS = 'COMPLETED' ";
        $sql .= "and APP_CREATE_DATE > '$dateIni' and APP_CREATE_DATE <= '$dateEnd' ";
        $rs = $stmt->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();
        $casesCompleted = $row['CANT'];
        if ($casesCompleted + $casesTodo != 0) {
            $this->value = $casesCompleted / ($casesCompleted + $casesTodo) * 100;
        } else {
            $this->value = 0;
        }
        $this->open = $casesCompleted;
        $this->completed = $casesCompleted + $casesTodo;
        switch ($config['DAS_INS_CONTEXT_TIME']) {
            case 'TODAY':
                $this->centerLabel = 'Today';
                break;
            case 'YESTERDAY':
                $this->centerLabel = 'Yesterday';
                break;
            case 'THIS_WEEK':
                $this->centerLabel = 'This week';
                break;
            case 'PREVIOUS_WEEK':
                $this->centerLabel = 'Previous week';
                break;
            case 'THIS_MONTH':
                $this->centerLabel = 'This month';
                break;
            case 'PREVIOUS_MONTH':
                $this->centerLabel = 'Previous month';
                break;
            case 'THIS_QUARTER':
                $this->centerLabel = 'This quarter';
                break;
            case 'PREVIOUS_QUARTER':
                $this->centerLabel = 'Previous quarter';
                break;
            case 'THIS_YEAR':
                $this->centerLabel = 'This year';
                break;
            case 'PREVIOUS_YEAR':
                $this->centerLabel = 'Previous year';
                break;
            default:
                $this->centerLabel = '';
                break;
        }
        return true;
    }

    public function render ($width = 300)
    {
        G::LoadClass( 'pmGauge' );
        $g = new pmGauge();
        $g->w = $width;
        $g->value = $this->value;

        $g->redFrom = $this->redFrom;
        $g->redTo = $this->redTo;
        $g->yellowFrom = $this->yellowFrom;
        $g->yellowTo = $this->yellowTo;
        $g->greenFrom = $this->greenFrom;
        $g->greenTo = $this->greenTo;

        $g->centerLabel = $this->centerLabel;
        $g->open = $this->open;
        $g->completed = $this->completed;
        $g->render();
    }

}