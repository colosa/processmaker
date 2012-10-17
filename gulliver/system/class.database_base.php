<?php

/**
 * class.database_base.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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
 */

/**
 * interface iDatabase
 *
 * @package gulliver.system
 */

interface iDatabase
{

    public function generateDropTableSQL ($sTable);

    public function generateCreateTableSQL ($sTable, $aColumns);

    public function generateDropColumnSQL ($sTable, $sColumn);

    public function generateAddColumnSQL ($sTable, $sColumn, $aParameters);

    public function generateChangeColumnSQL ($sTable, $sColumn, $aParameters);

    public function close ();
}

/**
 * class database_base
 *
 * @package gulliver.system
 * @access public
 */
class database_base implements iDatabase
{
    protected $sType;
    protected $sServer;
    protected $sUser;
    protected $sPass;
    protected $sDataBase;
    protected $oConnection;
    protected $sQuoteCharacter = '';
    protected $sEndLine = ';';

    /**
     * Function __construct
     *
     * @access public
     * @param string $sType
     * @param string $sServer
     * @param string $sUser
     * @param string $sPass
     * @param string $sDataBase
     * @return void
     */

    public function __construct ($sType = DB_ADAPTER, $sServer = DB_HOST, $sUser = DB_USER, $sPass = DB_PASS, $sDataBase = DB_NAME)
    {
        $this->sType = $sType;
        $this->sServer = $sServer;
        $this->sUser = $sUser;
        $this->sPass = $sPass;
        $this->sDataBase = $sDataBase;
        $this->oConnection = null;
        $this->sQuoteCharacter = '';
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sTable
     * @return string
     */
    public function generateDropTableSQL ($sTable)
    {
        $sSQL = 'DROP TABLE IF EXISTS ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
        return $sSQL;
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sTable
     * @param string $sColumn
     * @return void
     */
    public function generateCreateTableSQL ($sTable, $aColumns)
    {
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sTable
     * @param string $sColumn
     * @return void
     */
    public function generateDropColumnSQL ($sTable, $sColumn)
    {
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sTable
     * @param string $sColumn
     * @param string $aParameters
     * @return void
     */
    public function generateAddColumnSQL ($sTable, $sColumn, $aParameters)
    {
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sTable
     * @param string $sColumn
     * @param string $aParameters
     * @return void
     */
    public function generateChangeColumnSQL ($sTable, $sColumn, $aParameters)
    {
    }

    /**
     * Function generateDropTableSQL
     *
     * @access public
     * @param string $sQuery
     * @return void
     */
    public function executeQuery ($sQuery)
    {
    }

    /**
     * Function close
     *
     * @access public
     * @return void
     */
    public function close ()
    {
    }
}

