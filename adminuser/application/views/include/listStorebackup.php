<div class="store-label col-md-12">
	<h3>Select Store</h3>
</div>
<div class="store-menu">
	<div class="col-md-12">
		<?php
			// print_r($getAllStore);
			$count = 1;
			
			foreach($getAllStore as $_list):
				$img = '';
				$img = FCPATH . 'template/images/'.strtolower($_list->name).'.png';

				if(!file_exists($img)){
					
					$img = base_url().'template/images/unilab.gif';

				}else{

					$img = base_url(). 'template/images/'.strtolower($_list->name).'.png';
				}

				// else{
					// $img = base_url().'/template/images/unilab.gif';
				// }											
				if($count == 1){
					echo '<div class="row img-rows">';
				}
					echo '<div class="col-md-4">
	               <img src="'.$img.'" class="img-responsive clickh-img" alt="Image" onclick="connectdb2('.$_list->website_id.',0)" >'.$_list->name.'</div>';
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
