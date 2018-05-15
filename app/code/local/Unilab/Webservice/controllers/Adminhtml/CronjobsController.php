<?php 


class Unilab_Webservice_Adminhtml_CronJobsController extends Mage_Core_Controller_Front_Action
{ 	


	public function sendCustomersCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->sendCustomer();    
	
	}
	
	public function sendUpdateCustomersCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->updateCustomer();    
	
	}


	public function sendAddAddressCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->addAddress();    
	
	}
	public function sendUpdateAddressCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->updateAddress();    
	
	}

	public function sendOrderCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->sendOrder();    
	
	}
	public function sendCancelOrderCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->CancelOrder();    
	
	}
	public function sendProductCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->sendProduct();    
		

	}

	public function runCronJobAction()
	{
		
		$response = Mage::getModel('webservice/netsuite_postdata_cronjobs')->runcronjobs();    
	
	}
	
}