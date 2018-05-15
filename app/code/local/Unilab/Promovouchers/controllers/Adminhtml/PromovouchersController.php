<?php
class Unilab_Promovouchers_Adminhtml_PromovouchersController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initAction()
    {

	    $this->loadLayout()

            ->_setActiveMenu('promo/promo')

            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Promo Vouchers'), Mage::helper('adminhtml')->__('Promo Vouchers'));

        return $this;

    }

	
	public function indexAction() 
	{	

		$this->_title($this->__('catalogrule'))->_title($this->__('Promo Vouchers'));

	    $this->_initAction();       
		
        $this->_addContent($this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers'));

        $this->renderLayout();

    }
	
	
	public function newAction()
    {

        $promovouchersID     	= $this->getRequest()->getParam('id');

        $promovouchersModel  	= Mage::getModel('promovouchers/promovouchers')->load($promovouchersID);

 

        if ($promovouchersModel->getId() || $promovouchersID == 0) 
		{

            Mage::register('promovouchers_data', $promovouchersModel);


            $this->loadLayout();

            $this->_setActiveMenu('promo/promo');

           

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Promo Vouchers'), Mage::helper('adminhtml')->__('Manage Promo Vouchers'));

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Promo Vouchers'), Mage::helper('adminhtml')->__('Manage Promo Vouchers'));

           

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

           

            $this->_addContent($this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_edit'))

                 ->_addLeft($this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_edit_tabs'));

            $this->renderLayout();

			

        } else {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promovouchers')->__('Item does not exist'));

            $this->_redirect('*/*/');

        }

    }	
	
	public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
		
            try {			
                $postData = $this->getRequest()->getPost();
												
				$promovouchersModel = Mage::getModel('promovouchers/promovouchers');
				
				$promovouchersModel->setvoucher_id($postData['voucher_id'])
						->setsalesruleParent($postData['salesrule_parent'])
						->setvoucherCode($postData['voucher_code'])
						->setvoucherCredits($postData['voucher_credits'])
						->setvoucherBalance($postData['voucher_credits'])
                        ->setvoucherCustomer($postData['voucher_customer'])
						->save(); 

						
				$promovouchersid = $promovouchersModel->getvoucher_id();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Promo Vouchers was successfully saved!'));
                Mage::getSingleton('adminhtml/session')->setmilestonegiveawayData(false);
 
                $this->_redirect('*/*/');
				
                return;
				
            } catch (Exception $e) {
			
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setmilestonegiveawayData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			
                return;
            }
        }
        $this->_redirect('*/*/');
    }
	   
	
	public function editAction()
    {
		$this->_title($this->__('promovouchers'))->_title($this->__('Edit Promo Vouchers'));

        $promovouchersID     	= $this->getRequest()->getParam('id');
        $promovouchersModel  	= Mage::getModel('promovouchers/promovouchers')->load($promovouchersID);
 
        if ($promovouchersModel->getvoucher_id() || $promovouchersID == 0) {
 
            Mage::register('promovouchers_data', $promovouchersModel);
 
            $this->loadLayout();
            $this->_setActiveMenu('promo/promo');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Promo Vouchers'), Mage::helper('adminhtml')->__('Manage Promo Vouchers'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Promo Vouchers Edit'), Mage::helper('adminhtml')->__('Manage Promo Vouchers Edit'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_edit'))
                 ->_addLeft($this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_edit_tabs'));
               
            $this->renderLayout();
			
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promovouchers')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
		
    }
	
	public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $promovouchersModel = Mage::getModel('promovouchers/promovouchers');
               
                $promovouchersModel->setvoucher_id($this->getRequest()->getParam('id'))
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
	
	public function gridAction()
    {	
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_grid')->toHtml()
        );
    }
	
}