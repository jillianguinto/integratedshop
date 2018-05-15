<?php
/**
 * @category
 * @package
 * @author Gabriel Féron <gabriel.feron@lepape.com>
 */
class Lepape_CatalogRule_Model_CatalogRule_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     * Get attribute value
     *
     * Bugfix: When we are dealing with a global scope attribute, _entityAttributeValues is not set
     *         That's expected, see the parent class comments) but we don't want to use array()
     *         when dealing with a global attribute, because this replaces every 
     *         globally-scoped attribute value with an empty one!
     *         Instead, we use the current attribute value (which is already scoped).
     *
     *         This seems redundant but it's the less intrusive way to fix what seems to be a shitty bug.
     *
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _getAttributeValue($object)
    {
        $storeId = $object->getStoreId();
        $defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
        $productValues = isset($this->_entityAttributeValues[$object->getId()]) ? $this->_entityAttributeValues[$object->getId()] : array($defaultStoreId => $object->getData($this->getAttribute()));
        $defaultValue = isset($productValues[$defaultStoreId]) ? $productValues[$defaultStoreId] : null;
        $value = isset($productValues[$storeId]) ? $productValues[$storeId] : $defaultValue;

        $value = $this->_prepareDatetimeValue($value, $object);
        $value = $this->_prepareMultiselectValue($value, $object);
        return $value;
    }
}
