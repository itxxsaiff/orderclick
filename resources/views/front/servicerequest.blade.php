@extends('front.theme.default')
@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp
    <style>
        .ocb-wrap { background: #f6f8f5; padding: 40px 0 64px; }
        .ocb-card { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid #e7ece4; border-radius: 20px;
            padding: 34px 32px; box-shadow: 0 30px 64px -42px rgba(20,40,28,.4); }
        .ocb-card h1 { font-size: 26px; font-weight: 800; color: #17201a; margin: 0 0 6px; }
        .ocb-card .ocb-sub { color: #6a756c; margin: 0 0 24px; font-size: 15px; }
        .ocb-field { margin-bottom: 16px; }
        .ocb-field label { display: block; font-weight: 600; font-size: 13.5px; color: #39443c; margin-bottom: 6px; }
        .ocb-field label .req { color: #d64545; }
        .ocb-field .ocb-in { width: 100%; height: 48px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px; font-size: 15px; color: #17201a; background: #fff; box-sizing: border-box; }
        .ocb-field textarea.ocb-in { height: auto; padding: 12px 14px; }
        .ocb-field .ocb-in:focus { outline: none; border-color: var(--bs-primary); box-shadow: 0 0 0 3px rgba(0,0,0,.06); }
        .ocb-grid2 { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 14px; }
        .ocb-submit { width: 100%; height: 52px; border: 0; border-radius: 12px; background: var(--bs-primary); color: #fff; font-weight: 650; font-size: 16px; cursor: pointer; margin-top: 8px; }
        .ocb-submit:hover { filter: brightness(.93); }
        @media (max-width: 560px) { .ocb-grid2 { grid-template-columns: 1fr; } }
    </style>

    <div class="ocb-wrap">
        <div class="container">
            <div class="ocb-card">
                <h1>{{ $isAr ? 'اطلب خدمة' : 'Request a Service' }}</h1>
                <p class="ocb-sub">{{ $isAr ? 'أخبرنا بما تحتاجه وسنتواصل معك.' : 'Tell us what you need and we\'ll get back to you.' }}</p>

                <form action="{{ URL::to(@$storeinfo->slug . '/save-service') }}" method="POST">
                    @csrf
                    <div class="ocb-field">
                        <label>{{ $isAr ? 'الخدمة' : 'Service' }} <span class="req">*</span></label>
                        @if (count($services) > 0)
                            <select name="service_name" id="ocb_service" class="ocb-in" required onchange="ocbService(this)">
                                <option value="">{{ $isAr ? 'اختر خدمة' : 'Select a service' }}</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->item_name }}" data-id="{{ $s->id }}">{{ $s->item_name }}@if ($s->item_price > 0) — {{ helper::currency_formate($s->item_price, $vdata) }} @endif</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="service_id" id="ocb_service_id">
                        @else
                            <input type="text" name="service_name" class="ocb-in" required placeholder="{{ $isAr ? 'مثال: تنظيف، صيانة، سباكة' : 'e.g. Cleaning, Maintenance, Plumbing' }}">
                        @endif
                    </div>

                    <div class="ocb-field">
                        <label>{{ $isAr ? 'العنوان / الموقع' : 'Address / Location' }} <span class="req">*</span></label>
                        <input type="text" name="address" class="ocb-in" required placeholder="{{ $isAr ? 'أين تحتاج الخدمة؟' : 'Where do you need the service?' }}">
                    </div>

                    <div class="ocb-grid2">
                        <div class="ocb-field">
                            <label>{{ $isAr ? 'التاريخ المفضل' : 'Preferred Date' }}</label>
                            <input type="date" name="preferred_date" class="ocb-in" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="ocb-field">
                            <label>{{ $isAr ? 'الوقت المفضل' : 'Preferred Time' }}</label>
                            <input type="time" name="preferred_time" class="ocb-in">
                        </div>
                    </div>

                    <div class="ocb-grid2">
                        <div class="ocb-field">
                            <label>{{ $isAr ? 'الاسم' : 'Your Name' }} <span class="req">*</span></label>
                            <input type="text" name="customer_name" class="ocb-in" required>
                        </div>
                        <div class="ocb-field">
                            <label>{{ $isAr ? 'رقم الجوال' : 'Mobile' }} <span class="req">*</span></label>
                            <input type="text" name="mobile" class="ocb-in" required>
                        </div>
                    </div>

                    <div class="ocb-field">
                        <label>{{ $isAr ? 'البريد الإلكتروني (اختياري)' : 'Email (optional)' }}</label>
                        <input type="email" name="email" class="ocb-in">
                    </div>
                    <div class="ocb-field">
                        <label>{{ $isAr ? 'تفاصيل الطلب' : 'Details' }}</label>
                        <textarea name="notes" rows="3" class="ocb-in" placeholder="{{ $isAr ? 'صف ما تحتاجه' : 'Describe what you need' }}"></textarea>
                    </div>

                    <button type="submit" class="ocb-submit">
                        <i class="fa-solid fa-paper-plane"></i> {{ $isAr ? 'إرسال الطلب' : 'Send Request' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function ocbService(sel) {
            var id = sel.options[sel.selectedIndex].getAttribute('data-id') || '';
            var f = document.getElementById('ocb_service_id');
            if (f) f.value = id;
        }
    </script>
@endsection
