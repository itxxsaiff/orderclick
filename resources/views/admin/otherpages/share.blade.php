@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.share') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $isMob = is_numeric(strpos(strtolower(@$_SERVER['HTTP_USER_AGENT']), 'mobile'));
        if (helper::checkcustomdomain($vendor_id) == null) {
            $url = URL::to($user->slug);
        } else {
            $url = 'https://' . helper::checkcustomdomain($vendor_id);
        }
        $qr = 'https://qrcode.tec-it.com/API/QRCode?data=' . urlencode($url) . '&choe=UTF-8';
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <div class="row g-4 mb-7">
        <div class="col-lg-5 col-12">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body text-center">
                    <h6 class="mb-3 fw-600">{{ $isAr ? 'رمز QR للمتجر' : 'Store QR Code' }}</h6>
                    <img src="{{ $qr }}" width="220" height="220" class="rounded border p-2" alt="Store QR">
                    <p class="text-muted fs-7 mt-2 mb-3">{{ $isAr ? 'يمسحه العميل ليفتح متجرك مباشرة' : 'Customers scan this to open your store' }}</p>
                    <a href="{{ $qr }}" target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-down-to-line me-1"></i>{{ trans('labels.download') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-12">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <h6 class="mb-2 fw-600">{{ $isAr ? 'رابط متجرك' : 'Your Store Link' }}</h6>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="ocStoreLink" value="{{ $url }}" readonly>
                        <button class="btn btn-primary" type="button" onclick="ocCopyLink()">
                            <i class="fa-regular fa-copy me-1"></i><span id="ocCopyTxt">{{ $isAr ? 'نسخ' : 'Copy' }}</span>
                        </button>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-up-right-from-square me-1"></i>{{ $isAr ? 'فتح المتجر' : 'Open store' }}
                        </a>
                        <button type="button" class="btn btn-success btn-sm" onclick="ocShare()">
                            <i class="fa-solid fa-share-nodes me-1"></i>{{ $isAr ? 'مشاركة' : 'Share' }}
                        </button>
                    </div>

                    <h6 class="mb-3 fw-600">{{ $isAr ? 'شارك على' : 'Share on' }}</h6>
                    <div class="row g-2">
                        <div class="col-sm-6 col-12">
                            <a href="{{ $isMob == '1' ? 'whatsapp://send/send?text=' . urlencode($url) : 'https://web.whatsapp.com/send?text=' . urlencode($url) }}"
                                target="_blank" class="btn btn-social whatsappcolor w-100">
                                <i class="fa-brands fa-whatsapp text-wa-color"></i> {{ trans('labels.whatsapp') }}
                            </a>
                        </div>
                        <div class="col-sm-6 col-12">
                            <a href="https://www.facebook.com/sharer.php?u={{ urlencode($url) }}" target="_blank"
                                class="btn btn-social facebookcolor w-100">
                                <i class="fa-brands fa-facebook text-fb-color"></i> {{ trans('labels.facebook') }}
                            </a>
                        </div>
                        <div class="col-sm-6 col-12">
                            <a href="http://twitter.com/share?text={{ urlencode($user->name) }}&url={{ urlencode($url) }}"
                                target="_blank" class="btn btn-social twittercolor w-100">
                                <i class="fa-brands fa-twitter text-tw-color"></i> {{ trans('labels.twitter') }}
                            </a>
                        </div>
                        <div class="col-sm-6 col-12">
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($url) }}" target="_blank"
                                class="btn btn-social linkedincolor w-100">
                                <i class="fa-brands fa-linkedin text-ld-color"></i> {{ trans('labels.linkedin') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ocCopyLink() {
            var i = document.getElementById('ocStoreLink');
            i.select();
            i.setSelectionRange(0, 99999);
            var done = function () {
                var t = document.getElementById('ocCopyTxt');
                t.textContent = '{{ $isAr ? 'تم النسخ ✓' : 'Copied ✓' }}';
                setTimeout(function () { t.textContent = '{{ $isAr ? 'نسخ' : 'Copy' }}'; }, 2000);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(i.value).then(done).catch(function () { document.execCommand('copy'); done(); });
            } else {
                document.execCommand('copy');
                done();
            }
        }

        function ocShare() {
            var url = document.getElementById('ocStoreLink').value;
            if (navigator.share) {
                navigator.share({ title: @json($user->name), text: @json($user->name), url: url });
            } else {
                ocCopyLink();
            }
        }
    </script>
@endsection
