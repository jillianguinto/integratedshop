<?php



class Unilab_Minimumordervalue_EmowviController extends Mage_Core_Controller_Front_Action

{
	public function indexAction()
	{
				echo "asda";
	}

	public function checkcityAction()

	{

		$citycity = $this->getRequest()->getParam('citycity');

		$response = Mage::getModel('minimumordervalue/mycarrier_carrier')->addData($citycity)->isavailable();

		// $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

		

		// $select 	= $connection->select()->from('rra_eventproduct_items', array('*'))

		// 			->where('eventproductid=?',$sub_events);



		// $ResultArr 	= $connection->fetchAll($select); 

		

		// $connection->commit();

		

		// $html = null;

		// foreach($ResultArr as $value)

		// {

		// 	$product_id = $name = $value['productid'];

		// 	$product    = Mage::getModel('catalog/product')->load($product_id);



		// 	$id = $product->getId();

		// 	$name = $product->getName();

		// 	$html .= "<option value='$id'>$name</option>";

		// }

		

		// $html .= "<option value='' disabled='' selected='' style='display: none;'> -- Select Race Category -- </option>";

		

		// $response['racelist'] = $html;

		// if($citycity == "Valenzuela"){
		// 	$result = true;
		// }else{
		// 	$result = false;
		// }


		//$response['enablemov'] = $result;

		

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

	}


}