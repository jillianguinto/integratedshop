<?php


function getSignature($req=array()) {			
	$source = 	$req['MerchantKey'].
				$req['MerchantCode'].
				$req['RefNo'].
				$req['Amount'].
				$req['Currency'];		
	
	return base64_encode(getHex2bin(sha1($source)));
}

function getHex2bin($hexSource)
{
	$bin = '';
	for ($i=0;$i<strlen($hexSource);$i=$i+2)
	{
		$bin .= chr(hexdec(substr($hexSource,$i,2)));
	}
	return $bin;
}


$conf = array();
$conf['MerchantKey']	= 'jpdYVmglr2';
$conf['MerchantCode']	= 'PH00093';
$conf['PaymentId'] 		= '1';
$conf['RefNo'] 			= '12000010';
$conf['Amount']			= '10000';
$conf['Currency'] 		= 'PHP';
$conf['ProdDesc'] 		= 'TESTING DESCRIPTION';
$conf['UserName'] 		= 'diszo.sasil';
$conf['UserEmail'] 		= 'diszo.sasil@movent.com';
$conf['UserContact'] 	= '5198016';
$conf['Remark'] 		= 'Testing';
$conf['Signature'] 		= getSignature($conf);
$conf['ResponseURL'] 	= 'http://healthfirst.ecomqa.com/ipay88/process/response';
$conf['BackendURL'] 	= 'http://healthfirst.ecomqa.com/ipay88/process/response';

?>

<html>
<body>
<form action="https://sandbox.ipay88.com.ph/epayment/entry.asp" method="post" name="ePayment">
	
	<?php
	$conf2 = $conf;
	unset($conf2['MerchantKey']);
	?>
	
	<?php foreach($conf2 as $key => $value) : ?>
		<input type="hidden" name="<?php echo $key?>" value="<?php echo $value; ?>"/>
		
	<?php endforeach; ?>
	
	<input type="submit" name="submit" value="Proceed with iPay88 Payment"/>
	
</form>
</body>
</html>

<?php


if(isset($_POST['submit'])){
		
	echo "<pre>";
	print_r($_POST);
	
	
}




?>