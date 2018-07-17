<?php
require_once 'DB.php'; // PEAR/DB
require_once 'SOAP/Client.php';
require_once 'config.php';

class clientInfo {
    var $name;
    var $version;
    var $resultsURL;

    function clientInfo($ar=NULL) {
        if (is_array($ar)) {
            foreach ($ar as $k=>$v) {
                $this->$k = $v;
            }
        }
    }
}

class serverInfo {
    var $id;
    var $service_id;
    var $name;
    var $version;
    var $endpointURL;
    var $wsdlURL;

    function serverInfo($ar=NULL) {
        if (is_array($ar)) {
            foreach ($ar as $k=>$v) {
                $this->$k = $v;
            }
        }
    }
}

class Service {
    var $id;
    var $name;
    var $description;
    var $wsdlURL;
    var $websiteURL;

    function Service($ar=NULL) {
        if (is_array($ar)) {
            foreach ($ar as $k=>$v) {
                $this->$k = $v;
            }
        }
    }
}

class subscriberInfo {
    var $notificationID;
    var $expires; /* dateTime */
}

class ChangeItem {
    var $id;
    var $timestamp; /* dateTime */
    var $headline;
    var $notes;
    var $url;
}

function getLocalInteropServer($testname,$id,$localBaseUrl='http://localhost/soap_interop/') {
    $localServer = array();
    $localServer['service_id']=$id;
    $localServer['name']='Local PEAR::SOAP';
    $localServer['version']=SOAP_LIBRARY_VERSION;
    switch ($testname) {
    case 'Round 2 Base':
        $localServer['endpointURL']=$localBaseUrl.'server_Round2Base.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/interop.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 2 Group B':
        $localServer['endpointURL']=$localBaseUrl.'server_Round2GroupB.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/interopB.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 2 Group C':
        $localServer['endpointURL']=$localBaseUrl.'server_Round2GroupC.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/echoheadersvc.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D EmptySA':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDEmptySA.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/emptysa.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D Compound 1':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDCompound1.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/compound1.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D Compound 2':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDCompound2.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/compound2.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D DocLit':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDDocLit.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/InteropTestDocLit.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D DocLitParams':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDDocLitParams.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/InteropTestDocLitParameters.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D Import 1':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDImport1.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/import1.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D Import 2':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDImport2.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/import2.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D Import 3':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDImport3.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/import3.wsdl.php';
        return new serverInfo($localServer);
    case 'Round 3 Group D RpcEnc':
        $localServer['endpointURL']=$localBaseUrl.'server_Round3GroupDRpcEnc.php';
        $localServer['wsdlURL']=$localBaseUrl.'wsdl/InteropTestRpcEnc.wsdl.php';
        return new serverInfo($localServer);
    #case 'Round 3 Group E DocLit':
    #case 'Round 3 Group E RpcEnc':
    #case 'Round 3 Group F Extensibility':
    #case 'Round 3 Group F ExtensibilityRequired':
    #case 'Round 3 Group F Headers':
    #case 'Round 4 DIME/Doc Attachments':
    #case 'Round 4 DIME/RPC Attachments':
    #case 'Round 4 MIME/Doc Attachments':
    #case 'Round 4 SwA/RPC Attachments':
    }
    return NULL;
}

