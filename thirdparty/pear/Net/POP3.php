<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002, Richard Heyes                                     |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Author: Richard Heyes <richard@phpguru.org>                           |
// | Co-Author: Damian Fernandez Sosa <damlists@cnba.uba.ar>               |
// +-----------------------------------------------------------------------+
//
// $Id: POP3.php,v 1.2 2004/12/05 16:34:39 damian Exp $

require_once('Net/Socket.php');



/**
*  +----------------------------- IMPORTANT ------------------------------+
*  | Usage of this class compared to native php extensions such as IMAP   |
*  | is slow and may be feature deficient. If available you are STRONGLY  |
*  | recommended to use the php extensions.                               |
*  +----------------------------------------------------------------------+
*
* POP3 Access Class
*
* For usage see the example script
*/

define('NET_POP3_STATE_DISCONNECTED',  1, true);
define('NET_POP3_STATE_AUTHORISATION', 2, true);
define('NET_POP3_STATE_TRANSACTION',   4, true);

class Net_POP3 {

    /*
    * Some basic information about the mail drop
    * garnered from the STAT command
    *
    * @var array
    */
    var $_maildrop;

    /*
    * Used for APOP to store the timestamp
    *
    * @var string
    */
    var $_timestamp;

    /*
    * Timeout that is passed to the socket object
    *
    * @var integer
    */
    var $_timeout;

    /*
    * Socket object
    *
    * @var object
    */
    var $_socket;

    /*
    * Current state of the connection. Used with the
    * constants defined above.
    *
    * @var integer
    */
    var $_state;

    /*
    * Hostname to connect to
    *
    * @var string
    */
    var $_host;

    /*
    * Port to connect to
    *
    * @var integer
    */
    var $_port;

    /**
    * To allow class debuging
    * @var boolean
    */
    var $_debug = false;


    /**
    * The auth methods this class support
    * @var array
    */
    //var $supportedAuthMethods=array('DIGEST-MD5', 'CRAM-MD5', 'APOP' , 'PLAIN' , 'LOGIN', 'USER');
    //Disabling DIGEST-MD5 for now
    var $supportedAuthMethods=array( 'CRAM-MD5', 'APOP' , 'PLAIN' , 'LOGIN', 'USER');
    //var $supportedAuthMethods=array( 'CRAM-MD5', 'PLAIN' , 'LOGIN');
    //var $supportedAuthMethods=array( 'PLAIN' , 'LOGIN');


    /**
    * The auth methods this class support
    * @var array
    */
    var $supportedSASLAuthMethods=array('DIGEST-MD5', 'CRAM-MD5');


    /**
    * The capability response
    * @var array
    */
    var $_capability;

   /*
    * Constructor. Sets up the object variables, and instantiates
    * the socket object.
    *
    */


    function Net_POP3()
    {
        $this->_timestamp =  ''; // Used for APOP
        $this->_maildrop  =  array();
        $this->_timeout   =  3;
        $this->_state     =  NET_POP3_STATE_DISCONNECTED;
        $this->_socket    =& new Net_Socket();
        /*
        * Include the Auth_SASL package.  If the package is not available,
        * we disable the authentication methods that depend upon it.
        */
        if ((@include_once 'Auth/SASL.php') == false) {
            if($this->_debug){
                echo "AUTH_SASL NOT PRESENT!\n";
            }
            foreach($this->supportedSASLAuthMethods as $SASLMethod){
                $pos = array_search( $SASLMethod, $this->supportedAuthMethods );
                if($this->_debug){
                    echo "DISABLING METHOD $SASLMethod\n";
                }
                unset($this->supportedAuthMethods[$pos]);
            }
        }



    }


    /**
    * Handles the errors the class can find
    * on the server
    *
    * @access private
    * @return PEAR_Error
    */

