<?php
    ini_set('memory_limit', '1024M');
	include 'config.php';
	include 'model.php';
	
	$sql = "SELECT * from cleansql_report where type='DBM' order by report_id"; 
	$query = mysql_query($sql);

	while ($result = mysql_fetch_assoc($query)) {  
		
		$dbm_query 	= $result['sql_query']; 
		$title 		= strtolower($result['title']);
		$name 		= str_replace(' ','_',$title);
		
		
		$rsqry = export_to_csv($dbm_query);
		
		$file = csv_results($rsqry, $name); 
		
		//sendtoftpAdobe($file); 
		
		//error_log($name, 3, "../var/log/exportcsv_adobe.log");

		
	}
	
	
	function sendtoftpAdobe($name) 
	{
		$ftphost		= "161.202.200.23";
		$ftpuser		= "sftpuser";
		$ftppassword 	= "password1@";	
		$ftpfolder		= "integrated";	
		
		$file = $_SERVER['DOCUMENT_ROOT'].'/exportcsv/csv/'.$name;
		$remote_file = 'public_html/csv/' . $ftpfolder . '/'.$name;

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
    
?>
