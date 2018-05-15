<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Transaction_View extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_warehouse';
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $transaction_id = $this->getRequest()->getParam('transaction_id');
        $transaction = Mage::getModel('inventory/transaction')->load($transaction_id);
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl("inventoryadmin/adminhtml_warehouse/edit", array("id" => $warehouse_id)) . '\')');
        if ($transaction->getType() == 1) {
            $id = $transaction->getRequestStockId();
            if ($id && Mage::helper('inventory/requeststock')->checkCancelRequeststock($id)) {
                $this->_addButton('cancelRequesting', array(
                    'label' => Mage::helper('adminhtml')->__('Cancel'),
                    'onclick' => 'cancelRequesting()',
                    'class' => 'delete',
                    ), -110);
            }
        }
        if ($transaction->getType() == 2) {
            $sendstockId = $transaction->getSendStockId();
            if($sendstockId) {
                $sendStock = Mage::getModel('inventory/sendstock')->load($sendstockId);
                $warehouseId = $sendStock->getToId();
                $admin = Mage::getSingleton('admin/session')->getUser();
                if ($warehouseId && Mage::helper('inventory/warehouse')->canTransfer($admin->getId(), $warehouseId)) {
                    if ($sendStock->getStatus() == 1) {
                        $day = Mage::getStoreConfig('inventory/transaction/cancel_time');
                        $created_at = $sendStock->getCreatedAt();
                        $cancelDay = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($created_at)) . " +$day day"));
                        if (strtotime($cancelDay) > strtotime(now())) {
                            $this->_addButton('cancelSending', array(
                                'label' => Mage::helper('adminhtml')->__('Cancel'),
                                'onclick' => 'cancelSending()',
                                'class' => 'delete',
                                ), -110);
                        }
                    }
                }
            }
        }
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_formScripts[] = "
			function cancelRequesting(){
				if (confirm('Are you sure?')) {
					var url = '" . $this->getUrl('inventory/adminhtml_requeststock/cancel', array('id' => $id, 'warehouse_id' => $warehouse_id)) . "';
					window.location.href = url;
				}
            }
            
            function cancelSending(){
				if (confirm('Are you sure?')) {
					var url = '" . $this->getUrl('inventory/adminhtml_sendstock/cancel', array('id' => $sendstockId,'warehouse_id'=>$warehouse_id)) . "';
					window.location.href = url;
				}
            }
		";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('transaction_data')
            && Mage::registry('transaction_data')->getId()
        ) {
            return Mage::helper('inventory')->__("View Transaction '%s'", $this->htmlEscape(Mage::helper('inventory')->getIncrementId(Mage::registry('transaction_data')))
            );
        }
    }

}