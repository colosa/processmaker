<?php

namespace ProcessMaker\Services\Api\Google;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

class Authentication extends Api
{

    /**
     * Get token for user gmail
     *
     * @param array $request_data
     *
     * @return array
     *
     * @url POST /gmail
     *
     *
     */
    public function doAuthenticationAccountGmail ($request_data)
    {
        try{
            $oGoogle = new \ProcessMaker\Services\Google\Authentication();
            $response = $oGoogle->postTokenAccountGmail($request_data);
            return $response;
        } catch (\Exception $e){
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}