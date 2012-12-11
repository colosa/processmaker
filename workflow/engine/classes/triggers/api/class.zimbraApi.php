<?php

/**
 * zimbra.class.php
 *
 * Zimbra API
 *
 * @version 1.3
 * @module zimbra.class.php
 * @author Zachary Tirrell <zbtirrell@plymouth.edu>
 * @GPL 2007, Plymouth State University, ITS
 */
class Zimbra
{

    public $debug = false;
    public $error;
    protected $_connected = false; // boolean to determine if the connect function has been called
    protected static $_num_soap_calls = 0; // the number of times a SOAP call has been made
    protected $_preAuthKey; // key for doing pre-authentication
    protected $_lcached_assets = array (); // an array to hold assets that have been cached
    protected $_preauth_expiration = 0; // 0 indicates using the default preauth expiration as defined on the server
    protected $_dev; // boolean indicating whether this is development or not
    protected $_protocol; // which protocol to use when building the URL
    protected $_server1; // = 'ip-10-73-18-235.ec2.internal'; // hostname of zimbra server
    protected $_server; // displayname of zimbra server
    protected $_path = '/service/soap';
    protected $_timestamp;
    protected $_account_info;
    protected $_admin = false; // operating as an admin
    protected $_curl;
    protected $_auth_token; // used for repeat calls to zimbra through soap
    protected $_session_id; // used for repeat calls to zimbra through soap
    protected $_idm; // IDMObject
    protected $_username; // the user we are operating as


    /**
     * __construct
     *
     * constructor sets up connectivity to servers
     *
     * @since version 1.0
     * @acess public
     * @param string $username username
     * @param string $which defaults to prod
     */

    public function __construct ($username, $serverUrl, $preAuthKey, $which = 'prod', $protocol = 'http')
    {
        if ($which == 'dev') {
            $which = 'zimbra_dev';
            $this->_dev = true;
        } else {
            $which = 'zimbra';
        }

        $this->_preAuthKey = $preAuthKey;
        $this->_protocol = $protocol."://"; // could also be http://
        $this->_server = $serverUrl; //'zimbra.hostname.edu';
        $this->_server1 = $serverUrl; //'zimbra.hostname.edu';
        $this->_username = $username;
        $this->_timestamp = time() . '000';
    }

    // end __construct


    /**
     * sso
     *
     * sso to Zimbra
     *
     * @since version 1.0
     * @access public
     * @param string $options options for sso
     * @return boolean
     */
    public function sso ($options = '')
    {
        if ($this->_username) {
            setcookie( 'ZM_SKIN', 'plymouth', time() + 60 * 60 * 24 * 30, '/', '.plymouth.edu' );

            $pre_auth = $this->getPreAuth( $this->_username );
            $url = $this->_protocol . '/service/preauth?account=' . $this->_username . '@' . $this->_server . '&expires=' . $this->_preauth_expiration . '&timestamp=' . $this->_timestamp . '&preauth=' . $pre_auth; //.'&'.$options;
            header( "Location: $url" );
            exit();
        } else {
            return false;
        }
    }

    // end sso


    /**
     * createAccount
     *
     * @param string $name account name
     * @param string $password password
     * @return string account id
     */
    function createAccount ($name, $password)
    {
        $option_string = '';

        try {

            $soap = '<CreateAccountRequest xmlns="urn:zimbraAccount">
            <name>' . $name . '@' . $this->_server1 . '</name>
            <password>' . $password . '</password>' . $option_string . '
            <session/>
            </CreateAccountRequest>';

            $response = $this->soapRequest( $soap );
        } catch (SoapFault $exception) {
            print_exception( $exception );
        }

        return $result['SOAP:ENVELOPE']['SOAP:BODY']['CREATEACCOUNTRESPONSE']['ACCOUNT']['ID'];
    }

    /**
     * getPreAuth
     *
     * get the preauth key needed for single-sign on
     *
     * @since version1.0
     * @access public
     * @param string $username username
     * @return string preauthentication key in hmacsha1 format
     */
    private function getPreAuth ($username)
    {
        $account_identifier = $username . '@' . $this->_server1;
        $by_value = 'name';
        $expires = $this->_preauth_expiration;
        $timestamp = $this->_timestamp;

        $string = $account_identifier . '|' . $by_value . '|' . $expires . '|' . $timestamp;

        return $this->hmacsha1( $this->_preAuthKey, $string );
    }

    // end getPreAuth