class SOAP_Interop_registrationAndNotificationService_ServicesPort extends SOAP_Client {
    function SOAP_Interop_registrationAndNotificationService_ServicesPort() {
        $this->SOAP_Client("http://soap.4s4c.com/registration/soap.asp", 0);
        $this->_auto_translation = true;
    }
    function &ServiceList() {
        return $this->call("ServiceList", 
                        $v = NULL, 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/services',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/services#ServiceList',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &Servers($serviceID) {
        return $this->call("Servers", 
                        $v = array("serviceID"=>$serviceID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/services',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/services#Servers',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &Clients($serviceID) {
        return $this->call("Clients", 
                        $v = array("serviceID"=>$serviceID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/services',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/services#Clients',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}
class SOAP_Interop_registrationAndNotificationService_ClientsPort extends SOAP_Client {
    function SOAP_Interop_registrationAndNotificationService_ClientsPort() {
        $this->SOAP_Client("http://soap.4s4c.com/registration/soap.asp", 0);
        $this->_auto_translation = true;
    }
    function &RegisterClient($serviceID, $clientInfo) {
        return $this->call("RegisterClient", 
                        $v = array("serviceID"=>$serviceID, "clientInfo"=>$clientInfo), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/clients',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/clients#RegisterClient',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &UpdateClient($clientID, $clientInfo) {
        return $this->call("UpdateClient", 
                        $v = array("clientID"=>$clientID, "clientInfo"=>$clientInfo), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/clients',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/clients#UpdateClient',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &RemoveClient($clientID) {
        return $this->call("RemoveClient", 
                        $v = array("clientID"=>$clientID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/clients',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/clients#RemoveClient',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}
class SOAP_Interop_registrationAndNotificationService_ServersPort extends SOAP_Client {
    function SOAP_Interop_registrationAndNotificationService_ServersPort() {
        $this->SOAP_Client("http://soap.4s4c.com/registration/soap.asp", 0);
        $this->_auto_translation = true;
    }
    function &RegisterServer($serviceID, $serverInfo) {
        return $this->call("RegisterServer", 
                        $v = array("serviceID"=>$serviceID, "serverInfo"=>$serverInfo), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/servers',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/servers#RegisterServer',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &UpdateServer($serverID, $serverInfo) {
        return $this->call("UpdateServer", 
                        $v = array("serverID"=>$serverID, "serverInfo"=>$serverInfo), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/servers',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/servers#UpdateServer',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &RemoveServer($serverID) {
        return $this->call("RemoveServer", 
                        $v = array("serverID"=>$serverID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/servers',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/servers#RemoveServer',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}
class SOAP_Interop_registrationAndNotificationService_SubscriberPort extends SOAP_Client {
    function SOAP_Interop_registrationAndNotificationService_SubscriberPort() {
        $this->SOAP_Client("http://soap.4s4c.com/registration/soap.asp", 0);
        $this->_auto_translation = true;
    }
    function &Subscribe($serviceID, $ServerChanges, $ClientChanges, $NotificationURL) {
        return $this->call("Subscribe", 
                        $v = array("serviceID"=>$serviceID, "ServerChanges"=>$ServerChanges, "ClientChanges"=>$ClientChanges, "NotificationURL"=>$NotificationURL), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/subscriber',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/subscriber#Subscribe',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &Renew($notificationID) {
        return $this->call("Renew", 
                        $v = array("notificationID"=>$notificationID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/subscriber',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/subscriber#Renew',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function &Cancel($notificationID) {
        return $this->call("Cancel", 
                        $v = array("notificationID"=>$notificationID), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/subscriber',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/subscriber#Cancel',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}
class SOAP_Interop_registrationAndNotificationService_ChangeLogPort extends SOAP_Client {
    function SOAP_Interop_registrationAndNotificationService_ChangeLogPort() {
        $this->SOAP_Client("http://soap.4s4c.com/registration/soap.asp", 0);
        $this->_auto_translation = true;
    }
    function &RecentChanges($MaxEntries) {
        return $this->call("RecentChanges", 
                        $v = array("MaxEntries"=>$MaxEntries), 
                        array('namespace'=>'http://soap.pocketsoap.com/registration/changeLog',
                            'soapaction'=>'http://soap.pocketsoap.com/registration/changeLog#RecentChanges',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}

class SOAP_Interop_registrationDB {
    var $DSN;
    var $dbc = NULL;
    
    var $client; // soap_client
    var $services;
    var $currentServiceId;
    var $servers;
    var $clients;
    
    function SOAP_Interop_registrationDB()
    {
        global $interopConfig;
	$this->DSN = $interopConfig['DSN'];
        $this->client =& new SOAP_Interop_registrationAndNotificationService_ServicesPort();
        $this->connectDB();
        $this->getServiceList();
    }
    
    function connectDB()
    {
        if (!$this->dbc)
            $this->dbc =& DB::connect($this->DSN, true);
        if (PEAR::isError($this->dbc)) {
            echo $this->dbc->getMessage();
            $this->dbc = NULL;
            return false;
        }
        return true;
    }
    
    function updateDB()
    {
        $this->updateServiceDb();
        $this->updateServerDb();
        $this->updateClientsDb();
    }
    
    function &retreiveServiceList()
    {
        if (!$this->services) {
            $this->services =& $this->client->ServiceList();
        }
        return $this->services;
    }
    
    function &retreiveServerList($serviceID)
    {
        if (!$this->servers || $this->currentServiceId != $serviceID) {
            $this->currentServiceId = $serviceID;
            $this->servers =& $this->client->Servers($serviceID);
        }
        return $this->servers;
    }
    
    function &retreiveClientList($serviceID)
    {
        if (!$this->clients || $this->currentServiceId != $serviceID) {
            $this->currentServiceId = $serviceID;
            $this->clients =& $this->client->Clients($serviceID);
        }
        return $this->clients;
    }
    
    function updateServiceDb()
    {
        if (!$this->connectDB()) return false;
        $this->retreiveServiceList();
        echo "Updating Services\n";
        foreach ($this->services as $service) {
            $res = $this->dbc->getRow("select id from services where id='{$service->id}'");
            if ($res && !PEAR::isError($res)) {
                $res = $this->dbc->query("update services set name='{$service->name}',".
                                         "description='{$service->description}',wsdlURL='{$service->wsdlURL}',".
                                         "websiteURL='{$service->websiteURL}' where id='{$service->id}'");
            } else {
                $res = $this->dbc->query("insert into services (id,name,description,wsdlURL,websiteURL) ".
                                         "values('{$service->id}','{$service->name}','{$service->description}','{$service->wsdlURL}','{$service->websiteURL}')");
            }
        }
    }

    function _updateOrAddServer($id, $server) {
        $res = $this->dbc->getRow("select * from serverinfo where service_id='$id' and name='{$server->name}'");
        if ($res && !PEAR::isError($res)) {
            $res = $this->dbc->query("update serverinfo set ".
                                        "version='{$server->version}', ".
                                        "endpointURL='{$server->endpointURL}', ".
                                        "wsdlURL='{$server->wsdlURL}' where id={$res->id}");
        } else {
            $res = $this->dbc->query("insert into serverinfo (service_id,name,version,endpointURL,wsdlURL) ".
                                        "values('$id','{$server->name}','{$server->version}','{$server->endpointURL}','{$server->wsdlURL}')");
        }
        if (PEAR::isError($res)) {
            echo $res->toString() . "\n";
        }
    }
    
    function updateServerDb()
    {
        global $INTEROP_LOCAL_SERVER;
        if (!$this->connectDB()) return false;
        $this->retreiveServiceList();
        $c = count($this->services);
        for ($i=0;$i<$c;$i++) {
            $this->retreiveServerList($this->services[$i]->id);
            echo "Updating Servers for {$this->services[$i]->name}\n";
            if (!$this->servers) continue;
            foreach ($this->servers as $server) {
                $this->_updateOrAddServer($this->services[$i]->id, $server);
            }
            // add the local server now
            if ($INTEROP_LOCAL_SERVER) {
                $server = getLocalInteropServer($this->services[$i]->name, $this->services[$i]->id);
                if ($server) {
                    $this->_updateOrAddServer($this->services[$i]->id, $server);
                }
            }
        }
    }    

    function updateClientsDb()
    {
        if (!$this->connectDB()) return false;
        $this->retreiveServiceList();
        foreach ($this->services as $service) {
            $this->retreiveClientList($service->id);
            echo "Updating Clients for {$service->name}\n";
            if (!$this->clients) continue;
            foreach ($this->clients as $client) {
                $res = $this->dbc->getRow("select id from clientinfo where id='{$service->id}' and name='{$client->name}'");
                if ($res && !PEAR::isError($res)) {
                    $res = $this->dbc->query("update clientinfo set ".
                                             "version='{$client->version}', ".
                                             "resultsURL='{$client->resultsURL}' where ".
                                             "id='{$service->id}',name='{$client->name}'");
                } else {
                    $res = $this->dbc->query("insert into clientinfo (id,name,version,resultsURL) ".
                                             "values('{$service->id}','{$client->name}','{$client->version}','{$client->resultsURL}')");
                }
                
            }
        }
    }
    
    function &getServiceList($forcefetch=FALSE)
    {
        if (!$forcefetch && !$this->services) {
            $this->dbc->setFetchMode(DB_FETCHMODE_OBJECT,'Service');
            $this->services = $this->dbc->getAll('select * from services',NULL, DB_FETCHMODE_OBJECT );
        }
        if ($forcefetch || !$this->services) {
            $this->updateServiceDb();
        }
        return $this->services;
    }
    
    function &getServerList($serviceID,$forcefetch=FALSE)
    {
        if (!$forcefetch && (!$this->servers || $this->currentServiceId != $serviceID)) {
            $this->dbc->setFetchMode(DB_FETCHMODE_OBJECT,'serverInfo');
            $this->servers = $this->dbc->getAll("select * from serverinfo where service_id = '$serviceID'",NULL, DB_FETCHMODE_OBJECT );
        }
        if ($forcefetch || !$this->servers) {
            $this->updateServerDb();
            $this->dbc->setFetchMode(DB_FETCHMODE_OBJECT,'serverInfo');
            $this->servers = $this->dbc->getAll("select * from serverinfo where service_id = '$serviceID'",NULL, DB_FETCHMODE_OBJECT );
            if (!$this->servers) {
                die("Error retrieving server list!\n");
            }
        }
        return $this->servers;        
    }    

    function &getClientList($serviceID,$forcefetch=FALSE)
    {
        if (!$forcefetch && (!$this->clients || $this->currentServiceId != $serviceID)) {
            $this->dbc->setFetchMode(DB_FETCHMODE_OBJECT,'clientInfo');
            $this->clients = $this->dbc->getAll("select * from clientinfo where id = '$serviceID'",NULL, DB_FETCHMODE_OBJECT );
        }
        if ($forcefetch || !$this->clients) {
            $this->updateClientDb();
            $this->dbc->setFetchMode(DB_FETCHMODE_OBJECT,'clientInfo');
            $this->clients = $this->dbc->getAll("select * from clientinfo where id = '$serviceID'",NULL, DB_FETCHMODE_OBJECT );
            if (!$this->clients) {
                die("Error retrieving client list!\n");
            }
        }
        return $this->clients;        
    }
    
    function &findService($serviceName)
    {
        $this->getServiceList();
        $c = count($this->services);
        for ($i=0 ; $i<$c; $i++) {
            if (strcmp($serviceName, $this->services[$i]->name)==0) return $this->services[$i];
        }
        return NULL;
    }
}

#$reg =& new SOAP_Interop_registrationAndNotificationDB();
#$reg->updateDB();
//print_r($l);
?>