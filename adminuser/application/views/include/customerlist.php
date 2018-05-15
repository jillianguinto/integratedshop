<?php if(ccheck_form(isuser_id(),"admin/customer")== true ): ?>

<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <i class="fa fa-fw fa-users"></i> Manage Customer
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <a href="<?php echo base_url();?>">
                                    <i class="fa fa-dashboard"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <strong>
                                <i class="fa fa-leaf"></i>
                                <?php  echo $this->uri->segment(1) . " [ ".count($getAllcustomer) ." ]"; ?>
                                </strong>
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10">
                    
                            <?php 
                            
                            if(!empty($errormessage)):
                                echo message_handler($errormessage,$errortype); 
                            endif;
                            
                            ?>
                    
                        <p><?php if(ccheck_form(isuser_id(),"admin/customer/manage")== true ): ?>
                        <a id="" href="#addcustomermodal" type="button" class="btn btn-success" data-toggle="modal" ><i class="fa fa-file-excel-o"></i> ADD NEW CUSTOMER</a>
                    <?php else: ?>
                        <!--<a class="disabled" id="" href="" type="button" class="btn btn-xs btn-success" data-toggle="" ><i class="fa fa-file-excel-o"></i> ADD NEW CUSTOMER</a>-->
                    <?php endif; ?> 
                            <!-- <button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button> -->
                            <!-- <button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button> -->
                        <!--<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button></p>-->
                        <a href="<?php echo base_url()?>index.php/Customers/exportCus" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a></p>

                    </div>              
                    <!-- <div class="col-lg-2">
                    
                    </div> -->


                   
                    <div class="col-lg-12">


                        <!-- test -->
                        

                        <!-- // -->
                        <form id="configform" name= "input" action="#" method="get">    
                            <table id="customerlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>

                                    <tr id="filterrow">
                                        <td style="font-size: 12px;">ID</td>
                                        <td style="font-size: 12px;">Name</td>
                                        <td style="font-size: 12px;">Email</td>
                                        <td style="font-size: 12px;">Customer Since</td>
                                        <td class="target" style="font-size: 12px;">Last Logged In</td>                             
                                        <td class="target" style="font-size: 12px;"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        
                                            if(empty($getAllcustomer)):
                                            
                                                echo '<tr id="no-records">';
                                                echo '<td><center>NO RECORD FOUND!</center></td>';
                                                echo '</tr>';   
                                                
                                            else:       
                                                                                
                                                foreach($getAllcustomer as $k=> $_list):




                                                    $customerId = $_list->entity_id;
                                                    
                                                    echo '<tr id="has-records">';
                                                    echo '<td>'.$_list->entity_id.'</td>';
                                                    echo '<td>'.$_list->first_name.'</td>';
                                                    echo '<td>'.$_list->email.'</td>';

                                                    $customer_since = date("F d, Y h:i:s",strtotime($_list->customer_since));
                                                    
                                                    echo '<td data-title="">'.$customer_since.'</td>'; ?>
                                                

                                                    <td><?php 
                                                        
                                                        
                                                            if (!empty($_list->last_login))
                                                            {
                                                                echo date("F d, Y h:i:s",strtotime($_list->last_login));
                                                              
                                                            }
                                                            



                                                    ?></td>                                             

                                                    <?php 
                                                    
                                                        
                                                    echo '<td>
                                                            <center>
                                                                <button type="button" class="_view btn btn-primary" title="View" data-id="'.$customerId.'"><i class="fa fa-eye"></i> </button>';
                                                                if(ccheck_form(isuser_id(),"admin/customer/manage")== true ){
                                                                    echo '<br /><br /> <button type="button" class="_view-x btn btn-danger" title="Edit" data-id="'.$customerId.'"><i class="fa fa-remove"></i> </button>';
                                                                }else{                                                  
                                                                    
                                                                }
                                                              
                                                    
                                                    echo '  </center>
                                                        </td>
                                                    </tr>';
                                            
                                                endforeach;
                                            endif;
                                        ?>
                                </tbody>

                            </table>    

                        </form>    
                        
                    </div>   
                   
                </div>

            </div>

        </div>
<?php else: echo "This module is not allowed for viewing!"?>
<!--<a id="" href="#addcustomermodal" type="button" class="btn btn-xs btn-success" data-toggle="modal" ><i class="fa fa-file-excel-o"></i> ADD NEW CUSTOMER</a>-->

