<?php

/**
 * Dashboard controller
 * @inherits Controller
 *
 * @access public
 */

class Dashboard extends Controller
{

    // Class properties
    private $pmDashlet;

    // Class constructor
    public function __construct ()
    {
        G::LoadClass( 'pmDashlet' );
        $this->pmDashlet = new PMDashlet();
    }

    // Functions for the dashboards users module - Start


    public function index ($httpData)
    {
        try {
            $dashletsExist = $this->getDashletsInstancesForCurrentUser();
            $dashletsHide  = array();
            $dashletColumns = 2;

            G::LoadClass( 'configuration' );
            $oConfiguration = new Configurations();
            $aConfiguration = $oConfiguration->load('Dashboard', '', '', $_SESSION['USER_LOGGED']);
            if (is_array($aConfiguration) && count($aConfiguration) != 0) {
                if (isset($aConfiguration["COLUMNS"])) {
                    $dashletColumns = $aConfiguration["COLUMNS"];
                }

                if (isset($aConfiguration["ORDER"])) {

                    $listDashletAux = array();
                    $listDashletAuxShow = array();
                    foreach ($dashletsExist as $key => $value) {
                        $listDashletAux[$value['DAS_INS_UID']] = $key;
                    }

                    $dashletsShow['0'] = array();
                    foreach ($aConfiguration['ORDER']['0'] as $value) {
                        if (isset($listDashletAux[$value])) {
                            $listDashletAuxShow[] = $value;
                            $dashletsShow['0'][] = $dashletsExist[$listDashletAux[$value]];
                        }
                    }

                    $dashletsShow['1'] = array();
                    foreach ($aConfiguration['ORDER']['1'] as $value) {
                        if (isset($listDashletAux[$value])) {
                            $listDashletAuxShow[] = $value;
                            $dashletsShow['1'][] = $dashletsExist[$listDashletAux[$value]];
                        }
                    }

                    $dashletsShow['2'] = array();
                    foreach ($aConfiguration['ORDER']['2'] as $value) {
                        if (isset($listDashletAux[$value])) {
                            $listDashletAuxShow[] = $value;
                            $dashletsShow['2'][] = $dashletsExist[$listDashletAux[$value]];
                        }
                    }

                    $orderCol = 0;
                    foreach ($listDashletAux as $key => $value) {
                        if (!(in_array($key, $listDashletAuxShow))) {
                            $dashletsShow[$orderCol][] = $dashletsExist[$value];
                            $orderCol++;
                            if ($orderCol == 3) {
                                $orderCol = 0;
                            }
                        }
                    }
                } else {
                    $col = 0;
                    foreach ($dashletsExist as $value) {
                        $dashletsShow[$col][] = $value;
                        $col++;
                        if ($col == 3) {
                            $col = 0;
                        }
                    }
                }
            } else {
                $col = 0;
                foreach ($dashletsExist as $value) {
                    $dashletsShow[$col][] = $value;
                    $col++;
                    if ($col == 3) {
                        $col = 0;
                    }
                }
            }

            $this->setJSVar( 'dashletsAll', $dashletsExist);
            $this->setJSVar( 'dashletsInstances', $dashletsShow);
            $this->setJSVar( 'dashletsColumns', $dashletColumns);
            $this->includeExtJS( 'dashboard/index' );
            $this->includeExtJSLib( 'ux/portal' );
            G::RenderPage( 'publish', 'extJs' );
        } catch (Exception $error) {
            //ToDo: Display a error message
        }
    }

