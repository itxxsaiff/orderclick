$(function () {
    "use strict";
    var dtToday = new Date();
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if (month < 10)
        month = '0' + month.toString();
    if (day < 10)
        day = '0' + day.toString();
    var maxDate = year + '-' + month + '-' + day;
    $('#delivery_dt').attr('min', maxDate);
});
$(document).ready(function () {
    "use strict";
    $("input[name='cart-delivery']").on("click", function () {
        var test = $(this).val();

        if (test == 1) {
            $("#open").show();
            $("#shippinginfodiv").show();
            $("#delivery").show();
            $("#pickup").hide();
            $("#delivery_date").show();
            $("#pickup_date").hide();
            $("#table_show").hide();
            $("#data_time").show();
            $("#delivery-charge-section").show();
            var sub_total = parseFloat($('#sub_total').val());
            var delivery_charge = parseFloat($('#delivery_charge').val());
            var tax = parseFloat($('#totaltax').val());
            var discount_amount = parseFloat($('#discount_amount').val());
            if (isNaN(discount_amount)) {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax +
                    delivery_charge)));
                $('#grand_total').val((sub_total + tax + delivery_charge).toFixed(2));
            } else {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax +
                    delivery_charge - discount_amount)));
                $('#grand_total').val((sub_total + tax + delivery_charge - discount_amount).toFixed(
                    2));
            }
        } else if (test == 2) {
            $("#open").hide();
            $("#shippinginfodiv").hide();
            $("#delivery").hide();
            $("#pickup").show();
            $("#delivery_date").hide();
            $("#table_show").hide();
            $("#pickup_date").show();
            $("#data_time").show();
            $("#delivery-charge-section").hide();
            var sub_total = parseFloat($('#sub_total').val());
            var tax = parseFloat($('#totaltax').val());
            var discount_amount = parseFloat($('#discount_amount').val());
            if (isNaN(discount_amount)) {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax)));
                $('#grand_total').val((sub_total + tax).toFixed(2));
            } else {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax -
                    discount_amount)));
                $('#grand_total').val((sub_total + tax - discount_amount).toFixed(2));
            }
        } else {
            $("#open").hide();
            $("#shippinginfodiv").hide();
            $("#delivery").hide();
            $("#pickup").hide();
            $("#delivery_date").hide();
            $("#pickup_date").hide();
            $("#table_show").show();
            $("#data_time").hide();
            $("#delivery-charge-section").hide();
            var sub_total = parseFloat($('#sub_total').val());
            var tax = parseFloat($('#totaltax').val());
            var discount_amount = parseFloat($('#discount_amount').val());
            if (isNaN(discount_amount)) {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax)));
                $('#grand_total').val((sub_total + tax).toFixed(2));
            } else {
                $('#grand_total_view').text(currency_formate(parseFloat(sub_total + tax -
                    discount_amount)));
                $('#grand_total').val((sub_total + tax - discount_amount).toFixed(2));
            }
        }
    });
    if ("{{ helper::appdata($storeinfo->id)->delivery_type }}" != "both") {
        $(function () {
            $("input[name$='cart-delivery']:checked").click();
        });
    }
});

function copyToClipboard(element) {
    "use strict";
    $("#couponcode").val(element);
    $("#offcanvasRight").offcanvas("hide");
}

$('#delivery_dt').on('change', function () {
    "use strict";
    $('#delivery_time').empty();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: $('#sloturl').val(),
        type: "post",
        dataType: "json",
        data: {
            inputDate: $(this).val(),
            vendor_id: $("#store_id").val()
        },
        success: function (response) {
            if (response == "1") {
                $('#store_close').removeClass('d-none');
                $('#delivery_time').addClass('d-none');
            } else {
                $('#store_close').addClass('d-none');
                $('#delivery_time').removeClass('d-none');
                $('#delivery_time').append('<option value="">' + select + '</option>');
                for (var i in response) {
                    $('#delivery_time').append('<option value="' + response[i]["slot"] + '">' + response[i]["slot"] + '</option>');
                }

            }
        }, error: function () {
            toastr.error(wrong);
        }
    });
});

if (
    parseFloat(min_order_amount_for_free_shipping) >
    parseFloat($("#sub_total").val())
) {
    $("#shipping_area").on("change", function () {
        "use strict";

        let delivery_charge = parseFloat(
            $(this).find(":selected").attr("data-delivery-charge")
        );

        $(".delivery_charge").html(currency_formate(delivery_charge));
        let sub_total = parseFloat($("#sub_total").val());

        if ($("#discount_amount").val() == "") {
            var offer_amount = parseFloat(0);
        } else {
            var offer_amount = parseFloat($("#discount_amount").val());
        }

        let tax_amount = parseFloat($("#totaltax").val());

        let grand_total = sub_total - offer_amount + tax_amount + delivery_charge;

        $("#delivery_charge").val(delivery_charge);

        $("#grand_total").val(grand_total);

        $("#grand_total_view").text(currency_formate(grand_total));
    });
}

