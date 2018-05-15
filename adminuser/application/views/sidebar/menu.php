<body onload="init()">

<?php //echo "<pre>"; print_r($_COOKIE); echo "</pre>";?>
	<!-- Modal -->
	<div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	   <div class="modal-dialog modal-md">
	      	<div class="modal-content">
	       		<div class="modal-header">
	         		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	         		<h4 class="modal-title" id="myModalLabel"></h4>
	       		</div>
	       		<div class="modal-body" id="getCode" style="overflow-x-: scroll-;"></div>
	       		
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>	               
	            </div>c
	    	</div>
	   </div>
	</div>

    <div id="wrapper">
	
	        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
	                    <span class="sr-only">Admin Panel</span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                </button>
	                <a id="sstore" class="navbar-brand" href="<?php echo base_url(); ?>"  data-toggle="modal">Admin Panel 
						<?php 
						//print_r($_COOKIE);
						//print_r($_COOKIE);
						//print_r(is_usersession());
						//echo "hahah";

						
						//committing error
							//if(is_logged() == true):
							//	echo   " - ". web_sitename(). "<b class='caret'></b>";
							//endif;
						?>
					</a>
	            </div>
				
	            <ul class="nav navbar-right top-nav">

	                <li class="dropdown">
	                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-user"></i> 				
							<?php 

								if(is_logged() == true):
								
									echo "Hi! " . is_usersession();
									echo "<b class='caret'></b>";
								
								else:
									echo "Please login!";
								endif;
							?>
						</a>
						
						<?php if(is_logged() == true):	?>
						
							<ul class="dropdown-menu">
								<li><a href='javascript:void(0)'  onclick='logoutform()'><i class='fa fa-power-off'></i> Logout</a> </li>
							</ul>
						
						<?php endif; ?>
						
	                </li>

	            </ul>
				<?php if(is_logged() == true): ?>
					<div class="collapse navbar-collapse navbar-ex1-collapse">
					
						<ul class="nav navbar-nav side-nav">
						<!-- TO Hide SIDEBAR MENU
							<li <?php if($this->uri->segment(1) == ''): ?>class="active" <?php endif;?> >

							<?php if(ccheck_form(isuser_id(),"admin/dashboard")== true ): ?>
								<a href="<?php echo base_url()?>" class=""><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
							<?php else: ?>
								<a href="<?php echo base_url()?>" class="disabled"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
							<?php endif; ?>

							</li>
										
							<li <?php if($this->uri->segment(1) == 'customers'): ?>class="active" <?php endif;?> >

							<?php if(ccheck_form(isuser_id(),"admin/customer")== true ): ?>
								<a href="<?php echo base_url()?>index.php/customers"><i class="fa fa-fw fa-users"></i> Customers</a>
							<?php else: ?>
								<a href="<?php echo base_url()?>index.php/customers" class="disabled"><i class="fa fa-fw fa-users"></i> Customers</a>
							<?php endif; ?>
							</li>
						
							<li>
							<?php if(ccheck_form(isuser_id(),"admin/sales")== true ): ?>
								<a href="javascript:;" data-toggle="collapse" data-target="#sales" <?php if($this->uri->segment(1) == 'sales'): ?> class="parent-active" aria-expanded="true" <?php endif;?>><i class="fa fa-shopping-cart"></i> Sales <i class="fa fa-fw fa-caret-down"></i></a>
								<ul id="sales" <?php if($this->uri->segment(1) == 'sales'): ?> class="collapse in" aria-expanded="true" <?php else: ?> aria-expanded="false" class="collapse" style="height: 0px;" <?php endif;?> >
									<li <?php if($this->uri->segment(2) == 'order'): ?> class="sub-active" <?php endif;?> >
									<?php if(ccheck_form(isuser_id(),"admin/sales/order")== true ): ?>
									   <a href="<?php echo base_url()?>index.php/sales/order"> <i class="fa fa-qrcode"></i>  Order</a>
									 <?php else: ?>
									  <a href="<?php echo base_url()?>index.php/sales/order" class="disabled"> <i class="fa fa-qrcode"></i>  Order</a>
									 <?php endif; ?>
									</li>
									<li <?php if($this->uri->segment(2) == 'shipment'): ?> class="sub-active" <?php endif;?>>
									<?php if(ccheck_form(isuser_id(),"admin/sales/shipment")== true ): ?>			
										 <a href="<?php echo base_url()?>index.php/sales/shipment"><i class="fa fa-plane"></i> Shipment</a>
									<?php else: ?>
									 	 <a href="<?php echo base_url()?>index.php/sales/shipment" class="disabled"><i class="fa fa-plane"></i> Shipment</a>
									 <?php endif; ?>	
									</li>
									<li <?php if($this->uri->segment(2) == 'invoice'): ?> class="sub-active" <?php endif;?>>
									<?php if(ccheck_form(isuser_id(),"admin/sales/invoice")== true ): ?>	
									    <a href="<?php echo base_url()?>index.php/sales/invoice"><i class="fa fa-money"></i>  Invoice</a>
									 <?php else: ?>
									    <a href="<?php echo base_url()?>index.php/sales/invoice" class="disabled"><i class="fa fa-money"></i>  Invoice</a>
									 <?php endif; ?>   
									</li>
								</ul>
							<?php else: ?>
							<?php endif; ?>
							</li>					
							<li><a href="javascript:;" data-toggle="collapse" data-target="#products" <?php if($this->uri->segment(1) == 'products' OR $this->uri->segment(1) == 'categories'): ?> class="parent-active" aria-expanded="true" <?php endif;?>><i class="fa fa-leaf"></i> Products <i class="fa fa-fw fa-caret-down"></i></a>
								<ul id="products"   <?php if($this->uri->segment(1) == 'products'): ?> class="collapse in" aria-expanded="true" <?php else: ?> aria-expanded="false" class="collapse" style="height: 0px;" <?php endif;?> >
								<li <?php if($this->uri->segment(1) == 'products'): ?> class="sub-active" <?php endif;?>>
								<?php if(ccheck_form(isuser_id(),"admin/catalog/products")== true ): ?>
									<a href="<?php echo base_url()?>index.php/products"> <i class="fa fa-qrcode"></i> Manage Products</a>
								<?php else: ?>
							    	<a href="<?php echo base_url()?>index.php/products" class="disabled"> <i class="fa fa-qrcode"></i> Manage Products</a>
								<?php endif; ?>
								</li>
								<li  <?php if($this->uri->segment(1) == 'categories'): ?> class="sub-active" <?php endif;?>>
								<?php if(ccheck_form(isuser_id(),"admin/catalog/categories")== true ): ?>
								<a href="<?php echo base_url()?>index.php/categories"> <i class="fa fa-qrcode"></i> Manage Categories</a>
								<?php else: ?>
								<a href="<?php echo base_url()?>index.php/categories" class="disabled"> <i class="fa fa-qrcode"></i> Manage Categories</a>
								<?php endif; ?>
								</li>
								</ul>
							</li>

							<li><a href="javascript:;" data-toggle="collapse" data-target="#promotions" <?php if($this->uri->segment(1) == 'promotions'): ?> class="parent-active" aria-expanded="true" <?php endif;?>><i class="fa fa-shopping-cart"></i> Promotions <i class="fa fa-fw fa-caret-down"></i></a>
								<ul id="promotions" <?php if($this->uri->segment(1) == 'promotions'): ?> class="collapse in" aria-expanded="true" <?php else: ?> aria-expanded="false" class="collapse" style="height: 0px;" <?php endif;?> >
									<li <?php if($this->uri->segment(1) == 'promotions'): ?> class="sub-active" <?php endif;?>>
									<?php if(ccheck_form(isuser_id(),"admin/promo")== true ): ?>
										<a href="<?php echo base_url()?>index.php/promotions"><i class="fa fa-life-ring"></i> Catalog Price Rules</a>
									<?php else: ?>
										<a href="<?php echo base_url()?>index.php/promotions" class="disabled"><i class="fa fa-life-ring"></i> Catalog Price Rules</a>
									<?php endif; ?>
									</li>
								</ul>
							</li>

						!-->
							<li><a href="javascript:;" data-toggle="collapse" data-target="#reports" <?php if($this->uri->segment(1) == 'sumReport'): ?> class="parent-active" aria-expanded="true" <?php endif;?>><i class="fa fa-shopping-cart"></i> Month-end-reports <i class="fa fa-fw fa-caret-down"></i></a>
								<ul id="reports" <?php if($this->uri->segment(1) == 'sumReport'): ?> class="collapse in" aria-expanded="true" <?php else: ?> aria-expanded="false" class="collapse" style="height: 0px;display:block !important" <?php endif;?> >
									<li <?php if($this->uri->segment(2) == 'Viewdetails'): ?> class="sub-active" <?php endif;?>>									
										<a href="<?php echo base_url()?>index.php/sumReport/Viewdetails" >	<i class="fa fa-shopping-cart"></i> Order Details</a>
									</li>
									<li <?php if($this->uri->segment(2) == 'Viewsumm'): ?> class="sub-active" <?php endif;?>>
										<a href="<?php echo base_url()?>index.php/sumReport/Viewsumm" >	<i class="fa fa-shopping-cart"></i> Order Summary</a>
									</li>
								</ul>
							</li>
						</ul>

					</div>
				<?php endif; ?>

	        </nav>
	
		<div class="modal fade" id="LogoutModalProgress" tabindex="-1" role="dialog" aria-labelledby="LogoutModalProgress">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" ><i class='fa fa-power-off'></i> Logout</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label for="recipient-name" class="control-label" style="font-size:16px;">
					<i class="fa fa-spinner faa-spin animated"></i> Please wait</label>
					<input type="hidden" class="form-control" id="accountID">
				  </div>	  
			  </div>
			</div>
		  </div>
		</div>	
		

