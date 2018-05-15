<?php

class Gurutheme_Advancedquickorder_Helper_Data extends Mage_Core_Helper_Abstract {

    public function _getStoreConfig($path) {
        $_storeid = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig('advancedquickorder/advancedquickorder_' . $path, $_storeid);
    }
   
    public function _getProductOptions($_product, $_rowid, $method) {
        
        $result = '';
        if(!class_exists('Gurutheme_Advancedquickorder_AjaxController'))
        {
            require_once 'app/code/local/Gurutheme/Advancedquickorder/controllers/AjaxController.php';
        }
        
        $controller = new Gurutheme_Advancedquickorder_AjaxController(
                            Mage::app()->getRequest()->setActionName('loadproductoption'),
                            Mage::app()->getResponse()
                        );   
        
        Mage::unregister('rowid');
        Mage::register('rowid', $_rowid);
        
        $controller->loadLayout('advancedquickorder_ajax_loadproductoption');
        $layout = $controller->getLayout();

        if ($method == 'options') {
            
            $html = $layout
                ->getBlock('advancedquickorder.product.info.options')
                ->setRowId($_rowid)
                ->setProduct($_product)                
                ->toHtml();
            return $html;
        } 
        
        if ($method == 'configurable') {
            $html = $layout
                ->getBlock('advancedquickorder.product.info.configurable')
                ->setProduct($_product)
                ->setRowId($_rowid)
                ->toHtml();
            return $html;
        }
        return $result;
    }
    
    /*
     * 
     * 
     *

    public function _createConfigurableOptions($attributeOptions) {
        
        $result = '';
        foreach ($attributeOptions as $id => $o) {
            $result .= '<div class="option-item">';
            $result .= '<label><em>*&ensp;</em>' . $o['label'] . '</label>
                                            <ul id="options-' . $id . '-list" class="options-list"><li>
                                                <select name="super_attribute[' . $id . ']" class="quick-options sl-product-option">
                                            <option value="0">-- Please Select --</option>';
            $values = $o['options'];
            foreach ($values as $k => $v) {               
                $result .= '<option value="' . $k . '">' . $v . '</option>';
            }
            $result .= '</select></li></ul><div style="display:none;width:135px" class="validation-advice">This is a required field</div>';
            $result .= '</div>';
        }

        return $result;
        
        
        $pid = Mage::app()->getRequest()->getParam('productid');
        $rowid = Mage::app()->getRequest()->getParam('rowid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('advancedquickorder/catalog_product_view_type_configurable')
            ->setProduct($item)
            ->setRowId($rowid)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/type/options/configurable.phtml')
            ->toHtml();
        return $html;
    }
     * 
     * 
     */

    public function _getHandleProductCollection() {
        $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
        $collections = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addAttributeToFilter('visibility', $visibility)
                        ->addAttributeToFilter('product_suggestion', array('eq' => 1))
                        ->addAttributeToFilter('type_id', array(
                                                            'in' => array(
                                                                    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                                                                    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                                                            )
                                               ))
                        ->setOrder('name', 'asc')
                        ->setPageSize(Mage::getStoreConfig('advancedquickorder/advancedquickorder_suggestion/numberofproducts'))
                        ->setCurPage(1);
        return $collections;
    }

    public function _getBestSellerProductCollection() {
        $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
        $collections = Mage::getResourceModel('reports/product_collection')
                        ->setOrder('ordered_qty', 'desc');        
        $collections->getSelect()->where('order_items.store_id ='.Mage::app()->getStore()->getId());
        $collections->addAttributeToSelect('*')
                    ->addAttributeToFilter('visibility', $visibility)
                    ->addAttributeToFilter('type_id', array(
                                                        'in' => array(
                                                                Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                                                                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                                                        )
                                           ))
                    ->addOrderedQty()
                    ->setPageSize(Mage::getStoreConfig('advancedquickorder/advancedquickorder_suggestion/numberofproducts'))
                    ->setCurPage(1);
        return $collections;
    }
    
    public function _getMostViewedProductCollection(){
        
    }
    
