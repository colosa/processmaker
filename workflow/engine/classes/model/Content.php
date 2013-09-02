<?php
/**
 * Content.php
 *
 * @package workflow.engine.classes.model
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
 *
 */

//require_once 'classes/model/om/BaseContent.php';

/**
 * Skeleton subclass for representing a row from the 'CONTENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Content extends BaseContent
{
    public $langs;
    public $rowsProcessed;
    public $rowsInserted;
    public $rowsUnchanged;
    public $rowsClustered;
    public $langsAsoc;
    /*
     * Load the content row specified by the parameters:
     * @param string $sUID
     * @return variant
    */
    public function load ($ConCategory, $ConParent, $ConId, $ConLang)
    {
        $content = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $ConLang );
        if (is_null( $content )) {
            //we dont find any value for this field and language in CONTENT table;
            $ConValue = Content::autoLoadSave( $ConCategory, $ConParent, $ConId, $ConLang );
        } else {
            //krumo($content);
            $ConValue = $content->getConValue();
            if ($ConValue == "") {
                //try to find a valid translation
                $ConValue = Content::autoLoadSave( $ConCategory, $ConParent, $ConId, $ConLang );
            }
        }
        return $ConValue;
    }

    /*
    * Find a valid Lang for current Content. The most recent
    * @param string $ConCategory
    * @param string  $ConParent
    * @param string $ConId
    * @return string
    *
    */
    public function getDefaultContentLang ($ConCategory, $ConParent, $ConId, $destConLang)
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->clearSelectColumns()->clearOrderByColumns();

        $Criteria->addSelectColumn( ContentPeer::CON_CATEGORY );
        $Criteria->addSelectColumn( ContentPeer::CON_PARENT );
        $Criteria->addSelectColumn( ContentPeer::CON_ID );
        $Criteria->addSelectColumn( ContentPeer::CON_LANG );
        $Criteria->addSelectColumn( ContentPeer::CON_VALUE );

        $Criteria->add( ContentPeer::CON_CATEGORY, $ConCategory, CRITERIA::EQUAL );
        $Criteria->add( ContentPeer::CON_PARENT, $ConParent, CRITERIA::EQUAL );
        $Criteria->add( ContentPeer::CON_ID, $ConId, CRITERIA::EQUAL );
        $Criteria->add( ContentPeer::CON_LANG, $destConLang, CRITERIA::NOT_EQUAL );

        $rs = ContentPeer::doSelectRS( $Criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();

        if (is_array( $row = $rs->getRow() )) {
            $defaultLang = $row['CON_LANG'];
        } else {
            $defaultLang = "";
        }
        return ($defaultLang);
    }

    /*
    * Change the value of all records
    * @param string $ConCategory
    * @param string  $ConParent
    * @param string $ConId
    * @param string $ConValue
    * @return void
    *
    */
    public function updateEqualValue ($ConCategory, $ConParent, $ConId, $ConValue)
    {
        $con = Propel::getConnection('workflow');
        $c1 = new Criteria('workflow');
        $c1->add(ContentPeer::CON_CATEGORY, $ConCategory);
        $c1->add(ContentPeer::CON_PARENT, $ConParent);
        $c1->add(ContentPeer::CON_ID, $ConId);

        // update set
        $c2 = new Criteria('workflow');
        $c2->add(ContentPeer::CON_VALUE, $ConValue);
        BasePeer::doUpdate($c1, $c2, $con);
    }

    /*
    * Load the content row and the Save automatically the row for the destination language
    * @param string $ConCategory
    * @param string  $ConParent
    * @param string $ConId
    * @param string $destConLang
    * @return string
    * if the row doesn't exist, it will be created automatically, even the default 'en' language
    */
    public function autoLoadSave ($ConCategory, $ConParent, $ConId, $destConLang)
    {
        //search in 'en' language, the default language
        $content = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, 'en' );

        if ((is_null( $content )) || ($content->getConValue() == "")) {
            $differentLang = Content::getDefaultContentLang( $ConCategory, $ConParent, $ConId, $destConLang );
            $content = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $differentLang );
        }

        //to do: review if the $destConLang is a valid language/
        if (is_null( $content )) {
            $ConValue = '';
            //we dont find any value for this field and language in CONTENT table
        } else {
            $ConValue = $content->getConValue();
        }

        try {
            $con = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $destConLang );
            if (is_null( $con )) {
                $con = new Content();
            }
            $con->setConCategory( $ConCategory );
            $con->setConParent( $ConParent );
            $con->setConId( $ConId );
            $con->setConLang( $destConLang );
            $con->setConValue( $ConValue );
            if ($con->validate()) {
                $res = $con->save();
            }
        } catch (Exception $e) {
            throw ($e);
        }

        return $ConValue;
    }

    /*
    * Insert a content row
    * @param string $ConCategory
    * @param string $ConParent
    * @param string $ConId
    * @param string $ConLang
    * @param string $ConValue
    * @return variant
    */
    public function addContent ($ConCategory, $ConParent, $ConId, $ConLang, $ConValue)
    {
        try {
            if ($ConLang != 'en') {
                $baseLangContent = ContentPeer::retrieveByPk( $ConCategory, $ConParent, $ConId, 'en' );
                if ($baseLangContent === null) {
                    Content::addContent( $ConCategory, $ConParent, $ConId, 'en', $ConValue );
                }
            }

            $con = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $ConLang );

            if (is_null( $con )) {
                $con = new Content();
                $con->setConCategory( $ConCategory );
                if ($con->getConParent() != $ConParent) {
                    $con->setConParent( $ConParent );
                }
                $con->setConId( $ConId );
                $con->setConLang( $ConLang );
                $con->setConValue( $ConValue );
                if ($con->validate()) {
                    $res = $con->save();
                    return $res;
                } else {
                    $e = new Exception( "Error in addcontent, the row $ConCategory, $ConParent, $ConId, $ConLang is not Valid" );
                    throw ($e);
                }
            } else {
                if ($con->getConParent() == $ConParent && $con->getConCategory() == $ConCategory && $con->getConValue() == $ConValue && $con->getConLang() == $ConLang && $con->getConId() == $ConId) {
                    return true;
                }
            }
            Content::updateEqualValue( $ConCategory, $ConParent, $ConId, $ConValue );
            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /*
    * Insert a content row
    * @param string $ConCategory
    * @param string $ConParent
    * @param string $ConId
    * @param string $ConLang
    * @param string $ConValue
    * @return variant
    */
    public function insertContent ($ConCategory, $ConParent, $ConId, $ConLang, $ConValue)
    {
        try {
            $con = new Content();
            $con->setConCategory( $ConCategory );
            $con->setConParent( $ConParent );
            $con->setConId( $ConId );
            $con->setConLang( $ConLang );
            $con->setConValue( $ConValue );
            if ($con->validate()) {
                $res = $con->save();
                return $res;
            } else {
                $e = new Exception( "Error in addcontent, the row $ConCategory, $ConParent, $ConId, $ConLang is not Valid" );
                throw ($e);
            }
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /*
    * remove a content row
    * @param string $ConCategory
    * @param string $ConParent
    * @param string $ConId
    * @param string $ConLang
    * @param string $ConValue
    * @return variant
    */
    public function removeContent ($ConCategory, $ConParent, $ConId)
    {
        try {
            $c = new Criteria();
            $c->add( ContentPeer::CON_CATEGORY, $ConCategory );
            $c->add( ContentPeer::CON_PARENT, $ConParent );
            $c->add( ContentPeer::CON_ID, $ConId );
            $result = ContentPeer::doSelectRS( $c );
            $result->next();
            $row = $result->getRow();
            while (is_array( $row )) {
                ContentPeer::doDelete( array ($ConCategory,$ConParent,$ConId,$row[3]) );
                $result->next();
                $row = $result->getRow();
            }
        } catch (Exception $e) {
            throw ($e);
        }

    }

    /*
    * Reasons if the record already exists
    *
    * @param  string  $ConCategory
    * @param  string  $ConParent
    * @param  string  $ConId
    * @param  string  $ConLang
    * @param  string  $ConValue
    * @return boolean true or false
    */
    public function Exists ($ConCategory, $ConParent, $ConId, $ConLang)
    {
        try {
            $oPro = ContentPeer::retrieveByPk( $ConCategory, $ConParent, $ConId, $ConLang );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Content') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /*
    * Regenerate Table Content
    *
    * @param  array  $langs
    */
    public function regenerateContent ($langs, $workSpace = SYS_SYS)
    {
        //Search the language
        $key = array_search( 'en', $langs );
        if ($key === false) {
            $key = array_search( SYS_LANG, $langs );
            if ($key === false) {
                $key = '0';
            }
        }
        $this->langsAsoc = array ();
        foreach ($langs as $key => $value) {
            $this->langsAsoc[$value] = $value;
        }

        $this->langs = $langs;
        $this->rowsProcessed = 0;
        $this->rowsInserted = 0;
        $this->rowsUnchanged = 0;
        $this->rowsClustered = 0;

        //Creating table CONTENT_BACKUP
        $connection = Propel::getConnection( 'workflow' );
        $oStatement = $connection->prepareStatement( "CREATE TABLE IF NOT EXISTS `CONTENT_BACKUP` (
            `CON_CATEGORY` VARCHAR(30) default '' NOT NULL,
            `CON_PARENT` VARCHAR(32) default '' NOT NULL,
            `CON_ID` VARCHAR(100) default '' NOT NULL,
            `CON_LANG` VARCHAR(10) default '' NOT NULL,
            `CON_VALUE` MEDIUMTEXT NOT NULL,
            CONSTRAINT CONTENT_BACKUP_PK PRIMARY KEY (CON_CATEGORY,CON_PARENT,CON_ID,CON_LANG)
        )Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Table for add content';" );
        $oStatement->executeQuery();

        $sql = " SELECT DISTINCT CON_LANG
                FROM CONTENT ";
        $stmt = $connection->createStatement();
        $rs = $stmt->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );
        while ($rs->next()) {
            $row = $rs->getRow();
            $language = $row['CON_LANG'];
            if (array_search( $row['CON_LANG'], $langs ) === false) {
                Content::removeLanguageContent( $row['CON_LANG'] );
            }
        }

        $sql = " SELECT CON_ID, CON_CATEGORY, CON_LANG, CON_PARENT, CON_VALUE
                FROM CONTENT
                ORDER BY CON_ID, CON_CATEGORY, CON_PARENT, CON_LANG";

        G::LoadClass( "wsTools" );
        $workSpace = new workspaceTools( $workSpace );
        $workSpace->getDBInfo();

        $link = mysql_pconnect( $workSpace->dbHost, $workSpace->dbUser, $workSpace->dbPass) or die( "Could not connect" );

        mysql_select_db( $workSpace->dbName, $link );
        mysql_query( "SET NAMES 'utf8';" );
        mysql_query( "SET FOREIGN_KEY_CHECKS=0;" );
        mysql_query( 'SET OPTION SQL_BIG_SELECTS=1' );
        $result = mysql_unbuffered_query( $sql, $link );
        $list = array ();
        $default = array ();
        $sw = array ('CON_ID' => '','CON_CATEGORY' => '','CON_PARENT' => ''
        );
        while ($row = mysql_fetch_assoc( $result )) {
            if ($sw['CON_ID'] == $row['CON_ID'] && $sw['CON_CATEGORY'] == $row['CON_CATEGORY'] && $sw['CON_PARENT'] == $row['CON_PARENT']) {
                $list[] = $row;
            } else {
                $this->rowsClustered ++;
                if (count( $langs ) != count( $list )) {
                    $this->checkLanguage( $list, $default );
                } else {
                    $this->rowsUnchanged = $this->rowsUnchanged + count( $langs );
                }
                $sw = array ();
                $sw['CON_ID'] = $row['CON_ID'];
                $sw['CON_CATEGORY'] = $row['CON_CATEGORY'];
                $sw['CON_LANG'] = $row['CON_LANG'];
                $sw['CON_PARENT'] = $row['CON_PARENT'];
                unset( $list );
                unset( $default );
                $list = array ();
                $default = array ();
                $list[] = $row;
            }
            if ($sw['CON_LANG'] == $langs[$key]) {
                $default = $row;
            }
            $this->rowsProcessed ++;
        }
        if (count( $langs ) != count( $list )) {
            $this->checkLanguage( $list, $default );
        } else {
            $this->rowsUnchanged = $this->rowsUnchanged + count( $langs );
        }
        mysql_free_result( $result );
        $total = $this->rowsProcessed + $this->rowsInserted;

        $statement = $connection->prepareStatement( "INSERT INTO CONTENT
            SELECT CON_CATEGORY, CON_PARENT, CON_ID , CON_LANG, CON_VALUE
            FROM CONTENT_BACKUP" );
        $statement->executeQuery();

        $statement = $connection->prepareStatement( "DROP TABLE CONTENT_BACKUP" );
        $statement->executeQuery();

        //close connection
        $sql = "SELECT * FROM information_schema.processlist WHERE command = 'Sleep' and user = SUBSTRING_INDEX(USER(),'@',1) and db = DATABASE() ORDER BY id;";
        $stmt = $connection->createStatement();
        $rs = $stmt->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );
        while ($rs->next()) {
            $row = $rs->getRow();
            $oStatement = $connection->prepareStatement( "kill ". $row['ID'] );
            $oStatement->executeQuery();
        }

        if (! isset( $_SERVER['SERVER_NAME'] )) {
            CLI::logging( "Rows Processed ---> $this->rowsProcessed ..... \n" );
            CLI::logging( "Rows Clustered ---> $this->rowsClustered ..... \n" );
            CLI::logging( "Rows Unchanged ---> $this->rowsUnchanged ..... \n" );
            CLI::logging( "Rows Inserted  ---> $this->rowsInserted ..... \n" );
            CLI::logging( "Rows Total     ---> $total ..... \n" );
        }
    }

    public function checkLanguage ($content, $default)
    {
        if (count( $content ) > 0) {
            $langs = $this->langs;
            $langsAsoc = $this->langsAsoc;
            //Element default
            $default = (count( $default ) > 0) ? $default : $content[0];
            foreach ($content as $key => $value) {
                unset( $langsAsoc[$value['CON_LANG']] );
            }
            foreach ($langsAsoc as $key => $value) {
                $this->rowsInserted ++;
                $this->fastInsertContent( $default['CON_CATEGORY'], $default['CON_PARENT'], $default['CON_ID'], $value, $default['CON_VALUE'] );
            }
        }
    }

    public function fastInsertContent ($ConCategory, $ConParent, $ConId, $ConLang, $ConValue)
    {
        $ConValue = mysql_real_escape_string( $ConValue );
        $connection = Propel::getConnection( 'workflow' );
        $statement = $connection->prepareStatement( "INSERT INTO CONTENT_BACKUP (
        CON_CATEGORY, CON_PARENT, CON_ID , CON_LANG, CON_VALUE)
        VALUES ('$ConCategory', '$ConParent', '$ConId', '$ConLang', '$ConValue');" );
        $statement->executeQuery();
    }

    public function removeLanguageContent ($lanId)
    {
        try {
            $c = new Criteria();
            $c->addSelectColumn( ContentPeer::CON_CATEGORY );
            $c->addSelectColumn( ContentPeer::CON_PARENT );
            $c->addSelectColumn( ContentPeer::CON_ID );
            $c->addSelectColumn( ContentPeer::CON_LANG );

            $c->add( ContentPeer::CON_LANG, $lanId );

            $result = ContentPeer::doSelectRS( $c );
            $result->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $result->next();
            $row = $result->getRow();

            while (is_array( $row )) {
                $content = ContentPeer::retrieveByPK( $row['CON_CATEGORY'], $row['CON_PARENT'], $row['CON_ID'], $lanId );

                if ($content !== null) {
                    $content->delete();
                }
                $result->next();
                $row = $result->getRow();
            }

        } catch (Exception $e) {
            throw ($e);
        }
    }

    //Added by Enrique at Feb 9th,2011
    //Gets all Role Names by Role
    public function getAllContentsByRole ($sys_lang = SYS_LANG)
    {
        if (! isset( $sys_lang )) {
            $sys_lang = 'en';
        }
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn( ContentPeer::CON_ID );
        $oCriteria->addAsColumn( 'ROL_NAME', ContentPeer::CON_VALUE );
        //$oCriteria->addAsColumn('ROL_UID', ContentPeer::CON_ID);
        $oCriteria->add( ContentPeer::CON_CATEGORY, 'ROL_NAME' );
        $oCriteria->add( ContentPeer::CON_LANG, $sys_lang );
        $oDataset = ContentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRoles = Array ();
        while ($oDataset->next()) {
            $xRow = $oDataset->getRow();
            $aRoles[$xRow['CON_ID']] = $xRow['ROL_NAME'];
        }
        return $aRoles;
    }
}

