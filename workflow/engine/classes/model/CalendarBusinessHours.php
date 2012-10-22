<?php
/**
 * CalendarBusinessHours.php
 * @package    workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseCalendarBusinessHours.php';


/**
 * Skeleton subclass for representing a row from the 'CALENDAR_BUSINESS_HOURS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class CalendarBusinessHours extends BaseCalendarBusinessHours
{
    public function getCalendarBusinessHours($CalendarUid)
    {
        $Criteria = new Criteria('workflow');
        $Criteria->clearSelectColumns ( );

        $Criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_UID );
        $Criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_DAY );
        $Criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_START );
        $Criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_END );

        $Criteria->add (  CalendarBusinessHoursPeer::CALENDAR_UID, $CalendarUid , CRITERIA::EQUAL );
        $Criteria->addDescendingOrderByColumn ( CalendarBusinessHoursPeer::CALENDAR_BUSINESS_DAY );
        $Criteria->addAscendingOrderByColumn ( CalendarBusinessHoursPeer::CALENDAR_BUSINESS_START );

        $rs = CalendarBusinessHoursPeer::doSelectRS($Criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $fields=array();
        $count=0;
        while (is_array($row)) {
            $count++;
            $fields[$count] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $fields;
    }

    public function deleteAllCalendarBusinessHours($CalendarUid)
    {
        $toDelete = $this->getCalendarBusinessHours($CalendarUid);
        foreach ($toDelete as $key => $businessHoursInfo) {
            $CalendarUid = $businessHoursInfo['CALENDAR_UID'];
            $CalendarBusinessDay = $businessHoursInfo['CALENDAR_BUSINESS_DAY'];
            $CalendarBusinessStart = $businessHoursInfo['CALENDAR_BUSINESS_START'];
            $CalendarBusinessEnd = $businessHoursInfo['CALENDAR_BUSINESS_END'];
            //if exists the row in the database propel will update it, otherwise will insert.
            $tr = CalendarBusinessHoursPeer::retrieveByPK ( $CalendarUid,$CalendarBusinessDay, $CalendarBusinessStart,$CalendarBusinessEnd );
            if (( is_object ( $tr ) &&  get_class ($tr) == 'CalendarBusinessHours' ) ) {
                $tr->delete();
            }
        }
    }

    public function saveCalendarBusinessHours($aData)
    {
        $CalendarUid = $aData['CALENDAR_UID'];
        $CalendarBusinessDay = $aData['CALENDAR_BUSINESS_DAY'];
        $CalendarBusinessStart = $aData['CALENDAR_BUSINESS_START'];
        $CalendarBusinessEnd = $aData['CALENDAR_BUSINESS_END'];

        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarBusinessHoursPeer::retrieveByPK ( $CalendarUid,$CalendarBusinessDay, $CalendarBusinessStart,$CalendarBusinessEnd );
        if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'CalendarBusinessHours' ) ) {
            $tr = new CalendarBusinessHours();
        }

        $tr->setCalendarUid( $CalendarUid );
        $tr->setCalendarBusinessDay( $CalendarBusinessDay );
        $tr->setCalendarBusinessStart( $CalendarBusinessStart );
        $tr->setCalendarBusinessEnd( $CalendarBusinessEnd );

        if ($tr->validate() ) {
            // we save it, since we get no validation errors, or do whatever else you like.
            $res = $tr->save();
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = $CalendarBusinessDay.'<hr/>';
            $validationFailuresArray = $tr->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
            G::SendTemporalMessage($msg);
        }
    }
}

