<?php
class Unilab_Healthplus_Model_Healthplus 
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}

	public function getPointsbyOrder($orderId)
	{
		$storeid 		= Mage::app()->getStore()->getStoreId();
		$order_value 	= Mage::getStoreConfig('healthplus/healthplussettings/ordervalue', $storeid); //500
		$credit_value 	= Mage::getStoreConfig('healthplus/healthplussettings/creditvalue', $storeid); //20

		$sql = "SELECT * FROM sales_flat_order_item WHERE order_id ='$orderId'";
		$result = $this->_getConnection()->fetchAll($sql);
		
		foreach($result as $_result)
		{
			$prodid = $_result['product_id'];
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		
			if ($healthplus == '1') {
				$total = $total + $_result['row_total'];
			}
		}
		
		$subtotal = floor($total / $order_value);

		if($subtotal > 0){
			$points = $subtotal * $credit_value;
			
		}else{
			$points = 0;
		}

		return $points;
	}


	public function getPointsbyQuote()
	{
		
		$storeid 		= Mage::app()->getStore()->getStoreId();
		$order_value 	= Mage::getStoreConfig('healthplus/healthplussettings/ordervalue', $storeid); //500
		$credit_value 	= Mage::getStoreConfig('healthplus/healthplussettings/creditvalue', $storeid); //20
		
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$total = 0;
		foreach ($cart->getAllVisibleItems() as $item) 
		{ 		  
			$prodid = $item->getProduct()->getId();
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		  
		  if ($healthplus == '1') {
			  	$qty = $item->getQty();
				$price = $item->getProduct()->getPrice();
				$totalprice = $price * $qty;
				$total = $total + $totalprice;
		  }

		}

		$subtotal = floor($total / $order_value);

		if($subtotal > 0){
			$points = $subtotal * $credit_value;
			
		}else{
			$points = 0;
		}

		return $points;

	}


	public function addAvailablecredit($orderId)
	{
		$sales_order 	= Mage::getModel('sales/order')->load($orderId);
		$customer_email = $sales_order->getCustomerEmail();
		
		$quoteid 		= $sales_order->getQuoteId();
		$get_points 	= $this->getPointsbyOrder($orderId);

		$sql 	= "SELECT *  FROM unilab_health_plus WHERE email ='$customer_email'";
		$result = $this->_getConnection()->fetchRow($sql);

		$current_available_balance = $result['available_balance'];
		$available_balance = $current_available_balance + $get_points;

		//Update HealthPlus
		$update_health_credit = "UPDATE unilab_health_plus SET available_balance = '$available_balance' WHERE email = '$customer_email'";
		$this->_getConnection()->Query($update_health_credit);
	 	$this->_getConnection()->commit();

	}
	
	public function gethealthplusDiscount()
	{
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$total = 0;
		foreach ($cart->getAllVisibleItems() as $item) 
		{ 		  
			$prodid = $item->getProduct()->getId();
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		  
		  if ($healthplus == '1') {
			  	$qty = $item->getQty();
				$price = $item->getProduct()->getPrice();
				$totalprice = $price * $qty;
				$total = $total + $totalprice;
		  }

		}
		
		return $total;
	}
	
	public function healthplusData()
	{
		$customer_email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		
		$sql = "SELECT * FROM unilab_health_plus WHERE email ='$customer_email'";
		$row = $this->_getConnection()->fetchRow($sql);
        $this->_getConnection()->commit();
		
		$response['credit_earned'] = $row['credit_earned'];
		$response['credit_use'] = $row['credit_use'];
		$response['credit_balance'] = $row['credit_balance'];
		$response['available_balance'] = $row['available_balance'];
		
		return $response;
		
	}
	


	public function carthealthcredits($grandTotal)
	{
		
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$total = 0;
		foreach ($cart->getAllVisibleItems() as $item) 
		{ 		  
			$prodid = $item->getProduct()->getId();
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		  
		  if ($healthplus == '1') {
			  	$qty = $item->getQty();
				$price = $item->getProduct()->getPrice();
				$totalprice = $price * $qty;
				$total = $total + $totalprice;
		  }

		}
		
		$healthplusData = $this->healthplusData();
		
		if($healthplusData['available_balance'] == $total)
		{
			$discountAmt = $healthplusData['available_balance'];
			$response['success'] = true;

		}elseif($healthplusData['available_balance'] > $total){
			
			$discountAmt = $total;
			$response['success'] = true;
			
		}else{
			$discountAmt = 0;
			$response['message'] = "Your available Health+ points must be equal or greater than accumulated Health+ Item value.";
			$response['success'] = false;
			
		}
		
		$gtotal = $grandTotal - $discountAmt;
		
		if($grandTotal >= $gtotal){
			$grandtotal = $grandTotal;
		}else{
			$grandtotal = $gtotal;   
		}
		
		$response['total'] = $total;
		$response['available_balance'] = $healthplusData['available_balance'];
		$response['gtotal'] = $grandTotal;
		$response['discount'] = $discountAmt; 
		$response['discountAmt'] = Mage::helper('core')->currency($discountAmt,true,false);	
		$response['grandTotal'] = Mage::helper('core')->currency($gtotal,true,false);	   
		

		return $response;
	}	
	
	public function checkcartwithHC()
	{
		$session    = Mage::getSingleton('checkout/session');
		$quote_id 	= $session->getQuoteId(); 

		$sqlselect  = "SELECT * FROM sales_flat_quote WHERE entity_id = '$quote_id'";
		$result   	= $this->_getConnection()->fetchRow($sqlselect);
		$this->_getConnection()->commit();  
		
		return $result['healthcredit'];
	}
	

	//observer
	public function savehealthcredits($observer)
	{
		$storeid 		= 	Mage::app()->getStore()->getStoreId();
		$healthplusEnabled = Mage::getStoreConfig('healthplus/healthplussettings/enabled', $storeid);
		
		$orderModel 	= $observer->getOrder();
		$order_id       = $orderModel->getId();
		$sales_order 	= Mage::getModel('sales/order')->load($order_id);
		
		$healthcredit = $this->checkcartwithHC();
		
		$customer_email = $sales_order->getCustomerEmail();

		if($healthplusEnabled == 1)
		{
			if($healthcredit == 1)
			{
				$discount 	 = Mage::getModel('healthplus/observer')->carthealthcredits();
				$discountAmt = $discount['discountAmt'];
				
				$insert_credit_used = "INSERT INTO unilab_credit_used (order_id, email, points) VALUES('$order_id','$customer_email','$discountAmt')";
				$this->_getConnection()->Query($insert_credit_used);
				$this->_getConnection()->commit();
				
			}else{
				$discountAmt = 0;
			}
				
			$get_points = $this->getPointsbyQuote();


			$insert_credit_earned = "INSERT INTO unilab_credit_earned (order_id, email, points) VALUES('$order_id','$customer_email','$get_points')";
			$this->_getConnection()->Query($insert_credit_earned);
			$this->_getConnection()->commit();

			$check_user =  "SELECT * FROM unilab_health_plus WHERE email = '$customer_email'";
			$result = $this->_getConnection()->fetchRow($check_user);

			$credit_earned 		= $result['credit_earned'] + $get_points;
			$credit_used 		= $result['credit_use'] + $discountAmt;
			$credit_balance 	= ($result['credit_balance'] - $discountAmt) + $get_points;
			$available_balance 	= $result['available_balance']  - $discountAmt;


			if($result)
			{
				$update_health_credit = "UPDATE unilab_health_plus SET credit_earned = '$credit_earned', credit_use = '$credit_used', credit_balance = '$credit_balance', available_balance = '$available_balance' WHERE email = '$customer_email'";
				$this->_getConnection()->Query($update_health_credit);
				$this->_getConnection()->commit();


			}else{

				$insert_health_plus = "INSERT INTO unilab_health_plus (email, credit_earned, credit_use, credit_balance ) VALUES('$customer_email','$credit_earned','$credit_used','$credit_balance')";  
				$this->_getConnection()->Query($insert_health_plus);
				$this->_getConnection()->commit();
			
			}
		}
		
	}

	public function cancelOrder($orderId)
	{
		$sales_order 	= Mage::getModel('sales/order')->load($orderId);
		$customer_email = $sales_order->getCustomerEmail();
		
		$quoteid 		= $sales_order->getQuoteId();
		$get_points 	= $this->getPointsbyOrder($orderId);

		$sql_credit_earned 		= "UPDATE unilab_credit_earned SET points = '0' WHERE order_id ='$orderId'";
		$this->_getConnection()->Query($sql_credit_earned);

		$sql 	= "SELECT *  FROM unilab_health_plus WHERE email ='$customer_email'";
		$result = $this->_getConnection()->fetchRow($sql);

		$credit_earned 		= $result['credit_earned'] - $get_points;
		$credit_balance 		= $result['credit_balance'] - $get_points;

		$sql_update = "UPDATE unilab_health_plus SET credit_earned = '$credit_earned', credit_balance = '$credit_balance' WHERE email = '$customer_email'";
		$this->_getConnection()->Query($sql_update);

	 	$this->_getConnection()->commit();
	 	Mage::log('cancel order- '.$sql_credit_earned,null,'healthplus.log');
	}


	public function checkSuccessPoints($orderId)
	{
		$sales_order = Mage::getModel('sales/order')->load($orderId);
		$sql = "SELECT points FROM unilab_credit_earned WHERE order_id = '$orderId'";
		$result = $this->_getConnection()->fetchRow($sql);
		$this->_getConnection()->commit();
		return $result['points'];
	}


}












