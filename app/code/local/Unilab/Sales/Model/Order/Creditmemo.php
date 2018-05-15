<?php 
class Unilab_Sales_Model_Order_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{ 

    const XML_PATH_ACCOUNTANT_EMAIL_TEMPLATE               = 'customconfig/creditmemo/template'; 
    const XML_PATH_ACCOUNTANT_EMAIL_IDENTITY               = 'customconfig/creditmemo/identity';
    const XML_PATH_ACCOUNTANT_EMAIL_COPY_TO                = 'customconfig/creditmemo/copy_to';
    const XML_PATH_ACCOUNTANT_EMAIL_COPY_METHOD            = 'customconfig/creditmemo/copy_method';
    const XML_PATH_ACCOUNTANT_EMAIL_EMAIL                  = 'customconfig/creditmemo/email';
    const XML_PATH_ACCOUNTANT_EMAIL_ENABLED                = 'customconfig/creditmemo/enabled';
 
	public function notifyAccountant($notifyAccountant = true)
    {
        $order   = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewCreditmemoEmailToAccountant($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo 	= $this->_getEmails(self::XML_PATH_ACCOUNTANT_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_ACCOUNTANT_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyAccountant) {
            return $this;
        }

        // Start store emulation process
        $appEmulation			= Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_ACCOUNTANT_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId   = Mage::getStoreConfig(self::XML_PATH_ACCOUNTANT_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer    = Mage::getModel('core/email_template_mailer'); 
		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo($order->getCustomerEmail(), $customerName);
		if ($copyTo && $copyMethod == 'bcc') {
			// Add bcc to customer email
			foreach ($copyTo as $email) {
				$emailInfo->addBcc($email);
			}
		}
		$mailer->addEmailInfo($emailInfo); 

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy')) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_ACCOUNTANT_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'creditmemo'   => $this,
                'comment'      => '',
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send(); 

        return $this;
    }
	
	public function getFeeAmountTotals()
	{	
		$rate_config    = Mage::getModel('xend/config')->setType(Unilab_Xend_Model_Config::CONFIG_TYPE_RATE)
													->getConfig();
		$insurance_fee	= 0.00;
		
		if($this->getFeeAmount() > 0 ){
		
		
		
		}
		
		if($this->getSubtotal() > $rate_config->getMaxFreeShippingInsurance()){  
			$insurable_amount     = (($this->getSubtotal() - $rate_config->getMaxFreeShippingInsurance()) * ($rate_config->getInsurancePercentExcess() / 100)); 
			if($insurable_amount>=100){ 
				$insurance_fee   = ceil($insurable_amount/100) * 100;
			}else{  
				 $insurance_fee	 = $insurable_amount;
			}  		
		} 
		
		return floor($insurance_fee);
	}

}
