<?php
/* @reference   - http://www.ayasoftware.com/how-import-configurable-products-csv-file-magento-system
 * @description - Refactored Product Importer Class  
 * @developer   - Jerick.duguran@gmail.com - Movent, Inc.
 * @date 		- December 06, 2013
 */
class Unilab_Catalog_Model_Convert_Adapter_Productconfigurableimporter extends Mage_Catalog_Model_Convert_Adapter_Product {
     
	
    protected $_categoryCache = array ();
	
	
   /**
     * @desc  Save product (import) 
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData) {
	
        $product = $this->getProductModel ();
        $product->setData ( array () );
        if ($stockItem = $product->getStockItem ()){
            $stockItem->setData ( array () );
        }
        $multiplier = 1;
		
		if(isset($importData['unilab_moq'])){
			$multiplier = (int) $importData['unilab_moq'];
		} 
		
        if (empty ( $importData ['store'] )) {
            if (! is_null ( $this->getBatchParams ( 'store' ) )) {
                $store = $this->getStoreById ( $this->getBatchParams ( 'store' ) );
            } else {
                $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not defined', 'store' );
                Mage::throwException ( $message );
            }
        } else {
            $store = $this->getStoreByCode ( $importData ['store'] );
        }
         
        if ($store === false) {
            $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, store "%s" field not exists', $importData ['store'] );
            Mage::throwException ( $message );
        }
        if (empty ( $importData ['sku'] )) {
            $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not defined', 'sku' );
            Mage::throwException ( $message );
        }
		
		if (empty($importData['unilab_unit_price']) && $importData ['type'] == 'simple') {
            $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not defined', 'unilab_baseprice' );
            Mage::throwException ( $message );
        }
		
        $product->setStoreId ( $store->getId () );
        $productId = $product->getIdBySku ( $importData ['sku'] );
        $new = true; // fix for duplicating attributes error
        if ($productId) {
            $product->load ( $productId );
            $new = false; // fix for duplicating attributes error
        }
        $productTypes = $this->getProductTypes ();
        $productAttributeSets = $this->getProductAttributeSets ();
         
        /**
         * Check product define type
         */
         