// TO-HIDE-PAYMENT-TYPE-ERROR
$("input:radio[name=payment]").on("click", function (event) {
    "use strict";
    if ($(this).val() == 3) {
        $("#card-element").removeClass("d-none");
    } else {
        $("#card-element").addClass("d-none");
    }
});

setTimeout(function () {
    $('input:radio[name=payment]:checked').on('click', function (event) {
        "use strict";
        if ($(this).val() == 3) {
            $('#card-element').removeClass('d-none');
        } else {
            $('#card-element').addClass('d-none');
        }
    }).click();
}, 2000);

function Order() {
    "use strict";

    var sub_total = parseFloat($('#sub_total').val());
    var tax = $('#tax').val();
    var tax_name = $('#tax_name').val();
    if (
        $("#add_amount").val() != "" &&
        $("#add_amount").val() != undefined
    ) {
        var tips = $("#add_amount").val();
        var grand_total =
            parseFloat($("#grand_total").val()) +
            parseFloat($("#add_amount").val());
    } else {
        var tips = 0;
        var grand_total = $("#grand_total").val();
    }
    var delivery_time = $('#delivery_time').val();
    var delivery_date = $('#delivery_dt').val();
    var delivery_area = $("#shipping_area").find(":selected").attr("data-area-name");
    var delivery_charge = parseFloat($('#delivery_charge').val());
    var discount_amount = parseFloat($('#discount_amount').val());
    var offer_type = $('#offer_type').val();
    var couponcode = $('#coupon_code').val();
    var order_type = $("input:radio[name=cart-delivery]:checked").val();
    var address = $('#address').val();
    var postal_code = $('#postal_code').val();
    var building = $('#building').val();
    var landmark = $('#landmark').val();
    var notes = $('#notes').val();
    var customer_name = $('#customer_name').val();
    var customer_email = $('#customer_email').val();
    var customer_mobile = $('#customer_mobile').val();
    var vendor = $('#vendor').val();
    var payment_type = $('input[name="payment"]:checked').attr("data-payment_type");
    var flutterwavekey = $('#flutterwavekey').val();
    var paystackkey = $('#paystackkey').val();
    var checkplanurl = $('#checkplanurl').val();
    var paymenturl = $('#paymenturl').val();
    var mecadourl = $('#mecadourl').val();
    var paypalurl = $('#paypalurl').val();
    var myfatoorahurl = $('#myfatoorahurl').val();
    var toyyibpayurl = $('#toyyibpayurl').val();
    var phonepeurl = $('#phonepeurl').val();
    var paytaburl = $('#paytaburl').val();
    var mollieurl = $('#mollieurl').val();
    var khaltiurl = $('#khaltiurl').val();
    var xenditurl = $('#xenditurl').val();
    var url = $('#payment_url').val();
    var website_title = $('#website_title').val();
    var image = $('#image').val();
    var slug = $('#slug').val();
    var failure = $('#failure').val();
    var table = $('#table').val();
    if (order_type == "1") {
        if (delivery_date == "") {
            toastr.error($('#delivery_date_required').val());
            return false;
        } else if (delivery_time == "") {
            toastr.error($('#delivery_time_required').val());
            return false;
        } else if (address == "") {
            toastr.error($('#address_required').val());
            return false;
        } else if ($("#shipping_area").find(":selected").val() == "") {
            toastr.error($('#delivery_area_required').val());
            return false;
        }
    } else if (order_type == "2") {
        if (delivery_date == "") {
            toastr.error($('#pickup_date_required').val());
            return false;
        } else if (delivery_time == "") {
            toastr.error($('#pickup_time_required').val());
            return false;
        }
    } else if (order_type == "3") {
        if (table == "") {
            toastr.error($('#table_required').val());
            return false;

        }
    }
    if (customer_name == "") {
        toastr.error($('#customer_name_required').val());
        return false;
    } else if (customer_mobile == "") {
        toastr.error($('#customer_mobile_required').val());
        return false;
    } else if (customer_email == "") {
        toastr.error($('#customer_email_required').val());
        return false;
    }
    $('.checkout').prop("disabled", true);
    $('.checkout').html('<span class="loader"></span>');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: checkplanurl,
        data: {
            vendor_id: vendor,
        },
        method: 'POST',
        success: function (response) {
            if (response.status == 1) {
                //COD + V2 manual/offline methods (Cash, Cash on Pickup, BenefitPay, Bank QR, Payment Link)
                if (payment_type == "1" || payment_type == '16' || payment_type == '17' || payment_type == '18' || payment_type == '19' || payment_type == '20' || payment_type == '21') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: paymenturl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            slug: slug,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }
                //Razorpay
                if (payment_type == "2") {
                    var options = {
                        "key": $('#razorpay').val(),
                        "amount": (parseInt(grand_total * 100)), // 2000 paise = INR 20
                        "name": website_title,
                        "description": "Order payment",
                        "image": "image",
                        "handler": function (response) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                url: paymenturl,
                                type: 'post',
                                data: {
                                    payment_id: response.razorpay_payment_id,
                                    sub_total: sub_total,
                                    tax: tax,
                                    tax_name: tax_name,
                                    grand_total: grand_total,
                                    delivery_time: delivery_time,
                                    delivery_date: delivery_date,
                                    delivery_area: delivery_area,
                                    delivery_charge: delivery_charge,
                                    discount_amount: discount_amount,
                                    offer_type: offer_type,
                                    couponcode: couponcode,
                                    order_type: order_type,
                                    address: address,
                                    postal_code: postal_code,
                                    building: building,
                                    landmark: landmark,
                                    notes: notes,
                                    customer_name: customer_name,
                                    customer_email: customer_email,
                                    customer_mobile: customer_mobile,
                                    vendor_id: vendor,
                                    payment_type: payment_type,
                                    slug: slug,
                                    table: table,
                                    tips: tips,
                                    buynow: $('#buynow_key').val(),
                                },
                                success: function (response) {
                                    if (response.status == 1) {
                                        window.location.href = response.url;
                                    } else {
                                        $('.checkout').prop("disabled", false);
                                        $('.checkout').html('Place Order');
                                        toastr.error(response.message);
                                    }
                                },
                                error: function () {
                                    $('.checkout').prop("disabled", false);
                                    $('.checkout').html('Place Order');
                                    toastr.error(wrong);
                                }
                            });
                        },
                        "prefill": {
                            "contact": customer_mobile,
                            "email": customer_email,
                            "name": customer_name,
                        },
                        "theme": {
                            "color": "#366ed4"
                        },
                        "modal": {
                            "ondismiss": function () {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                            }
                        },
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    e.preventDefault();
                }
                //Stripe
                if (payment_type == "3") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: paymenturl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            slug: slug,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }
                //Flutterwave
                if (payment_type == "4") {
                    FlutterwaveCheckout({
                        public_key: flutterwavekey,
                        tx_ref: customer_name,
                        amount: grand_total,
                        currency: "NGN",
                        payment_options: " ",
                        customer: {
                            email: customer_email,
                            phone_number: customer_mobile,
                            name: customer_name,
                        },
                        callback: function (data) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                url: paymenturl,
                                method: 'POST',
                                data: {
                                    payment_id: data.flw_ref,
                                    sub_total: sub_total,
                                    tax: tax,
                                    tax_name: tax_name,
                                    grand_total: grand_total,
                                    delivery_time: delivery_time,
                                    delivery_date: delivery_date,
                                    delivery_area: delivery_area,
                                    delivery_charge: delivery_charge,
                                    discount_amount: discount_amount,
                                    offer_type: offer_type,
                                    couponcode: couponcode,
                                    order_type: order_type,
                                    address: address,
                                    postal_code: postal_code,
                                    building: building,
                                    landmark: landmark,
                                    notes: notes,
                                    customer_name: customer_name,
                                    customer_email: customer_email,
                                    customer_mobile: customer_mobile,
                                    vendor_id: vendor,
                                    payment_type: payment_type,
                                    slug: slug,
                                    table: table,
                                    tips: tips,
                                    buynow: $('#buynow_key').val(),
                                },
                                success: function (response) {
                                    if (response.status == 1) {
                                        window.location.href = response.url;
                                    } else {
                                        $('.checkout').prop("disabled", false);
                                        $('.checkout').html('Place Order');
                                        toastr.error(response.message);
                                    }
                                },
                                error: function () {
                                    $('.checkout').prop("disabled", false);
                                    $('.checkout').html('Place Order');
                                    toastr.error(wrong);
                                }
                            });
                        },
                        onclose: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                        },
                        customizations: {
                            title: website_title,
                            description: "Order payment",
                            logo: image,
                        },
                    });
                }
                //Paystack
                if (payment_type == "5") {
                    let handler = PaystackPop.setup({
                        key: paystackkey,
                        email: customer_email,
                        amount: grand_total * 100,
                        currency: 'GHS', // Use GHS for Ghana Cedis or USD for US Dollars
                        ref: 'trx_' + Math.floor((Math.random() * 1000000000) + 1),
                        label: "Order payment",
                        onClose: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                        },
                        callback: function (response) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                url: paymenturl,
                                data: {
                                    payment_id: response.trxref,
                                    sub_total: sub_total,
                                    tax: tax,
                                    tax_name: tax_name,
                                    grand_total: grand_total,
                                    delivery_time: delivery_time,
                                    delivery_date: delivery_date,
                                    delivery_area: delivery_area,
                                    delivery_charge: delivery_charge,
                                    discount_amount: discount_amount,
                                    offer_type: offer_type,
                                    couponcode: couponcode,
                                    order_type: order_type,
                                    address: address,
                                    postal_code: postal_code,
                                    building: building,
                                    landmark: landmark,
                                    notes: notes,
                                    customer_name: customer_name,
                                    customer_email: customer_email,
                                    customer_mobile: customer_mobile,
                                    vendor_id: vendor,
                                    payment_type: payment_type,
                                    slug: slug,
                                    table: table,
                                    tips: tips,
                                    buynow: $('#buynow_key').val(),
                                },
                                method: 'POST',
                                success: function (response) {
                                    if (response.status == 1) {
                                        window.location.href = response.url;
                                    } else {
                                        $('.checkout').prop("disabled", false);
                                        $('.checkout').html('Place Order');
                                        toastr.error(response.message);
                                    }
                                },
                                error: function () {
                                    $('.checkout').prop("disabled", false);
                                    $('.checkout').html('Place Order');
                                }
                            });
                        }
                    });
                    handler.openIframe();
                }
                //mercado pago
                if (payment_type == "7") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: mecadourl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);

                        }
                    });
                }
                //PayPal
                if (payment_type == "8") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: paypalurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            $(".callpaypal").trigger("click");
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }
                // myfatoorah
                if (payment_type == '9') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: myfatoorahurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }
                //toyyibpay
                if (payment_type == '10') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: toyyibpayurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                //phonepe
                if (payment_type == '11') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: phonepeurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                //paytab
                if (payment_type == '12') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: paytaburl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                //mollie
                if (payment_type == '13') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: mollieurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                //khalti
                if (payment_type == '14') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: khaltiurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                //xendit
                if (payment_type == '15') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: xenditurl,
                        data: {
                            sub_total: sub_total,
                            tax: tax,
                            tax_name: tax_name,
                            grand_total: grand_total,
                            delivery_time: delivery_time,
                            delivery_date: delivery_date,
                            delivery_area: delivery_area,
                            delivery_charge: delivery_charge,
                            discount_amount: discount_amount,
                            offer_type: offer_type,
                            couponcode: couponcode,
                            order_type: order_type,
                            address: address,
                            postal_code: postal_code,
                            building: building,
                            landmark: landmark,
                            notes: notes,
                            customer_name: customer_name,
                            customer_email: customer_email,
                            customer_mobile: customer_mobile,
                            vendor_id: vendor,
                            payment_type: payment_type,
                            return: '1',
                            slug: slug,
                            url: url,
                            failure: failure,
                            table: table,
                            tips: tips,
                            buynow: $('#buynow_key').val(),
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response.status == 1) {
                                window.location.href = response.url;
                            } else {
                                $('.checkout').prop("disabled", false);
                                $('.checkout').html('Place Order');
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $('.checkout').prop("disabled", false);
                            $('.checkout').html('Place Order');
                            toastr.error(wrong);
                        }
                    });
                }

                // Banktransfer
                if (payment_type == '6') {
                    $('#payment_type').val(payment_type);
                    $('#modal_customer_name').val($('#customer_name').val());
                    $('#modal_customer_email').val($('#customer_email').val());
                    $('#modal_customer_mobile').val($('#customer_mobile').val());
                    $('#modal_address').val(address);
                    $('#modal_delivery_date').val(delivery_date);
                    $('#modal_delivery_time').val(delivery_time);
                    $('#modal_delivery_area').val(delivery_area);
                    $('#modal_delivery_charge').val(delivery_charge);
                    $('#modal_discount_amount').val(discount_amount);
                    $('#modal_couponcode').val(couponcode);
                    $('#modal_ordertype').val(order_type);
                    $('#modal_building').val(building);
                    $('#modal_landmark').val(landmark);
                    $('#modal_postal_code').val(postal_code);
                    $('#modal_message').val(notes);
                    $('#modal_vendor_id').val(vendor);
                    $('#modal_slug').val(slug);
                    $('#modal_subtotal').val(sub_total);
                    $('#modal_grand_total').val(grand_total);
                    $('#modal_tax').val(tax);
                    $('#modal_tax_name').val(tax_name);
                    $('#modal_order_type').val(order_type);
                    $('#modal_offer_type').val(offer_type);
                    $('#modal_table').val(table);
                    $('#modal_tips').val(tips);
                    $('#modal_buynow').val($('#buynow_key').val());
                    $('#payment_description').html($('#bank_payment').val());
                    $('#modalbankdetails').modal('show');
                    $("#modalbankdetails").on('hidden.bs.modal', function (e) {
                        $('.checkout').prop("disabled", false);
                        $('.checkout').html('Place Order');
                    });
                }
            } else {
                $('.checkout').prop("disabled", false);
                $('.checkout').html('Place Order');
                toastr.error(response.message);
            }
        },
        error: function () {
            $('.checkout').prop("disabled", false);
            $('.checkout').html('Place Order');
            toastr.error(wrong);
        }
    });
}
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test($email);
}