    /**
     * hmacsha1
     *
     * generate an HMAC using SHA1, required for preauth
     *
     * @since version 1.0
     * @access public
     * @param int $key encryption key
     * @param string $data data to encrypt
     * @return string converted to hmac sha1 format
     */
    private function hmacsha1 ($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen( $key ) > $blocksize) {
            $key = pack( 'H*', $hashfunc( $key ) );
        }
        $key = str_pad( $key, $blocksize, chr( 0x00 ) );
        $ipad = str_repeat( chr( 0x36 ), $blocksize );
        $opad = str_repeat( chr( 0x5c ), $blocksize );
        $hmac = pack( 'H*', $hashfunc( ($key ^ $opad) . pack( 'H*', $hashfunc( ($key ^ $ipad) . $data ) ) ) );
        return bin2hex( $hmac );
    }

    // end hmacsha1


    /**
     * connect
     *
     * connect to the Zimbra SOAP service
     *
     * @since version 1.0
     * @access public
     * @return array associative array of account information
     */
    public function connect ()
    {
        if ($this->_connected) {
            return $this->_account_info;
        }
        $completeurl = $this->_protocol . $this->_server . $this->_path;
        $this->_curl = curl_init();
        curl_setopt( $this->_curl, CURLOPT_URL, $this->_protocol . $this->_server . $this->_path );
        curl_setopt( $this->_curl, CURLOPT_POST, true );
        curl_setopt( $this->_curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYHOST, false );

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt( $this->_curl, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '') );
            if ($sysConf['proxy_port'] != '') {
                curl_setopt( $this->_curl, CURLOPT_PROXYPORT, $sysConf['proxy_port'] );
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt( $this->_curl, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : '') );
            }
            curl_setopt( $this->_curl, CURLOPT_HTTPHEADER, array ('Expect:'
            ) );
        }

        $preauth = $this->getPreAuth( $this->_username );
        $header = '<context xmlns="urn:zimbraAccount' . (($this->_admin) ? 'Admin' : '') . '"><session/></context>';

        if ($this->_admin) {
            $body = '<AuthRequest xmlns="urn:zimbraAdmin">
            <name>' . $this->_admin_username . '</name>
            <password>' . $this->_admin_password . '</password>
            </AuthRequest>';
        } else {
            $body = '<AuthRequest xmlns="urn:zimbraAccount">
            <account by="name">' . $this->_username . '@' . $this->_server1 . '</account>
            <preauth timestamp="' . $this->_timestamp . '" expires="' . $this->_preauth_expiration . '">' . $preauth . '</preauth>
            </AuthRequest>';
        }

        $response = $this->soapRequest( $body, $header, true );
        if ($response) {
            $tmp = $this->makeXMLTree( $response );
            $this->_account_info = $tmp['soap:Envelope'][0]['soap:Header'][0]['context'][0]['refresh'][0]['folder'][0];

            $this->session_id = $this->extractSessionID( $response );
            $this->auth_token = $this->extractAuthToken( $response );

            $this->_connected = true;

            //return $this->_account_info;
            return $this->_connected;
        } else {
            $this->_connected = false;
            return false;
        }
    }

    // end connect


    /**
     * administerUser
     *
     * set the user you are administering (experimental)
     *
     * @since version 1.0
     * @access public
     * @param string $username username to administer
     * @return boolean
     */
    public function administerUser ($username)
    {
        if (! $this->_admin) {
            return false;
        }

        $this->_username = $username;

        $body = '<DelegateAuthRequest xmlns="urn:zimbraAdmin">
        <account by="name">' . $this->_username . '@' . $this->_server . '</account>
        </DelegateAuthRequest>';
        $response = $this->soapRequest( $body, $header );
        if ($response) {
            $tmp = $this->makeXMLTree( $response );
            $this->_account_info = $tmp['soap:Envelope'][0]['soap:Header'][0]['context'][0]['refresh'][0]['folder'][0];

            $this->session_id = $this->extractSessionID( $response );
            $this->auth_token = $this->extractAuthToken( $response );

            return true;
        } else {
            return false;
        }
    }

    // end administerUser


    /**
     * getInfo
     *
     * generic function to get information on mailbox, preferences, attributes, properties, and more!
     *
     * @since version 1.0
     * @access public
     * @param string $options options for info retrieval, defaults to null
     * @return array information
     */
    public function getInfo ($options = '')
    {
        // valid sections: mbox,prefs,attrs,zimlets,props,idents,sigs,dsrcs,children
        $option_string = $this->buildOptionString( $options );

        $soap = '<GetInfoRequest xmlns="urn:zimbraAccount"' . $option_string . '></GetInfoRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );
            return $array['soap:Envelope'][0]['soap:Body'][0]['GetInfoResponse'][0];
        } else {
            return false;
        }
    }

    // end getInfo


    /**
     * getMessages
     *
     * get the messages in folder, deafults to inbox
     *
     * @since version 1.0
     * @access public
     * @param string $search folder to retrieve from, defaults to in:inbox
     * @param array $options options to apply to retrieval
     * @return array array of messages
     */
    public function getMessages ($search = 'in:inbox', $options = array('limit' => 5, 'fetch' => 'none'))
    {
        $option_string = $this->buildOptionString( $options );

        $soap = '<SearchRequest xmlns="urn:zimbraMail" types="message"' . $option_string . '>
        <query>' . $search . '</query>
        </SearchRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );
            return $array['soap:Envelope'][0]['soap:Body'][0]['SearchResponse'][0];
        } else {
            return false;
        }
    }

    // end getMessages


    /**
     * getContacts
     *
     * get the Contacts in folder, deafults to inbox
     *
     * @since version 1.0
     * @access public
     * @param string $search folder to retrieve from, defaults to in:inbox
     * @param array $options options to apply to retrieval
     * @return array array of messages
     */
    public function getContacts ($search = 'in:contacts', $options = array('limit' => 5, 'fetch' => 'none'))
    {
        $option_string = $this->buildOptionString( $options );

        $soap = '<SearchRequest xmlns="urn:zimbraMail" types="contact"' . $option_string . '>
        <query>' . $search . '</query>
        </SearchRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );
            return $array['soap:Envelope'][0]['soap:Body'][0]['SearchResponse'][0];
        } else {
            return false;
        }
    }

    // end getContacts


    /* getAppointments
     *
    * get the Appointments in folder
    *
    * @since     version 1.0
    * @access    public
    * @param     string $search folder to retrieve from
    * @param     array $options options to apply to retrieval
    * @return    array array of messages
    */

    public function getAppointments ($search = 'in:calendar', $options = array('limit' => 50, 'fetch' => 'none'))
    {
        $option_string = $this->buildOptionString( $options );

        $soap = '<SearchRequest xmlns="urn:zimbraMail" types="appointment"' . $option_string . '>
        <query>' . $search . '</query>
        </SearchRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );
            return $array['soap:Envelope'][0]['soap:Body'][0]['SearchResponse'][0];
        } else {
            return false;
        }
    }

    // end getAppointments


    /* getTasks
     *
    * get the Tasks in folder
    *
    * @since     version 1.0
    * @access    public
    * @param     string $search folder to retrieve from
    * @param     array $options options to apply to retrieval
    * @return    array array of messages
    */

    public function getTasks ($search = 'in:tasks', $options = array('limit' => 50, 'fetch' => 'none'))
    {
        $option_string = $this->buildOptionString( $options );

        $soap = '<SearchRequest xmlns="urn:zimbraMail" types="task"' . $option_string . '>
        <query>' . $search . '</query>
        </SearchRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );
            return $array['soap:Envelope'][0]['soap:Body'][0]['SearchResponse'][0];
        } else {
            return false;
        }
    }

    // end getTasks


    /**
     * getMessageContent
     *
     * get the content from a message
     *
     * @since version 1.0
     * @access public
     * @param int $id id number of message to retrieve content of
     * @return array associative array with message content, valid for tasks, calendar entries, and email messages.
     */
    public function getMessageContent ($id)
    {
        $soap = '<GetMsgRequest xmlns="urn:zimbraMail">
        <m id="' . $id . '" html="1">*</m>
        </GetMsgRequest>';
        $response = $this->soapRequest( $soap );

        if ($response) {
            $array = $this->makeXMLTree( $response );
            $temp = $array['soap:Envelope'][0]['soap:Body'][0]['GetMsgResponse'][0]['m'][0];

            $message = $temp['inv'][0]['comp'][0];

            // content with no attachment
            $message['content'] = $temp['mp'][0]['mp'][1]['content'][0];

            // content with attachment
            $message['content'] .= $temp['mp'][0]['mp'][0]['mp'][1]['content'][0];

            return $message;
        } else {
            return false;
        }
    }

    /**
     * getSubscribedCalendars
     *
     * get the calendars the user is subscribed to
     *
     * @since version 1.0
     * @access public
     * @return array $subscribed
     */
    public function getSubscribedCalendars ()
    {
        $subscribed = array ();
        if (is_array( $this->_account_info['link_attribute_name'] )) {
            foreach ($this->_account_info['link_attribute_name'] as $i => $name) {
                if ($this->_account_info['link_attribute_view'][$i] == 'appointment') {
                    $subscribed[$this->_account_info['link_attribute_id'][$i]] = $name;
                }
            }
        }
        return $subscribed;
    }

    // end getSubscribedCalendars


    /**
     * getSubscribedTaskLists
     *
     * get the task lists the user is subscribed to
     *
     * @since version 1.0
     * @access public
     * @return array $subscribed or false
     */
    public function getSubscribedTaskLists ()
    {
        $subscribed = array ();
        if (is_array( $this->_account_info['link_attribute_name'] )) {
            foreach ($this->_account_info['link_attribute_name'] as $i => $name) {
                if ($this->_account_info['link_attribute_view'][$i] == 'task') {
                    $subscribed[$this->_account_info['link_attribute_id'][$i]] = $name;
                }
            }
        }
        return $subscribed;
    }

    // end getSubscribedCalendars


    /**
     * getFolder
     *
     * get a folder (experimental)
     *
     * @since version 1.0
     * @access public
     * @param string $folder_options options for folder retrieval
     * @return array $folder or false
     */
    public function getFolder ($folderName, $folder_options = '')
    {

        //$folder_option_string = $this->buildOptionString($folder_options);


        $soap = '<GetFolderRequest xmlns="urn:zimbraMail" visible="1">
        <folder path="' . $folderName . '"/>
        </GetFolderRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );

            $folder = (is_array( $array['soap:Envelope'][0]['soap:Body'][0]['GetFolderResponse'][0]['folder'][0] )) ? $array['soap:Envelope'][0]['soap:Body'][0]['GetFolderResponse'][0]['folder'][0] : $array['soap:Envelope'][0]['soap:Body'][0]['GetFolderResponse'][0];

            $folder['u'] = (! isset( $folder['u'] )) ? $folder['folder_attribute_u'][0] : $folder['u'];
            $folder['n'] = (! isset( $folder['n'] )) ? $folder['folder_attribute_n'][0] : $folder['n'];

            return $folder;
        } else {
            return false;
        }
    }

    // end getFolder


    /**
     * getPrefrences
     *
     * get preferences
     *
     * @since version 1.0
     * @access public
     * @example example XML: <GetPrefsRequest> <!-- get only the specified prefs --> [<pref name="{name1}"/> <pref name="{name2}"/>] </GetPrefsRequest>
     * @return array $prefs or false
     */
    public function getPreferences ()
    {
        $soap = '<GetPrefsRequest xmlns="urn:zimbraAccount" />';
        $response = $this->soapRequest( $soap );
        if ($response) {
            $prefs = array ();
            $array = $this->makeXMLTree( $response );
            foreach ($array['soap:Envelope'][0]['soap:Body'][0]['GetPrefsResponse'][0]['pref'] as $k => $value) {
                $prefs[$array['soap:Envelope'][0]['soap:Body'][0]['GetPrefsResponse'][0]['pref_attribute_name'][$k]] = $value;
            }
            return $prefs;
        } else {
            return false;
        }
    }

    // end getPreferences


    /**
     * setPrefrences
     *
     * modify preferences
     *
     * @since version 1.0
     * @access public
     * @param string $options options to set the prefrences
     * @example example XML: <ModifyPrefsRequest> [<pref name="{name}">{value}</pref>...]+ </ModifyPrefsRequest>
     * @return boolean
     */
    public function setPreferences ($options = '')
    {
        $option_string = '';
        foreach ($options as $name => $value) {
            $option_string .= '<pref name="' . $name . '">' . $value . '</pref>';
        }

        $soap = '<ModifyPrefsRequest xmlns="urn:zimbraAccount">
        ' . $option_string . '
        </ModifyPrefsRequest>';
        $response = $this->soapRequest( $soap );
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    // end setPreferences


    /**
     * emailChannel
     *
     * build the email channel
     *
     * @since version 1.0
     * @access public
     */
    public function emailChannel ()
    {
        require_once 'xtemplate.php';
        $tpl = new XTemplate( '/web/pscpages/webapp/portal/channel/email/templates/index.tpl' );

        $tpl->parse( 'main.transition' );

        $total_messages = 0;
        $unread_messages = 0;

        $messages = $this->getMessages( 'in:inbox' );
        if (is_array( $messages )) {
            $more = $messages['more'];
            foreach ($messages['m'] as $message) {
                $clean_message = array ();

                $clean_message['subject'] = (isset( $message['su'][0] ) && $message['su'][0] != '') ? htmlentities( $message['su'][0] ) : '[None]';
                $clean_message['subject'] = (strlen( $clean_message['subject'] ) > 20) ? substr( $clean_message['subject'], 0, 17 ) . '...' : $clean_message['subject'];

                $clean_message['body_fragment'] = $message['fr'][0];
                $clean_message['from_email'] = $message['e_attribute_a'][0];
                $clean_message['from'] = ($message['e_attribute_p'][0]) ? htmlspecialchars( $message['e_attribute_p'][0] ) : $clean_message['from_email'];
                $clean_message['size'] = $this->makeBytesPretty( $message['s'], 40 * 1024 * 1024 );
                $clean_message['date'] = date( 'n/j/y', ($message['d'] / 1000) );
                $clean_message['id'] = $message['id'];
                $clean_message['url'] = 'http://go.plymouth.edu/mymail/msg/' . $clean_message['id'];

                $clean_message['attachment'] = false;
                $clean_message['status'] = 'read';
                $clean_message['deleted'] = false;
                $clean_message['flagged'] = false;
                if (isset( $message['f'] )) {
                    $clean_message['attachment'] = (strpos( $message['f'], 'a' ) !== false) ? true : false;
                    $clean_message['status'] = (strpos( $message['f'], 'u' ) !== false) ? 'unread' : 'read';
                    ;
                    $clean_message['deleted'] = (strpos( $message['f'], '2' ) !== false) ? true : false;
                    $clean_message['flagged'] = (strpos( $message['f'], 'f' ) !== false) ? true : false;
                }

                $tpl->assign( 'message', $clean_message );
                $tpl->parse( 'main.message' );
            }
            $inbox = $this->getFolder( array ('l' => 2
            ) );

            $total_messages = (int) $inbox['n'];
            $unread_messages = (int) $inbox['u'];
        }

        $tpl->assign( 'total_messages', $total_messages );
        $tpl->assign( 'unread_messages', $unread_messages );

        $info = $this->getInfo( array ('sections' => 'mbox'
        ) );
        if (is_array( $info['attrs'][0]['attr_attribute_name'] )) {
            $quota = $info['attrs'][0]['attr'][array_search( 'zimbraMailQuota', $info['attrs'][0]['attr_attribute_name'] )];
            $size_text = $this->makeBytesPretty( $info['used'][0], ($quota * 0.75) ) . ' out of ' . $this->makeBytesPretty( $quota );
            $tpl->assign( 'size', $size_text );
        }

        /* include_once 'portal_functions.php';
         $roles = getRoles($this->_username);

        if(in_array('faculty', $roles) || in_array('employee', $roles))
        {
        $tpl->parse('main.away_message');
        } */

        $tpl->parse( 'main' );
        $tpl->out( 'main' );
    }

    // end emailChannel


    /**
     * builOptionString
     *
     * make an option string that will be placed as attributes inside an XML tag
     *
     * @since version 1.0
     * @access public
     * @param array $options array of options to be parsed into a string
     * @return string $options_string
     */
    protected function buildOptionString ($options)
    {
        $options_string = '';
        foreach ($options as $k => $v) {
            $options_string .= ' ' . $k . '="' . $v . '"';
        }
        return $options_string;
    }

    // end buildOptionString


    /**
     * extractAuthToken
     *
     * get the Auth Token out of the XML
     *
     * @since version 1.0
     * @access public
     * @param string $xml xml to have the auth token pulled from
     * @return string $auth_token
     */
    private function extractAuthToken ($xml)
    {
        $auth_token = strstr( $xml, "<authToken" );
        $auth_token = strstr( $auth_token, ">" );
        $auth_token = substr( $auth_token, 1, strpos( $auth_token, "<" ) - 1 );
        return $auth_token;
    }

    /**
     * extractSessionID
     *
     * get the Session ID out of the XML
     *
     * @since version 1.0
     * @access public
     * @param string $xml xml to have the session id pulled from
     * @return int $session_id
     */
    private function extractSessionID ($xml)
    {

        //for testing purpose we are extracting lifetime instead of sessionid
        //$session_id = strstr($xml, "<lifetime");
        $session_id = strstr( $xml, "<sessionId" );
        $session_id = strstr( $session_id, ">" );
        $session_id = substr( $session_id, 1, strpos( $session_id, "<" ) - 1 );
        return $session_id;
    }

    // end extractSessionID


    /**
     * extractErrorCode
     *
     * get the error code out of the XML
     *
     * @since version 1.0
     * @access public
     * @param string $xml xml to have the error code pulled from
     * @return int $session_id
     */
    private function extractErrorCode ($xml)
    {
        $session_id = strstr( $xml, "<Code" );
        $session_id = strstr( $session_id, ">" );
        $session_id = substr( $session_id, 1, strpos( $session_id, "<" ) - 1 );
        return $session_id;
    }

    // end extractErrorCode


    /**
     * makeBytesPretty
     *
     * turns byte numbers into a more readable format with KB or MB
     *
     * @since version 1.0
     * @access public
     * @param int $bytes bytes to be worked with
     * @param boolean $redlevel
     * @return int $size
     */
    private function makeBytesPretty ($bytes, $redlevel = false)
    {
        if ($bytes < 1024) {
            $size = $bytes . ' B';
        } elseif ($bytes < 1024 * 1024) {
            $size = round( $bytes / 1024, 1 ) . ' KB';
        } else {
            $size = round( ($bytes / 1024) / 1024, 1 ) . ' MB';
        }
        if ($redlevel && $bytes > $redlevel) {
            $size = '<span style="color:red">' . $size . '</span>';
        }

        return $size;
    }

    // end makeBytesPretty


    /**
     * message
     *
     * if debug is on, show a message
     *
     * @since version 1.0
     * @access public
     * @param string $message message for debug
     */
    protected function message ($message)
    {
        if ($this->debug) {
            echo $message;
        }
    }

    // end message


    /**
     * soapRequest
     *
     * make a SOAP request to Zimbra server, returns the XML
     *
     * @since version 1.0
     * @access public
     * @param string $body body of page
     * @param boolean $header
     * @param boolean $footer
     * @return string $response
     */
    protected function soapRequest ($body, $header = false, $connecting = false)
    {
        if (! $connecting && ! $this->_connected) {
            throw new Exception( 'zimbra.class: soapRequest called without a connection to Zimbra server' );
        }

        if ($header == false) {
            $header = '<context xmlns="urn:zimbra">
            <authToken>' . $this->auth_token . '</authToken>
            <sessionId id="' . $this->session_id . '">' . $this->session_id . '</sessionId>
            </context>';
        }

        $soap_message = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
        <soap:Header>' . $header . '</soap:Header>
        <soap:Body>' . $body . '</soap:Body>
        </soap:Envelope>';
        $this->message( 'SOAP message:<textarea>' . $soap_message . '</textarea>' );

        curl_setopt( $this->_curl, CURLOPT_POSTFIELDS, $soap_message );

        if (! ($response = curl_exec( $this->_curl ))) {
            $this->error = 'ERROR: curl_exec - (' . curl_errno( $this->_curl ) . ') ' . curl_error( $this->_curl );
            return false;
        } elseif (strpos( $response, '<soap:Body><soap:Fault>' ) !== false) {
            $error_code = $this->extractErrorCode( $response );
            $this->error = 'ERROR: ' . $error_code . ':<textarea>' . $response . '</textarea>';
            $this->message( $this->error );
            $aError = array ('error' => $error_code
            );
            return $aError;
            //return false;
        }
        $this->message( 'SOAP response:<textarea>' . $response . '</textarea><br/><br/>' );

        $this->_num_soap_calls ++;
        return $response;
    }

    // end soapRequest


    /**
     * getNumSOAPCalls
     *
     * get the number of SOAP calls that have been made. This is for debugging and performancing
     *
     * @since version 1.0
     * @access public
     * @return int $this->_num_soap_calls
     */
    public function getNumSOAPCalls ()
    {
        return $this->_num_soap_calls;
    }

    // end getNumSOAPCalls


    /**
     * makeXMLTree
     *
     * turns XML into an array
     *
     * @since version 1.0
     * @access public
     * @param string $data data to be built into an array
     * @return array $ret
     */
    protected function makeXMLTree ($data)
    {
        // create parser
        $parser = xml_parser_create();
        xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
        xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
        xml_parse_into_struct( $parser, $data, $values, $tags );
        xml_parser_free( $parser );

        // we store our path here
        $hash_stack = array ();

        // this is our target
        $ret = array ();
        foreach ($values as $key => $val) {

            switch ($val['type']) {
                case 'open':
                    array_push( $hash_stack, $val['tag'] );
                    if (isset( $val['attributes'] )) {
                        $ret = $this->composeArray( $ret, $hash_stack, $val['attributes'] );
                    } else {
                        $ret = $this->composeArray( $ret, $hash_stack );
                    }
                    break;
                case 'close':
                    array_pop( $hash_stack );
                    break;
                case 'complete':
                    array_push( $hash_stack, $val['tag'] );
                    $ret = $this->composeArray( $ret, $hash_stack, $val['value'] );
                    array_pop( $hash_stack );

                    // handle attributes
                    if (isset( $val['attributes'] )) {
                        foreach ($val['attributes'] as $a_k => $a_v) {
                            $hash_stack[] = $val['tag'] . '_attribute_' . $a_k;
                            $ret = $this->composeArray( $ret, $hash_stack, $a_v );
                            array_pop( $hash_stack );
                        }
                    }
                    break;
            }
        }
        return $ret;
    }

    // end makeXMLTree


    /**
     * &composeArray
     *
     * function used exclusively by makeXMLTree to help turn XML into an array
     *
     * @since version 1.0
     * @access public
     * @param array $array
     * @param array $elements
     * @param array $value
     * @return array $array
     */
    private function &composeArray ($array, $elements, $value = array())
    {
        global $XML_LIST_ELEMENTS;

        // get current element
        $element = array_shift( $elements );

        // does the current element refer to a list
        if (sizeof( $elements ) > 0) {
            $array[$element][sizeof( $array[$element] ) - 1] = &$this->composeArray( $array[$element][sizeof( $array[$element] ) - 1], $elements, $value );
        } else {
            // if (is_array($value))
            $array[$element][sizeof( $array[$element] )] = $value;
        }

        return $array;
    }

    // end composeArray


    /**
     * noop
     *
     * keeps users session alive
     *
     * @since version 1.0
     * @access public
     * @return string xml response from the noop
     */
    public function noop ()
    {
        return $this->soapRequest( '<NoOpRequest xmlns="urn:zimbraMail"/>' );
    }

    /**
     * addAppointments
     *
     * add appointments in a calendar
     *
     * @since version 1.0
     * @access public
     * @param
     *
     *
     * @return
     *
     *
     */
    public function addAppointment ($serializeOp1)
    {
        $unserializeOp1 = unserialize( $serializeOp1 );

        $username = $unserializeOp1['username'];
        $subject = $unserializeOp1['subject'];
        $appointmentName = $unserializeOp1['appointmentName'];
        $friendlyName = $unserializeOp1['friendlyName'];
        $userEmail = $unserializeOp1['userEmail'];
        $domainName = $unserializeOp1['domainName'];
        $schedule = $unserializeOp1['schedule'];
        $cutype = $unserializeOp1['cutype'];
        $allDay = $unserializeOp1['allDay'];
        $isOrg = $unserializeOp1['isOrg'];
        $rsvp = $unserializeOp1['rsvp'];
        $atFriendlyName = $unserializeOp1['atFriendlyName'];
        $role = $unserializeOp1['role'];
        $location = $unserializeOp1['location'];
        $ptst = $unserializeOp1['ptst'];

        $dateFormat = $allDay == "1" ? "Ymd" : "Ymd\THis";
        $startDate = date( $dateFormat, strtotime( $unserializeOp1['startDate'] ) );
        $endDate = date( $dateFormat, strtotime( $unserializeOp1['endDate'] ) );
        $timeZone = $allDay == "1" ? "" : $unserializeOp1['tz'];

        $explodeEmail = explode( ';', $userEmail );
        $explodeFriendlyName = explode( ';', $atFriendlyName );
        $countExplodeEmail = count( $explodeEmail );

        $soap = '<CreateAppointmentRequest xmlns="urn:zimbraMail"><m>
        <su>' . $subject . '</su>';
        for ($i = 0; $i < $countExplodeEmail; $i ++) {
            $soap .= '<e p="' . $friendlyName . '" a="' . $explodeEmail[$i] . '" t="t"></e>';
        }
        $soap .= '<inv>
        <comp fb="' . $schedule . '" fba="' . $schedule . '" name="' . $appointmentName . '" allDay ="' . $allDay . '" isOrg="' . $isOrg . '" loc="' . $location . '">
        <s tz="' . $timeZone . '" d="' . $startDate . '"/>
        <e tz="' . $timeZone . '" d="' . $endDate . '"/>
        <or a="' . $username . '@' . $domainName . '" d="' . $friendlyName . '"></or>';
        for ($i = 0; $i < $countExplodeEmail; $i ++) {
            $soap .= '<at role="' . $role . '" ptst="' . $ptst . '" d="' . $explodeFriendlyName[$i] . '" rsvp="' . $rsvp . '" cutype="' . $cutype . '" a="' . $explodeEmail[$i] . '"></at>';
        }
        $soap .= '</comp>
        </inv>
        <mp ct="multipart/alternative">
        <mp ct="text/plain">
        <content>
        this is a sample Contents
        </content>
        </mp>
        </mp>
        </m>
        </CreateAppointmentRequest>';
        //G::pr($soap);die;
        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );

            return $array['soap:Envelope'][0]['soap:Body'][0]['CreateAppointmentResponse'];
        } else {
            return false;
        }
    }

    // end addAppointments


    /**
     * addTask
     *
     * add Task in a Task Tab
     *
     * @since version 1.0
     * @access public
     * @param array $options array of options to apply to retrieval from calendar
     * @return array associative array of appointments
     */
    public function addTask ($serializeOp1)
    {
        $unserializeOp1 = unserialize( $serializeOp1 );

        $subject = $unserializeOp1['subject'];
        $taskName = $unserializeOp1['taskName'];
        $friendlyName = $unserializeOp1['friendlyName'];
        $userEmail = $unserializeOp1['userEmail'];
        $priority = $unserializeOp1['priority'];
        $allDay = $unserializeOp1['allDay'];
        $class = $unserializeOp1['class'];
        $location = $unserializeOp1['location'];
        $dueDate = date( "Ymd", strtotime( $unserializeOp1['dueDate'] ) );
        $status = $unserializeOp1['status'];
        $percent = $unserializeOp1['percent'];

        $soap = '<CreateTaskRequest xmlns="urn:zimbraMail">
        <m l="15">
        <su>' . $subject . '</su>
        <e p="' . $friendlyName . '" a="' . $userEmail . '" t="t"></e>
        <inv>
        <comp allDay="' . $allDay . '"
        name="' . $taskName . '"
        class="' . $class . '"
        priority="' . $priority . '"
        percentComplete="' . $percent . '"
        status="' . $status . '"
        loc="' . $location . '">
        <dueDate d="' . $dueDate . '"/>
        </comp>
        </inv>
        <mp ct="multipart/alternative">
        <mp ct="text/plain">
        <content/>
        </mp>
        </mp>
        </m>
        </CreateTaskRequest>';
        $response = $this->soapRequest( $soap );

        if ($response) {
            $array = $this->makeXMLTree( $response );

            //return $array['soap:Envelope'][0]['soap:Body'][0]['BatchResponse'][0]['CreateTaskRequest'][0]['appt'];
            return $array['soap:Envelope'][0]['soap:Body'][0]['CreateTaskResponse'];
        } else {
            return false;
        }
    }

    // end addTask


    /**
     * addContacts
     *
     * add contact in a AddressBook
     *
     * @since version 1.0
     * @access public
     * @param
     *
     *
     * @return
     *
     *
     */
    public function addContacts ($serializeOp1)
    {
        $unserializeOp1 = unserialize( $serializeOp1 );

        $firstName = $unserializeOp1['firstName'];
        $lastName = $unserializeOp1['lastName'];
        $email = $unserializeOp1['email'];
        $otherData = $unserializeOp1['otherData'];
        $otherDataValue = $unserializeOp1['otherDataValue'];

        $soap = '<CreateContactRequest xmlns="urn:zimbraMail">
        <cn>
        <a n="firstName">' . $firstName . '</a>
        <a n="lastName">' . $lastName . '</a>
        <a n="email">' . $email . '</a>
        <a n="' . $otherData . '">' . $otherDataValue . '</a>
        </cn>
        </CreateContactRequest>';

        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );

            return $array['soap:Envelope'][0]['soap:Body'][0]['CreateContactResponse'];
        } else {
            return false;
        }
    }

    // end addContacts
    /**
     * addFolder
     *
     * add Folder in a BriefCase
     *
     * @since version 1.0
     * @access public
     * @param
     *
     *
     * @return
     *
     *
     */

    public function addFolder ($serializeOp1)
    {
        $unserializeOp1 = unserialize( $serializeOp1 );

        $folderName = $unserializeOp1['folderName'];
        $folderColor = $unserializeOp1['color'];

        $soap = '<CreateFolderRequest xmlns="urn:zimbraMail">
        <folder name="' . $folderName . '" color="' . $folderColor . '" view="document">
        </folder>
        </CreateFolderRequest>';

        $response = $this->soapRequest( $soap );
        if ($response) {
            $array = $this->makeXMLTree( $response );

            return $array['soap:Envelope'][0]['soap:Body'][0]['CreateFolderResponse'];
        } else {
            return false;
        }
    }

    // end addFolder
    /**
     * uploadDocument
     *
     * add Folder in a BriefCase
     *
     * @since version 1.0
     * @access public
     * @param
     *
     *
     * @return
     *
     *
     */

    public function upload ($folderId, $UploadId, $fileVersion = '', $docId = '')
    {
        if ($fileVersion == '' && $docId == '') {
            $soap = '<SaveDocumentRequest xmlns="urn:zimbraMail">
            <doc l="' . $folderId . '">
            <upload id="' . $UploadId . '"/>
            </doc>
            </SaveDocumentRequest>';
        } else {
            $soap = '<SaveDocumentRequest xmlns="urn:zimbraMail">
            <doc l="' . $folderId . '" ver="' . $fileVersion . '" id="' . $docId . '" >
            <upload id="' . $UploadId . '"/>
            </doc>
            </SaveDocumentRequest>';
        }

        $response = $this->soapRequest( $soap );
        if (is_array( $response )) {
            if (isset( $response['error'] )) {
                return $response;
            }
        } else {
            $array = $this->makeXMLTree( $response );

            return $array['soap:Envelope'][0]['soap:Body'][0]['SaveDocumentResponse'];
        }
    }

    // end uploadDocument


    /**
     * getDocId
     *
     * Get ID of File in Zimbra.
     *
     * @since version 1.0
     * @access public
     * @param
     *
     *
     * @return
     *
     *
     */
    public function getDocId ($folderId, $fileName)
    {
        $soap = '<GetItemRequest xmlns="urn:zimbraMail">
        <item l="' . $folderId . '" name="' . $fileName . '" />
        </GetItemRequest>';

        $response = $this->soapRequest( $soap );
        if (is_array( $response )) {
            if ($response['error']) {
                return false;
            }
        } else {
            $array = $this->makeXMLTree( $response );

            return $array['soap:Envelope'][0]['soap:Body'][0]['GetItemResponse'][0];
        }
    }

    // end getDocId
}