<?php endif; ?> 
        <div class="addcustomer-modal" id="addcustomer-modal">                  
          
            <div id="addcustomermodal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">New Customer</h4>
                        </div>


                        <form id="form-user" class="form-horizontal col-sm-12" action="<?php echo get_baseurl() ?>/index.php/default/connectmage/index" method="post">
    
	                   <input class="form-control required" name="cmdEvent" type="hidden" value="newCusCmd"> 

                                        
                        <div class="modal-body">                                   
                                        
                            <div class="row">                                   
                                <div class="col-md-12 customer-form">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Account Information</legend>       
                                       
                                            <div class="form-group">
											
                                                <label class="col-sm-3 control-label">Group</label> 
												
                                                <div class="col-sm-9">                 
												
                                                    <select id="_accountgroup_id" name="group_id" class="form-control">
													
                                                        <option value="1">General</option>
														
                                                        <option value="2">Wholesale</option>
														
                                                        <option value="3">Retailer</option>
														
                                                    </select>
                                                    <input name="disable_auto_group_change" class="required" type="checkbox" id="checkboxSuccess" value="1">Disable Automatic Group Change Based on VAT ID
                                                
                                                </div>
                                            </div>
											
											 <div class="form-group"><label class="col-sm-3 control-label">Store</label>
                                                <div class="col-sm-9"> 
                                                   <select id="websiteId" name="websiteId" class="form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select">
                                                         <?php 
														foreach($storeID as $id => $value)
														{
														echo '<option value="'.$value->website_id.'">'.$value->name.'</option>';
														}
														 
														 ?>
                                                    </select>
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Prefix</label>
                                                <div class="col-sm-9">          
                                                    <input name="prefix" class="form-control required"  type="text">
                                                </div>
                                            </div>     
                                           
                                        
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">First Name</label>
                                                <div class="col-sm-9">  
                                                    <input name="firstname" id="firstname_value" class="form-control required" type="text">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Middle Name/Initial</label>
                                                <div class="col-sm-9"> 
                                                    <input name="middlename" class="form-control required"  type="text">
                                                </div>  
                                            </div>
                                            
                                     
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Last Name</label>
                                                <div class="col-sm-9"> 
                                                    <input name="lastname" id="lastname_value" class="form-control required"  type="text">
                                                </div>
                                            </div>      
                                        
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Suffix</label>
                                                <div class="col-sm-9"> 
                                                    <input name="suffix" class="form-control required" type="text">
                                                </div>  
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email</label>
                                                <div class="col-sm-9"> 
                                                    <input name="email" class="form-control required" type="text">
                                                </div>  
                                            </div>
                                           
                                            

												  <div class="form-group">
                                                <label class="col-sm-3 control-label">Date of Birth (YYYY-MM-DD) </label>                                           
                                                <div class="col-sm-9"> 
                                                    <div class='input-group date' id='datetimepicker1'>
														<input type='text' name="dob" class="form-control" />
														<span class="input-group-addon">
															<span class="glyphicon glyphicon-calendar"></span>
														</span>
													</div>
                                                </div>
                                            </div>   
									 <script type="text/javascript">
										$(function () {
											$('#datetimepicker1').datetimepicker({
												useCurrent : false
											});
										});
									</script>
                                          
                                            <!-- <div class="form-group"><label class="col-sm-3 control-label">Tax/VAT Number</label><input name="taxvat" class="form-control email" data-placement="top" data-trigger="manual" data-content="Must be a valid e-mail address (user@gmail.com)" type="text"></div> -->
                                            <div class="form-group"><label class="col-sm-3 control-label">Gender</label>
                                                <div class="col-sm-9"> 
                                                    <select id="_accountgender" name="gender" class="form-control phone">
                                                        <option value="" selected="selected"></option>
                                                        <option value="1">Male</option>
                                                        <option value="2">Female</option>
                                                    </select>
                                                </div>  
                                            </div>

                                            <div class="form-group"><label class="col-sm-3 control-label">Civil Status</label>
                                                <div class="col-sm-9"> 
                                                    <input id="_accountcivil_status" name="civil_status" value="" class="form-control email" type="text">
                                                </div>
                                            </div> 
                                 
                                 
                                 
                                        
                                            <div class="form-group"><label class="col-sm-3 control-label">Agree on Terms </label>
                                                <div class="col-sm-9"> 
                                                    <select id="_accountagree_on_terms" name="agree_on_terms" class="form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select">
                                                        <option value="0" selected="selected">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>  
                                            </div>
                                            <!-- <div class="form-group"><label class="col-sm-3 control-label">Medical Oncologist Title</label><input name="medprefix" class="form-control required"  type="text"></div>
                                            <div class="form-group"><label class="col-sm-3 control-label">Medical Oncologist First Name</label><input name="medfname" class="form-control required"  type="text"></div>
                                            <div class="form-group"><label class="col-sm-3 control-label">Medical Oncologist Last Name</label><input name="medlname" class="form-control required" type="text"></div>
                                             -->

                                            <!-- divison  -->

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Division </label>   
                                                <div class="col-sm-9">     

                                                    <?php 



                                                    ?>



                                                    <?php

                                                        //$unilabdivision = $getAllcustomer[0]->unilabdivision;                                   
                                                        //$unilabdivision = $get_division_data['option_id']->unilabdivision;  

                                                        $option_id = array();   
                                                        foreach($get_division_data['option_id'] as $k=>$n){
                                                            $option_id[] = $n;
                                                        }

                                                        $unilabdivision_name = array(); 
                                                        foreach($get_division_data['unilabdivision_name'] as $l=>$m){
                                                            $unilabdivision_name[] = $m;
                                                        }
                                                    ?>          
                                                    <select multiple="multiple" id="unilabdivision" class="unilabdivision form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select" name="unilabdivision[]">
                                                        <?php foreach ($unilabdivision_name as $num => $value): ?>                                  
                                                                <option value="<?php echo $option_id[$num]; ?>"><?php echo $unilabdivision_name[$num]; ?></option>                                                                  
                                                        <?php endforeach; ?>    
                                                        
                                                    </select>
                                                </div>  
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Group / Specialty</label>  
                                                <div class="col-sm-9">     

                                                    <?php

                                                        //$unilabgroup = $getAllcustomer[0]->unilabgroup;                            

                                                      

                                                        $option_id = array();   
                                                        foreach($get_group_data['option_id'] as $k=>$n){
                                                            $option_id[] = $n;
                                                        }

                                                        $unilabgroup_name = array();    
                                                        foreach($get_group_data['unilabgroup_name'] as $l=>$m){
                                                            $unilabgroup_name[] = $m;
                                                        }

                                                    ?>          
                                                    <select multiple="multiple" id="unilabgroup" class="unilabgroup form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select"  name="unilabgroup[]">
                                                        <?php foreach ($unilabgroup_name as $num => $value): ?>                                 
                                                                <option value="<?php echo $option_id[$num]; ?>"><?php echo $unilabgroup_name[$num]; ?></option>                                                                 
                                                        <?php endforeach; ?>    
                                                        
                                                    </select>                           




                                                     
                                                </div>  
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Password</label>
                                                <div class="col-sm-9"> 
                                                    <input name="password" class="form-control required" type="password">
                                                </div>  
                                            </div>
                                    </fieldset>     
									
                                </div>                                                  
                  
                                    <!-- 2 -->
                                <div class="col-md-12 customer-form">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Addresses</legend>     
                                        
                                        <input class="form-control required"  type="hidden" name="address_firstname" value="">
                                        <input class="form-control required"  type="hidden" name="address_prefix" value="">                             
                                        <input class="form-control required"  type="hidden" name="address_middlename" value="">
                                        <input class="form-control required"  type="hidden" name="address_lastname" value="">
                                        <input class="form-control required"  type="hidden" name="address_suffix" value="">
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Company</label>
                                            <div class="col-sm-9">          
                                                <input class="form-control required"  type="text" name="address_company">
                                            </div>  
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Street Address</label>
                                            <div class="col-sm-9">   
                                                <input class="form-control required"  type="text" name="address_street">
                                            </div>  
                                        </div>
                                        

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">City </label>
                                            <div class="col-sm-9">   
                                                <input class="form-control required"  type="text" name="address_city">
                                            </div>  
                                        </div>
                                            
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Country </label>   
                                            <div class="col-sm-9">                      
                                                <select name="country_id" class="form-control required">
                                                    <option value=""> </option>
                                                    <option value="AF">Afghanistan</option>
                                                    <option value="PH" selected="selected">Philippines</option>
                                                    <option value="US">United States</option>
                                                </select>
                                            </div>  
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">State/Province</label>    
                                            <div class="col-sm-9">                                  
                                                <select name="address_region_id" class="form-control required">
                                                    <option value="0" selected="selected">-- Please select --</option>
                                                    <option value="485">Abra</option>
                                                    <option value="486">Agusan del Norte</option>
                                                    <option value="487">Agusan del Sur</option>
                                                    <option value="488">Aklan</option>
                                                    <option value="489">Albay</option>
                                                    <option value="490">Antique</option>
                                                    <option value="491">Apayao</option>
                                                    <option value="492">Aurora</option>
                                                    <option value="493">Basilan</option>
                                                    <option value="494">Bataan</option>
                                                    <option value="495">Batanes</option>
                                                    <option value="496">Batangas</option>
                                                    <option value="497">Benguet</option>
                                                    <option value="498">Biliran</option>
                                                    <option value="499">Bohol</option>
                                                    <option value="500">Bukidnon</option><option value="501">Bulacan</option>
                                                    <option value="502">Cagayan</option><option value="503">Camarines Norte</option>
                                                    <option value="504">Camarines Sur</option><option value="505">Camiguin</option>
                                                    <option value="506">Capiz</option><option value="507">Catanduanes</option>
                                                    <option value="508">Cavite</option><option value="509">Cebu</option>
                                                    <option value="510">Compostela Valley</option><option value="511">Cotabato</option>
                                                    <option value="512">Davao del Norte</option><option value="513">Davao del Sur</option>
                                                    <option value="514">Davao Oriental</option><option value="515">Dinagat Islands</option>
                                                    <option value="516">Eastern Samar</option><option value="517">Guimaras</option>
                                                    <option value="518">Ifugao</option><option value="519">Ilocos Norte</option>
                                                    <option value="520">Ilocos Sur</option><option value="521">Iloilo</option>
                                                    <option value="522">Isabela</option><option value="523">Kalinga</option>
                                                    <option value="524">La Union</option><option value="525">Laguna</option>
                                                    <option value="526">Lanao del Norte</option><option value="527">Lanao del Sur</option>
                                                    <option value="528">Leyte</option><option value="529">Maguindanao</option>
                                                    <option value="530">Marinduque</option><option value="531">Masbate</option>
                                                    <option value="532">Metro Manila</option><option value="533">Misamis Occidental</option>
                                                    <option value="534">Misamis Oriental</option><option value="535">Mountain Province</option>
                                                    <option value="536">Negros Occidental</option><option value="537">Negros Oriental</option>
                                                    <option value="538">Northern Samar</option><option value="539">Nueva Ecija</option>
                                                    <option value="540">Nueva Vizcaya</option><option value="541">Occidental Mindoro</option>
                                                    <option value="542">Oriental Mindoro</option><option value="543">Palawan</option>
                                                    <option value="544">Pampanga</option><option value="545">Pangasinan</option>
                                                    <option value="546">Quezon</option><option value="547">Quirino</option>
                                                    <option value="548">Rizal</option><option value="549">Romblon</option>
                                                    <option value="550">Samar</option><option value="551">Sarangani</option>
                                                    <option value="552">Siquijor</option><option value="553">Sorsogon</option>
                                                    <option value="554">South Cotabato</option><option value="555">Southern Leyte</option>
                                                    <option value="556">Sultan Kudarat</option><option value="557">Sulu</option>
                                                    <option value="558">Surigao Del Norte</option><option value="559">Surigao Del Sur</option>
                                                    <option value="560">Tarlac</option><option value="561">Tawi-Tawi</option>
                                                    <option value="562">Zambales</option>
                                                    <option value="563">Zamboanga del Norte</option>
                                                    <option value="564">Zamboanga del Sur</option>
                                                    <option value="565">Zamboanga Sibugay</option>
                                                </select>
                                            </div>  
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Zip/Postal Code</label>
                                            <div class="col-sm-9"> 
                                                <input class="form-control required"  type="text" name="address_postcode">
                                            </div>  
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Telephone </label>
                                            <div class="col-sm-9"> 
                                                <input class="form-control required"  type="text" name="address_telephone">
                                            </div>  
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Fax </label>
                                            <div class="col-sm-9"> 
                                                <input class="form-control required"  type="text" name="address_fax">
                                            </div>
                                        </div>  

                                        <!-- <div class="form-group"><label >VAT number </label><input class="form-control required"  type="text" name=""></div> -->
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Mobile</label>
                                            <div class="col-sm-9"> 
                                                <input class="form-control required"  type="text" name="">
                                            </div>
                                        </div>  
 
                                    </div>                          
                                </fieldset>        
                                                                            
                            </div>

                                      
                        </div>  

                        

                        <div class="modal-footer">
						
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							
                            <button type="submit" class="btn btn-primary pull-right">Save Customer</button> 
							<p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>
                            
                        </div>  

                        </form>      
                        
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>
        </div>      
    </div>

    <form name="customerform" action="?" method="post">
        <input type="hidden" value="" name="customerId" />
    </form>