    /*
     * 
     * commented by giappv 
     * version 2.1
     * 31-12-2013

    public function _getProductOptionsElement($o) {
        
        $optionType = $o->getType();
        
        switch ($optionType) {
            case 'drop_down': return $this->_createSelectOptions($o);
                break;
            case 'radio': return $this->_createSelectOptions($o);
                break;
            case 'checkbox': return $this->_createSelectOptions($o);
                break;
            case 'multiple': return $this->_createSelectOptions($o);
                break;
            case 'field': return $this->_createFieldOptions($o);
                break;
            case 'date_time': return $this->_createDateTimeOptions($o);
                break;
            case 'file': return $this->_createFileOptions($o);
                break;            
        }
    } 
     
    
    protected function _createDateTimeOptions($o)
    {
        $pid = Mage::app()->getRequest()->getParam('productid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('catalog/product_view_options_type_date')
            ->setOption($o)
            ->setProduct($item)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/options/type/date.phtml')
            ->toHtml();
        return $html;
    }
    
    protected function _createFileOptions($o)
    {
        $pid = Mage::app()->getRequest()->getParam('productid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('catalog/product_view_options_type_file')
            ->setOption($o)
            ->setProduct($item)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/options/type/file.phtml')
            ->toHtml();
        return $html;
    }
    
    protected function _createSelectOptions($o)
    {
        $pid = Mage::app()->getRequest()->getParam('productid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('advancedquickorder/catalog_product_view_options_type_select')
            ->setOption($o)
            ->setProduct($item)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/options/type/select.phtml')
            ->toHtml();
        return $html;
    }
    
    protected function _createFieldOptions($o)
    {
        $pid = Mage::app()->getRequest()->getParam('productid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('advancedquickorder/catalog_product_view_options_type_text')
            ->setOption($o)
            ->setProduct($item)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/options/type/text.phtml')
            ->toHtml();
        return $html;
    }

    public function _createDropdownOptions($o) {       
        $pid = Mage::app()->getRequest()->getParam('productid');
        $item = Mage::getModel('catalog/product')->load($pid);
        $layout = Mage::getSingleton('core/layout');
        $html = $layout
            ->createBlock('catalog/product_view_options_type_select')
            ->setOption($o)
            ->setProduct($item)
            ->setTemplate('gurutheme/advancedquickorder/catalog/product/view/options/type/select.phtml')
            ->toHtml();
        return $html;
    }
    
    public function _createMultipleOptions($o) {
        $result = '';
        $result .= '<div class="option-item">';
        $result .= '<label><em>*&ensp;</em>' . $o->getTitle() . '</label>
                                            <ul id="options-' . $o->getId() . '-list" class="options-list"><li>
                                                <select multiple="multiple" name="options[' . $o->getId() . '][]" class="quick-options multiselect sl-product-option">';
        $values = $o->getValues();
        foreach ($values as $k => $v) {
            $_data = $v->getData();
            if ($_data['price'] > 0) {
                $_price = ' +' . Mage::helper('core')->currency($_data['price']);
            } else {
                $_price = '';
            }
            $result .= '<option price="' . $_data['price'] . '" value="' . $_data['option_type_id'] . '">' . $_data['title'] . $_price . '</option>';
        }
        $result .= '</select></li></ul><div style="display:none;width:135px" class="validation-advice">This is a required field</div>';
        $result .= '</div>';
        return $result;
    }
     * 
     * 
     */

    public function _getTitle(){
        return Mage::getStoreConfig('advancedquickorder/advancedquickorder_general/title');
    }

    public function _getRoute(){
        return Mage::getStoreConfig('advancedquickorder/advancedquickorder_general/route');
    }
    
     

    public function buildCategoriesMultiselectValues(Varien_Data_Tree_Node $node, $values, $level = 0)
    {
        $level++;
        
        $values[$node->getId()]['value'] =  $node->getId();
        $values[$node->getId()]['label'] = str_repeat("--", $level) . $node->getName();
        
        foreach ($node->getChildren() as $child)
        {
            $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
        }
        
        return $values;
    }
    
    public function loadCategoryTree()
    {
        $tree = Mage::getResourceSingleton('catalog/category_tree')->load(); 
    
        $store = Mage::app()->getStore()->getId(); 
        $parentId = Mage::app()->getStore()->getRootCategoryId(); 
    
        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();
    
        $root = $tree->getNodeById($parentId);
        
        if($root && $root->getId() == 1)
        { 
            $root->setName(Mage::helper('catalog')->__('Root')); 
        }
        
        $collection = Mage::getModel('catalog/category')->getCollection() 
            ->setStoreId($store) 
            ->addAttributeToSelect('name') 
            ->addAttributeToSelect('is_active');
        
        $tree->addCollectionData($collection, true); 

        return $this->buildCategoriesMultiselectValues($root, array());    
    } 
    
    public function printCategoryTree($tree,$level) {
        $level ++;
        foreach($tree as $item) {
            echo '<option value="'.$item['value'].'">' . str_repeat("    ", $level).$item['label']." .</option>";
            $this->printCategoryTree($item['children'],$level);
        }
    }
    
    private function compareString($key, $product_name) {
        $lowerstr = strtolower($product_name);
        $pos = strpos($lowerstr, $key);
        $temp_search = substr($product_name, $pos, strlen($key));
        $result = str_replace($temp_search, '<em>' . $temp_search . '</em>', $product_name);
        return $result;
    }
    
