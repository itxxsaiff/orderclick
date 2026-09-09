@extends('admin.layout.auth_default')
@section('content')
    @php $ar = app()->getLocale() === 'ar'; @endphp

    <style>
        html, body { overflow-x: hidden; }
        .ocw, .ocw * { box-sizing: border-box; }
        .ocw { min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 26px 16px;
            background: radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.10), transparent 55%), #eef4ef; }
        .ocw__card { width: 100%; max-width: 1080px; background: #fff; border: 1px solid #e5e9e2; border-radius: 22px;
            box-shadow: 0 24px 60px -32px rgba(20,32,24,.4); overflow: hidden; }
        .ocw__head { padding: 22px 30px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .ocw__brand { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 700; color: #17201a; }
        .ocw__brand img { height: 34px; width: auto; }

        /* Stepper */
        .ocw__steps { display: flex; gap: 14px; padding: 18px 30px 0; flex-wrap: wrap; }
        .ocw__steps .st { flex: 1 1 180px; min-width: 150px; }
        .ocw__steps .row { display: flex; align-items: center; gap: 9px; }
        .ocw__steps .n { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13.5px; background: #eef2ec; color: #8a978d; flex: 0 0 auto; }
        .ocw__steps .lb { font-size: 14px; font-weight: 650; color: #8a978d; }
        .ocw__steps .bar { height: 3px; border-radius: 3px; background: #eef2ec; margin-top: 9px; }
        .ocw__steps .st.done .n { background: #1f9d55; color: #fff; }
        .ocw__steps .st.done .lb, .ocw__steps .st.active .lb { color: #17201a; }
        .ocw__steps .st.done .bar, .ocw__steps .st.active .bar { background: #1f9d55; }
        .ocw__steps .st.active .n { background: #1f9d55; color: #fff; }

        .ocw__body { padding: 22px 30px 30px; }
        .ocw__pill { display: inline-block; background: #eafaf0; color: #137a40; font-size: 12.5px; font-weight: 700;
            padding: 5px 12px; border-radius: 20px; }
        .ocw__title { font-size: 27px; font-weight: 700; color: #17201a; margin: 12px 0 4px; }
        .ocw__sub { color: #6b7669; font-size: 14.5px; margin: 0 0 20px; }

        .ocw label.f { display: block; font-size: 13.5px; font-weight: 600; color: #39443c; margin-bottom: 6px; }
        .ocw label.f .req { color: #d64545; }
        .ocw .in, .ocw select.in { width: 100%; height: 46px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px;
            font-size: 15px; color: #17201a; background: #fff; }
        .ocw textarea.in { height: auto; padding: 10px 14px; }
        .ocw .in:focus { outline: none; border-color: #1f9d55; box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
        .ocw .fg { margin-bottom: 15px; }
        .ocw .err { color: #d64545; font-size: 12.5px; margin-top: 5px; display: block; }

        /* Selectable cards (systems, activities, plans, payment) */
        .ocw__grid { display: grid; gap: 14px; }
        .ocw__grid.g3 { grid-template-columns: repeat(3, minmax(0,1fr)); }
        .ocw__pick { border: 2px solid #e5e9e2; border-radius: 14px; padding: 18px 16px; cursor: pointer; background: #fff;
            transition: .18s; position: relative; }
        .ocw__pick:hover { border-color: #bfe0cd; }
        .ocw__pick.sel { border-color: #1f9d55; background: #f4fbf7; }
        .ocw__pick .tick { position: absolute; top: 12px; right: 12px; width: 22px; height: 22px; border-radius: 50%;
            background: #1f9d55; color: #fff; font-size: 12px; display: none; align-items: center; justify-content: center; }
        .ocw__pick.sel .tick { display: flex; }
        .ocw__pick h4 { margin: 0 0 4px; font-size: 17px; font-weight: 700; color: #17201a; }
        .ocw__pick p { margin: 0; font-size: 13px; color: #7d887f; }
        .ocw__pick ul { margin: 12px 0 0; padding: 0; list-style: none; }
        .ocw__pick li { font-size: 13.5px; color: #39443c; padding: 3px 0; }
        .ocw__pick li i { color: #1f9d55; margin-right: 7px; }
        .ocw__price { margin-top: 12px; font-weight: 700; color: #17201a; }
        .ocw__badge { background: #1f9d55; color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 9px;
            border-radius: 20px; text-transform: uppercase; margin-left: 8px; }

        .ocw__pay { display: flex; gap: 10px; flex-wrap: wrap; }
        .ocw__pay .m { flex: 1 1 140px; border: 2px solid #e5e9e2; border-radius: 12px; padding: 14px; text-align: center;
            cursor: pointer; font-weight: 650; color: #39443c; background: #fff; }
        .ocw__pay .m.sel { border-color: #1f9d55; background: #1f9d55; color: #fff; }

        .ocw__actions { display: flex; gap: 12px; align-items: center; justify-content: space-between;
            margin-top: 24px; padding-top: 20px; border-top: 1px solid #eef2ec; flex-wrap: wrap; }
        .ocw__btn { height: 48px; border-radius: 11px; font-weight: 650; font-size: 15px; border: 0; cursor: pointer;
            padding: 0 24px; }
        .ocw__btn--p { background: #1f9d55; color: #fff; }
        .ocw__btn--p:hover { background: #198a49; }
        .ocw__btn--g { background: #fff; color: #39443c; border: 1px solid #d9e0d4; text-decoration: none;
            display: inline-flex; align-items: center; }
        .ocw__note { background: #f2fbf6; border-radius: 12px; padding: 14px 16px; font-size: 13.5px; color: #2b6b46;
            display: flex; gap: 9px; align-items: flex-start; margin-top: 18px; }

        @media (max-width: 820px) { .ocw__grid.g3 { grid-template-columns: 1fr; } }
        @media (max-width: 640px) {
            .ocw__body, .ocw__head, .ocw__steps { padding-left: 18px; padding-right: 18px; }
            .ocw__title { font-size: 23px; }
        }
    </style>

    <div class="ocw">
        <div class="ocw__card">
            <div class="ocw__head">
                <div class="ocw__brand">
                    <img src="{{ helper::image_path(helper::appdata('')->logo) }}" alt="">
                    {{ helper::appdata('')->website_title ?: 'Order Click' }}
                </div>

            </div>

            <div class="ocw__steps">
                @foreach ($steps as $n => $meta)
                    <div class="st {{ $n === $step ? 'active' : '' }} {{ $n < $step ? 'done' : '' }}">
                        <div class="row">
                            <span class="n">@if ($n < $step)<i class="fa-solid fa-check"></i>@else{{ $n }}@endif</span>
                            <span class="lb">{{ $meta['label'] }}</span>
                        </div>
                        <div class="bar"></div>
                    </div>
                @endforeach
            </div>

            <div class="ocw__body">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @include('register.steps.step' . $step)
            </div>
        </div>
    </div>
@endsection
