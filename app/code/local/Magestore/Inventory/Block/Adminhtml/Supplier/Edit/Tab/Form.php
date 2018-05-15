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
class Magestore_Inventory_Block_Adminhtml_Supplier_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getSupplierData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSupplierData();
            Mage::getSingleton('adminhtml/session')->setSupplierData(null);
        } elseif (Mage::registry('supplier_data')) {
            $data = Mage::registry('supplier_data')->getData();
        }
        $fieldset = $form->addFieldset('supplier_form', array(
            'legend' => Mage::helper('inventory')->__('General Information')
                ));

        if ($this->getRequest()->getParam('id'))
            $fieldset->addField('create_by', 'label', array(
                'label' => Mage::helper('inventory')->__('Created by'),
            ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('inventory')->__('Supplier Name '),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('contact_name', 'text', array(
            'label' => Mage::helper('inventory')->__('Contact Person'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'contact_name',
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('inventory')->__('Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'email',
        ));

        $fieldset->addField('telephone', 'text', array(
            'label' => Mage::helper('inventory')->__('Telephone'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'telephone',
        ));
        $fieldset->addField('fax', 'text', array(
            'label' => Mage::helper('inventory')->__('Fax'),
            'required' => false,
            'name' => 'fax',
        ));
        $fieldset->addField('street', 'text', array(
            'label' => Mage::helper('inventory')->__('Street'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'street',
        ));
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('inventory')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));
        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('inventory')->__('Country'),
            'required' => true,
            'name' => 'country_id',
            'values' => Mage::helper('inventory')->getCountryListHash(),
        ));
        $fieldset->addField('stateEl', 'note', array(
            'label' => Mage::helper('inventory')->__('State/Province'),
            'name' => 'stateEl',
            'text' => $this->getLayout()->createBlock('inventory/adminhtml_region')->setTemplate('inventory/region_supplier.phtml')->toHtml(),
        ));
        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('inventory')->__('Zip/Postal Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'postcode',
        ));

        $fieldset->addField('website', 'text', array(
            'label' => Mage::helper('inventory')->__('Website'),
            'required' => false,
            'name' => 'website',
        ));
        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('inventory')->__('Description'),
            'title' => Mage::helper('inventory')->__('Description'),
            'style' => 'width:274px; height:200px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('inventory')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('inventory/status')->getOptionHash(),
        ));

        if (Mage::getStoreConfig('inventory/dropship/enable')) {
            $fieldset = $form->addFieldset('supplierpass_form', array(
                'legend' => Mage::helper('inventory')->__('Password Management')
                    ));

            $fieldset->addField('new_password', 'text', array(
                'label' => Mage::helper('inventory')->__('New Password'),
                'required' => false,
                'name' => 'new_password',
            ));

            $fieldset->addField('send_mail', 'checkbox', array(
                'label' => Mage::helper('inventory')->__('Send new password to supplier'),
                'required' => false,
                'name' => 'send_mail',
            ));
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

}