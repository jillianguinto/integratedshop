<?php

class Gurutheme_Advancedquickorder_Model_Adminhtml_Observer {

    public function addColumn(Mage_Adminhtml_Block_Catalog_Product_Grid $block) {
        $block->addColumnAfter('product_suggestion', array(
            'header' => Mage::helper('advancedquickorder')->__('Suggestion'),
            'index' => 'product_suggestion',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('advancedquickorder')->__('No'),
                1 => Mage::helper('advancedquickorder')->__('Yes'),
            ),
        ));
        $block->addColumnsOrder('product_suggestion', 'sku')
            ->sortColumnsByOrder();
        if ($block->getCollection())
            $block->getCollection()->addAttributeToSelect('product_suggestion');

        $filter = $block->getParam($block->getVarNameFilter(), null);
        $column = $block->getColumn('product_suggestion');
        if (is_string($filter)) {
            $filter = $block->helper('adminhtml')->prepareFilterString($filter);
        } else if ($filter && is_array($filter)) {

        } else if (0 !== sizeof($this->_defaultFilter)) {
            $filter = $this->_defaultFilter;
        }

        if (isset($filter ['product_suggestion']) && (!empty($filter ['product_suggestion']) || strlen($filter ['product_suggestion']) > 0) && $column->getFilter()) {

            $column->getFilter()->setValue($filter ['product_suggestion']);

            if ($block->getCollection()) {
                $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
                if ($column->getFilterConditionCallback()) {
                    call_user_func($column->getFilterConditionCallback(), $block->getCollection(), $column);
                } else {
                    $cond = $column->getFilter()->getCondition();
                    if ($field && isset($cond)) {
                        $block->getCollection()->addFieldToFilter($field, $cond);
                    }
                }
            }
        }
    }

    public function onEavLoadBefore(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();
        if (!isset($collection))
            return;

        if (is_a($collection, 'Mage_Catalog_Model_Resource_Product_Collection')) {

            if (($productListBlock = Mage::app ()->getLayout()->getBlock('products_list')) != false && ($productListBlock instanceof Mage_Adminhtml_Block_Catalog_Product)) {
                $this->addColumn($productListBlock->getChild('grid'));
            } else if (($block = Mage::app ()->getLayout()->getBlock('admin.product.grid')) != false) {
                $this->addColumn($block);
            }
        }
    }

    public function onPrepareMassactionUpdateSuggestion($observer) {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
                && $block->getRequest()->getControllerName() == 'catalog_product') {
            $block->addItem('suggestion', array(
                'label' => 'Update Suggestion',
                'url' => Mage::app()->getStore()->getUrl('advancedquickorder/adminhtml_index/massUpdateSuggestion'),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'suggestion',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('advancedquickorder')->__('Suggestion'),
                        'values' => array(
                            0   => Mage::helper('adminhtml')->__('No'),
                            1   => Mage::helper('adminhtml')->__('Yes')
                        )
                    )
                )
            ));
        }
    }

}

?>
