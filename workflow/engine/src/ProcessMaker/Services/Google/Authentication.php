<?php
namespace ProcessMaker\Services\Google;

class Authentication
{
    /**
     * Post Token by user Gmail
     *
     * @param array $request_data
     *
     */
    public function postTokenAccountGmail($request_data)
    {
        $responseToken = array('msg' => \G::LoadTranslation( 'ID_UPGRADE_ENTERPRISE' ));

        /*----------------------------------********---------------------------------*/

        //Lets verify the gmail token
        $url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$request_data['token'];

        // init curl object
        $ch = curl_init();
        // define options
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        // apply those options
        curl_setopt_array($ch, $optArray);
        // execute request and get response
        $result = curl_exec($ch);
        $response = (json_decode($result));
        // Check if any error occurred
        if(curl_errno($ch))
        {
            throw (new \Exception(\G::LoadTranslation( 'ID_TO_URL' )));
        }
        $info = curl_getinfo($ch);
        curl_close($ch);

        //If there is response
        if($info['http_code'] == 200 && isset($response->email)){
            //If the usermail that was send in the end point es the same of the one in the response
            if($request_data['mail'] == $response->email){
                $oUsers = new \Users();
                $userExist = $oUsers->loadByUserEmailInArray($request_data['mail']);

                if(!$userExist){
                    throw (new \Exception(\G::LoadTranslation( 'ID_USER_NOT_FOUND')));
                }
                if(count($userExist) > 1){
                    throw (new \Exception(\G::LoadTranslation( 'ID_EMAIL_MORE_USER')));
                }
                if($userExist['0']['USR_STATUS'] != "ACTIVE"){
                    throw (new \Exception(\G::LoadTranslation('ID_USER_NOT_ACTIVE')));
                }
                $userExist = $userExist['0'];
                $oauthServer = new \ProcessMaker\Services\OAuth2\Server;
                $server = $oauthServer->getServer();
                $config = array(
                    'allow_implicit' => $server->getConfig('allow_implicit'),
                    'access_lifetime' => $server->getConfig('access_lifetime')
                );
                $storage = $server->getStorages();
                $accessToken = new \OAuth2\ResponseType\AccessToken($storage['access_token'],$storage['refresh_token'],$config);
                $responseToken = $accessToken->createAccessToken($request_data['clientid'], $userExist['USR_UID'],$request_data['scope']);

            } else {
                throw (new \Exception(\G::LoadTranslation( 'ID_EMAIL_NOT_CORRESPONDS_TOKEN' )));
            }
        }else {
            throw (new \Exception(\G::LoadTranslation( 'ID_PMGMAIL_VALID' )));
        }

        /*----------------------------------********---------------------------------*/

        return $responseToken;
    }

}