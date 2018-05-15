<?php 

class Unilab_Inquiry_IndexController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_EMAIL_RECIPIENT  	= 'unilab_customers/inquiry/recipient_email';
    const XML_PATH_EMAIL_SENDER     	= 'unilab_customers/inquiry/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   	= 'unilab_customers/inquiry/email_template';
    const XML_PATH_ENABLED          	= 'unilab_customers/inquiry/enabled';
    const XML_SENDER_COPY           	= 'unilab_customers/inquiry/sender_copy';
	
	const XML_PATH_EMAIL_TEMPLATE_PREFIX = 'unilab_customers';
	const XML_PATH_EMAIL_TEMPLATE_SUFFIX = 'email_template';

    public function preDispatch()
    {
        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
		$crumbs = $this->getLayout()->getBlock('breadcrumbs');
		$crumbs->addCrumb('home', array(
			'label' => Mage::helper('inquiry')->__("Home"),
			'title' => Mage::helper('inquiry')->__("Home"),
			'link' => Mage::getUrl('/')
		));
		$crumbs->addCrumb('contacts', array(
			'label' => Mage::helper('inquiry')->__("Contact Us"),
			'title' => Mage::helper('inquiry')->__("Contact Us"), 
		));
        $formData = Mage::getSingleton('customer/session')->getFormData();
        $this->getLayout()->getBlock('contactForm')
            ->setFormAction( Mage::getUrl('*/*/post') )
            ->setFormData($formData);

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                if (empty($post['subject'])){
                    $post['subject'] = ' ';
                }

				$allDepartments = Mage::helper('inquiry')->getAllDepartments();

				$recipient = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT); // default
										
                if (isset($post['department'])){
                    $department = Mage::helper('inquiry')->getDepartmentByCodeCstm($post['department'],true);
				
                    if (!empty($department)){
                        $recipient = $department['email'];
                        $post['subject'] = '['.$department['name'].']: '.$post['subject'];
                    }
                }
		
				$postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
				
                if ($error) {
                    Mage::getSingleton('customer/session')->setFormData($post);
                    throw new Exception();
                }


				$customerId = null;
				$customer = null;
				if(Mage::helper('customer')->isLoggedIn()){
					$customerId = Mage::helper('customer')->getCustomer()->getId();
					$customer = Mage::helper('customer')->getCustomer();
				}
				
				/* Try to retrieve customer via email address */
				/*
				if(!$customer && !Mage::helper('customer')->isLoggedIn()){
					try{
						if($customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
													->loadByEmail($post['email'])){
							 $customerId = $customer->getId();
						}
					}catch(Exception $e){}
				}
				*/
				
				$options = Mage::helper('inquiry')->getDepartmentByCodeCstm($post['department'],true);
				
				try{
					$inquiry = Mage::getSingleton('inquiry/inquiry')
				 			->setStoreId(Mage::app()->getStore()->getId())
							->setCustomerId($customerId)
							->setDepartment($options['code'])
							->setDepartmentEmail($options['email'])
							->setConcern(nl2br($post['comment']))
							//->setConcern(mysql_escape_string($post['comment']))
							->setEmailAddress($post['email'])
							->setName($post['name'])
							->setCreatedTime(now())
							->save();	
				}catch(exception $e){				
					throw new Exception(Mage::helper('inquiry')->__('Unable to submit your inquiry. Please try again later.'));
				}
							
							
				if($customer && $customer->getId()){					
					/* Load customer Info to Inquiry */				
					$inquiry->setCustomer($customer);					
					$customerAddressId = $customer->getDefaultShipping();
					if ($customerAddressId){
						$address = Mage::getModel('customer/address')->load($customerAddressId);
						$inquiry->setAddress($address->format('html'));
						$inquiry->setMobile($address->getMobile());
						$inquiry->setTelephone($address->getTelephone());
					}		
				}					
						
				
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        //Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        $department['template'],				
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipient,
                        null,
                        array('data' => $inquiry,
                        'store'    => 'Clickhealth',)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();   
                }
				
				if (Mage::getStoreConfigFlag(self::XML_SENDER_COPY)){
                   // $postObject->unsEmail();
                    $mailTemplate->unsReplyTo()
                        ->sendTransactional(
                            //Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                            $department['template'],
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                            $post['email'],
                            null,
                            array('data' => $inquiry,
                            'store'    => 'Clickhealth',)
                        );
                }                 

                

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('inquiry')->__('Thank you. We will contact you soon.'));
                Mage::getSingleton('customer/session')->unsFormData();
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('inquiry')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    } 
	
	public function postinquiryAction()
    { 
        $post 			= $this->getRequest()->getPost();
		$inquiry_helper = Mage::helper('inquiry');
        if ( $post ) { 
		
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try { 
				
				//CHECK IS USER IS LOGGED IN
				if(!Mage::helper('customer')->isLoggedIn()){				
                    throw new Exception(Mage::helper('inquiry')->__('You must logged in to post a message.'));
				}
				
				//CHECK IF DEPARTMENT IS EXISTING
				if(!$department = $inquiry_helper->getDepartmentByCode($post['type'],true)){					
                    Mage::getSingleton('customer/session')->setFormData($post);                    
                    throw new Exception(Mage::helper('inquiry')->__('Temporarily unreachable. Please try again later.'));
				}
				
				$customer		 = Mage::helper('customer')->getCustomer();				
				$recipient 	     = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT); 
                $post['subject'] = '['.$department->getName().']: '.$department->getSubject(); 

                $postObject = new Varien_Object();
                $postObject->setData($post); 
                $postObject->setCustomer($customer);
				
				$options = Mage::helper('inquiry')->getDepartmentByCodeCstm($post['type'],true);
			
				try{
					$inquiry = Mage::getSingleton('inquiry/inquiry')
				 			->setStoreId(Mage::app()->getStore()->getId())
							->setCustomerId(Mage::helper('customer')->getCustomer()->getId())
							->setDepartment($options['code'])
							->setDepartmentEmail($options['email'])
							->setConcern(nl2br($post['comment']))
							->setEmailAddress($customer->getEmail())
							->setName($customer->getFirstname().' '.$customer->getLastname())
							->setCreatedTime(now())
							->save();	
				}catch(exception $e){					
					throw new Exception(Mage::helper('inquiry')->__('Unable to submit your inquiry. Please try again later.'));
				}
				
				
				/* Load customer Info */
				if($customer->getId()){
					$inquiry->setCustomer($customer);					
					$customerAddressId = Mage::helper('customer')->getCustomer()->getDefaultShipping();
					if ($customerAddressId){
						$address = Mage::getModel('customer/address')->load($customerAddressId);
						$inquiry->setAddress($address->format('html'));
						$inquiry->setMobile($address->getMobile());
						$inquiry->setTelephone($address->getTelephone());
					}
				}
				
				
                $mailTemplate = Mage::getModel('core/email_template');
				
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($customer->getEmail())
                    ->sendTransactional(
						$department->getTemplate(),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $department->getEmail(),
                        null,
                        array('data' => $inquiry)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                if (Mage::getStoreConfigFlag(self::XML_SENDER_COPY)){
                    $postObject->unsEmail();
                    $mailTemplate->unsReplyTo()
                        ->sendTransactional(
                            $department->getTemplate(),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                            $customer->getEmail(),
                            null,
                            array('data' => $inquiry)
                        );
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('inquiry')->__('Thank you. We will contact you soon.'));
                Mage::getSingleton('customer/session')->unsFormData();
                
				$this->_redirectUrl($this->_getRefererUrl());
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true); 
				if(!$e->getMessage()){
				   Mage::getSingleton('customer/session')->addError(Mage::helper('inquiry')->__('Unable to submit your request. Please, try again later'));
				}else{
				   Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
				$this->_redirectUrl($this->_getRefererUrl());
                return;
            }

        } else {
             $this->_redirectUrl($this->_getRefererUrl());
        }
    }  
	
	protected function _getRefererUrl()
	{
		$refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
			$refererUrl = $url;
		}
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
			$refererUrl = Mage::helper('core')->urlDecode($url);
		}
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
			$refererUrl = Mage::helper('core')->urlDecode($url);
		}
	 
		if (!$this->_isUrlInternal($refererUrl)) {
			$refererUrl = Mage::app()->getStore()->getBaseUrl();
		}
		return $refererUrl;
	}

	public function showformAction()
	{ 
		$this->loadLayout(); 
		
		$code 	  = $this->getRequest()->getParam('code');  		
		$template = "inquiry/form/invalid.phtml";	 
		
		if($department_data =  Mage::helper('inquiry')->getDepartmentByCode($code,true))
		{ 
			if($department_data->getCode() == Unilab_Inquiry_Block_Quickaccess_Form::FORM_CODE_DOCTOR){
				$template = "inquiry/form/quickaccess/doctor.phtml";		
			}elseif($department_data->getCode() == Unilab_Inquiry_Block_Quickaccess_Form::FORM_CODE_PHARMACIST){	
				$template = "inquiry/form/quickaccess/pharmacist.phtml";	 			
			}else{		
				$template = "inquiry/form/quickaccess/doctor.phtml";	 
			}  
		}		
		
		$inquiry_form = $this->getLayout()->getBlock('inquiry.quickaccess.form')->setFormAction(Mage::getUrl('*/*/postinquiry'))
																->setCode($code)
																->setTemplate($template);
				
		//CHECK IS USER IS LOGGED IN
		if(!Mage::helper('customer')->isLoggedIn()){	
			$response 	 = array();
			$login_block = $this->getLayout()->createBlock("customer/form_login")
											 ->setChild('inquiry.form',$inquiry_form->setIsHidden(true))
											 ->setAjaxPostActionUrl(Mage::getUrl('customer/account/loginajax'))
											 ->setNotificationMessage(Mage::helper('inquiry')->__('You must logged in to post a message.'))->setTemplate('customer/form/loginajax.phtml');
			 
			return  $this->getResponse()->setBody($login_block->toHtml());
			
		}

		$this->renderLayout();
	}

}