getConnection('core_write'); 
$mysqli->query($sql); 
$process = Mage::getModel('index/process')->load(1); 
$process->reindexAll(); 
Mage::log("Finished Rebuilding Search Index At: " . date("d/m/y h:i:s")); 