function RedeemPoints(vendor_id) {
    "use strict";
    $('#btnredeempoint').prop("disabled", true);
    $('#btnredeempoint').html(
        '<span class="loader"></span>');
    var points = $('#points').val();
    var sub_total = parseFloat($('#sub_total').val());
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: $('#applyredeempoints').val(),
        method: 'post',
        data: {
            vendor_id: vendor_id,
            points: points,
            sub_total: sub_total,
        },
        success: function (response) {
            if (response.status == 1) {
                var grand_total = parseFloat($('#grand_total').val()) - response.data.price;
                $('#promocodesection').addClass('d-none');
                $('#discount_1').removeClass('d-none');
                $('#grand_total_view').html(currency_formate(grand_total));
                $('#grand_total').val(grand_total);
                $('#offer_amount').text('- ' + currency_formate(parseFloat(response.data.price)));
                $('#discount_amount').val(response.data.price);
                $('#offer_type').val(response.data.offer_type);
                $('#coupon_code').val(response.data.points);
                $("#points").prop("readonly", true);
                $('#couponcode').val('');
                $('#btnremovepoint').removeClass('d-none');
                $('#btnredeempoint').addClass('d-none');
                $('#btnredeempoint').prop("disabled", false);
                $('#btnredeempoint').html('Redeem');
            } else {
                $('#offcanvasRight').offcanvas('hide');
                $('#btnredeempoint').prop("disabled", false);
                $('#btnredeempoint').html('Redeem');
                toastr.error(response.message);
            }
        }, error: function () {
            $('#btnredeempoint').prop("disabled", false);
            $('#btnredeempoint').html('Redeem');
            toastr.error(wrong);
        }
    });
}

