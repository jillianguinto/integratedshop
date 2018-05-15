<?php 
/**
	-> Login or Logout Web Service
	
	-> Author 	: Richel R. Amante
	
	-> Email	: richelramante@gmail.compile
	
	-> Date		: August 20, 2015
	
	-> Filename	: LoginController.php	
	
**/

class RRA_Adminuser_LoginController extends Mage_Core_Controller_Front_Action
{ 

	public function indexAction()
	{
		
		$user_name		= $this->getRequest()->getParam('username');
		$passwordPost	= $this->getRequest()->getParam('password');

		$resource 		= Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$tableName 		= $resource->getTableName('admin_user');

		$password		= "SELECT password FROM " .$resource->getTableName('admin_user')." WHERE username= '".$user_name."'";
		$results_pass 	= $readConnection->fetchOne($password);
		$passmatch		= Mage::helper('core')->validateHash($passwordPost,$results_pass);	
		
		if($passmatch == true)
		{
			$response['success'] 		= true;
			$response['errorhandler'] 	= "";
			
		}else{
			
			$response['success'] 		= false;
			$response['errorhandler'] 	= "Invalid Username or password";
			
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
		
	}
	
}
