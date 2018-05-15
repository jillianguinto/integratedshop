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
class Magestore_Inventory_Block_Adminhtml_Stockreceiving_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getStockreceivingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStockreceivingData();
            Mage::getSingleton('adminhtml/session')->setStockreceivingData(null);
        } elseif (Mage::registry('stockreceiving_data')) {
            $data = Mage::registry('stockreceiving_data')->getData();
        }
        $dataObject = new Varien_Object();
        $dataObject->setData($data);
        $warehouseId = $dataObject->getData('warehouse_id_to');
        if (!$warehouseId)
            $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $warehouseSource = Mage::getModel('inventory/warehouse')->load($dataObject->getData('warehouse_id_from'));
        $warehouseDestination = Mage::getModel('inventory/warehouse')->load($warehouseId);
        $fieldset = $form->addFieldset('stockreceiving_form', array(
            'legend' => Mage::helper('inventory')->__('Stock Receiving')
            ));
        
        $fieldset->addField('warehouse_to', 'note', array(
            'label' => Mage::helper('inventory')->__('To Warehouse'),
            'name' => 'warehouse_from',
            'after_element_html' => '<input type="hidden" name="warehouse_id_to" id="warehouse_id_to" value="' . $warehouseDestination->getId() . '" />
                                        <a href='.Mage::helper("adminhtml")->getUrl('inventoryadmin/adminhtml_warehouse/edit', array('id'=>$warehouseDestination->getId())).'>'.$warehouseDestination->getName().'</a>                                    
                                        <table>
                                            <tr>
                                                <td>Manager Name:</td>
                                                <td>'.$warehouseDestination->getManagerName().'</td>
                                            </tr>
                                            <tr>
                                                <td>Manager Email:</td>
                                                <td>'.$warehouseDestination->getManagerEmail().'</td>
                                            </tr>
                                            <tr>
                                                <td>Phone Number:</td>
                                                <td>'.$warehouseDestination->getTelephone().'</td>
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

        $fieldset->addField('reference_invoice_number', 'text', array(
            'label' => Mage::helper('inventory')->__('Reference Invoice Number'),
            'disabled' => false,
            'index' => 'reference_invoice_number',
            'name' => 'reference_invoice_number',
        ));

        $fieldset->addField('type', 'hidden', array(
            'label' => Mage::helper('inventory')->__('Type'),
            'name' => 'type',
            'text' => 'Custom',
            'after_element_html' => '<input type="hidden" name="type" id="type" value="3" />',
        ));
        
         $fieldset->addField('comment','textarea',array(
                'label'        => Mage::helper('inventory')->__('Comment'),
                'name'         => 'comment',
                'disabled'     => false,
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}