<?php
	function export_to_csv($sql){
		
		$query = mysql_query($sql);
		$row = mysql_fetch_assoc($query);
		
		$results = array();
		while ($row = mysql_fetch_assoc($query)) {  
		
			$results[] = $row;
				
		} 
		
		return $results;
		
	}
	
	function csv_results($results,$name)
	{ 
		if(count($results) > 0 ){  

			$filename  = $_SERVER['DOCUMENT_ROOT'].'/exportcsvQA/csv/'.$name.'.csv';
			file_put_contents($filename,'');
			chmod($filename, 0777); 
			
			$delimiter = ',';
			$enclosure = '"';

			$outstream = fopen($filename, "w");  
			
			fputcsv($outstream, array_keys($results[0]), $delimiter, $enclosure);

			foreach($results as $result)
			{
				fputcsv($outstream, $result, $delimiter, $enclosure);
			}
			fclose($outstream);
			
			return $name.'.csv';
		}
	}
	
	function sendtoftp($name) 
	{
		$ftphost		= "161.202.200.30";
		$ftpuser		= "dbm";
		$ftppassword 	= "Vd8Ur+nqd]=r";		
		$ftpfolder		= "integrated";	
		
		$file = $_SERVER['DOCUMENT_ROOT'].'/exportcsvQA/csv/'.$name;
		$remote_file = 'csv/' . $ftpfolder . '/'.$name;

		$ftpconnect = ftp_connect($ftphost);

		if($ftpconnect){

		$login_result = ftp_login($ftpconnect, $ftpuser, $ftppassword);

			if($login_result){

				if (ftp_put($ftpconnect, $remote_file, $file, FTP_ASCII)) {

					echo "successfully uploaded $file\n" . "<br>";

				} else {

					echo "There was a problem while uploading $file\n"; 
				}
			}
		}
		else{
			echo "unable to connect";
		}
	}
	
	function import_to_dbm($name)
	{
		
		$ch = curl_init('http://161.202.200.30/dbm/api/integimport/' . $name . '/'); 
		 
		$data = array();
		$data['token'] = '48c38960947075c8345b5912a14106ab';
		$data['hostname'] = 'reg.ecomqa.com';
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		// execute!
		$response = curl_exec($ch);

		// close the connection, release resources used
		curl_close($ch);
		
		return $response; 
	}
?>