<?php

class Magestore_Inventory_Block_Adminhtml_Inventory_Renderer_Supplier extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $supplier_products = Mage::getModel('inventory/supplierproduct')
            ->getCollection()
            ->addFieldToFilter('product_id',$product_id);
		$content = '';
		$check = 0;//zend_debug::dump($this->getRequest()->getActionName());die();
        foreach($supplier_products as $supplier_product){
            $supplier_id = $supplier_product->getSupplierId();
            $url = Mage::helper('adminhtml')->getUrl('inventoryadmin/adminhtml_supplier/edit',array('id'=>$supplier_id,'inventory'=>true));
            $supplier = Mage::getModel('inventory/supplier')
                ->getCollection()
                ->addFieldToFilter('supplier_id',$supplier_id)
                ->getFirstItem();
            $name = $supplier->getName();
			if(in_array(Mage::app()->getRequest()->getActionName(),array('exportCsv','exportXml','exportCsvProductInfo','exportXmlProductInfo')))
			{
				if($check)
				$content.=', '.$name;
				else
				$content.=$name;
			}
			else
            $content .= "<a href=".$url.">$name;<a/>"."<br/>";
			$check++;
        }
        return $content;
    }

}

?>
