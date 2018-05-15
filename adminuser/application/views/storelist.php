<head>
  <title>E-commerce user admin</title>
    <!-- Bootstrap Core CSS -->
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/sb-admin.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/plugins/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="http://clickhealth.ecomqa.com/adminuser/template/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/styles.css" rel="stylesheet">
    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/font-awesome-animation.min.css" rel="stylesheet">

    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/modal.css" rel="stylesheet">

    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/product-upload.css" rel="stylesheet">

    <link href="http://clickhealth.ecomqa.com/adminuser/template/css/datatables.responsive.css" rel="stylesheet">
	<script type="text/javascript" src="http://clickhealth.ecomqa.com/adminuser/template/js/jquery.min.js"></script>   
	<script src="http://clickhealth.ecomqa.com/adminuser/template/js/jquery-ui.min.js"></script> 
	
	<script>
	function connectdb2(store) 
{
	location.href = 'http://clickhealth.ecomqa.com/adminuser/'; 
					// $.post('<?php base_url()?>login/',{
						// username: $("#username").val(), stid: store,
						
					// }, function (data){

						// if(data.username == false){
							// $("#admin_config").show();
							// $(".panel-body").show();
							// $("#please_wait").hide();	
							// $('#errorHandler').html('Invalid Account!');						
							// $('#errorHandler').show();
							// alert("your account is not permitted!");
							// location.reload();
						// }else{
							// location.href = '<?php base_url()?>adminuser';
							// console.log('<?php base_url()?>adminuser');
						// }
						
					// });				
			
			

			
		
		}

	
	</script>

</head>
<body style="background-color:#fff">
<style>
    .img-rows{
        text-align: center; 
    }
    .store-menu img{
        width: 60%;
        cursor: pointer;

    }
    .store-menu{
        padding: 10%;
        padding-top: 5%;
    }
    .store-label{
        text-align: center;

    }
   
    .clickh-img{
        width: 36%;
    }


</style>
<nav style="    background-color: #337AB7;
    border-color: #337AB7;"class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
	                    <span class="sr-only">Admin Panel</span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                </button>
	                <a id="sstore" style="color:#fff" class="navbar-brand" href="#" data-toggle="modal">Admin Panel 
											</a>
	            </div>
				
	            <ul class="nav navbar-right top-nav">

	                <li class="dropdown">
	                    <a href="#" style="#fff" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-user"></i> 				
							Please login!						</a>
						
												
	                </li>

	            </ul>
				
	        </nav>
<div id="menu-container">
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
</div>
</div>
</div>

</body>
