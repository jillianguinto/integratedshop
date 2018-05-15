<?php
class OrganicInternet_SimpleConfigurableProducts_Sales_Model_Quote
    extends Mage_Sales_Model_Quote
{
    /**
     * Updates quote item with new configuration
     *
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Mage_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file options (file inputs), so they won't
     *   intersect with other submitted options
     *
     * For more options see Mage_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int $itemId
     * @param Varien_Object $buyRequest
     * @param null|array|Varien_Object $params
     * @return Mage_Sales_Model_Quote_Item
     *
     * @see Mage_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItemById($itemId);
        if (!$item) {
            Mage::throwException(Mage::helper('sales')->__('Wrong quote item id to update configuration.'));
        }
        $productId = $item->getProduct()->getId();
 
        /* Stacey - EDIT for SCP Edit button in cart
         * For a config product, SCP only has one item in sales_flat_quote_item (the simple product) 
         * rather than 2 as default Magento (parent and child). So, during edit,
         * swap quote to use newly selected associated simple product.
         */
        //Mage::log("Edit for SCP");
        if ($buyRequest->getProduct() != $productId) {               
            $productId = $buyRequest->getProduct();
        }
        /* end EDIT for SCP */
 
        //We need to create new clear product instance with same $productId
        //to set new option values from $buyRequest
        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getStore()->getId())
            ->load($productId);
 
        if (!$params) {
            $params = new Varien_Object();
        } else if (is_array($params)) {
            $params = new Varien_Object($params);
        }
        $params->setCurrentConfig($item->getBuyRequest());
        $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);
 
        $buyRequest->setResetCount(true);
        $resultItem = $this->addProduct($product, $buyRequest);
 
        if (is_string($resultItem)) {
            Mage::throwException($resultItem);
        }
 
        if ($resultItem->getParentItem()) {
            $resultItem = $resultItem->getParentItem();
        }
 
        if ($resultItem->getId() != $itemId) {
            //Mage::log("here -- resultItem not itemId");
             
            if($item->getPrescriptionId() != ''){
				$resultItem->getPrescriptionId($item->getPrescriptionId());
			}
            
			/*
             * Product configuration didn't stick to original quote item
             * It either has same configuration as some other quote item's product or completely new configuration
             */
            $this->removeItem($itemId);
			
            $items = $this->getAllItems();
            foreach ($items as $item) {
                if (($item->getProductId() == $productId) && ($item->getId() != $resultItem->getId())) {
                    if ($resultItem->compare($item)) {
                        // Product configuration is same as in other quote item
                        $resultItem->setQty($resultItem->getQty() + $item->getQty());
                        $this->removeItem($item->getId());
                        break;
                    }
                }
            }
        } else {
            $resultItem->setQty($buyRequest->getQty());
        }
 
        return $resultItem;
    }
}