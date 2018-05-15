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
		
		sendtoftp($file); 
		
		error_log($name, 3, "../var/log/exportcsv.log");
		
		 
		//$response = import_to_dbm($name); 
		error_log($response, 3, "../var/log/exportcsv_response.log"); 
		
	} 
    
?>
