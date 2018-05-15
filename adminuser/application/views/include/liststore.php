<div class="store-label col-md-12">
	<h3>Select Store</h3>
</div>
<div class="store-menu">
	<div class="col-md-12">
		<?php
		// echo '<pre>';
		// print_r($cookie);
			// print_r($getAllStore);
			$count = 1;  
			
			foreach($getAllStore as $id => $value):
				$imagePath  = '';
				
				// if($id == 'name'){
				$name = $value->name;
				// }
				// if($id == 'store_id'){
				$store_id = $value->store_id;
				// }
				// if($id == 'imagePath'){
				$imagePath = $value->imagePath;
				// }
				// if($id == 'value'){
				$link = $value->value;
				// }
				// 
				//if(!(file_exists($imagePath)))
				//{
			//		$imagePath = base_url().'/template/images/no_image.png';
			//	}
			//	else
			//	{
					if($imagePath == '0')
					{
						$imagePath = base_url().'/template/images/no_image.png';
					}
			//	}
														
				if($count == 1){
					echo '<div class="row img-rows">';
				}
					echo '<div class="col-md-4">
	               <img src="'.$imagePath.'" class="img-responsive clickh-img" alt="Image" onclick="connectdb2('.$store_id.')" >'.$name.'</div>';
				if($count == 3)
				{
					echo'</div>';
					$count =0;
				}
				$count++;
			endforeach;
		?>
	</div>
</div>
<script>
/*
$(window).bind('beforeunload', function(e) {
    if (1)
    {
    $.ajax({
	 url: "<?php base_url()?>index.php/Store",
	cache: false
	})
	.done(function( html ) {
	$( "#menu-container" ).append( html );
	});
       /* e.preventDefault();
       // return false;
       *//*
    }
    
});
*/
</script>
