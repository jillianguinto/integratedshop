<?php

class Unilab_Inquiry_Block_Adminhtml_Inquiry_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this -> setId('inquiryGrid');
		$this -> setDefaultSort('inquiry_id');
		$this -> setDefaultDir('DESC');
		$this -> setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel('inquiry/inquiry') -> getCollection();
		$this -> setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this -> addColumn('inquiry_id', array('header' => Mage::helper('inquiry') -> __('ID'), 'align' => 'right', 'width' => '50px', 'index' => 'inquiry_id', ));

		$this -> addColumn('store_id', array('header' => Mage::helper('inquiry') -> __('Store'), 'align' => 'left', 'index' => 'store_id', 'type' => 'store', 'width' => '100px', ));

		$this -> addColumn('department', array('header' 	=> Mage::helper('inquiry') -> __('Department'), 
											   'align' 		=> 'left',
											   'index' 		=> 'department', 
											   'width' 		=> '100px',
											   'renderer'   => 'inquiry/adminhtml_inquiry_renderer_department',
											   'type'       => 'string'
											   
		));

		$this -> addColumn('department_email', array('header' => Mage::helper('inquiry') -> __('Department Email'), 'align' => 'left', 'index' => 'department_email', 'type' => 'string', 'width' => '100px',
		//'renderer'  => 'inquiry/adminhtml_inquiry_renderer_department'
		));

		$this -> addColumn('customer_id', 
				array('header' => Mage::helper('inquiry') -> __('Type'), 
					'align' => 'left', 'index' => 'customer_id', 
					//'type' => 'text', 
					'width' => '100px',
					'renderer'  => 'inquiry/adminhtml_inquiry_renderer_customertype',
					'type'      => 'options',
					'options'   => Mage::getSingleton('inquiry/status')->getCustomerTypes()
            		//'options'   => Mage::getSingleton('sales/order_payment_transaction')->getTransactionTypes()
		));
		
		
		
		$this -> addColumn('name', array('header' => Mage::helper('inquiry') -> __('Customer Name'), 'align' => 'left', 'index' => 'name', 'type' => 'string', 'width' => '100px',
		//'renderer'  => 'inquiry/adminhtml_inquiry_renderer_customer'
		));

		$this -> addColumn('email_address', array('header' => Mage::helper('inquiry') -> __('Customer Email'), 'align' => 'left', 'index' => 'email_address', 'type' => 'string', 'width' => '100px' ));

	/* 	$this -> addColumn('concern', array('header' => Mage::helper('inquiry') -> __('Concern/Reason'), 'align' => 'left', 'index' => 'concern', 'type' => 'string', 'width' => '200px' ));
 */
		//$this -> addColumn('created_time', array('header' => Mage::helper('inquiry') -> __('Date Created'), 'align' => 'left', 'index' => 'created_time', 'type' => 'string', 'width' => '100px'));
		
		
        $this->addColumn('is_read', array(
          'header'    => Mage::helper('inquiry')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_read',
          'type'      => 'options',
          'options'   => array(
              1 => 'Read',
              0 => 'Pending',
          ),
        ));
		
		$this->addColumn('created_time', array(
            'header'    => Mage::helper('sales')->__('Created At'),
            'index'     => 'created_time',
            'width'     => 1,
            'type'      => 'datetime',
            'align'     => 'center',
            'default'   => $this->__('N/A'),
            'html_decorators' => array('nobr')
        ));
 
		
		$this -> addExportType('*/*/exportCsv', Mage::helper('inquiry') -> __('CSV'));
		$this -> addExportType('*/*/exportXml', Mage::helper('inquiry') -> __('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this -> setMassactionIdField('inquiry_id');
		$this -> getMassactionBlock() -> setFormFieldName('inquiry');

		$this -> getMassactionBlock() -> addItem('delete', array('label' => Mage::helper('inquiry') -> __('Delete'), 'url' => $this -> getUrl('*/*/massDelete'), 'confirm' => Mage::helper('inquiry') -> __('Are you sure?')));

		$statuses = Mage::getSingleton('inquiry/status')->getOptionArray();

       // array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('is_active', array(
             'label'=> Mage::helper('inquiry')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'is_read',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('inquiry')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
		
		return $this;
	}

	public function getRowUrl($row) {
		//return $this -> getUrl('*/*/edit', array('id' => $row -> getId()));
		return $this->getUrl('*/*/view', array('id' => $row->getId()));
	}

}
