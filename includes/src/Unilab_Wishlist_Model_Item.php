<?php

class Unilab_Wishlist_Model_Item extends Mage_Wishlist_Model_Item
{
    
    /**
     * Add or Move item product to shopping cart
     *
     * Return true if product was successful added or exception with code
     * Return false for disabled or unvisible products
     *
     * @throws Mage_Core_Exception
     * @param Mage_Checkout_Model_Cart $cart
     * @param bool $delete  delete the item after successful add to cart
     * @return bool
     */
    public function addToCart(Mage_Checkout_Model_Cart $cart, $delete = false)
    {
        $product = $this->getProduct();
        $storeId = $this->getStoreId();

        if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getStoreId() == $storeId) {
                return false;
            }
        }
		
        if (!$product->isSalable()) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_NOT_SALABLE);
        }

		// @added by: diszo.sasil (2014-01-16)
		// To get the actual simple product from BuyRequest
        $buyRequest = $this->getBuyRequest();
		$curBuyRequest = $buyRequest->getData();			
		if(isset($curBuyRequest['actual_product']) && !empty($curBuyRequest['actual_product'])){
			$curBuyRequest['product'] = $curBuyRequest['actual_product'];
			$varienObject = new Varien_Object();
    		$varienObject->setData($curBuyRequest);
			$buyRequest = $varienObject;
			
			$product = Mage::getModel('catalog/product')
			                ->setStoreId($this->getStoreId())
			                ->load($curBuyRequest['actual_product']);
			
		}
		// End
		
        $cart->addProduct($product, $buyRequest);
        if (!$product->isVisibleInSiteVisibility()) {
            $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
        }

        if ($delete) {
            $this->delete();
        }

        return true;
    }

   
}
