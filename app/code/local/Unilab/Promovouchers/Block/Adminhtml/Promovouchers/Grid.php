<?php
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
    public function __construct()
    {
        parent::__construct();
        $this->setId('promovouchersGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
		
        $collection = Mage::getModel('promovouchers/promovouchers')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('voucher_id', array(
            'header'    => Mage::helper('promovouchers')->__('ID'),
            'align'     =>'right',
            'index'     => 'voucher_id',
        ));
		
		$this->addColumn('salesrule_parent', array(
            'header'    => Mage::helper('promovouchers')->__('Sales Rule'),
            'align'     =>'left',
            'index'     => 'salesrule_parent',
			'renderer' => 'promovouchers/adminhtml_promovouchers_edit_renderer_salesrule',
        ));	
		
		$this->addColumn('voucher_code', array(
            'header'    => Mage::helper('promovouchers')->__('Voucher Code'),
            'align'     =>'left',
            'index'     => 'voucher_code',
        ));		
		
		$this->addColumn('voucher_credits', array(
            'header'    => Mage::helper('promovouchers')->__('Voucher Credits'),
            'align'     =>'right',	
            'index'     => 'voucher_credits',
        ));		

        $this->addColumn('voucher_balance', array(
            'header'    => Mage::helper('promovouchers')->__('Voucher Balance'),
            'align'     =>'right',   
            'index'     => 'voucher_balance',
        ));     

        $this->addColumn('voucher_customer', array(
            'header'    => Mage::helper('promovouchers')->__('Customer Assigned'),
            'align'     =>'right',  
            'index'     => 'voucher_customer',
        )); 
			
		$this->addColumn('action', array(
            'header' => $this->__('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array(
                        'base' => '*/*/delete'
                    ),
                    'field' => 'id',
                    'confirm' => $this->__('Are you sure?')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true
        ));
		
 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getVoucher_id()));
    }
 
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
	
 
}