<?php
/**
 * emailSystemTest.php
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
global $RBAC;
switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'MAILSYSTEM';

$Fields['domain'] = 'colosa.com';
$Fields['to_email'] = '"Ian" <ika@openmail.cc>, "fernando" <fernando@colosa.com>';
$Fields['from_name'] = 'testsystem';
$Fields['from_email'] = 'ian@colosa.com';
$Fields['subject'] = 'Test email';
$Fields['body'] = 'Hello world, this is my first email ... ';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/emailSetupTest', '', $Fields, 'emailSystemSpool' );
G::RenderPage( 'publish' );

