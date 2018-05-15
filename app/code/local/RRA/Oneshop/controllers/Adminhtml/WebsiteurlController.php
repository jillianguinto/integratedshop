<?php
 
class RRA_Oneshop_Adminhtml_WebsiteurlController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
        $this->loadLayout()
		
            ->_setActiveMenu('system/websiteurl')
			
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Oneshop Website URL'), Mage::helper('adminhtml')->__('Oneshop Website URL'));
			
        return $this;
    }   
   
   
    public function indexAction() {	
	

		$this->_title($this->__('Admin'))->_title($this->__('Oneshop Website URL'));

        $this->_initAction();       
		
        $this->_addContent($this->getLayout()->createBlock('oneshop/adminhtml_websiteurl'));
		
        $this->renderLayout();
    }
	
	public function editAction()
    {
	
        $oneshopurlID     = $this->getRequest()->getParam('id');
		
		
											//module name // entityname from config.xml table variable
        $oneshopurlModel  = Mage::getModel('oneshop/oneshopurl')->load($oneshopurlID); 
 
        if ($oneshopurlModel->getId() || $oneshopurlID == 0) {
							//var only
            Mage::register('websiteurl_data', $oneshopurlModel);
			
			$this->_title($this->__('Edit Token'))->_title($this->__('Website URL'));

            $this->loadLayout();
									//nav menu	//modulename
            $this->_setActiveMenu('system/oneshop');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Website URL'), Mage::helper('adminhtml')->__('Website URL'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
																//module name	//block location	
            $this->_addContent($this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit'))
                 ->_addLeft($this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oneshop')->__('Website URL does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
	public function newAction()
    {
		
        $oneshopurlID     	= $this->getRequest()->getParam('id');
        $oneshopurlModel  	= Mage::getModel('oneshop/oneshopurl')->load($oneshopurlID);
 
        if ($oneshopurlModel->getId() || $oneshopurlID == 0) {
 
            Mage::register('websiteurl_data', $oneshopurlModel);
			
 			$this->_title($this->__('New Website URL'))->_title($this->__('Website URL'));
			
            $this->loadLayout();
			
            $this->_setActiveMenu('system/oneshop');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Website URL'), Mage::helper('adminhtml')->__('Website URL'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit'))
                 ->_addLeft($this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit_tabs'));
               
            $this->renderLayout();
			
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oneshop')->__('Website URL does not exist'));
            $this->_redirect('*/*/');
        }
    }	
	
    public function saveAction()
    {



        if ( $this->getRequest()->getPost() ) {
				$postData = $this->getRequest()->getPost();
				$date = date("Y/m/d H:i:s A");	
				
				
				if($postData['categoryid'])
				{
        			//$token 		= md5($postData['websiteurl'].$postData['banner']);
        			$separator	= md5("unilab");
        			$token 		= $postData['categoryid'] . $separator . md5($postData['websiteurl'].$postData['categoryid']). $separator . $postData['categoryid'];

                    //Mage::log($postData['websiteurl'].$separator, null, 'separator.log');
                    //Mage::log($postData['websiteurl'].$token, null, 'token.log');

        			if(isset($_FILES['banner']['name']) && $_FILES['banner']['name'] !== '')
        			{	
                        try {
            			 
    			            // part of an uplaod image					
                    		// $path 		= Mage::getBaseDir().'/onlinestore/media/images/banner/';
    						$path 		= Mage::getBaseDir().'/onlinestore/media/images/banner/';
    						$testPath = '/onlinestore/media/images/banner/';
    		                $fname 		= $_FILES['banner']['name']; //file name
                       		$fullname 	= $path.$fname;
                       		$true_name	= date('ymdhis', time()).basename($fname);
    						$test_target_path = $testPath.$true_name;
                       		$target_path = $path.$true_name;
                       		if(move_uploaded_file($_FILES['banner']['tmp_name'], $target_path)){
                       			Mage::log('The file ' .basename($fname). ' has been uploaded to ' . $path, true, 'upload_image_1.log');        
                       		}else{
                       			Mage::log($path , true, 'upload_image_0.log');      
                       		}
                            $oneshopurlModel = Mage::getModel('oneshop/oneshopurl');		
                            $oneshopurlModel->setId($this->getRequest()->getParam('id'))
                                ->setwebsiteurl($postData['websiteurl'])
                                ->setcategoryid($postData['categoryid'])
                                ->settoken($token)
                                ->setbanner($test_target_path)
                                // ->setdate_created($currentdate)
                                // ->setdate_updated($updatedate)
                                ->save();
                           

                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Website URL was successfully saved'));
             
                            $this->_redirect('*/*/');
            				
                            return;
            				
                        } catch (Exception $e) {
            				
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            				
                            return;
                        }
        			}
        			else
        			{
        				$true_name	= 'noimage.png';
        				$testPath = '/onlinestore/media/images/banner/';
        				$test_target_path = $testPath.$true_name;				
                        $oneshopurlModel = Mage::getModel('oneshop/oneshopurl');		
                        $oneshopurlModel->setId($this->getRequest()->getParam('id'))
                            ->setwebsiteurl($postData['websiteurl'])
                            ->setcategoryid($postData['categoryid'])
                            ->settoken($token)
        					->setbanner($test_target_path)
                            ->save();
        			
        			}
				}
				
		
			
       } 
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $inventorybatchModel = Mage::getModel('oneshop/oneshopurl');
               
                $inventorybatchModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Website URL was successfully deleted'));
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
	 
	public function exportXLSAction()
    {
		$fileName   = 'ListofTokens.xls';
		$content    = $this->getLayout()->createBlock('RRA_Oneshop_Block_Adminhtml_Websiteurl_Grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);  

    }
	 
    public function gridAction()
    {
		
        $this->loadLayout();
		
        $this->getResponse()->setBody(
		
               $this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_grid')->toHtml()
			   
        );
    }
	


}