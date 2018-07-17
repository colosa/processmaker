<?php
/**
 * dynaforms_Ajax.php
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
/*
 * Created on 07/01/2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
if (isset($_POST['dynaformName'])) {
    $dynaForm = new Dynaform();
    $res = $dynaForm->verifyExistingName($_POST['dynaformName'], $_POST['proUid'], $_POST['dynaformUid']);
    print ($res) ? 1 : 0;
}
global $_DBArray;
if (! isset( $_DBArray )) {
    $_DBArray = array ();
}

$oDynaformEditorAjax = new DynaformEditorAjax( $_POST );