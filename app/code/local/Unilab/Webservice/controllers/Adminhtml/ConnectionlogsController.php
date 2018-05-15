<?php

class Unilab_Webservice_Adminhtml_ConnectionlogsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
    {
        $this->loadLayout()
			 ->_setActiveMenu('system/connectionlogs')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Connection Logs'), Mage::helper('adminhtml')->__('Connection Logs'));

        return $this;
    } 
	

	public function indexAction() 
	{	
		$this->_title($this->__('Connection Logs'))->_title($this->__('Webservice'));
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('webservice/adminhtml_connectionlogs'));
        $this->renderLayout();
    }	
	
	
	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('webservice/adminhtml_connectionlogs_grid')->toHtml()
        );
    }

}
?>