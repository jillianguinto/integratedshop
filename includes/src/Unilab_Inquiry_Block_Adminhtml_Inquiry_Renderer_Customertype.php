<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diszo.sasil
 * Date: 11/7/12
 * Time: 2:02 PM
 * To change this template use File | Settings | File Templates.
 */

class Unilab_Inquiry_Block_Adminhtml_Inquiry_Renderer_Customertype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

    public function render(Varien_Object $row)
    {	
		if($row->getData($this->getColumn()->getIndex()) == 0) {
			return '<span style="color:red;font-weight:bold">GUEST</span>';
		}else{
			return '<span style="color:blue;font-weight:bold">CUSTOMER</span>';
		}	
    }
}
