<?php

/**

 * Magento

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the Open Software License (OSL 3.0)

 * that is bundled with this package in the file LICENSE.txt.

 * It is also available through the world-wide-web at this URL:

 * http://opensource.org/licenses/osl-3.0.php

 * If you did not receive a copy of the license and are unable to

 * obtain it through the world-wide-web, please send an email

 * to license@magentocommerce.com so we can send you a copy immediately.

 *

 * DISCLAIMER

 *

 * Do not edit or add to this file if you wish to upgrade Magento to newer

 * versions in the future. If you wish to customize Magento for your

 * needs please refer to http://www.magentocommerce.com for more information.

 *

 * @category    Mage

 * @package     Mage_Checkout

 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */



/**

 * Onepage controller for checkout

 *

 * @category    Mage

 * @package     Mage_Checkout

 * @author      Magento Core Team <core@magentocommerce.com>

 */

class Mage_Checkout_OnepageController extends Mage_Checkout_Controller_Action

{

    /**

     * List of functions for section update

     *

     * @var array

     */

    protected $_sectionUpdateFunctions = array(

        'payment-method'  => '_getPaymentMethodsHtml',

        'shipping-method' => '_getShippingMethodsHtml',

        'review'          => '_getReviewHtml',

    );



    /**

     * @var Mage_Sales_Model_Order

     */

    protected $_order;



    /**

     * Predispatch: should set layout area

     *

     * @return Mage_Checkout_OnepageController

     */

    public function preDispatch()

    {

        parent::preDispatch();

        $this->_preDispatchValidateCustomer();



        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();

        if ($checkoutSessionQuote->getIsMultiShipping()) {

            $checkoutSessionQuote->setIsMultiShipping(false);

            $checkoutSessionQuote->removeAllAddresses();

        }



        if (!$this->_canShowForUnregisteredUsers()) {

            $this->norouteAction();

            $this->setFlag('',self::FLAG_NO_DISPATCH,true);

            return;

        }



        return $this;

    }



    /**

     * Send Ajax redirect response

     *

     * @return Mage_Checkout_OnepageController

     */

    protected function _ajaxRedirectResponse()

    {

        $this->getResponse()

            ->setHeader('HTTP/1.1', '403 Session Expired')

            ->setHeader('Login-Required', 'true')

            ->sendResponse();

        return $this;

    }



    /**

     * Validate ajax request and redirect on failure

     *

     * @return bool

     */

    protected function _expireAjax()

    {

        if (!$this->getOnepage()->getQuote()->hasItems()

            || $this->getOnepage()->getQuote()->getHasError()

            || $this->getOnepage()->getQuote()->getIsMultiShipping()

        ) {

            $this->_ajaxRedirectResponse();

            return true;

        }

        $action = $this->getRequest()->getActionName();

        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)

