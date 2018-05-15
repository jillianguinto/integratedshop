<?php
class Unilab_Healthplus_Model_Availablecredit
{
	public function addAvailableCredit($orderno){

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection->beginTransaction();

		$sales_order 	= Mage::getModel('sales/order')->load($orderId);

		$customer = Mage::getSingleton('customer/session')->getCustomer();

		$customer_email =  $customer->getEmail();

		
		$sql = "SELECT * FROM sales_flat_order WHERE increment_id = '$orderno'";
		$result = $connection->fetchRow($sql);
		$quoteid = $result['quote_id'];

		$get_points = Mage::getModel('healthplus/healthplus')->getPointsbyQuote();
		
		//Mage::log('points- '.$get_points,null,'healthplus.log');

		if($result){

			$sql_uhp = "SELECT * FROM unilab_health_plus WHERE email ='$customer_email'";
			$result_uhp = $connection->fetchRow($sql_uhp);
			//Mage::log('sql uhp- '.$sql_uhp,null,'healthplus.log');

		   	$available_credit_balance = number_format($result_uhp['available_balance']);
		   	//Mage::log('current points- '.$available_credit_balance,null,'healthplus.log');

		   	$new_avb = $available_credit_balance + $get_points;
		   	//Mage::log('new points- '.$new_avb,null,'healthplus.log');

			$update_health_credit = "UPDATE unilab_health_plus SET available_balance = '$new_avb' WHERE email = '$customer_email'";
		 	$connection->Query($update_health_credit);
		 	$connection->commit();
		 	Mage::log($update_health_credit,null,'healthplus.log');
		}
		 	
	}
}