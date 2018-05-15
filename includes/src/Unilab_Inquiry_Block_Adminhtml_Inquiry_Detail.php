<?php

/**
 * Adminhtml Inquiry detail
 *
 * @category   Unilab
 * @package    Unilab_Inquity
 * @author     Movent Inc <diszo.sasil@movent.com>
 */
class Unilab_Inquiry_Block_Adminhtml_Inquiry_Detail extends Mage_Adminhtml_Block_Widget_Container
{
  
   protected $_inquiry;
   protected $_department;
  
    /**
     * Add control buttons
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_inquiry = Mage::registry('current_inquiry');
		
		$departments = Mage::helper('inquiry')->getDepartmentByCodeCstm($this->_inquiry->getDepartment());
		$this->_department = new Varien_Object();
        $this->_department->setData($departments);
		
        $backUrl = $this->getUrl('*/*/');
        $this->_addButton('back', array(
            'label'   => Mage::helper('inquiry')->__('Back'),
            'onclick' => "setLocation('{$backUrl}')",
            'class'   => 'back'
        ));      
		
		if ($this->_inquiry) {
            $this->_addButton('delete', array(
                'label'     => Mage::helper('adminhtml')->__('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getUrl('*/*/delete',array('id'=>$this->_inquiry->getId())) . '\')',
            ));
        }
    }


	public function getInquiry(){
		return $this->_inquiry;
	}

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('inquiry')->__("Inquiry For (%s) From [%s] | %s", $this->getDepartment()->getName(), $this->_inquiry->getEmailAddress(), $this->formatDate($this->_inquiry->getCreatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true));
    }
	
	public function getDepartment(){
		return $this->_department;
	}



    protected function _toHtml()
    {
    	/*
        $this->setTxnIdHtml($this->escapeHtml($this->_txn->getTxnId()));


        $this->setParentTxnIdHtml(
            $this->escapeHtml($this->_txn->getParentTxnId())
        );

        $this->setOrderIncrementIdHtml($this->escapeHtml($this->_txn->getOrder()->getIncrementId()));

        $this->setTxnTypeHtml($this->escapeHtml($this->_txn->getTxnType()));

        $this->setIsClosedHtml(
            ($this->_txn->getIsClosed()) ? Mage::helper('sales')->__('Yes') : Mage::helper('sales')->__('No')
        );

        $createdAt = (strtotime($this->_txn->getCreatedAt()))
            ? $this->formatDate($this->_txn->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
            : $this->__('N/A');
        $this->setCreatedAtHtml($this->escapeHtml($createdAt));
		
		*/

        return parent::_toHtml();
    }
}
