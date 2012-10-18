<?php
/**
 * buscador.php
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
$frm = $HTTP_GET_VARS;

?>

<h1>demo de buscador</h1>
<form method=post action="buscador2.php">
	<input type=hidden name=ticket value="<?php echo $frm['ticket'] ?>"> <input
		type=hidden name=tipo value="<?php echo $frm['tipo'] ?>">
Buscador tipo : <?php echo $frm['tipo'] ?><br>

	<table>
		<tr>
			<td>curso</td>
			<td><select name=curso>
					<option value="curso1">Curso 1</option>
					<option value="curso2">Curso 2</option>
					<option value="curso3">Curso 3</option>
					<option value="curso4">Curso 4</4option>

					<option value="curso5">Curso 5</option></td>
		</tr>
		<tr>
			<td colspan=2><input type=submit></td>
		</tr>
	</table>
</form>
</body>
</html>

