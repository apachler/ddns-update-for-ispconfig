<?php
echo "start <br/>";

if (! isset($_GET["username"])) die("No username found");
if (! isset($_GET["password"])) die("No password found");
if (! isset($_GET["url"])) die("No url found");

$firstdotpos=strpos($_GET["url"], '.');
if ($firstdotpos == false) {
	echo "Zone incorrecte <br/>";
	exit();
}

$dnszone=substr($_GET["url"], $firstdotpos+1) . '.';
echo "DNS Zone: " . $dnszone . "<br/>";

$soap_location = 'https://10.0.0.251:8080/remote/index.php';
$soap_uri = 'https://10.0.0.251:8080/remote/';

$ip = $_SERVER['REMOTE_ADDR'];
echo "Remote IP:" . $ip . "<br/>";
echo "Request DateTime " . time() . "</br>";

$client = new SoapClient(null, array('location' => $soap_location,
		'uri'      => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));
		
try {
	if($session_id = $client->login($_GET["username"], $_GET["password"])) {
		echo 'Logged successfull. Session ID:'.$session_id.'<br />';
	}

	// Charge la zone
	echo "Chargement de la zone: " . $dnszone." </br>";
	$zone = $client->dns_zone_get($session_id, array('origin' => $dnszone));
	if(count($zone)==0) {
		echo "DNS Zone not found</br>";
		$affected_rows=0;
	} 
	else
	{
		echo "DNS Zone found</br>";
		
		echo "Get Client ID from sys_userid<br/>";
		$client_id = $client->client_get_id($session_id,$zone[0]['sys_userid']);
		echo "Client_ID found: ".$client_id."<br/>";
		
		// echo "Zone content<br/>";
		// echo var_dump($zone);
		// echo "<br/>";
		

		//* Get the dns record
		echo "Record Content<br/>";
		$dns_record = $client->dns_a_get($session_id, array('name' => $_GET["url"]));
		var_dump($dns_record);
		echo "End content<br/>";
		
		// Change ip
		if ($dns_record[0]['data']!=$ip){
			$dns_record[0]['data'] = $ip;
			$affected_rows = $client->dns_a_update($session_id, 0, $dns_record[0]['id'], $dns_record[0]);

			echo "Old serial number ".$zone[0]['serial']."<br/>";
			$newserial = $zone[0]['serial']+1;
			echo "New serial number ".$newserial."<br/>";
			$zone[0]['serial'] = $newserial;
			echo "Update zone<br/>";
			$client->dns_zone_update($session_id, 0, $zone[0]['id'], $zone[0]);
			echo "Update zone finish<br/>";
		} else {
		
		echo "No update needed!</br>";
		}
	}
	
	echo "Number of records that have been changed in the database: ".$affected_rows."<br>";

	if($client->logout($session_id)) {
		echo 'Logged out.<br />';
	}


} catch (SoapFault $e) {
	echo $client->__getLastResponse();
	die('SOAP Error: '.$e->getMessage());
}


?>