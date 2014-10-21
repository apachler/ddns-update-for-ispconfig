<?php

class log {
    private $file = "log.txt" ; 
    
    function debug($log) {
        $log = date('Y-m-d H:i:s')." ".$log."\r\n"; 
        $maj = fopen($this->file,"a"); // On ouvre le fichier en lecture/écriture
        fseek($maj, 0, SEEK_END);
        fputs($maj, $log);            // On écrit dans le fichier
        fclose($maj);    
    }
    
    function clean() {
        $maj = fopen($this->file,"w+"); // On ouvre le fichier en lecture/écriture
        ftruncate($maj,0);            // on efface le contenu d'un fichier
        fclose($maj);  
    }
   
}

$soap_location = 'https://10.0.0.251:8080/remote/index.php';
$soap_uri = 'https://10.0.0.251:8080/remote/';

$log = new log() ; 
$log->clean() ; // on clean le fichier log
$log->debug("Session started");

if(!isset($_GET["username"]) || !isset($_GET["password"])) {
    $log->debug('username or password not found in get method') ; 
    if (empty($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        $log->debug('Authentication abort');
        exit;
    }
}
else
{
	$_SERVER['PHP_AUTH_USER'] = $_GET['username'];
	$_SERVER['PHP_AUTH_PW'] = $_GET['password'];
	$log->debug("AUTH USER ".$_SERVER['PHP_AUTH_USER']);
    $log->debug("AUTH PASS ".$_SERVER['PHP_AUTH_PW']);
}


if (!isset($_GET['myip']))
{
	$ip = $_SERVER["REMOTE_ADDR"] ;
	$log->debug("IP not in URL, use REMOTE_ADDR: ".$ip);
}
else 
{
	$ip = $_GET['myip'];
	$log->debug("IP in URL: ".$ip);
}

$log->debug("Start script by ".$ip." at ".$date);

if (!isset($_GET["hostname"])) {
    echo "dnserr" ; 
    $log->debug("hostname is missing") ; 
	exit;
}

if(substr($_GET['hostname'], -1, 1) != '.') {
    $log->debug("HOSTNAME must finish with a dot (update format) : ".$_GET["hostname"]) ;
    $_GET['hostname'] .= '.' ; 
    $log->debug("HOSTNAME updated to ".$_GET['hostname']) ; 
}

$firstdotpos=strpos($_GET["hostname"], '.');

if ($firstdotpos == false) {
	$log->debug("Invalid HOSTNAME format : ".$_GET["hostname"]);
    echo "notqdn" ; 
	exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    $log->debug('ERROR: HTTP method ' . $_SERVER['REQUEST_METHOD'] . ' is not allowed. badagent');
    echo 'badagent' ;
	exit;
}

$dnszone=substr($_GET["hostname"], $firstdotpos+1);
// . '.';
$log->debug("DNS Zone: " . $dnszone);

$client = new SoapClient(null, array('location' => $soap_location,
		'uri'      => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));
		
try {
	if($session_id = $client->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
		$log->debug('Logged successfull. Session ID:'.$session_id);
	} else {
        $log->debug('Not logged. Session ID : '.$session_id) ; 
        echo "badauth" ; 
		exit;
    }

	// Charge la zone
	$log->debug("Load DNS Zone: " . $dnszone);
	$zone = $client->dns_zone_get($session_id, array('origin' => $dnszone));
	if(count($zone)==0) {
		$log->debug("DNS Zone not found at ".time());
        echo "dnserr" ; 
		$affected_rows=0;
        exit;
	} 
	else
	{
		$log->debug("DNS Zone found");
	
		//* Get the dns record
		$log->debug("Record Content");
		$dns_record = $client->dns_a_get($session_id, array('name' => $_GET["hostname"]));
		// var_dump($dns_record);
		$log->debug("End content");
		if ($dns_record[0]['data']!=$ip){
			$dns_record[0]['data'] = $ip;
			$affected_rows = $client->dns_a_update($session_id, 0, $dns_record[0]['id'], $dns_record[0]);

			$log->debug("Old serial number ".$zone[0]['serial']);
			$newserial = $zone[0]['serial']+1;
			$log->debug("New serial number ".$newserial);
			$zone[0]['serial'] = $newserial;
			$log->debug("Update zone");
			$client->dns_zone_update($session_id, 0, $zone[0]['id'], $zone[0]);
			$log->debug("Update zone finish");
		} else {
			echo "nochange";
			$log->debug("No update needed!");
			exit;
		}
	}
	
	$log->debug("Number of records that have been changed in the database: ".$affected_rows);

	if($client->logout($session_id)) {
		$log->debug('Logged out');
	}
    echo 'good';  
    
    $log->debug("End script by ".$ip." at ".date('Y-m-d H:i:s')) ; 

	exit;    

} catch (SoapFault $e) {
	echo $client->__getLastResponse();
    $log->debug($client->__getLastResponse());
    $log->debug("serious error") ; 
	die('SOAP Error: '.$e->getMessage());
}


?>