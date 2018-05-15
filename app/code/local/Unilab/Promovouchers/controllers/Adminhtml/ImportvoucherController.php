<?php
class Unilab_Promovouchers_Adminhtml_ImportvoucherController extends Mage_Adminhtml_Controller_Action
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
        
		return $this->_Connection;
		
    }
	
	protected function _initAction()
    {

        $this->loadLayout()
            ->_setActiveMenu('promo/promo')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Promo Vouchers'), Mage::helper('adminhtml')->__('Import Promo Vouchers'));
        return $this;
		
        return $this;		
    }
	
	public function indexAction() 
	{	

		$this->_title($this->__('Promo Vouchers'))->_title($this->__('Import Promo Vouchers'));
	    $this->_initAction();    
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('promovouchers/importpromovoucher'));
		$this->renderLayout();	
	
    }
	
	public function importcsvAction() 
	{
		$postData = $this->getRequest()->getPost();
		$fullpath 	= $_FILES['csv_file']['tmp_name'];
		
		$fields				 		= array();
		$fields['salesrule_parent'] = $postData['salesrule'];
		
		$details = $this->salesrule_details($postData['salesrule']);
		
		$handle = fopen($fullpath, "r");
		$data = fgetcsv($handle, 1000, ",");
		
		date_default_timezone_set('Asia/Taipei');
		$date           = date("Y-m-d H:i:s");

		$i = true;
		
		try{
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
			{	
				
				$fields['voucher_code'] 	= $data[0];
				$fields['voucher_credits']	= $data[1];
				$fields['voucher_balance']	= $data[1];
				$fields['voucher_customer'] = $data[2];
				
				$this->_getConnection()->insert('unilab_promovouchers', $fields);
				$this->_getConnection()->commit(); 
				
				$fields2['rule_id'] 			= $fields['salesrule_parent'];
				$fields2['code'] 				= $data[0];
				$fields2['usage_limit'] 		= 0;
				$fields2['usage_per_customer'] 	= $details['usage_per_customer'];
				$fields2['times_used'] 			= 0;
				$fields2['expiration_date'] 	= $details['to_date'];
				$fields2['created_at'] 			= $date;
				$fields2['type'] 				= 1;
				
				$this->_getConnection()->insert('salesrule_coupon', $fields2);
				$this->_getConnection()->commit(); 
			
			}
			 
			$success = "Success";
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($success));
			
		}catch(Exception $e){
			
			Mage::log($e->getMessage());
			
			$warning = $e->getMessage();
			Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('adminhtml')->__($warning));
		}
		
		$this->_redirect('*/*/');
	}
	
	public function salesrule_details($saleruleid)
	{
		$qry 	= "SELECT * FROM  salesrule where rule_id='$saleruleid'";
		$result = $this->_getConnection()->fetchRow($qry);
		
		return $result;
	}
}