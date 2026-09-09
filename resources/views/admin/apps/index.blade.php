@extends('admin.layout.default')
@section('content')
<div class="row flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <div class="col-sm-auto col-12">
        <h5 class="pages-title color-changer fs-2">{{ trans('labels.addons_manager') }}</h5>
        @include('admin.layout.breadcrumb')
    </div>
    <div class="col-sm-auto col-12">
        <div class="d-flex justify-content-end">
            <a href="{{ URL::to('/admin/createsystem-addons') }}" class="btn w-100 btn-primary px-4 rounded-start-5 rounded-end-5">
                {{ trans('labels.install_update_addons') }}
            </a>
        </div>
    </div>
</div>
<div class="card rgb-success-light mb-3 border-0 box-shadow">
    <div class="card-body py-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div class="mb-sm-0 mb-2">
                <h5 class="card-title mb-1 color-changer fw-bold">Buy More Premium Addons</h5>
                <p class="text-muted fw-medium">Connect your favorite tools.</p>
            </div>
                <a href="https://tinyurl.com/43n6whv7" target="_blank" class="btn btn-dark fiexd-color fs-7 col-sm-auto col-12 px-4 rounded-start-5 rounded-end-5">Buy More Premium Addons</a>
        </div>
    </div>
</div>
    <div class="col-md-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title color-changer mb-1 fw-bold">Installed Addons ({{App\Models\SystemAddons::count()}})</h5>
                </div>
                <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-1 g-3 pt-3 addons-manager ">
                    @forelse(App\Models\SystemAddons::orderBy('name', 'ASC')->get() as $key => $addon)
                    <div class="col">
                        <div class="card h-100 rounded-3 overflow-hidden">
                            <img src="{!! URL('storage/app/public/addons/' . $addon->image) !!}" alt="">

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary mb-2 fw-400 fs-8">{{ $addon->version }}</span>
                                </div>
                                <h6 class="card-title color-changer fw-600 line-limit-2">{{ ucfirst($addon->name) }}</h6>
                            </div>
                            <div class="card-footer border-top bg-transparent d-flex justify-content-between align-items-center">
                                <p class="text-muted fs-13 fw-500">
                                    {{ date('d M Y', strtotime($addon->created_at)) }}
                                </p>
                                @if ($addon->activated == 1)
                                <a @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/systemaddons/status-' . $addon->id . '/2') }}')" @endif class="btn btn-sm btn-secondary px-3 rounded-start-5 rounded-end-5 {{ session()->get('direction') == 2 ? 'float-start' : 'float-end' }}">{{ trans('labels.activated') }}</a>
                                @else
                                <a @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/systemaddons/status-' . $addon->id . '/1') }}')" @endif class="btn btn-sm btn-danger px-3 rounded-start-5 rounded-end-5 {{ session()->get('direction') == 2 ? 'float-start' : 'float-end' }}">{{ trans('labels.deactivated') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col col-md-12 text-center text-muted">
                        <h4>{{ trans('labels.no_data') }}</h4>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
<script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/assets/js/custom/systemaddons.js') }}"></script>
@endsection