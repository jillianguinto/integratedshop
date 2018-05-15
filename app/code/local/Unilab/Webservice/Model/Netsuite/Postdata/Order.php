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
	
	public function getOrderinfo($orderId)
	{	
		$currentdate = date("Y-m-d H:i:s");   
	
		$order 			  	             = Mage::getModel('sales/order')->load($orderId);
    
		$customer			 			 = Mage::getModel("customer/customer");
		$customerOrderId				 = $customer->load($order->getCustomerId());			
		$customerShipping    			 = $order->getShippingAddress();
		$customerBilling   				 = $order->getBillingAddress();
	
		$customerOrderAddressShipping 	 = $customerShipping['firstname']." ". $customerShipping['middlename']." ". $customerShipping['lastname']." ". $customerShipping['street']." ".$customerShipping['city']." ".$customerShipping['region']." ".$customerShipping['postcode'];
        $customerOrderAddressBilling	 = $customerBilling['firstname']." ". $customerBilling['middlename']." ". $customerBilling['lastname']." ".$customerBilling['street']." ".$customerBilling['city']." ".$customerBilling['region']." ".$customerBilling['postcode'];
		$orderentityId					 = $order->getEntityId();
		$customerPaymentInfo			 = $this->_getConnection()->fetchRow("SELECT * FROM sales_payment_transaction WHERE order_id= '$orderentityId'"); 
		$paymentId						 = $customerPaymentInfo['payment_id'];
		$productId						 = $order->getEntityId();
		$paymentQry 					 = $this->_getConnection()->fetchRow("SELECT * from sales_flat_order_payment where parent_id = '$orderId'"); 
		$orderstat 						 = $order->getStatus();
		$getorderstatus 				 = $this->_getConnection()->fetchRow("SELECT * from sales_order_status where  status = '$orderstat'"); 


		// if ($paymentQry['method'] =="") {
			// $paymentmethod = $order->getStatus();
			
		// }else{
			// $paymentmethod = $paymentQry['method'];
		// }
	
		$results['email'] 			    = $order->getCustomerEmail();
		$results['orderid'] 		    = $order->getEntityId();
		$results['orderno'] 		    = $order->getIncrementId();
		//$results['orderstatus']	    = $order->getState();
		$results['name'] 			    = $order->getCustomerFirstname() ." ". $order->getCustomerMiddlename() ." ". $order->getCustomerLastname();
		$results['discprcnt'] 		    = $order->getDiscountPercent(); 
		$results['discsum'] 		    = $order->getDiscountAmount();
		$results['disccode'] 		    = $order->getCouponCode();
		$results['trantotal'] 		    = $order->getGrandTotal();
		$results['basesubtotal'] 	    = $order->getBaseSubtotal();
		$results['shippingfee'] 	    = $order->getShippingAmount();
		$results['refnumber'] 		    = $customerPaymentInfo['txn_id'];
		$results['refcreated'] 		    = $customerPaymentInfo['created_at'];
		$results['customerid'] 		    = $order->getCustomerId();
	    $results['paymentmethod'] 	    = $paymentQry['method']; //$paymentmethod;
		$results['shipping_method']	    = $order['shipping_method'];
		$results['shipto'] 		        = $customerOrderAddressShipping;
		$results['billto'] 		        = $customerOrderAddressBilling;
		$results['waybillno'] 		    = $order->getUnilabWaybillNumber();
		$results['trandate']		    = $order->getCreatedAt();
		$results['storeid']		        = $customerOrderId->getStoreId();
		$results['orderstatus']		    = $getorderstatus['label']; 
		$results['websiteId']		    = $customerOrderId->getWebsiteId();
		$results['customeraddressid']	= $customerBilling['customer_address_id'];
		$results['senttoNS'] 	    	= $order['senttoNS'];
		
	
		return $results;

	}
	
	public function getOrderitems($orderId)
	{
	
		$qry	 	= "select * from sales_flat_order_item where order_id= '$orderId'"; 
		$results 	= $this->_getConnection()->fetchAll($qry);
		$linenum	= 0;
		$itemData	= array();
		$itemData2	= array();
		
		foreach($results as $items)
		{
		
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
	
	public function createOrder($orderId,$storeid)
	{	  
		$currentdate            = date("Y-m-d H:i:s");   
	
		$netsettings            = $this->netsuitesettings($storeid);
			
		$postdata 		        = array();
		$postdata['gateway'] 	= $netsettings['gateway'];
		$postdata['token'] 	    = $netsettings['token'];
		$postdata['sitehost'] 	= $netsettings['sitehost'];
		$postdata['cmdEvent']   = 'CreateOrder';
		$postdata['hostname']	= $netsettings['hostname'];
		$orders			        = $this->getOrderinfo($orderId);
		$postdata['orders']	    = $orders;
		$postdata['orderitems'] = $this->getOrderitems($orderId);
		
		// if($orders['senttoNS'] == 1)
		// {
		// 	$response['message']		= "Order number ". $orders['orderno'] . " was already sent.";
		// 	$response['orderno'] 		= $orders['orderno'];
		// 	$response['success']		= false;

		// }else
		// {
		
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
			$client->setParameterPost('customer_id', $orders['customerid']); 
			$client->setParameterPost('customeraddressid', $orders['customeraddressid']);
			
			$_apiResponse = $client->request(Zend_Http_Client::POST); 
 		
			$apiResponse = json_decode($_apiResponse->getbody());
 
				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 

		 		$this->createlogs( $orders['orderno'],"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );	
	 
			if($api_ResponseData['dbm']['success'] == 1 || $api_ResponseData['dbm']['success'] == true ) 
			{
				$fields 						= array();
				$fields['senttoNS']				= $api_ResponseData['dbm']['success'];
				$fields['senttoNS_date']		= $currentdate;
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;
				$fields['sent_dbm_netsuite']	= $api_ResponseData['dbmtonetsuitestatus'][0]->sent;
				

				$where = array($this->_getConnection()->quoteInto('entity_id=?',$orderId));
				$this->_getConnection()->update('sales_flat_order', $fields, $where);
				$this->_getConnection()->commit();	
				if (isset($api_ResponseData['netsuite'])) {
					if (!$api_ResponseData['netsuite']['status']) {
						$api_ResponseData['netsuite']['status'] = 0;
					}
					if(!$api_ResponseData['netsuite']['message']){
						$api_ResponseData['netsuite']['message'] =$api_ResponseData['netsuite']['error']->message;

					}
					$this->createlogs( $orders['orderno'],"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );	
				}
				

			}
				$response['message']			= $api_ResponseData['dbm']['message'];
				$response['orderno'] 			= $orders['orderno'];
				$response['success']			= $api_ResponseData['dbm']['success'];
			
		// }
			
			
		Mage::log($postdata, true, $postdata['cmdEvent'].'_postdata_'.date('M-d-Y').'.log');
		Mage::log($response, true, $postdata['cmdEvent'].'_response_'.date('M-d-Y').'.log');

		 // Mage::getModel('webservice/netsuite_token')->savepostlogs($postdata,$storeid,$response);
		 
		return $response;
	}
	
	
	
	public function cancelOrder($orderId,$storeid)
	{	
		$currentdate			= date("Y-m-d H:i:s");   
		$netsettings 			= $this->netsuitesettings($storeid);
		
		$postdata 		        = array();
		$postdata['gateway'] 	= $netsettings['gateway'];
		$postdata['token'] 	    = $netsettings['token'];
		$postdata['sitehost'] 	= $netsettings['sitehost'];
		$postdata['cmdEvent'] 	= 'CancelOrder';
		$postdata['hostname']	= $netsettings['hostname'];
		$orders			        = $this->getOrderinfo($orderId);
		$postdata['orders']     = $orders;
	
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
		$client->setParameterPost('customeraddressid', $orders['customeraddressid']);

	
		$_apiResponse = $client->request(Zend_Http_Client::POST); 
		
		$apiResponse = json_decode($_apiResponse->getbody());
	 

		foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
		
		if($api_ResponseData['netsuiteCreate']){
					if (!$api_ResponseData['netsuiteCreate']['status']) {
						$api_ResponseData['netsuiteCreate']['status'] = 0;
					}
					if(!$api_ResponseData['netsuiteCreate']['message']){
						$api_ResponseData['netsuiteCreate']['message'] =$api_ResponseData['netsuiteCreate']['error']->message;

					}

				$this->createlogs( $orders['orderno'],"Netsuite",'CreateOrder',$api_ResponseData['netsuiteCreate']['status'],json_encode($api_ResponseData['netsuiteCreate']['message']." ".json_encode($api_ResponseData['netsuiteCreate']['data'])) );
			}
			
		$this->createlogs( $orders['orderno'],"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );		
		
		if($api_ResponseData['dbm']['success'] == 1)
		{
			$fields 						= array();
			$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
			$fields['update_senttoNS_date']	= $currentdate;

			$where = array($this->_getConnection()->quoteInto('entity_id=?',$orderId));
			$this->_getConnection()->update('sales_flat_order', $fields, $where);
			$this->_getConnection()->commit();	

					if (!$api_ResponseData['netsuite']['status']) {
						$api_ResponseData['netsuite']['status'] = 0;
					}
					if(!$api_ResponseData['netsuite']['message']){
						$api_ResponseData['netsuite']['message'] =$api_ResponseData['netsuite']['error']->message;

					}
					
			$this->createlogs( $orders['orderno'],"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );
		}else{
			$fields 						= array();
			$fields['update_senttoNS']		= 0;
			$fields['update_senttoNS_date']	= $currentdate;

			$where = array($this->_getConnection()->quoteInto('entity_id=?',$orderId));
			$this->_getConnection()->update('sales_flat_order', $fields, $where);
			$this->_getConnection()->commit();
		}
			$response['message']		= $api_ResponseData['dbm']['message'];
			$response['orderno'] 		= $orders['orderno'];
			$response['success']		= $api_ResponseData['dbm']['success'];


		Mage::log($postdata, true, $postdata['cmdEvent'].'_postdata_'.date('M-d-Y').'.log');
		Mage::log($response, true, $postdata['cmdEvent'].'_response_'.date('M-d-Y').'.log');


		// Mage::getModel('webservice/netsuite_token')->savepostlogs($postdata,$orders['storeid'],$response);
		
		return $response;
		
	}
	
	public function createlogs($id,$from,$cmdEvent,$status,$message){
		$currentdate = date("Y-m-d H:i:s");   

		if ($status == null || $status == '') {
			$status = 0 ;
		}

		$data = array(
					'transaction_id' 	=>  $id,
					'cmdEvent'			=>	$cmdEvent,
					'data_from'			=>	$from,
					'status' 			=>  $status,
					'logs'				=>  $message,
					'date'				=>	$currentdate,
					'type'				=> 'send'
					 );

 		$this->_getConnection()->insert('unilab_logs_netsuite', $data);
	}

}