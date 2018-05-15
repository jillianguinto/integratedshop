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
 * Inventory Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Stockissuing_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getStockissuingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStockissuingData();
            Mage::getSingleton('adminhtml/session')->setStockissuingData(null);
        } elseif (Mage::registry('stockissuing_data')) {
            $data = Mage::registry('stockissuing_data')->getData();
        }
        $dataObject = new Varien_Object();
        $dataObject->setData($data);
        $warehouseId = $dataObject->getData('warehouse_id_from');
        if (!$warehouseId)
            $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $warehouseSource = Mage::getModel('inventory/warehouse')->load($warehouseId);
        $warehouseDestination = Mage::getModel('inventory/warehouse')->load($dataObject->getData('warehouse_id_to'));
        $fieldset = $form->addFieldset('stockissuing_form', array(
            'legend' => Mage::helper('inventory')->__('Stock Issuing')
            ));
        $fieldset->addField('warehouse_from', 'note', array(
            'label' => Mage::helper('inventory')->__('From Warehouse'),
            'name' => 'warehouse_from',
            'after_element_html' => '<input type="hidden" name="warehouse_id_from" id="warehouse_id_from" value="' . $warehouseId . '" />
                                        <a href='.Mage::helper("adminhtml")->getUrl('inventoryadmin/adminhtml_warehouse/edit', array('id'=>$warehouseId)).'>'.$warehouseSource->getName().'</a>                                    
                                        <table>
                                            <tr>
                                                <td>Manager Name:</td>
                                                <td>'.$warehouseSource->getManagerName().'</td>
                                            </tr>
                                            <tr>
                                                <td>Manager Email:</td>
                                                <td>'.$warehouseSource->getManagerEmail().'</td>
                                            </tr>
                                            <tr>
                                                <td>Phone Number:</td>
                                                <td>'.$warehouseSource->getTelephone().'</td>
                                            </tr>
                                        </table>
                                    '
        ));
        $fieldset->addField('created_at', 'date', array(
            'label' => Mage::helper('inventory')->__('Created On'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'created_at',
            'index' => 'created_at',
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'value' => 'created_at',
            'time' => false,            
        ));                
        
        if ($dataObject->getType() == 1) {
            $fieldset->addField('warehouse_id_to', 'note', array(
                'label' => Mage::helper('inventory')->__('To Warehouse'),
                'class' => 'required-entry',
                'name' => 'warehouse_id_to',
                'text' => $warehouseDestination->getName()
            ));
        }
        $fieldset->addField('reference_invoice_number', 'text', array(
            'label' => Mage::helper('inventory')->__('Reference Invoice Number'),
            'disabled' => false,
            'name' => 'reference_invoice_number',
        ));
        $fieldset->addField('comment', 'textarea', array(
            'label' => Mage::helper('inventory')->__('Comment'),
            'name' => 'comment',
            'disabled' => false,
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}