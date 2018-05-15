<?php

class Unilab_Priorityshipping_Adminhtml_PriorityshippingController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Unilab'))->_title($this->__('MOV Shipping'));
        $this->loadLayout();
        // $this->_setActiveMenu('system/minimumordervalue');
        $this->_setActiveMenu('system/minimumordervalue');
        $this->_addContent($this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue'));
        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_grid')->toHtml()
        );
    }
	
	public function newAction()
    {
		
        $movshippingID     	= $this->getRequest()->getParam('id');
        // $movshippingModel  	= Mage::getModel('minimumordervalue/minimumordervalue')->load($movshippingID);
        $movshippingModel   = Mage::getModel('minimumordervalue/movshipping')->load($movshippingID);
 
        if ($movshippingModel->getId() || $movshippingID == 0) {
 
            Mage::register('movshipping_data', $movshippingModel);
 
            $this->loadLayout();
            // $this->_setActiveMenu('unilab/minimumordervalue');
            $this->_setActiveMenu('system/minimumordervalue');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('MOV Shipping'), Mage::helper('adminhtml')->__('MOV Shipping'));
           
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_edit'))
                 ->_addLeft($this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_edit_tabs'));
               
            $this->renderLayout(); 
			
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minimumordervalue')->__('Group does not exist'));
            $this->_redirect('*/*/');
        }
		
    }
	
	public function editAction()
    {
	
        $movshippingID     = $this->getRequest()->getParam('id');
		
        //$movshippingModel  = Mage::getModel('minimumordervalue/minimumordervalue')->load($movshippingID);
        $movshippingModel  = Mage::getModel('minimumordervalue/movshipping')->load($movshippingID);

 
        if ($movshippingModel->getId() || $movshippingID == 0) {
 
            Mage::register('movshipping_data', $movshippingModel);
			
			$this->_title($this->__('Edit Group'))->_title($this->__('MOV'));

            $this->loadLayout();
			
            $this->_setActiveMenu('system/minimumordervalue');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('MOV Shipping'), Mage::helper('adminhtml')->__('MOV Shipping'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_edit'))
                 ->_addLeft($this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minimumordervalue')->__('Group does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
	public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) 
		{
		
            try {			
			
                $postData = $this->getRequest()->getPost();
												
                // $movshippingModel  = Mage::getModel('minimumordervalue/minimumordervalue');
                $movshippingModel  = Mage::getModel('minimumordervalue/movshipping');
										
                $id = $this->getRequest()->getParam('id');
                    $movshippingModel->setId($this->getRequest()->getParam('id'))
					->setgroup($postData['group'])
                    ->setlistofcities(implode(',', $postData['listofcities']))
                    ->setgreaterequal_mov($postData['greaterequal_mov'])
                    ->setlessthan_mov($postData['lessthan_mov'])
                    ->save(); 
						

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Group was successfully saved!'));
                $this->_redirect('*/*/');
				
                return;
				
            } catch (Exception $e) {
			
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setmilestonegiveawayData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				
				//Mage::log($e->getMessage(), null ,errorlog);
				
				
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                // $movshippingModel = Mage::getModel('minimumordervalue/minimumordervalue');
                $movshippingModel  = Mage::getModel('minimumordervalue/movshipping');
               
                $movshippingModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                                       
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
 

}