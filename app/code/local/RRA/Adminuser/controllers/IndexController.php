<?php 

/*
 * @author: Richel.R.Amante
 * @email: richelramante@gmail.com
 * @date: 2015-10-23
 */
 		
 
class RRA_Adminuser_IndexController extends Mage_Core_Controller_Front_Action { 


	public function IndexAction(){
		
		
		try{
					
			if($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::ADD_PRODUCT){
						
				$response = Mage::getModel('adminuser/products_create')->create();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::DELETE_PRODUCT){
				
				$response = Mage::getModel('adminuser/products_delete')->delete();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::UPDATE_PRODUCT){
				
				$response = Mage::getModel('adminuser/products_update')->update();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::ADD_CATALOG_RULE){
				
				$response = Mage::getModel('adminuser/rule_catalog_create')->create();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::DELETE_CATALOG_RULE){	
			
				$response = Mage::getModel('adminuser/rule_catalog_delete')->delete();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::UPDATE_CATALOG_RULE){	
			
				$response = Mage::getModel('adminuser/rule_catalog_update')->update();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::ADD_SHOPPING_RULE){
				
				$response = Mage::getModel('adminuser/rule_shoppingcart_create')->create();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::DELETE_SHOPPING_RULE){	
			
				$response = Mage::getModel('adminuser/rule_shoppingcart_delete')->delete();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::UPDATE_SHOPPING_RULE){	
			
				$response = Mage::getModel('adminuser/rule_shoppingcart_update')->update();
				
			}elseif($_POST['cmdEvent'] == RRA_Adminuser_Helper_Data::ADD_CUSTOMER){	
			
				$response = Mage::getModel('adminuser/customer_adduser')->create();
				
			}else{
				
				$response['success']	= false;
				
				$response['ErrHndlr']	= "Please Double Check your command event.";			
			}
			
		}catch(Exception $e) {
			
			$response['success']	= false;
			$response['ErrHndlr']	= $e->getMessage();
			
		}
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 	
				
	}
		

}
