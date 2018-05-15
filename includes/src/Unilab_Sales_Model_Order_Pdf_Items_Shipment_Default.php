<?php 
class Unilab_Sales_Model_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Shipment_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array(); 
		
        // draw SKU
        $lines[0] = array(array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 35,
        ));

        // draw Product name
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => 100,
        ); 
		
		$store_id = Mage::getModel('sales/order_item')->load($item->getOrderItemId())->getStoreId();	
		$product = Mage::getModel('catalog/product')->setStoreId($store_id)->load($item->getProductId());	 
        // draw Attribute 
 
		$lines[0][] = array(
			'text' => $product->getAttributeText('unilab_size'),
			'feed' => 350
		); 
		
        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 565, 
            'align' => 'right'
        );
 
        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
