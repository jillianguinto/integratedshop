<?php
/***
KBANK 
 * 
 */
class Unilab_Perapal_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

 
	protected $_code  = 'perapal';
    protected $_formBlockType           = 'perapal/form';

    protected $_infoBlockType           = 'perapal/info';
	//protected $_formBlockType = 'payment/form_checkmo';
	//protected $_infoBlockType = 'payment/info_cod';
	 
	/**
	* Assign data to info model instance
	*
	* @param   mixed $data
	* @return  Mage_Payment_Model_Method_Checkmo
	*/
	public function assignData($data)
	{

		$details = array();
		if ($this->getPayableTo()) {
		$details['payable_to'] = $this->getPayableTo();
		}

		if ($this->getMailingAddress()) {
		$details['mailing_address'] = $this->getMailingAddress();
		}

		if (!empty($details)) {
		$this->getInfoInstance()->setAdditionalData(serialize($details));
		}



	return $this;

	}
	 
	public function getPayableTo()
	{

		return $this->getConfigData('payable_to');

	}
	 
	public function getMailingAddress()
	{

		return $this->getConfigData('mailing_address');

	}


	 public function getOrderPlaceRedirectUrl()
  	{

  	 /**

    **/
/*
     //send email 
      $ccemail               =  Mage::getStoreConfig('payment/perapal/email_recipient');
      $enable_email          =  Mage::getStoreConfig('payment/perapal/enable');
      $orderincrement        =  Mage::getSingleton('core/session')->getOrderIncrement();
      $what                     = Mage::getSingleton('core/session')->getwhatwhat();
    
    if($enable_email == 1) :


		$customermodel = Mage::getSingleton('customer/session')->getCustomer();

        $customer_email = $customermodel->getemail();
        $customer_firstname = $customermodel->getfirstname();

        try{ #send email
            $storeId = Mage::app()->getStore()->getStoreId();
            $frontendname = 'Clickhealth';
          
            // This is the template name from your etc/config.xml 
            $storeId = Mage::app()->getStore()->getStoreId();

            $template_id = 'Unilab - New Order Confirmation';  
            // Who were sending to...
            $email_to = $customer_email;
            $customer_name   = $customer_firstname;  
            $email_to_cc = explode(',', $ccemail);

            // Load our template by template_id
            $email_template  = Mage::getModel('core/email_template')->loadByCode($template_id);

            // Here is where we can define custom variables to go in our email template!
            $email_template_variables = array(
                'email' => $email,
                'firstname' => $customer_name,
                'frontendname' => $frontendname,
                'orderincrement' => $orderincrement

                // Other variables for our email template.
            );

            // I'm using the Store Name as sender name here.
            $sender_name = $frontendname;

            // I'm using the general store contact here as the sender email.
            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
            $email_template->setSenderName($sender_name);
            $email_template->setSenderEmail($sender_email); 

            //Send the email!
            $email_template->addBcc($email_to_cc);
            $email_template->send($email_to, $customer_name, $email_template_variables);  

        }catch (Exception $e) {
                        
            Mage::log($e->getMessage(), null, 'sendemail-perapal.log');
                        
        }

    endif;
        //send email
        /**

        **/


        return Mage::getUrl('checkout/onepage/success', array('_secure' => false));
  	}
 

}