            && !in_array($action, array('index', 'progress'))

        ) {

            $this->_ajaxRedirectResponse();

            return true;

        }

        return false;

    }



    /**

     * Get shipping method step html

     *

     * @return string

     */

    protected function _getShippingMethodsHtml()

    {

        $layout = $this->getLayout();

        $update = $layout->getUpdate();

        $update->load('checkout_onepage_shippingmethod');

        $layout->generateXml();

        $layout->generateBlocks();

        $output = $layout->getOutput();

        return $output;

    }



    /**

     * Get payment method step html

     *

     * @return string

     */

    protected function _getPaymentMethodsHtml()

    {

        $layout = $this->getLayout();

        $update = $layout->getUpdate();

        $update->load('checkout_onepage_paymentmethod');

        $layout->generateXml();

        $layout->generateBlocks();

        $output = $layout->getOutput();

        return $output;

    }



    /**

     * Return block content from the 'checkout_onepage_additional'

     * This is the additional content for shipping method

     *

     * @return string

     */

    protected function _getAdditionalHtml()

    {

        $layout = $this->getLayout();

        $update = $layout->getUpdate();

        $update->load('checkout_onepage_additional');

        $layout->generateXml();

        $layout->generateBlocks();

        $output = $layout->getOutput();

        Mage::getSingleton('core/translate_inline')->processResponseBody($output);

        return $output;

    }



    /**

     * Get order review step html

     *

     * @return string

     */

    protected function _getReviewHtml()

    {

        return $this->getLayout()->getBlock('root')->toHtml();

    }



    /**

     * Get one page checkout model

     *

     * @return Mage_Checkout_Model_Type_Onepage

     */

    public function getOnepage()

    {

        return Mage::getSingleton('checkout/type_onepage');

    }



    /**

     * Checkout page

     */

    public function indexAction()

    {

        if (!Mage::helper('checkout')->canOnepageCheckout()) {

            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));

            $this->_redirect('checkout/cart');

            return;

        }

        $quote = $this->getOnepage()->getQuote();

        if (!$quote->hasItems() || $quote->getHasError()) {

            $this->_redirect('checkout/cart');

            return;

        }

        if (!$quote->validateMinimumAmount()) {

            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?

                Mage::getStoreConfig('sales/minimum_order/error_message') :

                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');



            Mage::getSingleton('checkout/session')->addError($error);

            $this->_redirect('checkout/cart');

            return;

        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);

        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));

        $this->getOnepage()->initCheckout();

        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));

        $this->renderLayout();

    }



    /**

     * Refreshes the previous step

     * Loads the block corresponding to the current step and sets it

     * in to the response body

     *

     * This function is called from the reloadProgessBlock

     * function from the javascript

     *

     * @return string|null

     */

    public function progressAction()

    {

        // previous step should never be null. We always start with billing and go forward

        $prevStep = $this->getRequest()->getParam('prevStep', false);



        if ($this->_expireAjax() || !$prevStep) {

            return null;

        }



        $layout = $this->getLayout();

        $update = $layout->getUpdate();

        /* Load the block belonging to the current step*/

        $update->load('checkout_onepage_progress_' . $prevStep);

        $layout->generateXml();

        $layout->generateBlocks();

        $output = $layout->getOutput();

        $this->getResponse()->setBody($output);

        return $output;

    }



    /**

     * Shipping method action

     */

    public function shippingMethodAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        $this->loadLayout(false);

        $this->renderLayout();

    }



    /**

     * Review page action

     */

    public function reviewAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        $this->loadLayout(false);

        $this->renderLayout();

    }



    /**

     * Order success action

     */

    public function successAction()

    {

        $session = $this->getOnepage()->getCheckout();

        if (!$session->getLastSuccessQuoteId()) {

            $this->_redirect('checkout/cart');

            return;

        }

       $orderId = $session->getLastOrderId();
    
        $init_order                 = Mage::getModel("sales/order");
        $data                       = $init_order->load($this->getOnepage()->getCheckout()->getLastOrderId()); 
        $customeraddressIdbill      = $data['billing_address_id']; 
        $customeraddressIdship      = $data['shipping_address_id']; 
          
        $read                       = Mage::getSingleton('core/resource')->getConnection('core_read'); 
        $customeraddressdatabill    = $read->fetchRow("SELECT customer_address_id from sales_flat_order_address where entity_id = $customeraddressIdbill"); 
        $customeraddressdataship    = $read->fetchRow("SELECT customer_address_id from sales_flat_order_address where entity_id = $customeraddressIdship");

        Mage::dispatchEvent('checkout_onepage_controller_success', array("order"=>$data));

        $customerId             = $data['customer_id'];
        $billingcon             = $read->fetchRow(" SELECT * FROM `customer_address_entity` where entity_id =   $customeraddressdatabill[customer_address_id]");
        $shippingcon            = $read->fetchRow(" SELECT * FROM `customer_address_entity` where entity_id =   $customeraddressdataship[customer_address_id]");

        $storeidapi             = Mage::app()->getStore()->getStoreId();
        $netsuiteApiEnabled     = Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);

        if($netsuiteApiEnabled == 1 ){
    
                if( $billingcon['senttoNS'] == 0){
                    $addressId =  $customeraddressdatabill['customer_address_id'];
                    Mage::getModel('webservice/netsuite_postdata_customer')->addAddress($customerId,$addressId,$storeidapi);
                }
                if( $shippingcon['senttoNS'] == 0){
                    $addressId =  $customeraddressdataship['customer_address_id'];
                    Mage::getModel('webservice/netsuite_postdata_customer')->addAddress($customerId,$addressId,$storeidapi);
    
                }
    
                Mage::getModel('webservice/netsuite_postdata_order')->createOrder($orderId,$storeidapi);
         }



        $lastQuoteId = $session->getLastQuoteId();

        $lastOrderId = $session->getLastOrderId();

        $lastRecurringProfiles = $session->getLastRecurringProfileIds();

        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {

            $this->_redirect('checkout/cart');

            return;

        }



        $session->clear();

        $this->loadLayout();

        $this->_initLayoutMessages('checkout/session');

        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));

        $this->renderLayout();

    }



    /**

     * Failure action

     */

    public function failureAction()

    {

        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();

        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();



        if (!$lastQuoteId || !$lastOrderId) {

            $this->_redirect('checkout/cart');

            return;

        }



        $this->loadLayout();

        $this->renderLayout();

    }

	

    /**

     * Get additional info action

     */

    public function getAdditionalAction()

    {

        $this->getResponse()->setBody($this->_getAdditionalHtml());

    }



    /**

     * Address JSON

     */

    public function getAddressAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        $addressId = $this->getRequest()->getParam('address', false);

        if ($addressId) {

            $address = $this->getOnepage()->getAddress($addressId);



            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {

                $this->getResponse()->setHeader('Content-type', 'application/x-json');

                $this->getResponse()->setBody($address->toJson());

            } else {

                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');

            }

        }

    }



    /**

     * Save checkout method

     */

    public function saveMethodAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {

            $method = $this->getRequest()->getPost('method');

            $result = $this->getOnepage()->saveCheckoutMethod($method);

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        }

    }



    /**

     * Save checkout billing address

     */

    public function saveBillingAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost('billing', array());

            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);



            if (isset($data['email'])) {

                $data['email'] = trim($data['email']);

            }

            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);



            if (!isset($result['error'])) {

                if ($this->getOnepage()->getQuote()->isVirtual()) {

                    $result['goto_section'] = 'payment';

                    $result['update_section'] = array(

                        'name' => 'payment-method',

                        'html' => $this->_getPaymentMethodsHtml()

                    );

                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {

                    $result['goto_section'] = 'shipping_method';

                    $result['update_section'] = array(

                        'name' => 'shipping-method',

                        'html' => $this->_getShippingMethodsHtml()

                    );



                    $result['allow_sections'] = array('shipping');

                    $result['duplicateBillingInfo'] = 'true';

                } else {

                    $result['goto_section'] = 'shipping';

                }

            }



            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        }

    }



    /**



**/



    public function savenewBillingAction()

    {



        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {



            $data = $this->getRequest()->getPost('billing', array());

            $data_shipping = $this->getRequest()->getPost('shipping', array());



            try{



            $session        = Mage::getSingleton('checkout/session');

            $quote_id       = $session->getQuoteId();



            $customerAddressId     = $data['billing_id'];



            $firstname      = $data['firstname'];

            $lastname       = $data['lastname'];

            $street         = $data['street'][0];

            $city           = $data['city'];

            $read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 



            //$results    = $read->fetchAll("select * from unilab_cities where city_id = $data['city'] "); 

            // $city       = $results[0]['name'];   THIS COMMENTED BY LEANDRO

            $country        = $data['country_id'];

            $region         = $data['region'];

            $regionid       = $data['region_id'];

            $telephone      = $data['telephone'];

            $postcode       = $data['postcode'];

            $fax            = $data['fax'];

            $mobile         = $data['mobile'];

            $tin            = $data['tin'];



            $firstname_shipping      = $data_shipping['firstname'];

            $lastname_shipping       = $data_shipping['lastname'];

            $street_shipping         = $data_shipping['street'][0];

            $city_shipping           = $data_shipping['city'];

            $country_shipping        = $data_shipping['country_id'];

            $region_shipping         = $data_shipping['region'];

            $regionid_shipping       = $data_shipping['region_id'];

            $telephone_shipping      = $data_shipping['telephone'];

            $postcode_shipping       = $data_shipping['postcode'];

            $postcode_fax            = $data_shipping['fax'];

            $mobile_shipping         = $data_shipping['mobile'];



            if($customerAddressId != 0){

                

                //edit checkout session billing information

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                $connection->beginTransaction();



                //replace from the comment above - leandro

                $result    = $connection->fetchRow("select * from unilab_cities where city_id = $city");

                $city_name = $result['name'];



                $sqlUpdate = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId', firstname='$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', fax='$fax', vat_id='$tin'

                WHERE quote_id='$quote_id' AND address_type ='billing'";



                $connection->query($sqlUpdate);

                $connection->commit();

				// $addressData    = Mage::getModel('customer/address')->load($customerAddressId);

				// $addressData->setVatId($data['tin'])

				// ->save();





                if($data['use_for_shipping'] == 1):



                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                    $connection->beginTransaction();



                    $sqlUpdate_ = "UPDATE sales_flat_quote_address set firstname='$firstname', lastname='$lastname', street='$street', city='$city', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', fax='$fax', vat_id='$tin'

                    WHERE quote_id='$quote_id' AND address_type ='shipping'";



                    $connection->query($sqlUpdate_);

                    $connection->commit();



                endif;

                $result['reload'] = false; 

                //end edit checkout session billing information



                        //update billing information

                        if($data['save_in_address_book'] == 1){



                            $read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 

                            $results    = $read->fetchAll("select * from unilab_cities where city_id = {$data['city']} "); 

                            $city       = $results[0]['name'];



                            $addressData    = Mage::getModel('customer/address')->load($customerAddressId);

                            $addressData    ->setFirstname($data['firstname'])

                                            ->setLastname($data['lastname'])

                                            ->setStreet($data['street'][0])     

                                            ->setCity($city) 

                                            ->setCountry_id($data['country_id'])

                                            ->setRegion($data['region'])

                                            ->setRegion_id($data['region_id'])

                                            ->setPostcode($data['postcode'])

                                            ->setMobile($data['mobile'])

                                            ->setTelephone($data['telephone'])

                                            ->setFax($data['fax'])

                                            ->setVatId($data['tin'])

                                            ->save();



                                            $result_billing = $this->getOnepage()->saveBilling($addressData, $customerAddressId);



                                            if($data['use_for_shipping'] == 1):



                                                $result_shipping = $this->getOnepage()->saveShipping($addressData, $customerAddressId);



                                            endif;



                            $result['reload'] = true; 

                        }

                        //end update billing information

                    

                }else{ //create new billing information



                    if($data['save_in_address_book'] == 1){



                            $customeremail = $data['emailaddress'];



                                    $customer_ = Mage::getSingleton('customer/session')->getCustomer();

                                    $customer_->loadByEmail($customeremail);

                                    $cid = $customer_->getId();



                                    $read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 

                                    $results    = $read->fetchAll("select * from unilab_cities where city_id = {$data['city']} "); 

                                    $city       = $results[0]['name'];



                                    $addressData = Mage::getModel("customer/address");

                                    $addressData->setCustomerId($cid)

                                                ->setFirstname($data['firstname'])

                                                ->setLastname($data['lastname'])

                                                ->setCountryId($data['country_id'])

                                                ->setRegion($data['region'])

                                                ->setRegion_id($data['region_id'])

                                                ->setCity($city)

                                                ->setTelephone($data['telephone'])

                                                ->setPostcode($data['postcode'])

                                                ->setMobile($data['mobile'])

                                                ->setStreet($data['street'][0])

                                                ->setFax($data['fax'])

                                                ->setVatId($data['tin'])

                                                ->setSaveInAddressBook('1')

                                                ->save();



                                    $new_billingaddressid = $addressData->getId();

                                    $result_billing = $this->getOnepage()->saveBilling($addressData, $new_billingaddressid);

                                    $result['billing_id'] = $new_billingaddressid; 

                                    $result['reload'] = true; 



                            }elseif ($data['save_in_address_book'] == 0 && $data['use_for_shipping'] == 0) {

                                    $firstname_billing      = $data['firstname'];

                                    $lastname_billing        = $data['lastname'];

                                    $street_billing          = $data['street'][0];

                                    $city_billing            = $data['city'];

                                    $country_billing         = $data['country_id'];

                                    $region_billing          = $data['region'];

                                    $regionid_billing        = $data['region_id'];

                                    $telephone_billing       = $data['telephone'];

                                    $mobile_billing       = $data['mobile'];

                                    $postcode_billing        = $data['postcode'];

                                    $fax            = $data['fax'];



                                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                                    $connection->beginTransaction();



                                    $result    = $connection->fetchRow("select * from unilab_cities where city_id = $city_billing");



                                    $city_name = $result['name'];



                                    $sqlUpdatenew_ = "UPDATE sales_flat_quote_address set firstname='$firstname_billing ', lastname='$lastname_billing ', street='$street_billing ', city='$city_name ', region='$region_billing ', region_id='$regionid_billing', telephone='$telephone_billing ', mobile='$mobile_billing', postcode='$postcode_billing ', fax='$fax'

                                     WHERE quote_id='$quote_id' AND address_type ='billing'";



                                    $result['qry'] = $sqlUpdatenew_;

                                    $connection->query($sqlUpdatenew_);

                                    $connection->commit();

									

									

									$customer = Mage::getSingleton('customer/session')->getCustomer();

									$defaultBilling = $customer->getDefaultBillingAddress();

									$defaultShippping = $customer->getPrimaryShippingAddress();						



                                $result['reload'] = false; 

                            }

                            else{ //save in address == 0 ship to address == 1



                                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                                    $connection->beginTransaction();



                                    $sqlUpdatenew = "UPDATE sales_flat_quote_address set firstname='$firstname', lastname='$lastname', street='$street', city='$city', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', fax='$fax' 

                                    WHERE quote_id='$quote_id' AND address_type ='shipping'";



                                    $connection->query($sqlUpdatenew);

                                    $connection->commit();



                                $result['reload'] = false; 

                            }//end create new billing information

                        }



                } catch (Exception $e) {

    

                }

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                            

        }

    }