</body>

<script>
$(document).ready(function() {


    $('input[name="password"]').removeAttr('disabled');
    $('input[name="password_hash"]').click(function() {
         $('input[name="password"]').val('');
         $('input[name="password"]').attr('disabled',this.checked);
    });
    

    
    //$('#customerlist').DataTable();
    
    $("body").on('click', '._view', function (){        
        var customerId = $(this).attr('data-id');
        $("[name=customerId]").val(customerId);
        $("[name=customerform]").attr("action","<?php echo base_url()?>index.php/customers/view");
        $("[name=customerform]").submit();      
    }); 
    
});

function createpdf()
{
    window.location.href = "<?php echo base_url();?>index.php/pdf/orders";
}   

$(function () {
   $("#unilabdivision").attr("size",$(".unilabdivision option").length);
   $("#unilabgroup").attr("size",$(".unilabgroup option").length);
});



// Delete customer
$('button._view-x').click(function(e){

    e.preventDefault();

    var id = $(this).attr('data-id');

    var dataObject = {
        'entity_id':id
    }

    $.ajax({
        url: "<?php echo get_baseurl() ?>/adminuser/index.php/customers/delete",   
        type: 'post',  
        asynchronous: true,
     cache: false,
        data: dataObject, 
        beforeSend: function() {
            var sku = dataObject['sku'];
            var x = confirm("Are you sure you want to delete this customer id?\n" +id );
            if (x){
                return true;
            }
            else{

                return false;
            }   
        },
        success: function(response) 
        {     
                        
            if(response.status =='true'){
                alert(response.message);    
                
            }                                           

            window.location.reload(true);           

            
        }
    });


});
</script>

