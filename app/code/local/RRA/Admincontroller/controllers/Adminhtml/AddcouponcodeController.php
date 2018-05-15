<?php
 
class RRA_Admincontroller_Adminhtml_AddcouponcodeController extends Mage_Adminhtml_Controller_Action
{
 
 
    protected function _initAction()
    {

        $this->loadLayout()
            ->_setActiveMenu('promo/promo')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Company Address'), Mage::helper('adminhtml')->__('Import Coupon Code'));
        return $this;
		
        return $this;		
    }   
   
    public function indexAction() {	
		
		
		$this->_title($this->__('Coupon Code'))->_title($this->__('Import'));
		
		$filecsv = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcode';
		$filehead = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodehead';
		$filecount = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodecount';
		unlink($filecsv);
		unlink($filehead);
		unlink($filecount);			
		Mage::getSingleton('core/session')->unsRecords();	
		Mage::getSingleton('core/session')->unssavecount(); 
	    $this->_initAction();    
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('admincontroller/importcouponcode'));
		$this->renderLayout();	
	
    }

    public function importcsvAction() {	
		
		
		$couponID 	= $_POST['couponid'];
		$fullpath 	= $_FILES['csv_file']['tmp_name'];

		$csv 		= array_map("str_getcsv", file($fullpath));
		$head		= array_shift($csv);

		mkdir(Mage::getBaseDir('var'). DS. 'cache'. DS . 'mage--csv');
		$filecsv = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcode';
		$filehead = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodehead';
		file_put_contents($filecsv, json_encode($csv));
		file_put_contents($filehead, json_encode($head));

		
		try{
		
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("admincontroller/adminhtml_addcouponcode/result/id/$couponID"));

		}catch(Exception $e){
			
			Mage::log($e->getMessage());
				
		}	
	
    }	
	
	public function resultAction()
	{
	
	    $this->_initAction();    
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('admincontroller/importcouponcoderesult'));
		$this->renderLayout();	
		
	}
	
}