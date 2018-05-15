<?php

class Unilab_Inquiry_Block_Adminhtml_Inquiry_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('inquiry_form', array('legend'=>Mage::helper('inquiry')->__('Inquiry information')));


      $fieldset->addField('store_id', 'multiselect', array(
          'name'      => 'store_id',
          'label'     => Mage::helper('inquiry')->__('Store'),
          'title'     => Mage::helper('inquiry')->__('Store'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
	  
	   $fieldset->addField('department', 'text', array(
          'name'      => 'department',
          'label'     => Mage::helper('inquiry')->__('Department'),
          'title'     => Mage::helper('inquiry')->__('Department'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
	  
	  $fieldset->addField('department_email', 'text', array(
          'name'      => 'department_email',
          'label'     => Mage::helper('inquiry')->__('Department Email'),
          'title'     => Mage::helper('inquiry')->__('Department Email'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
	  
	  $fieldset->addField('name', 'text', array(
          'name'      => 'name',
          'label'     => Mage::helper('inquiry')->__('Customer Name'),
          'title'     => Mage::helper('inquiry')->__('Customer Name'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
	  
	  $fieldset->addField('email_address', 'text', array(
          'name'      => 'email_address',
          'label'     => Mage::helper('inquiry')->__('Customer Email'),
          'title'     => Mage::helper('inquiry')->__('Customer Email'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
	  
	
	  
	   $fieldset->addField('concern', 'editor', array(
          'name'      => 'concern',
          'label'     => Mage::helper('inquiry')->__('Concern/Reason'),
          'title'     => Mage::helper('inquiry')->__('Concern/Reason'),
          'style'     => 'width:500px; height:100px;',
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));

		$fieldset->addField('created_time', 'text', array(
          'name'      => 'created_time',
          'label'     => Mage::helper('inquiry')->__('Date Created'),
          'title'     => Mage::helper('inquiry')->__('Date Created'),
          'wysiwyg'   => false,
          'required'  => false,
          'readonly'  => true
      ));
     

      if ( Mage::getSingleton('adminhtml/session')->getFoodpartnersData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFoodpartnersData());
          Mage::getSingleton('adminhtml/session')->setFoodpartnersData(null);
      } elseif ( Mage::registry('inquiry_data') ) {
          $form->setValues(Mage::registry('inquiry_data')->getData());
      }
      return parent::_prepareForm();
  }

    public function getFeaturedProducts(){
        $foodpartners = Mage::getModel('inquiry/inquiry')->getCollection()
                            ->addFieldToFilter('status',array('eq'=>1))
                            ->addOrder('title', 'ASC')
                            ->getData();

        $options = array();
        if(count($foodpartners)>0){
            foreach($foodpartners as $row){
                $options[] = array('value'  => $row['inquiry_id'],
                                   'label' => $row['title'] );
            }
        }
        return $options;
    }

}
