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
 * Inventory Supplier Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Paymentterm_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Paymentterm_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getPaymenttermData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPaymenttermData();
            Mage::getSingleton('adminhtml/session')->setPaymenttermData(null);
        } elseif (Mage::registry('paymentterm_data')) {
            $data = Mage::registry('paymentterm_data')->getData();
        }
        $fieldset = $form->addFieldset('paymentterm_form', array(
            'legend' => Mage::helper('inventory')->__('General')
                ));

        if ($this->getRequest()->getParam('id'))
            $fieldset->addField('create_by', 'label', array(
                'label' => Mage::helper('inventory')->__('Created By'),
            ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('inventory')->__('Payment Term Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('inventory')->__('Description'),
            'title' => Mage::helper('inventory')->__('Description'),
            'style' => 'width:274px; height:200px;',
            'wysiwyg' => false,
            'required' => false,
        ));
        
        $fieldset->addField('payment_days', 'text', array(
            'label' => Mage::helper('inventory')->__('Payment Period'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'payment_days',
            'note' => Mage::helper('inventory')->__('day(s). Payment should be completed within this period.'),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('inventory')->__('Status'),
            'name' => 'status',
            'values' => array(
                1 => Mage::helper('inventory')->__('Active'),
                0 => Mage::helper('inventory')->__('Inactive')
            )
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}