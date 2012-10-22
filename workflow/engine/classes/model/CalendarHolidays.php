<?php
/**
 * CalendarHolidays.php
 * @package    workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseCalendarHolidays.php';

/**
 * Skeleton subclass for representing a row from the 'CALENDAR_HOLIDAYS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class CalendarHolidays extends BaseCalendarHolidays
{
    public function getCalendarHolidays ($CalendarUid)
    {
        $Criteria = new Criteria('workflow');
        $Criteria->clearSelectColumns ( );

        $Criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_UID );
        $Criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_NAME );
        $Criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_START );
        $Criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_END );

        $Criteria->add (  CalendarHolidaysPeer::CALENDAR_UID, $CalendarUid , CRITERIA::EQUAL );

        $rs = CalendarHolidaysPeer::doSelectRS($Criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $fields=array();
        $count=0;
        while (is_array($row)) {
            $count++;
            $a=explode(" ",$row['CALENDAR_HOLIDAY_START']);
            $row['CALENDAR_HOLIDAY_START']=$a[0];
            $a=explode(" ",$row['CALENDAR_HOLIDAY_END']);
            $row['CALENDAR_HOLIDAY_END']=$a[0];
            $fields[$count] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $fields;
    }

    public function deleteAllCalendarHolidays($CalendarUid)
    {
        $toDelete=$this->getCalendarHolidays($CalendarUid);
        foreach ($toDelete as $key => $holidayInfo) {
            $CalendarUid = $holidayInfo['CALENDAR_UID'];
            $CalendarHolidayName = $holidayInfo['CALENDAR_HOLIDAY_NAME'];
            $CalendarHolidayStart = $holidayInfo['CALENDAR_HOLIDAY_START'];
            $CalendarHolidayEnd = $holidayInfo['CALENDAR_HOLIDAY_END'];
            //if exists the row in the database propel will update it, otherwise will insert.

            $tr = CalendarHolidaysPeer::retrieveByPK ( $CalendarUid,$CalendarHolidayName );
            if (  ( is_object ( $tr ) &&  get_class ($tr) == 'CalendarHolidays' ) ) {
                $tr->delete();
            }
        }
    }

    public function saveCalendarHolidays($aData)
    {
        $CalendarUid = $aData['CALENDAR_UID'];
        $CalendarHolidayName = $aData['CALENDAR_HOLIDAY_NAME'];
        $CalendarHolidayStart = $aData['CALENDAR_HOLIDAY_START'];
        $CalendarHolidayEnd = $aData['CALENDAR_HOLIDAY_END'];

        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarHolidaysPeer::retrieveByPK ( $CalendarUid,$CalendarHolidayName);
        if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'CalendarHolidays' ) ) {
            $tr = new CalendarHolidays();
        }

        $tr->setCalendarUid( $CalendarUid );
        $tr->setCalendarHolidayName( $CalendarHolidayName );
        $tr->setCalendarHolidayStart( $CalendarHolidayStart );
        $tr->setCalendarHolidayEnd( $CalendarHolidayEnd );

        if ($tr->validate() ) {
            $res = $tr->save();
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $tr->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }

        //to do: uniform  coderror structures for all classes
    }
}

