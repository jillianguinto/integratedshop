<?php

class Magestore_Inventory_Block_Adminhtml_Report_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getNoticeData()) {
            $data = Mage::getSingleton('adminhtml/session')->getNoticeData();
            Mage::getSingleton('adminhtml/session')->setNoticeData(null);
        } elseif (Mage::registry('notice_data')) {
            $data = Mage::registry('notice_data')->getData();
        }
        $fieldset = $form->addFieldset('notice_form', array(
            'legend' => Mage::helper('inventory')->__('General Information')
                ));

        $fieldset->addField('notice_date', 'label', array(
            'label' => Mage::helper('inventory')->__('Requested Date'),
            'name' => 'notice_date'
        ));
        $id = Mage::app()->getRequest()->getParam('id');
        $fieldset->addField('zdescription', 'label', array(
            'label' => Mage::helper('inventory')->__('Description'),
            //'name'        => 'description',
            'after_element_html' => Mage::helper('inventory')->renderNotice($id),
            'tabindex' => 1
        ));

        $fieldset->addField('comment', 'label', array(
            'label' => Mage::helper('inventory')->__('Comment'),
            'name' => 'comment'
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('inventory')->__('Status'),
            'name' => 'status',
            'values' => array('0' => 'Unread', '1' => 'Read')
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}