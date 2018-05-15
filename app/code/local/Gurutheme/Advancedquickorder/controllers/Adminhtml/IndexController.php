<?php

class Gurutheme_Advancedquickorder_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    public function massUpdateSuggestionAction(){
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $suggestion_status = $this->getRequest()->getParam('suggestion');
                    $product->setProductSuggestion($suggestion_status)->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully updated', count($productIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/catalog_product/index');
    }

}

?>
