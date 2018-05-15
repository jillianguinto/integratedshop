<?php

class Unilab_Webservice_Helper_Data extends Mage_Core_Helper_Abstract
{	
	const ADD_CUSTOMER 			= 'AddCustomer';       		
	const UPDATE_CUSTOMER		= 'UpdateCustomer';
	const CUSTOMER_LOGIN  		= 'CustomerLogin';		
	const CUSTOMER_LOGOUT  		= 'CustomerLogout';		
	const FORGOT_PASSWORD 		= 'ForgotPassword';		
	const CHANGE_PASSWORD 		= 'ChangePassword';	

	const PRODUCT_VIEW 			= 'ProductView';
	const CATALOG_SEARCH 		= 'CatalogSearch';
	const ACCOUNT_VALIDATION 	= 'AccountValidation';	
	const SAVE_ACCOUNT 			= 'SaveAccount';	

	const EXISTING_CUSTOMER 	= 'ExistingCustomer';	
	const CUSTOMER_LOGINFB  	= 'CustomerLoginFB';	
	
	const MIGRATION  			= 'DataMigration';	//migration
	
	//NETSUITE
	const PROCESS_ORDER 		= 'ProcessOrder'; 
	const COMPLETE_ORDER 		= 'CompleteOrder'; 
	const CREATE_PRODUCT 		= 'CreateProduct';   
	const UPDATE_PRODUCT 		= 'UpdateProduct'; 
	const UPDATE_PRODUCT_PRICE 	= 'UpdateProductPrice'; 
	const UPDATE_PRODUCT_STATUS = 'UpdateProductStatus';  
	
	const CATEGORY		 		= 'AddCategory'; 
	const TYPE			 		= 'AddType'; 
	const FORMAT		 		= 'AddFormat';   
	const BENEFIT		 		= 'AddBenefit'; 
	const SEGMENT			 	= 'AddSegment'; 
	const DIVISION				= 'AddDivision';  
	const GROUP					= 'AddGroup'; 

	const GET_PRODUCT					= 'GetProduct';   	



}