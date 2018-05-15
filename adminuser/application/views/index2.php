<div id="page-wrapper">
    this is test
    <div class="container-fluid">
        <?php 
        // if (isstore_id()!=0):
        if(is_logged() == true):
            if (isstore_id()==""): 
            //echo "waala";
                endif;
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Admin Panel 					
                        <?php 
                        //print_r($_COOKIE);
                        if(is_logged() == true):				
                            echo   " - ". web_sitename();
                        endif;
                        ?>
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-fw fa-users fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo $getallCustomers; 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 
                                    </div>
                                    <div>
                                        Customers&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo base_url()?>index.php/customers">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-leaf fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo count($getAllProducts); 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 												
                                    </div>
                                    <div>
                                        Products&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo base_url()?>index.php/products">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-life-ring fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo count($getAllPromotions); 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 										
                                    </div>
                                    <div>
                                        Promotions&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo base_url()?>index.php/promotions">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-qrcode fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo $getAllsales; 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 										

                                    </div>
                                    <div>
                                        Orders&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <a  href="<?php echo base_url()?>index.php/sales/order">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>					
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-plane fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo count($getAllshipping); 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 										

                                    </div>
                                    <div>
                                        Shipping&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a  href="<?php echo base_url()?>index.php/sales/shipment">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-money fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php 
                                        if(is_logged() == true):
                                            echo count($getAllinvoice); 
                                        else:
                                            echo '0';
                                        endif;
                                        ?> 										

                                    </div>
                                    <div>
                                        Invoice&nbsp;
                                        <a href="<?php echo base_url()?>index.php/pdf/orders" title="Generate PDF" style="border-radius:.5px; font-weight:bold; color:white;"><i class="fa fa-file-pdf-o"></i></a>
                                        <a href="javascript::void(0)" style="border-radius:.5px; font-weight:bold; color:white;" title="Export to EXCEL"><i class="fa fa-file-excel-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a  href="<?php echo base_url()?>index.php/sales/invoice">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>


            </div>


        <?php else: ?>		

        <div class="row vertical-offset-100">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading" id="admin_config">                                
                        <div class="row-fluid user-row" style="color:#337AB7;font-size: 24px;">
                            <center><i class="fa fa-gears fa-5x"></i></center>
                        </div>
                    </div>
                    <div class="panel-heading" id="please_wait" style="display:none;color:#337AB7;font-size: 24px;">                                
                        <h2><i class="fa fa-gear faa-spin animated"></i>&nbsp;Please wait..</h2>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-danger" id="errorHandler" style="display:none"></div>									
                        <form accept-charset="UTF-8" role="form" class="form-signin">
                            <fieldset>
                                <label class="panel-login">
                                    <div class="login_result"></div>
                                </label>
                                <input class="form-control" placeholder="Username" id="username" type="text">
                                <input class="form-control" placeholder="Password" id="password" type="password" >
                                <!--<button type="button" class="btn btn-lg btn-success btn-block" id="btn-login" onclick="connectdb()">Login »</button>-->
                                <button type="button" class="btn btn-lg btn-success btn-block" id="btn-login" onclick="logStore2()">Login »</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
</div>
<div id="menu-container">
    <div class="store-label col-md-12">
        <h3>Select Store</h3>
    </div>
    <div class="store-menu">
        <div class="col-md-12">
            <div class="row img-rows">
                <div class="col-md-4">

                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(1)" >
                    Clickhealth
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(2)" >
                    Evolife
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(3)" >
                    Celeteque
                </div>
            </div>
            <div class="row img-rows">
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(4)">
                    Clickhealthplus
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(5)">
                    celeteq
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(12)">
                    Unilab Active Health
                </div>
            </div>
            <div class="row img-rows">
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(13)">
                    Family Health at Home
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(15)">
                    GutHealth
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(16)">
                    Liviacor
                </div>
            </div>
            <div class="row img-rows">
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(17)">
                    Athena
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(18)">
                    Conzace
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url()?>template/images/celeteq.png" class="img-responsive" alt="Image" onclick="connectdb2(19)">
                    Ritemed
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!-- Modal -->
<div id="StoreModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" onclick=''>&times;</button>
                <h4 class="modal-title">Select Store</h4>
            </div>
            <div class="modal-body">
                <!--<button type="button" class="btn btn-lg btn-success btn-block" id="btn-login" onclick="connectdb2(0)">press me for global! »</button>-->
                <ul class="list-group">
                    <div id="test">
                        <!--load ajax here-->
                    </div>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick=''>Close</button>
            </div>
        </div>
    </div>
</div>
</body>