    public function saveOrderDashlet ($data)
    {
        $this->setResponseType( 'json' );
        try {
            $orderDashlet[0] = Bootstrap::json_decode($data->positionCol0);
            $orderDashlet[1] = Bootstrap::json_decode($data->positionCol1);
            $orderDashlet[2] = Bootstrap::json_decode($data->positionCol2);

            G::loadClass('configuration');
            $oConfiguration = new Configurations();
            $aConfiguration = $oConfiguration->load('Dashboard', '', '', $_SESSION['USER_LOGGED']);

            $dataDashboard = array();
            if (isset($aConfiguration["CFG_VALUE"])) {
                $dataDashboard = $aConfiguration["CFG_VALUE"];
            }
            $dataNow['ORDER'] = $orderDashlet;

            if (isset($data->columns)) {
                $dataNow['COLUMNS'] = Bootstrap::json_decode($data->columns);
            }

            $dataDashboard = array_merge($dataDashboard, $dataNow);

            $oConfiguration->aConfig = $dataDashboard;
            $oConfiguration->saveConfig('Dashboard', '', '', $_SESSION['USER_LOGGED']);

            $result->success = '1';
            return $result;
        } catch (Exception $error) {
            //ToDo: Display a error message
        }
    }

    public function renderDashletInstance ($data)
    {
        try {
            if (! isset( $data->DAS_INS_UID )) {
                $data->DAS_INS_UID = '';
            }
            if ($data->DAS_INS_UID == '') {
                throw new Exception( 'Parameter "DAS_INS_UID" is empty.' );
            }
            $this->pmDashlet->setup( $data->DAS_INS_UID );

            if (! isset( $_REQUEST['w'] )) {
                $width = 300;
            } else {
                $width = $_REQUEST['w'];
            }
            $this->pmDashlet->render( $width );
        } catch (Exception $error) {
            //ToDo: Show the error message
            echo $error->getMessage();
        }
    }

    private function getDashletsInstancesForCurrentUser ()
    {
        try {
            if (! isset( $_SESSION['USER_LOGGED'] )) {
                throw new Exception( G::LoadTranslation('ID_SESSION_EXPIRED') );
            }
            return $this->pmDashlet->getDashletsInstancesForUser( $_SESSION['USER_LOGGED'] );
        } catch (Exception $error) {
            throw $error;
        }
    }

    // Functions for the dashboards users module - End


    // Functions for the dasboards administration module - Start


    public function dashletsList ()
    {
        try {
            $this->includeExtJS( 'dashboard/dashletsList' );
            if (isset( $_SESSION['__DASHBOARD_ERROR__'] )) {
                $this->setJSVar( '__DASHBOARD_ERROR__', $_SESSION['__DASHBOARD_ERROR__'] );
                unset( $_SESSION['__DASHBOARD_ERROR__'] );
            }
            $this->setView( 'dashboard/dashletsList' );
            G::RenderPage( 'publish', 'extJs' );
        } catch (Exception $error) {
            //ToDo: Display a error message
        }
    }

    public function getDashletsInstances ($data)
    {
        $this->setResponseType( 'json' );
        $result = new stdclass();
        $result->status = 'OK';
        try {
            if (! isset( $data->start )) {
                $data->start = null;
            }
            if (! isset( $data->limit )) {
                $data->limit = null;
            }
            $result->dashletsInstances = $this->pmDashlet->getDashletsInstances( $data->start, $data->limit );
            $result->totalDashletsInstances = count($result->dashletsInstances);
        } catch (Exception $error) {
            $result->status = 'ERROR';
            $result->message = $error->getMessage();
        }
        return $result;
    }

