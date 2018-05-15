<?php 

class Unilab_Catalog_Model_Convert_Adapter_Productpriceupdater
extends Mage_Catalog_Model_Convert_Adapter_Product
{  
    const ENTITY            = 'catalog_product';
    protected $_eventPrefix = 'catalog_product_priceupdate';
	
	public function saveRow(array $importData)
    {  
		$importData = array_change_key_case($importData, CASE_LOWER);
		
		if (empty($importData['moq'])) {
            $message = Mage::helper('catalog')->__('Skipping update row, required field "%s" is not defined.', 'MOQ');
            Mage::throwException($message);
        }
		
		if (empty($importData['unitprice'])) {
            $message = Mage::helper('catalog')->__('Skipping update row, required field "%s" is not defined.', 'UNITPRICE');
            Mage::throwException($message);
        }
		
		if($importData['moq'] < 1){
			$multiplier = 1;
		}else{
			$multiplier = $importData['moq'];
		}
		
		$importData['unilab_moq'] = $multiplier;
         		
		
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping update row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping update row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        } 
		 

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping update row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId){
            $product->load($productId);
        } else {
            $message = Mage::helper('catalog')->__('Skipping update row, product with sku "%s" was not found.', $importData['sku']);
            Mage::throwException($message);
        }

        $this->setProductTypeInstance($product); 

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        } 
		$importData['unilab_unit_price'] = $importData['unitprice'];
		 
        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
				if($field == 'unilab_unit_price'){  
					$setValue = $this->getNumber((string) ($value));
					$product->setData('price', $this->getNumber((string) ($value * $multiplier)));
				}else{
					$setValue = $this->getNumber($value);
				}	
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }  
            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData); 

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }
	
	public function finish()
    { 
        Mage::dispatchEvent($this->_eventPrefix . '_after', array());

        $entity = new Varien_Object();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $entity, self::ENTITY, Mage_Catalog_Model_Product_Indexer_Price::EVENT_TYPE_REINDEX_PRICE
        );
    }
}