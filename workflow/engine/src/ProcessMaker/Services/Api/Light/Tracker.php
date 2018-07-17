<?php

namespace ProcessMaker\Services\Api\Light;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 *
 * Process Api Controller
 *
 * @protected
 */
class Tracker extends Api
{
    /**
     * @return array
     * @access public
     * @url GET /case/:case/tracker/:pin
     */
    public function Authentication($case, $pin)
    {
        try {
            $response = \ProcessMaker\BusinessModel\Light\Tracker::authentication($case, $pin);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     * @access public
     * @url GET /process/:pro_uid/case/:app_uid/tracker-history
     */
    public function history($pro_uid, $app_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light\Tracker();
            if (!$oMobile->permissions($pro_uid, "history"))
            {
                throw (new \Exception(\G::LoadTranslation('ID_ACCOUNT_DISABLED_CONTACT_ADMIN')));
            }
            $response = $oMobile->history($pro_uid, $app_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     * @access public
     *
     * @param string $pro_uid {@min 1}{@max 32}
     * @param string $app_uid {@min 1}{@max 32}
     *
     * @url GET /process/:pro_uid/case/:app_uid/tracker-messages
     */
    public function getMessages($pro_uid, $app_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light\Tracker();
            if (!$oMobile->permissions($pro_uid, "messages"))
            {
                throw (new \Exception(\G::LoadTranslation('ID_ACCOUNT_DISABLED_CONTACT_ADMIN')));
            }
            $response = $oMobile->messages($pro_uid, $app_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     * @access public
     *
     * @param string $msg_uid {@min 1}{@max 32}
     * @param string $app_uid {@min 1}{@max 32}
     *
     * @url GET /process/case/:app_uid/message/:msg_uid/view
     */
    public function getViewMessages($app_uid, $msg_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light\Tracker();
            $Fields = \Cases::getHistoryMessagesTrackerView( $app_uid, $msg_uid );
            $response = $oMobile->parserMessages($Fields);
            //$response = $oMobile->messages($pro_uid, $app_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     * @access public
     *
     * @param string $pro_uid {@min 1}{@max 32}
     * @param string $app_uid {@min 1}{@max 32}
     *
     * @url GET /process/:pro_uid/case/:app_uid/tracker-docs
     */
    public function getObjects($pro_uid, $app_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light\Tracker();
            if (!$oMobile->permissions($pro_uid, "objects"))
            {
                throw (new \Exception(\G::LoadTranslation('ID_ACCOUNT_DISABLED_CONTACT_ADMIN')));
            }
            $response = $oMobile->objects($pro_uid, $app_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     * @access public
     *
     * @param string $pro_uid {@min 1}{@max 32}
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $obj_uid {@min 1}{@max 32}
     * @param string $type
     *
     * @url GET /process/:pro_uid/case/:app_uid/object/:obj_uid/:type/show
     */
    public function getShowObjects($pro_uid, $app_uid, $obj_uid, $type)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light\Tracker();
            $response = $oMobile->showObjects($pro_uid, $app_uid, $obj_uid, $type);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }
}