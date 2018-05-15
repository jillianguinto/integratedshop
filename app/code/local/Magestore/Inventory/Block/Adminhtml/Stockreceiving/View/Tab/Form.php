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
class Magestore_Inventory_Block_Adminhtml_Stockreceiving_View_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getReceivingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getReceivingData();
            Mage::getSingleton('adminhtml/session')->setReceivingData(null);
        } elseif (Mage::registry('stockreceiving_data')) {
            $data = Mage::registry('stockreceiving_data')->getData();
        }
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $adminCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $this->getRequest()->getParam('warehouse_id'));
        foreach ($adminCollection as $admin) {
            $can_transfer = $admin->getCanTransfer();
        }
        $dataObject = new Varien_Object();
        $dataObject->setData($data);
        $warehouseSource = Mage::getModel('inventory/warehouse')->load($dataObject->getData('warehouse_id_from'));
        $warehouseDestination = Mage::getModel('inventory/warehouse')->load($dataObject->getData('warehouse_id_to'));
        $fieldset = $form->addFieldset('stockreceiving_form', array(
            'legend' => Mage::helper('inventory')->__('Stock Receiving')
            ));
        if ($this->getRequest()->getParam('id') && $dataObject->getType() == 1) {
            $fieldset->addField('warehouse_id_from', 'note', array(
                'label' => Mage::helper('inventory')->__('From Warehouse'),
                'name' => 'warehouse_id_from',
                'after_element_html' => '
                <input id="warehouse_id_from" name="warehouse_id_from" type="hidden" value="'.$warehouseSource->getId().'" />
                <a href='.Mage::helper("adminhtml")->getUrl('inventoryadmin/adminhtml_warehouse/edit', array('id'=>$warehouseSource->getId())).'>'.$warehouseSource->getName().'</a>                                    
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
                                        </table>'
            ));
        }
        $fieldset->addField('warehouse_id_to', 'note', array(
            'label' => Mage::helper('inventory')->__('To Warehouse'),
            'name' => 'warehouse_id_to',
            'after_element_html' => '<input id="warehouse_id_to" name="warehouse_id_to" type="hidden" value="'.$warehouseDestination->getId().'" />
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
                                        </table>'
        ));
        
        if ($this->getRequest()->getParam('id')) {
           if($can_transfer == 1){
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
            }else{
                $fieldset->addField('created_at', 'note', array(
                'label' => Mage::helper('inventory')->__('Created On'),
                'class' => 'required-entry',
                'name' => 'created_at',
                'text' => $dataObject->getCreatedAt()
                ));
            }
        } else {
            $fieldset->addField('created_at', 'date', array(
                'label' => Mage::helper('inventory')->__('Created On'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'created_at',
                'index' => 'created_at',
                'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'value' => 'created_at',
                'time' => false,
            ));
        }
        
        if ($this->getRequest()->getParam('id')) {
            if($can_transfer == 1){
            $fieldset->addField('reference_invoice_number', 'text', array(
                'label' => Mage::helper('inventory')->__('Reference Invoice Number'),
                'name' => 'reference_invoice_number',
                'index' => 'reference_invoice_number',
            ));
            }else{
                $fieldset->addField('reference_invoice_number', 'note', array(
                'label' => Mage::helper('inventory')->__('Reference Invoice Number'),
                'name' => 'reference_invoice_number',
                'text' => $dataObject->getReferenceInvoiceNumber(),
            ));
            }
        } else {
            $fieldset->addField('reference_invoice_number', 'text', array(
                'label' => Mage::helper('inventory')->__('Reference Invoice Number'),
                'disabled' => false,
                'name' => 'reference_invoice_number',
            ));
        }
        
        if ($this->getRequest()->getParam('id')) {
            if($can_transfer == 1){
            $fieldset->addField('comment','textarea',array(
                'label'        => Mage::helper('inventory')->__('Comment'),
                'name'         => 'comment',
                'disabled'     => false,
            ));
            }else{
                $fieldset->addField('comment', 'note', array(
                'label' => Mage::helper('inventory')->__('Comment'),
                'name' => 'comment',
                'text' => $dataObject->getComment(),
            ));
            }
        } else {
            $fieldset->addField('comment','textarea',array(
                'label'        => Mage::helper('inventory')->__('Comment'),
                'name'         => 'comment',
                'disabled'     => false,
            ));
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

}