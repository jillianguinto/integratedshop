<?php
class Salesmod extends CI_Model {


	protected  $parent_id;

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }
    
    function getadminwebsite(){
    	
    		$sql 			= "SELECT store_id FROM core_store WHERE name='Athena'";
		$sqlResult 		= $this->db->query($sql); 
		
		return $sqlResult->result();
    
    	}	
    	

	function getAllsales($store_id)
	{	

		$sql 			= "SELECT * FROM sales_flat_order_grid WHERE store_id in($store_id)";
		$sqlResult 		= $this->db->query($sql); 

		return $sqlResult->num_rows();

	}
	
	function getAllorders($store_id)
	{	


		$sql 		= "SELECT * FROM sales_flat_order_grid WHERE store_id in($store_id)";

		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult->result();

	}	
	
	function getAllordersExport($store_id)
	{	


		$sql 		= "SELECT * FROM sales_flat_order_grid WHERE store_id in($store_id)";

		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult;

	}
	
	function getAllshipping($store_id)
	{	

		$sql 			= "SELECT * FROM sales_flat_shipment_grid WHERE store_id in($store_id)";
		$sqlResult 		= $this->db->query($sql); 	
		return $sqlResult->result();

	}	
	function getAllshippingExport($store_id)
	{	

		$sql 			= "SELECT * FROM sales_flat_shipment_grid WHERE store_id in($store_id)";
		$sqlResult 		= $this->db->query($sql); 	
		return $sqlResult;

	}	
	
	function getAllinvoice($store_id)
	{	
		 // $store_id  = str_replace(',',"','", "'".$store_id."'"); 
		$sql 			= "SELECT * FROM sales_flat_invoice_grid WHERE store_id in($store_id)";
		$sqlResult 		= $this->db->query($sql); 
		// echo $this->db->last_query();
		return $sqlResult->result();

	}
	function getAllinvoiceExport($store_id)
	{	
		 // $store_id  = str_replace(',',"','", "'".$store_id."'"); 
		$sql 			= "SELECT * FROM sales_flat_invoice_grid WHERE store_id in($store_id)";
		$sqlResult 		= $this->db->query($sql); 
		// echo $this->db->last_query();
		return $sqlResult;

	}


	function get_sales_flat_order(){
		$this->db->select('*');
		$this->db->from('sales_flat_order_grid');
		// $this->db->where("sales_flat_order_grid.customer_id=1161");
		$sqlResult = $this->db->get();			
		return $sqlResult->result();

	}

	function getShipmentByIncrementId($incrementId){	


		$this->db->select('*, sales_flat_order_payment.method,sales_flat_order_item.sku, sales_flat_order_item.name, sales_flat_order_item.price');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id');
		$this->db->join('sales_flat_order_item', 'sales_flat_order_item.order_id = sales_flat_order.entity_id');

		$this->db->where("sales_flat_order.increment_id='$incrementId'");
		
		$sqlResult = $this->db->get();			
		return $sqlResult->result();

		// $this->db->select('*, sales_flat_shipment_grid.increment_id,
		// 					sales_flat_shipment.increment_id,
		// 					sales_flat_shipment.entity_id,
		// 					sales_flat_shipment_item.parent_id,
		// 					sales_flat_order_address.parent_id,
		// 					sales_flat_order_payment.parent_id');
		// $this->db->from('sales_flat_order');
		// $this->db->join('sales_flat_shipment_grid', 'sales_flat_order.increment_id = sales_flat_shipment_grid.order_increment_id');		
		// $this->db->join('sales_flat_shipment', 'sales_flat_shipment_grid.increment_id = sales_flat_shipment.increment_id');
		// $this->db->join('sales_flat_shipment_item', 'sales_flat_shipment_item.parent_id = sales_flat_shipment.entity_id');
		// $this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');
		// $this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id');
		// echo $incrementId;
		// $this->db->where("sales_flat_shipment_grid.order_increment_id='$incrementId'");


		// $sqlResult = $this->db->get();			
		// return $sqlResult->result();
	
		}
	function getShipmentItem($incrementId) {
		// $sql 			= "SELECT * FROM sales_flat_shipment_item WHERE increment_id = '$incrementId'";
		// $sqlResult		= $this->db->query($sql);
		// return $sqlResult->result();

		$this->db->select('sales_flat_shipment_grid.increment_id,
							sales_flat_shipment.increment_id,
							sales_flat_shipment.entity_id,
							sales_flat_shipment_item.*,
							sales_flat_order_address.*,
							sales_flat_order_payment.*,
							sales_flat_order.*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_shipment_grid', 'sales_flat_order.increment_id = sales_flat_shipment_grid.order_increment_id');		
		$this->db->join('sales_flat_shipment', 'sales_flat_shipment_grid.increment_id = sales_flat_shipment.increment_id');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id');

		$this->db->where("sales_flat_shipment_grid.order_increment_id='$incrementId'");

		$sqlResult = $this->db->get();			
		return $sqlResult->result();

	}

	
		
	function getOrderByIncrementId($incrementId, $toreid){	

		$this->db->select('sales_flat_order.*,sales_flat_order_address.*, sales_flat_order_payment.method,sales_flat_order_item.*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order.entity_id');
		$this->db->join('sales_flat_order_item', 'sales_flat_order_item.order_id = sales_flat_order.entity_id');
		$this->db->where(array('sales_flat_order.increment_id' => $incrementId));
        $this->db->where_in('sales_flat_order.store_id', $toreid);		
		
		
		$sqlResult = $this->db->get();		
		
		return $sqlResult->result();

	}


	function getInvoiceByIncrementId($incrementId, $toreid){		

		$this->db->select('sales_flat_order.*, sales_flat_order_address.*,sales_flat_invoice.*, sales_flat_order_payment.method');
		//$this->db->select('sales_flat_order.*, sales_flat_order_address.*,sales_flat_invoice.*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id');
		$this->db->join('sales_flat_invoice', 'sales_flat_invoice.order_id = sales_flat_order.entity_id');	
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id');
		$this->db->where(array('sales_flat_order.entity_id' => $incrementId));
        $this->db->where_in('sales_flat_order.store_id',$toreid);		
		
	
		$sqlResult = $this->db->get();		
		$data = $sqlResult->result_array();

		$this->parent_id = $data[0]['entity_id'];
		
		return $sqlResult->result();
	}


	public function getItemSInvoiced(){

		$sql 			= "SELECT * FROM sales_flat_invoice_item WHERE parent_id = $this->parent_id";	
		$sqlResult		= $this->db->query($sql);
		return $sqlResult->result();
	} 

	function loadorderItems($getOrderByIncrementId)
	{
		
		$productItems = array();
		$response = array();
		
		$records =  count($getOrderByIncrementId) / 2;
		$count 	= 0;

		
		foreach($getOrderByIncrementId as $customerOrder){			

			if($records == $count){
				break;
			}



			$response['increment_id']  							= $customerOrder->increment_id;	
			$response['subtotal']    							= currency_ph($customerOrder->subtotal);	
			$response['grand_total']    						= currency_ph($customerOrder->grand_total);
			$response['shipping_amount']    					= currency_ph($customerOrder->shipping_amount);
			$response['total_paid']         					= currency_ph($customerOrder->total_paid);
			$response['total_refunded']    						= currency_ph($customerOrder->total_refunded);
			$response['total_due']         						= currency_ph($customerOrder->total_due);

			if($count == 0){    

				$productItems[$count]['sku']      				= $customerOrder->sku;
				$productItems[$count]['name']     				= $customerOrder->name;
				$productItems[$count]['price']                  	= $customerOrder->price;
				$productItems[$count]['original_price']         	= $customerOrder->original_price;
				$productItems[$count]['total_qty_ordered']      	= $customerOrder->qty_ordered; 
				$productItems[$count]['subtotal']           		= $customerOrder->subtotal;
				$productItems[$count]['tax_amount']           	= $customerOrder->tax_amount;
				$productItems[$count]['tax_percent']          	= $customerOrder->tax_percent;
				$productItems[$count]['discount_amount']      	= $customerOrder->discount_amount;
				$productItems[$count]['row_total']           	= $customerOrder->row_total;


				$response['created_at']     					= $customerOrder->created_at;
				$response['status']     						= $customerOrder->status;
				$response['store_name']    						= $customerOrder->store_name;
				$response['remote_ip']    						= $customerOrder->remote_ip;
				$response['customer_firstname']					= $customerOrder->customer_firstname; 
				$response['customer_lastname']					= $customerOrder->customer_lastname; 
				$response['email']            					= $customerOrder->email;
				$response['customer_dob']      					= $customerOrder->customer_dob;
					   

				$response['billing_firstname'] 					= $customerOrder->firstname;
				$response['billing_lastname']   				= $customerOrder->lastname;
				$response['billing_street']     				= $customerOrder->street;
				$response['billing_city']       				= $customerOrder->city;
				$response['billing_telephone']  				= $customerOrder->telephone;
				$response['billing_mobile']     				= $customerOrder->mobile;
				

			}else{

				$productItems[$count]['sku']      				= $customerOrder->sku;
				$productItems[$count]['name']     				= $customerOrder->name;
				$productItems[$count]['price']              	= $customerOrder->price;
				$productItems[$count]['original_price']     	= $customerOrder->original_price;
				$productItems[$count]['total_qty_ordered']		= $customerOrder->qty_ordered; 
				$productItems[$count]['subtotal']           	= $customerOrder->subtotal;
				$productItems[$count]['tax_amount']         	= $customerOrder->tax_amount;
				$productItems[$count]['tax_percent']        	= $customerOrder->tax_percent;
				$productItems[$count]['discount_amount']    	= $customerOrder->discount_amount;
				$productItems[$count]['row_total']          	= $customerOrder->row_total;


				$response['shipping_firstname']					= $customerOrder->firstname;
				$response['shipping_lastname']  				= $customerOrder->lastname;
				$response['shipping_street']    				= $customerOrder->street;
				$response['shipping_city']      				= $customerOrder->city;
				$response['shipping_telephone']					= $customerOrder->telephone;
				$response['shipping_mobile']    				= $customerOrder->mobile;


			}

			$response['method'] 								= $customerOrder->method;
			$response['shipping_description']   				= $customerOrder->shipping_description;
			$response['base_shipping_amount']   				= $customerOrder->base_shipping_amount;
			$response['unilab_waybill_number']  				= $customerOrder->unilab_waybill_number;
			$response['productItems'] 						= $productItems;

			$count++;




		}


		if(empty($response['shipping_firstname'])){

			$response['shipping_firstname']  					= $response['billing_firstname'];
			$response['shipping_lastname']   					= $response['billing_lastname'];
			$response['shipping_street']     					= $response['billing_street'];
			$response['shipping_city']       					= $response['billing_city'];
			$response['shipping_telephone']  					= $response['billing_telephone'];
			$response['shipping_mobile']     					= $response['billing_mobile'];

		}

		return $response;

	}



	function loadinvoiceItems($getOrderByIncrementId){
		// echo '<pre>';
		// print_r($getOrderByIncrementId);
		// die();
		$productItems = array();
		$response = array();
		
		$records =  count($getOrderByIncrementId) / 2;
		$count 	= 0;

		
		foreach($getOrderByIncrementId as $customerOrder){			

			if($records == $count){
				break;
			}



			$response['increment_id']  							= $customerOrder->increment_id;	
			$response['subtotal']    							= currency_ph($customerOrder->subtotal);	
			$response['grand_total']    						= currency_ph($customerOrder->grand_total);
			$response['shipping_amount']    					= currency_ph($customerOrder->shipping_amount);
			$response['total_paid']         					= currency_ph($customerOrder->total_paid);
			$response['total_refunded']    						= currency_ph($customerOrder->total_refunded);
			$response['total_due']         						= currency_ph($customerOrder->total_due);

			if($count == 0){    

				// $productItems[$count]['sku']      				= $customerOrder->sku;
				// $productItems[$count]['name']     				= $customerOrder->name;
				// $productItems[$count]['price']                  	= $customerOrder->price;
				// $productItems[$count]['original_price']         	= $customerOrder->original_price;
				// $productItems[$count]['total_qty_ordered']      	= $customerOrder->qty_ordered; 
				// $productItems[$count]['subtotal']           		= $customerOrder->subtotal;
				// $productItems[$count]['tax_amount']           	= $customerOrder->tax_amount;
				// $productItems[$count]['tax_percent']          	= $customerOrder->tax_percent;
				// $productItems[$count]['discount_amount']      	= $customerOrder->discount_amount;
				// $productItems[$count]['row_total']           	= $customerOrder->row_total;
						
				
				$response['shipping_invoiced']      			= $customerOrder->shipping_invoiced;
				$response['grand_total']           				= $customerOrder->grand_total;
				$response['subtotal_invoiced']      			= $customerOrder->subtotal_invoiced;

				//die();
				
				$response['created_at']     					= $customerOrder->created_at;
				$response['status']     						= $customerOrder->status;
				$response['store_name']    						= $customerOrder->store_name;
				$response['remote_ip']    						= $customerOrder->remote_ip;
				$response['customer_firstname']					= $customerOrder->customer_firstname; 
				$response['customer_lastname']					= $customerOrder->customer_lastname; 
				$response['email']            					= $customerOrder->email;
				$response['customer_dob']      					= $customerOrder->customer_dob;
					   

				$response['billing_firstname'] 					= $customerOrder->firstname;
				$response['billing_lastname']   				= $customerOrder->lastname;
				$response['billing_street']     				= $customerOrder->street;
				$response['billing_city']       				= $customerOrder->city;
				$response['billing_telephone']  				= $customerOrder->telephone;
				$response['billing_mobile']     				= $customerOrder->mobile;
				

			}else{

				// $productItems[$count]['sku']      				= $customerOrder->sku;
				// $productItems[$count]['name']     				= $customerOrder->name;
				// $productItems[$count]['price']              	= $customerOrder->price;
				// $productItems[$count]['original_price']     	= $customerOrder->original_price;
				// $productItems[$count]['total_qty_ordered']		= $customerOrder->qty_ordered; 
				// $productItems[$count]['subtotal']           	= $customerOrder->subtotal;
				// $productItems[$count]['tax_amount']         	= $customerOrder->tax_amount;
				// $productItems[$count]['tax_percent']        	= $customerOrder->tax_percent;
				// $productItems[$count]['discount_amount']    	= $customerOrder->discount_amount;
				// $productItems[$count]['row_total']          	= $customerOrder->row_total;


				$response['shipping_invoiced']      			= $customerOrder->shipping_invoiced;
				$response['grand_total']           				= $customerOrder->grand_total;
				$response['subtotal_invoiced']      			= $customerOrder->subtotal_invoiced;


				$response['shipping_firstname']					= $customerOrder->firstname;
				$response['shipping_lastname']  				= $customerOrder->lastname;
				$response['shipping_street']    				= $customerOrder->street;
				$response['shipping_city']      				= $customerOrder->city;
				$response['shipping_telephone']					= $customerOrder->telephone;
				$response['shipping_mobile']    				= $customerOrder->mobile;


			}

			$response['method'] 								= $customerOrder->method;
			$response['shipping_description']   				= $customerOrder->shipping_description;
			$response['base_shipping_amount']   				= $customerOrder->base_shipping_amount;
			$response['unilab_waybill_number']  				= $customerOrder->unilab_waybill_number;
			$response['productItems'] 							= $productItems;

			$count++;




		}


		if(empty($response['shipping_firstname'])){

			$response['shipping_firstname']  					= $response['billing_firstname'];
			$response['shipping_lastname']   					= $response['billing_lastname'];
			$response['shipping_street']     					= $response['billing_street'];
			$response['shipping_city']       					= $response['billing_city'];
			$response['shipping_telephone']  					= $response['billing_telephone'];
			$response['shipping_mobile']     					= $response['billing_mobile'];

		}

		return $response;
	} 



	public function getOrderHistory($history_id){
		
		$sql 			= "SELECT * FROM sales_flat_order_status_history WHERE parent_id = $history_id";
		$sqlResult 		= $this->db->query($sql); 
		return $sqlResult->result();

	}
	

}
?>