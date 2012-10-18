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
td {
	font-family: Tahoma, Verdana, sans-serif;
	font-size: 12px;
}
</style>
<script language="JavaScript">

// months as they appear in the calendar's title
var ARR_MONTHS = ["January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"];
// week day titles as they appear on the calendar
var ARR_WEEKDAYS = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
// day week starts from (normally 0-Su or 1-Mo)
var NUM_WEEKSTART = 1;
// path to the directory where calendar images are stored. trailing slash req.
var STR_ICONPATH = '/images/calendar/';

var re_url = new RegExp('datetime=(\\-?\\d+)');
var dt_current = (re_url.exec(String(window.location))
	? new Date(new Number(RegExp.$1)) : new Date());
var re_id = new RegExp('id=(\\d+)');
var num_id = (re_id.exec(String(window.location))
	? new Number(RegExp.$1) : 0);
var obj_caller = (window.opener ? window.opener.calendars[num_id] : null);

if (obj_caller && obj_caller.year_scroll) {
	// get same date in the previous year
	var dt_prev_year = new Date(dt_current);
	dt_prev_year.setFullYear(dt_prev_year.getFullYear() - 1);
	if (dt_prev_year.getDate() != dt_current.getDate())
		dt_prev_year.setDate(0);

	// get same date in the next year
	var dt_next_year = new Date(dt_current);
	dt_next_year.setFullYear(dt_next_year.getFullYear() + 1);
	if (dt_next_year.getDate() != dt_current.getDate())
		dt_next_year.setDate(0);
}

// get same date in the previous month
var dt_prev_month = new Date(dt_current);
dt_prev_month.setMonth(dt_prev_month.getMonth() - 1);
if (dt_prev_month.getDate() != dt_current.getDate())
	dt_prev_month.setDate(0);

// get same date in the next month
var dt_next_month = new Date(dt_current);
dt_next_month.setMonth(dt_next_month.getMonth() + 1);
if (dt_next_month.getDate() != dt_current.getDate())
	dt_next_month.setDate(0);

// get first day to display in the grid for current month
var dt_firstday = new Date(dt_current);
dt_firstday.setDate(1);
dt_firstday.setDate(1 - (7 + dt_firstday.getDay() - NUM_WEEKSTART) % 7);

// function passing selected date to calling window
function set_datetime(n_datetime, b_close) {
	if (!obj_caller) return;

	var dt_datetime = obj_caller.prs_time(
		(document.cal ? document.cal.time.value : ''),
		new Date(n_datetime)
	);

	if (!dt_datetime) return;
	if (b_close) {
		window.close();
		obj_caller.fecha = (document.cal
			? obj_caller.gen_tsmp(dt_datetime)
			: obj_caller.gen_date(dt_datetime)
		);
    var arr_date = obj_caller.fecha.split('-');
    obj_caller.date1.value = arr_date[0];
    obj_caller.date2.value = arr_date[1];
    obj_caller.date3.value = arr_date[2];
	}
	else obj_caller.popup(dt_datetime.valueOf());
}

</script>

<php $ARR_MONTHS=array
	( "January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December");
	$ARR_WEEKDAYS=array
	( "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" );
	$NUM_WEEKSTART=1; //day
	week starts from (normally 0-Suor 1-Mo)

?>
<table class="clsOTable" cellspacing="0" border="0" width="100%">
	<tr>
		<td bgcolor="#4682B4">
			<table cellspacing="1" cellpadding="3" border="0" width="100%">
				<tr>
					<td colspan="7"><table cellspacing="0" cellpadding="0" border="0"
							width="100%">
							<tr>
								<td>+(obj_caller&&obj_caller.year_scroll?'<a
									href="javascript:set_datetime('+dt_prev_year.valueOf()+')"> <img
										src="'+STR_ICONPATH+'prev_year.gif" width="16" height="16"
										border="0" alt="previous year"></a>&nbsp;':'')+' <a
									href="javascript:set_datetime('+dt_prev_month.valueOf()+')"> <img
										src="'+STR_ICONPATH+'prev.gif" width="16" height="16"
										border="0" alt="previous month"></a>
								</td>

								<td align="center" width="100%"><font color="#ffffff">
										+ARR_MONTHS[dt_current.getMonth()]+'
										'+dt_current.getFullYear() </font></td>
								<td><a
									href="javascript:set_datetime('+dt_next_month.valueOf()+')"> <img
										src="'+STR_ICONPATH+'next.gif" width="16" height="16"
										border="0" alt="next month"></a> '+(obj_caller &&
									obj_caller.year_scroll?'&nbsp; <a
									href="javascript:set_datetime('+dt_next_year.valueOf()+')"> <img
										src="'+STR_ICONPATH+'next_year.gif" width="16" height="16"
										border="0" alt="next year">
								</a>':'')+'</td>' );

							</tr>
						</table></td>
				</tr>
				<tr>
					<script language="JavaScript">
<?php
// print weekdays titles
for ($n = 0; $n < 7; $n ++)
    print "<td bgcolor='#87cefa' align='center'><font color='#ffffff'>" . $ARR_WEEKDAYS[(NUM_WEEKSTART + n) % 7] . "</font></td>";
print "</tr>";

?>
// print calendar table

var dt_current_day = new Date(dt_firstday);
while (dt_current_day.getMonth() == dt_current.getMonth() ||
	dt_current_day.getMonth() == dt_firstday.getMonth()) {
	// print row heder
	document.write('<tr>');
	for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
		if (dt_current_day.getDate() == dt_current.getDate() &&
			dt_current_day.getMonth() == dt_current.getMonth())
			// print current date
			document.write('<td bgcolor="#ffb6c1" align="center" width="14%">');
		else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
			// weekend days
			document.write('<td bgcolor="#dbeaf5" align="center" width="14%">');
		else
			// print working days of current month
			document.write('<td bgcolor="#ffffff" align="center" width="14%">');

		document.write('<a href="javascript:set_datetime('+dt_current_day.valueOf() +', true);">');

		if (dt_current_day.getMonth() == this.dt_current.getMonth())
			// print days of current month
			document.write('<font color="#000000">');
		else
			// print days of other months
			document.write('<font color="#606060">');

		document.write(dt_current_day.getDate()+'</font></a></td>');
		dt_current_day.setDate(dt_current_day.getDate()+1);
	}
	// print row footer
	document.write('</tr>');
}
if (obj_caller && obj_caller.time_comp) {
	document.write('<form onsubmit="javascript:set_datetime('+dt_current.valueOf()+', true)" name="cal"><tr><td colspan="7" bgcolor="#87CEFA"><font color="White" face="tahoma, verdana" size="2">Time: <input type="text" name="time" value="'+obj_caller.gen_time(this.dt_current)+'" size="8" maxlength="8"></font></td></tr></form>');
}
</script>

			</table>

	</tr>
	</td>
</table>
</body>
</html>