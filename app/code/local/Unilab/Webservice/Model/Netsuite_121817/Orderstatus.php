<?php

class Unilab_Webservice_Model_Netsuite_Orderstatus extends Mage_Core_Model_Abstract
{
	public function updateToProcessing(){
		try{

		$orderIncrementId = $_POST['orderno'];

			
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

			$orderstatus 	= $order->getStatus();
	
		if ($orderstatus ==	"canceled" || $orderstatus ==	"closed"){

				$response['message'] = 'Order was '.$orderstatus . '. Cannot process';		
				$response['success'] = false;

		}elseif ($orderstatus ==	"complete"){

				$response['message'] = 'Order already complete';		
				$response['success'] = false;  

		}elseif ($orderstatus ==	"processing"){

				$response['message'] = 'Order already processing';		
				$response['success'] = false;  

		}else{

			$items = $order->getItemsCollection();
			$qtys = array(); //this will be used for processing the invoice
			foreach($items as $item){
			    $qty_to_invoice = x; 
			    $qtys[$item->getId()] = $qty_to_invoice;
			}
			
		$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)
			->save();
			 
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			$amount = $invoice->getGrandTotal();
			$invoice->register()->pay();
			$invoice->getOrder()->setIsInProcess(true);
			 
			$history = $invoice->getOrder()->addStatusHistoryComment(
			    'Partial amount of $' . $amount . ' captured automatically.', false
			);
			 
			$history->setIsCustomerNotified(true);
			 
			$order->save();
			 
			Mage::getModel('core/resource_transaction')
			    ->addObject($invoice)
			    ->addObject($invoice->getOrder())
			    ->save();

		   
	

					$response['message'] = "Data was successfully updated.";
					$response['orderno']  = $orderIncrementId;
					$response['success']  = true;

					Mage::log($response['message'], true, 'updateToProcessing_true.log');
					Mage::log($_POST, true, 'update_To_Processing_true.log');
		}

		}catch(Exception $e){ 
			$response['message']  = $e->getMessage();
		  	$response['success']  = false;
			Mage::log($e->getMessage() , true, 'update_To_Processing_false.log');
		} 
		
		
		return $response;

	
	}

	public function updateToComplete()
	{	
		try{
		$orderIncrementId = $_POST['orderno'];

   		

		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

		$orderstatus 	= $order->getStatus();
	
		if ($orderstatus ==	"canceled" || $orderstatus ==	"closed"){

				$response['message'] = 'Order was '.$orderstatus . '. Cannot complete';		
				$response['success'] = false;

		}elseif ($orderstatus ==	"processing"){
			
   		 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$query= "UPDATE  sales_flat_order_grid SET  status = 'complete' WHERE increment_id = '$orderIncrementId'";
   			 	$connection->query($query);
			
	        $order->setData('state', "complete");
	        $order->setStatus("complete");       
	        $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false);
	        $history->setIsCustomerNotified(false);
	        $order->save();

	        
			
	    
					$response['message'] = "Order was successfully updated.";
					$response['orderno']  = $orderIncrementId;
					$response['success']  = true;
 

   		 }elseif($orderstatus ==	"complete"){
				$response['message'] = 'Order already completed';		
				$response['success'] = false;
		}else{		
				$response['message'] = 'Order processing is required';		
				$response['success'] = false;
	             }


		//$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		//$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true)->save();
					
					
					
					Mage::log($response['message'], true, 'updateToComplete_true.log');
					Mage::log($_POST, true, 'update_To_Complete_true.log');
				

		}catch(Exception $e){ 
			$response['message']  = $e->getMessage();
		  	$response['success']  = false;
			Mage::log($e->getMessage() , true, 'update_To_Complete_false.log');
		} 
		
		
		return $response;
	}



}