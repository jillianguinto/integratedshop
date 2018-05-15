<?php

class Unilab_Prescription_Adminhtml_PrescriptionController extends Mage_Adminhtml_Controller_action
{  

	 /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    } 
	
	/**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
	
	/**
     * Initialize item model instance
     *
     * @return Mage_Sales_Model_Order_Item || false
     */
    protected function _initItemPrescription()
    {
        $id	    = $this->getRequest()->getParam('item_id');
        $item   = Mage::getModel('sales/order_item')->load($id);

        if (!$item->getId()){
            $this->_getSession()->addError($this->__('This Item no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        } 
		
		Mage::register('sales_order_item', $item); 
		$prescription = Mage::getModel('prescription/prescription')->load($item->getPrescriptionId());
		 
		if(!$prescription->getId()){ 
			$this->_getSession()->addError($this->__('This Item has no prescription.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;		
		} 
	
		Mage::register('sales_order_item_prescription', $prescription); 
        return $item;
    }
	
	public function orderAction(){
		
		$this->_initOrder(); 
		
		$this->loadLayout();
		$this->renderLayout();
		
	}   
	
	public function viewAction(){
		
		$this->_initItemPrescription();  
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->renderLayout();
		
	}   
	

	protected function _initModel()
	{
		$model = Mage::getModel('prescription/prescription')
						->setStoreId($this->getRequest()->getParam('store', 0))
						->load($this->getRequest()->getParam('id'));

		$model->setData('_edit_mode', true);

		Mage::register('prescription_prescription', $model);
		Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));

		return $model;
	}

	protected function _initModelSave()
	{
		$model = null;
		if ($model 	    = $this->_initModel()) { 
			$modelData  = $this->getRequest()->getPost('prescription');		 
			$storeId    = (int) $this->getRequest()->getParam('store');
			$folderName = 'prescription'; 
			 
			if(isset($modelData['consumed'])){ 
				$modelData['consumed'] = 1;
			}else{
				$modelData['consumed'] = 0;
			}
			
			if(isset($_FILES['scanned_rx']['name']) && $_FILES['scanned_rx']['name'] != '') {
				try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('scanned_rx');

					// Any extention would work
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false); 
					$uploader->setFilesDispersion(false);

					// We set media as the upload dir
					$baseMediaPath = Mage::getBaseDir('media') . DS .$folderName;

					if(!is_dir($baseMediaPath)){
						@mkdir($baseMediaPath,777);
					}
					// We set media as the upload dir
                    $extension  = substr($_FILES['scanned_rx']['name'], strrpos($_FILES['scanned_rx']['name'], "."));
                    $uniqueName = md5($_FILES['scanned_rx']['name'].strtolower($model->getId()))."{$extension}";
                    $path       = Mage::getBaseDir('media') . DS . $folderName . DS;
                    $uploader->save($path, $uniqueName );
					
				} catch (Exception $e) {
					die($e->getMessage());
				} 
                //this way the name is saved in DB
                $modelData['scanned_rx'] = $folderName. '/' .$uniqueName;
			} else {
				// delete scanned_rx
				if(isset($modelData['scanned_rx'])){
					if (isset($modelData['scanned_rx']['delete'])) {
						$path = Mage::getBaseDir('media') . DS . 'prescription' ;
						$fileName = $modelData['scanned_rx']['value'];
						@unlink($path.$fileName);
						$modelData['scanned_rx'] = '';
					} else {
						$modelData['scanned_rx'] = $modelData['scanned_rx']['value'];
					}
				}
			}

			$dateFields = array();
			$attributes = $model->getAttributes();
			if($attributes){			
				foreach ($attributes as $attrKey => $attribute) {
					if ($attribute->getBackend()->getType() == 'datetime') {
						if (array_key_exists($attrKey, $data) && $data[$attrKey] != ''){
							$dateFields[] = $attrKey;
						}
					}
				}
			}
 
			// make sure to filter date fields 
			$modelData = $this->_filterDates($modelData, $dateFields); 
			$model->addData($modelData);

			/**
			 * Check "Use Default Value" checkboxes values
			 */
			if ($useDefaults = $this->getRequest()->getPost('use_default')) {
				foreach ($useDefaults as $attributeCode) {
					$model->setData($attributeCode, false);
				}
			}
		}
		return $model;
	}

	public function savePrescriptionAction()
	{
		$storeId  = $this->getRequest()->getParam('store', 0);
		$item_id  = $this->getRequest()->getParam('item_id', 0);
		if ($data = $this->getRequest()->getPost()){ 

			$model = $model = $this->_initModelSave();

			try {

				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('prescription')->__('Prescription was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('prescription/adminhtml_prescription/view', array('item_id' => $item_id,'store' => $storeId, '_current'	=> true));
					return;
				}
				$this->_redirect('*/*/', array('store'=>$storeId));
				return;

            } catch (Exception $e) {

				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id'	=> $this->getRequest()->getParam('id'), 'store'	=> $storeId, '_current'	=>true));
				return;

            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promobanner')->__('Unable to find item to save'));
		$this->_redirect('*/*/', array('store'=>$storeId));
	}
}