    public function dashletInstanceForm ($data)
    {
        try {
            $this->includeExtJS( 'dashboard/dashletInstanceForm', false );
            $this->setView( 'dashboard/dashletInstanceForm' );
            if (! isset( $data->DAS_INS_UID )) {
                $data->DAS_INS_UID = '';
            }
            $dashlets = $this->getDashlets();
            $this->setJSVar( 'storeDasUID', $dashlets );
            
            if ($data->DAS_INS_UID != '') {
                $this->pmDashlet->setup( $data->DAS_INS_UID );
                $this->setJSVar( 'dashletInstance', $this->pmDashlet->getDashletInstance() );
                $this->setJSVar( 'additionalFields', PMDashlet::getAdditionalFields( get_class( $this->pmDashlet->getDashletObject() ) ) );
            } else {
                $dashletInstance = new stdclass();
                $dashletInstance->DAS_UID = $dashlets[0][0];
                $dashlet = new Dashlet();
                $dashletFields = $dashlet->load( $dashletInstance->DAS_UID );
                $this->setJSVar( 'dashletInstance', $dashletInstance );
                $this->setJSVar( 'additionalFields', PMDashlet::getAdditionalFields( $dashletFields['DAS_CLASS'] ) );
            }
            G::RenderPage( 'publish', 'extJs' );
            return null;
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            G::header( 'Location: dashletsList' );
            die();
        }
    }

    public function saveDashletInstance ($data)
    {
        $this->setResponseType( 'json' );
        $result = new stdclass();
        $result->status = 'OK';
        try {
            $this->pmDashlet->saveDashletInstance( get_object_vars( $data ) );
        } catch (Exception $error) {
            $result->status = 'ERROR';
            $result->message = $error->getMessage();
        }
        return $result;
    }

    public function deleteDashletInstance ($data)
    {
        $this->setResponseType( 'json' );
        $result = new stdclass();
        $result->status = 'OK';
        try {
            if (! isset( $data->DAS_INS_UID )) {
                $data->DAS_INS_UID = '';
            }
            if ($data->DAS_INS_UID == '') {
                throw new Exception( 'Parameter "DAS_INS_UID" is empty.' );
            }
            $this->pmDashlet->deleteDashletInstance( $data->DAS_INS_UID );
        } catch (Exception $error) {
            $result->status = 'ERROR';
            $result->message = $error->getMessage();
        }
        return $result;
    }

    public function getAdditionalFields ($data)
    {
        $this->setResponseType( 'json' );
        $result = new stdclass();
        $result->status = 'OK';
        try {
            $dashlet = new Dashlet();
            $dashletFields = $dashlet->load( $data->DAS_UID );
            if (! is_null( $dashletFields )) {
                $result->additionalFields = PMDashlet::getAdditionalFields( $dashletFields['DAS_CLASS'] );
            } else {
                throw new Exception( 'Dashlet "' . $data->DAS_UID . '" does not exist.' );
            }
        } catch (Exception $error) {
            $result->status = 'ERROR';
            $result->message = $error->getMessage();
        }
        return $result;
    }