// end Zimbra class
// annoying sorting functions for getTasks...
// I don't know how to make usort calls to internal OO functions
// if someone knows how, please fix this :)


/**
 * zimbra_startSort
 *
 * sort of zimbra elements
 *
 * @since version 1.0
 * @access public
 * @param array $task_a
 * @param array $task_b
 * @return int (($task_a['dueDate']-$task_a['dur']) < ($task_b['dueDate']-$task_b['dur'])) ? -1 : 1
 */
function zimbra_startSort ($task_a, $task_b)
{
    if (($task_a['dueDate'] - $task_a['dur']) == ($task_b['dueDate'] - $task_b['dur'])) {
        return ($task_a['name'] < $task_b['name']) ? - 1 : 1;
    }
    return (($task_a['dueDate'] - $task_a['dur']) < ($task_b['dueDate'] - $task_b['dur'])) ? - 1 : 1;
}

/**
 * zimbra_dueSort
 *
 * sort by dueDate
 *
 * @since version 1.0
 * @access public
 * @param array $task_a
 * @param array $task_b
 * @return int ($task_a['dueDate'] < $task_b['dueDate']) ? -1 : 1
 */
function zimbra_dueSort ($task_a, $task_b)
{
    if ($task_a['dueDate'] == $task_b['dueDate']) {
        return ($task_a['name'] < $task_b['name']) ? - 1 : 1;
    }
    return ($task_a['dueDate'] < $task_b['dueDate']) ? - 1 : 1;
}

/**
 * zimbra_nameSort
 *
 * sort by name
 *
 * @since version 1.0
 * @access public
 * @param array $task_a
 * @param array $task_b
 * @return int ($task_a['name'] < $task_b['name']) ? -1 : 1
 */
function zimbra_nameSort ($task_a, $task_b)
{
    if ($task_a['name'] == $task_b['name']) {
        return 0;
    }
    return ($task_a['name'] < $task_b['name']) ? - 1 : 1;
}

