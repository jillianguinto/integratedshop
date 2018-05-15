	<?php if(ccheck_form(isuser_id(),"admin/sales/invoice")== true ): ?>			
		<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-money"></i> Manage Invoice
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
								<a href="<?php echo base_url();?>">
									<i class="fa fa-dashboard"></i> Dashboard
								</a>
                            </li>
                            <li>
								<a href="<?php echo base_url();?>index.php/sales/order">
									<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1); ?>
								</a>
                            </li>
                            <li><strong><?php echo $this->uri->segment(2) . " [ ".count($getAllinvoice) ." ]"; ?></strong></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
					<div class="col-lg-12">
						<p><button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>
						<!--<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button></p>-->
						<a href="<?php echo base_url()?>index.php/sales/exportInv" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a></p>
					</div>				
                    <div class="col-lg-12">
						<table id="invoicelist" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>Invoice Number</th>
										<th>Customer Name</th>
										<th>Order Number</th>
										<th>Grant Total</th>
										<th>Date</th>
										<th><center>Action</center></th>

									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>Invoice Number</th>
										<th>Customer Name</th>
										<th>Order Number</th>
										<th>Grant Total</th>
										<th>Date</th>
										<th><center>Action</center></th>
									</tr>
								</tfoot>
						 
								<tbody>
								
									<?php 
									
										if(empty($getAllinvoice)):
										
											echo '<tr>';
											echo '<td colspan="6"><center>NO RECORD FOUND!</center></td>';
											echo '</tr>';	
											
										else:		
											?>
												<script>
													$(document).ready(function() {
														$('#customerlist').DataTable();
													});
												</script>
											<?php

											// print_r($getAllinvoice);

											foreach($getAllinvoice as $_list):
													
												// $orderNumber = $_list->increment_id;
												$invoiceIncrement = $_list->order_id;
												
												echo '<tr>';
												echo '<td>'.$_list->increment_id.'</td>';
												echo '<td>'.$_list->billing_name.'</td>';
												echo '<td>'.$_list->order_increment_id.'</td>';
												echo '<td>'.$_list->grand_total.'</td>';
												echo '<td>'.$_list->created_at.'</td>';
												echo '<td><center><button type="button" class="_view btn btn-primary" title="View" data-id="'.$invoiceIncrement.'"><i class="fa fa-eye"></i> </button>';
												echo ' <button type="button" class="btn btn-danger" title="Print"  data-id="'.$invoiceIncrement.'"><i class="fa fa-print"></i></button></center></td>';
												echo '</tr>';
										
											endforeach;
										endif;
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


	<form name="invoiceform" action="?" method="post">
		<input type="hidden" value="" name="incrementId" />
	</form>

</body>

<script>

	$(document).ready(function() {
		
		$('#invoicelist').DataTable();
		
		$("body").on('click', '._view', function (){
			
			var incrementId = $(this).attr('data-id');
			$("[name=incrementId]").val(incrementId);
			$("[name=invoiceform]").attr("action","<?php echo base_url()?>index.php/sales/invoice_view");
			$("[name=invoiceform]").submit();
			
		});
		
		
	});
	
	function createpdf()
	{
		window.location.href = "<?php echo base_url();?>index.php/pdf/invoices";
	}
	

</script>