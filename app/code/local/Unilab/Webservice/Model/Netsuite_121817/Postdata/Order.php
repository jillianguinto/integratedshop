<?php
date_default_timezone_set('Asia/Taipei');
class Unilab_Webservice_Model_Netsuite_Postdata_Order extends Mage_Core_Model_Abstract
{	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	 
		
        return $this->_Connection;
    }	
	
    
    public function netsuitesettings($storeid)
	{
		$settings 				= array();
		$settings['gateway'] 	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuitereggateway', $storeid);
		$settings['token']		= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregtoken', $storeid);
		$settings['sitehost']	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteurl', $storeid);
		$arr 					= explode('/', Mage:: getBaseUrl());
		$settings['hostname']	= $arr[2];
		
		return $settings;
	}
	
	public function createOrder($orderId,$storeid)
	{	  
 
		$currentdate            = date("Y-m-d H:i:s");   
	
		$netsettings            = $this->netsuitesettings($storeid);
			
		$postdata 		            = array();
		$postdata['gateway'] 	    = $netsettings['gateway'];
		$postdata['token'] 	        = $netsettings['token'];
		$postdata['sitehost'] 	    = $netsettings['sitehost'];
		$postdata['cmdEvent'] 	    = 'CreateOrder';
		$postdata['hostname']	    = $netsettings['hostname'];
		$orders			            = $this->getOrderinfo($orderId );
		$postdata['orders']	        = $orders;
		$postdata['orderitems']     = $this->getOrderitems($orderId);
		
		Mage::getModel('webservice/validate_token')->saveapidatalogs($postdata);
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
		$client  = new Zend_Http_Client($postdata['gateway']); 
		$client->setAdapter($adapter);
		$client->setParameterPost('sitehost', $postdata['gateway']); 
		$client->setParameterPost('token', $postdata['token']); 
		$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
		$client->setParameterPost('hostname', $postdata['hostname']); 
		
		$client->setParameterPost('email', $orders['email']); 
		$client->setParameterPost('orderno', $orders['orderno']);
		$client->setParameterPost('orderid', $orders['orderid']);
		$client->setParameterPost('orderstatus', $orders['orderstatus']);
		$client->setParameterPost('name', $orders['name']);
		$client->setParameterPost('discprcnt', $orders['discprcnt']);
		$client->setParameterPost('discsum', $orders['discsum']);
		$client->setParameterPost('disccode', $orders['disccode']);
		$client->setParameterPost('trantotal', $orders['trantotal']);
		$client->setParameterPost('basesubtotal', $orders['basesubtotal']);
		$client->setParameterPost('trandate', $orders['trandate']);
		$client->setParameterPost('shippingfee', $orders['shippingfee']);
		$client->setParameterPost('shippingmethod', $orders['shipping_method']);
		$client->setParameterPost('refnumber', $orders['refnumber']);
		$client->setParameterPost('refcreated', $orders['refcreated']);
		$client->setParameterPost('paymentmethod', $orders['paymentmethod']);
		$client->setParameterPost('reference_id', $orders['reference_id']);
		$client->setParameterPost('shipto', $orders['shipto']);
		$client->setParameterPost('billto', $orders['billto']);
		$client->setParameterPost('waybillno', $orders['waybillno']);
		$client->setParameterPost('storeid', $orders['storeid']);
		$client->setParameterPost('orderitems', $postdata['orderitems']);
		//$client->setParameterPost('orderstatus', $orders['orderstatus']);
		$client->setParameterPost('customer_id', $orders['customerid']); 
		$client->setParameterPost('customeraddressid', $orders['customeraddressid']);
		
	
	
		$storeresponse 	            = $client->request(Zend_Http_Client::POST); 
		$StoreData 	                = json_decode($storeresponse->getbody());
		$successResponse['success'] = $StoreData->success;
		
		
		
		    if($successResponse['success'] == 1){
			
				$query= "UPDATE `sales_flat_order` SET `senttoNS` = 1 ,`senttoNS_date`= '$currentdate',`update_senttoNS` = 1 ,`update_senttoNS_date`= '$currentdate' WHERE `entity_id` = '$orderId'"; 
	        	$this->_getConnection()->query($query);
	        	
				$response['message']		= $StoreData->message;
				$response['customer_id'] 	= $orders['orderno'];
				$response['success']		= true;
			
			}else{
				
				$response['message']		= $StoreData->message;
				$response['customer_id'] 	= $orders['orderno'];
				$response['success']		= false;
			}
		
    		Mage::log($response , true, 'sendOrder_response_'.date("Y-m-d").'.log');
    		Mage::log($postdata, true, 'send_Order_postdata_'.date("Y-m-d").'.log');

		return $response;
	}
	
	public function getOrderinfo($orderId )
	{	$currentdate = date("Y-m-d H:i:s");   
	
	
		$order 			  	             = Mage::getModel('sales/order')->load($orderId);
    
		$customer			 			 = Mage::getModel("customer/customer");
		$customerOrderId				 = $customer->load($order->getCustomerId());			
		$customerShipping    			 = $order->getShippingAddress();
		$customerBilling   				 = $order->getBillingAddress();
	
		$customerOrderAddressShipping 	 = $customerShipping['firstname']." ". $customerShipping['middlename']." ". $customerShipping['lastname']." ". $customerShipping['street']." ".$customerShipping['city']." ".$customerShipping['region']." ".$customerShipping['postcode'];
        $customerOrderAddressBilling	 =  $customerBilling['firstname']." ". $customerBilling['middlename']." ". $customerBilling['lastname']." ".$customerBilling['street']." ".$customerBilling['city']." ".$customerBilling['region']." ".$customerBilling['postcode'];
		$orderentityId					 = $order->getEntityId();
		$customerPaymentInfo			 =  $this->_getConnection()->fetchRow("SELECT * FROM `sales_payment_transaction` WHERE `order_id`= '$orderentityId'"); 
		$paymentId						 = $customerPaymentInfo['payment_id'];
		$productId						 = $order->getEntityId();
		$paymentQry 					 = $this->_getConnection()->fetchRow("SELECT * from `sales_flat_order_payment` where entity_id = '$paymentId'"); 
		$orderstat 						 = $order->getStatus();
		$getorderstatus 				 = $this->_getConnection()->fetchRow("SELECT * from `sales_order_status` where  status = '$orderstat'"); 

		if ( $paymentQry['method'] =="") {
			$paymentmethod = $order->getStatus();
			
		}else{
			$paymentmethod = $paymentQry['method'];
		}
	
		$results['email'] 			    = $order->getCustomerEmail();
		$results['orderid'] 		    = $order->getEntityId();
		$results['orderno'] 		    = $order->getIncrementId();
		//$results['orderstatus']	    = $order->getState();
		$results['name'] 			    = $order->getCustomerFirstname() ." ". $order->getCustomerMiddlename() ." ". $order->getCustomerLastname();
		$results['discprcnt'] 		    = $order->getDiscountPercent() ;
		$results['discsum'] 		    = $order->getDiscountAmount();
		$results['disccode'] 		    = $order->getCouponCode();
		$results['trantotal'] 		    = $order->getGrandTotal();
		$results['basesubtotal'] 	    = $order->getBaseSubtotal();
		$results['shippingfee'] 	    = $order->getShippingAmount();
		$results['refnumber'] 		    = $customerPaymentInfo['txn_id'];
		$results['refcreated'] 		    = $customerPaymentInfo['created_at'];
		$results['customerid'] 		    = $order->getCustomerId();
	    $results['paymentmethod'] 	    = $paymentmethod;
		$results['shipping_method']	    = $order['shipping_method'];
		$results['shipto'] 		        = $customerOrderAddressShipping;
		$results['billto'] 		        = $customerOrderAddressBilling;
		$results['waybillno'] 		    = $order->getUnilabWaybillNumber();
		$results['trandate']		    = $currentdate;
		$results['storeid']		        = $customerOrderId->getStoreId();
		$results['orderstatus']		    = $getorderstatus['label']; 
		$results['websiteId']		    = $customerOrderId->getWebsiteId();
		$results['customeraddressid']	= $customerBilling['customer_address_id'];
	
		return $results;

	}
	
	public function getOrderitems($orderId){
	
		$qry	 = "select * from sales_flat_order_item where order_id= '$orderId'"; 
		$results = $this->_getConnection()->fetchAll($qry);
		$linenum	 = 0;
		$itemData	 = array();
		$itemData2			= array();
		foreach($results as $items){
		
			if($items['prescription_id'] == null ){
			
				$rx = 0 ;	
			}else{
				$rx = 1;
			}
			
		
		$itemData['itemcode'] 		= $items['sku'];
		$itemData['quantity'] 		= $items['qty_ordered'];
		$itemData['rx'] 		    = $rx;
		$itemData['itemdiscprcnt'] 	= $items['discount_percent'];
		$itemData['itemdiscamt'] 	= $items['discount_amount'];
		$itemData['price'] 		    = $items['price'];
		$itemData['linenum'] 		= $linenum;
		
		$linenum++;
		array_push($itemData2,$itemData);
		
		}
		
		return $itemData2;
	
	
	}
	
	public function cancelOrder($orderId,$storeid)
	{	
	
		$currentdate = date("Y-m-d H:i:s");   
		$netsettings = $this->netsuitesettings($storeid);
		
		$postdata 		        = array();
		$postdata['gateway'] 	= $netsettings['gateway'];
		$postdata['token'] 	    = $netsettings['token'];
		$postdata['sitehost'] 	= $netsettings['sitehost'];
		$postdata['cmdEvent'] 	= 'CancelOrder';
		$postdata['hostname']	= $netsettings['hostname'];
		$orders			        = $this->getOrderinfo($orderId);
		$postdata['orders']     = $orders;
		
		Mage::getModel('webservice/validate_token')->saveapidatalogs($postdata);	
	
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
		
		$client = new Zend_Http_Client($postdata['gateway']); 
		$client->setAdapter($adapter);
		$client->setParameterPost('token', $postdata['token']);
		$client->setParameterPost('sitehost', $postdata['gateway']); 
		$client->setParameterPost('token', $postdata['token']); 
		$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
		$client->setParameterPost('hostname', $postdata['hostname']); 
		$client->setParameterPost('storeid', $orders['storeid']);
		$client->setParameterPost('orderno', $orders['orderno']);
		$client->setParameterPost('orderstatus', $orders['orderstatus']);
		$client->setParameterPost('websiteid', $orders['websiteId']); 
		$client->setParameterPost('customer_id', $orders['customerid']); 
		
		$storeresponse 	 = $client->request(Zend_Http_Client::POST); 
		
		$StoreData 	 = json_decode($storeresponse->getbody());
		$successResponse['success']= $StoreData->success;
		
		
		if($successResponse['success'] == 1){
		
    		$query= "UPDATE `sales_flat_order` SET `update_senttoNS` = 1 ,`update_senttoNS_date`= '$currentdate' WHERE `entity_id` = '$orderId'"; 
    		$this->_getConnection()->query($query);
    		
		    $response['message']		= $StoreData->message;
			$response['customer_id'] 	= $orders['orderno'];
			$response['success']		= true;
		}else{
    		$query= "UPDATE `sales_flat_order` SET `update_senttoNS` = '0' ,`update_senttoNS_date`= '$currentdate' WHERE `entity_id` = '$orderId'"; 
    		$this->_getConnection()->query($query);
		
		    $response['message']		= $StoreData->message;
			$response['customer_id'] 	= $orders['orderno'];
			$response['success']		= false;
		
		}
        Mage::log($storeresponse,true,'cancelOrder_response_'.date("Y-m-d").'.log');
		Mage::log($postdata,true,'cancelOrder_postdata_'.date("Y-m-d").'.log');
		
	
		
	}
}