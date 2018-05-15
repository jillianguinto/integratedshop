<?php //BACKUP
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('islogged_helper');	

		$this->load->model('Customermod');
		
		$store_id  	= isstore_id();
		
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;			
		
		$this->store_id = store_id($store_id);
		
	}


	public function is_logged(){
		 // echo is_logged();
		// $model = mage::getModel('customer/customer')->getCollection();

		// echo $model->getSelect();
		$user = mage::getModel('customer/customer')->getCollection()->getData();
		echo $user->getEmail();
	}
	 
	public function index()
	{
		$baseurl = base_url();
		redirect($baseurl);
		
	}
	
	public function order()
	{

		$website_ids= null;

		foreach($this->store_id as $_item){

			foreach($_item as $_value){

			$website_ids .= "'$_value',";

			}

		}

		$website_ids = substr($website_ids, 0 , -1);

		$this->load->model('Salesmod');			
		
		$getAllorders 	= $this->Salesmod->getAllorders($website_ids);
		
		$userData = array(
			'getAllorders' 		=> $getAllorders
		);		

		
		$this->load->view('head/head');
		
		$this->load->view('sidebar/menu');
		
		$this->load->view('include/orderlist',$userData);
		
		$this->load->view('footer/footer');
		
	}


	public function exportOr()
	{
		$this->load->model('Salesmod');	
		$store_id  		= isstore_id();
		$getAllordersExport 	= $this->Salesmod->getAllordersExport($store_id); //just change me

		//$queMe = $this->db->last_query();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "orders.csv";
       //	$result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($getAllordersExport, $delimiter, $newline);
        force_download($filename, $data);
        //$tocsv =  $this->dbutil->csv_from_result($getAllcustomer, $delimiter, $newline);
        //force_download($filename, $tocsv);
	}
	public function order_view()
	{
		$store_id  	= isstore_id();
		
		$store_id = str_replace("'","",$store_id);
		$this->load->model('Salesmod');	

		if(isset($_POST['incrementId'])):
			$incrementId =  $_POST['incrementId'];
		else:
			$incrementId = 0;
		endif;
	
		$getOrderByIncrementId 	= $this->Salesmod->getOrderByIncrementId($incrementId, $store_id);

	
		if(empty($getOrderByIncrementId)):
			$baseurl = base_url().'index.php/sales/order';
			redirect($baseurl);
		endif;

		$userData	= $this->Salesmod->loadorderItems($getOrderByIncrementId);

		foreach($getOrderByIncrementId as $customerOrder):
			$history_id = $customerOrder->parent_id;
		endforeach;	
		
		$order_status_history = $this->Salesmod->getOrderHistory($history_id);
		foreach($order_status_history as $key=>$order_history):
			$userData['order_history'][$key] = $order_history;
		endforeach;

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/sales/view');
		$this->load->view('footer/footer');	
		
	}
	


	public function shipment()
	{
	
		$website_ids= null;

		foreach($this->store_id as $_item){

			foreach($_item as $_value){

			$website_ids .= "'$_value',";

			}

		}

		$website_ids = substr($website_ids, 0 , -1);
		
		$this->load->model('Salesmod');		
		$getAllshipping 		= $this->Salesmod->getAllshipping($website_ids);

		$userData = array(
			'getAllshipping' 	=> $getAllshipping
		);				
	
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/shippinglist');
		$this->load->view('footer/footer');
	}
	function exportShip()
	{
		$this->load->model('Salesmod');	
		$store_id  		= isstore_id();
		$getAllshippingExport = $this->Salesmod->getAllshippingExport($store_id); //just change me

		//$queMe = $this->db->last_query();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "shipments.csv";

       	//$result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($getAllshippingExport, $delimiter, $newline);
        force_download($filename, $data);
        //$tocsv =  $this->dbutil->csv_from_result($getAllcustomer, $delimiter, $newline);
        //force_download($filename, $tocsv);
	}

	public function shipment_view()
	{

		$this->load->model('Shippingmod');	
		
		if(isset($_POST['incrementId'])):
			$incrementId =  $_POST['incrementId'];
		else:
			$incrementId = 0;
		endif;
		
		$getShippingDetails 	= $this->Shippingmod->getShippingDetails($incrementId);
		$getShipmentItems 		= $this->Shippingmod->getShipmentItems($incrementId);
		$getShippingAddress 		= $this->Shippingmod->getShippingAddress($incrementId);
		$getBillingAddress 		= $this->Shippingmod->getBillingAddress($incrementId);

		if(empty($getShippingDetails)):
			$baseurl = base_url().'index.php/sales/shipment';
			redirect($baseurl);
		endif;
		
		$userData = array(

			'getShippingDetails' => $getShippingDetails,
			'getShipmentItems' => $getShipmentItems,
			'getShippingAddress' => $getShippingAddress,
			'getBillingAddress' => $getBillingAddress
		);		
		
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/sales/shipment/view');
		$this->load->view('footer/footer');
		
	}	

	public function invoice()
	{			
		
		$website_ids= null;

		foreach($this->store_id as $_item){

			foreach($_item as $_value){

			$website_ids .= "'$_value',";

			}

		}

		$website_ids = substr($website_ids, 0 , -1); 
		$this->load->model('Salesmod');	
		$getAllinvoice 			= $this->Salesmod->getAllinvoice($website_ids);

		$userData = array(
			'getAllinvoice'		=> $getAllinvoice
		);		

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/invoicelist');
		$this->load->view('footer/footer');
	}	
	function exportInv()
	{
		$this->load->model('Salesmod');	
		$store_id  		= isstore_id();
		$getAllinvoiceExport 	= $this->Salesmod->getAllinvoiceExport($store_id); //just change me

		//$queMe = $this->db->last_query();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "invoice.csv";

      // 	$result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($getAllinvoiceExport, $delimiter, $newline);
        force_download($filename, $data);
        //$tocsv =  $this->dbutil->csv_from_result($getAllcustomer, $delimiter, $newline);
        //force_download($filename, $tocsv);
	}
	public function invoice_view()
	{	
		$store_id  	= isstore_id();
		
		$store_id = str_replace("'","",$store_id);
		
		$this->load->model('Salesmod');	
		
		if(isset($_POST['incrementId'])):
			$incrementId =  $_POST['incrementId'];
		else:
			$incrementId = 0;
		endif;		
	
		$getInvoiceByIncrementId = $this->Salesmod->getInvoiceByIncrementId($incrementId, $store_id);	
			// echo $this->db->last_query();
		// die();
		//die();
		if(empty($getInvoiceByIncrementId)):
			$baseurl = base_url().'index.php/sales/invoice';
			redirect($baseurl);
		endif;
		
		
		$userData	= $this->Salesmod->loadinvoiceItems($getInvoiceByIncrementId);
	

		$getItemSInvoiced =  $this->Salesmod->getItemSInvoiced();	
	
		
		
		foreach($getItemSInvoiced as $key=>$item){ 		
			$userData['items_invoiced'][$key] = $item;
		}

		$data = json_decode(json_encode($userData), true);


		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$data);
	
		$this->load->view('include/sales/invoice/view');
		$this->load->view('footer/footer');
		
	}	

	

	

}