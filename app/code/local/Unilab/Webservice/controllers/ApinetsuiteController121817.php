<?php 
class Unilab_Webservice_ApinetsuiteController extends Mage_Core_Controller_Front_Action
{ 
	protected function _construct()
	{	
	
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: origin, x-requested-with, content-type");
		header("Access-Control-Allow-Methods: PUT, GET, POST");
		
		// //"PUT, GET, POST, DELETE, OPTIONS"	
		date_default_timezone_set('Asia/Taipei');
		
		// Mage::getModel('webservice/validate_token')->validatetoken();	
		
		//$this->myindexAction();
				
	}

	public function indexAction()   
	{
		
		try{
			
			$date           = date("Y-m-d");
			Mage::log($_POST, null, $_POST['cmdEvent'] . '_'. $date .'.log');

			if($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::PROCESS_ORDER)
			{
				$response = Mage::getModel('webservice/netsuite_orderstatus')->updateToProcessing();
			}
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::COMPLETE_ORDER)
			{
				$response = Mage::getModel('webservice/netsuite_orderstatus')->updateToComplete();
			}
			
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::CREATE_PRODUCT)
			{
				$response = Mage::getModel('webservice/netsuite_products')->create();
			}
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::UPDATE_PRODUCT)
			{
				$response = Mage::getModel('webservice/netsuite_products')->update();
			}
			//product price
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::UPDATE_PRODUCT_PRICE )
			{
				$response = Mage::getModel('webservice/netsuite_products')->updateproductprice();
			}
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::UPDATE_PRODUCT_STATUS )
			{
				$response = Mage::getModel('webservice/netsuite_products')->updateproductstatus();
			}
			
			
			//category
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::CATEGORY)
			{
				$response = Mage::getModel('webservice/netsuite_category')->create();
			}
			
			//type
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::TYPE)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createType();
			}
			
			//format
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::FORMAT)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createFormat();
			}

			//benefit
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::BENEFIT)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createBenefit();
			}
			
			//segment
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::SEGMENT)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createSegment();
			}
			
			//division
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::DIVISION)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createDivision();
			}
			
			//group
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::GROUP)
			{
				$response = Mage::getModel('webservice/netsuite_maintenance')->createGroup();
			}
			
			//test
			elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::GET_PRODUCT)
			{
				$response = Mage::getModel('webservice/netsuite_products')->getProduct();
			}
			

			else
			{
				
				$response['message'] 	= "Function ". $_POST['cmdEvent'] ." Not Exist!";
				$response['success']	= false;
			}
			
		}catch(Exception $e){
			
			Mage::log($e->getMessage(), null, "webservice_error_" . $date . ".log");
			
			$response['message'] = $e->getMessage();
			$response['success'] = false;
		}
		
		
		Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');
		
		//Mage::getModel('ulahwebservice/validate_token')->saveResponse($response);	
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 


		
	}
	
	
	
}
