<?php

require_once('Mage/Checkout/controllers/CartController.php');

class Unilab_Checkout_CartController 
	extends Mage_Checkout_CartController
{
	protected function _getModel()
	{
		return Mage::getModel('prescription/prescription');
	}
	
	protected function _initPrescriptions()
	{  
        $files  			 = $_FILES;		
		$prescription  		 = false;	 
		$prescription_params = $this->getRequest()->getParam('prescription');		 
		$prescription_type   = $prescription_params['type']; 	 
		   
		try{
			if(count($prescription_params) > 0){
				$prescription_model = $this->_getModel();
				 
				if(isset($prescription_params['prescription_id']) && !empty($prescription_params['prescription_id'])){
					$prescription_model =	$prescription_model->load($prescription_params['prescription_id']);
				}
				
				switch($prescription_type)
				{			
					case Unilab_Prescription_Model_Prescription::TYPE_NEW :		
							if(!$prescription_model->getId()){
								unset($prescription_params['prescription_id']);
							}
							$prescription_params['scanned_rx'] = "";
							$prescription = $prescription_model->setData($prescription_params)
															   ->save(); 
						break;
					case Unilab_Prescription_Model_Prescription::TYPE_PHOTO :	
							try {	
								 
								$scanned_rx_files 	 = array();
								$original_filenames	 = array();
								$path 	 			 = Mage::getBaseDir('media') . DS . 'prescriptions' . DS;								
								$temp_path 			 = Mage::getBaseDir('media') . DS . 'tmp' .DS . 'prescriptions' . DS; 
								
								if(isset($prescription_params['scanned_rxs']) && count($prescription_params['scanned_rxs']) > 0){										
									foreach($prescription_params['scanned_rxs'] as $orig_filename => $rx_name){
										if(file_exists($temp_path . $rx_name)){ 
											rename($temp_path.$rx_name,$path.$rx_name);
											$scanned_rx_files[] = $rx_name; 
										}elseif(file_exists($path . $rx_name)){ 
											$scanned_rx_files[] = $rx_name; 
										}
										$original_filenames[]   = $orig_filename;
									}
								}

								if(count($scanned_rx_files) > 0 ) {
									$prescription = $prescription_model->setData('scanned_rx',implode(",",$scanned_rx_files))
																	   ->setData('original_filename',implode(",",$original_filenames))
																	   ->setDatePrescribed($prescription_params['date_prescribed_2'])
																	   ->save(); 
									//update filenames
									$scanned_prescription = explode(",",$prescription->getScannedRx());
									$new_rx_formatted     = array();
									foreach($scanned_prescription as $scanned_rx){
										$extension     = str_replace('.', '', strrchr($scanned_rx, '.')); 	
										$new_file_name = $prescription->getId().'-'.md5(strtoupper($scanned_rx)).'.'.$extension;
										rename($path.$scanned_rx,$path.$new_file_name);
										$new_rx_formatted[] = $new_file_name;
									}
									$prescription->setScannedRx(implode(",",$new_rx_formatted))->save(); 
								} 
									
							} catch (Exception $e) { die($e->getMessage());
								throw new Mage_Core_Exception($e->getMessage());
							} 					
						break;
					case Unilab_Prescription_Model_Prescription::TYPE_EXISTING:
							if(!isset($prescription_params['prescription_id'])){
								throw new Exception("No Prescription Selected");
							}
							$prescription = Mage::getModel("prescription/prescription")->load($prescription_params['prescription_id']);							
						break;
					case Unilab_Prescription_Model_Prescription::TYPE_NONE: 
							$prescription = Unilab_Prescription_Model_Prescription::TYPE_NONE;			
						break;
					default: 
							$prescription = false;
						break;
				}
			}			
		}catch(Exception $e){ 
			throw new Mage_Core_Exception($e->getMessage());
		}
		
		return $prescription;		
	}
	
	protected function forceFormatRxFiles($files)
	{		 
		$formatted_files = array();
		
		if(count($_FILES['prescription']['name']['scanned_rx']) > 0){ 
			foreach($_FILES['prescription']['name']['scanned_rx'] as $rx_code=>$rx_file_name){
					$formatted_files[$rx_code]['name'] 		= $rx_file_name;
					$formatted_files[$rx_code]['type'] 		= $_FILES['prescription']['type']['scanned_rx'][$rx_code];
					$formatted_files[$rx_code]['tmp_name']	= $_FILES['prescription']['tmp_name']['scanned_rx'][$rx_code];
					$formatted_files[$rx_code]['error'] 	= $_FILES['prescription']['error']['scanned_rx'][$rx_code];
					$formatted_files[$rx_code]['size'] 		= $_FILES['prescription']['size']['scanned_rx'][$rx_code]; 
			} 
			unset($_FILES['prescription']); 	
		} 	

		return 	$formatted_files;
	}
	
	public function addAction()
    {  
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();  
		 
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            } 
		
			
			// Get The simple product
			if(isset($params['cpid']) && $params['product'] == $params['cpid'] && isset($params['actual_product']) && $params['actual_product'] != $params['product']){
				$product = Mage::getModel('catalog/product')
	                ->setStoreId(Mage::app()->getStore()->getId())
	                ->load($params['actual_product']);
			}else{
				 $product 		= $this->_initProduct();
			}
			
           
            $related	 	= $this->getRequest()->getParam('related_product');			
			
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

			

            $cart->addProduct($product, $params);
			
			/**** Prescription ****/
			if(($prescription  = $this->_initPrescriptions()) && $prescription instanceof Unilab_Prescription_Model_Prescription){ 
				$item = $cart->getQuote()->getItemByProduct($product);
				$item->setPrescriptionId($prescription->getId());
			}else{  
				if($prescription == Unilab_Prescription_Model_Prescription::TYPE_NONE){
					$cart->removeItem($item_id);
					$item->setPrescriptionId('NULL');
				}
			}				 		
			
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
		die($e->getMessage());
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
	
	protected function updateprescriptionAction()
    {
        try{ 
			
				$product 		= $this->_initProduct();
                $cart  			= $this->_getCart(); 
				
				$item_id 		= (int) $this->getRequest()->getParam('item_id');
				$item 			= $cart->getQuote()->getItemById($item_id);  
				 
				/**** Prescription ****/
				if(($prescription = $this->_initPrescriptions()) && $prescription instanceof Unilab_Prescription_Model_Prescription){ 
					$item->setPrescriptionId($prescription->getId());
				}else{  
					if($prescription == Unilab_Prescription_Model_Prescription::TYPE_NONE){
						$cart->removeItem($item_id); 
						$message = $this->__('Prescription is required for %s.', Mage::helper('core')->escapeHtml($product->getName())); 
					}
				} 
				
				$cart->save();
				
				if(!isset($message)){
					$message = $this->__('Prescription is updated for %s.', Mage::helper('core')->escapeHtml($product->getName()));
				}
				
				$this->_getSession()->addSuccess($message);
				$this->_getSession()->setCartWasUpdated(true);
                $this->_goBack(); 
				
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }


    /**
     * Action to reconfigure cart item
     */
    public function configureAction()
    {
        // Extract item and product to configure
        $id = (int) $this->getRequest()->getParam('id');
        $quoteItem = null;
        $cart = $this->_getCart();
        if ($id) {
            $quoteItem = $cart->getQuote()->getItemById($id);
        }

        if (!$quoteItem) {
            $this->_getSession()->addError($this->__('Quote item is not found.'));
            $this->_redirect('checkout/cart');
            return;
        }

        try {
            $params = new Varien_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $params->setBuyRequest($quoteItem->getBuyRequest());

            Mage::helper('catalog/product_view')->prepareAndRender($quoteItem->getProduct()->getId(), $this, $params);
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot configure product.'));
            Mage::logException($e);
            $this->_goBack();
            return;
        }
    }
	
	public function updateItemOptionsAction()
    {
    	
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams(); 
		
		if (!isset($params['options'])){
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            } 
						
			
			//set prescription ID
			$prescription_id =  $quoteItem->getPrescriptionId();  
            $item = $cart->updateItem($id, new Varien_Object($params));
			$item->setPrescriptionId($prescription_id);
			
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);
            $this->_goBack();
        }
        $this->_redirect('*/*');
    }
}

