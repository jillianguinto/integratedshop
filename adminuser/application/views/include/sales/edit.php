
		<div id="page-wrapper">
            <div class="container-fluid">
			
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-qrcode"></i> Manage Edit Order
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
                            <li><strong><?php echo $this->uri->segment(2); ?></strong></li>
                        </ol>
                    </div>
                </div>
				
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header">
                            Order Number : <?php echo $_POST['incrementId'];; ?>
                        </h3>
					</div>
                </div>

            </div>

        </div>

    </div>

</body>