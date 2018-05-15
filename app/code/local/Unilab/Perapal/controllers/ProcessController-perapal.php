<?php 
/*
KBANK
 */
class Unilab_Perapal_ProcessController extends Mage_Core_Controller_Front_Action
{
 

    public function IndexAction()
    {       

    /**

    **/
    /*
     //send email 
     $ccemail               =  Mage::getStoreConfig('payment/perapal/email_recipient');
     $enable_email          =  Mage::getStoreConfig('payment/perapal/enable');


    if($enable_email == 1) :

    // protected function newaccountemail($email, $firstname, $password)
    // {

        $order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order_details = Mage::getModel('sales/order')->loadByIncrementId($order_id);

        var_dump($order_details);
        die();
        echo $customerid = $order_details->getcustomerid();

        $customermodel = Mage::getModel('customer/customer')
            ->load($customerid);

        echo $customer_email = $customermodel->getemail();
        echo $customer_firstname = $customermodel->getfirstname();

        try{ #send email
            $storeId = Mage::app()->getStore()->getStoreId();
            $frontendname = Mage::app()->getStore()->getFrontendName();
            $ccemail               =  Mage::getStoreConfig('payment/perapal/email_recipient');

                
            // This is the template name from your etc/config.xml 
            $storeId = Mage::app()->getStore()->getStoreId();

            //if($storeId == 1):
            //$template_id = 'Elleva-New Account with free product';
            //else:
            //$template_id = 'Elleva-New Account with free product-thai';  
            //endif;

            $template_id = 'Unilab - New Order - Custom';  
            // Who were sending to...
            $email_to = $customer_email;
            $customer_name   = $customer_firstname;  
            $email_to_cc = explode(',', $ccemail);

            // Load our template by template_id
            //$email_template  = Mage::getModel('core/email_template')->loadDefault($template_id);
            $email_template  = Mage::getModel('core/email_template')->loadByCode($template_id);

            // Here is where we can define custom variables to go in our email template!
            $email_template_variables = array(
                'email' => $email,
                'firstname' => $customer_name,
                'password' => $password,
                'frontendname' => $frontendname,

                //'password' => $password

                // Other variables for our email template.
            );

            // I'm using the Store Name as sender name here.
            $sender_name = 'Elleva'; //Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
            // I'm using the general store contact here as the sender email.
            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
            $email_template->setSenderName($sender_name);
            $email_template->setSenderEmail($sender_email); 

            //Send the email!
            $email_template->addBcc($email_to_cc);
            $email_template->send($email_to, $customer_name, $email_template_variables); 
            // $email_template->send($ccemail, $customer_name, $email_template_variables);  //cc  

        }catch (Exception $e) {
                        
            Mage::log($e->getMessage(), null, 'sendemail-freeproductpromo.log');
                        
        }

    // }
    endif;
        //send email
        /**

        **/

//         $this->_redirect('checkout/onepage/success', array('_secure'=>true));



        // $this->_getCheckout()->setPaymentQuoteId($this->_getCheckout()->getQuoteId());
        // die();
        // $this->getResponse()->setBody($this->getLayout()->createBlock('bpisecurepay/redirect')->toHtml());
        // $this->_getCheckout()->unsQuoteId();
        // $this->_getCheckout()->unsRedirectUrl();
         //$this->_redirect('checkout/onepage/success', array('_secure'=>true));

        //          $this->loadLayout();          

 

        // $block = $this->getLayout()->createBlock(

        //     'Mage_Core_Block_Template',

        //     'ulahevents',

        //     array('template' => 'ulahevents/sites.phtml')

        // );

        

        // $this->getLayout()->getBlock('root')->setTemplate('page/unilab-2columns-right.phtml');

        // $this->getLayout()->getBlock('content')->append($block);

        // $this->_initLayoutMessages('core/session'); 

        // $this->renderLayout();
    }

    public function sendemailAction()
    {   
             /**

    **/
     //send email 
    //   $ccemail               =  Mage::getStoreConfig('payment/perapal/email_recipient');
    //   $enable_email          =  Mage::getStoreConfig('payment/perapal/enable');

    
    // if($enable_email == 1) :

        // echo "--".$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        // echo "---".$order_details = Mage::getModel('sales/order')->loadByIncrementId($order_id);

        $customermodel = Mage::getSingleton('customer/session')->getCustomer();

        $customer_email = $customermodel->getemail();
        $customer_firstname = $customermodel->getfirstname();


        try{ #send email
            $storeId = Mage::app()->getStore()->getStoreId();
            // $frontendname = Mage::app()->getStore()->getFrontendName();
            $frontendname = 'Clickhealth';   
             
            // This is the template name from your etc/config.xml 
            $storeId = Mage::app()->getStore()->getStoreId();

            $template_id = 'Unilab - New Order - Custom';  
            // Who were sending to...
            $email_to = $customer_email;
            $customer_name   = $customer_firstname;  
            $email_to_cc = explode(',', $ccemail);

            // Load our template by template_id
            //$email_template  = Mage::getModel('core/email_template')->loadDefault($template_id);
            $email_template  = Mage::getModel('core/email_template')->loadByCode($template_id);

            // Here is where we can define custom variables to go in our email template!
            $email_template_variables = array(
                'email' => $customer_email,
                'firstname' => $customer_name,
                'frontendname' => $frontendname,

                // Other variables for our email template.
            );

            // I'm using the Store Name as sender name here.
            $sender_name = $frontendname; //Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
            // I'm using the general store contact here as the sender email.
            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
            $email_template->setSenderName($sender_name);
            $email_template->setSenderEmail($sender_email); 

            //Send the email!
            $email_template->addBcc($email_to_cc); 
            
        // echo $customer_firstname;
        // die();

        }catch (Exception $e) {
                        
            Mage::log($e->getMessage(), null, 'sendemail-perapal.log');
                        
        }
    // endif;
        //send email
        /**

        **/

        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
  
}
