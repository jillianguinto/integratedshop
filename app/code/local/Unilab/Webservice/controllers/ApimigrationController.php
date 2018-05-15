<?php          
                              
class Unilab_Webservice_ApimigrationController extends Mage_Core_Controller_Front_Action  
{ 

    protected function _construct()
    {   
    
        header("Access-Control-Allow-Origin: *");
        
        header("Access-Control-Allow-Headers: origin, x-requested-with, content-type");
        
        header("Access-Control-Allow-Methods: PUT, GET, POST");

        
	}
	public function indexAction() 
    {  
                    
        $date = date("Y-m-d");

        Mage::log($_POST, null, $_POST['cmdEvent'] . '_POST_'. $date .'.log'); 
		
		//$tokendata = Mage::getModel('webservice/validate_token')->validatetoken(); 
		
		try{
			
			if($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::MIGRATION){   

               $response = Mage::getModel('webservice/function_migration')->AddCustomer(); 

			}else{   
				
				$response['message']    = "Function ". $_POST['cmdEvent'] ." Not Exist!";
                $response['success']    = 0;
            }
			
		}catch(Exception $e){
            
            Mage::log($e->getMessage(), null, "webservice_error_" . $date . ".log");
            
            $response['message'] = $e->getMessage();
            $response['success'] = 0;

        }
		
		Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
		
	}
	
}