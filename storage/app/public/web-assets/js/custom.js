$(window).on("load", function () {
    "use strict";
    $('#preloader').fadeOut('slow')
    if ($(".multimenu").find(".active")) {
        $(".multimenu").find(".active").parent().parent().addClass("show");
        $(".multimenu").find(".active").parent().parent().parent().attr("aria-expanded", true);
    }
});

$(window).scroll(function () {
    "use strict";
    if ($(this).scrollTop() > 80) {
        $('#header1').addClass('fixed-top');
    } else {
        $('#header1').removeClass('fixed-top');
    }
});
$(window).on("scroll", function () {
    "use strict";
    if ($(window).scrollTop() > 150) {
        if ($(window).width() > 768) {
            $(".view-cart-bar").removeClass("d-none");
        } else {
            $(".view-cart-bar").addClass("d-none");
        }
    } else {
        $(".view-cart-bar").addClass("d-none");
    }
});


$(document).ready(function () {
    "use strict";
    $('.zero-configuration').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5'
        ]
    });
});

// # Sweetalert2
$(document).on('click', '#sweetalert', function (e) {
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
            swalWithBootstrapButtons.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
});



function managefavorite(vendor_id, slug, type, manageurl, url) {
    "use strict";
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: manageurl,
        data: {
            slug: slug,
            type: type,
            favurl: manageurl,
            vendor_id: vendor_id,
            url: url
        },
        method: 'POST',
        success: function (response) {
            if (window.location.href.includes('details') || window.location.href.includes('favorites')) {
                location.reload();
            } else {
                $('.set-fav1-' + slug).html(response.data);
                $('#additems').modal('hide');
            }
        },
        error: function (e) {
            return false;
        }
    });
}


$('.category-slider-theme-10').on('click', '.owl-item', function () {
    $(".specs").hide().eq($(this).index()).show();
    $('.category-box').removeClass('active1').eq($(this).index()).addClass("active1");
    $('.owl-item').removeClass('active1');
})

$(".navgation_lower li").click(function () {
    $(".specs").hide().eq($(this).index()).show();
    $(".navgation_lower li ").removeClass("active1").eq($(this).index()).addClass("active1");
    $(".category-card li").removeClass("active1").eq($(this).index()).addClass("active1");
});

$(".mobile-menu-active li").click(function () {
    $(".mobile-menu-active li a").removeClass("active").eq($(this).index()).addClass("active");
});

function statusupdate(nexturl) {
    "use strict";
    manegedata(nexturl);
}
function manegedata(nexturl) {
    "use strict";
    if (env == 'sandbox') {
        if (!nexturl.includes('orders') && !nexturl.includes('logout')) {
            myFunction();
            return false;
        }
    }
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
            $('#preloader').show();
            location.href = nexturl;
        } else {
            result.dismiss === Swal.DismissReason.cancel
        }
    })
}
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    $("#foo").trigger("click");
    deferredPrompt = e;
});

if (window.matchMedia('(display-mode: standalone)').matches) {
    // If the app is installed, hide the install button or popup
    $('.pwa').addClass('d-none');
} else {
    const mobile_install_app = document.getElementById('mobile-install-app');
    if (mobile_install_app != null) {
        mobile_install_app.addEventListener('click', async () => {
            if (deferredPrompt !== null) {
                deferredPrompt.prompt();
                const {
                    outcome
                } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    deferredPrompt = null;

                }
            }
        });
    }
}
$(document).ready(function () {
    window.addEventListener('beforeinstallprompt', (e) => {
        $('.install-app-btn-container').show();
        deferredPrompt = e;
    });
});

$('#close-btn').click(function () {
    $('.pwa').addClass('d-none');
});

$(document).ready(function () {
    // Function to add blur class to wrapper when modal has 'show' class
    function addBlurOnModalShow() {
        if ($('.modal').hasClass('show')) {
            $('#main-content').addClass('blurred');
        }
    }
    // Call the function on document ready
    addBlurOnModalShow();
    // Event listener for modal visibility changes
    $('.modal').on('shown.bs.modal', function () {
        $('#main-content').addClass('blurred');
    });
    $('.modal').on('hidden.bs.modal', function () {
        $('#main-content').removeClass('blurred');
    });
});

$('#column').on('click', function () {
    "use strict";
    $('#column-view').addClass('d-none');
    $('#column').addClass('service-active');
    $('.listing-view').removeClass('d-none');
    $('#grid').removeClass('service-active');
});
$('#grid').on('click', function () {
    "use strict";
    $('#column-view').removeClass('d-none');
    $('#column').removeClass('service-active');
    $('.listing-view').addClass('d-none');
    $('#grid').addClass('service-active');
});


$('#close-btn-view').click(function () {
    $('.view-cart-bar-2').addClass('d-none');
});

function setLightMode() {
  document.documentElement.classList.remove('dark');
  document.documentElement.classList.add('light');
  localStorage.setItem('theme', 'light');
  $('#logoimage').attr('src', lightlogo);
  $('#logoimage2').attr('src', lightlogo);
  $('#footerlogoimage').attr('src', lightlogo);
}

function setDarkMode() {
  document.documentElement.classList.remove('light');
  document.documentElement.classList.add('dark');
  localStorage.setItem('theme', 'dark');
  $('#logoimage').attr('src', darklogo);
  $('#logoimage2').attr('src', darklogo);
  $('#footerlogoimage').attr('src', darklogo);
}