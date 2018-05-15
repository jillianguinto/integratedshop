

<?php 

// echo '<pre>';

// print_r($getShippingDetails);

// die();
foreach($getShippingDetails as $shipping):

    // from order item tb
	$state = $shipping->state;
	$status = $shipping->status;
	$shipping_description = $shipping->shipping_description;
	$store_name = $shipping->store_name;
	$created_at = $shipping->created_at;
	$remote_ip = $shipping->remote_ip;

	$customer_email = $shipping->customer_email;
	$customer_firstname = $shipping->customer_firstname;
	$customer_lastname = $shipping->customer_lastname;
	$customer_middlename = $shipping->customer_middlename;
	$bday = $shipping->customer_dob;

	//address
	$firstname = $shipping->firstname;
	$middlename = $shipping->middlename;
	$lastname = $shipping->lastname;
	$email = $shipping->email;
	$mobile = $shipping->mobile;
	$telephone = $shipping->telephone;
	$street = $shipping->street;
	$postcode = $shipping->postcode;
	$region = $shipping->region;
	$city = $shipping->city;
	$method = $shipping->method;
	$shipping_description = $shipping->shipping_description;
	$base_shipping_amount = $shipping->base_shipping_amount;
	$unilab_waybill_number = $shipping->unilab_waybill_number;

endforeach;
?>
<?php 
foreach ($getShipmentItems as $items1) {
 
	$shipping_name = $items1->shipping_name;
    $shipping_incrementid = $items1->increment_id;

}
 ?>

        <style type="text/css">
        ul li { list-style: none;}
        </style>
        <div id="page-wrapper">
            <div class="container-fluid">
            
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-qrcode"></i> Manage Orders View
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
                            Shipment Number : <?php echo $shipping_incrementid; ?>
                        </h3>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-shopping-cart"></i>
                                    Order # <?php echo $_POST['incrementId']; ?> (the order confirmation email was sent)
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $created_at; ?></span>
                                        <i class="fa fa-fw fa-calendar"></i> Order Date  
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $status; ?></span>
                                        <i class="fa fa-fw fa-comment-o"></i> Order Status
                                    </a>
                                 <!--    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $purchase_from; ?></span>
                                        <i class="fa fa-fw fa-comment"></i> Parchase From
                                    </a> -->
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $remote_ip; ?></span>
                                        <i class="fa fa-fw fa-warning"></i> Place form IP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-users fa-fw"></i> 
                                    Account Information
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $customer_firstname; ?> <?php echo $customer_lastname ; ?></span>
                                        <i class="fa fa-fw fa-user"></i> Customer Name
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $email; ?></span>
                                        <i class="fa fa-envelope"></i> Email
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $bday; ?></span>
                                        <i class="fa fa-birthday-cake"></i> Date of Birth
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>                      
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-truck fa-fw"></i> 
                                    Billing Address
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-fw fa-home"></i>
                                        <ul>                                        
                                            <li><?php echo $firstname; ?></li>
                                            <li><?php echo $street; ?></li>
                                            <li><?php echo $city; ?></li>
                                            <li>Philippines</li>
                                            <li>T: <?php echo $telephone; ?></li>
                                            <li>M: <?php echo $mobile; ?></li>

                                        </ul>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-truck fa-fw"></i> 
                                    Shipping Address
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-fw fa-home"></i>
                                        <ul>                                   
                                            <li><?php echo $shipping_name; ?></li>
                                            <li><?php echo $street; ?></li>
                                            <li><?php echo $city; ?></li>
                                            <li>Philippines</li>
                                            <li>T: <?php echo $telephone; ?></li>
                                            <li>M: <?php echo $mobile; ?></li>
                                        </ul>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>  

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-money fa-fw"></i> 
                                    Payment Information
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-fw fa-credit-card"></i>
                                        <ul>                                              
                                             <li><?php echo $method; ?></li>
                                             <li>Order was placed using PHP</li>
                                        </ul>    
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-send fa-fw"></i> 
                                    Shipping & Handling Information
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <i class="fa fa-fw fa-road"></i>
                                        <?php echo $shipping_description;?> <?php echo number_format($base_shipping_amount,2); ?><br/>
                                        <?php echo isset($unilab_waybill_number)? 'Waybill Number - ' . $unilab_waybill_number: ''; ?>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>      
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-truck fa-fw"></i> 
                                    Items Shipped
                                </h3>
                            </div>
                            <div class="panel-body">
                             <table class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Product</th>
                                         <th>Qty Shipped</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                 	<?php foreach ($getShipmentItems as $items) { 
                                 	
                                 			$name = $items->name;
                                 			$sku  = $items->sku;
                                 			$qty  = $items->qty;
                                 			$totalqty = $items->total_qty;
                                 	?>
                                 	
                                     <tr>
                                         <td>
                                            Name : <?php echo $name; ?> <br>
                                            SKU  : <?php echo $sku; ?>
                                         </td>
                                         <td>
                                            Qty  : <?php echo number_format($qty); ?>
                                         </td>
                                     </tr>
                                  <?php  }
                                 	 ?>
                                 </tbody>
                             </table>
                            </div>
                        </div>
                    </div>  
                                   
                </div>

            </div>

        </div>

    </div>

</body>