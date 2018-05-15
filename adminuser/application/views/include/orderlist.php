       <?php // print_r($getProductslist); ?>
		
		<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-qrcode"></i> Manage Orders
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
                            <li><strong><?php echo $this->uri->segment(2) . " [ ".count($getAllorders) ." ]"; ?></strong></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
					<div class="col-lg-12">
						<p><button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>
						<!--<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button></p>-->
						<a href="<?php echo base_url()?>index.php/sales/exportOr" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a></p>
					</div>				
                    <div class="col-lg-12">
						<table id="productlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>Order Number</th>
										<th>Customer Name</th>
										<th>Grant Total</th>
										<th>Total Paid</th>
										<th>Date</th>
										<th>Status</th>
										<th><center>Action</center></th>
									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>Order Number</th>
										<th>Customer Name</th>
										<th>Grant Total</th>
										<th>Total Paid</th>
										<th>Date</th>
										<th>Status</th>
										<th><center>Action</center></th>
									</tr>
								</tfoot>
						 
								<tbody>
									<?php
										
										foreach($getAllorders as $_list):
										
											$orderIncrement = $_list->increment_id;
											
											echo '<tr>';
											echo '<td>'.$_list->increment_id.'</td>';
											echo '<td>'.$_list->shipping_name.'</td>';
											echo '<td>'.$_list->grand_total.'</td>';
											echo '<td>'.$_list->total_paid.'</td>';
											echo '<td>'.$_list->created_at.'</td>';
											echo '<td>'.$_list->status.'</td>';
											echo '<td><center><button type="button" class="_view btn btn-primary" title="View" data-id="'.$orderIncrement.'"><i class="fa fa-eye"></i></button>';
											echo ' <button type="button" class="_view btn btn-danger" title="Print" data-id="'.$orderIncrement.'"><i class="fa fa-print"></i></button></center></td>';
											echo '</tr>';
									
										endforeach;
									?>
								</tbody>
							</table>                   
					</div>   
				   
                </div>

            </div>

        </div>

    </div>
	
	<form name="orderform" action="?" method="post">
		<input type="hidden" value="" name="incrementId" />
	</form>
	
</body>

<script>

	$(document).ready(function() {
		
		$('#productlist').DataTable();
		
		$("body").on('click', '._view', function (){
			
			var incrementId = $(this).attr('data-id');
			$("[name=incrementId]").val(incrementId);
			$("[name=orderform]").attr("action","<?php echo base_url()?>index.php/sales/order_view");
			$("[name=orderform]").submit();
			
		});
		
		
	});
	
	function createpdf()
	{
		window.location.href = "<?php echo base_url();?>index.php/pdf/orders";
	}
	

</script>