<script type="text/javascript">
// $(function () {
//    $(".select-multiple").attr("size",$(".select-multiple option").length);
// });

$(function() {

    $('.new-addr-btn').on('click', '.remove', function() {
        $(this).closest('div[class^=div]').remove();
    });    

    $(".add-new-customer").on("click",function(){

        var firstname = $('input[name=firstname]').val();
        var lastname  = $('input[name=lastname]').val();
        var i =0;
        $(".new-addr-btn").append();
    });
});

$("[name='prefix']").keyup(function(){  
    $("[name='address_prefix']").attr('value', $(this).val());
});

$("[name='firstname']").keyup(function(){
    $("[name='address_firstname']").attr('value', $(this).val());
});

$("[name='middlename']").keyup(function(){  
    $("[name='address_middlename']").attr('value', $(this).val());
});

$("[name='lastname']").keyup(function(){    
    $("[name='address_lastname']").attr('value', $(this).val());
});

$("[name='suffix']").keyup(function(){  
    $("[name='address_suffix']").attr('value', $(this).val());
});
</script>


<script type="text/javascript">
$(document).ready(function() {
    $('#form-user').formValidation({
            message: 'This value is not valid',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                firstname: {
                    message: 'The firstname is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The firstname is required and can\'t be empty'
                        }
                    }
                },
                lastname: {
                    message: 'The lastname is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The lastname is required and can\'t be empty'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'The email address is required and can\'t be empty'
                        },
                        emailAddress: {
                            message: 'The input is not a valid email address'
                        }
                    }
                },
                dob: {
                    message: 'The birthdate is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The birthdate is required and can\'t be empty'
                        }
                    }
                },
                gender: {
                    message: 'The gender is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The gender is required and can\'t be empty'
                        }
                    }
                },
                civil_status: {
                    message: 'The civil status is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The civil status is required and can\'t be empty'
                        }
                    }
                },               
                // password: {
                //     validators: {
                //         notEmpty: {
                //             message: 'The password is required and can\'t be empty'
                //         }
                //     }
                // },
                address_street: {
                    message: 'The street is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The street is required and can\'t be empty'
                        }
                    }
                },
                address_city: {
                    message: 'The city is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The city is required and can\'t be empty'
                        }
                    }
                },
                address_postcode: {
                    message: 'The postcode is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The postcode is required and can\'t be empty'
                        }
                    }
                },
            }
        })
        .on('success.form.fv', function(e) {
            // Prevent form submission
            e.preventDefault();

            var $form = $(e.target),
            fv    = $form.data('formValidation');

            // Use Ajax to submit form data
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function(result) {
					alert(result);
                }
				error: function(result){
					alert("Error");
				}
            });
        });
});
</script>


<style type="text/css">
#customerlist_filter{
    display: none;
}

.customer-form .form-group{
    padding: 0px !important;
}

.form-control{
    border: 1px solid #dcdcdc;
    border-radius: 0;
}
</style>