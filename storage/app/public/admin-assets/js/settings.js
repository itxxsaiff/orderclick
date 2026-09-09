$(document).ready(function () {
    $('#settingmenuContent').find('.hidechild').addClass('d-none');

    $('#settingmenuContent').find('.hidechild:first').removeClass('d-none');
});


$('.basicinfo').on('click', function () {

    "use strict";

    $('#settingmenuContent').find('.hidechild').addClass('d-none');
    $('#' + $(this).attr('data_attribute')).removeClass('d-none');

    $('.list-options').find('.active').removeClass('active');

    $(this).addClass('active');

});

$(document).ready(function () {
    $('#templatemenuContent').find('.templatehidechild').addClass('d-none');

    $('#templatemenuContent :first-child').removeClass('d-none');
});

$('#template_type').on('change', function () {
    $('#templatemenuContent').find('.templatehidechild').addClass('d-none');
    $('#templatemenuContent').find('textarea').prop('required', false);
    $('#' + $(this).find(':selected').data('attribute')).removeClass('d-none');
    $('#' + $(this).find(':selected').data('attribute')).find('textarea').prop('required', true);
}).change();

//Safe & Secure Checkout
$('.payment-checkbox').on('change', function () {
    var checkedCount = $('.payment-checkbox:checked').length;

    // If 4 checkboxes are already selected, disable others
    if (checkedCount >= 6) {
        $('.payment-checkbox').each(function () {
            if (!$(this).is(':checked')) {
                $(this).prop('disabled', true); // Disable unchecked checkboxes
            }
        });
    } else {
        $('.payment-checkbox').prop('disabled', false); // Enable all checkboxes
    }
}).change();

function show_feature_icon(x) {

    "use strict";

    $(x).next().html($(x).val())

}

var id = 1;

function add_features(icon, title, description) {
    if (layout == 2) {
        var class1 = 'rounded-start-0 rounded-end-5';
        var class2 = 'rounded-start-5 rounded-end-0 border-end-0';
    } else {
        var class1 = 'rounded-start-5 rounded-end-0';
        var class2 = 'rounded-start-0 rounded-end-5';

    }
    "use strict";

    var html = '<div class="col-12 remove' + id + '"><div class="row"><div class="col-md-4 form-group"><div class="input-group"><input type="text" class="form-control feature_icon mb-0 ' + class1 + '" onkeyup="show_feature_icon(this)" name="feature_icon[]" placeholder="' + icon + '" required><p class="input-group-text ' + class2 + '"></p></div></div><div class="col-md-4 form-group"><input type="text" class="form-control" name="feature_title[]" placeholder="' + title + '" required></div><div class="col-md-4 gap-2 d-flex form-group"><input type="text" class="form-control" name="feature_description[]" placeholder="' + description + '" required><button class="btn btn-danger btn-sm rounded-5 hov pricebtn" type="button" onclick="remove_features(' + id + ')"><i class="fa fa-trash"></i></button></div></div></div>';

    $('.extra_footer_features').append(html);

    $(".feature_required").prop('required', true);

    id++;

}

function remove_features(id) {

    "use strict";

    $('.remove' + id).remove();

    if ($('.extra_footer_features .row').length == 0) {

        $(".feature_required").prop('required', false);

    }

}

var id = 1;
function add_social_links(icon, link) {
    "use strict";
    var html =
        '<div class="col-12 remove' +
        id +
        '"><div class="row"><div class="col-md-6 form-group"><div class="input-group"><input type="text" class="form-control feature_icon" onkeyup="show_feature_icon(this)" name="social_icon[]" placeholder="' +
        icon +
        '" required><p class="input-group-text"></p></div></div><div class="col-md-6 d-flex gap-2 align-items-center form-group"><input type="text" class="form-control" name="social_link[]" placeholder="' +
        link +
        '" required><button class="btn btn-danger hov btn-sm rounded-5" type="button" onclick="remove_features(' +
        id +
        ')"><i class="fa fa-trash"></i></button></div></div></div>';
    $(".extra_social_links").append(html);
    $(".soaciallink_required").prop("required", true);
    id++;
}

var id = 1;
function add_fun_fact(icon, link) {
    "use strict";
    var html =
        '<div class="col-12 remove' +
        id +
        '"><div class="row"><div class="col-md-6 form-group"><div class="input-group"><input type="text" class="form-control feature_icon" name="fun_fact_title[]" placeholder="' +
        icon +
        '" required></div></div><div class="col-md-6 d-flex gap-2 align-items-center form-group"><input type="text" class="form-control" name="fun_fact_description[]" placeholder="' +
        link +
        '" required><button class="btn btn-danger hov btn-sm rounded-5" type="button" onclick="remove_features(' +
        id +
        ')"><i class="fa fa-trash"></i></button></div></div></div>';
    $(".extra_fun_fact").append(html);
    $(".soaciallink_required").prop("required", true);
    id++;
}

$("#checkout_login_required-switch").on("change", function (e) {
    if (this.checked) {
        $("#is_checkout_login_required").removeClass("d-none");

    } else {
        $("#is_checkout_login_required").addClass("d-none");

    }
}).change();