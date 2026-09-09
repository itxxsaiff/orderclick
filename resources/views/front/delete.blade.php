@extends('front.theme.default')
@section('content')
    <!------ breadcrumb ------>
    <section class="breadcrumb-sec">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug . '/') }}" class="text-dark">
                            {{ trans('labels.home') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.delete_profile') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <section class="bg-light py-sm-5 py-4">
        <div class="container">
            <div class="row gx-sm-3 gx-2">
                @include('front.theme.user_sidebar')
                <div class="col-lg-9 col-md-12">
                    <div class="card rounded">
                        <!-- Card body START -->
                        <div class="card-body py-4">
                            <h2 class="page-title mb-2">{{ trans('labels.delete_profile') }}</h2>
                            <p class="page-subtitle line-limit-2 mb-3">Lorem ipsum dolor sit, amet consectetur
                                adipisicing elit. Adipisci blanditiis dolore, doloremque, asperiores repellendus cumque,
                                ea qui enim nemo deserunt nam suscipit eveniet non voluptatem accusantium. Accusamus
                                fuga enim illo!</p>
                            <h6 class="fw-bold text-dark mb-2">Before you go...</h6>
                            <ul class="p-0">
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle fs-10 text-dark"></i>
                                    <p class="m-0">
                                        Take a backup of your data <a href="#">Here</a>
                                    </p>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle fs-10 text-dark"></i>
                                    <p class="m-0">
                                        If you delete your account, you will lose your all data.
                                    </p>
                                </li>
                            </ul>
                            <div class="form-check p-0 gap-2 d-flex align-items-center form-check-md my-4">
                                <input class="form-check-input m-0 p-0" type="checkbox" id="delete_account">
                                <label class="form-check-label m-0 p-0" for="delete_account">Yes, I'd like to
                                    delete my
                                    account</label>
                            </div>
                            <div class="d-md-flex align-items-center">
                                <a href="#"
                                    class="col-12 col-md-4 col-xl-3 btn btn-primary px-sm-4 px-3 py-2 fs-15 fw-500 mb-3 mb-md-0 me-2">Keep
                                    my
                                    account</a>
                                <a onclick="deleteaccount('{{ URL::to($storeinfo->slug . '/deleteaccount/') }}')"
                                    class="col-12 col-md-4 col-xl-3 btn btn-danger px-sm-4 px-3 py-2 fs-15 fw-500">
                                    {{ trans('labels.delete_profile') }}
                                </a>
                            </div>
                        </div>
                        <!-- Card body END -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('front.sum_qusction')
@endsection
@section('script')
    <script>
        var requiredmsg = "{{ trans('messages.checkbox_delete_account') }}";

        function deleteaccount(nexturl) {
            var deleted = document.getElementById("delete_account").checked;
            if (deleted == true) {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success mx-1',
                        cancelButton: 'btn btn-danger mx-1'
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
            } else {
                toastr.error(requiredmsg);
            }
        }
    </script>
@endsection
