<html>
<body>
<form action="testrcbc.php" method="post">
	<input type="submit" name="submit" value="Simulate"/>
</form>
</body>
</html>

<?php



if(isset($_POST['submit'])){
	
	$conf = array();		
	$conf['merchantCode']		= '011'; 
	$conf['merchantName']		= 'United Laboratories'; 
	$conf['refno']				= '12000010';
	$conf['total_amt']			=  '10000';	
	$conf['custcode']			= '123'; 
	$conf['custEmail']			= 'diszo.sasil@movent.com';
	$conf['custMobile']			= '12345678';
	$conf['status']				= 'PENDING';
		
	try {

		$serviceUrl = 'http://122.55.28.62/getmerchantdata/service.asmx?wsdl';
		
		$params = array();
		$method = 'SavePaymentDetails';
		
        $client = new SoapClient($serviceUrl, array('soap_version' => SOAP_1_1));
		
        $result = $client->$method($params);
		
        
		print_r($result);

    } catch(SoapFault $e) {
		echo $e->getMessage();
    }
	
	
}




?>