<link href="<?php echo base_url()?>template/css/custom-style.css" rel="stylesheet">

<script>

function loginform()
{
	$('#loginModal').modal('show');
}

$(document).keypress(function(e) {
	if(e.which == 13) {
		logStore();
	}
	//return false;
});


function logStore()
{
	if($("#username").val() == '' || $("#password").val() == ''){
		
		alert('Username or Password required!');
		return false;
	}
	$("#admin_config").hide();
	$(".panel-body").hide();
	$("#please_wait").show();
	
	$.post('../index.php/default/connectmage/login',{
		username: $("#username").val(),
		password: $("#password").val()
		
	}, function (xhr){
		
		var objRes = $.parseJSON(xhr)  
		if(objRes.success == true){

			$.post('<?php base_url()?>index.php/login/',{
			
				username: $("#username").val(),
			
				
			},
			
			function (data){
						
				var getresponse = $.parseJSON(data);
				if(getresponse.user_id != 0){
					location.href = '<?php base_url()?>index.php/';
				}
			}
			
			
			
			);
			
			

			// $.ajax({
			
			  // url: "<?php base_url()?>index.php/Store",
			  
			  // cache: false
			  
			// })
			// .done(function( html ) {
			
			    // $( "#menu-container" ).append( html );
				
			// });


			
			// $('#StoreModal').on('hidden.bs.modal', function () {
			// 	logoutform();
			// //location.reload();
			// });

			// $('#StoreModal').modal('show');
			
			// $('#StoreModal').modal({
			// 	backdrop: 'static'
			// });

			$("#please_wait").hide();
			$('#page-wrapper').hide();
			$('#wrapper').css('padding-left', '0');

			$('#menu-container').show();

			
			$(document).bind('keydown keyup', function(e) {
			    if(e.which === 116) {
			       console.log('blocked');
			       return false;
			    }
			    if(e.which === 82 && e.ctrlKey) {
			       console.log('blocked');
			       return false;
			    }
			});


		//ajax and modal hidden
		}else{
			$("#admin_config").show();
			$(".panel-body").show();
			$("#please_wait").hide();
			$('#errorHandler').html('Invalid Username or Password!');						
			$('#errorHandler').show();
		}

	});
}


