<?php
class Magestore_Inventory_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('noticeGrid');
        $this->setDefaultSort('notice_id');
        $this->setDefaultDir('DESC');
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventory/notice')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('notice_id', array(
            'header'    => Mage::helper('inventory')->__('ID'),
            'width' => '5px',
            'index'     => 'notice_id',
            'type'      => 'number'
        ));
        $this->addColumn('notice_date', array(
            'header'    => Mage::helper('inventory')->__('Notice Date'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'notice_date',
            //'type'      => 'date'
        ));
        $this->addColumn('description', array(
            'header'    => Mage::helper('inventory')->__('Description'),
            'align'     =>'left',
            //'width'     => '50px',
            'index'     => 'description',
            'renderer' => 'inventory/adminhtml_report_other_renderer_renderernotice',
            //'type'      => 'text'
        ));
        $this->addColumn('comment', array(
            'header'    => Mage::helper('inventory')->__('Comment'),
            'align'     =>'left',
            //'width'     => '50px',
            'index'     => 'comment',
            'type'      => 'text'
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('inventory')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'renderer' => 'inventory/adminhtml_report_other_renderer_renderernotice',
            'type'        => 'options',
            'options'     => array(
                0 => 'Unread',
                1 => 'Read',
            )
        ));
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notice_id');
        $this->getMassactionBlock()->setFormFieldName('notice');
        
        $statuses = array('Unread','Read');
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('inventory')->__('Change status'),
            'url'    => $this->getUrl('*/*/massstatusnotice', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('inventory')->__('Status'),
                    'values'=> $statuses
                ))
        ));

        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/readnotice', array('id' => $row->getId()));
    }
    
}