    function _raiseError($msg, $code =-1)
    {
    include_once 'PEAR.php';
    return PEAR::raiseError($msg, $code);
    }


    
    /*
    * Connects to the given host on the given port.
    * Also looks for the timestamp in the greeting
    * needed for APOP authentication
    *
    * @param  string $host Hostname/IP address to connect to
    * @param  string $port Port to use to connect to on host
    * @return bool  Success/Failure
    */
    function connect($host = 'localhost', $port = 110)
    {
        $this->_host = $host;
        $this->_port = $port;

        $result = $this->_socket->connect($host, $port, false, $this->_timeout);
        if ($result === true) {
            $data = $this->_recvLn();

            if( $this->_checkResponse($data) ){
            // if the response begins with '+OK' ...
//            if (@substr(strtoupper($data), 0, 3) == '+OK') {
                // Check for string matching apop timestamp
                if (preg_match('/<.+@.+>/U', $data, $matches)) {
                    $this->_timestamp = $matches[0];
                }
                $this->_maildrop = array();
                $this->_state    = NET_POP3_STATE_AUTHORISATION;

                return true;
            }
        }

        $this->_socket->disconnect();
        return false;
    }

    /*
    * Disconnect function. Sends the QUIT command
    * and closes the socket.
    *
    * @return bool Success/Failure
    */
    function disconnect()
    {
        return $this->_cmdQuit();
    }

    /*
    * Performs the login procedure. If there is a timestamp
    * stored, APOP will be tried first, then basic USER/PASS.
    *
    * @param  string $user Username to use
    * @param  string $pass Password to use
    * @param  mixed $apop Whether to try APOP first, if used as string you can select the auth methd to use ( $pop3->login('validlogin', 'validpass', "CRAM-MD5");
    *          Valid methods are: 'DIGEST-MD5','CRAM-MD5','LOGIN','PLAIN','APOP','USER' 
    * @return mixed  true on Success/ PEAR_ERROR on error
    */
    function login($user, $pass, $apop = true)
    {
        if ($this->_state == NET_POP3_STATE_AUTHORISATION) {

            if(PEAR::isError($ret= $this->_cmdAuthenticate($user , $pass , $apop ) ) ){
                return $ret;
            }
            if( ! PEAR::isError($ret)){
                $this->_state = NET_POP3_STATE_TRANSACTION;
                return true;
            }

        }
        return $this->_raiseError('Generic login error' , 1);
    }



    /**
    * Parses the response from the capability command. Stores
    * the result in $this->_capability
    *
    * @access private
    */
    function _parseCapability()
    {

        if(!PEAR::isError($data = $this->_sendCmd('CAPA'))){
            $data = $this->_getMultiline();
        }else {
            // CAPA command not supported, reset data var
            //  to avoid Notice errors of preg_split on an object
            $data = '';
        }
        $data = preg_split('/\r?\n/', $data, -1, PREG_SPLIT_NO_EMPTY);

        for ($i = 0; $i < count($data); $i++) {

            $capa='';
            if (preg_match('/^([a-z,\-]+)( ((.*))|$)$/i', $data[$i], $matches)) {

                $capa=strtolower($matches[1]);
                switch ($capa) {
                    case 'implementation':
                        $this->_capability['implementation'] = $matches[3];
                        break;
                    case 'sasl':
                        $this->_capability['sasl'] = preg_split('/\s+/', $matches[3]);
                        break;
                    default :
                        $this->_capability[$capa] = $matches[2];
                        break;
                }
            }
        }
    }




