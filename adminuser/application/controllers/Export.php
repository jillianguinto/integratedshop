<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//controller
class Export extends CI_Controller {
 public function exportToExcel() {
 $data = $_POST['data'];
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Transfer-Encoding: binary");
header("Content-Language: en");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=data.xls");
echo $data;
 }
	
}