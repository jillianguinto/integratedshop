<?php
class Shippingmod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }

    function getShippingDetails($incrementId) {
    
		$this->db->select('*');
		$this->db->from('sales_flat_order');
		//$this->db->join('sales_flat_shipment_grid', 'sales_flat_shipment_grid.order_increment_id = sales_flat_order.incremen_id','left');	
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id','left');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id','left');
		
		$this->db->where("sales_flat_order.increment_id='$incrementId'");
		
		$sqlResult = $this->db->get();			
		return $sqlResult->result();
    }

    function getShipmentItems($incrementId) {
    	
    	$this->db->select('*');
		$this->db->from('sales_flat_shipment_grid');
		$this->db->join('sales_flat_shipment', 'sales_flat_shipment_grid.increment_id  = sales_flat_shipment.increment_id','left');		
		$this->db->join('sales_flat_shipment_item', 'sales_flat_shipment_item.parent_id = sales_flat_shipment.entity_id','left');

		$this->db->where("sales_flat_shipment_grid.order_increment_id='$incrementId'");
		
		$sqlResult = $this->db->get();			
		return $sqlResult->result();


    }

    function getShippingAddress($incrementId) {
    	$this->db->select('*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id','left');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id','left');
		
		$this->db->where("sales_flat_order.increment_id='$incrementId'");
		$this->db->where("sales_flat_order_address.address_type = 'shipping'");
		
		$sqlResult = $this->db->get();			
		return $sqlResult->result();

    }

    function getBillingAddress($incrementId) {
    	$this->db->select('*');
		$this->db->from('sales_flat_order');
		$this->db->join('sales_flat_order_address', 'sales_flat_order_address.parent_id = sales_flat_order.entity_id','left');		
		$this->db->join('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = sales_flat_order_address.parent_id','left');
		
		$this->db->where("sales_flat_order.increment_id='$incrementId'");
		$this->db->where("sales_flat_order_address.address_type = 'billing'");
		
		$sqlResult = $this->db->get();			
		return $sqlResult->result();
    }
}