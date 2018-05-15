<?php

class Unilab_Standardshipping_StandardController extends Mage_Core_Controller_Front_Action

{

	public function checkcityAction()

	{

	$citycity = $this->getRequest()->getParam('citycity');

        $shiptoaddress          = $this->getRequest()->getParam('shiptoaddress');
        $shipaddressid          = $this->getRequest()->getParam('shipaddressid');
        $shippingregionid       = $this->getRequest()->getParam('shippingregionid');

	$response = Mage::getModel('minimumordervalue/mycarrier_carrier')->isavailable($citycity);


        $addressData    = Mage::getModel('customer/address')->load($shipaddressid);
        $addressData    ->setRegion_id($shippingregionid)
                        ->setCity($citycity)
                        ->setIsDefaultShipping('1')
                        ->save();

        //$result_shipping = $this->getOnepage()->saveShipping($addressData, $shipaddressid);

        //update quote session
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();

        $session        = Mage::getSingleton('checkout/session');
        $quote_id       = $session->getQuoteId();

        if($shiptoaddress == "true"):
        $sqlUpdate 	= "UPDATE sales_flat_quote_address set city='$citycity' WHERE quote_id='$quote_id' AND address_type ='billing'";
        $sqlUpdate_     = "UPDATE sales_flat_quote_address set city='$citycity' WHERE quote_id='$quote_id' AND address_type ='shipping'";

        $connection->query($sqlUpdate);
        $connection->query($sqlUpdate_);
        else:
        //$sqlUpdate      = "UPDATE sales_flat_quote_address set city='$citycity' WHERE quote_id='$quote_id' AND address_type ='billing'";
        $sqlUpdate_     = "UPDATE sales_flat_quote_address set city='$citycity' WHERE quote_id='$quote_id' AND address_type ='shipping'";

        $connection->query($sqlUpdate_);
        endif;

        $connection->commit();


		$result['enablemov'] = $response;

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

	}


}