function cleardata() {
  $('#additems').modal('hide');
  $('#item_id').val('');
  $('#item_name').val('');
  $('#item_price').val('');
  $('#item_tax').val('');
  $('#item_image').val('');
  $('#orignal_price').val('');
  $('#qty').val('');
  $('#extras').html('');
  $('#variants').html('');
  $('#viewitem_name').html('');
  $('#viewitem_price').html('');
}

function categories_filter(cat_id, nexturl) {
  $('.scroll-list').hasClass('active');
  $('.active').removeClass('active');
  $('#search-keyword').val('');

  if (cat_id == '') {
    $("#cat").addClass('active');
  }
  $("#cat-" + cat_id).addClass('active');
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: nexturl,
    method: "get",
    data: {
      id: cat_id
    },
    success: function (data) {
      $("#pos-item").html('');
      $("#cat_id").val();
      $("#pos-item").html(data);
    },
    error: function (data) {
      toastr.error(wrong);
      return false;
    }
  });
}

$('#search-keyword').keyup(function () {
  "use strict";

  var cat_id = $('#cat_id').val();
  var keyword = $('#search-keyword').val();
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: $('#search-url').val(),
    method: "get",
    data: {
      id: cat_id,
      keyword: keyword
    },
    success: function (data) {
      $("#pos-item").html('');
      $("#cat_id").val();
      $("#pos-item").html(data);
    },
    error: function (data) {
      toastr.error(wrong);
      return false;
    }
  });
});

function posaddtocart() {
  var item_id = $('#overview_item_id').val();
  var item_name = $('#overview_item_name').val();
  var item_image = $('#overview_item_image').val();
  var item_price = $('#overview_item_price').val();
  var item_original_price = $('#overview_item_original_price').val();
  var tax = $('#tax_val').val();
  var variants_name = $('#variants_name').val();
  var item_qty = $('#item_qty').val();
  var min_order = $('#item_min_order').val();
  var max_order = $('#item_max_order').val();
  var tax = $('#item_tax').val();
  var stock_management = $('#stock_management').val();
  var extras_id = ($('.Checkbox:checked').map(function () {
    return this.value;
  }).get().join('| '));
  var extras_name = ($('.Checkbox:checked').map(function () {
    return $(this).attr('extras_name');
  }).get().join('| '));
  var extras_price = ($('.Checkbox:checked').map(function () {
    return $(this).attr('price');
  }).get().join('| '));
  $('.add_to_cartbtn-' + item_id).html('<span class="loader"></span>');
  $('.add_to_cartbtn-' + item_id).prop('disabled', true);

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    },
    url: $("#addtocarturl").val(),
    data: {
      item_id: item_id,
      name: item_name,
      image: item_image,
      item_price: item_price,
      price: item_original_price,
      variants_name: variants_name,
      tax: tax,
      extras_id: extras_id,
      extras_name: extras_name,
      extras_price: extras_price,
      qty: item_qty,
      item_min_order: min_order,
      item_max_order: max_order,
      stock_management: stock_management,
      order_number: $('#order_number').val(),
    },
    method: "POST", //Post method,
    success: function (response) {
      if (response.status == 0) {
        toastr.error(response.message);
        $('.add_to_cartbtn-' + item_id).html('Add to Cart');
        $('.add_to_cartbtn-' + item_id).prop('disabled', false);
      } else {
        $("#variant_id").val("");
        $("#variants_name").val("");
        $(".addactive-" + item_id).addClass("active");
        $("#additems").modal("hide");
        $("#cartview").html("");
        $("#cartview").html(response);
        $('.add_to_cartbtn-' + item_id).html('Add to Cart');
        $('.add_to_cartbtn-' + item_id).prop('disabled', false);
        toastr.success("Add Success");
        cleardata();
      }
    },
    error: function () {
      $('.add_to_cartbtn-' + item_id).html('Add to Cart');
      $('.add_to_cartbtn-' + item_id).prop('disabled', false);
      toastr.error(wrong);
    }
  });
}


function showitems(id) {
  "use strict";
  $('.showload-' + id).show();
  $('.addcartbtn-' + id).hide();
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: $('#showitemurl').val(),
    method: "post",
    data: {
      id: id,
    },
    success: function (response) {
      $('.showload-' + id).hide();
      $('.addcartbtn-' + id).show();
      $('#viewproduct_body').html(response.output);
      $('#additems').modal('show');
    },
    error: function () {
      $('.showload-' + id).hide();
      $('.addcartbtn-' + id).show();
      toastr.error(wrong);
      return false;
    }
  });
}


