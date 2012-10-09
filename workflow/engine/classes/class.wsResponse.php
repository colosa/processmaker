<?php

/**
 * class.wsResponse.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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

/**
 *
 * @package workflow.engine.classes
 */

class wsResponse
{
    public $status_code = 0;
    public $message = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @return void
     */
    function __construct ($status, $message)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }

    /**
     * Function getPayloadString
     *
     * @param string $operation
     * @return string
     */
    function getPayloadString ($operation)
    {
        $res = "<$operation>\n";
        $res .= "<status_code>" . $this->status_code . "</status_code>";
        $res .= "<message>" . $this->message . "</message>";
        $res .= "<timestamp>" . $this->timestamp . "</timestamp>";
        //    $res .= "<array>" . $this->timestamp . "</array>";
        $res .= "<$operation>";
        return $res;
    }

    /**
     * Function getPayloadArray
     *
     * @return array
     */
    function getPayloadArray ()
    {
        return array ("status_code" => $this->status_code,'message' => $this->message,'timestamp' => $this->timestamp
        );
    }
}

/**
 * Class wsCreateUserResponse
 *
 * @package workflow.engine.classes
 */
class wsCreateUserResponse
{
    public $status_code = 0;
    public $message = '';
    public $userUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $userUID
     * @return void
     */
    function __construct ($status, $message, $userUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->userUID = $userUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}

/**
 * Class wsCreateGroupResponse
 *
 * @package workflow.engine.classes
 */
class wsCreateGroupResponse
{
    public $status_code = 0;
    public $message = '';
    public $groupUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $groupUID
     * @return void
     */
    function __construct ($status, $message, $groupUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->groupUID = $groupUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }

}

/**
 * Class wsCreateDepartmentResponse
 *
 * @package workflow.engine.classes
 */
class wsCreateDepartmentResponse
{
    public $status_code = 0;
    public $message = '';
    public $departmentUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $departmentUID
     * @return void
     */
    function __construct ($status, $message, $departmentUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->departmentUID = $departmentUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}

/**
 * Class wsGetVariableResponse
 *
 * @package workflow.engine.classes
 */
class wsGetVariableResponse
{
    public $status_code = 0;
    public $message = '';
    public $variables = null;
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $variables
     * @return void
     */
    function __construct ($status, $message, $variables)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->variables = $variables;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}

/**
 * Class wsGetCaseNotesResponse
 *
 * @package workflow.engine.classes
 */
class wsGetCaseNotesResponse
{
    public $status_code = 0;
    public $message = '';
    public $notes = null;
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param array|object|string $notes
     * @return void
     */
    function __construct ($status, $message, $notes)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->notes = $notes;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
?>