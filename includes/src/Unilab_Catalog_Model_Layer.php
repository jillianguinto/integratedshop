<?php 
class Unilab_Catalog_Model_Layer extends Mage_Catalog_Model_Layer
{  
	public function getUnilabFilterableAttributes()
    { 
        $setIds = $this->_getCategorySetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
	
	 /**
     * Get attribute sets identifiers of current product set
     *
     * @return array
     */
    protected function _getCategorySetIds()
    {
        $key = $this->getStateKey().'_SET_IDS';
        $setIds = $this->getAggregator()->getCacheData($key);

        if ($setIds === null) {
            $setIds = $this->getCategoryProductCollection()->getSetIds();
            $this->getAggregator()->saveCacheData($setIds, $key, $this->getStateTags());
        }

        return $setIds;
    }
	
	public function getCategoryProductCollection()
    { 
		$collection = $this->getCurrentCategory()->getProductCollection();
		$this->prepareProductCollection($collection); 

        return $collection;
    }


}
