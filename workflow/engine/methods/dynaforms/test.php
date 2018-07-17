<?php
/**
 * test.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
    /* START BOCK: DAVID CALLIZAYA: PLEASE NO BORRAR ESTE BLOQUE.*/
for ($r = 1; $r < 10; $r ++) {
    /*  The timestamp is a 60-bit value.  For UUID version 1, this is
    *  represented by Coordinated Universal Time (UTC) as a count of 100-
    *  nanosecond intervals since 00:00:00.00, 15 October 1582 (the date of
    *  Gregorian reform to the Christian calendar).
    */
    $t = explode( ' ', microtime() );
    $ts = $t[1] . substr( $t[0], 2, 7 );
    $t[0] = substr( '00' . base_convert( $ts, 10, 16 ), - 15 );
    var_dump( $ts );
    print ("\n<br/>") ;
    var_dump( $t );
    print ("\n<br/>") ;
}
/* START BOCK: DAVID CALLIZAYA: PLEASE NO BORRAR ESTE BLOQUE.*/
?>
<form action="test" method="post">
	<select name="form[test][]" multiple="multiple">
		<option value="one">one</option>
		<option value="two">two</option>
		<option value="three">three</option>
		<option value="four">four</option>
		<option value="five">five</option>
	</select> <input type="submit" value="Send" />
</form>
<?php

$filter = new InputFilter();
$test = $_POST['form']['test'];
if ($test) {
    $test = $filter->xssFilterHard($test);
    foreach ($test as $t) {
        echo 'You selected ', $t, '<br />';
    }
}