    /**
     * Returns the name of the best authentication method that the server
     * has advertised.
     *
     * @param string if !=null,authenticate with this method ($userMethod).
     *
     * @return mixed    Returns a string containing the name of the best
     *                  supported authentication method or a PEAR_Error object
     *                  if a failure condition is encountered.
     * @access private
     * @since  1.0
     */
    function _getBestAuthMethod($userMethod = null)
    {

/*
       return 'USER';
       return 'APOP';
       return 'DIGEST-MD5';
       return 'CRAM-MD5';
*/


        $this->_parseCapability();

        //unset($this->_capability['sasl']);

       if( isset($this->_capability['sasl']) ){
           $serverMethods=$this->_capability['sasl'];
       }else{
            $serverMethods=array('USER');
            // Check for timestamp before attempting APOP
            if ($this->_timestamp != null)
            {
                $serverMethods[] = 'APOP';
            }
       }

        if($userMethod !== null && $userMethod !== true ){
            $methods = array();
            $methods[] = $userMethod;
            return $userMethod;
        }else{
            $methods = $this->supportedAuthMethods;
        }

        if( ($methods != null) && ($serverMethods != null)){

            foreach ( $methods as $method ) {

                if ( in_array( $method , $serverMethods ) ) {
                    return $method;
                }
            }
            $serverMethods=implode(',' , $serverMethods );
            $myMethods=implode(',' ,$this->supportedAuthMethods);
            return $this->_raiseError("$method NOT supported authentication method!. This server " .
                "supports these methods: $serverMethods, but I support $myMethods");
        }else{
            return $this->_raiseError("This server don't support any Auth methods");
        }
    }






    /* Handles the authentication using any known method
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     * @param string The method to use ( if $usermethod == '' then the class chooses the best method (the stronger is the best ) )
     *
     * @return mixed  string or PEAR_Error
     *
     * @access private
     * @since  1.0
     */
    function _cmdAuthenticate($uid , $pwd , $userMethod = null )
    {


        if ( PEAR::isError( $method = $this->_getBestAuthMethod($userMethod) ) ) {
            return $method;
        }

        switch ($method) {
            case 'DIGEST-MD5':
                $result = $this->_authDigest_MD5( $uid , $pwd );
                break;
            case 'CRAM-MD5':
                $result = $this->_authCRAM_MD5( $uid , $pwd );
                break;
            case 'LOGIN':
                $result = $this->_authLOGIN( $uid , $pwd );
                break;
            case 'PLAIN':
                $result = $this->_authPLAIN( $uid , $pwd );
                break;
            case 'APOP':
                $result = $this->_cmdApop( $uid , $pwd );
                // if APOP fails fallback to USER auth
                if( PEAR::isError( $result ) ){
                    //echo "APOP FAILED!!!\n";
                    $result=$this->_authUSER( $uid , $pwd );
                }
                break;
            case 'USER':
                $result = $this->_authUSER( $uid , $pwd );
            break;


            default :
                $result = $this->_raiseError( "$method is not a supported authentication method" );
                break;
        }
        return $result;
    }




     /* Authenticates the user using the USER-PASS method.
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     *
     * @return mixed    true on success or PEAR_Error on failure
     *
     * @access private
     * @since  1.0
     */
    function _authUSER($user, $pass  )
    {
        if ( PEAR::isError($ret=$this->_cmdUser($user) ) ){
            return $ret;
        }
        if ( PEAR::isError($ret=$this->_cmdPass($pass) ) ){
            return $ret;
        }
        return true;
    }








     /* Authenticates the user using the PLAIN method.
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     *
     * @return array Returns an array containing the response
     *
     * @access private
     * @since  1.0
     */
    function _authPLAIN($user, $pass  )
    {
        $cmd=sprintf('AUTH PLAIN %s', base64_encode( chr(0) . $user . chr(0) . $pass ) );

        if ( PEAR::isError( $ret = $this->_send($cmd) ) ) {
            return $ret;
        }
        if ( PEAR::isError( $challenge = $this->_recvLn() ) ){
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }
        
        return true;
    }



     /* Authenticates the user using the PLAIN method.
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     *
     * @return array Returns an array containing the response
     *
     * @access private
     * @since  1.0
     */
    function _authLOGIN($user, $pass  )
    {
        $this->_send('AUTH LOGIN');

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }


