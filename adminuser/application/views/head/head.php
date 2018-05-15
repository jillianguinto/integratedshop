<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>E-commerce user admin</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url()?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url()?>template/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="<?php echo base_url()?>template/css/sb-admin.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="<?php echo base_url()?>template/css/plugins/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?php echo base_url()?>template/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url()?>template/css/styles.css" rel="stylesheet">
    <link href="<?php echo base_url()?>template/css/font-awesome-animation.min.css" rel="stylesheet">

    <link href="<?php echo base_url()?>template/css/modal.css" rel="stylesheet">

    <link href="<?php echo base_url()?>template/css/product-upload.css" rel="stylesheet">

    <link href="<?php echo base_url()?>template/css/datatables.responsive.css" rel="stylesheet">


    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/bs/dt-1.10.10/datatables.min.css"/> -->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
        
    
    <script src="<?php echo base_url()?>template/js/csv/table2CSV.js"></script> 

    

    <script src="<?php echo base_url()?>template/js/jquery-1.11.2.min.js"></script> 

    <script src="<?php echo base_url()?>template/js/datatables.responsive.js"></script>
	
	
	
	
    <script src="<?php echo base_url()?>template/js/dom-bootstrap3.js"></script> 

    <script type="text/javascript" src="<?php echo base_url()?>template/js/jquery.min.js"></script>   
    <link rel="stylesheet" href="<?php echo base_url()?>template/css/jquery-ui.css" />
    <script src="<?php echo base_url()?>template/js/jquery-ui.min.js"></script> 

    <script src="<?php echo base_url()?>template/js/allStyle.js"></script> 
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> 
    <script type="text/javascript" src="https://code.jquery.com/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />-->


    <script src="<?php echo base_url()?>template/js/bootstrap.validator.js"></script>   
    
    <script src="<?php echo base_url()?>template/js/jquery.dataTables.min.js"></script>
    <!-- <script src="<?php echo base_url()?>template/js/customerDataFilterTable/jquery.dataTables.min.js"></script> -->
    
    <script src="<?php echo base_url()?>template/js/customerDataFilterTable/customerDataFilterTable.js"></script>

    <script src="<?php echo base_url()?>template/js/dataTables.bootstrap.min.js"></script>  

    <script src="<?php echo base_url()?>template/js/Moment.js"></script>
    <script src="<?php echo base_url()?>template/js/bootstrap-datetimepicker.js"></script>  

    <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>-->

    <script src="<?php echo base_url()?>template/js/form-validation.js"></script>  
    <script src="<?php echo base_url()?>template/js/jsForm.js"></script>  
    
    <script src="<?php echo base_url()?>template/js/product-upload.js" type="text/javascript" ></script>    
    <!-- <script src="<?php echo base_url()?>template/js/validation.js_" type="text/javascript" ></script>   -->
    
    
    <script type="text/javascript" src="<?php echo base_url()?>template/dist/js/formValidation.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>template/dist/js/framework/bootstrap.js"></script>

    <script type="text/javascript" src="<?php echo base_url()?>template/js/bootstrap-multiselect.js"></script>

    <link rel="stylesheet" href="<?php echo base_url()?>template/css/bootstrap-multiselect.css" type="text/css"/>
    <!--script src="<?php echo base_url()?>template/js/tableExport.js" type="text/javascript" ></script>    
    <script src="<?php echo base_url()?>template/js/jquery.base64.js" type="text/javascript" ></script> 
    
    <script src="<?php echo base_url()?>template/js/jspdf/libs/sprintf.js" type="text/javascript" ></script>    
    <script src="<?php echo base_url()?>template/js/jspdf/jspdf.js" type="text/javascript" ></script>   
    <script src="<?php echo base_url()?>template/js/jspdf/libs/base64.js" type="text/javascript" ></script-->   
 
    <style type="text/css">
    table.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child{visibility: hidden;}

    .scheduler-border{
        border-bottom: 1px solid #555;
    }       
    
    #ui-datepicker-div{
        top: 297px !important;
    }   

    .customer-form .form-group{
        padding: 0px !important;
    }

    .address-div{
        padding-bottom: 30px;
    }

    .form-control{
        border: 1px solid #dcdcdc;
        border-radius: 0;
    }    
    </style>

    
</head>

<?php settimezome_taipei() ?>