<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diszo.sasil
 * Date: 11/7/12
 * Time: 2:02 PM
 * To change this template use File | Settings | File Templates.
 */

class Unilab_Inquiry_Block_Adminhtml_Inquiry_Renderer_Department extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

    public function render(Varien_Object $row)
    {	
        //$data = sprintf("%s (%s)",$row->getData($this->getColumn()->getIndex()),$row->getData('department_email'));
		$data = Mage::helper('inquiry')->getDepartmentByCodeCstm($row->getData($this->getColumn()->getIndex()),false);
		return $data['name'];
    }
}
