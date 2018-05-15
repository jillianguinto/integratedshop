<?php

/**
 *
 * @package Unilab_Prescription
 * @author jerick.duguran@gmail.com - Movent Inc.
 * @date 2013-10-22 03:00 PM
 *
 */
 
class Unilab_Prescription_Block_Adminhtml_Prescription_Sales_Order_View_Tab_Prescriptions 
	extends Mage_Adminhtml_Block_Sales_Order_Abstract
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{  
	
	protected function _construct()
    {
        parent::_construct();
		
        $this->setTemplate('prescription/sales/order/view/tab/prescriptions.phtml');
        $this->setId('order_prescriptions');
        $this->setUseAjax(true);
    }
	
	public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    } 
	
	 /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }
	
    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('prescription/adminhtml_prescription/order', array('_current' => true));
    }
	
	public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/sales_order_shipment/view',
            array(
                'shipment_id'=> $row->getId(),
                'order_id'  => $row->getOrderId()
             ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/shipments', array('_current' => true));
    }
 	
	
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('prescription')->__('Prescriptions');
    }

    public function getTabTitle()
    {
        return Mage::helper('prescription')->__('Order Prescriptions');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }

} 
?>