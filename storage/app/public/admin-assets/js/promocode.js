$('.type').on('change', function () {
    "use strict";
    if ($('.type').val() == '1') {
        $('#usage_limit_input').show();
        $('#usage_limit_input input').prop('required', true);
    } else {
        $('#usage_limit_input').hide();
        $('#usage_limit_input input').prop('required', false);
    }
}).change();
$('#start_date').on('change', function () {
    "use strict";
    if (new Date($('#start_date').val()) > new Date($('#end_date').val())) {
        $('#start_date').val('');
        toastr.error('start date must be less then end date !!');
    }

});
$('#end_date').on('change', function () {
    "use strict";
    if (new Date($('#start_date').val()) > new Date($('#end_date').val())) {
        $('#end_date').val('');
        toastr.error('start date must be less then end date !!');
    }
});