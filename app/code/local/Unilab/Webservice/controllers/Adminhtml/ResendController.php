<?php 
/**********Jillian Carla Guinto************/
class Unilab_Webservice_Adminhtml_ResendController extends Mage_Adminhtml_Controller_Action
{ 	

	public function IndexAction()
	{

		$response = Mage::getModel('webservice/postdata_salesorder')->sendorder($_POST['increment_id']);					
		
		if($response['success'] == true){			

			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Your Order was Successfully Sent'));						

		}else{			

			if(empty($response['message'])){

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__("Error while sending your Order"));			

			}else{

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__($response['message']));	
			}					
		}	

		$this->_redirect('adminhtml/sales_order/view', array('order_id'=>$_POST['order_id'],'key'=>$this->getRequest()->getParam('key')));	

	}


	public function sendorderAction()
	{	
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$order_id	= $this->getRequest()->getParam('order_id');

		$response = Mage::getModel('webservice/netsuite_postdata_order')->createOrder($order_id,$StoreId);	

			if($response['success']  == 1)
			{
				$msg = "Order was successfully sent";

			}else{
				
				$msg = $response['message'] ;
			}

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('webservice')->__($msg));		

		$this->_redirect('adminhtml/sales_order/index');

	}

	public function sendcustomerstoNetsuiteAction()
	{
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$id 		  = $this->getRequest()->getParam('id');  
		$read         = Mage::getSingleton('core/resource')->getConnection('core_read'); 
    	$addressCount = $read->fetchAll("SELECT count(*) FROM customer_address_entity WHERE parent_id = $id"); 

    	if($addressCount > 0)
    	{
    		$response = Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomer($id, $StoreId);   

    	}else
    	{
    		$response = Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomerWithoutAddress($id, $StoreId);
       	}

		if($response['success']  == 1)
		{
			$msg = "Customer was successfully sent";

		}else{
			$msg = $response['message'] ;
		}

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('webservice')->__($msg));	

		$this->_redirect('adminhtml/customer/index');		

	}

}