<?php


class Unilab_Catalog_Helper_Product extends Mage_Catalog_Helper_Product
{
 	/**
     * Inits product to be used for product controller actions and layouts
     * $params can have following data:
     *   'category_id' - id of category to check and append to product as current.
     *     If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $productId
     * @param Mage_Core_Controller_Front_Action $controller
     * @param Varien_Object $params
     *
     * @return false|Mage_Catalog_Model_Product
     */
    public function initProduct($productId, $controller, $params = null)
    {
        // Prepare data for routine
        if (!$params) {
            $params = new Varien_Object();
        }

        // Init and load product
        Mage::dispatchEvent('catalog_controller_product_init_before', array(
            'controller_action' => $controller,
            'params' => $params,
        ));

        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

		/*
        if (!$this->canShow($product)) {
            return false;
        }
		*/
		if (!$this->canShow($product)) {          
     		// Mage::log(__METHOD__ . ": Can't show " . $productId . ", get parent.");
		     if($product->getTypeId() == 'simple') {
		        $parent = Mage::getModel('catalog/product_type_configurable');
		        $parentIdsArray = $parent->getParentIdsByChild( $product->getId() );
		                 
		        // Fix by Stacey 10-7-2013. Do not assume configurable product.
		        if (!empty($parentIdsArray)) {
		           if ($parentIdsArray[0]) {
		                          
		            $childProduct = $product;
		            $product = Mage::getModel('catalog/product')
		                ->setStoreId(Mage::app()->getStore()->getId())
		                ->load($parentIdsArray[0]);
		           }
		        } else {
		            return false;
		        }
		    } else {
		        return false;
		    }
		}
		
		
		
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }

        // Load product current category
        $categoryId = $params->getCategoryId();
        if (!$categoryId && ($categoryId !== false)) {
            $lastId = Mage::getSingleton('catalog/session')->getLastVisitedCategoryId();
            if ($product->canBeShowInCategory($lastId)) {
                $categoryId = $lastId;
            }
        } elseif (!$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
        }

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
        }

        // Register current data and dispatch final events
        Mage::register('current_product', $product);
        Mage::register('product', $product);

        try {
            Mage::dispatchEvent('catalog_controller_product_init', array('product' => $product));
            Mage::dispatchEvent('catalog_controller_product_init_after',
                            array('product' => $product,
                                'controller_action' => $controller
                            )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $product;
    }
	
	public function getChildProducts($product, $checkSalable=true)
    {
        static $childrenCache = array();
        $cacheKey = $product->getId() . ':' . $checkSalable;

        if (isset($childrenCache[$cacheKey])) {
            return $childrenCache[$cacheKey];
        }

		// Updated by: diszo.sasil (2013-07-16) - Compatibility of Magento 1.7.0.2
		$conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
		$childProducts = $conf->getUsedProductCollection()->addAttributeToSelect(array('price', 'special_price', 'status', 'special_from_date', 'special_to_date'));
        
		//$childProducts = $product->getTypeInstance(true)->getUsedProductCollection($product);
        //$childProducts->addAttr	ibuteToSelect(array('price', 'special_price', 'status', 'special_from_date', 'special_to_date'));

        if ($checkSalable) {
            $salableChildProducts = array();
            foreach($childProducts as $childProduct) {
                if($childProduct->isSalable()) {
                    $salableChildProducts[] = $childProduct;
                }
            }
            $childProducts = $salableChildProducts;
        }

        $childrenCache[$cacheKey] = $childProducts;
        return $childProducts;
    }

	public function getChildProductWithLowestPrice($product, $priceType, $checkSalable=true)
    {
        $childProducts = $this->getChildProducts($product, $checkSalable);
        if (count($childProducts) == 0) { #If config product has no children
            return false;
        }
        $minPrice = PHP_INT_MAX;
        $minProd = false;
        foreach($childProducts as $childProduct) {
            if ($priceType == "finalPrice") {
                $thisPrice = $childProduct->getFinalPrice();
            } else {
                $thisPrice = $childProduct->getPrice();
            }
            if($thisPrice < $minPrice) {
                $minPrice = $thisPrice;
                $minProd = $childProduct;
            }
        } 
        return $minProd;
    }
}