    public function getOwnersByType ($data)
    {
        $this->setResponseType( 'json' );
        $result = new stdclass();
        $result->status = 'OK';
        try {
            switch ($data->type) {
                case 'EVERYBODY':
                    $result->total = 0;
                    $result->owners = array ();
                    break;
                case 'USER':
                    require_once 'classes/model/Users.php';

                    $users = array ();

                    $usersInstance = new Users();
                    $allUsers = $usersInstance->getAll();
                    foreach ($allUsers->data as $user) {
                        $users[] = array ('OWNER_UID' => $user['USR_UID'],'OWNER_NAME' => $user['USR_FIRSTNAME'] . ' ' . $user['USR_LASTNAME']
                        );
                    }

                    $result->total = $allUsers->totalCount;
                    $result->owners = $users;
                    break;
                case 'DEPARTMENT':
                    require_once 'classes/model/Department.php';
                    require_once 'classes/model/Content.php';

                    $departments = array ();
                    //SELECT
                    $criteria = new Criteria( 'workflow' );
                    $criteria->setDistinct();
                    $criteria->addSelectColumn( DepartmentPeer::DEP_UID );
                    $criteria->addSelectColumn( ContentPeer::CON_VALUE );
                    //FROM
                    $conditions = array ();
                    $conditions[] = array (DepartmentPeer::DEP_UID,ContentPeer::CON_ID
                    );
                    $conditions[] = array (ContentPeer::CON_CATEGORY,DBAdapter::getStringDelimiter() . 'DEPO_TITLE' . DBAdapter::getStringDelimiter()
                    );
                    $conditions[] = array (ContentPeer::CON_LANG,DBAdapter::getStringDelimiter() . 'en' . DBAdapter::getStringDelimiter()
                    );
                    $criteria->addJoinMC( $conditions, Criteria::LEFT_JOIN );
                    //WHERE
                    $criteria->add( DepartmentPeer::DEP_STATUS, 'ACTIVE' );
                    //ORDER BY
                    $criteria->addAscendingOrderByColumn( ContentPeer::CON_VALUE );

                    $dataset = DepartmentPeer::doSelectRS( $criteria );
                    $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $dataset->next();
                    while ($row = $dataset->getRow()) {
                        $departments[] = array ('OWNER_UID' => $row['DEP_UID'],'OWNER_NAME' => $row['CON_VALUE']
                        );
                        $dataset->next();
                    }

                    $result->total = DepartmentPeer::doCount( $criteria );
                    $result->owners = $departments;
                    break;
                case 'GROUP':
                    require_once 'classes/model/Groupwf.php';
                    require_once 'classes/model/Content.php';

                    $groups = array ();
                    //SELECT
                    $criteria = new Criteria( 'workflow' );
                    $criteria->setDistinct();
                    $criteria->addSelectColumn( GroupwfPeer::GRP_UID );
                    $criteria->addSelectColumn( ContentPeer::CON_VALUE );
                    //FROM
                    $conditions = array ();
                    $conditions[] = array (GroupwfPeer::GRP_UID,ContentPeer::CON_ID
                    );
                    $conditions[] = array (ContentPeer::CON_CATEGORY,DBAdapter::getStringDelimiter() . 'GRP_TITLE' . DBAdapter::getStringDelimiter()
                    );
                    $conditions[] = array (ContentPeer::CON_LANG,DBAdapter::getStringDelimiter() . 'en' . DBAdapter::getStringDelimiter()
                    );
                    $criteria->addJoinMC( $conditions, Criteria::LEFT_JOIN );
                    //WHERE
                    $criteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
                    //ORDER BY
                    $criteria->addAscendingOrderByColumn( ContentPeer::CON_VALUE );

                    $dataset = GroupwfPeer::doSelectRS( $criteria );
                    $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $dataset->next();
                    while ($row = $dataset->getRow()) {
                        $groups[] = array ('OWNER_UID' => $row['GRP_UID'],'OWNER_NAME' => $row['CON_VALUE']
                        );
                        $dataset->next();
                    }

                    $result->total = GroupwfPeer::doCount( $criteria );
                    $result->owners = $groups;
                    break;
            }
        } catch (Exception $error) {
            $result->status = 'ERROR';
            $result->message = $error->getMessage();
        }
        return $result;
    }

    private function getDashlets ()
    {
        try {
            require_once 'classes/model/Dashlet.php';

            $dashlets = array ();

            //SELECT
            $criteria = new Criteria( 'workflow' );
            $criteria->addSelectColumn( DashletPeer::DAS_UID );
            $criteria->addSelectColumn( DashletPeer::DAS_TITLE );
            $criteria->addSelectColumn( DashletPeer::DAS_CLASS );
            //ORDER BY
            $criteria->addAscendingOrderByColumn( DashletPeer::DAS_TITLE );

            $dataset = DashletPeer::doSelectRS( $criteria );
            $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $dataset->next();
            while ($row = $dataset->getRow()) {
                if ($this->pmDashlet->verifyPluginDashlet($row['DAS_CLASS'])) {
                    $dashlets[] = array ($row['DAS_UID'],$row['DAS_TITLE']);
                }
                $dataset->next();
            }

        } catch (Exception $error) {
            throw $error;
        }
        return $dashlets;
    }

    // Functions for the dasboards administration module - End
}
