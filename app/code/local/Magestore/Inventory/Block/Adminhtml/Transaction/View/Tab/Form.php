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
class Magestore_Inventory_Block_Adminhtml_Transaction_View_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getTransactionData()) {
            $data = Mage::getSingleton('adminhtml/session')->getTransactionData();
            Mage::getSingleton('adminhtml/session')->setTransactionData(null);
        } elseif (Mage::registry('transaction_data')) {
            $data = Mage::registry('transaction_data')->getData();
        }

        $fieldset = $form->addFieldset('transaction_form', array(
            'legend' => Mage::helper('inventory')->__('Transaction Information')
            ));

        $fieldset->addField('from_name', 'label', array(
            'label' => Mage::helper('inventory')->__('From'),
            'name' => 'from_name',
        ));
        $fieldset->addField('to_name', 'label', array(
            'label' => Mage::helper('inventory')->__('To'),
            'name' => 'to_name',
        ));
        
        $fieldset->addField('created_by', 'label', array(
            'label' => Mage::helper('inventory')->__('Created By'),
            'name' => 'created_by',
        ));
        
        $fieldset->addField('created_at', 'label', array(
            'label' => Mage::helper('inventory')->__('Created At'),
            'name' => 'created_at',
        ));
        
        $fieldset->addField('reason', 'label', array(
            'name' => 'reason',
            'label' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
            'title' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}