        if ( PEAR::isError( $ret = $this->_send(sprintf('%s', base64_encode($user))) ) ) {
            return $ret;
        }

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }

        if ( PEAR::isError( $ret = $this->_send(sprintf('%s', base64_encode($pass))) ) ) {
            return $ret;
        }

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        return $this->_checkResponse($challenge);
    }





     /* Authenticates the user using the CRAM-MD5 method.
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     *
     * @return array Returns an array containing the response
     *
     * @access private
     * @since  1.0
     */
    function _authCRAM_MD5($uid, $pwd )
    {
        if ( PEAR::isError( $ret = $this->_send( 'AUTH CRAM-MD5' ) ) ) {
            return $ret;
        }

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }

        // remove '+ '
        
        $challenge=substr($challenge,2);
        
        $challenge = base64_decode( $challenge );

        $cram = &Auth_SASL::factory('crammd5');
        $auth_str = base64_encode( $cram->getResponse( $uid , $pwd , $challenge ) );


        if ( PEAR::isError($error = $this->_send( $auth_str ) ) ) {
            return $error;
        }
        if ( PEAR::isError( $ret = $this->_recvLn() ) ) {
            return $ret;
        }
        //echo "RET:$ret\n";
        return $this->_checkResponse($ret);
    }



     /* Authenticates the user using the DIGEST-MD5 method.
     *
     * @param string The userid to authenticate as.
     * @param string The password to authenticate with.
     * @param string The efective user
     *
     * @return array Returns an array containing the response
     *
     * @access private
     * @since  1.0
     */
    function _authDigest_MD5($uid, $pwd)
    {
        if ( PEAR::isError( $ret = $this->_send( 'AUTH DIGEST-MD5' ) ) ) {
            return $ret;
        }

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }

        // remove '+ '
        $challenge=substr($challenge,2);

        $challenge = base64_decode( $challenge );
        $digest = &Auth_SASL::factory('digestmd5');
        $auth_str = base64_encode($digest->getResponse($uid, $pwd, $challenge, "localhost", "pop3" ));

        if ( PEAR::isError($error = $this->_send( $auth_str ) ) ) {
            return $error;
        }

        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        if( PEAR::isError($ret=$this->_checkResponse($challenge) )){
            return $ret;
        }
         /*
         * We don't use the protocol's third step because POP3 doesn't allow
         * subsequent authentication, so we just silently ignore it.
         */

        if ( PEAR::isError( $challenge = $this->_send("\r\n") ) ) {
            return $challenge ;
        }
        
        if ( PEAR::isError( $challenge = $this->_recvLn() ) ) {
            return $challenge;
        }
        
        return $this->_checkResponse($challenge);
        

    }










    /*
    * Sends the APOP command
    *
    * @param  $user Username to send
    * @param  $pass Password to send
    * @return bool Success/Failure
    */
    function _cmdApop($user, $pass)
    {
        if ($this->_state == NET_POP3_STATE_AUTHORISATION) {

            if (!empty($this->_timestamp)) {
                if(PEAR::isError($data = $this->_sendCmd('APOP ' . $user . ' ' . $this->encryptOld($this->_timestamp . $pass)) ) ){
                    return $data;
                }
                $this->_state = NET_POP3_STATE_TRANSACTION;
                return true;
            }
        }
        return $this->_raiseError('Not In NET_POP3_STATE_AUTHORISATION State1');
    }















    /*
    * Returns the raw headers of the specified message.
    *
    * @param  integer $msg_id Message number
    * @return mixed   Either raw headers or false on error
    */
    function getRawHeaders($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            return $this->_cmdTop($msg_id, 0);
        }

        return false;
    }

    /*
    * Returns the  headers of the specified message in an
    * associative array. Array keys are the header names, array
    * values are the header values. In the case of multiple headers
    * having the same names, eg Received:, the array value will be
    * an indexed array of all the header values.
    *
    * @param  integer $msg_id Message number
    * @return mixed   Either array of headers or false on error
    */
    function getParsedHeaders($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {

            $raw_headers = rtrim($this->getRawHeaders($msg_id));

            $raw_headers = preg_replace("/\r\n[ \t]+/", ' ', $raw_headers); // Unfold headers
            $raw_headers = explode("\r\n", $raw_headers);
            foreach ($raw_headers as $value) {
                $name  = substr($value, 0, $pos = strpos($value, ':'));
                $value = ltrim(substr($value, $pos + 1));
                if (isset($headers[$name]) AND is_array($headers[$name])) {
                    $headers[$name][] = $value;
                } elseif (isset($headers[$name])) {
                    $headers[$name] = array($headers[$name], $value);
                } else {
                    $headers[$name] = $value;
                }
            }

            return $headers;
        }

        return false;
    }

    /*
    * Returns the body of the message with given message number.
    *
    * @param  integer $msg_id Message number
    * @return mixed   Either message body or false on error
    */
    function getBody($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            $msg = $this->_cmdRetr($msg_id);
            return substr($msg, strpos($msg, "\r\n\r\n")+4);
        }

        return false;
    }

    /*
    * Returns the entire message with given message number.
    *
    * @param  integer $msg_id Message number
    * @return mixed   Either entire message or false on error
    */
    function getMsg($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            return $this->_cmdRetr($msg_id);
        }

        return false;
    }

    /*
    * Returns the size of the maildrop
    *
    * @return mixed Either size of maildrop or false on error
    */
    function getSize()
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if (isset($this->_maildrop['size'])) {
                return $this->_maildrop['size'];
            } else {
                list(, $size) = $this->_cmdStat();
                return $size;
            }
        }

        return false;
    }

    /*
    * Returns number of messages in this maildrop
    *
    * @return mixed Either number of messages or false on error
    */
    function numMsg()
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if (isset($this->_maildrop['num_msg'])) {
                return $this->_maildrop['num_msg'];
            } else {
                list($num_msg, ) = $this->_cmdStat();
                return $num_msg;
            }
        }

        return false;
    }

    /*
    * Marks a message for deletion. Only will be deleted if the
    * disconnect() method is called.
    *
    * @param  integer $msg_id Message to delete
    * @return bool Success/Failure
    */
    function deleteMsg($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            return $this->_cmdDele($msg_id);
        }

        return false;
    }

    /*
    * Combination of LIST/UIDL commands, returns an array
    * of data
    *
    * @param  integer $msg_id Optional message number
    * @return mixed Array of data or false on error
    */
    function getListing($msg_id = null)
    {
    
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if (!isset($msg_id)){
            
                $list=array();
                if ($list = $this->_cmdList()) {
                    if ($uidl = $this->_cmdUidl()) {
                        foreach ($uidl as $i => $value) {
                            $list[$i]['uidl'] = $value['uidl'];
                        }
                    }
                    return $list;
                }else{
                    return array();
                }
            } else {
                if ($list = $this->_cmdList($msg_id) AND $uidl = $this->_cmdUidl($msg_id)) {
                    return array_merge($list, $uidl);
                }
            }
        }

        return false;
    }

    /*
    * Sends the USER command
    *
    * @param  string $user Username to send
    * @return bool  Success/Failure
    */
    function _cmdUser($user)
    {
        if ($this->_state == NET_POP3_STATE_AUTHORISATION) {
            return $this->_sendCmd('USER ' . $user);
        }
        return $this->_raiseError('Not In NET_POP3_STATE_AUTHORISATION State');
    }


    /*
    * Sends the PASS command
    *
    * @param  string $pass Password to send
    * @return bool  Success/Failure
    */
    function _cmdPass($pass)
    {
        if ($this->_state == NET_POP3_STATE_AUTHORISATION) {
            return $this->_sendCmd('PASS ' . $pass);
        }
        return $this->_raiseError('Not In NET_POP3_STATE_AUTHORISATION State');
    }


    /*
    * Sends the STAT command
    *
    * @return mixed Indexed array of number of messages and
    *               maildrop size, or false on error.
    */
    function _cmdStat()
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if(!PEAR::isError($data = $this->_sendCmd('STAT'))){
                sscanf($data, '+OK %d %d', $msg_num, $size);
                $this->_maildrop['num_msg'] = $msg_num;
                $this->_maildrop['size']    = $size;

                return array($msg_num, $size);
            }
        }
        return false;
    }


    /*
    * Sends the LIST command
    *
    * @param  integer $msg_id Optional message number
    * @return mixed   Indexed array of msg_id/msg size or
    *                 false on error
    */
    function _cmdList($msg_id = null)
    {
        $return=array();
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if (!isset($msg_id)) {
                if(!PEAR::isError($data = $this->_sendCmd('LIST') )){
                    $data = $this->_getMultiline();
                    $data = explode("\r\n", $data);                    
                    foreach ($data as $line) {
                        if($line !=''){
                            sscanf($line, '%s %s', $msg_id, $size);
                            $return[] = array('msg_id' => $msg_id, 'size' => $size);
                        }
                    }
                    return $return;
                }
            } else {
                if(!PEAR::isError($data = $this->_sendCmd('LIST ' . $msg_id))){
                    if($data!=''){
                        sscanf($data, '+OK %d %d', $msg_id, $size);
                        return array('msg_id' => $msg_id, 'size' => $size);
                    }
                    return array();
                }
            }
        }
        

        return false;
    }


    /*
    * Sends the RETR command
    *
    * @param  integer $msg_id The message number to retrieve
    * @return mixed   The message or false on error
    */
    function _cmdRetr($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if(!PEAR::isError($data = $this->_sendCmd('RETR ' . $msg_id) )){
                $data = $this->_getMultiline();
                return $data;
            }
        }

        return false;
    }


    /*
    * Sends the DELE command
    *
    * @param  integer $msg_id Message number to mark as deleted
    * @return bool Success/Failure
    */
    function _cmdDele($msg_id)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            return $this->_sendCmd('DELE ' . $msg_id);
        }

        return false;
    }


    /*
    * Sends the NOOP command
    *
    * @return bool Success/Failure
    */
    function _cmdNoop()
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if(!PEAR::isError($data = $this->_sendCmd('NOOP'))){
                return true;
            }
        }

        return false;
    }

    /*
    * Sends the RSET command
    *
    * @return bool Success/Failure
    */
    function _cmdRset()
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {
            if(!PEAR::isError($data = $this->_sendCmd('RSET'))){
                return true;
            }
        }

        return false;
    }

    /*
    * Sends the QUIT command
    *
    * @return bool Success/Failure
    */
    function _cmdQuit()
    {
        $data = $this->_sendCmd('QUIT');
        $this->_state = NET_POP3_STATE_DISCONNECTED;
        $this->_socket->disconnect();

        return (bool)$data;
    }


    /*
    * Sends the TOP command
    *
    * @param  integer  $msg_id    Message number
    * @param  integer  $num_lines Number of lines to retrieve
    * @return mixed Message data or false on error
    */
    function _cmdTop($msg_id, $num_lines)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {

            if(!PEAR::isError($data = $this->_sendCmd('TOP ' . $msg_id . ' ' . $num_lines))){
                return $this->_getMultiline();
            }
        }

        return false;
    }

    /*
    * Sends the UIDL command
    *
    * @param  integer $msg_id Message number
    * @return mixed indexed array of msg_id/uidl or false on error
    */
    function _cmdUidl($msg_id = null)
    {
        if ($this->_state == NET_POP3_STATE_TRANSACTION) {

            if (!isset($msg_id)) {
                if(!PEAR::isError($data = $this->_sendCmd('UIDL') )){
                    $data = $this->_getMultiline();
                    $data = explode("\r\n", $data);
                    foreach ($data as $line) {
                        sscanf($line, '%d %s', $msg_id, $uidl);
                        $return[] = array('msg_id' => $msg_id, 'uidl' => $uidl);
                    }

                    return $return;
                }
            } else {

                $data = $this->_sendCmd('UIDL ' . $msg_id);
                sscanf($data, '+OK %d %s', $msg_id, $uidl);
                return array('msg_id' => $msg_id, 'uidl' => $uidl);
            }
        }

        return false;
    }









    /*
    * Sends a command, checks the reponse, and
    * if good returns the reponse, other wise
    * returns false.
    *
    * @param  string $cmd  Command to send (\r\n will be appended)
    * @return mixed First line of response if successful, otherwise false
    */
    function _sendCmd($cmd)
    {
        if (PEAR::isError($result = $this->_send($cmd) )){
            return $result ;
        }

        if (PEAR::isError($data = $this->_recvLn() )){
            return $data;
        }
        
        if ( strtoupper(substr($data, 0, 3)) == '+OK') {
            return $data;
        }
        
        
        return $this->_raiseError($data);
    }

    /*
    * Reads a multiline reponse and returns the data
    *
    * @return string The reponse.
    */
    function _getMultiline()
    {
        $data = '';
        while(!PEAR::isError($tmp = $this->_recvLn() ) ) {
            if($tmp == '.'){
                return substr($data, 0, -2);
            }
            if (substr($tmp, 0, 2) == '..') {
                $tmp = substr($tmp, 1);
            }
            $data .= $tmp . "\r\n";
        }
        return substr($data, 0, -2);
    }


   /**
    * Sets the bebug state
    *
    * @param  bool $debug 
    * @access public
    * @return void
    */
    function setDebug($debug=true)
    {
        $this->_debug=$debug;
    }





   /**
     * Send the given string of data to the server.
     *
     * @param   string  $data       The string of data to send.
     *
     * @return  mixed   True on success or a PEAR_Error object on failure.
     *
     * @access  private
     * @since   1.0
     */
    function _send($data)
    {
        if ($this->_debug) {
            echo "C: $data\n";
        }

        if (PEAR::isError($error = $this->_socket->writeLine($data))) {
            return $this->_raiseError('Failed to write to socket: ' . $error->getMessage());
        }
        return true;
    }



     /**
     * Receive the given string of data from the server.
     *
     * @return  mixed   a line of response on success or a PEAR_Error object on failure.
     *
     * @access  private
     * @since  1.0
     */
    function _recvLn()
    {
        if (PEAR::isError( $lastline = $this->_socket->readLine( 8192 ) ) ) {
            return $this->_raiseError('Failed to write to socket: ' . $this->lastline->getMessage() );
        }
        if($this->_debug){
            // S: means this data was sent by  the POP3 Server
            echo "S:$lastline\n" ;
        }
        return $lastline;
    }

     /**
     * Checks de server Response
     *
     * @param  string $response the response
     * @return  mixed   true on success or a PEAR_Error object on failure.
     *
     * @access  private
     * @since  1.3.3
     */

    function _checkResponse($response)
    {
        if (@substr(strtoupper($response), 0, 3) == '+OK') {
            return true;
        }else{
            if (@substr(strtoupper($response), 0, 4) == '-ERR') {
                return $this->_raiseError($response);
            }else{
                if (@substr(strtoupper($response), 0, 2) == '+ ') {
                    return true;
                }
            }
    
        }
        return $this->_raiseError("Unknown Response ($response)");
    }
    
    public function encryptOld($string)
    {
        if (!class_exists('G')) {
            $realdocuroot = str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] );
            $docuroot = explode( '/', $realdocuroot );
            array_pop( $docuroot );
            $pathhome = implode( '/', $docuroot ) . '/';
            array_pop( $docuroot );
            $pathTrunk = implode( '/', $docuroot ) . '/';
            require_once($pathTrunk.'gulliver/system/class.g.php');
        }
        return G::encryptOld($string);
    }
    


}

?>
