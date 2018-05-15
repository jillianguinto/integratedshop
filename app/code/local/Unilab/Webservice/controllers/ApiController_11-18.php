<?php          
                              
class Unilab_Webservice_ApiController extends Mage_Core_Controller_Front_Action  
{ 

    protected function _construct()
    {   
    
        header("Access-Control-Allow-Origin: *");
        
        header("Access-Control-Allow-Headers: origin, x-requested-with, content-type");
        
        header("Access-Control-Allow-Methods: PUT, GET, POST");

    }
    
    public function indexAction() 
    {  

        Mage::getModel('webservice/validate_token')->validatetoken();   
                    
        try{
              
            $date = date("Y-m-d");
            
            Mage::log($_POST, null, $_POST['cmdEvent'] . '_'. $date .'.log');
      
            if($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::ADD_CUSTOMER){
              
               $response = Mage::getModel('webservice/function_customer')->AddCustomer();                
             
            }elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::UPDATE_CUSTOMER){    

                $response = Mage::getModel('webservice/function_customer')->UpdateCustomer();                      

            }elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::CUSTOMER_LOGIN){ 
                                   
                $response = Mage::getModel('webservice/function_customer')->LoginCustomer();

            }elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::CUSTOMER_LOGOUT){                

                $response = Mage::getModel('webservice/function_customer')->LogoutCustomer();  

            }elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::FORGOT_PASSWORD){   

               $response = Mage::getModel('webservice/function_customer')->setforgotpassword();  
			   
		   }elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::CHANGE_PASSWORD){   

               $response = Mage::getModel('webservice/function_customer')->ChangePassword(); 
			   
			}elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::PRODUCT_VIEW){   

			   $response = Mage::getModel('webservice/function_store')->productview();   

			}elseif($_POST['cmdEvent'] == Unilab_Webservice_Helper_Data::ACCOUNT_VALIDATION){   

               $response = Mage::getModel('webservice/function_validation')->CheckEmail();   

            }elseif($_GET['token'] != ""){   

			   $response = Mage::getModel('webservice/function_store')->redirect();   
             
            }else{
                
               //$response = Mage::getModel('webservice/function_store')->redirect();     
				
				$response['message']    = "Function ". $_POST['cmdEvent'] ." Not Exist!";
                $response['success']    = false;
            }

        }catch(Exception $e){
            
            Mage::log($e->getMessage(), null, "webservice_error_" . $date . ".log");
            
            $response['message'] = $e->getMessage();
            $response['success'] = false;

        }
        
        Mage::getModel('webservice/validate_token')->saveResponse($response);  

        Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');
        
        // $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response['success']));   
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));     
       
    }

    
}
