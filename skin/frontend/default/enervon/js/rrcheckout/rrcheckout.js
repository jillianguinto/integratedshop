/*

@author: leandro talavera

Dated: 7-26-2017



==================

developers Note:

place your function inside of .read/.load

jQuery(document).ready is fired when the DOM is ready for interaction. (this load first than document.load())

jQuery(window).load is called when all assets are done loading, including images.  (this load after document.ready())



js beautifier = http://jsbeautifier.org/ (kind maintain psr2 indention thanks! - leandro)

==================



File Log(delete/replace the logs if you done some changes thnks! - leandro):



note: 

this were just a copied js from rrcehckout.phtml - leandro(7-27-17).

variable baseurl were declared in rrcehckout phtml - leandro(7-27-17).

*/





/* PLEASE  dont declare your DOM event or any condition outside or .document.ready

 - leandro (only variables should be declare outside of document.ready block) */





 

var currentPage = 'page-1-shipping';

var showBillingAddress = 'no';

var showPlaceOrderAlready = 'no';

var pageDone = {

    'pageOne': true,

    'pageTwo': false,

    'pageThree': false

};





jQuery(document).ready(function() {

    jQuery.noConflict();





    /* ======================== START window.load ====================*/

    jQuery(window).load(function() {

        jQuery.noConflict();

        showPageOne();

    });

    /* ======================== END window.load ====================*/



    var paymentname = jQuery("#paymentmethod").val();



    if (paymentname != 'bankdeposit') {

        jQuery("#payment_instruction_bankdeposit").css({

            display: "none"

        });

    } else {

        jQuery("#payment_instruction_bankdeposit").css({

            display: "block"

        });

    }



    if (paymentname != 'perapal') {

        jQuery("#payment_instruction_cebuana").css({

            display: "none"

        });

    } else {

        jQuery("#payment_instruction_cebuana").css({

            display: "block"

        });

    }



    //BILLING ADDRESS

    jQuery('.numbersOnly').keyup(function() {

        this.value = this.value.replace(/[^\d].+/, "");

    });



    function showPageOne() {

        jQuery('.shipping-address').fadeIn('slow');

        jQuery('.shipping-method').hide();

        jQuery('.payment-method').hide();

        jQuery('.billing-address').hide();

        jQuery('.orderview-wrapper').hide();

        pageDone['pageOne'] = true;

        currentPage = 'page-1-shipping';



        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'inline-block');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #fff');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#fff');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #fff');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#fff');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'none');



    }



    function showPagetwo() {

        var isChecked = jQuery('#yes-billing:checked').val() ? 1 : 0;

        var notChecked = jQuery('#no_billing:checked').val() ? 1 : 0;

        var check_billing = isChecked + notChecked;



        jQuery('.checkout-nav').show();

        if (!jQuery('#shipping_firstname').val() || !jQuery('#shipping_lastname').val() || !jQuery('#shipping_mobile').val() || !jQuery('#shipping_postcode').val() || !jQuery('#shipping_city').val() || !jQuery('#shipping_street1').val() || !jQuery('#shipping_region_id').val() || !jQuery('#shipping_country_id').val()) {

            //alert('Please fill up all the required fields');

            return false;

        }else if( check_billing == 0 ){
            
            jQuery(".select_billing_yes_no").focus();
            return false;

        }

        jQuery('.shipping-address').hide();

        jQuery('.shipping-method').fadeIn('slow');

        jQuery('.payment-method').hide();

        jQuery('.billing-address').hide();

        jQuery('.orderview-wrapper').hide();



        currentPage = 'page-2-shipping-option';

        pageDone['pageTwo'] = true;



        return true;



    }



    function showPagethree() {

        var isSelected = jQuery('.shipping_method:checked').val() ? 1 : 0;



        jQuery('.checkout-nav').show();

        if (isSelected == 0) {

            alert('Please select your shipping option');

            return false;

        }

        jQuery('.shipping-address').hide();

        jQuery('.shipping-method').hide();

        jQuery('.payment-method').fadeIn('slow');

        jQuery('.billing-address').hide();

        jQuery('.orderview-wrapper').hide();

        currentPage = 'page-3-payment-option';

        pageDone['pageThree'] = true;

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'inline-block');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #fff');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#fff');


        if (jQuery('[name="payment[method]"]:checked').val() == 'bankdeposit') {

            jQuery('#payment_instruction_bankdeposit').show();

        }

        return true;

    }



    function showPageFour() {

        var isSelected = jQuery('.payment_method:checked').val() ? 1 : 0;



        jQuery('.checkout-nav').show();

        if (isSelected == 0) {

            alert('Please select a payment method');

            return false;

        }



        var getPayment = jQuery("input[name='payment[method]']:checked").val();

        var getPaymentName = '';



        if (getPayment == 'perapal') {

            getPaymentName = 'Cebuana Lhuillier';

        } else if (getPayment == 'bpisecurepay') {

            getPaymentName = 'Credit Card';

        } else if (getPayment == 'cashondelivery') {

            getPaymentName = 'Cash On Delivery';

        } else if (getPayment == 'bankdeposit') {

            getPaymentName = 'Bank Deposit';

        }

        jQuery('#payment_method').html(getPaymentName);

        jQuery('.shipping-address').hide();

        jQuery('.shipping-method').hide();

        jQuery('.payment-method').hide();



        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#ffff00');



        //this shows weather billing or placing order page will be shown - leandro

        if (showBillingAddress == 'yes') {

            jQuery('.billing-address').fadeIn('slow');

            currentPage = 'page-4-billing-add';

            jQuery('.orderview-wrapper').hide();

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'inline-block');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'none');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #fff');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#fff');

        } else {  //same shipping and billing

            jQuery('.checkout-nav').hide();

            jQuery('.orderview-wrapper').fadeIn('slow');

            updateFromShippingSummary();

            console.log('a1');

            showPlaceOrderAlready = 'yes';
            currentPage = 'page-5-review';

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'none');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'inline-block');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #ffff00');

            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#ffff00');

        }

        return true;
    }



    function showPageFive() {

        if (!jQuery('#billing_firstname').val() || !jQuery('#billing_lastname').val() || !jQuery('#billing_mobile').val() || !jQuery('#billing_street1').val() || !jQuery('#billing_city').val() || !jQuery('#billing_postcode').val() || !jQuery('#billing_region_id').val() || !jQuery('#billing_country_id').val()) {

            //alert('Please fill up all the required fields');

            return false;

        }

        onchangeBillingSave();

        jQuery('.shipping-address').hide();

        jQuery('.shipping-method').hide();

        jQuery('.payment-method').hide();

        jQuery('.billing-address').hide();

        jQuery('.orderview-wrapper').fadeIn('slow');

        updateFromShippingSummary();

        showPlaceOrderAlready = 'yes';

        jQuery('.checkout-nav').hide();



        if (showBillingAddress == 'yes') {

            updateFromBillingSummary();

        }

        currentPage = 'page-5-review';



        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'none');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'inline-block');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #ffff00');

        jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#ffff00');

        return true;
    }

    //selecting Shipping Address Option
    jQuery('.shipping_address_id').click(function() {

        var addressid = jQuery(this).val();

        selectAddress_shipping(addressid);

    });

    //selecting Shipping Address Option
    jQuery('.shipping_address_id').click(function() {

        var addressid = jQuery(this).val();

        selectAddress_shipping(addressid);

    });



    function selectAddress_shipping(addressid) {
        
        jQuery("#loading_icon_checkout").css("display", "block");  

        var addID = addressid; //jQuery(this).val();        

        jQuery.post(baseurl + '/rracheckout/address/addressbill', {

                addressID: addID

            },

            function(addressData) {

                var dataobj = jQuery.parseJSON(addressData);



                if (dataobj.success == true) {
                    jQuery("#loading_icon_checkout").css("display", "none");  
                    jQuery.post(baseurl + '/rracheckout/ajax/onepage', {

                        region_id: dataobj.region_id,

                        city_id: dataobj.city

                    }, function(res) {

                        jQuery("#shipping_city").html(res);

                        check(jQuery('#shipping_region_id').val(), jQuery('#shipping_city').val());

                    });

                    jQuery('#shippingAddressId').val(addressid);
                    jQuery('#shipping_firstname').val(dataobj.firstname);
                    jQuery('#shipping_lastname').val(dataobj.lastname);
                    jQuery('#shipping_country_id').val(dataobj.country_id);
                    jQuery('#shipping_postcode').val(dataobj.postcode);
                    jQuery('#shipping_telephone').val(dataobj.telephone);
                    jQuery('#shipping_mobile').val(dataobj.mobile);
                    jQuery('#shipping_street1').val(dataobj.street);
                    jQuery('#shipping_region_id').val(dataobj.region_id);
                    jQuery('#shipping_region').val(dataobj.region);
                    jQuery('#shipping_city').val(dataobj.city);
                    jQuery('span.shipping_desc').css('display', 'none');
                    jQuery('input[name=shippingmethod]').attr('checked', false);
                } else {
                    jQuery('#shipping_firstname').val('');
                    jQuery('#shipping_lastname').val('');
                    jQuery('#shipping_postcode').val('');
                    jQuery('#shipping_city').val('');
                    jQuery('#shipping_telephone').val('');
                    jQuery('#shipping_mobile').val('');
                    jQuery('#shipping_street1').val('');
                    jQuery('#shipping_region_id').val('');
                    jQuery('#shipping_region').val('');
                    jQuery('#shipping_siab').show();
                }
            });
    }

    jQuery('#billing_firstname').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_fname = jQuery('#billing_firstname').val();
            jQuery('#shipping_firstname').val(bill_fname);
        }

    });



    jQuery('#billing_lastname').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_lname = jQuery('#billing_lastname').val();
            jQuery('#shipping_lastname').val(bill_lname);
        }

    });



    jQuery('#billing_country_id').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_country = jQuery('#billing_country_id').val();
            jQuery('#shipping_country_id').val(bill_country);
        }



    });



    jQuery('#billing_postcode').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_postcode = jQuery('#billing_postcode').val();
            jQuery('#shipping_postcode').val(bill_postcode);

        }



    });



    jQuery('#billing_region_id').change(function() {

        var billing_region = jQuery('#billing_region_id').val();

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            jQuery('#shipping_region_id').val(billing_region);

            setTimeout(function() {
                jQuery('input[name=shippingmethod').prop('checked', true).trigger('click');
            }, 2000);

        }

    });



    jQuery('#billing_city').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_city = jQuery('#billing_city').val();
            jQuery('#shipping_city').val(bill_city);
        }

    });



    jQuery('#billing_street1').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_street = jQuery('#billing_street1').val();
            jQuery('#shipping_street1').val(bill_street);
        }

    });



    jQuery('#billing_telephone').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {
            var bill_tel = jQuery('#billing_telephone').val();
            jQuery('#shipping_telephone').val(bill_tel);
        }



    });



    jQuery('#billing_mobile').change(function() {

        if (jQuery('#billing_use_for_shipping_yes').is(':checked')) {

            var bill_mobile = jQuery('#billing_mobile').val();

            jQuery('#shipping_mobile').val(bill_mobile);

        }

    });

    check(jQuery('#shipping_region_id').val(), jQuery('#shipping_city').val());

    jQuery(document).on('change', '#shipping_city', function() {

        check(jQuery('#shipping_region_id').val(), jQuery('#shipping_city').val());

    });

    jQuery(document).on('change', '#shipping_region_id', function() {
        check(jQuery('#shipping_region_id').val(), jQuery('#shipping_city').val());
    });

    function shippingnewsave() {
        var shippingform = jQuery('#shipping_address_form').serialize();
        jQuery.post(baseurl + '/checkout/onepage/savenewShipping/',
            shippingform,
            function(shippingformdata) {
                var dataobj = jQuery.parseJSON(shippingformdata);
                if (dataobj.reload == true) {
                    if(dataobj.shipping_id != ""){
                        jQuery('#shippingAddressId').val(dataobj.shipping_id);
                    }
                } else {
                    return;
                }
            });
    }



    jQuery('#shipping_save_in_address_book_yes').click(function() {
        if (confirm('Do you want to save your shipping address?')) {
            jQuery("#shipping_save_in_address_book").click();
        }

    });



    jQuery('#shipping_save_in_address_book').change(function() {

        if (jQuery('#shipping_save_in_address_book').is(':checked')) {
            jQuery("#shipping_save_in_address_book").val(1);
            <!-- // saving for new address will be after place order event -->
        } else {
            jQuery("#shipping_save_in_address_book").val(0);
        }

    });



    jQuery(document).on('change', '#shipping_region_id', function() {

        var data = jQuery(this).val();
        jQuery.post(baseurl + '/rracheckout/ajax/onepage', {

            region_id: data

        }, function(res) {

            jQuery("#shipping_city").html(res);

        });

        jQuery('span.shipping_desc').css('display', 'none');
        jQuery('input[name=shippingmethod').attr("checked", false);
    });



    function onchangeBillingSave(){

        var billingdata = jQuery('#billing_address_form').serialize();
        jQuery.post(baseurl + '/checkout/onepage/savenewBilling/',
            billingdata,
            function(billingformdata) {
                var dataobj = jQuery.parseJSON(billingformdata);
                if (dataobj.reload == true) {
                    if(dataobj.billing_id != ""){
                        jQuery('#billing_id').val(dataobj.billing_id);
                    }
                } else {
                    return;
                }
        });


    }

    jQuery('.billing_address_id').click(function() {
        var addressid = jQuery(this).val();
        selectAddress_billing(addressid);

    });


    function selectAddress_billing(addressid) {
        
        jQuery("#loading_icon_checkout_billing").css("display", "block");

        var addID = addressid; //jQuery('#billing_address_id').val();       

        jQuery.post(baseurl + '/rracheckout/address/addressbill', {
                addressID: addID
            },

            function(addressData) {

                var dataobj = jQuery.parseJSON(addressData)

                if (dataobj.success == true) {
                    jQuery("#loading_icon_checkout_billing").css("display", "none");
                    jQuery('#billing_id').val(dataobj.billing_id);
                    jQuery('#billing_firstname').val(dataobj.firstname);
                    jQuery('#billing_lastname').val(dataobj.lastname);
                    jQuery('#billing_country_id').val(dataobj.country_id);
                    jQuery('#billing_postcode').val(dataobj.postcode);
                    jQuery('#billing_telephone').val(dataobj.telephone);
                    jQuery('#billing_mobile').val(dataobj.mobile);
                    jQuery('#billing_street1').val(dataobj.street);
                    jQuery('#billing_region_id').val(dataobj.region_id);
                    jQuery('#billing_region').val(dataobj.region);

                    jQuery.post(baseurl + '/rracheckout/ajax/webuiltthiscity', {
                        region_id: dataobj.region_id,
                        cityval: dataobj.city
                    }, function(res) {
                        jQuery("#billing_city").html(res);
                    });

                    jQuery('#billing_city').val(dataobj.city);

                    if (document.getElementById('billing_use_for_shipping_yes').checked) {
                        check2(dataobj.region_id, dataobj.city);
                        address();

                        jQuery.post(baseurl + '/rracheckout/ajax/onepage', {
                            region_id: dataobj.region_id,
                            city_id: dataobj.city
                        }, function(res) {
                            jQuery("#shipping_city").html(res);
                        });

                        setTimeout(function() {
                            jQuery('input[name=shippingmethod').prop('checked', true).trigger('click');
                        }, 2000);

                    }

                    var paymentdata = jQuery("#billing_address_form").serialize();

                    jQuery.post(baseurl + '/checkout/onepage/saveBilling/',
                        paymentdata,
                        function(paymentdataret) {
                            var dataobj = jQuery.parseJSON(paymentdataret);
                            if (dataobj.success == true) {
                                jQuery('#amt_' + dataobj.carrier_code + '').html(dataobj.carrier_method_title + ' - ' + dataobj.shippingrate);
                                jQuery('.shipping_name').html(dataobj.carrier_title + ' - ' + dataobj.carrier_method_title);
                                jQuery('.shipping_amount').html(dataobj.shippingrate);
                                jQuery('.grand_total').html(dataobj.grandTotal);
                                jQuery('.tax_amount').html(dataobj.taxAmount);
                                jQuery('#submit_btn').removeAttr('disabled');
                            }
                        });

                } else {
                    jQuery('#billing_firstname').val('');
                    jQuery('#billing_lastname').val('');
                    jQuery('#billing_postcode').val('');
                    jQuery('#billing_city').val('');
                    jQuery('#billing_telephone').val('');
                    jQuery('#billing_mobile').val('');
                    jQuery('#billing_street1').val('');
                    jQuery('#billing_region_id').val('');
                    jQuery('#billing_region').val('');
                    jQuery('#billing_siab').show();
                }

            });

    }



    jQuery('#billing_save_in_address_book_yes').click(function() { //not working pa

        if (confirm('Do you want to save your billing address?')) {

            jQuery("#billing_save_in_address_book").click();

        }

    });



    jQuery('#billing_save_in_address_book').change(function() {

        if (jQuery('#billing_save_in_address_book').is(':checked')) {

            jQuery("#billing_save_in_address_book").val(1);

            <!-- // saving for new address will be after place order event -->

        } else {

            jQuery("#billing_save_in_address_book").val(0);

        }

    });



    jQuery(document).on('change', '#billing_region_id', function() {

        var data = jQuery(this).val();

        jQuery.post(baseurl + '/rracheckout/ajax/onepage', {
            region_id: data
        }, function(res) {
            jQuery("#billing_city").html(res);
        });

        if (document.getElementById('billing_use_for_shipping_yes').checked) {
            jQuery.post(baseurl + '/rracheckout/ajax/onepage', {
                region_id: data
            }, function(res) {
                jQuery("#shipping_city").html(res);
            });

        }

    });



    function updateFromShippingSummary() {

        jQuery('#shipping_fname').html(jQuery('#shipping_firstname').val() + '&nbsp;');
        jQuery('#shipping_lname').html(jQuery('#shipping_lastname').val());
        jQuery('#shipping_addres').html(jQuery('#shipping_street1').val());
        jQuery('#shipping_city_2').html(jQuery('#shipping_city option:selected').text());
        jQuery('#shipping_region_2').html(jQuery('#shipping_region_id option:selected').text());
        jQuery('#shipping_country').html(jQuery('#shipping_country_id').val());
        jQuery('#shipping_postcode2').html(jQuery('#shipping_postcode').val());
        jQuery('#shipping_mobile_num').html(jQuery('#shipping_mobile').val());

        if (showBillingAddress == 'no') {
            jQuery('#billing_firstname').val(jQuery('#shipping_firstname').val());
            jQuery('#billing_lastname').val(jQuery('#shipping_lastname').val());
            jQuery('#billing_country_id').val(jQuery('#shipping_country_id').val());
            jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            jQuery('#billing_telephone').val(jQuery('#shipping_mobile').val());
            jQuery('#billing_mobile').val(jQuery('#shipping_mobile').val());
            jQuery('#billing_street1').val(jQuery('#shipping_street1').val());
            jQuery('#billing_region_id').val(jQuery('#shipping_region_id option:selected').val());
            jQuery('#billing_region').val(jQuery('#shipping_region_id option:selected').html());

            jQuery.post(baseurl + '/rracheckout/ajax/webuiltthiscity', {
                region_id: jQuery('#shipping_region_id option:selected').val(),
                cityval: jQuery('#shipping_city option:selected').text()
            }, function(res) {
                jQuery("#billing_city").html(res);
                jQuery('#billing_city option:selected').html(jQuery('#shipping_city option:selected').html());
                jQuery('#billing_city').val(jQuery('#shipping_city option:selected').val());
            });

        }

        jQuery('#billing_fname').html(jQuery('#shipping_firstname').val());
        jQuery('#billing_lname').html(jQuery('#shipping_lastname').val());
        jQuery('#billing_address').html(jQuery('#shipping_street1').val());
        jQuery('#billing_city_2').html(jQuery('#shipping_city option:selected').text());
        jQuery('#billing_region_2').html(jQuery('#shipping_region_id option:selected').text());
        jQuery('#billing_country').html(jQuery('#shipping_country_id').val());
        jQuery('#billing_postcode2').html(jQuery('#shipping_postcode').val());
        jQuery('#billing_mobile_2').html(jQuery('#shipping_mobile').val());
    };



    //Place Order - Order View

    function updateFromBillingSummary() {

        jQuery('#billing_fname').html(jQuery('#billing_firstname').val() + '&nbsp;');
        jQuery('#billing_lname').html(jQuery('#billing_lastname').val());
        jQuery('#billing_address').html(jQuery('#billing_street1').val());
        jQuery('#billing_city_2').html(jQuery('#billing_city option:selected').text());
        jQuery('#billing_region_2').html(jQuery('#billing_region_id option:selected').text());
        jQuery('#billing_country').html(jQuery('#billing_country_id').val());
        jQuery('#billing_postcode2').html(jQuery('#billing_postcode').val());
        jQuery('#billing_mobile_2').html(jQuery('#billing_mobile').val());

    };



    jQuery(document).on('change', '#payment-method-form', function() {

        var get_payment = jQuery("input[name='payment[method]']:checked").val();
        var get_payment_name = '';

        if (get_payment == 'perapal') {
            get_payment_name = 'Cebuana Lhuillier';

        } else if (get_payment == 'bpisecurepay') {
            get_payment_name = 'Bank Deposit';

        } else if (get_payment == 'cashondelivery') {
            get_payment_name = 'Cash On Delivery';

        } else if (get_payment == 'bankdeposit') {
            get_payment_name = 'Bank Deposit';

        }

        jQuery('#payment_method').html(get_payment_name);

    });



    jQuery(document).on('change', '.shipping-address select', function() {

        updateFromBillingSummary();

    });



    jQuery('[name="select-billing"]').change(function() {

        var selecteVal = jQuery(this).val();

        if (selecteVal != 0) {
            showBillingAddress = 'no';
            updateFromShippingSummary();

        } else {
            showBillingAddress = 'yes';
            updateFromBillingSummary();

        }

    });





    function manualFormValidation(formId) {

        var formEvent = new VarienForm(formId, true);
        var e = null;

        try {
            formEvent.validator.validate();

        } catch (e) {

            console.log('form submitted might not exist - see the log below'); // - leandro
            console.log(e);

        }



    };



    jQuery(document).on('click', 'td .shipping_link, span#go-back-address', function() {
        showPageOne();
        jQuery('.checkout-nav').show();

    });

    jQuery(document).on('click', 'td .payment_link, span#go-back-payment-method', function() {

        jQuery('.checkout-nav').show();

        if (pageDone['pageOne'] && pageDone['pageTwo']) {
            showPagethree();
        } else {
            alert('Please fill up all the required fields');
        }

    });

    jQuery(document).on('click', 'td .placeorder_link', function() {

        jQuery('.checkout-nav').show();

        if (!pageDone['pageOne'] || !pageDone['pageTwo'] || !pageDone['pageThree']) {
            alert('Please fill up all the required fields');

            return false;
        }

        jQuery('.shipping-address').hide();
        jQuery('.shipping-method').hide();
        jQuery('.payment-method').hide();

        //this shows whether billing or placing order page will be shown - leandro

        if (showBillingAddress == 'yes') {
            jQuery('.orderview-wrapper').hide();
            jQuery('.billing-address').fadeIn('slow');
            currentPage = 'page-4-billing-add';
            updateFromBillingSummary();
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'none');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #ffff00');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#ffff00');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'inline-block');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'none');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #fff');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#fff');

        } else {
            var billingAddressOption = jQuery('[name="select-billing"]').val();

            updateFromShippingSummary();
            jQuery('.checkout-nav').hide();
            jQuery('.orderview-wrapper').fadeIn('slow');
            onchangeBillingSave();
            currentPage = 'page-5-review';
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._shipping .shipping_').css('display', 'none');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._payment .payment_').css('display', 'none');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment').css('border-top', '5px solid #ffff00');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .payment_link').css('color', '#ffff00');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline_t ._placeholder .placeholder_').css('display', 'inline-block');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeholder').css('border-top', '5px solid #ffff00');
            jQuery('.rracheckout-checkout-onepage .right-menu .cart_timeline .placeorder_link').css('color', '#ffff00');

        }

    });



    jQuery(document).on('click', 'span#go-back-address2', function() {

        document.getElementById('no_billing').checked = true;

        showBillingAddress = 'yes';
        showPageFour();

    });



    jQuery(document).on('click', '.checkout-paging', function() {

        var response = false;

        switch (currentPage) {

            case 'page-1-shipping':
                manualFormValidation('shipping_address_form');
                shippingnewsave();
                showPagetwo();

                break;

            case 'page-2-shipping-option':

                showPagethree();

                break;

            case 'page-3-payment-option':

                showPageFour();

                break;

            case 'page-4-billing-add':

                manualFormValidation('billing_address_form');
                showPageFive();

                break;
        }

    });

    jQuery(document).on('change', '#not-credicard', function(){
        if(jQuery(this).is(':checked')){
            jQuery('#bpisecurepay').prop('checked', false);
            jQuery('#bpisecurepay').closest('li').hide();
        }else{
            jQuery('#bpisecurepay').closest('li').show();
        }
    });



});