        if (empty ( $importData ['type'] ) || ! isset ( $productTypes [strtolower ( $importData ['type'] )] )) {
            $value = isset ( $importData ['type'] ) ? $importData ['type'] : '';
            echo $value;
            $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'type' );
            Mage::throwException ( $message );
        }
        $product->setTypeId ( $productTypes [strtolower ( $importData ['type'] )] );
        /**
         * Check product define attribute set
         */
        if (empty ( $importData ['attribute_set'] ) || ! isset ( $productAttributeSets [$importData ['attribute_set']] )) {
            $value = isset ( $importData ['attribute_set'] ) ? $importData ['attribute_set'] : '';
            $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set' );
            Mage::throwException ( $message );
        }
        $product->setAttributeSetId ( $productAttributeSets [$importData ['attribute_set']] );
         
        foreach ( $this->_requiredFields as $field ) {
            $attribute = $this->getAttribute ( $field );
            if (! isset ( $importData [$field] ) && $attribute && $attribute->getIsRequired ()) {
                $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" for new products not defined', $field );
                Mage::throwException ( $message );
            }
        }
         
        //================================================
        // this part handles configurable products and links
        //================================================         
 
        if ($importData ['type'] == 'configurable') {
            $product->setCanSaveConfigurableAttributes ( true );
            $configAttributeCodes = $this->userCSVDataAsArray ( $importData ['config_attributes'] );
            $usingAttributeIds = array ();
            foreach ( $configAttributeCodes as $attributeCode ) {
                $attribute = $product->getResource ()->getAttribute ( $attributeCode );
                if ($product->getTypeInstance ()->canUseAttribute ( $attribute )) {
                    if ($new) { // fix for duplicating attributes error
                        $usingAttributeIds [] = $attribute->getAttributeId ();
                    }
                }
            }
            if (! empty ( $usingAttributeIds )) {
                $product->getTypeInstance ()->setUsedProductAttributeIds ( $usingAttributeIds );
                $product->setConfigurableAttributesData ( $product->getTypeInstance ()->getConfigurableAttributesAsArray () );
                $product->setCanSaveConfigurableAttributes ( true );
                $product->setCanSaveCustomOptions ( true );
            }
            if (isset ( $importData ['associated'] )) {
                $product->setConfigurableProductsData ( $this->skusToIds ( $importData ['associated'], $product ) );
            }
        }
         
        /**
         * Init product links data (related, upsell, crosssell, grouped)
         */
        if (isset ( $importData ['related'] )) {
            $linkIds = $this->skusToIds ( $importData ['related'], $product );
            if (! empty ( $linkIds )) {
                $product->setRelatedLinkData ( $linkIds );
            }
        }
        if (isset ( $importData ['upsell'] )) {
            $linkIds = $this->skusToIds ( $importData ['upsell'], $product );
            if (! empty ( $linkIds )) {
                $product->setUpSellLinkData ( $linkIds );
            }
        }
        if (isset ( $importData ['crosssell'] )) {
            $linkIds = $this->skusToIds ( $importData ['crosssell'], $product );
            if (! empty ( $linkIds )) {
                $product->setCrossSellLinkData ( $linkIds );
            }
        }
        if (isset ( $importData ['grouped'] )) {
            $linkIds = $this->skusToIds ( $importData ['grouped'], $product );
            if (! empty ( $linkIds )) {
                $product->setGroupedLinkData ( $linkIds );
            }
        }
         
        //================================================ 
        if (isset ( $importData ['category_ids'] )) {
            $product->setCategoryIds ( $importData ['category_ids'] );
        }
        //// Para importar categorias
        if (isset ( $importData ['categories'] )) {
            $categoryIds = $this->_addCategories ( $importData ['categories'], $store );
            if ($categoryIds) {
                $product->setCategoryIds ( $categoryIds );
            }
        }
        ////
        foreach ( $this->_ignoreFields as $field ) {
            if (isset ( $importData [$field] )) {
                unset ( $importData [$field] );
            }
        }
         
        if ($store->getId () != 0) {
            $websiteIds = $product->getWebsiteIds ();
            if (! is_array ( $websiteIds )) {
                $websiteIds = array ();
            }
            if (! in_array ( $store->getWebsiteId (), $websiteIds )) {
                $websiteIds [] = $store->getWebsiteId ();
            }
            $product->setWebsiteIds ( $websiteIds );
        }
         
        if (isset ( $importData ['websites'] )) {
            $websiteIds = $product->getWebsiteIds ();
            if (! is_array ( $websiteIds )) {
                $websiteIds = array ();
            }
            $websiteCodes = explode( ',', $importData ['websites'] );
            foreach ( $websiteCodes as $websiteCode ) {
                try {
                    $website = Mage::app ()->getWebsite ( trim ( $websiteCode ) );
                    if (! in_array ( $website->getId (), $websiteIds )) {
                        $websiteIds [] = $website->getId ();
                    }
                } catch ( Exception $e ) {
                }
            }
            $product->setWebsiteIds ( $websiteIds );
            unset ( $websiteIds );
        }
         
        foreach ( $importData as $field => $value ) {
            // if (in_array($field, $this->_inventorySimpleFields)) {works only for old version )
            if (in_array ( $field, $this->_inventoryFields )) {
                continue;
            }
            if (in_array ( $field, $this->_imageFields )) {
                continue;
            }
             
            $attribute = $this->getAttribute ( $field );
            if (! $attribute) {
                continue;
            }
             
            $isArray = false;
            $setValue = $value;
             
            if ($attribute->getFrontendInput () == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value );
                $isArray = true;
                $setValue = array ();
            } 
            if ($value && $attribute->getBackendType () == 'decimal') {
				if($field == 'price' && $importData['product_type_id'] == 'configurable'){  
					$setValue = $this->getNumber((string) ($value));
				}elseif($field == 'unilab_unit_price' && $importData['product_type_id'] == 'simple'){  
					$setValue = $this->getNumber((string) ($value)); 
					$product->setData('price', $this->getNumber((string) ($value * $multiplier)));
				}else{
					$setValue = $this->getNumber($value);
				}	 
            }
             
            if ($value && $attribute->getBackendType () == 'datetime') {
                $setValue= date('Y-m-j', strtotime($value));
            }
             
             
            if ($attribute->usesSource ()) {
                $options = $attribute->getSource ()->getAllOptions ( false );
                 
                if ($isArray) {
                    foreach ( $options as $item ) {
                        if (in_array ( $item ['label'], $value )) {
                            $setValue [] = $item ['value'];
                        }
                    }
                } else {
                    $setValue = null;
                    foreach ( $options as $item ) {
                        if ($item ['label'] == $value) {
                            $setValue = $item ['value'];
                        }
                    }
                }
            }
            $product->setData ($field, $setValue );
        }
        if (! $product->getVisibility ()) {
            $product->setVisibility ( Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE );
        }
         
        $stockData = array ();
        //$inventoryFields = $product->getTypeId() == 'simple' ? $this->_inventorySimpleFields : $this->_inventoryOtherFields;
        $inventoryFields = isset ( $this->_inventoryFieldsProductTypes [$product->getTypeId ()] ) ? $this->_inventoryFieldsProductTypes [$product->getTypeId ()] : array ();
        foreach ( $inventoryFields as $field ) {
            if (isset ( $importData [$field] )) {
                if (in_array ( $field, $this->_toNumber )) {
                    $stockData [$field] = $this->getNumber ( $importData [$field] );
                } else {
                    $stockData [$field] = $importData [$field];
                }
            }
        }
        $product->setStockData ($stockData);
         
        $imageData = array ();
        foreach ( $this->_imageFields as $field ) {
            if (! empty ( $importData [$field] ) && $importData [$field] != 'no_selection') {
                if (! isset ( $imageData [$importData [$field]] )) {
                    $imageData [$importData [$field]] = array ();
                }
                $imageData [$importData [$field]] [] = $field;
            }
        }         
        foreach ( $imageData as $file => $fields ) {
				try {
					$product->addImageToMediaGallery ( Mage::getBaseDir ( 'media' ) . DS . 'import' . $file, $fields );
				}catch ( Exception $e )
				{ }
        }
		
		//IMPORT MEDIA IMAES TOO - FROM BASE 
        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();
        $arrayToMassAdd			  = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }		
         
        $product->setIsMassupdate ( true );
        $product->setExcludeUrlRewrite ( true );
         
        $product->save ();
         
        return true;
    }
     
    protected function userCSVDataAsArray($data) {
        return explode(',', str_replace ( " ", "", $data ) );
    }
     
    protected function skusToIds($userData, $product) {
        $productIds = array ();
        foreach ( $this->userCSVDataAsArray ( $userData ) as $oneSku ) {
            if (($a_sku = ( int ) $product->getIdBySku ( $oneSku )) > 0) {
                parse_str( "position=", $productIds [$a_sku]);
            }
        }
        return $productIds;
    }
    
	//CATEGORY IMPORTER	
    protected function _addCategories($categories, $store) {
        $rootId = $store->getRootCategoryId ();
        if (! $rootId) {
            return array ();
        }
        $rootPath = '1/' . $rootId;
        if (empty ( $this->_categoryCache [$store->getId ()] )) {
            $collection = Mage::getModel ( 'catalog/category' )->getCollection ()->setStore ( $store )->addAttributeToSelect ( 'name' );
            $collection->getSelect ()->where ( "path like '" . $rootPath . "/%'" );
             
            foreach ( $collection as $cat ) {
                $pathArr = explode ( '/', $cat->getPath () );
                $namePath = '';
                for($i = 2, $l = sizeof ( $pathArr ); $i < $l; $i ++) {
                    $name = $collection->getItemById ( $pathArr [$i] )->getName ();
                    $namePath .= (empty ( $namePath ) ? '' : '/') . trim ( $name );
                }
                $cat->setNamePath ( $namePath );
            }
             
            $cache = array ();
            foreach ( $collection as $cat ) {
                $cache [strtolower ( $cat->getNamePath () )] = $cat;
                $cat->unsNamePath ();
            }
            $this->_categoryCache [$store->getId ()] = $cache;
        }
        $cache = & $this->_categoryCache [$store->getId ()];
         
        $catIds = array ();
        foreach ( explode (',', $categories ) as $categoryPathStr ) {
            $categoryPathStr = preg_replace ( '#s*/s*#', '/', trim ( $categoryPathStr ) );
            if (! empty ( $cache [$categoryPathStr] )) {
                $catIds [] = $cache [$categoryPathStr]->getId ();
                continue;
            }
			
            $path = $rootPath;
            $namePath = '';
            foreach ( explode ( '/', $categoryPathStr ) as $catName ) {
                $namePath .= (empty ( $namePath ) ? '' : '/') . strtolower ( $catName );
                if (empty ( $cache [$namePath] )) {
                    $cat = Mage::getModel ( 'catalog/category' )->setStoreId ( $store->getId () )->setPath ( $path )->setName ( $catName )->// comment out the following line if new categories should stay inactive
                    setIsActive ( 1 )->save ();
                    $cache [$namePath] = $cat;
                }
                $catId = $cache [$namePath]->getId ();
                $path .= '/' . $catId;
            }
            if ($catId) {
                $catIds [] = $catId;
            }
        }
        return implode(',', $catIds );
    } 
} 