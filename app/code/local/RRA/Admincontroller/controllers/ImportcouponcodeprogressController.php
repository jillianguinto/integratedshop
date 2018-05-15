<?php 

class RRA_Admincontroller_ImportcouponcodeprogressController extends Mage_Core_Controller_Front_Action
{ 


	public function applycouponAction()
	{
		
		$couponCode = null;
		
	   Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
			->collectTotals()
			->save();
	
	}

	public function IndexAction()
	{
		
		$id = $_GET['id'];
		
		$filecsv = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcode';
		$csv = file_get_contents($filecsv);
		
		$filehead = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodehead';
		$head = file_get_contents($filehead);	

		$filecount = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodecount';

		$dataArr 			= array();		
		$dataArr['csv'] 	= json_decode($csv);
		$dataArr['head'] 	= json_decode($head);	
		$dataArr['id'] 		= json_decode($id);	

		
		$SaveData = Mage::getModel('admincontroller/importcouponcode')->addData($dataArr)->processData();
		$records  	= Mage::getSingleton('core/session')->getRecords();	
		$status  	= Mage::getSingleton('core/session')->getStatussave();
		Mage::getSingleton('core/session')->setsavecount($records['Savecount']); 
		file_put_contents($filecount,$records['Savecount']);
		
		$percent = $records['Savecount'] / $records['Allrecords'];
		$percent = $percent * 100;
		
		$resData = Mage::getSingleton('core/session')->getRecordsave();

		?>

			<div class="progressBar" style="width:99%">
			
				<?php 	if( $records['Savecount']  != $records['Allrecords']): ?>
				
					<label style="font-family:arial; font-size:14px;"><b>
					
					<?php 
					
						foreach($resData as $_value):
						
							echo $_value.'<br/>'; 
						
						endforeach;
					
						
					
					?>
									
					<p style="padding:10px 5px 10px 5px; width:<?php echo $percent?>%; font-family:arial; font-size:14px; background-color:#70BE32; background-image:url(<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'loading/animation.gif'?>); text-align:center; ">
				
				<?php else: ?>
				
				
					<label style="font-family:arial; font-size:14px;"><b>
					
					<?php 
					
						foreach($resData as $_value):
						
							echo $_value.'<br/>'; 
						
						endforeach;
					
					?>				
				
					<p style="padding:10px 5px 10px 5px; width:100%; font-family:arial; color: #fff; border: 1px solid #000; background-color:#70BE32; text-align:center; ">

				<?php endif; ?>
				
					
					<?php 
					
						if( $records['Savecount']  != $records['Allrecords']):
						
							if($percent >= 30):
							
								echo 'Uploading <b>'. $records['Savecount'].' / '.$records['Allrecords']." </b> records."; 
								echo ' [ '.round($percent, 1).'% ]  Please wait...';
								
							elseif($percent >= 6 && $percent <= 10):
							
								echo $records['Savecount'].' / '.$records['Allrecords']; 
								
							elseif($percent >= 10 && $percent <= 30):
							
								echo $records['Savecount'].' / '.$records['Allrecords']; 
								echo ' [ '.round($percent, 1).'% ]';
								
							elseif($percent <= 1):
							
								echo '<span style="font-size:9px;">'.round($percent, 1).'%</span>';
								
							else:
							
								echo '<span style="font-size:12px;">'.round($percent, 0).'%</span>';
								
							endif;
							
						else:
						
							echo '[ '.$records['Savecount'].' / '.$records['Allrecords'].' ] '. round($percent, 2)."% DONE!"; 
							
							Mage::getSingleton('core/session')->unsRecordsave();
							
						endif;
					
					?> 

				</p>
				
			</div>

		<?php		
		
		
			if ($records['Savecount'] != $records['Allrecords']):
			
				?>
				
					<script>
						document.location.reload(true);
					</script>
				
				<?php
			
			endif;

		
	}
	



}
