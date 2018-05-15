       <?php // print_r($getProductslist); ?>
	<?php if(ccheck_form(isuser_id(),"admin/sales/shipment")== true ): ?>		
		<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-plane"></i> Manage Shipping
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
								<a href="<?php echo base_url();?>">
									<i class="fa fa-dashboard"></i> Dashboard
								</a>
                            </li>
                            <li>
								<a href="<?php echo base_url();?>index.php/sales/order">
									<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1) ?>
								</a>
                            </li>
                            <li><strong><?php echo $this->uri->segment(2) . " [ ".count($getAllshipping) ." ]"; ?></strong></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
				
					<div class="col-lg-12">
						<p><button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>
						<!--<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button></p>-->
						<a href="<?php echo base_url()?>index.php/sales/exportShip" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a></p>
					</div>				
                    <div class="col-lg-12">
						<table id="shipppinglist" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>ID</th>
										<th>Customer Name</th>
										<th>Order Number</th>
										<th>Shipping Number</th>
										<th>Date</th>
										<th><center>Action</center></th>
									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>ID</th>
										<th>Customer Name</th>
										<th>Order Number</th>
										<th>Shipping Number</th>
										<th>Date</th>
										<th>Action</th>
									</tr>
								</tfoot>
						 
								<tbody>
									<?php

											foreach($getAllshipping as $_list):
												
												$orderIncrement = $_list->order_increment_id; 
											
											echo '<tr>';
											echo '<td>'.$_list->entity_id.'</td>';
											echo '<td>'.$_list->shipping_name.'</td>';
											echo '<td>'.$_list->order_increment_id.'</td>';
											echo '<td>'.$_list->increment_id.'</td>';
											echo '<td>'.$_list->created_at.'</td>';
											echo '<td><center><button type="button" class="_view btn btn-primary" title="View" data-id="'.$orderIncrement.'"><i class="fa fa-eye"></i> </button>';
											echo ' <button type="button" class="btn btn-danger" title="Print" data-id="'.$orderIncrement.'"><i class="fa fa-print"></i></button></center></td>';
											echo '</tr>';
									
										endforeach;
									?>
								</tbody>
							</table>                   
					</div>   
				   
                </div>

            </div>

        </div>
<?php else: echo "This module is not allowed for viewing!"?>
<!--<a id="" href="#addcustomermodal" type="button" class="btn btn-xs btn-success" data-toggle="modal" ><i class="fa fa-file-excel-o"></i> ADD NEW CUSTOMER</a>-->

<?php endif; ?>	
    </div>
    <form name="shipmentform" action="?" method="post">
		<input type="hidden" value="" name="incrementId" />
	</form>

</body>

	
<script>
	$(document).ready(function() {
		$('#shipppinglist').DataTable();
		$("body").on('click', '._view', function (){
		
			var incrementId = $(this).attr('data-id');
			$("[name=incrementId]").val(incrementId);
			$("[name=shipmentform]").attr("action","<?php echo base_url()?>index.php/sales/shipment_view");
			$("[name=shipmentform]").submit();
			
		});
	});
</script>