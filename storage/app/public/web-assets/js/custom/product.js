function showitems(id, item_name, item_price) {
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
            vendor_id: vendor_id,
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
function showreviews(id) {
    "use strict";
    $.ajax({
        url: $('#showreviewsurl').val(),
        method: "GET",
        data: {
            item_id: id,
            vendor_id: vendor_id,
        },
        success: function (response) {
            $('#viewreviewsbody').html(response.output);
            $('#offcanvasExample').offcanvas('show');
        },
        error: function () {
            toastr.error(wrong);
            return false;
        }
    });
}

function postreview(item_id, item_name) {
    "use strict";
    $('#item_id').val(item_id);
    $('#product_name').html(item_name);
    $('#addreview').modal('show');
}

$('#product_ratting').submit(function (event) {
    if (!$("input[name='star']:checked").val()) {
        event.preventDefault();
        toastr.error("Please select a ratting.");
    }
});