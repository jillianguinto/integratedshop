<?php
class Invoicemod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }	
	
	function getAllinvoice($store_id)
	{	
		 
		$sql 			= "SELECT * FROM sales_flat_invoice_grid WHERE store_id='$store_id'";
		$sqlResult 		= $this->db->query($sql); 
		return $sqlResult->result();

	}
	
	function getInvoiceByIncrementId($incrementId){	


		$this->db->select('sales_flat_order.*,sales_flat_order_address.*, sales_flat_order_payment.method,sales_flat_order_item.*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order.entity_id');
		$this->db->join('sales_flat_order_item', 'sales_flat_order_item.order_id = sales_flat_order.entity_id');
		// $this->db->join('sales_flat_order_status_history', 'sales_flat_order_status_history.parent_id = sales_flat_order.entity_id');		

		$this->db->where(array('sales_flat_order.increment_id' => $incrementId, 'sales_flat_order.store_id ' => $toreid));
		
		$sqlResult = $this->db->get();
		
		return $sqlResult->result();
	}	


	function loadinvoiceItems($getOrderByIncrementId)
	{
		
		$productItems = array();
		$response = array();
		
		$records =  count($getOrderByIncrementId) / 2;
		$count 	= 0;

		foreach($getOrderByIncrementId as $customerOrder):

			

			if($records == $count):
				break;
			endif;

			$response['subtotal']    			= currency_ph($customerOrder->subtotal);	
			$response['grand_total']    		= currency_ph($customerOrder->grand_total);
			$response['shipping_amount']    	= currency_ph($customerOrder->shipping_amount);
			$response['total_paid']         	= currency_ph($customerOrder->total_paid);
			$response['total_refunded']    		= currency_ph($customerOrder->total_refunded);
			$response['total_due']         		= currency_ph($customerOrder->total_due);

			if($count == 0):    

								
				$productItems[$count]['sku']      = $customerOrder->sku;
				$productItems[$count]['name']     = $customerOrder->name;
				$productItems[$count]['price']                    = $customerOrder->price;
				$productItems[$count]['original_price']          = $customerOrder->original_price;
				$productItems[$count]['total_qty_ordered']        = $customerOrder->qty_ordered; 
				$productItems[$count]['subtotal']            		= $customerOrder->subtotal;
				$productItems[$count]['tax_amount']           	= $customerOrder->tax_amount;
				$productItems[$count]['tax_percent']          	= $customerOrder->tax_percent;
				$productItems[$count]['discount_amount']      	= $customerOrder->discount_amount;
				$productItems[$count]['row_total']           		= $customerOrder->row_total;



				$response['created_at']     	= $customerOrder->created_at;
				$response['status']     		= $customerOrder->status;
				$response['store_name']    		= $customerOrder->store_name;
				$response['remote_ip']    		= $customerOrder->remote_ip;
				$response['customer_firstname']	= $customerOrder->customer_firstname; 
				$response['customer_lastname']	= $customerOrder->customer_lastname; 
				$response['email']            	= $customerOrder->email;
				$response['customer_dob']      	= $customerOrder->customer_dob;

					   

				$response['billing_firstname']  = $customerOrder->firstname;
				$response['billing_lastname']   = $customerOrder->lastname;
				$response['billing_street']     = $customerOrder->street;
				$response['billing_city']      = $customerOrder->city;
				$response['billing_telephone']  = $customerOrder->telephone;
				$response['billing_mobile']     = $customerOrder->mobile;



			else:  

			

				$productItems[$count]['sku']      = $customerOrder->sku;
				$productItems[$count]['name']     = $customerOrder->name;
				$productItems[$count]['price']                    = $customerOrder->price;
				$productItems[$count]['original_price']          = $customerOrder->original_price;
				$productItems[$count]['total_qty_ordered']        = $customerOrder->qty_ordered; 
				$productItems[$count]['subtotal']            		= $customerOrder->subtotal;
				$productItems[$count]['tax_amount']           	= $customerOrder->tax_amount;
				$productItems[$count]['tax_percent']          	= $customerOrder->tax_percent;
				$productItems[$count]['discount_amount']      	= $customerOrder->discount_amount;
				$productItems[$count]['row_total']           		= $customerOrder->row_total;

				$response['shipping_firstname']  = $customerOrder->firstname;
				$response['shipping_lastname']   = $customerOrder->lastname;
				$response['shipping_street']    = $customerOrder->street;
				$response['shipping_city']       = $customerOrder->city;
				$response['shipping_telephone']  = $customerOrder->telephone;
				$response['shipping_mobile']    = $customerOrder->mobile;

			endif;


			$response['method'] 				= $customerOrder->method;
			$response['shipping_description']   = $customerOrder->shipping_description;
			$response['base_shipping_amount']   = $customerOrder->base_shipping_amount;
			$response['unilab_waybill_number']  = $customerOrder->unilab_waybill_number;
			$response['productItems'] = $productItems;

			$count++;

		endforeach;		


		if(empty($response['shipping_firstname'])):

			$response['shipping_firstname']  = $response['billing_firstname'];
			$response['shipping_lastname']   = $response['billing_lastname'];
			$response['shipping_street']    = $response['billing_street'];
			$response['shipping_city']       = $response['billing_city'];
			$response['shipping_telephone']  = $response['billing_telephone'];
			$response['shipping_mobile']    = $response['billing_mobile'];

		endif;

		return $response;


	}



}
?>