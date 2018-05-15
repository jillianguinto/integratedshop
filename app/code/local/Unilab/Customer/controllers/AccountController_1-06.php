<?php 

require_once ('Mage/Customer/controllers/AccountController.php');

class Unilab_Customer_AccountController extends Mage_Customer_AccountController
{

	public function loginajaxAction()
	{ 

		$response = array('valid'=> false,'message'=>'');
		$session  = $this->_getSession();		
		$message  = '';

		if ($session->isLoggedIn())
		{

            $response['valid'] = true;

        }else{		

			if ($this->getRequest()->isPost())
			{

				$login = $this->getRequest()->getPost('login');

				if (!empty($login['username']) && !empty($login['password']))
				{

					try 
					{
						$session->login($login['username'], $login['password']);

						if ($session->getCustomer()->getIsJustConfirmed()) 
						{
							$this->_welcomeCustomer($session->getCustomer(), true);
						} 

						$response['valid'] = true;

					} 
					catch (Mage_Core_Exception $e) 
					{

						switch ($e->getCode()) 
						{

							case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:

								$value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);

								$message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);

								break;

							case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:

								$message = $e->getMessage();

								break;

							default:

								$message = $e->getMessage();

						}

					}
					catch (Exception $e) 
					{
						// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
					}

				} 
				else 
				{

					$session->addError($this->__('Login and password are required.'));
				}
			} 

			//$this->_loginPostRedirect();
		}  
		
		$response['message'] = $message;
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }



	/**

     * Create customer account action

     */

    public function createPostAction()
    {

        /** @var $session Mage_Customer_Model_Session */

        $session = $this->_getSession();

        if ($session->isLoggedIn()) 
		{

            $this->_redirect('*/*/');
            return;

        }

		
        $session->setEscapeMessages(true); // prevent XSS injection in user input

        if (!$this->getRequest()->isPost()) 
		{

            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

            $this->_redirectError($errUrl);

            return;

        }

        $customer = $this->_getCustomer();



        try 
		{

        	$errors = $this->_getCustomerErrors($customer);

			$params = $this->getRequest()->getPost();

			// Custom: validator for at telephone and mobile (at least one field value required)			

			// The telephone & mobile should be set as optional in customer configuration

			if(isset($params['telephone']) && empty($params['telephone']) && isset($params['mobile']) && empty($params['mobile']) )
			{

				$this->_addSessionError(array('Please provide at least one value for telephone or mobile.'));

				return;

			}

			
            if (empty($errors)) 
			{

                $customer->save();

                $this->_dispatchRegisterSuccess($customer);

                $this->_successProcessRegistration($customer);

                return;

            } else 
			{

                $this->_addSessionError($errors);

            }

        } 
		catch (Mage_Core_Exception $e) 
		{

            $session->setCustomerFormData($this->getRequest()->getPost());

            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) 
			{

                $url = $this->_getUrl('customer/account/forgotpassword');

                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);

                $session->setEscapeMessages(false);

            } 
			else 
			{

                $message = $e->getMessage();

            }

            $session->addError($message);

        } 
		catch (Exception $e) 
		{

            $session->setCustomerFormData($this->getRequest()->getPost())

                ->addException($e, $this->__('Cannot save the customer.'));

        }

        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

        $this->_redirectError($errUrl);

    }

	/**

     * Define target URL and redirect customer after logging in

     */

    protected function _loginPostRedirect()
	{

			$session = $this->_getSession();

			if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl())
			{

			    // Set default URL to redirect customer to
			    $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
	 
			    // Redirect customer to the last page visited after logging in
				if ($session->isLoggedIn())
				{

					if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard'))
					{

						$referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);

						if ($referer)
						{

							$referer = Mage::helper('core')->urlDecode($referer);

								if ($this->_isUrlInternal($referer))
								{
									$session->setBeforeAuthUrl($referer);
								}

						}

					}
					else if ($session->getAfterAuthUrl())
					{
						$session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
					}

				}
				else
				{
					$session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
					
				}

	 

		   }
		   else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl())
		   {
				 $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
				 
		   }
		   else
		   {

				if (!$session->getAfterAuthUrl())
				{
					$session->setAfterAuthUrl($session->getBeforeAuthUrl());
				}

	
				if ($session->isLoggedIn())
				{
					//$session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
					$session->setBeforeAuthUrl(Mage::getBaseUrl());
					
				}

		   }

	 

		   if ($this->getRequest()->isPost())
		   {

				 $login = $this->getRequest()->getPost('login');

				 $rememberme = $this->getRequest()->getPost('rememberme');

				 try
				 {

					  $cookie = Mage::getModel('core/cookie');

					  if (!empty($login['username']) && !empty($login['password']) && !empty($rememberme))
					  {
							$cookie->set('user_name', $login['username']);
							$cookie->set('pass_user_name', $login['password']);
							$cookie->set('rememberme', 1);
					  }
					  else if (!empty($login['username']) && !empty($login['password']) && empty($rememberme))
					  {

							$cookie->delete('user_name');
							$cookie->delete('pass_user_name');
							$cookie->delete('rememberme');

					  }

				 }
				 catch (Exception $e)
				 {

				 }

		   }
		   

		   $this->_redirectUrl($session->getBeforeAuthUrl(true));

	}

}

