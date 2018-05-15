<?php
require_once(Mage::getModuleDir('controllers','Mage_Wishlist').DS.'IndexController.php');
class Unilab_Wishlist_IndexController extends Mage_Wishlist_IndexController
{
   
    /**
     * Add the item to wish list
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    protected function _addItemToWishList()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');
	
        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }
		
		$requestParams = $this->getRequest()->getParams();
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {	   
		    
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.',
                $product->getName(), Mage::helper('core')->escapeUrl($referer));
            $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist.'));
        }

        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }
	
	
    /**
     * Remove item
     */
    public function removeAction()
    {
        $session = Mage::getSingleton('customer/session');
		
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);
        if (!$item->getId()) {
            return $this->norouteAction();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->norouteAction();
        }
        try {
            $item->delete();
            $wishlist->save();
			
			
            $message = $this->__('Successfully removed 1 wishlist item.');
            $session->addSuccess($message);
			
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist.')
            );
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
 
	 public function allcartAction()
    {
        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return ;
        }
		
      //  $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());
        //always keep items in wishlist 
		$isOwner    = false;

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart       = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

        $qtys = $this->getRequest()->getParam('qty');
        foreach ($collection as $item) {
            /** @var Mage_Wishlist_Model_Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }

            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            }
            catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
            );
        }
        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }

	
}
