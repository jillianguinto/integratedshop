<?php
 
class Unilab_Webservice_Adminhtml_ManagetokenController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
        $this->loadLayout()
		
            ->_setActiveMenu('system/token')
			
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage API Token'), Mage::helper('adminhtml')->__('Manage API Token'));
			
        return $this;
    }   
   
   
    public function indexAction() 
    {	
	$this->_title($this->__('Manage Token'))->_title($this->__('API'));

        $this->_initAction();       
		
        $this->_addContent($this->getLayout()->createBlock('webservice/adminhtml_token'));
		
        $this->renderLayout();
    }
 
    public function editAction()
    {
	
        $tokenID     = $this->getRequest()->getParam('id');
		
        $tokenModel  = Mage::getModel('webservice/token')->load($tokenID);
 
        if ($tokenModel->getId() || $tokenID == 0) {
 
            Mage::register('token_data', $tokenModel);
			
            $this->_title($this->__('Edit Token'))->_title($this->__('API'));

            $this->loadLayout();
			
            $this->_setActiveMenu('system/token');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('API Manager'), Mage::helper('adminhtml')->__('API Manager'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('webservice/adminhtml_token_edit'))
                 ->_addLeft($this->getLayout()->createBlock('webservice/adminhtml_token_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webservice')->__('API does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
    public function newAction()
    {
		
        $tokenID     	= $this->getRequest()->getParam('id');
        $tokenModel  	= Mage::getModel('webservice/token')->load($tokenID);
 
        if ($tokenModel->getId() || $tokenID == 0) {
 
            Mage::register('token_data', $tokenModel);
			
            $this->_title($this->__('New Token'))->_title($this->__('API'));
			
            $this->loadLayout();
			
            $this->_setActiveMenu('system/token');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('API News'), Mage::helper('adminhtml')->__('API News'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('webservice/adminhtml_token_edit'))
                 ->_addLeft($this->getLayout()->createBlock('webservice/adminhtml_token_edit_tabs'));
               
            $this->renderLayout();
			
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webservice')->__('API does not exist'));
            $this->_redirect('*/*/');
        }
    }	
	
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
			
            try {
			
                $postData = $this->getRequest()->getPost();
				
					if (empty($postData['createddate'])):	
							
								$currentdate = date("Y/m/d  H:i:s A");
								
								$updatedate = date("Y/m/d H:i:s A");	
							
								$salt	= md5("webservice" . $currentdate);
								$token 	= md5($salt .':'.$postData['hostname']);				
										
										$tokenModel = Mage::getModel('webservice/token');		
									   
										$tokenModel->setId($this->getRequest()->getParam('id'))
											->setstore($postData['store'])
											->sethostname($postData['hostname'])
											->setreturnurl($postData['returnurl'])	
											->settoken($token)
											->setisactive($postData['isactive'])
											->setcreateddate($currentdate)
											->setupdatedate($updatedate)
											->save();
								
					else:		
							
								$currentdate = $postData['createddate'];	
								
								$updatedate = date("Y/m/d H:i:s A");	
										
										$tokenModel = Mage::getModel('webservice/token');		
										$tokenModel->setId($this->getRequest()->getParam('id'))
											->setstore($postData['store'])
											->sethostname($postData['hostname'])
											->setreturnurl($postData['returnurl'])
											->setisactive($postData['isactive'])
											->setcreateddate($currentdate)
											->setupdatedate($updatedate)
											->save();
								
					endif;


					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('API Token was successfully saved'));
	 
					$this->_redirect('*/*/');
					
					return;
				
            } catch (Exception $e) {
				
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $inventorybatchModel = Mage::getModel('webservice/token');
               
                $inventorybatchModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('API Token was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
	 
    public function gridAction()
    {
		
        $this->loadLayout();
		
        $this->getResponse()->setBody(
		
               $this->getLayout()->createBlock('webservice/adminhtml_token_grid')->toHtml()
			   
        );
    }
}