<?php
class Unilab_Prescription_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {        
		$this->loadLayout();
		$this->renderLayout();
    } 
	
	public function askuserAction()
    {   
		$product   = Mage::getModel('catalog/product')->load($this->getRequest()->getPost('product')); 
		
		if (! Mage::helper('customer')->isLoggedIn()) {
			 $current_session = Mage::getSingleton('customer/session');
			 if(isset($current_session)) $current_session->setData("before_auth_url", $product->getProductUrl()); 
		} 

        if(!$this->getRequest()->isXmlHttpRequest()){
          $this->_redirect('*/*/');
        }  		
		 
		$response  = array('proceed_to_cart' => false); 				
		
		$this->loadLayout(); 
		
		/** Check if product exists and (product already in cart or product requires no prescription) **/
		if($product->getId() && $this->_getHelper()->isProductInCart($product)){			
			$response['proceed_to_cart'] = true; 			
		}
		else{		
			$askuser_dialog_block		    = $this->getLayout()->getBlock('prescription.cart.askuserdialog');
			$cancel_dialog_block		    = $this->getLayout()->getBlock('prescription.cart.cancel');
			$cancel_trans_dialog_block		= $this->getLayout()->getBlock('prescription.cart.cancel.transaction');
			$prescription_dialog_block	    = $this->getLayout()->getBlock('prescription.cart.prescriptiondialog')
																->setProductToCartFormFields($this->getRequest()->getPost());
																
			$response['askuser_dialog'] 	 = $askuser_dialog_block->toHtml();
			$response['prescription_dialog'] = $prescription_dialog_block->toHtml();	
			$response['cancel_dialog']	     = $cancel_dialog_block->toHtml();	
			$response['cancel_trans_dialog'] = $cancel_trans_dialog_block->toHtml(); 	
	
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
	
	public function editPrescriptionAction()
	{
		if(!$this->getRequest()->isXmlHttpRequest())
		{
            return;
        }  		  
		
		$item   	  = Mage::getModel('sales/quote_item')->load($this->getRequest()->getParam('item_id')); 
		$prescription =  Mage::getModel('prescription/prescription')->load($item->getPrescriptionId());
		
		$params = $this->getRequest()->getPost();
		$params = array_merge($params,array('form_action'=> Mage::getUrl('checkout/cart/updateprescription',array('product'=>$item->getProductId())),
											'product'	 => $item->getProductId()));
		$this->loadLayout(); 
		 	 
		$prescription_dialog_block	    = $this->getLayout()->getBlock('prescription.cart.prescriptiondialog')
															->setProductToCartFormFields($params)
															->setPrescription($prescription);
		
		$confirm_dialog_block	    = $this->getLayout()->getBlock('prescription.cart.confirm');	
		$cancel_trans_dialog_block	= $this->getLayout()->getBlock('prescription.cart.cancel.transaction');
		$calendar_block		  	    = $this->getLayout()->getBlock('html_calendar');	
		
		$response['prescription_dialog'] = $prescription_dialog_block->toHtml();	
		$response['confirm_dialog']		 = $confirm_dialog_block->toHtml();	 
		$response['cancel_trans_dialog'] = $cancel_trans_dialog_block->toHtml();	
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
	}
	
	public function presscriptionDialogAction()
	{
		if(!$this->getRequest()->isXmlHttpRequest()) {
            return;
        }  
		
		$ask_user_block			    = $this->getLayout()->getBlock('prescription.cart.askuser');
		$response['askuser_dialog'] = $ask_user_block->toHtml();
	
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	
	protected function _getHelper()
	{
		return Mage::helper('prescription');
	}
	
	public function presavescannedrxAction()
	{    
		//file_put_contents('./PRESCRIPTION_UPLOAD.log',print_r($_FILES,1).PHP_EOL, FILE_APPEND);	 
		  
		 try{  
			 $response			  = array('success'=> false); 
			 $post_parameters     = $this->getRequest()->getPost('prescription');
			 $current_rx_selected = 'prescription_scanned_rx';
			 if(empty($_FILES['prescription']['size']['scanned_rx'][$current_rx_selected])){
				throw new Exception(Mage::helper("prescription")->__("Error on file upload."));
			 }
			 if(isset($_FILES['prescription']['name']['scanned_rx'][$current_rx_selected]) && 
					  $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected] != ""){	 
					
							$path = Mage::getBaseDir('media') . DS . 'tmp' .DS . 'prescriptions' . DS; 
						
							$new_file_values = array();
							$new_file_values['prescription']['name']['scanned_rx']        = $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['type']['scanned_rx']        = $_FILES['prescription']['type']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['tmp_name']['scanned_rx']    = $_FILES['prescription']['tmp_name']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['error']['scanned_rx']       = $_FILES['prescription']['error']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['size']['scanned_rx']        = $_FILES['prescription']['size']['scanned_rx'][$current_rx_selected];
							
							//VALIDATE FIRST FILESIZE
							$image_size = ($new_file_values['prescription']['size']['scanned_rx']/1024)/1024;
							
							if(!in_array($new_file_values['prescription']['type']['scanned_rx'],array('image/jpeg', 'image/jpg','image/pjpeg','image/png','image/gif','image/x-png'))){
								$response['error']   = 'TYPE';  
							}elseif($image_size > Unilab_Prescription_Model_Prescription::DEFAULT_RX_IMG_SIZE || $image_size == 0){ 
								$response['error'] = 'SIZE'; 
							}
							
							if(!isset($response['error'])){ 
								$_FILES   = $new_file_values; 
								$uploader = new Varien_File_Uploader('prescription[scanned_rx]');
								$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
								$uploader->setAllowRenameFiles(false);
								$uploader->setFilesDispersion(false);
								
								$extension = $uploader->getFileExtension(); 
								$file_name  = md5(date("YMDhis").$_FILES['prescription']['name']['scanned_rx']);									
								$scanned_rx = $uploader->save($path, $file_name.'.'.$extension); 
								
								
								//SAVE TO RX TEMP DB ---> Cancelled (No possible quote id on first submission of RXs 
								//$prescription_temp_rxs = Mage::getModel("prescription/prescription_temp_rx");
								//$prescription_temp_rxs->setQuoteId($this->getQuote()->getId())->save(); 
								$burl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
								$response['file_path'] =  $burl."tmp/prescriptions/".$scanned_rx['file'];
								//$response['file_path'] =  Mage::getUrl("media/tmp/prescriptions/").$scanned_rx['file'];
								$response['success']   = true; 		
								$this->loadLayout(); 
								$preuploaded_rx_list  = $this->getLayout()->getBlock('prescription.scanned.new')
																		  ->setImageName($scanned_rx['file'])
																		  ->setImageSource($response['file_path']);
																		  
								$response['image_name'] 		 = $scanned_rx['file'];						
								$response['orig_image_name'] 	 = $new_file_values['prescription']['name']['scanned_rx'];				
								$response['scanned_rx_list'] 	 = $preuploaded_rx_list->toHtml();								
							}
			}	  		
		
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
 
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
	} 
	
	protected function getQuote()
	{
		return Mage::getModel('checkout/cart')->getQuote();
	}
	
	//OLD IMPLEMENTATION FOR MULTIPLE UPLOAD
	public function preSaveScannedRxMultipleAction()
	{   
		//file_put_contents('./PRESCRIPTION_UPLOAD.log',print_r($_POST,1).PHP_EOL, FILE_APPEND);	
		//file_put_contents('./PRESCRIPTION_UPLOAD.log',print_r($_FILES,1).PHP_EOL, FILE_APPEND);	 
		 
		 try{  
			 $response			  = array('success'=> false); 
			 $post_parameters     = $this->getRequest()->getPost('prescription');
			 $current_rx_selected = $post_parameters['current_rx_image_selected'];
		  
			 if(isset($_FILES['prescription']['name']['scanned_rx'][$current_rx_selected]) && 
					  $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected] != ""){	 
					
						$path = Mage::getBaseDir('media') . DS . 'tmp' .DS . 'prescriptions' . DS; 
						
							$new_file_values = array();
							$new_file_values['prescription']['name']['scanned_rx']        = $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['type']['scanned_rx']        = $_FILES['prescription']['type']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['tmp_name']['scanned_rx']    = $_FILES['prescription']['tmp_name']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['error']['scanned_rx']       = $_FILES['prescription']['error']['scanned_rx'][$current_rx_selected];
							$new_file_values['prescription']['size']['scanned_rx']        = $_FILES['prescription']['size']['scanned_rx'][$current_rx_selected];
							
							//VALIDATE FIRST FILESIZE
							$image_size = ($new_file_values['prescription']['size']['scanned_rx']/1024)/1024;
							
							if(!in_array($new_file_values['prescription']['type']['scanned_rx'],array('image/jpeg', 'image/jpg','image/pjpeg','image/png','image/gif'))){
								$response['error']   = 'TYPE';  
							}elseif($image_size > Unilab_Prescription_Model_Prescription::DEFAULT_RX_IMG_SIZE || $image_size == 0){ 
								$response['error'] = 'SIZE'; 
							}
							
							if(!isset($response['error'])){ 
								$_FILES   = $new_file_values; 
								$uploader = new Varien_File_Uploader('prescription[scanned_rx]');
								$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
								$uploader->setAllowRenameFiles(false);
								$uploader->setFilesDispersion(false);
								
								$extension = $uploader->getFileExtension(); 
								$file_name  = md5(date("YMDhis").$_FILES['prescription']['name']['scanned_rx']);									
								$scanned_rx = $uploader->save($path, $file_name.'.'.$extension); 
								$burl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
								$response['file_path'] =  $burl."tmp/prescriptions/".$scanned_rx['file'];
								//$response['file_path'] =  Mage::getUrl("media/tmp/prescriptions/").$scanned_rx['file'];
								$response['success']   = true; 
								
							}
			}	  		
		
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	} 
}