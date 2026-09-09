@extends('admin.layout.default')
@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp
    <style>
        .ocsv-modal-bg { position: fixed; inset: 0; background: rgba(15,23,20,.55); z-index: 1050; display: none; }
        .ocsv-modal-bg.show { display: flex; align-items: flex-start; justify-content: center; padding: 40px 16px; overflow-y: auto; }
        .ocsv-modal { background: #fff; border-radius: 16px; max-width: 560px; width: 100%; padding: 26px 26px 22px; box-shadow: 0 30px 70px -30px rgba(0,0,0,.5); }
        .ocsv-modal h5 { font-weight: 800; margin: 0 0 18px; }
        .ocsv-f { margin-bottom: 14px; }
        .ocsv-f label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 5px; color: #39443c; }
        .ocsv-f .req { color: #d64545; }
        .ocsv-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .ocsv-thumb { width: 46px; height: 46px; border-radius: 8px; object-fit: cover; border: 1px solid #e7ece4; }
        @media (max-width: 520px) { .ocsv-g2 { grid-template-columns: 1fr; } }
    </style>

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-auto">
            <h5 class="pages-title color-changer fs-2">{{ $isAr ? 'الخدمات' : 'Services' }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" onclick="ocsvOpen()">
                <i class="fa-solid fa-plus"></i> {{ $isAr ? 'إضافة خدمة' : 'Add Service' }}
            </button>
        </div>
    </div>

    <div class="card border-0 box-shadow mb-7">
        <div class="card-body">
            @if (count($services) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fs-8 text-muted">
                                <th></th>
                                <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                <th>{{ $isAr ? 'الفئة' : 'Type' }}</th>
                                <th>{{ $isAr ? 'السعر' : 'Price' }}</th>
                                <th>{{ $isAr ? 'المدة' : 'Duration' }}</th>
                                <th>{{ $isAr ? 'الحالة' : 'Active' }}</th>
                                <th class="text-end">{{ $isAr ? 'إجراء' : 'Action' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $s)
                                <tr>
                                    <td><img src="{{ helper::image_path($s->image) }}" class="ocsv-thumb" alt=""></td>
                                    <td class="fw-600">{{ $s->name }}</td>
                                    <td>{{ $s->category ?: '—' }}</td>
                                    <td>{{ $s->price > 0 ? helper::currency_formate($s->price, $vdata ?? Auth::user()->id) : '—' }}</td>
                                    <td>{{ $s->duration ?: '—' }}</td>
                                    <td>
                                        <form action="{{ URL::to('admin/booking-services/status') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $s->id }}">
                                            <button class="btn btn-sm {{ $s->is_available ? 'btn-success' : 'btn-outline-secondary' }}">
                                                {{ $s->is_available ? ($isAr ? 'مفعّل' : 'On') : ($isAr ? 'موقوف' : 'Off') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-light"
                                            onclick='ocsvEdit(@json($s))'><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ URL::to('admin/booking-services/delete') }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('{{ $isAr ? 'حذف هذه الخدمة؟' : 'Delete this service?' }}')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $s->id }}">
                                            <button class="btn btn-sm btn-light text-danger"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-hand-holding-medical fs-1 mb-3 d-block"></i>
                    {{ $isAr ? 'لا توجد خدمات بعد. أضف أول خدمة قابلة للحجز (طبيب، غرفة، خدمة...).' : 'No services yet. Add your first bookable item (a doctor, a room, a service...).' }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add / Edit modal --}}
    <div class="ocsv-modal-bg" id="ocsvModal">
        <div class="ocsv-modal">
            <div class="d-flex justify-content-between align-items-center">
                <h5 id="ocsvTitle">{{ $isAr ? 'إضافة خدمة' : 'Add Service' }}</h5>
                <button type="button" class="btn-close" onclick="ocsvClose()"></button>
            </div>
            <form id="ocsvForm" action="{{ URL::to('admin/booking-services') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="ocsv_id">
                <div class="ocsv-f">
                    <label class="d-flex align-items-center justify-content-between">
                        <span>{{ $isAr ? 'اسم الخدمة' : 'Service name' }} <span class="req">*</span></span>
                        @include('admin.partials.ai_assist', ['target' => '#ocsv_name', 'field' => $isAr ? 'اسم خدمة الحجز' : 'booking service name'])
                    </label>
                    <input type="text" name="name" id="ocsv_name" class="form-control" required
                        placeholder="{{ $isAr ? 'مثال: د. أحمد، غرفة ديلوكس، قص شعر' : 'e.g. Dr. Ahmed, Deluxe Room, Haircut' }}">
                </div>
                <div class="ocsv-g2">
                    <div class="ocsv-f">
                        <label>{{ $isAr ? 'الفئة / النوع' : 'Type / Category' }}</label>
                        <input type="text" name="category" id="ocsv_category" class="form-control"
                            placeholder="{{ $isAr ? 'قلب، غرفة، عيادة...' : 'Cardiology, Room, Clinic...' }}">
                    </div>
                    <div class="ocsv-f">
                        <label>{{ $isAr ? 'المدة' : 'Duration' }}</label>
                        <input type="text" name="duration" id="ocsv_duration" class="form-control"
                            placeholder="{{ $isAr ? '30 دقيقة / في الليلة' : '30 min / per night' }}">
                    </div>
                </div>
                <div class="ocsv-f">
                    <label>{{ $isAr ? 'السعر (اختياري)' : 'Price (optional)' }}</label>
                    <input type="number" step="0.01" min="0" name="price" id="ocsv_price" class="form-control" placeholder="0.00">
                </div>
                <div class="ocsv-f">
                    <label class="d-flex align-items-center justify-content-between">
                        <span>{{ $isAr ? 'الوصف' : 'Description' }}</span>
                        @include('admin.partials.ai_assist', ['target' => '#ocsv_description', 'field' => $isAr ? 'وصف خدمة الحجز' : 'booking service description'])
                    </label>
                    <textarea name="description" id="ocsv_description" rows="2" class="form-control"></textarea>
                </div>
                <div class="ocsv-f">
                    <label>{{ $isAr ? 'صورة' : 'Image' }}</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted" id="ocsv_curimg"></small>
                </div>
                <div class="ocsv-f form-check">
                    <input type="checkbox" name="is_available" id="ocsv_active" class="form-check-input" value="1" checked>
                    <label for="ocsv_active" class="form-check-label">{{ $isAr ? 'متاح للحجز' : 'Available for booking' }}</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2">
                    <i class="fa-solid fa-check"></i> {{ $isAr ? 'حفظ' : 'Save' }}
                </button>
            </form>
        </div>
    </div>

    <script>
        var ocsvStoreUrl = "{{ URL::to('admin/booking-services') }}";
        var ocsvUpdateUrl = "{{ URL::to('admin/booking-services/update') }}";
        function ocsvOpen() {
            document.getElementById('ocsvForm').reset();
            document.getElementById('ocsv_id').value = '';
            document.getElementById('ocsvForm').action = ocsvStoreUrl;
            document.getElementById('ocsvTitle').innerText = "{{ $isAr ? 'إضافة خدمة' : 'Add Service' }}";
            document.getElementById('ocsv_curimg').innerText = '';
            document.getElementById('ocsv_active').checked = true;
            document.getElementById('ocsvModal').classList.add('show');
        }
        function ocsvClose() { document.getElementById('ocsvModal').classList.remove('show'); }
        function ocsvEdit(s) {
            document.getElementById('ocsvForm').reset();
            document.getElementById('ocsvForm').action = ocsvUpdateUrl;
            document.getElementById('ocsvTitle').innerText = "{{ $isAr ? 'تعديل الخدمة' : 'Edit Service' }}";
            document.getElementById('ocsv_id').value = s.id;
            document.getElementById('ocsv_name').value = s.name || '';
            document.getElementById('ocsv_category').value = s.category || '';
            document.getElementById('ocsv_duration').value = s.duration || '';
            document.getElementById('ocsv_price').value = (s.price > 0 ? s.price : '');
            document.getElementById('ocsv_description').value = s.description || '';
            document.getElementById('ocsv_active').checked = (s.is_available == 1);
            document.getElementById('ocsv_curimg').innerText = s.image ? "{{ $isAr ? 'الصورة الحالية محفوظة' : 'Current image kept unless replaced' }}" : '';
            document.getElementById('ocsvModal').classList.add('show');
        }
        document.getElementById('ocsvModal').addEventListener('click', function (e) { if (e.target === this) ocsvClose(); });
    </script>
@endsection
