<?php
$baseURL = dirname("../exportcsvQA"); 
$xmlfile = $baseURL . "/app/etc/local.xml";

$xml=simplexml_load_file($xmlfile, 'SimpleXMLElement', LIBXML_NOCDATA) or die("Error: Cannot create object");
$json = json_encode($xml);
$array = json_decode($json,TRUE);
$arrConnection = $array['global']['resources']['default_setup']['connection'];

$servername	= $arrConnection['host'];
$username 	= $arrConnection['username'];
$password 	= $arrConnection['password'];
$dbname 	= $arrConnection['dbname'];

	if (!$conn = mysql_connect($servername, $username, $password)) {
		echo 'Could not connect to mysql';
		exit;
	}

	if (!mysql_select_db($dbname, $conn)) {
		echo 'Could not select database';
		exit; 
	}

	
?>