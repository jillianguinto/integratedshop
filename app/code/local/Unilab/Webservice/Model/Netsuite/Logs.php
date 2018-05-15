<?php
date_default_timezone_set('Asia/Taipei');
class Unilab_Webservice_Model_Netsuite_Logs extends Mage_Core_Model_Abstract
{	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	 
		
        return $this->_Connection;
    }	
	
	public function createlogs($id,$from,$cmdEvent,$status,$message){
		$currentdate = date("Y-m-d H:i:s");   

		$data = array(
					'transaction_id' 	=>  $id,
					'cmdEvent'			=>	$cmdEvent,
					'data_from'			=>	$from,
					'status' 			=>  $status,
					'logs'				=>  $message,
					'date'				=>	$currentdate,
					'type'				=>  $type
					 );

 		$response = $this->_getConnection()->insert('unilab_logs_netsuite', $data);

 		return $response;
	}
	public function createLogsNetsuite(){
		$logs = json_decode($_POST['response']);	
		$response = array();
		foreach($logs as $key => $value)
				{
				    $log[$key] = (array) $value;
				} 
				if (!$log['status'][0] || $log['status'] == false ) {
					$log['status'] = 0;

				}else{
					$log['status'] = $log['status'][0] ;
				}
				Mage::log($log,null,"logssawd.log");

				// if(!isset($log['message'])){
				// 	$log['message'] = $log['error']['message'];
				// }else{
				// 	$log['message'] = $log['message'][0] ;
				// }
				if(isset($_POST['orderno'])){

					$id 					= $_POST['orderno'];
					$response['orderno'] 	= $id;

				}elseif(isset($_POST['customerId'])){

					$id 					= $_POST['customerId'];
					$response['customerId'] 	= $id;

				}elseif(isset($_POST['addressId'])){

					$id 					= $_POST['addressId'];
					$response['addressId'] 	= $id;

				} 


				$response = $this->createlogs( $id ,"Netsuite",$_POST['cmdEventTriggered'],$log['status'] ,json_encode($_POST['response']),'send');	
				if($response == 1){
				 	$response['message'] = "Log was successfully saved in Magento.";
					$response['success']  = true;
				}
				Mage::log($response ,null,"saveresponse.log");

				return json_encode($response);


	}



}