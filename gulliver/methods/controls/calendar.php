<style>
/**
 * calendar.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
	td {font-family: Tahoma, Verdana, sans-serif; font-size: 11px;}
</style>
<?php

	$ARR_MONTHS = array ( "January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December");
	$ARR_WEEKDAYS = array ( "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" );

	$NUM_WEEKSTART = 1;  //day week starts from (normally 0-Su or 1-Mo)
	$STR_ICONPATH = '/controls/';

  $prevYear  = $STR_ICONPATH . 'prev_year.gif';
  $prevMonth = $STR_ICONPATH . 'prev.gif';
  $nextMonth = $STR_ICONPATH . 'next.gif';
  $nextYear  = $STR_ICONPATH . 'next_year.gif';

  if ( isset ( $_GET['v'] ) && $_GET['v'] != '' ) {
		$dt_value     = $_GET['v'];
		$dt_aux  = explode ( '-', $dt_value );
 		$dt_valueDay   = $dt_aux[2];
 		$dt_valueMonth = $dt_aux[1];
		$dt_valueYear  = $dt_aux[0];
	}
	else
	  $dt_value = '';

  if ( isset ( $_GET['d'] ) ) {
		$dt_current    = $_GET['d'];
		$dt_aux  = explode ( '-', $dt_current );
 		$dt_currentDay   = $dt_aux[2];
 		$dt_currentMonth = $dt_aux[1];
		$dt_currentYear  = $dt_aux[0];
		if ($dt_currentYear=='0000') $dt_currentYear = date ('Y');
		if ($dt_currentMonth=='00') $dt_currentMonth = date ('m');
		if ($dt_currentDay=='00') $dt_currentDay = date ('d');
	}
	else {
  	$dt_current    = date('Y-m-d');
	  $dt_currentDay   = date ('d');
  	$dt_currentMonth = date ('m');
	  $dt_currentYear  = date ('Y');
	}

  if ( isset ( $_GET['min'] ) && $_GET['min'] != '' ) {
		$dtmin_value     = $_GET['min'];
		$dtmin_aux  = explode ( '-', $dtmin_value );
 		$dtmin_valueDay   = $dtmin_aux[2];
 		$dtmin_valueMonth = $dtmin_aux[1];
		$dtmin_valueYear  = $dtmin_aux[0];
	}
	else
	  $dtmin_value  = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth,  $dt_currentDay,$dt_currentYear +4  ));

  if ( isset ( $_GET['max'] ) && $_GET['max'] != '') {
		$dtmax_value     = $_GET['max'];
		$dtmax_aux  = explode ( '-', $dtmax_value );
 		$dtmax_valueDay   = $dtmax_aux[2];
 		$dtmax_valueMonth = $dtmax_aux[1];
		$dtmax_valueYear  = $dtmax_aux[0];
	}
	else
	  $dtmax_value  = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth,  $dt_currentDay,$dt_currentYear -4  ));

  $dt_prev_year  = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth,  $dt_currentDay,$dt_currentYear -1  ));
  $dt_next_year  = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth,  $dt_currentDay,$dt_currentYear +1  ));
  $dt_prev_month = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth-1,$dt_currentDay,$dt_currentYear  )  );
  $dt_next_month = date('Y-m-d', mktime ( 0,0,0, $dt_currentMonth+1,$dt_currentDay,$dt_currentYear  )  );


	// get first day to display in the grid for current month
  //$dt_firstday = date();
//  print date ( 'W' );

  $i = 0;
  $start_date = mktime ( 0,0,0, $dt_currentMonth, 1 - $i, $dt_currentYear ) ;
  while ( date('w', $start_date) != $NUM_WEEKSTART ) {
  	$i++;
	  $start_date = mktime ( 0,0,0, $dt_currentMonth, 1 - $i, $dt_currentYear ) ;
  }
  $i = 0;
  $end_date = mktime ( 0,0,0, $dt_currentMonth+1, + $i, $dt_currentYear ) ;
  while ( date('w', $end_date) != (7+$NUM_WEEKSTART ) % 7 ) {
  	$i++;
	  $end_date = mktime ( 0,0,0, $dt_currentMonth+1, + $i, $dt_currentYear ) ;
  }
  $numWeeks = ( $end_date - $start_date )/3600/24/7 ;

//print date('Y-m-d', $start_date ) . " $dtmin_value $dtmax_value ";
?>
<input type='hidden' name='dtmin_value' id='dtmin_value' value='<?php echo $dtmin_value ?>' >
<input type='hidden' name='dtmax_value' id='dtmax_value' value='<?php echo $dtmax_value ?>' >
<table style='font-family:Arial;size:8pt;' cellspacing="0" border="0" width="250px">
<tr><td bgcolor="#4682B4" align='center'>
<table cellspacing="1" cellpadding="2" border="0" width="100%">
<tr><td colspan="7">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
	<td align="right" width='50'>
		<a href="#" onclick="set_datetime('<?php echo $dt_prev_year ?>'); return false; "><img src="<?php echo $prevYear; ?>" width="16" height="16" border="0" alt="previous year"></a>
		<a href="#" onclick="set_datetime('<?php echo $dt_prev_month ?>'); return false; "><img src="<?php echo $prevMonth; ?>" width="16" height="16" border="0" alt="previous month"></a>
	</td>

	<td align="center" width="150">
		<font color="#ffffff"><?php echo $ARR_MONTHS[ $dt_currentMonth - 1 ] . ' ' . $dt_currentYear ?></font>
	</td>

	<td align="left" width='50'>
		<a href="#" onclick="set_datetime('<?php echo $dt_next_month ?>'); return false; ">
		<img src="<?php echo $nextMonth; ?>" width="16" height="16" border="0" alt="next month"></a>
		<a href="#" onclick="set_datetime('<?php echo $dt_next_year ?>'); return false; ">
		<img src="<?php echo $nextYear; ?>" width="16" height="16" border="0" alt="next year"></a>
	</td>
</tr>
</table></td></tr>
<tr>
<?php
	// print weekdays titles
	for ($n=0; $n<7; $n++)
  	print "<td bgcolor='#87cefa' align='center'><font color='#ffffff'>" . $ARR_WEEKDAYS[ ($NUM_WEEKSTART+$n)%7] . "</font></td>";
	print "</tr>";

	// print calendar table
	for ($w = 0; $w < $numWeeks; $w++ ) {
		print "<tr>";
		for ($d = 0; $d < 7; $d++ ) {
			$day = $start_date + 24*3600*( $d + 7*$w );
			$bgcolor = 'ffffff';
			if ( date('w',$day) == 6 || date('w',$day) == 0 )
			 	$bgcolor = '#dbeaf5';
			if ( date('m', $day) != $dt_currentMonth   )
			 	$bgcolor = '#eeeeeee';
			if ( date('Y-m-d', $day) == $dt_value   )
			 	$bgcolor = '#ffb6c1';

			$strDay =  (date('d',$day)+0);
			$strDate =  date('Y-m-d',$day );
			//print "$strDate < $dtmin_value";
			if ( $strDate < $dtmin_value || $strDate > $dtmax_value)
				$link = "$strDay";
			else
				$link = "<a href='#' onclick=\"selectDate('$strDate');eval('try {if (form_' + datePickerPanel.formId + '.getElementByName(datePickerPanel.idName)) {form_' + datePickerPanel.formId + '.getElementByName(datePickerPanel.idName).updateDepententFields();}} catch (e) {}');return false;\" >$strDay</a>";
			print "<td bgcolor='$bgcolor' align='center' width='14%'>$link</td>";
		}
		print "</tr>";
	}
?>
</table>
</tr></td>
</table>