// $(document).keypress(function(e) {
// 	if(e.which == 13) {
// 		//connectdb();
// 		//alert('heheh');
// 	}
// });

function connectdb()
{
	
	if($("#username").val() == '' || $("#password").val() == ''){
		
		alert('Username or Password required!');
		return false;
	}
	
	$("#admin_config").hide();
	$(".panel-body").hide();
	$("#please_wait").show();
	
	$.post('../index.php/default/connectmage/login',{
		username: $("#username").val(),
		password: $("#password").val()
		
	}, function (xhr){
		
		var objRes = $.parseJSON(xhr)
		
		if(objRes.success == true){
			$.post('<?php base_url()?>index.php/login/',{
				username: $("#username").val(),
				
			}, function (data){

				if(data.username == false){
					$("#admin_config").show();
					$(".panel-body").show();
					$("#please_wait").hide();	
					$('#errorHandler').html('Invalid Account!');						
					$('#errorHandler').show();					
					
				}else{
					location.reload();
				}

			});				
			
			
		}else{
			$("#admin_config").show();
			$(".panel-body").show();
			$("#please_wait").hide();
			$('#errorHandler').html('Invalid Username or Password!');						
			$('#errorHandler').show();
		}

	});
	
}	
	
function logoutform()
{
	// $('#LogoutModalProgress').modal('show');
	
	$.post('<?php echo base_url()?>index.php/logout',{
		id: ''
		
	}, function (data){
		window.location.href= "<?php echo base_url()?>";
		//$('#LogoutModalProgress').modal('hide');
	});
	
}


