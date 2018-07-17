<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Pmtable Api Controller
 *
 */
class System extends Api
{
    /**
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /db-engines
     * @protected
     */
    public function doGetDataBaseEngines()
    {
        try {
            $oDBConnection = new \ProcessMaker\BusinessModel\DataBaseConnection();
            $response = $oDBConnection->getDbEngines();
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get count for all lists
     *
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /counters-lists
     * @protected
     */
    public function doGetCountersLists()
    {
        try {
            $userId   = $this->getUserId();
            $lists    = new \ProcessMaker\BusinessModel\Lists();
            $response = $lists->getCounters($userId);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get a list of the installed languages.
     *
     * @category HOR-3209,PROD-181
     * @return array
     * @url GET /languages
     * @public
     */
    public function doGetLanguages()
    {
        try {
            $language = new \ProcessMaker\BusinessModel\Language;
            $list = $language->getLanguageList();
            return ["data" => $list];
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @return array
     *
     * @author Gustavo Cruz <gustavo.cruz@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /enabled-features
     * @protected
     */
    public function doGetEnabledFeatures()
    {
        try {
            $enabledFeatures = array();
            /*----------------------------------********---------------------------------*/
            return $enabledFeatures;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get the list of installed skins.
     *
     * @url GET /skins
     * @return array
     * @access protected
     * @class  AccessControl {@permission PM_FACTORY}
     * @protected
     */
    public function doGetSkins()
    {
        try {
            $model = new \ProcessMaker\BusinessModel\Skins();
            $response = $model->getSkins();
            return ["data" => $response];
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

}
