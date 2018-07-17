<?php
/**
 * Application.php
 * @package    workflow.engine.classes.model
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

//require_once ('classes/model/om/BaseApplication.php');
//require_once ('classes/model/Content.php');


/**
 * Skeleton subclass for representing a row from the 'APPLICATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Application extends BaseApplication
{

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $app_title_content = '';
    protected $app_description_content = '';
    public static $app_status_values = ['DRAFT' => 1, 'TO_DO' => 2, 'COMPLETED' => 3, 'CANCELLED' => 4];

    /**
     * Get the [app_title_content] column value.
     * @return     string
     */
    public function getAppTitleContent()
    {
        $oApplication = new Application;
        if (!$oApplication->exists($this->getAppUid())) {
            return false;
        }

        if ($this->getAppUid() == '') {
            throw (new Exception( "Error in getAppTitleContent, the APP_UID can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->app_title_content = Content::load('APP_TITLE', '', $this->getAppUid(), $lang);

        return $this->app_title_content;
    }

    /**
     * Set the [app_title_content] column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setAppTitleContent($v)
    {
        if ($this->getAppUid() == '') {
            throw (new Exception( "Error in setAppTitleContent, the APP_UID can't be blank"));
        }

        //Since the native PHP type for this column is string,
        //we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if (in_array(ApplicationPeer::APP_TITLE, $this->modifiedColumns) || $v === '') {
            $this->app_title_content = $v;
            $lang = defined('SYS_LANG')? SYS_LANG : 'en';
            $res = Content::addContent('APP_TITLE', '', $this->getAppUid(), $lang, $this->app_title_content);
        }
    } // set()

    /**
     * Get the [app_description_content] column value.
     * @return     string
     */
    public function getAppDescriptionContent()
    {
        if ($this->getAppUid() == '') {
            throw (new Exception( "Error in getAppDescriptionContent, the APP_UID can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->app_description_content = Content::load('APP_DESCRIPTION', '', $this->getAppUid(), $lang);

        return $this->app_description_content;
    }

    /**
     * Set the [app_description_content] column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setAppDescriptionContent($v)
    {
        if ($this->getAppUid() == '') {
            throw ( new Exception( "Error in setAppDescriptionContent, the APP_UID can't be blank") );
        }

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if (in_array(ApplicationPeer::APP_DESCRIPTION, $this->modifiedColumns) || $v === '') {
            $this->app_description_content = $v;
            $lang = defined('SYS_LANG')? SYS_LANG : 'en';
            $res = Content::addContent('APP_DESCRIPTION', '', $this->getAppUid(), $lang, $this->app_description_content);
        }
    } // set()

    /**
     * Get the [app_proc_code] column value.
     * @return     string
     */
    /*public function getAppProcCode  ()
    {
      if ( $this->getAppUid() == '' ) {
        throw ( new Exception( "Error in getAppProcCode, the APP_UID can't be blank") );
      }
      $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
      $this->app_proc_code = Content::load ( 'APP_PROC_CODE', '', $this->getAppUid(), $lang );
      return $this->app_proc_code;
    }*/

    /**
     * Set the [app_proc_code] column value.
     *
     * @param      string $v new value
     * @return     void
     */
    /*public function setAppProcCode  ($v)
    {
      if ( $this->getAppUid() == '' ) {
        throw ( new Exception( "Error in setAppProcCode  , the APP_UID can't be blank") );
      }
      // Since the native PHP type for this column is string,
      // we will cast the input to a string (if it is not).
      if ($v !== null && !is_string($v)) {
        $v = (string) $v;
      }

      if ($this->app_proc_code !== $v || $v === '') {
        $this->app_proc_code = $v;
            $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
            $res = Content::addContent( 'APP_PROC_CODE', '', $this->getAppUid(), $lang, $this->app_proc_code );
      }
    }*/ // set()


    /**
     * Load the Application row specified in [app_id] column value.
     *
     * @param      string $AppUid   the uid of the application
     * @return     array  $Fields   the fields
     */

    public function Load($AppUid)
    {
        $con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);

        try {
            $oApplication = ApplicationPeer::retrieveByPK( $AppUid );
            if (is_object($oApplication) && get_class ($oApplication) == 'Application' ) {
                $aFields = $oApplication->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                //$aFields['APP_PROC_CODE'] = $oApplication->getAppProcCode();
                //$this->setAppProcCode($oApplication->getAppProcCode());

                return $aFields;
            } else {
                throw (new Exception("The Application row '$AppUid' doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Creates the Application
     *
     * @param
     *   $sProUid the process id
     *   $sUsrUid the userid
     * @return     void
     */
    public function create($sProUid, $sUsrUid)
    {
        require_once ("classes/model/AppSequence.php");
        $con = Propel::getConnection('workflow');

        try {
            //fill the default values for new application row
            $this->setAppUid(G::generateUniqueID());
            $this->setAppParent('');
            $this->setAppStatus('DRAFT');
            $this->setAppStatusId(1);
            $this->setProUid($sProUid);
            $this->setAppProcStatus('');
            $this->setAppProcCode('');
            $this->setAppParallel('N');
            $this->setAppInitUser($sUsrUid);
            $this->setAppCurUser($sUsrUid);
            $this->setAppCreateDate('now');
            $this->setAppInitDate('now');
            $this->setAppUpdateDate('now');

            $pin = G::generateCode(4, 'ALPHANUMERIC');
            $this->setAppPin(G::encryptOld($pin));

            $c = new Criteria();
            $c->clearSelectColumns();

            $oAppSequence = new AppSequence();
            $maxNumber = $oAppSequence->sequenceNumber();

            $this->setAppNumber($maxNumber);
            $this->setAppData(serialize(['APP_NUMBER' => $maxNumber, 'PIN' => $pin]));
            $this->setAppTitle('#' . $maxNumber);
            $this->setAppDescription('');

            if ($this->validate()) {
                $con->begin();
                $this->setAppTitleContent('#' . $maxNumber);
                $this->setAppDescriptionContent('');
                //to do: ID_CASE in translation $this->setAppTitle(G::LoadTranslation('ID_CASE') . $maxNumber);
                //Content::insertContent('APP_PROC_CODE', '', $this->getAppUid(), $lang, '');
                $res = $this->save();
                $con->commit();

                return $this->getAppUid();
            } else {
                $msg = '';

                foreach ($this->getValidationFailures() as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                }

                throw (new PropelException('The APPLICATION row cannot be created!', new PropelException($msg)));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Update the application row
     * @param     array $aData
     * @return    variant
    **/
    public function update($aData)
    {
        $con = Propel::getConnection( ApplicationPeer::DATABASE_NAME );

        try {
            $con->begin();
            $oApp = ApplicationPeer::retrieveByPK($aData['APP_UID']);

            if (is_object($oApp) && get_class($oApp) == 'Application') {
                $oApp->fromArray($aData, BasePeer::TYPE_FIELDNAME);

                if ($oApp->validate()) {
                    if (isset($aData['APP_STATUS'])) {
                        $oApp->setAppStatusId(self::$app_status_values[$aData['APP_STATUS']]);
                    }
                    if (isset($aData['APP_TITLE'])) {
                        $oApp->setAppTitleContent($aData['APP_TITLE']);
                    }
                    if (isset($aData['APP_DESCRIPTION'])) {
                        $oApp->setAppDescriptionContent($aData['APP_DESCRIPTION']);
                    }

                    $res = $oApp->save();
                    $con->commit();

                    return $res;
                } else {
                    $msg = '';

                    foreach ($oApp->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }

                    throw (new PropelException('The row cannot be updated!', new PropelException($msg)));
                }
            } else {
                $con->rollback();

                throw (new Exception("The row '" . $aData['APP_UID'] . "' in table APPLICATION doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Remove the application document registry
     * @param     array $aData or string $appUid
     * @return    string
    **/
    public function remove($appUid)
    {
        if (is_array($appUid)) {
            $appUid = (isset($appUid['APP_UID'])? $appUid['APP_UID'] : '');
        }

        try {
            $oApp = ApplicationPeer::retrieveByPK($appUid);

            if (!is_null($oApp)) {
                Content::removeContent('APP_TITLE', '', $oApp->getAppUid());
                Content::removeContent('APP_DESCRIPTION', '', $oApp->getAppUid());
                //Content::removeContent('APP_PROC_CODE', '', $oApp->getAppUid());

                return $oApp->delete();
            } else {
                throw (new Exception("The row '$appUid' in table Application doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function exists($sAppUid)
    {
        $oApplicaton = ApplicationPeer::retrieveByPK($sAppUid);

        return (!is_null($oApplicaton));
    }

    public function createApplication($aData)
    {
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn('MAX(' . ApplicationPeer::APP_NUMBER . ')');
        $c->add(ApplicationPeer::PRO_UID, $aData['PRO_UID']);
        $result = ApplicationPeer::doSelectRS($c);
        $result->next();
        $row = $result->getRow();
        $maxNumber = $row[0] + 1;
        $this->setAppUid(G::generateUniqueID());

        $this->setAppNumber($maxNumber);
        $this->setAppTitle('');
        $this->setAppDescription('');
        $this->setAppParent(isset($aData['APP_PARENT'])? $aData['APP_PARENT'] : 0);
        $this->setAppStatus(isset($aData['APP_STATUS'])? $aData['APP_STATUS'] : 'DRAFT');
        $this->setProUid($aData['PRO_UID']);
        $this->setAppProcStatus(isset($aData['APP_PROC_STATUS'])? $aData['APP_PROC_STATUS'] : '');
        $this->setAppProcCode(isset($aData['APP_PROC_CODE'])? $aData['APP_PROC_CODE'] : '');
        $this->setAppParallel(isset($aData['APP_PARALLEL'])? $aData['APP_PARALLEL'] : 'N');
        $this->setAppInitUser($aData['USR_UID']);
        $this->setAppCurUser($aData['USR_UID']);
        $this->setAppCreateDate(isset($aData['APP_CREATE_DATE'])? $aData['APP_CREATE_DATE'] : 'now');
        $this->setAppInitDate(isset($aData['APP_INIT_DATE'])? $aData['APP_INIT_DATE'] : 'now');
        $this->setAppUpdateDate(isset($aData['APP_UPDATE_DATE'])? $aData['APP_UPDATE_DATE'] : 'now');
        //$this->setAppData(serialize(array()));

        /** Start Comment : Sets the $this->Fields['APP_DATA']
        Merge between stored APP_DATA with new APP_DATA. **/
        if (!$this->getAppData()) {
            //        if (!$this->is_new)
            //        {
            //          $this->load($fields['APP_UID']);
            //        }
        }

        $this->Fields['APP_DATA'] = isset($this->Fields['APP_DATA'])? $this->Fields['APP_DATA'] : array();

        if (isset($fields['APP_DATA']) && is_array($fields['APP_DATA'])) {
            foreach ($fields['APP_DATA'] as $k => $v) {
                $this->Fields['APP_DATA'][$k] = $v;
            }
        }
        /** End Comment **/

        /*
        Begin Comment : Replace APP_DATA in APP_TITLE (before to serialize)
          $pro = new process( $this->_dbc );
          $pro->load((isset($fields['PRO_UID']) ? $fields['PRO_UID'] : $this->Fields['PRO_UID']));
          $fields['APP_TITLE'] = G::replaceDataField( $pro->Fields['PRO_TITLE'], $this->Fields['APP_DATA']);
        End Comment
        */

        //    parent::save();
        //    $this->Fields['APP_DATA'] = unserialize($this->Fields['APP_DATA']);
        //    /** Start Comment: Save in the table CONTENT */
        //      $this->content->saveContent('APP_TITLE',$fields);
        //    /** End Comment */

        if ($this->validate()) {
            $res = $this->save();
            $this->setAppTitleContent('');
            $this->setAppDescriptionContent('');
            $this->setAppProcCode('');
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $this->getValidationFailures();

            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }

            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }

        return $this->getAppUid();
    }

    /*----------------------------------********---------------------------------*/
}

