<?php

class Unilab_Inquiry_Adminhtml_InquiryController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('inquiry/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Inquiry Manager'), Mage::helper('adminhtml')->__('Inquiry Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }    

    public function newAction() {
        $this->_forward('edit');
    }

   
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('inquiry/inquiry');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Inquiry was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $inquiryIds = $this->getRequest()->getParam('inquiry');
        if(!is_array($inquiryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select inquiry(s)'));
        } else {
            try {
                foreach ($inquiryIds as $inquiryId) {
                    $inquiry = Mage::getModel('inquiry/inquiry')->load($inquiryId);
                    $inquiry->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($inquiryIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $inquiryIds = $this->getRequest()->getParam('inquiry');
        if(!is_array($inquiryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select inquiry(s)'));
        } else {
            try {
                foreach ($inquiryIds as $inquiryId) {
                    $inquiry = Mage::getSingleton('inquiry/inquiry')
                        ->load($inquiryId)
                        ->setIsRead($this->getRequest()->getParam('is_read'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($inquiryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'inquiry.csv';
        $content    = $this->getLayout()->createBlock('inquiry/adminhtml_inquiry_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'foodpartners.xml';
        $content    = $this->getLayout()->createBlock('inquiry/adminhtml_inquiry_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    
	/**
     * Initialize Inquiry model
     */
    protected function _initInquiry()
    {
        $inquiry = Mage::getModel('inquiry/inquiry')->load(
            $this->getRequest()->getParam('id')
        );

        if (!$inquiry->getId()) {
            $this->_getSession()->addError($this->__('Wrong Inquiry ID specified.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }        

        Mage::register('current_inquiry', $inquiry);
        return $inquiry;
    }

	 /**
     * View Inquiry Details action
     */
    public function viewAction()
    {
    	
		$inquiry = $this->_initInquiry();
        if (!$inquiry) {
            return;
        }
		    	
		$this->_title(sprintf("Inquiry View #%s", $inquiry->getInquiryId()));
	
        $this->loadLayout()
            ->_setActiveMenu('inquiry/inquiry')
            ->renderLayout();
    }

}
