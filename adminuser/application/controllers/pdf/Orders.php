<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');	
	}
	 
	public function index()
	{
		
		$this->load->model('Salesmod');	
		$sessionid 			= $this->input->cookie('useraccount',true);
		
		if(!empty($sessionid)):
			$datainfo 		= explode(",",$sessionid);
			$store_id 		= $datainfo[2];
		else:
			$store_id 		= 0;
		
		endif;
				
		$getAllorders 	= $this->Salesmod->getAllorders($store_id);
		
		$datenow				= date("y_m_d_H_i_s");
		$pdffilename 			= "orders_".$datenow.".pdf";
		$pdfFilePath			= FCPATH."downloads/".$pdffilename;
		$data['page_title'] 	= 'PDF';
		
		if(file_exists($pdfFilePath) == false)
		{
			
			ini_set('memory_limit','128M');
			$this->load->library('pdf');
			$pdf = 	$this->pdf->load();
			$pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822));
			
			$html = "<table border='0' cellpadding='0' cellspacing='0' style='border:solid 1px #000;'>";
			
			$html .= "<tr>";
			$html .= "<td  style='border:solid 1px #000;'>Order Number</td>";
			$html .= "<td style='border:solid 1px #000;'>Customer Name</td>";
			$html .= "<td  style='border:solid 1px #000;'>Grant Total</td>";
			$html .= "<td  style='border:solid 1px #000;'>Total Paid</td>";
			$html .= "<td  style='border:solid 1px #000;'>Date</td>";
			$html .= "<td  style='border:solid 1px #000;'>Status</td>";
			$html .= "</tr>";
			
			foreach($getAllorders as $_data):
			
				if(!empty($_data->increment_id)):
					$html .= "<tr>";
					$html .= '<td style="border:solid 1px #000;">'.$_data->increment_id.'</td>';
					$html .= '<td style="border:solid 1px #000;">'.$_data->shipping_name.'</td>';
					$html .= '<td style="border:solid 1px #000;">'.$_data->grand_total.'</td>';
					$html .= '<td style="border:solid 1px #000;">'.$_data->total_paid.'</td>';
					$html .= '<td style="border:solid 1px #000;">'.$_data->created_at.'</td>';
					$html .= '<td style="border:solid 1px #000;">'.$_data->status.'</td>';
					$html .= "</tr>";	
				endif;				
				
			endforeach;
			
			$html .= "</table>";
			
			$pdf->WriteHTML($html);
			$pdf->Output($pdfFilePath, "F");	

		}		

		header("Content-type: application/pdf");
		header('Content-Disposition: attachment; filename="'.$pdffilename.'"');
		readfile($pdfFilePath);
		unlink($pdfFilePath);
		redirect("sales/order");
		
	}
}