public function saveanotherBillingAction()

{

        

    if ($this->getRequest()->isPost()) {

        $data = $this->getRequest()->getPost('billing', array());



        $emailaddress   = $data['emailaddress'];

        $firstname      = $data['firstname'];

        $lastname       = $data['lastname'];

        $country        = $data['country_id'];

        $postcode       = $data['postcode'];

        $region         = $data['region'];

        $regionid       = $data['region_id'];

        $city           = $data['city'];

        $street         = $data['street'][0];

        $telephone      = $data['telephone'];

        $mobile         = $data['mobile'];

        $tin            = $data['tin'];

        



        $saveinabook    = $data['save_in_address_book'];



        try{



            if($firstname != '' && $lastname != '' && $country != '' && $postcode != '' && $regionid != '' && $city != '' && $street != '' && $mobile != ''):



                $customer_ = Mage::getSingleton('customer/session')->getCustomer();

                $customer_->loadByEmail($emailaddress);

                $cid = $customer_->getId();



                $read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 

                $results    = $read->fetchAll("select * from unilab_cities where city_id = {$data['city']} "); 

                $city       = $results[0]['name'];



                $addressData = Mage::getModel("customer/address");

                $addressData->setCustomerId($cid)

                            ->setFirstname($data['firstname'])

                            ->setLastname($data['lastname'])

                            ->setCountryId($data['country_id'])

                            ->setRegion($data['region'])

                            ->setRegion_id($data['region_id'])

                            ->setCity($city)

                            ->setTelephone($data['telephone'])

                            ->setPostcode($data['postcode'])

                            ->setMobile($data['mobile'])

                            ->setFax($data['fax'])

                            ->setStreet($data['street'][0])

                            ->setSaveInAddressBook('1')

                            ->setIsDefaultBilling('0')

                            ->setVatId( $data['tin'])

                            ->save();



                $session        = Mage::getSingleton('checkout/session');

                $quote_id       = $session->getQuoteId();

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                $connection->beginTransaction();



                $sqlUpdate = "UPDATE sales_flat_quote_address set firstname='$firstname', lastname='$lastname', street='$street', city='$city', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', fax='$fax'

                WHERE quote_id='$quote_id' AND address_type ='billing'";



                $connection->query($sqlUpdate);

                $connection->commit();                                 

                                    

                $result['reload'] =  true;

            else:



                $result['reload'] =  false;



            endif;



        }catch (Exception $e){



        }

        

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));     



    } 

}

    /**



    **/



    /**

     * Shipping address save action

     */

    public function saveShippingAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost('shipping', array());



            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);

            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);



            if (!isset($result['error'])) {

                $result['goto_section'] = 'shipping_method';

                $result['update_section'] = array(

                    'name' => 'shipping-method',

                    'html' => $this->_getShippingMethodsHtml()

                );

            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        }

    }



    /**



    **/

    public function savenewShippingAction()

    {

        

        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {



            $data = $this->getRequest()->getPost('shipping', array());

            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);

            $sameshippingbilling = $this->getRequest()->getPost('select-billing', false);



            $emailaddress   = $data['emailaddress'];

            $firstname      = $data['firstname'];

            $lastname       = $data['lastname'];

            $street         = $data['street'][0];

            $city           = $data['city'];

            $country        = $data['country_id'];

            $region         = $data['region'];

            $regionid       = $data['region_id'];

            $telephone      = $data['telephone'];

            $mobile         = $data['mobile'];

            $postcode       = $data['postcode'];

           

            $siab           = $data['save_in_address_book'];



            try{



                if($customerAddressId == 0){



                    if($firstname != '' && $lastname != '' && $country != '' && $postcode != '' && $regionid != '' && $city != '' && $street != '' && $mobile != ''):



                        $read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 

                        $results    = $read->fetchAll("select * from unilab_cities where city_id = $city "); 

                        $city_name  = $results[0]['name'];

                        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                        $connection->beginTransaction();



                        if($siab == 1){



                                $customer_ = Mage::getSingleton('customer/session')->getCustomer();

                                $customer_->loadByEmail($emailaddress);

                                $cid = $customer_->getId();



                                $shippingData = Mage::getModel("customer/address");

                                $shippingData->setCustomerId($cid)

                                            ->setFirstname($data['firstname'])

                                            ->setLastname($data['lastname'])

                                            ->setCountryId($data['country_id'])

                                            ->setRegion($data['region'])

                                            ->setRegion_id($data['region_id'])

                                            ->setCity($city_name)

                                            ->setTelephone($data['telephone'])

                                            ->setPostcode($data['postcode'])

                                            ->setMobile($data['mobile'])

                                            ->setStreet($data['street'][0])

                                            ->setFax($data['fax'])

                                            ->setSaveInAddressBook('1')

                                            ->save();



                                $new_shippingaddressid = $shippingData->getId();

                                $result_billing = $this->getOnepage()->saveShipping($shippingData, $new_shippingaddressid);

                                $result['shipping_id'] = $new_shippingaddressid;

                                if($sameshippingbilling == 1){



                                $result_billing = $this->getOnepage()->saveBilling($shippingData, $new_shippingaddressid);

                                }

                        }else{



                            $session        = Mage::getSingleton('checkout/session');

                            $quote_id       = $session->getQuoteId();



                            // $result_shipping = $this->getOnepage()->saveShipping($data, $customerAddressId);

                            $sqlUpdatenew_ = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId', firstname='$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', vat_id='$tin' 

                            WHERE quote_id='$quote_id' AND address_type ='shipping'";

                            $connection->query($sqlUpdatenew_);

                            $connection->commit();



                            if($sameshippingbilling == 1){



                                $result_billing = $this->getOnepage()->saveBilling($data, $customerAddressId);

                                $sqlUpdatenewb_ = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId',firstname= '$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', vat_id='$tin'

                                WHERE quote_id='$quote_id' AND address_type ='billing'";

                                $connection->query($sqlUpdatenewb_);

                                $connection->commit();

                            }

                        }



                        $result['reload'] = true; 



                    else:



                        $result['reload'] = false; 

                   

                    

                    endif;



                }else{



                    if($firstname != '' && $lastname != '' && $country != '' && $postcode != '' && $regionid != '' && $city != '' && $street != '' && $mobile != ''):



                        $session        = Mage::getSingleton('checkout/session');

                        $quote_id       = $session->getQuoteId();



                        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                        $connection->beginTransaction();

                        $result    = $connection->fetchRow("select * from unilab_cities where city_id = $city");

                        $city_name = $result['name'];



                        if($siab == 1){



                            $shippingData    = Mage::getModel('customer/address')->load($customerAddressId);

                            $shippingData       ->setFirstname($firstname)

                                                ->setLastname($lastname)

                                                ->setStreet($street)     

                                                ->setCity($city_name) 

                                                ->setCountry_id($country)

                                                ->setRegion($region)

                                                ->setRegion_id($regionid)

                                                ->setPostcode($postcode)

                                                ->setMobile($mobile)

                                                ->setTelephone($telephone)

                                                ->save();



                            $result_shipping = $this->getOnepage()->saveShipping($data, $customerAddressId);



                            if($sameshippingbilling == 1){



                                $result_billing = $this->getOnepage()->saveBilling($data, $customerAddressId);



                            //if ever shipping is modified but save in address book is not clicked    

                                $sqlUpdatenewb_ = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId',firstname= '$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', vat_id='$tin'

                                WHERE quote_id='$quote_id' AND address_type ='billing'";

                                $connection->query($sqlUpdatenewb_);

                                $connection->commit();

                            }

                        }else{



                            // $result_shipping = $this->getOnepage()->saveShipping($data, $customerAddressId);



                            $sqlUpdatenew_ = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId',firstname= '$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', vat_id='$tin'

                            WHERE quote_id='$quote_id' AND address_type ='shipping'";

                            $connection->query($sqlUpdatenew_);

                            $connection->commit();





                            if($sameshippingbilling == 1){



                                $result_billing = $this->getOnepage()->saveBilling($data, $customerAddressId);



                            //if ever shipping is modified but save in address book is not clicked    

                                $sqlUpdatenewb_ = "UPDATE sales_flat_quote_address set customer_address_id='$customerAddressId',firstname= '$firstname', lastname='$lastname', street='$street', city='$city_name', region='$region', region_id='$regionid', telephone='$telephone', postcode='$postcode', mobile='$mobile', vat_id='$tin'

                                WHERE quote_id='$quote_id' AND address_type ='billing'";

                                $connection->query($sqlUpdatenewb_);

                                $connection->commit();

                            }

                        }



                        $result['reload'] = true; 



                    else: 

                        $result['reload'] = false; 

                    

                    endif;    



                    $storeid = Mage::app()->getStore()->getStoreId();

                    if($storeid == 31){ //for Digital Data - ADOBE

                        $result['reload'] = true;

                    }

                }

                

            } catch (Exception $e) {

                    

                    $result['reload'] = false; 

            }



            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                            

        }

        

    }



    /**

     * Shipping method save action

     */

    public function saveShippingMethodAction()

    {

        if ($this->_expireAjax()) {

            return;

        }

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost('shipping_method', '');

            $result = $this->getOnepage()->saveShippingMethod($data);

            // $result will contain error data if shipping method is empty

            if (!$result) {

                Mage::dispatchEvent(

                    'checkout_controller_onepage_save_shipping_method',

                     array(

                          'request' => $this->getRequest(),

                          'quote'   => $this->getOnepage()->getQuote()));

                $this->getOnepage()->getQuote()->collectTotals();

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));



                $result['goto_section'] = 'payment';

                $result['update_section'] = array(

                    'name' => 'payment-method',

                    'html' => $this->_getPaymentMethodsHtml()

                );

            }

            $this->getOnepage()->getQuote()->collectTotals()->save();

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        }

    }



    /**

     * Save payment ajax action

     *

     * Sets either redirect or a JSON response

     */

    public function savePaymentAction()

    {   



        if ($this->_expireAjax()) {

            return;

        }

        try {

            if (!$this->getRequest()->isPost()) {

                $this->_ajaxRedirectResponse();

                return;

            }



            $data = $this->getRequest()->getPost('payment', array());

            $result = $this->getOnepage()->savePayment($data);



            //Mage::log($data, null, 'savePayment.log');

            // get section and redirect data

            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

            if (empty($result['error']) && !$redirectUrl) {

                $this->loadLayout('checkout_onepage_review');

                $result['goto_section'] = 'review';

                $result['update_section'] = array(

                    'name' => 'review',

                    'html' => $this->_getReviewHtml()

                );

            }

            if ($redirectUrl) {

                $result['redirect'] = $redirectUrl;

            }

        } catch (Mage_Payment_Exception $e) {

            if ($e->getFields()) {

                $result['fields'] = $e->getFields();

            }

            $result['error'] = $e->getMessage();

        } catch (Mage_Core_Exception $e) {

            $result['error'] = $e->getMessage();

        } catch (Exception $e) {

            Mage::logException($e);

            $result['error'] = $this->__('Unable to set Payment Method.');

        }

        updateShippingTin();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }



    /**

     * Get Order by quoteId

     *

     * @throws Mage_Payment_Model_Info_Exception

     * @return Mage_Sales_Model_Order

     */

    protected function _getOrder()

    {

        if (is_null($this->_order)) {

            $this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');

            if (!$this->_order->getId()) {

                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));

            }

        }

        return $this->_order;

    }



    /**

     * Create invoice

     *

     * @return Mage_Sales_Model_Order_Invoice

     */

    protected function _initInvoice()

    {

        $items = array();

        foreach ($this->_getOrder()->getAllItems() as $item) {

            $items[$item->getId()] = $item->getQtyOrdered();

        }

        /* @var $invoice Mage_Sales_Model_Service_Order */

        $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);

        $invoice->setEmailSent(true)->register();



        Mage::register('current_invoice', $invoice);

        return $invoice;

    }



    /**

     * Create order action

     */

    public function saveOrderAction()

    {

        

        if (!$this->_validateFormKey()) {

            $this->_redirect('*/*');

            return;

        }



        if ($this->_expireAjax()) {

            return;

        }



        $result = array();

        try {

            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();

            if ($requiredAgreements) {

                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));

                $diff = array_diff($requiredAgreements, $postedAgreements);

                if ($diff) {

                    $result['success'] = false;

                    $result['error'] = true;

                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');

                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                    return;

                }

            }



            $data = $this->getRequest()->getPost('payment', array());

            if ($data) {

                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT

                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY

                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY

                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX

                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

                $this->getOnepage()->getQuote()->getPayment()->importData($data);

            }

            

            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();

            $result['success'] = true;

            $result['error']   = false;

        } catch (Mage_Payment_Model_Info_Exception $e) {

            $message = $e->getMessage();

            if (!empty($message)) {

                $result['error_messages'] = $message;

            }

            $result['goto_section'] = 'payment';

            $result['update_section'] = array(

                'name' => 'payment-method',

                'html' => $this->_getPaymentMethodsHtml()

            );

        } catch (Mage_Core_Exception $e) {

            Mage::logException($e);

            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

            $result['success'] = false;

            $result['error'] = true;

            $result['error_messages'] = $e->getMessage();



            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();

            if ($gotoSection) {

                $result['goto_section'] = $gotoSection;

                $this->getOnepage()->getCheckout()->setGotoSection(null);

            }

            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();

            if ($updateSection) {

                if (isset($this->_sectionUpdateFunctions[$updateSection])) {

                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];

                    $result['update_section'] = array(

                        'name' => $updateSection,

                        'html' => $this->$updateSectionFunction()

                    );

                }

                $this->getOnepage()->getCheckout()->setUpdateSection(null);

            }

        } catch (Exception $e) {

            Mage::logException($e);

            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

            $result['success']  = false;

            $result['error']    = true;

            //$result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');

			

			$ckeckreturn = Mage::getModel('oneshop/orderincrement_check')->getlastincrementorder();



            $result['success']  = false;

            $result['error']    = true;

			$result['check']    = true;

           

			$result['message'] = $ckeckreturn;

        }

        $this->getOnepage()->getQuote()->save();

		

        /**

         * when there is redirect to third party, we don't want to save order yet.

         * we will save the order in return action.

         */



        if($result['success'] == true){

             

             $result['redirect'] = Mage::getBaseUrl() . "checkout/onepage/success/"; 

        }

        if (isset($redirectUrl)) {

            $result['redirect'] = $redirectUrl;

        }

        

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }



    /**

     * Filtering posted data. Converting localized data if needed

     *

     * @param array

     * @return array

     */

    protected function _filterPostData($data)

    {

        $data = $this->_filterDates($data, array('dob'));

        return $data;

    }



    /**

     * Check can page show for unregistered users

     *

     * @return boolean

     */

    protected function _canShowForUnregisteredUsers()

    {

        return Mage::getSingleton('customer/session')->isLoggedIn()

            || $this->getRequest()->getActionName() == 'index'

            || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())

            || !Mage::helper('checkout')->isCustomerMustBeLogged();

    }

}

	function updateShippingTin(){ 

   

		$session        = Mage::getSingleton('checkout/session');

		$quote_id       = $session->getQuoteId();

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		$read                       = Mage::getSingleton('core/resource')->getConnection('core_read'); 

		$customeraddressdata     = $read->fetchAll("SELECT customer_address_id ,address_type  FROM sales_flat_quote_address WHERE quote_id = $quote_id"); 



		// Mage::log($customeraddressdata[0]['customer_address_id'], null, 'wew.log');  



		$customerBillingAddressId = $customeraddressdata[0]['customer_address_id'];

		$customerShippingAddressId = $customeraddressdata[1]['customer_address_id'];

		 

		  if( $customerShippingAddressId == $customerBillingAddressId) {

		   $addressData    = Mage::getModel('customer/address')->load($customerBillingAddressId); 

		   $tin = $addressData->getVatId(); 

		   if($tin != null){

			$sqlUpdatenew_ = 'UPDATE sales_flat_quote_address set vat_id='.$tin.' WHERE quote_id='.$quote_id.' AND address_type ="shipping"';

			$connection->query($sqlUpdatenew_); 

			$connection->commit();

		   }



		  }

	}

