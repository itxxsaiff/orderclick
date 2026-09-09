@extends('admin.layout.default')
@section('content')
    @php
        $isAr = app()->getLocale() === 'ar';
        $tid = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        $oc_isSalon = optional(App\Models\Settings::where('vendor_id', $tid)->first())->business_type === 'salon';
        $ocMemberLabel = $oc_isSalon ? ($isAr ? 'عضو الفريق' : 'Team member') : ($isAr ? 'طبيب' : 'Doctor');
        $ocListLabel = $oc_isSalon ? ($isAr ? 'الفريق' : 'Team') : ($isAr ? 'الأطباء' : 'Doctors');
    @endphp
    <style>
        .ocdr-modal-bg { position: fixed; inset: 0; background: rgba(15,23,20,.55); z-index: 1050; display: none; }
        .ocdr-modal-bg.show { display: flex; align-items: flex-start; justify-content: center; padding: 40px 16px; overflow-y: auto; }
        .ocdr-modal { background: #fff; border-radius: 16px; max-width: 600px; width: 100%; padding: 26px 26px 22px; box-shadow: 0 30px 70px -30px rgba(0,0,0,.5); }
        .ocdr-modal h5 { font-weight: 800; margin: 0 0 18px; }
        .ocdr-f { margin-bottom: 14px; }
        .ocdr-f label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 5px; color: #39443c; }
        .ocdr-f .req { color: #d64545; }
        .ocdr-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .ocdr-thumb { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid #e7ece4; }
        @media (max-width: 520px) { .ocdr-g2 { grid-template-columns: 1fr; } }
    </style>

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-auto">
            <h5 class="pages-title color-changer fs-2">{{ $ocListLabel }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" onclick="ocdrOpen()"><i class="fa-solid fa-plus"></i> {{ ($isAr ? 'إضافة ' : 'Add ') . $ocMemberLabel }}</button>
        </div>
    </div>

    <div class="card border-0 box-shadow mb-7">
        <div class="card-body">
            @if (count($doctors) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fs-8 text-muted">
                                <th></th>
                                <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                <th>{{ $isAr ? 'التخصص' : 'Specialty' }}</th>
                                <th>{{ $isAr ? 'الخبرة' : 'Experience' }}</th>
                                <th>{{ $isAr ? 'الرسوم' : 'Fee' }}</th>
                                <th>{{ $isAr ? 'الحالة' : 'Active' }}</th>
                                <th class="text-end">{{ $isAr ? 'إجراء' : 'Action' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($doctors as $d)
                                <tr>
                                    <td><img src="{{ helper::image_path($d->image) }}" class="ocdr-thumb" alt=""></td>
                                    <td class="fw-600">{{ $d->name }}</td>
                                    <td>{{ $d->specialty ?: '—' }}</td>
                                    <td>{{ $d->experience ?: '—' }}</td>
                                    <td>{{ $d->fee > 0 ? helper::currency_formate($d->fee, $tid) : '—' }}</td>
                                    <td>
                                        <form action="{{ URL::to('admin/doctors/status') }}" method="POST" class="d-inline">
                                            @csrf<input type="hidden" name="id" value="{{ $d->id }}">
                                            <button class="btn btn-sm {{ $d->is_available ? 'btn-success' : 'btn-outline-secondary' }}">{{ $d->is_available ? ($isAr ? 'مفعّل' : 'On') : ($isAr ? 'موقوف' : 'Off') }}</button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-light" onclick='ocdrEdit(@json($d))'><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ URL::to('admin/doctors/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $isAr ? 'حذف هذا الطبيب؟' : 'Delete this doctor?' }}')">
                                            @csrf<input type="hidden" name="id" value="{{ $d->id }}">
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
                    <i class="fa-solid fa-user-doctor fs-1 mb-3 d-block"></i>
                    {{ $isAr ? 'لا يوجد أطباء بعد. أضف أول طبيب ليظهر في متجرك.' : 'No doctors yet. Add your first doctor to show them on your storefront.' }}
                </div>
            @endif
        </div>
    </div>

    <div class="ocdr-modal-bg" id="ocdrModal">
        <div class="ocdr-modal">
            <div class="d-flex justify-content-between align-items-center">
                <h5 id="ocdrTitle">{{ ($isAr ? 'إضافة ' : 'Add ') . $ocMemberLabel }}</h5>
                <button type="button" class="btn-close" onclick="ocdrClose()"></button>
            </div>
            <form id="ocdrForm" action="{{ URL::to('admin/doctors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="ocdr_id">
                <div class="ocdr-f">
                    <label>{{ $ocMemberLabel . ($isAr ? '' : ' name') }} <span class="req">*</span></label>
                    <input type="text" name="name" id="ocdr_name" class="form-control" required placeholder="{{ $isAr ? 'مثال: د. أحمد خالد' : 'e.g. Dr. Ahmed Khaled' }}">
                </div>
                <div class="ocdr-g2">
                    <div class="ocdr-f">
                        <label>{{ $isAr ? 'التخصص / القسم' : 'Specialty / Department' }}</label>
                        <input type="text" name="specialty" id="ocdr_specialty" class="form-control" placeholder="{{ $isAr ? 'قلب، أسنان، أطفال...' : 'Cardiology, Dental, Paediatrics...' }}">
                    </div>
                    <div class="ocdr-f">
                        <label>{{ $isAr ? 'الخبرة' : 'Experience' }}</label>
                        <input type="text" name="experience" id="ocdr_experience" class="form-control" placeholder="{{ $isAr ? '12 سنة' : '12 years' }}">
                    </div>
                </div>
                <div class="ocdr-g2">
                    <div class="ocdr-f">
                        <label>{{ $isAr ? 'المؤهلات' : 'Qualification' }}</label>
                        <input type="text" name="qualification" id="ocdr_qualification" class="form-control" placeholder="MBBS, MD">
                    </div>
                    <div class="ocdr-f">
                        <label>{{ $isAr ? 'رسوم الكشف' : 'Consultation fee' }}</label>
                        <input type="number" step="0.01" min="0" name="fee" id="ocdr_fee" class="form-control" placeholder="0.00">
                    </div>
                </div>
                <div class="ocdr-f">
                    <label>{{ $isAr ? 'اللغات' : 'Languages' }}</label>
                    <input type="text" name="languages" id="ocdr_languages" class="form-control" placeholder="{{ $isAr ? 'العربية، الإنجليزية' : 'English, Arabic' }}">
                </div>
                <div class="ocdr-f">
                    <label>{{ $isAr ? 'نبذة' : 'About' }}</label>
                    <textarea name="about" id="ocdr_about" rows="2" class="form-control"></textarea>
                </div>
                <div class="ocdr-f">
                    <label>{{ $isAr ? 'صورة' : 'Photo' }}</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted" id="ocdr_curimg"></small>
                </div>
                <div class="ocdr-f form-check">
                    <input type="checkbox" name="is_available" id="ocdr_active" class="form-check-input" value="1" checked>
                    <label for="ocdr_active" class="form-check-label">{{ $isAr ? 'متاح للحجز' : 'Available for booking' }}</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2"><i class="fa-solid fa-check"></i> {{ $isAr ? 'حفظ' : 'Save' }}</button>
            </form>
        </div>
    </div>

    <script>
        var ocdrStoreUrl = "{{ URL::to('admin/doctors') }}";
        var ocdrUpdateUrl = "{{ URL::to('admin/doctors/update') }}";
        function ocdrOpen() {
            var f = document.getElementById('ocdrForm'); f.reset(); f.action = ocdrStoreUrl;
            document.getElementById('ocdr_id').value = '';
            document.getElementById('ocdrTitle').innerText = "{{ ($isAr ? 'إضافة ' : 'Add ') . $ocMemberLabel }}";
            document.getElementById('ocdr_curimg').innerText = '';
            document.getElementById('ocdr_active').checked = true;
            document.getElementById('ocdrModal').classList.add('show');
        }
        function ocdrClose() { document.getElementById('ocdrModal').classList.remove('show'); }
        function ocdrEdit(d) {
            var f = document.getElementById('ocdrForm'); f.reset(); f.action = ocdrUpdateUrl;
            document.getElementById('ocdrTitle').innerText = "{{ ($isAr ? 'تعديل ' : 'Edit ') . $ocMemberLabel }}";
            document.getElementById('ocdr_id').value = d.id;
            document.getElementById('ocdr_name').value = d.name || '';
            document.getElementById('ocdr_specialty').value = d.specialty || '';
            document.getElementById('ocdr_experience').value = d.experience || '';
            document.getElementById('ocdr_qualification').value = d.qualification || '';
            document.getElementById('ocdr_fee').value = (d.fee > 0 ? d.fee : '');
            document.getElementById('ocdr_languages').value = d.languages || '';
            document.getElementById('ocdr_about').value = d.about || '';
            document.getElementById('ocdr_active').checked = (d.is_available == 1);
            document.getElementById('ocdr_curimg').innerText = d.image ? "{{ $isAr ? 'الصورة الحالية محفوظة' : 'Current image kept unless replaced' }}" : '';
            document.getElementById('ocdrModal').classList.add('show');
        }
        document.getElementById('ocdrModal').addEventListener('click', function (e) { if (e.target === this) ocdrClose(); });
    </script>
@endsection
