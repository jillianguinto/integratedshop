<?php 

class Unilab_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{ 
    public function canSendmailToAcountant()
    {
        return Mage::helper('sales')->canSendNewCreditmemoEmailToAccountant($this->getOrder()->getStore()->getId());
    }
}
