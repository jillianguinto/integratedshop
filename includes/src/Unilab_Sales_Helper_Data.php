<?php 

class Unilab_Sales_Helper_Data extends Mage_Sales_Helper_Data
{  
	public function canSendNewCreditmemoEmailToAccountant($store = null)
    {
        return Mage::getStoreConfigFlag(Unilab_Sales_Model_Order_Creditmemo::XML_PATH_ACCOUNTANT_EMAIL_ENABLED, $store);
    }
}
