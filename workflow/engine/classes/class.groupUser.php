<?php
/**
 * class.groupUser.php
 *
 * @package workflow.engine.ProcessMaker
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
 *
 * It works with the table GROUP_USER
 *
 * Copyright (C) 2007 COLOSA
 *
 * License: LGPL, see LICENSE
 *
 */

/**
 * GroupUser - GroupUser class
 *
 * @package workflow.engine.ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

G::LoadClass( 'pmObject' );

class GroupUser extends DBTable
{

    /**
     * Constructor
     *
     * @param object $oConnection
     * @return variant
     */
    function GroupUser ($oConnection = null)
    {
        if ($oConnection) {
            return parent::setTo( $oConnection, 'GROUP_USER', array ('GRP_UID','USR_UID'
            ) );
        } else {
            return;
        }
    }

    /*
  * Set the Data Base connection
  * @param object $oConnection
  * @return variant/ the connection or void
  */
    function setTo ($oConnection = null)
    {
        if ($oConnection) {
            return parent::setTo( $oConnection, 'GROUP_USER', array ('GRP_UID','USR_UID'
            ) );
        } else {
            return;
        }
    }

    /**
     * Of to assign a user from a group
     *
     * @param string $sGroup
     * @param string $sUser
     * @return void
     */
    function ofToAssignUser ($sGroup = '', $sUser = '')
    {
        $this->Fields['GRP_UID'] = $sGroup;
        $this->Fields['USR_UID'] = $sUser;
        parent::delete();
    }
}
?>