function RemovePoints() {
    "use strict";
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success mx-1 yes-btn',
            cancelButton: 'btn btn-danger mx-1 no-btn'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: are_you_sure,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: yes,
        cancelButtonText: no,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $('#btnremovepoint').prop("disabled", true);
            $('#btnremovepoint').html(
                '<span class="loader"></span>');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: $('#removeredeempoints').val(),
                method: 'post',
                success: function (response) {
                    if (response.status == 1) {
                        var grand_total = (parseFloat($('#grand_total').val()) + parseFloat($('#discount_amount').val()));
                        $('#promocodesection').removeClass('d-none');
                        $('#discount_1').addClass('d-none');
                        $('#discount_amount').val('');
                        $('#offer_type').val('');
                        $('#coupon_code').val('');
                        $('#grand_total_view').html(currency_formate(grand_total));
                        $('#grand_total').val(grand_total);
                        $("#points").prop("readonly", false);
                        $('#points').val('');
                        $('#couponcode').val('');
                        $('#btnremovepoint').addClass('d-none');
                        $('#btnredeempoint').removeClass('d-none');
                        $('#btnremovepoint').prop("disabled", false);
                        $('#btnremovepoint').html('Remove');
                    } else {
                        $('#btnremovepoint').prop("disabled", false);
                        $('#btnremovepoint').html('Remove');
                    }
                }, error: function () {
                    $('#btnremovepoint').prop("disabled", false);
                    $('#btnremovepoint').html('Remove');
                    toastr.error(wrong);
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            Swal.DismissReason.cancel
        }
    })
}