function RemoveCart(cart_id) {
  "use strict";
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success mx-1',
      cancelButton: 'btn btn-danger bg-danger mx-1'
    },
    buttonsStyling: false
  })
  swalWithBootstrapButtons.fire({
    icon: 'warning',
    title: title,
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: false,
    confirmButtonText: yes,
    cancelButtonText: no,
    reverseButtons: true,
    showLoaderOnConfirm: true,
    preConfirm: function () {
      return new Promise(function (resolve, reject) {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: $('#deletecarturl').val(),
          data: {
            cart_id: cart_id
          },
          method: 'POST',
          success: function (response) {
            if (response.status == 1) {
              location.reload();
            } else {
              swal("Cancelled", "{{ trans('messages.wrong') }} :(",
                "error");
            }
          },
          error: function (e) {
            swal("Cancelled", "{{ trans('messages.wrong') }} :(",
              "error");
          }
        });
      });
    },
  }).then((result) => {
    if (!result.isConfirmed) {
      result.dismiss === Swal.DismissReason.cancel
    }
  })
}

//Order now modal open
function OrderNow(ordernowurl) {
  "use strict";

  if ($('#rightsidebar').hasClass('show')) {
    var customerid = $('#customer1').val();
  } else {
    var customerid = $('#customer').val();
  }
  var sub_total = $('#sub_total').val();

  var discount_amount = $('#discount_amount').val();
  var grand_total = $('#grand_total').val();
  $('.pos_order_now').html('<span class="loader"></span>');
  $('.pos_order_now').prop('disabled', true);
  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    },
    data: {
      customerid: customerid,
    },
    url: ordernowurl,
    method: "POST",
    success: function (response) {
      if (response.status === 1) {
        $('#OrderNowContainer').html(response.output);
        $('#orderButton').modal('show');
        if (discount_amount == '' || discount_amount == 0) {
          $('.orderdiscount_amount').addClass('d-none');
        } else {
          $('#orderdiscount_amount').text(currency_formate(discount_amount));
        }
        $('#ordersub_total').text(currency_formate(sub_total));
        $('#ordergrand_total').text(currency_formate(grand_total));
        $("#orderButton").on('hidden.bs.modal', function (e) {
          $('.pos_order_now').html('Place Order');
          $('.pos_order_now').prop('disabled', false);
        });

      } else {
        $('.pos_order_now').html('Place Order');
        $('.pos_order_now').prop('disabled', false);
        toastr.error(wrong);
        return false;
      }
    }, error: function () {
      $('.pos_order_now').html('Place Order');
      $('.pos_order_now').prop('disabled', false);
      toastr.error(wrong);
      return false;
    }
  });
}


function placeorder() {
  var discount_amount = $("#discount_amount").val();
  if ($('#rightsidebar').hasClass('show')) {
    var customer = $('#customer1').val();
  } else {
    var customer = $('#customer').val();
  }
  var payment_type = $('input[name="payment_type"]:checked').val();
  var sub_total = $("#sub_total").val();
  var tax = $("#tax_data").val();
  var tax_name = $("#tax_name").val();
  var grand_total = $("#grand_total").val();
  var order_notes = $("#cart_order_note").val();

  var customer_name = $("#customer_name").val();
  var customer_email = $("#customer_email").val();
  var customer_phone = $("#customer_phone").val();
  // Reset previous error messages
  $('#customer_name_required').text("");
  $('#customer_email_required').text("");
  $('#customer_phone_required').text("");
  $('#payment_type_required').text("");

  // Validate customer details
  var valid = true; // Flag to check if all validations pass

  if (customer_name === '') {
    $('#customer_name_required').text("Please enter your name.");
    valid = false;
  }
  if (customer_email === '') {
    $('#customer_email_required').text("Please enter your email address.");
    valid = false;
  }
  if (customer_phone === '') {
    $('#customer_phone_required').text("Please enter your phone number.");
    valid = false;
  }

  var payment_type = $('input[name="payment_type"]:checked').val();
  if (!payment_type) {
    $('#payment_type_required').text("Please select a payment type.");
    valid = false;
  }

  if (!valid) {
    $('#orderButton').modal('show');
    return;
  }
  $('.pos_order').html('<span class="loader"></span>');
  $('.pos_order').prop('disabled', true);
  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    },
    url: $("#orderurl").val(),
    data: {
      discount_amount: discount_amount,
      customer: customer,
      customer_name: customer_name,
      customer_email: customer_email,
      customer_mobile: customer_phone,
      payment_type: payment_type,
      sub_total: sub_total,
      tax: tax,
      tax_name: tax_name,
      grand_total: grand_total,
      order_notes: order_notes
    },
    method: "POST",
    success: function (response) {
      $("#cartview").html("");
      $('#orderButton').modal('hide');
      $('#order_id').attr('href', response.url);
      $("#pos-invoice").modal("show");
      $("#pos-invoice").on('hidden.bs.modal', function (e) {
        location.reload();
      });
    },
    error: function (e) {
      $('.pos_order').html('Confirm');
      $('.pos_order').prop('disabled', false);
      toastr.error(wrong);
      return false;
    }
  });
}