function connectdb2(store) 
{
	$("#admin_config").hide();
	$(".panel-body").hide();
	$("#please_wait").show();
	if($("#username").val() == '' || $("#password").val() == ''){
		
		alert('Username or Password required!');
		return false;
	}
	
	//alert("wtf");
	$("#StoreModal").hide();
	$(".panel-body").hide();
	$("#please_wait").show();
	$.post('../index.php/default/connectmage/login',{
		username: $("#username").val(),
		password: $("#password").val(), //stid: store,
		
	}, function (xhr){
		
		var objRes = $.parseJSON(xhr)
		
		if(objRes.success == true){
			//alert(stid);

			$("#admin_config").hide();
			$(".panel-body").hide();
			$("#please_wait").show();

					$.post('<?php base_url()?>index.php/login/',{
						username: $("#username").val(), stid: store,
						//alert(stid);
					}, function (data){

						if(data.username == false){
							$("#admin_config").show();
							$(".panel-body").show();
							$("#please_wait").hide();	
							$('#errorHandler').html('Invalid Account!');						
							$('#errorHandler').show();
							alert("your account is not permitted!");
							location.reload();
						}else{
							location.reload();
						}
					});				
				}else{
					$("#admin_config").show();
					$(".panel-body").show();
					$("#please_wait").hide();
					$('#errorHandler').html('Invalid Username or Password!');						
					$('#errorHandler').show();
				}

			});
		
		}



		$('.disabled').click(function(e){
		     e.preventDefault();
		  });

		$('#sstore').click(function(e){
			/*
		    alert("hahah");
		    $('#StoreModal').modal('show');
		$( "#test" ).empty();
		$.ajax({
		  url: "<?php base_url()?>index.php/Store",
		  cache: false
		})
		  .done(function( html ) {
		    $( "#test" ).append( html );
		  });

		$('#StoreModal').on('hidden.bs.modal', function () {
		//location.reload();
		});
		*/
  });
  
function init(){
if($('#username').val()==""){

}else{
//alert('heheh');
/*
$.post('<?php base_url()?>index.php/login/',{
				username: $("#username").val(),
			});

			$.ajax({
			  url: "<?php base_url()?>index.php/Store",
			  cache: false
			})
			.done(function( html ) {
			    $( "#menu-container" ).append( html );
			});

$("#please_wait").hide();
			$('#page-wrapper').hide();
			$('#wrapper').css('padding-left', '0');

			$('#menu-container').show();

			
			$(document).bind('keydown keyup', function(e) {
			    if(e.which === 116) {
			       console.log('blocked');
			       return false;
			    }
			    if(e.which === 82 && e.ctrlKey) {
			       console.log('blocked');
			       return false;
			    }
			});
			*/
}
}
</script>