    public function getProductHTML()
    {
        $result = '<div class="p-auto">';
        $numberOfrows = Mage::getStoreConfig('advancedquickorder/advancedquickorder_general/numberofrows', Mage::app()->getStore());
        $pId_byName = array();
        $pId_bySku = array();
        $pId_add = array();
        $product_code = Mage::app()->getRequest()->getParam('productcode');
        $catid = Mage::app()->getRequest()->getParam('catid');

        $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
        $product_model = Mage::getModel('catalog/product');
        
        $category = Mage::getModel('catalog/category')->load($catid);
        
        if($catid ==  Mage::app()->getStore()->getRootCategoryId())
        {
            $product_collection = $product_model->getCollection();
        } else {
            $product_collection = $category->getProductCollection();
        }
        
        $product_collection = $product_collection
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addAttributeToFilter('visibility', $visibility)
                        ->addAttributeToFilter('status', array('eq' => 1))
                        ->addAttributeToFilter('type_id', array(
                                                            'in' => array(
                                                                    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                                                                    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                                                            )
                                               ))
                        ->addAttributeToFilter('name', array('like' => '%' . $product_code . '%'));
        

        foreach ($product_collection as $name => $item) {
            $pId_byName[$name] = $item->getId();
        }
        
        if($catid ==  Mage::app()->getStore()->getRootCategoryId())
        {
            $product_collection = $product_model->getCollection();
        } else {
            $product_collection = $category->getProductCollection();
        }

        $product_collection = $product_collection
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addAttributeToFilter('visibility', $visibility)
                        ->addAttributeToFilter('status', array('eq' => 1))
                        ->addAttributeToFilter('type_id', array(
                                                            'in' => array(
                                                                    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                                                                    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                                                            )
                                               ))
                        ->addAttributeToFilter('sku', array('like' => '%' . $product_code . '%'));
        


        foreach ($product_collection as $sku => $item) {
            $pId_bySku[$sku] = $item->getId();
        }

        $countId = count($pId_byName);

        foreach ($pId_bySku as $id) {
            if (!in_array($id, $pId_byName)) {
                $pId_add[$countId] = $id;
                $countId++;
            }
        }

        $arr = array_merge($pId_byName, $pId_add);
        $products = $product_model->getCollection()
                        ->addStoreFilter(Mage::app()->getStore())
                        ->addfieldtofilter('entity_id', array('in' => $arr));
                        //->addAttributeToSort('name','desc');

        $i = 0;
        if ($products->count() > 0) {
            foreach ($products as $item) {
                $_product = Mage::getModel('catalog/product')->load($item->getId());
                $pname = $this->compareString($product_code, $_product->getName());

                $i == $numberOfrows ? $result .= '<div class="viewall"><span>View all</span></div>' : $result .= '';
                $i >= $numberOfrows ? $nodisplay = 'no-display' : $nodisplay = '';
                $i == 0 ? $current = 'current' : $current = '';
                $_product->getOptions() ? $hasoption = 'options' : $hasoption = '';
                $_product->isConfigurable() ? $configurable = 'configurable' : $configurable = '';
                
                $pricemodel = Mage::getModel('catalog/product_type_price');
                $unitprice = Mage::helper("core")->currency($pricemodel->getFinalPrice(1, $_product), true, false);

                $result .= '<div class="p-item ' . $current . ' ' . $nodisplay . ' ' . $hasoption . ' ' . $configurable . '">
                                <img width="50" height="50" src="' . Mage::helper('catalog/image')->init($_product, 'small_image')->resize(50) . '" />
                                <span class="name"><label>' . $pname . '</label></span>
                                <span>SKU#: <label class="sku">' . $_product->getSku() . '</label></span>
                                <input type="hidden" class="pprice" value="' . $unitprice . '" />
                                <input type="hidden" class="pid" value="product-' . $i . '" />
                                <input type="hidden" class="prodid" value="' . $item->getId() . '" />
                            </div>';
                $i++;
            }
        } else {
            $result .= '<label style="padding-left:6px">No results found</label>';
        }

        $result .= '</div>';
        return $result;
    }
    
    
    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getOptionsPriceJsonConfig($_product)
    {
        $config = array();
        
        if (!$_product->getTypeInstance(true)->hasOptions($_product)) {
            return Mage::helper('core')->jsonEncode($config);
        }
        
        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $_product;
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
            'rowId'               => Mage::registry('rowid'),
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }
        return Mage::helper('core')->jsonEncode($config);
    }
    
    function productHasOptions($_product)
    {
         if ($_product->getTypeInstance(true)->hasOptions($_product)) {
            return true;
        }
        return false;
    }
    
    public function currencyByStore($value, $store = null, $format = true, $includeContainer = true)
    {
        $core_helper = Mage::helper('core');
        if(method_exists($core_helper, 'currencyByStore'))
        {
            return $core_helper->currencyByStore($value, $store, $format, $includeContainer);
        } 
        
        try {
            if (!($store instanceof Mage_Core_Model_Store)) {
                $store = Mage::app()->getStore($store);
            }

            $value = $store->convertPrice($value, $format, $includeContainer);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }

        return $value;
    }
    
}

?>