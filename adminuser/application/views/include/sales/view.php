        <?php //print_r(array_reverse($order_history));?>


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
                            Order Number : <?php echo $_POST['incrementId']; ?>
                        </h3>
					</div>
					<div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
								<i class="fa fa-clock-o fa-fw"></i> 
									Order # <?php echo $_POST['incrementId']; ?> (the order confirmation email was sent)
								</h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo date("F d, Y",strtotime($created_at)); ?>  </span>
                                        <i class="fa fa-fw fa-qrcode"></i> Order Date  
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $status; ?></span>
                                        <i class="fa fa-fw fa-qrcode"></i> Order Status
                                    </a>
                                 <!--    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $store_name; ?></span>
                                        <i class="fa fa-fw fa-comment"></i> Parchase From
                                    </a> -->
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo $remote_ip; ?></span>
                                        <i class="fa fa-fw fa-qrcode"></i> Place form IP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>	
					<div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
								<i class="fa fa-clock-o fa-fw"></i> 
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
                                        <i class="fa fa-fw fa-envelope"></i> Email
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <span class="badge"><?php echo date("F d, Y",strtotime($customer_dob));?></span>
                                        <i class="fa fa-fw fa-birthday-cake "></i> Date of Birth
                                    </a>								
                                </div>
                            </div>
                        </div>
                    </div>						
					<div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
								<i class="fa fa-home fa-fw"></i> 
									Billing Address
								</h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                      
										<ul>                                        
                                            <li><?php echo $billing_firstname; ?></li>
    										<li><?php echo $billing_street; ?></li>
    										<li><?php echo $billing_city; ?></li>
    										<li>Philippines</li>
                                            <li>T: <?php echo isset($billing_telephone)? $billing_telephone :''; ?></li>
    										<li>M: <?php echo $billing_mobile; ?></li>

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
								<i class="fa fa-plane"></i> 
									Shipping Address
								</h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                    
										<ul>                                   
                                            <li><?php echo $shipping_firstname; ?></li>
                                            <li><?php echo $shipping_street; ?></li>
                                            <li><?php echo $shipping_city; ?></li>
                                            <li>Philippines</li>
                                            <li>T: <?php echo isset($shipping_telephone)? $shipping_telephone :''; ?></li>
                                            <li>M: <?php echo $shipping_mobile; ?></li>
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
								<i class="fa fa-clock-o fa-fw"></i> 
									Payment Information
								</h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                      
                                        <ul>    
                                            <?php if($method == 'bpisecurepay'): ?>

                                                <li>BPI SecurePay</li>
                                            
                                            <?php endif; ?>                                               
											     
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
								<i class="fa fa-plane"></i> 
									Shipping & Handling Information
								</h3>
                            </div>
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                       

                                        <ul>
                                            <li><?php echo $shipping_description;?> <?php echo $base_shipping_amount; ?></li>
									        <?php echo isset($unilab_waybill_number)? "<li>Waybill Number - $unilab_waybill_number</li>": ''; ?>
                                        </ul>    
                                    </a>								
                                </div>
                            </div>
                        </div>
                    </div>		
					<div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
								<i class="fa fa-clock-o fa-fw"></i> 
									Items Ordered
								</h3>
                            </div>
                            <table id="productlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Item Status</th>
                                        <th>Original Price</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                        <th>Tax Amount</th>
                                        <th>Tax Percent</th>                                            
                                        <th>Discount Amount</th>
                                        <th>Row Total</th>
                                        
                                    </tr>
                                </thead>                                                             
                                <tbody>

                                   
                                
                                        <?php

                                            foreach($productItems as $_item){
                                                $total = 0;
                                                $total = number_format($_item['row_total'] - $_item['discount_amount'], 2);                                                       

                                                echo "<tr>";

                                                    echo "<td>".$_item['name']."<br/> SKU ".$_item['sku']."</td>";
                                                    echo "<td></td>";
                                                    echo "<td>".$_item['price']."</td>";

                                                    echo "<td>".$_item['original_price']."</td>";
                                                    echo "<td>".$_item['total_qty_ordered']."</td>";
                                                    echo "<td>".$_item['subtotal']."</td>";
                                                    echo "<td>".$_item['tax_amount']."</td>";
                                                    echo "<td>".$_item['tax_percent']."</td>";
                                                    echo "<td>".$_item['discount_amount']."</td>";
                                                    echo "<td>".$total."</td>";
                                                echo "</tr>";
                                            }


                                        ?>
                              
                                </tbody>
                            </table>    
                                            
                          
                        </div>
                    </div>	
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                <i class="fa fa-clock-o fa-fw"></i> 
                                    Comments History 

                               
                                </h3>
                            </div>
                            <table id="productlist" class="table table-striped table-bordered" cellspacing="0" width="100%">                                                                                               
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="col-sm-6 col-md-6">Add Order Comments</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-6 col-md-4">Status</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>                                       
                                            <div class="col-sm-6 col-md-4">
                                                <select class="form-control">
                                                    <option>Processing</option>                                                        
                                                </select> 
                                            </div>
                                        </td>
                                    <tr>
                                    </tr>    
                                        <td>    
                                            <div class="col-sm-6 col-md-12">
                                                <textarea class="form-control" rows="3"></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-sm-6 col-md-6 checkbox">
                                              <label>
                                                <input type="checkbox" value="">Visible on Frontend
                                              </label>
                                            </div>
                                      
                                            <div class="col-sm-6 col-md-6">
                                              <label>
                                                <button type="button" class="btn btn-warning">Submit Comment</button>
                                              </label>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php foreach (array_reverse($order_history) as $key => $history): ?>
                                    <tr>
                                        <td>
                                            <div class="col-sm-6 col-md-12 checkbox">
                                                <label>

                                                <?php 
                                                    echo date("F d, Y A | ",strtotime($history->created_at)); 

                                                    echo $history->status; ?>

                                                <br/> 
                                                    <?php 

                                                        if($history->is_customer_notified == 0):

                                                            echo 'Customer Not Notified';

                                                        elseif($history->is_customer_notified == 1):

                                                            echo 'Customer Not Notified';

                                                        elseif($history->is_customer_notified == 2):
                                                        
                                                            echo 'Customer Notification Not Applicable ';
                                                        
                                                        endif;    

                                                    ?> 
                                                <br/> <?php echo $history->comment; ?>
                                              </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>            
                        </div> 
                    </div>       
					<div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
								<i class="fa fa-clock-o fa-fw"></i> 
									Order Totals
								</h3>
                            </div>
                         
                            <table id="productlist" class="table table-striped table-bordered" cellspacing="0" width="100%">                                                                                               
                                <tbody>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td align="right"><?php echo $subtotal;?></td>
                                    </tr>
                                    <tr>
                                        <td>Shipping & Handling</td>
                                        <td align="right"><?php echo $shipping_amount;?></td>
                                    </tr>
                                    <tr>
                                        <td>Grand Total</td>
                                        <td align="right"><?php echo $grand_total;?></td>
                                    </tr>
                                    <tr>
                                        <td>Total Paid</td>
                                        <td align="right"><?php echo $total_paid;?></td>
                                    </tr>
                                    <tr>
                                        <td>Total Refunded</td>
                                        <td align="right"><?php echo $total_refunded; ?></td>
                                    </tr>           
                                    <tr>
                                        <td>Total Due</td>
                                        <td align="right"><?php echo $total_due;?></td>
                                    </tr>                                                       
                              
                                </tbody>
                            </table>                   
                          
                   
                        </div>
                    </div>					
                </div>

            </div>

        </div>

    </div>

</body>